<?php

use function Clue\StreamFilter\fun;

class Lead_workflow extends Workflow_app
{
    private static $module =array(
        'name'=>'lead',
        'title'=>'Lead',
        'description'=>'Lead module',
        'icon'=>'<i class="fa fa-tty  fa-fw fa-lg"></i>',
        // 'triggers'=>['lead_created','lead_updated','lead_deleted','lead_cronjob'],
        'triggers'=>['lead_created']
    );

    protected $lead_id ='';
    protected $lead;
    protected $merge_fields =[];

    private static $triggers =array(
        'lead_created'=>[
            'title'=>'Lead Created',
            'description'=>'Workflow starts when new lead created.',
            'icon'=>'<i class="fa fa-plus text-success" aria-hidden="true"></i>',
            'triggers'=>['condition','send_email','lead_assign_staff','send_whatsapp','send_sms'],
        ],
        'lead_updated'=>[
            'title'=>'Lead Updated',
            'description'=>'Workflow starts when lead deleted.',
            'icon'=>'<i class="fa fa-pencil-square-o text-primary" aria-hidden="true"></i>',
            'triggers'=>['condition','send_email','send_whatsapp','send_sms'],
        ],
        'lead_deleted'=>[
            'title'=>'Lead Deleted',
            'description'=>'Workflow starts when lead deleted.',
            'icon'=>'<i class="fa fa-trash text-danger" aria-hidden="true"></i>',
            'triggers'=>['condition','send_email','send_whatsapp','send_sms'],
        ],
        'lead_cronjob'=>[
            'title'=>'Lead Scheduler',
            'description'=>'Workflow starts when lead deleted.',
            'icon'=>'<i class="fa fa-clock-o text-warning" aria-hidden="true"></i>',
            'triggers'=>['condition','send_email','send_whatsapp','send_sms'],
        ],
        'condition'=>[
            'title'=>'Conditions',
            'description'=>'Define lead conditions.',
            'icon'=>'<i class="fa fa-question text-warning" aria-hidden="true"></i>',
            'triggers'=>['true','false'],
        ],
        'lead_assign_staff'=>[
            'title'=>'Assign Staff',
            'description'=>'Assign staff to lead.',
            'icon'=>'<i class="fa fa-user-plus text-success" aria-hidden="true"></i>',
            'triggers'=>['send_email','send_whatsapp','send_sms'],
        ],
        'send_email'=>[
            'title'=>'Send Email',
            'type'=>'notification',
            'medium'=>'email',
            'description'=>'Send email notification.',
            'icon'=>'<i class="fa fa-envelope" style="color:#BB001B" aria-hidden="true"></i>',
            'triggers'=>[],
        ],
        'send_whatsapp'=>[
            'title'=>'Send Whatsapp',
            'type'=>'notification',
            'medium'=>'whatsapp',
            'description'=>'Send Whatsapp notification.',
            'icon'=>'<i class="fa fa-whatsapp " style="color:#25D366" aria-hidden="true"></i>
            ',
            'triggers'=>[],
        ],
        // 'send_sms'=>[
        //     'title'=>'Send SMS',
        //     'type'=>'notification',
        //     'medium'=>'sms',
        //     'description'=>'Send SMS notification.',
        //     'icon'=>'<i class="fa fa-commenting text-primary" aria-hidden="true"></i>
        //     ',
        //     'triggers'=>[],
        // ],
        'true'=>[
            'title'=>'True',
            'description'=>'If condition true.',
            'icon'=>'<i class="fa fa-check text-success" aria-hidden="true"></i>',
            'triggers'=>['send_email','condition','lead_assign_staff','send_whatsapp','send_sms'],
        ],
        'false'=>[
            'title'=>'False',
            'description'=>'If condition false.',
            'icon'=>'<i class="fa fa-times text-danger" aria-hidden="true"></i>',
            'triggers'=>['send_email','condition','lead_assign_staff','send_whatsapp','send_sms'],
        ],
    );

    private static $mergefields =array('lead_merge_fields','staff_merge_fields','others_merge_fields');

    public function __construct()
    {
        parent::__construct();
        $this->ci->load->model('leads_model');
        $this->setModule(self::$module['name'],self::$module['title'],self::$module['description'],self::$module['icon'],self::$module['triggers']);

        foreach(self::$triggers as $name => $trigger){
            $this->setTrigger(self::$module['name'],$name,$trigger['title'],$trigger['description'],$trigger['icon'],$trigger['triggers'],isset($trigger['type'])?$trigger['type']:'',isset($trigger['medium'])?$trigger['medium']:'');
        }

        $this->setMergeFields(self::$module['name'], self::$mergefields);
        $this->ci->load->model('leads_model');
        $this->setLeadQueryFields();
        
    }

