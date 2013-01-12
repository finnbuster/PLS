<?php

/***********************************************************************
* Class: SLPlus_AdminUI
*
* The Store Locator Plus admin UI class.
*
* Provides various UI functions when someone is an admin on the WP site.
*
************************************************************************/

if (! class_exists('SLPlus_AdminUI')) {
    class SLPlus_AdminUI {
        
        /******************************
         * PUBLIC PROPERTIES & METHODS
         ******************************/
        public $addingLocation = false;
        public $currentLocation = array();
        public $parent = null;       
        public $styleHandle = 'csl_slplus_admin_css';

        /*************************************
         * The Constructor
         */
        function __construct($params=null) {

            // Register our admin styleseheet
            //
            if (file_exists(SLPLUS_PLUGINDIR.'css/admin.css')) {
                wp_register_style($this->styleHandle, SLPLUS_PLUGINURL .'/css/admin.css');
            }
        }

        /**
         * Set the parent property to point to the primary plugin object.
         *
         * Returns false if we can't get to the main plugin object.
         *
         * @global wpCSL_plugin__slplus $slplus_plugin
         * @return type boolean true if plugin property is valid
         */
        function setParent() {
            if (!isset($this->parent) || ($this->parent == null)) {
                global $slplus_plugin;
                $this->parent = $slplus_plugin;
            }
            return (isset($this->parent) && ($this->parent != null));
        }

        /**
         * Add an address into the SLP locations database.
         * 
         * @global type $wpdb
         * @param type $fields
         * @param type $sl_values
         * @param type $theaddress
         *
         */
        function add_this_addy($fields,$sl_values,$theaddress) {
            global $wpdb;
            $fields=substr($fields, 0, strlen($fields)-1);
            $sl_values=substr($sl_values, 0, strlen($sl_values)-1);
            $wpdb->query("INSERT into ". $wpdb->prefix . "store_locator ($fields) VALUES ($sl_values);");
            $this->do_geocoding($theaddress);

        }

        /**
         * Setup some of the general settings interface elements.
         */
        function build_basic_admin_settings() {
            if (!$this->setParent()) { return; }

            //-------------------------
            // Navbar Section
            //-------------------------
            $this->parent->settings->add_section(
                array(
                    'name'          => 'Navigation',
                    'div_id'        => 'slplus_navbar_wrapper',
                    'description'   => $this->parent->helper->get_string_from_phpexec(SLPLUS_COREDIR.'/templates/navbar.php'),
                    'innerdiv'      => false,
                    'is_topmenu'    => true,
                    'auto'          => false,
                    'headerbar'     => false
                )
            );

            //-------------------------
            // How to Use Section
            //-------------------------
             $this->parent->settings->add_section(
                array(
                    'name' => 'How to Use',
                    'description' => $this->parent->helper->get_string_from_phpexec(SLPLUS_PLUGINDIR.'/how_to_use.txt'),
                    'start_collapsed' => false
                )
            );

            //-------------------------
            // Google Communication
            //-------------------------
             $this->parent->settings->add_section(
                array(
                    'name'        => 'Google Communication',
                    'description' => 'These settings affect how the plugin communicates with Google to create your map.'.
                                        '<br/><br/>'
                )
            );

             $this->parent->settings->add_item(
                'Google Communication',
                __('Google API Key','csl-slplus'),
                'api_key',
                'text',
                false,
                'Your Google Maps V3 API Key.  Used for searches only. You will need to ' .
                '<a href="http://code.google.com/apis/console/" target="newinfo">'.
                'go to Google</a> to get your Google Maps API Key.'
            );


             $this->parent->settings->add_item(
                'Google Communication',
                __('Geocode Retries','csl-slplus'),
                'goecode_retries',
                'list',
                false,
                sprintf(__('How many times should we try to set the latitude/longitude for a new address. ' .
                    'Higher numbers mean slower bulk uploads ('.
                    '<a href="%s">plus version</a>'.
                    '), lower numbers makes it more likely the location will not be set during bulk uploads.',
                     SLPLUS_PREFIX),
                     'http://www.charlestonsw.com/product/store-locator-plus/'
                     ),                        
                array (
                      'None' => 0,
                      '1' => '1',
                      '2' => '2',
                      '3' => '3',
                      '4' => '4',
                      '5' => '5',
                      '6' => '6',
                      '7' => '7',
                      '8' => '8',
                      '9' => '9',
                      '10' => '10',
                    )
            );

             $this->parent->settings->add_item(
                'Google Communication',
                'Turn Off SLP Maps',
                'no_google_js',
                'checkbox',
                false,
                __('Check this box if your Theme or another plugin is providing Google Maps and generating warning messages.  THIS MAY BREAK THIS PLUGIN.', SLPLUS_PREFIX)
            );

            //--------------------------
            // Store Pages
            //
            $slp_rep_desc = __('These settings affect how the Store Pages add-on behaves. ', SLPLUS_PREFIX);
            if (!$this->parent->license->AmIEnabled(true, "SLPLUS-PAGES")) {
                $slp_rep_desc .= '<br/><br/>'.
                    __('This is a <a href="http://www.charlestonsw.com/product/store-locator-plus-store-pages/">Store Pages</a>'.
                    ' feature.  It provides a way to auto-create individual WordPress pages' .
                    ' for each of your locations. ', SLPLUS_PREFIX);
            } else {
                $slp_rep_desc .= '<span style="float:right;">(<a href="#" onClick="'.
                        'jQuery.post(ajaxurl,{action: \'license_reset_pages\'},function(response){alert(response);});'.
                        '">'.__('Delete license',SLPLUS_PREFIX).'</a>)</span>';
            }
            $slp_rep_desc .= '<br/><br/>';
            $this->parent->settings->add_section(
                array(
                    'name'        => 'Store Pages',
                    'description' => $slp_rep_desc
                )
            );
            if ($this->parent->license->AmIEnabled(true, "SLPLUS-PAGES")) {

                // Setup Store Pages Objects
                //
                if (!isset($slplus_plugin->StorePages) || !is_object($slplus_plugin->StorePages)) {
                    require_once(SLPLUS_PLUGINDIR . '/slp-pages/slp-pages.php');
                }
                $this->parent->StorePages->add_pages_settings();
            }

            //-------------------------
            // Pro Pack
            //
            $proPackMsg = (
                    $this->parent->license->packages['Pro Pack']->isenabled            ?
                    '' :
                    __('This is a <a href="http://www.charlestonsw.com/product/store-locator-plus/">Pro Pack</a>  feature. ', SLPLUS_PREFIX)
                    );
            $slp_rep_desc = __('These settings affect how the Pro Pack add-on behaves. ', SLPLUS_PREFIX);
            if (!$this->parent->license->AmIEnabled(true, "SLPLUS-PRO")) {
                $slp_rep_desc .= '<br/><br/>'.$proPackMsg;
            } else {
                $slp_rep_desc .= '<span style="float:right;">(<a href="#" onClick="'.
                        'jQuery.post(ajaxurl,{action: \'license_reset_propack\'},function(response){alert(response);});'.
                        '">'.__('Delete license',SLPLUS_PREFIX).'</a>)</span>';
            }
            $slp_rep_desc .= '<br/><br/>';
            $this->parent->settings->add_section(
                array(
                    'name'        => 'Pro Pack',
                    'description' => $slp_rep_desc
                )
            );
            if ($this->parent->license->AmIEnabled(true, "SLPLUS-PRO")) {
                $this->parent->settings->add_item(
                    'Pro Pack',
                    __('Enable reporting', SLPLUS_PREFIX),
                    'reporting_enabled',
                    'checkbox',
                    false,
                    __('Enables tracking of searches and returned results.  The added overhead ' .
                    'can increase how long it takes to return location search results.', SLPLUS_PREFIX)
                );
            }
            // Custom CSS Field
            //
            $this->parent->settings->add_item(
                    'Pro Pack',
                    __('Custom CSS',SLPLUS_PREFIX),
                    'custom_css',
                    'textarea',
                    false,
                    __('Enter your custom CSS, preferably for SLPLUS styling only but it can be used for any page element as this will go in your page header.',SLPLUS_PREFIX)
                    .$proPackMsg
                        ,
                    null,
                    null,
                    !$this->parent->license->packages['Pro Pack']->isenabled
                    );
        }

        /**
         *
         * @param type $a
         * @return type
         */
        function slp_escape($a) {
            $a=preg_replace("/'/"     , '&#39;'   , $a);
            $a=preg_replace('/"/'     , '&quot;'  , $a);
            $a=preg_replace('/>/'     , '&gt;'    , $a);
            $a=preg_replace('/</'     , '&lt;'    , $a);
            $a=preg_replace('/,/'     , '&#44;'   , $a);
            $a=preg_replace('/ & /'   , ' &amp; ' , $a);
            return $a;
        }

        /**
         * GeoCode a given location and update it in the database.
         *
         * Google Server-Side API geocoding is documented here:
         * https://developers.google.com/maps/documentation/geocoding/index
         * 
         * @global type $wpdb
         * @global type $slplus_plugin
         * @param type $address
         * @param type $sl_id
         */
        function do_geocoding($address,$sl_id='') {
            global $wpdb, $slplus_plugin;

            $delay = 0;
            $request_url =
                'http://maps.googleapis.com/maps/api/geocode/json'.
                '?sensor=false' .
                '&address=' . urlencode($address)
                ;

            // Loop through for X retries
            //
            $iterations = get_option(SLPLUS_PREFIX.'-goecode_retries');
            if ($iterations <= 0) { $iterations = 1; }
            $initial_iterations = $iterations;
            while($iterations){
                $iterations--;

                // Iterate through the rows, geocoding each address
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
                            $errorMessage .=  __(", The latitude and longitude did not change.", SLPLUS_PREFIX);
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
                    $errorMessage .= sprintf(__("Address %s <font color=red>failed to geocode</font>. ", 'csl-slplus'),$address);
                    $errorMessage .= sprintf(__("Received status %s.", 'csl-slplus'),$status)."\n<br>";
                    $errorMessage .= sprintf(
                            __("Total attempts %d, waited up to %4.2 seconds between request.", 'csl-slplus'),
                            $initial_iterations,
                            $delay/100000
                            ).
                            "\n<br>";
                  }
                  $delay += 100000;

                // Invalid address
                //
                } else if (strcmp($status, 'ZERO_RESULTS') == 0) {
                    $iterations = 0;
                    $errorMessage .= sprintf(__("Address %s <font color=red>failed to geocode</font>. ", 'csl-slplus'),$address);
                    $errorMessage .= sprintf(__("Unknown Address! Received status %s.", 'csl-slplus'),$status)."\n<br>";

                // Could Not Geocode
                //
                } else {
                    $geocode_pending = false;
                    echo sprintf(__("Address %s <font color=red>failed to geocode</font>. ", 'csl-slplus'),$address);
                    if ($status != '') {
                        $errorMessage .= sprintf(__("Received data %s.", 'csl-slplus'),'<pre>'.print_r($json,true).'</pre>')."\n";
                    } else {
                        $errorMessage .= sprintf(__("Reqeust sent to %s.", 'csl-slplus'),$request_url)."\n<br>";
                        $errorMessage .= sprintf(__("Received status %s.", 'csl-slplus'),$status)."\n<br>";
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
         * @global type $sl_google_map_domain
         * @global type $sl_google_map_country
         * @global type $sl_location_table_view
         * @global type $sl_search_label
         * @global type $sl_zoom_level
         * @global type $sl_zoom_tweak
         * @global type $sl_use_name_search
         * @global type $sl_radius_label
         * @global type $sl_website_label
         * @global type $sl_num_initial_displayed
         * @global type $sl_load_locations_default
         * @global type $sl_distance_unit
         * @global type $sl_map_overview_control
         */
        function initialize_variables() {
            global $sl_google_map_domain, $sl_google_map_country, $sl_location_table_view,
                $sl_search_label, $sl_zoom_level, $sl_zoom_tweak, $sl_use_name_search,
                $sl_radius_label, $sl_website_label, $sl_num_initial_displayed, $sl_load_locations_default,
                $sl_distance_unit, $sl_map_overview_control;

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

        }


        /**
         * Display the manage locations pagination
         *
         * @param type $totalLocations
         * @param int $num_per_page
         * @param int $start
         */
        function manage_locations_pagination($totalLocations = 0, $num_per_page = 10, $start = 0) {
            
            // Variable Init
            $pos=0;
            $prev = min(max(0,$start-$num_per_page),$totalLocations);
            $next = min(max(0,$start+$num_per_page),$totalLocations);
            $qry = isset($_GET['q'])?$_GET['q']:'';
            $cleared=preg_replace('/q=$qry/', '', $_SERVER['REQUEST_URI']);

            $extra_text=(trim($qry)!='')    ?
                __("for your search of", SLPLUS_PREFIX).
                    " <strong>\"$qry\"</strong>&nbsp;|&nbsp;<a href='$cleared'>".
                    __("Clear&nbsp;Results", SLPLUS_PREFIX)."</a>" :
                "" ;

            // URL Regex Replace
            //
            if (preg_match('#&start='.$start.'#',$_SERVER['QUERY_STRING'])) {
                $prev_page=str_replace("&start=$start","&start=$prev",$_SERVER['REQUEST_URI']);
                $next_page=str_replace("&start=$start","&start=$next",$_SERVER['REQUEST_URI']);
            } else {
                $prev_page=$_SERVER['REQUEST_URI']."&start=$prev";
                $next_page=$_SERVER['REQUEST_URI']."&start=$next";
            }
            
            // Pages String
            //
            $pagesString = '';
            if ($totalLocations>$num_per_page) {
                if ((($start/$num_per_page)+1)-5<1) {
                    $beginning_link=1;
                } else {
                    $beginning_link=(($start/$num_per_page)+1)-5;
                }
                if ((($start/$num_per_page)+1)+5>(($totalLocations/$num_per_page)+1)) {
                    $end_link=(($totalLocations/$num_per_page)+1);
                } else {
                    $end_link=(($start/$num_per_page)+1)+5;
                }
                $pos=($beginning_link-1)*$num_per_page;
                for ($k=$beginning_link; $k<$end_link; $k++) {
                    if (preg_match('#&start='.$start.'#',$_SERVER['QUERY_STRING'])) {
                        $curr_page=str_replace("&start=$start","&start=$pos",$_SERVER['QUERY_STRING']);
                    }
                    else {
                        $curr_page=$_SERVER['QUERY_STRING']."&start=$pos";
                    }
                    if (($start-($k-1)*$num_per_page)<0 || ($start-($k-1)*$num_per_page)>=$num_per_page) {
                        $pagesString .= "<a class='page-button' href=\"{$_SERVER['PHP_SELF']}?$curr_page\" >";
                    } else {
                        $pagesString .= "<a class='page-button thispage' href='#'>";
                    }


                    $pagesString .= "$k</a>";
                    $pos=$pos+$num_per_page;
                }
            }

            $prevpages = 
                "<a class='prev-page page-button" .
                    ((($start-$num_per_page)>=0) ? '' : ' disabled' ) .
                    "' href='".
                    ((($start-$num_per_page)>=0) ? $prev_page : '#' ).
                    "'>‹</a>"
                ;
            $nextpages = 
                "<a class='next-page page-button" .
                    ((($start+$num_per_page)<$totalLocations) ? '' : ' disabled') .
                    "' href='".
                    ((($start+$num_per_page)<$totalLocations) ? $next_page : '#').
                    "'>›</a>"
                ;

            $pagesString =
                $prevpages .
                $pagesString .
                $nextpages
                ;

            print
                '<div id="slp_pagination" class="tablenav top">'              .
                    '<div id="slp_pagination_pages" class="tablenav-pages">'    .
                        '<span class="displaying-num">'                         .
                                $totalLocations                                 .
                                ' '.__('locations',SLPLUS_PREFIX)               .
                            '</span>'                                           .
                            '<span class="pagination-links">'                   .
                            $pagesString                                        .
                            '</span>'                                           .
                        '</div>'                                                .
                        $extra_text                                             .
                    '</div>'
                ;
        }

        /**
         * Render the manage locations table header
         *
         * @param array $slpManageColumns - the manage locations columns pre-filter
         */
        function manage_locations_table_header($slpManageColumns,$slpCleanURL,$opt,$dir) {
            $tableHeaderString =
                    "<thead>
                    <tr >
                        <th colspan='1'><input type='checkbox' onclick='checkAll(this,document.forms[\"locationForm\"])' class='button'></th>
                        <th colspan='1'>".__("Actions", SLPLUS_PREFIX)."</th>"
                    ;
            foreach ($slpManageColumns as $slpField => $slpLabel) {
                $tableHeaderString .= $this->slpCreateColumnHeader($slpCleanURL,$slpField,$slpLabel,$opt,$dir);
            }
            $tableHeaderString .= '<th>Lat</th><th>Lon</th></tr></thead>';
            return $tableHeaderString;
        }

        /**
         * Enqueue the admin stylesheet when needed.
         */
        function enqueue_admin_stylesheet() {
            wp_enqueue_style($this->styleHandle);
        }

        /**
         * Setup the stylesheet only when needed.
         */
        function set_style_as_needed() {
            $slugPrefix = 'store-locator-plus_page_';

            // Add Locations
            //
            add_action(
                   'admin_print_styles-' . $slugPrefix . 'slp_add_locations',
                    array($this,'enqueue_admin_stylesheet')
                    );

            // General Settings
            //
           add_action(
                   'admin_print_styles-'  . $slugPrefix . 'slp_general_settings',
                    array($this,'enqueue_admin_stylesheet')
                    );
           add_action(
                   'admin_print_styles-'  . 'settings_page_csl-slplus-options',
                    array($this,'enqueue_admin_stylesheet')
                    );


            // Manage Locations
            //
            add_action(
                   'admin_print_styles-' . $slugPrefix . 'slp_manage_locations',
                    array($this,'enqueue_admin_stylesheet')
                    );

            // Map Settings
            //
            add_action(
                   'admin_print_styles-' . $slugPrefix . 'slp_map_settings',
                    array($this,'enqueue_admin_stylesheet')
                    );

            // Reporting
            //
            add_action(
                   'admin_print_styles-' . 'store-locator-le/reporting.php',
                    array($this,'enqueue_admin_stylesheet')
                    );

        }

        /**
         * Render The Create Page Button
         *
         * @param type $locationID
         * @param type $storePageID
         * @return type
         */
        function slpRenderCreatePageButton($locationID=-1,$storePageID=-1) {
            if ($locationID < 0) { return; }            
            $slpPageClass = (($storePageID>0)?'haspage_icon' : 'createpage_icon');
            print "<a   class='action_icon $slpPageClass' 
                        alt='".__('create page',SLPLUS_PREFIX)."' 
                        title='".__('create page',SLPLUS_PREFIX)."' 
                        href='".
                            preg_replace('/&createpage=/'.(isset($_GET['createpage'])?$_GET['createpage']:''), "",$_SERVER['REQUEST_URI']).
                            "&act=createpage&sl_id=$locationID&slp_pageid=$storePageID#a$locationID'
                   ></a>";            
        }  

        /**
         * Check if a URL starts with http://
         *
         * @param type $url
         * @return type
         */
        function url_test($url) {
            return (strtolower(substr($url,0,7))=="http://");
        }

        /**
         * Create the column headers for sorting the table.
         *
         * @param type $theURL
         * @param type $fldID
         * @param type $fldLabel
         * @param type $opt
         * @param type $dir
         * @return type
         */
        function slpCreateColumnHeader($theURL,$fldID='sl_store',$fldLabel='ID',$opt='sl_store',$dir='ASC') {
            if ($opt == $fldID) {
                $curDIR = (($dir=='ASC')?'DESC':'ASC');
            } else {
                $curDIR = $dir;
            }
            return "<th class='manage-column sortable'><a href='$theURL&o=$fldID&sortorder=$curDIR'>" .
                    "<span>$fldLabel</span>".
                    "<span class='sorting-indicator'></span>".
                    "</a></th>";
        }

        /**
         * Draw the add locations page.
         *
         * @global type $wpdb
         */
         function renderPage_AddLocations() {
                global $slplus_plugin,$wpdb;
                $this->initialize_variables();

                print "<div class='wrap'>
                            <div id='icon-add-locations' class='icon32'><br/></div>
                            <h2>Store Locator Plus - ".
                            __('Add Locations', SLPLUS_PREFIX).
                            "</h2>".                      
                      $slplus_plugin->helper->get_string_from_phpexec(SLPLUS_COREDIR.'/templates/navbar.php')                      
                      ;


                //Inserting addresses by manual input
                //
                if ( isset($_POST['store-']) && $_POST['store-']) {
                    $fieldList = '';
                    $sl_valueList = '';
                    foreach ($_POST as $key=>$sl_value) {
                        if (preg_match('#\-$#', $key)) {
                            $fieldList.='sl_'.preg_replace('#\-$#','',$key).',';
                            $sl_value=$this->slp_escape($sl_value);
                            $sl_valueList.="\"".stripslashes($sl_value)."\",";
                        }
                    }

                    $this_addy = 
                              $_POST['address-'].', '.
                              $_POST['address2-'].', '.
                              $_POST['city-'].', '.$_POST['state-'].' '.
                              $_POST['zip-'] . ', ' .
                              $_POST['country-']
                              ;

                    $slplus_plugin->AdminUI->add_this_addy($fieldList,$sl_valueList,$this_addy);
                    print "<div class='updated fade'>".
                            $_POST['store-'] ." " .
                            __("Added Succesfully",SLPLUS_PREFIX) . '.</div>';

                /** Bulk Upload
                 **/
                } elseif ( isset($_FILES['csvfile']['name']) &&
                       ($_FILES['csvfile']['name']!='')  &&
                        ($_FILES['csvfile']['size'] > 0)
                    ) {
                    add_filter('upload_mimes', array('SLPlus_AdminUI','custom_upload_mimes'));

                    // Get the type of the uploaded file. This is returned as "type/extension"
                    $arr_file_type = wp_check_filetype(basename($_FILES['csvfile']['name']));
                    if ($arr_file_type['type'] == 'text/csv') {

                                // Save the file to disk
                                //
                                $updir = wp_upload_dir();
                                $updir = $updir['basedir'].'/slplus_csv';
                                if(!is_dir($updir)) {
                                    mkdir($updir,0755);
                                }
                                if (move_uploaded_file($_FILES['csvfile']['tmp_name'],
                                        $updir.'/'.$_FILES['csvfile']['name'])) {
                                        $reccount = 0;

                                        $adle_setting = ini_get('auto_detect_line_endings');
                                        ini_set('auto_detect_line_endings', true);
                                        if (($handle = fopen($updir.'/'.$_FILES['csvfile']['name'], "r")) !== FALSE) {
                                            $fldNames = array('sl_store','sl_address','sl_address2','sl_city','sl_state',
                                                            'sl_zip','sl_country','sl_tags','sl_description','sl_url',
                                                            'sl_hours','sl_phone','sl_email','sl_image','sl_fax');
                                            $maxcols = count($fldNames);
                                            while (($data = fgetcsv($handle)) !== FALSE) {
                                                $num = count($data);
                                                if ($num <= $maxcols) {
                                                    $fieldList = '';
                                                    $sl_valueList = '';
                                                    $this_addy = '';
                                                    for ($fldno=0; $fldno < $num; $fldno++) {
                                                        $fieldList.=$fldNames[$fldno].',';
                                                        $sl_valueList.="\"".stripslashes($this->slp_escape($data[$fldno]))."\",";
                                                        if (($fldno>=1) && ($fldno<=6)) {
                                                            $this_addy .= $data[$fldno] . ', ';
                                                        }
                                                    }
                                                    $this_addy = substr($this_addy, 0, strlen($this_addy)-2);
                                                    $slplus_plugin->AdminUI->add_this_addy($fieldList,$sl_valueList,$this_addy);
                                                    sleep(0.5);
                                                    $reccount++;
                                                } else {
                                                     print "<div class='updated fade'>".
                                                        __('The CSV file has too many fields.',
                                                            SLPLUS_PREFIX
                                                            );
                                                     print ' ';
                                                     printf(__('Got %d expected less than %d.', SLPLUS_PREFIX),
                                                        $num,$maxcols);
                                                     print '</div>';
                                                }
                                            }
                                            fclose($handle);
                                        }
                                        ini_set('auto_detect_line_endings', $adle_setting);


                                        if ($reccount > 0) {
                                            print "<div class='updated fade'>".
                                                    sprintf("%d",$reccount) ." " .
                                                    __("locations added succesfully.",SLPLUS_PREFIX) . '</div>';
                                        }

                                // Could not save
                                } else {
                                        print "<div class='updated fade'>".
                                        __("File could not be saved, check the plugin directory permissions:",SLPLUS_PREFIX) .
                                            "<br/>" . $updir.

                                '.</div>';
                        }

                        // Not CSV Format Warning
                    } else {
                        print "<div class='updated fade'>".
                            __("Uploaded file needs to be in CSV format.",SLPLUS_PREFIX) .
                            " Type was " . $arr_file_type['type'] .
                            '.</div>';
                    }
                }

                $base=get_option('siteurl');
                $this->addingLocation = true;
                print 
                    '<div id="location_table_wrapper">'.
                        "<table id='manage_locations_table' class='slplus wp-list-table widefat fixed posts' cellspacing=0>" .
                            '<tr><td class="slp_locationinfoform_cell">' .
                                $slplus_plugin->AdminUI->createString_LocationInfoForm(array(),'', true) .
                            '</td></tr>' .
                        '</table>' .
                    '</div>'
                    ;
         }

         /**
          * Return the value of the field specified for the current location.
          * @param string $fldname - a location field
          * @return string - value of the field
          */
         function getFieldValue($fldname=null) {
             if (($fldname === null) || ($this->addingLocation)) { return ''; }
             else { return $this->currentLocation[$fldname]; }
         }

        /**
         * Render the edit locations form fields.
         *
         * @param named array $sl_value the location data.
         * @return string HTML of the form inputs
         */
        function renderFields_editlocation() {
            if (!$this->setParent()) { return; }

            $content = '';
            ob_start();
            ?>
            <table>
                <tr>
                    <td><div class="add_location_form">
                        <label  for='store-<?php echo $this->getFieldValue('sl_id')?>'><?php _e('Name of Location', SLPLUS_PREFIX);?></label>
                        <input name='store-<?php echo $this->getFieldValue('sl_id')?>' value='<?php echo $this->getFieldValue('sl_store')?>'><br/>

                        <label  for='address-<?php echo $this->getFieldValue('sl_id')?>'><?php _e('Street - Line 1', SLPLUS_PREFIX);?></label>
                        <input name='address-<?php echo $this->getFieldValue('sl_id')?>' value='<?php echo $this->getFieldValue('sl_address')?>'><br/>

                        <label  for='address2-<?php echo $this->getFieldValue('sl_id')?>'><?php _e('Street - Line 2', SLPLUS_PREFIX);?></label>
                        <input name='address2-<?php echo $this->getFieldValue('sl_id')?>' value='<?php echo $this->getFieldValue('sl_address2')?>'><br/>

                        <label  for='city-<?php echo $this->getFieldValue('sl_id')?>'><?php _e('City, State, ZIP', SLPLUS_PREFIX);?></label>
                        <input name='city-<?php echo $this->getFieldValue('sl_id')?>'    value='<?php echo $this->getFieldValue('sl_city')?>'     style='width: 21.4em; margin-right: 1em;'>
                        <input name='state-<?php echo $this->getFieldValue('sl_id')?>'   value='<?php echo $this->getFieldValue('sl_state')?>'    style='width: 7em; margin-right: 1em;'>
                        <input name='zip-<?php echo $this->getFieldValue('sl_id')?>'     value='<?php echo $this->getFieldValue('sl_zip')?>'      style='width: 7em;'><br/>

                        <label  for='country-<?php echo $this->getFieldValue('sl_id')?>'><?php _e('Country', SLPLUS_PREFIX);?></label>
                        <input name='country-<?php echo $this->getFieldValue('sl_id')?>' value='<?php echo $this->getFieldValue('sl_country')?>'  style='width: 40em;'><br/>

                        <?php
                        if ($this->parent->AdminUI->addingLocation === false) {
                        ?>
                            <label  for='latitude-<?php echo $this->getFieldValue('sl_id')?>'><?php _e('Latitude (N/S)', SLPLUS_PREFIX);?></label>
                            <?php if ($this->parent->license->packages['Pro Pack']->isenabled) { ?>
                                <input name='latitude-<?php echo $this->getFieldValue('sl_id')?>' value='<?php echo $this->getFieldValue('sl_latitude')?>'  style='width: 40em;'><br/>
                            <?php } else { ?>
                                <input class='disabled'  name='latitude-<?php echo $this->getFieldValue('sl_id')?>' value='<?php echo __('Changing the latitude is a Pro Pack feature.',SLPLUS_PREFIX).' ('.$this->getFieldValue('sl_latitude').')';?>'  style='width: 40em;'><br/>
                            <?php } ?>

                            <label  for='longitude-<?php echo $this->getFieldValue('sl_id')?>'><?php _e('Longitude (E/W)', SLPLUS_PREFIX);?></label>
                            <?php if ($this->parent->license->packages['Pro Pack']->isenabled) { ?>
                                <input name='longitude-<?php echo $this->getFieldValue('sl_id')?>' value='<?php echo $this->getFieldValue('sl_longitude')?>'  style='width: 40em;'><br/>
                            <?php } else { ?>
                                <input class='disabled' name='longitude-<?php echo $this->getFieldValue('sl_id')?>' value='<?php echo __('Changing the longitude is a Pro Pack feature.',SLPLUS_PREFIX).' ('.$this->getFieldValue('sl_longitude').')'; ?>'  style='width: 40em;'><br/>
                            <?php } ?>
                        <?php
                        }
                        ?>
                        </div>
                    </td>
                </tr>
            </table>
            <?php
            $content .= ob_get_clean();
            return $content;
        }



        /**
         * Render the General Settings admin page.
         *
         */
        function renderPage_GeneralSettings() {
            global $slplus_plugin;
            $slplus_plugin->settings->render_settings_page();
        }


        /**
         * Render the Manage Locations admin page.
         */
        function renderPage_ManageLocations() {
            require_once(SLPLUS_PLUGINDIR . '/include/slp-adminui_managelocations_class.php');
            $this->parent->AdminUI->ManageLocations = new SLPlus_AdminUI_ManageLocations();
            $this->parent->AdminUI->ManageLocations->render_adminpage();
        }

        /**
         * Render the Map Settings admin page.
         */
        function renderPage_MapSettings() {
            require_once(SLPLUS_PLUGINDIR . '/include/slp-adminui_mapsettings_class.php');
            $this->parent->AdminUI->MapSettings = new SLPlus_AdminUI_MapSettings();
            $this->parent->AdminUI->MapSettings->render_adminpage();


        }


         /**
          * Returns the string that is the Location Info Form guts.
          *
          * @global wpCSL_plugin__slplus $slplus_plugin
          * @param mixed $sl_value - the data values for this location in array format
          * @param int $locID - the ID number for this location
          * @param bool $addform - true if rendering add locations form
          */
         function createString_LocationInfoForm($sl_value, $locID, $addform=false) {
            global $slplus_plugin;
            $this->addingLocation = $addform;
            
            $slpEditForm = '';
            $this->currentLocation = apply_filters('slp_edit_location_data',$sl_value);

            /**
             * @see  http://goo.gl/ooXFC 'slp_edit_location_data' filter to manipulate edit location incoming data
             */
             $content  = ''                                                                     .
                "<form id='manualAddForm' name='manualAddForm' method='post' enctype='multipart/form-data'>"       .
                "<a name='a".$locID."'></a>"                                                    .
                "<table cellpadding='0' class='slp_locationinfoform_table'>"                           .
                "<tr><td valign='top'>"                                                         .
                $slplus_plugin->AdminUI->renderFields_editlocation()
                ;

                // Store Pages URLs
                //
                if (
                    ($slplus_plugin->license->packages['Store Pages']->isenabled) &&
                    !$addform &&
                    ($sl_value['sl_pages_url'] != '')
                    ){
                    $shortSPurl = preg_replace('/^.*?store_page=/','',$sl_value['sl_pages_url']);
                    $slpEditForm .= "<label for='store_page'>Store Page</label><a href='$sl_value[sl_pages_url]' target='csa'>$shortSPurl</a><br/>";
                }

                $edCancelURL = isset($_GET['edit']) ?
                    preg_replace('/&edit='.$_GET['edit'].'/', '',$_SERVER['REQUEST_URI']) :
                    $_SERVER['REQUEST_URI']
                    ;

                $alTitle =
                    ($addform?
                        __('Add Location',SLPLUS_PREFIX):
                        sprintf("%s #%d",__('Update Location', SLPLUS_PREFIX),$locID)
                    );
                $slpEditForm .= 
                        ($addform? '' : "<span class='slp-edit-location-id'>Location # $locID</span>") .
                        "<div id='slp_form_buttons'>" .
                        "<input type='submit' value='".($addform?__('Add',SLPLUS_PREFIX):__('Update', SLPLUS_PREFIX)).
                            "' alt='$alTitle' title='$alTitle' class='button-primary'>".
                        "<input type='button' class='button' value='".__('Cancel', SLPLUS_PREFIX)."' onclick='location.href=\"".$edCancelURL."\"'>".
                        "<input type='hidden' name='option_value-$locID' value='".($addform?'':$sl_value['sl_option_value'])."' />"  .
                        "</div>"
                        ;

                /**
                 * @see  http://goo.gl/ooXFC 'slp_edit_location_left_column' filter to manipulate edit location form, left column
                 */
                $content .= apply_filters('slp_edit_location_left_column',$slpEditForm)             .
                    '</td>'                                                                         .
                    "<td id='slp_manual_update_table_right_cell'>"
                    ;
                        
                $slpEditForm =
                        "<div id='slp_edit_right_column'>" .

                        "<strong>".__("Additional Information", SLPLUS_PREFIX)."</strong><br>".

                        "<textarea name='description-$locID' rows='5' cols='17'>".($addform?'':$sl_value['sl_description'])."</textarea>&nbsp;<small>".
                            __("Description", SLPLUS_PREFIX)."</small><br>".

                        "<input    name='tags-$locID'  value='".($addform?'':$sl_value['sl_tags'] )."'>&nbsp;<small>".
                            __("Tags (seperate with commas)", SLPLUS_PREFIX)."</small><br>".

                        "<input    name='url-$locID'   value='".($addform?'':$sl_value['sl_url']  )."'>&nbsp;<small>".
                            get_option('sl_website_label','Website')."</small><br>".

                        "<input    name='email-$locID' value='".($addform?'':$sl_value['sl_email'])."'>&nbsp;<small>".
                            __("Email", SLPLUS_PREFIX)."</small><br>".

                        "<input    name='hours-$locID' value='".($addform?'':$sl_value['sl_hours'])."'>&nbsp;<small>".
                            $slplus_plugin->settings->get_item('label_hours','Hours','_')."</small><br>".

                        "<input    name='phone-$locID' value='".($addform?'':$sl_value['sl_phone'])."'>&nbsp;<small>".
                            $slplus_plugin->settings->get_item('label_phone','Phone','_')."</small><br>".

                        "<input    name='fax-$locID'   value='".($addform?'':$sl_value['sl_fax']  )."'>&nbsp;<small>".
                            $slplus_plugin->settings->get_item('label_fax','Fax','_')."</small><br>".

                        "<input    name='image-$locID' value='".($addform?'':$sl_value['sl_image'])."'>&nbsp;<small>".
                            __("Image URL (shown with location)", SLPLUS_PREFIX)."</small>" .

                        '</div>'
                        ;

                /**
                 * @see  http://goo.gl/ooXFC 'slp_edit_location_right_column' filter to manipulate edit location form, right column
                 */
                $content .= apply_filters('slp_edit_location_right_column',$slpEditForm);
                $content .= '</td></tr></table>';

                // Bulk upload form
                //
                if ($addform && ($slplus_plugin->license->packages['Pro Pack']->isenabled)) {
                    $content .=
                        '<div class="slp_bulk_upload_div">' .
                        '<h2>'.__('Bulk Upload', SLPLUS_PREFIX).'</h2>'.
                        '<input type="file" name="csvfile" value="" id="bulk_file" size="60"><br/>' .
                        "<input type='submit' value='".__("Upload Locations", SLPLUS_PREFIX)."' class='button-primary'>".
                        '</div>';
                }

                $content .= '</form>';

                return apply_filters('slp_locationinfoform',$content);
         }

        /**
         * Allows WordPress to process csv file types
         *
         */
        function custom_upload_mimes ( $existing_mimes=array() ) {
            $existing_mimes['csv'] = 'text/csv';
            return $existing_mimes;
        }

        /**
         * Render an icon selector for the icon images store in the SLP plugin icon directory.
         *
         * @param type $inputFieldID
         * @param type $inputImageID
         * @return string
         */
         function rendorIconSelector($inputFieldID = null, $inputImageID = null) {
            if (!$this->setParent()) { return 'could not set parent'; }
            if (($inputFieldID == null) || ($inputImageID == null)) { return ''; }
            $htmlStr = '';

            $directories = apply_filters('slp_icon_directories',array(SLPLUS_ICONDIR, SLPLUS_UPLOADDIR."/saved-icons/"));
            foreach ($directories as $directory) {
                if (is_dir($directory)) {
                    if ($iconDir=opendir($directory)) {
                        $iconURL = (($directory === SLPLUS_ICONDIR)?SLPLUS_ICONURL:SLPLUS_UPLOADURL.'/saved-icons/');
                        $files=array();
                        while ($files[] = readdir($iconDir));
                        sort($files);
                        closedir($iconDir);

                        foreach ($files as $an_icon) {
                            if (
                                (preg_match('/\.(png|gif|jpg)/i', $an_icon) > 0) &&
                                (preg_match('/shadow\.(png|gif|jpg)/i', $an_icon) <= 0)
                                ) {
                                $htmlStr .=
                                    "<div class='slp_icon_selector_box'>".
                                        "<img class='slp_icon_selector'
                                             src='".$iconURL.$an_icon."'
                                             onclick='".
                                                "document.getElementById(\"".$inputFieldID."\").value=this.src;".
                                                "document.getElementById(\"".$inputImageID."\").src=this.src;".
                                             "'>".
                                     "</div>"
                                     ;
                            }
                        }
                    } else {
                        $this->parent->notifications->add_notice(
                                9,
                                sprintf(
                                        __('Could not read icon directory %s',SLPLUS_PREFIX),
                                        $directory
                                        )
                                );
                         $this->parent->notifications->display();
                    }
               }
            }
            if ($htmlStr != '') {
                $htmlStr = '<div id="'.$inputFieldID.'_icon_row" class="slp_icon_row">'.$htmlStr.'</div>';

            }


            return $htmlStr;
         }

    }
}        
     

