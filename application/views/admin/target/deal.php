<div class="panel_s">
	<div class="panel-body">
		<h4 class="no-margin"><?php echo $title; ?></h4>
		<button type="button" class="btn btn-default" data-toggle="modal" data-target="#projects_list_column_orderModal" style="float: right;margin-top: -22px;">
		  <i class="fa fa-list" aria-hidden="true"></i>
		</button>
		<button class="btn btn-default btn-default-dt-options" data-toggle="modal" data-target="#compose-modal" onclick="target_pop()" style="float: right;margin-top: -22px;margin-right:8px;">Add</button>
		<hr class="hr-panel-heading" />
		
		
		<div class="col-md-12 row">
			<!-- Modal -->
			<div class="modal fade" id="projects_list_column_orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			  <div class="modal-dialog" role="document">
				<div class="modal-content">
					<?php echo form_open_multipart(admin_url('settings/target_list_column'),array('id'=>'projects_list_column')); ?>
				  <div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel"><?php echo _l('target_list_column'); ?></h5>
				  </div>
				  <div class="modal-body">
					<div class="form-group">

			<?php $colarr = array(
			"assign"=>array("ins"=>"assign","ll"=>"assign"),
			"pipeline"=>array("ins"=>"pipeline","ll"=>"pipeline"),
			"pipeline_stage"=>array("ins"=>"pipeline_stage","ll"=>"pipeline_stage"),
			"tracking_metric"=>array("ins"=>"tracking_metric","ll"=>"tracking_metric"),
			"target_type"=>array("ins"=>"target_type","ll"=>"target_type"),
			"interval"=>array("ins"=>"interval","ll"=>"interval"),
			"start_date"=>array("ins"=>"start_date","ll"=>"start_date"),
			"end_date"=>array("ins"=>"end_date","ll"=>"end_date"),
			"count_value"=>array("ins"=>"count_value","ll"=>"count_value"),
			"user"=>array("ins"=>"user","ll"=>"user"),
			"manager"=>array("ins"=>"manager","ll"=>"manager"),
			); 
			$cf = get_custom_fields('target');
			//pr($cf);exit;
			foreach($cf as $custom_field) {
				
				$cur_arr = array('ins'=>$custom_field['slug'],'ll'=>$custom_field['name']);
			$colarr[$custom_field['slug']] = $cur_arr;
			  //array_push($colarr,$cur_arr);
			}
			$req_columns = array();

			?>  
			  <ul id="sortable">
			  <?php $targets_list_column_order = (array)json_decode(get_option('target_list_column_order')); $i = 0;
			  $req_table = array();
			  //pr($projects_list_column_order); ?>
			  <?php foreach($targets_list_column_order as $ckey=>$cval){ ?>
				  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
				  <input type="checkbox" name="settings[target_list_column][<?php echo $ckey; ?>]" value="1" checked="checked" /> <?php echo _l($colarr[$ckey]['ll']); ?>
				  </li>
			  <?php 
				$req_table[] = _l($colarr[$ckey]['ll']);
			  } ?>
			  <?php foreach($colarr as $ckey=>$cval){ 
						if(!isset($targets_list_column_order[$ckey])){?>
							<li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
								<input type="checkbox" name="settings[target_list_column][<?php echo $ckey; ?>]" value="1"/> <?php echo _l($cval['ll']); ?>
							</li>
			  <?php }
			  } ?>
			  
			</ul>
			  
			</div>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				  </div>
				  </form>
				</div>
			  </div>
			</div>

			</div>
		
		<?php
    $table_data = [];
	

    if(has_permission('target','','delete')) {
      //$table_data[] = '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="invoice-items"><label></label></div>';
    }

    $table_data = array_merge($table_data, $req_table);

    render_datatable($table_data,'target-deal');  ?>
			<?php /*
			<table  data-new-rel-type="project" data-last-order-identifier="tasks" data-default-order="" class="table table-rel-tasks dataTable no-footer" role="grid" aria-describedby="DataTables_Table_1_info" id="DataTables_Table_01" >
			  <thead>
				  <th><?php echo _l('assign_to'); ?></th>
				  <th><?php echo _l('tracking_metric');?></th>
				  <th><?php echo _l('interval');?></th>
				  <th><?php echo _l('start_date');?></th>
				  <th><?php echo _l('end_date');?></th>
				  <th><?php echo _l('options'); ?></th>
				</tr>
			  </thead>
			  <tbody>
				<?php if(!empty($targets)){
					foreach($targets as $target1){
				?>
						<tr>
							<td><?php echo ucfirst($target1['assign']);?></td>
							<td><?php echo ucfirst($target1['tracking_metic']);?></td>
							<td><?php echo ucfirst($target1['interval']);?></td>
							<td><?php echo date('d-m-Y',strtotime($target1['start_date']));?></td>
							<td>
								<?php 
								if(!empty($target1['end_date'])){
									echo date('d-m-Y',strtotime($target1['end_date']));
								}else{
									echo _l('no_end_date');
								}
								?>
							</td>
							<td>
								<a href="#" onclick="check_edit('<?php echo $target1['id'];?>')" data-toggle="modal" data-target="#edit_deal"><i class="fa fa-pencil"></i></a>
								<a href="<?php echo admin_url('target/deal_delete/'.$target1['id']); ?>" style="margin-left:10px;" title="Delete">
									<i class="fa fa-trash" aria-hidden="true" ></i>
								</a>
							</td>
						</tr>
				<?php 
					}
				}?>
				</tbody>
			</table>*/?>
		</div>
  </div>
