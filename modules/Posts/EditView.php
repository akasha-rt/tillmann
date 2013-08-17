<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('XTemplate/xtpl.php');
require_once('data/Tracker.php');
require_once('modules/Posts/Post.php');

require_once('modules/Administration/Administration.php');
require_once('modules/Posts/Forms.php');

$admin = new Administration();
$admin->retrieveSettings("notify");

if(!ACLController::checkAccess('Posts', 'edit', true)){
    ACLController::displayNoAccess(false);
    sugar_cleanup(true);
}

global $app_strings;
global $app_list_strings;
global $mod_strings;

$focus =& new Post();

if (!isset($_REQUEST['record']))
  $_REQUEST['record'] = "";
else {
    $focus->retrieve($_REQUEST['record']);
}

echo "\n<p>\n";
echo get_module_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_MODULE_NAME'].": ".$focus->title, true);
echo "\n</p>\n";

if(!empty($focus->id) && !is_admin($current_user) && $current_user->id != $focus->created_by)
{
  die('Only administrators or author of a post can edit it');
}

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
//require_once($theme_path.'layout_utils.php');

$GLOBALS['log']->info("Post EditView");
$xtpl=new XTemplate ('modules/Posts/EditView.html');
$xtpl->assign("MOD", $mod_strings);
$xtpl->assign("APP", $app_strings);

if (isset($_REQUEST['return_module']))
  $xtpl->assign("RETURN_MODULE", $_REQUEST['return_module']);
if (isset($_REQUEST['return_action']))
  $xtpl->assign("RETURN_ACTION", $_REQUEST['return_action']);

if(!isset($_REQUEST['return_id']))
  die("You should not be able to access the Post EditView directly. A Post is a reply to a thread.");

$xtpl->assign("RETURN_ID", $_REQUEST['return_id']);
$xtpl->assign("THREAD_ID", $_REQUEST['return_id']);

$xtpl->assign("JAVASCRIPT", get_set_focus_js());
$xtpl->assign("IMAGE_PATH", $image_path);
$xtpl->assign("PRINT_URL", "index.php?".$GLOBALS['request_string']);
/*
  var $id;
  var $title;
  var $body;
*/

$xtpl->assign("ID", $focus->id);

// initialize them to default
$title = $focus->title;
$description_html = $focus->description_html;

if($_REQUEST['return_module'] == "Threads" && $_REQUEST['return_action'] == "DetailView")
{
  if(!isset($_REQUEST['pull_reply_title_from']))
  {
    $row =
      $focus->db->fetchByAssoc( $GLOBALS['db']->query(
                        "select title, description_html, created_by ".
                        "from threads ".
                        "where id = '".$GLOBALS['db']->quote($_REQUEST['return_id'])."'"
                         )
                       );
  }
  else
  {
    $row =
      $focus->db->fetchByAssoc( $GLOBALS['db']->query(
                        "select title, description_html, created_by ".
                        "from posts ".
                        "where id = '".$GLOBALS['db']->quote($_REQUEST['pull_reply_title_from'])."'"
                         )
                       );
  }
  
  if(!isset($_REQUEST['editpost']))
  {
    $title = "Re: ".$row['title'];
    
    if(isset($_REQUEST['quote']) && $_REQUEST['quote'] == "1")
    {
        $quote = "<br /><br /><b><i>".$mod_strings['LBL_TEXT_USER']." '".get_assigned_user_name($row['created_by'])."' ".$mod_strings['LBL_TEXT_SAID'].":</i></b>
        <br />
        <table width=\"85%\" cellspacing=\"1\" cellpadding=\"1\" border=\"1\" align=\"center\" summary=\"\" style=\"font-style: italic;\">
          <tbody>
            <tr>
              <td>".html_entity_decode($row['description_html'])."</td>
            </tr>
          </tbody>
        </table>
        <br /><br />
        ";
        
        $description_html = htmlentities($quote, ENT_COMPAT, "UTF-8");
    }
  }
}

$xtpl->assign("TITLE", $title);
$xtpl->assign('DESCRIPTION_HTML', $description_html);
///////////////////////////////////////
////  TEXT EDITOR
if(file_exists('include/SugarTinyMCE.php')) {
    require_once("include/SugarTinyMCE.php");
    $tiny = new SugarTinyMCE();
    $tiny->defaultConfig['cleanup_on_startup']=true;
    $tinyHtml = $tiny->getInstance();
    $xtpl->assign("tiny", $tinyHtml);
	if(!empty($description_html)) {
	    $xtpl->assign('HTML_EDITOR', $description_html);
	}
	$xtpl->parse('main.htmlarea');
}else {
    $xtpl->parse('main.textarea');
}    
////  END TEXT EDITOR
///////////////////////////////////////

$xtpl->assign("THEME", $theme);
$xtpl->parse("main");
$xtpl->out("main");
?>
