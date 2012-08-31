<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');
/* * *******************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2011 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 * ****************************************************************************** */

/* * *******************************************************************************

 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 * ****************************************************************************** */

require_once("include/SugarObjects/templates/company/Company.php");

// Account is used to store account information.
class Account extends Company {

    var $field_name_map = array();
    // Stored fields
    var $date_entered;
    var $date_modified;
    var $modified_user_id;
    var $assigned_user_id;
    var $annual_revenue;
    var $billing_address_street;
    var $billing_address_city;
    var $billing_address_state;
    var $billing_address_country;
    var $billing_address_postalcode;
    var $billing_address_street_2;
    var $billing_address_street_3;
    var $billing_address_street_4;
    var $description;
    var $email1;
    var $email2;
    var $email_opt_out;
    var $invalid_email;
    var $employees;
    var $id;
    var $industry;
    var $name;
    var $ownership;
    var $parent_id;
    var $phone_alternate;
    var $phone_fax;
    var $phone_office;
    var $rating;
    var $shipping_address_street;
    var $shipping_address_city;
    var $shipping_address_state;
    var $shipping_address_country;
    var $shipping_address_postalcode;
    var $shipping_address_street_2;
    var $shipping_address_street_3;
    var $shipping_address_street_4;
    var $campaign_id;
    var $sic_code;
    var $ticker_symbol;
    var $account_type;
    var $website;
    var $custom_fields;
    var $created_by;
    var $created_by_name;
    var $modified_by_name;
    // These are for related fields
    var $opportunity_id;
    var $case_id;
    var $contact_id;
    var $task_id;
    var $note_id;
    var $meeting_id;
    var $call_id;
    var $email_id;
    var $member_id;
    var $parent_name;
    var $assigned_user_name;
    var $account_id = '';
    var $account_name = '';
    var $bug_id = '';
    var $module_dir = 'Accounts';
    var $emailAddress;
    var $table_name = "accounts";
    var $object_name = "Account";
    var $importable = true;
    var $new_schema = true;
    // This is used to retrieve related fields from form posts.
    var $additional_column_fields = Array('assigned_user_name', 'assigned_user_id', 'opportunity_id', 'bug_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id'
    );
    var $relationship_fields = Array('opportunity_id' => 'opportunities', 'bug_id' => 'bugs', 'case_id' => 'cases',
        'contact_id' => 'contacts', 'task_id' => 'tasks', 'note_id' => 'notes',
        'meeting_id' => 'meetings', 'call_id' => 'calls', 'email_id' => 'emails', 'member_id' => 'members',
        'project_id' => 'project',
    );
    //Meta-Data Framework fields
    var $push_billing;
    var $push_shipping;

    function Account() {
        parent::Company();

        $this->setupCustomFields('Accounts');

        foreach ($this->field_defs as $field) {
            if (isset($field['name'])) {
                $this->field_name_map[$field['name']] = $field;
            }
        }


        //Combine the email logic original here with bug #26450.
        if ((!empty($_REQUEST['parent_id']) && !empty($_REQUEST['parent_type']) && $_REQUEST['parent_type'] == 'Emails'
                && !empty($_REQUEST['return_module']) && $_REQUEST['return_module'] == 'Emails' )
                ||
                (!empty($_REQUEST['parent_type']) && $_REQUEST['parent_type'] != 'Accounts' &&
                !empty($_REQUEST['return_module']) && $_REQUEST['return_module'] != 'Accounts')) {
            $_REQUEST['parent_name'] = '';
            $_REQUEST['parent_id'] = '';
        }
    }

    function get_summary_text() {
        return $this->name;
    }

    function get_contacts() {
        return $this->get_linked_beans('contacts', 'Contact');
    }

    function clear_account_case_relationship($account_id='', $case_id='') {
        if (empty($case_id))
            $where = '';
        else
            $where = " and id = '$case_id'";
        $query = "UPDATE cases SET account_name = '', account_id = '' WHERE account_id = '$account_id' AND deleted = 0 " . $where;
        $this->db->query($query, true, "Error clearing account to case relationship: ");
    }

    /**
     * This method is used to provide backward compatibility with old data that was prefixed with http://
     * We now automatically prefix http://
     * @deprecated.
     */
    function remove_redundant_http() { /*
      if(preg_match("@http://@", $this->website))
      {
      $this->website = substr($this->website, 7);
      }
     */
    }

    function fill_in_additional_list_fields() {
        parent::fill_in_additional_list_fields();
        // Fill in the assigned_user_name
        //	$this->assigned_user_name = get_assigned_user_name($this->assigned_user_id);
    }

    function fill_in_additional_detail_fields() {
        parent::fill_in_additional_detail_fields();

        //rrs bug: 28184 - instead of removing this code altogether just adding this check to ensure that if the parent_name
        //is empty then go ahead and fill it.
        if (empty($this->parent_name) && !empty($this->id)) {
            $query = "SELECT a1.name from accounts a1, accounts a2 where a1.id = a2.parent_id and a2.id = '$this->id' and a1.deleted=0";
            $result = $this->db->query($query, true, " Error filling in additional detail fields: ");

            // Get the id and the name.
            $row = $this->db->fetchByAssoc($result);

            if ($row != null) {
                $this->parent_name = $row['name'];
            } else {
                $this->parent_name = '';
            }
        }

        // Set campaign name if there is a campaign id
        if (!empty($this->campaign_id)) {

            $camp = new Campaign();
            $where = "campaigns.id='{$this->campaign_id}'";
            $campaign_list = $camp->get_full_list("campaigns.name", $where, true);
            $this->campaign_name = $campaign_list[0]->name;
        }
    }

