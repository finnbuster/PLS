<?php

/***********************************************************************
* Class: slp_service_class
*
* The slplus service creation object
*
* This handles the creation of the mobile listener service
*
************************************************************************/

if (! class_exists('slp_service_class')) {
    class slp_service_class {
        
        
            /*************************************
             * The Constructor
             */
            function __construct($params) {
                foreach ($params as $name => $sl_value) {            
                    $this->$name = $sl_value;
                }         
                
                $this->service = SLPLUS_PLUGINDIR . "/include/mobile.php";
                $this->handle = fopen($this->service, "w");
                
                $this->createHeader();
            }
            
            function createHeader() {
                include(SLPLUS_COREDIR."/database-info.php");
                global $wpdb;
$this->ex('<?php
    import_request_variables("gp");

	$username="'.DB_USER.'";
	$password="'.DB_PASSWORD.'";
	$database="'.DB_NAME.'";
	$host="'.DB_HOST.'";
	$dbPrefix = "'.$wpdb->prefix.'";
    $units = "'.get_option('sl_distance_unit').'";
    $max_returned = "'.get_option(SLPLUS_PREFIX.'_maxreturned').'";
    $prefix_maxreturned = "'.SLPLUS_PREFIX.'_maxreturned'.'";
    $show_tags = "'.get_option(SLPLUS_PREFIX.'_show_tags').'";
    $reporting_enabled = "'.get_option(SLPLUS_PREFIX.'-reporting_enabled').'";
	// Get parameters from URL
	if (!isset($_REQUEST["lat"]) || !isset($_REQUEST["lng"]))
        die(json_encode( array("success" => false, "response" => "lat and long required")));
	$center_lat = $_REQUEST["lat"];
	$center_lng = $_REQUEST["lng"];
    if (isset($_REQUEST["max"]))
        $max_REQUEST = $_REQUEST["max"];
    else
        $max_REQUEST = 25;
    if (isset($_REQUEST["radius"]) && ($_REQUEST["radius"] != "")) {
        $radius = $_REQUEST["radius"];
    }
    else {
        $radius = "40000";
    }
    
    //-----------------
	// Set the active MySQL database
	//
	$connection=mysql_connect ($host, $username, $password);
	if (!$connection) { die(json_encode( array(\'success\' => false, \'response\' => \'Not connected : \' . mysql_error()))); }
	$db_selected = mysql_select_db($database, $connection);
	mysql_query("SET NAMES utf8");
	if (!$db_selected) {
		die (json_encode( array(\'success\' => false, \'response\' => \'Cant use db : \' . mysql_error())));
	}

	// If tags are passed filter to just those tags
	//
	$tag_filter = \'\'; 
	if (
		isset($_REQUEST[\'tags\']) && ($_REQUEST[\'tags\'] != \'\')
	){
		$posted_tag = preg_replace(\'/^\s+(.*?)/\',\'$1\',$_REQUEST[\'tags\']);
		$posted_tag = preg_replace(\'/(.*?)\s+$/\',\'$1\',$posted_tag);
		$tag_filter = " AND ( sl_tags LIKE \'%%". $posted_tag ."%%\') ";
	}

	$name_filter = \'\';
	if(isset($_REQUEST[\'name\']) && ($_REQUEST[\'name\'] != \'\'))
	{
		$posted_name = preg_replace(\'/^\s+(.*?)/\',\'$1\',$_REQUEST[\'name\']);
		$posted_name = preg_replace(\'/(.*?)\s+$/\',\'$1\',$posted_name);
		$name_filter = " AND (sl_store LIKE \'%%".$posted_name."%%\')";
	}
	
	//Since miles is default, if kilometers is selected, divide by 1.609344 in order to convert the kilometer value selection back in miles when generating the XML
	//
	$multiplier=($units=="km")? 6371 : 3959;

	$option[$prefix_maxreturned]=(trim($max_returned)!="")? 
    $max_returned : 
    \'25\';
	
	$max = mysql_real_escape_string($option[$prefix_maxreturned]);
    if ($max > $max_REQUEST && $max_REQUEST != \'\') {
        $max = mysql_real_escape_string($max_REQUEST);
    }
    
    //for ($rad = $radius; $rad < 40000; $rad += 100) {
		//Select all the rows in the markers table
		$query = sprintf(
			"SELECT *,".
			"( $multiplier * acos( cos( radians(\'%s\') ) * cos( radians( sl_latitude ) ) * cos( radians( sl_longitude ) - radians(\'%s\') ) + sin( radians(\'%s\') ) * sin( radians( sl_latitude ) ) ) ) AS sl_distance ".
			"FROM ${dbPrefix}store_locator ".
			"WHERE sl_longitude<>\'\' %s %s ".
			"HAVING (sl_distance < \'%s\') ".
			\'ORDER BY sl_distance ASC \'.
			\'LIMIT %s\',
			mysql_real_escape_string($center_lat),
			mysql_real_escape_string($center_lng),
			mysql_real_escape_string($center_lat),
			$tag_filter,
			$name_filter,
			mysql_real_escape_string($radius),
			$max
		);
		
		$result = mysql_query($query);
		if (!$result) {
			die(json_encode( array(\'success\' => false, \'response\' => \'Invalid query: \' . mysql_error())));
		}

		// Show Tags
		//
		$slplus_show_tags = ($show_tags ==1);

		// Reporting
		// Insert the query into the query DB
		// 
		if ($reporting_enabled === \'on\') {
			$qry = sprintf(                                              
					"INSERT INTO ${dbPrefix}slp_rep_query ". 
							   "(slp_repq_query,slp_repq_tags,slp_repq_address,slp_repq_radius) ". 
						"values (\'%s\',\'%s\',\'%s\',\'%s\')",
						mysql_real_escape_string($_SERVER[\'QUERY_STRING\']),
						mysql_real_escape_string($_REQUEST[\'tags\']),
						mysql_real_escape_string($_REQUEST[\'address\']),
						mysql_real_escape_string($_REQUEST[\'radius\'])
					);
			$wpdb->query($qry);
			$slp_QueryID = mysql_insert_id();
		}
		
		// Start the response string
		$response = array();
		
		// Iterate through the rows, printing XML nodes for each
		while ($row = @mysql_fetch_assoc($result)){
			// ADD to array of markers
			
			$marker = array(
				//\'test\' => stuff
				\'name\' => ($row[\'sl_store\']),
				\'address\' => ($row[\'sl_address\']),
				\'address2\' => ($row[\'sl_address2\']),
				\'city\' => ($row[\'sl_city\']),
				\'state\' => ($row[\'sl_state\']),
				\'zip\' => ($row[\'sl_zip\']),
				\'lat\' => $row[\'sl_latitude\'],
				\'lng\' => $row[\'sl_longitude\'],
				\'description\' => ($row[\'sl_description\']),
				\'url\' => ($row[\'sl_url\']),
				\'sl_pages_url\' => ($row[\'sl_pages_url\']),
				\'email\' => ($row[\'sl_email\']),
				\'hours\' => ($row[\'sl_hours\']),
				\'phone\' => ($row[\'sl_phone\']),
				\'fax\' => ($row[\'sl_fax\']),
				\'image\' => ($row[\'sl_image\']),
				\'distance\' => $row[\'sl_distance\'],
				\'tags\' => ($row[\'sl_tags\'])
			);
			$response[] = $marker;
			
			// Reporting
			// Insert the results into the reporting table
			//
			if ($reporting_enabled === "on") {
				$wpdb->query(
					sprintf(
						"INSERT INTO ${dbPrefix}slp_rep_query_results 
							(slp_repq_id,sl_id) values (%d,%d)",
							$slp_QueryID,
							$row[\'sl_id\']  
						)
					);           
			}
		}
		
		//if (count($response) > 1) {
		//	break;
		//}
	//}
    
    $options = array(
        \'units\' => $units,
    );
	
	// generate the response
    $response = json_encode( array( \'success\' => true, \'count\' => count($response), \'options\' => $options, \'response\' => $response) );
 
    // response output
    header( "Content-Type: application/json" );
    echo $response;
	
	
	
	  
 
    // IMPORTANT: don\'t forget to "exit"
    die();');
            }
            
            function ex($line) {
                fwrite($this->handle, $line);
            }
    }
}