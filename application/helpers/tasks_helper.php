<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Function that format task status for the final user
 * @param  string  $id    status id
 * @param  boolean $text
 * @param  boolean $clean
 * @return string
 */
function format_task_status($status, $text = false, $clean = false)
{
    if (!is_array($status)) {
        $status = get_task_status_by_id($status);
    }

    $status_name = $status['name'];

    $status_name = hooks()->apply_filters('task_status_name', $status_name, $status);

    if ($clean == true) {
        return $status_name;
    }

    $style = '';
    $class = '';
    if ($text == false) {
        $style = 'border: 1px solid ' . $status['color'] . ';color:' . $status['color'] . ';';
        $class = 'label';
    } else {
        $style = 'color:' . $status['color'] . ';';
    }

    return '<span class="' . $class . '" style="' . $style . '">' . $status_name . '</span>';
}

/**
 * Return predefined tasks priorities
 * @return array
 */
function get_tasks_priorities()
{
    return hooks()->apply_filters('tasks_priorities', [
        [
            'id'     => 1,
            'name'   => _l('task_priority_low'),
             'color' => '#777',

        ],
        [
            'id'     => 2,
            'name'   => _l('task_priority_medium'),
             'color' => '#03a9f4',

        ],
        [
            'id'    => 3,
            'name'  => _l('task_priority_high'),
            'color' => '#ff6f00',
        ],
        [
            'id'    => 4,
            'name'  => _l('task_priority_urgent'),
            'color' => '#fc2d42',
        ],
    ]);
}

/**
 * Get project name by passed id
 * @param  mixed $id
 * @return string
 */
function get_task_subject_by_id($id)
{
    $CI = & get_instance();
    $CI->db->select('name');
    $CI->db->where('id', $id);
    $task = $CI->db->get(db_prefix() . 'tasks')->row();
    if ($task) {
        return $task->name;
    }

    return '';
}

/**
 * Get task status by passed task id
 * @param  mixed $id task id
 * @return array
 */
function get_task_status_by_id($id)
{
    $CI       = &get_instance();
    $statuses = $CI->tasks_model->get_statuses();

    $status = [
      'id'         => 0,
      'bg_color'   => '#333',
      'text_color' => '#333',
      'name'       => '[Status Not Found]',
      'order'      => 1,
      ];

    foreach ($statuses as $s) {
        if ($s['id'] == $id) {
            $status = $s;

            break;
        }
    }

    return $status;
}

/**
 * Format task priority based on passed priority id
 * @param  mixed $id
 * @return string
 */
function task_priority($id)
{
    foreach (get_tasks_priorities() as $priority) {
        if ($priority['id'] == $id) {
            return $priority['name'];
        }
    }

    // Not exists?
    return $id;
}

/**
 * Get and return task priority color
 * @param  mixed $id priority id
 * @return string
 */
function task_priority_color($id)
{
    foreach (get_tasks_priorities() as $priority) {
        if ($priority['id'] == $id) {
            return $priority['color'];
        }
    }

    // Not exists?
    return '#333';
}
/**
 * Format html task assignees
 * This function is used to save up on query
 * @param  string $ids   string coma separated assignee staff id
 * @param  string $names compa separated in the same order like assignee ids
 * @return string
 */
function format_members_by_ids_and_names($ids, $names, $hidden_export_table = true, $image_class = 'staff-profile-image-small')
{
    $outputAssignees = '';
    $exportAssignees = '';

    $assignees   = explode(',', $names);
    $assigneeIds = explode(',', $ids);
    foreach ($assignees as $key => $assigned) {
        $assignee_id = $assigneeIds[$key];
        $assignee_id = trim($assignee_id);
        if ($assigned != '') {
            $outputAssignees .= '<a href="' . admin_url('profile/' . $assignee_id) . '">' .
                staff_profile_image($assignee_id, [
                  $image_class . ' mright5',
                ], 'small', [
                  'data-toggle' => 'tooltip',
                  'data-title'  => $assigned,
                ]) . '</a>';
            $exportAssignees .= $assigned . ', ';
        }
    }

    if ($exportAssignees != '') {
        $outputAssignees .= '<span class="hide">' . mb_substr($exportAssignees, 0, -2) . '</span>';
    }

    return $outputAssignees;
}

function format_display_members_by_ids_and_names($ids, $names, $hidden_export_table = true, $image_class = 'staff-profile-image-small')
{
    $outputAssignees = '';
    $exportAssignees = '';

    $assignees   = explode(',', $names);
    $assigneeIds = explode(',', $ids);
    
    foreach ($assignees as $key => $assigned) {
        $assignee_id = $assigneeIds[$key];
        $assignee_id = trim($assignee_id);
        if ($assigned != '') {
            $outputAssignees .= '<a href="' . admin_url('profile/' . $assignee_id) . '" style="white-space: nowrap;text-overflow: ellipsis;overflow: hidden;">' . $assigned . '</a>, ';
        }
    }

    if ($outputAssignees != '') {
        $outputAssignees =  mb_substr($outputAssignees, 0, -2);
    }

    return $outputAssignees;
}
/**
 * Format task relation name
 * @param  string $rel_name current rel name
 * @param  mixed $rel_id   relation id
 * @param  string $rel_type relation type
 * @return string
 */
function task_rel_name($rel_name, $rel_id, $rel_type)
{
    if ($rel_type == 'invoice') {
        $rel_name = format_invoice_number($rel_id);
    } elseif ($rel_type == 'estimate') {
        $rel_name = format_estimate_number($rel_id);
    } elseif ($rel_type == 'proposal') {
        $rel_name = format_proposal_number($rel_id);
    }

    return $rel_name;
}

/**
 * Task relation link
 * @param  mixed $rel_id   relation id
 * @param  string $rel_type relation type
 * @return string
 */
function task_rel_link($rel_id, $rel_type)
{
    $link = '#';
    if ($rel_type == 'customer') {
        $link = admin_url('clients/client/' . $rel_id);
    } elseif ($rel_type == 'invoice') {
        $link = admin_url('invoices/list_invoices/' . $rel_id);
    } elseif ($rel_type == 'project') {
        $link = admin_url('projects/view/' . $rel_id);
    } elseif ($rel_type == 'estimate') {
        $link = admin_url('estimates/list_estimates/' . $rel_id);
    } elseif ($rel_type == 'contract') {
        $link = admin_url('contracts/contract/' . $rel_id);
    } elseif ($rel_type == 'ticket') {
        $link = admin_url('tickets/ticket/' . $rel_id);
    } elseif ($rel_type == 'expense') {
        $link = admin_url('expenses/list_expenses/' . $rel_id);
    } elseif ($rel_type == 'lead') {
        $link = admin_url('leads/index/' . $rel_id);
    } elseif ($rel_type == 'proposal') {
        $link = admin_url('proposals/list_proposals/' . $rel_id);
    }

    return $link;
}
/**
 * Prepares task array gantt data to be used in the gantt chart
 * @param  array $task task array
 * @return array
 */
function get_task_array_gantt_data($task)
{
    $data           = [];
    $data['values'] = [];
    $values         = [];

    $data['desc'] = $task['name'];
    $data['name'] = '';

    $values['from']  = strftime('%Y/%m/%d', strtotime($task['startdate']));
    $values['to']    = strftime('%Y/%m/%d', strtotime($task['duedate']));
    $values['desc']  = $task['name'] . ' - ' . _l('task_total_logged_time') . ' ' . seconds_to_time_format($task['total_logged_time']);
    $values['label'] = $task['name'];
    if ($task['duedate'] && date('Y-m-d') > $task['duedate'] && $task['status'] != Tasks_model::STATUS_COMPLETE) {
        $values['customClass'] = 'ganttRed';
    } elseif ($task['status'] == Tasks_model::STATUS_COMPLETE) {
        $values['label']       = ' <i class="fa fa-check"></i> ' . $values['label'];
        $values['customClass'] = 'ganttGreen';
    }

    $values['dataObj'] = [
        'task_id' => $task['id'],
    ];

    $data['values'][] = $values;

    return $data;
}
/**
 * Common function used to select task relation name
 * @return string
 */
function tasks_rel_name_select_query()
{
    return '(CASE rel_type
        WHEN "contract" THEN (SELECT subject FROM ' . db_prefix() . 'contracts WHERE ' . db_prefix() . 'contracts.id = ' . db_prefix() . 'tasks.rel_id)
        WHEN "estimate" THEN (SELECT id FROM ' . db_prefix() . 'estimates WHERE ' . db_prefix() . 'estimates.id = ' . db_prefix() . 'tasks.rel_id)
        WHEN "proposal" THEN (SELECT id FROM ' . db_prefix() . 'proposals WHERE ' . db_prefix() . 'proposals.id = ' . db_prefix() . 'tasks.rel_id)
        WHEN "invoice" THEN (SELECT id FROM ' . db_prefix() . 'invoices WHERE ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'tasks.rel_id)
        WHEN "ticket" THEN (SELECT CONCAT(CONCAT("#",' . db_prefix() . 'tickets.ticketid), " - ", ' . db_prefix() . 'tickets.subject) FROM ' . db_prefix() . 'tickets WHERE ' . db_prefix() . 'tickets.ticketid=' . db_prefix() . 'tasks.rel_id)
        WHEN "lead" THEN (SELECT CASE ' . db_prefix() . 'leads.email WHEN "" THEN ' . db_prefix() . 'leads.name ELSE CONCAT(' . db_prefix() . 'leads.name, " - ", ' . db_prefix() . 'leads.email) END FROM ' . db_prefix() . 'leads WHERE ' . db_prefix() . 'leads.id=' . db_prefix() . 'tasks.rel_id)
        WHEN "customer" THEN (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM ' . db_prefix() . 'contacts WHERE userid = ' . db_prefix() . 'clients.userid and is_primary = 1) ELSE company END FROM ' . db_prefix() . 'clients WHERE ' . db_prefix() . 'clients.userid=' . db_prefix() . 'tasks.rel_id)
        WHEN "project" THEN (SELECT CONCAT(CONCAT(CONCAT("#",' . db_prefix() . 'projects.id)," - ",' . db_prefix() . 'projects.name), " - ", (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM ' . db_prefix() . 'contacts WHERE userid = ' . db_prefix() . 'clients.userid and is_primary = 1) ELSE company END FROM ' . db_prefix() . 'clients WHERE userid=' . db_prefix() . 'projects.clientid)) FROM ' . db_prefix() . 'projects WHERE ' . db_prefix() . 'projects.id=' . db_prefix() . 'tasks.rel_id)
        WHEN "expense" THEN (SELECT CASE expense_name WHEN "" THEN ' . db_prefix() . 'expenses_categories.name ELSE
         CONCAT(' . db_prefix() . 'expenses_categories.name, \' (\',' . db_prefix() . 'expenses.expense_name,\')\') END FROM ' . db_prefix() . 'expenses JOIN ' . db_prefix() . 'expenses_categories ON ' . db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category WHERE ' . db_prefix() . 'expenses.id=' . db_prefix() . 'tasks.rel_id)
        ELSE NULL
        END)';
}


/**
 * Tasks html table used all over the application for relation tasks
 * This table is not used for the main tasks table
 * @param  array  $table_attributes
 * @return string
 */
function init_relation_tasks_table1($table_attributes = [])
{
    $table_data = [
        _l('the_number_sign'),
        [
            'name'     => _l('tasks_dt_name'),
            'th_attrs' => [
                'style' => 'min-width:200px',
                ],
            ],
             _l('task_status'),
         [
            'name'     => _l('tasks_dt_datestart'),
            'th_attrs' => [
                'style' => 'min-width:75px',
                ],
            ],
         [
            'name'     => _l('task_duedate'),
            'th_attrs' => [
                'style' => 'min-width:75px',
                'class' => 'duedate',
                ],
            ],
         [
            'name'     => _l('task_assigned'),
            'th_attrs' => [
                'style' => 'min-width:75px',
                ],
            ],
        _l('tags'),
        _l('tasks_list_priority'),
    ];

    array_unshift($table_data, [
        'name'     => '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="rel-tasks"><label></label></div>',
        'th_attrs' => ['class' => ($table_attributes['data-new-rel-type'] !== 'project' ? 'not_visible' : '')],
    ]);

    $custom_fields = get_custom_fields('tasks', [
        'show_on_table' => 1,
    ]);

    foreach ($custom_fields as $field) {
        array_push($table_data, $field['name']);
    }

    $table_data = hooks()->apply_filters('tasks_related_table_columns', $table_data);

    $name = 'rel-tasks';
    if ($table_attributes['data-new-rel-type'] == 'lead') {
        $name = 'rel-tasks-leads';
    }

    $table      = '';
    $CI         = & get_instance();
    $table_name = '.table-' . $name;
	if(!isset($table_attributes['no-filters'])){
		$CI->load->view('admin/tasks/tasks_filter_by', [
			'view_table_name' => $table_name,
		]);
		
	}else{
		unset($table_attributes['no-filters']);
	}
    
    if (has_permission('tasks', '', 'create')) {
        $disabled   = '';
        $table_name = addslashes($table_name);
        if ($table_attributes['data-new-rel-type'] == 'customer' && is_numeric($table_attributes['data-new-rel-id'])) {
            if (total_rows(db_prefix() . 'clients', [
                'active' => 0,
                'userid' => $table_attributes['data-new-rel-id'],
            ]) > 0) {
                $disabled = ' disabled';
            }
        }
        // projects have button on top
        if ($table_attributes['data-new-rel-type'] != 'project') {
            echo "<a href='#' class='btn btn-info pull-left mbot25 mright5 new-task-relation" . $disabled . "' onclick=\"new_task_from_relation('$table_name'); return false;\" data-rel-id='" . $table_attributes['data-new-rel-id'] . "' data-rel-type='" . $table_attributes['data-new-rel-type'] . "'>" . _l('new_task') . '</a>';
        }
    }

    if ($table_attributes['data-new-rel-type'] == 'project') {
        echo "<a href='" . admin_url('tasks/detailed_overview?project_id=' . $table_attributes['data-new-rel-id']) . "' class='btn btn-success pull-right mbot25'>" . _l('detailed_overview') . '</a>';
        echo "<a href='" . admin_url('tasks/list_tasks?project_id=' . $table_attributes['data-new-rel-id'] . '&kanban=true') . "' class='btn btn-default pull-right mbot25 mright5 hidden-xs'>" . _l('view_kanban') . '</a>';
        echo '<div class="clearfix"></div>';
        echo $CI->load->view('admin/tasks/_bulk_actions', ['table' => '.table-rel-tasks'], true);
        echo $CI->load->view('admin/tasks/_summary', ['rel_id' => $table_attributes['data-new-rel-id'], 'rel_type' => 'project', 'table' => $table_name], true);
        echo '<a href="#" data-toggle="modal" data-target="#tasks_bulk_actions" class="hide bulk-actions-btn table-btn" data-table=".table-rel-tasks">' . _l('bulk_actions') . '</a>';
    } elseif ($table_attributes['data-new-rel-type'] == 'customer') {
        echo '<div class="clearfix"></div>';
        echo '<div id="tasks_related_filter">';
        echo '<p class="bold">' . _l('task_related_to') . ': </p>';

        echo '<div class="checkbox checkbox-inline mbot25">
        <input type="checkbox" checked value="customer" disabled id="ts_rel_to_customer" name="tasks_related_to[]">
        <label for="ts_rel_to_customer">' . _l('client') . '</label>
        </div>

        <div class="checkbox checkbox-inline mbot25">
        <input type="checkbox" value="project" id="ts_rel_to_project" name="tasks_related_to[]">
        <label for="ts_rel_to_project">' . _l('projects') . '</label>
        </div>

        <div class="checkbox checkbox-inline mbot25">
        <input type="checkbox" value="invoice" id="ts_rel_to_invoice" name="tasks_related_to[]">
        <label for="ts_rel_to_invoice">' . _l('invoices') . '</label>
        </div>

        <div class="checkbox checkbox-inline mbot25">
        <input type="checkbox" value="estimate" id="ts_rel_to_estimate" name="tasks_related_to[]">
        <label for="ts_rel_to_estimate">' . _l('estimates') . '</label>
        </div>

        <div class="checkbox checkbox-inline mbot25">
        <input type="checkbox" value="contract" id="ts_rel_to_contract" name="tasks_related_to[]">
        <label for="ts_rel_to_contract">' . _l('contracts') . '</label>
        </div>

        <div class="checkbox checkbox-inline mbot25">
        <input type="checkbox" value="ticket" id="ts_rel_to_ticket" name="tasks_related_to[]">
        <label for="ts_rel_to_ticket">' . _l('tickets') . '</label>
        </div>

        <div class="checkbox checkbox-inline mbot25">
        <input type="checkbox" value="expense" id="ts_rel_to_expense" name="tasks_related_to[]">
        <label for="ts_rel_to_expense">' . _l('expenses') . '</label>
        </div>

        <div class="checkbox checkbox-inline mbot25">
        <input type="checkbox" value="proposal" id="ts_rel_to_proposal" name="tasks_related_to[]">
        <label for="ts_rel_to_proposal">' . _l('proposals') . '</label>
        </div>';

        echo '</div>';
    }
    echo "<div class='clearfix'></div>";

    // If new column is added on tasks relations table this will not work fine
    // In this case we need to add new identifier eq task-relation
    $table_attributes['data-last-order-identifier'] = 'tasks';
    $table_attributes['data-default-order']         = get_table_last_order('tasks');

    $table .= render_datatable($table_data, $name, [], $table_attributes);

    return $table;
}

