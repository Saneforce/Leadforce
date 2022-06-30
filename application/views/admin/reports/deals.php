<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content"> 
		<div class="row">
		<?php if ($this->session->flashdata('debug')) {?>
			<div class="col-lg-12">
				<div class="alert alert-warning">
					<?php echo $this->session->flashdata('debug'); ?>
				</div>
			</div>
		<?php
		} 
		$record_val = end($this->uri->segment_array());
		?>
		<div class="panel_s project-menu-panel" style="margin-bottom:0px;">
			<div class="panel-body">
				<div class="horizontal-tabs">
					<div class="">
						 2 filters applied
					</div>
				</div>
			</div>
		</div>
		<div class="panel_s project-menu-panel" style="margin-top:-1px;">
			<div class="panel-body">
				<div class="horizontal-tabs">
					<div class="">
						<input type="hidden" id="cur_num" value="<?php echo count($filters);?>">
						<div class="row" id="ch_ids">
							<?php if(!empty($filters)){
								$i1 = 1;
								foreach($filters as $key => $filter1){?>
									<div  class="col-md-12 m-bt-10">
										<div  class="col-md-2" >
											<select data-live-search="true" class="selectpicker" id="filter_<?php echo $i1;?>" onchange="change_filter(this)">
												<?php $cur_val ='';
												if(!empty($all_clmns)){ ?>
													<optgroup label="Deal Master" data-max-options="2">
													<?php foreach ($all_clmns as $key1 => $all_val1){?>
														<option value="<?php echo $key1;?>" <?php if($key1==$filter1){ echo 'selected';}?>><?php echo _l($all_val1['ll']);?></option>
													<?php }?>
													</optgroup>
												<?php }
												if(!empty($cus_flds)){?>
													<optgroup label="Custom Fields"  data-max-options="2">
														<?php foreach ($cus_flds as  $key => $cus_fld1){?>
															<option value="<?php echo $key;?>" <?php if($key == $filter1){echo 'selected';}?>><?php echo $cus_fld1['ll'];?></option>
														<?php } ?>
													</optgroup>
												<?php }?>
												
											</select>
										</div>
										<div  class="col-md-6" >
											<div id="ch_dr_1">
												
											</div>
										</div>
										
									</div>
								<?php 
								
								$i1++;
								}
							}?>
						</div>
						<div class="row">
							<div class="col-md-12">
								<a href="javascript:void(0)" onclick="add_filter()"><i class="fa fa-plus-circle" style="font-size:xx-large"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel_s project-menu-panel">
			<div class="panel-body">
				<div class="col-md-12">
					<?php //echo $tab_view;?>
					
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<div class="btn-bottom-pusher"></div>
	</div>
</div>
<style>
.m-bt-10{
	margin-bottom:10px;
}
li.dropdown-header.optgroup-1 {
    display: block !important;
}
select.ui-datepicker-month,select.ui-datepicker-year {
    color: #000000;
}
ul.dropdown-menu li:first-child {
    display: block !important;
}
.autocomplete {
  position: relative;
  display: inline-block;
}
.autocomplete-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}

.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff; 
  border-bottom: 1px solid #d4d4d4; 
}

/*when hovering an item:*/
.autocomplete-items div:hover {
  background-color: #e9e9e9; 
}

