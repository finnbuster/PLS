<?php
/*
Template Name: Ongoing Events Widget
Author: Seth Shoultes
Contact: support@eventespresso.com
Website: http://www.eventespresso.com
Description: This is a custom widget for displaying normal and ongoing events.
Shortcode: N/A
Requirements: custom_includes.php
Notes: This file should be stored in your "/wp-content/uploads/espresso/templates/"
*/

if (!class_exists('Event_Espresso_Widget')) {
	class Event_Espresso_Widget extends WP_Widget {
		
		function Event_Espresso_Widget() {
			/* Widget settings. */
			$widget_options = array( 'classname' => 'events', 'description' => __('A widget to display your upcoming events.', 'events') );
	
			/* Widget control settings. */
			$control_options = array( 'width' => 300, 'height' => 350, 'id_base' => 'events-widget' );
	
			/* Create the widget. */
			$this->WP_Widget( 'events-widget', __('Event Registration Widget', 'events'), $widget_options, $control_options );
		}
	
	
		function widget($args, $instance ) {
			extract( $args );
			global $wpdb, $org_options;
			
			/* Our variables from the widget settings. */
			$title = apply_filters('widget_title', $instance['title'] );				
			
			/* Before widget (defined by themes). */
			echo $before_widget;
			
			/* Display the widget title if one was input (before and after defined by themes). */
			if ( $title )
				echo $before_title . $title . $after_title;
					$events_detail_tbl = get_option('events_detail_tbl');
					$paypal_cur =$org_options['currency_format'];
					$event_page_id =$org_options['event_page_id'];
					
					$sql = "SELECT e.* FROM ". EVENTS_DETAIL_TABLE . " e ";
					$sql .= " WHERE is_active = 'Y' ";//Makes sure event is active
					$sql .= " AND e.event_status != 'D' ";//Makes sure event is not deleted
					$sql .= " AND e.event_status != 'S' "; //Doesn't show secondary events
					//$sql .= " AND event_status = 'O' ";//Un-comment to only show ongoing events
					
					//Removing this line allows you to show events that may have expired
					//$sql .= " AND start_date >= '".date ( 'Y-m-d' )."' ";
					
					//These lines are used to show events within a registration start and end period
					$sql .= " AND e.registration_start <= '".date ( 'Y-m-d' )."' ";
					$sql .= " AND e.registration_end >= '".date ( 'Y-m-d' )."' ";
					
					//This line orders the events by date
					$sql .= " ORDER BY date(start_date), id";
					
					$events = $wpdb->get_results($sql);
	?>
					<div id="widget_display_all_events"><ul class="event_items">
	<?php
						foreach ($events as $event){
							$event_id = $event->id;
							$event_name = $event->event_name;
							$start_date = $event->start_date;
							
							if (!is_user_logged_in() && get_option('events_members_active') == 'true' && $event->member_only == 'Y') {
								//Display a message if the user is not logged in.
								//_e('Member Only Event. Please ','event_espresso') . event_espresso_user_login_link() . '.';
							}else if ( $event->start_date >= date ( 'Y-m-d' ) && $event->registration_start <= date ('Y-m-d') && $event->registration_end >= date ('Y-m-d') || $event->event_status == 'O'  && $event->registration_start <= date ('Y-m-d') ){?> 	
							<li><a href="<?php echo get_option('siteurl')?>/?page_id=<?php echo $event_page_id?>&regevent_action=register&event_id=<?php echo $event_id?>&name_of_event=<?php echo stripslashes($event_name)?>"><?php echo stripslashes($event_name)?> - <?php echo event_date_display($start_date)?></a></li>
	<?php 					}
						}
	?>
					</ul></div>
	<?php
		
			/* After widget (defined by themes). */
			echo $after_widget;
			}
			
		/* Update the widget settings. */
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
	
			/* Strip tags for title and name to remove HTML (important for text inputs). */
			$instance['title'] = strip_tags( $new_instance['title'] );
			
			return $instance;
		}
	
		/**
		 * Displays the widget settings controls on the widget panel.
		 * Make use of the get_field_id() and get_field_name() function
		 * when creating your form elements. This handles the confusing stuff.
		 */
		function form( $instance ) {
	
			/* Set up some default widget settings. */
			$defaults = array( 'title' => __('Upcoming Events', 'events') );
			$instance = wp_parse_args( (array) $instance, $defaults ); ?>
	
			<!-- Widget Title: Text Input -->
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'Upcoming Events'); ?></label>
				<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
			</p>
	
	<?php
		}
	
	}
}