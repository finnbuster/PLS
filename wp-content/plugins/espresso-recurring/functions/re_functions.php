<?php

/**
 * Event Espresso Recurring Events Helper Functions
 *
 *
 * @package		Event Espresso
 * @subpackage          Recurring Events
 * @author		Abel Sekepyan
 * @link		http://eventespresso.com/support/
 */
/**
 * MAJOR CLEANUP PLANNED FOR V1.2.0
 * - better comments
 * - more consolidation
 */


/**
 * Create a new master recurrence record
 *
 * @access	public
 * @param	$_POST
 * @return	int - insert id
 */
function add_recurrence_master_record() {

    global $wpdb;

    $recurrence_weekday = serialize( $_POST['recurrence_weekday'] );
    $recurrence_manual_dates = $_POST['recurrence_type'] == 'm' ? serialize( $_POST['recurrence_manual_dates']) . serialize($_POST['recurrence_manual_end_dates'] ) : NULL;

    $SQL = 'INSERT INTO ' . EVENT_ESPRESSO_RECURRENCE_TABLE . ' (
                                        recurrence_start_date,
                                        recurrence_event_end_date,
                                        recurrence_end_date,
                                        recurrence_regis_start_date,
                                        recurrence_regis_end_date,
                                        recurrence_type,
                                        recurrence_frequency,
                                        recurrence_interval,
                                        recurrence_weekday,
                                        recurrence_repeat_by,
                                        recurrence_regis_date_increment,
                                        recurrence_manual_dates,
                                        recurrence_visibility
                                        ) ';

    $SQL .= ' VALUES (' . "'" . $wpdb->escape( $_POST['recurrence_start_date'] ) . "', " . "'" . $wpdb->escape( $_POST['recurrence_event_end_date'] ) . "', " . "'" . $wpdb->escape( $_POST['recurrence_end_date'] ) . "', ";
    $SQL .= "'" . $wpdb->escape( $_POST['recurrence_regis_start_date'] ) . "', " . "'" . $wpdb->escape( $_POST['recurrence_regis_end_date'] ) . "', ";
    $SQL .= "'" . $wpdb->escape( $_POST['recurrence_type'] ) . "', ";
    $SQL .= "'" . $wpdb->escape( $_POST['recurrence_frequency'] ) . "', " . "'" . $wpdb->escape( $_POST['recurrence_interval'] ) . "', ";
    $SQL .= "'" . $recurrence_weekday . "', ";
    $SQL .= "'" . $wpdb->escape( $_POST['recurrence_repeat_by'] ) . "',";
    $SQL .= "'" . $wpdb->escape( $_POST['recurrence_regis_date_increment'] ) . "',";
    $SQL .= "'" . $recurrence_manual_dates . "',";
    $SQL .= "'" . $wpdb->escape( $_POST['recurrence_visibility'] ) . "')";

    $wpdb->query( $SQL );

    return $wpdb->insert_id;
}


/**
 * Update a master recurrence record
 *
 * @access	public
 * @param	$_POST
 * @return	int - insert id
 */
function update_recurrence_master_record() {

    //echo_f('',$_POST);

    global $wpdb;
$wpdb->show_errors();
    $recurrence_weekday = serialize( $_POST['recurrence_weekday'] );
    $recurrence_manual_dates = $_POST['recurrence_type'] == 'm' ? serialize( $_POST['recurrence_manual_dates']) . serialize($_POST['recurrence_manual_end_dates'] ) : NULL;

   return $wpdb->update( EVENT_ESPRESSO_RECURRENCE_TABLE,
            array(
                'recurrence_start_date' => $_POST['recurrence_start_date'],
                'recurrence_event_end_date' => $_POST['recurrence_event_end_date'],
                'recurrence_end_date' => $_POST['recurrence_end_date'],
                'recurrence_frequency' => $_POST['recurrence_frequency'],
                'recurrence_type' => $_POST['recurrence_type'],
                'recurrence_interval' => $_POST['recurrence_interval'],
                'recurrence_weekday' => $recurrence_weekday,
                'recurrence_repeat_by' => $_POST['recurrence_repeat_by'],
                'recurrence_regis_date_increment' => $_POST['recurrence_regis_date_increment'],
                'recurrence_manual_dates' => $recurrence_manual_dates,
                'recurrence_visibility' => ''/*$_POST['recurrence_visibility']*/,
                'recurrence_regis_start_date' => $_POST['recurrence_regis_start_date'],
                'recurrence_regis_end_date' => $_POST['recurrence_regis_end_date']
            ), array( 'recurrence_id' => $_POST['recurrence_id'] ), array( '%s','%s','%s','%s','%s','%d','%s','%s','%s','%s','%s','%s','%s' ), array( '%d' ) );

    //echo $wpdb->last_query;

}


