<?php
/*
Template Name: Table Display for Events
Author: Leland Zaremba
Contact: 
Website: 
Description: This is a template file for displaying a list of events displayed in a table for a maximum number of days from a category.
Shortcode: [EVENT_CAT_DATE_MAX_DAYS max_days="30" event_category_id="sailing3"]
Requirements: Custom CSS, rename table fields, Events Members Addon
Notes: This file should be stored in your "/wp-content/uploads/espresso/templates/" folder and you should have downloaded the custom_shortcodes.php and custom_includes.php files from shoultes.net.
*/
function display_event_espresso_cat_date_max($max_days="null", $event_category_id="null"){
  global $wpdb;
  //$org_options = get_option('events_organization_settings');
  //$event_page_id =$org_options['event_page_id'];
  if ($max_days != "null" && $event_category_id != "null" ){
    if ($_REQUEST['show_date_max'] == '1'){
      foreach ($_REQUEST as $k=>$v) $$k=$v;
    }
    //testing if we got the cat and day filters (it works)
    //echo $event_category_id;
    //echo $max_days;
    
    $max_days = $max_days;
    
    $sql  = "SELECT e.*, c.category_name, c.category_desc, c.display_desc, ese.start_time FROM ". EVENTS_DETAIL_TABLE . " e
    JOIN " . EVENTS_START_END_TABLE . " ese ON ese.event_id = e.id
    JOIN " . EVENTS_CATEGORY_REL_TABLE . " r ON r.event_id = e.id
    JOIN " . EVENTS_CATEGORY_TABLE . " c ON  c.id = r.cat_id
    WHERE ADDDATE('".date ( 'Y-m-d' )."', INTERVAL ".$max_days." DAY) >= e.start_date AND e.start_date >= '".date ( 'Y-m-d' )."'
    AND e.is_active = 'Y'
    AND  c.category_identifier = '" . $event_category_id . "'
    ORDER BY date(e.start_date), ese.start_time";
    
    event_espresso_get_event_list_table($sql);

  }        
}
//Events Custom Table Listing - Shows the events on your page in matching table.
function event_espresso_get_event_list_table($sql){
  event_espresso_session_start();
  if(!isset($_SESSION['event_espresso_sessionid'])){
    $sessionid = (mt_rand(100,999).time());
    $_SESSION['event_espresso_sessionid'] = $sessionid;
  }
  //print_r( $_SESSION['event_espresso_sessionid']); //See if the session already exists
  global $wpdb;
  //echo 'This page is located in ' . get_option( 'upload_path' );
  $org_options = get_option('events_organization_settings');
  $event_page_id = $org_options['event_page_id'];
  $currency_symbol = $org_options['currency_symbol'];
  $events = $wpdb->get_results($sql);
  
  $category_name = $wpdb->last_result[0]->category_name;
  $category_desc = $wpdb->last_result[0]->category_desc;
  $display_desc = $wpdb->last_result[0]->display_desc;
  
  if ($display_desc == 'Y'){
    echo '<p>' . htmlspecialchars_decode($category_name) . '</p>';
    echo '<p>' . htmlspecialchars_decode($category_desc) . '</p>';        
  }
  
  //If the members addon is installed, get the users information if available  
  if (file_exists(EVENT_ESPRESSO_UPLOAD_DIR . "members/member_functions.php")){
    if (get_option('events_members_active') == 'true'){
      require_once(EVENT_ESPRESSO_MEMBERS_DIR . "member_functions.php"); //Load Members functions
    }
  }

if (!is_user_logged_in() && get_option('events_members_active') == 'true' && $member_only == 'Y') {
          //Display a message if the user is not logged in.
           //_e('Member Only Event. Please ','event_espresso') . event_espresso_user_login_link() . '.';
}else{
?>

<div class="pagination">
Viewing all member only reservation times for the next 10 days.
</div>

<table class="forum">
  
      <tr>
          <th id="th-group"><?php _e('Event Date','event_espresso'); ?></th>
          <th id="th-group"><?php _e('Start Time','event_espresso'); ?></th>
          <th id="th-group"><?php _e('Description','event_espresso'); ?></th>
          <th id="th-group"><?php _e('Open Spots','event_espresso'); ?></th>
          <th id="th-group"><?php _e('Register Link','event_espresso'); ?></th>
          <th id="th-group"><?php _e('Boat Captain?','event_espresso'); ?></th>
      </tr>

      <?php
      
      foreach ($events as $event){
      $event_id = $event->id;
      $event_name = $event->event_name;
      $event_desc = $event->event_desc;
      $event_identifier = $event->event_identifier;
      $active = $event->is_active;
      $start_date = $event->start_date;
      $start_time = $event->start_time;
      $reg_limit = $event->reg_limit;
      $event_address = $event->address;
      $member_only = $event->member_only;
    
    $event_desc = strip_tags(html_entity_decode($event_desc));
    
    $live_button = '<a id="a_register_link-'.$event_id.'" href="'.get_option('siteurl').'/?page_id='.$event_page_id.'&regevent_action=register&event_id='.$event_id.'&name_of_event='.stripslashes($event_name).'">Reserve Spot</a>';
    
    $open_spots = get_number_of_attendees_reg_limit($event_id, 'available_spaces');
    
    if ( $open_spots < 1 ) { $live_button = 'Closed';  }
    
      ?>
      <tr class="">
          <td class="td-group">
            <?php echo event_date_display($start_date) ?>
          </td>
          <td class="td-group">
              <?php echo $start_time ?>
          </td>
          <td class="td-group">
              <?php echo $event_desc ?>
          </td>
          <td class="td-group">
              <?php echo $open_spots ?>
          </td>
          <td class="td-group">
              <?php echo $live_button ?>
          </td>
          <td class="td-group">
              No Captain yet
          </td>
      </tr>
      <?php } //close foreach ?>

</table>

<?php
// close is_user_logged_in  
	}
}