<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_user_id = get_current_user_id();
$messages        = bapl_get_messages();
?>
<?php if ( ! empty( $messages ) ) : ?>
    <div class="bapl-messages">
        <?php foreach ( $messages as $message ) : ?>
            <div class="bapl-message bapl-message-<?php echo esc_attr( $message['type'] ); ?>">
                <?php echo esc_html( $message['text'] ); ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" class="bapl-form bapl-form-registration-completion" enctype="multipart/form-data">

    <p><?php esc_html_e( 'Complete your profile to finish your registration.', 'buddyactivist-passwordless' ); ?></p>

    <?php if ( bapl_is_bp_xprofile_active() && function_exists( 'bp_has_profile' ) ) : ?>

        <div class="bapl-profile-fields">
            <?php
            if ( bp_has_profile( [ 'user_id' => $current_user_id ] ) ) :
                while ( bp_profile_groups() ) :
                    bp_the_profile_group();
                    ?>
                    <div class="bapl-profile-group">
                        <h4><?php bp_the_profile_group_name(); ?></h4>

                        <?php
                        while ( bp_profile_fields() ) :
                            bp_the_profile_field();

                            $field_id    = bp_get_the_profile_field_id();
                            $field_value = bp_get_the_profile_field_edit_value();
                            ?>
                            <div class="bapl-profile-field">
                                <label for="bapl_xprofile_<?php echo esc_attr( $field_id ); ?>">
                                    <?php bp_the_profile_field_name(); ?>
                                </label>

                                <input
                                    type="text"
                                    id="bapl_xprofile_<?php echo esc_attr( $field_id ); ?>"
                                    name="bapl_xprofile[<?php echo esc_attr( $field_id ); ?>]"
                                    value="<?php echo esc_attr( $field_value ); ?>"
                                />
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

    <?php else : ?>

        <p><?php esc_html_e( 'Profile fields are not available at the moment.', 'buddyactivist-passwordless' ); ?></p>

    <?php endif; ?>

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
