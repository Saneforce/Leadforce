<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('authentication/includes/head.php'); ?>
<body class="login_admin"<?php if(is_rtl()){ echo ' dir="rtl"'; } ?> >
 <div class="container">
 <div id="overlay" style="display: none;"><div class="spinner"></div></div>
  <div class="row">
   <div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2 authentication-form-wrapper">
    <div class="company-logo">
      <?php get_company_logo(); ?>
    </div>
    <div class="mtop40 authentication-form">
      <h1><?php echo _l('admin_auth_login_heading'); ?></h1>
      <?php $this->load->view('authentication/includes/alerts'); ?>
      <?php echo form_open($this->uri->uri_string(),array('class'=>'','id'=>'form_id','onsubmit'=>'log_form()')); ?>
      <?php echo validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
      <?php hooks()->do_action('after_admin_login_form_start'); ?>
      <div class="form-group">
        <label for="email" class="control-label"><?php echo _l('admin_auth_login_email'); ?></label>
        <input type="email" id="email" name="email" class="form-control" autofocus="1">
      </div>
      <div class="form-group">
        <label for="password" class="control-label"><?php echo _l('admin_auth_login_password'); ?></label>
        <input type="password" id="password" name="password" class="form-control"></div>
        <?php if(get_option('recaptcha_secret_key') != '' && get_option('recaptcha_site_key') != ''){ ?>
        <div class="g-recaptcha" data-sitekey="<?php echo get_option('recaptcha_site_key'); ?>"></div>
        <?php } ?>
        <div class="checkbox">
          <label for="remember">
           <input type="checkbox" id="remember" name="remember"> <?php echo _l('admin_auth_login_remember_me'); ?>
         </label>
       </div>
       <div class="form-group">
        <button type="submit" class="btn btn-info btn-block"><?php echo _l('admin_auth_login_button'); ?></button>
      </div>
      <div class="form-group">
        <a href="<?php echo admin_url('authentication/forgot_password'); ?>"><?php echo _l('admin_auth_login_fp'); ?></a>
      </div>

      <?php hooks()->do_action('before_admin_login_form_close'); ?>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
</div>
<style>
/* Absolute Center Spinner */
#overlay,.overlay_new {
  position: fixed;
  z-index: 999;
  overflow: show;
  margin: auto;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 50px;
  height: 50px;
}

/* Transparent Overlay */
#overlay:before,.overlay_new:before {
  content: '';
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255,255,255,0.5);
}

/* :not(:required) hides these rules from IE9 and below */
#overlay:not(:required),.overlay_new:not(:required) {
  /* hide "loading..." text */
  font: 0/0 a;
  color: transparent;
  text-shadow: none;
  background-color: transparent;
  border: 0;
}

#overlay:not(:required):after,.overlay_new:not(:required):after {
  content: '';
  display: block;
  font-size: 10px;
  width: 50px;
  height: 50px;
  margin-top: -0.5em;

  border: 3px solid rgba(33, 150, 243, 1.0);
  border-radius: 100%;
  border-bottom-color: transparent;
  -webkit-animation: spinner 1s linear 0s infinite;
  animation: spinner 1s linear 0s infinite;


}

/* Animation */

@-webkit-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-moz-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-o-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

@-webkit-keyframes rotation {
   from {-webkit-transform: rotate(0deg);}
   to {-webkit-transform: rotate(359deg);}
}
@-moz-keyframes rotation {
   from {-moz-transform: rotate(0deg);}
   to {-moz-transform: rotate(359deg);}
}
@-o-keyframes rotation {
   from {-o-transform: rotate(0deg);}
   to {-o-transform: rotate(359deg);}
}
@keyframes rotation {
   from {transform: rotate(0deg);}
   to {transform: rotate(359deg);}
}

</style>
<script>
function log_form(){
	document.getElementById('overlay').style.display = '';
}
</script>
</body>
</html>
