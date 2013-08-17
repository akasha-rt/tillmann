<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('modules/Threads/Thread.php');
require_once('include/utils.php');
global $current_user;

$focus =& new Thread();

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
foreach ($focus->additional_column_fields as $field){
	if (isset($_POST[$field])) {
		$focus->$field=$_POST[$field];		
	}
}

if(!empty($focus->id)){
	if (isset($_POST['is_sticky']) && ($_POST['is_sticky'] == '1' || $_POST['is_sticky'] == 'on'))
		$focus->is_sticky = 1;
	else
		$focus->is_sticky = 0;
}
else{
	if (isset($_POST['is_sticky']) && $_POST['is_sticky'] == 'on')
		$focus->is_sticky = 1;
}

$html = trim($focus->description_html);
if(!empty($html)) {
  if(false === stristr($html, '&lt;html')) {
    $focus->description_html = $html;
  }
}


/*
echo "<PRE>";
print_r($_POST);
print_r($focus);
echo "</PRE>";
die('');
*/

$new_thread = false;
if(empty($focus->id))
  $new_thread = true;

$focus->save();

$rs = $focus->db->query("SELECT title FROM forums WHERE id='".$focus->forum_id."'");
$row = $focus->db->fetchByAssoc($rs);
$title = $row['title'];

$rs = $focus->db->query("SELECT user_name FROM users WHERE id='".$focus->modified_user_id."'");
$row = $focus->db->fetchByAssoc($rs);
$user_name = $row['user_name'];

$focus->db->query("UPDATE threads SET parent_forum='".$title."',recent_post_date='".$focus->date_modified."',recent_post_modified_name='".$user_name."' WHERE id='".$focus->id."'");
		
$return_module = (!empty($_POST['return_module'])) ? $_POST['return_module'] : "Forums";
$return_action = (!empty($_POST['return_action'])) ? $_POST['return_action'] : "DetailView";

$return_id = $focus->id;
if($return_module == "Forums" && !empty($focus->forum_id))
  $return_id = $focus->forum_id;
else if(!empty($_POST['return_id']))
  $return_id = $_POST['return_id'];

// increments if this was a new thread created
if($new_thread && !empty($focus->forum_id))
{
  require_once('modules/Forums/Forum.php');
  $parent_forum = new Forum();
  $parent_forum->retrieve($focus->forum_id);
  $parent_forum->incrementThreadCount();
}

header("Location: index.php?action={$return_action}&module={$return_module}&record={$return_id}");	
?>
