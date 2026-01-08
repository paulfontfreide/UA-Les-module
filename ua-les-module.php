<?php
/**
 * Plugin Name: UA Les Module
 * Plugin URI: https://github.com/paulfontfreide/UA-Les-module
 * Description: Modern membership & offline seed management for Uno Animo les planning
 * Version: 2.2.0
 * Author: Uno Animo (Paul Font Freide)
 * Author URI: https://unoanimo.nl
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ua-les-module
 * Domain Path: /languages
 * Requires at least: 6.1
 * Requires PHP: 7.4
 *
 * @package UA_Les_Module
 * @version 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'UA_LM_VERSION', '2.2.0' );
define( 'UA_LM_FILE', __FILE__ );
define( 'UA_LM_DIR', plugin_dir_path( __FILE__ ) );
define( 'UA_LM_URL', plugin_dir_url( __FILE__ ) );
define( 'UA_LM_BASENAME', plugin_basename( __FILE__ ) );

// Load autoloader
require_once UA_LM_DIR . 'includes/class-autoloader.php';
UA_Les_Module\Autoloader::register();

// Initialize plugin
add_action( 'plugins_loaded', array( 'UA_Les_Module\Plugin', 'init' ) );

// Activation hook
register_activation_hook( UA_LM_FILE, array( 'UA_Les_Module\Installer', 'activate' ) );

// Deactivation hook
register_deactivation_hook( UA_LM_FILE, array( 'UA_Les_Module\Installer', 'deactivate' ) );