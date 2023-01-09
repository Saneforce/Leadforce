<?php
/**
 * Imap Class
 * This class enables you to use the IMAP Protocol
 *
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Email
 * @version    1.0.0-dev
 * @author     Natan Felles
 * @link       http://github.com/natanfelles/codeigniter-imap
 */

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Class Imap
 */
class Imap
{
	/**
	 * IMAP mailbox - The full "folder" path
	 *
	 * @var string
	 */
	protected $mailbox;

	/**
	 * IMAP stream
	 *
	 * @var resource
	 */
	protected $stream;

	/**
	 * The current folder
	 *
	 * @var string
	 */
	protected $folder = 'INBOX';

	/**
	 * [$search_criteria description]
	 *
	 * @var string
	 */
	protected $search_criteria;

	/**
	 * [$CI description]
	 *
	 * @var CI_Controller
	 */
	protected $CI;

	/**
	 * [$config description]
	 *
	 * @var array
	 */
	protected $config = [];

	/**
	 * [__construct description]
	 *
	 * @param array $config
	 */
	public function __construct(array $config = [])
	{
		$this->CI =& get_instance();
		//$config['cache']['active'] = true;
		if (! empty($config))
		{
			$this->config = $config;
			//$this->connect();
		}
	}

	/**
	 * @param array $config Options: host, encrypto, user, pass, port, folders
	 *
	 * @return boolean True if is connected
	 */
	public function connect(array $config = [])
	{
		$config       = array_replace_recursive($this->config, $config);
		$this->config = $config;
		
		if ($config['cache']['active'] === true)
		{
			$this->CI->load->driver('cache',
				[
				'adapter'    => $config['cache']['adapter'],
				'backup'     => $config['cache']['backup'],
				'key_prefix' => $config['cache']['key_prefix'],
				]);
		}

		$enc = '';

		if (isset($config['port']))
		{
			$enc .= ':' . $config['port'];
		}

		if (isset($config['encrypto']))
		{
			$enc .= '/' . $config['encrypto'];
		}

		if (isset($config['validate']) && $config['validate'] === false)
		{
			$enc .= '/novalidate-cert';
		}
		
		$this->mailbox = '{' . $config['host'] . ':993/imap/ssl/novalidate-cert}';
		imap_timeout(IMAP_OPENTIMEOUT, 3);
		$this->stream  = imap_open($this->mailbox, $config['username'], $config['password'])or die('Cannot connect to mail: ' . pr(imap_errors()));;
		//pre($this->stream);
		//show_error($this->get_last_error());

		return is_resource($this->stream);
	}
	public function check_imap(array $config = [])
    {
		$config       = array_replace_recursive($this->config, $config);
		$this->config = $config;
		$this->mailbox = '{' . $config['host'] .  ':'.$config['port'].'/imap/ssl/novalidate-cert}';
		imap_timeout(IMAP_OPENTIMEOUT, 3);
		 imap_timeout(IMAP_READTIMEOUT, 3);
		 imap_timeout(IMAP_WRITETIMEOUT, 3);
		 imap_timeout(IMAP_CLOSETIMEOUT, 3);
		$this->conn  = imap_open($this->mailbox, $config['username'], $config['password']);
		if(!$this->conn)
        {
			return false;
		}
        return true;
    }

	protected function set_cache($cache_id, $data)
	{
		if ($this->config['cache']['active'] === true)
		{
			//$_SESSION[$cache_id] = $data;
		
			return $this->CI->cache->save($cache_id, $data, $this->config['cache']['ttl']);
		}

		return true;
	}
	/*protected function set_cache($cache_id, $data)
	{
		
		$req_data['1_'.$cache_id] = $data;
		if(empty($this->CI->session->userdata('1_'.$cache_id))){
	
			$this->CI->session->set_userdata($req_data);
		}
		return true;
	}*/

	protected function get_cache($cache_id)
	{
		if ($this->config['cache']['active'] === true)
		{
			return $this->CI->cache->get($cache_id);
		}

		return false;
	}
	/*protected function get_cache($cache_id)
	{
		if (!empty($this->CI->session->userdata('1_'.$cache_id)))
		{
			return $this->CI->session->userdata('1_'.$cache_id);
		}

		return false;
	}*/

	/**
	 * [set_timeout description]
	 *
	 * @param integer $timeout
	 * @param string  $type    open, read, write, or close
	 *
	 * @return boolean
	 */
	public function set_timeout(int $timeout = 60, string $type = 'open')
	{
		$types = [
			'open'  => IMAP_OPENTIMEOUT,
			'read'  => IMAP_READTIMEOUT,
			'write' => IMAP_WRITETIMEOUT,
			'close' => IMAP_CLOSETIMEOUT,
		];

		return imap_timeout($types[$type], $timeout);
	}

	/**
	 * [get_timeout description]
	 *
	 * @param string $type open, read, write, or close
	 *
	 * @return integer
	 */
	public function get_timeout(string $type = 'open')
	{
		$types = [
			'open'  => IMAP_OPENTIMEOUT,
			'read'  => IMAP_READTIMEOUT,
			'write' => IMAP_WRITETIMEOUT,
			'close' => IMAP_CLOSETIMEOUT,
		];

		return imap_timeout($types[$type]);
	}

	/**
	 * [ping description]
	 *
	 * @return boolean
	 */
	public function ping()
	{
		//return $this->fun('ping');
		return imap_ping($this->stream);
	}

	/**
	 * [disconnect description]
	 *
	 * @return boolean
	 */
	public function disconnect()
	{
		if (is_resource($this->stream))
		{
			if ($this->config['expunge_on_disconnect'] === true)
			{
				$this->select_folder($this->get_trash_folder());
				$this->mark_as_deleted($this->search());
				$this->expunge();
			}

			// Clears all errors before to close
			// See: https://github.com/natanfelles/codeigniter-imap/issues/5#issuecomment-355453233
			imap_errors();

			return imap_close($this->stream);
		}

		return true;
	}

	/**
	 * [set_expunge_on_disconnect description]
	 *
	 * @param boolean $active
	 *
	 * @return Imap
	 */
	public function set_expunge_on_disconnect(bool $active = true)
	{
		$this->config['expunge_on_disconnect'] = $active;

		return $this;
	}

	/**
	 * [expunge description]
	 *
	 * @return boolean
	 */
	public function expunge()
	{
		return imap_expunge($this->stream);
	}

	/**
	 * Gets the last IMAP error that occurred during this page request
	 *
	 * @return string|boolean Last error message or false if no errors
	 */
	public function get_last_error()
	{
		return imap_last_error();
	}

	/**
	 * [get_errors description]
	 *
	 * @return array|boolean Array of errors or false if no errors
	 */
	public function get_errors()
	{
		return imap_errors();
	}

	public function get_alerts()
	{
		return imap_alerts();
	}

	/**
	 * Get all folders names
	 *
	 * @return array Array of folder names. If an item is an array then
	 *               this is an associative array of "folder" => [subfolders]
	 */
	public function get_folders()
	{
		
		$folders = imap_list($this->stream, $this->mailbox, '*');
		$folders = $this->get_subfolders(str_replace($this->mailbox, '', $folders));
		sort($folders);

		return $folders;
	}
	
	public function get_compay_folders($config)
	{
		$this->mailbox = '{' . $config['host'] . ':'.$config['port'].'/imap/ssl/novalidate-cert}';
		$this->stream  = imap_open($this->mailbox, $config['username'], $config['password']);
		$folders = imap_list($this->stream, $this->mailbox, '*');
		$folders = $this->get_subfolders(str_replace($this->mailbox, '', $folders));
		sort($folders);

		return $folders;
	}

	protected function get_subfolders($folders)
	{
		if(is_array($folders)){
			for ($i = 0; $i < count($folders); $i++)
			{
				
				if (isset(explode('.', $folders[$i])[1]))
				{
					$folders[$i] = $this->get_subfolders($folders[$i]);
				}
			}
		}
		

		// for ($i = 0; $i < count($folders); $i++)
		// {
		// 	if (strpos($folders[$i],'.') !== false)
		// 	{
		// 		$folders[$folders[$i]] = $this->static_dot_notation($folders[$i]);
		// 	}
		// }
		
		return $folders;
	}

	// function static_dot_notation($string, $value = null)
	// {
	// 	static $return;

	// 	$token = strtok($string, '.');

	// 	$ref =& $return;

	// 	while ($token !== false)
	// 	{
	// 		$ref   =& $ref[$token];
	// 		$token = strtok('.');
	// 	}

	// 	$ref = $value;

	// 	return $return;
	// }

	/**
	 * Select folder
	 *
	 * @param string $folder Folder name
	 *
	 * @return boolean
	 */
	public function select_folder(string $folder)
	{
		if ($result = imap_reopen($this->stream, $this->mailbox . $folder))
		{
			$this->folder = $folder;
		}

		return $result;
	}

	/**
	 * Add folder
	 *
	 * @param string $name Folder name
	 *
	 * @return boolean
	 */
	public function add_folder(string $folder_name)
	{
		return imap_createmailbox($this->stream, $this->mailbox . $folder_name);
	}

	/**
	 * Rename folder
	 *
	 * @param string $name     Current folder name
	 * @param string $new_name New folder name
	 *
	 * @return boolean    TRUE on success or FALSE on failure.
	 */
	public function rename_folder(string $name, string $new_name)
	{
		return imap_renamemailbox($this->stream, $this->mailbox . $name, $this->mailbox . $new_name);
	}

	/**
	 * Remove folder
	 *
	 * @param string $folder_name
	 *
	 * @return boolean TRUE on success or FALSE on failure.
	 */
	public function remove_folder(string $folder_name)
	{
		return imap_deletemailbox($this->stream, $this->mailbox . $folder_name);
	}

	/**
	 * Count all messages in the current or given folder,
	 * optionally matching a criteria
	 *
	 * @param string $folder
	 * @param string $flag_criteria Ex: RECENT, SEEN, UNSEEN, FROM "a@b.cc"
	 *
	 * @return integer
	 */
	public function count_messages(string $folder = null, string $flag_criteria = null)
	{
		$current_folder = $this->folder;

		if (isset($folder))
		{
			$this->select_folder($folder);
		}

		if ($flag_criteria)
		{
			$count = count($this->search($flag_criteria));
		}
		else
		{
			$count = imap_num_msg($this->stream);
		}

		if (isset($folder))
		{
			$this->select_folder($current_folder);
		}

		return $count;
	}

	/**
	 * Get quota usage and limit from mail account
	 *
	 * @return array
	 */
	public function get_quota(string $folder = null)
	{
		$current_folder = $this->folder;

		if (isset($folder))
		{
			$this->select_folder($folder);
		}

		$quota = imap_get_quotaroot($this->stream, $this->mailbox . $folder);

		if (isset($folder))
		{
			$this->select_folder($current_folder);
		}

		return $quota;
	}

	/**
	 * [move_messages description]
	 *
	 * @param mixed  $uids   Array list of uid's or a comma separated list of uids
	 * @param string $target
	 *
	 * @return boolean
	 */
	public function move_messages($uids, string $target)
	{
		if (is_array($uids))
		{
			$uids = implode(',', $uids);
		}
		if (imap_mail_move($this->stream, str_replace(' ', '', $uids), $target, CP_UID))
		{
			// Expunge is necessary to remove the original message that was
			// automatically marked as deleted when moved
			return imap_expunge($this->stream);
		}

		return false;
	}
	public function move_messages_trash($uids, string $target,$imapconf,string $folder)
	{
		$this->mailbox = '{' . $imapconf['host'] . ':'.$imapconf['port'].'/imap/ssl/novalidate-cert}'.$folder;
		$this->stream  = imap_open($this->mailbox, $imapconf['username'], $imapconf['password'])or die('Cannot connect to mail: ' . pr(imap_errors()));
		//$this->select_folder($target);
		if (is_array($uids))
		{
			$uids = implode(',', $uids);
		}
		
		if (imap_mail_move($this->stream, str_replace(' ', '', $uids),$target, CP_UID))
		{
			// Expunge is necessary to remove the original message that was
			// automatically marked as deleted when moved
			return imap_expunge($this->stream);
		}

		return false;
	}

	public function move_to_inbox($uids,$imapconf)
	{
		return $this->move_messages($uids, $this->get_inbox_folder($imapconf));
	}

