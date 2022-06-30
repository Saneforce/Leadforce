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
         <?php echo $this->session->flashdata('debug'); unset($_SESSION['debug']);?>
       </div>
     </div>
   <?php
    } ?>
 <div class="col-md-12" style="position: absolute;margin: 0;top: 50%;transform: translateY(-50%);-ms-transform: translateY(-50%);">
  <div class="col-md-3"></div>
  <div class="col-md-6">
    <div class="panel_s">
     <div class="panel-body">
		<div class="tab-content">
		<h3 style="color:#fff;text-align:center;font-weight:500">Email Setting</h3>
			<div role="tabpanel" class="tab-pane active" id="email_config">
				

				<?php 
				$ch_mail = '';
				if(!empty($settings['email']) ){
					$ch_mail = $settings['email'];
				}
				echo render_input('email','settings_email',$ch_mail); ?>
				<div class="smtp-fields">
				<?php
				$ps = '';
				if(!empty($settings['password']) ){
					$ps = $settings['password'];
				}
				echo render_input('password','Password',$ps,'password',array('autocomplete'=>'off')); ?>
				</div>
			</div>
			<div class="btn-bottom-toolbar1 text-right1">
          <button type="submit" class="btn btn-save" name="submit_save"><?php echo _l('settings_save'); ?></button>
        </div>
				
		</div>
    </div>
  </div>
  <div class="col-md-3"></div>
</div>
</div>
<div class="clearfix"></div>
</div>
<?php echo form_close(); ?>
<div class="btn-bottom-pusher"></div>
</div>
</div>
<div id="new_version"></div>
<style>
.btn-save{
	background-color: white;
    color: #0069e8;
}
.btn-save:hover{
	background-color: #0069e8;
    color: white;
}

.panel_s .panel-body {
    background: #0069e8 !important;
    border: 1px solid #dce1ef;
    border-radius: 4px;
    padding: 20px;
    position: relative;
}
.control-label, label {
    color: #fff;
	font-size:15px;
}
</style>
<?php init_tail(); ;?>

<?php hooks()->do_action('settings_tab_footer', 'email'); ?>
</body>
</html>