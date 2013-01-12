<?php
/*
Template Name: Ongoing Events List
Author: Seth Shoultes
Contact: support@eventespresso.com
Website: http://www.eventespresso.com
Description: This is a template file for displaying a list of normal and ongoing events on a page.
Shortcode: N/A
Requirements: custom_includes.php 
Notes: This file should be stored in your "/wp-content/uploads/espresso/templates/"
*/

//This block builds the default event list
if (!function_exists('display_all_events')) {
	function display_all_events(){
		//echo 'This page is located in ' . get_option( 'upload_path' );
		$sql = "SELECT e.* FROM ". EVENTS_DETAIL_TABLE . " e ";
		$sql .= " WHERE is_active = 'Y' ";//Makes sure event is active
		$sql .= " AND e.event_status != 'D' ";//Makes sure event is not deleted
		$sql .= " AND e.event_status != 'S' "; //Doesn't show secondary events
		//$sql .= " AND event_status = 'O' ";//Un-comment to only show ongoing events
		
		//Removing this line allows you to show events that may have expired
		//$sql .= " AND start_date >= '".date ( 'Y-m-d' )."' ";
		
		//These lines are used to show events within a registration start and end period
		$sql .= " AND e.registration_start <= '".date ( 'Y-m-d' )."' ";
		$sql .= " AND e.registration_end >= '".date ( 'Y-m-d' )."' ";
		
		//This line orders the events by date
		$sql .= " ORDER BY date(start_date), id";
		
		//This function outputs the event listings
		event_espresso_get_event_details($sql);
	}
}

//this block shows a list of events that are in a category
if (!function_exists('display_event_espresso_categories')) {
	function display_event_espresso_categories($event_category_id="null"){
		global $wpdb;
		if ($event_category_id != "null"){
			$sql = "SELECT e.*, c.category_name, c.category_desc, c.display_desc FROM ". EVENTS_DETAIL_TABLE . " e ";
			$sql .= " JOIN " . EVENTS_CATEGORY_REL_TABLE . " r ON r.event_id = e.id ";
			$sql .= " JOIN " . EVENTS_CATEGORY_TABLE . " c ON  c.id = r.cat_id ";
			$sql .= " WHERE c.category_identifier = '" . $event_category_id . "' ";
			$sql .= " AND is_active = 'Y' ";
			$sql .= " AND event_status != 'D' ";
			
			//Removing this line allows you to show events that may have expired
			//$sql .= " AND start_date >= '".date ( 'Y-m-d' )."' ";
			
			//These lines are used to show events within a registration start and end period
			$sql .= " AND e.registration_start <= '".date ( 'Y-m-d' )."' ";
			$sql .= " AND e.registration_end >= '".date ( 'Y-m-d' )."' ";
			
			//This line orders the events by date
			$sql .= " ORDER BY date(start_date), id ASC";
			
			//This function outputs the event listings
			event_espresso_get_event_details($sql);
		}
	}
}

//Events Listing - Shows the events on your page. 
if (!function_exists('event_espresso_get_event_details')) {
	function event_espresso_get_event_details($sql){
		event_espresso_session_start();
		if(!isset($_SESSION['event_espresso_sessionid'])){
			$sessionid = (mt_rand(100,999).time());
			$_SESSION['event_espresso_sessionid'] = $sessionid;
		}
		global $wpdb, $org_options;
		//echo 'This page is located in ' . get_option( 'upload_path' );
		$event_page_id = $org_options['event_page_id'];
		$currency_symbol = $org_options['currency_symbol'];
		$events = $wpdb->get_results($sql);
		
		$category_name = $wpdb->last_result[0]->category_name;
		$category_desc = $wpdb->last_result[0]->category_desc;
		$display_desc = $wpdb->last_result[0]->display_desc;
		
		if ($display_desc == 'Y'){
			echo '<p>' . stripslashes_deep($category_name) . '</p>';
			echo '<p>' . wpautop($category_desc) . '</p>';				
		}

		foreach ($events as $event){
			$event_id = $event->id;
			$event_name = $event->event_name;
			$event_identifier = $event->event_identifier;
			$active = $event->is_active;
			$registration_start = $event->registration_start;
			$registration_end = $event->registration_end;
			$start_date = $event->start_date;
			$reg_limit = $event->reg_limit;
			$event_address = $event->address;
			$member_only = $event->member_only;
			$externalURL = $event->externalURL;
			
			$registration_url = $externalURL != '' ? $externalURL : get_option('siteurl') . '/?page_id='.$event_page_id.'&regevent_action=register&event_id='. $event_id . '&name_of_event=' . stripslashes_deep($event_name);
		
			if (!is_user_logged_in() && get_option('events_members_active') == 'true' && $member_only == 'Y') {
				//Display a message if the user is not logged in.
				 //_e('Member Only Event. Please ','event_espresso') . event_espresso_user_login_link() . '.';
			}else if ( $start_date >= date ( 'Y-m-d' ) && $registration_start <= date ('Y-m-d') && $registration_end >= date ('Y-m-d') || $event->event_status == 'O'  && $registration_start <= date ('Y-m-d') ){?>  
				<div id="div_event_data-<?php echo $event_id?>" class="event_data">
				<h3 id="h3_event_title-<?php echo $event_id?>" class="event_title"><a id="a_event_title-<?php echo $event_id?>" class="a_event_title" href="<?php echo get_option('siteurl')?>/?page_id=<?php echo $event_page_id?>&regevent_action=register&event_id=<?php echo $event_id?>&name_of_event=<?php echo stripslashes_deep($event_name)?>"><?php echo stripslashes_deep($event_name)?> </a></h3>
				<p id="p_event_price-<?php echo $event_id?>" class="event_price">
				<?php echo __('Price: ','event_espresso') . event_espresso_get_price($event_id);?>
				</p>
											  
				<p id="p_event_date-<?php echo $event_id?>" class="event_date"><?php _e('Date:','event_espresso'); ?> <?php echo event_date_display($start_date)?></p>
				<p id="p_event_address-<?php echo $event_id?>" class="event_address"><?php _e('Address:','event_espresso'); ?> <?php echo $event_address; ?></p>
										  
				<p id="p_available_spaces-<?php echo $event_id?>" class="available_spaces"><?php _e('Available Spaces:','event_espresso')?> <?php echo get_number_of_attendees_reg_limit($event_id, 'available_spaces')?></p>
							   
					<p id="p_register_link-<?php echo $event_id?>" class="register_link">
					<a id="a_register_link-<?php echo $event_id?>" class="a_register_link" href="<?php echo get_option('siteurl')?>/?page_id=<?php echo $event_page_id?>&regevent_action=register&event_id=<?php echo $event_id?>&name_of_event=<?php echo stripslashes_deep($event_name)?>"><?php _e('Register Online','event_espresso'); ?></a>
					</p>
				</div>
	<?php
			} 
		}
	//Check to see how many database queries were performed
	//echo '<p>Database Queries: ' . get_num_queries() .'</p>';
	}
}