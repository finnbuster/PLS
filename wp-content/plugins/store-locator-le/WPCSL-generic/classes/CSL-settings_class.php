<?php

/****************************************************************************
 **
 ** class: wpCSL_settings__slplus
 **
 ** The main settings class.
 **
 ** Methods:
 **
 **     __construct         : Overload of the default class instantiation.
 **     add_section 
 **     default_broadcast
 **     get_broadcast   
 **     get_item            : Return the value of a WordPress option that was saved via the settings interface.
 **/
class wpCSL_settings__slplus {

    /**------------------------------------
     ** method: __construct
     **
     ** Overload of the default class instantiation.
     **
     **/
    function __construct($params) {
        // Default Params
        //
        $this->render_csl_blocks = true;        // Display the CSL info blocks
        $this->form_action = 'options.php';     // The form action for this page
        $this->save_text =__('Save Changes',WPCSL__slplus__VERSION);
        $this->css_prefix = '';  
        $this->has_packages = false;
        
        // Passed Params
        //        
        foreach ($params as $name => $value) {
            $this->$name = $value;
        }

        // Only do this if we are on admin panel
        //
        if (isset($this->parent) && (is_admin() && $this->parent->isOurAdminPage)) {
            
            // Only show the license section if the plugin settings
            // wants a license module
            if (!$this->no_license) {
                $this->license_section_title = 'Plugin License';
                $this->add_section(array(
                        'name' => $this->license_section_title,
                        'description' => "<p>To obtain a key, please purchase this plugin " .
                            "from <a href=\"{$this->url}\" target=\"_new\">{$this->url}</a>.</p>",
                        'auto' => false,
                        'div_id' => 'csl_license_block'
                    )
                );
                
            // We don't have a main license but we have paid option
            // packages
            } else if ($this->has_packages) {
                $this->license_section_title = 'Premium Options';
                $this->add_section(array(
                        'name' => $this->license_section_title,
                        'description' => "<h1>{$this->name} has premium options available.</h1>" .
                            "<p>Visit <a href=\"{$this->url}\" target=\"_new\">{$this->url}</a> to " .
                            "learn more about the available add-on packages.<br/> After you purchase " .
                            "an add-on package come back here to activate your add-on packages.</p>",
                        'auto' => false,
                        'div_id' => 'csl_license_block'
                    )
                );
            }
    
            // Render CSL Blocks - if set false we don't need this overhead
            //
            if ($this->render_csl_blocks) {        
                $this->csl_php_modules = get_loaded_extensions();
                natcasesort($this->csl_php_modules);
                global $wpdb;
                $this->add_section(
                    array(
                        'name' => 'Plugin Environment',
                        'description' =>
                            '<p>Here are the technical details about your plugin:<br />
                               <div style="border: solid 1px #E0E0E0; padding: 6px; margin: 6px;
                                   background-color: #F4F4F4;">


                                 <div style="clear:left;">
                                   <div style="width:150px; float:left; text-align: right;
                                       padding-right: 6px;">CSA IP Addresses:</div>
                                   <div style="float: left;">' . 
                                        gethostbyname('charlestonsw.com') . 
                                        ' and ' .  
                                        gethostbyname('license.charlestonsw.com') . 
                                    '</div>
                                 </div>                                
                                   
                                 <div style="clear:left;">
                                   <div style="width:150px; float:left; text-align: right;
                                       padding-right: 6px;">Active WPCSL:</div>
                                   <div style="float: left;">' . plugin_dir_path(__FILE__) . '</div>
                                 </div>                                
                                 <div style="clear:left;">
                                   <div style="width:150px; float:left; text-align: right;
                                       padding-right: 6px;">Site URL:</div>
                                   <div style="float: left;">' . get_option('siteurl') . '</div>
                                 </div>
                                 <div style="clear:left;">
                                   <div style="width:150px; float:left; text-align: right;
                                       padding-right: 6px;">Encryption Key:</div>
                                   <div style="float: left;">' . md5(get_option($this->prefix.'-license_key')) . '</div>
                                 </div>
                                 <div style="clear:left;">
                                   <div style="width:150px; float:left; text-align: right;
                                       padding-right: 6px;">License Key:</div>
                                   <div style="float: left;">' . (get_option($this->prefix.'-purchased')?'licensed':'unlicensed') . '</div>
                                 </div>
                                 
                                 <div style="clear:left;">
                                   <div style="width:150px; float:left; text-align: right;
                                       padding-right: 6px;">WPCSL Version:</div>
                                   <div style="float: left;">' . WPCSL__slplus__VERSION . '
                                   </div>
                                 </div>
                                 <div style="clear:left;">
                                   <div style="width:150px; float:left; text-align: right;
                                       padding-right: 6px;">WordPress Version:</div>
                                   <div style="float: left;">' . $GLOBALS['wp_version'] . '
                                   </div>
                                 </div>
                                 <div style="clear:left;">
                                   <div style="width:150px; float:left; text-align: right;
                                       padding-right: 6px;">MySQL Version:</div>
                                   <div style="float: left;">' . $wpdb->db_version() . '
                                   </div>
                                 </div>
                                 <div style="clear:left;">
                                   <div style="width:150px; float:left; text-align: right;
                                       padding-right: 6px;">PHP Version:</div>
                                   <div style="float: left;">' . phpversion() .'</div>
                                 </div>
                                 <div style="clear:left;">
                                   <div style="width:150px; float:left; text-align: right;
                                       padding-right: 6px;">PHP Modules:</div>
                                   <div style="float: left;">' .
                                     implode('<br/>',$this->csl_php_modules) . '
                                   </div>
                                 </div>
                                 <div style="clear:left;">&nbsp;</div>
                               </div>
                             </p>',
                        'auto' => false,
                        'start_collapsed' => true
                    )
                );
        
                $this->add_item(
                    'Plugin Environment', 
                    'Enable Debugging Output: ',   
                    'debugging',    
                    'checkbox'
                );
        
                $this->add_section(array(
                        'name' => 'Plugin Info',
                        'div_id' => 'csa_plugin_info',
                        'description' => $this->get_broadcast(),
                        'auto' => false
                    )
                );
            }
        }       
    }
    