/**
 * Check to see if the recurring event form has been modified when an event is modified.
 *
 * @access	public
 * @param	date - first event start date
 * @param	date - recurrance end date
 * @param	date - first event registration start date
 * @param	date - first event registration end date
 * @param	string - frequency (d,w,m)
 * @param	integer - interval (0-31)
 * @param	array - weekdays (0-6)
 * @param	string - repeat by
 * @param	string - repeat by
 * @param	int - recurrence_id
 * @return	bool
 */
function recurrence_form_modified( $params = array( ) ) {

    extract($params);
    global $wpdb;

    $weekdays = serialize( $weekdays );
    $recurrence_manual_dates = ($recurrence_type == 'm') ? serialize( $recurrence_manual_dates) . serialize($recurrence_manual_end_dates ) : NULL;
    //$recurrence_visibility = ($recurrence_visibility == '')?'': $recurrence_visibility;

    $result = $wpdb->get_row($wpdb->prepare("SELECT recurrence_id FROM " . EVENT_ESPRESSO_RECURRENCE_TABLE . " WHERE
                                                    recurrence_id = %d AND
                                                    recurrence_start_date = %s AND
                                                    recurrence_event_end_date = %s AND
                                                    recurrence_end_date = %s AND
                                                    recurrence_frequency = %s AND
                                                    recurrence_interval = %d AND
                                                    recurrence_type = %s AND
                                                    recurrence_weekday = %s AND
                                                    recurrence_repeat_by = %s AND
                                                    recurrence_regis_date_increment = %s AND
                                                    recurrence_manual_dates = %s AND
                                                    recurrence_visibility = %s  AND
                                                    recurrence_regis_start_date = %s AND
                                                    recurrence_regis_end_date = %s
                                                    ",
                                                    $recurrence_id, $start_date,$event_end_date,$end_date,$frequency,$interval,
                                                    $recurrence_type,$weekdays,$repeat_by,$recurrence_regis_date_increment,
                                                    $recurrence_manual_dates,$recurrence_visibility,$registration_start,$registration_end
            
            ) );

    //echo $wpdb->last_query;

    if ( $wpdb->num_rows == 1 )
        return false;

    return true;
}


/**
 * Returns an array of dates that the recurrences will happen on
 *
 * @access	public
 * @param	date - first event date
 * @param	date - last event date
 * @param	date - first event registration start date
 * @param	date - first event registration end date
 * @param	string - frequency (d,w,m)
 * @param	int - the interval (0-31)
 * @param	array - weekdays (0-6)
 * @param	string - repeat by
 * @return	array
 */
