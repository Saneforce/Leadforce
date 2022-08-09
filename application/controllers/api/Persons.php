<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Persons extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        load_admin_language();
        $this->load->model('Authenticationapi_model');
        $this->load->model('api_model');
        $this->load->model('projects_model');
        $postdata = file_get_contents("php://input");
        $_POST = (array) json_decode($postdata,true);
    }

    // get contacts
    public function getbydeal($id)
    {
        $contacts = $this->projects_model->get_project_contacts($id);
        $this->api_model->response_ok(true,$contacts,'');         
    }
}
