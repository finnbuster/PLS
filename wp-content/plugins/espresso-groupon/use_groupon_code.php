<?php
// check for groupon 
if ( ! function_exists( 'event_espresso_process_groupon' )) {
	function event_espresso_process_groupon( $event_id, $event_cost, $mer ) {
	
		do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');		

		$use_groupon_code = isset( $_POST['use_groupon'][$event_id] ) ? $_POST['use_groupon'][$event_id] : 'N';				

		if ( $mer ) {
			if ( isset( $_SESSION['espresso_session']['events_in_session'][$event_id]['groupon'] )) {
				$groupon_code = isset( $_SESSION['espresso_session']['events_in_session'][$event_id]['groupon']['code'] ) ? wp_strip_all_tags( $_SESSION['espresso_session']['events_in_session'][$event_id]['groupon']['code'] ) : FALSE;
				$use_groupon_code = $groupon_code ? 'Y' : 'N';
			}
			
		} else {
			$groupon_code = isset( $_POST['event_espresso_groupon_code'] ) ? wp_strip_all_tags( $_POST['event_espresso_groupon_code'] ) : '';
		}

		return event_espresso_groupon_payment_page( $event_id, $event_cost, $mer, $use_groupon_code );

	}
}



if ( ! function_exists( 'event_espresso_groupon_payment_page' )) {
	function event_espresso_groupon_payment_page( $event_id = FALSE, $event_cost = 0.00, $mer = TRUE, $use_groupon_code = 'N' ) {
	
		do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');		
 
		global $espresso_premium,$org_options;		
		if ( ! $espresso_premium ) {
			return FALSE;
		}

		$event_cost = (float)$event_cost;

		$groupon_code = isset( $_POST['event_espresso_groupon_code'] ) ? wp_strip_all_tags( $_POST['event_espresso_groupon_code'] ) : FALSE;
		//echo '<h4>$groupon_code : ' . $groupon_code . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
		if ( $groupon_code === FALSE && isset( $_SESSION['espresso_session']['events_in_session'][ $event_id ]['groupon'] ) ) {
			$groupon_code = isset( $_SESSION['espresso_session']['events_in_session'][ $event_id ]['groupon']['code'] ) ? wp_strip_all_tags( $_SESSION['espresso_session']['events_in_session'][ $event_id ]['groupon']['code'] ) : FALSE;
		}
//		echo '<h4>$groupon_code : ' . $groupon_code . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
		if ( ! $use_groupon_code ) {
			$use_groupon_code = isset( $_POST['use_groupon'][$event_id] ) ? $_POST['use_groupon'][$event_id] : 'N';			
		}
//		echo '<h4>$event_id : ' . $event_id . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
//		echo '<h4>$use_groupon_code : ' . $use_groupon_code . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
		
		if ( $use_groupon_code == 'Y' && $event_cost > 0 ) {
			if ( $groupon_code ){
				$msg = '';
				$error = '';
				$event_id = absint( $event_id );
				$groupon_id = FALSE;
				$groupon_status = FALSE;
						
				
				if ( isset( $_SESSION['espresso_session']['events_in_session'][ $event_id ] ) && isset( $_SESSION['espresso_session']['events_in_session'][ $event_id ]['groupon']['code'] )) {
//					printr( $_SESSION['espresso_session']['events_in_session'][ $event_id ]['groupon'], '$_SESSION groupon <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );
					// check if groupon has already been added to session
					if ( $_SESSION['espresso_session']['events_in_session'][ $event_id ]['groupon']['code'] == $groupon_code ) {
						// grab values from session
						$groupon = $_SESSION['espresso_session']['events_in_session'][ $event_id ]['groupon'];
						//printr( $groupon, '$groupon  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );
	                	$valid = TRUE;
						$groupon_id = $groupon['id'];
						$groupon_code = $groupon['code'];
						$groupon_status = $groupon['status'];
						$groupon_holder = $groupon['holder'];		

					}
					
				} else {

					global $wpdb;
					// lookup groupon details in db
					$SQL = "SELECT * FROM " . EVENTS_GROUPON_CODES_TABLE ;
					$SQL .= " WHERE  groupon_code = %s";
					$SQL .= " AND ( event_id = 0 OR event_id = %d )";
									
					if ( $groupon = $wpdb->get_row( $wpdb->prepare( $SQL, $groupon_code, $event_id ))) {	
						$valid = $groupon->groupon_status;;
						$groupon_id = $groupon->id;
						$groupon_code = $groupon->groupon_code;
						$groupon_status = $groupon->groupon_status;
						$groupon_holder = $groupon->groupon_holder;	
					}
					//printr( $groupon, '$groupon  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );
					
				}
				
//				echo '<h4>$groupon_id : ' . $groupon_id . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
//				echo '<h4>$groupon_status : ' . $groupon_status . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
		 
				if ( $groupon_id && $groupon_status ) {
					$event_cost = 0.00;
					if ( $mer ) {
						$groupon_details = array();					
						$groupon_details['id'] = $groupon_id;
						$groupon_details['code'] = $groupon_code;
						$groupon_details['status'] = $groupon_status;
						$groupon_details['holder'] = $groupon_holder;
						$groupon_details['discount'] = $event_cost;
						$_SESSION['espresso_session']['events_in_session'][ $event_id ]['groupon'] = $groupon_details;
						$_SESSION['espresso_session']['events_in_session'][ $event_id ]['use_groupon'][ $event_id ] = 'Y';
						
						$msg = '<p id="event_espresso_valid_groupon" style="margin:0;">';
						$msg .= '<strong>' . __('Voucher code ','event_espresso') . $groupon_code . '</strong>' . __(' purchased by ','event_espresso').$groupon_holder.'<br/>';
	          		    $msg .= __('has being successfully applied to the following events', 'event_espresso') . ':<br/>';
						
					} else {

						$msg = '<p id="event_espresso_valid_groupon" style="margin:0;">';
						$msg .= '<strong>' . __('Voucher code ','event_espresso') . $groupon_code . '</strong>' . __(' purchased by ','event_espresso').$groupon_holder.'<br/>';
	          		    $msg .= __('has being successfully applied to your registration', 'event_espresso');
	          		    $msg .= '</p>';
						
					}							

	            } else {
					
					$valid = FALSE;
					if ( $mer ) {
					
						$error =  '<p id="event_espresso_invalid_groupon" style="margin:0;color:red;">'.__('Sorry, voucher code ', 'event_espresso') . '<strong>' . $groupon_code . '</strong>' . __(' is either invalid, expired, has already been used, or can not be used for the event(s) you are applying it to.','event_espresso'). '</p>';
				
					} else {
					
						$msg = '<p id="event_espresso_invalid_groupon" style="margin:0;color:red;">';
						$msg .= __('Sorry, voucher code ', 'event_espresso') . '<strong>' . $groupon_code . '</strong>' . __(' is either invalid, expired, has already been used, or can not be used for the event(s) you are applying it to.','event_espresso');
	          		    $msg .= '</p>';
						
					}
					
	            }
//				printr( $_SESSION, '$_SESSION  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );
				return array( 'event_cost'=>$event_cost, 'valid'=>$valid, 'msg' => $msg, 'error' => $error, 'code' => $groupon_code );

			}
        }

		return FALSE;		
 
   }
}




