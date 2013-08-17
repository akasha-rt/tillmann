<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('modules/SugarFeed/feedLogicBase.php');
require_once('modules/SugarFeed/SugarFeed.php');

class PostFeed extends FeedLogicBase {
	var $module = "Posts";
	function pushFeed($bean, $event, $arguments){
		$GLOBALS['log']->debug("*********************** ASOL: adding Post feed");
		global $mod_strings;
        $text = '';
        if(empty($bean->fetched_row)){
            $text = $mod_strings['LBL_CREATED_POST']." [".$bean->module_dir.":".$bean->id.":".$bean->title."]";
        }else{
            $text = $mod_strings['LBL_UPDATED_POST']." [".$bean->module_dir.":".$bean->id.":".$bean->title."]";
        }
		
        if(!empty($text)){ 
            SugarFeed::pushFeed($text, $bean->module_dir, $bean->id, $bean->assigned_user_id);
        }
	}
}
?>
