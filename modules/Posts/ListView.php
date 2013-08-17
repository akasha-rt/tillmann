<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('XTemplate/xtpl.php');
require_once("data/Tracker.php");
require_once('modules/Posts/Post.php');
//require_once('themes/'.$theme.'/layout_utils.php');

require_once('include/ListView/ListView.php');

global $app_strings;
global $app_list_strings;
global $current_language;
$current_module_strings = return_module_language($current_language, 'Posts');

if(!ACLController::checkAccess('Posts', 'list', true)){
    ACLController::displayNoAccess(false);
    sugar_cleanup(true);
}

global $urlPrefix;

global $currentModule;

global $theme;

// If we are viewing a thread, makes the view limited to posts with
//   a parent of the current thread instead of displaying all posts
// If the action is "ForumsSearch", then they have done a global forums search
//   so we don't change the where
if($_REQUEST['module'] == "Threads")
  $where = " thread_id='".$GLOBALS['db']->quote($_REQUEST['record'])."' ";
else if($_REQUEST['action'] == "ForumsSearch")
  $where = $where;
else
  $where = "";

$seedPost =& new Post();

$ListView = new ListView();
$ListView->show_select_menu = false;
$ListView->show_delete_button = false;
$ListView->show_export_button = false;
$ListView->initNewXTemplate( 'modules/Posts/ListView.html',$current_module_strings);
$ListView->setHeaderTitle($current_module_strings['LBL_LIST_FORM_TITLE'] );

global $current_user;
if(is_admin($current_user) && $_REQUEST['module'] != 'DynamicLayout' && !empty($_SESSION['editinplace'])){
	$ListView->setHeaderText("<a href='index.php?action=index&module=DynamicLayout&from_action=ListView&from_module=".$_REQUEST['module'] ."'>".get_image($image_path."EditLayout","border='0' alt='Edit Layout' align='bottom'")."</a>" );
}

$ListView->setQuery($where, "", "date_modified", "POST");
$ListView->processListView($seedPost, "main", "POST");
?>