function espresso_update_groupon( $primary_attendee_id = FALSE, $groupon_code = FALSE ) {

	if ( ! $primary_attendee_id || ! $groupon_code ) {
		return FALSE;
	}
	
	global $wpdb;
	// double check that groupon code does exist and is still valid
	$SQL = "SELECT id FROM " . EVENTS_GROUPON_CODES_TABLE ;
	$SQL .= " WHERE  groupon_code = %s";								
	$SQL .= " AND  groupon_status = 1";								
	if ( $groupon = $wpdb->get_row( $wpdb->prepare( $SQL, $groupon_code ))) {
		$set_cols_and_values = array( 'groupon_status'=> FALSE, 'attendee_id'=> $primary_attendee_id, 'date'=> date(get_option('date_format')));
		$set_format = array( '%d', '%d', '%s' );
		$where_cols_and_values = array( 'id'=> $groupon->id );  	
		$where_format = array( '%d' );		
		$wpdb->update( EVENTS_GROUPON_CODES_TABLE, $set_cols_and_values, $where_cols_and_values, $set_format, $where_format  );
	}
}


/*function espresso_apply_goupon_to_attendee( $event_id, $final_price, $att_groupon ) {
	//printr( $att_groupon, '$att_groupon  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );
	if ( isset( $att_groupon['code'] ) && $att_groupon['code'] != '' ) {	
		if ( ! isset( $att_groupon['id'] )) {
			// lookup groupon details in db
			global $wpdb;
			$SQL = "SELECT * FROM " . EVENTS_GROUPON_CODES_TABLE ;
			$SQL .= " WHERE  groupon_code = %s";
			$SQL .= " AND ( event_id = 0 OR event_id = %d )";									
			if ( $groupon = $wpdb->get_row( $wpdb->prepare( $SQL, $att_groupon['code'], $event_id ))) {
				$att_groupon['id'] = $groupon->id;
				$att_groupon['status'] = $groupon->groupon_status;
				$att_groupon['holder'] = $groupon->groupon_holder;	
				$att_groupon['discount'] = $final_price;
			} else {
				$att_groupon['status'] = 0;
			}
		}
		if ( $att_groupon['status'] ) {
			$new_att_price_data = array( 
				'amount_pd' => 0.00,
				'final_price' => 0.00,
				'coupon_code' => $att_groupon['code'],
				'payment_date' => date(get_option('date_format')),
				'groupon' => $att_groupon
			);
			add_filter('filter_hook_espresso_attendee_cost', '__return_zero' );
			return $new_att_price_data;
		}				
	}
	return FALSE;
}*/








function event_espresso_groupon_registration_page($use_groupon_code, $event_id){
	if ($use_groupon_code == "Y"){ ?>
		<p class="event_form_field" id="groupon_code-<?php echo $event_id ?>">
			<label for="groupon_code"><?php _e('Enter Voucher code:','event_espresso'); ?></label> 
			<input tabIndex="9" maxLength="25" size="35" type="text" name="event_espresso_groupon_code" id="groupon_code-<?php echo $event_id;?>">
			<input type="hidden" name="use_groupon[<?php echo $event_id; ?>]" value="Y" />
		</p>
<?php
	}
}