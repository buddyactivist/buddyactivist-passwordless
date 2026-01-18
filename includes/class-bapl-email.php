<?php
/**
 * Email handling for BuddyActivist Passwordless Registration and Login.
 *
 * @package BuddyActivist_Passwordless
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BAPL_Email {

    /**
     * Send a magic link email.
     *
     * @param string $email  Recipient email.
     * @param string $action Action type: registration|login.
     * @param string $token  Magic link token.
     * @return bool Whether the email was sent successfully.
     */
    public static function send_magic_link( $email, $action, $token ) {

        $subject = '';
        $message = '';
        $url     = '';

        if ( $action === 'registration' ) {

            $url = add_query_arg(
                [
                    'bapl_action' => 'registration',
                    'bapl_token'  => rawurlencode( $token ),
                ],
                home_url( '/' )
            );

            $subject = __( 'Complete your registration', 'buddyactivist-passwordless' );

            $message = sprintf(
                __( "Click the link below to complete your registration:\n\n%s", 'buddyactivist-passwordless' ),
                $url
            );

        } elseif ( $action === 'login' ) {

            $url = add_query_arg(
                [
                    'bapl_action' => 'login',
                    'bapl_token'  => rawurlencode( $token ),
                ],
                home_url( '/' )
            );

            $subject = __( 'Login to your account', 'buddyactivist-passwordless' );

            $message = sprintf(
                __( "Click the link below to log in to the site:\n\n%s", 'buddyactivist-passwordless' ),
                $url
            );
        }

        if ( empty( $subject ) || empty( $message ) ) {
            return false;
        }

        $headers = [ 'Content-Type: text/plain; charset=UTF-8' ];

        return wp_mail( $email, $subject, $message, $headers );
    }
}
