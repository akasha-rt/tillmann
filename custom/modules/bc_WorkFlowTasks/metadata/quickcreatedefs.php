<?php
$module_name = 'bc_WorkFlowTasks';
$viewdefs [$module_name] = 
array (
  'QuickCreate' => 
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
            'label' => 'LBL_BC_WORKFLOW_BC_WORKFLOWTASKS_FROM_BC_WORKFLOW_TITLE',
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
            'label' => 'LBL_BC_WORKFLOWTASKS_CASES_FROM_CASES_TITLE',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'description',
            'comment' => 'Full text of the note',
            'label' => 'LBL_DESCRIPTION',
          ),
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
