<?php
function espresso_ticket_qr_code($atts){
	global $org_options;
	extract( $atts );
	$qr_data = '<img src="http://chart.googleapis.com/chart?chs=135x135&cht=qr&chl='.urlencode(json_encode(array( 'event_code'=>$event_code, 'registration_id'=>$registration_id, 'attendee_id'=>$attendee_id )) ).'" alt="QR Check-in Code" />';
	return $qr_data;
}

/**
	 * Get either a Gravatar URL or complete image tag for a specified email address.
	 *
	 * @param string $email The email address
	 * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
	 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
	 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
	 * @param boole $img True to return a complete IMG tag False for just the URL
	 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
	 * @return String containing either just a URL or a complete image tag
	 * @source http://gravatar.com/site/implement/images/php/
	 */
	 if (!function_exists('espresso_get_gravatar')) {
	
		function espresso_get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
			$url = 'http://www.gravatar.com/avatar/';
			$url .= md5( strtolower( trim( $email ) ) );
			$url .= "?s=$s&d=$d&r=$r";
			if ( $img ) {
				$url = '<img src="' . $url . '"';
				foreach ( $atts as $key => $val )
					$url .= ' ' . $key . '="' . $val . '"';
				$url .= ' />';
			}
			return $url;
		}
	 }
	 
function espresso_file_is_selected($name, $selected='') {
	   $input_item = $name;
			 $option_selections = array($selected);
	   if (!in_array( $input_item, $option_selections )  )
	   return false;
	   else
	   echo  'selected="selected"';
	   return;
}
function espresso_ticket_content($id) {
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM " . EVENTS_TICKET_TEMPLATES . " WHERE id =" . $id);
    foreach ($results as $result) {
        $ticket_id = $result->id;
        $ticket_name = stripslashes_deep($result->ticket_name);
        $ticket_content = stripslashes_deep($result->ticket_content);
    }
    $ticket_data = array('id' => $id, 'ticket_name' => $ticket_name,'ticket_content' => $ticket_content);
    return $ticket_data;
}

//Retunrs an array of available css template files
function espresso_ticket_css_template_files() {
	// read our template dir and build an array of files
	if (file_exists(EVENT_ESPRESSO_UPLOAD_DIR . "tickets/templates/css/base.css")) {
		$dhandle = opendir(EVENT_ESPRESSO_UPLOAD_DIR . 'tickets/templates/css/');//If the template files have been moved to the uplaods folder
	} else {
		$dhandle = opendir(ESPRESSO_TICKETING_FULL_PATH . 'templates/css/');
	}

	$files = array();

	$exclude = array( '.', '..', 'index.htm', 'index.html', 'index.php', '.svn', 'base.css', '.DS_Store', basename($_SERVER['PHP_SELF']) );

	//if we manage to open the directory
	if ($dhandle) {
		// loop through all of the files
		while (( $fname = readdir( $dhandle )) !== FALSE ) {
			// if the file is not in the array of things to exclude
			if ( !in_array( $fname, $exclude) && !is_dir( $fname )) {
				// then store the filename
				$files[] = $fname;
			}
		}
		// close the directory
		closedir($dhandle);
	}

	return $files;
}

//Retunrs an array of available template files
function espresso_ticket_template_files() {
	// read our template dir and build an array of files
	if (file_exists(EVENT_ESPRESSO_UPLOAD_DIR . "tickets/templates/index.php")) {
		$dhandle = opendir(EVENT_ESPRESSO_UPLOAD_DIR . 'tickets/templates/');//If the template files have been moved to the uplaods folder
	} else {
		$dhandle = opendir(ESPRESSO_TICKETING_FULL_PATH . 'templates/');
	}

	$files = array();

	$exclude = array( '.', '..', '.svn', '.DS_Store', basename($_SERVER['PHP_SELF']) );

	//if we manage to open the directory
	if ($dhandle) {
		// loop through all of the files
		while (( $fname = readdir( $dhandle )) !== FALSE ) {
			// if the file is not in the array of things to exclude
			if ( !in_array( $fname, $exclude) && !is_dir( $fname )) {
				// then store the filename
				$files[] = $fname;
			}
		}
		// close the directory
		closedir($dhandle);
	}

	return $files;
}


