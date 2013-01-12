<?php
//This builds the CSS dropdown
/*
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
	
?>
   <a title="<?php echo stripslashes_deep($event_name)?>" class="a_event_title" id="a_event_title-<?php echo $event_id?>" href="<?php echo $registration_url; ?>"><?php echo stripslashes_deep($event_name)?></a>