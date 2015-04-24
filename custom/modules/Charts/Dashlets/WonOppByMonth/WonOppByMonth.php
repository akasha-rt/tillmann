<?php

require_once('custom/include/Dashlets/DashletGenericBarChart.php');

class WonOppByMonth extends DashletGenericBarChart
{

    protected function getDataset()
    {
        global $db;
        $returnArrayWonOppBYMonth = array();

        $dashletSql = $this->getWonOppByMonth();
        $dashletData = $db->query($dashletSql);
        while ($row = $db->fetchByAssoc($dashletData)) {
            $index = $row['month'];
            $returnArrayWonOppBYMonth[$index] = $row['total_opp'];
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
        foreach ($returnArrayWonOppBYMonth as $key => $value) {
            $returnArray[$mappingMonths[$key]] = $value;
        }


        return $returnArray;
    }

    protected function getWonOppByMonth()
    {
        return "SELECT 
                    MONTH(opportunities.date_entered) AS month,
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