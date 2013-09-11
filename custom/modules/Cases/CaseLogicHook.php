<?php

/**
 * Case Custom code
 * @author Dhaval Darji
 */
class CaseLogicHook {

    /**
     * To call after_save logic hook
     * To close all related emails for closed case
     * @param Object | SugarBean $bean
     * @param type $event
     * @param type $arguments 
     */
    function closeEmails(&$bean, $event, $arguments) {
        if ($bean->status == 'Closed') {
            $bean->load_relationship('emails');
            foreach ($bean->emails->getBeans() as $email) {
                $email->status = 'closed';
                $email->save();
            }
        }
    }

    function queueNotification(&$bean, $event, $arguments) {
        if ($bean->fetched_row['assigned_user_id'] != $bean->assigned_user_id) {
            global $db;
            $id = create_guid();
            $sql = "INSERT into notification_queue (id,userid,bean_id,bean_type,date_time,is_notify) 
            VALUES ('{$id}','{$bean->assigned_user_id}','{$bean->id}','{$bean->module_name}',NOW(),0)";
            $db->query($sql);
        }
    }
    
    function saveWorkFlowTask(&$bean, $event, $arguments) {
            $workflowObj = new bc_WorkFlow();
            $workflowObj->retrieve($bean->bc_workflow_casesbc_workflow_ida);
            $workflowtask = $workflowObj->get_linked_beans('bc_workflow_bc_workflowtasks', 'bc_workflowtasks');
            $bean->retrieve($bean->id);
            $caseTask = $bean->get_linked_beans('bc_workflowtasks_cases', 'bc_workflowtasks');
            if(empty($caseTask)){
            foreach ($workflowtask as $key => $wf_task) {
                $wf_taskCase = new bc_WorkFlowTasks();
                $wf_taskCase->name = $wf_task->name;
                $wf_taskCase->status = $wf_task->status;
                $wf_taskCase->note = $wf_task->note;
                $wf_taskCase->description = $wf_task->description;
                $wf_taskCase->assigned_user_id = $wf_task->assigned_user_id;
                $wf_taskCase->save();
                $bean->load_relationship('bc_workflowtasks_cases');
                $bean->bc_workflowtasks_cases->add($wf_taskCase->id);
                unset($wf_taskCase);
            }
        }
    }

}

?>
