<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$dictionary['Bug']['fields']['threads'] = array (
    'name' => 'threads',
    'type' => 'link',
    'relationship' => 'bugs_threads',
    'module'=>'threads',
    'bean_name'=>'Threads',
    'source'=>'non-db',
    'vname'=>'LBL_THREADS',
);

$dictionary['Bug']['relationships']['bugs_threads'] = array(
    'lhs_module'=> 'Bugs',
    'lhs_table'=> 'bugs',
    'lhs_key' => 'id',
    'rhs_module'=> 'thread',
    'rhs_table'=> 'threads',
    'rhs_key' => 'id',
    'relationship_type'=>'many-to-many',
	'join_table'=> 'bugs_threads',
	'join_key_lhs'=>'bug_id',
	'join_key_rhs'=>'thread_id'
);
?>
