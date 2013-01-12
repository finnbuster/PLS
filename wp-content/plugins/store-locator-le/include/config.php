<?php

/**
 * We need the generic WPCSL plugin class, since that is the
 * foundation of much of our plugin.  So here we make sure that it has
 * not already been loaded by another plugin that may also be
 * installed, and if not then we load it.
 */
if (defined('SLPLUS_PLUGINDIR')) {
    if (class_exists('wpCSL_plugin__slplus') === false) {
        require_once(SLPLUS_PLUGINDIR.'WPCSL-generic/classes/CSL-plugin.php');
    }
    
    if (class_exists('SLPlus_Activation') == false) {
        require_once(SLPLUS_PLUGINDIR.'include/storelocatorplus-activation_class.php');
    }
    
    /**
     * This section defines the settings for the admin menu.
     */ 
    global $slplus_plugin;
    $slplus_plugin = new wpCSL_plugin__slplus(
        array(

            // Plugin data elements, helps make data lookups more efficient
            //
            // 'data' is where actual values are stored
            // 'dataElements' is used to fetch/initialize values whenever helper->loadPluginData() is called
            //
            'data'                  => array(),
            'dataElements'          =>
                array(
                      array(
                        'sl_map_end_icon'                   ,
                        'get_option'                ,
                        array('sl_map_end_icon'         ,SLPLUS_ICONURL.'bulb_azure.png'    )
                      ),
                      array('sl_map_home_icon'              ,
                          'get_option'              ,
                          array('sl_map_home_icon'      ,SLPLUS_ICONURL.'box_yellow_home.png'  )
                      ),
                      array('sl_map_height'         ,
                          'get_option'              ,
                          array('sl_map_height'         ,'480'                                  )
                      ),
                      array('sl_map_height_units'   ,
                          'get_option'              ,
                          array('sl_map_height_units'   ,'px'                                   )
                      ),
                      array('sl_map_width'          ,
                          'get_option'              ,
                          array('sl_map_width'          ,'100'                                  )
                      ),
                      array('sl_map_width_units'    ,
                          'get_option'              ,
                          array('sl_map_width_units'    ,'%'                                    )
                      ),
                      array('theme'                 ,
                          'get_item'                ,
                          array('theme'                 ,'default'                              )
                      ),
                ),

            // We don't want default wpCSL objects, let's set our own
            //
            'use_obj_defaults'      => false,
            'cache_obj_name'        => 'none',
            'helper_obj_name'       => 'default',
            'license_obj_name'      => 'default',
            'notifications_obj_name'=> 'default',
            'products_obj_name'     => 'none',
            'settings_obj_name'     => 'default',
            
            'themes_obj_name'       => 'default',
            'no_default_css'        => true,            
            
            'prefix'                => SLPLUS_PREFIX,
            'css_prefix'            => SLPLUS_PREFIX,
            'name'                  => 'Store Locator Plus',
            'sku'                   => 'SLPLUS',
            'admin_slugs'           => array('slp_general_settings'),

            'on_update' => array('SLPlus_Activate', 'update'),
            'version' => '3.8.6',

            'url'                   => 'http://www.charlestonsw.com/product/store-locator-plus-2/',            
            'support_url'           => 'http://www.charlestonsw.com/support/documentation/store-locator-plus/',
            'purchase_url'          => 'http://www.charlestonsw.com/product/store-locator-plus-2/',
            'rate_url'              => 'http://wordpress.org/extend/plugins/store-locator-le/',
            'forum_url'             => 'http://wordpress.org/support/plugin/store-locator-le',
            'updater_url'           => 'http://www.charlestonsw.com/updater/index.php',
            
            'basefile'              => SLPLUS_BASENAME,
            'plugin_path'           => SLPLUS_PLUGINDIR,
            'plugin_url'            => SLPLUS_PLUGINURL,
            'cache_path'            => SLPLUS_PLUGINDIR . 'cache',
            
            'uses_money'            => false,
            
            'driver_type'           => 'none',
            'driver_args'           => array(
                    'api_key'   => get_option(SLPLUS_PREFIX.'-api_key'),
                    'app_id'    => 'CyberSpr-',
                    'plus_pack_enabled' => get_option(SLPLUS_PREFIX.'-SLPLUS-isenabled'),
                    ),
            
            'has_packages'           => true,            
        )
    );   
    
    
    // Setup our optional packages
    //
    add_options_packages_for_slplus();
}    

/**************************************
 ** function: list_options_packages_for_slplus
 **
 ** Setup the option package list.
 **/
function add_options_packages_for_slplus() {
    configure_slplus_propack();
    configure_slplus_storepages();
}

/**************************************
 ** function: configure_slplus_propack
 **
 ** Configure the Pro Pack.
 **/
function configure_slplus_propack() {
    global $slplus_plugin;
   
    // Setup metadata
    //
    $myPurl = 'http://www.charlestonsw.com/product/store-locator-plus/';
    $slplus_plugin->license->add_licensed_package(
            array(
                'name'              => 'Pro Pack',
                'help_text'         => 'A variety of enhancements are provided with this package.  ' .
                                       'See the <a href="'.$myPurl.'" target="newinfo">product page</a> for details.  If you purchased this add-on ' .
                                       'come back to this page to enter the license key to activate the new features.',
                'sku'               => 'SLPLUS-PRO',
                'paypal_button_id'  => '59YT3GAJ7W922',
                'paypal_upgrade_button_id' => '59YT3GAJ7W922',
                'purchase_url'      => $myPurl
            )
        );
    
    // Enable Features Is Licensed
    //
    if ($slplus_plugin->license->packages['Pro Pack']->isenabled_after_forcing_recheck()) {
        
             //--------------------------------
             // Enable Themes
             //
             $slplus_plugin->themes_enabled = true;
             $slplus_plugin->themes->css_dir = SLPLUS_PLUGINDIR . 'css/';
    }        
}


/**************************************
 ** function: configure_slplus_storepages
 **
 ** Configure Store Pages.
 **/
function configure_slplus_storepages() {
    global $slplus_plugin;
   
    // Setup metadata
    //
    $myPurl = 'http://www.charlestonsw.com/product/store-locator-plus-store-pages/';
    $slplus_plugin->license->add_licensed_package(
            array(
                'name'              => 'Store Pages',
                'help_text'         => 'Create individual WordPress pages from your locations data. Great for SEO.  ' .
                                       'See the <a href="'.$myPurl.'" target="newinfo">product page</a> for details.  If you purchased this add-on ' .
                                       'come back to this page to enter the license key to activate the new features.',
                'sku'               => 'SLPLUS-PAGES',
                'paypal_button_id'  => 'CT449P2ZH454E',
                'paypal_upgrade_button_id' => 'CT449P2ZH454E',
                'purchase_url'      => $myPurl
            )
        );
}