    function get_list_view_data() {
        global $system_config, $current_user;
        $temp_array = $this->get_list_view_array();
        $temp_array["ENCODED_NAME"] = $this->name;
//		$temp_array["ENCODED_NAME"]=htmlspecialchars($this->name, ENT_QUOTES);
        if (!empty($this->billing_address_state)) {
            $temp_array["CITY"] = $this->billing_address_city . ', ' . $this->billing_address_state;
        } else {
            $temp_array["CITY"] = $this->billing_address_city;
        }
        $temp_array["BILLING_ADDRESS_STREET"] = $this->billing_address_street;
        $temp_array["SHIPPING_ADDRESS_STREET"] = $this->shipping_address_street;
        if (isset($system_config->settings['system_skypeout_on']) && $system_config->settings['system_skypeout_on'] == 1) {
            if (!empty($temp_array['PHONE_OFFICE']) && skype_formatted($temp_array['PHONE_OFFICE'])) {
                $temp_array['PHONE_OFFICE'] = '<a href="callto://' . $temp_array['PHONE_OFFICE'] . '">' . $temp_array['PHONE_OFFICE'] . '</a>';
            }
        }
        $temp_array["EMAIL1"] = $this->emailAddress->getPrimaryAddress($this);
        $this->email1 = $temp_array['EMAIL1'];
        $temp_array["EMAIL1_LINK"] = $current_user->getEmailLink('email1', $this, '', '', 'ListView');
        return $temp_array;
    }

    /**
      builds a generic search based on the query string using or
      do not include any $this-> because this is called on without having the class instantiated
     */
    function build_generic_where_clause($the_query_string) {
        $where_clauses = Array();
        $the_query_string = $this->db->quote($the_query_string);
        array_push($where_clauses, "accounts.name like '$the_query_string%'");
        if (is_numeric($the_query_string)) {
            array_push($where_clauses, "accounts.phone_alternate like '%$the_query_string%'");
            array_push($where_clauses, "accounts.phone_fax like '%$the_query_string%'");
            array_push($where_clauses, "accounts.phone_office like '%$the_query_string%'");
        }

        $the_where = "";
        foreach ($where_clauses as $clause) {
            if (!empty($the_where))
                $the_where .= " or ";
            $the_where .= $clause;
        }

        return $the_where;
    }

    function create_export_query(&$order_by, &$where, $relate_link_join='') {
        $custom_join = $this->custom_fields->getJOIN(true, true, $where);
        if ($custom_join)
            $custom_join['join'] .= $relate_link_join;
        $query = "SELECT
                                accounts.*,email_addresses.email_address email_address,
                                accounts.name as account_name,
                                users.user_name as assigned_user_name ";
        if ($custom_join) {
            $query .= $custom_join['select'];
        }
        $query .= " FROM accounts ";
        $query .= "LEFT JOIN users
	                                ON accounts.assigned_user_id=users.id ";

        //join email address table too.
        $query .= ' LEFT JOIN  email_addr_bean_rel on accounts.id = email_addr_bean_rel.bean_id and email_addr_bean_rel.bean_module=\'Accounts\' and email_addr_bean_rel.deleted=0 and email_addr_bean_rel.primary_address=1 ';
        $query .= ' LEFT JOIN email_addresses on email_addresses.id = email_addr_bean_rel.email_address_id ';

        if ($custom_join) {
            $query .= $custom_join['join'];
        }

        $where_auto = "( accounts.deleted IS NULL OR accounts.deleted=0 )";

        if ($where != "")
            $query .= "where ($where) AND " . $where_auto;
        else
            $query .= "where " . $where_auto;

        if (!empty($order_by))
            $query .= " ORDER BY " . $this->process_order_by($order_by, null);

        return $query;
    }

    function set_notification_body($xtpl, $account) {
        $xtpl->assign("ACCOUNT_NAME", $account->name);
        $xtpl->assign("ACCOUNT_TYPE", $account->account_type);
        $xtpl->assign("ACCOUNT_DESCRIPTION", $account->description);

        return $xtpl;
    }

    function bean_implements($interface) {
        switch ($interface) {
            case 'ACL':return true;
        }
        return false;
    }

    function get_unlinked_email_query($type=array()) {

        return get_unlinked_email_query($type, $this);
    }

