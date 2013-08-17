<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('XTemplate/xtpl.php');
require_once('data/Tracker.php');
require_once('modules/Posts/Post.php');
require_once('modules/Posts/Forms.php');
require_once('include/DetailView/DetailView.php');

global $mod_strings;
global $app_strings;
global $app_list_strings;

$log = LoggerManager::getLogger('post_detailview');

if(!ACLController::checkAccess('Posts', 'view', true)){
    ACLController::displayNoAccess(false);
    sugar_cleanup(true);
}

$focus =& new Post();
$detailView = new DetailView();
$offset=0;

if (isset($_REQUEST['offset']) or isset($_REQUEST['record'])) {
	$result = $detailView->processSugarBean("POST", $focus, $offset);
	if($result == null) {
	    sugar_die($app_strings['ERROR_NO_RECORD']);
	}
	$focus=$result;
} else {
	header("Location: index.php?module=Forums&action=index");
}

if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$focus->id = "";
}

$log->info("Post DetailView");

if ($_REQUEST['module'] == "Posts")
{
  echo "\n<p>\n";
  echo get_module_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_MODULE_NAME'].": ".$focus->title, true);
  echo "\n</p>\n";
}

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
//require_once($theme_path.'layout_utils.php');

$log->info("Post detail view");

if ($_REQUEST['module'] == "Posts")
  $xtpl=new XTemplate ('modules/Posts/DetailView.html');
else
{
  $xtpl=new XTemplate ('modules/Posts/DetailViewForThreads.html');
}
$xtpl->assign("MOD", $mod_strings);
$xtpl->assign("APP", $app_strings);
$xtpl->assign("THEME", $theme);
$xtpl->assign("GRIDLINE", $gridline);
$xtpl->assign("IMAGE_PATH", $image_path); $xtpl->assign("PRINT_URL", "index.php?".$GLOBALS['request_string']);
$xtpl->assign("ID", $focus->id);
if(isset($focus->description_html)) $xtpl->assign('DESCRIPTION_HTML', from_html($focus->description_html));
$xtpl->assign("RETURN_MODULE", "Posts");
$xtpl->assign("RETURN_ACTION", "DetailView");
$xtpl->assign("ACTION", "EditView");
$xtpl->assign("CREATED_BY", $focus->created_by);
$xtpl->assign("MODIFIED_BY", $focus->modified_user_id);
$xtpl->assign("CREATED_BY_USER", get_assigned_user_name($focus->created_by));
$xtpl->assign("MODIFIED_BY_USER", get_assigned_user_name($focus->modified_user_id));
$xtpl->assign("DATE_ENTERED", $focus->date_entered);
$xtpl->assign("DATE_MODIFIED", $focus->date_modified);
$xtpl->assign("THREAD_ID", $focus->thread_id);

$xtpl->assign("TITLE", $focus->title);
$desc_html=iconv($app_strings['LBL_CHARSET'],"ISO-8859-1",$focus->description_html);
$desc_html=html_entity_decode($desc_html, ENT_QUOTES, 'ISO-8859-1');
$desc_html=iconv("ISO-8859-1",$app_strings['LBL_CHARSET'],$desc_html);
$xtpl->assign("DESCRIPTION_HTML", $desc_html);

//Add Custom Fields
require_once('modules/DynamicFields/templates/Files/DetailView.php');

if($focus->date_modified != $focus->date_entered || $focus->modified_user_id != $focus->created_by)
{
  $xtpl->parse("main.modified");
}
  
if(is_admin($current_user) || $current_user->id == $focus->created_by)
  $xtpl->parse("main.owner_or_admin");

$xtpl->parse("main");
$xtpl->out("main");
?>
