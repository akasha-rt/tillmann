<?php

if (!isset($hook_array['after_save'])) {
    $hook_array['after_save'] = array();
}

$hook_array['after_save'][] = array(
    // Processing index. For sorting the array.
    count($hook_array['after_save']),
    
    // Label. A string value to identify the hook.
    'After Save Logic Hook after email gets import into the CRM',

    // The PHP file where your class is located.
    'custom/modules/Emails/AfterSaveLogicHook.php',

    // The class the method is in.
    'AfterSaveLogicHook',

    // The method to call.
    'after_save_hook_handler'
);
