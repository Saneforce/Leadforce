<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pipeline extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pipeline_model');
        $this->load->model('leads_model');
    }
/**
 * Pipeline List
**/
    public function index()
    {
		$this->load->model('payment_modes_model');
        $this->load->model('settings_model');
		if (!has_permission('pipeline', '', 'view')) {
            access_denied('pipeline');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('kb_pipelines');
        }
		if(isset($_POST['default_submit'])){
			$post_data['settings']['default_pipeline'] = $_POST['default_pipeline_id'];
			$success = $this->settings_model->update($post_data);
			 if ($success > 0) {
                set_alert('success', _l('default_pipeline_updated'));
            }
			redirect(admin_url('pipeline'));
		}
		$data['default_pipeline'] = get_option('default_pipeline');
        $data['title'] = _l('pipeline');
		$data['all_pipelines'] = $this->pipeline_model->getPipeline_all();
        $this->load->view('admin/pipeline/list', $data);
    }
	
/**
 * Add new or edit existing pipeline
**/
    public function save($id = '')
    {
        if (!has_permission('pipeline', '', 'view')) {
            access_denied('pipeline');
        }
        if ($this->input->post())
		{
            $data = $this->input->post();
            if ($id == '') {
				$checkpipelineexist = $this->pipeline_model->checkpipelineExist($data['name']);
				if(!empty($checkpipelineexist)) {
					set_alert('warning', _l('already_exist', _l('pipeline')));
					redirect(admin_url('pipeline'));
				}
				else {
					if (!has_permission('pipeline', '', 'create')) {
						access_denied('pipeline');
					}
					// Status
					$status = NULL;
					if(!empty($data['status'])) {
						$postval_status = $data['status'];
						foreach($postval_status as $stat) {
							$status .= $stat.',';
						}
						$status = rtrim($status,',');
					}
					$data['status'] = $status;
					// Status
					
					// Team Leaders
					$teamleaders = NULL;
					$staff_leader1 =  $this->db->query('SELECT staffid FROM ' . db_prefix() . 'staff WHERE role = 1')->result_array();
					if(!empty($staff_leader1)) {
						foreach($staff_leader1 as $teamlead) {
							$teamleaders .= $teamlead['staffid'].',';
						}
						
					}
					$staff_leader =  $this->db->query('SELECT staffid FROM ' . db_prefix() . 'staff WHERE role = 2')->result_array();
					if(!empty($staff_leader)) {
						foreach($staff_leader as $teamlead) {
							$teamleaders .= $teamlead['staffid'].',';
						}
						
					}
					if(!empty($teamleaders)){
						$teamleaders = rtrim($teamleaders,',');
					}
					$data['teamleader'] = $teamleaders;
					// Team Leaders
					
					// Team Members
					$teammembers = NULL;
					$staff_member1 =  $this->db->query('SELECT staffid FROM ' . db_prefix() . 'staff WHERE role = 1')->result_array();
					
					if(!empty($staff_member1)) {
						foreach($staff_member1 as $teammemb) {
							$teammembers .= $teammemb['staffid'].',';
						}
						//$teammembers = rtrim($teammembers,',');
					}
					$staff_member =  $this->db->query('SELECT staffid FROM ' . db_prefix() . 'staff WHERE role = 3')->result_array();
					
					if(!empty($staff_member)) {
						foreach($staff_member as $teammemb) {
							$teammembers .= $teammemb['staffid'].',';
						}
						//$teammembers = rtrim($teammembers,',');
					}
					if(!empty($teammembers)){
						$teammembers = rtrim($teammembers,',');
					}
					$data['teammembers'] = $teammembers;
					$data['clientid'] = isset($data['clientid'])?$data['clientid']:0;
					// Team Members
					$id = $this->pipeline_model->add_pipeline($data);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pipeline')));
						redirect(admin_url('pipeline'));
					}
				}
            }
			else {
				$checkpipelineexist = $this->pipeline_model->checkpipelineExist($data['name']);
				if(!empty($checkpipelineexist) && $checkpipelineexist->id != $id) {
					set_alert('warning', _l('already_exist', _l('pipeline')));
					redirect(admin_url('pipeline'));
				}
				else {
					if (!has_permission('pipeline', '', 'edit')) {
						access_denied('pipeline');
					}
					// Status
					$status = NULL;
					if(!empty($data['status'])) {
						$postval_status = $data['status'];
						foreach($postval_status as $stat) {
							$status .= $stat.',';
						}
						$status = rtrim($status,',');
					}
					$data['status'] = $status;
					// Status
					
					// // Team Leaders
					// $teamleaders = NULL;
					// if(!empty($data['teamleader'])) {
					// 	$postval_teamleaders = $data['teamleader'];
					// 	foreach($postval_teamleaders as $teamlead) {
					// 		$teamleaders .= $teamlead.',';
					// 	}
					// 	$teamleaders = rtrim($teamleaders,',');
					// }
					// $data['teamleader'] = $teamleaders;
					// // Team Leaders
					
					// // Team Members
					// $teammembers = NULL;
					// if(!empty($data['teammembers'])) {
					// 	$postval_teammembers = $data['teammembers'];
					// 	foreach($postval_teammembers as $teammemb) {
					// 		$teammembers .= $teammemb.',';
					// 	}
					// 	$teammembers = rtrim($teammembers,',');
					// }
					// $data['teammembers'] = $teammembers;
					// Team Members
					$data['clientid'] = $data['clientid'];
					$success = $this->pipeline_model->update_pipeline($data, $id);
					if ($success) {
						set_alert('success', _l('updated_successfully', _l('pipeline')));
					}
					redirect(admin_url('pipeline'));
				}
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('pipeline_lowercase'));
			$data['default_statuses'] = array();
        }
		else {
            $pipeline = $this->pipeline_model->getpipelinebyId($id);
            $data['pipeline'] = $pipeline;
            $title = _l('edit', _l('pipeline')) . ' ' . $pipeline->name;
			$data['default_statuses'] = explode(',',$pipeline->status);
        }
        $data['bodyclass'] = 'kb-pipeline';
        $data['title']     = $title;
		$data['statuses'] = $this->leads_model->get_status();
		$data['leads'] = $this->pipeline_model->getLeads();
		$data['teamleaders'] = $this->pipeline_model->getTeamleaders();
		$data['teammembers'] = $this->pipeline_model->getTeammembers();
        $this->load->view('admin/pipeline/form', $data);
    }
	
	public function get_stage()
	{
		$postval = $this->input->post();
		$stage = $postval['stage'];
		
        $stages = '<option value=""></option>';
        $stagelist = $this->pipeline_model->get_pipeline_stage($stage);
        $selected = ' ';
        if(count($stagelist) == 1){
            $selected = 'selected="selected"';
        }
		if(!empty($stagelist) && !empty($stage)) {
			foreach($stagelist as $stage1) {
                if(!empty($stage1['name'])){
                    $stages .= '<option value="'.$stage1['id'].'" '.$selected.'>'.$stage1['name'].'</option>';
                }
			}
		}
	
		$data['stages'] = $stages;
		$data['mgs'] = 'Data loaded successfully';
		$data['status'] = true;
		echo json_encode($data,true);
		exit;
    }

