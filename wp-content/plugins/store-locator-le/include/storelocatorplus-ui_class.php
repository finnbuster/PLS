<?php

/***********************************************************************
* Class: SLPlus_UI
*
* The Store Locator Plus UI class.
*
* Provides various UI functions when someone is surfing the site.
*
************************************************************************/

if (! class_exists('SLPlus_UI')) {
    class SLPlus_UI {
        
        /******************************
         * PUBLIC PROPERTIES & METHODS
         ******************************/
        private $usingThemeForest = false;
        public  $resultsString = '';

        /*************************************
         * The Constructor
         */
        function __construct($params = null) {
            $this->usingThemeForest = function_exists('webtreats_formatter');

            // Do the setting override or initial settings.
            //
            if ($params != null) {
                foreach ($params as $name => $sl_value) {
                    $this->$name = $sl_value;
                }
            }
        }


        /**
         * Set the plugin property to point to the primary plugin object.
         *
         * Returns false if we can't get to the main plugin object.
         *
         * @global wpCSL_plugin__slplus $slplus_plugin
         * @return boolean true if plugin property is valid
         */
        function setPlugin() {
            if (!isset($this->plugin) || ($this->plugin == null)) {
                global $slplus_plugin;
                $this->plugin = $slplus_plugin;
            }
            return (isset($this->plugin) && ($this->plugin != null));
        }

        /**
         * Return the HTML for a slider button.
         *
         * The setting parameter will be used for several things:
         * the div ID will be "settingid_div"
         * the assumed matching label option will be "settingid_label" for WP get_option()
         * the a href ID will be "settingid_toggle"
         *
         * @param string $setting - the ID for the setting
         * @param string $label - the default label to show
         * @param boolean $isChecked - default on/off state of checkbox
         * @param string $onClick - the onClick javascript
         * @return string - the slider HTML
         */
        function CreateSliderButton($setting=null, $label='', $isChecked = true, $onClick='') {
            if ($setting === null) { return ''; }
            if (!$this->setPlugin()) { return ''; }

            $label   = $this->plugin->settings->get_item($setting.'_label',$label);
            $checked = ($isChecked ? 'checked' : '');
            $onClick = (($onClick === '') ? '' : ' onClick="'.$onClick.'"');

            $content =
                "<div id='{$setting}_div' class='onoffswitch-block'>" .
                "<span class='onoffswitch-pretext'>$label</span>" .
                "<div class='onoffswitch'>" .
                "<input type='checkbox' name='onoffswitch' class='onoffswitch-checkbox' id='{$setting}-checkbox' $checked>" .
                "<label class='onoffswitch-label' for='{$setting}-checkbox'  $onClick>" .
                '<div class="onoffswitch-inner"></div>'.
                "<div class='onoffswitch-switch'></div>".
                '</label>'.
                '</div>' .
                '</div>';
             return $content;
        }


        /**
         * Returns true if the shortcode attribute='true' or settings is set to 1 (checkbox enabled)
         *
         * If the shortcode attribute = true
         * OR
         * attribute is not set (null) AND
         * the setting is checked
         *
         *
         * @param string $attribute - the key for the shortcode attribute
         * @param string $setting - the key for the admin panel setting
         * @return boolean
         */
        function ShortcodeOrSettingEnabled($attribute,$setting) {
            if (!$this->setPlugin()) { return false; }

            return (
                    (strcasecmp($this->plugin->data[$attribute],'true')==0)
                    ||
                    (($this->plugin->data[$attribute] === null) &&
                     (($this->plugin->settings->get_item($setting,0) == 1))
                    )
               );

            }

        /**
         * Create a search form input div.
         */
        function create_input_div($fldID=null,$label='',$placeholder='',$hidden=false,$divID=null) {
            if ($fldID === null) { return; }
            if ($divID === null) { $divID = $fldID; }

            $content =
                ($hidden?'':"<div id='$divID' class='search_item'>") .
                    (($hidden || ($label === '')) ? '' : "<label for='$fldID'>$label</label>") .
                    "<input type='".($hidden?'hidden':'text')."' id='$fldID' placeholder='$placeholder' size='50' value='' />" .
                ($hidden?'':"</div>")
                ;
            return $content;
        }
        
        /**
         * Do not texturize our shortcodes.
         * 
         * @param array $shortcodes
         * @return array
         */
        function no_texturize_shortcodes($shortcodes) {
           return array_merge($shortcodes,
                    array(
                     'STORE-LOCATOR',
                     'SLPLUS',
                     'slplus',
                    )
                   );
        }

        /**
         * Process the store locator plus shortcode.
         *
         * Variables this function uses and passes to the template
         * we need a better way to pass vars to the template parser so we don't
         * carry around the weight of these global definitions.
         * the other option is to unset($GLOBAL['<varname>']) at then end of this
         * function call.
         *
         * We now use $slplus_plugin->data to hold attribute data.
         *
         *
         * @global type $wpdb
         * @global type $slplus_plugin
         * @global type $sl_search_label
         * @global type $sl_radius_label
         * @global type $r_options
         * @global type $cs_options
         * @global type $sl_country_options
         * @global type $slplus_state_options
         * @param type $attributes
         * @param type $content
         * @return string HTML the shortcode will render
         */
         function render_shortcode($attributes, $content = null) {
            global  $wpdb, $slplus_plugin,
                $sl_search_label, $sl_radius_label, $r_options, $cs_options,
                $sl_country_options, $slplus_state_options;


            // Get Approved Shortcode Attributes
            $attributes =
                shortcode_atts(
                    apply_filters('slp_shortcode_atts',array()),
                    $attributes
                   );
            $slplus_plugin->data =
                array_merge(
                    $slplus_plugin->data,
                    (array) $attributes
                );

            $sl_search_label   = get_option('sl_search_label',__('Address',SLPLUS_PREFIX));
            $unit_display      = get_option('sl_distance_unit','mi');

            $r_options      =(isset($r_options)         ?$r_options      :'');
            $cs_options     =(isset($cs_options)        ?$cs_options     :'');
            $sl_country_options=(isset($sl_country_options)   ?$sl_country_options:'');
            $slplus_state_options=(isset($slplus_state_options)   ?$slplus_state_options:'');

            // Radius Options
            //
            $radiusSelections = get_option('sl_map_radii','1,5,10,(25),50,100,200,500');

            // Hide Radius, set the only (or default) radius
            if (get_option(SLPLUS_PREFIX.'_hide_radius_selections', 0) == 1) {
                preg_match('/\((.*?)\)/', $radiusSelections, $selectedRadius);
                $selectedRadius = preg_replace('/[^0-9]/', '', (isset($selectedRadius[1])?$selectedRadius[1]:$radiusSelections));
                $r_options = "<input type='hidden' id='radiusSelect' name='radiusSelect' value='$selectedRadius'>";

            // Build Pulldown
            } else {
                $radiusSelectionArray  = explode(",",$radiusSelections);
                foreach ($radiusSelectionArray as $radius) {
                    $selected=(preg_match('/\(.*\)/', $radius))? " selected='selected' " : "" ;
                    $radius=preg_replace('/[^0-9]/', '', $radius);
                    $r_options.="<option value='$radius' $selected>$radius $unit_display</option>";
                }
            }

            //-------------------
            // Show City Search option is checked
            // setup the pulldown list
            //
            if (get_option('sl_use_city_search',0)==1) {
                $cs_array=$wpdb->get_results(
                    "SELECT CONCAT(TRIM(sl_city), ', ', TRIM(sl_state)) as city_state " .
                        "FROM ".$wpdb->prefix."store_locator " .
                        "WHERE sl_city<>'' AND sl_state<>'' AND sl_latitude<>'' " .
                            "AND sl_longitude<>'' " .
                        "GROUP BY city_state " .
                        "ORDER BY city_state ASC",
                    ARRAY_A);

                if ($cs_array) {
                    foreach($cs_array as $sl_value) {
                $cs_options.="<option value='$sl_value[city_state]'>$sl_value[city_state]</option>";
                    }
                }
            }
            $sl_country_options     = (isset($this->parent->ProPack) ? $this->parent->ProPack->create_country_pd() : '');
            $slplus_state_options   = (isset($this->parent->ProPack) ? $this->parent->ProPack->create_state_pd()   : '');

            $columns = 1;
            $columns += (get_option('sl_use_city_search',0)!=1) ? 1 : 0;
            $columns += (get_option('sl_use_country_search',0)!=1) ? 1 : 0;
            $columns += (get_option('slplus_show_state_pd',0)!=1) ? 1 : 0;
            $sl_radius_label=get_option('sl_radius_label','');

            // Set our flag for later processing
            // of JavaScript files
            //
            if (!defined('SLPLUS_SHORTCODE_RENDERED')) {
                define('SLPLUS_SHORTCODE_RENDERED',true);
            }
            $this->parent->shortcode_was_rendered = true;

            // Setup the style sheets
            //
            $this->setup_stylesheet_for_slplus();

            // Search / Map Actions
            //
            add_action('slp_render_search_form' ,array('SLPlus_UI','slp_render_search_form'));
            add_action('slp_render_map'         ,array('SLPlus_UI','render_the_map'));

            //todo: make sure map type gets set to a sane value before getting here. Maybe not...
            //todo: if we allow map setting overrides via shortcode attributes we will need
            // to re-localize the script.  It was moved to the actions class so we can
            // localize prior to enqueue in the header.
            //
            // Localize the CSL Script
            $this->localizeCSLScript();

            return
                '<div id="sl_div">' .
                    $this->rawDeal($this->parent->helper->get_string_from_phpexec(SLPLUS_COREDIR . 'templates/search_and_map.php')) .
                '</div>'
                ;
        }


        /**
         * Localize the CSL Script
         *
         * @global type $slplus_plugin
         */
        function localizeCSLScript() {
            if (!$this->setPlugin()) { return false; }
            $this->plugin->helper->loadPluginData();

            $slplus_home_icon_file = str_replace(SLPLUS_ICONURL,SLPLUS_ICONDIR,$this->plugin->data['sl_map_home_icon']);
            $slplus_end_icon_file  = str_replace(SLPLUS_ICONURL,SLPLUS_ICONDIR,$this->plugin->data['sl_map_end_icon']);
            $this->plugin->data['home_size'] =(function_exists('getimagesize') && file_exists($slplus_home_icon_file))?
                getimagesize($slplus_home_icon_file) :
                array(0 => 20, 1 => 34);
            $this->plugin->data['end_size']  =(function_exists('getimagesize') && file_exists($slplus_end_icon_file)) ?
                getimagesize($slplus_end_icon_file)  :
                array(0 => 20, 1 => 34);

            $this->setResultsString();


            // Lets get some variables into our script
            //
            $scriptData = array(
                'plugin_url'        => SLPLUS_PLUGINURL,
                'core_url'          => SLPLUS_COREURL,
                'debug_mode'        => (get_option(SLPLUS_PREFIX.'-debugging') == 'on'),
                'disable_scroll'    => (get_option(SLPLUS_PREFIX.'_disable_scrollwheel')==1),
                'disable_dir'       => (get_option(SLPLUS_PREFIX.'_disable_initialdirectory' )==1),
                'distance_unit'     => esc_attr(get_option('sl_distance_unit'),'miles'),
                'load_locations'    => (get_option('sl_load_locations_default')==1),
                'label_directions'  => esc_attr(get_option(SLPLUS_PREFIX.'_label_directions',   'Directions')  ),
                'label_fax'         => esc_attr(get_option(SLPLUS_PREFIX.'_label_fax',          'Fax: ')         ),
                'label_hours'       => esc_attr(get_option(SLPLUS_PREFIX.'_label_hours',        'Hours: ')       ),
                'label_phone'       => esc_attr(get_option(SLPLUS_PREFIX.'_label_phone',        'Phone: ')       ),
                'map_3dcontrol'     => (get_option(SLPLUS_PREFIX.'_disable_largemapcontrol3d')==0),
                'map_country'       => $this->plugin->Actions->SetMapCenter(),
                'map_domain'        => get_option('sl_google_map_domain','maps.google.com'),
                'map_home_icon'     => $this->plugin->data['sl_map_home_icon'],
                'map_home_sizew'    => $this->plugin->data['home_size'][0],
                'map_home_sizeh'    => $this->plugin->data['home_size'][1],
                'map_end_icon'      => $this->plugin->data['sl_map_end_icon'],
                'map_end_sizew'     => $this->plugin->data['end_size'][0],
                'map_end_sizeh'     => $this->plugin->data['end_size'][1],
                'use_sensor'        => (get_option(SLPLUS_PREFIX."_use_location_sensor",0)==1),
                'map_scalectrl'     => (get_option(SLPLUS_PREFIX.'_disable_scalecontrol')==0),
                'map_type'          => get_option('sl_map_type','roadmap'),
                'map_typectrl'      => (get_option(SLPLUS_PREFIX.'_disable_maptypecontrol')==0),
                'msg_noresults'     => $this->plugin->settings->get_item('message_noresultsfound','No results found.','_'),
                'results_string'    => apply_filters('slp_javascript_results_string',$this->resultsString),
                'show_tags'         => (get_option(SLPLUS_PREFIX.'_show_tags')==1),
                'overview_ctrl'     => get_option('sl_map_overview_control',0),
                'use_email_form'    => (get_option(SLPLUS_PREFIX.'_use_email_form',0)==1),
                'use_pages_links'   => ($this->plugin->settings->get_item('use_pages_links','off')=='on'),
                'use_same_window'   => ($this->plugin->settings->get_item('use_same_window')=='on'),
                'website_label'     => esc_attr(get_option('sl_website_label','Website')),
                'zoom_level'        => get_option('sl_zoom_level',12),
                'zoom_tweak'        => get_option('sl_zoom_tweak',1)
                );
            wp_localize_script('csl_script','slplus',$scriptData);
        }

        /**
         * Set the default results string for stuff under the map.
         *
         * Results Output String In JavaScript Format
         *
         *              {0} aMarker.name,
         *              {1} parseFloat(aMarker.distance).toFixed(1),
         *              {2} slplus.distance_unit,
         *              {3} street,
         *              {4} street2,
         *              {5} city_state_zip,
         *              {6} thePhone,
         *              {7} theFax,
         *              {8} link,
         *              {9} elink,
         *              {10} slplus.map_domain,
         *              {11} encodeURIComponent(this.address),
         *              {12} encodeURIComponent(address),
         *              {13} slplus.label_directions,
         *              {14} tagInfo,
         *              {15} aMarker.id
         *              {16} aMarker.country
         *              {17} aMarker.hours
         *
         */
        function setResultsString() {
            if ($this->resultsString === '') {
                $this->resultsString =
                    '<center>' .
                        '<table width="96%" cellpadding="4px" cellspacing="0" class="searchResultsTable" id="slp_results_table_{15}">'  .
                            '<tr class="slp_results_row" id="slp_location_{15}">'  .
                                '<td class="results_row_left_column" id="slp_left_cell_{15}">'.
                                    '<span class="location_name">{0}</span>'.
                                    '<span class="location_distance"><br/>{1} {2}</span>'.
                                '</td>'  .
                                '<td class="results_row_center_column" id="slp_center_cell_{15}">' .
                                    '<span class="slp_result_address slp_result_street">{3}</span>'.
                                    '<span class="slp_result_address slp_result_street2">{4}</span>' .
                                    '<span class="slp_result_address slp_result_citystatezip">{5}</span>' .
                                    '<span class="slp_result_address slp_result_country">{16}</span>'.
                                    '<span class="slp_result_address slp_result_phone">{6}</span>' .
                                    '<span class="slp_result_address slp_result_fax">{7}</span>' .
                                '</td>'   .
                                '<td class="results_row_right_column" id="slp_right_cell_{15}">' .
                                    '<span class="slp_result_contact slp_result_website">{8}</span>' .
                                    '<span class="slp_result_contact slp_result_email">{9}</span>' .
                                    '<span class="slp_result_contact slp_result_directions"><a href="http://{10}' .
                                    '/maps?saddr={11}'  .
                                    '&daddr={12}'  .
                                    '" target="_blank" class="storelocatorlink">{13}</a></span>'.
                                    '<span class="slp_result_contact slp_result_tags">{14}</span>'.
                                '</td>'  .
                            '</tr>'  .
                        '</table>'  .
                    '</center>';
            }
        }

        /**
         * Setup the CSS for the product pages.
         */
        function setup_stylesheet_for_slplus() {
            if (!$this->setPlugin()) { return false; }
            $this->plugin->helper->loadPluginData();

          /**
           * @see http://goo.gl/UAXly - theme - the file name for a SLPlus display theme from ./core/css
           */
            if ($this->parent->license->packages['Pro Pack']->isenabled)  {
                $this->parent->themes->assign_user_stylesheet($this->parent->data['theme']);
            } else {
                wp_deregister_style(SLPLUS_PREFIX.'_user_header_css');
                wp_dequeue_style(SLPLUS_PREFIX.'_user_header_css');
                if ( file_exists(SLPLUS_PLUGINDIR.'css/default.css')) {
                    wp_enqueue_style(SLPLUS_PREFIX.'_user_header_css', SLPLUS_PLUGINURL .'/css/default.css');
                }
            }
        }


        /**
         * String all \r\n from the template to try to "unbreak" Theme Forest themes.
         *
         * This is VERY ugly, but a lot of people use Theme Forest.  They have a known bug
         * that MANY Theme Forest authors have introduced which will change this:
         * <table
         *    style="display:none"
         *    >
         *
         * To this:
         * <table<br/>
         *    style="display:none"<br/>
         *    >
         *
         * Which really fucks things up.
         *
         * Envato response?  "Oh well, we will tell the authors but can't really fix anything."
         *
         * Now our plugin has this ugly slow formatting function which sucks balls.   But we need it
         * if we are going to not alienate a bunch of Envato users that will never tell us they had an
         * issue. :/
         *
         * @param string $inStr
         * @return string
         */
        function rawDeal($inStr) {
            return str_replace(array("\r","\n"),'',$inStr);
        }

        /**
         * Render the search form for the map.
         */
        function slp_render_search_form() {
            global $slplus_plugin;
            echo apply_filters('slp_search_form_html',$slplus_plugin->helper->get_string_from_phpexec(SLPLUS_COREDIR . 'templates/search_form.php'));
        }

        /**
         * Render the SLP map
         *
         */
        function render_the_map() {
            global $slplus_plugin;

             $slplus_plugin->helper->loadPluginData();

            // Start the map table
            //
            $content =  
                '<table id="map_table" width="100%" cellspacing="0px" cellpadding="0px">' .
                '<tbody id="map_table_body">' .
                '<tr id="map_table_row">'.
                '<td id="map_table_cell" width="100%" valign="top">'
                ;

            // If starting image is set, create the overlay div.
            //
            $startingImage=get_option('sl_starting_image','');
            if ($startingImage != '') {
                $startingImage =
                    ((preg_match('/^http/',$startingImage) <= 0) ?SLPLUS_PLUGINURL:'').
                    $startingImage
                    ;

                $content .=
                    '<div id="map_box_image" ' .
                        'style="'.
                            "width:". $slplus_plugin->data['sl_map_width'].
                                      $slplus_plugin->data['sl_map_width_units'] .
                                      ';'.
                            "height:".$slplus_plugin->data['sl_map_height'].
                                      $slplus_plugin->data['sl_map_height_units'].
                                      ';'.
                        '"'.
                    '>'.
                    "<img src='$startingImage'>".
                    '</div>' .
                    '<div id="map_box_map">'
                    ;
            }
            
            // The Map Div
            //
            $content .=
                '<div id="map" ' .
                    'style="'.
                        "width:". $slplus_plugin->data['sl_map_width'].
                                  $slplus_plugin->data['sl_map_width_units'] .
                                  ';'.
                        "height:".$slplus_plugin->data['sl_map_height'].
                                  $slplus_plugin->data['sl_map_height_units'].
                                  ';'.
                    '"'.
                '>'.
                '</div>'
                ;

            // Credits Line
            if (!(get_option('sl_remove_credits',0)==1)) {
                $content .=
                    '<div id="slp_tagline" ' .
                        'style="'.
                            "width:". $slplus_plugin->data['sl_map_width'].
                                      $slplus_plugin->data['sl_map_width_units'] .
                                      ';'.
                        '"'.
                    '>'.
                    __('search provided by', 'csl-slplus') .
                    "<a href='". $slplus_plugin->url."' target='_blank'>".
                         $slplus_plugin->name.
                    "</a>".
                    '</div>'
                    ;
            }

            // If starting image is set, close the overlay div.
            //
            if ($startingImage != '') {
                $content .= '</div>';
            }
            
            // Close the table
            //
            $content .= '</td></tr></tbody></table>';

            // Render
            //
            echo apply_filters('slp_map_html',$content);
        }

        /**
         * Puts the tag list on the search form for users to select tags.
         *
         * @param type $tags
         * @param type $showany
         */
        function slp_render_search_form_tag_list($tags,$showany = false) {
            print "<select id='tag_to_search_for' >";

            // Show Any Option (blank value)
            //
            if ($showany) {
                print "<option value=''>".
                    __('Any',SLPLUS_PREFIX).
                    '</option>';
            }

            foreach ($tags as $selection) {
                $clean_selection = preg_replace('/\((.*)\)/','$1',$selection);
                print "<option value='$clean_selection' ";
                print (preg_match('#\(.*\)#', $selection))? " selected='selected' " : '';
                print ">$clean_selection</option>";
            }
            print "</select>";
        }
    }
}        
     


