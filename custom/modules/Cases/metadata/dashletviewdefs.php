<?php
$dashletData['CasesDashlet']['searchFields'] = array (
  'date_entered' => 
  array (
    'default' => '',
  ),
  'priority' => 
  array (
    'default' => '',
  ),
  'name' => 
  array (
    'default' => '',
  ),
  'type' => 
  array (
    'default' => '',
  ),
  'technical_c' => 
  array (
    'default' => '',
  ),
  'review_c' => 
  array (
    'default' => '',
  ),
);
$dashletData['CasesDashlet']['columns'] = array (
  'case_number' => 
  array (
    'width' => '5%',
    'label' => 'LBL_NUMBER',
    'default' => true,
    'name' => 'case_number',
  ),
  'name' => 
  array (
    'width' => '40%',
    'label' => 'LBL_LIST_SUBJECT',
    'link' => '1',
    'default' => true,
    'name' => 'name',
  ),
  'priority' => 
  array (
    'width' => '15%',
    'label' => 'LBL_PRIORITY',
    'default' => true,
    'name' => 'priority',
  ),
  'status' => 
  array (
    'width' => '8%',
    'label' => 'LBL_STATUS',
    'default' => true,
    'name' => 'status',
  ),
  'type' => 
  array (
    'type' => 'enum',
    'label' => 'LBL_TYPE',
    'width' => '10%',
    'default' => true,
    'name' => 'type',
  ),
  'technical_c' => 
  array (
    'type' => 'enum',
    'default' => true,
    'studio' => 'visible',
    'label' => 'LBL_TECHNICAL',
    'width' => '10%',
    'name' => 'technical_c',
  ),
  'product_c' => 
  array (
    'type' => 'varchar',
    'default' => true,
    'label' => 'LBL_PRODUCT',
    'width' => '10%',
    'name' => 'product_c',
  ),
  'review_c' => 
  array (
    'type' => 'enum',
    'default' => true,
    'studio' => 'visible',
    'label' => 'LBL_REVIEW',
    'width' => '10%',
    'name' => 'review_c',
  ),
  'supplier_c' => 
  array (
    'type' => 'varchar',
    'default' => true,
    'label' => 'LBL_SUPPLIER',
    'width' => '10%',
    'name' => 'supplier_c',
  ),
  'account_name' => 
  array (
    'width' => '29%',
    'link' => '1',
    'module' => 'Accounts',
    'id' => 'ACCOUNT_ID',
    'ACLTag' => 'ACCOUNT',
    'label' => 'LBL_ACCOUNT_NAME',
    'related_fields' => 
    array (
      0 => 'account_id',
    ),
    'name' => 'account_name',
    'default' => false,
  ),
  'resolution' => 
  array (
    'width' => '8%',
    'label' => 'LBL_RESOLUTION',
    'name' => 'resolution',
    'default' => false,
  ),
  'date_entered' => 
  array (
    'width' => '15%',
    'label' => 'LBL_DATE_ENTERED',
    'name' => 'date_entered',
    'default' => false,
  ),
  'date_modified' => 
  array (
    'width' => '15%',
    'label' => 'LBL_DATE_MODIFIED',
    'name' => 'date_modified',
    'default' => false,
  ),
  'created_by' => 
  array (
    'width' => '8%',
    'label' => 'LBL_CREATED',
    'name' => 'created_by',
    'default' => false,
  ),
  'assigned_user_name' => 
  array (
    'width' => '8%',
    'label' => 'LBL_LIST_ASSIGNED_USER',
    'name' => 'assigned_user_name',
    'default' => false,
  ),
);
