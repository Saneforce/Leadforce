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
		<div class="modal fade" id="clientid_add_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">
							<span class="edit-title"><?php echo _l('add_report'); ?></span>
						</h4>
					</div>
					<?php echo form_open('admin/reports/save_report',array('id'=>'clientid_add_group_modal')); ?>
					<div class="modal-body">
							<input type="hidden" id="cur_id12" value="<?php echo $id;?>">
							<?php $attrs = array('autofocus'=>true, 'required'=>true); ?>
							<?php echo render_input( 'name', 'name','','text',$attrs); ?>
							<div id="companyname_exists_info" class="hide"></div>
							<div class="form-group select-placeholder contactsdiv" >
								<label for="project_contacts_selectpicker"
                                class="control-label"><small class="req text-danger">* </small><?php echo _l('folder'); ?></label>
								 <div class="input-group input-group-select ">
								 <?php 
								 $selected = '';
								 echo render_select('folder',$folders,array('id',array('folder')),false,$selected,array('aria-describedby'=>'project_contacts-error','style'=>'height:21px;'),array(),'cur_class','',false);?>
								 <div class="input-group-addon" style="opacity: 1;">
									<a href="#" data-toggle="modal" data-target="#section_modal" ><i class="fa fa-plus"></i></a>
									</div>
								</div>
							</div>
					</div>
					<div class="modal-footer">
						<button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
						<button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
						
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
		<div class="modal fade" id="section_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button group="button" class="close" data-dismiss="modal" aria-label="Close"><span
								aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">
							<span class="edit-title"><?php echo _l('add_new',_l('folder')); ?></span>
						</h4>
					</div>
					<?php echo form_open('admin/reports/add_folder',array('id'=>'section_add')); ?>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<?php $attrs = array('autofocus'=>true, 'required'=>true); ?>
								<?php echo render_input( 'name', 'name','','',$attrs); ?>
								<div id="contact_exists_info" class="hide"></div>								
								<div class="input_fields_wrap_ae">
								
								</div>
								
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
						<button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>

					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
		<div class="panel_s project-menu-panel" style="margin-bottom:0px;">
			<div class="panel-body">
				<div class="horizontal-tabs">
					<div class="" style="float:left">
						 <?php echo count($filters);?> filters applied
					</div>
					<div class="float-right" style="float:right">
						<button type="button" class="btn btn-primary pull-right1" style="background-color:#61c786 !important;" data-toggle="modal" data-target="#public_add_modal">Public Link</button>
						<a href="<?php echo base_url('shared/index/'.$id);?>" class="btn btn-primary pull-right1" style="background-color:#61c786 !important;" >Save</a>
						<button type="button" class="btn btn-primary pull-right1" style="background-color:#61c786 !important;" data-toggle="modal" data-target="#clientid_add_modal">Save New</button>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="public_add_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
			<div class="modal-dialog" role="document">
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
									<div class="form-group" app-field-wrapper="name" ><label for="name" class="control-label"> Share Link</label><input type="text"  class="form-control" value="<?php echo admin_url('reports/shared/'.$link12['share_link']);?>"  readonly style="width:90%;float:left;"><a href="javascript:void(0);" " style="margin-left:10px;float:left" onclick="delete_link('<?php echo $link12['id'];?>')"><i class="fa fa-trash fa-2x" style="color:red"></i></a></div>
							<?php 
								}
							}?>
						
						</div>
						<div class="row"> <div class="col-md-12"><a href="javascript:void(0)" onclick="add_public_link('<?php echo $id;?>')">Add Link</a></div></div>
					</div>
					<div class="modal-footer">
						<button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
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
								$i1 = 1;$i2 =0;
								foreach($filters as $key => $filter1){
									
								?>
									<div  class="col-md-12 m-bt-10">
										<div  class="col-md-2" >
											<select data-live-search="true" class="selectpicker" id="filter_<?php echo $i1;?>" onchange="change_filter(this)">
												<?php $cur_val ='';
												if(!empty($all_clmns)){ ?>
													<optgroup label="Deal Master" data-max-options="2">
													<?php foreach ($all_clmns as $key1 => $all_val1){
														if($key1==$filter1 || !in_array($key1, $filters)){ 
														?>
														<option value="<?php echo $key1;?>" <?php if($key1==$filter1){ echo 'selected';}?>><?php echo _l($all_val1['ll']);?></option>
													<?php 
														}
													}?>
													</optgroup>
												<?php }
												if(!empty($cus_flds)){?>
													<optgroup label="Custom Fields"  data-max-options="2">
														<?php foreach ($cus_flds as  $key => $cus_fld1){
															if($key==$filter1 || !in_array($key, $filters)){ 
															?>
															<option value="<?php echo $key;?>" <?php if($key == $filter1){echo 'selected';}?>><?php echo $cus_fld1['ll'];?></option>
														<?php }
														}
														?>
													</optgroup>
												<?php }?>
												
											</select>
										</div>
										<div  class="col-md-6" >
											<div id="ch_dr_<?php echo $i1;?>">
												
											</div>
										</div>
										
									</div>
								<?php 
								$i2++;
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
					<?php $this->load->view('admin/reports/deal_list_column'); ?>
					<?php $this->load->view('admin/reports/deal_table_html'); ?>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<div class="btn-bottom-pusher"></div>
	</div>
	<input type="hidden" id="check_search_id">
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

.cur_class {
    height: 21px;
}
</style>
<?php init_tail(); app_admin_ajax_search_function();?>
<script>
function add_public_link(a){
	var data = {req_val:a};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/public_link',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#public_all').html(msg);
		}
	});
}
function delete_link(a){
	var cur_id12 = $('#cur_id12').val();
	var data = {req_val:a,cur_id12:cur_id12};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/delete_link',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#public_all').html(msg);
		}
	});
}
$(function(){
	var frm = $('#section_add');
    frm.submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: frm.attr('method'),
            url: frm.attr('action'),
            data: frm.serialize(),
            success: function (data) {
				alert_float('success', 'Folder Added Successfully');
				var emp = jQuery.parseJSON(data); 
				$("#folder").empty().append(emp.success);
			   $('#folder').selectpicker('refresh');
               $('#section_modal').modal('toggle');
            },
            error: function (data) {
                console.log('An error occurred.');
                console.log(data);
            },
        });
    });
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
	var cur_id12 = $('#cur_id12').val();
	var data = {cur_num:cur_num,cur_id12:cur_id12};	
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/add_filter',
		data: data,
		dataType: '',
		success: function(msg) {
			var obj = JSON.parse(msg);
			 $("#ch_ids").html(obj.output);
			 $('#cur_num').val(obj.cur_num);
			 var cur_num = $('#cur_num').val();
			 for(var i=1; i<=cur_num;i++){
				 $('#year_'+i).selectpicker('refresh');
				 $('#filter_option_'+i).selectpicker('refresh');
				 $('#filter_'+i).selectpicker('refresh');
				 $('#end_date_edit_'+i).datepicker({
					 dateFormat:'dd-mm-yy',
					 calendarWeeks: true,
					autoclose: true,
					changeMonth: true,
					changeYear: true,
					timepicker: false,
					onSelect: function(selectedDate) {
						$('#year_'+i).val('custom_period');
						$('#year_'+i).selectpicker('refresh');
					}
				});
				$('#start_date_edit_'+i).datepicker({
					 dateFormat:'dd-mm-yy',
					   timepicker: false,
					   calendarWeeks: true,
					  changeMonth: true,
					  changeYear: true,
					todayHighlight: true,
					  onSelect: function(selectedDate) {
						$('#end_date_edit_'+i).datepicker('option', 'minDate', selectedDate);
						$('#year_'+i).val('custom_period');
						$('#year_'+i).selectpicker('refresh');
					  }
				});
				
				appDatepicker();
				var a1 = 'filter_'+i;
				var b1 = $('#filter_'+i).val();
				change_filter1(a1,b1);
			 }
		}
	});
	
}
function del_filter(a){
	var cur_id12 = $('#cur_id12').val();
	var data = {req_val:a,cur_id12:cur_id12};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/del_filter',
		data: data,
		dataType: '',
		success: function(msg) {
			alert_float('success', 'Report Deleted Successfully');
			location.reload();
			/*var cur_num = $('#cur_num').val();
			
			for(var i=1;i<=cur_num;i++){
				var a1 = 'filter_'+i;
				var b1 = $('#filter_'+i).val();
				change_filter1(a1,b1)
			}
			var cur_num = $('#cur_num').val()-1;
			$('#cur_num').val(cur_num);*/
				
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
	$("#start_date_edit_"+req_val).show();
	$("#end_date_edit_"+req_val).show();
	if(a.value=='is_empty' || a.value=='is_not_empty'){
		$('#2_'+req_val+"_filter").hide();
		$('#3_'+req_val+"_filter").hide();
		$('#4_'+req_val+"_filter").hide();
		$("#start_date_edit_"+req_val).hide();
		$("#end_date_edit_"+req_val).hide();
	}
	var cur_id12 = $('#cur_id12').val();
	var data = {cur_val:a.value,req_val:req_val,cur_id12:cur_id12};
	var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/set_first_filters/'+a.value+'/'+req_val,
		data: data,
		dataType: '',
		success: function(msg) {
			var cur_num = $('#cur_num').val();
			for(var i=1;i<=cur_num;i++){
				var a1 = 'filter_'+i;
				var b1 = $('#filter_'+i).val();
				change_filter1(a1,b1)
			}
		}
	});
}
function change_filter(a){
	var cur_id = a.id;
	var req_val = cur_id.split("filter_");
	req_val = req_val[1];
	var cur_val = a.value;
	var cur_id12 = $('#cur_id12').val();
	var data = {cur_val:cur_val,req_val:req_val,cur_id12:cur_id12};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/set_filters',
		data: data,
		dataType: '',
		success: function(msg) {
			var cur_num = $('#cur_num').val();
			for(var i=1;i<=cur_num;i++){
				var a1 = 'filter_'+i;
				var b1 = $('#filter_'+i).val();
				change_filter1(a1,b1)
			}
		}
	});
}
function change_filter1(a,b){
	var cur_id = a;
	var req_val = cur_id.split("filter_");
	req_val = req_val[1];
	var cur_val = b;
	var cur_id12 = $('#cur_id12').val();
	var data = {cur_val:cur_val,req_val:req_val,cur_id12:cur_id12};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/get_filters',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#ch_dr_'+req_val).html(msg);
			
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
			$('#year_'+req_val).selectpicker('refresh');
			$('#filter_option_'+req_val).selectpicker('refresh');
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
			
			return true;
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
 
