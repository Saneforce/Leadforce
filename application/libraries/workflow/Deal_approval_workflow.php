<?php

class Deal_approval_workflow extends App_workflow
{

    private static $action = 'deal_approval';
    private static $name = 'Deal approval';
    private static $description = 'Trigger when new deal created';
    private static $icon ='<i class="fa fa-check-square-o"></i>';
    private static $availableServices = [
        'approval_request_email_notification' => [
            'type' => 'notification',
            'medium' => 'email',
            'name' => 'Send approval request email to staff',
            'description' => 'Send email to staff',
            'mergeFields' => ['deal_merge_fields', 'staff_merge_fields','others_merge_fields'],
        ],
        'approved_email_notification' => [
            'type' => 'notification',
            'medium' => 'email',
            'name' => 'Send approved notificaton email to staff',
            'description' => 'Send email to staff when deal is approved',
            'mergeFields' => ['deal_merge_fields', 'staff_merge_fields','others_merge_fields'],
        ],
        'rejected_email_notification' => [
            'type' => 'notification',
            'medium' => 'email',
            'name' => 'Send rejected notificaton email to staff',
            'description' => 'Send email to staff when deal is rejected',
            'mergeFields' => ['deal_merge_fields', 'staff_merge_fields','others_merge_fields'],
        ],
        // 'whatsapp_staff_notification' => [
        //     'type' => 'notification',
        //     'medium' => 'whatsapp',
        //     'name' => 'Send notification to staff',
        //     'description' => 'Send whatsapp message to staff',
        //     'mergeFields' => ['deal_merge_fields', 'staff_merge_fields'],
        // ],
        'approval_level' => [
            'type' => 'system',
            'medium' => 'approval',
            'name' => 'Approval level for deal',
            'description' => 'Add approval level to deal',
            'mergeFields'=>[]
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
        $deal = $CI->projects_model->get($deal_id);
        if ($flows) {
            $approvalLevel =0;
            $CI->db->select('count(id) as level');
            $CI->db->where('rel_type','project');
            $CI->db->where('reopened',0);
            $CI->db->where('rel_id',$deal_id);
            $approvedRow =$CI->db->get(db_prefix().'approval_history')->row();
            $approvedLevel =0;
            if($approvedRow){
                $approvedLevel =$approvedRow->level;
            }
            $CI->load->model('approval_model');
            $approvals =$CI->approval_model->getDealReportingLevels($deal->teamleader);
            $approval_request_email_notification =array();
            foreach ($flows as $flow) {
                if($flow->service =='approval_level'){
                    $approvalLevel++;
                }
                if($approvedLevel==0 && $flow->service =='approval_request_email_notification' && $flow->configure){
                    $approval_request_email_notification =$flow;
                }
                if ($flow->inactive == 0 && $flow->configure) {
                    if($flow->service =='approval_level' && $approvalLevel >$approvedLevel ){
                        if($approval_request_email_notification){
                            if($approvals[$approvalLevel-1]){
                                $this->emailNotification($approval_request_email_notification,$deal_id,$approvals[$approvalLevel-1]->staffid);
                            }
                        }
                        
                        $CI->db->where('id',$deal_id);
                        $CI->db->update(db_prefix().'projects',['approved'=>0]);
                        if(!$approvals[$approvalLevel-1]){
                            $this->approveDeal($deal_id);
                        }
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function approveDeal($deal_id)
    {
        $CI = &get_instance();
        $CI->load->model("approval_model");
        $CI->db->where('id',$deal_id);
        $CI->db->where('approved',0);
        $project =$CI->db->get(db_prefix().'projects')->row();
        if($project){
            $CI->load->model('approval_model');
            $histories = $CI->approval_model->getHistory('projects',$deal_id);
            $approvedHistory =count($histories);
            $approvals =$CI->approval_model->getDealReportingLevels($project->teamleader);
            if($approvals){
                $flows = (array) $CI->workflow_model->getflows(self::$action,0,['service'=>'approval_level']);
                $finalApproval =count($approvals);
                foreach($approvals as $approval_key => $approval){
                    if($approval_key == $approvedHistory){
                        if(get_staff_user_id() ==$approval->staffid || !$approval){
                            if($approval){
                                $CI->approval_model->addApprovalHistory('projects',$deal_id,get_staff_user_id(),$CI->input->post('reason'),$CI->input->post('remarks'),$CI->input->post('status'));
                            }else{
                                $_POST['status']=1;
                                $CI->approval_model->addApprovalHistory('projects',$deal_id,0,0,'Auto Approved by system',1);
                            }
                            if($CI->input->post('status')==1){ //for approval
                                if($finalApproval-1 !=$approval_key && isset($flows[$approval_key+1])){
                                    if(isset($flows[$approval_key+1])){
                                        if(!$approvals[$approval_key+1]){
                                            $this->approveDeal($deal_id);
                                            continue ;
                                        }
                                        $approval_request_email_notification_flows =$CI->workflow_model->getflows(self::$action,0,array('service'=>'approval_request_email_notification','inactive'=>0));
                                        if($approval_request_email_notification_flows){
                                            foreach($approval_request_email_notification_flows as $approval_request_email_notification_flow){
                                                if($approval_request_email_notification_flow->configure){
                                                    $this->emailNotification($approval_request_email_notification_flow,$deal_id,$approvals[$approval_key+1]->staffid);
                                                }
                                            }
                                        }

                                    }
                                }
                                if($finalApproval-1 ==$approval_key){ //final approval
                                    $approved_email_notification_flows =$CI->workflow_model->getflows(self::$action,0,array('service'=>'approved_email_notification','inactive'=>0));
                                    if($approved_email_notification_flows){
                                        foreach($approved_email_notification_flows as $approved_email_notification_flow){
                                            $this->emailNotification($approved_email_notification_flow,$deal_id,$project->created_by);
                                        }
                                    }
                                    $CI->db->where('id',$deal_id);
                                    $CI->db->update(db_prefix().'projects',['approved'=>1]);
                                    return 'redirect_projects';
                                }
                                return true;
                            }else{ // for rejection
                                $rejected_email_notification_flows =$CI->workflow_model->getflows(self::$action,0,array('service'=>'rejected_email_notification','inactive'=>0));
                                if($rejected_email_notification_flows){
                                    foreach($rejected_email_notification_flows as $rejected_email_notification_flow){
                                        $this->emailNotification($rejected_email_notification_flow,$deal_id,$project->created_by);
                                        if($histories){
                                            foreach($histories as $history){
                                                if($history->status ==1){
                                                    $this->emailNotification($rejected_email_notification_flow,$deal_id,$history->approved_by);
                                                }
                                            }
                                        }

                                    }
                                }
                                // $CI->db->where('id',$deal_id);
                                // $CI->db->update(db_prefix().'projects',['deleted_status'=>1]);
                                // if($project->lead_id){
                                //     $leadid = $project->lead_id;
                                //     $CI->db->where('id', $deal_id);
                                //     $CI->db->update(db_prefix() . 'projects', ['deleted_status' => 1]);

                                //     $CI->db->where('rel_id', $deal_id);
                                //     $CI->db->where('rel_type', 'project');
                                //     $CI->db->update(db_prefix() . 'tasks', ['rel_type' => 'lead', 'rel_id' => $leadid]);

                                //     $CI->db->where('rel_id', $deal_id);
                                //     $CI->db->where('rel_type', 'project');
                                //     $CI->db->update(db_prefix() . 'proposals', ['rel_type' => 'lead', 'rel_id' => $leadid]);

                                //     $CI->db->where('id',$project->lead_id);
                                //     $CI->db->update(db_prefix().'leads',['project_id'=>0,'deleted_status'=>0]);
                                // }
                            }
                        }else{
                            return false;
                        } 
                    }
                }
            }
        }else{
            return false;
        }
    }

    public function emailNotification($flow,$deal_id,$staffid)
    {
        $CI = &get_instance();
        $CI->db->where('staffid',$staffid);
        $staff = $CI->db->get(db_prefix().'staff')->row();
        if($staff){
            $configure =json_decode($flow->configure,true);
            $subject =$configure['subject'];
            $fromname =$configure['fromname'];
            $message =$configure['message'];
            $CI->load->library('merge_fields/deals_merge_fields');
            $deals_merge_fields = $CI->deals_merge_fields->format($deal_id);
            $CI->load->library('merge_fields/staff_merge_fields');
            $staff_merge_fields = $CI->staff_merge_fields->format($staffid);
            $CI->load->library('merge_fields/other_merge_fields');

            $others_merge_fields = $CI->other_merge_fields->format();
            $merge_fields = array_merge($deals_merge_fields, $staff_merge_fields,$others_merge_fields);
            foreach($merge_fields as $field_key => $field_value){
                $subject =str_replace($field_key,$field_value,$subject);
                $fromname =str_replace($field_key,$field_value,$fromname);
                $message =str_replace($field_key,$field_value,$message);
            }
            $this->sendEmail($fromname,$staff->email,$subject,$message,'Approval notification');
        }
        
    }  
}
