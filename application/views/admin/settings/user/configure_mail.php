<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content"> 
  <?php echo form_open('admin/outlook_mail/connect_mail',array('id'=>'email_int_group_modal'));?>
    <div class="row">
     <?php if ($this->session->flashdata('debug')) {
        ?>
       <div class="col-lg-12">
        <div class="alert alert-warning">
         <?php echo $this->session->flashdata('debug'); ?>
       </div>
     </div>
   <?php
    } ?>
  
  <div class="col-md-12">
    <div class="panel_s">
     <div class="panel-body">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active">
				<a href="#email_config" aria-controls="email_config" role="tab" data-toggle="tab">Email Sync</a>
			</li>
			
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="email_config">
					<?php 
					$cur_token = get_outlook_token();
					$user_email = '';
					if(!empty($cur_token->email)){
						$user_email = $cur_token->email;
					}
					$folders = array();
					if(!empty($cur_token->folders)){
						$folders = json_decode($cur_token->folders);
					}
					//echo form_open('admin/outlook_mail/connect_mail',array('id'=>'email_int_group_modal')); ?>
					<div class="modal-body">
						<div id="overlay5" style="display: none;"><div class="spinner"></div></div>
						<div class="row">
							<div class="col-md-12 form-group ">
							<?php $attrs = array('autofocus'=>true, 'required'=>true); ?>
							<?php echo render_input( 'connect_email', 'connect_email_addr',$user_email,'text',$attrs); ?>
							<div id="email_er_data" class=""></div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 form-group ">
								<label class="control-label" style="font-weight:500; margin-right:10px;"><small class="req text-danger">* </small><?php echo _l('filter_by_folder'); ?></label>
								<div class="radio radio-primary radio-inline">
									<input Type="radio" name="syn_folder" onchange="check_folder(this.value)" value="all" <?php if($cur_token->folder_type == 'all'){echo 'checked';}?>><label class=""><?php echo _l('syn_all_folder'); ?></label>
								</div>
								<div class="radio radio-primary radio-inline">
									<input Type="radio" name="syn_folder" value="specific"  onchange="check_folder(this.value)" <?php if(empty($cur_token->folder_type) || $cur_token->folder_type == 'specific'){echo 'checked';}?>><label class="" ><?php echo _l('syn_mail_spec_folder'); ?></label>
								</div>
							</div>
						</div>
						<div class="row" id="spec_flder" <?php if($cur_token->folder_type == 'all'){ ?>style="display:none" <?php }?>>
							<div class="col-md-12 form-group ">
								<div class="checkbox1 checkbox-info1" style="margin-left:30px;" >
									<input Type="checkbox" name="mail_folder[]" value="Inbox" checked disabled ><label class="check_label"><?php echo _l('inbox'); ?></label>
									<input Type="checkbox" name="mail_folder[]" value="Archive" <?php if(!empty($folders) && in_array('Archive',$folders)){echo 'checked';}?> ><label class="check_label" ><?php echo _l('archive'); ?></label>
									<input Type="checkbox" name="mail_folder[]" value="Conversation History"  class="check_rem_type" <?php if(!empty($folders) && in_array('Conversation History',$folders)){echo 'checked';}?>><label class="check_label"><?php echo _l('conversation_history'); ?></label>
									<input Type="checkbox" name="mail_folder[]" value="Outbox" <?php if(!empty($folders) && in_array('outbox',$folders)){echo 'checked';}?>><label class="check_label"><?php echo _l('outbox'); ?></label>
									<input Type="checkbox" name="mail_folder[]" value="Sent Items" <?php if(!empty($folders) && in_array('Sent Items',$folders)){echo 'checked';}?>><label class="check_label"><?php echo _l('sent_items'); ?></label>
									
								</div>
							</div>
						</div>
						<?php
						$yesterday = date("d-m-Y", strtotime("-1 days"));
						$week = date("d-m-Y", strtotime("-7 days"));
						$month_1 = $req_day = date("d-m-Y", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) );
						$month_3 = $req_day = date("d-m-Y", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-3 month" ) );
						$month_6 = $req_day = date("d-m-Y", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-6 month" ) );
						$year = date("d-m-Y", strtotime("-365 days"));
						?>
						<div class="row">
							<div class="col-md-12 form-group ">
								<label for="smtp_host"><small class="req text-danger">* </small>Sync Past Email </label><br />
								<select  name="sync_emails" class="selectpicker" data-width="100%">
									<option value="yesterday" <?php if($cur_token->filter_mail == 'yesterday'){echo 'selected';}?> >Yesterday (<?php echo $yesterday;?>) </option>
									<option value="yesterday" <?php if($cur_token->filter_mail == 'yesterday'){echo 'selected';}?> >Yesterday (<?php echo $yesterday;?>) </option>
									<option value="week" <?php if($cur_token->filter_mail == 'week'){echo 'selected';}?> >1 Week (<?php echo $week;?>)</option>
									<option value="month" <?php if($cur_token->filter_mail == 'month'){echo 'selected';}?> >1 Month (<?php echo $month_1;?>)</option>
									<option value="3 months" <?php if($cur_token->filter_mail == '3 months'){echo 'selected';}?> >3 Months (<?php echo $month_3;?>)</option>
									<option value="6 months" <?php if($cur_token->filter_mail == '6 months'){echo 'selected';}?> >6 Months (<?php echo $month_6;?>)</option>
									<option value="year" <?php if($cur_token->filter_mail == 'year'){echo 'selected';}?> >1 Year (<?php echo $year;?>)</option>
								</select>
							</div>
						</div>
					</div>
					
				</div>
			</div>
				<div class="btn-bottom-toolbar text-right">
          <button type="submit" class="btn btn-info" name="submit_save"><?php echo _l('connect_email'); ?></button>
        </div>
		</div>
    </div>
  </div>
</div>
<div class="clearfix"></div>
</div>
<?php echo form_close(); ?>
<div class="btn-bottom-pusher"></div>
</div>
</div>
<div id="new_version"></div>
<?php init_tail(); ;?>

<?php hooks()->do_action('settings_tab_footer', 'email'); ?>
<style>
label.check_label {
    margin-left: 30px;
}
input[type="checkbox"] {
    margin-left: 5px;
}
label.check_label {
    margin-left: 5px;
}
label.error {
    color: red;
    /* clear: both; */
    position: absolute;
    margin-top: 22px;
    float: left;
    margin-left: -15px;
}
</style>
<script>
function check_folder(a){
	$('#spec_flder').hide();
	if(a=='specific'){
		$('#spec_flder').show();
	}
}

</script>
</body>
</html>