<?php
/**
 *
 * @global type $sl_height
 * @global type $sl_width
 * @global type $sl_width_units
 * @global type $sl_height_units
 * @global type $sl_google_map_domain
 * @global type $sl_google_map_country
 * @global type $sl_location_table_view
 * @global type $sl_search_label
 * @global type $sl_zoom_level
 * @global type $sl_zoom_tweak
 * @global type $sl_use_name_search
 * @global type $sl_default_map
 * @global type $sl_radius_label
 * @global type $sl_website_label
 * @global type $sl_num_initial_displayed
 * @global type $sl_load_locations_default
 * @global type $sl_distance_unit
 * @global type $sl_map_overview_control
 * @global type $sl_admin_locations_per_page
 * @global type $sl_instruction_message
 * @global type $sl_map_character_encoding
 * @global string $slplus_name_label
 */
function initialize_variables() {
    global $sl_height, $sl_width, $sl_width_units, $sl_height_units;
    global $sl_google_map_domain, $sl_google_map_country, $sl_location_table_view;
    global $sl_search_label, $sl_zoom_level, $sl_zoom_tweak, $sl_use_name_search, $sl_default_map;
    global $sl_radius_label, $sl_website_label, $sl_num_initial_displayed, $sl_load_locations_default;
    global $sl_distance_unit, $sl_map_overview_control, $sl_admin_locations_per_page, $sl_instruction_message;
    global $sl_map_character_encoding, $slplus_name_label;
    
    $sl_map_character_encoding=get_option('sl_map_character_encoding');
    if (empty($sl_map_character_encoding)) {
        $sl_map_character_encoding="";
        add_option('sl_map_character_encoding', $sl_map_character_encoding);
        }
    $sl_instruction_message=get_option('sl_instruction_message');
    if (empty($sl_instruction_message)) {
        $sl_instruction_message="Enter Your Address or Zip Code Above.";
        add_option('sl_instruction_message', $sl_instruction_message);
        }
    $sl_admin_locations_per_page=get_option('sl_admin_locations_per_page');
    if (empty($sl_admin_locations_per_page)) {
        $sl_admin_locations_per_page="100";
        add_option('sl_admin_locations_per_page', $sl_admin_locations_per_page);
        }
    $sl_map_overview_control=get_option('sl_map_overview_control');
    if (empty($sl_map_overview_control)) {
        $sl_map_overview_control="0";
        add_option('sl_map_overview_control', $sl_map_overview_control);
        }
    $sl_distance_unit=get_option('sl_distance_unit');
    if (empty($sl_distance_unit)) {
        $sl_distance_unit="miles";
        add_option('sl_distance_unit', $sl_distance_unit);
        }
    $sl_load_locations_default=get_option('sl_load_locations_default');
    if (empty($sl_load_locations_default)) {
        $sl_load_locations_default="1";
        add_option('sl_load_locations_default', $sl_load_locations_default);
        }
    $sl_num_initial_displayed=get_option('sl_num_initial_displayed');
    if (empty($sl_num_initial_displayed)) {
        $sl_num_initial_displayed="25";
        add_option('sl_num_initial_displayed', $sl_num_initial_displayed);
        }
    $sl_website_label=get_option('sl_website_label');
    if (empty($sl_website_label)) {
        $sl_website_label="Website";
        add_option('sl_website_label', $sl_website_label);
        }
    $sl_radius_label=get_option('sl_radius_label');
    if (empty($sl_radius_label)) {
        $sl_radius_label="Radius";
        add_option('sl_radius_label', $sl_radius_label);
        }
    $sl_map_type=get_option('sl_map_type');
    if (isset($sl_map_type)) {
        $sl_map_type='roadmap';
        add_option('sl_map_type', $sl_map_type);
        }
    $sl_remove_credits=get_option('sl_remove_credits');
    if (empty($sl_remove_credits)) {
        $sl_remove_credits="0";
        add_option('sl_remove_credits', $sl_remove_credits);
        }
    $sl_use_name_search=get_option('sl_use_name_search');
    if (empty($sl_use_name_search)) {
        $sl_use_name_search="0";
        add_option('sl_use_name_search', $sl_use_name_search);
        }

    $sl_zoom_level=get_option('sl_zoom_level','4');
    add_option('sl_zoom_level', $sl_zoom_level);
    
    $sl_zoom_tweak=get_option('sl_zoom_tweak','1');
    add_option('sl_zoom_tweak', $sl_zoom_tweak);

    $sl_search_label=get_option('sl_search_label');
    if (empty($sl_search_label)) {
        $sl_search_label="Address";
        add_option('sl_search_label', $sl_search_label);
        }
	if (empty($slplus_name_label)) {
		$$slplus_name_label = "Store to search for";
		add_option('sl_name_label', $slplus_name_label);
	}
    $sl_location_table_view=get_option('sl_location_table_view');
    if (empty($sl_location_table_view)) {
        $sl_location_table_view="Normal";
        add_option('sl_location_table_view', $sl_location_table_view);
        }
    $sl_google_map_country=get_option('sl_google_map_country');
    if (empty($sl_google_map_country)) {
        $sl_google_map_country="United States";
        add_option('sl_google_map_country', $sl_google_map_country);
    }
    $sl_google_map_domain=get_option('sl_google_map_domain');
    if (empty($sl_google_map_domain)) {
        $sl_google_map_domain="maps.google.com";
        add_option('sl_google_map_domain', $sl_google_map_domain);
    }
    $sl_height=get_option('sl_map_height');
    if (empty($sl_height)) {
        add_option('sl_map_height', '350');
        $sl_height=get_option('sl_map_height');
        }
    
    $sl_height_units=get_option('sl_map_height_units');
    if (empty($sl_height_units)) {
        add_option('sl_map_height_units', "px");
        $sl_height_units=get_option('sl_map_height_units');
        }	
    
    $sl_width=get_option('sl_map_width');
    if (empty($sl_width)) {
        add_option('sl_map_width', "100");
        $sl_width=get_option('sl_map_width');
        }
    
    $sl_width_units=get_option('sl_map_width_units');
    if (empty($sl_width_units)) {
        add_option('sl_map_width_units', "%");
        $sl_width_units=get_option('sl_map_width_units');
        }	
}

