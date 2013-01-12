<?php
global $members;
$user = NULL;
$delete_roles = false;
if ( empty( $members ) ) {
    $user = wp_get_current_user();
} else {
    $user = new WP_User( $members->current_user->ID );
}

$avail_roles = array();
$users_of_blog = get_users_of_blog();
foreach ( (array)$users_of_blog as $blog_user ) {
	$meta_values = unserialize( $blog_user->meta_value );
	foreach ( ( array) $meta_values as $role => $value ) {
		if ( !isset( $avail_roles[$role] ) )	$avail_roles[$role] = 0;
		++$avail_roles[$role];
	}
}
unset( $users_of_blog );

if ( current_user_can( 'delete_roles' ) )	$delete_roles = true;
$default_role = get_option( 'default_role' );
$all_roles = $active_roles = $inactive_roles = 0;
foreach ( $wp_roles->role_names as $role => $name ) {
	$all_roles++;
	if ( isset( $avail_roles[$role] ) && $avail_roles[$role] ) {
		$active_roles++;
		$active_roles_arr[$role] = $name;
	}else {
		$inactive_roles++;
		$inactive_roles_arr[$role] = $name;
	}
}
if ( isset( $_GET['role_status'] ) && 'active' == $_GET['role_status'] ) {
	$roles_loop_array = $active_roles_arr;
	$title = __('Edit Active Roles', 'event-espresso');
	$current_page = admin_url( esc_url( "admin.php?page=roles&role_status=active" ) );
}elseif ( isset( $_GET['role_status'] ) && 'inactive' == $_GET['role_status'] ) {
	$roles_loop_array = $inactive_roles_arr;
	$title = __('Edit Inactive Roles', 'event-espresso');
	$current_page = admin_url( esc_url( "admin.php?page=roles&role_status=inactive" ) );
}else {
	$roles_loop_array = $wp_roles->role_names;
	$title = __('Edit Roles', 'event-espresso');
	$current_page = admin_url( esc_url( "admin.php?page=roles" ) );
}
ksort( $roles_loop_array );
?>
<div class="wrap">
	<h2><?php echo $title; ?></h2>
	<?php do_action( 'espresso_pre_edit_roles_form' );?>
	<div id="poststuff">
		<form id="roles" action="<?php echo $current_page; ?>" method="post">
			<?php wp_nonce_field( espresso_get_nonce( 'edit-roles' ) ); ?>
			<ul class="subsubsub">
				<li><a <?php if ( isset( $_GET['role_status'] ) && 'active' !== $_GET['role_status'] && 'inactive' !== $_GET['role_status'] ) echo 'class="current"'; ?> href="<?php echo admin_url( esc_url( "admin.php?page=roles" ) ); ?>"><?php _e('All', 'event-espresso'); ?> <span class="count">(<span id="all_count"><?php echo $all_roles; ?></span>)</span></a> | </li>
				<li><a <?php if ( isset( $_GET['role_status'] ) && 'active' == $_GET['role_status'] ) echo 'class="current"'; ?> href="<?php echo admin_url( esc_url( "admin.php?page=roles&amp;role_status=active" ) ); ?>"><?php _e('Active', 'event-espresso'); ?> <span class="count">(<span id="active_count"><?php echo $active_roles; ?></span>)</span></a> | </li>
				<li><a <?php if ( isset( $_GET['role_status'] ) && 'inactive' == $_GET['role_status'] ) echo 'class="current"'; ?> href="<?php echo admin_url( esc_url( "admin.php?page=roles&amp;role_status=inactive" ) ); ?>"><?php _e('Inactive', 'event-espresso'); ?> <span class="count">(<span id="inactive_count"><?php echo $inactive_roles; ?></span>)</span></a></li>
			</ul>
			<div class="tablenav">
				<div class="alignleft actions">
					<select name="action">
						<option value="" selected="selected"><?php _e('Bulk Actions', 'event-espresso'); ?></option>
						<?php if ( $delete_roles ) echo '<option value="delete">' . __('Delete', 'event-espresso') . '</option>'; ?>
					</select>
					<input type="submit" value="<?php _e('Apply', 'event-espresso'); ?>" name="doaction" id="doaction" class="button-secondary action" />
				</div>
				<br class="clear" />
			</div>
			<table class="widefat fixed" cellspacing="0">
				<thead>
					<tr>
						<th class='check-column'><input type='checkbox' /></th>
						<th class='name-column'><?php _e('Role Name', 'event-espresso'); ?></th>
						<th><?php _e('Role', 'event-espresso'); ?></th>
						<th><?php _e('Users', 'event-espresso'); ?></th>
						<th><?php _e('Capabilities', 'event-espresso'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th class='check-column'><input type='checkbox' /></th>
						<th class='name-column'><?php _e('Role Name', 'event-espresso'); ?></th>
						<th><?php _e('Role', 'event-espresso'); ?></th>
						<th><?php _e('Users', 'event-espresso'); ?></th>
						<th><?php _e('Capabilities', 'event-espresso'); ?></th>
					</tr>
				</tfoot>
				<tbody id="users" class="list:user user-list plugins">
				<?php foreach ( $roles_loop_array as $role => $name ) { ?>
					<?php $name = str_replace( '|User role', '', $name ); ?>
					<tr valign="top" class="<?php if ( $avail_roles[$role] ) echo 'active'; else echo 'inactive'; ?>">
						<th class="manage-column column-cb check-column">
							<?php if ( $role !== $default_role && !$user->has_cap( $role ) ) { ?>
								<input name="roles[<?php echo $role; ?>]" id="<?php echo $role; ?>" type="checkbox" value="<?php echo $role; ?>" />
							<?php } ?>
						</th>
						<td class='plugin-title'>
							<?php $edit_link = admin_url( wp_nonce_url( "admin.php?page=roles&amp;action=edit&amp;role={$role}", espresso_get_nonce( 'edit-roles' ) ) ); ?> 
							<a href="<?php echo $edit_link; ?>" title="<?php printf( __('Edit the %1$s role', 'event-espresso'), $name ); ?>"><strong><?php echo $name; ?></strong></a>
							<div class="row-actions">
								<a href="<?php echo $edit_link; ?>" title="<?php printf( __('Edit the %1$s role', 'event-espresso'), $name ); ?>"><?php _e('Edit', 'event-espresso'); ?></a> 
								<?php
								if ( $delete_roles && $role !== $default_role && !$user->has_cap( $role ) ) {
									$delete_link = admin_url( wp_nonce_url( "admin.php?page=roles&amp;action=delete&amp;role={$role}", espresso_get_nonce( 'edit-roles' ) ) ); ?>
									| <a href="<?php echo $delete_link; ?>" title="<?php printf( __('Delete the %1$s role', 'event-espresso'), $name ); ?>"><?php _e('Delete', 'event-espresso'); ?></a>
								<?php }
								if ( $role == $default_role ) { ?>
									| <a href="<?php echo admin_url( ( 'options-general.php' ) ); ?>" title="<?php _e('Change default role', 'event-espresso'); ?>"><?php _e('Default Role', 'event-espresso'); ?></a> 
								<?php }
								if ( isset( $avail_roles[$role] ) && $avail_roles[$role] ) { ?>
									| <a href="<?php echo admin_url( esc_url( "users.php?role={$role}" ) ); ?>" title="<?php printf( __('View all users with the %1$s role', 'event-espresso'), $name ); ?>"><?php _e('View Users', 'event-espresso'); ?></a> 
								<?php } ?>
							</div>
						</td>
						<td class='desc'>
							<p><?php echo $role; ?></p>
						</td>
						<td class='desc'>
							<p><?php
							if ( isset( $avail_roles[$role] ) && 1 < $avail_roles[$role] )
								echo '<a href="' . admin_url( esc_url( "users.php?role={$role}" ) ) . '" title="' . sprintf( __('View all users with the %1$s role', 'event-espresso'), $name ) . '">' . sprintf( __('%1$s Users', 'event-espresso'), $avail_roles[$role] ) . '</a>'; 
							elseif ( isset( $avail_roles[$role] ) && 1 == $avail_roles[$role] )
								echo '<a href="' . admin_url( esc_url( "users.php?role={$role}" ) ) . '" title="' . sprintf( __('View all users with the %1$s role', 'event-espresso'), $name ) . '">' . __('1 User', 'event-espresso') . '</a>'; 
							else
								echo '<em>' . __('No users have this role.', 'event-espresso') . '</em>';
							?></p>
						</td>
						<td class='desc'>
							<p>
							<?php
							$role_2 = get_role( $role );
							if ( is_array( $role_2->capabilities ) ) {
								$cap_count = count( $role_2->capabilities ); 
								if ( 1 < $cap_count ) printf( __('%1$s Capabilities', 'event-espresso'), $cap_count );
								elseif ( 1 == $cap_count ) _e('1 Capability', 'event-espresso');
							}
							else
								echo '<em>' . __('This role has no capabilities', 'event-espresso') . '</em>'; ?>
							</p>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			<div class="tablenav">
				<div class="alignleft actions">
					<select name="action2">
						<option value="" selected="selected"><?php _e('Bulk Actions', 'event-espresso'); ?></option>
						<?php if ( $delete_roles ) echo '<option value="delete">' . __('Delete', 'event-espresso') . '</option>'; ?>
					</select>
					<input type="submit" value="<?php _e('Apply', 'event-espresso'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
				</div>
				<br class="clear" />
			</div>
		</form>
	</div>
</div>