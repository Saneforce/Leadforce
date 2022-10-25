<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Workflow extends AdminController
{
    public $moudle_permission_name = 'workflow';

    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        if (!has_permission($this->moudle_permission_name, '', 'edit')) {
            access_denied($this->moudle_permission_name);
        }
        $data =['title'=>_l('workflow_automation')];
        $this->load->view('admin/workflow/settings',$data);
    }
    
    public function flow($action)
    {
        if (!has_permission($this->moudle_permission_name, '', 'edit')) {
            access_denied($this->moudle_permission_name);
        }
        $class =$action.'_workflow';
        $data =['title'=>_l('workflow_automation')];
        $data ['workflow'] =$this->$class->getWorkFlow($action);
        $this->load->view('admin/workflow/flow',$data);
    }

    public function addflow()
    {

        echo json_encode(
            $this->workflow_model->addFlow()
        );

        
        
    }

    public function updateFlowStatus($id,$status)
    {
        if (!has_permission($this->moudle_permission_name, '', 'edit')) {
            access_denied($this->moudle_permission_name);
        }
        $this->workflow_model->updateFlowStatus($id,$status);
    }

    public function configure($id)
    {
        if (!has_permission($this->moudle_permission_name, '', 'edit')) {
            access_denied($this->moudle_permission_name);
        }

        $data =['title'=>_l('workflow_automation')];
        $flow =$this->workflow_model->getFlow($id);
        $class =$flow->action.'_workflow';
        $data['flowdetails'] =$flow;
        $data ['workflow'] =$this->$class->getWorkFlow($flow->action);
        $data ['service'] =$data['workflow']['services'][$flow->service];
        if($flow->configure){
            $configure =json_decode($flow->configure,true);
            $data['configure'] =$configure;
        }
        switch ($data['workflow']['services'][$flow->service]['medium']) {
            case 'whatsapp':
                
                $this->load->view('admin/workflow/configure/whatsapp',$data);
                break;
            case 'email':
                $this->load->view('admin/workflow/configure/email',$data);
                break;
            case 'approval':
                $this->db->where('active',1);
                $this->db->where('action_for','Active');
                $data['staffs'] =$this->db->get(db_prefix().'staff')->result_object();
                $this->db->select('count(id) as approvalLevel');
                $this->db->where('id <=',$id);
                $this->db->where('action',$flow->action);
                $this->db->where('service',$flow->service);
                $counted_approval =$this->db->get(db_prefix().'workflow')->row();
                $data['approvalLevel'] =$counted_approval->approvalLevel;
                $this->load->view('admin/workflow/configure/approval',$data);
                break;
            default:
                break;
        }
        
    }

    public function savewhatsappconfig($id)
    {
        $this->form_validation->set_rules('template', 'Template', 'required');
        if ($this->form_validation->run() == FALSE){
            echo json_encode([
                'success'=> false,
                'errors' => $this->form_validation->error_array(),
                'msg' => 'Could not save data'
            ]);
        }else{
            $variable_errors =array();
            foreach($_POST['variables'] as $key => $variable){
                if(strlen(trim($variable)) ==0){
                    $variable_errors ['varibale_'.($key+1)] ='Variable {{'.($key+1).'}} cannot be empty';
                }
            }

            if($variable_errors){
                echo json_encode([
                    'success'=> false,
                    'errors' => $variable_errors,
                    'msg' => 'Please correct the errors'
                ]);
            }else{
                $configs =json_encode(array(
                    'template'=>$this->input->post('template'),
                    'variables'=>$this->input->post('variables')
                ));
                $this->workflow_model->updateFlowConfigure($id,$configs);
                echo json_encode([
                    'success'=> true,
                    'msg' => _l('updated_successfully')
                ]);
            }
            
        }
    }

    public function deleteflow($id)
    {
        if (!has_permission($this->moudle_permission_name, '', 'edit')) {
            access_denied($this->moudle_permission_name);
        }
        
        $this->workflow_model->deleteFlow($id);
        echo json_encode([
            'success'=> true,
            'msg' => _l('deleted_successfully')
        ]);
    }

    public function saveapprovalconfig($id)
    {
        $this->form_validation->set_rules('approver', 'Assing approver', 'required');
        if ($this->form_validation->run() == FALSE){
            echo json_encode([
                'success'=> false,
                'errors' => $this->form_validation->error_array(),
                'msg' => 'Could not save data'
            ]);
        }else{
            $configs =json_encode(array(
                'approver'=>$this->input->post('approver'),
            ));
            $this->workflow_model->updateFlowConfigure($id,$configs);
            echo json_encode([
                'success'=> true,
                'msg' => _l('updated_successfully')
            ]);
        }
    }
    
    public function saveemailconfig($id)
    {
        $this->form_validation->set_rules('subject', 'Subject', 'required');
        $this->form_validation->set_rules('fromname', 'Fromname', 'required');
        $this->form_validation->set_rules('message', 'Message', 'required');
        if ($this->form_validation->run() == FALSE){
            echo json_encode([
                'success'=> false,
                'errors' => $this->form_validation->error_array(),
                'msg' => 'Could not save data'
            ]);
        }else{
            $configs =json_encode(array(
                'subject'=>$this->input->post('subject'),
                'fromname'=>$this->input->post('fromname'),
                'plaintext'=>$this->input->post('plaintext'),
                'message'=>$this->input->post('message'),
            ));
            $this->workflow_model->updateFlowConfigure($id,$configs);
            echo json_encode([
                'success'=> true,
                'msg' => _l('updated_successfully')
            ]);
        }
    }
}
