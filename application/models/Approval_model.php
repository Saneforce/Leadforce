<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Approval_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staff_model');
        $this->load->model('workflow_model');
    }

    public function getHistory($rel_type,$rel_id)
    {
        $this->db->where('rel_type',$rel_type);
        $this->db->where('rel_id',$rel_id);
        return $this->db->get(db_prefix().'approval_history')->result_object();
    }

    public function getReportingLevels($staffid)
    {
        $levels =array();
        $this->db->where('staffid',$staffid);
        $staff =$this->db->get(db_prefix().'staff')->row();
        if($staff->reporting_to){
            $levels[] =$this->staff_model->get($staff->reporting_to);
            $levels =array_merge($levels,$this->getReportingLevels($staff->reporting_to));
        }
        return $levels;
    }

    public function getDealReportingLevels($staffid)
    {
        $staffLevels =$this->getReportingLevels($staffid);
        $flows =$this->workflow_model->getflows('deal_approval');
        $levels =array();
        
        if($flows){
            $flowCounter =0;
            foreach ($flows as $flow) {
                if($flow->service =='approval_level'){
                    $flowCounter++;
                    $configure =false;
                    if($flow->configure){
                        $configure =json_decode($flow->configure ,true);
                        if($configure['approver'] =='REPORTING_LEVEL'){
                            if(isset($staffLevels[$flowCounter-1])){
                                $levels []=$staffLevels[$flowCounter-1];
                            }else{
                                $levels []=false;
                            }
                        }else{
                            $levels[] =$this->staff_model->get($configure['approver']);
                        }
                    }
                }
            }
        }
        return $levels;
    }

    public function addApprovalHistory($rel_type,$rel_id,$approved_by,$reason,$remarks,$status)
    {
        $data =array(
            'rel_type'=>$rel_type,
            'rel_id'=>$rel_id,
            'approved_by'=>$approved_by,
            'reason'=>$reason,
            'remarks'=>$remarks,
            'status'=>$status,
            'ip_address'=>$this->input->ip_address()
        );
        $this->db->insert(db_prefix().'approval_history',$data);
        return true;
    }
}