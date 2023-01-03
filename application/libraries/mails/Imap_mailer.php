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

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('email');
        $this->CI->load->library('imap');
        $this->CI->load->model('leads_model');
        $this->imapconf = get_imap_setting();
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
    
    public function send() 
    {
        $this->smtpconf = $this->get_smtp_settings();
        $this->send_smtp();
        $this->read_imap();
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
        $this->saveLocal($this->getMessage($uid));
    }

}