<?php
// Check options for disabling update emails
$disable_core_update_emails   = get_option('simple_wp_toolbox_disable_core_update_emails', 0);
$disable_plugin_update_emails = get_option('simple_wp_toolbox_disable_plugin_update_emails', 0);
$disable_theme_update_emails  = get_option('simple_wp_toolbox_disable_theme_update_emails', 0);

if ($disable_core_update_emails) {
    // Disable core update emails (only success emails as per given code)
    add_filter('auto_core_update_send_email', 'simple_wp_toolbox_stop_core_update_emails', 10, 4);
    function simple_wp_toolbox_stop_core_update_emails($send, $type, $core_update, $result) {
        if (!empty($type) && $type == 'success') {
            return false;
        }
        return true;
    }
}

if ($disable_plugin_update_emails) {
    // Disable plugin update emails
    add_filter('auto_plugin_update_send_email', '__return_false');
}

if ($disable_theme_update_emails) {
    // Disable theme update emails
    add_filter('auto_theme_update_send_email', '__return_false');
}
