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
            'supplier_url' => 'supplier_url',
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
                          bc_storedata_cstm.other_notes_c,
                          bc_storedata_cstm.supplier_url_c AS supplier_url
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
                        OR bc_storedata_cstm.supplier_url_c LIKE '%{$searchString}%'
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
        '</table></td></tr><tr><td><div style="overflow: auto;max-height:400px;">' .
        $finalResult .
        '</div></td></tr></tbody></table>';
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
                              threads.title                AS thname,
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
                              LEFT JOIN threads threads
                                ON threads.id = n_q.bean_id
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
                case "Threads":
                    $name[$bean_id][0] = $row['thname'];
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

//  @niranjan-Start 24/11/2012 for  Priority Task   
    public function action_updatetask() {
        $tasks = new Task();
        $tasks = $tasks->retrieve($_REQUEST['record']);
        $tasks->status = 'Completed';
        $tasks->save();
        exit;
    }

//  @niranjan-End 
    public function action_add_follow_list() {
        global $db, $current_user;
        $select_query = "SELECT id,deleted from followup where module_id='{$_REQUEST['record']}' and user_id='{$current_user->id}'";
        $select_result = $db->query($select_query);
        $select_row = $db->fetchByAssoc($select_result);
        if ($select_row) {
            $select_follow_query = "SELECT deleted from followup where module_id = '{$_REQUEST['record']}' and user_id='{$current_user->id}'";
            $select_follow_result = $db->query($select_follow_query);
            $row_follow = $db->fetchByAssoc($select_follow_result);
            $update_follow_query = "update followup set deleted =";
            if ($row_follow['deleted'] == '0')
                $update_follow_query .= '1';
            else
                $update_follow_query .= '0';
            $update_follow_query .= " where module_id='{$_REQUEST['record']}' and user_id='{$current_user->id}'";
            $update_follow_result = $db->query($update_follow_query);
        }else {
            $id = create_guid();
            $query = "insert into followup values('{$id}','{$_REQUEST['module_name']}','{$_REQUEST['record']}','{$_REQUEST['userId']}',0);";
            $db->query($query);
        }
        exit;
    }

    public function action_remove_follow_list() {
        global $db, $current_user;
        $update_follow_query = "update followup set deleted =1 where module_id='{$_REQUEST['record']}' and module_name='{$_REQUEST['module_name']}' and user_id='{$current_user->id}'";
        $update_follow_result = $db->query($update_follow_query);
        exit;
    }

    public function action_remove_sort_rows() {
        global $db, $current_user;
        if ($_REQUEST['my_item']) {
            $query = "SELECT  followup.id,followup.module_name,followup.module_id,followup.user_id,followup.deleted,cases.case_number,cases.name,cases.assigned_user_id,cases.status from followup,cases WHERE followup.module_name='Cases' and followup.user_id='{$current_user->id}' and followup.deleted=0 and followup.module_id=cases.id group by cases.id";
        } else {
            $query = "SELECT  followup.id,followup.module_name,followup.module_id,followup.user_id,followup.deleted,cases.case_number,cases.name,cases.assigned_user_id,cases.status from followup,cases WHERE followup.module_name='Cases' and followup.deleted=0 and followup.module_id=cases.id group by cases.id";
        }
        $result = $db->query($query);
        $totalRow = $result->num_rows;
        while ($row = $db->fetchByAssoc($result)) {
            $userObject = new User();
            $userObject->retrieve($row['assigned_user_id']);
            $case_name = "<a href='index.php?module=Cases&action=DetailView&record={$row['module_id']}'>" . $row['name'] . "</a>";
            $displayArray[] = array('number' => $row['case_number'], 'name' => $case_name, 'status' => $row['status'], 'user' => $userObject->name, 'id' => $row['module_id'], 'module' => $row['module_name'], 'name_row' => $row['name']);
            $countDisplayRow++;
        }
        if ($_REQUEST['my_item']) {
            $query = "SELECT  followup.id,followup.module_name,followup.module_id,followup.user_id,followup.deleted,tasks.name,tasks.assigned_user_id,tasks.status from followup,tasks WHERE followup.module_name='Task' and followup.user_id='{$current_user->id}' and followup.deleted=0 and followup.module_id=tasks.id group by tasks.id";
        } else {
            $query = "SELECT  followup.id,followup.module_name,followup.module_id,followup.user_id,followup.deleted,tasks.name,tasks.assigned_user_id,tasks.status from followup,tasks WHERE followup.module_name='Task'  and followup.deleted=0 and followup.module_id=tasks.id group by tasks.id";
        }
        $result = $db->query($query);
        $totalRow += $result->num_rows;
        while ($row = $db->fetchByAssoc($result)) {
            $userObject = new User();
            $userObject->retrieve($row['assigned_user_id']);
            $task_name = "<a href='index.php?module=Tasks&action=DetailView&record={$row['module_id']}'>" . $row['name'] . "</a>";
            $displayArray[] = array('number' => '-', 'name' => $task_name, 'status' => $row['status'], 'user' => $userObject->name, 'id' => $row['module_id'], 'module' => $row['module_name'], 'name_row' => $row['name']);
            $countDisplayRow++;
        }
        if ($_REQUEST['column_by'] == 'name')
            $orderBy = $_REQUEST['column_by'] . '_row';
        else
            $orderBy = $_REQUEST['column_by'];
        for ($start = 0; $start < (count($displayArray) - 1); $start++) {
            for ($second = 0; $second < (count($displayArray) - $start - 1); $second++) {
                if ($_REQUEST['ordered'] == 'ASC') {
                    if ($displayArray[$second][$orderBy] > $displayArray[$second + 1][$orderBy]) {
                        $swap = $displayArray[$second];
                        $displayArray[$second] = $displayArray[$second + 1];
                        $displayArray[$second + 1] = $swap;
                    }
                } else {
                    if ($displayArray[$second][$orderBy] < $displayArray[$second + 1][$orderBy]) {
                        $swap = $displayArray[$second];
                        $displayArray[$second] = $displayArray[$second + 1];
                        $displayArray[$second + 1] = $swap;
                    }
                }
            }
        }
        $printTrs = '';
        $endIndex = ($_REQUEST['numberOfRow'] < count($displayArray)) ? $_REQUEST['numberOfRow'] : count($displayArray);
        for ($start = 0; $start < $endIndex; $start++) {
            $printTrs .= "<tr class=\"oddListRowS1\" id='oddListRowS1'>";
            $printTrs .= "<td width='10px'><img src='custom/image/follow2.png' title='Remove from Watch List' style='height:17px;width:20px;cursor:pointer;' onclick='removeFromFollowList(\"{$displayArray[$start]['id']}\",\"{$displayArray[$start]['module']}\");'></td>";
            if ($displayArray[$start]['module'] == "Task")
                $printTrs .= "<td width='10px'><a href='index.php?module={$displayArray[$start]['module']}&record={$displayArray[$start]['id']}&action=DetailView'><img src='themes/Sugar5/images/icon_Tasks_32.gif' title='Task' style='height:17px;width:20px;cursor:pointer;' /></a></td>";
            else
                $printTrs .= "<td width='10px'><a href='index.php?module={$displayArray[$start]['module']}&record={$displayArray[$start]['id']}&action=DetailView'><img src='themes/Sugar5/images/icon_Cases_32.gif' title='Case' style='height:17px;width:20px;cursor:pointer;' /></a></td>";
            foreach ($displayArray[$start] as $key => $value) {
                if ($key != "module" && $key != "id" && $key != 'name_row')
                    $printTrs .= "<td valign='top'>$value</td>";
            }
            $printTrs .= "</tr>";
        }
        echo $printTrs;
        exit;
    }

    public function action_pagination() {
        global $db, $current_user;
        if ($_REQUEST['my_item']) {
            $query = "SELECT  followup.id,followup.module_name,followup.module_id,followup.user_id,followup.deleted,cases.case_number,cases.name,cases.assigned_user_id,cases.status from followup,cases WHERE followup.module_name='Cases' and followup.user_id='{$current_user->id}' and followup.deleted=0 and followup.module_id=cases.id group by cases.id";
        } else {
            $query = "SELECT  followup.id,followup.module_name,followup.module_id,followup.user_id,followup.deleted,cases.case_number,cases.name,cases.assigned_user_id,cases.status from followup,cases WHERE followup.module_name='Cases' and followup.deleted=0 and followup.module_id=cases.id group by cases.id";
        }
        $result = $db->query($query);
        $totalRow = $result->num_rows;
        while ($row = $db->fetchByAssoc($result)) {
            $userObject = new User();
            $userObject->retrieve($row['assigned_user_id']);
            $case_name = "<a href='index.php?module=Cases&action=DetailView&record={$row['module_id']}'>" . $row['name'] . "</a>";
            $displayArray[] = array('number' => $row['case_number'], 'name' => $case_name, 'status' => $row['status'], 'user' => $userObject->name, 'id' => $row['module_id'], 'module' => $row['module_name'], 'name_row' => $row['name']);
            $countDisplayRow++;
        }
        if ($_REQUEST['my_item']) {
            $query = "SELECT  followup.id,followup.module_name,followup.module_id,followup.user_id,followup.deleted,tasks.name,tasks.assigned_user_id,tasks.status from followup,tasks WHERE followup.module_name='Task' and followup.user_id='{$current_user->id}' and followup.deleted=0 and followup.module_id=tasks.id group by tasks.id";
        } else {
            $query = "SELECT  followup.id,followup.module_name,followup.module_id,followup.user_id,followup.deleted,tasks.name,tasks.assigned_user_id,tasks.status from followup,tasks WHERE followup.module_name='Task'  and followup.deleted=0 and followup.module_id=tasks.id group by tasks.id";
        }
        $result = $db->query($query);
        $totalRow += $result->num_rows;
        while ($row = $db->fetchByAssoc($result)) {
            $userObject = new User();
            $userObject->retrieve($row['assigned_user_id']);
            $task_name = "<a href='index.php?module=Tasks&action=DetailView&record={$row['module_id']}'>" . $row['name'] . "</a>";
            $displayArray[] = array('number' => '-', 'name' => $task_name, 'status' => $row['status'], 'user' => $userObject->name, 'id' => $row['module_id'], 'module' => $row['module_name'], 'name_row' => $row['name']);
            $countDisplayRow++;
        }
        if ($_REQUEST['last_sort'] != "") {
            if ($_REQUEST['sort_direction'] == "")
                $_REQUEST['sort_direction'] = "ASC";
            $orderBy = '';
            if ($_REQUEST['last_sort'] == 'name')
                $orderBy = $_REQUEST['last_sort'] . '_row';
            else
                $orderBy = $_REQUEST['last_sort'];
            for ($start = 0; $start < (count($displayArray) - 1); $start++) {
                for ($second = 0; $second < (count($displayArray) - $start - 1); $second++) {
                    if ($_REQUEST['sort_direction'] == 'ASC') {
                        if ($displayArray[$second][$orderBy] > $displayArray[$second + 1][$orderBy]) {
                            $swap = $displayArray[$second];
                            $displayArray[$second] = $displayArray[$second + 1];
                            $displayArray[$second + 1] = $swap;
                        }
                    } else {
                        if ($displayArray[$second][$orderBy] < $displayArray[$second + 1][$orderBy]) {
                            $swap = $displayArray[$second];
                            $displayArray[$second] = $displayArray[$second + 1];
                            $displayArray[$second + 1] = $swap;
                        }
                    }
                }
            }
        }
        $printTrs = '';
        if ($_REQUEST['direction'] == 'next') {
            for ($start = ($_REQUEST['start'] + $_REQUEST['number_row']); $start < ($_REQUEST['start'] + ($_REQUEST['number_row'] * 2)); $start++) {
                if ($displayArray[$start] != null) {
                    $printTrs .= "<tr class=\"oddListRowS1\" id='oddListRowS1'>";
                    $printTrs .= "<td width='10px'><img src='custom/image/follow2.png' title='Remove from Watch List' style='height:17px;width:20px;cursor:pointer;' onclick='removeFromFollowList(\"{$displayArray[$start]['id']}\",\"{$displayArray[$start]['module']}\");'></td>";
                    if ($displayArray[$start]['module'] == "Task")
                        $printTrs .= "<td width='10px'><a href='index.php?module={$displayArray[$start]['module']}&record={$displayArray[$start]['id']}&action=DetailView'><img src='themes/Sugar5/images/icon_Tasks_32.gif' title='Task' style='height:17px;width:20px;cursor:pointer;' /></a></td>";
                    else
                        $printTrs .= "<td width='10px'><a href='index.php?module={$displayArray[$start]['module']}&record={$displayArray[$start]['id']}&action=DetailView'><img src='themes/Sugar5/images/icon_Cases_32.gif' title='Case' style='height:17px;width:20px;cursor:pointer;' /></a></td>";
                    foreach ($displayArray[$start] as $key => $value) {
                        if ($key != "module" && $key != "id" && $key != 'name_row')
                            $printTrs .= "<td valign='top'>$value</td>";
                    }
                    $printTrs .= "</tr>";
                }
            }
        }else if ($_REQUEST['direction'] == 'prev') {
            for ($start = ($_REQUEST['start'] - $_REQUEST['number_row']); $start < (($_REQUEST['start'] - $_REQUEST['number_row']) + $_REQUEST['number_row']); $start++) {
                if ($displayArray[$start] != null) {
                    $printTrs .= "<tr class=\"oddListRowS1\" id='oddListRowS1'>";
                    $printTrs .= "<td width='10px'><img src='custom/image/follow2.png' title='Remove from Watch List' style='height:17px;width:20px;cursor:pointer;' onclick='removeFromFollowList(\"{$displayArray[$start]['id']}\",\"{$displayArray[$start]['module']}\");'></td>";
                    if ($displayArray[$start]['module'] == "Task")
                        $printTrs .= "<td width='10px'><a href='index.php?module={$displayArray[$start]['module']}&record={$displayArray[$start]['id']}&action=DetailView'><img src='themes/Sugar5/images/icon_Tasks_32.gif' title='Task' style='height:17px;width:20px;cursor:pointer;' /></a></td>";
                    else
                        $printTrs .= "<td width='10px'><a href='index.php?module={$displayArray[$start]['module']}&record={$displayArray[$start]['id']}&action=DetailView'><img src='themes/Sugar5/images/icon_Cases_32.gif' title='Case' style='height:17px;width:20px;cursor:pointer;' /></a></td>";
                    foreach ($displayArray[$start] as $key => $value) {
                        if ($key != "module" && $key != "id" && $key != 'name_row')
                            $printTrs .= "<td valign='top'>$value</td>";
                    }
                    $printTrs .= "</tr>";
                }
            }
        }else if ($_REQUEST['direction'] == 'end') {
            $remain = count($displayArray) % $_REQUEST['number_row'];
            for ($start = ($remain == 0) ? (count($displayArray) - 3) : (count($displayArray) - $remain); $start < (count($displayArray) + $remain); $start++) {
                if ($displayArray[$start] != null) {
                    $printTrs .= "<tr class=\"oddListRowS1\" id='oddListRowS1'>";
                    $printTrs .= "<td width='10px'><img src='custom/image/follow2.png' title='Remove from Watch List' style='height:17px;width:20px;cursor:pointer;' onclick='removeFromFollowList(\"{$displayArray[$start]['id']}\",\"{$displayArray[$start]['module']}\");'></td>";
                    if ($displayArray[$start]['module'] == "Task")
                        $printTrs .= "<td width='10px'><a href='index.php?module={$displayArray[$start]['module']}&record={$displayArray[$start]['id']}&action=DetailView'><img src='themes/Sugar5/images/icon_Tasks_32.gif' title='Task' style='height:17px;width:20px;cursor:pointer;' /></a></td>";
                    else
                        $printTrs .= "<td width='10px'><a href='index.php?module={$displayArray[$start]['module']}&record={$displayArray[$start]['id']}&action=DetailView'><img src='themes/Sugar5/images/icon_Cases_32.gif' title='Case' style='height:17px;width:20px;cursor:pointer;' /></a></td>";
                    foreach ($displayArray[$start] as $key => $value) {
                        if ($key != "module" && $key != "id" && $key != 'name_row')
                            $printTrs .= "<td valign='top'>$value</td>";
                    }
                    $printTrs .= "</tr>";
                }
            }
        }else if ($_REQUEST['direction'] == 'start') {
            for ($start = 0; $start < $_REQUEST['number_row']; $start++) {
                if ($displayArray[$start] != null) {
                    $printTrs .= "<tr class=\"oddListRowS1\" id='oddListRowS1'>";
                    $printTrs .= "<td width='10px'><img src='custom/image/follow2.png' title='Remove from Watch List' style='height:17px;width:20px;cursor:pointer;' onclick='removeFromFollowList(\"{$displayArray[$start]['id']}\",\"{$displayArray[$start]['module']}\");'></td>";
                    if ($displayArray[$start]['module'] == "Task")
                        $printTrs .= "<td width='10px'><a href='index.php?module={$displayArray[$start]['module']}&record={$displayArray[$start]['id']}&action=DetailView'><img src='themes/Sugar5/images/icon_Tasks_32.gif' title='Task' style='height:17px;width:20px;cursor:pointer;' /></a></td>";
                    else
                        $printTrs .= "<td width='10px'><a href='index.php?module={$displayArray[$start]['module']}&record={$displayArray[$start]['id']}&action=DetailView'><img src='themes/Sugar5/images/icon_Cases_32.gif' title='Case' style='height:17px;width:20px;cursor:pointer;' /></a></td>";
                    foreach ($displayArray[$start] as $key => $value) {
                        if ($key != "module" && $key != "id" && $key != 'name_row')
                            $printTrs .= "<td valign='top'>$value</td>";
                    }
                    $printTrs .= "</tr>";
                }
            }
        }

        echo $printTrs;
        exit;
    }

}

?>