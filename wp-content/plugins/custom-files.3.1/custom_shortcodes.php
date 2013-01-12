<?php
/*This is a custom shortcodes file. Please visit the Wordpress Shortcode API [http://codex.wordpress.org/Shortcode_API] for the latest information for making custom shortcodes. */

/*
Shortcode Name: Date Range Display
Author: Seth Shoultes
Contact: seth@eventespresso.com
Website: http://eventespresso.com
Description: This shortcode displays events in a table format and allows registrants to choose an event within a certain date range.
Usage Example: [EVENT_DATE_RANGE date_1="2009-12-22" date_2="2009-12-31"]
Requirements: date_range_display.php - template file
Notes: 
*/
function show_event_date_range($atts) {
	extract(shortcode_atts(array('date_1' => __('No Date Supplied','event_espresso'), 'date_2' => __('No Date Supplied','event_espresso') ), $atts));
	$date_1 = "{$date_1}";
	$date_2 = "{$date_2}";
	ob_start();
	display_event_espresso_date_range($date_1, $date_2);
	$buffer = ob_get_contents();
	ob_end_clean();
	return $buffer;
}
add_shortcode('EVENT_DATE_RANGE', 'show_event_date_range');

/*
Shortcode Name: Event Table Display
Author: Seth Shoultes
Contact: seth@eventespresso.com
Website: http://eventespresso.com
Description: This code displays events in a table
Usage Example: [EVENT_TABLE_DISPLAY]
Requirements: table_display.php - template file
Notes: 
*/
function show_event_table_display($atts) {
	ob_start();
	display_event_espresso_table();
	$buffer = ob_get_contents();
	ob_end_clean();
	return $buffer;
}
add_shortcode('EVENT_TABLE_DISPLAY', 'show_event_table_display');

/*
Shortcode Name: Upcoming Events
Author: Seth Shoultes
Contact: seth@eventespresso.com
Website: http://eventespresso.com
Description: Only show events within a certain number number of days into the future. The example below only shows events that start within 30 days from the current date.
Usage Example: [EVENT_DATE_MAX_DAYS max_days="30"]
Requirements: display_event_espresso_date_max() function in the custom_functions.php file
Notes: 
*/
function show_event_date_max($atts) {
	extract(shortcode_atts(array('max_days' => 'No Date Supplied'), $atts));
	$max_days = "{$max_days}";
	ob_start();
	display_event_espresso_date_max($max_days);
	$buffer = ob_get_contents();
	ob_end_clean();
	return $buffer;
}
add_shortcode('EVENT_DATE_MAX_DAYS', 'show_event_date_max');

/*
Shortcode Name: Upcoming Events
Author: Leland Zaremba
Contact: 
Website: 
Description: Only show events in a CATEGORY within a certain number number of days into the future. The example below only shows events in a certain category that start within 30 days from the current date.
Usage Example: [EVENT_CAT_DATE_MAX_DAYS max_days="30" event_category_id="1"]
Requirements: event_list_table.php - template file
Notes: 
*/
function show_cat_event_date_max($atts) {
	extract(shortcode_atts(array('max_days' => 'No Date Supplied', 'event_category_id' => __('No Category ID Supplied','event_espresso')), $atts));
	$max_days = "{$max_days}";
	$event_category_id = "{$event_category_id}";
	ob_start();
	display_event_espresso_cat_date_max($max_days, $event_category_id);
	$buffer = ob_get_contents();
	ob_end_clean();
	return $buffer;
}
add_shortcode('EVENT_CAT_DATE_MAX_DAYS', 'show_cat_event_date_max');

/*
Shortcode Name: Espresso Movie Table
Author: Seth Shoultes
Contact: seth@eventespresso.com
Website: http://www.eventespresso.com
Description: Displays a movie listing like table. Allows you to show events in a CATEGORY within a certain number number of days into the future and a qty. The example below only shows events in a certain category that start within 30 days from the current date.
Usage Example: [ESPRESSO_MOVIE_TABLE max_days="30" qty="3" category_id="gracecard" ]
Custom CSS for the table display
Notes: This file should be stored in your "/wp-content/uploads/espresso/templates/" folder and you should have the custom_includes.php files installed in your "/wp-content/uploads/espresso/" directory.
*/
function espresso_movie_table($atts) {
	extract(shortcode_atts(array('max_days' => '', 'qty' => '10','category_id' => 'No Category ID Supplied'), $atts));
	$max_days = "{$max_days}";
	$qty = "{$qty}";
	$category_id = "{$category_id}";
	ob_start();
	espresso_display_movie_table($max_days, $qty, $category_id);
	$buffer = ob_get_contents();
	ob_end_clean();
	return $buffer;
}
add_shortcode('ESPRESSO_MOVIE_TABLE', 'espresso_movie_table');

/*
Shortcode Name: Espresso Table
Author: Seth Shoultes
Contact: seth@eventespresso.com
Website: http://www.eventespresso.com
Description: Shows events in a table for showing classes etc. Only show events in a CATEGORY within a certain number number of days into the future and a qty. The example below only shows events in a certain category that start within 30 days from the current date.
Usage Example: [ESPRESSO_TABLE max_days="30" qty="3" category_id="gracecard" order_by="state"]
Custom CSS for the table display
Notes: This file should be stored in your "/wp-content/uploads/espresso/templates/" folder and you should have the custom_includes.php files installed in your "/wp-content/uploads/espresso/" directory.
*/
function espresso_table($atts) {
	ob_start();
	espresso_display_table($atts);
	$buffer = ob_get_contents();
	ob_end_clean();
	return $buffer;
}
add_shortcode('ESPRESSO_TABLE', 'espresso_table');