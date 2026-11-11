<?php
/**
 * Limit failed login attempts by IP address.
 */

define('MARCS_CWF_LOGIN_LIMIT', 4);
define('MARCS_CWF_LOGIN_LIMIT_WINDOW', DAY_IN_SECONDS);
define('MARCS_CWF_LOGIN_LIMIT_DB_VERSION', '1.0');

/**
 * Get the name of the login limiter database table.
 *
 * @return string
 */
function marcs_cwf_login_limiter_get_table_name() {
    global $wpdb;

    return $wpdb->prefix . 'marcs_cwf_login_limits';
}

/**
 * Create or upgrade the login limiter table and migrate active transient data.
 */
function marcs_cwf_login_limiter_maybe_create_table() {
    global $wpdb;

    if (get_option('marcs_cwf_login_limiter_db_version') === MARCS_CWF_LOGIN_LIMIT_DB_VERSION) {
        if (!wp_next_scheduled('marcs_cwf_login_limiter_cleanup')) {
            wp_schedule_event(time() + DAY_IN_SECONDS, 'daily', 'marcs_cwf_login_limiter_cleanup');
        }
        return;
    }

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $table_name      = marcs_cwf_login_limiter_get_table_name();
    $charset_collate = $wpdb->get_charset_collate();
    $sql             = "CREATE TABLE {$table_name} (
        ip_hash char(32) NOT NULL,
        attempts smallint(5) unsigned NOT NULL DEFAULT 0,
        attempts_expires bigint(20) unsigned NOT NULL DEFAULT 0,
        locked_until bigint(20) unsigned NOT NULL DEFAULT 0,
        PRIMARY KEY  (ip_hash),
        KEY attempts_expires (attempts_expires),
        KEY locked_until (locked_until)
    ) {$charset_collate};";

    dbDelta($sql);

    $existing_table = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_name)));

    if ($existing_table !== $table_name) {
        return;
    }

    marcs_cwf_login_limiter_migrate_transients($table_name);
    update_option('marcs_cwf_login_limiter_db_version', MARCS_CWF_LOGIN_LIMIT_DB_VERSION, false);

    if (!wp_next_scheduled('marcs_cwf_login_limiter_cleanup')) {
        wp_schedule_event(time() + DAY_IN_SECONDS, 'daily', 'marcs_cwf_login_limiter_cleanup');
    }
}
add_action('plugins_loaded', 'marcs_cwf_login_limiter_maybe_create_table', 20);

/**
 * Migrate active login limiter data from the previous transient storage.
 *
 * @param string $table_name Login limiter table name.
 */
function marcs_cwf_login_limiter_migrate_transients($table_name) {
    global $wpdb;

    $tracked_ips = get_option('marcs_cwf_login_limiter_ips', array());

    if (!is_array($tracked_ips)) {
        return;
    }

    $now = time();

    foreach ($tracked_ips as $ip_hash) {
        if (!preg_match('/^[a-f0-9]{32}$/', $ip_hash)) {
            continue;
        }

        $attempts = get_transient('marcs_cwf_login_attempts_' . $ip_hash);
        $locked   = get_transient('marcs_cwf_login_lockout_' . $ip_hash);

        if ($attempts === false && $locked === false) {
            continue;
        }

        $attempt_count   = $attempts === false ? 0 : absint($attempts);
        $attempts_expiry = $attempts === false ? 0 : $now + MARCS_CWF_LOGIN_LIMIT_WINDOW;
        $locked_until    = $locked === false ? 0 : $now + MARCS_CWF_LOGIN_LIMIT_WINDOW;

        $result = $wpdb->insert(
            $table_name,
            array(
                'ip_hash'          => $ip_hash,
                'attempts'         => $attempt_count,
                'attempts_expires' => $attempts_expiry,
                'locked_until'     => $locked_until,
            ),
            array('%s', '%d', '%d', '%d')
        );

        if ($result !== false) {
            delete_transient('marcs_cwf_login_attempts_' . $ip_hash);
            delete_transient('marcs_cwf_login_lockout_' . $ip_hash);
        }
    }

    delete_option('marcs_cwf_login_limiter_ips');
}

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
 * Get the stored lockout expiration for an IP address.
 *
 * @param string $ip_address IP address.
 * @return int
 */