function init_relation_tasks_table($table_attributes = [])
{
	$fields = get_option('deal_fields');
	$tasks_need_fields =get_tasks_need_fields();
    $need_fields =$tasks_need_fields['need_fields'];
	$table_datas = [
	   'id'=>_l('the_number_sign'),
		'task_name'=> [
            'name'     => _l('tasks_dt_name'),
            'th_attrs' => [
                'style' => 'min-width:200px',
                ],
            ],
		'status'=>_l('task_status'),
		'tasktype'=> [
            'name'     => _l('tasktype'),
            'th_attrs' => [
                'style' => 'min-width:75px',
                'class' => 'duedate',
                ],
            ],
		'description'=>[
            'name'     => _l('description'),
            'th_attrs' => [
                'style' => 'min-width:75px',
                ],
            ],
		'startdate'=>[
			'name'     => _l('scheduled_date'),
			'th_attrs' => ['class' => 'duedate'],
		],
		'dateadded'=>[
			'name'     => _l('create_date'),
			'th_attrs' => ['class' => 'duedate'],
		],
		'datemodified'=>[
			'name'     => _l('modified_date'),
			'th_attrs' => ['class' => 'duedate'],
		],
		'datefinished'=>[
			'name'     => _l('finished_date'),
			'th_attrs' => ['class' => 'duedate'],
		],
		'assignees'=>[
            'name'     => _l('task_assigned'),
            'th_attrs' => [
                'style' => 'min-width:75px',
                ],
            ],
		'tags'=>_l('tags'),
		'project_name'=>_l('project_name'),
		'project_status'=>_l('project_status'),
		'project_pipeline'=>_l('pipeline'),
		'company'=>_l('client'),
		'teamleader'=>_l('teamleader'),
		'project_contacts'=>_l('project_contacts'),
		'priority'=>_l('tasks_list_priority'),
	];
	/*$table_data_temp = array(
        'id'=>_l('the_number_sign'),
        'task_name'=>[
            'name'     => _l('tasks_dt_name'),
            'th_attrs' => [
                'style' => 'min-width:200px',
                ],
            ],
              'status'=>_l('task_status'),
         
          'tasktype'=>[
            'name'     => _l('tasktype'),
            'th_attrs' => [
                'style' => 'min-width:75px',
                'class' => 'duedate',
                ],
            ],
            'startdate'=>[
                'name'     => _l('tasks_dt_datestart'),
                'th_attrs' => [
                    'style' => 'min-width:75px',
                    ],
                ],
         'assignees'=>[
            'name'     => _l('task_assigned'),
            'th_attrs' => [
                'style' => 'min-width:75px',
                ],
            ],
        'description'=>[
            'name'     => _l('description'),
            'th_attrs' => [
                'style' => 'min-width:75px',
                ],
            ],
        'tags'=>_l('tags'),
		
    'project_name'=>_l('project_name'),
    'project_status'=>_l('project_status'),
    'company'=>_l('client'),
    'teamleader'=>_l('teamleader'),
    'project_contacts'=>_l('project_contacts'),
        'priority'=>_l('tasks_list_priority'),
    );*/
	/*if(!empty($table_data_temp)){
		foreach($table_data_temp as $cfkey=>$cfval){
			 if(empty($need_fields) || (!in_array($cfkey, $need_fields) )){
				 unset($table_data_temp[$cfkey]);
			 }
		}
	}*/
//	pre($need_fields);
	foreach($table_datas as $ckey=>$cval){ 
		if(!empty($need_fields) && in_array($ckey, $need_fields)){
			$table_data_temp[$ckey] = $cval;
		}
	}

 /*   $custom_fields = get_table_custom_fields('projects');
foreach($custom_fields as $cfkey=>$cfval){
    $table_data_temp[$cfval['slug']] = $cfval['name'];
}
$custom_fields = get_table_custom_fields('contacts');
foreach($custom_fields as $cfkey=>$cfval){
    $table_data_temp[$cfval['slug']] = $cfval['name'];
}
$custom_fields = get_table_custom_fields('customers');
foreach($custom_fields as $cfkey=>$cfval){
    $table_data_temp[$cfval['slug']] = $cfval['name'];
}*/
$custom_fields = get_table_custom_fields('tasks');
foreach($custom_fields as $cfkey=>$cfval){
    $table_data_temp[$cfval['slug']] = $cfval['name'];
}
//pre($table_data_temp);
/*
//pre($table_attributes['data-new-rel-type']);
if ($table_attributes['data-new-rel-type'] !== 'project') {
	$table_data_temp['project_name'] = _l('project_name');
}
if ($table_attributes['data-new-rel-type'] !== 'customer') {
	$table_data_temp['company'] = _l('client');
}
*/
$tasks_list_column_order = (array)json_decode(get_option('tasks_list_column_order_'.$table_attributes['data-new-rel-type'])); //pr($tasks_list_column_order);
$table_data = array();
 foreach($tasks_list_column_order as $ckey=>$cval){
	 if(isset($table_data_temp[$ckey])){
		 $table_data[] =$table_data_temp[$ckey];
	 }
 }
  if(!isset($tasks_list_column_order['startdate'])){
	  $Temp['name'] = $table_data[0];
	  $Temp['th_attrs'] = ['class' => 'duedate'];
	 // $table_data[0] = $Temp;
  }
// pre($table_attributes['data-new-rel-type']);

    array_unshift($table_data, [
        'name'     => '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="rel-tasks"><label></label></div>',
        'th_attrs' => ['class' => ($table_attributes['data-new-rel-type'] !== 'project' ? 'not_visible' : '')],
    ]);

   /* $custom_fields = get_custom_fields('tasks', [
        'show_on_table' => 1,
    ]);

    foreach ($custom_fields as $field) {
        array_push($table_data, $field['name']);
    }*/

    $table_data = hooks()->apply_filters('tasks_related_table_columns', $table_data);

    $name = 'rel-tasks';
    if ($table_attributes['data-new-rel-type'] == 'lead') {
        $name = 'rel-tasks-leads';
    }

    $table      = '';
    $CI         = & get_instance();
    $table_name = '.table-' . $name;
    $CI->load->view('admin/tasks/tasks_filter_by', [
        'view_table_name' => $table_name,'table_attributes'=>$table_attributes
    ]);
    if (has_permission('tasks', '', 'create')) {
        $disabled   = '';
        $table_name = addslashes($table_name);
        if ($table_attributes['data-new-rel-type'] == 'customer' && is_numeric($table_attributes['data-new-rel-id'])) {
            if (total_rows(db_prefix() . 'clients', [
                'active' => 0,
                'userid' => $table_attributes['data-new-rel-id'],
            ]) > 0) {
                $disabled = ' disabled';
            }
        }
        // projects have button on top
        if ($table_attributes['data-new-rel-type'] != 'project' && $table_attributes['data-new-rel-type'] != 'customer' && $table_attributes['data-new-rel-type'] != 'contact') {
            echo "<a href='#' class='btn btn-info pull-left mbot25 mright5 new-task-relation" . $disabled . "' onclick=\"new_task_from_relation('$table_name'); return false;\" data-rel-id='" . $table_attributes['data-new-rel-id'] . "' data-rel-type='" . $table_attributes['data-new-rel-type'] . "'>" . _l('new_task') . '</a>';
        }
    }

    if ($table_attributes['data-new-rel-type'] == 'project') {
        
		//echo "<a href='" . admin_url('tasks/detailed_overview?project_id=' . $table_attributes['data-new-rel-id']) . "' class='btn btn-success pull-right mbot25'>" . _l('detailed_overview') . '</a>';
       
		echo '<a href="#" onclick="new_task_from_relation(undefined,'."'project'".','.$table_attributes['data-new-rel-id'].'); return false;" class="btn btn-info pull-left mbot25 mright5">'._l('new_task').'</a>';
        echo "<a href='" . admin_url('tasks/list_tasks?project_id=' . $table_attributes['data-new-rel-id'] . '&kanban=true') . "' class='btn btn-default pull-left mbot25 mright5 hidden-xs'>" . _l('view_kanban') . '</a>';
        echo '<div class="clearfix"></div>';
        echo $CI->load->view('admin/tasks/_bulk_actions', ['table' => '.table-rel-tasks'], true);
        echo $CI->load->view('admin/tasks/_summary', ['rel_id' => $table_attributes['data-new-rel-id'], 'rel_type' => 'project', 'table' => $table_name], true);
        echo '<a href="#" data-toggle="modal" data-target="#tasks_bulk_actions" class="hide bulk-actions-btn table-btn" data-table=".table-rel-tasks">' . _l('bulk_actions') . '</a>';
    } elseif ($table_attributes['data-new-rel-type'] == 'customer') { /*
        echo '<div class="clearfix"></div>';
        echo '<div id="tasks_related_filter">';
        echo '<p class="bold">' . _l('task_related_to') . ': </p>';

        echo '<div class="checkbox checkbox-inline mbot25">
        <input type="checkbox" checked value="customer" disabled id="ts_rel_to_customer" name="tasks_related_to[]">
        <label for="ts_rel_to_customer">' . _l('client') . '</label>
        </div>

        <div class="checkbox checkbox-inline mbot25">
        <input type="checkbox" value="project" id="ts_rel_to_project" name="tasks_related_to[]">
        <label for="ts_rel_to_project">' . _l('projects') . '</label>
        </div>

        <div class="checkbox checkbox-inline mbot25">
        <input type="checkbox" value="invoice" id="ts_rel_to_invoice" name="tasks_related_to[]">
        <label for="ts_rel_to_invoice">' . _l('invoices') . '</label>
        </div>

        <div class="checkbox checkbox-inline mbot25">
        <input type="checkbox" value="estimate" id="ts_rel_to_estimate" name="tasks_related_to[]">
        <label for="ts_rel_to_estimate">' . _l('estimates') . '</label>
        </div>

        <div class="checkbox checkbox-inline mbot25">
        <input type="checkbox" value="contract" id="ts_rel_to_contract" name="tasks_related_to[]">
        <label for="ts_rel_to_contract">' . _l('contracts') . '</label>
        </div>

        <div class="checkbox checkbox-inline mbot25">
        <input type="checkbox" value="ticket" id="ts_rel_to_ticket" name="tasks_related_to[]">
        <label for="ts_rel_to_ticket">' . _l('tickets') . '</label>
        </div>

        <div class="checkbox checkbox-inline mbot25">
        <input type="checkbox" value="expense" id="ts_rel_to_expense" name="tasks_related_to[]">
        <label for="ts_rel_to_expense">' . _l('expenses') . '</label>
        </div>

        <div class="checkbox checkbox-inline mbot25">
        <input type="checkbox" value="proposal" id="ts_rel_to_proposal" name="tasks_related_to[]">
        <label for="ts_rel_to_proposal">' . _l('proposals') . '</label>
        </div>';

        echo '</div>';
    */ }
    echo "<div class='clearfix'></div>";

    // If new column is added on tasks relations table this will not work fine
    // In this case we need to add new identifier eq task-relation
    $table_attributes['data-last-order-identifier'] = 'tasks';
    $table_attributes['data-default-order']         = get_table_last_order('tasks');

    $table .= render_datatable($table_data, $name, [], $table_attributes);

    return $table;
}

/**
 * Return tasks summary formated data
 * @param  string $where additional where to perform
 * @return array
 */
function tasks_summary_data($rel_id = null, $rel_type = null)
{
    $CI            = &get_instance();
    $tasks_summary = [];
    $statuses      = $CI->tasks_model->get_statuses();
    //pr($statuses); exit;
    foreach ($statuses as $status) {
        $tasks_where = 'rel_type != "" AND ';
        if($status['id'] == 1) {
            //$tasks_where = $tasks_where.' AND startdate > ' . date('Y-m-d');
            $tasks_where = ' date(startdate) > CURDATE() AND status != 5 ';
        } elseif($status['id'] == 3) {
            //$tasks_where = $tasks_where.' AND startdate = ' . date('Y-m-d');
            $tasks_where = ' date(startdate) = CURDATE() AND status != 5 ';
        } elseif($status['id'] == 2) {
            $tasks_where = ' date(startdate) < CURDATE() AND status != 5 ';
        }  elseif($status['id'] == 5) {
            $tasks_where = 'status = ' . $status['id'];
        }
        $sdate = date('Y-m-d', strtotime($aRow['startdate'])); 
        if(strtotime($sdate) == strtotime(date('Y-m-d')) && $aRow['status'] != 5) {
            $tasks_where = 'status = 3';
        }
        if(strtotime($sdate) > strtotime(date('Y-m-d')) && $aRow['status'] != 5) {
            $tasks_where = 'status = 1';
        }
        if (!has_permission('tasks', '', 'view')) {
            $tasks_where .= ' ' . get_tasks_where_string();
        }
        $my_staffids = $CI->staff_model->get_my_staffids();
        
        $tasks_my_where = 'id IN(SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid=' . get_staff_user_id() . ') AND ' . $tasks_where;
        if ($rel_id && $rel_type) {
            if($my_staffids){
                
                $tasks_where .= ' AND rel_id=' . $rel_id . ' AND tbltasks.id IN (select taskid from tbltask_assigned where staffid IN (' . implode(',',$my_staffids) . '))';
                $tasks_my_where .= ' AND rel_id=' . $rel_id . ' AND tbltasks.id IN (select taskid from tbltask_assigned where staffid IN (' . implode(',',$my_staffids) . '))';
                //array_push($where, ' AND ' . db_prefix() . 'tasks.rel_id IN (SELECT ' . db_prefix() . 'projects.id FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ') OR  ' . db_prefix() . 'projects.teamleader in (' . implode(',',$my_staffids) . ') )');
            } else {
                $tasks_where .= ' AND rel_id=' . $rel_id . ' AND rel_type="' . $rel_type . '"';
                $tasks_my_where .= ' AND rel_id=' . $rel_id . ' AND rel_type="' . $rel_type . '"';
            }
        } else {
            if($my_staffids){
				$tasks_where .= ' AND (' . db_prefix() . 'tasks.id in (select taskid from tbltask_assigned where staffid in (' . implode(',',$my_staffids) . ')) OR ' . db_prefix() . 'tasks.rel_id IN (SELECT ' . db_prefix() . 'projects.id FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ')) )';
            	$tasks_my_where .= ' AND (' . db_prefix() . 'tasks.id in (select taskid from tbltask_assigned where staffid in (' . implode(',',$my_staffids) . ')) OR ' . db_prefix() . 'tasks.rel_id IN (SELECT ' . db_prefix() . 'projects.id FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ')))';
			}else{
				$sqlProjectTasksWhere = ' AND CASE
				WHEN rel_type="project" AND rel_id IN (SELECT project_id FROM ' . db_prefix() . 'project_settings WHERE project_id=rel_id AND name="hide_tasks_on_main_tasks_table" AND value=1)
				THEN rel_type != "project"
				ELSE 1=1
				END';
				$tasks_where .= $sqlProjectTasksWhere;
				$tasks_my_where .= $sqlProjectTasksWhere;
			}
        }
        
		$tasks_where =$tasks_where;
        $tasks_my_where = $tasks_my_where;
        $summary                   = [];
        $summary['total_tasks']    = total_rows(db_prefix() . 'tasks', $tasks_where);
        $summary['total_my_tasks'] = total_rows(db_prefix() . 'tasks', $tasks_my_where);
        $summary['color']          = $status['color'];
        $summary['name']           = $status['name'];
        $summary['status_id']      = $status['id'];
        $tasks_summary[]           = $summary;
    }
    $b = array(2, 1, 0, 3); // rule indicating new key order
    $c = array();
    foreach($b as $index) {
        $c[] = $tasks_summary[$index];
    }
    return $c;
}


function get_sql_calc_task_logged_time($task_id)
{
    /**
    * Do not remove where task_id=
    * Used in tasks detailed_overview to overwrite the taskid
    */
    return 'SELECT SUM(CASE
            WHEN end_time is NULL THEN ' . time() . '-start_time
            ELSE end_time-start_time
            END) as total_logged_time FROM ' . db_prefix() . 'taskstimers WHERE task_id =' . $task_id;
}

function get_sql_select_task_assignees_ids()
{
    return '(SELECT GROUP_CONCAT(staffid SEPARATOR ",") FROM ' . db_prefix() . 'task_assigned WHERE taskid=' . db_prefix() . 'tasks.id ORDER BY ' . db_prefix() . 'task_assigned.staffid)';
}

function get_sql_select_task_asignees_full_names()
{
    return '(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM '.db_prefix().'task_assigned JOIN '.db_prefix().'staff ON '.db_prefix().'staff.staffid = '.db_prefix().'task_assigned.staffid WHERE taskid='.db_prefix().'tasks.id ORDER BY '.db_prefix().'task_assigned.staffid)';
}

function get_sql_select_task_total_checklist_items()
{
    return '(SELECT COUNT(id) FROM '.db_prefix().'task_checklist_items WHERE taskid='.db_prefix().'tasks.id) as total_checklist_items';
}

function get_sql_select_task_total_finished_checklist_items()
{
    return '(SELECT COUNT(id) FROM '.db_prefix().'task_checklist_items WHERE taskid='.db_prefix().'tasks.id AND finished=1) as total_finished_checklist_items';
}

/**
 * This text is used in WHERE statements for tasks if the staff member don't have permission for tasks VIEW
 * This query will shown only tasks that are created from current user, public tasks or where this user is added is task follower.
 * Other statement will be included the tasks to be visible for this user only if Show All Tasks For Project Members is set to YES
 * @return string
 */
