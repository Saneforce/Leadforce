<?php

class Workflow_app
{
    protected static $modules =[];

    protected static $alltriggers =[];

    protected static $allowedmergefields =[];

    protected static $queryfields =[];

    private static $allMergeFields =[
        'others_merge_fields'=>[
            'name'=>'System placeholders',
            'placeholders'=>[
                '{logo_url}'=>'Logo URL',
                '{logo_image_with_url}'=>'Logo image with URL',
                '{dark_logo_image_with_url}'=>'Dark logo image with URL',
                '{crm_url}'=>'CRM URL',
                '{admin_url}'=>'Admin URL',
                '{main_domain}'=>'Main Domain',
                '{companyname}'=>'Company Name',
                '{email_signature}'=>'Email Signature',
                '{terms_and_conditions_url}'=>'Terms and conditinos URL',
                '{privacy_policy_url}'=>'Privacy Policy URL'
            ],
        ],
        'customer_merge_fields'=>[
            '{contact_firstname}'=>'Customer first name',
            '{contact_lastname}'=>'Customer last name',
            '{client_company}'=>'Customer company name',
            '{client_vat_number}'=>'Customer VAT number',
            '{client_id}'=>'Customer Id',
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
            'name'=>'Lead placeholders',
            'placeholders'=>[
                '{lead_name}'=>'Lead name',
                '{lead_email}'=>'Lead email',
                '{lead_position}'=>'Lead position',
                '{lead_company}'=>'Lead company',
                '{lead_country}'=>'Lead country',
                '{lead_zip}'=>'Lead ZIP code',
                '{lead_city}'=>'Lead city',
                '{lead_state}'=>'Lead state',
                '{lead_address}'=>'Lead address',
                '{lead_assigned}'=>'Lead assigned',
                '{lead_status}'=>'Lead status',
                '{lead_source}'=>'Lead source',
                '{lead_phonenumber}'=>'Lead phone number',
                '{lead_link}'=>'Lead link',
                '{lead_website}'=>'Lead website',
                '{lead_description}'=>'Lead description',
            ]
        ],
        'staff_merge_fields'=>[
            'name'=>'Staff placeholders',
            'placeholders'=>[
                '{staff_firstname}'=>'Staff first name',
                '{staff_lastname}'=>'Staff last name',
            ]
        ],
        'deal_merge_fields'=>[
            'name'=>'Deal placeholders',
            'placeholders'=>[
                '{deal_name}'=>'Deal name',
                '{deal_description}'=>'Deal description',
                '{deal_start_date}'=>'Deal start date',
                '{deal_deadline}'=>'Deal deadline',
                '{deal_link}'=>'Deal link',
            ]
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

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('workflow_model');
        $this->ci->load->model('designation_model');
        $this->ci->load->helper('whatsapp_helper');
        $this->ci->load->model('sms_model');
    }
    
    protected function setModule($name,$title,$description,$icon,$triggers)
    {
        self::$modules [$name] =array(
            'name'=>$name,
            'title'=>$title,
            'description'=>$description,
            'icon'=>$icon,
            'triggers'=>$triggers,
        );
    }
    public function getModuleDetails($module)
    {
        if(isset(self::$modules [$module])){
            return self::$modules [$module];
        }else{
            array();
        }
    }

    public function getModules()
    {
        return self::$modules;
    }
    protected function setTrigger($module,$name,$title,$description,$icon,$tirggers =array(),$type ='',$medium ='')
    {
        self::$alltriggers [$module][$name]=array(
            'title'=>$title,
            'description'=>$description,
            'icon'=>$icon,
            'triggers'=>$tirggers,
            'type'=>$type,
            'medium'=>$medium,
        );
    }

    public function getTriggers($module =''){
        $triggers =array();
        if($module ==''){
            $triggers =self::$alltriggers;
        }elseif(isset(self::$alltriggers [$module])){
            $triggers =self::$alltriggers [$module];
        }

        return $triggers;
    }

    protected function setMergeFields($name, $allowedfiedls){
        self::$allowedmergefields [$name] =$allowedfiedls;
    }

    public function getMergeFields($name)
    {
        $mergefields =array();
        if(isset(self::$allowedmergefields[$name])){
            foreach(self::$allowedmergefields[$name] as $field){
                if(isset(self::$allMergeFields[$field])){
                    $mergefields [$field] =self::$allMergeFields[$field];
                }
            }
            return $mergefields;
        }
        return array();
    }

    protected function setQueryFields($name,$queryfields)
    {
        if(!isset(self::$queryfields [$name])){
            self::$queryfields [$name] =array();
        }
        self::$queryfields [$name][$queryfields['id']] =$queryfields;
        
    }

    public function getQueryFields($name)
    {
        if(isset(self::$queryfields[$name])){
            return self::$queryfields[$name];
        }
        return array();
    }

    protected function mergefieldsContent($mergefields,$content)
    {
        foreach($mergefields as $field_key => $field_value){
            $content =str_replace($field_key,$field_value,$content);
        }
        return $content;
    }

    protected function sendEmail($fromname,$send_to,$subject,$message,$servicename)
    {

        // echo 'Fromname : ';
        // pr($fromname);
        // echo '<hr>';
        // echo 'Subject : ';
        // pr($subject);
        // echo '<hr>';
        // echo 'Message : ';
        // pr($message);
        // echo '<hr>';
        // pr('sending email to '.$send_to.'   ...');
        // echo '<hr>';
        // echo '<hr>';
        // return true;
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

    protected function run($module,$parent_id)
    {
        $child_flows =$this->ci->workflow_model->getmoduleflows($module,['parent_id'=>$parent_id]);
        if($child_flows){
            foreach($child_flows as $flow){
                
                $continue =false;
                switch($flow->action){
                    case 'send_email':
                        $this->run_email($flow);
                        break;
                    case 'send_whatsapp':
                        $this->run_whatsapp($flow);
                        break;
                    case 'send_sms':
                        $this->run_sms($flow);
                        break;
                    case 'condition':
                        $condition =$this->run_condition($flow);
                        if($condition){
                            $true_flows =$this->ci->workflow_model->getmoduleflows($module,['parent_id'=>$flow->id,'action'=>'true']);
                            if($true_flows){
                                foreach($true_flows as $true_flow){
                                    $this->run($module,$true_flow->id);
                                }
                            }
                        }else{
                            
                            $false_flows =$this->ci->workflow_model->getmoduleflows($module,['parent_id'=>$flow->id,'action'=>'false']);
                            if($false_flows){
                                foreach($false_flows as $false_flow){
                                    $this->run($module,$false_flow->id);
                                }
                            }
                        }
                        $continue =true;
                        break;
                    case 'add_activity':
                        $this->run_add_activity($flow);
                        break;
                    default:
                        $this->check_flow($flow);
                        break;
                }

                if($continue){
                    continue;
                }

                $this->run($module,$flow->id);
            }
        }
    }

    protected function get_roles_staffs($roles){
        $staffs =$staff_ids =array();
        foreach($roles as $role){
            
            $rolebasedstaffs =$this->ci->roles_model->get_role_staff($role);
            if($rolebasedstaffs){
                $staffs =array_merge($staffs,$rolebasedstaffs);
            }
        }

        if($staffs){
            foreach ($staffs as $staff) {
                $staff_ids [] =$staff['staffid'];
            }
        }
        return $staff_ids;
    }

    protected function get_designations_staffs($designations){
        $staffs =$staff_ids =array();
        foreach($designations as $designation){
            $designationbasedstaffs =$this->ci->designation_model->get_designation_staff($designation);
            if($designationbasedstaffs){
                $staffs =array_merge($staffs,$designationbasedstaffs);
            }
        }

        if($staffs){
            foreach ($staffs as $staff) {
                $staff_ids [] =$staff['staffid'];
            }
        }
        return $staff_ids;
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

    public function sendWhatsapp($to,$flow,$mergeFields)
    {
        $configure = $flow->configure;
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

        if(isset($flow->configure['header_media_caption'])){
            $header_media_caption =$flow->configure['header_media_caption'];
        }
        if(isset($flow->configure['header_media_link'])){
            $header_media_link =$flow->configure['header_media_link'];
        }
        if(isset($flow->configure['header_variable'])){
            $header_variable =$flow->configure['header_variable'];
        }

        foreach ($mergeFields as $key => $val) {
            if(isset($flow->configure['header_variable'])){
                $header_variable = stripos($header_variable, $key) !== false
                ? str_ireplace($key, $val, $header_variable)
                : str_ireplace($key, '""', $header_variable);
            }
            if(isset($flow->configure['header_media_link'])){
                $header_media_link = stripos( $header_media_link, $key) !== false
                ? str_ireplace($key, $val,  $header_media_link)
                : str_ireplace($key, '""',  $header_media_link);
            }
            if(isset($flow->configure['header_media_caption'])){
                $header_media_caption = stripos($header_media_caption, $key) !== false
                ? str_ireplace($key, $val, $header_media_caption)
                : str_ireplace($key, '""', $header_media_caption);

            }
        }
        $headers =[];
        if($flow->configure['header_format']){
            $headers =array(
                'header_format'=>$flow->configure['header_format'],
                'header_variable'=>$header_variable,
                'header_media_link'=>$header_media_link,
                'header_media_caption'=>$header_media_caption,
            );
        }

        // echo 'Headers';
        // pr($header_variable);
        // pr($header_media_caption);
        // pr($header_media_link);
        // echo 'Fromname : ';
        // pr($configure['template']);
        // echo '<hr>';
        // echo 'Fields : ';
        // pr($whatsappFields);
        // echo '<hr>';
        // pr('sending whatsapp message to '.$to.'   ...');
        // echo '<hr>';
        // return true;
        whatsapp_send_template_message($to,$configure['template'],$whatsappFields,$headers);
    }

    public function sendSms($to,$flow,$mergeFields)
    {
        
        $configure = $flow->configure;
        $fields =$configure['variables'];
        $smsFields =array();
        if($fields){
            foreach($fields as $field){
                foreach ($mergeFields as $key => $val) {
                    $val =strlen($val)>0?$val:' ';
                    $field = stripos($field, $key) !== false
                        ? str_ireplace($key, $val, $field)
                        : str_ireplace($key, '""', $field);
                }
                $smsFields [] =$field;
            }
        }

        $smsconfiguration =$this->ci->sms_model->getConfig();
        if($smsconfiguration ){
            if($smsconfiguration['provider'] =='daffytel'){
                $this->ci->load->library('sms/sms_daffytel');
                $this->ci->sms_daffytel->send($to,$configure['template'],$smsFields);
            }
        }
        
    }
}