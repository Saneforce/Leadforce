<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Roles_model extends App_Model {

    /**
     * Add new employee role
     * @param mixed $data
     */
    public function add($data) {
        $permissions = [];
        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
        }

        $data['permissions'] = serialize($permissions);

        $this->db->insert(db_prefix() . 'roles', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Role Added [ID: ' . $insert_id . '.' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Update employee role
     * @param  array $data role data
     * @param  mixed $id   role id
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

        $this->db->where('roleid', $id);
        $this->db->update(db_prefix() . 'roles', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($update_staff_permissions == true) {
            $this->load->model('staff_model');

            $staff = $this->staff_model->get('', [
                'role' => $id,
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
     * Get employee role by id
     * @param  mixed $id Optional role id
     * @return mixed     array if not id passed else object
     */
    public function get($id = '') {
        if (is_numeric($id)) {

            $role = $this->app_object_cache->get('role-' . $id);

            if ($role) {
                return $role;
            }

            $this->db->where('roleid', $id);

            $role = $this->db->get(db_prefix() . 'roles')->row();
            $role->permissions = !empty($role->permissions) ? unserialize($role->permissions) : [];

            $this->app_object_cache->add('role-' . $id, $role);

            return $role;
        }

        return $this->db->get(db_prefix() . 'roles')->result_array();
    }

    /**
     * Delete employee role
     * @param  mixed $id role id
     * @return mixed
     */
    public function delete($id) {
        $current = $this->get($id);

        // Check first if role is used in table
        if (is_reference_in_table('role', db_prefix() . 'staff', $id)) {
            return [
                'referenced' => true,
            ];
        }

        $affectedRows = 0;
        $this->db->where('roleid', $id);
        $this->db->delete(db_prefix() . 'roles');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            log_activity('Role Deleted [ID: ' . $id);

            return true;
        }

        return false;
    }

    public function get_contact_permissions($id) {
        $this->db->where('userid', $id);

        return $this->db->get(db_prefix() . 'contact_permissions')->result_array();
    }

    public function get_role_staff($role_id) {
        $this->db->where('role', $role_id);

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
