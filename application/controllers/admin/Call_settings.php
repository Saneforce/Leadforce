<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Call_settings extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('payment_modes_model');
        $this->load->model('settings_model');
        $this->load->model('callsettings_model');
    }

    public function index()
    {
        $this->enable_call();
    }

    /* List all custom fields */
    public function enable_call()
    {
        if (!has_permission('settings', '', 'view')) {
            access_denied('settings');
        }

        $tab = 'enable_call';
        if ($this->input->post()) {
            //pre($this->input->post());
            if (!has_permission('settings', '', 'edit')) {
                access_denied('settings');
            }
			if(empty($_POST['source_from']) || $_POST['source_from'] != 'daffytel'){
				unset($_POST['country_daffy']);
			}
            if(!empty(trim($_POST['id']))) {
                $updateData = array();
				if($_POST['call_enable'] == 1){
					$updateData['source_from'] = $_POST['source_from'];
					$updateData['enable_call'] = $_POST['call_enable'];
					if(!empty($_POST['source_from']) && $_POST['source_from'] == 'daffytel'){
						 $updateData['country_code'] = $_POST['country_daffy'];
						 $updateData['webhook']		 = $_POST['webhook'];
					}
					$updateData['app_id'] = $_POST['app_key'];
					$updateData['app_secret'] = $_POST['app_secret'];
					$updateData['recorder'] = $_POST['recorder'];
				}
				else{
					$updateData['source_from'] = '';
					$updateData['enable_call'] = '';
					
					$updateData['country_code'] = '';
					$updateData['webhook']		 = '';
					$updateData['app_id'] = '';
					$updateData['app_secret'] = '';
					$updateData['recorder'] = '';
				}
                $update = $this->callsettings_model->updateCallSettings($updateData, $_POST['id']);
                if($update) {
                    set_alert('success', _l('call_settings_updated'));
                } else {
                    set_alert('warning', _l('call_settings_failed'));
                }
            } else {
                $result = $this->callsettings_model->getcallSettings();
                if(empty($result)) {
                    $updateData = array();
					$updateData['source_from'] = $_POST['source_from'];
                    $updateData['enable_call'] = $_POST['call_enable'];
					if(!empty($_POST['source_from']) && $_POST['source_from'] == 'daffytel'){
						 $updateData['country_code'] = $_POST['country_daffy'];
						 $updateData['webhook']		 = $_POST['webhook'];
					}
                    $updateData['app_id'] = $_POST['app_key'];
                    $updateData['app_secret'] = $_POST['app_secret'];
                    $updateData['recorder'] = $_POST['recorder'];
                    $insert = $this->callsettings_model->insertCallSettings($updateData);
                    if($insert > 0) {
                        set_alert('success', _l('call_settings_updated'));
                    } else {
                        set_alert('warning', _l('call_settings_failed'));
                    }
                }
            }
        }
        $this->load->model('taxes_model');
        $this->load->model('tickets_model');
        $this->load->model('leads_model');
        $this->load->model('currencies_model');
        $data['taxes']                                   = $this->taxes_model->get();
        $data['ticket_priorities']                       = $this->tickets_model->get_priority();
        $data['ticket_priorities']['callback_translate'] = 'ticket_priority_translate';
        $data['roles']                                   = $this->roles_model->get();
        $data['leads_sources']                           = $this->leads_model->get_source();
        $data['leads_statuses']                          = $this->leads_model->get_status();
        $data['title']                                   = _l('options');

        $data['admin_tabs'] = ['update', 'info'];

        if (!$tab || (in_array($tab, $data['admin_tabs']) && !is_admin())) {
            $tab = 'general';
        }
        $data['tabs'] = $this->app_tabs->get_settings_tabs();


        
        if($tab != 'enable_call' && $tab != 'agent'){
			if(!empty($data['tabs'])){
				foreach($data['tabs'] as $tab_1 => $val12){
					if($tab_1 == 'enable_call' || $tab_1 == 'agent'){
						unset($data['tabs'][$tab_1]);
					}
					
				}
			}
		}
		else{
			if(!empty($data['tabs'])){
				foreach($data['tabs'] as $tab_1 => $val12){
					
					if($tab_1 != 'enable_call' && $tab_1 != 'agent'){
						unset($data['tabs'][$tab_1]);
					}
					
				}
			}
        }
        
        if (!in_array($tab, $data['admin_tabs'])) {
			
            $data['tab'] = $this->app_tabs->filter_tab($data['tabs'], $tab);
        } else {
            // Core tabs are not registered
            $data['tab']['slug'] = $tab;
            $data['tab']['view'] = 'admin/settings/includes/' . $tab;
        }
        $data['callsettings'] = $this->callsettings_model->getcallSettings();
        $data['countries']    = $this->callsettings_model->getCountries();
        //echo '<pre>';print_r($data['callsettings']);exit;
        $this->load->view('admin/settings/all', $data);
    }

    public function agent()
    {
        if (!has_permission('settings', '', 'view')) {
            access_denied('settings');
        }

        $tab = 'agent';
        
        $this->load->model('taxes_model');
        $this->load->model('tickets_model');
        $this->load->model('leads_model');
        $this->load->model('currencies_model');
        $this->load->model('pipeline_model');

        
        $data['taxes']                                   = $this->taxes_model->get();
        $data['ticket_priorities']                       = $this->tickets_model->get_priority();
        $data['ticket_priorities']['callback_translate'] = 'ticket_priority_translate';
        $data['roles']                                   = $this->roles_model->get();
        $data['leads_sources']                           = $this->leads_model->get_source();
        $data['leads_statuses']                          = $this->leads_model->get_status();
        $data['title']                                   = _l('options');

        $data['admin_tabs'] = ['update', 'info'];

        if (!$tab || (in_array($tab, $data['admin_tabs']) && !is_admin())) {
            $tab = 'general';
        }
        $data['tabs'] = $this->app_tabs->get_settings_tabs();


        
        if($tab != 'enable_call' && $tab != 'agent'){
			if(!empty($data['tabs'])){
				foreach($data['tabs'] as $tab_1 => $val12){
					if($tab_1 == 'enable_call' || $tab_1 == 'agent'){
						unset($data['tabs'][$tab_1]);
					}
					
				}
			}
		}
		else{
			if(!empty($data['tabs'])){
				foreach($data['tabs'] as $tab_1 => $val12){
					
					if($tab_1 != 'enable_call' && $tab_1 != 'agent'){
						unset($data['tabs'][$tab_1]);
					}
					
				}
			}
        }
        if (!in_array($tab, $data['admin_tabs'])) {
			$data['tabs']['agent']['view'] = 'admin/agent/list';
			//echo '<pre>';print_r($data['tabs']);exit;
            $data['tab'] = $this->app_tabs->filter_tab($data['tabs'], $tab);
        } else {
            // Core tabs are not registered
            $data['tab']['slug'] = $tab;
            $data['tab']['view'] = 'admin/' . $tab . '/list';
        }
        
        $data['agents']    = $this->callsettings_model->getAllAgents();
        $data['editAgents']    = $this->pipeline_model->getPipelineTeamleaders(0);
        //pre($data['agents']);
        $data['agent_result'] = $this->callsettings_model->getAgents();
        $data['deactive_agent_result'] = $this->callsettings_model->getDeactiveAgents();
        $data['callsettings'] = $this->callsettings_model->getcallSettings();
        //$data['agent_result'] = $this->callsettings_model->getAllAgent();
        $this->load->view('admin/settings/all', $data);
    }

    public function saveAgent() {
		if(!empty($_POST['token'])){
			$updateData = array();
			$updateData['access_token'] = $_POST['token'];
			$update = $this->callsettings_model->updateCallSettings($updateData,'');
			$updateData = array();
			unset($_POST['token']);
		}
        $data = $res = array();
		$callsettings = $this->callsettings_model->getcallSettings();
        $data['staff_id'] = $_POST['extid'];
        $data['source_from'] = $callsettings->source_from;
        $data['phone'] = $_POST['phone_number'];
        $data['agent_id'] = $_POST['agentid'];
        $data['status'] = $_POST['status'];
		if($callsettings->source_from=='telecmi'){
			$data['start_time'] = $_POST['start_time'];
			$data['end_time'] = $_POST['end_time'];
			$data['password'] = $_POST['password'];
			$data['sms_alert'] = $_POST['sms_alert'];
		}
		$result = $this->callsettings_model->addAgent($data);
        if($result) {
            $res['msg'] = 'Agent saved successfully.';
            $res['status'] = 'success';
        } else {
            $res['msg'] = 'Problem to save Agent.';
            $res['status'] = 'fail';
        }
        echo json_encode($res);
    }
	public function updatetoken() {
		$updateData = array();
		$updateData['access_token'] = $res['access_token'] = $_POST['token'];
		$update = $this->callsettings_model->updateCallSettings($updateData,'');
		$res['status'] = 'success';
		echo json_encode($res);
	}
    public function updateAgent() {

        //pre($_POST);
		if(!empty($_POST['token'])){
			$updateData = array();
			$updateData['access_token'] = $_POST['token'];
			$update = $this->callsettings_model->updateCallSettings($updateData,'');
			$updateData = array();
			unset($_POST['token']);
		}
        $data = $res = array();
		$callsettings = $this->callsettings_model->getcallSettings();
        $data['staff_id'] = $_POST['staff_id'];
        $data['phone'] = $_POST['phone_number'];
        
        $data['status'] = $_POST['status'];
		if($callsettings->source_from=='telecmi'){
			$data['agent_id'] = $_POST['agentid'];
			$data['start_time'] = $_POST['start_time'];
			$data['end_time'] = $_POST['end_time'];
			$data['password'] = $_POST['password'];
			$data['sms_alert'] = $_POST['sms_alert'];
		}
        $id = $_POST['id'];
        $result = $this->callsettings_model->updateAgent($data, $id);
        //if($result) {
            $res['msg'] = 'Agent saved successfully.';
            $res['status'] = 'success';
        // } else {
        //     $res['msg'] = 'Problem to save Agent.';
        //     $res['status'] = 'fail';
        // }
        echo json_encode($res);
    }

    public function getEmpDetail() {
        $emp_id = $_POST['emp_id'];
        $result = $this->callsettings_model->getEmpDetail($emp_id);
        $data['phone'] = $result->phonenumber;
        $data['name'] = $result->firstname;
        echo json_encode($data);
    }

    public function getAgentDetails() {
        $id = $_POST['id'];
        $result = $this->callsettings_model->getAgentDetail($id);
        echo json_encode($result);
    }

    public function activateAgent() {
        $id = $_POST['id'];
		if(!empty($_POST['token'])){
			$updateData = array();
			$updateData['access_token'] = $_POST['token'];
			$update = $this->callsettings_model->updateCallSettings($updateData,'');
			$updateData = array();
			unset($_POST['token']);
		}
        $result = $this->callsettings_model->activateAgent($id);
        if($result) {
            $res['msg'] = 'Agent Activated successfully.';
            $res['status'] = 'success';
        } else {
            $res['msg'] = 'Problem to Activate Agent.';
            $res['status'] = 'fail';
        }
        echo json_encode($res);
    }
	 public function delete_agent_db()
    {
		 $id = $_POST['id'];
		 $response = $this->callsettings_model->delete_agent_db($id);
        if($response) {
            $res['msg'] =  _l('deleted', _l('agent'));
            $res['status'] = 'success';
        } else {
            $res['msg'] =  _l('problem_deleting', _l('agent'));
            $res['status'] = 'fail';
        }
        echo json_encode($res);
	}
    public function delete_agent()
    {
        $id = $_POST['id'];
		if(!empty($_POST['token'])){
			
			$updateData = array();
			$updateData['access_token'] = $_POST['token'];
			$update = $this->callsettings_model->updateCallSettings($updateData,'');
			unset($_POST['token']);
			$updateData = array();
		}
        $response = $this->callsettings_model->delete_agent($id);
        if($response == true) {
            $res['msg'] =  _l('deleted', _l('agent'));
            $res['status'] = 'success';
        } else {
            $res['msg'] =  _l('problem_deleting', _l('agent'));
            $res['status'] = 'fail';
        }
        echo json_encode($res);
    }

    public function getAppAgentDetails() {
        $staffid = get_staff_user_id();
        $appDetails = $this->callsettings_model->getcallSettings();
        //pre($appDetails);
        $staff = $this->callsettings_model->getAgentDetailbyStaffId($staffid);
        $result = array();
        $result['agent_id'] = $staff->agent_id;
        $result['app_secret'] = $appDetails->app_secret;
        $result['code'] = $appDetails->country_code;
        $result['app_id'] = $appDetails->app_id;
        $result['contact_no'] = $_POST['contact_no'];
        $result['webhook'] = $appDetails->webhook;
        $result['agent_no'] = $staff->phone;
        $result['status'] = 'success';
        
        //pre($result);
        echo json_encode($result);
    }

    public function createActivity() {
        $data = $_POST;
		if(!empty($data['token'])){
			unset($data['token']);
			$updateData = array();
			$updateData['access_token'] = $_POST['token'];
			$update = $this->callsettings_model->updateCallSettings($updateData,'');
			$updateData = array();
		}
        $resArray = array();
        if($_POST['type'] == 'task') {
            $task = $this->callsettings_model->getTaskDetails($_POST['deal_id']);
            $data['deal_id'] = $task->rel_id;
            $data['status'] = $task->status;
            $data['rel_id'] = $task->rel_id;
            $data['rel_type'] = $task->rel_type;
            $data['contact_id'] = $task->contacts_id;
            //pre($data);
        }elseif($_POST['deal_id']>0 || $_POST['type'] =='deal'){
            $data['rel_id'] = $_POST['deal_id'];
            $data['rel_type'] = 'project';
            $data['contact_id'] = $_POST['contact_id'];
        }else{
            $data['rel_id'] = $_POST['contact_id'];
            $data['rel_type'] = 'contact';
            $data['contact_id'] = $_POST['contact_id'];
        }
        if($_POST['type'] == 'contact') {
            $task = $this->callsettings_model->getTaskDetails($_POST['deal_id']);
            $data['deal_id'] = $task->rel_id;
            //pre($task);
        }
        $result = $this->callsettings_model->addtask($data);
		// echo $this->db->last_query();exit;
        if($result) {
            $resArray['status'] = 'success';
            $resArray['message'] = $data['msg'];
        } else {
            $resArray['status'] = 'error';
            $resArray['message'] = $data['msg'];
        }
        //pre($resArray);
        echo json_encode($resArray);
    }

    public function getPersonDeals() {
        $id = $_POST['contact'];
        $contactno = $_POST['phone'];
        $deals = $this->callsettings_model->getPersonDeals($id);
        //pre($_POST);
        $html = '';
        $cnt = 0;
        $pid = '';
        if($this->input->post('listOwn')){
            $contact =$this->clients_model->get_contact($_POST['contact']);
            if($contact){
                $html .= '<option value="">'.$contact->firstname." ".$contact->lastname.'</option>';
            }
            
        }
        foreach($deals as $deal) {
            $primary = '';
            if($deal['is_primary'] == 1) {
                $primary = '(Primary)';
            }
            $html .= '<option value="'.$deal['project_id'].'">#'.$deal['project_id'].' - '.$deal['project'].' - '.$deal['company'].' '.$primary.'</option>';
            $pid = $deal['project_id'];
            $cnt++;
        }
        
        //pre($deals);
        if($deals || $this->input->post('listOwn')) {
            if($cnt == 1) {
                $result['pid'] = $pid;
            }elseif($cnt == 0) {
                $result['pid'] = '';
            }
            $result['status'] = 'success';
            $result['result'] = $html;
            $result['contactId'] = $id;
            $result['contactNumber'] = $contactno;
            $result['cnt'] = $cnt;
            
        } else {
            $result['status'] = 'error';
            $result['result'] = 'No Deals linked with this contact';
        }
        echo json_encode($result);
    }

    public function getCallHistory() {
        $id = $_POST['id'];
        
        $callhis = $this->callsettings_model->getCallHistory($id);
        //pre($callhis);
        $html = '<table class="table table-bordered">
        <thead>
          <tr>
            <th>Call Status</th>
            <th>Agent Id</th>
            <th>From</th>
            <th>To</th>
            <th>Duration</th>
          </tr>
        </thead>
        <tbody>';
         
        foreach($callhis as $val) {
            if(!empty($val['status'])) {
                $status = (($val['status'] == 'answered')?"Answered":"Missed");
            } else {
                $status = 'Could not connect';
            }
            $html .= '<tr>';
            $html .= '<td>'.$status.'</td>';
            $html .= '<td>'.$val['agent'].'</td>';
            $html .= '<td>'.$val['call_from'].'</td>';
            $html .= '<td>'.$val['call_to'].'</td>';
            $html .= '<td>'.$val['duration'].' Sec</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        $result = array();
        $result['status'] = 'success';
        $result['result'] = $html;
        echo json_encode($result);
        //pre($callhis);
    }
}
