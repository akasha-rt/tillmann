<?php

require_once('custom/include/Dashlets/DashletGenericPieChart.php');

class OverDueTasksPerEmployee extends DashletGenericPieChart {

    protected $groupBy = array('username');

    protected function getDataset() {
        global $db;
        $returnArray = array();

        $dashletSql = $this->getOverDueTasksPerEmployeeQuery();
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
    protected function getOverDueTasksPerEmployeeQuery() {
        return "SELECT COUNT(tasks.id) AS total , CONCAT(users.first_name,' ',users.last_name) AS name , users.user_name AS username
                    FROM tasks 
                    LEFT JOIN users  ON users.id = tasks.assigned_user_id
                    WHERE tasks.deleted = 0 AND users.deleted = 0 AND tasks.date_due < NOW()
                    GROUP BY  username
                    ORDER BY username ASC";
    }

}

?>
