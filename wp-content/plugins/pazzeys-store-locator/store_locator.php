<?php 
/*
Plugin Name: Pazzey's Store Locator 
Plugin URI: http://www.crispuno.ca/?p=250
Description: Store Locator Plugin that lets you embed a Google Maps powered store locator. To embed the store locator, use the following shortcut: [storelocator] . To specify width and height [storelocator width="500" height="500"]
Version: 1.2
Author: Cris Puno
Author URI: http://crispuno.ca
*/
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php


//Create Post Type
add_action( 'init', 'create_store_post_type');

function create_store_post_type() {
	register_post_type( 'pazzey_store',
		array(
			'labels' => array(
				'name' => __( 'Stores' ),
				'singular_name' => __( 'Store' ),
				'add_new' => __( 'Add New Store' ),
				'add_new_item' => __( 'Add Store' ),
				'edit' => __( 'Edit' ),
				'edit_item' => __( 'Edit Store' ),
				'new_item' => __( 'New Store' ),
				'view' => __( 'View Store' ),
				'view_item' => __( 'View Store' ),
				'search_items' => __( 'Search Stores' ),
				'not_found' => __( 'No Stores found' ),
				'not_found_in_trash' => __( 'No Stores found in Trash' ),
				'parent' => __( 'Parent Store' ),

			),
			'public' => true,
			'show_ui' => true,
			'publicly_queryable' => truew,
			'exclude_from_search' => true,
			'menu_position' => 20,
			'hierarchical' => true,
			'query_var' => true,
			'supports' => array( 'title', 'editor', 'excerpt','thumbnail','page-attributes' ),
		)
	);
	flush_rewrite_rules( false );
}

add_action( 'add_meta_boxes', 'pazzey_add_location_box' );
wp_reset_query();

add_action( 'save_post', 'pazzey_save_postdata' );

function pazzey_add_location_box() {
    add_meta_box( 
        'pazzey_sectionid',
        __( 'Store Location', 'pazzey_textdomain' ),
        'pazzey_inner_custom_box',
        'pazzey_store' 
		,'normal'
        ,'high'
    );
}

function pazzey_inner_custom_box( $post ) { 
	$streetaddress = get_post_meta($post->ID, 'pazzey_address',true);
	$city = get_post_meta($post->ID, 'pazzey_city',true);
	$state = get_post_meta($post->ID, 'pazzey_state',true);
	$zip = get_post_meta($post->ID, 'pazzey_postal',true);
	$country = get_post_meta($post->ID, 'pazzey_country',true);
	$lat = get_post_meta($post->ID, 'pazzey_lat',true);
	$lng =  get_post_meta($post->ID, 'pazzey_lng',true);
	if (!$lng||!$lat){
	$latlng = '40, -100';
	}else{
	$latlng = $lat.','.$lng;}
	$page_data = get_page($post->ID);
	$content = apply_filters('the_content', $page_data->post_content);
	$cont = str_replace( array("\n", "\r"), ' ', esc_attr( strip_tags( @html_entity_decode( $content, ENT_QUOTES, get_option('blog_charset') ) ) ) );
	$cont = esc_html($cont);
	$infowindow = '<h4>'.get_the_title().'</h4>'.$streetaddress.',<br /> '.$city.', '.$state.'<br /> '.$zip.' '.$country.'<br /><br />'.$cont;
	$googlesearch = str_replace("<br />", " ", $infowindow);
	//Javascript for Google Map ?>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript">
	  function initialize() {
		var myLatlng = new google.maps.LatLng(<?php echo $latlng; ?>);
		var myOptions = {
		  zoom: 14,
		  center: myLatlng,
		  mapTypeId: google.maps.MapTypeId.ROADMAP
		}

		var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

		var contentString = '<?php echo $infowindow; ?>';
			
		var infowindow = new google.maps.InfoWindow({
			content: contentString
		});

		var marker = new google.maps.Marker({
			position: myLatlng,
			map: map,
			title: 'Location'
		});
		
		  infowindow.open(map,marker);
		
	  }

	</script>
<?php

  wp_nonce_field( plugin_basename( __FILE__ ), 'pazzey_noncename' ); ?>
  <table border="0" bordercolor="" style="background-color:" width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td style="vertical-align:top;padding:5px;width:50%">
		<p>
			<label>Street Address:</label><br />
			<input id="pazzey_address" name="pazzey_address" value="<?php echo get_post_meta($post->ID, 'pazzey_address',true) ?>" style="width:80%;"/>
		</p>
		<p>
			<label>City:</label><br />
			<input id="pazzey_city" name="pazzey_city" value="<?php echo get_post_meta($post->ID, 'pazzey_city',true) ?>" style="width:80%;"/>
		</p>
		<p>
			<label>Province / State:</label><br />
			<input id="pazzey_state" name="pazzey_state" value="<?php echo get_post_meta($post->ID, 'pazzey_state',true) ?>" style="width:80%;"/>
		</p>
		<p>
			<label>Country:</label><br />
			<input id="pazzey_country" name="pazzey_country" value="<?php echo get_post_meta($post->ID, 'pazzey_country',true) ?>" style="width:80%;"/>
		</p>
		<p>
			<label>Postal Code:</label><br />
			<input id="pazzey_postal" name="pazzey_postal" value="<?php echo get_post_meta($post->ID, 'pazzey_postal',true) ?>" style="width:80%;"/>
		</p>
		<p><strong>Google Maps Coordinates</strong> (automatically populates when saved)</p>
		<p>
			<label>Latitude:</label><br />
			<input id="pazzey_lat" name="pazzey_lat" value="<?php echo get_post_meta($post->ID, 'pazzey_lat',true) ?>" style="width:80%;"/>
		</p>
		<p>
			<label>Longitude:</label><br />
			<input id="pazzey_lng" name="pazzey_lng" value="<?php echo get_post_meta($post->ID, 'pazzey_lng',true) ?>" style="width:80%;"/>
		</p>
		</td>
		<td style="vertical-align:top;padding:5px;align:right;width:50%">
		<div id="map_canvas" style="width:100%; height:450px;position:relative;"><script type="text/javascript">
    initialize(); </script></div></td>
	</tr>
</table>
		
<?php

}

