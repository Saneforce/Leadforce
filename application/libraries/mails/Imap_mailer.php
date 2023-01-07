<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Imap_mailer
{
    protected $to;
    protected $subject;
    protected $message;
    protected $cc;
    protected $bcc;
    protected $attachments;
    protected $rel_type;
    protected $rel_id;
    protected $CI;
    protected $redirectTo;
    protected $smtpconf;
    protected $imapconf;
    protected $draft;
    protected $currentUid;
    protected $attachment_ids;
    protected $parentId;
    protected $outlookCredentials;
    protected $outlookToken;

    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('leads_model');
        if(get_option('connect_mail')=='no'){
            $this->CI->load->helper("tasks_helper");
            $this->outlookCredentials = outlook_credential();
            $this->outlookToken = get_outlook_token();
            refresh_token($this->outlookToken->email,$this->outlookToken->refresh_token);
            $this->outlookToken = get_outlook_token();

            
        }else{
            $this->CI->load->library('email');
            $this->CI->load->library('imap');
            $this->imapconf = get_imap_setting();
            $this->smtpconf = $this->get_smtp_settings();
        }
        
    }
    public function set_to($to)
    {
        $this->to =$to;
    }

    public function set_subject($subject)
    {
        $this->subject =$subject;
    }

    public function set_message($message)
    {
        $this->message =$message;
    }

    public function set_cc($cc)
    {
        $this->cc =$cc;
    }

    public function set_bcc($bcc)
    {
        $this->bcc =$bcc;
    }

    public function set_attachments($attachments)
    {
        $this->attachments =$attachments;
    }

    public function set_rel_type($rel_type)
    {
        $this->rel_type =$rel_type;
    }

    public function set_rel_id($rel_id)
    {
        $this->rel_id =$rel_id;
    }

    public function set_redirectTo($redirectTo)
    {
        $this->redirectTo =$redirectTo;
    }

    public function set_draft($draft)
    {
        $this->draft =$draft;
    }

    public function set_parentId($parentId)
    {
        $this->parentId =$parentId;
    }

    public function get_smtp_settings()
    {
        $smtp_cong = array();
        
        if(get_option('company_mail_server')=='no' ){
            $user_config = get_or_update_setting(get_staff_user_id(),db_prefix() . 'personal_mail_setting');
            $smtp_cong['host'] 	   = $user_config['smtp_host'];
            $smtp_cong['encrypto'] = $user_config['smtp_encryption'];
            $smtp_cong['port']	   = $user_config['smtp_port'];
            $smtp_cong['username'] = $user_config['smtp_username'];
            $smtp_cong['password'] = $user_config['smtp_password'];
            $smtp_cong['validate'] = true;
            $smtp_cong['smtp_user'] = $user_config['smtp_email'];
            $smtp_cong['smtp_pass'] = $user_config['smtp_password'];
        }
        else{
            $user_config = get_or_update_setting(get_staff_user_id(),db_prefix() . 'user_mail_setting');
            $smtp_cong['username'] = $user_config['email'];
            $smtp_cong['password'] = $user_config['password'];
            $smtp_cong['validate'] = true;
            $smtp_cong['smtp_user'] = $user_config['email'];
            $smtp_cong['smtp_pass'] = $user_config['password'];
            $smtp_cong['host'] 	   = get_option('company_smtp_global_host');
            $smtp_cong['encrypto'] = get_option('company_smtp_global_encryption');
            $smtp_cong['port']	   = get_option('company_smtp_global_port');
		    
        }
        return $smtp_cong;
        
    }

    public function send_smtp()
    {
        $this->CI->email->initialize($this->smtpconf);
        $this->CI->email->from($this->imapconf['username'], '');
        $this->CI->email->to(array($this->to));
        $this->CI->email->cc($this->cc);
        $this->CI->email->bcc($this->bcc);
        $this->CI->email->reply_to($this->imapconf['username'], 'Replay me');
        $this->CI->email->subject($this->subject);
        $this->CI->email->message($this->message);
        
        if(!empty($this->attachments)){
            $_FILES["attachment"] =$this->attachments;
            $req_data = check_upload();
            $req_datas = json_decode($req_data);
            $this->attachment_ids = $req_datas->name;
            $req_files = $req_datas->path;
            if(!empty($req_files)){
                foreach($req_files as $req_file123){
                    $this->CI->email->attach( $req_file123);
                }
            }
        }
        
        if ($this->CI->email->send()) {

        }else{
            $message       = 'Cannot Connect SMTP Server.';
            set_alert('warning', $message);
            redirect($this->redirectTo);
        }
    }

    public function read_imap()
    {
        if(!empty($this->draft)){
            $this->CI->imap->delete_mail($this->imapconf,$this->draft);
        }
        
        $imap = $this->CI->imap->check_imap($this->imapconf);

        if ($imap) {
            $uid = $this->CI->imap->get_company_latest_email_addresses($this->imapconf);
            if($uid == 'Cannot Read') {
                $email = get_mail_message($_POST,$this->imapconf);
                if($this->redirectTo){
                    $message = "Don't have access to read Sent Folder. Please enable the read permission to Sent folder in your mail server.";
                    set_alert('warning', $message);
                    redirect($this->redirectTo);
                }
            }else{
                
                if(!empty($req_files)){
                    foreach($req_files as $req_file12){
                        unlink($req_file12);
                    }
                }
                if($this->redirectTo){
                    $email = $this->CI->imap->get_company_mail_details($this->imapconf,$uid);
                    $this->currentUid =$uid;
                }
                $this->saveLocal($email);
            }
        } else {
            $message       = 'Cannot Connect IMAP Server.';
            set_alert('warning', $message);
            redirect($this->redirectTo);
        }
    }

    public function saveLocal($email)
    {
        if(get_option('email_local')=='yes'){
            if($this->currentUid != 'Cannot Read') {
                $this->attachment_ids = array_column($email['attachments'], 'name'); 
            }
            $data =array(
                'mailid'=>$email['id'],
                'assignee'=>0,
                'task_id'=>0,
                'project_id'=>0,
                'contacts_id'=>0,
                'uid'=>$email['uid'],
                'staff_id'=>get_staff_user_id(),
                'from_email'=>$email['from']['email'],
                'from_name'=>$email['from']['name'],
                'mail_to'=>json_encode($email['to']),
                'cc'=>json_encode($email['cc']),
                'bcc'=>json_encode($email['bcc']),
                'reply_to'=>json_encode($email['reply_to']),
                'message_id'=>$email['message_id'],
                'in_reply_to'=>$email['in_reply_to'],
                'mail_references'=>json_encode($email['references']),
                'date'=>$email['date'],
                'udate'=>$email['udate'],
                'subject'=>$email['subject'],
                'recent'=>$email['recent'],
                'priority'=>$email['priority'],
                'mail_read'=>$email['read'],
                'answered'=>$email['answered'],
                'flagged'=>$email['flagged'],
                'deleted'=>$email['deleted'],
                'draft'=>$email['draft'],
                'size'=>$email['size'],
                'attachements'=>json_encode($this->attachment_ids),
                'attachment_id'=>'',
                'body_html'=>$email['body']['html'],
                'body_plain'=>$email['body']['plain'],
                'folder'=>'Sent_mail',
                'mail_by'=>'',
                'lead_id'=>0,
            );
            if($this->rel_type =='project')
                $data['project_id']	= $this->rel_id;
            elseif($this->rel_type =='lead')
                $data['lead_id']	= $this->rel_id;
            
            if(get_option('connect_mail')=='no'){
                $data['mail_by'] ='outlook';
            }

            if($this->parentId >0){
                $data['local_id'] =$this->parentId;
                $this->CI->db->insert(db_prefix() . 'reply', $data);
            }else{
                $this->CI->db->insert(db_prefix() . 'localmailstorage', $data);
            }
            
            if($this->rel_type =='lead'){
                $this->CI->leads_model->log_activity($this->rel_id,'email','added',$this->CI->db->insert_id());
            }
            
        }
        
        if($this->redirectTo){
            set_alert('success', 'Mail sent successfully');
            redirect($this->redirectTo);
        }
    }
    
    public function send_outlook()
    {
        $to = $cc = $bcc =array();
        $toarray = explode(",", $this->to);
        if(!empty($toarray)){
            foreach ($toarray as $eachTo) {
                if(strlen(trim($eachTo)) > 0) {
                    array_push($to, array(
                        "EmailAddress" => array(
                            "Address" => trim($eachTo)
                        )
                    ));
                }
            }
        }
        else{
            array_push($to, array(
                "EmailAddress" => array(
                    "Address" => trim($this->to)
                )
            ));
        }

        $cclist = explode(",", $this->cc);
        if(!empty($cclist)){
            foreach ($cclist as $eachcc) {
                if(strlen(trim($eachcc)) > 0) {
                    array_push($cc, array(
                        "EmailAddress" => array(
                            "Address" => trim($eachcc)
                        )
                    ));
                }
            }
        }
        else{
            array_push($cc, array(
                "EmailAddress" => array(
                    "Address" => trim($this->cc)
                )
            ));
        }

        $bcclist = explode(",", $this->bcc);
        if(!empty($bcclist)){
            foreach ($bcclist as $eachbcc) {
                if(strlen(trim($eachcc)) > 0) {
                    array_push($bcc, array(
                        "EmailAddress" => array(
                            "Address" => trim($eachbcc)
                        )
                    ));
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
        $_FILES["attachment"] =$this->attachments;
        $request = array(
            "Message" => array(
                "Subject" =>$this->subject,
                "ToRecipients" => $to,
                "CcRecipients" => $cc,
                "BccRecipients" => $bcc,
                "Attachments" => get_attachement(),
                "Body" => array(
                    "ContentType" => "HTML",
                    "Content" => $this->message
                )
            )
        );
        $request = json_encode($request);
        
        $headers = array(
            "User-Agent: php-tutorial/1.0",
            "Authorization: Bearer ".$this->outlookToken->token,
            "Accept: application/json",
            "Content-Type: application/json",
            "Content-Length: ". strlen($request)
        );
        $req_url = $this->outlookCredentials["api_url"].'/me/sendmail';
        $response = runCurl($req_url, $request, $headers);
    }

    public function read_outlook()
    {

        $outlookmessage =false;
        $headers = array(
			"User-Agent: php-tutorial/1.0",
			"Authorization: Bearer ".$this->outlookToken->token,
			"Accept: application/json",
			"client-request-id: ".makeGuid(),
			"return-client-request-id: true",
			"X-AnchorMailbox: ". $this->outlookToken->email
		);
        $outlookApiUrl = $this->outlookCredentials["api_url"] . "/me/mailFolders" ;
		$response = runCurl($outlookApiUrl, null, $headers);
		$response = explode("\n", trim($response));
		$response = $response[count($response) - 1];
		$response = json_decode($response, true);
        
		if(!empty($response['value'])){
			foreach($response['value'] as $folder1){
				$icon = ucwords(strtolower($folder1['DisplayName']));
				if($icon == 'Sent Items'){
					$outlookApiUrl1 = $this->outlookCredentials["api_url"] . "/me/mailFolders/".$folder1['Id']."/messages" ;
					$response1 = runCurl($outlookApiUrl1, null, $headers);
					$response1 = explode("\n", trim($response1));
					$response1 = $response1[count($response1) - 1];
					$response1 = json_decode($response1, true);
					$outlookmessage =$response1['value'][0];
					break;
				}
				
			}
		}
        
        if($outlookmessage){
            $to = $cc = $bcc = array();
            if(!empty($outlookmessage['ToRecipients'])){
                foreach($outlookmessage['ToRecipients'] as $mailto){
                    $to[] =array(
                        'email'=>$mailto['EmailAddress']['Address'],
                        'name'=>$mailto['EmailAddress']['Name'],
                    );
                }
            }

            if(!empty($outlookmessage['CcRecipients'])){
                foreach($outlookmessage['CcRecipients'] as $mailcc){
                    $cc[] =array(
                        'email'=>$mailcc['EmailAddress']['Address'],
                        'name'=>$mailcc['EmailAddress']['Name'],
                    );
                }
            }

            if(!empty($outlookmessage['BccRecipients'])){
                foreach($outlookmessage['BccRecipients'] as $mailbcc){
                    $bcc[] =array(
                        'email'=>$mailbcc['EmailAddress']['Address'],
                        'name'=>$mailbcc['EmailAddress']['Name'],
                    );
                }
            }

            $email =array(
                'id'=>0,
                'uid'=>0,
                'from'=>array(
                    'email'=>$outlookmessage['From']['EmailAddress']['Address'],
                    'name'=>$outlookmessage['From']['EmailAddress']['Name']
                ),
                'to'=>$to,
                'cc'=>$cc,
                'bcc'=>$cc,
                'reply_to'=>$outlookmessage['ReplyTo'],
                'message_id'=>$outlookmessage['Id'],
                'in_reply_to'=>json_encode($outlookmessage['ReplyTo']),
                'references'=>'',
                'date'=>$outlookmessage['ReceivedDateTime'],
                'udate'=>strtotime($outlookmessage['SentDateTime']),
                'subject'=>$outlookmessage['Subject'],
                'recent'=>'',
                'priority'=>'',
                'read'=>$outlookmessage['IsRead'],
                'answered'=>$outlookmessage['IsRead'],
                'flagged'=>$outlookmessage['Flag']['FlagStatus'],
                'deleted'=>'',
                'draft'=>'',
                'size'=>'',
                'body'=>array(
                    'html'=>$outlookmessage['Body']['Content'],
                    'plain'=>$outlookmessage['BodyPreview'],
                )
            );
            $this->saveLocal($email);
            if($this->redirectTo){
                set_alert('success', 'Mail sent successfully');
                redirect($this->redirectTo);
            }
        }
        
        
    }
    public function send() 
    {
        if(get_option('connect_mail')=='no'){
            $this->send_outlook();
            $this->read_outlook();
        }else{
            $this->send_smtp();
            $this->read_imap();
        }
        
    }

    public function getMessage($uid)
    {
        $this->CI->imap->connect($this->imapconf);
        $message = $this->CI->imap->get_message($uid);
        $this->CI->imap->mark_as_read_company($uid,$this->imapconf);
        return $message;
    }

    public function connectEmail($uid)
    {
        if(is_array($uid)){
            $this->saveLocal($uid);
        }else{
            $this->saveLocal($this->getMessage($uid));
        }
        
    }

}