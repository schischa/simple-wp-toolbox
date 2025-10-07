<?php
// Disable selected Site Health checks based on settings
add_filter('site_status_tests', function ($tests) {

    if (get_option('mcfp_disable_site_health_php_version')) {
        unset($tests['direct']['php_version']);
    }

    if (get_option('mcfp_disable_site_health_theme_version')) {
        unset($tests['direct']['theme_version']);
    }

    return $tests;
}, 10, 1);
