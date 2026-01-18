<?php
/**
 * Loader for BuddyActivist Passwordless Registration and Login.
 *
 * Initializes all plugin components and enqueues frontend assets.
 *
 * @package BuddyActivist_Passwordless
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BAPL_Loader {

    /**
     * Class instances.
     *
     * @var BAPL_Registration
     * @var BAPL_Login
     * @var BAPL_Block_Native
     */
    protected $registration;
    protected $login;
    protected $block_native;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->registration = new BAPL_Registration();
        $this->login        = new BAPL_Login();
        $this->block_native = new BAPL_Block_Native();
    }

    /**
     * Initialize plugin components.
     */
    public function init() {

        // Initialize registration flow
        $this->registration->init();

        // Initialize login flow
        $this->login->init();

        // Block all native WP/BP/BB auth flows
        $this->block_native->init();

        // Enqueue frontend assets
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    /**
     * Enqueue plugin CSS and JS.
     */
    public function enqueue_assets() {

        wp_enqueue_style(
            'bapl-frontend',
            BAPL_PLUGIN_URL . 'assets/css/bapl-frontend.css',
            [],
            BAPL_VERSION
        );

        wp_enqueue_script(
            'bapl-frontend',
            BAPL_PLUGIN_URL . 'assets/js/bapl-frontend.js',
            [ 'jquery' ],
            BAPL_VERSION,
            true
        );
    }
}