if (! class_exists('SLPlus_UI_DivManager')) {
    class SLPlus_UI_DivManager {

        function DivStr($str1, $str2) {
            if ($str2 == '') {
                return $str1;
            } else {
                return $str1.$str2;
            }
        }

        function buildDiv10($blank) {
            global $slp_thishtml_10;
            $content = $this->DivStr($blank,$slp_thishtml_10);
            $slp_thishtml_10 = '';
            return $content;
        }

        function buildDiv20($blank) {
            global $slp_thishtml_20;
            $content = $this->DivStr($blank,$slp_thishtml_20);
            $slp_thishtml_20 = '';
            return $content;
        }

        function buildDiv30($blank) {
            global $slp_thishtml_30;
            $content = $this->DivStr($blank,$slp_thishtml_30);
            $slp_thishtml_30 = '';
            return $content;
        }

        function buildDiv40($blank) {
            global $slp_thishtml_40;
            $content = $this->DivStr($blank,$slp_thishtml_40);
            $slp_thishtml_40 = '';
            return $content;
        }

        function buildDiv50($blank) {
            global $slp_thishtml_50;
            $content = $this->DivStr($blank,$slp_thishtml_50);
            $slp_thishtml_50 = '';
            return $content;
        }

        function buildDiv60($blank) {
            global $slp_thishtml_60;
            $content = $this->DivStr($blank,$slp_thishtml_60);
            $slp_thishtml_60 = '';
            return $content;
        }

        function buildDiv70($blank) {
            global $slp_thishtml_70;
            $content = $this->DivStr($blank,$slp_thishtml_70);
            $slp_thishtml_70 = '';
            return $content;
        }

        function buildDiv80($blank) {
            global $slp_thishtml_80;
            $content = $this->DivStr($blank,$slp_thishtml_80);
            $slp_thishtml_80 = '';
            return $content;
        }

        function buildDiv90($blank) {
            global $slp_thishtml_90;
            $content = $this->DivStr($blank,$slp_thishtml_90);
            $slp_thishtml_90 = '';
            return $content;
        }
    }
}