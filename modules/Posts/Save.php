<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

//_ppd($_POST);
require_once('modules/Posts/Post.php');
global $current_user;

$focus =& new Post();

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

$new_post = false;
if(empty($focus->id))
  $new_post = true;

$focus->save();

$rs = $focus->db->query("SELECT user_name FROM users WHERE id='".$focus->modified_user_id."'");
$row = $focus->db->fetchByAssoc($rs);
$user_name = $row['user_name'];

$focus->db->query("UPDATE threads SET recent_post_date='".$focus->date_modified."',recent_post_modified_name='".$user_name."' WHERE id='".$focus->thread_id."'");
		
$return_module = (!empty($_POST['return_module'])) ? $_POST['return_module'] : "Threads";
$return_action = (!empty($_POST['return_action'])) ? $_POST['return_action'] : "DetailView";
$return_id = ($return_module == "Threads" && !empty($focus->thread_id)) ? $focus->thread_id : $focus->id;

// increments if this was a new post created
if($new_post)
{
  require_once('modules/Threads/Thread.php');
  
  $parent_thread = new Thread();
  $parent_thread->retrieve($focus->thread_id);
  $parent_thread->incrementPostCount();
}

header("Location: index.php?action={$return_action}&module={$return_module}&record={$return_id}");
?>