/**
 *
 * @global type $wpdb
 * @global type $slplus_plugin
 * @param type $address
 * @param type $sl_id
 */
function do_geocoding($address,$sl_id='') {    
    global $wpdb, $slplus_plugin;    
    
    $delay = 0;    
    $base_url = "http://maps.googleapis.com/maps/api/geocode/json?sensor=false";
    
    // Loop through for X retries
    //
    $iterations = get_option(SLPLUS_PREFIX.'-goecode_retries');
    if ($iterations <= 0) { $iterations = 1; }
    while($iterations){
    	$iterations--;     
    
        // Iterate through the rows, geocoding each address
        $request_url = $base_url . "&address=" . urlencode($address);
        $errorMessage = '';
        

        // Use HTTP Handler (WP_HTTP) first...
        //
        if (isset($slplus_plugin->http_handler)) { 
            $result = $slplus_plugin->http_handler->request( 
                            $request_url, 
                            array('timeout' => 3) 
                            ); 
            if ($slplus_plugin->http_result_is_ok($result) ) {
                $raw_json = $result['body'];
            }
            
        // Then Curl...
        //
        } elseif (extension_loaded("curl") && function_exists("curl_init")) {
                $cURL = curl_init();
                curl_setopt($cURL, CURLOPT_URL, $request_url);
                curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
                $raw_json = curl_exec($cURL);
                curl_close($cURL);

        // Lastly file_get_contents
        //
        } else {
             $raw_json = file_get_contents($request_url);
        }

        // If raw_json exists, parse it
        //
        if (isset($raw_json)) {
            $json = json_decode($raw_json);
            $status = $json->{'status'};
            
        // no raw json
        //
        } else {
            $json = '';
            $status = '';
        }
        
        // Geocode completed successfully
        //
        if (strcmp($status, "OK") == 0) {
            $iterations = 0;      // Break out of retry loop if we are OK
            $delay = 0;
            
            // successful geocode
            $geocode_pending = false;
            $lat = $json->results[0]->geometry->location->lat;
            $lng = $json->results[0]->geometry->location->lng;
            // Update newly inserted address
            //
            if ($sl_id=='') {
                $query = sprintf("UPDATE " . $wpdb->prefix ."store_locator " .
                       "SET sl_latitude = '%s', sl_longitude = '%s' " .
                       "WHERE sl_id = LAST_INSERT_ID()".
                       " LIMIT 1;", 
                       mysql_real_escape_string($lat), 
                       mysql_real_escape_string($lng)
                       );
            // Update an existing address
            //
            } else {
                $query = sprintf("UPDATE " . $wpdb->prefix ."store_locator SET sl_latitude = '%s', sl_longitude = '%s' WHERE sl_id = $sl_id LIMIT 1;", mysql_real_escape_string($lat), mysql_real_escape_string($lng));
            }
            
            // Run insert/update
            //
            $update_result = $wpdb->query($query);
            if ($update_result == 0) {
                $theDBError = htmlspecialchars(mysql_error($wpdb->dbh),ENT_QUOTES);
                $errorMessage .= __("Could not set the latitude and/or longitude  ", SLPLUS_PREFIX);
                if ($theDBError != '') {
                    $errorMessage .= sprintf(
                                            __("Error: %s.", SLPLUS_PREFIX),
                                            $theDBError
                                            );
                } elseif ($update_result === 0) {
                    $errorMessage .=  __("It appears the address did not change.", SLPLUS_PREFIX);
                } else {
                    $errorMessage .=  __("No error logged.", SLPLUS_PREFIX);
                    $errorMessage .= "<br/>\n" . __('Query: ', SLPLUS_PREFIX);
                    $errorMessage .= print_r($wpdb->last_query,true);
                    $errorMessage .= "<br/>\n" . "Results: " . gettype($update_result) . ' '. $update_result;
                }

            }

        // Geocoding done too quickly
        //
        } else if (strcmp($status, "OVER_QUERY_LIMIT") == 0) {
            
          // No iterations left, tell user of failure
          //
	      if(!$iterations){
            $errorMessage .= sprintf(__("Address %s <font color=red>failed to geocode</font>. ", SLPLUS_PREFIX),$address);
            $errorMessage .= sprintf(__("Received status %s.", SLPLUS_PREFIX),$status)."\n<br>";
	      }                       
          $delay += 100000;

        // Invalid address
        //
        } else if (strcmp($status, 'ZERO_RESULTS') == 0) {
	    	$iterations = 0; 
	    	$errorMessage .= sprintf(__("Address %s <font color=red>failed to geocode</font>. ", SLPLUS_PREFIX),$address);
	      	$errorMessage .= sprintf(__("Unknown Address! Received status %s.", SLPLUS_PREFIX),$status)."\n<br>";
          
        // Could Not Geocode
        //
        } else {
            $geocode_pending = false;
            echo sprintf(__("Address %s <font color=red>failed to geocode</font>. ", SLPLUS_PREFIX),$address);
            if ($status != '') {
                $errorMessage .= sprintf(__("Received data %s.", SLPLUS_PREFIX),'<pre>'.print_r($json,true).'</pre>')."\n";
            } else {
                $errorMessage .= sprintf(__("Reqeust sent to %s.", SLPLUS_PREFIX),$request_url)."\n<br>";
                $errorMessage .= sprintf(__("Received status %s.", SLPLUS_PREFIX),$status)."\n<br>";
            }
        }

        // Show Error Messages
        //
        if ($errorMessage != '') {
            print '<div class="geocode_error">' .
                    $errorMessage .
                    '</div>';
        }

        usleep($delay);
    }
}    

