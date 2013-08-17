<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
/*if (!defined('THEMEPATH'))
  define('THEMEPATH', $theme_path);
require_once(THEMEPATH.'layout_utils.php');*/
global $mod_strings;
echo "\n<p>\n";
echo get_module_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_MODULE_TITLE'], true); 
echo "\n</p>\n";

include ('modules/Threads/ListView.php');
?>
