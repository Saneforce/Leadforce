<?php

class Lead_created_workflow extends App_workflow
{

    private static $action = 'lead_created';
    private static $name = 'Lead created';
    private static $description = 'Trigger when new lead created';
    private static $icon ='<i class="fa fa-tty"></i>';
    private static $availableServices = [
        'whatsapp_staff_notification' => [
            'type' => 'notification',
            'medium' => 'whatsapp',
            'name' => 'Send notification to staff',
            'description' => 'Send whatsapp message to staff',
            'mergeFields' => ['lead_merge_fields', 'staff_merge_fields'],
        ],
        'whatsapp_customer_notification' => [
            'type' => 'notification',
            'medium' => 'whatsapp',
            'name' => 'Send notification to customer',
            'description' => 'Send whatsapp message to customer',
            'mergeFields' => ['lead_merge_fields'],
        ]
    ];

    public function __construct()
    {
        parent::__construct();
        $this->addWorkflow(self::$action, self::$name, self::$description, self::$availableServices,self::$icon);
    }

    public function getService($name)
    {
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }
        return false;
    }

    public function trigger($lead_id)
    {

        $CI = &get_instance();
        $CI->load->model('leads_model');
        $flows = $CI->workflow_model->getflows(self::$action);
        $CI->load->library('merge_fields/leads_merge_fields');
        $leads_merge_fields = $CI->leads_merge_fields->format($lead_id);
        $lead = $CI->leads_model->get($lead_id);

        if ($flows) {
            foreach ($flows as $flow) {
                if ($flow->inactive == 0 && $flow->configure) {

                    switch ($flow->service) {
                        case 'whatsapp_staff_notification':
                            $CI->load->library('merge_fields/staff_merge_fields');
                            $staff_merge_fields = $CI->staff_merge_fields->format($lead->assigned);
                            $merge_fields = array_merge($leads_merge_fields, $staff_merge_fields);
                            $CI->db->where('staffid', $lead->assigned);
                            $staff = $CI->db->get(db_prefix() . 'staff')->row();
                            if ($staff) {
                                $this->sendWhatsapp($this->getCountryCallingCode($staff->phone_country_code) . $staff->phonenumber, $flow, $merge_fields);
                            }
                            break;
                        case 'whatsapp_customer_notification':
                            $this->sendWhatsapp($this->getCountryCallingCode($lead->phone_country_code) .$lead->phonenumber, $flow, $leads_merge_fields);
                            break;
                        default:
                            # code...
                            break;
                    }
                }
            }
        }
    }
}
