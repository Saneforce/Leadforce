<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reminder extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('payment_modes_model');
        $this->load->model('settings_model');
    }

    public function index()
    {
       /* if (!has_permission('Reminder', '', 'view')) {
            access_denied('Reminder');
        }*/
		$data = array();
		if(isset($_POST['reminder_save'])){
			$post_data =  $this->input->post();
			$remind_data  = $setting_data = array();
			$setting_data = get_reminder_data($post_data);
			if($_REQUEST['remind_status'] == 'enable'){
				$remind_data['settings']['reminder_settings'] = $_REQUEST['reminder_settings'];
				$remind_data['settings']['remind_status'] = $_REQUEST['remind_status'];
			}else{
				$remind_data['settings']['remind_status'] = $_REQUEST['remind_status'];
				$remind_data['settings']['reminder_settings'] = '';
			}
			if( $_REQUEST['reminder_settings']== 'user' || $_REQUEST['remind_status']== 'disable'){
				$setting_data['reminder_type']	= '';
				$setting_data['customer_reminder'] = '';
				$setting_data['customer_mail']	= '';
				$setting_data['act_notify']		= '';
				$setting_data['act_alert']		= '';
				$setting_data['act_mail']		= '';
				$setting_data['act_date_time']	= '';
				$setting_data['act_day']		= '';
				$setting_data['act_month']		= '';
				$setting_data['pr_notify']		= '';
				$setting_data['pr_mail']		= '';
				$setting_data['pr_date_time']	= '';
				$setting_data['pr_day']			= '';
				$setting_data['pr_month']		= '';
				$setting_data['tar_notify']		= '';
				$setting_data['tar_mail']		= '';
				$setting_data['tar_date_time']	= '';
				$setting_data['tar_day']		= '';
				$setting_data['tar_month']		= '';
			}
            $success  = $this->settings_model->update($remind_data);
            $success1 = $this->settings_model->update_reminder_settings(null,$setting_data);
            set_alert('success', _l('rem_settings_updated'));
			redirect(admin_url('reminder'));
			exit;
		}
		$data['reminder_settings']   = $this->settings_model->get_reminder_settings(null);
		$data['reminder_status']  	 = get_option('remind_status');
		$data['cur_setting']  =  'company';
		//pre($data);
		$data['title']     = _l('reminder');
        //$this->load->view('admin/reminder/setting', $data);
        $this->load->view('admin/reminder/user_setting', $data);
    }
	 public function user()
    {
		if(get_option('reminder_settings') == 'company' || get_option('remind_status') == 'disable'){
            access_denied('Reminder');
			exit;
        }
		$data = array();
		$staffid = get_staff_user_id();
		if(isset($_POST['reminder_save'])){
			$post_data =  $this->input->post();
			$remind_data  = $setting_data = array();
			$setting_data = get_reminder_data($post_data);
            $success1 = $this->settings_model->update_reminder_settings($staffid,$setting_data);
            set_alert('success', _l('rem_settings_updated'));
			redirect(admin_url('reminder/user'));
			exit;
		}
		
		$data['reminder_settings']   = $this->settings_model->get_reminder_settings($staffid);
		//pre($data);
		$data['cur_setting']  =  'user';
		if(!empty($data['reminder_settings']->remind_status)){
			$data['reminder_status']  	 = $data['reminder_settings']->remind_status;
		}
		else{
			$data['reminder_settings'] = 'disable';
		}
		$data['title']     = _l('reminder');
        $this->load->view('admin/reminder/user_setting', $data);
    }
}