function change_2_filter(a){
	var cur_id = a.id;
	var req_val = cur_id.split("year_");
	req_val = req_val[1];
	var cur_val = $('#'+cur_id).val();
	var check_search = $('#check_search_id').val();
	var cur_id12 = $('#cur_id12').val();
	if(check_search==''){
		//$('.dropdown-menu open').hide();
		var data = {cur_val:cur_val,req_val:req_val,cur_id12:cur_id12};
		var ajaxRequest = $.ajax({
			type: 'POST',
			url: admin_url + 'reports/set_second_filters',
			data: data,
			dataType: '',
			success: function(msg) {
				var cur_num = $('#cur_num').val();
				for(var i=1;i<=cur_num;i++){
					var a1 = 'filter_'+i;
					var b1 = $('#filter_'+i).val();
					change_filter1(a1,b1);
				}
			}
		});
	}
}
function check_all_val(){
	$('#check_search_id').val('12');
}
function check_all_val1(){
	$('#check_search_id').val('');
	$('.dropdown-menu open').hide();
}
$(function(){
	
     var ProjectsServerParams = {};

     $.each($('._hidden_inputs._filters input'),function(){
         ProjectsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
     });

     initDataTable('.table-projects', admin_url+'reports/deal_edit_table/<?php echo $id;?>', undefined, [0], ProjectsServerParams, <?php echo hooks()->apply_filters('projects_table_default_order', json_encode(array())); ?>);

     init_ajax_search('customer', '#clientid_copy_project.ajax-search');
});
</script>