    //For customizing all account subpanel
    //Dhaval
    function getActivityQuery() {
        $_REQUEST['table_names'] = array("tasks", "meetings", "calls");
        $query = "SELECT DISTINCT
                          tasks.id,
                          tasks.name,
                          tasks.status,
                          LTRIM(RTRIM(CONCAT(IFNULL(contacts.first_name,''),' ',IFNULL(contacts.last_name,''))))    contact_name,
                          tasks.contact_id,
                          ' '                       contact_name_owner,
                          ' '                       contact_name_mod,
                          tasks.date_due         AS date_start,
                          LTRIM(RTRIM(CONCAT(IFNULL(jt1.first_name,''),' ',IFNULL(jt1.last_name,'')))) AS signed_user_name,
                          tasks.assigned_user_id,
                          jt1.created_by         AS signed_user_name_owner,
                          'Users'                AS signed_user_name_mod,
                          tasks.created_by,
                          'tasks'                   panel_name
                        FROM tasks
                          LEFT JOIN contacts contacts
                            ON (tasks.contact_id = contacts.id OR tasks.parent_id = contacts.id)
                              AND contacts.deleted = 0
                           --   AND tasks.parent_type = 'Contacts'
                          LEFT JOIN users jt1
                            ON tasks.assigned_user_id = jt1.id
                              AND jt1.deleted = 0
                              AND jt1.deleted = 0
                          LEFT JOIN accounts tasks_rel
                            ON tasks.parent_id = tasks_rel.id
                              AND tasks_rel.deleted = 0
                              AND (tasks.parent_type = 'Accounts' OR tasks.parent_type = 'Contacts')
                          LEFT JOIN accounts_contacts
                            ON (accounts_contacts.contact_id = contacts.id
                                 OR accounts_contacts.account_id = tasks_rel.id)
                              AND accounts_contacts.deleted = 0
                        WHERE ((tasks.parent_id = '{$this->id}'
                                 OR accounts_contacts.account_id = '{$this->id}')
                               AND (tasks.status != 'Completed'
                                     AND tasks.status != 'Deferred'))
                            AND tasks.deleted = 0)
                        UNION ALL 
                        (SELECT DISTINCT
                              meetings.id,
                              meetings.name,
                              meetings.status,
                              ' '                          contact_name,
                              ' '                          contact_id,
                              ' '                          contact_name_owner,
                              ' '                          contact_name_mod,
                              meetings.date_start,
                              LTRIM(RTRIM(CONCAT(IFNULL(jt1.first_name,''),' ',IFNULL(jt1.last_name,'')))) AS signed_user_name,
                              meetings.assigned_user_id,
                              jt1.created_by            AS signed_user_name_owner,
                              'Users'                   AS signed_user_name_mod,
                              meetings.created_by,
                              'meetings'                   panel_name
                        FROM meetings
                          LEFT JOIN users jt1
                            ON meetings.assigned_user_id = jt1.id
                              AND jt1.deleted = 0
                              AND jt1.deleted = 0
                          LEFT JOIN accounts meetings_rel
                            ON meetings.parent_id = meetings_rel.id
                              AND meetings_rel.deleted = 0
                              AND (meetings.parent_type = 'Accounts' OR meetings.parent_type = 'Contacts')
                          LEFT JOIN meetings_contacts
                            ON meetings.id = meetings_contacts.meeting_id
                           --   AND meetings.parent_type = 'Contacts'
                              AND meetings_contacts.deleted = 0
                          LEFT JOIN accounts_contacts
                            ON (accounts_contacts.contact_id = meetings_contacts.contact_id
                                 OR accounts_contacts.account_id = meetings_rel.id)
                              AND accounts_contacts.deleted = 0
                        WHERE ((meetings.parent_id = '{$this->id}'
                                 OR accounts_contacts.account_id = '{$this->id}')
                               AND (meetings.status != 'Held'
                                     AND meetings.status != 'Not Held'))
                            AND meetings.deleted = 0) 
                        UNION ALL 
                        (SELECT DISTINCT
                              calls.id,
                              calls.name,
                              calls.status,
                              ' '                       contact_name,
                              ' '                       contact_id,
                              ' '                       contact_name_owner,
                              ' '                       contact_name_mod,
                              calls.date_start,
                              LTRIM(RTRIM(CONCAT(IFNULL(jt1.first_name,''),' ',IFNULL(jt1.last_name,'')))) AS signed_user_name,
                              calls.assigned_user_id,
                              jt1.created_by         AS signed_user_name_owner,
                              'Users'                AS signed_user_name_mod,
                              calls.created_by,
                              'calls'                   panel_name
                        FROM calls
                          LEFT JOIN users jt1
                            ON calls.assigned_user_id = jt1.id
                              AND jt1.deleted = 0
                              AND jt1.deleted = 0
                          LEFT JOIN accounts calls_rel
                            ON calls.parent_id = calls_rel.id
                              AND calls_rel.deleted = 0
                              AND (calls.parent_type = 'Accounts' OR calls.parent_type = 'Contacts')
                          LEFT JOIN calls_contacts
                            ON calls.id = calls_contacts.call_id
                           --   AND calls.parent_type = 'Contacts'
                              AND calls_contacts.deleted = 0
                          LEFT JOIN accounts_contacts
                            ON (accounts_contacts.contact_id = calls_contacts.contact_id
                                 OR accounts_contacts.account_id = calls_rel.id)
                              AND accounts_contacts.deleted = 0
                        WHERE ((calls.parent_id = '{$this->id}'
                                 OR accounts_contacts.account_id = '{$this->id}')
                               AND (calls.status != 'Held'
                                     AND calls.status != 'Not Held'))
                            AND calls.deleted = 0 ";
        return $query;
    }

