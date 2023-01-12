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
  color: white;
  margin-bottom: auto;
  margin-top: auto;
  width: 1260px;
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
h5 {
  margin-bottom: 15px 113px;
  text-transform: none;
  text-align: center;
  color:rgb(87 121 176);
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
  border-radius: 5px;
  border:none;
  margin-top:auto;
  margin-bottom:auto;
  width:100%;
  padding:0 50px;
}
.authentication-form-wrapper {
  margin-top: -2px;
  position: flex;
  min-height: 1px;
  padding-right: 50px;
  padding-left: 50px; 
  height:700px;   
  display:flex;
}
input[type="email"] {
  width: 100%;
  border: 1px solid #A3BFEC;
  border-radius: 6px;
  height: 40px;
  margin-bottom: 24px;
  outline: none;
  box-sizing: border-box;
  transition: 0.3s;
}
input[type="email"]:focus {
  border: 1.5px solid #A3BFEC;
  border-radius: 3px; 
}
#password {
  width: 100%;
  border: 1px solid #A3BFEC;
  border-radius: 6px;
  height: 40px;
  margin-bottom: 15px;
  outline: none;
  box-sizing: border-box;
  transition: 0.3s;
}
input[type=password]:focus {
  border: 1.5px solid #1966fc;
  border-radius: 3px
}
.icon{
  position :relative;
}
.icon svg {
  position: absolute;
  left: 10px;
  top: 15px;
  color:  #031233;
}
.fa-eye {
  position: absolute;
  top: 13px;
  right: 10px;
  cursor: pointer;
  color: #8d8484;
}
.btn-info {
  color: #fff;
  background: rgb(3 18 51);
  border: 0;
}
.btn-info:hover,.btn-info.active, .btn-info:active, .open > .dropdown-toggle.btn-info,.btn-info:focus,.btn-info.active.focus, .btn-info.active:focus, .btn-info.active:hover, .btn-info:active.focus, .btn-info:active:focus, .btn-info:active:hover, .open > .dropdown-toggle.btn-info.focus, .open > .dropdown-toggle.btn-info:focus, .open > .dropdown-toggle.btn-info:hover {
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
.carousel-wrapper{ 
  background-color:rgb(3 18 51);
  height:700px;
}
.carousel {
  position: absolute;  
  display: grid;
  grid-template-rows: auto ;
  padding-bottom: 58px;
  overflow: hidden;
  transition: 0.8s ease-in-out;
}
.images-wrapper {
  display: flex;
  height:100%;
}
.image {
  width: 100%;
  grid-column: 1/2;
  grid-row: 1/2;
  opacity: 0;
  transition: opacity 0.3s, transform 0.5s;
}
.image.show {
  opacity: 1;
  transform: none;
}
p {
  margin: 0 0 10px;
  font-size: 20px;
}
.text-slider {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  bottom: 48px;
  position: absolute;
  width: 100%;
}
.text-wrap {
  max-height: 25px;
  overflow: hidden;
  margin-bottom: 40px;
}
.text-wrapper{
  position: absolute;
  width: 500px;
  left: 70px;
  top: 40px;
  font-weight: 500;
  font-size: 26px;
  line-height: 47px;
  display: flex;
  align-items: center;
  text-align: center;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  color: #FFFFFF;
}
.text-group {
  display: flex;
  flex-direction: column;
  text-align: center;
  transform: translateY();
  transition: 0.3s;
} 
.bullets {
  display: flex;
  align-items: center;
  justify-content: center;
  padding-bottom: 20px;
}
.bullets span {
  display: block;
  width: 0.5rem;
  height: 0.5rem;
  background-color: #aaa;
  margin: 0 0.25rem;
  border-radius: 50%;
  cursor: pointer;
  transition: 0.3s;
}
.bullets span.active {
  width: 1.1rem;
  background-color: #151111;
  border-radius: 1rem;
}

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
  border: 5px solid rgba(3, 18, 51, 1);
  border-radius: 100%;
  border-bottom-color: transparent;
  -webkit-animation: spinner 2s linear 0s infinite;
  animation: spinner 2s linear 0s infinite;
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
<body class="login_admin"<?php if(is_rtl()){ echo ' dir="rtl"'; } ?> >
<div class=box>
  <div class="container">
    <div id="overlay" style="display: none;"><div class="spinner"></div></div>
      <div class="row">

        <div class="col-md-6 carousel-wrapper"> 
            <div class="carousel-wrapper"> 
                  <div class="text-wrapper">
                      <p>With great cheerfulness, zeal and brightest rays of joy and hope!!!</p>
                  </div>       
                  <div class="images-wrapper">
                      <img src="<?php echo base_url('uploads/company/login_banner.png') ?>" class="image img-1 show" alt="" />
                  </div>
            </div>
        </div>  
            <div class="col-md-6 authentication-form-wrapper"> 
              <div class="mtop40 authentication-form">
                  <div class="company-logo">
                      <?php get_company_logo();?>
                  </div>
                  <div class=text>
                    <h1>Get Started</h1>
                    <h5>Please enter credentials to login!</h5>
                  </div>
                  <div class=form-wrapper>
                          <?php $this->load->view('authentication/includes/login_alerts'); ?>
                          <?php echo form_open($this->uri->uri_string(),array('class'=>'','id'=>'form_id','onsubmit'=>'log_form()')); ?>
                          <?php echo validation_errors('<div class="alert alert-danger text-center" >', '</div>'); ?>
                          <?php hooks()->do_action('after_admin_login_form_start'); ?> 

                        <div class="form-group">
                            <div class=icon>
                              <input type="email" id="email" name="email" class="form-control" autofocus="1" placeholder="Email"style="padding-left: 35px;">
                              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="13" viewBox="0 0 14 13" fill="none"><path d="M11.0833 0H2.91667C2.1434 0.00092625 1.40208 0.308515 0.855295 0.855295C0.308514 1.40208 0.00092625 2.1434 0 2.91667L0 9.91667C0.00092625 10.6899 0.308514 11.4313 0.855295 11.978C1.40208 12.5248 2.1434 12.8324 2.91667 12.8333H11.0833C11.8566 12.8324 12.5979 12.5248 13.1447 11.978C13.6915 11.4313 13.9991 10.6899 14 9.91667V2.91667C13.9991 2.1434 13.6915 1.40208 13.1447 0.855295C12.5979 0.308515 11.8566 0.00092625 11.0833 0ZM2.91667 1.16667H11.0833C11.4326 1.16735 11.7737 1.27255 12.0627 1.46872C12.3517 1.66489 12.5754 1.94305 12.705 2.26742L8.23783 6.73517C7.90908 7.0626 7.46399 7.24644 7 7.24644C6.53601 7.24644 6.09092 7.0626 5.76217 6.73517L1.295 2.26742C1.42459 1.94305 1.64827 1.66489 1.93728 1.46872C2.22628 1.27255 2.56738 1.16735 2.91667 1.16667ZM11.0833 11.6667H2.91667C2.45254 11.6667 2.00742 11.4823 1.67923 11.1541C1.35104 10.8259 1.16667 10.3808 1.16667 9.91667V3.79167L4.93733 7.56C5.48487 8.10615 6.22665 8.41286 7 8.41286C7.77335 8.41286 8.51513 8.10615 9.06267 7.56L12.8333 3.79167V9.91667C12.8333 10.3808 12.649 10.8259 12.3208 11.1541C11.9926 11.4823 11.5475 11.6667 11.0833 11.6667Z" fill="#031233"/></svg>
                            </div>
                            <div class=icon><input type="password" id="password" name="password" class="form-control" placeholder="Password"style="padding-left: 35px;">
                              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="14" viewBox="0 0 12 14" fill="none">
                              <path d="M10.1462 4.914V4.08333C10.1462 3.00037 9.70932 1.96175 8.93177 1.19598C8.15421 0.430207 7.09962 0 6 0C4.90037 0 3.84578 0.430207 3.06822 1.19598C2.29067 1.96175 1.85384 3.00037 1.85384 4.08333V4.914C1.32631 5.14074 0.877301 5.51397 0.561723 5.98804C0.246145 6.46211 0.0776767 7.01647 0.0769196 7.58333V11.0833C0.0778601 11.8566 0.39018 12.5979 0.945373 13.1447C1.50057 13.6915 2.2533 13.9991 3.03846 14H8.96153C9.74669 13.9991 10.4994 13.6915 11.0546 13.1447C11.6098 12.5979 11.9221 11.8566 11.9231 11.0833V7.58333C11.9223 7.01647 11.7538 6.46211 11.4383 5.98804C11.1227 5.51397 10.6737 5.14074 10.1462 4.914ZM3.03846 4.08333C3.03846 3.30979 3.35048 2.56792 3.90587 2.02094C4.46127 1.47396 5.21455 1.16667 6 1.16667C6.78545 1.16667 7.53872 1.47396 8.09412 2.02094C8.64952 2.56792 8.96153 3.30979 8.96153 4.08333V4.66667H3.03846V4.08333ZM10.7385 11.0833C10.7385 11.5475 10.5512 11.9926 10.218 12.3208C9.88477 12.649 9.4328 12.8333 8.96153 12.8333H3.03846C2.56719 12.8333 2.11522 12.649 1.78198 12.3208C1.44875 11.9926 1.26153 11.5475 1.26153 11.0833V7.58333C1.26153 7.1192 1.44875 6.67408 1.78198 6.3459C2.11522 6.01771 2.56719 5.83333 3.03846 5.83333H8.96153C9.4328 5.83333 9.88477 6.01771 10.218 6.3459C10.5512 6.67408 10.7385 7.1192 10.7385 7.58333V11.0833Z" fill="#031233"/></svg>
                              <i class="fa-solid fa-eye" id="eye"></i>
                            </div>
                        </div>

                              <?php if(get_option('recaptcha_secret_key') != '' && get_option('recaptcha_site_key') != ''){ ?><div class="g-recaptcha" data-sitekey="<?php echo get_option('recaptcha_site_key'); ?>"></div><?php } ?>                                      
                      
                        <div class="form-group">

                            <div class="checkbox">
                                  <label for="remember" style="color:black;">
                                  <input type="checkbox" id="remember" name="remember"> Remember me</label>
                            </div>

                                  <button type="submit" class="btn btn-info btn-block"><?php echo _l('admin_auth_login_button'); ?></button>

                            <div class="form-group text-right" style="margin-top:15px">
                                  <a href="<?php echo admin_url('authentication/forgot_password'); ?>"><?php echo _l('admin_auth_login_fp'); ?></a>
                            </div>
                            
                        </div>
                              <?php hooks()->do_action('before_admin_login_form_close'); ?><?php echo form_close(); ?>
                    </div>
                </div>
           </div>
       </div>
    </div>
  </div>
<script>
function log_form(){
document.getElementById('overlay').style.display = '';
}
const inputs = document.querySelectorAll(".input-field");
const main = document.querySelector("main");
const bullets = document.querySelectorAll(".bullets span");
const images = document.querySelectorAll(".image");
const passwordInput = document.querySelector("#password")
const eye = document.querySelector("#eye")

inputs.forEach((inp) => {
inp.addEventListener("focus", () => {
inp.classList.add("active");
});
inp.addEventListener("blur", () => {
if (inp.value != "") return;
inp.classList.remove("active");
});
});


let currentIndex = 0;
const interval = 4000; 

function moveSlider() {
let index = this.dataset.value;

let currentImage = document.querySelector(`.img-${index}`);
images.forEach((img) => img.classList.remove("show"));
currentImage.classList.add("show");

const textSlider = document.querySelector(".text-group");
textSlider.style.transform = `translateY(${-(index - 1) * 40}px)`;

bullets.forEach((bull) => bull.classList.remove("active"));
this.classList.add("active");
}

function autoSlide() {
currentIndex++;
if (currentIndex > bullets.length - 1) {
currentIndex = 0;
}
bullets[currentIndex].click();
}

setInterval(autoSlide, interval)

bullets.forEach((bullet) => {
bullet.addEventListener("click", moveSlider);
});
eye.addEventListener("click", function(){
this.classList.toggle("fa-eye-slash")
const type = passwordInput.getAttribute("type") === "password" ? "text" : "password"
passwordInput.setAttribute("type", type)
})


</script>
</body>
</html>
