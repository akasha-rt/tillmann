<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$dictionary['Account']['fields']['threads'] = array (
    'name' => 'threads',
    'type' => 'link',
    'relationship' => 'accounts_threads',
    'module'=>'threads',
    'bean_name'=>'Threads',
    'source'=>'non-db',
    'vname'=>'LBL_THREADS',
);

$dictionary['Account']['relationships']['accounts_threads'] = array(
    'lhs_module'=> 'Accounts',
    'lhs_table'=> 'accounts',
    'lhs_key' => 'id',
    'rhs_module'=> 'thread',
    'rhs_table'=> 'threads',
    'rhs_key' => 'id',
    'relationship_type'=>'many-to-many',
	'join_table'=> 'accounts_threads',
	'join_key_lhs'=>'account_id',
	'join_key_rhs'=>'thread_id'
);
?>
