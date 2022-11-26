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
				<h1 class="text_center"><?php echo $title;?></h1>
				<h4 class="text_center"><?php echo _l('live_updates');?></h4>
				<div class="row">
					<div class="col-md-12 " >
						<?php render_dashboard_widgets('report-4'); ?>
					</div>
				</div>
			</div>
		</div>
<?php 
$req_data['summary'] = $summary;
$this->load->view('admin/dashboard/report_dashboard_js',$req_data); ?>
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
		.text_center{
			text-align:center;
		}
		.dropdown1 {
			
		}
		hr.hr-panel-heading-dashboard {
			margin-top: 20px !important;
		}
		</style>
	</body>
</html>
