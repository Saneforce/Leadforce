<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">

    <div class="row">
      <?php echo form_open_multipart($this->uri->uri_string(), ['id' => 'settings-form', 'class' => 'custom-update-url']); ?>
      <?php if ($this->session->flashdata('debug')) {
      ?>
        <div class="col-lg-12">
          <div class="alert alert-warning">
            <?php echo $this->session->flashdata('debug');
            unset($_SESSION['debug']); ?>
          </div>
        </div>
      <?php
      } ?>
        <div class="col-md-4 col-md-offset-4">
          <div class="panel_s">
            <div class="panel-body">
              <div class="tab-content">
                <h4>Connect Email</h4>
                <hr>
                <div role="tabpanel" class="tab-pane active" id="email_config">
                  <?php
                  $ch_mail = '';
                  if (!empty($settings['email'])) {
                    $ch_mail = $settings['email'];
                  }
                  echo render_input('email', 'settings_email', $ch_mail); ?>
                  <div class="smtp-fields">
                    <?php
                    $ps = '';
                    if (!empty($settings['password'])) {
                      $ps = $settings['password'];
                    }
                    echo render_input('password', 'Password', $ps, 'password', array('autocomplete' => 'off')); ?>
                  </div>
                </div>
                <div class="btn-bottom-toolbar1 pull-right">
                  <button type="submit" class="btn btn-save btn-primary" name="submit_save"><?php echo _l('save'); ?></button>
                </div>

              </div>
            </div>
          </div>
        </div>
      <?php echo form_close(); ?>
    </div>

    <div class="btn-bottom-pusher"></div>
  </div>
</div>
<div id="new_version"></div>
<?php init_tail(); ?>

<?php hooks()->do_action('settings_tab_footer', 'email'); ?>
</body>

</html>