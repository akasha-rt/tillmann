<?php
// created: 2013-12-20 14:38:55
$subpanel_layout['list_fields'] = array (
  'task_sequence_c' => 
  array (
    'type' => 'int',
    'default' => true,
    'vname' => 'LBL_TASK_SEQUENCE',
    'width' => '10%',
  ),
  'name' => 
  array (
    'vname' => 'LBL_NAME',
    'widget_class' => 'SubPanelDetailViewLink',
    'width' => '20%',
    'default' => true,
  ),
  'description' => 
  array (
    'type' => 'text',
    'vname' => 'LBL_DESCRIPTION',
    'sortable' => false,
    'width' => '25%',
    'default' => true,
  ),
  'status' => 
  array (
    'type' => 'enum',
    'default' => true,
    'studio' => 'visible',
    'vname' => 'LBL_STATUS',
    'width' => '10%',
  ),
  'note' => 
  array (
    'type' => 'text',
    'studio' => 'visible',
    'vname' => 'LBL_NOTE',
    'sortable' => false,
    'width' => '15%',
    'default' => true,
  ),
  'date_modified' => 
  array (
    'vname' => 'LBL_DATE_MODIFIED',
    'width' => '10%',
    'default' => true,
  ),
  'edit_button' => 
  array (
    'widget_class' => 'SubPanelEditButton',
    'module' => 'bc_WorkFlowTasks',
    'width' => '4%',
    'default' => true,
  ),
);