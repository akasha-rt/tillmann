<?php

require_once('custom/include/Dashlets/DashletGenericBarChart.php');

class ComplaintPerMonth extends DashletGenericBarChart
{

    protected function getDataset()
    {
        global $db;
        $returnArrayMonthComplaint = array();

        $dashletSql = $this->getComplaintPerMonth();
        $dashletData = $db->query($dashletSql);
        while ($row = $db->fetchByAssoc($dashletData)) {
            $index = $row['MONTH'];
            $returnArrayMonthComplaint[$index] = $row['Total'];
        }
        $mappingMonths = array(
            '1' => 'Jan',
            '2' => 'Feb',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'Aug',
            '9' => 'Sept',
            '10' => 'Oct',
            '11' => 'Nov',
            '12' => 'Dec',
        );
        foreach ($returnArrayMonthComplaint as $key => $value) {
            $returnArray[$mappingMonths[$key]] = $value;
        }


        return $returnArray;
    }

    protected function getComplaintPerMonth()
    {
        return "SELECT
                        MONTH(cases.date_entered) AS MONTH,
                        COUNT(cases.id) AS Total
                      FROM cases
                        JOIN cases_cstm
                          ON cases.id = cases_cstm.id_c
                            AND cases.deleted = 0
                      WHERE cases_cstm.technical_c = 'Complaint'
                          AND YEAR(date_entered) = YEAR(CURDATE())
                      GROUP BY MONTH(date_entered)";
    }

}

?>