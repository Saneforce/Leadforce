<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Designation extends AdminController {
    /* List all staff designation */
public function __construct()
    {
        parent::__construct();
    }

    public function index() {
        if (!has_permission('designation', '', 'view')) {
            access_denied('designation');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('designation');
        }
        $data['title'] = _l('all_designation');
        $this->load->view('admin/designation/manage', $data);
    }

    /* Add new designation or edit existing one */

    public function designations($id = '') {
        if (!has_permission('designation', '', 'view')) {
            access_denied('designation');
        }
        // pre('SUMMA');
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('designation', '', 'create')) {
                    access_denied('designation');
                }
                $id = $this->designation_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('designation')));
                    redirect(admin_url('designation/designations/' . $id));
                }
            } else {
                if (!has_permission('designation', '', 'edit')) {
                    access_denied('designation');
                }
                $success = $this->designation_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('designation')));
                }
                redirect(admin_url('designation/designations/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('designation_lowercase'));
        } else {
            $data['designation_staff'] = $this->designation_model->get_designation_staff($id);
            $designation = $this->designation_model->get($id);
            $data['designation'] = $designation;
            $title = _l('edit', _l('designation_lowercase')) . ' ' . $designation->name;
        }
        $data['roles']         = $this->roles_model->get();
        $data['title'] = $title;
//        pre($data);
        $this->load->view('admin/designation/designation', $data);
    }

    /* Modules Permissions edit existing one */

    public function modulespermissions($id = '') {
        if (!has_permission('designation', '', 'view')) {
            access_denied('designation');
        }
        if ($this->input->post()) {
            if (!has_permission('designation', '', 'edit')) {
                access_denied('designation');
            }
            $success = $this->designation_model->update_modules_permissions($this->input->post());
            if ($success) {
                set_alert('success', _l('updated_successfully', 'Modules Permissions'));
            }
            redirect(admin_url('designation/modulespermissions'));
        }
        $data['modules_permissions'] = $this->designation_model->get_modules_permissions();
//        $data['mp'] = $this->designation_model->get_modules_permissions_active();
        $title = _l('edit', 'Modules Permissions') . ' ';
        $data['title'] = $title;
        $this->load->view('admin/designation/modulespermissions', $data);
    }

    /* Delete designation from database */

    public function delete($id) {
        if (!has_permission('designation', '', 'delete')) {
            access_denied('designation');
        }
        if (!$id) {
            redirect(admin_url('designation'));
        }
        $response = $this->designation_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('designation_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('designation')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('designation_lowercase')));
        }
        redirect(admin_url('designation'));
    }

}