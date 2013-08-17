<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$subpanel_layout = array(
	'top_buttons' => array(
        array('widget_class' => 'SubPanelTopCreateButton'),
//        array('widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Threads'),        
	),
	'where' => '',
	'default_order_by' => 'threads.is_sticky desc, threads.date_modified desc',
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
        'forum_id' => array(
           'usage' => 'query_only',        
        ),
        /*
		'is_sticky'=> array(
		    'vname' => 'LBL_IS_STICKY',
		    'width' => '7%',
		),
        */
        'date_modified' => array(
            'vname' => 'LBL_DATE_MODIFIED',
		    'width' => '18%',/* restpre tjos of upi add back in edit and remove buttons below '13%',*/
        ),
		'edit_button'=>array(
			'widget_class' => 'SubPanelEditButton',
		 	'module' => 'Threads',
			'width' => '5%',
		),
		'remove_button'=>array(
			'widget_class' => 'SubPanelRemoveButton',
		 	'module' => 'Threads',
			'width' => '5%',
		),
	),
);
?>
