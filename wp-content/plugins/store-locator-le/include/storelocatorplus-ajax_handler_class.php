<?php

/**
 * The Store Locator Plus Ajax Handler Class
 *
 * Manage the AJAX calls that come in from our admin and frontend UI.
 * Currently only holds new AJAX calls, all calls need to go in here.
 * 
 */

if (! class_exists('SLPlus_AjaxHandler')) {
    class SLPlus_AjaxHandler {
        
        /******************************
         * PUBLIC PROPERTIES & METHODS
         ******************************/
        public $parent = null;

        /*************************************
         * The Constructor
         */
        function __construct($params=null) {
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
         * Format the result data into a named array.
         *
         * We will later use this to build our JSONP response.
         *
         * @param array $data - the data from the SLP database
         * @return named array
         */
        function slp_add_marker($row = null) {
            if ($row == null) {
                return '';
            }
            $marker = array(
                  'name'        => esc_attr($row['sl_store']),
                  'address'     => esc_attr($row['sl_address']),
                  'address2'    => esc_attr($row['sl_address2']),
                  'city'        => esc_attr($row['sl_city']),
                  'state'       => esc_attr($row['sl_state']),
                  'zip'         => esc_attr($row['sl_zip']),
                  'country'     => esc_attr($row['sl_country']),
                  'lat'         => $row['sl_latitude'],
                  'lng'         => $row['sl_longitude'],
                  'description' => html_entity_decode($row['sl_description']),
                  'url'         => esc_attr($row['sl_url']),
                  'sl_pages_url'=> esc_attr($row['sl_pages_url']),
                  'email'       => esc_attr($row['sl_email']),
                  'hours'       => esc_attr($row['sl_hours']),
                  'phone'       => esc_attr($row['sl_phone']),
                  'fax'         => esc_attr($row['sl_fax']),
                  'image'       => esc_attr($row['sl_image']),
                  'distance'    => $row['sl_distance'],
                  'tags'        => ((get_option(SLPLUS_PREFIX.'_show_tags',0) ==1)? esc_attr($row['sl_tags']) : ''),
                  'option_value'=> esc_js($row['sl_option_value']),
                  'id'          => $row['sl_id'],
              );

              $marker = apply_filters('slp_results_marker_data',$marker);
              return $marker;
        }

        /**
         * Handle AJAX request for OnLoad action.
         *
         * @global type $wpdb
         */
        function csl_ajax_onload() {
            global $wpdb;
            $username=DB_USER;
            $password=DB_PASSWORD;
            $database=DB_NAME;
            $host=DB_HOST;
            $dbPrefix = $wpdb->prefix;
            $this->setParent();

            $connection=mysql_connect ($host, $username, $password);
            if (!$connection) {
                die (json_encode( array('success' => false, 'slp_version' => $this->parent->version, 'response' => 'Not connected : ' . mysql_error())));
            }

            // Set the active MySQL database
            $db_selected = mysql_select_db($database, $connection);
            mysql_query("SET NAMES utf8");
            if (!$db_selected) {
              die (json_encode( array('success' => false, 'slp_version' => $this->parent->version, 'response' => 'Can\'t use db : ' . mysql_error())));
            }

            $num_initial_displayed=trim(get_option('sl_num_initial_displayed','25'));

            // If tags are passed filter to just those tags
            //
            $tag_filter = '';
            if (
                isset($_POST['tags']) && ($_POST['tags'] != '')
               ){
                $posted_tag = preg_replace('/^\s+(.*?)/','$1',$_POST['tags']);
                $posted_tag = preg_replace('/(.*?)\s+$/','$1',$posted_tag);
                $tag_filter = " AND ( sl_tags LIKE '%%". $posted_tag ."%%') ";
            }

            // If store names are passed, filter show those names
            $name_filter = '';
            if ((get_option(SLPLUS_PREFIX.'_show_name_search') == 1) &&
                isset($_POST['name']) && ($_POST['name'] != ''))
            {
                $posted_name = preg_replace('/^\s+(.*?)/','$1',$_POST['name']);
                $posted_name = preg_replace('/(.*?)\s+$/','$1',$posted_name);
                $name_filter = " AND (sl_store LIKE '%%".$posted_name."%%')";
            }

            // Select all the rows in the markers table
            // Radius was ignored in the original SLP, showing all locations up to N max
            // that is why 99999 is hard-coded here
            //
            $multiplier=(get_option('sl_distance_unit')=="km")? 6371 : 3959;
            $query = sprintf(
                "SELECT *,".
                "( $multiplier * acos( cos( radians('%s') ) * cos( radians( sl_latitude ) ) * cos( radians( sl_longitude ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( sl_latitude ) ) ) ) AS sl_distance ".
                "FROM ${dbPrefix}store_locator ".
                "WHERE sl_longitude<>'' and sl_longitude<>'' %s %s ".
                "HAVING (sl_distance < '%s') ".
                'ORDER BY sl_distance ASC '.
                'LIMIT %s',
                mysql_real_escape_string($_POST['lat']),
                mysql_real_escape_string($_POST['lng']),
                mysql_real_escape_string($_POST['lat']),
                $tag_filter,
                $name_filter,
                mysql_real_escape_string('99999'),
                $num_initial_displayed
            );
            $result = mysql_query(apply_filters('slp_mysql_search_query',$query));

            if (!$result) {
              die('Invalid query: ' . mysql_error());
            }


            // Iterate through the rows, printing json nodes for each
            $response = array();
            while ($row = @mysql_fetch_assoc($result)){
                $response[] = $this->slp_add_marker($row);
            }

            header( "Content-Type: application/json" );
            echo json_encode( 
                    array(  'success'       => true,
                            'count'         => count($response) ,
                            'slp_version'   => $this->parent->version,
                            'type'          => 'load',
                            'response'      => $response
                        )
                    );
            die();
        }

        /**
         * Handle AJAX request for Search calls.
         *
         * @global type $wpdb
         */
        function csl_ajax_search() {
            global $wpdb;
            $username=DB_USER;
            $password=DB_PASSWORD;
            $database=DB_NAME;
            $host=DB_HOST;
            $dbPrefix = $wpdb->prefix;

            $this->setParent();

            // Get parameters from URL
            $center_lat = $_POST["lat"];
            $center_lng = $_POST["lng"];
            $radius = $_POST["radius"];

            //-----------------
            // Set the active MySQL database
            //
            $connection=mysql_connect ($host, $username, $password);
            if (!$connection) { die(json_encode( array('success' => false, 'slp_version' => $this->parent->version, 'response' => 'Not connected : ' . mysql_error()))); }
            $db_selected = mysql_select_db($database, $connection);
            mysql_query("SET NAMES utf8");
            if (!$db_selected) {
                die (json_encode( array('success' => false, 'slp_version' => $this->parent->version, 'response' => 'Can\'t use db : ' . mysql_error())));
            }

            // If tags are passed filter to just those tags
            //
            $tag_filter = '';
            if (
                isset($_POST['tags']) && ($_POST['tags'] != '')
            ){
                $posted_tag = preg_replace('/^\s+(.*?)/','$1',$_POST['tags']);
                $posted_tag = preg_replace('/(.*?)\s+$/','$1',$posted_tag);
                $tag_filter = " AND ( sl_tags LIKE '%%". $posted_tag ."%%') ";
            }

            $name_filter = '';
            if(isset($_POST['name']) && ($_POST['name'] != ''))
            {
                $posted_name = preg_replace('/^\s+(.*?)/','$1',$_POST['name']);
                $posted_name = preg_replace('/(.*?)\s+$/','$1',$posted_name);
                $name_filter = " AND (sl_store LIKE '%%".$posted_name."%%')";
            }

            //Since miles is default, if kilometers is selected, divide by 1.609344 in order to convert the kilometer value selection back in miles when generating the XML
            //
            $multiplier=(get_option('sl_distance_unit')=="km")? 6371 : 3959;

            $option[SLPLUS_PREFIX.'_maxreturned']=(trim(get_option(SLPLUS_PREFIX.'_maxreturned'))!="")?
            get_option(SLPLUS_PREFIX.'_maxreturned') :
            '25';

            $max = mysql_real_escape_string($option[SLPLUS_PREFIX.'_maxreturned']);
            $query = sprintf(
                "SELECT *,".
                "( $multiplier * acos( cos( radians('%s') ) * cos( radians( sl_latitude ) ) * cos( radians( sl_longitude ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( sl_latitude ) ) ) ) AS sl_distance ".
                "FROM ${dbPrefix}store_locator ".
                "WHERE sl_longitude<>'' and sl_longitude<>'' %s %s ".
                "HAVING (sl_distance < '%s') ".
                'ORDER BY sl_distance ASC '.
                'LIMIT %s',
                mysql_real_escape_string($center_lat),
                mysql_real_escape_string($center_lng),
                mysql_real_escape_string($center_lat),
                $tag_filter,
                $name_filter,
                mysql_real_escape_string($radius),
                mysql_real_escape_string($option[SLPLUS_PREFIX.'_maxreturned'])
            );

            $result = mysql_query(apply_filters('slp_mysql_search_query',$query));
            if (!$result) {
                die(json_encode( array('success' => false, 'slp_version' => $this->parent->version, 'query' => $query, 'response' => 'Invalid query: ' . mysql_error())));
            }

            // Reporting
            // Insert the query into the query DB
            //
            if (get_option(SLPLUS_PREFIX.'-reporting_enabled','off') === 'on') {
                $qry = sprintf(
                        "INSERT INTO ${dbPrefix}slp_rep_query ".
                                   "(slp_repq_query,slp_repq_tags,slp_repq_address,slp_repq_radius) ".
                            "values ('%s','%s','%s','%s')",
                            mysql_real_escape_string($_SERVER['QUERY_STRING']),
                            mysql_real_escape_string($_POST['tags']),
                            mysql_real_escape_string($_POST['address']),
                            mysql_real_escape_string($_POST['radius'])
                        );
                $wpdb->query($qry);
                $slp_QueryID = mysql_insert_id();
            }

            // Iterate through the rows, printing XML nodes for each
            $response = array();
            while ($row = @mysql_fetch_assoc($result)){
                $thisLocation = $this->slp_add_marker($row);
                if (!empty($thisLocation)) {
                    $response[] = $thisLocation;

                    // Reporting
                    // Insert the results into the reporting table
                    //
                    if (get_option(SLPLUS_PREFIX.'-reporting_enabled') === "on") {
                        $wpdb->query(
                            sprintf(
                                "INSERT INTO ${dbPrefix}slp_rep_query_results
                                    (slp_repq_id,sl_id) values (%d,%d)",
                                    $slp_QueryID,
                                    $row['sl_id']
                                )
                            );
                    }
                }
            }
            header( "Content-Type: application/json" );
            echo json_encode( 
                    array(  'success'       => true,
                            'count'         => count($response),
                            'option'        => $_POST['address'],
                            'slp_version'   => $this->parent->version,
                            'type'          => 'search', 
                            'response'      => $response
                        )
                    );
            die();
         }


        /**
         * Remove the Store Pages license.
         */
        function license_reset_pages() {
            if (!$this->setParent()) { die(__('Store Pages license could not be removed.',SLPLUS_PREFIX)); }

            global $wpdb;

            foreach (array(
                        SLPLUS_PREFIX.'-SLP-PAGES-isenabled',
                        SLPLUS_PREFIX.'-SLP-PAGES-last_lookup',
                        SLPLUS_PREFIX.'-SLP-PAGES-latest-version',
                        SLPLUS_PREFIX.'-SLP-PAGES-latest-version-numeric',
                        SLPLUS_PREFIX.'-SLP-PAGES-lk',
                        SLPLUS_PREFIX.'-SLP-PAGES-version',
                        SLPLUS_PREFIX.'-SLP-PAGES-version-numeric',

                        SLPLUS_PREFIX.'-SLPLUS-PAGES-isenabled',
                        SLPLUS_PREFIX.'-SLPLUS-PAGES-last_lookup',
                        SLPLUS_PREFIX.'-SLPLUS-PAGES-latest-version',
                        SLPLUS_PREFIX.'-SLPLUS-PAGES-latest-version-numeric',
                        SLPLUS_PREFIX.'-SLPLUS-PAGES-lk',
                        SLPLUS_PREFIX.'-SLPLUS-PAGES-version',
                        SLPLUS_PREFIX.'-SLPLUS-PAGES-version-numeric',
                        )
                    as $optionName) {
                $query = 'DELETE FROM '.$wpdb->prefix."options WHERE option_name='$optionName'";
                $wpdb->query($query);
            }
            
            die(__('Store Pages license has been removed. Refresh the General Settings page.', SLPLUS_PREFIX));
        }

        /**
         * Remove the Pro Pack license.
         */
        function license_reset_propack() {
            if (!$this->setParent()) { die(__('Pro Pack license could not be removed.',SLPLUS_PREFIX)); }

            global $wpdb;

            foreach (array(
                        SLPLUS_PREFIX.'-SLPLUS-PRO-isenabled',
                        SLPLUS_PREFIX.'-SLPLUS-PRO-last_lookup',
                        SLPLUS_PREFIX.'-SLPLUS-PRO-latest-version',
                        SLPLUS_PREFIX.'-SLPLUS-PRO-latest-version-numeric',
                        SLPLUS_PREFIX.'-SLPLUS-PRO-lk',
                        SLPLUS_PREFIX.'-SLPLUS-PRO-version',
                        SLPLUS_PREFIX.'-SLPLUS-PRO-version-numeric',
                        )
                    as $optionName) {
                $query = 'DELETE FROM '.$wpdb->prefix."options WHERE option_name='$optionName'";
                $wpdb->query($query);
            }
            
            die(__('Pro Pack license has been removed. Refresh the General Settings page.', SLPLUS_PREFIX));
        }

	}
}
