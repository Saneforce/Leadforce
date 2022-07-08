<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tasks extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('projects_model');
        $this->load->model('tasktype_model');
        $this->load->model('callsettings_model');
		$this->load->library('session');
        unset($_SESSION['pipelines']);
        unset($_SESSION['member']);
        unset($_SESSION['gsearch']);
    }

    /* Open also all taks if user access this /tasks url */
    public function index($id = '')
    {
        $this->tasks_model->auto_update_status();
        $this->list_tasks($id);
    }

    /* List all tasks */
    public function list_tasks($id = '')
    {
        close_setup_menu();
        // If passed from url
        $data['custom_view'] = $this->input->get('custom_view') ? $this->input->get('custom_view') : '';
        $data['taskid']      = $id;

        if ($this->input->get('kanban')) {
            $this->switch_kanban(0, true);
        }

        $data['switch_kanban'] = false;
        $data['bodyclass']     = 'tasks-page';

        if ($this->session->userdata('tasks_kanban_view') == 'true') {
            $data['switch_kanban'] = true;
            $data['bodyclass']     = 'tasks-page kan-ban-body';
        }

        $data['title'] = _l('tasks');
		$fields = get_option('deal_fields');
		$data['need_fields'] = array('project_name','id','tasktype','priority','assignees','task_name','description','tags','company','project_contacts','teamleader','status','project_status','startdate','dateadded','datemodified','datefinished');
		
        $this->load->view('admin/tasks/manage', $data);
    }
    /* List all tasks */
    public function gettasks_summary_data($id = '')
    {
        $data['title'] = _l('tasks');
        $this->load->view('admin/tasks/_summary_data', $data);
    }

    public function table()
    {
        //pr($_REQUEST); exit;
        $this->app->get_table_data('tasks');
    }

    public function kanban()
    {
        echo $this->load->view('admin/tasks/kan_ban', [], true);
    }

    public function ajax_search_assign_task_to_timer()
    {
        if ($this->input->is_ajax_request()) {
            $q = $this->input->post('q');
            $q = trim($q);
            $this->db->select('name, id,' . tasks_rel_name_select_query() . ' as subtext');
            $this->db->from(db_prefix() . 'tasks');
            $this->db->where('' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . get_staff_user_id() . ')');
            //   $this->db->where('id NOT IN (SELECT task_id FROM '.db_prefix().'taskstimers WHERE staff_id = ' . get_staff_user_id() . ' AND end_time IS NULL)');
            $this->db->where('status != ', 5);
            $this->db->where('billed', 0);
            $this->db->where('(name LIKE "%' . $q . '%" OR ' . tasks_rel_name_select_query() . ' LIKE "%' . $q . '%")');
            echo json_encode($this->db->get()->result_array());
        }
    }

    public function tasks_kanban_load_more()
    {
        $status = $this->input->get('status');
        $page   = $this->input->get('page');

        $where = [];
        if ($this->input->get('project_id')) {
            $where['rel_id']   = $this->input->get('project_id');
            $where['rel_type'] = 'project';
        }

        $tasks = $this->tasks_model->do_kanban_query($status, $this->input->get('search'), $page, false, $where);

        foreach ($tasks as $task) {
            $this->load->view('admin/tasks/_kan_ban_card', [
                'task'   => $task,
                'status' => $status,
            ]);
        }
    }

    public function update_order()
    {
        $this->tasks_model->update_order($this->input->post());
    }

    public function switch_kanban($set = 0, $manual = false)
    {
        if ($set == 1) {
            $set = 'false';
        } else {
            $set = 'true';
        }

        $this->session->set_userdata([
            'tasks_kanban_view' => $set,
        ]);
        if ($manual == false) {
            // clicked on VIEW KANBAN from projects area and will redirect again to the same view
            if (strpos($_SERVER['HTTP_REFERER'], 'project_id') !== false) {
                redirect(admin_url('tasks'));
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }
	

    // Used in invoice add/edit
    public function get_billable_tasks_by_project($project_id)
    {
        if ($this->input->is_ajax_request() && (has_permission('invoices', '', 'edit') || has_permission('invoices', '', 'create'))) {
            $customer_id = get_client_id_by_project_id($project_id);
            echo json_encode($this->tasks_model->get_billable_tasks($customer_id, $project_id));
        }
    }
    // Used in invoice add/edit
    public function get_billable_tasks_by_lead($lead_id)
    {
        if ($this->input->is_ajax_request() && (has_permission('invoices', '', 'edit') || has_permission('invoices', '', 'create'))) {
            $customer_id = get_client_id_by_leads_id($lead_id);
            echo json_encode($this->tasks_model->get_billable_tasks($customer_id, $lead_id,'lead'));
        }
    }

    // Used in invoice add/edit
    public function get_billable_tasks_by_customer_id($customer_id)
    {
        if ($this->input->is_ajax_request() && (has_permission('invoices', '', 'edit') || has_permission('invoices', '', 'create'))) {
            echo json_encode($this->tasks_model->get_billable_tasks($customer_id));
        }
    }

    public function update_task_description($id)
    {
        if (has_permission('tasks', '', 'edit')) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'tasks', [
                'description' => $this->input->post('description', false),
            ]);
        }
    }

    public function detailed_overview()
    {
        $overview = [];

        $has_permission_create = has_permission('tasks', '', 'create');
        $has_permission_view   = has_permission('tasks', '', 'view');
        // ROle based records
        $my_staffids = $this->staff_model->get_my_staffids();
        if (!$has_permission_view) {
            $staff_id = get_staff_user_id();
        } elseif ($this->input->post('member')) {
            $staff_id = $this->input->post('member');
        } else {
            $staff_id = '';
        }

        $month = ($this->input->post('month') ? $this->input->post('month') : date('m'));
        if ($this->input->post() && $this->input->post('month') == '') {
            $month = '';
        }

        $status = $this->input->post('status');

        $fetch_month_from = 'startdate';

        $year       = ($this->input->post('year') ? $this->input->post('year') : date('Y'));
        $project_id = $this->input->get('project_id');

        for ($m = 1; $m <= 12; $m++) {
            if ($month != '' && $month != $m) {
                continue;
            }

            // Task rel_name
            $sqlTasksSelect = '*,' . tasks_rel_name_select_query() . ' as rel_name';

            // Task logged time
            $selectLoggedTime = get_sql_calc_task_logged_time('tmp-task-id');
            // Replace tmp-task-id to be the same like tasks.id
            $selectLoggedTime = str_replace('tmp-task-id', db_prefix() . 'tasks.id', $selectLoggedTime);

            if (is_numeric($staff_id)) {
                $selectLoggedTime .= ' AND staff_id=' . $staff_id;
                $sqlTasksSelect .= ',(' . $selectLoggedTime . ')';
            } else {
                $sqlTasksSelect .= ',(' . $selectLoggedTime . ')';
            }

            $sqlTasksSelect .= ' as total_logged_time';

            // Task checklist items
            $sqlTasksSelect .= ',' . get_sql_select_task_total_checklist_items();

            if (is_numeric($staff_id)) {
                $sqlTasksSelect .= ',(SELECT COUNT(id) FROM ' . db_prefix() . 'task_checklist_items WHERE taskid=' . db_prefix() . 'tasks.id AND finished=1 AND finished_from=' . $staff_id . ') as total_finished_checklist_items';
            } else {
                $sqlTasksSelect .= ',' . get_sql_select_task_total_finished_checklist_items();
            }

            // Task total comment and total files
            $selectTotalComments = ',(SELECT COUNT(id) FROM ' . db_prefix() . 'task_comments WHERE taskid=' . db_prefix() . 'tasks.id';
            $selectTotalFiles    = ',(SELECT COUNT(id) FROM ' . db_prefix() . 'files WHERE rel_id=' . db_prefix() . 'tasks.id AND rel_type="task"';

            if (is_numeric($staff_id)) {
                $sqlTasksSelect .= $selectTotalComments . ' AND staffid=' . $staff_id . ') as total_comments_staff';
                $sqlTasksSelect .= $selectTotalFiles . ' AND staffid=' . $staff_id . ') as total_files_staff';
            }

            $sqlTasksSelect .= $selectTotalComments . ') as total_comments';
            $sqlTasksSelect .= $selectTotalFiles . ') as total_files';

            // Task assignees
            $sqlTasksSelect .= ',' . get_sql_select_task_asignees_full_names() . ' as assignees' . ',' . get_sql_select_task_assignees_ids() . ' as assignees_ids';

            $this->db->select($sqlTasksSelect);

            $this->db->where('MONTH(' . $fetch_month_from . ')', $m);
            $this->db->where('YEAR(' . $fetch_month_from . ')', $year);

            if ($project_id && $project_id != '') {
                $this->db->where('rel_id', $project_id);
                $this->db->where('rel_type', 'project');
            }

            if (!$has_permission_view) {
                $sqlWhereStaff = '(id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid=' . $staff_id . ')';

                // User dont have permission for view but have for create
                // Only show tasks createad by this user.
                if ($has_permission_create) {
                    $sqlWhereStaff .= ' OR addedfrom=' . get_staff_user_id();
                }

                $sqlWhereStaff .= ')';
                $this->db->where($sqlWhereStaff);
            } elseif ($has_permission_view) {
                if (is_numeric($staff_id)) {
                    $this->db->where('(id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid=' . $staff_id . '))');
                }
            }

            if($my_staffids){
                $this->db->where('rel_id IN (SELECT ' . db_prefix() . 'projects.id FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ') OR  ' . db_prefix() . 'projects.teamleader in (' . implode(',',$my_staffids) . ') )');
            }

            if ($status) {
                $this->db->where('status', $status);
            }

            $this->db->order_by($fetch_month_from, 'ASC');
            array_push($overview, $m);
            $overview[$m] = $this->db->get(db_prefix() . 'tasks')->result_array();
        }

        unset($overview[0]);

        $overview = [
            'staff_id' => $staff_id,
            'detailed' => $overview,
        ];

        $data['members']  = $this->staff_model->get();
        $data['overview'] = $overview['detailed'];
        $data['years']    = $this->tasks_model->get_distinct_tasks_years(($this->input->post('month_from') ? $this->input->post('month_from') : 'startdate'));
        $data['staff_id'] = $overview['staff_id'];
        $data['title']    = _l('detailed_overview');
        $this->load->view('admin/tasks/detailed_overview', $data);
    }

    public function init_relation_tasks($rel_id, $rel_type)
    {
		$fields = get_option('deal_fields');
        $data['need_fields'] = array('project_name','id','tasktype','priority','assignees','task_name','description','tags','company','project_contacts','teamleader','status','project_status','startdate','dateadded','datemodified','datefinished');
		if(!empty($fields) && $fields != 'null'){
			$req_fields = json_decode($fields);
			$i = 17;
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
		//$data['need_fields'] = array('project_name','id','tasktype','priority','assignees','task_name','description','tags','company','project_contacts','teamleader','status','project_status','startdate');
		//$fields = get_option('deal_fields');
		//$data['need_fields'] = array('project_name','id','tasktype','priority','assignees','task_name','description','tags','company','project_contacts','teamleader','status','project_status','startdate');
		$data['rel_id'] = $rel_id;
		$data['rel_type'] = $rel_type;
       if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('tasks_relations', $data);
        }
    }
	


    public function emailmanagement() {
        $this->load->library('imap');
        $imapconf = array();
        $imapsettings = $this->db->get(db_prefix() . 'options')->result_array();
        foreach($imapsettings as $config) {
            if($config['name'] == 'imap_host')
                $imapconf['host'] = $config['value'];
            if($config['name'] == 'imap_encryption')
                $imapconf['encrypto'] = $config['value'];
            if($config['name'] == 'imap_username')
                $imapconf['username'] = $config['value'];
            if($config['name'] == 'imap_password')
                $imapconf['password'] = $config['value'];
            if($config['name'] == 'imap_port')
                $imapconf['port'] = $config['value'];
        }
        $imapconf['validate'] = true;
        //Initialize the connection:
        $this->imap->connect($imapconf);
        //Get the required datas:
        $data['folders']       = $this->imap->get_folders_inbox();
        //echo "<pre>"; print_r($data); exit;
        $data['title']         = _l('acs_emailmanagemnet');
        $this->load->view('admin/staff/emailmanagment', $data);
    }

     /* Add new task or update existing 
    
    Array
(
    [/admin/tasks/task/] => 
    [task_mark_complete_id] => 
    [billable] => on
    [tasktype] => 2
    [name] => test
    [description] => test description
    [assignees] => Array
        (
            [0] => 1
        )

    [startdate] => 10-09-2020
    [priority] => 2
    [repeat_every_custom] => 1
    [repeat_type_custom] => day
    [rel_type] => project
    [rel_id] => 102
    [contacts_id] => 105
    [tags] => 
)
    */

    public function createtaskbymail() {
       // echo "<pre>"; print_r($_REQUEST); exit;
            $this->db->where('admin', 1);
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
            if ($contacts) {
                $this->db->where('contacts_id', $contacts->id);
                $this->db->limit(1);
                $project = $this->db->get(db_prefix() . 'project_contacts')->row();
                if ($project) {
                    $data['rel_id'] = $project->project_id;
                    $data['contacts_id'] = $project->contacts_id;
                    //Initialize the connection:

                    $this->load->library('email');
                    $smtpconf = array();
                    $smtpsettings = $this->db->get(db_prefix() . 'options')->result_array();
                    foreach($smtpsettings as $config) {
                        if($config['name'] == 'smtp_host')
                            $smtpconf['host'] = $config['value'];
                        if($config['name'] == 'smtp_encryption')
                            $smtpconf['encrypto'] = $config['value'];
                        if($config['name'] == 'smtp_username')
                            $smtpconf['username'] = $config['value'];
                        if($config['name'] == 'smtp_password')
                            $smtpconf['password'] = $config['value'];
                        if($config['name'] == 'smtp_port')
                            $smtpconf['port'] = $config['value'];
                    }
                    $smtpconf['validate'] = true;
                    // $config = array(
                    //     'protocol'     => smtp_protocol,
                    //     'smtp_host'     => smtp_host,
                    //     'smtp_port'     => smtp_port,
                    //     'smtp_user' => smtp_user,
                    //     'smtp_pass'     => smtp_pass,
                    //     'charset'     => smtp_charset,
                    //     'mailtype'     => smtp_mailtype,
                    //     'newline' => smtp_newline
                    // );

                    $this->email->initialize($smtpconf);

                    $this->email->from($smtpconf['username'], 'New Activity Created');
                    $list = array($this->input->post('toemail', false));
                    $this->email->to($list);
                    $this->email->cc($this->input->post('ccemail', false));
                    $this->email->bcc($this->input->post('bccemail', false));
                    $this->email->reply_to($smtpconf['username'], 'Replay me');
                    $this->email->subject($this->input->post('name', false));
                    $this->email->message($this->input->post('description', false));

                    if ($this->email->send()) {
                        $this->load->library('imap');
                        $imapconf = array();
                        $imapsettings = $this->db->get(db_prefix() . 'options')->result_array();
                        foreach($imapsettings as $config) {
                            if($config['name'] == 'imap_host')
                                $imapconf['host'] = $config['value'];
                            if($config['name'] == 'imap_encryption')
                                $imapconf['encrypto'] = $config['value'];
                            if($config['name'] == 'imap_username')
                                $imapconf['username'] = $config['value'];
                            if($config['name'] == 'imap_password')
                                $imapconf['password'] = $config['value'];
                            if($config['name'] == 'imap_port')
                                $imapconf['port'] = $config['value'];
                        }
                        $imapconf['validate'] = true;
                        // $configImap = array(
                        //     'host'     => imap_host,
                        //     'encrypto' => imap_encrypto,
                        //     'username'     => imap_username,
                        //     'password'     => imap_password,
                        //     'port'     => imap_port,
                        //     'validate' => true
                        // );
                        
                        //Initialize the connection:
                        $imap = $this->imap->connect($imapconf);
                        //echo "<pre>"; print_r($imap); exit;
                        //Get the required datas:
                        if ($imap) {
                            $uid = $this->imap->get_latest_email_addresses();
                            $data['source_from'] = $uid;
                        } else {
                            $message       = 'Cannot Connect IMAP Server.';
                            set_alert('warning', $message);
                            redirect(admin_url('tasks'));
                        }
                    } else {
                        $message       = 'Cannot Connect SMTP Server.';
                        set_alert('warning', $message);
                        redirect(admin_url('tasks'));
                    }
                } else {
                    $message       = 'Cannot create Activity.';
                    set_alert('warning', $message);
                    redirect(admin_url('tasks'));
                }
            } else {
                $message       = 'Email address not exist.';
                set_alert('warning', $message);
                redirect(admin_url('tasks'));
            }
            
			
			if(isset($data['task_mark_complete_id']) && !empty($data['task_mark_complete_id'])){
				$this->tasks_model->mark_as(5, $data['task_mark_complete_id']);
			}
			if(isset($data['task_mark_complete_id'])){
				unset($data['task_mark_complete_id']);
			}
            if (!has_permission('tasks', '', 'create')) {
                header('HTTP/1.0 400 Bad error');
                echo json_encode([
                    'success' => false,
                    'message' => _l('access_denied'),
                ]);
                die;
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
                    $message       = _l('added_successfully', _l('task'));
                    set_alert('success', $message);
                    redirect(admin_url('tasks'));
                } 
            }
    }
	
	

    
	/* Add new task or update existing */
    public function task($id = '')
    {
        if (!has_permission('tasks', '', 'edit') && !has_permission('tasks', '', 'create')) {
            ajax_access_denied();
        }
        // $updateactiv['tasktype'] = 1; 
        // $this->db->where('tasktype', 0);
        // $this->db->update(db_prefix() . 'tasks', $updateactiv);
        $data = [];
        // FOr new task add directly from the projects milestones
        if ($this->input->get('milestone_id')) {
            $this->db->where('id', $this->input->get('milestone_id'));
            $milestone = $this->db->get(db_prefix() . 'milestones')->row();
            if ($milestone) {
                $data['_milestone_selected_data'] = [
                    'id'       => $milestone->id,
                    'due_date' => _d($milestone->due_date),
                ];
            }
        }
		
		if(isset($_GET['rel_type']) && !empty($_GET['rel_type']) && $_GET['rel_type'] == 'project_task'){
			$_GET['rel_type'] = 'project';
			if(!empty($_GET['rel_id'])){
				$project_task = $this->tasks_model->get($_GET['rel_id']);
				$_GET['rel_id'] = $project_task->rel_id;
			}
			
		}
		
		
        if ($this->input->get('start_date')) {
            $data['start_date'] = $this->input->get('start_date');
            $data['duedate'] = $this->input->get('start_date');
        }
        if ($this->input->post()) {
            $data                = $this->input->post();
            $data['description'] = $this->input->post('description', false);
			
			if(isset($data['task_mark_complete_id']) && !empty($data['task_mark_complete_id'])){
				$this->tasks_model->mark_as(5, $data['task_mark_complete_id']);
			}
			if(isset($data['task_mark_complete_id'])){
				unset($data['task_mark_complete_id']);
			}
            if ($id == '') {
                if (!has_permission('tasks', '', 'create')) {
                    header('HTTP/1.0 400 Bad error');
                    echo json_encode([
                        'success' => false,
                        'message' => _l('access_denied'),
                    ]);
                    die;
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
                }
                echo json_encode([
                    'success' => $success,
                    'id'      => $_id,
                    'message' => $message,
                ]);
            } else {
                if (!has_permission('tasks', '', 'edit')) {
                    header('HTTP/1.0 400 Bad error');
                    echo json_encode([
                        'success' => false,
                        'message' => _l('access_denied'),
                    ]);
                    die;
                }
                $data_assignee = $data['assignees'];
                unset($data['assignees']);

                $success = $this->tasks_model->update($data, $id);
                $data['taskid'] =  $id;
                $task_assignees_already     = $this->tasks_model->get_task_assignees($id);
                $task_assignees_ids = [];
                foreach ($task_assignees_already as $aa) {
                    array_push($task_assignees_ids, $aa['assigneeid']);
                }
    
                foreach($data_assignee as $taskey => $tasvalue ){
                    if(!in_array($tasvalue,$task_assignees_ids)){
                        $data['assignee'] = $tasvalue;
                        $this->tasks_model->add_task_assignees($data);
                    }
                }

                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('task'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                    'id'      => $id,
                ]);
            }
            die;
        }

        $data['milestones']         = [];
        $data['checklistTemplates'] = $this->tasks_model->get_checklist_templates();
		$data['tasktypes'] = $this->tasktype_model->getTasktypes();
        if ($id == '') {
            $title = _l('add_new', _l('task_lowercase'));
        } else {
            $data['task'] = $this->tasks_model->get($id);
            if($data['task']) {
                $this->db->where('touserid', get_staff_user_id());
                $this->db->where('link', '#taskid='.$id);
                $this->db->update(db_prefix() . 'notifications', [
                    'isread' => 1,
                    'isread_inline' => 1,
                ]);
           }
            if ($data['task']->rel_type == 'project') {
                $data['milestones'] = $this->projects_model->get_milestones($data['task']->rel_id);
            }
            $title = _l('edit', _l('task_lowercase')) . ' ' . $data['task']->name;
        }
		
		
        $data['project_end_date_attrs'] = [];
        
        $data['staff']              = $this->staff_model->get('', ['active' => 1]);
        
        if ($this->input->get('rel_type') == 'project' && $this->input->get('rel_id') || ($id !== '' && $data['task']->rel_type == 'project')) {
           
            $project = $this->projects_model->get($id === '' ? $this->input->get('rel_id') : $data['task']->rel_id);

             $data['project_details_company'] = '<select  disabled="disabled" class="selectpicker" data-width="100%">
            <option selected>'.$project->client_data->company.'</option>
            </select>';

            $project_contacts = $this->projects_model->get_project_contacts($project->id);
            //echo "<pre>"; print_r($project_contacts); exit;
            $project_contacts_text = '<select disabled="disabled" class="selectpicker" name="contacts_id" data-width="100%">';
            foreach($project_contacts as $contact){
                // $project_contacts_text .= '<br /><a href="'.admin_url('profile/'.$contact["contacts_id"]).'"> '.get_contact_full_name($contact['contacts_id']).'</a>';
                $project_contacts_text .= '<option '.((isset($data['task']->contacts_id) && $data['task']->contacts_id == $contact['contacts_id'])?'selected':'').'  value="'.$contact['contacts_id'].'">'.get_contact_full_name($contact['contacts_id']).'</option>';
            }
            $project_contacts_text .= ' </select>';
            $data['project_contacts_text'] = $project_contacts_text;
            if ($project->deadline) {
                $data['project_end_date_attrs'] = [
                    'data-date-end-date' => $project->deadline,
                ];
            }
            $data['project'] = $project;
			//$data['staff']              = $this->staff_model->get('', ['active' => 1,'pipeline_id'=>$project->pipeline_id]);
        }
        //echo "<pre>"; print_r($data); exit;
        $data['id']    = $id;
        $data['title'] = $title;
		$fields = get_option('deal_fields');
		$data['need_fields'] = array();
		if(!empty($fields) && $fields != 'null'){
			$data['need_fields'] = json_decode($fields);
		}
        $this->load->view('admin/tasks/task', $data);
    }
	
	
	
	public function edit_task($id = '')
    {
        if (!has_permission('tasks', '', 'edit') && !has_permission('tasks', '', 'create')) {
            ajax_access_denied();
        }
        // $updateactiv['tasktype'] = 1; 
        // $this->db->where('tasktype', 0);
        // $this->db->update(db_prefix() . 'tasks', $updateactiv);
        $data = [];
        // FOr new task add directly from the projects milestones
        if ($this->input->get('milestone_id')) {
            $this->db->where('id', $this->input->get('milestone_id'));
            $milestone = $this->db->get(db_prefix() . 'milestones')->row();
            if ($milestone) {
                $data['_milestone_selected_data'] = [
                    'id'       => $milestone->id,
                    'due_date' => _d($milestone->due_date),
                ];
            }
        }
		
		if(isset($_GET['rel_type']) && !empty($_GET['rel_type']) && $_GET['rel_type'] == 'project_task'){
			$_GET['rel_type'] = 'project';
			if(!empty($_GET['rel_id'])){
				$project_task = $this->tasks_model->get($_GET['rel_id']);
				$_GET['rel_id'] = $project_task->rel_id;
			}
			
		}
		
		
        if ($this->input->get('start_date')) {
            $data['start_date'] = $this->input->get('start_date');
            $data['duedate'] = $this->input->get('start_date');
        }
        if ($this->input->post()) {
            $data                = $this->input->post();
            $data['description'] = $this->input->post('description', false);
			
			if(isset($data['task_mark_complete_id']) && !empty($data['task_mark_complete_id'])){
				$this->tasks_model->mark_as(5, $data['task_mark_complete_id']);
			}
			if(isset($data['task_mark_complete_id'])){
				unset($data['task_mark_complete_id']);
			}
            if ($id == '') {
                if (!has_permission('tasks', '', 'create')) {
                    header('HTTP/1.0 400 Bad error');
                    echo json_encode([
                        'success' => false,
                        'message' => _l('access_denied'),
                    ]);
                    die;
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
                }
                echo json_encode([
                    'success' => $success,
                    'id'      => $_id,
                    'message' => $message,
                ]);
            } else {
                if (!has_permission('tasks', '', 'edit')) {
                    header('HTTP/1.0 400 Bad error');
                    echo json_encode([
                        'success' => false,
                        'message' => _l('access_denied'),
                    ]);
                    die;
                }
                $data_assignee = $data['assignees'];
                unset($data['assignees']);

                $success = $this->tasks_model->update($data, $id);
                $data['taskid'] =  $id;
                $task_assignees_already     = $this->tasks_model->get_task_assignees($id);
                $task_assignees_ids = [];
                foreach ($task_assignees_already as $aa) {
                    array_push($task_assignees_ids, $aa['assigneeid']);
                }
    
                foreach($data_assignee as $taskey => $tasvalue ){
                    if(!in_array($tasvalue,$task_assignees_ids)){
                        $data['assignee'] = $tasvalue;
                        $this->tasks_model->add_task_assignees($data);
                    }
                }

                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('task'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                    'id'      => $id,
                ]);
            }
            die;
        }

        $data['milestones']         = [];
        $data['checklistTemplates'] = $this->tasks_model->get_checklist_templates();
		$data['tasktypes'] = $this->tasktype_model->getTasktypes();
        if ($id == '') {
            $title = _l('add_new', _l('task_lowercase'));
        } else {
            $data['task'] = $this->tasks_model->get($id);
            if($data['task']) {
                $this->db->where('touserid', get_staff_user_id());
                $this->db->where('link', '#taskid='.$id);
                $this->db->update(db_prefix() . 'notifications', [
                    'isread' => 1,
                    'isread_inline' => 1,
                ]);
           }
            if ($data['task']->rel_type == 'project') {
                $data['milestones'] = $this->projects_model->get_milestones($data['task']->rel_id);
            }
            $title = _l('edit', _l('task_lowercase')) . ' ' . $data['task']->name;
        }
		
		
        $data['project_end_date_attrs'] = [];
        
        $data['staff']              = $this->staff_model->get('', ['active' => 1]);
        
        if ($this->input->get('rel_type') == 'project' && $this->input->get('rel_id') || ($id !== '' && $data['task']->rel_type == 'project')) {
           
            $project = $this->projects_model->get($id === '' ? $this->input->get('rel_id') : $data['task']->rel_id);

             $data['project_details_company'] = '<select  disabled="disabled" class="selectpicker" data-width="100%">
            <option selected>'.$project->client_data->company.'</option>
            </select>';

            $project_contacts = $this->projects_model->get_project_contacts($project->id);
            //echo "<pre>"; print_r($project_contacts); exit;
            $project_contacts_text = '<select disabled="disabled" class="selectpicker" name="contacts_id" data-width="100%">';
            foreach($project_contacts as $contact){
                // $project_contacts_text .= '<br /><a href="'.admin_url('profile/'.$contact["contacts_id"]).'"> '.get_contact_full_name($contact['contacts_id']).'</a>';
                $project_contacts_text .= '<option '.((isset($data['task']->contacts_id) && $data['task']->contacts_id == $contact['contacts_id'])?'selected':'').'  value="'.$contact['contacts_id'].'">'.get_contact_full_name($contact['contacts_id']).'</option>';
            }
            $project_contacts_text .= ' </select>';
            $data['project_contacts_text'] = $project_contacts_text;
            if ($project->deadline) {
                $data['project_end_date_attrs'] = [
                    'data-date-end-date' => $project->deadline,
                ];
            }
            $data['project'] = $project;
			//$data['staff']              = $this->staff_model->get('', ['active' => 1,'pipeline_id'=>$project->pipeline_id]);
        }
        //echo "<pre>"; print_r($data); exit;
		$title = _l('add_new', _l('task_lowercase'));
        $data['id']    = $id;
        $data['title'] = $title;
        $this->load->view('admin/tasks/edit_task', $data);
    }

    public function copy()
    {
        if (has_permission('tasks', '', 'create')) {
            $new_task_id = $this->tasks_model->copy($this->input->post());
            $response    = [
                'new_task_id' => '',
                'alert_type'  => 'warning',
                'message'     => _l('failed_to_copy_task'),
                'success'     => false,
            ];
            if ($new_task_id) {
                $response['message']     = _l('task_copied_successfully');
                $response['new_task_id'] = $new_task_id;
                $response['success']     = true;
                $response['alert_type']  = 'success';
            }
            echo json_encode($response);
        }
    }

    public function get_billable_task_data($task_id)
    {
        $task              = $this->tasks_model->get_billable_task_data($task_id);
        $task->description = seconds_to_time_format($task->total_seconds) . ' ' . _l('hours');
        echo json_encode($task);
    }

    /**
     * Task ajax request modal
     * @param  mixed $taskid
     * @return mixed
     */
    public function get_task_data($taskid, $return = false)
    {
        $tasks_where = [];

        if (!has_permission('tasks', '', 'view')) {
            $tasks_where = get_tasks_where_string(false);
        }

        $task = $this->tasks_model->get($taskid, $tasks_where);

        if (!$task) {
            header('HTTP/1.0 404 Not Found');
            echo 'Task not found';
            die();
        }

        $data['checklistTemplates'] = $this->tasks_model->get_checklist_templates();
        $data['task']               = $task;
        $data['id']                 = $task->id;
        $data['staff']              = $this->staff_model->get('', ['active' => 1]);
        $data['reminders']          = $this->tasks_model->get_reminders($taskid);

        $data['staff_reminders'] = $this->tasks_model->get_staff_members_that_can_access_task($taskid);

        $data['project_deadline'] = null;
        if ($task->rel_type == 'project') {
            $data['project_deadline'] = get_project_deadline($task->rel_id);
            $project = $this->projects_model->get($task->rel_id);
            $data['project_details_company'] = $project->client_data->company;
            $project_contacts = $this->projects_model->get_project_contacts($project->id);
            $project_contacts_text = '';
            foreach($project_contacts as $contact){
                if(isset($task->contacts_id) && $task->contacts_id == $contact["contacts_id"]){
                    $project_contacts_text .= '<br /><a href="'.admin_url('clients/view_contact/'.$contact["contacts_id"]).'"> '.get_contact_full_name($contact['contacts_id']).'</a>';
                }
            }
            $data['project_contacts_text'] = $project_contacts_text;
        }

        if ($return == false) {
            $this->load->view('admin/tasks/view_task_template', $data);
        } else {
            return $this->load->view('admin/tasks/view_task_template', $data, true);
        }
    }

    public function add_reminder($task_id)
    {
        $message    = '';
        $alert_type = 'warning';
        if ($this->input->post()) {
            $success = $this->misc_model->add_reminder($this->input->post(), $task_id);
            if ($success) {
                $alert_type = 'success';
                $message    = _l('reminder_added_successfully');
            }
        }
        echo json_encode([
            'taskHtml'   => $this->get_task_data($task_id, true),
            'alert_type' => $alert_type,
            'message'    => $message,
        ]);
    }

    public function edit_reminder($id)
    {
        $reminder = $this->misc_model->get_reminders($id);
        if ($reminder && ($reminder->creator == get_staff_user_id() || is_admin()) && $reminder->isnotified == 0) {
            $success = $this->misc_model->edit_reminder($this->input->post(), $id);
            echo json_encode([
                    'taskHtml'   => $this->get_task_data($reminder->rel_id, true),
                    'alert_type' => 'success',
                    'message'    => ($success ? _l('updated_successfully', _l('reminder')) : ''),
                ]);
        }
    }

    public function delete_reminder($rel_id, $id)
    {
        $success    = $this->misc_model->delete_reminder($id);
        $alert_type = 'warning';
        $message    = _l('reminder_failed_to_delete');
        if ($success) {
            $alert_type = 'success';
            $message    = _l('reminder_deleted');
        }
        echo json_encode([
            'taskHtml'   => $this->get_task_data($rel_id, true),
            'alert_type' => $alert_type,
            'message'    => $message,
        ]);
    }

    public function get_staff_started_timers($return = false)
    {
        $data['startedTimers'] = $this->misc_model->get_staff_started_timers();
        $_data['html']         = $this->load->view('admin/tasks/started_timers', $data, true);
        $_data['total_timers'] = count($data['startedTimers']);

        $timers = json_encode($_data);
        if ($return) {
            return $timers;
        }

        echo $timers;
    }

    public function save_checklist_item_template()
    {
        if (has_permission('checklist_templates', '', 'create')) {
            $id = $this->tasks_model->add_checklist_template($this->input->post('description'));
            echo json_encode(['id' => $id]);
        }
    }

    public function remove_checklist_item_template($id)
    {
        if (has_permission('checklist_templates', '', 'delete')) {
            $success = $this->tasks_model->remove_checklist_item_template($id);
            echo json_encode(['success' => $success]);
        }
    }

    public function init_checklist_items()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $post_data          = $this->input->post();
                $data['task_id']    = $post_data['taskid'];
                $data['checklists'] = $this->tasks_model->get_checklist_items($post_data['taskid']);
                $this->load->view('admin/tasks/checklist_items_template', $data);
            }
        }
    }

    public function task_tracking_stats($task_id)
    {
        $data['stats'] = json_encode($this->tasks_model->task_tracking_stats($task_id));
        $this->load->view('admin/tasks/tracking_stats', $data);
    }

    public function checkbox_action($listid, $value)
    {
        $this->db->where('id', $listid);
        $this->db->update(db_prefix() . 'task_checklist_items', [
            'finished' => $value,
        ]);

        if ($this->db->affected_rows() > 0) {
            if ($value == 1) {
                $this->db->where('id', $listid);
                $this->db->update(db_prefix() . 'task_checklist_items', [
                    'finished_from' => get_staff_user_id(),
                ]);
                hooks()->do_action('task_checklist_item_finished', $listid);
            }
        }
    }

    public function add_checklist_item()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                echo json_encode([
                    'success' => $this->tasks_model->add_checklist_item($this->input->post()),
                ]);
            }
        }
    }

    public function update_checklist_order()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $this->tasks_model->update_checklist_order($this->input->post());
            }
        }
    }

    public function delete_checklist_item($id)
    {
        $list = $this->tasks_model->get_checklist_item($id);
        if (has_permission('tasks', '', 'delete') || $list->addedfrom == get_staff_user_id()) {
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'success' => $this->tasks_model->delete_checklist_item($id),
                ]);
            }
        }
    }

    public function update_checklist_item()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $desc = $this->input->post('description');
                $desc = trim($desc);
                $this->tasks_model->update_checklist_item($this->input->post('listid'), $desc);
                echo json_encode(['can_be_template' => (total_rows(db_prefix() . 'tasks_checklist_templates', ['description' => $desc]) == 0)]);
            }
        }
    }

    public function make_public($task_id)
    {
        if (!has_permission('tasks', '', 'edit')) {
            json_encode([
                'success' => false,
            ]);
            die;
        }
        echo json_encode([
            'success'  => $this->tasks_model->make_public($task_id),
            'taskHtml' => $this->get_task_data($task_id, true),
        ]);
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->tasks_model->add_attachment_to_database($this->input->post('task_id'), $this->input->post('files'), $this->input->post('external'));
        }
    }

    /* Add new task comment / ajax */
    public function add_task_comment()
    {
        $data            = $this->input->post();
        $data['content'] = $this->input->post('content', false);
        if ($this->input->post('no_editor')) {
            $data['content'] = nl2br($this->input->post('content'));
        }
        $comment_id = false;
        if ($data['content'] != ''
            || (isset($_FILES['file']['name']) && is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)) {
            $comment_id = $this->tasks_model->add_task_comment($data);
            if ($comment_id) {
                $commentAttachments = handle_task_attachments_array($data['taskid'], 'file');
                if ($commentAttachments && is_array($commentAttachments)) {
                    foreach ($commentAttachments as $file) {
                        $file['task_comment_id'] = $comment_id;
                        $this->misc_model->add_attachment_to_database($data['taskid'], 'task', [$file]);
                    }

                    if (count($commentAttachments) > 0) {
                        $this->db->query('UPDATE ' . db_prefix() . "task_comments SET content = CONCAT(content, '[task_attachment]')
                            WHERE id = " . $comment_id);
                    }
                }
            }
        }
        echo json_encode([
            'success'  => $comment_id ? true : false,
            'taskHtml' => $this->get_task_data($data['taskid'], true),
        ]);
    }

    public function download_files($task_id, $comment_id = null)
    {
        $taskWhere = 'external IS NULL';

        if ($comment_id) {
            $taskWhere .= ' AND task_comment_id=' . $comment_id;
        }

        if (!has_permission('tasks', '', 'view')) {
            $taskWhere .= ' AND ' . get_tasks_where_string(false);
        }

        $files = $this->tasks_model->get_task_attachments($task_id, $taskWhere);

        if (count($files) == 0) {
            redirect($_SERVER['HTTP_REFERER']);
        }

        $path = get_upload_path_by_type('task') . $task_id;

        $this->load->library('zip');

        foreach ($files as $file) {
            $this->zip->read_file($path . '/' . $file['file_name']);
        }

        $this->zip->download('files.zip');
        $this->zip->clear_data();
    }

    /* Add new task follower / ajax */
    public function add_task_followers()
    {
        if (has_permission('tasks', '', 'edit') || has_permission('tasks', '', 'create')) {
            echo json_encode([
                'success'  => $this->tasks_model->add_task_followers($this->input->post()),
                'taskHtml' => $this->get_task_data($this->input->post('taskid'), true),
            ]);
        }
    }

    /* Add task assignees / ajax */
    public function add_task_assignees()
    {
        if (has_permission('tasks', '', 'edit') || has_permission('tasks', '', 'create')) {
            echo json_encode([
                'success'  => $this->tasks_model->add_task_assignees($this->input->post()),
                'taskHtml' => $this->get_task_data($this->input->post('taskid'), true),
            ]);
        }
    }

    public function edit_comment()
    {
        if ($this->input->post()) {
            $data            = $this->input->post();
            $data['content'] = $this->input->post('content', false);
            if ($this->input->post('no_editor')) {
                $data['content'] = nl2br(clear_textarea_breaks($this->input->post('content')));
            }
            $success = $this->tasks_model->edit_comment($data);
            $message = '';
            if ($success) {
                $message = _l('task_comment_updated');
            }
            echo json_encode([
                'success'  => $success,
                'message'  => $message,
                'taskHtml' => $this->get_task_data($data['task_id'], true),
            ]);
        }
    }

    /* Remove task comment / ajax */
    public function remove_comment($id)
    {
        echo json_encode([
            'success' => $this->tasks_model->remove_comment($id),
        ]);
    }

    /* Remove assignee / ajax */
    public function remove_assignee($id, $taskid)
    {
        if (has_permission('tasks', '', 'edit') && has_permission('tasks', '', 'create')) {
            $success = $this->tasks_model->remove_assignee($id, $taskid);
            $message = '';
            if ($success) {
                $message = _l('task_assignee_removed');
            }
            echo json_encode([
                'success'  => $success,
                'message'  => $message,
                'taskHtml' => $this->get_task_data($taskid, true),
            ]);
        }
    }

    /* Remove task follower / ajax */
    public function remove_follower($id, $taskid)
    {
        if (has_permission('tasks', '', 'edit') && has_permission('tasks', '', 'create')) {
            $success = $this->tasks_model->remove_follower($id, $taskid);
            $message = '';
            if ($success) {
                $message = _l('task_follower_removed');
            }
            echo json_encode([
                'success'  => $success,
                'message'  => $message,
                'taskHtml' => $this->get_task_data($taskid, true),
            ]);
        }
    }

    /* Unmark task as complete / ajax*/
    public function unmark_complete($id)
    {
        if ($this->tasks_model->is_task_assignee(get_staff_user_id(), $id)
            || $this->tasks_model->is_task_creator(get_staff_user_id(), $id)
            || has_permission('tasks', '', 'edit')) {
            $success = $this->tasks_model->unmark_complete($id);

            // Don't do this query if the action is not performed via task single
            $taskHtml = $this->input->get('single_task') === 'true' ? $this->get_task_data($id, true) : '';

            $message = '';
            if ($success) {
                $message = _l('task_unmarked_as_complete');
            }
            echo json_encode([
                'success'  => $success,
                'message'  => $message,
                'taskHtml' => $taskHtml,
            ]);
        } else {
            echo json_encode([
                'success'  => false,
                'message'  => '',
                'taskHtml' => '',
            ]);
        }
    }

    public function mark_as($status, $id)
    {
        if ($this->tasks_model->is_task_assignee(get_staff_user_id(), $id)
            || $this->tasks_model->is_task_creator(get_staff_user_id(), $id)
            || has_permission('tasks', '', 'edit')) {
            $success = $this->tasks_model->mark_as($status, $id);

            // Don't do this query if the action is not performed via task single
            $taskHtml = $this->input->get('single_task') === 'true' ? $this->get_task_data($id, true) : '';

            $message = '';

            if ($success) {
                $message = _l('task_marked_as_success', format_task_status($status, true, true));
            }

            echo json_encode([
                'success'  => $success,
                'message'  => $message,
                'taskHtml' => $taskHtml,
            ]);
        } else {
            echo json_encode([
                'success'  => false,
                'message'  => '',
                'taskHtml' => '',
            ]);
        }
    }

    public function change_priority($priority_id, $id)
    {
        if (has_permission('tasks', '', 'edit')) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'tasks', ['priority' => $priority_id]);

            $success = $this->db->affected_rows() > 0 ? true : false;

            // Don't do this query if the action is not performed via task single
            $taskHtml = $this->input->get('single_task') === 'true' ? $this->get_task_data($id, true) : '';
            echo json_encode([
                'success'  => $success,
                'taskHtml' => $taskHtml,
            ]);
        } else {
            echo json_encode([
                'success'  => false,
                'taskHtml' => $taskHtml,
            ]);
        }
    }

    public function change_milestone($milestone_id, $id)
    {
        if (has_permission('tasks', '', 'edit')) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'tasks', ['milestone' => $milestone_id]);

            $success = $this->db->affected_rows() > 0 ? true : false;
            // Don't do this query if the action is not performed via task single
            $taskHtml = $this->input->get('single_task') === 'true' ? $this->get_task_data($id, true) : '';
            echo json_encode([
                'success'  => $success,
                'taskHtml' => $taskHtml,
            ]);
        } else {
            echo json_encode([
                'success'  => false,
                'taskHtml' => $taskHtml,
            ]);
        }
    }

    public function task_single_inline_update($task_id)
    {
        if (has_permission('tasks', '', 'edit')) {
            $post_data = $this->input->post();
            foreach ($post_data as $key => $val) {
                $this->db->where('id', $task_id);
                $this->db->update(db_prefix() . 'tasks', [$key => to_sql_date($val)]);
            }
        }
    }

    /* Delete task from database */
    public function delete_task($id)
    {
        if (!has_permission('tasks', '', 'delete')) {
            access_denied('tasks');
        }
        $success = $this->tasks_model->delete_task($id);
        $message = _l('problem_deleting', _l('task_lowercase'));
        if ($success) {
            $message = _l('deleted', _l('task'));
            set_alert('success', $message);
        } else {
            set_alert('warning', $message);
        }

        if (strpos($_SERVER['HTTP_REFERER'], 'tasks/index') !== false || strpos($_SERVER['HTTP_REFERER'], 'tasks/view') !== false) {
            redirect(admin_url('tasks'));
        } elseif (preg_match("/projects\/view\/[1-9]+/", $_SERVER['HTTP_REFERER'])) {
            $project_url = explode('?', $_SERVER['HTTP_REFERER']);
            redirect($project_url[0] . '?group=project_tasks');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * Remove task attachment
     * @since  Version 1.0.1
     * @param  mixed $id attachment it
     * @return json
     */
    public function remove_task_attachment($id)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->tasks_model->remove_task_attachment($id));
        }
    }

    /**
     * Upload task attachment
     * @since  Version 1.0.1
     */
    public function upload_file()
    {
        if ($this->input->post()) {
            $taskid  = $this->input->post('taskid');
            $files   = handle_task_attachments_array($taskid, 'file');
            $success = false;

            if ($files) {
                $i   = 0;
                $len = count($files);
                foreach ($files as $file) {
                    $success = $this->tasks_model->add_attachment_to_database($taskid, [$file], false, ($i == $len - 1 ? true : false));
                    $i++;
                }
            }

            echo json_encode([
                'success'  => $success,
                'taskHtml' => $this->get_task_data($taskid, true),
            ]);
        }
    }

    public function timer_tracking()
    {
        $task_id   = $this->input->post('task_id');
        $adminStop = $this->input->get('admin_stop') && is_admin() ? true : false;

        if ($adminStop) {
            $this->session->set_flashdata('task_single_timesheets_open', true);
        }

        echo json_encode([
            'success' => $this->tasks_model->timer_tracking(
                $task_id,
                $this->input->post('timer_id'),
                nl2br($this->input->post('note')),
                $adminStop
            ),
            'taskHtml' => $this->input->get('single_task') === 'true' ? $this->get_task_data($task_id, true) : '',
            'timers'   => $this->get_staff_started_timers(true),
        ]);
    }

    public function delete_user_unfinished_timesheet($id)
    {
        $this->db->where('id', $id);
        $timesheet = $this->db->get(db_prefix() . 'taskstimers')->row();
        if ($timesheet && $timesheet->end_time == null && $timesheet->staff_id == get_staff_user_id()) {
            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'taskstimers');
        }
        echo json_encode(['timers' => $this->get_staff_started_timers(true)]);
    }

    public function delete_timesheet($id)
    {
        if (has_permission('tasks', '', 'delete') || has_permission('projects', '', 'delete') || total_rows(db_prefix() . 'taskstimers', ['staff_id' => get_staff_user_id(), 'id' => $id]) > 0) {
            $alert_type = 'warning';
            $success    = $this->tasks_model->delete_timesheet($id);
            if ($success) {
                $this->session->set_flashdata('task_single_timesheets_open', true);
                $message = _l('deleted', _l('project_timesheet'));
                set_alert('success', $message);
            }
            if (!$this->input->is_ajax_request()) {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function log_time()
    {
        $success = $this->tasks_model->timesheet($this->input->post());
        if ($success === true) {
            $this->session->set_flashdata('task_single_timesheets_open', true);
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

    public function update_tags()
    {
        if (has_permission('tasks', '', 'create') || has_permission('tasks', '', 'edit')) {
            handle_tags_save($this->input->post('tags'), $this->input->post('task_id'), 'task');
        }
    }
	
    public function bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_tasks');
        $total_deleted = 0;
        if ($this->input->post()) {
            $status    = $this->input->post('status');
            $ids       = $this->input->post('ids');
            $tags      = $this->input->post('tags');
            $assignees = $this->input->post('assignees');
            $milestone = $this->input->post('milestone');
            $priority  = $this->input->post('priority');
            $is_admin  = is_admin();
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if (has_permission('tasks', '', 'delete')) {
                            if ($this->tasks_model->delete_task($id)) {
                                $total_deleted++;
                            }
                        }
                    } else {
                        if ($status) {
                            if ($this->tasks_model->is_task_creator(get_staff_user_id(), $id)
                                || $is_admin
                                || $this->tasks_model->is_task_assignee(get_staff_user_id(), $id)) {
                                $this->tasks_model->mark_as($status, $id);
                            }
                        }
                        if ($priority || $milestone) {
                            $update = [];
                            if ($priority) {
                                $update['priority'] = $priority;
                            }
                            if ($milestone) {
                                $update['milestone'] = $milestone;
                            }
                            $this->db->where('id', $id);
                            $this->db->update(db_prefix() . 'tasks', $update);
                        }
                        if ($tags) {
                            handle_tags_save($tags, $id, 'task');
                        }
                        if ($assignees) {
                            $notifiedUsers = [];
                            $user_id = $assignees;
                            if (!$this->tasks_model->is_task_assignee($user_id, $id)) {
                                $this->db->select('rel_type,rel_id');
                                $this->db->where('id', $id);
                                $task = $this->db->get(db_prefix() . 'tasks')->row();
                                if ($task->rel_type == 'project') {
                                    // User is we are trying to assign the task is not project member
                                    if (total_rows(db_prefix() . 'project_members', ['project_id' => $task->rel_id, 'staff_id' => $user_id]) == 0) {
                                        $this->db->insert(db_prefix() . 'project_members', ['project_id' => $task->rel_id, 'staff_id' => $user_id]);
                                    }
                                }
                                
                                $this->db->where('taskid', $id);
                                $this->db->delete(db_prefix() . 'task_assigned');

                                $this->db->insert(db_prefix() . 'task_assigned', [
                                    'staffid'       => $user_id,
                                    'taskid'        => $id,
                                    'assigned_from' => get_staff_user_id(),
                                    ]);
                                if ($user_id != get_staff_user_id()) {
                                    $notification_data = [
                                    'description' => 'not_task_assigned_to_you',
                                    'touserid'    => $user_id,
                                    'link'        => '#taskid=' . $id,
                                    ];

                                    $notification_data['additional_data'] = serialize([
                                        get_task_subject_by_id($id),
                                    ]);
                                    if (add_notification($notification_data)) {
                                        array_push($notifiedUsers, $user_id);
                                    }
                                }
                            }
                            
                            // foreach ($assignees as $user_id) {
                            //     if (!$this->tasks_model->is_task_assignee($user_id, $id)) {
                            //         $this->db->select('rel_type,rel_id');
                            //         $this->db->where('id', $id);
                            //         $task = $this->db->get(db_prefix() . 'tasks')->row();
                            //         if ($task->rel_type == 'project') {
                            //             // User is we are trying to assign the task is not project member
                            //             if (total_rows(db_prefix() . 'project_members', ['project_id' => $task->rel_id, 'staff_id' => $user_id]) == 0) {
                            //                 $this->db->insert(db_prefix() . 'project_members', ['project_id' => $task->rel_id, 'staff_id' => $user_id]);
                            //             }
                            //         }
                            //         $this->db->insert(db_prefix() . 'task_assigned', [
                            //             'staffid'       => $user_id,
                            //             'taskid'        => $id,
                            //             'assigned_from' => get_staff_user_id(),
                            //             ]);
                            //         if ($user_id != get_staff_user_id()) {
                            //             $notification_data = [
                            //             'description' => 'not_task_assigned_to_you',
                            //             'touserid'    => $user_id,
                            //             'link'        => '#taskid=' . $id,
                            //             ];

                            //             $notification_data['additional_data'] = serialize([
                            //                 get_task_subject_by_id($id),
                            //             ]);
                            //             if (add_notification($notification_data)) {
                            //                 array_push($notifiedUsers, $user_id);
                            //             }
                            //         }
                            //     }
                            // }
                            pusher_trigger_notification($notifiedUsers);
                        }
                    }
                }
            }
            if ($this->input->post('mass_delete')) {
                set_alert('success', _l('total_tasks_deleted', $total_deleted));
            }
        }
    }
}