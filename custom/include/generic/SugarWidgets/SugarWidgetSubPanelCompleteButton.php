<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');
require_once('include/generic/SugarWidgets/SugarWidgetField.php');

//TODO Rename this to edit link
class SugarWidgetSubPanelCompleteButton extends SugarWidgetField {

    function displayHeaderCell($layout_def) {
        return '&nbsp;';
    }

    function displayList($layout_def) {
        global $app_strings;
        if ($layout_def['fields']['STATUS'] != 'Completed') {
            $html .= "<a href='javascript:void(0);' onclick='CompelteWfTasks(\"{$layout_def['fields']['ID']}\");' class='listViewTdToolsS1'>" . $app_strings['LNK_COMPLETE'] . "</a>&nbsp;";
        } else {
            $html .="&nbsp;";
        }
        return $html;
    }

}

?>