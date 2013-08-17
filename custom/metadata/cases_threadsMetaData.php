<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$dictionary['cases_threads'] = array(
	'table' => 'cases_threads',
	'fields' => array(
		array('name' =>'id', 'type' =>'char', 'len'=>'36', 'required'=>true, 'default'=>''),
		array('name' =>'case_id', 'type' =>'char', 'len'=>'36', 'required'=>true, 'default'=>''),
		array('name' =>'thread_id', 'type' =>'char', 'len'=>'36', 'required'=>true, 'default'=>''),
		array('name' =>'deleted', 'type' =>'tinyint', 'len'=>'1', 'required'=>true, 'default'=>'0'),
		array('name' =>'relationship_type', 'type' =>'varchar', 'len'=>'50', 'required'=>true, 'default'=>'Cases'),
		array('name' =>'date_modified', 'type' =>'datetime'),
	),
	'relationships' => array(
		'cases_threads' => array(
			'lhs_module'=> 'Cases',
			'lhs_table'=> 'cases',
			'lhs_key' => 'id',
			'rhs_module'=> 'Threads',
			'rhs_table'=> 'threads',
			'rhs_key' => 'id',
			'join_table' => 'cases_threads',
			'join_key_lhs' => 'case_id',
			'join_key_rhs' => 'thread_id',
			'relationship_type'=>'many-to-many',
		)
	),
	'indices' => array (
		array(
			'name' =>'cases_threadspk',
			'type' =>'primary',
			'fields'=>array('id')
		),
		array(
			'name' =>'idx_cas_thr_cas',
			'type' =>'index',
			'fields'=>array('case_id')
		),
		array(
			'name' =>'idx_cas_thr_thr',
			'type' =>'index',
			'fields'=>array('thread_id')
		),
		array(
			'name' => 'idx_cases_threads',
			'type'=>'alternate_key',
			'fields'=> array(
				'case_id',
				'thread_id',
			)
		)
	)
);
?>