    function getHistoryQuery() {
        $_REQUEST['table_names'] = array("tasks", "meetings", "calls", "notes", "emails", "emails");
        $query = "SELECT DISTINCT
                          tasks.id,
                          tasks.name,
                          tasks.status,
                          LTRIM(RTRIM(CONCAT(IFNULL(contacts.first_name,''),' ',IFNULL(contacts.last_name,''))))    contact_name,
                          tasks.contact_id,
                          ' '                       contact_name_owner,
                          ' '                       contact_name_mod,
                          tasks.date_modified,
                          tasks.date_entered,
                          LTRIM(RTRIM(CONCAT(IFNULL(jt1.first_name,''),' ',IFNULL(jt1.last_name,'')))) AS signed_user_name,
                          tasks.assigned_user_id,
                          jt1.created_by         AS signed_user_name_owner,
                          'Users'                AS signed_user_name_mod,
                          0                         reply_to_status,
                          tasks.parent_id,
                          tasks.parent_type,
                          ' '                       filename,
                          ' '                    AS signed_user_owner,
                          ' '                    AS signed_user_mod,
                          tasks.created_by,
                          'tasks'                   panel_name
                        FROM tasks
                          LEFT JOIN contacts contacts
                            ON (tasks.contact_id = contacts.id OR tasks.parent_id = contacts.id)
                              AND contacts.deleted = 0
                              AND contacts.deleted = 0
                           --   AND tasks.parent_type = 'Contacts'
                          LEFT JOIN users jt1
                            ON tasks.assigned_user_id = jt1.id
                              AND jt1.deleted = 0
                              AND jt1.deleted = 0
                          LEFT JOIN accounts tasks_rel
                            ON tasks.parent_id = tasks_rel.id
                              AND tasks_rel.deleted = 0
                              AND (tasks.parent_type = 'Accounts' OR tasks.parent_type = 'Contacts')
                          LEFT JOIN accounts_contacts
                            ON (accounts_contacts.contact_id = contacts.id
                                 OR accounts_contacts.account_id = tasks_rel.id)
                              AND accounts_contacts.deleted = 0
                        WHERE ((tasks.parent_id = '{$this->id}'
                                 OR accounts_contacts.account_id = '{$this->id}')
                               AND (tasks.status = 'Completed'
                                     OR tasks.status = 'Deferred'))
                            AND tasks.deleted = 0)
                        UNION ALL 
                        (SELECT DISTINCT
                          meetings.id,
                          meetings.name,
                          meetings.status,
                          ' '                          contact_name,
                          ' '                          contact_id,
                          ' '                          contact_name_owner,
                          ' '                          contact_name_mod,
                          meetings.date_modified,
                          meetings.date_entered,
                          LTRIM(RTRIM(CONCAT(IFNULL(jt1.first_name,''),' ',IFNULL(jt1.last_name,'')))) AS signed_user_name,
                          meetings.assigned_user_id,
                          jt1.created_by            AS signed_user_name_owner,
                          'Users'                   AS signed_user_name_mod,
                          0                            reply_to_status,
                          meetings.parent_id,
                          meetings.parent_type,
                          ' '                          filename,
                          ' '                       AS signed_user_owner,
                          ' '                       AS signed_user_mod,
                          meetings.created_by,
                          'meetings'                   panel_name
                        FROM meetings
                          LEFT JOIN users jt1
                            ON meetings.assigned_user_id = jt1.id
                              AND jt1.deleted = 0
                              AND jt1.deleted = 0
                          LEFT JOIN accounts meetings_rel
                            ON meetings.parent_id = meetings_rel.id
                              AND meetings_rel.deleted = 0
                              AND (meetings.parent_type = 'Accounts' OR meetings.parent_type = 'Contacts')
                          LEFT JOIN meetings_contacts
                            ON meetings.id = meetings_contacts.meeting_id
                           --   AND meetings.parent_type = 'Contacts'
                              AND meetings_contacts.deleted = 0
                          LEFT JOIN accounts_contacts
                            ON (accounts_contacts.contact_id = meetings_contacts.contact_id
                                 OR accounts_contacts.account_id = meetings_rel.id)
                              AND accounts_contacts.deleted = 0
                        WHERE ((meetings.parent_id = '{$this->id}'
                                 OR accounts_contacts.account_id = '{$this->id}')
                               AND (meetings.status = 'Held'
                                     OR meetings.status = 'Not Held'))
                            AND meetings.deleted = 0) 
                        UNION ALL 
                        (SELECT DISTINCT
                          calls.id,
                          calls.name,
                          calls.status,
                          ' '                       contact_name,
                          ' '                       contact_id,
                          ' '                       contact_name_owner,
                          ' '                       contact_name_mod,
                          calls.date_modified,
                          calls.date_entered,
                          LTRIM(RTRIM(CONCAT(IFNULL(jt1.first_name,''),' ',IFNULL(jt1.last_name,'')))) AS signed_user_name,
                          calls.assigned_user_id,
                          jt1.created_by         AS signed_user_name_owner,
                          'Users'                AS signed_user_name_mod,
                          0                         reply_to_status,
                          calls.parent_id,
                          calls.parent_type,
                          ' '                       filename,
                          ' '                    AS signed_user_owner,
                          ' '                    AS signed_user_mod,
                          calls.created_by,
                          'calls'                   panel_name
                        FROM calls
                          LEFT JOIN users jt1
                            ON calls.assigned_user_id = jt1.id
                              AND jt1.deleted = 0
                              AND jt1.deleted = 0
                          LEFT JOIN accounts calls_rel
                            ON calls.parent_id = calls_rel.id
                              AND calls_rel.deleted = 0
                              AND (calls.parent_type = 'Accounts' OR calls.parent_type = 'Contacts')
                          LEFT JOIN calls_contacts
                            ON calls.id = calls_contacts.call_id
                           --   AND calls.parent_type = 'Contacts'
                              AND calls_contacts.deleted = 0
                          LEFT JOIN accounts_contacts
                            ON (accounts_contacts.contact_id = calls_contacts.contact_id
                                 OR accounts_contacts.account_id = calls_rel.id)
                              AND accounts_contacts.deleted = 0
                        WHERE ((calls.parent_id = '{$this->id}'
                                 OR accounts_contacts.account_id = '{$this->id}')
                               AND (calls.status = 'Held'
                                     OR calls.status = 'Not Held'))
                            AND calls.deleted = 0) 
                        UNION ALL 
                        (SELECT DISTINCT
                          notes.id,
                          notes.name,
                          ' '                       STATUS,
                          LTRIM(RTRIM(CONCAT(IFNULL(contacts.first_name,''),' ',IFNULL(contacts.last_name,''))))    contact_name,
                          notes.contact_id,
                          ' '                       contact_name_owner,
                          ' '                       contact_name_mod,
                          notes.date_modified,
                          notes.date_entered,
                          LTRIM(RTRIM(CONCAT(IFNULL(jt1.first_name,''),' ',IFNULL(jt1.last_name,'')))) AS signed_user_name,
                          notes.assigned_user_id,
                          jt1.created_by         AS signed_user_name_owner,
                          'Users'                AS signed_user_name_mod,
                          0                         reply_to_status,
                          notes.parent_id,
                          notes.parent_type,
                          notes.filename,
                          ' '                    AS signed_user_owner,
                          ' '                    AS signed_user_mod,
                          notes.created_by,
                          'notes'                   panel_name
                        FROM notes
                          LEFT JOIN contacts contacts
                            ON (notes.contact_id = contacts.id
                                 OR notes.parent_id = contacts.id)
                              AND contacts.deleted = 0
                         --     AND notes.parent_type = 'Contacts'
                          LEFT JOIN users jt1
                            ON notes.assigned_user_id = jt1.id
                              AND jt1.deleted = 0
                              AND jt1.deleted = 0
                          LEFT JOIN accounts notes_rel
                            ON notes.parent_id = notes_rel.id
                              AND notes_rel.deleted = 0
                              AND (notes.parent_type = 'Accounts' OR notes.parent_type = 'Contacts')
                              AND notes_rel.id = '{$this->id}'
                          LEFT JOIN accounts_contacts
                            ON (accounts_contacts.contact_id = contacts.id
                                 OR accounts_contacts.account_id = notes_rel.id)
                              AND accounts_contacts.deleted = 0
                        WHERE (notes.parent_id = '{$this->id}'
                                OR accounts_contacts.account_id = '{$this->id}')
                            AND notes.deleted = 0) 
                        UNION ALL 
                        (SELECT DISTINCT
                          emails.id,
                          emails.name,
                          emails.status,
                          ' '                        contact_name,
                          ' '                        contact_id,
                          ' '                        contact_name_owner,
                          ' '                        contact_name_mod,
                          emails.date_modified,
                          emails.date_entered,
                          LTRIM(RTRIM(CONCAT(IFNULL(jt0.first_name,''),' ',IFNULL(jt0.last_name,'')))) AS signed_user_name,
                          emails.assigned_user_id,
                          jt0.created_by          AS signed_user_name_owner,
                          'Users'                 AS signed_user_name_mod,
                          emails.reply_to_status,
                          emails.parent_id,
                          emails.parent_type,
                          ' '                        filename,
                          ' '                     AS signed_user_owner,
                          ' '                     AS signed_user_mod,
                          ' '                        created_by,
                          'emails'                   panel_name
                        FROM emails
                          LEFT JOIN users jt0
                            ON emails.assigned_user_id = jt0.id
                              AND jt0.deleted = 0
                          INNER JOIN emails_beans
                            ON emails.id = emails_beans.email_id
                              AND emails_beans.deleted = 0
                              AND (emails_beans.bean_module = 'Accounts'
                                    OR emails_beans.bean_module = 'Contacts')
                          LEFT JOIN accounts_contacts
                            ON (accounts_contacts.contact_id = emails_beans.bean_id
                                 OR accounts_contacts.account_id = emails_beans.bean_id)
                              AND accounts_contacts.deleted = 0
                        WHERE (emails_beans.bean_id = '{$this->id}'
                                OR accounts_contacts.account_id = '{$this->id}')
                            AND emails.deleted = 0) 
                        UNION ALL 
                        (SELECT DISTINCT
                          emails.id,
                          emails.name,
                          emails.status,
                          ' '                        contact_name,
                          ' '                        contact_id,
                          ' '                        contact_name_owner,
                          ' '                        contact_name_mod,
                          emails.date_modified,
                          emails.date_entered,
                          LTRIM(RTRIM(CONCAT(IFNULL(jt0.first_name,''),' ',IFNULL(jt0.last_name,'')))) AS signed_user_name,
                          emails.assigned_user_id,
                          jt0.created_by          AS signed_user_name_owner,
                          'Users'                 AS signed_user_name_mod,
                          emails.reply_to_status,
                          emails.parent_id,
                          emails.parent_type,
                          ' '                        filename,
                          ' '                     AS signed_user_owner,
                          ' '                     AS signed_user_mod,
                          emails.created_by,
                          'linkedemails'             panel_name
                        FROM emails
                          LEFT JOIN users jt0
                            ON emails.assigned_user_id = jt0.id
                              AND jt0.deleted = 0
                          JOIN (SELECT DISTINCT
                                  email_id
                                FROM emails_email_addr_rel eear
                                  JOIN email_addr_bean_rel eabr
                                    ON (eabr.bean_module = 'Accounts'
                                         OR eabr.bean_module = 'Contacts')
                                      AND eabr.email_address_id = eear.email_address_id
                                      AND eabr.deleted = 0
                                WHERE eear.deleted = 0
                                    AND eear.email_id NOT IN(SELECT
                                                               eb.email_id
                                                             FROM emails_beans eb
                                                               LEFT JOIN accounts_contacts
                                                                 ON (accounts_contacts.contact_id = eb.bean_id
                                                                      OR accounts_contacts.account_id = eb.bean_id)
                                                                   AND accounts_contacts.deleted = 0
                                                             WHERE eb.deleted = 0
                                                                AND (eb.bean_module = 'Accounts'
                                                                     OR eb.bean_module = 'Contacts')
                                                                 AND (eb.bean_id = '{$this->id}'
                                                                       OR accounts_contacts.account_id = '{$this->id}'))) derivedemails
                            ON derivedemails.email_id = emails.id
                        WHERE emails.deleted = 0";
        return $query;
    }