    /**------------------------------------
     ** method: get_broadcast
     **
     **/
     function get_broadcast() {
         $content = '';
         
        // HTTP Handler is not set fail the license check
        //
        if (isset($this->http_handler)) { 
            if ($this->broadcast_url != '') {
                $result = $this->http_handler->request( 
                                $this->broadcast_url, 
                                array('timeout' => 3) 
                                ); 
                if ($this->parent->http_result_is_ok($result) ) {
                    return $result['body'];
                }
            }                
        }         
        
        // Return default content
        //
        if ($content == '') {
            return $this->default_broadcast();
        }
     }
     
    /**------------------------------------
     ** method: default_broadcast
     **
     **/
     function default_broadcast() {
         return
                        '
                        <div class="cybersprocket-cslbox">
                         <div class="cybersprocket-cslinfo">
                         <h4>This plugin has been brought to you by <a href="http://www.charlestonsw.com"
                                target="_new">Charleston Software Associates</a></h4>
                         <p>If there is anything I can do to improve my work or if you wish to hire me to customize
                            this plugin please
                            <a href="http://www.charlestonsw.com/mindset/contact-us/" target="csa">email me</a>
                            and let me know.
                         </p>
                         </div>
                         </div>
                         ' ;
     }

    /**------------------------------------
     ** method: add_section
     **
     **/
    function add_section($params) {
        if (!isset($this->sections[$params['name']])) {
            $this->sections[$params['name']] = new wpCSL_settings_section__slplus(
                array_merge(
                    $params,
                    array('plugin_url' => $this->plugin_url,
                          'css_prefix' => $this->css_prefix,                       
                            )
                )
            );
        }            
    }
    

    /**------------------------------------
     ** method: get_item
     **
     ** Return the value of a WordPress option that was saved via the settings interface.
     **/
    function get_item($name, $default = null, $separator='-') {
        $option_name = $this->prefix . $separator . $name;
        if (!isset($this->$option_name)) {            
            $this->$option_name =
                ($default == null) ?
                    get_option($option_name) :
                    get_option($option_name,$default)
                    ;
        }
        return $this->$option_name;
    }
    