function get_tasks_where_string($table = true)
{
    $_tasks_where = '('.db_prefix().'tasks.id IN (SELECT taskid FROM '.db_prefix().'task_assigned WHERE staffid = ' . get_staff_user_id() . ') OR '.db_prefix().'tasks.id IN (SELECT taskid FROM '.db_prefix().'task_followers WHERE staffid = ' . get_staff_user_id() . ') OR ('.db_prefix().'tasks.addedfrom=' . get_staff_user_id() . ' AND is_added_from_contact=0)';
    if (get_option('show_all_tasks_for_project_member') == 1) {
        $_tasks_where .= ' OR ('.db_prefix().'tasks.rel_type="project" AND '.db_prefix().'tasks.rel_id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id=' . get_staff_user_id() . '))';
    }
    $_tasks_where .= ' OR is_public = 1)';
    if ($table == true) {
        $_tasks_where = 'AND ' . $_tasks_where;
    }
    return $_tasks_where;
}
function get_or_update_setting($staffid,$table){
	$CI   = &get_instance();
	$cond = array('user_id'=>$staffid);
	$mail_setting = $CI->db->where($cond)->get($table)->result_array();
	if(!empty($mail_setting[0]))
		return $mail_setting[0];
	else
		return array();
}
function get_imap_setting($staffid=''){
	$CI   = &get_instance();
	$CI->load->library('imap');
	if(empty($staffid)){
		$staffid = get_staff_user_id();
	}
	$imapconf = array();
	
	if(get_option('company_mail_server')=='no' ){
		$table = db_prefix() . 'personal_mail_setting';
		$config = get_or_update_setting($staffid,$table);
		//foreach($imapsettings as $config){

			if(!empty($config)){
				$imapconf['host'] 	  = $config['imap_host'];
				$imapconf['encrypto'] = $config['imap_encryption'];
				$imapconf['username'] = $config['imap_username'];
				$imapconf['password'] = $config['imap_password'];
				$imapconf['port']	  = $config['imap_port'];
				$imapconf['validate'] = true;
			}
		//}
	}
	else{
		$table = db_prefix() . 'user_mail_setting';
		$setting1 = get_or_update_setting($staffid,$table);
		$imapsettings = $CI->db->get(db_prefix() . 'options')->result_array();
		foreach($imapsettings as $config) {
			if($config['name'] == 'company_imap_host')
				$imapconf['host'] = $config['value'];
			if($config['name'] == 'company_imap_server')
				$imapconf['server'] = $config['value'];
			if($config['name'] == 'company_imap_encryption')
				$imapconf['encrypto'] = $config['value'];
			if($config['name'] == 'company_imap_port')
				$imapconf['port'] = $config['value'];
		
			$imapconf['username'] = $setting1['email'];
			$imapconf['password'] = $setting1['password'];
			
		}
		
		$imapconf['validate'] = true;
	}
	return $imapconf;
}
function get_smtp_setings(){
	$CI   = &get_instance();
	$smtp_cong = array();
	$smtp_cong['host'] 	   = get_option('company_smtp_host');
	$smtp_cong['encrypto'] = get_option('company_smtp_encryption');
	$smtp_cong['username'] = get_option('company_smtp_username');
	$ps = get_option('company_smtp_password');
	$smtp_cong['password'] = $ps;
	if(!empty($ps)){
		if(false == $CI->encryption->decrypt($ps)){
			$ps = $ps;
		} else {
			$ps = $CI->encryption->decrypt($ps);
		}
	}
	$smtp_cong['password'] = $ps;
	$smtp_cong['port']	   = get_option('company_smtp_port');
	$smtp_cong['validate'] = true;
	$smtp_cong['smtp_user'] = $smtp_cong['username'];
	$smtp_cong['smtp_pass'] = $ps;
	return $smtp_cong;
	
}
function get_smtp_setings1(){
	$CI   = &get_instance();
	$staffid = get_staff_user_id();
	$smtp_cong = array();
	if(get_option('company_mail_server')=='no' ){
		//foreach($imapsettings as $config){
			$table = db_prefix() . 'personal_mail_setting';
			$config = get_or_update_setting($staffid,$table);
			if(!empty($config)){
				$smtp_cong['host'] 	   = $config['smtp_host'];
				$smtp_cong['encrypto'] = $config['smtp_encryption'];
				$smtp_cong['username'] = $config['smtp_username'];
				
				$smtp_cong['password'] = $config['smtp_password'];
				$smtp_cong['password'] = $ps;
				$smtp_cong['port']	   = $config['smtp_port'];
				$smtp_cong['validate'] = true;
			}
		//}
	}
	else{
		$table = db_prefix() . 'user_mail_setting';
		$setting1 = get_or_update_setting($staffid,$table);
		$imapsettings = $CI->db->get(db_prefix() . 'options')->result_array();
		foreach($imapsettings as $config) {
			if($config['name'] == 'company_smtp_host')
				$smtp_cong['host'] = $config['value'];
			if($config['name'] == 'company_smtp_encryption')
				$smtp_cong['encrypto'] = $config['value'];
			if($config['name'] == 'company_smtp_port')
				$smtp_cong['port'] = $config['value'];
			if($config['name'] == 'company_smtp_username')
				$smtp_cong['username'] = $config['value'];
			if($config['name'] == 'company_smtp_password')
				$smtp_cong['password'] = $config['value'];

		}
		$smtp_cong['username'] = $setting1['email'];
		$smtp_cong['password'] = $CI->encryption->encrypt($smtp_cong['password']);
		$smtp_cong['validate'] = true;
	}
	$req_out = get_imap_setting($staffid);
	$smtp_cong['smtp_user'] = $req_out['username'];
	$smtp_cong['smtp_pass'] = $req_out['password'];
	return $smtp_cong;
}
function search_email_address($staffid,$table,$term=''){
	$CI   = &get_instance();
	$cond = array('deleted_status='=>0);
	if(!empty($term)){
		$cond_like['email'] = $term;
		$searches = $CI->db->where($cond)->like($cond_like)->get($table)->result_array();
	}
	else{
		$searches = $CI->db->where($cond)->get($table)->result_array();
	}
	if(!empty($searches))
		return $searches;
	else
		return array();
}
function get_company($id,$table){
	$CI   = &get_instance();
	$cond = array('userid='=>$id);
	$searches = $CI->db->where($cond)->get($table)->result_array();
	if(!empty($searches))
		return $searches;
	else
		return array();
}
function get_files($project_id)
{
	$CI   = &get_instance();
	if (is_client_logged_in()) {
		$CI->db->where('visible_to_customer', 1);
	}
	$CI->db->where('id', $project_id);

	return $CI->db->get(db_prefix() . 'project_files')->result_array();
}

