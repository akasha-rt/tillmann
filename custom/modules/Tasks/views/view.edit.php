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

class TasksViewEdit extends ViewEdit {

    public $useForSubpanel = true;

    function TasksViewEdit() {
        parent::ViewEdit();
    }

    function display() {
        parent::display();
        $this->ev->process();
        /*  $this->ev->fieldDefs['is_priority_c']['readonly'] = "readonly";
          $this->ev->fieldDefs['date_due']['readonly'] = "readonly";
          $this->ev->display(); */
        if ($this->ev->fieldDefs['is_priority_c']['value'] == "1") {
            echo "<script>
            window.onload=function(){
            if(document.getElementById('is_priority_c').checked){
            document.getElementById('is_priority_c').disabled = true;
            document.getElementById('date_due_date').disabled = true;
            document.getElementById('date_due_trigger').disabled = true;
            document.getElementById('date_due_hours').disabled = true;
            document.getElementById('date_due_minutes').disabled = true;
            document.getElementById('date_due_meridiem').disabled = true;
            $('#date_due_trigger').hide();
            var val1 = $('#is_priority_c').val();
            $('input[name=is_priority_c]').val(val1);
            }
            }</script>";
        }
        if ($this->ev->fieldDefs['is_recursive_c']['value'] == "1") {

            $rpt_type = $this->ev->fieldDefs['repeat_type_c']['value'];
            $rpt_delay = $this->ev->fieldDefs['repeat_interval_c']['value'];
            $rpt_count = $this->ev->fieldDefs['repeat_count_c']['value'];
            $rpt_until = $this->ev->fieldDefs['repeat_until_c']['value'];
            $rpt_dow = $this->ev->fieldDefs['repeat_dow_c']['value'];
            echo "<script>
            window.onload=function(){            
           init_recursive('" . $rpt_type . "','" . $rpt_delay . "','" . $rpt_count . "','" . $rpt_until . "','" . $rpt_dow . "');               
            }</script>";
        }
        //init_recursive(" . $rpt_type . "," . $rpt_delay . "," . $rpt_count . "," . $rpt_until . ',' . $rpt_dow . ");
    }

}

?>