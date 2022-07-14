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
        $links = $this->db->query("SELECT * FROM " . db_prefix() . "report_public WHERE share_link = '".$shared."' ")->result_array();
		if(empty($links) || empty($shared)){
			 access_denied('reports');
		}
		$data = array();
		$data['id'] = $links[0]['report_id'];
		$fields = get_option('deal_fields');
		$data['need_fields'] = $data['need_fields_edit'] =  $data['mandatory_fields1'] = array('name');
		$data['need_fields_label'] = array('project_name');
		$i = $j = 1;
		if(!empty($fields1) && $fields1 != 'null'){
			$i1 =0;
			$req_fields2 = json_decode($fields1);
			if(!empty($req_fields2)){
				foreach($req_fields2 as $req_field2){
					$data['mandatory_fields1'][$i1] = $req_field2;
					$i1++;
				}
			}
		}
		if(!empty($fields) && $fields != 'null'){
			$req_fields = json_decode($fields);
			
			if(!empty($req_fields)){
				foreach($req_fields as $req_field11){
					$data['need_fields_edit'][$i] = $req_field11;
					if($req_field11 == 'clientid'){
						$data['need_fields'][$i] = 'company';
						$data['need_fields_label'][$j] = 'project_customer';
					}
					else if($req_field11 == 'primary_contact'){
						$data['need_fields_label'][$j] = 'project_primary_contacts';
						$data['need_fields'][$i] = 'contact_email1';
						$i++;
						$data['need_fields'][$i] = 'contact_phone1';
						$i++;
						$data['need_fields'][$i] = 'contact_name';
					}
					else if($req_field11 == 'teamleader'){
						$data['need_fields'][$i] = 'teamleader_name';
						$data['need_fields_label'][$j] = 'teamleader';
					}
					else if($req_field11 == 'project_members[]'){
						$data['need_fields'][$i] = 'members';
						$data['need_fields_label'][$j] = 'project_members';
					}
					else if($req_field11 == 'project_contacts[]'){
						$data['need_fields'][$i] = 'project_contacts[]';
						$data['need_fields_label'][$j] = 'project_contacts';
					}
					else if($req_field11 == 'project_cost'){
						$data['need_fields'][$i] = 'project_cost';
						$data['need_fields_label'][$j] = 'project_total_cost';
					}
					else if($req_field11 == 'pipeline_id'){
						$data['need_fields'][$i] = 'pipeline_id';
						$data['need_fields_label'][$j] = 'pipeline';
					}
					else{
						
						$data['need_fields_label'][$j] = $req_field11;
						$data['need_fields'][$i] = $req_field11;
						if($req_field11 == 'status'){
							$i++;
							$data['need_fields_label'][$j] = 'project_status';
							$data['need_fields'][$i] = 'project_status';
						}
					}
					$i++;
					$j++;
				}
				
			}
		}
		$data['need_fields'][$i] = 'id';
		$i++;
		$data['need_fields'][$i] = 'product_qty';
		$i++;
		$data['need_fields'][$i] = 'product_amt';
		$i++;
		$data['need_fields'][$i] = 'projects_budget';
		$i++;
		$data['need_fields'][$i] = 'customers_hyperlink';
		$data['clientid'] = '';
		$this->load->view('admin/reports/public',$data);
    }
	public function deal_edit_table($id = '')
    {
		$filters = $this->db->query("SELECT * FROM " . db_prefix() . "report_filter where report_id = '".$id."'")->result_array();
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
		$fields = get_option('deal_fields');
		$data['need_fields'] = $data['need_fields_edit'] =  $data['mandatory_fields1'] = array('name');
		$data['need_fields_label'] = array('project_name');
		$i = $j = 1;
		if(!empty($fields1) && $fields1 != 'null'){
			$i1 =0;
			$req_fields2 = json_decode($fields1);
			if(!empty($req_fields2)){
				foreach($req_fields2 as $req_field2){
					$data['mandatory_fields1'][$i1] = $req_field2;
					$i1++;
				}
			}
		}
		if(!empty($fields) && $fields != 'null'){
			$req_fields = json_decode($fields);
			
			if(!empty($req_fields)){
				foreach($req_fields as $req_field11){
					$data['need_fields_edit'][$i] = $req_field11;
					if($req_field11 == 'clientid'){
						$data['need_fields'][$i] = 'company';
						$data['need_fields_label'][$j] = 'project_customer';
					}
					else if($req_field11 == 'primary_contact'){
						$data['need_fields_label'][$j] = 'project_primary_contacts';
						$data['need_fields'][$i] = 'contact_email1';
						$i++;
						$data['need_fields'][$i] = 'contact_phone1';
						$i++;
						$data['need_fields'][$i] = 'contact_name';
					}
					else if($req_field11 == 'teamleader'){
						$data['need_fields'][$i] = 'teamleader_name';
						$data['need_fields_label'][$j] = 'teamleader';
					}
					else if($req_field11 == 'project_members[]'){
						$data['need_fields'][$i] = 'members';
						$data['need_fields_label'][$j] = 'project_members';
					}
					else if($req_field11 == 'project_contacts[]'){
						$data['need_fields'][$i] = 'project_contacts[]';
						$data['need_fields_label'][$j] = 'project_contacts';
					}
					else if($req_field11 == 'project_cost'){
						$data['need_fields'][$i] = 'project_cost';
						$data['need_fields_label'][$j] = 'project_total_cost';
					}
					else if($req_field11 == 'pipeline_id'){
						$data['need_fields'][$i] = 'pipeline_id';
						$data['need_fields_label'][$j] = 'pipeline';
					}
					else{
						
						$data['need_fields_label'][$j] = $req_field11;
						$data['need_fields'][$i] = $req_field11;
						if($req_field11 == 'status'){
							$i++;
							$data['need_fields_label'][$j] = 'project_status';
							$data['need_fields'][$i] = 'project_status';
						}
					}
					$i++;
					$j++;
				}
				
			}
		}
		$data['need_fields'][$i] = 'id';
		$i++;
		$data['need_fields'][$i] = 'product_qty';
		$i++;
		$data['need_fields'][$i] = 'product_amt';
		$i++;
		$data['need_fields'][$i] = 'projects_budget';
		$i++;
		$data['need_fields'][$i] = 'customers_hyperlink';
		$data['clientid'] = '';
        $this->app->get_table_data('report_deal_public', $data);
        
    }
}