</div>
<div class="modal fade" id="compose-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-wrapper">
		<div class="modal-dialog" style="width:50%">
			<div class="modal-content">
				<div class="modal-header bg-blue">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><?php echo _l('add_goal');?></h4>
				</div>
				<div class="col-md-12 bg-white" style="border-radius:6px;">
					<div class="col-md-12"style="border-bottom:2px solid #e5e5e5;margin-bottom:15px;">
					<div class="col-md-5" style="margin-top:10px;">
						<p class="p_head"><?php echo _l('choose_entity');?></p>
						<div class="tabs active1" id="tab01" style="border-radius:10px;">
							<h6 class="text-muted"><span class="cur_deal"><i class="fa fa-dollar"></i></span><?php echo _l('deal');?><div class="pull-right dol_sym"><i class="fa fa-angle-right" style="font-size:40px;"></div></i></h6>
						</div>
						<div class="modal-footer"></div>
					</div>
					<div class="col-md-7" style="border-left:2px solid #e5e5e5;margin-top:10px;">
						<p class="p_head"><?php echo _l('choose_goal_type');?></p>
						<fieldset id="tab011" class="show">
							<form action="" method="post" id="compose_email" enctype='multipart/form-data' >
								<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
								<div class="modal-body">
									<div class="form-group">
										<div class="full_cont_div req_class" onclick="show_div('cur_div1','added')" id="cur_div1">
											<div class="first_cont_div"><?php echo _l('added');?></div>
											<div class="second_cont_div req_class" id="cur_div11">Based on the number or value of new deals</div>
										</div>
									</div>
									<div class="form-group">
										<div class="full_cont_div req_class" onclick="show_div('cur_div2','progressed')" id="cur_div2">
											<div class="first_cont_div"><?php echo _l('progressed');?></div>
											<div class="second_cont_div req_class" id="cur_div21">Based on the number or value of deals entering a certain stage</div>
										</div>
									</div>
									<div class="form-group">
										<div class="full_cont_div req_class" onclick="show_div('cur_div3','won')" id="cur_div3">
											<div class="first_cont_div"><?php echo _l('won');?></div>
											<div class="second_cont_div req_class" id="cur_div31">Based on the number or value of won deals</div>
										</div>
									</div>
								</div>
							</form>
						</fieldset>
						 <fieldset id="tab021" >
							<div id="template_header">
							</div>
							<form method='post' class='form-inline' id='default_template' action='<?php echo admin_url('company_mail/change_default'); ?>'>
								<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
								<div id="template_list1">
								</div>
							</form>
						</fieldset>
						
					</div>
					</div>
					<div class="modal-footer" style="background:#f7f7f7">
						<div>
							<button type="button" class="btn pull-right1" onclick="target_cancel()"><?php echo _l('cancel');?></button>
							<button type="button" class="btn btn-primary pull-right1" disabled id="enab_but" onclick="target_continue()"><?php echo _l('continue');?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="edit_deal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-wrapper">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-blue">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><?php echo _l('edit_goal_deal');?> <span id="goal_txt"></span></h4>
				</div>
				<div id="edit_deal_content"></div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="create_deal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-wrapper">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-blue">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><?php echo _l('add_goal_deal');?> <span id="goal_txt"></span></h4>
				</div>
				<form action="<?php echo admin_url('target/deal'); ?>" method="post" id="add_deal">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
					<div class="modal-body">
						<div class="form-group">
							<label ><b><?php echo _l('assign');?></b></label>
							<select class="form-control" name="assign" onchange="check_user(this)">
								<option value="company"><?php echo _l('company');?></option>
								<option value="user"><?php echo _l('user');?></option>
								<option value="team"><?php echo _l('team');?></option>
							</select>
						</div>
						<div class="form-group" style="display:none" id="select_manger1">
							<label > <small class="req text-danger">* </small><b><?php echo _l('manager');?></b></label>
							<select class="form-control selectpicker" data-live-search="true" name="select_manager[]" required   data-none-selected-text="<?php echo  _l('Select Manager');?>" id="select_manger2" onchange="check_manger(this)">
							</select>
						</div>
						<div class="form-group" style="display:none" id="assign_user_wise">
							<label ><b><?php echo _l('assign_user_wise');?></b></label>
							<input type="checkbox"  value="assign_user" name="assign_user_wise1" id="assign_user_wise1" onclick="assign_user(this)">
						</div>
						<div class="form-group" style="display:none" id="select_user1">
							<label > <small class="req text-danger">* </small><b><?php echo _l('user');?></b></label>
							<select class="form-control selectpicker" data-live-search="true" name="select_user[]" required  multiple data-none-selected-text="<?php echo  _l('Select User');?>" id="select_user2">
							</select>
						</div>
						<div class="form-group" id="pipe_stage" style="display:none">
							<label > <small class="req text-danger">* </small><b><?php echo _l('pipeline_stage');?></b></label>
							<select class="form-control selectpicker" data-live-search="true" name="pipeline_stage[]" required  multiple data-none-selected-text="<?php echo _l('Select Pipline Stage'); ?>" onclick="pipeline_stage()" id="pipeline_stage" data-actions-box='true'>
								<?php if(!empty($pipe_status)){
									foreach($pipe_status as $pipe_status1){
								?>
										<option value="<?php echo $pipe_status1['id'];?>"><?php echo $pipe_status1['name'];?></option>
								<?php 
									}
								}?>
							</select>
						</div>
						<div class="form-group" id="pipeselect_new">
							<label > <small class="req text-danger">* </small><b><?php echo _l('pipeline');?></b></label>
							<select class="form-control selectpicker" data-live-search="true" name="select_deal[]" required   data-none-selected-text="<?php echo _l('Select Pipline'); ?>" onchange="sel_deal()" id="select_deal_new" data-actions-box='true'>
								
							</select>
						</div>
						
						<div class="form-group" id="pipeselect">
							<label > <small class="req text-danger">* </small><b><?php echo _l('pipeline');?></b></label>
							<select class="form-control selectpicker" data-live-search="true" name="select_deal[]" required  multiple data-none-selected-text="<?php echo _l('Select Pipline'); ?>" onchange="sel_deal()" id="select_deal" data-actions-box='true'>
								<?php if(!empty($deals)){
									foreach($deals as $deal12){
								?>
										<option value="<?php echo $deal12['id'];?>"><?php echo $deal12['name'];?></option>
								<?php 
									}
								}?>
							</select>
						</div>
						<div class="form-group">
							<label style="margin-right:10px;"><b><?php echo _l('tracking_metric');?></b></label>
							<input type="radio" name="tracking_metic" id="track_value" value="Value" onchange="ch_count(this)" style="vertical-align:text-bottom;">   <?php echo _l('project_cost');?>
							<input type="radio" name="tracking_metic" id="track_count" checked value="Count"  onchange="ch_count(this)" style="vertical-align:text-bottom;margin-left:5px;">   <?php echo _l('count');?>
						</div>
						<div class="form-group">
							<label ><b><?php echo _l('interval');?></b></label>
							<select class="form-control" name="interval" id="interval" onchange="sel_deal()">
								<option value="Weekly"><?php echo _l('weekly');?></option>
								<option value="Monthly"><?php echo _l('monthly');?></option>
								<option value="Quarterly"><?php echo _l('quarterly');?></option>
								<option value="Half Yearly"><?php echo _l('half_yearly');?></option>
								<option value="Yearly"><?php echo _l('yearly');?></option>
							</select>
						</div>
						<div class="form-group">
							<label > <small class="req text-danger">* </small><b><?php echo _l('duration');?></b></label>
							<div>
								<input type="text" name="start_date" class="form-control datepicker1" style="width:49%;float:left" required  autocomplete="off" id="start_date1" placeholder="Start Date" >
								<input type="hidden" name="goal_val" class="form-control" id="goal_val" >
								<input type="text" name="end_date" class="form-control datepicker1" style="width:49%;float:left;margin-left:10px;"  autocomplete="off" id="end_date1" placeholder="End Date" >
							</div>
						</div>
						<div class="form-group" style="background:lightgray;margin-top:65px;">
							<?php echo _l('expected_outcome');?>
						</div>
						<div id="sel_deal">
							<div id="all_int">
								<div class="form-group half_width" ><label ><b><?php echo _l('interval');?></b></label>
									<div>
										<input type="text" name="intervals[]" class="form-control " readonly value="Weekly" id="ch_interval">
									</div>
								</div>
								<div class="form-group half_width mar_10" >
									<label class="ch_Count ch_value1" ><b><?php echo _l('count');?></b></label>
									<label class="ch_Value ch_value1" style="display:none"><b><?php echo _l('value_inr');?></b></label>
									<div><input type="number" name="intreval_value[]" class="form-control " min="0" ></div>
								</div>
							</div>
							<div id="month_int" style="display:none">
								<?php if(!empty($months)){
									foreach($months as $month12){
										?>
											<div class="form-group half_width" ><label><b><?php echo _l('interval').' ('.$month12.' )';?></b></label>
												<div>
													<input type="text" name="intervals1[]" class="form-control " readonly value="Monthly" >
												</div>
											</div>
											<div class="form-group half_width mar_10" >
												<label class="ch_Count ch_value1"><b><?php echo _l('count');?></b></label>
												<label class="ch_Value ch_value1" style="display:none"><b><?php echo _l('value_inr');?></b></label>
												<div><input type="number" name="intreval_value2[]" class="form-control "  min="0"></div>
											</div>
										<?php
									}
								}?>
							</div>
						</div>
						<div id="custom_fields_items">
							<?php echo render_custom_fields('target'); ?>
						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default pull-left" onclick="show_previous()"><i class="fa fa-arrow-left" aria-hidden="true" style="margin-right:10px;"></i><?php echo _l('previous_step');?></button>
						<button type="submit" class="btn btn-primary pull-right" name="save"><?php echo _l('save');?></button>
						<button type="button" class="btn btn-primary1 pull-right" onclick="close_form_popup()"><?php echo _l('cancel');?></button>
						
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
<script>
function assign_user(a){
	$('#select_user1').hide();
	if(a.checked){
		var select_manger2 = $('#select_manger2').val();
		var data = {assign:select_manger2};
		 var ajaxRequest = $.ajax({
            type: 'POST',
            url: admin_url + 'target/getusers',
            data: data,
            dataType: '',
            success: function(msg) {
				$('#select_user2').empty().append();
				$('#select_user2').append(msg);
				$('#select_user2').selectpicker('refresh');
				$('#select_user1').show();
				
            }
        });
	}
}
function assign_user_edit(a){
	$('#select_user1_edit').hide();
	$('#select_user2_edit').empty();
	$('#select_user2_edit').selectpicker('refresh');
	if(a.checked){
		var select_manger2 = $('#select_manger2_edit').val();
		var data = {assign:select_manger2};
		 var ajaxRequest = $.ajax({
            type: 'POST',
            url: admin_url + 'target/getusers',
            data: data,
            dataType: '',
            success: function(msg) {
				
				$('#select_user1_edit').show();
				$('#select_user2_edit').empty().append();
				$('#select_user2_edit').append(msg);
				$('#select_user2_edit').selectpicker('refresh');
            }
        });
	}
}
function check_manger(a){
	$('#assign_user_wise').hide();
	$('#select_user1').hide();
	$('#select_user1_edit').hide();
	$('#assign_user_wise1').prop('checked', false);
	if(a.value !=''){
		$('#assign_user_wise').show();
		$('#select_user1').hide();
		$('#select_user1_edit').hide();
		
		
	}
}
function check_user(a){
	$('#select_manger1').hide();
	$('#select_user1').hide();
	$('#assign_user_wise').hide();
	if(a.value == 'user'){
		var data = {assign:a.value};
        var ajaxRequest = $.ajax({
            type: 'POST',
            url: admin_url + 'target/getusers',
            data: data,
            dataType: '',
            success: function(msg) {
				//$('#select_user2').empty().append('<option value="">Nothing Selected</option>');
				$('#select_user2').empty();
				$('#select_user2').append(msg);
				$('#select_user2').selectpicker('refresh');
				$('#select_user1').show();
            }
        });
	}
	else if(a.value == 'team'){
		var data = {assign:a.value};
        var ajaxRequest = $.ajax({
            type: 'POST',
            url: admin_url + 'target/getusers',
            data: data,
            dataType: '',
            success: function(msg) {
				
				$('#select_manger2').empty().append('<option value="">Select Manager</option>');
				//$('#select_manger2').empty();
				$('#select_manger2').append(msg);
				$('#select_manger2').selectpicker('refresh');
				$('#select_manger1').show();
				//$('#assign_user_wise').show();
            }
        });
	}
}
function check_user_edit(a){
	$('#select_manger1_edit').hide();
	$('#select_user1_edit').hide();
	var target_id = $('#target_id').val();
	$('#assign_user_wise').hide();
	$('#assign_user_wise1').prop('checked', false);
	$('#select_manger2_edit').empty();
	$('#select_user2_edit').empty();
	$('#select_user2_edit').selectpicker('refresh');
	$('#select_manger2_edit').selectpicker('refresh');
	if(a == 'user'){
		var data = {assign:a,'target_id':target_id};
        var ajaxRequest = $.ajax({
            type: 'POST',
            url: admin_url + 'target/getusers_edit',
            data: data,
            dataType: '',
            success: function(msg) {
				//$('#select_user2_edit').empty().append('<option value="">Nothing Selected</option>');
				$('#select_user2_edit').empty();
				$('#select_user2_edit').append(msg);
				$('#select_user2_edit').selectpicker('refresh');
				$('#select_user1_edit').show();
				appDatepicker();
            }
        });
	}
	else if(a == 'team'){
		var data = {assign:a,'target_id':target_id};
        var ajaxRequest = $.ajax({
            type: 'POST',
            url: admin_url + 'target/getusers_edit',
            data: data,
            dataType: '',
            success: function(msg) {
				var msg1 = JSON.parse(msg);
				//$('#select_manger2_edit').empty().append('<option value="">Nothing Selected</option>');
				$('#select_manger2_edit').empty().append('<option value="">Select Manager</option>');
				$('#select_manger2_edit').append(msg1.req_out);
				$('#select_manger2_edit').selectpicker('refresh');
				//$('#select_manger2_edit').selectpicker('refresh');
				$('#select_manger1_edit').show();
				$('#assign_user_wise').show();
				if(msg1.assign_user == 'assign_user'){
					$('#assign_user_wise1').prop('checked',true);
					//$('#select_user2_edit').empty().append('<option value="">Nothing Selected</option>');
					$('#select_user2_edit').empty();
					$('#select_user2_edit').append(msg1.users);
					$('#select_user2_edit').selectpicker('refresh');
					$('#select_user1_edit').show();
				}
				appDatepicker();
            }
        });
	}
}

