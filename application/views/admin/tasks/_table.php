<?php

defined('BASEPATH') or exit('No direct script access allowed');
$table_datas = [
   'id'=>_l('the_number_sign'),
    'task_name'=> _l('tasks_dt_name'),
    'status'=>_l('task_status'),
    'tasktype'=> _l('tasktype'),
    'description'=>_l('description'),
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
    'assignees'=>_l('task_assigned'),
    'tags'=>_l('tags'),
    'project_name'=> '(Deal / Lead) Name',
    'project_status'=>_l('project_status'),
    'project_pipeline'=>_l('pipeline'),
    'company'=>_l('client'),
    'teamleader'=>_l('teamleader'),
    'project_contacts'=>_l('project_contacts'),
    'priority'=>_l('tasks_list_priority'),
    'rel_type'=>'Type',
];
/*$table_data_temp = array(
    'id'=>_l('the_number_sign'),
    'task_name'=> _l('tasks_dt_name'),
    'status'=>_l('task_status'),
    'tasktype'=> _l('tasktype'),
    'description'=>_l('description'),
    'startdate'=>[
        'name'     => _l('tasks_dt_datestart'),
        'th_attrs' => ['class' => 'duedate'],
    ],
    'assignees'=>_l('task_assigned'),
    'tags'=>_l('tags'),
    'project_name'=>_l('project_name'),
    'project_status'=>_l('project_status'),
    'company'=>_l('client'),
    'teamleader'=>_l('teamleader'),
    'project_contacts'=>_l('project_contacts'),
    'priority'=>_l('tasks_list_priority'),
);*/

foreach($table_datas as $ckey=>$cval){ 
	if(!empty($need_fields) && in_array($ckey, $need_fields)){
		$table_data_temp[$ckey] = $cval;
    }
    if($ckey == 'rel_type') {
        $table_data_temp[$ckey] = $cval;
    }
}
/*$custom_fields = get_table_custom_fields('projects', ['show_on_table' => 1]);
foreach($custom_fields as $cfkey=>$cfval){
    $table_data_temp[$cfval['slug']] = $cfval['name'];
}
$custom_fields = get_table_custom_fields('contacts', ['show_on_table' => 1]);
foreach($custom_fields as $cfkey=>$cfval){
    $table_data_temp[$cfval['slug']] = $cfval['name'];
}
$custom_fields = get_table_custom_fields('customers', ['show_on_table' => 1]);
foreach($custom_fields as $cfkey=>$cfval){
    $table_data_temp[$cfval['slug']] = $cfval['name'];
}*/
$custom_fields = get_table_custom_fields('tasks', ['show_on_table' => 1]);
foreach($custom_fields as $cfkey=>$cfval){
    $table_data_temp[$cfval['slug']] = $cfval['name'];
}
$tasks_list_column_order = (array)json_decode(get_option('tasks_list_column_order')); //pr($tasks_list_column_order);
//pre($table_data_temp);exit;

  

/*
$custom_fields = get_custom_fields('tasks', [
    'show_on_table' => 1,
]);

foreach ($custom_fields as $cfkey=>$cfval) {
	//$table_data_temp[$cfval['slug']] = $cfval['name'];
   //array_push($table_data_temp, $field['name']);
}*/

$table_data = array();
 foreach($tasks_list_column_order as $ckey=>$cval){
	if(isset($table_data_temp[$ckey])){
		$table_data[] =$table_data_temp[$ckey];
	}
 }
if(!isset($tasks_list_column_order['startdate'])){
	$Temp['name'] = $table_data[0];
	$Temp['th_attrs'] = ['class' => 'duedate'];
	$table_data[0] = $Temp;
}

array_unshift($table_data, [
    'name'     => '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="tasks"><label></label></div>',
    'th_attrs' => ['class' => (isset($bulk_actions) ? '' : 'not_visible')],
]);//pre($table_data);
$table_data = hooks()->apply_filters('tasks_table_columns', $table_data);
render_datatable($table_data, 'tasks', [], [
	'data-last-order-identifier' => 'tasks',
	'data-default-order'         => get_table_last_order('tasks'),
]);
