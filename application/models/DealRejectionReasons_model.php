<?php

defined('BASEPATH') or exit('No direct script access allowed');

class DealRejectionReasons_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

/**
 * Get all active DealRejectionReasons details
**/
	public function getDealRejectionReasons()
    {
		$this->db->where('publishstatus', '1');
        return $this->db->get(db_prefix() . 'dealrejectionreasons')->result_array();
    }
	
/**
 * Check DealRejectionReasons details exist by name
**/
	public function checkDealRejectionReasonsExist($name)
    {
		$this->db->where('LOWER(name)', strtolower($name));
        return $this->db->get(db_prefix() . 'dealrejectionreasons')->row();
    }
	
/**
 * View existing DealRejectionReasons details
**/
	public function getDealRejectionReasonsbyId($id)
    {
		$this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'dealrejectionreasons')->row();
    }
	
/** 
 * Add new DealRejectionReasons details
**/
    public function add_DealRejectionReasons($data)
    {
        $data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by']  = get_staff_user_id();
        $this->db->insert(db_prefix() . 'dealrejectionreasons', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New DealRejectionReasons added [DealRejectionReasonsID: '.$insert_id.']');
        }
        return $insert_id;
    }

/**
 * Update existing DealRejectionReasons details
**/
    public function update_DealRejectionReasons($data, $id)
    {
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by']  = get_staff_user_id();
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dealrejectionreasons', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('DealRejectionReasons Updated [DealRejectionReasonsID: ' . $id . ']');
            return true;
        }
        return false;
    }
	
/**
 * Delete existing DealRejectionReasons details
**/
    public function delete_DealRejectionReasons($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'dealrejectionreasons');
        if ($this->db->affected_rows() > 0) {
			log_activity('DealRejectionReasons Deleted [DealRejectionReasonsID: ' . $id . ']');
			return true;
        }
        return false;
    }

}