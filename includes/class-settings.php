<?php

/**
 * The settings functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Media_Restriction
 * @subpackage Media_Restriction/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The settings functionality of the plugin.
 *
 * Defines the plugin settings, admin menu, and settings page.
 *
 * @since      1.0.0
 * @package    Media_Restriction
 * @subpackage Media_Restriction/includes
 */
class Media_Restriction_Settings {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name = 'media-restriction';

	/**
	 * The options name to be used in this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $option_name    Option name of this plugin.
	 */
	private $option_name = 'media_restriction_options';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		// Nothing to initialize.
	}

	/**
	 * Add admin menu.
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'Media Access Restriction Settings', 'media-restriction' ),
			__( 'Media Restriction', 'media-restriction' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_settings_page' ),
			'dashicons-lock',
			30
		);
	}

	/**
	 * Register settings.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		register_setting(
			$this->plugin_name . '_options_group',
			$this->option_name,
			array( $this, 'sanitize_options' )
		);
	}

	/**
	 * Sanitize options.
	 *
	 * @since    1.0.0
	 * @param    array $input    The options array.
	 * @return   array           The sanitized options array.
	 */
	public function sanitize_options( $input ) {
		$sanitized_input = array();
		$defaults        = $this->get_default_options();

		// Sanitize restricted roles
		if ( isset( $input['restricted_roles'] ) && is_array( $input['restricted_roles'] ) ) {
			$sanitized_input['restricted_roles'] = array_map( 'sanitize_text_field', $input['restricted_roles'] );
		} else {
			$sanitized_input['restricted_roles'] = $defaults['restricted_roles'];
		}

		// Sanitize excluded users
		if ( isset( $input['excluded_users'] ) && is_array( $input['excluded_users'] ) ) {
			$excluded_users = array();
			foreach ( $input['excluded_users'] as $user_id ) {
				if ( ! empty( $user_id ) && ctype_digit( (string) $user_id ) ) {
					$excluded_users[] = absint( $user_id );
				}
			}
			$sanitized_input['excluded_users'] = array_unique( $excluded_users );
		} else {
			$sanitized_input['excluded_users'] = $defaults['excluded_users'];
		}

		return $sanitized_input;
	}

	/**
	 * Get the default options.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @return   array    Default options.
	 */
	private function get_default_options() {
		return array(
			'restricted_roles' => array(),
			'excluded_users'   => array(),
		);
	}

	/**
	 * Get the plugin options.
	 *
	 * Retrieves the options from the database and merges with defaults.
	 *
	 * @since    1.0.0
	 * @return   array    The plugin options.
	 */
	public function get_options() {
		$options = get_option( $this->option_name );
		return wp_parse_args( $options, $this->get_default_options() );
	}

	/**
	 * Display the settings page.
	 *
	 * @since    1.0.0
	 */
	public function display_settings_page() {
		// Get roles and users for the template.
		$user_roles  = new Media_Restriction_User_Roles();
		$roles       = $user_roles->get_roles();
		$users       = $user_roles->get_users();
		$options     = $this->get_options(); // Use the new get_options method
		$option_name = $this->option_name; // Pass option name to template

		// Include the settings page template.
		require_once MEDIA_RESTRICTION_PLUGIN_DIR . 'templates/settings-page.php';
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @since    1.0.0
	 * @param    string $hook_suffix    The current admin page.
	 */
	public function enqueue_assets( $hook_suffix ) {
		// Only load on our settings page.
		// The hook suffix for a top-level menu page is 'toplevel_page_{menu_slug}'.
		if ( 'toplevel_page_' . $this->plugin_name !== $hook_suffix ) {
			return;
		}

		// Enqueue Select2 CSS.
		wp_enqueue_style( 'select2', MEDIA_RESTRICTION_PLUGIN_URL . 'assets/css/select2.min.css', array(), '4.1.0-rc.0' );
		// Enqueue Plugin Admin CSS.
		wp_enqueue_style( $this->plugin_name . '-admin', MEDIA_RESTRICTION_PLUGIN_URL . 'assets/css/admin.css', array( 'select2' ), MEDIA_RESTRICTION_VERSION );

		// Enqueue Select2 JS.
		wp_enqueue_script( 'select2', MEDIA_RESTRICTION_PLUGIN_URL . 'assets/js/select2.min.js', array('jquery'), '4.1.0-rc.0', true );
		// Enqueue Plugin Admin JS.
		wp_enqueue_script( $this->plugin_name . '-admin', MEDIA_RESTRICTION_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery', 'select2' ), MEDIA_RESTRICTION_VERSION, true );
	}
}
