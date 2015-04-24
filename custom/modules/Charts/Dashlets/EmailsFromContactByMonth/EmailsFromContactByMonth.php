<?php

require_once('custom/include/Dashlets/DashletGenericBarChart.php');

class EmailsFromContactByMonth extends DashletGenericBarChart {

    protected function getDataset() {
        global $db;
        $returnArrayEmailsPerContactByMonth = array();

        $dashletSql = $this->getEmailsFromContactByMonth();
        $dashletData = $db->query($dashletSql);
        while ($row = $db->fetchByAssoc($dashletData)) {
            $index = $row['month'];
            $returnArrayEmailsPerContactByMonth[$index] = $row['email_count'];
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
            if ($key != $curr_month || $curr_month == 'Jan') {
                if (array_key_exists($key, $returnArrayEmailsPerContactByMonth)) {
                    $returnArray[$mappingMonths[$key]] = $returnArrayEmailsPerContactByMonth[$key];
                } else {
                    $returnArray[$mappingMonths[$key]] = 0;
                }
            }else{
                break;
            }
        }


        return $returnArray;
    }

    protected function getEmailsFromContactByMonth() {
        return "SELECT 
                    DATE_FORMAT(emails.date_sent, '%b') AS month,
                    COUNT(emails.id) AS email_count
                FROM
                    emails
                        JOIN
                    contacts ON contacts.id = emails.parent_id
                        AND emails.parent_type = 'Contacts'
                        AND emails.deleted = 0
                        AND contacts.deleted = 0
                        AND emails.type = 'out'
                        AND emails.status = 'sent'
                        AND contacts.lead_source = 'EB15'
                WHERE
                    YEAR(emails.date_sent) = YEAR(CURDATE())
                GROUP BY MONTH(emails.date_sent)";
    }

}

?>