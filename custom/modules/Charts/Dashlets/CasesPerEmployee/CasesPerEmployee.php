<?php

require_once('custom/include/Dashlets/DashletGenericPieChart.php');

class CasesPerEmployee extends DashletGenericPieChart {

    protected $groupBy = array('username');

    protected function getDataset() {
        global $db;
        $returnArray = array();

        $dashletSql = $this->getCasesPerEmployeeQuery();
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
    protected function getCasesPerEmployeeQuery() {
        return "SELECT COUNT(cases.id) AS total , CONCAT(users.first_name,' ',users.last_name) AS name , users.user_name AS username
                    FROM cases 
                    LEFT JOIN users  ON users.id = cases.assigned_user_id
                    WHERE cases.deleted = 0 AND users.deleted = 0
                    GROUP BY  username
                    ORDER BY username ASC";
    }

}

?>