function get_deal_deails($deal_id){
	$CI   = &get_instance();
	$req_project_id = $req_project_name = '';
	$project12 = $CI->db->query('SELECT id,name FROM '.db_prefix().'projects  where id = '.$deal_id.' limit 0,1')->row();
	if ($project12 ) {
		$req_project_name = $project12->name;
		$req_project_id = $project12->id;
	}
	$req_out = array('project_id'=>$req_project_id,'project_name'=>$req_project_name);
	return $req_out;
}
function get_deal_name($to_mail,$ch_deal){
	$req_project_id = $req_project_name = '';
	$CI   = &get_instance();
	$CI->db->where('email', $to_mail);
	 $contacts12 = $CI->db->get(db_prefix() . 'contacts')->row();
	 $count_tot = 0;
	 $staff_id = get_staff_user_id();
	 if ($contacts12) {
		 if($ch_deal!='' && $ch_deal == 'first open deal'){
			$project12 = $CI->db->query('SELECT pc.project_id,p.name as p_name FROM ' . db_prefix() . 'project_contacts pc,'.db_prefix().'projects p where pc.contacts_id ='.$contacts12->id.' and p.stage_of = 0 and p.id = pc.project_id  order by p.id asc limit 0,1')->row();
			if ($project12 ) {
				$req_project_name = $project12->p_name;
				$req_project_id = $project12->project_id;
			}
		}
		else if($ch_deal!='' && $ch_deal == 'last open deal'){
			$project12 = $CI->db->query('SELECT pc.project_id,p.name as p_name FROM ' . db_prefix() . 'project_contacts pc,'.db_prefix().'projects p where pc.contacts_id ='.$contacts12->id.' and p.stage_of = 0 and p.id = pc.project_id order by p.id desc limit 0,1')->row();
			if ($project12 ){
				$req_project_name = $project12->p_name;
				$req_project_id = $project12->project_id;
			}
		}
		else if($ch_deal!='' && $ch_deal == 'more activities available in open deal'){
			$project12 = $CI->db->query('SELECT pc.project_id,p.name as p_name FROM ' . db_prefix() . 'project_contacts pc,'.db_prefix().'projects p where pc.contacts_id ='.$contacts12->id.' and p.stage_of = 0 and p.id = pc.project_id order by p.id asc ')->result_array();
			if (!empty($project12) && $req_project_id=='') {
				foreach($project12 as $project121){
					$CI->db->select("count(*) tot_activitiy",'project_id');
					$CI->db->where('project_id	', $project121['project_id']);
					$project13 = $CI->db->get(db_prefix() . 'project_activity')->row();
				
					if ($project13 && $count_tot <$project13->tot_activitiy) {
						$req_project_name = $project121['p_name'];
						$req_project_id = $project121['project_id'];
						$count_tot = $project13->tot_activitiy;
					}
				}
			}
			
		}
		else{
			$project12 = $CI->db->query('SELECT pc.project_id,p.name as p_name FROM ' . db_prefix() . 'project_contacts pc,'.db_prefix().'projects p where pc.contacts_id ='.$contacts12->id.' and p.stage_of = 0 and p.id = pc.project_id order by p.id asc limit 0,1')->row();
			if ($project12 ) {
				$req_project_name = $project12->p_name;
				$req_project_id = $project12->project_id;
			}
		}
		
	}
	$req_out = array('project_id'=>$req_project_id,'project_name'=>$req_project_name);
	return $req_out;
}
function get_deal_id($to_mail,$ch_deal){
	
	$req_project_id = '';
	$CI   = &get_instance();
	$CI->db->where('email', $to_mail);
	 $contacts12 = $CI->db->get(db_prefix() . 'contacts')->row();
	 $count_tot = 0;
	 $staff_id = get_staff_user_id();
	 if ($contacts12) {
		 if($ch_deal!='' && $ch_deal == 'first open deal'){
			$project12 = $CI->db->query('SELECT pc.project_id FROM ' . db_prefix() . 'project_contacts pc,'.db_prefix().'projects p where pc.contacts_id ='.$contacts12->id.' and p.stage_of = 0 and p.id = pc.project_id  and p.teamleader = '.$staff_id.' order by p.id asc limit 0,1')->row();
			if ($project12 ) {
				$req_project_id = $project12->project_id;
			}
		}
		else if($ch_deal!='' && $ch_deal == 'last open deal'){
			$project12 = $CI->db->query('SELECT pc.project_id FROM ' . db_prefix() . 'project_contacts pc,'.db_prefix().'projects p where pc.contacts_id ='.$contacts12->id.' and p.stage_of = 0 and p.id = pc.project_id  and p.teamleader = '.$staff_id.' order by p.id desc limit 0,1')->row();
			if ($project12 ){
				$req_project_id = $project12->project_id;
			}
		}
		else if($ch_deal!='' && $ch_deal == 'more activities available in open deal'){
			$project12 = $CI->db->query('SELECT pc.project_id FROM ' . db_prefix() . 'project_contacts pc,'.db_prefix().'projects p where pc.contacts_id ='.$contacts12->id.' and p.stage_of = 0 and p.id = pc.project_id  and p.teamleader = '.$staff_id.' order by p.id asc ')->result_array();
			if (!empty($project12) && $req_project_id=='') {
				foreach($project12 as $project121){
					$CI->db->select("count(*) tot_activitiy",'project_id');
					$CI->db->where('project_id	', $project121['project_id']);
					$project13 = $CI->db->get(db_prefix() . 'project_activity')->row();
				
					if ($project13 && $count_tot <$project13->tot_activitiy) {
						$req_project_id = $project121['project_id'];
						$count_tot = $project13->tot_activitiy;
					}
				}
			}
			
		}
		else{
			$project12 = $CI->db->query('SELECT pc.project_id FROM ' . db_prefix() . 'project_contacts pc,'.db_prefix().'projects p where pc.contacts_id ='.$contacts12->id.' and p.stage_of = 0 and p.id = pc.project_id  and p.teamleader = '.$staff_id.' order by p.id asc limit 0,1')->row();
			if ($project12 ) {
				$req_project_id = $project12->project_id;
			}
		}
		
	}
	return $req_project_id;
}
function get_deal_id_otheruser($ch_deal,$to_mails = array(),$cc_mails = array(),$bcc_mails = array()){
	$req_res = array();
	$CI   = &get_instance();
	if(!empty($to_mails)){
		foreach($to_mails as $to_mail1){
			$req_res = get_deal_id_contactuser($to_mail1['name'],$ch_deal);
			if(!empty($req_res)){
				break;
			}
		}
	}
	if(!empty($req_res) && !empty($cc_mails)){
		foreach($cc_mails as $cc_mail1){
			$req_res = get_deal_id_contactuser($cc_mail1['name'],$ch_deal);
			if(!empty($req_res)){
				break;
			}
		}
	}
	if(!empty($req_res) && !empty($bcc_mails)){
		foreach($bcc_mails as $bcc_mail1){
			$req_res = get_deal_id_contactuser($bcc_mail1['name'],$ch_deal);
			if(!empty($req_res)){
				break;
			}
		}
	}
	return $req_res;
}
function get_deal_id_contactuser($from_mail,$ch_deal){
	
	$req_project_id = '';
	$CI   = &get_instance();
	 $count_tot = 0;
	 $req_res = array();
	 $CI->db->reconnect();
	 if($ch_deal!='' && $ch_deal == 'first open deal'){
		$project12 = $CI->db->query("SELECT pc.project_id,c.id c_id FROM " . db_prefix() . "project_contacts pc,".db_prefix()."projects p,".db_prefix()."contacts c where c.email = '".$from_mail."' and pc.contacts_id =c.id and p.stage_of = 0 and p.id = pc.project_id order by p.id asc limit 0,1")->row();
		if ($project12 ) {
			$req_res['project_id'] = $project12->project_id;
			$req_res['contact_id'] = $project12->c_id;
		}
		else{
		}
	}
	else if($ch_deal!='' && $ch_deal == 'last open deal'){
		$project12 = $CI->db->query("SELECT pc.project_id,c.id c_id FROM " . db_prefix() . "project_contacts pc,".db_prefix()."projects p,".db_prefix()."contacts c where c.email = '".$from_mail."' and pc.contacts_id = c.id and p.stage_of = 0 and p.id = pc.project_id order by p.id desc limit 0,1")->row();
		if ($project12 ){
			$req_res['project_id'] = $project12->project_id;
			$req_res['contact_id'] = $project12->c_id;
		}
	}else if($ch_deal!='' && $ch_deal == 'more activities available in open deal'){
		$project12 = $CI->db->query("SELECT pc.project_id,c.id c_id FROM " . db_prefix() ."project_contacts pc,".db_prefix()."projects p,".db_prefix()."contacts c where c.email = '".$from_mail."' and pc.contacts_id =c.id and p.stage_of = 0 and p.id = pc.project_id order by p.id ")->result_array();
		if (!empty($project12) && $req_project_id=='') {
			foreach($project12 as $project121){
				$CI->db->reconnect();
				$CI->db->select("count(*) tot_activitiy",'project_id');
				$CI->db->where('project_id	', $project121['project_id']);
				$project13 = $CI->db->get(db_prefix() . 'project_activity')->row();
			
				if ($project13 && $count_tot <$project13->tot_activitiy) {
					$req_project_id = $req_res['project_id'] = $project121['project_id'];
					$req_res['contact_id'] = $project121['c_id'];
					$count_tot = $project13->tot_activitiy;
				}
			}
		}
		
	}
	else{
		$project12 = $CI->db->query("SELECT pc.project_id,c.id c_id FROM " . db_prefix() . "project_contacts pc,".db_prefix()."projects p,".db_prefix()."contacts c where c.email = '".$from_mail."' and pc.contacts_id =c.id and p.stage_of = 0 and p.id = pc.project_id order by p.id asc limit 0,1")->row();
		if ($project12 ) {
			$req_res['project_id'] = $project12->project_id;
			$req_res['contact_id'] = $project12->c_id;
		}
	}
	return json_encode($req_res);
}
function check_file_exists($file){
	if (file_exists($file)) {
		return $file;
	} 
	else{
		$file = '1'.$file;
	}
}
function check_upload(){
	$CI   = &get_instance();
	$file_count = count($_FILES['attachment']['name'])-1;
	$req_filename = $req_path = array();
	if(isset($file_count) && $file_count>0){
		for($j=0;$j<$file_count;$j++){
			if(!empty($_FILES['attachment']['name'][$j])){
				$config['upload_path'] = 'uploads/emails';
				$config['allowed_types'] = '*';
				
				$config['encrypt_name'] =  TRUE;
				$config['remove_spaces'] = TRUE;
				$_FILES['attachment[]']['name']= $_FILES['attachment']['name'][$j];
				$_FILES['attachment[]']['type']= $_FILES['attachment']['type'][$j];
				$_FILES['attachment[]']['tmp_name']= $_FILES['attachment']['tmp_name'][$j];
				$_FILES['attachment[]']['error']= $_FILES['attachment']['error'][$j];
				$_FILES['attachment[]']['size']= $_FILES['attachment']['size'][$j];    
				$CI->load->library('upload', $config);
				$CI->upload->initialize($config);
				
				 if ( $CI->upload->do_upload('attachment[]')){
					 $req_data = $CI->upload->data();
					 $req_filename[] = $req_data['file_name'];
					 $req_path[] = $req_data['full_path'];
				 }
				 
				 
			}
		}
	}
	$output = array('name'=>$req_filename,'path'=>$req_path);
	return json_encode($output);
}
function get_mail_message($messages,$imapconf){
	$CI   = &get_instance();
	$req_messages['cc']['email'] = $req_messages['cc']['name'] = $req_messages['bcc']['email'] = $req_messages['bcc']['name'] = '';
	$req_messages['id'] = $req_messages['uid'] = 0;
	$req_messages['from']['email']		= $imapconf['username'];
	$req_messages['from']['name']		= 'New Activity Created';
	$req_messages['to']['email'] 		= $messages['toemail'];
	$req_messages['to']['name'] 		= '';
	if(!empty($messages['ccemail'])){
		$req_messages['cc']['email']		= $messages['ccemail'];
		$req_messages['cc']['name']			= '';
	}
	if(!empty($messages['bccemail'])){
		$req_messages['bcc']['email']		= $messages['bccemail'];
		$req_messages['bcc']['name']		= '';
	}
	$req_messages['reply_to']['email'] 	= $imapconf['username'];
	$req_messages['reply_to']['name']	= 'Replay me';
	$req_messages['message_id']			= '';
	$req_messages['in_reply_to']		= '';
	$req_messages['mail_references']	= array();
	$req_messages['date']				= date(DATE_RFC822);
	$req_messages['udate']				= strtotime(date(DATE_RFC822));
	$req_messages['subject']			= $messages['name'];
	$req_messages['recent']				= 0;
	$req_messages['priority']			= 0;
	$req_messages['read']				= 0;
	$req_messages['answered']			= 0;
	$req_messages['flagged']			= 0;
	$req_messages['deleted']			= 0;
	$req_messages['draft']				= 0;
	$req_messages['size']				= 0;
	$req_messages['body']['html']		= $messages['description'];
	$req_messages['body']['plain']		= strip_tags($messages['description']);
	$req_messages['to']					= array($req_messages['to']);
	$req_messages['reply_to']			= array($req_messages['reply_to']);
	$req_messages['cc']					= array($req_messages['cc']);
	$req_messages['bcc']				= array($req_messages['bcc']);
	
	return $req_messages;
}
function add_target($status){
	$CI   = &get_instance();
	if(get_option('target_company') == 'Calendar'){
		$data['months'] = array('Jan','Feb','Mar','Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec');
	}
	else{
		$data['months'] = array('Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar');
	}
	$ins_data = $ins_deal = $ins_interval =  $ins_user = $ins_manager = array();
	extract($_POST);
	$ins_data['assign'] 		= $assign;
	$ins_data['tracking_metric']= $tracking_metic;
	$ins_data['interval'] 		= $interval;
	$ins_data['target_type'] 	= $goal_val;
	$ins_data['target_status'] 	= $status;
	$ins_data['start_date']		= date('Y-m-d',strtotime($start_date));
	if(!empty($end_date)){
		$ins_data['end_date']		= date('Y-m-d',strtotime($end_date));
	}
	
	$table = db_prefix() . 'target';
	$CI->db->insert($table, $ins_data);
	$target_id = $CI->db->insert_id();
	if($goal_val == 'progressed'){
		if(!empty($pipeline_stage)){
			$i = 0;
			$search_stage = implode(', ', $pipeline_stage);
			$stage_res = $CI->db->query("SELECT  GROUP_CONCAT(name SEPARATOR ', ') as pipeline FROM " . db_prefix() ."projects_status WHERE id in( ".$search_stage.")")->result_array();
			foreach($pipeline_stage as $pipeline_stage12){
				$ins_stage[$i]['stage_id'] = $pipeline_stage12;
				$ins_stage[$i]['target_id'] = $target_id;
				$i++;
			}
			$table = db_prefix() . 'target_stage';
			$CI->db->insert_batch($table, $ins_stage);
		}
	}
	
	if(!empty($select_deal)){
		$i = 0;
		$search_deal = implode(', ', $select_deal);
		$pipe_res = $CI->db->query("SELECT  GROUP_CONCAT(name SEPARATOR ', ') as pipeline FROM " . db_prefix() ."pipeline WHERE id in( ".$search_deal.")")->result_array();
		foreach($select_deal as $select_deal1){
			$ins_deal[$i]['pipeline'] = $select_deal1;
			$ins_deal[$i]['target_id'] = $target_id;
			$i++;
		}
		$table = db_prefix() . 'target_pipeline';
		$CI->db->insert_batch($table, $ins_deal);
	}
	
	if(!empty($select_manager)){
		$i = 0;
		$search_manager = implode(', ', $select_manager);
		$manager_res = $CI->db->query("SELECT  GROUP_CONCAT(CONCAT(`firstname`, ' ', `lastname`) SEPARATOR ', ') as contact_name FROM " . db_prefix() ."staff WHERE staffid in( ".$search_manager.")")->result_array();
		foreach($select_manager as $select_manager1){
			$ins_manager[$i]['manager'] = $select_manager1;
			$ins_manager[$i]['target_id'] = $target_id;
			$ins_manager[$i]['assign_user'] = $assign_user_wise1;
			$i++;
		}
		$table = db_prefix() . 'target_manager';
		$CI->db->insert_batch($table, $ins_manager);
	}
	if(!empty($select_user)){
		$search_user = implode(', ', $select_user);
		$user_res = $CI->db->query("SELECT  GROUP_CONCAT(CONCAT(`firstname`, ' ', `lastname`) SEPARATOR ', ') as contact_name FROM " . db_prefix() ."staff WHERE staffid in( ".$search_user.")")->result_array();
		$i = 0;
		foreach($select_user as $select_user1){
			$ins_user[$i]['user'] = $select_user1;
			$ins_user[$i]['target_id'] = $target_id;
			$i++;
		}
		$table = db_prefix() . 'target_user';
		$CI->db->insert_batch($table, $ins_user);
	}
	if($interval == 'Monthly'){
		if(!empty($intervals1)){
			$i = 0;
			$req_search = '';
			foreach($intervals1 as $interval12){
				$req_search .= $data['months'][$i].' :'.$intreval_value2[$i].',';
				$i++;
			}
			$req_search = rtrim($req_search,",");
			$i = 0;
			foreach($intervals1 as $interval11){
				if($i>=12){
					break;
				}
				$ins_interval[$i]['interval_type'] = $tracking_metic;
				$ins_interval[$i]['interval'] = $interval11;
				if(!empty($intreval_value2[$i])){
					$ins_interval[$i]['interval_value'] = $intreval_value2[$i];
				}else{
					$ins_interval[$i]['interval_value'] = 0;
				}
				$ins_interval[$i]['target_id'] = $target_id;
				$ins_interval[$i]['i_month'] = $data['months'][$i];
				$ins_interval[$i]['s_search'] = $req_search;
				$j = $i + 1;
				if(get_option('target_company') == 'Calendar'){
					$ins_interval[$i]['calender'] = $j;
					$k = $j + 9;
					if($k>12){
						$k = $k -12;
					}
					$ins_interval[$i]['finance'] = $k;
				}else{
					$ins_interval[$i]['finance'] = $j;
					$k = $j + 3;
					if($k>12){
						$k = $k -12;
					}
					$ins_interval[$i]['calender'] = $k;
				}
				$i++;
			}
		}
	}else{
		if(!empty($intervals)){
			$i = 0;
			foreach($intervals as $interval1){
				$ins_interval[$i]['interval_type'] = $tracking_metic;
				$ins_interval[$i]['interval'] = $interval1;
				if(!empty($intreval_value[$i])){
					$ins_interval[$i]['interval_value'] = $intreval_value[$i];
				}else{
					$ins_interval[$i]['interval_value'] = 0;
				}
				$ins_interval[$i]['s_search'] = $ins_interval[$i]['interval_value'];
				$ins_interval[$i]['target_id'] = $target_id;
				$i++;
			}
		}
	}
	$table = db_prefix().'target_interval';
	$CI->db->insert_batch($table, $ins_interval);
	if (isset($custom_fields)) {
		handle_custom_fields_post($target_id, $custom_fields);
	}
	return true;
}
function edit_target($status,$target_id){
	$CI   = &get_instance();
	$data = array();
	if(get_option('target_company') == 'Calendar'){
		$data['months'] = array('Jan','Feb','Mar','Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec');
	}
	else{
		$data['months'] = array('Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar');
	}
	$ins_data = $ins_deal = $ins_interval =  $ins_user = $ins_manager = array();
	extract($_POST);
	$ins_data['assign'] 		= $assign;
	$ins_data['tracking_metric'] = $tracking_metic;
	$ins_data['interval'] 		= $interval;
	$ins_data['target_status'] 	= $status;
	$ins_data['start_date']		= date('Y-m-d',strtotime($start_date));
	if(!empty($end_date)){
		$ins_data['end_date']	= date('Y-m-d',strtotime($end_date));
	}
	else{
		$ins_data['end_date']	= null;
	}
	$search_deal = implode(', ', $select_deal);
	$pipe_res = $CI->db->query("SELECT  GROUP_CONCAT(name SEPARATOR ', ') as pipeline FROM " . db_prefix() ."pipeline WHERE id in( ".$search_deal.")")->result_array();
	$table = db_prefix() . 'target';
	$condition = array('id'=>$target_id);
	
	$CI->db->update($table, $ins_data, $condition);
	if(!empty($pipeline_stage)){
		$i = 0;
		$condition = array('target_id'=>$target_id);
		$CI->db->where($condition);
		$table = db_prefix() . 'target_stage';
		$result = $CI->db->delete($table);
		$search_stage = implode(', ', $pipeline_stage);
		$stage_res = $CI->db->query("SELECT  GROUP_CONCAT(name SEPARATOR ', ') as pipeline FROM " . db_prefix() ."projects_status WHERE id in( ".$search_stage.")")->result_array();
		foreach($pipeline_stage as $pipeline_stage12){
			$ins_stage[$i]['stage_id'] = $pipeline_stage12;
			$ins_stage[$i]['target_id'] = $target_id;
			$i++;
		}
		$table = db_prefix() . 'target_stage';
		$CI->db->insert_batch($table, $ins_stage);
	}
	
	if(!empty($select_deal)){
		$i = 0;
		$condition = array('target_id'=>$target_id);
		$CI->db->where($condition);
		$table = db_prefix() . 'target_pipeline';
		$result = $CI->db->delete($table);
		foreach($select_deal as $select_deal1){
			$ins_deal[$i]['pipeline'] = $select_deal1;
			$ins_deal[$i]['target_id'] = $target_id;
			$i++;
		}
		
		$CI->db->insert_batch($table, $ins_deal);
	}
	$condition = array('target_id'=>$target_id);
	$CI->db->where($condition);
	$table = db_prefix() . 'target_manager';
	$result = $CI->db->delete($table);
	if(!empty($select_manager)){
		$i = 0;
		$search_manager = implode(', ', $select_manager);
		$manager_res = $CI->db->query("SELECT  GROUP_CONCAT(CONCAT(`firstname`, ' ', `lastname`) SEPARATOR ', ') as contact_name FROM " . db_prefix() ."staff WHERE staffid in( ".$search_manager.")")->result_array();
		foreach($select_manager as $select_manager1){
			$ins_manager[$i]['manager'] = $select_manager1;
			$ins_manager[$i]['target_id'] = $target_id;
			$ins_manager[$i]['assign_user'] = $assign_user_wise1;
			$i++;
		}
		$CI->db->insert_batch($table, $ins_manager);
	}
	$condition = array('target_id'=>$target_id);
	$CI->db->where($condition);
	$table = db_prefix() . 'target_user';
	$result = $CI->db->delete($table);
	if(!empty($select_user)){
		$i = 0;
		$search_user = implode(', ', $select_user);
		$user_res = $CI->db->query("SELECT  GROUP_CONCAT(CONCAT(`firstname`, ' ', `lastname`) SEPARATOR ', ') as contact_name FROM " . db_prefix() ."staff WHERE staffid in( ".$search_user.")")->result_array();
		foreach($select_user as $select_user1){
			$ins_user[$i]['user'] = $select_user1;
			$ins_user[$i]['target_id'] = $target_id;
			$i++;
		}
		$CI->db->insert_batch($table, $ins_user);
	}
	
	$i = 0;
	$condition = array('target_id'=>$target_id);
	$CI->db->where($condition);
	$table = db_prefix().'target_interval';
	$result = $CI->db->delete($table);
		
	if($interval == 'Monthly'){
		if(!empty($intervals1)){
			$i = 0;
			$req_search = '';
			foreach($intervals1 as $interval12){
				if($i>=12){
					break;
				}
				$req_search .= $data['months'][$i].' :'.$intreval_value2[$i].',';
				$i++;
			}
			$req_search = rtrim($req_search,",");
			$i = 0;
			foreach($intervals1 as $interval11){
				if($i>=12){
					break;
				}
				$ins_interval[$i]['interval_type'] = $tracking_metic;
				$ins_interval[$i]['interval'] = $interval11;
				if(!empty($intreval_value2[$i])){
					$ins_interval[$i]['interval_value'] = $intreval_value2[$i];
				}else{
					$ins_interval[$i]['interval_value'] = 0;
				}
				$ins_interval[$i]['target_id'] = $target_id;
				$ins_interval[$i]['i_month'] = $data['months'][$i];
				$ins_interval[$i]['s_search'] = $req_search;
				$j = $i + 1;
				if(get_option('target_company') == 'Calendar'){
					$ins_interval[$i]['calender'] = $j;
					$k = $j + 9;
					if($k>12){
						$k = $k -12;
					}
					$ins_interval[$i]['finance'] = $k;
				}else{
					$ins_interval[$i]['finance'] = $j;
					$k = $j + 3;
					if($k>12){
						$k = $k -12;
					}
					$ins_interval[$i]['calender'] = $k;
				}
				$i++;
			}
		}
	}else{
		if(!empty($intervals1)){
			$i = 0;
			foreach($intervals1 as $interval1){
				$ins_interval[$i]['interval_type'] = $tracking_metic;
				$ins_interval[$i]['interval'] = $interval1;
				if(!empty($intreval_value2[$i])){
					$ins_interval[$i]['interval_value'] = $intreval_value2[$i];
				}else{
					$ins_interval[$i]['interval_value'] = 0;
				}
				$ins_interval[$i]['s_search'] = $ins_interval[$i]['interval_value'];
				$ins_interval[$i]['target_id'] = $target_id;
				break;
				$i++;
			}
		}
	}
	$CI->db->insert_batch($table, $ins_interval);
	if (isset($custom_fields)) {
		handle_custom_fields_post($target_id, $custom_fields);
	}
	return true;
}
function custom_check($custom){
	switch($custom){
		case 'target':
			$colarr = array(
				"assign"=>1,"pipeline"=>1,"pipeline_stage"=>1,"tracking_metric"=>1,"target_type"=>1,
				"interval"=>1,"start_date"=>1,"end_date"=>1,"count_value"=>1,"user"=>1,"manager"=>1,
			); 
			custom_update1($colarr,'target','target_list_column_order','target_list_column_order');
				$colarr = array(
				"assign"=>1,"pipeline"=>1,"tracking_metric"=>1,"target_type"=>1,"interval"=>1,"start_date"=>1,"end_date"=>1,"count_value"=>1,"user"=>1,"manager"=>1,
			); 
			custom_update1($colarr,'target','target_activity_list_column','target_activity_list_column');
			break;
		case 'customers':
			 $colarr = array(
				"company"=>1,"active"=>1,"customerGroups"=>1,"datecreated"=>1,"vat"=>1,"phonenumber"=>1,"country"=>1,"city"=>1,"zip"=>1,"state"=>1,"address"=>1,"website"=>1
			); 
			custom_update1($colarr,'customers','clients_list_column_order','clients_list_column_order');
			break;
		case 'contacts':
			$colarr = array(
            "firstname"=>1,"email"=>1,"company"=>1,"phonenumber"=>1,"title"=>1,"active"=>1
            ); 
			custom_update1($colarr,'contacts','contacts_list_column_order','contacts_list_column_order');
			break;
		case 'projects':
			 $colarr = array("id"=>1,"name"=>1,"project_cost"=>1,"teamleader_name"=>1,"contact_name"=>1,"product_qty"=>1,"product_amt"=>1,"company"=>1,"tags"=>1,"start_date"=>1,"deadline"=>1,"members"=>1,"status"=>1,"project_status"=>1,"pipeline_id"=>1,"contact_email1"=>1,"contact_phone1"=>1); 
			custom_update1($colarr,'projects','projects_list_column_order','projects_list_column_order');
			break;
		case 'tasks':
			 $colarr = array("id"=>1,"task_name"=>1,"project_name"=>1,"project_status"=>1,"company"=>1,"teamleader"=>1,"project_contacts"=>1,"status"=>1,"tasktype"=>1,"description"=>1,"startdate"=>1,"assignees"=>1,"tags"=>1,"priority"=>1);  
			custom_update1($colarr,'tasks','tasks_list_column_order','tasks_list_column_order');
			break;
	}
	return true;
}
function custom_update1($colarr,$custom,$option,$settings){
	$CI   = &get_instance();
	$cf = get_custom_fields($custom);
	foreach($cf as $custom_field) {
		$cur_arr = array('ins'=>$custom_field['slug'],'ll'=>$custom_field['name']);
		$colarr[$custom_field['slug']] = $cur_arr;
	}
	$targets_list_column_order = (array)json_decode(get_option($option));
	$i = 0;
	$setting_update = 1;
	$target_setting = array();
	foreach($targets_list_column_order as $ckey=>$cval){ 
		if(isset($colarr[$ckey])){
			$target_setting[$ckey] = 1;
		}else{
			$setting_update = 2;
		}
	}
	if($setting_update == 2){
		$post_data['settings'][$settings] = json_encode($target_setting);
		$CI->settings_model->update($post_data);
		
	}
	return true;
}
function get_reminder_data($post_data){
	$setting_data = array();
	$setting_data = array('reminder_type'=>'','customer_reminder'=>'','customer_mail'=>'','act_notify'=>'','act_alert'=>'','act_mail'=>'','act_date_time'=>'','act_month'=>'','act_day'=>'','pr_notify'=>'','pr_mail'=>'','pr_date_time'=>'','pr_month'=>'','pr_day'=>'','tar_notify'=>'','tar_mail'=>'','tar_date_time'=>'','tar_month'=>'','tar_day'=>'');
	if($post_data['remind_status'] == 'enable'){
		$setting_data['reminder_type'] = json_encode($post_data['reminder_type']);
		if(!empty($post_data['customer_reminder'])){
			$setting_data['customer_reminder'] = $post_data['customer_reminder'];
		}
		if(isset($post_data['customer_d']) && isset($post_data['customer_h']) && isset($post_data['customer_m'])){
			$setting_data['customer_mail'] = $post_data['customer_d'].':'.$post_data['customer_h'].':'.$post_data['customer_m'];
		}
		if(!empty($post_data['activity_notify'])){
			$setting_data['act_notify'] = json_encode($post_data['activity_notify']);
		}
		if(isset($post_data['activity_alert_d']) && isset($post_data['activity_alert_h']) && isset($post_data['activity_alert_m'])){
			$setting_data['act_alert'] = $post_data['activity_alert_d'].':'.$post_data['activity_alert_h'].':'.$post_data['activity_alert_m'];
		}
		if(!empty($post_data['activity_mail']) && !empty($post_data['activity_notify'])  && in_array("include summary email", $post_data['activity_notify']) ){
			$setting_data['act_mail'] = $post_data['activity_mail'];
			if($post_data['activity_mail'] == 'daily'){
				$setting_data['act_date_time'] = $post_data['activity_daily'];
			}
			if($post_data['activity_mail'] == 'monthly'){
				$setting_data['act_date_time'] = $post_data['activity_monthly'];
				$setting_data['act_month'] = $post_data['activity_month_f'];
			}
			else{
				$setting_data['act_month'] = '';
			}
			if($post_data['activity_mail'] == 'weekly'){
				$setting_data['act_day'] = $post_data['activity_week_d'];
				$setting_data['act_date_time'] = $post_data['activity_week_t'];
			}
		}
		if(!empty($post_data['proposal_notify'])){
			$setting_data['pr_notify'] = json_encode($post_data['proposal_notify']);
		}
		if(!empty($post_data['proposal_mail'])  && !empty($post_data['proposal_notify'])  && (in_array("include summary email", $post_data['proposal_notify']) || in_array("show alert", $post_data['proposal_notify']) )){
			$setting_data['pr_mail'] = $post_data['proposal_mail'];
			if($post_data['proposal_mail'] == 'daily'){
				$setting_data['pr_date_time'] = $post_data['proposal_daily'];
			}
			if($post_data['proposal_mail'] == 'monthly'){
				$setting_data['pr_date_time'] = $post_data['proposal_monthly'];
				$setting_data['pr_month'] = $post_data['proposal_month_f'];
			}
			else{
				$setting_data['pr_month'] = '';
			}
			if($post_data['proposal_mail'] == 'weekly'){
				$setting_data['pr_day'] = $post_data['proposal_week_d'];
				$setting_data['pr_date_time'] = $post_data['proposal_week_t'];
			}
		}
		if(!empty($post_data['target_notify'])){
			$setting_data['tar_notify'] = json_encode($post_data['target_notify']);
		}
		if(!empty($post_data['target_mail'])  && !empty($post_data['target_notify'])  && (in_array("include summary email", $post_data['target_notify']) ||  in_array("show alert", $post_data['target_notify']) )){
			$setting_data['tar_mail'] = $post_data['target_mail'];
			if($post_data['target_mail'] == 'daily'){
				$setting_data['tar_date_time'] = $post_data['target_daily'];
			}
			if($post_data['target_mail'] == 'monthly'){
				$setting_data['tar_date_time'] = $post_data['target_monthy'];
				$setting_data['tar_month'] = $post_data['target_month_f'];
			}
			else{
				$setting_data['tar_month'] = '';
			}
			if($post_data['target_mail'] == 'weekly'){
				$setting_data['tar_day'] = $post_data['target_weekly_d'];
				$setting_data['tar_date_time'] = $post_data['target_weekly_t'];
			}
		}
	}
	$setting_data['remind_status'] = $post_data['remind_status'];
	return $setting_data;
}
function get_reminder_settings($user_id){
	$CI   = &get_instance();
	$table = db_prefix() . 'reminder_settings';
	$CI->db->select('*');
	$CI->db->from($table);
	$cond = array('user_id'=>$user_id);
	$CI->db->where($cond); 
	$query = $CI->db->get();
	$res = $query->row();
	return $res;
}
function check_get_msg($cur_type,$cur_lang){
	$req_date = date("Y-m-d");
	$CI   = &get_instance();
	$res = $CI->db->query("SELECT emailtemplateid ,type,slug,language,name,subject,message,fromname,fromemail,plaintext,active,order FROM " . db_prefix() . "emailtemplates WHERE type = '".$cur_type."' and language ='".$cur_lang."'")->row();
	
	return $res;
}
function check_customer_activity_admin($cur_date,$alert_type,$cus_reminder)
{
	$res = array();
	$req_date = date("Y-m-d");
	$req_time = date("Y-m-d H:i:s");
	$CI   = &get_instance();
	if($cus_reminder == 'all_activities'){
		$res = $CI->db->query("SELECT t.*,c.email as c_mail FROM " . db_prefix() . "tasks t," . db_prefix() . "task_assigned ta," . db_prefix() . "contacts c where ta.taskid= t.id and c.id = t.contacts_id  and t.startdate='".$cur_date."' and t.status!= '5' order by t.id desc")->result_array();
	}
	else{
		$res = $CI->db->query("SELECT t.*,c.email as c_mail FROM " . db_prefix() . "tasks t," . db_prefix() . "task_assigned ta," . db_prefix() . "contacts c where ta.taskid= t.id and c.id = t.contacts_id  and t.startdate='".$cur_date."' and t.status!= '5' and t.send_reminder = 'yes' order by t.id desc")->result_array();
	}
	return $res;
}
function check_customer_activity($cur_date,$staff_id,$alert_type,$cus_reminder)
{
	$res = array();
	$req_date = date("Y-m-d");
	$req_time = date("Y-m-d H:i:s");
	$CI   = &get_instance();
	if($cus_reminder == 'all_activities'){
		$res = $CI->db->query("SELECT t.*,c.email as c_mail FROM " . db_prefix() . "tasks t," . db_prefix() . "task_assigned ta," . db_prefix() . "contacts c where ta.taskid= t.id and c.id = t.contacts_id and ta.staffid = '".$staff_id."' and t.startdate='".$cur_date."' and t.status!= '5' order by t.id desc")->result_array();
	}else{
		$res = $CI->db->query("SELECT t.*,c.email as c_mail FROM " . db_prefix() . "tasks t," . db_prefix() . "task_assigned ta," . db_prefix() . "contacts c where ta.taskid= t.id and c.id = t.contacts_id and ta.staffid = '".$staff_id."' and t.startdate='".$cur_date."' and t.status!= '5' and t.send_reminder = 'yes' order by t.id desc")->result_array();
	}
	return $res;
}
function check_task_activity($cur_date,$staff_id,$alert_type)
{
	$res = array();
	$req_date = date("Y-m-d");
	$req_time = date("Y-m-d H:i:s");
	$CI   = &get_instance();
	$task_fields = "t.id,t.name,t.tasktype,t.description,t.priority,t.dateadded,t.datemodified,t.startdate,t.duedate,t.datefinished,t.addedfrom,t.is_added_from_contact,t.status,t.send_reminder,t.recurring_type,t.repeat_every,t.recurring,t.is_recurring_from,t.cycles,t.total_cycles,t.custom_recurring,t.last_recurring_date,t.rel_id,t.rel_type,t.is_public,t.contacts_id,t.billable,t.billed,t.invoice_id,t.hourly_rate,t.milestone,t.kanban_order,t.milestone_order,t.visible_to_client,t.deadline_notified,t.source_from,t.imported_id,t.call_request_id,t.call_code,t.call_msg";		
	$res = $CI->db->query("SELECT ".$task_fields." FROM " . db_prefix() . "tasks t," . db_prefix() . "task_assigned ta WHERE	 ta.taskid= t.id and ta.staffid = '".$staff_id."' and t.startdate='".$cur_date."' and t.status!= '5' order by t.id desc")->result_array();
			
	return $res;
}
function ins_remind($staffid,$remind_type,$type_id,$remind_date,$alert_type){
	$CI   = &get_instance();
	$ins_data = array();
	$ins_data['staff_id']     = $staffid;
	$ins_data['remind_type']  = $remind_type;
	$ins_data['type_id'] 	  = $type_id;
	$ins_data['remind_date']  = $remind_date;
	$ins_data['alert_type']   = $alert_type;
	$table = db_prefix() . 'check_remind';
	$insert_id = $CI->db->insert($table,$ins_data);
	return true;
}
function ins_remind_notification($desc,$staffid,$link,$ch_req_time,$additional_data){
	$CI   = &get_instance();
	$ins_data = array();
	$ins_data['description']    = $desc;
	$ins_data['touserid'] 		= $staffid;
	$ins_data['fromcompany']	= 1;
	$ins_data['fromuserid'] 	= 0;
	$ins_data['link'] 	 		= $link;
	$ins_data['date']  			= $ch_req_time;
	$ins_data['additional_data']= $additional_data;
	$table = db_prefix() . 'notifications';
	$insert_id = $CI->db->insert($table,$ins_data);
	return true;
}

