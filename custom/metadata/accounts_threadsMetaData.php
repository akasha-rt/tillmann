<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$dictionary['accounts_threads'] = array(
	'table' => 'accounts_threads',
	'fields' => array(
		array('name' =>'id', 'type' =>'char', 'len'=>'36', 'required'=>true, 'default'=>''),
		array('name' =>'account_id', 'type' =>'char', 'len'=>'36', 'required'=>true, 'default'=>''),
		array('name' =>'thread_id', 'type' =>'char', 'len'=>'36', 'required'=>true, 'default'=>''),
		array('name' =>'deleted', 'type' =>'tinyint', 'len'=>'1', 'required'=>true, 'default'=>'0'),
		array('name' =>'relationship_type', 'type' =>'varchar', 'len'=>'50', 'required'=>true, 'default'=>'Accounts'),
		array('name' =>'date_modified', 'type' =>'datetime'),
	),
	'relationships' => array(
		'accounts_threads' => array(
			'lhs_module'=> 'Accounts',
			'lhs_table'=> 'accounts',
			'lhs_key' => 'id',
			'rhs_module'=> 'Threads',
			'rhs_table'=> 'threads',
			'rhs_key' => 'id',
			'join_table' => 'accounts_threads',
			'join_key_lhs' => 'account_id',
			'join_key_rhs' => 'thread_id',
			'relationship_type'=>'many-to-many',
		)
	),
	'indices' => array (
		array(
			'name' =>'accounts_threadspk',
			'type' =>'primary',
			'fields'=>array('id')
		),
		array(
			'name' =>'idx_acc_thr_acc',
			'type' =>'index',
			'fields'=>array('account_id')
		),
		array(
			'name' =>'idx_acc_thr_thr',
			'type' =>'index',
			'fields'=>array('thread_id')
		),
		array(
			'name' => 'idx_accounts_threads',
			'type'=>'alternate_key',
			'fields'=> array(
				'account_id',
				'thread_id',
			)
		)
	)
);
?>
