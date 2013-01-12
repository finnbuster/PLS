<?php 
    global $sl_num_initial_displayed,
            $sl_zoom, $sl_zoom_adj, $sl_height,$sl_height_units,$sl_width,$sl_width_units,$checked3,
            $slplus_plugin;

    $slplus_message = ($slplus_plugin->license->packages['Pro Pack']->isenabled) ?
        __('',SLPLUS_PREFIX) :
        __('Extended settings are available in the <a href="%s">%s</a> premium add-on.',SLPLUS_PREFIX)
?>
<div id='map_settings'>
    <div class='section_column'>   
        <div class='map_designer_settings'>
            <h2><?php _e('Features', SLPLUS_PREFIX); ?></h2>
            <div class="section_column_content">
        
            
<?php
                //------------------------
                // Initial Look & Feel
                //
                echo '<p class="slp_admin_info"><strong>'.__('Initial Look and Feel',SLPLUS_PREFIX).'</strong></p>';
                echo '<p>'.sprintf($slplus_message,$slplus_plugin->purchase_url,'Pro Pack').'</p>';
?>
            <div class='form_entry'>
                <label for='sl_remove_credits'><?php _e('Remove Credits', SLPLUS_PREFIX);?></label>
                <input name='sl_remove_credits' value='1' type='checkbox' <?php echo $checked3;?> >
            </div>
<?php

                echo $slplus_plugin->AdminUI->MapSettings->CreateCheckboxDiv(
                    '-force_load_js',
                    __('Force Load JavaScript',SLPLUS_PREFIX),
                    __('Force the JavaScript for Store Locator Plus to load on every page with early loading. ' .
                    'This can slow down your site, but is compatible with more themes and plugins.', SLPLUS_PREFIX),
                    SLPLUS_PREFIX,
                    false,
                    1
                    );

                echo $slplus_plugin->AdminUI->MapSettings->CreateCheckboxDiv(
                        'sl_load_locations_default',
                        __('Immediately Show Locations', SLPLUS_PREFIX),
                        __('Display locations as soon as map loads, based on map center and default radius',SLPLUS_PREFIX),
                        ''
                        );
                echo $slplus_plugin->AdminUI->MapSettings->CreateInputDiv(
                        'sl_num_initial_displayed',
                        __('Number To Show Initially',SLPLUS_PREFIX),
                        __('How many locations should be shown when Immediately Show Locations is checked.  Recommended maximum is 50.',SLPLUS_PREFIX),
                        ''
                        );

                // Pro Pack : Initial Look & Feel
                //
                if ($slplus_plugin->license->packages['Pro Pack']->isenabled) {
                        echo $slplus_plugin->AdminUI->MapSettings->CreateInputDiv(
                                'sl_starting_image',
                                __('Starting Image',SLPLUS_PREFIX),
                                __('If set, this image will be displayed until a search is performed.  Enter the full URL for the image.',SLPLUS_PREFIX),
                                ''
                                );
                        echo $slplus_plugin->AdminUI->MapSettings->CreateCheckboxDiv(
                            '_disable_initialdirectory',
                            __('Disable Initial Directory',SLPLUS_PREFIX),
                            __('Do not display the listings under the map when "immediately show locations" is checked.', SLPLUS_PREFIX)
                            );
                }

                //------------------------
                // Map Settings
                //
                echo '<p class="slp_admin_info" style="clear:both;"><strong>'.__('Map Settings',SLPLUS_PREFIX).'</strong></p>';
                echo '<p>'.sprintf($slplus_message,$slplus_plugin->purchase_url,'Pro Pack').'</p>';
                echo $slplus_plugin->AdminUI->MapSettings->CreatePulldownDiv(
                        'sl_map_type',
                        array('roadmap','hybrid','satellite','terrain'),
                        $label=__('Default Map Type', SLPLUS_PREFIX),
                        $msg=__('What style Google Map should we use?', SLPLUS_PREFIX),
                        $prefix='',
                        $default='roadmap'
                        );

                // Pro Pack : Map Settings
                //
                if ($slplus_plugin->license->packages['Pro Pack']->isenabled) {
                        echo $slplus_plugin->AdminUI->MapSettings->CreateTextAreaDiv(
                                SLPLUS_PREFIX.'_map_center',
                                __('Center Map At',SLPLUS_PREFIX),
                                __('Enter an address to serve as the initial focus for the map. Default is the center of the country.',SLPLUS_PREFIX),
                                ''
                                );
                        echo $slplus_plugin->AdminUI->MapSettings->CreateCheckboxDiv(
                            'sl_map_overview_control',
                            __('Show Map Inset Box',SLPLUS_PREFIX),
                            __('When checked the map inset is shown.', SLPLUS_PREFIX),
                            ''
                            );
                        echo $slplus_plugin->AdminUI->MapSettings->CreateCheckboxDiv(
                            '_disable_scrollwheel',
                            __('Disable Scroll Wheel',SLPLUS_PREFIX),
                            __('Disable the scrollwheel zoom on the maps interface.', SLPLUS_PREFIX)
                            );
                        echo $slplus_plugin->AdminUI->MapSettings->CreateCheckboxDiv(
                            '_disable_largemapcontrol3d',
                            __('Hide map 3d control',SLPLUS_PREFIX),
                            __('Turn the large map 3D control off.', SLPLUS_PREFIX)
                            );
                        echo $slplus_plugin->AdminUI->MapSettings->CreateCheckboxDiv(
                            '_disable_scalecontrol',
                            __('Hide map scale',SLPLUS_PREFIX),
                            __('Turn the map scale off.', SLPLUS_PREFIX)
                            );
                        echo $slplus_plugin->AdminUI->MapSettings->CreateCheckboxDiv(
                            '_disable_maptypecontrol',
                            __('Hide map type',SLPLUS_PREFIX),
                            __('Turn the map type selector off.', SLPLUS_PREFIX)
                            );
                }


