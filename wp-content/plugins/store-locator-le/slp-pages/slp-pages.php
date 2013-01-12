<?php
/**
 * Plugin Name: Store Locator Plus : Store Pages
 * Plugin URI: http://www.charlestonsw.com/product/store-locator-plus-store-pages/
 * Description: A premium add-on pack for Store Locator Plus that creates custom pages for your locations.
 * Version: 3.8.2
 * Author: Charleston Software Associates
 * Author URI: http://charlestonsw.com/
 * Requires at least: 3.3
 * Test up to : 3.4.2
 *
 * Text Domain: csl-slplus
 * Domain Path: /languages/
 *
 * @package StoreLocatorPlus
 * @subpackage StorePages
 * @category UserInterfaces
 * @author Charleston Software Associates
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// No SLP? Get out...
//
if ( !in_array( 'store-locator-le/store-locator-le.php', apply_filters( 'active_plugins', get_option('active_plugins')))) {
    return;
}

// If we have not been here before, let's get started...
//
if ( ! class_exists( 'SLPPages' ) ) {

    /**
     * Main SLP Pages Class
     */
    class SLPPages {

        /**
         * Properties
         */
        private $dir;
        private $metadata = null;
        public  $plugin = null;
        private $slug = null;
        private $url;
        private $adminMode = false;

        /**
         * Constructor
         */
        function __construct() {
            $this->url  = plugins_url('',__FILE__);
            $this->dir  = plugin_dir_path(__FILE__);
            $this->slug = plugin_basename(__FILE__);

            // Admin / Nav Menus (start of admin stack)
            //
            add_action('admin_menu' ,
                    array($this,'admin_menu')
                    );

            add_filter('slp_action_boxes',array($this,'manage_locations_actionbar'));
        }


        //====================================================
        // WordPress Admin Actions
        //====================================================

        /**
         * WordPress admin_init hook for Tagalong.
         */
        function admin_init(){
            
            // WordPress Update Checker - if this plugin is active
            //
            if (is_plugin_active($this->slug)) {
                $this->metadata = get_plugin_data(__FILE__, false, false);
                $this->Updates = new SLPlus_Updates(
                        $this->metadata['Version'],
                        $this->plugin->updater_url,
                        $this->slug
                        );
            }
        }

        /**
         * WordPress admin_menu hook for Tagalong.
         */
        function admin_menu(){
            $this->adminMode = true;
            if (!$this->setPlugin()) { return ''; }

            // Admin Actions
            //
            add_action('admin_init' ,
                    array($this,'admin_init')
                    );
        }


        //====================================================
        // Helpers
        //====================================================

        /**
         * Debug for action hooks.
         * 
         * @param type $tagname
         * @param type $parm1
         * @param type $parm2
         */
        function debug($tagname,$parm1=null,$parm2=null) {
            print "$tagname<br/>\n".
                  "<pre>".print_r($parm1,true)."</pre>".
                  "<pre>".print_r($parm2,true)."</pre>"
                    ;
            die($this->slug . ' debug hooked.');
        }


        /**
         * Set the plugin property to point to the primary plugin object.
         *
         * Returns false if we can't get to the main plugin object or
         * STORE PAGES IS NOT LICENSED
         *
         * @TODO REMOVE the Store Pages license check when this becomes an independent plugin.
         *
         * @global wpCSL_plugin__slplus $slplus_plugin
         * @return type boolean true if plugin property is valid
         */
        function setPlugin() {
            if (!isset($this->plugin) || ($this->plugin == null)) {
                global $slplus_plugin;
                $this->plugin = $slplus_plugin;
            }
            return (
                isset($this->plugin)    &&
                ($this->plugin != null) &&
                $this->plugin->license->packages['Store Pages']->isenabled
                );
        }


        //====================================================
        // Store Pages Custom Methods
        //====================================================

        /**
         * Add store pages settings to the admin interface.
         *
         * @return string
         */
        function add_pages_settings() {
            if (!$this->setPlugin()) { return ''; }
                $this->plugin->settings->add_item(
                    'Store Pages',
                    __('Pages Replace Websites', SLPLUS_PREFIX),
                    'use_pages_links',
                    'checkbox',
                    false,
                    __('Use the Store Pages local URL in place of the website URL on the map results list.', SLPLUS_PREFIX)
                );
                $this->plugin->settings->add_item(
                    'Store Pages',
                    __('Prevent New Window', SLPLUS_PREFIX),
                    'use_same_window',
                    'checkbox',
                    false,
                    __('Prevent Store Pages web links from opening in a new window.', SLPLUS_PREFIX)
                );
        }

        /**
         * Create a new store pages page.
         *
         * @global wpCSL_plugin__slplus $slplus_plugin
         * @global type $wpdb
         * @param type $locationID
         * @return type
         */
         function CreatePage($locationID=-1, $keepContent = false, $post_status = 'publish')  {
            if (!$this->setPlugin()) { return ''; }

            // If incorrect location ID get out of here
            //
            if ($locationID < 0) {
                return -1;
            }

            // Get The Store Data
            //
            global $wpdb;
            if ($store=$wpdb->get_row('SELECT * FROM '.$wpdb->prefix."store_locator WHERE sl_id = $locationID", ARRAY_A)) {

                $slpStorePage = get_post($store['sl_linked_postid']);
                if (empty($slpStorePage->ID)) {
                    $store['sl_linked_postid'] = -1;
                }
                
                // Update the row
                //
                $wpdb->update($wpdb->prefix."store_locator", $store, array('sl_id' => $locationID));

                // Prior Post Status
                // If new post, use 'draft' as status
                // otherwise keep the current publication state.
                //
                if ($post_status === 'prior') {
                    $post_status =
                        (empty($slpStorePage->ID))      ?
                        'draft'                         :
                        $slpStorePage->post_status
                        ;
                }


                // Create the page
                //
                $slpNewListing = array(
                    'ID'            => (($store['sl_linked_postid'] > 0)?$store['sl_linked_postid']:''),
                    'post_type'     => 'store_page',
                    'post_status'   => $post_status,
                    'post_title'    => $store['sl_store'],
                    'post_content' =>
                        ($keepContent) ?
                            (empty($slpStorePage->ID) ?
                                '' 
                                : 
                                $slpStorePage->post_content
                            ):
                            $this->CreatePageContent($store)
                    );

                // Apply Third Party Filters
                //
                $slpNewListing = apply_filters('slp_pages_insert_post',$slpNewListing);

                return wp_insert_post($slpNewListing);
             }
         }

         /**
          * Create the content for a Store Page.
          *
          * Creates the content for the page.  If plus pack is installed
          * it uses the plus template file, otherwise we use the hard-coded
          * layout.
          *
          * @param type $store
          * @return string
          */
         function CreatePageContent($store) {
             $content = '';

             // Default Content
             //
             $content .= "<span class='storename'>".$store['sl_store']."</span>\n";
             if ($store['sl_image']         !='') {
                 $content .= '<img class="alignright size-full" title="'.$store['sl_store'].'" src="'.$store['sl_image'].'"/>'."\n";
             }
             if ($store['sl_address']       !='') { $content .= $store['sl_address'] . "\n"; }
             if ($store['sl_address2']      !='') { $content .= $store['sl_address2'] . "\n"; }

             if ($store['sl_city']          !='') {
                $content .= $store['sl_city'];
                if ($store['sl_state'] !='') { $content .= ', '; }
             }
             if ($store['sl_state']         !='') { $content .= $store['sl_state']; }
             if ($store['sl_zip']           !='') { $content .= " ".$store['sl_zip']."\n"; }
             if ($store['sl_country']       !='') { $content .= " ".$store['sl_country']."\n"; }
             if ($store['sl_description']   !='') { $content .= "<h1>Description</h1>\n<p>". html_entity_decode($store['sl_description']) ."</p>\n"; }

             $slpContactInfo = '';
             if ($store['sl_phone'] !='') { $slpContactInfo .= __('Phone: ',SLPLUS_PREFIX).$store['sl_phone'] . "\n"; }
             if ($store['sl_fax'] !='') { $slpContactInfo .= __('Fax: ',SLPLUS_PREFIX).$store['sl_fax'] . "\n"; }
             if ($store['sl_email'] !='') { $slpContactInfo .= '<a href="mailto:'.$store['sl_email'].'">'.$store['sl_email']."</a>\n"; }
             if ($store['sl_url']   !='') { $slpContactInfo .= '<a href="'.$store['sl_url'].'">'.$store['sl_url']."</a>\n"; }
             if ($slpContactInfo    != '') {
                $content .= "<h1>Contact Info</h1>\n<p>".$slpContactInfo."</p>\n";
             }

             return apply_filters('slp_pages_content',$content);
         }

         /**
          * Add Pro Pack action buttons to the action bar
          *
          * @param array $actionBoxes - the existing action boxes, 'A'.. each named array element is an array of HTML strings
          * @return string
          */
         function manage_locations_actionbar($actionBoxes) {
                if (!$this->setPlugin()) { return $actionBoxes; }
                $actionBoxes['C'][] =
                        '<p class="centerbutton">' .
                            "<a class='like-a-button' href='#' "            .
                                    "onclick=\"doAction('createpage','"     .
                                        __('Create Pages?',SLPLUS_PREFIX)   .
                                        "')\" name='createpage_selected'>"  .
                                        __('Create Pages', SLPLUS_PREFIX)   .
                             '</a>'                                         .
                        '</p>'
                ;
                return $actionBoxes;
         }
    }

    // Instantiate ourselves as an object
    //
    global$slplus_plugin;
    $slplus_plugin->StorePages = new SLPPages();
}