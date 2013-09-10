<?php
// created: 2013-09-10 15:17:29
$dictionary["bc_WorkFlowTasks"]["fields"]["bc_workflowtasks_cases"] = array (
  'name' => 'bc_workflowtasks_cases',
  'type' => 'link',
  'relationship' => 'bc_workflowtasks_cases',
  'source' => 'non-db',
  'vname' => 'LBL_BC_WORKFLOWTASKS_CASES_FROM_CASES_TITLE',
  'id_name' => 'bc_workflowtasks_casescases_ida',
);
$dictionary["bc_WorkFlowTasks"]["fields"]["bc_workflowtasks_cases_name"] = array (
  'name' => 'bc_workflowtasks_cases_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_BC_WORKFLOWTASKS_CASES_FROM_CASES_TITLE',
  'save' => true,
  'id_name' => 'bc_workflowtasks_casescases_ida',
  'link' => 'bc_workflowtasks_cases',
  'table' => 'cases',
  'module' => 'Cases',
  'rname' => 'name',
);
$dictionary["bc_WorkFlowTasks"]["fields"]["bc_workflowtasks_casescases_ida"] = array (
  'name' => 'bc_workflowtasks_casescases_ida',
  'type' => 'link',
  'relationship' => 'bc_workflowtasks_cases',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'right',
  'vname' => 'LBL_BC_WORKFLOWTASKS_CASES_FROM_BC_WORKFLOWTASKS_TITLE',
);
