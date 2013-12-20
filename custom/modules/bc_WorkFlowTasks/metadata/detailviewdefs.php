<?php
$module_name = 'bc_WorkFlowTasks';
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
            'name' => 'task_sequence_c',
            'label' => 'LBL_TASK_SEQUENCE',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'bc_workflow_bc_workflowtasks_name',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'status',
            'studio' => 'visible',
            'label' => 'LBL_STATUS',
          ),
          1 => 
          array (
            'name' => 'bc_workflowtasks_cases_name',
          ),
        ),
        3 => 
        array (
          0 => 'description',
          1 => 
          array (
            'name' => 'note',
            'studio' => 'visible',
            'label' => 'LBL_NOTE',
          ),
        ),
      ),
    ),
  ),
);
?>
