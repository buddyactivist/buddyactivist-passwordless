<?php
/**
 * Login flow for BuddyActivist Passwordless Registration and Login.
 *
 * Handles:
 * - Login form shortcode
 * - Sending login magic link
 * - Processing login magic link
 *
 * @package BuddyActivist_Passwordless
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BAPL_Login {

    /**
     * Initialize hooks and shortcodes.
     */
    public function init() {

        // Shortcode for login form
        add_shortcode( 'bapl_login', [ $this, 'render_login_form' ] );

        // Handle login form submission
        add_action( 'init', [ $this, 'handle_login_request' ] );

        // Handle login magic link
        add_action( 'init', [ $this, 'handle_magic_link' ] );
    }

    /**
     * Render the login form shortcode.
     *
     * @return string
     */
    public function render_login_form() {

        ob_start();
        include BAPL_PLUGIN_DIR . 'templates/bapl-login-form.php';
        return ob_get_clean();
    }

    /**
     * Handle login form submission.
     */
    public function handle_login_request() {

        if ( ! isset( $_POST['bapl_login_send'] ) ) {
            return;
        }

        if ( empty( $_POST['bapl_nonce'] ) ||
             ! BAPL_Security::verify_nonce( $_POST['bapl_nonce'], 'bapl_login' ) ) {
            return;
        }

        $email = isset( $_POST['bapl_email'] ) ? sanitize_email( $_POST['bapl_email'] ) : '';

        if ( ! is_email( $email ) ) {
            return;
        }

        $user = get_user_by( 'email', $email );

        if ( ! $user ) {
            return;
        }

        $token = BAPL_Magic_Link::generate( $email, 'login' );

        BAPL_Email::send_magic_link( $email, 'login', $token );
    }

    /**
     * Handle login magic link.
     */
    public function handle_magic_link() {

        if ( empty( $_GET['bapl_action'] ) ||
             $_GET['bapl_action'] !== 'login' ||
             empty( $_GET['bapl_token'] ) ) {
            return;
        }

        $token = sanitize_text_field( wp_unslash( $_GET['bapl_token'] ) );

        $data = BAPL_Magic_Link::validate( $token, 'login' );

        if ( ! $data ) {
            return;
        }

        $email = $data['email'];

        $user = get_user_by( 'email', $email );

        if ( ! $user ) {
            return;
        }

        // Log user in
        wp_set_current_user( $user->ID );
        wp_set_auth_cookie( $user->ID, true );

        // Redirect to BuddyPress profile if available
        if ( function_exists( 'bp_core_get_user_domain' ) ) {
            $url = bp_core_get_user_domain( $user->ID );
        } else {
            $url = get_author_posts_url( $user->ID );
        }

        bapl_redirect( $url );
    }
}
