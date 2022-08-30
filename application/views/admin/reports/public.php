<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_datas =  deal_get_fields();
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
		<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/tabletools/2.2.4/css/dataTables.tableTools.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.4.0/css/select.dataTables.min.css">

		<!-- jQuery Library -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<!-- Datatable JS -->
		<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
		<script src="//cdn.datatables.net/tabletools/2.2.4/js/dataTables.tableTools.min.js"></script>
		<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.4.1/css/buttons.dataTables.min.css">
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
 <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
 <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.4.1/js/buttons.print.min.js"></script>
	</head>
	<body>
		<div class="container" <?php if(empty($id)){?>style="overflow-x:hidden" <?php }?>>
			<div class="row">
				<?php if(!empty($id)){?>
					<div class="col-md-12">
						<div class="col-md-6"></div>
							<div class="col-md-6">
								<h2>Report</h2>
								<div class="info" id="buttons"></div>
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
				<?php }else{?>
					<div class="col-md-6 mx-auto mt-5">
						 <div class="payment">
							<div class="payment_header">
							   <div class="check"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></div>
							</div>
							<div class="content">
							   <h1>Opps ! </h1>
							   <p>The Report link has been deleted. </p>
							</div>
							
						 </div>
					  </div>
				<?php }?>
			</div>
		</div>
	</body>
	<script>
	$(document).ready(function(){
	    var table = $('#empTable').DataTable({
			lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'All'],
        ],
		oLanguage: {
       sLengthMenu: "_MENU_",
    },
		dom: 'lBfrtip',
   buttons: [
   {
       'extend': 'excelHtml5',
   },
   {
       'extend': 'csvHtml5',
   },
   {
       'extend': 'print',
   },
   {
       'extend': 'pdfHtml5',
	   orientation : 'landscape',
                pageSize : 'A2',
                text : '<i class="fa fa-file-pdf-o"> PDF</i>',
                titleAttr : 'PDF',
   }
    ],
	
        select: false,
        colReorder: false,
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
	select{
		height:40px;
		margin-right:15px;
	}
	.container{
		margin: 25px;
		overflow-x: scroll;
	}
	th,td {
		white-space: nowrap;
	}
	

    .payment
	{
		border:1px solid #f01b1b;
		height:280px;
        border-radius:20px;
        background:#fff;
	}
   .payment_header
   {
	   background:#f01b1b;
	   padding:20px;
       border-radius:20px 20px 0px 0px;
	   
   }
   
   .check
   {
	   margin:0px auto;
	   width:50px;
	   height:50px;
	   border-radius:100%;
	   background:#fff;
	   text-align:center;
   }
   
   .check i
   {
	   vertical-align:middle;
	   line-height:50px;
	   font-size:30px;
   }

    .content 
    {
        text-align:center;
    }

    .content  h1
    {
        font-size:25px;
        padding-top:25px;
    }

    .content a
    {
        width:200px;
        height:35px;
        color:#fff;
        border-radius:30px;
        padding:5px 10px;
        background:#f01b1b;
        transition:all ease-in-out 0.3s;
    }

    .content a:hover
    {
        text-decoration:none;
        background:#000;
    }
   
</style>
</html>