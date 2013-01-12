<?php
/*
  Plugin Name: Event Espresso - Multi Event Registration
  Plugin URI: http://eventespresso.com/
  Description: Multi Events Registration addon for Event Espresso.

  Version: 1.0.4

  Author: Seth Shoultes
  Author URI: http://www.eventespresso.com

  Copyright (c) 2011 Event Espresso  All Rights Reserved.

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
define("ESPRESSO_MULTI_REG_VERSION", '1.0.4');

//Update notifications
add_action('action_hook_espresso_multiple_update_api', 'ee_multiple_load_pue_update');
function ee_multiple_load_pue_update() {
	global $org_options, $espresso_check_for_updates;
	if ( $espresso_check_for_updates == false )
		return;
		
	if (file_exists(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php')) { //include the file 
		require(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php' );
		$api_key = $org_options['site_license_key'];
		$host_server_url = 'http://eventespresso.com';
		$plugin_slug = 'espresso-multiple';
		$options = array(
			'apikey' => $api_key,
			'lang_domain' => 'event_espresso',
			'checkPeriod' => '24',
			'option_key' => 'site_license_key',
      'options_page_slug' => 'event-espresso'
		);
		$check_for_updates = new PluginUpdateEngineChecker($host_server_url, $plugin_slug, $options); //initiate the class and start the plugin update engine!
	}
}

register_activation_hook(__FILE__, 'event_espresso_multi_reg_install');
register_deactivation_hook(__FILE__, 'event_espresso_multi_reg_deactivate');

$wp_plugin_url = WP_PLUGIN_URL;
//$wp_content_url = WP_CONTENT_URL;

if (is_ssl()) {
    $wp_plugin_url = str_replace('http://', 'https://', WP_PLUGIN_URL);
    //$wp_content_url = str_replace('http://', 'https://', WP_CONTENT_URL);
}


//define( "EVENT_ESPRESSO_MULTI_REG_TABLE", get_option( $wpdb->prefix . 'events_multi_reg_tbl' ) );
define("ESPRESSO_MULTI_REG_PATH", "/" . plugin_basename(dirname(__FILE__)) . "/");
define("ESPRESSO_MULTI_REG_FULL_PATH", WP_PLUGIN_DIR . ESPRESSO_MULTI_REG_PATH);
define("ESPRESSO_MULTI_REG_FULL_URL", $wp_plugin_url . ESPRESSO_MULTI_REG_PATH);
define("ESPRESSO_MULTI_REG_MODULE_ACTIVE", TRUE);

session_start();
global $events_in_session;
if(isset($_SESSION['events_in_session'])) $events_in_session = $_SESSION['events_in_session'];

if (!function_exists('event_espresso_multi_reg_install')) {
    function event_espresso_multi_reg_install() {
        update_option('event_espresso_multi_reg_version', ESPRESSO_MULTI_REG_MODULE_VERSION);
        update_option('event_espresso_multi_reg_active', 1);
        global $wpdb;
    }
}

if (!function_exists('event_espresso_multi_reg_deactivate')) {
    function event_espresso_multi_reg_deactivate() {
        update_option('event_espresso_multi_reg_active', 0);
    }
}

if (!function_exists('event_espresso_multi_reg_init')) {
    function event_espresso_multi_reg_init() {

    }
}
?>