<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('include/Popups/Popup_picker.php');

$popup = new Popup_Picker();

echo $popup->process_page();
?>
