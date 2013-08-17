<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

//_ppd($_POST);
require_once('modules/Forums/Forum.php');
require_once('modules/ForumTopics/ForumTopic.php');
global $current_user;

//included for saving category ranking as well as category -- see below
global $app_list_strings;

$focus = new Forum();

if ($_POST['isDuplicate'] != 1) {
	$focus->retrieve($_POST['record']);
}

$focus->explicit=0;
foreach ($focus->column_fields as $field) {
	if (isset($_POST[$field])) {

		if ($field == 'explicit' && $_POST[$field]=='on') {
			$focus->$field=1;
		} else {
			$focus->$field=$_POST[$field];		
		}
	}
}
foreach ($focus->additional_column_fields as $field) {
	if (isset($_POST[$field])) {
		$focus->$field=$_POST[$field];		
	}
}

$seedForumTopic = new ForumTopic();
$topics = $seedForumTopic->get_topics();
$focus->save();
		
$return_module = (!empty($_POST['return_module'])) ? $_POST['return_module'] : "Forums";
$return_action = (!empty($_POST['return_action'])) ? $_POST['return_action'] : "DetailView";
$return_id = $focus->id;

header("Location: index.php?action={$return_action}&module={$return_module}&record={$return_id}");	
?>
