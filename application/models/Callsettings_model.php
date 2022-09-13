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
        $settings = $this->db->get(db_prefix() . 'call_settings')->result_object();
        return $settings;
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
        $this->db->where("( deleted IS NULL OR deleted = 0)");
        $this->db->where(db_prefix() . 'call_settings.id = ' . db_prefix() . 'agents.ivr_id');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'agents.staff_id','left');
        $this->db->join(db_prefix() . 'call_settings', db_prefix() . 'call_settings.id = ' . db_prefix() . 'agents.ivr_id','left');
        $this->db->select(db_prefix().'agents.*,'.db_prefix() . 'call_settings.ivr_name ,'.db_prefix().'staff.firstname as staff_name');
        $agents = $this->db->get(db_prefix() . 'agents')->result_array();
        return $agents;
    }

    public function getDeactiveAgents() {

        $this->db->where('deleted', 1);
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'agents.staff_id','left');
        $this->db->join(db_prefix() . 'call_settings', db_prefix() . 'call_settings.id = ' . db_prefix() . 'agents.ivr_id','left');
        $this->db->select(db_prefix().'agents.*,'.db_prefix() . 'call_settings.ivr_name ,'.db_prefix().'staff.firstname as staff_name');
        $agents = $this->db->get(db_prefix() . 'agents')->result_array();
        return $agents;
    }

    public function getAgentDetail($id) {
        $this->db->where(db_prefix().'agents.id', $id);
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'agents.staff_id','left');
        $this->db->join(db_prefix() . 'call_settings', db_prefix() . 'call_settings.id = ' . db_prefix() . 'agents.ivr_id','left');
        $this->db->select(db_prefix().'agents.*,'.db_prefix() . 'call_settings.ivr_name ,'.db_prefix() . 'call_settings.app_id ,'.db_prefix() . 'call_settings.app_secret ,'.db_prefix() . 'call_settings.channel ,'.db_prefix().'staff.firstname as staff_name');
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

        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'agents.staff_id');
        $this->db->join(db_prefix() . 'call_settings', db_prefix() . 'call_settings.id = ' . db_prefix() . 'agents.ivr_id');
        $this->db->select(db_prefix().'agents.*,'.db_prefix() . 'call_settings.ivr_name ,'.db_prefix() . 'call_settings.app_id ,'.db_prefix() . 'call_settings.app_secret ,'.db_prefix() . 'call_settings.channel ,'.db_prefix().'staff.firstname as staff_name');
        $this->db->where('staff_id',$id);
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
        if(isset($post['addedfrom'])){
            $data['addedfrom']             = $post['addedfrom'];
        }else{
            $data['addedfrom']             = get_staff_user_id();
        }
        
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
                    'staffid'       => $data['addedfrom'],
                    'assigned_from' => $data['addedfrom'],
                ]);
            
                $this->db->insert(db_prefix() . 'task_followers', [
                    'taskid'  => $insert_id,
                    'staffid' => $data['addedfrom'],
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
        $this->db->where(db_prefix().'agents.staff_id', get_staff_user_id());
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'agents.staff_id','left');
        $this->db->join(db_prefix() . 'call_settings', db_prefix() . 'call_settings.id = ' . db_prefix() . 'agents.ivr_id','left');
        $this->db->select(db_prefix().'agents.*,'.db_prefix() . 'call_settings.ivr_name ,'.db_prefix() . 'call_settings.app_id ,'.db_prefix() . 'call_settings.app_secret ,'.db_prefix() . 'call_settings.channel ,'.db_prefix().'staff.firstname as staff_name,'.db_prefix().'call_settings.enable_call');
        $agent = $this->db->get(db_prefix() . 'agents')->row();
        if($agent && $agent->enable_call ==1){
            return 1;
        }else{
            return 0;
        }

    }

    public function list_vendors()
    {
        return array(
            'telecmi' => 'Tele CMI',
            'tata' => 'TATA Tele Services',
            'daffytel' => 'Daffytel',
        );
    }

    public function check_ivr_name_same($id,$ivr_name)
    {
        $this->db->where('id',$id);
        $current_row = $this->db->get(db_prefix().'call_settings')->row();
        
        if($current_row && $current_row->ivr_name == $ivr_name){
            return true;
        }
        return false;
    }

    public function get_active_ivr_vendors()
    {
        $this->db->where('enable_call',1);
        $this->db->group_by('source_from'); 
        $vendors =$this->db->get(db_prefix().'call_settings')->result_object();
        $vendors_list=$this->list_vendors();
        $active_vendors=array();
        if($vendors){
            foreach($vendors as $vendor){
                if(isset($vendors_list[$vendor->source_from])){
                    $active_vendors[$vendor->source_from] =$vendors_list[$vendor->source_from];
                }
            }
            
           
        }
        return $active_vendors;
    }

    public function get_active_ivrs()
    {
        $this->db->where('enable_call',1);
        return $this->db->get(db_prefix().'call_settings')->result_object();
    }
    
    public function syncTelecmiAgents($ivr)
    {
        if($ivr->channel =='international_softphone' || $ivr->channel =='national_softphone' || $ivr->channel =='national'){
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://rest.telecmi.com/v2/user/all',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{
                "appid":'.$ivr->app_id.',
                "secret":"'.$ivr->app_secret.'"
            }',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));
            $response = curl_exec($curl);
            
            curl_close($curl);
            $response = json_decode($response); 
        }

        if(isset($response->agents) && count($response->agents)>0){
            $agent_ids =array();
            foreach($response->agents as $key => $agent){
                $agent_ids []=$agent->agent_id;
                $this->db->where(['source_from'=>'telecmi','agent_id'=>$agent->agent_id,'ivr_id'=>$ivr->id]);
                $exist =$this->db->get(db_prefix().'agents')->row();
                $data =array(
                    'source_from'=>'telecmi',
                    'phone'=>$agent->phone,
                    'agent_id'=>$agent->agent_id,
                    'password'=>$agent->password,
                    'sms_alert'=>'',
                    'start_time'=>$agent->start_time,
                    'end_time'=>$agent->end_time,
                    'deleted'=>0,
                    'ivr_id'=>$ivr->id,
                );
                if(!$exist){
                    $data['staff_id'] =0;
                    $data['status'] ='online';
                    $this->db->insert(db_prefix().'agents',$data);
                }else{
                    $this->db->where(['source_from'=>'telecmi','agent_id'=>$agent->agent_id,'ivr_id'=>$ivr->id]);
                    $this->db->update(db_prefix().'agents',$data);
                }
                
            }

            $this->db->where(['source_from'=>'telecmi','ivr_id'=>$ivr->id]);
            $agents =$this->db->get(db_prefix().'agents')->result();
            if($agents){
                foreach($agents as $agent){
                    if(!in_array($agent->agent_id,$agent_ids)){
                        $this->db->where('id',$agent->id);
                        $this->db->delete(db_prefix().'agents');
                    }
                }
            }
        }
    }
    public function syncAgents()
    {
        
        $this->db->where('enable_call',1);
        $ivrs =$this->db->get(db_prefix().'call_settings')->result_object();
        
        if($ivrs){
            foreach($ivrs as $ivr){
                switch ($ivr->source_from) {
                    case 'telecmi':
                        $this->syncTelecmiAgents($ivr);
                        break;
                    
                    default:
                        # code...
                        break;
                }
            }
        }    
    }

    public function check_app_id_same($id,$app_id)
    {
        $this->db->where('id',$id);
        $current_row = $this->db->get(db_prefix().'call_settings')->row();
        
        if($current_row && $current_row->app_id == $app_id){
            return true;
        }
        return false;
    }

    public function check_app_id_secret($id,$app_secret)
    {
        $this->db->where('id',$id);
        $current_row = $this->db->get(db_prefix().'call_settings')->row();
        
        if($current_row && $current_row->app_secret == $app_secret){
            return true;
        }
        return false;
    }

    public function getCallingCode($country_iso2)
    {
        $this->db->where('iso2',$country_iso2);
        $country =$this->db->get(db_prefix().'countries')->row();
        if($country){
            return $country->calling_code;
        }
        return '';
    }
}
