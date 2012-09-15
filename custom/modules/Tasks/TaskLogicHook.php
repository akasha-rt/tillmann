<?php

/**
 * Case Custom code
 * @author Dhaval Darji
 */
class TaskLogicHook {

    /**
     * To call after_save logic hook
     * To close all related emails for closed case
     * @param Object | SugarBean $bean
     * @param type $event
     * @param type $arguments 
     */
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
