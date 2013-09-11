<?php

require_once 'include/MVC/View/views/view.detail.php';

class bc_WorkFlowTasksViewDetail extends ViewDetail {

    function display() {
        if (!empty($this->bean->bc_workflow_bc_workflowtasksbc_workflow_ida)) {
            echo "<script type='text/javascript'>
                  $(document).ready(function(){
                     var note_field = $('#note').parent();
                     var note_lable = $('#note').parent().closest('td').prev('td');
                     note_field.html('');
                     note_lable.html('');
                     
                    var status_field_tr = $('#status').parents('tr');
                    status_field_tr.hide();
                     
                     /*var status_field = $('#status').parent();
                     var status_lable = $('#status').parent().closest('td').prev('td');
                     status_field.html('');
                     status_lable.html('');
                     var case_field = $('#bc_workflowtasks_casescases_ida').parent();
                     var case_lable = $('#bc_workflowtasks_casescases_ida').parent().closest('td').prev('td');
                     case_field.html('');
                     case_lable.html('');*/
                     
                   });
                </script>";
        } else {
            echo "<script type='text/javascript'>
                $(document).ready(function(){
                    var case_field = $('#bc_workflow_bc_workflowtasksbc_workflow_ida').parent();
                    var case_lable = $('#bc_workflow_bc_workflowtasksbc_workflow_ida').parent().closest('td').prev('td');
                    case_field.html('');
                    case_lable.html('');
                    });
                    </script> ";
        }

        parent::display();
    }

}

?>
