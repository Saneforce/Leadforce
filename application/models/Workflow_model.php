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
        return $this->db->get(db_prefix().'workflow')->row();
    }

    public function addFlow($action,$service)
    {
        $data =array(
            'action'=>$action,
            'service'=>$service
        );
        return $this->db->insert(db_prefix().'workflow',$data);
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
        $this->db->where('id',$id);
        $this->db->delete(db_prefix().'workflow');
        return $id;
    }

    public function getflows($action)
    {
        $this->db->where('action',$action);
        return $this->db->get(db_prefix().'workflow')->result_object();
    }
}