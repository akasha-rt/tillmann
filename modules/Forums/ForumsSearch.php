<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('XTemplate/xtpl.php');
require_once('modules/Threads/Thread.php');
require_once('modules/Posts/Post.php');
global $mod_strings, $modListHeader;

//main
global $app_strings;

$xtpl=new XTemplate ('modules/Forums/ForumsSearch.html');
$xtpl->assign("MOD", $mod_strings);
$xtpl->assign("APP", $app_strings);
$xtpl->assign("THEME", $theme);
$xtpl->assign("IMAGE_PATH", $image_path); $xtpl->assign("PRINT_URL", "index.php?".$GLOBALS['request_string']);

if(isset($_REQUEST['query_string_title'])) $xtpl->assign("QUERY_STRING_TITLE", $_REQUEST['query_string_title']);
if(isset($_REQUEST['query_string_body'])) $xtpl->assign("QUERY_STRING_BODY", $_REQUEST['query_string_body']);
if(isset($_REQUEST['query_string_user']) && !empty($_REQUEST['query_string_user']))
  $xtpl->assign("QUERY_STRING_USER", get_select_options_with_id(get_user_array(TRUE), $_REQUEST['query_string_user']));
else
  $xtpl->assign("QUERY_STRING_USER", get_select_options_with_id(get_user_array(TRUE), ''));

echo get_module_title("Search", $mod_strings['LBL_FORUM_SEARCH_RESULTS'], true);
$current_module_strings = return_module_language($current_language, 'Forums');
echo get_form_header($current_module_strings['LBL_SEARCH_FORM_TITLE'], "", false);

$xtpl->parse("main");
$xtpl->out("main");
/* remove this comment segment (and the one below) to add blocking for empty searches
if( (isset($_REQUEST['query_string_title']) ||
     isset($_REQUEST['query_string_body'])  ||
     isset($_REQUEST['query_string_user']))
   &&
    (preg_match("/[\w]/", $_REQUEST['query_string_title']) ||
     preg_match("/[\w]/", $_REQUEST['query_string_body'])  ||
     preg_match("/[\w]/", $_REQUEST['query_string_user']))
  )
{
*/
    if(array_key_exists('Forums', $modListHeader))
    {
        if(!isset($_REQUEST['query_string_title'])) $_REQUEST['query_string_title'] = "";
        if(!isset($_REQUEST['query_string_body'])) $_REQUEST['query_string_body'] = "";
        if(!isset($_REQUEST['query_string_user'])) $_REQUEST['query_string_user'] = "";
        $where = Thread::build_generic_where_clause($_REQUEST['query_string_title'], $_REQUEST['query_string_body'], $_REQUEST['query_string_user']);
        include ("modules/Threads/ListView.php");
    
        $where = Post::build_generic_where_clause($_REQUEST['query_string_title'], $_REQUEST['query_string_body'], $_REQUEST['query_string_user']);
        include ("modules/Posts/ListView.php");
    }
/* remove this comment segment (and the one above) to add blocking for empty searches
}
else {
    echo "<br><br><em>".$mod_strings['ERR_ONE_CHAR']."</em>";
    //echo "</td></tr></table>\n";
}
*/
?>
