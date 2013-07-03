<?php

require_once('include/MVC/Controller/SugarController.php');

class la_LoginAuditController extends SugarController {

    public function __construct() {
        parent::SugarController();
    }

    public function action_autoLogoutOnBrowserClose() {
        global $db, $current_user;
        $id = create_guid();
        $date = TimeDate::getInstance()->nowDb();
        $dateToday = TimeDate::getInstance()->nowDbDate();
        $username = $current_user->user_name;
        $is_admin = $current_user->is_admin;
        $ip = $_SESSION['ipaddress'];
        $findLastLogoutRecord = "SELECT
                                    id,
                                    MAX(date_entered) AS MaxDate
                                  FROM la_loginaudit
                                  WHERE DATE(date_entered) = '{$dateToday}'
                                      AND result = 'Logout'
                                      AND typed_name = '{$username}'
                                      AND deleted = 0";
        $lastLogoutRow = $db->fetchByAssoc($db->query($findLastLogoutRecord));
        if ($lastLogoutRow['MaxDate'] == null) {
            $query = "INSERT INTO la_loginaudit
                        (id,
                         NAME,
                         date_entered,
                         typed_name,
                         is_admin,
                         result,
                         ip_address,
                         created_by,
                         assigned_user_id)
                    VALUES ('" . $id . "',
                            '',
                            '" . $date . "',
                            '" . $username . "',
                            '" . $is_admin . "',
                            'Logout',
                            '" . $ip . "',
                            '{$current_user->id}',
                            '{$current_user->id}')";
            $db->query($query);
        } else {
            $Update = "Update la_loginaudit set date_entered = '{$date}' where id = '{$lastLogoutRow['id']}'";
            $query = $db->query($Update);
        }
        exit;
    }

}
?>
