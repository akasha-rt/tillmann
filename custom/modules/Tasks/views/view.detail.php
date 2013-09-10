<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once 'include/MVC/View/views/view.detail.php';

class TasksViewDetail extends ViewDetail {

    function __construct() {
        parent::ViewDetail();
    }

    public function getModuleTitle($show_help = true) {
        global $current_user, $db;
        $follow_result = $db->query("SELECT id from followup where module_id='{$this->bean->id}' and deleted=0 and module_name='Task'");
        $follow_row = $db->fetchByAssoc($follow_result);
        $watchIcon = '<script type="text/javascript" src="custom/include/js/Home/add_follow_list.js"></script><h2>';
        if ($follow_row)
            $watchIcon .= '<img src="custom/image/follow2.png" style="height:17px;width:20px;cursor:pointer;" id="' . $this->bean->id . '" onclick="addToWatchList(this,\'' . $current_user->id . '\',\'Task\');" title="Remove from Watch List" />';
        else
            $watchIcon .= '<img src="custom/image/follow1.png" style="height:17px;width:20px;cursor:pointer;" id="' . $this->bean->id . '" onclick="addToWatchList(this,\'' . $current_user->id . '\',\'Task\');" title="Add to Watch List" />';
        $print = '<a href="index.php?module=Tasks&action=index"><img src="themes/Sugar5/images/icon_Tasks_32.gif" alt="Tasks" title="Tasks" align="absmiddle"></a><span class="pointer">»</span>' . $this->bean->name . '</h2>';
        echo $watchIcon . $print;
        parent::getModuleTitle($show_help);
    }

    function display() {
        parent::display();
    }

}

?>