    function getDocumentQuery() {
        $_REQUEST['table_names'] = array("documents");
        $query = "SELECT DISTINCT
                      documents.id,
                      documents.document_name,
                      documents.document_revision_id,
                      documents.category_id,
                      documents.status_id,
                      documents.active_date,
                      documents.assigned_user_id,
                      'documents'                       panel_name
                    FROM documents
                      LEFT JOIN documents_accounts
                        ON documents.id = documents_accounts.document_id
                          AND documents_accounts.account_id = '{$this->id}'
                          AND documents_accounts.deleted = 0
                      LEFT JOIN documents_contacts
                        ON documents.id = documents_contacts.document_id
                          AND documents_contacts.deleted = 0
                      LEFT JOIN accounts_contacts
                        ON (accounts_contacts.contact_id = documents_contacts.contact_id
                             OR accounts_contacts.account_id = documents_accounts.account_id)
                          AND accounts_contacts.deleted = 0
                    WHERE documents.deleted = 0
                        AND accounts_contacts.account_id = '{$this->id}'";
        return $query;
    }

    function getOpportunityQuery() {
        $_REQUEST['table_names'] = array("opportunities");
        $query = "SELECT DISTINCT
                      opportunities.id,
                      opportunities.name,
                      opportunities.sales_stage,
                      opportunities.date_closed,
                      opportunities.amount_usdollar,
                      LTRIM(RTRIM(CONCAT(IFNULL(jt0.first_name,''),' ',IFNULL(jt0.last_name,'')))) AS signed_user_name,
                      opportunities.assigned_user_id,
                      jt0.created_by                 AS signed_user_name_owner,
                      'Users'                        AS signed_user_name_mod,
                      opportunities.currency_id,
                      opportunities.created_by,
                      'opportunities'                   panel_name
                    FROM opportunities
                      LEFT JOIN opportunities_cstm
                        ON opportunities.id = opportunities_cstm.id_c
                      LEFT JOIN users jt0
                        ON opportunities.assigned_user_id = jt0.id
                          AND jt0.deleted = 0
                          AND jt0.deleted = 0
                      LEFT JOIN accounts_opportunities
                        ON opportunities.id = accounts_opportunities.opportunity_id
                          AND accounts_opportunities.account_id = '{$this->id}'
                          AND accounts_opportunities.deleted = 0
                      LEFT JOIN opportunities_contacts
                        ON opportunities.id = opportunities_contacts.opportunity_id
                          AND opportunities_contacts.deleted = 0
                      LEFT JOIN accounts_contacts
                        ON (accounts_contacts.contact_id = opportunities_contacts.contact_id
                             OR accounts_contacts.account_id = accounts_opportunities.account_id)
                          AND accounts_contacts.deleted = 0
                    WHERE opportunities.deleted = 0
                        AND accounts_contacts.account_id = '{$this->id}'";
        return $query;
    }

