<?php set_time_limit(0);
defined('BASEPATH') or exit('No direct script access allowed');
class Shared extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        hooks()->do_action('admin_auth_init');
		$this->load->helper('report_summary');
		$this->load->helper('reports');
		$this->load->model('dashboard_model');
		$this->load->model('staff_model');
    }
    public function index($shared)
    {
		$data = array();
        $links = $this->db->query("SELECT id,report_id,link_name,share_link FROM " . db_prefix() . "report_public WHERE share_link = '".$shared."' ")->result_array();
		$data['id'] = $links[0]['report_id'];
		$reports1 = $this->db->query("SELECT report_name,report_type,folder_id FROM " . db_prefix() . "report WHERE id = '".$data['id']."' ")->row();
		
		$folder = $this->db->query("SELECT id,folder_type FROM " . db_prefix() . "folder WHERE id = '".$reports1->folder_id."' ")->row();
		
		$data['type']	=	$folder->folder_type;
		$data['report_name'] =	$reports1->report_name.'('.$reports1->report_type.')';
		if($data['type'] == 'deal'){
			$fields = deal_needed_fields();
			$needed = json_decode($fields,true);
		}
		else{
			$needed = get_tasks_need_fields();
		}
		if (($key = array_search('id', $needed['need_fields'])) !== false) {
			unset($needed['need_fields'][$key]);
		}
		$data['need_fields']		=	$needed['need_fields'];
		$data['need_fields_label']	=	$needed['need_fields_label'];
		$data['need_fields_edit']	=	$needed['need_fields_edit'];
		$data['mandatory_fields1']	=	$needed['mandatory_fields1'];
		$data['clientid'] = '';
		$this->load->view('admin/reports/public',$data);
    }
	public function dashboard($shared = '')
	{
		
		$data = array();
		
		
        $links = $this->db->query("SELECT id,staff_id,link_name,share_link,dashboard_id FROM " . db_prefix() . "dashboard_public WHERE share_link = '".$shared."' ")->result_array();
		$req_staff = $staff_id = $links[0]['staff_id'];
		
		$dashboard_report = $this->db->query("SELECT id,dashboard_name FROM " . db_prefix() . "dashboard_report WHERE id = '".$links[0]['dashboard_id']."' ")->result_array();
		
		$low_hie = '';
		$lowlevel = $this->staff_model->printHierarchyTree_staff($staff_id,$prefix = '');
		if(!empty($lowlevel)) {
			$low_hie = ' OR staffid IN ('.implode(',', $lowlevel).')';
		}
		$staffdetails =  $this->db->query('SELECT *, staffid as staff_id FROM ' . db_prefix() . 'staff WHERE staffid = "'.$staff_id.'"'.$low_hie)->result_array();
		$data['project_members'] =  $staffdetails;
		$all_staff_id = array_column($data['project_members'],'staff_id');
		$staff_ids = implode(',',$all_staff_id);
		$all_reports =  $this->db->query('SELECT d.id,d.staff_id,d.report_id,d.type,d.tab_1,d.tab_2,d.sort1,d.sort2,r.view_by,r.view_type,r.measure_by,r.report_name,r.date_range,r.report_type FROM '. db_prefix().'dashboard d,'. db_prefix().'report r  WHERE d.staff_id in ('.$staff_ids.') and r.id = d.report_id and d.dashboard_id = "'.$links[0]['dashboard_id'].'" order by d.sort1,d.sort2 asc')->result_array();
		
		$data = get_dashboard_report($all_reports,$req_staff,$staff_ids);
		$data['public'] = 'shared';
		$data['title']  = $dashboard_report[0]['dashboard_name'];
		$this->load->view('admin/dashboard/dashboard_public', $data);
	}
	public function deal_edit_table($id = '')
    {
		$type = $_REQUEST['type'];
		
		$filters = $this->db->query("SELECT filter_1,filter_2,filter_3,filter_4,filter_5 FROM " . db_prefix() . "report_filter where report_id = '".$id."'")->result_array();
		if(!empty($filters)){
			$i = 0;
			foreach($filters as $filter12){
				$data['req_deals']['filters'][$i]	=	$filter12['filter_1'];
				$data['req_deals']['filters1'][$i]	=	$filter12['filter_2'];
				$data['req_deals']['filters2'][$i]	=	$filter12['filter_3'];
				$data['req_deals']['filters3'][$i]	=	$filter12['filter_4'];
				$data['req_deals']['filters4'][$i]	=	$filter12['filter_5'];
				$i++;
			}
		}
		if($type == 'deal'){
			$fields = deal_needed_fields();
			$needed = array();
			if(!empty($fields) && $fields != 'null'){
				$needed = json_decode($fields,true);
			}
			$custom_fields = get_table_custom_fields('projects');
		}
		else{
			$needed = get_tasks_need_fields();
			$custom_fields = get_table_custom_fields('tasks');
			$reports1 = $this->db->query("SELECT report_type,folder_id FROM " . db_prefix() . "report WHERE id = '".$id."' ")->row();
			$data['report_name']	=	$reports1->report_type;
		}
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
		$data['id'] = $id;
		if (($key = array_search('id', $needed['need_fields'])) !== false) {
			unset($needed['need_fields'][$key]);
		}
		$data['need_fields']		=	$needed['need_fields'];
		$data['need_fields_label']	=	$needed['need_fields_label'];
		$data['need_fields_edit']	=	$needed['need_fields_edit'];
		$data['mandatory_fields1']	=	$needed['mandatory_fields1'];
		$data['clientid'] = '';
		if($type == 'deal'){
			$this->app->get_table_data('report_deal', $data);
		}
		else{
			$this->app->get_table_data('report_activity', $data);
		}
    }
}