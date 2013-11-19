<?php
$viewdefs ['Cases'] = 
array (
  'EditView' => 
  array (
    'templateMeta' => 
    array (
      'maxColumns' => '2',
      'widths' => 
      array (
        0 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
        1 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
      ),
      'useTabs' => false,
    ),
    'panels' => 
    array (
      'lbl_case_information' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'case_number',
            'type' => 'readonly',
          ),
        ),
        1 => 
        array (
          0 => 'priority',
        ),
        2 => 
        array (
          0 => 'status',
          1 => 'type',
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'name',
            'displayParams' => 
            array (
              'size' => 75,
              'required' => false,
            ),
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'description',
            'nl2br' => true,
          ),
          1 => 
          array (
            'name' => 'product_c',
            'studio' => 'visible',
            'label' => 'LBL_PRODUCT',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'bc_workflow_cases_name',
            'label' => 'LBL_BC_WORKFLOW_CASES_FROM_BC_WORKFLOW_TITLE',
          ),
          1 => 
          array (
            'name' => 'supplier_c',
            'studio' => 'visible',
            'label' => 'LBL_SUPPLIER',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'technical_c',
            'studio' => 'visible',
            'label' => 'LBL_TECHNICAL',
          ),
          1 => 
          array (
            'name' => 'review_c',
            'studio' => 'visible',
            'label' => 'LBL_REVIEW',
          ),
        ),
      ),
      'LBL_PANEL_ASSIGNMENT' => 
      array (
        0 => 
        array (
          0 => 'assigned_user_name',
        ),
      ),
    ),
  ),
);
?>
