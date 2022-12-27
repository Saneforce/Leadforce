<?php

header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Leads extends AdminController
{
    public function __construct()
    {
        parent::__construct();
		$this->load->model('projects_model');
        $this->load->model('leads_model');
		$this->load->model('pipeline_model');
		$this->load->library('user_agent');
		$this->load->model('callsettings_model');
		$this->load->helper('timeline_helper');
		if (strpos($this->agent->referrer(), '/leads') !== false) {
		}
		else {
			// $this->session->set_userdata([
			// 	'leads_kanban_view' => 'false',
			// 	'switch_kanban_noscroll' => 'true',
			// 	'switch_forecast' => 'false',
			// ]);
		}
    }

    /* List all leads */
    public function index($id = '')
    {
        close_setup_menu();
        if (!is_staff_member()) {
            access_denied('Leads');
        }

        //$data['switch_kanban'] = true;

        if ($this->session->userdata('leads_kanban_view') == 'true') {
            //$data['switch_kanban'] = false;
            //$data['bodyclass']     = 'kan-ban-body';
        }

        
        //pre($data);
        if (is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1') {
            $this->load->model('gdpr_model');
            $data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();
        }
        $data['summary']  = get_leads_summary();
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources']  = $this->leads_model->get_source();
        $data['title']    = _l('leads');
        
        if(!is_admin(get_staff_user_id())) {
            $low_hie = '';
            $lowlevel = $this->staff_model->printHierarchyTree(get_staff_user_id(),$prefix = '');
            if(!empty($lowlevel)) {
                $low_hie = ' OR staffid IN ('.implode(',', $lowlevel).')';
            }
            $staffdetails =  $this->db->query('SELECT *, staffid as staff_id FROM ' . db_prefix() . 'staff WHERE staffid = "'.get_staff_user_id().'"'.$low_hie)->result_array();
            $newarr = array(array('staffid' => '', 'firstname' => 'Nothing', 'lastname' => 'selected'));
            $data['staff'] = array_merge($newarr, $staffdetails);
        } else {
            $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        }
        
        //pre($data['sources']);
        // in case accesed the url leads/index/ directly with id - used in search
        $data['leadid'] = $id;
        $this->load->view('admin/leads/manage_leads', $data);
    }

    public function table()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
        $this->app->get_table_data('leads');
    }

    public function kanban()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
		if (!empty($this->session->userdata('pipeline'))) {
			$pipeline = $this->session->userdata('pipeline');
			$data['statuses'] = $this->pipeline_model->getPipelineleadstatus($pipeline);
		}
		else {
			$pipeline = '';
			$data['statuses'] = $this->leads_model->get_status();
		}
		$data['selectedpipeline'] = $pipeline;
        echo $this->load->view('admin/leads/kan-ban', $data, true);
    }

    /* Add or update lead */
    public function lead($id = '')
    {
        if (!is_staff_member() || ($id != '' && !$this->leads_model->staff_can_access_lead($id))) {
            ajax_access_denied();
        }
        if ($this->input->post()) {
            if ($id == '') {
                $id      = $this->leads_model->add($this->input->post());
                $message = $id ? _l('added_successfully', _l('lead')) : '';

                echo json_encode([
                    'success'  => $id ? true : false,
                    'id'       => $id,
                    'message'  => $message,
                ]);
                die;
            } else {
                $emailOriginal   = $this->db->select('email')->where('id', $id)->get(db_prefix() . 'leads')->row()->email;
                $proposalWarning = false;
                $message         = '';
                $success         = $this->leads_model->update($this->input->post(), $id);

                if ($success) {
                    $emailNow = $this->db->select('email')->where('id', $id)->get(db_prefix() . 'leads')->row()->email;

                    $proposalWarning = (total_rows(db_prefix() . 'proposals', [
                        'rel_type' => 'lead',
                        'rel_id'   => $id, ]) > 0 && ($emailOriginal != $emailNow) && $emailNow != '') ? true : false;

                    $message = _l('updated_successfully', _l('lead'));
                }
                echo json_encode([
                    'success'  => $success,
                    'id'       => $id,
                    'message'  => $message,
                ]);
                die;
            }
        }
        if($id){
            echo $this->_get_lead_data($id);
        }else{
            echo json_encode([
                'leadView' => $this->_get_lead_data($id),
            ]);
        }
        
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
		redirect(admin_url('leads'));
    }

    private function _get_lead_data($id = '')
    {
        $reminder_data       = '';
        $data['lead_locked'] = false;
        $data['openEdit']    = $this->input->get('edit') ? true : false;
        $data['members']     = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        
        //$data['status_id']   = $this->input->get('status_id') ? $this->input->get('status_id') : get_option('leads_default_status');
        $data['pipeline_id']   = $this->input->get('pipeline_id') ? $this->input->get('pipeline_id') : '';
		
		if(!empty($id)) {
			$this->db->where('id', $id);
            $lead_details = $this->db->get(db_prefix() . 'leads')->row_array();
            $this->db->where('id', $lead_details['source']);
            $source   = $this->db->get(db_prefix() . 'leads_sources')->row();
            $data['source'] = $source->name;
			$data['teamleaders'] = $this->pipeline_model->getPipelineTeamleaders($lead_details['pipeline_id']);
			$data['teammembers'] = $this->pipeline_model->getPipelineTeammembers($lead_details['pipeline_id']);
            $data['statuses'] = $this->pipeline_model->getPipelineleadstatus($lead_details['pipeline_id']);
            $data['sources']  = $this->leads_model->get_source();
		}
		else {
			$data['teamleaders'] = $this->staff_model->get('', ['role' => 2, 'active' => 1]);
			$data['teammembers'] = $this->staff_model->get('', ['role' => 1, 'active' => 1]);
            $data['statuses'] = $this->leads_model->get_status();
            $data['sources']  = $this->leads_model->get_source();
		}
        if (is_numeric($id)) {
            $leadWhere = (has_permission('leads', '', 'view') ? [] : '(assigned = ' . get_staff_user_id() . ' OR addedfrom=' . get_staff_user_id() . ' OR is_public=1)');

            $lead = $this->leads_model->get($id, $leadWhere);

            if (!$lead) {
                header('HTTP/1.0 404 Not Found');
                echo _l('lead_not_found');
                die;
            }
            $data['lead_person_details'] =[];
            $data['lead_clients_details'] =[];
            $lead_contact_id =$this->leads_model->get_lead_contact($lead->id);
            if($lead->client_id){
                $data['lead_clients_details'] =$this->clients_model->get($lead->client_id);
            }
            if($lead_contact_id){
                $this->db->where('id',$lead_contact_id->contacts_id);
                $person_details =$this->db->get(db_prefix().'contacts')->row();
                $data['lead_person_details']= $person_details;
            }
            if (total_rows(db_prefix() . 'clients', ['leadid' => $id ]) > 0) {
                $data['lead_locked'] = ((!is_admin() && get_option('lead_lock_after_convert_to_customer') == 1) ? true : false);
            }

            $reminder_data = $this->load->view('admin/includes/modals/reminder', [
                    'id'             => $lead->id,
                    'name'           => 'lead',
                    'members'        => $data['members'],
                    'reminder_title' => _l('lead_set_reminder_title'),
                ], true);

            $data['lead']          = $lead;
            $data['mail_activity'] = $this->leads_model->get_mail_activity($id);
            $data['notes']         = $this->misc_model->get_notes($id, 'lead');
            $data['activity_log']  = $this->leads_model->get_lead_activity_log($id);

            if (is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1') {
                $this->load->model('gdpr_model');
                $data['purposes'] = $this->gdpr_model->get_consent_purposes($lead->id, 'lead');
                $data['consents'] = $this->gdpr_model->get_consents(['lead_id' => $lead->id]);
            }
            
        }

        $products = $this->products_model->getleads_products($id, $lead->lead_currency);
        $data['productscnt'] = count($products);
        $data['logs_count'] = $this->leads_model->get_logs_count($lead->id);
        $data['emails_count'] = $this->leads_model->get_emails_count($lead->id);
        $data['activity_count'] = $this->leads_model->get_activities_count($lead->id);
        $data['proposal_count'] = $this->leads_model->get_proposal_count($lead->id);
        $data['files_count'] = $this->leads_model->get_files_count($lead->id);
        $data['notes_count'] = $this->leads_model->get_notes_count($lead->id);
        $data['leadproducts'] = $products;
        $data['lead_currency'] = $lead->lead_currency;
        $discount_value = 0;
        foreach($data['leadproducts'] as $value) {
            if($value['discount'] > 0) {
                $discount_value = 1;
            }
        }
        $data['discount_value'] = $discount_value;

		$data['pipelines'] = $this->pipeline_model->getPipeline();
		$this->load->model('currencies_model');	
        $data['currencies'] = $this->currencies_model->get();	
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        if(!$data['lead_currency']){
            $data['lead_currency'] = $data['base_currency']->name;
        }
        $data = hooks()->apply_filters('lead_view_data', $data);
        
        $data['client_contacts']     = $this->clients_model->getAllContacts_active();

        $data['allcurrency'] = $this->projects_model->get_allcurrency();
        $currency = $this->currencies_model->get_base_currency();
        $this->load->model('invoice_items_model');
        $data['products'] = $this->invoice_items_model->get_items($currency->name);
        $data['discount_option'] = get_option('product_discount_option');

        $table = db_prefix() . 'template';
        $staffid = get_staff_user_id();
        $cond = array('user_id'=>$staffid);
        $templates = $this->db->where($cond)->get($table)->result_array();
        $data['templates'] = $templates;
        $data['default_val'] = '';
        if(!empty($templates[0]['description'])){
            $data['default_val'] = $templates[0]['description'];
        }

        $data ['group'] =$this->input->get('group');
        if(!$data['group']){
            $data['group'] ='lead_activity';
        }
        if($id){
            echo $this->load->view('admin/leads/leadview', $data, true);
        }
        else{
            return [
                'data'          => $this->load->view('admin/leads/lead', $data, true),
                'reminder_data' => $reminder_data,
            ];
        }
        
    }

    public function leads_kanban_load_more()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }

        $status = $this->input->get('status');
        $page   = $this->input->get('page');

        $this->db->where('id', $status);
        $status = $this->db->get(db_prefix() . 'leads_status')->row_array();

        $leads = $this->leads_model->do_kanban_query($status['id'], $this->input->get('search'), $page, [
            'sort_by' => $this->input->get('sort_by'),
            'sort'    => $this->input->get('sort'),
        ]);

        foreach ($leads as $lead) {
            $this->load->view('admin/leads/_kan_ban_card', [
                'lead'   => $lead,
                'status' => $status,
            ]);
        }
    }

    public function switch_kanban($set = 0)
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }
        $this->session->set_userdata([
            'leads_kanban_view' => $set,
			'switch_kanban_noscroll' => 'false',
			'switch_forecast' => 'false'
        ]);
        redirect($_SERVER['HTTP_REFERER']);
    }
	
	public function switch_kanban_noscroll($set = 0)
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }
        $this->session->set_userdata([
			'leads_kanban_view' => 'false',
			'switch_forecast' => 'false',
            'switch_kanban_noscroll' => $set,
        ]);
        redirect($_SERVER['HTTP_REFERER']);
    }
	public function switch_forecast($set = 0)
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }
        $this->session->set_userdata([
			'leads_kanban_view' => 'false',
			'switch_kanban_noscroll' => 'false',
            'switch_forecast' => $set,
        ]);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function export($id)
    {
        if (is_admin()) {
            $this->load->library('gdpr/gdpr_lead');
            $this->gdpr_lead->export($id);
        }
    }

    /* Delete lead from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('leads'));
        }

        if (!is_lead_creator($id) && !has_permission('leads', '', 'delete')) {
            access_denied('Delte Lead');
        }

        $response = $this->leads_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('lead_lowercase')));
        } elseif ($response === true) {
            set_alert('success', _l('deleted', _l('lead')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('lead_lowercase')));
        }
        $ref = $_SERVER['HTTP_REFERER'];
        $ref =admin_url('leads');
        // if user access leads/inded/ID to prevent redirecting on the same url because will throw 404
        if (!$ref || strpos($ref, 'index/' . $id) !== false) {
            redirect(admin_url('leads'));
        }

        redirect($ref);
    }

    public function mark_as_lost($id)
    {
        if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($id)) {
            ajax_access_denied();
        }
        $message = '';
        $success = $this->leads_model->mark_as_lost($id);
        if ($success) {
            $message = _l('lead_marked_as_lost');
        }
        echo json_encode([
            'success'  => $success,
            'message'  => $message,
            'id'       => $id,
            'redirect'=>admin_url('leads/lead/'.$id)
        ]);
    }

    public function unmark_as_lost($id)
    {
        if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($id)) {
            ajax_access_denied();
        }
        $message = '';
        $success = $this->leads_model->unmark_as_lost($id);
        if ($success) {
            $message = _l('lead_unmarked_as_lost');
        }
        echo json_encode([
            'success'  => $success,
            'message'  => $message,
            'id'       => $id,
            'redirect'=>admin_url('leads/lead/'.$id)
        ]);
    }

    public function mark_as_junk($id)
    {
        if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($id)) {
            ajax_access_denied();
        }
        $message = '';
        $success = $this->leads_model->mark_as_junk($id);
        if ($success) {
            $message = _l('lead_marked_as_junk');
        }
        echo json_encode([
            'success'  => $success,
            'message'  => $message,
            'id'       => $id,
            'redirect'=>admin_url('leads/lead/'.$id)
        ]);
    }

    public function unmark_as_junk($id)
    {
        if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($id)) {
            ajax_access_denied();
        }
        $message = '';
        $success = $this->leads_model->unmark_as_junk($id);
        if ($success) {
            $message = _l('lead_unmarked_as_junk');
        }
        echo json_encode([
            'success'  => $success,
            'message'  => $message,
            'id'       => $id,
            'redirect'=>admin_url('leads/lead/'.$id)
        ]);
    }

    public function add_activity()
    {
        $leadid = $this->input->post('leadid');
        if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($leadid)) {
            ajax_access_denied();
        }
        if ($this->input->post()) {
            $message = $this->input->post('activity');
            $aId     = $this->leads_model->log_lead_activity($leadid, $message);
            if ($aId) {
                $this->db->where('id', $aId);
                $this->db->update(db_prefix() . 'lead_activity_log', ['custom_activity' => 1]);
            }
            echo json_encode(['leadView' => $this->_get_lead_data($leadid), 'id' => $leadid]);
        }
    }

    public function get_convert_data($id)
    {
        if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($id)) {
            ajax_access_denied();
        }
        if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1') {
            $this->load->model('gdpr_model');
            $data['purposes'] = $this->gdpr_model->get_consent_purposes($id, 'lead');
        }
        $data['lead'] = $this->leads_model->get($id);
        $data['pipelines'] = $this->pipeline_model->getPipeline();
        $data['statuses'] = $this->leads_model->get_status();
        $data['teamleaders']  = $this->staff_model->get('', ['active' => 1]);
        $data['teammembers']  = $this->staff_model->get('', ['role' => 1, 'active' => 1]);
        $this->load->view('admin/leads/convert_to_customer', $data);
    }
    public function leads()
    {
        if (!has_permission('leads', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data('leads');
    }
    /**
     * Convert lead to client
     * @since  version 1.0.1
     * @return mixed
     */
    public function convert_to_customer()
    {
        if (!is_staff_member()) {
            access_denied('Lead Convert to Customer');
        }

        if ($this->input->post()) {
            $default_country  = get_option('customer_default_country');
            $data             = $this->input->post();
            //$data['password'] = $this->input->post('password', false);

            $original_lead_email = $data['original_lead_email'];
            unset($data['original_lead_email']);

            $notes = $this->misc_model->get_notes($data['leadid'], 'lead');
            unset($data['transfer_notes']);

            $files = $this->misc_model->get_files($data['leadid'], 'lead');

            if (isset($data['transfer_consent'])) {
                $this->load->model('gdpr_model');
                $consents = $this->gdpr_model->get_consents(['lead_id' => $data['leadid']]);
                unset($data['transfer_consent']);
            }

            if (isset($data['merge_db_fields'])) {
                $merge_db_fields = $data['merge_db_fields'];
                unset($data['merge_db_fields']);
            }

            if (isset($data['merge_db_contact_fields'])) {
                $merge_db_contact_fields = $data['merge_db_contact_fields'];
                unset($data['merge_db_contact_fields']);
            }

            if (isset($data['include_leads_custom_fields'])) {
                $include_leads_custom_fields = $data['include_leads_custom_fields'];
                unset($data['include_leads_custom_fields']);
            }

            if ($data['country'] == '' && $default_country != '') {
                $data['country'] = $default_country;
            }

            $data['billing_street']  = $data['address'];
            $data['billing_city']    = $data['city'];
            $data['billing_state']   = $data['state'];
            $data['billing_zip']     = $data['zip'];
            $data['billing_country'] = $data['country'];
			$data['progress'] = $this->projects_model->getprogressstatus($data['status']);
            $data['is_primary'] = 1;

            $this->db->insert(db_prefix() . 'projects', [
                'name'         => $data['name'],
                'status'         => $data['status'],
                'pipeline_id'       => $data['pipeline'],
                'teamleader'      => $data['teamleader'],
                'progress'      => $data['progress'],
                'start_date'      => date('Y-m-d'),
                'project_created'    => date('Y-m-d'),
                'created_by'    => get_staff_user_id(),
            ]);
            $deal_id = $this->db->insert_id();
            if($deal_id) {
                if (isset($notes)) {
                    foreach ($notes as $note) {
                        $this->db->insert(db_prefix() . 'project_notes', [
                            'project_id'         => $deal_id,
                            'content'         => $note['description'],
                            'staff_id'       => $note['staffid'],
                            'dateadded'      => $note['dateadded']
                        ]);
                    }
                }

                if (isset($files)) {
                    foreach ($files as $file) {
                        $this->db->insert(db_prefix() . 'project_files', [
                            'project_id'         => $deal_id,
                            'file_name'         => $file['file_name'],
                            'subject'       => $file['file_name'],
                            'filetype'      => $file['filetype'],
                            'dateadded'      => $file['dateadded'],
                            'staffid'      => $file['staffid']
                        ]);
                    }
                }
            }
            if(empty($data['company'])) {
                $data['company'] = $data['name'];
            }
            //pre($data);
            unset($data['name']);
            unset($data['status']);
            unset($data['pipeline']);
            unset($data['teamleader']);
            $clientid                 = $this->clients_model->add($data, true);
            if ($clientid) {
                if(isset($data['firstname'])) {
                    $this->db->insert(db_prefix() . 'contacts', [
                        'userid'         => $clientid,
                        'userids'         => $clientid,
                        'firstname'       => $data['firstname'],
                        'email'      => $data['email'],
                        'phonenumber'      => $data['phonenumber'],
                        'title'    => $data['title'],
                        'is_primary' => 1
                        ]);
                    $primary_contact_id = $this->db->insert_id();
                }
            }
            // } else {
            //     if(isset($data['firstname'])) {
            //         $this->db->insert(db_prefix() . 'contacts', [
            //             'firstname'       => $data['firstname'],
            //             'email'      => $data['email'],
            //             'phonenumber'      => $data['phonenumber'],
            //             'position'    => $data['title'],
            //             'is_primary' => 1
            //             ]);
            //         $primary_contact_id = $this->db->insert_id();
            //     }
            // }

            if($primary_contact_id) {
                $this->db->insert(db_prefix() . 'project_contacts', [
                    'project_id'       => $deal_id,
                    'contacts_id'      => $primary_contact_id,
                    'is_primary' => 1
                ]);
            }
            if($clientid) {
                $this->db->where('id', $deal_id);
                $this->db->update(db_prefix() . 'projects', ['clientid' => $clientid]);
            }
                    
            $this->db->where('id', $deal_id);
            $this->db->update(db_prefix() . 'projects', ['lead_id' => $data['leadid'], 'project_currency' => 'INR']);

            if($primary_contact_id) {
                $this->db->where('rel_id', $data['leadid']);
                $this->db->where('rel_type', 'lead');
                $this->db->update(db_prefix() . 'tasks', ['rel_type' => 'project', 'rel_id' => $deal_id, 'contacts_id' => $primary_contact_id]);
            } else {
                $this->db->where('rel_id', $data['leadid']);
                $this->db->where('rel_type', 'lead');
                $this->db->update(db_prefix() . 'tasks', ['rel_type' => 'project', 'rel_id' => $deal_id]);
            }

            $this->db->where('rel_id', $data['leadid']);
            $this->db->where('rel_type', 'lead');
            $this->db->update(db_prefix() . 'proposals', ['rel_type' => 'project', 'rel_id' => $deal_id]);

            $this->db->where('id', $data['leadid']);
            $this->db->update(db_prefix() . 'leads', ['project_id' => $deal_id, 'deleted_status' => 1]);
            
            // hooks()->do_action('after_add_project_approval', $deal_id);
        
            set_alert('success', _l('lead_to_client_base_converted_success'));

            redirect(admin_url('projects/view/'.$deal_id.'?group=project_overview'));
        }      
        
    }

    public function convert_to_lead($id)
    {
        $this->db->where('id', $id);
        $leadid = $this->db->get(db_prefix() . 'projects')->row()->lead_id;

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'projects', ['deleted_status' => 1]);

        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'project');
        $this->db->update(db_prefix() . 'tasks', ['rel_type' => 'lead', 'rel_id' => $leadid]);

        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'project');
        $this->db->update(db_prefix() . 'proposals', ['rel_type' => 'lead', 'rel_id' => $leadid]);

        // $notes = $this->misc_model->get_notes($data['leadid'], 'lead');

        // $files = $this->misc_model->get_files($data['leadid'], 'lead');
        // if($deal_id) {
        //     if (isset($notes)) {
        //         foreach ($notes as $note) {
        //             $this->db->insert(db_prefix() . 'project_notes', [
        //                 'project_id'         => $deal_id,
        //                 'content'         => $note['description'],
        //                 'staff_id'       => $note['staffid'],
        //                 'dateadded'      => $note['dateadded']
        //             ]);
        //         }
        //     }

        //     if (isset($files)) {
        //         foreach ($files as $file) {
        //             $this->db->insert(db_prefix() . 'project_files', [
        //                 'project_id'         => $deal_id,
        //                 'file_name'         => $file['file_name'],
        //                 'subject'       => $file['file_name'],
        //                 'filetype'      => $file['filetype'],
        //                 'dateadded'      => $file['dateadded'],
        //                 'staffid'      => $file['staffid']
        //             ]);
        //         }
        //     }
        // }

        $this->db->where('id', $leadid);
        $this->db->update(db_prefix() . 'leads', [ 'deleted_status' => 0]);
        set_alert('success', _l('deal_to_lead_base_converted_success'));

        redirect(admin_url('leads/index/'.$leadid));
    }

    public function convert_lead_to_existing_deal($id)
    {
        $this->db->where('id', $id);
        $projid = $this->db->get(db_prefix() . 'leads')->row()->project_id;

        $this->db->where('id', $projid);
        $this->db->update(db_prefix() . 'projects', ['deleted_status' => 0]);

        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'lead');
        $this->db->update(db_prefix() . 'tasks', ['rel_type' => 'project', 'rel_id' => $projid]);

        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'lead');
        $this->db->update(db_prefix() . 'proposals', ['rel_type' => 'project', 'rel_id' => $projid]);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'leads', ['project_id' => $projid, 'deleted_status' => 1]);
                
        set_alert('success', _l('lead_to_client_base_converted_success'));

        redirect(admin_url('projects/view/'.$projid.'?group=project_overview'));
    }

    /* Used in kanban when dragging and mark as */
    public function update_lead_status()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            $this->leads_model->update_lead_status($this->input->post());
        }
    }

    public function update_status_order()
    {
        if ($post_data = $this->input->post()) {
            $this->leads_model->update_status_order($post_data);
        }
    }

    public function add_lead_attachment()
    {
        $id       = $this->input->post('id');
        $lastFile = $this->input->post('last_file');

        if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($id)) {
            ajax_access_denied();
        }

        handle_lead_attachments($id);
        echo json_encode(['leadView' => $lastFile ? $this->_get_lead_data($id) : [], 'id' => $id]);
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->leads_model->add_attachment_to_database(
                $this->input->post('lead_id'),
                $this->input->post('files'),
                $this->input->post('external')
            );
        }
    }

    public function delete_attachment($id, $lead_id)
    {
        if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($lead_id)) {
            ajax_access_denied();
        }
        echo json_encode([
            'success' => $this->leads_model->delete_lead_attachment($id),
        ]);
    }

    public function delete_note($id, $lead_id)
    {
        if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($lead_id)) {
            ajax_access_denied();
        }
        echo json_encode([
            'success' => $this->misc_model->delete_note($id),
        ]);
    }

    public function update_all_proposal_emails_linked_to_lead($id)
    {
        $success = false;
        $email   = '';
        if ($this->input->post('update')) {
            $this->load->model('proposals_model');

            $this->db->select('email');
            $this->db->where('id', $id);
            $email = $this->db->get(db_prefix() . 'leads')->row()->email;

            $proposals = $this->proposals_model->get('', [
                'rel_type' => 'lead',
                'rel_id'   => $id,
            ]);
            $affected_rows = 0;

            foreach ($proposals as $proposal) {
                $this->db->where('id', $proposal['id']);
                $this->db->update(db_prefix() . 'proposals', [
                    'email' => $email,
                ]);
                if ($this->db->affected_rows() > 0) {
                    $affected_rows++;
                }
            }

            if ($affected_rows > 0) {
                $success = true;
            }
        }

        echo json_encode([
            'success' => $success,
            'message' => _l('proposals_emails_updated', [
                _l('lead_lowercase'),
                $email,
            ]),
        ]);
    }

    public function save_form_data()
    {
        $data = $this->input->post();

        // form data should be always sent to the request and never should be empty
        // this code is added to prevent losing the old form in case any errors
        if (!isset($data['formData']) || isset($data['formData']) && !$data['formData']) {
            echo json_encode([
                'success' => false,
            ]);
            die;
        }

        // If user paste with styling eq from some editor word and the Codeigniter XSS feature remove and apply xss=remove, may break the json.
        $data['formData'] = preg_replace('/=\\\\/m', "=''", $data['formData']);

        $this->db->where('id', $data['id']);
        $this->db->update(db_prefix() . 'web_to_lead', [
            'form_data' => $data['formData'],
        ]);
        if ($this->db->affected_rows() > 0) {
            echo json_encode([
                'success' => true,
                'message' => _l('updated_successfully', _l('web_to_lead_form')),
            ]);
        } else {
            echo json_encode([
                'success' => false,
            ]);
        }
    }

    public function form($id = '')
    {
        if (!is_admin()) {
            access_denied('Web To Lead Access');
        }
        if ($this->input->post()) {
            if ($id == '') {
                $data = $this->input->post();
                $id   = $this->leads_model->add_form($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('web_to_lead_form')));
                    redirect(admin_url('leads/form/' . $id));
                }
            } else {
                $success = $this->leads_model->update_form($id, $this->input->post());
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('web_to_lead_form')));
                }
                redirect(admin_url('leads/form/' . $id));
            }
        }

        $data['formData'] = [];
        $custom_fields    = get_custom_fields('leads', 'type != "link"');

        $cfields       = format_external_form_custom_fields($custom_fields);
        $data['title'] = _l('web_to_lead');

        if ($id != '') {
            $data['form'] = $this->leads_model->get_form([
                'id' => $id,
            ]);
            $data['title']    = $data['form']->name . ' - ' . _l('web_to_lead_form');
            $data['formData'] = $data['form']->form_data;
        }

        $this->load->model('roles_model');
        $data['roles']    = $this->roles_model->get();
        $data['statuses'] = $this->leads_model->get_status();
		
		$data['teamleaders'] = $this->staff_model->get('', [
            'active'       => 1,
            'role' => 2,
        ]);

        $data['members'] = $this->staff_model->get('', [
            'active'       => 1,
            'action_for'       => 'Active',
            //'role' => 1,
        ]);

        $data['languages'] = $this->app->get_available_languages();
        $data['cfields']   = $cfields;

        $db_fields = [];
        $fields    = [
            'name',
            'title',
            'email',
            'phonenumber',
            'company',
            'address',
            'city',
            'state',
            'country',
            'zip',
            'description',
            'website',
        ];

        $fields = hooks()->apply_filters('lead_form_available_database_fields', $fields);

        $className = 'form-control';

        foreach ($fields as $f) {
            $_field_object = new stdClass();
            $type          = 'text';
            $subtype       = '';
            if ($f == 'email') {
                $subtype = 'email';
            } elseif ($f == 'description' || $f == 'address') {
                $type = 'textarea';
            } elseif ($f == 'country') {
                $type = 'select';
            }

            if ($f == 'name') {
                $label = _l('lead_add_edit_name');
            } elseif ($f == 'email') {
                $label = _l('lead_add_edit_email');
            } elseif ($f == 'phonenumber') {
                $label = _l('lead_add_edit_phonenumber');
            } else {
                $label = _l('lead_' . $f);
            }

            $field_array = [
                'subtype'   => $subtype,
                'type'      => $type,
                'label'     => $label,
                'className' => $className,
                'name'      => $f,
            ];

            if ($f == 'country') {
                $field_array['values'] = [];

                $field_array['values'][] = [
                    'label'    => '',
                    'value'    => '',
                    'selected' => false,
                ];

                $countries = get_all_countries();
                foreach ($countries as $country) {
                    $selected = false;
                    if (get_option('customer_default_country') == $country['country_id']) {
                        $selected = true;
                    }
                    array_push($field_array['values'], [
                        'label'    => $country['short_name'],
                        'value'    => (int) $country['country_id'],
                        'selected' => $selected,
                    ]);
                }
            }

            if ($f == 'name') {
                $field_array['required'] = true;
            }

            $_field_object->label    = $label;
            $_field_object->name     = $f;
            $_field_object->fields   = [];
            $_field_object->fields[] = $field_array;
            $db_fields[]             = $_field_object;
        }
        $data['bodyclass'] = 'web-to-lead-form';
        $data['db_fields'] = $db_fields;
        $data['sources']  = $this->leads_model->get_source();
        $this->load->view('admin/leads/formbuilder', $data);
    }

    public function forms($id = '')
    {
        if (!is_admin()) {
            access_denied('Web To Lead Access');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('web_to_lead');
        }

        $data['title'] = _l('web_to_lead');
        $this->load->view('admin/leads/forms', $data);
    }

    public function delete_form($id)
    {
        if (!is_admin()) {
            access_denied('Web To Lead Access');
        }

        $success = $this->leads_model->delete_form($id);
        if ($success) {
            set_alert('success', _l('deleted', _l('web_to_lead_form')));
        }

        redirect(admin_url('leads/forms'));
    }

    /* Manage leads sources */
    public function sources()
    {
        if (!is_admin()) {
            access_denied('Leads Sources');
        }
        $data['indiaMart'] = array('UNIQUE_QUERY_ID' => 'QUERY_ID',
        'QUERY_TYPE' => 'QTYPE',
        'SENDER_NAME' => 'SENDERNAME',
        'SENDER_EMAIL' => 'SENDERMAIL',
        'SENDER_MOBILE' => 'MOB',
       // 'GLUSER_USR_COMPANYNAME' => 'GLUSER_USR_COMPANYNAME',
        'SENDER_ADDRESS' => 'ENQ_ADDRESS',
        'SENDER_CITY' => 'ENQ_CITY',
        'ENQ_STATE' => 'ENQ_STATE',
        'SENDER_COUNTRY_ISO' => 'COUNTRY_ISO',
        'QUERY_PRODUCT_NAME' => 'PRODUCT_NAME',
        'QUERY_MESSAGE' => 'ENQ_MESSAGE',
        //'DATE_RE' => 'DATE_RE',
      //  'DATE_R' => 'DATE_R',
       // 'DATE_TIME_RE' => 'DATE_TIME_RE',
       // 'LOG_TIME' => 'LOG_TIME',
  //      'QUERY_MODID' => 'QUERY_MODID',
        'CALL_DURATION' => 'ENQ_CALL_DURATION',
        'RECEIVER_MOBILE' => 'ENQ_RECEIVER_MOB',
        'SENDER_EMAIL_ALT' => 'EMAIL_ALT',
        'SENDER_MOBILE_ALT' => 'MOBILE_ALT');
        $data['sources'] = $this->leads_model->get_source_admin();
        $data['members']     = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        $data['title']   = 'Leads sources';
        $this->db->where('slug', 'indiamart');
        $field_val = $this->db->get(db_prefix() . 'leads_sources')->row();
        $fvs = json_decode($field_val->fields);
        $data['fvs']   = $fvs;
        $data['result']   = $field_val;
        //pre($fvs);
        
        if(isset($fvs->lead_company)) {
            $c = explode(',',$fvs->lead_company);
            $i = 0;
            foreach($c as $val) {
                $company.$i = $val;
                $i++;
            }
            
        }
        $data['name'] = explode(',',$fvs->name);;
        $data['lead_company'] = explode(',',$fvs->lead_company);
        
        $this->load->view('admin/leads/manage_sources', $data);
    }

    /* Add or update leads sources */
    public function source()
    {
        if (!is_admin() && get_option('staff_members_create_inline_lead_source') == '0') {
            access_denied('Leads Sources');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $inline = isset($data['inline']);
                if (isset($data['inline'])) {
                    unset($data['inline']);
                }
                $data['slug'] = strtolower($data['name']);
                $this->db->select('slug');
                $this->db->where('slug', $data['slug']);
                $slug = $this->db->get(db_prefix() . 'leads_sources')->row()->slug;

                if($slug == $data['slug']) {
                    set_alert('warning', 'Record already exist. Please try with different Social Media Name.');
                    return false;
                }
                $id = $this->leads_model->add_source($data);

                if (!$inline) {
                    if ($id) {
                        set_alert('success', _l('added_successfully', _l('lead_source')));
                    }
                } else {
                    echo json_encode(['success' => $id ? true : fales, 'id' => $id]);
                }
            } else {
                $id = $data['id'];
                unset($data['id']);
                //$data['slug'] = strtolower($data['name']);
                $success = $this->leads_model->update_source($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('lead_source')));
                }
            }
        }
    }

    public function merge_fields()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $lead_company = $name = '';
            $updateData = array();
            if($data['name'] != '0') {
                $name = $data['name'];
            }
            if($data['name1'] != '0') {
                if($name != '0') {
                    $name = $name.','.$data['name1'];
                }
            }
            if($data['name2'] != '0') {
                if($name != '0') {
                    $name = $name.','.$data['name2'];
                }
            }
            if($name != '0') {
                $updateData['name'] = $name;
            }
            
            if($data['lead_company'] != '0')
                $lead_company = $data['lead_company'];
            if($data['lead_company1'] != '0') {
                if($lead_company != '0') {
                    $lead_company = $lead_company.','.$data['lead_company1'];
                }
            }
            if($data['lead_company2'] != '0') {
                if($lead_company != '0') {
                    $lead_company = $lead_company.','.$data['lead_company2'];
                }
            }
            if($lead_company != '0') {
                $updateData['lead_company'] = $lead_company;
            }

            if($data['title'] != '0') {
                $updateData['title'] = $data['title'];
            } else {
                $updateData['title'] = '';
            }

            if($data['email'] != '0') {
                $updateData['email'] = $data['email'];
            } else {
                $updateData['email'] = '';
            }

            if($data['website'] != '0') {
                $updateData['website'] = $data['website'];
            } else {
                $updateData['website'] = '';
            }

            if($data['phonenumber'] != '0') {
                $updateData['phonenumber'] = $data['phonenumber'];
            } else {
                $updateData['phonenumber'] = '';
            }

            if($data['address'] != '0') {
                $updateData['address'] = $data['address'];
            } else {
                $updateData['address'] = '';
            }

            if($data['city'] != '0') {
                $updateData['city'] = $data['city'];
            } else {
                $updateData['city'] = '';
            }

            if($data['state'] != '0') {
                $updateData['state'] = $data['state'];
            } else {
                $updateData['state'] = '';
            }

            if($data['country'] != '0') {
                $updateData['country'] = $data['country'];
            } else {
                $updateData['country'] = '';
            }

            if($data['zip'] != '0') {
                $updateData['zip'] = $data['zip'];
            } else {
                $updateData['zip'] = '';
            }

            if($data['assigned'] != '0') {
                $updateData['assigned'] = $data['assigned'];
            } else {
                $updateData['assigned'] = '';
            }

            if($data['custom_fields'] != '0') {
                $updateData['custom_fields'] = $data['custom_fields'];
            } else {
                $updateData['custom_fields'] = '';
            }

            if($data['description'] != '0') {
                $updateData['description'] = $data['description'];
            } else {
                $updateData['description'] = '';
            }

            
            $jsonData = json_encode($updateData);
            $fieldData = array();
            $fieldData['fields'] = $jsonData;
            $fieldData['user_account'] = $data['user_account'];
            $fieldData['unique_key'] = $data['unique_key'];
            
            $id = $data['source_id'];
            //pre($fieldData);
            $success = $this->leads_model->update_source_fields($fieldData, $id);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('lead_source')));
            }
            redirect(admin_url('leads/sources'));
        }
    }

    /* Delete leads source */
    public function delete_source($id)
    {
        if (!is_admin()) {
            access_denied('Delete Lead Source');
        }
        if (!$id) {
            redirect(admin_url('leads/sources'));
        }
        $response = $this->leads_model->delete_source($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('lead_source_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('lead_source')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('lead_source_lowercase')));
        }
        redirect(admin_url('leads/sources'));
    }

    // Statuses
    /* View leads statuses */
    public function statuses()
    {
        if (!is_admin()) {
            access_denied('Leads Statuses');
        }
        $data['statuses'] = $this->leads_model->get_status();
        $data['title']    = 'Leads statuses';
        $this->load->view('admin/leads/manage_statuses', $data);
    }

    /* Add or update leads status */
    public function status()
    {
        if (!is_admin() && get_option('staff_members_create_inline_lead_status') == '0') {
            access_denied('Leads Statuses');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $inline = isset($data['inline']);
                if (isset($data['inline'])) {
                    unset($data['inline']);
                }
                $id = $this->leads_model->add_status($data);
                if (!$inline) {
                    if ($id) {
                        set_alert('success', _l('added_successfully', _l('lead_status')));
                    }
                } else {
                    echo json_encode(['success' => $id ? true : fales, 'id' => $id]);
                }
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->leads_model->update_status($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('lead_status')));
                }
            }
        }
    }

    /* Delete leads status from databae */
    public function delete_status($id)
    {
        if (!is_admin()) {
            access_denied('Leads Statuses');
        }
        if (!$id) {
            redirect(admin_url('leads/statuses'));
        }
        $response = $this->leads_model->delete_status($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('lead_status_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('lead_status')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('lead_status_lowercase')));
        }
        redirect(admin_url('leads/statuses'));
    }

    /* Add new lead note */
    public function add_note($rel_id)
    {
        if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($rel_id)) {
            ajax_access_denied();
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if ($data['contacted_indicator'] == 'yes') {
                $contacted_date         = to_sql_date($data['custom_contact_date'], true);
                $data['date_contacted'] = $contacted_date;
            }

            unset($data['contacted_indicator']);
            unset($data['custom_contact_date']);

            // Causing issues with duplicate ID or if my prefixed file for lead.php is used
            $data['description'] = isset($data['lead_note_description']) ? $data['lead_note_description'] : $data['description'];

            if (isset($data['lead_note_description'])) {
                unset($data['lead_note_description']);
            }

            $note_id = $this->misc_model->add_note($data, 'lead', $rel_id);
            $this->leads_model->log_activity($rel_id,'note','added',$note_id);
            if ($note_id) {
                if (isset($contacted_date)) {
                    $this->db->where('id', $rel_id);
                    $this->db->update(db_prefix() . 'leads', [
                        'lastcontact' => $contacted_date,
                    ]);
                    if ($this->db->affected_rows() > 0) {
                        $this->leads_model->log_lead_activity($rel_id, 'not_lead_activity_contacted', false, serialize([
                            get_staff_full_name(get_staff_user_id()),
                            _dt($contacted_date),
                        ]));
                    }
                }
            }
        }
        set_alert('success', 'Note added successfully');
        redirect(admin_url('leads/lead/'.$rel_id.'?group=lead_notes'));
    }

    public function test_email_integration()
    {
        if (!is_admin()) {
            access_denied('Leads Test Email Integration');
        }

        app_check_imap_open_function(admin_url('leads/email_integration'));

        require_once(APPPATH . 'third_party/php-imap/Imap.php');

        $mail = $this->leads_model->get_email_integration();
        $ps   = $mail->password;
        if (false == $this->encryption->decrypt($ps)) {
            set_alert('danger', _l('failed_to_decrypt_password'));
            redirect(admin_url('leads/email_integration'));
        }
        $mailbox    = $mail->imap_server;
        $username   = $mail->email;
        $password   = $this->encryption->decrypt($ps);
        $encryption = $mail->encryption;
        // open connection
        $imap = new Imap($mailbox, $username, $password, $encryption);

        if ($imap->isConnected() === false) {
            set_alert('danger', _l('lead_email_connection_not_ok') . '<br /><b>' . $imap->getError() . '</b>');
        } else {
            set_alert('success', _l('lead_email_connection_ok'));
        }

        redirect(admin_url('leads/email_integration'));
    }

    public function email_integration()
    {
        if (!is_admin()) {
            access_denied('Leads Email Intregration');
        }
        if ($this->input->post()) {
            $data             = $this->input->post();
            $data['password'] = $this->input->post('password', false);

            if (isset($data['fakeusernameremembered'])) {
                unset($data['fakeusernameremembered']);
            }
            if (isset($data['fakepasswordremembered'])) {
                unset($data['fakepasswordremembered']);
            }

            $success = $this->leads_model->update_email_integration($data);
            if ($success) {
                set_alert('success', _l('leads_email_integration_updated'));
            }
            redirect(admin_url('leads/email_integration'));
        }
        $data['roles']    = $this->roles_model->get();
        $data['statuses'] = $this->leads_model->get_status();

        $data['members'] = $this->staff_model->get('', [
            'active'       => 1,
            'role' => 1,
        ]);
		
		$data['teamleaders'] = $this->staff_model->get('', [
            'active' => 1,
            'role' => 2,
        ]);

        $data['title']     = _l('leads_email_integration');
        $data['mail']      = $this->leads_model->get_email_integration();
        $data['bodyclass'] = 'leads-email-integration';
        $this->load->view('admin/leads/email_integration', $data);
    }

    public function change_status_color()
    {
        if ($this->input->post()) {
            $this->leads_model->change_status_color($this->input->post());
        }
    }

    public function import()
    {
        if (!is_admin() && get_option('allow_non_admin_members_to_import_leads') != '1') {
            access_denied('Leads Import');
        }
        $dbFields = $this->db->list_fields(db_prefix() . 'leads');
        array_push($dbFields, 'tags');
        $this->load->library('import/import_leads', [], 'import');
        $this->import->setDatabaseFields($dbFields)->setCustomFields(get_custom_fields('leads'));
        if ($this->input->post('download_sample') === 'true') {
			$this->import->downloadSample();
        }
        if ($this->input->post() && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
            $this->import->setSimulation($this->input->post('simulate'))
				->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
				->setFilename($_FILES['file_csv']['name'])
				->perform();
            $data['total_rows_post'] = $this->import->totalRows();
            if (!$this->import->isSimulation()) {
                set_alert('success', _l('import_total_imported', $this->import->totalImported()));
            }
        }
		$data['pipelines'] = $this->pipeline_model->getPipeline();
        $data['statuses'] = $this->leads_model->get_status();
        $data['teamleaders']  = $this->staff_model->get('', ['role' => 2, 'active' => 1]);
        $data['teammembers']  = $this->staff_model->get('', ['role' => 1, 'active' => 1]);

        $data['title'] = _l('import');
        $this->load->view('admin/leads/import', $data);
    }

    public function validate_unique_field()
    {
        if ($this->input->post()) {

            // First we need to check if the field is the same
            $lead_id = $this->input->post('lead_id');
            $field   = $this->input->post('field');
            $value   = $this->input->post($field);

            if ($lead_id != '') {
                $this->db->select($field);
                $this->db->where('id', $lead_id);
                $row = $this->db->get(db_prefix() . 'leads')->row();
                if ($row->{$field} == $value) {
                    echo json_encode(true);
                    die();
                }
            }

            echo total_rows(db_prefix() . 'leads', [ $field => $value ]) > 0 ? 'false' : 'true';
        }
    }

    public function bulk_action()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }

        hooks()->do_action('before_do_bulk_action_for_leads');
        $total_deleted = 0;
        if ($this->input->post()) {
            $ids                   = $this->input->post('ids');
            $source                = $this->input->post('source');
            $status                = $this->input->post('status');
            $assigned              = $this->input->post('assigned');
            $visibility            = $this->input->post('visibility');
            $tags                  = $this->input->post('tags');
            $last_contact          = $this->input->post('last_contact');
            $lost                  = $this->input->post('lost');
            $has_permission_delete = has_permission('leads', '', 'delete');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($has_permission_delete) {
                            if ($this->leads_model->delete($id)) {
                                $total_deleted++;
                            }
                        }
                    } else {
                        if ($source || $status || $assigned || $last_contact || $visibility) {
                            $update = [];
                            if ($status) {
                                // We will use the same function to update the status
                                $this->leads_model->update_lead_status([
                                    'status' => $status,
                                    'leadid' => $id,
                                ]);
                            }
                            if ($source) {
                                $update['source'] = $source;
                            }
                            if ($assigned) {
                                $update['assigned'] = $assigned;
                            }
                            if ($last_contact) {
                                $last_contact          = to_sql_date($last_contact, true);
                                $update['lastcontact'] = $last_contact;
                            }

                            if ($visibility) {
                                if ($visibility == 'public') {
                                    $update['is_public'] = 1;
                                } else {
                                    $update['is_public'] = 0;
                                }
                            }

                            if (count($update) > 0) {
                                $this->db->where('id', $id);
                                $this->db->update(db_prefix() . 'leads', $update);
                            }
                        }
                        if ($tags) {
                            handle_tags_save($tags, $id, 'lead');
                        }
                        if ($lost == 'true') {
                            $this->leads_model->mark_as_lost($id);
                        }
                    }
                }
            }
        }

        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_leads_deleted', $total_deleted));
        }
    }
	
	public function switch_pipeline()
    {
		$data = $this->input->post();
        $this->session->set_userdata([
            'pipeline' => $data['pipeline'],
        ]);
		$result['mgs'] = 'Pipeline activated successfully';
		$result['status'] = true;
		echo json_encode($result,true);
		exit;
    }
	
	public function changepipeline()
	{
		$postval = $this->input->post();
		$pipeline = $postval['pipeline_id'];
		
        $members = '<option value=""></option><option value="">All Members</option>';
        $leaders = '<option value="">Select Owner</option>';
		$already_leaders = array();
        $memberslist = $this->pipeline_model->getPipelineTeammembers($pipeline);
        $get_staff_user_id = get_staff_user_id();
        $selected = '';
        if(count($memberslist) == 1){
            $selected = 'selected="selected"';
        }
		if(!empty($memberslist)) {
			foreach($memberslist as $member) {
                $selected = '';
                    if(empty($selected) && $member['id']== $get_staff_user_id){
                        $selected = 'selected';
                    }
					// $already_leaders[] = $member['id'];
                    $members .= '<option value="'.$member['id'].'" '.$selected.'>'.$member['name'].'</option>';
                    // $leaders .= '<option value="'.$member['id'].'" '.$selected.'>'.$member['name'].'</option>';
			}
		}
		
		//Deal Owners list
        $leaderlist = $this->pipeline_model->getPipelineTeamleaders($pipeline);
		if(!empty($leaderlist)) {
			foreach($leaderlist as $leader) {
                $selected = '';
                if(empty($selected) && $leader['id']== $get_staff_user_id){
                    $selected = 'selected';
                    $selectedId = $leader['id'];
                }
                $already_leaders[] = $leader['id'];
                $leaders .= '<option value="'.$leader['id'].'" '.$selected.'>'.$leader['name'].'</option>';
			}
        }
        
        $followers = '';
        //Deal Followers list
        if(!empty($leaderlist)) {
			foreach($leaderlist as $leader) {
                $selected = '';
                if($selectedId != $leader['id']){
                    $followers .= '<option value="'.$leader['id'].'" '.$selected.'>'.$leader['name'].'</option>';
                }
			}
		}
		
		$statuses = '<option value=""></option>';
        $statuseslist = $this->pipeline_model->getPipelineleadstatus($pipeline);
        $selected = ' ';
        if(count($statuseslist) == 1){
            $selected = 'selected="selected"';
        }
		if(!empty($statuseslist)) {
			foreach($statuseslist as $status) {
                if(!empty($status['name'])){
                $statuses .= '<option value="'.$status['id'].'" '.$selected.'>'.$status['name'].'</option>';
                }
			}
		}
		$data = $this->pipeline_model->getPipelineClientDetails($pipeline);
		$data['statuses'] = $statuses;
		$data['teammembers'] = $members;
        $data['teamleaders'] = $leaders;
        $data['followers'] = $followers;
		$data['mgs'] = 'Data loaded successfully';
		$data['status'] = true;
		echo json_encode($data,true);
		exit;
    }
	public function changepipelineteamleader()
	{
		$postval = $this->input->post();
		$teamleader = $postval['teamleader'];
		
        $members = '<option value=""></option>';
        $memberslist = $this->pipeline_model->getPipelineTeamleaderTeammembers($teamleader);
        $selected = ' ';
        if(count($memberslist) == 1){
            $selected = 'selected="selected"';
        }
		if(!empty($memberslist)) {
			foreach($memberslist as $member) {
                if(!empty($member['name'])){
                    $members .= '<option value="'.$member['id'].'" '.$selected.'>'.$member['name'].'</option>';
                }
			}
		}
	
		$data['teammembers'] = $members;
		$data['mgs'] = 'Data loaded successfully';
		$data['status'] = true;
		echo json_encode($data,true);
		exit;
    }
    public function selectAllpipeline()
	{
		$postval = $this->input->post();
		
        $members = '<option value=""></option><option value="">All Members</option>';
        $leaders = '<option value=""></option>';
		$already_leaders = array();
        $memberslist = $this->pipeline_model->getAllPipelineTeammembers();
        $get_staff_user_id = get_staff_user_id();
        $selected = '';
        if(count($memberslist) == 1){
            $selected = 'selected="selected"';
        }
		if(!empty($memberslist)) {
			foreach($memberslist as $member) {
                if(!empty($member['name'])){
                    if(empty($selected) && $member['id']== $get_staff_user_id){
                        $selected = 'selected="selected"';
                    }
					// $already_leaders[] = $member['id'];
                    $members .= '<option value="'.$member['id'].'" '.$selected.'>'.$member['name'].'</option>';
                    // $leaders .= '<option value="'.$member['id'].'" '.$selected.'>'.$member['name'].'</option>';
                }
			}
        }

        $data['teammembers'] = $members;
		$data['mgs'] = 'Data loaded successfully';
		$data['status'] = true;
		echo json_encode($data,true);
		exit;
    }

    public function getpipelineteamember()
	{
        $postval = $this->input->post();
        
 		$teamleader = $postval['leaderid'];
        $pipeline = $postval['pipeline'];
        $members = '';
        $memberslist = $this->pipeline_model->getTeammembersexceptowner($teamleader, $pipeline);
        $selected = ' ';
        if(count($memberslist) == 1){
            $selected = 'selected="selected"';
        }
		if(!empty($memberslist)) {
			foreach($memberslist as $member) {
                if(!empty($member['name'])){
                    $members .= '<option value="'.$member['id'].'" '.$selected.'>'.$member['name'].'</option>';
                }
			}
		}
	
		$data['teammembers'] = $members;
		$data['mgs'] = 'Data loaded successfully';
		$data['status'] = true;
		echo json_encode($data,true);
		exit;
    }

    public function getpersondetails($personid)     
    {
        $data =$this->clients_model->get_contact($personid);
        if($data){
            echo json_encode(array('success'=>true,'data'=>$data));
        }else{
            echo json_encode(array('success'=>false));
        }
    }

    public function getclientdetails($clientid)
    {
        $data =$this->clients_model->get($clientid);
        if($data){
            echo json_encode(array('success'=>true,'data'=>$data));
        }else{
            echo json_encode(array('success'=>false));
        }
    }

    public function saveleadproducts($lead)
    {
        $count =$this->products_model->save_lead_products($_POST, $lead, $_POST['currency']);
        echo json_encode(array('success'=>true,'msg'=>'Items updated successfully','itemscount'=>$count));
    }
    

    public function createtaskcompanymail($id) {
        $this->load->model('tasktype_model');
        $this->load->library('session');
        $staff_id = get_staff_user_id();
        
        $this->db->where('staffid ', $staff_id);
        $redirect_url = site_url().'admin/leads/lead/'.$id.'?group=tab_email';
        $assignee_admin = $this->db->get(db_prefix() . 'staff')->row();
        $data['description'] = $this->input->post('description', false);
        $data['task_mark_complete_id'] = $this->input->post('task_mark_complete_id', false);
        $data['billable'] = $this->input->post('billable', false);
        $data['tasktype'] = $this->input->post('tasktype', false);
        $data['name'] = $this->input->post('name', false);
        $data['assignees'][0] = $assignee_admin->staffid;
        $data['startdate'] = date('d-m-Y H:i:s');
        $data['priority'] = $this->input->post('priority', false);
        $data['repeat_every_custom'] = $this->input->post('repeat_every_custom', false);
        $data['repeat_type_custom'] = $this->input->post('repeat_type_custom', false);
        $data['rel_type'] = $this->input->post('rel_type', false);
        $data['tags'] = $this->input->post('tags', false);
        
        $ch_lead_id = $id;
        $this->db->where('id', $this->input->post('contactid'));
        $contacts = $this->db->get(db_prefix() . 'contacts')->row();

        if (!empty($data['description'])) {
        if ($contacts) {
            $this->db->where('contacts_id', $contacts->id);
            $this->db->limit(1);
            $project = $this->db->get(db_prefix() . 'lead_contacts')->row();
            if ($project) {
                $data['rel_id'] = $ch_lead_id;
                $data['contacts_id'] = $contacts->id;
                //Initialize the connection:

                $this->load->library('email');
                
                $imapconf =  $smtpconf = array();
                $imapconf = get_imap_setting();
                $smtpconf = get_smtp_setings();
                // pr($imapconf);
                // pre($smtpconf);
                $this->email->initialize($smtpconf);
                $req_name = $contacts->firstname.' '.$contacts->lastname;
                
                $this->email->from($smtpconf['username'], $req_name);
                $list = array($this->input->post('toemail', false));
                $this->email->to($list);
                $this->email->cc($this->input->post('ccemail', false));
                $this->email->bcc($this->input->post('bccemail', false));
                $this->email->reply_to($smtpconf['username'], 'Replay me');
                $this->email->subject($this->input->post('name', false));
                $this->email->message($this->input->post('description', false));
                $req_files = array();
                if(!empty($_FILES["attachment"])){
                    $req_data = check_upload();
                    $req_datas = json_decode($req_data);
                    $source_from1 = $req_datas->name;
                    $req_files = $req_datas->path;
                    if(!empty($req_files)){
                        foreach($req_files as $req_file123){
                            $this->email->attach( $req_file123);
                        }
                    }
                    /*$m_file = explode(',',$_REQUEST['m_file']);
                    $file_count = count($_FILES['attachment']['name']);
                    for($j=0;$j<$file_count;$j++){
                        if(!empty($_FILES['attachment']['name'][$j]) && (empty($m_file[0]) || !in_array($j, $m_file))){
                            $newFilePath = $req_files[$j] = FCPATH.'uploads/'.$_FILES['attachment']['name'][$j];
                            move_uploaded_file($_FILES['attachment']['tmp_name'][$j], $newFilePath);
                            $this->email->attach( $newFilePath);
                        }
                    }*/
                } 
                if ($ch_data = $this->email->send()) {
                    if(!empty($req_files)){
                        foreach($req_files as $req_file12){
                            unlink($req_file12);
                        }
                    }
                    $this->load->library('imap');
                    $draft = $this->input->post('cur_draft_id', false);
                    if(!empty($draft)){
                        $this->imap->delete_mail($imapconf,$draft);
                    }
                    //Initialize the connection:
                    $imap = $this->imap->check_imap($imapconf);
                    //Get the required datas:
                    if ($imap) {
                        $uid = $this->imap->get_company_latest_email_addresses($imapconf);
                        if($uid == 'Cannot Read') {
                            $messages = get_mail_message($_POST,$imapconf);
                            $message = "Don't have access to read Sent Folder. Please enable the read permission to Sent folder in your mail server.";
                            set_alert('warning', $message);
                            redirect($redirect_url);
                        }
                        else{
                            if(!empty($req_files)){
                                foreach($req_files as $req_file12){
                                    unlink($req_file12);
                                }
                            }
                            $messages = $this->imap->get_company_mail_details($imapconf,$uid);
                            $data['source_from'] = $uid;
                        }
                    } else {
                        $message       = 'Cannot Connect IMAP Server.';
                        set_alert('warning', $message);
                        redirect($redirect_url);
                    }
                } else {
                    $message       = 'Cannot Connect SMTP Server.';
                    set_alert('warning', $message);
                    redirect($redirect_url);
                }
            } else {
                $message       = 'Cannot create Activity.';
                set_alert('warning', $message);
                redirect($redirect_url);
            }
        } else {
            $message       = 'Email address not exist.';
            set_alert('warning', $message);
            redirect($redirect_url);
        }
        }else{
             $message       = 'Please enter message';
            set_alert('warning', $message);
            redirect($redirect_url);
        }
        if(get_option('link_deal')=='yes'){
        if(isset($data['task_mark_complete_id']) && !empty($data['task_mark_complete_id'])){
            $this->tasks_model->mark_as(5, $data['task_mark_complete_id']);
        }
        if(isset($data['task_mark_complete_id'])){
            unset($data['task_mark_complete_id']);
        }
        $data_assignee = $data['assignees'];
        unset($data['assignees']);
        // $id   = $data['taskid']  = $this->tasks_model->add($data);
        
        // foreach($data_assignee as $taskey => $tasvalue ){
        //     $data['assignee'] = $tasvalue;
        //     $this->tasks_model->add_task_assignees($data);
        // }
        $id =0;
        $_id     = false;
        $success = false;
        $message = '';
        if (true || $id) {
            $success       = true;
            // $_id           = $id;
            // $message       = _l('added_successfully', _l('task'));
            // $uploadedFiles = handle_task_attachments_array($id);
            // if ($uploadedFiles && is_array($uploadedFiles)) {
            //     foreach ($uploadedFiles as $file) {
            //         $this->misc_model->add_attachment_to_database($id, 'task', [$file]);
            //     }
            // }
            if ($success) {
                if($uid != 'Cannot Read') {
                    $source_from1 = array_column($messages['attachments'], 'name'); 
                }
                $i = 0;
                $cur_project12 = $this->projects_model->get_project($ch_lead_id);
                $req_msg[$i]['lead_id']	= $ch_lead_id;
                $req_msg[$i]['task_id']		= $id;
                $req_msg[$i]['mailid']		= $messages['id'];
                $req_msg[$i]['uid'] 		= $messages['uid'];
                if(!empty($cur_project12->teamleader)){
                    $req_msg[$i]['staff_id'] 	= $cur_project12->teamleader;
                }else{
                    $cur_project12 = $this->projects_model->get_primary_project_contact($ch_lead_id);
                    $req_msg[$i]['staff_id'] 	= $cur_project12->contacts_id;
                }
                $req_msg[$i]['from_email'] 	= $messages['from']['email'];
                $req_msg[$i]['from_name'] 	= $messages['from']['name'];
                $req_msg[$i]['mail_to']		= json_encode($messages['to']);
                $req_msg[$i]['cc']			= json_encode($messages['cc']);
                $req_msg[$i]['bcc']			= json_encode($messages['bcc']);
                $req_msg[$i]['reply_to']	= json_encode($messages['reply_to']);
                $req_msg[$i]['message_id']	= $messages['message_id'];
                $req_msg[$i]['in_reply_to']	= $messages['in_reply_to'];
                $req_msg[$i]['mail_references']	= json_encode($messages['references']);
                $req_msg[$i]['date']		= $messages['date'];
                $req_msg[$i]['udate']		= $messages['udate'];
                $req_msg[$i]['subject']		= $messages['subject'];
                $req_msg[$i]['recent']		= $messages['recent'];
                $req_msg[$i]['priority']	= $messages['priority'];
                $req_msg[$i]['mail_read']	= $messages['read'];
                $req_msg[$i]['answered']	= $messages['answered'];
                $req_msg[$i]['flagged']		= $messages['flagged'];
                $req_msg[$i]['deleted']		= $messages['deleted'];
                $req_msg[$i]['draft']		= $messages['draft'];
                $req_msg[$i]['size']		= $messages['size'];
                $req_msg[$i]['attachements']= json_encode($source_from1);
                $req_msg[$i]['body_html']	= $messages['body']['html'];
                $req_msg[$i]['body_plain']	= $messages['body']['plain'];
                $req_msg[$i]['folder']	= 'Sent_mail';
                $table = db_prefix() . 'localmailstorage';
                $this->db->insert_batch($table, $req_msg);
                $emailid =$this->db->insert_id();
                $this->leads_model->log_activity($ch_lead_id,'email','added',$emailid);

                echo $message       = _l('added_successfully', _l('task'));
                set_alert('success', $message);
                redirect($redirect_url);
            } 
        }
        }
        else{
            
                set_alert('success', 'Mail Send Successfully');
                redirect($redirect_url);
        }
    }

    public function get_org_person($id='')
    {
        $this->db->select(db_prefix() . 'contacts.firstname,'.db_prefix() . 'contacts.lastname,'.db_prefix() . 'contacts.id,'.db_prefix() . 'contacts.phonenumber,'.db_prefix() . 'contacts.email');
        $this->db->where(db_prefix().'contacts.deleted_status',0);
        $this->db->where(db_prefix().'contacts.active',1);
        if($id){
            $this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.id = ' . db_prefix() . 'contacts_clients.contactid', 'left');
            $this->db->where(db_prefix().'contacts_clients.userid',$id);
            echo json_encode( $this->db->get(db_prefix().'contacts_clients')->result_object());
        }else{
            echo json_encode( $this->db->get(db_prefix().'contacts')->result_object());
        }
        
    }

    public function load_more_activities($lead_id)
    {
        
        if(isset($_GET['page'])){
            $activities =render_lead_activities($lead_id,$_GET['page']);
            if($activities){
                echo json_encode(array('success'=>true,'content'=>$activities));
                die;
            }
        }
        echo json_encode(array('success'=>true,'content'=>false));
        
    }
}
