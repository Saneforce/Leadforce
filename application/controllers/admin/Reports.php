<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends AdminController
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

    /* See knowledge base article reports*/
    public function knowledge_base_articles()
    {
		if (!has_permission('reports', '', 'view')) {
            access_denied('reports');
        }
        $this->load->model('knowledge_base_model');
        $data['groups'] = $this->knowledge_base_model->get_kbg();
        $data['title']  = _l('kb_reports');
        $this->load->view('admin/reports/knowledge_base_articles', $data);
    }

    /*
        public function tax_summary(){
           $this->load->model('taxes_model');
           $this->load->model('payments_model');
           $this->load->model('invoices_model');
           $data['taxes'] = $this->db->query("SELECT DISTINCT taxname,taxrate FROM ".db_prefix()."item_tax WHERE rel_type='invoice'")->result_array();
            $this->load->view('admin/reports/tax_summary',$data);
        }*/
    /* Repoert leads conversions */
    public function leads()
    {
		if (!has_permission('reports', '', 'view')) {
            access_denied('reports');
        }
        $type = 'leads';
        if ($this->input->get('type')) {
            $type                       = $type . '_' . $this->input->get('type');
            $data['leads_staff_report'] = json_encode($this->reports_model->leads_staff_report());
        }
        $this->load->model('leads_model');
        $data['statuses']               = $this->leads_model->get_status();
        $data['leads_this_week_report'] = json_encode($this->reports_model->leads_this_week_report());
        $data['leads_sources_report']   = json_encode($this->reports_model->leads_sources_report());
        $this->load->view('admin/reports/' . $type, $data);
    }

    /* sales reportts */
    public function sales()
    {
		if (!has_permission('reports', '', 'view')) {
            access_denied('reports');
        }
        $data['mysqlVersion'] = $this->db->query('SELECT VERSION() as version')->row();
        $data['sqlMode']      = $this->db->query('SELECT @@sql_mode as mode')->row();

        if (is_using_multiple_currencies() || is_using_multiple_currencies(db_prefix() . 'creditnotes') || is_using_multiple_currencies(db_prefix() . 'estimates') || is_using_multiple_currencies(db_prefix() . 'proposals')) {
            $this->load->model('currencies_model');
            $data['currencies'] = $this->currencies_model->get();
        }
        $this->load->model('invoices_model');
        $this->load->model('estimates_model');
        $this->load->model('proposals_model');
        $this->load->model('credit_notes_model');

        $data['credit_notes_statuses'] = $this->credit_notes_model->get_statuses();
        $data['invoice_statuses']      = $this->invoices_model->get_statuses();
        $data['estimate_statuses']     = $this->estimates_model->get_statuses();
        $data['payments_years']        = $this->reports_model->get_distinct_payments_years();
        $data['estimates_sale_agents'] = $this->estimates_model->get_sale_agents();

        $data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();

        $data['proposals_sale_agents'] = $this->proposals_model->get_sale_agents();
        $data['proposals_statuses']    = $this->proposals_model->get_statuses();

        $data['invoice_taxes']     = $this->distinct_taxes('invoice');
        $data['estimate_taxes']    = $this->distinct_taxes('estimate');
        $data['proposal_taxes']    = $this->distinct_taxes('proposal');
        $data['credit_note_taxes'] = $this->distinct_taxes('credit_note');


        $data['title'] = _l('sales_reports');
        $this->load->view('admin/reports/sales', $data);
    }
    /* deals reportts */
    public function deals()
    {
		if (!has_permission('reports', '', 'view')) {
            access_denied('reports');
        }
        $data['mysqlVersion'] = $this->db->query('SELECT VERSION() as version')->row();
        $data['sqlMode']      = $this->db->query('SELECT @@sql_mode as mode')->row();

        $this->load->model('pipeline_model');
		$fields = get_option('deal_fields');
		$data['need_fields'] = array();
		if(!empty($fields) && $fields != 'null'){
			$data['need_fields'] = json_decode($fields);
		}
		if(!empty($data['need_fields']) && in_array("pipeline_id", $data['need_fields']) ){
			$data['pipelines'] = $this->pipeline_model->getPipeline();
		}else{
			$default_pipeline = get_option('default_pipeline');
			$data['pipelines'] = $this->pipeline_model->getpipelinebyIdarray($default_pipeline);
		}
        if(!is_admin(get_staff_user_id())) {
            $my_staffids = $this->staff_model->get_my_staffids();
            if($my_staffids) {
                $staffdetails =  $this->db->query('SELECT staffid as id, CONCAT(firstname," ",lastname) as name FROM ' . db_prefix() . 'staff WHERE staffid in (' . implode(',',$my_staffids) . ')')->result_array();
            } else {
                $staffdetails =  $this->db->query('SELECT staffid as id, CONCAT(firstname," ",lastname) as name FROM ' . db_prefix() . 'staff WHERE staffid in (' . implode(',',$my_staffids) . ')')->result_array();
            }
            $data['teammembers'] =  $staffdetails;
        } else {
            $data['teammembers'] = $this->pipeline_model->getTeammembers();
        }
 
        $data['title'] = _l('deals_reports');
        $this->load->view('admin/reports/deals', $data);
    }
	public function add_report(){
		if (!has_permission('reports', '', 'create')) {
            access_denied('reports');
        }
		extract($_REQUEST);
		$fields = deal_needed_fields();
		$needed = array();
		if(!empty($fields) && $fields != 'null'){
			$needed = json_decode($fields,true);
			$need_fields = $needed['need_fields'];
		}
		$type = '';
		$filter_data['filters'] = array();
		if($report_12_id == 'performance'){
			if (in_array('project_start_date', $need_fields)){
				$filter_data['filters'][0]	=	'project_start_date';  
				$filter_data['filters1'][0]	=	'is';  
				$filter_data['filters2'][0]	=	'this_year';  
				$filter_data['filters3'][0]	=	'01-01-'.date('Y');  
				$filter_data['filters4'][0]	=	'31-12-'.date('Y');  
				
				$filter_data['view_by']		=	'project_start_date';
				$filter_data['view_type']	=	'date';
				$filter_data['date_range1']	=	'Monthly';
				$filter_data['sel_measure']	=	_l('num_deals');
			}
			$type = 'deal';
		}
		else if($report_12_id == 'conversion'){
			$i = 0 ;
			if (in_array('project_start_date', $need_fields)){
				$filter_data['filters'][$i]		=	'project_start_date';  
				$filter_data['filters1'][$i]	=	'is';  
				$filter_data['filters2'][$i]	=	'this_year';  
				$filter_data['filters3'][$i]	=	'01-01-'.date('Y');  
				$filter_data['filters4'][$i]	=	'31-12-'.date('Y');
				$i++;
			}
			if (in_array('project_status', $need_fields)){
				$filter_data['filters'][$i]		=	'project_status';  
				$filter_data['filters1'][$i]	=	'is_any_of';  
				$filter_data['filters2'][$i]	=	'WON,LOSS';  
				$i++;
			}
			if (in_array('pipeline_id', $need_fields)){
				$pipelines = $this->pipeline_model->getPipeline();
				$filter_data['filters'][$i]	=	'pipeline_id';  
				$filter_data['filters1'][$i]	=	'is';  
				$filter_data['filters2'][$i]	=	$pipelines[0]['id']; 
			}
			$type = 'deal';
		}
		else if($report_12_id == 'duration'){
			$i = 0;
			if (in_array('project_start_date', $need_fields)){
				$filter_data['filters'][$i]		=	'project_start_date';  
				$filter_data['filters1'][$i]	=	'is';  
				$filter_data['filters2'][$i]	=	'this_year';  
				$filter_data['filters3'][$i]	=	'01-01-'.date('Y');  
				$filter_data['filters4'][$i]	=	'31-12-'.date('Y'); 
				$i++;
			}
			if (in_array('pipeline_id', $need_fields)){
				$pipelines = $this->pipeline_model->getPipeline();
				$filter_data['filters'][1]	=	'pipeline_id';  
				$filter_data['filters1'][1]	=	'is';  
				$filter_data['filters2'][1]	=	$pipelines[0]['id']; 
				$filter_data['filters'][2]	=	'project_status';  
				$filter_data['filters1'][2]	=	'is_any_of';  
				$filter_data['filters2'][2]	=	'WON,LOST'; 
			}
			$type = 'deal';
		}
		else if($report_12_id == 'progress'){
			if (in_array('project_start_date', $need_fields)){
				$filter_data['filters'][0]	=	'project_start_date';  
				$filter_data['filters1'][0]	=	'is';  
				$filter_data['filters2'][0]	=	'this_year';  
				$filter_data['filters3'][0]	=	'01-01-'.date('Y');  
				$filter_data['filters4'][0]	=	'31-12-'.date('Y'); 
			}
			$type = 'deal';
		}
		else if($report_12_id == 'activity_performance'){
			$filter_data['activity_filters'][0]		=	'dateadded';  
			$filter_data['activity_filters1'][0]	=	'is';  
			$filter_data['activity_filters2'][0]	=	'this_month';  
			$filter_data['activity_filters3'][0]	=	'01-'.date('m').'-'.date('Y');  
			$filter_data['activity_filters4'][0]	=	date('t').'-'.date('m').'-'.date('Y'); 
			$type = 'activity';
		}
		else if($report_12_id == 'email_performance'){
			$filter_data['activity_filters'][0]		=	'dateadded';  
			$filter_data['activity_filters1'][0]	=	'is';  
			$filter_data['activity_filters2'][0]	=	'this_month';  
			$filter_data['activity_filters3'][0]	=	'01-'.date('m').'-'.date('Y');
			$filter_data['activity_filters4'][0]	=	date('t').'-'.date('m').'-'.date('Y'); 
			$type = 'activity';
		}
		else if($report_12_id == 'call_performance'){
			$filter_data['activity_filters'][0]		=	'dateadded';  
			$filter_data['activity_filters1'][0]	=	'is';  
			$filter_data['activity_filters2'][0]	=	'this_month';  
			$filter_data['activity_filters3'][0]	=	'01-'.date('m').'-'.date('Y');
			$filter_data['activity_filters4'][0]	=	date('t').'-'.date('m').'-'.date('Y');  
			$type = 'activity';
		}
		$filter_data['report_type'] = _l($report_12_id);
		$summary_filter = set_activity_summary($type);
		$this->session->set_userdata($filter_data);
		$this->session->set_userdata($summary_filter);
		if($report_12_id == 'activity_performance' || $report_12_id == 'email_performance' || $report_12_id == 'call_performance'){
			redirect(admin_url('activity_reports/add'));
		}else{
			redirect(admin_url('reports/add'));
		}
	}
	public function summary(){
		if(isset($_POST['submit'])){
			$filter_data['view_by']		=	$_POST['view_by'];
			$filter_data['view_type']	=	$_POST['view_type'];
			$filter_data['date_range1']	=	$_POST['date_range1'];
			$filter_data['sel_measure']	=	$_POST['sel_measure'];
			$type = $_POST['summary_val'];
			$type = ($type=='deal')?'':'activity_';
			$this->session->set_userdata($filter_data);
			if(!empty($_POST['summary_edit'])){
				redirect(admin_url($type.'reports/edit/'.$_POST['summary_edit']));
				exit;
			}
		}
		redirect(admin_url($type.'reports/add'));
	}
	public function edit_deal_report($id,$shared_id = ''){
		$type = $_GET['type'];
		$type = ($type=='')?'deal':$type;
		if(empty($type) || ( $type!='deal' && $type!='activity')){
			redirect(admin_url());
		}
		$req_filter = ($type=='deal')?'':'activity_';
		$fields = "filter_1,filter_2,filter_3,filter_4,filter_5";
		$condition = array('report_id'=>$id);
		$this->db->select($fields);
		$this->db->from(db_prefix() . "report_filter");
		$this->db->where($condition); 
		$query = $this->db->get();
		$filters = $query->result_array();
		
		$fields = "report_type";
		$condition = array('id'=>$id);
		$this->db->select($fields);
		$this->db->from(db_prefix() . "report");
		$this->db->where($condition); 
		$query = $this->db->get();
		$reports = $query->result_array();
		$filter_data = array();
		$filter_data['report_type']	=	$reports[0]['report_type'];
		if(!empty($filters)){
			$i = 0;
			foreach($filters as $filter12){
				$filter_data[$req_filter.'filters_edit_'.$id][$i]	=	$filter12['filter_1'];
				$filter_data[$req_filter.'filters1_edit_'.$id][$i]	=	$filter12['filter_2'];
				$filter_data[$req_filter.'filters2_edit_'.$id][$i]	=	$filter12['filter_3'];
				$filter_data[$req_filter.'filters3_edit_'.$id][$i]	=	$filter12['filter_4'];
				$filter_data[$req_filter.'filters4_edit_'.$id][$i]	=	$filter12['filter_5'];
				$i++;
			}
		}
		$summary_filter = set_activity_summary($type);
		$this->session->set_userdata($filter_data);
		$this->session->set_userdata($summary_filter);
		if(empty($shared_id)){
			if($type == 'deal'){
				redirect(admin_url('reports/edit/'.$id));
			}
			else{
				redirect(admin_url('activity_reports/edit/'.$id));
			}
		}
		else{
			redirect(admin_url('reports/view_shared/'.$shared_id.'/'.$type));
		}
	}
	public function deal_table($clientid = '')
    {
		$cur_id =  '';
		if(!empty($clientid)){
			$cur_id = '_edit_'.$clientid;
		}
		$type = $_REQUEST['type'];
		$req_url = ($type == 'deal')?'':'activity_';
		$data['filters']	=	$this->session->userdata($req_url.'filters'.$cur_id);
		$data['filters1']	=	$this->session->userdata($req_url.'filters1'.$cur_id);
		$data['filters2']	=	$this->session->userdata($req_url.'filters2'.$cur_id);
		$data['filters3']	=	$this->session->userdata($req_url.'filters3'.$cur_id);
		$data['filters4']	=	$this->session->userdata($req_url.'filters4'.$cur_id);
		if($type == 'deal'){
			$fields = deal_needed_fields();
			$needed = json_decode($fields,true);
			if (($key = array_search('id', $needed['need_fields'])) !== false) {
				unset($needed['need_fields'][$key]);
			}
			if (($key = array_search('project_created', $needed['need_fields'])) !== false ) {
				unset($needed['need_fields'][$key]);
			}
			if (($key = array_search('product_count', $needed['need_fields'])) !== false ) {
				unset($needed['need_fields'][$key]);
			}
		}
		else{
			$needed = get_tasks_need_fields();
			$reports1 = $this->db->query("SELECT report_name,report_type,folder_id FROM " . db_prefix() . "report WHERE id = '".$clientid."' ")->row();
			$data['report_name'] = $reports1->report_type;
		}
		
		$data['need_fields']		=	$needed['need_fields'];
		$data['need_fields_label']	=	$needed['need_fields_label'];
		$data['need_fields_edit']	=	$needed['need_fields_edit'];
		$data['mandatory_fields1']	=	$needed['mandatory_fields1'];
		$data['clientid'] = $clientid;
		if($type == 'deal'){
			$this->app->get_table_data('report_deal', $data);
		}
		else{
			$this->app->get_table_data('report_activity', $data);
		}
    }
	public function check_view_by($type = ''){
		$type = ($type == '' || $type == 'deal')?'':'activity_'; 
		if ($this->input->is_ajax_request()) {
			$view_by	=	$_REQUEST['view_by'];
			if(check_activity_date($view_by)){
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
					$ch_custom	= $this->db->query("SELECT id  FROM " . db_prefix() . "customfields where slug = '".$view_by."' and (type = 'date_picker' or type = 'date_picker_time' or type = 'date_range')")->result_array();
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
		$deal_val = deal_values();
		$data =  json_decode($deal_val, true);
		$fields = deal_needed_fields();
		$needed = array();
		if(!empty($fields) && $fields != 'null'){
			$needed = json_decode($fields,true);
		}
		$data['filters']	=	$filters = $this->session->userdata('filters');
		$data['filters1']	=	$this->session->userdata('filters1');
		$data['filters2']	=	$this->session->userdata('filters2');
		$data['filters3']	=	$this->session->userdata('filters3');
		$data['filters4']	=	$this->session->userdata('filters4');
		$custom_fields = get_table_custom_fields('projects');
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
		$data['folders'] = get_report_folder('deal');
		$data['id'] = $data['links'] = '';
		if (($key = array_search('id', $needed['need_fields'])) !== false) {
			unset($needed['need_fields'][$key]);
		}
		if (($key = array_search('project_created', $needed['need_fields'])) !== false ) {
			unset($needed['need_fields'][$key]);
		}
		if (($key = array_search('product_count', $needed['need_fields'])) !== false ) {
			unset($needed['need_fields'][$key]);
		}
		$data['report_name']		=  	$this->session->userdata('report_type');
		$data['folder_id'] 			=   '';
		$data['need_fields']		=	$needed['need_fields'];
		$data['need_fields_label']	=	$needed['need_fields_label'];
		$data['need_fields_edit']	=	$needed['need_fields_edit'];
		$data['mandatory_fields1']	=	$needed['mandatory_fields1'];
		$data['report_page'] = 'deal';
		$data['report_filter'] =  $this->load->view('admin/reports/filter', $data,true);
		$data['report_footer'] =  $this->load->view('admin/reports/report_footer', $data,true);
		$data['teamleaders'] = $this->staff_model->get('', [ 'active' => 1]);
		$data['summary'] = $this->performance_summary($data);
        $this->load->view('admin/reports/deals_views', $data);
	}
	public function get_deal_summary($type = ''){
		if ($this->input->is_ajax_request()) {	
			$type = ($type == '' || $type == 'deal')?'':'activity_'; 
			if($type == ''){
				$deal_val = deal_values();
				$deals =  json_decode($deal_val, true);
				$fields = deal_needed_fields();
				$needed = json_decode($fields,true);
				if (($key = array_search('id', $needed['need_fields'])) !== false) {
					unset($needed['need_fields'][$key]); 
				}
				if (($key = array_search('project_created', $needed['need_fields'])) !== false ) {
					unset($needed['need_fields'][$key]);
				}
				if (($key = array_search('product_count', $needed['need_fields'])) !== false ) {
					unset($needed['need_fields'][$key]);
				}
			}
			else{
				$task_val = task_values();
				$tasks =  json_decode($task_val, true);
				$needed = get_tasks_need_fields();
				
			}
			$cur_id =  '';
			if(!empty($_REQUEST['edit_id'])){
				$cur_id = '_edit_'.$_REQUEST['edit_id'];
			}
			$deals['filters']	=	$this->session->userdata($type.'filters'.$cur_id);
			$deals['filters1']	=	$this->session->userdata($type.'filters1'.$cur_id);
			$deals['filters2']	=	$this->session->userdata($type.'filters2'.$cur_id);
			$deals['filters3']	=	$this->session->userdata($type.'filters3'.$cur_id);
			$deals['filters4']	=	$this->session->userdata($type.'filters4'.$cur_id);
			$data = $_REQUEST;
			
			$data['need_fields']=	$needed['need_fields'];
			if($type == ''){
				if($data['view_by']		==	'project_start_date'){
					$data['view_by']	=	'start_date';
				}
				if($data['view_by']		==	'project_deadline'){
					$data['view_by']	=	'deadline';
				}
				if($data['view_by']		==	'won_date' || $data['view_by']		==	'lost_date'){
					$data['view_by']	=	'stage_on';
				}
				$data['results']		=	get_qry($data['clmn'],$data['crow'],$data['view_by'],$data['measure'],$data['date_range'],$data['view_type'],$data['sum_id'],$deals);
			}
			else{
				$data['results']		=	get_task_qry($data['clmn'],$data['crow'],$data['view_by'],$data['measure'],$data['date_range'],$data['view_type'],$data['sum_id'],$deals);
			}
			if($data['date_range']	==	'Monthly' && $data['view_type'] == 'date'){
				$req_month =  (int) $data['crow'];
				$req_date  = '01-'.$req_month.'-'.date('Y');
				$data['cur_record']	=	date('M',strtotime($req_date)).' '.date('Y').', '.ucfirst($data['clmn']).' deals';
			}
			else{
				if($data['clmn'] == 'total_cnt_deal' || $data['clmn'] == 'total_cnt_task'){
					$data['cur_record']	=	$data['crow'].', '._l($data['clmn']);

				}else{
					$data['cur_record']	=	$data['crow'].', '._l($data['clmn']).' '._l('deals');
				}
			}
			$data['summary']=	$this->load->view('admin/reports/'.$type.'summary_table', $data,true);
			echo json_encode($data,true);
		}
	}
	public function performance_summary($filters){
		$cur_year  = date('Y');
		$data = array();
		$data['rows']			=	array();
		$data['view_by']		=	$view_by = $this->session->userdata('view_by');
		$data['view_type']		=	$this->session->userdata('view_type');
		$data['date_range1']	=	$this->session->userdata('date_range1');
		$data['sel_measure']	=	$this->session->userdata('sel_measure');
		if($view_by == 'project_start_date'){
			$view_by = 'start_date';
		}
		else if($view_by == 'project_deadline'){
			$view_by = 'deadline';
		}
		else if($view_by == 'won_date' || $view_by == 'lost_date'){
			$view_by = 'stage_on';
		}
		$data['columns']		=	array($view_by,'own','lost','open','avg_deal','total_val_deal','total_cnt_deal');
		if($data['sel_measure'] == 'Deal Value'){
			$data['columns']	=	array($view_by,'avg_deal','total_val_deal','total_cnt_deal');
		}
		if($data['sel_measure'] == 'Number of Products'){
			$data['columns']	=	array($view_by,'open','own','total_num_prdts');
		}
		if($data['sel_measure'] == 'Product Value'){
			$data['columns']	=	array($view_by,'open','own','avg_prdt_val','total_val_prdt');
		}
		if($view_by == 'project_status'){
			$data['columns']	=	array($view_by,'avg_deal','total_val_deal','total_cnt_deal');
		}
		$i1 = 0;
		if(!empty($data['columns'])){
			foreach($data['columns'] as $clmn1){
				$data['summary_cls'][$i1++]['vals'] = _l($clmn1);
				$i1++;
			}
		}
		if($data['view_type'] != 'date'){
			$fields = get_table_fields($view_by);
			$sum_data = summary_val($fields['tables'],$fields['fields'],$fields['qry_cond'],$data['sel_measure'],$view_by,$fields['cur_rows'],$filters,'deal');
		}
		else{
			$months = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
			$own = $open = $lost = $tot_cnt = $tot_prt = $tot_val = $avg_deal = $tot_avg = 0; 
			if($data['view_type'] == 'date' && ($data['date_range1'] == 'Monthly')){
				if(!empty($months)){
					$j = 1;$i = 0;
					foreach($months as $month1){
						if(check_activity_date($view_by)){
							$j = $i+1;
							$qry_cond   = " and MONTH(".$view_by.") = '".$j."' and YEAR(".$view_by.") = '".$cur_year."'";
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
								$qry_cond   = " and id in(".$cur_projects.")";
							}
							else{
								$qry_cond   = " and id=''";
							}
							$cur_row    = ($month1).' '.$cur_year;
							$sum_data[$i]	= date_summary($qry_cond,$cur_row,$data['sel_measure'],$view_by,$filters);
							
							$i++;
							$j++;
						}
						$tot_avg = $tot_avg + $sum_data[$i-1]['avg_deal'];
						$own	=	$own + $sum_data[$i-1]['own'];
						$open	=	$open + $sum_data[$i-1]['open'];
						$lost	=	$lost + $sum_data[$i-1]['lost'];
						$tot_cnt=	$tot_cnt + $sum_data[$i-1]['total_cnt_deal'];
						$tot_val=	$tot_val + $sum_data[$i-1]['total_val_deal'];
					}
					$sum_data[$i] = deal_avg($own,$open,$lost,$tot_cnt,$tot_val,$view_by,$i,$tot_avg);
					$i++;
					$sum_data[$i] = deal_total($own,$open,$lost,$tot_cnt,$tot_val,$view_by,$tot_avg);
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
								if(check_activity_date($view_by)){
									$qry_cond   .= " and ".$view_by." >= '".$start_date."' ";
								}
								else{
									$customs   = $this->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and CONVERT(cv.value,date)  >='".$start_date."' and CONVERT(cv.value,date) <='".$end_date."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
									$cur_projects = '';
									if(!empty($customs)){
										foreach($customs as $custom1){
											$cur_projects .= $custom1['relid'].',';
										}	
										$cur_projects = rtrim($cur_projects,",");
										$qry_cond   .= " and id in(".$cur_projects.")";
									}
									else{
										$qry_cond   .= " and id=''";
									}
								}
								$own	=	$own + $sum_data[$m-1]['own'];
								$open	=	$open + $sum_data[$m-1]['open'];
								$lost	=	$lost + $sum_data[$m-1]['lost'];
								$tot_cnt=	$tot_cnt + $sum_data[$m-1]['total_cnt_deal'];
								$k++;
								$req_end_days = $w_end_date - $req_month;
								$w_start_date	= 1;
								$w_end_date		= $req_end_days;
								
								$req_key = array_search ($key, $months);
								$start_date  = date('Y-m-d',strtotime($w_start_date.'-'.$months[$req_key+1].'-'.$cur_year));
								$end_date	 = date('Y-m-d',strtotime($req_end_days.'-'.$months[$req_key+1].'-'.$cur_year));
								if(check_activity_date($view_by)){
									$qry_cond 	 .= " and ".$view_by." <= '".$end_date."'";
								}else{
									$customs   = $this->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and CONVERT(cv.value,date)  >='".$start_date."' and CONVERT(cv.value,date) <='".$end_date."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
									$cur_projects = '';
									if(!empty($customs)){
										foreach($customs as $custom1){
											$cur_projects .= $custom1['relid'].',';
										}	
										$cur_projects = rtrim($cur_projects,",");
										$qry_cond   .= " and id in(".$cur_projects.")";
									}
									else{
										$qry_cond   .= " and id=''";
									}
								}
								$cur_row    = 'W'.($m+1).' '.$cur_year;
								$sum_data[$m]	= date_summary($qry_cond,$cur_row,$data['sel_measure'],$view_by,$filters);
								$m++;
								$own	=	$own + $sum_data[$m-1]['own'];
								$open	=	$open + $sum_data[$m-1]['open'];
								$lost	=	$lost + $sum_data[$m-1]['lost'];
								$tot_cnt=	$tot_cnt + $sum_data[$m-1]['total_cnt_deal'];
								$tot_val=	$tot_val + $sum_data[$m-1]['total_val_deal'];
								$tot_avg = $tot_avg + $sum_data[$m-1]['avg_deal'];
								
								$w_start_date	= $w_end_date +1;
								$w_end_date		= $w_end_date +7;
								break;
							}
							else{
								if($num_month >= $start_days){
									$start_date  = date('Y-m-d',strtotime($w_start_date.'-'.$key.'-'.$cur_year));
									$end_date	 = date('Y-m-d',strtotime($w_end_date.'-'.$key.'-'.$cur_year));
									if(check_activity_date($view_by)){
										$qry_cond 	 = " and ".$view_by." >= '".$start_date."' and ".$view_by." <= '".$end_date."'";
									}
									else{
										$customs   = $this->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and CONVERT(cv.value,date)  >='".$start_date."' and CONVERT(cv.value,date) <='".$end_date."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
										$cur_projects = '';
										if(!empty($customs)){
											foreach($customs as $custom1){
												$cur_projects .= $custom1['relid'].',';
											}	
											$cur_projects = rtrim($cur_projects,",");
											$qry_cond   = " and id in(".$cur_projects.")";
										}
										else{
											$qry_cond   = " and id=''";
										}
									}
									$cur_row    = 'W'.($m+1).' '.$cur_year;
									$sum_data[$m]	= date_summary($qry_cond,$cur_row,$data['sel_measure'],$view_by,$filters);
									$m++;
									$own	=	$own + $sum_data[$m-1]['own'];
									$open	=	$open + $sum_data[$m-1]['open'];
									$lost	=	$lost + $sum_data[$m-1]['lost'];
									$tot_cnt=	$tot_cnt + $sum_data[$m-1]['total_cnt_deal'];
									$tot_val=	$tot_val + $sum_data[$m-1]['total_val_deal'];
									$tot_avg = $tot_avg + $sum_data[$m-1]['avg_deal'];
									$w_start_date	= $w_end_date +1;
									$w_end_date		= $w_end_date +7;
									break;
								}
							}
							$k++;
						}
					}
					$sum_data[$m] = deal_avg($own,$open,$lost,$tot_cnt,$tot_val,$view_by,$m,$tot_avg);
					$m++;
					$sum_data[$m] = deal_total($own,$open,$lost,$tot_cnt,$tot_val,$view_by,$tot_avg);
				}
			}
			if($data['view_type'] == 'date' && ($data['date_range1'] == 'Quarterly')){	
				$month_period = array(31,30,30,31);
				$j = 1;
				for($i=0;$i<=3;$i++){
					$k = $j+2;
					$start_date = $cur_year.'-'.$j.'-1';
					$end_date   = $cur_year.'-'.$k.'-'.$month_period[$i];
					if(check_activity_date($view_by)){
						$qry_cond   = " and ".$view_by." >= '".$start_date."' and ".$view_by." <= '".$end_date."' ";
					}
					else{
						$customs   = $this->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and CONVERT(cv.value,date)  >='".$start_date."' and CONVERT(cv.value,date) <='".$end_date."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
						$cur_projects = '';
						if(!empty($customs)){
							foreach($customs as $custom1){
								$cur_projects .= $custom1['relid'].',';
							}	
							$cur_projects = rtrim($cur_projects,",");
							$qry_cond   = " and id in(".$cur_projects.")";
						}
						else{
							$qry_cond   = " and id=''";
						}
					}
					$cur_row    = 'Q'.($i+1).' '.$cur_year;
					$sum_data[$i]	= date_summary($qry_cond,$cur_row,$data['sel_measure'],$view_by,$filters);
					$j = $j+3;
					$tot_avg = $tot_avg + $sum_data[$i]['avg_deal'];
					$own	=	$own + $sum_data[$i]['own'];
					$open	=	$open + $sum_data[$i]['open'];
					$lost	=	$lost + $sum_data[$i]['lost'];
					$tot_cnt=	$tot_cnt + $sum_data[$i]['total_cnt_deal'];
					$tot_val=	$tot_val + $sum_data[$i]['total_val_deal'];
				}
				$sum_data[$i] = deal_avg($own,$open,$lost,$tot_cnt,$tot_val,$view_by,$i,$tot_avg);
				$i++;
				$sum_data[$i] = deal_total($own,$open,$lost,$tot_cnt,$tot_val,$view_by,$tot_avg);
			}
			if($data['view_type'] == 'date' && ($data['date_range1'] == 'Yearly')){	
				$i = 0;
				if(check_activity_date($view_by)){
					$qry_cond   = " and YEAR(".$view_by.") = '".$cur_year."'";
				}
				else{
					$customs   = $this->db->query("SELECT relid  FROM " . db_prefix() . "customfieldsvalues cv,".db_prefix()."customfields cf where cv.fieldto = 'projects' and year(CONVERT(cv.value,date)) <='".$cur_year."' and cf.slug ='".$view_by."' and cf.id = cv.fieldid")->result_array();
						$cur_projects = '';
						if(!empty($customs)){
							foreach($customs as $custom1){
								$cur_projects .= $custom1['relid'].',';
							}	
							$cur_projects = rtrim($cur_projects,",");
							$qry_cond   = " and id in(".$cur_projects.")";
						}
						else{
							$qry_cond   = " and id=''";
						}
				}
				$sum_data[$i]	= date_summary($qry_cond,$cur_year,$data['sel_measure'],$view_by,$filters);
				$own	=	$own + $sum_data[$i]['own'];
				$open	=	$open + $sum_data[$i]['open'];
				$lost	=	$lost + $sum_data[$i]['lost'];
				$tot_cnt=	$tot_cnt + $sum_data[$i]['total_cnt_deal'];
				$tot_val=	$tot_val + $sum_data[$i]['total_val_deal'];$tot_avg=   $tot_avg + $sum_data[$i]['avg_deal'];				
				$i++;
				$sum_data[$i] = deal_avg($own,$open,$lost,$tot_cnt,$tot_val,$view_by,1,$tot_avg);
				$i++;
				$sum_data[$i] = deal_total($own,$open,$lost,$tot_cnt,$tot_val,$view_by,$tot_avg );
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
		$data = get_edit_data('deal',$id);
		$data['summary'] = $this->performance_summary($data);
		$data['report_page'] = 'deal';
        $this->load->view('admin/reports/deals_views', $data);
	}
	public function current_share(){
		if ($this->input->is_ajax_request()) {
			$source_from = array();
			extract($_REQUEST);
			$data = array();
			$shares = $this->db->query("SELECT id,share_type,report_id FROM " . db_prefix() ."shared where  report_id = '".$report_id."'")->result_array();
			$all_staffs = $this->db->query("SELECT staffid,firstname,lastname FROM " . db_prefix() ."staff where active = '1'")->result_array();
			if(!empty($shares)){
				$staffs = $this->db->query("SELECT id,share_id,staff_id FROM " . db_prefix() ."shared_staff where  share_id = '".$shares[0]['id']."'")->result_array();				
				$source_from = array_column($staffs, 'staff_id'); 
				$data['shared'] = $shares[0]['share_type'];
			}
			$req_out = '';
			if(!empty($all_staffs)){
						$i = 0;
				foreach($all_staffs as $staff1){
					if (!empty($source_from) && in_array($staff1['staffid'], $source_from)){
						$req_out .= ' <option value="'.$staff1['staffid'].'" selected>'.$staff1['firstname'].' '.$staff1['lastname'].'</option> ';
					}else{
						$req_out .= ' <option value="'.$staff1['staffid'].'" >'.$staff1['firstname'].' '.$staff1['lastname'].'</option> ';
					}
				}
				$data['staff'] = $req_out;
			}
			echo json_encode($data,true);
		}
	}
	public function share_report(){
		if ($this->input->is_ajax_request()) {
			extract($_REQUEST);
			$shares = $this->db->query("SELECT id FROM " . db_prefix() ."shared where  report_id = '".$report_id."'")->result_array();
			if($_REQUEST['shared'] != 'Everyone' && empty($teamleader12)){
				if(!empty($shares)){
					$share_id = $shares[0]['id'];
					$condition = array('share_id'=>$share_id);
					$table = db_prefix().'shared_staff';
					$this->db->where($condition);
					$result = $this->db->delete($table);
					
					$condition = array('id'=>$share_id);
					$table = db_prefix().'shared';
					$this->db->where($condition);
					$result = $this->db->delete($table);
					echo json_encode([
						'success' => 'success',
						'message' => 'Report Unshared Successfully'
					]);
				}
			}
			else{
				if(empty($shares)){
					$ins_share = array();
					$ins_share['report_id']		= $_REQUEST['report_id'];
					$ins_share['share_type']	= $_REQUEST['shared'];
					$ins_share['shared_source']	= $_REQUEST['folder_type'];
					$this->db->insert(db_prefix() . 'shared', $ins_share);
					$share_id	=	$this->db->insert_id();
					$ins_share = array();
					$ins_share['share_id']		= $share_id;
					if(!empty($teamleader12)){
						$condition = array('share_id'=>$share_id);
						$table = db_prefix() . 'shared_staff';
						$this->db->where($condition);
						$result = $this->db->delete($table);
						foreach($teamleader12 as $teamleader11){
							$ins_share['staff_id']		= $teamleader11;
							$this->db->insert($table, $ins_share);
						}
					}
				}
				else{
					$upd_share = array();
					$share_id = $shares[0]['id'];
					$cond['id']		= $share_id;
					$upd_share['share_type']	= $_REQUEST['shared'];
					$result = $this->db->update(db_prefix() . 'shared', $upd_share, $cond);
					$ins_share = array();
					$ins_share['share_id']		= $share_id;
					if(!empty($teamleader12)){
						$condition = array('share_id'=>$share_id);
						$table = db_prefix() . 'shared_staff';
						$this->db->where($condition);
						$result = $this->db->delete($table);
						foreach($teamleader12 as $teamleader11){
							$ins_share['staff_id']		= $teamleader11;
							$this->db->insert($table, $ins_share);
						}
					}
				}
				echo json_encode([
					'success' => 'success',
					'message' => 'Report Shared Successfully'
				]);
			}
		}
	}
	public function save_2_filter($type = ''){
		$type = ($type == '' || $type == 'deal')?'':'activity_'; 
		$cur_id =  '';
		if(!empty($_REQUEST['cur_id12'])){
			$cur_id = '_edit_'.$_REQUEST['cur_id12'];
		}
		$filters	=	$this->session->userdata($type.'filters2'.$cur_id);
		$filters1	=	$this->session->userdata($type.'filters1'.$cur_id);
		$filter_data = array();
		if(!empty($filters)){
			$i = 0;
			foreach($filters as $filter_val){
				$filter_data[$type.'filters2'.$cur_id][$i]  =	$filter_val;
				$i++;
			}
		}
		if(!empty($filters1)){
			$i = 0;
			foreach($filters1 as $filter_val1){
				$filter_data[$type.'filters1'.$cur_id][$i]  =	$filter_val1;
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
			$filter_data[$type.'filters2'.$cur_id][$_REQUEST['num_val']]	=	$req_val;
		}
		$this->session->set_userdata($filter_data);
	}
	public function save_filter_report($type =''){
		$type = ($type == '' || $type == 'deal')?'':'activity_'; 
		$cur_id =  '';
		if(!empty($_REQUEST['cur_id121'])){
			$cur_id = '_edit_'.$_REQUEST['cur_id121'];
		}
		$filter_data = array();
		$filters	=	$this->session->userdata($type.'filters'.$cur_id);
		$filters1	=	$this->session->userdata($type.'filters1'.$cur_id);
		if(!empty($filters1)){
			$i = 0;
			foreach($filters1 as $filter_val1){
				$filter_data[$type.'filters1'.$cur_id][$i]  =	$filter_val1;
				$i++;
			}
		}
		if(!empty($filters)){
			$i = $j = 0;
			foreach($filters as $filter12){
				if(!empty($_REQUEST['filter_'.$filter12])){
					$req_val = implode(",",$_REQUEST['filter_'.$filter12]);
					$filter_data[$type.'filters2'.$cur_id][$i]	=	$req_val;
					if(empty($filter_data[$type.'filters1'.$cur_id][$i])){
						if($filter12!='product_qty' && $filter12!='product_amt' && $filter12!='project_cost'){
							$filter_data[$type.'filters1'.$cur_id][$i]	=	'is';
						}
						else{
							$filter_data[$type.'filters1'.$cur_id][$i]	=	'is_more_than';
						}
					}
					
					$check_cond = filter_cond1($req_val);
					if(!$check_cond ){
						$filter_data[$type.'filters3'.$cur_id][$i]	=	$_REQUEST['filter_4'][$j];  
						$filter_data[$type.'filters4'.$cur_id][$i]	=	$_REQUEST['filter_5'][$j]; 
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
			if($type == '')
				redirect(admin_url('reports/edit/'.$_REQUEST['cur_id121'].$filter_tab));
			else
				redirect(admin_url('activity_reports/edit/'.$_REQUEST['cur_id121'].$filter_tab));
		}
		else{
			if($type == '')
				redirect(admin_url('reports/add'.$filter_tab));
			else
				redirect(admin_url('activity_reports/add'.$filter_tab));
		}
	}
	public function set_first_filters($cur_val,$cur_num1,$type = ''){
		$type = ($type == '' || $type == 'deal')?'':'activity_'; 
		$cur_num = $cur_num1-1;
		$cur_id12 = '';
		if(!empty($_REQUEST['cur_id12'])){
			$cur_id12 = '_edit_'.$_REQUEST['cur_id12'];
		}
		$filters1	=	$this->session->userdata($type.'filters1'.$cur_id12);
		if(!empty($filters1)){
			foreach($filters1 as $key12 => $filter1){
				$filter_data[$type.'filters1'.$cur_id12][$key12]	=	$filter1;  
			}
		}
		$req_out = '';
		$filter_data[$type.'filters1'.$cur_id12][$cur_num]	=	$cur_val;
		$this->session->set_userdata($filter_data);
		return true;
	}
	public function set_second_filters($type=''){
		$type = ($type == '' || $type == 'deal')?'':'activity_'; 
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
			$filters2	=	$this->session->userdata($type.'filters2');
			$filters3	=	$this->session->userdata($type.'filters3');
		}
		else{
			$filters2	=	$this->session->userdata($type.'filters2'.$cur_id12);
			$filters3	=	$this->session->userdata($type.'filters3'.$cur_id12);
		}
		if(!empty($filters2)){
			foreach($filters2 as $key12 => $filter2){
				$filter_data[$type.'filters2'.$cur_id12][$key12]	=	$filter2;  
			}
		}
		$filter_data[$type.'filters2'.$cur_id12][$cur_num]	=	$cur_val;
		if($cur_val=='last_year'){
			$last_year = date('Y')-1;
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data[$type.'filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data[$type.'filters3'.$cur_id12][$cur_num]	=	'01-01-'.$last_year;  
			}
			$filters4	=	$this->session->userdata($type.'filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data[$type.'filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data[$type.'filters4'.$cur_id12][$cur_num]	=	'31-12-'.$last_year;
			}
		}
		if($cur_val=='next_year'){
			$next_year = date('Y')+1;
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data[$type.'filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data[$type.'filters3'.$cur_id12][$cur_num]	=	'01-01-'.$next_year;  
			}
			$filters4	=	$this->session->userdata($type.'filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data[$type.'filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data[$type.'filters4'.$cur_id12][$cur_num]	=	'31-12-'.$next_year;
			}
		}
		if($cur_val=='this_year'){
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data[$type.'filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data[$type.'filters3'.$cur_id12][$cur_num]	=	'01-01-'.date('Y');  
			}
			$filters4	=	$this->session->userdata($type.'filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data[$type.'filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data[$type.'filters4'.$cur_id12][$cur_num]	=	'31-12-'.date('Y');
			}
		}
		 elseif ($cur_val == 'today') {
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data[$type.'filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data[$type.'filters3'.$cur_id12][$cur_num]		=	date('d-m-Y');  
			}
			$filters4	=	$this->session->userdata($type.'filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data[$type.'filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data[$type.'filters4'.$cur_id12][$cur_num]		=	date('d-m-Y');
			}
		} elseif ($cur_val == 'yesterday') {
			$yesterday = date('d-m-Y',strtotime("-1 days"));
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data[$type.'filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data[$type.'filters3'.$cur_id12][$cur_num]		=	$yesterday;  
			}
			$filters4	=	$this->session->userdata($type.'filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data[$type.'filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data[$type.'filters4'.$cur_id12][$cur_num]		=	$yesterday;
			}
		} 
		elseif ($cur_val == 'tomorrow') {
			$tomorrow = date('d-m-Y',strtotime("+1 days"));
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data[$type.'filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data[$type.'filters3'.$cur_id12][$cur_num]		=	$tomorrow;  
			}
			$filters4	=	$this->session->userdata($type.'filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data[$type.'filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data[$type.'filters4'.$cur_id12][$cur_num]		=	$tomorrow;
			}
		} 
		elseif ($cur_val == 'this_week') {
			$str_date = date('d-m-Y',strtotime('monday this week'));
			$end_date = date('d-m-Y',strtotime('sunday this week'));
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data[$type.'filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data[$type.'filters3'.$cur_id12][$cur_num]		=	$str_date;  
			}
			$filters4	=	$this->session->userdata($type.'filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data[$type.'filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data[$type.'filters4'.$cur_id12][$cur_num]		=	$end_date;
			}
		} elseif ($cur_val == 'last_week') {
			$str_date = date('d-m-Y',strtotime('monday this week',strtotime("-1 week +1 day")));
			$end_date = date('d-m-Y',strtotime('sunday this week',strtotime("-1 week +1 day")));
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data[$type.'filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data[$type.'filters3'.$cur_id12][$cur_num]		=	$str_date;  
			}
			$filters4	=	$this->session->userdata($type.'filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data[$type.'filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data[$type.'filters4'.$cur_id12][$cur_num]		=	$end_date;
			}
		} 
		elseif ($cur_val == 'next_week') {
			$str_date = date('d-m-Y',strtotime('monday this week',strtotime("+1 week +1 day")));
			$end_date = date('d-m-Y',strtotime('sunday this week',strtotime("+1 week +1 day")));
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data[$type.'filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data[$type.'filters3'.$cur_id12][$cur_num]		=	$str_date;  
			}
			$filters4	=	$this->session->userdata($type.'filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data[$type.'filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data[$type.'filters4'.$cur_id12][$cur_num]		=	$end_date;
			}
		} 
		elseif ($cur_val == 'this_month') {
			$str_date = date('01-m-Y');
			$end_date = date('t-m-Y');
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data[$type.'filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data[$type.'filters3'.$cur_id12][$cur_num]		=	$str_date;  
			}
			$filters4	=	$this->session->userdata($type.'filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data[$type.'filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data[$type.'filters4'.$cur_id12][$cur_num]		=	$end_date;
			}
		}
		elseif ($cur_val == 'last_month') {
			$str_date = date('01-m-Y',strtotime('last month'));
			$end_date = date('t-m-Y',strtotime('last month'));
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data[$type.'filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data[$type.'filters3'.$cur_id12][$cur_num]		=	$str_date;  
			}
			$filters4	=	$this->session->userdata($type.'filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data[$type.'filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data[$type.'filters4'.$cur_id12][$cur_num]		=	$end_date;
			}
		}
		elseif ($cur_val == 'next_month') {
			$date = date('01-m-Y');
			$str_date = date("01-m-Y", strtotime ( '+1 month' , strtotime ( $date ) )) ;
			$end_date = date("31-m-Y", strtotime ( '+1 month' , strtotime ( $date ) )) ;
			if(!empty($filters3)){
				foreach($filters3 as $key12 => $filter3){
					$filter_data[$type.'filters3'.$cur_id12][$key12]	=	$filter3;  
				}
				$filter_data[$type.'filters3'.$cur_id12][$cur_num]	=	$str_date;  
			}
			$filters4	=	$this->session->userdata($type.'filters4'.$cur_id12);
			if(!empty($filters4)){
				foreach($filters4 as $key12 => $filter4){
					$filter_data[$type.'filters4'.$cur_id12][$key12]	=	$filter4;  
				}
				$filter_data[$type.'filters4'.$cur_id12][$cur_num]		=	$end_date;
			}
		}		
		$this->session->set_userdata($filter_data);
		return true;
	}
	public function set_3_filters($type=''){
		$type = ($type == '' || $type == 'deal')?'':'activity_'; 
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
			$filters2	=	$this->session->userdata($type.'filters2');
			$filters3	=	$this->session->userdata($type.'filters3');
		}
		else{
			$filters2	=	$this->session->userdata($type.'filters2'.$cur_id12);
			$filters3	=	$this->session->userdata($type.'filters3'.$cur_id12);
		}
		if(!empty($filters2)){
			foreach($filters2 as $key12 => $filter2){
				$filter_data[$type.'filters2'.$cur_id12][$key12]	=	$filter2;  
			}
		}
		$filter_data['filters2'.$cur_id12][$cur_num]	=	'custom_period';
		if(!empty($filters3)){
			foreach($filters3 as $key12 => $filter3){
				$filter_data[$type.'filters3'.$cur_id12][$key12]	=	$filters3;  
			}
			$filter_data[$type.'filters3'.$cur_id12][$cur_num]	=	$cur_val;  
		}
		$this->session->set_userdata($filter_data);
		return true;
	}
	public function set_4_filters($type=''){
		$type = ($type == '' || $type == 'deal')?'':'activity_'; 
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
			$filters2	=	$this->session->userdata($type.'filters2');
			$filters4	=	$this->session->userdata($type.'filters4');
		}
		else{
			$filters2	=	$this->session->userdata($type.'filters2'.$cur_id12);
			$filters4	=	$this->session->userdata($type.'filters4'.$cur_id12);
		}
		if(!empty($filters2)){
			foreach($filters2 as $key12 => $filter2){
				$filter_data[$type.'filters2'.$cur_id12][$key12]	=	$filter2;  
			}
		}
		$filter_data[$type.'filters2'.$cur_id12][$cur_num]	=	'custom_period';
		if(!empty($filters4)){
			foreach($filters4 as $key12 => $filter3){
				$filter_data[$type.'filters4'.$cur_id12][$key12]	=	$filters3;  
			}
			$filter_data[$type.'filters4'.$cur_id12][$cur_num]	=	$cur_val;  
		}
		$this->session->set_userdata($filter_data);
		return true;
	}
	public function set_filters($cur_val='',$cur_num1=''){
		$cur_id12 = '';
		if(!empty($_REQUEST['cur_id12'])){
			$cur_id12 = '_edit_'.$_REQUEST['cur_id12'];
		}
		$filters	=	$this->session->userdata('filters'.$cur_id12);
		if(!empty($filters)){
			foreach($filters as $key12 => $filter12){
				$filter_data['filters'.$cur_id12][$key12]	=	$filter12;  
			}
		}
		if(!empty($cur_val) && !empty($cur_num1)){
			$filter_data['filters'.$cur_id12][$cur_num1]	=	$cur_val;
		}
		else{
			$cur_val  = $_REQUEST['cur_val'];
			$cur_num1 = $_REQUEST['req_val']-1;
			$filter_data['filters'.$cur_id12][$cur_num1]	=	$cur_val;
		}
		$filters1	=	$this->session->userdata('filters1'.$cur_id12);
		$filters2	=	$this->session->userdata('filters2'.$cur_id12);
		if(!empty($filters1)){
			foreach($filters1 as $key12 => $filter1){
				$filter_data['filters1'.$cur_id12][$key12]	=	$filter1;  
			}
		}
		if(!empty($filters2)){
			foreach($filters2 as $key12 => $filter1){
				$filter_data['filters2'.$cur_id12][$key12]	=	$filter1;  
			}
		}
		switch($cur_val){
			case 'project_start_date':
			case 'project_deadline':
			case 'won_date':
			case 'lost_date':
			case 'project_created':
			case 'project_modified':
				$filters3	=	$this->session->userdata('filters3'.$cur_id12);
				$filters4	=	$this->session->userdata('filters4'.$cur_id12);
				if(!empty($filters3)){
					foreach($filters3 as $key12 => $filter1){
						$filter_data['filters3'.$cur_id12][$key12]	=	$filter1;
					}
				}
				if(!empty($filters4)){
					foreach($filters4 as $key12 => $filter1){
						$filter_data['filters4'.$cur_id12][$key12]	=	$filter1;
					}
				}
				$filter_data['filters1'.$cur_id12][$cur_num1]	=	'is';  
				$filter_data['filters2'.$cur_id12][$cur_num1]	=	'this_year';  
				$filter_data['filters3'.$cur_id12][$cur_num1]	=	'01-01-'.date('Y');  
				$filter_data['filters4'.$cur_id12][$cur_num1]	=	'31-12-'.date('Y'); 
				break;
			case 'name':
			case 'teamleader_name':
			case 'contact_name':
			case 'company':
			case 'members':
			case 'created_by':
			case 'modified_by':
				$filter_data['filters1'.$cur_id12][$cur_num1]	=	'is'; 
				$filter_data['filters2'.$cur_id12][$cur_num1] = '';
				break;
			case 'loss_reason':
				$filter_data['filters1'.$cur_id12][$cur_num1]	=	'is'; 
				$this->db->where('publishstatus', '1');
				$reasons = $this->db->get(db_prefix() . 'deallossreasons')->result_array();
				$filter_data['filters2'.$cur_id12][$cur_num1]	=	$reasons[0]['id'];
				break;
			case 'project_currency':
				$filter_data['filters1'.$cur_id12][$cur_num1]	=	'is'; 
				$this->db->order_by('isdefault', 'desc');
				$currencies = $this->db->get(db_prefix() . 'currencies')->result_array();
				$filter_data['filters2'.$cur_id12][$cur_num1]	=	$currencies[0]['name'];
				break;
			case 'status':
				$all_status = get_stage_report();
				$filter_data['filters1'.$cur_id12][$cur_num1]	=	'is'; 
				$filter_data['filters2'.$cur_id12][$cur_num1]	=	$all_status[0]['id']; 
				break;
			case 'project_status':
				$filter_data['filters1'.$cur_id12][$cur_num1]	=	'is'; 
				$filter_data['filters2'.$cur_id12][$cur_num1]	=	'WON';  
				break;
			case 'pipeline_id':
				$pipelines = get_pipeline_report();
				$filter_data['filters1'.$cur_id12][$cur_num1]	=	'is'; 
				$filter_data['filters2'.$cur_id12][$cur_num1]	=	$pipelines[0]['id'];  
				break;
			case 'tags':
			case 'contact_email1':
			case 'contact_phone1':
				$filter_data['filters1'.$cur_id12][$cur_num1]	=	'is'; 
				break;
			case 'product_qty':
			case 'product_amt':
			case 'project_cost':
				$filter_data['filters1'.$cur_id12][$cur_num1]	=	'is_more_than';  
				break;
			default:
				$fields =  $this->db->query("SELECT type FROM " . db_prefix() . "customfields where slug = '".$cur_val."' ")->row();
				if($fields->type == 'date_picker'){
					$filters3	=	$this->session->userdata('filters3'.$cur_id12);
					$filters4	=	$this->session->userdata('filters4'.$cur_id12);
					if(!empty($filters3)){
						foreach($filters3 as $key12 => $filter1){
							$filter_data['filters3'.$cur_id12][$key12]	=	$filter1;  
						}
					}
					if(!empty($filters4)){
						foreach($filters4 as $key12 => $filter1){
							$filter_data['filters4'.$cur_id12][$key12]	=	$filter1;  
						}
					}
					$filter_data['filters1'.$cur_id12][$cur_num1]	=	'is';  
					$filter_data['filters2'.$cur_id12][$cur_num1]	=	'this_year';  
					$filter_data['filters3'.$cur_id12][$cur_num1]	=	'01-01-'.date('Y');  
					$filter_data['filters4'.$cur_id12][$cur_num1]	=	'31-12-'.date('Y'); 
				}
				else if($fields->type == 'select'){
					$filter_data['filters1'.$cur_id12][$cur_num1]	=	'is';
				}
				else if($fields->type == 'number'){
					$filter_data['filters1'.$cur_id12][$cur_num1]	=	'is_more_than';
				}
				else{
					$filter_data['filters1'.$cur_id12][$cur_num1]	=	'is';
				}
				break;
		}	
		$this->session->set_userdata($filter_data);
		return true;
	}
	public function del_filter($type = ''){
		$type = ($type == '' || $type == 'deal')?'':'activity_'; 
		$cur_id12 = '';
		if(!empty($_REQUEST['cur_id12'])){
			$cur_id12 = '_edit_'.$_REQUEST['cur_id12'];
		}
		$filter_data = array();
		$filters	=	$this->session->userdata($type.'filters'.$cur_id12);
		$cur_num1 = $_REQUEST['req_val'];
		if(!empty($filters)){
			$i = 1;
			$i1 = 0;
			foreach($filters as $key12 => $filter12){
				if($i!=$cur_num1){
					$filter_data[$type.'filters'.$cur_id12][$i1]	=	$filter12;  
					$i1++;
				}
				$i++;
			}
		}
		$filters1	=	$this->session->userdata($type.'filters1'.$cur_id12);
		if(!empty($filters1)){
			$i = 1;$i1 = 0;
			foreach($filters1 as $key12 => $filter1){
				if($i!=$cur_num1){
					$filter_data[$type.'filters1'.$cur_id12][$i1]	=	$filter1; 
					$i1++;
				}
				$i++;
			}
		}
		$filters2	=	$this->session->userdata($type.'filters2'.$cur_id12);
		$filters3	=	$this->session->userdata($type.'filters3'.$cur_id12);
		$filters4	=	$this->session->userdata($type.'filters4'.$cur_id12);
		if(!empty($filters2)){
			$i = 1;$i1 = 0;
			foreach($filters2 as $key12 => $filter1){
				if($i!=$cur_num1){
					$filter_data[$type.'filters2'.$cur_id12][$i1]	=	$filter1; 
					$i1++;
				}
				$i++;
			}
		}
		if(!empty($filters3)){
			$i = 1;$i1 = 0;
			foreach($filters3 as $key12 => $filter1){
				if($i!=$cur_num1){
					$filter_data[$type.'filters3'.$cur_id12][$i1]	=	$filter1;  
					$i1++;
				}
				$i++;
			}
		}
		if(!empty($filters4)){
			$i = 1;$i1 = 0;
			foreach($filters4 as $key12 => $filter1){
				if($i!=$cur_num1){
					$filter_data[$type.'filters4'.$cur_id12][$i1]	=	$filter1;  
					$i1++;
				} 
				$i++;
			}
		}	
		if(empty($filter_data)){
			$filter_data[$type.'filters'.$cur_id12]  = array();
			$filter_data[$type.'filters1'.$cur_id12] = array();
			$filter_data[$type.'filters2'.$cur_id12] = array();
			$filter_data[$type.'filters3'.$cur_id12] = array();
			$filter_data[$type.'filters4'.$cur_id12] = array();
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
		$deal_val 	=	deal_values();
		$data 		=  	json_decode($deal_val, true);
		$all_clmns  = 	$data['all_clmns'];
		unset($all_clmns['id']);
		unset($all_clmns['product_count']);
		unset($all_clmns['project_created']);
		$cus_flds = $data['cus_flds'];
		$filter_data = set_filter('',$all_clmns,$cus_flds);
		$this->session->set_userdata($filter_data);
		$filters	=	$this->session->userdata('filters'.$cur_id12);

		$req_out = '';
		$fields = deal_needed_fields();
		$needed = json_decode($fields,true);
		if (($key = array_search('id', $needed['need_fields'])) !== false ) {
			unset($needed['need_fields'][$key]);
		}
		if (($key = array_search('project_created', $needed['need_fields'])) !== false ) {
			unset($needed['need_fields'][$key]);
		}
		if (($key = array_search('product_count', $needed['need_fields'])) !== false ) {
			unset($needed['need_fields'][$key]);
		}
		$need_fields		=	$needed['need_fields'];
		if(!empty($filters)){
			$i1 = 1;
			foreach($filters as $key => $filter1){
				$req_out	.=	'<div  class="col-md-12 m-bt-10"><div  class="col-md-2" >';
				$req_out	.=	'<select data-live-search="true" class="selectpicker" id="filter_'.$i1.'" onchange="change_filter(this)">';
				if(!empty($all_clmns)){ 
					$req_out	.=	'<optgroup label="Deal Master" data-max-options="2">';
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
		$filters	=	$this->session->userdata('filters'.$cur_id12);
		$filters2	=	$this->session->userdata('filters2'.$cur_id12);
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
			case 'teamleader_name':
				$selected = '';
				$rel_data = get_relation_data('manager',$selected);
				$rel_val = get_relation_values($rel_data,'contacts');
				if(empty($filters2[$req_val-1])){
					$req_out = get_req_val($req_val,'select','id','name','',$rel_val,'deal');
				}
				else{
					$req_data = array();
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
					$req_out = get_req_val($req_val,'select','staffid','firstname,lastname','',$req_data,'deal');
				}
				break;
			case 'contact_name':
				$selected = '';
				$rel_data = get_relation_data('contacts',$selected);
				$rel_val = get_relation_values($rel_data,'contacts');
				if(empty($filters2[$req_val-1])){
					$req_out = get_req_val($req_val,'select','id','name','',$rel_val,'deal');
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
					$req_out = get_req_val($req_val,'select','id','firstname,lastname','',$req_data,'deal');
				}
				break;	
			case 'company':
				$selected = '';
				$rel_data = get_relation_data('customer',$selected);
				$rel_val = get_relation_values($rel_data,'customer');
				if(empty($filters2[$req_val-1])){
					$req_out = get_req_val($req_val,'select','id','name','',$rel_val,'deal');
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
					$req_out = get_req_val($req_val,'select','userid','company','',$req_data,'deal');
				}
				break;
				
				case 'project_start_date':
				case 'project_deadline':
				case 'won_date':
				case 'lost_date':
				case 'project_created':
				case 'project_modified':
					$req_out = get_req_val($req_val,'date','','','','','deal');
					break;
				case 'modified_by':
				case 'created_by':
				case 'members':
				
					$selected = '';
					$rel_data = get_relation_data('staff',$selected);
					$rel_val = get_relation_values($rel_data,'staff');
					if(empty($filters2[$req_val-1])){
						$req_out = get_req_val($req_val,'select','id','name','',$rel_val,'deal');
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
						$req_out = get_req_val($req_val,'select','staffid','firstname,lastname','',$req_data,'deal');
					}
					break;
				case 'status':
					$all_status = get_stage_report();
					
					$req_out = get_req_val($req_val,'select','id','name','',$all_status,'deal');
					
					break;
				case 'project_status':
					$all_status = array('WON'=>'WON','LOSS'=>'LOST');
					$req_out = get_req_val($req_val,'select','','','key',$all_status,'deal');
					break;
				case 'pipeline_id':
					$pipelines = get_pipeline_report();
					$req_out = get_req_val($req_val,'select','id','name','',$pipelines,'deal');
					break;
				case 'loss_reason_name':
					$this->db->where('publishstatus', '1');
					$reasons = $this->db->get(db_prefix() . 'deallossreasons')->result_array();
					$req_out = get_req_val($req_val,'select','id','name','',$reasons,'deal');
					break;
				case 'project_currency':
					$filter_data['filters1'.$cur_id12][$cur_num1]	=	'is'; 
					$this->db->order_by('isdefault', 'desc');
					$currencies = $this->db->get(db_prefix() . 'currencies')->result_array();
					$req_out = get_req_val($req_val,'select','id','name','',$currencies,'deal');
					break;
				case 'name':
					$selected = '';
					$cond = array();
					$rel_data = get_relation_data('project',$selected);
					$req_data = array();
					$rel_val = get_relation_values($rel_data,'project');
					if(empty($filters2[$req_val-1])){
						$req_out = get_req_val($req_val,'select','id','name','',$rel_val,'deal');
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
						$req_out = get_req_val($req_val,'select','id','name','',$req_data,'deal');
					}
					break;
				case 'tags':
					$selected = '';
					$rel_val = get_relation_values($rel_data,'tags');
					$rel_data = get_relation_data('tags',$selected);
					if(empty($filters2[$req_val-1])){
						$rel_data = array();
						$req_out = get_req_val($req_val,'select','id','name','',$rel_data,'deal');
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
						$req_out = get_req_val($req_val,'select','id','name','',$req_data,'deal');
					}
					break;
				case 'contact_email1':
					$selected = '';
					$rel_data = get_relation_data('contact',$selected);
					$rel_val = get_relation_values($rel_data,'contact');
					if(empty($filters2[$req_val-1])){
						$req_out = get_req_val($req_val,'select','email','email','',$rel_val,'deal');
					}else{
						if (str_contains($filters2[$req_val-1], ',')) {
							$req_vals1 = explode(',',$filters2[$req_val-1]);
							$i2 =0;
						}
						foreach($rel_data as $rel_li1){
							if (str_contains($filters2[$req_val-1], ',')) {
								if(in_array($rel_li1['email'],$req_vals1)){
									$req_data[$i2] = $rel_li1;
									$i2++;
								}
							}
							else{
								if($rel_li1['email']==$filters2[$req_val-1]){
									$req_data[0] = $rel_li1;
									break;
								}

							}
						}
						$req_out = get_req_val($req_val,'select','email','email','',$req_data,'deal');
					}
					break;
				case 'contact_phone1':
					$selected = '';
					$rel_data = get_relation_data('contacts',$selected);
					$rel_val = get_relation_values($rel_data,'contacts');

					if(empty($filters2[$req_val-1])){
						$req_out = get_req_val($req_val,'select','phonenumber','phonenumber','',$rel_val,'deal');
					}else{
						if (str_contains($filters2[$req_val-1], ',')) {
							$req_vals1 = explode(',',$filters2[$req_val-1]);
							$i2 =0;
						}
						foreach($rel_data as $rel_li1){
							if (str_contains($filters2[$req_val-1], ',')) {
								if(in_array($rel_li1['phonenumber'],$req_vals1)){
									$req_data[$i2] = $rel_li1;
									$i2++;
								}
							}
							else{
								if($rel_li1['phonenumber']==$filters2[$req_val-1]){
									$req_data[0] = $rel_li1;
									break;
								}
							}
						}
						$req_out = get_req_val($req_val,'select','phonenumber','phonenumber','',$req_data,'deal');
					}
					break;
					case 'project_cost':
				case 'product_qty':
				case 'product_amt':
					$req_out = get_req_val($req_val,'number','','','','','deal');
					break;
				default:
					$fields =  $this->db->query("SELECT type,options FROM " . db_prefix() . "customfields where slug = '".$cur_val."' ")->row();
					if($fields->type == 'date_picker' || $fields->type == 'date_picker_time' || $fields->type == 'date_range'){
						$req_out = get_req_val($req_val,'date','','','','','deal');
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
						$req_out = get_req_val($req_val,'select','','','key',$req_array,'deal');
					}
					else if($fields->type == 'number'){
						$req_out = get_req_val($req_val,'number','','','','','deal');
					}
					else{
						$req_out = get_req_val($req_val,'text','','','','','deal');
					}
					break;
		}
		if(!empty($check_val1) && !empty($check_val2)){
			return $req_out;
		}else{
			echo $req_out;
		}
	}
	public function add_folder(){
		if ($this->input->is_ajax_request()) {
			$ins_section = array();
			$ins_section['folder'] = $_REQUEST['name1'];
			$ins_section['folder_type'] = $_REQUEST['folder_type'];
			$this->db->insert(db_prefix() . 'folder', $ins_section);
			$data =  get_report_folder($_REQUEST['folder_type']);
			$options = '';
			foreach($data as $val) {
				$options .= '<option value="'.$val['id'].'">'.$val['folder'].'</option>';
			}
			echo json_encode([
				'success' => $options
			]);
		}
	}
	public function shared($shared){
		$links = $this->db->query("SELECT id FROM " . db_prefix() . "report_public WHERE share_link = '".$shared."' ")->result_array();
		if(empty($links) || empty($shared)){
			redirect(admin_url());
			exit;
		}
		$data = array();
		$this->load->view('admin/reports/public',$data);
	}
	public function update_report($req_id,$type=''){
		$cur_id12 = '_edit_'.$req_id;
		$type = ($type=='')?'deal':$type;
		$req_filter = ($type=='deal')?'':'activity_';
		$condition = array('report_id'=>$req_id);
		$table = db_prefix() . 'report_filter';
		$this->db->where($condition);
		$result = $this->db->delete($table);
		$filters	=	$this->session->userdata($req_filter.'filters'.$cur_id12);
		$filters1	=	$this->session->userdata($req_filter.'filters1'.$cur_id12);
		$filters2	=	$this->session->userdata($req_filter.'filters2'.$cur_id12);
		$filters3	=	$this->session->userdata($req_filter.'filters3'.$cur_id12);
		$filters4	=	$this->session->userdata($req_filter.'filters4'.$cur_id12);
		if(!empty($filters)){
			$i = 0;
			foreach($filters as $filter12){
				$filter_report = array();
				$filter_report['report_id']	=	$req_id;
				$filter_report['filter_1']	=	$filter12;
				$filter_report['filter_2']	=	$filters1[$i];
				if(!empty($filters2[$i]))
					$filter_report['filter_3']	=	$filters2[$i];
				if(!empty($filters3[$i]))
					$filter_report['filter_4']	=	$filters3[$i];
				if(!empty($filters4[$i]))
					$filter_report['filter_5']	=	$filters4[$i];
				$this->db->insert(db_prefix() . 'report_filter', $filter_report);
				$i++;;
			}
		}
		$reports =  $this->db->query("SELECT folder_id FROM " . db_prefix() . "report WHERE id = '".$req_id."'")->result_array();
		$folder = $reports[0]['folder_id'];
		$this->session->unset_userdata($req_filter.'filters'.$cur_id12);
		$this->session->unset_userdata($req_filter.'filters1'.$cur_id12);
		$this->session->unset_userdata($req_filter.'filters2'.$cur_id12);
		$this->session->unset_userdata($req_filter.'filters3'.$cur_id12);
		$this->session->unset_userdata($req_filter.'filters4'.$cur_id12);
		set_alert('success', _l('updated_successfully', _l('report')));
		redirect(admin_url('reports/view_deal_report/'.$folder.'/'.$type));
	}
	public function delete_link(){
		$report_id = $_REQUEST['cur_id12'];
		$cur_id12  = $_REQUEST['req_val'];
		$cond = array('id'=>$cur_id12);
		$this->db->where($cond);
		$this->db->delete(db_prefix() . 'report_public');
		$req_out = '';
		$req_out = get_public($report_id);
		echo $req_out;
		
	}
	public function public_link(){
		$report_id = $_REQUEST['req_val'];
		$ins_public = array();
		$ins_public['report_id']	=	$report_id;
		$this->db->insert(db_prefix() . 'report_public', $ins_public);
		$public_id	=	$this->db->insert_id();
		$cond = array('id'=>$public_id);
		$upd_public['share_link'] = md5($public_id.$report_id.'_sharelink');
		$this->db->update(db_prefix() . 'report_public', $upd_public, $cond);
		$req_out = get_public($report_id);
		echo $req_out;
	}
	public function load_public(){
		$report_id = $_REQUEST['cur_id'];
		$req_out = get_public($report_id);
		echo $req_out;
	}
	public function check_publick(){
		$report_id = $_REQUEST['req_val'];
		$req_out = '';
		$links = $this->db->query("SELECT link_name FROM " . db_prefix() . "report_public WHERE id = '".$report_id."' ")->row();
		echo $req_out = $links->link_name;
	}
	public function update_public_name(){
		extract($_REQUEST);
		$cond = array('id'=>$link_id);
		$upd_data = array('link_name'=>$ch_name12);
		$result = $this->db->update(db_prefix() . "report_public", $upd_data, $cond);
		return true;
	}
	public function save_report($type=''){
		$cur_id12 = '';
		extract($_POST);
		if(!empty($_REQUEST['cur_id12'])){
			$cur_id12 = '_edit_'.$_REQUEST['cur_id12'];
		}
		$report_from = '';
		if($folder_type == 'activity'){
			$report_from = 'activity_';
		}
		$filters	=	$this->session->userdata($report_from.'filters'.$cur_id12);
		$filters1	=	$this->session->userdata($report_from.'filters1'.$cur_id12);
		$filters2	=	$this->session->userdata($report_from.'filters2'.$cur_id12);
		$filters3	=	$this->session->userdata($report_from.'filters3'.$cur_id12);
		$filters4	=	$this->session->userdata($report_from.'filters4'.$cur_id12);
		$ins_report = array();
		$ins_report['folder_id']	=	$folder;
		$ins_report['report_name']	=	$name;
		if(empty($cur_id12))
			$ins_report['report_type']	=	$this->session->userdata('report_type');
		else{
			$report = $this->db->query("SELECT report_type FROM " . db_prefix() . "report WHERE id = '".$_REQUEST['cur_id12']."' ")->row();
			$ins_report['report_type']	=	$report->report_type;
		}
		$this->db->insert(db_prefix() . 'report', $ins_report);
		$report_id	=	$this->db->insert_id();
		if(!empty($filters)){
			$i = 0;
			foreach($filters as $filter12){
				$filter_report = array();
				$filter_report['report_id']	=	$report_id;
				$filter_report['filter_1']	=	$filter12;
				$filter_report['filter_2']	=	$filters1[$i];
				if(!empty($filters2[$i]))
					$filter_report['filter_3']	=	$filters2[$i];
				if(!empty($filters3[$i]))
					$filter_report['filter_4']	=	$filters3[$i];
				if(!empty($filters4[$i]))
					$filter_report['filter_5']	=	$filters4[$i];
				$this->db->insert(db_prefix() . 'report_filter', $filter_report);
				$i++;;
			}
		}
		$this->session->unset_userdata($report_from.'filters'.$cur_id12);
		$this->session->unset_userdata($report_from.'filters1'.$cur_id12);
		$this->session->unset_userdata($report_from.'filters2'.$cur_id12);
		$this->session->unset_userdata($report_from.'filters3'.$cur_id12);
		$this->session->unset_userdata($report_from.'filters4'.$cur_id12);
		set_alert('success', _l('added_successfully', _l('report')));
		redirect(admin_url('reports/view_deal_report/'.$folder.'/'.$folder_type));
	}
	public function view_deal_folder($type=''){
		if (!has_permission('reports', '', 'view')) {
            access_denied('reports');
        }
		$type = ($type=='')?'deal':$type;
		if(empty($type) || ( $type!='deal' && $type!='activity')){
			redirect(admin_url());
		}
		$data = array();
		$data['title']   =  _l('view_report');
		$data['type']    =  $type;
		$this->load->view('admin/reports/folder_deal', $data);
	}
	public function all_share($type=''){
		if (!has_permission('reports', '', 'view')) {
            access_denied('reports');
        }
		$type = ($type=='')?'deal':$type;
		if(empty($type) || ( $type!='deal' && $type!='activity')){
			redirect(admin_url());
		}
		$data = array();
		$data['title']   =  _l('shared_list');
		$data['type']    =  $type;
		$this->load->view('admin/reports/shared_list', $data);
	}
	public function view_shared($id,$type=''){
		$type = ($type=='')?'deal':$type;
		if(empty($type) || ( $type!='deal' && $type!='activity')){
			redirect(admin_url());
		}
		$req_url = ($type=='deal')?'':'activity_';
		$this->load->model('pipeline_model');
		$data = array();
		$data['title'] = _l('add_report');
		$deal_val = deal_values();
		$data =  json_decode($deal_val, true);
		$data['filters']	=	$this->session->userdata($req_url.'filters_edit_'.$id);
		$data['filters1']	=	$this->session->userdata($req_url.'filters1_edit_'.$id);
		$data['filters2']	=	$this->session->userdata($req_url.'filters2_edit_'.$id);
		$data['filters3']	=	$this->session->userdata($req_url.'filters3_edit_'.$id);
		$data['filters4']	=	$this->session->userdata($req_url.'filters4_edit_'.$id);
		
		$shares = $this->db->query("SELECT report_id FROM " . db_prefix() . "shared WHERE id = '".$id."' ")->row();
		$id = $shares->report_id;
		if(empty($id)){
			redirect(admin_url('reports/view_deal_folder/'.$type));
			exit;
		}
		$data['id'] = $id;
		$reports1 = $this->db->query("SELECT report_name,report_type,folder_id FROM " . db_prefix() . "report WHERE id = '".$id."' ")->row();
		
		$data['report_name']		=	$reports1->report_name.'('.$reports1->report_type.')';
		$data['folder_id']			=	$reports1->folder_id;
		if($type == 'deal'){
			$fields = deal_needed_fields();
			$needed = json_decode($fields,true);
		}
		else{
			$needed = get_tasks_need_fields();
		}
		if (($key = array_search('id', $needed['need_fields'])) !== false) {
			unset($needed['need_fields'][$key]);
		}
		if (($key = array_search('project_created', $needed['need_fields'])) !== false ) {
			unset($needed['need_fields'][$key]);
		}
		if (($key = array_search('product_count', $needed['need_fields'])) !== false ) {
			unset($needed['need_fields'][$key]);
		}
		$data['type']				=	$type;
		$data['need_fields']		=	$needed['need_fields'];
		$data['need_fields_label']	=	$needed['need_fields_label'];
		$data['need_fields_edit']	=	$needed['need_fields_edit'];
		$data['mandatory_fields1']	=	$needed['mandatory_fields1'];
		
        $this->load->view('admin/reports/share_view', $data);
	}
	public function shared_list(){
		if (!has_permission('reports', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data('shared_list');
	}
	public function folder_edit(){
		$folder_id = $_REQUEST['cur_id'];
		$req_out = '';
		$result = $this->db->query("SELECT folder FROM " . db_prefix() . "folder WHERE id = '".$folder_id."' ")->row();
		echo $result->folder;
	}
	public function report_edit(){
		$report_id = $_REQUEST['cur_id'];
		$req_out = '';
		$result = $this->db->query("SELECT report_name,report_type FROM " . db_prefix() . "report WHERE id = '".$report_id."' ")->row();
		echo $result->report_name.' '.$result->report_type;
	}
	public function update_folder(){
		if ($this->input->is_ajax_request()) {
			$upd_folder = array();
			$upd_folder['folder'] = $_REQUEST['name'];
			$cond['id']			  = $_REQUEST['folder_id'];
			$result = $this->db->update(db_prefix() . 'folder', $upd_folder, $cond);
			echo json_encode([
				'success' => 'success'
			]);
		}
	}
	public function update_edit_report(){
		if ($this->input->is_ajax_request()) {
			$upd_report = array();
			$upd_report['report_name'] = $_REQUEST['name'];
			$cond['id']			  = $_REQUEST['report_id'];
			$result = $this->db->update(db_prefix() . 'report', $upd_report, $cond);
			echo json_encode([
				'success' => 'success'
			]);
		}
	}
	public function view_deal_report($id='',$type=''){
		if (!has_permission('reports', '', 'view')) {
            access_denied('reports');
        }
		$type = ($type=='')?'deal':$type;
		if(empty($type) || ( $type!='deal' && $type!='activity')){
			redirect(admin_url());
		}
		$data = array();
		$data['id']		  =	 $id;
		$data['type']	  =	 $type;
		$folder = $this->db->query("SELECT folder FROM " . db_prefix() . "folder WHERE id = '".$id."' and folder_type = '".$type."' ")->row();
		$data['title']    =  _l('view_report_'.$type).' Of '.$folder->folder;
		$this->load->view('admin/reports/report_deal', $data);
	}
	
	public function delete_report($id,$type=''){
		$type = ($type=='')?'deal':$type;
		if(empty($type) || ( $type!='deal' && $type!='activity')){
			redirect(admin_url());
		}
		$reports =  $this->db->query("SELECT folder_id FROM " . db_prefix() . "report WHERE id = '".$id."'")->result_array();
		$folder = $reports[0]['folder_id'];
		if(empty($folder)){
			redirect(admin_url());
			exit;
		}
		$table = db_prefix() . 'report_filter';
		$cond = array('report_id'=>$id);
		$this->db->where($cond);
		$result = $this->db->delete($table);
		
		$table = db_prefix() . 'report';
		$cond = array('id'=>$id);
		$this->db->where($cond);
		$result = $this->db->delete($table);
		
		$table = db_prefix() . 'shared';
		$cond = array('report_id'=>$id);
		$this->db->where($cond);
		$result = $this->db->delete($table);
		set_alert('success', _l('deleted_successfully', _l('report')));
		redirect(admin_url('reports/view_deal_report/'.$folder.'/'.$type));
	}
	public function folder_deal_view(){
		if (!has_permission('reports', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data('folder_deal_view');
	}
	
	public function report_deal_view($id,$type=''){
		if (!has_permission('reports', '', 'view')) {
            ajax_access_denied();
        }
		$data['id'] = $id;
		$data['type'] = $type;
        $this->app->get_table_data('report_deal_view',$data);
	}
    /* deals reportts */
    public function activities()
    {
        $data['mysqlVersion'] = $this->db->query('SELECT VERSION() as version')->row();
        $data['sqlMode']      = $this->db->query('SELECT @@sql_mode as mode')->row();

        $this->load->model('pipeline_model');
		$data['pipelines'] = $this->pipeline_model->getPipeline();
        if(!is_admin(get_staff_user_id())) {
            $my_staffids = $this->staff_model->get_my_staffids();
            if($my_staffids) {
                $staffdetails =  $this->db->query('SELECT staffid as id, CONCAT(firstname," ",lastname) as name FROM ' . db_prefix() . 'staff WHERE staffid in (' . implode(',',$my_staffids) . ')')->result_array();
            } else {
                $staffdetails =  $this->db->query('SELECT staffid as id, CONCAT(firstname," ",lastname) as name FROM ' . db_prefix() . 'staff WHERE staffid in (' . implode(',',$my_staffids) . ')')->result_array();
            }
            $data['teammembers'] =  $staffdetails;
        } else {
            $data['teammembers'] = $this->pipeline_model->getTeammembers();
        }
        $this->load->model('tasktype_model');
        $data['tasktypes'] = $this->tasktype_model->getTasktypes();
		
		$fields = get_option('deal_fields');
		$data['need_fields'] = array();
		if(!empty($fields) && $fields != 'null'){
			$data['need_fields'] = json_decode($fields);
		}
		if(!empty($data['need_fields']) && in_array("pipeline_id", $data['need_fields']) ){
			$data['pipelines'] = $this->pipeline_model->getPipeline();
		}else{
			$default_pipeline = get_option('default_pipeline');
			$data['pipelines'] = $this->pipeline_model->getpipelinebyIdarray($default_pipeline);
		}

        $data['title'] = _l('activities_reports');
        $this->load->view('admin/reports/activities', $data);
    }
    /* Customer report */
    public function customers_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $select = [
                get_sql_select_client_company(),
                '(SELECT COUNT(clientid) FROM ' . db_prefix() . 'invoices WHERE ' . db_prefix() . 'invoices.clientid = ' . db_prefix() . 'clients.userid AND status != 5)',
                '(SELECT SUM(subtotal) - SUM(discount_total) FROM ' . db_prefix() . 'invoices WHERE ' . db_prefix() . 'invoices.clientid = ' . db_prefix() . 'clients.userid AND status != 5)',
                '(SELECT SUM(total) FROM ' . db_prefix() . 'invoices WHERE ' . db_prefix() . 'invoices.clientid = ' . db_prefix() . 'clients.userid AND status != 5)',
            ];

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                $i = 0;
                foreach ($select as $_select) {
                    if ($i !== 0) {
                        $_temp = substr($_select, 0, -1);
                        $_temp .= ' ' . $custom_date_select . ')';
                        $select[$i] = $_temp;
                    }
                    $i++;
                }
            }
            $by_currency = $this->input->post('report_currency');
            $currency    = $this->currencies_model->get_base_currency();
            if ($by_currency) {
                $i = 0;
                foreach ($select as $_select) {
                    if ($i !== 0) {
                        $_temp = substr($_select, 0, -1);
                        $_temp .= ' AND currency =' . $by_currency . ')';
                        $select[$i] = $_temp;
                    }
                    $i++;
                }
                $currency = $this->currencies_model->get($by_currency);
            }
            $aColumns     = $select;
            $sIndexColumn = 'userid';
            $sTable       = db_prefix() . 'clients';
            $where        = [];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], $where, [
                'userid',
            ]);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            $x       = 0;
            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($i == 0) {
                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                    } elseif ($aColumns[$i] == $select[2] || $aColumns[$i] == $select[3]) {
                        if ($_data == null) {
                            $_data = 0;
                        }
                        $_data = app_format_money($_data, $currency->name);
                    }
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
            echo json_encode($output);
            die();
        }
    }

    public function payments_received()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('payment_modes_model');
            $payment_gateways = $this->payment_modes_model->get_payment_gateways(true);
            $select           = [
                db_prefix() . 'invoicepaymentrecords.id',
                db_prefix() . 'invoicepaymentrecords.date',
                'invoiceid',
                get_sql_select_client_company(),
                'paymentmode',
                'transactionid',
                'note',
                'amount',
            ];
            $where = [
                'AND status != 5',
            ];

            $custom_date_select = $this->get_where_report_period(db_prefix() . 'invoicepaymentrecords.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'invoicepaymentrecords';
            $join         = [
                'JOIN ' . db_prefix() . 'invoices ON ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid',
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'invoices.clientid',
                'LEFT JOIN ' . db_prefix() . 'payment_modes ON ' . db_prefix() . 'payment_modes.id = ' . db_prefix() . 'invoicepaymentrecords.paymentmode',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'number',
                'clientid',
                db_prefix() . 'payment_modes.name',
                db_prefix() . 'payment_modes.id as paymentmodeid',
                'paymentmethod',
                'deleted_customer_name',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data['total_amount'] = 0;
            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($aColumns[$i] == 'paymentmode') {
                        $_data = $aRow['name'];
                        if (is_null($aRow['paymentmodeid'])) {
                            foreach ($payment_gateways as $gateway) {
                                if ($aRow['paymentmode'] == $gateway['id']) {
                                    $_data = $gateway['name'];
                                }
                            }
                        }
                        if (!empty($aRow['paymentmethod'])) {
                            $_data .= ' - ' . $aRow['paymentmethod'];
                        }
                    } elseif ($aColumns[$i] == db_prefix() . 'invoicepaymentrecords.id') {
                        $_data = '<a href="' . admin_url('payments/payment/' . $_data) . '" target="_blank">' . $_data . '</a>';
                    } elseif ($aColumns[$i] == db_prefix() . 'invoicepaymentrecords.date') {
                        $_data = _d($_data);
                    } elseif ($aColumns[$i] == 'invoiceid') {
                        $_data = '<a href="' . admin_url('invoices/list_invoices/' . $aRow[$aColumns[$i]]) . '" target="_blank">' . format_invoice_number($aRow['invoiceid']) . '</a>';
                    } elseif ($i == 3) {
                        if (empty($aRow['deleted_customer_name'])) {
                            $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                        } else {
                            $row[] = $aRow['deleted_customer_name'];
                        }
                    } elseif ($aColumns[$i] == 'amount') {
                        $footer_data['total_amount'] += $_data;
                        $_data = app_format_money($_data, $currency->name);
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            $footer_data['total_amount'] = app_format_money($footer_data['total_amount'], $currency->name);
            $output['sums']              = $footer_data;
            echo json_encode($output);
            die();
        }
    }


    public function activities_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('tasktype_model');
            
            

            $where              = [];
            $custom_date_select = $this->get_where_report_period('startdate');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $type_reports = $this->input->post('type_reports');
            if($type_reports == 'activities-added'){
                array_push($where, 'AND '.db_prefix() . 'tasks.status != 5');
            }else{
                array_push($where, 'AND '.db_prefix() . 'tasks.status = 5');
            }
            array_push($where, ' AND '.db_prefix().'tasks.rel_type = "project"');
            if ($this->input->post('teamleader')) {
                $teamleader  = $this->input->post('teamleader');
				array_push($where, 'AND '.db_prefix() . 'task_assigned.staffid IN (' . $teamleader . ')');
            } else {
                if(!is_admin(get_staff_user_id())) {
                    $my_staffids = $this->staff_model->get_my_staffids();
                    if($my_staffids) {
                        array_push($where, 'AND '.db_prefix() . 'task_assigned.staffid IN (' . implode(',',$my_staffids) . ')');
                    } else {
                        array_push($where, 'AND '.db_prefix() . 'task_assigned.staffid IN (' . get_staff_user_id() . ')');
                    }
                }
            }
            
            

            if ($this->input->post('pipeline_id')) {
                $pipeline_id  = $this->input->post('pipeline_id');
				array_push($where, 'AND '.db_prefix() . 'projects.pipeline_id IN (' . $pipeline_id . ')');
            }



            $activities_based_by = $this->input->post('activities_based_by');
            
            if($activities_based_by == 'name'){
                $tasktypes = $this->tasktype_model->getTasktypes();
                $select = [
                    "CONCAT(".db_prefix().'staff.firstname," ",'.db_prefix().'staff.lastname) as name',
                    'count('.db_prefix().'tasks.id) as count'
                ];
                foreach($tasktypes as $v){
                    $select[] = 'sum(IF('.db_prefix().'tasks.tasktype = "'.$v['id'].'", 1, 0)) as `'.$v['id'].'`';
                }
                $select[] =	db_prefix().'tasks.id as id' ;
                $select[] =	db_prefix() . 'projects.teamleader as teamleader';
                $select[] =	db_prefix() . 'task_assigned.staffid as staffid';
            }else{
                $ssselect = implode('  ',$where);
                $total_count =  $this->db->query('SELECT count(tbltasks.id) as count FROM ' . db_prefix() . 'tasks JOIN tblprojects JOIN tbltask_assigned  WHERE  1 ' . $ssselect . ' ')->row()->count;
                $select = [
                    db_prefix().'tasktype.name as name',
                    'count('.db_prefix().'tasks.id) as count',
                    '((count('.db_prefix().'tasks.id)/'.$total_count.') * 100 ) as percentage',
                    db_prefix().'tasktype.id as task_type_id',
                    '0 as staffid',
                ];
            }
			
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'tasks';
            $join         = [];
			array_push($join, ' LEFT JOIN '.db_prefix().'tasktype  as '.db_prefix().'tasktype ON '.db_prefix().'tasktype.id = ' .db_prefix() . 'tasks.tasktype');
			array_push($join, ' LEFT JOIN '.db_prefix().'projects  as '.db_prefix().'projects ON '.db_prefix().'tasks.rel_id = ' .db_prefix() . 'projects.id AND '.db_prefix().'tasks.rel_type = "project"');
			array_push($join, ' LEFT JOIN '.db_prefix().'task_assigned  as '.db_prefix().'task_assigned ON '.db_prefix().'tasks.id = ' .db_prefix() . 'task_assigned.taskid');
			array_push($join, ' LEFT JOIN '.db_prefix().'staff  as '.db_prefix().'staff ON '.db_prefix().'staff.staffid = ' .db_prefix() . 'task_assigned.staffid');
            
            $group_by = ' group by '.db_prefix() . 'task_assigned.staffid ';
            if($activities_based_by == 'type' || $activities_based_by == ''){
                $group_by = ' group by '.db_prefix() . 'tasks.tasktype ';
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [],$group_by);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            
            foreach ($rResult as $aRow) {
                $row = [];
                $row[] = $aRow['name'];
                if($activities_based_by == 'name'){
                    $row[] = '<a class="btn btn-link" onclick="init_ac_details(event,'.$aRow['staffid'].',\'user\',\'all\','.$aRow['count'].',\''.$type_reports.'\')">
        '.$aRow['count'].'
    </a><div class="hide divdwdr  panel-body" id="dropdownacd_'.$aRow['staffid'].'_user_all_'.$type_reports.'">'.$this->get_task_details_reports_table($aRow,'user','all',$type_reports).'</div>';
                    foreach($tasktypes as $v){
                    $row[] = '<a class="btn btn-link" onclick="init_ac_details(event,'.$aRow['staffid'].',\'user\','.$v['id'].','.$aRow[$v['id']].',\''.$type_reports.'\')">
        '.$aRow[$v['id']].'
    </a><div class="hide divdwdr  panel-body" id="dropdownacd_'.$aRow['staffid'].'_user_'.$v['id'].'_'.$type_reports.'">'.$this->get_task_details_reports_table($aRow,'user',$v['id'],$type_reports).'</div>';
                    }
                }else{

                    $row[] = '<a class="btn btn-link" onclick="init_ac_details(event,'.$aRow['staffid'].',\'type\','.$aRow['task_type_id'].','.$aRow['count'].',\''.$type_reports.'\')">
        '.$aRow['count'].'
    </a><div class="hide divdwdr  panel-body" id="dropdownacd_'.$aRow['staffid'].'_type_'.$aRow['task_type_id'].'_'.$type_reports.'">'.$this->get_task_details_reports_table($aRow,'type',$aRow['task_type_id'],$type_reports).'</div>';
                    

                     $row[] = round($aRow['percentage'],2);
                }
				
                $output['aaData'][] = $row;
            }

          
            echo json_encode($output);
            die();
        }
    }

    
	public function get_task_details_reports_table($aRow,$by = 'user',$all = 'all',$type_reports = ''){
        return '<div id="ac_details_reports_'.$aRow['staffid'].'_'.$by.'_'.$all.'_'.$type_reports.'">
   <table class="table ac_details ac_details_reports_'.$aRow['staffid'].'_'.$by.'_'.$all.'_'.$type_reports.' scroll-responsive">
      <thead>
         <tr>
            <th>'. _l('tasks_dt_name').'</th>
            <th>'. _l('task_status').'</th>
            <th>'. _l('tasks_dt_datestart').'</th>
            <th>'. _l('project_name').'</th>
            <th>'. _l('project_status').'</th>
			<th>'._l('client').'</th>
			<th>'. _l('contacts').'</th>
			<th>'. _l('tasks_list_priority').'</th>
         </tr>
      </thead>
      <tbody></tbody>
   </table>
</div>';
    }

    public function activities_detail_report($user = '',$by='user',$all='all')
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('tasktype_model');
            $tasktypes = $this->tasktype_model->getTasktypes();
            $select = [
                db_prefix() . 'tasks.name as task_name',
                db_prefix() .'tasks.status as status',
                'startdate', 
                db_prefix() . 'projects.name as project_name',
                db_prefix() . 'projects_status.name as project_status',
                db_prefix() . 'clients.company as company',
                '(SELECT GROUP_CONCAT(CONCAT(\' <a  href="' . admin_url('clients/view_contact/' ) . '\',' . db_prefix() . 'contacts.id ,\'"> \',' . db_prefix() . 'contacts.firstname, \' \', ' . db_prefix() . 'contacts.lastname,\' </a> \') SEPARATOR ", ") FROM ' . db_prefix() . 'contacts JOIN ' . db_prefix() . 'project_contacts on ' . db_prefix() . 'project_contacts.contacts_id = ' . db_prefix() . 'contacts.id WHERE ' . db_prefix() . 'project_contacts.project_id=' . db_prefix() . 'projects.id ORDER BY ' . db_prefix() . 'contacts.id) as project_contacts',
                db_prefix() . 'projects.teamleader as teamleader',
				db_prefix().'tasks.priority as priority' ,
				db_prefix().'tasks.id as id' ,
				db_prefix().'tasks.rel_id as rel_id' ,
                db_prefix().'tasks.rel_type as rel_type' ,
                db_prefix().'clients.userid as cuserid',
            ];
            
            

            $where              = [];
            $custom_date_select = $this->get_where_report_period('startdate');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            array_push($where, ' AND '.db_prefix().'tasks.rel_type = "project"');
            if(!empty($user) && $user != 0){
                array_push($where, ' AND ' .db_prefix() . 'task_assigned.staffid = '.$user);
            }
            $type_reports = $this->input->post('type_reports');
            if($type_reports == 'activities-added'){
                array_push($where, 'AND '.db_prefix() . 'tasks.status != 5');
            }else{
                array_push($where, 'AND '.db_prefix() . 'tasks.status = 5');
            }
            
            if(!empty($all) && $all != 'all'){
                array_push($where, ' AND '.db_prefix().'tasks.tasktype  = '.$all);
            }
            
            if ($this->input->post('teamleader')) {
                $teamleader  = $this->input->post('teamleader');
				array_push($where, 'AND '.db_prefix() . 'task_assigned.staffid IN (' . $teamleader . ')');
            } else {
                if(!is_admin(get_staff_user_id())) {
                    $my_staffids = $this->staff_model->get_my_staffids();
                    if($my_staffids) {
                        array_push($where, 'AND '.db_prefix() . 'task_assigned.staffid IN (' . implode(',',$my_staffids) . ')');
                    } else {
                        array_push($where, 'AND '.db_prefix() . 'task_assigned.staffid IN (' . get_staff_user_id() . ')');
                    }
                }
            }

            if ($this->input->post('pipeline_id')) {
                $pipeline_id  = $this->input->post('pipeline_id');
				array_push($where, 'AND '.db_prefix() . 'projects.pipeline_id IN (' . $pipeline_id . ')');
            }

            
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'tasks';
            $join         = [];
			array_push($join, ' LEFT JOIN '.db_prefix().'projects  as '.db_prefix().'projects ON '.db_prefix().'tasks.rel_id = ' .db_prefix() . 'projects.id AND '.db_prefix().'tasks.rel_type = "project"');
			array_push($join, ' LEFT JOIN '.db_prefix().'task_assigned  as '.db_prefix().'task_assigned ON '.db_prefix().'tasks.id = ' .db_prefix() . 'task_assigned.taskid');
			array_push($join, ' LEFT JOIN '.db_prefix().'staff  as '.db_prefix().'staff ON '.db_prefix().'staff.staffid = ' .db_prefix() . 'task_assigned.staffid');
            array_push($join, 'LEFT JOIN '.db_prefix().'projects_status  as '.db_prefix().'projects_status ON '.db_prefix().'projects_status.id = ' .db_prefix() . 'projects.status');
            array_push($join, 'LEFT JOIN '.db_prefix().'clients  as '.db_prefix().'clients ON '.db_prefix().'clients.userid = ' .db_prefix() . 'projects.clientid');
            array_push($join, 'LEFT JOIN '.db_prefix().'contacts  as '.db_prefix().'contacts ON '.db_prefix().'contacts.id = ' .db_prefix() . 'tasks.contacts_id');
            
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];
                $row[] = '<a href="#" class="display-block main-tasks-table-href-name' . (!empty($aRow['rel_id']) ? ' mbot5' : '') . '" onclick="edit_task(' . $aRow['id'] . '); return false;">' . $aRow['task_name'] . '</a>';
                $status          = get_task_status_by_id($aRow['status']);
				$row[] = $status['name'];
                $row[] = _d($aRow['startdate']);
                $link = task_rel_link($aRow['rel_id'], $aRow['rel_type']);
				$row[] = '<a class="task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . $link . '">' . $aRow['project_name'] . '</a>';
				$row[] = $aRow['project_status'];
				$row[] = '<a class="task-table-related" data-toggle="tooltip" title="' . _l('company') . '" href="' . admin_url("clients/client/".$aRow['cuserid']) . '">' . $aRow['company'] . '</a>';
				$row[] = $aRow['project_contacts'];
				$row[] = task_priority($aRow['priority']);
                $output['aaData'][] = $row;
            }

          
            echo json_encode($output);
            die();
        }
    }

    public function deals_wons_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $select = [
                "CONCAT(".db_prefix().'staff.firstname," ",'.db_prefix().'staff.lastname) as name',
                'count('.db_prefix().'projects.id) as count',
                'sum(project_cost) as sum',
                'avg(project_cost) as avg',
                'avg(DATEDIFF(stage_on, start_date)) as dayavg',
				'id',
				db_prefix() . 'projects.teamleader as teamleader',
            ];

            

            $where              = [];
            $custom_date_select = $this->get_where_report_period('stage_on');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            array_push($where, ' AND stage_of = 1');
            if ($this->input->post('teamleader')) {
                $teamleader  = $this->input->post('teamleader');
				array_push($where, 'AND teamleader IN (' . $teamleader . ')');
            } else {
                if(!is_admin(get_staff_user_id())) {
                    $my_staffids = $this->staff_model->get_my_staffids();
                    if($my_staffids) {
                        array_push($where, 'AND teamleader in (' . implode(',',$my_staffids) . ')');
                    } else {
                        array_push($where, 'AND teamleader IN (' . get_staff_user_id() . ')');
                    }
                }
            }

            
            if ($this->input->post('pipeline_id')) {
                $pipeline_id  = $this->input->post('pipeline_id');
				array_push($where, 'AND pipeline_id IN (' . $pipeline_id . ')');
            }
            array_push($where, 'AND tblprojects.deleted_status = 0');
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'projects';
            $join         = [];
			array_push($join, ' LEFT JOIN '.db_prefix().'staff  as '.db_prefix().'staff ON '.db_prefix().'staff.staffid = ' .db_prefix() . 'projects.teamleader');
			
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [],' group by teamleader ');
            $output  = $result['output'];
            $rResult = $result['rResult'];

			$currency = $this->currencies_model->get_base_currency();
            foreach ($rResult as $aRow) {
                $row = [];
				$row[] = $aRow['name'];
				$row[] = '<a class="btn btn-link" onclick="init_dealsw_details(event,'.$aRow['teamleader'].','.$aRow['count'].')">
    '.$aRow['count'].'
  </a><div class="hide divdwdr  panel-body" id="dropdowndeald_'.$aRow['teamleader'].'">'.$this->get_deals_wons_details_reports_table($aRow).'</div>';
				$row[] = app_format_money($aRow['sum'],$currency);
				$row[] = app_format_money($aRow['avg'],$currency);
				$row[] = round($aRow['dayavg'],1);
				
                $output['aaData'][] = $row;
            }

          
            echo json_encode($output);
            die();
        }
    }
	
	public function get_deals_wons_details_reports_table($aRow){
        return '<div id="deals_wons_details_reports_'.$aRow['teamleader'].'">
   <table class="table deals_wons_details deals_wons_details_reports_'.$aRow['teamleader'].' scroll-responsive">
      <thead>
         <tr>
            <th>'. _l("Deal Name").'</th>
            <th>'.  _l("Deal Value").'</th>
            <th>'.  _l("Organisation").'</th>
            <th>'. _l("Contact Person").'</th>
            <th>'.  _l("Total Activities").'</th>
			<th>'.  _l("Won Date").'</th>
			<th>'.  _l("Owner").'</th>
         </tr>
      </thead>
      <tbody></tbody>
   </table>
</div>';
    }
	public function deals_wons_detail_report($teamleader = 0){
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $select = [
                db_prefix() . 'projects.name as name',
                db_prefix() . 'projects.project_cost as project_cost',
                db_prefix() . 'clients.company as organisation',
                '(SELECT GROUP_CONCAT(CONCAT(\' <a  href="' . admin_url('clients/view_contact/' ) . '\',' . db_prefix() . 'contacts.id ,\'"> \',' . db_prefix() . 'contacts.firstname, \' \', ' . db_prefix() . 'contacts.lastname,\' </a> \') SEPARATOR ", ") FROM ' . db_prefix() . 'contacts JOIN ' . db_prefix() . 'project_contacts on ' . db_prefix() . 'project_contacts.contacts_id = ' . db_prefix() . 'contacts.id WHERE ' . db_prefix() . 'project_contacts.project_id=' . db_prefix() . 'projects.id ORDER BY ' . db_prefix() . 'contacts.id) as contact_person',
                '(SELECT count(id) FROM ' . db_prefix() . 'tasks WHERE rel_type = "project" AND rel_id=' . db_prefix() . 'projects.id) as total_activities',
                db_prefix() . 'projects.stage_on as stage_on',
                "CONCAT(".db_prefix().'staff.firstname," ",'.db_prefix().'staff.lastname) as owner_name',
				db_prefix() . 'projects.id',
				db_prefix() . 'projects.id as id',
				db_prefix() . 'projects.userid',
				db_prefix() . 'projects.clientid as clientid',
				db_prefix() . 'projects.teamleader as teamleader',
            ];

            

            $where              = [];
            $custom_date_select = $this->get_where_report_period('stage_on');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            array_push($where, ' AND teamleader = '.$teamleader);
            array_push($where, ' AND stage_of = 1');
            if ($this->input->post('teamleader')) {
                $teamleader  = $this->input->post('teamleader');
				array_push($where, 'AND teamleader IN (' . $teamleader . ')');
            } else {
                if(!is_admin(get_staff_user_id())) {
                    $my_staffids = $this->staff_model->get_my_staffids();
                    if($my_staffids) {
                        array_push($where, 'AND teamleader in (' . implode(',',$my_staffids) . ')');
                    } else {
                        array_push($where, 'AND teamleader IN (' . get_staff_user_id() . ')');
                    }
                }
            }

            if ($this->input->post('pipeline_id')) {
                $pipeline_id  = $this->input->post('pipeline_id');
				array_push($where, 'AND pipeline_id IN (' . $pipeline_id . ')');
            }

            array_push($where, 'AND tblprojects.deleted_status = 0');

            $aColumns     = $select;
            $sIndexColumn =  'id';
            $sTable       = db_prefix() . 'projects';
            $join         = [];
            array_push($join, ' LEFT JOIN '.db_prefix().'staff  as '.db_prefix().'staff ON '.db_prefix().'staff.staffid = ' .db_prefix() . 'projects.teamleader');
            array_push($join, ' LEFT JOIN '.db_prefix().'clients  as '.db_prefix().'clients ON '.db_prefix().'clients.userid = ' .db_prefix() . 'projects.clientid');
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            $currency = $this->currencies_model->get_base_currency();
            foreach ($rResult as $aRow) {
                $row = [];

                $link = admin_url('projects/view/' . $aRow['id']);
                $row[] = '<a href="' . $link . '">' . $aRow['name'] . '</a>';

                $row[] = app_format_money($aRow['project_cost'],$currency);

                $link = admin_url('clients/client/' . $aRow['clientid']);
                $row[] = '<a href="' . $link . '">' .$aRow['organisation']. '</a>';
                
				$row[] = $aRow['contact_person'];
				$row[] = $aRow['total_activities'];
				$row[] = $aRow['stage_on'];
				$row[] = $aRow['owner_name'];
				
				
                $output['aaData'][] = $row;
            }

          
            echo json_encode($output);
            die();
        }

    }


    
    public function deals_loss_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $select = [
                "CONCAT(".db_prefix().'staff.firstname," ",'.db_prefix().'staff.lastname) as name',
                db_prefix().'projects_status.name as Stages',
                db_prefix().'deallossreasons.name as Reasons',
                'count('.db_prefix().'projects.id) as count',
                'sum(project_cost) as sum',
                'avg(project_cost) as avg',
                'avg(DATEDIFF(stage_on, start_date)) as dayavg',
				db_prefix() . 'projects.id as id',
				db_prefix() . 'projects.teamleader as teamleader',
				db_prefix() . 'projects.loss_reason as loss_reason',
				db_prefix() . 'projects.status as status',
            ];

            

            $where              = [];
            $custom_date_select = $this->get_where_report_period('stage_on');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            array_push($where, ' AND stage_of = 2');
            if ($this->input->post('teamleader')) {
                $teamleader  = $this->input->post('teamleader');
				array_push($where, 'AND teamleader IN (' . $teamleader . ')');
            } else {
                if(!is_admin(get_staff_user_id())) {
                    $my_staffids = $this->staff_model->get_my_staffids();
                    if($my_staffids) {
                        array_push($where, 'AND teamleader in (' . implode(',',$my_staffids) . ')');
                    } else {
                        array_push($where, 'AND teamleader IN (' . get_staff_user_id() . ')');
                    }
                }
            }

            

            if ($this->input->post('pipeline_id')) {
                $pipeline_id  = $this->input->post('pipeline_id');
				array_push($where, 'AND pipeline_id IN (' . $pipeline_id . ')');
            }

            array_push($where, 'AND tblprojects.deleted_status = 0');

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'projects';
            $join         = [];
			array_push($join, ' LEFT JOIN '.db_prefix().'staff  as '.db_prefix().'staff ON '.db_prefix().'staff.staffid = ' .db_prefix() . 'projects.teamleader');
			array_push($join, ' LEFT JOIN '.db_prefix().'deallossreasons  as '.db_prefix().'deallossreasons ON '.db_prefix().'deallossreasons.id = ' .db_prefix() . 'projects.loss_reason');
			array_push($join, ' LEFT JOIN '.db_prefix().'projects_status  as '.db_prefix().'projects_status ON '.db_prefix().'projects_status.id = ' .db_prefix() . 'projects.status');
            $by_group_by = ' group by teamleader ';
            $deals_loss_by = $this->input->post('deals_loss_by');
            if ($deals_loss_by && $deals_loss_by == 'status') {
                 $by_group_by = ' group by '.db_prefix() . 'projects.status ';
            }elseif ($deals_loss_by && $deals_loss_by == 'loss_reason') {
                 $by_group_by = ' group by loss_reason ';
            }
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], $by_group_by);
            $output  = $result['output'];
            $rResult = $result['rResult'];

			$currency = $this->currencies_model->get_base_currency();
            foreach ($rResult as $aRow) {
                $row = [];
                
				$row[] = $aRow['name'];
				$row[] = $aRow['Stages'];
				$row[] = $aRow['Reasons'];
				$row[] = '<a class="btn btn-link" onclick="init_dealsl_details(event,'.$aRow['teamleader'].','.$aRow['status'].','.$aRow['loss_reason'].','.$aRow['count'].')">
    '.$aRow['count'].'
  </a><div class="hide divdldr  panel-body" id="dropdowndealdl_'.$aRow['teamleader'].'_'.$aRow['status'].'_'.$aRow['loss_reason'].'">'.$this->get_deals_loss_details_reports_table($aRow).'</div>';
				$row[] = app_format_money($aRow['sum'],$currency);
				$row[] = app_format_money($aRow['avg'],$currency);
				$row[] = round($aRow['dayavg'],1);
				
                $output['aaData'][] = $row;
            }

          
            echo json_encode($output);
            die();
        }
    }
	
	public function get_deals_loss_details_reports_table($aRow){
        return '<div id="deals_loss_details_reports_'.$aRow['teamleader'].'_'.$aRow['status'].'_'.$aRow['loss_reason'].'">
   <table class="table deals_loss_details deals_loss_details_reports_'.$aRow['teamleader'].'_'.$aRow['status'].'_'.$aRow['loss_reason'].' scroll-responsive">
      <thead>
         <tr>
            <th>'. _l("Deal Name").'</th>
            <th>'.  _l("Deal Value").'</th>
            <th>'.  _l("Organisation").'</th>
            <th>'. _l("Contact Person").'</th>
            <th>'.  _l("Total Activities").'</th>
			<th>'.  _l("Lost Date").'</th>
			<th>'.  _l("Owner").'</th>
			<th>'.  _l("Stages").'</th>
			<th>'.  _l("Lost Reason").'</th>
         </tr>
      </thead>
      <tbody></tbody>
   </table>
</div>';
    }

    public function deals_loss_detail_report($teamleader = 0,$status = 0,$loss_reason = 0){
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $select = [
                db_prefix() . 'projects.name as name',
                db_prefix() . 'projects.project_cost as project_cost',
                db_prefix() . 'clients.company as organisation',
                '(SELECT GROUP_CONCAT(CONCAT(\' <a  href="' . admin_url('clients/view_contact/' ) . '\',' . db_prefix() . 'contacts.id ,\'"> \',' . db_prefix() . 'contacts.firstname, \' \', ' . db_prefix() . 'contacts.lastname,\' </a> \') SEPARATOR ", ") FROM ' . db_prefix() . 'contacts JOIN ' . db_prefix() . 'project_contacts on ' . db_prefix() . 'project_contacts.contacts_id = ' . db_prefix() . 'contacts.id WHERE ' . db_prefix() . 'project_contacts.project_id=' . db_prefix() . 'projects.id ORDER BY ' . db_prefix() . 'contacts.id) as contact_person',
                '(SELECT count(id) FROM ' . db_prefix() . 'tasks WHERE rel_type = "project" AND rel_id=' . db_prefix() . 'projects.id) as total_activities',
                db_prefix() . 'projects.stage_on as stage_on',
                "CONCAT(".db_prefix().'staff.firstname," ",'.db_prefix().'staff.lastname) as owner_name',
                 db_prefix().'projects_status.name as Stages',
				db_prefix().'deallossreasons.name as Reasons',
				db_prefix() . 'projects.id',
				db_prefix() . 'projects.id as id',
				db_prefix() . 'projects.userid',
				db_prefix() . 'projects.clientid as clientid',
				db_prefix() . 'projects.teamleader as teamleader',
            ];

            

            $where              = [];
            $custom_date_select = $this->get_where_report_period('stage_on');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $deals_loss_by = $this->input->post('deals_loss_by');
            if ($deals_loss_by && $deals_loss_by == 'status') {
                array_push($where, ' AND '.db_prefix() . 'projects.status = '.$status);
            }elseif ($deals_loss_by && $deals_loss_by == 'loss_reason') {
                array_push($where, ' AND loss_reason = '.$loss_reason);
            }else{
                array_push($where, ' AND teamleader = '.$teamleader);
            }
            
            array_push($where, ' AND stage_of = 2');
            if ($this->input->post('teamleader')) {
                $teamleader  = $this->input->post('teamleader');
				array_push($where, 'AND teamleader IN (' . $teamleader . ')');
            } else {
                if(!is_admin(get_staff_user_id())) {
                    $my_staffids = $this->staff_model->get_my_staffids();
                    if($my_staffids) {
                        array_push($where, 'AND teamleader in (' . implode(',',$my_staffids) . ')');
                    } else {
                        array_push($where, 'AND teamleader IN (' . get_staff_user_id() . ')');
                    }
                }
            }

            if ($this->input->post('pipeline_id')) {
                $pipeline_id  = $this->input->post('pipeline_id');
				array_push($where, 'AND pipeline_id IN (' . $pipeline_id . ')');
            }

            array_push($where, 'AND tblprojects.deleted_status = 0');

            $aColumns     = $select;
            $sIndexColumn =  'id';
            $sTable       = db_prefix() . 'projects';
            $join         = [];
            array_push($join, ' LEFT JOIN '.db_prefix().'staff  as '.db_prefix().'staff ON '.db_prefix().'staff.staffid = ' .db_prefix() . 'projects.teamleader');
            array_push($join, ' LEFT JOIN '.db_prefix().'clients  as '.db_prefix().'clients ON '.db_prefix().'clients.userid = ' .db_prefix() . 'projects.clientid');
            array_push($join, ' LEFT JOIN '.db_prefix().'projects_status  as '.db_prefix().'projects_status ON '.db_prefix().'projects_status.id = ' .db_prefix() . 'projects.status');
            array_push($join, ' LEFT JOIN '.db_prefix().'deallossreasons  as '.db_prefix().'deallossreasons ON '.db_prefix().'deallossreasons.id = ' .db_prefix() . 'projects.loss_reason');
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            $currency = $this->currencies_model->get_base_currency();
            foreach ($rResult as $aRow) {
                $row = [];

                $link = admin_url('projects/view/' . $aRow['id']);
                $row[] = '<a href="' . $link . '">' . $aRow['name'] . '</a>';

                $row[] = app_format_money($aRow['project_cost'],$currency);

                $link = admin_url('clients/client/' . $aRow['clientid']);
                $row[] = '<a href="' . $link . '">' .$aRow['organisation']. '</a>';
                
				$row[] = $aRow['contact_person'];
				$row[] = $aRow['total_activities'];
				$row[] = $aRow['stage_on'];
				$row[] = $aRow['owner_name'];
				$row[] = $aRow['Stages'];
				$row[] = $aRow['Reasons'];
				
				
                $output['aaData'][] = $row;
            }

          
            echo json_encode($output);
            die();
        }

    }


    private function get_where_report_period_month($field = 'date')
    {
        $months_report      = $this->input->post('report_months');
        $custom_date_select = '';
        $mo_date = array();
        if ($months_report != '') {
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth   = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int) $months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth   = date('Y-m-t');
                }
                $mo_date['s'] = $beginMonth;
                $mo_date['e'] = $beginMonth;
            } elseif ($months_report == 'today') {
                $mo_date['s'] = date('Y-m-d');
                $mo_date['e'] = date('Y-m-d');
            } elseif ($months_report == 'yesterday') {
                $mo_date['s'] = date('Y-m-d',strtotime("-1 days"));
                $mo_date['e'] = date('Y-m-d',strtotime("-1 days"));
            } elseif ($months_report == 'this_week') {
                $mo_date['s'] = date('Y-m-d',strtotime('monday this week'));
                $mo_date['e'] = date('Y-m-d',strtotime('sunday this week'));
            } elseif ($months_report == 'last_week') {
                $mo_date['s'] = date('Y-m-d',strtotime('monday this week',strtotime("-1 week +1 day")));
                $mo_date['e'] = date('Y-m-d',strtotime('sunday this week',strtotime("-1 week +1 day")));
            } elseif ($months_report == 'this_month') {
                $mo_date['s'] = date('Y-m-01');
                $mo_date['e'] = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $mo_date['s'] = date('Y-m-d', strtotime(date('Y-01-01')));
                $mo_date['e'] = date('Y-m-d', strtotime(date('Y-12-31')));

            } elseif ($months_report == 'last_year') {
                $mo_date['s'] = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $mo_date['e'] = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));

            } elseif ($months_report == 'custom') {
                $mo_date['s'] = to_sql_date($this->input->post('report_from'));
                $mo_date['e'] = to_sql_date($this->input->post('report_to'));
                
            }
        }else{
            $mo_date['s'] = date('Y-m-d', strtotime(date('Y-m-1',strtotime("-5 months"))));
            $mo_date['e'] = date('Y-m-d', strtotime(date('Y-m-t')));
        }

        return $mo_date;
    }

    
    
    public function get_list_all_months_between_two_dates(){
        $month_arr= $month_arr_temp= array();
        $mo_date = $this->get_where_report_period_month($field = 'date');
        $i = date("Ym", strtotime($mo_date['s']));
        while($i <= date("Ym", strtotime($mo_date['e']))){
            $month_arr_temp['text'] = date("M", strtotime($i."01"));
            $month_arr_temp['value'] = date("Y-m", strtotime($i."01"));
            if(substr($i, 4, 2) == "12")
                $i = (date("Y", strtotime($i."01")) + 1)."01";
            else
                $i++;
            $month_arr[] = $month_arr_temp;
        }
        return $month_arr;
    }
    
    public function deals_started_status_detail_report(){
        if ($this->input->is_ajax_request()) {
            $ma = $this->get_list_all_months_between_two_dates();

            $table = '<table class="table  dataTable"><thead><tr><th>'. _l("Status").'</th>';
            foreach($ma as $mak => $mav){
                $table .= '<th class="text-center">'. $mav['text'].'</th>';
            }
            $table .= '</tr></thead><tbody>';

            // Deals Started
            $table_Started = '<tbody><tr><td>'. _l("Deals Started").'</td>';
            // Won
            $table_Won = '<td>'. _l("Won").'</td>';
             // Open
            $table_Open = '<td>'. _l("Open").'</td>';
            // Lost
            $table_Lost = '<td>'. _l("Lost").'</td>';
            // Conversion to Won
            $table_cw = '<td>'. _l("Conversion to Won").' %</td>';
            // Conversion to Lost	
            $table_cl = '<td>'. _l("Conversion to Lost").' %</td>';


            $where              = [];
            $custom_date_select = $this->get_where_report_period('start_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            
            if ($this->input->post('teamleader')) {
                $teamleader  = $this->input->post('teamleader');
				array_push($where, 'AND teamleader IN (' . $teamleader . ')');
            } else {
                if(!is_admin(get_staff_user_id())) {
                    $my_staffids = $this->staff_model->get_my_staffids();
                    if($my_staffids) {
                        array_push($where, 'AND teamleader in (' . implode(',',$my_staffids) . ')');
                    } else {
                        array_push($where, 'AND teamleader IN (' . get_staff_user_id() . ')');
                    }
                }
            }

            if ($this->input->post('pipeline_id')) {
                $pipeline_id  = $this->input->post('pipeline_id');
				array_push($where, 'AND pipeline_id IN (' . $pipeline_id . ')');
            }

            $ssselect = implode(' ',$where);

            foreach($ma as $mak => $mav){
                $Started =  $this->db->query('SELECT count(id) as count FROM ' . db_prefix() . 'projects  WHERE 1 ' . $ssselect . ' and start_date like "'.$mav['value'].'%"')->row()->count;
                $Won =  $this->db->query('SELECT count(id) as count FROM ' . db_prefix() . 'projects  WHERE 1 and stage_of = 1 ' . $ssselect . ' and start_date like "'.$mav['value'].'%"')->row()->count;
                $Open =  $this->db->query('SELECT count(id) as count FROM ' . db_prefix() . 'projects  WHERE 1 and stage_of = 0  ' . $ssselect . ' and start_date like "'.$mav['value'].'%"')->row()->count;
                $Lost =  $this->db->query('SELECT count(id) as count FROM ' . db_prefix() . 'projects  WHERE 1 and stage_of = 2  ' . $ssselect . ' and start_date like "'.$mav['value'].'%"')->row()->count;
               $tex_val = "'".$mav['value']."'";

                $table_Started .= '<td class="nullh text-center">'.'<a class="btn btn-link" onclick="init_dealss_details(event,'.$tex_val.',\'Started\',\'all\','.$Started.')">'.$Started.'</a>
                <div class="hide divdsdr  panel-body" id="dropdowndealdl_'.$mav['value'].'_Started_all">'.$this->get_deals_started_status_details_reports_table($mav,'Started','all').'</div>'
  .'</td>';

                $table_Won .= '<td class="nullh text-center">'.'<a class="btn btn-link" onclick="init_dealss_details(event,'.$tex_val.',\'Won\',\'1\','.$Won.')">'.$Won.'</a>
                <div class="hide divdsdr  panel-body" id="dropdowndealdl_'.$mav['value'].'_Won_1">'.$this->get_deals_started_status_details_reports_table($mav,'Won','1').'</div>'
  .'</td>';
                $table_Open .= '<td class="nullh text-center">'.'<a class="btn btn-link" onclick="init_dealss_details(event,'.$tex_val.',\'Open\',\'0\','.$Open.')">'.$Open.'</a>
                <div class="hide divdsdr  panel-body" id="dropdowndealdl_'.$mav['value'].'_Open_0">'.$this->get_deals_started_status_details_reports_table($mav,'Open','0').'</div>'
  .'</td>';
                 $table_Lost .= '<td class="nullh text-center">'.'<a class="btn btn-link" onclick="init_dealss_details(event,'.$tex_val.',\'Lost\',\'2\','.$Open.')">'.$Lost.'</a>
                <div class="hide divdsdr  panel-body" id="dropdowndealdl_'.$mav['value'].'_Lost_2">'.$this->get_deals_started_status_details_reports_table($mav,'Lost','2').'</div>'
  .'</td>';
                $table_cw .= '<td class="text-center">'. round((($Won/($Started>0?$Started:1))*100),2).'</td>';
                $table_cl .= '<td class="text-center">'. round((($Lost/($Started>0?$Started:1))*100),2).'</td>';
            }
            $table_Started .= '</tr>';
            $table_Won .= '</tr>';
            $table_Open .= '</tr>';
            $table_Lost .= '</tr>';
            $table_cw .= '</tr>';
            $table_cl .= '</tr>';

            // Started
            $table .= $table_Started;
            // Won
            $table .= $table_Won;
            // Open
            $table .= $table_Open;
            // Lost
            $table .= $table_Lost;
            // Conversion to Won
            $table .=  $table_cw;
            // Conversion to Lost	
            $table .=  $table_cl;


            

            $table .= '</tbody></table>';
            echo $table;  
            exit();
        }
    }

    
	public function get_deals_started_status_details_reports_table($aRow,$is,$all){
        return '<div id="deals_started_details_reports_'.$aRow['value'].'_'.$is.'_'.$all.'">
   <table class="table deals_started_details deals_started_details_reports_'.$aRow['value'].'_'.$is.'_'.$all.' scroll-responsive">
      <thead>
         <tr>
            <th>'. _l("Deal Name").'</th>
            <th>'.  _l("Deal Value").'</th>
            <th>'.  _l("Organisation").'</th>
            <th>'. _l("Contact Person").'</th>
            <th>'.  _l("Total Activities").'</th>
			<th>'.  _l("Start Date").'</th>
			<th>'.  _l("Owner").'</th>
			<th>'.  _l("Stages").'</th>
			<th>'.  _l("Lost Reason").'</th>
         </tr>
      </thead>
      <tbody></tbody>
   </table>
</div>';
    }
    
    public function deals_started_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $where              = [];
            $custom_date_select = $this->get_where_report_period('start_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
                        

            if ($this->input->post('pipeline_id')) {
                $pipeline_id  = $this->input->post('pipeline_id');
				array_push($where, 'AND pipeline_id IN (' . $pipeline_id . ')');
            }
            
            if ($this->input->post('teamleader')) {
                $teamleader  = $this->input->post('teamleader');
				array_push($where, 'AND teamleader IN (' . $teamleader . ')');
            } else {
                if(!is_admin(get_staff_user_id())) {
                    $my_staffids = $this->staff_model->get_my_staffids();
                    if($my_staffids) {
                        array_push($where, 'AND teamleader in (' . implode(',',$my_staffids) . ')');
                    } else {
                        array_push($where, 'AND teamleader IN (' . get_staff_user_id() . ')');
                    }
                }
            }

            $ssselect = implode(' ',$where);
            $select = [
                "CONCAT(".db_prefix().'staff.firstname," ",'.db_prefix().'staff.lastname) as name',
                db_prefix().'projects_status.name as Stages',
                db_prefix().'deallossreasons.name as Reasons',
                'count('.db_prefix().'projects.id) as count',
                'sum(project_cost) as sum',
                '(SELECT sum(dfgdf.project_cost) FROM ' . db_prefix() . 'projects as dfgdf WHERE 1 ' . $ssselect . ' and ' . db_prefix() . 'projects.teamleader = dfgdf.teamleader) as avg',
                'avg(project_cost) as dayavg',
                '(SELECT count(dfgdf.id) FROM ' . db_prefix() . 'projects as dfgdf WHERE 1 ' . $ssselect . ' and ' . db_prefix() . 'projects.teamleader = dfgdf.teamleader) as dcc',
				db_prefix() . 'projects.id as id',
				db_prefix() . 'projects.teamleader as teamleader',
				db_prefix() . 'projects.loss_reason as loss_reason',
				db_prefix() . 'projects.status as status',
            ];

            

            array_push($where, ' AND stage_of = 0');
            array_push($where, 'AND tblprojects.deleted_status = 0');
            
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'projects';
            $join         = [];
			array_push($join, ' LEFT JOIN '.db_prefix().'staff  as '.db_prefix().'staff ON '.db_prefix().'staff.staffid = ' .db_prefix() . 'projects.teamleader');
			array_push($join, ' LEFT JOIN '.db_prefix().'deallossreasons  as '.db_prefix().'deallossreasons ON '.db_prefix().'deallossreasons.id = ' .db_prefix() . 'projects.loss_reason');
			array_push($join, ' LEFT JOIN '.db_prefix().'projects_status  as '.db_prefix().'projects_status ON '.db_prefix().'projects_status.id = ' .db_prefix() . 'projects.status');
            $by_group_by = ' group by teamleader ';
            $deals_loss_by = $this->input->post('deals_started_by');
            if ($deals_loss_by && $deals_loss_by == 'status') {
                 $by_group_by = ' group by '.db_prefix() . 'projects.status ';
            }elseif ($deals_loss_by && $deals_loss_by == 'loss_reason') {
                 $by_group_by = ' group by loss_reason ';
            }
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], $by_group_by);
            $output  = $result['output'];
            $rResult = $result['rResult'];

			$currency = $this->currencies_model->get_base_currency();
            foreach ($rResult as $aRow) {
                $row = [];
                
				$row[] = $aRow['name'];
				$row[] = $aRow['Stages'];
				$row[] = $aRow['Reasons'];
				$row[] = '<a class="btn btn-link" onclick="init_dealss_details(event,'.$aRow['teamleader'].','.$aRow['status'].','.$aRow['loss_reason'].','.$aRow['count'].')">
    '.$aRow['count'].'
  </a><div class="hide divdsdr  panel-body" id="dropdowndealdl_'.$aRow['teamleader'].'_'.$aRow['status'].'_'.$aRow['loss_reason'].'">'.$this->get_deals_started_details_reports_table($aRow).'</div>';
                $row[] = app_format_money($aRow['sum'],$currency);
                $aRow['status'] = 'all';
				$row[] = '<a class="btn btn-link" onclick="init_dealss_details(event,'.$aRow['teamleader'].',\'all\','.$aRow['loss_reason'].','.$aRow['dcc'].')">
    '.$aRow['dcc'].'
  </a><div class="hide divdsdr  panel-body" id="dropdowndealdl_'.$aRow['teamleader'].'_all_'.$aRow['loss_reason'].'">'.$this->get_deals_started_details_reports_table($aRow).'</div>'.' / '.app_format_money($aRow['avg'],$currency);
				$row[] = app_format_money($aRow['dayavg'],$currency);
				
                $output['aaData'][] = $row;
            }

          
            echo json_encode($output);
            die();
        }
    }
	
	public function get_deals_started_details_reports_table($aRow){
        return '<div id="deals_started_details_reports_'.$aRow['teamleader'].'_'.$aRow['status'].'_'.$aRow['loss_reason'].'">
   <table class="table deals_started_details deals_started_details_reports_'.$aRow['teamleader'].'_'.$aRow['status'].'_'.$aRow['loss_reason'].' scroll-responsive">
      <thead>
         <tr>
            <th>'. _l("Deal Name").'</th>
            <th>'.  _l("Deal Value").'</th>
            <th>'.  _l("Organisation").'</th>
            <th>'. _l("Contact Person").'</th>
            <th>'.  _l("Total Activities").'</th>
			<th>'.  _l("Start Date").'</th>
			<th>'.  _l("Owner").'</th>
			<th>'.  _l("Stages").'</th>
			
         </tr>
      </thead>
      <tbody></tbody>
   </table>
</div>';
    }

    public function deals_started_detail_report($teamleader = 0,$status = 0,$loss_reason = 0){
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $select = [
                db_prefix() . 'projects.name as name',
                db_prefix() . 'projects.start_date as start_date',
                db_prefix() . 'projects.project_cost as project_cost',
                db_prefix() . 'clients.company as organisation',
                '(SELECT GROUP_CONCAT(CONCAT(\' <a  href="' . admin_url('clients/view_contact/' ) . '\',' . db_prefix() . 'contacts.id ,\'"> \',' . db_prefix() . 'contacts.firstname, \' \', ' . db_prefix() . 'contacts.lastname,\' </a> \') SEPARATOR ", ") FROM ' . db_prefix() . 'contacts JOIN ' . db_prefix() . 'project_contacts on ' . db_prefix() . 'project_contacts.contacts_id = ' . db_prefix() . 'contacts.id WHERE ' . db_prefix() . 'project_contacts.project_id=' . db_prefix() . 'projects.id ORDER BY ' . db_prefix() . 'contacts.id) as contact_person',
                '(SELECT count(id) FROM ' . db_prefix() . 'tasks WHERE rel_type = "project" AND rel_id=' . db_prefix() . 'projects.id) as total_activities',
                db_prefix() . 'projects.stage_on as stage_on',
                "CONCAT(".db_prefix().'staff.firstname," ",'.db_prefix().'staff.lastname) as owner_name',
                 db_prefix().'projects_status.name as Stages',
				db_prefix().'deallossreasons.name as Reasons',
				db_prefix() . 'projects.id',
				db_prefix() . 'projects.id as id',
				db_prefix() . 'projects.userid',
				db_prefix() . 'projects.clientid as clientid',
				db_prefix() . 'projects.teamleader as teamleader',
            ];

            

            $where              = [];
            $custom_date_select = $this->get_where_report_period('start_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $deals_started_by=	$this->input->post('deals_started_by');
            if($deals_started_by != 'status'){
                if($status != 'all'){
                    array_push($where, ' AND stage_of = 0');
                }
                array_push($where, ' AND teamleader = '.$teamleader);
            }else{
                array_push($where, ' AND start_date like "'.$teamleader.'%"');
                if($loss_reason != 'all'){
                    array_push($where, ' AND stage_of = '.$loss_reason);
                }
            }
            
            if ($this->input->post('teamleader')) {
                $teamleader  = $this->input->post('teamleader');
				array_push($where, 'AND teamleader IN (' . $teamleader . ')');
            } else {
                if(!is_admin(get_staff_user_id())) {
                    $my_staffids = $this->staff_model->get_my_staffids();
                    if($my_staffids) {
                        array_push($where, 'AND teamleader in (' . implode(',',$my_staffids) . ')');
                    } else {
                        array_push($where, 'AND teamleader IN (' . get_staff_user_id() . ')');
                    }
                }
            }

            if ($this->input->post('pipeline_id')) {
                $pipeline_id  = $this->input->post('pipeline_id');
				array_push($where, 'AND pipeline_id IN (' . $pipeline_id . ')');
            }

            array_push($where, 'AND tblprojects.deleted_status = 0');

            $aColumns     = $select;
            $sIndexColumn =  'id';
            $sTable       = db_prefix() . 'projects';
            $join         = [];
            array_push($join, ' LEFT JOIN '.db_prefix().'staff  as '.db_prefix().'staff ON '.db_prefix().'staff.staffid = ' .db_prefix() . 'projects.teamleader');
            array_push($join, ' LEFT JOIN '.db_prefix().'clients  as '.db_prefix().'clients ON '.db_prefix().'clients.userid = ' .db_prefix() . 'projects.clientid');
            array_push($join, ' LEFT JOIN '.db_prefix().'projects_status  as '.db_prefix().'projects_status ON '.db_prefix().'projects_status.id = ' .db_prefix() . 'projects.status');
            array_push($join, ' LEFT JOIN '.db_prefix().'deallossreasons  as '.db_prefix().'deallossreasons ON '.db_prefix().'deallossreasons.id = ' .db_prefix() . 'projects.loss_reason');
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            $currency = $this->currencies_model->get_base_currency();
            foreach ($rResult as $aRow) {
                $row = [];

                $link = admin_url('projects/view/' . $aRow['id']);
                $row[] = '<a href="' . $link . '">' . $aRow['name'] . '</a>';

                $row[] = app_format_money($aRow['project_cost'],$currency);

                $link = admin_url('clients/client/' . $aRow['clientid']);
                $row[] = '<a href="' . $link . '">' .$aRow['organisation']. '</a>';
                
				$row[] = $aRow['contact_person'];
				$row[] = $aRow['total_activities'];
				$row[] = $aRow['start_date'];
				$row[] = $aRow['owner_name'];
				
				
				$row[] = $aRow['Stages'];
				
				
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

    }

    public function proposals_report()
    {

    }
	
    public function estimates_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('estimates_model');

            $estimateTaxes     = $this->distinct_taxes('estimate');
            $totalTaxesColumns = count($estimateTaxes);

            $select = [
                'number',
                get_sql_select_client_company(),
                'invoiceid',
                'YEAR(date) as year',
                'date',
                'expirydate',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                'reference_no',
                'status',
            ];

            $estimatesTaxesSelect = array_reverse($estimateTaxes);

            foreach ($estimatesTaxesSelect as $key => $tax) {
                array_splice($select, 9, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="estimate" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'estimates.id) as total_tax_single_' . $key);
            }

            $where              = [];
            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('estimate_status')) {
                $statuses  = $this->input->post('estimate_status');
                $_statuses = [];
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            if ($this->input->post('sale_agent_estimates')) {
                $agents  = $this->input->post('sale_agent_estimates');
                $_agents = [];
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $agent);
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'estimates';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'estimates.clientid',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'userid',
                'clientid',
                db_prefix() . 'estimates.id',
                'discount_percent',
                'deleted_customer_name',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'          => 0,
                'subtotal'       => 0,
                'total_tax'      => 0,
                'discount_total' => 0,
                'adjustment'     => 0,
            ];

            foreach ($estimateTaxes as $key => $tax) {
                $footer_data['total_tax_single_' . $key] = 0;
            }

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('estimates/list_estimates/' . $aRow['id']) . '" target="_blank">' . format_estimate_number($aRow['id']) . '</a>';

                if (empty($aRow['deleted_customer_name'])) {
                    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                } else {
                    $row[] = $aRow['deleted_customer_name'];
                }

                if ($aRow['invoiceid'] === null) {
                    $row[] = '';
                } else {
                    $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['invoiceid']) . '" target="_blank">' . format_invoice_number($aRow['invoiceid']) . '</a>';
                }

                $row[] = $aRow['year'];

                $row[] = _d($aRow['date']);

                $row[] = _d($aRow['expirydate']);

                $row[] = app_format_money($aRow['subtotal'], $currency->name);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'], $currency->name);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'], $currency->name);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach ($estimateTaxes as $tax) {
                    $row[] = app_format_money(($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]), $currency->name);
                    $footer_data['total_tax_single_' . $i] += ($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]);
                    $t--;
                    $i++;
                }

                $row[] = app_format_money($aRow['discount_total'], $currency->name);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'], $currency->name);
                $footer_data['adjustment'] += $aRow['adjustment'];


                $row[] = $aRow['reference_no'];

                $row[] = format_estimate_status($aRow['status']);

                $output['aaData'][] = $row;
            }
            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }
            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    
    private function get_where_report_period($field = 'date')
    {
        $months_report      = $this->input->post('report_months');
        $custom_date_select = '';
        if ($months_report != '') {
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth   = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int) $months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth   = date('Y-m-t');
                }

                $custom_date_select = 'AND (date(' . $field . ') BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
            } elseif ($months_report == 'today') {
                $custom_date_select = 'AND (date(' . $field . ') = "' . date('Y-m-d') . '")';
            } elseif ($months_report == 'yesterday') {
                $custom_date_select = 'AND (date(' . $field . ') = "' . date('Y-m-d', strtotime("-1 days")) . '")';
            } elseif ($months_report == 'this_week') {
                $custom_date_select = 'AND (date(' . $field . ') BETWEEN "' . date('Y-m-d',strtotime('monday this week')) . '" AND "' . date('Y-m-d', strtotime('sunday this week')) . '")';
            } elseif ($months_report == 'last_week') {
                $custom_date_select = 'AND (date(' . $field . ') BETWEEN "' . date('Y-m-d',strtotime('monday this week',strtotime("-1 week +1 day"))) . '" AND "' . date('Y-m-d', strtotime('sunday this week',strtotime("-1 week +1 day"))) . '")';
            } elseif ($months_report == 'this_month') {
                $custom_date_select = 'AND (date(' . $field . ') BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
            } elseif ($months_report == 'this_year') {
                $custom_date_select = 'AND (date(' . $field . ') BETWEEN "' .
                date('Y-m-d', strtotime(date('Y-01-01'))) .
                '" AND "' .
                date('Y-m-d', strtotime(date('Y-12-31'))) . '")';
            } elseif ($months_report == 'last_year') {
                $custom_date_select = 'AND (date(' . $field . ') BETWEEN "' .
                date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .
                '" AND "' .
                date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($this->input->post('report_from'));
                $to_date   = to_sql_date($this->input->post('report_to'));
                if ($from_date == $to_date) {
                    $custom_date_select = 'AND date(' . $field . ') = "' . $from_date . '"';
                } else {
                    $custom_date_select = 'AND (date(' . $field . ') BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
                }
            }
        }

        return $custom_date_select;
    }


    public function items()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $v = $this->db->query('SELECT VERSION() as version')->row();
            // 5.6 mysql version don't have the ANY_VALUE function implemented.

            if ($v && strpos($v->version, '5.7') !== false) {
                $aColumns = [
                        'ANY_VALUE(description) as description',
                        'ANY_VALUE((SUM(' . db_prefix() . 'itemable.qty))) as quantity_sold',
                        'ANY_VALUE(SUM(rate*qty)) as rate',
                        'ANY_VALUE(AVG(rate*qty)) as avg_price',
                    ];
            } else {
                $aColumns = [
                        'description as description',
                        '(SUM(' . db_prefix() . 'itemable.qty)) as quantity_sold',
                        'SUM(rate*qty) as rate',
                        'AVG(rate*qty) as avg_price',
                    ];
            }

            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'itemable';
            $join         = ['JOIN ' . db_prefix() . 'invoices ON ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'itemable.rel_id'];

            $where = ['AND rel_type="invoice"', 'AND status != 5', 'AND status=2'];

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            if ($this->input->post('sale_agent_items')) {
                $agents  = $this->input->post('sale_agent_items');
                $_agents = [];
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $agent);
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], 'GROUP by description');

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total_amount' => 0,
                'total_qty'    => 0,
            ];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = $aRow['description'];
                $row[] = $aRow['quantity_sold'];
                $row[] = app_format_money($aRow['rate'], $currency->name);
                $row[] = app_format_money($aRow['avg_price'], $currency->name);
                $footer_data['total_amount'] += $aRow['rate'];
                $footer_data['total_qty'] += $aRow['quantity_sold'];
                $output['aaData'][] = $row;
            }

            $footer_data['total_amount'] = app_format_money($footer_data['total_amount'], $currency->name);

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function credit_notes()
    {
        if ($this->input->is_ajax_request()) {
            $credit_note_taxes = $this->distinct_taxes('credit_note');
            $totalTaxesColumns = count($credit_note_taxes);

            $this->load->model('currencies_model');

            $select = [
                'number',
                'date',
                get_sql_select_client_company(),
                'reference_no',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                '(SELECT ' . db_prefix() . 'creditnotes.total - (
                  (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.credit_id=' . db_prefix() . 'creditnotes.id)
                  +
                  (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'creditnote_refunds WHERE ' . db_prefix() . 'creditnote_refunds.credit_note_id=' . db_prefix() . 'creditnotes.id)
                  )
                ) as remaining_amount',
                'status',
            ];

            $where = [];

            $credit_note_taxes_select = array_reverse($credit_note_taxes);

            foreach ($credit_note_taxes_select as $key => $tax) {
                array_splice($select, 5, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="credit_note" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'creditnotes.id) as total_tax_single_' . $key);
            }

            $custom_date_select = $this->get_where_report_period();

            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');

            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            if ($this->input->post('credit_note_status')) {
                $statuses  = $this->input->post('credit_note_status');
                $_statuses = [];
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'creditnotes';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'creditnotes.clientid',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'userid',
                'clientid',
                db_prefix() . 'creditnotes.id',
                'discount_percent',
                'deleted_customer_name',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'            => 0,
                'subtotal'         => 0,
                'total_tax'        => 0,
                'discount_total'   => 0,
                'adjustment'       => 0,
                'remaining_amount' => 0,
            ];

            foreach ($credit_note_taxes as $key => $tax) {
                $footer_data['total_tax_single_' . $key] = 0;
            }
            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('credit_notes/list_credit_notes/' . $aRow['id']) . '" target="_blank">' . format_credit_note_number($aRow['id']) . '</a>';

                $row[] = _d($aRow['date']);

                if (empty($aRow['deleted_customer_name'])) {
                    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
                } else {
                    $row[] = $aRow['deleted_customer_name'];
                }

                $row[] = $aRow['reference_no'];

                $row[] = app_format_money($aRow['subtotal'], $currency->name);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'], $currency->name);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'], $currency->name);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach ($credit_note_taxes as $tax) {
                    $row[] = app_format_money(($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]), $currency->name);
                    $footer_data['total_tax_single_' . $i] += ($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]);
                    $t--;
                    $i++;
                }

                $row[] = app_format_money($aRow['discount_total'], $currency->name);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'], $currency->name);
                $footer_data['adjustment'] += $aRow['adjustment'];

                $row[] = app_format_money($aRow['remaining_amount'], $currency->name);
                $footer_data['remaining_amount'] += $aRow['remaining_amount'];

                $row[] = format_credit_note_status($aRow['status']);

                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function invoices_report()
    {
        if ($this->input->is_ajax_request()) {
            $invoice_taxes     = $this->distinct_taxes('invoice');
            $totalTaxesColumns = count($invoice_taxes);

            $this->load->model('currencies_model');
            $this->load->model('invoices_model');

            $select = [
                'number',
                get_sql_select_client_company(),
                'YEAR(date) as year',
                'date',
                'duedate',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                '(SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.invoice_id=' . db_prefix() . 'invoices.id) as credits_applied',
                '(SELECT total - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid = ' . db_prefix() . 'invoices.id) - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.invoice_id=' . db_prefix() . 'invoices.id))',
                'status',
            ];

            $where = [
                'AND status != 5',
            ];

            $invoiceTaxesSelect = array_reverse($invoice_taxes);

            foreach ($invoiceTaxesSelect as $key => $tax) {
                array_splice($select, 8, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="invoice" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'invoices.id) as total_tax_single_' . $key);
            }

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('sale_agent_invoices')) {
                $agents  = $this->input->post('sale_agent_invoices');
                $_agents = [];
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $agent);
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $by_currency              = $this->input->post('report_currency');
            $totalPaymentsColumnIndex = (12 + $totalTaxesColumns - 1);

            if ($by_currency) {
                $_temp = substr($select[$totalPaymentsColumnIndex], 0, -2);
                $_temp .= ' AND currency =' . $by_currency . ')) as amount_open';
                $select[$totalPaymentsColumnIndex] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency                          = $this->currencies_model->get_base_currency();
                $select[$totalPaymentsColumnIndex] = $select[$totalPaymentsColumnIndex] .= ' as amount_open';
            }

            if ($this->input->post('invoice_status')) {
                $statuses  = $this->input->post('invoice_status');
                $_statuses = [];
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'invoices';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'invoices.clientid',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'userid',
                'clientid',
                db_prefix() . 'invoices.id',
                'discount_percent',
                'deleted_customer_name',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'           => 0,
                'subtotal'        => 0,
                'total_tax'       => 0,
                'discount_total'  => 0,
                'adjustment'      => 0,
                'applied_credits' => 0,
                'amount_open'     => 0,
            ];

            foreach ($invoice_taxes as $key => $tax) {
                $footer_data['total_tax_single_' . $key] = 0;
            }

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" target="_blank">' . format_invoice_number($aRow['id']) . '</a>';

                if (empty($aRow['deleted_customer_name'])) {
                    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                } else {
                    $row[] = $aRow['deleted_customer_name'];
                }

                $row[] = $aRow['year'];

                $row[] = _d($aRow['date']);

                $row[] = _d($aRow['duedate']);

                $row[] = app_format_money($aRow['subtotal'], $currency->name);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'], $currency->name);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'], $currency->name);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach ($invoice_taxes as $tax) {
                    $row[] = app_format_money(($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]), $currency->name);
                    $footer_data['total_tax_single_' . $i] += ($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]);
                    $t--;
                    $i++;
                }

                $row[] = app_format_money($aRow['discount_total'], $currency->name);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'], $currency->name);
                $footer_data['adjustment'] += $aRow['adjustment'];

                $row[] = app_format_money($aRow['credits_applied'], $currency->name);
                $footer_data['applied_credits'] += $aRow['credits_applied'];

                $amountOpen = $aRow['amount_open'];
                $row[]      = app_format_money($amountOpen, $currency->name);
                $footer_data['amount_open'] += $amountOpen;

                $row[] = format_invoice_status($aRow['status']);

                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function expenses($type = 'simple_report')
    {
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['currencies']    = $this->currencies_model->get();

        $data['title'] = _l('expenses_report');
        if ($type != 'simple_report') {
            $this->load->model('expenses_model');
            $data['categories'] = $this->expenses_model->get_category();
            $data['years']      = $this->expenses_model->get_expenses_years();

            if ($this->input->is_ajax_request()) {
                $aColumns = [
                    'category',
                    'amount',
                    'expense_name',
                    'tax',
                    'tax2',
                    '(SELECT taxrate FROM ' . db_prefix() . 'taxes WHERE id=' . db_prefix() . 'expenses.tax)',
                    'amount as amount_with_tax',
                    'billable',
                    'date',
                    get_sql_select_client_company(),
                    'invoiceid',
                    'reference_no',
                    'paymentmode',
                ];
                $join = [
                    'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'expenses.clientid',
                    'LEFT JOIN ' . db_prefix() . 'expenses_categories ON ' . db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category',
                ];
                $where  = [];
                $filter = [];
                include_once(APPPATH . 'views/admin/tables/includes/expenses_filter.php');
                if (count($filter) > 0) {
                    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
                }

                $by_currency = $this->input->post('currency');
                if ($by_currency) {
                    $currency = $this->currencies_model->get($by_currency);
                    array_push($where, 'AND currency=' . $by_currency);
                } else {
                    $currency = $this->currencies_model->get_base_currency();
                }

                $sIndexColumn = 'id';
                $sTable       = db_prefix() . 'expenses';
                $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                    db_prefix() . 'expenses_categories.name as category_name',
                    db_prefix() . 'expenses.id',
                    db_prefix() . 'expenses.clientid',
                    'currency',
                ]);
                $output  = $result['output'];
                $rResult = $result['rResult'];
                $this->load->model('currencies_model');
                $this->load->model('payment_modes_model');

                $footer_data = [
                    'tax_1'           => 0,
                    'tax_2'           => 0,
                    'amount'          => 0,
                    'total_tax'       => 0,
                    'amount_with_tax' => 0,
                ];

                foreach ($rResult as $aRow) {
                    $row = [];
                    for ($i = 0; $i < count($aColumns); $i++) {
                        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                            $_data = $aRow[strafter($aColumns[$i], 'as ')];
                        } else {
                            $_data = $aRow[$aColumns[$i]];
                        }
                        if ($aRow['tax'] != 0) {
                            $_tax = get_tax_by_id($aRow['tax']);
                        }
                        if ($aRow['tax2'] != 0) {
                            $_tax2 = get_tax_by_id($aRow['tax2']);
                        }
                        if ($aColumns[$i] == 'category') {
                            $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" target="_blank">' . $aRow['category_name'] . '</a>';
                        } elseif ($aColumns[$i] == 'expense_name') {
                            $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" target="_blank">' . $aRow['expense_name'] . '</a>';
                        } elseif ($aColumns[$i] == 'amount' || $i == 6) {
                            $total = $_data;
                            if ($i != 6) {
                                $footer_data['amount'] += $total;
                            } else {
                                if ($aRow['tax'] != 0 && $i == 6) {
                                    $total += ($total / 100 * $_tax->taxrate);
                                }
                                if ($aRow['tax2'] != 0 && $i == 6) {
                                    $total += ($aRow['amount'] / 100 * $_tax2->taxrate);
                                }
                                $footer_data['amount_with_tax'] += $total;
                            }

                            $_data = app_format_money($total, $currency->name);
                        } elseif ($i == 9) {
                            $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
                        } elseif ($aColumns[$i] == 'paymentmode') {
                            $_data = '';
                            if ($aRow['paymentmode'] != '0' && !empty($aRow['paymentmode'])) {
                                $payment_mode = $this->payment_modes_model->get($aRow['paymentmode'], [], false, true);
                                if ($payment_mode) {
                                    $_data = $payment_mode->name;
                                }
                            }
                        } elseif ($aColumns[$i] == 'date') {
                            $_data = _d($_data);
                        } elseif ($aColumns[$i] == 'tax') {
                            if ($aRow['tax'] != 0) {
                                $_data = $_tax->name . ' - ' . app_format_number($_tax->taxrate) . '%';
                            } else {
                                $_data = '';
                            }
                        } elseif ($aColumns[$i] == 'tax2') {
                            if ($aRow['tax2'] != 0) {
                                $_data = $_tax2->name . ' - ' . app_format_number($_tax2->taxrate) . '%';
                            } else {
                                $_data = '';
                            }
                        } elseif ($i == 5) {
                            if ($aRow['tax'] != 0 || $aRow['tax2'] != 0) {
                                if ($aRow['tax'] != 0) {
                                    $total = ($total / 100 * $_tax->taxrate);
                                    $footer_data['tax_1'] += $total;
                                }
                                if ($aRow['tax2'] != 0) {
                                    $total += ($aRow['amount'] / 100 * $_tax2->taxrate);
                                    $footer_data['tax_2'] += $total;
                                }
                                $_data = app_format_money($total, $currency->name);
                                $footer_data['total_tax'] += $total;
                            } else {
                                $_data = app_format_number(0);
                            }
                        } elseif ($aColumns[$i] == 'billable') {
                            if ($aRow['billable'] == 1) {
                                $_data = _l('expenses_list_billable');
                            } else {
                                $_data = _l('expense_not_billable');
                            }
                        } elseif ($aColumns[$i] == 'invoiceid') {
                            if ($_data) {
                                $_data = '<a href="' . admin_url('invoices/list_invoices/' . $_data) . '">' . format_invoice_number($_data) . '</a>';
                            } else {
                                $_data = '';
                            }
                        }
                        $row[] = $_data;
                    }
                    $output['aaData'][] = $row;
                }

                foreach ($footer_data as $key => $total) {
                    $footer_data[$key] = app_format_money($total, $currency->name);
                }

                $output['sums'] = $footer_data;
                echo json_encode($output);
                die;
            }
            $this->load->view('admin/reports/expenses_detailed', $data);
        } else {
            if (!$this->input->get('year')) {
                $data['current_year'] = date('Y');
            } else {
                $data['current_year'] = $this->input->get('year');
            }


            $data['export_not_supported'] = ($this->agent->browser() == 'Internet Explorer' || $this->agent->browser() == 'Spartan');

            $this->load->model('expenses_model');

            $data['chart_not_billable'] = json_encode($this->reports_model->get_stats_chart_data(_l('not_billable_expenses_by_categories'), [
                'billable' => 0,
            ], [
                'backgroundColor' => 'rgba(252,45,66,0.4)',
                'borderColor'     => '#fc2d42',
            ], $data['current_year']));

            $data['chart_billable'] = json_encode($this->reports_model->get_stats_chart_data(_l('billable_expenses_by_categories'), [
                'billable' => 1,
            ], [
                'backgroundColor' => 'rgba(37,155,35,0.2)',
                'borderColor'     => '#84c529',
            ], $data['current_year']));

            $data['expense_years'] = $this->expenses_model->get_expenses_years();

            if (count($data['expense_years']) > 0) {
                // Perhaps no expenses in new year?
                if (!in_array_multidimensional($data['expense_years'], 'year', date('Y'))) {
                    array_unshift($data['expense_years'], ['year' => date('Y')]);
                }
            }

            $data['categories'] = $this->expenses_model->get_category();

            $this->load->view('admin/reports/expenses', $data);
        }
    }

    public function expenses_vs_income($year = '')
    {
        $_expenses_years = [];
        $_years          = [];
        $this->load->model('expenses_model');
        $expenses_years = $this->expenses_model->get_expenses_years();
        $payments_years = $this->reports_model->get_distinct_payments_years();

        foreach ($expenses_years as $y) {
            array_push($_years, $y['year']);
        }
        foreach ($payments_years as $y) {
            array_push($_years, $y['year']);
        }

        $_years = array_map('unserialize', array_unique(array_map('serialize', $_years)));

        if (!in_array(date('Y'), $_years)) {
            $_years[] = date('Y');
        }

        rsort($_years, SORT_NUMERIC);
        $data['report_year'] = $year == '' ? date('Y') : $year;

        $data['years']                           = $_years;
        $data['chart_expenses_vs_income_values'] = json_encode($this->reports_model->get_expenses_vs_income_report($year));
        $data['title']                           = _l('als_expenses_vs_income');
        $this->load->view('admin/reports/expenses_vs_income', $data);
    }

    /* Total income report / ajax chart*/
    public function total_income_report()
    {
        echo json_encode($this->reports_model->total_income_report());
    }

    public function report_by_payment_modes()
    {
        echo json_encode($this->reports_model->report_by_payment_modes());
    }

    public function report_by_customer_groups()
    {
        echo json_encode($this->reports_model->report_by_customer_groups());
    }

    /* Leads conversion monthly report / ajax chart*/
    public function leads_monthly_report($month)
    {
        echo json_encode($this->reports_model->leads_monthly_report($month));
    }

    private function distinct_taxes($rel_type)
    {
        return $this->db->query('SELECT DISTINCT taxname,taxrate FROM ' . db_prefix() . "item_tax WHERE rel_type='" . $rel_type . "' ORDER BY taxname ASC")->result_array();
    }
}
