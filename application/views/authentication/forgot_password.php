<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('authentication/includes/head.php'); ?>
<style>
body {
  font-family: "Roboto", "Helvetica Neue", Helvetica, Arial, sans-serif;
  background-color:#EEEFF8;   
  background-repeat: no-repeat;
  background-attachment: fixed;
  background-size: cover;
  font-size: 13px;
  color: #6a6c6f;
  margin: 0;
  padding: 0;
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
  margin-right: 70px;
  margin-left: 60px;
}
.container {
  margin-right: auto;
  margin-left: auto;
  background: white;   
  margin-bottom: auto;
  margin-top: auto;
  width: 1185px;
  box-shadow: 0px 0px 30px rgba(0, 86, 255, 0.15);
}
.container {
  padding-right: 15px;
  margin-left: auto;
  left:0px;
  right:0px;
  display: flex;
  justify-content: space-around;
}
text{  
  padding: 5px;
  border: 0;    
  font: inherit;
  vertical-align: baseline;
}
.form-wrapper{
  margin: 0;
  border: 0;
  font-size: 100%;
  font: inherit;
  vertical-align: baseline;
  margin-top:30px;
}
.authentication-form {
  border-radius: 2px;
  border: none;
  margin-top: 50px;
  margin-bottom: 50px;
  width: 100%;
  padding: 0 80px;
}
.authentication-form-wrapper {
  margin-top: 25px;
}
p{
  color:#4D4D4D;
}
input[type="email"] {
  width: 100%;
  border: 1px solid #A3BFEC;
  border-radius: 4px;
  margin-bottom: 15px;
  outline: none;
  box-sizing: border-box;
  transition: 0.3s;
}
input[type="email"]:focus {
  border: 1px solid #A3BFEC;
  border-radius: 3px; 
}
.btn-info {
  color: #fff;
  background: rgb(3 18 51);
}
.btn-info:hover {
  color: #fff;
  background: #071940;
}
.company-logo {
  display: block;
  width: 245px;
  margin-right: auto;
  margin-left: auto;
}
.company-logo {
  padding: 0px 0px;
}
.company-logo img {
  margin-bottom: 20px;
}
.icon{
  position :relative;
}
.icon svg {
  position: absolute;
  left: 10px;
  top: 11px;
  color: #031233;
}
.arrow{
  position :relative;
}
.arrow svg {
  position: absolute;
  right: 80px;
  top: 2px;
  color: #031233;
}
p {
  margin: 0 0 10px;
  font-size: 16px;
}
</style>
<body class="authentication forgot-password">
<div class="box">
  <div class="container">
    <div class="col-md-5 authentication-form-wrapper">       
      <div class="authentication-form" >
        <div class="company-logo">
            <?php echo get_company_logo(); ?>
        </div>
      <div class=form-wrapper> 
        <div>
            <img src="<?php echo base_url('uploads/company/forgot_password.png') ?>" style="max-width: 100%;"/>
        </div>
            <p style="font-size: 30px;text-align:center;color: black;">Forgot Password?</p>
            <p style="font-size: 16px;text-align:center;">No worries, We’ll help you!!</p>
            <?php $this->load->view('authentication/includes/login_alerts'); ?>
            <?php echo form_open($this->uri->uri_string()); ?>
            <?php echo validation_errors('<p class=" text-danger text-center">', '</p>'); ?>
      <div class="form-group">
        <div class=icon>
            <input type="email" id="email" name="email" class="form-control" autofocus="1" placeholder="Email"style="padding-left: 35px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="13" viewBox="0 0 14 13" fill="none">
            <path d="M11.0833 0H2.91667C2.1434 0.00092625 1.40208 0.308515 0.855295 0.855295C0.308514 1.40208 0.00092625 2.1434 0 2.91667L0 9.91667C0.00092625 10.6899 0.308514 11.4313 0.855295 11.978C1.40208 12.5248 2.1434 12.8324 2.91667 12.8333H11.0833C11.8566 12.8324 12.5979 12.5248 13.1447 11.978C13.6915 11.4313 13.9991 10.6899 14 9.91667V2.91667C13.9991 2.1434 13.6915 1.40208 13.1447 0.855295C12.5979 0.308515 11.8566 0.00092625 11.0833 0ZM2.91667 1.16667H11.0833C11.4326 1.16735 11.7737 1.27255 12.0627 1.46872C12.3517 1.66489 12.5754 1.94305 12.705 2.26742L8.23783 6.73517C7.90908 7.0626 7.46399 7.24644 7 7.24644C6.53601 7.24644 6.09092 7.0626 5.76217 6.73517L1.295 2.26742C1.42459 1.94305 1.64827 1.66489 1.93728 1.46872C2.22628 1.27255 2.56738 1.16735 2.91667 1.16667ZM11.0833 11.6667H2.91667C2.45254 11.6667 2.00742 11.4823 1.67923 11.1541C1.35104 10.8259 1.16667 10.3808 1.16667 9.91667V3.79167L4.93733 7.56C5.48487 8.10615 6.22665 8.41286 7 8.41286C7.77335 8.41286 8.51513 8.10615 9.06267 7.56L12.8333 3.79167V9.91667C12.8333 10.3808 12.649 10.8259 12.3208 11.1541C11.9926 11.4823 11.5475 11.6667 11.0833 11.6667Z" fill="#031233"/>
            </svg>
        </div>
      </div>
      <div class="form-group">
          <button type="submit" class="btn btn-info btn-block"><?php echo('SUBMIT'); ?></button>
      </div>
      <div class=arrow style="margin-top:15px;text-align: right;">
          <a href="<?php echo admin_url('authentication/'); ?>"><?php echo('Back to login'); ?>
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="12" viewBox="0 0 15 12" fill="none">
          <path d="M0.46967 5.46967C0.176777 5.76256 0.176777 6.23744 0.46967 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.46967 5.46967ZM15 5.25L1 5.25V6.75L15 6.75V5.25Z" fill="#4C70AB"/>
          </svg></a>
      </div>    
          <?php echo form_close(); ?>
    </div>
  </div>
</div>
</body>
</html>
