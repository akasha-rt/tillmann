<?php

$hook_version = 1;
$hook_array = array();

$hook_array['before_save'] = array();
$hook_array['before_save'][] = array(0, 'Validate Pass', 'custom/modules/bc_ExternalOffice/bc_ExternalOfficeCustomCode.php', 'bc_ExternalOfficeCustomCode', 'validatePass');
?>
