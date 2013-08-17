<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$dictionary['Opportunity']['fields']['threads'] = array (
    'name' => 'threads',
    'type' => 'link',
    'relationship' => 'opportunities_threads',
    'module'=>'threads',
    'bean_name'=>'Threads',
    'source'=>'non-db',
    'vname'=>'LBL_THREADS',
);

$dictionary['Opportunity']['relationships']['opportunities_threads'] = array(
    'lhs_module'=> 'Opportunities',
    'lhs_table'=> 'opportunities',
    'lhs_key' => 'id',
    'rhs_module'=> 'thread',
    'rhs_table'=> 'threads',
    'rhs_key' => 'id',
    'relationship_type'=>'many-to-many',
	'join_table'=> 'opportunities_threads',
	'join_key_lhs'=>'opportunity_id',
	'join_key_rhs'=>'thread_id'
);
?>
