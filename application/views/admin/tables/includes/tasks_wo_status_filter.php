<?php

defined('BASEPATH') or exit('No direct script access allowed');

$filter = [];

$CI = &get_instance();

// $task_statuses = $CI->tasks_model->get_statuses();
// $_statuses     = [];
// foreach ($task_statuses as $status) {
//     if ($CI->input->post('task_status_' . $status['id'])) {
//         array_push($_statuses, $status['id']);
//     }
// }
// $whereclause = '';
// if (count($_statuses) == 1) {
//         if($_statuses[0] == 3) {
//             $whereclause .= ' (date(startdate) = "' . date('Y-m-d') . '" AND '.db_prefix() .'tasks.status != 5) ';
//         } elseif ($_statuses[0] == 1) {
//             $whereclause .= ' (date(startdate) > "' . date('Y-m-d') . '" AND '.db_prefix() .'tasks.status != 5) ';
//         } else {
//             $whereclause .= db_prefix() .'tasks.status IN (' . implode(', ', $_statuses) . ') ';
//         }
// } else {
//     $i = 0;
//     foreach ($_statuses as $_status) {
//         if($_status == 3) {
//             if($i > 0)
//                 $whereclause .= ' OR (date(startdate) = "' . date('Y-m-d') . '" AND '.db_prefix() .'tasks.status != 5) ';
//             else
//                 $whereclause .= ' (date(startdate) = "' . date('Y-m-d') . '" AND '.db_prefix() .'tasks.status != 5) ';
//             $i++;
//         }
//         if($_status == 2) {
//             if($i > 0)
//                 $whereclause .= ' OR '.db_prefix() .'tasks.status = 2 ';
//             else
//                 $whereclause .= db_prefix() .'tasks.status = 2 ';
//             $i++;
//         }
//         if($_status == 1) {
//             if($i > 0)
//                 $whereclause .= ' OR (date(startdate) > "' . date('Y-m-d') . '" AND '.db_prefix() .'tasks.status != 5) ';
//             else
//                 $whereclause .= ' (date(startdate) > "' . date('Y-m-d') . '" AND '.db_prefix() .'tasks.status != 5) ';
//             $i++;
//         }
//         if($_status == 5) {
//             if($i > 0)
//                 $whereclause .= ' OR '.db_prefix() .'tasks.status = 5 ';
//             else
//                 $whereclause .= db_prefix() .'tasks.status = 5 ';
//             $i++;
//         }
//     }
// }
// if($whereclause) {
//     $whereclause = ' ('.$whereclause.') ';
//     array_push($filter, $whereclause);
// }
$whereclause = '';
array_push($filter, ' tbltasks.id != "" ');
if ($CI->input->post('my_tasks')) {
    array_push($filter, ' AND (' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . get_staff_user_id() . '))');
}
if ($CI->input->post('not_assigned')) {
    array_push($filter, 'AND ' . db_prefix() . 'tasks.id NOT IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned)');
}
if ($CI->input->post('due_date_passed')) {
    array_push($filter, 'AND (duedate < "' . date('Y-m-d') . '" AND duedate IS NOT NULL) AND '.db_prefix() .'tasks.status != ' . Tasks_model::STATUS_COMPLETE);
}
if ($CI->input->post('recurring_tasks')) {
    array_push($filter, 'AND recurring = 1');
}
if ($CI->input->post('today_tasks')) {
    array_push($filter, 'AND date(startdate) = "' . date('Y-m-d') . '" ');
}
if ($CI->input->post('tomorrow_tasks')) {
    array_push($filter, 'AND date(startdate) = "' . date("Y-m-d", strtotime("+1 day")) . '"');
}
if ($CI->input->post('yesterday_tasks')) {
    array_push($filter, 'AND date(startdate) = "' . date("Y-m-d", strtotime("-1 day")) . '"');
}
if ($CI->input->post('thisweek_tasks')) {
    array_push($filter, 'AND (date(startdate) >= "' . date("Y-m-d", strtotime('monday this week')) . '" AND date(startdate) <= "' . date("Y-m-d", strtotime('sunday this week')) . '")');
}
if ($CI->input->post('lastweek_tasks')) {
    array_push($filter, 'AND (date(startdate) >= "' . date("Y-m-d", strtotime('monday this week',strtotime("-1 week +1 day"))) . '" AND date(startdate) <= "' . date("Y-m-d", strtotime('sunday this week',strtotime("-1 week +1 day"))) . '")');
}
if ($CI->input->post('nextweek_tasks')) {
    array_push($filter, 'AND (date(startdate) >= "' . date("Y-m-d", strtotime('monday this week',strtotime("+1 week -1 day"))) . '" AND date(startdate) <= "' . date("Y-m-d", strtotime('sunday this week',strtotime("+1 week -1 day"))) . '")');
}
if ($CI->input->post('thismonth_tasks')) {
    array_push($filter, 'AND (date(startdate) >= "' . date("Y-m-01") . '" AND date(startdate) <= "' . date("Y-m-t") . '")');
}
if ($CI->input->post('lastmonth_tasks')) {
    array_push($filter, 'AND (date(startdate) >= "' . date("Y-m-01",strtotime("-1 month")) . '" AND date(startdate) <= "' . date("Y-m-t",strtotime("-1 month")) . '")');
}
if ($CI->input->post('nextmonth_tasks')) {
    array_push($filter, 'AND (date(startdate) >= "' . date("Y-m-01",strtotime("+1 month")) . '" AND date(startdate) <= "' . date("Y-m-t",strtotime("+1 month")) . '")');
}
if ($CI->input->post('custom_tasks')) {
    array_push($filter, 'AND (date(startdate) >= "' . date("Y-m-d", strtotime($CI->input->post('custom_date_start_tasks'))) . '" AND date(startdate) <= "' . date("Y-m-d", strtotime($CI->input->post('custom_date_end_tasks'))) . '")');
}
if ($CI->input->post('my_following_tasks')) {
    array_push($filter, 'AND (' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_followers WHERE staffid = ' . get_staff_user_id() . '))');
}
if ($CI->input->post('billable')) {
    array_push($filter, 'AND billable = 1');
}
if ($CI->input->post('billed')) {
    array_push($filter, 'AND billed = 1');
}
if ($CI->input->post('not_billed')) {
    array_push($filter, 'AND billable =1 AND billed=0');
}
if ($CI->input->post('upcoming_tasks')) {
    array_push($filter, 'AND (date(startdate) > "' . date('Y-m-d') . '") AND '.db_prefix() .'tasks.status != ' . Tasks_model::STATUS_COMPLETE);
}

$assignees  = $CI->misc_model->get_tasks_distinct_assignees();
$_assignees = [];
foreach ($assignees as $__assignee) {
    if ($CI->input->post('task_assigned_' . $__assignee['assigneeid'])) {
        array_push($_assignees, $__assignee['assigneeid']);
    }
}
if (count($_assignees) > 0) {
    array_push($filter, 'AND (' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid IN (' . implode(', ', $_assignees) . ')))');
}


$tasktypes  = $CI->misc_model->get_tasks_distinct_tasktype();
$_tasktypes = [];
foreach ($tasktypes as $__tasktypes) {
    if ($CI->input->post('task_tasktype_' . $__tasktypes['id'])) {
        array_push($_tasktypes, $__tasktypes['id']);
    }
}
if (count($_tasktypes) > 0) {
    array_push($filter, 'AND (' . db_prefix() . 'tasks.tasktype IN (' . implode(', ', $_tasktypes) . '))');
}


if (!has_permission('tasks', '', 'view')) {
    array_push($wherewo, get_tasks_where_string());
}

if (count($filter) > 0) {
    array_push($wherewo, 'AND (' . prepare_dt_filter($filter) . ')');
}

$wherewo = hooks()->apply_filters('tasks_table_sql_where', $wherewo);