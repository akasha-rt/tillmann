<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$admin_option_defs=array();
$admin_option_defs['ForumTopics']['forum_topics']= array($image_path . 'ForumTopics','LBL_FORUM_TOPICS_TITLE','LBL_FORUM_TOPICS_VERSION','./index.php?module=ForumTopics&action=index');
$admin_group_header[]=array('LBL_FORUM_TOPICS_TITLE','',false,$admin_option_defs, 'LBL_FORUM_TOPICS_DESC');
?>
