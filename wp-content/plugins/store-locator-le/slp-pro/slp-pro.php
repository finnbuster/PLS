<?php
/**
 * Plugin Name: Store Locator Plus : Pro Pack
 * Plugin URI: http://www.charlestonsw.com/product/store-locator-plus/
 * Description: A premium add-on pack for Store Locator Plus that provides more admin power tools for wrangling locations.
 * Version: 3.8.2
 * Author: Charleston Software Associates
 * Author URI: http://charlestonsw.com/
 * Requires at least: 3.3
 * Test up to : 3.5
 *
 * Text Domain: csl-slplus
 * Domain Path: /languages/
 *
 * @package StoreLocatorPlus
 * @subpackage ProPack
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
if ( ! class_exists( 'SLPPro' ) ) {

    /**
     * Main SLP Pro Class
     */
    class SLPPro {

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

            // Filters
            //
            add_filter('slp_shortcode_atts',array($this,'extend_main_shortcode'));

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
         * PRO PACK IS NOT LICENSED
         *
         * @TODO REMOVE the Pro Pack license check when this becomes an independent plugin.
         *
         * @global wpCSL_plugin__slplus $slplus_plugin
         * @return boolean true if plugin property is valid
         */
        function setPlugin() {
            if (!isset($this->plugin) || ($this->plugin == null)) {
                global $slplus_plugin;
                $this->plugin = $slplus_plugin;
            }
            return (
                isset($this->plugin)    &&
                ($this->plugin != null) &&
                $this->plugin->license->packages['Pro Pack']->isenabled
                );
        }


        //====================================================
        // Pro Pack Custom Methods
        //====================================================


        /**************************************
         ** function: slplus_create_country_pd()
         **
         ** Create the county pulldown list, mark the checked item.
         **
         **/
        function create_country_pd() {
            if (!$this->setPlugin()) { return ''; }

            global $wpdb;
            $myOptions = '';

            // If Use Country Search option is enabled
            // build our country pulldown.
            //
            if (get_option('sl_use_country_search',0)==1) {
                $cs_array=$wpdb->get_results(
                    "SELECT TRIM(sl_country) as country " .
                        "FROM ".$wpdb->prefix."store_locator " .
                        "WHERE sl_country<>'' " .
                            "AND sl_latitude<>'' AND sl_longitude<>'' " .
                        "GROUP BY country " .
                        "ORDER BY country ASC",
                    ARRAY_A);

                // If we have country data show it in the pulldown
                //
                if ($cs_array) {
                    foreach($cs_array as $sl_value) {
                      $myOptions.=
                        "<option value='$sl_value[country]'>" .
                        $sl_value['country']."</option>";
                    }
                }
            }
            return $myOptions;
        }


        /**
         * Create the state pulldown list, mark the checked item.
         *
         * @global type $wpdb
         * @global type $slplus_plugin
         * @return string
         */
        function create_state_pd() {
            if (!$this->setPlugin()) { return ''; }

            global $wpdb;
            $myOptions = '';

            // If Use State Search option is enabled
            // build our state pulldown.
            //
            if (get_option('slplus_show_state_pd',0)==1) {
                $cs_array=$wpdb->get_results(
                    "SELECT TRIM(sl_state) as state " .
                        "FROM ".$wpdb->prefix."store_locator " .
                        "WHERE sl_state<>'' " .
                            "AND sl_latitude<>'' AND sl_longitude<>'' " .
                        "GROUP BY state " .
                        "ORDER BY state ASC",
                    ARRAY_A);

                // If we have country data show it in the pulldown
                //
                if ($cs_array) {
                    foreach($cs_array as $sl_value) {
                      $myOptions.=
                        "<option value='$sl_value[state]'>" .
                        $sl_value['state']."</option>";
                    }
                }
            }
            return $myOptions;
        }

        /**
         * Extends the main SLP shortcode approved attributes list, setting defaults.
         * 
         * This will extend the approved shortcode attributes to include the items listed.
         * The array key is the attribute name, the value is the default if the attribute is not set.
         * 
         * @param array $valid_atts - current list of approved attributes
         */
        function extend_main_shortcode($valid_atts) {
            if (!$this->setPlugin()) { return array(); }

            return array_merge(
                    array(
                        'endicon'          => null,
                        'homeicon'         => null,
                        'only_with_tag'    => null,
                        'tags_for_pulldown'=> null,
                        'theme'            => null,
                        ),
                    $valid_atts
                );
        }

         /**
          * Add the create pages button to box "C" on the action bar
          *
          * @param array $actionBoxes - the existing action boxes, 'A'.. each named array element is an array of HTML strings
          * @return string
          */
         function manage_locations_actionbar($actionBoxes) {
                if (!$this->setPlugin()) { return $actionBoxes; }
                $actionBoxes['A'][] =
                       '<p class="centerbutton">' .
                           '<a class="like-a-button" href="#" ' .
                               'onclick="doAction(\'recode\',\''.__('Recode selected?',SLPLUS_PREFIX).'\');" '.
                               'name="recode_selected">'.__("Recode Selected", SLPLUS_PREFIX).
                           '</a>' .
                        '</p>'
                        ;
                $actionBoxes ['B'][] =
                    '<div id="tag_actions">' .
                            '<a  class="like-a-button" href="#" name="tag_selected"    '.
                                'onclick="doAction(\'add_tag\',\''.__('Tag selected?',SLPLUS_PREFIX).'\');">'.
                                __('Tag Selected', SLPLUS_PREFIX).
                            '</a>'.
                            '<a  class="like-a-button" href="#" name="untag_selected"  ' .
                                'onclick="doAction(\'remove_tag\',\''. __('Remove tag from selected?',SLPLUS_PREFIX).'\');">'.
                                __('Untag Selected', SLPLUS_PREFIX).
                            '</a>'.
                    '</div>' .
                    '<div id="tagentry">'.
                        '<label for="sl_tags">'.__('Tags', SLPLUS_PREFIX).'</label><input name="sl_tags">'.
                    '</div>'
                    ;
                return $actionBoxes;
        }

        /**
         * Report Downloads admin header, setup JavaScript.
         */
        function report_downloads() {
            ?>
            <script type="text/javascript" src="<?php echo SLPLUS_COREURL; ?>js/jquery.tablesorter.min.js"></script>
            <script type="text/javascript" >
            jQuery(document).ready(
                function($) {
                    // Make tables sortable
                     var tstts = $("#topsearches_table").tablesorter( {sortList: [[1,1]]} );
                     var trtts = $("#topresults_table").tablesorter( {sortList: [[5,1]]} );

                    // Export Results Button Click
                    //
                    jQuery("#export_results").click(
                        function(e) {
                            jQuery('<form action="<?php echo SLPLUS_PLUGINURL; ?>/downloadcsv.php" method="post">'+
                                    '<input type="hidden" name="filename" value="topresults">' +
                                    '<input type="hidden" name="query" value="' + jQuery("[name=topresults]").val() + '">' +
                                    '<input type="hidden" name="sort"  value="' + trtts[0].config.sortList.toString() + '">' +
                                    '<input type="hidden" name="all"   value="' + jQuery("[name=export_all]").is(':checked') + '">' +
                                    '</form>'
                                    ).appendTo('body').submit().remove();
                        }
                    );

                    // Export Searches Button Click
                    //
                    jQuery("#export_searches").click(
                        function(e) {
                            jQuery('<form action="<?php echo SLPLUS_PLUGINURL; ?>/downloadcsv.php" method="post">'+
                                    '<input type="hidden" name="filename" value="topsearches">' +
                                    '<input type="hidden" name="query" value="' + jQuery("[name=topsearches]").val() + '">' +
                                    '<input type="hidden" name="sort"  value="' + tstts[0].config.sortList.toString() + '">' +
                                    '<input type="hidden" name="all"   value="' + jQuery("[name=export_all]").is(':checked') + '">' +
                                    '</form>'
                                    ).appendTo('body').submit().remove();
                        }
                    );

                }
            );
            </script>
            <?php
        }
    }

    // Instantiate ourselves as an object
    //
    global $slplus_plugin;
    $slplus_plugin->ProPack = new SLPPro();
}