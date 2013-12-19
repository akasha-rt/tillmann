<?php
$popupMeta = array (
    'moduleMain' => 'bc_ExternalOffice',
    'varName' => 'bc_ExternalOffice',
    'orderBy' => 'bc_externaloffice.name',
    'whereClauses' => array (
  'name' => 'bc_externaloffice.name',
  'office_code' => 'bc_externaloffice.office_code',
),
    'searchInputs' => array (
  1 => 'name',
  4 => 'office_code',
),
    'searchdefs' => array (
  'name' => 
  array (
    'type' => 'name',
    'link' => true,
    'label' => 'LBL_NAME',
    'width' => '10%',
    'name' => 'name',
  ),
  'office_code' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_OFFICE_CODE',
    'width' => '10%',
    'name' => 'office_code',
  ),
),
    'listviewdefs' => array (
  'NAME' => 
  array (
    'type' => 'name',
    'link' => true,
    'label' => 'LBL_NAME',
    'width' => '10%',
    'default' => true,
  ),
  'OFFICE_CODE' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_OFFICE_CODE',
    'width' => '10%',
    'default' => true,
  ),
),
);
