<?php

require_once 'include/SearchForm/SearchForm2.php';

class advancedPopupSearchForm extends SearchForm {

    function advancedPopupSearchForm($seed, $module, $action = 'index') {
        parent::SearchForm($seed, $module, $action);
    }

    function display($header = true) {
        global $theme, $timedate, $current_user;
        $header_txt = '';
        $footer_txt = '';
        $return_txt = '';
        $this->th->ss->assign('module', $this->module);
        $this->th->ss->assign('action', $this->action);
        $this->th->ss->assign('displayView', $this->displayView);
        $this->th->ss->assign('APP', $GLOBALS['app_strings']);
        //Show the tabs only if there is more than one
        if ($this->nbTabs > 1) {
            $this->th->ss->assign('TABS', $this->_displayTabs($this->module . '|' . $this->displayView));
        }
        $this->th->ss->assign('searchTableColumnCount', ((isset($this->searchdefs['templateMeta']['maxColumns']) ? $this->searchdefs['templateMeta']['maxColumns'] : 2) * 2 ) - 1);
        $this->th->ss->assign('fields', $this->fieldDefs);
        $this->th->ss->assign('customFields', $this->customFieldDefs);
        $this->th->ss->assign('formData', $this->formData);
        $time_format = $timedate->get_user_time_format();
        $this->th->ss->assign('TIME_FORMAT', $time_format);
        $this->th->ss->assign('USER_DATEFORMAT', $timedate->get_user_date_format());
        $this->th->ss->assign('CALENDAR_FDOW', $current_user->get_first_day_of_week());

        $date_format = $timedate->get_cal_date_format();
        $time_separator = ":";
        if (preg_match('/\d+([^\d])\d+([^\d]*)/s', $time_format, $match)) {
            $time_separator = $match[1];
        }
        // Create Smarty variables for the Calendar picker widget
        $t23 = strpos($time_format, '23') !== false ? '%H' : '%I';
        if (!isset($match[2]) || $match[2] == '') {
            $this->th->ss->assign('CALENDAR_FORMAT', $date_format . ' ' . $t23 . $time_separator . "%M");
        } else {
            $pm = $match[2] == "pm" ? "%P" : "%p";
            $this->th->ss->assign('CALENDAR_FORMAT', $date_format . ' ' . $t23 . $time_separator . "%M" . $pm);
        }
        $this->th->ss->assign('TIME_SEPARATOR', $time_separator);

        //Show and hide the good tab form
        foreach ($this->tabs as $tabkey => $viewtab) {
            $viewName = str_replace(array($this->module . '|', '_search'), '', $viewtab['key']);
            if (strpos($this->view, $viewName) !== false) {
                $this->tabs[$tabkey]['displayDiv'] = '';
                //if this is advanced tab, use form with saved search sub form built in
                if ($viewName == 'advanced') {
                    $this->tpl = 'custom/include/SearchForm/Popups/tpls/advancedPopupSearchFormGenericAdvanced.tpl'; //Reena
                    if ($this->action == 'ListView') {
                        $this->th->ss->assign('DISPLAY_SEARCH_HELP', true);
                    }
                    $this->th->ss->assign('DISPLAY_SAVED_SEARCH', $this->displaySavedSearch);
                    $this->th->ss->assign('SAVED_SEARCH', $this->displaySavedSearch());
                    //this determines whether the saved search subform should be rendered open or not
                    if (isset($_REQUEST['showSSDIV']) && $_REQUEST['showSSDIV'] == 'yes') {
                        $this->th->ss->assign('SHOWSSDIV', 'yes');
                        $this->th->ss->assign('DISPLAYSS', '');
                    } else {
                        $this->th->ss->assign('SHOWSSDIV', 'no');
                        $this->th->ss->assign('DISPLAYSS', 'display:none');
                    }
                }
            } else {
                $this->tabs[$tabkey]['displayDiv'] = 'display:none';
            }
        }

        $this->th->ss->assign('TAB_ARRAY', $this->tabs);

        $totalWidth = 0;
        if (isset($this->searchdefs['templateMeta']['widths'])
                && isset($this->searchdefs['templateMeta']['maxColumns'])) {
            $totalWidth = ( $this->searchdefs['templateMeta']['widths']['label'] +
                    $this->searchdefs['templateMeta']['widths']['field'] ) *
                    $this->searchdefs['templateMeta']['maxColumns'];
            // redo the widths in case they are too big
            if ($totalWidth > 100) {
                $resize = 100 / $totalWidth;
                $this->searchdefs['templateMeta']['widths']['label'] =
                        $this->searchdefs['templateMeta']['widths']['label'] * $resize;
                $this->searchdefs['templateMeta']['widths']['field'] =
                        $this->searchdefs['templateMeta']['widths']['field'] * $resize;
            }
        }
        $this->th->ss->assign('templateMeta', $this->searchdefs['templateMeta']);
        $this->th->ss->assign('HAS_ADVANCED_SEARCH', !empty($this->searchdefs['layout']['advanced_search']));
        $this->th->ss->assign('displayType', $this->displayType);
        // return the form of the shown tab only
        if ($this->showSavedSearchesOptions) {
            $this->th->ss->assign('SAVED_SEARCHES_OPTIONS', $this->displaySavedSearchSelect());
        }
        if ($this->module == 'Documents') {
            $this->th->ss->assign('DOCUMENTS_MODULE', true);
        }
        $return_txt = $this->th->displayTemplate($this->seed->module_dir, 'SearchForm_' . $this->parsedView, $this->tpl);
        if ($header) {
            $this->th->ss->assign('return_txt', $return_txt);
            $header_txt = $this->th->displayTemplate($this->seed->module_dir, 'SearchFormHeader', 'include/SearchForm/tpls/header.tpl');
            //pass in info to render the select dropdown below the form
            $footer_txt = $this->th->displayTemplate($this->seed->module_dir, 'SearchFormFooter', 'include/SearchForm/tpls/footer.tpl');

            $return_txt = $header_txt . $footer_txt;
        }
        return $return_txt;
    }

}

