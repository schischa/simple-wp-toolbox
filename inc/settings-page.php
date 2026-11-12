<?php
add_action('admin_menu', 'simple_wp_toolbox_create_settings_page');
function simple_wp_toolbox_create_settings_page() {
    add_options_page(
        "Simple WP Toolbox",
        "Simple WP Toolbox",
        'manage_options',
        'simple-wp-toolbox',
        'simple_wp_toolbox_settings_page_html'
    );
}

add_action('admin_init', 'simple_wp_toolbox_register_settings');
function simple_wp_toolbox_register_settings() {
    // Existing settings
    register_setting('simple_wp_toolbox_settings_group', 'simple_wp_toolbox_theme_color', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_hex_color',
        'default'           => '#db5945',
    ));

    register_setting('simple_wp_toolbox_settings_group', 'simple_wp_toolbox_disable_comments', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));

    register_setting('simple_wp_toolbox_settings_group', 'simple_wp_toolbox_disable_xmlrpc', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));

    // Update email settings
    register_setting('simple_wp_toolbox_settings_group', 'simple_wp_toolbox_disable_core_update_emails', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));
    register_setting('simple_wp_toolbox_settings_group', 'simple_wp_toolbox_disable_plugin_update_emails', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));
    register_setting('simple_wp_toolbox_settings_group', 'simple_wp_toolbox_disable_theme_update_emails', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));

    // 🆕 Site Health test disabling options
    register_setting('simple_wp_toolbox_settings_group', 'simple_wp_toolbox_disable_php_version_test', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));
    register_setting('simple_wp_toolbox_settings_group', 'simple_wp_toolbox_disable_theme_version_test', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));
    register_setting('simple_wp_toolbox_settings_group', 'simple_wp_toolbox_disable_https_status_test', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));
    register_setting('simple_wp_toolbox_settings_group', 'simple_wp_toolbox_disable_background_updates_test', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));

    // Dashboard Widget Settings
    register_setting('simple_wp_toolbox_settings_group', 'simple_wp_toolbox_enable_dashboard_widget', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));

    register_setting('simple_wp_toolbox_settings_group', 'simple_wp_toolbox_enable_login_limiter', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 1,
    ));

    add_settings_section(
        'simple_wp_toolbox_login_security_section',
        'Login Security',
        'simple_wp_toolbox_login_security_section_cb',
        'simple_wp_toolbox_settings_group'
    );
    add_settings_field(
        'simple_wp_toolbox_enable_login_limiter_field',
        'Login Limiter',
        'simple_wp_toolbox_enable_login_limiter_field_cb',
        'simple_wp_toolbox_settings_group',
        'simple_wp_toolbox_login_security_section'
    );

    // Sections
    add_settings_section(
        'simple_wp_toolbox_theme_section',
        'Theme Settings',
        'simple_wp_toolbox_theme_section_cb',
        'simple_wp_toolbox_settings_group'
    );
    add_settings_field(
        'simple_wp_toolbox_theme_color_field',
        'Theme Color',
        'simple_wp_toolbox_theme_color_field_cb',
        'simple_wp_toolbox_settings_group',
        'simple_wp_toolbox_theme_section'
    );

    add_settings_section(
        'simple_wp_toolbox_comment_section',
        'Comment Settings',
        'simple_wp_toolbox_comment_section_cb',
        'simple_wp_toolbox_settings_group'
    );
    add_settings_field(
        'simple_wp_toolbox_disable_comments_field',
        'Disable Comments',
        'simple_wp_toolbox_disable_comments_field_cb',
        'simple_wp_toolbox_settings_group',
        'simple_wp_toolbox_comment_section'
    );

    add_settings_section(
        'simple_wp_toolbox_advanced_section',
        'Advanced Settings',
        'simple_wp_toolbox_advanced_section_cb',
        'simple_wp_toolbox_settings_group'
    );
    add_settings_field(
        'simple_wp_toolbox_disable_xmlrpc_field',
        'Disable XML-RPC',
        'simple_wp_toolbox_disable_xmlrpc_field_cb',
        'simple_wp_toolbox_settings_group',
        'simple_wp_toolbox_advanced_section'
    );

    add_settings_section(
        'simple_wp_toolbox_updates_section',
        'Update Notifications',
        'simple_wp_toolbox_updates_section_cb',
        'simple_wp_toolbox_settings_group'
    );
    add_settings_field(
        'simple_wp_toolbox_disable_core_update_emails_field',
        'Disable Core Update Emails',
        'simple_wp_toolbox_disable_core_update_emails_field_cb',
        'simple_wp_toolbox_settings_group',
        'simple_wp_toolbox_updates_section'
    );
    add_settings_field(
        'simple_wp_toolbox_disable_plugin_update_emails_field',
        'Disable Plugin Update Emails',
        'simple_wp_toolbox_disable_plugin_update_emails_field_cb',
        'simple_wp_toolbox_settings_group',
        'simple_wp_toolbox_updates_section'
    );
    add_settings_field(
        'simple_wp_toolbox_disable_theme_update_emails_field',
        'Disable Theme Update Emails',
        'simple_wp_toolbox_disable_theme_update_emails_field_cb',
        'simple_wp_toolbox_settings_group',
        'simple_wp_toolbox_updates_section'
    );

    // Dashboard Widget Section
    add_settings_section(
        'simple_wp_toolbox_dashboard_section',
        'Dashboard Widget',
        'simple_wp_toolbox_dashboard_section_cb',
        'simple_wp_toolbox_settings_group'
    );

    add_settings_field(
        'simple_wp_toolbox_enable_dashboard_widget_field',
        'System Information',
        'simple_wp_toolbox_enable_dashboard_widget_field_cb',
        'simple_wp_toolbox_settings_group',
        'simple_wp_toolbox_dashboard_section'
    );

    // 🆕 Site Health Section
    add_settings_section(
        'simple_wp_toolbox_site_health_section',
        'Site Health Settings',
        'simple_wp_toolbox_site_health_section_cb',
        'simple_wp_toolbox_settings_group'
    );
    add_settings_field(
        'simple_wp_toolbox_disable_php_version_test_field',
        'Disable PHP Version Test',
        'simple_wp_toolbox_disable_php_version_test_field_cb',
        'simple_wp_toolbox_settings_group',
        'simple_wp_toolbox_site_health_section'
    );
    add_settings_field(
        'simple_wp_toolbox_disable_theme_version_test_field',
        'Disable Theme Version Test',
        'simple_wp_toolbox_disable_theme_version_test_field_cb',
        'simple_wp_toolbox_settings_group',
        'simple_wp_toolbox_site_health_section'
    );
    add_settings_field(
        'simple_wp_toolbox_disable_https_status_test_field',
        'Disable HTTPS Status Test',
        'simple_wp_toolbox_disable_https_status_test_field_cb',
        'simple_wp_toolbox_settings_group',
        'simple_wp_toolbox_site_health_section'
    );
    add_settings_field(
        'simple_wp_toolbox_disable_background_updates_test_field',
        'Disable Background Updates Test',
        'simple_wp_toolbox_disable_background_updates_test_field_cb',
        'simple_wp_toolbox_settings_group',
        'simple_wp_toolbox_site_health_section'
    );
}

