<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('modules/SugarFeed/feedLogicBase.php');
require_once('modules/SugarFeed/SugarFeed.php');

class ThreadFeed extends FeedLogicBase {
    var $module = "Threads";
    function pushFeed($bean, $event, $arguments){
    	$GLOBALS['log']->debug("*********************** ASOL: adding Thread feed");
    	global $mod_strings;
        $text = '';
        if(empty($bean->fetched_row)){
            $text = $mod_strings['LBL_CREATED_THREAD']." [".$bean->module_dir.":".$bean->id.":".$bean->title."]";
        }else{
            $text = $mod_strings['LBL_UPDATED_THREAD']." [".$bean->module_dir.":".$bean->id.":".$bean->title."]";
        }
		
        if(!empty($text)){ 
            SugarFeed::pushFeed($text, $bean->module_dir, $bean->id, $bean->assigned_user_id);
        }
    }
}
?>
