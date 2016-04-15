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

 * Description:
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights
 * Reserved. Contributor(s): ______________________________________..
 * ******************************************************************************* */


$focus = new Email();
$focus->email2init();
$focus->et->preflightUser($current_user);
$out = $focus->et->displayEmailFrame();
echo $out;
// Comment By Govind: After upgarde Sugar640 to Suite753, now don't need to include jquery.
//echo "<script type='text/javascript' language='javascript' src='custom/include/js/jquery.js'></script>";
echo "<script>var composePackage = null;</script>";

$skipFooters = true;
/**
 * TO add functionality of QuickSearch to TO field
 * @author Dhaval darji
 */
/*$quicksearch_js='';
require_once('custom/include/QuickSearchDefaults_cust.php');
$qsd = new QuickSearchDefaults_cust();
$json =  getJSONobj();
$qsd->setFormName('ComposeEditView1');
$sqs_objects = array('addressTO1' => $qsd->getQSParent());
$quicksearch_js .= '<script type="text/javascript" language="javascript">
sqs_objects = ' . $json->encode($sqs_objects) . '</script>';


echo $quicksearch_js;*/
//'<script type="text/javascript" language="javascript">sqs_objects = {"addressTO{idx}":{"form":"ComposeEditView{idx}","method":"query","modules":["Accounts"],"group":"or","field_list":["name","id"],"populate_list":["addressTO{idx}","emailSubject{idx}"],"required_list":["addressTO{idx}"],"conditions":[{"name":"name","op":"like_custom","end":"%","value":""}],"order":"name","limit":"30","no_match_text":"No Match"}}</script>' +
//END - Dhaval