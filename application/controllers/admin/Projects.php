<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Projects extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('projects_model');
        $this->load->model('currencies_model');
        $this->load->model('invoice_items_model');
        $this->load->model('pipeline_model');
        $this->load->model('DealLossReasons_model');
        $this->load->model('callsettings_model');
        $this->load->helper('date');
        $this->load->helper('upload');
        $this->load->library('user_agent');
        $this->load->helper('approval_helper');
    }

    public function set_session_url()
    {
        $this->session->set_userdata([
            'Projects_pre_url' => $this->router->fetch_method(),
            'Projects_pre_current_url' => 'projects/'.$this->router->fetch_method().'?'.(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:''),
        ]);
    }
    public function index()
    {
       
        $pre_url = $this->session->userdata('Projects_pre_url');
        $pre_current_url = $this->session->userdata('Projects_pre_current_url');
        $status = $this->input->get('status');
        if(!empty($status) && $this->router->fetch_method()=='index'){
            $this->index_list();
        }else{
        if($pre_url == 'gantt'){
            redirect(admin_url($pre_current_url));
        }elseif($pre_url == 'kanban_noscroll'){
            redirect(admin_url($pre_current_url));
        }elseif($pre_url == 'kanbans'){
            redirect(admin_url($pre_current_url));
        }elseif($pre_url == 'index'){
            redirect(admin_url($pre_current_url));
        }elseif($pre_url == 'index_list'){
            redirect(admin_url($pre_current_url));
        }else{
            redirect(admin_url('projects/kanban_noscroll'));
        }
    }
    }
    public function index_list()
    {
        if(!isset($_GET['gsearch'])) {
            $this->session->unset_userdata('pipelines');
            $this->session->unset_userdata('member');
            $this->session->unset_userdata('gsearch');
        }

        if(!isset($_GET['approvalList'])) {
            $this->session->unset_userdata('approvalList');
        }
        $data['show_approval_list']    = $this->input->get('approval');
        $data['gsearch']    = $this->input->get('gsearch');
        $selected_statuses = [];
        $selectedMember    = null;
        $pipelines   = $this->input->get('pipelines');
        $data['statuses']  = $this->projects_model->get_project_statuses($pipelines);
        $this->set_session_url();
        close_setup_menu();
        $this->session->set_userdata($_GET);
        $appliedStatuses = $this->input->get('status');
        $appliedMember   = $this->input->get('member');
        
        $data['selected_statuses'] = $this->input->get('pipelines');

        if (has_permission('projects', '', 'view')) {
            $selectedMember          = $appliedMember;
            $data['selectedMember']  = $selectedMember;
            if(!is_admin(get_staff_user_id())) {
                $low_hie = '';
                $lowlevel = $this->staff_model->printHierarchyTree(get_staff_user_id(),$prefix = '');
                if(!empty($lowlevel)) {
                    $low_hie = ' OR staffid IN ('.implode(',', $lowlevel).')';
                }
                $staffdetails =  $this->db->query('SELECT *, staffid as staff_id FROM ' . db_prefix() . 'staff WHERE staffid = "'.get_staff_user_id().'"'.$low_hie)->result_array();
                $data['project_members'] =  $staffdetails;
            } else {
                if(isset($_GET['pipelines']) && $_GET['pipelines'] != '')
                    $data['project_members'] = $this->pipeline_model->getPipelineFilterTeammembers($_GET['pipelines']);
                else
                    $data['project_members'] = $this->projects_model->get_distinct_projects_members();
            }
        }
        $data['pipelines'] = $this->pipeline_model->getPipeline();

        
        $data['statuses'] = $this->projects_model->get_project_statuses();
		$data['teamleaders']    = $this->pipeline_model->getPipelineTeamleaders('');
        $data['title']    = _l('projects');
		$fields = get_option('deal_fields');
		$fields1 = get_option('deal_mandatory');
		$data['mandatory_fields1'] = array('name');
		$i = $j = 1;
		if(!empty($fields1) && $fields1 != 'null'){
			$i1 =0;
			$req_fields2 = json_decode($fields1);
			if(!empty($req_fields2)){
				foreach($req_fields2 as $req_field2){
					$data['mandatory_fields1'][$i1] = $req_field2;
					$i1++;
				}
			}
		}
		
        $deal_fields = deal_needed_fields();
		$needed_fields = json_decode($deal_fields,true);
		$data['need_fields'] = $needed_fields['need_fields'];
		$data['need_fields_label'] = $needed_fields['need_fields_label'];
		$data['need_fields_edit']	=	$needed_fields['need_fields_edit'];
		$data['mandatory_fields1']	=	$needed_fields['mandatory_fields1'];
		$mandatory_count			=	count($data['mandatory_fields1']);
		$data['mandatory_fields1'][$mandatory_count]	=	'teamleader_name';
		$data['client_contacts']     = $this->clients_model->getAllContacts_active();
		$allcurrency = $this->projects_model->get_allcurrency();
        $data['allcurrency'] = $allcurrency;
        $currency = $this->currencies_model->get_base_currency();
        $data['basecurrency'] = $currency;
        $this->load->view('admin/projects/manage', $data);
    }

    public function table($clientid = '')
    {
		$fields = get_option('deal_fields');
        $data =array();

        $deal_fields = deal_needed_fields();
		$needed_fields = json_decode($deal_fields,true);
		$data['need_fields'] = $needed_fields['need_fields'];

		$data['clientid'] = $clientid;
        $this->app->get_table_data('projects', $data);
       /* $this->app->get_table_data('projects', [
            'clientid' => $clientid,
        ]);*/
        
    }

    public function staff_projects()
    {
        $this->app->get_table_data('staff_projects');
    }

    public function expenses($id)
    {
        $this->load->model('expenses_model');
        $this->app->get_table_data('project_expenses', [
            'project_id' => $id,
        ]);
    }

    public function add_expense()
    {
        if ($this->input->post()) {
            $this->load->model('expenses_model');
            $id = $this->expenses_model->add($this->input->post());
            if ($id) {
                set_alert('success', _l('added_successfully', _l('expense')));
                echo json_encode([
                    'url'       => admin_url('projects/view/' . $this->input->post('project_id') . '/?group=project_expenses'),
                    'expenseid' => $id,
                ]);
                die;
            }
            echo json_encode([
                'url' => admin_url('projects/view/' . $this->input->post('project_id') . '/?group=project_expenses'),
            ]);
            die;
        }
    }

    public function checkduplicate() {
        $result = $this->projects_model->checkduplicate();
        if($result) {
            $li = '';
            foreach($result as $val) {
                $address = '';
                if($val['company']) {
                    $address = ' - '.$val['company'];
                }
                $li .='<li style="list-style:inside; padding:5px;"><b>'.$val['name'].'</b>'.$address.'</li>';
            }
            $message = 'Deal name already exist!, if you still want to create the deal you can ignore this message.'.'<ul>'.$li.'</ul>';
        } else {
            $message = "no";
        }
        
        echo json_encode([
            'message' => $message,
        ]);
        
        exit;
    }

    public function project($id = '')
    {
        if (!has_permission('projects', '', 'edit') && !has_permission('projects', '', 'create')) {
            access_denied('Projects');
        }
		$fields = get_option('deal_fields');
		$fields1 = get_option('deal_mandatory');
		
		$check_field = array();
		if(!empty($fields) && $fields != 'null'){
			$check_field = json_decode($fields);
		}

        if ($this->input->post()) {
            $data = $project_contacts = $this->input->post();
			$curr_date1 = date('d-m-Y');
			$sdate = strtotime($curr_date1);
			if(!empty($data['start_date'])){
				$sdate = strtotime($data['start_date']);
			}
            if(!empty($data['deadline']) && $data['deadline']) {
                $edate = strtotime($data['deadline']);
                if($edate < $sdate) {
                    if($id) {
                        set_alert('warning', 'Deal Closing date should not be less than Start date.');
                        redirect(admin_url('projects/project/' . $id));
                    } else {
                        set_alert('warning', 'Deal Closing date should not be less than Start date.');
                        redirect(admin_url('projects/project'));
                    }
                }
            }
			$data['project_currency'] =  '';
			if(!empty($data['currency'])){
				$data['project_currency'] = $data['currency'];
			}
            unset($data['currency']);
            if(isset($data['project_contacts'])){
                unset($data['project_contacts']);
            }
			$primary_contact = '';
			if(!empty($data['primary_contact'])){
				$primary_contact = $data['primary_contact'];
			}
            if(isset($data['primary_contact'])){
                unset($data['primary_contact']);
            }
			
			if(empty($data['pipeline_id'])){
					if(!empty(get_option('default_pipeline'))){
						$data['pipeline_id'] = get_option('default_pipeline');
					}
					else{
						if($id) {
							set_alert('warning', 'Please set deafult pipeline.');
							redirect(admin_url('projects/project/' . $id));
						} else {
							set_alert('warning', 'Please set deafult pipeline.');
							redirect(admin_url('projects/project'));
						}
					}
			}
			if(empty($data['status'])){
				
				$default_pipeline = get_option('default_pipeline');
				$deals = $this->pipeline_model->getpipelinebyId($data['pipeline_id']);
				
				if(!empty($deals->default_status)){
					$data['status'] = $deals->default_status;
				}
				else{
					if($id) {
                        set_alert('warning', 'Please set deafult stage for default pipeline.');
                        redirect(admin_url('projects/project/' . $id));
                    } else {
                        set_alert('warning', 'Please set deafult stage for default pipeline.');
                        redirect(admin_url('projects/project'));
                    }
				}
			}
			$data['description'] =  '';
			if(!empty($this->input->post('description'))){
				$data['description'] = $this->input->post('description', false);
			}
            if ($id == '') {
                if (!has_permission('projects', '', 'create')) {
                    access_denied('Projects');
                }
               
                $products = array();
                if(isset($data['product']) && !empty($data['product'])) {
                    $products['product'] = $data['product'];
                    $products['price'] = $data['price'];
                    $products['qty'] = $data['qty'];
                    $products['total'] = $data['total'];
                }
				if(!empty($products['total'])){
					$data['project_cost'] = array_sum($products['total']);
				}
				if(!empty($this->input->post('project_cost'))){
					$data['project_cost'] = $this->input->post('project_cost');
				}
                unset($data['product']);
                unset($data['price']);
                unset($data['qty']);
                unset($data['total']);
				$data['progress'] = $this->projects_model->getprogressstatus($data['status']);
                $data['created_by'] =get_staff_user_id();
                $id = $this->projects_model->add($data,$products,$project_contacts,$primary_contact);

                if($this->input->post('lead_id')){
                    $this->leads_model->convert_to_deal($this->input->post('lead_id'),$id,$primary_contact);
                }
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('project')));
                    redirect(admin_url('projects/view/' . $id));
                }
            } else {
                
                if (!has_permission('projects', '', 'edit')) {
                    access_denied('Projects');
                }
                unset($data['product']);
                unset($data['price']);
                unset($data['qty']);
                unset($data['total']);
                $data['project_modified'] =date('Y-m-d H:i:s');
                $success = $this->projects_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('project')));
                }
                $this->projects_model->add_edit_contacts($project_contacts, $id);
                $this->projects_model->add_primary_contacts($primary_contact, $id);
                redirect(admin_url('projects/view/' . $id));
            }

           
        }
        if ($id == '') {
            $title                            = _l('add_new', _l('project_lowercase'));
            $data['auto_select_billing_type'] = $this->projects_model->get_most_used_billing_type();
        } else {
            $data['project']                               = $this->projects_model->get($id);
            $products = $this->products_model->getdeals_products($id, $data['project']->project_currency);
            $data['productscnt'] = count($products);
            $data['project']->settings->available_features = unserialize($data['project']->settings->available_features);
            $data['contacts'] = $this->projects_model->get_project_contacts($id);
            
            $data['project_members'] = $this->projects_model->get_project_members($id,(array)(isset($data['project'])?$data['project']:array()));
            $title                   = _l('edit', _l('project_lowercase'));
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }
   
        $data['last_project_settings'] = $this->projects_model->get_last_project_settings();

        if (count($data['last_project_settings'])) {
            $key                                          = array_search('available_features', array_column($data['last_project_settings'], 'name'));
            $data['last_project_settings'][$key]['value'] = unserialize($data['last_project_settings'][$key]['value']);
        }
		$fields2 = get_option('deal_important');
		$fields3 = get_option('deal_important_msg');
		$data['need_fields'] = $data['mandatory_fields'] = $data['important_fields'] = $data['important_messages'] = array();
		if(!empty($fields) && $fields != 'null'){
			$data['need_fields'] = array();
			$data['need_fields'] = json_decode($fields);
		}
		if(!empty($fields1) && $fields1 != 'null'){
			$data['mandatory_fields'] = json_decode($fields1);
		}
		if(!empty($fields2) && $fields2 != 'null'){
			$data['important_fields'] = json_decode($fields2);
		}
		if(!empty($fields3) && $fields3 != 'null'){
			$data['important_messages'] = json_decode($fields3);
		}
		if(/*!empty($data['need_fields']) && in_array("clientid", $data['need_fields']) && */!empty($data['mandatory_fields']) && in_array("clientid", $data['mandatory_fields'])){
			$data['client_contacts']     = $this->clients_model->get_deals_contacts($data['project']->clientid, ['active' => 1]);
		}else{
			$data['client_contacts']     = $this->clients_model->get_deals_contacts_list($data['project']->clientid, ['active' => 1]);
			
		}
        $data['settings'] = $this->projects_model->get_settings();
        
        $data['staff']    = $this->pipeline_model->getPipelineTeammembers((isset($data['project'])?$data['project']->pipeline_id:0));
        $data['teamleaders']    = $this->pipeline_model->getPipelineTeamleaders((isset($data['project'])?$data['project']->pipeline_id:0));
         if(!empty($data['need_fields']) && in_array("pipeline_id", $data['need_fields'])){
			$data['pipelines'] = $this->pipeline_model->getPipeline();
			$data['statuses'] = $this->pipeline_model->getPipelineleadstatus((isset($data['project'])?$data['project']->pipeline_id:0));
		 }else{
			$default_pipeline = get_option('default_pipeline');
			$deals = $this->pipeline_model->getpipelinebyId($default_pipeline);
			
			$data['statuses'] = $this->pipeline_model->get_pipeline_stage($deals->default_status);
		 }
        $allcurrency = $this->projects_model->get_allcurrency();
        $data['allcurrency'] = $allcurrency;
        $currency = $this->currencies_model->get_base_currency();
        $data['basecurrency'] = $currency->name;
        $data['products'] = $this->invoice_items_model->get_items($currency->name);
        $data['title'] = $title;
        $data['ownerHierarchy'] = '';
        if($data['project']) {
            $data['ownerHierarchy'] = $this->staff_model->printCategoryTree($data['project']->teamleader,$prefix = '');
        }
        $data['my_staffids'] = $this->staff_model->get_my_staffids();
        $data['viewIds'] = $this->staff_model->getFollowersViewList();
		$data['cur_id']	 = $id;
		$data['cur_staff_id'] = get_staff_user_id();
        $data['lead_id'] ='';
        $data['lead_products'] =false;
        if($this->input->get('lead_id')){
            // $this->load->model('leads_model');
            $lead_details =$this->leads_model->get($this->input->get('lead_id'),['project_id'=>0]);
            if($lead_details){
                $_POST['name']=$lead_details->name;
                $_POST['teamleader']=$lead_details->assigned;
                $_POST['clientid']=$lead_details->client_id;
                $lead_contact =$this->leads_model->get_lead_contact($lead_details->id);
                if($lead_contact){
                    $_POST['project_contacts']=[$lead_contact->contacts_id];
                }
                $data['title'] ='Convert lead to deal';
                $data['lead_id'] =$this->input->get('lead_id');
                $data['lead_details'] =$lead_details;
                $data['lead_products'] =$this->products_model->getleads_products($lead_details->id);
            }
            
        }
        $this->load->view('admin/projects/project', $data);
    }

    public function getContactpersonList() {
        $data = $this->clients_model->get_deals_contacts($_POST['clientId']);
        $options = '';
        foreach($data as $val) {
            $options .= '<option value="'.$val['id'].'">'.$val['firstname'].'</option>';
        }
        echo json_encode([
            'success' => $options
        ]);
    }

    public function savedealproducts() {
        $data = $this->input->post();
        $products = array();
        if(isset($data['product']) && !empty($data['product'])) {
            $products['product'] = $data['product'];
            $products['price'] = $data['price'];
            $products['qty'] = $data['qty'];
            $products['total'] = $data['total'];
            $products['discount'] = $data['discount'];
            foreach($data['no'] as $value) {
                if($data['status_'.$value]) {
                    $data['status'][] = $data['status_'.$value];
                } else {
                    $data['status'][] = 0;
                }
                if($data['variation_'.$value]) {
                    $data['variation'][] = $data['variation_'.$value];
                } else {
                    $data['variation'][] = 0;
                }
            }
            $products['status'] = $data['status'];
            $products['variation'] = $data['variation'];
            $products['method'] = $data['method'];
            $products['grandtotal'] = $data['grandtotal'];
            $products['projectid'] = $data['project_id'];
            if($data['method'] == 2 || $data['method'] == 3) {
                $products['tax'] = $data['tax'];
            }
            $this->load->model('currencies_model');
            if($_POST['currency']) {
                $cur = $_POST['currency'];
            } else {
                $currency = $this->currencies_model->get_base_currency();
                $cur = $currency->name;
            }
            $success = $this->products_model->save_deals_products($products, $data['project_id'], $cur); 
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('project')));
            }
            redirect(admin_url('projects/view/' . $data['project_id'])); 
        } else {
            $this->db->where('projectid',$data['project_id']);
            $success = $this->db->delete(db_prefix() . 'project_products');
            if ($success) {
                $update['project_cost'] = '0.00';
                $update['project_currency'] = $_POST['currency'];
                $this->db->where('id', $data['project_id']);
                $updateid = $this->db->update(db_prefix() . 'projects', $update);
                set_alert('success', _l('updated_successfully', _l('project')));
            }
            redirect(admin_url('projects/view/' . $data['project_id'])); 
        }
        
        
    }

    public function prodgetvaraiton($id,$currency) {
        if($currency) {
            $currency = $currency;
        } else {
            $this->load->model('currencies_model');
            $currency = $this->currencies_model->get_base_currency();
            $currency = $currency->name;
        }
        return $data = $this->products_model->getVariationfieldbyid($currency, $id);
    }

    public function gantt()
    {
        $this->set_session_url();
        $data['title'] = _l('project_gant');

        $selected_statuses = [];
        $selectedMember    = null;
        $data['statuses']  = $this->projects_model->get_project_statuses();

        $appliedStatuses = $this->input->get('status');
        $appliedMember   = $this->input->get('member');
        $data['gsearch']    = $this->input->get('gsearch');

        $allStatusesIds = [];
        foreach ($data['statuses'] as $status) {
            if (!isset($status['filter_default'])
                || (isset($status['filter_default']) && $status['filter_default'])
                && !$appliedStatuses) {
                $selected_statuses[] = $status['id'];
            } elseif ($appliedStatuses) {
                if (in_array($status['id'], $appliedStatuses)) {
                    $selected_statuses[] = $status['id'];
                }
            } else {
                // All statuses
                $allStatusesIds[] = $status['id'];
            }
        }

        if (count($selected_statuses) == 0) {
            $selected_statuses = $allStatusesIds;
        }

        $data['selected_statuses'] = $selected_statuses;

        if (has_permission('projects', '', 'view')) {
            $selectedMember          = $appliedMember;
            $data['selectedMember']  = $selectedMember;
            $data['project_members'] = $this->projects_model->get_distinct_projects_members();
        }

        $data['gantt_data'] = $this->projects_model->get_all_projects_gantt_data([
            'status' => $selected_statuses,
            'member' => $selectedMember,
        ]);

        $this->load->view('admin/projects/gantt', $data);
    }

    public function view($id)
    {
        if (has_permission('projects', '', 'view') || $this->projects_model->is_member($id)) {
            close_setup_menu();
            $project = $this->projects_model->get($id);
           if($project) {
                $this->db->where('touserid', get_staff_user_id());
                $this->db->where('link', 'projects/view/'.$id);
                $this->db->update(db_prefix() . 'notifications', [
                    'isread' => 1,
                    'isread_inline' => 1,
                ]);
           }
            $currency = $this->projects_model->get_currency($id);

            $products = $this->products_model->getdeals_products($id, $project->project_currency);
			
            $data['products'] = $this->products_model->getitem_price($project->project_currency);
            $data['productscnt'] = count($products);
            $data['dealproducts'] = $products;
            $data['proj_currency'] = $project->project_currency;
			$data['need_fields'] = array();
			$fields = get_option('deal_fields');
			if(!empty($fields) && $fields != 'null'){
				$data['need_fields'] = array();
				$data['need_fields'] = json_decode($fields);
			}
            $allcurrency = $this->projects_model->get_allcurrency();
            $data['allcurrency'] = $allcurrency;
            $discount_value = 0;
            foreach($data['dealproducts'] as $value) {
                if($value['discount'] > 0) {
                    $discount_value = 1;
                }
            }
            $data['discount_value'] = $discount_value;
            $data['discount_option'] = get_option('product_discount_option');
            if (!$project) {
                blank_page(_l('project_not_found'));
            }

            $project->settings->available_features = unserialize($project->settings->available_features);
            $data['statuses']                      = $this->projects_model->get_project_statuses();
            $data['all_deallossreasons']            = $this->DealLossReasons_model->getDealLossReasons();
            $group = !$this->input->get('group') ? 'project_tasks' : $this->input->get('group');
            if($project->approved==0)
                $group = !$this->input->get('group') ? 'project_overview' : $this->input->get('group');

            // Unable to load the requested file: admin/projects/project_tasks#.php - FIX
            if (strpos($group, '#') !== false) {
                $group = str_replace('#', '', $group);
            }

            $data['tabs'] = get_project_tabs_admin();
            $data['tab']  = $this->app_tabs->filter_tab($data['tabs'], $group);

            if (!$data['tab']) {
                show_404();
            }

            $this->load->model('payment_modes_model');
            $data['payment_modes'] = $this->payment_modes_model->get('', [], true);

            $data['project']  = $project;
            $data['teamleader'] = $this->staff_model->get($data['project']->teamleader);
            $data['currency'] = $project->project_currency;

            $data['project_total_logged_time'] = $this->projects_model->total_logged_time($id);
            $data['staff']        = $this->staff_model->get('', ['action_for' => 'Active']);
            $data['client_contacts']     = $this->clients_model->get_deals_contacts($data['project']->clientid, ['active' => 1]);
            $percent           = $this->projects_model->calc_progress($id);
            $data['bodyclass'] = '';

            $this->app_scripts->add(
                'projects-js',
                base_url($this->app_scripts->core_file('assets/js', 'projects.js')) . '?v=' . $this->app_scripts->core_version(),
                'admin',
                ['app-js', 'jquery-comments-js', 'jquery-gantt-js', 'circle-progress-js']
            );
            $data['project_members'] = $this->projects_model->get_project_members($id,(array)(isset($data['project'])?$data['project']:array()));
            $data['ownerHierarchy'] = '';
                if($data['project']) {
                    $data['ownerHierarchy'] = $this->staff_model->printCategoryTree($data['project']->teamleader,$prefix = '');
                }
            $data['my_staffids'] = $this->staff_model->get_my_staffids();
            $data['viewIds'] = $this->staff_model->getFollowersViewList();
			if($group == 'project_tasks'){
				$fields = get_option('deal_fields');
				$data['need_fields'] = array('project_name','id','tasktype','priority','assignees','task_name','description','tags','company','project_contacts','teamleader','status','project_status','startdate','dateadded','datemodified','datefinished','project_pipeline');
				if(!empty($fields) && $fields != 'null'){
					$req_fields = json_decode($fields);
					$i = 18;
					if(!empty($req_fields)){
						
						foreach($req_fields as $req_field11){
							if($req_field11 == 'clientid'){
								$data['need_fields'][$i] = 'company';
							}
							else if($req_field11 == 'project_contacts[]'){
								$data['need_fields'][$i] = 'project_contacts';
							}
							else if($req_field11 == 'teamleader'){
								$data['need_fields'][$i] = 'teamleader';
							}
							else if($req_field11 == 'status'){
								$data['need_fields'][$i] = 'status';
								$i++;
								$data['need_fields'][$i] = 'project_status';
							}
							else if($req_field11 == 'startdate'){
								$data['need_fields'][$i] = 'startdate';
							}
							$i++;
						}
					}
				}
			}
			if($group == 'project_tasks_bycall'){
				$fields = get_option('deal_fields');
				$data['need_fields'] = array('project_name','id','tasktype','priority','assignees','task_name','description','tags','company','project_contacts','teamleader','status','project_status','startdate','dateadded','datemodified','datefinished','project_pipeline');
				if(!empty($fields) && $fields != 'null'){
					$req_fields = json_decode($fields);
					$i = 18;
					if(!empty($req_fields)){
						
						foreach($req_fields as $req_field11){
							if($req_field11 == 'clientid'){
								$data['need_fields'][$i] = 'company';
								$i++;
							}
							else if($req_field11 == 'project_contacts[]'){
								$data['need_fields'][$i] = 'project_contacts';
								$i++;
							}
							else if($req_field11 == 'teamleader'){
								$data['need_fields'][$i] = 'teamleader';
								$i++;
							}
							else if($req_field11 == 'status'){
								$data['need_fields'][$i] = 'status';
								$i++;
								$data['need_fields'][$i] = 'project_status';
								$i++;
							}
							else if($req_field11 == 'startdate'){
								$data['need_fields'][$i] = 'startdate';
								$i++;
							}
							
						}
					}
				}
			}
            if ($group == 'project_overview') {
                $data['members'] = $this->projects_model->get_project_members($id);
                $data['contacts'] = $this->projects_model->get_project_contacts($id);
                $data['pipeline'] = $this->pipeline_model->getpipelinebyId($data['project']->pipeline_id);
                $data['teamleader'] = $this->staff_model->get($data['project']->teamleader);
                

                foreach ($data['members'] as $key => $member) {
                    $data['members'][$key]['total_logged_time'] = 0;
                    $member_timesheets                          = $this->tasks_model->get_unique_member_logged_task_ids($member['staff_id'], ' AND task_id IN (SELECT id FROM ' . db_prefix() . 'tasks WHERE rel_type="project" AND rel_id="' . $id . '")');

                    foreach ($member_timesheets as $member_task) {
                        $data['members'][$key]['total_logged_time'] += $this->tasks_model->calc_task_total_time($member_task->task_id, ' AND staff_id=' . $member['staff_id']);
                    }
                }
                $data['statuses'] = $this->pipeline_model->getPipelineleadstatus((isset($data['project'])?$data['project']->pipeline_id:0));
                $data['project_total_days']        = round((human_to_unix($data['project']->deadline . ' 00:00') - human_to_unix($data['project']->start_date . ' 00:00')) / 3600 / 24);
                $data['project_days_left']         = $data['project_total_days'];
                $data['project_time_left_percent'] = 100;
                if ($data['project']->deadline) {
                    if (human_to_unix($data['project']->start_date . ' 00:00') < time() && human_to_unix($data['project']->deadline . ' 00:00') > time()) {
                        $data['project_days_left']         = round((human_to_unix($data['project']->deadline . ' 00:00') - time()) / 3600 / 24);
                        $data['project_time_left_percent'] = $data['project_days_left'] / $data['project_total_days'] * 100;
                        $data['project_time_left_percent'] = round($data['project_time_left_percent'], 2);
                    }
                    if (human_to_unix($data['project']->deadline . ' 00:00') < time()) {
                        $data['project_days_left']         = 0;
                        $data['project_time_left_percent'] = 0;
                    }
                }

                $__total_where_tasks = 'rel_type = "project" AND rel_id=' . $id;
                if (!has_permission('tasks', '', 'view')) {
                    $__total_where_tasks .= ' AND ' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . get_staff_user_id() . ')';

                    if (get_option('show_all_tasks_for_project_member') == 1) {
                        $__total_where_tasks .= ' AND (rel_type="project" AND rel_id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . '))';
                    }
                }

                $__total_where_tasks = hooks()->apply_filters('admin_total_project_tasks_where', $__total_where_tasks, $id);

                $where = ($__total_where_tasks == '' ? '' : $__total_where_tasks . ' AND ') . 'status != ' . Tasks_model::STATUS_COMPLETE;

                $data['tasks_not_completed'] = total_rows(db_prefix() . 'tasks', $where);
                $total_tasks                 = total_rows(db_prefix() . 'tasks', $__total_where_tasks);
                $data['total_tasks']         = $total_tasks;

                $where = ($__total_where_tasks == '' ? '' : $__total_where_tasks . ' AND ') . 'status = ' . Tasks_model::STATUS_COMPLETE . ' AND rel_type="project" AND rel_id="' . $id . '"';

                $data['tasks_completed'] = total_rows(db_prefix() . 'tasks', $where);

                $data['tasks_not_completed_progress'] = ($total_tasks > 0 ? number_format(($data['tasks_completed'] * 100) / $total_tasks, 2) : 0);
                $data['tasks_not_completed_progress'] = round($data['tasks_not_completed_progress'], 2);

                @$percent_circle        = $percent / 100;
                $data['percent_circle'] = $percent_circle;


                $data['project_overview_chart'] = $this->projects_model->get_project_overview_weekly_chart_data($id, ($this->input->get('overview_chart') ? $this->input->get('overview_chart'):'this_week'));
            } elseif ($group == 'project_invoices') {
                $this->load->model('invoices_model');

                $data['invoiceid']   = '';
                $data['status']      = '';
                $data['custom_view'] = '';

                $data['invoices_years']       = $this->invoices_model->get_invoices_years();
                $data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();
                $data['invoices_statuses']    = $this->invoices_model->get_statuses();
            } elseif ($group == 'project_gantt') {
                $gantt_type         = (!$this->input->get('gantt_type') ? 'milestones' : $this->input->get('gantt_type'));
                $taskStatus         = (!$this->input->get('gantt_task_status') ? null : $this->input->get('gantt_task_status'));
                $data['gantt_data'] = $this->projects_model->get_gantt_data($id, $gantt_type, $taskStatus);
            } elseif ($group == 'project_milestones') {
                $data['bodyclass'] .= 'project-milestones ';
                $data['milestones_exclude_completed_tasks'] = $this->input->get('exclude_completed') && $this->input->get('exclude_completed') == 'yes' || !$this->input->get('exclude_completed');

                $data['total_milestones'] = total_rows(db_prefix() . 'milestones', ['project_id' => $id]);
                $data['milestones_found'] = $data['total_milestones'] > 0 || (!$data['total_milestones'] && total_rows(db_prefix() . 'tasks', ['rel_id' => $id, 'rel_type' => 'project', 'milestone' => 0]) > 0);
            } elseif ($group == 'project_files') {
                $data['files'] = $this->projects_model->get_files($id);
            } elseif ($group == 'project_expenses') {
                $this->load->model('taxes_model');
                $this->load->model('expenses_model');
                $data['taxes']              = $this->taxes_model->get();
                $data['expense_categories'] = $this->expenses_model->get_category();
                $data['currencies']         = $this->currencies_model->get();
            } elseif ($group == 'project_activity') {
                $data['activity'] = $this->projects_model->get_activity($id);
				
            } elseif ($group == 'project_tasks_bycall') {
                $data['activity'] = $this->projects_model->get_activity($id);
            } elseif ($group == 'project_notes') {
                $data['notes'] = $this->projects_model->get_notes($id);
                $data['staff_notes'] = $this->projects_model->get_staff_notes($id);
            } elseif ($group == 'project_estimates') {
                $this->load->model('estimates_model');
                $data['estimates_years']       = $this->estimates_model->get_estimates_years();
                $data['estimates_sale_agents'] = $this->estimates_model->get_sale_agents();
                $data['estimate_statuses']     = $this->estimates_model->get_statuses();
                $data['estimateid']            = '';
                $data['switch_pipeline']       = '';
            } elseif ($group == 'project_tickets') {
                $data['chosen_ticket_status'] = '';
                $this->load->model('tickets_model');
                $data['ticket_assignees'] = $this->tickets_model->get_tickets_assignes_disctinct();

                $this->load->model('departments_model');
                $data['staff_deparments_ids']          = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                $data['default_tickets_list_statuses'] = hooks()->apply_filters('default_tickets_list_statuses', [1, 2, 4]);
            } elseif ($group == 'project_timesheets') {
                // Tasks are used in the timesheet dropdown
                // Completed tasks are excluded from this list because you can't add timesheet on completed task.
                $data['tasks']                = $this->projects_model->get_tasks($id, 'status != ' . Tasks_model::STATUS_COMPLETE . ' AND billed=0');
                $data['timesheets_staff_ids'] = $this->projects_model->get_distinct_tasks_timesheets_staff($id);
            }
			elseif ($group == 'project_email') {
				$row_per_page = 10;
				$staffid = get_staff_user_id();
				$data['cur_project_id'] = $id;
				$data['ch_contact'] = $ch_contact = $this->projects_model->get_primary_contact($id);
				$data['all_dels'] = array();
					$all_deals1 = $this->projects_model->get_project($id);
					if(!empty($all_deals1)){
						$data['all_dels']['project_id'] = $all_deals1->id;
						$data['all_dels']['project_name'] = $all_deals1->name;
					}

				
				$table = db_prefix() . 'template';
				$cond = array('user_id'=>$staffid);
				$templates = $this->db->where($cond)->get($table)->result_array();
				$data['templates'] = $templates;
				$data['default_val'] = '';
				if(!empty($templates[0]['description'])){
					$data['default_val'] = $templates[0]['description'];
				}
				
				$ch_emails = $this->projects_model->get_email($id);
				if(empty($_REQUEST['page_no'])){
					$data['emails'] = $this->projects_model->get_email($id,$row_per_page,0);
					$data['email_count'] = $this->projects_model->count_email($staffid,$id,$row_per_page,0);
				}
				else{
					$ch_cur_page = ($_REQUEST['page_no']-1)*$row_per_page;
					$data['emails'] = $this->projects_model->get_email($id,$row_per_page,$ch_cur_page);
					$data['email_count'] = $this->projects_model->count_email($staffid,$id,$row_per_page,$ch_cur_page);
				}
				$this->load->library('pagination');
				
				$allcount = count($ch_emails);
				$config['base_url'] = base_url().'admin/projects/view/'.$id.'?group=project_email';
				$config['use_page_numbers'] = TRUE;
				$config['total_rows'] = $allcount;
				$config['per_page'] = $row_per_page;
		 
				$config['full_tag_open']    = '<div class="pagging text-center"><nav><ul class="pagination">';
				$config['full_tag_close']   = '</ul></nav></div>';
				$config['num_tag_open']     = '<li class="page-item"><span class="page-link">';
				$config['num_tag_close']    = '</span></li>';
				$config['cur_tag_open']     = '<li class="page-item active"><span class="page-link">';
				$config['cur_tag_close']    = '<span class="sr-only">(current)</span></span></li>';
				$config['next_tag_open']    = '<li class="page-item"><span class="page-link">';
				$config['next_tag_close']  = '<span aria-hidden="true"></span></span></li>';
				$config['prev_tag_open']    = '<li class="page-item"><span class="page-link">';
				$config['prev_tag_close']  = '</span></li>';
				$config['first_tag_open']   = '<li class="page-item"><span class="page-link">';
				$config['first_tag_close'] = '</span></li>';
				$config['last_tag_open']    = '<li class="page-item"><span class="page-link">';
				$config['last_tag_close']  = '</span></li>';
				$config['enable_query_strings'] = TRUE;
				$config['page_query_string'] = TRUE;
				 $config['query_string_segment'] = 'page_no';
				$config['num_links'] = 5;
		 
				$this->pagination->initialize($config);
				$data['pagination'] = $this->pagination->create_links();
            }

            // Discussions
            if ($this->input->get('discussion_id')) {
                $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
                $data['discussion']                        = $this->projects_model->get_discussion($this->input->get('discussion_id'), $id);
                $data['current_user_is_admin']             = is_admin();
            }
            $data['files'] = $this->projects_model->get_files($id);
            $data['percent'] = $percent;

            $this->app_scripts->add('circle-progress-js', 'assets/plugins/jquery-circle-progress/circle-progress.min.js');

            $other_projects       = [];
            $other_projects_where = 'id != ' . $id;

            $statuses = $this->projects_model->get_project_statuses();

            $other_projects_where .= ' AND (';
            foreach ($statuses as $status) {
                if (isset($status['filter_default']) && $status['filter_default']) {
                    $other_projects_where .= 'status = ' . $status['id'] . ' OR ';
                }
            }
           
            $other_projects_where = rtrim($other_projects_where, ' OR ');

            $other_projects_where .= ')';

            if (!has_permission('projects', '', 'view')) {
                $other_projects_where .= ' AND ' . db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
            }
            $data['contacts'] = $this->projects_model->get_project_contacts($id);
            $data['pipelines'] = $this->pipeline_model->getPipeline();
            $data['teamleaders']    = $this->pipeline_model->getPipelineTeamleaders((isset($data['project'])?$data['project']->pipeline_id:0));
            if (strpos($other_projects_where, '()') !== false) {
            } else {
                $data['other_projects'] = $this->projects_model->get('', $other_projects_where);
            }
			$data['req_staff_id'] = get_staff_user_id();
            $data['title']          = $data['project']->name;
            $data['bodyclass'] .= 'project invoices-total-manual estimates-total-manual';
            $data['project_status'] = get_project_status_by_id($project->status);
            $data['projectcost'] = $data['project']->project_cost;
            $orgclientid = $data['project']->client_data->userid;
            $orgname = $data['project']->client_data->company;
            $orgcontacts = $this->clients_model->get_contacts($orgclientid);
            $primarycontact = "";
            foreach($orgcontacts as $val) {
                if($val['is_primary'] > 0) {
                    $primarycontact = $val['firstname']." ".$val['lastname']." - ";
                }
            }
            $data['primarycont'] =  $primarycontact;

            // for approval 
            $this->load->model('workflow_model');
            $this->load->model('approval_model');
            $this->load->model('DealRejectionReasons_model');
            $this->load->model('staff_model');
            $data['all_dealrejectionreasons']            = $this->DealRejectionReasons_model->getDealRejectionReasons();
            $data['approval_flow'] =$this->workflow_model->getflows('deal_approval');
            $data['approval_history'] =(array) $this->approval_model->getHistory('projects',$id);
            $this->db->where('rel_type','projects');
            $this->db->where('rel_id',$id);
            $this->db->where('status',0);
            $this->db->where('reopened',0);
            $this->db->select('count(id) as rejected');
            $deal_rejected_details =$this->db->get(db_prefix().'approval_history')->row();
            if($deal_rejected_details)
                $data['deal_rejected'] =$deal_rejected_details->rejected;
            else
                $data['deal_rejected'] =0;
            $data['staff_hierarchy'] =$this->approval_model->getDealReportingLevels($project->teamleader);
            $this->load->view('admin/projects/view', $data);
        } else {
            access_denied('Project View');
        }
    }

    public function getcontactsbyorg() {
        $data = $this->clients_model->get_deals_contacts($_POST['clientid']);
        $contactids = $this->clients_model->get_projects_contacts($_POST['project_id']);
        $primaryid = $this->clients_model->get_projects_primary_contacts($_POST['project_id']);
        $cids = array();
        $pid = '';
        foreach($contactids as $value) {
            $cids[] = $value['contacts_id'];
        }
        foreach($primaryid as $pvalue) {
            $pid = $pvalue['contacts_id'];
        }
        $options = $poptions = '';
        foreach($data as $val) {
            $selected = '';
            if(in_array($val['id'],$cids)) {
                $selected = ' selected ';
            }
            $options .= '<option value="'.$val['id'].'" '.$selected.'>'.$val['firstname'].'</option>';

            $pselected = '';
            if($val['id'] == $pid) {
                $pselected = ' selected ';
            }
            if(in_array($val['id'],$cids)) {
                $poptions .= '<option value="'.$val['id'].'" '.$pselected.'>'.$val['firstname'].'</option>';
            }
        }

        echo json_encode([
            'contact' => $options,
            'primarycontact' => $poptions
        ]);
    }

    public function notesbyid ($id) {
        $data = $this->projects_model->get_notes_byid($id);
        echo json_encode($data);
			 exit();
    }

    public function mark_as()
    {
        $success = false;
        $message = '';
        if ($this->input->is_ajax_request()) {
            if (has_permission('projects', '', 'create') || has_permission('projects', '', 'edit')) {
                $status = get_project_status_by_id($this->input->post('status_id'));

                $message = _l('project_marked_as_failed', $status['name']);
                $success = $this->projects_model->mark_as($this->input->post());

                if ($success) {
                    $message = _l('project_marked_as_success', $status['name']);
                }
            }
        }
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
    }
	
	public function mark_as_won_loss_reopen()
    {
        $success = false;
        $message = '';
        if ($this->input->is_ajax_request()) {
            if (has_permission('projects', '', 'create') || has_permission('projects', '', 'edit')) {
               
                $success = $this->projects_model->mark_as_won_loss_reopen($this->input->post());

                if ($success) {
                    $message = _l('project_marked_as_success', $status['name']);
                }
            }
        }
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
    }
	

    public function kanban_noscroll()
    {
        $this->set_session_url();
       
        $data['title'] = _l('project_gant');


        $data['gsearch']    = $this->input->get('gsearch');
        $selected_statuses = [];
        $selectedMember    = null;
        $pipelines   = $this->input->get('pipelines');
        $data['statuses']  = $this->projects_model->get_project_statuses($pipelines);

        $appliedStatuses = $this->input->get('status');
        $appliedMember   = $this->input->get('member');
        $this->session->set_userdata($_GET);
    
        $data['selected_statuses'] = $this->input->get('pipelines');

        if (has_permission('projects', '', 'view')) {
            $selectedMember          = $appliedMember;
            $data['selectedMember']  = $selectedMember;
            if(!is_admin(get_staff_user_id())) {
                $low_hie = '';
                $lowlevel = $this->staff_model->printHierarchyTree(get_staff_user_id(),$prefix = '');
                if(!empty($lowlevel)) {
                    $low_hie = ' OR staffid IN ('.implode(',', $lowlevel).')';
                }
                $staffdetails =  $this->db->query('SELECT *, staffid as staff_id FROM ' . db_prefix() . 'staff WHERE staffid = "'.get_staff_user_id().'" '.$low_hie)->result_array();
                $data['project_members'] =  $staffdetails;
            } else {
                if(isset($_GET['pipelines']) && $_GET['pipelines'] != '')
                    $data['project_members'] = $this->pipeline_model->getPipelineFilterTeammembers($_GET['pipelines']);
                else
                    $data['project_members'] = $this->projects_model->get_distinct_projects_members();
            }
        }
        $data['gantt_data'] = $this->projects_model->get_all_projects_gantt_data([
            'status' => $selected_statuses,
            'member' => $selectedMember,
        ]);
        
        $data['pipelines'] = $this->pipeline_model->getPipeline();
      
        $this->load->view('admin/projects/kanbans', $data);
    }
    public function kanbans()
    {
        $this->set_session_url();
       
        $data['title'] = _l('project_gant');


        $data['gsearch']    = $this->input->get('gsearch');
        $selected_statuses = [];
        $selectedMember    = null;
        $pipelines   = $this->input->get('pipelines');
        $data['statuses']  = $this->projects_model->get_project_statuses($pipelines);

        $appliedStatuses = $this->input->get('status');
        $appliedMember   = $this->input->get('member');
        $this->session->set_userdata($_GET);
    

        $data['selected_statuses'] = $this->input->get('pipelines');

        if (has_permission('projects', '', 'view')) {
            $selectedMember          = $appliedMember;
            $data['selectedMember']  = $selectedMember;
            if(!is_admin(get_staff_user_id())) {
                $low_hie = '';
                $lowlevel = $this->staff_model->printHierarchyTree(get_staff_user_id(),$prefix = '');
                if(!empty($lowlevel)) {
                    $low_hie = ' OR staffid IN ('.implode(',', $lowlevel).')';
                }
                $staffdetails =  $this->db->query('SELECT *, staffid as staff_id FROM ' . db_prefix() . 'staff WHERE staffid = "'.get_staff_user_id().'"'.$low_hie)->result_array();
                $data['project_members'] =  $staffdetails;
            } else {
                if(isset($_GET['pipelines']) && $_GET['pipelines'] != '')
                    $data['project_members'] = $this->pipeline_model->getPipelineFilterTeammembers($_GET['pipelines']);
                else
                    $data['project_members'] = $this->projects_model->get_distinct_projects_members();
            }
        }

        $data['gantt_data'] = $this->projects_model->get_all_projects_gantt_data([
            'status' => $selected_statuses,
            'member' => $selectedMember,
        ]);
        $data['pipelines'] = $this->pipeline_model->getPipeline();
        $this->load->view('admin/projects/kanban_noscroll', $data);
    }

    public function kanbans_forecast()
    {
        $this->set_session_url();
        
        $data['title'] = _l('project_gant');


        if(isset($_REQUEST['nav']) && !empty($_REQUEST['nav'])) {
            $_SESSION['nav'] = $_REQUEST['nav'];
        } else {
            
            unset($_SESSION['nav']);
        }
        if(isset($_REQUEST['forecast_showby'])) {
            $_SESSION['forecast_showby'] = $_REQUEST['forecast_showby'];
        } else {
            if(!isset($_SESSION['forecast_showby'])) {
                $_SESSION['forecast_showby'] = 'close date';
            }
        }
        if(isset($_REQUEST['forecast_orderby'])) {
            $_SESSION['forecast_orderby'] = $_REQUEST['forecast_orderby'];
        } else {
            if(!isset($_SESSION['forecast_orderby'])) {
                $_SESSION['forecast_orderby'] = 'open deal';
            } 
        }
        if(isset($_REQUEST['forecast_intervel'])) {
            $_SESSION['forecast_intervel'] = $_REQUEST['forecast_intervel'];
        } else {
            if(!isset($_SESSION['forecast_intervel'])) {
                $_SESSION['forecast_intervel'] = 'quarter';
                $_SESSION['nav'] = 'start';
            }
        }
        if(isset($_REQUEST['forecast_column'])) {
            $_SESSION['forecast_column'] = $_REQUEST['forecast_column'];
        } else {
            if(!isset($_SESSION['forecast_column'])) {
                $_SESSION['forecast_column'] = '4';
            }
        }
        
        $data['gsearch']    = $this->input->get('gsearch');
        $selected_statuses = [];
        $selectedMember    = null;
        $pipelines   = $this->input->get('pipelines');
        $data['statuses']  = $this->projects_model->get_project_statuses($pipelines);

        $appliedStatuses = $this->input->get('status');
        $appliedMember   = $this->input->get('member');
        $this->session->set_userdata($_GET);
    

        $data['selected_statuses'] = $this->input->get('pipelines');

        if (has_permission('projects', '', 'view')) {
            $selectedMember          = $appliedMember;
            $data['selectedMember']  = $selectedMember;
            if(!is_admin(get_staff_user_id())) {
                $low_hie = '';
                $lowlevel = $this->staff_model->printHierarchyTree(get_staff_user_id(),$prefix = '');
                if(!empty($lowlevel)) {
                    $low_hie = ' OR staffid IN ('.implode(',', $lowlevel).')';
                }
                $staffdetails =  $this->db->query('SELECT *, staffid as staff_id FROM ' . db_prefix() . 'staff WHERE staffid = "'.get_staff_user_id().'"'.$low_hie)->result_array();
                $data['project_members'] =  $staffdetails;
            } else {
                if(isset($_GET['pipelines']) && $_GET['pipelines'] != '')
                    $data['project_members'] = $this->pipeline_model->getPipelineFilterTeammembers($_GET['pipelines']);
                else
                    $data['project_members'] = $this->projects_model->get_distinct_projects_members();
            }
        }

        $data['gantt_data'] = $this->projects_model->get_all_projects_gantt_data([
            'status' => $selected_statuses,
            'member' => $selectedMember,
        ]);
        $data['pipelines'] = $this->pipeline_model->getPipeline();
        $this->load->view('admin/projects/kanban_forecast', $data);
    }

    
    public function projects_kanban_load_more()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }

        $status = $this->input->get('status');
        $page   = $this->input->get('page');

       
        $pipelines   = $this->input->get('pipelines');
        $status  = $this->projects_model->get_project_statuses($pipelines);

        $leads = $this->projects_model->do_kanban_query($status['id'], $this->input->get('search'), $page, [
            'sort_by' => $this->input->get('sort_by'),
            'sort'    => $this->input->get('sort'),
        ]);

        foreach ($leads as $lead) {
            $this->load->view('admin/projects/_kan_ban_card', [
                'lead'   => $lead,
                'status' => $status,
            ]);
        }
    }
    public function header_gsearch()
    {
        $gsearch   = $this->input->post('globalsearch');
        $my_staffids = $this->staff_model->get_my_staffids();
        $data = array();
		$project_fields = "id,name,description,status,pipeline_id,clientid,teamleader,billing_type,start_date,deadline,project_created,created_by,project_modified,modified_by,date_finished,progress,progress_from_tasks,project_cost,project_rate_per_hour,estimated_hours,addedfrom,stage_of,stage_on,loss_reason,loss_remark,deleted_status,project_currency,imported_id,lead_id";
        if(!is_admin(get_staff_user_id())) {
            if($my_staffids) {
                $data['projects'] = $this->db->query('SELECT '.$project_fields.' FROM tblprojects where ((teamleader in (' . implode(',',$my_staffids) . ')) OR id IN (select project_id from tblproject_members where staff_id in (' . implode(',',$my_staffids) . '))) AND name like "%'.$gsearch.'%" ')->result_array();
            } else {
                $data['projects'] = $this->db->query('SELECT '.$project_fields.' FROM tblprojects where ((teamleader = "'.get_staff_user_id().'") OR id IN (select project_id from tblproject_members where staff_id="'.get_staff_user_id().'")) AND name like "%'.$gsearch.'%" ')->result_array();
            }
        } else {
            $data['projects'] = $this->projects_model->get('',[
                "name like '%".$gsearch."%'" => "",
            ]);
        }
         $client_fields = "userid,company,vat,phonenumber,country,city,zip,state,address,website,datecreated,active,leadid,billing_street,billing_city,billing_state,billing_zip,billing_country,shipping_street,shipping_city,shipping_state,shipping_zip,shipping_country,longitude,latitude,default_language,default_currency,show_primary_contact,stripe_id,registration_confirmed,addedfrom,deleted_status";
        if(!is_admin(get_staff_user_id())) {
            if($my_staffids) {
                $data['clients'] = $this->db->query('SELECT '.$client_fields.' FROM tblclients where ((addedfrom in (' . implode(',',$my_staffids) . ')) OR userid IN (select clientid from tblprojects where id IN (select project_id from tblproject_members where staff_id in (' . implode(',',$my_staffids) . '))) OR userid IN ( select clientid from tblprojects where teamleader in (' . implode(',',$my_staffids) . '))) AND active = 1 AND (company like "%'.$gsearch.'%" OR phonenumber like "%'.$gsearch.'%")')->result_array();
            } else {
                $data['clients'] = $this->db->query('SELECT '.$client_fields.' FROM tblclients where ((addedfrom="'.get_staff_user_id().'") OR userid IN (select clientid from tblprojects where id IN (select project_id from tblproject_members where staff_id="'.get_staff_user_id().'")) OR userid IN ( select clientid from tblprojects where teamleader = "'.get_staff_user_id().'")) AND active = 1 AND (company like "%'.$gsearch.'%" OR phonenumber like "%'.$gsearch.'%")')->result_array();
            }
            
        } else {
            $data['clients'] = $this->clients_model->get('',[
                db_prefix() .'clients.active' => 1, " (".
                db_prefix() ."clients.company like '%".$gsearch."%'" . " OR ".
                db_prefix() ."clients.phonenumber like '%".$gsearch."%'" .") and ".db_prefix()."clients.company != " => "",
            ]);
        }

        if(!is_admin(get_staff_user_id())) {
            if($my_staffids) {
                $where = ' WHERE ('.db_prefix().'contacts.addedfrom IN (' . implode(',',$my_staffids) . ') OR (' . db_prefix() . 'contacts.userid IN (SELECT ' . db_prefix() . 'projects.clientid FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ')  AND tblprojects.clientid != "")) OR  (' . db_prefix() . 'contacts.userid IN (SELECT ' . db_prefix() . 'projects.clientid FROM ' . db_prefix() . 'projects where ' . db_prefix() . 'projects.teamleader in (' . implode(',',$my_staffids) . ') AND tblprojects.clientid != "" )))   AND (tblcontacts.firstname like "%'.$gsearch.'%" OR tblcontacts.email like "%'.$gsearch.'%" OR tblcontacts.phonenumber like "%'.$gsearch.'%")  AND tblcontacts.deleted_status=0 AND tblclients.deleted_status=0 '.$likeqry;
                $where_summary_inactiveperson_qry = 'SELECT  tblcontacts.*
                FROM tblcontacts
                LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7'.$where;

            } else {
                $where = ' WHERE ('.db_prefix().'contacts.addedfrom = "'.get_staff_user_id().'" OR (' . db_prefix() . 'contacts.userid IN (SELECT ' . db_prefix() . 'projects.clientid FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id = "'.get_staff_user_id().'"  AND tblprojects.clientid != "")) OR  (' . db_prefix() . 'contacts.userid IN (SELECT ' . db_prefix() . 'projects.clientid FROM ' . db_prefix() . 'projects where ' . db_prefix() . 'projects.teamleader = "'.get_staff_user_id().'" AND tblprojects.clientid != "" )))   AND (tblcontacts.firstname like "%'.$gsearch.'%" OR tblcontacts.email like "%'.$gsearch.'%" OR tblcontacts.phonenumber like "%'.$gsearch.'%")  AND tblcontacts.deleted_status=0 AND tblclients.deleted_status=0 '.$likeqry;
                $where_summary_inactiveperson_qry = 'SELECT  tblcontacts.*
                FROM tblcontacts
                LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7'.$where;
               
                        
            }
            $data['contacts'] = $this->db->query($where_summary_inactiveperson_qry)->result_array();
        } else {
            $data['contacts'] = $this->clients_model->get_contacts('',[
                db_prefix() .'contacts.active' => 1," (".
                db_prefix() ."contacts.firstname like '%".$gsearch."%' OR ".
                db_prefix() ."contacts.email like '%".$gsearch."%' OR " .
                db_prefix() ."contacts.phonenumber like '%".$gsearch."%'".") and ".db_prefix()."contacts.firstname != " => "",
            ]);
        }

			$data['projects_count'] = count($data['projects']);
			$data['clients_count'] = count($data['clients']);
			$data['contacts_count'] = count($data['contacts']);
			$data['all_count'] = $data['contacts_count'] + $data['clients_count'] + $data['projects_count'];
			
			$data['all_html'] = $data['projects_html'] = $data['clients_html'] = $data['contacts_html'] = '';
			
			foreach($data['clients'] as $client){ 
				$data['clients_html'] .= '<li class="relative notification-wrapper thsr-client">
		<div class="media">
			<div class="media-body">
			 <h5 class="media-heading mtop5">
				<a href="'.admin_url('clients/client/'.$client["userid"]).'">'.$client['company'].'</a>
			 </h5>
			</div>
		</div>
	</li>';
			}
			
			$data['all_html'] .= $data['clients_html'];

			foreach($data['projects'] as $project){ 
				$data['projects_html'] .= '<li class="relative notification-wrapper thsr-client">
		<div class="media">
			<div class="media-body">
			 <h5 class="media-heading mtop5">
				<a href="'.admin_url('projects/view/'.$project["id"]).'">'.$project['name'].'</a>
			 </h5>
			</div>
		</div>
	</li>';
			}
			
			$data['all_html'] .= $data['projects_html'];
            
			foreach($data['contacts'] as $contact){ 
				$data['contacts_html'] .= '<li class="relative notification-wrapper thsr-client">
		<div class="media">
			<div class="media-body">
			 <h5 class="media-heading mtop5">
				<a href="'.admin_url('clients/view_contact/'.$contact["id"]).'">'.get_contact_full_name($contact['id']).'</a>
			 </h5>
			</div>
		</div>
	</li>';
			}
			
			$data['all_html'] .= $data['contacts_html'];
            if(!empty($gsearch)) {

            } else {
                $data['all_count'] = $data['projects_count'] = $data['clients_count'] = $data['contacts_count'] = 0;
                $data['contacts'] = $data['clients'] = $data['projects'] = '';
                $data['all_html'] = $data['projects_html'] = $data['clients_html'] = $data['contacts_html'] = '';
            }
			
			 echo json_encode($data);
			 exit();
        $this->load->view('admin/includes/header_gsearch_result_top',$data);
    }    

    public function file($id, $project_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();

        $data['file'] = $this->projects_model->get_file($id, $project_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('admin/projects/_file', $data);
    }

    public function kanban()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
		if (!empty($this->session->userdata('pipelines'))) {
			$pipeline = $this->session->userdata('pipelines');
			$data['statuses'] = $this->pipeline_model->getPipelineprojectsstatus($pipeline);
		}
		else {
			$pipeline = '';
			$data['statuses'] = $this->pipeline_model->getPipelineprojectsstatus();
        }
        $data['selectedpipeline'] = $pipeline;
        echo $this->load->view('admin/projects/kan-ban', $data, true);
    }
	
	public function kanban_more_load()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
		if (!empty($this->session->userdata('pipelines'))) {
			$pipeline = $this->session->userdata('pipelines');
			$data['statuses'] = $this->pipeline_model->getPipelineprojectsstatus($pipeline);
		}
		else {
			$pipeline = '';
			$data['statuses'] = $this->pipeline_model->getPipelineprojectsstatus();
        }
        $data['selectedpipeline'] = $pipeline;
        echo $this->load->view('admin/projects/kan-ban_ajax', $data, true);
    }

    public function forecast()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
		if (!empty($this->session->userdata('pipelines'))) {
			$pipeline = $this->session->userdata('pipelines');
			$data['statuses'] = $this->pipeline_model->getPipelineprojectsstatus($pipeline);
		}
		else {
			$pipeline = '';
			$data['statuses'] = $this->pipeline_model->getPipelineprojectsstatus();
        }
        $data['selectedpipeline'] = $pipeline;
        echo $this->load->view('admin/projects/kan-ban-forecast', $data, true);
    }

    /* Used in kanban when dragging and mark as */
    public function update_project_status()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            $this->projects_model->update_project_status($this->input->post());
        }
    }

    public function update_file_data()
    {
        if ($this->input->post()) {
            $this->projects_model->update_file_data($this->input->post());
        }
    }

    public function add_external_file()
    {
        if ($this->input->post()) {
            $data                        = [];
            $data['project_id']          = $this->input->post('project_id');
            $data['files']               = $this->input->post('files');
            $data['external']            = $this->input->post('external');
            $data['visible_to_customer'] = ($this->input->post('visible_to_customer') == 'true' ? 1 : 0);
            $data['staffid']             = get_staff_user_id();
            $this->projects_model->add_external_file($data);
        }
    }

    public function download_all_files($id)
    {
        if ($this->projects_model->is_member($id) || has_permission('projects', '', 'view')) {
            $files = $this->projects_model->get_files($id);
            if (count($files) == 0) {
                set_alert('warning', _l('no_files_found'));
                redirect(admin_url('projects/view/' . $id . '?group=project_files'));
            }
            $path = get_upload_path_by_type('project') . $id;
            $this->load->library('zip');
            foreach ($files as $file) {
                $this->zip->read_file($path . '/' . $file['file_name']);
            }
            $this->zip->download(slug_it(get_project_name_by_id($id)) . '-files.zip');
            $this->zip->clear_data();
        }
    }

    public function export_project_data($id)
    {
        if (has_permission('projects', '', 'create')) {
            app_pdf('project-data', LIBSPATH . 'pdf/Project_data_pdf', $id);
        }
    }

    public function update_task_milestone()
    {
        if ($this->input->post()) {
            $this->projects_model->update_task_milestone($this->input->post());
        }
    }

    public function update_milestones_order()
    {
        if ($post_data = $this->input->post()) {
            $this->projects_model->update_milestones_order($post_data);
        }
    }

    public function pin_action($project_id)
    {
        $this->projects_model->pin_action($project_id);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function add_edit_members($project_id)
    {
        if (has_permission('projects', '', 'edit') || has_permission('projects', '', 'create')) {
            $this->projects_model->add_edit_members($this->input->post(), $project_id);
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
	public function edit_description($project_id)
    {
        if (has_permission('projects', '', 'edit') || has_permission('projects', '', 'create')) {
            $this->projects_model->edit_desc_project($this->input->post(), $project_id);
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    public function add_edit_contacts($project_id)
    {
        if (has_permission('projects', '', 'edit') || has_permission('projects', '', 'create')) {
            $this->projects_model->add_edit_contacts($this->input->post(), $project_id);
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    public function dyfieldupdate()
    {
        
        if (has_permission('projects', '', 'edit') || has_permission('projects', '', 'create')) {
            $data = $this->input->post();
            if(isset($data['custom_field'])){
				if(is_array($data['f_val'])){
					$data['f_val']	= implode(',',$data['f_val']);
				}
				$success = $this->projects_model->update_custom_val($data['project_id'],$data['slug'],$data['f_val']);
				if($success){
					$success = $this->projects_model->update(array('project_modified'=>date('Y-m-d H:i:s'),'modified_by'=>get_staff_user_id()), $data['project_id']);
				}
			}
			else{
				if(isset($data['clientid_copy_project']) && !empty($data['clientid_copy_project'])){
					$success = $this->projects_model->update(array('clientid'=>$data['clientid_copy_project'],'project_modified'=>date('Y-m-d H:i:s'),'modified_by'=>get_staff_user_id()), $data['project_id']);
				}elseif(isset($data['pipeline_id']) && !empty($data['pipeline_id'])){
					$success = $this->projects_model->update(array('pipeline_id'=>$data['pipeline_id'],'project_modified'=>date('Y-m-d H:i:s'),'modified_by'=>get_staff_user_id()), $data['project_id']);
				}elseif(isset($data['teamleader']) && !empty($data['teamleader'])){
					$success = $this->projects_model->update(array('teamleader'=>$data['teamleader'],'project_modified'=>date('Y-m-d H:i:s'),'modified_by'=>get_staff_user_id()), $data['project_id']);
				}elseif(isset($data['project_cost']) && !empty($data['project_cost'])){
					$success = $this->projects_model->update(array('project_cost'=>$data['project_cost'],'project_modified'=>date('Y-m-d H:i:s'),'modified_by'=>get_staff_user_id()), $data['project_id']);
				}elseif(isset($data['name']) && !empty($data['name'])){
					$success = $this->projects_model->update(array('name'=>$data['name'],'project_modified'=>date('Y-m-d H:i:s'),'modified_by'=>get_staff_user_id()), $data['project_id']);
				}elseif(isset($data['deadline']) && !empty($data['deadline'])){
					$checkdeadline = $this->projects_model->checkdeadline($data['deadline'], $data['project_id']);
					if($checkdeadline) {
						$success = $this->projects_model->update(array('deadline'=>$data['deadline'],'project_modified'=>date('Y-m-d H:i:s'),'modified_by'=>get_staff_user_id()), $data['project_id']);
					} else {
						$error = 'Close date should not be lesser than Start date.';
					}
				}elseif(isset($data['date_finished']) && !empty($data['date_finished'])){
					$success = $this->projects_model->update(array('date_finished'=>$data['date_finished'],'project_modified'=>date('Y-m-d H:i:s'),'modified_by'=>get_staff_user_id()), $data['project_id']);
				}elseif(isset($data['status']) && !empty($data['status'])){
					
					$success = $this->projects_model->update(array('status'=>$data['status'],'project_modified'=>date('Y-m-d H:i:s'),'modified_by'=>get_staff_user_id()), $data['project_id']);
				}
			}
            if ($success) {
                $data['message'] = _l('updated_successfully', _l('project'));
            }
            if ($error) {
                $data['err'] = $error;
            }
            echo json_encode($data );
            die;
        }
    }

    public function discussions($project_id)
    {
        if ($this->projects_model->is_member($project_id) || has_permission('projects', '', 'view')) {
            if ($this->input->is_ajax_request()) {
                $this->app->get_table_data('project_discussions', [
                    'project_id' => $project_id,
                ]);
            }
        }
    }

    public function discussion($id = '')
    {
        if ($this->input->post()) {
            $message = '';
            $success = false;
            if (!$this->input->post('id')) {
                $id = $this->projects_model->add_discussion($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('project_discussion'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                ]);
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->projects_model->edit_discussion($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('project_discussion'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                ]);
            }
            die;
        }
    }

    public function get_discussion_comments($id, $type)
    {
        echo json_encode($this->projects_model->get_discussion_comments($id, $type));
    }

    public function add_discussion_comment($discussion_id, $type)
    {
        echo json_encode($this->projects_model->add_discussion_comment($this->input->post(), $discussion_id, $type));
    }

    public function update_discussion_comment()
    {
        echo json_encode($this->projects_model->update_discussion_comment($this->input->post()));
    }

    public function delete_discussion_comment($id)
    {
        echo json_encode($this->projects_model->delete_discussion_comment($id));
    }

    public function delete_discussion($id)
    {
        $success = false;
        if (has_permission('projects', '', 'delete')) {
            $success = $this->projects_model->delete_discussion($id);
        }
        $alert_type = 'warning';
        $message    = _l('project_discussion_failed_to_delete');
        if ($success) {
            $alert_type = 'success';
            $message    = _l('project_discussion_deleted');
        }
        echo json_encode([
            'alert_type' => $alert_type,
            'message'    => $message,
        ]);
    }

    public function change_milestone_color()
    {
        if ($this->input->post()) {
            $this->projects_model->update_milestone_color($this->input->post());
        }
    }

    public function upload_file($project_id)
    {
        handle_project_file_uploads($project_id);
    }

    public function change_file_visibility($id, $visible)
    {
        if ($this->input->is_ajax_request()) {
            $this->projects_model->change_file_visibility($id, $visible);
        }
    }

    public function change_activity_visibility($id, $visible)
    {
        if (has_permission('projects', '', 'create')) {
            if ($this->input->is_ajax_request()) {
                $this->projects_model->change_activity_visibility($id, $visible);
            }
        }
    }

    public function remove_file($project_id, $id)
    {
        $this->projects_model->remove_file($id);
		set_alert('success', 'File removed successfully.');
        redirect(admin_url('projects/view/' . $project_id . '?group=project_files'));
    }

    public function remove_notes($project_id, $id)
    {
        $this->projects_model->remove_notes($project_id, $id);
        redirect(admin_url('projects/view/' . $project_id . '?group=project_notes'));
    }

    public function milestones_kanban()
    {
        $data['milestones_exclude_completed_tasks'] = $this->input->get('exclude_completed_tasks') && $this->input->get('exclude_completed_tasks') == 'yes';

        $data['project_id'] = $this->input->get('project_id');
        $data['milestones'] = [];

        $data['milestones'][] = [
          'name'              => _l('milestones_uncategorized'),
          'id'                => 0,
          'total_logged_time' => $this->projects_model->calc_milestone_logged_time($data['project_id'], 0),
          'color'             => null,
          ];

        $_milestones = $this->projects_model->get_milestones($data['project_id']);

        foreach ($_milestones as $m) {
            $data['milestones'][] = $m;
        }

        echo $this->load->view('admin/projects/milestones_kan_ban', $data, true);
    }

    public function milestones_kanban_load_more()
    {
        $milestones_exclude_completed_tasks = $this->input->get('exclude_completed_tasks') && $this->input->get('exclude_completed_tasks') == 'yes';

        $status     = $this->input->get('status');
        $page       = $this->input->get('page');
        $project_id = $this->input->get('project_id');
        $where      = [];
        if ($milestones_exclude_completed_tasks) {
            $where['status !='] = Tasks_model::STATUS_COMPLETE;
        }
        $tasks = $this->projects_model->do_milestones_kanban_query($status, $project_id, $page, $where);
        foreach ($tasks as $task) {
            $this->load->view('admin/projects/_milestone_kanban_card', ['task' => $task, 'milestone' => $status]);
        }
    }

    public function milestones($project_id)
    {
        if ($this->projects_model->is_member($project_id) || has_permission('projects', '', 'view')) {
            if ($this->input->is_ajax_request()) {
                $this->app->get_table_data('milestones', [
                    'project_id' => $project_id,
                ]);
            }
        }
    }

    public function milestone($id = '')
    {
        if ($this->input->post()) {
            $message = '';
            $success = false;
            if (!$this->input->post('id')) {
                $id = $this->projects_model->add_milestone($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('project_milestone')));
                }
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->projects_model->update_milestone($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('project_milestone')));
                }
            }
        }

        redirect(admin_url('projects/view/' . $this->input->post('project_id') . '?group=project_milestones'));
    }

    public function delete_milestone($project_id, $id)
    {
        if (has_permission('projects', '', 'delete')) {
            if ($this->projects_model->delete_milestone($id)) {
                set_alert('deleted', 'project_milestone');
            }
        }
        redirect(admin_url('projects/view/' . $project_id . '?group=project_milestones'));
    }

    public function bulk_action_files()
    {
        hooks()->do_action('before_do_bulk_action_for_project_files');
        $total_deleted       = 0;
        $hasPermissionDelete = has_permission('projects', '', 'delete');
        // bulk action for projects currently only have delete button
        if ($this->input->post()) {
            $fVisibility = $this->input->post('visible_to_customer') == 'true' ? 1 : 0;
            $ids         = $this->input->post('ids');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($hasPermissionDelete && $this->input->post('mass_delete') && $this->projects_model->remove_file($id)) {
                        $total_deleted++;
                    } else {
                        $this->projects_model->change_file_visibility($id, $fVisibility);
                    }
                }
            }
        }
        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_files_deleted', $total_deleted));
        }
    }

    public function timesheets($project_id)
    {
        if ($this->projects_model->is_member($project_id) || has_permission('projects', '', 'view')) {
            if ($this->input->is_ajax_request()) {
                $this->app->get_table_data('timesheets', [
                    'project_id' => $project_id,
                ]);
            }
        }
    }

    public function timesheet()
    {
        if ($this->input->post()) {
            $message = '';
            $success = false;
            $success = $this->tasks_model->timesheet($this->input->post());
            if ($success === true) {
                $message = _l('added_successfully', _l('project_timesheet'));
            } elseif (is_array($success) && isset($success['end_time_smaller'])) {
                $message = _l('failed_to_add_project_timesheet_end_time_smaller');
            } else {
                $message = _l('project_timesheet_not_updated');
            }
            echo json_encode([
                'success' => $success,
                'message' => $message,
            ]);
            die;
        }
    }

    public function timesheet_task_assignees($task_id, $project_id, $staff_id = 'undefined')
    {
        $assignees             = $this->tasks_model->get_task_assignees($task_id);
        $data                  = '';
        $has_permission_edit   = has_permission('projects', '', 'edit');
        $has_permission_create = has_permission('projects', '', 'edit');
        // The second condition if staff member edit their own timesheet
        if ($staff_id == 'undefined' || $staff_id != 'undefined' && (!$has_permission_edit || !$has_permission_create)) {
            $staff_id     = get_staff_user_id();
            $current_user = true;
        }
        foreach ($assignees as $staff) {
            $selected = '';
            // maybe is admin and not project member
            if ($staff['assigneeid'] == $staff_id && $this->projects_model->is_member($project_id, $staff_id)) {
                $selected = ' selected';
            }
            if ((!$has_permission_edit || !$has_permission_create) && isset($current_user)) {
                if ($staff['assigneeid'] != $staff_id) {
                    continue;
                }
            }
            $data .= '<option value="' . $staff['assigneeid'] . '"' . $selected . '>' . get_staff_full_name($staff['assigneeid']) . '</option>';
        }
        echo $data;
    }

    public function remove_team_member($project_id, $staff_id)
    {
        if (has_permission('projects', '', 'edit') || has_permission('projects', '', 'create')) {
            if ($this->projects_model->remove_team_member($project_id, $staff_id)) {
                set_alert('success', _l('project_member_removed'));
            }
        }
        redirect(admin_url('projects/view/' . $project_id));
    }

    public function remove_team_contact($project_id, $staff_id)
    {
        if (has_permission('projects', '', 'edit') || has_permission('projects', '', 'create')) {
            if ($this->projects_model->remove_team_contact($project_id, $staff_id)) {
                set_alert('success', _l('project_member_removed'));
            }
        }
        redirect(admin_url('projects/view/' . $project_id));
    }

    public function save_note($project_id)
    {
        $new_str = str_replace("&nbsp;","",str_replace(" ","",$_POST['content']));
        if(trim($new_str) == '') {
            set_alert('warning', 'Cannot save empty notes.');
            redirect(admin_url('projects/view/' . $project_id . '?group=project_notes'));
            exit;
        }
        if ($this->input->post()) {
            $success = $this->projects_model->save_note($this->input->post(null, false), $project_id);
            if ($success) {
                set_alert('success', _l('note_saved_successfully'));
                redirect(admin_url('projects/view/' . $project_id . '?group=project_notes'));
            }
        } 
        
    }

    public function edit_note($project_id)
    {
        if ($this->input->post()) {
            $success = $this->projects_model->edit_note($this->input->post(null, false),$project_id);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('project_note')));
            }
            redirect(admin_url('projects/view/' . $project_id . '?group=project_notes'));
        }
    }

    public function delete($project_id)
    {
        if (has_permission('projects', '', 'delete')) {
            $project = $this->projects_model->get($project_id);
            $success = $this->projects_model->delete($project_id);
            if ($success) {
                set_alert('success', _l('deleted', _l('project')));
                if (strpos($_SERVER['HTTP_REFERER'], 'clients/') !== false) {
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    redirect(admin_url('projects'));
                }
            } else {
                set_alert('warning', _l('problem_deleting', _l('project_lowercase')));
                redirect(admin_url('projects/view/' . $project_id));
            }
        }
    }

    public function restore_project($id)
    {
        $this->projects_model->restore_project($id);
        $this->session->set_flashdata('gdpr_restore_warning', 'Project has been Restored.');
        redirect(admin_url('projects/view/' . $id));
    }

    public function copy($project_id)
    {
        if (has_permission('projects', '', 'create')) {
            $id = $this->projects_model->copy($project_id, $this->input->post());
            if ($id) {
                set_alert('success', _l('project_copied_successfully'));
                redirect(admin_url('projects/view/' . $id));
            } else {
                set_alert('danger', _l('failed_to_copy_project'));
                redirect(admin_url('projects/view/' . $project_id));
            }
        }
    }

    public function mass_stop_timers($project_id, $billable = 'false')
    {
        if (has_permission('invoices', '', 'create')) {
            $where = [
                'billed'       => 0,
                'startdate <=' => date('Y-m-d'),
            ];
            if ($billable == 'true') {
                $where['billable'] = true;
            }
            $tasks                = $this->projects_model->get_tasks($project_id, $where);
            $total_timers_stopped = 0;
            foreach ($tasks as $task) {
                $this->db->where('task_id', $task['id']);
                $this->db->where('end_time IS NULL');
                $this->db->update(db_prefix() . 'taskstimers', [
                    'end_time' => time(),
                ]);
                $total_timers_stopped += $this->db->affected_rows();
            }
            $message = _l('project_tasks_total_timers_stopped', $total_timers_stopped);
            $type    = 'success';
            if ($total_timers_stopped == 0) {
                $type = 'warning';
            }
            echo json_encode([
                'type'    => $type,
                'message' => $message,
            ]);
        }
    }

    public function get_pre_invoice_project_info($project_id)
    {
        if (has_permission('invoices', '', 'create')) {
            $data['billable_tasks'] = $this->projects_model->get_tasks($project_id, [
                'billable'     => 1,
                'billed'       => 0,
                'startdate <=' => date('Y-m-d'),
            ]);

            $data['not_billable_tasks'] = $this->projects_model->get_tasks($project_id, [
                'billable'    => 1,
                'billed'      => 0,
                'startdate >' => date('Y-m-d'),
            ]);

            $data['project_id']   = $project_id;
            $data['billing_type'] = get_project_billing_type($project_id);

            $this->load->model('expenses_model');
            $this->db->where('invoiceid IS NULL');
            $data['expenses'] = $this->expenses_model->get('', [
                'project_id' => $project_id,
                'billable'   => 1,
            ]);

            $this->load->view('admin/projects/project_pre_invoice_settings', $data);
        }
    }

    public function get_invoice_project_data()
    {
        if (has_permission('invoices', '', 'create')) {
            $type       = $this->input->post('type');
            $project_id = $this->input->post('project_id');
            // Check for all cases
            if ($type == '') {
                $type == 'single_line';
            }
            $this->load->model('payment_modes_model');
            $data['payment_modes'] = $this->payment_modes_model->get('', [
                'expenses_only !=' => 1,
            ]);
            $this->load->model('taxes_model');
            $data['taxes']         = $this->taxes_model->get();
            $data['currencies']    = $this->currencies_model->get();
            $data['base_currency'] = $this->currencies_model->get_base_currency();
            $this->load->model('invoice_items_model');

            $data['ajaxItems'] = false;
            if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
                $data['items'] = $this->invoice_items_model->get_grouped();
            } else {
                $data['items']     = [];
                $data['ajaxItems'] = true;
            }

            $data['items_groups'] = $this->invoice_items_model->get_groups();
            $data['staff']        = $this->staff_model->get('', ['action_for' => 'Active']);
            $project              = $this->projects_model->get($project_id);
            $data['project']      = $project;
            $items                = [];

            $project    = $this->projects_model->get($project_id);
            $item['id'] = 0;

            $default_tax     = unserialize(get_option('default_tax'));
            $item['taxname'] = $default_tax;

            $tasks = $this->input->post('tasks');
            if ($tasks) {
                $item['long_description'] = '';
                $item['qty']              = 0;
                $item['task_id']          = [];
                if ($type == 'single_line') {
                    $item['description'] = $project->name;
                    foreach ($tasks as $task_id) {
                        $task = $this->tasks_model->get($task_id);
                        $sec  = $this->tasks_model->calc_task_total_time($task_id);
                        $item['long_description'] .= $task->name . ' - ' . seconds_to_time_format($sec) . ' ' . _l('hours') . "\r\n";
                        $item['task_id'][] = $task_id;
                        if ($project->billing_type == 2) {
                            if ($sec < 60) {
                                $sec = 0;
                            }
                            $item['qty'] += sec2qty($sec);
                        }
                    }
                    if ($project->billing_type == 1) {
                        $item['qty']  = 1;
                        $item['rate'] = $project->project_cost;
                    } elseif ($project->billing_type == 2) {
                        $item['rate'] = $project->project_rate_per_hour;
                    }
                    $item['unit'] = '';
                    $items[]      = $item;
                } elseif ($type == 'task_per_item') {
                    foreach ($tasks as $task_id) {
                        $task                     = $this->tasks_model->get($task_id);
                        $sec                      = $this->tasks_model->calc_task_total_time($task_id);
                        $item['description']      = $project->name . ' - ' . $task->name;
                        $item['qty']              = floatVal(sec2qty($sec));
                        $item['long_description'] = seconds_to_time_format($sec) . ' ' . _l('hours');
                        if ($project->billing_type == 2) {
                            $item['rate'] = $project->project_rate_per_hour;
                        } elseif ($project->billing_type == 3) {
                            $item['rate'] = $task->hourly_rate;
                        }
                        $item['task_id'] = $task_id;
                        $item['unit']    = '';
                        $items[]         = $item;
                    }
                } elseif ($type == 'timesheets_individualy') {
                    $timesheets     = $this->projects_model->get_timesheets($project_id, $tasks);
                    $added_task_ids = [];
                    foreach ($timesheets as $timesheet) {
                        if ($timesheet['task_data']->billed == 0 && $timesheet['task_data']->billable == 1) {
                            $item['description'] = $project->name . ' - ' . $timesheet['task_data']->name;
                            if (!in_array($timesheet['task_id'], $added_task_ids)) {
                                $item['task_id'] = $timesheet['task_id'];
                            }

                            array_push($added_task_ids, $timesheet['task_id']);

                            $item['qty']              = floatVal(sec2qty($timesheet['total_spent']));
                            $item['long_description'] = _l('project_invoice_timesheet_start_time', _dt($timesheet['start_time'], true)) . "\r\n" . _l('project_invoice_timesheet_end_time', _dt($timesheet['end_time'], true)) . "\r\n" . _l('project_invoice_timesheet_total_logged_time', seconds_to_time_format($timesheet['total_spent'])) . ' ' . _l('hours');

                            if ($this->input->post('timesheets_include_notes') && $timesheet['note']) {
                                $item['long_description'] .= "\r\n\r\n" . _l('note') . ': ' . $timesheet['note'];
                            }

                            if ($project->billing_type == 2) {
                                $item['rate'] = $project->project_rate_per_hour;
                            } elseif ($project->billing_type == 3) {
                                $item['rate'] = $timesheet['task_data']->hourly_rate;
                            }
                            $item['unit'] = '';
                            $items[]      = $item;
                        }
                    }
                }
            }
            if ($project->billing_type != 1) {
                $data['hours_quantity'] = true;
            }
            if ($this->input->post('expenses')) {
                if (isset($data['hours_quantity'])) {
                    unset($data['hours_quantity']);
                }
                if (count($tasks) > 0) {
                    $data['qty_hrs_quantity'] = true;
                }
                $expenses       = $this->input->post('expenses');
                $addExpenseNote = $this->input->post('expenses_add_note');
                $addExpenseName = $this->input->post('expenses_add_name');

                if (!$addExpenseNote) {
                    $addExpenseNote = [];
                }

                if (!$addExpenseName) {
                    $addExpenseName = [];
                }

                $this->load->model('expenses_model');
                foreach ($expenses as $expense_id) {
                    // reset item array
                    $item                     = [];
                    $item['id']               = 0;
                    $expense                  = $this->expenses_model->get($expense_id);
                    $item['expense_id']       = $expense->expenseid;
                    $item['description']      = _l('item_as_expense') . ' ' . $expense->name;
                    $item['long_description'] = $expense->description;

                    if (in_array($expense_id, $addExpenseNote) && !empty($expense->note)) {
                        $item['long_description'] .= PHP_EOL . $expense->note;
                    }

                    if (in_array($expense_id, $addExpenseName) && !empty($expense->expense_name)) {
                        $item['long_description'] .= PHP_EOL . $expense->expense_name;
                    }

                    $item['qty'] = 1;

                    $item['taxname'] = [];
                    if ($expense->tax != 0) {
                        array_push($item['taxname'], $expense->tax_name . '|' . $expense->taxrate);
                    }
                    if ($expense->tax2 != 0) {
                        array_push($item['taxname'], $expense->tax_name2 . '|' . $expense->taxrate2);
                    }
                    $item['rate']  = $expense->amount;
                    $item['order'] = 1;
                    $item['unit']  = '';
                    $items[]       = $item;
                }
            }
            $data['customer_id']          = $project->clientid;
            $data['invoice_from_project'] = true;
            $data['add_items']            = $items;
            $this->load->view('admin/projects/invoice_project', $data);
        }
    }

    public function get_rel_project_data($id, $task_id = '')
    {
        if ($this->input->is_ajax_request()) {
            $selected_milestone = '';
            if ($task_id != '' && $task_id != 'undefined') {
                $task               = $this->tasks_model->get($task_id);
                $selected_milestone = $task->milestone;
            }

            $allow_to_view_tasks = 0;
            $this->db->where('project_id', $id);
            $this->db->where('name', 'view_tasks');
            $project_settings = $this->db->get(db_prefix() . 'project_settings')->row();
            if ($project_settings) {
                $allow_to_view_tasks = $project_settings->value;
            }
            $deadline = get_project_deadline($id);

            $project_contacts = $this->projects_model->get_project_contacts($id);
            $project_details = $this->projects_model->get($id);
            $project_contacts_text = '<select class="selectpicker" readonly="readonly"  name="contacts_id" data-width="100%"><option></option>';
            $i=0;
            foreach($project_contacts as $contact){
                if($contact['is_primary'] == 1) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $project_contacts_text .= '<option  value="'.$contact['contacts_id'].'" '.$selected.'>'.get_contact_full_name($contact['contacts_id']).'</option>';
                $i++;
            }
            $project_contacts_text .= ' </select>';
            $project_company_text = '<select class="selectpicker" disabled="disabled" data-width="100%">
            <option selected>'.$project_details->client_data->company.'</option>
            </select>';


            echo json_encode([
                'deadline'            => $deadline,
                'deadline_formatted'  => $deadline ? _d($deadline) : null,
                'allow_to_view_tasks' => $allow_to_view_tasks,
                'project_details'     => $project_details,
                'project_contacts'     => $project_contacts,
                'project_contacts_text'     => $project_contacts_text,
                'project_company_text'     => $project_company_text,
                'billing_type'        => get_project_billing_type($id),
                'milestones'          => render_select('milestone', $this->projects_model->get_milestones($id), [
                    'id',
                    'name',
                ], 'task_milestone', $selected_milestone),
            ]);
        }
    }

    public function invoice_project($project_id)
    {
        if (has_permission('invoices', '', 'create')) {
            $this->load->model('invoices_model');
            $data               = $this->input->post();
            $data['project_id'] = $project_id;
            $invoice_id         = $this->invoices_model->add($data);
            if ($invoice_id) {
                $this->projects_model->log_activity($project_id, 'project_activity_invoiced_project', format_invoice_number($invoice_id));
                set_alert('success', _l('project_invoiced_successfully'));
            }
            redirect(admin_url('projects/view/' . $project_id . '?group=project_invoices'));
        }
    }

    public function view_project_as_client($id, $clientid)
    {
        if (is_admin()) {
            login_as_client($clientid);
            redirect(site_url('clients/project/' . $id));
        }
    }
	public function download_attachment($attachements){
		$req_files = array();
		$i = 0;
		$attachements = urldecode($attachements);
		$attachements = json_decode($attachements);
		foreach($attachements as $attachement1){
			$ch_content = $attachement1;
			$file = $req_files[$i] = 'uploads/emails/'.$attachement1;
			$i++;
		}
		if(count($attachements)>1){
			$this->load->library('zip');
			foreach ($req_files as $req_file1) {
				$this->zip->read_file( $req_file1);
			}
			$this->zip->download('files.zip');
			$this->zip->clear_data();
		}
		else{
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			header("Content-Type: text/plain");
		}
	}
	public function download_attachment_single($attachements){
		$file = 'uploads/emails/'.$attachements;
		
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		header("Content-Type: text/plain");
		readfile($file);
	}
	public function getmessage() {
		$id = $_REQUEST['uid'];
		$project = $this->projects_model->storage_message($id);
		$add_content = "'".$id."'";
		$project_staff_id = $project->staff_id;
		$req_project_id = $project->project_id;
		$cur_project = $this->projects_model->get_project($req_project_id);
		$cur_project_staff_id = $cur_project->teamleader;
		$output = '';
        $output .= '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h4 class="modal-title"><i class="fa fa-envelope"></i> '.$project->subject.'</h4></div>';
		$output .= '<div class="modal-body"><div class="email-app"><main class="message"><div class="details">';
		//$output .= '<div class="title">'.$inboxEmails['subject'].'</div>';
		$output .= '<div class="header"  style="float:left;width:100%"><div class="from"><span>'.$project->from_email.'</span>'.$project->from_email.'</div><div class="date">'.date("d-M-Y H:i A",$project->udate).'</div></div>';
		$staffid = get_staff_user_id();
		$ch_admin = is_admin($staffid);
		$j1 = 0;
		$output1 = '';
		if(!empty($project->attachements)){
			$attachements = json_decode($project->attachements);
			
			foreach($attachements as $attachement12){
				$msg_id = $project->message_id;
				if(!empty($project->mail_by) && $project->mail_by=='outlook'){
					$downoad_url = admin_url('outlook_mail/download_attachment_single_project/'.$project->id.'/'.$j1);
				}else{
					if($project->uid!=0){
						if($project->folder!='INBOX'){
							$downoad_url = admin_url('company_mail/download_attachment_single/'.$project->uid).'?folder=[Gmail]/Sent Mail&attach_id='.$j1;
						}else{
							$downoad_url = admin_url('company_mail/download_attachment_single/'.$project->uid).'?folder=INBOX&attach_id='.$j1;
						}
					}
					else{
						$downoad_url = admin_url('projects/download_attachment_single/'.$attachement12);
					}
				}
				$output1 .= '<div class="btn btn-default pull-left" style="margin-right:10px;clear:both;margin-bottom:5px"><a href="'.$downoad_url.'">'.$attachement12.'</a></div>';
				$j1++;
			}
			if($j1>1){
				if(!empty($project->mail_by) && $project->mail_by=='outlook'){
					$msg_id = $project->message_id;
					$downoad_url = admin_url('outlook_mail/outlook_all_download_attachment?msg_id='.$msg_id);
				}
				else{
					if($project->uid!=0){
						if($project->folder!='INBOX'){
							$downoad_url = admin_url('company_mail/download_attachment/'.$project->uid).'?folder=[Gmail]/Sent Mail';
						}else{
							$downoad_url = admin_url('company_mail/download_attachment/'.$project->uid).'?folder=INBOX';
						}
					}
					else{
						$downoad_url = admin_url('projects/download_attachment/'.urlencode($project->attachements));
					}
				}
				$output1 .= '<div class="btn btn-default pull-left" style="margin-right:10px;clear:both;margin-bottom:5px"><a href="'.$downoad_url.'">Download All</a></div>';
			}
		}
		if($staffid == $project_staff_id){ 
			$replys = $this->projects_model->reply_messages($id);
			if(empty($replys)){
				$output .= '<div class="content" style="margin-bottom:100px">'.$project->body_html.'</div><div style="top:-22px;position:relative">'.$output1.'</div>';
			}
			else{
				$output .= '<div class="content" >'.$project->body_html.'</div><div>'.$output1.'</div>';
				$req1 = count($replys);
				$i = 1;
				foreach($replys as $reply1){
					$output .= '<div class="header" style="float:left;width:100%"><div class="from"><span>'.$reply1['from_email'].'</span>'.$reply1['from_email'].'</div><div class="date">'.date("d-M-Y H:i A",$reply1['udate']).'</div></div>';
					$j1 = 0;
					$output1 = '';
					if(!empty($reply1['attachements'])){
						$attachements = json_decode($reply1['attachements']);
						
						foreach($attachements as $attachement12){
							if($reply1['uid']!=0){
								if($reply1['folder']!='INBOX'){
									$downoad_url = admin_url('company_mail/download_attachment_single/'.$reply1['uid']).'?folder=[Gmail]/Sent Mail&attach_id='.$j1;
								}else{
									$downoad_url = admin_url('company_mail/download_attachment_single/'.$reply1['uid']).'?folder=INBOX&attach_id='.$j1;
								}
							}
							else{
								$downoad_url = admin_url('projects/download_attachment_single/'.$attachement12);
							}
							$output1 .= '<div class="btn btn-default pull-left" style="margin-right:10px;margin-bottom:5px"><a href="'.$downoad_url.'">'.$attachement12.'</a></div>';
							$j1++;
						}
						if($j1>1){
							if($reply1['uid']!=0){
								if($reply1['folder']!='INBOX'){
									$downoad_url = admin_url('company_mail/download_attachment/'.$reply1['uid']).'?folder=[Gmail]/Sent Mail';
								}else{
									$downoad_url = admin_url('company_mail/download_attachment/'.$reply1['uid']).'?folder=INBOX';
								}
							}
							else{
								$downoad_url = admin_url('projects/download_attachment/'.urlencode($reply1['attachements']));
							}
							$output1 .= '<div class="btn btn-default pull-left" style="margin-right:10px;"><a href="'.$downoad_url.'">Download All</a></div>';
						}
					}
					if($i == $req1){
						$output .= '<div class="content" style="margin-bottom:100px">'.$reply1['body_html'].'</div><div style="float:left;width:100%;position:relative">'.$output1.'</div>';
					}
					else{
						$output .= '<div class="content" >'.$reply1['body_html'].'</div><div>'.$output1.'</div>';
					}
					$i++;
				}
			}
			
			$output .= '<div class="col-md-12" ><div class="col-md-12"><div style="padding:0px 0px 33px 0px"><button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#forward-modal" onclick="add_content('.$add_content.')"><i class="fa fa-forward" ></i> Forward</button><button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#reply-modal" onclick="add_to('.$add_content.')" style="margin-right:10px;"><i class="fa fa-reply" ></i> Reply</button><button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#reply-modal" onclick="add_reply_all('.$add_content.')" style="margin-right:10px;"><i class="fa fa-reply" ></i> Reply All</button></div></div><div class="col-md-12"  style="margin-top:20px;">';
		}
		else{
			$output .= '<div class="content" style="margin-bottom:100px">'.$project->body_html.'</div>';
			$output .= '<div class="col-md-12" style="margin-top:-100px"><div class="col-md-12"><div style="padding:0px 0px 33px 0px"></div></div><div class="col-md-12" style="margin-top:20px;">';
			
			$j1 = 0;
			if(!empty($project->attachements)){
				$attachements = json_decode($project->attachements);
				
				foreach($attachements as $attachement12){
					if($project->uid!=0){
						if($project->folder!='INBOX'){
							$downoad_url = admin_url('company_mail/download_attachment_single/'.$project->uid).'?folder=[Gmail]/Sent Mail&attach_id='.$j1;
						}else{
							$downoad_url = admin_url('company_mail/download_attachment_single/'.$project->uid).'?folder=INBOX&attach_id='.$j1;
						}
					}else{
						$downoad_url = admin_url('projects/download_attachment_single/'.$attachement12);
					}
					$output .= '<div class="btn btn-default pull-left" style="margin-right:10px;"><a href="'.$downoad_url.'">'.$attachement12.'</a></div>';
					$j1++;
				}
				if($j1>1){
					if($project->uid!=0){
						if($project->folder!='INBOX'){
							$downoad_url = admin_url('company_mail/download_attachment/'.$project->uid).'?folder=[Gmail]/Sent Mail';
						}else{
							$downoad_url = admin_url('company_mail/download_attachment/'.$project->uid).'?folder=INBOX';
						}
					}
					else{
						$downoad_url = admin_url('projects/download_attachment/'.urlencode($project->attachements));
					}
					$output .= '<div class="btn btn-default pull-left" style="margin-right:10px;"><a href="'.$downoad_url.'">Download All</a></div>';
				}
			}
		}
		
		$output .= '</div></div></div></div></main></div></div>';
        echo $output;
        exit;
    }
	public function to_mail() {
        $id = $_REQUEST['uid'];
		$staffid = get_staff_user_id();
		$this->db->where('staffid ', $staffid);
        $staff = $this->db->get(db_prefix() . 'staff')->row();
		$project = $this->projects_model->storage_message($id);
		$req_to = $project->from_email;
		$data = array('subject'=>$project->subject,'message'=>$project->body_html,'from_address'=>$project->from_email,'to_address'=>$req_to,'message_id'=>$project->message_id);
        echo json_encode($data);
        exit;
    }
	public function add_reply_all() {
        $id = $_REQUEST['uid'];
		$staffid = get_staff_user_id();
		$this->db->where('staffid ', $staffid);
        $staff = $this->db->get(db_prefix() . 'staff')->row();
		$project = $this->projects_model->storage_message($id);
		$req_to = $project->from_email;
		if(!empty($project->to)){
			$to_mails = json_decode($project->to);
			foreach($to_mails as $req_mail1){
				if($req_mail1['email'] != $staff->email){
					$req_to .= $req_mail1['email'].',';
				}
			}
		}
		if(!empty($project->cc)){
			$cc_mails = json_decode($project->cc);
			foreach($cc_mails as $cc_mail1){
				if($cc_mail1['email'] != $staff->email){
					$req_to .= $cc_mail1['email'].',';
				}
			}
		}
		$req_to = rtrim($req_to,',');
		$data = array('subject'=>$project->subject,'message'=>$project->body_html,'from_address'=>$project->from_email,'to_address'=>$req_to);
        echo json_encode($data);
        exit;
    }
	public function content() {
		$id = $_REQUEST['uid'];
		$add_content = "'".$_REQUEST['uid']."'";
		$project = $this->projects_model->storage_message($id);
		$output = array();
		$output['subject'] = $project->subject;
		$output['message'] = $project->body_html;
		$output['message_id'] = $project->message_id;
        echo json_encode($output);
        exit;
    }
	public function send_outlook($project_id){
		$outlook_data = outlook_credential();
		$cur_token = get_outlook_token();
		$token		= $cur_token->token;
		$user_email = $cur_token->email;
		$refresh_token		= $cur_token->refresh_token;
		$check_data = refresh_token($user_email,$refresh_token);
		$cur_token = get_outlook_token();
		$token		= $cur_token->token;
		$user_email = $cur_token->email;
		$staff_id = get_staff_user_id();
		$redirect_url = site_url().'admin/projects/view/'.$project_id.'?group=project_email';
		$redirect_url1 = $redirect_url;
		$this->db->where('staffid ', $staff_id);
		$assignee_admin = $this->db->get(db_prefix() . 'staff')->row();
		$req_name = trim($assignee_admin->firstname.' '.$assignee_admin->lastname);
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
	   $ch_project_id = $project_id;
		$toemail = explode(",", $_POST["toemail"]);
		if(!empty($toemail)){
			$toemail = $toemail[0];
		}else{
			$toemail = $this->input->post('toemail');
		}
		$this->db->where('email', $this->input->post('toemail'));
		$contacts = $this->db->get(db_prefix() . 'contacts')->row();
		if(!empty($data['description'])){
			if(!empty($contacts->id)){
				$this->db->where('contacts_id', $contacts->id);
			}
			$this->db->limit(1);
			$project = $this->db->get(db_prefix() . 'project_contacts')->row();
			if ($project && !empty($ch_project_id)) {
				$data['rel_id'] = $ch_project_id;
			}
			else{
					$data['rel_id'] = 0;
			}
			if ($contacts && !empty($contacts->id)) {
				$data['contacts_id'] = $contacts->id;
			}else{
				$data['contacts_id'] = 0;
			}
			$req_name1 = '';
			$to = $cc = $bcc =array();
			$toFromForm = explode(",", $_POST["toemail"]);
			if(!empty($toFromForm)){
				foreach ($toFromForm as $eachTo) {
					if(strlen(trim($eachTo)) > 0) {
						$thisTo = array(
							"EmailAddress" => array(
								"Address" => trim($eachTo)
							)
						);
						array_push($to, $thisTo);
					}
				}
			}
			else{
				$thisTo = array(
					"EmailAddress" => array(
						"Address" => trim($_POST["toemail"])
					)
				);
				array_push($to, $thisTo);
			}
			$ccFromForm = explode(",", $_POST["ccemail"]);
			if(!empty($ccFromForm)){
				foreach ($ccFromForm as $eachcc) {
					if(strlen(trim($eachcc)) > 0) {
						$thiscc = array(
							"EmailAddress" => array(
								"Address" => trim($eachcc)
							)
						);
						array_push($cc, $thiscc);
					}
				}
			}
			else{
				$thiscc = array(
					"EmailAddress" => array(
						"Address" => trim($_POST["ccemail"])
					)
				);
				array_push($cc, $thiscc);
			}
			$bccFromForm = explode(",", $_POST["bccemail"]);
			if(!empty($bccFromForm)){
				foreach ($bccFromForm as $eachcc) {
					if(strlen(trim($eachcc)) > 0) {
						$thisbcc = array(
							"EmailAddress" => array(
								"Address" => trim($eachcc)
							)
						);
						array_push($bcc, $thisbcc);
					}
				}
			}
			else{
				$thisbcc = array(
					"EmailAddress" => array(
						"Address" => trim($_POST["bccemail"])
					)
				);
				array_push($bcc, $thisbcc);
			}
			$attachments = get_attachement();
			
			if (count($to) == 0) {
				//die("Need email address to send email");
			}
			$request = array(
				"Message" => array(
					"Subject" =>$data["name"],
					"ToRecipients" => $to,
					"Attachments" => $attachments,
					"Body" => array(
						"ContentType" => "HTML",
						"Content" => utf8_encode($data["description"])
					)
				)
			);
			$source_from1 = $source_from2 = array();
			if (!empty($attachments)) {
				$source_from1 = array_column($attachments, 'Name'); 
			}
			$request = json_encode($request);
			$headers = array(
				"User-Agent: php-tutorial/1.0",
				"Authorization: Bearer ".$token,
				"Accept: application/json",
				"Content-Type: application/json",
				"Content-Length: ". strlen($request)
			);
			$req_url = $outlook_data["api_url"].'/me/sendmail';
			$response = runCurl($req_url, $request, $headers);
			if(!empty($response)){
				$messages = $this->last_sent_item();
				if (!empty($attachments)) {
					$list_attachment = $this->list_attachment($messages['Id']);
					$source_from2 = array_column($list_attachment, 'Id'); 
				}
				if(get_option('link_deal')=='yes' && !empty($data['rel_id'])){
					if(isset($data['task_mark_complete_id']) && !empty($data['task_mark_complete_id'])){
						$this->tasks_model->mark_as(5, $data['task_mark_complete_id']);
					}
					if(isset($data['task_mark_complete_id'])){
						unset($data['task_mark_complete_id']);
					}
					$data_assignee = $data['assignees'];
					unset($data['assignees']);
					$id   = $data['taskid']  = $this->tasks_model->add($data);
					
					foreach($data_assignee as $taskey => $tasvalue ){
						$data['assignee'] = $tasvalue;
						$this->tasks_model->add_task_assignees($data);
					}
					$_id     = false;
					$success = false;
					$message = '';
					if ($id) {
						$success       = true;
						$_id           = $id;
						$message       = _l('added_successfully', _l('task'));
						$uploadedFiles = handle_task_attachments_array($id);
						if ($uploadedFiles && is_array($uploadedFiles)) {
							foreach ($uploadedFiles as $file) {
								$this->misc_model->add_attachment_to_database($id, 'task', [$file]);
							}
						}
						if ($success) {
				
							$i = $j2 = 0;
							$cur_project12 = $this->projects_model->get_project($ch_project_id);
							$messages = $this->last_sent_item();
							$req_msg[$i]['project_id']	= $ch_project_id;
							$req_msg[$i]['task_id']		= $id;
							$req_msg[$i]['staff_id'] 	= $cur_project12->teamleader;
							$req_msg[$i]['from_email'] 	= $messages['From']['EmailAddress']['Address'];
							$req_msg[$i]['from_name'] 	= $messages['From']['EmailAddress']['Name'];
							$mail_to = $mail_cc = $mail_bcc = array();
							if(!empty($messages['ToRecipients'])){
								foreach($messages['ToRecipients'] as $mail1){
									$mail_to[$j2]['email']	= $mail1['EmailAddress']['Address'];
									$mail_to[$j2]['name'] 	= $mail1['EmailAddress']['Name'];
									$j2++;
								}
							}
							$j2 = 0;
							
							if(!empty($messages['CcRecipients']['EmailAddress']['Address'])){
								foreach($messages['CcRecipients'] as $mail1){
									$mail_cc[$j2]['email']	= $mail1['EmailAddress']['Address'];
									$mail_cc[$j2]['name'] 	= $mail1['EmailAddress']['Name'];
									$j2++;
								}
							}
							$j2 = 0;
							if(!empty($messages['BccRecipients']['EmailAddress']['Address'])){
								foreach($messages['BccRecipients'] as $mail1){
									$mail_bcc[$j2]['email']	= $mail1['EmailAddress']['Address'];
									$mail_bcc[$j2]['name'] 	= $mail1['EmailAddress']['Name'];
									$j2++;
								}
							}
							$req_msg[$i]['mail_to']		= json_encode($mail_to);
							$req_msg[$i]['cc']			= json_encode($mail_cc);
							$req_msg[$i]['bcc']			= json_encode($mail_bcc);
							$req_msg[$i]['reply_to']	= json_encode($messages['ReplyTo']);
							$req_msg[$i]['message_id']	= $messages['Id'];
							$req_msg[$i]['in_reply_to']	= json_encode($messages['ReplyTo']);
							$req_msg[$i]['date']		= $messages['ReceivedDateTime'];
							$req_msg[$i]['udate']		= strtotime($messages['SentDateTime']);
							$req_msg[$i]['subject']		= $messages['Subject'];
							
							$req_msg[$i]['mail_read']	= $messages['IsRead'];
							$req_msg[$i]['answered']	= $messages['IsRead'];
							$req_msg[$i]['flagged']		= $messages['Flag']['FlagStatus'];
							$req_msg[$i]['attachements']= json_encode($source_from1);
							$req_msg[$i]['attachment_id']= json_encode($source_from2);
							$req_msg[$i]['body_html']	= $messages['Body']['Content'];
							$req_msg[$i]['body_plain']	= $messages['BodyPreview'];
							$req_msg[$i]['folder']	= 'Sent_mail';
							$req_msg[$i]['mail_by']	= 'outlook';
							$table = db_prefix() . 'localmailstorage';
							$this->db->insert_batch($table, $req_msg);
							
							echo $message       = _l('added_successfully', _l('task'));
							set_alert('success', $message);
							redirect($redirect_url);
						} 
					}
				}
				else{
					
						set_alert('success', 'Mail Send Successfully');
						redirect($redirect_url1);
				}
			}
			else{
				set_alert('danger', 'Cannot Send Mail');
						redirect($redirect_url1);
			}
			
		}
	}
	public function last_sent_item(){
		$outlook_data = outlook_credential();
		$cur_token = get_outlook_token();
		$token		= $cur_token->token;
		$user_email = $cur_token->email;
		$headers = array(
			"User-Agent: php-tutorial/1.0",
			"Authorization: Bearer ".$token,
			"Accept: application/json",
			"client-request-id: ".makeGuid(),
			"return-client-request-id: true",
			"X-AnchorMailbox: ". $user_email
		);
		$outlookApiUrl = $outlook_data["api_url"] . "/me/mailFolders" ;
		$response = runCurl($outlookApiUrl, null, $headers);
		$response = explode("\n", trim($response));
		$response = $response[count($response) - 1];
		$response = json_decode($response, true);
		if(!empty($response['value'])){
			foreach($response['value'] as $folder1){
				$icon = ucwords(strtolower($folder1['DisplayName']));
				if($icon == 'Sent Items'){
					$outlookApiUrl1 = $outlook_data["api_url"] . "/me/mailFolders/".$folder1['Id']."/messages" ;
					$response1 = runCurl($outlookApiUrl1, null, $headers);
					$response1 = explode("\n", trim($response1));
					$response1 = $response1[count($response1) - 1];
					$response1 = json_decode($response1, true);
					return $response1['value'][0];
					break;
				}
				
			}
		}
	}
	public function forward_outlook($project_id) {
		$outlook_data = outlook_credential();
		$cur_token = get_outlook_token();
		$token		= $cur_token->token;
		$user_email = $cur_token->email;
		$refresh_token		= $cur_token->refresh_token;
		$check_data = refresh_token($user_email,$refresh_token);
		$cur_token = get_outlook_token();
		$token		= $cur_token->token;
		$staff_id = get_staff_user_id();
		$redirect_url = site_url().'admin/projects/view/'.$project_id.'?group=project_email';
		$redirect_url1 = $redirect_url;
		
		$assignee_admin = $this->db->get(db_prefix() . 'staff')->row();
		$req_name = trim($assignee_admin->firstname.' '.$assignee_admin->lastname);
		$data['description'] = $this->input->post('description', false);
		$data['task_mark_complete_id'] = $this->input->post('task_mark_complete_id', false);
		$data['billable'] = $this->input->post('billable', false);
		$data['tasktype'] = $this->input->post('tasktype', false);
		$data['name'] = $this->input->post('name', false);
		$data['assignees'][0] = $assignee_admin->staffid;
		$data['startdate'] = date('d-m-Y');
		$data['priority'] = $this->input->post('priority', false);
		$data['repeat_every_custom'] = $this->input->post('repeat_every_custom', false);
		$data['repeat_type_custom'] = $this->input->post('repeat_type_custom', false);
		$data['rel_type'] = $this->input->post('rel_type', false);
		$data['tags'] = $this->input->post('tags', false);
		$this->db->where('email', $this->input->post('toemail'));
		$contacts = $this->db->get(db_prefix() . 'contacts')->row();
		
		if(!empty($data['description'])){
			$this->db->where('contacts_id', $contacts->id);
			$this->db->limit(1);
			$project = $this->db->get(db_prefix() . 'project_contacts')->row();
			$data['rel_id'] = $project_id;
				
			   if(!empty($contacts->id)){
					$data['contacts_id'] = $contacts->id;
			   }else{
				   $data['contacts_id'] = 0;
			   }
				//Initialize the connection:
				$req_name1 = '';
				$to = $cc = $bcc =array();
				
				$toFromForm = explode(",", $_POST["toemail"]);
				if(!empty($toFromForm)){
					foreach ($toFromForm as $eachTo) {
						if(strlen(trim($eachTo)) > 0) {
							$thisTo = array(
								"EmailAddress" => array(
									"Address" => trim($eachTo)
								)
							);
							array_push($to, $thisTo);
						}
					}
				}
				else{
					$thisTo = array(
						"EmailAddress" => array(
							"Address" => trim($_POST["toemail"])
						)
					);
					array_push($to, $thisTo);
				}
				$ccFromForm = explode(",", $_POST["ccemail"]);
				if(!empty($ccFromForm)){
					foreach ($ccFromForm as $eachcc) {
						if(strlen(trim($eachcc)) > 0) {
							$thiscc = array(
								"EmailAddress" => array(
									"Address" => trim($eachcc)
								)
							);
							array_push($cc, $thiscc);
						}
					}
				}
				else{
					$thiscc = array(
						"EmailAddress" => array(
							"Address" => trim($_POST["ccemail"])
						)
					);
					array_push($cc, $thiscc);
				}
				$bccFromForm = explode(",", $_POST["bccemail"]);
				if(!empty($bccFromForm)){
					foreach ($bccFromForm as $eachcc) {
						if(strlen(trim($eachcc)) > 0) {
							$thisbcc = array(
								"EmailAddress" => array(
									"Address" => trim($eachcc)
								)
							);
							array_push($bcc, $thisbcc);
						}
					}
				}
				else{
					$thisbcc = array(
						"EmailAddress" => array(
							"Address" => trim($_POST["bccemail"])
						)
					);
					array_push($bcc, $thisbcc);
				}
				$attachments = get_attachement();
				$request = array(
					"Message" => array(
						"Subject" =>$data["name"],
						"ToRecipients" => $to,
						"CcRecipients" => $cc,
						"BccRecipients" => $bcc,
						"Attachments" => $attachments,
						"Body" => array(
							"ContentType" => "HTML",
							"Content" => utf8_encode($data["description"])
						)
					)
				);
				if(!empty($attachments)){
					$request['Message']['Attachments'] = $attachments;
				}
				
				$request = json_encode($request);
				$headers = array(
					"User-Agent: php-tutorial/1.0",
					"Authorization: Bearer ".$token,
					"Accept: application/json",
					"Content-Type: application/json",
					"Content-Length: ". strlen($request)
				);			
				$msg_id  = $_REQUEST['msg_id'];
				$req_url = $outlook_data["api_url"].'/me/messages/'.$msg_id.'/forward';
				$response = runCurl($req_url, $request, $headers);
		}else{
			 $message       = 'Please enter message';
			set_alert('warning', $message);
			redirect($redirect_url1);
		}
		if(get_option('link_deal')=='yes'){
		$_id     = false;
		$success = false;
		$message = 'Activity Log Added Successfully';
	   log_activity('New Activity log Added', 'Name: ' . $data['name'] . ']');
	   set_alert('success', $message);
		}
		else{
			$message = 'Mail Send Successfully';
			set_alert('success', $message);
		}
	   redirect($redirect_url1);
    }
	public function reply_outlook($project_id) {
		$outlook_data = outlook_credential();
		$cur_token = get_outlook_token();
		$token		= $cur_token->token;
		$user_email = $cur_token->email;
		$refresh_token		= $cur_token->refresh_token;
		$check_data = refresh_token($user_email,$refresh_token);
		$cur_token = get_outlook_token();
		$token		= $cur_token->token;
		$staff_id = get_staff_user_id();
		$redirect_url = site_url().'admin/projects/view/'.$project_id.'?group=project_email';
		$redirect_url1 = $redirect_url;
		
		$assignee_admin = $this->db->get(db_prefix() . 'staff')->row();
		$req_name = trim($assignee_admin->firstname.' '.$assignee_admin->lastname);
		$data['description'] = $this->input->post('description', false);
		$data['task_mark_complete_id'] = $this->input->post('task_mark_complete_id', false);
		$data['billable'] = $this->input->post('billable', false);
		$data['tasktype'] = $this->input->post('tasktype', false);
		$data['name'] = $this->input->post('name', false);
		$data['assignees'][0] = $assignee_admin->staffid;
		$data['startdate'] = date('d-m-Y');
		$data['priority'] = $this->input->post('priority', false);
		$data['repeat_every_custom'] = $this->input->post('repeat_every_custom', false);
		$data['repeat_type_custom'] = $this->input->post('repeat_type_custom', false);
		$data['rel_type'] = $this->input->post('rel_type', false);
		$data['tags'] = $this->input->post('tags', false);
		$this->db->where('email', $this->input->post('toemail'));
		$contacts = $this->db->get(db_prefix() . 'contacts')->row();
		if(!empty($data['description'])){
			$this->db->where('contacts_id', $contacts->id);
			$this->db->limit(1);
			$project = $this->db->get(db_prefix() . 'project_contacts')->row();
				if(!empty($project->project_id)){
						$data['rel_id'] = $project->project_id;
				   }else{
					   $data['rel_id'] = $id;
				   }
				   if(!empty($contacts->id)){
						$data['contacts_id'] = $contacts->id;
				   }else{
					   $data['contacts_id'] = 0;
				   }
				//Initialize the connection:
				$req_name1 = '';
				$to = $cc = $bcc =array();
				$toFromForm = explode(",", $_POST["toemail"]);
				if(!empty($toFromForm)){
					foreach ($toFromForm as $eachTo) {
						if(strlen(trim($eachTo)) > 0) {
							$thisTo = array(
								"EmailAddress" => array(
									"Address" => trim($eachTo)
								)
							);
							array_push($to, $thisTo);
						}
					}
				}
				else{
					$thisTo = array(
						"EmailAddress" => array(
							"Address" => trim($_POST["toemail"])
						)
					);
					array_push($to, $thisTo);
				}
				$ccFromForm = explode(",", $_POST["ccemail"]);
				if(!empty($ccFromForm)){
					foreach ($ccFromForm as $eachcc) {
						if(strlen(trim($eachcc)) > 0) {
							$thiscc = array(
								"EmailAddress" => array(
									"Address" => trim($eachcc)
								)
							);
							array_push($cc, $thiscc);
						}
					}
				}
				else{
					$thiscc = array(
						"EmailAddress" => array(
							"Address" => trim($_POST["ccemail"])
						)
					);
					array_push($cc, $thiscc);
				}
				$bccFromForm = explode(",", $_POST["bccemail"]);
				if(!empty($bccFromForm)){
					foreach ($bccFromForm as $eachcc) {
						if(strlen(trim($eachcc)) > 0) {
							$thisbcc = array(
								"EmailAddress" => array(
									"Address" => trim($eachcc)
								)
							);
							array_push($bcc, $thisbcc);
						}
					}
				}
				else{
					$thisbcc = array(
						"EmailAddress" => array(
							"Address" => trim($_POST["bccemail"])
						)
					);
					array_push($bcc, $thisbcc);
				}
				$attachments = get_attachement();
				$source_from1 = $source_from2 = array();
				if (!empty($attachments)) {
					$source_from1 = array_column($attachments, 'Name'); 
				}
				$request = array(
					"Message" => array(
						"Subject" =>$data["name"],
						"ToRecipients" => $to,
						"CcRecipients" => $cc,
						"BccRecipients" => $bcc,
						"Attachments" => $attachments,
						"Body" => array(
							"ContentType" => "HTML",
							"Content" => utf8_encode($data["description"])
						)
					)
				);
				if(!empty($attachments)){
					$request['Message']['Attachments'] = $attachments;
				}
				
				$request = json_encode($request);
				$headers = array(
					"User-Agent: php-tutorial/1.0",
					"Authorization: Bearer ".$token,
					"Accept: application/json",
					"Content-Type: application/json",
					"Content-Length: ". strlen($request)
				);			
				$msg_id  = $_REQUEST['msg_id'];
				
				$req_url = $outlook_data["api_url"].'/me/messages/'.$msg_id.'/reply';
				$response = runCurl($req_url, $request, $headers);
				if(!empty($response)){
					$messages = $this->last_sent_item();
					$req_msg_id = $_REQUEST['ch_uid'];
					if (!empty($attachments)) {
						$list_attachment = $this->list_attachment($messages['Id']);
						$source_from2 = array_column($list_attachment, 'Id'); 
					}
					$cond_array = array('message_id'=> $req_msg_id);
					$this->db->where($cond_array);
					$this->db->limit(1);
					$local_storage = $this->db->get(db_prefix() . 'localmailstorage')->row();
					if(!empty($local_storage->id)){
						$i = $j2 = 0;
						$staff_id = get_staff_user_id();
						$cur_project12 = $this->projects_model->get_project($ch_project_id);
						$req_msg[$i]['project_id']	= $data['rel_id'];
						$req_msg[$i]['task_id']		= $data['rel_id'];
						$req_msg[$i]['staff_id'] 	= $staff_id;
						$req_msg[$i]['from_email'] 	= $messages['From']['EmailAddress']['Address'];
						$req_msg[$i]['from_name'] 	= $messages['From']['EmailAddress']['Name'];
						$mail_to = $mail_cc = $mail_bcc = array();
						if(!empty($messages['ToRecipients'])){
							foreach($messages['ToRecipients'] as $mail1){
								$mail_to[$j2]['email']	= $mail1['EmailAddress']['Address'];
								$mail_to[$j2]['name'] 	= $mail1['EmailAddress']['Name'];
								$j2++;
							}
						}
						$j2 = 0;
						
						if(!empty($messages['CcRecipients']['EmailAddress']['Address'])){
							foreach($messages['CcRecipients'] as $mail1){
								$mail_cc[$j2]['email']	= $mail1['EmailAddress']['Address'];
								$mail_cc[$j2]['name'] 	= $mail1['EmailAddress']['Name'];
								$j2++;
							}
						}
						$j2 = 0;
						if(!empty($messages['BccRecipients']['EmailAddress']['Address'])){
							foreach($messages['BccRecipients'] as $mail1){
								$mail_bcc[$j2]['email']	= $mail1['EmailAddress']['Address'];
								$mail_bcc[$j2]['name'] 	= $mail1['EmailAddress']['Name'];
								$j2++;
							}
						}
						$req_msg[$i]['mail_to']		= json_encode($mail_to);
						$req_msg[$i]['cc']			= json_encode($mail_cc);
						$req_msg[$i]['bcc']			= json_encode($mail_bcc);
						$req_msg[$i]['reply_to']	= json_encode($messages['ReplyTo']);
						$req_msg[$i]['message_id']	= $messages['Id'];
						$req_msg[$i]['in_reply_to']	= json_encode($messages['ReplyTo']);
						$req_msg[$i]['date']		= $messages['ReceivedDateTime'];
						$req_msg[$i]['udate']		= strtotime($messages['SentDateTime']);
						$req_msg[$i]['subject']		= $messages['Subject'];
						
						$req_msg[$i]['mail_read']	= $messages['IsRead'];
						$req_msg[$i]['answered']	= $messages['IsRead'];
						$req_msg[$i]['flagged']		= $messages['Flag']['FlagStatus'];
						$req_msg[$i]['attachements']= json_encode($source_from1);
						$req_msg[$i]['attachment_id']= json_encode($source_from2);
						$req_msg[$i]['body_html']	= $messages['Body']['Content'];
						$req_msg[$i]['body_plain']	= $messages['BodyPreview'];
						$req_msg[$i]['folder']	= 'Sent_mail';
						$req_msg[$i]['mail_by']	= 'outlook';
						$table = db_prefix() . 'localmailstorage';
						$this->db->insert_batch($table, $req_msg);
						$message = 'Activity Log Added Successfully';
						log_activity('New Activity Log Added', 'Name: ' . $data['name'] . ']');
						set_alert('success', $message);
						
					}
				}
				else{
					$message = 'Invalid message id can not send reply for this mail';
					set_alert('warning', $message);
				}
				redirect($redirect_url1);
		}else{
			 $message       = 'Please enter message';
			set_alert('warning', $message);
			redirect($redirect_url1);
		}
    }
	public function list_attachment($msg_id){
		$outlook_data = outlook_credential();
		$cur_token = get_outlook_token();
		$token		= $cur_token->token;
		$user_email = $cur_token->email;
		$headers = array(
			"User-Agent: php-tutorial/1.0",
			"Authorization: Bearer ".$token,
			"Accept: application/json",
			"client-request-id: ".makeGuid(),
			"return-client-request-id: true",
			"X-AnchorMailbox: ". $user_email
		);
		$outlookApiUrl = $outlook_data["api_url"] . "/me/Messages/".$msg_id."/attachments";
		$response = runCurl($outlookApiUrl, null, $headers);
		$response = explode("\n", trim($response));
		$response = $response[count($response) - 1];
		$response = json_decode($response, true);
		return $response["value"];
	}
	public function createtaskcompanymail($id) {
			$this->load->model('tasktype_model');
			$this->load->library('session');
			$staff_id = get_staff_user_id();
			
            $this->db->where('staffid ', $staff_id);
			$redirect_url = site_url().'admin/projects/view/'.$id.'?group=project_email';
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
            $this->db->where('email', $this->input->post('toemail'));
			$ch_project_id = $id;
            $contacts = $this->db->get(db_prefix() . 'contacts')->row();
			if (!empty($data['description'])) {
            if ($contacts) {
                $this->db->where('contacts_id', $contacts->id);
                $this->db->limit(1);
                $project = $this->db->get(db_prefix() . 'project_contacts')->row();
                if ($project) {
                    $data['rel_id'] = $ch_project_id;
                    $data['contacts_id'] = $contacts->id;
                    //Initialize the connection:

                    $this->load->library('email');
					
                     $imapconf =  $smtpconf = array();
					 $imapconf = get_imap_setting();
					 $smtpconf = get_smtp_setings();
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
            $id   = $data['taskid']  = $this->tasks_model->add($data);
			
            foreach($data_assignee as $taskey => $tasvalue ){
                $data['assignee'] = $tasvalue;
                $this->tasks_model->add_task_assignees($data);
            }
            $_id     = false;
            $success = false;
            $message = '';
            if ($id) {
                $success       = true;
                $_id           = $id;
                $message       = _l('added_successfully', _l('task'));
                $uploadedFiles = handle_task_attachments_array($id);
                if ($uploadedFiles && is_array($uploadedFiles)) {
                    foreach ($uploadedFiles as $file) {
                        $this->misc_model->add_attachment_to_database($id, 'task', [$file]);
                    }
                }
                if ($success) {
					if($uid != 'Cannot Read') {
						$source_from1 = array_column($messages['attachments'], 'name'); 
					}
					$i = 0;
					$cur_project12 = $this->projects_model->get_project($ch_project_id);
					$req_msg[$i]['project_id']	= $ch_project_id;
					$req_msg[$i]['task_id']		= $id;
					$req_msg[$i]['mailid']		= $messages['id'];
					$req_msg[$i]['uid'] 		= $messages['uid'];
					if(!empty($cur_project12->teamleader)){
						$req_msg[$i]['staff_id'] 	= $cur_project12->teamleader;
					}else{
						$cur_project12 = $this->projects_model->get_primary_project_contact($ch_project_id);
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
	public function forward($id) {
			$staff_id = get_staff_user_id();
            $this->db->where('staffid ', $staff_id);
			$redirect_url = site_url().'admin/projects/view/'.$id.'?group=project_email';
            $assignee_admin = $this->db->get(db_prefix() . 'staff')->row();
            $data['description'] = $this->input->post('description', false);
            $data['task_mark_complete_id'] = $this->input->post('task_mark_complete_id', false);
            $data['billable'] = $this->input->post('billable', false);
            $data['tasktype'] = $this->input->post('tasktype', false);
            $data['name'] = $this->input->post('name', false);
            $data['assignees'][0] = $assignee_admin->staffid;
            $data['startdate'] = date('d-m-Y');
            $data['priority'] = $this->input->post('priority', false);
            $data['repeat_every_custom'] = $this->input->post('repeat_every_custom', false);
            $data['repeat_type_custom'] = $this->input->post('repeat_type_custom', false);
            $data['rel_type'] = $this->input->post('rel_type', false);
            $data['tags'] = $this->input->post('tags', false);
            $this->db->where('email', $this->input->post('toemail'));
            $contacts = $this->db->get(db_prefix() . 'contacts')->row();
				if(!empty($data['description'])){
                $this->db->where('contacts_id', $contacts->id);
                $this->db->limit(1);
                $project = $this->db->get(db_prefix() . 'project_contacts')->row();
				   if(!empty($project->project_id)){
						$data['rel_id'] = $project->project_id;
				   }else{
					   $data['rel_id'] = $id;
				   }
				   if(!empty($contacts->id)){
						$data['contacts_id'] = $contacts->id;
				   }else{
					   $data['contacts_id'] = 0;
				   }
                    //Initialize the connection:

                    $this->load->library('email');
                      $imapconf =  $smtpconf = array();
					 $imapconf = get_imap_setting();
					 $smtpconf = get_smtp_setings();
                    $this->email->initialize($smtpconf);

                    $this->email->from($smtpconf['username'], 'New Activity log Added');
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
					} 

                    if ($ch_data = $this->email->send()) {
						if(!empty($req_files)){
							foreach($req_files as $req_file12){
								unlink($req_file12);
							}
						}
                        $this->load->library('imap');
                        //Initialize the connection:
                        $imap = $this->imap->check_imap($imapconf);
						//Get the required datas:
                        if ($imap) {
                            $uid = $this->imap->get_company_latest_email_addresses($imapconf);
                           
							$data['source_from'] = $uid;
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
                //}
            //} 
			}else{
				 $message       = 'Please enter message';
                set_alert('warning', $message);
                redirect($redirect_url);
			}
            
			if(get_option('link_deal')=='yes'){
			
            $data_assignee = $data['assignees'];
            unset($data['assignees']);
			
            $_id     = false;
            $success = false;
            $message = 'Activity Log Added Successfully';
           log_activity('New Activity log Added', 'Name: ' . $data['name'] . ']');
		   set_alert('success', $message);
			}
			else{
			$message = 'Mail Send Successfully';
			set_alert('success', $message);
		}
		   redirect($redirect_url);
    }
	public function reply($id) {
		
			$redirect_url = site_url().'admin/projects/view/'.$id.'?group=project_email';
			$staff_id = get_staff_user_id();
            $this->db->where('staffid ', $staff_id);
            $assignee_admin = $this->db->get(db_prefix() . 'staff')->row();
            $data['description'] = $this->input->post('description', false);
            $data['task_mark_complete_id'] = $this->input->post('task_mark_complete_id', false);
            $data['billable'] = $this->input->post('billable', false);
            $data['tasktype'] = $this->input->post('tasktype', false);
            $data['name'] = $this->input->post('name', false);
            $data['assignees'][0] = $assignee_admin->staffid;
            $data['startdate'] = date('d-m-Y');
            $data['priority'] = $this->input->post('priority', false);
            $data['repeat_every_custom'] = $this->input->post('repeat_every_custom', false);
            $data['repeat_type_custom'] = $this->input->post('repeat_type_custom', false);
            $data['rel_type'] = $this->input->post('rel_type', false);
            $data['tags'] = $this->input->post('tags', false);
            $this->db->where('email', $this->input->post('toemail'));
            $contacts = $this->db->get(db_prefix() . 'contacts')->row();
			if(!empty($data['description'])){
                $this->db->where('contacts_id', $contacts->id);
                $this->db->limit(1);
                $project = $this->db->get(db_prefix() . 'project_contacts')->row();
                   if(!empty($project->project_id)){
						$data['rel_id'] = $project->project_id;
				   }else{
					   $data['rel_id'] = $id;
				   }
				   if(!empty($contacts->id)){
						$data['contacts_id'] = $contacts->id;
				   }else{
					   $data['contacts_id'] = 0;
				   }
                    //Initialize the connection:

                    $this->load->library('email');
                     $imapconf =  $smtpconf = array();
					 $imapconf = get_imap_setting();
					 $smtpconf = get_smtp_setings();
                    $this->email->initialize($smtpconf);

                    $this->email->from($imapconf['username'], 'New Activity log Added');
                    $list = array($this->input->post('toemail', false));
                    $this->email->to($list);
                    $this->email->cc($this->input->post('ccemail', false));
                    $this->email->bcc($this->input->post('bccemail', false));
                    $this->email->reply_to($list);
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
						
					}
                    if ($ch_data = $this->email->send()) {
						
                        $this->load->library('imap');
                        //Initialize the connection:
                        $imap = $this->imap->check_imap($imapconf);
						//Get the required datas:
                        if ($imap) {
                            $uid = $this->imap->get_company_latest_email_addresses($imapconf);
							if($uid == 'Cannot Read') {
								$messages = get_mail_message($_POST,$imapconf);
								
							}else{
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
               
			}else{
				 $message       = 'Please enter message';
                set_alert('warning', $message);
                redirect($redirect_url);
			}
			$i = 0;
			if($uid != 'Cannot Read') {
				$source_from1 = array_column($messages['attachments'], 'name'); 
			}
			$req_msg[$i]['project_id']	= $id;
			$req_msg[$i]['local_id']	= $_POST['local_id'];
			$req_msg[$i]['task_id']		= $id;
			$req_msg[$i]['mailid']		= $messages['id'];
			$req_msg[$i]['uid'] 		= $messages['uid'];
			$req_msg[$i]['staff_id'] 	= $staff_id;
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
			$table = db_prefix() . 'reply';
			$this->db->insert_batch($table, $req_msg);
			
            
			if(get_option('link_deal')=='yes'){
            $_id     = false;
            $success = false;
            set_alert('success', 'New Activity Log Added Successfully');
            log_activity('New Activity Log Added', 'Name: ' . $data['name'] . ']');
		    
			}
			else{
			$message = 'Mail Send Successfully';
			set_alert('success', $message);
		}
		redirect($redirect_url);
    }
	public function trash(){
		$this->load->library('imap');
		$staffid = get_staff_user_id();
		$table = db_prefix() . 'personal_mail_setting';
		$imapconf = get_imap_setting();
		
		
		if(!empty($_REQUEST['mails'])){
			foreach($_REQUEST['mails'] as $org_id12){
				$this->db->where('id', $org_id12);
				$contacts = $this->db->get(db_prefix() . 'localmailstorage')->row();
				if($contacts->folder == 'INBOX'){
					$this->imap->move_to_trash($_REQUEST['mails'],$imapconf,'INBOX');
				}
				else{
					$this->imap->move_to_trash($_REQUEST['mails'],$imapconf,'[Gmail]/Sent Mail');
				}
				$this->db->where_in('id', $org_id12);
				$this->db->delete(db_prefix() .'localmailstorage');
			}
		}
		echo json_encode($_REQUEST['mails']);
    }
    
    public function savepipelineAndstage()
    {
            $data = $this->input->post();
            if(isset($data['pipeline_id']) && !empty($data['pipeline_id'])){
                $success = $this->projects_model->update(array('pipeline_id'=>$data['pipeline_id']), $data['project_id']);
            }
            if(isset($data['status']) && !empty($data['status'])){
                $success = $this->projects_model->update(array('status'=>$data['status']), $data['project_id']);
            }
            if ($success) {
                $data['message'] = _l('updated_successfully', _l('project'));
            }
            if ($error) {
                $data['err'] = $error;
            }
            echo json_encode($data );
            die;
    }
	public function kanban_forecast_more_load()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
		if (!empty($this->session->userdata('pipelines'))) {
			$pipeline = $this->session->userdata('pipelines');
			$data['statuses'] = $this->pipeline_model->getPipelineprojectsstatus($pipeline);
		}
		else {
			$pipeline = '';
			$data['statuses'] = $this->pipeline_model->getPipelineprojectsstatus();
        }
        $data['selectedpipeline'] = $pipeline;
        echo $this->load->view('admin/projects/kan-ban_forecast_ajax', $data, true);
    }
	public function bulk_edit(){
		$req_project = $_POST['edit_cur_id'];
		$target_ids = explode(",",rtrim($req_project,","));
		$all_target1 = count($target_ids);
		$message = '';
		if(!empty($_POST['sel_project_cost'])){
			$this->db->where_in("projectid", array(rtrim($req_project,",")));
			$products = $this->db->get(db_prefix() . 'project_products')->result_array();
			if(!empty($products)){
				$i1 = 0;
				foreach($products as $product12){
					if (($key = array_search($product12['projectid'], $target_ids)) !== false) {
						unset($target_ids[$key]);
						$i1++;
					}
				}
				$message1 = "can't edit ".$i1." deal because product added that deal";
				$this->session->set_flashdata('warning_msg_deal', $message1);				 
			}
			$req_project = implode(',',$target_ids);
			if(!empty($req_project)){
				$target_ids = explode(",",rtrim($req_project,","));
			}else{
				$target_ids = array();
			}
		}
		$all_target = count($target_ids);
		$redirect_url = site_url().'admin/projects/index_list?pipelines=&member=&gsearch=';
		if(!empty($_POST['deal_fields'])){
			$all_fields = $_POST['deal_fields'];
			$upd_data	= array();
			foreach($all_fields as $key=>$all_field1){
				if($all_field1 == 'Edit current value...'){
					if($key == 'teamleader_name'){
						$key = 'teamleader';
						$upd_data[$key] = $_POST['sel_teamleader_name'];
					}
					else{
						$upd_data[$key] = $_POST['sel_'.$key];
					}
					if($key == 'project_cost'){
						$upd_data['project_currency'] = $_POST['project_currency'];
					}
				}
				else if($all_field1 == 'Clear the field'){
					$upd_data[$key] = '';
				}
			}
			$success = $this->projects_model->update_multiple($upd_data, $req_project);
			if ($success) {
				if(!empty($all_target)){
					if($all_target>0){
						$message = _l('updated_successfully', $all_target.' '._l('project').'s');
					}
					else{
						$message = _l('updated_successfully', $all_target.' '._l('project'));
					}
					set_alert('success', $message);
				}
			}
			
		}
		
		if(!empty($_POST['custom_fields']['projects'])){
			foreach($_POST['custom_fields']['projects'] as $key=>$custom_field1){
				$upd_data	= array();
				if($_POST[$key] == 'Edit current value...'){
					$upd_data['value'] = $_POST['custom_fields']['projects'][$key];
					$this->projects_model->update_multiple_custom($upd_data,$req_project,$key);
				}
				else if($_POST[$key] == 'Clear the field'){
					$upd_data['value'] = '';
					$this->projects_model->update_multiple_custom($upd_data,$req_project,$key);
				}
			}
			if(!empty($all_target)){
				if($all_target>0){
					$message = _l('updated_successfully', $all_target.' '._l('project').'s');
				}
				else{
					$message = _l('updated_successfully', $all_target.' '._l('project'));
				}
				set_alert('success', $message);
			}
		}
		redirect($redirect_url,true);
        die;
	}
	public function edit_multiple(){
		$req_project = $_REQUEST['ids'];
		$target_ids = rtrim($req_project,",");
		$projects = $this->projects_model->get_multiples($target_ids);
		$projects = array_column($projects,'start_date');
		usort($projects, function($a, $b) {
			$dateTimestamp1 = strtotime($a);
			$dateTimestamp2 = strtotime($b);

			return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
		});
		$req_out = array();

		if(!empty($projects[0])){
			$req_out['start_date'] = $projects[0];
		}
		$stages = $this->projects_model->get_pipelinestages($target_ids);
		$stage_option = '<option value="">Select status</option>';
		if(!empty($stages)){
			foreach($stages as $stage12){
				$stage_option .= "<option value='".$stage12['id']."'>".$stage12['name']."</option>";
			}
		}
		$req_out['stages'] = $stage_option;
		echo json_encode($req_out);
	}
	public function stages(){
		$req_project = $_REQUEST['pipeline'];
		$req_out = array();
		$stages = $this->projects_model->pipelinestagebypipelineid($req_project);
		$stage_option = '<option value="">'._l('select_stage').'</option>';
		if(!empty($stages)){
			foreach($stages as $stage12){
				$stage_option .= "<option value='".$stage12['id']."'>".$stage12['name']."</option>";
			}
		}
		$req_out['stages'] = $stage_option;
		echo json_encode($req_out);
	}
	public function get_org_person(){
		$req_project = $_REQUEST['org'];
		$req_out = array();
		$persons = $this->projects_model->get_org_contact($req_project);
        $person_option ='';
		if(!empty($persons)){
			foreach($persons as $person12){
				$person_option .= "<option value='".$person12['id']."'>".$person12['firstname'].' '.$person12['lastname']."</option>";
			}
		}
		$req_out['persons'] = $person_option;
		echo json_encode($req_out);
	}
	public function get_cont_person(){
		$req_project = $_REQUEST['person'];
		$req_out = array();
		$persons = $this->projects_model->get_contactsbyids($req_project);
		if(!empty($persons)){
			foreach($persons as $person12){
				$person_option .= "<option value='".$person12['id']."'>".$person12['firstname'].' '.$person12['lastname']."</option>";
			}
		}
		$req_out['persons'] = $person_option;
		echo json_encode($req_out);
	}

    public function approve($id)
    {
        if($this->input->post('status') ==0 && !$this->input->post('reason')){
            echo json_encode(
                array(
                    'success'=>false,
                    'msg'=>'Reason cannot be empty'
                )
            );
            die;
        }
        
        if($this->input->post('status') ==0 && strlen(trim($this->input->post('remarks')))==0){
            echo json_encode(
                array(
                    'success'=>false,
                    'msg'=>'Remarks cannot be empty'
                )
            );
            die;
        }
        
        $success =approve_deal($id);
        if($success){
            if($this->input->post('status')==1){
                $msg ='Deal approved successfully';
            }else{
                $msg ='Deal rejected successfully';
            }

            if($success =='redirect_projects'){
                echo json_encode(
                    array(
                        'success'=>true,
                        'msg'=>$msg,
                        'redirect'=>admin_url('projects/index_list'),
                    )
                );
            }else{
                echo json_encode(
                    array(
                        'success'=>true,
                        'msg'=>$msg
                    )
                );
            }
            
            die;
        }else{
            echo json_encode(
                array(
                    'success'=>false,
                    'msg'=>'Could not approve this deal'
                )
            );
        }
    }

    public function approvalReopen($deal_id)
    {
        if (!has_permission('projects', '', 'edit')) {
            access_denied($this->moudle_permission_name);
        }

        $this->db->where('rel_type','projects');
        $this->db->where('rel_id',$deal_id);
        $this->db->update(db_prefix().'approval_history',['reopened'=>1]);

        //for auto approval
        $this->load->model('approval_model');
        $this->db->where('id',$deal_id);
        $project =$this->db->get(db_prefix().'projects')->row();
        if($project){
            $approvals =$this->approval_model->getDealReportingLevels($project->teamleader);
            if($approvals && !$approvals[0]){
                approve_deal($deal_id);
            }
        }
        set_alert('success', 'Reopened Successfully');
        redirect(admin_url('projects/view/'.$deal_id));
    }

    public function sendtoapproval($deal_id)
    {
        $this->load->model('approval_model');
        $hasHistory =$this->approval_model->hasHistory('projects',$deal_id);
        if(!$hasHistory){
            hooks()->do_action('after_add_project_approval', $deal_id);
        }
    }
}
