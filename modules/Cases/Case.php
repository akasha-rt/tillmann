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

 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 * ****************************************************************************** */

// Case is used to store customer information.
class aCase extends Basic {

    var $field_name_map = array();
    // Stored fields
    var $id;
    var $date_entered;
    var $date_modified;
    var $modified_user_id;
    var $assigned_user_id;
    var $case_number;
    var $resolution;
    var $description;
    var $name;
    var $status;
    var $priority;
    var $created_by;
    var $created_by_name;
    var $modified_by_name;
    // These are related
    var $bug_id;
    var $account_name;
    var $account_id;
    var $contact_id;
    var $task_id;
    var $note_id;
    var $meeting_id;
    var $call_id;
    var $email_id;
    var $assigned_user_name;
    var $account_name1;
    var $table_name = "cases";
    var $rel_account_table = "accounts_cases";
    var $rel_contact_table = "contacts_cases";
    var $module_dir = 'Cases';
    var $object_name = "Case";
    var $importable = true;

    /** "%1" is the case_number, for emails
     * leave the %1 in if you customize this
     * YOU MUST LEAVE THE BRACKETS AS WELL */
    var $emailSubjectMacro = '[CASE:%1]';
    // This is used to retrieve related fields from form posts.
    var $additional_column_fields = Array('bug_id', 'assigned_user_name', 'assigned_user_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id');
    var $relationship_fields = Array('account_id' => 'accounts', 'bug_id' => 'bugs',
        'task_id' => 'tasks', 'note_id' => 'notes',
        'meeting_id' => 'meetings', 'call_id' => 'calls', 'email_id' => 'emails',
    );

    function aCase() {
        parent::SugarBean();
        global $sugar_config;
        if (!$sugar_config['require_accounts']) {
            unset($this->required_fields['account_name']);
        }
        $this->setupCustomFields('Cases');
        foreach ($this->field_defs as $field) {
            $this->field_name_map[$field['name']] = $field;
        }
    }

    var $new_schema = true;

