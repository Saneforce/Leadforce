<?php

/**
 *  Description of DBModel
 *  Author : VGS_104
 */
class DBModel extends CI_Model {

    public function insert($tableName, $data) {
        $this->db->insert($tableName, $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        } else {
            $error = $this->db->last_query();
            return $error;
        }
    }

    public function update($tableName, $data, $condition_array = array()) {
        foreach ($condition_array as $col => $colvalue)
            if (is_array($colvalue)) {
                $this->db->where_in($col, $colvalue);
            } else {
                $this->db->where($col, $colvalue);
            }

        $this->db->update($tableName, $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            $error = $this->db->last_query();
            return $error;
        }
    }

    public function usersTokenCheck($condition_array = array()) {
        $access_token = '';
        if (isset($condition_array['access_token']))
            $access_token = $condition_array['access_token'];
			
        $this->db->where("CONCAT(md5(staffid), md5('####'), md5(email)) =", $access_token);
        $result = $this->db->get('tblstaff');
        $user = $result->row_array();
        if (count($user) > 0) {
            $user_data = [
                'staff_user_id'   => $user['staffid'],
                'staff_logged_in' => true,
            ];
            $this->session->set_userdata($user_data);
            //echo "<pre>"; print_r($_SESSION); exit;
            $user['access_token'] = $access_token;
            return $user;
        } else {
            return false;
        }
    }

    public function rowCheck($tableName, $condition_array = array()) {
        foreach ($condition_array as $col => $colvalue)
            $this->db->where($col, $colvalue);
        $result = $this->db->get($tableName);
        if ($result->num_rows() > 0)
            return true;
        else
            return false;
    }

    public function getAll($tableName, $condition_array = array(), $select_array = array()) {
        if (count($select_array) > 0) {
            foreach ($select_array as $col => $colvalue)
                $this->db->select($col . ' as ' . $colvalue);
        } else {
            $this->db->select('*');
        }
        foreach ($condition_array as $col => $colvalue)
            $this->db->where($col, $colvalue);

        $data = array();
        if ($tableName != 'job_rate')
            $this->db->where('dels', '0');
        $result = $this->db->get($tableName);
        foreach ($result->result() as $row) {
            if (isset($row->status))
                $row->status = $row->status === '0' ? FALSE : TRUE;
            $data[] = $row;
        }
        return $data;
    }

    public function getGridCountAll($tableName, $condition_array = array()) {
        $joinQueryTable = array();
        foreach ($condition_array as $col => $colvalue) {
            if (!empty($colvalue)) {
                $DropdownFieldArray = explode('_', $col);
                if ($DropdownFieldArray[0] == 'fk') {
                    if (strpos($colvalue, '_') !== false) {
                        $new_colvalue = explode('-', $colvalue);
                        $colvalue = $new_colvalue[0];
                        $sevalue = $new_colvalue[1];
                    } else {
                        $sevalue = 'id';
                    }

                    if (!in_array($DropdownFieldArray[1], $joinQueryTable)) {
                        $this->db->join($DropdownFieldArray[1] . ' ' . $DropdownFieldArray[1], $DropdownFieldArray[1] . '.id = ' . $tableName . '.' . $sevalue, 'left');
                        array_push($joinQueryTable, $DropdownFieldArray[1]);
                    }

                    $this->db->like($DropdownFieldArray[1] . '.' . $DropdownFieldArray[2], $colvalue, 'both');
                } else {
                    if (in_array($col, array('startdate', 'enddate', 'deadlineapplicants'))) {
                        $this->db->like('DATE_FORMAT(' . $this->db->dbprefix($tableName) . '.' . $col . ',"%d/%m/%Y")', $colvalue, 'both');
                    } elseif (in_array($col, array('applydate'))) {
                        $this->db->like('DATE_FORMAT(' . $this->db->dbprefix($tableName) . '.' . $col . ',"%D %b %y")', $colvalue, 'both');
                    } else {
                        $this->db->like($this->db->dbprefix($tableName) . '.' . $col, $colvalue, 'both');
                    }
//                    $this->db->like($tableName . '.' . $col, $colvalue, 'both');
                }
            }
        }
        $this->db->where($this->db->dbprefix($tableName) . '.dels', '0');
        $this->db->select('count(*) as num_rows');
        $result = $this->db->get($tableName . ' ' . $this->db->dbprefix($tableName))->row_array();
        return $result['num_rows'];
    }

