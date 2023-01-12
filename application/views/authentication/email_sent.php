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
.container {
  margin-right: auto;
  margin-left: auto;
  background: white;   
  margin-bottom: auto;
  margin-top: auto;
  width: 1185px;
  box-shadow: 0px 0px 30px rgba(0, 86, 255, 0.15);
  padding-right: 15px;
  margin-left: auto;
  left:0px;
  right:0px;
  display: flex;
  justify-content: space-around;
}
.email-sent {
  padding: 20px;
  background: #fff;
}
.company-logo {
  display: block;
  width: 245px;
  margin-right: auto;
  margin-left: auto;
  margin-bottom: 0px;
  margin-top: 0px;
}
.company-logo {
  padding: 25px 0px;
}
.company-logo img {
  margin-bottom: 50px;
}
</style>
<body class="authentication-email-sent">
  <div class="box">
    <div class="container">
      <div class="col-md-5 ">       
        <div class="email-sent" >
          <div class="company-logo"><?php echo get_company_logo(); ?></div> 
            <div class=frame-wrapper style="display: contents;"><img src="<?php echo base_url('uploads/company/email_sent.png') ?>" style="max-width: 100%;"/></div>
            <div class=text-wrapper><p style="font-size: 30px;text-align:center;color: black;">Mail delivered to your Inbox</p><p style="font-size: 16px;text-align:center;color:#4D4D4D;">Please check you mail to reset password!</p></div>
          <div style="margin-top:15px;text-align: center;"><a href="<?php echo admin_url('authentication/'); ?>"><?php echo('Back to login'); ?></a></div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>