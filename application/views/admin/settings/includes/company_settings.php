<?php defined('BASEPATH') or exit('No direct script access allowed');
$ch_mail_server = get_option('company_mail_server');
 ?>
<div class="form-group mtop15">

	<label for="smtp_host">Connect mail with security (IMAP)</label><br />
	<select id="connect_mail" name="connect_mail" class="selectpicker" data-width="100%" onchange="connect_mail1(this)">
		<option value="" ><?php echo _l('smtp_encryption_none'); ?></option>
		<option value="yes" <?php if(get_option('connect_mail') =='yes'){echo 'selected';}?>>Yes</option>
		<option value="no" <?php if(get_option('connect_mail')=='no'){echo 'selected';}?>>No</option>
	</select>
</div>
<div class="" <?php if(get_option('connect_mail')=='yes'){?> style="display:none"<?php }?> id="email_setting">
	<a href="<?php echo base_url().'admin/company_mail/configure_email';?>" >Click Here To Go Email Settings (for connect email)</a>
</div>
<div class="form-group mtop15" id="mail_server_setting" <?php if(get_option('connect_mail')=='no'){?> style="display:none"<?php }?>>
	<label for="smtp_host">Mail Server Setting</label><br />
	<select id="mail_server" name="mail_server" class="selectpicker" data-width="100%" onchange="mail_server1(this)">
		<option value="" ><?php echo _l('smtp_encryption_none'); ?></option>
		<option value="yes" <?php if(!empty($ch_mail_server) && $ch_mail_server=='yes'){echo 'selected';}?>>Company</option>
		<option value="no" <?php if(!empty($ch_mail_server) && $ch_mail_server=='no'){echo 'selected';}?>>Personal</option>

	</select>
