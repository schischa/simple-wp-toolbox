<?php
// Whitelabelling: Change login logo to website's custom logo (or default WP logo if none set)
add_action('login_enqueue_scripts', 'simple_wp_toolbox_login_logo');
function simple_wp_toolbox_login_logo() {
    $custom_logo_id = get_theme_mod('custom_logo');
    $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');

    if (!$logo_url) {
        $logo_url = admin_url('images/wordpress-logo.svg');
    }

    echo '<style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(' . esc_url($logo_url) . ');
            height: 150px;
            width: 100%;
            background-size: contain;
            background-repeat: no-repeat;
        }
    </style>';
}

// Modify login logo link URL to point to site's homepage
add_filter('login_headerurl', 'simple_wp_toolbox_login_logo_url');
function simple_wp_toolbox_login_logo_url() {
    return home_url();
}

// Modify the login logo title attribute to the site's name
add_filter('login_headertext', 'simple_wp_toolbox_login_logo_url_title');
function simple_wp_toolbox_login_logo_url_title() {
    return get_bloginfo('name');
}
