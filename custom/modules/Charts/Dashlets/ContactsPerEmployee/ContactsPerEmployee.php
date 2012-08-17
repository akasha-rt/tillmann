<?php

require_once('custom/include/Dashlets/DashletGenericPieChart.php');

class ContactsPerEmployee extends DashletGenericPieChart {

    protected $groupBy = array('username');

    protected function getDataset() {
        global $db;
        $returnArray = array();

        $dashletSql = $this->getContactsPerEmployeeQuery();
        $dashletData = $db->query($dashletSql);
        while ($row = $db->fetchByAssoc($dashletData)) {
            $returnArray[] = array('username' => (trim($row['name']) != '') ? $row['name'] : $row['username'], 'total' => $row['total']);
        }

        return $returnArray;
    }

    /**
     * To get Query
     * @return string $query
     * @author Dhaval Darji
     */
    protected function getContactsPerEmployeeQuery() {
        return "SELECT COUNT(contacts.id) AS total , CONCAT(users.first_name,' ',users.last_name) AS name , users.user_name AS username
                    FROM contacts 
                    LEFT JOIN users  ON users.id = contacts.assigned_user_id
                    WHERE contacts.deleted = 0 AND users.deleted = 0
                    GROUP BY  username
                    ORDER BY username ASC";
    }

}

?>
