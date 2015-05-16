<?php

/*
 * Jewish calendar file
 */

/**
 * Display event calendar with days that have posts as links.
 *
 * The calendar is cached, which will be retrieved, if it exists. If there are
 * no posts for the month, then it will not be displayed.
 *
 * First get gregorian date. Then convert it to hebrew date. Then
 *
 * @since 1.0.0
 *
 * @param int $catID Optional, default is false. If true, this function will use asSet to false for return.
 */
function kamoha_get_event_calendar( $catID = MEETINGS_CAT, $page_url ){

    global $wp_locale;
    $this_H_month_num = $this_H_year_num = '';

    $cur_url = (empty( $page_url )) ? curPageURL() : $page_url;

    /* ----------------------------------------------------
     * Init relevant dates - gregorian, hewbrew, and julian
     * ---------------------------------------------------- */
    /* Let's figure out what the relevant month and year are */
    if ( strpos( $cur_url, 'month=' ) === false ) { // this is the current month
// Get first get the gregorian date of today
        $this_G_year = date( 'Y' );
        $this_G_month = date( 'm' );
        $this_G_day = date( 'd' );
// and then we derive the hebrew date
        $this_J_date = gregoriantojd( $this_G_month, $this_G_day, $this_G_year ); // first convert gregorian date to julian
        $this_H_date_num = explode( "/", jdtojewish( $this_J_date ) ); // get the jewish date in numbers (i.e., 11/19/5774), and separate them into an array, so can be passed to jewishtojd
        $this_H_month_num = $this_H_date_num[0];
        $this_H_year_num = $this_H_date_num[2];
        // in non-leap year, there isn't a 6th month. So in case of Adar (6), turn in into 7
        if ( $this_H_month_num == 6 && !is_jewish_leap_year( $this_H_year_num ) ) {
            $this_H_month_num = 7;
        }
        $firstOfMonthJdate = jewishtojd( $this_H_month_num, 1, $this_H_year_num ); // we need julian date of first of month for later use
    } else { // this is a different month, gotten to by the prev or next link
        /* here we start with the Jewish date - which is given in the url, and then convert it to the Gregorian date */
        /* We either get here by ajax - and this is what the condition checks - or by explicit url of type http://dev.linux.ort.org.il/kamoha/?month=577412#events_box */
        if ( !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
            $this_H_month_num = substr( $cur_url, strpos( $cur_url, 'month' ) + 10, 2 );
            $this_H_year_num = substr( $cur_url, strpos( $cur_url, 'month' ) + 6, 4 );
        } else {
            // Some urls are with old calendar parameters, like http://www.kamoha.org.il/?p=13017&month=201304
            $this_H_month_num = substr( filter_input( INPUT_GET, 'month' ), 4, 2 );
            $this_H_year_num = substr( filter_input( INPUT_GET, 'month' ), 0, 4 );
            // check if gregorian date:
            if ( substr( $this_H_year_num, 0, 2 ) != '57' ) {
                //error_log($cur_url);
                if ( $this_H_month_num == 13 ) { // can't be in gregorian year 
                    $this_H_month_num = 12;
                }
                $this_J_date = gregoriantojd( $this_H_month_num, 1, $this_H_year_num );
                $this_H_date_num = explode( "/", jdtojewish( $this_J_date ) ); // get the jewish date in numbers (i.e., 11/19/5774), and separate them into an array, so can be passed to jewishtojd
                $this_H_month_num = $this_H_date_num[0];
                $this_H_year_num = $this_H_date_num[2];
                //error_log($cur_url. ' ' . $thisHmonthNum . ' ' . $thisHyearNum);
                // take care of month 6 in non-leap year - turn it to 7
                if ( $this_H_month_num == '6' && !is_jewish_leap_year( $this_H_year_num ) ) {
                    $this_H_month_num = 7;
                }
                if ( $this_H_month_num == '13' && !is_jewish_leap_year( $this_H_year_num ) ) {
                    $this_H_month_num = 12;
                }
            }
        }
        $this_G_date_time = strtotime( jdtogregorian( jewishtojd( $this_H_month_num, 1, $this_H_year_num ) ) ); // get gregorian date, and turn it into time with strtotime, to use in date function
        $this_G_year = date( 'Y', $this_G_date_time );
        $this_G_month = date( 'm', $this_G_date_time );
        $this_G_day = date( 'd', $this_G_date_time );
        $firstOfMonthJdate = gregoriantojd( $this_G_month, $this_G_day, $this_G_year ); // we need julian date of first of month for later use
    }

    /* ----------------------------------------------------
     * Print the month title
     * ---------------------------------------------------- */
    kamoha_display_month_title( $this_G_month, $this_G_day, $this_G_year, $this_H_month_num, $this_H_year_num );

    /* ----------------------------------------------------
     * Query DB for events posts, that happened in relevant month/year
     * ---------------------------------------------------- */

// Get hebrew month and year in string, for getting the events that happened in this month
    $this_H_month_str = $this_H_year_str = '';
    kamoha_get_hebrew_date_str( $this_G_month, $this_G_day, $this_G_year, $this_H_month_str, $this_H_year_str );
// Get the posts from the category that have dates that match the relevant month and year
    $events = new WP_Query( array(
        'meta_key' => 'date', // bring only posts that have a custom fiels called date
        'cat=' => $catID, // the posts have to belong to the meetings cat is
        'post__not_in' => get_option( 'sticky_posts' ),
        'meta_query' => array(// bring only posts who's date custom field match the 2 parameters:
            'relation' => 'AND', // both conditions have to be met
            array(// the date has to have the current year in it
                'key' => 'date',
                'value' => stripslashes( mb_substr( $this_H_year_str, 1 ) ), /* substr is to remove the leading ה in the year */
                'compare' => 'LIKE',
            ),
            array(// the date has to have the current month in it, between slashes
                'key' => 'date',
                'value' => $this_H_month_str,
                'compare' => 'LIKE',
            )
        ),
            )
    );

    /* ----------------------------------------------------
     * Create array of events that happened in this month/year
     * ---------------------------------------------------- */
    $eventArr = array(); // array of all events
//
    // Loop through all posts returned by query, and create an object for each with post title, content, date and link
    foreach ( $events->posts as $event ) {
        /* post meta date field example:
         * 16-17/05/2014 ט"ז-י"ז באייר תשע"ד (פרשת בחוקותי)         */
        $date_field = explode( " ", get_post_meta( $event->ID, 'date', true ) ); // get the gregorian date from the field
        $full_date = explode( "/", $date_field[0] ); // divide it into day(s)/month/year
        $days = explode( "-", $full_date[0] ); // divide the day(s) part into seperate days
        $month = $full_date[1];
        $year = $full_date[2];

        foreach ( $days as $day ) {
            $postdata = array(
                "post_title" => $event->post_title,
                // "post_content" => strip_tags ( $event->post_content ),
                "guid" => $event->guid,
                "year" => $year,
                "month" => $month,
                "day" => $day
            );


            array_push( $eventArr, $postdata );
        }
    }
    wp_reset_postdata();


    /* ----------------------------------------------------
     * Display calendar
     * ---------------------------------------------------- */
    if ( !isset( $calendar_output ) ) {
        $calendar_output = '';
    }
    $calendar_output .= '<div class="calendar_wrap"><table class="wp-calendar">';

    /*     * ****** Begin build the calendar header - names of days of week ************* */
    $calendar_output .= '<thead>
	<tr>';

    $myweek = array();
    $week_begins = intval( get_option( 'start_of_week' ) ); /* week_begins = 0 stands for Sunday */
    for ( $wdcount = 0; $wdcount <= 6; $wdcount ++ ) {
        $myweek[] = $wp_locale->get_weekday( ($wdcount + $week_begins) % 7 );
    }

    foreach ( $myweek as $wd ) {
        $day_name = $wp_locale->get_weekday_abbrev( $wd );

        $calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$day_name</th>";
    }

    $calendar_output .= '
	</tr>
	</thead>';
    /*     * ********* End build the calendar header  *************** */


    /*     * ****** Begin build the calendar body ******************** */
    $calendar_output .= '
	<tbody>
	<tr>';

// See how much we should pad in the beginning
    $pad = calendar_week_mod2( jddayofweek( $firstOfMonthJdate ) - $week_begins ); // 'w' is the numeric representation of the day of the week - 0 (for Sunday) through 6 (for Saturday)
    if ( 0 != $pad ) { // $pad is the number of days before the first of the month. Use it to make the first column span that number of columns
        $calendar_output .= "\n\t\t" . '<td colspan="' . esc_attr( $pad ) . '" class="pad">&nbsp;</td>';
    }

    $found_event = false; // was event found for this day in month

    $hebrew_days_dates = array('א', 'ב', 'ג', 'ד', 'ה', 'ו', 'ז', 'ח', 'ט', 'י', 'י"א', 'י"ב', 'י"ג', 'י"ד', 'ט"ו', 'ט"ז', 'י"ז', 'י"ח', 'י"ט', 'כ', 'כ"א', 'כ"ב', 'כ"ג', 'כ"ד', 'כ"ה', 'כ"ו', 'כ"ז', 'כ"ח', 'כ"ט', 'ל');

    /* get days in month to know the last date in current jewish month */
    $daysinmonth = cal_days_in_month( CAL_JEWISH, $this_H_month_num, $this_H_year_num );

    $today = date( 'n/j/Y' );

    $num_days_last_week = 0; // needed to give colspan to last td in last row
    /*     * ** create a table cell for each day in month ** */
    for ( $day = 0; $day < $daysinmonth; ++$day ) { // loop for all days in current month
        if ( isset( $newrow ) && $newrow ) {
            $calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t"; // close previous tr (if exists, and open a new one)
        }

        $newrow = false;

// get current gregorian date
        $currGdate = jdtogregorian( jewishtojd( $this_H_month_num, $day + 1, $this_H_year_num ) );
        $currGdateTime = strtotime( $currGdate );
        $currGday = date( 'd', $currGdateTime );
        $currGmonth = date( 'm', $currGdateTime );
        $currGyear = date( 'Y', $currGdateTime );

// table cell with indication the current date (today)
        /* gmdate is like date, except return time is GMT
         * j is day of the month without leading zeros          */
        if ( $currGdate == $today ) {
            $calendar_output .= '<td class="today">';
        } else {
            $calendar_output .= '<td>';
        }


// check if eny event happened on this date
        $event_index = array();
        $event_counter = 0;
        foreach ( $eventArr as $event ) {
            if ( $event['day'] == $currGday && $event['month'] == $currGmonth && $event['year'] == $currGyear ) {
                $event_index[] = $event_counter;
            }

            $event_counter ++;
        }


// table cell with indication of an event date (event-day)
        if ( count( $event_index ) > 0 ) {
            $calendar_output .='<div class="event-day"><span class="jewish_daycal">' . $hebrew_days_dates[$day] . '</span><span class="gregorian_daycal"> ' . $currGday . '</span><div class="events">';
            for ( $i = 0; $i < sizeof( $event_index ); $i ++ ) {
                $calendar_output .= '<a href="' . $eventArr[$event_index[$i]]['guid'] . '" ><em class="event-day-title"> ' . $eventArr[$event_index[$i]]['post_title'] . '</em></a>'; // rel="tooltip"
            }
            $calendar_output .='</div></div>';
            $found_event = true;
        }

        /* If an event wasn't found for this date, create a regular table cell */
        if ( $found_event == false ) {
            $calendar_output .= "<span class=\"daycal\"><span class=\"jewish_daycal\">" . $hebrew_days_dates[$day] . "</span><span class=\"gregorian_daycal\"> " . $currGday . "</span></span>";
        } else { // else, initialize $found_event
            $found_event = false;
        }

        $calendar_output .= '</td>';

// if we've gotten to end of week, turn on $newrow so we closr the current <tr> and open a new one
        $num_days_last_week = calendar_week_mod2( jddayofweek( gregoriantojd( $currGmonth, $currGday, $currGyear ) ) - $week_begins );
        if ( 6 == $num_days_last_week )
            $newrow = true;
    }

// after looping through the days of the current month, check if the last row was finished. if not, add a <td> with padded colspan, and close the <tr>
    $pad = 7 - $num_days_last_week - 1;
    if ( $pad != 0 && $pad != 7 ) {
        $calendar_output .= "\n\t\t" . '<td class="pad" colspan="' . esc_attr( $pad ) . '">&nbsp;</td>';
    }

    $calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";

    /*     * ****** End build the calendar body ******************** */

    echo $calendar_output;

    echo '</div>';

    kamoha_display_next_prev_links( $this_H_month_num, $this_H_year_num, $cur_url );
}

