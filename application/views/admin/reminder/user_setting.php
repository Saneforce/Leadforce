<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
.followers-div, .addfollower_btn {
  display:none;
}
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="no-margin">
                <?php echo $title; ?>
            </h4>
            <hr class="hr-panel-heading" />
           
            <?php echo form_open($this->uri->uri_string(),array('id'=>'reminder_form','autocomplete'=>'off','onsubmit'=>'return validate_remind_from()')); ?>
              <input type="hidden" id="ch_type" value="<?php echo $cur_setting;?>">
              <div class="col-md-12 pipeselect">
				<div class="form-group" >
					<label class="control-label" style="font-weight:500; margin-right:10px;"><?php echo _l('enable_reminder'); ?></label>
					<div class="radio radio-primary radio-inline">
						<input Type="radio" name="remind_status" id="ch_rm_setting" value="enable"  onchange="ch_rem_seting(this)" <?php if($reminder_status =='enable'){echo 'checked';}?>><label class=""><?php echo _l('Yes'); ?></label>
					</div>
					<div class="radio radio-primary radio-inline">
						<input Type="radio" name="remind_status" value="disable" id="ch_rm_setting" onchange="ch_rem_seting(this)" <?php if($reminder_status =='disable'){echo 'checked';}?>><label class="" ><?php echo _l('No'); ?></label>
					</div>
					<div class="error radio_error" id="rem_set_er" style="display:none">This field is required.</div>
				</div>
				<?php if($cur_setting == 'company'){?>
					<div class="form-group select-placeholder contactid input-group-select" id="ch_company" <?php if($reminder_status !='enable'){?> style="display:none" <?php }?>>
					  <label class="control-label"><?php echo _l('reminder_settings'); ?> </label>
					  <div class="dropdown bootstrap-select reminder_settings input-group-select show-tick bs3 bs3-has-addon" style="width: 93%;">
						<select id="reminder_settings" name="reminder_settings" class=" selectpicker"  data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" tabindex="-98" required onchange="check_reminder(this.value)" required>
						  <option value=""><?php echo _l('deallossreasons_option_select'); ?></option>
						  <option value="company" <?php if(get_option('reminder_settings') == 'company'){ echo 'selected';}?> ><?php echo _l('company_level'); ?></option>
						  <option value="user" <?php if(get_option('reminder_settings') == 'user'){ echo 'selected';}?>><?php echo _l('user_level'); ?></option>
						  
						</select>
					  </div>
					</div>
				<?php }?>
               <?php $req_reminder_type = array();
				if(!empty($reminder_settings->reminder_type)){
					$req_reminder_type = json_decode($reminder_settings->reminder_type);
				}
				?>
				<div class="checkbox checkbox-info"  id="type_reminder" <?php if($cur_setting == 'company' && get_option('reminder_settings') != 'company'){ ?>style="display:none" <?php }?> <?php if($reminder_status =='disable'){ ?>style="display:none" <?php }?>>
					<input Type="checkbox" name="reminder_type[]" value="activity" onclick="check_activity(this,'activity_reminder')" <?php if(!empty($req_reminder_type) && in_array("activity", $req_reminder_type)){echo 'checked';}?> class="check_rem_type" id="act_rem_type"><label class="check_label"><?php echo _l('activity_reminder'); ?></label>
					<input Type="checkbox" name="reminder_type[]" value="proposal"  onclick="check_activity(this,'proposal_reminder')" <?php if(!empty($req_reminder_type) &&in_array("proposal", $req_reminder_type)){echo 'checked';}?> class="check_rem_type" id="pr_rem_type"><label class="check_label" ><?php echo _l('proposal_reminder'); ?></label>
					<input Type="checkbox" name="reminder_type[]" value="target" onclick="check_activity(this,'target_reminder')" <?php if(!empty($req_reminder_type) &&in_array("target", $req_reminder_type)){echo 'checked';}?> class="check_rem_type" id="tar_rem_type"><label class="check_label"><?php echo _l('target_reminder'); ?></label>
					<input Type="checkbox" name="reminder_type[]" value="customer" onclick="check_activity(this,'customer_reminder')" <?php if(!empty($req_reminder_type) &&in_array("customer", $req_reminder_type)){echo 'checked';}?> class="check_rem_type" id="customer_rem_type"><label class="check_label"><?php echo _l('customer_reminder'); ?></label>
					<div class="error check_error" id="rem_type_er" style="display:none">This field is required.</div>
				</div>
				<?php $act_notify = array();
				if(!empty($reminder_settings->act_notify)){
					$act_notify = json_decode($reminder_settings->act_notify);
				}
				?>
				<div class="form-group" <?php if(empty($req_reminder_type) || !in_array("customer", $req_reminder_type)){?> style="display:none"  <?php }?> id="customer_reminder">
					<label class="control-label"><h4><?php echo _l('customer_reminder'); ?></h4></label>
					<div class="radio radio-primary radio-inline" style="margin-left:5px;">
						<input Type="radio" name="customer_reminder" id="activity_customer" value="all_activities"  <?php if((!empty($reminder_settings->customer_reminder) && $reminder_settings->customer_reminder == 'all_activities') || empty($reminder_settings->customer_reminder)){echo 'checked';}?>><label class=""><?php echo _l('all_activities'); ?></label>
					</div>
					<div class="radio radio-primary radio-inline">
						<input Type="radio" name="customer_reminder" value="required_activities" id="activity_customer" <?php if((!empty($reminder_settings->customer_reminder) && $reminder_settings->customer_reminder == 'required_activities') ){echo 'checked';}?>><label class="" ><?php echo _l('required_activities'); ?></label>
					</div>
					<div class="form-group m_left2" >
						<label ><?php echo _l('send_reminder_notify'); ?></label><br />
				 <?php 
					$cur_times = array();
					for($i=0;$i<25;$i++){
						$cur_times[] = $i;
					}
					$cus_day =$cus_hr = $cus_min = '';
					$cus_mail = array();
					if(!empty($reminder_settings->customer_mail)){
						$cus_mail = explode(':',$reminder_settings->customer_mail);
						if(isset($cus_mail[0])){
							$cus_day = $cus_mail[0];
						}
						if(isset($cus_mail[1])){
							$cus_hr = $cus_mail[1];
						}
						if(isset($cus_mail[2])){
							$cus_min = $cus_mail[2];
						}
					}
					?>
					<select  class="form-control" style="width:30%;float:left;" name="customer_d" required>
						<option value=""><?php echo _l('deallossreasons_option_select'); ?></option>
						<?php foreach($cur_times as $cur_time12){?>
							<option value="<?php echo $cur_time12;?>" <?php if($cus_day == $cur_time12){echo 'selected';} ?>><?php echo $cur_time12.' days';?></option>
						<?php }?>
					</select>
					<select  class="form-control" style="width:30%;float:left;margin-left:1%" name="customer_h" required>
						<option value=""><?php echo _l('deallossreasons_option_select'); ?></option>
						<?php foreach($cur_times as $cur_time12){?>
							<option value="<?php echo $cur_time12;?>" <?php if($cus_hr == $cur_time12){echo 'selected';} ?>><?php echo $cur_time12.' hrs';?></option>
						<?php }?>
					</select>
					
					<?php 
					$cur_times = array();
					for($i=0;$i<60;$i++){
						$cur_times[] = $i;
					}
					?>
					<select  class="form-control" style="width:30%;float:left;margin-left:1%" name="customer_m" required>
						<option value=""><?php echo _l('deallossreasons_option_select'); ?></option>
						<?php foreach($cur_times as $cur_time12){?>
							<option value="<?php echo $cur_time12;?>" <?php if($cus_min == $cur_time12){echo 'selected';} ?>><?php echo $cur_time12.' mins';?></option>
						<?php }?>
					</select>
					<br>
				</div>
					
				 </div>
				<div class="form-group" <?php if(empty($req_reminder_type) || !in_array("activity", $req_reminder_type)){?> style="display:none"  <?php }?> id="activity_reminder">
					<label class="control-label"><h4><?php echo _l('activity_reminder'); ?></h4></label>
					<div class="checkbox checkbox-info m_left2" >
						<input type="checkbox" name="activity_notify[]" value="show alert" id="activity_reminder_alert" onclick="check_activity(this,'activity_alert')" <?php if(in_array("show alert", $act_notify)){echo 'checked';}?> class="activity_notify ch_act_alert"><label class="check_label"><?php echo _l('show_alert'); ?></label>
						<input type="checkbox" name="activity_notify[]" value="send mail individual" onclick="check_activity(this,'activity_alert')" id="activity_reminder_individual" <?php if(in_array("send mail individual", $act_notify)){echo 'checked';}?>  class="activity_notify ch_act_alert"><label class="check_label" ><?php echo _l('send_mail_individual'); ?></label>
						<input type="checkbox" name="activity_notify[]" value="include summary email" onclick="check_activity(this,'activity_email')" id="activity_reminder_notify" <?php if(in_array("include summary email", $act_notify)){echo 'checked';}?>  class="activity_notify"><label class="check_label" ><?php echo _l('include_summary_mail'); ?></label>
						<div class="error check_error" id="act_notify_er" style="display:none">This field is required.</div>
					</div>
				 </div>
				 <div class="form-group m_left2" <?php if((!in_array("show alert", $act_notify) && !in_array("send mail individual", $act_notify) ) || (empty($req_reminder_type) || !in_array("activity", $req_reminder_type))){?>style="display:none;" <?php }?> id="activity_alert">
				 <label ><?php echo _l('show_alert_notify'); ?></label><br />
				 <?php 
					$cur_times = array();
					for($i=0;$i<25;$i++){
						$cur_times[] = $i;
					}
					$act_day =$act_hr = $act_min = '';
					$act_alert = array();
					if(!empty($reminder_settings->act_alert)){
						$act_alert = explode(':',$reminder_settings->act_alert);
						if(isset($act_alert[0])){
							$act_day = $act_alert[0];
						}
						if(isset($act_alert[1])){
							$act_hr = $act_alert[1];
						}
						if(isset($act_alert[2])){
							$act_min = $act_alert[2];
						}
					}
					?>
					<select  class="form-control" style="width:30%;float:left;" name="activity_alert_d" required>
						<option value=""><?php echo _l('deallossreasons_option_select'); ?></option>
						<?php foreach($cur_times as $cur_time12){?>
							<option value="<?php echo $cur_time12;?>" <?php if($act_day== $cur_time12){echo 'selected';}?>><?php echo $cur_time12.' days';?></option>
						<?php }?>
					</select>
					<select  class="form-control" style="width:30%;float:left;margin-left:1%" name="activity_alert_h" required>
						<option value=""><?php echo _l('deallossreasons_option_select'); ?></option>
						<?php foreach($cur_times as $cur_time12){?>
							<option value="<?php echo $cur_time12;?>" <?php if($act_hr== $cur_time12){echo 'selected';}?>><?php echo $cur_time12.' hrs';?></option>
						<?php }?>
					</select>
					
					<?php 
					$cur_times = array();
					for($i=0;$i<60;$i++){
						$cur_times[] = $i;
					}
					?>
					<select  class="form-control" style="width:30%;float:left;margin-left:1%" name="activity_alert_m" required>
						<option value=""><?php echo _l('deallossreasons_option_select'); ?></option>
						<?php foreach($cur_times as $cur_time12){?>
							<option value="<?php echo $cur_time12;?>"  <?php if($act_min== $cur_time12){echo 'selected';}?>><?php echo $cur_time12.' mins';?></option>
						<?php }?>
					</select>
					<br>
				</div>
				<div class="form-group m_left2 select-placeholder contactid input-group-select" id="activity_email" <?php if(!in_array("include summary email", $act_notify) || (empty($req_reminder_type) || !in_array("activity", $req_reminder_type))){?>style="display:none;" <?php }?> ><br>
                  <label class="control-label"><?php echo _l('include_summary_mail'); ?></label><br>
                  <div class="dropdown bootstrap-select reminder_settings input-group-select show-tick bs3 bs3-has-addon" style="width: 45%;float:left">
                    <select  class=" selectpicker" name="activity_mail" data-width="100%" data-none-selected-text="Nothing selected" tabindex="-98" id="activity_mail" onchange="check_mail('activity',this.value)" required>
						<option value="daily" <?php if(!empty($reminder_settings->act_mail) && $reminder_settings->act_mail == 'daily'){echo 'selected';}?>><?php echo _l('daily'); ?></option>
                      <option value="weekly"  <?php if(!empty($reminder_settings->act_mail) && $reminder_settings->act_mail == 'weekly'){echo 'selected';}?>><?php echo _l('weekly'); ?></option>
                      <option value="monthly"  <?php if(!empty($reminder_settings->act_mail) && $reminder_settings->act_mail == 'monthly'){ echo 'selected';}?>><?php echo _l('monthly'); ?></option>
                      
                    </select>
                  </div>
                </div>
				<div class="form-group m_left2 activity_mail" style="<?php if((empty($reminder_settings->act_mail) || $reminder_settings->act_mail != 'monthly') || (empty($req_reminder_type) || !in_array("activity", $req_reminder_type))){?>display:none;<?php }?>width: 45%;float:left;margin-top:-1.5%;" id="activity_monthly">
				<?php $act_time = explode(',',$reminder_settings->act_date_time);?>
					<input Type="text" class="form-control datetimepicker" name="activity_monthly" required placeholder ="please select date and Time" value="<?php  if($reminder_settings->act_mail == 'monthly' && !empty($reminder_settings->act_date_time) ){ echo $reminder_settings->act_date_time;}?>" style="width:48%;float:left" id="activity_monthy_t">
					<select  class="form-control" style="width:48%;float:left;margin-left:1%" name="activity_month_f" required>
						
						<option value="current_month" <?php  if(!empty($reminder_settings->act_month) && $reminder_settings->act_month =='current_month'){ echo 'selected';}?>>Current Month</option>
						<option value="next_month" <?php  if(!empty($reminder_settings->act_month) && $reminder_settings->act_month =='next_month'){ echo 'selected';}?>>Next Month</option>
						
					</select>
				</div>
				<div class="form-group m_left2 activity_mail" style="width: 45%;float:left;margin-top:-1.5%;<?php if((empty($reminder_settings->act_mail) || $reminder_settings->act_mail != 'daily') || (empty($req_reminder_type) || !in_array("activity", $req_reminder_type)) || !in_array("include summary email", $act_notify)  ){?>display:none;<?php }?>" id="activity_daily">
					<input Type="text" class="form-control timepicker" name="activity_daily" id="activity_daily12" required placeholder ="please select time" value="<?php  if($reminder_settings->act_mail == 'daily' &&!empty($reminder_settings->act_date_time)){ echo $reminder_settings->act_date_time;}?>" >
				</div>
				<div class="form-group m_left2 activity_mail" style="<?php if((empty($reminder_settings->act_mail) || $reminder_settings->act_mail != 'weekly') || (empty($req_reminder_type) || !in_array("activity", $req_reminder_type))){?>display:none;<?php }?>width: 45%;float:left;margin-top:-1.5%;" id="activity_weekly" required>
					<?php 
					$cur_times = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
					?>
					<select  class="form-control" style="width:50%;float:left" name="activity_week_d" required>
						<option value=""><?php echo _l('deallossreasons_option_select'); ?> Day</option>
						<?php foreach($cur_times as $key => $cur_time12){?>
							<option value="<?php echo $key;?>" <?php  if(isset($reminder_settings->act_day) && $reminder_settings->act_day!='' && $reminder_settings->act_day ==$key){ echo 'selected';}?>><?php echo $cur_time12;?></option>
						<?php }?>
					</select>
					<input Type="text" class="form-control timepicker" style="width:49%;float:left;margin-left:1%" name="activity_week_t" id="activity_week_t12" required placeholder ="please select time" value="<?php  if(!empty($reminder_settings->act_date_time)){ echo $reminder_settings->act_date_time;}?>">
				</div>
				<?php $pr_notify = array();
				if(!empty($reminder_settings->pr_notify)){
					$pr_notify = json_decode($reminder_settings->pr_notify);
				}
				$cnt = 0;
				if(in_array("show alert", $pr_notify)){
					$cnt++;
				}
				if(in_array("include summary email", $pr_notify)){
					$cnt++;
				}
				?>
				<div class="form-group" <?php if(empty($req_reminder_type) || !in_array("proposal", $req_reminder_type)){?> style="display:none" <?php }?> id="proposal_reminder">
					<label class="control-label"><h4><?php echo _l('proposal_reminder'); ?></h4></label>
					<div class="checkbox checkbox-info m_left2" >
						<input Type="checkbox" name="proposal_notify[]" value="show alert" onclick="check_prop_tar(this,'proposal_email','proposal_cnt')" id="proposal_reminder_alert" <?php if(in_array("show alert", $pr_notify)){echo 'checked';}?> class="pr_notify"><label class="check_label" ><?php echo _l('show_alert'); ?></label>
						<input type="hidden" id="proposal_cnt" value="<?php echo $cnt;?>">
						<input Type="checkbox" onclick="check_prop_tar(this,'proposal_email','proposal_cnt')" id="proposal_reminder_notify" name="proposal_notify[]" value="include summary email" <?php if(in_array("include summary email", $pr_notify)){echo 'checked';}?> class="pr_notify"><label class="check_label" ><?php echo _l('include_summary_mail'); ?></label>
						<div class="error check_error" id="pr_notify_er" style="display:none">This field is required.</div>
					</div>
				 </div>
				<div class="form-group m_left2 select-placeholder contactid input-group-select" id="proposal_email" <?php if((!in_array("show alert", $pr_notify) && !in_array("include summary email", $pr_notify) ) || (empty($req_reminder_type) || !in_array("proposal", $req_reminder_type))){ ?>style="display:none" <?php }?>>
                  <div class="dropdown bootstrap-select reminder_settings input-group-select show-tick bs3 bs3-has-addon" style="width: 45%;float:left;">
                    <select  class=" selectpicker" name="proposal_mail" data-width="100%" data-none-selected-text="Nothing selected" tabindex="-98" required onchange="check_mail('proposal',this.value)" id="proposal_mail">
                      <option value="daily" <?php if(!empty($reminder_settings->pr_mail) && $reminder_settings->pr_mail == 'daily'){echo 'selected';}?>><?php echo _l('daily'); ?></option>
                      <option value="weekly"  <?php if(!empty($reminder_settings->pr_mail) && $reminder_settings->pr_mail == 'weekly'){echo 'selected';}?>><?php echo _l('weekly'); ?></option>
                      <option value="monthly"  <?php if(!empty($reminder_settings->pr_mail) && $reminder_settings->pr_mail == 'monthly'){ echo 'selected';}?>><?php echo _l('monthly'); ?></option>
                      
                    </select>
                  </div>
                </div>
				<?php $pr_date_time = explode(',',$reminder_settings->pr_date_time);?>
				<div class="form-group m_left2 proposal_mail" style="<?php if((empty($reminder_settings->pr_mail) || $reminder_settings->pr_mail != 'monthly') || (empty($req_reminder_type) || !in_array("proposal", $req_reminder_type))){?>display:none;<?php }?>width: 45%;float:left" id="proposal_monthly">
					<input Type="text" class="form-control datetimepicker" name="proposal_monthly" placeholder ="Please Select Date And Time" value="<?php  if($reminder_settings->pr_mail == 'monthly' && !empty($reminder_settings->pr_date_time) ){ echo $reminder_settings->pr_date_time;}?>" required style="width:48%;float:left;" id="proposal_monthy_t">
					<?php /*<input Type="text" class="form-control timepicker " name="proposal_monthy_t" placeholder ="please select time"  value="<?php  if($reminder_settings->pr_mail == 'monthly' && !empty($reminder_settings->pr_date_time) && !empty($pr_date_time[1])){ echo $pr_date_time[1];}?>"  required style="width:23%;float:left;margin-left:1%">*/?>
					<select  class="form-control" style="width:48%;float:left;margin-left:1%" name="proposal_month_f" required>
						
						<option value="current_month" <?php  if(!empty($reminder_settings->pr_month) && $reminder_settings->pr_month =='current_month'){ echo 'selected';}?>>Current Month</option>
						<option value="next_month" <?php  if(!empty($reminder_settings->pr_month) && $reminder_settings->pr_month =='next_month'){ echo 'selected';}?>>Next Month</option>
						
					</select>
				</div>
				<div class="form-group m_left2 proposal_mail" style="width: 45%;float:left;<?php if((empty($reminder_settings->pr_mail) || $reminder_settings->pr_mail != 'daily') || (empty($req_reminder_type) || !in_array("proposal", $req_reminder_type))){?>display:none;<?php }?>" id="proposal_daily">
					<input Type="text" class="form-control timepicker" name="proposal_daily" placeholder ="Please Select Time" value="<?php  if($reminder_settings->pr_mail == 'daily' && !empty($reminder_settings->pr_date_time)){ echo $reminder_settings->pr_date_time;}?>" required id="proposal_daily_t">
				</div>
				<div class="form-group m_left2 proposal_mail" style="<?php if((empty($reminder_settings->pr_mail) || $reminder_settings->pr_mail != 'weekly') || (empty($req_reminder_type) || !in_array("proposal", $req_reminder_type))){?>display:none;<?php }?>width: 45%;float:left;" id="proposal_weekly">
					<?php 
					$cur_times = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
					?>
					<select  class="form-control" style="width:50%;float:left" name="proposal_week_d" required id="proposal_week_d">
						<option value=""><?php echo _l('deallossreasons_option_select'); ?> Day</option>
						<?php foreach($cur_times as $key => $cur_time12){?>
							<option value="<?php echo $key;?>" <?php  if(!empty($reminder_settings->pr_day) && $reminder_settings->pr_day ==$key){ echo 'selected';}?>><?php echo $cur_time12;?></option>
						<?php }?>
					</select>
					<input Type="text" class="form-control timepicker" style="width:49%;float:left;margin-left:1%"  name="proposal_week_t" placeholder ="Please Select Time"  value="<?php  if(!empty($reminder_settings->pr_date_time)){ echo $reminder_settings->pr_date_time;}?>" required id="proposal_week_t">
				</div>
				<?php $tar_notify = array();
				if(!empty($reminder_settings->tar_notify)){
					$tar_notify = json_decode($reminder_settings->tar_notify);
				}
				$cnt = 0;
				if(in_array("show alert", $tar_notify)){
					$cnt++;
				}
				if(in_array("include summary email", $tar_notify)){
					$cnt++;
				}
				?>
				<div class="form-group" <?php if(empty($req_reminder_type) || !in_array("target", $req_reminder_type)){?> style="display:none" <?php }?> id="target_reminder">
					<label class="control-label"><h4><?php echo _l('target_reminder'); ?></h4></label>
					<div class="checkbox checkbox-info m_left2" >
						<input Type="checkbox"  name="target_notify[]" value="show alert" onclick="check_prop_tar(this,'target_email','target_cnt')" id="target_reminder_alert" <?php if(in_array("show alert", $tar_notify)){echo 'checked';}?>  class="tar_notify"><label class="check_label"><?php echo _l('show_alert'); ?></label>
						<input type="hidden" id="target_cnt" value="<?php echo $cnt;?>">
						<input Type="checkbox" name="target_notify[]" value="include summary email" onclick="check_prop_tar(this,'target_email','target_cnt')" id="target_reminder_notify" <?php if(in_array("include summary email", $tar_notify)){echo 'checked';}?> class="tar_notify"><label class="check_label"  ><?php echo _l('include_summary_mail'); ?></label>
						<div class="error check_error" id="tar_notify_er" style="display:none">This field is required.</div>
					</div>
				 </div>
				<div class="form-group m_left2 select-placeholder contactid input-group-select" id="target_email" <?php if((!in_array("show alert", $tar_notify) && !in_array("include summary email", $tar_notify) ) || (empty($req_reminder_type) || !in_array("target", $req_reminder_type))){ ?>style="display:none" <?php }?>>
                  <div class="dropdown bootstrap-select reminder_settings input-group-select show-tick bs3 bs3-has-addon" style="width: 45%;float:left;">
                    <select  class=" selectpicker"  data-width="100%" data-none-selected-text="Nothing selected" tabindex="-98" required onchange="check_mail('target',this.value)" name="target_mail" id="target_mail">
					<option value="daily" <?php if(!empty($reminder_settings->tar_mail) && $reminder_settings->tar_mail == 'daily'){echo 'selected';}?>><?php echo _l('daily'); ?></option>
                      <option value="weekly"  <?php if(!empty($reminder_settings->tar_mail) && $reminder_settings->tar_mail == 'weekly'){echo 'selected';}?>><?php echo _l('weekly'); ?></option>
                      <option value="monthly"  <?php if(!empty($reminder_settings->tar_mail) && $reminder_settings->tar_mail == 'monthly'){ echo 'selected';}?>><?php echo _l('monthly'); ?></option>
                    </select>
                  </div>
                </div>
				<div class="form-group m_left2 target_mail" style="<?php if((empty($reminder_settings->tar_mail) || $reminder_settings->tar_mail != 'monthly') || (empty($req_reminder_type) || !in_array("target", $req_reminder_type))){?>display:none;<?php }?>width: 45%;float:left" id="target_monthly">
				<?php $tarr_date_time = explode(',',$reminder_settings->tar_date_time);?>
					<input Type="text" class="form-control datetimepicker" name="target_monthy" placeholder ="please select date and time"  value="<?php  if($reminder_settings->tar_mail == 'monthly' && !empty($reminder_settings->tar_date_time)){ echo $reminder_settings->tar_date_time;}?>" id="target_monthy_t" required style="width:48%;float:left">
					
					<?php /*<input Type="text" class="form-control timepicker " name="target_monthy_t" placeholder ="please select time"  value="<?php  if($reminder_settings->tar_mail == 'monthly' && !empty($reminder_settings->tar_date_time) && !empty($tarr_date_time[1])){ echo $tarr_date_time[1];}?>" id="target_monthy_t" required style="width:23%;float:left;margin-left:1%">*/?>
					<select  class="form-control" style="width:48%;float:left;margin-left:1%" name="target_month_f" required>
						
						<option value="current_month" <?php  if(!empty($reminder_settings->tar_month) && $reminder_settings->tar_month =='current_month'){ echo 'selected';}?>>Current Month</option>
						<option value="next_month" <?php  if(!empty($reminder_settings->tar_month) && $reminder_settings->tar_month =='next_month'){ echo 'selected';}?>>Next Month</option>
						
					</select>
				</div>
				<div class="form-group m_left2 target_mail" style="width: 45%;float:left;<?php if((empty($reminder_settings->tar_mail) || $reminder_settings->tar_mail != 'daily') || (empty($req_reminder_type) || !in_array("target", $req_reminder_type))){?>display:none;<?php }?>" id="target_daily">
					<input Type="text" class="form-control timepicker" name="target_daily" placeholder ="Please Select Time"  value="<?php  if(!empty($reminder_settings->tar_date_time)){ echo $reminder_settings->tar_date_time;}?>" id="target_daily_t" required>
				</div>
				<div class="form-group m_left2 target_mail" style="<?php if(empty($reminder_settings->tar_mail) || $reminder_settings->tar_mail != 'weekly'){?>display:none;<?php }?>width: 45%;float:left;" id="target_weekly">
					<?php 
					$cur_times = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
					?>
					<select  class="form-control" style="width:50%;float:left" name="target_weekly_d" id="target_weekly_d" required>
						<option value=""><?php echo _l('deallossreasons_option_select'); ?> Day</option>
						<?php foreach($cur_times as $key => $cur_time12){?>
							<option value="<?php echo $key;?>" <?php  if(!empty($reminder_settings->tar_day) && $reminder_settings->tar_day ==$key){ echo 'selected';}?>><?php echo $cur_time12;?></option>
						<?php }?>
					</select>
					<input Type="text" class="form-control timepicker" style="width:49%;float:left;margin-left:1%" name="target_weekly_t" placeholder ="Please Select Time"  value="<?php  if(!empty($reminder_settings->tar_date_time)){ echo $reminder_settings->tar_date_time;}?>" id="target_weekly_t" required>
				</div>
              </div>
			  
              <div class="col-md-6" style="margin-top:20px;">
                <button type="submit" class="btn btn-primary" name="reminder_save"><?php echo _l('save'); ?></button>
              </div>
              <?php echo form_close(); ?>
				</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
