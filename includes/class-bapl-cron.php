<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class BAPL_Cron {

    public function init() {
        add_filter( 'cron_schedules', [ $this, 'add_quarterhourly_schedule' ] );

        if ( ! wp_next_scheduled( 'bapl_check_unpaid_users' ) ) {
            wp_schedule_event( time(), 'quarterhourly', 'bapl_check_unpaid_users' );
        }

        add_action( 'bapl_check_unpaid_users', [ $this, 'check_unpaid_users' ] );
    }

    public function add_quarterhourly_schedule( $schedules ) {
        $schedules['quarterhourly'] = [
            'interval' => 15 * 60,
            'display'  => 'Every 15 minutes'
        ];
        return $schedules;
    }

    public function check_unpaid_users() {

        $args = [
            'meta_key'     => 'bapl_payment_timestamp',
            'meta_compare' => 'EXISTS',
            'fields'       => 'ID'
        ];

        $users = get_users( $args );

        foreach ( $users as $user_id ) {

            $timestamp = (int) get_user_meta( $user_id, 'bapl_payment_timestamp', true );
            $completed = get_user_meta( $user_id, 'bapl_payment_completed', true );

            if ( $completed ) continue;

            if ( time() > $timestamp + 3600 ) {
                require_once ABSPATH . 'wp-admin/includes/user.php';
                wp_delete_user( $user_id );
            }
        }
    }
}
