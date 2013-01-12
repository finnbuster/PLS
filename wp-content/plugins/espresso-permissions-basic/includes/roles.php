<?php
global $wp_roles, $wpdb;
$action = isset( $_GET['action'] ) ? $_GET['action'] : '';
if ( isset( $_POST['edit-role-saved'] ) && $_POST['edit-role-saved'] == 'Y' )
	$action = 'role-updated';
elseif ( isset( $_POST['action'] ) && 'delete' == $_POST['action'] && isset( $_POST['doaction'] ) && __('Apply', 'event_espresso') == $_POST['doaction'] )
	$action = 'bulk-delete';
elseif ( isset( $_POST['action2'] ) && 'delete' == $_POST['action2'] && isset( $_POST['doaction2'] ) && __('Apply', 'event_espresso') == $_POST['doaction2'] )
	$action = 'bulk-delete';

switch( $action ) {
	case 'bulk-delete' :
		$default_role = get_option( 'default_role' );
		$delete_roles = $_POST['roles'];
		if ( !is_array( $delete_roles ) ) {
			require_once( 'edit-roles.php' );
			break;
		}else {
			check_admin_referer( espresso_get_nonce( 'edit-roles' ) );
			add_action( 'espresso_pre_edit_roles_form', 'espresso_message_roles_deleted' );
			foreach ( $delete_roles as $role ) {
				$wp_user_search = new WP_User_Search( '', '', $role );
				$change_users = $wp_user_search->get_results();
				if ( isset( $change_users ) && is_array( $change_users ) ) {
					foreach( $change_users as $move_user ) {
						$new_user = new WP_User( $move_user );
						if ( $new_user->has_cap( $role ) ) {
							$new_user->remove_role( $role );
							$new_user->set_role( $default_role );
						}
					}
				}
				remove_role( $role );
			}
			require_once( 'edit-roles.php' );
			break;
		}
		break;
	case 'delete' :
		check_admin_referer( espresso_get_nonce( 'edit-roles' ) );
		add_action( 'espresso_pre_edit_roles_form', 'espresso_message_role_deleted' );
		$default_role = get_option( 'default_role' );
		$role = $_GET['role'];
		$wp_user_search = new WP_User_Search( '', '', $role );
		$change_users = $wp_user_search->get_results();
		if ( isset( $change_users ) && is_array( $change_users ) ) {
			foreach( $change_users as $move_user ) {
				$new_user = new WP_User( $move_user );
				if ( $new_user->has_cap( $role ) ) {
					$new_user->remove_role( $role );
					$new_user->set_role( $default_role );
				}
			}
		}
		remove_role( $role );
		require_once( 'default-roles.php' );
		break;
	case 'role-updated' :
		$title = __('Edit Role', 'event_espresso');
		$role = $_GET['role'];
		require_once( 'edit-roles.php' );
		break;
	case 'edit' :
		check_admin_referer( espresso_get_nonce( 'edit-roles' ) );
		$title = __('Edit Role', 'event_espresso');
		$role = $_GET['role'];
		require_once( 'edit-roles.php' );
		break;
	case 'new' :
		require_once( 'new-roles.php' );
		break;
	default :
		require_once( 'default-roles.php' );
		break;
}

?>