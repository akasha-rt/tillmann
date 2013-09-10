<?php
// created: 2013-09-10 15:17:29
$dictionary["bc_workflowtasks_cases"] = array (
  'true_relationship_type' => 'one-to-many',
  'relationships' => 
  array (
    'bc_workflowtasks_cases' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'lhs_key' => 'id',
      'rhs_module' => 'bc_WorkFlowTasks',
      'rhs_table' => 'bc_workflowtasks',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'bc_workflowtasks_cases_c',
      'join_key_lhs' => 'bc_workflowtasks_casescases_ida',
      'join_key_rhs' => 'bc_workflowtasks_casesbc_workflowtasks_idb',
    ),
  ),
  'table' => 'bc_workflowtasks_cases_c',
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
      'name' => 'bc_workflowtasks_casescases_ida',
      'type' => 'varchar',
      'len' => 36,
    ),
    4 => 
    array (
      'name' => 'bc_workflowtasks_casesbc_workflowtasks_idb',
      'type' => 'varchar',
      'len' => 36,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'bc_workflowtasks_casesspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'bc_workflowtasks_cases_ida1',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'bc_workflowtasks_casescases_ida',
      ),
    ),
    2 => 
    array (
      'name' => 'bc_workflowtasks_cases_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'bc_workflowtasks_casesbc_workflowtasks_idb',
      ),
    ),
  ),
);