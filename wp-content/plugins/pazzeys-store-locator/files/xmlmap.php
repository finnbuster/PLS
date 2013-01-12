<?php
//Xmlmap.php - generate XML for Google Maps

require('../../../../wp-load.php' );
	$dom = new DOMDocument("1.0");
	$node = $dom->createElement("markers");
	$parnode = $dom->appendChild($node); 

	$qcountry = urldecode($wp_query->query_vars['country']);
	$central_lat = $_GET['lat'];
	$central_lng = $_GET['lng'];
	$radius = $_GET['radius'];
	$restriction1 = 'pazzey_country';
	$restriction2 = 'pazzey_city';
	$restriction3 = 'pazzey_state';

	header("Content-type: text/xml");
	$sql = 	"SELECT * FROM $wpdb->posts
			LEFT JOIN $wpdb->postmeta AS $restriction1 ON($wpdb->posts.ID = $restriction1.post_id AND $restriction1.meta_key = '$restriction1')
			LEFT JOIN $wpdb->postmeta AS $restriction2 ON($wpdb->posts.ID = $restriction2.post_id AND $restriction2.meta_key = '$restriction2')
			LEFT JOIN $wpdb->postmeta AS $restriction3 ON($wpdb->posts.ID = $restriction3.post_id AND $restriction3.meta_key = '$restriction3')";
			$sql .="WHERE $wpdb->posts.post_type = 'pazzey_store' AND $wpdb->posts.post_status = 'publish' ";
	//echo $sql;
	$storequery = $wpdb->get_results( $sql);
	$earth_radius = 3960.00; // in miles
	$matchecount = 0;

	foreach ( $storequery as $store ) 
	{		
			$id = $store->ID;
			$name = $store->post_title;
			$street = get_post_meta($id, 'pazzey_address',true);
			$city = get_post_meta($id, 'pazzey_city',true);
			$province = get_post_meta($id, 'pazzey_state',true);
			$country = get_post_meta($id, 'pazzey_country',true);
			$zip = get_post_meta($id, 'pazzey_postal',true);
			$address = $street.',<br /> '.$city.', '.$province.'<br /> '.$zip.' '.$country;
			$moreinfo = $store->post_content;
			$lat = get_post_meta($id, 'pazzey_lat',true);
			$lng = get_post_meta($id, 'pazzey_lng',true);
			$delta_lat = $central_lat - $lat;
			$delta_lon = $central_lng - $lng;
			$alpha    = $delta_lat/2;
			$beta     = $delta_lon/2;
			$a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin(deg2rad($beta)) * sin(deg2rad($beta)) ;
			$c        = asin(min(1, sqrt($a)));
			$distance = 2*$earth_radius * $c;
			$distance = round($distance, 4);
			  //echo $central_lat;
			if ($distance < $radius) {
			$locations[] = array ('name'=>$name,'address'=>$address,'lat'=>$lat,'lng'=>$lng,'distance'=>$distance,'moreinfo'=>$moreinfo);
			}
	}
	
	//Sort by distance
	function cmp($a, $b){
		return $a['distance'] - $b['distance'];
	}
	usort($locations, "cmp");
	
	//Loop through array
	foreach ($locations as $location) {
		$node = $dom->createElement("marker");
		$newnode = $parnode->appendChild($node);
			foreach ( $location as $key=>$value ) 
			{				
					$newnode->setAttribute($key, $value);
			}
	}
	
	echo $dom->saveXML();
?>