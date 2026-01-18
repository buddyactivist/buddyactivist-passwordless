<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class BAPL_Registration {

    public function init() {
        add_action( 'init', [ $this, 'maybe_handle_completion_submit' ] );
    }

    public function maybe_handle_completion_submit() {
        if ( isset( $_POST['bapl_registration_complete'] ) ) {
            $this->handle_completion_submit();
        }
    }

    public function handle_completion_submit() {

        if ( ! isset( $_POST['bapl_nonce'] ) || ! wp_verify_nonce( $_POST['bapl_nonce'], 'bapl_registration_completion' ) ) {
            return;
        }

        $user_id = get_current_user_id();

        // Salvataggio avatar + xProfile (già implementato nel tuo plugin)

        // Salva timestamp per cron
        update_user_meta( $user_id, 'bapl_payment_timestamp', time() );

        $payment_shortcode = get_option( 'bapl_payment_shortcode', '' );

        if ( ! empty( $payment_shortcode ) ) {

            wp_redirect( site_url( '/bapl-registration-completed/' ) );
            exit;

        } else {

            delete_user_meta( $user_id, 'bapl_payment_timestamp' );
            update_user_meta( $user_id, 'bapl_payment_completed', 1 );

            if ( function_exists( 'bp_core_get_user_domain' ) ) {
                wp_redirect( bp_core_get_user_domain( $user_id ) );
            } else {
                wp_redirect( get_author_posts_url( $user_id ) );
            }
            exit;
        }
    }
}
