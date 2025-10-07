<?php
add_action('admin_menu', 'marcs_cwf_create_settings_page');
function marcs_cwf_create_settings_page() {
    add_options_page(
        "Marc's Custom WP Functions",
        "Marc's Custom WP Functions",
        'manage_options',
        'marcs-custom-wp-functions',
        'marcs_cwf_settings_page_html'
    );
}

add_action('admin_init', 'marcs_cwf_register_settings');
function marcs_cwf_register_settings() {
    // Existing settings
    register_setting('marcs_cwf_settings_group', 'marcs_cwf_theme_color', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_hex_color',
        'default'           => '#db5945',
    ));

    register_setting('marcs_cwf_settings_group', 'marcs_cwf_disable_comments', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));

    register_setting('marcs_cwf_settings_group', 'marcs_cwf_disable_xmlrpc', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));

    // Update email settings
    register_setting('marcs_cwf_settings_group', 'marcs_cwf_disable_core_update_emails', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));
    register_setting('marcs_cwf_settings_group', 'marcs_cwf_disable_plugin_update_emails', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));
    register_setting('marcs_cwf_settings_group', 'marcs_cwf_disable_theme_update_emails', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));

    // 🆕 Site Health test disabling options
    register_setting('marcs_cwf_settings_group', 'marcs_cwf_disable_php_version_test', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));
    register_setting('marcs_cwf_settings_group', 'marcs_cwf_disable_theme_version_test', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));
    register_setting('marcs_cwf_settings_group', 'marcs_cwf_disable_https_status_test', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));
    register_setting('marcs_cwf_settings_group', 'marcs_cwf_disable_background_updates_test', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ));

    // Sections
    add_settings_section(
        'marcs_cwf_theme_section',
        'Theme Settings',
        'marcs_cwf_theme_section_cb',
        'marcs_cwf_settings_group'
    );
    add_settings_field(
        'marcs_cwf_theme_color_field',
        'Theme Color',
        'marcs_cwf_theme_color_field_cb',
        'marcs_cwf_settings_group',
        'marcs_cwf_theme_section'
    );

    add_settings_section(
        'marcs_cwf_comment_section',
        'Comment Settings',
        'marcs_cwf_comment_section_cb',
        'marcs_cwf_settings_group'
    );
    add_settings_field(
        'marcs_cwf_disable_comments_field',
        'Disable Comments',
        'marcs_cwf_disable_comments_field_cb',
        'marcs_cwf_settings_group',
        'marcs_cwf_comment_section'
    );

    add_settings_section(
        'marcs_cwf_advanced_section',
        'Advanced Settings',
        'marcs_cwf_advanced_section_cb',
        'marcs_cwf_settings_group'
    );
    add_settings_field(
        'marcs_cwf_disable_xmlrpc_field',
        'Disable XML-RPC',
        'marcs_cwf_disable_xmlrpc_field_cb',
        'marcs_cwf_settings_group',
        'marcs_cwf_advanced_section'
    );

    add_settings_section(
        'marcs_cwf_updates_section',
        'Update Notifications',
        'marcs_cwf_updates_section_cb',
        'marcs_cwf_settings_group'
    );
    add_settings_field(
        'marcs_cwf_disable_core_update_emails_field',
        'Disable Core Update Emails',
        'marcs_cwf_disable_core_update_emails_field_cb',
        'marcs_cwf_settings_group',
        'marcs_cwf_updates_section'
    );
    add_settings_field(
        'marcs_cwf_disable_plugin_update_emails_field',
        'Disable Plugin Update Emails',
        'marcs_cwf_disable_plugin_update_emails_field_cb',
        'marcs_cwf_settings_group',
        'marcs_cwf_updates_section'
    );
    add_settings_field(
        'marcs_cwf_disable_theme_update_emails_field',
        'Disable Theme Update Emails',
        'marcs_cwf_disable_theme_update_emails_field_cb',
        'marcs_cwf_settings_group',
        'marcs_cwf_updates_section'
    );

    // 🆕 Site Health Section
    add_settings_section(
        'marcs_cwf_site_health_section',
        'Site Health Settings',
        'marcs_cwf_site_health_section_cb',
        'marcs_cwf_settings_group'
    );
    add_settings_field(
        'marcs_cwf_disable_php_version_test_field',
        'Disable PHP Version Test',
        'marcs_cwf_disable_php_version_test_field_cb',
        'marcs_cwf_settings_group',
        'marcs_cwf_site_health_section'
    );
    add_settings_field(
        'marcs_cwf_disable_theme_version_test_field',
        'Disable Theme Version Test',
        'marcs_cwf_disable_theme_version_test_field_cb',
        'marcs_cwf_settings_group',
        'marcs_cwf_site_health_section'
    );
    add_settings_field(
        'marcs_cwf_disable_https_status_test_field',
        'Disable HTTPS Status Test',
        'marcs_cwf_disable_https_status_test_field_cb',
        'marcs_cwf_settings_group',
        'marcs_cwf_site_health_section'
    );
    add_settings_field(
        'marcs_cwf_disable_background_updates_test_field',
        'Disable Background Updates Test',
        'marcs_cwf_disable_background_updates_test_field_cb',
        'marcs_cwf_settings_group',
        'marcs_cwf_site_health_section'
    );
}

