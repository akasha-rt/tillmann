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




require_once('include/Dashlets/DashletGeneric.php');

class FollowUpCasesDashlet extends DashletGeneric {

    var $display_row;
    var $deshlate_id;
    var $myItem;

    function FollowUpCasesDashlet($id, $def = null) {
        global $current_user, $app_strings;
        //require_once('custom/modules/Home/Dashlets/FollowUpCasesDashlet/FollowUpCasesDashlet.data.php');
        parent::DashletGeneric($id, $def);
        if (empty($def['title']))
            $this->title = 'Watch List Dashlet';
        $this->searchFields = $dashletData['FollowUpCasesDashlet']['searchFields'];
        $this->display_row = ($def['displayRows']) ? $def['displayRows'] : 5;
        $this->myItem = $this->myItemsOnly;
        $this->deshlate_id = $id;
        $this->columns = $dashletData['FollowUpCasesDashlet']['columns'];
        if (empty($def['title']))
            $this->title = 'Watch List Dashlet';
        $this->seedBean = new aCase();
    }

    public function display() {
        $displayArray = array();
        $totalRow = 0;
        $countDisplayRow = 0;
        $idArray = array();
        $header = array('remove' => '', 'module_icon' => '', 'number' => 'Number', 'name' => 'Name/Subject', 'status' => 'Status', 'user' => 'Assign User');
        $ss = new Sugar_Smarty();
        global $db, $current_user;
        if ($this->myItem) {
            $query = "SELECT  followup.id,followup.module_name,followup.module_id,followup.user_id,followup.deleted,cases.case_number,cases.name,cases.assigned_user_id,cases.status from followup,cases WHERE followup.module_name='Cases' and followup.user_id='{$current_user->id}' and followup.deleted=0 and followup.module_id=cases.id group by cases.id";
        } else {
            $query = "SELECT  followup.id,followup.module_name,followup.module_id,followup.user_id,followup.deleted,cases.case_number,cases.name,cases.assigned_user_id,cases.status from followup,cases WHERE followup.module_name='Cases' and followup.deleted=0 and followup.module_id=cases.id group by cases.id";
        }
        $result = $db->query($query);
        $totalRow = $result->num_rows;
        while ($row = $db->fetchByAssoc($result)) {
            if ($countDisplayRow >= $this->display_row)
                break;
            $userObject = new User();
            $userObject->retrieve($row['assigned_user_id']);
            $case_name = "<a href='index.php?module=Cases&action=DetailView&record={$row['module_id']}'>" . $row['name'] . "</a>";
            $displayArray[] = array('number' => $row['case_number'], 'name' => $case_name, 'status' => $row['status'], 'user' => $userObject->name, 'id' => $row['module_id'], 'module' => $row['module_name']);
            $countDisplayRow++;
        }
        if ($this->myItem) {
            $query = "SELECT  followup.id,followup.module_name,followup.module_id,followup.user_id,followup.deleted,tasks.name,tasks.assigned_user_id,tasks.status from followup,tasks WHERE followup.module_name='Task' and followup.user_id='{$current_user->id}' and followup.deleted=0 and followup.module_id=tasks.id group by tasks.id";
        } else {
            $query = "SELECT  followup.id,followup.module_name,followup.module_id,followup.user_id,followup.deleted,tasks.name,tasks.assigned_user_id,tasks.status from followup,tasks WHERE followup.module_name='Task'  and followup.deleted=0 and followup.module_id=tasks.id group by tasks.id";
        }
        $result = $db->query($query);
        $totalRow += $result->num_rows;
        while ($row = $db->fetchByAssoc($result)) {
            if ($countDisplayRow >= $this->display_row)
                break;
            $userObject = new User();
            $userObject->retrieve($row['assigned_user_id']);
            $task_name = "<a href='index.php?module=Tasks&action=DetailView&record={$row['module_id']}'>" . $row['name'] . "</a>";
            $displayArray[] = array('number' => '-', 'name' => $task_name, 'status' => $row['status'], 'user' => $userObject->name, 'id' => $row['module_id'], 'module' => $row['module_name']);
            $countDisplayRow++;
        }
        $ss->assign('assign_user', $displayArray);
        $ss->assign('header', $header);
        $ss->assign('reocrds_id', $idArray);
        $ss->assign('total_record', $totalRow);
        $ss->assign('pagginationBy', ($this->display_row) ? $this->display_row : 5);
        $ss->assign('deshlate_id', $this->deshlate_id);
        if ($this->myItem) {
            $ss->assign('item_checked', '1');
        } else {
            $ss->assign('item_checked', '0');
        }
        return $ss->fetch('custom/modules/Home/Dashlets/FollowUpCasesDashlet/FollowUpCasesDashlet.tpl');
    }

}

?>
