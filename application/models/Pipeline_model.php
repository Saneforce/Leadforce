<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pipeline_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

/**
 * Get all active pipeline details
**/
	public function getPipeline()
    {
		$fields = get_option('deal_fields');
		$need_fields = array();
		if(!empty($fields) && $fields != 'null'){
			$need_fields = json_decode($fields);
		}
		if(!empty($need_fields) && in_array("pipeline_id", $need_fields)){
			$this->db->where('publishstatus', '1');
			return $this->db->get(db_prefix() . 'pipeline')->result_array();
		}
		else{
			$default_pipeline = get_option('default_pipeline');
			$this->db->where('id', $default_pipeline);
			$this->db->where('publishstatus', '1');
			return $this->db->get(db_prefix() . 'pipeline')->result_array();
			
		}
    }
	public function getPipeline_all()
    {

			$this->db->where('publishstatus', '1');
			return $this->db->get(db_prefix() . 'pipeline')->result_array();
    }
	public function getpipelinebyIdarray($id)
    {
		$this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'pipeline')->result_array();
    }
	public function getpipelinebyIdInarray($ids)
    {
		$this->db->where_in('id', $ids);
        return $this->db->get(db_prefix() . 'pipeline')->result_array();
    }
/**
 * Check pipeline details exist by name
**/
	public function checkpipelineExist($name)
    {
		$this->db->where('LOWER(name)', strtolower($name));
        return $this->db->get(db_prefix() . 'pipeline')->row();
    }
/**
 * View existing pipeline details
**/
	public function getpipelinebyId($id)
    {
		$this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'pipeline')->row();
    }
/** 
 * Add new pipeline details
**/
    public function add_pipeline($data)
    {
        $data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by']  = get_staff_user_id();
        $this->db->insert(db_prefix() . 'pipeline', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New pipeline added [PipelineID: '.$insert_id.']');
        }
        return $insert_id;
    }
/**
 * Update existing pipeline details
**/
    public function update_pipeline($data, $id)
    {
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by']  = get_staff_user_id();
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'pipeline', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Pipeline Updated [PipelineID: ' . $id . ']');
            return true;
        }
        return false;
    }
/**
 * Get lead status name
**/
    public function getleadstatusName($id)
    {
	
        $this->db->where('id', $id);
        $this->db->order_by("statusorder", "asc");
        return $this->db->get(db_prefix() . 'projects_status')->row();
    }
/**
 * Get Pipeline Client Details
**/
    public function getPipelineClientDetails($id)
    {
        $Client =	$this->db->where(db_prefix() . 'pipeline.id', $id)
                ->join(db_prefix() . 'pipeline', db_prefix() . 'pipeline.clientid = '.db_prefix() .'clients.userid', 'left')
                ->join(db_prefix() . 'contacts', db_prefix() . 'contacts.userid = '.db_prefix() .'clients.userid and '.db_prefix() . 'contacts.is_primary = 1', 'left')
                ->select(array(db_prefix() . 'clients.userid as client_id',db_prefix() . 'clients.*',db_prefix() . 'contacts.*'))
                ->get(db_prefix() . 'clients')
                ->row_array();
        $Client['contacts'] = $this->db->where(db_prefix() . 'contacts.userid', $Client['userid'])->get(db_prefix() . 'contacts')->result_array();
        return $Client;
    }
	
