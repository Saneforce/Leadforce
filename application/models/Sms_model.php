<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sms_model extends App_Model
{
    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function getTemplates()
    {
        $templates =$this->db->get(db_prefix().'sms_templates')->result_object();
        return $templates;
    }

    public function getTemplate($template_id)
    {
        $this->db->where('template_id',$template_id);
        $template =$this->db->get(db_prefix().'sms_templates')->row();
        return $template;
    }

    public function saveTemplate()
    {
        $this->form_validation->set_rules('name', 'Template name', 'required|is_unique['.db_prefix().'sms_templates.name]');
        $this->form_validation->set_rules('template_id', 'Template Id', 'required|is_unique['.db_prefix().'sms_templates.template_id]');
        $this->form_validation->set_rules('sender', 'Sender', 'required');
        $this->form_validation->set_rules('route', 'Route', 'required');
        $this->form_validation->set_rules('content', 'Content', 'required');
        if ($this->form_validation->run() == FALSE){
            return [
                'success'=>false,
                'errors' => $this->form_validation->error_array(),
                'msg'=>'Could not save template'
            ];
        }else{
            $data =array(
                'name'=>$this->input->post('name'),
                'template_id'=>$this->input->post('template_id'),
                'sender'=>$this->input->post('sender'),
                'route'=>$this->input->post('route'),
                'content'=>$this->input->post('content'),
            );
            $this->db->insert(db_prefix().'sms_templates',$data);
            return array(
                'success'=>true,
                'msg'=>'Template added successfully'
            );
        }
    }

    public function getVariablersCount($message)
    {
        preg_match_all("/\{([^W]+?)\}/", $message, $result);
        
        if(isset($result[0])){
            return count($result[0]);
        }
        return 0;
    }

    public function getConfig()
    {
        $configuration =get_option('sms_configuration');
        if($configuration){
            return json_decode($configuration,true);
        }
        return array();
    }

    public function deleteTemplate($id)
    {
        $this->db->where('id',$id);
        $this->db->delete(db_prefix().'sms_templates');
    }
}