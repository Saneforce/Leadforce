<?php

defined('BASEPATH') or exit('No direct script access allowed');

class deal_fields extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('payment_modes_model');
        $this->load->model('settings_model');
        $this->load->model('callsettings_model');
    }

    public function index()
    {
        $data = array();
		if ($this->input->post()) {
			$all_fields = array('name','clientid','project_contacts','primary_contact','pipeline_id','status','teamleader','project_members','project_cost','project_start_date','project_deadline','tags','description');
			$i1 = 0;
			$cur_message = array();
			if(!empty($all_fields)){
				foreach($all_fields as $all_field1){
					$cur_message[$all_field1] = $_POST['important_message'][$i1];
					$i1++;
				}
			}
			$post_data['settings']['deal_fields'] = json_encode($_POST['deal']);
			$post_data['settings']['deal_mandatory'] = json_encode($_POST['deal_mandatory']);
			$post_data['settings']['deal_important'] = json_encode($_POST['deal_important']);
			$post_data['settings']['deal_important_msg'] = json_encode($cur_message);
			$success = $this->settings_model->update($post_data);
			 if ($success > 0) {
                set_alert('success', _l('deal_needed_field_updated'));
            }
			redirect(admin_url('deal_fields'));
		}
		$data['title']   = 'Deal Fields';
		$fields = get_option('deal_fields');
		$fields1 = get_option('deal_mandatory');
		$fields2 = get_option('deal_important');
		$fields3 = get_option('deal_important_msg');
		$data['needed_fields'] = $data['mandatory_fields'] = $data['important_fields'] =  $data['important_msg'] = array();
		if(!empty($fields) && $fields != 'null'){
			$data['needed_fields'] = json_decode($fields);
		}
		if(!empty($fields1) && $fields1 != 'null'){
			$data['mandatory_fields'] = json_decode($fields1);
		}
		if(!empty($fields2)){
			$data['important_fields'] = json_decode($fields2);
		}
		if(!empty($fields3)){
			$data['important_msg'] = json_decode($fields3);
		}
        $this->load->view('admin/deal_fields/fields', $data);
    }

}