//Creates the ticket pdf
function espresso_ticket_launch($attendee_id=0, $registration_id=0){
	global $wpdb, $org_options;
	$data = new stdClass;

	//Make sure we have attendee data
	if ($attendee_id==0 || $registration_id==0)
		return;

	//Get the event record
    $sql = "SELECT ed.*, et.css_file, et.template_file, et.ticket_content, et.ticket_logo_url ";
    isset($org_options['use_venue_manager']) && $org_options['use_venue_manager'] == 'Y' ? $sql .= ", v.id venue_id, v.name venue_name, v.address venue_address, v.city venue_city, v.state venue_state, v.zip venue_zip, v.country venue_country, v.meta venue_meta " : '';
    $sql .= " FROM " . EVENTS_DETAIL_TABLE . " ed ";
    isset($org_options['use_venue_manager']) && $org_options['use_venue_manager'] == 'Y' ? $sql .= " LEFT JOIN " . EVENTS_VENUE_REL_TABLE . " r ON r.event_id = ed.id LEFT JOIN " . EVENTS_VENUE_TABLE . " v ON v.id = r.venue_id " : '';
    $sql .= " JOIN " . EVENTS_ATTENDEE_TABLE . " ea ON ea.event_id=ed.id ";
	$sql .= " LEFT JOIN " . EVENTS_TICKET_TEMPLATES . " et ON et.id=ed.ticket_id ";
    $sql .= " WHERE ea.id = '" . $attendee_id . "' AND ea.registration_id = '" . $registration_id . "' ";
	//echo $sql;
    $data->event = $wpdb->get_row($sql, OBJECT);

	//Get the attendee record
    $sql = "SELECT ea.* FROM " . EVENTS_ATTENDEE_TABLE . " ea WHERE ea.id = '" . $attendee_id . "' AND ea.registration_id = '" . $registration_id . "' ";
    $data->attendee = $wpdb->get_row($sql, OBJECT);
	
	if (empty($data->attendee)){
		//echo 'Invalid data supplied.';
		return;
	}

	//Get the primary/first attendee
	$data->primary_attendee = espresso_is_primary_attendee($data->attendee->id) == true ? true : false;

	//unserialize the event meta
	$data->event->event_meta = unserialize($data->event->event_meta);

	//Get the registration date
	$data->attendee->registration_date = $data->attendee->date;

	//Get the CSS file
	$data->event->css_file = (!empty($data->event->css_file) && $data->event->css_file > '0') ? $data->event->css_file : 'simple.css';
	//echo $data->event->css_file;

	//Get the HTML file
	$data->event->template_file = (!empty($data->event->template_file) && $data->event->template_file > '0') ? $data->event->template_file : 'index.php';

	//Venue information
    if (isset($org_options['use_venue_manager']) && $org_options['use_venue_manager'] == 'Y') {
		$data->event->venue_id = !empty($data->event->venue_id)?$data->event->venue_id:'';
		$data->event->venue_name = !empty($data->event->venue_name)?$data->event->venue_name:'';
		$data->event->address = !empty($data->event->venue_address)?$data->event->venue_address:'';
		$data->event->address2 = !empty($data->event->venue_address2)?$data->event->venue_address2:'';
		$data->event->city = !empty($data->event->venue_city)?$data->event->venue_city:'';
		$data->event->state = !empty($data->event->venue_state)?$data->event->venue_state:'';
		$data->event->zip = !empty($data->event->venue_zip)?$data->event->venue_zip:'';
		$data->event->country = !empty($data->event->venue_country)?$data->event->venue_country:'';
		$data->event->venue_meta = !empty($data->event->venue_meta)?unserialize($data->event->venue_meta):'';
    } else {
        $data->event->venue_name = !empty($data->event->venue_title)?$data->event->venue_title:'';
    }

	//Create the Gravatar image
	$data->gravatar = '<img src="' . espresso_get_gravatar($data->attendee->email, $size = '100', $default = 'http://www.gravatar.com/avatar/' ) . '" alt="Gravatar">';

	//Google map IMAGE creation
	$data->event->google_map_image = espresso_google_map_link(array('id' => $data->event->venue_id, 'address' => $data->event->address, 'city' => $data->event->city, 'state' => $data->event->state, 'zip' => $data->event->zip, 'country' => $data->event->country, 'type'=>'map'));

	//Google map LINK creation
	$data->event->google_map_link = espresso_google_map_link(array('address' => $data->event->address, 'city' => $data->event->city, 'state' => $data->event->state, 'zip' => $data->event->zip, 'country' => $data->event->country, 'type'=>'text'));

	//Create the logo
	$data->event->ticket_logo_image = '';
	$data->event->ticket_logo_url = empty($data->event->ticket_logo_url) ? $org_options['default_logo_url']: $data->event->ticket_logo_url;
	if ( !empty($data->event->ticket_logo_url) ){
		$image_size = getimagesize($data->event->ticket_logo_url);
		$data->event->ticket_logo_image = '<img src="'.$data->event->ticket_logo_url.'" '.$image_size[3].' alt="logo" /> ';
	}

	//Create the QR Code image
	$data->qr_code = espresso_ticket_qr_code( array(
		'attendee_id' => $data->attendee->id,
		'event_name' => stripslashes_deep($data->event->event_name),
		'attendee_first' => $data->attendee->fname,
		'attendee_last' => $data->attendee->lname,
		'registration_id' => $data->attendee->registration_id,
		'event_code' => $data->event->event_code,
		'ticket_type' => $data->attendee->price_option,
		'event_time' => $data->attendee->event_time,
		'final_price' => $data->attendee->final_price,
	));

	//Build the seating assignment
	$seatingchart_tag = '';
	if (defined("ESPRESSO_SEATING_CHART")) {
		if (class_exists("seating_chart")) {
			if ( seating_chart::check_event_has_seating_chart($data->attendee->event_id)) {
				$rs = $wpdb->get_row("select scs.* from ".EVENTS_SEATING_CHART_EVENT_SEAT_TABLE." sces inner join ".EVENTS_SEATING_CHART_SEAT_TABLE." scs on sces.seat_id = scs.id where sces.attendee_id = ".$attendee_id);
				if ( $rs !== NULL ) {
					 $data->attendee->seatingchart_tag = $rs->custom_tag." ".$rs->seat." ".$rs->row;
				}
			}
		}
	}

	//Build the ticket name
	$ticket_name = sanitize_title_with_dashes($data->attendee->id.' '.$data->attendee->fname.' '.$data->attendee->lname);

	//Get the HTML as an object
    ob_start();
	if (file_exists(EVENT_ESPRESSO_UPLOAD_DIR . "tickets/templates/index.php")) {
		require_once(EVENT_ESPRESSO_UPLOAD_DIR . 'tickets/templates/'.$data->event->template_file);
	} else {
		require_once('templates/index.php');
	}
	$content = ob_get_clean();
	$content = espresso_replace_ticket_shortcodes($content, $data);

	//Check if debugging or mobile is set
	if ( (isset($_REQUEST['pdf']) && $_REQUEST['pdf']==true)){
		//Create the PDF
		define('DOMPDF_ENABLE_REMOTE',true);
		require_once(EVENT_ESPRESSO_PLUGINFULLPATH . '/tpc/dompdf/dompdf_config.inc.php');
		$dompdf = new DOMPDF();
		$dompdf->load_html($content);
		//$dompdf->set_paper('A4', 'landscape');
		$dompdf->render();
		$dompdf->stream($ticket_name.".pdf", array("Attachment" => false));
		exit(0);
	}

	echo $content;
	exit(0);

}

