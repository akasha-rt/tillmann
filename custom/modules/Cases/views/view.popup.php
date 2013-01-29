<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once('include/MVC/View/views/view.popup.php');
require_once('modules/MySettings/StoreQuery.php');

class CasesViewPopup extends ViewPopup {

    var $lv;
    var $storeQuery;

    function CasesViewPopup() {
        parent::ViewPopup();
    }

    function display() {
        global $popupMeta;
        $searchdefs = array();

        if (($this->bean instanceOf SugarBean) && !$this->bean->ACLAccess('list')) {
            ACLController::displayNoAccess();
            sugar_cleanup(true);
        }

        if (isset($_REQUEST['metadata']) && strpos($_REQUEST['metadata'], "..") !== false)
            die("Directory navigation attack denied.");
        if (!empty($_REQUEST['metadata']) && $_REQUEST['metadata'] != 'undefined'
                && file_exists('modules/' . $this->module . '/metadata/' . $_REQUEST['metadata'] . '.php')) // if custom metadata is requested
            require_once('modules/' . $this->module . '/metadata/' . $_REQUEST['metadata'] . '.php');
        elseif (file_exists('custom/modules/' . $this->module . '/metadata/popupdefs.php'))
            require_once('custom/modules/' . $this->module . '/metadata/popupdefs.php');
        elseif (file_exists('modules/' . $this->module . '/metadata/popupdefs.php'))
            require_once('modules/' . $this->module . '/metadata/popupdefs.php');

        if (!empty($popupMeta) && !empty($popupMeta['listviewdefs'])) {
            if (is_array($popupMeta['listviewdefs'])) {
                $listViewDefs[$this->module] = $popupMeta['listviewdefs'];
            } else {
                require_once($popupMeta['listviewdefs']);
            }
        } elseif (file_exists('custom/modules/' . $this->module . '/metadata/listviewdefs.php')) {
            require_once('custom/modules/' . $this->module . '/metadata/listviewdefs.php');
        } elseif (file_exists('modules/' . $this->module . '/metadata/listviewdefs.php')) {
            require_once('modules/' . $this->module . '/metadata/listviewdefs.php');
        }

        //check for searchdefs as well
        if (!empty($popupMeta) && !empty($popupMeta['searchdefs'])) {
            if (is_array($popupMeta['searchdefs'])) {
                $searchdefs[$this->module]['layout']['advanced_search'] = $popupMeta['searchdefs'];
            } else {
                require_once($popupMeta['searchdefs']);
            }
        } else if (empty($searchdefs) && file_exists('custom/modules/' . $this->module . '/metadata/searchdefs.php')) {
            require_once('custom/modules/' . $this->module . '/metadata/searchdefs.php');
        } else if (empty($searchdefs) && file_exists('modules/' . $this->module . '/metadata/searchdefs.php')) {
            require_once('modules/' . $this->module . '/metadata/searchdefs.php');
        }

        //if you click the pagination button, it will poplate the search criteria here
        if (!empty($this->bean) && isset($_REQUEST[$this->module . '2_' . strtoupper($this->bean->object_name) . '_offset'])) {
            if (!empty($_REQUEST['current_query_by_page'])) {
                $blockVariables = array('mass', 'uid', 'massupdate', 'delete', 'merge', 'selectCount',
                    'lvso', 'sortOrder', 'orderBy', 'request_data', 'current_query_by_page');
                $current_query_by_page = unserialize(base64_decode($_REQUEST['current_query_by_page']));
                foreach ($current_query_by_page as $search_key => $search_value) {
                    if ($search_key != $this->module . '2_' . strtoupper($this->bean->object_name) . '_offset'
                            && !in_array($search_key, $blockVariables)) {
                        $_REQUEST[$search_key] = $GLOBALS['db']->quote($search_value);
                    }
                }
            }
        }
        if (!empty($listViewDefs) && !empty($searchdefs)) {
            require_once('custom/include/Popups/advancedPopupSmarty.php'); //Reena
            $displayColumns = array();
            $filter_fields = array();

            $popup = new advancedPopupSmarty($this->bean, $this->module);

            $popup->mergeDisplayColumns = true;
            //check to see if popupdes contains searchdefs
            $popup->_popupMeta = $popupMeta;
            $popup->listviewdefs = $listViewDefs;
            $popup->searchdefs = $searchdefs;

            // Original Code 
//        if (isset($_REQUEST['query'])) {
//            $popup->searchForm->populateFromRequest();
//        }

            /**
             * @author Reena Sattani
             * 
             */
            if (!empty($_REQUEST['saved_search_select'])) {
                if ($_REQUEST['saved_search_select'] == '_none' || !empty($_REQUEST['button'])) {
                    $_SESSION['LastSavedView'][$_REQUEST['module']] = '';
                    unset($_REQUEST['saved_search_select']);
                    unset($_REQUEST['saved_search_select_name']);

                    //use the current search module, or the current module to clear out layout changes
                    if (!empty($_REQUEST['search_module']) || !empty($_REQUEST['module'])) {
                        $mod = !empty($_REQUEST['search_module']) ? $_REQUEST['search_module'] : $_REQUEST['module'];
                        global $current_user;
                        //Reset the current display columns to default.
                        $current_user->setPreference('ListViewDisplayColumns', array(), 0, $mod);
                    }
                } else if (empty($_REQUEST['button']) && (empty($_REQUEST['clear_query']) || $_REQUEST['clear_query'] != 'true')) {
                    $popup->saved_search = loadBean('SavedSearch');
                    $popup->saved_search->retrieveSavedSearch($_REQUEST['saved_search_select']);
                    $popup->saved_search->populateRequest();
                } elseif (!empty($_REQUEST['button'])) { // click the search button, after retrieving from saved_search
                    $_SESSION['LastSavedView'][$_REQUEST['module']] = '';
                    unset($_REQUEST['saved_search_select']);
                    unset($_REQUEST['saved_search_select_name']);
                }
            }
            $popup->storeQuery = new StoreQuery();
            if (!isset($_REQUEST['query'])) {
                $popup->storeQuery->loadQuery($popup->module);                
                unset($popup->storeQuery->query['request_data']);
                $popup->storeQuery->populateRequest();
            } else {
                $popup->storeQuery->saveFromRequest($popup->module);
            }

            if (!empty($_REQUEST['displayColumns'])) {
                foreach (explode('|', $_REQUEST['displayColumns']) as $num => $col) {
                    if (!empty($listViewDefs[$this->module][$col]))
                        $displayColumns[$col] = $listViewDefs[$this->module][$col];
                }
            }
            else {

                foreach ($listViewDefs[$this->module] as $col => $params) {
                    $filter_fields[strtolower($col)] = true;
                    if (!empty($params['related_fields'])) {
                        foreach ($params['related_fields'] as $field) {
                            //id column is added by query construction function. This addition creates duplicates
                            //and causes issues in oracle. #10165
                            if ($field != 'id') {
                                $filter_fields[$field] = true;
                            }
                        }
                    }
                    if (!empty($params['default']) && $params['default'])
                        $displayColumns[$col] = $params;
                }
            }
            $popup->displayColumns = $displayColumns;
            $popup->filter_fields = $filter_fields;
            $popup->searchForm = new advancedPopupSearchForm($popup->seed, $popup->module, $popup->action);
            $popup->searchForm->setup($searchdefs, $searchFields, 'include/SearchForm/tpls/SearchFormGeneric.tpl', $view, $popup->listViewDefs);
            $popup->searchForm->lv = $this->lv;

            if (isset($_REQUEST['query'])) {
                // we have a query
                if (!empty($_SERVER['HTTP_REFERER']) && preg_match('/action=EditView/', $_SERVER['HTTP_REFERER'])) { // from EditView cancel
                    $popup->searchForm->populateFromArray($popup->storeQuery->query);
                } else {
                    $popup->searchForm->populateFromRequest();
                }
            }
            //End - Reena

            $massUpdateData = '';
            if (isset($_REQUEST['mass'])) {
                foreach (array_unique($_REQUEST['mass']) as $record) {
                    $massUpdateData .= "<input style='display: none' checked type='checkbox' name='mass[]' value='$record'>\n";
                }
            }
            $popup->massUpdateData = $massUpdateData;

            $popup->setup('include/Popups/tpls/PopupGeneric.tpl');

            //We should at this point show the header and javascript even if to_pdf is true.
            //The insert_popup_header javascript is incomplete and shouldn't be relied on.
            if (isset($this->options['show_all']) && $this->options['show_all'] == false) {
                unset($this->options['show_all']);
                $this->options['show_javascript'] = true;
                $this->options['show_header'] = true;
                $this->_displayJavascript();
            }
            insert_popup_header(null, false);
            echo $popup->display();
        } else {
            if (file_exists('modules/' . $this->module . '/Popup_picker.php')) {
                require_once('modules/' . $this->module . '/Popup_picker.php');
            } else {
                require_once('include/Popups/Popup_picker.php');
            }

            $popup = new Popup_Picker();
            $popup->_hide_clear_button = true;
            echo $popup->process_page();
        }
        echo '<script type="text/javascript" src="custom/include/Popups/javascript/advancedPopUpJsFunctions.js"></script>';
    }

}

?>