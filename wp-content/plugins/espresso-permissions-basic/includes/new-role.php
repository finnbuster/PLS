<?php
if ( $_POST['new-role-submit'] == 'Y' ) {
	check_admin_referer( espresso_get_nonce( 'new-role' ) );
	if ( !empty( $_POST['capabilities'] ) && is_array( $_POST['capabilities'] ) )	$new_user_caps = $_POST['capabilities'];
	else	$new_user_caps = null;
	if ( $_POST['role-name'] && $_POST['role-id'] ) {
		$new_role = strip_tags( $_POST['role-id'] );
		$new_role = str_replace( array( '-', ' ', '&nbsp;' ) , '_', $new_role );
		$new_role = preg_replace('/[^A-Za-z0-9_]/', '', $new_role );
		$new_role = strtolower( $new_role );
		$new_role_name = strip_tags( $_POST['role-name'] );
		$new_role_added = add_role( $new_role, $new_role_name, $new_user_caps );
	}
}
?>
<div class="wrap">
	<h2><?php _e('Add a new user role', 'event_espresso'); ?></h2>
	<?php if ( $new_role_added ) espresso_admin_message( '', sprintf( __('The %1$s role has been created.', 'event_espresso'), $_POST['role-name'] ) ); ?>
	<?php do_action( 'espresso_pre_new_role_form' ); // Available action hook for displaying messages. ?>
	<div id="poststuff">
		<form name="form0" method="post" action="<?php echo admin_url( "admin.php?page=roles&amp;action=new" ); ?>" style="border:none;background:transparent;">
			<?php wp_nonce_field( espresso_get_nonce( 'new-role' ) ); ?>
			<div class="postbox open">
				<h3><?php _e('Create a new user role', 'event_espresso'); ?></h3>
				<div class="inside">
					<table class="form-table">
					<tr>
						<td colspan=2>
							<?php printf( __('Here you can create as many new roles as you\'d like.  Roles are a way of grouping your users.  You can give individual users a role from the <a href="%1$s" title="Manage Users">user management</a> screen.  This will allow you to do specific things for users with a specific role.  Once you\'ve created a new role, you can manage it with the <em>Edit Roles</em> component.', 'event_espresso'), admin_url( 'users.php' ) ); ?>						
						</td>
					</tr>
					<tr>
						<th style="width: 20%;">
							<label for="role-id"><strong><?php _e('Role:', 'event_espresso'); ?></strong></label>
						</th>
						<td>
							<?php _e('<strong>Required:</strong> Enter the name of your role.  This is a unique key that should only contain numbers, letters, and underscores.  Please don\'t add spaces or other odd characters.', 'event_espresso'); ?>
							<br />
							<input id="role-id" name="role-id" value="" size="30" />
						</td>
					</tr>

					<tr>
						<th style="width: 20%;">
							<label for="role-name"><strong><?php _e('Role Label:', 'event_espresso'); ?></strong></label>
						</th>
						<td>
							<?php _e('<strong>Required:</strong> Enter a label your role.  This will be the title that is displayed in most cases.', 'event_espresso'); ?>
							<br />
							<input id="role-name" name="role-name" value="" size="30" />
						</td>
					</tr>

					<tr>
						<th style="width: 20%;">
							<strong><?php _e('Capabilities:', 'event_espresso'); ?></strong>
						</th>
						<td>
							<?php _e('<strong>Optional:</strong> Select which capabilities your new role should have.  These may be changed later using the <em>Edit Roles</em> component.', 'event_espresso'); ?>
							<br /><br />
							<?php foreach ( espresso_get_capabilities() as $cap ) : ?>
								<div style="float: left; width: 32.67%; margin: 0 0 5px 0;">
									<input name='capabilities[<?php echo $cap; ?>]' id='capabilities-<?php echo $cap; ?>' type="checkbox" value='<?php echo $cap; ?>' <?php if ( in_array( $cap, espresso_new_role_default_capabilities() ) ) echo "checked='checked'"; ?> /> 
									<label for="capabilities-<?php echo $cap; ?>"><?php if ( in_array( $cap, espresso_new_role_default_capabilities() ) ) echo "<strong>$cap</strong>"; else echo $cap; ?></label>
								</div>
							<?php endforeach; ?>
						</td>
					</tr>
					</table>
				</div>
			</div>
			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="<?php _e('Create Role', 'event_espresso') ?>" />
				<input type="hidden" name="new-role-submit" value="Y" />
			</p>
		</form>
	</div>
</div>