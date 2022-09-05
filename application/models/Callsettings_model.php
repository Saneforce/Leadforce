<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Callsettings_model extends App_Model {

    private $contact_columns;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get client object based on passed clientid if not passed clientid return array of all clients
     * @param  mixed $id    client id
     * @param  array  $where
     * @return mixed
     */
    public function getcallSettings() {
        $client = $this->db->get(db_prefix() . 'call_settings')->row();
        return $client;
    }

    public function insertCallSettings($data) {
        $this->db->insert(db_prefix() . 'call_settings', $data);
        return $userid = $this->db->insert_id();
    }

    public function updateCallSettings($data, $id) {
        $affectedRows = 0;
		if(!empty(trim($id))){
			$this->db->where('id', trim($id));
		}
        $this->db->update(db_prefix() . 'call_settings', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        return $affectedRows;
    }

    public function getAgents() {
		 $client = $this->db->get(db_prefix() . 'call_settings')->row();
        $this->db->select('tblagents.*, (select firstname from tblstaff where staffid = tblagents.staff_id) as staff_name');
		$this->db->where("( deleted IS NULL OR deleted = 0) and (source_from = '".$client->source_from."')");
        $agents = $this->db->get(db_prefix() . 'agents')->result_array();
        return $agents;
    }

    public function getDeactiveAgents() {
		$client = $this->db->get(db_prefix() . 'call_settings')->row();
        $this->db->select('tblagents.*, (select firstname from tblstaff where staffid = tblagents.staff_id) as staff_name');
        $this->db->where('deleted', 1);
        $this->db->where('source_from', $client->source_from);
        $agents = $this->db->get(db_prefix() . 'agents')->result_array();
        return $agents;
    }

    public function getAgentDetail($id) {
        $this->db->select('tblagents.*, (select firstname from tblstaff where staffid = tblagents.staff_id) as staff_name');
        $this->db->where('id',$id);
        $agents = $this->db->get(db_prefix() . 'agents')->row();
        return $agents;
    }

    public function activateAgent($id) {
        $affectedRows = 0;
        $data = array();
        $data['deleted'] = 0;
		if(!empty($_POST['agentid'])){
			 $data['agent_id'] = $_POST['agentid'];
		}
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'agents', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        return $affectedRows;
    }

    public function getAllAgents() {
		$client = $this->db->get(db_prefix() . 'call_settings')->row();
		$this->db->where('source_from',$client->source_from);
        $result = $this->db->get(db_prefix() . 'agents')->result_array();
        foreach($result as $item) {
            $array[] = $item['staff_id'];         
        }
        $this->db->where_not_in('staffid', $array);
        $this->db->where('action_for','Active');
        return $agents = $this->db->get(db_prefix() . 'staff')->result_array();
    }

    public function getEmpDetail($id) {
        $this->db->where('staffid',$id);
        $agents = $this->db->get(db_prefix() . 'staff')->row();
        return $agents;
    }

    public function addAgent($data) {
        $this->db->insert(db_prefix() . 'agents', $data);
        return $userid = $this->db->insert_id();
    }

    public function updateAgent($data, $id) {
        $affectedRows = 0;
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'agents', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        return $affectedRows;
    }

    public function delete_agent($id) {
        $affectedRows = 0;
        $data = array();
        $data['deleted'] = 1;
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'agents', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        return $affectedRows;
    }
	public function delete_agent_db($id) {
      
        $this->db->where('id', $id);
		$result = $this->db->delete(db_prefix() . 'agents');
		if($result)
			return true;
		else
			return false;
       
    }

    public function getAgentDetailbyStaffId($id) {
        $this->db->select('tblagents.*, (select firstname from tblstaff where staffid = tblagents.staff_id) as staff_name');
        $this->db->where('staff_id',$id);
        $this->db->where('source_from',CALL_SOURCE_FROM);
        $agents = $this->db->get(db_prefix() . 'agents')->row();
        return $agents;
    }

    public function getTaskDetails($id) {
        $this->db->where('id',$id);
        $task = $this->db->get(db_prefix() . 'tasks')->row();
        return $task;
    }

    public function addtask($post)
    {
        $data = array();
        $data['name'] = 'Call';
        $data['call_request_id'] = $post['req'];
        $data['call_code'] = $post['code'];
        $data['call_msg'] = $post['msg'];
        
        $data['tasktype'] = 1;
        $data['priority'] = 2;

        $data['rel_id'] = $post['rel_id'];
        $data['rel_type'] = $post['rel_type'];
        $data['contacts_id'] = $post['contact_id'];
       
        $startdate = date('Y-m-d H:i:s');
        $data['startdate']             = $startdate;
        $data['dateadded']             = $startdate;
        //$data['datefinished']             = $startdate;
        $data['addedfrom']             = get_staff_user_id();
        $data['is_added_from_contact'] = 0;
        
		$data['status'] = 3;

        $data['is_public'] = 0;

        $data['recurring'] = 0;

        $data['visible_to_client'] = 0;
            
        $data['billable'] = 1;
        
        $data['milestone'] = 0;
        
        
        if(isset($_POST['type']) && $_POST['type'] == 'task' && $post['status'] != 5) {
            $insert_id = $_POST['deal_id'];
        } else {
            $this->db->insert(db_prefix() . 'tasks', $data);
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                $this->db->insert(db_prefix() . 'task_assigned', [
                    'taskid'        => $insert_id,
                    'staffid'       => get_staff_user_id(),
                    'assigned_from' => get_staff_user_id(),
                ]);
            
                $this->db->insert(db_prefix() . 'task_followers', [
                    'taskid'  => $insert_id,
                    'staffid' => get_staff_user_id(),
                ]);

                log_activity('New Task Added [ID:' . $insert_id . ', Name: ' . $data['name'] . ']');
                hooks()->do_action('after_add_task', $insert_id);
            }
        }
        if ($insert_id) {
//Call History Table

            $totData = array();
            $totData['task_id'] = $insert_id;
            $totData['call_to'] = $post['to'];
            $totData['agent'] = $post['agent'];
            $this->db->insert(db_prefix() . 'call_history', $totData);
            $history_id = $this->db->insert_id();

            $this->db->where('agent',$post['agent']);
            $chistory = $this->db->get(db_prefix() . 'call_history_flag')->row();
            if($chistory) {
                $updateHis['history_id'] = $history_id;
                $updateHis['call_to'] = $post['to'];
                $this->db->where('agent', $post['agent']);
                $this->db->update(db_prefix() . 'call_history_flag', $updateHis);
            } else {
                $insertHis['history_id'] = $history_id;
                $insertHis['call_to'] = $post['to'];
                $insertHis['agent'] = $post['agent'];
                $this->db->insert(db_prefix() . 'call_history_flag', $insertHis);
            }
            return $insert_id;
        }

        return false;
    }

    public function getPersonDeals($id) {
        $this->db->select('tblproject_contacts.*,tblprojects.name as project,tblclients.company as company');
        $this->db->where(db_prefix() . 'project_contacts.contacts_id',$id);
        $this->db->join(db_prefix() . 'projects', db_prefix() . 'projects.id = ' . db_prefix() . 'project_contacts.project_id');
		//if(!empty($id)){
			//$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'projects.clientid');
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'projects.clientid ='. db_prefix() . 'clients.userid  ' , 'left');
		//}
        $projects = $this->db->get(db_prefix() . 'project_contacts')->result_array();
        return $projects;
    }

    public function getCallHistory($id) {
        $this->db->select(db_prefix() .'call_history.*,'.db_prefix() . 'agents.phone as agent_no');
        $this->db->join(db_prefix() . 'agents', db_prefix() .'agents.agent_id = '.db_prefix() . 'call_history.agent','left');
        $this->db->where(db_prefix() . 'call_history.task_id',$id);
        $chistory = $this->db->get(db_prefix() . 'call_history')->result_array();
        return $chistory;
    }
	
	 public function getCountries() {
        $this->db->select('*');
        $countries = $this->db->get(db_prefix() . 'countries')->result_array();
        return $countries;
    }

    public function accessToCall() {
        $call = $this->db->get(db_prefix() . 'call_settings')->row();
        if(isset($call->enable_call) && $call->enable_call == 1) {
            $this->db->where('staff_id',get_staff_user_id());
            $this->db->where("( deleted IS NULL OR deleted = 0) and (source_from = '".$call->source_from."')");
            $agent = $this->db->get(db_prefix() . 'agents')->row();
            if($agent) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }

    }
    
}
