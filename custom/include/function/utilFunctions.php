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

//Get value for Opp - country
function getCountryForOpp($focus, $name, $value, $view) {
    switch ($view) {
        case 'EditView':
        case 'QuickCreate':
            return get_dd_edit('Country', $focus->$name);
            break;
        default :
            return get_dd_detail('Country', $focus->$name);
            break;
    }
}

//  @niranjan-Start 24/11/2012 for  Priority Task   
function getPrioritytasks() {
    global $db, $current_user;
    $sql_task = $db->query("SELECT
                                    tasks.id as id,
                                    tasks.name as name,
                                    tasks.status as status,
                                    tasks.date_start as date_start, 
                                    tasks.date_due as due_date, 
                                    LTRIM(RTRIM(CONCAT(IFNULL(jt1.first_name,''), ' ',IFNULL(jt1.last_name,'')))) AS assigned_user_name,
                                    tasks.assigned_user_id  
                                  FROM tasks  
                                    LEFT JOIN users jt1
                                      ON tasks.assigned_user_id = jt1.id
                                        AND jt1.deleted = 0
                                    INNER JOIN tasks_cstm t_c
                                    ON t_c.id_c = tasks.id
                                  AND t_c.is_priority_c = 1
                                   WHERE tasks.deleted = 0 
                                   AND tasks.status != 'Completed'
                                   AND tasks.assigned_user_id = '{$current_user->id}'");
    if ($sql_task) {
        $i = 0;
        while ($row_task = $db->fetchByAssoc($sql_task)) {
            $timedate = new TimeDate;
            $task_startdate = $timedate->to_display_date_time($row_task['date_start'], true, true);
            $due_date = $timedate->to_display_date_time($row_task['due_date'], true, true);
            $end_dt = split(" ", $due_date);
            $end_td['g_date'] = str_replace("/", "-", $end_dt['0']);
            $end_td['g_time'] = $end_dt['1'];
            $end_td['g_time'] = date("H:i:s", STRTOTIME($end_td['g_time']));
            $end_date = implode(" ", $end_td);
            $prior_tasks = $row_task['id'] . '#' . $end_date . '#' . $row_task['name'] . '#' . $task_startdate . '#' . $due_date . '#' . $row_task['status'];
            echo "<input type='hidden' name='priority_tasks{$i}' id='priority_tasks{$i}' value='{$prior_tasks}'>";
            $i++;
        }
        echo "<input type='hidden' name='task_count' id='task_count' value='{$i}'>";
    }
}

//  @niranjan-end
?>
