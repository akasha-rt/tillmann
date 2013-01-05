<?php
// Do not store anything in this file that is not part of the array or the hook version.  This file will	
// be automatically rebuilt in the future. 
 $hook_version = 1; 
$hook_array = Array(); 
// position, file, function 
$hook_array['after_login'] = Array(); 
$hook_array['after_login'][] = Array(1, 'SugarFeed old feed entry remover', 'modules/SugarFeed/SugarFeedFlush.php','SugarFeedFlush', 'flushStaleEntries'); 
$hook_array['after_login'][] = Array(100, 'Check Login status', 'modules/la_LoginAudit/hook_functions.php','loginActions', 'updateLoginAudit'); 
$hook_array['login_failed'] = Array(); 
$hook_array['login_failed'][] = Array(99, 'Check Login status', 'modules/la_LoginAudit/hook_functions.php','loginActions', 'updateLoginAudit'); 
$hook_array['before_logout'] = Array(); 
$hook_array['before_logout'][] = Array(101, 'Check Login status', 'modules/la_LoginAudit/hook_functions.php','loginActions', 'updateLoginAudit'); 



?>