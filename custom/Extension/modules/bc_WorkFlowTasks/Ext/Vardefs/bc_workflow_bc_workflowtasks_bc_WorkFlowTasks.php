<?php
// created: 2013-09-10 15:17:29
$dictionary["bc_WorkFlowTasks"]["fields"]["bc_workflow_bc_workflowtasks"] = array (
  'name' => 'bc_workflow_bc_workflowtasks',
  'type' => 'link',
  'relationship' => 'bc_workflow_bc_workflowtasks',
  'source' => 'non-db',
  'vname' => 'LBL_BC_WORKFLOW_BC_WORKFLOWTASKS_FROM_BC_WORKFLOW_TITLE',
  'id_name' => 'bc_workflow_bc_workflowtasksbc_workflow_ida',
);
$dictionary["bc_WorkFlowTasks"]["fields"]["bc_workflow_bc_workflowtasks_name"] = array (
  'name' => 'bc_workflow_bc_workflowtasks_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_BC_WORKFLOW_BC_WORKFLOWTASKS_FROM_BC_WORKFLOW_TITLE',
  'save' => true,
  'id_name' => 'bc_workflow_bc_workflowtasksbc_workflow_ida',
  'link' => 'bc_workflow_bc_workflowtasks',
  'table' => 'bc_workflow',
  'module' => 'bc_WorkFlow',
  'rname' => 'name',
);
$dictionary["bc_WorkFlowTasks"]["fields"]["bc_workflow_bc_workflowtasksbc_workflow_ida"] = array (
  'name' => 'bc_workflow_bc_workflowtasksbc_workflow_ida',
  'type' => 'link',
  'relationship' => 'bc_workflow_bc_workflowtasks',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'right',
  'vname' => 'LBL_BC_WORKFLOW_BC_WORKFLOWTASKS_FROM_BC_WORKFLOWTASKS_TITLE',
);
