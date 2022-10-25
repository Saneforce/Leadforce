<?php

class Deal_created_workflow extends App_workflow
{

    private static $action = 'deal_created';
    private static $name = 'Deal created';
    private static $description = 'Trigger when new deal created';
    private static $icon ='<i class="fa fa-usd"></i>';
    private static $availableServices = [
        'whatsapp_staff_notification' => [
            'type' => 'notification',
            'medium' => 'whatsapp',
            'name' => 'Send notification to staff',
            'description' => 'Send whatsapp message to staff',
            'mergeFields' => ['deal_merge_fields', 'staff_merge_fields'],
        ],
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

    public function trigger($deal_id)
    {

        $CI = &get_instance();
        $CI->load->model('projects_model');
        $flows = $CI->workflow_model->getflows(self::$action);
        $CI->load->library('merge_fields/deals_merge_fields');
        $deals_merge_fields = $CI->deals_merge_fields->format($deal_id);
        $deal = $CI->projects_model->get($deal_id);

        if ($flows) {
            foreach ($flows as $flow) {
                if ($flow->inactive == 0 && $flow->configure) {
                    switch ($flow->service) {
                        case 'whatsapp_staff_notification':
                            $CI->load->library('merge_fields/staff_merge_fields');
                            $staff_merge_fields = $CI->staff_merge_fields->format($deal->teamleader);
                            $merge_fields = array_merge($deals_merge_fields, $staff_merge_fields);
                            $CI->db->where('staffid', $deal->teamleader);
                            $staff = $CI->db->get(db_prefix() . 'staff')->row();
                            if ($staff) {
                                $this->sendWhatsapp($this->getCountryCallingCode($staff->phone_country_code) . $staff->phonenumber, $flow, $merge_fields);
                            }
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
