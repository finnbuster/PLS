<?php
//This is a template file for displaying a list of events on a page. These functions are used with the {ESPRESSO_EVENTS} shortcode.

//This is an group of functions for querying all of the events in your databse. 
//This file should be stored in your "/wp-content/uploads/espresso/templates/" directory.
//Note: All of these functions can be overridden using the "Custom Files" addon. The custom files addon also contains sample code to display ongoing events

if (!function_exists('display_all_events')) {
	function display_all_events(){
		
		//If set to true, the event page will display recurring events.
		$display_recurrence_event = true; //If set to true, the event page will display recurring events.
		
		//Old query
		/*$sql = "SELECT e.* FROM ". EVENTS_DETAIL_TABLE . " e ";
		$sql .= " WHERE is_active = 'Y' ";
		$sql .= $display_recurrence_event == false ? " AND e.recurrence_id = '0' " : '';
		$sql .= " ORDER BY date(start_date), id";*/
		
		//New query
		//This query seperates the recurring events and groups them together.
		$sql = "SELECT e.*, if(e.recurrence_id >0,(SELECT e2.start_date FROM ". EVENTS_DETAIL_TABLE . " e2  WHERE e2.is_active = 'Y' AND e2.recurrence_id = e.recurrence_id ORDER BY e2.start_date LIMIT 1),e.start_date ) as sorter FROM ". EVENTS_DETAIL_TABLE . " e ";
		$sql .= " WHERE is_active = 'Y' ";
		$sql .= $display_recurrence_event == false ? " AND e.recurrence_id = '0' " : '';
		$sql .= " ORDER BY sorter, id";
		event_espresso_get_event_details($sql);//This function is located below
	}
}

if (!function_exists('display_event_espresso_categories')) {
	function display_event_espresso_categories($event_category_id="null", $css_class=NULL){
		global $wpdb;
		if ($event_category_id != "null"){
		    
			$display_recurrence_event = true; //If set to true, the event page will display recurring events.
			
			$sql = "SELECT e.*, c.category_name, c.category_desc, c.display_desc FROM ". EVENTS_DETAIL_TABLE . " e ";
			$sql .= " JOIN " . EVENTS_CATEGORY_REL_TABLE . " r ON r.event_id = e.id ";
			$sql .= " JOIN " . EVENTS_CATEGORY_TABLE . " c ON  c.id = r.cat_id ";
			$sql .= " WHERE c.category_identifier = '" . $event_category_id . "' ";
			$sql .= $display_recurrence_event == false ? " AND e.recurrence_id = '0' " : '';
			$sql .= " ORDER BY date(start_date), id ASC";
			event_espresso_get_event_details($sql, $css_class);//This function is located below
		}
	}
}

