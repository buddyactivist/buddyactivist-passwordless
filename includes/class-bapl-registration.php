<?php
/**
 * Registration flow for BuddyActivist Passwordless Registration and Login.
 *
 * Handles:
 * - Registration form shortcode
 * - Sending registration magic link
 * - Processing magic link
 * - Registration completion form
 * - Final registration step
 *
 * @package BuddyActivist_Passwordless
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BAPL_Registration {

    /**
     * Initialize hooks and shortcodes.
     */
    public function init() {

        // Shortcodes
        add_shortcode( 'bapl_registration', [ $this, 'render_registration_form' ] );
        add_shortcode( 'bapl_registration_completion', [ $this, 'render_completion_form' ] );
        add_shortcode( 'bapl_registration_completed', [ $this, 'render_completed_page' ] );

        // Magic link handler
        add_action( 'init', [ $this, 'handle_magic_link' ] );

        // Completion form handler
        add_action( 'init', [ $this, 'handle_completion_submit' ] );

        // Final step handler
        add_action( 'init', [ $this, 'handle_end_registration' ] );
    }

    /**
     * Render the registration form shortcode.
     *
     * @return string
     */
    public function render_registration_form() {

        if ( isset( $_POST['bapl_registration_send'] ) ) {
            $this->handle_registration_request();
        }

        ob_start();
        include BAPL_PLUGIN_DIR . 'templates/bapl-registration-form.php';
        return ob_get_clean();
    }

    /**
     * Handle registration form submission.
     */
    protected function handle_registration_request() {

        if ( empty( $_POST['bapl_nonce'] ) ||
             ! BAPL_Security::verify_nonce( $_POST['bapl_nonce'], 'bapl_registration' ) ) {
            return;
        }

        $email = isset( $_POST['bapl_email'] ) ? sanitize_email( $_POST['bapl_email'] ) : '';

        if ( ! is_email( $email ) ) {
            return;
        }

        $token = BAPL_Magic_Link::generate( $email, 'registration' );

        BAPL_Email::send_magic_link( $email, 'registration', $token );
    }

    /**
     * Handle registration magic link.
     */
    public function handle_magic_link() {

        if ( empty( $_GET['bapl_action'] ) ||
             $_GET['bapl_action'] !== 'registration' ||
             empty( $_GET['bapl_token'] ) ) {
            return;
        }

        $token = sanitize_text_field( wp_unslash( $_GET['bapl_token'] ) );

        $data = BAPL_Magic_Link::validate( $token, 'registration' );

        if ( ! $data ) {
            return;
        }

        $email = $data['email'];

        // Create or fetch user
        $user = get_user_by( 'email', $email );

        if ( ! $user ) {

            $username = BAPL_Security::generate_username_from_email( $email );
            $password = BAPL_Security::generate_random_password();

            $user_id = wp_create_user( $username, $password, $email );

            if ( is_wp_error( $user_id ) ) {
                return;
            }

            $user = get_user_by( 'id', $user_id );
        }

        // Log user in
        wp_set_current_user( $user->ID );
        wp_set_auth_cookie( $user->ID, true );

        // Mark registration as incomplete
        update_user_meta( $user->ID, 'bapl_registration_incomplete', 1 );

        // Redirect to completion page
        bapl_redirect( bapl_get_page_url( 'bapl-registration-completion' ) );
    }

    /**
     * Render the registration completion form.
     *
     * @return string
     */
    public function render_completion_form() {

        ob_start();
        include BAPL_PLUGIN_DIR . 'templates/bapl-registration-completion.php';
        return ob_get_clean();
    }

    /**
     * Handle registration completion form submission.
     */
    public function handle_completion_submit() {

        if ( ! isset( $_POST['bapl_registration_complete'] ) ) {
            return;
        }

        if ( empty( $_POST['bapl_nonce'] ) ||
             ! BAPL_Security::verify_nonce( $_POST['bapl_nonce'], 'bapl_registration_completion' ) ) {
            return;
        }

        if ( ! is_user_logged_in() ) {
            return;
        }

        $user_id = get_current_user_id();

        /**
         * Future extension:
         * - Save BuddyPress xProfile fields
         * - Save avatar upload
         */

        delete_user_meta( $user_id, 'bapl_registration_incomplete' );

        bapl_redirect( bapl_get_page_url( 'bapl-registration-completed' ) );
    }

    /**
     * Render the final registration page.
     *
     * @return string
     */
    public function render_completed_page() {

        ob_start();
        include BAPL_PLUGIN_DIR . 'templates/bapl-registration-completed.php';
        return ob_get_clean();
    }

    /**
     * Handle the final "End registration" button.
     */
    public function handle_end_registration() {

        if ( ! isset( $_POST['bapl_end_registration'] ) ) {
            return;
        }

        if ( empty( $_POST['bapl_nonce'] ) ||
             ! BAPL_Security::verify_nonce( $_POST['bapl_nonce'], 'bapl_registration_completed' ) ) {
            return;
        }

        if ( ! is_user_logged_in() ) {
            return;
        }

        $user_id = get_current_user_id();

        // Redirect to BuddyPress profile if available
        if ( function_exists( 'bp_core_get_user_domain' ) ) {
            $url = bp_core_get_user_domain( $user_id );
        } else {
            $url = get_author_posts_url( $user_id );
        }

        bapl_redirect( $url );
    }
}
