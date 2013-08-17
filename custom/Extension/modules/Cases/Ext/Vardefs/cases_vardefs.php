<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$dictionary['Case']['fields']['threads'] = array (
    'name' => 'threads',
    'type' => 'link',
    'relationship' => 'cases_threads',
    'module'=>'threads',
    'bean_name'=>'Threads',
    'source'=>'non-db',
    'vname'=>'LBL_THREADS',
);

$dictionary['Case']['relationships']['cases_threads'] = array(
    'lhs_module'=> 'Cases',
    'lhs_table'=> 'cases',
    'lhs_key' => 'id',
    'rhs_module'=> 'thread',
    'rhs_table'=> 'threads',
    'rhs_key' => 'id',
    'relationship_type'=>'many-to-many',
	'join_table'=> 'cases_threads',
	'join_key_lhs'=>'case_id',
	'join_key_rhs'=>'thread_id'
);
?>
