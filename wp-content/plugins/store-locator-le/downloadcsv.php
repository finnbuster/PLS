<?php
/****************************************************************************
 ** file: downloadcsv.php
 **
 ** Export CSV report data.
 ***************************************************************************/
 
//===========================================================================
// Supporting Functions
//=========================================================================== 

/**************************************
 ** function: array_to_CSV()
 **
 ** Return a CSV string from an array.
 **
 **/
function array_to_CSV($data)
{
    $outstream = fopen("php://temp", 'r+');
    fputcsv($outstream, $data, ',', '"');
    rewind($outstream);
    $csv = fgets($outstream);
    fclose($outstream);
    return $csv;
}
    
    
//===========================================================================
// Main Processing
//===========================================================================

// Database Connection
include("./core/database-info.php");
$query = $_POST['query'];

// CSV Header
header( 'Content-Description: File Transfer' );
header( 'Content-Disposition: attachment; filename=slplus_' . $_POST['filename'] . '.csv' );
header( 'Content-Type: application/csv;');
header( 'Pragma: no-cache');
header( 'Expires: 0');

// All records - revise query
//
if (isset($_POST['all']) && ($_POST['all'] == 'true')) {
    $query = preg_replace('/\s+LIMIT \d+(\s+|$)/','',$query);    
}

$slpQueryTable     = $wpdb->prefix . 'slp_rep_query';
$slpResultsTable   = $wpdb->prefix . 'slp_rep_query_results';
$slpLocationsTable = $wpdb->prefix . 'store_locator';

$expr = "/,(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/";
$parts = preg_split($expr, trim(html_entity_decode($query, ENT_QUOTES | ENT_HTML5)));
$parts = preg_replace("/^\"(.*)\"$/","$1",$parts);

if ($parts[0] == 'addr') {
    $slpReportStartDate = $parts[1];
    $slpReportEndDate = $parts[2];
    $slpReportLimit = $parts[3];
    
$query = sprintf(
    "SELECT slp_repq_address, count(*)  as QueryCount FROM %s " .
        "WHERE slp_repq_time > '%s' AND " .
        "      slp_repq_time <= '%s' " .
        "GROUP BY slp_repq_address ".
        "ORDER BY QueryCount DESC " .
        "LIMIT %s"
        ,
    $slpQueryTable,
    $slpReportStartDate,
    $slpReportEndDate,
    $slpReportLimit
    );
} else if ($parts[0] == 'top') {
    $slpReportStartDate = $parts[1];
    $slpReportEndDate = $parts[2];
    $slpReportLimit = $parts[3];

    $query = sprintf(
    "SELECT sl_store,sl_city,sl_state, sl_zip, sl_tags, count(*) as ResultCount " . 
        "FROM %s res ".
            "LEFT JOIN %s sl ". 
                "ON (res.sl_id = sl.sl_id) ".  
            "LEFT JOIN %s qry ". 
                "ON (res.slp_repq_id = qry.slp_repq_id) ".  
            "WHERE slp_repq_time > '%s' AND slp_repq_time <= '%s' ".
        "GROUP BY sl_store,sl_city,sl_state,sl_zip,sl_tags ".
        "ORDER BY ResultCount DESC ".
        "LIMIT %s"
        ,
    $slpResultsTable,
    $slpLocationsTable,
    $slpQueryTable,
    $slpReportStartDate,
    $slpReportEndDate,
    $slpReportLimit
    );
}
$query = stripslashes(htmlspecialchars_decode($query,ENT_QUOTES));
$query = $wpdb->prepare($query);
//echo $query;
// Run the query & output the data in a CSV
$thisDataset = $wpdb->get_results($query,ARRAY_N);


// Sorting
// The sort comes in based on the display table column order which
// matches the query output column order listed here.
//
// It is a paired array, first number is the column number (zero offset)
// second number is the sort order [0=ascending, 1=descending]
//
// The sort needs to happen AFTER the select.
//

// Get our sort array
//
$thisSort = explode(',',$_POST['sort']);

// Build our array_multisort command and our sort index/sort order arrays
// we will need this later for helping do a multi-dimensional sort
//
$sob = 'sort';
$amsstring='';
$sortarrayindex = 0;
foreach($thisSort as $sl_value) {
    if ($sob == 'sort') {
        $sort[] = $sl_value;
        $amsstring .= '$s[' . $sortarrayindex++ . '], ';
        $sob='order';        
    } else {
        $order[] = $sl_value;
        $amsstring .= ($sl_value == 0) ? 'SORT_ASC, ' : 'SORT_DESC, ';
        $sob='sort';
    }
}
$amsstring .= '$thisDataset';

// Now that we have our sort arrays and commands,
// build the indexes that will be used to do the 
// multi-dimensional sort
//
foreach ($thisDataset as $key => $row) {
    $sortarrayindex = 0;
    foreach ($sort as $column) {
        $s[$sortarrayindex++][$key] = $row[$column];
    }
}

// Now do the multidimensional sort
//
// This will sort using the first array ($s[0] we built in the above 2 steps)
// to determine what order to put the "records" (the outter array $thisDataSet)
// into.
//
// If there are secondary arrays ($s[1..n] as built above) we then further
// refine the sort using these secondary arrays.  Think of them as the 2nd
// through nth columns in a multi-column sort on a spreadsheet. 
// 
// This exactly mimics the jQuery sorts that manage our tables on the HTML
// page.
//

//array_multisort($amsstring);
// Output the sorted CSV strings
// This simply iterates through our newly sorted array of records we
// got from the DB and writes them out in CSV format for download.
//
foreach ($thisDataset as $thisDatapoint) {    
    print array_to_CSV($thisDatapoint);
}

