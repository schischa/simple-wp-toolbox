<?php
/**
 * Limit failed login attempts by IP address.
 */

define('MARCS_CWF_LOGIN_LIMIT', 4);
define('MARCS_CWF_LOGIN_LIMIT_WINDOW', DAY_IN_SECONDS);

/**
 * Get the IP address of the current request.
 *
 * REMOTE_ADDR is used directly so client supplied forwarding headers are not trusted.
 *
 * @return string|false
 */
function marcs_cwf_login_limiter_get_ip() {
    if (empty($_SERVER['REMOTE_ADDR'])) {
        return false;
    }

    $ip_address = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));

    if (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
        return false;
    }

    return $ip_address;
}

/**
 * Build a transient key for the given login limiter data and IP address.
 *
 * @param string $type Data type, either attempts or lockout.
 * @param string $ip_address IP address.
 * @return string
 */
function marcs_cwf_login_limiter_get_key($type, $ip_address) {
    return 'marcs_cwf_login_' . $type . '_' . md5($ip_address);
}

/**
 * Track an IP hash while it has outstanding attempts or an active lockout.
 *
 * @param string $ip_address IP address.
 */
function marcs_cwf_login_limiter_track_ip($ip_address) {
    $ip_hash = md5($ip_address);
    $tracked_ips = get_option('marcs_cwf_login_limiter_ips', array());

    if (!is_array($tracked_ips)) {
        $tracked_ips = array();
    }

    $tracked_ips = array_filter($tracked_ips, function ($tracked_hash) {
        return get_transient('marcs_cwf_login_attempts_' . $tracked_hash) !== false
            || get_transient('marcs_cwf_login_lockout_' . $tracked_hash) !== false;
    });
    $tracked_ips[] = $ip_hash;

    update_option('marcs_cwf_login_limiter_ips', array_values(array_unique($tracked_ips)), false);
}

/**
 * Remove an IP hash from the tracking option when it has no active lockout.
 *
 * @param string $ip_address IP address.
 */
function marcs_cwf_login_limiter_untrack_ip($ip_address) {
    $ip_hash = md5($ip_address);
    $tracked_ips = get_option('marcs_cwf_login_limiter_ips', array());

    if (is_array($tracked_ips)) {
        $tracked_ips = array_diff($tracked_ips, array($ip_hash));
        update_option('marcs_cwf_login_limiter_ips', array_values($tracked_ips), false);
    }
}

/**
 * Reject authentication requests from an IP address that is currently locked out.
 *
 * @param WP_User|WP_Error|null $user Authenticated user or error.
 * @return WP_User|WP_Error|null
 */
function marcs_cwf_login_limiter_check($user) {
    if (!get_option('marcs_cwf_enable_login_limiter', 1)) {
        return $user;
    }

    $ip_address = marcs_cwf_login_limiter_get_ip();

    if (!$ip_address || get_transient(marcs_cwf_login_limiter_get_key('lockout', $ip_address)) === false) {
        return $user;
    }

    return new WP_Error(
        'marcs_cwf_login_locked',
        __('Too many failed login attempts. Please try again in 24 hours.', 'marcs-custom-wp-functions')
    );
}
add_filter('authenticate', 'marcs_cwf_login_limiter_check', PHP_INT_MAX);

/**
 * Also reject a valid password for a locked IP before WordPress completes login.
 *
 * @param WP_User|WP_Error $user Authenticated user or error.
 * @return WP_User|WP_Error
 */
function marcs_cwf_login_limiter_check_authenticated_user($user) {
    if (is_wp_error($user)) {
        return $user;
    }

    return marcs_cwf_login_limiter_check($user);
}
add_filter('wp_authenticate_user', 'marcs_cwf_login_limiter_check_authenticated_user', PHP_INT_MAX);

/**
 * Show the lockout message immediately after the attempt that triggers the lockout.
 *
 * @param string $errors Login error messages.
 * @return string
 */