    /**------------------------------------
     ** Class: WPCSL_Settings
     **------------------------------------
     ** Method: add_item
     **
     ** Parameters:
     **    section name
     **    display name, the label that shows before the input field
     **    name, the database key for the setting
     **    type (default: text, list, checkbox, textarea)
     **    required setting? (default: false, true)
     **    description (default: null) - this is what shows via the expand/collapse setting
     **    custom (default: null, name/value pair if list
     **    value (default: null), the value to use if not using get_option
     **    disabled (default: false), show the input but keep it disabled
     **
     **/
    function add_item($section, $display_name, $name, $type = 'text',
            $required = false, $description = null, $custom = null,
            $value = null, $disabled = false, $onChange = ''
            ) {

        $name = $this->prefix .'-'.$name;

        //** Need to check the section exists first. **/
        if (!isset($this->sections[$section])) {
            if (isset($this->notifications)) {
                $this->notifications->add_notice(
                    3,
                    sprintf(
                       __('Program Error: section <em>%s</em> not defined.',WPCSL__slplus__VERSION),
                       $section
                       )
                );
            }
            return;
        }
        $this->sections[$section]->add_item(
            array(
                'prefix' => $this->prefix,
                'css_prefix' => $this->css_prefix,
                'display_name' => $display_name,
                'name' => $name,
                'type' => $type,
                'required' => $required,
                'description' => $description,
                'custom' => $custom,
                'value' => $value,
                'disabled' => $disabled,
                'onChange' => $onChange
            )
        );

        if ($required) {
            if (get_option($name) == '') {
                if (isset($this->notifications)) {
                    $this->notifications->add_notice(
                        1,
                        "Please provide a value for <em>$display_name</em>",
                        "options-general.php?page={$this->prefix}-options#".
                            strtolower(strtr($display_name,' ', '_'))
                    );
                }
            }
        }
    }

    /**
     * Add a simple checkbox to the settings array.
     *
     * @param string $section - slug for the parent section
     * @param string $label - text to appear before the setting
     * @param string $fieldID - the option value field
     * @param string $description - the help text under the more icon expansion
     * @param string $value - the default value to use, overrides get-option(name)
     * @param boolean $disabled - true if the field is disabled
     */
    function add_checkbox($section,$label,$fieldID,$description=null,$value=null,$disabled=false) {
        $this->add_item(
                $section,
                $label,
                $fieldID,
                'checkbox',
                false,
                $description,
                null,
                $value,
                $disabled
                );
    }

    /**
     * Add a simple text input to the settings array.
     *
     * @param string $section - slug for the parent section
     * @param string $label - text to appear before the setting
     * @param string $fieldID - the option value field
     * @param string $description - the help text under the more icon expansion
     * @param string $value - the default value to use, overrides get-option(name)
     * @param boolean $disabled - true if the field is disabled
     */
    function add_input($section,$label,$fieldID,$description=null,$value=null,$disabled=false) {
        $this->add_item(
                $section,
                $label,
                $fieldID,
                'text',
                false,
                $description,
                null,
                $value,
                $disabled
                );
    }


    /**------------------------------------
     ** Method: register
     ** 
     ** This function should be used via an admin_init action 
     **
     **/
    function register() {
        if (isset($this->license)) {
            $this->license->initialize_options();
        }
        if (isset($this->cache)) {
            $this->cache->initialize_options();
        }

        if (isset($this->sections)) {
            foreach ($this->sections as $section) {
                $section->register($this->prefix);
            }
        }            
    }

    /**------------------------------------
     ** method: render_settings_page
     **
     ** Create the HTML for the plugin settings page on the admin panel
     **/
    function render_settings_page() {
        $this->header();
        
        // Redner all top menus first.
        //
        foreach ($this->sections as $section) {
            if (isset($section->is_topmenu) && ($section->is_topmenu)) {
                $section->display();
            }
        }        

        // Show the plugin environment and info section on every plugin
        //
        if ($this->render_csl_blocks && isset($this->sections['Plugin Info'])) {
            $this->sections['Plugin Info']->display();
        }

        // Only render license section if plugin settings
        // asks for it
        if (isset($this->license_section_title) && (isset($this->sections[$this->license_section_title]))) {
            if ($this->has_packages || !$this->no_license) {
                $this->sections[$this->license_section_title]->header();
                $this->show_plugin_settings();
                $this->sections[$this->license_section_title]->footer();
            }
        }            

        // Draw each settings section as defined in the plugin config file
        //
        foreach ($this->sections as $section) {
            if ($section->auto) {
                $section->display();
            }
        }

        // Show the plugin environment and info section on every plugin
        //
        if ($this->render_csl_blocks && isset($this->sections['Plugin Environment'])) {
            $this->sections['Plugin Environment']->display();
        }
        $this->render_javascript();
        $this->footer();
    }

