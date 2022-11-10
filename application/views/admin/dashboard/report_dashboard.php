<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();$staffid = get_staff_user_id(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
		
            <?php hooks()->do_action('after_dashboard_half_container'); ?>
			<?php  if(!empty($types)){?>
				<div class="col-md-12 " style="margin-bottom:10px;" >
					<?php echo form_open(admin_url('dashboard/view/'.$id),array());?>
						<div class="col-md-6">
							<div class="col-md-3 <?php if(empty($dashoard_data[0]['period'])){ echo 'w_100';}?>" id="period">
								<select data-live-search="false" data-width="100%" class="ajax-search selectpicker" data-none-selected-text="Nothing selected" tabindex="-98" id="year" onchange="change_2_filter(this)" name="filter_1">
									<option value=""><?php echo _l('select_period');?></option>
									<option value="this_year" <?php if(!empty($dashoard_data[0]['period']) && $dashoard_data[0]['period'] == 'this_year' ){ echo 'selected';}?>><?php echo _l('this_year');?></option>
									<option value="last_year" <?php if(!empty($dashoard_data[0]['period']) && $dashoard_data[0]['period'] == 'last_year' ){ echo 'selected';}?>><?php echo _l('last_year');?></option>
									<option value="next_year" <?php if(!empty($dashoard_data[0]['period']) && $dashoard_data[0]['period'] == 'next_year' ){ echo 'selected';}?>><?php echo _l('next_year');?></option>
									<option value="this_month" <?php if(!empty($dashoard_data[0]['period']) && $dashoard_data[0]['period'] == 'this_month' ){ echo 'selected';}?>> <?php echo _l('this_month');?></option>
									<option value="last_month" <?php if(!empty($dashoard_data[0]['period']) && $dashoard_data[0]['period'] == 'last_month' ){ echo 'selected';}?>><?php echo _l('last_month');?></option>
									<option value="next_month" <?php if(!empty($dashoard_data[0]['period']) && $dashoard_data[0]['period'] == 'next_month' ){ echo 'selected';}?>><?php echo _l('next_month');?></option>
									<option value="this_week" <?php if(!empty($dashoard_data[0]['period']) && $dashoard_data[0]['period'] == 'this_week' ){ echo 'selected';}?>><?php echo _l('this_week');?></option>
									<option value="last_week" <?php if(!empty($dashoard_data[0]['period']) && $dashoard_data[0]['period'] == 'last_week' ){ echo 'selected';}?>><?php echo _l('last_week');?></option>
									<option value="next_week" <?php if(!empty($dashoard_data[0]['period']) && $dashoard_data[0]['period'] == 'next_week' ){ echo 'selected';}?>><?php echo _l('next_week');?></option>
									<option value="today" <?php if(!empty($dashoard_data[0]['period']) && $dashoard_data[0]['period'] == 'today' ){ echo 'selected';}?>><?php echo _l('today');?></option>
									<option value="yesterday" <?php if(!empty($dashoard_data[0]['period']) && $dashoard_data[0]['period'] == 'yesterday' ){ echo 'selected';}?> ><?php echo _l('yesterday');?></option>
									<option value="tomorrow" <?php if(!empty($dashoard_data[0]['period']) && $dashoard_data[0]['period'] == 'tomorrow' ){ echo 'selected';}?>  ><?php echo _l('tomorrow');?></option>
									<option value="custom_period" <?php if(!empty($dashoard_data[0]['period']) && $dashoard_data[0]['period'] == 'custom_period' ){ echo 'selected';}?>><?php echo _l('custom_period');?></option>
								</select>
							</div>
							<div class="col-md-9" id="period_date" <?php if(empty($dashoard_data[0]['period'])){?>style="display:none" <?php }?>>
								<div class="col-md-6" >
									<input type="text" class="form-control filter_date" name="filter_2" id="start_date_edit" readonly value="<?php if(!empty($dashoard_data[0]['date1']) ){ echo date('d-m-Y',strtotime($dashoard_data[0]['date1']));}?>">
								</div>
								<div class="col-md-6" >
									<input type="text" class="form-control filter_date"  name="filter_3" id="end_date_edit" readonly value="<?php if(!empty($dashoard_data[0]['date2']) ){ echo date('d-m-Y',strtotime($dashoard_data[0]['date2']));}?>">
									
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="col-md-8"  >
								<select data-live-search="false" data-width="100%" class="ajax-search selectpicker" data-none-selected-text="Nothing selected" tabindex="-98"  name="filter_4">
									<option value=""><?php echo _l('all_member');?></option>
									<?php if(!empty($project_members)){
										foreach($project_members as $project_member1){?>
											<option value="<?php echo $project_member1['staff_id'];?>" <?php if(!empty($dashoard_data[0]['member']) && $dashoard_data[0]['member'] == $project_member1['staff_id'] ){ echo 'selected';}?>><?php echo $project_member1['firstname'].' '.$project_member1['lastname'];?></option>
									<?php }
									}
									?>
								</select>
							</div>
							<div class="col-md-4">
								<button type="submit" name="apply_filter" class="btn btn-primary pull-right1 btn_bg"  ><?php echo _l('apply_filter');?></button>
							</div>
						</div>
						<div class="col-md-2">
							<?php if(!empty($types)){?>
								<button type="button" class="btn btn-primary pull-right1" style="background-color:#61c786 !important;float:right" data-toggle="modal" data-target="#public_add_modal" onclick="load_public('<?php echo $staffid;?>',<?php echo $id;?>)"><?php echo _l('public_link');?></button>
								<input type="hidden" id="cur_report" value="<?php echo $staffid;?>">
							<?php }?>
						</div>
					<?php echo form_close();?>
				</div>
			<?php }?>
			<div class="col-md-12 " >
					<h1><?php echo $title;?></h1>
                <?php render_dashboard_widgets('report-4'); ?>
            </div>
            <?php hooks()->do_action('after_dashboard'); ?>
        </div>
    </div>
</div>
<div class="modal fade" id="clientid_add_modal_public" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static" style="z-index:99999">
	<div class="modal-dialog" role="document">
	<div id="overlay_deal12" class="overlay_new" style="display: none;"><div class="spinner"></div></div>
		<div class="modal-content">
			<div class="modal-header">
				<button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">
					<span class="edit-title"><?php echo _l('public_link'); ?></span>
				</h4>
			</div>
			<?php echo form_open(admin_url('dashboard/update_public_name'),array('id'=>'update_public_name')); ?>
			<div class="modal-body">
				<input type="hidden" id="link_id" name="link_id">
				<input type="hidden" id="dashboard_id1" name="dashboard_id" value="<?php echo $id;?>">
					<?php $attrs = array('autofocus'=>true, 'required'=>true); ?>
					<?php echo render_input( 'ch_name12', 'name','','text',$attrs); ?>
					<div id="companyname_exists_info" class="hide"></div>
					
			</div>
			<div class="modal-footer">
				<button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
<div class="modal fade" id="public_add_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog" role="document">
		<div id="overlay_deal_public" class="overlay_new" style="display: none;"><div class="spinner"></div></div>
		<div class="modal-content">
			<div class="modal-header">
				<button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">
					<span class="edit-title"><?php echo _l('public_link'); ?></span>
				</h4>
			</div>
			<div class="modal-body">
				<div>
					<?php echo _l('public_content');?>
				</div>
				<div id="public_all">
					<?php if(!empty($links)){
						foreach($links as $link12){
						?>
							<div class="form-group" app-field-wrapper="name" ><label for="name" class="control-label"> <?php echo _l('share_link');?></label><input type="text"  class="form-control" value="<?php echo base_url('shared/index/'.$link12['share_link']);?>"  readonly style="width:90%;float:left;"><a href="javascript:void(0);" " style="margin-left:10px;float:left" onclick="delete_link('<?php echo $link12['id'];?>')"><i class="fa fa-trash fa-2x" style="color:red"></i></a></div>
					<?php 
						}
					}?>
				
				</div>
				<div class="row"> <div class="col-md-12"><a href="javascript:void(0)" onclick="add_public_link('<?php echo $staffid;?>','<?php echo $id;?>')"><?php echo _l('add_link');?></a></div></div>
			</div>
			<div class="modal-footer">
				<button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<?php $this->load->view('admin/dashboard/dashboard_js'); ?>
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
					if($sum1['view_by'] == 'priority'){
						if($sum1['summary_cls'][$i]['priority'] == '1'){
							$req_label .= '"'._l('task_priority_low').'",';
						}
						else if($sum1['summary_cls'][$i]['priority'] == '2'){
							$req_label .= '"'._l('task_priority_medium').'",';
						}
						else if($sum1['summary_cls'][$i]['priority'] == '3'){
							$req_label .= '"'._l('task_priority_high').'",';
						}
						else if($sum1['summary_cls'][$i]['priority'] == '4'){
							$req_label .= '"'._l('task_priority_urgent').'",';
						}
						else{
							$req_label .= '"'.$sum_row.'",';
						}
					}
					else if($sum1['view_by'] == 'project_status'){
						if($sum_row == '0'){
							$req_label .= '"'.  _l('proposal_status_open').'",';
						}
						else if($sum_row == '1'){
							$req_label .= '"'. _l('project-status-won').'",';
						}
						else if($sum_row == '2'){
							$req_label .= '"'. _l('project-status-loss').'",';
						}
						else{
							$req_label .= '"'.$sum_row.'",';
						}
					}
					else if($report_page == 'activity' && $sum1['view_by'] == 'status'){
						if($sum_row == '1'){
							$req_label .= '"'. _l('task_status_1').'",';
						}
						else if($sum_row == '2'){
							$req_label .= '"'.  _l('task_status_2').'",';
						}
						else if($sum_row == '3'){
							$req_label .= '"'.  _l('task_status_3').'",';
						}
						else if($sum_row == '4'){
							$req_label .= '"'.  _l('task_status_4').'",';
						}
						else if($sum_row == '5'){
							$req_label .= '"'.  _l('task_status_5').'",';
						}
					}
					else{
						$req_label .= '"'.$sum_row.'",';
					}
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
					if($report_page != 'activity' && $sum1['view_by'] == 'status'){
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
				var b = $('#dashboard_id1').val();
				load_public(a,b);
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
</body>
</html>