function marcs_cwf_login_limiter_show_lockout_error($errors) {
    if (!get_option('marcs_cwf_enable_login_limiter', 1)) {
        return $errors;
    }

    $ip_address = marcs_cwf_login_limiter_get_ip();

    if (!$ip_address || get_transient(marcs_cwf_login_limiter_get_key('lockout', $ip_address)) === false) {
        return $errors;
    }

    return esc_html__('Your IP address has been blocked for 24 hours after too many failed login attempts.', 'marcs-custom-wp-functions');
}
add_filter('login_errors', 'marcs_cwf_login_limiter_show_lockout_error');

/**
 * Record a failed login and lock the IP after the fourth failure.
 *
 * @param string $username Submitted username.
 */
function marcs_cwf_login_limiter_record_failure($username) {
    if (!get_option('marcs_cwf_enable_login_limiter', 1)) {
        return;
    }

    $ip_address = marcs_cwf_login_limiter_get_ip();

    if (!$ip_address) {
        return;
    }

    if (get_transient(marcs_cwf_login_limiter_get_key('lockout', $ip_address)) !== false) {
        return;
    }

    $attempts_key = marcs_cwf_login_limiter_get_key('attempts', $ip_address);
    $attempts = (int) get_transient($attempts_key) + 1;

    if ($attempts >= MARCS_CWF_LOGIN_LIMIT) {
        delete_transient($attempts_key);
        set_transient(
            marcs_cwf_login_limiter_get_key('lockout', $ip_address),
            1,
            MARCS_CWF_LOGIN_LIMIT_WINDOW
        );
        marcs_cwf_login_limiter_track_ip($ip_address);
        return;
    }

    set_transient($attempts_key, $attempts, MARCS_CWF_LOGIN_LIMIT_WINDOW);
    marcs_cwf_login_limiter_track_ip($ip_address);
}
add_action('wp_login_failed', 'marcs_cwf_login_limiter_record_failure', 1);

/**
 * Clear failed login attempts after a successful login.
 *
 * @param string  $user_login Username.
 * @param WP_User $user       Authenticated user.
 */
function marcs_cwf_login_limiter_clear_attempts($user_login, $user) {
    $ip_address = marcs_cwf_login_limiter_get_ip();

    if ($ip_address) {
        delete_transient(marcs_cwf_login_limiter_get_key('attempts', $ip_address));

        if (get_transient(marcs_cwf_login_limiter_get_key('lockout', $ip_address)) === false) {
            marcs_cwf_login_limiter_untrack_ip($ip_address);
        }
    }
}
add_action('wp_login', 'marcs_cwf_login_limiter_clear_attempts', 10, 2);

/**
 * Reset all tracked login attempts and lockouts.
 */
function marcs_cwf_login_limiter_reset() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You are not allowed to reset login lockouts.', 'marcs-custom-wp-functions'));
    }

    check_admin_referer('marcs_cwf_login_limiter_reset');

    $transient_names = array();
    $tracked_ips = get_option('marcs_cwf_login_limiter_ips', array());

    if (is_array($tracked_ips)) {
        foreach ($tracked_ips as $ip_hash) {
            if (preg_match('/^[a-f0-9]{32}$/', $ip_hash)) {
                $transient_names[] = 'marcs_cwf_login_attempts_' . $ip_hash;
                $transient_names[] = 'marcs_cwf_login_lockout_' . $ip_hash;
            }
        }
    }

    global $wpdb;
    $like = $wpdb->esc_like('_transient_marcs_cwf_login_') . '%';
    $stored_transients = $wpdb->get_col($wpdb->prepare(
        "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
        $like
    ));

    foreach ($stored_transients as $option_name) {
        if (preg_match('/^_transient_(marcs_cwf_login_(?:attempts|lockout)_[a-f0-9]{32})$/', $option_name, $matches)) {
            $transient_names[] = $matches[1];
        }
    }

    foreach (array_unique($transient_names) as $transient_name) {
        delete_transient($transient_name);
    }

    delete_option('marcs_cwf_login_limiter_ips');

    wp_safe_redirect(add_query_arg(
        array(
            'page'                     => 'marcs-custom-wp-functions',
            'login_limiter_reset'      => '1',
        ),
        admin_url('options-general.php')
    ));
    exit;
}
add_action('admin_post_marcs_cwf_login_limiter_reset', 'marcs_cwf_login_limiter_reset');
