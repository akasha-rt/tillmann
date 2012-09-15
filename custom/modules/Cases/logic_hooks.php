<?php

// Do not store anything in this file that is not part of the array or the hook version.  This file will	
// be automatically rebuilt in the future. 
$hook_version = 1;
$hook_array = Array();
// position, file, function 
$hook_array['before_save'] = Array();
$hook_array['before_save'][] = Array(1, 'Cases push feed', 'modules/Cases/SugarFeeds/CaseFeed.php', 'CaseFeed', 'pushFeed');


/**
 * Case Logic Hook 
 * To close all Emails related to closed case.
 * @author Dhaval Darji 
 */
$hook_array['after_save'] = Array();
$hook_array['after_save'][] = Array(1, 'Close Emails', 'custom/modules/Cases/CaseLogicHook.php', 'CaseLogicHook', 'closeEmails');

$hook_array['before_save'][] = Array(2, 'Queue Notification', 'custom/modules/Cases/CaseLogicHook.php', 'CaseLogicHook', 'queueNotification');
?>