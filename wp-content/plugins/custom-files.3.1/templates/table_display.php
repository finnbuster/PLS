<?php
/*
Template Name: Events Table Display
Author: Seth Shoultes
Contact: support@eventespresso.com
Website: http://www.eventespresso.com
Description: This is a template file for displaying a list of events displayed in a table.
Shortcode: [EVENT_TABLE_DISPLAY]
Requirements: 
Notes: This file should be stored in your "/wp-content/uploads/espresso/templates/" directory and you should have downloaded the custom_shortcodes.php file from shoultes.net.
*/

function display_event_espresso_table(){
	global $wpdb;
	$org_options = get_option('events_organization_settings');
	$event_page_id =$org_options['event_page_id'];
	$curdate = date("Y-m-d");
?>
<form id="event_espresso_table" name="event_espresso_table" method="post" action="<?php echo get_option('siteurl')?>/?page_id=<?php echo $event_page_id?>&regevent_action=register">
  <table width="100%" border="0">
    <thead align="left">
		<th><?php _e('Location','event_espresso'); ?></th>
		<th><?php _e('Date','event_espresso'); ?></th>
		<th><?php _e('Times','event_espresso'); ?></th>
	</thead>
	<tbody>
<?php
	
	$sql = "SELECT e.* FROM ". EVENTS_DETAIL_TABLE . " e ";
	$sql .= " WHERE is_active = 'Y' ";//Makes sure event is active
	$sql .= " AND event_status != 'D' ";//Makes sure event is not deleted
	//$sql .= " AND event_status = 'O' ";//Un-comment to only show ongoing events
					
	//Removing this line keeps events from showing that may be expired
	$sql .= " AND start_date >= '".date ( 'Y-m-d' )."' ";
					
	//These lines are used to show events within a registration start and end period
	$sql .= " AND e.registration_start <= '".date ( 'Y-m-d' )."' ";
	$sql .= " AND e.registration_end >= '".date ( 'Y-m-d' )."' ";
					
	//This line orders the events by date
	$sql .= " ORDER BY date(start_date), id";
	
	$results = $wpdb->get_results($sql);
	if ($wpdb->num_rows > 0) {
		foreach ($results as $result){
			$event_id= $result->id;
			$event_name=stripslashes($result->event_name);
			$event_identifier=stripslashes($result->event_identifier);
			$address = $result->address;
			$reg_limit = $result->reg_limit;
			$start_date = $result->start_date;
			$end_date = $result->end_date;
			$is_active= $result->is_active;
?>			
	<tr>
      <td align="left" valign="top">
      <p class="event_title"><?php echo $event_name?></p>
        
           <p><?php _e('Address:','event_espresso'); ?> <br />
		   <?php echo $address?>
          <br />
          <?php _e('Phone:','event_espresso'); ?> <?php echo $phone?>
        </p></td>
      <td align="left" valign="top"><p>
          <?php echo event_date_display($start_date, 'l, M d, Y')?>
        </p></td>
          <td valign="top" align="left">
            <?php 
            $event_times = $wpdb->get_results("SELECT * FROM " . EVENTS_START_END_TABLE . " WHERE event_id='".$event_id."'");
                foreach ($event_times as $time){
					$start_time = $time->start_time;
					$time_id = $time->id;
    ?>
            <p><label>
              <input type="radio" name="event_id_time" value="<?php echo $event_id?>|<?php echo $start_time?>|<?php echo $time_id?>" id="event_id_<?php echo $event_id?>_<?php echo $time_id?>" />
              <?php echo $start_time?>
              </label>
            </p>
<?php 	}?>
		</td>
    </tr>
<?php	
		}//End for each event details
		
	}else{
?>			
	<tr><h3 class="expired_event"><?php _e('Sorry, there are no events here.','event_espresso'); ?></h3></tr>
<?php	
}	?>

    </tbody>
   </table>
   <input name="Submit" type="submit" value="Register" />
  </form>
<?php //$myrole = event_espresso_get_current_user_role();
//echo $myrole;?>
<?php
}