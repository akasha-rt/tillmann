<?php
$module_name = 'la_LoginAudit';

global $current_user;

if (!is_admin($current_user) && !is_admin_for_any_module($current_user))
{
   sugar_die("Unauthorized access to Login Audit.");
}


if(!isset($_REQUEST['orderBy'])){
        $_REQUEST['orderBy'] = 'date_entered';
        $_REQUEST['sortOrder'] = 'desc';
}


$listViewDefs [$module_name] = 
array (
  'MODIFIED_BY_NAME' => 
  array (
    'width' => '10%',
    'label' => 'LBL_MODIFIED',
    'default' => true,
    'link' => false,
  ),
  'IP_ADDRESS' => 
  array (
    'width' => '10%',
    'label' => 'LBL_IP_ADDRESS',
    'default' => true,
  ),
  'TYPED_NAME' => 
  array (
    'width' => '10%',
    'label' => 'LBL_TYPED_NAME',
    'default' => true,
  ),
  'DATE_ENTERED' => 
  array (
    'width' => '10%',
    'label' => 'LBL_DATE_ENTERED',
    'default' => true,
  ),
  'IS_ADMIN' => 
  array (
    'width' => '10%',
    'label' => 'LBL_IS_ADMIN',
    'default' => true,
  ),
  'RESULT' => 
  array (
    'width' => '10%',
    'label' => 'LBL_RESULT',
    'default' => true,
  ),
);
//$this->lv->showMassupdateFields = 0;
?>
