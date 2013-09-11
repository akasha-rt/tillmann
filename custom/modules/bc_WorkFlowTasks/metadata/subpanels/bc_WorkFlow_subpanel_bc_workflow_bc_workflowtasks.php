<?php
// created: 2013-09-10 15:56:00
$subpanel_layout['list_fields'] = array (
  'name' => 
  array (
    'vname' => 'LBL_NAME',
    'widget_class' => 'SubPanelDetailViewLink',
    'width' => '30%',
    'default' => true,
  ),
  'description' => 
  array (
    'type' => 'text',
    'vname' => 'LBL_DESCRIPTION',
    'sortable' => false,
    'width' => '30%',
    'default' => true,
  ),
  'date_entered' => 
  array (
    'type' => 'datetime',
    'vname' => 'LBL_DATE_ENTERED',
    'width' => '15%',
    'default' => true,
  ),
  'date_modified' => 
  array (
    'vname' => 'LBL_DATE_MODIFIED',
    'width' => '15%',
    'default' => true,
  ),
  'edit_button' => 
  array (
    'widget_class' => 'SubPanelEditButton',
    'module' => 'bc_WorkFlowTasks',
    'width' => '4%',
    'default' => true,
  ),
//  'remove_button' => 
//  array (
//    'widget_class' => 'SubPanelRemoveButton',
//    'module' => 'bc_WorkFlowTasks',
//    'width' => '5%',
//    'default' => true,
//  ),
);