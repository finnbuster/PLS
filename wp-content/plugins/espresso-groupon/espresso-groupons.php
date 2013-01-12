<?php
/*
Plugin Name: Event Espresso - Social Coupons
Plugin URI: http://eventespresso.com/
Description: Groupon integration addon for Event Espresso. <a href="admin.php?page=support">Support</a>

Version: 1.5.3

Author: Seth Shoultes
Author URI: http://www.eventespresso.com

Copyright (c) 2008-2011 Seth Shoultes  All Rights Reserved.

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

//Update notifications
add_action('action_hook_espresso_groupon_update_api', 'ee_groupon_load_pue_update');
function ee_groupon_load_pue_update() {
	global $org_options, $espresso_check_for_updates;
	if ( $espresso_check_for_updates == false )
		return;
		
	if (file_exists(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php')) { //include the file 
		require(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php' );
		$api_key = $org_options['site_license_key'];
		$host_server_url = 'http://eventespresso.com';
		$plugin_slug = 'espresso-groupon';
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

require_once("event_management_functions.php");
require_once("use_groupon_code.php");
require_once("groupons_admin_page.php");
function event_espresso_groupon_install(){
//Groupon database install
$table_name = "events_groupon_codes";
$table_version = "1.5.3";
$sql = "id int(11) NOT NULL AUTO_INCREMENT,
	event_id int(10) unsigned NOT NULL,
	groupon_code varchar(50) DEFAULT '0',
	groupon_status INT DEFAULT '1',
	groupon_holder TEXT DEFAULT NULL,
	attendee_id int(11) DEFAULT '0',
	date varchar(50) DEFAULT '0000-00-00',
	PRIMARY KEY (id)";
event_espresso_run_install ($table_name, $table_version, $sql);
//Groupon database install end
add_option('events_groupons_active', 'true', '', 'yes');
update_option('events_groupons_active', 'true');
}
function event_espresso_groupon_deactivate(){
	update_option( 'events_groupons_active', 'false');
}
register_activation_hook(__FILE__,'event_espresso_groupon_install');//Install groupon tables
register_deactivation_hook(__FILE__,'event_espresso_groupon_deactivate');

//$event_espresso_groupon_dir = EVENT_ESPRESSO_UPLOAD_DIR."groupons/";
//define("EVENT_ESPRESSO_GROUPON_DIR", $event_espresso_groupon_dir);
define("EVENTS_GROUPON_CODES_TABLE", get_option('events_groupon_codes_tbl')); //Define Groupon db table shortname