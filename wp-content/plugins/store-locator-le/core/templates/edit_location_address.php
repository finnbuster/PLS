<?php 
    global $sl_value, $slplus_plugin;
?>
<table>
    <tr>
        <td><div class="add_location_form">
            <label  for='store-<?php echo $sl_value['sl_id']?>'><?php _e('Name of Location', SLPLUS_PREFIX);?></label>
            <input name='store-<?php echo $sl_value['sl_id']?>' value='<?php echo $sl_value['sl_store']?>'><br/>

            <label  for='address-<?php echo $sl_value['sl_id']?>'><?php _e('Street - Line 1', SLPLUS_PREFIX);?></label>
            <input name='address-<?php echo $sl_value['sl_id']?>' value='<?php echo $sl_value['sl_address']?>'><br/>

		    <label  for='address2-<?php echo $sl_value['sl_id']?>'><?php _e('Street - Line 2', SLPLUS_PREFIX);?></label>
            <input name='address2-<?php echo $sl_value['sl_id']?>' value='<?php echo $sl_value['sl_address2']?>'><br/>

		    <label  for='city-<?php echo $sl_value['sl_id']?>'><?php _e('City, State, ZIP', SLPLUS_PREFIX);?></label>
            <input name='city-<?php echo $sl_value['sl_id']?>'    value='<?php echo $sl_value['sl_city']?>'     style='width: 21.4em; margin-right: 1em;'>
            <input name='state-<?php echo $sl_value['sl_id']?>'   value='<?php echo $sl_value['sl_state']?>'    style='width: 7em; margin-right: 1em;'>
            <input name='zip-<?php echo $sl_value['sl_id']?>'     value='<?php echo $sl_value['sl_zip']?>'      style='width: 7em;'><br/>

		    <label  for='country-<?php echo $sl_value['sl_id']?>'><?php _e('Country', SLPLUS_PREFIX);?></label>
            <input name='country-<?php echo $sl_value['sl_id']?>' value='<?php echo $sl_value['sl_country']?>'  style='width: 40em;'><br/>

            <?php
            if ($slplus_plugin->addform === false) {
            ?>
                <label  for='latitude-<?php echo $sl_value['sl_id']?>'><?php _e('Latitude (N/S)', SLPLUS_PREFIX);?></label>
                <?php if ($slplus_plugin->license->packages['Pro Pack']->isenabled) { ?>
                    <input name='latitude-<?php echo $sl_value['sl_id']?>' value='<?php echo $sl_value['sl_latitude']?>'  style='width: 40em;'><br/>
                <?php } else { ?>
                    <input class='disabled'  name='latitude-<?php echo $sl_value['sl_id']?>' value='<?php echo __('Changing the latitude is a Pro Pack feature.',SLPLUS_PREFIX).' ('.$sl_value['sl_latitude'].')';?>'  style='width: 40em;'><br/>
                <?php } ?>

                <label  for='longitude-<?php echo $sl_value['sl_id']?>'><?php _e('Longitude (E/W)', SLPLUS_PREFIX);?></label>
                <?php if ($slplus_plugin->license->packages['Pro Pack']->isenabled) { ?>
                    <input name='longitude-<?php echo $sl_value['sl_id']?>' value='<?php echo $sl_value['sl_longitude']?>'  style='width: 40em;'><br/>
                <?php } else { ?>
                    <input class='disabled' name='longitude-<?php echo $sl_value['sl_id']?>' value='<?php echo __('Changing the longitude is a Pro Pack feature.',SLPLUS_PREFIX).' ('.$sl_value['sl_longitude'].')'; ?>'  style='width: 40em;'><br/>
                <?php } ?>
            <?php
            }
            ?>
            </div>
        </td>
    </tr>
</table>
