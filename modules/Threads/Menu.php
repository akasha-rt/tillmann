<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

global $mod_strings, $app_strings, $current_user;
/*if(is_admin($current_user))*/
  $module_menu = Array(
	  Array("index.php?module=Forums&action=index", $mod_strings['LNK_FORUM_LIST'],"ForumList"),
	  Array("index.php?module=Forums&action=EditView&return_module=Forums&return_action=index", $mod_strings['LNK_NEW_FORUM'],"CreateForum"),
//	  Array("index.php?module=Threads&action=index", $mod_strings['LNK_THREAD_LIST'],"ThreadList"),
  );
/*else
  $module_menu = Array(
	  Array("index.php?module=Forums&action=index", $mod_strings['LNK_FORUM_LIST'],"ForumList"),
//	  Array("index.php?module=Threads&action=index", $mod_strings['LNK_THREAD_LIST'],"ThreadList"),
  );*/
?>