function get_all_staffs(){
	$CI   = &get_instance();
	$CI->db->where('active', '1');
	$CI->db->select('*');
	return $CI->db->get(db_prefix() . 'staff')->result_array();
}

function check_activity_mail($cur_date,$act_mail,$act_date_time,$act_day,$act_month,$staff_id,$alert_type){
	$res = array();
	$req_date = date("Y-m-d");
	$CI   = &get_instance();
	$cur_day   =  date('w');
	$cur_date  = date('Y-m-d');
	$cur_time  = date('H:i');
	$req_date1 = $req_date2 = $req_time ='';
	$req_time = date('H:i',strtotime($act_date_time));
	if($act_mail == 'monthly'){
		$req_date1 = date('d-m',strtotime($act_date_time));
	}
	$task_fields = "t.id,t.name,t.tasktype,t.description,t.priority,t.dateadded,t.datemodified,t.startdate,t.duedate,t.datefinished,t.addedfrom,t.is_added_from_contact,t.status,t.send_reminder,t.recurring_type,t.repeat_every,t.recurring,t.is_recurring_from,t.cycles,t.total_cycles,t.custom_recurring,t.last_recurring_date,t.rel_id,t.rel_type,t.is_public,t.contacts_id,t.billable,t.billed,t.invoice_id,t.hourly_rate,t.milestone,t.kanban_order,t.milestone_order,t.visible_to_client,t.deadline_notified,t.source_from,t.imported_id,t.call_request_id,t.call_code,t.call_msg";
	$cur_d_m = date('d-m');
	if(($act_mail == 'weekly' && $act_day == $cur_day && strtotime($req_time) == strtotime($cur_time)) || ($act_mail == 'daily' && strtotime($req_time) == strtotime($cur_time) ) ||($act_mail == 'monthly' && strtotime($req_date1) == strtotime($cur_d_m) && strtotime($req_time) == strtotime($cur_time) )){
			if($act_mail == 'daily'){
				$res = $CI->db->query("SELECT ".$task_fields." FROM " . db_prefix() . "tasks t," . db_prefix() . "task_assigned ta WHERE	 ta.taskid= t.id and ta.staffid = '".$staff_id."' and DATE_FORMAT(t.startdate,'%Y-%m-%d') ='".$cur_date."' and t.status!= '5' order by t.id desc")->result_array();
				
			}
			else if($act_mail == 'weekly'){
				$ch_days = '';
				for($i=0;$i<7;$i++){
					$ch_days = "'".date('Y-m-d', strtotime('+'.$i.' days'))."',".$ch_days;
				}
				$ch_days = rtrim($ch_days,",");
				
				$res = $CI->db->query("SELECT ".$task_fields." FROM " . db_prefix() . "tasks t," . db_prefix() . "task_assigned ta WHERE ta.taskid= t.id and ta.staffid = '".$staff_id."' and DATE_FORMAT(t.startdate,'%Y-%m-%d') in(".$ch_days.") and t.status!= '5'  order by t.id desc")->result_array();
			}
			else if($act_mail == 'monthly'){
				$cur_m = date('m');
				$cur_y = date('Y');
				$next_m = date('m',strtotime('first day of +1 month'));
				$next_y = date('Y',strtotime('first day of +1 month'));
				
				if($act_month == 'current_month'){
					$res = $CI->db->query("SELECT ".$task_fields." FROM " . db_prefix() . "tasks t," . db_prefix() . "task_assigned ta WHERE ta.taskid= t.id and ta.staffid = '".$staff_id."' and MONTH(t.startdate) = '".$cur_m."' and Year(t.startdate) = '".$cur_y."' and t.status!= '5'  order by t.id desc")->result_array();
				}
				else{
					$res = $CI->db->query("SELECT ".$task_fields." FROM " . db_prefix() . "tasks t," . db_prefix() . "task_assigned ta WHERE ta.taskid= t.id and ta.staffid = '".$staff_id."' and ((MONTH(t.startdate) = '".$next_m."' and Year(t.startdate) = '".$next_y."') or (MONTH(t.startdate) = '".$cur_m."' and Year(t.startdate) = '".$cur_y."') ) and t.status!= '5' order by t.id desc")->result_array();
				}
			}
	}
	
	return $res;
}
function check_proposal_mail($pr_mail,$pr_date_time,$pr_day,$pr_month,$staff_id=''){
	$res =array();
	$cur_day   =  date('w');
	$cur_date  = date('Y-m-d');
	$cur_time  = date('H:i');
	$req_date1 = $req_time ='';
	$req_time = date('H:i',strtotime($pr_date_time));
	if($pr_mail == 'monthly'){
		$req_date1 = date('d-m',strtotime($pr_date_time));
	}
	$cur_d_m = date('d-m');
	
	$proposal_fields = "id,subject,content,addedfrom,datecreated,total,subtotal,total_tax,adjustment,discount_percent,discount_total,discount_type,show_quantity_as,currency,open_till,date,rel_id,rel_type,assigned,hash,proposal_to,country,zip,state,city,address,email,phone,allow_comments,status,estimate_id,invoice_id,date_converted,pipeline_order,is_expiry_notified,acceptance_firstname,acceptance_lastname,acceptance_email,acceptance_date,acceptance_ip,signature,template_contents,pdftemplate";
	$CI   = &get_instance();
	$req_res = array();
	if(($pr_mail == 'weekly' && $pr_day == $cur_day && strtotime($req_time) == strtotime($cur_time)) || ($pr_mail == 'daily' && strtotime($req_time) == strtotime($cur_time) ) ||($pr_mail == 'monthly' && strtotime($req_date1) == strtotime($cur_d_m) && strtotime($req_time) == strtotime($cur_time) )){
		$req_date = date("Y-m-d");
		
			if($pr_mail == 'daily'){
				$res = $CI->db->query("SELECT ".$proposal_fields." FROM " . db_prefix() . "proposals WHERE  addedfrom = '".$staff_id."' and open_till ='".$cur_date."' and status!='3'  order by id desc")->result_array();
			}
			else if($pr_mail == 'weekly'){
				$ch_days = '';
				for($i=0;$i<7;$i++){
					$ch_days = "'".date('Y-m-d', strtotime('+'.$i.' days'))."',".$ch_days;
				}
				$ch_days = rtrim($ch_days,",");
				$res = $CI->db->query("SELECT ".$proposal_fields." FROM " . db_prefix() . "proposals WHERE  addedfrom = '".$staff_id."' and  DATE_FORMAT(open_till,'%Y-%m-%d') in(".$ch_days.") and status!='3' order by id desc")->result_array();
			}
			else if($pr_mail == 'monthly'){
				$cur_m = date('m');
				$cur_y = date('Y');
				$next_m = date('m',strtotime('first day of +1 month'));
				$next_y = date('Y',strtotime('first day of +1 month'));
				
				if($pr_month == 'current_month'){
					$res = $CI->db->query("SELECT ".$proposal_fields." FROM " . db_prefix() . "proposals WHERE addedfrom = '".$staff_id."' and MONTH(open_till) = '".$cur_m."' and Year(open_till) = '".$cur_y."' and status!='3'  order by id desc")->result_array();
				}
				else{
					
					$res = $CI->db->query("SELECT ".$proposal_fields." FROM " . db_prefix() . "proposals WHERE addedfrom = '".$staff_id."' and ((MONTH(open_till) = '".$next_m."' and Year(open_till) = '".$next_y."') or (MONTH(open_till) = '".$cur_m."' and Year(open_till) = '".$cur_y."') ) and status!='3' order by id desc")->result_array();
				}
			}
	}
	return $res;
}
function check_targets_mail($tar_mail,$tar_date_time,$tar_day,$tar_month,$staff_id=''){
	$cur_day   =  date('w');
	$cur_date  = date('Y-m-d');
	$cur_time  = date('H:i');
	$req_date1 = $req_time ='';
	$req_time = date('H:i',strtotime($tar_date_time));
	if($tar_mail == 'monthly'){
		$req_date1 = date('d-m',strtotime($tar_date_time));
	}
	
	$cur_d_m = date('d-m');
	$CI   = &get_instance();
	$req_res = array();
	if(($tar_mail == 'weekly' && $tar_day == $cur_day  && strtotime($req_time) == strtotime($cur_time)) || ($tar_mail == 'daily' && strtotime($req_time) == strtotime($cur_time) ) ||($tar_mail == 'monthly' && strtotime($req_date1) == strtotime($cur_d_m) && strtotime($req_time) == strtotime($cur_time) )){
		$req_date = date("Y-m-d");
		
			if($tar_mail == 'daily'){
				$res = $CI->db->query("SELECT t.id,t.assign,t.tracking_metric,t.interval,t.start_date,t.end_date,t.target_type,t.target_status,t.create_date FROM " . db_prefix() . "target t," . db_prefix() . "target_user tu WHERE	 tu.target_id= t.id and tu.user = '".$staff_id."' and t.start_date='".$cur_date."' order by t.id desc")->result_array();
				
				$res1 = $CI->db->query("SELECT t.id,t.assign,t.tracking_metric,t.interval,t.start_date,t.end_date,t.target_type,t.target_status,t.create_date FROM " . db_prefix() . "target t," . db_prefix() . "target_manager tm WHERE tm.target_id= t.id and tm.manager = '".$staff_id."' and t.start_date='".$cur_date."' order by t.id desc")->result_array();
				
			}
			else if($tar_mail == 'weekly'){
				$res = $CI->db->query("SELECT t.id,t.assign,t.tracking_metric,t.interval,t.start_date,t.end_date,t.target_type,t.target_status,t.create_date FROM " . db_prefix() . "target t," . db_prefix() . "target_user tu WHERE tu.target_id= t.id and tu.user = '".$staff_id."' and t.start_date='".$cur_date."'  and DATE_FORMAT(t.start_date,'%Y-%m-%d') in(".$ch_days.") order by t.id desc")->result_array();
				$res1 = $CI->db->query("SELECT t.id,t.assign,t.tracking_metric,t.interval,t.start_date,t.end_date,t.target_type,t.target_status,t.create_date FROM " . db_prefix() . "target t," . db_prefix() . "target_manager tm WHERE tm.target_id= t.id and tm.manager = '".$staff_id."' and t.start_date='".$cur_date."'  and DATE_FORMAT(t.start_date,'%Y-%m-%d') in(".$ch_days.") order by t.id desc")->result_array();
			}
			else if($tar_mail == 'monthly'){
				$cur_m = date('m');
				$cur_y = date('Y');
				$next_m = date('m',strtotime('first day of +1 month'));
				$next_y = date('Y',strtotime('first day of +1 month'));
				
				if($tar_month == 'current_month'){
					$res = $CI->db->query("SELECT t.id,t.assign,t.tracking_metric,t.interval,t.start_date,t.end_date,t.target_type,t.target_status,t.create_date FROM " . db_prefix() . "target t," . db_prefix() . "target_user tu WHERE tu.target_id= t.id and tu.user = '".$staff_id."' and MONTH(t.start_date) = '".$cur_m."' and Year(t.start_date) = '".$cur_y."'  order by t.id desc")->result_array();
					$res1 = $CI->db->query("SELECT t.id,t.assign,t.tracking_metric,t.interval,t.start_date,t.end_date,t.target_type,t.target_status,t.create_date FROM " . db_prefix() . "target t," . db_prefix() . "target_manager tm WHERE tm.target_id= t.id and tm.manager = '".$staff_id."' and MONTH(t.start_date)='".$cur_m."' and Year(t.start_date) = '".$cur_y."' order by t.id desc")->result_array();
				}
				else{
					$res = $CI->db->query("SELECT t.id,t.assign,t.tracking_metric,t.interval,t.start_date,t.end_date,t.target_type,t.target_status,t.create_date FROM " . db_prefix() . "target t," . db_prefix() . "target_user tu WHERE tu.target_id= t.id and tu.user = '".$staff_id."' and ((MONTH(t.start_date) = '".$next_m."' and Year(t.start_date) = '".$next_y."') or (MONTH(t.start_date) = '".$cur_m."' and Year(t.start_date) = '".$cur_y."') ) order by t.id desc")->result_array();
				}
			}
		$i = 0;
		if(!empty($res)){
			foreach($res as $req_out){
				$req_res[$i] = $req_out;
				$i++;
			}
		}
		if(!empty($res1)){
			foreach($res1 as $req_out1){
				$req_res[$i] = $req_out1;
				$i++;
			}
		}
	}
	
	return $req_res;
}

 function get_task_assignees($id)
{
	$CI   = &get_instance();
	$CI->db->select('id,' . db_prefix() . 'task_assigned.staffid as assigneeid,assigned_from,firstname,lastname,CONCAT(firstname, " ", lastname) as full_name,is_assigned_from_contact');
	$CI->db->from(db_prefix() . 'task_assigned');
	$CI->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'task_assigned.staffid');
	$CI->db->where('taskid', $id);
	$CI->db->order_by('firstname', 'asc');

	return $CI->db->get()->row();
}
function remind_send_mail($to_mail,$subject,$req_msg,$req_name1=''){
	$CI   = &get_instance();
	$CI->load->library('email');
	$smtpconf = array();
	 $smtpsettings = $CI->db->get(db_prefix() . 'options')->result_array();
	foreach($smtpsettings as $config) {
		if($config['name'] == 'smtp_host')
			$smtpconf['host'] = $config['value'];
		if($config['name'] == 'smtp_encryption')
			$smtpconf['encrypto'] = $config['value'];
		if($config['name'] == 'smtp_username')
			$smtpconf['username'] = $config['value'];
		if($config['name'] == 'smtp_password')
			$smtpconf['password'] = $config['value'];
		if($config['name'] == 'smtp_port')
			$smtpconf['port'] = $config['value'];
	}
	$smtpconf['validate'] = true;
	$CI->email->initialize($smtpconf);
	$CI->email->from(get_option('smtp_email'), $req_name1);
	$list = array($to_mail);
	$CI->email->to($list);
	$CI->email->subject($subject);
	$CI->email->message($req_msg);
	$CI->email->send();
	return true;
}
function get_act_msg($req_msg,$act_1){
	$CI   = &get_instance();						
	if(!empty($act_1['name'])){
		$req_msg = str_replace("{task_subject}",$act_1['name'],$req_msg);
	}
	if(!empty($act_1['tasktype'])){
		$res = $CI->db->query("SELECT id,name,status,created_date,created_by,updated_date,updated_by FROM " . db_prefix() . "tasktype WHERE id = '".$act_1['tasktype']."'")->row();
		$cur_type = '';
		if(!empty($res->name)){
			$cur_type = $res->name;
		}
		$req_msg = str_replace("{task_type}",$cur_type,$req_msg);
	}
	else{
		$req_msg = str_replace("{task_type}",'',$req_msg);
	}
	if(!empty($act_1['description'])){
		$req_msg = str_replace("{task_description}",$act_1['description'],$req_msg);
	}
	else{
		$req_msg = str_replace("{task_description}",'',$req_msg);
	}
	if(!empty($act_1['priority'])){
		$req_msg = str_replace("{task_priority}",task_priority($act_1['priority']),$req_msg);
	}
	else{
		$req_msg = str_replace("{task_priority}",'',$req_msg);
	}
	if(!empty($act_1['id'])){
			$req_val = get_task_assignees($act_1['id']);
			$cur_asgn = '';
		if(!empty($req_val->full_name)){
			$cur_asgn = $req_val->full_name;
		}
		$req_msg = str_replace("{task_assign}",$cur_asgn,$req_msg);
	}
	else{
		$req_msg = str_replace("{task_assign}",'',$req_msg);
	}
	if(!empty($act_1['startdate'])){
		$req_msg = str_replace("{task_date}",date('d-m-Y',strtotime($act_1['startdate'])),$req_msg);
		$req_msg = str_replace("{task_time}",date('H:i',strtotime($act_1['startdate'])),$req_msg);
	}
	else{
		$req_msg = str_replace("{task_date}",'',$req_msg);
		$req_msg = str_replace("{task_time}",'',$req_msg);
	}
	$contact_fields = "id,userid,userids,is_primary,firstname,lastname,email,phonenumber,alternative_emails,alternative_phonenumber,title,datecreated,password,new_pass_key,new_pass_key_requested,email_verified_at,email_verification_key,email_verification_sent_at,last_ip,last_login,last_password_change,active,profile_image,direction,invoice_emails,estimate_emails,credit_note_emails,contract_emails,task_emails,project_emails,ticket_emails,deleted_status,addedfrom";
	if(!empty($act_1['contacts_id'])){
		$res = $CI->db->query("SELECT ".$contact_fields." FROM " . db_prefix() . "contacts WHERE id = '".$act_1['contacts_id']."'")->row();
		$req_val = get_task_assignees($act_1['contacts_id']);
		$cur_contact = '';
		if(!empty($res->firstname)){
			$cur_contact = $res->firstname.' '.$res->lastname;
		}
		
		$req_msg = str_replace("{task_contact}",$cur_contact,$req_msg);
	}
	else{
		$req_msg = str_replace("{task_contact}",'',$req_msg);
	}
	if(!empty($act_1['status'])){ 
		$status   = get_task_status_by_id($act_1['status']);
		$req_msg = str_replace("{task_status}",$status['name'],$req_msg);
	}
	else{
		$req_msg = str_replace("{task_status}",'',$req_msg);
	}
	if(!empty($act_1['rel_id'])){
								
		$res = $CI->db->query("SELECT name,clientid FROM " . db_prefix() . "projects WHERE id = '".$act_1['rel_id']."'")->row();
		
		$cur_deal = '';
		if(!empty($res->name)){
			$cur_deal = $res->name;
		}
		
		$req_msg = str_replace("{task_deal}",$cur_deal,$req_msg);
		if(!empty($res->clientid)){
			$res = $CI->db->query("SELECT company FROM " . db_prefix() . "clients WHERE userid = '".$res->clientid."'")->row();
			$cur_org = '';
			if(!empty($res->company)){
				$cur_org = $res->company;
			}
			$req_msg = str_replace("{task_org}",$cur_org,$req_msg);
		}
		else{
			$req_msg = str_replace("{task_org}",'',$req_msg);
		}
	}
	else{
		$req_msg = str_replace("{task_deal}",'',$req_msg);
		$req_msg = str_replace("{task_org}",'',$req_msg);
	}
	return $req_msg;
}
function get_prop_msg($req_msg,$prop){
	$CI   = &get_instance();	
	if ($prop['currency'] != 0) {
		 $CI->db->where('id', $prop['currency']);
         $currency = $CI->db->get(db_prefix() . 'currencies')->row();
	} else {
		$currency = get_base_currency();
	}
	if(!empty($prop['id'])){
		$req_msg = str_replace("{proposal_id}",$prop['id'],$req_msg);
		$req_msg = str_replace("{proposal_number}",format_proposal_number($prop['id']),$req_msg);
		$req_msg = str_replace("{proposal_link}", site_url('proposal/' . $prop['id'] . '/' . $prop['hash']),$req_msg);
	}
	
	if(!empty($prop['subject'])){
		$req_msg = str_replace("{proposal_subject}",$prop['subject'],$req_msg);
	}
	if(!empty($prop['total'])){
		$req_msg = str_replace("{proposal_total}",app_format_money($prop['total'],$currency),$req_msg);
	}
	if(!empty($prop['subtotal'])){
		$req_msg = str_replace("{proposal_subtotal}",app_format_money($prop['subtotal'],$currency),$req_msg);
	}
	if(!empty($prop['open_till'])){
		$req_msg = str_replace("{proposal_open_till}",_d($prop['open_till']),$req_msg);
	}
	
	if(!empty($prop['proposal_to'])){ 
		$req_msg = str_replace("{proposal_proposal_to}",$prop['proposal_to'],$req_msg);
	}
	if(!empty($prop['address'])){ 
		$req_msg = str_replace("{proposal_address}",$prop['address'],$req_msg);
	}
	else{
		$req_msg = str_replace("{proposal_address}",'',$req_msg);
	}
	if(!empty($prop['email'])){ 
		$req_msg = str_replace("{proposal_email}",$prop['email'],$req_msg);
	}
	else{
		$req_msg = str_replace("{proposal_email}",'',$req_msg);
	}
	if(!empty($prop['phone'])){ 
		$req_msg = str_replace("{proposal_phone}",$prop['phone'],$req_msg);
	}
	else{
		$req_msg = str_replace("{proposal_phone}",'',$req_msg);
	}
	if(!empty($prop['city'])){ 
		$req_msg = str_replace("{proposal_city}",$prop['city'],$req_msg);
	}
	else{
		$req_msg = str_replace("{proposal_city}",'',$req_msg);
	}
	if(!empty($prop['state'])){ 
		$req_msg = str_replace("{proposal_state}",$prop['state'],$req_msg);
	}
	else{
		$req_msg = str_replace("{proposal_state}",'',$req_msg);
	}
	if(!empty($prop['zip'])){ 
		$req_msg = str_replace("{proposal_zip}",$prop['zip'],$req_msg);
	}
	else{
		$req_msg = str_replace("{proposal_zip}",'',$req_msg);
	}
	if(!empty($prop['country'])){ 
		 $CI->db->where('country_id', $prop['country']);
        $country = $CI->db->get(db_prefix().'countries')->row();
		$req_msg = str_replace("{proposal_country}",$country->short_name,$req_msg);
	}
	else{
		$req_msg = str_replace("{proposal_country}",'',$req_msg);
	}
	return $req_msg;
}
function get_tar_msg($req_msg,$tar){
	$CI   = &get_instance();	
	
	if(!empty($tar['assign'])){
		$req_msg = str_replace("{assign}",$tar['assign'],$req_msg);
	}
	
	if(!empty($tar['tracking_metric'])){
		$req_msg = str_replace("{tracking_metric}",$tar['tracking_metric'],$req_msg);
	}
	if(!empty($tar['target_type'])){
		$req_msg = str_replace("{target_type}",$tar['id'],$req_msg);
	}
	if(!empty($tar['id'])){
		$CI->db->where('target_id', $tar['id']);
        $target_user1 = $CI->db->get(db_prefix().'target_user')->result_array();
		$target_user1 = array_column($target_user1, 'user'); 
		if(!empty($target_user1)){
			$search_manager = implode(', ', $target_user1);

			$manager_res = $CI->db->query("SELECT  GROUP_CONCAT(CONCAT(`firstname`, ' ', `lastname`) SEPARATOR ', ') as contact_name FROM " . db_prefix() ."staff WHERE staffid in( ".$search_manager.")")->result_array();
			$target_user1 = array_column($manager_res, 'contact_name'); 
			$search_manager = implode(', ', $target_user1);
			$req_msg = str_replace("{user}", $search_manager,$req_msg);
		}
		else{
			$req_msg = str_replace("{user}",'',$req_msg);
		}
	}
	
	if(!empty($tar['id'])){
		$CI->db->where('target_id', $tar['id']);
        $target_user1 = $CI->db->get(db_prefix().'target_manager')->result_array();
		$target_user1 = array_column($target_user1, 'user'); 
		if(!empty($target_user1)){
			$search_manager = implode(', ', $target_user1);
			$manager_res = $CI->db->query("SELECT  GROUP_CONCAT(CONCAT(`firstname`, ' ', `lastname`) SEPARATOR ', ') as contact_name FROM " . db_prefix() ."staff WHERE staffid in( ".$search_manager.")")->result_array();
			$target_user1 = array_column($manager_res, 'contact_name'); 
			$search_manager = implode(', ', $target_user1);
			$req_msg = str_replace("{manager}", $search_manager,$req_msg);
		}
		else{
			$req_msg = str_replace("{manager}",'',$req_msg);
		}
	}
	if(!empty($tar['id'])){
		$manager_res = $CI->db->query("SELECT  s_search FROM " . db_prefix() ."target_interval WHERE target_id = '".$tar['id']."' group by target_id")->row();
		if(!empty($manager_res->s_search)){
			$req_msg = str_replace("{count_value}",$manager_res->s_search,$req_msg);
		}
		else{
			$req_msg = str_replace("{count_value}",'',$req_msg);
		}
	}
	if(!empty($tar['id'])){
		$CI->db->where('target_id', $tar['id']);
		$target_user1 = $CI->db->get(db_prefix().'target_pipeline')->result_array();
		$target_user1 = array_column($target_user1, 'pipeline'); 
		if(!empty($target_user1)){
			$search_manager = implode(', ', $target_user1);
			$manager_res = $CI->db->query("SELECT  name FROM " . db_prefix() ."pipeline WHERE id in( ".$search_manager.")")->result_array();
			$target_user1 = array_column($manager_res, 'name'); 
			$search_manager = implode(', ', $target_user1);
			$req_msg = str_replace("{pipeline}", $search_manager,$req_msg);
		}else{
			$req_msg = str_replace("{pipeline}",'',$req_msg);
		}
	}
	if(!empty($tar['id'])){
		$CI->db->where('target_id', $tar['id']);
		$target_user1 = $CI->db->get(db_prefix().'target_stage')->row();
		if(!empty($target_user1)){
			$manager_res = $CI->db->query("SELECT  id,name,statusorder,color,progress,isdefault,status,created_date,updated_date,created_by,updated_by,filter_default FROM " . db_prefix() ."projects_status WHERE id ='".$target_user1->stage_id."'")->row();
			
			$req_msg = str_replace("{pipeline_stage}", $manager_res->name,$req_msg);
		}else{
			$req_msg = str_replace("{pipeline_stage}",'',$req_msg);
		}
	}
	else{
		$req_msg = str_replace("{pipeline_stage}",'',$req_msg);
	}
	if(!empty($tar['start_date'])){ 
		$req_msg = str_replace("{start_date}",date('d-m-Y',strtotime($tar['start_date'])),$req_msg);
	}
	if(!empty($tar['end_date'])){ 
		$req_msg = str_replace("{end_date}",date('d-m-Y',strtotime($tar['end_date'])),$req_msg);
	}
	else{
		$req_msg = str_replace("{end_date}",'',$req_msg);
	}
	if(!empty($tar['interval'])){ 
		$req_msg = str_replace("{Interval}",$tar['interval'],$req_msg);
	}
	return $req_msg;
}
function outlook_credential(){
	$client_id 		= '7eaa4912-3d74-4317-87be-3a9d37e69b3c';
	$secret_val 	= 'R4z8Q~63AL~HfY4Ua5Ql7PVueNRK~IddHSSSYcDa';
	$redirect_uri 	= base_url().'admin/outlook_mail/index';
	$scopes 		= array("offline_access", "openid","https://outlook.office.com/mail.read","https://outlook.office.com/mail.send","https://outlook.office.com/mail.readwrite");
	$authority	    = "https://login.microsoftonline.com";
	$token_url		= "/common/oauth2/v2.0/token";
	$api_url		= "https://outlook.office.com/api/v2.0";
	$credential = array('client_id'=>$client_id,'redirect_uri'=>$redirect_uri,'client_secret'=>$secret_val,'scopes'=>$scopes,'authority'=>$authority,'token_url'=>$token_url,'api_url'=>$api_url);
	return $credential;
}
function makeGuid(){
    if (function_exists('com_create_guid')) {
        error_log("Using 'com_create_guid'.");
        return strtolower(trim(com_create_guid(), '{}'));
    }
    else {
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid, 12, 4).$hyphen
            .substr($charid, 16, 4).$hyphen
            .substr($charid, 20, 12);
        return $uuid;
    }
}
function runCurl($url, $post = null, $headers = null,$custom = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
	if($custom!=null){
	 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $custom);
	}
    curl_setopt($ch, CURLOPT_POST, $post == null ? 0 : 1);
    if($post != null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if($headers != null) {
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
	
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($http_code >= 400) {
		 return '';
    }
    return $response;
}
function refresh_token($user_email,$token) {
	if(!empty($token)){
		$outlook_data = outlook_credential();
		$scopes 	= $outlook_data['scopes'];
		$token_request_data = array (
			"grant_type" => "refresh_token",
			"refresh_token" => $token,
			"redirect_uri" => $outlook_data["redirect_uri"],
			"scope" => $scopes,
			"client_id" => $outlook_data["client_id"],
			"client_secret" => $outlook_data["client_secret"]
		);
		$body = http_build_query($token_request_data);
		$response = runCurl($outlook_data["authority"].$outlook_data["token_url"], $body);
		$response = json_decode($response);
		$token = $response->access_token;
		store_outlook_token($user_email,$token,$response->refresh_token);
	}
	return true;
}
function store_outlook_token($user_email,$token,$refresh_token=''){
	$CI   = &get_instance();
	$staffid = get_staff_user_id();
	$CI->db->where('user_id', $staffid);
	$table = db_prefix().'outlook_mail';
    $sel_token = $CI->db->get($table)->result_array();
	
	if(!empty($sel_token)){
		$condition = array('user_id'=>$staffid);
		$upd_data  = array('email'=>$user_email,'token'=>$token); 
		if(!empty($refresh_token)){
			$upd_data['refresh_token']	= $refresh_token;
		}
		$CI->db->update($table, $upd_data, $condition);
	}else{
		$ins_data  = array('email'=>$user_email,'token'=>$token,'user_id'=>$staffid); 
		if(!empty($refresh_token)){
			$ins_data['refresh_token']	= $refresh_token;
		}
		$CI->db->insert($table,$ins_data);
	}
	return true;
}
function store_outlook_mail(){
	extract($_POST);
	array_push($mail_folder,"Inbox");
	$folders = json_encode($mail_folder);
	
	$CI   = &get_instance();
	$staffid = get_staff_user_id();
	$CI->db->where('user_id', $staffid);
	$table = db_prefix().'outlook_mail';
    $sel_token = $CI->db->get($table)->result_array();
	if(!empty($sel_token)){
		$condition = array('user_id'=>$staffid);
		$upd_data  = array('folder_type'=>$syn_folder,'folders'=>$folders,'filter_mail'=>$sync_emails); 
		$CI->db->update($table, $upd_data, $condition);
	}else{
		$ins_data  = array('folder_type'=>$syn_folder,'folders'=>$folders,'filter_mail'=>$sync_emails); 
		$CI->db->insert($table,$ins_data);
	}
	return true;
}
function get_outlook_token(){
	$CI   = &get_instance();
	$staffid = get_staff_user_id();
	$CI->db->where('user_id', $staffid);
	$table = db_prefix().'outlook_mail';
    $sel_token = $CI->db->get($table)->row();
	return $sel_token;
}
function get_outlook_token_bycron($staffid){
	$CI   = &get_instance();
	$CI->db->where('user_id', $staffid);
	$table = db_prefix().'outlook_mail';
    $sel_token = $CI->db->get($table)->row();
	return $sel_token;
}
function outlook_download_all_file($response){
	$CI   = &get_instance();
	$req_files = array();
	$i = 0;
	 foreach ($response["value"] as $attachment) {
        $file = $req_files[$i] =   "uploads/" . md5($attachment["ContentId"]) . "-" . $attachment["Name"];
        file_put_contents($file, base64_decode($attachment["ContentBytes"]));
		$i++;
    }
	if(count($response["value"])>1){
		$CI->load->library('zip');
		foreach ($req_files as $req_file1) {
			$CI->zip->read_file( $req_file1);
			unlink(FCPATH.'uploads/'.$req_file1);
		}
		$CI->zip->download('files.zip');
		$CI->zip->clear_data();
	}
	else{
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		header("Content-Type: text/plain");
		readfile($file);
		unlink(FCPATH.'uploads/'.$file);
	}
}
function get_attachement(){
	$attachments = array();
	$CI   = &get_instance();
	$file_count = count($_FILES['attachment']['name'])-1;
	$req_filename = $req_path = array();
	if(isset($file_count) && $file_count>0){
		for($i=0;$i<$file_count;$i++){
			if(strlen(trim($_FILES["attachment"]["name"][$i])) > 0) {
				$content = base64_encode(file_get_contents($_FILES["attachment"]["tmp_name"][$i]));
				$attachment = array(
					"@odata.type" => "#Microsoft.OutlookServices.FileAttachment",
					"Name" => $_FILES["attachment"]["name"][$i],
					"ContentBytes" => $content
				);
				array_push($attachments, $attachment);
			}
		}
	}
	return $attachments;
}
function deal_values(){
	$CI   = &get_instance();
	$colarr = deal_all_fields(); 
	$custom_fields = get_table_custom_fields('projects');
	$cus_1 = array();
	foreach($custom_fields as $cfkey=>$cfval){
		$cus_1[$cfval['slug']] =  array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
	}
	$req_out = array('all_clmns'=>$colarr,'cus_flds'=>$cus_1);
	return json_encode($req_out);
}
function deal_get_fields(){
	$colarr = array(
		'id'=>_l('the_number_sign'),
		"name"=>_l("project_name"),
		"project_cost"=>_l("project_cost"),
		"teamleader_name"=>_l("teamleader_name"),
		"contact_name"=>_l("contact_name"),
		"product_qty"=>_l("product_qty"),
		"product_count"=>_l("product_count"),
		"product_amt"=>_l("product_amt"),
		"company"=>_l("project_customer"),
		"tags"=>_l("tags"),
		"project_start_date"=>_l("project_start_date"),
		"project_deadline"=>_l("project_deadline"),
		"members"=>_l("project_members"),
		"status"=>_l("project_status"),
		"project_status"=>_l("status"),
		"pipeline_id"=>_l("pipeline"),
		"contact_email1"=>_l("company_primary_email"),
		"contact_phone1"=>_l("company_primary_phone"),
		'won_date'=>_l('won_date'),
	   'lost_date'=>_l('lost_date'),
	   'loss_reason_name'=>_l('loss_reason'),
	   'project_currency'=>_l('currency'),
	   'project_created'=>_l('create_date'),
	   'project_modified'=>_l('modified_date'),
	   'modified_by'=>_l('modified_by'),
	   'created_by'=>_l('created_by'),
	   'won_date'=>_l('won_date'),
	   'lost_date'=>_l('lost_date'),
	   'loss_reason_name'=>_l('loss_reason'),
	   'project_currency'=>_l('currency'),
	   'project_created'=>_l('create_date'),
	   'project_modified'=>_l('modified_date'),
	   'modified_by'=>_l('modified_by'),
	   'created_by'=>_l('created_by'),
	); 
	return $colarr;
}
function deal_all_fields(){
	$colarr = array(
		"id"=>array("ins"=>"id","ll"=>"id"),
		"name"=>array("ins"=>"name","ll"=>"project_name"),
		"project_cost"=>array("ins"=>"project_cost","ll"=>"project_cost"),
		"teamleader_name"=>array("ins"=>"teamleader","ll"=>"teamleader_name"),
		"contact_name"=>array("ins"=>"project_contacts","ll"=>"contact_name"),
		"product_qty"=>array("ins"=>"product_qty","ll"=>"product_qty"),
		"product_count"=>array("ins"=>"product_count","ll"=>"product_count"),
		"product_amt"=>array("ins"=>"product_amt","ll"=>"product_amt"),
		"company"=>array("ins"=>"company","ll"=>"project_customer"),
		"tags"=>array("ins"=>"tags","ll"=>"tags"),
		"project_start_date"=>array("ins"=>"start_date","ll"=>"project_start_date"),
		"project_deadline"=>array("ins"=>"deadline","ll"=>"project_deadline"),
		"members"=>array("ins"=>"project_contacts","ll"=>"project_members"),
		"status"=>array("ins"=>"status","ll"=>"project_status"),
		"project_status"=>array("ins"=>"stage_of","ll"=>"status"),
		"pipeline_id"=>array("ins"=>"pipeline_id","ll"=>"pipeline"),
		"contact_email1"=>array("ins"=>"contact_email1","ll"=>"company_primary_email"),
		"contact_phone1"=>array("ins"=>"contact_phone1","ll"=>"company_primary_phone"),
		"won_date"=>array("ins"=>"won_date","ll"=>"won_date"),
		"lost_date"=>array("ins"=>"lost_date","ll"=>"lost_date"),
		"loss_reason_name"=>array("ins"=>"loss_reason_name","ll"=>"loss_reason"),
		"project_currency"=>array("ins"=>"project_currency","ll"=>"currency"),
		"project_created"=>array("ins"=>"project_created","ll"=>"create_date"),
		"project_modified"=>array("ins"=>"project_modified","ll"=>"modified_date"),
		"modified_by"=>array("ins"=>"modified_by","ll"=>"modified_by"),
		"created_by"=>array("ins"=>"created_by","ll"=>"created_by"),
		); 
	return $colarr;
}
function deal_needed_fields(){
	$fields = get_option('deal_fields');
	$fields1 = get_option('deal_mandatory');
	$data['need_fields'] = $data['need_fields_edit'] =  $data['mandatory_fields1'] = array('name','teamleader_name');
	$data['need_fields_label'] = array('project_name','teamleader');
	$i = $j = 2;
	if(!empty($fields1) && $fields1 != 'null'){
		$i1 =0;
		$req_fields2 = json_decode($fields1);
		if(!empty($req_fields2)){
			foreach($req_fields2 as $req_field2){
				$data['mandatory_fields1'][$i1] = $req_field2;
				$i1++;
			}
		}
	}
	if(!empty($fields) && $fields != 'null'){
		$req_fields = json_decode($fields);
		if(!empty($req_fields)){
			foreach($req_fields as $req_field11){
				$data['need_fields_edit'][$i] = $req_field11;
				if($req_field11 == 'clientid'){
					$data['need_fields'][$i] = 'company';
					$data['need_fields_label'][$j] = 'project_customer';
				}
				else if($req_field11 == 'primary_contact'){
					$data['need_fields_label'][$j] = 'project_primary_contacts';
					$data['need_fields'][$i] = 'contact_email1';
					$i++;
					$data['need_fields'][$i] = 'contact_phone1';
					$i++;
					$data['need_fields'][$i] = 'contact_name';
				}
				else if($req_field11 == 'teamleader'){
					$data['need_fields'][$i] = 'teamleader_name';
					$data['need_fields_label'][$j] = 'teamleader';
				}
				else if($req_field11 == 'project_members[]'){
					$data['need_fields'][$i] = 'members';
					$data['need_fields_label'][$j] = 'project_members';
				}
				else if($req_field11 == 'project_contacts[]'){
					$data['need_fields'][$i] = 'project_contacts[]';
					$data['need_fields_label'][$j] = 'project_contacts';
				}
				else if($req_field11 == 'project_cost'){
					$data['need_fields'][$i] = 'project_cost';
					$data['need_fields_label'][$j] = 'project_total_cost';
				}
				else if($req_field11 == 'pipeline_id'){
					$data['need_fields'][$i] = 'pipeline_id';
					$data['need_fields_label'][$j] = 'pipeline';
				}
				else{
					
					$data['need_fields_label'][$j] = $req_field11;
					$data['need_fields'][$i] = $req_field11;
					if($req_field11 == 'status'){
						$i++;
						$data['need_fields_label'][$j] = 'project_status';
						$data['need_fields'][$i] = 'project_status';
					}
				}
				$i++;
				$j++;
			}
			
		}
	}
	$data['need_fields'][$i] = 'id';
	$i++;
	$data['need_fields'][$i] = 'product_qty';
	$i++;
	$data['need_fields'][$i] = 'product_count';
	$i++;
	$data['need_fields'][$i] = 'product_amt';
	$i++;
	$data['need_fields'][$i] = 'projects_budget';
	$i++;
	$data['need_fields'][$i] = 'customers_hyperlink';
	$i++;
	$data['need_fields'][$i] = 'won_date';
     $i++;
	$data['need_fields'][$i] = 'lost_date';
	$i++;
	$data['need_fields'][$i] = 'loss_reason_name';
	$i++;
	$data['need_fields'][$i] = 'project_currency';
	$i++;
	$data['need_fields'][$i] = 'project_created';
	$i++;
	$data['need_fields'][$i] = 'project_modified';
	$i++;
	$data['need_fields'][$i] = 'modified_by';
	$i++;
	$data['need_fields'][$i] = 'created_by';
	return json_encode($data);
}
function get_public($report_id){
	$CI   = &get_instance();
	$req_out = '';
	$links = $CI->db->query("SELECT id,report_id,link_name,share_link FROM " . db_prefix() . "report_public WHERE report_id = '".$report_id."' ")->result_array();
	if(!empty($links)){
		foreach($links as $link12){
			$req_id = "'".$link12['id']."'";
			$req_out .= '<div class="form-group" app-field-wrapper="name" style="float:left;width:100%"><label for="name" class="control-label"> '.$link12['link_name'].' <a href="javascript:void(0)" onclick="check_publick('.$req_id.')" style="margin-left:5px;" data-toggle="modal" data-target="#clientid_add_modal_public"><i class="fa fa-edit"></i></a></label><br><input type="text" id="name_'.$link12['id'].'" name="name" class="form-control" value="'.base_url('shared/index/'.$link12['share_link']).'"  readonly style="width:75%;float:left;"><button onclick="myFunction('.$req_id.')" style="float:left;margin-left:15px;height:35px;">Copy Link</button><a href="javascript:void(0);" onclick="delete_link('.$req_id.')" style="margin-left:10px;float:left"><i class="fa fa-trash fa-2x" style="color:red"></i></a></div>
					';
		}
	}
	echo $req_out;
}
function get_tasks_need_fields(){
	$fields = get_option('deal_fields');
	$data =array();
	$data['need_fields'] = array('project_name','id','tasktype','priority','assignees','task_name','description','tags','company','project_contacts','teamleader','status','project_status','startdate','dateadded','datemodified','datefinished','project_pipeline','rel_type');
	if(!empty($fields) && $fields != 'null'){
		$req_fields = json_decode($fields);
		if(!empty($req_fields)){
			foreach($req_fields as $req_field11){
				if($req_field11 == 'clientid'){
					$data['need_fields'][] = 'company';
				}
				else if($req_field11 == 'project_contacts[]'){
					$data['need_fields'][]= 'project_contacts';
				}
				else if($req_field11 == 'teamleader'){
					$data['need_fields'][]= 'teamleader';
				}
				else if($req_field11 == 'status'){
					$data['need_fields'][]= 'status';
					$data['need_fields'][]= 'project_status';
				}
				else if($req_field11 == 'startdate'){
					$data['need_fields'][]= 'startdate';
				}
			}
		}
	}
	return $data;
}
function task_values(){
	$CI   = &get_instance();
	$colarr = task_all_columns(); 
	$custom_fields = get_table_custom_fields('tasks');
	$cus_1 = array();
	foreach($custom_fields as $cfkey=>$cfval){
		$cus_1[$cfval['slug']] =  array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
	}
	$req_out = array('all_clmns'=>$colarr,'cus_flds'=>$cus_1);
	return json_encode($req_out);
}
function get_tasks_all_fields()
{
	$aColumns_temp = array(
		'id'=>db_prefix() . 'tasks.id as id',
		'task_name'=>db_prefix() . 'tasks.name as task_name',
		'project_name'=>db_prefix() . 'projects.name as project_name',
		'project_status'=>db_prefix() . 'projects_status.name as project_status',
		'project_pipeline'=>db_prefix() . 'pipeline.name as project_pipeline',
		'company'=>db_prefix() . 'clients.company as company',
		'teamleader'=>db_prefix() . 'projects.teamleader as p_teamleader', 
		'status'=>db_prefix() .'tasks.status as status',
		'tasktype'=>db_prefix() . 'tasktype.name as tasktype',
		'project_contacts'=>db_prefix() . 'contacts.firstname as project_contacts', 
		'startdate'=>'startdate', 
		'dateadded'=>'dateadded', 
		'datemodified'=>'datemodified', 
		'datefinished'=>'datefinished', 
		'assignees'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM '.db_prefix().'staff where staffid IN (select staffid from tbltask_assigned where taskid = '.db_prefix().'tasks.id)) as assignees',
		'tags'=>'(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'tasks.id and rel_type="task" ORDER by tag_order ASC) as tags',
		'priority'=>'priority',
		'description'=>db_prefix() . 'tasks.description as description',
		'rel_type'=>db_prefix() . 'tasks.rel_type as rel_type',
	);
	return $aColumns_temp;
}
function task_get_fields(){
	$colarr = array(
		"id"=>_l("the_number_sign"),
		"task_name"=>_l("tasks_dt_name"),
		"tasktype"=>_l("task_type"),
		"status"=>_l("task_status"),
		"description"=>_l("description"),
		"startdate"=>_l("scheduled_date"),
		"dateadded"=>_l("create_date"),
		"datemodified"=>_l("modified_date"),
		"datefinished"=>_l("finished_date"),
		"assignees"=>_l("task_assigned"),
		"tags"=>_l("tags"),
		"project_name"=>_l("project_name"),
		"project_status"=>_l("project_status"),
		"project_pipeline"=>_l("pipeline"),
		"company"=>_l("client"),
		"teamleader"=>_l("teamleader"),
		"project_contacts"=>_l("project_contacts"),
		"priority"=>_l("tasks_list_priority"),
		"rel_type"=>_l("Type"),
	); 
	return $colarr;
}
function task_all_columns(){
	$colarr = array(
		"id"=>array("ins"=>"id","ll"=>"the_number_sign"),
		"task_name"=>array("ins"=>"task_name","ll"=>"tasks_dt_name"),
		"tasktype"=>array("ins"=>"tasktype","ll"=>"task_type"),
		"status"=>array("ins"=>"status","ll"=>"task_status"),
		"description"=>array("ins"=>"description","ll"=>"description"),
		"startdate"=>array("ins"=>"startdate","ll"=>"scheduled_date"),
		"dateadded"=>array("ins"=>"dateadded","ll"=>"create_date"),
		"datemodified"=>array("ins"=>"modified_date","ll"=>"modified_date"),
		"datefinished"=>array("ins"=>"finished_date","ll"=>"finished_date"),
		"assignees"=>array("ins"=>"assignees","ll"=>"task_assigned"),
		"tags"=>array("ins"=>"tags","ll"=>"tags"),
		"project_name"=>array("ins"=>"project_name","ll"=>"project_name"),
		"project_status"=>array("ins"=>"project_status","ll"=>"project_status"),
		"project_pipeline"=>array("ins"=>"project_pipeline","ll"=>"pipeline"),
		"company"=>array("ins"=>"company","ll"=>"client"),
		"teamleader"=>array("ins"=>"teamleader","ll"=>"teamleader"),
		"project_contacts"=>array("ins"=>"project_contacts","ll"=>"project_contacts"),
		"priority"=>array("ins"=>"priority","ll"=>"tasks_list_priority"),
		"rel_type"=>array("ins"=>"type","ll"=>"rel_type"),
	); 
	return $colarr;
}
function task_relatedto_list()
{
	$relatedto =array();
	if(has_permission('projects', '','view')){
		$relatedto ['project'] =_l('project');
	}
	if(has_permission('customers', '','view')){
		$relatedto ['customer'] =_l('client');
	}
	if(has_permission('leads', '','view')){
		$relatedto ['lead'] =_l('lead');
	}
	if(has_permission('contacts', '','view')){
		$relatedto ['contact'] =_l('contact');
	}
	return $relatedto;
}
function task_count_cond(){
	$CI   = &get_instance();
	$where_cond = '';
	if(!empty($_REQUEST['today_tasks']) || (!empty($_REQUEST['cur_val1']) && $_REQUEST['cur_val1']=='today_tasks')){
		$where_cond = " where ".db_prefix()."tasks.startdate like '%".date('Y-m-d')."%' ";
	}
	if(!empty($_REQUEST['tomorrow_tasks']) || (!empty($_REQUEST['cur_val1']) && $_REQUEST['cur_val1']=='tomorrow_tasks')){
		$tomorrow = date("Y-m-d", strtotime("+1 day"));
		$where_cond = " where ".db_prefix()."tasks.startdate like '%".$tomorrow."%' ";
	}
	if(!empty($_REQUEST['yesterday_tasks']) || (!empty($_REQUEST['cur_val1']) && $_REQUEST['cur_val1']=='yesterday_tasks')){
		$yesterday = date("Y-m-d", strtotime("-1 day"));
		$where_cond = " where ".db_prefix()."tasks.startdate like '%".$yesterday."%' ";
	}
	if(!empty($_REQUEST['thisweek_tasks']) || (!empty($_REQUEST['cur_val1']) && $_REQUEST['cur_val1']=='thisweek_tasks')){
		$week_start = date('Y-m-d',strtotime('sunday this week',strtotime("-1 week +1 day"))).' 00:00:00';
		$week_end = date('Y-m-d',strtotime('saturday this week')).' 23:59:59';
		$where_cond = " where ".db_prefix()."tasks.startdate >= '".$week_start."' and ".db_prefix()."tasks.startdate <= '".$week_end."' ";
	}
	if(!empty($_REQUEST['lastweek_tasks']) || (!empty($_REQUEST['cur_val1']) && $_REQUEST['cur_val1']=='lastweek_tasks')){
		$week_start = date('Y-m-d',strtotime('sunday this week',strtotime("-2 week +1 day"))).' 00:00:00';
		$week_end = date('Y-m-d',strtotime('saturday this week',strtotime("-1 week +1 day"))).' 23:59:59';
		$where_cond = " where ".db_prefix()."tasks.startdate >= '".$week_start."' and ".db_prefix()."tasks.startdate <= '".$week_end."' ";
	} 
	if(!empty($_REQUEST['nextweek_tasks']) || (!empty($_REQUEST['cur_val1']) && $_REQUEST['cur_val1']=='nextweek_tasks')){
		$week_start = date('Y-m-d',strtotime('sunday this week',strtotime("+0 week +1 day"))).' 00:00:00';
		$week_end = date('Y-m-d',strtotime('saturday this week',strtotime("+1 week +1 day"))).' 23:59:59';
		$where_cond = " where ".db_prefix()."tasks.startdate >= '".$week_start."' and ".db_prefix()."tasks.startdate <= '".$week_end."' ";
	}
	if(!empty($_REQUEST['thismonth_tasks']) || (!empty($_REQUEST['cur_val1']) && $_REQUEST['cur_val1']=='thismonth_tasks')){
		$where_cond = " where month(".db_prefix()."tasks.startdate) = '".date('m')."' and year(".db_prefix()."tasks.startdate) = '".date('Y')."' ";
	}
	if(!empty($_REQUEST['lastmonth_tasks']) || (!empty($_REQUEST['cur_val1']) && $_REQUEST['cur_val1']=='lastmonth_tasks')){
		$month = date('m',strtotime('last month'));
		$year  = date('Y',strtotime('last month'));
		$where_cond = " where month(".db_prefix()."tasks.startdate) = '".$month."' and year(".db_prefix()."tasks.startdate) = '".$year."' ";
	}
	if(!empty($_REQUEST['nextmonth_tasks']) || (!empty($_REQUEST['cur_val1']) && $_REQUEST['cur_val1']=='nextmonth_tasks')){
		$date = date('01-m-Y');
		$month = date("m", strtotime ('+1 month',strtotime($date)));
		$year = date("Y", strtotime ('+1 month',strtotime($date)));
		$where_cond = " where  month(".db_prefix()."tasks.startdate) = '".$month."' and year(".db_prefix()."tasks.startdate) = '".$year."' ";
	}  
	if(!empty($_REQUEST['custom_tasks']) || (!empty($_REQUEST['cur_val1']) && $_REQUEST['cur_val1']=='custom_tasks')){
		$month_start = date('Y-m-d',strtotime($_REQUEST['custom_date_start_tasks'])).' 00:00:00';
		$month_end   = date('Y-m-d',strtotime($_REQUEST['custom_date_end_tasks'])).' 23:59:59';
		$where_cond  = " where ".db_prefix()."tasks.startdate >= '".$month_start."' and ".db_prefix()."tasks.startdate <= '".$month_end."' ";
	}
	$in_cond = '';
	if(!empty($_REQUEST['upcoming_tasks']) || (!empty($_REQUEST['cur_val1']) && $_REQUEST['cur_val1']=='upcoming_tasks')){
		$where_cond = " where ".db_prefix()."tasks.status = '1' ";
	}
	if(!empty($_REQUEST['task_status_1'])){
		$in_cond .= '1,';
	}
	if(!empty($_REQUEST['task_status_2'])){
		$in_cond .= '2,';
	}
	if(!empty($_REQUEST['task_status_3'])){
		$in_cond .= '3,';
	}
	if(!empty($_REQUEST['task_status_4'])){
		$in_cond .= '4,';
	}
	if(!empty($_REQUEST['task_status_5'])){
		$in_cond .= '5,';
	}
	if(!empty($in_cond)){
		$in_cond= rtrim($in_cond, ',');
		//if(!empty($where_cond))
			$where_cond .= " and ".db_prefix()."tasks.status in(".$in_cond.") ";
		/* else
			$where_cond  = " and ".db_prefix()."tasks.status in(".$in_cond.") "; */
	}
	$cond	= array('taskid!='=>'0');
	$CI->db->select('staffid');
	$CI->db->from(db_prefix()."task_assigned");
	$CI->db->where($cond); 
	$CI->db->group_by('staffid'); 
	$query = $CI->db->get();
	$assigns = $query->result_array();
	$all_staff = array();
	if(!empty($assigns)){
		foreach($assigns as $assign1){
			if(!empty($_REQUEST['task_assigned_'.$assign1['staffid']]) ){
				$all_staff[] = $assign1['staffid'];
			}
		}
	}
	if(!empty($_REQUEST['my_tasks']) && empty($all_staff)){
		$all_staff[] = get_staff_user_id();
		
		if(!empty($where_cond))
			$where_cond .= " and ".db_prefix()."tasks.id in(SELECT taskid FROM ".db_prefix(). "task_assigned WHERE staffid=".get_staff_user_id().") ";
		else
			$where_cond  = " where ".db_prefix()."tasks.id in(SELECT taskid FROM ".db_prefix(). "task_assigned WHERE staffid=".get_staff_user_id().") ";
	}
	if(!empty($all_staff)){
		if(!empty($where_cond))
			$where_cond .= " and ".db_prefix()."tasks.id in(select taskid from ".db_prefix()."task_assigned where staffid IN(".implode(',',$all_staff).")) ";
		else
			$where_cond  = " where ".db_prefix()."tasks.id in(select taskid from ".db_prefix()."task_assigned where staffid IN(".implode(',',$all_staff).")) ";
	}
	if(!empty($_REQUEST['cur_val']) && $_REQUEST['cur_val']=='my_tasks'){
		$req_cond = (!empty($where_cond))?" and ":" where ";
		$where_cond  .= $req_cond.db_prefix()."tasks.id IN(SELECT taskid FROM ".db_prefix(). "task_assigned WHERE staffid=".get_staff_user_id().") ";
	}
	if(!empty($_REQUEST['task_type']) ){
		$_REQUEST['task_type'] = trim($_REQUEST['task_type'],",");
		$req_cond = (!empty($where_cond))?" and ":" where ";
		$where_cond  .= $req_cond.db_prefix()."tasks.tasktype IN(".$_REQUEST['task_type'].") ";
	}
	if(!empty($_REQUEST['task_assign']) ){
		$_REQUEST['task_assign'] = trim($_REQUEST['task_assign'],",");
		$req_cond = (!empty($where_cond))?" and ":" where ";
		$where_cond  .= $req_cond.db_prefix()."tasks.id IN(select taskid from ".db_prefix()."task_assigned where staffid IN(".$_REQUEST['task_assign'].")) ";
	}
	if(!empty($_REQUEST['task_project']) ){
		$req_cond = (!empty($where_cond))?" and ":" where ";
		$where_cond  .= $req_cond.db_prefix()."tasks.rel_id = '".$_REQUEST['task_project']."' and rel_type = 'project' ";
	}
	$fields = "id,name";
	$cond	= array('status'=>'Active');
	$CI->db->select($fields);
	$CI->db->from(db_prefix()."tasktype");
	$CI->db->where($cond); 
	$query = $CI->db->get();
	$types = $query->result_array();
	if(!empty($types)){
		$i = 0;
		$req_where = '';
		foreach($types as $type1){
			if(!empty($_REQUEST['task_tasktype_'.$type1['id']])){
				if(empty($where_cond)){
					 $req_where =  1;
					$where_cond = " where ( ".db_prefix()."tasks.tasktype = (select id from ".db_prefix()."tasktype where name='".$type1['name']."' and status ='Active') ";
				}
				else{
					if(empty($req_where)){
						$req_where = 1;
						$where_cond .= " and ( ".db_prefix()."tasks.tasktype = (select id from ".db_prefix()."tasktype where name='".$type1['name']."' and status ='Active') ";
					}
					else{
						$req_where = 1;
						$where_cond .= " or ".db_prefix()."tasks.tasktype = (select id from ".db_prefix()."tasktype where name='".$type1['name']."' and status ='Active') ";
					}
				}
				$i++;
			}
		}
		if(!empty($req_where)){
			$where_cond .= " )";
		}
	}
	return $where_cond;
}


