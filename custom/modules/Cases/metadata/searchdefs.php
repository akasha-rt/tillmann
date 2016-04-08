<?php
// created: 2016-04-08 13:46:17
$searchdefs['Cases'] = array (
  'layout' => 
  array (
    'basic_search' => 
    array (
      0 => 'name',
      1 => 
      array (
        'name' => 'current_user_only',
        'label' => 'LBL_CURRENT_USER_FILTER',
        'type' => 'bool',
      ),
      2 => 
      array (
        'name' => 'open_only',
        'label' => 'LBL_OPEN_ITEMS',
        'type' => 'bool',
        'default' => false,
        'width' => '10%',
      ),
    ),
    'advanced_search' => 
    array (
      0 => 
      array (
        'name' => 'case_number',
        'default' => true,
        'width' => '10%',
      ),
      1 => 
      array (
        'name' => 'name',
        'default' => true,
        'width' => '10%',
      ),
      2 => 
      array (
        'name' => 'status',
        'default' => true,
        'width' => '10%',
      ),
      3 => 
      array (
        'label' => 'LBL_PRODUCT',
        'width' => '10%',
        'name' => 'product_c',
        'default' => true,
      ),
      4 => 
      array (
        'name' => 'assigned_user_id',
        'type' => 'enum',
        'label' => 'LBL_ASSIGNED_TO',
        'function' => 
        array (
          'name' => 'get_user_array',
          'params' => 
          array (
            0 => false,
          ),
        ),
        'default' => true,
        'width' => '10%',
      ),
      5 => 
      array (
        'name' => 'priority',
        'default' => true,
        'width' => '10%',
      ),
      6 => 
      array (
        'label' => 'LBL_SUPPLIER',
        'width' => '10%',
        'name' => 'supplier_c',
        'default' => true,
      ),
      7 => 
      array (
        'type' => 'enum',
        'default' => true,
        'studio' => 'visible',
        'label' => 'LBL_REVIEW',
        'width' => '10%',
        'name' => 'review_c',
      ),
      8 => 
      array (
        'type' => 'enum',
        'default' => true,
        'studio' => 'visible',
        'label' => 'LBL_TECHNICAL',
        'width' => '10%',
        'name' => 'technical_c',
      ),
    ),
  ),
  'templateMeta' => 
  array (
    'maxColumns' => '3',
    'maxColumnsBasic' => '4',
    'widths' => 
    array (
      'label' => '10',
      'field' => '30',
    ),
  ),
);