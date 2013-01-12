<?php
/*
Plugin Name: Event Espresso - Permissions
Plugin URI: http://www.eventespresso.com
Description: Provides support for allowing members of the espreesso_event_admin role to administer events.
Version: 1.5.3
Author: Event Espresso
Author URI: http://www.eventespresso.com
Copyright 2011  Event Espresso  (email : support@eventespresso.com)
*/

//Update notifications
add_action('action_hook_espresso_permissions_basic_update_api', 'ee_permissions_basic_load_pue_update');
function ee_permissions_basic_load_pue_update() {
	global $org_options, $espresso_check_for_updates;
	if ( $espresso_check_for_updates == false )
		return;
		
	if (file_exists(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php')) { //include the file 
		require(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php' );
		$api_key = $org_options['site_license_key'];
		$host_server_url = 'http://eventespresso.com';
		$plugin_slug = 'espresso-permissions-basic';
		$options = array(
			'apikey' => $api_key,
			'lang_domain' => 'event_espresso',
			'checkPeriod' => '24',
			'option_key' => 'site_license_key',
			'options_page_slug' => 'event_espresso'
		);
		$check_for_updates = new PluginUpdateEngineChecker($host_server_url, $plugin_slug, $options); //initiate the class and start the plugin update engine!
	}
}

include("includes/functions.php");
//Define the version of the plugin
function espresso_manager_version() {
	return '1.5.2';
}
define("ESPRESSO_MANAGER_VERSION", espresso_manager_version() );

//Define the plugin directory and path
define("ESPRESSO_MANAGER_PLUGINPATH", "/" . plugin_basename( dirname(__FILE__) ) . "/");
define("ESPRESSO_MANAGER_PLUGINFULLPATH", WP_PLUGIN_DIR . ESPRESSO_MANAGER_PLUGINPATH  );
define("ESPRESSO_MANAGER_PLUGINFULLURL", WP_PLUGIN_URL . ESPRESSO_MANAGER_PLUGINPATH );

//Globals
global $espresso_manager;
$espresso_manager = get_option('espresso_manager_settings');

//Install the plugin
function espresso_manager_install(){
	//Install Event Manager Options
	$espresso_manager = array(
					'espresso_manager_general' => "administrator",
					'espresso_manager_events' => "administrator",
					'espresso_manager_categories' => "administrator",
					'espresso_manager_discounts' => "administrator",
					'espresso_manager_groupons' => "administrator",
					'espresso_manager_form_builder' => "administrator",
					'espresso_manager_form_groups' => "administrator",
					'espresso_manager_event_emails' => "administrator",
					'espresso_manager_payment_gateways' => "administrator",
					'espresso_manager_members' => "administrator",
					'espresso_manager_calendar' => "administrator",
					'espresso_manager_social' => "administrator",
					'espresso_manager_addons' => "administrator",
					'espresso_manager_support' => "administrator",
					'espresso_manager_venue_manager' => "administrator",
					'espresso_manager_personnel_manager' => "administrator",
					'espresso_manager_ticketing' => "administrator",
					'espresso_manager_seating' => "administrator",
					'event_manager_approval' => "N",
					'event_manager_venue'=>'Y',
					'event_manager_staff'=>'Y',
					'event_manager_create_post'=>'Y',
					'event_manager_share_cats'=>'Y',
					'minimum_fes_level'=>'espresso_event_manager',
				);
	add_option( 'espresso_manager_settings', $espresso_manager );
	// add more capabilities to the subscriber role only for this plugin
	$result = add_role('espresso_event_admin', 'Espresso Master Admin', array(
	    'read' => true, // True allows that capability
	    'edit_posts' => false,
	    'espresso_group_admin' => false,
	    'espresso_event_admin' => true,
	    'espresso_event_manager' => true,
	    'delete_posts' => false, // Use false to explicitly deny
	));

	//we need to make sure administrator gets all new caps
	$role = get_role('administrator');
	$role->add_cap('espresso_group_admin');
	$role->add_cap('espresso_event_admin');
	$role->add_cap('espresso_event_manager');
}
register_activation_hook(__FILE__,'espresso_manager_install');


add_action( 'action_hook_espresso_permissions', 'espresso_permissions_run', 20 );
// IMPORTANT: added the bellow add_action in init because the above add action doesn't work. Because, espresso core loads before this
// and the do_action call fails because this add_action wasn't loaded.
if ( !function_exists('espresso_is_admin')) {
    add_action('init','espresso_permissions_run',20);
}
function espresso_permissions_run(){ 
	//Checks to see if a user is an admin
	//Overridden in pro  
    
	if (!function_exists('espresso_is_admin')) {
		function espresso_is_admin(){
			if( current_user_can('espresso_event_admin') || current_user_can('administrator') || current_user_can('espresso_group_admin') ){
				return true;
			}
		}	
	}
	
	//Checks to see if this is the users event
	//Overridden in pro
	if (!function_exists('espresso_is_my_event')) {
		function espresso_is_my_event($event_id){
			global $wpdb;
			if( current_user_can('administrator') || espresso_member_data('role')=='espresso_event_admin'){
				return true;
			}
		}
	}
	
	//Returns information about the current roles
	//Overridden in pro
	if (!function_exists('espresso_role_data')) {
		function espresso_role_data($type){
			global $wpdb;
			$sql = "SELECT
			ID, user_email, user_login,
			first_name.meta_value as first_name,
			last_name.meta_value as last_name,
			phone_number.meta_value as phone_number,
			wp_capabilities.meta_value as wp_capabilities ";
			$sql .= " FROM wp_users
				JOIN wp_usermeta AS wp_capabilities ON wp_capabilities.user_id=ID
					AND wp_capabilities.meta_key='wp_capabilities'
				LEFT JOIN wp_usermeta AS first_name ON first_name.user_id=ID
					AND first_name.meta_key='first_name'
				LEFT JOIN wp_usermeta AS last_name ON last_name.user_id=ID
					AND last_name.meta_key='last_name'
				LEFT JOIN wp_usermeta AS phone_number ON phone_number.user_id=ID
					AND phone_number.meta_key='phone_number' ";
			$sql .= " WHERE ";
			//$sql .= " wp_capabilities.meta_value LIKE '%administrator%' OR wp_capabilities.meta_value LIKE '%espresso_event_admin%' OR wp_capabilities.meta_value LIKE '%espresso_event_manager%' ";
			//$sql .= " ORDER BY ID";
		
			switch($type){
				case 'admin_count':
					$sql .= " wp_capabilities.meta_value LIKE '%administrator%' ";
					$wpdb->get_results($sql);
					return $wpdb->num_rows;
				break;
				case 'event_admin_count':
					$sql .= " wp_capabilities.meta_value LIKE '%espresso_event_admin%' ";
					$wpdb->get_results($sql);
					return $wpdb->num_rows;
				break;
			}
		}
	}

}

//Checks to see if a user has permissions to view an event.
//Overridden in pro
if (!function_exists('espresso_can_view_event')) {
	function espresso_can_view_event($event_id){
		if ( current_user_can('espresso_event_admin')==true || espresso_is_my_event($event_id) ){
			return true;
		}
	}	
}



//Returns the id, capability, and role of a user
//Core only
function espresso_member_data($type=''){
	global $current_user;
	wp_get_current_user();

	$curauth = wp_get_current_user();
	$user_id = $curauth->ID;
	$user = new WP_User( $user_id );

	switch ($type){
		case 'id':
		return $user_id;
		break;
		case 'cap':
			if ( !empty( $user->allcaps ) && is_array( $user->allcaps ) ) {
				$str = array();
				foreach($user->allcaps as $k=>$v){
					$str[] = $k;
				}
				return implode("|",$str);
			}
		break;
		case 'role';
			if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
			foreach ( $user->roles as $role )
				return $role;
			}
		break;
	}
}

//Returns the user meta
//Core only
if (!function_exists('espresso_user_meta')) {
	function espresso_user_meta($user_id, $key){
		$user = new WP_User( $user_id );
		//echo "<pre>".print_r($user,true)."</pre>";
		//print_r($user->data);
		//echo array_key_exists($key, $user);
		//echo $user->data->ID;
		if ($user->data->ID >0 && array_key_exists($key, $user->data)) {
			return esc_attr($user->$key);
		}
	}
}

/**  ADDED BY DARREN  **/
/* These filters are for hooking R&P into the questions/question groups management. They get removed and modified by R&P Pro */
add_filter('espresso_get_user_question_groups_where', 'espresso_rp_basic_question_groups_where', 10, 3);
add_filter('espresso_get_user_question_groups_groups', 'espresso_rp_basic_question_groups_groups', 10, 3);
add_filter('espresso_get_user_questions_for_group', 'espresso_rp_basic_get_user_questions_for_group', 10, 3);
add_filter('espresso_get_user_questions_for_group_extra_attached', 'espresso_rp_basic_get_user_questions_for_group', 10, 3);
//questions
add_filter('espresso_get_user_questions_where', 'espresso_rp_basic_get_user_questions_where', 10, 3);
add_filter('espresso_get_user_questions_questions', 'espresso_rp_basic_get_user_questions_questions', 10, 3);
add_filter('espresso_get_question_groups_for_event_where', 'espresso_rp_basic_get_question_groups_for_event_where', 10, 3);
add_filter('espresso_get_question_groups_for_event_groups', 'espresso_rp_basic_get_question_groups_for_event_groups', 10, 3);

function espresso_rp_basic_question_groups_where($where, $user_id, $num) {
	$modified_where = " WHERE qg.wp_user = '" . $user_id . "' ";

	if ( espresso_is_admin() && !$num ) {
		$where = !isset($_REQUEST['all']) ? $modified_where : "";
	} else {
		$where = $modified_where;
	}

	return $where;
}

//currently doing nothing with the filter.  Just a placeholder for now.
function espresso_rp_basic_question_groups_groups($groups, $user_id, $num) {
	return $groups;
}

function espresso_rp_basic_get_user_questions_for_group( $where, $group_id, $user_id ) {
		$where = " WHERE 1=1";

	return $where;
}

function espresso_rp_basic_get_user_questions_where( $where, $user_id, $num ) {
	$modified_where = " WHERE q.wp_user = '" . $user_id . "' ";
	if ( espresso_is_admin() && !$num ) {
		$where = !isset($_REQUEST['all']) ? $modified_where : "";
	} else {
		$where = $modified_where;
	}


	if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'new_group' )
		$where =  "";

	return $where;
}

