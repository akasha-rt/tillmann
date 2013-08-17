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
$job_strings[] = 'updateCaseStatusOnModification';
$job_strings[] = 'updateCustomerFromMagento';
$job_strings[] = 'sendMonthlyWorkLog';
$job_strings[] = 'processUploadImportPermitCase';

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

        if ($mail->Send()) {
            $emailObj->to_addrs = $address;
            $emailObj->type = 'out';
            $emailObj->deleted = '0';
            $emailObj->name = $subject;
            $emailObj->description = $email_body_plain;
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

        if ($mail->Send()) {
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
        $bean->po_number_c = $PO_VAT_Case['po_number'];
        $bean->vat_number_c = $PO_VAT_Case['vat_number'];
        $bean->order_number_c = $PO_VAT_Case['order_number'];
        $bean->case_number = $PO_VAT_Case['case_number'];

        //Send email
        $emailtemplate = new EmailTemplate();
        if (!is_null($PO_VAT_Case['po_number']) && $PO_VAT_Case['po_number'] != "") {
            $emailtemplate = $emailtemplate->retrieve('9a5243cf-982c-3c64-bd3c-50ff8ee7171f');
            $email_body = $emailtemplate->body_html;
            $email_body_plain = $emailtemplate->body;
            $email_body = str_replace('$customerName', $bean->customer_name_c, $email_body);
            $email_body = str_replace('$po_number', $bean->po_number_c, $email_body);
            $email_body = str_replace('$order_number', $bean->order_number_c, $email_body);
            $email_body = str_replace('$case_No', $bean->case_number, $email_body);
            $mailSubject = $emailtemplate->subject;
            $mailSubject = str_replace('$order_number', $bean->order_number_c, $mailSubject);
        } elseif (!is_null($PO_VAT_Case['vat_number']) && $PO_VAT_Case['vat_number'] != "") {
            $emailtemplate = $emailtemplate->retrieve('47062b68-ad29-beb2-454c-50ff8e5e4572');
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

        if ($mail->Send()) {
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
    global $db;
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
                    cev1.value                 AS first_name,
                    cev2.value                 AS last_name,
                    ceav1.value                AS phone,
                    ceav2.value                AS country_id,
                    t.date                     AS last_ship_date
                  FROM customer_entity
                    LEFT JOIN customer_entity_varchar cev1
                      ON cev1.entity_id = customer_entity.entity_id
                        AND cev1.attribute_id = 5
                    LEFT JOIN customer_entity_varchar cev2
                      ON cev2.entity_id = customer_entity.entity_id
                        AND cev2.attribute_id = 7
                    LEFT JOIN customer_entity_int
                      ON customer_entity.entity_id = customer_entity_int.entity_id
                        AND customer_entity_int.attribute_id = 13
                    LEFT JOIN customer_address_entity_varchar ceav1
                      ON ceav1.entity_id = customer_entity_int.value
                        AND ceav1.attribute_id = 29
                    LEFT JOIN customer_address_entity_varchar ceav2
                      ON ceav2.entity_id = customer_entity_int.value
                        AND ceav2.attribute_id = 25
                    LEFT JOIN (SELECT
                                 customer_id,
                                 MAX( created_at )           AS DATE
                               FROM sales_flat_shipment
                               WHERE customer_id IS NOT NULL
                               GROUP BY customer_id) AS t
                      ON t.customer_id = customer_entity.entity_id
                      {$limitRecords} 
                      ORDER BY created_at asc,t.date asc";
    $result = mysql_query($query);
    while ($data = mysql_fetch_array($result)) {
        //existing customer
        if (isset($sugar_email_list[$data['email']])) {
            $contacts = new Contact();
            $contacts->retrieve($sugar_email_list[$data['email']]);
            if ($contacts->type_c == 'Enquiry') {
                $contacts->type_c = 'One_time_customer';
                $contacts->last_shipment_date_c = $data['last_ship_date'];
                $contacts->save();
            } else if ($contacts->type_c == 'One_time_customer') {
                $contacts->type_c = 'Regular_Customer';
                $contacts->last_shipment_date_c = $data['last_ship_date'];
                $contacts->save();
            }
        } else {
            //new customer
            $contacts = new Contact();
            $contacts->first_name = $data['first_name'];
            $contacts->last_name = $data['last_name'];
            $contacts->email1 = $data['email'];
            $contacts->phone_mobile = $data['phone'];
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

function processUploadImportPermitCase() {
    global $db;
    $select = "SELECT
                customer_email_c AS Customer_Email
              FROM cases_cstm
                LEFT JOIN cases
                  ON cases.id = cases_cstm.id_c
              WHERE cases.deleted = 0
                  AND cases_cstm.permit_flag_c = 1";
    $query = $db->query($select);
    $emailtemplate = new EmailTemplate();
    $emailtemplate = $emailtemplate->retrieve('11f5cc02-d34a-6470-7b0f-51ef75e58e9a');
    while ($result = $db->fetchByAssoc($query)) {
        $email_body = $emailtemplate->body_html;
        $email_body_plain = $emailtemplate->body;
        $mailSubject = $emailtemplate->subject;
        $email_address = $result['Customer_Email'];

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
        $mail->Send();
        return true;
        // END
    }
}

?>