function pazzey_save_postdata( $post_id ) {
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  if ( !wp_verify_nonce( $_POST['pazzey_noncename'], plugin_basename( __FILE__ ) ) )
      return;
  if ( 'page' == $_POST['post_type'] ) 
  {
    if ( !current_user_can( 'edit_page', $post_id ) )
        return;
  }
  else
  {
    if ( !current_user_can( 'edit_post', $post_id ) )
        return;
  }

  $storeaddress = $_POST['pazzey_address'];
  $storecity = $_POST['pazzey_city'];
  $storestate = $_POST['pazzey_state'];
  $storecountry = $_POST['pazzey_country'];
  $storepostal = $_POST['pazzey_postal'];
  $storelat = $_POST['pazzey_lat'];
  $storelng = $_POST['pazzey_lng'];
  update_post_meta($post_id , 'pazzey_address', $storeaddress);
  update_post_meta($post_id , 'pazzey_city', $storecity);
  update_post_meta($post_id , 'pazzey_state', $storestate);
  update_post_meta($post_id , 'pazzey_country', $storecountry);
  update_post_meta($post_id , 'pazzey_postal', $storepostal);
	//Geocode location
			$address = $storeaddress.', '.$storecity.', '.$storeprovince.', '.$storecountry;
			$newaddress = str_replace(" ","+",$address);			
			$ch = curl_init();
			$_url = "http://maps.google.com/maps/api/geocode/json?address='.$newaddress.'&sensor=false";
			$timeout = 5; // set to zero for no timeout
			curl_setopt ($ch, CURLOPT_URL, $_url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$geocode = curl_exec($ch);
			curl_close($ch);
			//$geocode=@file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$newaddress.'&sensor=false');
		if(!$geocode){ return $storelat = ''; $storelng = '';
		} else {
		$output= json_decode($geocode);
		$storelat = $output->results[0]->geometry->location->lat;
		$storelng = $output->results[0]->geometry->location->lng;
		update_post_meta($post_id , 'pazzey_lat', $storelat);
		update_post_meta($post_id , 'pazzey_lng', $storelng);
		}
	

}

//Shortcode
function showstorelocator_shortcode( $atts ) {
	extract(shortcode_atts(array(
	    'width' => '650',
		'height' => '500',
	), $atts));

$storecode ='<iframe name="storelocator" src ="'.get_bloginfo('wpurl').'/wp-content/plugins/pazzeys-store-locator/files/storelocator.php?height='.$height.'" width="'.$width.'px" height="'.$height.'px" scrolling="no" FRAMEBORDER="0" ></iframe>';
		
	return $storecode;
}
add_shortcode('storelocator', 'showstorelocator_shortcode');
?>