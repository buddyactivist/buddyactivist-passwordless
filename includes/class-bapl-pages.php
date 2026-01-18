<?php
/**
 * Handle dynamic pages for BuddyActivist Passwordless.
 *
 * - Creates the "Registration Completed" page only if a payment shortcode is set.
 * - Deletes it if the payment shortcode is removed.
 *
 * @package BuddyActivist_Passwordless
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BAPL_Pages {

    /**
     * Init hooks.
     */
    public function init() {

        // React when the payment shortcode option changes.
        add_action( 'update_option_bapl_payment_shortcode', [ $this, 'maybe_sync_completed_page' ], 10, 2 );

        // Also ensure consistency on plugin load (e.g. first install / manual DB changes).
        add_action( 'init', [ $this, 'ensure_pages_state' ] );
    }

    /**
     * Ensure pages state on init (idempotent).
     */
    public function ensure_pages_state() {

        $payment_shortcode = get_option( 'bapl_payment_shortcode', '' );

        if ( empty( $payment_shortcode ) ) {
            // No payment → make sure the page does not exist.
            $this->delete_completed_page();
        } else {
            // Payment active → make sure the page exists and is synced.
            $this->create_or_update_completed_page( $payment_shortcode );
        }
    }

    /**
     * Called when the payment shortcode option is updated.
     *
     * @param string $old Old value.
     * @param string $new New value.
     */
    public function maybe_sync_completed_page( $old, $new ) {

        $new = trim( (string) $new );

        if ( $new === '' ) {
            // Shortcode removed → delete page.
            $this->delete_completed_page();
            return;
        }

        // Shortcode added or changed → create or update page.
        $this->create_or_update_completed_page( $new );
    }

    /**
     * Create or update the "Registration Completed" page.
     *
     * @param string $shortcode Payment shortcode.
     */
    protected function create_or_update_completed_page( $shortcode ) {

        $slug = 'bapl-registration-completed';

        // Try to find existing page by path.
        $page = get_page_by_path( $slug );

        $content = $shortcode;

        if ( $page ) {

            // Update existing page if needed.
            $update_args = [
                'ID'           => $page->ID,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => $content,
            ];

            // Avoid unnecessary DB writes if content is already the same.
            if ( $page->post_content !== $content || $page->post_status !== 'publish' ) {
                wp_update_post( $update_args );
            }

        } else {

            // Create new page.
            $page_args = [
                'post_title'   => 'Registration Completed',
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => $content,
            ];

            wp_insert_post( $page_args );
        }
    }

    /**
     * Delete the "Registration Completed" page if it exists.
     */
    protected function delete_completed_page() {

        $slug = 'bapl-registration-completed';
        $page = get_page_by_path( $slug );

        if ( $page && ! is_wp_error( $page ) ) {
            wp_delete_post( $page->ID, true );
        }
    }
}