//currently just a placeholder
function espresso_rp_basic_get_user_questions_questions( $questions, $user_id, $num ) {
	return $questions;
}

function espresso_rp_basic_get_question_groups_for_event_where($where, $existing_question_groups, $event) {
	$modified_where = " WHERE qg.wp_user = '" . get_current_user_id() . "' ";

	//if we've got existing $questions then we want to make sure we're pulling them in.
	if ( !empty($existing_question_groups) ) {
		$modified_where .= " OR qg.id IN (  " . implode( ',', $existing_question_groups ) . " ) ";
	}

	return $modified_where;
}

function espresso_rp_basic_get_question_groups_for_event_groups( $event_question_groups, $existing_question_groups, $event ) {
	$current_user_groups = array();
	$other_user_groups = array();
	$checked_groups = array();
	$has_system_group = false;
	$current_user_id = get_current_user_id();
	
	//basically we want to make sure we're just displaying the system group for the EVENT user (otherwise we'll have duplicates. Also, let's make sure there IS a system group (if there isn't then we need to display the default).
	foreach ( $event_question_groups as $question_group ) {
		if ( $question_group->system_group == 1 && $question_group->wp_user == $current_user_id ) {
			$current_user_groups[] = $question_group;
			$has_system_group = true;
			continue;
		}

		if ( $question_group->system_group == 1 && $question_group->wp_user != $current_user_id ) {
			//TODO: note.  This is assuming that ONLY one system group is currently in place for EE (personal).  If more system groups are added down the road then this has to be modified (3.2 will have changes to allow for more possible system groups).
			if ( $has_system_group ) continue;
			$other_user_groups[] = $question_group;
			$has_system_group = true;
			continue;
		}

		$checked_groups[] = $question_group;
	}

	if ( !empty($current_user_groups) && $has_system_group ) {
		$other_user_groups = $current_user_groups;
	}

	$checked_groups = ( $has_system_group ) ? array_merge($other_user_groups, $checked_groups ) : $checked_groups;

	//this is a fringe case... shouldn't ever be needed but well you know how it goes...
	if ( !$has_system_group ) {
		//hth did the event get saved/displayed without at least one system question group?  We better add one into the array.
		global $wpdb;
		$sql = "SELECT qg.* FROM " . EVENTS_QST_GROUP_TABLE . " AS qg WHERE qg.system_group = '1' AND ( qg.wp_user = '0' or qg.wp_user = '1' ) ";
		$default_groups = $wpdb->get_results($wpdb->prepare($sql) );
		$checked_groups = array_merge($default_groups, $checked_groups);
	}

	return $checked_groups;
}