function pipeline_stage(){
	var pipeline_stage  = $('#pipeline_stage').val();
	
	var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'target/pipe_stage?pipeline_stage='+pipeline_stage,
	//	data: data,
		dataType: '',
		success: function(msg) {
			$('#select_deal_new').empty().append('<option value="">Select Pipeline</option>');
			//$('#select_deal_new').empty().append(msg);
			$('#select_deal_new').append(msg);
			$('#select_deal_new').selectpicker('refresh');
			$('#pipeselect_new').show();
		}
	});
}
function pipeline_stage_edit(){
	var pipeline_stage  = $('#pipeline_stage_1').val();
	
	var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'target/pipe_stage?pipeline_stage='+pipeline_stage,
	//	data: data,
		dataType: '',
		success: function(msg) {
			//$('#select_deal_new1').empty().append('<option value="">Nothing Selected</option>');
			$('#select_deal_new1').empty().append('<option value="">Select Pipeline</option>');
			$('#select_deal_new1').append(msg);
			$('#select_deal_new1').selectpicker('refresh');
		}
	});
}
function sel_deal(){
	var b = $('#select_deal').val();
	b = b+','
	 var c = b.split(",");
	 var d = (c.length)-1;
	 var req_val = '';
	 var rad_val = $('input[name="tracking_metic"]:checked').val();
	 var int_val = $('#interval').val();
	$('#ch_interval').val(int_val);
	$('#all_int').hide();
	$('#month_int').hide();
	if(int_val == 'Monthly'){
		$('#month_int').show();
	}else{
		$('#all_int').show();
	}
	$('#all_int1').hide();
	$('#month_int1').hide();
	$('#week_int1').hide();
	if(int_val == 'Monthly'){
		$('#month_int1').show();
		
	}else{
		$('#week_int1').show();
	}
	$('#ch_week_ch').val(int_val);
}
function show_previous(){
	$('#create_deal').modal('hide');
	$('#compose-modal').modal('show');
}
function ch_count(a){
	$('.ch_value1').hide();
	var req_id = 'ch_'+a.value;
	$('.'+req_id).show();
}
function close_form_popup(){
	$('#compose-modal').modal('hide');
	$('#create_deal').modal('hide')
	$('#edit_deal').modal('hide')
}
function target_continue(){
	document.getElementById('add_deal').reset();
	$('.ch_value').hide();
	$('#ch_Count').show();
	$('#compose-modal').modal('hide');
	$('#pipe_stage').hide();
	$('#pipeselect').hide();
	$('#pipeselect_new').hide();
	$('#create_deal').modal('show');
	var gloabl_val = $('#goal_val').val();
	if(gloabl_val == 'progressed'){
		$('#pipe_stage').show();
		$('#pipeselect_new').show();
	}else{
		$('#pipeselect').show();
	}
}
function target_cancel(){
	$('#compose-modal').modal('hide');
}
function target_pop(){
	$('#edit_deal_content').html('');
	$('.req_class').removeClass('active_new');
	$('#enab_but').prop('disabled', true);
}
function show_div(a,b){
	$('#goal_txt').html(b);
	//$('.error').hide();
	$('label[class="error"]').hide();
	$('#select_manger1').hide();
	$('#assign_user_wise').hide();
	$('#select_user1').hide();
	$('#month_int').hide();
	$('#all_int').show();
	$('#goal_val').val(b);
	$('.req_class').removeClass('active_new');
    $('#'+a).addClass('active_new');
	$('#'+a+'1').addClass('active_new');
	$(':button').prop('disabled', false);
	
	$('#pipeline_stage').val('');
	$('#select_deal').val('');
	$('#select_deal_new').val('');
	$('#pipeline_stage').selectpicker('refresh');
	$('#select_deal').selectpicker('refresh');
	$('#select_deal_new').selectpicker('refresh');
}
function check_edit(a){
	$('#edit_deal_content').html('');
	var data = {target_id:a};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'target/check_edit',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#edit_deal_content').html(msg);
			$('#select_deal_edit').selectpicker('refresh');
			$('#pipeline_stage_1').selectpicker('refresh');
			$('#select_deal_new1').selectpicker('refresh');
			$('#select_manger2_edit').selectpicker('refresh');
			var a = $('#edit_assign').val();
			check_user_edit(a);
			var ch_edit = $('#start_date_edit').val();
			console.info(ch_edit);
			
			$('#end_date_edit').datepicker({
				 dateFormat:'dd-mm-yy',
				 calendarWeeks: true,
				autoclose: true,
				changeMonth: true,
				changeYear: true,
				timepicker: false,
			});
			$('#start_date_edit').datepicker({
				 dateFormat:'dd-mm-yy',
				   timepicker: false,
				   calendarWeeks: true,
				  changeMonth: true,
				  changeYear: true,
				todayHighlight: true,
				  onSelect: function(selectedDate) {
					  console.info(selectedDate);
						$('#end_date_edit').datepicker('option', 'minDate', selectedDate);
				  }
			});
			$('#end_date_edit').datepicker('option', 'minDate', ch_edit);
			appDatepicker();
			
		}
	});
}
 
  </script>
