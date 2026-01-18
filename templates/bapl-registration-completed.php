<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="bapl-registration-completed">

    <p><?php esc_html_e( 'Your registration is almost complete. If required, complete the payment below.', 'buddyactivist-passwordless' ); ?></p>

    <form method="post" class="bapl-form bapl-form-registration-completed">
        <?php wp_nonce_field( 'bapl_registration_completed', 'bapl_nonce' ); ?>

        <button type="submit" name="bapl_end_registration">
            <?php esc_html_e( 'Finish registration', 'buddyactivist-passwordless' ); ?>
        </button>
    </form>
</div>