/** END ADDED BY DARREN **/


//This function is previously declared in functions/main.php. Credit goes to Justin Tadlock (http://justintadlock.com/archives/2009/09/18/custom-capabilities-in-plugins-and-themes)
//This function simply returns a custom capability, nothing else. Can be used to change admin capability of the Event Manager menu without the admin losing rights to certain menus.
//Core only
if (!function_exists('espresso_management_capability')) {
	function espresso_management_capability( $default, $custom ) {
		return $custom;
	}
	add_filter( 'espresso_management_capability', 'espresso_management_capability', 10, 3 );
}

//Add a settings link to the Plugins page, so people can go straight from the plugin page to the settings page.
//Core only
function espresso_manager_plugin_actions( $links, $file ){
	// Static so we don't call plugin_basename on every plugin row.
	static $this_plugin;
	if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);

	if ( $file == $this_plugin ){
		$org_settings_link = '<a href="admin.php?page=espresso_permissions">' . __('Settings') . '</a>';
		array_unshift( $links, $org_settings_link ); // before other links
	}
	return $links;
}
add_filter( 'plugin_action_links', 'espresso_manager_plugin_actions', 10, 2 );



//Create pages
//Core only
function espresso_permissions_roles_mnu(){
	global $wpdb, $espresso_manager, $wp_roles;
?>
<div id="configure_espresso_manager_form" class="wrap meta-box-sortables ui-sortable">
  <div id="icon-options-event" class="icon32"> </div>
  <h2>
    <?php _e('Event Espresso - User Roles Manager','event_espresso'); ?>
  </h2>
  <div id="event_espresso-col-left" style="width:70%;">
    <?php espresso_edit_roles_page(); ?>
  </div>
</div>
<?php
}

