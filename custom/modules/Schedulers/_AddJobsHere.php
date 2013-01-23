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
$job_strings[] = 'processOverDueCase';
$job_strings[] = 'processPOAndVATCases';

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

function checkOpportunitySalesData() {
    $GLOBALS['log']->debug('Custom Scheduler : Starting checkOpportunitySalesData');
    require_once('modules/Emails/Email.php');
    require_once('modules/Opportunities/Opportunity.php');
    global $db;
    $oppDataSql = $db->query("SELECT
                                    opp.id                      AS id,
                                    opp_c.product_c             AS product_sku,
                                    opp_c.country_c             AS country,
                                    LTRIM(RTRIM(jt0.first_name)) AS assigned_user_name,
                                    LTRIM(RTRIM(contacts.first_name)) AS contact_name,
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
                                  WHERE DATE_ADD(opp.date_entered,INTERVAL  7 DAY) <= NOW()
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
        //Create Opp object
        $currentOpp = new Opportunity();
        $currentOpp = $currentOpp->retrieve($oppId);

        $name = $contactData[$oppId]['Contact_name'];
        $assigned_user_name = $contactData[$oppId]['assigned_user_name'];
        $email_address = $contactData[$oppId]['email_address'];
        //$email_address = 'dhaval@india.biztechconsultancy.com';
        $emailtemplate = new EmailTemplate();
        if (count($oppOrderStatus) == 0) {
            $currentOpp->sales_stage = "Closed Lost";
            $emailtemplate = $emailtemplate->retrieve('786de532-f84f-6209-5e57-507505bf9e65');
        } else {
            $currentOpp->sales_stage = "Closed Won";
            $emailtemplate = $emailtemplate->retrieve('cd5a91aa-9409-249c-b17d-507505fe8269');
        }

        //Load the signature
        require_once 'modules/Users/UserSignature.php';
        $signature = new UserSignature();
        $signature->retrieve_by_string_fields(array("user_id" => '1'));

        $email_body = $emailtemplate->body_html;
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
            $currentOpp->is_email_sent_c = 1;
            $currentOpp->save();
            /* $db->query("UPDATE opportunities_cstm
              SET is_email_sent_c = 1
              WHERE opportunities_cstm.id_c = '" . $oppId . "'"); */
        } else {
            $mail_msg = $mail->ErrorInfo;
            //echo "error sending " . $mail_msg;
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
        $emailtemplate = $emailtemplate->retrieve('34565e1a-257d-c1af-0eba-50e7a45b7b60');

        $email_body = $emailtemplate->body_html;
        $email_body = str_replace('$customer_name_c', $bean->customer_name_c, $email_body);
        $email_body = str_replace('$invoice_no_c', $bean->invoice_no_c, $email_body);

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
        $mail->AltBody = from_html($email_body);

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

        if ($mail->Send()) {
            $emailObj->to_addrs = $address;
            $emailObj->type = 'out';
            $emailObj->deleted = '0';
            $emailObj->name = $subject;
            $emailObj->description = null;
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
    global $db;

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
        $bean->customer_po_number_c = $PO_VAT_Case['po_number'];
        $bean->customer_vat_number_c = $PO_VAT_Case['vat_number'];
        $bean->customer_order_number_c = $PO_VAT_Case['order_number'];
        $bean->customer_case_number = $PO_VAT_Case['case_number'];

        //Send email
        $emailtemplate = new EmailTemplate();
        if (!is_null($PO_VAT_Case['po_number']) && $PO_VAT_Case['po_number'] != "") {
            $emailtemplate = $emailtemplate->retrieve('9a5243cf-982c-3c64-bd3c-50ff8ee7171f');
            $email_body = $emailtemplate->body_html;
            $email_body = str_replace('$customerName', $bean->customer_name_c, $email_body);
            $email_body = str_replace('$po_number', $bean->customer_po_number_c, $email_body);
            $email_body = str_replace('$order_number', $bean->customer_order_number_c, $email_body);
            $email_body = str_replace('$case_No', $bean->customer_case_number, $email_body);
            $mailSubject = $emailtemplate->subject;
            $mailSubject = str_replace('$order_number', $bean->customer_order_number_c, $mailSubject);
        } elseif (!is_null($PO_VAT_Case['vat_number']) && $PO_VAT_Case['vat_number'] != "") {
            $emailtemplate = $emailtemplate->retrieve('47062b68-ad29-beb2-454c-50ff8e5e4572');
            $email_body = $emailtemplate->body_html;
            $email_body = str_replace('$customerName', $bean->customer_name_c, $email_body);
            $email_body = str_replace('$order_number', $bean->customer_order_number_c, $email_body);
            $email_body = str_replace('$case_No', $bean->customer_case_number, $email_body);
            $mailSubject = $emailtemplate->subject;
            $mailSubject = str_replace('$order_number', $bean->customer_order_number_c, $mailSubject);
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
            $emailObj->parent_type = 'Cases';
            $emailObj->parent_id = $bean->id;
            $user_id = 'c01295a1-6e11-1c36-099b-4fe99aef1381';
            $emailObj->date_sent = TimeDate::getInstance()->nowDb();
            $emailObj->assigned_user_id = $user_id;
            $emailObj->modified_user_id = $user_id;
            $emailObj->created_by = $user_id;
            $emailObj->status = 'sent';
            $emailObj->save();
            $bean->customer_po_number_c = '';
            $bean->customer_vat_number_c = '';
            $bean->save();
        } else {
            $mail_msg = $mail->ErrorInfo;
        }
    }
    return true;
}

?>
