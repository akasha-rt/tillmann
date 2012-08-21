<?php

require_once('custom/include/Dashlets/DashletGenericBarChart.php');

class EmailPerMonth extends DashletGenericBarChart {

    protected $_seedName = 'Employees';

    protected function getDataset() {
        global $db;
        $returnArrayPersonal = array();
        $returnArrayGroup = array();

        //For Group account
        $dashletSql = $this->getEmailPerMonthQueryGroup();
        $dashletData = $db->query($dashletSql);
        while ($row = $db->fetchByAssoc($dashletData)) {
            $index = $row['Month'];
            $returnArrayGroup[$index] = $row['Total'];
        }

        //For Personal account
        $dashletSql = $this->getEmailPerMonthQueryPersonal();
        $dashletData = $db->query($dashletSql);
        while ($row = $db->fetchByAssoc($dashletData)) {
            $index = $row['Month'];
            $returnArrayPersonal[$index] = $row['Total'];
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
        foreach ($returnArrayGroup as $key => $value) {
            $returnArray[$mappingMonths[$key]] = $value + $returnArrayPersonal[$key];
        }



        return $returnArray;
    }

    /**
     * To get Query for Group email account
     * @return string $query
     * @author Dhaval Darji
     */
    protected function getEmailPerMonthQueryGroup() {
        return "SELECT Month(date_entered) as Month,count(id) as Total FROM emails WHERE year(date_entered)=year(curdate()) group by Month(date_entered)";
        //return "SELECT (case when Month(date_entered)=1 then 'January' when Month(date_entered)=2 then 'February' when Month(date_entered)=3 then 'March' when Month(date_entered)=4 then 'April' when Month(date_entered)=5 then 'May' when Month(date_entered)=6 then 'June' when Month(date_entered)=7 then 'July' when Month(date_entered)=8 then 'August' when Month(date_entered)=9 then 'September' when Month(date_entered)=10 then 'October' when Month(date_entered)=11 then 'November' when Month(date_entered)=12 then 'December' end) as Month,count(id) as Total FROM emails WHERE year(date_entered)=year(curdate()) group by Month(date_entered)";
    }

    /**
     * To get Query for Personal email account
     * @return string $query
     * @author Dhaval Darji
     */
    protected function getEmailPerMonthQueryPersonal() {
        return "SELECT Month(senddate) as Month,count(ie_id) as Total FROM email_cache WHERE year(senddate)=year(curdate()) group by Month(senddate)";
    }

}

?>