    protected function setLeadQueryFields(){

        $this->setQueryFields(
            self::$module['name'],
            array(
                'id'=>'name',
                'label'=>'Name',
                'type'=>'string',
                'input'=>'text',
            )
        );

        $this->setQueryFields(
            self::$module['name'],
            array(
                'id'=>'company',
                'label'=>'Organization',
                'type'=>'string',
                'input'=>'text',
            )
        );

        $this->setQueryFields(
            self::$module['name'],
            array(
                'id'=>'email',
                'label'=>'Email',
                'type'=>'string',
                'input'=>'text',
            )
        );
        
        $this->setQueryFields(
            self::$module['name'],
            array(
                'id'=>'phonenumber',
                'label'=>'Phone number',
                'type'=>'string',
                'input'=>'text',
            )
        );

        $this->setQueryFields(
            self::$module['name'],
            array(
                'id'=>'address',
                'label'=>'Address',
                'type'=>'string',
                'input'=>'text',
            )
        );

        $this->setQueryFields(
            self::$module['name'],
            array(
                'id'=>'city',
                'label'=>'City',
                'type'=>'string',
                'input'=>'text',
            )
        );

        $countries =get_all_countries();
        if($countries){
            $sources =array();
            foreach($countries as $country){
                $sources [$country['country_id']] =$country['short_name'];
            }
            $this->setQueryFields(
                self::$module['name'],
                array(
                    'id'=>'country',
                    'label'=>'Country',
                    'type'=>'string',
                    'input'=>'select',
                    'values'=>$sources,
                    'operators'=> array('equal', 'not_equal', 'is_null', 'is_not_null')
                )
            );
        }

        $this->setQueryFields(
            self::$module['name'],
            array(
                'id'=>'state',
                'label'=>'State',
                'type'=>'string',
                'input'=>'text',
            )
        );
        
        $lead_sources =$this->ci->leads_model->get_source();
        if($lead_sources){
            $sources =array();
            foreach($lead_sources as $source){
                if($source['name'] && strlen(trim($source['name'])) >0){
                    $sources [$source['id']] =$source['name'];
                }
            }
            $this->setQueryFields(
                self::$module['name'],
                array(
                    'id'=>'source',
                    'label'=>'Source',
                    'type'=>'string',
                    'input'=>'select',
                    'values'=>$sources,
                    'operators'=> array('equal', 'not_equal', 'is_null', 'is_not_null')
                )
            );
        }
    }

    public function lead_created($lead_id){
        $lead_created_flow =$this->ci->workflow_model->getmoduleflows(self::$module['name'],['action'=>'lead_created']);
        if($lead_created_flow){
            $this->lead_id =$lead_id;
            $this->lead = $this->ci->leads_model->get($lead_id);

            $this->setup();

            $lead_created_flow =$lead_created_flow[0];
            $this->run(self::$module['name'],$lead_created_flow->id);
        }
    }

    private function setup(){
        $this->run_mergefields();
    }

    private function run_mergefields()
    {
        $this->ci->load->library('merge_fields/leads_merge_fields');
        $leads_merge_fields = $this->ci->leads_merge_fields->format($this->lead_id);
        $this->ci->load->library('merge_fields/staff_merge_fields');
        $staff_merge_fields = $this->ci->staff_merge_fields->format($this->lead->assigned);
        $this->ci->load->library('merge_fields/other_merge_fields');
        $others_merge_fields = $this->ci->other_merge_fields->format();
        $this->merge_fields = array_merge($leads_merge_fields, $staff_merge_fields,$others_merge_fields);
    }

    protected function run_condition($flow){
        if($flow->configure){
            $sql = "SELECT * FROM ".db_prefix()."leads WHERE ( ".$flow->configure['sql']." ) and id = ?";
            $params =$flow->configure['params'];
            $params [] =$this->lead_id;
            $row =$this->ci->db->query($sql, $params)->row();
            return ($row)?true:false ;
        }
    }