// Section callbacks
function simple_wp_toolbox_theme_section_cb() { echo '<p>Set the theme color used by modern browsers for UI elements.</p>'; }
function simple_wp_toolbox_comment_section_cb() { echo '<p>Configure how comments behave on your website.</p>'; }
function simple_wp_toolbox_advanced_section_cb() { echo '<p>Configure advanced settings to improve security or adjust site functionality.</p>'; }
function simple_wp_toolbox_updates_section_cb() { echo '<p>Control update notification emails. Disable specific emails here.</p>'; }
function simple_wp_toolbox_site_health_section_cb() { echo '<p>Control which Site Health tests are disabled.</p>'; }
function simple_wp_toolbox_login_security_section_cb() { echo '<p>Limit repeated failed login attempts.</p>'; }

// Field callbacks
function simple_wp_toolbox_theme_color_field_cb() {
    $theme_color = get_option('simple_wp_toolbox_theme_color', '#db5945');
    echo '<input type="text" name="simple_wp_toolbox_theme_color" value="' . esc_attr($theme_color) . '" class="regular-text" />';
}

function simple_wp_toolbox_disable_comments_field_cb() {
    $disable_comments = get_option('simple_wp_toolbox_disable_comments', 0);
    ?>
    <fieldset>
        <label><input type="radio" name="simple_wp_toolbox_disable_comments" value="0" <?php checked($disable_comments, 0); ?>> Remove only the URL field.</label><br>
        <label><input type="radio" name="simple_wp_toolbox_disable_comments" value="1" <?php checked($disable_comments, 1); ?>> Disable all comments entirely.</label>
    </fieldset>
    <?php
}

function simple_wp_toolbox_disable_xmlrpc_field_cb() {
    $value = get_option('simple_wp_toolbox_disable_xmlrpc', 0);
    echo '<input type="checkbox" name="simple_wp_toolbox_disable_xmlrpc" value="1" ' . checked(1, $value, false) . '> Disable XML-RPC';
}

