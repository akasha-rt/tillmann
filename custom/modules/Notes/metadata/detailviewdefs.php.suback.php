<?php
// created: 2016-04-08 13:01:38
$viewdefs['Notes']['DetailView'] = array (
  'templateMeta' => 
  array (
    'maxColumns' => '2',
    'widths' => 
    array (
      0 => 
      array (
        'label' => '10',
        'field' => '30',
      ),
      1 => 
      array (
        'label' => '10',
        'field' => '30',
      ),
    ),
    'useTabs' => false,
    'tabDefs' => 
    array (
      'LBL_NOTE_INFORMATION' => 
      array (
        'newTab' => false,
        'panelDefault' => 'expanded',
      ),
      'LBL_PANEL_ASSIGNMENT' => 
      array (
        'newTab' => false,
        'panelDefault' => 'expanded',
      ),
    ),
  ),
  'panels' => 
  array (
    'lbl_note_information' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'name',
          'label' => 'LBL_SUBJECT',
        ),
        1 => 
        array (
          'name' => 'parent_name',
          'customLabel' => '{sugar_translate label=\'LBL_MODULE_NAME\' module=$fields.parent_type.value}',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'filename',
        ),
        1 => 'contact_name',
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'description',
          'label' => 'LBL_NOTE_STATUS',
        ),
        1 => 
        array (
          'name' => 'type_c',
          'studio' => 'visible',
          'label' => 'LBL_TYPE',
        ),
      ),
    ),
    'LBL_PANEL_ASSIGNMENT' => 
    array (
      0 => 
      array (
        0 => 'assigned_user_name',
        1 => 
        array (
          'name' => 'date_modified',
          'label' => 'LBL_DATE_MODIFIED',
          'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'date_entered',
          'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
        ),
      ),
    ),
  ),
);