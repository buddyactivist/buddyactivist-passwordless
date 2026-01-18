<?php
/**
 * Security utilities for BuddyActivist Passwordless Registration and Login.
 *
 * @package BuddyActivist_Passwordless
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BAPL_Security {

    /**
     * Generate a safe alphanumeric username based on an email address.
     *
     * @param string $email User email.
     * @return string Generated username.
     */
    public static function generate_username_from_email( $email ) {

        // Extract the part before @
        $base = current( explode( '@', $email ) );

        // Remove non-alphanumeric characters
        $base = preg_replace( '/[^a-zA-Z0-9]/', '', $base );

        if ( empty( $base ) ) {
            $base = 'user';
        }

        $base = strtolower( $base );
        $username = $base;
        $i = 1;

        // Ensure uniqueness
        while ( username_exists( $username ) ) {
            $username = $base . $i;
            $i++;
        }

        return $username;
    }

    /**
     * Generate a secure random password.
     *
     * @param int $length Password length.
     * @return string Random password.
     */
    public static function generate_random_password( $length = 24 ) {
        return wp_generate_password( $length, true, true );
    }

    /**
     * Verify a WordPress nonce.
     *
     * @param string $nonce Nonce value.
     * @param string $action Nonce action.
     * @return bool
     */
    public static function verify_nonce( $nonce, $action ) {
        return wp_verify_nonce( $nonce, $action );
    }
}
