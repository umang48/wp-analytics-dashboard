<?php
/**
 * Plugin Name: Privacy-First Analytics Dashboard
 * Plugin URI:  https://phptutorialpoints.in/
 * Description: A privacy-first, self-hosted analytics dashboard for WordPress. Stores data locally (DB or Text) and displays it with a beautiful React dashboard.
 * Version:     1.0.0
 * Author:      umangapps48
 * Author URI:  https://profiles.wordpress.org/umangapps48/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wp-analytics-dashboard
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define plugin constants.
define( 'WAD_VERSION', '1.0.0' );
define( 'WAD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WAD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Autoloader or require core files.
require_once WAD_PLUGIN_DIR . 'includes/class-wad-activator.php';
require_once WAD_PLUGIN_DIR . 'includes/class-wad-deactivator.php';
require_once WAD_PLUGIN_DIR . 'includes/class-wad-core.php';

// Activation/Deactivation hooks.
function activate_wp_analytics_dashboard() {
	WAD_Activator::activate();
}

function deactivate_wp_analytics_dashboard() {
	WAD_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_analytics_dashboard' );
register_deactivation_hook( __FILE__, 'deactivate_wp_analytics_dashboard' );

// Run the plugin.
function run_wp_analytics_dashboard() {
	$plugin = new WAD_Core();
	$plugin->run();
}

run_wp_analytics_dashboard();