</div>
	<div id="have_server" <?php if(empty($ch_mail_server) || $ch_mail_server!='yes'){?>style="display:none"<?php }?>>
	<!-- <ul class="nav nav-tabs" role="tablist">
		<li role="presentation" id="imap_li" class="active" <?php if(get_option('connect_mail')=='no'){?> style="display:none"<?php }?>>
			<a href="#email_queue" aria-controls="email_queue" role="tab" data-toggle="tab" id="imap_queue" onclick="act_tab('email_queue','email_config');"><?php echo _l('settings_imap_settings_heading'); ?></a>
		</li>
	</ul> -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="email_queue" <?php if(get_option('connect_mail')=='no'){?> style="display:none"<?php }?>>

			<div class="form-group mtop15">
				<div class="row">
					<div class="col-md-6">
						<label for="smtp_host">Mail Server</label><br />
						<select id="imapserver" class="selectpicker" data-width="100%" name="company_imap_server">
							<option value="" <?php if(get_option('company_imap_server') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
							<option value="gmail" <?php if(get_option('company_imap_server') == 'gmail'){echo 'selected';} ?>>Gmail</option>
							<option value="yahoo" <?php if(get_option('company_imap_server') == 'yahoo'){echo 'selected';} ?>>Yahoo</option>
							<option value="outlook" <?php if(get_option('company_imap_server') == 'outlook'){echo 'selected';} ?>>Outlook</option>
							<option value="others" <?php if(get_option('company_imap_server') == 'others'){echo 'selected';} ?>>Others</option>
						</select>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<div class="col-md-12">
						<p class="text-muted">Incoming Server ( IMAP )</p>
					</div>
					<div class="col-md-6">
						<div class="imaphost">
							<label for="imap_host"><?php echo _l('imap_host'); ?></label><br />
							<input type="text" name="imap_host" id="imap_host" class="form-control" value="<?php echo get_option('company_imap_host'); ?>" readonly>
						</div>
						<div class="imapother">
							<label for="imap_host"><?php echo _l('imap_host'); ?></label><br />
							<input type="text" name="imap_host" id="imap_host" class="form-control" value="<?php echo get_option('company_imap_host'); ?>">
						</div>
					</div>
					<div class="col-md-3">
						<div class="imapgmail">
							<label for="imap_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
							<select name="imap_encryption" id="imap_encryption" class="selectpicker" data-width="100%">
								<option value="" <?php if(get_option('company_imap_encryption') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
								<option value="ssl" <?php if(get_option('company_imap_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
							</select>
						</div>

						<div class="imapyahoo">
							<label for="imap_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
							<select name="imap_encryption" id="imap_encryption" class="selectpicker" data-width="100%">
								<option value="" <?php if(get_option('company_imap_encryption') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
								<option value="ssl" <?php if(get_option('company_imap_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
								<option value="tls" <?php if(get_option('company_imap_encryption') == 'tls'){echo 'selected';} ?>>TLS</option>
								
							</select>
						</div>

						<div class="imapoutlook">
							<label for="imap_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
							<select name="imap_encryption" id="imap_encryption" class="selectpicker" data-width="100%" onchange="check_encryption()">
								<option value="" <?php if(get_option('company_imap_encryption') == ''){echo 'selected';} ?> ><?php echo _l('smtp_encryption_none'); ?></option>
								<option value="ssl" <?php if(get_option('company_imap_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
								<option value="tls" <?php if(get_option('company_imap_encryption') == 'tls'){echo 'selected';} ?>>TLS</option>
								<option value="STARTTLS" <?php if(get_option('company_imap_encryption') == 'STARTTLS'){echo 'selected';} ?>>STARTTLS</option>
							</select>
						</div>
						<div class="imapother">
							<label for="imap_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
							<input type="text" name="imap_encryption" id="imap_encryption" class="form-control" value="<?php echo get_option('company_imap_encryption'); ?>">
						</div>
					</div>
					<div class="col-md-3">
						<div class="imapother">
							<label for="imap_port"><?php echo _l('imap_port'); ?></label><br />
							<input type="text" name="imap_port" id="imap_port" class="form-control" value="<?php echo get_option('company_imap_port'); ?>">
						</div>
						<div class="imapport">
							<label for="imap_port"><?php echo _l('imap_port'); ?></label><br />
							<input type="text" name="imap_port" id="imap_port" class="form-control" value="<?php echo get_option('company_imap_port'); ?>" >
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="row">
					<div class="col-md-12">
						<p class="text-muted">Outgoing Server ( SMTP )</p>
					</div>
					<div class="col-md-6">
						<div class="smtphost">
							<label for="smtp_host"><?php echo _l('smtp_host'); ?></label><br />
							<input type="text" name="smtp_host" id="smtp_host" class="form-control" value="<?php echo get_option('company_smtp_global_host'); ?>" readonly>
						</div>
						<div class="smtpother">
							<label for="smtp_host"><?php echo _l('smtp_host'); ?></label><br />
							<input type="text" name="smtp_host" id="smtp_host" class="form-control" value="<?php echo get_option('company_smtp_global_host'); ?>">
						</div>
					</div>
					<div class="col-md-3">
						<div class="smtpgmail">
							<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
							<select name="smtp_encryption" id="smtp_encryption" class="selectpicker" data-width="100%">
								<option value="" <?php if(get_option('company_smtp_global_encryption') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
								<option value="ssl" <?php if(get_option('company_smtp_global_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
							</select>
						</div>

						<div class="smtpyahoo">
							<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
							<select name="smtp_encryption" id="smtp_encryption" class="selectpicker" data-width="100%">
								<option value="" <?php if(get_option('company_smtp_global_encryption') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
								<option value="ssl" <?php if(get_option('company_smtp_global_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
								<option value="tls" <?php if(get_option('company_smtp_global_encryption') == 'tls'){echo 'selected';} ?>>TLS</option>
								
							</select>
						</div>

						<div class="smtpoutlook">
							<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
							<select name="smtp_encryption" id="smtp_encryption" class="selectpicker" data-width="100%" onchange="check_encryption()">
								<option value="" <?php if(get_option('company_smtp_global_encryption') == ''){echo 'selected';} ?> ><?php echo _l('smtp_encryption_none'); ?></option>
								<option value="ssl" <?php if(get_option('company_smtp_global_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
								<option value="tls" <?php if(get_option('company_smtp_global_encryption') == 'tls'){echo 'selected';} ?>>TLS</option>
								<option value="STARTTLS" <?php if(get_option('company_smtp_global_encryption') == 'STARTTLS'){echo 'selected';} ?>>STARTTLS</option>
							</select>
						</div>
						<div class="smtpother">
							<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
							<input type="text" name="smtp_encryption" id="smtp_encryption" class="form-control" value="<?php echo get_option('company_smtp_global_encryption'); ?>">
						</div>
					</div>
					<div class="col-md-3">
						<div class="smtpother">
							<label for="smtp_port"><?php echo _l('smtp_port'); ?></label><br />
							<input type="text" name="smtp_port" id="smtp_port" class="form-control" value="<?php echo get_option('company_smtp_global_port'); ?>">
						</div>
						<div class="smtpport">
							<label for="smtp_port"><?php echo _l('smtp_port'); ?></label><br />
							<input type="text" name="smtp_port" id="smtp_port" class="form-control" value="<?php echo get_option('company_smtp_global_port'); ?>" >
						</div>
					</div>
				</div>
			</div>
	</div>
	</div>
</div>
<script >
function act_tab(a,b){
	$('#'+a).show();
	$('#'+b).hide();
}
function mail_server1(a){
	$('#have_server').hide();
	var b = $('#connect_mail').val();
	if(a.value=='yes' && b == 'yes'){
		$('#have_server').show();
	}
	$('#email_config').hide();
	$('#email_queue').show();
}
function connect_mail1(a){
	$('#have_server').hide();
	var b = $('#mail_server').val();
	$('#imap_queue').hide();
	$('#email_queue').hide();
	$('#email_config').hide();
	$('#imap_li').hide();
	//$('#smtp_li').addClass('active');
	$('#imap_li').addClass('active');
	//$('#email_config').addClass('active');
	$('#mail_server_setting').hide();
	$('#email_setting').hide();
	$('#email_queue').addClass('active');
	if(a.value=='yes'){
		$('#imap_queue').show();
		$('#imap_li').show();
		//$('#email_queue').show();
		//$('#email_config').hide();
		$('#email_queue').show();
		if(b == 'yes'){
			$('#have_server').show();
		}
		$('#mail_server_setting').show();
	
	}else{
		$('#email_setting').show();
		$('#email_config').hide();
		$('#email_queue').hide();
		
	}
}
function check_encryption(){
	$('#imap_port').val('993');
}
</script>