function espresso_permissions_newroles_mnu(){
	global $wpdb, $espresso_manager, $wp_roles;
	//Debug
	//echo "-----> ".espresso_user_cap("espresso_group_admin");

?>
<div id="configure_espresso_manager_form" class="wrap meta-box-sortables ui-sortable">
  <div id="icon-options-event" class="icon32"> </div>
  <h2>
    <?php _e('Event Espresso - User Roles Manager','event_espresso'); ?>
  </h2>
  <div id="event_espresso-col-left" style="width:70%;">
    <?php espresso_new_role_page(); ?>
  </div>
</div>
<?php
}

function espresso_permissions_config_mnu(){

	global $wpdb, $espresso_manager, $wp_roles;

	function espresso_manager_updated(){
		return __('Manager details saved.','event_espresso');
	}

	if ( isset( $_POST['update_permissions'] ) && 'update' == $_POST['update_permissions'] ) {

		$espresso_manager['espresso_manager_general']           = isset( $_POST['espresso_manager_general'] ) ? $_POST['espresso_manager_general'] : '';
		$espresso_manager['espresso_manager_events']            = isset( $_POST['espresso_manager_events'] ) ? $_POST['espresso_manager_events'] : '';
		$espresso_manager['espresso_manager_categories']        = isset( $_POST['espresso_manager_categories'] ) ? $_POST['espresso_manager_categories'] : '';
		$espresso_manager['espresso_manager_discounts']         = isset( $_POST['espresso_manager_discounts'] ) ? $_POST['espresso_manager_discounts'] : '';
		$espresso_manager['espresso_manager_groupons']          = isset( $_POST['espresso_manager_groupons'] ) ? $_POST['espresso_manager_groupons'] : '';
		$espresso_manager['espresso_manager_form_builder']      = isset( $_POST['espresso_manager_form_builder'] ) ? $_POST['espresso_manager_form_builder'] : '';
		$espresso_manager['espresso_manager_form_groups']       = isset( $_POST['espresso_manager_form_groups'] ) ? $_POST['espresso_manager_form_groups'] : '';
		$espresso_manager['espresso_manager_event_emails']      = isset( $_POST['espresso_manager_event_emails'] ) ? $_POST['espresso_manager_event_emails'] : '';
		$espresso_manager['espresso_manager_payment_gateways']  = isset( $_POST['espresso_manager_payment_gateways'] ) ? $_POST['espresso_manager_payment_gateways'] : '';
		$espresso_manager['espresso_manager_members']           = isset( $_POST['espresso_manager_members'] ) ? $_POST['espresso_manager_members'] : '';
		$espresso_manager['espresso_manager_calendar']          = isset( $_POST['espresso_manager_calendar'] ) ? $_POST['espresso_manager_calendar'] : '';
		$espresso_manager['espresso_manager_ticketing']          = isset( $_POST['espresso_manager_ticketing'] ) ? $_POST['espresso_manager_ticketing'] : '';
		$espresso_manager['espresso_manager_seating']          = isset( $_POST['espresso_manager_seating'] ) ? $_POST['espresso_manager_seating'] : '';
		$espresso_manager['espresso_manager_social']            = isset( $_POST['espresso_manager_social'] ) ? $_POST['espresso_manager_social'] : '';
		$espresso_manager['espresso_manager_addons']            = isset( $_POST['espresso_manager_addons'] ) ? $_POST['espresso_manager_addons'] : '';
		$espresso_manager['espresso_manager_support']           = isset( $_POST['espresso_manager_support'] ) ? $_POST['espresso_manager_support'] : '';
		$espresso_manager['espresso_manager_venue_manager']     = isset( $_POST['espresso_manager_venue_manager'] ) ? $_POST['espresso_manager_venue_manager'] : '';
		$espresso_manager['espresso_manager_personnel_manager'] = isset( $_POST['espresso_manager_personnel_manager'] ) ? $_POST['espresso_manager_personnel_manager'] : '';
		$espresso_manager['event_manager_approval']             = isset( $_POST['event_manager_approval'] ) ? $_POST['event_manager_approval'] : '';
		$espresso_manager['event_manager_venue']                = isset( $_POST['event_manager_venue'] ) ? $_POST['event_manager_venue'] : '';
		$espresso_manager['event_manager_staff']                = isset( $_POST['event_manager_staff'] ) ? $_POST['event_manager_staff'] : '';
		$espresso_manager['event_manager_create_post']          = isset( $_POST['event_manager_create_post'] ) ? $_POST['event_manager_create_post'] : '';
		$espresso_manager['event_manager_share_cats']           = isset( $_POST['event_manager_share_cats'] ) ? $_POST['event_manager_share_cats'] : '';
		$espresso_manager['minimum_fes_level']           		= isset( $_POST['minimum_fes_level'] ) ? $_POST['minimum_fes_level'] : '';

		update_option( 'espresso_manager_settings', $espresso_manager);
		add_action( 'admin_notices', 'espresso_manager_updated');
	}
	if ( isset( $_REQUEST['reset_permissions'] ) && 'true' == $_REQUEST['reset_permissions'] ) {
		delete_option("espresso_manager_settings");
		espresso_manager_install();
	}
	$espresso_manager = get_option('espresso_manager_settings');

	$values=array(
		array('id'=>'administrator','text'=> __('Administrator','event_espresso')),
		array('id'=>'espresso_event_admin','text'=> __('Event Admin','event_espresso')),
	);
	
	//OVerride the values array if the pro version is installed
	if (function_exists('espresso_manager_pro_options')) {
		$values=array(
			array('id'=>'administrator','text'=> __('Administrator','event_espresso')),
			array('id'=>'espresso_event_admin','text'=> __('Master Admin','event_espresso')),
			array('id'=>'espresso_event_manager','text'=> __('Event Manager','event_espresso')),
			array('id'=>'espresso_group_admin','text'=> __('Regional Manager','event_espresso'))
		);
	}
?>
<div id="configure_espresso_manager_form" class="wrap meta-box-sortables ui-sortable">
  <div class="wrap">
    <div id="icon-options-event" class="icon32"> </div>
    <h2>
      <?php _e('Event Espresso - Event Manager Permissions','event_espresso'); ?>
    </h2>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
      <?php event_espresso_display_right_column ();?>
      <div id="post-body">
        <div id="post-body-content">
        <div class="postbox">
              <h3>
                <?php _e('Current Roles/Capabilities', 'event_espresso'); ?>
              </h3>
              <div class="inside">
          <ul>
            <?php
#	print_r(	get_role("administrator")	);
#	echo espresso_member_data('role');
						$users_of_blog = count_users();
						$total_users = $users_of_blog['total_users'];
						$avail_roles =& $users_of_blog['avail_roles'];
						unset($users_of_blog);
                        $role = isset( $_REQUEST['role'] ) ? $_REQUEST['role'] : '';
						$current_role = false;
						$class = empty($role) ? ' class="current"' : '';
						$role_links = array();
						//$role_links[] = "<li><a href='users.php'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_users, 'users' ), number_format_i18n( $total_users ) ) . '</a>';
						foreach ( $wp_roles->get_names() as $this_role => $name ) {
//							if ( !isset($avail_roles[$this_role]) ) continue;
							$class = '';
							if ( $this_role == $role ) {
								$current_role = $role;
								$class = ' class="current"';
							}
							$name = translate_user_role( $name );
							/* translators: User role name with count */
                            if ( isset( $avail_roles[$this_role] ) )
                                $name = sprintf( __('%1$s <span class="count">(%2$s)</span>'), $name, $avail_roles[$this_role] );
							switch($this_role){
								case 'administrator':
									$role_links[] = "<li><a href='users.php?role=$this_role'$class>$name</a><br />".__('Access to all admin pages and all events/attendees.', 'event_espresso');
								break;
								case 'espresso_event_admin':
									$role_links[] = "<li><a href='users.php?role=$this_role'$class>$name</a><br />".__('Access to selected admin pages below and all events/attendees.', 'event_espresso');
								break;
								
							}
							if (function_exists('espresso_manager_pro_options')) {
								switch($this_role){
									case 'espresso_group_admin':
										$role_links[] = "<li><a href='users.php?role=$this_role'$class>$name</a><br />".__('Access to selected admin pages below, personal events/attendees, and any events/attendees that are located within the assigned locale/region.', 'event_espresso');
									break;
									case 'espresso_event_manager':
										$role_links[] = "<li><a href='users.php?role=$this_role'$class>$name</a><br />".__('Access to selected admin pages below and personal events/attendees.', 'event_espresso');
									break;
								}
							}
						}

						echo implode( "</li>\n", $role_links) . '</li>';
						unset($role_links);

				?>
          </ul>
          </div>
          </div>
          <form class="espresso_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']?>">
            <div class="postbox">
              <h3>
                <?php _e('Minimum Page Permissions', 'event_espresso'); ?>
              </h3>
              <table id="table" class="widefat fixed" width="100%">
                <tbody>
                  <tr>
                    <td><label for="espresso_manager_general">
                        <?php _e('General Settings Page','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_general', $values, $espresso_manager['espresso_manager_general']);?></td>
                  </tr>
                  <tr>
                    <td><label for="espresso_manager_events">
                        <?php _e('Event/Attendee Listings Page','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_events', $values, $espresso_manager['espresso_manager_events']);?></td>
                  </tr>
                  <tr>
                    <td><label for="espresso_manager_categories">
                        <?php _e('Categories Page','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_categories', $values, $espresso_manager['espresso_manager_categories']);?></td>
                  </tr>
                  <tr>
                    <td><label for="espresso_manager_discounts">
                        <?php _e('Discounts Page','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_discounts', $values, $espresso_manager['espresso_manager_discounts']);?></td>
                  </tr>
                  <?php if (function_exists('event_espresso_groupon_config_mnu')) {?>
                  <tr>
                    <td><label for="espresso_manager_groupons">
                        <?php _e('Groupons Page','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_groupons', $values, $espresso_manager['espresso_manager_groupons']);?></td>
                  </tr>
                  <?php }?>
                  <tr>
                    <td><label for="espresso_manager_form_builder">
                        <?php _e('Questions Page','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_form_builder', $values, $espresso_manager['espresso_manager_form_builder']);?></td>
                  </tr>
                  <tr>
                    <td><label for="espresso_manager_form_groups">
                        <?php _e('Question Groups Page','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_form_groups', $values, $espresso_manager['espresso_manager_form_groups']);?></td>
                  </tr>
                  <tr>
                    <td><label for="espresso_manager_venue_manager">
                        <?php _e('Venue Manager Page','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_venue_manager', $values, $espresso_manager['espresso_manager_venue_manager']);?></td>
                  </tr>
                  <tr>
                    <td><label for="espresso_manager_personnel_manager">
                        <?php _e('Staff Manager Page','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_personnel_manager', $values, $espresso_manager['espresso_manager_personnel_manager']);?></td>
                  </tr>
                  <tr>
                    <td><label for="espresso_manager_event_emails">
                        <?php _e('Email Manager Page','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_event_emails', $values, $espresso_manager['espresso_manager_event_emails']);?></td>
                  </tr>
                  <tr>
                    <td><label for="espresso_manager_payment_gateways">
                        <?php _e('Payment Settings Page','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_payment_gateways', $values, $espresso_manager['espresso_manager_payment_gateways']);?></td>
                  </tr>
                  <tr>
                    <td><label for="espresso_manager_members">
                        <?php _e('Member Settings Page','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_members', $values, $espresso_manager['espresso_manager_members']);?></td>
                  </tr>
                  <tr>
                    <td><label for="espresso_manager_calendar">
                        <?php _e('Calendar Settings Page','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_calendar', $values, $espresso_manager['espresso_manager_calendar']);?></td>
                  </tr>
				  <tr>
                    <td><label for="espresso_manager_ticketing">
                        <?php _e('Ticket Templates','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_ticketing', $values, $espresso_manager['espresso_manager_ticketing']);?></td>
                  </tr>
				  <tr>
                    <td><label for="espresso_manager_seating">
                        <?php _e('Seating Chart','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_seating', $values, $espresso_manager['espresso_manager_seating']);?></td>
                  </tr>
                  <tr>
                    <td><label for="espresso_manager_social">
                        <?php _e('Social Media Settings Page','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_social', $values, $espresso_manager['espresso_manager_social']);?></td>
                  </tr>
                  <tr>
                    <td><label for="espresso_manager_addons">
                        <?php _e('Addons Page','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_addons', $values, $espresso_manager['espresso_manager_addons']);?></td>
                  </tr>
                  <tr>
                    <td><label for="espresso_manager_support">
                        <?php _e('Support Page','event_espresso'); ?>
                      </label></td>
                    <td><?php echo select_input('espresso_manager_support', $values, $espresso_manager['espresso_manager_support']);?></td>
                  </tr>
                </tbody>
              </table>
            </div>
<?php 
			if (function_exists('espresso_manager_pro_options')) {
				echo espresso_manager_pro_options();
			}
?>

            <input type="hidden" name="update_permissions" value="update" />
            <p>
              <input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Permissions', 'event_espresso'); ?>" id="save_permissions" />
            </p>
            <p>
              <?php _e('Reset Permissions?', 'event_espresso'); ?>
              <input name="reset_permissions" type="checkbox" value="true" />
            </p>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>
<?php
}

