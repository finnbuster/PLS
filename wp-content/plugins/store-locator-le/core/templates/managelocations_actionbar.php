<?php
/****************************************************************************
 ** file: core/templates/managelocations_actionbar.php
 **
 ** The action bar for the manage locations page.
 ***************************************************************************/
 
 global $slplus_plugin, $sl_hidden;

 if (get_option('sl_location_table_view') == 'Expanded') {
     $altViewText = __('Switch to normal view?',SLPLUS_PREFIX);
     $viewText = __('Normal View',SLPLUS_PREFIX);
 } else {
     $altViewText = __('Switch to expanded view?',SLPLUS_PREFIX);
     $viewText = __('Expanded View',SLPLUS_PREFIX);
 }

 $actionBoxes = array();
?>
<script type="text/javascript">
function doAction(theAction,thePrompt) {
    if((thePrompt == '') || confirm(thePrompt)){
        LF=document.forms['locationForm'];
        LF.act.value=theAction;
        LF.submit();
    }else{
        return false;
    }
}
</script>
<div id="action_buttons">
    <div id="action_bar_header"><h3><?php print __('Actions and Filters',SLPLUS_PREFIX); ?></h3></div>
    <div class="boxbar">
<?php

    // Basic Delete Icon
    //
    $actionBoxes['A'][] =
            '<p class="centerbutton">' .
                '<a class="like-a-button" href="#" ' .
                        'onclick="doAction(\'delete\',\''.__('Delete selected?',SLPLUS_PREFIX).'\');" ' .
                        'name="delete_selected">'.__("Delete Selected", SLPLUS_PREFIX).
                '</a>'.
            '</p>'
            ;

    // Loop through the action boxes content array
    //
    $actionBoxes = apply_filters('slp_action_boxes',$actionBoxes);
    ksort($actionBoxes);
    foreach ($actionBoxes as $boxNumber => $actionBoxLine) {
        print "<div id='box_$boxNumber' class='actionbox'>";
        foreach ($actionBoxLine as $LineHTML) {
            print $LineHTML;
        }
        print '</div>';
    }
 
        print '<div id="search_block" class="searchlocations filterbox">';
        ?>
                <p class="centerbutton"><input class='like-a-button' type='submit' value='<?php print __("Search Locations", SLPLUS_PREFIX); ?>'></p>
                <input id='search-q' value='<?php print (isset($_REQUEST['q'])?$_REQUEST['q']:''); ?>' name='q'>
                <?php 
                print $sl_hidden;
        print '</div>';
        print '<div id="list_options" class="filterbox">';
        ?>
            <p class="centerbutton"><a class='like-a-button' href='#' onclick="doAction('changeview','<?php echo $altViewText; ?>');"><?php echo $viewText; ?></a></p>
            <?php print __('Show ', SLPLUS_PREFIX); ?>
            <select name='sl_admin_locations_per_page'
               onchange="doAction('locationsPerPage','');">
    <?php
        $pagelen = get_option('sl_admin_locations_per_page');
        $opt_arr=array(10,25,50,100,200,300,400,500,1000,2000,4000,5000,10000);
        foreach ($opt_arr as $sl_value) {
            $selected=($pagelen==$sl_value)? " selected " : "";
            print "<option value='$sl_value' $selected>$sl_value</option>";
        }
    ?>
            </select>
            <?php print __(' locations', SLPLUS_PREFIX) . '.';
        print '</div>';

        //----------
        // Pro Pack
        //
        if ($slplus_plugin->license->packages['Pro Pack']->isenabled) {
            print '<div id="filterbox"  class="filterbox">';
        ?>
            <p class="centerbutton"><a class='like-a-button' href="#" onclick="doAction('show_uncoded','')" name="show_uncoded"><?php echo __("Show Uncoded", SLPLUS_PREFIX); ?></a></p>
            <p class="centerbutton"><a class='like-a-button' href="#" onclick="doAction('show_all','')" name="show_all"><?php echo __("Show All", SLPLUS_PREFIX); ?></a></p>
        <?php
            print '</div>';
        }

do_action('slp_add_manage_locations_action_box');

print
    '</div>' .
'</div>';