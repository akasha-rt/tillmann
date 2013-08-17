<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$app_list_strings['moduleList']['Forums']='Foros';
$app_list_strings['moduleList']['Threads']='Hilos';
$app_list_strings['moduleList']['Posts']='Publicaciones';
$app_list_strings['moduleList']['ForumTopics']='Temas del Foro';

//added this to allow Threads to display as a subpanel without having it as a tab
global $modules_exempt_from_availability_check;
$modules_exempt_from_availability_check['Threads'] = 'Hilos';
?>
