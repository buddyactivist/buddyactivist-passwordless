<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class BAPL_Pages {

    public function init() {
        add_action( 'update_option_bapl_payment_shortcode', [ $this, 'maybe_sync_completed_page' ], 10, 2 );
        add_action( 'init', [ $this, 'ensure_pages_state' ] );
    }

    public function ensure_pages_state() {
        $payment_shortcode = get_option( 'bapl_payment_shortcode', '' );

        if ( empty( $payment_shortcode ) ) {
            $this->delete_completed_page();
        } else {
            $this->create_or_update_completed_page( $payment_shortcode );
        }
    }

    public function maybe_sync_completed_page( $old, $new ) {
        $new = trim( (string) $new );

        if ( $new === '' ) {
            $this->delete_completed_page();
            return;
        }

        $this->create_or_update_completed_page( $new );
    }

    protected function create_or_update_completed_page( $shortcode ) {
        $slug = 'bapl-registration-completed';
        $page = get_page_by_path( $slug );

        $content = $shortcode;

        if ( $page ) {
            $update_args = [
                'ID'           => $page->ID,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => $content,
            ];

            if ( $page->post_content !== $content || $page->post_status !== 'publish' ) {
                wp_update_post( $update_args );
            }

        } else {
            wp_insert_post([
                'post_title'   => 'Registration Completed',
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => $content,
            ]);
        }
    }

    protected function delete_completed_page() {
        $slug = 'bapl-registration-completed';
        $page = get_page_by_path( $slug );

        if ( $page && ! is_wp_error( $page ) ) {
            wp_delete_post( $page->ID, true );
        }
    }
}
