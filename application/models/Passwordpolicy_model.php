<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Passwordpolicy_model extends App_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getPasswordPolicy() {
        $this->db->where('name', 'password_policy');
        $query =$this->db->get(db_prefix() . 'options')->row();
        if($query){
            return json_decode($query->value);
        }
        return false;
    }

    public function updatePasswordPolicy($data) {
        if(!$this->getPasswordPolicy()){
            $this->db->insert(db_prefix() . 'options', ['name'=>'password_policy','value'=>$data]);
        }else{
            $this->db->where('name', 'password_policy');
            $this->db->update(db_prefix() . 'options', ['name'=>'password_policy','value'=>$data]);
        }
    }

    public function check_password_history($staff, $userid, $password)
    {
        $password_policy =$this->getPasswordPolicy();
        if($password_policy && isset($password_policy->enable_password_policy) && $password_policy->enable_password_policy==1 && $password_policy->pass_history >0){
            $this->db->where('is_staff', $staff);
            $this->db->where('staff_id', $userid);
            $this->db->order_by("changed_on", "DESC");
            $this->db->limit($password_policy->pass_history, 0);
            $query =$this->db->get(db_prefix() . 'pass_history');
            foreach ($query->result() as $row){
                if(app_hasher()->CheckPassword($password, $row->new_pass)){
                    echo $row->new_pass;
                    return false;
                }
            }
        }
        
        return true;
    }

    public function save_password_history($staff, $userid, $password)
    {
        $password_policy =$this->getPasswordPolicy();
        if($password_policy && isset($password_policy->enable_password_policy) && $password_policy->enable_password_policy==1){
            $this->db->insert(db_prefix() . 'pass_history',['is_staff'=>$staff,'staff_id'=>$userid,'new_pass'=>app_hash_password($password)]);
        }
    }

    public function validate_password($password)
    {
        $passwordpolicy=$this->getPasswordPolicy();
        if($passwordpolicy && isset($passwordpolicy->enable_password_policy)){
            if($passwordpolicy->password_min_length >0 && strlen($password)< $passwordpolicy->password_min_length){
                return _l('minimum_character_error',[_l('password'),$passwordpolicy->password_min_length]);
            }
            if($passwordpolicy->password_max_length >0 && strlen($password)> $passwordpolicy->password_max_length){
                return _l('maximum_character_error',[_l('password'),$passwordpolicy->password_max_length]);
            }
            switch ($passwordpolicy->password_strength) {
                default:
                case 'low':
                    break;

                case 'medium':
                    if(preg_match('/[a-zA-Z]/',$password)==false || preg_match('/\d/',          $password)==false){
                        return _l('Password should have atleast one letter and one number');
                    }
                    break;
            
                case 'high':
                    if(preg_match('/[a-z]/',$password)==false || preg_match('/[A-Z]/',$password)==false || preg_match('/\d/',$password)==false || preg_match('/[^a-zA-Z\d]/', $password)==false){
                       return _l('Password should have atleast one small letter, one capital letter, one number and one special character');
                    }
                    break;
            }
        }

        return true;
    }

    public function login_fail_log($staff,$staffid)
    {
        $passwordpolicy=$this->getPasswordPolicy();
        if($passwordpolicy && isset($passwordpolicy->enable_password_policy) && $passwordpolicy->lock_invalid_attempt >0){
            if($staff){
                
                $this->db->where('staffid', $staffid);
                $user = $this->db->get(db_prefix() . 'staff')->row();
                $this->db->where('staffid', $staffid);
                $data =['login_fails'=>$user->login_fails+1];
                if($user->login_fails+1 >= $passwordpolicy->lock_invalid_attempt){
                    $data['login_locked_on'] =date ('Y-m-d H:i:s');
                }
                $this->db->update(db_prefix() . 'staff',$data);
            }
        }
        
    }

    public function reset_login_fail_log($staff,$staffid)
    {
        $passwordpolicy=$this->getPasswordPolicy();
        if($passwordpolicy && isset($passwordpolicy->enable_password_policy) && $passwordpolicy->lock_invalid_attempt >0){
            if($staff){
                $this->db->where('staffid', $staffid);
                $this->db->update(db_prefix() . 'staff', ['login_fails'=>0,'login_locked_on'=>null]);
            }
        }
    }
    
}
