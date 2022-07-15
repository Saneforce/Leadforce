<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_datas = deal_get_fields();
$table_data_temp = array();
foreach($table_datas as $ckey=>$cval){ 
	$req_key = $ckey;
	if($req_key == 'start_date'){
		$req_key = 'project_start_date';
	}
	if($req_key == 'deadline'){
		$req_key = 'project_deadline';
	}
	if(!empty($need_fields) && in_array($req_key, $need_fields)){
		$table_data_temp[$ckey] = $cval;
	}
}
$custom_fields = get_custom_fields('projects', ['show_on_table' => 1]);
$check_cus = array();
foreach ($custom_fields  as $cfkey=>$cfval) {
    $table_data_temp[$cfval['slug']] = $cfval['name'];
}

$custom_fields = get_custom_fields('customers', ['show_on_table' => 1]);
foreach ($custom_fields  as $cfkey=>$cfval) {
    $table_data_temp[$cfval['slug']] = $cfval['name'];
}
$report_deal_list_column = (array)json_decode(get_option('report_deal_list_column_order')); 
//pr($projects_list_column_order);
$table_data = array();

$hasPermissionEdit   = has_permission('projects', '', 'edit');
if ($hasPermissionEdit) {
	//$table_data['checkbox'] = '<input type="checkbox" class="check_email" onclick="check_all(this)" id="select_all" value="all">';
}
 foreach($report_deal_list_column as $ckey=>$cval){
	 if(isset($table_data_temp[$ckey])){
			$table_data[] =$table_data_temp[$ckey];
	 }
 }

$table_data = hooks()->apply_filters('projects_table_columns', $table_data);
//pre(get_table_last_order('customers'));
$url = base_url(uri_string());
render_datatable($table_data, isset($class) ?  $class : 'projects', [], [
	'data-last-order-identifier' => 'projects',
	'data-default-order'  => get_table_last_order('projects'),
]);
