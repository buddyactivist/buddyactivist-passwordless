<?php
if ( ! defined( 'ABSPATH' ) ) exit;

global $post;

$payment_shortcode = get_option( 'bapl_payment_shortcode', '' );
$has_payment = ! empty( $payment_shortcode );
?>

<div class="bapl-registration-completed">

    <!-- Messaggio introduttivo -->
    <p><?php esc_html_e( 'To complete your registration, follow the instructions below.', 'buddyactivist-passwordless' ); ?></p>

    <!-- Messaggio fallback pagamento fallito -->
    <?php if ( isset( $_GET['payment'] ) && $_GET['payment'] === 'failed' ) : ?>
        <div class="bapl-payment-error">
            <?php esc_html_e( 'The payment was not successful. Please try again.', 'buddyactivist-passwordless' ); ?>
        </div>
    <?php endif; ?>

    <?php if ( $has_payment ) : ?>

        <!-- Messaggio pagamento richiesto -->
        <p><?php esc_html_e( 'Your registration is almost complete. If required, complete the payment below.', 'buddyactivist-passwordless' ); ?></p>

        <?php
        // Recupero timestamp pagamento
        $timestamp = (int) get_user_meta( get_current_user_id(), 'bapl_payment_timestamp', true );

        // 60 minuti = 3600 secondi
        $remaining = max( 0, ( $timestamp + 3600 ) - time() );
        ?>

        <!-- Timer -->
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

        <!-- Shortcode pagamento -->
        <div class="bapl-payment-box">
            <?php the_content(); ?>
        </div>

        <!-- Redirect automatico dopo pagamento -->
        <script>
        document.addEventListener('bapl_payment_completed', function() {
            window.location.href = "<?php echo esc_url( function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( get_current_user_id() ) : home_url() ); ?>";
        });
        </script>

    <?php else : ?>

        <!-- Caso teorico: pagina esiste ma non c'è shortcode -->
        <p><?php esc_html_e( 'No payment is required to complete your registration.', 'buddyactivist-passwordless' ); ?></p>

    <?php endif; ?>

</div>
