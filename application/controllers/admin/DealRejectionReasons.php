<?php

defined('BASEPATH') or exit('No direct script access allowed');

class DealRejectionReasons extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('DealRejectionReasons_model');
    }
/**
 * DealRejectionReasons List
**/
    public function index()
    {
		if (!has_permission('DealRejectionReasons', '', 'view')) {
            access_denied('DealRejectionReasons');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('kb_DealRejectionReasons');
        }
        $data['title'] = _l('DealRejectionReasons');
        $this->load->view('admin/DealRejectionReasons/list', $data);
    }
	
/**
 * Add new or edit existing DealRejectionReasons
**/
    public function save($id = '')
    {
        if (!has_permission('DealRejectionReasons', '', 'view')) {
            access_denied('DealRejectionReasons');
        }
        if ($this->input->post())
		{
            $data = $this->input->post();
            if ($id == '') {
				$checkDealRejectionReasonsexist = $this->DealRejectionReasons_model->checkDealRejectionReasonsExist($data['name']);
				if(!empty($checkDealRejectionReasonsexist)) {
					set_alert('warning', _l('already_exist', _l('DealRejectionReasons')));
					redirect(admin_url('DealRejectionReasons'));
				}
				else {
					if (!has_permission('DealRejectionReasons', '', 'create')) {
						access_denied('DealRejectionReasons');
					}
					
					
					$id = $this->DealRejectionReasons_model->add_DealRejectionReasons($data);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('DealRejectionReasons')));
						redirect(admin_url('DealRejectionReasons'));
					}
				}
            }
			else {
				$checkDealRejectionReasonsexist = $this->DealRejectionReasons_model->checkDealRejectionReasonsExist($data['name']);
				if(!empty($checkDealRejectionReasonsexist) && $checkDealRejectionReasonsexist->id != $id) {
					set_alert('warning', _l('already_exist', _l('DealRejectionReasons')));
					redirect(admin_url('DealRejectionReasons'));
				}
				else {
					if (!has_permission('DealRejectionReasons', '', 'edit')) {
						access_denied('DealRejectionReasons');
					}
					
					$success = $this->DealRejectionReasons_model->update_DealRejectionReasons($data, $id);
					if ($success) {
						set_alert('success', _l('updated_successfully', _l('DealRejectionReasons')));
					}
					redirect(admin_url('DealRejectionReasons'));
				}
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('DealRejectionReasons_lowercase'));
        }
		else {
            $DealRejectionReasons = $this->DealRejectionReasons_model->getDealRejectionReasonsbyId($id);
            $data['DealRejectionReasons'] = $DealRejectionReasons;
            $title = _l('edit', _l('DealRejectionReasons')) . ' ' . $DealRejectionReasons->name;
        }
        $data['bodyclass'] = 'kb-DealRejectionReasons';
        $data['title']     = $title;
        $this->load->view('admin/DealRejectionReasons/form', $data);
    }

/**
 * View existing DealRejectionReasons details
**/
	public function view($id)
    {
        if (!has_permission('DealRejectionReasons', '', 'view')) {
            access_denied('View DealRejectionReasons');
        }
        $data['dealrejectionreasons'] = $this->DealRejectionReasons_model->getDealRejectionReasonsbyId($id);

        if (!$data['dealrejectionreasons']) {
            show_404();
        }
        add_views_tracking('kb_DealRejectionReasons', $id);
        $data['title'] = $data['dealrejectionreasons']->name;
        $this->load->view('admin/DealRejectionReasons/view', $data);
    }
	
/**
 * Delete existing DealRejectionReasons details
**/
    public function delete_DealRejectionReasons($id)
    {
        if (!has_permission('DealRejectionReasons', '', 'delete')) {
            access_denied('DealRejectionReasons');
        }
        if (!$id) {
            redirect(admin_url('DealRejectionReasons'));
        }
        $response = $this->DealRejectionReasons_model->delete_DealRejectionReasons($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('DealRejectionReasons')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('DealRejectionReasons_lowercase')));
        }
        redirect(admin_url('DealRejectionReasons'));
    }
}