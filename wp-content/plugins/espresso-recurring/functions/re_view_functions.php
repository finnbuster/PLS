<?php
/**
 * Event Espresso Recurring Events View Functions
 *
 * Outputting Functions
 *
 * @package		Event Espresso
 * @subpackage          Recurring Events
 * @author		Abel Sekepyan
 * @link		http://eventespresso.com/support/
 */
function event_espresso_re_form( $recurrence_id = null ) {
    if ( !function_exists( 'recurrence_table_manual' ) )
        require('re_functions.php');
	
    global $wpdb;
    $recurrence_edit_mode = false;
//If recurrence_id is supplied, then this is a recurring event
    if ( $recurrence_id  > 0 )
    {
        $recurrence_edit_mode = true;
        $result = $wpdb->get_row( 'SELECT re.*
                                    FROM ' . EVENT_ESPRESSO_RECURRENCE_TABLE . ' re
                                    INNER JOIN ' . EVENTS_DETAIL_TABLE . ' ed
                                    ON re.recurrence_id = ed.recurrence_id
                                    WHERE re.recurrence_id = ' . $recurrence_id .
                        ' ORDER BY ed.start_date ASC
                                    LIMIT 1', ARRAY_A );

        extract( $result );
    }
?>
    <div style="display: block;" id="recurring_event_wrapper" class="postbox">
        <div class="handlediv" title="Click to toggle"><br>
        </div>
        <h3 class="hndle"><span>
            <?php _e( 'Recurring Event Manager', 'event_espresso' ) ?>
        </span></h3>
    <div class="inside recurring_events_wrapper">

        <span class="">
            <?php

            if ( $recurrence_edit_mode == false )
            {
            ?>
                <div class="ui-state-highlight ui-corner-all">
                    <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>

                    <?php _e( 'IF THIS IS A RECURRING EVENT, A RECORD WILL BE CREATED FOR EACH INSTANCE. ', 'event_espresso' ); ?>
                </p>
            </div>

            <?php } ?>
            </span>

        <?php if ( $_REQUEST['action'] == 'edit' ) : ?>
                    <div id="recurrence_message" class="recurrence_message re_fr ui-corner-all" style="">
            <?php _e( 'Would you like to apply any of the changes that you have made to this event to:', 'event_espresso' ); ?>
                    <span>
                        <br />
                        <br /><input type="radio" name="recurrence_apply_changes_to" value="1" checked="checked" />&nbsp;
                        <label><?php _e( 'Only this instance.', 'event_espresso' ); ?></label>
                        <br /><input type="radio" name="recurrence_apply_changes_to" value="2" />&nbsp;
                        <label><?php _e( 'All events in the series', 'event_espresso.' ); ?></label>
                        <br /><input type="radio" name="recurrence_apply_changes_to" value="3" />&nbsp;
                        <label><?php _e( 'This and all upcoming events.', 'event_espresso' ); ?></label>&nbsp;
                    </span>
                </div>
        <?php endif; ?>

                    <div><label><?php _e( 'Is this a recurring event?', 'event_espresso' ); ?></label>
                        <select name="recurrence" id="recurrence">
                            <option value="N"><?php _e( 'No', 'event_espresso' ); ?></option>
                            <option value="Y" <?php echo $recurrence_edit_mode ? 'selected="selected"' : ''; ?>><?php _e( 'Yes', 'event_espresso' ); ?></option>
                        </select>
                    </div>


                    <div class="recurring_events_details">


                        <ul>
                            <li>
                                <label><?php _e( 'Create dates automatically or select manually?', 'event_espresso' ); ?></label>

                                <select name="recurrence_type" id="recurrence_type">
                        <?php if ( ($recurrence_edit_mode && $recurrence_type == 'a') || !$recurrence_edit_mode ): ?>
                            <option value="a"><?php _e( 'Automatic', 'event_espresso' ); ?></option>
                        <?php endif; ?>
                        <?php if ( ($recurrence_edit_mode && $recurrence_type == 'm') || !$recurrence_edit_mode ): ?>
                                <option value="m"><?php _e( 'Manual', 'event_espresso' ); ?></option>
                        <?php endif; ?>
                            </select>
                            <a class="thickbox" href="#TB_inline?height=300&width=400&inlineId=info_recurrence_type"><img src="<?php echo EVENT_ESPRESSO_PLUGINFULLURL ?>/images/question-frame.png" width="16" height="16" /></a>
                        </li>
                        <li>
                            <div class="">
                                <div class="ui-state-highlight ui-corner-all" style="clear:both;">
                                    <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>

                                <?php _e( 'If you plan on using different registration dates for each event,
                                            please enter the First Event\'s Registration Start/End Dates below.
                                            Otherwise, enter the registration dates that will cover all events in the series.', 'event_espresso' ); ?>
                            </p>
                        </div>
                        <p><b><?php _e( 'Registration Starts On:', 'event_espresso' ); ?></b><br />

                            <input type="text" class="required datepicker" size="15" name="recurrence_regis_start_date" id="recurrence_regis_start_date" value="<?php echo $recurrence_edit_mode && isset( $recurrence_regis_start_date ) ? $recurrence_regis_start_date : ''; ?>" />
                        </p>
                        <p>
                            <b><?php _e( 'Registration Ends On:', 'event_espresso' ); ?></b> <br />

                            <input type="text" class="required datepicker" size="15" name="recurrence_regis_end_date" id="recurrence_regis_end_date" value="<?php echo $recurrence_edit_mode ? $recurrence_regis_end_date : ''; ?>" />
                        </p>

                    </div>
                </li>
                <li>
                    <p>
                        <b><?php _e( 'Are all events available between the registration dates above?', 'event_espresso' ); ?></b>
                        <a class="thickbox" href="#TB_inline?height=300&width=400&inlineId=info_reg_formula"><img src="<?php echo EVENT_ESPRESSO_PLUGINFULLURL ?>/images/question-frame.png" width="16" height="16" /></a>
                        <br /><span>

                            <input type="radio" name="recurrence_regis_date_increment" value="N" <?php echo $recurrence_edit_mode && $recurrence_regis_date_increment == 'N' ? 'checked="checked"' : ''; ?> />

                            <?php _e( "No (each event's registration start and end dates will be incremented according to a formula).", 'event_espresso' ); ?>
                            </span><br />
                            <span>
                                <input type="radio" name="recurrence_regis_date_increment" value="Y" <?php echo $recurrence_edit_mode && $recurrence_regis_date_increment == 'Y' ? 'checked="checked"' : ''; ?>/>
                            <?php _e( "Yes (all created events are available for registration between the above registration dates).", 'event_espresso' ); ?>
                            </span>

                        </p>
                    </li>
                    <!--<li>
                        <div class="">
                            <p><b><?php _e( 'Each Event\'s Visibility: ', 'event_espresso' ); ?>
                                    <input type="text" class="required" size="5" name="recurrence_visibility" id="recurrence_visibility" value="<?php echo $recurrence_edit_mode ? $recurrence_visibility : ''; ?>" />
                                </b><br />
                            <table class="left_align">
                                <tr><td class="label"><?php _e( 'Blank', 'event_espresso' ); ?></td><td><?php _e( ' Visible Immediately', 'event_espresso' ); ?></td></tr>
                                <tr><td class="label">0</td><td><?php _e( 'Visible on Registration Start Date', 'event_espresso' ); ?></td></tr>
                                <tr><td class="label">>0</td><td><?php _e( ' Visible X Days Before Registration Start Date', 'event_espresso' ); ?></td></tr>
                            </table>
                        </div>
                    </li>-->

                    <li>
                        <!-- Fields for automatic recurrence creation -->

                        <div id="recurrence_automatic" class="recurrence_type">

                            <div class="">
                                <p><b><?php _e( 'First Event Date:', 'event_espresso' ); ?></b>

                                    <input type="text" class="required datepicker" size="15" name="recurrence_start_date" id="recurrence_start_date" value="<?php echo $recurrence_edit_mode && isset( $recurrence_start_date ) ? $recurrence_start_date : ''; ?>" />

                                    <b><?php _e( 'First Event End Date:', 'event_espresso' ); ?></b>

                                    <input type="text" class="required datepicker" size="15" name="recurrence_event_end_date" id="recurrence_event_end_date" value="<?php echo $recurrence_edit_mode && isset( $recurrence_event_end_date ) ? ($recurrence_event_end_date != '0000-00-00'?$recurrence_event_end_date:'') : ''; ?>" />


                                </p>    
                                <p>
                                    <b><?php _e( 'Last Event Date:', 'event_espresso' ); ?></b>

                                    <input type="text" class="required datepicker" size="15" name="recurrence_end_date" id="recurrence_end_date" value="<?php echo $recurrence_edit_mode ? $recurrence_end_date : ''; ?>" />
                                </p>

                            </div>
                            <br />


                        <?php

//used for js display
                                $input_labels = array(
                                    'd' => 'days',
                                    'w' => 'weeks',
                                    'm' => 'months'
                                );
                        ?>
                                <label><?php _e( 'Event Repeats:', 'event_espresso' ); ?></label>
                                <select name="recurrence_frequency" id="recurrence_frequency">

                                    <option value="d"  <?php echo ($recurrence_edit_mode && $recurrence_frequency == 'd') ? 'selected="selected"' : ''; ?>><?php _e( 'Daily', 'event_espresso' ); ?></option>
                                    <option value="w"  <?php echo ($recurrence_edit_mode && $recurrence_frequency == 'w') ? 'selected="selected"' : ''; ?>><?php _e( 'Weekly', 'event_espresso' ); ?></option>
                                    <option value="m"  <?php echo ($recurrence_edit_mode && $recurrence_frequency == 'm') ? 'selected="selected"' : ''; ?>><?php _e( 'Monthly', 'event_espresso' ); ?></option>

                                </select>
                                <br />
                                <label><?php _e( 'Every:', 'event_espresso' ); ?></label>
                                <select name="recurrence_interval" id="re_interval">

                            <?php for ( $i = 1;
                                            $i < 31; $i++ ): ?>

                                    <option value="<?php echo $i; ?>" <?php echo ($recurrence_edit_mode && $i == $recurrence_interval) ? 'selected="selected"' : ''; ?>><?php echo $i; ?></option>

                            <?php endfor; ?>

                                </select>
                                &nbsp;<span id="recurrence_period_other">

                            <?php echo ($recurrence_edit_mode) ? $input_labels[$recurrence_frequency] : 'days'; ?>


                                </span>
                                <br />
                                <div id="recurrence_weekday_on" class="">
                                    <h4><?php _e( 'Repeats On:', 'event_espresso' ); ?></h4>
                            <?php

// check the weekday checkboxes

                                    $recurrence_weekday = ($recurrence_edit_mode && ($recurrence_weekday != '' && $recurrence_weekday != 'N;')) ? unserialize( $recurrence_weekday ) : array( );

                                    $weekdays_shifted = array( );

                                    foreach ( $recurrence_weekday as $k => $v )
                                        $weekdays_shifted[$v] = $v;

                                    $weekday_checked = array( );
                                    for ( $i = 0; $i < 8; $i++ ) {

                                        $weekday_checked[$i] = array_key_exists( $i, $weekdays_shifted ) ? 'checked="checked"' : '';
                                    }
                            ?>
                                    <input type="checkbox" <?php echo $weekday_checked[0]; ?> id="recurrence_weekday_0" name="recurrence_weekday[]" value="0">
                                    <label for="recurrence_weekday_0"><?php _e( 'Sun', 'event_espresso' ); ?></label>

                                    <input type="checkbox" <?php echo $weekday_checked[1]; ?> id="recurrence_weekday_1" name="recurrence_weekday[]" value="1">
                                    <label for="recurrence_weekday_1"><?php _e( 'Mon', 'event_espresso' ); ?></label>


                                    <input type="checkbox" <?php echo $weekday_checked[2]; ?> id="recurrence_weekday_2" name="recurrence_weekday[]" value="2">
                                    <label for="recurrence_weekday_2"><?php _e( 'Tue', 'event_espresso' ); ?></label>


                                    <input type="checkbox" <?php echo $weekday_checked[3]; ?> id="recurrence_weekday_3" name="recurrence_weekday[]" value="3">
                                    <label for="recurrence_weekday_3"><?php _e( 'Wed', 'event_espresso' ); ?></label>

                                    <input type="checkbox" <?php echo $weekday_checked[4]; ?> id="recurrence_weekday_4" name="recurrence_weekday[]" value="4">
                                    <label for="recurrence_weekday_4"><?php _e( 'Thu', 'event_espresso' ); ?></label>


                                    <input type="checkbox" <?php echo $weekday_checked[5]; ?> id="recurrence_weekday_5" name="recurrence_weekday[]" value="5">
                                    <label for="recurrence_weekday_5"><?php _e( 'Fri', 'event_espresso' ); ?></label>

                                    <input type="checkbox" <?php echo $weekday_checked[6]; ?> id="recurrence_weekday_6" name="recurrence_weekday[]" value="6">
                                    <label for="recurrence_weekday_6"><?php _e( 'Sat', 'event_espresso' ); ?></label>


                                </div>
                                <div id="recurrence_repeat_by" class="">
                                    <h4><?php _e( 'Repeat By:', 'event_espresso' ); ?></h4>

                                    <span>
                                        <input type="radio" name="recurrence_repeat_by" value="dom" checked="checked">  <label><?php _e( 'Day of Month', 'event_espresso' ); ?></label>

                                        <input type="radio" name="recurrence_repeat_by" value="dow"><label><?php _e( 'Day of Week', 'event_espresso' ); ?></label>&nbsp;
                                    </span>

                                </div>


                            </div>
                        </li>
                        <li>
                            <!-- Fields for automatic recurrence creation -->
                            <div id="recurrence_manual" class="">

                                <div class="ui-state-highlight ui-corner-all">
                                    <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>

                                <?php _e( 'You can add as many days as you would like.  Start with the very first event date.', 'event_espresso' ); ?>
                                </p>
                            </div>

                            <div id="recurrence_manual_date_wrapper">
                                <table id="recurrence_manual_date_table">
                                <?php

                                    if ( $recurrence_edit_mode && $recurrence_type == 'm' )
                                    {

                                        recurrence_table_manual( $recurrence_id, '' );
                                    }
                                ?>
                                    <tr><td>
                                        <?php _e( 'Event Date:', 'event_espresso' ); ?>
                                    </td>
                                    <td>
                                        <input type="text" class="required datepicker" size="15" name="recurrence_manual_dates[]" value="" />
                                    </td>
                                    <td>
                                        <?php _e( ' to ', 'event_espresso' ); ?><input type="text" class="required datepicker" size="15" name="recurrence_manual_end_dates[]" value="" />
                                    </td>
                                    <td>
                                        <img  class="recurrence_remove_manual_dates" src=" <?php echo EVENT_ESPRESSO_PLUGINFULLURL; ?>images/icons/remove.gif" alt="Delete" />
                                    </td>
                                </tr>
                            </table>
                            <span><img  class="recurrence_add_manual_dates" src=" <?php echo EVENT_ESPRESSO_PLUGINFULLURL; ?>images/icons/add.png" alt="Add" /></span>
                        </div>

                    </div>

                </li>
            </ul>
        </div>


        <table style="width:98%"  id="recurrence_summary">
            <tr>
								<?php  if ( $recurrence_edit_mode && $recurrence_type == 'a' ){ ?>
								<td>
                    <div class="ov_scroll">
                        <?php

                                      

                                            $results = $wpdb->get_results( "SELECT * FROM " . EVENTS_DETAIL_TABLE . " as ed WHERE ed.event_status = 'A' AND ed.recurrence_id = $recurrence_id AND start_date != '' ORDER BY ed.start_date ", ARRAY_A );

                                            if ( $wpdb->num_rows > 0 )
                                            {
                                                recurrence_table( $results, '<p>' . __( 'Other events in this series.', 'event_espresso' ) . '</p>' );
                                            }
                                      ?>
                        
                                    </div>
                                </td>
																	<?php } ?>
                                <td>

                                    <div  id="recurrence_ajax_response" class="ov_scroll">

                                        <p><strong><?php _e( 'Immediate Response Window', 'event_espresso' ); ?></strong></p>
                                    </div>



                                </td></tr>
                        </table>
                    </div>
                    <input type="hidden" id = "recurrence_field_changed" value="0" />
                </div>
<?php question_box() ?>
                                        <script type="text/javascript">


                                            //jeere : jQuery Event Espresso Recurring Events
                                            jeere = jQuery.noConflict();
                                            //GLOBAL
                                            function get_recurrence_change(){

                                                var ajax_loader_img = '<img src="' + '<?php echo EVENT_ESPRESSO_RECURRENCE_FULL_URL ?>' + 'images/ajax-loader.gif" alt="Recurring Event" />';

                                                /*var rm = jeere('#recurrence_message');
                                                                            if( !rm.is(':visible') ){
                                                                            rm.slideDown('slow');
                                                                        }*/

                                                jeere('#recurrence_ajax_response').html(ajax_loader_img);

                                                //if you assign a pre-existing key to recurrence_weekday, this won't work.  Ex. recurrence_weekday[0]
                                                var weekday_values = [];
                                                jeere.each(jeere("input[name='recurrence_weekday[]']:checked"), function() {
                                                    weekday_values.push(jeere(this).val());
                                                });
                                                var recurrence_manual_dates = [];
                                                jeere.each(jeere("input[name='recurrence_manual_dates[]']"), function() {
                                                    recurrence_manual_dates.push(jeere(this).val());
                                                });
                                                var recurrence_manual_end_dates = [];
                                                jeere.each(jeere("input[name='recurrence_manual_end_dates[]']"), function() {
                                                    recurrence_manual_end_dates.push(jeere(this).val());
                                                });

                                                var data = {
                                                    action: 'show_recurring_dates',
                                                    start_date: jeere("input[name='start_date']").val(),
                                                    recurrence_start_date: jeere("input[name='recurrence_start_date']").val(),
                                                    recurrence_event_end_date: jeere("input[name='recurrence_event_end_date']").val(),
                                                    recurrence_end_date: jeere("input[name='recurrence_end_date']").val(),
                                                    recurrence_regis_start_date: jeere("input[name='recurrence_regis_start_date']").val(),
                                                    recurrence_regis_end_date: jeere("input[name='recurrence_regis_end_date']").val(),
                                                    recurrence_frequency: jeere("select[name='recurrence_frequency']").val(),
                                                    recurrence_interval: jeere("select[name='recurrence_interval']").val(),
                                                    recurrence_type: jeere("select[name='recurrence_type']").val(),
                                                    recurrence_manual_dates: recurrence_manual_dates,
                                                    recurrence_manual_end_dates: recurrence_manual_end_dates,
                                                    recurrence_weekday: weekday_values,
                                                    recurrence_repeat_by: jeere("input[name='recurrence_repeat_by']:checked").val(),
                                                    recurrence_regis_date_increment: jeere("input[name='recurrence_regis_date_increment']:checked").val(),
                                                    recurrence_apply_changes_to: jeere("input[name='recurrence_apply_changes_to']:checked").val(),
                                                    recurrence_visibility: jeere("input[name='recurrence_visibility']").val(),
                                                    recurrence_id: jeere("input[name='recurrence_id']").val()
                                                };

                                                //Pull the projected event recurrences.
                                                jeere.post(ajaxurl, data, function(response) {
                                                    jeere('#recurrence_ajax_response').html( response);
                                                });




                                            }

                                            //Fire the function above based on the changes.

                                            jeere('.recurring_events_details :input, input[name="recurrence_apply_changes_to"]').change(function(){

                                                //Dont't fire if only this instance is being changed
                                                if (jeere(this).attr('name') == 'recurrence_apply_changes_to' && (jeere('#recurrence_field_changed').val() == 0 && jeere('input[name="recurrence_apply_changes_to"]:checked').val() == 1))
                                                    return false;
                                                //Temporary, will ocmbine later
                                                if (jeere(this).attr('name') == 'recurrence_apply_changes_to' && jeere('#recurrence_field_changed').val() == 0)
                                                    return false;

                                                // RE form changed
                                                jeere('#recurrence_field_changed').val(1);
                                                //If RE form changed and Only this instance is selected, warn
                                                if (jeere('input[name="recurrence_apply_changes_to"]:checked').val() == 1 && jeere('#recurrence_field_changed').val() == 1) {
                                                    alert('<?php _e( 'If you are making changes to the recurrence formula, please select "All events in the series" or "All upcoming events" for the changes to take effect.', 'event_espresso' ) ?>');
                                                    return false;
                                                }

                                                if(jeere('#recurrence_type').val() =='a')
                                                    get_recurrence_change();


                                            });


                                            // END GLOBAL



                                            jQuery(document).ready(function(jeere) {

                                                /*jeere('#recurrence_manual_date_wrapper :input').live("change", function(){

                                                                            // If using the clone method below, the last selected date will be automatically in
                                                                            // the newly created field.  May confuse the user but can be useful
                                                                            // Will wait for user input
                                                                            //var template = jeere('#recurrence_manual_date_table tr:last').clone();

                                                                            //jeere('input[name="recurrence_apply_changes_to"]')[1].checked = true;
                                                                            jeere('input[name="recurrence_apply_changes_to"][value="2"]').attr("checked", true);
                                                                            var template = jeere('#recurrence_manual_date_table tr:last').html();

                                                                            jeere('#recurrence_manual_date_table').append('<tr>' + template + '</tr>');
                                                                            jeere('#recurrence_manual_date_wrapper :input').removeClass('hasDatepicker').removeAttr('id')
                                                                            .datepicker({
                                                                                changeMonth: true,
                                                                                changeYear: true,
                                                                                dateFormat: "yy-mm-dd",
                                                                                showButtonPanel: true}
                                                                        );
                                                                            return false;
                                                                        });*/
                                                jeere('.recurrence_add_manual_dates').click(function(){

                                                    // If using the clone method below, the last selected date will be automatically in
                                                    // the newly created field.  May confuse the user but can be useful
                                                    // Will wait for user input
                                                    //var template = jeere('#recurrence_manual_date_table tr:last').clone();

                                                    //jeere('input[name="recurrence_apply_changes_to"]')[1].checked = true;
                                                    jeere('input[name="recurrence_apply_changes_to"][value="2"]').attr("checked", true);
                                                    var template = jeere('#recurrence_manual_date_table tr:last').html();

                                                    jeere('#recurrence_manual_date_table').append('<tr>' + template + '</tr>');
                                                    jeere('#recurrence_manual_date_wrapper :input').removeClass('hasDatepicker').removeAttr('id')
                                                    .datepicker({
                                                        changeMonth: true,
                                                        changeYear: true,
                                                        dateFormat: "yy-mm-dd",
                                                        showButtonPanel: true}
                                                );
                                                    return false;
                                                });
                                                jeere('.recurrence_remove_manual_dates').live("click", function(){

                                                    //if (confirm('Are you sure?') === false)
                                                    //    return false;

                                                    if(jeere("#recurrence_manual_date_table tr").size() == 1)
                                                    {
                                                        alert ("First element can't be deleted.");
                                                        return false;
                                                    }
                                                    jeere(this).parents().eq(1).slideUp().remove();

                                                    //jeere('input[name="recurrence_apply_changes_to"]').val(2);
                                                    //jeere('input[name="recurrence_apply_changes_to"]')[1].checked = true;
                                                    jeere('input[name="recurrence_apply_changes_to"][value="2"]').attr("checked", true);

                                                });

                                                jeere("#tabs").tabs({ selected: 1 });


                                                //Hide the RE forms on page load based on what is in the fields
                                                hide_fields ();


                                                /*jeere('#start_date').change(function(){

                                                                            jeere('#recurrence_start_date').val(jeere(this).val());

                                                                        });*/

                                                //Hide/show fields depending if RE or not
                                                jeere('#recurrence').change(function(){

                                                    hide_fields();

                                                });

                                                /*jeere('.recurring_events_details input[name!="recurrence_apply_changes_to"]').change(function(){
                                                                            //jeere('#recurrence_field_changed').val(1);
                                                                        });*/



                                                //Will be used before submit to check for required fields
                                                var recurrence_required = ['start_date', 'recurrence_end_date','recurrence_interval'];

                                                //Show and hide weekdays and monthly fields based on frequency selection of weekly or monthly
                                                jeere('#recurrence_frequency').change(function(){
                                                    hide_fields ();
                                                    return false;

                                                });
                                                jeere('#recurrence_type').change(function(){
                                                    hide_fields ();
                                                    return false;

                                                });

                                                //Hide the RE forms based on what RE fields are selected
                                                function hide_fields (){

                                                    if (jeere('#recurrence').val() == 'N') {
                                                        jeere('.recurring_events_details').slideUp(400);
                                                        jeere('.ov_scroll').hide();

                                                    } else {
                                                        jeere('.recurring_events_details').slideDown(400);
                                                        jeere('.ov_scroll').show();
                                                    }

                                                    if (jeere('#recurrence_type').val() == 'm') {
                                                        jeere('#recurrence_automatic').slideUp(400);
                                                        jeere('#recurrence_manual').slideDown(400);
                                                        jeere('#recurrence_summary').slideUp(400);
                                                    } else {
                                                        jeere('#recurrence_manual').slideUp(400);
                                                        jeere('#recurrence_automatic').slideDown(400);
                                                        jeere('#recurrence_summary').slideDown(400);
                                                    }

                                                    var val = jeere("#recurrence_frequency").val();

                                                    switch (val) {
                                                        case 'd':
                                                            jeere('#recurrence_period_other').html('<?php _e( 'days', 'event_espresso' ); ?>');
                                                            jeere('#recurrence_weekday_on').hide();
                                                            jeere('#recurrence_repeat_by').hide();

                                                            break;
                                                        case 'w':
                                                            jeere('#recurrence_period_other').html('<?php _e( 'weeks', 'event_espresso' ); ?>');
                                                            jeere('#recurrence_weekday_on').show();
                                                            jeere('#recurrence_repeat_by').hide();
                                                            break;
                                                        case 'm':
                                                            jeere('#recurrence_period_other').html('<?php _e( 'months', 'event_espresso' ); ?>');
                                                            jeere('#recurrence_repeat_by').show();
                                                            jeere('#recurrence_weekday_on').hide();
                                                            break;
                                                        default:
                                                            jeere('#recurrence_period_other').html('');
                                                            jeere('#recurrence_repeat_by').html('');
                                                            break;
                                                        }

                                                    }



                                                    jeere('form').submit(function(){
                                                        //alert(jeere('form').serialize());
                                                        //alert(jeere("input[name='recurrence_weekday[]']:checked").length);
                                                        //check_inputs();
                                                        //return false;

                                                    });

                                                    //Will be used in the next version to check for required fields
                                                    function check_inputs(){

                                                        if(jeere('#recurrence').val() == 'N'){

                                                            return false;
                                                        }

                                                        var l = recurrence_required.length;
                                                        for (var i = 0;  i < l; i++) {
                                                            if (jeere('#'+recurrence_required[i]).val() == '') {
                                                                jeere('#'+recurrence_required[i]).css({'background-color' :'pink'});
                                                            }


                                                        }
                                                        return false;


                                                    };







                                                });


                                        </script>



<?php

                                    }


                                    function recurrence_table( $arr, $label, $from_arr = 0  ) {
                                        //Called by ajax and in edit view
                                        $counter = 1;
                                        
                                        

                                        echo "<h4>" . __( "$label", 'event_espresso' ) . "</h4>";
                                        echo "<table class='recurring_summary'>";
                                        echo "<tr>";
                                        echo "<th></th>";
                                        echo "<th>" . __( 'Event Date', 'event_espresso' ) . "</th>";
                                        echo "<th>" . __( 'Event End Date', 'event_espresso' ) . "</th>";
                                        echo "<th>" . __( 'Registration Start', 'event_espresso' ) . "</th>";
                                        echo "<th>" . __( 'Registration End', 'event_espresso' ) . "</th>";
                                        //echo "<th>" . __( 'Visible On', 'event_espresso' ) . "</th>";
                                        echo "</tr> \n";
										if (!is_array($arr)){
											return '<p>'.__('Please select a day of the week above.', 'event_espresso').'</p>';
										}
                                        foreach ( $arr as $result ) {
                                            extract( $result );
                                                
                                                $end_date = $from_arr == 1?$event_end_date:$end_date;

                                            echo "<tr>";
                                            echo "<td>" . $counter . "</td>";
                                            echo "<td>" . date( EVENT_ESPRESSO_RECURRENCE_DATE_FORMAT, strtotime( $start_date ) ) . "</td>";
                                            echo "<td>" . date( EVENT_ESPRESSO_RECURRENCE_DATE_FORMAT, strtotime( $end_date ) ) . "</td>";
                                            echo "<td>" . date( EVENT_ESPRESSO_RECURRENCE_DATE_FORMAT, strtotime( $registration_start ) ) . "</td>";
                                            echo "<td>" . date( EVENT_ESPRESSO_RECURRENCE_DATE_FORMAT, strtotime( $registration_end ) ) . "</td>";
                                            //echo "<td>" . date( EVENT_ESPRESSO_RECURRENCE_DATE_FORMAT, strtotime( $visible_on ) ) . "</td>";
                                            echo "</tr> \n";

                                            $counter++;
                                        }

                                        echo "</table>";
                                    }


                                    function recurrence_table_manual( $recurrence_id, $label, $from_arr = 0 ) {
                                        global $wpdb;
                                        $results = $wpdb->get_results( "SELECT edt.*, count(eat.id) as attendee_cnt FROM " . EVENTS_DETAIL_TABLE . " edt
                                                LEFT JOIN " . EVENTS_ATTENDEE_TABLE . " eat
                                                    ON edt.id = eat.event_id
                                                WHERE edt.event_status = 'A'
                                                    AND edt.recurrence_id = $recurrence_id
                                                GROUP BY edt.id
                                                ORDER BY edt.start_date ", ARRAY_A );

                                        if ( $wpdb->num_rows == 0 )
                                        {
                                            return false;
                                        }

                                        foreach ( $results as $result ) {
                                            extract( $result );
                                            $end_date = $from_arr == 1?$event_end_date:$end_date;
?>
                                            <tr><td>
        <?php _e( 'Event Date:', 'event_espresso' ); ?>
                                        </td>
                                        <td>
                                            <input type="text" class="required datepicker" size="15" name="recurrence_manual_dates[]" value="<?php echo date( 'Y-m-d', strtotime( $start_date ) ); ?>" />
                                        </td>
                                        <td>
                                            <?php _e( ' to ', 'event_espresso' ); ?><input type="text" class="required datepicker" size="15" name="recurrence_manual_end_dates[]" value="<?php echo date( 'Y-m-d', strtotime( $end_date ) ); ?>" />
                                        </td>
                                        <td>
        <?php if ( $attendee_cnt > 0 ):; ?>
        <?php echo $attendee_cnt . __( ' attendees.  Can\'t be deleted until attendees are deleted.', 'event_espresso' ); ?>
        <?php else: ?>
                                                    <img  class="recurrence_remove_manual_dates" src=" <?php echo EVENT_ESPRESSO_PLUGINFULLURL; ?>images/icons/remove.gif" alt="Delete" />
        <?php endif; ?>
                                                </td>
                                            </tr>
<?php

                                                }
                                            }


                                            function question_box() {
?>
                                                <div id="info_reg_formula" style="display:none">
    <?php _e( '<h2>Registration Date Usage</h2>
      <p>
      <ul>
      <li>
No:  Use this if each event in the series will have its own registration start and end dates.
    <b>Make sure to enter the <u>First Event\'s Registration Start and End dates above</u>.</b>
    </li>
    <li>
Yes: Use this if all events are only available between the above registration start and end dates.
    </li>
</p>', 'event_espresso' ); ?>
                                            </div>

                                            <div id="info_recurrence_type" style="display:none">
    <?php _e( '<h2>Recurrence Type</h2>
      <p>
      <ul>
      <li>
Automatic:  The events will be created automatically, based on the information that you select below.
    </li>
    <li>
Manual: You will be given the option to manually select as many different dates as you would like.
    </li>
NOTE: Once you create events in Automatic mode, those events can\'t be switched to Manual, and vise versa.
</ul>
</p>', 'event_espresso' ); ?>
                                            </div>

<?php

											}