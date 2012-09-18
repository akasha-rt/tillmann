<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');
/* * *******************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2011 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 * ****************************************************************************** */

require_once 'include/Popups/PopupSmarty.php';
require_once('custom/include/SearchForm/Popups/advancedPopupSearchForm.php');

class advancedPopupSmarty extends PopupSmarty {

    function advancedPopupSmarty($seed, $module) {
        parent::PopupSmarty($seed, $module);
        $this->searchForm = new advancedPopupSearchForm($this->seed, $this->module);
    }

    /*
     * Display the Smarty template.  Here we are using the TemplateHandler for caching per the module.
     */

    function display($end = true) {
        global $app_strings;

        if (!is_file(sugar_cached("jsLanguage/{$GLOBALS['current_language']}.js"))) {
            require_once('include/language/jsLanguage.php');
            jsLanguage::createAppStringsCache($GLOBALS['current_language']);
        }
        $jsLang = getVersionedScript("cache/jsLanguage/{$GLOBALS['current_language']}.js", $GLOBALS['sugar_config']['js_lang_version']);

        $this->th->ss->assign('data', $this->data['data']);
        $this->data['pageData']['offsets']['lastOffsetOnPage'] = $this->data['pageData']['offsets']['current'] + count($this->data['data']);
        $this->th->ss->assign('pageData', $this->data['pageData']);

        $navStrings = array('next' => $GLOBALS['app_strings']['LNK_LIST_NEXT'],
            'previous' => $GLOBALS['app_strings']['LNK_LIST_PREVIOUS'],
            'end' => $GLOBALS['app_strings']['LNK_LIST_END'],
            'start' => $GLOBALS['app_strings']['LNK_LIST_START'],
            'of' => $GLOBALS['app_strings']['LBL_LIST_OF']);
        $this->th->ss->assign('navStrings', $navStrings);


        $associated_row_data = array();

        //C.L. - Bug 44324 - Override the NAME entry to not display salutation so that the data returned from the popup can be searched on correctly
        $searchNameOverride = !empty($this->seed) && $this->seed instanceof Person && (isset($this->data['data'][0]['FIRST_NAME']) && isset($this->data['data'][0]['LAST_NAME'])) ? true : false;

        global $locale;
        foreach ($this->data['data'] as $val) {
            $associated_row_data[$val['ID']] = $val;
            if ($searchNameOverride) {
                $associated_row_data[$val['ID']]['NAME'] = $locale->getLocaleFormattedName($val['FIRST_NAME'], $val['LAST_NAME']);
            }
        }
        $is_show_fullname = showFullName() ? 1 : 0;
        $json = getJSONobj();
        $this->th->ss->assign('jsLang', $jsLang);
        $this->th->ss->assign('lang', substr($GLOBALS['current_language'], 0, 2));
        $this->th->ss->assign('headerTpl', 'custom/include/Popups/tpls/advancedPopupHeader.tpl');
        $this->th->ss->assign('footerTpl', 'custom/include/Popups/tpls/advancedPopupFooter.tpl');
        $this->th->ss->assign('ASSOCIATED_JAVASCRIPT_DATA', 'var associated_javascript_data = ' . $json->encode($associated_row_data) . '; var is_show_fullname = ' . $is_show_fullname . ';');
        $this->th->ss->assign('module', $this->seed->module_dir);
        $request_data = empty($_REQUEST['request_data']) ? '' : $_REQUEST['request_data'];
        $this->th->ss->assign('request_data', $request_data);
        $this->th->ss->assign('fields', $this->fieldDefs);
        $this->th->ss->assign('formData', $this->formData);
        $this->th->ss->assign('APP', $GLOBALS['app_strings']);
        $this->th->ss->assign('MOD', $GLOBALS['mod_strings']);
        $this->th->ss->assign('popupMeta', $this->_popupMeta);
        $this->th->ss->assign('current_query', base64_encode(serialize($_REQUEST)));
        $this->th->ss->assign('customFields', $this->customFieldDefs);
        $this->th->ss->assign('numCols', NUM_COLS);
        $this->th->ss->assign('massUpdateData', $this->massUpdateData);
        $this->th->ss->assign('sugarVersion', $GLOBALS['sugar_version']);
        $this->th->ss->assign('should_process', $this->should_process);

        if ($this->_create) {
            $this->th->ss->assign('ADDFORM', $this->getQuickCreate()); //$this->_getAddForm());
            $this->th->ss->assign('ADDFORMHEADER', $this->_getAddFormHeader());
            $this->th->ss->assign('object_name', $this->seed->object_name);
        }
        $this->th->ss->assign('LIST_HEADER', get_form_header($GLOBALS['mod_strings']['LBL_LIST_FORM_TITLE'], '', false));
        $this->th->ss->assign('SEARCH_FORM_HEADER', get_form_header($GLOBALS['mod_strings']['LBL_SEARCH_FORM_TITLE'], '', false));
        $str = $this->th->displayTemplate($this->seed->module_dir, $this->view, $this->tpl);
        return $str;
    }