// Section callbacks
function marcs_cwf_theme_section_cb() { echo '<p>Set the theme color used by modern browsers for UI elements.</p>'; }
function marcs_cwf_comment_section_cb() { echo '<p>Configure how comments behave on your website.</p>'; }
function marcs_cwf_advanced_section_cb() { echo '<p>Configure advanced settings to improve security or adjust site functionality.</p>'; }
function marcs_cwf_updates_section_cb() { echo '<p>Control update notification emails. Disable specific emails here.</p>'; }
function marcs_cwf_site_health_section_cb() { echo '<p>Control which Site Health tests are disabled.</p>'; }

// Field callbacks
function marcs_cwf_theme_color_field_cb() {
    $theme_color = get_option('marcs_cwf_theme_color', '#db5945');
    echo '<input type="text" name="marcs_cwf_theme_color" value="' . esc_attr($theme_color) . '" class="regular-text" />';
}

function marcs_cwf_disable_comments_field_cb() {
    $disable_comments = get_option('marcs_cwf_disable_comments', 0);
    ?>
    <fieldset>
        <label><input type="radio" name="marcs_cwf_disable_comments" value="0" <?php checked($disable_comments, 0); ?>> Remove only the URL field.</label><br>
        <label><input type="radio" name="marcs_cwf_disable_comments" value="1" <?php checked($disable_comments, 1); ?>> Disable all comments entirely.</label>
    </fieldset>
    <?php
}

function marcs_cwf_disable_xmlrpc_field_cb() {
    $value = get_option('marcs_cwf_disable_xmlrpc', 0);
    echo '<input type="checkbox" name="marcs_cwf_disable_xmlrpc" value="1" ' . checked(1, $value, false) . '> Disable XML-RPC';
}

// Update email fields
function marcs_cwf_disable_core_update_emails_field_cb() {
    $v = get_option('marcs_cwf_disable_core_update_emails', 0);
    echo '<input type="checkbox" name="marcs_cwf_disable_core_update_emails" value="1" ' . checked(1, $v, false) . '> Disable Core Update Success Emails';
}
function marcs_cwf_disable_plugin_update_emails_field_cb() {
    $v = get_option('marcs_cwf_disable_plugin_update_emails', 0);
    echo '<input type="checkbox" name="marcs_cwf_disable_plugin_update_emails" value="1" ' . checked(1, $v, false) . '> Disable Plugin Update Emails';
}
function marcs_cwf_disable_theme_update_emails_field_cb() {
    $v = get_option('marcs_cwf_disable_theme_update_emails', 0);
    echo '<input type="checkbox" name="marcs_cwf_disable_theme_update_emails" value="1" ' . checked(1, $v, false) . '> Disable Theme Update Emails';
}

// 🆕 Site Health fields
function marcs_cwf_disable_php_version_test_field_cb() {
    $v = get_option('marcs_cwf_disable_php_version_test', 0);
    echo '<input type="checkbox" name="marcs_cwf_disable_php_version_test" value="1" ' . checked(1, $v, false) . '> Disable PHP Version Test';
}
function marcs_cwf_disable_theme_version_test_field_cb() {
    $v = get_option('marcs_cwf_disable_theme_version_test', 0);
    echo '<input type="checkbox" name="marcs_cwf_disable_theme_version_test" value="1" ' . checked(1, $v, false) . '> Disable Theme Version Test';
}
function marcs_cwf_disable_https_status_test_field_cb() {
    $v = get_option('marcs_cwf_disable_https_status_test', 0);
    echo '<input type="checkbox" name="marcs_cwf_disable_https_status_test" value="1" ' . checked(1, $v, false) . '> Disable HTTPS Status Test';
}
function marcs_cwf_disable_background_updates_test_field_cb() {
    $v = get_option('marcs_cwf_disable_background_updates_test', 0);
    echo '<input type="checkbox" name="marcs_cwf_disable_background_updates_test" value="1" ' . checked(1, $v, false) . '> Disable Background Updates Test';
}

// Page output
function marcs_cwf_settings_page_html() {
    if (!current_user_can('manage_options')) return;
    ?>
    <div class="wrap">
        <h1>Marc's Custom WP Functions Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('marcs_cwf_settings_group');
            do_settings_sections('marcs_cwf_settings_group');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
