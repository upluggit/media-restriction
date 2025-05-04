<?php
/**
 * Template for the settings page.
 *
 * @since      1.0.0
 * @package    Media_Restriction
 * @subpackage Media_Restriction/templates
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap media-restriction">
	<h1><?php echo esc_html__( 'Media Access Restriction Settings', 'media-restriction' ); ?></h1>
	<p class="description"><?php echo esc_html__( 'Manage who can see what in the Media Library.', 'media-restriction' ); ?></p>
	
	<form method="post" action="options.php">
		<?php settings_fields( 'media-restriction_options_group' ); ?>
		
		<div class="media-restriction-card">
			<h2 class="media-restriction-section-title">1. <?php echo esc_html__( 'Restrict Media Access by Role', 'media-restriction' ); ?></h2>
			<p class="description"><?php echo esc_html__( 'Define which user roles should be restricted to viewing only their own media files.', 'media-restriction' ); ?></p>
			
			<div class="media-restriction-role-checkboxes">
				<?php foreach ( $roles as $role_key => $role_name ) : ?>
					<?php
					// Skip administrator role.
					if ( 'administrator' === $role_key ) {
						continue;
					}
					?>
					<div class="media-restriction-role-item">
						<label for="role-<?php echo esc_attr( $role_key ); ?>" class="media-restriction-checkbox-label">
							<input type="checkbox"
								id="role-<?php echo esc_attr( $role_key ); ?>"
								name="<?php echo esc_attr( $option_name ); ?>[restricted_roles][]"
								value="<?php echo esc_attr( $role_key ); ?>"
								<?php checked( in_array( $role_key, $options['restricted_roles'] ) ); ?>>
							<?php echo esc_html( $role_name ); ?>
						</label>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		
		<div class="media-restriction-card">
			<h2 class="media-restriction-section-title">2. <?php echo esc_html__( 'Exclude Specific Users (Optional)', 'media-restriction' ); ?></h2>
			<p class="description"><?php echo esc_html__( 'Choose individual users who should be excluded from the restriction, even if they belong to a restricted role.', 'media-restriction' ); ?></p>

			<div class="media-restriction-user-select-container">
				<select name="<?php echo esc_attr( $option_name ); ?>[excluded_users][]" multiple="multiple" class="media-restriction-multiselect" id="excluded-users">
					<?php foreach ( $users as $user ) : ?>
						<?php
							$current_user_id = (int) $user->ID;
						?>
						<option value="<?php echo esc_attr( $user->ID ); ?>"
								<?php selected( in_array( $current_user_id, $options['excluded_users'] ) ); ?>>
							<?php echo esc_html( $user->display_name ); ?> (<?php echo esc_html( $user->user_email ); ?>)
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		
		<?php submit_button( __( 'Save Changes', 'media-restriction' ) ); ?>
	</form>

	<!-- Toast Notification -->
	<div id="success-toast" class="success-toast" style="display: none;">
		<?php echo esc_html__( 'Settings saved successfully', 'media-restriction' ); ?>
	</div>
</div>