<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not a valid entry point');

class bc_ExternalOfficeController extends SugarController {

    function action_validateAIPSetting() {
        $api_url = $_POST['api_url'];
        $api_user = $_POST['api_user'];
        $api_pass = $_POST['api_pass'];

        //Validate it!
        require_once 'include/nusoap/nusoap.php';
        $soapclient = new nusoapclient($api_url, true);
        $user_auth = array(
            'user_auth' => array(
                'user_name' => $api_user,
                'password' => $api_pass,
            )
        );
        $result_array = $soapclient->call('login', $user_auth);
        echo $result_array['id'];
        exit;
    }

}

?>
