<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$fields_array['Forum'] = array (
	'column_fields' => Array(
		'id',
		'date_entered',
		'category',
		'created_by',
		'date_modified',
		'modified_user_id',
		'created_by_user_name',
		'deleted',
		'title',
	),
	'list_fields' => Array(
		'id',
		'date_entered',
		'category',
		'created_by',
		'date_modified',
		'modified_user_id',
		'created_by_user_name',
		'deleted',
		'title',
	),
	'required_fields' => Array(
		"title"=>1,
	),
);
?>
