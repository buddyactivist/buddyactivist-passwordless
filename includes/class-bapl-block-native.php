<?php
/**
 * Blocks all native WordPress, BuddyPress, and BuddyBoss
 * registration, login, and password reset flows.
 *
 * Ensures BuddyActivist Passwordless is the ONLY authentication system.
 *
 * @package BuddyActivist_Passwordless
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BAPL_Block_Native {

    /**
     * Initialize hooks.
     */
    public function init() {

        // Block WordPress login, register, lost password
        add_action( 'login_init', [ $this, 'block_wp_login_register_reset' ], 1 );

        // Block BuddyPress register and activate pages
        add_action( 'bp_init', [ $this, 'block_bp_register_activate' ], 1 );

        // Override BuddyPress login URL
        add_filter( 'bp_get_login_url', [ $this, 'filter_bp_login_url' ] );

        // Override WordPress lost password URL
        add_filter( 'lostpassword_url', [ $this, 'filter_lostpassword_url' ], 10, 2 );

        // Override BuddyBoss login/register URLs
        add_filter( 'buddyboss_theme_login_url', [ $this, 'filter_bb_login_url' ] );
        add_filter( 'buddyboss_theme_register_url', [ $this, 'filter_bb_register_url' ] );
    }

    /**
     * Block WordPress login, registration, and password reset.
     */
    public function block_wp_login_register_reset() {

        $action = isset( $_REQUEST['action'] ) ? sanitize_key( $_REQUEST['action'] ) : 'login';

        // Block registration
        if ( $action === 'register' ) {
            bapl_redirect( bapl_get_page_url( 'bapl-registration' ) );
        }

        // Block lost password and reset password
        if ( in_array( $action, [ 'lostpassword', 'rp', 'resetpass' ], true ) ) {
            bapl_redirect( bapl_get_page_url( 'bapl-login' ) );
        }

        // Block login
        if ( $action === 'login' ) {
            bapl_redirect( bapl_get_page_url( 'bapl-login' ) );
        }
    }

    /**
     * Block BuddyPress registration and activation pages.
     */
    public function block_bp_register_activate() {

        // Override BuddyPress signup page
        add_filter( 'bp_get_signup_page', function() {
            return bapl_get_page_url( 'bapl-registration' );
        } );

        // Override BuddyPress activation page
        add_filter( 'bp_get_activation_page', function() {
            return bapl_get_page_url( 'bapl-registration' );
        } );
    }

    /**
     * Override BuddyPress login URL.
     *
     * @return string
     */
    public function filter_bp_login_url() {
        return bapl_get_page_url( 'bapl-login' );
    }

    /**
     * Override WordPress lost password URL.
     *
     * @return string
     */
    public function filter_lostpassword_url() {
        return bapl_get_page_url( 'bapl-login' );
    }

    /**
     * Override BuddyBoss login URL.
     *
     * @return string
     */
    public function filter_bb_login_url() {
        return bapl_get_page_url( 'bapl-login' );
    }

    /**
     * Override BuddyBoss register URL.
     *
     * @return string
     */
    public function filter_bb_register_url() {
        return bapl_get_page_url( 'bapl-registration' );
    }
}
