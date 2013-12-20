<?php
$dashletData['bc_WorkFlowTasksDashlet']['searchFields'] = array (
  'task_sequence_c' => 
  array (
    'default' => '',
  ),
  'name' => 
  array (
    'default' => '',
  ),
  'bc_workflowtasks_cases_name' => 
  array (
    'default' => '',
  ),
  'status' => 
  array (
    'default' => '',
  ),
);
$dashletData['bc_WorkFlowTasksDashlet']['columns'] = array (
  'task_sequence_c' => 
  array (
    'type' => 'int',
    'default' => true,
    'label' => 'LBL_TASK_SEQUENCE',
    'width' => '10%',
  ),
  'name' => 
  array (
    'width' => '30%',
    'label' => 'LBL_LIST_NAME',
    'link' => true,
    'default' => true,
    'name' => 'name',
  ),
  'bc_workflowtasks_cases_name' => 
  array (
    'type' => 'relate',
    'link' => 'bc_workflowtasks_cases',
    'label' => 'LBL_BC_WORKFLOWTASKS_CASES_FROM_CASES_TITLE',
    'width' => '20%',
    'default' => true,
    'name' => 'bc_workflowtasks_cases_name',
  ),
  'status' => 
  array (
    'type' => 'enum',
    'default' => true,
    'studio' => 'visible',
    'label' => 'LBL_STATUS',
    'width' => '15%',
    'name' => 'status',
  ),
  'date_entered' => 
  array (
    'width' => '15%',
    'label' => 'LBL_DATE_ENTERED',
    'default' => true,
    'name' => 'date_entered',
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
