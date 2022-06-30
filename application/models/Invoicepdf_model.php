<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Invoicepdf_model extends App_Model
{
    public static $hierarchyTree = array();
    public function __construct()
    {
        parent::__construct();
    }

    public function storeInvConfig($data) {
        $this->db->insert(db_prefix() . 'invoice_pdf_config', $data);
        $insertedId = $this->db->insert_id();
        if($insertedId) {
            return true;
        }
    }

    function get_invoice_config() {
        
        return $result = $this->db->get(db_prefix() . 'invoice_pdf_config')->row();
        
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