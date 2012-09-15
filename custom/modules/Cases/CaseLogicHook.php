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

}

?>
