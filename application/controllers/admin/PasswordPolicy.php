<?php

defined('BASEPATH') or exit('No direct script access allowed');

class PasswordPolicy extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('passwordpolicy_model');
    }

    public function index()
    {
        if (!has_permission('settings', '', 'view')) {
            access_denied('settings');
        }

        if ($this->input->post()) {
            if (!has_permission('settings', '', 'edit')) {
                access_denied('settings');
            }
            $password_policy =array();
            if($this->input->post('enable_password_policy') ==1){
                $password_policy ['enable_password_policy'] =1;
                $password_policy ['first_time_change_pass'] =$this->input->post('first_time_change_pass');
                $password_policy ['password_strength'] =$this->input->post('password_strength');
                $password_policy ['pass_change_period'] =$this->input->post('pass_change_period');
                $password_policy ['pass_history'] =$this->input->post('pass_history');
                $password_policy ['lock_invalid_attempt'] =$this->input->post('lock_invalid_attempt');
                $password_policy ['lock_auto_release'] =$this->input->post('lock_auto_release');
                $password_policy ['password_min_length'] =$this->input->post('password_min_length');
                $password_policy ['password_max_length'] =$this->input->post('password_max_length');
                $password_policy ['last_modified_by'] =get_staff_user_id();
            }else{
                $password_policy ['enable_password_policy'] =0;
            }
            $this->passwordpolicy_model->updatePasswordPolicy(json_encode($password_policy));
            set_alert('success', _l('updated_successfully', _l('password_policy')));
        }
        $data =array();
        $data['title'] = _l('password_policy');
        $data['password_policy'] =$this->passwordpolicy_model->getPasswordPolicy();
        foreach($data['password_policy'] as $name => $value){
            $_POST[$name] =$value;
        }
        $this->load->view('admin/password_policy/settings', $data);
    }

    
    public function changepassword()
    {
        if ($this->input->post()) {
            $policy_validation =$this->passwordpolicy_model->validate_password($this->input->post('newpasswordr', false));
            if($policy_validation !==true){
                set_alert('danger', $policy_validation);
                redirect(admin_url('authentication'));
            }elseif(!$this->passwordpolicy_model->check_password_history(true, get_staff_user_id(), $this->input->post('newpasswordr', false))){
                set_alert('danger', _l('cannot_use_old_password'));
                redirect(admin_url('authentication'));
            }else{
                $response = $this->staff_model->change_password($this->input->post(null, false), get_staff_user_id());
                if (is_array($response) && isset($response[0]['passwordnotmatch'])) {
                    set_alert('danger', _l('staff_old_password_incorrect'));
                } else {
                    if ($response == true) {
                        set_alert('success', _l('staff_password_changed'));
                    } else {
                        set_alert('warning', _l('staff_problem_changing_password'));
                    }
                }
                redirect(admin_url('staff/edit_profile'));
            }
            
        }
        
        $data =array();
        $data['title'] = _l('staff_edit_profile_change_your_password');
        $this->load->view('admin/password_policy/changepassword', $data);
    }

}
