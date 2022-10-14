<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Whatsapp_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function saveSettings()
    {
        $this->form_validation->set_rules('active', 'Enable Whatsapp', 'required|max[200]');
        $this->form_validation->set_rules('business_id', 'Business ID', 'required|max[200]');
        $this->form_validation->set_rules('phonenumber_id', 'Phone number ID', 'required|max[200]');
        $this->form_validation->set_rules('user_access_token', 'Access Token', 'required');
        $this->form_validation->set_rules('waba_id', 'WhatsApp Business Account ID', 'required|max[200]');

        if ($this->form_validation->run() == FALSE){
            return [
                'success'=> false,
                'errors' => $this->form_validation->error_array(),
                'msg' => _l('whats_app_problem_to_save_data')
            ];
        }else{
            update_option('whatsapp_enabled',$this->input->post('active'));
            $settings =array(
                'business_id'=>$this->input->post('business_id'),
                'phonenumber_id'=>$this->input->post('phonenumber_id'),
                'user_access_token'=>$this->input->post('user_access_token'),
                'waba_id'=>$this->input->post('waba_id'),
            );
            update_option('whatsapp_options',json_encode($settings));
            return [
                'success'=> true,
                'msg' => _l('whats_app_account_saved')
            ];
        }
    }

    public function getSettings()
    {
        $options =get_option('whatsapp_options');
        if($options){
            return json_decode($options,true);
        }
        return array(
            'business_id'=>'',
            'phonenumber_id'=>'',
            'user_access_token'=>'',
            'waba_id'=>'',
            'active'=>0
        );
    }
    
    public function isActive()
    {
        if(get_option('whatsapp_enabled') === 1){
            return true;
        }
        return false;
    }
}