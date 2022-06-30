<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tasktype_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

/**
 * Get Task-types
**/
	public function getTasktypes()
    {
		$this->db->where('status', 'Active');
        return $this->db->get(db_prefix() . 'tasktype')->result_array();
    }
	
/**
 * Check Task-type exist
**/
	public function checkTasktypeexist($name)
    {
		$this->db->where('LOWER(name)', strtolower($name));
        return $this->db->get(db_prefix() . 'tasktype')->row();
    }
	
/**
 * View Task-type
**/
	public function getTasktype($id)
    {
		$this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'tasktype')->row();
    }
	
/** 
 * Add Task-type
**/
    public function addTasktype($data)
    {
        $data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by']  = get_staff_user_id();
        $this->db->insert(db_prefix() . 'tasktype', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New task-type added [Task-type-ID: '.$insert_id.']');
        }
        return $insert_id;
    }

/**
 * Update Task-type
**/
    public function updateTasktype($data, $id)
    {
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by']  = get_staff_user_id();
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tasktype', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Task-type Updated [Task-type-ID: ' . $id . ']');
            return true;
        }
        return false;
    }
	
/**
 * Delete Tasktype
**/
    public function deleteTasktype($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tasktype');
        if ($this->db->affected_rows() > 0) {
			log_activity('Task-type Deleted [Task-type-ID: ' . $id . ']');
			return true;
        }
        return false;
    }
}