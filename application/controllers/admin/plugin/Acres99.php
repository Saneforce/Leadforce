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
        if(!is_admin()){
            access_denied();
        }
        if($this->input->post()){
            $this->plugins_model->add_config('99acres_lead',['web_form_id'=>$this->input->post('web_form')]);
            set_alert('success', 'Form Saved successfully');
        }
        $data =[];
        $data['title'] ='99Acres';
        $configs =$this->plugins_model->get_configs_by_plugin('99acres_lead');
        $web_forms =$this->db->get(db_prefix().'web_to_lead')->result_array();
        $data['web_forms'] =$data['configs'] =[];
        if($web_forms){
            foreach($web_forms as $web_form){
                $data['web_forms'] [$web_form['id']] =$web_form;
            }
        }
        foreach($configs as $key => $config){
            if(isset($data['web_forms'][$config->config['web_form_id']])){
                $config->form_name =$data['web_forms'][$config->config['web_form_id']]['name'];
                $data['configs'][]=$config;
            }
        }
        $this->load->view('admin/plugins/acres99/acres99',$data);
    }

    public function config($id)
    {
        if(!is_admin()){
            access_denied();
        }
        $config =$this->plugins_model->get_config($id);
        if($config){
            $this->db->where('id',$config->config['web_form_id']);
            $web_form =$this->db->get(db_prefix().'web_to_lead')->row();
            if($web_form){
                $data =array('web_form'=>$web_form,'configure_id'=>$id);
                $this->load->view('admin/plugins/acres99/configuration_view',$data);
            }
        }
    }

    public function deleteconfig($id)
    {
        if(!is_admin()){
            access_denied();
        }
        $this->plugins_model->delete($id);
        set_alert('success', 'Form removed successfully');
        redirect(admin_url('plugin/acres99'));
    }
}