<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Target extends AdminController
{
    public function __construct()
    {
        parent::__construct();
		$this->load->model('payment_modes_model');
		$this->load->model('settings_model');
		$this->load->model('projects_model');
		$this->load->model('pipeline_model');
    }

    public function index()
    {
    	if (!has_permission('target', '', 'view')) {
            access_denied('target');
        }
		$data = array();
		if(isset($_POST['save'])){
			$post_data['settings']['target_company'] = $_POST['target'];
			$success = $this->settings_model->update($post_data);
			 if ($success > 0) {
                set_alert('success', _l('settings_updated'));
            }
			redirect(admin_url('target'));
		}
		$data['title']  =  'Set Target View On Company Level';
		$data['tab_view'] =  $this->load->view('admin/target/company', $data,true);
		
        $this->load->view('admin/target/target', $data);
    }
	public function deal()
    {
    	if (!has_permission('target', '', 'view')) {
            access_denied('target');
        }
		$data = array();
		if(get_option('target_company') == 'Calendar'){
			$data['months'] = array('Jan','Feb','Mar','Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec');
		}
		else{
			$data['months'] = array('Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar');
		}
		if(isset($_POST['save'])){
			add_target('deal');
			$message       = _l('added_successfully', _l('target'));
			set_alert('success', $message);
			$redirect_url = site_url().'admin/target/deal';
			redirect($redirect_url);
			exit;
		}
		$fields = get_option('deal_fields');
		$data['need_fields'] = array();
		if(!empty($fields) && $fields != 'null'){
			$data['need_fields'] = json_decode($fields);
		}
		if(!empty($data['need_fields']) && in_array("pipeline_id", $data['need_fields']) ){
			$deals = $this->pipeline_model->getPipeline();
			$data['pipe_status']	  =  $this->projects_model->get_pipe_status();
		}else{
			$default_pipeline = get_option('default_pipeline');
			$deals = $this->pipeline_model->getpipelinebyIdarray($default_pipeline);
			$cur_id = $deals[0]['default_status'];
			$data['pipe_status']	  =  $this->projects_model->get_pipe_statusbyid($cur_id);
		}
		$data['deals']	  =  $deals;
		
		$data['targets']  =  $this->projects_model->get_all_targets('deal');
		$data['title']    =  _l('target_deal');
		
		$data['tab_view'] =  $this->load->view('admin/target/deal', $data,true);
		
        $this->load->view('admin/target/all', $data);
       // $this->load->view('admin/target/ch_deal', $data);
    }
	public function deal_table()
    {
        if (!has_permission('target', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data('target-deal');
    }
	public function activity_table()
    {
        if (!has_permission('target', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data('target-activity');
    }
	public function edit_deal($target_id)
    {
    	if (!has_permission('target', '', 'view')) {
            access_denied('target');
        }
		$data = array();
		if(get_option('target_company') == 'Calendar'){
			$data['months'] = array('Jan','Feb','Mar','Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec');
		}
		else{
			$data['months'] = array('Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar');
		}
		if(isset($_POST['save'])){
			edit_target('deal',$target_id);
			$message       = _l('updated_successfully', _l('target'));
			set_alert('success', $message);
			$redirect_url = site_url().'admin/target/deal';
			redirect($redirect_url);
			exit;
		}
    }
	public function edit_activity($target_id)
    {
    	if (!has_permission('target', '', 'view')) {
            access_denied('target');
        }
		if(get_option('target_company') == 'Calendar'){
			$data['months'] = array('Jan','Feb','Mar','Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec');
		}
		else{
			$data['months'] = array('Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar');
		}
		if(isset($_POST['save'])){
			edit_target('activity',$target_id);
			$message       = _l('updated_successfully', _l('target'));
			set_alert('success', $message);
			$redirect_url = site_url().'admin/target/activity';
			redirect($redirect_url);
			exit;
		}
    }
	public function activity()
    {
    	if (!has_permission('target', '', 'view')) {
            access_denied('target');
        }
		if(get_option('target_company') == 'Calendar'){
			$data['months'] = array('Jan','Feb','Mar','Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec');
		}
		else{
			$data['months'] = array('Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar');
		}
		if(isset($_POST['save'])){
			add_target('activity');
			
			$message       = _l('added_successfully', _l('activity'));
			set_alert('success', $message);
			$redirect_url = site_url().'admin/target/activity';
			redirect($redirect_url);
			exit;
		}
		
		$data = array();
		$activities = $this->projects_model->all_activiites();
		$data['activities']	  =  $activities;
		$deals = $this->pipeline_model->getPipeline();
		$fields = get_option('deal_fields');
		$data['need_fields'] = array();
		if(!empty($fields) && $fields != 'null'){
			$data['need_fields'] = json_decode($fields);
		}
		if(!empty($data['need_fields']) && in_array("pipeline_id", $data['need_fields']) ){
			$deals = $this->pipeline_model->getPipeline();
		}else{
			$default_pipeline = get_option('default_pipeline');
			$deals = $this->pipeline_model->getpipelinebyIdarray($default_pipeline);
		}
		$data['deals']	  =  $deals;
		$data['targets']  =  $this->projects_model->get_all_targets('activity');
		if(get_option('target_company') == 'Calendar'){
			$data['months'] = array('Jan','Feb','Mar','Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec');
		}
		else{
			$data['months'] = array('Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar');
		}
		$data['title']    =  _l('target_activity');
		$data['tab_view'] =  $this->load->view('admin/target/activity', $data,true);
        $this->load->view('admin/target/activity_all', $data);
    }
	public function deal_delete($del_id){
		$table = db_prefix().'target';
		$condition = array('id'=>$del_id);
		$this->db->where($condition);
		$this->db->delete($table);
		$table = db_prefix().'target_deal';
		$condition = array('target_id'=>$del_id);
		$this->db->where($condition);
		$this->db->delete($table);
		$table = db_prefix().'target_manager';
		$condition = array('target_id'=>$del_id);
		$this->db->where($condition);
		$this->db->delete($table);
		$table = db_prefix().'target_user';
		$condition = array('target_id'=>$del_id);
		$this->db->where($condition);
		$this->db->delete($table);
		$table = db_prefix().'target_interval';
		$condition = array('target_id'=>$del_id);
		$this->db->where($condition);
		$this->db->delete($table);
		$message       = _l('deleted_successfully');
		set_alert('success', $message);
		$redirect_url = site_url().'admin/target/deal';
		redirect($redirect_url);
		exit;
	}
	public function activity_delete($del_id){
		$table = db_prefix().'target';
		$condition = array('id'=>$del_id);
		$this->db->where($condition);
		$this->db->delete($table);
		$table = db_prefix().'target_deal';
		$condition = array('target_id'=>$del_id);
		$this->db->where($condition);
		$this->db->delete($table);
		$table = db_prefix().'target_manager';
		$condition = array('target_id'=>$del_id);
		$this->db->where($condition);
		$this->db->delete($table);
		$table = db_prefix().'target_user';
		$condition = array('target_id'=>$del_id);
		$this->db->where($condition);
		$this->db->delete($table);
		$table = db_prefix().'target_interval';
		$condition = array('target_id'=>$del_id);
		$this->db->where($condition);
		$this->db->delete($table);
		$message       = _l('deleted_successfully');
		set_alert('success', $message);
		$redirect_url = site_url().'admin/target/activity';
		redirect($redirect_url);
		exit;
	}
	public function getusers(){
		extract($_POST);
		if(!empty($assign) && $assign == 'user'){
			$staffs = $this->projects_model->get_all_staffs();
			$req_out = '';
			if(!empty($staffs)){
				foreach($staffs  as $staff_1){
					$req_out .= '<option value="'.$staff_1['staffid'].'">'.$staff_1['firstname'].' '.$staff_1['lastname'].'</option>';
				}
			}
			echo $req_out;
		}
		else if(!empty($assign) && $assign == 'team'){
			$staffs = $this->projects_model->get_all_manager();
			$req_out = '';
			if(!empty($staffs)){
				foreach($staffs  as $staff_1){
					$req_out .= '<option value="'.$staff_1['staffid'].'">'.$staff_1['firstname'].' '.$staff_1['lastname'].'</option>';
				}
			}
			echo $req_out;
		}
		else{
			if(!empty($assign)){
				$staffs = $this->projects_model->get_user_manger($assign);
				$req_out = '';
				if(!empty($staffs)){
					foreach($staffs  as $staff_1){
						$req_out .= '<option value="'.$staff_1['staffid'].'">'.$staff_1['firstname'].' '.$staff_1['lastname'].'</option>';
					}
				}
				echo $req_out;
			}
		}
	}
	public function getusers_edit(){
		extract($_REQUEST);
		if(!empty($assign) && $assign == 'user'){
			$staffs = $this->projects_model->get_all_staffs();
			$target_user = $this->projects_model->get_target_user($target_id);
			$target_user = array_column($target_user, 'user'); 
			$req_out = '';
			if(!empty($staffs)){
				foreach($staffs  as $staff_1){
					if(in_array($staff_1['staffid'],$target_user)){
						$req_out .= '<option value="'.$staff_1['staffid'].'" selected>'.$staff_1['firstname'].' '.$staff_1['lastname'].'</option>';
					}
					else{
						$req_out .= '<option value="'.$staff_1['staffid'].'">'.$staff_1['firstname'].' '.$staff_1['lastname'].'</option>';
					}
				}
			}
			echo $req_out;
		}
		else if(!empty($assign) && $assign == 'team'){
			$staffs = $this->projects_model->get_all_manager();
			$target_user = $this->projects_model->get_target_manager($target_id);
			$req_out = $assign_user = $users ='';
			if(!empty($target_user[0]['assign_user'])){
				$assign_user = $target_user[0]['assign_user'];
			}
			$target_user = array_column($target_user, 'manager'); 
			
			$target_user1 = $this->projects_model->get_target_user($target_id);
			$target_user1 = array_column($target_user1, 'user'); 
			
			if(!empty($staffs)){
				$req_out = '<option value="">Select Manager</option>';
				foreach($staffs  as $staff_1){
					if(in_array($staff_1['staffid'],$target_user)){
						$req_out .= '<option value="'.$staff_1['staffid'].'" selected>'.$staff_1['firstname'].' '.$staff_1['lastname'].'</option>';
					}
					else{
						$req_out .= '<option value="'.$staff_1['staffid'].'">'.$staff_1['firstname'].' '.$staff_1['lastname'].'</option>';
					}
				}
			}
			$staffs = $this->projects_model->get_user_manger($target_user[0]);
			if(!empty($staffs)){
				foreach($staffs  as $staff_1){
					if(in_array($staff_1['staffid'],$target_user1)){
						$users .= '<option value="'.$staff_1['staffid'].'" selected>'.$staff_1['firstname'].' '.$staff_1['lastname'].'</option>';
					}
					else{
						$users .= '<option value="'.$staff_1['staffid'].'">'.$staff_1['firstname'].' '.$staff_1['lastname'].'</option>';
					}
				}
			}
			
			$output = array('req_out'=>$req_out,'assign_user'=>$assign_user,'users'=>$users);
			echo json_encode($output);exit;
			//echo $req_out;
		}
		else{
			if(!empty($assign)){
				$staffs = $this->projects_model->get_user_manger($assign);
				$req_out = '';
				if(!empty($staffs)){
					foreach($staffs  as $staff_1){
						$req_out .= '<option value="'.$staff_1['staffid'].'">'.$staff_1['firstname'].' '.$staff_1['lastname'].'</option>';
					}
				}
				echo $req_out;
			}
		}
	}
	public function pipe_stage(){
		$pipe_stage = $_REQUEST['pipeline_stage'];
		if (strpos($pipe_stage, ',') !== false) {
			$gn_stages = explode(',',$pipe_stage);
		}
		else{
			$gn_stages = array($pipe_stage);
		}
		$pipes = $this->projects_model->get_pipe_value($gn_stages);;
		$req_out = '';
		if(!empty($pipes)){
			foreach($pipes  as $pipe1){
				$check = 1;
				if (strpos($pipe1['status'], ',') !== false) {
					$req_vals = explode(',',$pipe1['status']);
					if(!empty($gn_stages)){
						foreach($req_vals as $req_val1){
							if (!in_array($req_val1, $gn_stages)){
								$check == 2;
								break;
							}
						}
							
					}
					
				}
				else{
					if (!in_array($pipe1['status'], $gn_stages)){
						$check == 2;
						break;
					}
				}
				if($check == 1){
					$req_out .= '<option value="'.$pipe1['id'].'">'.$pipe1['name'].'</option>';
				}
			}
		}
		echo $req_out;
	}
	public function check_edit(){
		$target_id = $_POST['target_id'];
		$fields = get_option('deal_fields');
		$data['need_fields'] = array();
		if(!empty($fields) && $fields != 'null'){
			$data['need_fields'] = json_decode($fields);
		}
		if(!empty($data['need_fields']) && in_array("pipeline_id", $data['need_fields']) ){
			$deals = $this->pipeline_model->getPipeline();
			$sel_stages = $this->projects_model->get_all_pipe_stages($target_id);
			$data['pipe_status']	  =  $this->projects_model->get_pipe_status();
		}
		else{
			$default_pipeline = get_option('default_pipeline');
			$deals = $this->pipeline_model->getpipelinebyId_array($default_pipeline);
			$sel_stages	  =   $this->projects_model->get_edit_pipe_stages($target_id,$deals[0]['default_status']);
			$data['pipe_status']	  =  $this->projects_model->get_pipe_statusbyid($deals[0]['default_status']);			
		}
		$data['deals']	  =  $deals;
		$data['target_id']=  $target_id;
		$data['targets'] = $this->projects_model->get_target_id($target_id);
		
		$sel_deals = $this->projects_model->get_all_deal_target($target_id);
		
		$data['sel_deals'] = array_column($sel_deals, 'pipeline'); 
		$data['sel_stages'] = array_column($sel_stages, 'stage_id'); 
		if(get_option('target_company') == 'Calendar'){
			$data['months'] = array('Jan','Feb','Mar','Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec');
		}
		else{
			$data['months'] = array('Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar');
		}
		
		$data['intervals'] = $this->projects_model->get_all_interval($target_id);
		$this->load->view('admin/target/edit_deal', $data);
	}
	public function check_edit_activity(){
		$target_id = $_POST['target_id'];
		$fields = get_option('deal_fields');
		$data['need_fields'] = array();
		if(!empty($fields) && $fields != 'null'){
			$data['need_fields'] = json_decode($fields);
		}
		if(!empty($data['need_fields']) && in_array("pipeline_id", $data['need_fields']) ){
			$deals = $this->pipeline_model->getPipeline();
		}
		else{
			$default_pipeline = get_option('default_pipeline');
			$deals = $this->pipeline_model->getpipelinebyId_array($default_pipeline);
		}
		$data['deals']	  =  $deals;
		$data['target_id']=  $target_id;
		$data['targets'] = $this->projects_model->get_target_id($target_id);
		$data['intervals'] = $this->projects_model->get_all_interval($target_id);
		$sel_deals = $this->projects_model->get_all_deal_target($target_id);
		$data['sel_deals'] = array_column($sel_deals, 'pipeline'); 
		if(get_option('target_company') == 'Calendar'){
			$data['months'] = array('Jan','Feb','Mar','Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec');
		}
		else{
			$data['months'] = array('Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar');
		}
		$this->load->view('admin/target/edit_activity', $data);
	}
}