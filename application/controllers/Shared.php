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
		$fields = deal_needed_fields();
		$needed = json_decode($fields,true);
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
	public function deal_edit_table($id = '')
    {
		$filters = $this->db->query("SELECT filter_1,filter_2,filter_3,filter_4,filter_5 FROM " . db_prefix() . "report_filter where report_id = '".$id."'")->result_array();
		if(!empty($filters)){
			$i = 0;
			foreach($filters as $filter12){
				$data['filters'][$i]	=	$filter12['filter_1'];
				$data['filters1'][$i]	=	$filter12['filter_2'];
				$data['filters2'][$i]	=	$filter12['filter_3'];
				$data['filters3'][$i]	=	$filter12['filter_4'];
				$data['filters4'][$i]	=	$filter12['filter_5'];
				$i++;
			}
		}
		$data['id'] = $id;
		$fields = deal_needed_fields();
		$needed = json_decode($fields,true);
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