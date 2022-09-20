<?php set_time_limit(0);

defined('BASEPATH') or exit('No direct script access allowed');

class Shared extends App_Controller
{
    public function __construct()
    {
        parent::__construct();

        

        hooks()->do_action('admin_auth_init');
    }

    public function index($shared)
    {
		$data = array();
        $links = $this->db->query("SELECT id,report_id,link_name,share_link FROM " . db_prefix() . "report_public WHERE share_link = '".$shared."' ")->result_array();
		
		
		$data['id'] = $links[0]['report_id'];
		$reports1 = $this->db->query("SELECT report_name,report_type,folder_id FROM " . db_prefix() . "report WHERE id = '".$data['id']."' ")->row();
		
		$data['report_name'] =	$reports1->report_name.'('.$reports1->report_type.')';
		$fields = deal_needed_fields();
		$needed = json_decode($fields,true);
		
		$data['need_fields']		=	$needed['need_fields'];
		$data['need_fields_label']	=	$needed['need_fields_label'];
		$data['need_fields_edit']	=	$needed['need_fields_edit'];
		$data['mandatory_fields1']	=	$needed['mandatory_fields1'];
		$data['clientid'] = '';
		$this->load->view('admin/reports/public',$data);
    }
	public function deal_edit_table($id = '')
    {
		$this->load->helper('report_summary');
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
		$fields = deal_needed_fields();
		$needed = array();
		if(!empty($fields) && $fields != 'null'){
			$needed = json_decode($fields,true);
		}
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
		$data['id'] = $id;
		if (($key = array_search('id', $needed['need_fields'])) !== false) {
			unset($needed['need_fields'][$key]);
		}
		$data['need_fields']		=	$needed['need_fields'];
		$data['need_fields_label']	=	$needed['need_fields_label'];
		$data['need_fields_edit']	=	$needed['need_fields_edit'];
		$data['mandatory_fields1']	=	$needed['mandatory_fields1'];
		$data['clientid'] = '';
        $this->app->get_table_data('report_deal_public', $data);
        
    }
}