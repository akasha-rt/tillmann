<?php

require_once('custom/include/Dashlets/DashletGenericBarChart.php');

class EmailsFromContactByMonth extends DashletGenericBarChart
{

    protected function getDataset()
    {
        global $db;
        $returnArrayEmailsPerContactByMonth = array();

        $dashletSql = $this->getEmailsFromContactByMonth();
        $dashletData = $db->query($dashletSql);
        while ($row = $db->fetchByAssoc($dashletData)) {
            $index = $row['month'];
            $returnArrayEmailsPerContactByMonth[$index] = $row['email_count'];
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
        foreach ($returnArrayEmailsPerContactByMonth as $key => $value) {
            $returnArray[$mappingMonths[$key]] = $value;
        }


        return $returnArray;
    }

    protected function getEmailsFromContactByMonth()
    {
        return "SELECT 
                    MONTH(emails.date_sent) AS month,
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