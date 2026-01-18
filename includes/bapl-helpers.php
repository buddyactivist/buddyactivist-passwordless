<?php
/**
 * Helper functions for BuddyActivist Passwordless Registration and Login.
 *
 * @package BuddyActivist_Passwordless
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get the URL of a page by its slug.
 *
 * @param string $slug Page slug.
 * @return string URL.
 */
function bapl_get_page_url( $slug ) {
    $page = get_page_by_path( $slug );
    if ( $page ) {
        return get_permalink( $page->ID );
    }
    return home_url( '/' . $slug . '/' );
}

/**
 * Safe redirect and exit.
 *
 * @param string $url Target URL.
 */
function bapl_redirect( $url ) {
    wp_safe_redirect( $url );
    exit;
}

/**
 * Check if BuddyPress xProfile component is active.
 *
 * @return bool
 */
function bapl_is_bp_xprofile_active() {
    return function_exists( 'bp_is_active' ) && bp_is_active( 'xprofile' );
}

/**
 * Check if BuddyPress avatar functions are available.
 *
 * @return bool
 */
function bapl_is_bp_avatar_active() {
    return function_exists( 'bp_core_avatar_handle_upload' );
}
