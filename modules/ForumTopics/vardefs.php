<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$dictionary['ForumTopic'] = array(
	'table' => 'forumtopics', 'comment' => 'Topics are used to categorize forums',
	'fields' => array (
		'id' => array (
			'name' => 'id',
			'vname' => 'LBL_ID',
			'type' => 'id',
			'required'=>true,
			'reportable'=>false,
			'comment' => 'Unique identifier',
		),
		'deleted' => array (
			'name' => 'deleted',
			'vname' => 'LBL_DELETED',
			'type' => 'bool',
			'required'=>true,
			'reportable'=>false,
			'comment' => 'Record deletion indicator',
		),
		'date_entered' => array (
			'name' => 'date_entered',
			'vname' => 'LBL_DATE_ENTERED',
			'type' => 'datetime',
			'required'=>true,
			'comment' => 'Date record created',
		),
		'date_modified' => array (
			'name' => 'date_modified',
			'vname' => 'LBL_DATE_MODIFIED',
			'type' => 'datetime',
			'required'=>true,
			'comment' => 'Date record last modified',
		),
		'modified_user_id' => array (
			'name' => 'modified_user_id',
			'rname' => 'user_name',
			'id_name' => 'modified_user_id',
			'vname' => 'LBL_ASSIGNED_TO',
			'type' => 'assigned_user_name',
			'table' => 'users',
			'isnull' => 'false',
			'dbType' => 'id',
			'required'=>true,
			'reportable'=>true,
			'comment' => 'User ID who last modified record',
		),
		'created_by' => array (
			'name' => 'created_by',
			'rname' => 'user_name',
			'id_name' => 'modified_user_id',
			'vname' => 'LBL_ASSIGNED_TO',
			'type' => 'assigned_user_name',
			'table' => 'users',
			'isnull' => 'false',
			'dbType' => 'id',
			'comment' => 'User ID who created record',
		),
		'name' => array (
			'name' => 'name',
			'vname' => 'LBL_NAME',
			'dbType' => 'varchar',
			'type' => 'name',
			'len' => '50',
			'required'=>true,
			'comment' => 'Topic name',
		),
		'list_order' => array (
			'name' => 'list_order',
			'vname' => 'LBL_LIST_ORDER',
			'type' => 'int',
			'len' => '4',
			'comment' => 'Topic list order',
		),
),
'indices' => array (
	array('name' =>'forumtopicspk', 'type' =>'primary', 'fields'=>array('id')),
	array('name' =>'idx_forumtopics', 'type'=>'index', 'fields'=>array('name','deleted')),
)
);
?>
