<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$dictionary['Project']['fields']['threads'] = array (
    'name' => 'threads',
    'type' => 'link',
    'relationship' => 'project_threads',
    'module'=>'threads',
    'bean_name'=>'Threads',
    'source'=>'non-db',
    'vname'=>'LBL_THREADS',
);

$dictionary['Project']['relationships']['project_threads'] = array(
    'lhs_module'=> 'Project',
    'lhs_table'=> 'project',
    'lhs_key' => 'id',
    'rhs_module'=> 'thread',
    'rhs_table'=> 'threads',
    'rhs_key' => 'id',
    'relationship_type'=>'many-to-many',
	'join_table'=> 'project_threads',
	'join_key_lhs'=>'project_id',
	'join_key_rhs'=>'thread_id'
);
?>
