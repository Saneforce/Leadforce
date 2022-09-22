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
	public function summary(){
		if(isset($_POST['submit'])){
			$filter_data['view_by']		=	$_POST['view_by'];
			$filter_data['view_type']	=	$_POST['view_type'];
			$filter_data['date_range1']	=	$_POST['date_range1'];
			$filter_data['sel_measure']	=	$_POST['sel_measure'];
			$this->session->set_userdata($filter_data);
			if(!empty($_POST['summary_edit'])){
				redirect(admin_url('activity_reports/edit/'.$_POST['summary_edit']));
				exit;
			}
		}
		redirect(admin_url('activity_reports/add'));
	}
	public function activity_table($clientid = '')
    {
		$cur_id =  '';
		if(!empty($clientid)){
			$cur_id = '_edit_'.$clientid;
		}
		$data['filters']	=	$this->session->userdata('activity_filters'.$cur_id);
		$data['filters1']	=	$this->session->userdata('activity_filters1'.$cur_id);
		$data['filters2']	=	$this->session->userdata('activity_filters2'.$cur_id);
		$data['filters3']	=	$this->session->userdata('activity_filters3'.$cur_id);
		$data['filters4']	=	$this->session->userdata('activity_filters4'.$cur_id);
		$needed = get_tasks_need_fields();
		if($clientid == ''){
			$data['report_name']	=	$this->session->userdata('report_type');
		}
		else{
			$reports1 = $this->db->query("SELECT report_type,folder_id FROM " . db_prefix() . "report WHERE id = '".$clientid."' ")->row();
			$data['report_name']	=	$reports1->report_type;
		}
		$data['need_fields']		=	$needed['need_fields'];
		$data['need_fields_label']	=	$needed['need_fields_label'];
		$data['need_fields_edit']	=	$needed['need_fields_edit'];
		$data['mandatory_fields1']	=	$needed['mandatory_fields1'];
		$data['clientid'] = $clientid;
        $this->app->get_table_data('report_activity', $data);
    }
	public function check_view_by(){
		if ($this->input->is_ajax_request()) {
			$view_by	=	$_REQUEST['view_by'];
			if($view_by == 'startdate' || $view_by == 'dateadded' || $view_by == 'datemodified' || $view_by == 'datefinished'){
				echo 'date';
			}
			else{
				$fields			= deal_needed_fields();
				$needed			= json_decode($fields,true);
				$need_fields	= $needed['need_fields'];
				if (in_array($view_by, $need_fields))
				{
					echo '';
				}
				else{
					$ch_custom	= $this->db->query("SELECT id  FROM " . db_prefix() . "customfields where slug = '".$view_by."' and type = 'date_picker'")->result_array();
					if(!empty($ch_custom)){
						echo 'date';
					}
					else{
						echo '';
					}
				}
			}
		}
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
					unset($data['filters'][$i]);
					unset($data['filters1'][$i]);
					unset($data['filters2'][$i]);
					unset($data['filters3'][$i]);
					unset($data['filters4'][$i]);
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
		$data['summary'] = $this->performance_summary($data);
		$data['report_page'] = 'activity';
        $this->load->view('admin/reports/deals_views', $data);
	}
	public function get_task_summary(){
		if ($this->input->is_ajax_request()) {			
			$task_val = task_values();
			$tasks =  json_decode($task_val, true);
			$cur_id =  '';
			if(!empty($_REQUEST['edit_id'])){
				$cur_id = '_edit_'.$_REQUEST['edit_id'];
			}
			$deals['filters']	=	$this->session->userdata('activity_filters'.$cur_id);
			$deals['filters1']	=	$this->session->userdata('activity_filters1'.$cur_id);
			$deals['filters2']	=	$this->session->userdata('activity_filters2'.$cur_id);
			$deals['filters3']	=	$this->session->userdata('activity_filters3'.$cur_id);
			$deals['filters4']	=	$this->session->userdata('activity_filters4'.$cur_id);
			$data = $_REQUEST;
			$needed = get_tasks_need_fields();
			if (($key = array_search('id', $needed['need_fields'])) !== false) {
				//unset($needed['need_fields'][$key]); 
			}
			$data['need_fields']	=	$needed['need_fields'];
			
			$data['results']		=	get_task_qry($data['clmn'],$data['crow'],$data['view_by'],$data['measure'],$data['date_range'],$data['view_type'],$data['sum_id'],$deals);
			if($data['date_range']	==	'Monthly' && $data['view_type'] == 'date'){
				$req_month =  (int) $data['crow'];
				$req_date  = '01-'.$req_month.'-'.date('Y');
				$data['cur_record']	=	date('M',strtotime($req_date)).' '.date('Y').', '.ucfirst($data['clmn']).' deals';
			}
			else{
				if($data['clmn'] == 'total_cnt_task'){
					$data['cur_record']	=	$data['crow'].', '._l($data['clmn']);
				}else{
					$data['cur_record']	=	$data['crow'].', '._l($data['clmn']).' '._l('deals');
				}
			}
			$data['summary']		=	$this->load->view('admin/reports/activity_summary_table', $data,true);
			echo json_encode($data,true);
		}
	}
	public function performance_summary($filters){
		$cur_year  = date('Y');
		$data 	   = array();
		$data['rows']			=	array();
		$data['view_by']		=	$view_by = $this->session->userdata('view_by');
		if(empty($view_by)){
			$data['view_by']	=   $view_by = 'status';
		}
		$data['view_type']		=	$this->session->userdata('view_type');
		$data['date_range1']	=	$this->session->userdata('date_range1');
		$data['sel_measure']	=	$this->session->userdata('sel_measure');
		$data['columns']		=	array($view_by,'upcoming','overdue','today','completed','total_val_task');
		$i1 = $upcoming  = $overdue =  $today = $in_progress = $completed = 0;
		if(!empty($data['columns'])){
			foreach($data['columns'] as $clmn1){
				$data['summary_cls'][$i1++]['vals'] = _l($clmn1);
				$i1++;
			}
		}
		if($data['view_type'] != 'date'){
			$fields   = get_task_table_fields($view_by);
			$sum_data = summary_val($fields['tables'],$fields['fields'],$fields['qry_cond'],$data['sel_measure'],$view_by,$fields['cur_rows'],$filters,'activity');
		}
		else{
			$months = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
			$tot_cnt = $tot_prt = $tot_val = $avg_deal = $tot_avg = 0; 
			if($data['view_type'] == 'date' && ($data['date_range1'] == 'Monthly')){
				if(!empty($months)){
					$j = 1;$i = 0;
					foreach($months as $month1){
						if(check_task_date($view_by)){
							$j = $i+1;
							$qry_cond   = " MONTH(".db_prefix()."tasks.".$view_by.") = '".$j."' and YEAR(".db_prefix()."tasks.".$view_by.") = '".$cur_year."'";
							$cur_row    = ($month1).' '.$cur_year;
							$sum_data[$i]	= date_summary($qry_cond,$cur_row,$data['sel_measure'],$view_by,$filters);
							$i++;
						}
						else{
							$cur_row    = ($month1).' '.$cur_year;
							$j1 = $j;
							if($j<10){
								$j1 = '0'.$j;
							}
							$ch_value = $cur_year.'-'.$j1;
							$qry_cond = '';
							$customs   = $this->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and cv.value like '%".$ch_value."%' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
							$cur_projects = '';
							if(!empty($customs)){
								foreach($customs as $custom1){
									$cur_projects .= $custom1['relid'].',';
								}	
								$cur_projects = rtrim($cur_projects,",");
								$qry_cond   = " ".db_prefix()."tasks.id in(".$cur_projects.")";
							}
							else{
								$qry_cond   = " ".db_prefix()."tasks.id=''";
							}
							$cur_row    = ($month1).' '.$cur_year;
							$sum_data[$i]	= date_summary($qry_cond,$cur_row,$data['sel_measure'],$view_by,$filters);
							
							$i++;
							$j++;
						}
						$tot_avg 	= 	$tot_avg + $sum_data[$i-1]['avg_task'];
						$upcoming	=	$upcoming + $sum_data[$i-1]['upcoming'];
						$overdue	=	$overdue + $sum_data[$i-1]['overdue'];
						$today		=	$today + $sum_data[$i-1]['today'];
						$in_progress=	$in_progress + $sum_data[$i-1]['in_progress'];
						$completed	=	$completed + $sum_data[$i-1]['completed'];
						$tot_cnt=	$tot_cnt + $sum_data[$i-1]['total_val_task'];
						$tot_val=	$tot_val + $sum_data[$i-1]['total_val_task'];
					}
					$sum_data[$i] = task_avg($upcoming,$overdue,$today,$in_progress,$completed,$tot_cnt,$tot_val,$view_by,$i,$tot_avg);
					$i++;
					$sum_data[$i] = task_total($upcoming,$overdue,$today,$in_progress,$completed,$tot_cnt,$tot_val,$view_by,$tot_avg);
				}
			}
			if($data['view_type'] == 'date' && ($data['date_range1'] == 'Weekly')){
				$cur_month = date('M');
				$cur_date  = date('d');
				$num_dates = $m = $tot_avg = 0;
				$sum_data  = array();
				$months_num = array('Jan'=>31,'Feb'=>28,'Mar'=>31,'Apr'=>30,'May'=>31,'Jun'=>30,'Jul'=>31,'Aug'=>31,'Sep'=>30,'Oct'=>31,'Nov'=>30,'Dec'=>31);
				if($cur_year % 4 == 0){
					$months_num['Feb'] = 29;
				}
				if(!empty($months_num)){
					foreach($months_num as $key => $month1){
						if($key == $cur_month){
							$num_dates = $num_dates + (int) $cur_date;
							break;
						}
						else{
							$num_dates = $num_dates + $month1;
						}	
					}
				}
				$weeks = ceil($num_dates/7);
				if(!empty($weeks)){
					$w_start_date	= 1;
					$w_end_date		= 7;
					for($i=0;$i<$weeks;$i++){
						$j = $i +1;
						$end_days	= $j*7;
						$start_days	= $end_days - 6;
						$num_month =  0;$k = 1;
						$qry_cond = '';
						foreach($months_num as $key => $req_month){
							$num_month = $num_month + $req_month;
							if($num_month >= $start_days && $num_month <= $end_days){
								$start_date	= date('Y-m-d',strtotime($w_start_date.'-'.$key.'-'.$cur_year));
								$end_date   = date('Y-m-d',strtotime($req_month.'-'.$key.'-'.$cur_year));
								if(check_task_date($view_by)){
									$qry_cond   .= "  ".db_prefix()."tasks.".$view_by." >= '".$start_date."' ";
								}
								else{
									$customs   = $this->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and CONVERT(cv.value,date)  >='".$start_date."' and CONVERT(cv.value,date) <='".$end_date."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
									$cur_projects = '';
									if(!empty($customs)){
										foreach($customs as $custom1){
											$cur_projects .= $custom1['relid'].',';
										}	
										$cur_projects = rtrim($cur_projects,",");
										$qry_cond   .= " ".db_prefix()."tasks.id in(".$cur_projects.")";
									}
									else{
										$qry_cond   .= " and ".db_prefix()."tasks.id=''";
									}
								}
								$upcoming	=	$upcoming + $sum_data[$m-1]['upcoming'];
								$overdue	=	$overdue + $sum_data[$m-1]['overdue'];
								$today		=	$today + $sum_data[$m-1]['today'];
								$in_progress=	$in_progress + $sum_data[$m-1]['in_progress'];
								$completed	=	$completed + $sum_data[$m-1]['completed'];
								$tot_cnt=	$tot_cnt + $sum_data[$m-1]['total_val_task'];
								$tot_val=	$tot_val + $sum_data[$m-1]['total_val_task'];
								$k++;
								$req_end_days = $w_end_date - $req_month;
								$w_start_date	= 1;
								$w_end_date		= $req_end_days;
								
								$req_key = array_search ($key, $months);
								$start_date  = date('Y-m-d',strtotime($w_start_date.'-'.$months[$req_key+1].'-'.$cur_year));
								$end_date	 = date('Y-m-d',strtotime($req_end_days.'-'.$months[$req_key+1].'-'.$cur_year));
								if(check_task_date($view_by)){
									$qry_cond 	 .= " and ".db_prefix()."tasks.".$view_by." <= '".$end_date."'";
								}else{
									$customs   = $this->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and CONVERT(cv.value,date)  >='".$start_date."' and CONVERT(cv.value,date) <='".$end_date."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
									$cur_projects = '';
									if(!empty($customs)){
										foreach($customs as $custom1){
											$cur_projects .= $custom1['relid'].',';
										}	
										$cur_projects = rtrim($cur_projects,",");
										$qry_cond   .= " and ".db_prefix()."tasks.id in(".$cur_projects.")";
									}
									else{
										$qry_cond   .= " and ".db_prefix()."tasks.id=''";
									}
								}
								$cur_row    = 'W'.($m+1).' '.$cur_year;
								$sum_data[$m]	= date_summary($qry_cond,$cur_row,$data['sel_measure'],$view_by,$filters);
								$m++;
								
								$w_start_date	= $w_end_date +1;
								$w_end_date		= $w_end_date +7;
								break;
							}
							else{
								if($num_month >= $start_days){
									$start_date  = date('Y-m-d',strtotime($w_start_date.'-'.$key.'-'.$cur_year));
									$end_date	 = date('Y-m-d',strtotime($w_end_date.'-'.$key.'-'.$cur_year));
									if(check_task_date($view_by)){
										$qry_cond 	 = " ".db_prefix()."tasks.".$view_by." >= '".$start_date."' and ".db_prefix()."tasks.".$view_by." <= '".$end_date."'";
									}
									else{
										$customs   = $this->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and CONVERT(cv.value,date)  >='".$start_date."' and CONVERT(cv.value,date) <='".$end_date."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
										$cur_projects = '';
										if(!empty($customs)){
											foreach($customs as $custom1){
												$cur_projects .= $custom1['relid'].',';
											}	
											$cur_projects = rtrim($cur_projects,",");
											$qry_cond   = " ".db_prefix()."tasks.id in(".$cur_projects.")";
										}
										else{
											$qry_cond   = " ".db_prefix()."tasks.id=''";
										}
									}
									$cur_row    = 'W'.($m+1).' '.$cur_year;
									$sum_data[$m]	= date_summary($qry_cond,$cur_row,$data['sel_measure'],$view_by,$filters);
									$m++;
									$upcoming	=	$upcoming + $sum_data[$m-1]['upcoming'];
									$overdue	=	$overdue + $sum_data[$m-1]['overdue'];
									$today		=	$today + $sum_data[$m-1]['today'];
									$in_progress=	$in_progress + $sum_data[$m-1]['in_progress'];
									$completed	=	$completed + $sum_data[$m-1]['completed'];
									$tot_cnt=	$tot_cnt + $sum_data[$m-1]['total_val_task'];
									$tot_val=	$tot_val + $sum_data[$m-1]['total_val_task'];
									$tot_avg = $tot_avg + $sum_data[$m-1]['avg_task'];
									$w_start_date	= $w_end_date +1;
									$w_end_date		= $w_end_date +7;
									break;
								}
							}
							$k++;
						}
					}
					$upcoming	= array_sum(array_column($sum_data,'upcoming'));
					$overdue	= array_sum(array_column($sum_data,'overdue'));
					$today		= array_sum(array_column($sum_data,'today'));
					$in_progress= array_sum(array_column($sum_data,'in_progress'));
					$completed	= array_sum(array_column($sum_data,'completed'));
					$tot_cnt	= $tot_val = $upcoming + $overdue + $today + $in_progress+ $completed;
					$sum_data[$m] = task_avg($upcoming,$overdue,$today,$in_progress,$completed,$tot_cnt,$tot_val,$view_by,$m,$tot_avg);
					$m++;
					$sum_data[$m] = task_total($upcoming,$overdue,$today,$in_progress,$completed,$tot_cnt,$tot_val,$view_by,$tot_avg);
				}
			}
			if($data['view_type'] == 'date' && ($data['date_range1'] == 'Quarterly')){	
				$month_period = array(31,30,30,31);
				$j = 1;
				for($i=0;$i<=3;$i++){
					$k = $j+2;
					$start_date = $cur_year.'-'.$j.'-1';
					$end_date   = $cur_year.'-'.$k.'-'.$month_period[$i];
					if(check_task_date($view_by)){
						$qry_cond   = " ".db_prefix()."tasks.".$view_by." >= '".$start_date."' and ".db_prefix()."tasks.".$view_by." <= '".$end_date."' ";
					}
					else{
						$customs   = $this->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and CONVERT(cv.value,date)  >='".$start_date."' and CONVERT(cv.value,date) <='".$end_date."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
						$cur_projects = '';
						if(!empty($customs)){
							foreach($customs as $custom1){
								$cur_projects .= $custom1['relid'].',';
							}	
							$cur_projects = rtrim($cur_projects,",");
							$qry_cond   = " ".db_prefix()."tasks.id in(".$cur_projects.")";
						}
						else{
							$qry_cond   = " ".db_prefix()."tasks.id=''";
						}
					}
					$cur_row    = 'Q'.($i+1).' '.$cur_year;
					$sum_data[$i]	= date_summary($qry_cond,$cur_row,$data['sel_measure'],$view_by,$filters);
					$j = $j+3;
					$tot_avg = $tot_avg + $sum_data[$i]['avg_task'];
					$upcoming	=	$upcoming + $sum_data[$i]['upcoming'];
					$overdue	=	$overdue + $sum_data[$i]['overdue'];
					$today		=	$today + $sum_data[$i]['today'];
					$in_progress=	$in_progress + $sum_data[$i]['in_progress'];
					$completed	=	$completed + $sum_data[$i]['completed'];
					$tot_cnt=	$tot_cnt + $sum_data[$i]['total_val_task'];
					$tot_val=	$tot_val + $sum_data[$i]['total_val_task'];
				}
				$sum_data[$i] = task_avg($upcoming,$overdue,$today,$in_progress,$completed,$tot_val,$tot_val,$view_by,$i,$tot_avg);
				$i++;
				$sum_data[$i] = task_total($upcoming,$overdue,$today,$in_progress,$completed,$tot_cnt,$tot_val,$view_by,$tot_avg);
			}
			if($data['view_type'] == 'date' && ($data['date_range1'] == 'Yearly')){	
				$i = 0;
				if(check_task_date($view_by)){
					$qry_cond   = " YEAR(".db_prefix()."tasks.".$view_by.") = '".$cur_year."'";
				}
				else{
					$customs   = $this->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and year(CONVERT(cv.value,date)) <='".$cur_year."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
						$cur_projects = '';
						if(!empty($customs)){
							foreach($customs as $custom1){
								$cur_projects .= $custom1['relid'].',';
							}	
							$cur_projects = rtrim($cur_projects,",");
							$qry_cond   = " ".db_prefix()."tasks.id in(".$cur_projects.")";
						}
						else{
							$qry_cond   = " id=''";
						}
				}
				$sum_data[$i]	= date_summary($qry_cond,$cur_year,$data['sel_measure'],$view_by,$filters);
				$upcoming	=	$upcoming + $sum_data[$i]['upcoming'];
				$overdue	=	$overdue + $sum_data[$i]['overdue'];
				$today		=	$today + $sum_data[$i]['today'];
				$in_progress=	$in_progress + $sum_data[$i]['in_progress'];
				$completed	=	$completed + $sum_data[$i]['completed'];
				$tot_val	=	$tot_val + $sum_data[$i]['total_val_task'];
				$tot_avg	=   $tot_avg + $sum_data[$i]['avg_task'];				
				$i++;
				$sum_data[$i] = task_avg($upcoming,$overdue,$today,$in_progress,$completed,$tot_val,$tot_val,$view_by,1,$tot_avg);
				$i++;
				$sum_data[$i] = task_total($upcoming,$overdue,$today,$in_progress,$completed,$tot_val,$tot_val,$view_by,$tot_avg );
			}
		}
		$data['summary_cls'] = $sum_data;
		if(isset($sum_data[0]['rows'])){
			$data['rows'] = array_column($sum_data, 'rows');
		}
		return $data;
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
		$data['title'] = _l('add_report');
		$deal_val = task_values();
		$data =  json_decode($deal_val, true);
		$needed = get_tasks_need_fields();
		$data['id'] = $id;
		$data['filters']	=	$filters = $this->session->userdata('activity_filters_edit_'.$id);
		$data['filters1']	=	$this->session->userdata('activity_filters1_edit_'.$id);
		$data['filters2']	=	$this->session->userdata('activity_filters2_edit_'.$id);
		$data['filters3']	=	$this->session->userdata('activity_filters3_edit_'.$id);
		$data['filters4']	=	$this->session->userdata('activity_filters4_edit_'.$id);
		
		$data['folders'] = get_report_folder('activity');
		$data['teamleaders'] = $this->staff_model->get('', [ 'active' => 1]);
		$data['links'] = $this->db->query("SELECT report_id,link_name,link_name FROM " . db_prefix() . "report_public WHERE report_id = '".$id."' ")->result_array();
		$reports1 = $this->db->query("SELECT report_name,report_type,folder_id FROM " . db_prefix() . "report WHERE id = '".$id."' ")->row();
		if (($key = array_search('id', $needed['need_fields'])) !== false) {
			unset($needed['need_fields'][$key]);
		}
		if(empty($reports1)){
			redirect(admin_url());exit;
		}
		$data['report_name']		=	$reports1->report_name.'('.$reports1->report_type.')';
		$data['folder_id']			=	$reports1->folder_id;
		$data['need_fields']		=	$needed['need_fields'];
		$data['need_fields_label']	=	$needed['need_fields_label'];
		$data['need_fields_edit']	=	$needed['need_fields_edit'];
		$data['mandatory_fields1']	=	$needed['mandatory_fields1'];
		$data['report_page'] = 'activity';
		$data['report_filter'] =  $this->load->view('admin/reports/filter', $data,true);
		$data['report_footer'] =  $this->load->view('admin/reports/report_footer', $data,true);
		$shares = $this->db->query("SELECT share_type,id FROM " . db_prefix() ."shared where  report_id = '".$id."'")->result_array();
		$data['share_types'] = $data['share_persons'] = array();
		$share_id = '';
		if(!empty($shares)){
			$i = 0;
			foreach($shares as $share12){
				$data['share_types'][$i] = $share12['share_type'];
				$share_id = $share12['id'];
				$i++;
			}
		}
		$share_persons = $this->db->query("SELECT staff_id FROM " . db_prefix() ."shared_staff where  share_id = '".$share_id."'")->result_array();
		if(!empty($share_persons)){
			$i = 0;
			foreach($share_persons as $share_person12){
				$data['share_persons'][$i] = $share_person12['staff_id'];
				$i++;
			}
		}
		$data['summary'] = $this->performance_summary($data);
		$data['report_page'] = 'activity';
        $this->load->view('admin/reports/deals_views', $data);
	}
	public function save_2_filter(){
		$cur_id =  '';
		if(!empty($_REQUEST['cur_id12'])){
			$cur_id = '_edit_'.$_REQUEST['cur_id12'];
		}
		$filters	=	$this->session->userdata('activity_filters2'.$cur_id);
		$filters1	=	$this->session->userdata('activity_filters1'.$cur_id);
		$filter_data = array();
		if(!empty($filters)){
			$i = 0;
			foreach($filters as $filter_val){
				$filter_data['activity_filters2'.$cur_id][$i]  =	$filter_val;
				$i++;
			}
		}
		if(!empty($filters1)){
			$i = 0;
			foreach($filters1 as $filter_val1){
				$filter_data['activity_filters1'.$cur_id][$i]  =	$filter_val1;
				$i++;
			}
		}
		if(!empty($_REQUEST['req_val'])){
			if(is_array($_REQUEST['req_val'])){
				$req_val = implode(",",$_REQUEST['req_val']);
			}
			else{
				$req_val = $_REQUEST['req_val'];
			}
			$filter_data['activity_filters2'.$cur_id][$_REQUEST['num_val']]	=	$req_val;
		}
		$this->session->set_userdata($filter_data);
	}
	public function save_filter_report(){
		$cur_id =  '';
		if(!empty($_REQUEST['cur_id121'])){
			$cur_id = '_edit_'.$_REQUEST['cur_id121'];
		}
		$filter_data = array();
		$filters	=	$this->session->userdata('activity_filters'.$cur_id);
		$filters1	=	$this->session->userdata('activity_filters1'.$cur_id);
		if(!empty($filters1)){
			$i = 0;
			foreach($filters1 as $filter_val1){
				$filter_data['activity_filters1'.$cur_id][$i]  =	$filter_val1;
				$i++;
			}
		}
		if(!empty($filters)){
			$i = $j = 0;
			foreach($filters as $filter12){
				if(!empty($_REQUEST['filter_'.$filter12])){
					$req_val = implode(",",$_REQUEST['filter_'.$filter12]);
					$filter_data['activity_filters2'.$cur_id][$i]	=	$req_val;
					if(empty($filter_data['activity_filters1'.$cur_id][$i])){
						$filter_data['activity_filters1'.$cur_id][$i]	=	'is';
					}
					
					$check_cond = filter_cond1($req_val);
					if(!$check_cond ){
						$filter_data['activity_filters3'.$cur_id][$i]	=	$_REQUEST['filter_4'][$j];  
						$filter_data['activity_filters4'.$cur_id][$i]	=	$_REQUEST['filter_5'][$j]; 
						$j++;
					}
					
					$i++;
				}
			}
		}
		$this->session->set_userdata($filter_data);
		set_alert('success', _l('filters_applied_successfully', _l('report')));
		$filter_tab = '';
		if($_REQUEST['filter_tab'] == 2){
			$filter_tab = '?filter_tab='.$_REQUEST['filter_tab'];
		}
		if(!empty($_REQUEST['cur_id121'])){
			redirect(admin_url('activity_reports/edit/'.$_REQUEST['cur_id121'].$filter_tab));
		}
		else{
			redirect(admin_url('activity_reports/add'.$filter_tab));
		}
	}
	
	public function set_first_filters($cur_val,$cur_num1){
		$cur_num = $cur_num1-1;
		$cur_id12 = '';
		if(!empty($_REQUEST['cur_id12'])){
			$cur_id12 = '_edit_'.$_REQUEST['cur_id12'];
		}
		$filters1	=	$this->session->userdata('activity_filters1'.$cur_id12);
		if(!empty($filters1)){
			foreach($filters1 as $key12 => $filter1){
				$filter_data['activity_filters1'.$cur_id12][$key12]	=	$filter1;  
			}
		}
		$req_out = '';
		$filter_data['activity_filters1'.$cur_id12][$cur_num]	=	$cur_val;
		$this->session->set_userdata($filter_data);
		return true;
	}
	public function set_second_filters($cur_val='',$cur_num1=''){
		$cur_val = $_REQUEST['cur_val'];
		if(is_array($cur_val)){
			$cur_val = implode(',', $cur_val);
		}
		$cur_num  =  $_REQUEST['req_val']-1;
		$cur_id12 = '';
		if(!empty($_REQUEST['cur_id12'])){
			$cur_id12 = '_edit_'.$_REQUEST['cur_id12'];
		}
		if(empty($_REQUEST['cur_id12'])){
			$filters2	=	$this->session->userdata('activity_filters2');
			$filters3	=	$this->session->userdata('activity_filters3');
		}
		else{
			$filters2	=	$this->session->userdata('activity_filters2'.$cur_id12);
			$filters3	=	$this->session->userdata('activity_filters3'.$cur_id12);
		}
		if(!empty($filters2)){
			foreach($filters2 as $key12 => $filter2){
				$filter_data['activity_filters2'.$cur_id12][$key12]	=	$filter2;  
			}
		}
		$filter_data['activity_filters2'.$cur_id12][$cur_num]	=	$cur_val;
		if($cur_val=='last_year'){
			$last_year = date('Y')-1;
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data['activity_filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data['activity_filters3'.$cur_id12][$cur_num]	=	'01-01-'.$last_year;  
			}
			$filters4	=	$this->session->userdata('activity_filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data['activity_filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data['activity_filters4'.$cur_id12][$cur_num]	=	'31-12-'.$last_year;
			}
		}
		if($cur_val=='next_year'){
			$next_year = date('Y')+1;
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data['activity_filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data['activity_filters3'.$cur_id12][$cur_num]	=	'01-01-'.$next_year;  
			}
			$filters4	=	$this->session->userdata('activity_filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data['activity_filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data['activity_filters4'.$cur_id12][$cur_num]	=	'31-12-'.$next_year;
			}
		}
		if($cur_val=='this_year'){
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data['activity_filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data['activity_filters3'.$cur_id12][$cur_num]	=	'01-01-'.date('Y');  
			}
			$filters4	=	$this->session->userdata('activity_filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data['activity_filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data['activity_filters4'.$cur_id12][$cur_num]	=	'31-12-'.date('Y');
			}
		}
		 elseif ($cur_val == 'today') {
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data['activity_filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data['activity_filters3'.$cur_id12][$cur_num]	=	date('d-m-Y');  
			}
			$filters4	=	$this->session->userdata('activity_filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data['activity_filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data['activity_filters4'.$cur_id12][$cur_num]	=	date('d-m-Y');
			}
		} elseif ($cur_val == 'yesterday') {
			$yesterday = date('d-m-Y',strtotime("-1 days"));
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data['activity_filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data['activity_filters3'.$cur_id12][$cur_num]	=	$yesterday;  
			}
			$filters4	=	$this->session->userdata('activity_filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data['activity_filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data['activity_filters4'.$cur_id12][$cur_num]	=	$yesterday;
			}
		} 
		elseif ($cur_val == 'tomorrow') {
			$tomorrow = date('d-m-Y',strtotime("+1 days"));
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data['activity_filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data['activity_filters3'.$cur_id12][$cur_num]	=	$tomorrow;  
			}
			$filters4	=	$this->session->userdata('activity_filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data['activity_filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data['activity_filters4'.$cur_id12][$cur_num]	=	$tomorrow;
			}
		} 
		elseif ($cur_val == 'this_week') {
			$str_date = date('d-m-Y',strtotime('monday this week'));
			$end_date = date('d-m-Y',strtotime('sunday this week'));
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data['activity_filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data['activity_filters3'.$cur_id12][$cur_num]	=	$str_date;  
			}
			$filters4	=	$this->session->userdata('activity_filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data['activity_filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data['activity_filters4'.$cur_id12][$cur_num]	=	$end_date;
			}
		} elseif ($cur_val == 'last_week') {
			$str_date = date('d-m-Y',strtotime('monday this week',strtotime("-1 week +1 day")));
			$end_date = date('d-m-Y',strtotime('sunday this week',strtotime("-1 week +1 day")));
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data['activity_filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data['activity_filters3'.$cur_id12][$cur_num]	=	$str_date;  
			}
			$filters4	=	$this->session->userdata('activity_filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data['activity_filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data['activity_filters4'.$cur_id12][$cur_num]	=	$end_date;
			}
		} 
		elseif ($cur_val == 'next_week') {
			$str_date = date('d-m-Y',strtotime('monday this week',strtotime("+1 week +1 day")));
			$end_date = date('d-m-Y',strtotime('sunday this week',strtotime("+1 week +1 day")));
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data['activity_filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data['activity_filters3'.$cur_id12][$cur_num]	=	$str_date;  
			}
			$filters4	=	$this->session->userdata('activity_filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data['activity_filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data['activity_filters4'.$cur_id12][$cur_num]	=	$end_date;
			}
		} 
		elseif ($cur_val == 'this_month') {
			$str_date = date('01-m-Y');
			$end_date = date('t-m-Y');
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data['activity_filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data['activity_filters3'.$cur_id12][$cur_num]	=	$str_date;  
			}
			$filters4	=	$this->session->userdata('activity_filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data['activity_filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data['activity_filters4'.$cur_id12][$cur_num]	=	$end_date;
			}
		}
		elseif ($cur_val == 'last_month') {
			$str_date = date('01-m-Y',strtotime('last month'));
			$end_date = date('t-m-Y',strtotime('last month'));
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data['activity_filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data['activity_filters3'.$cur_id12][$cur_num]	=	$str_date;  
			}
			$filters4	=	$this->session->userdata('activity_filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data['activity_filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data['activity_filters4'.$cur_id12][$cur_num]	=	$end_date;
			}
		}
		elseif ($cur_val == 'next_month') {
			$date = date('01-m-Y');
			$str_date = date("01-m-Y", strtotime ( '+1 month' , strtotime ( $date ) )) ;
			$end_date = date("31-m-Y", strtotime ( '+1 month' , strtotime ( $date ) )) ;
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data['activity_filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data['activity_filters3'.$cur_id12][$cur_num]	=	$str_date;  
			}
			$filters4	=	$this->session->userdata('activity_filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data['activity_filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data['activity_filters4'.$cur_id12][$cur_num]	=	$end_date;
			}
		}		
		$this->session->set_userdata($filter_data);
		return true;
	}
	public function set_3_filters($cur_val='',$cur_num1=''){
		$cur_val = $_REQUEST['cur_val'];
		if(is_array($cur_val)){
			$cur_val = implode(',', $cur_val);
		}
		$cur_num  =  $_REQUEST['req_val']-1;
		$cur_id12 = '';
		if(!empty($_REQUEST['cur_id12'])){
			$cur_id12 = '_edit_'.$_REQUEST['cur_id12'];
		}
		if(empty($_REQUEST['cur_id12'])){
			$filters2	=	$this->session->userdata('filters2');
			$filters3	=	$this->session->userdata('filters3');
		}
		else{
			$filters2	=	$this->session->userdata('filters2'.$cur_id12);
			$filters3	=	$this->session->userdata('filters3'.$cur_id12);
		}
		if(!empty($filters2)){
			foreach($filters2 as $key12 => $filter2){
				$filter_data['filters2'.$cur_id12][$key12]	=	$filter2;  
			}
		}
		$filter_data['filters2'.$cur_id12][$cur_num]	=	'custom_period';
		if(!empty($filters3)){
			foreach($filters3 as $key12 => $filter3){
				$filter_data['filters3'.$cur_id12][$key12]	=	$filters3;  
			}
			$filter_data['filters3'.$cur_id12][$cur_num]	=	$cur_val;  
		}
		$this->session->set_userdata($filter_data);
		return true;
	}
	public function set_4_filters($cur_val='',$cur_num1=''){
		$cur_val = $_REQUEST['cur_val'];
		if(is_array($cur_val)){
			$cur_val = implode(',', $cur_val);
		}
		$cur_num  =  $_REQUEST['req_val']-1;
		$cur_id12 = '';
		if(!empty($_REQUEST['cur_id12'])){
			$cur_id12 = '_edit_'.$_REQUEST['cur_id12'];
		}
		if(empty($_REQUEST['cur_id12'])){
			$filters2	=	$this->session->userdata('filters2');
			$filters4	=	$this->session->userdata('filters4');
		}
		else{
			$filters2	=	$this->session->userdata('filters2'.$cur_id12);
			$filters4	=	$this->session->userdata('filters4'.$cur_id12);
		}
		if(!empty($filters2)){
			foreach($filters2 as $key12 => $filter2){
				$filter_data['filters2'.$cur_id12][$key12]	=	$filter2;  
			}
		}
		$filter_data['filters2'.$cur_id12][$cur_num]	=	'custom_period';
		if(!empty($filters4)){
			foreach($filters4 as $key12 => $filter3){
				$filter_data['filters4'.$cur_id12][$key12]	=	$filters3;  
			}
			$filter_data['filters4'.$cur_id12][$cur_num]	=	$cur_val;  
		}
		$this->session->set_userdata($filter_data);
		return true;
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
				$filter_data['activity_filters2'.$cur_id12][$cur_num1] = '';
			case 'tags':
				$filter_data['activity_filters1'.$cur_id12][$cur_num1]	=	'is'; 
				break;
			case 'product_qty':
			case 'product_amt':
			case 'project_cost':
				$filter_data['activity_filters1'.$cur_id12][$cur_num1]	=	'is_more_than';  
				break;
			default:
				$fields =  $this->db->query("SELECT type FROM " . db_prefix() . "customfields where slug = '".$cur_val."' ")->row();
				if($fields->type == 'date_picker'){
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
	public function del_filter(){
		$cur_id12 = '';
		if(!empty($_REQUEST['cur_id12'])){
			$cur_id12 = '_edit_'.$_REQUEST['cur_id12'];
		}
		$filter_data = array();
		$filters	=	$this->session->userdata('activity_filters'.$cur_id12);
		$cur_num1 = $_REQUEST['req_val'];
		if(!empty($filters)){
			$i = 1;
			$i1 = 0;
			foreach($filters as $key12 => $filter12){
				if($i!=$cur_num1){
					$filter_data['activity_filters'.$cur_id12][$i1]	=	$filter12;  
					$i1++;
				}
				$i++;
			}
		}
		$filters1	=	$this->session->userdata('activity_filters1'.$cur_id12);
		if(!empty($filters1)){
			$i = 1;$i1 = 0;
			foreach($filters1 as $key12 => $filter1){
				if($i!=$cur_num1){
					$filter_data['activity_filters1'.$cur_id12][$i1]	=	$filter1; 
					$i1++;
				}
				$i++;
			}
		}
		$filters2	=	$this->session->userdata('activity_filters2'.$cur_id12);
		$filters3	=	$this->session->userdata('activity_filters3'.$cur_id12);
		$filters4	=	$this->session->userdata('activity_filters4'.$cur_id12);
		if(!empty($filters2)){
			$i = 1;$i1 = 0;
			foreach($filters2 as $key12 => $filter1){
				if($i!=$cur_num1){
					$filter_data['activity_filters2'.$cur_id12][$i1]	=	$filter1; 
					$i1++;
				}
				$i++;
			}
		}
		if(!empty($filters3)){
			$i = 1;$i1 = 0;
			foreach($filters3 as $key12 => $filter1){
				if($i!=$cur_num1){
					$filter_data['activity_filters3'.$cur_id12][$i1]	=	$filter1;  
					$i1++;
				}
				$i++;
			}
		}
		if(!empty($filters4)){
			$i = 1;$i1 = 0;
			foreach($filters4 as $key12 => $filter1){
				if($i!=$cur_num1){
					$filter_data['activity_filters4'.$cur_id12][$i1]	=	$filter1;  
					$i1++;
				} 
				$i++;
			}
		}	
		if(empty($filter_data)){
			$filter_data['activity_filters'.$cur_id12]  = array();
			$filter_data['activity_filters1'.$cur_id12] = array();
			$filter_data['activity_filters2'.$cur_id12] = array();
			$filter_data['activity_filters3'.$cur_id12] = array();
			$filter_data['activity_filters4'.$cur_id12] = array();
		}
		$this->session->set_userdata($filter_data);
		return true;
	}
	public function add_filter(){
		$cur_id12 = '';
		if(!empty($_REQUEST['cur_id12'])){
			$cur_id12 = '_edit_'.$_REQUEST['cur_id12'];
		}
		$filters	=	$this->session->userdata('activity_filters'.$cur_id12);
		$cur_num1	=	$_REQUEST['cur_num'];
		$cur_num	=	$_REQUEST['cur_num'] +1;
		$deal_val = task_values();
		$data =  json_decode($deal_val, true);
		unset($data['all_clmns']['id']);
		unset($data['all_clmns']['task_name']);
		unset($data['all_clmns']['description']);
		$all_clmns = $data['all_clmns'];
		
		$cus_flds = $data['cus_flds'];
		if(!empty($filters)){
			foreach($filters as $key12 => $filter1){
				if(!empty($all_clmns[$filter1])){
					unset($all_clmns[$filter1]);
				}if(!empty($cus_flds[$filter1])){
					unset($cus_flds[$filter1]);
				}
				$filter_data['activity_filters'.$cur_id12][$key12]	=	$filter1;
				if($filter1 == 'startdate' || $filter1 == 'dateadded' || $filter1 == 'datemodified' || $filter1 == 'datefinished'){
					$filter_data['activity_filters1'.$cur_id12][$key12]	=	'is';
					$filter_data['activity_filters2'.$cur_id12][$key12]	=	'this_year';
					$filter_data['activity_filters3'.$cur_id12][$key12]	=	'01-01-'.date('Y');
					$filter_data['activity_filters4'.$cur_id12][$key12]	=	'31-12-'.date('Y');
				}
			}
		}
		if(!empty($all_clmns)){
			foreach($all_clmns as $key => $all_clmn1){
				$filter_data['activity_filters'.$cur_id12][$cur_num1]	=	$key;  
				break;
			}
		}
		else if(!empty($cus_flds)){
			foreach($cus_flds as $key => $cus_fld1){
				$filter_data['activity_filters'.$cur_id12][$cur_num1]	=	$key;  
				break;
			}
		}
		$this->session->set_userdata($filter_data);
		$all_clmns = $data['all_clmns'];
		$cus_flds = $data['cus_flds'];
		$filters	=	$this->session->userdata('activity_filters'.$cur_id12);

		$req_out = '';
		$needed = get_tasks_need_fields();
		$need_fields		=	$needed['need_fields'];
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
		$check_val1 = $cur_val;
		$check_val2 = $req_val;
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
					if($fields->type == 'date_picker'){
						$req_out = $this->get_req_val($req_val,'date','','','','','task');
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
					break;
		}
		if(!empty($check_val1) && !empty($check_val2)){
			return $req_out;
		}else{
			echo $req_out;
		}
	}
}