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


require_once('include/MVC/View/views/view.edit.php');

class CasesViewEdit extends ViewEdit {

    public $useForSubpanel = true;

    function CasesViewEdit() {
        parent::ViewEdit();
    }

    /**
     * display
     * Override the display method to support customization for the buttons that display
     * a popup and allow you to copy the account's address into the selected contacts.
     * The custom_code_billing and custom_code_shipping Smarty variables are found in
     * include/SugarFields/Fields/Address/DetailView.tpl (default).  If it's a English U.S.
     * locale then it'll use file include/SugarFields/Fields/Address/en_us.DetailView.tpl.
     */
    function display() {
        require_once 'custom/include/custom_utils.php';
        $product_temp_array = getProductName($this->bean->product_c);
        $productIDVal = array();
        $key = 0;
        foreach ($product_temp_array as $id => $val) {
            $productIDVal[$key]['id'] = $id;
            $productIDVal[$key]['name'] = $val;
            $key++;
        }
        $procuctJSON = json_encode($productIDVal);
        $supplierList = array();
        $supplier_temp = explode(",", $this->bean->supplier_c);
        foreach ($supplier_temp as $ids => $vals) {
            if (!empty($vals)) {
                $supplierList[$ids]['id'] = $vals;
                $supplierList[$ids]['name'] = $vals;
            }
        }
        $supplierJSON = json_encode($supplierList);
        echo "
            <script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js'></script>
            <script type='text/javascript' src='custom/include/js/jquery.tokeninput.js'></script>
            <link rel='stylesheet' type='text/css' href='custom/include/css/token-input-facebook.css' />
            <script type='text/javascript'>
            $(document).ready(function () {
                $('#product_c').tokenInput('index.php?module=Cases&action=saveDataInbcDropdown&product_c=1',{prePopulate: {$procuctJSON}});
                $('#supplier_c').tokenInput('index.php?module=Cases&action=saveDataInbcDropdown&supplier_c=1',{prePopulate: {$supplierJSON}});
            });
            </script>
            ";
        parent::display();
        if (!empty($this->bean->bc_workflow_casesbc_workflow_ida)) {
            echo "<script>
                  $(document).ready(function(){
                     $('#btn_bc_workflow_cases_name').hide();
                     $('#btn_clr_bc_workflow_cases_name').hide();
                     $('#bc_workflow_cases_name').attr('readOnly','readOnly');
                  });
                </script>";
        }
        if ($this->ev->formName == 'form_SubpanelQuickCreate_Cases') {

            echo "<script>            
   removeFromValidate('form_SubpanelQuickCreate_Cases','account_name');             
          </script>";
        }
        if ($this->ev->view == 'EditView') {

            echo "<script>                  
        removeFromValidate('EditView','account_name');      
          </script>";
        }
    }

}

?>