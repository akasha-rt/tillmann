<?php
// created: 2013-09-10 15:17:29
$dictionary["bc_workflow_bc_workflowtasks"] = array (
  'true_relationship_type' => 'one-to-many',
  'relationships' => 
  array (
    'bc_workflow_bc_workflowtasks' => 
    array (
      'lhs_module' => 'bc_WorkFlow',
      'lhs_table' => 'bc_workflow',
      'lhs_key' => 'id',
      'rhs_module' => 'bc_WorkFlowTasks',
      'rhs_table' => 'bc_workflowtasks',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'bc_workflow_bc_workflowtasks_c',
      'join_key_lhs' => 'bc_workflow_bc_workflowtasksbc_workflow_ida',
      'join_key_rhs' => 'bc_workflow_bc_workflowtasksbc_workflowtasks_idb',
    ),
  ),
  'table' => 'bc_workflow_bc_workflowtasks_c',
  'fields' => 
  array (
    0 => 
    array (
      'name' => 'id',
      'type' => 'varchar',
      'len' => 36,
    ),
    1 => 
    array (
      'name' => 'date_modified',
      'type' => 'datetime',
    ),
    2 => 
    array (
      'name' => 'deleted',
      'type' => 'bool',
      'len' => '1',
      'default' => '0',
      'required' => true,
    ),
    3 => 
    array (
      'name' => 'bc_workflow_bc_workflowtasksbc_workflow_ida',
      'type' => 'varchar',
      'len' => 36,
    ),
    4 => 
    array (
      'name' => 'bc_workflow_bc_workflowtasksbc_workflowtasks_idb',
      'type' => 'varchar',
      'len' => 36,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'bc_workflow_bc_workflowtasksspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'bc_workflow_bc_workflowtasks_ida1',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'bc_workflow_bc_workflowtasksbc_workflow_ida',
      ),
    ),
    2 => 
    array (
      'name' => 'bc_workflow_bc_workflowtasks_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'bc_workflow_bc_workflowtasksbc_workflowtasks_idb',
      ),
    ),
  ),
);