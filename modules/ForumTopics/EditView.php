<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$_REQUEST['edit']='true';
include ("modules/ForumTopics/index.php");
include ("modules/ForumTopics/Forms.php");
?>
