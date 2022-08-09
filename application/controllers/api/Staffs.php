<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Staffs extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        load_admin_language();
        $this->load->model('Authenticationapi_model');
        $this->load->model('staff_model');
        $this->load->model('api_model');
        $postdata = file_get_contents("php://input");
        $_POST = (array) json_decode($postdata,true);
    }

    // get staffs
    public function getall()
    {
        $staffs =$this->staff_model->get('', ['active' => 1]);
        if($staffs){
            $new_staffs =array();
            foreach($staffs as $staff){
                unset($staff['password']);
                unset($staff['new_pass_key']);
                unset($staff['new_pass_key_requested']);
                $new_staffs [] = $staff;
            }
            $this->api_model->response_ok(true,$new_staffs,'');
        }else{
            $this->api_model->response_ok(true,[],'No records found');
        }
               
    }
}