    /**------------------------------------
     ** method: show_plugin_settings
     **
     ** This is a function specifically for showing the licensing stuff,
     ** should probably be moved over to the licensing submodule
     **/
    function show_plugin_settings() {
       $theLicenseKey = get_option($this->prefix.'-license_key');

       $license_ok =(  (get_option($this->prefix.'-purchased') == '1')   &&
                      ($theLicenseKey != '')
                          );

        // If has_packages is true that means we have an unlicensed product
        // so we don't want to show the license box
        //
        if (!$this->has_packages) {
            $content = "<tr valign=\"top\">\n";
            $content .= "  <th  class=\"input_label\" scope=\"row\">License Key *</th>";
            $content .= "    <td>";
            $content .= "<input type=\"text\"".
                ((!$license_ok) ?
                    "name=\"{$this->prefix}-license_key\"" :
                    '') .
                " value=\"". $theLicenseKey .
                "\"". ($license_ok?'disabled' :'') .
                " />";

            if ($license_ok) {
                $content .=
                    '<p class="slp_license_info">'.$theLicenseKey.'</p>'        .
                    '<input type="hidden" name="'.$this->prefix.'-license_key" '.
                        'value="'.$theLicenseKey.'"/>'                          .
                    '<span><img src="'. $this->plugin_url                       .
                              '/images/check_green.png" border="0" '            .
                              'style="padding-left: 5px;" '                     .
                              'alt="License validated!" '                       .
                              'title="License validated!"></span>'              ;
            }

            $content .= (!$license_ok) ?
                ('<span><font color="red"><br/>Without a license key, this plugin will ' .
                    'only function for Admins</font></span>') :
                '';
            $content .= (!(get_option($this->prefix.'-license_key') == '') &&
                        !get_option($this->prefix.'-purchased')) ?
                ('<span><font color="red">Your license key could not be verified</font></span>') :
                '';

            if (!$license_ok) {
                $content .= $this->MakePayPalButton($this->paypal_button_id);
            }

            $content .= '<div id="prodsku">sku: ';
            if (isset($this->sku) && ($this->sku != '')) {
                $content .= $this->sku;
            } else {
                $content .= 'not set';
            }
            $content .= '</div>';



        // If we are using has_packages we need to seed our content string
        //
        } else {
            $content ='';
        }

        // List Packages
        //
        $content .= $this->ListThePackages($license_ok);

        // If the main product or packages show the license box
        // Then show a save button here
        //
       $license_ok =(  (get_option($this->prefix.'-purchased') == '1')   &&
                      (get_option($this->prefix.'-license_key') != '')
                          );
        if (!$license_ok) {
            $content .= '<tr><td colspan="2">' .
                $this->generate_save_button_string().
                '</td></tr>';
        }

        echo $content;
    }


    /**
     * Create the package license otuput for the admin interface.
     */
    function ListThePackages($license_ok = false) {
        $content = '';
        if (isset($this->parent->license->packages) && ($this->parent->license->packages > 0)) {
            $content .= '<tr valign="top"><td class="optionpack" colspan="2">';
            foreach ($this->parent->license->packages as $package) {
                $content .= '<div class="optionpack_box" id="pack_'.$package->sku.'">';
                $content .= '<div class="optionpack_name">'.$package->name.'</div>';
                $content .= '<div class="optionpack_info">'.$this->EnabledOrBuymeString($license_ok,$package).'</div>';
                $content .= '</div>';
            }
            $content .= '</td></tr>';
        }
        return $content;
    }
    
