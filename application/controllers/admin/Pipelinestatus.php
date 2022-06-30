<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pipelinestatus extends AdminController
{
    public function __construct()
    {
        parent::__construct();
		$this->load->model('pipelinestatus_model');
    }

/**
 * Task-types List
**/
    public function index()
    {
		if (!has_permission('pipelinestatus', '', 'view')) {
            access_denied('pipelinestatus');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('pipelinestatus');
        }
        $data['title'] = _l('pipelinestatus');
        $this->load->view('admin/pipelinestatus/list', $data);
    }

/**
 * Add or edit Task-type
**/
    public function save($id = '')
    {
        if (!has_permission('pipelinestatus', '', 'view')) {
            access_denied('pipelinestatus');
        }
        if ($this->input->post())
		{
            $data = $this->input->post();
            if ($id == '') {
				$checkpipelinestatusexist = $this->pipelinestatus_model->checkPipelinestatusexist($data['name']);
				if(!empty($checkpipelinestatusexist)) {
					set_alert('warning', _l('already_exist', _l('pipelinestatus')));
					redirect(admin_url('pipelinestatus'));
				}
				else {
					if (!has_permission('pipelinestatus', '', 'create')) {
						access_denied('pipelinestatus');
					}
					$id = $this->pipelinestatus_model->addPipelinestatus($data);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pipelinestatus')));
						redirect(admin_url('pipelinestatus'));
					}
				}
            }
			else {
				$checkpipelinestatusexist = $this->pipelinestatus_model->checkPipelinestatusexist($data['name']);
				if(!empty($checkpipelinestatusexist) && $checkpipelinestatusexist->id != $id) {
					set_alert('warning', _l('already_exist', _l('pipelinestatus')));
					redirect(admin_url('pipelinestatus'));
				}
				else {
					if (!has_permission('pipelinestatus', '', 'edit')) {
						access_denied('pipelinestatus');
                    }
                   
					$success = $this->pipelinestatus_model->updatePipelinestatus($data, $id);
					if ($success) {
						set_alert('success', _l('updated_successfully', _l('pipelinestatus')));
					}
					redirect(admin_url('pipelinestatus'));
				}
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('pipelinestatus_lowercase'));
        }
		else {
            $pipelinestatus = $this->pipelinestatus_model->getPipelinestatus($id);
            $data['pipelinestatus'] = $pipelinestatus;
            $title = _l('edit', _l('pipelinestatus')) . ' ' . $pipelinestatus->name;
        }
        $data['bodyclass'] = 'pipelinestatus';
        $data['title']     = $title;
        $this->load->view('admin/pipelinestatus/form', $data);
    }
	
/**
 * View Task-type
**/
	public function view($id)
    {
        if (!has_permission('pipelinestatus', '', 'view')) {
            access_denied('View pipelinestatus');
        }
        $data['pipelinestatus'] = $this->pipelinestatus_model->getPipelinestatus($id);

        if (!$data['pipelinestatus']) {
            show_404();
        }
        add_views_tracking('pipelinestatus', $id);
        $data['title'] = $data['pipelinestatus']->name;
        $this->load->view('admin/pipelinestatus/view', $data);
    }
	
/**
 * Delete Task-type
**/
    public function delete_pipelinestatus()
    {
       // pre($_POST);

        if (!has_permission('pipelinestatus', '', 'delete')) {
            access_denied('pipelinestatus');
        }
       
        $response = $this->pipelinestatus_model->deletePipelinestatus();
        if ($response == true) {
            set_alert('success', _l('deleted', _l('pipelinestatus')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('pipelinestatus_lowercase')));
        }
        redirect(admin_url('pipelinestatus'));
    }


    public function getpipelinedeals() {
        $id = $_GET['id'];
        $data = $this->pipelinestatus_model->getpipelinedealstatus($id);
        echo json_encode($data);
        exit();
    }
}