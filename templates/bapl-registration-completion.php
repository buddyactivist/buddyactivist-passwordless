<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<form method="post" class="bapl-form bapl-form-registration-completion" enctype="multipart/form-data">

    <p><?php esc_html_e( 'Complete your profile to finish your registration.', 'buddyactivist-passwordless' ); ?></p>

    <div class="bapl-profile-fields">
        <?php
        // Placeholder for future BuddyPress xProfile integration.
        ?>
    </div>

    <div class="bapl-avatar-upload">
        <label>
            <?php esc_html_e( 'Upload your avatar (optional)', 'buddyactivist-passwordless' ); ?>
            <input type="file" name="bapl_avatar" />
        </label>
    </div>

    <?php wp_nonce_field( 'bapl_registration_completion', 'bapl_nonce' ); ?>

    <button type="submit" name="bapl_registration_complete">
        <?php esc_html_e( 'Complete registration', 'buddyactivist-passwordless' ); ?>
    </button>
</form>
