<?php
add_filter( 'espresso_get_capabilities', 'espresso_remove_old_levels' );
add_action( 'espresso_pre_components_form', 'espresso_message_no_edit_roles' );
add_action( 'espresso_pre_edit_role_form', 'espresso_message_no_edit_roles' );
add_action( 'espresso_pre_edit_roles_form', 'espresso_message_no_edit_roles' );
add_action( 'espresso_pre_components_form', 'espresso_message_no_create_roles' );
add_action( 'espresso_pre_new_role_form', 'espresso_message_no_create_roles' );

function espresso_get_capabilities() {
	$capabilities = array();
	$default_caps = espresso_get_default_capabilities();
	$role_caps = espresso_get_role_capabilities();
	$plugin_caps = espresso_get_additional_capabilities();
	$capabilities = array_merge( $default_caps, $role_caps, $plugin_caps );
	$capabilities = apply_filters( 'espresso_get_capabilities', $capabilities );
	sort( $capabilities );
	return array_unique( $capabilities );
}
function espresso_get_role_capabilities() {
	global $wp_roles;
	$capabilities = array();
	foreach ( $wp_roles->role_objects as $key => $role ) {
		if ( is_array( $role->capabilities ) ) {
			foreach ( $role->capabilities as $cap => $grant ){
				$capabilities[$cap] = $cap;
			}
		}
	}
	return $capabilities;
}
function espresso_get_additional_capabilities() {
	$capabilities = array(
		'create_roles',	// Ability to create new roles
		'delete_roles',	// Ability to delete roles
		'edit_roles',	// Ability to edit a role's caps
		'restrict_content'	// Ability to restrict content (content permissions component)
	);
	return $capabilities;
}
function espresso_get_default_capabilities() {
	$defaults = array(
		'activate_plugins',
		'create_users',
		'delete_others_pages',
		'delete_others_posts',
		'delete_pages',
		'delete_plugins',
		'delete_posts',
		'delete_private_pages',
		'delete_private_posts',
		'delete_published_pages',
		'delete_published_posts',
		'delete_users',
		'edit_dashboard',
		'edit_files',
		'edit_others_pages',
		'edit_others_posts',
		'edit_pages',
		'edit_plugins',
		'edit_posts',
		'edit_private_pages',
		'edit_private_posts',
		'edit_published_pages',
		'edit_published_posts',
		'edit_themes',
		'edit_users',
		'import',
		'install_plugins',
		'install_themes',
		'manage_categories',
		'manage_links',
		'manage_options',
		'moderate_comments',
		'publish_pages',
		'publish_posts',
		'read',
		'read_private_pages',
		'read_private_posts',
		'switch_themes',
		'unfiltered_html',
		'unfiltered_upload',
		'update_plugins',
		'update_themes',
		'upload_files'
	);
	return $defaults;
}
function espresso_user_cap( $cap = '' ) {
	if ( !$cap )	return false;
	$caps = espresso_get_role_capabilities();
	if ( in_array( $cap, $caps ) )	return true;
	return false;
}
function espresso_get_old_levels() {
	return array( 'level_0', 'level_1', 'level_2', 'level_3', 'level_4', 'level_5', 'level_6', 'level_7', 'level_8', 'level_9', 'level_10' );
}
function espresso_remove_old_levels( $capabilities ) {
	return array_diff( $capabilities, espresso_get_old_levels() );
}
function espresso_admin_message( $class = '', $message = '' ) {
	if( empty($message ) ) return ;
	if ( !$class )	$class = 'updated fade below-h2';
	echo "<div class='{$class}' style='padding: 5px 10px;'>";
	echo $message;
	echo '</div>';
}
function espresso_get_nonce( $action = '' ) {
	if ( $action )
		return "espresso-permissions-action_{$action}";
	else
		return "espresso-permissions";
}