/**
 *
 * @param type $a
 * @return type
 */
function comma($a) {
	$a=preg_replace("/'/"     , '&#39;'   , $a);
	$a=preg_replace('/"/'     , '&quot;'  , $a);
	$a=preg_replace('/>/'     , '&gt;'    , $a);
	$a=preg_replace('/</'     , '&lt;'    , $a);
	$a=preg_replace('/,/'     , '&#44;'   , $a);
	$a=preg_replace('/ & /'   , ' &amp; ' , $a);
    return $a;
}

/**************************************
 ** function: slplus_add_pages_settings()
 **
 ** Add store pages settings to the admin interface.
 **
 **/
function slplus_add_pages_settings() {
    global $slplus_plugin;

    if ($slplus_plugin->license->AmIEnabled(true, "SLPLUS-PAGES")) {
        $slplus_plugin->settings->add_item(
            'Store Pages',
            __('Pages Replace Websites', SLPLUS_PREFIX),
            'use_pages_links',
            'checkbox',
            false,
            __('Use the Store Pages local URL in place of the website URL on the map results list.', SLPLUS_PREFIX)
        );
        $slplus_plugin->settings->add_item(
            'Store Pages',
            __('Prevent New Window', SLPLUS_PREFIX),
            'use_same_window',
            'checkbox',
            false,
            __('Prevent Store Pages web links from opening in a new window.', SLPLUS_PREFIX)
        );
    }
}


/**************************************
 ** function: slplus_create_country_pd()
 **
 ** Create the county pulldown list, mark the checked item.
 **
 **/
