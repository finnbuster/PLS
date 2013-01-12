<?php

/***********************************************************************
* Class: SLPlus_AdminUI_ManageLocations
*
* The Store Locator Plus admin UI Manage Locations class.
*
* Provides various UI functions when someone is an admin on the WP site.
*
************************************************************************/

if (! class_exists('SLPlus_AdminUI_ManageLocations')) {
    class SLPlus_AdminUI_ManageLocations {
        
        /******************************
         * PUBLIC PROPERTIES & METHODS
         ******************************/
        public $parent = null;
        public $settings = null;

        /**
         * Called when this object is created.
         *
         * @param type $params
         */
        function __construct($params=null) {
            if (!$this->setParent()) {
                die('could not set parent');
                return;
                }
        }

        /**
         * Set the parent property to point to the primary plugin object.
         *
         * Returns false if we can't get to the main plugin object.
         *
         * @global wpCSL_plugin__slplus $slplus_plugin
         * @return type boolean true if plugin property is valid
         */
        function setParent() {
            if (!isset($this->parent) || ($this->parent == null)) {
                global $slplus_plugin;
                $this->parent = $slplus_plugin;
            }
            return (isset($this->parent) && ($this->parent != null));
        }


        /**
         * Render the manage locations admin page.
         *
         */
        function render_adminpage() {
            if (!$this->setParent()) { return; }
            global $wpdb;

            // Script
            //
            ?>
            <script language="JavaScript">
                /*=================== Confirming Button Click ===================== */
                function confirmClick(message,href) {
                    if (confirm(message)) {	location.href=href; }
                    else  { return false; }
                }

                /* ================= For Player Form: Checks All or None ======== */
                function checkAll(cbox,formObj) {
                    var i=0;
                    if (cbox.checked==true)
                        cbox.checked==false;
                    else
                        cbox.checked==true;
                    while (formObj.elements[i]!=null) {
                        formObj.elements[i].checked=cbox.checked;
                        i++;
                    }
                }
            </script>

            <?php
            // Header Text
            //
            print "<div class='wrap'>
                        <div id='icon-edit-locations' class='icon32'><br/></div>
                        <h2>".
                        __('Store Locator Plus - Manage Locations', SLPLUS_PREFIX).
                        "</h2>" .
                  $this->parent->helper->get_string_from_phpexec(SLPLUS_COREDIR.'/templates/navbar.php')
                  ;


            // Form and variable setup for processing
            //
            $sl_hidden='';
            foreach($_REQUEST as $key=>$val) {
                if ($key!="q" && $key!="o" && $key!="sortorder" && $key!="start" && $key!="act" && $key!='sl_tags' && $key!='sl_id') {
                    $sl_hidden.="<input type='hidden' value='$val' name='$key'>\n";
                }
            }
            $this->parent->AdminUI->initialize_variables();

            // If delete link is clicked
            if (isset($_GET['delete']) && ($_GET['delete']!='')) {
                $wpdb->query("DELETE FROM ".$wpdb->prefix."store_locator ".
                    "WHERE sl_id='".$_GET['delete']."'");
            }

            //------------------------------------------------------------------------
            // EDITING
            //------------------------------------------------------------------------
            if ($_POST                                                  &&
                (isset($_GET['edit']) && $_GET['edit'])                 &&
                (!isset($_POST['act']) || (isset($_POST['act']) && ($_POST['act']!="delete")))
                ) {

                // Get our original address first
                //
                $old_address=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."store_locator WHERE sl_id=$_GET[edit]", ARRAY_A);
                if (!isset($old_address[0]['sl_address']))  { $old_address[0]['sl_address'] = '';   }
                if (!isset($old_address[0]['sl_address2'])) { $old_address[0]['sl_address2'] = '';  }
                if (!isset($old_address[0]['sl_city'])) 	{ $old_address[0]['sl_city'] = ''; 	    }
                if (!isset($old_address[0]['sl_state'])) 	{ $old_address[0]['sl_state'] = ''; 	}
                if (!isset($old_address[0]['sl_zip'])) 	    { $old_address[0]['sl_zip'] = ''; 		}

                // Update The Location Data
                //
                $field_value_str = '';
                foreach ($_POST as $key=>$sl_value) {
                    if (preg_match('#\-'.$_GET['edit'].'#', $key)) {
                        $slpFieldName = preg_replace('#\-'.$_GET['edit'].'#', '', $key);
                        if (!$this->parent->license->packages['Pro Pack']->isenabled) {
                            if ( ($slpFieldName == 'latitude') || ($slpFieldName == 'longitude')) {
                                continue;
                            }
                        }
                        $field_value_str.="sl_".$slpFieldName."='".trim($this->parent->AdminUI->slp_escape($sl_value))."', ";
                        $_POST[$slpFieldName]=$sl_value;
                    }
                }
                $field_value_str=substr($field_value_str, 0, strlen($field_value_str)-2);
                $field_value_str = apply_filters('slp_update_location_data',$field_value_str,$_GET['edit']);
                $wpdb->query("UPDATE ".$wpdb->prefix."store_locator SET $field_value_str WHERE sl_id=$_GET[edit]");

                // Check our address
                //
                if (!isset($_POST['address'])   ) { $_POST['address'] = '';     }
                if (!isset($_POST['address2'])  ) { $_POST['address2'] = '';    }
                if (!isset($_POST['city'])      ) { $_POST['city'] = '';        }
                if (!isset($_POST['state'])     ) { $_POST['state'] = '';       }
                if (!isset($_POST['zip'])       ) { $_POST['zip'] = '';         }
                $the_address=
                        $_POST['address']   .' '    .
                        $_POST['address2']  .', '   .
                        $_POST['city']      .', '   .
                        $_POST['state']     .' '    .
                        $_POST['zip'];

                // RE-geocode if the address changed
                // or if the lat/long is not set
                //
                if (   ($the_address!=
                        $old_address[0]['sl_address'].' '.$old_address[0]['sl_address2'].', '.$old_address[0]['sl_city'].', '.
                        $old_address[0]['sl_state'].' '.$old_address[0]['sl_zip']
                        ) ||
                        ($old_address[0]['sl_latitude']=="" || $old_address[0]['sl_longitude']=="")
                        ) {
                    $this->parent->AdminUI->do_geocoding($the_address,$_GET['edit']);
                }

                // Redirect to the edit page
                //
                $pageRedirect = "<script>location.replace('".preg_replace('/&edit='.$_GET['edit'].'/', '',$_SERVER['REQUEST_URI'])."');</script>";
                print apply_filters('slp_edit_location_redirect',$pageRedirect);
            }

            //------------------------------------------------------------------------
            // ACTION HANDLER
            // If post action is set
            //------------------------------------------------------------------------
            if ($_POST) {extract($_POST);}
            if (isset($_REQUEST['act'])) {

                // Delete Action
                if ($_REQUEST['act']=="delete") {
                    if (isset($sl_id)) {

                        // use this to delete 100 at a time
                        //
                        $delQueries = array();

                        if (is_array($sl_id)==1) {
                            $id_string="";
                            $idCount = 0;
                            foreach ($sl_id as $sl_value) {
                                $idCount++;
                                $id_string.="$sl_value,";

                                // Got 100?  Push a delete string on the stack
                                //
                                if ($idCount == 100) {
                                    $idCount = 0;
                                    $id_string=substr($id_string, 0, strlen($id_string)-1);
                                    array_push($delQueries,"DELETE FROM ".$wpdb->prefix."store_locator WHERE sl_id IN ($id_string)");
                                    $id_string='';
                                }
                            }

                            // Clean up any stragglers
                            //
                            $id_string=substr($id_string, 0, strlen($id_string)-1);

                        // Single Item Delete
                        //
                        } else {
                            $id_string=$sl_id;
                        }

                        // push the last one on the stack
                        //
                        if ($id_string != ''){
                            array_push($delQueries,"DELETE FROM ".$wpdb->prefix."store_locator WHERE sl_id IN ($id_string)");
                        }

                        // Run deletions
                        //
                        foreach ($delQueries as $delQuery) {
                            $delete_result = $wpdb->query($delQuery);
                            if ($delete_result == 0) {
                                $errorMessage .= __("Could not delete the locations.  ", SLPLUS_PREFIX);
                                $theDBError = htmlspecialchars(mysql_error($wpdb->dbh),ENT_QUOTES);
                                if ($theDBError != '') {
                                    $errorMessage .= sprintf(
                                                            __("Error: %s.", SLPLUS_PREFIX),
                                                            $theDBError
                                                            );
                                } elseif ($delete_result === 0) {
                                    $errorMessage .=  __("It appears the delete was for no records.", SLPLUS_PREFIX);
                                } else {
                                    $errorMessage .=  __("No error logged.", SLPLUS_PREFIX);
                                    $errorMessage .= "<br/>\n" . __('Query: ', SLPLUS_PREFIX);
                                    $errorMessage .= print_r($wpdb->last_query,true);
                                    $errorMessage .= "<br/>\n" . "Results: " . gettype($delete_result) . ' '. $delete_result;
                                }

                            }

                        }
                    }

                // Tagging Action
                }  elseif (preg_match('#tag#i', $_REQUEST['act'])) {

                    //adding or removing tags for specified a locations
                    if (isset($sl_id)) {
                        if (is_array($sl_id)) {
                            $id_string='';
                            foreach ($sl_id as $sl_value) {
                                $id_string.="$sl_value,";
                            }
                            $id_string=substr($id_string, 0, strlen($id_string)-1);
                        } else {
                            $id_string=$sl_id;
                        }

                        // If we have some store IDs
                        //
                        if ($id_string != '') {
                            //adding tags
                            if ($act=="add_tag") {
                                $wpdb->query("UPDATE ".$wpdb->prefix."store_locator SET sl_tags=CONCAT(sl_tags, ',".strtolower($sl_tags).", ') WHERE sl_id IN ($id_string)");

                            //removing tags
                            } elseif ($act=="remove_tag") {
                                if (empty($sl_tags)) {
                                    //if no tag is specified, all tags will be removed from selected locations
                                    $wpdb->query("UPDATE ".$wpdb->prefix."store_locator SET sl_tags='' WHERE sl_id IN ($id_string)");
                                } else {
                                    $wpdb->query("UPDATE ".$wpdb->prefix."store_locator SET sl_tags=REPLACE(sl_tags, ',$sl_tags,', '') WHERE sl_id IN ($id_string)");
                                }
                            }
                        }
                    }

                // Locations Per Page Action
                } elseif ($_REQUEST['act']=="locationsPerPage") {
                    update_option('sl_admin_locations_per_page', $_REQUEST['sl_admin_locations_per_page']);
                    extract($_REQUEST);

                // Change View Action
                //
                } elseif ($_REQUEST['act']=='changeview') {
                    if (get_option('sl_location_table_view') == 'Expanded') {
                        update_option('sl_location_table_view', 'Normal');
                    } else {
                        update_option('sl_location_table_view', 'Expanded');
                    }

                // Recode The Address
                //
                } elseif ($_REQUEST['act']=='recode') {
                    if (isset($_REQUEST['sl_id'])) {
                        if (!is_array($_REQUEST['sl_id'])) {
                            $theLocations = array($_REQUEST['sl_id']);
                        } else {
                            $theLocations = $_REQUEST['sl_id'];
                        }

                        // Process SL_ID Array
                        //
                        foreach ($theLocations as $thisLocation) {
                                $address=$wpdb->get_row("SELECT * FROM ".$wpdb->prefix."store_locator WHERE sl_id=$thisLocation", ARRAY_A);

                                if (!isset($address['sl_address'])) { $address['sl_address'] = '';  print 'BLANK<br/>';	}
                                if (!isset($address['sl_address2'])){ $address['sl_address2'] = ''; }
                                if (!isset($address['sl_city'])) 	{ $address['sl_city'] = ''; 	}
                                if (!isset($address['sl_state'])) 	{ $address['sl_state'] = ''; 	}
                                if (!isset($address['sl_zip'])) 	{ $address['sl_zip'] = ''; 		}

                                $this->parent->AdminUI->do_geocoding("$address[sl_address] $address[sl_address2], $address[sl_city], $address[sl_state] $address[sl_zip]",$thisLocation);
                        }
                    }

                // Create Store Page(s)
                //
                } elseif ($_REQUEST['act'] == 'createpage') {
                    if (isset($_REQUEST['sl_id'])) {
                        if (!is_array($_REQUEST['sl_id'])) {
                            $theLocations = array($_REQUEST['sl_id']);
                        } else {
                            $theLocations = $_REQUEST['sl_id'];
                        }

                        foreach ($theLocations as $thisLocation) {
                            $slpNewPostID = $this->parent->StorePages->CreatePage($thisLocation);
                            if ($slpNewPostID >= 0) {
                                $slpNewPostURL = get_permalink($slpNewPostID);
                                $wpdb->query("UPDATE ".$wpdb->prefix."store_locator ".
                                                "SET sl_linked_postid=$slpNewPostID, ".
                                                "sl_pages_url='$slpNewPostURL' ".
                                                "WHERE sl_id=$thisLocation"
                                                );
                                print "<div class='updated settings-error'>" .
                                        ( (isset($_REQUEST['slp_pageid']) && ($slpNewPostID != $_REQUEST['slp_pageid']))?'Created new ':'Updated ').
                                        " store page #<a href='$slpNewPostURL'>$slpNewPostID</a>" .
                                        " for location # $thisLocation" .
                                        "</div>\n";
                            } else {
                                print "<div class='updated settings-error'>Could NOT create page" .
                                        " for location # $thisLocation" .
                                        "</div>\n";
                            }
                        }
                    }
                } //--- Create Page Action

                do_action('slp_managelocations_action');
                
            } //--- REQUEST['act'] is set


            //------------------------------------------------------------------------
            // CHANGE UPDATER
            // Changing Updater
            //------------------------------------------------------------------------
            if (isset($_GET['changeUpdater']) && ($_GET['changeUpdater']==1)) {
                if (get_option('sl_location_updater_type')=="Tagging") {
                    update_option('sl_location_updater_type', 'Multiple Fields');
                    $updaterTypeText="Multiple Fields";
                } else {
                    update_option('sl_location_updater_type', 'Tagging');
                    $updaterTypeText="Tagging";
                }
                $_SERVER['REQUEST_URI']=preg_replace('/&changeUpdater=1/', '', $_SERVER['REQUEST_URI']);
                print "<script>location.replace('".$_SERVER['REQUEST_URI']."');</script>";
            }


            //------------------------------------------------------------------------
            // QUERY BUILDING
            //------------------------------------------------------------------------
            $qry = isset($_REQUEST['q']) ? $_REQUEST['q'] : '';
            $where=($qry!='')?
                    " WHERE ".
                    "sl_store    LIKE '%$qry%' OR ".
                    "sl_address  LIKE '%$qry%' OR ".
                    "sl_address2 LIKE '%$qry%' OR ".
                    "sl_city     LIKE '%$qry%' OR ".
                    "sl_state    LIKE '%$qry%' OR ".
                    "sl_zip      LIKE '%$qry%' OR ".
                    "sl_tags     LIKE '%$qry%' "
                    :
                    '' ;

            /* Uncoded items */
            if (isset($_REQUEST['act'])) {
                if ($_REQUEST['act'] == 'show_uncoded') {
                    if ($where == '') { $where = 'WHERE '; }
                    $where .= ' sl_latitude IS NULL or sl_longitude IS NULL';
                }
            }


            $opt= (isset($_GET['o']) && (trim($_GET['o']) != ''))
            ? $_GET['o'] : "sl_store";
            $dir= (isset($_GET['sortorder']) && (trim($_GET['sortorder'])=='DESC'))
            ? 'DESC' : 'ASC';

            // Get the sort order and direction out of our URL
            //
            $slpCleanURL = str_replace("&o=$opt&sortorder=$dir", '', $_SERVER['REQUEST_URI']);

            //------------------------------------------------------------------------
            // UI
            //------------------------------------------------------------------------

            // Pagination
            //
            $totalLocations=$wpdb->get_var("SELECT count(sl_id) FROM ".$wpdb->prefix."store_locator $where");
            $start=(isset($_GET['start'])&&(trim($_GET['start'])!=''))?$_GET['start']:0;
            $num_per_page=get_option('sl_admin_locations_per_page','50');
            if ($totalLocations>0) {
                $this->parent->AdminUI->manage_locations_pagination(
                        $totalLocations,
                        $num_per_page,
                        $start
                        );
            }

            // Actionbar Section
            //
            print '<form id="manage_locations_actionbar_form" name="locationForm" method="post">'.
                    '<input name="act" type="hidden">' .
                    '<div id="slplus_actionbar">' .
                        $this->parent->helper->get_string_from_phpexec(SLPLUS_COREDIR.'/templates/managelocations_actionbar.php') .
                    '</div>'
                    ;

            // Search Filter, no actions
            // Clear the start, we want all records
            //
            if (isset($_POST['q']) && ($_POST['q'] != '') && ($_POST['act'] == '')) {
                $start = 0;
            }

            // We have matching locations
            //
            if ($slpLocations=$wpdb->get_results(
                    "SELECT * FROM " .$wpdb->prefix."store_locator " .
                            "$where ORDER BY $opt $dir ".
                            "LIMIT $start,$num_per_page",
                    ARRAY_A
                    )
                ) {

                // Setup Table Columns
                //
                $slpManageColumns = array(
                        'sl_id'         =>  __('ID'       ,SLPLUS_PREFIX),
                        'sl_store'      =>  __('Name'     ,SLPLUS_PREFIX),
                        'sl_address'    =>  __('Street'   ,SLPLUS_PREFIX),
                        'sl_address2'   =>  __('Street2'  ,SLPLUS_PREFIX),
                        'sl_city'       =>  __('City'     ,SLPLUS_PREFIX),
                        'sl_state'      =>  __('State'    ,SLPLUS_PREFIX),
                        'sl_zip'        =>  __('Zip'      ,SLPLUS_PREFIX),
                        'sl_tags'       =>  __('Tags'     ,SLPLUS_PREFIX),
                    );

                // Expanded View
                //
                if (get_option('sl_location_table_view')!="Normal") {
                    $slpManageColumns = array_merge($slpManageColumns,
                                array(
                                    'sl_description'    => __('Description'  ,SLPLUS_PREFIX),
                                    'sl_url'            => get_option('sl_website_label','Website'),
                                )
                            );

                    // Store Pages URLs
                    //
                    if ($this->parent->license->packages['Store Pages']->isenabled) {
                        $slpManageColumns = array_merge($slpManageColumns,
                                    array(
                                        'sl_pages_url'      => __('Pages URL'          ,SLPLUS_PREFIX),
                                    )
                                );
                    }

                    $slpManageColumns = array_merge($slpManageColumns,
                                array(
                                    'sl_email'       => __('Email'        ,SLPLUS_PREFIX),
                                    'sl_hours'       => $this->parent->settings->get_item('label_hours','Hours','_'),
                                    'sl_phone'       => $this->parent->settings->get_item('label_phone','Phone','_'),
                                    'sl_fax'         => $this->parent->settings->get_item('label_fax'  ,'Fax'  ,'_'),
                                    'sl_image'       => __('Image'        ,SLPLUS_PREFIX),
                                )
                            );

                }
                $slpManageColumns = apply_filters('slp_manage_location_columns', $slpManageColumns);


                // Get the manage locations table header
                //
                $tableHeaderString = $this->parent->AdminUI->manage_locations_table_header($slpManageColumns,$slpCleanURL,$opt,$dir);
                print  "<div id='location_table_wrapper'>" .
                            "<table id='manage_locations_table' class='slplus wp-list-table widefat fixed posts' cellspacing=0>" .
                                $tableHeaderString;

                // Render The Data
                //
                $bgcol = '#eee';
                foreach ($slpLocations as $sl_value) {

                    // Row color
                    //
                    $bgcol=($bgcol=="#eee")?"#fff":"#eee";
                    $bgcol=($sl_value['sl_latitude']=="" || $sl_value['sl_longitude']=="")? "salmon" : $bgcol;

                    // Clean Up Data with trim()
                    //
                    $locID = $sl_value['sl_id'];
                    $sl_value=array_map("trim",$sl_value);

                    // EDIT MODE
                    // Show the edit form in a new row for the location that was selected.
                    //
                    if (isset($_GET['edit']) && ($locID==$_GET['edit'])) {
                        print
                            "<tr id='slp_location_edit_row'>"               .
                            "<td class='slp_locationinfoform_cell' colspan='".(count($slpManageColumns)+4)."'>".
                            $this->parent->AdminUI->createString_LocationInfoForm($sl_value, $locID) .
                            '</td></tr>';

                    // DISPLAY MODE
                    //
                    } else {

                        // Custom Filters to set the links on special data like URLs and Email
                        //
                        $sl_value['sl_url']=(!$this->parent->AdminUI->url_test($sl_value['sl_url']) && trim($sl_value['sl_url'])!="")?
                            "http://".$sl_value['sl_url'] :
                            $sl_value['sl_url'] ;
                        $sl_value['sl_url']=($sl_value['sl_url']!="")?
                            "<a href='$sl_value[sl_url]' target='blank'>".__("View", SLPLUS_PREFIX)."</a>" :
                            "" ;
                        $sl_value['sl_email']=($sl_value['sl_email']!="")?
                            "<a href='mailto:$sl_value[sl_email]' target='blank'>".__("Email", SLPLUS_PREFIX)."</a>" :
                            "" ;
                        $sl_value['sl_image']=($sl_value['sl_image']!="")?
                            "<a href='$sl_value[sl_image]' target='blank'>".__("View", SLPLUS_PREFIX)."</a>" :
                            "" ;
                        $sl_value['sl_description']=($sl_value['sl_description']!="")?
                            "<a onclick='alert(\"".$this->parent->AdminUI->slp_escape($sl_value['sl_description'])."\")' href='#'>".
                            __("View", SLPLUS_PREFIX)."</a>" :
                            "" ;

                        print
                        "<tr style='background-color:$bgcol'>" .
                            "<th><input type='checkbox' name='sl_id[]' value='$locID'></th>" .
                            "<th class='thnowrap'>".
                            "<a class='action_icon edit_icon' alt='".__('edit',SLPLUS_PREFIX)."' title='".__('edit',SLPLUS_PREFIX)."'
                                href='".preg_replace('/&edit='.(isset($_GET['edit'])?$_GET['edit']:'').'/', '',$_SERVER['REQUEST_URI']).
                            "&edit=" . $locID ."#a$locID'></a>".
                            "&nbsp;" .
                            "<a class='action_icon delete_icon' alt='".__('delete',SLPLUS_PREFIX)."' title='".__('delete',SLPLUS_PREFIX)."'
                                href='".$_SERVER['REQUEST_URI']."&delete=$locID' " .
                                "onclick=\"confirmClick('".sprintf(__('Delete %s?',SLPLUS_PREFIX),$sl_value['sl_store'])."', this.href); return false;\"></a>";

                        // Store Pages Active?
                        // Show the create page button & fix up the sl_pages_url data
                        //
                        if ($this->parent->license->packages['Store Pages']->isenabled) {
                            $shortSPurl = preg_replace('/^.*?store_page=/','',$sl_value['sl_pages_url']);
                            $sl_value['sl_pages_url'] = "<a href='$sl_value[sl_pages_url]' target='cybersprocket'>$shortSPurl</a>";
                            call_user_func_array(array('SLPlus_AdminUI','slpRenderCreatePageButton'),array($locID,$sl_value['sl_linked_postid']));
                        }
                        print "</th>";

                        // Data Columns
                        //
                        foreach ($slpManageColumns as $slpField => $slpLabel) {
                            print '<td>' . apply_filters('slp_column_data',$sl_value[$slpField], $slpField, $slpLabel) . '</td>';
                        }

                        // Lat/Long Columns
                        //
                        print
                                '<td>'.$sl_value['sl_latitude'] .'</td>' .
                                '<td>'.$sl_value['sl_longitude'].'</td>' .
                            '</tr>';
                    }
                }

                // Close Out Table
                //
                print $tableHeaderString .'</table></div>';

            // No Locations Found
            //
            } else {

                    print "<div class='csa_info_msg'>".
                            (
                             ($qry!='')?
                                    __("Search Locations returned no matches.", SLPLUS_PREFIX) :
                                    __("No locations have been created yet.", SLPLUS_PREFIX)
                            ) .
                          "</div>";
            }


            if ($totalLocations!=0) {
                $this->parent->AdminUI->manage_locations_pagination(
                        $totalLocations,
                        $num_per_page,
                        $start
                        );
            }
            print "</form></div>";
        }

    }
}        
     

