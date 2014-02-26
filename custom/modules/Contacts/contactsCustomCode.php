<?php

class contactsCustomCode {

    function updateDateModified(&$bean, $event, $arguments) {
        $bean->date_modified = TimeDate::getInstance()->now();
        $bean->save(false);
    }

}

?>