    /**
     * To customize History subpanel to add Email address for Emails
     * @author Dhaval Darji
     */
    function getEmailAdd() {
        $sql = "SELECT
                      meetings.id,
                      meetings.name,
                      meetings.status,
                      ' '     from_addr_name,
                      ' '                          contact_name,
                      ' '                          contact_id,
                      meetings.date_modified,
                      meetings.date_entered,
                      LTRIM(RTRIM(CONCAT(IFNULL(jt1.first_name,''),' ',IFNULL(jt1.last_name,'')))) AS signed_user_name,
                      meetings.assigned_user_id,
                      jt1.created_by            AS signed_user_name_owner,
                      'Users'                   AS signed_user_name_mod,
                      0                            reply_to_status,
                      ' '                          contact_name_owner,
                      ' '                          contact_name_mod,
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
                      INNER JOIN cases meetings_rel
                        ON meetings.parent_id = meetings_rel.id
                          AND meetings_rel.deleted = 0
                          AND meetings.parent_type = 'Cases'
                    WHERE (meetings.parent_id = '{$this->id}'
                           AND (meetings.status = 'Held'
                                 OR meetings.status = 'Not Held'))
                        AND meetings.deleted = 0) 
                     UNION ALL 
                    (SELECT
                      tasks.id,
                      tasks.name,
                      tasks.status,
                      ' '     from_addr_name,
                      LTRIM(RTRIM(CONCAT(IFNULL(contacts.first_name,''),' ',IFNULL(contacts.last_name,''))))    contact_name,
                      tasks.contact_id,
                      tasks.date_modified,
                      tasks.date_entered,
                      LTRIM(RTRIM(CONCAT(IFNULL(jt1.first_name,''),' ',IFNULL(jt1.last_name,'')))) AS signed_user_name,
                      tasks.assigned_user_id,
                      jt1.created_by         AS signed_user_name_owner,
                      'Users'                AS signed_user_name_mod,
                      0                         reply_to_status,
                      ' '                       contact_name_owner,
                      ' '                       contact_name_mod,
                      tasks.parent_id,
                      tasks.parent_type,
                      ' '                       filename,
                      ' '                    AS signed_user_owner,
                      ' '                    AS signed_user_mod,
                      tasks.created_by,
                      'tasks'                   panel_name
                    FROM tasks
                      LEFT JOIN contacts contacts
                        ON tasks.contact_id = contacts.id
                          AND contacts.deleted = 0
                          AND contacts.deleted = 0
                      LEFT JOIN users jt1
                        ON tasks.assigned_user_id = jt1.id
                          AND jt1.deleted = 0
                          AND jt1.deleted = 0
                      INNER JOIN cases tasks_rel
                        ON tasks.parent_id = tasks_rel.id
                          AND tasks_rel.deleted = 0
                          AND tasks.parent_type = 'Cases'
                    WHERE (tasks.parent_id = '{$this->id}'
                           AND (tasks.status = 'Completed'
                                 OR tasks.status = 'Deferred'))
                        AND tasks.deleted = 0) 
                     UNION ALL  
                    (SELECT
                      calls.id,
                      calls.name,
                      calls.status,
                      ' '     from_addr_name,
                      ' '                       contact_name,
                      ' '                       contact_id,
                      calls.date_modified,
                      calls.date_entered,
                      LTRIM(RTRIM(CONCAT(IFNULL(jt1.first_name,''),' ',IFNULL(jt1.last_name,'')))) AS signed_user_name,
                      calls.assigned_user_id,
                      jt1.created_by         AS signed_user_name_owner,
                      'Users'                AS signed_user_name_mod,
                      0                         reply_to_status,
                      ' '                       contact_name_owner,
                      ' '                       contact_name_mod,
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
                      INNER JOIN cases calls_rel
                        ON calls.parent_id = calls_rel.id
                          AND calls_rel.deleted = 0
                          AND calls.parent_type = 'Cases'
                    WHERE (calls.parent_id = '{$this->id}'
                           AND (calls.status = 'Held'
                                 OR calls.status = 'Not Held'))
                        AND calls.deleted = 0) 
                     UNION ALL  
                    (SELECT
                      notes.id,
                      notes.name,
                      ' '                       STATUS,
                      ' '     from_addr_name,
                      LTRIM(RTRIM(CONCAT(IFNULL(contacts.first_name,''),' ',IFNULL(contacts.last_name,''))))    contact_name,
                      notes.contact_id,
                      notes.date_modified,
                      notes.date_entered,
                      LTRIM(RTRIM(CONCAT(IFNULL(jt1.first_name,''),' ',IFNULL(jt1.last_name,'')))) AS signed_user_name,
                      notes.assigned_user_id,
                      jt1.created_by         AS signed_user_name_owner,
                      'Users'                AS signed_user_name_mod,
                      0                         reply_to_status,
                      ' '                       contact_name_owner,
                      ' '                       contact_name_mod,
                      notes.parent_id,
                      notes.parent_type,
                      notes.filename,
                      ' '                    AS signed_user_owner,
                      ' '                    AS signed_user_mod,
                      notes.created_by,
                      'notes'                   panel_name
                    FROM notes
                      LEFT JOIN contacts contacts
                        ON notes.contact_id = contacts.id
                          AND contacts.deleted = 0
                          AND contacts.deleted = 0
                      LEFT JOIN users jt1
                        ON notes.assigned_user_id = jt1.id
                          AND jt1.deleted = 0
                          AND jt1.deleted = 0
                      INNER JOIN cases notes_rel
                        ON notes.parent_id = notes_rel.id
                          AND notes_rel.deleted = 0
                          AND notes.parent_type = 'Cases'
                    WHERE (notes.parent_id = '{$this->id}')
                        AND notes.deleted = 0) 
                     UNION ALL 
                    (SELECT
                      emails.id,
                      emails.name,
                      emails.status,
                      emails_text.from_addr      from_addr_name,
                      ' '                        contact_name,
                      ' '                        contact_id,
                      emails.date_modified,
                      emails.date_entered,
                      LTRIM(RTRIM(CONCAT(IFNULL(jt0.first_name,''),' ',IFNULL(jt0.last_name,'')))) AS signed_user_name,
                      emails.assigned_user_id,
                      jt0.created_by          AS signed_user_name_owner,
                      'Users'                 AS signed_user_name_mod,
                      emails.reply_to_status,
                      ' '                        contact_name_owner,
                      ' '                        contact_name_mod,
                      emails.parent_id,
                      emails.parent_type,
                      ' '                        filename,
                      ' '                     AS signed_user_owner,
                      ' '                     AS signed_user_mod,
                      emails.created_by,
                      'emails'                   panel_name
                    FROM emails
                      LEFT JOIN users jt0
                        ON emails.assigned_user_id = jt0.id
                          AND jt0.deleted = 0
                          AND jt0.deleted = 0
                      INNER JOIN emails_beans
                        ON emails.id = emails_beans.email_id                    
                          AND emails_beans.bean_id = '{$this->id}'
                          AND emails_beans.deleted = 0
                          AND emails_beans.bean_module = 'Cases'
                      LEFT JOIN emails_text
                        ON emails_text.email_id = emails_beans.email_id
                          AND emails_text.deleted = 0
                    WHERE emails.deleted = 0
                    ";
        return $sql;
    }