    /**------------------------------------
     ** method: EnabledOrBuymeString
     **
     **/
    function EnabledOrBuymeString($mainlicenseOK, $package) {
        $content = '';

        // If the main product is licensed or we want to force
        // the packages list, show the checkbox or buy/validate button.
        //
        if ($mainlicenseOK || $this->has_packages) {

            // Check if package is licensed now.
            //

            $package->isenabled = (

                    $package->force_enabled ||

                    $package->parent->check_license_key(
                        $package->sku,
                        true,
                        ($this->has_packages ? $package->license_key : ''),
                        true // Force a server check
                    )
                );

            $installed_version = (isset($package->force_version)?
                        $package->force_version :
                        get_option($this->prefix.'-'.$package->sku.'-version')
                        );
            $latest_version = get_option($this->prefix.'-'.$package->sku.'-latest-version');

            // Upgrade is available if the current package version < the latest available
            // -AND- the current package version is has been set
            $upgrade_available = (
                        ($installed_version != '') &&
                        (   get_option($this->prefix.'-'.$package->sku.'-version-numeric') <
                            get_option($this->prefix.'-'.$package->sku.'-latest-version-numeric')
                        )
                    );

            // Package is enabled, just show that
            //
            if ($package->isenabled && ($package->license_key != '')) {
                $packString = $package->name . ' is enabled!';

                $content .=
                    '<div class="csl_info_package_license">'.
                    (($package->sku!='')?'SKU: '.$package->sku.'<br/>':'').
                    (($package->license_key!='')?'License Key: '.$package->license_key.'<br/>':'').
                    '<img src="'. $this->plugin_url .
                    '/images/check_green.png" border="0" style="padding-left: 5px;" ' .
                    'alt="'.$packString.'" title="'.$packString.'">' .
                    (($installed_version != '')?'Version: ' . $installed_version : '') .
                    '</div>'.
                    '<input type="hidden" '.
                            'name="'.$package->lk_option_name.'" '.
                            ' value="'.$package->license_key.'" '.
                            ' />';
                    ;

                // OK - the license was verified, this package is valid
                // but the mainlicense was not set...
                // go set it.
                if (!$mainlicenseOK && ($package->license_key != '')) {
                    update_option($this->prefix.'-purchased',true);
                    update_option($this->prefix.'-license_key',$package->license_key);
                }

            // Package not enabled, show buy button
            //
            }

            if (!$package->isenabled || $upgrade_available || ($package->license_key == '')) {
                if ($package->isenabled && $upgrade_available) {
                    $content .= '<b>There is a new version available: ' . $latest_version . '</b><br>';
                    $content .= $this->MakePayPalButton($package->paypal_upgrade_button_id, $package->help_text);
                    $content .= "Once you've made your purchase, the plugin will automatically re-validate with the latest version.";
                } else {
                    $content .= $this->MakePayPalButton($package->paypal_button_id, $package->help_text);
                }

                // Show license entry box if we need to
                //
                if (
                        ($this->has_packages && !$upgrade_available) ||
                        ($package->license_key == '')
                    ){
                    $content .= "{$package->sku} Activation Key: <input type='text' ".
                            "name='{$package->lk_option_name}'" .
                            " value='' ".
                            " />";
                    if ($package->license_key != '') {
                        $content .=
                            "<br/><span class='csl_info'>".
                            "The key {$package->license_key} could not be validated.".
                            "</span>";
                    }
                }
            }

        // Main product not licensed, tell them.
        //
        } else {
            $content .= '<span>You must license the product before you can purchase add-on packages.</span>';
        }

        return $content;
    }

    /**------------------------------------
     ** method: MakePayPalButton
     **
     **/
    function MakePayPalButton($buttonID, $helptext = '') {
        
        // Set default help text
        //
        if ($helptext == '') {
            $helptext = 'Your license key is emailed within minutes of your purchase.<br/>'. 
                  'If you do not receive your license check your spam '.
                     'folder then <a href="http://www.charlestonsw.com/mindsetcontact-us/" '.
                     'target="csa">Contact us</a>.';
        }
        
        // PayPal Form String
        $ppFormString = 
                    "<form action='https://www.paypal.com/cgi-bin/webscr' target='_blank' method='post'>".
                    "<input type='hidden' name='cmd' value='_s-xclick'>".
                    "<input type='hidden' name='hosted_button_id' value='$buttonID'>".
                    "<input type='hidden' name='on0' value='Main License Key'>".
                    "<input type='hidden' name='os0' value='" . get_option($this->prefix.'-license_key') . "'>".                    "<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif' border='0' name='submit' alt='Lobby says buy more sprockets!'>".
                    "<img alt='' border='0' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'>".                    
                    "</form>"
                ;
        
        // Modal Form Helpers
        //
        // 
        //
        $modalFormSetup = '
            <script>
            jQuery(function() {
                jQuery( "#ppform_iframe_'.$buttonID.'" ).contents().find("body").html("'.$ppFormString.'");                                
            });
            </script>        
            ';
            
        // Build paypal form and send it back
        //
        return $modalFormSetup .
        '<div><iframe height="70" scrolling="no" id="ppform_iframe_'.$buttonID.'" name="ppform_iframe_'.$buttonID.'" src=""></iframe></div>'.                
                '<div>'.
                  '<p>'.$helptext.'</p>'.
                '</div>';
    }
    
