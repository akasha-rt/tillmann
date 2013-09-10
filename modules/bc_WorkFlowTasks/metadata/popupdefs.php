<?php
$popupMeta = array (
    'moduleMain' => 'bc_WorkFlowTasks',
    'varName' => 'bc_WorkFlowTasks',
    'orderBy' => 'bc_workflowtasks.name',
    'whereClauses' => array (
  'name' => 'bc_workflowtasks.name',
  'bc_workflowtasks_cases_name' => 'bc_workflowtasks.bc_workflowtasks_cases_name',
  'status' => 'bc_workflowtasks.status',
),
    'searchInputs' => array (
  1 => 'name',
  3 => 'status',
  4 => 'bc_workflowtasks_cases_name',
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
  'NAME' => 
  array (
    'type' => 'name',
    'link' => true,
    'label' => 'LBL_NAME',
    'width' => '10%',
    'default' => true,
  ),
  'BC_WORKFLOWTASKS_CASES_NAME' => 
  array (
    'type' => 'relate',
    'link' => 'bc_workflowtasks_cases',
    'label' => 'LBL_BC_WORKFLOWTASKS_CASES_FROM_CASES_TITLE',
    'width' => '10%',
    'default' => true,
  ),
  'STATUS' => 
  array (
    'type' => 'enum',
    'default' => true,
    'studio' => 'visible',
    'label' => 'LBL_STATUS',
    'width' => '10%',
  ),
  'DATE_ENTERED' => 
  array (
    'type' => 'datetime',
    'label' => 'LBL_DATE_ENTERED',
    'width' => '10%',
    'default' => true,
  ),
),
);
