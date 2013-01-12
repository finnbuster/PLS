<?php 
//This is the event list template page.
//This is a template file for displaying an event lsit on a page.
//There should be a copy of this file in your wp-content/uploads/espresso/ folder.
/*
* use the following shortcodes in a page or post:
* [EVENT_LIST]
* [EVENT_LIST limit=1]
* [EVENT_LIST show_expired=true]
* [EVENT_LIST show_deleted=true]
* [EVENT_LIST show_secondary=true]
* [EVENT_LIST show_recurrence=true]
* [EVENT_LIST category_identifier=your_category_identifier]
*
* Example:
* [EVENT_LIST limit=5 show_recurrence=true category_identifier=your_category_identifier]
*
*/
$first_event_instance = $events_group[0];
$first_event_excerpt = array_shift(explode('<!--more-->', html_entity_decode($first_event_instance['event_desc'])));

?>

<div id="event_data-<?php echo $first_event_instance['event_id']?>" class="event_data subpage_excerpt r <?php echo $css_class; ?> <?php echo $category_identifier; ?> event-data-display event-list-display event-display-boxes">
    
    <h2 id="event_title-<?php echo $first_event_instance['event_id']?>" class="event_title">
			<?php echo stripslashes_deep($first_event_instance['event_name'])?></h2>
	
	<?php if (count($events_group) > 1) :
			//Show short descriptions
			if ($first_event_excerpt != '' && isset($org_options['display_short_description_in_event_list']) && $org_options['display_short_description_in_event_list'] == 'Y') { ?>
	   			 <p><?php echo stripslashes_deep(wpautop($first_event_excerpt)); ?></p>
        <?php }?>
	    
	    <script type="text/javascript">
            $jaer = jQuery.noConflict();
        </script>
        
        <p><a href="#" onClick="$jaer('#date_picker_<?php echo $first_event_instance['event_id']?>').slideDown('slow');$jaer(this).parent().slideUp('slow');return false;" class="register_date" id="register_date_<?php echo $first_event_instance['event_id']?>">Register Now</a></p>
        <div class="date_picker" id="date_picker_<?php echo $first_event_instance['event_id']?>">
            <h6 style="margin-top:20px; top:-4px"><?php _e('Please Select a Date', 'event_espresso'); ?></h6>
            
            <ul>
        	    <?php foreach ($events_group as $e) :
                    $num_attendees = get_number_of_attendees_reg_limit($e['event_id'], 'num_attendees');//Get the number of attendees. Please visit http://eventespresso.com/forums/?p=247 for available parameters for the get_number_of_attendees_reg_limit() function.
                    echo '<li>';
                    if ($num_attendees >= $e['reg_limit']) : 
                        echo '<span class="error">';
                    else :
                        echo '<a href="'.$e['registration_url'].'">';
                    endif;
                    if ($e['start_date'] != $e['end_date']) : 
                        echo event_date_display($e['start_date'], 'F j, Y').'–'.event_date_display($e['end_date'], 'F j, Y'); 
                    else : 
                        echo event_date_display($e['start_date'], 'l, F j, Y');
                    endif;
                    if ($num_attendees >= $e['reg_limit']) : 
                        echo ' Sold Out</span> <a href="'.get_option('siteurl').'/?page_id='.$e['event_page_id'].'&e_reg=register&event_id='.$e['overflow_event_id'].'&name_of_event='.stripslashes_deep($e['event_name']).'">'.__('(Join Waiting List)').'</a>';
                    else :
                        echo '</a>';
                    endif;
                    echo '</li>';
                endforeach; ?>
            </ul>
            
        </div>
        
    <?php else : ?>
        
        <h6><?php echo event_date_display($events_group[0]['start_date'], get_option('date_format')); 
            if ($events_group[0]['start_date'] != $events_group[0]['end_date']) :
            echo '–'.event_date_display($events_group[0]['end_date'], get_option('date_format'));
            endif; ?>
        </h6>
        
        <?php //Show short descriptions
			if ($first_event_excerpt != '' && isset($org_options['display_short_description_in_event_list']) && $org_options['display_short_description_in_event_list'] == 'Y') { ?>
	   			 <p><?php echo stripslashes_deep(wpautop($first_event_excerpt)); ?></p>
        <?php }?>
        <?php $num_attendees = get_number_of_attendees_reg_limit($first_event_instance['event_id'], 'num_attendees'); ?>
        <?php if ($num_attendees >= $events_group[0]['reg_limit']) : ?>
            
            <p><span class="error">Sold Out</span> <a href="<?php echo get_option('siteurl')?>/?page_id=<?php echo $first_event_instance['event_page_id']?>&e_reg=register&event_id=<?php echo $first_event_instance['overflow_event_id']?>&name_of_event=<?php echo stripslashes_deep($first_event_instance['event_name'])?>" title="<?php echo stripslashes_deep($first_event_instance['event_name'])?>"><?php _e('Join Waiting List', 'event_espresso'); ?></a></p> 
            
        <?php else : ?>
            
            <p><a href="<?php echo $first_event_instance['registration_url']; ?>" title="<?php echo stripslashes_deep($first_event_instance['event_name'])?>"><?php _e('Register Now', 'event_espresso'); ?></a></p>
        
        <?php endif; ?>
    <?php endif; ?>

</div>