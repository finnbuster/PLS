<?php
/*
Shortcode Name: Espresso Table
Author: Seth Shoultes
Contact: 
Website: http://www.eventespresso.com
Description: Displays a movie listing like table. Allows you to show events in a CATEGORY within a certain number number of days into the future and a qty. The example below only shows events in a certain category that start within 30 days from the current date.
Usage Example: [ESPRESSO_MOVIE_TABLE max_days="30" qty="3" category_id="gracecard"]
Custom CSS for the table display
Notes: This file should be stored in your "/wp-content/uploads/espresso/templates/" folder and you should have the custom_includes.php files installed in your "/wp-content/uploads/espresso/" directory.
*/
function espresso_display_movie_table($max_days="", $qty="10", $category_id="NULL"){
 	global $wpdb;
	//$org_options = get_option('events_organization_settings');
	//$event_page_id =$org_options['event_page_id'];
	//testing if we got the cat and day filters (it works)
	/*echo $event_category_id;
	echo $max_days;
	echo $qty;*/
    
    $sql  = "SELECT e.*, c.category_name, c.category_desc, c.display_desc, ese.start_time, ese.end_time FROM ". EVENTS_DETAIL_TABLE . " e ";
    $sql  .= " JOIN " . EVENTS_START_END_TABLE . " ese ON ese.event_id = e.id ";
    $sql  .= " JOIN " . EVENTS_CATEGORY_REL_TABLE . " r ON r.event_id = e.id ";
    $sql  .= " JOIN " . EVENTS_CATEGORY_TABLE . " c ON  c.id = r.cat_id ";
    $sql  .= " WHERE e.is_active = 'Y' ";
	$sql  .= " AND event_status != 'D' ";
	$sql  .= " AND event_status != 'S' ";
	$sql  .= " AND  c.category_identifier = '" . $category_id . "' ";
	if ($max_days != ""){
		$sql  .= " AND ADDDATE('".date ( 'Y-m-d' )."', INTERVAL ".$max_days." DAY) >= e.start_date AND e.start_date >= '".date ( 'Y-m-d' )."' ";
  	}
    $sql  .= " ORDER BY date(e.start_date), ese.start_time ";
	$sql  .= " LIMIT 0," . $qty;
    
    ob_start();
    espresso_get_movie_tabe($sql);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
     
}
//Events Custom Table Listing - Shows the events on your page in matching table.
function espresso_get_movie_tabe($sql){
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
    echo '<p>' . stripslashes_deep($category_desc) . '</p>';        
  }
?>
<table class="espresso-table">
  
      <tr>
          <th id="th-group"><?php _e('City','event_espresso'); ?></th>
          <th id="th-group"><?php _e('State','event_espresso'); ?></th>
          <th id="th-group"><?php _e('Location','event_espresso'); ?></th>
          <th id="th-group"><?php _e('Date','event_espresso'); ?></th>
          <th id="th-group"><?php _e('Time','event_espresso'); ?></th>
          <th id="th-group"><?php _e('Tickets','event_espresso'); ?></th>
      </tr>

      <?php
      
      foreach ($events as $event){
	      $reg_limit = $event->reg_limit;
      
    
    $event_desc = wpautop($event->event_desc);
    
    $live_button = '<a id="a_register_link-'.$event->id.'" href="'.get_option('siteurl').'/?page_id='.$event_page_id.'&regevent_action=register&event_id='.$event->id.'&name_of_event='.stripslashes_deep($event->event_name).'">Buy Now</a>';
    
	//Check to see how many open spots are available
    $open_spots = get_number_of_attendees_reg_limit($event->id, 'available_spaces') == 'Unlimited' ? 999 : get_number_of_attendees_reg_limit($event->id, 'available_spaces');
	//echo $open_spots;
	
    if ( $open_spots < 1 ) { $live_button = 'Closed';  }
    
      ?>
      <tr class="">
       	<td class="td-group">
            <?php echo $event->city ?>
          </td>
      	  <td class="td-group">
            <?php echo $event->state ?>
          </td>
          <td class="td-group">
            <?php echo $event->address ?>
          </td>
          <td class="td-group">
              <?php echo event_date_display($event->start_date, $format = 'M d Y') ?>
          </td>
          <td class="td-group">
              <?php echo $event->start_time ?>
          </td>
         
          <td class="td-group">
              <?php echo $live_button ?>
          </td>
      </tr>
      <?php } //close foreach ?>

</table>

<?php
}