    function getCampaignQuery() {
        //in beta do not use
        $_REQUEST['table_names'] = array("campaign_log");
        $query = "SELECT DISTINCT
                      campaign_log.id,
                      jt0.name                      campaign_name1,
                      campaign_log.campaign_id,
                      jt0.assigned_user_id          campaign_name1_owner,
                      'Campaigns'                   campaign_name1_mod,
                      campaign_log.activity_type,
                      campaign_log.activity_date,
                      campaign_log.related_id,
                      campaign_log.related_type,
                      campaign_log.target_id,
                      campaign_log.target_type,
                      'campaigns'                   panel_name
                    FROM campaign_log
                      LEFT JOIN campaigns jt0
                        ON campaign_log.campaign_id = jt0.id
                          AND jt0.deleted = 0
                          AND jt0.deleted = 0
                      LEFT JOIN accounts campaigns_rel
                        ON campaign_log.target_id = campaigns_rel.id
                          AND campaigns_rel.id = '{$this->id}'
                          AND campaigns_rel.deleted = 0
                      LEFT JOIN contacts campaigns_rel_rel
                        ON campaign_log.target_id = campaigns_rel_rel.id
                          AND campaigns_rel_rel.deleted = 0
                      LEFT JOIN accounts_contacts
                        ON (accounts_contacts.contact_id = campaigns_rel_rel.id
                             OR accounts_contacts.account_id = campaigns_rel.id)
                          AND accounts_contacts.deleted = 0
                    WHERE (campaign_log.target_id = '{$this->id}'
                            OR accounts_contacts.account_id = '{$this->id}')
                        AND campaign_log.deleted = 0";
        return $query;
    }

