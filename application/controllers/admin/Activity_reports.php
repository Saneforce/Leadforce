<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Activity_reports extends AdminController
{
    /**
     * Codeigniter Instance
     * Expenses detailed report filters use $ci
     * @var object
     */
    private $ci;

    public function __construct()
    {
        parent::__construct();
        
        $this->ci = &get_instance();
        $this->load->model('reports_model');
		$this->load->model('projects_model');
		$this->load->model('clients_model');
		$this->load->model('pipeline_model');
		$this->load->model('callsettings_model');
		$this->load->helper('report_summary');
		$this->load->helper('reports');
    }

    /* No access on this url */
    public function index()
    {
        redirect(admin_url());
    }
	public function add(){
		if (!has_permission('reports', '', 'create')) {
            access_denied('reports');
        }
		$this->load->model('pipeline_model');
		$data = array();
		$data['title'] = _l('add_report');
		$activity_val = task_values();
		$data =  json_decode($activity_val, true);
		$needed = get_tasks_need_fields();
		$needed['need_fields'][] = 'rel_type';
		$data['filters']	=	$filters = $this->session->userdata('activity_filters');
		$data['filters1']	=	$this->session->userdata('activity_filters1');
		$data['filters2']	=	$this->session->userdata('activity_filters2');
		$data['filters3']	=	$this->session->userdata('activity_filters3');
		$data['filters4']	=	$this->session->userdata('activity_filters4');
		$custom_fields = get_table_custom_fields('tasks');
		$customs = array_column($custom_fields, 'slug'); 
		if(!empty($filters)){
			$i = 0;
			foreach($filters as $filter1){
				if ((!empty($needed['need_fields']) && !in_array($filter1, $needed['need_fields'])) && (!in_array($filter1, $customs)) ){
					unset($data['activity_filters'][$i]);
					unset($data['activity_filters1'][$i]);
					unset($data['activity_filters2'][$i]);
					unset($data['activity_filters3'][$i]);
					unset($data['activity_filters4'][$i]);
				}
				$i++;
			}
		}
		$data['folders'] = get_report_folder('activity');
		$data['id'] = $data['links'] = '';
		if (($key = array_search('id', $needed['need_fields'])) !== false) {
			unset($needed['need_fields'][$key]);
		}
		if (($key = array_search('task_name', $needed['need_fields'])) !== false) {
			unset($needed['need_fields'][$key]);
		}
		$data['report_name']		=	$this->session->userdata('report_type');
		$data['folder_id']			=	'';
		$data['need_fields']		=	$needed['need_fields'];
		$data['need_fields_label']	=	$needed['need_fields_label'];
		$data['need_fields_edit']	=	$needed['need_fields_edit'];
		$data['mandatory_fields1']	=	$needed['mandatory_fields1'];
		unset($data['all_clmns']['id']);
		unset($data['all_clmns']['task_name']);
		unset($data['all_clmns']['description']);
		$data['report_page'] = 'activity';
		$data['report_filter'] =  $this->load->view('admin/reports/filter', $data,true);
		$data['report_footer'] =  $this->load->view('admin/reports/report_footer', $data,true);
		$data['teamleaders'] = $this->staff_model->get('', [ 'active' => 1]);
		$data['summary'] = array();
		$data['summary'] = activity_performance_summary($data);
		$data['cur_tab'] = '1';
		$data['cur_tab2'] = '';
        $this->load->view('admin/reports/deals_views', $data);
	}
	
	public function edit($id){
		if (!has_permission('reports', '', 'create')) {
            access_denied('reports');
        }
		if (!has_permission('reports', '', 'view')) {
            access_denied('reports');
        }
		$this->load->model('pipeline_model');
		$data = array();
		$data = get_edit_data('activity',$id);
		//$data['summary'] = $this->performance_summary($data);
		$data['report_page'] = 'activity';
		$fields = 'tab_1,tab_2,view_by,view_type,date_range,measure_by';
		$condition = array('id'=>$id);
		$this->db->select($fields);
		$this->db->from(db_prefix().'report');
		$this->db->where($condition); 
		$query = $this->db->get();
		$res = $query->result_array();
		$data['summary'] = activity_performance_summary($data,$res[0]['view_by'],$res[0]['view_type'],$res[0]['date_range'],$res[0]['measure_by']);
		$data['cur_tab']  = $res[0]['tab_1'];
		$data['cur_tab2'] = $res[0]['tab_2'];
        $this->load->view('admin/reports/deals_views', $data);
	}
	public function set_filters($cur_val='',$cur_num1=''){
		$cur_id12 = '';
		if(!empty($_REQUEST['cur_id12'])){
			$cur_id12 = '_edit_'.$_REQUEST['cur_id12'];
		}
		$filters	=	$this->session->userdata('activity_filters'.$cur_id12);
		if(!empty($filters)){
			foreach($filters as $key12 => $filter12){
				$filter_data['activity_filters'.$cur_id12][$key12]	=	$filter12;  
			}
		}
		if(!empty($cur_val) && !empty($cur_num1)){
			$filter_data['activity_filters'.$cur_id12][$cur_num1]	=	$cur_val;
		}
		else{
			$cur_val  = $_REQUEST['cur_val'];
			$cur_num1 = $_REQUEST['req_val']-1;
			$filter_data['activity_filters'.$cur_id12][$cur_num1]	=	$cur_val;
		}
		$filters1	=	$this->session->userdata('activity_filters1'.$cur_id12);
		$filters2	=	$this->session->userdata('activity_filters2'.$cur_id12);
		if(!empty($filters1)){
			foreach($filters1 as $key12 => $filter1){
				$filter_data['activity_filters1'.$cur_id12][$key12]	=	$filter1;  
			}
		}
		if(!empty($filters2)){
			foreach($filters2 as $key12 => $filter1){
				$filter_data['activity_filters2'.$cur_id12][$key12]	=	$filter1;  
			}
		}
		switch($cur_val){
			case 'startdate':
			case 'dateadded':
			case 'datemodified':
			case 'datefinished':
				$filters3	=	$this->session->userdata('activity_filters3'.$cur_id12);
				$filters4	=	$this->session->userdata('activity_filters4'.$cur_id12);
				if(!empty($filters3)){
					foreach($filters3 as $key12 => $filter1){
						$filter_data['activity_filters3'.$cur_id12][$key12]	=	$filter1;
					}
				}
				if(!empty($filters4)){
					foreach($filters4 as $key12 => $filter1){
						$filter_data['activity_filters4'.$cur_id12][$key12]	=	$filter1;
					}
				}
				$filter_data['activity_filters1'.$cur_id12][$cur_num1]	=	'is';  
				$filter_data['activity_filters2'.$cur_id12][$cur_num1]	=	'this_year';  
				$filter_data['activity_filters3'.$cur_id12][$cur_num1]	=	'01-01-'.date('Y');  
				$filter_data['activity_filters4'.$cur_id12][$cur_num1]	=	'31-12-'.date('Y'); 
				break;
			case 'assignees':
				$filter_data['activity_filters1'.$cur_id12][$cur_num1]	=	'is'; 
				$filter_data['activity_filters2'.$cur_id12][$cur_num1]  =   '';
			case 'tags':
				$filter_data['activity_filters1'.$cur_id12][$cur_num1]	=	'is'; 
				break;
			case 'tasktype':
				$this->db->where('status', 'Active');
				$task_types = $this->db->get(db_prefix() . 'tasktype')->result_array();
				$filter_data['activity_filters1'.$cur_id12][$cur_num1]	=	'is'; 
				$filter_data['activity_filters2'.$cur_id12][$cur_num1]  = 	$task_types[0]['name'];
				break;
			case 'rel_type':
				$filter_data['activity_filters1'.$cur_id12][$cur_num1]	=	'is'; 
				$filter_data['activity_filters2'.$cur_id12][$cur_num1]  =	'contact';
				break;
			case 'product_qty':
			case 'product_amt':
			case 'project_cost':
				$filter_data['activity_filters1'.$cur_id12][$cur_num1]	=	'is_more_than';  
				break;
			default:
				$fields =  $this->db->query("SELECT type FROM " . db_prefix() . "customfields where slug = '".$cur_val."' ")->row();
				if($fields->type == 'date_picker' || $fields->type == 'date_picker_time' || $fields->type == 'date_range'){
					$filters3	=	$this->session->userdata('activity_filters3'.$cur_id12);
					$filters4	=	$this->session->userdata('activity_filters4'.$cur_id12);
					if(!empty($filters3)){
						foreach($filters3 as $key12 => $filter1){
							$filter_data['activity_filters3'.$cur_id12][$key12]	=	$filter1;  
						}
					}
					if(!empty($filters4)){
						foreach($filters4 as $key12 => $filter1){
							$filter_data['activity_filters4'.$cur_id12][$key12]	=	$filter1;  
						}
					}
					$filter_data['activity_filters1'.$cur_id12][$cur_num1]	=	'is';  
					$filter_data['activity_filters2'.$cur_id12][$cur_num1]	=	'this_year';  
					$filter_data['activity_filters3'.$cur_id12][$cur_num1]	=	'01-01-'.date('Y');  
					$filter_data['activity_filters4'.$cur_id12][$cur_num1]	=	'31-12-'.date('Y'); 
				}
				else if($fields->type == 'select'){
					$filter_data['activity_filters1'.$cur_id12][$cur_num1]	=	'is';
				}
				else if($fields->type == 'number'){
					$filter_data['activity_filters1'.$cur_id12][$cur_num1]	=	'is_more_than';
				}
				else{
					$filter_data['activity_filters1'.$cur_id12][$cur_num1]	=	'is';
				}
				break;
		}	
		$this->session->set_userdata($filter_data);
		return true;
	}
	public function add_filter(){
		$cur_id12 = '';
		if(!empty($_REQUEST['cur_id12'])){
			$cur_id12 = '_edit_'.$_REQUEST['cur_id12'];
		}
		$cur_num1	=	$_REQUEST['cur_num'];
		$cur_num	=	$_REQUEST['cur_num'] +1;
		$deal_val	=	task_values();
		$data		=	json_decode($deal_val, true);
		unset($data['all_clmns']['id']);
		unset($data['all_clmns']['task_name']);
		unset($data['all_clmns']['description']);
		$all_clmns  =	$data['all_clmns'];
		$cus_flds = $data['cus_flds'];
		$filter_data = set_filter('activity_',$all_clmns,$cus_flds);
		$this->session->set_userdata($filter_data);
		
		$filters	=	$this->session->userdata('activity_filters'.$cur_id12);

		$req_out = '';
		$needed = get_tasks_need_fields();
		$need_fields		=	$needed['need_fields'];
		$needed['need_fields'][] = 'rel_type';
		if (($key = array_search('id', $needed['need_fields'])) !== false ) {
			unset($needed['need_fields'][$key]);
		}
		if (($key = array_search('task_name', $needed['need_fields'])) !== false ) {
			unset($needed['need_fields'][$key]);
		}
		if(!empty($filters)){
			$i1 = 1;
			foreach($filters as $key => $filter1){
				$req_out	.=	'<div  class="col-md-12 m-bt-10"><div  class="col-md-2" >';
				$req_out	.=	'<select data-live-search="true" class="selectpicker" id="filter_'.$i1.'" onchange="change_filter(this)">';
				if(!empty($all_clmns)){ 
					$req_out	.=	'<optgroup label="Activity Master" data-max-options="2">';
					foreach ($all_clmns as $key1 => $all_val1){
						if(($key1==$filter1 || !in_array($key1, $filters)) && in_array($key1, $need_fields)){
							if($key1==$filter1){
								$req_out	.=	'<option value="'.$key1.'" selected>'._l($all_val1['ll']).'</option>';
							}else{
								$req_out	.=	'<option value="'.$key1.'">'._l($all_val1['ll']).'</option>';
							}
						}
					}
					$req_out	.=	'</optgroup>';
				}
				if(!empty($cus_flds)){
					$req_out	.=	'<optgroup label="Custom Fields" data-max-options="2">';
					foreach ($cus_flds as  $key12 => $cus_fld1){
						if($key12==$filter1 || !in_array($key12, $filters)){
							if($key12==$filter1){
								$req_out	.=	'<option value="'.$key12.'" selected>'.$cus_fld1['ll'].'</option>';
							}else{
								$req_out	.=	'<option value="'.$key12.'">'.$cus_fld1['ll'].'</option>';
							}
						}
					}
					$req_out	.=	'</optgroup>';
				}
				$req_out	.=	'</select></div><div  class="col-md-6" ><div id="ch_dr_'.$i1.'">';
				$req_out	.=	$this->get_filters($filter1,$i1);
				$req_out	.=	'</div></div></div></div></div>';
				$i1++;
			}
		}
		$req_val = array('output'=>$req_out,'cur_num'=>$cur_num);
		echo json_encode($req_val);
	}
	public function get_filters($cur_val='',$req_val=''){
		$cur_id12 = '';
		if(!empty($_REQUEST['cur_id12'])){
			$cur_id12 = '_edit_'.$_REQUEST['cur_id12'];
		}
		$filters	=	$this->session->userdata('activity_filters'.$cur_id12);
		$filters2	=	$this->session->userdata('activity_filters2'.$cur_id12);
		$check_val1 =	$cur_val;
		$check_val2	=	$req_val;
		if(empty($cur_val)){
			$cur_val = $_REQUEST['cur_val'];
		}
		if(empty($req_val)){
			$req_val = $_REQUEST['req_val'];
		}
		$req_out = '';
		switch($cur_val){
			case 'teamleader':
				$selected = '';
				$rel_data = get_relation_data('manager',$selected);
				$rel_val  = get_relation_values($rel_data,'contacts');
				if(empty($filters2[$req_val-1])){
					$req_out = get_req_val($req_val,'select','id','name','',$rel_val,'task');
				}
				else{
					$req_data = array();
					if (str_contains($filters2[$req_val-1], ',')) {
						$req_vals1 = explode(',',$filters2[$req_val-1]);
						$i2 = 0;
					}
					foreach($rel_data as $rel_li1){
						if (str_contains($filters2[$req_val-1], ',')) {
							if(in_array($rel_li1['staffid'],$req_vals1)){
								$req_data[$i2] = $rel_li1;
								$i2++;
							}
						}
						else{
							if($rel_li1['staffid']==$filters2[$req_val-1]){
								$req_data[0] = $rel_li1;
								break;
							}
						}
					}
					$req_out = get_req_val($req_val,'select','staffid','firstname,lastname','',$req_data,'task');
				}
				break;
			case 'task_name':
				$selected = '';
					$cond = array();
					$rel_data = get_relation_data('task',$selected);
					$req_data = array();
					$rel_val = get_relation_values($rel_data,'task');
					if(empty($filters2[$req_val-1])){
						$req_out = get_req_val($req_val,'select','id','name','',$rel_val,'task');
					}else{
						if (str_contains($filters2[$req_val-1], ',')) {
							$req_vals1 = explode(',',$filters2[$req_val-1]);
							$i2 =0;
						}
						foreach($rel_data as $rel_li1){
							if (str_contains($filters2[$req_val-1], ',')) {
								if(in_array($rel_li1['id'],$req_vals1)){
									$req_data[$i2] = $rel_li1;
									$i2++;
								}
							}
							else{
								if($rel_li1['id']==$filters2[$req_val-1]){
									$req_data[0] = $rel_li1;
									break;
								}
							}
						}
						$req_out = get_req_val($req_val,'select','id','name','',$req_data,'task');
					}
				break;
			case 'tasktype':
				$this->db->where('status', 'Active');
				$task_types = $this->db->get(db_prefix() . 'tasktype')->result_array();
				$req_out	= get_req_val($req_val,'select','id','name','',$task_types,'task');
				break;
			case 'rel_type':
				$all_status = array('contact'=>_l('contact'),'customer'=>_l('clients'),'project'=>_l('project'),'lead'=>_l('lead'),'proposal'=>_l('proposal'));
				$req_out = get_req_val($req_val,'select','','','key',$all_status,'task');
				break;
			case 'project_contacts':
				$selected = '';
				$rel_data = get_relation_data('contacts',$selected);
				$rel_val = get_relation_values($rel_data,'contacts');
				if(empty($filters2[$req_val-1])){
					$req_out = get_req_val($req_val,'select','id','name','',$rel_val,'task');
				}
				else{
					$req_data = array();
					if (str_contains($filters2[$req_val-1], ',')) {
						$req_vals1 = explode(',',$filters2[$req_val-1]);
						$i2 =0;
					}
					foreach($rel_data as $rel_li1){
						if (str_contains($filters2[$req_val-1], ',')) {
							if(in_array($rel_li1['id'],$req_vals1)){
								$req_data[$i2] = $rel_li1;
								$i2++;
							}
						}
						else{
							if($rel_li1['id']==$filters2[$req_val-1]){
								$req_data[0] = $rel_li1;
								break;
							}
						}
					}
					$req_out = get_req_val($req_val,'select','id','firstname,lastname','',$req_data,'task');
				}
				break;	
			case 'company':
				$selected = '';
				$rel_data = get_relation_data('customer',$selected);
				$rel_val = get_relation_values($rel_data,'customer');
				if(empty($filters2[$req_val-1])){
					$req_out = get_req_val($req_val,'select','id','name','',$rel_val,'task');
				}
				else{
					$req_data = array();
					if (str_contains($filters2[$req_val-1], ',')) {
						$req_vals1 = explode(',',$filters2[$req_val-1]);
						$i2 =0;
					}
					foreach($rel_data as $rel_li1){
						if (str_contains($filters2[$req_val-1], ',')) {
							if(in_array($rel_li1['userid'],$req_vals1)){
								$req_data[$i2] = $rel_li1;
								$i2++;
							}
						}
						else{
							if($rel_li1['userid']==$filters2[$req_val-1]){
								$req_data[0] = $rel_li1;
								break;
							}
						}
					}
					$req_out = get_req_val($req_val,'select','userid','company','',$req_data,'task');
				}
				break;
			case 'dateadded':
			case 'startdate':
			case 'datemodified':
			case 'datefinished':
				$req_out = get_req_val($req_val,'date','','','','','task');
				break;
			case 'assignees':
				$selected = '';
				$rel_data = get_relation_data('staff',$selected);
				$rel_val = get_relation_values($rel_data,'staff');
				if(empty($filters2[$req_val-1])){
					$req_out = get_req_val($req_val,'select','id','name','',$rel_val,'task');
				}else{
					if (str_contains($filters2[$req_val-1], ',')) {
						$req_vals1 = explode(',',$filters2[$req_val-1]);
						$i2 =0;
					}
					foreach($rel_data as $rel_li1){
						if (str_contains($filters2[$req_val-1], ',')) {
							if(in_array($rel_li1['staffid'],$req_vals1)){
								$req_data[$i2] = $rel_li1;
								$i2++;
							}
						}
						else{
							if($rel_li1['staffid']==$filters2[$req_val-1]){
								$req_data[0] = $rel_li1;
								break;
							}
						}
					}
					$req_out = get_req_val($req_val,'select','staffid','firstname,lastname','',$req_data,'task');
				}
				break;
			case 'status':
				$all_status = array('1'=>_l('task_status_1'),'2'=>_l('task_status_2'),'3'=>_l('task_status_3'),'5'=>_l('task_status_5'));
				$req_out = get_req_val($req_val,'select','','','key',$all_status,'task');
				break;
			case 'project_status':
				$all_status = get_stage_report();
				$req_out	= get_req_val($req_val,'select','id','name','',$all_status,'task');
				break;
			case 'priority':
				$all_status = array('1'=>_l('task_priority_low'),'2'=>_l('task_priority_medium'),'3'=>_l('task_priority_high'),'4'=>_l('task_priority_urgent'));
				$req_out = get_req_val($req_val,'select','','','key',$all_status,'task');
				break;
			case 'project_pipeline':
				$pipelines  = get_pipeline_report();
				$req_out	= get_req_val($req_val,'select','id','name','',$pipelines,'task');
				break;
			case 'project_status':
				$all_status = get_stage_report();
				$req_out = get_req_val($req_val,'select','id','name','',$all_status,'task');
				break;
			case 'project_name':
				$selected = '';
				$cond	  = array();
				$rel_data = get_relation_data('project',$selected);
				$req_data = array();
				$rel_val  = get_relation_values($rel_data,'project');
				if(empty($filters2[$req_val-1])){
					$req_out = get_req_val($req_val,'select','id','name','',$rel_val,'task');
				}else{
					if (str_contains($filters2[$req_val-1], ',')) {
						$req_vals1 = explode(',',$filters2[$req_val-1]);
						$i2 =0;
					}
					foreach($rel_data as $rel_li1){
						if (str_contains($filters2[$req_val-1], ',')) {
							if(in_array($rel_li1['id'],$req_vals1)){
								$req_data[$i2] = $rel_li1;
								$i2++;
							}
						}
						else{
							if($rel_li1['id']==$filters2[$req_val-1]){
								$req_data[0] = $rel_li1;
								break;
							}
						}
					}
					$req_out = get_req_val($req_val,'select','id','name','',$req_data,'task');
				}
				break;
			case 'tags':
				$selected = '';
				$rel_val = get_relation_values($rel_data,'tags');
				$rel_data = get_relation_data('tags',$selected);
				if(empty($filters2[$req_val-1])){
					$rel_data = array();
					$req_out = get_req_val($req_val,'select','id','name','',$rel_data,'task');
				}else{
					if (str_contains($filters2[$req_val-1], ',')) {
						$req_vals1 = explode(',',$filters2[$req_val-1]);
						$i2 =0;
					}
					foreach($rel_data as $rel_li1){
						if (str_contains($filters2[$req_val-1], ',')) {
							if(in_array($rel_li1['id'],$req_vals1)){
								$req_data[$i2] = $rel_li1;
								$i2++;
							}
						}
						else{
							if($rel_li1['id']==$filters2[$req_val-1]){
								$req_data[0] = $rel_li1;
								break;
							}
						}
					}
					$req_out = get_req_val($req_val,'select','id','name','',$req_data,'task');
				}
				break;
			default:
				$fields =  $this->db->query("SELECT type,options FROM " . db_prefix() . "customfields where slug = '".$cur_val."' ")->row();
				if($fields->type == 'date_picker' || $fields->type == 'date_picker_time' || $fields->type == 'date_range'){
					$req_out = get_req_val($req_val,'date','','','','','task');
				}
				else if($fields->type == 'select' || $fields->type=='multiselect'){
					$req_array = array();
					if (str_contains($fields->options, ',')) { 
						$options = explode(',',$fields->options);
						foreach($options as $option1){
							$req_array[$option1] = $option1;
						}
					}
					else{
						$req_array[$fields->options] = $fields->options;
					}
					$req_out = get_req_val($req_val,'select','','','key',$req_array,'task');
				}
				else if($fields->type == 'number'){
					$req_out = get_req_val($req_val,'number','','','','','task');
				}
				else{
					$req_out = get_req_val($req_val,'text','','','','','task');
				} 
				//$req_out = get_default_val($cur_val,$req_val,'task');
				break;
		}
		if(!empty($check_val1) && !empty($check_val2)){
			return $req_out;
		}else{
			echo $req_out;
		}
	}
}