<?php

function getProductFromStoreData() {
    global $db;

    $productSQL = $db->query("SELECT *
                                FROM bc_dropdown
                                WHERE dropdown_id = 'productStoreData'
                                ORDER BY option_name");
    $option_array = array();
    $option_array[$dd['']] = 'None';
    while ($dd = $db->fetchByAssoc($productSQL)) {
        $option_array[$dd['option_val']] = $dd['option_name'];
    }
    return $option_array;
}

function getSupplierFromStoreData() {
    global $db;
    $supplierSQL = $db->query("SELECT *
                                FROM bc_dropdown
                                WHERE dropdown_id = 'supplierStoreData'
                                ORDER BY option_name");
    $option_array = array();
    $option_array[$dd['']] = 'None';
    while ($dd = $db->fetchByAssoc($supplierSQL)) {
        $option_array[$dd['option_val']] = $dd['option_name'];
    }
    return $option_array;
}

?>
