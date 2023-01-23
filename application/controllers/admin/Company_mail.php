<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Company_mail extends AdminController
{
    public function __construct()
    {
        parent::__construct();
		$this->load->model('projects_model');
		$this->load->model('leads_model');
        $this->load->model('tasktype_model');
		$this->load->library('session');
		unset($_SESSION['pipelines']);
        unset($_SESSION['member']);
        unset($_SESSION['gsearch']);
    }
	public function changedeals () {
        if($_POST['uid']) {
            $this->db->where('source_from', $_POST['uid']);
			if(!empty($_POST['rel_id'])){
				$this->db->update(db_prefix() . 'tasks', ['rel_id' => $_POST['rel_id']]);
			}
			else{
				$this->db->set('rel_id', null);
				$this->db->where('source_from', $_POST['uid']);
				$this->db->update(db_prefix() . 'tasks');
			}
            set_alert('success', 'Deal updated successfully.');
            redirect(admin_url('company_mail/check_company_mail'));
        }
    }
	public function get_or_update_setting($staffid,$table){
		$cond = array('user_id'=>$staffid);
		$mail_setting = $this->db->where($cond)->get($table)->result_array();
		if(isset($_POST['submit_save'])){
			$post_data = $_POST;
			unset($post_data['submit_save']);
			if($table == db_prefix() . 'personal_mail_setting'){
				$post_data['smtp_server'] = $post_data['imap_server'];
				$post_data['smtp_username'] = $post_data['imap_username'];
				$post_data['smtp_password'] = $post_data['imap_password'];
				$post_data['smtp_email'] = $post_data['smtp_username'];
				$post_data['imap_email'] = $post_data['imap_username'];
				//$post_data['smtp_password'] = $this->encryption->encrypt($post_data['smtp_password']);
				$ch_admin = is_admin($staffid);
				
					$this->load->model('payment_modes_model');
					$this->load->model('settings_model');
					/*$post_data1['settings']['smtp_encryption'] = $post_data['smtp_encryption'];
					$post_data1['settings']['smtp_host'] = $post_data['smtp_host'];
					$post_data1['settings']['smtp_port'] = $post_data['smtp_port'];
					$post_data1['settings']['smtp_email'] = $post_data['smtp_email'];
					$post_data1['settings']['smtp_username'] = $post_data['smtp_username'];
					$post_data1['settings']['smtp_password'] = $post_data['smtp_password'];*/
				if($ch_admin){
					//$this->settings_model->update($post_data1);
				}
			}
			if(!empty($mail_setting)){
				$this->db->update($table, $post_data, $cond);
			}
			else{
				$post_data['user_id'] = $staffid;
				$this->db->insert($table,$post_data);
			}
			set_alert('success', _l('settings_updated'));
			redirect($this->uri->uri_string());
			exit;
		}
		if(!empty($mail_setting[0]))
			return $mail_setting[0];
		else
			return array();
	}
	public function email_settings() {
		if(get_option('connect_mail')=='no'){
			redirect(site_url().'admin/company_mail/configure_email');
			exit;
		}
		if(get_option('company_mail_server')=='yes'){
			redirect(site_url().'admin/company_mail/company_mail_setting');
		}
		$staffid = get_staff_user_id();
		$table = db_prefix() . 'personal_mail_setting';
		$data['settings'] = $this->get_or_update_setting($staffid,$table);
		//echo '<pre>';print_r($data);exit;
		unset($_SESSION['debug']);
		$data['title'] ='Connect Email';
		$this->load->view('admin/settings/user/mail', $data);
    }
	public function configure_email(){
		if(get_option('connect_mail')=='yes'){
			if(get_option('company_mail_server')=='yes'){
				redirect(site_url().'admin/company_mail/company_mail_setting');
				exit;
			}
			if(get_option('company_mail_server')=='no'){
				redirect(site_url().'admin/company_mail/email_settings');
				exit;
			}
		}
		$data = array();
		$this->load->view('admin/settings/user/configure_mail', $data);
	}
	public function imap_data($imapconf,$pag_no = '',$search_txt=''){
		$staffid = get_staff_user_id();
		$this->load->library('imap');
		//$this->imap->connect($imapconf);
		//$data['counts']       = $this->imap->unread_message();
		$data['counts']        = '';
		$data['folders']       = $this->imap->get_company_folders_inbox($imapconf,$pag_no,$data['counts'],$search_txt);
		$data['title']         = _l('acs_emailmanagemnet');
		return $data;
	}
	function download_attachment($uid){
		$folder = $_REQUEST['folder'];
		$imapconf = get_imap_setting();
		$this->load->library('imap');
		$this->imap->download_attachment($imapconf,$uid,$folder);
	}
	function download_attachment_single($uid){
		$folder = $_REQUEST['folder'];
		$attach_id = $_REQUEST['attach_id'];
		$imapconf = get_imap_setting();
		$this->load->library('imap');
		$this->imap->download_attachment_single($imapconf,$uid,$folder,$attach_id);
	}
	
	public function getmessage() {
        $this->load->library('imap');
        $imapconf = get_imap_setting();
        
        $this->imap->connect($imapconf);

        $folders = $this->imap->company_getmessage();
        $this->imap->mark_as_read_company($_REQUEST['uid'],$imapconf);
        echo json_encode($folders);
        exit;
    }
	public function content() {
        $this->load->library('imap');
        $imapconf = get_imap_setting();
        $this->imap->connect($imapconf);
        $folders = $this->imap->company_content();
        echo json_encode($folders);
        exit;
    }
	public function delete_mail(){
		$uid = $_REQUEST['uid'];
		$this->load->library('imap');
        $imapconf = get_imap_setting();
		$this->imap->connect($imapconf);
		$this->imap->delete_mail($imapconf,$uid);
	}
	public function delete_mail_all(){
		$uid = $_REQUEST['mails'];
		$folder = $_REQUEST['folder'];
		$this->load->library('imap');
        $imapconf = get_imap_setting();
		$this->imap->connect($imapconf);
		$this->imap->delete_mail_all($imapconf,$folder,$uid);
		echo json_encode($uid);
	}
	public function save_draft(){
		$to = $_REQUEST['to'];
		$subject = $_REQUEST['subject'];
		$text = $_REQUEST['text'];
		$draft = $_REQUEST['draft'];
		
		$this->load->library('imap');
		
        $imapconf = get_imap_setting();
		$from = $imapconf['username'];
		if(!empty($draft)){
			$this->imap->delete_mail($imapconf,$draft);
		}
        $this->imap->connect($imapconf);
		echo $cur_uid = $this->imap->save_msg_draft($imapconf,$from,$to,$subject,$text);
		
	}
	public function to_mail() {
        $this->load->library('imap');
        $imapconf = get_imap_setting();;
        $staffid = get_staff_user_id();
		$this->db->where('staffid ', $staffid);
        $staff = $this->db->get(db_prefix() . 'staff')->row();
        $this->imap->connect($imapconf);

        $folders = $this->imap->company_content($staff->email,'');
		$email = $this->imap->get_message($_REQUEST['uid']);
		
		$this->db->where('message_id',$email['message_id']);
        $local_message =$this->db->get(db_prefix().'localmailstorage')->row();
		$rel_data = array(
			'rel_type'=>'',
			'rel_id'=>'',
			'parent_id'=>'',
		);
		if($local_message){
			if($local_message->deal_id){
				$rel_data = array(
					'rel_type'=>'project',
					'rel_id'=>$local_message->deal_id,
					'parent_id'=>$local_message->id,
				);
			}elseif($local_message->lead_id){
				$rel_data = array(
					'rel_type'=>'lead',
					'rel_id'=>$local_message->lead_id,
					'parent_id'=>$local_message->id,
				);
			}
		}
		
		$folders ['rel_data'] =$rel_data;
        echo json_encode($folders);
        exit;
    }
	public function add_reply_all() {
        $this->load->library('imap');
        $imapconf = get_imap_setting();;
        $staffid = get_staff_user_id();
		$this->db->where('staffid ', $staffid);
        $staff = $this->db->get(db_prefix() . 'staff')->row();
        $this->imap->connect($imapconf);
        $folders = $this->imap->company_content($staff->email,'all');
        echo json_encode($folders);
        exit;
    }
	public function pagination_mail($pag_no) {
		$search_txt = '';
		if(!empty($_REQUEST['search_txt'])){
			$search_txt = $_REQUEST['search_txt'];
		}
		$staffid = get_staff_user_id();
        $this->load->library('imap');
        $imapconf = array();
		$data = array();
		if(get_option('company_mail_server')=='no'){
			$table = db_prefix() . 'personal_mail_setting';
			
		}else{
			$table = db_prefix() . 'user_mail_setting';
		}

		
		$data['settings'] = $config = $this->get_or_update_setting($staffid,$table);
		
		if(!empty($data['settings'])){
			$imapconf = get_imap_setting();
			
			//Initialize the connection:
			if($this->imap->check_imap($imapconf)){
				$data = $this->imap_data($imapconf,$pag_no,$search_txt);
				$this->load->library('pagination');
				$row_per_page = 10;
				$allcount = $data['folders']['tot_cnt'];
				$config['base_url'] = base_url().'tasks/pagination_mail';
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
				$config['last_link'] = false;
				$config['num_links'] = 5;
		 
				$this->pagination->initialize($config);
		 
				$data['pagination'] = $this->pagination->create_links();
				$req_out = json_encode($data);
				$ch_err = json_last_error_msg();
				if( $ch_err !=''){
					$req_out = json_encode($data,JSON_INVALID_UTF8_IGNORE);
				}
				echo $req_out;
				//echo json_last_error_msg();
			}
		}
    }
	public function check_user_mail() {
		if(get_option('connect_mail')=='no'){
			redirect(admin_url('outlook_mail/connect_outlook'));
		}
		if(get_option('company_mail_server')=='yes'){
			redirect(site_url().'admin/company_mail/check_company_mail');
		}
		$this->load->library('imap');
		$imapconf = array();
		$data = array();
		$staffid = get_staff_user_id();
		$table = db_prefix() . 'personal_mail_setting';
		$data['settings'] = $config = $this->get_or_update_setting($staffid,$table);
		$data['title'] ='Email';
		if(!empty($config)){
			$imapconf = get_imap_setting();
			//Initialize the connection:
			//pre($imapconf);
			if($this->imap->check_imap($imapconf)){
				//$data = $this->imap_data($imapconf);
				$table = db_prefix() . 'template';
				$cond = array('user_id'=>$staffid);
				$templates = $this->db->where($cond)->get($table)->result_array();
				$data['templates'] = $templates;
				$data['default_val'] = '';
				if(!empty($templates[0]['description'])){
					$data['default_val'] = $templates[0]['description'];
				}
				$this->load->view('admin/staff/companyemailmanagement', $data);
			}
			else{
				set_debug_alert(_l('invalid_credential'));
				$this->load->view('admin/settings/user/mail', $data);
			}
		}
		else{
			$this->load->view('admin/settings/user/mail', $data);
		}
    }
	
	public function check_company_mail() {
		if(get_option('company_mail_server')=='no'){
			redirect(site_url().'admin/company_mail/check_user_mail');
		}
		$staffid = get_staff_user_id();
        $this->load->library('imap');
        $imapconf = array();
		$data = array();
		$table = db_prefix() . 'user_mail_setting';
		$data['settings'] = $config = $this->get_or_update_setting($staffid,$table);
		
		if(get_option('deal_map') != 'if more than one open deal – allow to map manually'){
			
			$data['project_name'] = get_deal_name($this->input->post('toemail'),get_option('deal_map'));
		}
		else{
			$data['project_name'] ='';
		}
		
		if(!empty($data['settings'])){
			$imapconf = get_imap_setting();
			$req_out = 1;
			if($imapconf['server']!='others'){
				$req_val = '@'.$imapconf['server'];
				if (strpos($imapconf['username'], $req_val) == false) {
					set_debug_alert(_l('invalid_credential'));
					$req_out = 0;
					$this->load->view('admin/settings/user/companymail', $data);					
				}
			}
			if($req_out == 1){
				//Initialize the connection:
				if($this->imap->check_imap($imapconf)){
					//$data = $this->imap_data($imapconf);
					$table = db_prefix() . 'template';
					$cond = array('user_id'=>$staffid);
					$templates = $this->db->where($cond)->get($table)->result_array();
					$data['templates'] = $templates;
					$data['default_val'] = '';
					if(!empty($templates[0]['description'])){
						$data['default_val'] = $templates[0]['description'];
					}
					$this->load->view('admin/staff/companyemailmanagement', $data);
				}
				else{
					set_debug_alert(_l('invalid_credential'));
					$this->load->view('admin/settings/user/companymail', $data);
				}
			}
		}
		else{
			$this->load->view('admin/settings/user/companymail', $data);
		}
    }
	public function trash(){
		$this->load->library('imap');
		$staffid = get_staff_user_id();
		$table = db_prefix() . 'personal_mail_setting';
		$imapconf = get_imap_setting();
		$folder = $_REQUEST['folder'];
		$this->imap->move_to_trash($_REQUEST['mails'],$imapconf,$folder);
		echo json_encode($_REQUEST['mails']);
	}
	public function unread(){
		$imapconf = get_imap_setting();
		$ch_data = $this->imap->mark_as_unread($_REQUEST['mails'],$imapconf);
		echo json_encode($_REQUEST['mails']);
	}
	public function read_msg(){
		$imapconf = get_imap_setting();
		$ch_data = $this->imap->mark_as_read_company($_REQUEST['mails'],$imapconf);
		echo json_encode($_REQUEST['mails']);
		
	}
	public function company_mail_setting() {
		if(get_option('connect_mail')=='no'){
			redirect(site_url().'admin/company_mail/configure_email');
			exit;
		}
		if(get_option('company_mail_server')=='no'){
			redirect(site_url().'admin/company_mail/email_settings');
		}
		$staffid = get_staff_user_id();
		$data = array();
		$table = db_prefix() . 'user_mail_setting';
		$data['settings'] = $this->get_or_update_setting($staffid,$table);
		unset($_SESSION['debug']);
		$data['title'] ='Connect Email';
		$this->load->view('admin/settings/user/companymail', $data);
    }
	public function create_template(){
		$ins_data['user_id'] = get_staff_user_id();
		$ins_data['template_name'] = trim($_POST['template_name']);
		$ins_data['description'] = trim($_POST['template_description']);
		$req_data = array();
		$req_data['status']  = 'success';
		if($ins_data['template_name']== ''){
			$req_data['name_error'] = 1;
			$req_data['status']  = 'error';
		}
		if($ins_data['description']==''){
			$req_data['description_error'] = 1;
			$req_data['status']  = 'error';
		}
		if($req_data['status'] == 'success'){
			$this->db->insert(db_prefix() . 'template', $ins_data);
		}
		echo json_encode($req_data);
	}
	public function update_template(){
		$upd_data['user_id'] = $staffid = get_staff_user_id();
		$upd_data['template_name'] = trim($_POST['template_edit_name']);
		$upd_data['description'] = trim($_POST['template_edit_description']);
		$req_data = array();
		$req_data['status']  = 'success';
		if($upd_data['template_name']== ''){
			$req_data['name_error'] = 1;
			$req_data['status']  = 'error';
		}
		if($upd_data['description']==''){
			$req_data['description_error'] = 1;
			$req_data['status']  = 'error';
		}
		$cond = array('user_id'=>$staffid,'id'=>$_POST['template_id']);
		if($req_data['status'] == 'success'){
			$this->db->update(db_prefix() . 'template', $upd_data,$cond);
		}
		echo json_encode($req_data);
	}
	public function change_default(){
		$staffid = get_staff_user_id();
		$data = array();
		$table = db_prefix() . 'template';
		$upd_data['default'] = '0';
		$cond = array('user_id'=>$staffid);
		$this->db->where($cond);
		$this->db->update(db_prefix() . 'template',$upd_data);
		$id = trim($_POST['default_template']);
		$cond = array('user_id'=>$staffid,'id'=>$id);
		$upd_data['default'] = '1';
		$this->db->where($cond);
		$this->db->update(db_prefix() . 'template',$upd_data);
		$cond = array('id'=>$id,'default'=>'1');
		$templates = $this->db->where($cond)->get($table)->result_array();
		$json_data['status'] = 'error';
		if(!empty($templates[0])){
			$json_data = $templates[0];
			$json_data['status'] = 'success';
		}
		echo json_encode($json_data);
	}
	public function delete_template(){
		$staffid = get_staff_user_id();
		$table = db_prefix() . 'template';
		$cond = array('user_id'=>$staffid,'id'=>$_POST['template_id']);
		$this->db->where($cond);
		$result = $this->db->delete($table);
	}
	public function edit_template(){
		$staffid = get_staff_user_id();
		$table = db_prefix() . 'template';
		$cond = array('user_id'=>$staffid,'id'=>$_POST['template_id']);
		$templates = $this->db->where($cond)->get($table)->result_array();
		echo json_encode($templates[0]);
	}
	public function template_list() {
		$staffid = get_staff_user_id();
		$data = array();
		$table = db_prefix() . 'template';
		$cond = array('user_id'=>$staffid);
		$this->db->order_by('id','desc');
		$templates = $this->db->where($cond)->get($table)->result_array();
		$hide_id = '"template-list"';
		$output['header'] = "";
		$output['select_drop'] = '<option value="">None</option>';
		$output['table'] = "<div class='table-responsive' style='padding:15px 0px 15px 0px;'>";
		$output['table'] .= "<div class='col-md-12'>";
		if(!empty($templates)){
			//$output['table'] .= "<div class=''><div class='form-group' style='margin-top:10px;margin-bottom:10px;'><label style='padding-right:10px;font-weight:700;'>Default Template </label>";
		//	$output['table'] .= "<div class=''><div class='form-group' style='margin-top:10px;margin-bottom:10px;'>";
			//$output['table'] .= "<div class='' style='margin-top:10px;margin-bottom:10px;'>";
			//$output['table'] .= "<select name='default_template' class='form-control selectwidth' id='ch_default_temp'> <option value=''>None</option>";
			/*foreach($templates as $template1){
				if($template1['default'] ==1){
					$output['table'] .= "<option value='".$template1['id']."' selected>".$template1['template_name']."</option>";
				}
				else{
					$output['table'] .= "<option value='".$template1['id']."'>".$template1['template_name']."</option>";
				}
			}*/
			/*$output['table'] .= "</select><div class='btn-group' style='margin-left:10px;'><button type='button' class='btn btn-primary' id='default_submit' onclick='submit_default()' style='letter-spacing: 2px;
    font-weight: 700;'>Save</button></div></div>";*/
			//$output['table'] .= "</div>";
			$output['table'] .= "<table class='table dt-table' ><thead><th>Name</th><th>Action</th></thead>";
			foreach($templates as $template2){
				$req_class = "list_1".$template2['id'];
				$req_id    = "'".$template2['id']."'";
				$output['table'] .= '<tr class="'.$req_class.'">';
				$output['table'] .= '<td>'.$template2['template_name'].'</td>';
				$output['table'] .= '<td><center><a href="javascript:void(0)" onclick="edit_template('.$req_id.')" data-toggle="modal" data-target="#Edit-template" title="Edit"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" onclick="del_template('.$req_id.')" title="Delete"><i class="fa fa-trash"></i></a></center></td>';
				$output['table'] .= '</tr>';
				$output['select_drop'] .= '<option value="'.$template2['id'].'">'.$template2['template_name'].'</option>';
			}
		}
		else{
			$output['table'] .= "</div><div style='float:right;margin:10px;'></div><table class='table  table-bordered table-tasks dataTable no-footer' ><thead><th>Action</th></thead>";
			$output['table'] .="<tr><td colspan='2'>No Record's Found</td></tr>";
		}
		$output['table'] .= '</table></div>';
        echo json_encode($output);
        exit;
    }
	/*public function check_mail(){
		error_reporting(-1);
		ini_set('display_errors', 1);
		$staff_id = get_staff_user_id();
            $this->db->where('staffid ', $staff_id);
			if(get_option('company_mail_server')=='yes'){
				$redirect_url1 = site_url().'admin/company_mail/check_company_mail';
			}
			else{
				$redirect_url1 = site_url().'admin/company_mail/check_user_mail';
			}
			
			$_REQUEST['deal_id'] = 366; 
			$redirect_url = site_url().'admin/tasks';
            $assignee_admin = $this->db->get(db_prefix() . 'staff')->row();
			$req_name = trim($assignee_admin->firstname.' '.$assignee_admin->lastname);
            $data['description'] = 'test';
            $data['task_mark_complete_id'] = '1';
            $data['billable'] = '2';
            $data['tasktype'] = '2';
            $data['name'] = 'check';
            $data['assignees'][0] = $assignee_admin->staffid;
            $data['startdate'] = date('d-m-Y H:i:s');
            $data['priority'] = '1';
            $data['repeat_every_custom'] = '1';
            $data['repeat_type_custom'] = 'day';
            $data['rel_type'] = 'project';
            $data['tags'] = '';
            $this->db->where('email', $this->input->post('toemail'));
			if(get_option('deal_map') != 'if more than one open deal – allow to map manually'){
				$ch_project_id = get_deal_id($this->input->post('toemail'),get_option('deal_map'));
			}
			else{
				$ch_project_id = $_REQUEST['deal_id'];
			}
            $contacts = $this->db->get(db_prefix() . 'contacts')->row();
			if(!empty($data['description'])){
           // if ($contacts ) {
                $this->db->where('contacts_id', $contacts->id);
                $this->db->limit(1);
                $project = $this->db->get(db_prefix() . 'project_contacts')->row();
               // if ($project && !empty($ch_project_id)) {
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
                    //Initialize the connection:

                    $this->load->library('email');
                     $imapconf =  $smtpconf = array();
					 $imapconf = get_imap_setting();
					 $smtpconf = get_smtp_setings();
                    $this->email->initialize($smtpconf);
                    $this->email->from($imapconf['username'], $req_name);
                    $list = array('rajeshkumar.r@techmango.net');
                    $cc_list = array('');
                    $bcc_list = array('');
					
                    $this->email->to($list);
                    $this->email->cc('');
                    $this->email->bcc('');
                    $this->email->reply_to($imapconf['username'], 'Replay me');
                    $this->email->subject('test');
                    $this->email->message('checking');
					$req_files = array();
					if(!empty($_FILES["attachment"])){
						$m_file = explode(',',$_REQUEST['m_file']);
						$file_count = count($_FILES['attachment']['name']);
						for($j=0;$j<$file_count;$j++){
							if(!empty($_FILES['attachment']['name'][$j]) && (empty($m_file[0]) || !in_array($j, $m_file))){
								$newFilePath = $req_files[$j] = FCPATH.'uploads/'.$_FILES['attachment']['name'][$j];
								move_uploaded_file($_FILES['attachment']['tmp_name'][$j], $newFilePath);
								$this->email->attach( $newFilePath);
							}
						}
					} 
                    if ($ch_data = $this->email->send()) {
						if(!empty($req_files)){
							foreach($req_files as $req_file12){
								unlink($req_file12);
							}
						}
						//imap_num_recent();
                        $this->load->library('imap');
						
                        //Initialize the connection:
                        $imap = $this->imap->check_imap($imapconf);
						//Get the required datas:
                        if ($imap) {
                            $uid = $this->imap->get_company_latest_email_addresses($imapconf);
							/*if($uid == 'Cannot Read') {
								//$message = "Don't have access to read Sent Folder. Please enable the read permission to Sent folder in your mail server.";
								//set_alert('warning', $message);
                            	redirect($redirect_url1);
							//}
							$messages = $this->imap->get_company_mail_details($imapconf,$uid);
                            $data['source_from'] = $uid;
                        } else {
                            $message       = 'Cannot Connect IMAP Server.';
                            set_alert('warning', $message);
                            redirect($redirect_url1);
                        }
                    } else {
                        $message       = 'Cannot Connect SMTP Server.';
                        set_alert('warning', $message);
                        redirect($redirect_url1);
                    }
                /*} else {
                    //$message       = 'Cannot create Activity.';
                   // set_alert('warning', $message);
                   // redirect($redirect_url1);
               // }
            //} else {
            //    $message       = 'Email address not exist.';
             //   set_alert('warning', $message);
            //    redirect($redirect_url1);
            //}
            }else{
				
				 $message       = 'Please enter message';
				set_alert('warning', $message);
				redirect($redirect_url1);
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
					
					$source_from1 = array_column($messages['attachments'], 'name'); 
					$i = 0;
					$cur_project12 = $this->projects_model->get_project($ch_project_id);
					$req_msg[$i]['project_id']	= $ch_project_id;
					$req_msg[$i]['task_id']		= $id;
					$req_msg[$i]['mailid']		= $messages['id'];
					$req_msg[$i]['uid'] 		= $messages['uid'];
					$req_msg[$i]['staff_id'] 	= $cur_project12->teamleader;
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
                    redirect($redirect_url1);
			}
	}*/
	public function createtaskcompanymail() {
		if($this->input->post('deal_id')){
			$relarray =explode('_',$this->input->post('deal_id'));
			if($relarray){
				$_POST['rel_type'] =$relarray[0];
				$_POST['deal_id'] =$relarray[1];
			}
		}
		if(get_option('connect_mail')=='no'){
			$redirect_url = admin_url('outlook_mail/connect_outlook');
		}else{
			if(get_option('company_mail_server')=='yes'){
				$redirect_url = site_url().'admin/company_mail/check_company_mail';
			}
			else{
				$redirect_url = site_url().'admin/company_mail/check_user_mail';
			}
		}

		if($this->input->post('redirect')){
			if($this->input->post('redirect') =='lead'){
				$redirect_url = admin_url('leads/lead/'.$_POST['deal_id'].'?group=tab_email');
			}
		}
		$this->load->library('mails/imap_mailer');
		$this->imap_mailer->set_to($this->input->post('toemail', false));
		$this->imap_mailer->set_subject($this->input->post('name', false));
		$this->imap_mailer->set_message($this->input->post('description', false));
		$this->imap_mailer->set_cc($this->input->post('ccemail', false));
		$this->imap_mailer->set_bcc($this->input->post('bccemail', false));
		$this->imap_mailer->set_attachments($_FILES["attachment"]);
		$this->imap_mailer->set_rel_type($this->input->post('rel_type', false));
		$this->imap_mailer->set_rel_id($this->input->post('deal_id', false));
		$this->imap_mailer->set_redirectTo($redirect_url);
		$this->imap_mailer->set_draft($this->input->post('cur_draft_id', false));
		$this->imap_mailer->send();
    }
	public function deal_values(){
		echo render_deal_lead_list_by_email($_REQUEST['toemail']);
	}
	public function forward() {

		if(get_option('connect_mail')=='no'){
			$redirect_url = admin_url('outlook_mail/connect_outlook');
		}else{
			if(get_option('company_mail_server')=='yes'){
				$redirect_url = site_url().'admin/company_mail/check_company_mail';
			}
			else{
				$redirect_url = site_url().'admin/company_mail/check_user_mail';
			}
		}
		$this->load->library('mails/imap_mailer');
		$this->imap_mailer->set_to($this->input->post('toemail', false));
		$this->imap_mailer->set_subject($this->input->post('name', false));
		$this->imap_mailer->set_message($this->input->post('description', false));
		$this->imap_mailer->set_cc($this->input->post('ccemail', false));
		$this->imap_mailer->set_bcc($this->input->post('bccemail', false));
		$this->imap_mailer->set_attachments($_FILES["attachment"]);
		$this->imap_mailer->set_rel_type($this->input->post('rel_type', false));
		$this->imap_mailer->set_rel_id($this->input->post('rel_id', false));
		$this->imap_mailer->set_redirectTo($redirect_url);
		$this->imap_mailer->send();
    }
	public function reply() {
		if(get_option('connect_mail')=='no'){
			$redirect_url = admin_url('outlook_mail/connect_outlook');
		}else{
			if(get_option('company_mail_server')=='yes'){
				$redirect_url = site_url().'admin/company_mail/check_company_mail';
			}
			else{
				$redirect_url = site_url().'admin/company_mail/check_user_mail';
			}
		}
		
		$this->load->library('mails/imap_mailer');
		$this->imap_mailer->set_to($this->input->post('toemail', false));
		$this->imap_mailer->set_subject($this->input->post('name', false));
		$this->imap_mailer->set_message($this->input->post('description', false));
		$this->imap_mailer->set_cc($this->input->post('ccemail', false));
		$this->imap_mailer->set_bcc($this->input->post('bccemail', false));
		$this->imap_mailer->set_attachments($_FILES["attachment"]);
		$this->imap_mailer->set_rel_type($this->input->post('rel_type', false));
		$this->imap_mailer->set_rel_id($this->input->post('rel_id', false));
		$this->imap_mailer->set_redirectTo($redirect_url);
		$this->imap_mailer->set_parentId($this->input->post('parent_id',false));
		$this->imap_mailer->send();
    }
	public function autocomplete(){
		$this->load->library('imap');
		$imapconf = $data = array();
		$staffid = get_staff_user_id();;
		$table = db_prefix() . 'contacts';
		$term = '';
		if(!empty($_REQUEST['search'])){
			$term = $_REQUEST['search'];
		}
		$all_array = search_email_address($staffid,$table,$term);
		$mail_address = array();
		$i = 0;
		if(!empty($all_array)){
			$table1 = db_prefix() . 'clients';
			foreach($all_array as $all_array1){
				$company = get_company($all_array1['userid'],$table1);
				$company = $company[0]['company'];
				$mail_address[$i]['label'] = $all_array1['firstname'].' '.$all_array1['lastname'].' - '.$all_array1['email'].' ('.$company.')';
				$mail_address[$i]['value'] = $all_array1['email'];
				$mail_address[$i]['id'] = $all_array1['id'];
				$i++;
			}
		}
		echo json_encode($mail_address);
	}

	public function linkemail()
	{
		if($this->input->post('linkto')){
			$relarray =explode('_',$this->input->post('linkto'));
			if($relarray){
				$rel_type =$relarray[0];
				$rel_id =$relarray[1];

				$this->load->library('mails/imap_mailer');
				$this->imap_mailer->set_rel_type($rel_type);
				$this->imap_mailer->set_rel_id($rel_id);
				$this->imap_mailer->connectEmail($this->input->post('linktouid'));
				echo json_encode(array('success'=>true,'msg'=>'Email linked successfully'));
				die;
			}
		}

		echo json_encode(array('success'=>false,'msg'=>'Could not link email'));

		
	}
}