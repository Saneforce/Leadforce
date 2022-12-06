<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Facebook extends AdminController
{
    public $moudle_permission_name = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('facebook_helper');
        $this->load->model('facebook_model');
    }

    public function leadads()
    {
        $data =['title'=>'Facebook Lead ads'];
        $data['leadads']  = $this->facebook_model->get_leadgen_configs();
        $this->load->view('admin/plugins/facebook/leadads',$data);
    }
    public function connectleadads()
    {
        if(isset($_POST['user_id']) && isset($_POST['page_id']) && isset($_POST['form_id']) && isset($_POST['config'])){
            $webhook =facebook_set_leadgen_webhook('415646190764150','5fee2dacf58aac13aef1918cfb6d9514');
            if(isset($webhook['success']) && $webhook['success']){
                $response =$this->facebook_model->save_leadgen_config($_POST);
                echo json_encode($response);
            }else{
                echo json_encode(array('success'=>false));
            }
            return;
        }
        $this->load->model('leads_model');
        $data =['title'=>'Facebook Lead ads'];
        $data['lead_sources']  = $this->leads_model->get_source();
        $this->load->view('admin/plugins/facebook/connectleadads',$data);
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
            $this->db->where('form_id',$this->input->get('formId'));
            if($this->db->get(db_prefix().'facebook_leadgen_configs')->row()){
                echo json_encode(array('success'=>false,'msg'=>'Selected form already connected'));
            }else{
                $forms = facebook_get_leadgen_form_details($this->input->get('formId'),$this->input->get('page_access_token'));
                echo  json_encode(array('success'=>true,'data'=>$forms));
            }
            
        }
        
    }

    public function get_page_profilelink()
    {
        if($this->input->get('pageId') && $this->input->get('page_access_token')){
            $forms = facebook_get_page_profilelink($this->input->get('pageId'),$this->input->get('page_access_token'));
            echo json_encode(array('success'=>true,'data'=>$forms));
        }
    }

    public function deleteleadad($id)
    {
        $this->facebook_model->delete_leadgen_config($id);
        redirect(admin_url('plugin/facebook/leadads'));
    }
}