<?php
//This is a custom includes file. It is used to include files such as templates that you have made for displaying events.

//user submitted custom template file for displaying events in a table by date for a maximum amount of days by category. 
//Eg. [EVENT_CAT_DATE_MAX_DAYS max_days="30" event_category_id="sailing3"]
if (file_exists(EVENT_ESPRESSO_TEMPLATE_DIR."event_list_table.php")){
	require_once(EVENT_ESPRESSO_TEMPLATE_DIR."event_list_table.php");
}

//custom template file for displaying events in a table using a shortcode. 
//Eg. [EVENT_TABLE_DISPLAY]
if (file_exists(EVENT_ESPRESSO_TEMPLATE_DIR."table_display.php")){
	require_once(EVENT_ESPRESSO_TEMPLATE_DIR."table_display.php");
}

//custom template file for showing a date range of events using a shortcode. 
//Eg. [EVENT_DATE_RANGE date_1="2009-12-22" date_2="2009-12-31"]
if (file_exists(EVENT_ESPRESSO_TEMPLATE_DIR."date_range_display.php")){
	require_once(EVENT_ESPRESSO_TEMPLATE_DIR."date_range_display.php");
}

//This is an improved version of the table display above
//Eg. [ESPRESSO_TABLE max_days="30" qty="3" category_id="gracecard" sortby="state"]
if (file_exists(EVENT_ESPRESSO_TEMPLATE_DIR."espresso_table.php")){
	require_once(EVENT_ESPRESSO_TEMPLATE_DIR."espresso_table.php");
}

//Ongoing Events
//These lines can be uncommented to facilitate ongoing events
//Custom template for showing normal and ongoing events
//if (file_exists(EVENT_ESPRESSO_TEMPLATE_DIR."ongoing_event_list.php")){
//	require_once(EVENT_ESPRESSO_TEMPLATE_DIR."ongoing_event_list.php");
//}

//Ongoing Events Widget 
//Custom widget for displaying normal ongoing events
//if (file_exists(EVENT_ESPRESSO_TEMPLATE_DIR."ongoing_events_widget.php")){
//	require_once(EVENT_ESPRESSO_TEMPLATE_DIR."ongoing_events_widget.php");
//}

//Secondary Events
//These lines can be uncommented to facilitate secondary/waiting list events
//Custom template for showing events, but also adds a link to the page that will redirect to a secondary event.
//if (file_exists(EVENT_ESPRESSO_TEMPLATE_DIR."event_list_secondary.php")){
//	require_once(EVENT_ESPRESSO_TEMPLATE_DIR."event_list_secondary.php");
//}

//Eg. [MOVIE_LIST max_days="30" qty="3" category_id="gracecard"]
if (file_exists(EVENT_ESPRESSO_TEMPLATE_DIR."movie_list.php")){
	require_once(EVENT_ESPRESSO_TEMPLATE_DIR."movie_list.php");
}

/*
Shortcode Name: CSS Dropdown
* use the following shortcodes in a page or post:
* [EVENT_CSS_DROPDOWN]
* [EVENT_CSS_DROPDOWN limit=1]
* [EVENT_CSS_DROPDOWN show_expired=true]
* [EVENT_CSS_DROPDOWN show_deleted=true]
* [EVENT_CSS_DROPDOWN show_secondary=true]
* [EVENT_CSS_DROPDOWN show_recurrence=true]
* [EVENT_CSS_DROPDOWN category_identifier=your_category_identifier]
*
* Example:
* [EVENT_CSS_DROPDOWN limit=5 show_recurrence=true category_identifier=your_category_identifier]
*
*/
if (file_exists(EVENT_ESPRESSO_TEMPLATE_DIR."css-dropdown/css_dropdown.php")){
	require_once(EVENT_ESPRESSO_TEMPLATE_DIR."css-dropdown/css_dropdown.php");
}