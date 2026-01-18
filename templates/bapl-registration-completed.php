<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $post;

// Payment shortcode to detect
$payment_shortcode = 'your_payment_shortcode';

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
        // Retrieve timestamp for countdown
        $timestamp = (int) get_user_meta( get_current_user_id(), 'bapl_payment_timestamp', true );

        // 60 minutes = 3600 seconds
        $remaining = max( 0, ( $timestamp + 3600 ) - time() );
        ?>

        <div id="bapl-timer" data-remaining="<?php echo esc_attr( $remaining ); ?>">
            <?php esc_html_e( 'You have 60 minutes to complete the payment.', 'buddyactivist-passwordless' ); ?>
        </div>

        <script>
        (function() {
            const el = document.getElementById('bapl-timer');
            if (!el) return;

            let remaining = parseInt(el.dataset.remaining, 10);

            function updateTimer() {
                if (remaining <= 0) {
                    el.textContent = "<?php echo esc_js( __( 'Your time to complete the payment has expired.', 'buddyactivist-passwordless' ) ); ?>";
                    return;
                }

                const minutes = Math.floor(remaining / 60);
                const seconds = remaining % 60;

                el.textContent = minutes + "m " + (seconds < 10 ? "0" : "") + seconds + "s";

                remaining--;
                setTimeout(updateTimer, 1000);
            }

            updateTimer();
        })();
        </script>

        <?php
        // Render payment shortcode via page content
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
