<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$popupMeta = array(
	'moduleMain' => 'Post',
	'varName' => 'POST',
	'className' => 'Post',
	'orderBy' => 'title',
	'whereClauses' => array(
		'name' => 'post.title', 
	),
	'searchInputs' =>array(
		'title'
	),
);
?>