    protected function run_email($flow)
    {
        if($flow->configure){
            $sendto =$flow->configure['sendto'];
            $subject =$this->mergefieldsContent($this->merge_fields,$flow->configure['subject']);
            $fromname =$this->mergefieldsContent($this->merge_fields,$flow->configure['fromname']);
            $message =$this->mergefieldsContent($this->merge_fields,$flow->configure['message']);

            $sendto ='';
            if($flow->configure['sendto'] =='staff'){
                $this->ci->db->where('staffid', $this->lead->assigned);
                $staff = $this->ci->db->get(db_prefix() . 'staff')->row();
                $sendto =$staff->email;
            }elseif($flow->configure['sendto'] =='customer'){
                $sendto =$this->lead->email;
            }
            $this->sendEmail($fromname,$sendto,$subject,$message,'workflow lead created');
        }
    }

    protected function check_flow($flow){
        $staff_id =0;
        switch ($flow->action) {
            case 'lead_assign_staff':
                $configure =$flow->configure;
                if($configure['type']=='direct_assign'){
                    if($configure['assignto']){
                        $staff_id =$configure['assignto'];
                    }
                }elseif($configure['type']=='round_robin_method'){
                    $last_run =0;

                    if(isset($configure['round_robin_last_run'])){
                        $last_run =$configure['round_robin_last_run']+1;
                    }
                    

                    if($configure['stafftype'] =='staff'){
                        $staff_ids =$configure['assigntogroup'];
                    }else if($configure['stafftype'] =='roles'){
                        $staff_ids =$this->get_roles_staffs($configure['assigntorole']);
                    }else if($configure['stafftype'] =='designation'){
                        $staff_ids =$this->get_designations_staffs($configure['assigntodesignation']);
                    }

                    if(!$staff_ids[$last_run]){
                        $last_run =0;
                    }

                    if($staff_ids){
                        $staff_id =$staff_ids[$last_run];
                    }
                    if($staff_id){
                        $configure ['round_robin_last_run'] =$last_run;
                        $this->ci->workflow_model->updateFlowConfigure($flow->id,json_encode($configure));
                    }
                    
                }elseif($configure['type']=='having_less_no_of_leads'){
                    $staff_ids =array();
                    if($configure['stafftype'] =='staff'){
                        $staff_ids =$configure['assigntogroup'];
                    }else if($configure['stafftype'] =='roles'){
                        $staff_ids =$this->get_roles_staffs($configure['assigntorole']);
                    }else if($configure['stafftype'] =='designation'){
                        $staff_ids =$this->get_designations_staffs($configure['assigntodesignation']);
                    }
                    if($staff_ids){
                        $this->ci->db->where_in('assigned', $staff_ids);
                        $this->ci->db->group_by("assigned");
                        $this->ci->db->order_by('COUNT(id)', 'ASC');
                        $low_lead =$this->ci->db->get(db_prefix().'leads')->row();
                        if($low_lead){
                            $staff_id =$low_lead->assigned;
                        }
                        
                    }
                }elseif($configure['type']=='having_more_conversions'){
                    $staff_ids =array();
                    if($configure['stafftype'] =='staff'){
                        $staff_ids =$configure['assigntogroup'];
                    }else if($configure['stafftype'] =='roles'){
                        $staff_ids =$this->get_roles_staffs($configure['assigntorole']);
                    }else if($configure['stafftype'] =='designation'){
                        $staff_ids =$this->get_designations_staffs($configure['assigntodesignation']);
                    }
                    if($staff_ids){
                        $this->ci->db->where_in('assigned', $staff_ids);
                        $this->ci->db->where('project_id !=', 0);
                        $this->ci->db->group_by("assigned");
                        $this->ci->db->order_by('COUNT(id)', 'DESC');
                        $more_conversion =$this->ci->db->get(db_prefix().'leads')->row();
                        if($more_conversion){
                            $staff_id =$more_conversion->assigned;
                        }
                    }
                }
                break;
            
            default:
                break;
        }
        if($staff_id){
            
            $this->lead->assigned =$staff_id;
            // pr('assgined to '.$this->lead->assigned);
            $this->ci->db->where('id',$this->lead_id);
            $this->ci->db->update(db_prefix().'leads',['assigned'=>$staff_id]);
        }
        
    }

    protected function run_whatsapp($flow)
    {
        if($flow->configure){
            $sendto =$flow->configure['sendto'];
            if($flow->configure['sendto'] =='staff'){
                $this->ci->db->where('staffid', $this->lead->assigned);
                $staff = $this->ci->db->get(db_prefix() . 'staff')->row();
                $sendto =$this->getCountryCallingCode($staff->phone_country_code) . $staff->phonenumber;
            }elseif($flow->configure['sendto'] =='customer'){
                $sendto =$this->getCountryCallingCode($this->lead->phone_country_code) .$this->lead->phonenumber;
            }
            $this->sendWhatsapp($sendto, $flow, $this->merge_fields);
        }
    }
}