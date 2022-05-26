<?php

/**
 *
 * @package Advanced OpenPortal
 * @copyright SalesAgility Ltd http://www.salesagility.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with this program; if not, see http://www.gnu.org/licenses
 * or write to the Free Software Foundation,Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA 02110-1301  USA
 *
 * @author Salesagility Ltd <support@salesagility.com>
 */
$job_strings[] = 'pollMonitoredInboxesCustomAOP';

// Add cron from old bb crm.
//Add the new job type to the Option in the job dropdown in scheduler
$job_strings[] = 'createOppFromCase';
$job_strings[] = 'checkOpportunitySalesData';
$job_strings[] = 'processOverDueCase';
$job_strings[] = 'processPOAndVATCases';
$job_strings[] = 'updateCaseStatusOnModification';
$job_strings[] = 'updateCustomerFromMagento';
$job_strings[] = 'sendMonthlyWorkLog';
$job_strings[] = 'sendDailyCaseOverDueTaskEmail';
$job_strings[] = 'processUploadImportPermitCase';
$job_strings[] = 'updateStoreDataDropDowns';
$job_strings[] = 'updateLastShipDateFromOrdersInMagento';
$job_strings[] = 'sendCustomerFirstFollowUp';
$job_strings[] = 'sendCustomerSecondFollowUp';
$job_strings[] = 'sendCustomerSecondFollowUpMonthly';
$job_strings[] = 'campaignForDiscountsForData';
$job_strings[] = 'campaignForAutomaticEnquiry';
$job_strings[] = 'updateProductComplaintsData';
//

$GLOBALS['log']->info('Custom add jobs here loaded');

function getDistributionMethod($ieX) {
    global $sugar_config;

    $method = $sugar_config['aop']['distribution_method'];
    //Check if there is a portal setting for the distribution method.
    if ($method) {
        return $method;
    } else {
        return $ieX->get_stored_options("distrib_method", "");
    }
}

function pollMonitoredInboxesCustomAOP() {

    $_bck_up = array('team_id' => $GLOBALS['current_user']->team_id, 'team_set_id' => $GLOBALS['current_user']->team_set_id);
    $GLOBALS['log']->info('----->Scheduler fired job of type pollMonitoredInboxesCustomAOP()');
    global $dictionary;
    global $app_strings;
    global $sugar_config;

    require_once('modules/Configurator/Configurator.php');
    require_once('modules/Emails/EmailUI.php');

    $ie = new InboundEmail();
    $emailUI = new EmailUI();
    $r = $ie->db->query('SELECT id, name FROM inbound_email WHERE is_personal = 0 AND deleted=0 AND status=\'Active\' AND mailbox_type != \'bounce\'');
    $GLOBALS['log']->debug('Just got Result from get all Inbounds of Inbound Emails');

    while ($a = $ie->db->fetchByAssoc($r)) {
        $GLOBALS['log']->debug('In while loop of Inbound Emails');
        $ieX = new InboundEmail();
        $ieX->retrieve($a['id']);
        $GLOBALS['current_user']->team_id = $ieX->team_id;
        $GLOBALS['current_user']->team_set_id = $ieX->team_set_id;
        $mailboxes = $ieX->mailboxarray;
        foreach ($mailboxes as $mbox) {
            $ieX->mailbox = $mbox;
            $newMsgs = array();
            $msgNoToUIDL = array();
            $connectToMailServer = false;
            if ($ieX->isPop3Protocol()) {
                $msgNoToUIDL = $ieX->getPop3NewMessagesToDownloadForCron();
                // get all the keys which are msgnos;
                $newMsgs = array_keys($msgNoToUIDL);
            }
            if ($ieX->connectMailserver() == 'true') {
                $connectToMailServer = true;
            } // if

            $GLOBALS['log']->debug('Trying to connect to mailserver for [ ' . $a['name'] . ' ]');
            if ($connectToMailServer) {
                $GLOBALS['log']->debug('Connected to mailserver');
                if (!$ieX->isPop3Protocol()) {
                    $newMsgs = $ieX->getNewMessageIds();
                }
                if (is_array($newMsgs)) {
                    $current = 1;
                    $total = count($newMsgs);
                    require_once("include/SugarFolders/SugarFolders.php");
                    $sugarFolder = new SugarFolder();
                    $groupFolderId = $ieX->groupfolder_id;
                    $isGroupFolderExists = false;
                    $users = array();
                    if ($groupFolderId != null && $groupFolderId != "") {
                        $sugarFolder->retrieve($groupFolderId);
                        $isGroupFolderExists = true;
                    } // if
                    $messagesToDelete = array();
                    if ($ieX->isMailBoxTypeCreateCase()) {
                        $users[] = $sugarFolder->assign_to_id;
                        $distributionMethod = getDistributionMethod($ieX);
                        if ($distributionMethod == 'singleUser') {
                            $distributionUserId = $sugar_config['aop']['distribution_user_id'];
                        } elseif ($distributionMethod != 'roundRobin') {
                            $counts = $emailUI->getAssignedEmailsCountForUsers($users);
                        } else {
                            $lastRobin = $emailUI->getLastRobin($ieX);
                        }
                        $GLOBALS['log']->debug('distribution method id [ ' . $distributionMethod . ' ]');
                    }
                    foreach ($newMsgs as $k => $msgNo) {
                        $uid = $msgNo;
                        if ($ieX->isPop3Protocol()) {
                            $uid = $msgNoToUIDL[$msgNo];
                        } else {
                            $uid = imap_uid($ieX->conn, $msgNo);
                        } // else
                        if ($isGroupFolderExists) {
                            if ($ieX->importOneEmail($msgNo, $uid)) {
                                // add to folder
                                $sugarFolder->addBean($ieX->email);
                                if ($ieX->isPop3Protocol()) {
                                    $messagesToDelete[] = $msgNo;
                                } else {
                                    $messagesToDelete[] = $uid;
                                }
                                if ($ieX->isMailBoxTypeCreateCase()) {
                                    $userId = "";
                                    if ($distributionMethod == 'singleUser') {
                                        $userId = $distributionUserId;
                                    } elseif ($distributionMethod == 'roundRobin') {
                                        if (sizeof($users) == 1) {
                                            $userId = $users[0];
                                            $lastRobin = $users[0];
                                        } else {
                                            $userIdsKeys = array_flip($users); // now keys are values
                                            $thisRobinKey = $userIdsKeys[$lastRobin] + 1;
                                            if (!empty($users[$thisRobinKey])) {
                                                $userId = $users[$thisRobinKey];
                                                $lastRobin = $users[$thisRobinKey];
                                            } else {
                                                $userId = $users[0];
                                                $lastRobin = $users[0];
                                            }
                                        } // else
                                    } else {
                                        if (sizeof($users) == 1) {
                                            foreach ($users as $k => $value) {
                                                $userId = $value;
                                            } // foreach
                                        } else {
                                            asort($counts); // lowest to highest
                                            $countsKeys = array_flip($counts); // keys now the 'count of items'
                                            $leastBusy = array_shift($countsKeys); // user id of lowest item count
                                            $userId = $leastBusy;
                                            $counts[$leastBusy] = $counts[$leastBusy] + 1;
                                        }
                                    } // else
                                    $GLOBALS['log']->debug('userId [ ' . $userId . ' ]');
                                    $ieX->handleCreateCase($ieX->email, $userId);
                                } // if
                            } // if
                        } else {
                            if ($ieX->isAutoImport()) {
                                $ieX->importOneEmail($msgNo, $uid);
                            } else {
                                /* If the group folder doesn't exist then download only those messages
                                  which has caseid in message */

                                $ieX->getMessagesInEmailCache($msgNo, $uid);
                                $email = new Email();
                                $header = imap_headerinfo($ieX->conn, $msgNo);
                                $email->name = $ieX->handleMimeHeaderDecode($header->subject);
                                $email->from_addr = $ieX->convertImapToSugarEmailAddress($header->from);
                                $email->reply_to_email = $ieX->convertImapToSugarEmailAddress($header->reply_to);
                                if (!empty($email->reply_to_email)) {
                                    $contactAddr = $email->reply_to_email;
                                } else {
                                    $contactAddr = $email->from_addr;
                                }
                                $mailBoxType = $ieX->mailbox_type;
                                $ieX->handleAutoresponse($email, $contactAddr);
                            } // else
                        } // else
                        $GLOBALS['log']->debug('***** On message [ ' . $current . ' of ' . $total . ' ] *****');
                        $current++;
                    } // foreach
                    // update Inbound Account with last robin
                    if ($ieX->isMailBoxTypeCreateCase() && $distributionMethod == 'roundRobin') {
                        $emailUI->setLastRobin($ieX, $lastRobin);
                    } // if
                } // if
                if ($isGroupFolderExists) {
                    $leaveMessagesOnMailServer = $ieX->get_stored_options("leaveMessagesOnMailServer", 0);
                    if (!$leaveMessagesOnMailServer) {
                        if ($ieX->isPop3Protocol()) {
                            $ieX->deleteMessageOnMailServerForPop3(implode(",", $messagesToDelete));
                        } else {
                            $ieX->deleteMessageOnMailServer(implode($app_strings['LBL_EMAIL_DELIMITER'], $messagesToDelete));
                        }
                    }
                }
            } else {
                $GLOBALS['log']->fatal("SCHEDULERS: could not get an IMAP connection resource for ID [ {$a['id']} ]. Skipping mailbox [ {$a['name']} ].");
                // cn: bug 9171 - continue while
            } // else
        } // foreach
        imap_expunge($ieX->conn);
        imap_close($ieX->conn, CL_EXPUNGE);
    } // while
    $GLOBALS['current_user']->team_id = $_bck_up['team_id'];
    $GLOBALS['current_user']->team_set_id = $_bck_up['team_set_id'];
    return true;
}

