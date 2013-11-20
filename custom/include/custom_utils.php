<?php

/* function getProductFromStoreData() {
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
  } */

function getProductFromStoreData() {
    global $db;

    $productSQL = $db->query("SELECT *
                                FROM bc_dropdown
                                WHERE dropdown_id = 'productStoreData'
                                ORDER BY option_name");
    /* $productData = $db->fetchByAssoc($productSQL);
      if (empty($productData['id'])) {
      $dd_res = $db->query("INSERT INTO bc_dropdown
      (dropdown_id,
      option_val,
      option_name)
      SELECT
      'productStoreData' AS dropdown_id,
      bc_storedata.sku   AS option_val,
      bc_storedata.name  AS option_name
      FROM bc_storedata
      WHERE bc_storedata.deleted = 0
      AND bc_storedata.name != ''
      AND bc_storedata.name IS NOT NULL
      AND bc_storedata.sku != ''
      AND bc_storedata.sku IS NOT NULL
      GROUP BY bc_storedata.sku
      ORDER BY bc_storedata.name");
      } */

    $option_array = array();
    $option_array[$dd['']] = 'None';
    while ($dd = $db->fetchByAssoc($productSQL)) {
        $option_array[$dd['option_val']] = $dd['option_name'];
    }
    return $option_array;
}

/* function getSupplierFromStoreData() {
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
  } */

function getSupplierFromStoreData() {
    global $db;
    $supplierSQL = $db->query("SELECT *
                                FROM bc_dropdown
                                WHERE dropdown_id = 'supplierStoreData'
                                ORDER BY option_name");
    /* $dd_res = $db->query("INSERT INTO bc_dropdown
      (dropdown_id,
      option_val,
      option_name)
      SELECT DISTINCT
      'supplierStoreData'     AS dropdown_id,
      bc_storedata.supplierid AS option_val,
      bc_storedata.supplierid AS option_name
      FROM bc_storedata
      WHERE bc_storedata.deleted = 0
      AND bc_storedata.supplierid != ''
      AND bc_storedata.supplierid IS NOT NULL
      AND bc_storedata.supplierid != ''
      ORDER BY bc_storedata.supplierid"); */
    $option_array = array();
    $option_array[$dd['']] = 'None';
    while ($dd = $db->fetchByAssoc($supplierSQL)) {
        $option_array[$dd['option_val']] = $dd['option_name'];
    }
    return $option_array;
}

?>
