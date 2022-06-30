<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Designation_model extends App_Model {

    /**
     * Add new employee designation
     * @param mixed $data
     */
    public function add($data) {
        $permissions = [];
        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
        }

        $data['permissions'] = serialize($permissions);

        $this->db->insert(db_prefix() . 'designations', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Role Added [ID: ' . $insert_id . '.' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Update employee designation
     * @param  array $data designation data
     * @param  mixed $id   designation id
     * @return boolean
     */
    public function update($data, $id) {
        $affectedRows = 0;
        $permissions = [];
        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
        }

        $data['permissions'] = serialize($permissions);

        $update_staff_permissions = false;
        if (isset($data['update_staff_permissions'])) {
            $update_staff_permissions = true;
            unset($data['update_staff_permissions']);
        }

        $this->db->where('designationid', $id);
        $this->db->update(db_prefix() . 'designations', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($update_staff_permissions == true) {
            $this->load->model('staff_model');

            $staff = $this->staff_model->get('', [
                'designation' => $id,
            ]);

            foreach ($staff as $member) {
                if ($this->staff_model->update_permissions($permissions, $member['staffid'])) {
                    $affectedRows++;
                }
            }
        }

        if ($affectedRows > 0) {
            log_activity('Role Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Get employee designation by id
     * @param  mixed $id Optional designation id
     * @return mixed     array if not id passed else object
     */
    public function get($id = '') {
        if (is_numeric($id)) {

            $designation = $this->app_object_cache->get('designation-' . $id);

            if ($designation) {
                return $designation;
            }

            $this->db->where('designationid', $id);

            $designation = $this->db->get(db_prefix() . 'designations')->row();
            $designation->permissions = !empty($designation->permissions) ? unserialize($designation->permissions) : [];

            $this->app_object_cache->add('designation-' . $id, $designation);

            return $designation;
        }

        return $this->db->get(db_prefix() . 'designations')->result_array();
    }

    /**
     * Delete employee designation
     * @param  mixed $id designation id
     * @return mixed
     */
    public function delete($id) {
        $current = $this->get($id);

        // Check first if designation is used in table
        if (is_reference_in_table('designation', db_prefix() . 'staff', $id)) {
            return [
                'referenced' => true,
            ];
        }

        $affectedRows = 0;
        $this->db->where('designationid', $id);
        $this->db->delete(db_prefix() . 'designations');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            log_activity('Role Deleted [ID: ' . $id);

            return true;
        }

        return false;
    }

    public function get_designation_roles($id) {
        $this->db->where('designationid', $id);
        $designation = $this->db->get(db_prefix() . 'designations')->row();
        return $designation->roleid;
    }

    public function get_contact_permissions($id) {
        $this->db->where('userid', $id);

        return $this->db->get(db_prefix() . 'contact_permissions')->result_array();
    }

    public function get_designation_staff($designation_id) {
        $this->db->where('designation', $designation_id);

        return $this->db->get(db_prefix() . 'staff')->result_array();
    }

    public function get_modules_permissions() {
        return $this->db->order_by('features_label')->get(db_prefix() . 'modules_permissions')->result_array();
    }

    public function get_modules_permissions_not_active() {
        $mpa = $this->db->query('SELECT GROUP_CONCAT(features) as mpa FROM `' . db_prefix() . 'modules_permissions`  where active = "0" GROUP by active')->row_array();
        return isset($mpa['mpa']) ? explode(',', $mpa['mpa']) : array();
    }

    public function is_feature_enable($feature = '') {
        $mpa = $this->db->query('SELECT * FROM `' . db_prefix() . 'modules_permissions`  where active = "0" and features = "'.$feature.'"')->row_array();
        return isset($mpa['id']) ? false : true;
    }
    
    public function update_modules_permissions($datainfo = array()) {
        $this->db->update(db_prefix() . 'modules_permissions', array('active' => '0'));
        if (isset($datainfo['mp']) && count($datainfo['mp'])) {
            foreach ($datainfo['mp'] as $key => $value) {
                $this->db->where('id', $key);
                $this->db->update(db_prefix() . 'modules_permissions', array('active' => "1"));
            }
        }
    }

}
