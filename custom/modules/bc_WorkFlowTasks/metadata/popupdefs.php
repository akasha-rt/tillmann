<?php
$popupMeta = array (
    'moduleMain' => 'bc_WorkFlowTasks',
    'varName' => 'bc_WorkFlowTasks',
    'orderBy' => 'bc_workflowtasks.name',
    'whereClauses' => array (
  'name' => 'bc_workflowtasks.name',
  'bc_workflowtasks_cases_name' => 'bc_workflowtasks.bc_workflowtasks_cases_name',
  'status' => 'bc_workflowtasks.status',
  'task_sequence_c' => 'bc_workflowtasks_cstm.task_sequence_c',
),
    'searchInputs' => array (
  1 => 'name',
  3 => 'status',
  4 => 'bc_workflowtasks_cases_name',
  5 => 'task_sequence_c',
),
    'searchdefs' => array (
  'task_sequence_c' => 
  array (
    'type' => 'int',
    'label' => 'LBL_TASK_SEQUENCE',
    'width' => '10%',
    'name' => 'task_sequence_c',
  ),
  'name' => 
  array (
    'type' => 'name',
    'link' => true,
    'label' => 'LBL_NAME',
    'width' => '10%',
    'name' => 'name',
  ),
  'bc_workflowtasks_cases_name' => 
  array (
    'type' => 'relate',
    'link' => 'bc_workflowtasks_cases',
    'label' => 'LBL_BC_WORKFLOWTASKS_CASES_FROM_CASES_TITLE',
    'width' => '10%',
    'name' => 'bc_workflowtasks_cases_name',
  ),
  'status' => 
  array (
    'type' => 'enum',
    'studio' => 'visible',
    'label' => 'LBL_STATUS',
    'width' => '10%',
    'name' => 'status',
  ),
),
    'listviewdefs' => array (
  'TASK_SEQUENCE_C' => 
  array (
    'type' => 'int',
    'default' => true,
    'label' => 'LBL_TASK_SEQUENCE',
    'width' => '10%',
  ),
  'NAME' => 
  array (
    'type' => 'name',
    'link' => true,
    'label' => 'LBL_NAME',
    'width' => '10%',
    'default' => true,
    'name' => 'name',
  ),
  'BC_WORKFLOWTASKS_CASES_NAME' => 
  array (
    'type' => 'relate',
    'link' => 'bc_workflowtasks_cases',
    'label' => 'LBL_BC_WORKFLOWTASKS_CASES_FROM_CASES_TITLE',
    'width' => '10%',
    'default' => true,
    'name' => 'bc_workflowtasks_cases_name',
  ),
  'STATUS' => 
  array (
    'type' => 'enum',
    'default' => true,
    'studio' => 'visible',
    'label' => 'LBL_STATUS',
    'width' => '10%',
    'name' => 'status',
  ),
  'DATE_ENTERED' => 
  array (
    'type' => 'datetime',
    'label' => 'LBL_DATE_ENTERED',
    'width' => '10%',
    'default' => true,
    'name' => 'date_entered',
  ),
),
);