//Function to call when the new job is called from cronjob
function createOppFromCase() {
    $GLOBALS['log']->debug('Custom Scheduler : Starting createOppFromCase');
    require_once('modules/Opportunities/Opportunity.php');
    $op = new Opportunity();

    global $db, $sugar_config;
    $case_list = $db->query("SELECT
                                c.id,
                                c.name,
                                c.description,
                                c.assigned_user_id,
                                c.case_number,
                                c_c.contact_id
                              FROM cases c
                                INNER JOIN contacts_cases c_c
                                  ON c.id = c_c.case_id
                                    AND c_c.deleted = 0
                                INNER JOIN cases_audit c_adt
                                  ON c_adt.parent_id = c.id
                                    AND c_adt.before_value_string != 'PO'
                                    AND c_adt.after_value_string != 'PO'
                                    AND c_adt.field_name = 'status'
                                    AND c_adt.after_value_string = 'Closed'
                              WHERE DATE_ADD(c_adt.date_created,INTERVAL 7 DAY) <= NOW()
                                  AND c.status = 'Closed'
                                  AND c.convertedtoopp = '0'
                                  AND c.deleted = 0
                                  AND c_c.deleted = 0
                              GROUP BY c.id");
    while ($case = $db->fetchByAssoc($case_list)) {
        //$op->id = create_guid();  //if id is set save() will update the record if not then will insert new row
        $op->name = $case['name'];
        $op->description = $case['description'];
        $op->assigned_user_id = $case['assigned_user_id'];
        $op->deleted = 0;
        $op->date_entered = date('%Y-%m-%d H:i:s');
        $op->date_modified = date('%Y-%m-%d H:i:s');
        $op->save();

        $op->Updateconvertedtoopp($case['id']);
        $op->set_opportunity_contact_relationship($case['contact_id']);
    }

    //Return true to notify the successfull execution of the job
    return true;
}

function getWorkingDays($startDate,$endDate){
    // do strtotime calculations just once
    $endDate = strtotime($endDate);
    $startDate = strtotime($startDate);

    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
    //We add one to inlude both dates in the interval.
    $days = ($endDate - $startDate) / 86400;

    $no_full_weeks = floor($days / 7);
    $no_remaining_days = fmod($days, 7);

    //It will return 1 if it's Monday,.. ,7 for Sunday
    $the_first_day_of_week = date("N", $startDate);
    $the_last_day_of_week = date("N", $endDate);

    //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
    //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
    if ($the_first_day_of_week <= $the_last_day_of_week) {
        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
    }
    else {
        // (edit by Tokes to fix an edge case where the start day was a Sunday
        // and the end day was NOT a Saturday)

        // the day of the week for start is later than the day of the week for end
        if ($the_first_day_of_week == 7) {
            // if the start date is a Sunday, then we definitely subtract 1 day
            $no_remaining_days--;

            if ($the_last_day_of_week == 6) {
                // if the end date is a Saturday, then we subtract another day
                $no_remaining_days--;
            }
        }
        else {
            // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
            // so we skip an entire weekend and subtract 2 days
            $no_remaining_days -= 2;
        }
    }

    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
   $workingDays = $no_full_weeks * 5;
    if ($no_remaining_days > 0 )
    {
      $workingDays += $no_remaining_days;
    }

    return $workingDays;
}

function resetEmailSentCount() {
    global $db;
    $db->query("UPDATE contacts SET email_sent_count = 0");
}

function getOptOutStatus($con_id) {
    global $db;
    $query = $db->query("SELECT 
    ea.opt_out from email_addresses ea
    inner join email_addr_bean_rel eabr
    on eabr.email_address_id = ea.id
    inner join contacts
    on contacts.id = eabr.bean_id
    where contacts.id = '" . $con_id . "'");
    return $db->fetchByAssoc($query)['opt_out'];
}

function checkOpportunitySalesData() {
    $GLOBALS['log']->debug('Custom Scheduler : Starting checkOpportunitySalesData');
    $dateToday = TimeDate::getInstance()->nowDbDate();
    $date = date_create($dateToday);
    if(date_format($date,"m") == '01' && date_format($date,"d") == '01') {
        resetEmailSentCount();
    }

    require_once('modules/Emails/Email.php');
    require_once('modules/Opportunities/Opportunity.php');
    global $db, $sugar_config;
    $conDataSql = $db->query("SELECT
                                    LTRIM(RTRIM(jt0.first_name)) AS assigned_user_name,
                                    LTRIM(RTRIM(contacts.first_name)) AS contact_name,
                                    contacts.id                 AS conid,
                                    contact_email.email_address AS email_address
                                  FROM contacts contacts
                                    LEFT JOIN users jt0
                                      ON contacts.assigned_user_id = jt0.id
                                        AND jt0.deleted = 0
                                    INNER JOIN contacts_cstm
                                      ON contacts.id = contacts_cstm.id_c
                                    LEFT OUTER JOIN (SELECT
                                                       contacts.id                  AS con_id,
                                                       email_address
                                                     FROM contacts
                                                       LEFT JOIN email_addr_bean_rel
                                                         ON email_addr_bean_rel.bean_id = contacts.id
                                                       LEFT JOIN email_addresses
                                                         ON email_addresses.id = email_addr_bean_rel.email_address_id
                                                     WHERE email_addr_bean_rel.deleted = 0
                                                         AND email_addresses.deleted = 0
                                                         AND contacts.deleted = 0) contact_email
                                      ON contact_email.con_id = contacts.id
                                  WHERE 
                                        ABS(DATEDIFF(NOW(), contacts.date_modified))
                                        - ABS(DATEDIFF(ADDDATE(NOW(), INTERVAL 1 - DAYOFWEEK(NOW()) DAY),
                                        ADDDATE(contacts.date_modified, INTERVAL 1 - DAYOFWEEK(contacts.date_modified) DAY))) / 7 * 2
                                        - (DAYOFWEEK(IF(contacts.date_modified < NOW(), contacts.date_modified, NOW())) = 1)
                                        - (DAYOFWEEK(IF(contacts.date_modified > NOW(), contacts.date_modified, NOW())) = 7) = 7
                                  
                                      AND contacts_cstm.type_c = 'Enquiry'
									");
    $contactData = array();
    while ($row = $db->fetchByAssoc($conDataSql)) {
        $contactData[] = $row;
    }
    //SOAP CALL END
    //SEND EMAIL START
    foreach ($contactData as $conData) {
        $name = $conData['contact_name'];
        $assigned_user_name = $conData['assigned_user_name'];
        $email_address = $conData['email_address'];
        $con_id = $conData['conid'];
        $isOptOut = getOptOutStatus($con_id);
        $currentCon = new Contact();
        $currentCon->retrieve($con_id);
        if(!$isOptOut && $currentCon->email_sent_count < 3) {
            //$email_address = 'dhaval@india.biztechconsultancy.com';
            $emailtemplate = new EmailTemplate();
            $emailtemplate->retrieve($sugar_config['opp_no_order_placed']);


            //Load the signature
            require_once 'modules/Users/UserSignature.php';
            $signature = new UserSignature();
            $signature->retrieve_by_string_fields(array("user_id" => '1'));

            $email_body = $emailtemplate->body_html;
            $email_body_plain = $emailtemplate->body;
            $email_body = str_replace('$contact_first_name', $name, $email_body);
            $email_body = str_replace('$contact_user_first_name', $assigned_user_name, $email_body);
            //add the signature
            $email_body = str_replace('[signature]', $signature->signature_html, $email_body);
            $mailSubject = $emailtemplate->subject;

            $emailObj = new Email();
            $defaults = $emailObj->getSystemDefaultEmail();
            $mail = new SugarPHPMailer();
            $mail->setMailerForSystem();
            $mail->ClearAllRecipients();
            $mail->ClearReplyTos();
            $mail->From = $defaults['email'];
            $mail->FromName = $defaults['name'];
            $subject = $mailSubject;
            $mail->Subject = $subject;
            $mail->Body = from_html($email_body);
            $mail->AltBody = $email_body_plain;
            $mail->prepForOutbound();
            $address = $email_address;
            $mail->AddAddress($email_address);
            $sendEmail = false;
            if($currentCon->is_email_sent == 0) {
                $sendEmail = true;
            } else {
                if (getWorkingDays(str_replace('/', '-', $currentCon->email_sent_date), date("Y-m-d")) >= 7) {
                    $sendEmail = true;
                }
            }
            $GLOBALS['log']->fatal('$sendEmail', $sendEmail);

            if ($sendEmail && !empty($email_address) && $mail->Send()) {
                $emailObj->to_addrs = $address;
                $emailObj->type = 'out';
                $emailObj->deleted = '0';
                $emailObj->name = $subject;
                $emailObj->description = $email_body_plain;
                $emailObj->description_html = from_html($email_body);
                $emailObj->from_addr = $defaults['email'];
                $user_id = '1';
                $emailObj->date_sent = TimeDate::getInstance()->nowDb();
                $emailObj->assigned_user_id = $user_id;
                $emailObj->modified_user_id = $user_id;
                $emailObj->created_by = $user_id;
                $emailObj->status = 'sent';
                $emailObj->save();
                $dateToday = TimeDate::getInstance()->nowDbDate();
                $db->query("UPDATE contacts
                SET is_email_sent = 1,
                email_sent_date ='" .  $dateToday . "',
                email_sent_count= email_sent_count + 1
                WHERE contacts.id = '" . $con_id . "'");
                /* $db->query("UPDATE opportunities_cstm
                    SET is_email_sent_c = 1
                    WHERE opportunities_cstm.id_c = '" . $oppId . "'"); */
            } else {
                $mail_msg = $mail->ErrorInfo;
                echo "error sending " . $mail_msg;
            }
        }
    }
    //Return true to notify the successfull execution of the job
    return true;
}

/**
 * TO process Overdue payment Cases
 * @global type $sugar_config
 * @global type $db
 * @return boolean
 */
function processOverDueCase() {
    $GLOBALS['log']->debug('Custom Scheduler : Starting processOverDueCase');
    global $sugar_config;
    global $db;

    require_once 'modules/Cases/Case.php';
    require_once 'modules/EmailTemplates/EmailTemplate.php';
    require_once 'modules/Emails/Email.php';
    require_once 'include/SugarPHPMailer.php';
    require_once 'modules/Notes/Note.php';

    $sql = "SELECT id,overdue_payment_c,customer_name_c,invoice_no_c,customer_email_c 
                FROM cases LEFT JOIN cases_cstm 
                    ON cases.id = cases_cstm.id_c
            WHERE cases.deleted=0 AND cases_cstm.overdue_payment_c = 'true'";
    $result = $db->query($sql);
    while ($overDueCase = $db->fetchByAssoc($result)) {

        $bean = new aCase();
        $bean->retrieve($overDueCase['id']);
        $bean->overdue_payment_c = $overDueCase['overdue_payment_c'];
        $bean->customer_name_c = $overDueCase['customer_name_c'];
        $bean->invoice_no_c = $overDueCase['invoice_no_c'];
        $bean->customer_email_c = $overDueCase['customer_email_c'];

        //Send email
        $emailtemplate = new EmailTemplate();
        $emailtemplate->retrieve($sugar_config['overdue_pay_invoice']);

        $email_body = $emailtemplate->body_html;
        //for plain text supported email client
        $email_body_plain = $emailtemplate->body;
        $email_body = str_replace('$customer_name_c', $bean->customer_name_c, $email_body);
        $email_body = str_replace('$invoice_no_c', $bean->invoice_no_c, $email_body);
        $email_body = str_replace('$invoice_no_body_c', $bean->invoice_no_body_c, $email_body);
        $email_body = str_replace('$case_No', $bean->case_number, $email_body);

        //Correct the subject
        $mailSubject = $emailtemplate->subject;
        $mailSubject = str_replace('$invoice_no_c', $bean->invoice_no_c, $mailSubject);

        //$bean->customer_email_c = 'dhaval@india.biztechconsultancy.com';
        $email_address = $bean->customer_email_c;

        $emailObj = new Email();
        $defaults = $emailObj->getSystemDefaultEmail();
        $mail = new SugarPHPMailer();
        $mail->setMailerForSystem();
        $mail->ClearAllRecipients();
        $mail->ClearReplyTos();
        $mail->From = $defaults['email'];
        $mail->FromName = $defaults['name'];
        $subject = $mailSubject;
        $mail->Subject = $subject;
        $mail->Body = from_html($email_body);
        $mail->AltBody = $email_body_plain; //Sets the text-only body of the message.
        //For attachment
        $bean->load_relationship('notes');
        foreach ($bean->notes->getBeans(new Note()) as $note) {
            $noteId = $note->id;
        }
        $filename_attach = 'Invoice-' . $bean->invoice_no_c . '_' . date('Ymd') . '.pdf';
        $file_location = $sugar_config['upload_dir'] . $noteId;
        $mime_type = 'application/pdf';
        $mail->AddAttachment($file_location, $filename_attach, 'base64', $mime_type); //Attach each file to message
        //End - attachment
        $mail->prepForOutbound();
        $address = $email_address;
        $mail->AddAddress($email_address);

        if (!empty($email_address) && $mail->Send()) {
            $emailObj->to_addrs = $address;
            $emailObj->type = 'out';
            $emailObj->deleted = '0';
            $emailObj->name = $subject;
            $emailObj->description = $email_body_plain; //Sets the text-only body of the message.
            $emailObj->description_html = from_html($email_body);
            $emailObj->from_addr = $defaults['email'];
            $emailObj->parent_type = 'Cases';
            $emailObj->parent_id = $bean->id;
            $emailObj->attachments = $mail->attachment;
            $user_id = 'c01295a1-6e11-1c36-099b-4fe99aef1381';
            $emailObj->date_sent = TimeDate::getInstance()->nowDb();
            $emailObj->assigned_user_id = $user_id;
            $emailObj->modified_user_id = $user_id;
            $emailObj->created_by = $user_id;
            $emailObj->status = 'sent';
            $emailObj->save();
            //Save case and reset flag
            $bean->overdue_payment_c = '';
            $bean->save();
        } else {
            $mail_msg = $mail->ErrorInfo;
        }
    }
    return true;
}

function processPOAndVATCases() {
    $GLOBALS['log']->debug('Custom Scheduler : Starting processPOAndVATCases');
    global $db, $sugar_config;

    require_once 'modules/Cases/Case.php';
    require_once 'modules/EmailTemplates/EmailTemplate.php';
    require_once 'modules/Emails/Email.php';
    require_once 'include/SugarPHPMailer.php';
    require_once 'modules/Notes/Note.php';

    $sql = "SELECT
            cases.id                    AS id,
            cases_cstm.customer_name_c  AS customer_name,
            cases_cstm.customer_email_c AS customer_email,
            cases_cstm.po_number_c      AS po_number,
            cases_cstm.vat_number_c     AS vat_number,
            cases_cstm.order_number_c   AS order_number,
            cases.case_number           AS case_number
          FROM cases cases
            LEFT JOIN cases_cstm cases_cstm
              ON cases.id = cases_cstm.id_c
          WHERE cases.deleted = 0
              AND ((cases_cstm.po_number_c != ''
                    AND cases_cstm.po_number_c IS NOT NULL)
                    OR (cases_cstm.vat_number_c != ''
                        AND cases_cstm.vat_number_c IS NOT NULL))";
    $result = $db->query($sql);
    while ($PO_VAT_Case = $db->fetchByAssoc($result)) {
        $bean = new aCase();
        $bean->retrieve($PO_VAT_Case['id']);
        $bean->customer_name_c = $PO_VAT_Case['customer_name'];
        $bean->customer_email_c = $PO_VAT_Case['customer_email'];
        $bean->po_number_c = $PO_VAT_Case['po_number'];
        $bean->vat_number_c = $PO_VAT_Case['vat_number'];
        $bean->order_number_c = $PO_VAT_Case['order_number'];
        $bean->case_number = $PO_VAT_Case['case_number'];

        //Send email
        $emailtemplate = new EmailTemplate();
        if (!is_null($PO_VAT_Case['po_number']) && $PO_VAT_Case['po_number'] != "") {
            $emailtemplate->retrieve($sugar_config['po_reminder_order']);
            $email_body = $emailtemplate->body_html;
            $email_body_plain = $emailtemplate->body;
            $email_body = str_replace('$customerName', $bean->customer_name_c, $email_body);
            $email_body = str_replace('$po_number', $bean->po_number_c, $email_body);
            $email_body = str_replace('$order_number', $bean->order_number_c, $email_body);
            $email_body = str_replace('$case_No', $bean->case_number, $email_body);
            $mailSubject = $emailtemplate->subject;
            $mailSubject = str_replace('$order_number', $bean->order_number_c, $mailSubject);
        } elseif (!is_null($PO_VAT_Case['vat_number']) && $PO_VAT_Case['vat_number'] != "") {
            $emailtemplate->retrieve($sugar_config['zero_vat_cert_reminder']);
            $email_body = $emailtemplate->body_html;
            $email_body_plain = $emailtemplate->body;
            $email_body = str_replace('$customerName', $bean->customer_name_c, $email_body);
            $email_body = str_replace('$order_number', $bean->order_number_c, $email_body);
            $email_body = str_replace('$case_No', $bean->case_number, $email_body);
            $mailSubject = $emailtemplate->subject;
            $mailSubject = str_replace('$order_number', $bean->order_number_c, $mailSubject);
        } else {
            continue;
        }

        //$bean->customer_email_c = 'dhaval@india.biztechconsultancy.com';
        $email_address = $bean->customer_email_c;

        $emailObj = new Email();
        $defaults = $emailObj->getSystemDefaultEmail();
        $mail = new SugarPHPMailer();
        $mail->setMailerForSystem();
        $mail->ClearAllRecipients();
        $mail->ClearReplyTos();
        $mail->From = $defaults['email'];
        $mail->FromName = $defaults['name'];
        $subject = $mailSubject;
        $mail->Subject = $subject;
        $mail->Body = from_html($email_body);
        $mail->AltBody = $email_body_plain;

        $mail->prepForOutbound();
        $address = $email_address;
        $mail->AddAddress($email_address);

        if (!empty($email_address) && $mail->Send()) {
            $emailObj->to_addrs = $address;
            $emailObj->type = 'out';
            $emailObj->deleted = '0';
            $emailObj->name = $subject;
            $emailObj->description = $email_body_plain;
            $emailObj->description_html = from_html($email_body);
            $emailObj->from_addr = $defaults['email'];
            $emailObj->parent_type = 'Cases';
            $emailObj->parent_id = $bean->id;
            $user_id = 'c01295a1-6e11-1c36-099b-4fe99aef1381';
            $emailObj->date_sent = TimeDate::getInstance()->nowDb();
            $emailObj->assigned_user_id = $user_id;
            $emailObj->modified_user_id = $user_id;
            $emailObj->created_by = $user_id;
            $emailObj->status = 'sent';
            $emailObj->save();
            $bean->po_number_c = '';
            $bean->vat_number_c = '';
            $bean->save();
        } else {
            $mail_msg = $mail->ErrorInfo;
        }
    }
    return true;
}

function updateCaseStatusOnModification() {
    global $db, $sugar_config;
    $query = "SELECT id
              FROM cases
              WHERE 5 * ( DATEDIFF( DATE( NOW( ) ) , date_modified ) 
              DIV 7 ) + MID(  '0123444401233334012222340111123400012345001234550', 7 * WEEKDAY( date_modified ) + 
              WEEKDAY( NOW( ) ) +1, 1 )  > 2 AND status IN('Pending Input','pending_customer','pending_supplier') AND deleted = 0";
    $result = $db->query($query);
    $case = new aCase();
    while ($row_case = $db->fetchByAssoc($result)) {
        $case->retrieve($row_case['id']);
        $case->status = "Open";
        $case->save();
    }
    return true;
}

function updateCustomerFromMagento() {
    global $db, $sugar_config;
    //retrive last customer created/updated dates from sugar config
    $configQuery = 'SELECT
                        NAME,
                        VALUE
                      FROM config
                      WHERE category = "MagentoData"
                          AND NAME IN("customerCreatedDate","lastShipmentDate")';
    $configResult = $db->query($configQuery);
    while ($configData = $db->fetchByAssoc($configResult)) {
        if ($configData['NAME'] == 'customerCreatedDate') {
            $created_date = $configData['NAME'];
            $customerCreatedDate = $configData['VALUE'];
        } else {
            $lastship_date = $configData['NAME'];
            $lastShipmentDate = $configData['VALUE'];
        }
    }
    $limitRecords = '';
    $firstTime = false;
    if (isset($created_date) && isset($lastship_date)) {
        $limitRecords .= " WHERE (created_at > '{$customerCreatedDate}' OR t.date > '{$lastShipmentDate}') ";
        $maxCreatedDate = $customerCreatedDate;
        $maxShipDate = $lastShipmentDate;
    } else {
        $firstTime = true;
        $maxCreatedDate = '1970-01-01 00:00:00';
        $maxShipDate = '1970-01-01 00:00:00';
    }
    //Load contact email addresses from sugar
    $sugar_email_query = "SELECT
                    contacts.id                   AS contact_id,
                    email_addresses.email_address AS email
                  FROM contacts
                    LEFT JOIN email_addr_bean_rel
                      ON contacts.id = email_addr_bean_rel.bean_id
                    LEFT JOIN email_addresses
                      ON email_addresses.id = email_addr_bean_rel.email_address_id
                  WHERE contacts.deleted = 0
                      AND email_addr_bean_rel.deleted = 0
                      AND email_addresses.deleted = 0
                      AND email_addr_bean_rel.primary_address = 1";
    $sql_email_result = $db->query($sugar_email_query);
    $sugar_email_list = array();
    while ($sugar_email_data = $db->fetchByAssoc($sql_email_result)) {
        $sugar_email_list[$sugar_email_data['email']] = $sugar_email_data['contact_id'];
    }
    //End - contact email loading
    mysql_connect($sugar_config['magento_config']['host'], $sugar_config['magento_config']['db_user'], $sugar_config['magento_config']['db_password']);
    mysql_select_db($sugar_config['magento_config']['database']);

    $query = "SELECT
                    customer_entity.entity_id,
                    customer_entity.created_at,
                    customer_entity.email,
                    cev1.value AS first_name,
                    cev2.value AS last_name,
                    ceav1.value AS phone,
                    t.date AS last_ship_date,
                    caet1.value AS street,
                    ceav3.value AS city,
                    ceav4.value AS state,
                    ceav5.value AS postal,
                    ceav2.value AS country_id
                  FROM customer_entity
                    LEFT JOIN customer_entity_varchar cev1
                      ON cev1.entity_id = customer_entity.entity_id
                        AND cev1.attribute_id = 5

                    LEFT JOIN customer_entity_varchar cev2
                      ON cev2.entity_id = customer_entity.entity_id
                        AND cev2.attribute_id = 7

                    LEFT JOIN customer_entity_int
                      ON customer_entity.entity_id = customer_entity_int.entity_id
                    AND customer_entity_int.attribute_id = 14

                    LEFT JOIN
                    (
                    SELECT MIN(entity_id) AS entity_id,
                    parent_id
                    FROM
                    customer_address_entity
                    GROUP BY parent_id
                    ) AS a ON a.parent_id = customer_entity.entity_id

                    LEFT JOIN customer_address_entity_varchar ceav1
                    ON ceav1.entity_id = IF(customer_entity_int.value IS NULL,a.entity_id,customer_entity_int.value)

                        AND ceav1.attribute_id = 29

                    LEFT JOIN customer_address_entity_text caet1
                    ON caet1.entity_id = IF(customer_entity_int.value IS NULL,a.entity_id,customer_entity_int.value)

                    AND caet1.attribute_id = 23

                    LEFT JOIN customer_address_entity_varchar ceav3
                    ON ceav3.entity_id = IF(customer_entity_int.value IS NULL,a.entity_id,customer_entity_int.value)

                    AND ceav3.attribute_id = 24

                    LEFT JOIN customer_address_entity_varchar ceav4
                    ON ceav4.entity_id = IF(customer_entity_int.value IS NULL,a.entity_id,customer_entity_int.value)

                    AND ceav4.attribute_id = 26

                    LEFT JOIN customer_address_entity_varchar ceav5
                    ON ceav5.entity_id = IF(customer_entity_int.value IS NULL,a.entity_id,customer_entity_int.value)

                    AND ceav5.attribute_id = 28

                    LEFT JOIN customer_address_entity_varchar ceav2
                    ON ceav2.entity_id = IF(customer_entity_int.value IS NULL,a.entity_id,customer_entity_int.value)


                        AND ceav2.attribute_id = 25


                    LEFT JOIN (SELECT
                                 customer_id,
                    MAX( created_at ) AS DATE
                               FROM sales_flat_shipment
                               WHERE customer_id IS NOT NULL
                               GROUP BY customer_id) AS t
                      ON t.customer_id = customer_entity.entity_id

                      {$limitRecords} 
                    ORDER BY created_at ASC,t.date asc";
    $result = mysql_query($query);
    while ($data = mysql_fetch_array($result)) {
        //existing customer
        if (isset($sugar_email_list[$data['email']])) {
            $contacts = new Contact();
            $contacts->retrieve($sugar_email_list[$data['email']]);
            if ($contacts->type_c == 'Enquiry') {
                $contacts->type_c = 'One_time_customer';
                $contacts->last_shipment_date_c = $data['last_ship_date'];
                $contacts->primary_address_street = $data['street'];
                $contacts->primary_address_city = $data['city'];
                $contacts->primary_address_state = $data['state'];
                $contacts->primary_address_postalcode = $data['postal'];
                $contacts->primary_address_country = $data['country_id'];
                $contacts->save();
            } else if ($contacts->type_c == 'One_time_customer') {
                $contacts->type_c = 'Regular_Customer';
                $contacts->last_shipment_date_c = $data['last_ship_date'];
                $contacts->primary_address_street = $data['street'];
                $contacts->primary_address_city = $data['city'];
                $contacts->primary_address_state = $data['state'];
                $contacts->primary_address_postalcode = $data['postal'];
                $contacts->primary_address_country = $data['country_id'];
                $contacts->save();
            }
        } else {
            //new customer
            $contacts = new Contact();
            $contacts->first_name = $data['first_name'];
            $contacts->last_name = $data['last_name'];
            $contacts->email1 = $data['email'];
            $contacts->phone_mobile = $data['phone'];
            $contacts->primary_address_street = $data['street'];
            $contacts->primary_address_city = $data['city'];
            $contacts->primary_address_state = $data['state'];
            $contacts->primary_address_postalcode = $data['postal'];
            $contacts->primary_address_country = $data['country_id'];
            $contacts->type_c = 'Magento';
            $contacts->last_shipment_date_c = $data['last_ship_date'];
            $contacts->save();
        }

        if ($data['created_at'] > $maxCreatedDate) {
            $maxCreatedDate = $data['created_at'];
        }
        if ($data['last_ship_date'] > $maxShipDate) {
            $maxShipDate = $data['last_ship_date'];
        }
    }
    if ($firstTime) {
        $insertConfig = "Insert into config (category,name,value) Values ('MagentoData', 'customerCreatedDate' ,'{$maxCreatedDate}'),
            ('MagentoData', 'lastShipmentDate' ,'{$maxShipDate}')";
        $db->query($insertConfig);
    } else {
        $updateConfig = "UPDATE config set value = '{$maxCreatedDate}' where name = 'customerCreatedDate'";
        $db->query($updateConfig);
        $updateConfig = "UPDATE config set value = '{$maxShipDate}' where name = 'lastShipmentDate'";
        $db->query($updateConfig);
    }
    return true;
}

function sendMonthlyWorkLog() {
    global $db, $sugar_config;
    $query = "SELECT
                DATE(la_loginaudit.date_entered) AS DATE,
                la_loginaudit.typed_name AS USER,
                login.first_login        AS LoginTime,
                logout.last_logout       AS LogOutTime,
                TIMEDIFF( logout.last_logout, login.first_login ) AS TotalHours,
                MONTHNAME(STR_TO_DATE(MONTH(NOW() - INTERVAL 1 MONTH), '%m')) AS MONTH
              FROM la_loginaudit
                LEFT OUTER JOIN (SELECT
                                   la_loginaudit.typed_name  AS USER,
                                   MIN(la_loginaudit.date_entered) AS first_login
                                 FROM la_loginaudit
                                 WHERE la_loginaudit.deleted = 0
                                     AND la_loginaudit.result = 'Success'
                                     AND MONTH(la_loginaudit.date_entered) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
                                     AND la_loginaudit.typed_name IS NOT NULL
                                     AND la_loginaudit.typed_name <> ''
                                 GROUP BY DATE(la_loginaudit.date_entered), la_loginaudit.typed_name) AS login
                  ON login.user = la_loginaudit.typed_name
                    AND DATE(login.first_login) = DATE(la_loginaudit.date_entered)
                LEFT OUTER JOIN (SELECT
                                   la_loginaudit.typed_name  AS USER,
                                   MAX(la_loginaudit.date_entered) AS last_logout
                                 FROM la_loginaudit
                                 WHERE la_loginaudit.deleted = 0
                                     AND la_loginaudit.result = 'logout'
                                     AND MONTH(la_loginaudit.date_entered) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
                                     AND la_loginaudit.typed_name IS NOT NULL
                                     AND la_loginaudit.typed_name <> ''
                                 GROUP BY DATE(la_loginaudit.date_entered), la_loginaudit.typed_name) AS logout
                  ON logout.user = la_loginaudit.typed_name
                    AND DATE(logout.last_logout) = DATE(la_loginaudit.date_entered)
              WHERE la_loginaudit.deleted = 0
                  AND MONTH(la_loginaudit.date_entered) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
                  AND typed_name != ''
              GROUP BY DATE(la_loginaudit.date_entered), la_loginaudit.typed_name
              ORDER BY DATE asc";
    $result = $db->query($query);
    $finalExportData = array();
    $timeDate = new TimeDate();
    while ($data = $db->fetchByAssoc($result)) {
        $data['DATE'] = $timeDate->to_display_date($data['DATE']);
        $data['LoginTime'] = $timeDate->to_display_time($data['LoginTime']);
        $data['LogOutTime'] = $timeDate->to_display_time($data['LogOutTime']);
        $finalExportData[] = $data;
    }

    function cleanData(&$str) {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        $str = preg_replace("/&nbsp;/", "", $str);
        if (strstr($str, '"'))
            $str = '"' . str_replace('"', '""', $str) . '"';
    }

    $flag = false;
    $exceldata = '';
    foreach ($finalExportData as $row) {
        if (!$flag) {
            // display field/column names as first row
            $exceldata = implode("\t", array_keys($row)) . "\r\n";
            $flag = true;
        }
        array_walk($row, 'cleanData');
        $exceldata .= implode("\t", array_values($row)) . "\r\n";
    }
    $filename = "WorkLog: " . $row['MONTH'] . " Month";
    $File = "MonthlyWorkLog/{$filename}.xls";
    $Handle = fopen($File, 'w+');
    $Data = $exceldata;
    fwrite($Handle, $Data);
    //Send email
    $email_body = "Dear Admin,<br />
        Please find the attached spreadsheet, containing monthly worklog of {$row['MONTH']} month.";
    $mailSubject = "Monthly {$filename}";

    $emailObj = new Email();
    $defaults = $emailObj->getSystemDefaultEmail();
    $mail = new SugarPHPMailer();
    $mail->setMailerForSystem();
    $mail->ClearAllRecipients();
    $mail->ClearReplyTos();
    $mail->From = $defaults['email'];
    $mail->FromName = $defaults['name'];
    $mail->Subject = $mailSubject;
    $mail->Body = from_html($email_body);
    $mail->AltBody = from_html($email_body);

    //For attachment
    $filename_attach = "{$filename}.xls";
    $file_location = $File;
    $mime_type = 'application/vnd.ms-excel';
    $mail->AddAttachment($file_location, $filename_attach, 'base64', $mime_type); //Attach each file to message

    $mail->prepForOutbound();
    foreach ($sugar_config['worklog_emails'] as $email_address) {
        $mail->AddAddress($email_address);
    }
    $mail->Send();
    return true;
}

function sendDailyCaseOverDueTaskEmail() {
    global $db, $sugar_config;
    $gloablUserData = array();
    $openCaseQuery = "SELECT
                    COUNT(cases.id)        AS open_case,
                    cases.assigned_user_id AS User_id
                  FROM cases
                    LEFT JOIN users
                      ON cases.assigned_user_id = users.id
                        AND users.deleted = 0
                  WHERE cases.deleted = 0
                      AND users.deleted = 0
                      AND cases.status = 'Open'
                  GROUP BY User_id";
    $openCaseResult = $db->query($openCaseQuery);
    while ($openCaseData = $db->fetchByAssoc($openCaseResult)) {
        $gloablUserData[$openCaseData['User_id']]['open_case'] = $openCaseData['open_case'];
    }
    $newCaseQuery = "SELECT
                    COUNT(cases.id)        AS new_case,
                    cases.assigned_user_id AS User_id
                  FROM cases
                    LEFT JOIN users
                      ON cases.assigned_user_id = users.id
                        AND users.deleted = 0
                  WHERE cases.deleted = 0
                      AND users.deleted = 0
                      AND cases.status = 'New'
                  GROUP BY User_id";
    $newCaseResult = $db->query($newCaseQuery);
    while ($newCaseData = $db->fetchByAssoc($newCaseResult)) {
        $gloablUserData[$newCaseData['User_id']]['new_case'] = $newCaseData['new_case'];
    }
    $overDueTaskQuery = "SELECT
                    COUNT(tasks.id) AS overdue_task,
                    tasks.assigned_user_id AS User_id
                  FROM tasks
                    LEFT JOIN users
                      ON tasks.assigned_user_id = users.id
                      AND users.deleted = 0
                  WHERE tasks.deleted = 0
                      AND users.deleted = 0
                      AND tasks.date_due < '" . TimeDate::getInstance()->nowDb() . "'
                      AND tasks.status = 'Not Started'
                  GROUP BY User_id";
    $overDueTaskResult = $db->query($overDueTaskQuery);
    while ($overDueTaskData = $db->fetchByAssoc($overDueTaskResult)) {
        $gloablUserData[$overDueTaskData['User_id']]['overdue_task'] = $overDueTaskData['overdue_task'];
    }
    foreach ($gloablUserData as $userID => $dailyDigestData) {
        $user_Obj = new User();
        $user_Obj->retrieve($userID);
        $user_tz = $user_Obj->user_preferences['global']['timezone'];
        if (is_null($user_tz)) {
            $user_gmtoffset = $user_Obj->getUserDateTimePreferences();
            $userTZ = explode(' ', $user_gmtoffset['userGmt']);
            unset($userTZ[count($userTZ) - 1]);
            $user_tz = implode('_', $userTZ);
        }
        date_default_timezone_set($user_tz);

        $UserTime = strtotime(date("h:i A"));
        if ($UserTime >= strtotime('06:00 PM') && $user_Obj->overdue_email_sent_c != TimeDate::getInstance()->nowDate() && $user_Obj->status == 'Active') {
            $emailtemplate = new EmailTemplate();
            $emailtemplate->retrieve($sugar_config['daily_work_digest']);
            $email_body = $emailtemplate->body_html;
            $email_body_plain = $emailtemplate->body;
            $email_body = str_replace('$user_first_name', (empty($user_Obj->first_name)) ? $user_Obj->last_name : $user_Obj->first_name, $email_body);
            $email_body = str_replace('$openCaseCount', (empty($dailyDigestData['open_case'])) ? 0 : $dailyDigestData['open_case'], $email_body);
            $email_body = str_replace('$newCaseCount', (empty($dailyDigestData['new_case'])) ? 0 : $dailyDigestData['new_case'], $email_body);
            $email_body = str_replace('$overdueTaskCount', (empty($dailyDigestData['overdue_task'])) ? 0 : $dailyDigestData['overdue_task'], $email_body);
            $mailSubject = $emailtemplate->subject;
            $emailObj = new Email();
            $defaults = $emailObj->getSystemDefaultEmail();
            $mail = new SugarPHPMailer();
            $mail->setMailerForSystem();
            $mail->ClearAllRecipients();
            $mail->ClearReplyTos();
            $mail->From = $defaults['email'];
            $mail->FromName = $defaults['name'];
            $subject = $mailSubject;
            $mail->Subject = $subject;
            $mail->Body = from_html($email_body);
            $mail->AltBody = $email_body_plain;
            $mail->prepForOutbound();
            if (isset($user_Obj->private_email_c) && $user_Obj->private_email_c != '' && !is_null($user_Obj->private_email_c)) {
                //$mail->AddAddress($user_Obj->private_email_c);
                foreach ($sugar_config['dail_work_digest_cc'] as $email) {
                    $mail->AddAddress($email);
                }
                if ($mail->Send()) {
                    $user_Obj->overdue_email_sent_c = TimeDate::getInstance()->nowDate();
                    $user_Obj->save();
                }
            }

            // END
        }
    }
    return true;
}

function processUploadImportPermitCase() {
    global $db, $sugar_config;
    $select = "SELECT
                cases.id                    AS id,
                cases_cstm.customer_name_c  AS customer_name,
                cases_cstm.customer_email_c AS customer_email,
                cases_cstm.order_number_c   AS order_number,
                cases.case_number           AS case_number
              FROM cases_cstm
                LEFT JOIN cases
                  ON cases.id = cases_cstm.id_c
              WHERE cases.deleted = 0
                  AND cases_cstm.permit_flag_c = 1";
    $query = $db->query($select);
    $emailtemplate = new EmailTemplate();
    $emailtemplate->retrieve($sugar_config['permit_reminder_order']);
    while ($result = $db->fetchByAssoc($query)) {
        $email_body = $emailtemplate->body_html;
        $email_body_plain = $emailtemplate->body;
        $mailSubject = $emailtemplate->subject;

        $email_body = str_replace('$customerName', $result['customer_name'], $email_body);
        $email_body = str_replace('$order_number', $result['order_number'], $email_body);
        $email_body = str_replace('$case_No', $result['case_number'], $email_body);

        $email_body_plain = str_replace('$customerName', $result['customer_name'], $email_body_plain);
        $email_body_plain = str_replace('$order_number', $result['order_number'], $email_body_plain);
        $email_body_plain = str_replace('$case_No', $result['case_number'], $email_body_plain);

        $mailSubject = str_replace('$order_number', $result['order_number'], $mailSubject);

        $email_address = $result['customer_email'];

        $emailObj = new Email();
        $defaults = $emailObj->getSystemDefaultEmail();
        $mail = new SugarPHPMailer();
        $mail->setMailerForSystem();
        $mail->ClearAllRecipients();
        $mail->ClearReplyTos();
        $mail->From = $defaults['email'];
        $mail->FromName = $defaults['name'];
        $subject = $mailSubject;
        $mail->Subject = $subject;
        $mail->Body = from_html($email_body);
        $mail->AltBody = $email_body_plain;
        $mail->prepForOutbound();
        $mail->AddAddress($email_address);
        if (!empty($email_address)) {
            $mail->Send();
        }
        //update permit flag to 0
        $query = "UPDATE cases_cstm SET permit_flag_c = 0 WHERE id_c = '{$result['id']}' ";
        $db->query($query);
    }
    return true;
}

function updateStoreDataDropDowns() {
    global $db, $sugar_config;

    $purgeSQL = "DELETE
                FROM bc_dropdown
                WHERE bc_dropdown.dropdown_id = 'supplierStoreData'
                     OR bc_dropdown.dropdown_id = 'productStoreData'";
    $db->query($purgeSQL);

    $productInsertSQL = "INSERT INTO bc_dropdown
                                (dropdown_id,
                                 option_val,
                                 option_name)
                    SELECT
                      'productStoreData' AS dropdown_id,
                      bc_storedata.sku   AS option_val,
                      bc_storedata.name  AS option_name
                    FROM bc_storedata
                    WHERE bc_storedata.deleted = 0
                        AND bc_storedata.name != ''
                        AND bc_storedata.name IS NOT NULL
                        AND bc_storedata.sku != ''
                        AND bc_storedata.sku IS NOT NULL
                    GROUP BY bc_storedata.sku
                    ORDER BY bc_storedata.name";
    $db->query($productInsertSQL);

    $supplierInsertSQL = "INSERT INTO bc_dropdown
                                                (dropdown_id,
                                                 option_val,
                                                 option_name)
                                    SELECT DISTINCT
                                      'supplierStoreData'     AS dropdown_id,
                                      bc_storedata.supplierid AS option_val,
                                      bc_storedata.supplierid AS option_name
                                    FROM bc_storedata
                                    WHERE bc_storedata.deleted = 0
                                        AND bc_storedata.supplierid != ''
                                        AND bc_storedata.supplierid IS NOT NULL
                                        AND bc_storedata.supplierid != ''
                                    ORDER BY bc_storedata.supplierid";
    $db->query($supplierInsertSQL);
    return true;
}

function updateLastShipDateFromOrdersInMagento() {
    global $db;
    include 'custom/include/magentoSoapIntegration/config.php';
    try {
        $customerSoapResponse = $soap->call($session_id, 'sales_order.getWeeklyShipments');
    } catch (Exception $e) {
        $customerSoapResponse = array();
    }
    foreach ($customerSoapResponse as $MagentoData) {
        $selctQuery = "UPDATE contacts_cstm
                            LEFT JOIN contacts
                          ON contacts_cstm.id_c = contacts.id
                        LEFT JOIN email_addr_bean_rel
                          ON email_addr_bean_rel.bean_id = contacts.id
                        LEFT JOIN email_addresses
                          ON email_addresses.id = email_addr_bean_rel.email_address_id
                          SET contacts_cstm.last_shipment_date_c = '" . date('Y-m-d', strtotime($MagentoData['shipped_date'])) . "',
                          contacts_cstm.first_followup_c = NULL
                      WHERE contacts.deleted = '0' AND email_addresses.email_address = '{$MagentoData['customer_email']}'";
        $db->query($selctQuery);
    }
    return true;
}

function sendCustomerFirstFollowUp() {
    global $sugar_config;
    global $db;

    require_once 'modules/Contacts/Contact.php';
    require_once 'modules/EmailTemplates/EmailTemplate.php';
    require_once 'modules/Emails/Email.php';
    require_once 'include/SugarPHPMailer.php';

    $sql = "SELECT
              contacts_cstm.last_shipment_date_c,
              contacts.id
            FROM contacts
              JOIN contacts_cstm
                ON contacts.id = contacts_cstm.id_c
                 LEFT JOIN email_addr_bean_rel AS ear
                                ON ear.bean_id = contacts.id
                                  AND ear.deleted = 0
                              LEFT JOIN email_addresses ea
                                ON ea.id = ear.email_address_id
                                  AND ea.deleted = 0
              JOIN (SELECT
                      contacts_cases.contact_id
                    FROM `cases`
                      LEFT JOIN cases_cstm
                        ON cases_cstm.id_c = cases.id
                      LEFT JOIN contacts_cases
                        ON contacts_cases.case_id = cases.id
                          AND contacts_cases.deleted = 0
                    WHERE cases.deleted = 0
                        AND cases.status <> 'Closed'
                        AND cases_cstm.technical_c = 'Complaint'
                        AND contacts_cases.contact_id IS NOT NULL
                    GROUP BY contacts_cases.contact_id) AS complaint_cases
                ON complaint_cases.contact_id <> contacts.id
            WHERE contacts.deleted = 0
                AND DATEDIFF(DATE(CURDATE()), contacts_cstm.last_shipment_date_c) > 10
                AND contacts_cstm.type_c IN('Regular_Customer','One_time_customer')
                AND contacts_cstm.last_shipment_date_c > '2014-06-01'
                AND (contacts_cstm.last_shipment_date_c IS NOT NULL
                      OR last_shipment_date_c != '')
                AND (contacts_cstm.first_followup_c IS NULL
                AND ea.email_address IS NOT NULL AND ea.opt_out = 0
                      OR contacts_cstm.first_followup_c = '')
            GROUP by ea.email_address
            ORDER BY contacts_cstm.last_shipment_date_c
            LIMIT 100";
    $result = $db->query($sql);
    while ($contactRow = $db->fetchByAssoc($result)) {

        $bean = new Contact();
        $bean->retrieve($contactRow['id']);

        //Send email
        $emailtemplate = new EmailTemplate();
        $emailtemplate->retrieve($sugar_config['cus_first_followup_template']);

        $email_body = $emailtemplate->body_html;
        //for plain text supported email client
        $email_body_plain = $emailtemplate->body;
        $email_body = str_replace('$contact_first_name', $bean->first_name, $email_body);

        //Correct the subject
        $mailSubject = $emailtemplate->subject;

        $email_address = $bean->email1;

        $emailObj = new Email();
        $defaults = $emailObj->getSystemDefaultEmail();
        $mail = new SugarPHPMailer();
        $mail->setMailerForSystem();
        $mail->ClearAllRecipients();
        $mail->ClearReplyTos();
        $mail->From = $defaults['email'];
        $mail->FromName = $defaults['name'];
        $subject = $mailSubject;
        $mail->Subject = $subject;
        $mail->Body = from_html($email_body);
        $mail->AltBody = $email_body_plain; //Sets the text-only body of the message.
        $mail->prepForOutbound();
        $address = $email_address;
        $mail->AddAddress($email_address);

        if (!empty($email_address) && $mail->Send()) {
            $emailObj->to_addrs = $address;
            $emailObj->type = 'out';
            $emailObj->deleted = '0';
            $emailObj->name = $subject;
            $emailObj->description = $email_body_plain; //Sets the text-only body of the message.
            $emailObj->description_html = from_html($email_body);
            $emailObj->from_addr = $defaults['email'];
            $emailObj->parent_type = 'Contacts';
            $emailObj->parent_id = $bean->id;
            $user_id = '1';
            $emailObj->date_sent = TimeDate::getInstance()->nowDb();
            $emailObj->assigned_user_id = $user_id;
            $emailObj->modified_user_id = $user_id;
            $emailObj->created_by = $user_id;
            $emailObj->status = 'sent';
            $emailObj->save();

            // set first_ followup flag
            $date = TimeDate::getInstance()->nowDbDate();
            $query = "UPDATE contacts_cstm SET first_followup_c = '{$date}' WHERE id_c = '{$bean->id}'";
            $db->query($query);
        } else {
            $mail_msg = $mail->ErrorInfo;
        }
    }
    return true;
}

function sendCustomerSecondFollowUp() {
    global $sugar_config;
    global $db;

    require_once 'modules/Contacts/Contact.php';
    require_once 'modules/EmailTemplates/EmailTemplate.php';
    require_once 'modules/Emails/Email.php';
    require_once 'include/SugarPHPMailer.php';

    $sql = "SELECT
              contacts_cstm.last_shipment_date_c,
              contacts.id
            FROM contacts
              JOIN contacts_cstm
                ON contacts.id = contacts_cstm.id_c
                LEFT JOIN email_addr_bean_rel AS ear
                                ON ear.bean_id = contacts.id
                                  AND ear.deleted = 0
                              LEFT JOIN email_addresses ea
                                ON ea.id = ear.email_address_id
                                  AND ea.deleted = 0
              JOIN (SELECT
                      contacts_cases.contact_id
                    FROM `cases`
                      LEFT JOIN cases_cstm
                        ON cases_cstm.id_c = cases.id
                      LEFT JOIN contacts_cases
                        ON contacts_cases.case_id = cases.id
                          AND contacts_cases.deleted = 0
                    WHERE cases.deleted = 0
                        AND cases.status <> 'Closed'
                        AND cases_cstm.technical_c = 'Complaint'
                        AND contacts_cases.contact_id IS NOT NULL
                    GROUP BY contacts_cases.contact_id) AS complaint_cases
                ON complaint_cases.contact_id <> contacts.id
            WHERE contacts.deleted = 0
                AND DATEDIFF(DATE(CURDATE()), contacts_cstm.last_shipment_date_c) > 20
                AND contacts_cstm.type_c IN('One_time_customer')
                AND contacts_cstm.last_shipment_date_c > '2014-06-01'
                AND (contacts_cstm.last_shipment_date_c IS NOT NULL
                      OR last_shipment_date_c != '')
                AND (contacts_cstm.second_followup_c IS NULL
                AND ea.email_address IS NOT NULL AND ea.opt_out = 0
                      OR contacts_cstm.second_followup_c = '')
            GROUP by ea.email_address
            ORDER BY contacts_cstm.last_shipment_date_c
            LIMIT 100";

    $result = $db->query($sql);
    while ($contactRow = $db->fetchByAssoc($result)) {

        $bean = new Contact();
        $bean->retrieve($contactRow['id']);

        //Send email
        $emailtemplate = new EmailTemplate();
        $emailtemplate->retrieve($sugar_config['cus_sec_followup_template']);

        $email_body = $emailtemplate->body_html;
        //for plain text supported email client
        $email_body_plain = $emailtemplate->body;
        $email_body = str_replace('$contact_first_name', $bean->first_name, $email_body);

        //Correct the subject
        $mailSubject = $emailtemplate->subject;

        $email_address = $bean->email1;

        $emailObj = new Email();
        $defaults = $emailObj->getSystemDefaultEmail();
        $mail = new SugarPHPMailer();
        $mail->setMailerForSystem();
        $mail->ClearAllRecipients();
        $mail->ClearReplyTos();
        $mail->From = $defaults['email'];
        $mail->FromName = $defaults['name'];
        $subject = $mailSubject;
        $mail->Subject = $subject;
        $mail->Body = from_html($email_body);
        $mail->AltBody = $email_body_plain; //Sets the text-only body of the message.
        $mail->prepForOutbound();
        $address = $email_address;
        $mail->AddAddress($email_address);

        if (!empty($email_address) && $mail->Send()) {
            $emailObj->to_addrs = $address;
            $emailObj->type = 'out';
            $emailObj->deleted = '0';
            $emailObj->name = $subject;
            $emailObj->description = $email_body_plain; //Sets the text-only body of the message.
            $emailObj->description_html = from_html($email_body);
            $emailObj->from_addr = $defaults['email'];
            $emailObj->parent_type = 'Contacts';
            $emailObj->parent_id = $bean->id;
            $user_id = '1';
            $emailObj->date_sent = TimeDate::getInstance()->nowDb();
            $emailObj->assigned_user_id = $user_id;
            $emailObj->modified_user_id = $user_id;
            $emailObj->created_by = $user_id;
            $emailObj->status = 'sent';
            $emailObj->save();

            // set second followup flag
            $date = TimeDate::getInstance()->nowDbDate();
            $query = "UPDATE contacts_cstm SET second_followup_c = '{$date}' WHERE id_c = '{$bean->id}'";
            $db->query($query);
        } else {
            $mail_msg = $mail->ErrorInfo;
        }
    }
    return true;
}

function sendCustomerSecondFollowUpMonthly() {
    global $sugar_config;
    global $db;

    require_once 'modules/Contacts/Contact.php';
    require_once 'modules/EmailTemplates/EmailTemplate.php';
    require_once 'modules/Emails/Email.php';
    require_once 'include/SugarPHPMailer.php';

    $sql = "SELECT
              contacts_cstm.last_shipment_date_c,
              contacts.id
            FROM contacts
              JOIN contacts_cstm
                ON contacts.id = contacts_cstm.id_c
                LEFT JOIN email_addr_bean_rel AS ear
                                ON ear.bean_id = contacts.id
                                  AND ear.deleted = 0
                              LEFT JOIN email_addresses ea
                                ON ea.id = ear.email_address_id
                                  AND ea.deleted = 0
              JOIN (SELECT
                      contacts_cases.contact_id
                    FROM `cases`
                      LEFT JOIN cases_cstm
                        ON cases_cstm.id_c = cases.id
                      LEFT JOIN contacts_cases
                        ON contacts_cases.case_id = cases.id
                          AND contacts_cases.deleted = 0
                    WHERE cases.deleted = 0
                        AND cases.status <> 'Closed'
                        AND cases_cstm.technical_c = 'Complaint'
                        AND contacts_cases.contact_id IS NOT NULL
                    GROUP BY contacts_cases.contact_id) AS complaint_cases
                ON complaint_cases.contact_id <> contacts.id
            WHERE contacts.deleted = 0
                AND contacts_cstm.type_c IN('One_time_customer')
                AND (contacts_cstm.last_shipment_date_c IS NULL
                      OR last_shipment_date_c = '')
                AND (contacts_cstm.second_followup_c IS NULL
                AND ea.email_address IS NOT NULL AND ea.opt_out = 0
                      OR contacts_cstm.second_followup_c = '')
            GROUP by ea.email_address
            LIMIT 300";


    $result = $db->query($sql);
    while ($contactRow = $db->fetchByAssoc($result)) {

        $bean = new Contact();
        $bean->retrieve($contactRow['id']);

        //Send email
        $emailtemplate = new EmailTemplate();
        $emailtemplate->retrieve($sugar_config['cus_sec_followup_template']);

        $email_body = $emailtemplate->body_html;
        //for plain text supported email client
        $email_body_plain = $emailtemplate->body;
        $email_body = str_replace('$contact_first_name', $bean->first_name, $email_body);

        //Correct the subject
        $mailSubject = $emailtemplate->subject;

        $email_address = $bean->email1;

        $emailObj = new Email();
        $defaults = $emailObj->getSystemDefaultEmail();
        $mail = new SugarPHPMailer();
        $mail->setMailerForSystem();
        $mail->ClearAllRecipients();
        $mail->ClearReplyTos();
        $mail->From = $defaults['email'];
        $mail->FromName = $defaults['name'];
        $subject = $mailSubject;
        $mail->Subject = $subject;
        $mail->Body = from_html($email_body);
        $mail->AltBody = $email_body_plain; //Sets the text-only body of the message.
        $mail->prepForOutbound();
        $address = $email_address;
        $mail->AddAddress($email_address);

        if (!empty($email_address) && $mail->Send()) {
            $emailObj->to_addrs = $address;
            $emailObj->type = 'out';
            $emailObj->deleted = '0';
            $emailObj->name = $subject;
            $emailObj->description = $email_body_plain; //Sets the text-only body of the message.
            $emailObj->description_html = from_html($email_body);
            $emailObj->from_addr = $defaults['email'];
            $emailObj->parent_type = 'Contacts';
            $emailObj->parent_id = $bean->id;
            $user_id = '1';
            $emailObj->date_sent = TimeDate::getInstance()->nowDb();
            $emailObj->assigned_user_id = $user_id;
            $emailObj->modified_user_id = $user_id;
            $emailObj->created_by = $user_id;
            $emailObj->status = 'sent';
            $emailObj->save();

            // set second followup flag
            $date = TimeDate::getInstance()->nowDbDate();
            $query = "UPDATE contacts_cstm SET second_followup_c = '{$date}' WHERE id_c = '{$bean->id}'";
            $db->query($query);
        } else {
            $mail_msg = $mail->ErrorInfo;
        }
    }
    return true;
}

function campaignForAutomaticEnquiry() {
    global $db, $sugar_config, $current_user;
    $emailtemplate = new EmailTemplate();
    $emailtemplate->retrieve($sugar_config['auto_enquiry_camp_template']);
    $mailSubject = $emailtemplate->subject;
    $sendMailDate = TimeDate::getInstance()->nowDbDate();
    //CONCAT(con.first_name, IF(con.last_name IS NOT NULL, CONCAT(' ' , con.last_name), '')) AS NAME,
    $result = $db->query("SELECT
                              con.id           AS ID,
                              con.first_name   AS NAME,
                              ea.email_address AS Email
                            FROM contacts con
                              LEFT JOIN contacts_cstm con_cstm
                                ON con.id = con_cstm.id_c
                                  AND con.deleted = 0
                              LEFT JOIN email_addr_bean_rel AS ear
                                ON ear.bean_id = con.id
                                  AND ear.deleted = 0
                              LEFT JOIN email_addresses ea
                                ON ea.id = ear.email_address_id
                                  AND ea.deleted = 0
                              JOIN (SELECT
                                      contacts_cases.contact_id
                                    FROM `cases`
                                      LEFT JOIN cases_cstm
                                        ON cases_cstm.id_c = cases.id
                                      LEFT JOIN contacts_cases
                                        ON contacts_cases.case_id = cases.id
                                          AND contacts_cases.deleted = 0
                                    WHERE cases.deleted = 0
                                        AND cases.status <> 'Closed'
                                        AND cases_cstm.technical_c = 'Complaint'
                                        AND contacts_cases.contact_id IS NOT NULL
                                    GROUP BY contacts_cases.contact_id) AS complaint_cases
                                ON complaint_cases.contact_id <> con.id
                            WHERE con.deleted = 0
                                AND con_cstm.type_c IN('Enquiry')
                                AND DATEDIFF(CURDATE(),con.date_modified) > '21'
                                AND ea.email_address IS NOT NULL AND ea.opt_out = 0
                                AND (con_cstm.automatic_enquiry_c IS NULL
                                      OR con_cstm.automatic_enquiry_c = '')
                            GROUP BY ea.email_address
                            LIMIT 100");
    while ($result_query = $db->fetchByAssoc($result)) {
        $email_body = $emailtemplate->body_html;
        $email_body_plain = $emailtemplate->body;
        $email_body = str_replace('$contact_first_name', $result_query['NAME'], $email_body);
        $email_body_plain = str_replace('$contact_first_name', $result_query['NAME'], $email_body_plain);
        $email_address = $result_query['Email'];

        $emailObj = new Email();
        $defaults = $emailObj->getSystemDefaultEmail();
        $mail = new SugarPHPMailer();
        $mail->setMailerForSystem();
        $mail->ClearAllRecipients();
        $mail->ClearReplyTos();
        $mail->From = $defaults['email'];
        $mail->FromName = $defaults['name'];
        $subject = $mailSubject;
        $mail->Subject = $subject;
        $mail->Body = from_html($email_body);
        $mail->AltBody = $email_body_plain;
        $mail->prepForOutbound();
        $mail->AddAddress($email_address);
        if (!empty($email_address) && $mail->Send()) {
            $emailObj->to_addrs = $email_address;
            $emailObj->type = 'out';
            $emailObj->deleted = '0';
            $emailObj->name = $subject;
            $emailObj->description = $email_body_plain;
            $emailObj->description_html = from_html($email_body);
            $emailObj->from_addr = $defaults['email'];
            $emailObj->parent_type = 'Contacts';
            $emailObj->parent_id = $result_query['ID'];
            $user_id = $current_user->id;
            $emailObj->date_sent = TimeDate::getInstance()->nowDb();
            $emailObj->assigned_user_id = $user_id;
            $emailObj->modified_user_id = $user_id;
            $emailObj->created_by = $user_id;
            $emailObj->status = 'sent';
            $emailObj->save();
            $query = "UPDATE contacts_cstm SET automatic_enquiry_c = '{$sendMailDate}' WHERE id_c = '{$result_query['ID']}' ";
            $db->query($query);
        } else {
            $mail_msg = $mail->ErrorInfo;
        }
    }
    return true;
}

function campaignForDiscountsForData() {
    global $db, $sugar_config, $current_user;
    $emailtemplate = new EmailTemplate();
    $emailtemplate->retrieve($sugar_config['discounts_for_data_template']);
    $mailSubject = $emailtemplate->subject;
    $sendMailDate = TimeDate::getInstance()->nowDbDate();
    //CONCAT(con.first_name, IF(con.last_name IS NOT NULL, CONCAT(' ' , con.last_name), '')) AS NAME,
    $result = $db->query("SELECT
                              con.id                        AS ID,
                              con.first_name                AS NAME,
                              ea.email_address              AS Email,
                              con_cstm.last_shipment_date_c
                            FROM contacts con
                              LEFT JOIN contacts_cstm con_cstm
                                ON con.id = con_cstm.id_c
                                  AND con.deleted = 0
                              LEFT JOIN email_addr_bean_rel AS ear
                                ON ear.bean_id = con.id
                                  AND ear.deleted = 0
                              LEFT JOIN email_addresses ea
                                ON ea.id = ear.email_address_id
                                  AND ea.deleted = 0
                              JOIN (SELECT
                                      contacts_cases.contact_id
                                    FROM `cases`
                                      LEFT JOIN cases_cstm
                                        ON cases_cstm.id_c = cases.id
                                      LEFT JOIN contacts_cases
                                        ON contacts_cases.case_id = cases.id
                                          AND contacts_cases.deleted = 0
                                    WHERE cases.deleted = 0
                                        AND cases.status <> 'Closed'
                                        AND cases_cstm.technical_c = 'Complaint'
                                        AND contacts_cases.contact_id IS NOT NULL
                                    GROUP BY contacts_cases.contact_id) AS complaint_cases
                                ON complaint_cases.contact_id <> con.id
                            WHERE con.deleted = 0
                                AND con_cstm.type_c IN('One_time_customer','Regular_Customer')
                                AND IF(con_cstm.last_shipment_date_c IS NULL
                                        OR con_cstm.last_shipment_date_c = '', DATEDIFF(CURDATE(),IF(con_cstm.second_followup_c IS NOT NULL, con_cstm.second_followup_c, con_cstm.first_followup_c)) > '28', DATEDIFF(CURDATE(),con_cstm.last_shipment_date_c) > '42')
                                AND IF(con_cstm.last_shipment_date_c IS NOT NULL, con_cstm.last_shipment_date_c > '2014-05-01', TRUE)
                                AND ea.email_address IS NOT NULL AND ea.opt_out = 0
                                AND (con_cstm.discounts_followup_c IS NULL
                                      OR con_cstm.discounts_followup_c = '')
                            GROUP by ea.email_address
                            ORDER by con_cstm.last_shipment_date_c
                            LIMIT 100");
    while ($result_query = $db->fetchByAssoc($result)) {
        $email_body = $emailtemplate->body_html;
        $email_body_plain = $emailtemplate->body;
        $email_body = str_replace('$contact_first_name', $result_query['NAME'], $email_body);
        $email_body_plain = str_replace('$contact_first_name', $result_query['NAME'], $email_body_plain);
        $email_address = $result_query['Email'];

        $emailObj = new Email();
        $defaults = $emailObj->getSystemDefaultEmail();
        $mail = new SugarPHPMailer();
        $mail->setMailerForSystem();
        $mail->ClearAllRecipients();
        $mail->ClearReplyTos();
        $mail->From = $defaults['email'];
        $mail->FromName = $defaults['name'];
        $subject = $mailSubject;
        $mail->Subject = $subject;
        $mail->Body = from_html($email_body);
        $mail->AltBody = $email_body_plain;
        $mail->prepForOutbound();
        $mail->AddAddress($email_address);
        if (!empty($email_address) && $mail->Send()) {
            $emailObj->to_addrs = $email_address;
            $emailObj->type = 'out';
            $emailObj->deleted = '0';
            $emailObj->name = $subject;
            $emailObj->description = $email_body_plain;
            $emailObj->description_html = from_html($email_body);
            $emailObj->from_addr = $defaults['email'];
            $emailObj->parent_type = 'Contacts';
            $emailObj->parent_id = $result_query['ID'];
            $user_id = $current_user->id;
            $emailObj->date_sent = TimeDate::getInstance()->nowDb();
            $emailObj->assigned_user_id = $user_id;
            $emailObj->modified_user_id = $user_id;
            $emailObj->created_by = $user_id;
            $emailObj->status = 'sent';
            $emailObj->save();
            $query = "UPDATE contacts_cstm SET discounts_followup_c = '{$sendMailDate}' WHERE id_c = '{$result_query['ID']}' ";
            $db->query($query);
        } else {
            $mail_msg = $mail->ErrorInfo;
        }
    }
    return true;
}

function updateProductComplaintsData() {
    global $db;
    $selectQuery = "SELECT
                            id,
                            product_c AS product
                          FROM cases
                            JOIN cases_cstm
                              ON cases.id = cases_cstm.id_c
                          WHERE cases.deleted = 0
                              AND cases_cstm.technical_c = 'Complaint'
                              AND cases_cstm.product_c IS NOT NULL
                              AND cases_cstm.product_c != ''";

    $query_Run = $db->query($selectQuery);
    $complaintDataArray = array();
    $complaintData = array();
    while ($result = $db->fetchByAssoc($query_Run)) {
        $prod = explode(',', $result['product']);
        foreach ($prod as $product) {
            $complaintData[] = $product;
        }
    }
    $complaintData = array_filter($complaintData);
    $product_complaint_count = array_count_values($complaintData);
    $product_complaint_count = array_filter($product_complaint_count, function ($value, $key) {
        return ($value >= 2);
    });
    foreach ($product_complaint_count as $product => $complaintCount) {
        $insertData[] = "('{$product}', '{$complaintCount}')";
    }
    $values = implode(", ", $insertData);
    $deleteQuery = "Delete from product_complaint_tbl";
    $db->query($deleteQuery);
    $insertData = "Insert Into product_complaint_tbl (complaint_product,complaint) Values {$values}";
    $db->query($insertData);
    return true;
}
