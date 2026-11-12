<?php
/**
 * Disable selected Site Health checks based on the plugin settings.
 *
 * @param array $tests Site Health test definitions.
 * @return array
 */
function simple_wp_toolbox_filter_site_health_tests($tests) {
    $disabled_tests = array(
        'direct' => array(
            'php_version'  => 'simple_wp_toolbox_disable_php_version_test',
            'https_status' => 'simple_wp_toolbox_disable_https_status_test',
        ),
        'async'  => array(
            'theme_version'      => 'simple_wp_toolbox_disable_theme_version_test',
            'background_updates' => 'simple_wp_toolbox_disable_background_updates_test',
        ),
    );

    foreach ($disabled_tests as $type => $test_options) {
        foreach ($test_options as $test_name => $option_name) {
            if (get_option($option_name) && isset($tests[$type][$test_name])) {
                unset($tests[$type][$test_name]);
            }
        }
    }

    return $tests;
}
add_filter('site_status_tests', 'simple_wp_toolbox_filter_site_health_tests');
