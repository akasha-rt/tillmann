<?php

/**
 * Email Custom code
 * @author Dhaval Darji
 */
class EMailsLogicHook {

    /**
     * TO attach Note to case if email is related to case
     * @param type $bean
     * @param type $event
     * @param type $arguments 
     */
    function attachNotesToCase(&$bean, $event, $arguments) {
        //This is for Case only
        if (
                isset($arguments['related_module']) && $arguments['related_module'] == 'Cases' &&
                isset($arguments['related_id']) && !empty($arguments['related_id']) && !$bean->synced
        ) {
            if ($bean->load_relationship('notes')) {
                foreach ($bean->notes->getBeans() as $note) {
                    $note->retrieve($note->id);
                    //$note->load_relationship('cases');
                    //$note->cases->add($arguments['related_id']);
                    $note->parent_type = $arguments['related_module'];
                    $note->parent_id = $arguments['related_id'];
                    $note->save();
                }
            }
            if (count($_REQUEST) > 4) {

                $case = new aCase();
                $case->retrieve($arguments['related_id']);

                if (!empty($case->external_office_c)) {

                    global $sugar_config;
                    $email_sync_data = array();
                    $email_key_fields = $sugar_config['sync_data']['Emails']['to_sync'];

                    //initiate syncing!
                    include_once 'custom/modules/bc_ExternalOffice/externalOfficeComm.php';
                    $comm_gateway = new ExternalOfficeComm($case->external_office_c);

                    if (empty($bean->external_email_id_c)) {

                        foreach ($email_key_fields as $field) {
                            $email_sync_data[$field] = $bean->$field;
                        }
                        $email_sync_data['assigned_user_id'] = $case->external_user_id_c;
                        $email_sync_data['assigned_user_name'] = $case->external_user_name_c;
                        $email_sync_data['parent_type'] = 'Cases';
                        $email_sync_data['parent_id'] = $case->external_case_id_c;
                        $email_sync_data['external_email_id_c'] = $bean->id;
                        //Sync note
                        $bean->external_email_id_c = $comm_gateway->syncEmailToExternalOffice($email_sync_data);
                        $bean->synced = true;
                    }
                }
            }
        }
    }

}

?>