function marcs_cwf_login_limiter_get_lockout($ip_address) {
    global $wpdb;

    $locked_until = $wpdb->get_var($wpdb->prepare(
        'SELECT locked_until FROM ' . marcs_cwf_login_limiter_get_table_name() . ' WHERE ip_hash = %s',
        md5($ip_address)
    ));

    return (int) $locked_until;
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

    if (!$ip_address || marcs_cwf_login_limiter_get_lockout($ip_address) <= time()) {
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

    if (!$ip_address || marcs_cwf_login_limiter_get_lockout($ip_address) <= time()) {
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
    global $wpdb;

    if (!get_option('marcs_cwf_enable_login_limiter', 1)) {
        return;
    }

    $ip_address = marcs_cwf_login_limiter_get_ip();

    if (!$ip_address || marcs_cwf_login_limiter_get_lockout($ip_address) > time()) {
        return;
    }

    $now             = time();
    $attempts_expiry = $now + MARCS_CWF_LOGIN_LIMIT_WINDOW;
    $locked_until    = $now + MARCS_CWF_LOGIN_LIMIT_WINDOW;
    $table_name      = marcs_cwf_login_limiter_get_table_name();

    $wpdb->query($wpdb->prepare(
        "INSERT INTO {$table_name} (ip_hash, attempts, attempts_expires, locked_until)
        VALUES (%s, 1, %d, 0)
        ON DUPLICATE KEY UPDATE
            locked_until = IF(locked_until > %d, locked_until, IF(attempts_expires <= %d, 0, IF(attempts + 1 >= %d, %d, 0))),
            attempts = IF(attempts_expires <= %d, 1, attempts + 1),
            attempts_expires = IF(attempts_expires <= %d, %d, attempts_expires)",
        md5($ip_address),
        $attempts_expiry,
        $now,
        $now,
        MARCS_CWF_LOGIN_LIMIT,
        $locked_until,
        $now,
        $now,
        $attempts_expiry
    ));
}
add_action('wp_login_failed', 'marcs_cwf_login_limiter_record_failure', 1);

/**
 * Clear failed login attempts after a successful login.
 *
 * @param string  $user_login Username.
 * @param WP_User $user       Authenticated user.
 */
function marcs_cwf_login_limiter_clear_attempts($user_login, $user) {
    global $wpdb;

    $ip_address = marcs_cwf_login_limiter_get_ip();

    if ($ip_address) {
        $wpdb->delete(
            marcs_cwf_login_limiter_get_table_name(),
            array('ip_hash' => md5($ip_address)),
            array('%s')
        );
    }
}
add_action('wp_login', 'marcs_cwf_login_limiter_clear_attempts', 10, 2);

/**
 * Remove expired attempt and lockout records.
 */
function marcs_cwf_login_limiter_cleanup() {
    global $wpdb;

    $now = time();
    $wpdb->query($wpdb->prepare(
        'DELETE FROM ' . marcs_cwf_login_limiter_get_table_name() . ' WHERE attempts_expires <= %d AND locked_until <= %d',
        $now,
        $now
    ));
}
add_action('marcs_cwf_login_limiter_cleanup', 'marcs_cwf_login_limiter_cleanup');

/**
 * Unschedule login limiter cleanup when the plugin is deactivated.
 */
function marcs_cwf_login_limiter_deactivate() {
    wp_clear_scheduled_hook('marcs_cwf_login_limiter_cleanup');
}

/**
 * Reset all login attempts and lockouts.
 */
function marcs_cwf_login_limiter_reset() {
    global $wpdb;

    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You are not allowed to reset login lockouts.', 'marcs-custom-wp-functions'));
    }

    check_admin_referer('marcs_cwf_login_limiter_reset');
    $wpdb->query('TRUNCATE TABLE ' . marcs_cwf_login_limiter_get_table_name());

    wp_safe_redirect(add_query_arg(
        array(
            'page'                => 'marcs-custom-wp-functions',
            'login_limiter_reset' => '1',
        ),
        admin_url('options-general.php')
    ));
    exit;
}
add_action('admin_post_marcs_cwf_login_limiter_reset', 'marcs_cwf_login_limiter_reset');
