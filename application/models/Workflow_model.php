<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Workflow_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getFlow($id)
    {
        $this->db->where('id',$id);
        $data = $this->db->get(db_prefix().'workflow')->row();
        if($data){
            $data->configure =json_decode($data->configure,true);
        }
        return $data;
    }

    public function addApprovalFlow()
    {
        $this->form_validation->set_rules('action', 'Action', 'required');
        $this->form_validation->set_rules('service', 'Service', 'required');
        if ($this->form_validation->run() == FALSE){
            return [
                'success'=>false,
                'msg'=>'Could not add Flow'
            ];
        }else{
            if($this->input->post('parent_id') >0){
                $this->db->where('id',$this->input->post('parent_id'));
                $flow =$this->db->get(db_prefix().'workflow')->row();
                if($flow && $flow->service =='approval_level' && $this->input->post('service') =='approval_level'){
                    return [
                        'success'=>false,
                        'msg'=>'Parent flow doesnot support approval flow'
                    ];
                }
            }
            if($this->input->post('action') =='deal_approval'){
                if($this->input->post('service') =='approval_request_email_notification' || $this->input->post('service') =='approved_email_notification' || $this->input->post('service') =='rejected_email_notification'){
                    $this->db->where('action',$this->input->post('action'));
                    $this->db->where('service',$this->input->post('service'));
                    $flow_exists =$this->db->get(db_prefix().'workflow')->row();
                    if($flow_exists){
                        return [
                            'success'=>false,
                            'msg'=>'Selected service already exists'
                        ];
                    }
                }
            }
            $data =array(
                'action'=>$this->input->post('action'),
                'service'=>$this->input->post('service'),
                'parent_id'=>$this->input->post('parent_id')
            );
            $this->db->insert(db_prefix().'workflow',$data);
            return array(
                'success'=>true,
                'msg'=>'Flow added successfully'
            );
        }
        
    }

    public function addFlow()
    {
        if($this->input->post('action') =='deal_approval'){
            return $this->addApprovalFlow();
        }
        if($this->input->post('module')=='lead' && ($this->input->post('action')=='lead_created' || $this->input->post('action')=='lead_updated' || $this->input->post('action')=='lead_deleted' || $this->input->post('action')=='lead_cronjob')){
            $this->db->where('module',$this->input->post('module'));
            $this->db->where('action',$this->input->post('action'));
            $this->db->where('parent_id',$this->input->post('parent_id'));
            $exists =$this->db->get(db_prefix().'workflow')->row();
            if($exists){
                return array(
                    'success'=>false,
                    'msg'=>'Trigger already exists'
                );
            }
        }elseif($this->input->post('module')=='deal' && ($this->input->post('action')=='deal_approval')){
            $this->db->where('module',$this->input->post('module'));
            $this->db->where('action',$this->input->post('action'));
            $this->db->where('parent_id',$this->input->post('parent_id'));
            $exists =$this->db->get(db_prefix().'workflow')->row();
            if($exists){
                return array(
                    'success'=>false,
                    'msg'=>'Trigger already exists'
                );
            }
        }
        $data =array(
            'module'=>$this->input->post('module'),
            'action'=>$this->input->post('action'),
            'service'=>$this->input->post('service'),
            'parent_id'=>$this->input->post('parent_id')
        );
        $this->db->insert(db_prefix().'workflow',$data);
        return array(
            'success'=>true,
            'inserted_id'=>$this->db->insert_id(),
            'msg'=>'Flow added successfully'
        );
    }

    public function updateFlowConfigure($id,$configure)
    {
        $this->db->where('id',$id);
        $data =array(
            'configure'=>$configure,
        );
        $this->db->update(db_prefix().'workflow',$data);
        return $id;
    }
    

    public function updateFlowStatus($id,$status)
    {
        $this->db->where('id',$id);
        $data =array(
            'inactive'=>!$status,
        );
        $this->db->update(db_prefix().'workflow',$data);
        return $id;
    }

    public function deleteFlow($id)
    {
        // delete all childern flows
        $this->db->where('parent_id',$id);
        $this->db->delete(db_prefix().'workflow');

        $this->db->where('id',$id);
        $this->db->delete(db_prefix().'workflow');
        return $id;
    }

    public function getflows($action,$parent_id=0,$where=[])
    {
        $this->db->where('parent_id',$parent_id);
        if($where){
            $this->db->where($where);
        }
        $this->db->where('action',$action);
        $all_flows =$this->db->get(db_prefix().'workflow')->result_object();
        if($all_flows){
            foreach($all_flows as $key => $flow){
                $all_flows[$key]->configure = json_decode($flow->configure,true);
            }
        }
        return $all_flows;
    }

    public function getmoduleflows($module,$where=array())
    {
        $this->db->select('id,action,parent_id,configure');
        $this->db->where('module',$module);
        if($where){
            $this->db->where($where);
        }
        $all_flows =$this->db->get(db_prefix().'workflow')->result_object();
        if($all_flows){
            foreach($all_flows as $key => $flow){
                $all_flows[$key]->configure = json_decode($flow->configure,true);
            }
        }
        
        return $all_flows;
    }
}