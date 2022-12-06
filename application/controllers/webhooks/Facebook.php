<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Facebook extends App_Controller
{
    public function leadads()
    {
        
        $this->load->helper('facebook_helper');
        if(isset($_REQUEST['hub_challenge']) && isset($_REQUEST['hub_verify_token'])){
            $challenge = $_REQUEST['hub_challenge'];
            $verify_token = $_REQUEST['hub_verify_token'];

            if ($verify_token === 'abc123') {
                echo $challenge;
            }
        }else{
            if($data = json_decode(file_get_contents("php://input"))) {
                foreach ($data->entry as $entry) {
                    foreach ($entry->changes as $change) {
                        $this->db->where('form_id',$change->value->form_id);
                        $this->db->where('page_id',$change->value->page_id);
                        $leadgen_config =$this->db->get(db_prefix().'facebook_leadgen_configs')->row();
                        if($leadgen_config){
                            $leadgen_details =facebook_get_leadgen_details($change->value->leadgen_id,'EAAF6ByuNgHYBAPiwHqr8aOFcMN4Xoh22aG5H5wnipIbCA569IiYa9p4ZBSJxSoDCBenASHPEesVLRU4YGaJHuGg5J6anefxyO7luFHhdJiKOoLmWkR9BnivVl4abtONXYfoKhqDyjZBQ7NF1JiPISOeVZArS7FjpwGTH4ZCx4BivWftWCgI5rvtnXDMihwfO3TKWMDSTRZBPgMQCpHGdMxhrKE2myLJRZC2GH8WoNcuQZDZD');
                            if(isset($leadgen_details['field_data'])){
                                $configs =json_decode($leadgen_config->config,true);
                                $leaddata=array(
                                    'name'=>$configs['name'],
                                    'source'=>$configs['view_source'],
                                    'title'=>$configs['title'],
                                    'email'=>$configs['email'],
                                    'website'=>$configs['website'],
                                    'phonenumber'=>$configs['phonenumber'],
                                    'address'=>$configs['address'],
                                    'city'=>$configs['city'],
                                    'state'=>$configs['state'],
                                    'country'=>$configs['country'],
                                    'zip'=>$configs['zip'],
                                    'description'=>$configs['description'],
                                );
                                $merged_data =array();
                                foreach ($leadgen_details['field_data'] as $field_data) {
                                    $merged_data[$field_data['name']] =$field_data['values'];
                                }
                                foreach ($leaddata as $key => $value) {
                                    if(isset($merged_data[$value])){
                                        $leaddata[$key] =$merged_data[$value][0];
                                    }
                                    if(!$value){
                                        unset($leaddata[$key]);
                                    }
                                }

                                $leaddata['addedfrom'] =0;
                                $leaddata['teamleader'] = 0;
                                $leaddata['status'] = 0;
                                $leaddata['pipeline_id'] = 0;
                                $leaddata['startdate'] = date('Y-m-d');
                                $leaddata['enddate'] =  date('Y-m-d');
                                $leaddata['currency'] = 0;
                                $leaddata['rate'] = 0;
                                $leaddata['project_id'] = 0;
                                $leaddata['deleted_status'] = 0;
                                $leaddata['dateadded'] = date('Y-m-d H:i:s');
                                $this->db->insert(db_prefix() . 'leads', $leaddata);
                                $insert_id = $this->db->insert_id();
                                hooks()->do_action('lead_created', $insert_id);
                            }
                        }
                    }
                }
                
            }

            
        }
        
    }
}