// Update email fields
function simple_wp_toolbox_disable_core_update_emails_field_cb() {
    $v = get_option('simple_wp_toolbox_disable_core_update_emails', 0);
    echo '<input type="checkbox" name="simple_wp_toolbox_disable_core_update_emails" value="1" ' . checked(1, $v, false) . '> Disable Core Update Success Emails';
}
function simple_wp_toolbox_disable_plugin_update_emails_field_cb() {
    $v = get_option('simple_wp_toolbox_disable_plugin_update_emails', 0);
    echo '<input type="checkbox" name="simple_wp_toolbox_disable_plugin_update_emails" value="1" ' . checked(1, $v, false) . '> Disable Plugin Update Emails';
}
function simple_wp_toolbox_disable_theme_update_emails_field_cb() {
    $v = get_option('simple_wp_toolbox_disable_theme_update_emails', 0);
    echo '<input type="checkbox" name="simple_wp_toolbox_disable_theme_update_emails" value="1" ' . checked(1, $v, false) . '> Disable Theme Update Emails';
}

// 🆕 Site Health fields
function simple_wp_toolbox_disable_php_version_test_field_cb() {
    $v = get_option('simple_wp_toolbox_disable_php_version_test', 0);
    echo '<input type="checkbox" name="simple_wp_toolbox_disable_php_version_test" value="1" ' . checked(1, $v, false) . '> Disable PHP Version Test';
}
function simple_wp_toolbox_disable_theme_version_test_field_cb() {
    $v = get_option('simple_wp_toolbox_disable_theme_version_test', 0);
    echo '<input type="checkbox" name="simple_wp_toolbox_disable_theme_version_test" value="1" ' . checked(1, $v, false) . '> Disable Theme Version Test';
}
function simple_wp_toolbox_disable_https_status_test_field_cb() {
    $v = get_option('simple_wp_toolbox_disable_https_status_test', 0);
    echo '<input type="checkbox" name="simple_wp_toolbox_disable_https_status_test" value="1" ' . checked(1, $v, false) . '> Disable HTTPS Status Test';
}
function simple_wp_toolbox_disable_background_updates_test_field_cb() {
    $v = get_option('simple_wp_toolbox_disable_background_updates_test', 0);
    echo '<input type="checkbox" name="simple_wp_toolbox_disable_background_updates_test" value="1" ' . checked(1, $v, false) . '> Disable Background Updates Test';
}

// Dashboard Widget callbacks
function simple_wp_toolbox_dashboard_section_cb() {
    echo '<p>Display basic WordPress and server information on the admin dashboard.</p>';
}

function simple_wp_toolbox_enable_dashboard_widget_field_cb() {
    $enabled = get_option('simple_wp_toolbox_enable_dashboard_widget', 0);
    ?>
    <fieldset>
        <input type="hidden" name="simple_wp_toolbox_enable_dashboard_widget" value="0">
        <label>
            <input
                type="checkbox"
                name="simple_wp_toolbox_enable_dashboard_widget"
                value="1"
                <?php checked(1, $enabled); ?>
            >
            Enable System Information Dashboard Widget
        </label>
    </fieldset>
    <?php
}

function simple_wp_toolbox_enable_login_limiter_field_cb() {
    $enabled = get_option('simple_wp_toolbox_enable_login_limiter', 1);
    $reset_url = wp_nonce_url(
        admin_url('admin-post.php?action=simple_wp_toolbox_login_limiter_reset'),
        'simple_wp_toolbox_login_limiter_reset'
    );
    ?>
    <fieldset>
        <input type="hidden" name="simple_wp_toolbox_enable_login_limiter" value="0">
        <label>
            <input
                type="checkbox"
                name="simple_wp_toolbox_enable_login_limiter"
                value="1"
                <?php checked(1, $enabled); ?>
            >
            Enable the login limiter (4 failed attempts, 24-hour IP lockout)
        </label>
        <p>
            <a class="button button-secondary" href="<?php echo esc_url($reset_url); ?>">
                <?php esc_html_e('Reset Login Lockouts', 'simple-wp-toolbox'); ?>
            </a>
        </p>
    </fieldset>
    <?php
}

// Page output
function simple_wp_toolbox_settings_page_html() {
    if (!current_user_can('manage_options')) return;
    ?>
    <div class="wrap">
        <h1>Simple WP Toolbox Settings</h1>
        <?php if (isset($_GET['login_limiter_reset']) && '1' === $_GET['login_limiter_reset']) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e('All login attempts and lockouts have been reset.', 'simple-wp-toolbox'); ?></p></div>
        <?php endif; ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('simple_wp_toolbox_settings_group');
            do_settings_sections('simple_wp_toolbox_settings_group');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
