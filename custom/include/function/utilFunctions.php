<?php

/**
 * Custom functions
 * @author Dhaval Darji
 */
function get_dd_edit($dd_name = 'Country', $opt = '') {
    global $db;
    $option = ($opt) ? split(",", $opt) : array();

    $dd_res = $db->query("SELECT * FROM bc_dropdown
             WHERE dropdown_id = '{$dd_name}' ORDER BY option_name");
    $option_str = "<option value='' >None</option>";
    while ($dd = $db->fetchByAssoc($dd_res)) {
        $sel = '';
        if (in_array($dd['option_val'], $option)) {
            $sel = "selected";
        }
        $option_str.="<option value='{$dd['option_val']}' {$sel}>{$dd['option_name']}</option>";
    }
    return $option_str;
}

function get_dd_detail($dd_name = 'Country', $opt = '') {
    global $db;
    $option = ($opt != '') ? split(",", $opt) : array(0, 0);
    for ($i = 0; $i < count($option); $i++) {
        $option[$i] = "'" . $option[$i] . "'";
    }
    $option = implode(",", $option);
    $dd_res = $db->query("SELECT GROUP_CONCAT(' ',option_name) as ans FROM bc_dropdown
             WHERE dropdown_id = '{$dd_name}' AND option_val IN ({$option}) ORDER BY option_name");
    $dd = $db->fetchByAssoc($dd_res);
    return $dd['ans'];
}

?>
