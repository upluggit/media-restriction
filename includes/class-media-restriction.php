<?php
/**
 * The core plugin class.
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
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since      1.0.0
 * @package    Media_Restriction
 * @subpackage Media_Restriction/includes
 */
class Media_Restriction {

	/**
	 * The settings instance.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Media_Restriction_Settings    $settings    Handles all settings operations.
	 */
	protected $settings;

	/**
	 * The media filter instance.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Media_Restriction_Filter    $media_filter    Handles media filtering operations.
	 */
	protected $media_filter;

	/**
	 * The user roles instance.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Media_Restriction_User_Roles    $user_roles    Handles user roles operations.
	 */
	protected $user_roles;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->settings     = new Media_Restriction_Settings();
		$this->media_filter = new Media_Restriction_Filter();
		$this->user_roles   = new Media_Restriction_User_Roles();
	}

	/**
	 * Run the plugin.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->define_admin_hooks();
		$this->define_filter_hooks();
	}

	/**
	 * Register all of the hooks related to the admin area functionality.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		// Admin menu and settings.
		add_action( 'admin_menu', array( $this->settings, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this->settings, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this->settings, 'enqueue_assets' ) );
	}

	/**
	 * Register all of the hooks related to media filtering.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_filter_hooks() {
		// Filter for grid view (AJAX) - set high priority
		add_filter( 'ajax_query_attachments_args', array( $this->media_filter, 'filter_media_library' ) );

		// Filter for list view - set high priority
		add_action( 'pre_get_posts', array( $this->media_filter, 'filter_media_list_view' ) );
	}
}
