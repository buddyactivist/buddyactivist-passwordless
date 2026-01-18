<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $post;

// Change this to your payment shortcode
$payment_shortcode = 'il_tuo_shortcode_di_pagamento';

$has_payment = false;

if ( isset( $post->post_content ) && has_shortcode( $post->post_content, $payment_shortcode ) ) {
    $has_payment = true;
}
?>

<div class="bapl-registration-completed">

    <!-- Message always visible -->
    <p><?php esc_html_e( 'To complete your registration, follow the instructions below.', 'buddyactivist-passwordless' ); ?></p>

    <?php if ( $has_payment ) : ?>

        <p><?php esc_html_e( 'Your registration is almost complete. If required, complete the payment below.', 'buddyactivist-passwordless' ); ?></p>

        <?php
        // The page content will render the payment shortcode
        the_content();
        ?>

    <?php else : ?>

        <p><?php esc_html_e( 'To complete your registration, press the button below.', 'buddyactivist-passwordless' ); ?></p>

        <form method="post" class="bapl-form bapl-form-end-registration">
            <?php wp_nonce_field( 'bapl_registration_completed', 'bapl_nonce' ); ?>
            <button type="submit" name="bapl_end_registration">
                <?php esc_html_e( 'Finish registration', 'buddyactivist-passwordless' ); ?>
            </button>
        </form>

    <?php endif; ?>

</div>
