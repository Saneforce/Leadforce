<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Authentication extends App_Controller
{
    public function __construct()
    {
        parent::__construct();

        load_admin_language();
		$this->load->model('Authenticationapi_model');
		$this->load->model('api_model');
		$postdata = file_get_contents("php://input");
        $_POST = (array) json_decode($postdata);
    }

    public function login()
    {
        if ($_POST) {
			$email    = $_POST['email'];
			$password = $_POST['password'];
			$domain	 = $_POST['domain'];
			$remember = false;
			$data = $this->Authenticationapi_model->login($email, $password,$domain, $remember, true);
			if (is_array($data) && isset($data['memberinactive'])) {
				$this->api_model->response_bad_request(false,[],_l('admin_auth_inactive_account'));
			} elseif ($data == false) {
				$this->api_model->response_bad_request(false,[],_l('admin_auth_invalid_email_or_password')._l('or_invalid_domain'));
			} else {
				$this->api_model->response_ok(true,$data,'');
			}
        }
	}
	
	public function forgotpassword()
    {
        if (is_staff_logged_in()) {
            redirect(admin_url());
		}
        if ($_POST['email']) {
			$success = $this->Authenticationapi_model->forgot_password($_POST['email'], true);
			if (is_array($success) && isset($success['memberinactive'])) {
				$this->api_model->response_bad_request(false,[],_l('inactive_account'));
			} elseif ($success == true) {
				$this->api_model->response_ok(true,[],_l('check_email_for_resetting_password'));
			} else {
				$this->api_model->response_bad_request(false,[],_l('error_setting_new_password_key'));
			}
			echo $out;
			exit;
        }
    }

}
