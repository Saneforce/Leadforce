<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Relationdata extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        load_admin_language();
        $this->load->model('Authenticationapi_model');
        $this->load->model('api_model');
        $this->load->model('misc_model');
        $postdata = file_get_contents("php://input");
        $_POST = (array) json_decode($postdata,true);
    }

    // get staffs
    public function getall($type,$rel_id='')
    {
        $data = get_relation_data($type);
        $relOptions = init_relation_options($data, $type, $rel_id);
        $this->api_model->response_ok(true,$relOptions,'');       
    }
}