<?php init_tail(); ?>
<style>
.error{
	color:#F3031C;

}
.check_label{
	margin-right:2%;
}
.m_left2{
	margin-left:2%;
}
label#customer_d-error {
    position: absolute;
    float: left;
    left: 3%;
	 margin-top: 3%;
}
label#customer_h-error {
    position: absolute;
    float: left;
    left: 33%;
	 margin-top: 3%;
}
label#customer_m-error {
    position: absolute;
    float: left;
    left: 63%;
	 margin-top: 3%;
}
label#activity_alert_d-error {
    position: absolute;
    float: left;
    left: 3%;
	 margin-top: 3%;
}
label#activity_alert_h-error {
    position: absolute;
    float: left;
    left: 33%;
	 margin-top: 3%;
}
label#activity_alert_m-error {
    position: absolute;
    float: left;
    left: 63%;
	 margin-top: 3%;
}
label#activity_week_d-error {
    position: absolute;
}
label#activity_week_t-error {
    position: absolute;
    float: left;
    left: 48%;
	 margin-top: 3%;
}
label#reminder_settings-error {
    position: absolute;
    margin-top: 3%;
    padding-top: 5px;
}
label#proposal_week_d-error {
    position: absolute;
    float: left;
    margin-top: 3%;
    left: 48%;
}
label#proposal_week_t-error {
    position: absolute;
    margin-top: 3%;
    float: left;
    left: 70%;
}
label#target_weekly_d-error {
    position: absolute;
    float: left;
    left: 48%;
    margin-top: 3%;
}
label#target_weekly_t-error {
    position: absolute;
    left: 70%;
    margin-top: 3%;
}
#activity_reminder{
	margin-top:30px !important;
}
#proposal_reminder{
	margin-top:30px !important;
}
#target_reminder{
	margin-top:30px !important;
}
</style>
<script>
// just for the demos, avoids form submit
$( "#reminder_form" ).validate({});
function ch_rem_seting(a){
	if(a.value=='disable'){
		$('#ch_company').hide();
		$('#type_reminder').hide();
		$('#activity_reminder').hide();
		$('#activity_alert').hide();
		$('#activity_email').hide();
		$('#activity_monthly').hide();
		$('#activity_daily').hide();
		$('#activity_weekly').hide();
		$('#proposal_reminder').hide();
		$('#proposal_email').hide();
		$('#proposal_monthly').hide();
		$('#proposal_daily').hide();
		$('#proposal_weekly').hide();
		$('#target_reminder').hide();
		$('#target_email').hide();
		$('#target_daily').hide();
		$('#target_monthly').hide();
		$('#target_weekly').hide();
		$('#reminder_settings').val('');
		$('.selectpicker').selectpicker('refresh');
		$('.check_rem_type').prop('checked', false);
		$('#reminder_settings-error').hide();
	}
	else{
		$('#reminder_settings').val('');
		$('.selectpicker').selectpicker('refresh');
		$('.check_rem_type').prop('checked', false);
		$('#reminder_settings-error').hide();
		var ch_type = $('#ch_type').val();
		if(ch_type == 'company'){
			$('#ch_company').show();
		}
		else{
			$('#type_reminder').show();
		}
	}
}
function check_reminder(a){
	$('#type_reminder').hide();
	$('#customer_reminder').hide(); 
	$('#activity_reminder').hide();
	$('#activity_alert').hide();
	$('#activity_email').hide();
	$('#activity_monthly').hide();
	$('#activity_daily').hide();
	$('#activity_weekly').hide();
	$('#proposal_reminder').hide();
	$('#proposal_email').hide();
	$('#proposal_monthly').hide();
	$('#proposal_daily').hide();
	$('#proposal_weekly').hide();
	$('#target_reminder').hide();
	$('#target_email').hide();
	$('#target_daily').hide();
	$('#target_monthly').hide();
	$('#target_weekly').hide();
	if(a == 'company'){
		$('#type_reminder').show();
	}
	else{
		$('.check_rem_type').prop('checked', false);
	}
	if(a!=''){
		$('#reminder_settings-error').hide();
	}
}
function check_activity(a,b){
	var cur_type  = a.value;
	$('#'+b).hide();
	if(b == 'activity_email'){
		$('select[name^="activity_mail"] option[value="daily"]').attr("selected","selected");
	}
	$('#'+cur_type+'_daily').hide();
	$('#'+cur_type+'_monthly').hide();
	$('#'+cur_type+'_weekly').hide();
	$('#'+cur_type+'_email').hide();
	$('#'+b+'_alert').prop('checked', false);
	$('#'+b+'_notify').prop('checked', false);
	$('#'+b+'_individual').prop('checked', false);
	
	if(a.checked){
		$('#'+b).show();
		if(b=='activity_email'){
			$('#activity_daily').show();
			$('#activity_email').show();
		}
		$('#act_notify_er').hide();
	}
	else{
		if(b=='activity_email'){
			$('#activity_daily').hide();
			$('#activity_email').hide();
			$('#activity_monthly').hide();
			$('#activity_weekly').hide();
			$('#activity_mail').val('daily');
			$('#activity_daily12').val('');
			$('#activity_monthy_t').val('');
			$('#activity_week_t12').val('');
		}
		else if( b == 'activity_alert'){
			var notify_id = $('.ch_act_alert:checkbox:checked').length > 0;
			if(notify_id == false){
				$('#activity_alert').hide();
			}
			else{
				$('#activity_alert').show();
			}
		}
		else if(b=='activity_reminder'){
			$('#activity_daily').hide();
			$('#activity_monthly').hide();
			$('#activity_weekly').hide();
			$('#activity_email').hide();
			$('#activity_alert').hide();
			$('#activity_mail').val('daily');
			$('#activity_daily12').val('');
			$('#activity_monthy_t').val('');
			$('#activity_week_t12').val('');
		}
	}
	$('.selectpicker').selectpicker('refresh');
	var rem_type = 0;
	 $("input:checkbox[class=check_rem_type]:checked").each(function () {
		rem_type = 1;
	});
	if(rem_type == 1){
		$('#rem_type_er').hide();
	}
	
}
function check_mail(a,b){
	$('.'+a+'_mail').hide();
	if(a=='activity'){
		$('#'+a+'_week_t12').val('');
	}else{
		$('#'+a+'_weekly_t').val('');
	}
	
	
	$('#'+a+'_'+b).show();
}
function check_prop_tar(a,b,c){
	var cnt = parseInt($('#'+c).val());
	if(a.checked){
		cnt++;
		$('#'+c).val(cnt);
	}
	else{
		cnt--;
		$('#'+c).val(cnt);
	}
	var req_val = parseInt($('#'+c).val());
	
	if(req_val>0){
		$('#'+b).show();
		if(b == 'target_email'){
			$('#tar_notify_er').hide();
		}
	}
	else{
		$('#'+b).hide();
	}
	if(a.checked){
		if(c == 'proposal_cnt'){
			if(req_val==1){
				$('#proposal_mail').val('daily');
				$('#proposal_daily').show();
			}
			$('#proposal_email').show();
			$('.selectpicker').selectpicker('refresh');
			$('#pr_notify_er').hide();
		}
		else{
			if(req_val==1){
				$('#target_daily').show();
				$('#target_mail').val('daily');
			}
			('#target_email').show();
			$('.selectpicker').selectpicker('refresh');
			$('#tar_notify_er').hide();
		}
	}
	else{
		if(req_val==0){
		if(c == 'proposal_cnt'){
			$('#proposal_email').hide();
			$('#proposal_monthly').hide();
			$('#proposal_weekly').hide();
			$('#proposal_daily').hide();
			$('#proposal_mail').val('daily');
			$('#proposal_daily_t').val('');
			$('#proposal_week_t').val('');
			$('#proposal_monthy_t').val('');
			$('.selectpicker').selectpicker('refresh');

		}
		else{
			$('#target_email').hide();
			$('#target_daily').hide();
			$('#target_monthly').hide();
			$('#target_weekly').hide();
			$('#target_mail').val('daily');
			$('#target_daily_t').val('');
			$('#target_weekly_t').val('');
			$('#target_monthy_t').val('');
			$('.selectpicker').selectpicker('refresh');
		}
		}

	}
}
<?php /*if($cur_setting == 'user'){?>
	function validate_remind_from(){
		$('.check_error').hide();
		var remind_setting = $('input[name="remind_status"]:checked').val();;
		if(remind_setting == 'disable'){
			$('#rem_set_er').hide();
			return true;
		}
		var rem_type = 0;
		 $("input:checkbox[class=check_rem_type]:checked").each(function () {
			rem_type = 1;
		});
		if(rem_type == 0){
			$('#rem_type_er').show();
			return false;
		}
		else{
			 var actchecked = $("#act_rem_type").is(":checked");
			if (actchecked) {
				var act_notify = 0;
				$("input:checkbox[class=activity_notify]:checked").each(function () {
					 act_notify = 1;
				});
				if(act_notify == 0){
					$('#act_notify_er').show();
					return false;
				}
			}
			var prchecked = $("#pr_rem_type").is(":checked");
			if (prchecked) {
				var pr_notify = 0;
				$("input:checkbox[class=pr_notify]:checked").each(function () {
					 pr_notify = 1;
				});
				if(pr_notify == 0){
					$('#pr_notify_er').show();
					return false;
				}
			}
			var tarchecked = $("#tar_rem_type").is(":checked");
			if (tarchecked) {
				var tar_notify = 0;
				$("input:checkbox[class=tar_notify]:checked").each(function () {
					 tar_notify = 1;
				});
				if(tar_notify == 0){
					$('#tar_notify_er').show();
					return false;
				}
			}
		}
		return true;
	}
<?php }else{*/?>
	function validate_remind_from(){
		var ch_type = $('#ch_type').val();
		if(ch_type == 'user'){
			var remind_val = 'company';
		}else{
			var remind_val     = $('#reminder_settings').val();
		}
		var remind_setting = $('input[name="remind_status"]:checked').val();;
		$('.check_error').hide();
		if(remind_setting != ''){
			$('#rem_set_er').hide();
		}
		else{
			$('#rem_set_er').show();
			return false;
		}
		if(remind_val == 'company'){
			if(remind_setting == 'disable'){
				$('#rem_set_er').hide();
				return true;
			}
			var rem_type = 0;
			 $("input:checkbox[class=check_rem_type]:checked").each(function () {
				rem_type = 1;
			});
			if(rem_type == 0){
				$('#rem_type_er').show();
				return false;
			}
			else{
				 var actchecked = $("#act_rem_type").is(":checked");
				if (actchecked) {
					var act_notify = $('.activity_notify:checkbox:checked').length > 0;
					if(act_notify == false){
						$('#act_notify_er').show();
						return false;
					}
				}
				var prchecked = $("#pr_rem_type").is(":checked");
				if (prchecked) {
					var pr_notify = 0;
					$("input:checkbox[class=pr_notify]:checked").each(function () {
						 pr_notify = 1;
					});
					if(pr_notify == 0){
						$('#pr_notify_er').show();
						return false;
					}
				}
				var tarchecked = $("#tar_rem_type").is(":checked");
				if (tarchecked) {
					var tar_notify = 0;
					$("input:checkbox[class=tar_notify]:checked").each(function () {
						 tar_notify = 1;
					});
					if(tar_notify == 0){
						$('#tar_notify_er').show();
						return false;
					}
				}
			}
		}
		return true;
	}
<?php //}?>

	/*$('#activity_monthy_t').datepicker( { changeYear: false, dateFormat: 'dd-mm' } );
	$('#proposal_monthy_t').datepicker( { changeYear: false, dateFormat: 'dd-mm' } );
	$('#target_monthy_t').datepicker( { changeYear: false, dateFormat: 'dd-mm' } );*/
</script>
<style>
span.ui-datepicker-year { display:none }
</style>
</body>
</html>