    public function getGridAll($tableName, $condition_array = array()) {
        $joinQueryTable = array();
        if (isset($condition_array['select'])) {
            foreach ($condition_array['select'] as $Fkkey => $FKvalue) {
                $DropdownFieldArray = explode('_', $FKvalue);
                if ($DropdownFieldArray[0] == 'fk') {
                    $this->db->select($DropdownFieldArray[1] . '.' . $DropdownFieldArray[2] . ' as ' . $FKvalue);
                    if (!in_array($DropdownFieldArray[1], $joinQueryTable)) {
                        $this->db->join($DropdownFieldArray[1] . ' ' . $DropdownFieldArray[1], $this->db->dbprefix($tableName) . '.' . $FKvalue . '= ' . $DropdownFieldArray[1] . '.id', 'left');
                        array_push($joinQueryTable, $DropdownFieldArray[1]);
                    }
                } else {
                    if ($FKvalue == 'applydate') {
                        $selects = "DATE_FORMAT(" . $this->db->dbprefix($tableName) . "." . $FKvalue . ",'%D %b %y')as " . $FKvalue . "";
                        $this->db->select($selects, FALSE);
                    } else
                        $this->db->select($this->db->dbprefix($tableName) . '.' . $FKvalue);
                }
            }
        } else {
            $this->db->select('*');
        }

        if (isset($condition_array['filter'])) {
            foreach ($condition_array['filter'] as $col => $colvalue) {
                if ($colvalue != '') {
                    $DropdownFieldArray = explode('_', $col);
                    if ($DropdownFieldArray[0] == 'fk') {
                        $colvalue1 = (int) $colvalue;
                        if (!strcmp($colvalue1, $colvalue)) {
                            $this->db->where($DropdownFieldArray[1] . '.' . $DropdownFieldArray[2], $colvalue);
                        } else {
                            $this->db->like($DropdownFieldArray[1] . '.' . $DropdownFieldArray[2], $colvalue, 'both');
                        }
                    } else {
                        $colvalue1 = (int) $colvalue;
                        if (in_array($col, array('applydate'))) {
                            $this->db->like('DATE_FORMAT(' . $this->db->dbprefix($tableName) . '.' . $col . ',"%D %b %y")', $colvalue, 'both');
                        } elseif (in_array($col, array('reservationdate'))) {
                            $this->db->like('DATE_FORMAT(' . $this->db->dbprefix($tableName) . '.' . $col . ',"%d/%m/%Y")', $colvalue, 'both');
                        } elseif (!strcmp($colvalue1, $colvalue)) {
                            $this->db->where($tableName . '.' . $col, $colvalue);
                        } else {
                            $this->db->like($this->db->dbprefix($tableName) . '.' . $col, $colvalue, 'both');
                        }
                    }
                }
            }
        }

        if (isset($condition_array['sorting'])) {
            foreach ($condition_array['sorting'] as $col => $colvalue) {
                $DropdownFieldArray = explode('_', $col);
                if ($DropdownFieldArray[0] == 'fk') {
                    $this->db->order_by($DropdownFieldArray[1] . '.' . $DropdownFieldArray[2], $colvalue);
                } else {
                    $this->db->order_by($col, $colvalue);
                }
            }
        }



        if (isset($_POST['count']) && isset($_POST['page'])) {
            $params['count'] = isset($_POST['count']) ? $_POST['count'] : 10;
            $params['page'] = isset($_POST['page']) ? (($_POST['page'] - 1) * $params['count']) : 0;
            $this->db->limit($params['count'], ($params['page']));
        }

        $data = array();
        if ($tableName != 'job_rate')
            $this->db->where($this->db->dbprefix($tableName) . '.dels', '0');
        $this->db->from($tableName . ' ' . $this->db->dbprefix($tableName));
        $result = $this->db->get();
        foreach ($result->result() as $row) {
            if (isset($row->status)) {
                if ($tableName == 'apply')
                    $row->applystatus = $row->status;
                $row->status = $row->status === '0' ? FALSE : TRUE;
            }
            if (isset($row->cdate))
                $row->cdate = cdatedbton($row->cdate);
            if (isset($row->id))
                $row->ecodeid = md5($row->id);
            $data[] = $row;
        }
        return $data;
    }

    public function getCountAll($tableName, $condition_array = array()) {
        $joinQueryTable = array();
        foreach ($condition_array as $col => $colvalue) {
            if (!empty($colvalue)) {
                $colvalue1 = (int) $colvalue;
                if (!strcmp($colvalue1, $colvalue)) {
                    $this->db->where($tableName . '.' . $col, $colvalue);
                } else {
                    $this->db->like($tableName . '.' . $col, $colvalue, 'both');
                }
            }
        }
        $this->db->select(array('count(*) as count'));
        $this->db->where($this->db->dbprefix($tableName) . '.dels', '0');
        $result = $this->db->get($tableName . ' ' . $this->db->dbprefix($tableName));
        $results = $result->row_array();
        return ($results['count'] > 0) ? $results['count'] : 0;
    }

    public function executeQuery($sql) {
        $q = $this->db->query($sql);
        $data = array();
        foreach ($q->result() as $row) {
            if (isset($row->status))
                $row->status = $row->status === '0' ? FALSE : TRUE;
            $data[] = $row;
        }
        return $data;
    }

    public function delete($tableName, $condition_array = array(), $where = array()) {
        foreach ($condition_array as $col => $colvalue)
            $this->db->where($col, $colvalue);
        if (count($where) > 0) {
            $this->db->where_not_in('id', $where);
        }
        $this->db->delete($tableName);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            $error = $this->db->last_query();
            return $error;
        }
    }

}