function espresso_login_form() {
	echo espresso_get_login_form();
}
function espresso_get_login_form() {
	global $user_identity, $user_ID;

	if ( is_user_logged_in() ) {

		$login = '<div class="login-form">';
			$login .= '<p><strong>' . sprintf( __('Welcome, %1$s!', 'event_espresso'), $user_identity ) . '</strong></p>';
		$login .= '</div>';
	}
	else {

		$login = '<div class="log-in login-form">';

			$login .= '<form class="log-in" action="' . get_bloginfo( 'wpurl' ) . '/wp-login.php" method="post">';

				$login .= '<p class="text-input">';
					$login .= '<label class="text" for="log">' . __('Username:', 'event_espresso') . '</label>';
					$login .= '<input class="field" type="text" name="log" id="log" value="' . esc_attr( $user_login ) . '" size="23" />';
				$login .= '</p>';

				$login .= '<p class="text-input">';
					$login .= '<label class="text" for="pwd">' . __('Password:', 'event_espresso') . '</label>';
					$login .= '<input class="field" type="password" name="pwd" id="pwd" size="23" />';
				$login .= '</p>';

				$login .= '<div class="clear">';
					$login .= '<input type="submit" name="submit" value="' . __('Log In', 'event_espresso') . '" class="log-in" />';
					$login .= '<label class="remember"><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> ' . __('Remember me', 'event_espresso') . '</label>';
					$login .= '<input type="hidden" name="redirect_to" value="' . $_SERVER['REQUEST_URI'] . '"/>';
				$login .= '</div>';

			$login .= '</form>';

		$login .= '</div>';
	}

	return $login;
}
function espresso_list_users( $args = array() ) {
	global $wpdb;
	$defaults = array(
		'order' => 'ASC',
		'orderby' => 'display_name',
		'include' => '',
		'exclude' => '',
		//'role' => '',
		'limit' => '',
		//'optioncount' => false,
		'show_fullname' => true,
		//'exclude_empty' => false,
		//'exclude_admin' => true,
		'echo' => true,
	);
	$r = wp_parse_args( $args, $defaults );
	$r = apply_filters( 'espresso_list_users_args', $r );
	extract( $r, EXTR_SKIP );
	$query = "SELECT * FROM $wpdb->users";
	$query_where = array();
	if ( is_array( $include ) )	$include = join( ',', $include );
	$include = preg_replace( '/[^0-9,]/', '', $include ); // (int)
	if ( $include )	$query_where[] = "ID IN ($include)";
	if ( is_array($exclude) )	$exclude = join( ',', $exclude );
	$exclude = preg_replace( '/[^0-9,]/', '', $exclude ); // (int)
	if ( $exclude )	$query_where[] = "ID NOT IN ($exclude)";
	if ( $query_where )	$query .= " WHERE " . join( ' AND', $query_where );
	$query .= " ORDER BY $orderby $order";
	if ( '' != $limit ) {
		$limit = absint( $limit );
		$query .= ' LIMIT ' . $limit;
	}
	$users = $wpdb->get_results( $query );
	$output = '';
	if ( !empty( $users ) ) {
		foreach ( (array) $users as $user ) {
			$user->ID = (int) $user->ID;
			$author = get_userdata( $user->ID );
			$name = $author->display_name;
			if ( $show_fullname && ($author->first_name != '' && $author->last_name != '') )	$name = "$author->first_name $author->last_name";
			$class = "user-{$user->ID}";
			if ( is_author( $user->ID ) )	$class .= ' current-user';
			if ( $hide_empty )
				$output .= "<li class='$class'>$name</li>\n";
			else
				$output .= "<li class='$class'><a href='" . get_author_posts_url( $author->ID, $author->user_nicename ) . "' title='" . sprintf(__("Posts by %s"), esc_attr( $author->display_name ) ) . "'>$name</a></li>\n";
		}
	}
	$output = apply_filters( 'espresso_list_users', $output );
	if ( !$echo )	return $output;
	echo $output;
}
function espresso_message_role_deleted() {
	$message = __('Role deleted.', 'event_espresso');
	espresso_members_admin_message( '', $message );
}
function espresso_message_roles_deleted() {
	$message = __('Selected roles deleted.', 'event_espresso');
	espresso_members_admin_message( '', $message );
}
function espresso_message_no_edit_roles() {
	if ( !espresso_user_cap( 'edit_roles' ) ) {
		$message = __('No role currently has the <code>edit_roles</code> capability.  Please add this to each role that should be able to manage/edit roles. If you do not change this, any user that has the <code>edit_users</code> capability will be able to edit roles.', 'event_espresso');
		espresso_members_admin_message( '', $message );
	}
}
function espresso_component_load_edit_roles() {
	global $espresso_manage_roles_page;
	$edit_roles_cap = 'edit_roles';
}

function espresso_edit_roles_page() {
	require_once( 'roles.php' );
}
function espresso_new_role_default_capabilities() {
	$capabilities = array( 'read' );
	return apply_filters( 'espresso_new_role_default_capabilities', $capabilities );
}
function espresso_message_no_create_roles() {
	if ( !espresso_user_cap( 'create_roles' ) ) {
		$message = __('To create new roles, you must give the <code>create_roles</code> capability to at least one role.', 'event_espresso');
		espresso_members_admin_message( '', $message );
	}
}
function espresso_new_role_page() {
	require_once( 'new-role.php' );
}

function espresso_members_admin_message( $class = '', $message = '' ) {
	if ( !$class )
		$class = 'updated fade below-h2';

	echo "<div class='{$class}' style='padding: 5px 10px;'>";
	echo $message;
	echo '</div>';
}