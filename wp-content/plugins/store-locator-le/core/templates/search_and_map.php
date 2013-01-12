<?php
global $slplus_plugin;
$slplus_plugin->helper->loadPluginData();
do_action('slp_render_search_form');
do_action('slp_render_map');
?>

<!-- Results Table -->
<table id='results_table'>
    <tr id='cm_mapTR' class='slp_map_search_results'>
        <td width='' valign='top' id='map_sidebar_td'>
            <div id='map_sidebar' style='width:<?php echo  $slplus_plugin->data['sl_map_width'].$slplus_plugin->data['sl_map_width_units'];?>;'>
                <div class='text_below_map'><?php echo get_option('sl_instruction_message',__('Enter Your Address or Zip Code Above.','csl-slplus')); ?></div>
            </div>
        </td>
    </tr>
</table>
