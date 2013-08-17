<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$popupMeta = array(
	'moduleMain' => 'Thread',
	'varName' => 'THREAD',
	'className' => 'Thread',
	'orderBy' => 'title',
	'whereClauses' => array(
		'title' => 'threads.title',
		'created_by' => 'users.user_name',
	),
	'searchInputs' =>
		array('title', 'created_by')
	);					
?>