    function get_summary_text() {
        return "$this->name";
    }

    function listviewACLHelper() {
        $array_assign = parent::listviewACLHelper();
        $is_owner = false;
        if (!empty($this->account_id)) {

            if (!empty($this->account_id_owner)) {
                global $current_user;
                $is_owner = $current_user->id == $this->account_id_owner;
            }
        }
        if (!ACLController::moduleSupportsACL('Accounts') || ACLController::checkAccess('Accounts', 'view', $is_owner)) {
            $array_assign['ACCOUNT'] = 'a';
        } else {
            $array_assign['ACCOUNT'] = 'span';
        }

        return $array_assign;
    }

    function save_relationship_changes($is_update) {
        parent::save_relationship_changes($is_update);

        if (!empty($this->contact_id)) {
            $this->set_case_contact_relationship($this->contact_id);
        }
    }

    function set_case_contact_relationship($contact_id) {
        global $app_list_strings;
        $default = $app_list_strings['case_relationship_type_default_key'];
        $this->load_relationship('contacts');
        $this->contacts->add($contact_id, array('contact_role' => $default));
    }

    function fill_in_additional_list_fields() {
        parent::fill_in_additional_list_fields();
        /* // Fill in the assigned_user_name
          //$this->assigned_user_name = get_assigned_user_name($this->assigned_user_id);

          $account_info = $this->getAccount($this->id);
          $this->account_name = $account_info['account_name'];
          $this->account_id = $account_info['account_id']; */
    }

    function fill_in_additional_detail_fields() {
        parent::fill_in_additional_detail_fields();
        // Fill in the assigned_user_name
        $this->assigned_user_name = get_assigned_user_name($this->assigned_user_id);

        $this->created_by_name = get_assigned_user_name($this->created_by);
        $this->modified_by_name = get_assigned_user_name($this->modified_user_id);

        if (!empty($this->id)) {
            $account_info = $this->getAccount($this->id);
            if (!empty($account_info)) {
                $this->account_name = $account_info['account_name'];
                $this->account_id = $account_info['account_id'];
            }
        }
    }

    /** Returns a list of the associated contacts
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     */
    function get_contacts() {
        $this->load_relationship('contacts');
        $query_array = $this->contacts->getQuery(true);

        //update the select clause in the retruned query.
        $query_array['select'] = "SELECT contacts.id, contacts.first_name, contacts.last_name, contacts.title, contacts.email1, contacts.phone_work, contacts_cases.contact_role as case_role, contacts_cases.id as case_rel_id ";

        $query = '';
        foreach ($query_array as $qstring) {
            $query.=' ' . $qstring;
        }
        $temp = Array('id', 'first_name', 'last_name', 'title', 'email1', 'phone_work', 'case_role', 'case_rel_id');
        return $this->build_related_list2($query, new Contact(), $temp);
    }
    function create_new_list_query($order_by, $where, $filter = array(), $params = array(), $show_deleted = 0, $join_type = '', $return_array = false, $parentbean = null, $singleSelect = false) {
        if ($order_by == '' || empty($order_by)) {
            $order_by = 'date_entered DESC';
        }
        $ret_array = parent::create_new_list_query($order_by, $where, $filter, $params, $show_deleted, $join_type, true, $parentbean, $singleSelect);
        $ret_array['select'] .= ', "" as follow_button_c ';
        if (!$return_array)
            return $ret_array['select'] . $ret_array['from'] . $ret_array['where'] . $ret_array['order_by'];
        return $ret_array;
    }

