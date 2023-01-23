<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Acres99 extends AdminController
{
    public $moudle_permission_name = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('plugins_model');
    }

    public function index()
    {

        if($this->input->post()){
            $this->plugins_model->save_config('99acres_lead',['web_form_id'=>$this->input->post('web_form')]);
            set_alert('success', 'Form Saved successfully');
        }
        $data =[];
        $data['title'] ='99Acres';
        $config =$this->plugins_model->get_config_by_plugin('99acres_lead');
        $data['configured_web_forms'] ='';
        if($config){
            $data['configured_web_forms'] =$config->config['web_form_id'];
        }
        $data['web_forms'] =$this->db->get(db_prefix().'web_to_lead')->result_array();
        $this->load->view('admin/plugins/acres99/acres99',$data);
    }

    public function config()
    {
        $config =$this->plugins_model->get_config_by_plugin('99acres_lead');
        $data['configured_web_forms'] ='';
        if($config){
            $this->db->where('id',$config->config['web_form_id']);
            $web_form =$this->db->get(db_prefix().'web_to_lead')->row();
            if($web_form){
                $data =array('web_form'=>$web_form);
                $this->load->view('admin/plugins/acres99/configuration_view',$data);
            }
        }

    }
}