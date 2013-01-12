<?php
/**
  Plugin Name: Event Espresso - Ticketing
  Plugin URI: http://eventespresso.com/
  Description: Ticketing system for Event Espresso

  Version: 2.0.9

  Author: Event Espresso
  Author URI: http://www.eventespresso.com

  Copyright (c) 2012 Event Espresso  All Rights Reserved.

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
 //Define the version of the plugin
function espresso_ticketing_version() {
	return '2.0.9';
}

//Update notifications
add_action('action_hook_espresso_ticketing_update_api', 'ee_ticketing_load_pue_update');
function ee_ticketing_load_pue_update() {
	global $org_options, $espresso_check_for_updates;
	if ( $espresso_check_for_updates == false )
		return;
		
	if (file_exists(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php')) { //include the file 
		require(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php' );
		$api_key = $org_options['site_license_key'];
		$host_server_url = 'http://eventespresso.com';
		$plugin_slug = 'espresso-ticketing';
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

global $wpdb;
define("ESPRESSO_TICKETING_VERSION", espresso_ticketing_version());
define("ESPRESSO_TICKETING_PATH", "/" . plugin_basename(dirname(__FILE__)) . "/");
define("ESPRESSO_TICKETING_FULL_PATH", WP_PLUGIN_DIR . ESPRESSO_TICKETING_PATH);
define("ESPRESSO_TICKETING_FULL_URL", WP_PLUGIN_URL . ESPRESSO_TICKETING_PATH);
define("ESPRESSO_TICKETING_ACTIVE", TRUE);
define("EVENTS_TICKET_TEMPLATES", $wpdb->prefix . "events_ticket_templates");
//echo $espresso_path;
require_once('functions.php');
require_once('manager/index.php');
/* function event_espresso_ticket_config_mnu() {
  } */
//Install plugin
register_activation_hook(__FILE__, 'espresso_ticketing_install');
register_deactivation_hook(__FILE__, 'espresso_ticketing_deactivate');
//Deactivate the plugin
if (!function_exists('espresso_ticketing_deactivate')) {

	function espresso_ticketing_deactivate() {
		update_option('espresso_ticketing_active', 0);
	}

}

//Install the plugin
if (!function_exists('espresso_ticketing_install')) {

	function espresso_ticketing_install() {

		update_option('espresso_ticketing_version', ESPRESSO_TICKETING_VERSION);
		update_option('espresso_ticketing_active', 1);
		global $wpdb;

		$table_version = ESPRESSO_TICKETING_VERSION;

		$table_name = "events_ticket_templates";
		$sql = "id int(11) unsigned NOT NULL AUTO_INCREMENT,
			ticket_name VARCHAR(100) DEFAULT NULL,
			css_file VARCHAR(100) DEFAULT 'simple.css',
			template_file VARCHAR(100) DEFAULT 'index.php',
			ticket_subject VARCHAR(250) DEFAULT NULL,
			ticket_content TEXT,
			ticket_logo_url TEXT,
			ticket_meta LONGTEXT DEFAULT NULL,
			wp_user int(22) DEFAULT '1',
			UNIQUE KEY id (id)";

		event_espresso_run_install($table_name, $table_version, $sql);

		$table_name = "events_attendee_checkin";
    	$sql = "id int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
			attendee_id int(11) NOT NULL,
			registration_id varchar(23) NOT NULL,
			event_id int(11) NOT NULL,
			checked_in int(11) NOT NULL,
			date_scanned datetime NOT NULL,
            KEY attendee_id (attendee_id, registration_id, event_id)";
		
		event_espresso_run_install($table_name, $table_version, $sql);
	}

}

//Install plugin
register_activation_hook( __FILE__, 'espresso_ticketing_install' );
register_deactivation_hook( __FILE__, 'espresso_ticketing_deactivate' );

function espresso_ticket_url($attendee_id, $registration_id, $extra = ''){
	$extra = empty($extra) ? '' : '&amp;'.$extra;
	return home_url().'/?ticket_launch=true&amp;id='.$attendee_id.'&amp;r_id='. $registration_id.'&amp;html=true'.$extra;
}

if (!function_exists("espresso_enqueue_admin_ticketing_menu_css")) {
    function espresso_enqueue_admin_ticketing_menu_css(){
        if ( is_admin() && function_exists('espresso_version') && espresso_version() >= '3.2.P' ){
            wp_enqueue_style('espresso_ticketing_menu', ESPRESSO_TICKETING_FULL_URL . 'css/admin-menu-styles.css');
        }

        if (isset($_REQUEST['page']) && $_REQUEST['page']=='event_tickets') {
        	wp_enqueue_style('espresso_ticketing', ESPRESSO_TICKETING_FULL_URL . 'css/admin-styles.css');
        }
    }
}
add_action('init', 'espresso_enqueue_admin_ticketing_menu_css');



function espresso_event_attendee_table_ticketing_header($t_cols) {
	?>
	<th class="manage-column column-title" id="attended" scope="col" title="Click to Sort" style="width: 8%;"> <span>
			<?php _e('Attended', 'event_espresso'); ?>
		</span> <span class="sorting-indicator"></span> </th>
	<?php
	$t_cols += 1;
	return $t_cols;
}

add_filter('filter_hook_espresso_event_attendee_table_header', 'espresso_event_attendee_table_ticketing_header');

function espresso_attendee_table_ticketing_secondary_button() {
	?>
	<input name="attended_customer" type="submit" class="button-secondary" id="attended_customer" value="<?php _e('Mark as Attended', 'event_espresso'); ?>" style="margin:10px 0 0 20px;" />

	<input name="unattended_customer" type="submit" class="button-secondary" id="unattended_customer" value="<?php _e('Unmark as Attended', 'event_espresso'); ?>" style="margin:10px 0 0 20px;" />
	<?php
}

add_action('action_hook_espresso_attendee_table_secondary_button', 'espresso_attendee_table_ticketing_secondary_button');

function espresso_event_editor_ticketing_meta_box($event) {
	?>
	<div class="inside">
		<p><?php echo espresso_ticket_dd($event->ticket_id); ?></p>
	</div>
	<?php
}

function espresso_register_ticketing_event_editor_meta_boxes() {
	add_meta_box('espresso_event_editor_ticketing_box', __('Custom Tickets', 'event_espresso'), 'espresso_event_editor_ticketing_meta_box', 'toplevel_page_events', 'side', 'default');
}

add_action('current_screen', 'espresso_register_ticketing_event_editor_meta_boxes', 40);
