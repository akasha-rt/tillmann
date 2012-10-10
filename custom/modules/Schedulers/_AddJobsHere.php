<?php

/**
 * Custom Scheduler File
 * @author Dhaval darji
 */
if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

//Add the new job type to the Option in the job dropdown in scheduler
$job_strings[] = 'createOppFromCase';
$job_strings[] = 'checkOpportunitySalesData';

//Function to call when the new job is called from cronjob
function createOppFromCase() {
    $GLOBALS['log']->debug('Custom Scheduler : Starting createOppFromCase');
    require_once('modules/Opportunities/Opportunity.php');
    $op = new Opportunity();

    global $db;
    $case_list = $db->query("SELECT
                              c.id,
                              c.name,
                              c.description,
                              c.assigned_user_id,
                              c.case_number,
                              c_c.contact_id
                            FROM cases c
                              LEFT JOIN contacts_cases c_c
                                ON c.id = c_c.case_id
                            WHERE DATE_ADD(c.date_modified,INTERVAL 14 DAY) <= NOW()
                                AND c.status = 'Closed'
                                AND c.convertedtoopp = '0'
                                AND c.deleted = 0
                                AND c_c.deleted = 0
                                AND c.id IN(SELECT
                                                c_a.parent_id
                                            FROM cases_audit c_a
                                            WHERE (c_a.before_value_string != 'PO'
                                                   AND c_a.after_value_string != 'PO')
                                                AND c_a.field_name = 'status')
                                 GROUP By c.id");
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

function checkOpportunitySalesData() {
    $GLOBALS['log']->debug('Custom Scheduler : Starting checkOpportunitySalesData');
    require_once('modules/Emails/Email.php');
    global $db;
    $oppDataSql = $db->query("SELECT
                                    opp.id                      AS id,
                                    opp_c.product_c             AS product_sku,
                                    opp_c.country_c             AS country,
                                    LTRIM(RTRIM(CONCAT(IFNULL(jt0.first_name,''),' ',IFNULL(jt0.last_name,'')))) AS assigned_user_name,
                                    LTRIM(RTRIM(CONCAT(IFNULL(contacts.first_name,''),' ',IFNULL(contacts.last_name,'')))) AS contact_name,
                                    contacts.id                 AS conid,
                                    contact_email.email_address AS email_address
                                  FROM opportunities opp
                                    LEFT JOIN opportunities_cstm opp_c
                                      ON opp.id = opp_c.id_c
                                      AND opp_c.is_email_sent_c = 0
                                    LEFT JOIN users jt0
                                      ON opp.assigned_user_id = jt0.id
                                        AND jt0.deleted = 0
                                    LEFT JOIN opportunities_contacts
                                      ON opp.id = opportunities_contacts.opportunity_id
                                        AND opportunities_contacts.deleted = 0
                                    INNER JOIN contacts contacts
                                      ON contacts.id = opportunities_contacts.contact_id
                                        AND contacts.deleted = 0
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
                                  WHERE DATE_ADD(opp.date_entered,INTERVAL 7 DAY) <= NOW()
                                      AND opp.sales_stage = 'Proposal/Price Quote'
                                      AND opp_c.product_c IS NOT NULL
                                      AND opp_c.product_c <> ''
                                      AND opp_c.country_c IS NOT NULL
                                      AND opp_c.country_c <> ''
                                      AND opp.deleted = 0
                                      AND opp_c.is_email_sent_c = 0");
    $oppSoapData = array();
    $contactData = array();
    while ($oppData = $db->fetchByAssoc($oppDataSql)) {
        $oppSoapData[$oppData['id']]['country'] = $oppData['country'];
        $oppSoapData[$oppData['id']]['product_sku'] = $oppData['product_sku'];
        $contactData[$oppData['id']]['assigned_user_name'] = $oppData['assigned_user_name'];
        $contactData[$oppData['id']]['Contact_name'] = $oppData['contact_name'];
        $contactData[$oppData['id']]['email_address'] = $oppData['email_address'];
    }

    //SOAP CALL BEGIN
    include 'custom/include/magentoSoapIntegration/config.php';

    try {
        $oppSoapResponse = $soap->call($session_id, 'sales_order.getOrderStatus', array($oppSoapData));
    } catch (Exception $e) {
        $oppSoapResponse = array();
    }
    //SOAP CALL END
    //SEND EMAIL START
    foreach ($oppSoapResponse as $oppId => $oppOrderStatus) {
        $name = $contactData[$oppId]['Contact_name'];
        $assigned_user_name = $contactData[$oppId]['assigned_user_name'];
        $email_address = $contactData[$oppId]['email_address'];
        //$email_address = 'dhaval@india.biztechconsultancy.com';
        $emailtemplate = new EmailTemplate();
        if (count($oppOrderStatus) == 0) {

            $emailtemplate = $emailtemplate->retrieve('b60bf519-5c0d-c8f9-9884-5073e8ddc58a');
        } else {

            $emailtemplate = $emailtemplate->retrieve('1798a89a-2995-0b03-f07a-5073e8e73554');
        }

        //Load the signature
        require_once 'modules/Users/UserSignature.php';
        $signature = new UserSignature();
        $signature->retrieve_by_string_fields(array("user_id" => '1'));

        $email_body = $emailtemplate->body_html;
        $email_body = str_replace('%first_name', $name, $email_body);
        $email_body = str_replace('%Assigned_user_firstname', $assigned_user_name, $email_body);
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
        $mail->AltBody = from_html($email_body);
        $mail->prepForOutbound();
        $address = $email_address;
        $mail->AddAddress($email_address);

        if ($mail->Send()) {
            $emailObj->to_addrs = $address;
            $emailObj->type = 'out';
            $emailObj->deleted = '0';
            $emailObj->name = $subject;
            $emailObj->description = null;
            $emailObj->description_html = from_html($email_body);
            $emailObj->from_addr = $defaults['email'];
            $emailObj->parent_type = 'Opportunities';
            $emailObj->parent_id = $oppId;
            $user_id = '1';
            $emailObj->date_sent = TimeDate::getInstance()->nowDb();
            $emailObj->assigned_user_id = $user_id;
            $emailObj->modified_user_id = $user_id;
            $emailObj->created_by = $user_id;
            $emailObj->status = 'sent';
            $emailObj->save();
            $db->query("UPDATE opportunities_cstm
                            SET is_email_sent_c = 1
                        WHERE opportunities_cstm.id_c = '" . $oppId . "'");
        } else {
            $mail_msg = $mail->ErrorInfo;
            //echo "error sending " . $mail_msg;
        }
    }

    //Return true to notify the successfull execution of the job
    return true;
}

?>
