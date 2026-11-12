<?php
// Get user setting for comment behavior: 0 = remove URL field only, 1 = disable comments entirely
$disable_comments = get_option('simple_wp_toolbox_disable_comments', 0);

if ($disable_comments) {
    add_action('admin_init', 'simple_wp_toolbox_disable_comments_admin');
    add_filter('comments_open', '__return_false', 20, 2);
    add_filter('pings_open', '__return_false', 20, 2);
    add_filter('comments_array', '__return_empty_array', 10, 2);
    add_action('admin_menu', 'simple_wp_toolbox_remove_comments_menu');
    add_action('admin_bar_menu', 'simple_wp_toolbox_remove_comments_icon', 999);
} else {
    add_filter('comment_form_default_fields', 'simple_wp_toolbox_remove_comment_url_field');
}

/**
 * Disable comment administration and remove comment support from post types.
 */
function simple_wp_toolbox_disable_comments_admin() {
    global $pagenow;

    if ('edit-comments.php' === $pagenow) {
        wp_safe_redirect(admin_url());
        exit;
    }

    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
        }

        if (post_type_supports($post_type, 'trackbacks')) {
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}

/**
 * Remove the Comments item from the admin menu.
 */
function simple_wp_toolbox_remove_comments_menu() {
    remove_menu_page('edit-comments.php');
}

/**
 * Remove the Comments item from the admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
 */
function simple_wp_toolbox_remove_comments_icon($wp_admin_bar) {
    $wp_admin_bar->remove_node('comments');
}

/**
 * Remove the URL field from the comment form.
 *
 * @param array $fields Comment form fields.
 * @return array
 */
function simple_wp_toolbox_remove_comment_url_field($fields) {
    unset($fields['url']);

    return $fields;
}
