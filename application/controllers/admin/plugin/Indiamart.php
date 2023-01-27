<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Indiamart extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('leads_model');
    }

    public function index()
    {
        if(!is_admin()){
            access_denied();
        }
        $data =array('title'=>'Indiamart');
        $data['indiaMart'] = array('UNIQUE_QUERY_ID' => 'QUERY_ID',
        'QUERY_TYPE' => 'QTYPE',
        'SENDER_NAME' => 'SENDERNAME',
        'SENDER_EMAIL' => 'SENDERMAIL',
        'SENDER_MOBILE' => 'MOB',
       // 'GLUSER_USR_COMPANYNAME' => 'GLUSER_USR_COMPANYNAME',
        'SENDER_ADDRESS' => 'ENQ_ADDRESS',
        'SENDER_CITY' => 'ENQ_CITY',
        'ENQ_STATE' => 'ENQ_STATE',
        'SENDER_COUNTRY_ISO' => 'COUNTRY_ISO',
        'QUERY_PRODUCT_NAME' => 'PRODUCT_NAME',
        'QUERY_MESSAGE' => 'ENQ_MESSAGE',
        //'DATE_RE' => 'DATE_RE',
      //  'DATE_R' => 'DATE_R',
       // 'DATE_TIME_RE' => 'DATE_TIME_RE',
       // 'LOG_TIME' => 'LOG_TIME',
  //      'QUERY_MODID' => 'QUERY_MODID',
        'CALL_DURATION' => 'ENQ_CALL_DURATION',
        'RECEIVER_MOBILE' => 'ENQ_RECEIVER_MOB',
        'SENDER_EMAIL_ALT' => 'EMAIL_ALT',
        'SENDER_MOBILE_ALT' => 'MOBILE_ALT');

        $this->db->where('slug', 'indiamart');
        $field_val = $this->db->get(db_prefix() . 'leads_sources')->row();
        $fvs = json_decode($field_val->fields);
        $data['fvs']   = $fvs;
        $data['result']   = $field_val;
        //pre($fvs);
        $data['name'] = explode(',',$fvs->name);;
        $data['lead_company'] = explode(',',$fvs->lead_company);

        $this->load->view('admin/plugins/indiamart/mergefields',$data);
    }

    public function saveMergeFields()
    {
        if(!is_admin()){
            access_denied();
        }
        
        if ($this->input->post()) {
            $this->db->where('slug', 'indiamart');
            $source =$this->db->get(db_prefix().'leads_sources')->row();

            if($source){
                $data = $this->input->post();
                $lead_company = $name = '';
                $updateData = array();
                if($data['name'] != '0') {
                    $name = $data['name'];
                }
                if($data['name1'] != '0') {
                    if($name != '0') {
                        $name = $name.','.$data['name1'];
                    }
                }
                if($data['name2'] != '0') {
                    if($name != '0') {
                        $name = $name.','.$data['name2'];
                    }
                }
                if($name != '0') {
                    $updateData['name'] = $name;
                }
                
                if($data['lead_company'] != '0')
                    $lead_company = $data['lead_company'];
                if($data['lead_company1'] != '0') {
                    if($lead_company != '0') {
                        $lead_company = $lead_company.','.$data['lead_company1'];
                    }
                }
                if($data['lead_company2'] != '0') {
                    if($lead_company != '0') {
                        $lead_company = $lead_company.','.$data['lead_company2'];
                    }
                }
                if($lead_company != '0') {
                    $updateData['lead_company'] = $lead_company;
                }

                if($data['title'] != '0') {
                    $updateData['title'] = $data['title'];
                } else {
                    $updateData['title'] = '';
                }

                if($data['email'] != '0') {
                    $updateData['email'] = $data['email'];
                } else {
                    $updateData['email'] = '';
                }

                if($data['website'] != '0') {
                    $updateData['website'] = $data['website'];
                } else {
                    $updateData['website'] = '';
                }

                if($data['phonenumber'] != '0') {
                    $updateData['phonenumber'] = $data['phonenumber'];
                } else {
                    $updateData['phonenumber'] = '';
                }

                if($data['address'] != '0') {
                    $updateData['address'] = $data['address'];
                } else {
                    $updateData['address'] = '';
                }

                if($data['city'] != '0') {
                    $updateData['city'] = $data['city'];
                } else {
                    $updateData['city'] = '';
                }

                if($data['state'] != '0') {
                    $updateData['state'] = $data['state'];
                } else {
                    $updateData['state'] = '';
                }

                if($data['country'] != '0') {
                    $updateData['country'] = $data['country'];
                } else {
                    $updateData['country'] = '';
                }

                if($data['zip'] != '0') {
                    $updateData['zip'] = $data['zip'];
                } else {
                    $updateData['zip'] = '';
                }

                if($data['assigned'] != '0') {
                    $updateData['assigned'] = $data['assigned'];
                } else {
                    $updateData['assigned'] = '';
                }

                if($data['custom_fields'] != '0') {
                    $updateData['custom_fields'] = $data['custom_fields'];
                } else {
                    $updateData['custom_fields'] = '';
                }

                if($data['description'] != '0') {
                    $updateData['description'] = $data['description'];
                } else {
                    $updateData['description'] = '';
                }

                
                $jsonData = json_encode($updateData);
                $fieldData = array();
                $fieldData['fields'] = $jsonData;
                $fieldData['user_account'] = $data['user_account'];
                $fieldData['unique_key'] = $data['unique_key'];
                $success = $this->leads_model->update_source_fields($fieldData, $source->id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('lead_source')));
                }
            }
            
            redirect(admin_url('plugin/indiamart'));
        }
    }

}
