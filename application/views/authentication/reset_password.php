<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('authentication/includes/head.php'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<style>
body {
  font-family: "Roboto", "Helvetica Neue", Helvetica, Arial, sans-serif;
  background-color:#EEEFF8;   
  background-repeat: no-repeat;
  background-attachment: fixed;
  background-size: cover;
  font-size: 13px;
  color: #6a6c6f;
  height:auto;
  display:flex;
  flex-direction: column;
}
.box{
  display:flex;
  height:100vh;
}
.row {
  background-color: white;
  box-shadow: 0 1px 3px rgb(0 0 0 / 12%), 0 1px 2px rgb(0 0 0 / 24%); 
  border-radius:5px;
}
.container {
  margin-right: auto;
  margin-left: auto;
  margin-bottom: auto;
  margin-top: auto;
}
text{  
  padding: 5px;
  border: 0;    
  font: inherit;
  vertical-align: baseline;
}
h1 {
  font-weight: 500;
  font-size: 25px;
  margin-bottom: 15px;
  text-transform: none;
  text-align: center;
  -webkit-text-fill-color: rgb(3 18 51);
  -webkit-background-clip: text;
}
p {
  margin: 0 0 10px;
  font-size: 20px;
}
.form-wrapper{
  margin: 60px;
  border: 0;
  font-size: 100%;
  font: inherit;
  vertical-align: baseline;
  margin-top:25px;
}
.authentication-form {
  border-radius: 12px;
  border: none;
  margin-top: 50px;
  margin-bottom: 50px;
  width: 100%;
  padding: 0 80px;
}

.authentication-form-wrapper {
  margin-top: -2px; */
  position: flex;
  min-height: 1px;
  height: 735px;
  display: flex;
  width: 435px;
  position: relative;
  left: 370px;
}

input[type="#password"] {
  width: 100%;
  border: 1px solid #031233;
  border-radius: 6px;
  height: 40px;
  margin-bottom: 15px;
  outline: none;
  box-sizing: border-box;
  transition: 0.3s;
}
input[type=password]:focus {
  border: 1.5px solid #031233;
  border-radius: 3px
}
.btn-info {
  color: #fff;
  background: rgb(3 18 51);
  border: 0;
}
.btn-info:hover {
  color: #fff;
  background: #071940;
  border: 0;
}
.company-logo {
  display: block;
  width: 270px;
  margin-right: auto;
  margin-left: auto;
  margin-bottom:20px;
}
.company-logo img {
  margin-bottom: 20px;
}
.form-group {
  margin-bottom: 20px;
}
.icon{
  position :relative;
}
.fa-eye {
  position: absolute;
  top: 13px;
  right: 10px;
  cursor: pointer;
  color: #8d8484;
}
</style>

<body class="authentication reset-password">
  <div class="box">
    <div class="container">
      <div class="row">
        <div class="col-md-6 authentication-form-wrapper"> 
          <div class="mtop40 authentication-form" style="display: flex;">
            <div class="form-group" style="width: 100%;">      
                  <div class="company-logo" style="margin-top: 65px;">
                      <?php get_company_logo();?>
                  </div>       
                      <h1><?php echo _l('admin_auth_reset_password_heading'); ?></h1>
                      <?php echo form_open($this->uri->uri_string()); ?>
                      <?php echo validation_errors('<div class=" text-danger text-center">', '</div>'); ?>
                      <?php $this->load->view('authentication/includes/login_alerts'); ?>
                  <div class="form-group" app-field-wrapper="password" id="ch_password">
                      <label for="password" class="control-label">Password</label>
                    <div class=icon><input type="password" id="password" name="password" class="form-control" value="" autocomplete="new-password" fdprocessedid="0j3kjcb" aria-autocomplete="list">
                      <i class="fa-solid fa-eye" id="eye"></i>
                    </div>
                  </div>
                      <?php echo render_input('passwordr','admin_auth_reset_password_repeat','','password'); ?>
                      <button type="submit" class="btn btn-info btn-block"><?php echo _l('auth_reset_password_submit'); ?></button>
            </div>
                      <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div> 
  </div>
  <script>
    const passwordInput = document.querySelector("#password")
    const eye = document.querySelector("#eye")
    eye.addEventListener("click", function(){
    this.classList.toggle("fa-eye-slash")
    const type = passwordInput.getAttribute("type") === "password" ? "text" : "password"
    passwordInput.setAttribute("type", type)
    })
  </script>
</body>
</html>
