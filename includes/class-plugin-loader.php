<?php
/**
 * The core plugin class
 *
 * @since 1.0.0
 */

class Premium_Academy_Loader {

    /**
     * The unique identifier of this plugin.
     *
     * @since 1.0.0
     * @access protected
     * @var string $plugin_name
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since 1.0.0
     * @access protected
     * @var string $version
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * @since 1.0.0
     */
    public function __construct() {
        if ( defined( 'PREMIUM_ACADEMY_VERSION' ) ) {
            $this->version = PREMIUM_ACADEMY_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'premium-academy';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since 1.0.0
     * @access private
     */
    private function load_dependencies() {
        require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-security-manager.php';
        require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-validation.php';
        require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-sanitization.php';
        require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-database.php';
        require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-encryption.php';
        require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-two-factor-auth.php';
        require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-audit-logger.php';
        require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/functions.php';
    }

    /**
     * Define internationalization functionality.
     *
     * @since 1.0.0
     * @access private
     */
    private function set_locale() {
        load_plugin_textdomain(
            'premium-academy',
            false,
            dirname( PREMIUM_ACADEMY_PLUGIN_BASENAME ) . '/languages/'
        );
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     *
     * @since 1.0.0
     * @access private
     */
    private function define_admin_hooks() {
        if ( is_admin() ) {
            add_action( 'admin_menu', 'premium_academy_add_admin_menu' );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
            add_action( 'wp_ajax_premium_academy_login', 'premium_academy_admin_login' );
            add_action( 'wp_ajax_premium_academy_verify_2fa', 'premium_academy_verify_2fa' );
        }
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     *
     * @since 1.0.0
     * @access private
     */
    private function define_public_hooks() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
        add_shortcode( 'premium_academy', 'premium_academy_display_frontend' );
        add_action( 'wp_ajax_premium_academy_submit_admission', 'premium_academy_submit_admission' );
        add_action( 'wp_ajax_premium_academy_submit_contact', 'premium_academy_submit_contact' );
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * @since 1.0.0
     */
    public function enqueue_admin_assets() {
        wp_enqueue_style( 'premium-academy-admin', PREMIUM_ACADEMY_PLUGIN_URL . 'assets/css/admin-style.css', array(), PREMIUM_ACADEMY_VERSION );
        wp_enqueue_script( 'premium-academy-admin', PREMIUM_ACADEMY_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), PREMIUM_ACADEMY_VERSION, true );
        
        wp_localize_script( 'premium-academy-admin', 'premiumAcademyAdmin', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'premium_academy_nonce' ),
        ) );
    }

    /**
     * Enqueue public scripts and styles.
     *
     * @since 1.0.0
     */
    public function enqueue_public_assets() {
        wp_enqueue_style( 'premium-academy-main', PREMIUM_ACADEMY_PLUGIN_URL . 'assets/css/style.css', array(), PREMIUM_ACADEMY_VERSION );
        wp_enqueue_style( 'premium-academy-responsive', PREMIUM_ACADEMY_PLUGIN_URL . 'assets/css/responsive.css', array(), PREMIUM_ACADEMY_VERSION );
        wp_enqueue_script( 'premium-academy-main', PREMIUM_ACADEMY_PLUGIN_URL . 'assets/js/main.js', array( 'jquery' ), PREMIUM_ACADEMY_VERSION, true );
        
        wp_localize_script( 'premium-academy-main', 'premiumAcademy', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'premium_academy_nonce' ),
        ) );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since 1.0.0
     */
    public function run() {
        do_action( 'premium_academy_loaded' );
    }
}
