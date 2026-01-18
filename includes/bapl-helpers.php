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
 * Falls back to home_url( /slug/ ) if the page object cannot be found.
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
