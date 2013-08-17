<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$layout_defs['Forums'] = array(
	'subpanel_setup' => array(
		'threads' => array(
			'top_buttons' => array(
				array(
					'widget_class' => 'SubPanelTopCreateButton',         
				),
				/*array(
					'widget_class' => 'SubPanelTopSelectButton',
					'popup_module' => 'Threads'
				),*/
			),
			'order' => 10,
			'module' => 'Threads',
			'subpanel_name' => 'default',
			'get_subpanel_data' => 'threads',
			'add_subpanel_data' => 'thread_id',
			'title_key' => 'LBL_THREADS_SUBPANEL_TITLE',
		),
	),
);
?>
