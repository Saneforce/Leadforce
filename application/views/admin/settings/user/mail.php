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
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="email_queue">
				<div class="row">
					<div class="col-md-6">
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
					</div>
				</div>
				


				<div class="row">
					<?php 
					$ch_mail = '';
					if(!empty($settings['imap_email']) ){
						$ch_mail = $settings['imap_email'];
					}
					//echo render_input('imap_email','settings_email',$ch_mail); ?>
					<div class="col-md-6">
						<?php 
						$ch_username = '';
						if(!empty($settings['imap_username']) ){
							$ch_username = $settings['imap_username'];
						}
						echo render_input('imap_username','imap_username',$ch_username); ?>
					</div>
					<div class="col-md-6">
						<?php
						//$ps = get_option('imap_password');
						$ps = '';
						if(!empty($settings['imap_password']) ){
							$ps = $settings['imap_password'];
						}
						echo render_input('imap_password','settings_imapemail_password',$ps,'password',array('autocomplete'=>'off')); ?>
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
								<input type="text" name="imap_host" id="imap_host" class="form-control" value="<?php if(!empty($settings['imap_host']) ){echo $settings['imap_host'];}?>" readonly>
							</div>
							<div class="imapother">
								<label for="imap_host"><?php echo _l('imap_host'); ?></label><br />
								<input type="text" name="imap_host" id="imap_host" class="form-control" value="<?php if(!empty($settings['imap_host']) ){echo $settings['imap_host'];}?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="imapgmail">
								<label for="imap_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
								<select name="imap_encryption" id="imap_encryption" class="selectpicker" data-width="100%">
									<option value="" <?php echo isset($settings['imap_host']) && $settings['imap_encryption']=="" ?'selected':''?>><?php echo _l('smtp_encryption_none'); ?></option>
									<option value="ssl" <?php echo isset($settings['imap_host']) && $settings['imap_encryption']=="ssl" ?'selected':''?>>SSL</option>
								</select>
							</div>

							<div class="imapyahoo">
								<label for="imap_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
								<select name="imap_encryption" id="imap_encryption" class="selectpicker" data-width="100%">
									<option value="" <?php echo isset($settings['imap_host']) && $settings['imap_encryption']=='' ?'selected':''?>><?php echo _l('smtp_encryption_none'); ?></option>
									<option value="ssl" <?php echo isset($settings['imap_host']) && $settings['imap_encryption']=='ssl' ?'selected':''?>>SSL</option>
									<option value="tls" <?php echo isset($settings['imap_host']) && $settings['imap_encryption']=="tls" ?'selected':''?>>TLS</option>
									
								</select>
							</div>

							<div class="imapoutlook">
								<label for="imap_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
								<select name="imap_encryption" id="imap_encryption" class="selectpicker" data-width="100%" onchange="check_encryption()">
									<option value="" <?php echo isset($settings['imap_host']) && $settings['imap_encryption']=='' ?'selected':''?> ><?php echo _l('smtp_encryption_none'); ?></option>
									<option value="ssl" <?php echo isset($settings['imap_host']) && $settings['imap_encryption']=='ssl' ?'selected':''?>>SSL</option>
									<option value="tls" <?php echo isset($settings['imap_host']) && $settings['imap_encryption']=='tls' ?'selected':''?>>TLS</option>
									<option value="STARTTLS" <?php echo isset($settings['imap_host']) && $settings['imap_encryption']=='STARTTLS' ?'selected':''?>>STARTTLS</option>
								</select>
							</div>
							<div class="imapother">
								<label for="imap_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
								<input type="text" name="imap_encryption" id="imap_encryption" class="form-control" value="<?php if(!empty($settings['imap_encryption']) ){echo $settings['imap_encryption'];}?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="imapother">
								<label for="imap_port"><?php echo _l('imap_port'); ?></label><br />
								<input type="text" name="imap_port" id="imap_port" class="form-control" value="<?php if(!empty($settings['imap_port']) ){echo $settings['imap_port'];}?>">
							</div>
							<div class="imapport">
								<label for="imap_port"><?php echo _l('imap_port'); ?></label><br />
								<input type="text" name="imap_port" id="imap_port" class="form-control" value="<?php if(!empty($settings['imap_port']) ){echo $settings['imap_port'];}?>" >
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
								<input type="text" name="smtp_host" id="smtp_host" class="form-control" value="<?php if(!empty($settings['smtp_host']) ){echo $settings['smtp_host'];}?>" readonly>
							</div>
							<div class="smtpother">
								<label for="smtp_host"><?php echo _l('smtp_host'); ?></label><br />
								<input type="text" name="smtp_host" id="smtp_host" class="form-control" value="<?php if(!empty($settings['smtp_host']) ){echo $settings['smtp_host'];}?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="smtpgmail">
								<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
								<select name="smtp_encryption" id="smtp_encryption" class="selectpicker" data-width="100%">
									<option value="" <?php echo isset($settings['smtp_host']) && $settings['smtp_encryption']=="" ?'selected':''?>><?php echo _l('smtp_encryption_none'); ?></option>
									<option value="ssl" <?php echo isset($settings['smtp_host']) && $settings['smtp_encryption']=='ssl' ?'selected':''?>>SSL</option>
								</select>
							</div>

							<div class="smtpyahoo">
								<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
								<select name="smtp_encryption" id="smtp_encryption" class="selectpicker" data-width="100%">
									<option value="" <?php echo isset($settings['smtp_host']) && $settings['smtp_encryption']=="" ?'selected':''?>><?php echo _l('smtp_encryption_none'); ?></option>
									<option value="ssl" <?php echo isset($settings['smtp_host']) && $settings['smtp_encryption']=='ssl' ?'selected':''?>>SSL</option>
									<option value="tls" <?php echo isset($settings['smtp_host']) && $settings['smtp_encryption']=='tls' ?'selected':''?>>TLS</option>
									
								</select>
							</div>

							<div class="smtpoutlook">
								<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
								<select name="smtp_encryption" id="smtp_encryption" class="selectpicker" data-width="100%" onchange="check_encryption()">
									<option value="" <?php echo isset($settings['smtp_host']) && $settings['smtp_encryption']=="" ?'selected':''?> ><?php echo _l('smtp_encryption_none'); ?></option>
									<option value="ssl" <?php echo isset($settings['smtp_host']) && $settings['smtp_encryption']=='ssl' ?'selected':''?>>SSL</option>
									<option value="tls" <?php echo isset($settings['smtp_host']) && $settings['smtp_encryption']=='tls' ?'selected':''?>>TLS</option>
									<option value="STARTTLS" <?php echo isset($settings['smtp_host']) && $settings['smtp_encryption']=='STARTTLS' ?'selected':''?>>STARTTLS</option>
								</select>
							</div>
							<div class="smtpother">
								<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
								<input type="text" name="smtp_encryption" id="smtp_encryption" class="form-control" value="<?php if(!empty($settings['smtp_encryption']) ){echo $settings['smtp_encryption'];}?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="smtpother">
								<label for="smtp_port"><?php echo _l('smtp_port'); ?></label><br />
								<input type="text" name="smtp_port" id="smtp_port" class="form-control" value="<?php if(!empty($settings['smtp_port']) ){echo $settings['smtp_port'];}?>">
							</div>
							<div class="smtpport">
								<label for="smtp_port"><?php echo _l('smtp_port'); ?></label><br />
								<input type="text" name="smtp_port" id="smtp_port" class="form-control" value="<?php if(!empty($settings['smtp_port']) ){echo $settings['smtp_port'];}?>" >
							</div>
						</div>
					</div>
				</div>

				
				
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