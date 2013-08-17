<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$layout_defs['Threads'] = array(
	'subpanel_setup' => array(
    'posts' => array(
      'top_buttons' => array(
        array(
          'widget_class' => 'SubPanelTopCreateButton',         
        ),
        array(
          'widget_class' => 'SubPanelTopSelectButton', 
          'popup_module' => 'Posts'
        ),
      ),
      'order' => 10,
      'module' => 'Posts',
      'subpanel_name' => 'default',
      'get_subpanel_data' => 'posts',
      'add_subpanel_data' => 'post_id',
      'title_key' => 'LBL_POSTS_SUBPANEL_TITLE',
    ),
  ),
);
?>
