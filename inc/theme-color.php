<?php
// Add theme-color Meta Tag (fetched from options)
add_action('wp_head', 'simple_wp_toolbox_output_theme_color_meta');
function simple_wp_toolbox_output_theme_color_meta() {
    $theme_color = get_option('simple_wp_toolbox_theme_color', '#db5945');
    ?>
    <meta name="theme-color" content="<?php echo esc_attr($theme_color); ?>">
    <?php
}
