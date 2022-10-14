<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Whatsapp extends AdminController
{
    public $moudle_permission_name = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('whatsapp_model');
        $this->load->helper('whatsapp_helper');
    }

    public function index()
    {
        if($this->input->post('business_id')){
            if (!has_permission($this->moudle_permission_name, '', 'edit')) {
                access_denied($this->moudle_permission_name);
            }
            echo json_encode($this->whatsapp_model->saveSettings());
            die;
        }
        
        if (!has_permission($this->moudle_permission_name, '', 'view')) {
            access_denied($this->moudle_permission_name);
        }
        $data =array('title'=>'whatsapp_settings');
        $data['settings'] =$this->whatsapp_model->getSettings();
        $this->load->view('admin/whatsapp/settings',$data);
    }

    public function gettemplates()
    {
        
        $templates =whatsapp_get_templates();
        if(is_array($templates) && !empty($templates)){
            echo json_encode([
                'success' => true,
                'data' => $templates,
            ]);
        }else{
            echo json_encode([
                'success' => false,
                'data' => array(),
            ]);
        }
    }

    public function gettemplate($template_name)
    {
        $templates =whatsapp_get_templates();
        
        if(is_array($templates) && !empty($templates)){
            foreach($templates as $template){
                if($template->name ==$template_name){
                    
                    foreach($template->components as $key => $component){
                        $template->components[$key]->variables=whatsapp_count_variables($component->text);
                    }
                    echo json_encode([
                        'success' => true,
                        'data' => $template,
                    ]);
                    die;
                }
            }
        }
        echo json_encode([
            'success' => false,
            'msg' =>'Could not get template try again later',
            'data' => array(),
        ]);
    }
}