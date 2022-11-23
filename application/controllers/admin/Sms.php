<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sms extends AdminController
{
    public $moudle_permission_name = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('sms_model');
    }

    public function index()
    {
        if (!has_permission($this->moudle_permission_name, '', 'view')) {
            access_denied($this->moudle_permission_name);
        }
        $data =['title'=>_l('sms')];
        $data ['templates'] =$this->sms_model->getTemplates();
        $this->load->view('admin/sms/templates',$data);
    }

    public function saveTemplate()
    {
        if (!has_permission($this->moudle_permission_name, '', 'create')) {
            echo json_encode( [
                'success'=>false,
                'errors' => $this->form_validation->error_array(),
                'msg'=>'Could not save template'
            ]);
            die;
        }

        echo json_encode($this->sms_model->saveTemplate());
    }

    public function getTemplate($template_id)
    {
        $template =$this->sms_model->getTemplate($template_id);
        $template->variables=$this->sms_model->getVariablersCount($template->content);
        echo json_encode([
            'success' => true,
            'data' => $template,
        ]);
        die;

    }

    public function daffytel()
    {
        if (!has_permission($this->moudle_permission_name, '', 'edit')) {
            access_denied($this->moudle_permission_name);
        }

        if($this->input->post('access_token')){
            update_option('sms_configuration',json_encode(array('provider'=>'daffytel','access_token'=>$this->input->post('access_token'))));
            set_alert('success', _l('updated_successfully'));
        }
        $configuration =$this->sms_model->getConfig();
        if($configuration ){
            if($configuration['provider'] =='daffytel'){
                $_POST['access_token'] =$configuration['access_token'];
            }
        }
        $data =['title'=>_l('daffytel')];
        $this->load->view('admin/sms/daffytel',$data);
    }
}