function slplus_create_country_pd() {
    global $wpdb, $slplus_plugin;

    // Pro Pack Enabled
    //
    if ($slplus_plugin->license->packages['Pro Pack']->isenabled) {
        $myOptions = '';

        // If Use Country Search option is enabled
        // build our country pulldown.
        //
        if (get_option('sl_use_country_search',0)==1) {
            $cs_array=$wpdb->get_results(
                "SELECT TRIM(sl_country) as country " .
                    "FROM ".$wpdb->prefix."store_locator " .
                    "WHERE sl_country<>'' " .
                        "AND sl_latitude<>'' AND sl_longitude<>'' " .
                    "GROUP BY country " .
                    "ORDER BY country ASC",
                ARRAY_A);

            // If we have country data show it in the pulldown
            //
            if ($cs_array) {
                foreach($cs_array as $sl_value) {
                  $myOptions.=
                    "<option value='$sl_value[country]'>" .
                    $sl_value['country']."</option>";
                }
            }
        }
        return $myOptions;

    // No Pro Pack
    //
    } else {
        return '';
    }
}

/**************************************
 ** function: slplus_create_state_pd()
 **
 ** Create the state pulldown list, mark the checked item.
 **
 **/
function slplus_create_state_pd() {
    global $wpdb, $slplus_plugin;

    // Pro Pack Enabled
    //
    if ($slplus_plugin->license->packages['Pro Pack']->isenabled) {
        $myOptions = '';

        // If Use State Search option is enabled
        // build our state pulldown.
        //
        if (get_option('slplus_show_state_pd',0)==1) {
            $cs_array=$wpdb->get_results(
                "SELECT TRIM(sl_state) as state " .
                    "FROM ".$wpdb->prefix."store_locator " .
                    "WHERE sl_state<>'' " .
                        "AND sl_latitude<>'' AND sl_longitude<>'' " .
                    "GROUP BY state " .
                    "ORDER BY state ASC",
                ARRAY_A);

            // If we have country data show it in the pulldown
            //
            if ($cs_array) {
                foreach($cs_array as $sl_value) {
                  $myOptions.=
                    "<option value='$sl_value[state]'>" .
                    $sl_value['state']."</option>";
                }
            }
        }
        return $myOptions;

    // No Pro Pack
    //
    } else {
        return '';
    }
}



/**************************************
 ** function: slpreport_downloads()
 **
 ** Setup the javascript hook for reporting AJAX
 **
 **/
function slpreport_downloads() {
    ?>
    <script type="text/javascript" src="<?php echo SLPLUS_COREURL; ?>js/jquery.tablesorter.min.js"></script>
    <script type="text/javascript" >
    jQuery(document).ready(
        function($) {
            // Make tables sortable
             var tstts = $("#topsearches_table").tablesorter( {sortList: [[1,1]]} );
             var trtts = $("#topresults_table").tablesorter( {sortList: [[5,1]]} );

            // Export Results Button Click
            //
            jQuery("#export_results").click(
                function(e) {
                    jQuery('<form action="<?php echo SLPLUS_PLUGINURL; ?>/downloadcsv.php" method="post">'+
                            '<input type="hidden" name="filename" value="topresults">' +
                            '<input type="hidden" name="query" value="' + jQuery("[name=topresults]").val() + '">' +
                            '<input type="hidden" name="sort"  value="' + trtts[0].config.sortList.toString() + '">' +
                            '<input type="hidden" name="all"   value="' + jQuery("[name=export_all]").is(':checked') + '">' +
                            '</form>'
                            ).appendTo('body').submit().remove();
                }
            );

            // Export Searches Button Click
            //
            jQuery("#export_searches").click(
                function(e) {
                    jQuery('<form action="<?php echo SLPLUS_PLUGINURL; ?>/downloadcsv.php" method="post">'+
                            '<input type="hidden" name="filename" value="topsearches">' +
                            '<input type="hidden" name="query" value="' + jQuery("[name=topsearches]").val() + '">' +
                            '<input type="hidden" name="sort"  value="' + tstts[0].config.sortList.toString() + '">' +
                            '<input type="hidden" name="all"   value="' + jQuery("[name=export_all]").is(':checked') + '">' +
                            '</form>'
                            ).appendTo('body').submit().remove();
                }
            );

        }
    );
    </script>
    <?php
}


/**
 * Help deserialize data to array.
 *
 * Useful for sl_option_value  field processing.
 *
 * @param type $value
 * @return type
 */
function slp_deserialize_to_array($value) {
    $arrayData = maybe_unserialize($value);
    if (!is_array($arrayData)) {
        if ($arrayData == '') {
            $arrayData = array();
        } else {
            $arrayData = array('value' => $arrayData);
        }
    }
    return $arrayData;
}

/**************************************
 ** function: get_string_from_phpexec()
 **
 ** Executes the included php (or html) file and returns the output as a string.
 **
 ** Parameters:
 **  $file (string, required) - name of the file
 **/
function get_string_from_phpexec($file) {
    global $slplus_plugin;
    return $slplus_plugin->helper->get_string_from_phpexec($file);
}