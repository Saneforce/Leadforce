<?php

defined('BASEPATH') or exit('No direct script access allowed');

class All_contacts extends AdminController
{
    /* List all clients */
    public function index()
    {
        $this->load->model('callsettings_model');
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('all_contacts');
        }

        if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1') {
            $this->load->model('gdpr_model');
            $data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();
        }
        if(!isset($_REQUEST['a'])) {
            unset($_SESSION['alpha']);
        }
        $data['contacts'] = $this->clients_model->getAllContacts();
        $data['title'] = _l('customer_contacts');
		$fields = get_option('deal_fields');
		$fields1 = get_option('deal_mandatory');
		$data['need_fields'] = $data['mandatory_fields'] = array();
		$data['need_fields'] = array('firstname','email','company','priority','phonenumber','title','active');
		if(!empty($fields)  && $fields != 'null'){
			//$data['need_fields'] = json_decode($fields);
			$req_fields = json_decode($fields);
			$i = 7;
			if(!empty($req_fields)){
				foreach($req_fields as $req_field11){
					$data['need_fields'][$i] = $req_field11;
					$i++;
				}
			}
		}
		if(!empty($fields1)  && $fields1 != 'null'){
			$data['mandatory_fields'] = json_decode($fields1);
		}
        $this->load->view('admin/clients/all_contacts', $data);
    }

}
