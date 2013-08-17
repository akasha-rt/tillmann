<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$dictionary['opportunities_threads'] = array(
	'table' => 'opportunities_threads',
	'fields' => array(
		array('name' =>'id', 'type' =>'char', 'len'=>'36', 'required'=>true, 'default'=>''),
		array('name' =>'opportunity_id', 'type' =>'char', 'len'=>'36', 'required'=>true, 'default'=>''),
		array('name' =>'thread_id', 'type' =>'char', 'len'=>'36', 'required'=>true, 'default'=>''),
		array('name' =>'deleted', 'type' =>'tinyint', 'len'=>'1', 'required'=>true, 'default'=>'0'),
		array('name' =>'relationship_type', 'type' =>'varchar', 'len'=>'50', 'required'=>true, 'default'=>'Opportunities'),
		array('name' =>'date_modified', 'type' =>'datetime'),
	),
	'relationships' => array(
		'opportunities_threads' => array(
			'lhs_module'=> 'Opportunities',
			'lhs_table'=> 'opportunities',
			'lhs_key' => 'id',
			'rhs_module'=> 'Threads',
			'rhs_table'=> 'threads',
			'rhs_key' => 'id',
			'join_table' => 'opportunities_threads',
			'join_key_lhs' => 'opportunity_id',
			'join_key_rhs' => 'thread_id',
			'relationship_type'=>'many-to-many',
		)
	),
	'indices' => array (
		array(
			'name' =>'opportunities_threadspk',
			'type' =>'primary',
			'fields'=>array('id')
		),
		array(
			'name' =>'idx_opp_thr_opp',
			'type' =>'index',
			'fields'=>array('opportunity_id')
		),
		array(
			'name' =>'idx_opp_thr_thr',
			'type' =>'index',
			'fields'=>array('thread_id')
		),
		array(
			'name' => 'idx_opportunities_threads',
			'type'=>'alternate_key',
			'fields'=> array(
				'opportunity_id',
				'thread_id',
			)
		)
	)
);
?>
