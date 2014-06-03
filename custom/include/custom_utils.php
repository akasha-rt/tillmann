<?php

function getProductName($id)
{
    global $db;
    $new_result = array();
    $id = "'" . implode("','", explode(",", $id)) . "'";
    if (!empty($id)) {
        $select_Products = "SELECT
         bc_storedata.sku,
        bc_storedata.name
      FROM bc_storedata
      WHERE bc_storedata.sku IN  ($id)";
        $query = $db->query($select_Products);
        while ($result = $db->fetchByAssoc($query)) {
            $new_result[$result['sku']] = $result['name'];
        }
    }
    return $new_result;
}

function getExternalOfficeList($focus, $name, $value, $view)
{
    global $db;
    $result = $db->query("SELECT
                            bc_externaloffice.office_code,
                            bc_externaloffice.name
                          FROM bc_externaloffice
                          WHERE bc_externaloffice.deleted = 0");
    $option_array[''] = 'None';
    while ($data = $db->fetchByAssoc($result)) {
        $option_array[$data['office_code']] = $data['name'];
    }
    return $option_array;
}

function getExternalOfficeUserList($focus, $name, $value, $view)
{
    //$focus->external_office_c
    $user_array = array('' => 'None');
    if (!empty($focus->external_office_c)) {
        include_once 'custom/modules/bc_ExternalOffice/externalOfficeComm.php';
        $comm_gateway = new ExternalOfficeComm($focus->external_office_c);
        $user_array = $comm_gateway->getExternalOfficeUsers();
    }
    return $user_array;
}

?>
