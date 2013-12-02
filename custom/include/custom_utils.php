<?php

function getProductName($id) {
    global $db;
    $new_result = array();
    $id = "'".implode("','",  explode(",", $id))."'";
    $select_Products = "SELECT
         bc_storedata.sku,
        bc_storedata.name
      FROM bc_storedata
      WHERE bc_storedata.sku IN  ($id)";
    $query = $db->query($select_Products);
    while ($result = $db->fetchByAssoc($query)) {
        $new_result[$result['sku']] = $result['name'];
    }
    return $new_result;
}
?>