/*when navigating through the items using the arrow keys:*/
.autocomplete-active {
  background-color: DodgerBlue !important; 
  color: #ffffff; 
}
</style>
<?php init_tail(); app_admin_ajax_search_function();?>
<script>
$(function(){
	$('#end_date_edit_1').datepicker({
		 dateFormat:'dd-mm-yy',
		 calendarWeeks: true,
		autoclose: true,
		changeMonth: true,
		changeYear: true,
		timepicker: false,
		onSelect: function(selectedDate) {
			$('#year_1').val('custom_period');
			$('#year_1').selectpicker('refresh');
		}
	});
	$('#start_date_edit_1').datepicker({
		 dateFormat:'dd-mm-yy',
		   timepicker: false,
		   calendarWeeks: true,
		  changeMonth: true,
		  changeYear: true,
		todayHighlight: true,
		  onSelect: function(selectedDate) {
			$('#end_date_edit_1').datepicker('option', 'minDate', selectedDate);
			$('#year_1').val('custom_period');
			$('#year_1').selectpicker('refresh');
		  }
	});
	appDatepicker();
});
function add_filter(){
	var cur_num = $('#cur_num').val();
	var data = {cur_num:cur_num};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/add_filter',
		data: data,
		dataType: '',
		success: function(msg) {
			var obj = JSON.parse(msg);
			 $("#ch_ids").append(obj.output);
			 $('#cur_num').val(obj.cur_num);
			 var cur_num = $('#cur_num').val();
			 $('#year_'+cur_num).selectpicker('refresh');
			 $('#filter_option_'+cur_num).selectpicker('refresh');
			 $('#filter_'+cur_num).selectpicker('refresh');
		}
	});
	
}
function check_filter(a){
	var cur_id = a.id;
	var req_val = cur_id.split("filter_option_");
	req_val = req_val[1];
	$('#2_'+req_val+"_filter").show();
	$('#3_'+req_val+"_filter").show();
	$('#4_'+req_val+"_filter").show();
	if(a.value=='is_empty' || a.value=='is_not_empty'){
		$('#2_'+req_val+"_filter").hide();
		$('#3_'+req_val+"_filter").hide();
		$('#4_'+req_val+"_filter").hide();
	}
	if(a.value=='is_any_of'){
		$('#year_'+req_val).attr('multiple', true);
		$('#year_'+req_val).selectpicker('refresh');
	}
}
function change_filter(a){
	var cur_id = a.id;
	var req_val = cur_id.split("filter_");
	req_val = req_val[1];
	var cur_val = a.value;
	
	var data = {cur_val:cur_val,req_val:req_val};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/get_filters',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#ch_dr_'+req_val).html(msg);
			$('#year_'+req_val).selectpicker('refresh');
			$('#filter_option_'+req_val).selectpicker('refresh');
			if(cur_val=='project_start_date' || cur_val == 'project_deadline'){
				$('#end_date_edit_'+req_val).datepicker({
					 dateFormat:'dd-mm-yy',
					 calendarWeeks: true,
					autoclose: true,
					changeMonth: true,
					changeYear: true,
					timepicker: false,
					onSelect: function(selectedDate) {
						$('#year_'+req_val).val('custom_period');
						$('#year_'+req_val).selectpicker('refresh');
					}
				});
				$('#start_date_edit_'+req_val).datepicker({
					 dateFormat:'dd-mm-yy',
					   timepicker: false,
					   calendarWeeks: true,
					  changeMonth: true,
					  changeYear: true,
					todayHighlight: true,
					  onSelect: function(selectedDate) {
						$('#end_date_edit_'+req_val).datepicker('option', 'minDate', selectedDate);
						$('#year_'+req_val).val('custom_period');
						$('#year_'+req_val).selectpicker('refresh');
					  }
				});
				appDatepicker();
				 
			}
			if(cur_val=='name'){
				init_ajax_search('project', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='company'){
				init_ajax_search('customer', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='contact_name'){
				init_ajax_search('contacts', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='teamleader_name'){
				init_ajax_search('manager', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='members'){
				init_ajax_search('staff', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='contact_email1'){
				init_ajax_search('staff_email', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='contact_phone1'){
				init_ajax_search('staff_phone', '#year_'+req_val+'.ajax-search');
			}
		}
	});
}
function change_filter1(a,b){
	var cur_id = a;
	var req_val = cur_id.split("filter_");
	req_val = req_val[1];
	var cur_val = b;
	var data = {cur_val:cur_val,req_val:req_val};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/get_filters',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#ch_dr_'+req_val).html(msg);
			$('#year_'+req_val).selectpicker('refresh');
			$('#filter_option_'+req_val).selectpicker('refresh');
			if(cur_val=='project_start_date' || cur_val == 'project_deadline'){
				$('#end_date_edit_'+req_val).datepicker({
					 dateFormat:'dd-mm-yy',
					 calendarWeeks: true,
					autoclose: true,
					changeMonth: true,
					changeYear: true,
					timepicker: false,
					onSelect: function(selectedDate) {
						$('#year_'+req_val).val('custom_period');
						$('#year_'+req_val).selectpicker('refresh');
					}
				});
				$('#start_date_edit_'+req_val).datepicker({
					 dateFormat:'dd-mm-yy',
					   timepicker: false,
					   calendarWeeks: true,
					  changeMonth: true,
					  changeYear: true,
					todayHighlight: true,
					  onSelect: function(selectedDate) {
						$('#end_date_edit_'+req_val).datepicker('option', 'minDate', selectedDate);
						$('#year_'+req_val).val('custom_period');
						$('#year_'+req_val).selectpicker('refresh');
					  }
				});
				appDatepicker();
				 
			}
			if(cur_val=='name'){
				init_ajax_search('project', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='company'){
				init_ajax_search('customer', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='contact_name'){
				init_ajax_search('contacts', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='teamleader_name'){
				init_ajax_search('manager', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='members'){
				init_ajax_search('staff', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='contact_email1'){
				init_ajax_search('staff_email', '#year_'+req_val+'.ajax-search');
			}
			if(cur_val=='contact_phone1'){
				init_ajax_search('staff_phone', '#year_'+req_val+'.ajax-search');
			}
		}
	});
}
<?php if(!empty($filters)){
	$i1 = 1;
	foreach($filters as $key => $filter1){?>
	change_filter1('filter_<?php echo $i1;?>','<?php echo $filter1;?>');
	<?php $i1++;}
}
?>
</script>