<?php

require_once('custom/include/Dashlets/DashletGenericBarChart.php');

class WonOppByMonth extends DashletGenericBarChart {

    protected function getDataset() {
        global $db;
        $returnArrayWonOppBYMonth = array();

        $dashletSql = $this->getWonOppByMonth();
        $dashletData = $db->query($dashletSql);
        while ($row = $db->fetchByAssoc($dashletData)) {
            $index = $row['month'];
            $returnArrayWonOppBYMonth[$index] = $row['total_opp'];
        }
        $curr_month = date('M',strtotime('next month'));
        $mappingMonths = array(
            'Jan' => 'Jan',
            'Feb' => 'Feb',
            'Mar' => 'March',
            'Apr' => 'April',
            'May' => 'May',
            'Jun' => 'June',
            'Jul' => 'July',
            'Aug' => 'Aug',
            'Sep' => 'Sept',
            'Oct' => 'Oct',
            'Nov' => 'Nov',
            'Dec' => 'Dec',
        );

        foreach ($mappingMonths as $key => $value) {
            if ($key != $curr_month  || $curr_month == 'Jan') {
                if (array_key_exists($key, $returnArrayWonOppBYMonth)) {
                    $returnArray[$mappingMonths[$key]] = $returnArrayWonOppBYMonth[$key];
                } else {
                    $returnArray[$mappingMonths[$key]] = 0;
                }
            }else{
                break;
            }
        }


        return $returnArray;
    }

    protected function getWonOppByMonth() {
        return "SELECT 
                    DATE_FORMAT(opportunities.date_entered, '%b') AS month,
                    COUNT(opportunities.id) AS total_opp
                FROM
                    opportunities
                         JOIN
                    opportunities_contacts ON opportunities.id = opportunities_contacts.opportunity_id
                        AND opportunities_contacts.deleted = 0
                         JOIN
                    contacts ON contacts.id = opportunities_contacts.contact_id
                        AND contacts.deleted = 0
                WHERE
                    contacts.lead_source = 'EB15'
                        AND YEAR(opportunities.date_entered) = YEAR(CURDATE())
                        AND opportunities.sales_stage IN ('Closed Won' , 'Won')
                GROUP BY MONTH(opportunities.date_entered)
            ";
    }

}

?>