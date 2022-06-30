<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AccountTransfer_model extends App_Model
{
    public static $hierarchyTree = array();
    public function __construct()
    {
        parent::__construct();
    }

    public function storeTransferHierarchy($data) {
        $this->db->insert(db_prefix() . 'transfer_history', $data);
        $insertedId = $this->db->insert_id();
        if($insertedId) {
            return true;
        }
    }

    function getOwnerHierarchyWithoutAdmin($id,$content) {
        // (do the required processing...)
        $this->db->where('role !=',1);
        $this->db->where('staffid',$id);
        $result = $this->db->get(db_prefix() . 'staff')->result_array();
        
        if($result) {
            foreach ($result as $val) {
                //Send Email
                $this->load->library('email');
                $smtpconf = array();
                $smtpsettings = $this->db->get(db_prefix() . 'options')->result_array();
                foreach($smtpsettings as $config) {
                    if($config['name'] == 'smtp_host')
                        $smtpconf['host'] = $config['value'];
                    if($config['name'] == 'smtp_encryption')
                        $smtpconf['encrypto'] = $config['value'];
                    if($config['name'] == 'smtp_username')
                        $smtpconf['username'] = $config['value'];
                    if($config['name'] == 'smtp_password')
                        $smtpconf['password'] = $config['value'];
                    if($config['name'] == 'smtp_port')
                        $smtpconf['port'] = $config['value'];
                }
                $smtpconf['validate'] = true;

                $this->email->initialize($smtpconf);

                $this->email->from($smtpconf['username'], 'Account Transfered');
                $this->email->to($val['email']);
                //$this->email->to('sathya.safari@gmail.com');
                $this->email->subject('Account Transfered');
                $this->email->message($content);

                if ($this->email->send()) {
                    $data['msg'] = 'Message has been sent';
                    // $data['msg'] = $mail->ErrorInfo;
                }
                    
                $this->getOwnerHierarchyWithoutAdmin($val['reporting_to'],$content);
            }
            return true;
        } else {
            return false;
        }
      }

    public function printOwnerHierarchyWithoutAdmin($id,$content) {
          //echo $id; exit;
        $staffids = $this->getOwnerHierarchyWithoutAdmin($id,$content);
        //pre($staffids);
        //return explode(',',$staffids);
        return true;
    }

    public function get_transfer_history() {
        $this->db->select('tbltransfer_history.*,(select firstname from tblstaff where staffid = tbltransfer_history.transfer_by) as t_by, (select firstname from tblstaff where staffid = tbltransfer_history.trans_from) as t_from, (select firstname from tblstaff where staffid = tbltransfer_history.trans_to) as t_to');
        return $result = $this->db->get(db_prefix() . 'transfer_history')->result_array();
    }

    public function getuserDetails($id) {
        
        $this->db->select('tblstaff.firstname, tblstaff.email, (select name from tbldesignations where designationid = tblstaff.designation) as desig');
        $this->db->where('tblstaff.staffid',$id);
        return $result = $this->db->get(db_prefix() . 'staff')->row();
    }

    public function get_transfer_history_byid($id) {
        $this->db->where('id',$id);
        return $result = $this->db->get(db_prefix() . 'transfer_history')->row();
    }

/**
 * Delete existing DealLossReasons details
**/
    public function delete_followers($id)
    {
        $this->db->where('emp_id', $id);
        $this->db->delete(db_prefix() . 'followers');
        if ($this->db->affected_rows() > 0) {
			return true;
        }
        return false;
    }

    public function addFollowers($data) {
        $this->db->where('emp_id', $data['emp_id']);
        $this->db->where('follower_id', $data['follower_id']);
        $result = $this->db->get(db_prefix() . 'followers')->row();
        //pre($result);
        if(!$result) {
            //pre($data);
            $this->db->insert(db_prefix() . 'followers', $data);
            $insertedId = $this->db->insert_id();
            if($insertedId) {
                return true;
            }
        }
        //return true;
    }

    public function get_staffs_whom_follow()
    {
        $this->db->where('active', 1);
        $this->db->where('action_for', 'Active');
        $this->db->order_by('firstname','asc');
        $emp = $this->db->get('staff')->result_array();
        return $emp;
    }

    public function get_to_staffs($empid)
    {
        $this->db->where('staffid !=', $empid);
        $this->db->where('active', 1);
        $this->db->where('action_for', 'Active');
        $this->db->order_by('firstname','asc');
        $emp = $this->db->get('staff')->result_array();
        return $emp;
    }

    public function addFollowersPermission($data) {
        $this->db->where('emp_id', $data['emp_id']);
        $this->db->delete(db_prefix() . 'followers_permission');
        
        $this->db->insert(db_prefix() . 'followers_permission', $data);
        $insertedId = $this->db->insert_id();
        if($insertedId) {
            return $insertedId;
        }
        return false;
    }

    public function check_followers_permission($id) {
        $this->db->where('emp_id', $id);
        if($_POST['assign'])
            $this->db->where('p_type', $_POST['assign']);
        return $this->db->get(db_prefix() . 'followers_permission')->row();
    }

    public function check_followers($id) {
        $this->db->where('emp_id', $id);
        return $this->db->get(db_prefix() . 'followers')->result_array();
    }
}