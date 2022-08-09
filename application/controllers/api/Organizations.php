<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Organizations extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        load_admin_language();
        $this->load->model('Authenticationapi_model');
        $this->load->model('api_model');
        $postdata = file_get_contents("php://input");
        $_POST = (array) json_decode($postdata,true);
    }

    // get staffs
    public function getbydeal($id)
    {
        $deal =$this->db->get(db_prefix() .'projects', ['id' => $id])->row();
        if($deal){
            if($deal->clientid){
                $organization =$this->db->get(db_prefix() .'clients', ['userid' => $deal->clientid,'active' => 1])->row();
                if($organization){
                    $this->api_model->response_ok(true,$organization,'');
                }else{
                    $this->api_model->response_ok(true,[],'No records found');
                }
            }else{
                $this->api_model->response_ok(true,[],'No records found');
            }
            $this->api_model->response_ok(true,$new_staffs,'');
        }else{
            $this->api_model->response_ok(true,[],'No records found');
        }
               
    }
}