    function get_list_view_data() {
        global $current_language,$current_user;
        $app_list_strings = return_app_list_strings_language($current_language);

        $temp_array = $this->get_list_view_array();
        $temp_array['NAME'] = (($this->name == "") ? "<em>blank</em>" : $this->name);
        $temp_array['PRIORITY'] = empty($this->priority) ? "" : $app_list_strings['case_priority_dom'][$this->priority];
        $temp_array['STATUS'] = empty($this->status) ? "" : $app_list_strings['case_status_dom'][$this->status];
        $temp_array['ENCODED_NAME'] = $this->name;
        $temp_array['CASE_NUMBER'] = $this->case_number;
        global $current_user,$db;
        $follow_result = $db->query("SELECT id from followup where module_id='{$temp_array['ID']}' and deleted=0 and module_name='Cases' and user_id='{$current_user->id}'");
        $follow_row = $db->fetchByAssoc($follow_result);
        if($follow_row)
            $temp_array['FOLLOW_BUTTON_C'] = '<img src="custom/image/follow2.png" style="height:17px;width:20px;cursor:pointer;" id="'.$temp_array['ID'].'" onclick="addToWatchList(this,\''.$current_user->id.'\',\'Cases\');" title="Remove from Watch List" />';
        else
            $temp_array['FOLLOW_BUTTON_C'] = '<img src="custom/image/follow1.png" style="height:17px;width:20px;cursor:pointer;" id="'.$temp_array['ID'].'" onclick="addToWatchList(this,\''.$current_user->id.'\',\'Cases\');" title="Add to Watch List" />';
        $temp_array['SET_COMPLETE'] = "<a href='index.php?return_module=Home&return_action=index&action=EditView&module=Cases&record=$this->id&status=Closed'>" . SugarThemeRegistry::current()->getImage("close_inline", "title=" . translate('LBL_LIST_CLOSE', 'Cases') . " border='0'", null, null, '.gif', translate('LBL_LIST_CLOSE', 'Cases')) . "</a>";
        //$temp_array['ACCOUNT_NAME'] = $this->account_name; //overwrites the account_name value returned from the cases table.
        return $temp_array;
    }

    /**
      builds a generic search based on the query string using or
      do not include any $this-> because this is called on without having the class instantiated
     */
    function build_generic_where_clause($the_query_string) {
        $where_clauses = Array();
        $the_query_string = $this->db->quote($the_query_string);
        array_push($where_clauses, "cases.name like '$the_query_string%'");
        array_push($where_clauses, "accounts.name like '$the_query_string%'");

        if (is_numeric($the_query_string))
            array_push($where_clauses, "cases.case_number like '$the_query_string%'");

        $the_where = "";

        foreach ($where_clauses as $clause) {
            if ($the_where != "")
                $the_where .= " or ";
            $the_where .= $clause;
        }

        if ($the_where != "") {
            $the_where = "(" . $the_where . ")";
        }

        return $the_where;
    }

    function set_notification_body($xtpl, $case) {
        global $app_list_strings;

        $xtpl->assign("CASE_SUBJECT", $case->name);
        $xtpl->assign("CASE_PRIORITY", (isset($case->priority) ? $app_list_strings['case_priority_dom'][$case->priority] : ""));
        $xtpl->assign("CASE_STATUS", (isset($case->status) ? $app_list_strings['case_status_dom'][$case->status] : ""));
        $xtpl->assign("CASE_DESCRIPTION", $case->description);

        return $xtpl;
    }

    function bean_implements($interface) {
        switch ($interface) {
            case 'ACL':return true;
        }
        return false;
    }

    function save($check_notify = FALSE) {
        return parent::save($check_notify);
    }

    /**
     * retrieves the Subject line macro for InboundEmail parsing
     * @return string
     */
    function getEmailSubjectMacro() {
        global $sugar_config;
        return (isset($sugar_config['inbound_email_case_subject_macro']) && !empty($sugar_config['inbound_email_case_subject_macro'])) ?
                $sugar_config['inbound_email_case_subject_macro'] : $this->emailSubjectMacro;
    }

    function getAccount($case_id) {
        if (empty($case_id))
            return array();
        $ret_array = array();
        $query = "SELECT acc.id, acc.name from accounts  acc, cases  where acc.id = cases.account_id and cases.id = '" . $case_id . "' and cases.deleted=0 and acc.deleted=0";
        $result = $this->db->query($query, true, " Error filling in additional detail fields: ");

        // Get the id and the name.
        $row = $this->db->fetchByAssoc($result);

        if ($row != null) {
            $ret_array['account_name'] = stripslashes($row['name']);
            $ret_array['account_id'] = $row['id'];
        } else {
            $ret_array['account_name'] = '';
            $ret_array['account_id'] = '';
        }
        return $ret_array;
    }

}

?>
