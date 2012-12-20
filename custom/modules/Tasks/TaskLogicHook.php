<?php

/**
 * Case Custom code
 * @author Dhaval Darji
 */
class TaskLogicHook {

    /**
     * To call after_save logic hook
     * To close all related emails for closed case
     * @param Object | SugarBean $bean
     * @param type $event
     * @param type $arguments 
     */
    function queueNotification(&$bean, $event, $arguments) {
        if ($bean->fetched_row['assigned_user_id'] != $bean->assigned_user_id) {
            global $db;
            $id = create_guid();
            $sql = "INSERT into notification_queue (id,userid,bean_id,bean_type,date_time,is_notify) 
            VALUES ('{$id}','{$bean->assigned_user_id}','{$bean->id}','{$bean->module_name}',NOW(),0)";
            $db->query($sql);
        }
    }

    function recursiveTask(&$bean, $event, $arguments) {
        require_once("modules/Calendar/CalendarUtils.php");
        global $db;
        if ($bean->is_recursive_c == 1) {
            $days = array('sun', 'mod', 'tue', 'wed', 'thur', 'fri', 'sat',);
            $repeat_type = $_REQUEST['repeat_type'];
            $repeat_delay = $_REQUEST['repeat_delay'];
            $repeat_count = $_REQUEST['repeat_count'];
            $repeat_until_input = $_REQUEST['repeat_until'];
            if (strlen($repeat_until) > 0) {
                $repeat_until = date($GLOBALS['timedate']->get_date_format(), STRTOTIME($repeat_until_input));
            } else {
                $repeat_until = "";
            }
            $repeat_dow = $_REQUEST['repeat_dow'];
            $dow = "";
            foreach ($days as $idx => $day) {
                if ($repeat_dow[$day] == 'on') {
                    $dow .= $idx;
                }
            }
            $params = array(
                'type' => $repeat_type,
                'interval' => $repeat_delay,
                'count' => $repeat_count,
                'until' => $repeat_until,
                'dow' => $dow,
            );

            $repeatArr = CalendarUtils::build_repeat_sequence($_REQUEST['date_start'], $params);
            if (isset($repeatArr) && is_array($repeatArr) && count($repeatArr) > 0) {
                if ($bean->repeat_parent_id_c != "") {
                    $id = $bean->repeat_parent_id_c;
                } else {
                    $id = $bean->id;
                }
                $delete_flag = 0;
                $date_modified = $GLOBALS['timedate']->nowDb();
                $time_arr = $repeatArr;
                foreach ($time_arr as $date_start) {
                    $clone = clone $bean; // we don't use clone keyword cause not necessary
                    $str_temp_arr = explode(" ", $clone->date_start);
                    $R_task_id = create_guid();
                    $timedate = new TimeDate;
                    $str_date = $timedate->to_db_date($date_start);
                    $str_time = $timedate->to_db_time($date_start);
                    $clone->date_start = $str_date . " " . $str_time;
                    $end_date_arr = explode(" ", $clone->date_due);
                    $diff = abs(strtotime($end_date_arr[0]) - strtotime($str_temp_arr[0]));
                    $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                    $due_date = date('Y-m-d', strtotime($clone->date_start . " + {$days} day"));
                    $due_date .=" " . $end_date_arr[1];
                    $clone->repeat_type = $repeat_type;
                    $clone->repeat_interval = $repeat_delay;
                    $clone->repeat_until = $repeat_until_input;
                    $clone->repeat_count = $repeat_count;
                    $clone->recurring_source = "Sugar";
                    $clone->repeat_parent_id = $id;
                    $rpt_dow = array();
                    if ($clone->date_entered == "") {
                        $clone->date_entered = $clone->date_modified;
                    }

                    foreach ($repeat_dow as $wday => $val) {
                        $rpt_dow[] = $wday . "=" . $val;
                    }
                    $clone->repeat_dow_c = implode("||", $rpt_dow);
                    if ($_REQUEST['record'] != "" && $delete_flag == 0) {
                        $get_rec_tasks_sql = "SELECT tasks.id,tasks_cstm.repeat_parent_id_c
                                                FROM tasks
                                                INNER JOIN tasks_cstm
                                                ON tasks_cstm.id_c = tasks.id
                                                AND tasks.deleted = 0
                                                AND tasks_cstm.repeat_parent_id_c = '{$bean->repeat_parent_id_c}'
                                                AND tasks.id != '{$bean->id}'";
                        $get_rec_tasks_res = $db->query($get_rec_tasks_sql);
                        while ($Recursive_result = $db->fetchByAssoc($get_rec_tasks_res)) {
                            $db->query("DELETE FROM tasks WHERE tasks.id = '{$Recursive_result['id']}'");
                            $db->query("DELETE FROM tasks_cstm WHERE tasks_cstm.id_c = '{$Recursive_result['id']}'");
                        }
                        $delete_flag = 1;
                    }
                    $R_task_sql = "INSERT INTO tasks
                                                    (id,
                                                     NAME,
                                                     date_entered,
                                                     date_modified,
                                                     modified_user_id,
                                                     created_by,
                                                     description,
                                                     deleted,
                                                     assigned_user_id,
                                                     STATUS,
                                                     date_due_flag,
                                                     date_due,
                                                     date_start_flag,
                                                     date_start,
                                                     parent_type,
                                                     parent_id,
                                                     contact_id,
                                                     priority)
                                        VALUES ('{$R_task_id}',
                                                '{$clone->name}',
                                                '{$clone->date_entered}',
                                                '{$clone->date_modified}',
                                                '{$clone->modified_user_id}',
                                                '{$clone->created_by}',
                                                '{$clone->description}',
                                                0,
                                                '{$clone->assigned_user_id}',
                                                '{$clone->status}',
                                                0,
                                                '{$due_date}',
                                                0,
                                                '{$clone->date_start}',
                                                '{$clone->parent_type}',
                                                '{$clone->parent_id}',
                                                '{$clone->contact_id}',
                                                '{$clone->priority}')";
                    $R_task_sql_cstm = "INSERT INTO tasks_cstm (
                                                     id_c,
                                                     is_recursive_c,
                                                     repeat_type_c,
                                                     repeat_interval_c,
                                                     repeat_until_c,
                                                     repeat_count_c,
                                                     repeat_parent_id_c,
                                                     recurring_source_c,
                                                     repeat_dow_c) 
                                              VALUES ('{$R_task_id}',
                                                 1,
                                                '{$clone->repeat_type}',
                                                '{$clone->repeat_interval}',
                                                '{$clone->repeat_until}',
                                                '{$clone->repeat_count}',
                                                '{$clone->repeat_parent_id}',
                                                '{$clone->recurring_source}',
                                                '{$clone->repeat_dow_c}')";

                    $db->query($R_task_sql);
                    $db->query($R_task_sql_cstm);
                }
                $db->query("UPDATE tasks_cstm SET
                                                     repeat_type_c = '{$clone->repeat_type}',
                                                     repeat_interval_c = '{$clone->repeat_interval}',
                                                     repeat_until_c = '{$clone->repeat_until}',
                                                     repeat_count_c = '{$clone->repeat_count}',
                                                     repeat_parent_id_c = '{$clone->repeat_parent_id}',
                                                     recurring_source_c = '{$clone->recurring_source}',
                                                     repeat_dow_c = '{$clone->repeat_dow_c}'    
                                                   WHERE tasks_cstm.id_c = '{$bean->id}' ");
            }
        }
    }

}

?>
