<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('modules/Forums/Forum.php');

global $current_user;

$focus = new Forum();

if(!isset($_REQUEST['record']))
	sugar_die("A record number must be specified to delete the forum.");

if(!is_admin($current_user) && $current_user->id != $focus->created_by)
{
	die('Only administrators can delete a Forum');
}
	
$focus->retrieve($_REQUEST['record']);
if(!$focus->ACLAccess('Delete')){
	ACLController::displayNoAccess(true);
	sugar_cleanup(true);
}

$focus->mark_deleted($_REQUEST['record']);

header("Location: index.php?module=".$_REQUEST['return_module']."&action=".$_REQUEST['return_action']."&record=".$_REQUEST['return_id']);
?>
