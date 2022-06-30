<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Custom_fields extends AdminController
{
    private $pdf_fields = [];

    private $client_portal_fields = [];

    private $client_editable_fields = [];

    public function __construct()
    {
        parent::__construct();
		$this->load->database();
        $this->load->model('custom_fields_model');
        if (!is_admin()) {
            access_denied('Access Custom Fields');
        }
        // Add the pdf allowed fields
        $this->pdf_fields             = $this->custom_fields_model->get_pdf_allowed_fields();
        $this->client_portal_fields   = $this->custom_fields_model->get_client_portal_allowed_fields();
        $this->client_editable_fields = $this->custom_fields_model->get_client_editable_fields();
    }

    /* List all custom fields */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
			$sTable       = db_prefix().'customfields';
			$this->db->where('fieldto','contacts');
			$this->db->update($sTable, ['fieldto' => 'Person']);
			
            $this->app->get_table_data('custom_fields');
			
        }
		
        $data['title'] = _l('custom_fields');
        $this->load->view('admin/custom_fields/manage', $data);
		
    }

    public function field($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                $id = $this->custom_fields_model->add($this->input->post());

                if ($id) {
                    set_alert('success', _l('added_successfully', _l('custom_field')));
                   // redirect(admin_url('custom_fields/field/' . $id));
                    redirect(admin_url('custom_fields'));
                }
            } else {
                $success = $this->custom_fields_model->update($this->input->post(), $id);
				$ch_data = $this->input->post();
				
                if (is_array($success) && isset($success['cant_change_option_custom_field'])) {
                    set_alert('warning', _l('cf_option_in_use'));
                } elseif ($success === true) {
					if (isset($ch_data['disabled'])) {
						$custom_fields = $this->custom_fields_model->get($id);
						$this->load->model('payment_modes_model');
						$this->load->model('settings_model');
						custom_check($custom_fields->fieldto);
					}
                    set_alert('success', _l('updated_successfully', _l('custom_field')));
                }
                //redirect(admin_url('custom_fields/field/' . $id));
                redirect(admin_url('custom_fields'));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('custom_field_lowercase'));
        } else {
            $data['custom_field'] = $this->custom_fields_model->get($id);
            $title                = _l('edit', _l('custom_field_lowercase'));
        }
        $data['pdf_fields']             = $this->pdf_fields;
        $data['client_portal_fields']   = $this->client_portal_fields;
        $data['client_editable_fields'] = $this->client_editable_fields;
        $data['title']                  = $title;
        $this->load->view('admin/custom_fields/customfield', $data);
    }

    /* Delete announcement from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('custom_fields'));
        }
		$custom_fields = $this->custom_fields_model->get($id);
		$this->load->model('payment_modes_model');
		$this->load->model('settings_model');
		custom_check($custom_fields->fieldto);
        $response = $this->custom_fields_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('custom_field')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('custom_field_lowercase')));
        }
        redirect(admin_url('custom_fields'));
    }

    /* Change custom field status active or inactive */
    public function change_custom_field_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->custom_fields_model->change_custom_field_status($id, $status);
			$custom_fields = $this->custom_fields_model->get($id);
			if($status==0){
				$this->load->model('payment_modes_model');
				$this->load->model('settings_model');
				custom_check($custom_fields->fieldto);
			}
        }
    }
}
