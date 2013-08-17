<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
 
$layout_defs['Accounts']['subpanel_setup']['threads'] =  array(
	'order' => 83,
	'module' => 'Threads',
	'sort_order' => 'asc',
	'sort_by' => 'date_modified',
	'get_subpanel_data' => 'threads',
	'add_subpanel_data' => 'thread_id',
	'subpanel_name' => 'default',
	'title_key' => 'LBL_THREADS_SUBPANEL_TITLE',
	'top_buttons' => array(
		array('widget_class' => 'SubPanelTopCreateButton'),
		array('widget_class' => 'SubPanelTopSelectButton'),
	),
);
?>
