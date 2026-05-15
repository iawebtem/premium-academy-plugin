<?php
/**
 * Plugin Name: Premium Academy
 * Plugin URI: https://premiumacademy.edu.gh
 * Description: A secure, advanced WordPress plugin for Premium Academy with student admissions, staff management, and secure admin panel.
 * Version: 1.0.0
 * Author: Premium Academy Development Team
 * Author URI: https://premiumacademy.edu.gh
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: premium-academy
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'PREMIUM_ACADEMY_VERSION', '1.0.0' );
define( 'PREMIUM_ACADEMY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PREMIUM_ACADEMY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PREMIUM_ACADEMY_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Require autoloader
require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-plugin-loader.php';

/**
 * The code that runs during plugin activation
 */
function activate_premium_academy() {
    require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-plugin-activator.php';
    Premium_Academy_Activator::activate();
}

/**
 * The code that runs during plugin deactivation
 */
function deactivate_premium_academy() {
    require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-plugin-deactivator.php';
    Premium_Academy_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_premium_academy' );
register_deactivation_hook( __FILE__, 'deactivate_premium_academy' );

/**
 * Begins execution of the plugin
 */
function run_premium_academy() {
    $plugin = new Premium_Academy_Loader();
    $plugin->run();
}

run_premium_academy();
