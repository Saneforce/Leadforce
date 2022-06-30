<?php

defined('BASEPATH') or exit('No direct script access allowed');

$filter = [];


$task_statuses = $this->ci->tasks_model->get_statuses();
$_statuses     = [];
foreach ($task_statuses as $status) {
    if ($this->ci->input->post('task_status_' . $status['id'])) {
        array_push($_statuses, $status['id']);
    }
}
$whereclause = '';
if (count($_statuses) == 1) {
        if($_statuses[0] == 3) {
            $whereclause .= ' (date(startdate) = "' . date('Y-m-d') . '" AND '.db_prefix() .'tasks.status != 5) ';
        } elseif ($_statuses[0] == 1) {
            $whereclause .= ' (date(startdate) > "' . date('Y-m-d') . '" AND '.db_prefix() .'tasks.status != 5) ';
        } else {
            $whereclause .= db_prefix() .'tasks.status IN (' . implode(', ', $_statuses) . ') ';
        }
} else {
    $i = 0;
    foreach ($_statuses as $_status) {
        if($_status == 3) {
            if($i > 0)
                $whereclause .= ' OR (date(startdate) = "' . date('Y-m-d') . '" AND '.db_prefix() .'tasks.status != 5) ';
            else
                $whereclause .= ' (date(startdate) = "' . date('Y-m-d') . '" AND '.db_prefix() .'tasks.status != 5) ';
            $i++;
        }
        if($_status == 2) {
            if($i > 0)
                $whereclause .= ' OR '.db_prefix() .'tasks.status = 2 ';
            else
                $whereclause .= db_prefix() .'tasks.status = 2 ';
            $i++;
        }
        if($_status == 1) {
            if($i > 0)
                $whereclause .= ' OR (date(startdate) > "' . date('Y-m-d') . '" AND '.db_prefix() .'tasks.status != 5) ';
            else
                $whereclause .= ' (date(startdate) > "' . date('Y-m-d') . '" AND '.db_prefix() .'tasks.status != 5) ';
            $i++;
        }
        if($_status == 5) {
            if($i > 0)
                $whereclause .= ' OR '.db_prefix() .'tasks.status = 5 ';
            else
                $whereclause .= db_prefix() .'tasks.status = 5 ';
            $i++;
        }
    }
}
if($whereclause) {
    $whereclause = ' ('.$whereclause.') ';
    array_push($filter, $whereclause);
}

if ($this->ci->input->post('my_tasks')) {
    array_push($filter, ' AND (' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . get_staff_user_id() . '))');
}
if ($this->ci->input->post('not_assigned')) {
    array_push($filter, 'AND ' . db_prefix() . 'tasks.id NOT IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned)');
}
if ($this->ci->input->post('due_date_passed')) {
    array_push($filter, 'AND (duedate < "' . date('Y-m-d') . '" AND duedate IS NOT NULL) AND '.db_prefix() .'tasks.status != ' . Tasks_model::STATUS_COMPLETE);
}
if ($this->ci->input->post('recurring_tasks')) {
    array_push($filter, 'AND recurring = 1');
}
if ($this->ci->input->post('today_tasks')) {
    array_push($filter, 'AND date(startdate) = "' . date('Y-m-d') . '" ');
}
if ($this->ci->input->post('tomorrow_tasks')) {
    array_push($filter, 'AND date(startdate) = "' . date("Y-m-d", strtotime("+1 day")) . '"');
}
if ($this->ci->input->post('yesterday_tasks')) {
    array_push($filter, 'AND date(startdate) = "' . date("Y-m-d", strtotime("-1 day")) . '"');
}
if ($this->ci->input->post('thisweek_tasks')) {
    array_push($filter, 'AND (date(startdate) >= "' . date("Y-m-d", strtotime('monday this week')) . '" AND date(startdate) <= "' . date("Y-m-d", strtotime('sunday this week')) . '")');
}
if ($this->ci->input->post('lastweek_tasks')) {
    array_push($filter, 'AND (date(startdate) >= "' . date("Y-m-d", strtotime('monday this week',strtotime("-1 week +1 day"))) . '" AND date(startdate) <= "' . date("Y-m-d", strtotime('sunday this week',strtotime("-1 week +1 day"))) . '")');
}
if ($this->ci->input->post('nextweek_tasks')) {
    array_push($filter, 'AND (date(startdate) >= "' . date("Y-m-d", strtotime('monday this week',strtotime("+1 week -1 day"))) . '" AND date(startdate) <= "' . date("Y-m-d", strtotime('sunday this week',strtotime("+1 week -1 day"))) . '")');
}
if ($this->ci->input->post('thismonth_tasks')) {
    array_push($filter, 'AND (date(startdate) >= "' . date("Y-m-01") . '" AND date(startdate) <= "' . date("Y-m-t") . '")');
}
if ($this->ci->input->post('lastmonth_tasks')) {
    array_push($filter, 'AND (date(startdate) >= "' . date("Y-m-01",strtotime("-1 month")) . '" AND date(startdate) <= "' . date("Y-m-t",strtotime("-1 month")) . '")');
}
if ($this->ci->input->post('nextmonth_tasks')) {
    array_push($filter, 'AND (date(startdate) >= "' . date("Y-m-01",strtotime("+1 month")) . '" AND date(startdate) <= "' . date("Y-m-t",strtotime("+1 month")) . '")');
}
if ($this->ci->input->post('custom_tasks')) {
    array_push($filter, 'AND (date(startdate) >= "' . date("Y-m-d", strtotime($this->ci->input->post('custom_date_start_tasks'))) . '" AND date(startdate) <= "' . date("Y-m-d", strtotime($this->ci->input->post('custom_date_end_tasks'))) . '")');
}
if ($this->ci->input->post('my_following_tasks')) {
    array_push($filter, 'AND (' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_followers WHERE staffid = ' . get_staff_user_id() . '))');
}
if ($this->ci->input->post('billable')) {
    array_push($filter, 'AND billable = 1');
}
if ($this->ci->input->post('billed')) {
    array_push($filter, 'AND billed = 1');
}
if ($this->ci->input->post('not_billed')) {
    array_push($filter, 'AND billable =1 AND billed=0');
}
if ($this->ci->input->post('upcoming_tasks')) {
    array_push($filter, 'AND (date(startdate) > "' . date('Y-m-d') . '") AND '.db_prefix() .'tasks.status != ' . Tasks_model::STATUS_COMPLETE);
}

$assignees  = $this->ci->misc_model->get_tasks_distinct_assignees();
$_assignees = [];
foreach ($assignees as $__assignee) {
    if ($this->ci->input->post('task_assigned_' . $__assignee['assigneeid'])) {
        array_push($_assignees, $__assignee['assigneeid']);
    }
}
if (count($_assignees) > 0) {
    array_push($filter, 'AND (' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid IN (' . implode(', ', $_assignees) . ')))');
}


$tasktypes  = $this->ci->misc_model->get_tasks_distinct_tasktype();
$_tasktypes = [];
foreach ($tasktypes as $__tasktypes) {
    if ($this->ci->input->post('task_tasktype_' . $__tasktypes['id'])) {
        array_push($_tasktypes, $__tasktypes['id']);
    }
}
if (count($_tasktypes) > 0) {
    array_push($filter, 'AND (' . db_prefix() . 'tasks.tasktype IN (' . implode(', ', $_tasktypes) . '))');
}


if (!has_permission('tasks', '', 'view')) {
    array_push($where, get_tasks_where_string());
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

$where = hooks()->apply_filters('tasks_table_sql_where', $where);
