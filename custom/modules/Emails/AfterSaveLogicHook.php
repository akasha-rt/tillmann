<?php

class AfterSaveLogicHook
{
    /**
     * @function Handle after_save functionality
     * @return null
     */
    public function after_save_hook_handler($bean, $event, $arguments)
    {
        $this->archive_email($bean);
        $this->create_contact($bean);
    }

    /**
     * @function Update email's type and status when it is linked with "Case"
     * @return null
     */
    public function archive_email($bean)
    {
        global $db;
            
        if ($bean->parent_type == "Cases" && $bean->parent_id) {
            // set type and status to 'archived'
            $email_id = $bean->id;
            $query = "UPDATE emails SET type = 'archived', status = 'archived' WHERE id = '$email_id'";
            $db->query($query);
        }
    }

    /**
     * @function Create new contact if the contact does not exists with same email_address
     * @return null
     */
    public function create_contact($bean)
    {
        global $db;
        $email_id = $bean->id;
        // get sender email address
        $query = "SELECT from_addr FROM emails_text WHERE email_id = '$email_id'";
        $result = $db->query($query);
        $row = $db->fetchByAssoc($result);
        if ($row)
        {
            $from_addr = $row['from_addr'];
            $from_addr = htmlspecialchars_decode($from_addr);
            // extract first_name and email address from from_addr
            $pattern = '/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i';
            preg_match_all($pattern, $from_addr, $matches);
            $email_address = $matches[0][0];

            $parts = explode("<", $from_addr);
            $first_name = $parts[0];

            if(empty($first_name) || count($parts) == 1){
                $parts = explode("@", $from_addr);
                $first_name = $parts[0];
            }

            if(empty($email_address)){
                return;
            }

            $fn = '';
            $ln = '';
            $this->split_contact_name($first_name, $fn, $ln);

            // check if email address is related to a contact
            $query = <<<EOQ
                SELECT eml_addr_bean.*
                FROM email_addresses eml_addr
                INNER JOIN email_addr_bean_rel eml_addr_bean
                ON eml_addr_bean.email_address_id = eml_addr.id
                AND eml_addr.deleted = 0
                AND eml_addr_bean.deleted = 0
                AND eml_addr.email_address = "$email_address"
                AND eml_addr_bean.bean_module = "Contacts";
EOQ;
            $result = $db->query($query);

            if($result->num_rows == 0) {
            	// create new contact
                $contact = new Contact();
                $contact->first_name = $fn;
                $contact->last_name = $ln;
                $contact->email1 = $email_address;
                $contact->assigned_user_id = '1';
                $cntc_id = $contact->save();
            }
        }
    }

    public function split_contact_name($name, &$fn, &$ln)
    {
        $name_parts = explode(" ", $name);
        if(count($name_parts) > 1) {
            $fn = array_shift($name_parts);
            $ln = implode(" ", $name_parts);
        }
        else
        {
            $fn = "";
            $ln = $name;
        }
    }
}
