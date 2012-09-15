<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once('include/MVC/Controller/SugarController.php');

class HomeController extends SugarController {

    function action_lookup() {
        global $db;
        $searchString = $_GET['search'];
        $searchString = ($searchString) ? $searchString : '@@@@@';
        $targetModString = return_module_language('en_us', 'bc_StoreData');
        $productField = array(
            'name' => 'name',
            'catalognumber' => 'catalognumber',
            'sku' => 'sku',
            'supplierid' => 'supplierid',
            'immunogen' => 'immunogen',
            'admin_immunogen_c' => 'admin_immunogen',
            'purchasingemail' => 'purchasingemail',
            'purchasingname' => 'purchasingname',
            'supportemail' => 'supportemail',
            'supportname' => 'supportname'
        );
        $orderField = array(
            'order_number_c' => 'order_number',
            'customer_po_number_c' => 'customer_po_number',
            'order_status_c' => 'order_status',
            'other_notes_c' => 'other_notes'
        );
        $lookUpSql = "SELECT bc_storedata.name,
                          bc_storedata.catalognumber,
                          bc_storedata.sku,
                          bc_storedata.supplierid,
                          bc_storedata.immunogen,
                          bc_storedata_cstm.admin_immunogen_c,
                          bc_storedata.purchasingemail,
                          bc_storedata.purchasingname,
                          bc_storedata.supportemail,
                          bc_storedata.supportname,
                          bc_storedata_cstm.order_number_c,
                          bc_storedata_cstm.customer_po_number_c,
                          bc_storedata_cstm.order_status_c,
                          bc_storedata_cstm.other_notes_c
                FROM bc_storedata
                LEFT JOIN bc_storedata_cstm
                    ON bc_storedata_cstm.id_c = bc_storedata.id
                WHERE (bc_storedata.id LIKE '%{$searchString}%'
                        OR bc_storedata.name LIKE '%{$searchString}%'
                        OR bc_storedata.description LIKE '%{$searchString}%'
                        OR bc_storedata.catalognumber LIKE '%{$searchString}%'
                        OR bc_storedata.sku LIKE '%{$searchString}%'
                        OR bc_storedata.supplierid LIKE '%{$searchString}%'
                        OR bc_storedata.immunogen LIKE '%{$searchString}%'
                        OR bc_storedata.purchasingemail LIKE '%{$searchString}%'
                        OR bc_storedata.purchasingname LIKE '%{$searchString}%'
                        OR bc_storedata.supportemail LIKE '%{$searchString}%'
                        OR bc_storedata.supportname LIKE '%{$searchString}%'
                        OR bc_storedata_cstm.order_number_c LIKE '%{$searchString}%'
                        OR bc_storedata_cstm.customer_po_number_c LIKE '%{$searchString}%'
                        OR bc_storedata_cstm.order_status_c LIKE '%{$searchString}%'
                        OR bc_storedata_cstm.other_notes_c LIKE '%{$searchString}%'
                        OR bc_storedata_cstm.admin_immunogen_c LIKE '%{$searchString}%')
                        AND deleted = 0";

        $lookUpResult = $db->query($lookUpSql);

        $lookResultCount = $db->getRowCount($lookUpResult);

        $productData = array();
        $orderData = array();
        $productCounter = 0;
        $orderCounter = 0;
        while ($result_row = $db->fetchByAssoc($lookUpResult)) {
            if (isset($result_row['catalognumber']) && $result_row['catalognumber'] != "") {
                foreach ($productField as $field => $lableKey) {
                    $productData[$productCounter][$field] = $result_row[$field];
                }
                $productCounter++;
            } else {
                foreach ($orderField as $field => $lableKey) {
                    $orderData[$orderCounter][$field] = $result_row[$field];
                }
                $orderCounter++;
            }
        }

        $lookUpTable = '<table width="100%" cellpadding="2" cellspacing="0" class="olFgClass">
                <th style="float: left;">Products</th><tr>';
        //For Products first
        foreach ($productField as $key => $field) {
            $lookUpTable .= "<td><strong>{$targetModString['LBL_' . strtoupper($field)]}</strong></td>";
        }
        $lookUpTable .= "</tr>";
        foreach ($productData as $key => $result_row) {
            $lookUpTable .= '<tr>';
            foreach ($result_row as $key => $values) {
                $lookUpTable .= "<td>{$values}</td>";
            }
            $lookUpTable .= '</tr>';
        }
        //if no product data?
        if (count($productData) < 1) {
            $lookUpTable .= "<tr><td  colspan='" . count($productField) . "'>No Records Found</td></tr>";
        }
        $lookUpTable .= '</table>';
        $lookUpTable .= '<table width="100%" cellpadding="2" cellspacing="0" class="olFgClass"><th style="float: left;">Orders</th><tr>';
        foreach ($orderField as $key => $field) {
            $lookUpTable .= "<td><strong>{$targetModString['LBL_' . strtoupper($field)]}</strong></td>";
        }
        $lookUpTable .= "</tr>";
        foreach ($orderData as $key => $result_row) {
            $lookUpTable .= '<tr>';
            foreach ($result_row as $key => $values) {
                $lookUpTable .= "<td>{$values}</td>";
            }
            $lookUpTable .= '</tr>';
        }
        //if no product data?
        if (count($orderData) < 1) {
            $lookUpTable .= "<tr><td colspan='" . count($orderField) . "'>No Records Found</td></tr>";
        }
        $lookUpTable .= "</table>";


        $finalResult = '';
        if ($lookResultCount == 0) {
            $finalResult = '<table width="500px" cellpadding="2" cellspacing="0" class="olFgClass">
                    <tr><td>No Records Found</td></tr></table>';
        } else {
            $finalResult = $lookUpTable;
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

    /**
     * @author Reena Sattani
     * @return 
     */
    public function action_getnotify() {
        global $db, $current_user;
        $name = array();
        $sql = $db->query("SELECT * FROM (SELECT
                              n_q.id        AS nid,
                              n_q.bean_id   AS bean_id,
                              n_q.bean_type AS bean_type,
                              emails.name                  AS ename,
                              users.user_name              AS uname,
                              notes.name                   AS nname,
                              cases.name                   AS cname,
                              tasks.name                   AS tname,
                              n_q.date_time
                            FROM notification_queue n_q
                              LEFT JOIN emails emails
                                ON emails.id = n_q.bean_id
                              LEFT JOIN notes notes
                                ON notes.id = n_q.bean_id
                              LEFT JOIN tasks tasks
                                ON tasks.id = n_q.bean_id
                              LEFT JOIN cases cases
                                ON cases.id = n_q.bean_id
                              LEFT JOIN users users
                                ON n_q.userid = users.id
                            WHERE n_q.is_notify = 0
                                AND users.id = '{$current_user->id}' ORDER BY date_time DESC) AS Notify_data
                            GROUP BY bean_type
                            ORDER BY date_time DESC");
        while ($row = $db->fetchByAssoc($sql)) {

            $bean_id = $row['bean_id'];
            $bean_type = $row['bean_type'];
            switch ($bean_type) {
                case "Emails":
                    $name[$bean_id][0] = $row['ename'];
                    break;

                case "Tasks":
                    $name[$bean_id][0] = $row['tname'];
                    break;

                case "Cases":
                    $name[$bean_id][0] = $row['cname'];
                    break;

                case "Notes":
                    $name[$bean_id][0] = $row['nname'];
                    break;
            }

            $name[$bean_id][1] = $bean_type;
            $curr_user = $row['uname'];
        }

        $name = serialize($name);
        $result = $name . "||" . $curr_user;
        $updateSql = $db->query("UPDATE notification_queue
                                        SET is_notify = 1
                                       WHERE is_notify = 0 AND userid = '{$current_user->id}'");

        echo $result;
        exit;
    }

}

?>