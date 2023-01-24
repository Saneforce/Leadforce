<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Plugins extends AdminController
{
    public $moudle_permission_name = 'settings';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if(!is_admin()){
            access_denied();
        }
        $data =[];
        $data['title'] ='Plugins';
        $this->load->view('admin/plugins/plugins',$data);
    }

}