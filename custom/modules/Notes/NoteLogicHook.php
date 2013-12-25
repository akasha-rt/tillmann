<?php

/**
 * Case Custom code
 * @author Dhaval Darji
 */
class NoteLogicHook {

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

    function syncNoteWithExternalOffice(&$bean, $event, $arguments) {
        if (!$bean->synced && count($_REQUEST) > 4 && $bean->parent_type == 'Cases' &&
                !empty($bean->parent_id) && empty($bean->fetched_row['id'])) {

            $case = new aCase();
            $case->retrieve($bean->parent_id);

            if (!empty($case->external_office_c)) {

                global $sugar_config;
                $note_sync_data = array();
                $note_key_fields = $sugar_config['sync_data']['Notes']['to_sync'];

                //initiate syncing!
                include_once 'custom/modules/bc_ExternalOffice/externalOfficeComm.php';
                $comm_gateway = new ExternalOfficeComm($case->external_office_c);

                if (empty($bean->external_note_id_c)) {

                    foreach ($note_key_fields as $field) {
                        $note_sync_data[$field] = $bean->$field;
                    }
                    $note_sync_data['assigned_user_id'] = $case->external_user_id_c;
                    $note_sync_data['assigned_user_name'] = $case->external_user_name_c;
                    $note_sync_data['parent_type'] = 'Cases';
                    $note_sync_data['parent_id'] = $case->external_case_id_c;
                    $note_sync_data['external_note_id_c'] = $bean->id;
                    //Sync note
                    $bean->external_note_id_c = $comm_gateway->syncNoteToExternalOffice($note_sync_data);
                    $bean->synced = true;
                }
            }
        }
    }

}

?>
