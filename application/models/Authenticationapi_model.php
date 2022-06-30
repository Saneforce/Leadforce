<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Authenticationapi_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  string Email address for login
     * @param  string User Password
     * @param  boolean Set cookies for user if remember me is checked
     * @param  boolean Is Staff Or Client
     * @return boolean if not redirect url found, if found redirect to the url
     */
    public function login($email, $password,$domain, $remember, $staff)
    {
        if ((!empty($email)) and (!empty($password)) and (!empty($domain))) {
			$domain1 = $domain;
			$req_domain = explode('.leadforce.mobi',$domain);	
			$this->load->model('base');
			$domain = $req_domain[0];
			$this->dynamicDB = array(	
				'hostname' => 'localhost',	
				'username' => 'root',	
				'password' => 'opc@345Pass',	
				'database' => 'dev_crm',	
				'dbdriver' => 'mysqli',	
				'dbprefix' => 'tbl',	
				'pconnect' => FALSE,	
				'db_debug' => TRUE,	
				'char_set' => 'utf8',	
				'dbcollat' => 'utf8_general_ci'	
			);
			$this->db2 = $this->load->database($this->dynamicDB, TRUE); 
			$this->db2->select('*');
			$this->db2->from('tblcompany');
			$query = $this->db2->get();
			$companies = $query->result_array();
		
			$companies1 = array_column($companies, 'shortcode'); 
			if(!in_array($domain,$companies1)){
				hooks()->do_action('failed_login_attempt', [
					'domain'            => $domain1,
				]);
				log_activity('Failed Login Attempt [domain: ' . $Domain1 . ', This domain not available , IP: ' . $this->input->ip_address() . ']');

				// Password failed, return
				return false;
			}
			$this->dynamicDB1 = array(	
				'hostname' => 'localhost',	
				'username' => 'root',	
				'password' => 'opc@345Pass',	
				'database' => 'dev_crm_'.$domain,	
				'dbdriver' => 'mysqli',	
				'dbprefix' => 'tbl',	
				'pconnect' => FALSE,	
				'db_debug' => TRUE,	
				'char_set' => 'utf8',	
				'dbcollat' => 'utf8_general_ci'	
			);
			$this->db3 = $this->load->database($this->dynamicDB1, TRUE); 
            $table = db_prefix() . 'contacts';
            $_id   = 'id';
            if ($staff == true) {
                $table = db_prefix() . 'staff';
                $_id   = 'staffid';
            }
            $this->db3->select("*,CONCAT(md5(staffid), md5('####'), md5(email)) as access_token");
            $this->db3->where('email', $email);
            $user = $this->db3->get($table)->row();
            
            if ($user) {
                // Email is okey lets check the password now
                if (!app_hasher()->CheckPassword($password, $user->password)) {
                    hooks()->do_action('failed_login_attempt', [
                        'user'            => $user,
                        'is_staff_member' => $staff,
                    ]);

                    log_activity('Failed Login Attempt [Email: ' . $email . ', Is Staff Member: ' . ($staff == true ? 'Yes' : 'No') . ', IP: ' . $this->input->ip_address() . ']');

                    // Password failed, return
                    return false;
                }
            } else {

                hooks()->do_action('non_existent_user_login_attempt', [
                        'email'           => $email,
                        'is_staff_member' => $staff,
                ]);

                log_activity('Non Existing User Tried to Login [Email: ' . $email . ', Is Staff Member: ' . ($staff == true ? 'Yes' : 'No') . ', IP: ' . $this->input->ip_address() . ']');

                return false;
            }

            if ($user->active == 0) {
                hooks()->do_action('inactive_user_login_attempt', [
                        'user'            => $user,
                        'is_staff_member' => $staff,
                ]);
                log_activity('Inactive User Tried to Login [Email: ' . $email . ', Is Staff Member: ' . ($staff == true ? 'Yes' : 'No') . ', IP: ' . $this->input->ip_address() . ']');

                return [
                    'memberinactive' => true,
                ];
            }
            unset($user->password);
            unset($user->new_pass_key);
            unset($user->new_pass_key_requested);
           
            return $user;
        }
		return false;
    }
 
     /**
     * @param  string Email from the user
     * @param  Is Client or Staff
     * @return boolean
     * Generate new password key for the user to reset the password.
     */
    public function forgot_password($email, $staff = false)
    {
        $table = db_prefix() . 'contacts';
        $_id   = 'id';
        if ($staff == true) {
            $table = db_prefix() . 'staff';
            $_id   = 'staffid';
        }
        $this->db->where('email', $email);
        $user = $this->db->get($table)->row();
        //echo "<pre>"; print_r($user); exit;
        if ($user) {
            if ($user->active == 0) {
                return [
                    'memberinactive' => true,
                ];
            }

            $new_pass_key = app_generate_hash();
            $this->db->where($_id, $user->$_id);
            $this->db->update($table, [
                'new_pass_key'           => $new_pass_key,
                'new_pass_key_requested' => date('Y-m-d H:i:s'),
            ]);

            if ($this->db->affected_rows() > 0) {
                $data['new_pass_key'] = $new_pass_key;
                $data['staff']        = $staff;
                $data['userid']       = $user->$_id;
                $merge_fields         = [];

                if ($staff == false) {
                    $sent = send_mail_template('customer_contact_forgot_password', $user->email, $user->userid, $user->$_id, $data);
                } else {
                    $sent = send_mail_template('staff_forgot_password', $user->email, $user->$_id, $data);
                }

                if ($sent) {
                    hooks()->do_action('forgot_password_email_sent', ['is_staff_member' => $staff, 'user' => $user]);

                    return true;
                }

                return false;
            }

            return false;
        }

        return false;
    }

    
}
