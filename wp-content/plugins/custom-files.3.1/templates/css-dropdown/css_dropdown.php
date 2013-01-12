<?php 

/*
Shortcode Name: CSS Dropdown
Author: Seth Shoultes
Contact: seth@eventespresso.com
Website: http://eventespresso.com
Description: This function creates a pure CSS dropdown.
Usage Example:
Requirements:
Notes: 
*/

if (!function_exists('espresso_css_dropdown_sc')) {
	function espresso_css_dropdown_sc($atts){	
		global $wpdb;
		
		add_action('wp_footer', 'espresso_load_css_dropdown_files');
		
		if (!function_exists('espresso_load_css_dropdown_files')) {
			function espresso_load_css_dropdown_files() {
			 	
				wp_register_script('yahoo-dom-event', (EVENT_ESPRESSO_UPLOAD_URL . "templates/css-dropdown/scripts/yahoo-dom-event.js"), false, EVENT_ESPRESSO_VERSION); 
				wp_print_scripts('yahoo-dom-event');
				
				wp_register_script('animation-min', (EVENT_ESPRESSO_UPLOAD_URL . "templates/css-dropdown/scripts/animation-min.js"), false, EVENT_ESPRESSO_VERSION); 
				wp_print_scripts('animation-min');
				
				wp_register_script('main-javascript', (EVENT_ESPRESSO_UPLOAD_URL . "templates/css-dropdown/scripts/main-javascript.js"), false, EVENT_ESPRESSO_VERSION); 
				wp_print_scripts('main-javascript');
				
			}
		}

		
		
		extract(shortcode_atts(array('category_identifier' => 'NULL','show_expired' => 'false', 'show_secondary'=>'false','show_deleted'=>'false','show_recurrence'=>'false', 'limit' => '0', 'order_by' => 'NULL', 'template_name'=>'css_dropdown_display', 'button_text'=>'Choose an Event'),$atts));		
		
		if ($category_identifier != 'NULL'){
			$type = 'category';
		}
		$show_expired = $show_expired == 'false' ? " AND e.start_date >= '".date ( 'Y-m-d' )."' " : '';
		$show_secondary = $show_secondary == 'false' ? " AND e.event_status != 'S' " : '';
		$show_deleted = $show_deleted == 'false' ? " AND e.event_status != 'D' " : '';
		$show_recurrence = $show_recurrence == 'false' ? " AND e.recurrence_id = '0' " : '';
		$limit = $limit > 0 ? " LIMIT 0," . $limit . " " : '';
		$order_by = $order_by != 'NULL'? " ORDER BY ". $order_by ." ASC " : " ORDER BY date(start_date), id ASC ";

		if ($type == 'category'){
			$sql = "SELECT e.* FROM " . EVENTS_CATEGORY_TABLE . " c ";
			$sql .= " JOIN " . EVENTS_CATEGORY_REL_TABLE . " r ON r.cat_id = c.id ";
			$sql .= " JOIN " . EVENTS_DETAIL_TABLE . " e ON e.id = r.event_id ";
			$sql .= " WHERE c.category_identifier = '" . $category_identifier . "' ";
			$sql .= " AND e.is_active = 'Y' ";
		}else{
			$sql = "SELECT e.* FROM " . EVENTS_DETAIL_TABLE . " e ";
			$sql .= " WHERE e.is_active = 'Y' ";
		}
		$sql .= $show_expired;
		$sql .= $show_secondary;
		$sql .= $show_deleted;
		$sql .= $show_recurrence;
		$sql .= $order_by;
		$sql .= $limit;
		//template located in event_list_dsiplay.php
		ob_start();
		//Load the scripts and styles, build the HTML
		?>
    
    <style type="text/css">
	
	.leftBoxHeading_Off {
	width: 295px;
	height: 19px;
	background: url(<?php echo EVENT_ESPRESSO_UPLOAD_URL ?>templates/css-dropdown/images/left-box-heading-off.png) no-repeat;
	color: #6699cc;
	font-size: 100%;
	padding: 8px 0px 0px 18px;
	cursor: pointer;
	}
	
	.leftBoxHeading_On {
	width: 295px;
	height: 19px;
	background: url(<?php echo EVENT_ESPRESSO_UPLOAD_URL ?>templates/css-dropdown/images/left-box-heading-on.png) no-repeat;
	color: #6699cc;
	font-size: 110%;
	padding: 8px 0px 0px 18px;
	cursor: pointer;
	}
	
	.leftBoxExpander {
	width: 295px;
	overflow: hidden;
	height: 0px;
	background: url(<?php echo EVENT_ESPRESSO_UPLOAD_URL ?>templates/css-dropdown/images/left-box-expander-bg.png);
	}
	.leftBoxInnerPic {
	width: 287px;
	overflow: hidden;
	background: url(<?php echo EVENT_ESPRESSO_UPLOAD_URL ?>templates/css-dropdown/images/left-box-inner-bg.png) bottom repeat-x;
	margin: 0px 0px 0px 1px;
	}
	.leftBoxInnerPicImg {
	width: 89px;
	overflow: hidden;
	float: left;
	padding: 0px 0px 0px 18px;
	}
	
.leftBoxInnerPicUlWrap {
	width: 167px;
	overflow: hidden;
	float: left;
	}
	
.leftBoxInnerPic ul {
	width: 138px;
	padding: 0px 0px 0px 0px;
	margin: -20px 0px 0px 9px;
	list-style: none;
	}
	
.leftBoxInnerPic ul li {
	color: #6699cc;
	font-size: 110%;
	width: 266px;
	}
	
.leftBoxInnerPic ul li a {
	color: #6699cc;
	text-decoration: none;
	display: block;
	padding: 3px 0px 3px 20px;
	}
	
.leftBoxInnerPic ul li a:hover {
	color: #6699cc;
	}
	
.leftBoxFooter_Off {
	width: 295px;
	height: 12px;
	background: url(<?php echo EVENT_ESPRESSO_UPLOAD_URL ?>templates/css-dropdown/images/left-box-footer-off.png) no-repeat;
	cursor: pointer;
	margin: 0px 0px 15px 0px;
	}
	
.leftBoxFooter_On {
	width: 295px;
	height: 12px;
	background: url(<?php echo EVENT_ESPRESSO_UPLOAD_URL ?>templates/css-dropdown/images/left-box-footer-on.png) no-repeat;
	cursor: pointer;
	margin: 0px 0px 15px 0px;
	}
	
.leftBoxInner {
	width: 287px;
	overflow: hidden;
	margin: 0px 0px 0px 1px;
	}


    </style>

<div id="lhsHeader6" class="leftBoxHeading_Off" onClick="lhsAction('6',true,'T6_Effective_Behaviour_Change');"><?php echo $button_text ?></div>
        <div id="lhsExpander6" class="leftBoxExpander">
          <div id="lhsInner6" class="leftBoxInnerPic"> <img src="<?php echo EVENT_ESPRESSO_UPLOAD_URL ?>templates/css-dropdown/images/left-box-inner-img.png" alt="Left image" height="18" width="287" />
                <ul>
                <?php echo  espresso_list_builder($sql,  'css-dropdown/css_dropdown_display.php', '<li>', '</li>'); //Call out the template file ?>
				  
                </ul>
          </div>
        </div>
        <div id="lhsFooter6" class="leftBoxFooter_Off" onClick="lhsAction('6',true,'false');"></div>

<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}
add_shortcode('EVENT_CSS_DROPDOWN', 'espresso_css_dropdown_sc');