	public function move_to_trash($uids,$imapconf,$folder)
	{
		$req_s = $this->get_compay_folders($imapconf);
		if(in_array('[Gmail]/Trash',$req_s)){
			return $this->move_messages_trash($uids,'[Gmail]/Trash',$imapconf,$folder);
		}elseif(in_array('Deleted',$req_s)){
			return $this->move_messages_trash($uids,'Deleted',$imapconf,$folder);
		//return $this->move_messages($uids, $this->get_trash_folder($imapconf));
		}elseif(in_array('Trash',$req_s)){
			return $this->move_messages_trash($uids,'Trash',$imapconf,$folder);
		//return $this->move_messages($uids, $this->get_trash_folder($imapconf));
		}
		return true;
	}

	public function move_to_draft($uids,$imapconf)
	{
		return $this->move_messages($uids, $this->get_draft_folder($imapconf));
	}

	public function move_to_spam($uids,$imapconf)
	{
		return $this->move_messages($uids, $this->get_spam_folder($imapconf));
	}

	public function move_to_sent($uids,$imapconf)
	{
		return $this->move_messages($uids, $this->get_sent_folder($imapconf));
	}
	public function delete_mail_all($config,string $folder,$uids){
		$this->mailbox = '{' . $config['host'] . ':'.$config['port'].'/imap/ssl/novalidate-cert}'.$folder;
		$mbox  = imap_open($this->mailbox, $config['username'], $config['password'])or die('Cannot connect to mail: ' . pr(imap_errors()));
		if (is_array($uids))
		{
			if(!empty($uids)){
				$uids1 = implode(',', $uids);
				$type = '[Gmail]/Drafts';
				foreach($uids as $uid1){
					imap_delete($mbox, $uid1, FT_UID);
					
				}
				imap_expunge($mbox);
				imap_close($mbox);
			}
		}
		else{
			imap_delete($mbox, $uids, FT_UID);
			imap_expunge($mbox);
			imap_close($mbox);
		}
		
	}
	public function delete_mail($config,$uid){
		$this->mailbox = '{' . $config['host'] . ':'.$config['port'].'/imap/ssl/novalidate-cert}[Gmail]/Drafts';
		$mbox  = imap_open($this->mailbox, $config['username'], $config['password'])or die('Cannot connect to mail: ' . pr(imap_errors()));
		//imap_delete($mbox, 1);
		imap_delete($mbox, $uid, FT_UID);
		imap_expunge($mbox);
		imap_close($mbox);
	}
	public function save_msg_draft($config,$from,$to,$subject,$text){
		$this->mailbox = '{' . $config['host'] . ':'.$config['port'].'/imap/ssl/novalidate-cert}[Gmail]/Drafts';
		$this->stream  = imap_open($this->mailbox, $config['username'], $config['password'])or die('Cannot connect to mail: ' . pr(imap_errors()));
		
		imap_append($this->stream, $this->mailbox
						   , "From: ".$from."\r\n"
						   . "To: ".$to."\r\n"
						   . "Subject: ".$subject."\r\n"
						   . "\r\n"
						   . "".$text."\r\n"
						   );
		$req_uid = '';
		$this->select_folder('[Gmail]/Drafts');
		$uids = $this->search();
		$req_uid = $uid[0];
		/*$folders = $this->get_compay_folders($config);
		foreach ($folders as $folder)
		{
			$this->select_folder('[Gmail]/Drafts');
			$uids = $this->search();
			$io = 0;
			foreach ($uids as $uid)
			{
				if($io == 0) {
					$req_uid = $uid;
					break;
				}
				$io++; 
			}
		}*/

		imap_close($this->stream);
		return $req_uid;
	}

	/**
	 * [move_messages description]
	 *
	 * @param mixed $uids Array list of uid's or a comma separated list of uids
	 *
	 * @return boolean
	 */
	public function mark_as_read($uids)
	{
		return $this->message_setflag($uids, 'Seen');
	}

	/**
	 * [move_messages description]
	 *
	 * @param mixed $uids Array list of uid's or a comma separated list of uids
	 *
	 * @return boolean
	 */
	public function mark_as_unread($uids,$imapconf)
	{
		return $this->message_company_clearflag($uids, 'Seen',$imapconf);
	}
	
	public function mark_as_read_company($uids,$imapconf)
	{
		return $this->message_company_setflag($uids, 'Seen',$imapconf);
	}
	public function mark_as_company_draft($uids,$imapconf)
	{
		return $this->message_setflag($uids, 'Draft',$imapconf);
	}
	protected function message_company_setflag($uids, string $flag,$config)
	{
		if (is_array($uids))
		{
			if(!empty($uids)){
				foreach($uids as $uid1){
					$this->CI->session->unset_userdata('msg_1'.$uid1);
				}
			}
			$uids = implode(',', $uids);
		}
		else{
			$this->CI->session->unset_userdata('msg_1'.$uids);
		}
		$this->mailbox = '{' . $config['host'] . ':'.$config['port'].'/imap/ssl/novalidate-cert}';
		$this->stream  = imap_open($this->mailbox, $config['username'], $config['password'])or die('Cannot connect to mail: ' . pr(imap_errors()));
		return imap_setflag_full($this->stream, str_replace(' ', '', $uids), '\\' . ucfirst($flag), ST_UID);
	}
	
	protected function message_company_clearflag($uids, string $flag,$config)
	{
		if (is_array($uids))
		{
			
			if(!empty($uids)){
				foreach($uids as $uid1){
					$this->CI->session->unset_userdata('msg_1'.$uid1);
				}
			}
			$uids = implode(',', $uids);
		}
		else{
			$this->CI->session->unset_userdata('msg_1'.$uids);
		}
		$this->mailbox = '{' . $config['host'] . ':'.$config['port'].'/imap/ssl/novalidate-cert}';
		$this->stream  = imap_open($this->mailbox, $config['username'], $config['password'])or die('Cannot connect to mail: ' . pr(imap_errors()));
		return  imap_clearflag_full($this->stream, str_replace(' ', '', $uids), '\\' . ucfirst($flag), ST_UID);
	}

	/**
	 * [move_messages description]
	 *
	 * @param mixed $uids Array list of uid's or a comma separated list of uids
	 *
	 * @return boolean
	 */
	public function mark_as_answered($uids)
	{
		return $this->message_setflag($uids, 'Answered');
	}

	/**
	 * [move_messages description]
	 *
	 * @param mixed $uids Array list of uid's or a comma separated list of uids
	 *
	 * @return boolean
	 */
	public function mark_as_unanswered($uids)
	{
		return $this->message_clearflag($uids, 'Answered');
	}

	/**
	 * [move_messages description]
	 *
	 * @param mixed $uids Array list of uid's or a comma separated list of uids
	 *
	 * @return boolean
	 */
	public function mark_as_flagged($uids)
	{
		return $this->message_setflag($uids, 'Flagged');
	}

	/**
	 * [move_messages description]
	 *
	 * @param mixed $uids Array list of uid's or a comma separated list of uids
	 *
	 * @return boolean
	 */
	public function mark_as_unflagged($uids)
	{
		return $this->message_clearflag($uids, 'Flagged');
	}

	/**
	 * [move_messages description]
	 *
	 * @param mixed $uids Array list of uid's or a comma separated list of uids
	 *
	 * @return boolean
	 */
	public function mark_as_deleted($uids)
	{
		return $this->message_setflag($uids, 'Deleted');
	}

	/**
	 * [move_messages description]
	 *
	 * @param mixed $uids Array list of uid's or a comma separated list of uids
	 *
	 * @return boolean
	 */
	public function mark_as_undeleted($uids)
	{
		return $this->message_clearflag($uids, 'Deleted');
	}

	/**
	 * [move_messages description]
	 *
	 * @param mixed $uids Array list of uid's or a comma separated list of uids
	 *
	 * @return boolean
	 */
	public function mark_as_draft($uids)
	{
		return $this->message_setflag($uids, 'Draft');
	}

	/**
	 * [move_messages description]
	 *
	 * @param mixed $uids Array list of uid's or a comma separated list of uids
	 *
	 * @return boolean
	 */
	public function mark_as_undraft($uids)
	{
		return $this->message_clearflag($uids, 'Draft');
	}

	/**
	 * [message_setflag description]
	 *
	 * @param mixed  $uids Array list of uid's or a comma separated list of uids
	 * @param string $flag
	 *
	 * @return boolean
	 */
	protected function message_setflag($uids, string $flag)
	{
		if (is_array($uids))
		{
			$uids = implode(',', $uids);
		}

		return imap_setflag_full($this->stream, str_replace(' ', '', $uids), '\\' . ucfirst($flag), ST_UID);
	}

	/**
	 * [message_clearflag description]
	 *
	 * @param mixed  $uids Array list of uid's or a comma separated list of uids
	 * @param string $flag
	 *
	 * @return boolean
	 */
	protected function message_clearflag($uids, string $flag)
	{
		if (is_array($uids))
		{
			$uids = implode(',', $uids);
		}
		return imap_clearflag_full($this->stream, str_replace(' ', '', $uids), '\\' . ucfirst($flag), ST_UID);
	}

	public function get_latest_email_addresses()
	{ 
		$cache_id = 'email_addresses';

		if (($cache = $this->get_cache($cache_id)) !== false)
		{
			return $cache;
		}
		$current_folder = $this->folder;
		$contacts       = [];
		foreach ($this->get_folders() as $folder)
		{
			$this->select_folder('[Gmail]/Sent Mail');
			$uids = $this->search();
			$io = 0;
			foreach ($uids as $uid)
			{
				if($io == 0) {
					return $uid;
					 exit;
				}
				$io++; 
			}
		}

		ksort($contacts);

		$this->select_folder($current_folder);

		$this->set_cache($cache_id, $contacts);

		return $contacts;
	}
	public function get_company_latest_email_addresses($imapconf)
	{ 
		$cache_id = 'email_addresses';

		if (($cache = $this->get_cache($cache_id)) !== false)
		{
			return $cache;
		}
		$current_folder = $this->folder;
		$contacts       = [];
		$req_s = $this->get_compay_folders($imapconf);
		foreach ($this->get_compay_folders($imapconf) as $folder)
		{
			$this->select_folder('[Gmail]/Sent Mail');
			$uids = $this->search();
			if(empty($uids)){
				if(in_array('Sent',$req_s)){
					$this->select_folder('Sent');
					$uids = $this->search();
					$io = 0;
					if(empty($uids)){
						return "Cannot Read";
						exit;
					}
					foreach ($uids as $uid)
					{
						if($io == 0) {
							return $uid;
							 exit;
						}
						$io++; 
					}

				}/*else if(in_array('INBOX.Sent',$req_s)){
				$this->select_folder('INBOX.Sent');
				$uids = $this->search();
				$io = 0;
				foreach ($uids as $uid)
				{
					if($io == 0) {
						return $uid;
						 exit;
					}
					$io++; 
				}

			}*/ else {
					return "Cannot Read";
					exit;
				}
			}else{
				$io = 0;
				foreach ($uids as $uid)
				{
					if($io == 0) {
						return $uid;
						 exit;
					}
					$io++; 
				}
			}
		}

		ksort($contacts);

		$this->select_folder($current_folder);

		$this->set_cache($cache_id, $contacts);

		return $contacts;
	}

	public function get_sentitems()
	{ 
		//echo "<pre>"; print_r($_POST); exit;
		$cache_id = 'email_addresses';
		if (($cache = $this->get_cache($cache_id)) !== false)
		{
			return $cache;
		}

		$current_folder = '[Gmail]/Sent Mail';
		
		$mailList = [];
		$output = '';
		
		//echo "<pre>"; print_r($folder); exit;
		$this->select_folder($current_folder);
		$uids = $this->search();
		$io = 0;
		if($uids) {
			foreach ($uids as $uid)
			{
				$inboxEmails = $this->get_message($uid);
				$sentDate = date("d-m-Y",$inboxEmails['udate']);
				if($sentDate == date('d-m-Y')) {
					$mailList[$io]['uid'] = $uid;
					$mailList[$io]['toemail'] = $inboxEmails['to'];
					$mailList[$io]['name'] = $inboxEmails['subject'];
					$mailList[$io]['description'] = $inboxEmails['body']['plain'];
					$io++;
				} else {
					return $mailList;
					exit;
				}
			}
		}
		
	}
	
	public function get_inboxitems()
	{ 
		//echo "<pre>"; print_r($_POST); exit;
		$cache_id = 'email_addresses';
		if (($cache = $this->get_cache($cache_id)) !== false)
		{
			return $cache;
		}

		$current_folder = 'Inbox';
		
		$mailList = [];
		$output = '';
		
		//echo "<pre>"; print_r($folder); exit;
		$this->select_folder($current_folder);
		$uids = $this->search();
		$io = 0;
		if($uids) {
			foreach ($uids as $uid)
			{
				$inboxEmails = $this->get_message($uid);
				$sentDate = date("d-m-Y",$inboxEmails['udate']);
				if($sentDate == date('d-m-Y')) {
					$mailList[$io]['uid'] = $uid;
					$mailList[$io]['from'] = $inboxEmails['from'];
					$mailList[$io]['to'] = $inboxEmails['to'];
					$mailList[$io]['cc'] = $inboxEmails['cc'];
					$mailList[$io]['name'] = $inboxEmails['subject'];
					$mailList[$io]['description'] = $inboxEmails['body']['plain'];
					$io++;
				} else {
					return $mailList;
					exit;
				}
			}
		}
		
	}
	
