<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('authorization');
		$this->load->model('base');
		$this->load->helper('security');
		$this->load->helper('url');
	}
	
/**
 * Admin Login
**/
	public function adminLogin()
	{
		
		if ($this->session->userdata('loggeduserid')) {
			redirect(site_url('superadmin/company'));
		}
		$data = array();
		if(isset($_POST['submit']))
		{
			
			$this->load->library('form_validation');
			$this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'required');
			
			if ($this->form_validation->run() == FALSE)
			{
				$this->load->view("superadmin/login", $data);
			}
			else {
				$email	= $this->input->post('email');
				$password 	= $this->input->post('password');
			
				//check the username and password exist
				$query = $this->db->get_where('superadmin', array('email' => $email, 'password' => md5($password), 'status' => 1 ));
				
				if ($query->num_rows() > 0 ) {
					$row = $query->row(); 
					$session_data = array("loggeduserid" => $row->id, "loggeduseremail" => $email);
					$this->session->set_userdata($session_data);
					redirect(site_url('superadmin/company'));
				}
				else {
					$this->session->set_flashdata('errormessage','Invalid username or password');
					redirect(site_url('superadmin/login'));
				}
			}
		}
		else {
			$this->load->view("superadmin/login", $data);
		}
	}

/**
 * Admin Logout
**/
	public function adminLogout()
	{
		$this->session->sess_destroy();
		redirect(site_url('superadmin/login'));
	}
}