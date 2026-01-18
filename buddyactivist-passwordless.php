<?php
/**
 * Plugin Name: BuddyActivist Passwordless Registration and Login
 * Description: Passwordless registration and login for BuddyPress/BuddyBoss using email magic links.
 * Author: BuddyActivist
 * Version: 1.0.0
 * Text Domain: buddyactivist-passwordless
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin constants
 */
define( 'BAPL_VERSION', '1.0.0' );
define( 'BAPL_PLUGIN_FILE', __FILE__ );
define( 'BAPL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BAPL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin activation
 * - Disable WordPress registration
 * - Create required pages with bapl- prefixed slugs and shortcodes
 */
register_activation_hook( __FILE__, 'bapl_activate_plugin' );

function bapl_activate_plugin() {

    // Disable WordPress core registration
    update_option( 'users_can_register', 0 );

    // Pages to create
    $pages = [
        'bapl-registration' => [
            'title'     => 'Registration',
            'shortcode' => '[bapl_registration]',
        ],
        'bapl-registration-completion' => [
            'title'     => 'Registration Completion',
            'shortcode' => '[bapl_registration_completion]',
        ],
        'bapl-registration-completed' => [
            'title'     => 'Registration Completed',
            'shortcode' => '[bapl_registration_completed]',
        ],
        'bapl-login' => [
            'title'     => 'Login',
            'shortcode' => '[bapl_login]',
        ],
    ];

    foreach ( $pages as $slug => $data ) {

        $existing = get_page_by_path( $slug );

        if ( ! $existing ) {
            wp_insert_post( [
                'post_title'     => $data['title'],
                'post_name'      => $slug,
                'post_content'   => $data['shortcode'],
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_author'    => 1,
                'comment_status' => 'closed',
            ] );
        }
    }
}

/**
 * Plugin bootstrap
 */
add_action( 'plugins_loaded', 'bapl_init_plugin' );

function bapl_init_plugin() {

    // Load translations
    load_plugin_textdomain(
        'buddyactivist-passwordless',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages/'
    );

    // Core includes
    require_once BAPL_PLUGIN_DIR . 'includes/bapl-helpers.php';
    require_once BAPL_PLUGIN_DIR . 'includes/class-bapl-security.php';
    require_once BAPL_PLUGIN_DIR . 'includes/class-bapl-magic-link.php';
    require_once BAPL_PLUGIN_DIR . 'includes/class-bapl-email.php';
    require_once BAPL_PLUGIN_DIR . 'includes/class-bapl-registration.php';
    require_once BAPL_PLUGIN_DIR . 'includes/class-bapl-login.php';
    require_once BAPL_PLUGIN_DIR . 'includes/class-bapl-block-native.php';
    require_once BAPL_PLUGIN_DIR . 'includes/class-bapl-loader.php';

    // Initialize loader
    $loader = new BAPL_Loader();
    $loader->init();
}
