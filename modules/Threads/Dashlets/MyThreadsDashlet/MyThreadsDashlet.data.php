<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

global $current_user;

$dashletData['MyThreadsDashlet']['searchFields'] = array(
	'title'			=> array('default' => ''),
	'parent_forum'	=> array('default' => ''),
	'date_entered'	=> array('default' => ''),
	'recent_post_date'	=> array('default' => ''));
$dashletData['MyThreadsDashlet']['columns'] = array(
	'title'			=> array(
						'width'		=> '45',
						'label'		=> 'LBL_TITLE',
						'link'		=> true,
						'default'	=> true),
	'parent_forum' => array(
						'width'		=> '20', 
						'label'		=> 'LBL_PARENT_FORUM_NAME',
						'default'	=> true),
	'recent_post_date' => array(
						'width'		=> '20', 
						'label'		=> 'LBL_RECENT_POST_DATE',
						'defaultOrderColumn' => array('sortOrder' => 'DESC'),
						'default'	=> true),
	'recent_post_modified_name' => array(
						'width'		=> '15',
						'label'		=> 'LBL_RECENT_POST_MODIFIED_NAME',
						'default' => true),
);
?>
