<?php

defined('BASEPATH') or exit('No direct script access allowed');

class DealLossReasons extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('DealLossReasons_model');
    }
/**
 * DealLossReasons List
**/
    public function index()
    {
		if (!has_permission('DealLossReasons', '', 'view')) {
            access_denied('DealLossReasons');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('kb_DealLossReasonss');
        }
        $data['title'] = _l('DealLossReasons');
        $this->load->view('admin/DealLossReasons/list', $data);
    }
	
/**
 * Add new or edit existing DealLossReasons
**/
    public function save($id = '')
    {
        if (!has_permission('DealLossReasons', '', 'view')) {
            access_denied('DealLossReasons');
        }
        if ($this->input->post())
		{
            $data = $this->input->post();
            if ($id == '') {
				$checkDealLossReasonsexist = $this->DealLossReasons_model->checkDealLossReasonsExist($data['name']);
				if(!empty($checkDealLossReasonsexist)) {
					set_alert('warning', _l('already_exist', _l('DealLossReasons')));
					redirect(admin_url('DealLossReasons'));
				}
				else {
					if (!has_permission('DealLossReasons', '', 'create')) {
						access_denied('DealLossReasons');
					}
					
					
					$id = $this->DealLossReasons_model->add_DealLossReasons($data);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('DealLossReasons')));
						redirect(admin_url('DealLossReasons'));
					}
				}
            }
			else {
				$checkDealLossReasonsexist = $this->DealLossReasons_model->checkDealLossReasonsExist($data['name']);
				if(!empty($checkDealLossReasonsexist) && $checkDealLossReasonsexist->id != $id) {
					set_alert('warning', _l('already_exist', _l('DealLossReasons')));
					redirect(admin_url('DealLossReasons'));
				}
				else {
					if (!has_permission('DealLossReasons', '', 'edit')) {
						access_denied('DealLossReasons');
					}
					
					$success = $this->DealLossReasons_model->update_DealLossReasons($data, $id);
					if ($success) {
						set_alert('success', _l('updated_successfully', _l('DealLossReasons')));
					}
					redirect(admin_url('DealLossReasons'));
				}
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('DealLossReasons_lowercase'));
        }
		else {
            $DealLossReasons = $this->DealLossReasons_model->getDealLossReasonsbyId($id);
            $data['DealLossReasons'] = $DealLossReasons;
            $title = _l('edit', _l('DealLossReasons')) . ' ' . $DealLossReasons->name;
        }
        $data['bodyclass'] = 'kb-DealLossReasons';
        $data['title']     = $title;
        $this->load->view('admin/DealLossReasons/form', $data);
    }

/**
 * View existing DealLossReasons details
**/
	public function view($id)
    {
        if (!has_permission('DealLossReasons', '', 'view')) {
            access_denied('View DealLossReasons');
        }
        $data['deallossreasons'] = $this->DealLossReasons_model->getDealLossReasonsbyId($id);

        if (!$data['deallossreasons']) {
            show_404();
        }
        add_views_tracking('kb_DealLossReasons', $id);
        $data['title'] = $data['deallossreasons']->name;
        $this->load->view('admin/DealLossReasons/view', $data);
    }
	
/**
 * Delete existing DealLossReasons details
**/
    public function delete_DealLossReasons($id)
    {
        if (!has_permission('DealLossReasons', '', 'delete')) {
            access_denied('DealLossReasons');
        }
        if (!$id) {
            redirect(admin_url('DealLossReasons'));
        }
        $response = $this->DealLossReasons_model->delete_DealLossReasons($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('DealLossReasons')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('DealLossReasons_lowercase')));
        }
        redirect(admin_url('DealLossReasons'));
    }
}