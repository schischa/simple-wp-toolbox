<?php
// Register the dashboard widget if enabled
add_action('wp_dashboard_setup', 'simple_wp_toolbox_register_dashboard_widget');

function simple_wp_toolbox_register_dashboard_widget() {
    // Only display the widget when explicitly enabled
    if (!get_option('simple_wp_toolbox_enable_dashboard_widget', 0)) {
        return;
    }

    // Restrict visibility to administrators
    if (!current_user_can('manage_options')) {
        return;
    }

    wp_add_dashboard_widget(
        'simple_wp_toolbox_system_info',
        __('Simple WP Toolbox System Information', 'simple-wp-toolbox'),
        'simple_wp_toolbox_dashboard_widget_html'
    );
}

// Format bytes into a human-readable value
function simple_wp_toolbox_format_bytes($bytes) {
    if ($bytes === -1) {
        return __('Unlimited', 'simple-wp-toolbox');
    }

    return size_format($bytes, 2);
}

// Display system information
function simple_wp_toolbox_dashboard_widget_html() {
    global $wpdb;

    $theme = wp_get_theme();

    $memory_usage = memory_get_usage(true);
    $memory_peak  = memory_get_peak_usage(true);
    $memory_limit = ini_get('memory_limit');

    $memory_limit_bytes = wp_convert_hr_to_bytes($memory_limit);

    ?>
    <table class="widefat striped">
        <tbody>
            <tr>
                <td><strong><?php esc_html_e('WordPress', 'simple-wp-toolbox'); ?></strong></td>
                <td><?php echo esc_html(get_bloginfo('version')); ?></td>
            </tr>
            <tr>
                <td><strong><?php esc_html_e('PHP Version', 'simple-wp-toolbox'); ?></strong></td>
                <td><?php echo esc_html(PHP_VERSION); ?></td>
            </tr>
            <tr>
                <td><strong><?php esc_html_e('Database', 'simple-wp-toolbox'); ?></strong></td>
                <td><?php echo esc_html($wpdb->db_version()); ?></td>
            </tr>
            <tr>
                <td><strong><?php esc_html_e('Memory Usage', 'simple-wp-toolbox'); ?></strong></td>
                <td><?php echo esc_html(simple_wp_toolbox_format_bytes($memory_usage)); ?></td>
            </tr>
            <tr>
                <td><strong><?php esc_html_e('Peak Memory Usage', 'simple-wp-toolbox'); ?></strong></td>
                <td><?php echo esc_html(simple_wp_toolbox_format_bytes($memory_peak)); ?></td>
            </tr>
            <tr>
                <td><strong><?php esc_html_e('PHP Memory Limit', 'simple-wp-toolbox'); ?></strong></td>
                <td>
                    <?php
                    echo esc_html($memory_limit === '-1'
                        ? __('Unlimited', 'simple-wp-toolbox')
                        : $memory_limit
                    );
                    ?>
                </td>
            </tr>
            <?php if ($memory_limit_bytes > 0) : ?>
                <tr>
                    <td><strong><?php esc_html_e('Memory Usage (%)', 'simple-wp-toolbox'); ?></strong></td>
                    <td>
                        <?php
                        echo esc_html(
                            round(($memory_peak / $memory_limit_bytes) * 100, 1) . '%'
                        );
                        ?>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <td><strong><?php esc_html_e('Active Theme', 'simple-wp-toolbox'); ?></strong></td>
                <td>
                    <?php
                    echo esc_html($theme->get('Name') . ' ' . $theme->get('Version'));
                    ?>
                </td>
            </tr>
            <tr>
                <td><strong><?php esc_html_e('Web Server', 'simple-wp-toolbox'); ?></strong></td>
                <td><?php echo esc_html($_SERVER['SERVER_SOFTWARE'] ?? __('Unknown', 'simple-wp-toolbox')); ?></td>
            </tr>
            <tr>
                <td><strong><?php esc_html_e('Max Execution Time', 'simple-wp-toolbox'); ?></strong></td>
                <td><?php echo esc_html(sprintf(__('%s seconds', 'simple-wp-toolbox'), ini_get('max_execution_time'))); ?></td>
            </tr>
            <tr>
                <td><strong><?php esc_html_e('Upload Limit', 'simple-wp-toolbox'); ?></strong></td>
                <td><?php echo esc_html(ini_get('upload_max_filesize')); ?></td>
            </tr>
            <tr>
                <td><strong><?php esc_html_e('Multisite', 'simple-wp-toolbox'); ?></strong></td>
                <td><?php echo is_multisite() ? esc_html__('Yes', 'simple-wp-toolbox') : esc_html__('No', 'simple-wp-toolbox'); ?></td>
            </tr>
        </tbody>
    </table>
    <?php
}
