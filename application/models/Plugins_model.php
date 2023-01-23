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
    
    public function save_config($plugin,$config)
    {
        $this->db->where('plugin',$plugin);

        $exsits =$this->db->get(db_prefix().'plugin_configs')->row();
        if($exsits){
            $this->db->where('plugin',$plugin);
            $this->db->update(db_prefix().'plugin_configs',array('config'=>json_encode($config)));
        }else{
            $this->db->insert(db_prefix().'plugin_configs',array('plugin'=>$plugin,'config'=>json_encode($config)));
        }
    }

    public function get_config($id)
    {
        $this->db->where('id',$id);
        $config =$this->db->get(db_prefix().'plugin_configs')->row();
        if($config && $config->config){
            $config->config =json_decode($config->config,true);
        }
        return $config;
    }

    public function get_config_by_plugin($plugin)
    {
        $this->db->where('plugin',$plugin);
        $config =$this->db->get(db_prefix().'plugin_configs')->row();
        if($config && $config->config){
            $config->config =json_decode($config->config,true);
        }
        return $config;
    }

    public function get_leadads()
    {
        $this->db->where('plugin','facebook_leadads');
        $leadads =$this->db->get(db_prefix().'plugin_configs')->result_object();
        if($leadads){
            foreach($leadads as $key => $value){
                if($leadads[$key]->config){
                    $leadads[$key]->config =json_decode($leadads[$key]->config,true);
                }
            }
        }
        return $leadads;
    }

    public function delete($id)
    {
        $this->db->where('id',$id);
        $this->db->delete(db_prefix().'plugin_configs');
    }
}