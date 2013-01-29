<?php

require_once('custom/include/Dashlets/DashletGenericBarChart.php');

class LatestOppPerEmployee extends DashletGenericBarChart {

    protected $_seedName = 'Employees';

    protected function getDataset() {
        global $db;
        $returnArray = array();

        $dashletSql = $this->getLatestOppPerEmployeeQuery();
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
    protected function getLatestOppPerEmployeeQuery() {

        return "SELECT COUNT(opportunities.id) AS total , CONCAT(users.first_name,' ',users.last_name) AS name , users.user_name AS username
                        FROM opportunities
                        LEFT JOIN users ON users.id = opportunities.assigned_user_id
                        WHERE opportunities.deleted = 0 AND users.deleted = 0
                        AND opportunities.date_entered > DATE_SUB(Now(),INTERVAL 7 DAY)
                        GROUP BY username
                        ORDER BY username ASC";
    }

}

?>