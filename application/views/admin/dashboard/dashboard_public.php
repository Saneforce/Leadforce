<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<html lang="en">
	<head>
		<title><?php echo isset($title) ? $title : get_option('companyname'); ?></title>
		<link rel="stylesheet" type="text/css" id="vendor-css" href="<?php echo base_url();?>/assets/builds/vendor-admin.css?v=2.4.0">
		<link rel="stylesheet" type="text/css" id="app-css" href="<?php echo base_url();?>/assets/css/style.min.css?v=2.4.0">
		<script type="text/javascript" id="vendor-js" src="<?php echo base_url();?>/assets/builds/vendor-admin.js?v=2.4.0"></script>

		<script type="text/javascript" id="bootstrap-select-js" src="<?php echo base_url();?>/assets/builds/bootstrap-select.min.js?v=2.4.0"></script>
		<?php $this->load->view('admin/dashboard/dashboard_js'); ?>
	</head>
	<body>
		<div id="wrapper1">
			<div class="content">
				<h1><?php echo _l('dashboard');?></h1>
				<div class="row">
					<div class="col-md-12 " >
						<?php render_dashboard_widgets('report-4'); ?>
					</div>
				</div>
			</div>
		</div>

		
		<?php
		$i1 = 0;
		if(!empty($summary)){
			foreach($summary as $sum1){
				$req_label = $req_data = $req_color = '';
				$i = 0;
				
				if(!empty($sum1['rows'])){ 
					foreach($sum1['rows'] as $sum_row){
						$report_page = $types[$i1];
						
						if($sum_row!='Average' && $sum_row!='Total'){
							$req_label .= '"'.$sum_row.'",';
							if($report_page == 'deal'){
								if(!empty($sum1['summary_cls'][$i]['total_cnt_deal']))
									$req_data .= '"'.$sum1['summary_cls'][$i]['total_cnt_deal'].'",';
								else
									$req_data .= '"0",';
							}
							else{
								if(!empty($sum1['summary_cls'][$i]['total_val_task']))						
									$req_data .= '"'.$sum1['summary_cls'][$i]['total_val_task'].'",';
								else
									$req_data .= '"0",';
							}
							if($sum1['view_by'] == 'status'){
								$this->db->select('color');
								$this->db->where('name', $sum_row);
								$progress =  $this->db->get(db_prefix() . 'projects_status')->row();
								$req_color .= '"'.$progress->color.'",';
							}
							else{
								$req_color .= '"'.random_color().'",';
							}
						}
						$i++;
					}
					$req_label	= rtrim($req_label,',');
					$req_data	= rtrim($req_data,',');
					$req_color	= rtrim($req_color,',');
					?>
						<script>
						$(function() {
							var pie_chart = $('#report_pie_chart_<?php echo $i1;?>');
							if(pie_chart.length > 0){
								 new Chart(pie_chart, {
									type: 'pie',
									data: {"labels":[<?php echo $req_label;?>],"datasets":[{"data":[<?php echo $req_data;?>],"backgroundColor":[<?php echo $req_color;?>],"label":"<?php echo _l('summary');?>"}]},
									options: {
										responsive:true,
										maintainAspectRatio:false,
								   }
							   });
							}
							var bar_chart = $('#report_bar_chart_<?php echo $i1;?>');
							if(bar_chart.length > 0){
								new Chart(bar_chart, {
									type: 'bar',
									data: {"labels":[<?php echo $req_label;?>],"datasets":[{"data":[<?php echo $req_data;?>],"backgroundColor":[<?php echo $req_color;?>],"label":"<?php echo _l('summary');?>"}]},
									options:{
										responsive:true,
										maintainAspectRatio:false,
										scales: {
											xAxes: [{
											  scaleLabel: {
												display: true,
												labelString: '<?php echo _l($summary['view_by']);?>'
											  }
											}],
											yAxes: [{
											  scaleLabel: {
												display: true,
												labelString: '<?php echo $summary['sel_measure'];?>'
											  }
											}],
										}
									}
								});
							}
							var horizontalBar = $('#report_horizontal_chart_<?php echo $i1;?>');
							if(horizontalBar.length > 0){
								new Chart(horizontalBar, {
									type: 'horizontalBar',
									data: {"labels":[<?php echo $req_label;?>],"datasets":[{"data":[<?php echo $req_data;?>],"backgroundColor":[<?php echo $req_color;?>],"label":"<?php echo _l('summary');?>"}]},
									options:{
										responsive:true,
										maintainAspectRatio:false,
										scales: {
											yAxes: [{
											  scaleLabel: {
												display: true,
												labelString: '<?php echo _l($summary['view_by']);?>'
											  }
											}],
											xAxes: [{
											  scaleLabel: {
												display: true,
												labelString: '<?php echo $summary['sel_measure'];?>'
											  }
											}]
										}
									}
								});
							}
						});
						</script>
					<?php 
				}
				$i1++;
			}
		}
		?>
		<script>
		$( function() {
			$("#update_public_name").submit(function(e) {
				e.preventDefault(); // avoid to execute the actual submit of the form.
				var form = $(this);
				var actionUrl = form.attr('action');
				document.getElementById('overlay_deal12').style.display = '';
				$.ajax({
					type: form.attr('method'),
					url: actionUrl,
					data: form.serialize(), // serializes the form's elements.
					success: function(data)
					{
						alert_float('success', 'Link Name Updated Successfully');
						document.getElementById('overlay_deal12').style.display = 'none';
						$('#clientid_add_modal_public').modal('toggle');
						var a = $('#cur_report').val();
						load_public(a);
					}
				});
				
			});
			$('#end_date_edit').datepicker({
				 dateFormat:'dd-mm-yy',
				 calendarWeeks: true,
				autoclose: true,
				changeMonth: true,
				changeYear: true,
				timepicker: false,
				onSelect: function(selectedDate) {
					$('#year').val('custom_period');
					$('#year').selectpicker('refresh');
				}
			});
			$('#start_date_edit').datepicker({
				 dateFormat:'dd-mm-yy',
				   timepicker: false,
				   calendarWeeks: true,
				  changeMonth: true,
				  changeYear: true,
				todayHighlight: true,
				  onSelect: function(selectedDate) {
					$('#end_date_edit').datepicker('option', 'minDate', selectedDate);
					$('#year').val('custom_period');
					$('#year').selectpicker('refresh');
				  }
			});
			appDatepicker();
		});

		</script>
		<style>
		ul.dropdown-menu li:first-child {
			 display: block !important;
		}
		body{
			background-Color:#fff;
		}
		</style>
	</body>
</html>
