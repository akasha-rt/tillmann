<?php
$module_name = 'bc_ExternalOffice';
$viewdefs [$module_name] = 
array (
  'DetailView' => 
  array (
    'templateMeta' => 
    array (
      'form' => 
      array (
        'buttons' => 
        array (
          0 => 'EDIT',
          1 => 'DUPLICATE',
          2 => 'DELETE',
          3 => 'FIND_DUPLICATES',
        ),
      ),
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
      'syncDetailEditViews' => true,
    ),
    'panels' => 
    array (
      'default' => 
      array (
        0 => 
        array (
          0 => 'name',
          1 => 
          array (
            'name' => 'office_code',
            'label' => 'LBL_OFFICE_CODE',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'api_url',
            'label' => 'LBL_API_URL',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'api_user',
            'label' => 'LBL_API_USER',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'api_user_pass',
            'label' => 'LBL_API_USER_PASS',
          ),
        ),
      ),
    ),
  ),
);
?>
