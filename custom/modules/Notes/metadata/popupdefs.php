<?php
$popupMeta = array (
    'moduleMain' => 'Notes',
    'varName' => 'Note',
    'orderBy' => 'notes.name',
    'whereClauses' => array (
  'name' => 'notes.name',
  'parent_name' => 'notes.parent_name',
  'filename' => 'notes.filename',
  'date_entered' => 'notes.date_entered',
  'type_c' => 'notes_cstm.type_c',
),
    'searchInputs' => array (
  1 => 'name',
  4 => 'parent_name',
  5 => 'filename',
  6 => 'date_entered',
  7 => 'type_c',
),
    'searchdefs' => array (
  'name' => 
  array (
    'name' => 'name',
    'width' => '10%',
  ),
  'parent_name' => 
  array (
    'type' => 'parent',
    'label' => 'LBL_RELATED_TO',
    'width' => '10%',
    'name' => 'parent_name',
  ),
  'filename' => 
  array (
    'type' => 'name',
    'name' => 'filename',
    'width' => '10%',
  ),
  'type_c' => 
  array (
    'type' => 'enum',
    'studio' => 'visible',
    'label' => 'LBL_TYPE',
    'width' => '10%',
    'name' => 'type_c',
  ),
  'date_entered' => 
  array (
    'type' => 'datetime',
    'label' => 'LBL_DATE_ENTERED',
    'width' => '10%',
    'name' => 'date_entered',
  ),
),
);
