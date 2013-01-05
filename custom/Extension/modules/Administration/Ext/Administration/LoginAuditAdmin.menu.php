<?php

global $theme;

if($theme){
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
} else {
	$image_path = 'themes/default/images/';
}

$admin_option_defs = array();
$admin_option_defs['Administration']['LoginAudit'] = array(
	'icon_Opacus', 'LBL_LOGINAUDIT_ADMIN', 'LBL_LOGINAUDIT_DESCRIPTION', './index.php?module=la_LoginAudit&action=index'
	);
$admin_group_header[]= array('LBL_LOGINAUDIT_GROUP','',false,$admin_option_defs, 'LBL_LOGINAUDIT_GROUP_DESCRIPTION');

?>
