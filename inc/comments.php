<?php
// Get user setting for comment behavior: 0 = remove URL field only, 1 = disable comments entirely
$disable_comments = get_option('simple_wp_toolbox_disable_comments', 0);

if ($disable_comments) {
    /**
     * Disabling all comments entirely:
     * Code sourced from WPBeginner.
     * Reference: https://www.wpbeginner.com/
     */
    
    add_action('admin_init', function () {
        // Redirect any user trying to access comments page
        global $pagenow;
        if ($pagenow === 'edit-comments.php') {
            wp_safe_redirect(admin_url());
            exit;
        }

        // Remove comments metabox from dashboard
        remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
        
        // Disable support for comments and trackbacks in all post types
        foreach (get_post_types() as $post_type) {
            if (post_type_supports($post_type, 'comments')) {
                remove_post_type_support($post_type, 'comments');
                remove_post_type_support($post_type, 'trackbacks');
            }
        }
    });

    // Close comments on the front-end
    add_filter('comments_open', '__return_false', 20, 2);
    add_filter('pings_open', '__return_false', 20, 2);

    // Hide existing comments
    add_filter('comments_array', '__return_empty_array', 10, 2);

    // Remove comments page in menu
    add_action('admin_menu', function () {
        remove_menu_page('edit-comments.php');
    });

    // Remove comments links from admin bar (WPBeginner code)
    add_action('init', function () {
        if (is_admin_bar_showing()) {
            remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
        }
    });

    // Ensure the comment icon is completely removed from the admin bar
    // by removing the comments node explicitly.
    add_action('admin_bar_menu', 'simple_wp_toolbox_remove_comments_icon', 999);
    function simple_wp_toolbox_remove_comments_icon($wp_admin_bar) {
        $wp_admin_bar->remove_node('comments');
    }

} else {
    // Default behavior: remove URL field from the comment form only
    add_filter('comment_form_default_fields', 'cfp_remove_url_field');
    function cfp_remove_url_field($fields) {
        if (isset($fields['url'])) {
            unset($fields['url']);
        }
        return $fields;
    }
}
