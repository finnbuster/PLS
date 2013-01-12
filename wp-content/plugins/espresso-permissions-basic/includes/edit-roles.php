<?php
$role = get_role( $role );
$role_updated = false;
if ( isset( $_POST['edit-role-saved'] ) && 'Y' == $_POST['edit-role-saved'] ) {
	check_admin_referer( espresso_get_nonce( 'edit-roles' ) );
	$role_updated = true;
	foreach ( espresso_get_capabilities() as $cap ) {
        $posted_cap = false;
        if ( isset( $_POST['role-caps'] ) && isset( $_POST['role-caps']["{$role->name}-{$cap}"] ))
            $posted_cap = $_POST['role-caps']["{$role->name}-{$cap}"];
		if ( !$role->has_cap( $cap ) && $posted_cap )	$role->add_cap( $cap );
		elseif ( $role->has_cap( $cap ) && !$posted_cap )	$role->remove_cap( $cap );
	}
	if ( !empty( $_POST['new-cap'] ) && is_array( $_POST['new-cap'] ) ) {
		foreach ( $_POST['new-cap'] as $new_cap ) {
			$new_cap = strip_tags( $new_cap );
			$new_cap = str_replace( array( '-', ' ', '&nbsp;' ) , '_', $new_cap );
			$new_cap = preg_replace('/[^A-Za-z0-9_]/', '', $new_cap );
			$new_cap = strtolower( $new_cap );
			if ( $new_cap && !$role->has_cap( $new_cap ) )	$role->add_cap( $new_cap );
		}
	}
}
?>
<div class="wrap">
	<h2><?php printf(__('Edit the %1$s role', 'event-espresso'), $role->name ); ?></h2>
	<?php if ( $role_updated ) espresso_admin_message( '', __('Role updated.', 'event-espresso') ); ?>
	<?php do_action( 'espresso_pre_edit_role_form' ); //Available pre-form hook for displaying messages. ?>
	<div id="poststuff">
		<form name="form0" method="post" action="<?php echo admin_url( esc_url( "admin.php?page=roles&amp;action=edit&amp;role={$role->name}" ) ); ?>" style="border:none;background:transparent;">
			<?php wp_nonce_field( espresso_get_nonce( 'edit-roles' ) ); ?>
			<div class="postbox open">
				<h3><?php printf( __('<strong>Role:</strong> %1$s', 'event-espresso'), $role->name ); ?></h3>
				<div class="inside">
					<table class="form-table">
					<tr>
						<th style="width: 20%;">
							<strong><?php _e('Capabilities', 'event-espresso'); ?></strong>
						</th>
						<td>
							<?php _e('Select which capabilities this role should have. Make sure you understand what the capability does before giving it to just any role. This is a powerful feature, but it can cause you some grief if you give regular ol\' Joe more capabilities than yourself.', 'event-espresso'); ?>
							<br /><br />
						<?php
							foreach ( espresso_get_capabilities() as $cap ) {
								if ( $role->has_cap( $cap ) )	$checked = " checked='checked' ";
								else	$checked = '';
?>
								<div style='overflow: hidden; margin: 0 0 5px 0; float:left; width: 32.67%;'>
								<input name='<?php echo "role-caps[{$role->name}-{$cap}]"; ?>' id='<?php echo "{$role->name}-{$cap}"; ?>' <?php echo $checked; ?> type='checkbox' value='true' /> 
								<label for="<?php echo "{$role->name}-{$cap}"; ?>"><?php if ( $checked ) echo "<strong>$cap</strong>"; else echo "<em>$cap</em>"; ?></label>
								</div>
							<?php } // Endforeach ?>
						</td>
					</tr>
					<tr>
						<th style="width: 20%;">
							<strong><?php _e('New Capabilities', 'event-espresso'); ?></strong>
						</th>
						<td>
							<?php _e('Add up to six new capabilities with this form for this role (more can be added later). Please only use letters, numbers, and underscores.', 'event-espresso'); ?>
							<br /><br />
							<p style="margin: 0 0 5px 0; float:left; width: 32.67%;">
								<input id="new-cap-1" name="new-cap[]" value="" size="20" /> 
							</p>
							<p style="margin: 0 0 5px 0; float:left; width: 32.67%;">
								<input id="new-cap-2" name="new-cap[]" value="" size="20" /> 
							</p>
							<p style="margin: 0 0 5px 0; float:left; width: 32.67%;">
								<input id="new-cap-3" name="new-cap[]" value="" size="20" /> 
							</p>
							<p style="margin: 0 0 5px 0; float:left; width: 32.67%;">
								<input id="new-cap-4" name="new-cap[]" value="" size="20" /> 
							</p>
							<p style="margin: 0 0 5px 0; float:left; width: 32.67%;">
								<input id="new-cap-5" name="new-cap[]" value="" size="20" /> 
							</p>
							<p style="margin: 0 0 5px 0; float:left; width: 32.67%;">
								<input id="new-cap-6" name="new-cap[]" value="" size="20" /> 
							</p>
						</td>
					</tr>
					</table>
				</div>
			</div>
			<p class="submit" style="clear:both;">
				<input type="submit" name="Submit"  class="button-primary" value="<?php _e('Update Role', 'event-espresso') ?>" />
				<input type="hidden" name="edit-role-saved" value="Y" />
			</p>
		</form>
	</div>
</div>