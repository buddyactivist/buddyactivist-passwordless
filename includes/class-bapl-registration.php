public function handle_completion_submit() {

    if ( ! isset( $_POST['bapl_nonce'] ) || ! wp_verify_nonce( $_POST['bapl_nonce'], 'bapl_registration_completion' ) ) {
        return;
    }

    $user_id = get_current_user_id();

    // Saving xProfile + avatar 

    $payment_shortcode = get_option( 'bapl_payment_shortcode', '' );

    if ( ! empty( $payment_shortcode ) ) {

        // Redirect to payment page
        wp_redirect( site_url( '/bapl-registration-completed/' ) );
        exit;

    } else {

        // Redirect to Buddypress profile
        if ( function_exists( 'bp_core_get_user_domain' ) ) {
            wp_redirect( bp_core_get_user_domain( $user_id ) );
        } else {
            wp_redirect( get_author_posts_url( $user_id ) );
        }
        exit;
    }
}
