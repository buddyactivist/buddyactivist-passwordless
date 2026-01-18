<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<form method="post" class="bapl-form bapl-form-login">
    <p><?php esc_html_e( 'Enter your email address to log in.', 'buddyactivist-passwordless' ); ?></p>

    <div class="bapl-form-row">
        <input type="email" name="bapl_email" required />
        <button type="submit" name="bapl_login_send">
            <?php esc_html_e( 'Send magic link', 'buddyactivist-passwordless' ); ?>
        </button>
    </div>

    <?php wp_nonce_field( 'bapl_login', 'bapl_nonce' ); ?>
</form>
