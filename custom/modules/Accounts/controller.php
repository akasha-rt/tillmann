<?PHP

require_once('include/MVC/Controller/SugarController.php');

class AccountsController extends SugarController {

    function action_getItemHistoryChart() {
        global $db;
        $id = $_REQUEST['id'];
        $accName = $_REQUEST['accName'];
        require_once "custom/modules/Accounts/chart/Includes/FusionCharts.php";

        $sql = "SELECT MONTH(date_entered) AS res_month,COUNT(id) res_count FROM ((SELECT DISTINCT
                          tasks.id,
                          tasks.date_entered AS date_entered
                        FROM tasks
                          LEFT JOIN contacts contacts
                            ON (tasks.contact_id = contacts.id OR tasks.parent_id = contacts.id)
                              AND contacts.deleted = 0
                          LEFT JOIN accounts tasks_rel
                            ON tasks.parent_id = tasks_rel.id
                              AND tasks_rel.deleted = 0
                          LEFT JOIN accounts_contacts
                            ON (accounts_contacts.contact_id = contacts.id
                                 OR accounts_contacts.account_id = tasks_rel.id)
                              AND accounts_contacts.deleted = 0
                        WHERE ((tasks.parent_id = '{$id}'
                                 OR accounts_contacts.account_id = '{$id}')
                               AND (tasks.status = 'Completed'
                                     OR tasks.status = 'Deferred'))
                            AND tasks.deleted = 0
                            AND (tasks.parent_type = 'Accounts' OR tasks.parent_type = 'Contacts'))
                        UNION ALL 
                        (SELECT DISTINCT
                          meetings.id,
                          meetings.date_entered AS date_entered
                        FROM meetings
                          LEFT JOIN accounts meetings_rel
                            ON meetings.parent_id = meetings_rel.id
                              AND meetings_rel.deleted = 0
                          LEFT JOIN meetings_contacts
                            ON meetings.id = meetings_contacts.meeting_id
                              AND meetings_contacts.deleted = 0
                          LEFT JOIN accounts_contacts
                            ON (accounts_contacts.contact_id = meetings_contacts.contact_id
                                 OR accounts_contacts.account_id = meetings_rel.id)
                              AND accounts_contacts.deleted = 0
                        WHERE ((meetings.parent_id = '{$id}'
                                 OR accounts_contacts.account_id = '{$id}')
                               AND (meetings.status = 'Held'
                                     OR meetings.status = 'Not Held'))
                            AND meetings.deleted = 0
                            AND (meetings.parent_type = 'Accounts' OR meetings.parent_type = 'Contacts')) 
                        UNION ALL 
                        (SELECT DISTINCT
                          calls.id,
                          calls.date_entered AS date_entered
                        FROM calls
                         LEFT JOIN accounts calls_rel
                            ON calls.parent_id = calls_rel.id
                              AND calls_rel.deleted = 0
                          LEFT JOIN calls_contacts
                            ON calls.id = calls_contacts.call_id
                              AND calls_contacts.deleted = 0
                          LEFT JOIN accounts_contacts
                            ON (accounts_contacts.contact_id = calls_contacts.contact_id
                                 OR accounts_contacts.account_id = calls_rel.id)
                              AND accounts_contacts.deleted = 0
                        WHERE ((calls.parent_id = '{$id}'
                                 OR accounts_contacts.account_id = '{$id}')
                               AND (calls.status = 'Held'
                                     OR calls.status = 'Not Held'))
                            AND calls.deleted = 0
                            AND (calls.parent_type = 'Accounts' OR calls.parent_type = 'Contacts')) 
                        UNION ALL 
                        (SELECT DISTINCT
                          notes.id,
                          notes.date_entered AS date_entered
                        FROM notes
                          LEFT JOIN contacts contacts
                            ON (notes.contact_id = contacts.id
                                 OR notes.parent_id = contacts.id)
                              AND contacts.deleted = 0
                         LEFT JOIN accounts notes_rel
                            ON notes.parent_id = notes_rel.id
                              AND notes_rel.deleted = 0
                              AND notes_rel.id = '{$id}'
                          LEFT JOIN accounts_contacts
                            ON (accounts_contacts.contact_id = contacts.id
                                 OR accounts_contacts.account_id = notes_rel.id)
                              AND accounts_contacts.deleted = 0
                        WHERE (notes.parent_id = '{$id}'
                                OR accounts_contacts.account_id = '{$id}')
                            AND notes.deleted = 0                              
                            AND (notes.parent_type = 'Accounts' OR notes.parent_type = 'Contacts')) 
                        UNION ALL 
                        (SELECT
                              emails.id,
                          emails.date_entered AS date_entered
                            FROM emails 
                              LEFT JOIN contacts
                              ON emails.parent_id = contacts.id
                              AND contacts.deleted = 0
                              LEFT JOIN accounts
                              ON emails.parent_id = accounts.id
                              AND accounts.deleted = 0
                              INNER JOIN emails_beans
                                ON emails.id = emails_beans.email_id
                                  AND ((emails_beans.bean_module = 'Accounts' AND emails_beans.bean_id = accounts.id) OR (emails_beans.bean_module = 'Contacts' AND emails_beans.bean_id = contacts.id)) 
                                  AND emails_beans.deleted = 0
                             LEFT JOIN emails_text
                                ON emails_text.email_id = emails_beans.email_id
                                    AND emails_text.deleted = 0
                            WHERE emails.deleted = 0
                            AND (accounts.id = '{$id}'
                            OR contacts.id IN (SELECT
                                                           contacts.id
                                                         FROM accounts
                                                           LEFT JOIN accounts_contacts
                                                             ON accounts_contacts.account_id = accounts.id
                                                             AND accounts_contacts.deleted = 0
                                                           LEFT JOIN contacts
                                                             ON contacts.id = accounts_contacts.contact_id
                                                         WHERE contacts.deleted = 0
                                                             AND accounts.deleted = 0
                                                             AND accounts.id = '{$id}'))) 
                            UNION ALL 
                            (SELECT
                              emails.id,
                          emails.date_entered AS date_entered
                            FROM emails
                              JOIN (SELECT DISTINCT
                                      email_id
                                    FROM emails_email_addr_rel eear
                                      JOIN email_addr_bean_rel eabr
                                        ON ((eabr.bean_module = 'Accounts' AND eabr.bean_id = '{$id}') OR (eabr.bean_module = 'Contacts' AND eabr.bean_id IN (SELECT
                                                           contacts.id
                                                         FROM accounts
                                                           LEFT JOIN accounts_contacts
                                                             ON accounts_contacts.account_id = accounts.id
                                                             AND accounts_contacts.deleted = 0
                                                           LEFT JOIN contacts
                                                             ON contacts.id = accounts_contacts.contact_id
                                                         WHERE contacts.deleted = 0
                                                             AND accounts.deleted = 0
                                                             AND accounts.id = '{$id}')))

                                          AND eabr.email_address_id = eear.email_address_id
                                          AND eabr.deleted = 0
                                    WHERE eear.deleted = 0
                            ) derivedemails
                                ON derivedemails.email_id = emails.id
                        LEFT JOIN emails_text
                            ON emails_text.email_id = emails.id
                                AND emails_text.deleted = 0
                            WHERE emails.deleted = 0)) history WHERE YEAR(date_entered)=YEAR(CURDATE()) GROUP BY MONTH(date_entered) ORDER BY date_entered";
        $result = $db->query($sql);


        echo'<script type="text/javascript">
            function closeItemHistoryChart(){                    
                $("#historydetail_div").fadeOut("slow");
		$("#backgroundpopup").fadeOut("slow");
                $("#historydetail_div").remove();
                $("#backgroundpopup").remove();
                $("#historydetail").remove();
                
            } 
            </script>';

        echo '<div id="historydetail" style="width: 600px; box-shadow: 0 2px 10px #666;border-radius: 6px; position: absolute; -webkit-border-radius: 6px;">
            <table width="100%" border="0" cellpadding="1" cellspacing="0" class="olBgClass">';
        echo '<tbody><tr><td>
            <table width="100%" border="0" cellpadding="2" cellspacing="0" class="olCgClass">
            <tbody><tr><td width="100%" class="olCgClass"><div class="olCapFontClass">';
        echo '<div style="float:left">History Item count / Month - ' . $accName . '</div>
            <a href="#" title="Close" onclick="javascript:closeItemHistoryChart();">
            <span style="color:#eeeeff;font-family:Verdana,Arial,Helvetica;font-size:67%;text-decoration:underline;">
            <div style="float: right"><img border="0" style="margin-left:2px; margin-right: 2px;" src="themes/Sugar5/images/close.gif"></div></span></a></div></td></tr>
            </tbody></table>';
        echo'<table width="100%" border="0" cellpadding="2" cellspacing="0" class="olFgClass"><tbody>';
        echo '<tr>';
        echo '<td valign="top">';
        echo '<fieldset><legend><strong>Account History Item Detail</strong></legend>';
        $status = array();
        $i = 1;
        while ($row = $db->fetchByAssoc($result)) {
            $status[$i]['name'] = $row['res_month'];
            $status[$i]['value'] = $row['res_count'];
            $i++;
        }
        $strXML = "<graph xAxisName='Month' yAxisName='Item' decimalPrecision='0' formatNumberScale='0' showValues='0' numberSuffix='' yaxismaxvalue='100' >";
        $color = array('AFD8F8', 'F6BD0F', '8BBA00', 'FF8E46', '008E8E', '8E468E', '588526', '20B295', '226809', 'SCCCCC', '823A3A', 'FF0000');
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
        $data = array();
        $i = 1;
        foreach ($mappingMonths as $key => $val) {
            $data[$i]['month'] = $val;
            foreach ($status as $formskey => $forms) {
                if ($forms['name'] == $key) {
                    $data[$i]['value'] = $forms['value'];
                }
            }
            $i++;
        }
        foreach ($data as $key => $val) {
            $strXML .= "<set name='" . $val['month'] . "' value='" . $val['value'] . "' color='" . $color[$key] . "' hoverText='Item'/>";
        }
        $strXML .= "</graph>";
        echo renderChartHTML("custom/modules/Accounts/chart/FusionCharts/FCF_Column2D.swf", "", $strXML, "myNext", 500, 300);
        echo '</fieldset>';
        echo '</td>';
        echo '</tr>';
        echo '<tbody></table></td></tr></tbody></table></div>';
        exit;
    }

}

?>