function api_tasks_summary_data($rel_id = null, $rel_type = null)
{
    $CI            = &get_instance();
    $tasks_summary = [];
    $statuses      = ['overdue','today','tomorrow','dayaftertomorrow'];
    //pr($statuses); exit;
    foreach ($statuses as $status) {
        $tasks_where = 'rel_type != "" AND ';
        if($status == 'overdue') {
			$tasks_where = ' date(startdate) < CURDATE() AND status != 5 ';
        } elseif($status == 'today') {
            $tasks_where = ' date(startdate) = CURDATE()';
        } elseif($status == 'tomorrow') {
            $tasks_where = ' date(startdate) = (CURDATE()+1)';
        }  elseif($status== 'dayaftertomorrow') {
            $tasks_where = ' date(startdate) = (CURDATE()+2)';
        }
        $my_staffids = $CI->staff_model->get_my_staffids();
        
        $tasks_my_where = 'id IN(SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid=' . get_staff_user_id() . ') AND ' . $tasks_where;
        if ($rel_id && $rel_type) {
            if($my_staffids){
                
                $tasks_where .= ' AND rel_id=' . $rel_id . ' AND tbltasks.id IN (select taskid from tbltask_assigned where staffid IN (' . implode(',',$my_staffids) . '))';
                $tasks_my_where .= ' AND rel_id=' . $rel_id . ' AND tbltasks.id IN (select taskid from tbltask_assigned where staffid IN (' . implode(',',$my_staffids) . '))';
                //array_push($where, ' AND ' . db_prefix() . 'tasks.rel_id IN (SELECT ' . db_prefix() . 'projects.id FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ') OR  ' . db_prefix() . 'projects.teamleader in (' . implode(',',$my_staffids) . ') )');
            } else {
                $tasks_where .= ' AND rel_id=' . $rel_id . ' AND rel_type="' . $rel_type . '"';
                $tasks_my_where .= ' AND rel_id=' . $rel_id . ' AND rel_type="' . $rel_type . '"';
            }
        } else {
            if($my_staffids){
				$tasks_where .= ' AND (' . db_prefix() . 'tasks.id in (select taskid from tbltask_assigned where staffid in (' . implode(',',$my_staffids) . ')) OR ' . db_prefix() . 'tasks.rel_id IN (SELECT ' . db_prefix() . 'projects.id FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ')) )';
            	$tasks_my_where .= ' AND (' . db_prefix() . 'tasks.id in (select taskid from tbltask_assigned where staffid in (' . implode(',',$my_staffids) . ')) OR ' . db_prefix() . 'tasks.rel_id IN (SELECT ' . db_prefix() . 'projects.id FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ')))';
			}else{
				$sqlProjectTasksWhere = ' AND CASE
				WHEN rel_type="project" AND rel_id IN (SELECT project_id FROM ' . db_prefix() . 'project_settings WHERE project_id=rel_id AND name="hide_tasks_on_main_tasks_table" AND value=1)
				THEN rel_type != "project"
				ELSE 1=1
				END';
				$tasks_where .= $sqlProjectTasksWhere;
				$tasks_my_where .= $sqlProjectTasksWhere;
			}
        }
        
		$tasks_where =$tasks_where;
        $tasks_my_where = $tasks_my_where;
        $summary                   = [];
        $summary['total_tasks']    = total_rows(db_prefix() . 'tasks', $tasks_where);
        $summary['total_my_tasks'] = total_rows(db_prefix() . 'tasks', $tasks_my_where);
        $tasks_summary[$status]           = $summary;
    }
    return $tasks_summary;
}