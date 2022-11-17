<?php

use function Clue\StreamFilter\fun;

class Deal_workflow extends Workflow_app
{
    private static $module =array(
        'name'=>'deal',
        'title'=>'Deal',
        'description'=>'Deal module',
        'icon'=>'<i class="fa fa-usd  fa-fw fa-lg"></i>',
        'triggers'=>['deal_approval','deal_updated']
    );

    protected $deal_id ='';
    protected $deal;
    protected $merge_fields =[];

    private static $triggers =array(
        'deal_approval'=>[
            'title'=>'Deal approval',
            'description'=>'Workflow starts when new deal created.',
            'icon'=>'<i class="fa fa-check-square-o text-primary" aria-hidden="true"></i>',
            'triggers'=>['approval_level','condition','send_email'],
        ],
        'deal_updated'=>[
            'title'=>'Deal Updated',
            'description'=>'Workflow starts when deal updated.',
            'icon'=>'<i class="fa fa-pencil-square-o text-primary" aria-hidden="true"></i>',
            'triggers'=>['approval_level','condition','send_email'],
        ],
        'approval_level'=>[
            'title'=>'Approval Level',
            'description'=>'Workflow starts when deal approved.',
            'icon'=>'<i class="fa fa-sitemap text-warning" aria-hidden="true"></i>',
            'triggers'=>['deal_approved','deal_rejected','send_email'],
        ],
        'deal_approved'=>[
            'title'=>'Deal Approved',
            'description'=>'If deal approved',
            'icon'=>'<i class="fa fa-check text-success" aria-hidden="true"></i>',
            'triggers'=>['send_email','approval_level','condition'],
        ],
        'deal_rejected'=>[
            'title'=>'Deal Rejected',
            'description'=>'If deal rejected',
            'icon'=>'<i class="fa fa-times text-danger" aria-hidden="true"></i>',
            'triggers'=>['send_email'],
        ],
        'send_email'=>[
            'title'=>'Send Email',
            'type'=>'notification',
            'medium'=>'email',
            'description'=>'Send email notification.',
            'icon'=>'<i class="fa fa-envelope" style="color:#BB001B" aria-hidden="true"></i>',
            'triggers'=>[],
        ],
        'condition'=>[
            'title'=>'Conditions',
            'description'=>'Define deal conditions.',
            'icon'=>'<i class="fa fa-question text-warning" aria-hidden="true"></i>',
            'triggers'=>['true','false'],
        ],
        'true'=>[
            'title'=>'True',
            'description'=>'If condition true.',
            'icon'=>'<i class="fa fa-check text-success" aria-hidden="true"></i>',
            'triggers'=>['send_email','condition','approval_level'],
        ],
        'false'=>[
            'title'=>'False',
            'description'=>'If condition false.',
            'icon'=>'<i class="fa fa-times text-danger" aria-hidden="true"></i>',
            'triggers'=>['send_email','condition','approval_level'],
        ],
        
    );

    private static $mergefields =array('deal_merge_fields','staff_merge_fields','others_merge_fields');

    public function __construct()
    {
        parent::__construct();

        $this->ci->load->model('staff_model');
        $this->ci->load->model('pipeline_model');
        $this->ci->load->model('pipelinestatus_model');
        $this->ci->load->model('projects_model');
        $this->setModule(self::$module['name'],self::$module['title'],self::$module['description'],self::$module['icon'],self::$module['triggers']);

        foreach(self::$triggers as $name => $trigger){
            $this->setTrigger(self::$module['name'],$name,$trigger['title'],$trigger['description'],$trigger['icon'],$trigger['triggers'],isset($trigger['type'])?$trigger['type']:'',isset($trigger['medium'])?$trigger['medium']:'');
        }

        $this->setMergeFields(self::$module['name'], self::$mergefields);
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
                'id'=>'description',
                'label'=>'Description',
                'type'=>'string',
                'input'=>'text',
            )
        );

        $staffs =$this->ci->staff_model->get();
        if($staffs){
            $sources =array();
            foreach($staffs as $staff){
                $sources [$staff['staffid']] =$staff['firstname'].' '.$staff['lastname'];
            }
            $this->setQueryFields(
                self::$module['name'],
                array(
                    'id'=>'teamleader',
                    'label'=>'Deal Owner',
                    'type'=>'string',
                    'input'=>'select',
                    'values'=>$sources,
                    'operators'=> array('equal', 'not_equal', 'is_null', 'is_not_null')
                )
            );

            $this->setQueryFields(
                self::$module['name'],
                array(
                    'id'=>'created_by',
                    'label'=>'Deal Created by',
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
                'id'=>'project_created',
                'label'=>'Date created',
                'type'=>'date',
                'validation'=> [
                    'format'=> 'yyyy/mm/dd'
                ],
                'plugin'=> 'datepicker',
                'plugin_config'=> [
                    'format'=> 'yyyy/mm/dd',
                    'todayBtn'=> 'linked',
                    'todayHighlight'=> true,
                    'autoclose'=> true
                ]
            )
        );

        $this->setQueryFields(
            self::$module['name'],
            array(
                'id'=>'project_cost',
                'label'=>'Deal Value',
                'type'=>'double',
                'validation'=> [
                    'min'=> 0,
                    'step'=> 1
                ]
            )
        );

        $pipelines =$this->ci->pipeline_model->getPipeline_all();
        if($pipelines){
            $sources =array();
            foreach($pipelines as $pipeline){
                $sources [$pipeline['id']] =$pipeline['name'];
            }
            $this->setQueryFields(
                self::$module['name'],
                array(
                    'id'=>'pipeline_id',
                    'label'=>'Deal Pipeline',
                    'type'=>'string',
                    'input'=>'select',
                    'values'=>$sources,
                    'operators'=> array('equal', 'not_equal', 'is_null', 'is_not_null')
                )
            );
        }

        $stages =$this->ci->pipelinestatus_model->getPipelinestatuss();
        if($stages){
            $sources =array();
            foreach($stages as $stage){
                $sources [$stage['id']] =$stage['name'];
            }
            $this->setQueryFields(
                self::$module['name'],
                array(
                    'id'=>'status',
                    'label'=>'Deal Stage',
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
                'id'=>'stage_of',
                'label'=>'Deal Status',
                'type'=>'integer',
                'input'=>'select',
                'values'=> [
                    1=> 'Won',
                    2=> 'Lost'
                ],
                'operators'=> ['equal']
            )
        );
    }

    public function request_approval($deal_id)
    {
        $request_approval =$this->ci->workflow_model->getmoduleflows(self::$module['name'],['action'=>'deal_approval','parent_id'=>0]);
        if($request_approval){
            $this->deal_id =$deal_id;
            $this->deal = $this->ci->projects_model->get($deal_id);
            $this->setup();
            $request_approval =$request_approval[0];
            $this->run(self::$module['name'],$request_approval->id);
            return true;
        }

        return false;
    }

    private function setup(){
        $this->run_mergefields();
    }

    private function run_mergefields()
    {
        $this->ci->load->library('merge_fields/deals_merge_fields');
        $deals_merge_fields = $this->ci->deals_merge_fields->format($this->deal_id);
        $this->ci->load->library('merge_fields/staff_merge_fields');
        $staff_merge_fields = $this->ci->staff_merge_fields->format($this->deal->teamleader);
        $this->ci->load->library('merge_fields/other_merge_fields');
        $others_merge_fields = $this->ci->other_merge_fields->format();
        $this->merge_fields = array_merge($deals_merge_fields, $staff_merge_fields,$others_merge_fields);
    }

    protected function check_flow($flow){

    }
}