<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once('include/MVC/Controller/SugarController.php');

class HomeController extends SugarController {

    function action_lookup() {
        global $db;
        $searchString = $_GET['search'];
        $targetModString = return_module_language('en_us', 'bc_StoreData');

        $lookUpSql = "SELECT bc_storedata.name,
                          bc_storedata.catalognumber,
                          bc_storedata.sku,
                          bc_storedata.supplierid,
                          bc_storedata.immunogen,
                          bc_storedata.purchasingemail,
                          bc_storedata.purchasingname,
                          bc_storedata.supportemail,
                          bc_storedata.supportname
                FROM bc_storedata
                WHERE (id LIKE '%{$searchString}%'
                        OR name LIKE '%{$searchString}%'
                        OR description LIKE '%{$searchString}%'
                        OR catalognumber LIKE '%{$searchString}%'
                        OR sku LIKE '%{$searchString}%'
                        OR supplierid LIKE '%{$searchString}%'
                        OR immunogen LIKE '%{$searchString}%'
                        OR purchasingemail LIKE '%{$searchString}%'
                        OR purchasingname LIKE '%{$searchString}%'
                        OR supportemail LIKE '%{$searchString}%'
                        OR supportname LIKE '%{$searchString}%')
                        AND deleted = 0";

        $lookUpResult = $db->query($lookUpSql);

        $lookResultCount = $db->getRowCount($lookUpResult);

        $fields_array = $db->getFieldsArray($lookUpResult, true);
        //$field_with_lablel = array();
        $lookUpHeader = '<table width="100%" cellpadding="2" cellspacing="0" class="olFgClass"><tr>';
        foreach ($fields_array as $key => $field) {
            //$field_with_lablel[$field] = $targetModString['LBL_' . strtoupper($field)];
            //$lookUpHeader .= "<td><strong>{$field_with_lablel[$field]}</strong></td>";
            $lookUpHeader .= "<td><strong>{$targetModString['LBL_' . strtoupper($field)]}</strong></td>";
        }
        $lookUpHeader .= "</tr>";

        $lookUpRows = '';
        while ($result_row = $db->fetchByAssoc($lookUpResult)) {
            $lookUpRows .= '<tr>';
            foreach ($result_row as $key => $values) {
                $lookUpRows .= "<td>{$values}</td>";
            }
            $lookUpRows .= '</tr>';
        }
        $lookUpRows .= "</table>";

        $finalResult = '';
        if ($lookResultCount == 0) {
            $finalResult = '<table width="500px" cellpadding="2" cellspacing="0" class="olFgClass">
                    <tr><td>No Records Found</td></tr></table>';
        } else {
            $finalResult = $lookUpHeader . $lookUpRows;
        }

        echo '<table width="100%" border="0" cellpadding="1" cellspacing="0" class="olBgClass">' .
        '<tbody><tr><td>' .
        '<table width="100%" border="0" cellpadding="2" cellspacing="0" class="olCgClass">' .
        '<tbody><tr><td width="100%" class="olCgClass">' .
        '<div class="olCapFontClass">' .
        '<div style="float:left">Look-up Result </div>' .
        '<div style="float: right">' .
        '<a href="#" onClick="$(\'#lookup_result_div\').hide();" title="Click to Close" id="closediv">' .
        '<img border="0" style="margin-left:2px; margin-right: 2px;" src="index.php?entryPoint=getImage&amp;themeName=Sugar5&amp;imageName=close.gif">' .
        '</a></div></div>' .
        '</td></tr></tbody>' .
        '</table>' .
        $finalResult .
        '</td></tr></tbody></table>';
        exit;
    }

}

?>