<?php 
global  $sl_city_checked, $sl_country_checked, $sl_show_tag_checked, $sl_show_any_checked,
    $sl_radius_label, $sl_website_label,$slpMapSettings,$sl_the_distance_unit;

echo "<div id='search_settings'>";
echo "<div class='section_column'>";
echo "<h2>".__('Features', SLPLUS_PREFIX)."</h2>";
echo CreateInputDiv(
        'sl_map_radii',
        __('Radii Options', SLPLUS_PREFIX),
        __('Separate each number with a comma ",". Put parenthesis "( )" around the default.',SLPLUS_PREFIX),
        '',
        '10,25,50,100,(200),500'
        );
?>
            
        <div class='form_entry'>
            <label for='sl_distance_unit'><?php _e('Distance Unit', SLPLUS_PREFIX);?>:</label>
            <select name='sl_distance_unit'>
            <?php
                $sl_the_distance_unit[__("Kilometers", SLPLUS_PREFIX)]="km";
                $sl_the_distance_unit[__("Miles", SLPLUS_PREFIX)]="miles";
                
                foreach ($sl_the_distance_unit as $key=>$sl_value) {
                    $selected=(get_option('sl_distance_unit')==$sl_value)?" selected " : "";
                    print "<option value='$sl_value' $selected>$key</option>\n";
                }
                ?>
            </select>
        </div>    
                   
        <?php

        //----------------------------------------------------------------------
        // Pro Pack Enabled
        //
        global $slplus_plugin;
        $ppFeatureMsg = (!$slplus_plugin->license->packages['Pro Pack']->isenabled ?
                            sprintf(
                                    __(' This is a <a href="%s" target="csa">Pro Pack</a> feature.', SLPLUS_PREFIX),
                                    $slplus_plugin->purchase_url
                                    ) :
                            ''
                         );
        echo CreateCheckboxDiv(
            '_hide_radius_selections',
            __('Hide radius selection',SLPLUS_PREFIX),
            __('Hides the radius selection from the user, the default radius will be used.', SLPLUS_PREFIX) . $ppFeatureMsg,
            SLPLUS_PREFIX,
            !$slplus_plugin->license->packages['Pro Pack']->isenabled
            );

        echo CreateCheckboxDiv(
            '_show_search_by_name',
            __('Show search by name box', SLPLUS_PREFIX),
            __('Shows the name search entry box to the user.', SLPLUS_PREFIX) . $ppFeatureMsg,
            SLPLUS_PREFIX,
            !$slplus_plugin->license->packages['Pro Pack']->isenabled

            );

        echo CreateCheckboxDiv(
            '_hide_address_entry',
            __('Hide address entry box',SLPLUS_PREFIX),
            __('Hides the address entry box from the user.', SLPLUS_PREFIX) . $ppFeatureMsg,
            SLPLUS_PREFIX,
            !$slplus_plugin->license->packages['Pro Pack']->isenabled
            );

        echo CreateCheckboxDiv(
            '_use_location_sensor',
            __('Use location sensor', SLPLUS_PREFIX),
            __('This turns on the location sensor (GPS) to set the default search address.  This can be slow to load and customers are prompted whether or not to allow location sensing.', SLPLUS_PREFIX) . $ppFeatureMsg,
            SLPLUS_PREFIX,
            !$slplus_plugin->license->packages['Pro Pack']->isenabled
        );

        echo CreateCheckboxDiv(
                'sl_use_city_search',
                __('Show City Pulldown',SLPLUS_PREFIX),
                __('Displays the city pulldown on the search form. It is built from the unique city names in your location list.',SLPLUS_PREFIX) . $ppFeatureMsg,
                '',
                !$slplus_plugin->license->packages['Pro Pack']->isenabled
                );

        echo CreateCheckboxDiv(
            'sl_use_country_search',
            __('Show Country Pulldown',SLPLUS_PREFIX),
            __('Displays the country pulldown on the search form. It is built from the unique country names in your location list.',SLPLUS_PREFIX) . $ppFeatureMsg,
            '',
            !$slplus_plugin->license->packages['Pro Pack']->isenabled
            );

        echo CreateCheckboxDiv(
            'slplus_show_state_pd',
            __('Show State Pulldown',SLPLUS_PREFIX),
            __('Displays the state pulldown on the search form. It is built from the unique state names in your location list.',SLPLUS_PREFIX) . $ppFeatureMsg,
            '',
            !$slplus_plugin->license->packages['Pro Pack']->isenabled
            );

        echo CreateCheckboxDiv(
            '_disable_search',
            __('Hide Find Locations button',SLPLUS_PREFIX),
            __('Remove the "Find Locations" button from the search form.', SLPLUS_PREFIX) . $ppFeatureMsg,
            SLPLUS_PREFIX,
            !$slplus_plugin->license->packages['Pro Pack']->isenabled
            );

        echo CreateCheckboxDiv(
            '_disable_find_image',
            __('Use Find Location Text Button',SLPLUS_PREFIX),
            __('Use a standard text button for "Find Locations" instead of the provided button images.', SLPLUS_PREFIX) . $ppFeatureMsg,
            SLPLUS_PREFIX
            );

        do_action('slp_add_search_form_features_setting');

        ?>        
    </div>

    <!-- Tags Section -->
