<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once('modules/ForumTopics/ForumTopic.php');
global $mod_strings, $current_user;

$focus = new ForumTopic();

if (!isset($_REQUEST['record']))
    sugar_die($mod_strings['ERR_DELETE_RECORD']);

$focus->retrieve($_REQUEST['record']);
if (is_admin($current_user) || (!is_admin($current_user) && $current_user->id == $focus->created_by)) {
    if ($focus->can_delete) {
        $focus->mark_deleted($_REQUEST['record']);
    } else {
        sugar_die($mod_strings['ERR_DELETE_USED_TOPIC']);
    }
}
header("Location: index.php?module=" . $_REQUEST['return_module'] . "&action=" . $_REQUEST['return_action'] . "&record=" . $_REQUEST['return_id']);
?>