	public function get_folders_inbox()
	{
		$mailList = [];
		$folders = imap_list($this->stream, $this->mailbox, '*');
		$mailList['folders'] = $this->get_subfolders(str_replace($this->mailbox, '', $folders));
		$output = '';
		$this->select_folder('INBOX');
		$uids = $this->search();
		$io = 1;
		if($uids) {
			$totalcnt = count($uids);
			foreach ($uids as $uid)
			{
				if($io <= 10) {
					$inboxEmails = $this->get_message($uid);
					if($inboxEmails['from']['name'] != '') {
						$fromname = $inboxEmails['from']['name'];
					} else {
						$fromname = $inboxEmails['from']['email'];
					}
					$toname = '';
					foreach($inboxEmails['to'] as $to) {
						$toname .= $to['email'].',';
					}
					//echo "<pre>"; print_r($inboxEmails); exit;
					$toname = substr($toname, 0, -1);

					$CI          = & get_instance();
					$sQuery = "select name from tblprojects where id = (select rel_id from tbltasks where source_from = '".$inboxEmails['uid']."')";
					$rResult = $CI->db->query($sQuery)->result_array();

					$output .= '<td class="name"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.$fromname.'</a></td>';
					$output .= '<td class="name"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.$toname.'</a></td>';
					$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.substr($inboxEmails['subject'],0,30).'</a></td>';
					$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.substr(strip_tags(str_replace('[image: Google]','',$inboxEmails['body']['plain'])),0,30).'...</a></td>';
					$output .= '<td class="subject"><a href="#" onClick="updatedeal('.$inboxEmails['uid'].');">'.htmlentities($rResult[0]['name']).'</a></td>';
					$output .= '<td class="time"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.date("d-M-Y h:i A",$inboxEmails['udate']).'</a></td></tr>';
					if($io == 10 || $io == $totalcnt) {
						$mailList['table'] = $output;
						$mailList['field'] = '<input type="hidden" id="folder" value="'.$_REQUEST['folder'].'"><input type="hidden" id="uid" value="'.$inboxEmails['uid'].'">';
						return $mailList; 
						exit;
					}
				}
				$io++;
			}
		} else {
			$mailList['table'] = '<tr><td colspan="4" style="text-align:center;">No Emails Found.</td></tr>';
			$mailList['field'] = '<input type="hidden" id="folder" value="Inbox"><input type="hidden" id="uid" value="'.$_REQUEST['uid'].'">';
			return $mailList; 
			exit;
		}
		sort($folders);

		return $folders;
	}
	public function get_company_mail_details($imapconf,$uid){
		$this->mailbox = '{' . $imapconf['host'] . ':'.$imapconf['port'].'/imap/ssl/novalidate-cert}';
		$this->stream  = imap_open($this->mailbox, $imapconf['username'], $imapconf['password'])or die('Cannot connect to mail: ' . pr(imap_errors()));
		$req_s = $this->get_compay_folders($imapconf);
		$req_s = $this->get_compay_folders($imapconf);
		
		$this->select_folder('[Gmail]/Sent Mail');
		$uids = $this->search();
		if(empty($uids)){
			if(in_array('Sent',$req_s)){
				$this->select_folder('Sent');
			}
			/*else if(in_array('INBOX.Sent',$req_s)){
				$this->select_folder('INBOX.Sent');
			}*/
		}
		$inboxEmails = $this->get_message($uid);
		return $inboxEmails;
	}
	public function get_company_folders_inbox($imapconf,$pag_no='',$counts='',$search='')
	{
		
		$mailList = [];
		$start = microtime(true);
		$this->mailbox = '{' . $imapconf['host'] . ':'.$imapconf['port'].'/imap/ssl/novalidate-cert}';
		$this->stream  = imap_open($this->mailbox, $imapconf['username'], $imapconf['password'])or die('Cannot connect to mail: ' . pr(imap_errors()));;
		
		$folders = imap_list($this->stream, $this->mailbox, '*');

		$mailList['folders'] = $this->get_subfolders(str_replace($this->mailbox, '', $folders));
		if(!empty($mailList['folders'])){
			$i = $req_count = 0;
			$mailList['folder_values'] = '<ul class="nav nav-pills nav-stacked"><li class="header">Folders</li>';
			foreach($mailList['folders'] as $name) { 
				$icon = ucwords(strtolower(str_replace('[Gmail]/','',$name)));
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
					$icon = $name;
					$faicon = 'fa-folder';
				}
				if(isset($counts[$name])){
					$req_count = $counts[$name];
				}
				else{
					$req_count = '';
				}
				if($name==$_REQUEST['folder']) {
					$class = 'active';
				} else {
					$class = '';
				}
				$req_icon = "'".$name."'";
				//$mailList['folder_values'] .= '<li class="'.$class.'"><a href="#" id="'.strtolower(str_replace(' ','-',$icon)).'" onClick="getMailList('.$req_icon.');"><i class="fa '.$faicon.'"></i> '.$icon.' ('.$req_count.')</a></li>';
				$mailList['folder_values'] .= '<li class="'.$class.'"><a href="#" id="'.strtolower(str_replace(' ','-',$icon)).'" onClick="getMailList('.$req_icon.');"><i class="fa '.$faicon.'"></i> '.$icon.'</a></li>';
				
				$i++;
			}
			$mailList['folder_values'] .= '</ul>';
		}
		
		$output = '';
		if(empty($_REQUEST['folder'])){
			$_REQUEST['folder'] = 'INBOX';
		}
		$this->select_folder($_REQUEST['folder']);
		$this->mailbox = '{' . $imapconf['host'] . ':'.$imapconf['port'].'/imap/ssl/novalidate-cert}'.$_REQUEST['folder'];
		$this->stream  = imap_open($this->mailbox, $imapconf['username'], $imapconf['password'])or die('Cannot connect to mail: ' . pr(imap_errors()));
		$MC = imap_check($this->stream);
		