function find_recurrence_dates( $params = array( ) ) {

    extract( $params );

    $start_date = date( "Y-m-d", strtotime( $start_date ) ); //just in case it comes in in another format
    $recurrence_dates = array( );

    if ( $start_date == '' || $start_date == '0000-00-00' || $recurrence_regis_date_increment == '' )
        return $recurrence_dates;

    /*
     * --------------------------
     * Daily
     * --------------------------
     */

    if ( $frequency == 'd' )
    {
        $date_difference = get_difference( $start_date, $end_date, 3 );
        //for ( $i = $interval; $i <= $date_difference; $i = $i + $interval ) {
        for ( $i = 0; $i <= $date_difference; $i = $i + $interval ) {



            $recurrence_date = date( "Y-m-d", strtotime( "+$i day", strtotime( $start_date ) ) );
            $recurrence_event_end_date = $event_end_date == ''?$recurrence_date:date( "Y-m-d", strtotime( "+$i day", strtotime( $event_end_date ) ) );


            $recurrence_dates[$recurrence_date]['recurrence_id'] = $params['recurrence_id'];
            $recurrence_dates[$recurrence_date]['start_date'] = $recurrence_date;
            $recurrence_dates[$recurrence_date]['event_end_date'] = $recurrence_event_end_date;
            if ( $recurrence_regis_date_increment == 'N' )
            {

                $recurrence_dates[$recurrence_date]['registration_start'] = date( "Y-m-d", strtotime( "+$i day", strtotime( $registration_start ) ) );
                $recurrence_dates[$recurrence_date]['registration_end'] = date( "Y-m-d", strtotime( "+$i day", strtotime( $registration_end ) ) );
            }
            else
            {
                $recurrence_dates[$recurrence_date]['registration_start'] = date( "Y-m-d", strtotime( $registration_start ) );
                $recurrence_dates[$recurrence_date]['registration_end'] = date( "Y-m-d", strtotime( $registration_end ) );
            }

            if ( $recurrence_visibility != '' )
            {
                $recurrence_dates[$recurrence_date]['visible_on'] = date( "Y-m-d", strtotime( "-$recurrence_visibility day", strtotime( $recurrence_dates[$recurrence_date]['registration_start'] ) ) );
            } else
                $recurrence_dates[$recurrence_date]['visible_on'] = date( "Y-m-d" );
        }
    }

    /*
     * --------------------------
     * Weekly
     * --------------------------
     */

    if ( $frequency == 'w' && count( $weekdays ) > 0 )
    {
        //Have to do this in order for ajax to work
        //jQuery won't recognize array fields[] with a preassigned key.  Ex. on the form, if I put, name="weekday[0]", jquery can't find the value
        //will look for a fix
		if (empty($weekdays)){
				return '<p>'.__('Please select a day of the week above.', 'event_espresso').'</p>';
			}
			
        foreach ( $weekdays as $k => $v )
            $weekdays_shifted[$v] = $v;

        //Difference between the two days in weeks
        $week_difference = get_difference( $start_date, $end_date, 4 );
        $individual_event_duration = get_difference( $start_date, $event_end_date, 3 ); //in days

        for ( $i = 0; $i <= $week_difference; $i = $i + $interval ) {

            //Iterate through each one of the weeks
            $recurrence_week = date( "Y-m-d", strtotime( "+$i week ", strtotime( $start_date ) ) );

            //Find the weekday of the first event start date
            $recurrence_day_of_week = date( "w", strtotime( $recurrence_week ) );

            //Find the day of the first Sunday of the week
            $recurrence_first_weekday = date( "Y-m-d", strtotime( "-" . $recurrence_day_of_week . " day", strtotime( $recurrence_week ) ) );
			
			if (empty($weekdays_shifted)){
				return '<p>'.__('Please select a day of the week above.', 'event_espresso').'</p>';
			}
			
            /* Since the supplied $weekday array is just Sunday + the array value, it will just be a simple addition */
            foreach ( $weekdays_shifted as $k => $v ) {

                $recurrence_date_new = date( "Y-m-d", strtotime( "+$v day", strtotime( $recurrence_first_weekday ) ) );

                //Exclude days that fall before the first day of the events
                if ( $recurrence_date_new >= $start_date && $recurrence_date_new <= $end_date )
                {

                    $recurrence_dates[$recurrence_date_new]['recurrence_id'] = $params['recurrence_id'];
                    $recurrence_dates[$recurrence_date_new]['start_date'] = $recurrence_date_new;
                    $recurrence_dates[$recurrence_date_new]['event_end_date'] = $event_end_date =='' ? $recurrence_date_new : date( "Y-m-d", strtotime( "+$individual_event_duration day", strtotime( $recurrence_date_new ) ) );

                    /* Are all events available between the two registration dates or should they increment? */
                    if ( $recurrence_regis_date_increment == 'N' )
                    {
                        $recurrance_date_difference = get_difference( $start_date, $recurrence_date_new, 3 );
                        $recurrence_dates[$recurrence_date_new]['registration_start'] = date( "Y-m-d", strtotime( "+$recurrance_date_difference day", strtotime( $registration_start ) ) );
                        $recurrence_dates[$recurrence_date_new]['registration_end'] = date( "Y-m-d", strtotime( "+$recurrance_date_difference day", strtotime( $registration_end ) ) );
                    }
                    else
                    {
                        $recurrence_dates[$recurrence_date_new]['registration_start'] = date( "Y-m-d", strtotime( $registration_start ) );
                        $recurrence_dates[$recurrence_date_new]['registration_end'] = date( "Y-m-d", strtotime( $registration_end ) );
                    }
                    if ( $recurrence_visibility != '' )
                    {
                        $recurrence_dates[$recurrence_date_new]['visible_on'] = date( "Y-m-d", strtotime( "-$recurrence_visibility day", strtotime( $recurrence_dates[$recurrence_date_new]['registration_start'] ) ) );
                    } else
                        $recurrence_dates[$recurrence_date_new]['visible_on'] = date( "Y-m-d" );
                }
            }

            reset( $weekdays_shifted ); //reset array pointer
        }
    }

    /*
     * --------------------------
     * Monthly
     * --------------------------
     */

    if ( $frequency == 'm' )
    {

        $month_difference = get_difference( $start_date, $end_date, 5 );

        $individual_event_duration = get_difference( $start_date, $event_end_date, 3 ); //in days
        /* if by day of month (i.e. repeats on the same day every month) */
        if ( $repeat_by == 'dom' )
        {

            for ( $i = 0; $i <= $month_difference; $i = $i + $interval ) {
                $recurrence_date = date( "Y-m-d", strtotime( "+$i month", strtotime( $start_date ) ) );

                $recurrence_dates[$recurrence_date]['start_date'] = $recurrence_date;
                $recurrence_dates[$recurrence_date]['recurrence_id'] = $params['recurrence_id'];
                $recurrence_dates[$recurrence_date]['event_end_date'] = $event_end_date =='' ? $recurrence_date :date( "Y-m-d", strtotime( "+$individual_event_duration day", strtotime( $recurrence_date ) ) );
                
                if ( $recurrence_regis_date_increment == 'N' )
                {
                    $recurrence_dates[$recurrence_date]['registration_start'] = date( "Y-m-d", strtotime( "+$i month", strtotime( $registration_start ) ) );
                    $recurrence_dates[$recurrence_date]['registration_end'] = date( "Y-m-d", strtotime( "+$i month", strtotime( $registration_end ) ) );
                }
                else
                {
                    $recurrence_dates[$recurrence_date]['registration_start'] = date( "Y-m-d", strtotime( $registration_start ) );
                    $recurrence_dates[$recurrence_date]['registration_end'] = date( "Y-m-d", strtotime( $registration_end ) );
                }

                if ( $recurrence_visibility != '' )
                {
                    $recurrence_dates[$recurrence_date]['visible_on'] = date( "Y-m-d", strtotime( "-$recurrence_visibility day", strtotime( $recurrence_dates[$recurrence_date]['registration_start'] ) ) );
                } else
                    $recurrence_dates[$recurrence_date]['visible_on'] = date( "Y-m-d" );
            }
        }
        else
        {
            /* get the string representation of the weekday of the first event date */
            $week_number = week_in_the_month( $start_date );

            for ( $i = 0; $i < $month_difference; $i = $i + $interval ) {

                $next_month = date( "F Y", strtotime( "+$i month", strtotime( $start_date ) ) );
                /* find the next event date */
                $recurrence_date = date( "Y-m-d", strtotime( "$week_number of $next_month" ) );
				$of_check = strtotime($recurrence_date);
				if ( $of_check <= 0 )
				{
					$recurrence_date = date( "Y-m-d", strtotime( "$week_number  $next_month" ) );
				}
				$check_again = strtotime($recurrence_date);
				if ( $check_again <= 0 )
				{
					die("Failed to calculate date in Event Espresso > Recurrence Manager > Re-calculation function");
				}
                $recurrence_dates[$recurrence_date]['start_date'] = $recurrence_date;
                $recurrence_dates[$recurrence_date]['event_end_date'] = $event_end_date =='' ? $recurrence_date :date( "Y-m-d", strtotime( "+$individual_event_duration day", strtotime( $recurrence_date ) ) );
                
                if ( $recurrence_regis_date_increment == 'N' )
                {

                    $recurrence_dates[$recurrence_date]['registration_start'] = date( "Y-m-d", strtotime( "+$i month", strtotime( $registration_start ) ) );
                    $recurrence_dates[$recurrence_date]['registration_end'] = date( "Y-m-d", strtotime( "+$i month", strtotime( $registration_end ) ) );
                }
                else
                {

                    $recurrence_dates[$recurrence_date]['registration_start'] = date( "Y-m-d", strtotime( $registration_start ) );
                    $recurrence_dates[$recurrence_date]['registration_end'] = date( "Y-m-d", strtotime( $registration_end ) );
                }

                if ( $recurrence_visibility != '' )
                {
                    $recurrence_dates[$recurrence_date]['visible_on'] = date( "Y-m-d", strtotime( "-$recurrence_visibility day", strtotime( $recurrence_dates[$recurrence_date]['registration_start'] ) ) );
                } else
                    $recurrence_dates[$recurrence_date]['visible_on'] = date( "Y-m-d" );
            }
        }
    }

    //remove the first key since that day will be added/modified before the add_event_to_db or update_event recursion
    if ( isset( $adding_to_db ) )
    {
        //unset( $recurrence_dates[$start_date] );
    }

    return $recurrence_dates;
}


