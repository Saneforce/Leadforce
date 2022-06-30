<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AssignFollowers_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
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
        $this->db->where('role !=', 1);
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