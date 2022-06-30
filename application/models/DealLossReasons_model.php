<?php

defined('BASEPATH') or exit('No direct script access allowed');

class DealLossReasons_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

/**
 * Get all active DealLossReasons details
**/
	public function getDealLossReasons()
    {
		$this->db->where('publishstatus', '1');
        return $this->db->get(db_prefix() . 'deallossreasons')->result_array();
    }
	
/**
 * Check DealLossReasons details exist by name
**/
	public function checkDealLossReasonsExist($name)
    {
		$this->db->where('LOWER(name)', strtolower($name));
        return $this->db->get(db_prefix() . 'deallossreasons')->row();
    }
	
/**
 * View existing DealLossReasons details
**/
	public function getDealLossReasonsbyId($id)
    {
		$this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'deallossreasons')->row();
    }
	
/** 
 * Add new DealLossReasons details
**/
    public function add_DealLossReasons($data)
    {
        $data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by']  = get_staff_user_id();
        $this->db->insert(db_prefix() . 'deallossreasons', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New DealLossReasons added [DealLossReasonsID: '.$insert_id.']');
        }
        return $insert_id;
    }

/**
 * Update existing DealLossReasons details
**/
    public function update_DealLossReasons($data, $id)
    {
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by']  = get_staff_user_id();
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'deallossreasons', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('DealLossReasons Updated [DealLossReasonsID: ' . $id . ']');
            return true;
        }
        return false;
    }
	
/**
 * Delete existing DealLossReasons details
**/
    public function delete_DealLossReasons($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'deallossreasons');
        if ($this->db->affected_rows() > 0) {
			log_activity('DealLossReasons Deleted [DealLossReasonsID: ' . $id . ']');
			return true;
        }
        return false;
    }

}