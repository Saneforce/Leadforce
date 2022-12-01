<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Plugins_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_config($plugin_name,$config)
    {
        $this->db->insert(db_prefix().'plugin_configs',array('plugin'=>$plugin_name,'config'=>json_encode($config)));
    }

    public function update_config($id,$config)
    {
        $this->db->insert(db_prefix().'plugin_configs',array('config'=>json_encode($config)));
    }

    public function get_config($id)
    {
        $this->db->where('id',$id);
        $config =$this->db->get(db_prefix().'plugin_configs')->row();
        if($config && $config->config){
            $config->config =json_decode($config->config);
        }
        return $config;
    }
}