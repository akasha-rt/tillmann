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
                isset($arguments['related_module'])
                && $arguments['related_module'] == 'Cases'
                && isset($arguments['related_id'])
                && !empty($arguments['related_id'])
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
        }
    }

}

?>
