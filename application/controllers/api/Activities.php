<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Activities extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        load_admin_language();
        $this->load->model('Authenticationapi_model');
        $this->load->model('projects_model');
        $this->load->model('tasktype_model');
        $this->load->model('tasks_model');
        $this->load->model('tasktype_model');
        $this->load->model('staff_model');
        $this->load->model('pipeline_model');
        $this->load->model('gdpr_model');
        $this->load->model('api_model');
        $this->load->model('clients_model');
        $this->load->helper('projects_helper');
        $this->load->helper('tasks_helper');

        $this->load->library([
            'app_object_cache',
            'app_tabs'
        ]);

        $postdata = file_get_contents("php://input");
        $_POST = (array) json_decode($postdata,true);
    }

    // get activites
    public function getall()
    {
        if(isset($_POST['search'])){
            $_POST['search'] =array('value'=>$_POST['search']);
        }
        $length =25;
        if(isset($_POST['page'])){
            if($_POST['page'] ==0){
                $_POST['page'] =1;
            }
            $_POST['start'] =((int)$_POST['page']-1)*$length;
            $_POST['length'] =$length;
        }
        $result =$this->tasks_model->get_tasks_list(true);
        if($result){
            $tasks =$result['rResult'];
            if($tasks){
                $this->api_model->response_ok(true,$tasks,'');
            }else{
                $this->api_model->response_ok(true,[],'No records found');
            } 
        }else{
            $this->api_model->response_bad_request(true,[],'No records found');
        }
               
    }

    // add new activity
    public function post()
    {
        if (!has_permission('tasks', '', 'create')) {
            $this->api_model->response_bad_request(false,[],_l('access_denied'));
        }
        $data                = $this->input->post();
        $data['description'] = $this->input->post('description', false);
        $data['duedate'] = $this->input->post('duedate', null);

        $valid =$this->tasks_model->validate_task_form_data($data);
        if($valid !==true){
            $this->api_model->response_bad_request(false,$valid,'Could not add activity');
        }

        if(isset($data['task_mark_complete_id']) && !empty($data['task_mark_complete_id'])){
            $this->tasks_model->mark_as(5, $data['task_mark_complete_id']);
        }
        if(isset($data['task_mark_complete_id'])){
            unset($data['task_mark_complete_id']);
        }

        
        $data_assignee = $data['assignees'];
        unset($data['assignees']);
        $id   = $data['taskid']  = $this->tasks_model->add($data);
        foreach($data_assignee as $taskey => $tasvalue ){
            $data['assignee'] = $tasvalue;
           
            $this->tasks_model->add_task_assignees($data);
        }
        $_id     = false;
        $success = false;
        $message = '';
        if ($id) {
            $success       = true;
            $_id           = $id;
            $message       = _l('added_successfully', _l('task'));
            $uploadedFiles = handle_task_attachments_array($id);
            if ($uploadedFiles && is_array($uploadedFiles)) {
                foreach ($uploadedFiles as $file) {
                    $this->misc_model->add_attachment_to_database($id, 'task', [$file]);
                }
            }
            $this->api_model->response_ok(true,[],$message);
        }
        $this->api_model->response_bad_request(false,[],'Could not add activity');
    }

    // update existing activity by id
    public function put($id)
    {
		if (!has_permission('tasks', '', 'edit')) {
            $this->api_model->response_bad_request(false,[],_l('access_denied'));
        }

        $this->db->where('id', $id);
        $task = $this->db->get(db_prefix() . 'tasks')->row();
        if(!$task){
            $this->api_model->response_bad_request(false,$valid,'Could not update activity');
        }
        $data                = $this->input->post();
        $data['description'] = $this->input->post('description', false);
        $valid =$this->tasks_model->validate_task_form_data($data,$id);
        if($valid !==true){
            $this->api_model->response_bad_request(false,$valid,'Could not update activity');
        }
        $data_assignee = $data['assignees'];
        unset($data['assignees']);

        $success = $this->tasks_model->update($data, $id);
        $data['taskid'] =  $id;
        $task_assignees_already     = $this->tasks_model->get_task_assignees($id);
        $task_assignees_ids = [];
        foreach ($task_assignees_already as $aa) {
            array_push($task_assignees_ids, $aa['assigneeid']);
        }
        
        foreach($data_assignee as $taskey => $tasvalue ){
            if(!in_array($tasvalue,$task_assignees_ids)){
                $data['assignee'] = $tasvalue;
                $this->tasks_model->add_task_assignees($data);
            }
        }

        if ($success) {
            $message = _l('updated_successfully', _l('task'));
            $this->api_model->response_ok(true,[],$message);
        }
        $this->api_model->response_bad_request(false,[],'Could not add activity');
        
    }

    // delete existing activity by id
    public function delete($id)
    {
        
		if (!has_permission('tasks', '', 'delete')) {
            $this->api_model->response_bad_request(false,[],_l('access_denied'));
        }
        $success = $this->tasks_model->delete_task($id);
        $message = _l('problem_deleting', _l('task_lowercase'));
        if ($success) {
            $message = _l('deleted', _l('task'));
            $this->api_model->response_ok(true,[],$message);
        } else {
            $this->api_model->response_bad_request(false,[],$message);
        }
    }

    // get existing activity by id
    public function get($id)
    {
        $task =$this->tasks_model->get($id);
        if($task){
            $this->api_model->response_ok(true,$task,'');
        }else{
            $this->api_model->response_bad_request(false,[],'No data found');
        }
        
    }

    // get activity custom fields
    public function getcustomfields()
    {
        $custom_fields =get_custom_fields('tasks');
        if($custom_fields){
            $this->api_model->response_ok(true,$custom_fields,'');
        }else{
            $this->api_model->response_ok(true,[],'No data found');
        }
    }

    // mark as done activity by id
    public function markasdone($id)
    {
        $status =5;
        if ($this->tasks_model->is_task_assignee(get_staff_user_id(), $id) || $this->tasks_model->is_task_creator(get_staff_user_id(), $id) || has_permission('tasks', '', 'edit')) {
            $success = $this->tasks_model->mark_as($status, $id);
            if ($success) {
                $message = _l('task_marked_as_success', format_task_status($status, true, true));
                $this->api_model->response_ok(true,[],$message);
            }
        }
        $this->api_model->response_bad_request(false,[],'Could not update activity status');
    }

    // unmark as done activity by id
    public function unmarkasdone($id)
    {
        if ($this->tasks_model->is_task_assignee(get_staff_user_id(), $id) || $this->tasks_model->is_task_creator(get_staff_user_id(), $id) || has_permission('tasks', '', 'edit')) {
            $success = $this->tasks_model->unmark_complete($id);
            $message = '';
            if ($success) {
                $message = _l('task_unmarked_as_complete');
                $this->api_model->response_ok(true,[],$message);
            }
        }
        $this->api_model->response_bad_request(false,[],'Could not update activity status');
    }
}
