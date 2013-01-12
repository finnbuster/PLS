<?php
/*
Template Name: Date Range Display
Author: Seth Shoultes
Contact: support@eventespresso.com
Website: http://www.eventespresso.com
Description: This shortcode displays events in a table format and allows registrants to choose an event within a certain date range. An example of use would be having this on a post that is set to display in the future. You must use dates that are within a calendar period. It is expiremental at the moment, use at your own risk.  I would love to hear your feedback on this.
Shortcode: [EVENT_DATE_RANGE date_1="2009-12-22" date_2="2009-12-31"]
Requirements: 
Notes: This file should be stored in your "/wp-content/uploads/espresso/templates/" directory and you should have downloaded the custom_shortcodes.php file from shoultes.net.
*/
function display_event_espresso_date_range($date_1="null", $date_2="null"){
				global $wpdb;
				$org_options = get_option('events_organization_settings');
				$event_page_id =$org_options['event_page_id'];
				
				if ($date_1 != "null" && $date_2 != "null"){
					if ($_REQUEST['show_date_range'] == '1'){
						foreach ($_REQUEST as $k=>$v) $$k=$v;
					}
					$date_1 = $date_1;
					$date_2 = $date_2;
					$sql  = "SELECT * FROM " . EVENTS_DETAIL_TABLE . " WHERE start_date BETWEEN DATE('".$date_1."') AND DATE('".$date_2."') AND start_date >= '".date ( 'Y-m-d' )."' ORDER BY date(start_date)";
					$result = mysql_query($sql);
						
					//echo $sql;
					?>

                <form id="form1" name="form1" method="post" action="<?php echo get_option('siteurl')?>/?page_id=<?php echo $event_page_id?>&regevent_action=register">
                  <table width="100%" border="0">
                    <thead align="left">
                    <th>Date</th>
                      <th>Location</th>
                      <th>Times</th>
                      </thead>
                    <tbody>
                      <?php
	while ($row = mysql_fetch_assoc ($result)){
	$event_id = $row['id'];
	//$category_name = $row['category_name'];
	$sql = "SELECT * FROM ". EVENTS_DETAIL_TABLE . " WHERE id = '".$event_id."'";
	$result = mysql_query ($sql);
	while ($row = mysql_fetch_assoc ($result)){
		foreach ($row as $k=>$v) $$k=$v;
										
			$sql2= "SELECT SUM(quantity) FROM " . EVENTS_ATTENDEE_TABLE . " WHERE event_id='$event_id'";
			$result2 = mysql_query($sql2);
								
										while($row = mysql_fetch_array($result2)){
											$num_attendees =  $row['SUM(quantity)'];
										}
										
										if ($reg_limit != ""){
											
											if ($reg_limit > $num_attendees){
												$available_spaces = $reg_limit - $num_attendees;
											}else if ($reg_limit <= $num_attendees){
												$available_spaces = '<span style="color: #F00; font-weight:bold;">'.__('Event Full','event_espresso').'</span>';
											}
										}
										
										if ($reg_limit == "" || $reg_limit == " " || $reg_limit == "999"){$available_spaces = __('Unlimited','event_espresso');}
		
		?>

                                <tr>
                                  <td align="left" valign="top"><p>
                                      <?php echo event_date_display($start_date, 'l, M d, Y')?>
                                    </p></td>
                                  <td align="left" valign="top"><p><span class="event_title"><strong>
                                      <?php echo $event_name?>
                                      </strong></span><br />
                                      <?php echo $address?>
                                      <br />
                                      <?php echo $city?>, <?php echo $state?> <?php echo $zip?>
                                      <br />
                                      <?php echo $phone?>
                                    </p></td>
                                  <td valign="top" align="left">
<?php 
	$sql3= "SELECT * FROM " . EVENTS_START_END_TABLE . " WHERE event_id='".$id."'";
	//echo $sql3;
	$result3 = mysql_query($sql3);
	while($row3 = mysql_fetch_array($result3)){
		$start_time =  $row3['start_time'];
		$start_time_id =  $row3['id'];
?>
                                    <p><label>
                                      <input type="radio" name="event_id_time" value="<?php echo $id?>|<?php echo $start_time?>|<?php echo $start_time_id?>" id="event_id_<?php echo $id?>-<?php echo $start_time?>" />
                                      <?php echo $start_time?>
                                      </label>
                                    </p>
                                    <?php }?></td>
                                </tr>

<?php
								}
                                    }
                                    ?>
                    </tbody>
                  </table>
                  <input name="Submit" type="submit" value="Register" />
                </form>
<?php
				}				
}