/**
 * Delete existing pipeline details
**/
    public function delete_pipeline()
    {
        if($_POST['selected_option'] == 'delete') {
            $data['deleted_status'] = 1;
            $this->db->where('pipeline_id', $_POST['id']);
            $this->db->update(db_prefix() . 'projects', $data);
            if ($this->db->affected_rows() > 0) {
                $this->db->where('id', $_POST['id']);
                $this->db->delete(db_prefix() . 'pipeline');
                if ($this->db->affected_rows() > 0) {
                    log_activity('Pipeline Deleted [Pipeline-ID: ' . $_POST['id'] . ']');
                    return true;
                }
            }
        } else {
            if($_POST['delete'] == 'delete') {
                $this->db->where('id', $_POST['id']);
                $this->db->delete(db_prefix() . 'pipeline');
                if ($this->db->affected_rows() > 0) {
                    log_activity('Pipeline Deleted [Pipeline-ID: ' . $_POST['id'] . ']');
                    return true;
                }
            } else {
                $data['pipeline_id'] = $_POST['pipeline_id'];
                $data['status'] = $_POST['status'];
                $this->db->where('pipeline_id', $_POST['id']);
                $this->db->update(db_prefix() . 'projects', $data);
                if ($this->db->affected_rows() > 0) {
                    $this->db->where('id', $_POST['id']);
                    $this->db->delete(db_prefix() . 'pipeline');
                    if ($this->db->affected_rows() > 0) {
                        log_activity('Pipeline Deleted [Pipeline-ID: ' . $_POST['id'] . ']');
                        return true;
                    }
                }
            }
        }
    }
/**
 * Get leads
**/
	public function getLeads()
    {
        return $this->db->get(db_prefix() . 'leads')->result_array();
    }
	
/**
 * Get Pipeline lead status
**/
    public function getPipelineleadstatus($pipeline)
    {
		$this->db->where('id', $pipeline);
        $pipelindetails = $this->db->get(db_prefix() . 'pipeline')->row();
		
        $this->db->where_in('id', explode(',',$pipelindetails->status));
        $this->db->order_by("statusorder", "asc");
		$statuses = $this->db->get(db_prefix() . 'projects_status')->result_array();
        return $statuses;
    }	
/**
 * Get Pipeline lead status
**/
    public function getPipelineprojectsstatus($pipeline = '')
    {
        if(!empty($pipeline)){
            $this->db->where('id', $pipeline);
            $pipelindetails = $this->db->get(db_prefix() . 'pipeline')->row();
            
            $this->db->where_in('id', explode(',',$pipelindetails->status));
        }
        $this->db->order_by("statusorder", "asc");
		$statuses = $this->db->get(db_prefix() . 'projects_status')->result_array();

        return $statuses;
    }
/**
 * Team leaders
**/
	public function getTeamleaders()
    {
        $this->db->where('active', '1');
        $this->db->where('action_for', 'Active');
		//$this->db->where('role', '2');
		$this->db->select('staffid as id,CONCAT(firstname," ",lastname) as name');
        return $this->db->get(db_prefix() . 'staff')->result_array();
    }
/**
 * Specific Team leaders Details
**/
	public function getTeamleaderdetails($id)
    {
		$this->db->where('active', '1');
        // $this->db->where('role', '2');
        //$this->db->where_in('role', array('2','1'));
		$this->db->where('staffid', $id);
		$this->db->select('staffid as id,CONCAT(firstname," ",lastname) as name');
        return $this->db->get(db_prefix() . 'staff')->row_array();
    }
/**
 * Team Members
**/
	public function getTeammembers()
    {
        $this->db->where('active', '1');
        $this->db->where('action_for', 'Active');
		//$this->db->where_in('role', array('2','1','3'));
		$this->db->select('staffid as id,CONCAT(firstname," ",lastname) as name');
		return $this->db->get(db_prefix() . 'staff')->result_array();
    }
/**
 * Specific Team Members Details
**/
	public function getTeammemberdetails($id)
    {
		$this->db->where('active', '1');
		//$this->db->where('role', '3');
		$this->db->where('staffid', $id);
		$this->db->select('staffid as id,CONCAT(firstname," ",lastname) as name');
		return $this->db->get(db_prefix() . 'staff')->row_array();
    }
/**
 * Get Pipeline Team Members
**/
	public function getPipelineTeammembers($pipeline)
    {
        $this->db->where('active', '1');
        $this->db->where('action_for', 'Active');
		$this->db->select('*,staffid as id,CONCAT(firstname," ",lastname) as name');
		return $this->db->get(db_prefix() . 'staff')->result_array();
    }
    public function getPipelineFilterTeammembers($pipeline)
    {
        $this->db->where('active', '1');
        $this->db->where('action_for', 'Active');
		$this->db->select('*,staffid as staff_id,CONCAT(firstname," ",lastname) as name');
		return $this->db->get(db_prefix() . 'staff')->result_array();
    }
