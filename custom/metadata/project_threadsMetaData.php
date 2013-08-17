<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$dictionary['project_threads'] = array(
	'table' => 'project_threads',
	'fields' => array(
		array('name' =>'id', 'type' =>'char', 'len'=>'36', 'required'=>true, 'default'=>''),
		array('name' =>'project_id', 'type' =>'char', 'len'=>'36', 'required'=>true, 'default'=>''),
		array('name' =>'thread_id', 'type' =>'char', 'len'=>'36', 'required'=>true, 'default'=>''),
		array('name' =>'deleted', 'type' =>'tinyint', 'len'=>'1', 'required'=>true, 'default'=>'0'),
		array('name' =>'relationship_type', 'type' =>'varchar', 'len'=>'50', 'required'=>true, 'default'=>'Project'),
		array('name' =>'date_modified', 'type' =>'datetime'),
	),
	'relationships' => array(
		'project_threads' => array(
			'lhs_module'=> 'Project',
			'lhs_table'=> 'project',
			'lhs_key' => 'id',
			'rhs_module'=> 'Threads',
			'rhs_table'=> 'threads',
			'rhs_key' => 'id',
			'join_table' => 'project_threads',
			'join_key_lhs' => 'project_id',
			'join_key_rhs' => 'thread_id',
			'relationship_type'=>'many-to-many',
		)
	),
	'indices' => array (
		array(
			'name' =>'project_threadspk',
			'type' =>'primary',
			'fields'=>array('id')
		),
		array(
			'name' =>'idx_project_thr_project',
			'type' =>'index',
			'fields'=>array('project_id')
		),
		array(
			'name' =>'idx_project_thr_thr',
			'type' =>'index',
			'fields'=>array('thread_id')
		),
		array(
			'name' => 'idx_project_threads',
			'type'=>'alternate_key',
			'fields'=> array(
				'project_id',
				'thread_id',
			)
		)
	)
);
?>
