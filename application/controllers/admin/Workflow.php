<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Workflow extends AdminController
{
    public $moudle_permission_name = 'settings';

    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        if (!has_permission($this->moudle_permission_name, '', 'view')) {
            access_denied($this->moudle_permission_name);
        }
        $data =['title'=>'workflow_automation'];
        $this->load->view('admin/workflow/settings',$data);
    }
    
    public function flow($action)
    {
        if (!has_permission($this->moudle_permission_name, '', 'view')) {
            access_denied($this->moudle_permission_name);
        }
        $class =$action.'_workflow';
        $data =['title'=>'workflow_automation'];
        $data ['workflow'] =$this->$class->getWorkFlow($action);
        $this->load->view('admin/workflow/flow',$data);
    }

    public function addflow()
    {
        if (!has_permission($this->moudle_permission_name, '', 'create')) {
            echo json_encode(
                array(
                    'success'=>false,
                    'msg'=>'Could not add Flow'
                )
            );
            die;
        }
        if($this->input->post('action') && $this->input->post('service')){
            $this->workflow_model->addFlow($this->input->post('action'),$this->input->post('service'));
            echo json_encode(
                array(
                    'success'=>true,
                    'msg'=>'Flow added successfully'
                )
            );

        }else{
            echo json_encode(
                array(
                    'success'=>false,
                    'msg'=>'Could not add Flow'
                )
            );
        }
        
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

        $data =['title'=>'workflow_automation'];
        $flow =$this->workflow_model->getFlow($id);
        $class =$flow->action.'_workflow';
        $data['flowdetails'] =$flow;
        $data ['workflow'] =$this->$class->getWorkFlow($flow->action);
        $data ['service'] =$data['workflow']['services'][$flow->service];
        switch ($data['workflow']['services'][$flow->service]['medium']) {
            case 'whatsapp':
                if($flow->configure){
                    $configure =json_decode($flow->configure,true);
                    $data['configure'] =$configure;
                }
                $this->load->view('admin/workflow/configure/whatsapp',$data);
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
        if (!has_permission($this->moudle_permission_name, '', 'delete')) {
            access_denied($this->moudle_permission_name);
        }
        
        $this->workflow_model->deleteFlow($id);
        echo json_encode([
            'success'=> true,
            'msg' => _l('deleted_successfully')
        ]);
    }
}
