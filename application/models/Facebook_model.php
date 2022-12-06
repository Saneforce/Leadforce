<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Facebook_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName =db_prefix().'facebook_leadgen_configs';
    }
    
    public function save_leadgen_config()
    {

        $data =array(
            "user_id"=>$_POST['user_id'],
            "page_id"=>$_POST['page_id'],
            "form_id"=>$_POST['form_id'],
            "config"=>json_encode($_POST['config'])
        );
        $this->db->insert($this->tableName,$data);
        return [
            'success'=> true,
            'msg' => _l('whats_app_account_saved')
        ];
    }

    public function delete_leadgen_config($id)
    {
        $this->db->where('id',$id);
        $this->db->delete($this->tableName);
    }

    public function get_leadgen_config($id)
    {
        $this->db->where('id',$id);
        $result =$this->db->get($this->tableName)->row();
        if($result && $result->config){
            $result->config =json_decode($result->config,true);
        }
        return $result;
    }

    public function get_leadgen_configs()
    {
        $result =$this->db->get($this->tableName)->result_object();
        if($result){
            foreach($result as $key => $value){
                if($result[$key]->config){
                    $result[$key]->config =json_decode($result[$key]->config,true);
                }
            }
        }
        return $result;
    }
    

}