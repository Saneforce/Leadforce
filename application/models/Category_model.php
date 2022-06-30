<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Category_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get tax by id
     * @param  mixed $id tax id
     * @return mixed     if id passed return object else array
     */
    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix().'item_category')->row();
        }
        $this->db->order_by('id', 'desc');

        return $this->db->get(db_prefix().'item_category')->result_array();
    }

    /**
     * Add new tax
     * @param array $data tax data
     * @return boolean
     */
    public function add($data)
    {
        unset($data['categoryid']);
        
        $data['cat_name']    = trim($data['name']);
		unset($data['name']);
        $this->db->insert(db_prefix().'item_category', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Category Added [ID: ' . $insert_id . ', ' . $data['cat_name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Edit tax
     * @param  array $data tax data
     * @return boolean
     */
    public function edit($data)
    {
        $catid        = $data['categoryid'];
        unset($data['categoryid']);
        $data['cat_name']    = trim($data['name']);
		 unset($data['name']);
        $this->db->where('id', $catid);
        $this->db->update(db_prefix().'item_category', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Category Updated [ID: ' . $catid . ', ' . $data['cat_name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete Category from database
     * @param  mixed $id Category id
     * @return boolean
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'item_category');
        if ($this->db->affected_rows() > 0) {
            log_activity('Category Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }
}