    /**
     * Output the settings page header HTML
     */
    function header() {
        echo "<div class='wrap'>\n";
        screen_icon(preg_replace('/\W/','_',$this->name));
        echo "<h2>{$this->name}</h2>\n";
        echo "<form method='post' action='".$this->form_action."'>\n";
        echo settings_fields($this->prefix.'-settings');

        echo '<div id="csa_admin_wrapper" class="metabox-holder">' .
                '<div class="meta-box-sortables">'
           ;
?>
<script type="text/javascript">
         jQuery(document).ready(function($) {
             $('.postbox').children('h3, .handlediv').click(function(){
                 $(this).siblings('.inside').toggle();
             });
         });
         jQuery(document).ready(function($) {
             $('.<?php echo $this->css_prefix;?>-moreicon').click(function(){
                 $(this).siblings('.<?php echo $this->css_prefix; ?>-moretext').toggle();
             });
         });
</script>
<?php
    }

    /**------------------------------------
     ** method: footer
     **
     **/
    function footer() {
        print '</div></div>' .
              $this->generate_save_button_string() .
             '</form></div>';
    }
        
    /**------------------------------------
     ** method: generate_save_button_string
     **
     **/
    function generate_save_button_string() {
        return sprintf('<input type="submit" class="button-primary" value="%s" />',
         $this->save_text
         );                    
    }

    /**------------------------------------
     ** method: render_javascript
     **
     **/
    function render_javascript() {
        echo "<script type=\"text/javascript\">
            function swapVisibility(id) {
              var item = document.getElementById(id);
              item.style.display = (item.style.display == 'block') ? 'none' : 'block';
            }
          </script>";
    }

    /**------------------------------------
     ** method: check_required
     **
     **/
    function check_required($section = null) {
        if ($section == null) {
            foreach ($this->sections as $section) {
                foreach ($section->items as $item) {
                    if ($item->required && get_option($item->name) == '') return false;
                }
            }
        } else {
            
            // The requested section does not exist yet.
            if (!isset($this->sections[$section])) { return false; }
            
            // Check the required items
            //
            foreach ($this->sections[$section]->items as $item) {
                if ($item->required && get_option($item->name) == '') return false;
            }
        }

        return true;
    }

}

/****************************************************************************
 **
 ** class: wpCSL_settings_section__slplus
 **
 **/
class wpCSL_settings_section__slplus {

    /**------------------------------------
     **/
    function __construct($params) {
        $this->headerbar = true;
        $this->innerdiv  = true;
        foreach ($params as $name => $value) {
            $this->$name = $value;
        }
        
        if (!isset($this->auto)) $this->auto = true;
    }

    /**------------------------------------
     ** Class: wpCSL_settings_section
     ** Method: add_item
     **
     **/
    function add_item($params) {
        $this->items[] = new wpCSL_settings_item__slplus(
            array_merge(
                $params,
                array('plugin_url' => $this->plugin_url,
                      'css_prefix' => $this->css_prefix,
                      )
            )
        );
    }

    /**------------------------------------
     **/
    function register($prefix) {
        if (!isset($this->items)) return false;
        foreach ($this->items as $item) {
            $item->register($prefix);
        }
    }

    /**------------------------------------
     **/
    function display() {
        $this->header();

        if (isset($this->items)) {
            foreach ($this->items as $item) {
                $item->display();
            }
        }

        $this->footer();
    }

    /**------------------------------------
     **/
    function header() {
        echo "<div class=\"postbox\" " . (isset($this->div_id) ?  "id='$this->div_id'" : '') . ">";
        
        if ($this->headerbar) {
            echo "<div class=\"handlediv\" title=\"Click to toggle\"><br/></div>
             <h3 class=\"hndle\">
               <span>{$this->name}</span>
               <a name=\"".strtolower(strtr($this->name, ' ', '_'))."\"></a>
             </h3>";
        }             

        if ($this->innerdiv) {
            echo"<div class=\"inside\" " . (isset($this->start_collapsed) && $this->start_collapsed ? 'style="display:none;"' : '') .
                 "><div class='section_description'>";
         }
         echo $this->description;
         if ($this->innerdiv) {         
            echo '</div><table class="form-table" style="margin-top: 0pt;">';
         }
    }

