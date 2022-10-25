<?php

class App_workflow
{
    private static $workflows =[];

    private static $allMergeFields =[
        'others_merge_fields'=>[
            '{logo_url}',
            '{logo_image_with_url}',
            '{dark_logo_image_with_url}',
            '{crm_url}',
            '{admin_url}',
            '{main_domain}',
            '{companyname}',
            '{email_signature}',
            '{terms_and_conditions_url}',
            '{privacy_policy_url}',
        ],
        'customer_merge_fields'=>[
            '{contact_firstname}',
            '{contact_lastname}',
            '{client_company}',
            '{client_vat_number}',
            '{client_id}',
        ],
        'invoice_merge_fields'=>[
            '{invoice_link}',
            '{invoice_number}',
            '{invoice_duedate}',
            '{invoice_date}',
            '{invoice_status}',
            '{invoice_subtotal}',
            '{invoice_total}',
        ],
        'proposal_merge_fields'=>[
            '{proposal_number}',
            '{proposal_id}',
            '{proposal_subject}',
            '{proposal_open_till}',
            '{proposal_subtotal}',
            '{proposal_total}',
            '{proposal_proposal_to}',
            '{proposal_link}',
        ],
        'lead_merge_fields'=>[
            '{lead_name}',
            '{lead_email}',
            '{lead_position}',
            '{lead_company}',
            '{lead_country}',
            '{lead_zip}',
            '{lead_city}',
            '{lead_state}',
            '{lead_address}',
            '{lead_assigned}',
            '{lead_status}',
            '{lead_source}',
            '{lead_phonenumber}',
            '{lead_link}',
            '{lead_website}',
            '{lead_description}',
        ],
        'staff_merge_fields'=>[
            '{staff_firstname}',
            '{staff_lastname}',
        ],
        'deal_merge_fields'=>[
            '{deal_name}',
            '{deal_description}',
            '{deal_start_date}',
            '{deal_deadline}',
            '{deal_link}',
        ],
        'activity_merge_fields'=>[
            '{task_type}',
            '{task_user_take_action}',
            '{task_related}',
            '{task_name}',
            '{task_description}',
            '{task_status}',
            '{task_priority}',
            '{task_startdate}',
        ],
    ];


    private static $servicesIcons =[
        'whatsapp'=>'<i class="fa fa-whatsapp" aria-hidden="true"></i>',
        'approval'=>'<i class="fa fa-check-square-o" aria-hidden="true"></i>',
        'email'=>'<i class="fa fa-envelope-o" aria-hidden="true"></i>',
    ];
    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('workflow_model');
    }

    public function addWorkflow($action,$name,$description,$services,$icon)
    {
        
        foreach($services as $service_id => $service){
            $mergeFields =$service['mergeFields'];
            $newMergeFields =array();
            if($mergeFields){
                foreach($mergeFields as $mergeField){
                    $newMergeFields[$mergeField] =$this->getMergeFields($mergeField);
                }
            }
            
            $services[$service_id]['mergeFields']=$newMergeFields;
            $services[$service_id]['icon']=$this->getSeviceIcon($services[$service_id]['medium']);
        }
        self::$workflows[$action] =array(
            'action'=>$action,
            'name'=>$name,
            'icon'=>$icon,
            'description'=>$description,
            'services'=>$services,
            'flows'=>$this->ci->workflow_model->getflows($action)
        );
    }

    public function getWorkflows()
    {
        return self::$workflows;
    }

    public function getWorkFlow($action)
    {
        return self::$workflows[$action];
    }

    public function getMergeFields($name)
    {
        return self::$allMergeFields[$name];
    }

    public function sendWhatsapp($to,$flow,$mergeFields)
    {
        $CI = &get_instance();
        $CI->load->helper('whatsapp_helper');
        $configure = json_decode($flow->configure,true);
        $fields =$configure['variables'];
        $whatsappFields =array();
        if($fields){
            foreach($fields as $field){
                foreach ($mergeFields as $key => $val) {
                    $val =strlen($val)>0?$val:' ';
                    $field = stripos($field, $key) !== false
                        ? str_ireplace($key, $val, $field)
                        : str_ireplace($key, '""', $field);
                }
                $whatsappFields [] =$field;
            }
        }
        whatsapp_send_template_message($to,$configure['template'],$whatsappFields);
    }

    public function getCountryCallingCode($iso2)
    {
        $CI = &get_instance();
        $CI->db->where('iso2',$iso2);
        $country =$CI->db->get(db_prefix().'countries')->row();
        if($country){
            return $country->calling_code;
        }else{
            return '91';
        }
    }

    public function getSeviceIcon($medium)
    {
        if(isset(self::$servicesIcons[$medium])){
            return self::$servicesIcons[$medium];
        }else{
            return '';
        }
    }

    public function sendEmail($fromname,$send_to,$subject,$message,$servicename)
    {
        $this->ci->load->config('email');

        $this->ci->email->clear(true);
        $this->ci->email->set_newline(config_item('newline'));
        $this->ci->email->from(get_option('smtp_email'), $fromname);

        $this->ci->email->subject($subject);

        $this->ci->email->message($message);
        $this->ci->email->to($send_to);
        
        if ($this->ci->email->send()) {
            log_activity('Email Send To [Email: ' . $send_to . ', Template: ' . $servicename . ']');
            return true;
        }
        if (ENVIRONMENT !== 'production') {
            log_activity('Failed to send email - ' . $this->ci->email->print_debugger());
        }

        return false;
    }
}
