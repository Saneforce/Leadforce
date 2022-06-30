
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <title>
    LEADFORCE - Login  </title>
  <link rel="shortcut icon" id="favicon" href="<?php echo base_url('uploads/company/favicon.png');?>">
<link rel="stylesheet" type="text/css" id="reset-css" href="<?php echo base_url('assets/css/reset.min.css?v=2.4.0');?>">
<link rel="stylesheet" type="text/css" id="bootstrap-css" href="<?php echo base_url('assets/plugins/bootstrap/css/bootstrap.min.css?v=2.4.0');?>">
<link rel="stylesheet" type="text/css" id="roboto-css" href="<?php echo base_url('assets/plugins/roboto/roboto.css?v=2.4.0');?>">
<link rel="stylesheet" type="text/css" id="bootstrap-overrides" href="<?php echo base_url('assets/css/bs-overides.min.css?v=2.4.0');?>">
  <style>
  body {
    font-family: "Roboto", "Helvetica Neue", Helvetica, Arial, sans-serif;
    background-color: #0069e8;
    font-size: 13px;
    color: #6a6c6f;
    margin: 0;
    padding: 0;
  }

  h1 {
    font-weight: 400;
    font-size: 24px;
    margin-bottom: 35px;
    text-transform: uppercase;
    text-align: center;
  }

  .btn-primary {
    color: #ffffff;
    background-color: #28b8da;
    border-color: #22a7c6;
  }

  .btn-primary:focus,
  .btn-primary.focus {
    color: #ffffff;
    background-color: #1e95b1;
    border-color: #0f4b5a;
  }

  .btn-primary:hover {
    color: #ffffff;
    background-color: #1e95b1;
    border-color: #197b92;
  }

  .btn-primary:active,
  .btn-primary.active,
  .open>.dropdown-toggle.btn-primary {
    color: #ffffff;
    background-color: #1e95b1;
    border-color: #197b92;
  }

  .btn-primary:active:hover,
  .btn-primary.active:hover,
  .open>.dropdown-toggle.btn-primary:hover,
  .btn-primary:active:focus,
  .btn-primary.active:focus,
  .open>.dropdown-toggle.btn-primary:focus,
  .btn-primary:active.focus,
  .btn-primary.active.focus,
  .open>.dropdown-toggle.btn-primary.focus {
    color: #ffffff;
    background-color: #197b92;
    border-color: #0f4b5a;
  }

  .btn-primary:active,
  .btn-primary.active,
  .open>.dropdown-toggle.btn-primary {
    background-image: none;
  }

  .btn-primary.disabled,
  .btn-primary[disabled],
  fieldset[disabled] .btn-primary,
  .btn-primary.disabled:hover,
  .btn-primary[disabled]:hover,
  fieldset[disabled] .btn-primary:hover,
  .btn-primary.disabled:focus,
  .btn-primary[disabled]:focus,
  fieldset[disabled] .btn-primary:focus,
  .btn-primary.disabled.focus,
  .btn-primary[disabled].focus,
  fieldset[disabled] .btn-primary.focus,
  .btn-primary.disabled:active,
  .btn-primary[disabled]:active,
  fieldset[disabled] .btn-primary:active,
  .btn-primary.disabled.active,
  .btn-primary[disabled].active,
  fieldset[disabled] .btn-primary.active {
    background-color: #28b8da;
    border-color: #22a7c6;
  }

  .btn-primary .badge {
    color: #28b8da;
    background-color: #ffffff;
  }

  input[type="text"],
  input[type="password"],
  input[type="datetime"],
  input[type="datetime-local"],
  input[type="date"],
  input[type="month"],
  input[type="time"],
  input[type="week"],
  input[type="number"],
  input[type="email"],
  input[type="url"],
  input[type="search"],
  input[type="tel"],
  input[type="color"],
  .uneditable-input,
  input[type="color"] {
    border: 1px solid #bfcbd9;
    -webkit-box-shadow: none;
    box-shadow: none;
    color: #494949;
    font-size: 14px;
    line-height: 1;
    height: 36px;
  }

  input[type="text"]:focus,
  input[type="password"]:focus,
  input[type="datetime"]:focus,
  input[type="datetime-local"]:focus,
  input[type="date"]:focus,
  input[type="month"]:focus,
  input[type="time"]:focus,
  input[type="week"]:focus,
  input[type="number"]:focus,
  input[type="email"]:focus,
  input[type="url"]:focus,
  input[type="search"]:focus,
  input[type="tel"]:focus,
  input[type="color"]:focus,
  .uneditable-input:focus,
  input[type="color"]:focus {
    border-color: #03a9f4;
    -webkit-box-shadow: none;
    box-shadow: none;
    outline: 0 none;
  }

  input.form-control {
    -webkit-box-shadow: none;
    box-shadow: none;
  }

  .company-logo {
    padding: 25px 10px;
    display: block;
  }

  .company-logo img {
    margin: 0 auto;
    display: block;
  }

  .authentication-form-wrapper {
    margin-top: 25px;
  }

  @media (max-width:768px) {
    .authentication-form-wrapper {
      margin-top: 15px;
    }
  }

  .authentication-form {
    border-radius: 2px;
    border: 1px solid #e4e5e7;
    padding: 20px;
    background: #fff;
  }

  label {
    font-weight: 400 !important;
  }

  @media screen and (max-height: 575px), screen and (min-width: 992px) and (max-width:1199px) {
    #rc-imageselect,
    .g-recaptcha {
      transform: scale(0.83);
      -webkit-transform: scale(0.83);
      transform-origin: 0 0;
      -webkit-transform-origin: 0 0;
    }
  }
  .login-error {
    text-align: center;
    color: red;
    padding: 10px 10px;
    margin-top: -22px;
  }
</style>
</head>
<body class="login_admin" >
 <div class="container">
  <div class="row">
   <div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2 authentication-form-wrapper">
    <div class="company-logo">
      <a href="<?php echo base_url('superadmin/login');?>" class="logo img-responsive">
        <img src="<?php echo base_url('uploads/company/logo.png');?>" class="img-responsive" alt="LEADFORCE">
        </a>    </div>
    <div class="mtop40 authentication-form">
      <h1>Super Admin Login</h1>
      <div class="row">
  </div>
  <?php echo form_open($this->uri->uri_string()); ?>

      <!-- <form action="" method="post" > -->
	  	<?php if(validation_errors()){ ?>
			<div class="alert alert-error login-error">
				<?php echo validation_errors(); ?>
			</div>
		<?php } ?>
		<!-- Error message start -->
		<?php if(!empty($this->session->flashdata('errormessage'))){ ?>
			<div class="flash-message">
				<div class="alert alert-error login-error">
					<?php echo $this->session->flashdata('errormessage'); ?>
				</div>
			</div>
		<?php } ?>
		<!-- Error message end -->
                  <div class="form-group">
        <label for="email" class="control-label">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" autofocus="1">
      </div>
      <div class="form-group">
        <label for="password" class="control-label">Password</label>
        <input type="password" id="password" name="password" class="form-control"></div>
               
       <div class="form-group">
        <button type="submit" class="btn btn-info btn-block" name="submit">Login</button>
      </div>
      

            </form>    </div>
  </div>
</div>
</div>
</body>
</html>

	