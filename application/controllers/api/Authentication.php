<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Authentication extends App_Controller
{
    public function __construct()
    {
        parent::__construct();

        load_admin_language();
		$this->load->model('Authenticationapi_model');
		$postdata = file_get_contents("php://input");
        $_POST = (array) json_decode($postdata);
    }

    public function loginapi()
    {
        if ($_REQUEST) {
			$email    = $_REQUEST['email'];
			$password = $_REQUEST['password'];
			$domain	 = $_REQUEST['domain'];
			$remember = false;
			$data = $this->Authenticationapi_model->login($email, $password,$domain, $remember, true);
			if (is_array($data) && isset($data['memberinactive'])) {
				$outputArr["status_code"] = 400;
				$outputArr["status"] = false;
				$outputArr["error_message"] =  _l('admin_auth_inactive_account');
				$out =json_encode($outputArr);
			} elseif ($data == false) {
				$outputArr["status_code"] = 400;
				$outputArr["status"] = false;
				$outputArr["error_message"] =  _l('admin_auth_invalid_email_or_password')._l('or_invalid_domain');
				$out =json_encode($outputArr);
			} else {
				$outputArr["status_code"] = 200;
				$outputArr["status"] = true;
				$outputArr["response"] = $data;
				$out =json_encode($outputArr);
			}
			echo $out;
			exit;
        }
	}
	
	public function forgot_password()
    {
        if (is_staff_logged_in()) {
            redirect(admin_url());
		}
		//echo "<pre>"; print_r($_POST); exit;
        if ($_POST['email']) {
			$success = $this->Authenticationapi_model->forgot_password($_POST['email'], true);
			if (is_array($success) && isset($success['memberinactive'])) {
				$outputArr["status_code"] = 400;
				$outputArr["status"] = false;
				$outputArr["error_message"] =  _l('inactive_account');
				$out =json_encode($outputArr);
			} elseif ($success == true) {
				$outputArr["status_code"] = 200;
				$outputArr["status"] = true;
				$outputArr["response"] = _l('check_email_for_resetting_password');
				$out =json_encode($outputArr);
			} else {
				$outputArr["status_code"] = 400;
				$outputArr["status"] = false;
				$outputArr["error_message"] =  _l('error_setting_new_password_key');
				$out =json_encode($outputArr);
			}
			echo $out;
			exit;
        }
    }

}
