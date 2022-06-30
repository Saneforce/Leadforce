<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tasktype extends AdminController
{
    public function __construct()
    {
        parent::__construct();
		$this->load->model('tasktype_model');
    }

/**
 * Task-types List
**/
    public function index()
    {
		if (!has_permission('tasktype', '', 'view')) {
            access_denied('tasktype');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('tasktype');
        }
        $data['title'] = _l('tasktype');
        $this->load->view('admin/tasktype/list', $data);
    }

/**
 * Add or edit Task-type
**/
    public function save($id = '')
    {
        if (!has_permission('tasktype', '', 'view')) {
            access_denied('tasktype');
        }
        if ($this->input->post())
		{
            $data = $this->input->post();
            if ($id == '') {
				$checktasktypeexist = $this->tasktype_model->checkTasktypeexist($data['name']);
				if(!empty($checktasktypeexist)) {
					set_alert('warning', _l('already_exist', _l('tasktype')));
					redirect(admin_url('tasktype'));
				}
				else {
					if (!has_permission('tasktype', '', 'create')) {
						access_denied('tasktype');
					}
					$id = $this->tasktype_model->addTasktype($data);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('tasktype')));
						redirect(admin_url('tasktype'));
					}
				}
            }
			else {
				$checktasktypeexist = $this->tasktype_model->checkTasktypeexist($data['name']);
				if(!empty($checktasktypeexist) && $checktasktypeexist->id != $id) {
					set_alert('warning', _l('already_exist', _l('tasktype')));
					redirect(admin_url('tasktype'));
				}
				else {
					if (!has_permission('tasktype', '', 'edit')) {
						access_denied('tasktype');
					}
					$success = $this->tasktype_model->updateTasktype($data, $id);
					if ($success) {
						set_alert('success', _l('updated_successfully', _l('tasktype')));
					}
					redirect(admin_url('tasktype'));
				}
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('tasktype_lowercase'));
        }
		else {
            $tasktype = $this->tasktype_model->getTasktype($id);
            $data['tasktype'] = $tasktype;
            $title = _l('edit', _l('tasktype')) . ' ' . $tasktype->name;
        }
        $data['bodyclass'] = 'tasktype';
        $data['title']     = $title;
        $this->load->view('admin/tasktype/form', $data);
    }
	
/**
 * View Task-type
**/
	public function view($id)
    {
        if (!has_permission('tasktype', '', 'view')) {
            access_denied('View tasktype');
        }
        $data['tasktype'] = $this->tasktype_model->getTasktype($id);

        if (!$data['tasktype']) {
            show_404();
        }
        add_views_tracking('tasktype', $id);
        $data['title'] = $data['tasktype']->name;
        $this->load->view('admin/tasktype/view', $data);
    }
	
/**
 * Delete Task-type
**/
    public function delete_tasktype($id)
    {
        if (!has_permission('tasktype', '', 'delete')) {
            access_denied('tasktype');
        }
        if (!$id) {
            redirect(admin_url('tasktype'));
        }
        $response = $this->tasktype_model->deleteTasktype($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('tasktype')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('tasktype_lowercase')));
        }
        redirect(admin_url('tasktype'));
    }
}