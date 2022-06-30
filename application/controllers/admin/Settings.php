<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('payment_modes_model');
        $this->load->model('settings_model');
    }

    /* View all settings */
    public function index()
    {
       
        if (!has_permission('settings', '', 'view')) {
            access_denied('settings');
        }

        $tab = $this->input->get('group');


        if ($this->input->post()) {
            if (!has_permission('settings', '', 'edit')) {
                access_denied('settings');
            }
            $logo_uploaded     = (handle_company_logo_upload() ? true : false);
            $favicon_uploaded  = (handle_favicon_upload() ? true : false);
            $signatureUploaded = (handle_company_signature_upload() ? true : false);
            
            $post_data = $this->input->post();
            $tmpData   = $this->input->post(null, false);

			if (isset($post_data['settings']['tasks_list_column_order'])) {
                $post_data['settings']['tasks_list_column_order'] = json_encode($post_data['settings']['tasks_list_column_order']);
            }
			
            if (isset($post_data['settings']['email_header'])) {
                $post_data['settings']['email_header'] = $tmpData['settings']['email_header'];
            }

            if (isset($post_data['settings']['email_footer'])) {
                $post_data['settings']['email_footer'] = $tmpData['settings']['email_footer'];
            }

            if (isset($post_data['settings']['email_signature'])) {
                $post_data['settings']['email_signature'] = $tmpData['settings']['email_signature'];
            }

            if (isset($post_data['settings']['smtp_password'])) {
                $post_data['settings']['smtp_password'] = $tmpData['settings']['smtp_password'];
            }
			if($_REQUEST['group'] == 'smtp_settings') {
				$ch_mail_server = get_option('company_mail_server');
				if(get_option('connect_mail')!='no' && !empty($ch_mail_server) && $ch_mail_server=='yes'){
					$post_data['settings']['smtp_encryption'] = $_REQUEST['smtp_encryption'];
					$post_data['settings']['smtp_host'] = $_REQUEST['smtp_host'];
					$post_data['settings']['smtp_port'] = $_REQUEST['smtp_port'];
					$post_data['settings']['smtp_email'] = $_REQUEST['smtp_email'];
					$post_data['settings']['smtp_username'] = $_REQUEST['smtp_username'];
					$post_data['settings']['smtp_password'] = $_REQUEST['smtp_password'];
				}
				else{
					$ch1_staffid = get_staff_user_id();
					$ch_admin = is_admin($ch1_staffid);
					$cond = array('user_id'=>$ch1_staffid);
					$table = db_prefix() . 'personal_mail_setting';
					$mail_setting12 = $this->db->where($cond)->get($table)->result_array();
					if(!empty($mail_setting12)){
						$post_data['settings']['smtp_encryption'] = $mail_setting12[0]['smtp_encryption'];
						$post_data['settings']['smtp_host'] = $mail_setting12[0]['smtp_host'];
						$post_data['settings']['smtp_port'] = $mail_setting12[0]['smtp_port'];
						$post_data['settings']['smtp_email'] = $mail_setting12[0]['smtp_email'];
						$post_data['settings']['smtp_username'] = $mail_setting12[0]['smtp_username'];
						$post_data['settings']['smtp_password'] = $mail_setting12[0]['smtp_password'];
					}
				}
				//$post_data['settings']['company_mail_server'] = $_REQUEST['mail_server'];
                $post_data['settings']['company_smtp_server'] = $_REQUEST['company_smtpserver'];
               
                $post_data['settings']['company_smtp_host'] = $_REQUEST['smtp_host'];
                $post_data['settings']['company_smtp_encryption'] = $_REQUEST['smtp_encryption'];
                $post_data['settings']['company_smtp_port'] = $_REQUEST['smtp_port'];
				$post_data['settings']['company_smtp_username'] = $_REQUEST['smtp_username'];
                $post_data['settings']['company_smtp_password'] = $_REQUEST['smtp_password'];
				$post_data['settings']['company_smtp_email'] = $_REQUEST['smtp_email'];
				
				/*$post_data['settings']['company_imap_encryption'] = $_REQUEST['imap_encryption'];
                $post_data['settings']['company_imap_host'] = $_REQUEST['imap_host'];
                $post_data['settings']['company_imap_port'] = $_REQUEST['imap_port'];
                $post_data['settings']['company_smtp_email'] = $_REQUEST['smtp_email'];*/
			}
			if($_REQUEST['group'] == 'company_settings') {
				$post_data['settings']['connect_mail'] = $_REQUEST['connect_mail'];
				 $post_data['settings']['company_imap_server'] = $_REQUEST['company_imap_server'];
				/*if($_REQUEST['mail_server'] == 'yes'){
					$post_data['settings']['smtp_encryption'] = $_REQUEST['smtp_encryption'];
					$post_data['settings']['smtp_host'] = $_REQUEST['smtp_host'];
					$post_data['settings']['smtp_port'] = $_REQUEST['smtp_port'];
					$post_data['settings']['smtp_email'] = $_REQUEST['smtp_email'];
					$post_data['settings']['smtp_username'] = $_REQUEST['smtp_username'];
					$post_data['settings']['smtp_password'] = $_REQUEST['smtp_password'];
				}
				else{
					$ch1_staffid = get_staff_user_id();
					$ch_admin = is_admin($ch1_staffid);
					$cond = array('user_id'=>$ch1_staffid);
					$table = db_prefix() . 'personal_mail_setting';
					$mail_setting12 = $this->db->where($cond)->get($table)->result_array();
					if(!empty($mail_setting12)){
						$post_data['settings']['smtp_encryption'] = $mail_setting12[0]['smtp_encryption'];
						$post_data['settings']['smtp_host'] = $mail_setting12[0]['smtp_host'];
						$post_data['settings']['smtp_port'] = $mail_setting12[0]['smtp_port'];
						$post_data['settings']['smtp_email'] = $mail_setting12[0]['smtp_email'];
						$post_data['settings']['smtp_username'] = $mail_setting12[0]['smtp_username'];
						$post_data['settings']['smtp_password'] = $mail_setting12[0]['smtp_password'];
					}
				}*/
				
                /*$post_data['settings']['company_mail_server'] = $_REQUEST['mail_server'];
                $post_data['settings']['company_smtp_server'] = $_REQUEST['company_smtpserver'];
                $post_data['settings']['company_imap_server'] = $_REQUEST['company_imap_server'];
                $post_data['settings']['company_smtp_host'] = $_REQUEST['smtp_host'];
                $post_data['settings']['company_smtp_encryption'] = $_REQUEST['smtp_encryption'];
                $post_data['settings']['company_smtp_port'] = $_REQUEST['smtp_port'];
				$post_data['settings']['company_smtp_username'] = $_REQUEST['smtp_username'];
                $post_data['settings']['company_smtp_password'] = $_REQUEST['smtp_password'];*/
				
				$post_data['settings']['company_mail_server'] = $_REQUEST['mail_server'];
				
				$post_data['settings']['company_imap_encryption'] = $_REQUEST['imap_encryption'];
                $post_data['settings']['company_imap_host'] = $_REQUEST['imap_host'];
                $post_data['settings']['company_imap_port'] = $_REQUEST['imap_port'];
                //$post_data['settings']['company_smtp_email'] = $_REQUEST['smtp_email'];
               
            }
            if($_REQUEST['active_tab'] == 'email_config' || $_REQUEST['group'] == 'email') {
                $post_data['settings']['smtp_encryption'] = $_REQUEST['smtp_encryption'];
                $post_data['settings']['smtp_host'] = $_REQUEST['smtp_host'];
                $post_data['settings']['smtp_port'] = $_REQUEST['smtp_port'];
                $post_data['settings']['smtp_email'] = $_REQUEST['smtp_email'];
                $post_data['settings']['smtp_username'] = $_REQUEST['smtp_username'];
                $post_data['settings']['smtp_password'] = $_REQUEST['smtp_password'];
            }
            if($_REQUEST['active_tab'] == 'email_queue' || $_REQUEST['group'] == 'email') {
                $post_data['settings']['imap_encryption'] = $_REQUEST['imap_encryption'];
                $post_data['settings']['imap_host'] = $_REQUEST['imap_host'];
                $post_data['settings']['imap_port'] = $_REQUEST['imap_port'];
                $post_data['settings']['imap_email'] = $_REQUEST['imap_email'];
                $post_data['settings']['imap_username'] = $_REQUEST['imap_username'];
                $post_data['settings']['imap_password'] = $_REQUEST['imap_password'];
            }
			if($_REQUEST['group'] == 'deal') {
                $post_data['settings']['link_deal'] = $_REQUEST['link_deal'];
				$post_data['settings']['deal_map'] =  '';
				if($_REQUEST['link_deal'] == 'yes')
					$post_data['settings']['deal_map'] = $_REQUEST['map_deal'];
               
            }
			if($_REQUEST['group'] == 'email_local') {
                $post_data['settings']['email_local'] = $_REQUEST['email_local'];
			}
            //echo "<pre>"; print_r($post_data); exit;
            $success = $this->settings_model->update($post_data);
//echo $this->db->last_query();exit;
            if ($success > 0) {
                set_alert('success', _l('settings_updated'));
            }

            if ($logo_uploaded || $favicon_uploaded) {
                set_debug_alert(_l('logo_favicon_changed_notice'));
            }

            // Do hard refresh on general for the logo
            if ($tab == 'general') {
                redirect(admin_url('settings?group=' . $tab), 'refresh');
            } elseif ($signatureUploaded) {
                redirect(admin_url('settings?group=pdf&tab=signature'));
            } else {
                $redUrl = admin_url('settings?group=' . $tab);
                if ($this->input->get('active_tab')) {
                    $redUrl .= '&tab=' . $this->input->get('active_tab');
                }
                redirect($redUrl);
            }
        }

        $this->load->model('taxes_model');
        $this->load->model('tickets_model');
        $this->load->model('leads_model');
        $this->load->model('currencies_model');
        $data['taxes']                                   = $this->taxes_model->get();
        $data['ticket_priorities']                       = $this->tickets_model->get_priority();
        $data['ticket_priorities']['callback_translate'] = 'ticket_priority_translate';
        $data['roles']                                   = $this->roles_model->get();
        $data['leads_sources']                           = $this->leads_model->get_source();
        $data['leads_statuses']                          = $this->leads_model->get_status();
        $data['title']                                   = _l('options');

        $data['admin_tabs'] = ['update', 'info'];

        if (!$tab || (in_array($tab, $data['admin_tabs']) && !is_admin())) {
            $tab = 'general';
        }

        $data['tabs'] = $this->app_tabs->get_settings_tabs();
        $emailtab = array();

       /* $emailtab['slug'] ='email';
        $emailtab['name'] = 'Email Settings';
        $emailtab['view'] = 'admin/settings/includes/' . $tab;
        $emailtab['position'] = 70;
        $emailtab['icon'] = '';
        $emailtab['href'] = '#';
        $emailtab['children'] = array();
        $data['tabs']['email'] = $emailtab; */
        //echo "<pre>"; print_r($data['tabs']); exit;
		if($tab != 'company_settings' && $tab != 'deal' && $tab != 'email_local' && $tab != 'enable_call' && $tab != 'agent'){
			if(!empty($data['tabs'])){
				foreach($data['tabs'] as $tab_1 => $val12){
					if($tab_1 == 'company_settings' || $tab_1 == 'deal' || $tab_1 == 'email_local'  || $tab_1 == 'enable_call' || $tab_1 == 'agent'){
						unset($data['tabs'][$tab_1]);
					}
					
				}
			}
		}
		else{
			if(!empty($data['tabs'])){
				foreach($data['tabs'] as $tab_1 => $val12){
					
					if($tab_1 != 'company_settings' && $tab_1 != 'deal' && $tab_1 != 'email_local'){
						unset($data['tabs'][$tab_1]);
					}
					
				}
			}
		}
        if (!in_array($tab, $data['admin_tabs'])) {
			
			//echo '<pre>';print_r($data['tabs']);exit;
            $data['tab'] = $this->app_tabs->filter_tab($data['tabs'], $tab);
        } else {
            // Core tabs are not registered
            $data['tab']['slug'] = $tab;
            $data['tab']['view'] = 'admin/settings/includes/' . $tab;
        }
/*
        if (!$data['tab'] && $tab == 'email') {
            $data['tab']['slug'] = $tab;
            $data['tab']['name'] = 'Email Settings';
            $data['tab']['view'] = 'admin/settings/includes/' . $tab;
            $data['tab']['position'] = 70;
            $data['tab']['icon'] = '';
            $data['tab']['href'] = '#';
            $data['tab']['children'] = array();
        }
        */
        if (!$data['tab']) {
            show_404();
        }



        if ($data['tab']['slug'] == 'update') {
            if (!extension_loaded('curl')) {
                $data['update_errors'][] = 'CURL Extension not enabled';
                $data['latest_version']  = 0;
                $data['update_info']     = json_decode('');
            } else {
                $data['update_info'] = $this->app->get_update_info();
                if (strpos($data['update_info'], 'Curl Error -') !== false) {
                    $data['update_errors'][] = $data['update_info'];
                    $data['latest_version']  = 0;
                    $data['update_info']     = json_decode('');
                } else {
                    $data['update_info']    = json_decode($data['update_info']);
                    $data['latest_version'] = $data['update_info']->latest_version;
                    $data['update_errors']  = [];
                }
            }

            if (!extension_loaded('zip')) {
                $data['update_errors'][] = 'ZIP Extension not enabled';
            }

            $data['current_version'] = $this->current_db_version;
        }
 
        $data['contacts_permissions'] = get_contact_permissions();
        $data['payment_gateways']     = $this->payment_modes_model->get_payment_gateways(true);
//echo "<pre>"; print_r($data); exit;
        $this->load->view('admin/settings/all', $data);
    }

    public function delete_tag($id)
    {
        if (!$id) {
            redirect(admin_url('settings?group=tags'));
        }

        if (!has_permission('settings', '', 'delete')) {
            access_denied('settings');
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tags');
        $this->db->where('tag_id', $id);
        $this->db->delete(db_prefix() . 'taggables');

        redirect(admin_url('settings?group=tags'));
    }

    public function tasks_list_column()
    {
		 //pre($_SERVER);
		if (!has_permission('settings', '', 'view')) {
            access_denied('settings');
        }
		
		if ($this->input->post()) {
            if (!has_permission('settings', '', 'edit')) {
                access_denied('settings');
            }
           

            $post_data = $this->input->post();
            $rel_type_is = $this->input->post('rel_type_is');
			if (isset($post_data['settings']['tasks_list_column_order'])) {
                $post_data['settings']['tasks_list_column_order'.$rel_type_is] = json_encode($post_data['settings']['tasks_list_column_order']);
            }
			$success = $this->settings_model->update($post_data);

		}
		$this->load->library('user_agent');
		redirect($this->agent->referrer());
    }
	

    public function projects_list_column()
    {
		 //pre($_SERVER);
		if (!has_permission('settings', '', 'view')) {
            access_denied('settings');
        }
		
		if ($this->input->post()) {
            if (!has_permission('settings', '', 'edit')) {
                access_denied('settings');
            }
           

            $post_data = $this->input->post();
			if (isset($post_data['settings']['projects_list_column'])) {
                $post_data['settings']['projects_list_column_order'] = json_encode($post_data['settings']['projects_list_column']);
            }
			$success = $this->settings_model->update($post_data);

		}
		$this->load->library('user_agent');
		redirect($this->agent->referrer());
    }
	public function target_list_column()
    {
		 //pre($_SERVER);
		if (!has_permission('settings', '', 'view')) {
            access_denied('settings');
        }
		
		if ($this->input->post()) {
            if (!has_permission('settings', '', 'edit')) {
                access_denied('settings');
            }
           

            $post_data = $this->input->post();
			if (isset($post_data['settings']['target_list_column'])) {
                $post_data['settings']['target_list_column_order'] = json_encode($post_data['settings']['target_list_column']);
            }
			$success = $this->settings_model->update($post_data);

		}
		$this->load->library('user_agent');
		redirect($this->agent->referrer());
    }
	public function target_activity_list_column()
    {
		 //pre($_SERVER);
		if (!has_permission('settings', '', 'view')) {
            access_denied('settings');
        }
		
		if ($this->input->post()) {
            if (!has_permission('settings', '', 'edit')) {
                access_denied('settings');
            }
           

            $post_data = $this->input->post();
			if (isset($post_data['settings']['target_list_column'])) {
                $post_data['settings']['target_activity_list_column'] = json_encode($post_data['settings']['target_list_column']);
            }
			$success = $this->settings_model->update($post_data);

		}
		$this->load->library('user_agent');
		redirect($this->agent->referrer());
    }


    public function leads_list_column()
    {
		 //pre($_SERVER);
		if (!has_permission('settings', '', 'view')) {
            access_denied('settings');
        }
		
		if ($this->input->post()) {
            if (!has_permission('settings', '', 'edit')) {
                access_denied('settings');
            }
           

            $post_data = $this->input->post();
			if (isset($post_data['settings']['leads_list_column'])) {
                $post_data['settings']['leads_list_column'] = json_encode($post_data['settings']['leads_list_column']);
            }
			$success = $this->settings_model->update($post_data);

		}
		$this->load->library('user_agent');
		redirect($this->agent->referrer());
    }
    public function client_list_column()
    {
		 //pre($_SERVER);
		if (!has_permission('settings', '', 'view')) {
            access_denied('settings');
        }
		
		if ($this->input->post()) {
            if (!has_permission('settings', '', 'edit')) {
                access_denied('settings');
            }
           

            $post_data = $this->input->post();
			if (isset($post_data['settings']['clients_list_column'])) {
                $post_data['settings']['clients_list_column_order'] = json_encode($post_data['settings']['clients_list_column']);
            }
			$success = $this->settings_model->update($post_data);

		}
		$this->load->library('user_agent');
		redirect($this->agent->referrer());
    }

    public function contacts_list_column()
    {
		 //pre($_SERVER);
		if (!has_permission('settings', '', 'view')) {
            access_denied('settings');
        }
		
		if ($this->input->post()) {
            if (!has_permission('settings', '', 'edit')) {
                access_denied('settings');
            }
           

            $post_data = $this->input->post();
			if (isset($post_data['settings']['contacts_list_column'])) {
                $post_data['settings']['contacts_list_column_order'] = json_encode($post_data['settings']['contacts_list_column']);
            }
			$success = $this->settings_model->update($post_data);

		}
		$this->load->library('user_agent');
		redirect($this->agent->referrer());
    }
	
	public function remove_signature_image()
    {
        if (!has_permission('settings', '', 'delete')) {
            access_denied('settings');
        }

        $sImage = get_option('signature_image');
        if (file_exists(get_upload_path_by_type('company') . '/' . $sImage)) {
            unlink(get_upload_path_by_type('company') . '/' . $sImage);
        }

        update_option('signature_image', '');

        redirect(admin_url('settings?group=pdf&tab=signature'));
    }

    /* Remove company logo from settings / ajax */
    public function remove_company_logo($type = '')
    {
        hooks()->do_action('before_remove_company_logo');

        if (!has_permission('settings', '', 'delete')) {
            access_denied('settings');
        }

        $logoName = get_option('company_logo');
        if ($type == 'dark') {
            $logoName = get_option('company_logo_dark');
        }

        $path = get_upload_path_by_type('company') . '/' . $logoName;
        if (file_exists($path)) {
            unlink($path);
        }

        update_option('company_logo' . ($type == 'dark' ? '_dark' : ''), '');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function remove_favicon()
    {
        hooks()->do_action('before_remove_favicon');
        if (!has_permission('settings', '', 'delete')) {
            access_denied('settings');
        }
        if (file_exists(get_upload_path_by_type('company') . '/' . get_option('favicon'))) {
            unlink(get_upload_path_by_type('company') . '/' . get_option('favicon'));
        }
        update_option('favicon', '');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function delete_option($name)
    {
        if (!has_permission('settings', '', 'delete')) {
            access_denied('settings');
        }

        echo json_encode([
            'success' => delete_option($name),
        ]);
    }
}