/**
 * Get the current page url. Used by kamoha_get_event_calendar, to determione what month should be displayed
 * @return string
 */
function curPageURL(){
    $pageURL = 'http';
    if ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ) {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ( $_SERVER["SERVER_PORT"] != "80" ) {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

/**
 * Display the current hebrew and gregorian months and year
 * @param type $this_G_month - current gregorian month
 * @param type $this_G_day - current gregorian day
 * @param type $this_G_year - current gregorian year
 * @param type $this_H_month_num - current hebrew month as int
 * @param type $this_H_year_num - current hebrew year as int
 */
function kamoha_display_month_title( $this_G_month, $this_G_day, $this_G_year, $this_H_month_num, $this_H_year_num ){
    global $wp_locale;
// Get hebrew month and year in string
    $this_H_month_str = $this_H_year_str = '';
    kamoha_get_hebrew_date_str( $this_G_month, $this_G_day, $this_G_year, $this_H_month_str, $this_H_year_str );

// get the range of gregorian months the this hebrew month elapses
    $first_of_jewish_month_julian = jewishtojd( $this_H_month_num, 01, $this_H_year_num );
    $first_of_jewish_month_gregorian = jdtogregorian( $first_of_jewish_month_julian );
    $gregorian_date_start = strtotime( $first_of_jewish_month_gregorian ); // get gregorian date in numbers (i.e., 6/29/2014) and convert to datetime in order to use date function on
    $startyear = date( 'Y', $gregorian_date_start );
    $startmonth = date( 'm', $gregorian_date_start );

    /* get days in month to know the last date in current jewish month */
    $daysinmonth = cal_days_in_month( CAL_JEWISH, $this_H_month_num, $this_H_year_num );
    $gregorian_date_end = strtotime( jdtogregorian( jewishtojd( $this_H_month_num, $daysinmonth, $this_H_year_num ) ) );
    $endyear = date( 'Y', $gregorian_date_end );
    $endmonth = date( 'm', $gregorian_date_end );

    /* Print the name of the hebrew and gregorian month / months before calendar */
    /* log error if exists */
    if ( $startmonth == '0' ) {
        error_log( '$startmonth == 00' );
        error_log( $this_H_month_num . ' ' . $this_H_year_num . ' ' . curPageURL() );
    }
    if ( $endmonth == '0' ) {
        error_log( '$endmonth == 00' );
        error_log( $this_H_month_num . ' ' . $this_H_year_num . ' ' . curPageURL() );
    }

    if ( $startyear == $endyear ) {
        $calendar_output = '<h3 class="clear">'
                . '<span class="month_name_jew">' . $this_H_month_str . ' ' . $this_H_year_str . '</span> \ '
                . '<span class="month_name_greg">' . $wp_locale->get_month( $startmonth );
        if ( $startmonth != $endmonth ) {
            $calendar_output.= ' - ' . $wp_locale->get_month( $endmonth );
        }
        $calendar_output .= ' ' . $startyear . '</span>'
                . '</h3>';
    } else {
        $calendar_output = '<h3 class="clear">'
                . '<span class="month_name_jew">' . $this_H_month_str . ' ' . $this_H_year_str . '</span> \ '
                . '<span class="month_name_greg">' . $wp_locale->get_month( $startmonth ) . ' ' . $startyear . ' - ' . $wp_locale->get_month( $endmonth ) . ' ' . $endyear . '</span>'
                . '</h3>';
    }
    echo $calendar_output;
}

/**
 * Fill the value of $thisHmonthStr (for example תשרי), and $thisHYearStr (for example תשע"ד) according to given gregorian date
 * @param type $this_G_month - gregorian month
 * @param type $this_G_day - gregorian day
 * @param type $this_G_year - gregorian year
 * @param type $this_H_month_str - passed by reference, get a value of hebrew month name  (כסלו)
 * @param type $this_H_year_str - passed by reference, get a value of hebrew year name  (תשנ"ו)
 */
function kamoha_get_hebrew_date_str( $this_G_month, $this_G_day, $this_G_year, &$this_H_month_str, &$this_H_year_str ){
    $this_J_date = gregoriantojd( $this_G_month, $this_G_day, $this_G_year ); // first convert gregorian date to julian
    $this_H_date_str = explode( " ", iconv( 'WINDOWS-1255', 'UTF-8', jdtojewish( $this_J_date, true, CAL_JEWISH_ADD_GERESHAYIM ) ) ); // and then convert it to jewish
    $jewish_date_length = count( $this_H_date_str );
    $this_H_year_str = $this_H_date_str[$jewish_date_length - 1]; // the last item is the year. It's not always the 3rd item, because in Adar b' the 3rd item is the b'
    $this_H_month_str = kamoha_get_heb_month_name( $this_H_date_str );
}

/**
 * Get number of days since the start of the week.
 *
 * @since 1.5.0
 * @usedby get_calendar()
 *
 * @param int $num Number of day.
 * @return int Days since the start of the week.
 */
function calendar_week_mod2( $num ){
    $base = 7;
    return ($num - $base * floor( $num / $base ));
}

/**
 * Get the Hebrew month name from full date.
 * The hewbrew month could be the 2nd or 3rd item in the array, because of אדר ב which is separated to 2 items
 * Also correct חשון to מרחשוון and סיון to סיוון
 * @param type $hebDate - date array
 * @return string - hebrew month name (אייר)
 */
function kamoha_get_heb_month_name( $hebDate ){
    if ( count( $hebDate ) == 3 ) {
        $ret = $hebDate[1];
    } else {
        $ret = substr( $hebDate[1], 1 ) . ' ' . $hebDate[2] . '\''; //
    }
    switch ( $ret ) {
        case 'חשון':
            $ret = 'מרחשוון';
            break;
        case 'סיון' :
            $ret = 'סיוון';
    }
    return $ret;
}

/**
 * Create links for next and previous months
 * @param type $thismonth
 * @param type $thisyear
 */
function kamoha_display_next_prev_links( $thismonth, $thisyear, $page_url ){
// Calculate next month and year, for links.
    $prevurl = $nexturl = $page_url;
    $prevmonth = '' . zeroise( $thismonth - 1, 2 );
    $nextmonth = '' . zeroise( $thismonth + 1, 2 );
    switch ( $thismonth ) {
        case '01':
            $prevyear = '' . zeroise( $thisyear - 1, 2 );
            $nextyear = '' . zeroise( $thisyear, 2 );
            $prevmonth = zeroise( 13, 2 );
            break;
        case '13':
            $prevyear = '' . zeroise( $thisyear, 2 );
            $nextyear = '' . zeroise( $thisyear + 1, 2 );
            $nextmonth = '' . zeroise( 1, 2 );
            break;
        case '05':
            if ( is_jewish_leap_year( $thisyear ) ) {
                /* In both leap years and and non-leap years, jdtojewish, jewishtojd, and cal_days_in_month expects 13 months.
                 * In non-leap years, cal_days_in_month returns 0 for month 6, and for jdtojewish and jewishtojd return the same data (אדר) for both month 6 and month 7
                 * This means that month 6 has to be skipped...  cal_days_in_month is documented here: https://bugs.php.net/bug.php?id=61185 */
                $prevyear = '' . zeroise( $thisyear, 2 );
                $nextyear = '' . zeroise( $thisyear, 2 );
            } else {
                $prevyear = '' . zeroise( $thisyear, 2 );
                $nextyear = '' . zeroise( $thisyear, 2 );
                $nextmonth = '' . zeroise( 7, 2 );
            }
            break;
        case '07':
            if ( is_jewish_leap_year( $thisyear ) ) {
                /* In both leap years and and non-leap years, jdtojewish, jewishtojd, and cal_days_in_month expects 13 months.
                 * In non-leap years, cal_days_in_month returns 0 for month 6, and for jdtojewish and jewishtojd return the same data (אדר) for both month 6 and month 7
                 * This means that month 6 has to be skipped...  cal_days_in_month is documented here: https://bugs.php.net/bug.php?id=61185 */
                $prevyear = '' . zeroise( $thisyear, 2 );
                $nextyear = '' . zeroise( $thisyear, 2 );
            } else {
                $prevyear = '' . zeroise( $thisyear, 2 );
                $nextyear = '' . zeroise( $thisyear, 2 );
                $prevmonth = '' . zeroise( 5, 2 );
            }
            break;

        default :
            $prevyear = '' . zeroise( $thisyear, 2 );
            $nextyear = '' . zeroise( $thisyear, 2 );
            break;
    }

// Prev/Next month links, with anchor to the events box
    $prevurl = add_query_arg( 'month', $prevyear . $prevmonth, str_replace( $prevurl, '#events_box', '' ) ) . '#events_box';
    $nexturl = add_query_arg( 'month', $nextyear . $nextmonth, str_replace( $nexturl, '#events_box', '' ) ) . '#events_box';

// Prev/Next month buttons
    echo '<span class="prev_next_links prevmonth"><a href="' . esc_url( $prevurl ) . '" class="ajax-link">&lt; ' . __( 'Previous month', 'kamoha' ) . ' </a></span>';
    echo '<span class="prev_next_links nextmonth"><a href="' . esc_url( $nexturl ) . '" class="ajax-link">' . __( 'Next month', 'kamoha' ) . ' &gt; </a></span>';
}

function is_jewish_leap_year( $year ){
    if ( $year % 19 == 0 || $year % 19 == 3 || $year % 19 == 6 ||
            $year % 19 == 8 || $year % 19 == 11 || $year % 19 == 14 ||
            $year % 19 == 17 ) {
        return true;
    } else {
        return false;
    }
}

/**
 * The function taking care of the ajax request for calendar.
 */
function kamoha_calendar_ajax_process_request(){
// first check if data is being sent and that it is the data we want
    if ( isset( $_POST["month"] ) ) {
// now set our response var equal to that of the POST var (this will need to be sanitized based on what you're doing with with it)
        $response = $_POST["month"];
?>
        <?php

        kamoha_get_event_calendar( MEETINGS_CAT, $response );
        die();
    }
}

add_action( 'wp_ajax_calendar_response', 'kamoha_calendar_ajax_process_request' );
add_action( 'wp_ajax_nopriv_calendar_response', 'kamoha_calendar_ajax_process_request' );
