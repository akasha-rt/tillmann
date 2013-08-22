<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('modules/Posts/Post.php');

/*
Including thread.php so I can pull parent forum to make sure that
the parent's 'recent_post_title' isn't this one. If it is,
we have to remove that from the parent thread so there is no
reference to this thread
*/
require_once('modules/Threads/Thread.php');

if(!ACLController::checkAccess('Posts', 'delete', true)){
    ACLController::displayNoAccess(false);
    sugar_cleanup(true);
}
global $current_user;
$focus = new Post();

if(!isset($_REQUEST['record']))
	sugar_die("A record number must be specified to delete the post.");

if(!is_admin($current_user) && $current_user->id != $focus->created_by)
{
	die('Only administrators can delete a Post');
}
	
$focus->retrieve($_REQUEST['record']);
if(!$focus->ACLAccess('Delete')){
	ACLController::displayNoAccess(true);
	sugar_cleanup(true);
}
$focus->mark_deleted($_REQUEST['record']);

// decrements values as applicable
if(!empty($focus->thread_id))
{
  require_once('modules/Threads/Thread.php');

  $parent_thread = new Thread();
  $parent_thread->retrieve($focus->thread_id);
  $parent_thread->decrementPostCount();
}

header("Location: index.php?module=".$_REQUEST['return_module']."&action=".$_REQUEST['return_action']."&record=".$_REQUEST['return_id']);
?>