/**
 * Returns an array of dates, along with the registration dates, when manual mode is selected
 *
 * @access	public

 * @return	array
 */
function find_recurrence_manual_dates( $params = array( ) ) {
//echo_f('p',$params);
    extract( $params );


    $recurrence_dates = array( );

    if ( count( $recurrence_manual_dates ) == 0 )
        return $recurrence_dates;

    //Make sure there are no repeat dates
    $recurrence_manual_dates = array_unique( $recurrence_manual_dates );
    $recurrence_manual_end_dates = array_unique( $recurrence_manual_end_dates );

    $start_date = date( "Y-m-d", strtotime( $recurrence_manual_dates[0] ) ); //just in case it comes in in another format

    /*
     * Since we already have the first date, for each one of the manually entered dates, we will find the
     * difference in days, and if the user wants to increment the registration dates, we will do so
     * using the date difference.
     *
     */

    foreach ( $recurrence_manual_dates as $k => $v ) {

        if ( $v != '' )
        {

            $date_difference = get_difference( $start_date, $v, 3 );

            $recurrence_dates[$v]['recurrence_id'] = $recurrence_id;
            $recurrence_dates[$v]['start_date'] = $v;
            $recurrence_dates[$v]['event_end_date'] = $recurrence_manual_end_dates[$k] != ''?$recurrence_manual_end_dates[$k]:$v;
            if ( $recurrence_regis_date_increment == 'N' )
            {
                $recurrence_dates[$v]['registration_start'] = date( "Y-m-d", strtotime( "+$date_difference day", strtotime( $registration_start ) ) );
                $recurrence_dates[$v]['registration_end'] = date( "Y-m-d", strtotime( "+$date_difference day", strtotime( $registration_end ) ) );
            }
            else
            {
                $recurrence_dates[$v]['registration_start'] = date( "Y-m-d", strtotime( $registration_start ) );
                $recurrence_dates[$v]['registration_end'] = date( "Y-m-d", strtotime( $registration_end ) );
            }

            if ( $recurrence_visibility != '' )
            {
                $recurrence_dates[$v]['visible_on'] = date( "Y-m-d", strtotime( "-$recurrence_visibility day", strtotime( $recurrence_dates[$v]['registration_start'] ) ) );
            } else
                $recurrence_dates[$v]['visible_on'] = date( "Y-m-d" );
        }
    }




    //remove the first key since that day will be added/modified before the add_event_to_db or update_event recursion
    if ( isset( $adding_to_db ) )
    {
        //unset( $recurrence_dates[$start_date] );
    }

    return $recurrence_dates;
}


