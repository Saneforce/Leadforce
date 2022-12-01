<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Facebook extends AdminController
{
    public $moudle_permission_name = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('facebook_helper');
        $this->load->model('plugins_model');
    }

    public function leadads($id=0)
    {
        if(isset($_POST['page'])){
            $this->plugins_model->add_config('facebook_leadads',$_POST);
            echo json_encode(array('success'=>true));
            return;
        }
        $this->load->model('leads_model');
        $data =['title'=>'Facebook Lead ads'];
        $data['lead_sources']  = $this->leads_model->get_source();
        $data['config_id'] =$id;
        if($id >0){
            $data['facebook_leadads_configs']  = $this->leads_model->get_config($id);
        }
        $this->load->view('admin/plugins/facebook/leadads',$data);
    }

    public function get_pages()
    {
        if($this->input->get('userID') && $this->input->get('access_token')){
            $pages = facebook_get_pages($this->input->get('userID'),$this->input->get('access_token'));
            echo  json_encode(array('success'=>true,'data'=>$pages));
        }
        
    }

    public function get_leadgen_forms()
    {
        if($this->input->get('pageId') && $this->input->get('page_access_token')){
            $forms = facebook_get_leadgen_forms($this->input->get('pageId'),$this->input->get('page_access_token'));
            echo  json_encode(array('success'=>true,'data'=>$forms));
        }
        
    }

    public function get_leadgen_form_details()
    {
        if($this->input->get('formId') && $this->input->get('page_access_token')){
            $forms = facebook_get_leadgen_form_details($this->input->get('formId'),$this->input->get('page_access_token'));
            echo  json_encode(array('success'=>true,'data'=>$forms));
        }
        
    }
}