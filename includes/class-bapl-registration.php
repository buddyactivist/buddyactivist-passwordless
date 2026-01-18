<?php
/**
 * Registration flow for BuddyActivist Passwordless Registration and Login.
 *
 * @package BuddyActivist_Passwordless
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BAPL_Registration {

    public function init() {
        add_shortcode( 'bapl_registration', [ $this, 'render_registration_form' ] );
        add_shortcode( 'bapl_registration_completion', [ $this, 'render_completion_form' ] );
        add_shortcode( 'bapl_registration_completed', [ $this, 'render_completed_page' ] );

        add_action( 'init', [ $this, 'handle_magic_link' ] );
        add_action( 'init', [ $this, 'handle_completion_submit' ] );
        add_action( 'init', [ $this, 'handle_end_registration' ] );
    }

    public function render_registration_form() {
        if ( isset( $_POST['bapl_registration_send'] ) ) {
            $this->handle_registration_request();
        }
        ob_start();
        include BAPL_PLUGIN_DIR . 'templates/bapl-registration-form.php';
        return ob_get_clean();
    }

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

    public function handle_magic_link() {

        if ( empty( $_GET['bapl_action'] ) ||
             $_GET['bapl_action'] !== 'registration' ||
             empty( $_GET['bapl_token'] ) ) {
            return;
        }

        $token = sanitize_text_field( wp_unslash( $_GET['bapl_token'] ) );
        $data  = BAPL_Magic_Link::validate( $token, 'registration' );

        if ( ! $data ) {
            return;
        }

        $email = $data['email'];
        $user  = get_user_by( 'email', $email );

        if ( ! $user ) {
            $username = BAPL_Security::generate_username_from_email( $email );
            $password = BAPL_Security::generate_random_password();
            $user_id  = wp_create_user( $username, $password, $email );

            if ( is_wp_error( $user_id ) ) {
                return;
            }

            $user = get_user_by( 'id', $user_id );
        }

        wp_set_current_user( $user->ID );
        wp_set_auth_cookie( $user->ID, true );

        update_user_meta( $user->ID, 'bapl_registration_incomplete', 1 );

        bapl_redirect( bapl_get_page_url( 'bapl-registration-completion' ) );
    }

    public function render_completion_form() {
        ob_start();
        include BAPL_PLUGIN_DIR . 'templates/bapl-registration-completion.php';
        return ob_get_clean();
    }

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
         * Save BuddyPress xProfile fields.
         */
        if ( bapl_is_bp_xprofile_active() &&
             ! empty( $_POST['bapl_xprofile'] ) &&
             is_array( $_POST['bapl_xprofile'] ) ) {

            $xprofile_data = wp_unslash( $_POST['bapl_xprofile'] );

            foreach ( $xprofile_data as $field_id => $value ) {

                $field_id = (int) $field_id;
                if ( $field_id <= 0 ) {
                    continue;
                }

                $value = sanitize_text_field( $value );
                if ( $value === '' ) {
                    continue;
                }

                xprofile_set_field_data( $field_id, $user_id, $value );
            }
        }

        /**
         * Avatar upload handling (BuddyPress).
         */
        if ( bapl_is_bp_avatar_active() && ! empty( $_FILES['bapl_avatar']['name'] ) ) {

            require_once ABSPATH . 'wp-admin/includes/file.php';

            $file = $_FILES['bapl_avatar'];
            $allowed = [ 'image/jpeg', 'image/png', 'image/gif' ];

            if ( in_array( $file['type'], $allowed, true ) ) {

                $uploaded = wp_handle_upload( $file, [ 'test_form' => false ] );

                if ( ! isset( $uploaded['error'] ) ) {

                    $avatar_args = [
                        'item_id'       => $user_id,
                        'object'        => 'user',
                        'original_file' => $uploaded['file'],
                    ];

                    $avatar = bp_core_avatar_handle_upload( $avatar_args );

                    if ( ! is_wp_error( $avatar ) ) {

                        $crop_args = [
                            'item_id'       => $user_id,
                            'object'        => 'user',
                            'original_file' => $uploaded['file'],
                            'crop_w'        => 150,
                            'crop_h'        => 150,
                            'crop_x'        => 0,
                            'crop_y'        => 0,
                        ];

                        bp_core_avatar_handle_crop( $crop_args );
                    }
                }
            }
        }

        delete_user_meta( $user_id, 'bapl_registration_incomplete' );

        bapl_redirect( bapl_get_page_url( 'bapl-registration-completed' ) );
    }

    public function render_completed_page() {
        ob_start();
        include BAPL_PLUGIN_DIR . 'templates/bapl-registration-completed.php';
        return ob_get_clean();
    }

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

        if ( function_exists( 'bp_core_get_user_domain' ) ) {
            $url = bp_core_get_user_domain( $user_id );
        } else {
            $url = get_author_posts_url( $user_id );
        }

        bapl_redirect( $url );
    }
}
