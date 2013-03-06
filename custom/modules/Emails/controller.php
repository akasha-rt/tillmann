

<?php

require_once('include/MVC/Controller/SugarController.php');

class EmailsController extends SugarController {

    public function __construct() {
        parent::SugarController();
    }

    public function action_template() {

        global $db;
        $dept = $_GET['dept'];
        $idx = $_GET['idx'];

        $query_order = "SELECT id,name,department
                        FROM email_templates
                        WHERE department = '" . $dept . "' AND deleted = 0 ORDER BY name ASC ";
        $result = $db->query($query_order);
        $html = '';
        $html .= '<select name="email_template' . $idx . '" id="email_template' . $idx . '"  onchange="SUGAR.email2.composeLayout.applyEmailTemplate(' . $idx . ', this.value);">';
        $html .= '<option value="">-none-</option>';
        while ($res_order = $db->fetchByAssoc($result)) {
            $html .= '<option value="' . $res_order['id'] . '">';
            $html .= $res_order['name'];
            $html .= '</option>';
        }
        $html .= '</select>';
        echo $html . "||" . $idx;
        exit;
    }

    public function action_EmailQuickSearch() {
        $result = $this->QuickSearch();
        if ($result && !empty($result)) {
            echo json_encode($result);
        } else {
            echo '';
        }
        exit;
    }

    private function QuickSearch() {
        global $db;
        $result = array();
        $search_text = (isset($_REQUEST['name']) && !empty($_REQUEST['name']) ? $_REQUEST['name'] : '');

        if (empty($search_text)) {
            return false;
        }

        $sql = "SELECT email_addresses.email_address as email,email_addr_bean_rel.bean_id AS bean_id,
            email_addr_bean_rel.bean_module AS bean_module,accounts.name,contacts.first_name,contacts.last_name,
            leads.first_name,leads.last_name
            FROM email_addresses
            LEFT JOIN email_addr_bean_rel ON email_addr_bean_rel.email_address_id = email_addresses.id
            LEFT JOIN accounts ON email_addr_bean_rel.bean_id = accounts.id AND accounts.deleted = 0 
            LEFT JOIN contacts ON email_addr_bean_rel.bean_id = contacts.id AND contacts.deleted = 0 
            LEFT JOIN leads ON email_addr_bean_rel.bean_id = leads.id AND leads.deleted = 0 
            WHERE email_addresses.deleted = 0 AND email_addr_bean_rel.deleted = 0 
            AND (email_addresses.email_address LIKE '{$search_text}%' OR  accounts.name LIKE '{$search_text}%' 
                OR contacts.first_name LIKE '{$search_text}%' OR contacts.last_name LIKE '{$search_text}%'
                OR leads.first_name LIKE '{$search_text}%' OR leads.last_name LIKE '{$search_text}%')";

        $rs = $db->query($sql);
        if (!$rs) {
            return false;
        }
        $i = 0;
        while ($rows = $db->fetchByAssoc($rs)) {
            $tblname = lcfirst($rows['bean_module']);

            if ($tblname != "accounts")
                $select = " CONCAT(IF({$tblname}.first_name IS NULL,'',{$tblname}.first_name),' ',IF({$tblname}.last_name IS NULL,'',{$tblname}.last_name)) AS name ";
            else
                $select = " IF({$tblname}.name IS NULL,'',{$tblname}.name) AS name ";

            $sel_name = "SELECT {$select}
                    FROM {$tblname}
                    WHERE {$tblname}.id = '{$rows['bean_id']}' AND {$tblname}.deleted = 0";
            $result = $db->query($sel_name, false, '', false, false, (__FILE__ . ' Line_' . __LINE__), 'QuickSearch');
            $row = $db->fetchByAssoc($result);

            $r[$i]['name'] = $row['name'] . "&lt" . $rows['email'] . "&gt";
            $r[$i]['option'] = $row['name'] . "<" . $rows['email'] . ">";

            $i++;
        }
        return $r;
    }

}
?>