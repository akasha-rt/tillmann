<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$dictionary['bugs_threads'] = array(
	'table' => 'bugs_threads',
	'fields' => array(
		array('name' =>'id', 'type' =>'char', 'len'=>'36', 'required'=>true, 'default'=>''),
		array('name' =>'bug_id', 'type' =>'char', 'len'=>'36', 'required'=>true, 'default'=>''),
		array('name' =>'thread_id', 'type' =>'char', 'len'=>'36', 'required'=>true, 'default'=>''),
		array('name' =>'deleted', 'type' =>'tinyint', 'len'=>'1', 'required'=>true, 'default'=>'0'),
		array('name' =>'relationship_type', 'type' =>'varchar', 'len'=>'50', 'required'=>true, 'default'=>'Bugs'),
		array('name' =>'date_modified', 'type' =>'datetime'),
	),
	'relationships' => array(
		'bugs_threads' => array(
			'lhs_module'=> 'Bugs',
			'lhs_table'=> 'bugs',
			'lhs_key' => 'id',
			'rhs_module'=> 'Threads',
			'rhs_table'=> 'threads',
			'rhs_key' => 'id',
			'join_table' => 'bugs_threads',
			'join_key_lhs' => 'bug_id',
			'join_key_rhs' => 'thread_id',
			'relationship_type'=>'many-to-many',
		)
	),
	'indices' => array (
		array(
			'name' =>'bugs_threadspk',
			'type' =>'primary',
			'fields'=>array('id')
		),
		array(
			'name' =>'idx_bug_thr_bug',
			'type' =>'index',
			'fields'=>array('bug_id')
		),
		array(
			'name' =>'idx_bug_thr_thr',
			'type' =>'index',
			'fields'=>array('thread_id')
		),
		array(
			'name' => 'idx_bugs_threads',
			'type'=>'alternate_key',
			'fields'=> array(
				'bug_id',
				'thread_id',
			)
		)
	)
);
?>
