<form action="<?php echo admin_url('target/edit_activity/'.$target_id); ?>" method="post" id="edit_deal1">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
	<div class="modal-body">
		<div class="form-group">
			<label ><b><?php echo _l('assign');?></b></label>
			<select class="form-control" name="assign" onchange="check_user_edit(this.value)" id="edit_assign" required>
				<option value="company" <?php if($targets[0]['assign'] == 'company'){echo 'selected';}?>><?php echo _l('company');?></option>
				<option value="user" <?php if($targets[0]['assign'] == 'user'){echo 'selected';}?>><?php echo _l('user');?></option>
				<option value="team" <?php if($targets[0]['assign'] == 'team'){echo 'selected';}?>><?php echo _l('team');?></option>
			</select>
			<input type="hidden"  class="form-control" id="target_id" value="<?php echo $targets[0]['id'];?>">
		</div>
		<div class="form-group" style="display:none" id="select_manger1_edit">
			<label > <small class="req text-danger">* </small><b><?php echo _l('manager');?></b></label>
			<select class="form-control selectpicker" data-live-search="true" name="select_manager[]"    data-none-selected-text="<?php echo  _l('Select Manager');?>" id="select_manger2_edit" onchange="check_manger(this)" required>
			</select>
		</div>
		<div class="form-group" style="display:none" id="assign_user_wise">
			<label ><b><?php echo _l('assign_user_wise');?></b></label>
			<input type="checkbox"  value="assign_user" name="assign_user_wise1" id="assign_user_wise1" onclick="assign_user_edit(this)">
		</div>
		<div class="form-group" style="display:none" id="select_user1_edit">
			<label > <small class="req text-danger">* </small><b><?php echo _l('user');?></b></label>
			<select class="form-control selectpicker" data-live-search="true" name="select_user[]"   multiple data-none-selected-text="<?php echo  _l('Select User');?>" id="select_user2_edit" required>
			</select>
		</div>
		
		<div class="form-group" id="edit_pipeselect">
			<label > <small class="req text-danger">* </small><b><?php echo _l('pipeline');?></b></label>
			<select class="form-control selectpicker" data-live-search="true" name="select_deal[]"   multiple data-none-selected-text="<?php echo _l('Select Pipline'); ?>"  id="select_deal_edit" data-actions-box='true' required>
				<?php if(!empty($deals)){
					foreach($deals as $deal12){
				?>
						<option value="<?php echo $deal12['id'];?>" <?php if(in_array($deal12['id'],$sel_deals)){echo 'selected';}?>><?php echo $deal12['name'];?></option>
				<?php 
					}
				}?>
			</select>
		</div>
		<div class="form-group">
			<label style="margin-right:10px;"><b><?php echo _l('tracking_metric');?></b></label>
			<input type="radio" name="tracking_metic" id="track_value" value="Value" required onchange="ch_count(this)" style="vertical-align:text-bottom;" <?php if($targets[0]['tracking_metric'] == 'Value'){echo 'checked';}?>>   <?php echo _l('project_cost');?>
			<input type="radio" name="tracking_metic" id="track_count"  value="Count"  onchange="ch_count(this)" style="vertical-align:text-bottom;margin-left:5px;" <?php if($targets[0]['tracking_metric'] == 'Count'){echo 'checked';}?>>   <?php echo _l('count');?>
		</div>
		<div class="form-group">
			<label ><b><?php echo _l('interval');?></b></label>
			<select class="form-control" name="interval" id="interval" onchange="sel_deal()" required>
				<option value="Weekly" <?php if($targets[0]['interval'] == 'Weekly'){echo 'selected';}?>><?php echo _l('weekly');?></option>
				<option value="Monthly" <?php if($targets[0]['interval'] == 'Monthly'){echo 'selected';}?>><?php echo _l('monthly');?></option>
				<option value="Quarterly" <?php if($targets[0]['interval'] == 'Quarterly'){echo 'selected';}?>><?php echo _l('quarterly');?></option>
				<option value="Half Yearly" <?php if($targets[0]['interval'] == 'Half Yearly'){echo 'selected';}?>><?php echo _l('half_yearly');?></option>
				<option value="Yearly" <?php if($targets[0]['interval'] == 'Yearly'){echo 'selected';}?>><?php echo _l('yearly');?></option>
			</select>
		</div>
		<div class="form-group">
			<label > <small class="req text-danger">* </small><b><?php echo _l('duration');?></b></label>
			<div>
				<input type="text" name="start_date" class="form-control datepicker1" style="width:49%;float:left" required value="<?php echo date('d-m-Y',strtotime($targets[0]['start_date']));?>" autocomplete="off" placeholder="Start Date" id="start_date_edit">
				<input type="hidden" name="goal_val" class="form-control" id="goal_val" >
				<input type="text" name="end_date" class="form-control datepicker1" style="width:49%;float:left;margin-left:10px;" <?php if(!empty($targets[0]['end_date'])){?>value="<?php echo date('d-m-Y',strtotime($targets[0]['end_date']));?>" <?php }?> autocomplete="off" placeholder="End Date" minDate="<?php echo date('d-m-Y',strtotime($targets[0]['start_date']));?>" id="end_date_edit">
			</div>
		</div>
		<div class="form-group" style="background:lightgray;margin-top:65px;">
			<?php echo _l('expected_outcome');?>
		</div>
		<div id="sel_deal">
			<div id="all_int1">
				<?php if(!empty($intervals)){
					foreach($intervals as $interval1){
				?>	
						<div class="form-group half_width" ><label ><b><?php echo _l('interval');?></b></label>
							<div>
								<input type="text" name="intervals1[]" class="form-control " readonly value="<?php echo $interval1['interval'];?>">
							</div>
						</div>
						<div class="form-group half_width mar_10" >
							<label class="ch_Count ch_value1" style=" <?php if($targets[0]['tracking_metric'] == 'Value'){?>display:none<?php }?>"><b><?php echo _l('count');?></b></label>
							<label class="ch_Value ch_value1"  <?php if($targets[0]['tracking_metric'] == 'Count'){?>style="display:none" <?php }?>><b><?php echo _l('value_inr');?></b></label>
							<div><input type="number" name="intreval_value2[]" class="form-control " value="<?php echo $interval1['interval_value'];?>" min="0"></div>
						</div>
				<?php
					}
				}?>
			</div>
			<div id="week_int1" style="display:none">
				<div class="form-group half_width" ><label ><b><?php echo _l('interval');?></b></label>
					<div>
						<input type="text" name="intervals1[]" class="form-control " readonly value="Monthly" id="ch_week_ch">
					</div>
				</div>
				<div class="form-group half_width mar_10" >
					<label class="ch_Count ch_value1" ><b><?php echo _l('count');?></b></label>
					<label class="ch_Value ch_value1" style="display:none"><b><?php echo _l('value_inr');?></b></label>
					<div><input type="number" name="intreval_value2[]" class="form-control "  min="0"></div>
				</div>
			</div>
			<div id="month_int1" style="display:none">
				<?php if(!empty($months)){
					foreach($months as $month12){
						?>
							<div class="form-group half_width" ><label ><b><?php echo _l('interval').' ('.$month12.' )';?></b></label>
								<div>
									<input type="text" name="intervals1[]" class="form-control " readonly value="Monthly" >
								</div>
							</div>
							<div class="form-group half_width mar_10" >
								<label class="ch_Count ch_value1"><b><?php echo _l('count');?></b></label>
								<label class="ch_Value ch_value1" style="display:none"><b><?php echo _l('value_inr');?></b></label>
								<div><input type="number" name="intreval_value2[]" class="form-control " min="0" ></div>
							</div>
						<?php
					}
				}?>
			</div>
		</div>
		<div id="custom_fields_items">
			<?php echo render_custom_fields( 'target',$target_id); ?>
		</div>
	</div>
	<div class="modal-footer">
		
		<button type="submit" class="btn btn-primary pull-right" name="save"><?php echo _l('save');?></button>
		<button type="button" class="btn btn-primary1 pull-right" onclick="close_form_popup()" style="margin-right:8px;"><?php echo _l('cancel');?></button>
		
	</div>
	</form>
	<script>
	
	$( "#edit_deal1" ).validate({
  rules: {
	select_deal: {
	  required: true
	},
	select_user2_edit: {
	  required: true,
	},
	select_manger2_edit: {
	  required: true,
	}
  }
});

</script>