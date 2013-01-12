<?php
/*
Plugin Name: Event Espresso - Members Addon
Plugin URI: http://eventespresso.com/
Description: Member integration addon for Event Espresso. <a href="admin.php?page=support">Support</a>
Version: 1.9.6

Author: Event Espresso
Author URI: http://www.eventespresso.com

Copyright (c) 2008-2012 Event Espresso All Rights Reserved.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

function espresso_members_version() {
	return '1.9.6';
}

//Update notifications
add_action('action_hook_espresso_members_update_api', 'ee_members_load_pue_update');
function ee_members_load_pue_update() {
	global $org_options, $espresso_check_for_updates;
	if ( $espresso_check_for_updates == false )
		return;
		
	if (file_exists(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php')) { //include the file 
		require(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php' );
		$api_key = $org_options['site_license_key'];
		$host_server_url = 'http://eventespresso.com';
		$plugin_slug = 'espresso-members';
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

//Load the member files
function espresso_load_member_files() {
	require_once("member_functions.php");
	require_once("user_settings_page.php");
	require_once("my_events_page.php");
}
add_action( 'plugins_loaded', 'espresso_load_member_files', 1 );

function event_espresso_members_install(){
//Members Addon database install
$table_name = "events_member_rel";
$table_version = "1.9";
$sql = "id int(11) NOT NULL AUTO_INCREMENT,
	event_id INT(11) DEFAULT NULL,
	user_id INT(11) DEFAULT NULL,
	user_role VARCHAR(50) DEFAULT NULL,
	attendee_id INT(11) DEFAULT NULL,
	PRIMARY KEY (id)";
event_espresso_run_install($table_name, $table_version, $sql);
add_option('events_members_active', 'true', '', 'yes');
update_option('events_members_active', 'true');
add_option('events_member_settings', '', '', 'yes');
//Members Addon database install end
}
function event_espresso_members_deactivate(){
	update_option( 'events_members_active', 'false');
}
register_activation_hook(__FILE__,'event_espresso_members_install');//Install members tables
register_deactivation_hook(__FILE__,'event_espresso_members_deactivate');

global $wpdb;
define("EVENTS_MEMBER_REL_TABLE",  $wpdb->prefix . "events_member_rel"); //Define Groupon db table shortname
define("EVNT_MBR_PLUGINPATH", "/" . plugin_basename( dirname(__FILE__) ) . "/");
define("EVENT_ESPRESSO_MEMBERS_DIR", WP_PLUGIN_DIR . EVNT_MBR_PLUGINPATH  );
define("EVNT_MBR_PLUGINFULLURL", WP_PLUGIN_URL . EVNT_MBR_PLUGINPATH );

