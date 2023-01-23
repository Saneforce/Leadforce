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
        $data =[];
        $data['title'] ='Plugins';
        $this->load->view('admin/plugins/plugins',$data);
    }

}