		$showMessages = 1000;
		if($_REQUEST['sort_val'] !=1 || $_REQUEST['sort_option']!='date'){
			$criterias = [
				'date'    => SORTDATE, // message Date
				'arrival' => SORTARRIVAL, // arrival date
				'from'    => SORTFROM, // mailbox in first From address
				'subject' => SORTSUBJECT, // message subject
				'to'      => SORTTO, // mailbox in first To address
				'cc'      => SORTCC, // mailbox in first cc address
				'size'    => SORTSIZE, // size of message in octets
			];
			if(empty($pag_no)){
				$pag_no  = 1;
			}
			$totalMessages = $MC->Nmsgs;
			
			$req_page = ($pag_no*10)/$showMessages;
			
			if($req_page<=1){
				$req_page = 1;
			}
			if($totalMessages>=$showMessages){
				$req_page = (int) $req_page;
				if($_REQUEST['sort_val'] ==0 && $_REQUEST['sort_option']=='date'){
					if($req_page == 1){
						$cur_lmt = ($showMessages * ($req_page-1))+1;
					}else{
						$cur_lmt = ($showMessages * ($req_page-1));
					}
					$cur_offt = ($cur_lmt + $showMessages)-1;
					$results =imap_fetch_overview($this->stream,$cur_lmt.":".$cur_offt,0);
					$last_rec = count($results) -1;
					$uids = imap_sort($this->stream, $criterias[$_REQUEST['sort_option']],$_REQUEST['sort_val'],SE_UID,'SINCE "'.$results[0]->date.'" BEFORE "'.$results[$last_rec]->date.'"');
				}else{
					$cur_lmt = ($totalMessages - ($showMessages * $req_page) )+1;
					$cur_offt = $totalMessages - (($showMessages * ($req_page-1)) );
					$results =imap_fetch_overview($this->stream,$cur_lmt.":".$cur_offt,0);
					$last_rec = count($results) -1;
					$uids = imap_sort($this->stream, $criterias[$_REQUEST['sort_option']],$_REQUEST['sort_val'],SE_UID,'SINCE "'.$results[$last_rec]->date.'" BEFORE "'.$results[0]->date.'"');
				}
				
			}
			else{
				$uids = imap_sort($this->stream, $criterias[$_REQUEST['sort_option']],$_REQUEST['sort_val'],SE_UID);
			}
			//$uids = imap_sort($this->stream, $criterias[$_REQUEST['sort_option']],$_REQUEST['sort_val'],SE_UID);
			
			//$uids = imap_sort($this->stream, SORTFROM,$_REQUEST['sort_val'],SE_UID);
		}
		else if(trim($search)==''){
			//$uids = $this->search();
			//pre($MC);
			if(empty($pag_no)){
				$pag_no  = 1;
			}
			$totalMessages = $MC->Nmsgs;
			if($totalMessages>=$showMessages){
				$req_page = ($pag_no*10)/$showMessages;
				
				if($req_page<=1){
					$req_page = 1;
				}
				$req_page = (int) $req_page;
				
					$cur_lmt = ($totalMessages - ($showMessages * $req_page) )+1;
					$cur_offt = $totalMessages - (($showMessages * ($req_page-1)) );
				
				//$cur_lmt = 1;
				
				$results =imap_fetch_overview($this->stream,$cur_lmt.":".$cur_offt,0);
				$last_rec = count($results) -1;
				if($last_rec >10){
					$last_rec -=10;
				}else{
					$last_rec=0;
				}
				// $uids = imap_sort($this->stream, SORTDATE,$_REQUEST['sort_val'],SE_UID,'SINCE "'.$results[0]->date.'" BEFORE "'.$results[$last_rec]->date.'"');
				$uids = imap_sort($this->stream, SORTDATE,$_REQUEST['sort_val'],SE_UID,'SINCE "'.$results[$last_rec]->date.'"');
			}
			else{
				$uids = $this->search();
			}
			
		}
		else{
			//$uids = imap_sort($this->stream, SORTDATE,1,SE_UID);
			$uids = imap_search($this->stream, 'Text "'.$search.'"', SE_UID);
			rsort($uids);
		}
		$io = 1;
		$mailList['tot_cnt'] =  0;
		$MC = imap_check($this->stream);
		$mailList['tot_cnt'] = $totalcnt =  $MC->Nmsgs;
		if($uids && ($_REQUEST['sort_val'] ==1 && $_REQUEST['sort_option']=='date')) {
			//$mailList['tot_cnt'] = $totalcnt = count($uids);
			if(!empty($pag_no)){
				$rm_val = (($pag_no -1) * 10) ;
				$uids = array_slice($uids, $rm_val); 
				$io = (($pag_no -1) * 10) + 1;
				$req_val = (($pag_no-1) * 10) + 10;
			}
			$uids = array_slice($uids, 0, 10);
		}
		else{
			//$mailList['tot_cnt'] = $totalcnt = count($uids);
			$io = (($pag_no -1) * 10) + 1;
			$req_val = (($pag_no-1) * 10) + 10;
			$uids = array_slice($uids, $req_val, 10);
		}
		if($uids){
			foreach ($uids as $uid)
			{
				
				//else{
				//if($io <= $req_val && !empty($pag_no)) {
					if(empty($this->CI->session->userdata('msg_1'.$uid)) || trim($search) != '' || $_REQUEST['folder'] != 'INBOX'){
						$inboxEmails = $this->get_message($uid);
						if(trim($search) == '' && $_REQUEST['folder'] == 'INBOX'){
							$req_data['msg_1'.$uid] = $inboxEmails;
							//$this->CI->session->set_userdata($req_data);
						}
					}
					else{
						$inboxEmails = $this->CI->session->userdata('msg_1'.$uid);
					}
					if(!empty($inboxEmails)){
				/*	if($inboxEmails['from']['name'] != '') {
						$fromname = $inboxEmails['from']['name'];
					}else {*/
						$fromname = $inboxEmails['from']['email'];
					//}
					$toname = '';
					foreach($inboxEmails['to'] as $to) {
						$toname .= $to['email'].',';
					}
					//echo "<pre>"; print_r($inboxEmails); exit;
					$toname = substr($toname, 0, -1);

					$CI          = & get_instance();
					$sQuery = "select name from tblprojects where id = (select rel_id from tbltasks where source_from = '".$inboxEmails['uid']."' limit 0,1) ";
					$rResult = $CI->db->query($sQuery)->result_array();
					if(!isset($rResult[0]['name'])){
						$rResult[0]['name'] = '';
					}

					// check lead connncted
					$this->CI->db->where('uid',$inboxEmails['uid']);
					$this->CI->db->where('message_id',$inboxEmails['message_id']);
					$local_mail =$this->CI->db->get(db_prefix().'localmailstorage')->row();
					$connect_rel_data ='';
					if($local_mail){
						if($local_mail->deal_id){
							$this->CI->db->where('deleted_status',0);
							$this->CI->db->where('id',$local_mail->deal_id);
							$deal =$this->CI->db->get(db_prefix().'projects')->row();
							$connect_rel_data ='<a href="#" onClick="updatedeal('.$inboxEmails['uid'].');">'.htmlentities($deal->name).' (Deal)</a>';
						}elseif($local_mail->lead_id){
							$this->CI->db->where('deleted_status',0);
							$this->CI->db->where('id',$local_mail->lead_id);
							$lead =$this->CI->db->get(db_prefix().'leads')->row();
							$connect_rel_data ='<a target="_blank" href="'.admin_url('leads/lead/'.$lead->id).'">'.htmlentities($lead->name).' (Lead)</a>';
						}
					}
					if($inboxEmails['read'] !=1){
						$output .= '<tr class="'.$uid.'_mail_row unread_col_col"><td><input type="checkbox" name="mails[]" class="check_mail" onclick="check_header()" value="'.$uid.'"></td>';
						//$output .= '<td class="name"><i class="fa fa-circle"></i></td>';
					}else{
						$output .= '<tr class="'.$uid.'_mail_row read_col_col"><td><input type="checkbox" name="mails[]" class="check_mail" onclick="check_header()" value="'.$uid.'"></td>';
						//$output .= '<td class="name"></td>';
					}
					if($_REQUEST['folder'] != '[Gmail]/Sent Mail' && $_REQUEST['folder'] != '[Gmail]/Drafts'){
						$output .= '<td class="name"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.$fromname.'</a></td>';
					}
					else{
						$output .= '<td class="name"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.$toname.'</a></td>';
					}
					$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.substr($inboxEmails['subject'],0,30).'</a></td>';
					//$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.substr(strip_tags(str_replace('[image: Google]','',$inboxEmails['body']['plain'])),0,30).'...</a></td>';
					$output .= '<td class="subject">'.$connect_rel_data.'</td>';
					
					if(!empty($inboxEmails['attachments'])){
						$output .= '<td><a href="'.admin_url('company_mail/download_attachment/'.$inboxEmails['uid']).'?folder='.$_REQUEST['folder'].'" onclick="download_attachment('.$inboxEmails['uid'].')"><i class="fa fa-paperclip" aria-hidden="true"></i></a></td>';
					}else{
						$output .= '<td></td>';
					}
					$output .= '<td class="time"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.date("D, d M Y h:i A",$inboxEmails['udate']).'</a></td></tr>';
					if($io == $req_val || $io == $totalcnt) {
						$mailList['table'] = $output;
						$mailList['field'] = '<input type="hidden" id="folder" value="'.$_REQUEST['folder'].'"><input type="hidden" id="uid" value="'.$inboxEmails['uid'].'">';
						return $mailList; 
						exit;
					}
				/*}
				elseif($io>$req_val){
					break;
				}*/
				//}
				$io++;
			}
			}
		} else {
			$mailList['table'] = '<tr><td colspan="4" style="text-align:center;">No Emails Found.</td></tr>';
			$mailList['field'] = '<input type="hidden" id="folder" value="Inbox"><input type="hidden" id="uid" value="'.$_REQUEST['uid'].'">';
			return $mailList; 
			exit;
		}
		sort($folders);
		return $folders;
	}
	public function downloadfile($attachements){
		$req_files = array();
		$i = 0;
		foreach($attachements as $attachement1){
			$ch_content = $attachement1['content'];
			$file = $req_files[$i] = 'uploads/'.$attachement1['name'];
			file_put_contents($file, $ch_content);
			/*$file = $req_files[$i] = $attachement1['name'];
			$txt = fopen('uploads/'.$file, "w") or die("Unable to open file!");
			fwrite($txt,$ch_content);
			fclose($txt);*/
			$i++;
		}
		if(count($attachements)>1){
			$this->CI->load->library('zip');
			foreach ($req_files as $req_file1) {
				$this->CI->zip->read_file( $req_file1);
				unlink(FCPATH.'uploads/'.$req_file1);
			}
			$this->CI->zip->download('files.zip');
			$this->CI->zip->clear_data();
		}
		else{
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			header("Content-Type: text/plain");
			readfile($file);
			unlink(FCPATH.'uploads/'.$file);
		}

	}
	public function downloadfile_single($attachements){
		$req_files = array();
		$ch_content = $attachements['content'];
		$file = 'uploads/'.$attachements['name'];
		file_put_contents($file, $ch_content);
		//$txt = fopen('uploads/'.$file, "w") or die("Unable to open file!");
		//fwrite($txt,$ch_content);
		//fclose($txt);
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		header("Content-Type: text/plain");
		readfile($file);
		unlink(FCPATH.'uploads/'.$file);

	}
	public function download_attachment($config,$email_number,$folder){
		$username = $config['username'];
		$password = $config['password'];
		$this->mailbox = '{' . $config['host'] . ':'.$config['port'].'/imap/ssl/novalidate-cert}'.$folder;
		$this->stream  = imap_open($this->mailbox, $config['username'], $config['password'])or die('Cannot connect to mail: ' . pr(imap_errors()));
		$inboxEmails = $this->get_message($email_number);
		if(!empty($inboxEmails['attachments'])){
			$this->downloadfile($inboxEmails['attachments']);
		}
		
	}
	public function download_attachment_single($config,$email_number,$folder,$attach_id){
		$username = $config['username'];
		$password = $config['password'];
		$this->mailbox = '{' . $config['host'] . ':'.$config['port'].'/imap/ssl/novalidate-cert}'.$folder;
		$this->stream  = imap_open($this->mailbox, $config['username'], $config['password'])or die('Cannot connect to mail: ' . pr(imap_errors()));
		$inboxEmails = $this->get_message($email_number);
		if(!empty($inboxEmails['attachments'][$attach_id])){
			$this->downloadfile_single($inboxEmails['attachments'][$attach_id]);
		}
		
	}
	public function unread_message(){
		
		$mailList = [];
		$config = $this->config;
		$username = $config['username'];
		$password = $config['password'];
		$this->mailbox = '{' . $config['host'] . ':'.$config['port'].'/imap/ssl/novalidate-cert}';
		$this->stream  = imap_open($this->mailbox, $config['username'], $config['password'])or die('Cannot connect to mail: ' . pr(imap_errors()));;
		$folders = imap_list($this->stream, $this->mailbox, '*');
		$req_count = array();
		$mailList['folders'] = $this->get_subfolders(str_replace($this->mailbox, '', $folders));
		
		//$mbox_info1 = imap_status($this->stream,"{".$config['host'].":".$config['port']."/imap/ssl/novalidate-cert}".$_REQUEST['folder'], SA_MESSAGES);
		//$req_count['current_folder'] = $mbox_info1->messages;
		if(!empty($mailList['folders'])){
			foreach($mailList['folders'] as $folder12){
				//$mbox = imap_open("{".$config['host'].":993/imap/ssl/novalidate-cert}".$folder12,$username,$password);
				//$mbox_info = imap_mailboxmsginfo($mbox);
				//$mbox = imap_open("{".$config['host'].":993/imap/ssl/novalidate-cert}".$folder12,$username,$password);
				$mbox_info = imap_status($this->stream,"{".$config['host'].':'.$config['port']."/imap/ssl/novalidate-cert}".$folder12, SA_UNSEEN);
				


				if($mbox_info){
					$req_count[$folder12] = $mbox_info->unseen;
				//	$req_count[$folder12]['overall'] = $mbox_info1->messages;
				}
				
			}
			
		}
		//imap_close($this->stream );

		return $req_count;
	}
	public function get_inbox_email()
	{ 
		//echo "<pre>"; print_r($_POST); exit;
		$cache_id = 'email_addresses';
		if (($cache = $this->get_cache($cache_id)) !== false)
		{
			return $cache;
		}
		

		if($_REQUEST['folder'] == 'Inbox') {
			$current_folder = $this->folder;
		} elseif($_REQUEST['folder'] == 'All Mail') {
			$current_folder = '[Gmail]/All Mail';
		} elseif($_REQUEST['folder'] == 'Sent Mail') {
			$current_folder = '[Gmail]/Sent Mail';
		} elseif($_REQUEST['folder'] == 'Trash') {
			$current_folder = '[Gmail]/Trash';
		} elseif($_REQUEST['folder'] == 'Drafts') {
			$current_folder = '[Gmail]/Drafts';
		} elseif($_REQUEST['folder'] == 'Important') {
			$current_folder = '[Gmail]/Important';
		} elseif($_REQUEST['folder'] == 'Starred') {
			$current_folder = '[Gmail]/Starred';
		} elseif($_REQUEST['folder'] == 'Spam') {
			$current_folder = '[Gmail]/Spam';
		} elseif($_REQUEST['folder'] == 'Bin') {
			$current_folder = '[Gmail]/Bin';
		} else {
			$current_folder = $_REQUEST['folder'];
		}
		
		$mailList = [];
		$output = '';
		
		//echo "<pre>"; print_r($folder); exit;
		$this->select_folder($current_folder);
		$uids = $this->search();
		$io = 1;
		$j = 1;
		if($uids) {
			$totalcnt = count($uids);
			foreach ($uids as $uid)
			{
				if($_REQUEST['uid']) {
					
					//echo $_REQUEST['uid']; exit;
					if($io <= 10 && $uid < $_REQUEST['uid']) {
						if($io == 1) {
							$remaincnt = $totalcnt - $j;
						}
						$inboxEmails = $this->get_message($uid);
						if($_REQUEST['folder'] == 'Sent Mail') {
							$fromname = '';
							foreach($inboxEmails['to'] as $to) {
								$fromname .= $to['email'].',';
							}
							//echo "<pre>"; print_r($inboxEmails); exit;
							$fromname = substr($fromname, 0, -1);
						} else {
							if($inboxEmails['from']['name'] != '') {
								$fromname = $inboxEmails['from']['name'];
							} else {
								$fromname = $inboxEmails['from']['email'];
							}
							$toname = '';
							foreach($inboxEmails['to'] as $to) {
								$toname .= $to['email'].',';
							}
							//echo "<pre>"; print_r($inboxEmails); exit;
							$toname = substr($toname, 0, -1);
						}
						//echo "<pre>"; print_r($inboxEmails); exit;
						$CI          = & get_instance();
						$sQuery = "select name from tblprojects where id = (select rel_id from tbltasks where source_from = '".$inboxEmails['uid']."')";
						$rResult = $CI->db->query($sQuery)->result_array();
						
						if($_REQUEST['folder'] == 'Sent Mail') {
							$output .= '<tr><td class="name"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities($fromname).'</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities(substr($inboxEmails['subject'],0,30)).'</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities(substr(strip_tags(str_replace('[image: Google]','',$inboxEmails['body']['plain'])),0,30)).'...</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="updatedeal('.$inboxEmails['uid'].');">'.htmlentities($rResult[0]['name']).'</a></td>';
							$output .= '<td class="time"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.date("d-M-Y h:i A",$inboxEmails['udate']).'</a></td></tr>';
						} else {
							$output .= '<tr><td class="name"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities($fromname).'</a></td>';
							$output .= '<td class="name"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities($toname).'</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities(substr($inboxEmails['subject'],0,30)).'</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities(substr(strip_tags(str_replace('[image: Google]','',$inboxEmails['body']['plain'])),0,30)).'...</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="updatedeal('.$inboxEmails['uid'].');">'.htmlentities($rResult[0]['name']).'</a></td>';
							$output .= '<td class="time"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.date("d-M-Y h:i A",$inboxEmails['udate']).'</a></td></tr>';
						}
						if($io == 10 || $io == $remaincnt) {
							$mailList['table'] = $output;
							$mailList['field'] = '<input type="hidden" id="folder" value="'.$_REQUEST['folder'].'"><input type="hidden" id="uid" value="'.$inboxEmails['uid'].'">';
							return $mailList; 
							exit;
						}
						$io++;
					}
					$j++;
				} else {
					if($io <= 10) {
						$inboxEmails = $this->get_message($uid);
						if($_REQUEST['folder'] == 'Sent Mail') {
							$fromname = '';
							foreach($inboxEmails['to'] as $to) {
								$fromname .= $to['email'].',';
							}
							//echo "<pre>"; print_r($inboxEmails); exit;
							$fromname = substr($fromname, 0, -1);
						} else {
							if($inboxEmails['from']['name'] != '') {
								$fromname = $inboxEmails['from']['name'];
							} else {
								$fromname = $inboxEmails['from']['email'];
							}
							$toname = '';
							foreach($inboxEmails['to'] as $to) {
								$toname .= $to['email'].',';
							}
							//echo "<pre>"; print_r($inboxEmails); exit;
							$toname = substr($toname, 0, -1);
						}
						if($io == 1) {
							if($_REQUEST['folder'] == 'Sent Mail')
								$output .= '<tr><th style="width:10%;"><b>To</b></th><th><b>Subject</b></th><th><b>Content</b></th><th><b>Deals</b></th><th><b>Date</b></th></tr>';
							else
								$output .= '<tr><th><b>From</b></th><th><b>To</b></th><th><b>Subject</b></th><th><b>Content</b></th><th><b>Deals</b></th><th><b>Date</b></th></tr>';
						}
						$CI          = & get_instance();
						$sQuery = "select name from tblprojects where id = (select rel_id from tbltasks where source_from = '".$inboxEmails['uid']."')";
						$rResult = $CI->db->query($sQuery)->result_array();
						if($_REQUEST['folder'] == 'Sent Mail') {
							$output .= '<tr><td class="name"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities($fromname).'</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities(substr($inboxEmails['subject'],0,30)).'</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities(substr(strip_tags(str_replace('[image: Google]','',$inboxEmails['body']['plain'])),0,30)).'...</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="updatedeal('.$inboxEmails['uid'].');">'.htmlentities($rResult[0]['name']).'</a></td>';
							$output .= '<td class="time"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.date("d-M-Y h:i A",$inboxEmails['udate']).'</a></td></tr>';
						} else {
							$output .= '<tr><td class="name"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities($fromname).'</a></td>';
							$output .= '<td class="name"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities($toname).'</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities(substr($inboxEmails['subject'],0,30)).'</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities(substr(strip_tags(str_replace('[image: Google]','',$inboxEmails['body']['plain'])),0,30)).'...</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="updatedeal('.$inboxEmails['uid'].');">'.htmlentities($rResult[0]['name']).'</a></td>';
							$output .= '<td class="time"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.date("d-M-Y h:i A",$inboxEmails['udate']).'</a></td></tr>';
						}
						if($io == 10 || $io == $totalcnt) {
							$mailList['table'] = $output;
							$mailList['field'] = '<input type="hidden" id="folder" value="'.$_REQUEST['folder'].'"><input type="hidden" id="uid" value="'.$inboxEmails['uid'].'">';
							//echo "<pre>"; print_r($mailList); exit;
							return $mailList; 
							exit;
						}
					}
					$io++;
				}
			}
		} else {
			$mailList['table'] = '<tr><td colspan="4" style="text-align:center;">No Emails Found.</td></tr>';
			$mailList['field'] = '<input type="hidden" id="folder" value="'.$_REQUEST['folder'].'"><input type="hidden" id="uid" value="'.$_REQUEST['uid'].'">';
			return $mailList; 
			exit;
		}
	}
	
	public function get_company_inbox_email()
	{ 
		//echo "<pre>"; print_r($_POST); exit;
		$cache_id = 'email_addresses';
		if (($cache = $this->get_cache($cache_id)) !== false)
		{
			return $cache;
		}
		

		if($_REQUEST['folder'] == 'Inbox') {
			$current_folder = $this->folder;
		} elseif($_REQUEST['folder'] == 'All Mail') {
			$current_folder = '[Gmail]/All Mail';
		} elseif($_REQUEST['folder'] == 'Sent Mail') {
			$current_folder = '[Gmail]/Sent Mail';
		} elseif($_REQUEST['folder'] == 'Trash') {
			$current_folder = '[Gmail]/Trash';
		} elseif($_REQUEST['folder'] == 'Drafts') {
			$current_folder = '[Gmail]/Drafts';
		} elseif($_REQUEST['folder'] == 'Important') {
			$current_folder = '[Gmail]/Important';
		} elseif($_REQUEST['folder'] == 'Starred') {
			$current_folder = '[Gmail]/Starred';
		} elseif($_REQUEST['folder'] == 'Spam') {
			$current_folder = '[Gmail]/Spam';
		} elseif($_REQUEST['folder'] == 'Bin') {
			$current_folder = '[Gmail]/Bin';
		} else {
			$current_folder = $_REQUEST['folder'];
		}
		
		$mailList = [];
		$output = '';
		
		//echo "<pre>"; print_r($folder); exit;
		$this->select_folder($current_folder);
		$uids = $this->search();
		$io = 1;
		$j = 1;
		if($uids) {
			$totalcnt = count($uids);
			foreach ($uids as $uid)
			{
				if($_REQUEST['uid']) {
					
					//echo $_REQUEST['uid']; exit;
					if($io <= 10 && $uid < $_REQUEST['uid']) {
						if($io == 1) {
							$remaincnt = $totalcnt - $j;
						}
						$inboxEmails = $this->get_message($uid);
						if($_REQUEST['folder'] == 'Sent Mail') {
							$fromname = '';
							foreach($inboxEmails['to'] as $to) {
								$fromname .= $to['email'].',';
							}
							//echo "<pre>"; print_r($inboxEmails); exit;
							$fromname = substr($fromname, 0, -1);
						} else {
							if($inboxEmails['from']['name'] != '') {
								$fromname = $inboxEmails['from']['name'];
							} else {
								$fromname = $inboxEmails['from']['email'];
							}
							$toname = '';
							foreach($inboxEmails['to'] as $to) {
								$toname .= $to['email'].',';
							}
							//echo "<pre>"; print_r($inboxEmails); exit;
							$toname = substr($toname, 0, -1);
						}
						//echo "<pre>"; print_r($inboxEmails); exit;
						$CI          = & get_instance();
						$sQuery = "select name from tblprojects where id = (select rel_id from tbltasks where source_from = '".$inboxEmails['uid']."')";
						$rResult = $CI->db->query($sQuery)->result_array();
						
						if($_REQUEST['folder'] == 'Sent Mail') {
							$output .= '<tr class="'.$uid.'_mail_row"><td><input type="checkbox" name="mails[]" class="check_mail" onclick="check_header()" value="'.$uid.'"></td>';
							if($inboxEmails['read'] !=1){
								$output .= '<td class="name"><i class="fa fa-circle"></i></td>';
							}else{
								$output .= '<td class="name"></td>';
							}
							$output .= '<td class="name"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities($toname).'</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities(substr($inboxEmails['subject'],0,30)).'</a></td>';
							//$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities(substr(strip_tags(str_replace('[image: Google]','',$inboxEmails['body']['plain'])),0,30)).'...</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="updatedeal('.$inboxEmails['uid'].');">'.htmlentities($rResult[0]['name']).'</a></td>';
							$output .= '<td></td><td class="time"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.date("d-M-Y h:i A",$inboxEmails['udate']).'</a></td></tr>';
						} else {
							$output .= '<tr class="'.$uid.'_mail_row"><td><input type="checkbox" name="mails[]" class="check_mail" onclick="check_header()" value="'.$uid.'"></td>';
							if($inboxEmails['read'] !=1){
								$output .= '<td class="name"><i class="fa fa-circle"></i></td>';
							}else{
								$output .= '<td class="name"></td>';
							}
							$output .= '<td class="name"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities($fromname).'</a></td>';
							//$output .= '<td class="name"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities($toname).'</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities(substr($inboxEmails['subject'],0,30)).'</a></td>';
							//$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities(substr(strip_tags(str_replace('[image: Google]','',$inboxEmails['body']['plain'])),0,30)).'...</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="updatedeal('.$inboxEmails['uid'].');">'.htmlentities($rResult[0]['name']).'</a></td>';
							$output .= '<td></td><td class="time"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.date("d-M-Y h:i A",$inboxEmails['udate']).'</a></td></tr>';
						}
						if($io == 10 || $io == $remaincnt) {
							$mailList['table'] = $output;
							$mailList['field'] = '<input type="hidden" id="folder" value="'.$_REQUEST['folder'].'"><input type="hidden" id="uid" value="'.$inboxEmails['uid'].'">';
							return $mailList; 
							exit;
						}
						$io++;
					}
					$j++;
				} else {
					if($io <= 10) {
						$inboxEmails = $this->get_message($uid);
						if($_REQUEST['folder'] == 'Sent Mail') {
							$fromname = '';
							foreach($inboxEmails['to'] as $to) {
								$fromname .= $to['email'].',';
							}
							//echo "<pre>"; print_r($inboxEmails); exit;
							$fromname = substr($fromname, 0, -1);
						} else {
							if($inboxEmails['from']['name'] != '') {
								$fromname = $inboxEmails['from']['name'];
							} else {
								$fromname = $inboxEmails['from']['email'];
							}
							$toname = '';
							foreach($inboxEmails['to'] as $to) {
								$toname .= $to['email'].',';
							}
							//echo "<pre>"; print_r($inboxEmails); exit;
							$toname = substr($toname, 0, -1);
						}
						if($io == 1) {
							if($_REQUEST['folder'] == 'Sent Mail')
								$output .= '<tr><th style="width:10%;"><input type="checkbox" id="select_all" onclick="check_all(this)"></th><th><b>Unread Icon</b></th><th><b>To</b></th><th><b>Subject</b></th><th><b>Deals</b></th><th><b>Attachement Icon</b></th><th><b>Date</b></th></tr>';
							else
								$output .= '<tr><th style="width:10%;"><input type="checkbox" id="select_all" onclick="check_all(this)"></th><th><b>Unread Icon</b></th><th><b>From</b></th><th><b>Subject</b></th><th><b>Deals</b></th><th><b>Attachement Icon</b></th><th><b>Date</b></th></tr>';
						}
						$CI          = & get_instance();
						$sQuery = "select name from tblprojects where id = (select rel_id from tbltasks where source_from = '".$inboxEmails['uid']."')";
						$rResult = $CI->db->query($sQuery)->result_array();
						if($_REQUEST['folder'] == 'Sent Mail') {
							$output .= '<tr class="'.$uid.'_mail_row"><td><input type="checkbox" name="mails[]" class="check_mail" onclick="check_header()" value="'.$uid.'"></td>';
							if($inboxEmails['read'] !=1){
								$output .= '<td class="name"><i class="fa fa-circle"></i></td>';
							}else{
								$output .= '<td class="name"></td>';
							}
							$output .= '<td class="name"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities($toname).'</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities(substr($inboxEmails['subject'],0,30)).'</a></td>';
							//$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities(substr(strip_tags(str_replace('[image: Google]','',$inboxEmails['body']['plain'])),0,30)).'...</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="updatedeal('.$inboxEmails['uid'].');">'.htmlentities($rResult[0]['name']).'</a></td>';
							$output .= '<td></td><td class="time"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.date("d-M-Y h:i A",$inboxEmails['udate']).'</a></td></tr>';
						} else {
							$output .= '<tr class="'.$uid.'_mail_row"><td><input type="checkbox" name="mails[]" class="check_mail" onclick="check_header()" value="'.$uid.'"></td>';
							if($inboxEmails['read'] !=1){
								$output .= '<td class="name"><i class="fa fa-circle"></i></td>';
							}else{
								$output .= '<td class="name"></td>';
							}
							$output .= '<td class="name"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities($fromname).'</a></td>';
							//$output .= '<td class="name"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities($toname).'</a></td>';
							$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities(substr($inboxEmails['subject'],0,30)).'</a></td>';
							//$output .= '<td class="subject"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.htmlentities(substr(strip_tags(str_replace('[image: Google]','',$inboxEmails['body']['plain'])),0,30)).'...</a></td>';
							$output .= '<td></td><td class="subject"><a href="#" onClick="updatedeal('.$inboxEmails['uid'].');">'.htmlentities($rResult[0]['name']).'</a></td>';
							$output .= '<td class="time"><a href="#" onClick="getMessage('.$inboxEmails['uid'].');">'.date("d-M-Y h:i A",$inboxEmails['udate']).'</a></td></tr>';
						}
						if($io == 10 || $io == $totalcnt) {
							$mailList['table'] = $output;
							$mailList['field'] = '<input type="hidden" id="folder" value="'.$_REQUEST['folder'].'"><input type="hidden" id="uid" value="'.$inboxEmails['uid'].'">';
							//echo "<pre>"; print_r($mailList); exit;
							return $mailList; 
							exit;
						}
					}
					$io++;
				}
			}
		} else {
			$mailList['table'] = '<tr><td colspan="4" style="text-align:center;">No Emails Found.</td></tr>';
			$mailList['field'] = '<input type="hidden" id="folder" value="'.$_REQUEST['folder'].'"><input type="hidden" id="uid" value="'.$_REQUEST['uid'].'">';
			return $mailList; 
			exit;
		}
	}

	public function getmessage()
	{ 
		//echo "<pre>"; print_r($_REQUEST); exit;
		$cache_id = 'email_addresses';
		if (($cache = $this->get_cache($cache_id)) !== false)
		{
			return $cache;
		}
		if($_REQUEST['folder'] == 'Inbox') {
			$current_folder = $this->folder;
		} elseif($_REQUEST['folder'] == 'All Mail') {
			$current_folder = '[Gmail]/All Mail';
		} elseif($_REQUEST['folder'] == 'Sent Mail') {
			$current_folder = '[Gmail]/Sent Mail';
		} elseif($_REQUEST['folder'] == 'Trash') {
			$current_folder = '[Gmail]/Trash';
		} elseif($_REQUEST['folder'] == 'Drafts') {
			$current_folder = '[Gmail]/Drafts';
		} elseif($_REQUEST['folder'] == 'Important') {
			$current_folder = '[Gmail]/Important';
		} elseif($_REQUEST['folder'] == 'Starred') {
			$current_folder = '[Gmail]/Starred';
		} elseif($_REQUEST['folder'] == 'Spam') {
			$current_folder = '[Gmail]/Spam';
		} elseif($_REQUEST['folder'] == 'Bin') {
			$current_folder = '[Gmail]/Bin';
		} else {
			$current_folder = $_REQUEST['folder'];
		}
		
		$mailList = [];
		$output = '';
		
		//echo "<pre>"; print_r($folder); exit;
		$this->select_folder($current_folder);
		
		$inboxEmails = $this->get_message($_REQUEST['uid']);
		//echo "<pre>"; print_r($inboxEmails); exit;
		$output .= '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h4 class="modal-title"><i class="fa fa-envelope"></i> '.$inboxEmails['subject'].'</h4></div>';
		$output .= '<div class="modal-body"><div class="email-app"><main class="message"><div class="details">';
		//$output .= '<div class="title">'.$inboxEmails['subject'].'</div>';
		$output .= '<div class="header"><div class="from"><span>'.$inboxEmails['from']['email'].'</span>'.$inboxEmails['from']['email'].'</div><div class="date">'.date("d-M-Y H:i A",$inboxEmails['udate']).'</div></div>';
		$output .= '<div class="content">'.$inboxEmails['body']['html'].'</div>';
		$output .= '</div></main></div></div>';
		
		$mailList['body'] = $output;
		return $mailList; 
		exit;
	}
	public function company_content($to_email='',$reply='',$current_folder='INBOX')
	{ 
		$this->select_folder($current_folder);
		$inboxEmails = $this->get_message($_REQUEST['uid']);
		$req_to = $inboxEmails['from']['email'].',';
		if(!empty($inboxEmails['to']) && $reply=='all'){
			foreach($inboxEmails['to'] as $req_mail1){
				if($req_mail1['email'] != $to_email){
					$req_to .= $req_mail1['email'].',';
				}
			}
		}
		if(!empty($inboxEmails['cc'])  && $reply=='all'){
			foreach($inboxEmails['cc'] as $cc_mail1){
				if($cc_mail1['email'] != $to_email){
					$req_to .= $cc_mail1['email'].',';
				}
			}
		}
		$req_to = rtrim($req_to,',');
		
		$data = array('subject'=>$inboxEmails['subject'],'message'=>$inboxEmails['body']['html'],'from_address'=>$inboxEmails['from']['email'],'to_address'=>$req_to,'uid'=>$_REQUEST['uid']);
		return $data; 
		exit;
	}
	
	public function company_getmessage()
	{ 
		//echo "<pre>"; print_r($_REQUEST); exit;
		$cache_id = 'email_addresses';
		if (($cache = $this->get_cache($cache_id)) !== false)
		{
			return $cache;
		}
		if($_REQUEST['folder'] == 'Inbox') {
			$current_folder = $this->folder;
		} elseif($_REQUEST['folder'] == 'All Mail') {
			$current_folder = '[Gmail]/All Mail';
		} elseif($_REQUEST['folder'] == 'Sent Mail') {
			$current_folder = '[Gmail]/Sent Mail';
		} elseif($_REQUEST['folder'] == 'Trash') {
			$current_folder = '[Gmail]/Trash';
		} elseif($_REQUEST['folder'] == 'Drafts') {
			$current_folder = '[Gmail]/Drafts';
		} elseif($_REQUEST['folder'] == 'Important') {
			$current_folder = '[Gmail]/Important';
		} elseif($_REQUEST['folder'] == 'Starred') {
			$current_folder = '[Gmail]/Starred';
		} elseif($_REQUEST['folder'] == 'Spam') {
			$current_folder = '[Gmail]/Spam';
		} elseif($_REQUEST['folder'] == 'Bin') {
			$current_folder = '[Gmail]/Bin';
		} else {
			$current_folder = $_REQUEST['folder'];
		}
		
		$mailList = [];
		$output = '';		
		$this->select_folder($current_folder);
		$inboxEmails = $this->get_message($_REQUEST['uid']);
		$add_content = "'".$_REQUEST['uid']."'";	
		$output .= '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h4 class="modal-title"><i class="fa fa-envelope"></i> '.$inboxEmails['subject'].'</h4></div>';
		$output .= '<div class="modal-body">';
		
		$this->CI->db->where('uid',$inboxEmails['uid']);
		$this->CI->db->where('message_id',$inboxEmails['message_id']);
		if(!$this->CI->db->get(db_prefix().'localmailstorage')->row()){
			$linked_deals_leads =render_deal_lead_list_by_email($inboxEmails['from']['email']);
			$output .='
				<div class="row" id="linktowrapper">
					<div class="col-md-12">
						<h5>Link to Deal or Lead</h5>
						<div class="form-inline">
							<input type="hidden" id="linktouid" value="'.$inboxEmails['uid'].'" >';
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
				<h3>'.$inboxEmails['subject'].'</h3>
			</div>
			<div class="emailViewerMeta">
				<div class="row">
					<div class="col-md-6">
						<p class="no-margin" style="font-size: 13px;">From : <a>'.$inboxEmails['from']['email'].'</a></p>
						<p class="no-margin" style="font-size: 13px;">To : <a>'.$inboxEmails['to'][0]['email'].'</a></p>
						<p class="no-margin" style="font-size: 13px;">'.date("d-M-Y H:i A",$inboxEmails['udate']).'</p>
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
			if(!empty($inboxEmails['attachments'])){
				foreach($inboxEmails['attachments'] as $attachement12){
					$downoad_url = admin_url('company_mail/download_attachment_single/'.$inboxEmails['uid']).'?folder='.$_REQUEST['folder'].'&attach_id='.$j1;
					$output .= '<a class="btn btn-default mright5"  href="'.$downoad_url.'"><i class="fa fa-download" aria-hidden="true"></i> '.$attachement12['name'].'</a>';
					$j1++;
				}
			}
			if($j1>1){
				$downoad_url = admin_url('company_mail/download_attachment/'.$inboxEmails['uid']).'?folder='.$_REQUEST['folder'];
					
				$output .= '<a class="btn btn-default"  href="'.$downoad_url.'"><i class="fa fa-download" aria-hidden="true"></i> Download All</a>';
			}
			$output .='</div>';
			$output .='<div class="emailViewerBody" style="margin-top:20px">'.$inboxEmails['body']['html'].'</div>';

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
		return $mailList; 
		exit;
	}

	/**
	 * Get all email addresses from all messages
	 *
	 * @return array Array with all email addresses
	 */
	public function get_all_email_addresses($imapconf)
	{ 
		$cache_id = 'email_addresses';
		if (($cache = $this->get_cache($cache_id)) !== false)
		{
			return $cache;
		}
		//$folders = imap_list($this->stream, $this->mailbox, '*');
		
		$current_folder = $this->folder;
		
		$contacts       = [];
		foreach ($this->get_compay_folders($imapconf) as $folder)
		{
			//echo "<pre>"; print_r($folder); exit;
			$this->select_folder('[Gmail]/Sent Mail');

			$uids = $this->search();
			$io = 0;
			foreach ($uids as $uid)
			{
				//echo $uid; exit;
				
				$msg = $this->get_message($uid);
				// As we get the messages uid's ordering by newest we do not
				// need to replace the name if we already have the most recent
				$contacts[$msg['from']['email']] = isset($contacts[$msg['from']['email']])
												   ? $contacts[$msg['from']['email']]
												   : $msg['from']['name'];

				foreach (['to', 'cc', 'bcc'] as $field)
				{
					foreach ($msg[$field] as $i)
					{
						$contacts[$i['email']] = isset($contacts[$i['email']])
												 ? $contacts[$i['email']]
												 : $i['name'];
					}
				}
				
				$io++; 
				
			}
			
		}

		ksort($contacts);
		$this->select_folder($current_folder);

		$this->set_cache($cache_id, $contacts);

		return $contacts;
	}

	/**
	 * Return content of messages attachment
	 * Save the attachment in a optional path or get the binary code in the content index
	 *
	 * @param integer $uid   Message uid
	 * @param integer $index Index of the attachment - 0 to the first attachment
	 *
	 * @return array|boolean False if attachment could not be get
	 */
	public function get_attachment(int $uid, int $index = 0)
	{
		$cache_id = $this->folder . ':message_' . $uid . ':attachment_' . $index;

		if (($cache = $this->get_cache($cache_id)) !== false)
		{
			return $cache;
		}

		$id         = imap_msgno($this->stream, $uid);
		$structure  = imap_fetchstructure($this->stream, $id);
		$attachment = $this->_get_attachments($uid, $structure, '', $index);

		$this->set_cache($cache_id, $attachment);

		if (empty($attachment))
		{
			return false;
		}

		return $attachment;
	}

	/**
	 * [get_attachments description]
	 *
	 * @param integer $uid
	 * @param array   $indexes
	 *
	 * @return array
	 */
	public function get_attachments(int $uid, array $indexes = [])
	{
		$attachments = [];

		foreach ($indexes as $index)
		{
			$attachments[] = $this->get_attachment($uid, (int)$index);
		}

		return $attachments;
	}

	/**
	 * [_get_attachments description]
	 *
	 * @param integer      $uid
	 * @param object       $structure
	 * @param string       $part_number
	 * @param integer|null $index
	 * @param boolean      $with_content
	 *
	 * @return array
	 */
	protected function _get_attachments(int $uid, $structure, string $part_number = '',	int $index = null)
	{
		$id          = imap_msgno($this->stream, $uid);
		$attachments = [];

		if (isset($structure->parts))
		{
			foreach ($structure->parts as $key => $sub_structure)
			{
				$new_part_number = empty($part_number) ? $key + 1 : $part_number . '.' . ($key + 1);

				$results = $this->_get_attachments($uid, $sub_structure, $new_part_number);

				if (count($results))
				{
					if (isset($results[0]['name']))
					{
						foreach ($results as $result)
						{
							array_push($attachments, $result);
						}
					}
					else
					{
						array_push($attachments, $results);
					}
				}

				// If we already have the given indexes return here
				if (! is_null($index) && isset($attachments[$index]))
				{
					return $attachments[$index];
				}
			}
		}
		else
		{
			$attachment = [];

			if (isset($structure->dparameters[0]) && !empty($structure->dparameters[0]))
			{
				$bodystruct   = imap_bodystruct($this->stream, $id, $part_number);
				if(!empty($bodystruct->dparameters[0]->value)){
					$decoded_name = imap_mime_header_decode($bodystruct->dparameters[0]->value);
					$filename     = $this->convert_to_utf8($decoded_name[0]->text);
					$content      = imap_fetchbody($this->stream, $id, $part_number);
					$content      = (string)$this->struc_decoding($content, $bodystruct->encoding);

					$attachment = [
						'name'         => (string)$filename,
						'part_number'  => (string)$part_number,
						'encoding'     => (int)$bodystruct->encoding,
						'size'         => (int)$structure->bytes,
						'reference'    => isset($bodystruct->id) ? (string)$bodystruct->id : '',
						'disposition'  => (string)strtolower($structure->disposition),
						'type'         => (string)strtolower($structure->subtype),
						'content'      => $content,
						'content_size' => strlen($content),
					];
				}
			}

			return $attachment;
		}

		return $attachments;
	}

	/**
	 * [struc_decoding description]
	 *
	 * @param string  $text
	 * @param integer $encoding
	 *
	 * @see http://php.net/manual/pt_BR/function.imap-fetchstructure.php
	 *
	 * @return string
	 */
	protected function struc_decoding(string $text, int $encoding = 5)
	{
		switch ($encoding)
		{
			case ENC7BIT: // 0 7bit
				return $text;
			case ENC8BIT: // 1 8bit
				return imap_8bit($text);
			case ENCBINARY: // 2 Binary
				return imap_binary($text);
			case ENCBASE64: // 3 Base64
				return imap_base64($text);
			case ENCQUOTEDPRINTABLE: // 4 Quoted-Printable
				return quoted_printable_decode($text);
			case ENCOTHER: // 5 other
				return $text;
			default:
				return $text;
		}
	}

	protected function get_default_folder(string $type,$imapconf=array())
	{
		//echo $type;exit;
		foreach ($this->get_compay_folders($imapconf) as $folder)
		{
			//if (strtolower($folder) === strtolower($this->config['folders'][$type]))
			if (strtolower($folder) === strtolower($type))
			{
				return $folder;
			}
		}

		// No folder found? Create one
		$this->add_folder($this->config['folders'][$type]);

		return $this->config['folders'][$type];
	}

	protected function get_inbox_folder()
	{
		return $this->get_default_folder('inbox');
	}

	protected function get_trash_folder($imapconf)
	{
		return $this->get_default_folder('[Gmail]/Trash',$imapconf);
	}

	protected function get_sent_folder()
	{
		return $this->get_default_folder('sent');
	}

	protected function get_spam_folder()
	{
		return $this->get_default_folder('spam');
	}

	protected function get_draft_folder()
	{
		return $this->get_default_folder('draft');
	}

	/**
	 * Create the final message array
	 *
	 * @param integer $uid          Message uid
	 * @param boolean $with_body    Define if the output will get the message body
	 * @param boolean $embed_images Define if message body will show embeded images
	 *
	 * @return array|boolean
	 */
	public function get_message(int $uid)
	{
		 $cache_id = $this->folder . ':message_' . $uid;

		if (($cache = $this->get_cache($cache_id)) !== false)
		{
			return $cache;
		}	
		// TODO: Maybe put this check before try get from cache
		// then we will know if the msg already exists
		$id = imap_msgno($this->stream, $uid);

		// If id is zero the message do not exists
		if ($id === 0)
		{
			return false;
		}

		$header = imap_headerinfo($this->stream, $id);

		// Check Priority
		preg_match('/X-Priority: ([\d])/mi', imap_fetchheader($this->stream, $id), $matches);
		$priority = isset($matches[1]) ? $matches[1] : 3;

		$subject = '';

		if (isset($header->subject) && strlen($header->subject) > 0)
		{
			foreach (imap_mime_header_decode($header->subject) as $decoded_header)
			{
				$subject .= $decoded_header->text;
			}
		}

		$email = [
			'id'          => (int)$id,
			'uid'         => (int)$uid,
			'from'        => isset($header->from[0]) ? (array)$this->to_address($header->from[0]) : [],
			'to'          => isset($header->to) ? (array)$this->array_to_address($header->to) : [],
			'cc'          => isset($header->cc) ? (array)$this->array_to_address($header->cc) : [],
			'bcc'         => isset($header->bcc) ? (array)$this->array_to_address($header->bcc) : [],
			'reply_to'    => isset($header->reply_to) ? (array)$this->array_to_address($header->reply_to) : [],
			//'return_path' => isset($header->return_path) ? (array)$this->array_to_address($header->return_path) : [],
			'message_id'  => $header->message_id,
			'in_reply_to' => isset($header->in_reply_to) ? (string)$header->in_reply_to : '',
			'references'  => isset($header->references) ? explode(' ', $header->references) : [],
			'date'        => $header->date,//date('c', strtotime(substr($header->date, 0, 30))),
			'udate'       => (int)$header->udate,
			'subject'     => $this->convert_to_utf8($subject),
			'priority'    => (int)$priority,
			'recent'      => strlen(trim($header->Recent)) > 0,
			'read'        => strlen(trim($header->Unseen)) < 1,
			'answered'    => strlen(trim($header->Answered)) > 0,
			'flagged'     => strlen(trim($header->Flagged)) > 0,
			'deleted'     => strlen(trim($header->Deleted)) > 0,
			'draft'       => strlen(trim($header->Draft)) > 0,
			'size'        => (int)$header->Size,
			'attachments' => (array)$this->_get_attachments($uid, imap_fetchstructure($this->stream, $id)),
			'body'        => $this->get_body($uid),
		];

		$email = $this->embed_images($email);

		for ($i = 0; $i < count($email['attachments']); $i++)
		{
			if ($email['attachments'][$i]['disposition'] !== 'attachment')
			{
				unset($email['attachments'][$i]);
			}
		}
		$this->set_cache($cache_id, $email);
		return $email;
	}

	/**
	 * Get messages
	 *
	 * @param array|string $uids         Array list of uid's or a comma separated list of uids
	 * @param boolean      $with_body    Define if the output will get the message body
	 * @param boolean      $embed_images Define if message body will show embeded images
	 *
	 * @return array|boolean
	 */
	public function get_messages($uids)
	{
		$messages = [];

		if (empty($uids))
		{
			return false;
		}

		if (is_string($uids))
		{
			$uids = explode(',', $uids);
		}

		foreach ($uids as $uid)
		{
			$messages[] = $this->get_message((int)$uid);
		}

		return $messages;
	}

	/**
	 * [get_eml description]
	 *
	 * @param integer $uid [description]
	 *
	 * @see https://stackoverflow.com/questions/7496266/need-to-save-a-copy-of-email-using-imap-php-and-then-can-be-open-in-outlook-expr
	 *
	 * @return string      [description]
	 */
	public function get_eml(int $uid)
	{
		$headers = imap_fetchheader($this->stream, $uid, FT_UID | FT_PREFETCHTEXT);
		$body    = imap_body($this->stream, $uid, FT_UID);

		return $headers . "\n" . $body;
	}

	public function fun(string $function, ...$params)
	{
		array_unshift($params, $this->stream);

		return call_user_func_array("imap_{$function}", $params);
	}

	/**
	 * [get_threads description]
	 *
	 * @see https://stackoverflow.com/questions/16248448/php-creating-a-multidimensional-array-of-message-threads-from-a-multidimensional
	 *
	 * @return array
	 */
	public function get_threads()
	{
		$thread = imap_thread($this->stream, SE_UID);
		$items  = [];

		foreach ($thread as $key => $uid)
		{
			$item = explode('.', $key);

			$node = (int)$item[0];

			$items[$node]['node'] = $node;


			switch ($item[1]) {
				case 'num':
					$items[$node]['num'] = $uid;
					$message = $this->get_message($uid);
					$items[$node]['msg'] = $message['subject'] . ' - ' . $message['date'];
					break;
				case 'next':
					$items[$node]['next'] = $uid; // node id
					break;
				case 'branch':
					$items[$node]['branch'] = $uid; // node id
					break;
			}
		}

		return $items;
	}

	/**
	 * Paginate uid's returning messages by "page" number
	 *
	 * @param array   $uids
	 * @param integer $page     Starts with 1
	 * @param integer $per_page
	 *
	 * @return array
	 */
	public function paginate(array $uids, int $page = 1, int $per_page = 10)
	{
		if (count($uids) < $per_page * $page)
		{
			return [];
		}

		return $this->get_messages(array_slice($uids, $per_page * $page - $per_page, $per_page));
	}

	/**
	 * Embed inline images in HTML Body
	 *
	 * @param array $email The email message
	 *
	 * @return array
	 */
	protected function embed_images(array $email)
	{
		foreach ($email['attachments'] as $key => $attachment)
		{
			if ($attachment['disposition'] === 'inline' && ! empty($attachment['reference']))
			{
				$reference = str_replace(['<', '>'], '', $attachment['reference']);
				$img_embed = 'data:image/' . $attachment['type'] . ';base64,' . base64_encode($attachment['content']);

				$email['body']['html'] = str_replace('cid:' . $reference, $img_embed, $email['body']['html']);
			}
		}

		return $email;
	}

	/**
	 * [search description]
	 *
	 * @param string  $search_criteria
	 *                                 ALL - return all messages matching the rest of the criteria
	 *                                 ANSWERED - match messages with the \\ANSWERED flag set
	 *                                 BCC "string" - match messages with "string" in the Bcc: field
	 *                                 BEFORE "date" - match messages with Date: before "date"
	 *                                 BODY "string" - match messages with "string" in the body of the message
	 *                                 CC "string" - match messages with "string" in the Cc: field
	 *                                 DELETED - match deleted messages
	 *                                 FLAGGED - match messages with the \\FLAGGED (sometimes referred to as Important or Urgent) flag set
	 *                                 FROM "string" - match messages with "string" in the From: field
	 *                                 KEYWORD "string" - match messages with "string" as a keyword
	 *                                 NEW - match new messages
	 *                                 OLD - match old messages
	 *                                 ON "date" - match messages with Date: matching "date"
	 *                                 RECENT - match messages with the \\RECENT flag set
	 *                                 SEEN - match messages that have been read (the \\SEEN flag is set)
	 *                                 SINCE "date" - match messages with Date: after "date"
	 *                                 SUBJECT "string" - match messages with "string" in the Subject:
	 *                                 TEXT "string" - match messages with text "string"
	 *                                 TO "string" - match messages with "string" in the To:
	 *                                 UNANSWERED - match messages that have not been answered
	 *                                 UNDELETED - match messages that are not deleted
	 *                                 UNFLAGGED - match messages that are not flagged
	 *                                 UNKEYWORD "string" - match messages that do not have the keyword "string"
	 *                                 UNSEEN - match messages which have not been read yet
	 * @param string  $sort_by
	 *                             One of:
	 *                             'date' = message Date
	 *                             'arrival' = arrival date
	 *                             'from' = mailbox in first From address
	 *                             'subject' = message subject
	 *                             'to' = mailbox in first To address
	 *                             'cc' = mailbox in first cc address
	 *                             'size' = size of message in octets
	 * @param boolean $descending
	 *
	 * @see http://php.net/manual/pt_BR/function.imap-sort.php
	 * @see http://php.net/manual/pt_BR/function.imap-search.php
	 *
	 * @return array Array of uid's matching the search criteria
	 */
	public function search(string $search_criteria = 'ALL', string $sort_by = 'date', bool $descending = true)
	{
		$search_criteria = $this->search_criteria . ' ' . $search_criteria;
//echo 'fds';exit;
		$this->search_criteria = 'SINCE "8 May 2021"';
		$criterias = [
			'date'    => SORTDATE, // message Date
			'arrival' => SORTARRIVAL, // arrival date
			'from'    => SORTFROM, // mailbox in first From address
			'subject' => SORTSUBJECT, // message subject
			'to'      => SORTTO, // mailbox in first To address
			'cc'      => SORTCC, // mailbox in first cc address
			'size'    => SORTSIZE, // size of message in octets
		];

		return imap_sort($this->stream, $criterias[$sort_by], (int)$descending, SE_UID, $search_criteria, 'UTF-8');
	}

	/**
	 * [search_body description]
	 *
	 * @param string $str
	 *
	 * @return Imap
	 */
	public function search_body($str)
	{
		$this->search_criteria .= ' BODY "' . $str . '"';

		return $this;
	}

	/**
	 * [search_body description]
	 *
	 * @param string $str
	 *
	 * @return Imap
	 */
	public function search_subject($str)
	{
		$this->search_criteria .= ' SUBJECT "' . $str . '"';

		return $this;
	}

	/**
	 * [search_body description]
	 *
	 * @param string|integer $str String on valid format (D, d M Y) or a timestamp
	 *
	 * @return Imap
	 */
	public function search_on_date($str)
	{
		if (is_numeric($str))
		{
			$str = date('D, d M Y', $str);
		}

		$this->search_criteria .= ' ON "' . $str . '"';

		return $this;
	}

	/**
	 * Return general folder statistics
	 *
	 * @param string $folder
	 *
	 * @return array
	 */
	public function get_folder_stats(string $folder = null)
	{
		$current_folder = $this->folder;

		if (isset($folder))
		{
			$this->select_folder($folder);
		}

		$stats = imap_mailboxmsginfo($this->stream);

		if ($stats)
		{
			$stats = [
				'unread'   => $stats->Unread,
				'deleted'  => $stats->Deleted,
				'messages' => $stats->Nmsgs,
				'size'     => $stats->Size,
				'Date'     => $stats->Date,
				'date'     => date('c', strtotime(substr($stats->Date, 0, 30))),
				'recent'   => $stats->Recent,
			];
		}

		if (isset($folder))
		{
			$this->select_folder($current_folder);
		}

		return $stats;
	}

	/**
	 * [convert_to_utf8 description]
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	protected function convert_to_utf8(string $str)
	{
		if (mb_detect_encoding($str, 'UTF-8, ISO-8859-1, GBK') !== 'UTF-8')
		{
			$str = utf8_encode($str);
		}

		$str = iconv('UTF-8', 'UTF-8//IGNORE', $str);

		return $str;
	}

	/**
	 * [array_to_address description]
	 *
	 * @param array $addresses
	 *
	 * @return array
	 */
	protected function array_to_address($addresses = [])
	{
		$formated = [];

		foreach ($addresses as $address)
		{
			$formated[] = $this->to_address($address);
		}

		return $formated;
	}

	/**
	 * [to_address description]
	 *
	 * @param object $headerinfos
	 *
	 * @return array
	 */
	protected function to_address($headerinfos)
	{
		$from = [
			'email' => '',
			'name'  => '',
		];

		if (isset($headerinfos->mailbox) && isset($headerinfos->host))
		{
			$from['email'] = $headerinfos->mailbox . '@' . $headerinfos->host;
		}

		if (! empty($headerinfos->personal))
		{
			$name         = imap_mime_header_decode($headerinfos->personal);
			$name         = $name[0]->text;
			$from['name'] = empty($name) ? '' : $this->convert_to_utf8($name);
		}

		return $from;
	}

	/**
	 * [get_body description]
	 *
	 * @param integer $uid
	 *
	 * @return array
	 */
	protected function get_body(int $uid)
	{
		return [
			'html'  => $this->get_part($uid, 'TEXT/HTML'),
			'plain' => $this->get_part($uid, 'TEXT/PLAIN'),
		];
	}

	/**
	 * [get_part description]
	 *
	 * @param integer        $uid
	 * @param string         $mimetype
	 * @param object|boolean $structure   The bodystruct or false to none
	 * @param string|boolean $part_number Part number or false to none
	 *
	 * @return string
	 */
	protected function get_part(int $uid, $mimetype = '', $structure = false, $part_number = '')
	{
		if (! $structure)
		{
			$structure = imap_fetchstructure($this->stream, $uid, FT_UID);
		}

		if ($structure)
		{
			if ($mimetype === $this->get_mime_type($structure))
			{
				if (! $part_number)
				{
					$part_number = '1';
				}

				$text = imap_fetchbody($this->stream, $uid, $part_number, FT_UID | FT_PEEK);

				return $this->struc_decoding($text, $structure->encoding);
			}

			if ($structure->type === TYPEMULTIPART) // 1 multipart
			{
				foreach ($structure->parts as $index => $subStruct)
				{
					$prefix = '';

					if ($part_number)
					{
						$prefix = $part_number . '.';
					}

					$data = $this->get_part($uid, $mimetype, $subStruct, $prefix . ($index + 1));

					if ($data)
					{
						return $data;
					}
				}
			}
		}

		return false;
	}

	/**
	 * [get_mime_type description]
	 *
	 * @param object $structure
	 *
	 * @see http://php.net/manual/pt_BR/function.imap-fetchstructure.php
	 *
	 * @return string
	 */
	protected function get_mime_type($structure)
	{
		$primary_body_types = [
			TYPETEXT        => 'TEXT',
			TYPEMULTIPART   => 'MULTIPART',
			TYPEMESSAGE     => 'MESSAGE',
			TYPEAPPLICATION => 'APPLICATION',
			TYPEAUDIO       => 'AUDIO',
			TYPEIMAGE       => 'IMAGE',
			TYPEVIDEO       => 'VIDEO',
			TYPEMODEL       => 'MODEL',
			TYPEOTHER       => 'OTHER',
		];

		if ($structure->ifsubtype)
		{
			return strtoupper($primary_body_types[(int)$structure->type] . '/' . $structure->subtype);
		}

		return 'TEXT/PLAIN';
	}

	/**
	 * [__destruct description]
	 */
	public function __destruct()
	{
		// TODO: Maybe is not necessary auto-close everytime
		// Analyze it
		if (is_resource($this->stream))
		{
			imap_errors();
			imap_close($this->stream);
		}
	}
	function array_flatten($array) { 
		  if (!is_array($array)) { 
			return FALSE; 
		  } 
		  $result = array(); 
		  foreach ($array as $key => $value) { 
			if (is_array($value)) { 
			  $result = array_merge($result, array_flatten($value)); 
			} 
			else { 
			  $result[$key] = $value; 
			} 
		  } 
		  return $result; 
		} 
	public function get_all_mails($staffid,$ch_deal='',$config='')
	{
		//$limit = 50;
		$mailList = [];
		$this->mailbox = '{' . $config['host'] . ':'.$config['port'].'/imap/ssl/novalidate-cert}';
		imap_timeout(IMAP_OPENTIMEOUT, 3);
		 imap_timeout(IMAP_READTIMEOUT, 3);
		 imap_timeout(IMAP_WRITETIMEOUT, 3);
		 imap_timeout(IMAP_CLOSETIMEOUT, 3);
		$this->stream  = imap_open($this->mailbox, $config['username'], $config['password'])or die('Cannot connect to mail: ' . pr(imap_errors()));;
		$folders = imap_list($this->stream, $this->mailbox, '*');
		$mailList['folders'] = $this->get_subfolders(str_replace($this->mailbox, '', $folders));
		$this->select_folder('[Gmail]/All Mail');
		$uids = $this->search();
		$req_uid = array_slice($uids, 0, 20);
		$CI          = & get_instance();
		$CI->db->reconnect();
		
		
		$sQuery = "select uid as source_from from ".db_prefix()."localmailstorage  where  staff_id = '".$staffid."' ";
		$rResults1 = $CI->db->query($sQuery)->result_array();
		$source_from = array();
		$source_from = array_column($rResults1, 'source_from'); 
		$rResults = array_diff($req_uid, $source_from);
		
		$sQuery1 = "select uid from ".db_prefix()."imapuid where folder='INBOX' and staff_id = '".$staffid."'";
		$CI->db->reconnect();
		$rResults12 = $CI->db->query($sQuery1)->result_array();
		$source_from1 = array();
		$source_from1 = array_column($rResults12, 'uid'); 
		$rResults = array_diff($rResults, $source_from1);
		$staff_fields = "staffid,email,firstname,lastname,facebook,linkedin,phonenumber,skype,password,datecreated,profile_image,last_ip,last_login,last_activity,last_password_change,new_pass_key,new_pass_key_requested,admin,role,designation,reporting_to,emp_id,action_for,active,default_language,direction,media_path_slug,is_not_staff,hourly_rate,two_factor_auth_enabled,two_factor_auth_code,two_factor_auth_code_requested,email_signature,deavite_re_assign,deavite_follow,deavite_follow_ids,login_fails,login_locked_on";
		$i = $j1 = 0;
		$req_msg = array();
		if(!empty($rResults)){
			$CI->db->reconnect();
			$sQuery12 = "select ".$staff_fields." from ".db_prefix()."staff where staffid = '".$staffid."'";
			$assignee_admin = $CI->db->query($sQuery12)->row();
			foreach($rResults as $uid1){
				$messages = $this->get_message($uid1);
				$CI->db->reconnect();
				$CI->db->insert(db_prefix() . 'imapuid',array('staff_id'=>$staffid,'uid'=>$messages['uid'],'folder'=>'INBOX'));
				if($messages['in_reply_to'] || ($messages['references'] && isset($message['references'][0]) && $message['references'][0])){
					if($messages['references']){
						$CI->db->where('message_id',$messages['references'][0]);
					}else{
						$CI->db->where('message_id',$messages['in_reply_to']);
					}
					$local_mail = $CI->db->get(db_prefix().'localmailstorage')->row();

					if($local_mail){
						if($local_mail->project_id){
							$rel_type ='project';
							$rel_id =$local_mail->project_id;
						}else{
							$rel_type ='lead';
							$rel_id =$local_mail->lead_id;
						}
						$CI->load->library('mails/imap_mailer');
						$CI->imap_mailer->set_rel_type($rel_type);
						$CI->imap_mailer->set_rel_id($rel_id);
						$CI->imap_mailer->connectEmail($messages);
					}
				}elseif(false){
					$req_project_id = get_deal_id_contactuser($messages['from']['email'],get_option('deal_map'));
					if(empty($req_project_id)){
						$req_project_id = get_deal_id_otheruser(get_option('deal_map'),$messages['to'],$messages['cc'],$messages['bcc']);
					}
					$req_project_id = json_decode($req_project_id);
					if(!empty($req_project_id) && !empty($messages['uid']) && !empty($messages['id'])){
						$ins_data['description'] = $messages['body']['html'];
						$ins_data['billable']	 = 1;
						$ins_data['tasktype']	 = 2;
						$ins_data['name']		 = $messages['subject'];
						$ins_data['startdate']	 = date('d-m-Y H:i:s');
						$ins_data['priority']	 = 1;
						$ins_data['rel_type']	 = 'project';
						$ins_data['rel_id']		 = $req_project_id->project_id;
						$ins_data['contacts_id'] = $req_project_id->contact_id;
						$ins_data['source_from'] = $uid1;
						$CI->db->reconnect();
						$task_fields = "id,name,tasktype,description,priority,dateadded,datemodified,startdate,duedate,datefinished,addedfrom,is_added_from_contact,status,send_reminder,recurring_type,repeat_every,recurring,is_recurring_from,cycles,total_cycles,custom_recurring,last_recurring_date,rel_id,rel_type,is_public,contacts_id,billable,billed,invoice_id,hourly_rate,milestone,kanban_order,milestone_order,visible_to_client,deadline_notified,source_from,imported_id,call_request_id,call_code,call_msg";
						$sQuery13 = "select ".$task_fields." from ".db_prefix()."tasks where source_from = '".$uid1."'";
						$tasks_data = $CI->db->query($sQuery13)->row();
						$data_assignee 			 = $assignee_admin->staffid;
						if(empty($tasks_data)){
							$CI->db->reconnect();
							$id   = $CI->tasks_model->add($ins_data);
							$assignData = [
									'taskid'  => $id,
									'staffid' => $data_assignee,
									];
							$CI->db->reconnect();
							$CI->db->insert(db_prefix() . 'task_assigned', $assignData);
						}
						else{
							$id = $tasks_data->id;
						}
						$CI->db->reconnect();
						$table_new1 = db_prefix() . 'projects';
						$CI->db->select('*');
						$CI->db->from($table_new1);
						$condition12 = array('id'=>$ins_data['rel_id']);
						$CI->db->where($condition12);
						$cur_project12 = $CI->db->get()->row();
						$ins_attachement = array_column($messages['attachments'], 'name'); 
						$req_msg[$i]['project_id']	= $ins_data['rel_id'];
						$req_msg[$i]['task_id']		= $id;
						$req_msg[$i]['assignee']	= $data_assignee;
						$req_msg[$i]['mailid']		= $messages['id'];
						$req_msg[$i]['uid'] 		= $messages['uid'];
						$req_msg[$i]['contacts_id']	= $ins_data['contacts_id'];
						if(!empty($cur_project12->teamleader)){
							$req_msg[$i]['staff_id'] 	= $cur_project12->teamleader;
						}
						else{
							$CI->db->select('*');
							$CI->db->where('project_id', $ins_data['rel_id']);
							$CI->db->where('is_primary', 1);
							$cur_project12 = $CI->db->get(db_prefix() . 'project_contacts')->row();
							//$cur_project12 = $this->projects_model->get_primary_project_contact($ch_project_id);
							$req_msg[$i]['staff_id'] 	= $cur_project12->contacts_id;
						}
						$req_msg[$i]['from_email'] 	= $messages['from']['email'];
						$req_msg[$i]['from_name'] 		= $messages['from']['name'];
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
						$req_msg[$i]['attachements']= json_encode($ins_attachement);
						$req_msg[$i]['body_html']	= $messages['body']['html'];
						$req_msg[$i]['body_plain']	= $messages['body']['plain'];
						$req_msg[$i]['folder']		= 'INBOX';
						$CI->db->reconnect();
						$table = db_prefix() . 'localmailstorage';
						$CI->db->insert($table,$req_msg[$i]);
						$i++;
						
					}
					$uid_data[$j1]['folder']	= 'INBOX';
					$uid_data[$j1]['uid']		= $uid1;
					$uid_data[$j1]['staff_id']	= $staff_id;
					$uid_data = array('uid'=>$uid1,'staff_id'=>$staffid,'folder'=>'INBOX');
					$j1++;
				}
				
			}
		}
		
		if(!empty($req_msg)){
			return true;
		}
		return false;
	}

}
