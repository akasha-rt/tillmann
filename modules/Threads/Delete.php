<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once('modules/Threads/Thread.php');

/*
  Including forum.php so I can pull parent forum to make sure that
  the parent's 'recent_thread_title' isn't this one. If it is,
  we have to remove that from the parent forum so there is no
  reference to this thread
 */
require_once('modules/Forums/Forum.php');

if (!ACLController::checkAccess('Threads', 'delete', true)) {
    ACLController::displayNoAccess(false);
    sugar_cleanup(true);
}

$focus = new Thread();

// creating the forum
$focusParentForum = new Forum();
global $current_user;
if (!isset($_REQUEST['record']))
    sugar_die("A record number must be specified to delete the thread.");
$focus->retrieve($_REQUEST['record']);
if (!is_admin($current_user) && $current_user->id != $focus->created_by) {
    die('Only administrators can delete a Thread');
}


if (!$focus->ACLAccess('Delete')) {
    ACLController::displayNoAccess(true);
    sugar_cleanup(true);
}

$focus->mark_deleted($_REQUEST['record']);

// pull in forum info
$focusParentForum->retrieve($focus->forum_id);

// if condition passing means the parent forum's most recent thread was this one
// SOOOO, we have to set it to inform the user that that thread was deleted
if (!strcmp($focus->id, $focusParentForum->recent_thread_id)) {
    $GLOBALS['db']->query(
            "update forums " .
            "set recent_thread_title='" . $GLOBALS['db']->quote("Thread was deleted by administrator") . "', " .
            "recent_thread_id=0 " .
            "where id='" . $GLOBALS['db']->quote($focusParentForum->id) . "'"
    );
}

// we also have to mark all child posts as deleted
$result = $GLOBALS['db']->query(
        "select id " .
        "from posts " .
        "where thread_id='" . $GLOBALS['db']->quote($focus->id) . "' "
);

require_once('modules/Posts/Post.php');

while ($row = $focus->db->fetchByAssoc($result)) {
    $child_post = new Post();
    $child_post->mark_deleted($row['id']);

    $focus->decrementPostCount();
}

if (!empty($focus->forum_id)) {
    require_once('modules/Forums/Forum.php');
    $parent_forum = new Forum();
    $parent_forum->retrieve($focus->forum_id);
    $parent_forum->decrementThreadCount();
}

header("Location: index.php?module=" . $_REQUEST['return_module'] . "&action=" . $_REQUEST['return_action'] . "&record=" . $_REQUEST['return_id']);
?>