    function getLeadQuery() {
        $_REQUEST['table_names'] = array("leads");
        $query = "SELECT DISTINCT
                      leads.id,
                      leads.first_name,
                      leads.last_name,
                      leads.salutation,
                      LTRIM(RTRIM(CONCAT(IFNULL(leads.first_name,''),' ',IFNULL(leads.last_name,'')))) AS NAME,
                      leads.refered_by,
                      leads.lead_source,
                      leads.phone_work,
                      leads.lead_source_description,
                      LTRIM(RTRIM(CONCAT(IFNULL(jt0.first_name,''),' ',IFNULL(jt0.last_name,'')))) AS signed_user_name,
                      leads.assigned_user_id,
                      jt0.created_by                AS signed_user_name_owner,
                      'Users'                       AS signed_user_name_mod,
                      leads.created_by,
                      leads.account_name,
                      'leads'                          panel_name
                    FROM leads
                      LEFT JOIN users jt0
                        ON leads.assigned_user_id = jt0.id
                          AND jt0.deleted = 0
                          AND jt0.deleted = 0
                      LEFT JOIN accounts leads_rel
                        ON leads.account_id = leads_rel.id
                          AND leads_rel.deleted = 0
                      LEFT JOIN contacts leads_rel_con
                        ON leads.contact_id = leads_rel_con.id
                          AND leads_rel_con.deleted = 0
                      LEFT JOIN accounts_contacts
                        ON (accounts_contacts.contact_id = leads.contact_id
                             OR accounts_contacts.account_id = leads.account_id)
                          AND accounts_contacts.deleted = 0
                    WHERE (leads.account_id = '{$this->id}'
                            OR accounts_contacts.account_id = '{$this->id}')
                        AND leads.deleted = 0";
        return $query;
    }

    function getBugQuery() {
        $_REQUEST['table_names'] = array("bugs");
        $query = "SELECT DISTINCT
                      bugs.id,
                      bugs.bug_number,
                      bugs.name,
                      bugs.status,
                      bugs.type,
                      bugs.priority,
                      LTRIM(RTRIM(CONCAT(IFNULL(jt0.first_name,''),' ',IFNULL(jt0.last_name,'')))) AS signed_user_name,
                      bugs.assigned_user_id,
                      jt0.created_by        AS signed_user_name_owner,
                      'Users'               AS signed_user_name_mod,
                      bugs.created_by,
                      'bugs'                   panel_name
                    FROM bugs
                      LEFT JOIN users jt0
                        ON bugs.assigned_user_id = jt0.id
                          AND jt0.deleted = 0
                          AND jt0.deleted = 0
                      LEFT JOIN accounts_bugs
                        ON bugs.id = accounts_bugs.bug_id
                          AND accounts_bugs.account_id = '{$this->id}'
                          AND accounts_bugs.deleted = 0
                      LEFT JOIN contacts_bugs
                        ON bugs.id = contacts_bugs.bug_id
                          AND contacts_bugs.deleted = 0
                      LEFT JOIN accounts_contacts
                        ON (accounts_contacts.contact_id = contacts_bugs.contact_id
                             OR accounts_contacts.account_id = accounts_bugs.account_id)
                          AND accounts_contacts.deleted = 0
                    WHERE bugs.deleted = 0
                        AND accounts_contacts.account_id = '{$this->id}'";
        return $query;
    }

