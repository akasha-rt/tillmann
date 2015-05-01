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
                    emails_email_addr_rel ON emails_email_addr_rel.email_id = emails.id
                        AND emails_email_addr_rel.deleted = 0
                        AND emails.deleted = 0
                        JOIN
                    email_addresses ON email_addresses.id = emails_email_addr_rel.email_address_id
                        AND email_addresses.deleted = 0
                        JOIN
                    email_addr_bean_rel ON email_addr_bean_rel.email_address_id = email_addresses.id
                        AND email_addr_bean_rel.deleted = 0
                        JOIN
                    contacts ON contacts.id = email_addr_bean_rel.bean_id
                        AND email_addr_bean_rel.bean_module = 'Contacts'
                        AND contacts.lead_source = 'EB15'
                WHERE
                    YEAR(emails.date_sent) = YEAR(CURDATE())
                        AND emails.type = 'inbound'
                GROUP BY MONTH(emails.date_sent)";
    }
    
}

?>