    /*
     * Setup up the smarty template. we added an extra step here to add the order by from the popupdefs.
     */

    function setup($file) {

        if (isset($this->_popupMeta)) {
            if (isset($this->_popupMeta['create']['formBase'])) {
                require_once('modules/' . $this->seed->module_dir . '/' . $this->_popupMeta['create']['formBase']);
                $this->_create = true;
            }
        }
        if (!empty($this->_popupMeta['create'])) {
            $formBase = new $this->_popupMeta['create']['formBaseClass']();
            if (isset($_REQUEST['doAction']) && $_REQUEST['doAction'] == 'save') {
                //If it's a new record, set useRequired to false
                $useRequired = empty($_REQUEST['id']) ? false : true;
                $formBase->handleSave('', false, $useRequired);
            }
        }

        $params = array();
        if (!empty($this->_popupMeta['orderBy'])) {
            $params['orderBy'] = $this->_popupMeta['orderBy'];
        }

        if (file_exists('custom/modules/' . $this->module . '/metadata/metafiles.php')) {
            require('custom/modules/' . $this->module . '/metadata/metafiles.php');
        } elseif (file_exists('modules/' . $this->module . '/metadata/metafiles.php')) {
            require('modules/' . $this->module . '/metadata/metafiles.php');
        }

        if (!empty($metafiles[$this->module]['searchfields'])) {
            require($metafiles[$this->module]['searchfields']);
        } elseif (file_exists('modules/' . $this->module . '/metadata/SearchFields.php')) {
            require('modules/' . $this->module . '/metadata/SearchFields.php');
        }
        $this->searchdefs[$this->module]['templateMeta']['maxColumns'] = 2;
        $this->searchdefs[$this->module]['templateMeta']['widths']['label'] = 10;
        $this->searchdefs[$this->module]['templateMeta']['widths']['field'] = 30;

        $this->searchForm->view = 'PopupSearchForm';
        $this->searchForm->setup($this->searchdefs, $searchFields, 'custom/include/SearchForm/tpls/CasePopup.tpl', 'advanced_search', $this->listviewdefs);  // Reena

        $lv = new ListViewSmarty();
        $displayColumns = array();
        if (!empty($_REQUEST['displayColumns'])) {
            foreach (explode('|', $_REQUEST['displayColumns']) as $num => $col) {
                if (!empty($listViewDefs[$this->module][$col]))
                    $displayColumns[$col] = $this->listviewdefs[$this->module][$col];
            }
        }
        else {
            foreach ($this->listviewdefs[$this->module] as $col => $para) {
                if (!empty($para['default']) && $para['default'])
                    $displayColumns[$col] = $para;
            }
        }
        $params['massupdate'] = true;
        if (!empty($_REQUEST['orderBy'])) {
            $params['orderBy'] = $_REQUEST['orderBy'];
            $params['overrideOrder'] = true;
            if (!empty($_REQUEST['sortOrder']))
                $params['sortOrder'] = $_REQUEST['sortOrder'];
        }

        $lv->displayColumns = $displayColumns;
        $this->searchForm->lv = $lv;
        //reena
        $this->searchForm->displaySavedSearch = true;


        $this->searchForm->populateFromRequest('advanced_search');
        $searchWhere = $this->_get_where_clause();
        $this->searchColumns = $this->searchForm->searchColumns;
        //parent::setup($this->seed, $file, $searchWhere, $params, 0, -1, $this->filter_fields);

        $this->should_process = true;

        if (isset($params['export'])) {
            $this->export = $params['export'];
        }
        if (!empty($params['multiSelectPopup'])) {
            $this->multi_select_popup = $params['multiSelectPopup'];
        }
        if (!empty($params['massupdate']) && $params['massupdate'] != false) {
            $this->show_mass_update_form = true;
            $this->mass = new MassUpdate();
            $this->mass->setSugarBean($this->seed);
            if (!empty($params['handleMassupdate']) || !isset($params['handleMassupdate'])) {
                $this->mass->handleMassUpdate();
            }
        }

        // create filter fields based off of display columns
        if (empty($this->filter_fields) || $this->mergeDisplayColumns) {
            foreach ($this->displayColumns as $columnName => $def) {
                $this->filter_fields[strtolower($columnName)] = true;
                if (!empty($def['related_fields'])) {
                    foreach ($def['related_fields'] as $field) {
                        //id column is added by query construction function. This addition creates duplicates
                        //and causes issues in oracle. #10165
                        if ($field != 'id') {
                            $this->filter_fields[$field] = true;
                        }
                    }
                }
                if (!empty($this->seed->field_defs[strtolower($columnName)]['db_concat_fields'])) {
                    foreach ($this->seed->field_defs[strtolower($columnName)]['db_concat_fields'] as $index => $field) {
                        if (!isset($this->filter_fields[strtolower($field)]) || !$this->filter_fields[strtolower($field)]) {
                            $this->filter_fields[strtolower($field)] = true;
                        }
                    }
                }
            }
            foreach ($this->searchColumns as $columnName => $def) {
                $this->filter_fields[strtolower($columnName)] = true;
            }
        }


        if (!empty($_REQUEST['query']) || (!empty($GLOBALS['sugar_config']['save_query']) && $GLOBALS['sugar_config']['save_query'] != 'populate_only')) {
            $data = $this->lvd->getListViewData($this->seed, $searchWhere, 0, -1, $this->filter_fields, $params, 'id');
        } else {
            $this->should_process = false;
            $data = array(
                'data' => array(),
                'pageData' => array(
                    'bean' => array('moduleDir' => $this->seed->module_dir),
                    'ordering' => '',
                    'offsets' => array('total' => 0, 'next' => 0, 'current' => 0),
                ),
            );
        }

        foreach ($this->displayColumns as $columnName => $def) {
            $seedName = strtolower($columnName);

            if (empty($this->displayColumns[$columnName]['type'])) {
                if (!empty($this->lvd->seed->field_defs[$seedName]['type'])) {
                    $seedDef = $this->lvd->seed->field_defs[$seedName];
                    $this->displayColumns[$columnName]['type'] = (!empty($seedDef['custom_type'])) ? $seedDef['custom_type'] : $seedDef['type'];
                } else {
                    $this->displayColumns[$columnName]['type'] = '';
                }
            }//fi empty(...)

            if (!empty($this->lvd->seed->field_defs[$seedName]['options'])) {
                $this->displayColumns[$columnName]['options'] = $this->lvd->seed->field_defs[$seedName]['options'];
            }

            //C.L. Fix for 11177
            if ($this->displayColumns[$columnName]['type'] == 'html') {
                $cField = $this->seed->custom_fields;
                if (isset($cField) && isset($cField->bean->$seedName)) {
                    $seedName2 = strtoupper($columnName);
                    $htmlDisplay = html_entity_decode($cField->bean->$seedName);
                    $count = 0;
                    while ($count < count($data['data'])) {
                        $data['data'][$count][$seedName2] = &$htmlDisplay;
                        $count++;
                    }
                }
            }//fi == 'html'

            if (!empty($this->lvd->seed->field_defs[$seedName]['sort_on'])) {
                $this->displayColumns[$columnName]['orderBy'] = $this->lvd->seed->field_defs[$seedName]['sort_on'];
            }
        }

        $this->process($file, $data, $this->seed->object_name);
    }

}

?>
