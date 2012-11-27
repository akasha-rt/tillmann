<?php

$layout_defs['ProspectLists']['subpanel_setup']['leads'] = array(
    'order' => 30,
    'module' => 'Leads',
    'sort_by' => 'last_name, first_name',
    'sort_order' => 'asc',
    'subpanel_name' => 'forTargetList',
    'get_subpanel_data' => 'leads',
    'title_key' => 'LBL_LEADS_SUBPANEL_TITLE',
    'top_buttons' => array(
        array('widget_class' => 'SubPanelTopButtonQuickCreate'),
        array('widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'),
    ),
);

$layout_defs['ProspectLists']['subpanel_setup']['contacts'] = array(
    'order' => 20,
    'module' => 'Contacts',
    'sort_by' => 'last_name, first_name',
    'sort_order' => 'asc',
    'subpanel_name' => 'forTargetList',
    'get_subpanel_data' => 'contacts',
    'title_key' => 'LBL_CONTACTS_SUBPANEL_TITLE',
    'top_buttons' => array(
        array('widget_class' => 'SubPanelTopButtonQuickCreate'),
        array('widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'),
    ),
);
?>
