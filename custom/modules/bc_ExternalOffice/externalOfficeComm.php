<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not a valid entry point');

include 'include/nusoap/nusoap.php';

class ExternalOfficeComm {

    var $api_url = '';
    var $api_user = '';
    var $api_pass = '';
    var $session_id = '';
    var $soap_client = '';

    function __construct($api_url, $api_user, $api_pass) {
        $this->api_url = $api_url;
        $this->api_user = $api_user;
        $this->api_pass = $api_pass;
        $this->soap_client = new nusoapclient($this->api_url, true);
    }

    private function checkSession() {
        return (!empty($this->session_id)) ? true : $this->login();
    }

    private function login() {
        $user_auth = array(
            'user_auth' => array(
                'user_name' => $this->api_user,
                'password' => $this->api_pass,
            )
        );
        $result_array = $this->soap_client->call('login', $user_auth);
        $error = $result_array['error'];
        if ($error['number'] <> 0) {
            $this->session_id = '';
            return false;
        } else {
            $this->session_id = $result_array['id'];
            return true;
        }
    }

    function getExternalOfficeUsers() {

        if ($this->checkSession()) {
            $user_list_params = array(
                'session' => $this->session_id,
                'module_name' => 'Users',
                'select_fields' => array('id', 'first_name', 'last_name'),
                'deleted' => 0,
                'query' => 'users.status = "active"',
            );
            $user_result = $this->soap_client->call('get_entry_list', $user_list_params);
            $error = $user_result['error'];
            if ($error['number'] <> 0) {
                $this->session_id = '';
                return false;
            }

            $user_array = array();
            foreach ($user_result['entry_list'] as $key => $user_data) {
                $id = $first_name = $last_name = '';
                foreach ($user_data['name_value_list'] as $name_data_pair) {
                    $$name_data_pair['name'] = $name_data_pair['value'];
                }
                $user_array[$id] = trim($first_name . ' ' . $last_name);
            }
            return $user_array;
        }
    }

    function logout() {
        $error = $this->soap_client->call('logout', $this->session_id);
    }

}

?>
