<?php

class Authorization extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function check_login()
	{
		if (!$this->session->userdata('loggeduserid')) {
			return false;
		}
		else {
			return true;
		}
	}
		
	public function check_auth()
	{
		if (!$this->check_login()) {
			redirect(site_url('adminLogin'));
		}
	}
}


?>