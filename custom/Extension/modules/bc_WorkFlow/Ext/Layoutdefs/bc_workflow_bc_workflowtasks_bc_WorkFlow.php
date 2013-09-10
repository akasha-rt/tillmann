<?php
 // created: 2013-09-10 15:17:29
$layout_defs["bc_WorkFlow"]["subpanel_setup"]['bc_workflow_bc_workflowtasks'] = array (
  'order' => 100,
  'module' => 'bc_WorkFlowTasks',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_BC_WORKFLOW_BC_WORKFLOWTASKS_FROM_BC_WORKFLOWTASKS_TITLE',
  'get_subpanel_data' => 'bc_workflow_bc_workflowtasks',
  'top_buttons' => 
  array (
    0 => 
    array (
      'widget_class' => 'SubPanelTopButtonQuickCreate',
    ),
    1 => 
    array (
      'widget_class' => 'SubPanelTopSelectButton',
      'mode' => 'MultiSelect',
    ),
  ),
);
