<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$subpanel_layout = array(
	'top_buttons' => array(
        array('widget_class' => 'SubPanelTopCreateButton'),
        array('widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Posts'),
	),
	'where' => '',
	'default_order_by' => 'posts.date_modified',

	'list_fields' => array(
		'title'=>array(
			'vname' => 'LBL_TITLE',
			'widget_class' => 'SubPanelDetailViewLink',
			'width' => '75%',
		),
/*
		'created_by'=> array(
		    'vname' => 'LBL_CREATED_BY',
		    'widget_class' => 'SubPanelDetailViewLink',
		    'width' => '15%',
		),
*/
        'thread_id' => array(
           'usage' => 'query_only',        
        ),
        'date_modified' => array(
            'vname' => 'LBL_DATE_MODIFIED',
		    'width' => '15%',
        ),
		'edit_button'=>array(
			'widget_class' => 'SubPanelEditButton',
		 	'module' => 'Posts',
			'width' => '5%',
		),
		'remove_button'=>array(
			'widget_class' => 'SubPanelRemoveButton',
		 	'module' => 'Posts',
			'width' => '5%',
		),
	),
);
?>
