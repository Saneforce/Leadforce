<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<ul class="nav nav-tabs" role="tablist">
	<li role="presentation" class="active">
		<a href="#email_config" aria-controls="email_config" role="tab" data-toggle="tab"><?php echo _l('settings_smtp_settings_heading'); ?></a>
	</li>
	<li role="presentation">
		<a href="#email_queue" aria-controls="email_queue" role="tab" data-toggle="tab"><?php echo _l('settings_imap_settings_heading'); ?></a>
	</li>
</ul>
<div class="tab-content">
	<div role="tabpanel" class="tab-pane active" id="email_config">
		<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
		<!-- <input type="text" class="fake-autofill-field" name="fakeusernameremembered" value='' tabindex="-1" />
		<input type="password" class="fake-autofill-field" name="fakepasswordremembered" value='' tabindex="-1" /> -->
		<!-- <h4 style="margin-top:-20px;"><?php echo _l('settings_smtp_settings_heading'); ?> <small><?php echo _l('settings_smtp_settings_subheading'); ?></small></h4>
		<hr /> -->
		<div class="form-group">

			<!-- <label for="mail_engine"><?php echo _l('mail_engine'); ?></label><br />
			<div class="radio radio-inline radio-primary">
				<input type="radio" name="settings[mail_engine]" id="phpmailer" value="phpmailer" <?php if(get_option('mail_engine') == 'phpmailer'){echo 'checked';} ?>>
				<label for="phpmailer">PHPMailer</label>
			</div>

			<div class="radio radio-inline radio-primary">
				<input type="radio" name="settings[mail_engine]" id="codeigniter" value="codeigniter" <?php if(get_option('mail_engine') == 'codeigniter'){echo 'checked';} ?>>
				<label for="codeigniter">CodeIgniter</label>
			</div>
			<hr /> -->
			<!-- <label for="email_protocol"><?php echo _l('email_protocol'); ?></label><br />
			<div class="radio radio-inline radio-primary">
				<input type="radio" name="settings[email_protocol]" id="smtp" value="smtp" <?php if(get_option('email_protocol') == 'smtp'){echo 'checked';} ?>>
				<label for="smtp">SMTP</label>
			</div> -->

			<!-- <div class="radio radio-inline radio-primary">
				<input type="radio" name="settings[email_protocol]" id="sendmail" value="sendmail" <?php if(get_option('email_protocol') == 'sendmail'){echo 'checked';} ?>>
				<label for="sendmail">Sendmail</label>
			</div> -->

			<!-- <div class="radio radio-inline radio-primary">
				<input type="radio" name="settings[email_protocol]" id="mail" value="mail" <?php if(get_option('email_protocol') == 'mail'){echo 'checked';} ?>>
				<label for="mail">Mail</label>
			</div> -->
		</div>
		<div class="smtp-fields<?php if(get_option('email_protocol') == 'mail'){echo ' hide'; } ?>">

		<div class="form-group mtop15">
			<label for="smtp_host">Mail Server</label><br />
			<select id="smtpserver" class="selectpicker" data-width="100%">
				<option value="" <?php if(get_option('smtp_host') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
				<option value="gmail" <?php if(get_option('smtp_host') == 'smtp.gmail.com'){echo 'selected';} ?>>Gmail</option>
				<option value="yahoo" <?php if(get_option('smtp_host') == 'smtp.mail.yahoo.com'){echo 'selected';} ?>>Yahoo</option>
				<option value="outlook" <?php if(get_option('smtp_host') == 'smtp.office365.com'){echo 'selected';} ?>>Outlook</option>
				<option value="others" <?php if(get_option('smtp_host') != '' && get_option('smtp_host') != 'smtp.gmail.com' && get_option('smtp_host') != 'smtp.mail.yahoo.com' && get_option('smtp_host') != 'smtp.office365.com'){echo 'selected';} ?>>Others</option>
			</select>
		</div>
		<!-- Host -->
		<div class="form-group mtop15 smtphost">
			<label for="smtp_host"><?php echo _l('smtp_host'); ?></label><br />
			<input Type="text" class="form-control" name="smtp_host" id="smtp_host" value="<?php echo get_option('smtp_host'); ?>" readonly>
		</div>
<!-- Encription Type -->
		<div class="form-group mtop15 gmail">
			<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
			<select name="smtp_encryption" id="smtp_encryption" class="selectpicker" data-width="100%">
				<option value="" <?php if(get_option('smtp_encryption') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
				<option value="ssl" <?php if(get_option('smtp_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
				<option value="tls" <?php if(get_option('smtp_encryption') == 'tls'){echo 'selected';} ?>>TLS</option>
				<option value="STARTTLS" <?php if(get_option('smtp_encryption') == 'STARTTLS'){echo 'selected';} ?>>STARTTLS</option>
			</select>
		</div>

		<div class="form-group mtop15 yahoo">
			<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
			<select name="smtp_encryption" id="smtp_encryption" class="selectpicker" data-width="100%">
				<option value="" <?php if(get_option('smtp_encryption') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
				<option value="ssl" <?php if(get_option('smtp_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
				<option value="tls" <?php if(get_option('smtp_encryption') == 'tls'){echo 'selected';} ?>>TLS</option>
			</select>
		</div>

		<div class="form-group mtop15 outlook">
			<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
			<select name="smtp_encryption" id="smtp_encryption" class="selectpicker" data-width="100%">
				<option value="" <?php if(get_option('smtp_encryption') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
				<option value="ssl" <?php if(get_option('smtp_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
				<option value="tls" <?php if(get_option('smtp_encryption') == 'tls'){echo 'selected';} ?>>TLS</option>
				<option value="STARTTLS" <?php if(get_option('smtp_encryption') == 'STARTTLS'){echo 'selected';} ?>>STARTTLS</option>
			</select>
		</div>

		<!-- Port -->
		<div class="form-group mtop15 smtpport">
			<label for="smtp_port"><?php echo _l('smtp_port'); ?></label><br />
			<input Type="text" class="form-control" name="smtp_port" id="smtp_port" value="<?php echo get_option('smtp_port'); ?>" readonly>
		</div>
		
		<div class="form-group mtop15 smtpother">
			<label for="smtp_host"><?php echo _l('smtp_host'); ?></label><br />
			<input type="text" name="smtp_host" id="smtp_host" class="form-control" value="<?php echo get_option('smtp_host'); ?>">
		</div>
		
		<div class="form-group mtop15 smtpother">
			<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
			<input type="text" name="smtp_encryption" id="smtp_encryption" class="form-control" value="<?php echo get_option('smtp_encryption'); ?>">
		</div>
		<div class="form-group mtop15 smtpother">
			<label for="smtp_port"><?php echo _l('smtp_port'); ?></label><br />
			<input type="text" name="smtp_port" id="smtp_port" class="form-control" value="<?php echo get_option('smtp_port'); ?>">
		</div>

		<?php //echo render_input('smtp_host','settings_email_host',get_option('smtp_host')); ?>
		<?php //echo render_input('smtp_port','settings_email_port',get_option('smtp_port')); ?>
		</div>
		<?php echo render_input('smtp_email','settings_email',get_option('smtp_email')); ?>
		<div class="smtp-fields<?php if(get_option('email_protocol') == 'mail'){echo ' hide'; } ?>">
		<?php echo render_input('smtp_username','smtp_username',get_option('smtp_username')); ?>
		<?php
		$ps = get_option('smtp_password');
		if(!empty($ps)){
			if(false == $this->encryption->decrypt($ps)){
				$ps = $ps;
			} else {
				$ps = $this->encryption->decrypt($ps);
			}
		}
		echo render_input('smtp_password','settings_email_password',$ps,'password',array('autocomplete'=>'off')); ?>
		</div>
		<!-- <?php //echo render_input('settings[smtp_email_charset]','settings_email_charset',get_option('smtp_email_charset')); ?>
		<?php //echo render_input('settings[bcc_emails]','bcc_all_emails',get_option('bcc_emails')); ?>
		<?php //echo render_textarea('settings[email_signature]','settings_email_signature',get_option('email_signature'), ['data-entities-encode'=>'true']); ?>
		<hr />
		<?php //echo render_textarea('settings[email_header]','email_header',get_option('email_header'),array('rows'=>15, 'data-entities-encode'=>'true')); ?>
		<?php //echo render_textarea('settings[email_footer]','email_footer',get_option('email_footer'),array('rows'=>15, 'data-entities-encode'=>'true')); ?>
		<hr />
		<h4><?php //echo _l('settings_send_test_email_heading'); ?></h4>
		<p class="text-muted"><?php //echo _l('settings_send_test_email_subheading'); ?></p>
		<div class="form-group">
			<div class="input-group">
				<input type="email" class="form-control" name="test_email" data-ays-ignore="true" placeholder="<?php echo _l('settings_send_test_email_string'); ?>">
				<div class="input-group-btn">
					<button type="button" class="btn btn-default test_email p7">Test</button>
				</div>
			</div>
		</div> -->

	</div>
	<div role="tabpanel" class="tab-pane" id="email_queue">

		<div class="form-group mtop15">
			<label for="smtp_host">Mail Server</label><br />
			<select id="imapserver" class="selectpicker" data-width="100%">
				<option value="" <?php if(get_option('imap_host') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
				<option value="gmail" <?php if(get_option('imap_host') == 'imap.gmail.com'){echo 'selected';} ?>>Gmail</option>
				<option value="yahoo" <?php if(get_option('imap_host') == 'imap.mail.yahoo.com'){echo 'selected';} ?>>Yahoo</option>
				<option value="outlook" <?php if(get_option('imap_host') == 'outlook.office365.com'){echo 'selected';} ?>>Outlook</option>
				<option value="others" <?php if(get_option('imap_host') != '' && get_option('imap_host') != 'imap.gmail.com' && get_option('imap_host') != 'imap.mail.yahoo.com' && get_option('imap_host') != 'outlook.office365.com'){echo 'selected';} ?>>Others</option>
			</select>
		</div>

		<div class="form-group mtop15 imaphost">
			<label for="imap_host"><?php echo _l('imap_host'); ?></label><br />
			<input type="text" name="imap_host" id="imap_host" class="form-control" value="<?php echo get_option('imap_host'); ?>" readonly>
		</div>

		<div class="form-group mtop15 imapgmail">
			<label for="imap_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
			<select name="imap_encryption" id="imap_encryption" class="selectpicker" data-width="100%">
				<option value="" <?php if(get_option('imap_encryption') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
				<option value="ssl" <?php if(get_option('imap_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
			</select>
		</div>

		<div class="form-group mtop15 imapyahoo">
			<label for="imap_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
			<select name="imap_encryption" id="imap_encryption" class="selectpicker" data-width="100%">
				<option value="" <?php if(get_option('imap_encryption') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
				<option value="ssl" <?php if(get_option('imap_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
			</select>
		</div>

		<div class="form-group mtop15 imapoutlook">
			<label for="imap_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
			<select name="imap_encryption" id="imap_encryption" class="selectpicker" data-width="100%">
				<option value="" <?php if(get_option('imap_encryption') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
				<option value="ssl" <?php if(get_option('imap_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
			</select>
		</div>

		<div class="form-group mtop15 imapport">
			<label for="imap_port"><?php echo _l('imap_port'); ?></label><br />
			<input type="text" name="imap_port" id="imap_port" class="form-control" value="<?php echo get_option('imap_port'); ?>" readonly>
		</div>
		
		
		<div class="form-group mtop15 imapother">
			<label for="imap_host"><?php echo _l('imap_host'); ?></label><br />
			<input type="text" name="imap_host" id="imap_host" class="form-control" value="<?php echo get_option('imap_host'); ?>">
		</div>
		<div class="form-group mtop15 imapother">
			<label for="imap_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
			<input type="text" name="imap_encryption" id="imap_encryption" class="form-control" value="<?php echo get_option('imap_encryption'); ?>">
		</div>
		<div class="form-group mtop15 imapother">
			<label for="imap_port"><?php echo _l('imap_port'); ?></label><br />
			<input type="text" name="imap_port" id="imap_port" class="form-control" value="<?php echo get_option('imap_port'); ?>">
		</div>
		<?php //echo render_input('imap_host','settings_imapemail_host',get_option('imap_host')); ?>
		<?php //echo render_input('imap_port','settings_imapemail_port',get_option('imap_port')); ?>
		<?php echo render_input('imap_email','settings_email',get_option('imap_email')); ?>
		<?php echo render_input('imap_username','imap_username',get_option('imap_username')); ?>
		<?php
		$ps = get_option('imap_password');
		echo render_input('imap_password','settings_imapemail_password',$ps,'password',array('autocomplete'=>'off')); ?>
</div></div>
