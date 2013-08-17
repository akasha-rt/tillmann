<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$app_list_strings['moduleList']['Forums']='Forums';
$app_list_strings['moduleList']['Threads']='Threads';
$app_list_strings['moduleList']['Posts']='Posts';
$app_list_strings['moduleList']['ForumTopics']='ForumTopics';

//added this to allow Threads to display as a subpanel without having it as a tab
global $modules_exempt_from_availability_check;
$modules_exempt_from_availability_check['Threads'] = 'Threads';
?>
