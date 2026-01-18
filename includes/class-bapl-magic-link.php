<?php
/**
 * Magic link generation and validation for BuddyActivist Passwordless Registration and Login.
 *
 * @package BuddyActivist_Passwordless
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BAPL_Magic_Link {

    /**
     * Token expiration time in seconds.
     * Default: 15 minutes.
     */
    const EXPIRATION = 900;

    /**
     * Generate a signed magic link token.
     *
     * @param string $email  User email.
     * @param string $action Action type: registration|login.
     * @return string Base64 encoded token.
     */
    public static function generate( $email, $action ) {

        $data = [
            'email'     => sanitize_email( $email ),
            'action'    => sanitize_key( $action ),
            'timestamp' => time(),
            'nonce'     => wp_generate_uuid4(),
        ];

        $payload   = wp_json_encode( $data );
        $signature = hash_hmac( 'sha256', $payload, self::get_secret_key() );

        $token = base64_encode(
            wp_json_encode(
                [
                    'payload'   => $payload,
                    'signature' => $signature,
                ]
            )
        );

        return $token;
    }

    /**
     * Validate a magic link token.
     *
     * @param string $token           Base64 encoded token.
     * @param string $expected_action Expected action: registration|login.
     * @return array|false Token data array or false if invalid.
     */
    public static function validate( $token, $expected_action ) {

        $decoded = json_decode( base64_decode( $token ), true );

        if ( ! is_array( $decoded ) || empty( $decoded['payload'] ) || empty( $decoded['signature'] ) ) {
            return false;
        }

        $payload   = $decoded['payload'];
        $signature = $decoded['signature'];

        // Verify signature
        $calc_signature = hash_hmac( 'sha256', $payload, self::get_secret_key() );

        if ( ! hash_equals( $calc_signature, $signature ) ) {
            return false;
        }

        $data = json_decode( $payload, true );

        if ( ! is_array( $data ) ) {
            return false;
        }

        // Required fields
        if ( empty( $data['email'] ) || empty( $data['action'] ) || empty( $data['timestamp'] ) ) {
            return false;
        }

        // Action mismatch
        if ( $data['action'] !== $expected_action ) {
            return false;
        }

        // Expired token
        if ( ( time() - (int) $data['timestamp'] ) > self::EXPIRATION ) {
            return false;
        }

        return $data;
    }

    /**
     * Retrieve the secret key used for signing tokens.
     *
     * @return string Secret key.
     */
    protected static function get_secret_key() {
        return wp_salt( 'bapl_magic_link' );
    }
}
