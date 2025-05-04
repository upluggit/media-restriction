<?php
/**
 * The media filtering functionality of the plugin.
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
 * The media filtering functionality of the plugin.
 *
 * Handles the filtering of media library based on user roles and exclusions.
 *
 * @since      1.0.0
 * @package    Media_Restriction
 * @subpackage Media_Restriction/includes
 */
class Media_Restriction_Filter {

	/**
	 * The settings instance.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Media_Restriction_Settings    $settings    Handles all settings operations.
	 */
	private $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->settings = new Media_Restriction_Settings();
		add_action('admin_init', array($this, 'setup_filters'));
	}

	public function setup_filters() {
		// Add our filters with high priority
		add_filter('ajax_query_attachments_args', array($this, 'filter_media_library'));
		add_action('pre_get_posts', array($this, 'filter_media_list_view'));
		add_filter('posts_where', array($this, 'enforce_media_restriction_where'), 10, 2);
	}

	/**
	 * Check if the current user should be restricted.
	 *
	 * @since    1.0.0
	 * @return   bool    True if the user should be restricted, false otherwise.
	 */
	private function should_restrict_user() {
		// Skip if user is admin or has the capability to manage options
		if (current_user_can('manage_options')) {
			return false;
		}

		// Get current user
		$current_user = wp_get_current_user();
		$options = $this->settings->get_options();

		// Check if user's role is restricted
		$is_restricted = false;
		foreach ($current_user->roles as $role) {
			if (in_array($role, $options['restricted_roles'])) {
				$is_restricted = true;
				break;
			}
		}

		// If user is in excluded list, don't restrict
		if ($is_restricted && in_array($current_user->ID, $options['excluded_users'])) {
			$is_restricted = false;
		}

		return $is_restricted;
	}

	public function filter_media_library($query) {
		if ($this->should_restrict_user()) {
			// Force our author restriction regardless of other plugins
			$query['author'] = get_current_user_id();
			$query['post_type'] = 'attachment';
			$query['post_status'] = 'inherit';
			$query['posts_per_page'] = 80;
			$query['paged'] = isset($query['paged']) ? $query['paged'] : 1;
			$query['orderby'] = 'date';
			$query['order'] = 'DESC';
		}
		return $query;
	}

	public function filter_media_list_view($query) {
		global $pagenow;
		
		if (is_admin() && 'upload.php' === $pagenow && $query->is_main_query()) {
			if ($this->should_restrict_user()) {
				$query->set('author', get_current_user_id());
				$query->set('suppress_filters', true);
			}
		}
	}

	public function enforce_media_restriction_where($where) {
		if (
			(isset($_REQUEST['action']) && $_REQUEST['action'] === 'query-attachments') ||
			(is_admin() && isset($GLOBALS['pagenow']) && 'upload.php' === $GLOBALS['pagenow'])
			) {
				if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'query-attachments') {
					$nonce = isset($_REQUEST['_ajax_nonce']) ? wp_unslash(sanitize_text_field($_REQUEST['_ajax_nonce'])) : '';
					
					if (empty($nonce) || !wp_verify_nonce($nonce, 'query-attachments')) {
						return $where;
					}
				}

				global $wpdb;
				$current_user_id = get_current_user_id();

				$regex = "/\s+AND\s+(?:{$wpdb->posts}\.)?post_author\s*(?:=|IN)\s*\(?\d+(?:,\s*\d+)*\)?/i";
				$where = preg_replace( $regex, '', $where );

				if ( $this->should_restrict_user() ) {
					$where .= $wpdb->prepare( " AND {$wpdb->posts}.post_author = %d", $current_user_id );
				}
		}
		return $where;
	}
}
