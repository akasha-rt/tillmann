<?php

// Do not store anything in this file that is not part of the array or the hook version.  This file will	
// be automatically rebuilt in the future. 
$hook_version = 1;
$hook_array = Array();
// position, file, function 
$hook_array['before_save'] = Array();
$hook_array['before_save'][] = Array(1, 'Cases push feed', 'modules/Cases/SugarFeeds/CaseFeed.php', 'CaseFeed', 'pushFeed');
$hook_array['before_save'][] = Array(2, 'Check if case is created or edited', 'custom/modules/Cases/CaseLogicHook.php', 'CaseLogicHook', 'assignInitialStatus');
$hook_array['before_save'][] = Array(3, 'Queue Notification', 'custom/modules/Cases/CaseLogicHook.php', 'CaseLogicHook', 'queueNotification');
$hook_array['before_save'][] = Array(4, 'Sync Case with external Office', 'custom/modules/Cases/CaseLogicHook.php', 'CaseLogicHook', 'syncCaseWithExternalOffice');


/**
 * Case Logic Hook 
 * To close all Emails related to closed case.
 * @author Dhaval Darji 
 */
$hook_array['after_save'] = Array();
$hook_array['after_save'][] = Array(1, 'Close Emails', 'custom/modules/Cases/CaseLogicHook.php', 'CaseLogicHook', 'closeEmails');
$hook_array['after_save'][] = Array(2, 'Save WorkFlow Task List', 'custom/modules/Cases/CaseLogicHook.php', 'CaseLogicHook', 'saveWorkFlowTask');
/*
$hook_array['after_relationship_add'] = Array();
$hook_array['after_relationship_add'][] = Array(1, 'Open Case on new Email', 'custom/modules/Cases/CaseLogicHook.php', 'CaseLogicHook', 'openCaseOnNewEmail');
 */
?>