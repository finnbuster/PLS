<?php
/*
Plugin Name: Store Locator Plus
Plugin URI: http://www.charlestonsw.com/products/store-locator-plus/
Description: Manage multiple locations with ease. Map stores or other points of interest with ease via Gooogle Maps.  This is a highly customizable, easily expandable, enterprise-class location management system.
Version: 3.8.6
Author: Charleston Software Associates
Author URI: http://www.charlestonsw.com
License: GPL3

Copyright 2013  Charleston Software Associates (info@charlestonsw.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

===
 * Code Docs Hints (PHPDoc Format)
 *
 * Filters are documented with @see http://goo.gl/ooXFC
 * The plugin object extended data ($slplus_plugin->data[]) elements are documented with @see http://goo.gl/UAXly

*/

if (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME']==='d.csa.com')){
    error_reporting(E_ALL);
}


// Drive Path Defines
//
if (defined('SLPLUS_PLUGINDIR') === false) {
    define('SLPLUS_PLUGINDIR', plugin_dir_path(__FILE__));
}
if (defined('SLPLUS_COREDIR') === false) {
    define('SLPLUS_COREDIR', SLPLUS_PLUGINDIR . 'core/');
}
if (defined('SLPLUS_ICONDIR') === false) {
    define('SLPLUS_ICONDIR', SLPLUS_PLUGINDIR . 'images/icons/');
}


// URL Defines
//
if (defined('SLPLUS_PLUGINURL') === false) {
    define('SLPLUS_PLUGINURL', plugins_url('',__FILE__));
}
if (defined('SLPLUS_COREURL') === false) {
    define('SLPLUS_COREURL', SLPLUS_PLUGINURL . '/core/');
}
if (defined('SLPLUS_ICONURL') === false) {
    define('SLPLUS_ICONURL', SLPLUS_PLUGINURL . '/images/icons/');
}
if (defined('SLPLUS_ADMINPAGE') === false) {
    define('SLPLUS_ADMINPAGE', admin_url() . 'admin.php?page=' . SLPLUS_COREDIR );
}
if (defined('SLPLUS_PLUSPAGE') === false) {
    define('SLPLUS_PLUSPAGE', admin_url() . 'admin.php?page=' . SLPLUS_PLUGINDIR );
}
// The relative path from the plugins directory
//
if (defined('SLPLUS_BASENAME') === false) {
    define('SLPLUS_BASENAME', plugin_basename(__FILE__));
}

// SLP Uploads Dir
//
if (defined('SLPLUS_UPLOADDIR') === false) {
    $upload_dir = wp_upload_dir('slp');
    $error = $upload_dir['error'];
    if (empty($error)) {
        define('SLPLUS_UPLOADDIR', $upload_dir['path']);
        define('SLPLUS_UPLOADURL', $upload_dir['url']);
    } else {
        $error = preg_replace(
                '/Unable to create directory /',
                'Unable to create directory ' . ABSPATH ,
                $error
                );
        define('SLPLUS_UPLOADDIR', SLPLUS_PLUGINDIR);
        define('SLPLUS_UPLOADURL', SLPLUS_PLUGINURL);
    }
}

// Our product prefix
//
if (defined('SLPLUS_PREFIX') === false) {
    define('SLPLUS_PREFIX', 'csl-slplus');
}

// Include our needed files
//
global $slplus_plugin;
include_once(SLPLUS_PLUGINDIR . '/include/config.php'	);

// Errors?
//
if ($error != '') {
    $slplus_plugin->notifications->add_notice(4,$error);
}

// General WP Action Interface
//
//instantiated via admin_init() only...
// adminUI class
// Activation class
//
require_once(SLPLUS_PLUGINDIR . '/include/storelocatorplus-actions_class.php');
$slplus_plugin->Actions = new SLPlus_Actions(array('parent'=>$slplus_plugin));     // Lets invoke this and make it an object

require_once(SLPLUS_PLUGINDIR . '/include/storelocatorplus-activation_class.php');

require_once(SLPLUS_PLUGINDIR . '/include/storelocatorplus-ui_class.php');
$slplus_plugin->UI = new SLPlus_UI(array('parent'=>$slplus_plugin));

// TODO Pro Pack Temp Include
require_once(SLPLUS_PLUGINDIR . '/slp-pro/slp-pro.php');

require_once(SLPLUS_PLUGINDIR . '/include/mobile-listener.php');

require_once(SLPLUS_PLUGINDIR . '/include/storelocatorplus-ajax_handler_class.php');
$slplus_plugin->AjaxHandler = new SLPlus_AjaxHandler(array('parent'=>$slplus_plugin));     // Lets invoke this and make it an object


// Regular Actions
//
add_action('init'               ,array($slplus_plugin->Actions,'init')                 );
add_action('wp_enqueue_scripts' ,array($slplus_plugin->Actions,'wp_enqueue_scripts')   );
add_action('wp_footer'          ,array($slplus_plugin->Actions,'wp_footer')            );
add_action('wp_head'            ,array($slplus_plugin->Actions,'wp_head')              );
add_action('shutdown'           ,array($slplus_plugin->Actions,'shutdown')             );

// Admin Actions
//
add_action('admin_menu'         ,array($slplus_plugin->Actions,'admin_menu')           );
add_action('admin_init'         ,array($slplus_plugin->Actions,'admin_init'),10        );
if (isset($slplus_plugin->ProPack)) {
    add_action('admin_head'         ,array($slplus_plugin->ProPack,'report_downloads')     );
}

// Short Codes
//
add_shortcode('STORE-LOCATOR', array($slplus_plugin->UI,'render_shortcode'));
add_shortcode('SLPLUS',array($slplus_plugin->UI,'render_shortcode'));
add_shortcode('slplus',array($slplus_plugin->UI,'render_shortcode'));

// Text Domains
//
load_plugin_textdomain(SLPLUS_PREFIX, false, SLPLUS_COREDIR . 'languages/');


//------------------------
// AJAX Hooks
//------------------------

// Mobile Listener
//
add_action('wp_ajax_csl_get_locations'          , array('csl_mobile_listener', 'GetLocations'));
add_action('wp_ajax_nopriv_csl_get_locations'   , array('csl_mobile_listener', 'GetLocations'));

// Ajax search
//
add_action('wp_ajax_csl_ajax_search'            , array($slplus_plugin->AjaxHandler,'csl_ajax_search'));
add_action('wp_ajax_nopriv_csl_ajax_search'     , array($slplus_plugin->AjaxHandler,'csl_ajax_search'));

add_action('wp_ajax_nopriv_csl_get_closest_location', array('csl_mobile_listener', 'GetClosestLocation'));

// Ajax Load
//
add_action('wp_ajax_csl_ajax_onload'            , array($slplus_plugin->AjaxHandler,'csl_ajax_onload'));
add_action('wp_ajax_nopriv_csl_ajax_onload'     , array($slplus_plugin->AjaxHandler,'csl_ajax_onload'));

// License resets
add_action('wp_ajax_license_reset_pages'        , array($slplus_plugin->AjaxHandler,'license_reset_pages'));
add_action('wp_ajax_license_reset_propack'      , array($slplus_plugin->AjaxHandler,'license_reset_propack'));

