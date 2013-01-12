<?php
/****************************************************************************
 ** file: core/templates/navbar.php
 **
 ** The top Store Locator Settings navigation bar.
 ***************************************************************************/

// Put all SLP sidebar nav items in main navbar
//
global $submenu, $slplus_plugin;
if (!isset($slplus_plugin) || !isset($submenu[$slplus_plugin->prefix]) || !is_array($submenu[$slplus_plugin->prefix])) {
    echo apply_filters('slp_navbar','');
} else {
    $content = 
        '<div id="slplus_navbar">' .
            '<div class="about-wrap"><h2 class="nav-tab-wrapper">';

    // Loop through all SLP sidebar menu items on admin page
    //
    foreach ($submenu[$slplus_plugin->prefix] as $slp_menu_item) {

        //--------------------------------------------
        // Check for Pro Pack, if not enabled skip:
        //  - Show Reports Tab
        //
        if (
                (!$slplus_plugin->license->packages['Pro Pack']->isenabled) &&
                ($slp_menu_item[0] == __('Reports',SLPLUS_PREFIX))
            ){
            continue;
        }

        // Create top menu item
        //
        $selectedTab = ((isset($_REQUEST['page']) && ($_REQUEST['page'] === $slp_menu_item[2])) ? ' nav-tab-active' : '' );
        $content .= apply_filters(
                'slp_navbar_item_tweak',
                '<a class="nav-tab'.$selectedTab.'" href="'.menu_page_url( $slp_menu_item[2], false ).'">'.
                    $slp_menu_item[0].
                '</a>'
                );
    }
    $content .= apply_filters('slp_navbar_item','');
    $content .='</h2></div></div>';
    echo apply_filters('slp_navbar',$content);
}
