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

}

?>
