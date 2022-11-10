<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model');
		$this->load->helper('report_summary');
		$this->load->helper('reports');
    }

    /* This is admin dashboard view */
    public function index()
    {
        //pre($_REQUEST);
        close_setup_menu();
        $this->load->model('departments_model');
        $this->load->model('todo_model');
        $data['departments'] = $this->departments_model->get();

        // $data['todos'] = $this->todo_model->get_todo_items(0);
        // // Only show last 5 finished todo items
        // $this->todo_model->setTodosLimit(5);
        // $data['todos_finished']            = $this->todo_model->get_todo_items(1);
        // $data['upcoming_events_next_week'] = $this->dashboard_model->get_upcoming_events_next_week();
        // $data['upcoming_events']           = $this->dashboard_model->get_upcoming_events();
        // $data['title']                     = _l('dashboard_string');
        // $this->load->model('currencies_model');
        // $data['currencies']    = $this->currencies_model->get();
        // $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['activity_log']  = $this->misc_model->get_activity_log();
        // Tickets charts
        $tickets_awaiting_reply_by_status     = $this->dashboard_model->tickets_awaiting_reply_by_status();
        $tickets_awaiting_reply_by_department = $this->dashboard_model->tickets_awaiting_reply_by_department();

        $data['tickets_reply_by_status']              = json_encode($tickets_awaiting_reply_by_status);
        $data['tickets_awaiting_reply_by_department'] = json_encode($tickets_awaiting_reply_by_department);

        $data['tickets_reply_by_status_no_json']              = $tickets_awaiting_reply_by_status;
        $data['tickets_awaiting_reply_by_department_no_json'] = $tickets_awaiting_reply_by_department;

        $data['projects_status_stats'] = json_encode($this->dashboard_model->projects_status_stats());
        $data['leads_status_stats']    = json_encode($this->dashboard_model->leads_status_stats());
        $data['google_ids_calendars']  = $this->misc_model->get_google_calendar_ids();
        $data['bodyclass']             = 'dashboard invoices-total-manual';
        $this->load->model('announcements_model');
        $data['staff_announcements']             = $this->announcements_model->get();
        $data['total_undismissed_announcements'] = $this->announcements_model->get_total_undismissed_announcements();

        $this->load->model('projects_model');
        $data['projects_activity'] = $this->projects_model->get_activity('', hooks()->apply_filters('projects_activity_dashboard_limit', 20));
        add_calendar_assets();
        $this->load->model('utilities_model');
        $this->load->model('estimates_model');
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();

        $this->load->model('proposals_model');
        $data['proposal_statuses'] = $this->proposals_model->get_statuses();

        $wps_currency = 'undefined';
        if (is_using_multiple_currencies()) {
            $wps_currency = $data['base_currency']->id;
        }
        $data['weekly_payment_stats'] = json_encode($this->dashboard_model->get_weekly_payments_statistics($wps_currency));

        $data['dashboard'] = true;

        $data['user_dashboard_visibility'] = get_staff_meta(get_staff_user_id(), 'dashboard_widgets_visibility');

        if (!$data['user_dashboard_visibility']) {
            $data['user_dashboard_visibility'] = [];
        } else {
            $data['user_dashboard_visibility'] = unserialize($data['user_dashboard_visibility']);
        }
        $data['user_dashboard_visibility'] = json_encode($data['user_dashboard_visibility']);

		$this->load->model('pipeline_model');
		if(isset($_POST['apply_sumbit'])){
			$cur_data['dash_form_data'] = $_POST;
			$this->session->set_userdata($cur_data);
			redirect(admin_url());
			exit;
		}
		if(isset($_GET['clear'])){
			$this->session->unset_userdata('dash_form_data');
		}
		if(!empty($this->session->userdata('dash_form_data'))){
			$data['dash_form_data'] = $this->session->userdata('dash_form_data');
		}
        
        if(!is_admin(get_staff_user_id())) {
            
            $low_hie = '';
            $lowlevel = $this->staff_model->printHierarchyTree(get_staff_user_id(),$prefix = '');
            if(!empty($lowlevel)) {
                $low_hie = ' OR staffid IN ('.implode(',', $lowlevel).')';
            }
            $staffdetails =  $this->db->query('SELECT *, staffid as staff_id FROM ' . db_prefix() . 'staff WHERE staffid = "'.get_staff_user_id().'"'.$low_hie)->result_array();
            $data['project_members'] =  $staffdetails;
        } else {
            if(isset($_GET['pipelines']) && $_GET['pipelines'] != '')
                $data['project_members'] = $this->pipeline_model->getPipelineFilterTeammembers($_GET['pipelines']);
            else
                $data['project_members'] = $this->projects_model->get_distinct_projects_members();
        }
		$this->session->set_userdata(['dashboard_form_data' => $_POST]);
		$data['pipelines'] = $this->pipeline_model->getPipeline();
		$data['teammembers'] = $this->pipeline_model->getTeammembers();
		$data['statuses'] = $this->leads_model->get_status();
        $data = hooks()->apply_filters('before_dashboard_render', $data);
        $this->load->view('admin/dashboard/dashboard', $data);
    }

    /* Chart weekly payments statistics on home page / ajax */
    public function weekly_payments_statistics($currency)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->dashboard_model->get_weekly_payments_statistics($currency));
            die();
        }
    }
	public function report(){
		if (!has_permission('reports', '', 'view')) {
            access_denied('reports');
        }
		$data = array();
		$data['title']   =  _l('view_dashboard');
		$this->load->view('admin/dashboard/view_dashboard', $data);
	}
	public function view_dashboard(){
		if (!has_permission('reports', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data('view_dashboard');
	}
	public function edit_dashboard(){
		if ($this->input->is_ajax_request()) {
			$upd_dashboard = array();
			$upd_dashboard['dashboard_name'] = $_REQUEST['name'];
			$cond['id']			  = $_REQUEST['dashboard_id'];
			$result = $this->db->update(db_prefix() . 'dashboard_report', $upd_dashboard, $cond);
			echo json_encode([
				'success' => 'success'
			]);
		}
	}
	public function view($id){
		if(!is_admin(get_staff_user_id())) {
            
            $low_hie = '';
			$staff_id = get_staff_user_id();
            $lowlevel = $this->staff_model->printHierarchyTree($staff_id,$prefix = '');
            if(!empty($lowlevel)) {
                $low_hie = ' OR staffid IN ('.implode(',', $lowlevel).')';
            }
            $staffdetails =  $this->db->query('SELECT *, staffid as staff_id FROM ' . db_prefix() . 'staff WHERE staffid = "'.get_staff_user_id().'"'.$low_hie)->result_array();
            $all_members = $data['project_members'] =  $staffdetails;
        } else {
            if(isset($_GET['pipelines']) && $_GET['pipelines'] != '')
                $all_members = $data['project_members'] = $this->pipeline_model->getPipelineFilterTeammembers($_GET['pipelines']);
            else
                $all_members =  $data['project_members'] = $this->projects_model->get_distinct_projects_members();
        }
		if(isset($_POST['apply_filter'])){
			extract($_POST);
			$staff_id = get_staff_user_id();
			$cond = array('staff_id'=>$staff_id,'dashboard_id'=>$id);
			$this->db->where($cond);
			$this->db->delete(db_prefix() . 'dashboard_filter');
			
			$ins_data = array();
			$ins_data['staff_id']		=	$staff_id;
			$ins_data['dashboard_id']	=	$id;
			if(!empty($filter_1)){
				$ins_data['period']		=	$filter_1;
				if(!empty($filter_2))
					$ins_data['date1']	=	date('Y-m-d',strtotime($filter_2));
				if(!empty($filter_3))
					$ins_data['date2']	=	date('Y-m-d',strtotime($filter_3));
			}
			else{
				$ins_data['period']		=	'';
				$ins_data['date1']		=	'';
				$ins_data['date2']		=	'';
			}
			if(!empty($filter_4))
				$ins_data['member']		=	$filter_4;
			else
				$ins_data['member']		=	'';
			$this->db->insert(db_prefix() . 'dashboard_filter', $ins_data);
			redirect(admin_url('dashboard/view/'.$id));
			exit;
		}
		$all_staff_id = array_column($data['project_members'],'staff_id');
		$staff_ids = implode(',',$all_staff_id);
		$all_reports =  $this->db->query('SELECT d.id,d.staff_id,d.report_id,d.type,d.tab_1,d.tab_2,d.sort1,d.sort2,r.view_by,r.view_type,r.measure_by,r.report_name,r.date_range,r.report_type FROM '. db_prefix().'dashboard d,'. db_prefix().'report r  WHERE d.staff_id in ('.$staff_ids.') and r.id = d.report_id and d.dashboard_id = "'.$id.'" group by d.report_id order by d.sort1,d.sort2 asc')->result_array();
		if(empty($all_reports)){
			redirect(admin_url('dashboard/report'));
			exit;
		}
		$staff_id = get_staff_user_id();
		$data = get_dashboard_report($all_reports,$staff_id);
		$data['id'] = $id;
		$data['public'] = '';
		$data['project_members'] = $all_members;
		$dashboard_report = $this->db->query("SELECT id,dashboard_name FROM " . db_prefix() . "dashboard_report WHERE id = '".$id."' ")->result_array();
		$data['title']  = $dashboard_report[0]['dashboard_name'];
		$this->load->view('admin/dashboard/report_dashboard', $data);
	}
	public function refresh_chart(){
		extract($_REQUEST);
		$all_reports =  $this->db->query('SELECT d.id,d.staff_id,d.report_id,d.type,d.tab_1,d.tab_2,d.sort1,d.sort2,r.view_by,r.view_type,r.measure_by,r.report_name,r.date_range,r.report_type FROM '. db_prefix().'dashboard d,'. db_prefix().'report r  WHERE d.id ="'.$dashboard_id.'" and r.id = d.report_id order by d.sort1,d.sort2 asc')->result_array();
		$i1 = 0;
		$staff_id = get_staff_user_id();
		$data = get_dashboard_report($all_reports,$staff_id);
		
		$cond = array('staff_id'=>$staff_id);
		$this->db->select('staff_id,period,date1,date2,member');
		$this->db->where($cond); 
		$this->db->from(db_prefix() . 'dashboard_filter');
		$query = $this->db->get();
		$data['dashoard_data'] = $query->result_array();
		$summaries = array();
		if(!empty($data['summary'])){
			foreach($data['summary'] as $sumarry1){
				$summaries[$i] = $sumarry1;
				$i1++;
			}
		}
		$data['summary'] = $data['summary'][0];
		$summary_view = $this->load->view('admin/reports/summary_view', $data,true);
		$i1 = 0;
		if(!empty($summaries)){
			foreach($summaries as $sum1){
				$req_label = $req_data = $req_color = array();
				$i = 0;
				if(!empty($sum1['rows'])){ 
					foreach($sum1['rows'] as $sum_row){
						if($sum_row!='Average' && $sum_row!='Total'){
							
							if($sum1['view_by'] == 'priority'){
								if($sum1['summary_cls'][$i1]['priority'] == '1'){
									$req_label[] = _l('task_priority_low');
								}
								else if($sum1['summary_cls'][$i1]['priority'] == '2'){
									$req_label[] = _l('task_priority_medium');
								}
								else if($sum1['summary_cls'][$i1]['priority'] == '3'){
									$req_label[] = _l('task_priority_high');
								}
								else if($sum1['summary_cls'][$i1]['priority'] == '4'){
									$req_label[] = _l('task_priority_urgent');
								}
								else{
									$req_label[] = $sum_row;
								}
							}
							else if($sum1['view_by'] == 'project_status'){
								if($sum_row == '0'){
									$req_label[] =  _l('proposal_status_open');
								}
								else if($sum_row == '1'){
									$req_label[] =  _l('project-status-won');
								}
								else if($sum_row == '2'){
									$req_label[] = _l('project-status-loss');
								}
								else{
									$req_label[] = $sum_row;
								}
							}
							else if($data['types'][$i1] == 'activity' && $sum1['view_by'] == 'status'){
								if($sum_row == '1'){
									$req_label[] = _l('task_status_1');
								}
								else if($sum_row == '2'){
									$req_label[] =  _l('task_status_2');
								}
								else if($sum_row == '3'){
									$req_label[] =   _l('task_status_3');
								}
								else if($sum_row == '4'){
									$req_label[] =  _l('task_status_4');
								}
								else if($sum_row == '5'){
									$req_label[] =  _l('task_status_5');
								}
							}
							else{
								$req_label[] = $sum_row;
							}
							if($data['types'][$i1] == 'deal')
								$req_data[]= $sum1['summary_cls'][$i]['total_cnt_deal'];
							 else	
								$req_data[] = $sum1['summary_cls'][$i]['total_val_task'];
							if($data['types'][$i1] != 'activity' && $sum1['view_by'] == 'status'){
								$this->db->select('color');
								$this->db->where('name', $sum_row);
								$progress =  $this->db->get(db_prefix() . 'projects_status')->row();
								$req_color[]= $progress->color.',';
							}
							else{
								$req_color[] = random_color();
							}
						}
						$i++;
					}
					$req_x		= _l($sum1['view_by']);
					$req_y		= _l($sum1['sel_measure']);
				}
				$i1++;
			}
		}
		$req_out = array('labels'=>$req_label,'data'=>$req_data,'color'=>$req_color,'req_x'=>$req_x,'req_y'=>$req_y,'label'=>_l('summary'),'summary'=>$summary_view);
		echo json_encode($req_out);
	}
	public function get_filter(){
		extract($_POST);
		$start_date = $end_date = '';
		switch($cur_val){
			case 'this_year':
				$start_date	=	'01-01-'.date('Y'); 
				$end_date	=	'31-12-'.date('Y'); 
				break;
			case 'last_year':
				$last_year = date('Y')-1;
				$start_date	=	'01-01-'.$last_year; 
				$end_date	=	'31-12-'.$last_year; 
				break;
			case 'next_year':
				$next_year = date('Y')+1;
				$start_date	=	'01-01-'.$next_year; 
				$end_date	=	'31-12-'.$next_year; 
				break;
			case 'this_month':
				$start_date	=	'01-'.date('m').'-'.date('Y'); 
				$end_date	=	date('t').'-'.date('m').'-'.date('Y');
				break;
			case 'last_month':
				$start_date	=	date('01-m-Y',strtotime('last month')); 
				$end_date	=	date('t-m-Y',strtotime('last month'));
				break;
			case 'next_month':
				$date = date('01-m-Y');
				$start_date	=	date("01-m-Y",strtotime('+1 month',strtotime($date))); 
				$end_date	=	date("31-m-Y",strtotime('+1 month',strtotime($date)));
				break;
			case 'this_week':
				$start_date	=	date('d-m-Y',strtotime('monday this week')); 
				$end_date	=	date('d-m-Y',strtotime('sunday this week'));
				break;
			case 'last_week':
				$start_date	=	date('d-m-Y',strtotime('monday this week',strtotime("-1 week +1 day"))); 
				$end_date	=	date('d-m-Y',strtotime('sunday this week',strtotime("-1 week +1 day")));
				break;
			case 'next_week':
				$start_date	=	date('d-m-Y',strtotime('monday this week',strtotime("+1 week +1 day"))); 
				$end_date	=	date('d-m-Y',strtotime('sunday this week',strtotime("+1 week +1 day")));
				break;
			case 'today':
				$start_date	=	date('Y-m-d'); 
				$end_date	=	date('Y-m-d');
				break;
			case 'yesterday':
				$start_date	=	date('Y-m-d',strtotime("-1 days")); 
				$end_date	=	date('Y-m-d',strtotime("-1 days"));
				break;
			case 'tomorrow':
				$start_date	=	date('Y-m-d',strtotime("+1 days")); 
				$end_date	=	date('Y-m-d',strtotime("+1 days"));
				break;
		}
		$req_out = array('start_date'=>$start_date,'end_date'=>$end_date);
		echo json_encode($req_out,true);
	}
	public function report_widgets_order(){
		if(!empty($_POST)){
			foreach($_POST as $p_key=>$p_arrays){
				if(!empty($p_arrays)){
					foreach($p_arrays as $key=>$dashboard){
						if(!empty($dashboard)){
							$cond = array('id'=>$dashboard);
							$upd_data = array('sort1'=>$p_key,'sort2'=>$key);
							$this->db->update(db_prefix() . "dashboard", $upd_data, $cond);
						}
					}
				}
			}
		}
	}
	public function public_link(){
		$staff_id = $_REQUEST['req_val'];
		$d_id 	  = $_REQUEST['d_id'];
		$ins_public = array();
		$ins_public['staff_id']		=	$staff_id;
		$ins_public['dashboard_id']	=	$d_id;
		$this->db->insert(db_prefix() . 'dashboard_public', $ins_public);
		$public_id	=	$this->db->insert_id();
		$cond = array('id'=>$public_id);
		$upd_public['share_link'] = md5($public_id.$staff_id.'_sharelink');
		$this->db->update(db_prefix() . 'dashboard_public', $upd_public, $cond);
		$req_out = get_public_dashboard($staff_id,$d_id);
		echo $req_out;
	}
	public function load_public(){
		$staff_id = $_REQUEST['cur_id'];
		$dash_id  = $_REQUEST['dash_id'];
		$req_out  = get_public_dashboard($staff_id,$dash_id);
		echo $req_out;
	}
	public function check_publick(){
		$id = $_REQUEST['req_val'];
		$req_out = '';
		$links = $this->db->query("SELECT link_name FROM " . db_prefix() . "dashboard_public WHERE id = '".$id."' ")->row();
		echo $req_out = $links->link_name;
	}
	public function update_public_name(){
		extract($_REQUEST);
		$cond = array('id'=>$link_id);
		$upd_data = array('link_name'=>$ch_name12);
		$result = $this->db->update(db_prefix() . "dashboard_public", $upd_data, $cond);
		return true;
	}
	public function delete_link(){
		$staff_id =  get_staff_user_id();;
		$cur_id12  = $_REQUEST['req_val'];
		$cond = array('id'=>$cur_id12);
		$this->db->where($cond);
		$this->db->delete(db_prefix() . 'dashboard_public');
		$req_out = '';
		$req_out = get_public_dashboard($staff_id,$_REQUEST['dash_id']);
		echo $req_out;
		
	}
	public function delete_dashboard($dash_id){
		$cond = array('id'=>$dash_id);
		$this->db->where($cond);
		$this->db->delete(db_prefix() . 'dashboard');
		set_alert('success', _l('deleted_successfully', _l('report')));
		redirect(admin_url('dashboard/report'));
	}
}
