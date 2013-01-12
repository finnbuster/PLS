<?php
/**
  Plugin Name: Event Espresso - Recurring Events
  Plugin URI: http://eventespresso.com/
  Description: Recurring Events addon for Event Espresso.

  Version: 1.1.7

  Author: Event Espresso
  Author URI: http://www.eventespresso.com

  Copyright (c) 2010 Event Espresso  All Rights Reserved.

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
add_action('action_hook_espresso_recurring_update_api', 'ee_recurring_load_pue_update');
function ee_recurring_load_pue_update() {
	global $org_options, $espresso_check_for_updates;
	if ( $espresso_check_for_updates == false )
		return;
		
	if (file_exists(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php')) { //include the file 
		require(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php' );
		$api_key = $org_options['site_license_key'];
		$host_server_url = 'http://eventespresso.com';
		$plugin_slug = 'espresso-recurring';
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

register_activation_hook( __FILE__, 'event_espresso_re_install' );
register_deactivation_hook( __FILE__, 'event_espresso_re_deactivate' );

global $wpdb;
define( "EVENT_ESPRESSO_RECURRENCE_TABLE", $wpdb->prefix . 'events_recurrence');
define( "EVENT_ESPRESSO_RECURRENCE_PATH", "/" . plugin_basename( dirname( __FILE__ ) ) . "/" );
define( "EVENT_ESPRESSO_RECURRENCE_FULL_PATH", WP_PLUGIN_DIR . EVENT_ESPRESSO_RECURRENCE_PATH );
define( "EVENT_ESPRESSO_RECURRENCE_FULL_URL", WP_PLUGIN_URL . EVENT_ESPRESSO_RECURRENCE_PATH );
define( "EVENT_ESPRESSO_RECURRENCE_MODULE_ACTIVE", TRUE );
define( "EVENT_ESPRESSO_RECURRENCE_MODULE_VERSION", '1.1.7' );


/*
 * Used for display, you can use any of the php date formats (http://php.net/manual/en/function.date.php) *
 */

define( "EVENT_ESPRESSO_RECURRENCE_DATE_FORMAT", 'D, m/d/Y' );

if ( !function_exists( 'event_espresso_re_install' ) )
{


    function event_espresso_re_install() {


        update_option( 'event_espresso_re_version', EVENT_ESPRESSO_RECURRENCE_MODULE_VERSION );
        update_option( 'event_espresso_re_active', 1 );
        global $wpdb;

        $table_version = EVENT_ESPRESSO_RECURRENCE_MODULE_VERSION;

        $table_name = $wpdb->prefix . "events_recurrence";
		$sql = "CREATE TABLE " . $table_name . " (
	               `recurrence_id` int(11) NOT NULL AUTO_INCREMENT,
                      `recurrence_start_date` date NOT NULL,
                      `recurrence_event_end_date` date NOT NULL,
                      `recurrence_end_date` date NOT NULL,
                      `recurrence_regis_start_date` date NOT NULL,
                      `recurrence_regis_end_date` date NOT NULL,
                      `recurrence_frequency` tinytext NOT NULL,
                      `recurrence_interval` tinyint(4) NOT NULL,
                      `recurrence_weekday` varchar(255) NOT NULL,
                      `recurrence_type` tinytext NOT NULL,
                      `recurrence_repeat_by` tinytext NOT NULL,
                      `recurrence_regis_date_increment` tinytext NOT NULL,
                      `recurrence_manual_dates` LONGTEXT NULL,
                      `recurrence_visibility` varchar(2) DEFAULT NULL,
                      PRIMARY KEY (`recurrence_id`),
                      UNIQUE KEY `recurrence_id` (`recurrence_id`))";

        if ( $wpdb->get_var( "show tables like '$table_name'" ) != $table_name )
        {




            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta( $sql );

            update_option( $table_name . "_tbl_version", $table_version );
            update_option( $table_name . "_tbl", $table_name );
        }

		 $installed_ver = get_option( $table_name.'_tbl_version' );
	     if( $installed_ver != $table_version ) {
			$sql_create_table = "CREATE TABLE " . $table_name . " ( " . $sql . " ) ;";
	      	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	     	dbDelta($sql_create_table);
	     	update_option( $table_name.'_tbl_version', $table_version );
	      }
    }

}

if ( !function_exists( 'event_espresso_re_deactivate' ) )
{


    function event_espresso_re_deactivate() {

        update_option( 'event_espresso_re_active', 0 );
    }

}

add_action( 'wp_ajax_show_recurring_dates', 'recurring_days' );

if ( !function_exists( 'recurring_days' ) )
{


    function recurring_days() {
        global $wpdb;

        if ($_POST['recurrence_start_date']=='' || $_POST['recurrence_end_date'] =='' || $_POST['recurrence_regis_start_date']=='' || $_POST['recurrence_regis_end_date'] == '')
            exit("Continue selecting fields.");
        require('functions/re_functions.php');
        require('functions/re_view_functions.php');

        $re_params = array(
            'start_date' => $_POST['recurrence_start_date'],
            'event_end_date' => $_POST['recurrence_event_end_date'],
            'end_date' => $_POST['recurrence_end_date'],
            'registration_start' => $_POST['recurrence_regis_start_date'],
            'registration_end' => $_POST['recurrence_regis_end_date'],
            'type' => $_POST['recurrence_type'],
            'frequency' => $_POST['recurrence_frequency'],
            'interval' => $_POST['recurrence_interval'],
            'weekdays' => $_POST['recurrence_weekday'],
            'repeat_by' => $_POST['recurrence_repeat_by'],
            'recurrence_regis_date_increment' => $_POST['recurrence_regis_date_increment'],
            'recurrence_visibility' => ''/*$_POST['recurrence_visibility']*/,
            'recurrence_id' => $_POST['recurrence_id']
        );



        if ( $_POST['recurrence_apply_changes_to'] == 3 )
        {
            // This and upcoming events based on recurrence id and start_date >=start_date
            $re_params['start_date'] = $_POST['start_date'];
        }
        $recurrence_dates = find_recurrence_dates( $re_params );
        //print_r($recurrence_dates);
        echo recurrence_table( $recurrence_dates, __( "Projected recurrences of this event.", 'event_espresso' ), 1 );

        die();
    }

}

function espresso_re_styles(){
	if (isset($_REQUEST['page'])) {
		switch ($_REQUEST['page']) {
			case ( 'events' ):
				wp_enqueue_style('recurring_events_style', EVENT_ESPRESSO_RECURRENCE_FULL_URL . 'css/recurring_events_style.css');
			break;
		}
	}
}
add_action('admin_print_styles', 'espresso_re_styles');