function get_difference( $start_date, $end_date, $format = 4 ) {

    if ( $start_date == '' || $end_date == '' )
        return false;

    /*
     * Can't remember where I got this.  Would love to credit the author
     */
    /* Y-m-d H:i:s */
    /* list($date, $time) = explode( ' ', $endDate ); */
    $startdate = explode( "-", $start_date );
    /* $starttime = explode( ":", $time ); */
    /* list($date, $time) = explode( ' ', $startDate ); */
    $enddate = explode( "-", $end_date );
    /* $endtime = explode( ":", $time ); */

    $seconds_difference = mktime( 0, 0, 0, $enddate[1], $enddate[2], $enddate[0] ) - mktime( 0, 0, 0, $startdate[1], $startdate[2], $startdate[0] );

    switch ( $format )
    {

        case 1: // Difference in Minutes
            return floor( $seconds_difference / 60 );

        case 2: // Difference in Hours
            return floor( $seconds_difference / 60 / 60 );

        case 3: // Difference in Days
            return floor( $seconds_difference / 60 / 60 / 24 );

        case 4: // Difference in Weeks
            return floor( $seconds_difference / 60 / 60 / 24 / 7 );

        case 5: // Difference in Months
            //return floor( $seconds_difference / 60 / 60 / 24 / 7 / 4 );
            $diff = abs(strtotime($end_date) - strtotime($start_date));

            $years = floor($diff / (365*60*60*24));

            return floor(($diff - $years * 365*60*60*24) / (30*60*60*24));

        default: // Difference in Years
            return floor( $seconds_difference / 365 / 60 / 60 / 24 );
    }
}