<style>
.error{
	color:#F3031C;
}
label#start_date-error {
    position: absolute;
    margin-top: 40px;
	float: left;
    left: 0;
    margin-left: 15px;
}
.active1{
	background: #eee;
}
.half_width{
	width:48%;
	float:left;
}
.mar_10{
	margin-left:10px;
}
.text-muted {
    
    min-height: 54px;
    text-align: center;
    border-radius: 4px;
    display: flex;
    align-items: center;
    padding: 8px 10px;
    margin-bottom: 2px;
    font-weight: 800; 
    box-sizing: border-box;
}
.p_head{
    font-size: 15px;
    text-transform: uppercase;
    font-weight: 500;
}
.full_cont_div:hover {
    background: #eee;
}
.full_cont_div{
	white-space:nowrap;
	overflow:hidden;
	align-items:center;
	cursor:pointer;
	display:grid;
	min-height:54px;
	border-radius:10px;
	padding-bottom:7px;
	padding-top:7px;
}
.first_cont_div{
	text-overflow:ellipsis;
	overflow:hidden;
	font-size:16px;
	margin-left:10px;
}
.second_cont_div{
	font-size:14px;
	white-space:initial;
	color:#747678;
	padding-right:22px;
	margin-left:12px;
}
.active_new{
	background-color: #468DDD !important;
    color: #ffff !important;
}
.cur_deal {
    height: 35px;
    width: 35px;
    border-radius: 50%;
    display: inline-block;
    background-color: #468DDD;
    color: #ffff;
    margin-right: 20px;
    align-items: center;
    padding: 10px;
}
.dol_sym{
	position:absolute;
	right:30px;
}
</style>