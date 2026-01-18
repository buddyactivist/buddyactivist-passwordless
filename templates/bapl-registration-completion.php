<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$payment_shortcode = get_option( 'bapl_payment_shortcode', '' );
$has_payment = ! empty( $payment_shortcode );
?>

<div class="bapl-registration-completion">

    <form method="post" enctype="multipart/form-data">

        <?php wp_nonce_field( 'bapl_registration_completion', 'bapl_nonce' ); ?>

        <!-- Campi xProfile + avatar -->

        <button type="submit" name="bapl_registration_complete">
            <?php echo $has_payment
                ? esc_html__( 'Continue registration', 'buddyactivist-passwordless' )
                : esc_html__( 'Complete registration', 'buddyactivist-passwordless' ); ?>
        </button>

    </form>

</div>
