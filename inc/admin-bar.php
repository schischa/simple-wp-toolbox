k<?php
// Disable WP logo and links in the admin bar
add_action('admin_bar_menu', 'chr_remove_wp_logo', 999);
function chr_remove_wp_logo( $wp_admin_bar ) {
    $wp_admin_bar->remove_node('wp-logo');
}

// Replace "Howdy" with "Hello" in the admin bar (English admin only)
function chr_replace_howdy( $wp_admin_bar ) {
    // Get the current user's admin language
    $user_lang = function_exists('get_user_locale') ? get_user_locale() : get_locale();

    // Only modify greeting if the admin language is English
    if ( strpos( $user_lang, 'en_' ) !== 0 ) {
        return;
    }

    // Retrieve "My Account" node safely
    $my_account = $wp_admin_bar->get_node( 'my-account' );
    if ( ! $my_account || empty( $my_account->title ) ) {
        return;
    }

    // Replace "Howdy" with "Hello"
    $greeting = str_replace( 'Howdy,', 'Hello ', $my_account->title );
    $wp_admin_bar->add_node( array(
        'id'    => 'my-account',
        'title' => $greeting,
    ) );
}
add_filter( 'admin_bar_menu', 'chr_replace_howdy', 9999 );