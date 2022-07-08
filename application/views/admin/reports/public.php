<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_datas = [
   'name'=>_l('project_name'),
   'teamleader_name'=>_l('teamleader_name'),
   'contact_name'=>_l('contact_name'),
   'project_cost'=>_l('project_cost'),
   'product_qty'=>_l('product_qty'),
   'product_amt'=>_l('product_amt'),
   'company'=>  _l('project_customer'),
   'tags'=>_l('tags'),
   'project_start_date'=>_l('project_start_date'),
   'project_deadline'=>_l('project_deadline'),
   'members'=>_l('project_members'),
   'status'=> _l('project_status'),
   'project_status'=> _l('status'),
   'pipeline_id'=>_l('pipeline'),
   'contact_email1'=>_l('company_primary_email'),
   'contact_phone1'=>_l('company_primary_phone'),
];
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
$table_data = array();
$req_datas = array();
 foreach($report_deal_list_column as $ckey=>$cval){
	 if(isset($table_data_temp[$ckey])){
			$table_data[] = $table_data_temp[$ckey];
			$req_datas[$ckey] = $table_data_temp[$ckey];
	 }
 }
$table_data = hooks()->apply_filters('projects_table_columns', $table_data);
?>
<html lang="en">
	<head>
		 <title><?php echo isset($title) ? $title : get_option('companyname'); ?></title>
		<!-- Datatable CSS -->
		<link href='https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>
		<!-- jQuery Library -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<!-- Datatable JS -->
		<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="col-md-6"></div>
						<div class="col-md-6">
							<h2>Report</h2>
							<table id='empTable' class='display dataTable' >
							  <thead>
								<tr>
								 <?php if(!empty($table_data)){
									foreach($table_data as $table1){
								?>
								  <th><?php echo $table1;?></th>
									<?php }
								 }
								?>
								</tr>
							  </thead>

							</table>
						</div>
				</div>
			</div>
		</div>
	</body>
	<script>
	$(document).ready(function(){
	   $('#empTable').DataTable({
		  'processing': true,
		  'serverSide': true,
		  'serverMethod': 'post',
		  'ajax': {
			  'url':'<?php echo base_url('shared/deal_edit_table/'.$id);?>'
		  },
		  'columns': [
			<?php if(!empty($req_datas)){
				foreach($req_datas as $key => $req_data12){
			?>
					{ data: '<?php echo $key;?>' },
				<?php }
			}?>
		  ]
	   });
	});
	</script>
	<style>
	.container{
		   position: absolute;
		padding: 10px;
		width: 96% !important;
		overflow-x: scroll;
	}
	</style>
</html>