    /**------------------------------------
     **/
    function footer() {
        if ($this->innerdiv) {
            echo '</table></div></div>';
        }
    }

}

/****************************************************************************
 **
 ** class: wpCSL_settings_item__slplus
 **
 ** Settings Page : Items Class
 ** This class manages individual settings on the admin panel settings page.
 **
 **/
class wpCSL_settings_item__slplus {

    /**------------------------------------
     **/
    function __construct($params) {
        foreach ($params as $name => $value) {
            $this->$name = $value;
        }
    }

    /**------------------------------------
     **/
    function register($prefix) {
        register_setting( $prefix.'-settings', $this->name );
    }

    /**------------------------------------
     **/
    function display() {
        $this->header();
        if (isset($this->value)) {
            $showThis = $this->value;
        } else {
            $showThis = get_option($this->name);
        }
        $showThis = htmlspecialchars($showThis);
        
        echo '<div class="'.$this->css_prefix.'-input'.($this->disabled?'-disabled':'').'">';
        
        switch ($this->type) {
            case 'textarea':
                echo '<textarea name="'.$this->name.'" '.
                    'cols="50" '.
                    'rows="5" '.
                    ($this->disabled?'disabled="disabled" ':'').
                    '>'.$showThis .'</textarea>';
                break;

            case 'text':
                echo '<input type="text" name="'.$this->name.'" '.
                    ($this->disabled?'disabled="disabled" ':'').                
                    'value="'. $showThis .'" />';
                break;

            case "checkbox":
                echo '<input type="checkbox" name="'.$this->name.'" '.
                    ($this->disabled?'disabled="disabled" ':'').                
                    ($showThis?' checked' : '').'>';
                break;

            case "list":
                echo $this->create_option_list();
                break;
                
            case "submit_button":
                echo '<input class="button-primary" type="submit" value="'.$showThis.'">';
                break;                

            default:
                echo $this->custom;
                break;

        }
        echo '</div>';

        if ($this->description != null) {
            $this->display_description_icon();
        }

        if ($this->required) {
            echo ((get_option($this->name) == '') ?
                '<div class="'.$this->css_prefix.'-reqbox">'.
                    '<div class="'.$this->css_prefix.'-reqicon"></div>'.
                    '<div class="'.$this->css_prefix.'-reqtext">This field is required.</div>'.
                '</div>'
                : ''
                );
        }
        
        if ($this->description != null) {
            $this->display_description_text();
        }
        
        $this->footer();
    }

    /**------------------------------------
     * If $type is 'list' then $custom is a hash used to make a <select>
     * drop-down representing the setting.  This function returns a
     * string with the markup for that list.
     */
    function create_option_list() {
        $content =
            "<select class='csl_select' ".
                "name='".$this->name."' ".
                "onChange='".$this->onChange."' ".
                "/>"
                ;

        foreach ($this->custom as $key => $value) {
            if (get_option($this->name) === $value) {
                $content .= "<option class='csl_option' value=\"$value\" " .
                    "selected=\"selected\">$key</option>\n";
            }
            else {
                $content .= "<option class='csl_option'  value=\"$value\">$key</option>\n";
            }
        }

        $content .= "</select>\n";

        return $content;
    }

    /**------------------------------------
     **/
    function header() {
        echo "<tr><th class='input_label".($this->disabled?'-disabled':'')."' scope='row'>" .
        "<a name='" .
        strtolower(strtr($this->display_name, ' ', '_')).
            "'></a>{$this->display_name}".
            (($this->required) ? ' *' : '').
            '</th><td>';

    }

    /**------------------------------------
     **/
    function footer() {
        echo '</td></tr>';
    }

    /**------------------------------------
     **/
    function display_description_icon() {
        echo '<div class="'.$this->css_prefix.'-moreicon" title="click for more info"><br/></div>';        
    }    
    
    /**------------------------------------
     **/
    function display_description_text() {
        echo 
            '<div class="'.$this->css_prefix.'-moretext" id="'.$this->name.'-moretext">' .
                $this->description .
            '</div>'
            ;
    }
}
