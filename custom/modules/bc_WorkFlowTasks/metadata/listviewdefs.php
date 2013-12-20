<?php
$module_name = 'bc_WorkFlowTasks';
$listViewDefs [$module_name] = 
array (
  'TASK_SEQUENCE_C' => 
  array (
    'type' => 'int',
    'default' => true,
    'label' => 'LBL_TASK_SEQUENCE',
    'width' => '10%',
  ),
  'NAME' => 
  array (
    'width' => '32%',
    'label' => 'LBL_NAME',
    'default' => true,
    'link' => true,
  ),
  'STATUS' => 
  array (
    'type' => 'enum',
    'default' => true,
    'studio' => 'visible',
    'label' => 'LBL_STATUS',
    'width' => '10%',
  ),
  'ASSIGNED_USER_NAME' => 
  array (
    'width' => '9%',
    'label' => 'LBL_ASSIGNED_TO_NAME',
    'module' => 'Employees',
    'id' => 'ASSIGNED_USER_ID',
    'default' => true,
  ),
  'DATE_ENTERED' => 
  array (
    'type' => 'datetime',
    'label' => 'LBL_DATE_ENTERED',
    'width' => '10%',
    'default' => true,
  ),
  'BC_WORKFLOW_BC_WORKFLOWTASKS_NAME' => 
  array (
    'type' => 'relate',
    'link' => 'bc_workflow_bc_workflowtasks',
    'label' => 'LBL_BC_WORKFLOW_BC_WORKFLOWTASKS_FROM_BC_WORKFLOW_TITLE',
    'width' => '10%',
    'default' => false,
  ),
  'NOTE' => 
  array (
    'type' => 'text',
    'studio' => 'visible',
    'label' => 'LBL_NOTE',
    'sortable' => false,
    'width' => '10%',
    'default' => false,
  ),
  'BC_WORKFLOWTASKS_CASES_NAME' => 
  array (
    'type' => 'relate',
    'link' => 'bc_workflowtasks_cases',
    'label' => 'LBL_BC_WORKFLOWTASKS_CASES_FROM_CASES_TITLE',
    'width' => '10%',
    'default' => false,
  ),
);
?>