//Performst the shortcode replacement
function espresso_replace_ticket_shortcodes($content, $data) {
    global $wpdb, $org_options;
    $SearchValues = array(
		//Attendee/Event Information
        "[att_id]",
		"[qr_code]",
		"[gravatar]",
		"[event_id]",
        "[event_identifier]",
        "[registration_id]",
		"[registration_date]",
        "[fname]",
        "[lname]",
        "[event_name]",
        "[description]",
        "[event_link]",
        "[event_url]",

        //Payment details
        "[cost]",
        "[ticket_type]",
		"[ticket_qty]",

		//Organization details
        "[company]",
        "[co_add1]",
        "[co_add2]",
        "[co_city]",
        "[co_state]",
        "[co_zip]",

		//Dates
        "[start_date]",
        "[start_time]",
        "[end_date]",
        "[end_time]",

		//Ticket data
		"[ticket_content]",

		//Logo
		"[ticket_logo_url]",
		"[ticket_logo_image]",

		//Venue information
		"[venue_title]",
		"[venue_address]",
		"[venue_address2]",
		"[venue_city]",
		"[venue_state]",
		"[venue_zip]",
		"[venue_country]",
		"[venue_phone]",
		"[venue_description]",

        "[venue_website]",
        "[venue_image]",

		"[google_map_image]",
        "[google_map_link]",
		"[seatingchart_tag]",
    );

    $ReplaceValues = array(
		//Attendee/Event Information
		$data->attendee->id,
		$data->qr_code,
		$data->gravatar,
        $data->attendee->event_id,
        $data->event->event_identifier,
        $data->attendee->registration_id,
		event_date_display($data->attendee->registration_date),
        stripslashes_deep($data->attendee->fname),
        stripslashes_deep($data->attendee->lname),
        stripslashes_deep($data->event->event_name),
        stripslashes_deep($data->event->event_desc),
       	$data->event_link,
        $data->event_url,

		//Payment details
        $org_options['currency_symbol'] .' '. $data->attendee->final_price,
        $data->attendee->price_option,
		$data->attendee->quantity,

		//Organization details
        stripslashes_deep($org_options['organization']),
        $org_options['organization_street1'],
        $org_options['organization_street2'],
        $org_options['organization_city'],
        $org_options['organization_state'],
        $org_options['organization_zip'],

		//Dates
        event_date_display($data->attendee->start_date),
        event_date_display($data->attendee->event_time, get_option('time_format')),
        event_date_display($data->attendee->end_date),
        event_date_display($data->attendee->end_time, get_option('time_format')),

		//Ticket data
		wpautop(stripslashes_deep(html_entity_decode($data->event->ticket_content, ENT_QUOTES))),

		//Logo
		$data->event->ticket_logo_url,
		$data->event->ticket_logo_image, //Returns the logo wrapped in an image tag

		//Venue information
		$data->event->venue_name,
		$data->event->address,
		$data->event->address2,
		$data->event->city,
		$data->event->state,
		$data->event->zip,
		$data->event->country,
		$data->event->venue_meta['phone'],
		wpautop(stripslashes_deep(html_entity_decode($data->event->venue_meta['description'], ENT_QUOTES))),

		$data->event->venue_meta['website'],
        $data->event->venue_meta['image'],

		$data->event->google_map_image,
        $data->event->google_map_link,
		$data->attendee->seatingchart_tag,
    );

	//Get the questions and answers
	$questions = $wpdb->get_results("select qst.question as question, ans.answer as answer from ".EVENTS_ANSWER_TABLE." ans inner join ".EVENTS_QUESTION_TABLE." qst on ans.question_id = qst.id where ans.attendee_id = ".$data->attendee->id, ARRAY_A);
	//echo '<p>'.print_r($questions).'</p>';
	if ($wpdb->num_rows > 0 && $wpdb->last_result[0]->question != NULL) {
		foreach($questions as $q){
			$k = $q['question'];
			$v = $q['answer'];

			//Output the question
			array_push($SearchValues,"[".'question_'.$k."]");
			array_push($ReplaceValues,$k);

			//Output the answer
			array_push($SearchValues,"[".'answer_'.$k."]");
			array_push($ReplaceValues,$v);
		}
	}

	//Get the event meta
	//echo '<p>'.print_r($data->event->event_meta).'</p>';
	if (!empty($data->event->event_meta)){
		foreach($data->event->event_meta as $k=>$v){
			array_push($SearchValues,"[".$k."]");
			array_push($ReplaceValues,stripslashes_deep($v));
		}
	}

    return str_replace($SearchValues, $ReplaceValues, $content);
}