<?php
    global $slplus_plugin;
    $slplus_message = ($slplus_plugin->license->packages['Pro Pack']->isenabled) ?
        __('',SLPLUS_PREFIX) :
        __('Tag features are available in the <a href="%s">%s</a> premium add-on.',SLPLUS_PREFIX)
?>
    <div class='section_column'>
        <h2><?php _e("Tags", SLPLUS_PREFIX); ?></h2>
        <div class="section_column_content">
            <p><?php printf($slplus_message,$slplus_plugin->purchase_url,'Pro Pack'); ?></p>

<?php
        //----------------------------------------------------------------------
        // Pro Pack Enabled
        //
        if ($slplus_plugin->license->packages['Pro Pack']->isenabled) {
            echo CreateCheckboxDiv(
                '_show_tag_search',
                __('Tag Input',SLPLUS_PREFIX),
                __('Show the tag entry box on the search form.', SLPLUS_PREFIX)
                );


            echo CreateInputDiv(
                    '_tag_search_selections',
                    __('Preselected Tag Searches', SLPLUS_PREFIX),
                    __("Enter a comma (,) separated list of tags to show in the search pulldown, mark the default selection with parenthesis '( )'. This is a default setting that can be overriden on each page within the shortcode.",SLPLUS_PREFIX)
                    );
            
            echo CreateCheckboxDiv(
                '_show_tag_any',
                __('Add "any" to tags pulldown',SLPLUS_PREFIX),
                __('Add an "any" selection on the tag pulldown list thus allowing the user to show all locations in the area, not just those matching a selected tag.', SLPLUS_PREFIX)
                );
        }

        do_action('slp_add_search_form_tag_setting');

echo '</div></div>';

// Search Form Labels
//
echo "<div class='section_column'>" .
     '<h2>'.__('Labels', 'csl-slplus') . '</h2>' .
    CreateInputDiv(
        'sl_search_label',
        __('Address', SLPLUS_PREFIX),
        __('Search form address label.',SLPLUS_PREFIX),
        '',
        'Address / Zip'
        ) .
    CreateInputDiv(
        'sl_name_label',
        __('Name', SLPLUS_PREFIX),
        __('Search form name label.',SLPLUS_PREFIX),
        '',
        'Name'
        ) .
    CreateInputDiv(
        'sl_radius_label',
        __('Radius', SLPLUS_PREFIX),
        __('Search form radius label.',SLPLUS_PREFIX),
        '',
        'Within'
        )
    ;

//----------------------------------------------------------------------
// Pro Pack Enabled
//
if ($slplus_plugin->license->packages['Pro Pack']->isenabled) {
    echo CreateInputDiv(
            '_search_tag_label',
            __('Tags', 'csl-slplus'),
            __('Search form label to prefix the tag selector.','csl-slplus')
            );
    echo CreateInputDiv(
            '_state_pd_label',
            __('State Label', 'csl-slplus'),
            __('Search form label to prefix the state selector.','csl-slplus')
            );
    echo CreateInputDiv(
            '_find_button_label',
            __('Find Button', 'csl-slplus'),
            __('The label on the find button, if text mode is selected.','csl-slplus'),
            SLPLUS_PREFIX,
            __('Find Locations','csl-slplus')
            );
}    

do_action('slp_add_search_form_label_setting');

echo "</div></div>";
