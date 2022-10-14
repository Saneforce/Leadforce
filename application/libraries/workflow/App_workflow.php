<?php

class App_workflow
{
    private static $workflows =[];

    private static $allMergeFields =[
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
            '{lead_website}',
            '{lead_description}',
            '{lead_phonenumber}',
            '{lead_company}',
            '{lead_zip}',
            '{lead_city}',
            '{lead_state}',
            '{lead_assigned}',
            '{lead_status}',
            '{lead_source}',
            '{lead_link}',
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

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('workflow_model');
    }

    public function addWorkflow($action,$name,$description,$services)
    {
        foreach($services as $service_id => $service){
            $mergeFields =$service['mergeFields'];
            $newMergeFields =array();
            foreach($mergeFields as $mergeField){
                $newMergeFields[$mergeField] =$this->getMergeFields($mergeField);
            }
            $services[$service_id]['mergeFields']=$newMergeFields;
        }
        self::$workflows[$action] =array(
            'action'=>$action,
            'name'=>$name,
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
                if(isset($mergeFields[$field])){
                    $whatsappFields [] =$mergeFields[$field];
                }else{
                    return false;
                }
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
}
