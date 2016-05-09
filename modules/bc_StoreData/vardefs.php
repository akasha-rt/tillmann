<?php

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

$dictionary['bc_StoreData'] = array(
    'table' => 'bc_storedata',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'fields' => array(
        'catalognumber' =>
        array(
            'required' => false,
            'name' => 'catalognumber',
            'vname' => 'LBL_CATALOGNUMBER',
            'type' => 'varchar',
            'massupdate' => 0,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'enabled',
            'duplicate_merge_dom_value' => '1',
            'audited' => false,
            'reportable' => true,
            'unified_search' => true,
            'len' => '255',
            'size' => '20',
        ),
        'sku' =>
        array(
            'required' => false,
            'name' => 'sku',
            'vname' => 'LBL_SKU',
            'type' => 'varchar',
            'massupdate' => 0,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'enabled',
            'duplicate_merge_dom_value' => '1',
            'audited' => false,
            'reportable' => true,
            'unified_search' => true,
            'len' => '255',
            'size' => '20',
        ),
        'supplierid' =>
        array(
            'required' => false,
            'name' => 'supplierid',
            'vname' => 'LBL_SUPPLIERID',
            'type' => 'varchar',
            'massupdate' => 0,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'enabled',
            'duplicate_merge_dom_value' => '1',
            'audited' => false,
            'reportable' => true,
            'unified_search' => true,
            'len' => '255',
            'size' => '20',
        ),
        'immunogen' =>
        array(
            'required' => false,
            'name' => 'immunogen',
            'vname' => 'LBL_IMMUNOGEN',
            'type' => 'varchar',
            'massupdate' => 0,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'enabled',
            'duplicate_merge_dom_value' => '1',
            'audited' => false,
            'reportable' => true,
            'unified_search' => false,
            'len' => '255',
            'size' => '20',
        ),
        'purchasingemail' =>
        array(
            'required' => false,
            'name' => 'purchasingemail',
            'vname' => 'LBL_PURCHASINGEMAIL',
            'type' => 'varchar',
            'massupdate' => 0,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'enabled',
            'duplicate_merge_dom_value' => '1',
            'audited' => false,
            'reportable' => true,
            'unified_search' => false,
            'len' => '255',
            'size' => '20',
        ),
        'purchasingname' =>
        array(
            'required' => false,
            'name' => 'purchasingname',
            'vname' => 'LBL_PURCHASINGNAME',
            'type' => 'varchar',
            'massupdate' => 0,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'enabled',
            'duplicate_merge_dom_value' => '1',
            'audited' => false,
            'reportable' => true,
            'unified_search' => false,
            'len' => '255',
            'size' => '20',
        ),
        'supportemail' =>
        array(
            'required' => false,
            'name' => 'supportemail',
            'vname' => 'LBL_SUPPORTEMAIL',
            'type' => 'varchar',
            'massupdate' => 0,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'enabled',
            'duplicate_merge_dom_value' => '1',
            'audited' => false,
            'reportable' => true,
            'unified_search' => false,
            'len' => '255',
            'size' => '20',
        ),
        'supportname' =>
        array(
            'required' => false,
            'name' => 'supportname',
            'vname' => 'LBL_SUPPORTNAME',
            'type' => 'varchar',
            'massupdate' => 0,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'enabled',
            'duplicate_merge_dom_value' => '1',
            'audited' => false,
            'reportable' => true,
            'unified_search' => false,
            'len' => '255',
            'size' => '20',
        ),
        'name' =>
        array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'name',
            'link' => true,
            'dbType' => 'varchar',
            'len' => '255',
            'unified_search' => true,
            'required' => true,
            'importable' => 'required',
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'selected',
            'massupdate' => 0,
            'comments' => '',
            'help' => '',
            'duplicate_merge_dom_value' => '1',
            'audited' => false,
            'reportable' => true,
            'size' => '20',
        ),
    ),
    'relationships' => array(
    ),
    'optimistic_locking' => true,
    'unified_search' => true,
);
if (!class_exists('VardefManager')) {
    require_once('include/SugarObjects/VardefManager.php');
}
VardefManager::createVardef('bc_StoreData', 'bc_StoreData', array('basic', 'assignable'));
