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
    var $office_code = '';
    var $name_value_list = '';

    function __construct($office_code) {
        $this->office_code = $office_code;
    }

    function setConnectionParams() {
        global $db;
        $office_query = "SELECT
                            bc_externaloffice.api_url,
                            bc_externaloffice.api_user,
                            bc_externaloffice.api_user_pass
                          FROM bc_externaloffice
                          WHERE bc_externaloffice.deleted = 0
                              AND bc_externaloffice.office_code = '{$this->office_code}'";
        $result = $db->query($office_query);
        $office_detail = $db->fetchByAssoc($result);
        $this->api_url = $office_detail['api_url'];
        $this->api_user = $office_detail['api_user'];
        $this->api_pass = $office_detail['api_user_pass'];
        $this->soap_client = new nusoapclient($this->api_url, true);
    }

    private function checkSession() {
        return (!empty($this->session_id)) ? true : $this->login();
    }

    private function login() {
        $this->setConnectionParams();
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

    function formateSyncDataNVL($sync_data) {
        $this->name_value_list = array();
        $index = 0;
        foreach ($sync_data as $name => $value) {
            $this->name_value_list[$index]['name'] = $name;
            $this->name_value_list[$index]['value'] = $value;
            $index++;
        }
    }

    function syncSetEntry($module_name) {
        $set_entry_params = array(
            'session' => $this->session_id,
            'module_name' => $module_name,
            'name_value_list' => $this->name_value_list
        );
        $set_entry_result = $this->soap_client->call('set_entry', $set_entry_params);
        $error = $set_entry_result['error'];
        if ($error['number'] <> 0) {
            $this->session_id = '';
            return false;
        }
        return $set_entry_result['id'];
    }

    function syncNoteAttachment($note_id, $filename) {
        global $sugar_config;
        $attachment = array(
            'id' => $note_id,
            'filename' => $filename,
            'file' => base64_encode(file_get_contents($sugar_config['upload_dir'] . $note_id))
        );
        $note_attachment = array(
            'session' => $this->session_id,
            'note' => $attachment
        );
        $this->soap_client->call('set_note_attachment', $note_attachment);
    }

    function syncCaseToExternalOffice($sync_data = array()) {
        $this->formateSyncDataNVL($sync_data);
        if ($this->checkSession()) {
            return $this->syncSetEntry('Cases');
        }
        return false;
    }

    function syncNoteToExternalOffice($sync_data = array()) {
        $this->formateSyncDataNVL($sync_data);
        if ($this->checkSession()) {
            $note_id = $this->syncSetEntry('Notes');
            if (!empty($sync_data['filename'])) {
                $this->syncNoteAttachment($note_id, $sync_data['filename']);
            }
            return $note_id;
        }
        return false;
    }

    function syncEmailToExternalOffice($sync_data = array()) {
        $this->formateSyncDataNVL($sync_data);
        if ($this->checkSession()) {
            return $this->syncSetEntry('Emails');
        }
        return false;
    }

    function logout() {
        $error = $this->soap_client->call('logout', $this->session_id);
    }

    function __destruct() {
        $this->logout();
    }

}
?>
