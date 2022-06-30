<div role="tabpanel" class="tab-pane active" id="email_config">			
			<div class="smtp-fields<?php if(get_option('email_protocol') == 'mail'){echo ' hide'; } ?>">
			<div class="form-group mtop15">
				<label for="smtp_host">Mail Server</label><br />
				<select id="smtpserver" class="selectpicker" data-width="100%" name="company_smtpserver">
					<option value="" <?php if(get_option('company_smtp_server') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
					<option value="gmail" <?php if(get_option('company_smtp_server') == 'gmail'){echo 'selected';} ?>>Gmail</option>
					<option value="yahoo" <?php if(get_option('company_smtp_server') == 'yahoo'){echo 'selected';} ?>>Yahoo</option>
					<option value="outlook" <?php if(get_option('company_smtp_server') == 'outlook'){echo 'selected';} ?>>Outlook</option>
					<option value="others" <?php if(get_option('company_smtp_server') == 'others'){echo 'selected';} ?>>Others</option>
				</select>
			</div>
			<!-- Host -->
			<div class="form-group mtop15 smtphost">
				<label for="smtp_host"><?php echo _l('smtp_host'); ?></label><br />
				<input Type="text" class="form-control" name="smtp_host" id="smtp_host" value="<?php echo get_option('company_smtp_host'); ?>" readonly>
			</div>
	<!-- Encription Type -->
			<div class="form-group mtop15 gmail">
				<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
				<select name="smtp_encryption" id="smtp_encryption" class="selectpicker" data-width="100%">
					<option value="" <?php if(get_option('company_smtp_encryption') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
					<option value="ssl" <?php if(get_option('company_smtp_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
					<option value="tls" <?php if(get_option('company_smtp_encryption') == 'tls'){echo 'selected';} ?>>TLS</option>
					<option value="STARTTLS" <?php if(get_option('company_smtp_encryption') == 'STARTTLS'){echo 'selected';} ?>>STARTTLS</option>
				</select>
			</div>

			<div class="form-group mtop15 yahoo">
				<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
				<select name="smtp_encryption" id="smtp_encryption" class="selectpicker" data-width="100%">
					<option value="" <?php if(get_option('company_smtp_encryption') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
					<option value="ssl" <?php if(get_option('company_smtp_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
					<option value="tls" <?php if(get_option('company_smtp_encryption') == 'tls'){echo 'selected';} ?>>TLS</option>
				</select>
			</div>

			<div class="form-group mtop15 outlook">
				<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
				<select name="smtp_encryption" id="smtp_encryption" class="selectpicker" data-width="100%">
					<option value="" <?php if(get_option('company_smtp_encryption') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
					<option value="ssl" <?php if(get_option('company_smtp_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
					<option value="tls" <?php if(get_option('company_smtp_encryption') == 'tls'){echo 'selected';} ?>>TLS</option>
					<option value="STARTTLS" <?php if(get_option('company_smtp_encryption') == 'STARTTLS'){echo 'selected';} ?>>STARTTLS</option>
				</select>
			</div>

			<!-- Port -->
			<div class="form-group mtop15 smtpport">
				<label for="smtp_port"><?php echo _l('smtp_port'); ?></label><br />
				<input Type="text" class="form-control" name="smtp_port" id="smtp_port" value="<?php echo get_option('company_smtp_port'); ?>" readonly>
			</div>
			
			<div class="form-group mtop15 smtpother">
				<label for="smtp_host"><?php echo _l('smtp_host'); ?></label><br />
				<input type="text" name="smtp_host" id="smtp_host" class="form-control" value="<?php echo get_option('company_smtp_host'); ?>">
			</div>
			
			<div class="form-group mtop15 smtpother">
				<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
				<input type="text" name="smtp_encryption" id="smtp_encryption" class="form-control" value="<?php echo get_option('company_smtp_encryption'); ?>">
			</div>
			<div class="form-group mtop15 smtpother">
				<label for="smtp_port"><?php echo _l('smtp_port'); ?></label><br />
				<input type="text" name="smtp_port" id="smtp_port" class="form-control" value="<?php echo get_option('company_smtp_port'); ?>">
			</div>
			<?php echo render_input('smtp_email','settings_email',get_option('company_smtp_email')); ?>
		<div class="smtp-fields<?php if(get_option('email_protocol') == 'mail'){echo ' hide'; } ?>">
		<?php echo render_input('smtp_username','smtp_username',get_option('company_smtp_username')); ?>
		<?php
		$ps = get_option('company_smtp_password');
		if(!empty($ps)){
			if(false == $this->encryption->decrypt($ps)){
				$ps = $ps;
			} else {
				$ps = $this->encryption->decrypt($ps);
			}
		}
		
		echo render_input('smtp_password','settings_email_password',$ps,'password',array('autocomplete'=>'off')); ?>
		</div>

			</div>
			

		</div>
		