/**
 * Get Pipeline Team Members
**/
	public function getPipelineTeamleaderTeammembers($teammembers)
    {
		
        $this->db->where('active', '1');
        $this->db->where('action_for', 'Active');
		$this->db->select('*,staffid as id,CONCAT(firstname," ",lastname) as name');
		return $this->db->get(db_prefix() . 'staff')->result_array();
    }
/**
 * Get Pipeline Team Leaders
**/
	public function getPipelineTeamleaders($pipeline)
    {
		
        $this->db->where('active', '1');
        $this->db->where('action_for', 'Active');
		$this->db->select('*,staffid as id,CONCAT(firstname," ",lastname) as name');
		return $this->db->get(db_prefix() . 'staff')->result_array();
    }
/**
 * Get Logged User Details
**/
	public function getUserdetails($staffid)
    {
		$this->db->where('active', '1');
		$this->db->where('staffid', $staffid);
        return $this->db->get(db_prefix() . 'staff')->row();
    }
/**
 * Get Specific Team Leader Pipelines
**/
	public function getTeamleadpipeline($teamleader)
    {
		$this->db->where('publishstatus', '1');
		$this->db->like('teamleader', $teamleader);
		
        return $this->db->get(db_prefix() . 'pipeline')->result_array();
    }
/**
 * Get Team Member Pipelines
**/
	public function getTeammemberpipeline($teammember)
    {
		$this->db->like('teammembers', $teammember);
        $this->db->where('action_for', 'Active');
		$this->db->where('publishstatus', '1');
        return $this->db->get(db_prefix() . 'pipeline')->result_array();
    }
    /**
 * Get Pipeline Team Members
**/
	public function getAllPipelineTeammembers()
    {
        return $this->db->query('SELECT *, staff_id as id, CONCAT(firstname," ",lastname) as name, firstname, lastname FROM ' . db_prefix() . 'project_members JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid=' . db_prefix() . 'project_members.staff_id GROUP by staff_id order by firstname ASC')->result_array();
    }
/**
 * Get Team Members Except Owner
**/
    public function getTeammembersexceptowner($teammember,$pipeline)
    {
        $this->db->where('active', '1');
        $this->db->where('action_for', 'Active');
        $this->db->where('staffid !=', $teammember);
		$this->db->select('*,staffid as id,CONCAT(firstname," ",lastname) as name');
        return $this->db->get(db_prefix() . 'staff')->result_array();
        
    }
	public function getpipelinedealstatus($id)
	{
		$this->db->where('pipeline_id', $id);
		$deals = $this->db->get(db_prefix() . 'projects')->result_array();
		$data = array();
		$cnt = count($deals);
		$this->db->where('id', $id);
		$pipeline_name = $this->db->get(db_prefix() . 'pipeline')->row();
		$data['name'] = $pipeline_name->name;
		if($cnt > 0) {
			$data['count'] = $cnt;
			$this->db->where('id !=', $id);
			$html = '';
			$pipelines = $this->db->get(db_prefix() . 'pipeline')->result_array();
			foreach($pipelines as $val) {
				$html .= '<option value="'.$val['id'].'">'.$val['name'].'</option>';
			}
			$data['pipelines'] = $html;

			$this->db->where('id !=', $pipelines[0]['id']);
			$projects_status = $this->db->get(db_prefix() . 'projects_status')->result_array();
			$html1 = '';
			foreach($projects_status as $val) {
				$html1 .= '<option value="'.$val['id'].'">'.$val['name'].'</option>';
			}
			$data['projects_status'] = $html1;
		} else {
			$data['count'] = 0;
		}
		return $data;
	}
	public function get_pipeline_stage($stage)
    {
        $this->db->where_in('id', $stage);
		$this->db->select('*');
		return $this->db->get(db_prefix() . 'projects_status')->result_array();
    }
	public function getpipelinebyId_array($id)
    {
		$this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'pipeline')->result_array();
    }
}