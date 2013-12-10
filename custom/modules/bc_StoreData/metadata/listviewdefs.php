<?php
$module_name = 'bc_StoreData';
$listViewDefs [$module_name] = 
array (
  'NAME' => 
  array (
    'width' => '15%',
    'label' => 'LBL_NAME',
    'default' => true,
    'link' => true,
  ),
  'SKU' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_SKU',
    'width' => '10%',
    'default' => true,
  ),
  'CATALOGNUMBER' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_CATALOGNUMBER',
    'width' => '10%',
    'default' => true,
  ),
  'IMMUNOGEN' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_IMMUNOGEN',
    'width' => '10%',
    'default' => true,
  ),
  'ADMIN_IMMUNOGEN_C' => 
  array (
    'type' => 'varchar',
    'default' => true,
    'label' => 'LBL_ADMIN_IMMUNOGEN',
    'width' => '10%',
  ),
  'ORDER_NUMBER_C' => 
  array (
    'type' => 'varchar',
    'default' => true,
    'label' => 'LBL_ORDER_NUMBER',
    'width' => '10%',
  ),
  'CUSTOMER_PO_NUMBER_C' => 
  array (
    'type' => 'varchar',
    'default' => true,
    'label' => 'LBL_CUSTOMER_PO_NUMBER',
    'width' => '10%',
  ),
  'SUPPLIERID' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_SUPPLIERID',
    'width' => '10%',
    'default' => true,
  ),
  'ASSIGNED_USER_NAME' => 
  array (
    'width' => '9%',
    'label' => 'LBL_ASSIGNED_TO_NAME',
    'module' => 'Employees',
    'id' => 'ASSIGNED_USER_ID',
    'default' => true,
  ),
);
?>