?>
            </div>
        </div>
    </div>        

    
    <div class='section_column'>       
        <div class='map_designer_settings'>
            <h2><?php _e('Dimensions', SLPLUS_PREFIX);?></h2>

            <?php
                echo $slplus_plugin->AdminUI->MapSettings->CreatePulldownDiv(
                        'sl_zoom_level',
                        array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19),
                        $label=__('Zoom Level', SLPLUS_PREFIX),
                        $msg=__('Initial zoom level of the map if "immediately show locations" is NOT selected or if only a single location is found.  0 = world view, 19 = house view.', SLPLUS_PREFIX),
                        $prefix='',
                        $default=4
                        );

                echo $slplus_plugin->AdminUI->MapSettings->CreatePulldownDiv(
                        'sl_zoom_tweak',
                        array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19),
                        $label=__('Zoom Adjustment', SLPLUS_PREFIX),
                        $msg=__('Changes how tight auto-zoom bounds the locations shown.  Lower numbers are closer to the locations.', SLPLUS_PREFIX),
                        $prefix='',
                        $default=4
                        );
            ?>
            
            <div class='form_entry'>
                <label for='height'><?php _e("Map Height", SLPLUS_PREFIX);?>:</label>
                <input name='height' value='<?php echo $sl_height;?>' class='small'>&nbsp;
                <?php $slplus_plugin->AdminUI->MapSettings->render_unit_selector($sl_height_units, "height_units"); ?>
            </div>
            
            <div class='form_entry'>
                <label for='height'><?php _e("Map Width", SLPLUS_PREFIX);?>:</label>
                <input name='width' value='<?php echo $sl_width;?>'  class='small'>&nbsp;
                <?php $slplus_plugin->AdminUI->MapSettings->render_unit_selector($sl_width_units, "width_units"); ?>
            </div>
        </div>
    </div>
    
    <div class='section_column'>   
        <div class='map_interface_settings'> 
            <h2><?php _e('Country', SLPLUS_PREFIX);?></h2>
            <div class='form_entry'>
                <label for='google_map_domain'><?php _e("Select Your Location", SLPLUS_PREFIX);?></label>
                <select name='google_map_domain'>
                <?php
                    foreach ($slplus_plugin->AdminUI->MapSettings->get_map_domains() as $key=>$sl_value) {
                        $selected=(get_option('sl_google_map_domain')==$sl_value)?" selected " : "";
                        print "<option value='$key:$sl_value' $selected>$key ($sl_value)</option>\n";
                    }
                ?>
                </select>
            </div>
            
            <div class='form_entry'>
                <label for='sl_map_character_encoding'><?php _e('Select Character Encoding', SLPLUS_PREFIX);?></label>
                <select name='sl_map_character_encoding'>
                <?php
                    foreach ($slplus_plugin->AdminUI->MapSettings->get_map_encodings() as $key=>$sl_value) {
                        $selected=(get_option('sl_map_character_encoding')==$sl_value)?" selected " : "";
                        print "<option value='$sl_value' $selected>$key</option>\n";                        
                    }
                ?>
                </select>
            </div>
        </div>
    </div>    
</div>

