<?php
/**
 * Remove login limiter data when the plugin is deleted.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

$table_name = $wpdb->prefix . 'simple_wp_toolbox_login_limits';
$wpdb->query("DROP TABLE IF EXISTS {$table_name}");

delete_option('simple_wp_toolbox_login_limiter_db_version');
delete_option('simple_wp_toolbox_login_limiter_ips');
