<?php

function getProductFromStoreData() {
    global $db;
    $dd_res = $db->query("SELECT
                            bc_storedata.name AS Product,
                            bc_storedata.sku  AS Sku
                          FROM bc_storedata
                          WHERE bc_storedata.deleted = 0
                              AND bc_storedata.name != ''
                              AND bc_storedata.name IS NOT NULL
                              AND bc_storedata.sku != ''
                              AND bc_storedata.sku IS NOT NULL
                          GROUP BY bc_storedata.sku
                          ORDER BY bc_storedata.name");
    while ($dd = $db->fetchByAssoc($dd_res)) {
        $option_array[$dd['Sku']] = $dd['Product'];
    }
    return $option_array;
}

function getSupplierFromStoreData() {
    global $db;
    $dd_res = $db->query("SELECT DISTINCT
                            bc_storedata.supplierid AS Supplier
                          FROM bc_storedata
                          WHERE bc_storedata.deleted = 0
                              AND bc_storedata.supplierid != ''
                              AND bc_storedata.supplierid IS NOT NULL
                              AND bc_storedata.supplierid != ''
                          ORDER BY bc_storedata.supplierid");
    while ($dd = $db->fetchByAssoc($dd_res)) {
        $option_array[$dd['Supplier']] = $dd['Supplier'];
    }
    return $option_array;
}

?>