    function getCaseQuery() {
        $_REQUEST['table_names'] = array("cases");
        $query = "SELECT DISTINCT
                      cases.id,
                      cases.case_number,
                      cases.name,
                      cases.status,
                      accounts.name                account_name,
                      cases.account_id,
                      accounts.assigned_user_id    account_name_owner,
                      'Accounts'                   account_name_mod,
                      cases.priority,
                      cases.date_entered,
                      LTRIM(RTRIM(CONCAT(IFNULL(jt1.first_name,''),' ',IFNULL(jt1.last_name,'')))) AS signed_user_name,
                      cases.assigned_user_id,
                      jt1.created_by            AS signed_user_name_owner,
                      'Users'                   AS signed_user_name_mod,
                      cases.created_by,
                      'cases'                      panel_name
                    FROM cases
                      LEFT JOIN cases_cstm
                        ON cases.id = cases_cstm.id_c
                      LEFT JOIN accounts accounts
                        ON cases.account_id = accounts.id
                          AND accounts.deleted = 0
                      LEFT JOIN users jt1
                        ON cases.assigned_user_id = jt1.id
                          AND jt1.deleted = 0
                          AND jt1.deleted = 0
                      LEFT JOIN contacts_cases
                        ON cases.id = contacts_cases.case_id
                          AND contacts_cases.deleted = 0
                      LEFT JOIN accounts_contacts
                        ON (accounts_contacts.contact_id = contacts_cases.contact_id
                             OR cases.account_id = accounts_contacts.account_id)
                          AND accounts_contacts.deleted = 0
                    WHERE (cases.account_id = '{$this->id}'
                            OR accounts_contacts.account_id = '{$this->id}')
                        AND cases.deleted = 0";
        return $query;
    }

    function getProjectQuery() {
        $_REQUEST['table_names'] = array("project");
        $query = "SELECT DISTINCT
                      project.id,
                      project.name,
                      LTRIM(RTRIM(CONCAT(IFNULL(jt0.first_name,''),' ',IFNULL(jt0.last_name,'')))) AS signed_user_name,
                      project.assigned_user_id,
                      jt0.created_by               AS signed_user_name_owner,
                      'Users'                      AS signed_user_name_mod,
                      project.estimated_start_date,
                      project.estimated_end_date,
                      project.created_by,
                      'project'                       panel_name
                    FROM project
                      LEFT JOIN users jt0
                        ON project.assigned_user_id = jt0.id
                          AND jt0.deleted = 0
                          AND jt0.deleted = 0
                      LEFT JOIN projects_accounts
                        ON project.id = projects_accounts.project_id
                          AND projects_accounts.account_id = '{$this->id}'
                          AND projects_accounts.deleted = 0
                      LEFT JOIN projects_contacts
                        ON project.id = projects_contacts.project_id
                          AND projects_contacts.deleted = 0
                      LEFT JOIN accounts_contacts
                        ON (accounts_contacts.contact_id = projects_contacts.contact_id
                             OR accounts_contacts.account_id = projects_accounts.account_id)
                          AND accounts_contacts.deleted = 0
                    WHERE project.deleted = 0
                        AND accounts_contacts.account_id = '{$this->id}'";
        return $query;
    }

    function create_list_count_query($query) {
        if (isset($_REQUEST['table_names'])) {
            // remove the 'order by' clause which is expected to be at the end of the query
            $pattern = '/\sORDER BY.*/is';  // ignores the case
            $replacement = '';
            $query = preg_replace($pattern, $replacement, $query);
            //handle distinct clause
            $star = '*';
            if (substr_count(strtolower($query), 'distinct')) {
                if (isset($_REQUEST['table_names']) && $_REQUEST['table_names'][0] != "")
                    $star = 'DISTINCT ' . $_REQUEST['table_names'][0] . '.id';
                else
                    $star = 'DISTINCT ' . $this->table_name . '.id';
            }

            // change the select expression to 'count(*)'
            $pattern = '/SELECT(.*?)(\s){1}FROM(\s){1}/is';  // ignores the case
            $replacement = 'SELECT count(' . $star . ') c FROM ';

            //if the passed query has union clause then replace all instances of the pattern.
            //this is very rare. I have seen this happening only from projects module.
            //in addition to this added a condition that has  union clause and uses
            //sub-selects.
            if (strstr($query, " UNION ALL ") !== false) {

                //seperate out all the queries.
                $union_qs = explode(" UNION ALL ", $query);
                foreach ($union_qs as $key => $union_query) {
                    $star = '*';
                    preg_match($pattern, $union_query, $matches);
                    if (!empty($matches)) {
                        if (stristr($matches[0], "distinct")) {
                            if (isset($_REQUEST['table_names']) && $_REQUEST['table_names'][$key] != "")
                                $star = 'DISTINCT ' . $_REQUEST['table_names'][$key] . '.id';
                            else
                                $star = 'DISTINCT ' . $this->table_name . '.id';
                        }
                    } // if
                    $replacement = 'SELECT count(' . $star . ') c FROM ';
                    $union_qs[$key] = preg_replace($pattern, $replacement, $union_query, 1);
                }
                $modified_select_query = implode(" UNION ALL ", $union_qs);
            } else {
                $modified_select_query = preg_replace($pattern, $replacement, $query, 1);
            }

            unset($_REQUEST['table_names']);
            return $modified_select_query;
        } else {
            return parent::create_list_count_query($query);
        }
    }

    //End - Dhaval
}