/**
 * Returns an integer indicating number of weeks in a given month
 *
 * @access	public
 * @param	int year
 * @param	int month
 * @param	int 0 or 1
 * @return	int
 */
function num_weeks( $year, $month, $start=0 ) {
    /* http://www.tek-tips.com/viewthread.cfm?qid=1498654&page=1 */
    $unix = strtotime( "$year-$month-01" );
    $num_days = date( 't', $unix );
    if ( $start === 0 )
    {
        $day_one = date( 'w', $unix ); // sunday based week 0-6
    }
    else
    {
        $day_one = date( 'N', $unix ); //monday based week 1-7
        $day_one--; //convert for 0 based weeks
    }

    /*
     * if day one is not the start of the week then advance to start
     */
    $num_weeks = floor( ($num_days - (6 - $day_one)) / 7 );
    return $num_weeks;
}


/**
 * Returns a string week position representation of the date supplied
 *
 * @access	public
 * @param	date
 * @return	string
 */
function week_number( $date ) {
    return ceil( date( 'j', strtotime( $date ) ) / 7 );
}


/**
 * Returns a string representation of the day in the particular week.  Example, first Monday.
 *
 * @access	public
 * @param	date
 * @return	string
 */
function week_in_the_month( $date ) {

	$version = explode('.', PHP_VERSION);
	if ( count($version) > 2 )
	{
		if ( $version[0] >= 5 && $version[1] >= 0 && $version[2] >= 2 )
		{
			return week_in_the_month_new ( $date );
		}
	}
	elseif( count($version) > 1 )
	{
		if ( $version[0] >= 5 && $version[1] > 0  )
		{
			return week_in_the_month_new( $date );		
		}
	}
    $week = week_number( $date );

    $weekday = date( "l", strtotime( $date ) );

    $date_year = date( "Y", strtotime( $date ) );
    $month = date( "m", strtotime( $date ) );

    /*
     *  Find the number of weeks that are in the month and year of the given date
     */

    $num_weeks = num_weeks( $date_year, $date_month, 0 );

    switch ( $week )
    {

        case 1:
            $week_in_the_month = "first " . $weekday;
            break;

        case $num_weeks:
            $week_in_the_month = "last " . $weekday;
            break;

        case 2:

            $week_in_the_month = "second " . $weekday;

            break;
        case 3:

            $week_in_the_month = "third " . $weekday;

            break;
        case 4:

            $week_in_the_month = "fourth " . $weekday;

            break;
        case 5:

            $week_in_the_month = "fifth " . $weekday;

            break;

       default:
            break;
    }

    return $week_in_the_month;
}

/**
 * Due to php version change older format is no longer valid. So, this function is added as adapter to return value valid for new version
 * Returns a string representation of the day in the particular week.  Example, first Monday.
 *
 * @access	public
 * @param	date
 * @return	string
 */
function week_in_the_month_new( $date ) {

    $week = week_number( $date );

    $weekday = date( "l", strtotime( $date ) );

    $date_year = date( "Y", strtotime( $date ) );
    $month = date( "m", strtotime( $date ) );

    /*
     *  Find the number of weeks that are in the month and year of the given date
     */

    $num_weeks = num_weeks( $date_year, $date_month, 0 );

    switch ( $week )
    {

        case 1:
			$week_in_the_month = "this " . $weekday;
            break;
        case $num_weeks:
            $week_in_the_month = "last " . $weekday;
            break;
        case 2:
			$week_in_the_month = "first " . $weekday;
            break;
        case 3:
			$week_in_the_month = "second " . $weekday;
            break;
        case 4:
			$week_in_the_month = "third " . $weekday;
            break;
        case 5:
			$week_in_the_month = "fourth " . $weekday;
            break;
        default:
            break;
    }

    return $week_in_the_month;
}

/* Output formatted into pre */


function echo_f( $d, $v ) {
    echo "<pre> $d >> ";
    if ( is_array( $v ) )
    {
        print_r( $v );
    }
    else
    {
        echo $v;
    }
    echo "</pre>";
}

/* End of file re_functions.php */
/* Location: plugins/espresso_recurring_events/functions/re_functions.php */
?>