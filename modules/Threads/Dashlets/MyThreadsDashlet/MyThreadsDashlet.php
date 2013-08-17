<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('include/Dashlets/DashletGeneric.php');
require_once('modules/Threads/Thread.php');

class MyThreadsDashlet extends DashletGeneric { 
    function MyThreadsDashlet($id, $def = null) {
		require('modules/Threads/Dashlets/MyThreadsDashlet/MyThreadsDashlet.data.php');
        $this->loadLanguage('MyThreadsDashlet', 'modules/Threads/Dashlets/'); // load the language strings here

		$this->myItemsOnly = false;
        parent::DashletGeneric($id, $def);
        
        if(empty($def['title'])) $this->title = $this->dashletStrings['LBL_MY_THREADS_TITLE'];
        else $this->title = $def['title'];        

        $this->searchFields = $dashletData['MyThreadsDashlet']['searchFields'];
        $this->columns = $dashletData['MyThreadsDashlet']['columns'];
        
        $this->seedBean = new Thread();        
    }
}
?>
