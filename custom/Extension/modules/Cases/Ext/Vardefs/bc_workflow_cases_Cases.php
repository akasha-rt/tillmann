<?php
// created: 2013-09-10 15:17:29
$dictionary["Case"]["fields"]["bc_workflow_cases"] = array (
  'name' => 'bc_workflow_cases',
  'type' => 'link',
  'relationship' => 'bc_workflow_cases',
  'source' => 'non-db',
  'vname' => 'LBL_BC_WORKFLOW_CASES_FROM_BC_WORKFLOW_TITLE',
  'id_name' => 'bc_workflow_casesbc_workflow_ida',
);
$dictionary["Case"]["fields"]["bc_workflow_cases_name"] = array (
  'name' => 'bc_workflow_cases_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_BC_WORKFLOW_CASES_FROM_BC_WORKFLOW_TITLE',
  'save' => true,
  'id_name' => 'bc_workflow_casesbc_workflow_ida',
  'link' => 'bc_workflow_cases',
  'table' => 'bc_workflow',
  'module' => 'bc_WorkFlow',
  'rname' => 'name',
);
$dictionary["Case"]["fields"]["bc_workflow_casesbc_workflow_ida"] = array (
  'name' => 'bc_workflow_casesbc_workflow_ida',
  'type' => 'link',
  'relationship' => 'bc_workflow_cases',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'right',
  'vname' => 'LBL_BC_WORKFLOW_CASES_FROM_CASES_TITLE',
);
