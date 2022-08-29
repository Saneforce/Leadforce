<?php set_time_limit(14400000);
ini_set('max_execution_time', 14400000);
//ini_set('default_socket_timeout', 600000);
ini_set('default_socket_timeout', 5);
defined('BASEPATH') or exit('No direct script access allowed');

class Cronjob extends CI_Controller 
{
    public function __construct()
    {
		parent::__construct();
		$this->load->model('base');
        $this->load->model('projects_model');
        $this->load->model('tasktype_model');
        // $this->load->model('callsettings_model');
		// $this->load->model('knowlarity_model');
        
    }

    /* Open also all taks if user access this /tasks url */
    public function index($id = '')
    {
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
        //echo "<pre>"; print_r($imapconf); exit;
		$inboxList       = $this->imap->get_inboxitems();
		
		foreach($inboxList as $inbox) {
			$this->db->where('source_from', $inbox['uid']);
            $taskExist = $this->db->get(db_prefix() . 'tasks')->row();
            if (empty($taskExist)) {
				$this->db->where('email', $inbox['from']['email']);
                $staffs = $this->db->get(db_prefix() . 'staff')->row();
				if($staffs) {
					$assignto = $staffs->staffid;
					$ccmails = array();
					foreach($inbox['cc'] as $ccmail) {
						$ccmails[] = $ccmail['email'];
					}
					if(in_array($imapconf['username'],$ccmails)) {
					
						
						foreach($inbox['to'] as $tomail) {
							$this->db->where('email', $tomail['email']);
							$contacts = $this->db->get(db_prefix() . 'contacts')->row();
						
							if ($contacts) {
								$data = array();
								$data['description'] = $inbox['description'];
								$data['task_mark_complete_id'] = '';
								$data['billable'] = 'on';
								$data['tasktype'] = 2;
								$data['name'] = $inbox['name'];
								$data['assignees'][0] = $assignto;
								$data['startdate'] = date('d-m-Y');
								$data['priority'] = 1;
								$data['repeat_every_custom'] = 1;
								$data['repeat_type_custom'] = 'day';
								$data['rel_type'] = 'project';
								$data['tags'] = '';
								$data['source_from'] = $inbox['uid'];
								//$this->db->where('contacts_id', $contacts->id);
								//$this->db->limit(1);
								//$project = $this->db->get(db_prefix() . 'project_contacts')->row();
								$sql = 'SELECT id FROM tblprojects
								 WHERE id IN (SELECT project_id FROM '.db_prefix().'project_contacts WHERE contacts_id=' .$contacts->id . ') AND deleted_status = 0 limit 1';
								$query = $this->db->query($sql);
								$project = $query->result_array();
								//echo $this->db->last_query();
								//echo "<pre>"; print_r($project); exit;
								if ($project) {
									$data['rel_id'] = $project[0]['id'];
									$data['contacts_id'] = $contacts->id;
								}
								//echo "<pre>"; print_r($data); exit;
								//echo "<pre>"; print_r($data); print_r($project); exit;
								if(isset($data['task_mark_complete_id']) && !empty($data['task_mark_complete_id'])){
									$this->tasks_model->mark_as(5, $data['task_mark_complete_id']);
								}
								if(isset($data['task_mark_complete_id'])){
									unset($data['task_mark_complete_id']);
								}
							   
								$data_assignee = $data['assignees'];
								unset($data['assignees']);
								//echo "<pre>"; print_r($data); exit;
								$id   = $data['taskid']  = $this->tasks_model->addcrontask($data);
								//echo $this->db->last_query();
								//echo "<pre>"; print_r($data); exit;
								foreach($data_assignee as $taskey => $tasvalue ){
									$data['assignee'] = $tasvalue;
									$this->tasks_model->add_crontask_assignees($data);
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
								
							}
						}
					
					}
				}
            }
        }
        $sentList       = $this->imap->get_sentitems();
        //echo "<pre>"; print_r($sentList); exit;
        foreach($sentList as $sent) {
            $this->db->where('source_from', $sent['uid']);
            $taskExist = $this->db->get(db_prefix() . 'tasks')->row();
            if (empty($taskExist)) {
                foreach($sent['toemail'] as $tomail) {
                    $this->db->where('email', $tomail['email']);
                    $contacts = $this->db->get(db_prefix() . 'contacts')->row();
                    
                    $this->db->where('admin', 1);
                    $assignee_admin = $this->db->get(db_prefix() . 'staff')->row();
                    if ($contacts) {
                        $data = array();
                        $data['description'] = $sent['description'];
                        $data['task_mark_complete_id'] = '';
                        $data['billable'] = 'on';
                        $data['tasktype'] = 2;
                        $data['name'] = $sent['name'];
                        $data['assignees'][0] = $assignee_admin->staffid;
                        $data['startdate'] = date('d-m-Y');
                        $data['priority'] = 1;
                        $data['repeat_every_custom'] = 1;
                        $data['repeat_type_custom'] = 'day';
                        $data['rel_type'] = 'project';
                        $data['tags'] = '';
                        $data['source_from'] = $sent['uid'];
                        $sql = 'SELECT id FROM tblprojects
								 WHERE id IN (SELECT project_id FROM '.db_prefix().'project_contacts WHERE contacts_id=' . $contacts->id . ') AND deleted_status = 0 ORDER BY id ASC limit 1';
						$query = $this->db->query($sql);
						$project = $query->result_array();
						//echo $this->db->last_query();
						//echo "<pre>"; print_r($project); exit;
						if ($project) {
							$data['rel_id'] = $project[0]['id'];
							$data['contacts_id'] = $contacts->id;
						}
                        
                        if(isset($data['task_mark_complete_id']) && !empty($data['task_mark_complete_id'])){
                            $this->tasks_model->mark_as(5, $data['task_mark_complete_id']);
                        }
                        if(isset($data['task_mark_complete_id'])){
                            unset($data['task_mark_complete_id']);
                        }
                       
                        $data_assignee = $data['assignees'];
                        unset($data['assignees']);
                        //echo "<pre>"; print_r($data); exit;
                        $id   = $data['taskid']  = $this->tasks_model->addcrontask($data);
                        foreach($data_assignee as $taskey => $tasvalue ){
                            $data['assignee'] = $tasvalue;
                            $this->tasks_model->add_crontask_assignees($data);
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
                        
                    }
                }
            }
        }
        echo "Activities Created Successfully";  exit;
    }
	public function store_local_mails(){
		$totData = array();
		$totData['status'] = 'store local mails cron jobs';
		$totData['last_updated'] = date('Y-m-d H:i:s');
		$this->db->insert('tblcronjobs', $totData);
		if(get_option('connect_mail') =='yes'){
			$this->load->library('imap');
			$this->db->where('active', 1);
			$all_staffs = $this->db->get(db_prefix() . 'staff')->result_array();
			if(!empty($all_staffs)){
				foreach($all_staffs as $staff1){
					$imapconf = $data = array();
					$imapconf = get_imap_setting($staff1['staffid']);
					if($this->imap->check_imap($imapconf)){
						
						if(get_option('email_local')=='yes' && get_option('link_deal')=='yes'){
							$this->imap->connect($imapconf);
							$ch_data = $this->imap->get_all_mails($staff1['staffid'],get_option('deal_map'),$imapconf);
							$this->db->reconnect(); 
							if($ch_data){
								//$table = db_prefix() . 'localmailstorage';
								//$this->db->insert_batch($table, $ch_data);
							}
						}
					}
				}
				echo "Activities Created Successfully";  
			}
		}
		else{
			$this->db->where('active', 1);
			$all_staffs = $this->db->get(db_prefix() . 'staff')->result_array();
			if(!empty($all_staffs)){
				foreach($all_staffs as $staff1){
					$outlook_data = outlook_credential();
					$staff_id = $staff1['staffid'];
					
					$cur_token = get_outlook_token_bycron($staff_id);
					$token		= $cur_token->token;
					$refresh_token		= $cur_token->refresh_token;
					$user_email = $cur_token->email;
					if(get_option('email_local')=='yes' && get_option('link_deal')=='yes' && !empty($refresh_token)){
						$check_data = refresh_token($user_email,$refresh_token);
						$headers = array(
							"User-Agent: php-tutorial/1.0",
							"Authorization: Bearer ".$token,
							"Accept: application/json",
							"client-request-id: ".makeGuid(),
							"return-client-request-id: true",
							"X-AnchorMailbox: ". $user_email
						);
						$search = array("\$select" => "Id,Subject,ReceivedDateTime,Sender,From,ToRecipients,HasAttachments,BodyPreview,isRead,SentDateTime,CcRecipients,BccRecipients,ReplyTo,Body,Flag");
						$outlookApiUrl = $outlook_data["api_url"] . "/me/mailFolders" ;
						$response = runCurl($outlookApiUrl, null, $headers);
						$response = explode("\n", trim($response));
						$response = $response[count($response) - 1];
						$response = json_decode($response, true);
						if(!empty($response['value'])){
							foreach($response['value'] as $folder1){
								$icon = ucwords(strtolower($folder1['DisplayName']));
								if($icon == 'Inbox'){
									$outlookApiUrl1 = $outlook_data["api_url"] . "/me/mailFolders/".$folder1['Id']."/messages?". http_build_query($search);
									$response1 = runCurl($outlookApiUrl1, null, $headers);
									$response1 = explode("\n", trim($response1));
									$response1 = $response1[count($response1) - 1];
									$response1 = json_decode($response1, true);
									$sQuery1 = "select * from ".db_prefix()."outlookmsgid where staff_id = '".$staff_id."' order by id desc ";
									$rResults12 = $this->db->query($sQuery1)->result_array();
									$req_msg_id = '';
									if(!empty($rResults12[0]['msg_id'])){
										$req_msg_id = $rResults12[0]['msg_id'];
									}
									
									if(!empty($response1['value']) && count($response1["value"])){
										
										foreach ($response1["value"] as $mail) {
											$source_from1 = $source_from2 = array();
											if(!empty($req_msg_id) && $req_msg_id == $mail['Id']){
												break;
											}
											if(!empty($mail['HasAttachments']) && $mail["HasAttachments"] == 1){
												$list_attachment = $this->list_attachment($mail['Id'],$staff_id);
												if(!empty($list_attachment)){
													$source_from1 = array_column($list_attachment, 'Name'); 
													$source_from2 = array_column($list_attachment, 'Id');
												}
											}
											$uid_data = array('msg_id'=>$mail['Id'],'staff_id'=>$staff_id);
											$this->db->insert(db_prefix() . 'outlookmsgid', $uid_data);
											$req_project_id = get_deal_id_contactuser($mail["From"]["EmailAddress"]["Address"],get_option('deal_map'));
											if(empty($req_project_id)){
												$req_project_id = get_deal_id_otheruser(get_option('deal_map'),$mail["From"]["EmailAddress"]["Address"],$mail["CcRecipients"]["EmailAddress"]["Address"],$mail["BccRecipients"]["EmailAddress"]["Address"]);
											}
											
											$req_project_id = json_decode($req_project_id);
											
											$req_project_id = $req_project_id->project_id;
											if(!empty($req_project_id) && !empty($mail['Id'])){
												$i = $j2 = 0;
												$cur_project12 = $this->projects_model->get_project($req_project_id);
												$req_msg[$i]['project_id']	= $req_project_id;
												$req_msg[$i]['task_id']		= $req_project_id;
												$req_msg[$i]['staff_id'] 	= $staff_id;
												$req_msg[$i]['from_email'] 	= $mail['From']['EmailAddress']['Address'];
												$req_msg[$i]['from_name'] 	= $mail['From']['EmailAddress']['Name'];
												$mail_to = $mail_cc = $mail_bcc = array();
												if(!empty($mail['ToRecipients'])){
													foreach($mail['ToRecipients'] as $mail1){
														$mail_to[$j2]['email']	= $mail1['EmailAddress']['Address'];
														$mail_to[$j2]['name'] 	= $mail1['EmailAddress']['Name'];
														$j2++;
													}
												}
												$j2 = 0;
												
												if(!empty($mail['CcRecipients']['EmailAddress']['Address'])){
													foreach($mail['CcRecipients'] as $mail1){
														$mail_cc[$j2]['email']	= $mail1['EmailAddress']['Address'];
														$mail_cc[$j2]['name'] 	= $mail1['EmailAddress']['Name'];
														$j2++;
													}
												}
												$j2 = 0;
												if(!empty($mail['BccRecipients']['EmailAddress']['Address'])){
													foreach($mail['BccRecipients'] as $mail1){
														$mail_bcc[$j2]['email']	= $mail1['EmailAddress']['Address'];
														$mail_bcc[$j2]['name'] 	= $mail1['EmailAddress']['Name'];
														$j2++;
													}
												}
												$req_msg[$i]['mail_to']		= json_encode($mail_to);
												$req_msg[$i]['cc']			= json_encode($mail_cc);
												$req_msg[$i]['bcc']			= json_encode($mail_bcc);
												$req_msg[$i]['reply_to']	= json_encode($mail['ReplyTo']);
												$req_msg[$i]['message_id']	= $mail['Id'];
												$req_msg[$i]['in_reply_to']	= json_encode($mail['ReplyTo']);
												$req_msg[$i]['date']		= $mail['ReceivedDateTime'];
												$req_msg[$i]['udate']		= strtotime($mail['SentDateTime']);
												$req_msg[$i]['subject']		= $mail['Subject'];
												
												$req_msg[$i]['mail_read']	= $mail['IsRead'];
												$req_msg[$i]['answered']	= $mail['IsRead'];
												$req_msg[$i]['flagged']		= $mail['Flag']['FlagStatus'];
												$req_msg[$i]['attachements']= json_encode($source_from1);
												$req_msg[$i]['attachment_id']= json_encode($source_from2);
												$req_msg[$i]['body_html']	= $mail['Body']['Content'];
												$req_msg[$i]['body_plain']	= $mail['BodyPreview'];
												$req_msg[$i]['folder']	= 'inbox';
												$req_msg[$i]['mail_by']	= 'outlook';
												$table = db_prefix() . 'localmailstorage';
												$this->db->insert_batch($table, $req_msg);
											}
										}
									}
								}
							}
						}
					}
				}
			}
				echo "Activities Created Successfully";  
		}
		exit;
	}
	public function list_attachment($msg_id,$stafff_id){
		$outlook_data = outlook_credential();
		$cur_token = get_outlook_token_bycron($stafff_id);
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

	public function answered_call_history() {
		$expurl = explode('/admin',admin_url());
		//echo $_SERVER['HTTP_HOST'];
		//exit;
		$this->dynamicDB = array(
			'hostname' => 'localhost',
			'username' => 'root',
			'password' => '',
			'database' => 'perfexcrm',
			'dbdriver' => 'mysqli',
			'dbprefix' => 'tbl',
			'pconnect' => FALSE,
			'db_debug' => TRUE,
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci'
		);
		$this->db2 = $this->load->database($this->dynamicDB, TRUE); 

		$this->db2->select('*');
	    $this->db2->from('tblcall_settings');
		$query = $this->db2->get();
		if ( $query->num_rows() > 0 )
    	{
			$url = 'https://piopiy.telecmi.com/v1/outanswered';
			//APP Credentials
			$row = $query->row_array();
			
			$this->db2->select('last_updated');
			$this->db2->where('action_for','answered');
			$this->db2->from('tblcall_cron');
			$query1 = $this->db2->get();
			if ( $query1->num_rows() > 0 )
			{
				$row1 = $query1->row_array();
				//$start_time = $row1['last_updated'];
				//echo "<br>";
				//$end_time = $start_time+900000;

				/** Temperary */
				$start_time = 1636482601000;
				$end_time = round(microtime(true) * 1000);

				$lastUpdate = array();
				$lastUpdate['last_updated'] = $end_time;
				$this->db2->where('action_for','answered');
				$this->db2->update('tblcall_cron', $lastUpdate);
			} else {
				$s_date = date('Y-m-d').' 00:00:01';
				// $start_time = strtotime($s_date).'000';
				// $end_time = round(microtime(true) * 1000); //exit;

				/** Temperary */
				$start_time = 1636482601000;
				$end_time = round(microtime(true) * 1000);

				$lastUpdate = array();
				$lastUpdate['last_updated'] = $end_time;
				$lastUpdate['action_for'] = 'answered';
				$this->db2->insert('tblcall_cron', $lastUpdate);
			}
				
			$ch = curl_init( $url );
			# Setup request to send json via POST.
			$payload = json_encode( array( "appid"=> (int)$row['app_id'], 
			"token"=> $row['app_secret'], 
			"start_date"=> (float)$start_time, 
			"end_date"=> (float)$end_time, 
			"page"=> 1,
			"limit"=> 10) );
			//curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			# Return response instead of printing.
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			# Send request.
			$result = curl_exec($ch);
			curl_close($ch);
			# Print response.
			//print($result);
			$result = json_decode($result, true);
			//pre($result);
			$count = $result['count'];
			$page = ceil($result['count']/10);
			$totData = array();
			for($i=1; $i<=$page; $i++) {
				$res = array();
				$ch1 = curl_init( $url );
				# Setup request to send json via POST.
				$data = json_encode( array( "appid"=> (int)$row['app_id'], 
				"token"=> $row['app_secret'], 
				"start_date"=> (float)$start_time, 
				"end_date"=> (float)$end_time,
				"page"=> (int)$i,
				"limit"=> 10) );
				//curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt( $ch1, CURLOPT_POSTFIELDS, $data );
				curl_setopt( $ch1, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
				# Return response instead of printing.
				curl_setopt( $ch1, CURLOPT_RETURNTRANSFER, true );
				# Send request.
				$res = curl_exec($ch1);
				curl_close($ch1);
				$res = json_decode($res, true);
				foreach($res['cdr'] as $val) {
					//pre($val);
					$this->db2->select('last_updated');
					$this->db2->where('cmiuid',$val['cmiuid']);
					$this->db2->from('tblcall_history');
					$query2 = $this->db2->get();
					if ( $query2->num_rows() > 0 ) {
						
					} else {
						// if (!is_dir('uploads/recordings/' . $val['agent'])) {
						// 	// dir doesn't exist, make it
						// 	mkdir('uploads/recordings/' . $val['agent']);
						// }
						$mp3 = 'https://piopiy.telecmi.com/v1/play?appid='.$row['app_id'].'&token='.$row['app_secret'].'&file='.$val['filename'];
						//file_put_contents($_SERVER['DOCUMENT_ROOT']."/perfex_crm/uploads/recordings/".$val['filename'], fopen($mp3, 'r'));
						file_put_contents($_SERVER['DOCUMENT_ROOT']."/uploads/recordings/".$val['filename'], fopen($mp3, 'r'));
						//echo "11111"; exit;
						//$totData[] = $val;
						$totData = array();
						$totData['task_id'] = 324;
						$totData['cmiuid'] = $val['cmiuid'];
						$totData['duration'] = $val['duration'];
						$totData['agent'] = $val['agent'];
						$totData['billedsec'] = $val['billedsec'];
						$totData['filename'] = $val['filename'];
						$totData['rate'] = $val['rate'];
						$totData['record'] = $val['record'];
						$totData['con_name'] = $val['name'];
						$totData['call_from'] = $val['from'];
						$totData['call_to'] = $val['to'];
						$totData['time'] = $val['time'];
						$totData['last_updated'] = $end_time;
						
						$this->db2->insert('tblcall_history', $totData);
					}
				}
			}
			
			echo "done";
exit;
		}
	}

	public function missed_call_history() {
		
		//echo $_SERVER['HTTP_HOST'];
		//exit;
		$this->dynamicDB = array(
			'hostname' => 'localhost',
			'username' => 'root',
			'password' => '',
			'database' => 'perfexcrm',
			'dbdriver' => 'mysqli',
			'dbprefix' => 'tbl',
			'pconnect' => FALSE,
			'db_debug' => TRUE,
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci'
		);
		$this->db2 = $this->load->database($this->dynamicDB, TRUE); 

		$this->db2->select('*');
	    $this->db2->from('tblcall_settings');
		$query = $this->db2->get();
		if ( $query->num_rows() > 0 )
    	{
			$url = 'https://piopiy.telecmi.com/v1/outmissed';
			//APP Credentials
			$row = $query->row_array();
			
			$this->db2->select('last_updated');
			$this->db2->where('action_for','missed');
			$this->db2->from('tblcall_cron');
			$query1 = $this->db2->get();
			if ( $query1->num_rows() > 0 )
			{
				// $row1 = $query1->row_array();
				// $start_time = $row1['last_updated'];
				// //echo "<br>";
				// $end_time = $start_time+900000;

				/* Temperary*/
				$start_time = 1636482601000;
				$end_time = round(microtime(true) * 1000);

				$lastUpdate = array();
				$lastUpdate['last_updated'] = $end_time;
				$this->db2->where('action_for','missed');
				$this->db2->update('tblcall_cron', $lastUpdate);
			} else {
				// $s_date = date('Y-m-d').' 00:00:01';
				// $start_time = strtotime($s_date).'000';
				$end_time = round(microtime(true) * 1000); //exit;

				$start_time = 1636482601000;
				//$end_time = 1636609542000;

				$lastUpdate = array();
				$lastUpdate['last_updated'] = $end_time;
				$lastUpdate['action_for'] = 'missed';
				$this->db2->insert('tblcall_cron', $lastUpdate);
			}
				
			$ch = curl_init( $url );
			# Setup request to send json via POST.
			$payload = json_encode( array( "appid"=> (int)$row['app_id'], 
			"token"=> $row['app_secret'], 
			"start_date"=> (float)$start_time, 
			"end_date"=> (float)$end_time, 
			"page"=> 1,
			"limit"=> 10) );
			//curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			# Return response instead of printing.
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			# Send request.
			$result = curl_exec($ch);
			curl_close($ch);
			# Print response.
			//print($result);
			$result = json_decode($result, true);
			//pre($result);
			$count = $result['count'];
			$page = ceil($result['count']/10);
			$totData = array();
			for($i=1; $i<=$page; $i++) {
				$res = array();
				$ch1 = curl_init( $url );
				# Setup request to send json via POST.
				$data = json_encode( array( "appid"=> (int)$row['app_id'], 
				"token"=> $row['app_secret'], 
				"start_date"=> (float)$start_time, 
				"end_date"=> (float)$end_time,
				"page"=> (int)$i,
				"limit"=> 10) );
				//curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt( $ch1, CURLOPT_POSTFIELDS, $data );
				curl_setopt( $ch1, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
				# Return response instead of printing.
				curl_setopt( $ch1, CURLOPT_RETURNTRANSFER, true );
				# Send request.
				$res = curl_exec($ch1);
				curl_close($ch1);
				$res = json_decode($res, true);
				foreach($res['cdr'] as $val) {
					//pre($val);
					$this->db2->select('last_updated');
					$this->db2->where('cmiuid',$val['cmiuid']);
					$this->db2->from('tblcall_history');
					$query2 = $this->db2->get();
					if ( $query2->num_rows() > 0 ) {
						
					} else {
						//echo "11111"; exit;
						//$totData[] = $val;
						$totData = array();
						$totData['task_id'] = 326;
						$totData['cmiuid'] = $val['cmiuid'];
						$totData['duration'] = $val['duration'];
						$totData['agent'] = $val['agent'];
						$totData['billedsec'] = $val['billedsec'];
						$totData['rate'] = $val['rate'];
						$totData['record'] = 'false';
						$totData['con_name'] = $val['name'];
						$totData['call_from'] = $val['from'];
						$totData['call_to'] = $val['to'];
						$totData['time'] = $val['time'];
						$totData['last_updated'] = $end_time;
						
						$this->db2->insert('tblcall_history', $totData);
					}
				}
			}
			
			echo "done";
exit;
		}
	}

	public function webhook_tata_call_history() {
  		
		$post = json_decode(file_get_contents("php://input"), true);
		//pre($post);
		$dbname = explode('.leadforce.mobi',$_SERVER['HTTP_HOST']);	
			 
		$this->dynamicDB = array(	
			'hostname' => 'localhost',	
			'username' => 'root',	
			'password' => 'opc@345Pass',	
			'database' => 'dev_crm_'.$dbname[0],	
			'dbdriver' => 'mysqli',	
			'dbprefix' => 'tbl',	
			'pconnect' => FALSE,	
			'db_debug' => TRUE,	
			'char_set' => 'utf8',	
			'dbcollat' => 'utf8_general_ci'	
		);
		// $this->dynamicDB = array(
		// 	'hostname' => 'localhost',
		// 	'username' => 'root',
		// 	'password' => '',
		// 	'database' => 'perfexcrm',
		// 	'dbdriver' => 'mysqli',
		// 	'dbprefix' => 'tbl',
		// 	'pconnect' => FALSE,
		// 	'db_debug' => TRUE,
		// 	'char_set' => 'utf8',
		// 	'dbcollat' => 'utf8_general_ci'
		// );
		$this->db2 = $this->load->database($this->dynamicDB, TRUE); 
		// $totData = array();
		// $totData['call_info'] = $post['recording_url'];
		// $this->db2->insert('tbltata', $totData);

		if($post) {
			//APP Credentials
			$this->db2->select('*');
			$this->db2->from('tblcall_settings');
			$query = $this->db2->get();
			$row = $query->row_array();
			
			$to = substr($post['call_to_number'],2);
			$cmiuid = $post['uuid'];
			if($post['hangup_cause'] == 'disconnected_by_callee' || $post['hangup_cause'] == 'disconnected_by_caller') {
				$status = 'answered';
			} else {
				$status = $post['hangup_cause'];
			}
			$agent = $post['answered_agent']['id'];
			$time = strtotime($post['start_stamp']);
			$duration = $post['duration'];
			$answeredsec = $post['billsec'];

			$this->db2->where('call_to',$to);
			$this->db2->where('agent',$agent);
			$chistory = $this->db2->get(db_prefix() . 'call_history_flag')->row();

			$this->db2->where('agent_id',$agent);
			$fromno = $this->db2->get(db_prefix() . 'agents')->row();
			
			if($chistory) {
				$hid = $chistory->history_id;
				$this->db2->select('*');
				$this->db2->where('id',$hid);
				$this->db2->from('tblcall_history');
				$query = $this->db2->get();
				if ( $query->num_rows() > 0 ) {
					if($post['recording_url'] != '') {
						$record = 'true';
						$mp3 = $post['recording_url'];
						$exp_mp3 = explode('https://s3.ap-south-1.amazonaws.com/call-recording-tata/call-recording/',$mp3);
						$fileurl = explode('/',$exp_mp3[1]);
						$filename = $fileurl[1];
						//file_put_contents($_SERVER['DOCUMENT_ROOT']."/perfex_crm/uploads/recordings/".$filename, fopen($mp3, 'r'));
						file_put_contents($_SERVER['DOCUMENT_ROOT']."/uploads/recordings/".$filename, fopen($mp3, 'r'));
					} else {
						$filename = '';
						$record = 'false';
					}
					//echo "11111"; exit;
					//$totData[] = $val;
					$totData = array();
					$totData['cmiuid'] = $cmiuid;
					$totData['duration'] = $duration;
					$totData['agent'] = $agent;
					$totData['billedsec'] = $answeredsec;
					$totData['filename'] = $filename;
					$totData['record'] = $record;
					$totData['status'] = $status;
					$totData['call_to'] = $to;
					$totData['call_from'] = $fromno->phone;
					$totData['time'] = $time;
					//pre($totData);
					$this->db2->where('id',$hid);
					$this->db2->update('tblcall_history', $totData);

					if($status == 'answered') {
						$result = $query->row();
						$taskupdate = array();
						$taskupdate['status'] = 5;
						//pre($totData);
						$this->db2->where('id',$result->task_id);
						$this->db2->update('tbltasks', $taskupdate);
					}
					echo "Done"; exit;
				}
			}
		}

	}

	public function webhook_call_history() {

		if($json = json_decode(file_get_contents("php://input"), true)) {
			$post = $json;
		} else {
			$post = $_POST;
		}
		//	 pr($post);
		$dbname = explode('.leadforce.mobi',$_SERVER['HTTP_HOST']);	
			 
		$this->dynamicDB = array(	
			'hostname' => 'localhost',	
			'username' => 'root',	
			'password' => 'opc@345Pass',	
			'database' => 'dev_crm_'.$dbname[0],	
			'dbdriver' => 'mysqli',	
			'dbprefix' => 'tbl',	
			'pconnect' => FALSE,	
			'db_debug' => TRUE,	
			'char_set' => 'utf8',	
			'dbcollat' => 'utf8_general_ci'	
		);
		//echo $_SERVER['HTTP_HOST'];
		//exit;
		// $this->dynamicDB = array(
		// 	'hostname' => 'localhost',
		// 	'username' => 'root',
		// 	'password' => '',
		// 	'database' => 'perfexcrm',
		// 	'dbdriver' => 'mysqli',
		// 	'dbprefix' => 'tbl',
		// 	'pconnect' => FALSE,
		// 	'db_debug' => TRUE,
		// 	'char_set' => 'utf8',
		// 	'dbcollat' => 'utf8_general_ci'
		// );
		file_put_contents("test1.txt",'fsd');
		$this->db2 = $this->load->database($this->dynamicDB, TRUE); 
		if($post) {
			
			//APP Credentials
			$this->db2->select('*');
			$this->db2->from('tblcall_settings');
			$query = $this->db2->get();
			$row = $query->row_array();
			
			 file_put_contents("test1.txt",json_encode($post));
			$appid = $post['appid'];
			$to = $post['to'];
			$cmiuid = $post['cmiuuid'];
			$status = $post['status'];
			$agent = $post['user'];
			$time = $post['time'];
			$answeredsec = $post['answeredsec'];
			$record = $post['record'];
			$filename = $post['filename'];

			$this->db2->where('call_to',$to);
			$this->db2->where('agent',$agent);
			$chistory = $this->db2->get(db_prefix() . 'call_history_flag')->row();

			$this->db2->where('agent_id',$agent);
			$fromno = $this->db2->get(db_prefix() . 'agents')->row();
			
			if($chistory) {
				$hid = $chistory->history_id;
				$this->db2->select('*');
				$this->db2->where('id',$hid);
				$this->db2->from('tblcall_history');
				$query = $this->db2->get();
				if ( $query->num_rows() > 0 ) {
					if($row['recorder'] == 1) {
						$mp3 = 'https://piopiy.telecmi.com/v1/play?appid='.$appid.'&token='.$row['app_secret'].'&file='.$filename;
						//file_put_contents($_SERVER['DOCUMENT_ROOT']."/perfex_crm/uploads/recordings/".$filename, fopen($mp3, 'r'));
						//if(file_exists($mp3)){
							file_put_contents($_SERVER['DOCUMENT_ROOT']."/uploads/recordings/".$filename, fopen($mp3, 'r'));
						
					} else {
						$filename = '';
					}
					//echo "11111"; exit;
					//$totData[] = $val;
					$totData = array();
					$totData['cmiuid'] = $cmiuid;
					$totData['duration'] = $answeredsec;
					$totData['agent'] = $agent;
					$totData['billedsec'] = $answeredsec;
					$totData['filename'] = $filename;
					$totData['record'] = $record;
					$totData['status'] = $status;
					$totData['call_to'] = $to;
					$totData['call_from'] = $fromno->phone;
					$totData['time'] = $time;
					//pre($totData);
					$this->db2->where('id',$hid);
					$this->db2->update('tblcall_history', $totData);

					if($status == 'answered') {
						$result = $query->row();
						$taskupdate = array();
						$taskupdate['status'] = 5;
						//pre($totData);
						$this->db2->where('id',$result->task_id);
						$this->db2->update('tbltasks', $taskupdate);
					}
					echo "Done"; exit;
				}
			}
		}
		
	}
	
	public function updateCron() {
		// $totData = array();
		// $totData['status'] = 'Inserted';
		// $totData['last_updated'] = date('Y-m-d H:i:s');
		// $this->db->insert('tblcronjobs', $totData);
       // echo date("i");
		if (date("i") == "00") {
           
            
			$url = 'https://leadforce.mobi/admin/cronjob/store_local_mails';
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$rr = curl_exec($ch);
			//pr($rr);
			curl_close($ch);

			$companies = $this->base->getAll('tblcompany');
			foreach ($companies as $company) {
				$url = 'https://' . $company->shortcode . '.leadforce.mobi/admin/cronjob/store_local_mails';
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$rr = curl_exec($ch);
				pr($rr);
				curl_close($ch);
			}

 		}
		//reminder settting  cron job
		$url = 'https://leadforce.mobi/admin/cronjob/reminder_mail';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$rr = curl_exec($ch);
		//pr($rr);
		curl_close($ch);

		$companies = $this->base->getAll('tblcompany');
		foreach ($companies as $company) {
			$url = 'https://' . $company->shortcode . '.leadforce.mobi/admin/cronjob/reminder_mail';
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$rr = curl_exec($ch);
			pr($rr);
			curl_close($ch);
		}
		exit;
	}
	public function webhook_daffy_history() {
  		
		$post = $_GET;
		$dbname = explode('.leadforce.mobi',$_SERVER['HTTP_HOST']);
		 file_put_contents("test.txt",json_encode($post));
		/*$this->dynamicDB = array(
			'hostname' => 'localhost',
			'username' => 'root',
			'password' => '',
			'database' => 'crm_5',
			'dbdriver' => 'mysqli',
			'dbprefix' => 'tbl',
			'pconnect' => FALSE,
			'db_debug' => TRUE,
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci'
		);*/
		$this->dynamicDB = array(	
			'hostname' => 'localhost',	
			'username' => 'root',	
			'password' => 'opc@345Pass',	
			'database' => 'dev_crm_'.$dbname[0],	
			'dbdriver' => 'mysqli',	
			'dbprefix' => 'tbl',	
			'pconnect' => FALSE,	
			'db_debug' => TRUE,	
			'char_set' => 'utf8',	
			'dbcollat' => 'utf8_general_ci'	
		);
		$this->db2 = $this->load->database($this->dynamicDB, TRUE); 
		

		if($post) {
			
			$from = $post['from'];
			$to = $post['to'];
			$start_at = $post['start_at'];
			$duration = $post['duration'];
			$status = $post['status'];
			$recording_url = $post['recording_url'];
			$time = $start_at;

			$totData = array();
			/*$totData['duration'] = $duration; //duration
			$totData['agent'] = $from;
			$totData['billedsec'] = $duration; //duration
			$totData['filename'] = $recording_url;
			$totData['record'] = 1;
			$totData['status'] = $status; //status
			$totData['call_to'] = $to; //to
			$totData['call_from'] = $from; //from
			$totData['time'] = $time;
			//pre($totData);
			$this->db2->where('id',127);
			$this->db2->update('tblcall_history', $totData);
			echo "done";
			exit;*/


			//APP Credentials
			$this->db2->select('*');
			$this->db2->from('tblcall_settings');
			$query = $this->db2->get();
			$row = $query->row_array();
			
			$to = substr($post['to'],2);
			$cmiuid = $post['webhook_id'];
			// if($post['hangup_cause'] == 'disconnected_by_callee' || $post['hangup_cause'] == 'disconnected_by_caller') {
			// 	$status = 'answered';
			// } else {
			// 	$status = $post['hangup_cause'];
			// }
			$from = $post['from'];
			$time = strtotime($post['start_at']);
			$duration = $post['duration'];
			$answeredsec = $post['duration'];

			$this->db2->where('call_to',$to);
			$this->db2->where('agent',0);
			$chistory = $this->db2->get(db_prefix() . 'call_history_flag')->row();

			// $this->db2->where('phone',$from);
			// $fromno = $this->db2->get(db_prefix() . 'agents')->row();
			
			//if($chistory) {
				$hid = $chistory->history_id;
				$this->db2->select('*');
				$this->db2->where('id',$hid);
				$this->db2->from('tblcall_history');
				$query = $this->db2->get();
				//if ( $query->num_rows() > 0 ) {
					if($post['recording_url'] != '') {
						$record = 'true';
						 $mp3 = $post['recording_url'];
						$exp_mp3 = explode('https://s3.ap-south-1.amazonaws.com/call-recording-tata/call-recording/',$mp3);
						//$fileurl = explode('/',$exp_mp3[1]);
						$fileurl = explode('/',$mp3);
						//$filename = $fileurl[1];
						$filename = end($fileurl);
						if (str_contains($filename, '.mp3?')) { 
							$fileurl = explode('.mp3?',$filename);
							$filename  = $fileurl[0].'.mp3';
							$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
							if (str_contains($actual_link, '.mp3?')) { 
								$mp31 = explode($mp3,$actual_link);
								$mp31 = $mp31[1];
								$mp32 = explode('&duration',$mp31);
								$mp3  = $mp3.$mp32[0];

							}

						}
//echo '<br>'.FCPATH."uploads/recordings/".$filename;						//file_put_contents($_SERVER['DOCUMENT_ROOT']."/perfex_crm/uploads/recordings/".$filename, fopen($mp3, 'r'));
						file_put_contents(FCPATH."uploads/recordings/".$filename, fopen($mp3, 'r'));

						//file_put_contents($_SERVER['DOCUMENT_ROOT']."/uploads/recordings/".$filename, fopen($mp3, 'r'));
					} else {
						$filename = '';
						$record = 'false';
					}
					//echo "11111"; exit;
					//$totData[] = $val;
					$totData = array();
					$totData['cmiuid'] = $cmiuid; //webhook_id
					$totData['duration'] = $duration; //duration
					$totData['agent'] = $from;
					$totData['billedsec'] = $answeredsec; //duration
					$totData['filename'] = $filename;
					$totData['record'] = $record;
					if($status == 'COMPLETED'){
						$status = 'answered';
					}
					$totData['status'] = $status; //status
					$totData['call_to'] = $to; //to
					$totData['call_from'] = $from; //from
					$totData['time'] = $time;
					//pre($totData);
					$this->db2->where('id',$hid);
					$this->db2->update('tblcall_history', $totData);
					if($status == 'answered') {
						$result = $query->row();
						$taskupdate = array();
						$taskupdate['status'] = 5;
						//pre($totData);
						$this->db2->where('id',$result->task_id);
						$this->db2->update('tbltasks', $taskupdate);
					}
					echo "Done"; exit;
				//}
			//}
		}

	}
	public function reminder_mail(){
		//reminder_mail();
		$staffs = get_all_staffs();
		if(get_option('reminder_settings') == 'company'){
			$reminders = get_reminder_settings(null);
		}
		$ch_remind_status = get_option('remind_status');
		$ch_req_time =  date('Y-m-d H:i:s');
		$staffid = null;
		
		if( $ch_remind_status == 'enable' && get_option('reminder_settings') != 'user' && !empty($staffs) ){
			foreach($staffs as $staff1){
				$staffid = $staff1['staffid'];
				//$reminders = get_reminder_settings($staffid);
				$ch_remind_status = $reminders->remind_status;
				if($ch_remind_status == 'enable' && !empty($reminders->reminder_type)){
					$req_reminder_type = json_decode($reminders->reminder_type);
					if($req_reminder_type !=null && in_array("customer", $req_reminder_type) ){
						$cus_mail = explode(':',$reminders->customer_mail);
						if(!empty($cus_mail[0])){
							$cus_day = $cus_mail[0];
						}
						if(!empty($cus_mail[1])){
							$cus_hr = $cus_mail[1];
						}
						if(!empty($cus_mail[2])){
							$cus_min = $cus_mail[2];
						}
						$cur_date_time = date("Y-m-d H:i:0");
						if(!empty($cus_day)){
							$cur_date_time = date("Y-m-d H:i:s", strtotime("+".$cus_day." days"));
						}
						$timestamp = strtotime($cur_date_time);
						$req_time = $timestamp;
						if(!empty($cus_hr)){
							$req_time = $timestamp + ($cus_hr * 60 * 60);
						}
						$req_time = date('Y-m-d H:i:s',$req_time);
						$timestamp = strtotime($req_time);
						$req_time = $timestamp;
						if(!empty($cus_min)){
							$req_time  = $timestamp + ($cus_min * 60);
						}
						$req_time  = date('Y-m-d H:i:s',$req_time);
						$activities = check_customer_activity_admin($req_time,'customer mail',$reminders->customer_reminder);
						$cur_date = date("Y-m-d");
						$alert_msgs = check_get_msg('activity_reminder','english');
						if(!empty($activities) && !empty($alert_msgs)){
							foreach($activities as $act_1){
								$req_msg =get_act_msg($alert_msgs->message,$act_1);
								$req_nam1 =get_act_msg($alert_msgs->fromname,$act_1);
								
								remind_send_mail($act_1['c_mail'],$alert_msgs->subject,$req_msg,$req_nam1);
								ins_remind($staffid,'activity',$act_1['id'],$cur_date,'customer mail');
							}
						}
					}
					
				}
				break;
			}
			
		}
		if(!empty($staffs) && $ch_remind_status == 'enable'){
			foreach($staffs as $staff1){
				
				$staffid = $staff1['staffid'];
				if(get_option('reminder_settings') == 'user'){
					$reminders = get_reminder_settings($staffid);
					$ch_remind_status = $reminders->remind_status;
					if($ch_remind_status == 'enable' && !empty($reminders->reminder_type)){
						$req_reminder_type = json_decode($reminders->reminder_type);
						if($req_reminder_type !=null && in_array("customer", $req_reminder_type) ){
							$cus_mail = explode(':',$reminders->customer_mail);
							if(!empty($cus_mail[0])){
								$cus_day = $cus_mail[0];
							}
							if(!empty($cus_mail[1])){
								$cus_hr = $cus_mail[1];
							}
							if(!empty($cus_mail[2])){
								$cus_min = $cus_mail[2];
							}
							$cur_date_time = date("Y-m-d H:i:0");
							if(!empty($cus_day)){
								$cur_date_time = date("Y-m-d H:i:s", strtotime("+".$cus_day." days"));
							}
							$timestamp = strtotime($cur_date_time);
							$req_time = $timestamp;
							if(!empty($cus_hr)){
								$req_time = $timestamp + ($cus_hr * 60 * 60);
							}
							$req_time = date('Y-m-d H:i:s',$req_time);
							$timestamp = strtotime($req_time);
							$req_time = $timestamp;
							if(!empty($cus_min)){
								$req_time  = $timestamp + ($cus_min * 60);
							}
							$req_time  = date('Y-m-d H:i:s',$req_time);
							$activities = check_customer_activity($req_time,$staffid,'customer mail',$reminders->customer_reminder);
							$cur_date = date("Y-m-d");
							$alert_msgs = check_get_msg('activity_reminder','english');
							if(!empty($activities) && !empty($alert_msgs)){
								foreach($activities as $act_1){
									$req_msg =get_act_msg($alert_msgs->message,$act_1);
									$req_nam1 =get_act_msg($alert_msgs->fromname,$act_1);
									
									remind_send_mail($act_1['c_mail'],$alert_msgs->subject,$req_msg,$req_nam1);
									ins_remind($staffid,'activity',$act_1['id'],$cur_date,'customer mail');
								}
							}
						}
						
					}
				}
				if($ch_remind_status == 'enable'){
					$req_reminder_type = $act_notify = $pr_notify = $tar_notify = array();
					
					if(!empty($reminders->act_notify)){
						$act_notify = json_decode($reminders->act_notify);
					}
					if(!empty($reminders->pr_notify)){
						$pr_notify = json_decode($reminders->pr_notify);
					}
					if(!empty($reminders->tar_notify)){
						$tar_notify = json_decode($reminders->tar_notify);
					}
					if(!empty($reminders->reminder_type) && $reminders->reminder_type != null){
						$req_reminder_type = json_decode($reminders->reminder_type);
						if($req_reminder_type !=null && in_array("activity", $req_reminder_type) && in_array("send mail individual", $act_notify) && !empty($reminders->act_alert) ){
							
							$act_alert = explode(':',$reminders->act_alert);
							if(!empty($act_alert[0])){
								$act_day = $act_alert[0];
							}
							if(!empty($act_alert[1])){
								$act_hr = $act_alert[1];
							}
							if(!empty($act_alert[2])){
								$act_min = $act_alert[2];
							}
							$cur_date_time = date("Y-m-d H:i:0");
							if(!empty($act_day)){
								$cur_date_time = date("Y-m-d H:i:s", strtotime("+".$act_day." days"));
							}
							$timestamp = strtotime($cur_date_time);
							$req_time = $timestamp;
							if(!empty($act_hr)){
								$req_time = $timestamp + ($act_hr * 60 * 60);
							}
							$req_time = date('Y-m-d H:i:s',$req_time);
							$timestamp = strtotime($req_time);
							$req_time = $timestamp;
							if(!empty($act_min)){
								$req_time  = $timestamp + ($act_min * 60);
							}
							$req_time  = date('Y-m-d H:i:s',$req_time);
							//$staffid = get_staff_user_id();
							
							$activities = check_task_activity($req_time,$staffid,'mail individual');
							//$activities = check_activity_mail($req_time,$reminders->act_mail,$reminders->act_date_time,$reminders->act_day,$reminders->act_month,$staffid,'mail individual');
							
							$cur_date = date("Y-m-d");
							$alert_msgs = check_get_msg('activity_reminder','english');
							if(!empty($activities) && !empty($alert_msgs)){
								//$original_proposal = array();
								foreach($activities as $act_1){
									$req_msg =get_act_msg($alert_msgs->message,$act_1);
									$req_nam1 =get_act_msg($alert_msgs->fromname,$act_1);
									
									remind_send_mail($staff1['email'],$alert_msgs->subject,$req_msg,$req_nam1);
									ins_remind($staffid,'activity',$act_1['id'],$cur_date,'mail individual');
								}
							}
							
						}
						if($req_reminder_type !=null && in_array("activity", $req_reminder_type) && in_array("include summary email", $act_notify) ){
							
							$cur_date_time = $ch_req_time;
							$timestamp = strtotime($cur_date_time);
							$req_time  = date('Y-m-d H:i:s',$timestamp);
							//$staffid = get_staff_user_id();
							
							$activities = check_activity_mail($req_time,$reminders->act_mail,$reminders->act_date_time,$reminders->act_day,$reminders->act_month,$staffid,'mail');
							
							$cur_date = date("Y-m-d");
							$alert_msgs = check_get_msg('activity_reminder','english');
							$html_uh = '';
							$html_th = '';
							$html_u  = '';
							$html_t  = '';
							
							if(!empty($activities) && !empty($alert_msgs)){
								foreach($activities as $act_1){
									$req_nam1 =get_act_msg($alert_msgs->fromname,$act_1);
									$cur_deal = $cur_contact = $cur_type = '';
									if(!empty($act_1['rel_id'])){
										$res = $this->db->query("SELECT * FROM " . db_prefix() . "projects WHERE id = '".$act_1['rel_id']."'")->row();
										if(!empty($res->name)){
											$cur_deal = $res->name;
										}
									}
									if(!empty($act_1['contacts_id'])){
										$res = $this->db->query("SELECT * FROM " . db_prefix() . "contacts WHERE id = '".$act_1['contacts_id']."'")->row();
										if(!empty($res->firstname)){
											$cur_contact = $res->firstname.' '.$res->lastname;
										}
									}
									$res = $this->db->query("SELECT * FROM " . db_prefix() . "tasktype WHERE id = '".$act_1['tasktype']."'")->row();
									if(!empty($res->name)){
										$cur_type = $res->name;
									}
									$ch_st_date = date('Y-m-d',strtotime($act_1['startdate']));
									if(strtotime($cur_date) == strtotime($ch_st_date)){
										$hml_th = " <h2> Today Activities </h2>";
										if(empty($html_t)){
											
											$html_t .= '<table role="presentation" border="2" cellspacing="0" width="100%"><thead><tr><th>Activity Type</th><th>Subject</th><th>Deal</th><th>Start Date</th><th>Contact Person</th><th>Description</th></tr></thead><tbody>';
										}
										$html_t .= "<tr><td style='text-align:center;'>".$cur_type."</td>";
										$html_t .= "<td style='text-align:center;'>".$act_1['name']."</td>";
										$html_t .= "<td style='text-align:center;'>".$cur_deal."</td>";
										if(!empty($act_1['startdate'])){
											$html_t .= "<td style='text-align:center;'>".date('d-m-Y H:i',strtotime($act_1['startdate']))."</td>";
										}else{
											$html_t .= "<td style='text-align:center;'></td>";
										}
										$html_t .= "<td style='text-align:center;'>".$cur_contact."</td>";
										$html_t .= "<td style='text-align:center;'>".$act_1['description']."</td></tr>";
									}
									else if(strtotime($cur_date) < strtotime($ch_st_date)){
										$html_uh = " <br><h2> Upcoming Activities </h2>";
										if(empty($html_u)){
											$html_u .= '<table role="presentation" border="2" cellspacing="0" width="100%"><thead><tr><th>Activity Type</th<th>Subject</th><th>Deal</th><th>Start Date</th><th>Primary Contact Person</th><th>Description</th></tr></thead><tbody>';
										}
										$html_u .= "<tr><td style='text-align:center;'>".$cur_type."</td>";
										$html_u .= "<td style='text-align:center;'>".$act_1['name']."</td>";
										$html_u .= "<td style='text-align:center;'>".$cur_deal."</td>";
										if(!empty($act_1['startdate'])){
											$html_u .= "<td style='text-align:center;'>".date('d-m-Y H:i',strtotime($act_1['startdate']))."</td>";
										}else{
											$html_u .= "<td style='text-align:center;'></td>";
										}
										$html_u .= "<td style='text-align:center;'>".$cur_contact."</td>";
										$html_u .= "<td style='text-align:center;'>".$act_1['description']."</td></tr>";
									}
								}
								if(!empty($html_t)){
									$html_t .= "</tbody></table>";
								}
								if(!empty($html_u)){
									$html_u .= "</tbody></table>";
								}

								$req_head = '<!DOCTYPE html><html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="viewport" content="width=device-width"></head><body>';
								$req_foot = '</body></html>';
								$req_msg = $req_head.$html_uh.$html_u.$hml_th.$html_t.$req_foot;
								$req_subject = '';
								if($reminders->act_mail == 'daily'){
									$req_subject = "Today Activities";
								}
								else if($reminders->act_mail == 'weekly'){
									$req_subject = "This Week Activities";
								}
								else if($reminders->act_mail == 'monthly'){
									$req_subject = "Monthly Activities";
								}
								if(!empty($html_u) || !empty($html_t)){
									remind_send_mail($staff1['email'],$req_subject,$req_msg,$req_nam1);
								}
								ins_remind($staffid,'activity',$act_1['id'],$cur_date,'mail');
							}
							
						}
						if($req_reminder_type !=null && in_array("activity", $req_reminder_type) && in_array("show alert", $act_notify) && !empty($reminders->act_alert) ){
							$act_alert = explode(':',$reminders->act_alert);
							if(!empty($act_alert[0])){
								$act_day = $act_alert[0];
							}
							if(!empty($act_alert[1])){
								$act_hr = $act_alert[1];
							}
							if(!empty($act_alert[2])){
								$act_min = $act_alert[2];
							}
							$cur_date_time = date("Y-m-d H:i:0");
							if(!empty($act_day)){
								$cur_date_time = date("Y-m-d H:i:s", strtotime("+".$act_day." days"));
							}
							$timestamp = strtotime($cur_date_time);
							$req_time = $timestamp;
							if(!empty($act_hr)){
								$req_time = $timestamp + ($act_hr * 60 * 60);
							}
							$req_time = date('Y-m-d H:i:s',$req_time);
							$timestamp = strtotime($req_time);
							$req_time = $timestamp;
							if(!empty($act_min)){
								$req_time  = $timestamp + ($act_min * 60);
							}
							$req_time  = date('Y-m-d H:i:s',$req_time);
							$activities = check_task_activity($req_time,$staffid,'notification');
							$cur_date = date("Y-m-d");
							$alert_msgs = check_get_msg('activity_show_alert','english');
							
							if(!empty($activities) && !empty($alert_msgs)){
								foreach($activities as $act_1){
									$req_msg =get_act_msg($alert_msgs->message,$act_1);
									$additional_data =serialize(['<b>' . $act_1['name'] . '</b>',]);
									ins_remind_notification($req_msg,$staffid,$link,$ch_req_time,$additional_data);
									ins_remind($staffid,'activity',$act_1['id'],$cur_date,'notification');
								}
							}
						}
						if($req_reminder_type !=null && in_array("proposal", $req_reminder_type) && (in_array("include summary email", $pr_notify) || in_array("show alert", $pr_notify) )){
							$proposals = check_proposal_mail($reminders->pr_mail,$reminders->pr_date_time,$reminders->pr_day,$reminders->pr_month,$staffid);
						
							$cur_date = date("Y-m-d");
							if(!empty($proposals)){
								foreach($proposals as $proposal1){
									
									$alert_msgs = check_get_msg('proposal_reminder','english');	
									$req_msg =get_prop_msg($alert_msgs->message,$proposal1);
									$req_nam1 =get_prop_msg($alert_msgs->fromname,$proposal1);
									if(in_array("include summary email", $pr_notify)){
										remind_send_mail($staff1['email'],$alert_msgs->subject,$req_msg,$req_nam1);
										ins_remind($staffid,'proposal',$proposal1['id'],$cur_date,'mail');
									}
									if(in_array("show alert", $pr_notify)){
										$link = 'proposals/list_proposals/' . $proposal1['id'];
							
										$alert_msgs = check_get_msg('proposal_show_alert','english');
										$req_msg =get_prop_msg($alert_msgs->message,$proposal1);
										
										$additional_data =serialize(['<b>' . $proposal1['content'] . '</b>',]);
										ins_remind_notification($req_msg,$staffid,$link,$ch_req_time,$additional_data);
										ins_remind($staffid,'proposal',$proposal1['id'],$cur_date,'notification');
									}
								}
							}
						}
						if($req_reminder_type !=null && in_array("target", $req_reminder_type) && (in_array("include summary email", $tar_notify) || in_array("show alert", $tar_notify) )){
							//$staffid = get_staff_user_id();
							$targets = check_targets_mail($reminders->tar_mail,$reminders->tar_date_time,$reminders->tar_day,$reminders->tar_month,$staffid);
							$cur_date = date("Y-m-d");
							
							if(!empty($targets)){
								foreach($targets as $target1){
									$alert_msgs = check_get_msg('target_reminder','english');	
									$req_msg =get_tar_msg($alert_msgs->message,$target1);
									$req_nam1 =get_tar_msg($alert_msgs->fromname,$target1);
									
									if(in_array("include summary email", $tar_notify)){
										
										remind_send_mail($staff1['email'],$alert_msgs->subject,$req_msg,$req_nam1);		
										ins_remind($staffid,'target',$target1['id'],$cur_date,'mail');
									}
									if(in_array("show alert", $tar_notify)){
										
										if($target1['target_status']=='activity'){
											$link = 'target/activity';
										}
										if($target1['target_status']=='deal'){
											$link = 'target/deal';
										}
										$alert_msgs = check_get_msg('target_show_alert','english');
										$req_msg =get_tar_msg($alert_msgs->message,$target1);
										$additional_data =serialize(['<b>' . $target1['target_type'] . '</b>',]);
										ins_remind_notification($req_msg,$staffid,$link,$ch_req_time,$additional_data);
										$this->db->insert(db_prefix() . 'notifications', $ins_data);
										ins_remind($staffid,'target',$target1['id'],$cur_date,'notification');
									}
								}
							}
						}
						
					}
				}
			}
		}
	}
	
	public function indiamart_leads() {
		
		$dbname = explode('.leadforce.mobi',$_SERVER['HTTP_HOST']);	
			 
		$this->dynamicDB = array(	
			'hostname' => 'localhost',	
			'username' => 'root',	
			'password' => 'opc@345Pass',	
			'database' => 'dev_crm_'.$dbname[0],	
			'dbdriver' => 'mysqli',	
			'dbprefix' => 'tbl',	
			'pconnect' => FALSE,	
			'db_debug' => TRUE,	
			'char_set' => 'utf8',	
			'dbcollat' => 'utf8_general_ci'	
		);
		$this->db2 = $this->load->database($this->dynamicDB, TRUE); 

		$this->db2->where('slug', 'indiamart');
		$this->db2->select('*');
	    $this->db2->from('tblleads_sources');
		$query = $this->db2->get();
		if ( $query->num_rows() > 0 )
    	{
			$row = $query->row_array();
			$fvs = json_decode($row['fields']);
			//pre($name[0]);
			if($row['user_account'] != '' && $row['unique_key'] != '') {
				if($row['last_date']) {
					$start = $row['last_date'];
				} else {
					$start = date('d-M-Y');
				}
				$end = date('d-M-Y');

				$lastUpdate = array();
				$lastUpdate['last_date'] = $end;
				$this->db2->where('slug','indiamart');
				$this->db2->update('tblleads_sources', $lastUpdate);
				//echo 'https://mapi.indiamart.com/wservce/enquiry/listing/GLUSR_MOBILE/'.$row['user_account'].'/GLUSR_MOBILE_KEY/'.$row['unique_key'].'/Start_Time/'.$start.'/End_Time/'.$end.'/'; exit;
				$ch = curl_init('https://mapi.indiamart.com/wservce/enquiry/listing/GLUSR_MOBILE/'.$row['user_account'].'/GLUSR_MOBILE_KEY/'.$row['unique_key'].'/Start_Time/'.$start.'/End_Time/'.$end.'/');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$data = curl_exec($ch);
				curl_close($ch);
				$result = json_decode($data);
				//pre($result);
				foreach($result as $val) {
					if(isset($val->Error_Message)) {
						echo $val->Error_Message;
						exit;
					} else {
						$this->db2->where('query_id', $val->QUERY_ID);
						$this->db2->select('*');
						$this->db2->from('tblleads');
						$query1 = $this->db2->get();
						$lexist = $query1->row_array();
						if(empty($lexist)) {
							$insertData = array();
							$nameData = $cnameData = '';
							$name = explode(',',$fvs->name);
							$q = $name[0];
							//pre($val->$name[0]);
							if(isset($name[0])) {
								$n = $name[0];
								$nameData = $val->$n;
							}
							if(isset($name[1])) {
								$n = $name[1];
								$nameData = $nameData.'_'.$val->$n;
							}
							if(isset($name[2])) {
								$n = $name[2];
								$nameData = $nameData.'_'.$val->$n;
							}
							$cname = explode(',',$fvs->lead_company);
							if(isset($cname[0])) {
								$c = $cname[0];
								$cnameData = $val->$c;
							}
							if(isset($cname[1])) {
								$c = $cname[1];
								$cnameData = $cnameData.'_'.$val->$c;
							}
							if(isset($cname[2])) {
								$c = $cname[2];
								$cnameData = $cnameData.'_'.$val->$c;
							}
							$title = $fvs->title;
							$email = $fvs->email;
							$address = $fvs->address;
							$description = $fvs->description;
							$assigned = $fvs->assigned;
							$website = $fvs->website;
							$phonenumber = $fvs->phonenumber;
							$city = $fvs->city;
							$state = $fvs->state;
							$country = $fvs->country;
							$zip = $fvs->zip;

							if($val->$country) {
								$this->db2->where('iso2', $val->$country);
								$this->db2->select('*');
								$this->db2->from('tblcountries');
								$query2 = $this->db2->get();
								$cntry = $query2->row();
								$country = $cntry->country_id;
							}

							$insertData['name'] = $nameData;
							$insertData['company'] = $cnameData;
							$insertData['title'] = $val->$title;
							$insertData['email'] = $val->$email;
							$insertData['address'] = $val->$address;
							$insertData['description'] = nl2br($val->$description);
							$insertData['addedfrom'] = $assigned;
							$insertData['website'] = $val->$website;
							$insertData['phonenumber'] = $val->$phonenumber;
							$insertData['city'] = $val->$city;
							$insertData['state'] = $val->$state;
							$insertData['country'] = $country;
							$insertData['zip'] = $val->$zip;
							$insertData['query_id'] = $val->QUERY_ID;
							$insertData['assigned'] = $assigned;
							$insertData['source'] = $row['id'];

							$insertData['dateadded'] = date('Y-m-d H:i:s');
							$custFields = array();
							if(!empty($fvs->custom_fields->leads) && isset($fvs->custom_fields->leads)) {
								foreach($fvs->custom_fields->leads as $fkey => $fval) {
									$custFields['leads'][$fkey] = $val->$fval;
								}
							}
							// pr($insertData);
							// pr($custFields);

							$this->db2->insert(db_prefix() . 'leads', $insertData);
							$insert_id = $this->db2->insert_id();
							if ($insert_id) {
								// log_activity('New Lead Added [ID: ' . $insert_id . '] from IndiaMART');
								// $this->log_lead_activity($insert_id, 'not_lead_activity_created');
					
								if (isset($custFields)) {
									//handle_custom_fields_post($insert_id, $custFields);
									foreach ($custFields['leads'] as $field_id => $field_value) {
										if ($field_value != '') {
											$this->db2->insert(db_prefix() . 'customfieldsvalues', [
												'relid'   => $insert_id,
												'fieldid' => $field_id,
												'fieldto' => 'leads',
												'value'   => $field_value,
											]);
										}
										//echo $field_id.'----------';
									}
								}
					
								//$this->lead_assigned_member_notification($insert_id, $val->$assigned);
								//hooks()->do_action('lead_created', $insert_id);
					
								echo  $insert_id;
							}
						}
					}
				}
				exit;
				
			} else {
				echo "Please use valid unique key & mobile number.";
				exit;
			}
		}
	}
	
	public function webhook_knowlarity_history()
	{
		$post = json_decode(file_get_contents("php://input"), true);
		if(isset($_GET['printdata'])){
			$myfile = fopen("knowlarity_callback_log.txt", "r") or die("Unable to open file!");
			echo fread($myfile,filesize("knowlarity_callback_log.txt"));
			fclose($myfile);
		}else{
			$myfile = fopen("knowlarity_callback_log.txt", "a") or die("Unable to open file!");
			$txt = json_encode($post)."\n";
			fwrite($myfile, $txt);
			fclose($myfile);
			echo 'callback loged successfully';
		}
		
		return ;
		$this->knowlarity_model->webhookHandler($post);
	}
}