<?php
/**
 * Plugin Name: Media Restriction
 * Plugin URI: https://github.com/upluggit/media-restriction
 * Description: Restrict media library access based on user roles
 * Version: 1.0.1
 * Author: upluggit 
 * Author URI: https://github.com/upluggit
 * Text Domain: media-restriction
 * Domain Path: /languages
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package Media_Restriction
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'MEDIA_RESTRICTION_VERSION', '1.0.0' );
define( 'MEDIA_RESTRICTION_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MEDIA_RESTRICTION_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MEDIA_RESTRICTION_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_media_restriction() {
	// Activation tasks if needed.
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_media_restriction() {
	// Deactivation tasks if needed.
}

register_activation_hook( __FILE__, 'activate_media_restriction' );
register_deactivation_hook( __FILE__, 'deactivate_media_restriction' );

/**
 * Load plugin textdomain.
 */
function media_restriction_load_textdomain() {
	load_plugin_textdomain( 'media-restriction', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'media_restriction_load_textdomain' );

/**
 * Begins execution of the plugin.
 */
function run_media_restriction() {
	// Include required files.
	require_once MEDIA_RESTRICTION_PLUGIN_DIR . 'includes/class-media-restriction.php';
	require_once MEDIA_RESTRICTION_PLUGIN_DIR . 'includes/class-settings.php';
	require_once MEDIA_RESTRICTION_PLUGIN_DIR . 'includes/class-media-filter.php';
	require_once MEDIA_RESTRICTION_PLUGIN_DIR . 'includes/class-user-roles.php';

	// Initialize the main plugin class.
	$plugin = new Media_Restriction();
	$plugin->run();
}
run_media_restriction();
