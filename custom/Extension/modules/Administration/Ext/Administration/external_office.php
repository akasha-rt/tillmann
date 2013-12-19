<?php

$admin_options_defs = array();
$admin_options_defs['bc_ExternalOffice']['create'] = array(
    'bc_ExternalOffice',
    'LBL_EO_CREATE_TITLE',
    'LBL_EO_CREATE_DESC',
    './index.php?module=bc_ExternalOffice&action=EditView'
);
$admin_options_defs['bc_ExternalOffice']['list'] = array(
    'bc_ExternalOffice',
    'LBL_EO_LIST_TITLE',
    'LBL_EO_LIST_DESC',
    './index.php?module=bc_ExternalOffice&action=index'
);
$admin_options_defs['bc_ExternalOffice']['import'] = array(
    'bc_ExternalOffice',
    'LBL_EO_IMPORT_TITLE',
    'LBL_EO_IMPORT_DESC',
    './index.php?module=Import&action=Step1&import_module=bc_ExternalOffice'
);

$admin_group_header[] = array(
    'LBL_EO_GROUP_TITLE',
    '',
    false,
    $admin_options_defs,
);
?>
