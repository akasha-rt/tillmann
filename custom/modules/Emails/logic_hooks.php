<?php

/**
 * Note Logic hook
 * @author Dhaval Darji
 */
$hook_version = 1;
$hook_array = Array();
$hook_array['after_relationship_add'] = Array();
$hook_array['after_relationship_add'][] = Array(1, 'Attach note to Case', 'custom/modules/Emails/EmailsLogicHook.php', 'EmailsLogicHook', 'attachNotesToCase');
?>
