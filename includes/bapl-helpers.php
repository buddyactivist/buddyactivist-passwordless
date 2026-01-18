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

/**
 * Add a message to be displayed in the current request.
 *
 * @param string $type Message type: success|error|info.
 * @param string $text Message text.
 */
function bapl_add_message( $type, $text ) {
    global $bapl_messages;

    if ( ! isset( $bapl_messages ) || ! is_array( $bapl_messages ) ) {
        $bapl_messages = [];
    }

    $type = in_array( $type, [ 'success', 'error', 'info' ], true ) ? $type : 'info';

    $bapl_messages[] = [
        'type' => $type,
        'text' => $text,
    ];
}

/**
 * Get all messages for the current request.
 *
 * @return array
 */
function bapl_get_messages() {
    global $bapl_messages;

    if ( ! isset( $bapl_messages ) || ! is_array( $bapl_messages ) ) {
        return [];
    }

    return $bapl_messages;
}
