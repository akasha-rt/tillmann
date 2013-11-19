<?php

function getProductFromStoreData() {
    if (isset(SugarCache::instance()->getProductFromStoreData)) {
        return SugarCache::instance()->getProductFromStoreData;
    }
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
    $option_array = array();
    $option_array[$dd['']] = 'None';
    while ($dd = $db->fetchByAssoc($dd_res)) {
        $option_array[$dd['Sku']] = $dd['Product'];
    }
    SugarCache::instance()->getProductFromStoreData = $option_array;
    return $option_array;
}

function getSupplierFromStoreData() {
    if (isset(SugarCache::instance()->getSupplierFromStoreData)) {
        return SugarCache::instance()->getSupplierFromStoreData;
    }
    global $db;
    $dd_res = $db->query("SELECT DISTINCT
                            bc_storedata.supplierid AS Supplier
                          FROM bc_storedata
                          WHERE bc_storedata.deleted = 0
                              AND bc_storedata.supplierid != ''
                              AND bc_storedata.supplierid IS NOT NULL
                              AND bc_storedata.supplierid != ''
                          ORDER BY bc_storedata.supplierid");
    $option_array = array();
    $option_array[$dd['']] = 'None';
    while ($dd = $db->fetchByAssoc($dd_res)) {
        $option_array[$dd['Supplier']] = $dd['Supplier'];
    }
    SugarCache::instance()->getSupplierFromStoreData = $option_array;
    return $option_array;
}

?>
