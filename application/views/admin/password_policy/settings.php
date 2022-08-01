<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open($this->uri->uri_string(),['id'=>'passwordPolicyForm']); ?>
	
						<!-- enable disable start -->
						<div class="form-group">
							<label for="rtl_support_admin" class="control-label clearfix">Enable password policy</label>
							<div class="radio radio-primary radio-inline">
								<input type="radio" id="enable_password_policy" name="enable_password_policy" value="1" <?php echo set_value('enable_password_policy')==1?'checked=""':''?>>
								<label for="enable_password_policy">Yes</label>
							</div>
							<div class="radio radio-primary radio-inline">
								<input type="radio" id="enable_password_policy" name="enable_password_policy" value="0" <?php echo set_value('enable_password_policy')==0?'checked=""':''?>>
								<label for="enable_password_policy">No</label>
							</div>
						</div>
						<!-- enable disable end -->

						<!-- password policy settings start -->
						<div id="password_policy_settigns" <?php echo set_value('enable_password_policy')==0?'style="display:none"':''?>>
							 <div class="">
								<hr class="hr-panel-heading" />
							 </div>
							<div class="row">
								<div class="form-group col-md-12">
									<label for="rtl_support_admin" class="control-label clearfix">First time change password</label>
									<div class="radio radio-primary radio-inline">
										<input type="radio" id="first_time_change_pass" name="first_time_change_pass" value="1" <?php echo set_value('first_time_change_pass')==1?'checked=""':''?>>
										<label for="first_time_change_pass">Yes</label>
									</div>
									<div class="radio radio-primary radio-inline">
										<input type="radio" id="first_time_change_pass" name="first_time_change_pass" value="0" <?php echo set_value('first_time_change_pass')==0?'checked=""':''?>>
										<label for="first_time_change_pass">No</label>
									</div>
								</div>
							</div>
							<div class="row">
								<?php $attrs =['min'=>1];
								?>
								<?php echo render_input('pass_change_period','password_change_period',set_value('password_change_period'),'number',$attrs,[],'col-md-6 clearfix'); ?>
								<?php 
									$attrs =['min'=>1]; 
									if(set_value('pass_change_period') ==''){
										$attrs['disabled'] ='disabled';
									}
									
								?>
								<?php echo render_input('pass_history','password_history',set_value('password_history'),'number',$attrs,[],'col-md-6 clearfix'); ?>
							</div>
							<div class="row">
								<?php $attrs =['min'=>1]; ?>
								<?php echo render_input('lock_invalid_attempt','lock_for_invalid_attempt',set_value('lock_invalid_attempt'),'number',$attrs,[],'col-md-6 clearfix'); ?>
								<?php 
									$attrs =['min'=>1]; 
									if(set_value('lock_invalid_attempt') ==''){
										$attrs['disabled'] ='disabled';
									}else{
										$attrs['required'] ='required';
									}
									
								?>
								<?php echo render_input('lock_auto_release','auto_release_for_lock',set_value('lock_auto_release'),'number',$attrs,[],'col-md-6 clearfix'); ?>
							</div>
							<div class="row">
								<div class="form-group col-md-12">
									<label for="rtl_support_admin" class="control-label clearfix">Password Strength</label>
									<div class="radio radio-primary radio-inline">
										<input type="radio" id="password_strength_low" name="password_strength" value="low" <?php echo set_value('password_strength')=='low'?'checked=""':''?>>
										<label for="password_strength_low">Low</label>
									</div>
									<div class="radio radio-primary radio-inline">
										<input type="radio" id="password_strength_medium" name="password_strength" value="medium" <?php echo set_value('password_strength')=='medium'?'checked=""':''?>>
										<label for="password_strength_medium">Medium</label>
									</div>
									<div class="radio radio-primary radio-inline">
										<input type="radio" id="password_strength_high" name="password_strength" value="high" <?php echo set_value('password_strength')=='high'?'checked=""':''?>>
										<label for="password_strength_high">High</label>
									</div>
								</div>
								<?php $attrs =['min'=>1]; ?>
								<?php echo render_input('password_min_length','password_minimum_length',set_value('password_min_length'),'number',$attrs,[],'col-md-6 clearfix'); ?>
								<?php $attrs =['min'=>1]; ?>
								<?php echo render_input('password_max_length','password_maximum_length',set_value('password_max_length'),'number',$attrs,[],'col-md-6 clearfix'); ?>
							</div>
						</div>
						<!-- password policy settings end -->

						<button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
						<?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
	$.validator.addMethod("greaterThan",
		function (value, element, param) {
			var $otherElement = $(param);
			return ($otherElement.val() === '' || value === '' || parseInt(value, 10) > parseInt($otherElement.val(), 10));
		},'Password maximum length should be greater than password minimum length'
	);
	appValidateForm('#passwordPolicyForm',{
		pass_change_period: {
			digits: true
		},
		pass_history: {
			digits: true
		},
		lock_invalid_attempt: {
			digits: true
		},
		lock_auto_release: {
			digits: true
		},
		password_min_length: {
			required:true,
			digits: true
		},
		password_max_length: {
			digits: true,
			greaterThan: "#password_min_length"
		},
    });
	
	$('input[type=radio][name=password_strength]').change(function() {
		if (this.value == 'low') {
			$("#password_min_length").attr('min',1);
		}
		else if (this.value == 'medium') {
			$("#password_min_length").attr('min',2);
		}else if (this.value == 'high') {
			$("#password_min_length").attr('min',4);
		}
		$("#passwordPolicyForm").validate().element("#password_min_length");
	});
	
	$('input[type=radio][name=enable_password_policy]').change(function() {
		if (this.value == '1') {
			$("#password_policy_settigns").show();
			$("#passwordPolicyForm").validate().settings.ignore = ":hidden";
		}
		else if (this.value == '0') {
			$("#password_policy_settigns").hide();
			$("#passwordPolicyForm").validate().settings.ignore = "*";
		}
	});

	$("#lock_invalid_attempt").keyup(function(){
		if($(this).val() >0){
			$("#lock_auto_release").removeAttr('disabled');
			$("#lock_auto_release").attr('required','required');
		}else{
			$("#lock_auto_release").val('');
			$("#lock_auto_release").attr('disabled','disabled');
			$("#lock_auto_release").removeAttr('required');
			$("#lock_auto_release").attr('aria-invalid','false');
			$("#lock_auto_release").parent().removeClass('has-error');
			$("#lock_auto_release-error").remove();
		}
	});

	$("#pass_change_period").keyup(function(){
		if($(this).val() >0){
			$("#pass_history").removeAttr('disabled');
		}else{
			$("#pass_history").val('');
			$("#pass_history").attr('disabled','disabled');
		}
	});

	$("#password_min_length").keyup(function(){
		$("#passwordPolicyForm").validate().element("#password_max_length");
	});

</script>
</body>
</html>