/**
 * View existing pipeline details
**/
	public function view($id)
    {
        if (!has_permission('pipeline', '', 'view')) {
            access_denied('View pipeline');
        }
        $data['pipeline'] = $this->pipeline_model->getpipelinebyId($id);

        if (!$data['pipeline']) {
            show_404();
        }
        add_views_tracking('kb_pipeline', $id);
        $data['title'] = $data['pipeline']->name;
        $this->load->view('admin/pipeline/view', $data);
    }
	
/**
 * Delete existing pipeline details
**/
    public function delete_pipeline()
    {
        if (!has_permission('pipeline', '', 'delete')) {
            access_denied('pipeline');
        }
       
        $response = $this->pipeline_model->delete_pipeline();
        if ($response == true) {
            set_alert('success', _l('deleted', _l('pipeline')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('pipeline_lowercase')));
        }
        redirect(admin_url('pipeline'));
	}
	
	public function getpipelinedeals() {
        $id = $_GET['id'];
        $data = $this->pipeline_model->getpipelinedealstatus($id);
        echo json_encode($data);
        exit();
	}

	public function pickpipelineandstage()
	{
		$postval = $this->input->post();
		$pipeline = $postval['pipeline_id'];
		
        $get_staff_user_id = get_staff_user_id();
		
		$pipeline_html = '';
		$pipelinelist = $this->pipeline_model->getPipeline();
		$selected = ' ';
        if(count($pipelinelist) == 1){
            $selected = 'selected="selected"';
		}
		$i=0;
		if(!empty($pipelinelist)) {
			foreach($pipelinelist as $status) {
				if($status['id'] == $postval['pipeline_id']) {
					$selected = 'selected="selected"';
					$i = 1;
				} else {
					$selected = '';
				}
                if(!empty($status['name'])){
                $pipeline_html .= '<option value="'.$status['id'].'" '.$selected.'>'.$status['name'].'</option>';
                }
			}
		}
		$phtml = '';
		if($i > 0) {
			$phtml = $pipeline_html;
		} else {
			$phtml .="<option>Nothing Selected</option>";
			$phtml .= $pipeline_html;
		}

		$statuses = '';
        $statuseslist = $this->pipeline_model->getPipelineleadstatus($pipeline);
        $selected = ' ';
        if(count($statuseslist) == 1){
            $selected = 'selected="selected"';
		}
		$statuses .= '<option></option>';
		if(!empty($statuseslist)) {
			foreach($statuseslist as $status) {
				if($status['id'] == $postval['status']) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
                if(!empty($status['name'])){
                $statuses .= '<option value="'.$status['id'].'" '.$selected.'>'.$status['name'].'</option>';
                }
			}
		}
		$data = $this->pipeline_model->getPipelineClientDetails($pipeline);
		$data['statuses'] = $statuses;
		$data['pipelines'] = $phtml;
		$data['mgs'] = 'Data loaded successfully';
		$data['status'] = true;
		echo json_encode($data,true);
		exit;
    }
	
	public function changepipeline()
	{
		$postval = $this->input->post();
		$pipeline = $postval['pipeline_id'];
		
        $get_staff_user_id = get_staff_user_id();
		
		$statuses = '';
        $statuseslist = $this->pipeline_model->getPipelineleadstatus($pipeline);
        $selected = ' ';
        if(count($statuseslist) == 1){
            $selected = 'selected="selected"';
		}
		if(!empty($statuseslist)) {
			foreach($statuseslist as $status) {
                if(!empty($status['name'])){
                $statuses .= '<option value="'.$status['id'].'" '.$selected.'>'.$status['name'].'</option>';
                }
			}
		}
		$data = $this->pipeline_model->getPipelineClientDetails($pipeline);
		$data['statuses'] = $statuses;
		$data['mgs'] = 'Data loaded successfully';
		$data['status'] = true;
		echo json_encode($data,true);
		exit;
    }
}