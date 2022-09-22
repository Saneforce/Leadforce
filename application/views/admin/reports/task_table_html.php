<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_datas = task_get_fields();
$table_data_temp = array();
foreach($table_datas as $ckey=>$cval){ 
	$req_key = $ckey;
	//if(!empty($need_fields) && in_array($req_key, $need_fields)){
		$table_data_temp[$ckey] = $cval;
	//}
}
$custom_fields = get_custom_fields('tasks', ['show_on_table' => 1]);
$check_cus = array();
foreach ($custom_fields  as $cfkey=>$cfval) {
    $table_data_temp[$cfval['slug']] = $cfval['name'];
}

$report_task_list_column = (array)json_decode(get_option('report_task_list_column_order')); 
$table_data = array();

 foreach($report_task_list_column as $ckey=>$cval){
	 if(isset($table_data_temp[$ckey])){
			$table_data[] =$table_data_temp[$ckey];
	 }
 }
$table_data = hooks()->apply_filters('tasks_table_columns', $table_data);
$url = base_url(uri_string());
render_datatable($table_data, isset($class) ?  $class : 'tasks_order', [], [
	'data-last-order-identifier' => 'tasks',
	'data-default-order'  => get_table_last_order('tasks'),
]);