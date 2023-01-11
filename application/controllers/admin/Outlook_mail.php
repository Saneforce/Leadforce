<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Outlook_mail extends AdminController
{
    public function __construct()
    {
        parent::__construct();
		$this->load->model('projects_model');
        $this->load->model('tasktype_model');
		$this->load->library('session');
		unset($_SESSION['pipelines']);
        unset($_SESSION['member']);
        unset($_SESSION['gsearch']);
    }
	public function index(){
		$staffid = get_staff_user_id();
		$cur_token = get_outlook_token();
		//pre($cur_token);
		$token		= $cur_token->refresh_token;
		$user_email = $cur_token->email;
		if(!empty($cur_token) && empty($token)){
			redirect(site_url().'admin/company_mail/configure_email');
			exit;
		}
		if (isset($_GET["code"])) {
			$outlook_data = outlook_credential();
			$scopes 	= $outlook_data['scopes'];
			$token_request_data = array (
				"grant_type" => "authorization_code",
				"code" => $_GET["code"],
				"redirect_uri" => $outlook_data['redirect_uri'],
				"scope" => implode(" ", $scopes),
				"client_id" => $outlook_data['client_id'],
				"client_secret" => $outlook_data["client_secret"]
			);
			
			$body = http_build_query($token_request_data);
			$response = runCurl($outlook_data["authority"].$outlook_data["token_url"], $body);
			$response = json_decode($response);
			$access_token	 = $response->access_token;
			$refresh_token 	 = $response->refresh_token;
			$headers = array(
				"User-Agent: php-tutorial/1.0",
				"Authorization: Bearer ".$access_token,
				"Accept: application/json",
				"client-request-id: ".makeGuid(),
				"return-client-request-id: true"
			);
			
			$outlookApiUrl = $outlook_data["api_url"] . "/Me";
			$response = runCurl($outlookApiUrl, null, $headers);
			$response = explode("\n", trim($response));
			$response = $response[count($response) - 1];
			$response = json_decode($response);
			
			if(!empty($response)){
				$user_email = $response->EmailAddress;
				store_outlook_token($user_email,$access_token,$refresh_token);
				redirect(site_url().'admin/outlook_mail/connect_outlook');
			}
			else{
				$outlook_data = outlook_credential();
				$client_id 		= $outlook_data['client_id'];
				$redirect_uri 	= $outlook_data['redirect_uri'];
				$scopes 		= $outlook_data['scopes'];
				$url = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?client_id='.$client_id."&redirect_uri=".$redirect_uri."&response_type=code&scope=".implode(" ", $scopes);
				redirect($url);
			}
		}
		else{
			$cur_token = get_outlook_token();
			//pre($cur_token);
			$token		= $cur_token->refresh_token;
			$user_email = $cur_token->email;
			$check_data = refresh_token($user_email,$token);
			$cur_token = get_outlook_token();
			$token		= $cur_token->token;
			//pre($check_data);
			if(empty($check_data) || empty($token)){
				$outlook_data = outlook_credential();
				$client_id 		= $outlook_data['client_id'];
				$redirect_uri 	= $outlook_data['redirect_uri'];
				$scopes 		= $outlook_data['scopes'];
				$url = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?client_id='.$client_id."&redirect_uri=".$redirect_uri."&response_type=code&scope=".implode(" ", $scopes);
				redirect($url);
			}
			else{
				redirect(site_url().'admin/outlook_mail/connect_outlook');
			}
		}
		
	}
	public function update_message($cur_val){
		
		$outlook_data = outlook_credential();
		$cur_token = get_outlook_token();
		$token		= $cur_token->token;
		$user_email = $cur_token->email;
		
		$msgs = $_REQUEST['mails'];
		if(!empty($msgs)){
			foreach($msgs as $msg_id){
				$headers = array(
					"User-Agent: php-tutorial/1.0",
					"Authorization: Bearer ".$token,
					"Accept: application/json",
					"client-request-id: ".makeGuid(),
					"return-client-request-id: true",
					"Content-type: application/json",
					"X-AnchorMailbox: ". $user_email
				);
				$request = array(
					"IsRead" => $cur_val,
					
				);
				$request = json_encode($request);
				$outlookApiUrl = $outlook_data["api_url"] . "/me/messages/".$msg_id;
				$response = runCurl($outlookApiUrl,$request,  $headers,'PATCH');
			}
		}
		echo '1';
		
	}
	public function delete_outlook_message(){
		
		$outlook_data = outlook_credential();
		$cur_token = get_outlook_token();
		$token		= $cur_token->token;
		$user_email = $cur_token->email;
		
		$msgs = $_REQUEST['mails'];
		if(!empty($msgs)){
			foreach($msgs as $msg_id){
				$headers = array(
					"User-Agent: php-tutorial/1.0",
					"Authorization: Bearer ".$token,
					"Accept: application/json",
					"client-request-id: ".makeGuid(),
					"return-client-request-id: true",
					"X-AnchorMailbox: ". $user_email
				);
				
				$request = array(
					"Message" => $msg_id
				);

				$outlookApiUrl = $outlook_data["api_url"] . "/me/messages/".$msg_id;
				$response = runCurl($outlookApiUrl,$request,  $headers,'DELETE');
			}
		}
		echo '1';
		
	}
	public function get_outlook_message(){
		$msg_id = $_REQUEST['msg_id'];
		
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
		
		
		$outlookApiUrl = $outlook_data["api_url"] . "/me/messages/".$msg_id;
		$response = runCurl($outlookApiUrl, null, $headers);
		$response = explode("\n", trim($response));
		$response = $response[count($response) - 1];
		$response = json_decode($response, true);
		//pre($response);
		$mailList = array();
		$mailList['subject'] 		= $response['Subject'];
		$mailList['message'] 		= $response['Body']['Content'];
		$mailList['from_address']	= $response['From']['EmailAddress']['Address'];
		$mailList['to_address'] 	= $response['To']['EmailAddress']['Address'];
		$mailList['msg_id'] 		= $msg_id;
		$output = '';
		$add_content = "'".$_REQUEST['msg_id']."'";
		$output .= '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h4 class="modal-title"><i class="fa fa-envelope"></i> '.$response['Subject'].'</h4></div>';
		$output .= '<div class="modal-body"><div class="email-app"><main class="message"><div class="details">';


		if($_REQUEST['folder'] == 'Sent Items'){
			$from_address = '';
			if(!empty($response['ToRecipients'])){
				foreach($response['ToRecipients'] as $to_mail1){
					$from_address .= $to_mail1['EmailAddress']['Address'].', ';
				}
				$from_address = rtrim($from_address,", ");
			}
		}else{
			$from_address =$response['From']['EmailAddress']['Address'];
		}
		$this->db->where('message_id',$response['Id']);
		if(!$this->db->get(db_prefix().'localmailstorage')->row()){

			$linked_deals_leads =render_deal_lead_list_by_email($from_address);

			$output .='
				<div class="row" id="linktowrapper">
					<div class="col-md-12">
						<h5>Link to Deal or Lead</h5>
						<div class="form-inline">
							<input type="hidden" id="linktouid" value="'.$response['Id'].'" >';
							if($linked_deals_leads){
								$output .='<div class="form-group mb-2" style="width:40%">
									<select class="selectpicker" data-none-selected-text="Select Deal or Lead"  name="linkto_rel_id" id="linkto_rel_id" data-width="100%" data-live-search="true">'.$linked_deals_leads.'</select>
								</div>
								<button class="btn btn-info" id="linkto_rel_id_submit" type="button">Link with existing</button>
								OR  ';
							}
							
							$output .='<a href="#" onclick="init_lead(0,false,'.$add_content.'); return false;" class="btn btn-info">New Lead</a>
						</div>
					</div>
					<div class="col-md-4">
						<div class="text-right">
						</div>
					</div>
				</div>
				<hr>
			';
		}

		$output .='<div id="emailViewer">
			<div class="emailViewerSubject">
				<h3>'.$response['Subject'].'</h3>
			</div>
			<div class="emailViewerMeta">
				<div class="row">
					<div class="col-md-6">
						<p class="no-margin" style="font-size: 13px;">From : <a>'.$response['From']['EmailAddress']['Address'].'</a></p>
						<p class="no-margin" style="font-size: 13px;">To : <a>'.$response['ToRecipients'][0]['EmailAddress']['Address'].'</a></p>
						<p class="no-margin" style="font-size: 13px;">'.date("d-M-Y H:i A",$response['udate']).'</p>
					</div>
					<div class="col-md-6">';
						$reply ='<div class="button-group">
							<button type="button" data-toggle="tooltip" data-original-title="Forward" class="btn btn-default pull-right" data-toggle="modal" data-target="#forward-modal" onclick="add_content('.$add_content.')"><i class="fa fa-share" aria-hidden="true"></i></button>
							<button type="button" data-toggle="tooltip" data-original-title="Reply" class="btn btn-default pull-right" data-toggle="modal" data-target="#reply-modal" onclick="add_to('.$add_content.')" style="margin-right:5px;"><i class="fa fa-reply" ></i></button>
							<button type="button" data-toggle="tooltip" data-original-title="Reply All" class="btn btn-default pull-right" data-toggle="modal" data-target="#reply-modal" onclick="add_reply_all('.$add_content.')" style="margin-right:5px;"><i class="fa fa-reply-all" aria-hidden="true"></i></button>
						</div>';
					$output .='</div>
				</div>
			</div>';
			$output .='<div style="margin-top:10px">';
			$j1 = 0;
			if(!empty($response['HasAttachments'] && $response['HasAttachments'] ==1)){
				$headers = array(
					"User-Agent: php-tutorial/1.0",
					"Authorization: Bearer ".$token,
					"Accept: application/json",
					"client-request-id: ".makeGuid(),
					"return-client-request-id: true",
					"X-AnchorMailbox: ". $user_email
				);
				$outlookApiUrl = $outlook_data["api_url"] . "/me/Messages/".$msg_id."/attachments";
				$response1 = runCurl($outlookApiUrl, null, $headers);
				$response1 = explode("\n", trim($response1));
				$response1 = $response1[count($response1) - 1];
				$response1 = json_decode($response1, true);
				
				$j = count($response1["value"]);
				foreach($response1["value"] as $attachement12){
					
					$name = "'".$attachement12['Name']."'";
					$content = "'".$attachement12['ContentBytes']."'";
					$downoad_url = admin_url('outlook_mail/download_attachment_single?name='.$attachement12['Name'].'&content='.$attachement12["ContentId"].'&msg_id='.$msg_id);
					$output .= '<div class="btn btn-default pull-left" style="margin-right:10px;"><a href="'.$downoad_url.'" onclick="download_single('.$name.','.$content.')">'.$attachement12['Name'].'</a></div>';
					$j1++;
				}
			}
			if($j1>1){
				$downoad_url = admin_url('outlook_mail/outlook_all_download_attachment?msg_id='.$msg_id);
					
				$output .= '<div class="btn btn-default pull-left" style="margin-right:10px;"><a href="'.$downoad_url.'">Download All</a></div>';
			}
			$output .='</div>';
			$output .='<div class="emailViewerBody" style="margin-top:20px">'.nl2br($response['Body']['Content']).'</div>';

		$output .='</div>';
		$output .= '</div>';
		$output .='
		<script>
			$("#linkto_rel_id").selectpicker();
			$("#linkto_rel_id_submit").click(function(){
				var linkto =$("#linkto_rel_id").val();
				var linktouid =$("#linktouid").val();
				$.ajax({
					url: admin_url+"company_mail/linkemail",
					type: "POST",
					data: {linkto:linkto,linktouid:linktouid},
					dataType: "json",
					success: function(data) {
						if(data.success){
							$("#linktowrapper").remove();
							alert_float("success", data.msg);
						}else{
							alert_float("danger", data.msg);
						}
						
					}               
				});
			})
		</script>
		';
		
		$mailList['body'] = $output;
		echo json_encode($mailList);
		$request = array(
			"IsRead" => true,
			
		);
		$request = json_encode($request);
		$headers = array(
					"User-Agent: php-tutorial/1.0",
					"Authorization: Bearer ".$token,
					"Accept: application/json",
					"client-request-id: ".makeGuid(),
					"return-client-request-id: true",
					"Content-type: application/json",
					"X-AnchorMailbox: ". $user_email
				);
		$outlookApiUrl = $outlook_data["api_url"] . "/me/messages/".$msg_id;
		$response2 = runCurl($outlookApiUrl,$request,  $headers,'PATCH');
		
	}
	public function download_attachment_single(){
		$msg_id 	= $_REQUEST['msg_id'];
		$name	 	= $_REQUEST['name'];
		$content_id	= $_REQUEST['content'];
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
		
		foreach ($response["value"] as $attachment) {
			//pre($attachment);
			if($attachment["ContentId"] == $content_id){
				$req_val['value'][0] = $attachment;
				outlook_download_all_file($req_val);
			}
		}
	}
	public function download_attachment_single_project($id,$content_id){
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
		$project = $this->projects_model->storage_message($id);
		$msg_id = $project->message_id;
		$attachments = json_decode($project->attachment_id,TRUE);
		$outlookApiUrl = $outlook_data["api_url"] . "/me/Messages/".$msg_id."/attachments";
		$response = runCurl($outlookApiUrl, null, $headers);
		$response = explode("\n", trim($response));
		$response = $response[count($response) - 1];
		$response = json_decode($response, true);
		
		foreach ($response["value"] as $attachment1) {
			if($attachments[$content_id] == $attachment1['Id']){
				$req_val['value'][0] = $attachment1;
				outlook_download_all_file($req_val);
			}
		}
		
	}
	public function outlook_all_download_attachment(){
		$msg_id = $_REQUEST['msg_id'];
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
		outlook_download_all_file($response);
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
	public function send(){
		$outlook_data = outlook_credential();
		$cur_token = get_outlook_token();
		$token		= $cur_token->token;
		$user_email = $cur_token->email;
		$staff_id = get_staff_user_id();
		$redirect_url = site_url().'admin/tasks';
		$redirect_url1 = site_url().'admin/outlook_mail/connect_outlook';
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
	   
		if(get_option('deal_map') != 'if more than one open deal – allow to map manually'){
			$ch_project_id = get_deal_id($this->input->post('toemail'),get_option('deal_map'));
		}
		else{
			$ch_project_id = $_REQUEST['deal_id'];
		}
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
				die("Need email address to send email");
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
				sleep(3);
				$messages = $this->last_sent_item();
				
				if (!empty($attachments)) {
					$list_attachment = $this->list_attachment($messages['Id']);
					$source_from2 = array_column($list_attachment, 'Id'); 
					//$source_from2['ContentId'] = array_column($list_attachment, 'ContentId'); 
					//$source_from2['ContentBytes'] = array_column($list_attachment, 'ContentBytes'); 
					//$source_from2['Name'] = array_column($list_attachment, 'Name'); 
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
			
		}
	}
	public function reply() {
		$outlook_data = outlook_credential();
		$cur_token = get_outlook_token();
		$token		= $cur_token->token;
		$user_email = $cur_token->email;
		$staff_id = get_staff_user_id();
		$redirect_url1 = site_url().'admin/outlook_mail/connect_outlook';
		
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
		//if ($contacts) {
			$this->db->where('contacts_id', $contacts->id);
			$this->db->limit(1);
			$project = $this->db->get(db_prefix() . 'project_contacts')->row();
			//if ($project) {
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
				redirect($redirect_url1);
		}else{
			 $message       = 'Please enter message';
			set_alert('warning', $message);
			redirect($redirect_url1);
		}
    }
	public function forward() {
		$outlook_data = outlook_credential();
		$cur_token = get_outlook_token();
		$token		= $cur_token->token;
		$user_email = $cur_token->email;
		$staff_id = get_staff_user_id();
		$redirect_url1 = site_url().'admin/outlook_mail/connect_outlook';
		
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
		//if ($contacts) {
			$this->db->where('contacts_id', $contacts->id);
			$this->db->limit(1);
			$project = $this->db->get(db_prefix() . 'project_contacts')->row();
			//if ($project) {
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
	public function list_outlook($pagno){
		$mailList = [];
	//	$cur_folder = ucwords(strtolower($_POST['folder']));
		$outlook_data = outlook_credential();
		$cur_token = get_outlook_token();
		$token		= $cur_token->token;
		$refresh_token		= $cur_token->refresh_token;
		$user_email = $cur_token->email;
		$check_data = refresh_token($user_email,$refresh_token);
		$req_folders = array();
		if(!empty($cur_token->folders)){
			$req_folders = json_decode($cur_token->folders);
		}
		$headers = array(
			"User-Agent: php-tutorial/1.0",
			"Authorization: Bearer ".$token,
			"Accept: application/json",
			"client-request-id: ".makeGuid(),
			"return-client-request-id: true",
			"X-AnchorMailbox: ". $user_email
		);
		$top = 10;
		if(!empty($pagno)){
			//$pagno = $pagno -1;
		}
		$skip = !empty($pagno) ? ((intval($pagno-1)*$top)) : 0;
		if(!empty($_REQUEST['search_txt'])){
			$search = array (
				// Only return selected fields
				"\$select" => "Subject,ReceivedDateTime,Sender,From,ToRecipients,HasAttachments,BodyPreview,isRead",
				// Sort by ReceivedDateTime, newest first
				//"\$orderby" => "ReceivedDateTime DESC",
				//"\$filter" => "ReceivedDateTime gt 2022-06-14T00:00:00Z",				// Return at most n results
				//"\$filter"=>"startswith(Subject,'".$_REQUEST['search_txt']."') ",				// Return at most n results
				//"\$top" => $top,
				//"\$skip" => $skip,
				"\$search"=>$_REQUEST['search_txt'],
			//	"\$orderby" => "ReceivedDateTime DESC",
			);
		}else{
			$req_sort_val = ($_REQUEST['sort_val'] ==1) ? 'DESC':'ASC';
			if($_REQUEST['sort_option'] == 'From'){
				$_REQUEST['sort_option'] ='from/emailAddress/address';
			}
			if($_REQUEST['sort_option'] == 'To'){
				$_REQUEST['sort_option'] ='ReceivedDateTime';
			}
			
			$search = array (
				// Only return selected fields
				"\$select" => "Subject,ReceivedDateTime,Sender,From,ToRecipients,HasAttachments,BodyPreview,isRead",
				// Sort by ReceivedDateTime, newest first
				"\$orderby" => "ReceivedDateTime ".$req_sort_val." , ".$_REQUEST['sort_option']." ".$req_sort_val,
				//"\$orderby" => $_REQUEST['sort_option']." ".$req_sort_val.", ReceivedDateTime ".$req_sort_val,
				// Return at most n results
				"\$top" => $top,
				//"\$filter" => "ReceivedDateTime gt 2022-06-07T00:00:00Z",
				"\$skip" => $skip,
				//"\$count" => true
			);

		}
		$req_day = ''; 
			
		if(empty($_REQUEST['search_txt'])){
			if($cur_token->filter_mail == 'yesterday'){
				$req_day = date("Y-m-d", strtotime("-1 days")).'T00:00:00Z';
			}
			if($cur_token->filter_mail == 'week'){
				$req_day = date("Y-m-d", strtotime("-7 days")).'T00:00:00Z';
			}
			else if($cur_token->filter_mail == 'month'){
				$req_day = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) ).'T00:00:00Z';
			}
			else if($cur_token->filter_mail == '3 months'){
				$req_day = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-3 month" ) ).'T00:00:00Z';
			}
			else if($cur_token->filter_mail == '6 months'){
				$req_day = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-6 month" ) ).'T00:00:00Z';
			}
			else if($cur_token->filter_mail == 'year'){
				$req_day = date("Y-m-d", strtotime("-365 days")).'T00:00:00Z';
			}
			$search['$filter'] = "ReceivedDateTime gt ".$req_day."";
		}
		//pre($search);
		$outlookApiUrl = $outlook_data["api_url"] . "/me/mailFolders" ;
		$response = runCurl($outlookApiUrl, null, $headers);
		$response = explode("\n", trim($response));
		$response = $response[count($response) - 1];
		$response = json_decode($response, true);
		//$folders =  $search = array(); 
		$folders =   array(); 
		$mailList['folder_values'] = '<ul class="nav nav-pills nav-stacked"><li class="header">Folders</li>';
		if(!empty($response['value'])){
			$i = 0;
			foreach($response['value'] as $folder1){
				$icon = ucwords(strtolower($folder1['DisplayName']));
				$folders[] = $folder1['DisplayName'];
				if($icon == 'Inbox'){
					$faicon = 'fa-inbox';
				}
				else if($icon == 'All Mail'){
					$faicon = 'fa-inbox';
				}
				else if($icon == 'Drafts'){
					$faicon = 'fa-pencil-square-o';
				}
				else if($icon == 'Important'){
					$faicon = 'fa-bookmark';
				}
				else if($icon == 'Sent Mail'){
					$faicon = 'fa-mail-forward';
				}
				else if($icon == 'Spam'){
					$faicon = 'fa-folder';
				}
				else if($icon == 'Starred'){
					$faicon = 'fa-star';
				}
				else if($icon == 'Trash'){
					$faicon = 'fa-trash';
				}
				else if($icon == 'Bin'){
					$faicon = 'fa-trash';
				}
				else {
					$icon = $folder1['DisplayName'];
					$faicon = 'fa-folder';
				}
				$name = $folder1['DisplayName'];
				
				if($icon == $_REQUEST['folder']){
					if(empty($_REQUEST['search_txt'])){
						$search['$top'] = 1000000000;
						$search['$skip'] = 0;
					}
					$outlookApiUrl12 = $outlook_data["api_url"] . "/me/mailFolders/".$folder1['Id']."/messages?". http_build_query($search);
					$response12 = runCurl($outlookApiUrl12, null, $headers);
					$response12 = explode("\n", trim($response12));
					$response12 = $response12[count($response12) - 1];
					$response12 = json_decode($response12, true);
					if(empty($_REQUEST['search_txt'])){
						$search['$top'] = $top;
						$search['$skip'] = $skip;
					}
					$mailList['tot_cnt']	= 0;
					if(!empty($response12['value'])){
						$mailList['tot_cnt']	= count($response12['value']);
					}
					$outlookApiUrl1 = $outlook_data["api_url"] . "/me/mailFolders/".$folder1['Id']."/messages?". http_build_query($search);
					$response1 = runCurl($outlookApiUrl1, null, $headers);
					$response1 = explode("\n", trim($response1));
					$response1 = $response1[count($response1) - 1];
					$response1 = json_decode($response1, true);
					
					
					$class = 'active';
				} else {
					$class = '';
				}
				$req_icon = "'".$name."'";
				if($cur_token->folder_type == 'all' || in_array($icon,$req_folders)){
					$mailList['folder_values'] .= '<li class="'.$class.'"><a href="#" id="'.strtolower(str_replace(' ','-',$icon)).'" onClick="getMailList('.$req_icon.');"><i class="fa '.$faicon.'"></i> '.$folder1['DisplayName'].'</a></li>';
				}
				$i++;
			}
			$mailList['folder_values'] .= '</ul>';
			$output = '';
			//pre($response1['value']);
			if(!empty($response1['value']) && count($response1["value"])){
				foreach ($response1["value"] as $mail) {
					if($mail['IsRead'] ==false){
						$output .= '<tr class="'.$mail['Id'].'_mail_row unread_col_col"><td><input type="checkbox" name="mails[]" class="check_mail" onclick="check_header()" value="'.$mail['Id'].'"></td>';
					}else{
						$output .= '<tr class="'.$mail['Id'].'_mail_row read_col_col"><td><input type="checkbox" name="mails[]" class="check_mail" onclick="check_header()" value="'.$mail['Id'].'"></td>';
					}
					$req_mail_id = "'".$mail['Id']."'";
					if($_REQUEST['folder'] != 'Sent Items' && $_REQUEST['folder'] != 'Drafts'){
						$fromname = $mail["From"]["EmailAddress"]["Address"];
						$output .= '<td class="name"><a href="#" onClick="getMessage('.$req_mail_id.');">'.$fromname.'</a></td>';
					}
					else{
						$to_address = '';
						if(!empty($mail['ToRecipients'])){
							foreach($mail['ToRecipients'] as $to_mail1){
								$to_address .= $to_mail1['EmailAddress']['Address'].', ';
							}
							$to_address = rtrim($to_address,", ");
						}
						$toname = $mail["Sender"]["EmailAddress"]["Address"];
						$output .= '<td class="name"><a href="#" onClick="getMessage('.$req_mail_id.');">'.$to_address.'</a></td>';
					}

					$this->db->where('message_id',$mail['Id']);
					

					$local_mail =$this->db->get(db_prefix().'localmailstorage')->row();
					$connect_rel_data ='';
					
					if($local_mail){
						if($local_mail->deal_id){
							$this->db->where('deleted_status',0);
							$this->db->where('id',$local_mail->deal_id);
							$deal =$this->db->get(db_prefix().'projects')->row();
							$connect_rel_data ='<a href="#" onClick="updatedeal('.$mail['Id'].');">'.htmlentities($deal->name).' (Deal)</a>';
						}elseif($local_mail->lead_id){
							$this->db->where('deleted_status',0);
							$this->db->where('id',$local_mail->lead_id);
							$lead =$this->db->get(db_prefix().'leads')->row();
							$connect_rel_data ='<a target="_blank" href="'.admin_url('leads/lead/'.$lead->id).'">'.htmlentities($lead->name).' (Lead)</a>';
						}
					}

					$output .= '<td class="subject"><a href="#" onClick="getMessage('.$req_mail_id.');">'.substr($mail['Subject'],0,30).'</a></td>';
					$output .= '<td class="subject">'.$connect_rel_data.'</td>';
					if(!empty($mail['HasAttachments']) && $mail["HasAttachments"] == 1){
						$output .= '<td><a href="'.admin_url('outlook_mail/outlook_all_download_attachment?msg_id='.$mail['Id']).'" ><i class="fa fa-paperclip" aria-hidden="true"></i></a></td>';
					}else{
						$output .= '<td></td>';
					}
					$output .= '<td class="time"><a href="#" onClick="getMessage('.$req_mail_id.');">'.date("D, d M Y h:i A",strtotime($mail['ReceivedDateTime'])).'</a></td></tr>';
					
				}
			}
			else{
				$output .= '<tr><td colspan="6" style="text-align:center">No Records Found</td></tr>';
			}
			$mailList['table'] = $output;
			$mailList['field'] = '<input type="hidden" id="folder" value="'.$_REQUEST['folder'].'">';
			$data['folders'] = $mailList;
			$this->load->library('pagination');
			$row_per_page = $top;
			$allcount = $mailList['tot_cnt'];
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
			$config['num_links'] = 5;
	 
			$this->pagination->initialize($config);
	 
			$data['pagination'] = $this->pagination->create_links();
			//echo '<pre>';print_r($data);exit;
			echo json_encode($data);
		}
		else{
			$client_id 		= $outlook_data['client_id'];
			$redirect_uri 	= $outlook_data['redirect_uri'];
			$scopes 		= $outlook_data['scopes'];
			$url = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?client_id='.$client_id."&redirect_uri=".$redirect_uri."&response_type=code&scope=".implode(" ", $scopes);
			redirect($url);
		}
	}
	public function connect_outlook(){
		$data = array();
		$data['title'] ='Outlook';
		$this->load->view('admin/outlook/emailmanagement', $data);
	}
	public function connect_mail() {
		extract($_POST);
		$cur_token = get_outlook_token();
		//pre($cur_token);
		if(!empty($cur_token)){
			$token		= $cur_token->refresh_token;
			$user_email = $cur_token->email;
			store_outlook_mail();
			if($user_email == $connect_email){
				$check_data = refresh_token($user_email,$token);
				$cur_token = get_outlook_token();
				$token		= $cur_token->token;
				
				if(!empty($check_data) && !empty($token)){
					$url = site_url().'admin/outlook_mail/connect_outlook';
					$response = array('code'=>'success','message'=>$url);
					echo json_encode($response);
					exit;
				}
			}
		}
		list($local, $domain) = explode('@', $connect_email);
		getmxrr($domain, $mxrecords); // http://php.net/manual/en/function.getmxrr.php
		$response = array();
		if(!empty($mxrecords)){
			if (str_contains($mxrecords[0], 'outlook.com')) { 
				$outlook_data = outlook_credential();
				$client_id 		= $outlook_data['client_id'];
				$redirect_uri 	= $outlook_data['redirect_uri'];
				$scopes 		= $outlook_data['scopes'];
				$url = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?client_id='.$client_id."&redirect_uri=".$redirect_uri."&response_type=code&scope=".implode(" ", $scopes);
				$response = array('code'=>'success','message'=>$url);
			}
			else{
				$response = array('code'=>'error','message'=>'Please Enter Outlook Email');
			}
		}
		else{
			$response = array('code'=>'error','message'=>'Please Enter Valid Email');
		}
		echo json_encode($response);
	}
	
}