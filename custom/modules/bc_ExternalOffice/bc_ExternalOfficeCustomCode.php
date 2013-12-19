<?php

class bc_ExternalOfficeCustomCode {

    function validatePass(&$bean, $event, $arguments) {
        if ($bean->fetched_row['api_user_pass'] != $bean->api_user_pass) {
            $bean->api_user_pass = md5($bean->api_user_pass);
        }
    }

}

?>
