<?php
/**
 * The user roles functionality of the plugin.
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
 * The user roles functionality of the plugin.
 *
 * Handles user roles and users operations.
 *
 * @since      1.0.0
 * @package    Media_Restriction
 * @subpackage Media_Restriction/includes
 */
class Media_Restriction_User_Roles {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		// Nothing to initialize.
	}

	/**
	 * Get all available roles.
	 *
	 * @since    1.0.0
	 * @return   array    The available roles.
	 */
	public function get_roles() {
		// Ensure all roles, including custom roles, are retrieved
		return wp_roles()->get_names();
	}

	/**
	 * Get all users for exclusion list.
	 *
	 * @since    1.0.0
	 * @return   array    The users.
	 */
	public function get_users() {
		return get_users(
			array(
				'fields' => array( 'ID', 'display_name', 'user_email' ),
			)
		);
	}
}
