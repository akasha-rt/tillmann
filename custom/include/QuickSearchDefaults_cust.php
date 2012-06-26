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


/**
 * QuickSearchDefaults class, outputs default values for setting up quicksearch
 *
 * @copyright  2004-2007 SugarCRM Inc.
 * @license    http://www.sugarcrm.com/crm/products/sugar-professional-eula.html  SugarCRM Professional End User License
 * @since      Class available since Release 4.0
 */
require_once 'include/QuickSearchDefaults.php';

class QuickSearchDefaults_cust extends QuickSearchDefaults {

    function getQSParent($parent = 'Accounts') {
        global $app_strings;

        $qsParent = array(
            'form' => $this->form_name,
            'method' => 'query',
            'modules' => array($parent),
            'group' => 'or',
            'field_list' => array('name', 'id'),
            'populate_list' => array('addressTO1', 'emailSubject1'),
            'required_list' => array('addressTO1'),
            'conditions' => array(array('name' => 'name', 'op' => 'like_custom', 'end' => '%', 'value' => '')),
            'order' => 'name',
            'limit' => '30',
            'no_match_text' => $app_strings['ERR_SQS_NO_MATCH']
        );

        return $qsParent;
    }

    function getQSEmail($parent = 'Emails') {
        global $app_strings;

        $qsParent = array(
            'form' => $this->form_name,
            'method' => 'query',
            'modules' => array($parent),
            'group' => 'or',
            'field_list' => array('email_address'),
            'populate_list' => array('addressTO1'),
            //'required_list' => array('parent_id'),
            'conditions' => array(array('name' => 'email_address', 'op' => 'like_custom', 'end' => '%', 'value' => '')),
            'order' => 'email_address',
            'limit' => '30',
            'no_match_text' => $app_strings['ERR_SQS_NO_MATCH']
        );

        return $qsParent;
    }

}

?>
