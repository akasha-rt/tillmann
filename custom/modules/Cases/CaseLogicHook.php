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
        if ($bean->status == 'Closed' && !$bean->synced) {
            $bean->load_relationship('emails');
            foreach ($bean->emails->getBeans() as $email) {
                $email->status = 'closed';
                $email->save();
            }
        }
    }

    function queueNotification(&$bean, $event, $arguments) {
        if ($bean->fetched_row['assigned_user_id'] != $bean->assigned_user_id && !$bean->synced) {
            global $db;
            $id = create_guid();
            $sql = "INSERT into notification_queue (id,userid,bean_id,bean_type,date_time,is_notify) 
            VALUES ('{$id}','{$bean->assigned_user_id}','{$bean->id}','{$bean->module_name}',NOW(),0)";
            $db->query($sql);
        }
    }

    function saveWorkFlowTask(&$bean, $event, $arguments) {
        $workflowObj = new bc_WorkFlow();
        $workflowObj->retrieve((!is_object($bean->bc_workflow_casesbc_workflow_ida)) ? $bean->bc_workflow_casesbc_workflow_ida : '');
        $workflowtask = $workflowObj->get_linked_beans('bc_workflow_bc_workflowtasks', 'bc_workflowtasks');
        $bean->retrieve($bean->id);
        $caseTask = $bean->get_linked_beans('bc_workflowtasks_cases', 'bc_workflowtasks');
        if (empty($caseTask)) {
            foreach ($workflowtask as $key => $wf_task) {
                $wf_taskCase = new bc_WorkFlowTasks();
                $wf_taskCase->name = $wf_task->name;
                $wf_taskCase->status = $wf_task->status;
                $wf_taskCase->note = $wf_task->note;
                $wf_taskCase->description = $wf_task->description;
                $wf_taskCase->assigned_user_id = $wf_task->assigned_user_id;
                $wf_taskCase->task_sequence_c = $wf_task->task_sequence_c;
                $wf_taskCase->save();
                $bean->load_relationship('bc_workflowtasks_cases');
                $bean->bc_workflowtasks_cases->add($wf_taskCase->id);
                unset($wf_taskCase);
            }
        }
    }

    function openCaseOnNewEmail(&$bean, $event, $arguments) {
        if ($arguments['related_module'] == 'Emails' && $bean->firstTime != true && !$bean->synced) {
            $email = BeanFactory::getBean('Emails', $arguments['related_id']);
            if ($email->status != 'closed') {
                $bean->status = 'Open';
                $bean->save(false);
            }
        }
    }

    function assignInitialStatus(&$bean, $event, $arguments) {
        if (empty($bean->fetched_row) && !$bean->synced)
            $bean->firstTime = true;
    }

    function syncCaseWithExternalOffice(&$bean, $event, $arguments) {
        if (!$bean->synced && count($_REQUEST) > 4 && !empty($bean->external_office_c) && $_REQUEST['module'] == 'Cases') {

            //check if office changed
            if ($bean->external_office_c != $bean->fetched_row['external_office_c']) {
                $bean->external_case_id_c = '';
            }

            global $sugar_config;
            $case_sync_data = array();
            $note_sync_data = array();
            $email_sync_data = array();

            //initiate syncing!
            include_once 'custom/modules/bc_ExternalOffice/externalOfficeComm.php';
            $comm_gateway = new ExternalOfficeComm($bean->external_office_c);

            $case_key_fields = $sugar_config['sync_data']['Cases']['to_sync'];
            $note_key_fields = $sugar_config['sync_data']['Notes']['to_sync'];
            $email_key_fields = $sugar_config['sync_data']['Emails']['to_sync'];

            //grab changes fields
            if (!empty($bean->external_case_id_c)) {
                foreach ($case_key_fields as $field) {
                    if ($bean->$field != $bean->fetched_row[$field]) {
                        $case_sync_data[$field] = $bean->$field;
                    }
                }
            } else {
                foreach ($case_key_fields as $field) {
                    $case_sync_data[$field] = $bean->$field;
                }
            }
            //correct few fields
            unset($case_sync_data['id']);
            if (!empty($bean->external_case_id_c)) {
                $case_sync_data['id'] = $bean->external_case_id_c;
            }
            $case_sync_data['assigned_user_id'] = $bean->external_user_id_c;
            $case_sync_data['assigned_user_name'] = $bean->external_user_name_c;
            $case_sync_data['external_office_c'] = $sugar_config['office_code'];
            $case_sync_data['external_user_id_c'] = $bean->assigned_user_id;
            $case_sync_data['external_user_name_c'] = $bean->assigned_user_name;
            $case_sync_data['external_case_id_c'] = $bean->id;
            //sync case first
            $bean->external_case_id_c = $comm_gateway->syncCaseToExternalOffice($case_sync_data);
            $bean->synced = true;

            //time for history items
            //notes first
            $bean->load_relationship("case_notes");
            $relatedNotes = $bean->get_linked_beans('notes', 'Notes');
            if (!empty($relatedNotes)) {
                foreach ($relatedNotes as $note) {
                    if (empty($note->external_note_id_c)) {
                        $note_sync_data = array();
                        foreach ($note_key_fields as $field) {
                            $note_sync_data[$field] = $note->$field;
                        }
                        $note_sync_data['assigned_user_id'] = $bean->external_user_id_c;
                        $note_sync_data['assigned_user_name'] = $bean->external_user_name_c;
                        $note_sync_data['parent_type'] = 'Cases';
                        $note_sync_data['parent_id'] = $bean->external_case_id_c;
                        $note_sync_data['external_note_id_c'] = $note->id;
                        //Sync note
                        $note->external_note_id_c = $comm_gateway->syncNoteToExternalOffice($note_sync_data);
                        $note->synced = true;
                        $note->save(false);
                    }
                }
            }

            //now emails
            $bean->load_relationship("case_emails");
            $relatedEmails = $bean->get_linked_beans('emails', 'Emails');
            if (!empty($relatedEmails)) {
                foreach ($relatedEmails as $email) {
                    if (empty($email->external_email_id_c)) {
                        $email_sync_data = array();
                        foreach ($email_key_fields as $field) {
                            $email_sync_data[$field] = $email->$field;
                        }
                        $email_sync_data['assigned_user_id'] = $bean->external_user_id_c;
                        $email_sync_data['assigned_user_name'] = $bean->external_user_name_c;
                        $email_sync_data['parent_type'] = 'Cases';
                        $email_sync_data['parent_id'] = $bean->external_case_id_c;
                        $email_sync_data['external_email_id_c'] = $email->id;
                        //Sync email
                        $email->external_email_id_c = $comm_gateway->syncEmailToExternalOffice($email_sync_data);
                        $email->synced = true;
                        $email->save(false);
                    }
                }
            }
        }
    }

}

?>
