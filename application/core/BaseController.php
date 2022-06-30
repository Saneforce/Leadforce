<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* * *
  This class contains the methods that are to be reused by every controller for
  formatting and returning the JSON response to the UI
 * * */

class BaseController extends CI_Controller {
    /*
      This method loads the default helper class.
     */

    public $getUserData;
    public $endAccessToken = 'sd';

    public function __construct() {
        parent::__construct();
        $this->load->model('dbmodel');        
        $this->checkUserAuth();
        $postdata = file_get_contents("php://input");
        $_POST = (array) json_decode($postdata);
        //$this->db->trans_start(); # Starting Transaction
    }

    public function checkUserAuth() {
        $request_header_params = $this->input->request_headers();
		if(!isset($request_header_params['access-token'])){
			$request_header_params['access-token'] = isset($request_header_params['Access-Token']) ? $request_header_params['Access-Token'] : '';
		}
		
        $condition_arr = array(
            'access_token' => isset($request_header_params['access-token']) ? $request_header_params['access-token'] : ''
        );
		
        $thisgetUserData = $this->dbmodel->usersTokenCheck($condition_arr);
		//echo "<pre>"; print_r($thisgetUserData); exit;
        if (is_array($thisgetUserData) && count($thisgetUserData) > 0) {
            $this->endAccessToken = $thisgetUserData['access_token'];
            $this->staffid = $thisgetUserData['staffid'];
			//$data['success']['Access'] = ' Authorized request ';
        } else {
            $data['error']['Access'] = ' Unauthorized request ';
            $this->handleError($data);
        }
    }

    public function handleSuccess($output) {
        if (isset($output['success_msg'])) {
            $output['success_msg'] = handleLangulage($output['success_msg']);
        }
        $outputArr["status"] = true;
        $outputArr["response"] = $output;
        $outputArr["access_token"] = $this->endAccessToken;
        $out = json_encode($outputArr);
        $this->db->trans_complete(); # Completing transaction
        echo $out;
        exit();
    }

    public function handleError($output) {
        $outputArr["status"] = false;
        $outputArr["error_message"] = $output;
        $out = json_encode($outputArr);
        $this->db->trans_complete(); # Completing transaction
        echo $out;
        exit();
    }

}
