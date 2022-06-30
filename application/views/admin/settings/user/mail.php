<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content"> 
  <?php echo form_open_multipart($this->uri->uri_string(),['id' => 'settings-form', 'class' => 'custom-update-url' ]);?>
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
			<li role="presentation" class="" style="display:none">
				<a href="#email_config" aria-controls="email_config" role="tab" data-toggle="tab"><?php echo _l('settings_smtp_settings_heading'); ?></a>
			</li>
			<li role="presentation" class="active" <?php if(get_option('connect_mail')=='no'){?> style="display:none"<?php }?>>
				<a href="#email_queue" aria-controls="email_queue" role="tab" data-toggle="tab"><?php echo _l('settings_imap_settings_heading'); ?></a>
			</li>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane " id="email_config" style="display:none">
				
				<div class="smtp-fields">

				<div class="form-group mtop15">
					<label for="smtp_host">Mail Server</label><br />
					<select id="smtpserver" name="smtp_server" class="selectpicker" data-width="100%">
						<option value="" ><?php echo _l('smtp_encryption_none'); ?></option>
						<option value="gmail" <?php if(!empty($settings['smtp_server']) && $settings['smtp_server'] == 'gmail'){echo 'selected';}?> >Gmail</option>
						<option value="yahoo" <?php if(!empty($settings['smtp_server']) && $settings['smtp_server'] == 'yahoo'){echo 'selected';}?> >Yahoo</option>
						<option value="outlook" <?php if(!empty($settings['smtp_server']) && $settings['smtp_server'] == 'outlook'){echo 'selected';}?> >Outlook</option>
						<option value="others" <?php if(!empty($settings['smtp_server']) && $settings['smtp_server'] == 'others'){echo 'selected';}?> >Others</option>
					</select>
				</div>
				<!-- Host -->
				<div class="form-group mtop15 smtphost">
					<label for="smtp_host"><?php echo _l('smtp_host'); ?></label><br />
					<input Type="text" class="form-control" name="smtp_host" id="smtp_host" value="<?php if(!empty($settings['smtp_host']) ){echo $settings['smtp_host'];}?>" readonly>
				</div>
		<!-- Encription Type -->
				<div class="form-group mtop15 gmail">
					<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
					<select name="smtp_encryption" id="smtp_encryption" class="selectpicker" data-width="100%">
						<option value="" ><?php echo _l('smtp_encryption_none'); ?></option>
						<option value="ssl" <?php if(!empty($settings['smtp_encryption']) && $settings['smtp_encryption'] == 'ssl'){echo 'selected';}?> >SSL</option>
						<option value="tls" <?php if(!empty($settings['smtp_encryption']) && $settings['smtp_encryption'] == 'tls'){echo 'selected';}?>>TLS</option>
						<option value="STARTTLS" <?php if(!empty($settings['smtp_encryption']) && $settings['smtp_encryption'] == 'STARTTLS'){echo 'selected';}?>>STARTTLS</option>
					</select>
				</div>

				<div class="form-group mtop15 yahoo">
					<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
					<select name="smtp_encryption" id="smtp_encryption" class="selectpicker" data-width="100%">
						<option value="" ><?php echo _l('smtp_encryption_none'); ?></option>
						<option value="ssl" <?php if(!empty($settings['smtp_encryption']) && $settings['smtp_encryption'] == 'ssl'){echo 'selected';}?>>SSL</option>
						<option value="tls" <?php if(!empty($settings['smtp_encryption']) && $settings['smtp_encryption'] == 'tls'){echo 'selected';}?>>TLS</option>
					</select>
				</div>

				<div class="form-group mtop15 outlook">
					<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
					<select name="smtp_encryption" id="smtp_encryption" class="selectpicker" data-width="100%">
						<option value="" ><?php echo _l('smtp_encryption_none'); ?></option>
						<option value="ssl" <?php if(!empty($settings['smtp_encryption']) && $settings['smtp_encryption'] == 'ssl'){echo 'selected';}?>>SSL</option>
						<option value="tls" <?php if(!empty($settings['smtp_encryption']) && $settings['smtp_encryption'] == 'tls'){echo 'selected';}?>>TLS</option>
						<option value="STARTTLS" <?php if(!empty($settings['smtp_encryption']) && $settings['smtp_encryption'] == 'STARTTLS'){echo 'selected';}?>>STARTTLS</option>
					</select>
				</div>

				<!-- Port -->
				<div class="form-group mtop15 smtpport">
					<label for="smtp_port"><?php echo _l('smtp_port'); ?></label><br />
					<input Type="text" class="form-control" name="smtp_port" id="smtp_port" value="<?php if(!empty($settings['smtp_port']) ){echo $settings['smtp_port'];}?>" >
				</div>
				
				<div class="form-group mtop15 smtpother">
					<label for="smtp_host"><?php echo _l('smtp_host'); ?></label><br />
					<input type="text" name="smtp_host" id="smtp_host" class="form-control" value="<?php if(!empty($settings['smtp_host']) ){echo $settings['smtp_host'];}?>">
				</div>
				
				<div class="form-group mtop15 smtpother">
					<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
					<input type="text" name="smtp_encryption" id="smtp_encryption" class="form-control" value="<?php if(!empty($settings['smtp_host']) ){echo $settings['smtp_encryption'];}?>">
				</div>
				<div class="form-group mtop15 smtpother">
					<label for="smtp_port"><?php echo _l('smtp_port'); ?></label><br />
					<input type="text" name="smtp_port" id="smtp_port" class="form-control" value="<?php if(!empty($settings['smtp_port']) ){echo $settings['smtp_port'];}?>">
				</div>
				</div>

				<?php 
				$ch_mail = '';
				if(!empty($settings['smtp_email']) ){
					$ch_mail = $settings['smtp_email'];
				}
				echo render_input('smtp_email','settings_email',$ch_mail); ?>
				<div class="smtp-fields">
				<?php
				$ch_username = '';
				if(!empty($settings['smtp_username']) ){
					$ch_username = $settings['smtp_username'];
				}

				echo render_input('smtp_username','smtp_username',$ch_username); ?>
				<?php
				$ps = '';
				if(!empty($settings['smtp_password']) ){
					$ps = $settings['smtp_password'];
				}
				if(false == $this->encryption->decrypt($ps)){
					$ps = $ps;
				} else {
					$ps = $this->encryption->decrypt($ps);
				}
				echo render_input('smtp_password','settings_email_password',$ps,'password',array('autocomplete'=>'off')); ?>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane active" id="email_queue">

				<div class="form-group mtop15">
					<label for="smtp_host">Mail Server</label><br />
					<select id="imapserver" name="imap_server" class="selectpicker" data-width="100%">
						<option value="" ><?php echo _l('smtp_encryption_none'); ?></option>
						<option value="gmail" <?php if(!empty($settings['imap_server']) && $settings['imap_server'] == 'gmail'){echo 'selected';}?>>Gmail</option>
						<option value="yahoo"  <?php if(!empty($settings['imap_server']) && $settings['imap_server'] == 'yahoo'){echo 'selected';}?>>Yahoo</option>
						<option value="outlook" <?php if(!empty($settings['imap_server']) && $settings['imap_server'] == 'outlook'){echo 'selected';}?>>Outlook</option>
						<option value="others" <?php if(!empty($settings['imap_server']) && $settings['imap_server'] == 'others'){echo 'selected';}?>>Others</option>
					</select>
				</div>

				<div class="form-group mtop15 imaphost">
					<label for="imap_host"><?php echo _l('imap_host'); ?></label><br />
					<input type="text" name="imap_host" id="imap_host" class="form-control" value="<?php if(!empty($settings['imap_host']) ){echo $settings['imap_host'];}?>" readonly >
				</div>

				<div class="form-group mtop15 imapgmail">
					<label for="imap_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
					<select name="imap_encryption" id="imap_encryption" class="selectpicker" data-width="100%">
						<option value="" ><?php echo _l('smtp_encryption_none'); ?></option>
						<option value="ssl" <?php if(!empty($settings['imap_encryption']) && $settings['imap_encryption'] == 'ssl'){echo 'selected';}?>>SSL</option>
					</select>
				</div>

				<div class="form-group mtop15 imapyahoo">
					<label for="imap_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
					<select name="imap_encryption" id="imap_encryption" class="selectpicker" data-width="100%">
						<option value="" ><?php echo _l('smtp_encryption_none'); ?></option>
						<option value="ssl" <?php if(!empty($settings['imap_encryption']) && $settings['imap_encryption'] == 'ssl'){echo 'selected';}?>>SSL</option>
					</select>
				</div>

				<div class="form-group mtop15 imapoutlook">
					<label for="imap_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
					<select name="imap_encryption" id="imap_encryption" class="selectpicker" data-width="100%">
						<option value="" ><?php echo _l('smtp_encryption_none'); ?></option>
						<option value="ssl" <?php if(!empty($settings['imap_encryption']) && $settings['imap_encryption'] == 'ssl'){echo 'selected';}?>>SSL</option>
					</select>
				</div>

				<div class="form-group mtop15 imapport">
					<label for="imap_port"><?php echo _l('imap_port'); ?></label><br />
					<input type="text" name="imap_port" id="imap_port" class="form-control" value="<?php if(!empty($settings['imap_port']) ){echo $settings['imap_port'];}?>" >
				</div>
				
				
				<div class="form-group mtop15 imapother">
					<label for="imap_host"><?php echo _l('imap_host'); ?></label><br />
					<input type="text" name="imap_host" id="imap_host" class="form-control" value="<?php if(!empty($settings['imap_host']) ){echo $settings['imap_host'];}?>" >
				</div>
				<div class="form-group mtop15 imapother">
					<label for="imap_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
					<input type="text" name="imap_encryption" id="imap_encryption" class="form-control" value="<?php if(!empty($settings['imap_encryption']) ){echo $settings['imap_encryption'];}?>">
				</div>
				<div class="form-group mtop15 imapother">
					<label for="imap_port"><?php echo _l('imap_port'); ?></label><br />
					<input type="text" name="imap_port" id="imap_port" class="form-control" value="<?php if(!empty($settings['imap_port']) ){echo $settings['imap_port'];}?>" >
				</div>
				<?php 
				$ch_mail = '';
				if(!empty($settings['imap_email']) ){
					$ch_mail = $settings['imap_email'];
				}
				//echo render_input('imap_email','settings_email',$ch_mail); ?>
				<?php 
				$ch_username = '';
				if(!empty($settings['imap_username']) ){
					$ch_username = $settings['imap_username'];
				}
				echo render_input('imap_username','imap_username',$ch_username); ?>
				<?php
				//$ps = get_option('imap_password');
				$ps = '';
				if(!empty($settings['imap_password']) ){
					$ps = $settings['imap_password'];
				}
				echo render_input('imap_password','settings_imapemail_password',$ps,'password',array('autocomplete'=>'off')); ?>
				</div>
				<div class="btn-bottom-toolbar text-right">
          <button type="submit" class="btn btn-info" name="submit_save"><?php echo _l('settings_save'); ?></button>
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

</body>
</html>