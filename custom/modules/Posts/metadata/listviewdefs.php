<?php
$listViewDefs ['Posts'] = 
array (
 'TITLE' => 
  array (
    'sortable' => false,
    'width' => '15%',
    'label' => 'LBL_TITLE',
    'default' => true,
    'link' => true,
  ),
  'THREAD_ID' => 
  array (
    'width' => '10%',
    'label' => 'LBL_THREAD_NAME',
    'default' => true,
    //'customCode' => '<a href="index.php?action=DetailView&module=Threads&record="{fields.thread_id.value}" class="listViewTdLinkS1">THREAD_NAME</a>',
    ),
  'CREATED_BY_USER' => 
  array (
    'width' => '15%',
    'label' => 'LBL_CREATED_BY',
    'default' => true,
  ),
  'MODIFIED_USER_ID' => 
  array (
    'width' => '15%',
    'label' => 'LBL_MODIFIED_USER_ID',
    'default' => true,
  ),
 'DATE_MODIFIED' => 
  array (
    'width' => '15%',
    'label' => 'LBL_DATE_MODIFIED',
    'default' => true,
  ),
);
?>