//Events Listing - Shows the events on your page. 
if (!function_exists('event_espresso_get_event_details')) {
	function event_espresso_get_event_details($sql, $css_class=NULL, $allow_override=0){
		
		global $wpdb, $org_options;
		//echo 'This page is located in ' . get_option( 'upload_path' );
    $event_page_id = $org_options['event_page_id'];
    $currency_symbol = isset($org_options['currency_symbol']) ? $org_options['currency_symbol'] : '';
    $events = $wpdb->get_results($sql);
    $category_id = isset($wpdb->last_result[0]->id) ? $wpdb->last_result[0]->id : '';
    $category_name = isset($wpdb->last_result[0]->category_name) ? $wpdb->last_result[0]->category_name : '';
    $category_identifier = isset($wpdb->last_result[0]->category_identifier) ? $wpdb->last_result[0]->category_identifier : '';
    $category_desc = isset($wpdb->last_result[0]->category_desc) ? html_entity_decode(wpautop($wpdb->last_result[0]->category_desc)) : '';
    $display_desc = isset($wpdb->last_result[0]->display_desc) ? $wpdb->last_result[0]->display_desc : '';
		
		/* group recuring events */
		$events_type_index = -1;
		$events_of_same_type = array();
		$last_recurrence_id = null;
		/* end group recuring events */
		
		if ($display_desc == 'Y'){
			echo '<p id="events_category_name-'. $category_id . '" class="events_category_name">' . stripslashes_deep($category_name) . '</p>';
			echo wpautop($category_desc);				
		}
		
		foreach ($events as $event){
			$event_id = $event->id;
			$event_name = $event->event_name;
			$event_desc = $event->event_desc;
			$event_identifier = $event->event_identifier;
			$active = $event->is_active;
			$registration_start = $event->registration_start;
			$registration_end = $event->registration_end;
			$start_date = $event->start_date;
			$end_date = $event->end_date;
			$reg_limit = $event->reg_limit;
			$event_address = $event->address;
			$event_address2 = $event->address2;
			$event_city = $event->city;
			$event_state = $event->state;
			$event_zip = $event->zip;
			$event_country = $event->country;
			$member_only = $event->member_only;
			$externalURL = $event->externalURL;
			$recurrence_id = $event->recurrence_id;
			
			$allow_overflow = $event->allow_overflow;
			$overflow_event_id = $event->overflow_event_id;
			
	
			//Address formatting
			$location = ($event_address != '' ? $event_address :'') . ($event_address2 != '' ? '<br />' . $event_address2 :'') . ($event_city != '' ? '<br />' . $event_city :'') . ($event_state != '' ? ', ' . $event_state :'') . ($event_zip != '' ? '<br />' . $event_zip :'') . ($event_country != '' ? '<br />' . $event_country :'');
			
			//Google map link creation
			$google_map_link = espresso_google_map_link(array( 'address'=>$event_address, 'city'=>$event_city, 'state'=>$event_state, 'zip'=>$event_zip, 'country'=>$event_country, 'text'=> 'Map and Directions', 'type'=> 'text') );
			
			//These variables can be used with other the espresso_countdown, espresso_countup, and espresso_duration functions and/or any javascript based functions.
			$start_timestamp = espresso_event_time($event_id, 'start_timestamp');
			$end_timestamp = espresso_event_time($event_id, 'end_timestamp');
			
			//This can be used in place of the registration link if you are usign the external URL feature
			$registration_url = $externalURL != '' ? $externalURL : get_option('siteurl') . '/?page_id='.$event_page_id.'&e_reg=register&event_id='. $event_id . '&name_of_event=' . stripslashes_deep($event_name);
		
			if (!is_user_logged_in() && get_option('events_members_active') == 'true' && $member_only == 'Y') {
				//Display a message if the user is not logged in.
				 //_e('Member Only Event. Please ','event_espresso') . event_espresso_user_login_link() . '.';
			}else{
			//Serve up the event list
			//As of version 3.0.17 the event lsit details have been moved to event_list_display.php
	            
		 		switch (event_espresso_get_status($event_id)){
						case 'NOT_ACTIVE':
							//Don't show the event if any of the above are true
						break;
						
						default:
						    /* skip secondary (waitlist) events */
						    $event_status = event_espresso_get_is_active($event_id);
						    if ($event_status['status'] == 'SECONDARY') {
						        break;
						    }						    
						    /* group recuring events */
						    $is_new_event_type = $last_recurrence_id == 0 || $last_recurrence_id != $recurrence_id;
    				        if ($is_new_event_type) :
    				            $events_type_index++;
                                $events_of_same_type[$events_type_index] = array();
                            endif;

    					    $event_data = array(
                                'event_id' => $event_id,
                                'event_page_id' => $event_page_id,
                                'event_name' => $event_name,
                                'event_desc' => $event_desc,
                                'start_date' => $start_date,
                                'end_date' => $end_date,
                                'reg_limit' => $reg_limit,
                                'registration_url' => $registration_url,
                                'overflow_event_id' => $overflow_event_id
                            );
    					    array_push($events_of_same_type[$events_type_index], $event_data);
    						$last_recurrence_id = $recurrence_id;
						    
						    // include('event_list_display.php');
						break;
				}
			} 
		}
		
		/* group recuring events */
		foreach ($events_of_same_type as $events_group) {
		    include('event_list_display.php');
	    }
		/* end group recuring events */
		
	//Check to see how many database queries were performed
	//echo '<p>Database Queries: ' . get_num_queries() .'</p>';
	}
}
