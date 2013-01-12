<?php
//This is a template file for displaying a list of events on a page. These functions are used with the {ESPRESSO_EVENTS} shortcode.
//This file should be stored in your wp-content/uploads/espresso/ folder.

//This is an group of functions for querying all of the events in your databse. 
//This file should be stored in your "/wp-content/uploads/espresso/templates/" directory.

//Note: All of these functions can be overridden using the "Custom Files" addon. The custom files addon also contains sample code to display ongoing events

if (!function_exists('display_all_events')) {
	function display_all_events(){
		//echo 'This page is located in ' . get_option( 'upload_path' );
		$sql = "SELECT e.* FROM ". EVENTS_DETAIL_TABLE . " e ";
		$sql .= " WHERE is_active = 'Y' ";
		$sql .= " AND event_status != 'D' ";
		$sql .= " AND event_status != 'S' ";
		$sql .= " AND start_date >= '".date ( 'Y-m-d' )."' ";
		$sql .= " AND e.registration_start <= '".date ( 'Y-m-d' )."' ";
		$sql .= " AND e.registration_end >= '".date ( 'Y-m-d' )."' ";
		$sql .= " ORDER BY date(start_date), id";
		event_espresso_get_event_details($sql);
	}
}

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
			$sql .= " AND event_status != 'S' ";
			$sql .= " AND start_date >= '".date ( 'Y-m-d' )."' ";
			$sql .= " AND e.registration_start <= '".date ( 'Y-m-d' )."' ";
			$sql .= " AND e.registration_end >= '".date ( 'Y-m-d' )."' ";
			$sql .= " ORDER BY date(start_date), id ASC";
			event_espresso_get_event_details($sql);
		}
	}
}

//Events Listing - Shows the events on your page. 
if (!function_exists('event_espresso_get_event_details')) {
	function event_espresso_get_event_details($sql){
		//print_r( $_SESSION['event_espresso_sessionid']); //See if the session already exists
		global $wpdb, $org_options;
		//echo 'This page is located in ' . get_option( 'upload_path' );
		$event_page_id = $org_options['event_page_id'];
		$currency_symbol = $org_options['currency_symbol'];
		$events = $wpdb->get_results($sql);
		$category_id = $wpdb->last_result[0]->id;
		$category_name = $wpdb->last_result[0]->category_name;
		$category_desc = html_entity_decode( wpautop($wpdb->last_result[0]->category_desc) );
		$display_desc = $wpdb->last_result[0]->display_desc;
		
		if ($display_desc == 'Y'){
			echo '<p id="events_category_name-'. $category_id . '" class="events_category_name">' . stripslashes_deep($category_name) . '</p>';
			echo wpautop($category_desc);				
		}
		
		foreach ($events as $event){
			$event_id = $event->id;
			$event_name = $event->event_name;
			$event_identifier = $event->event_identifier;
			$active = $event->is_active;
			$registration_start = $event->registration_start;
			$registration_end = $event->registration_end;
			$start_date = $event->start_date;
			$end_date = $event->end_date;
			$reg_limit = $event->reg_limit;
			$event_address = $event->address;
			$member_only = $event->member_only;
			$externalURL = $event->externalURL;
			
			$allow_overflow = $event->allow_overflow;
			$overflow_event_id = $event->overflow_event_id;
			
			$registration_url = $externalURL != '' ? $externalURL : get_option('siteurl') . '/?page_id='.$event_page_id.'&regevent_action=register&event_id='. $event_id . '&name_of_event=' . stripslashes_deep($event_name);
		
			if (!is_user_logged_in() && get_option('events_members_active') == 'true' && $member_only == 'Y') {
				//Display a message if the user is not logged in.
				 //_e('Member Only Event. Please ','event_espresso') . event_espresso_user_login_link() . '.';
			}else{?>  
				<div id="event_data-<?php echo $event_id?>" class="event_data">
				<h3 id="event_title-<?php echo $event_id?>" class="event_title"><a title="<?php echo stripslashes_deep($event_name)?>" class="a_event_title" id="a_event_title-<?php echo $event_id?>" href="<?php echo get_option('siteurl')?>/?page_id=<?php echo $event_page_id?>&regevent_action=register&event_id=<?php echo $event_id?>&name_of_event=<?php echo stripslashes_deep($event_name)?>"><?php echo stripslashes_deep($event_name)?></a></h3>
				<p id="p_event_price-<?php echo $event_id?>">
				<?php echo __('Price: ','event_espresso') . event_espresso_get_price($event_id);?>
				</p>
				
                <p id="event_date-<?php echo $event_id?>"><?php _e('Start Date:','event_espresso'); ?>  <?php echo event_date_display($start_date)?> <br /> <?php _e('End Date:','event_espresso'); ?> <?php echo event_date_display($end_date)?></p>

				<p id="event_address-<?php echo $event_id?>"><?php _e('Address:','event_espresso'); ?> <?php echo $event_address; ?></p>
<?php 
/*
* Display the amount of attendees and/or registration limit
Available parameters for the get_number_of_attendees_reg_limit() function
*  @ $event_id - required
*  @ $type - 
*	available_spaces = returns the number of available spaces
*	num_attendees = returns the number of attendees
*	reg_limit = returns the total number of spaces
*	num_incomplete = returns the number of incomplete (non paid) registrations
*	num_completed = returns the number of completed (paid) registrations
*	num_completed_slash_incomplete = returns the number of completed and incomplete registrations separated by a slash (eg. 3/1)
*		num_attendees_slash_reg_limit = returns the number of attendees and the registration limit separated by a slash (eg. 4/30)
*/
	$num_attendees = get_number_of_attendees_reg_limit($event_id, 'num_attendees');//Get the number of attendees
	if ($num_attendees >= $reg_limit  ){?>
				<p id="available_spaces-<?php echo $event_id?>"><?php _e('Available Spaces:','event_espresso')?> <?php echo get_number_of_attendees_reg_limit($event_id, 'available_spaces')?></p>
							   
				<p id="register_link-<?php echo $overflow_event_id?>"><a class="a_register_link" id="a_register_link-<?php echo $overflow_event_id?>" href="<?php echo get_option('siteurl')?>/?page_id=<?php echo $event_page_id?>&regevent_action=register&event_id=<?php echo $overflow_event_id?>&name_of_event=<?php echo stripslashes_deep($event_name)?>" title="<?php echo stripslashes_deep($event_name)?>"><?php _e('Join Waiting List','event_espresso'); ?></a></p> 
				</div>
<?php
	}else{
?>
		<p id="available_spaces-<?php echo $event_id?>"><?php _e('Available Spaces:','event_espresso')?> <?php echo get_number_of_attendees_reg_limit($event_id, 'available_spaces')?></p>
							   
				<p id="register_link-<?php echo $event_id?>"><a class="a_register_link" id="a_register_link-<?php echo $event_id?>" href="<?php echo get_option('siteurl')?>/?page_id=<?php echo $event_page_id?>&regevent_action=register&event_id=<?php echo $event_id?>&name_of_event=<?php echo stripslashes_deep($event_name)?>" title="<?php echo stripslashes_deep($event_name)?>"><?php _e('Register Online','event_espresso'); ?></a></p> 
				<p><?php echo espresso_show_social_media($event_id, 'twitter');?> <?php echo espresso_show_social_media($event_id, 'facebook');?></p>
				</div>
<?php
	}
	
			} 
		}
	//Check to see how many database queries were performed
	//echo '<p>Database Queries: ' . get_num_queries() .'</p>';
	}
}
