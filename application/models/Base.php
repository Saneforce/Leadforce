<?php

class Base extends CI_Model
{
	public function __construct()
	{
        parent::__construct();
    }

	public function insert($tableName,$data)
	{
        $this->db->insert($tableName,$data);
        if($this->db->affected_rows() > 0) {
			return true;
		}
		else{
			return false;
		}
    }

	public function insertId($tableName,$data)
	{
        $this->db->insert($tableName,$data);
        if($this->db->affected_rows() > 0) {
			return $this->db->insert_id();
		}
		else {
			return false;
		}
    }

    public function update($tableName,$data,$condition_array = array())
	{
		foreach($condition_array as $key => $val) {
			$this->db->where($key, $val); 
		}
		$this->db->update($tableName, $data);
		if($this->db->affected_rows() > 0) {
			return true;
		}
		else {
			if ($this->db->trans_status() === FALSE) {
				return false;
			}
			return true;
		}
	}
    
	public function getAll($tableName, $condition_array = array(), $order_by = array(), $limit = NULL, $page = 0)
	{
		if(isset($condition_array['OR_LIKE'])) {
			foreach($condition_array['OR_LIKE'] as $key => $val) {
				$this->db->or_like($key, $val); 
			}
			unset($condition_array['OR_LIKE']);
		}
		foreach($condition_array as $key => $val) {
			$this->db->where($key, $val); 
		}
		if(!empty($order_by)) {
			foreach($order_by as $field => $order) {
				$this->db->order_by($field, $order); 
			}
		}
		if(!empty($limit)){
			$this->db->limit($limit,$page);
		}
		$data = array();
		$result = $this->db->get($tableName);
		foreach($result->result() as $row) {
            if(isset($row->status)) {
                $row->status = $row->status==='0' ? false : true;
			}
            $data[] = $row;
		}
		return $data;
    }
	
	public function executeQuery($sql)
	{
        $result = $this->db->query($sql);
		return $result;
    }
	
	public function delete($tableName,$condition_array = array())
	{
		foreach($condition_array as $key => $val) {
			$this->db->where($key, $val); 
		}
		$this->db->delete($tableName);
		if($this->db->affected_rows() > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function filter_data($table, $data)
	{
		$filtered_data = array();
		$columns = $this->db->list_fields($table);
		if (is_array($data))
		{
			foreach ($columns as $column)
			{
				if (array_key_exists($column, $data)) {
					$filtered_data[$column] = $data[$column];
				}
			}
		}
		return $filtered_data;
	}

	public function send_notification($user_id, $message, $notification_type, $params = array())
	{
		$user = $this->getAll('fcm_tbl', array('fcm_token' => $user_id));
		if(empty($user)) {
			return false;
		}
		else {
			if(!empty($user[0]->fcm_token)) {
				$data = array();
				// Google cloud messaging FCM-API url
				$url = 'https://fcm.googleapis.com/fcm/send';
				$msg = array(
					'message' => $message,
					'notification_type' => $notification_type,
				);
				if(!empty($params)) {
					foreach($params as $key => $val) {
						$msg[$key] = $val;
					}
				}
				$fields = array();
				$fields['data'] = $msg;
				$fields['to'] = $user[0]->fcm_token;
				$headers = array(
					'Authorization: key=' . USER_SERVER_KEY,
					'Content-Type: application/json'
				);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
				$result = curl_exec($ch);
				if ($result === FALSE) {
					die('Curl failed: ' . curl_error($ch));
				}
				curl_close($ch);
				return $result;
			}
			else if(!empty($user[0]->ios_id)) {
				$deviceToken = $user[0]->ios_id;
				$ctx = stream_context_create();
				stream_context_set_option($ctx, 'ssl', 'local_cert', "ios_permission/Certificates.pem");
				stream_context_set_option($ctx, 'ssl', 'passphrase', '');
				// Open a connection to the APNS server
				$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
				if (!$fp) {
					exit("Failed to connect: $err $errstr" . PHP_EOL);
				}
				// Create the payload body
				$body['aps'] = array(
					'alert' => array(
						'title' => $subject,
						'body' => $message,
					),
					'sound' => 'default'
				);
				if(!empty($params)) {
					foreach($params as $key => $val) {
						if(in_array($key, array('chat_type', 'chat_status', 'chat_id'))) {
							$body[$key] = $val;
						}
						else {
							$body['chatinfo'][$key] = $val;
						}
					}
				}
				// Encode the payload as JSON
				$payload = json_encode($body);
				// Build the binary notification
				$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
				// Send it to the server
				$result = fwrite($fp, $msg, strlen($msg));
				//echo $result;
				// Close the connection to the server
				fclose($fp);
			}
		}
    }
	
	public function send_notification_wishlist($user_id, $message, $notification_type, $params = array())
	{
		$user = $this->getAll('fcm_tbl', array('fcm_token' => $user_id));
		if(empty($user)) {
			return false;
		}
		else {
			if(!empty($user[0]->fcm_token)) {
				$data = array();
				// Google cloud messaging FCM-API url
				$url = 'https://fcm.googleapis.com/fcm/send';
				$msg = array(
					'message' => $message,
					'notification_type' => $notification_type,
				);
				if(!empty($params)) {
					foreach($params as $key => $val) {
						$msg[$key] = $val;
					}
				}
				$fields = array();
				$fields['data'] = $msg;
				$fields['to'] = $user[0]->fcm_token;
				$headers = array(
					'Authorization: key=' . USER_SERVER_KEY,
					'Content-Type: application/json'
				);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
				$result = curl_exec($ch);
				if ($result === FALSE) {
					die('Curl failed: ' . curl_error($ch));
				}
				curl_close($ch);
				return $result;
			}
		}
    }
	
}