//Creates the dropdown for use in the event editor
if ( !function_exists( 'espresso_ticket_dd' ) ){
	function espresso_ticket_dd($current_value = 0){
		global $espresso_premium; if ($espresso_premium != true) return;
		global $wpdb;
		$sql = "SELECT id, ticket_name FROM " .EVENTS_TICKET_TEMPLATES;
		$sql .= " WHERE ticket_name != '' ORDER BY ticket_name ";
		//echo $sql;
		$tickets = $wpdb->get_results($sql);
		$num_rows = $wpdb->num_rows;
		//return print_r( $tickets );
		$field = '<select name="ticket_id" id="ticket_id">\n';
			$field .= '<option value="0">'.__('Select a Ticket', 'event_espresso').'</option>';
		if ($num_rows > 0) {
			foreach ($tickets as $ticket){
				$selected = $ticket->id == $current_value ? 'selected="selected"' : '';
				$field .= '<option '. $selected .' value="' . $ticket->id .'">' . $ticket->ticket_name. '</option>\n';
			}
		}
		$field .= '</select>';
		if ( function_exists('espresso_version') && espresso_version() >= '3.2' ){
			$ee_help = '<a class="thickbox" target="_blank" href="#TB_inline?height=400&width=500&inlineId=custom_ticket_info"><span class="question"> [?]</span></a>';
		}else{
			$ee_help = ' <a class="thickbox" href="#TB_inline?height=300&width=400&inlineId=status_types_info"><img src="' . EVENT_ESPRESSO_PLUGINFULLURL . '/images/question-frame.png" width="16" height="16" /></a>';
		}
		$html = '<p><label for="ticket_id">' .__('Custom Ticket ','event_espresso') . '</label>' . $field .  $ee_help . '</p>';
		return $html;
	}
}
