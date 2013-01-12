<?php
/*
Shortcode Name: Espresso Table
Author: Seth Shoultes
Contact: seth@eventespresso.com
Website: http://www.eventespresso.com
Description: Only show events in a CATEGORY within a certain number number of days into the future and a qty. The example below only shows events in a certain category that start within 30 days from the current date.
Usage Example: [ESPRESSO_TABLE max_days="30" qty="3" category_id="gracecard" order_by="state"]
Custom CSS for the table display
Notes: This file should be stored in your "/wp-content/uploads/espresso/templates/" folder and you should have the custom_includes.php files installed in your "/wp-content/uploads/espresso/" directory.
*/
function espresso_display_table($atts){
 	global $wpdb;
	$org_options = get_option('events_organization_settings');
	$event_page_id =$org_options['event_page_id'];

	global $load_espresso_scripts;	
		$load_espresso_scripts = true;//This tells the plugin to load the required scripts
		extract(shortcode_atts(array('event_category_id'=>'NULL','category_identifier' => 'NULL','show_expired' => 'false', 'show_secondary'=>'false','show_deleted'=>'false','show_recurrence'=>'true', 'limit' => '0', 'order_by' => 'NULL', 'max_days'=>''),$atts));		
		
		if ($category_identifier != 'NULL'){
			$type = 'category';
		}
		
		$show_expired = $show_expired == 'false' ? " AND e.start_date >= '".date ( 'Y-m-d' )."' " : '';
		$show_secondary = $show_secondary == 'false' ? " AND e.event_status != 'S' " : '';
		$show_deleted = $show_deleted == 'false' ? " AND e.event_status != 'D' " : '';
		$show_recurrence = $show_recurrence == 'false' ? " AND e.recurrence_id = '0' " : '';
		$limit = $limit > 0 ? " LIMIT 0," . $limit . " " : '';
		$order_by = $order_by != 'NULL'? " ORDER BY ". $order_by ." ASC " : " ORDER BY date(start_date), id ASC ";
		
		if ($type == 'category'){
			$sql = "SELECT e.* FROM " . EVENTS_CATEGORY_TABLE . " c ";
			$sql .= " JOIN " . EVENTS_CATEGORY_REL_TABLE . " r ON r.cat_id = c.id ";
			$sql .= " JOIN " . EVENTS_DETAIL_TABLE . " e ON e.id = r.event_id ";
			$sql .= " WHERE c.category_identifier = '" . $category_identifier . "' ";
			$sql .= " AND e.is_active = 'Y' ";
		}else{
			$sql = "SELECT e.* FROM " . EVENTS_DETAIL_TABLE . " e ";
			$sql .= " WHERE e.is_active = 'Y' ";
		}
		if ($max_days != ""){
				$sql  .= " AND ADDDATE('".date ( 'Y-m-d' )."', INTERVAL ".$max_days." DAY) >= e.start_date AND e.start_date >= '".date ( 'Y-m-d' )."' ";
		}
		$sql .= $show_expired;
		$sql .= $show_secondary;
		$sql .= $show_deleted;
		$sql .= $show_recurrence;
		$sql .= $order_by;
		$sql .= $limit;
		
		echo espresso_get_table($sql);
	
}
//Events Custom Table Listing - Shows the events on your page in matching table.
function espresso_get_table($sql){
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
<table class="espresso-table" width="100%">
  
      <thead class="espresso-table-header-row">
      <tr>
          <th class="th-group"><?php _e('Course','event_espresso'); ?></th>
          <th class="th-group"><?php _e('Location','event_espresso'); ?></th>
          <th class="th-group"><?php _e('City','event_espresso'); ?></th>
          <th class="th-group"><?php _e('State','event_espresso'); ?></th>
          <th class="th-group"><?php _e('Date','event_espresso'); ?></th>
          <th class="th-group"><?php _e('Time','event_espresso'); ?></th>
          <th class="th-group"><?php _e('','event_espresso'); ?></th>
     </tr>
      </thead>
	<tbody>

      <?php
      
      foreach ($events as $event){
	      $reg_limit = $event->reg_limit;
      
    
    $event_desc = wpautop($event->event_desc);
    
    $register_button = '<a id="a_register_link-'.$event->id.'" href="'.get_option('siteurl').'/?page_id='.$event_page_id.'&regevent_action=register&event_id='.$event->id.'&name_of_event='.stripslashes_deep($event->event_name).'">Register</a>';
    
	//Check to see how many open spots are available
    $open_spots = get_number_of_attendees_reg_limit($event->id, 'available_spaces') == 'Unlimited' ? 999 : get_number_of_attendees_reg_limit($event->id, 'available_spaces');
	//echo $open_spots;
	
    if ( $open_spots < 1 ) { $live_button = 'Closed';  }
    
      ?>
      <tr class="espresso-table-row">
       	<td class="td-group">
            <?php echo $event->event_name ?>
          </td>	
          <td class="td-group">
            <?php echo $event->address ?>
          </td>
          <td class="td-group">
            <?php echo $event->city ?>
          </td>
      	  <td class="td-group">
            <?php echo $event->state ?>
          </td>
          <td class="td-group">
              <?php echo event_date_display($event->start_date, $format = 'l, M d, Y') ?>
          </td>
          <td class="td-group">
              <?php echo espresso_event_time($event->id, 'start_time', get_option('time_format')) ?>
          </td>
         
          <td class="td-group">
              <?php echo $register_button ?>
          </td>
      </tr>
      <?php } //close foreach ?>
</tbody>
</table>

<?php
}