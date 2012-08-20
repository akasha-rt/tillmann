<?php

require_once('custom/include/Dashlets/DashletGenericBarChart.php');

class TasksPerEmployee extends DashletGenericBarChart {

    protected $_seedName = 'Employees';

    protected function getDataset() {
        global $db;
        $returnArray = array();

        $dashletSql = $this->getTasksPerEmployeeQuery();
        $dashletData = $db->query($dashletSql);
        while ($row = $db->fetchByAssoc($dashletData)) {
            $index = (trim($row['name']) != '') ? $row['name'] : $row['username'];
            $returnArray[$index] = $row['total'];
        }

        return $returnArray;
    }

    /**
     * To get Query
     * @return string $query
     * @author Dhaval Darji
     */
    protected function getTasksPerEmployeeQuery() {
        return "SELECT COUNT(tasks.id) AS total , CONCAT(users.first_name,' ',users.last_name) AS name , users.user_name AS username
                    FROM tasks 
                    LEFT JOIN users  ON users.id = tasks.assigned_user_id
                    WHERE tasks.deleted = 0 AND users.deleted = 0 AND tasks.status != 'Completed'
                    GROUP BY  username
                    ORDER BY username ASC";
    }

}

?>