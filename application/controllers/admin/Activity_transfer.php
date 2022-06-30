<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Activity_transfer extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Activity_user_model');
    }

    
    public function index()
    {
    	if (!has_permission('Activity_user', '', 'view')) {
            access_denied('Activity_user');
        }
        
        if ($this->input->post()) {
            //pre($_POST);
            $data = array();
            $id = $_POST['emp_id'];
            if(isset($_POST['action']) && $_POST['action'] == 'Transfer'){
                $dataUpdate['deavite_follow_ids'] = $this->get_deavite_follow_ids($id);
                $da = json_decode($dataUpdate['deavite_follow_ids']);
                if(count((array)$da->task->task_assigned) == 0){
                    set_alert('warning', _l('noaccount_transfered'));
                } else {
                    $content = $this->getTransferDetailsHtml();
                    $this->db->trans_begin();
                    
                    // $this->db->where('staffid', $id);
                    // $this->db->update(db_prefix() . 'staff', $dataUpdate);
                    $this->deavite_follow($dataUpdate,$id,$_POST['assign']);
                    //pre($content);

                    if($this->db->trans_status() === FALSE){
                        $this->db->trans_rollback();
                        set_alert('warning', _l('error_transfered'));
                    }else{
                        $this->db->trans_commit();
                        $this->Activity_user_model->printOwnerHierarchyWithoutAdmin($_POST['assign'],$content);
                        $this->Activity_user_model->printOwnerHierarchyWithoutAdmin($_POST['emp_id'],$content);
                    }
                    set_alert('success', _l('activity_transfer'));
                }
                
                redirect(admin_url('Activity_transfer','refresh'));   
            }
        }

//        pre($data['files']);\
        //$data['history'] = $this->AccountTransfer_model->get_transfer_history();
        $data['employees']    = $this->Activity_user_model->get_staffs_whom_follow();
        $data['title']     = _l('ActivityTransfer');
        $data['bodyclass'] = 'dynamic-create-groups';
        $this->load->view('admin/activity/activity_to_transfer', $data);
    }

    public function rollback() {
        if(isset($_POST['id'])){
            $history = $this->AccountTransfer_model->get_transfer_history_byid($_POST['id']);
            //pre($history);
            $content = $this->getRollbackDetailsHtml($history);
            $data['deavite_follow_ids'] = $history->transfer_details;

            $this->db->trans_begin();
            $this->deavite_follow($data,$history->trans_to,$history->trans_from);
                    
            $this->db->where('id',$_POST['id']);
            $this->db->update(db_prefix() . 'transfer_history', ['status' => 0,'rollback_on' => date('Y-m-d H:i:s')]);
            
            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $output = array();
                $output['err'] = _l('error_rollback');
                echo json_encode($output);     
            }else{
                $this->db->trans_commit();
                $this->AccountTransfer_model->printOwnerHierarchyWithoutAdmin($history->trans_to,$content);
                $this->AccountTransfer_model->printOwnerHierarchyWithoutAdmin($history->trans_from,$content);
                $output = array();
                $output['message'] = _l('account_rollback');
                echo json_encode($output);     
            }
                   
            exit();
        }
    }
    
    
    /* deavite follow */
    public function deavite_follow($data,$id,$to_id)
    {
        //pre($data);
        $da = json_decode($data['deavite_follow_ids']);
        $result = 0;
        //---task assigned 

        if(isset($da->task->task_assigned) && count((array)$da->task->task_assigned) > 0){
            $this->db->where_in('taskid',$da->task->task_assigned);
            $this->db->update(db_prefix() . 'task_assigned', ['staffid' => $to_id]);

             foreach($da->task->task_assigned as $vt){
            //     //Add Log
                 $description = 'Activity Moved From Staff ('.get_staff_full_name($id).') To ('.get_staff_full_name($to_id).') [ID: '.$vt.']';
                 $log = [
                     'description' => $description,
                     'date'        => date('Y-m-d H:i:s'),
                     ];
                      $log['staffid'] = get_staff_full_name(get_staff_user_id());
                      $this->db->insert(db_prefix() . 'activity_log', $log);
             }
        }

        return true;
    }

    public function get_deavite_follow_ids($id)
    {
        $returnarr = array();
        
        //---task assigned 
        $this->db->where('staffid',$id);
        $task_assigned = $this->db->select('taskid')->get(db_prefix() . 'task_assigned')->result();
        $returnarr['task']['task_assigned'] = array();
        foreach($task_assigned as $kt => $vt){
            $returnarr['task']['task_assigned'][] = $vt->taskid;
        }

        $returnarr['moveto'] = $_POST['assign'];

        return json_encode($returnarr);
    }

    public function getToEmployees() {
        //pre($_POST);
        $moveto = '';
        $output = array();
        $data = $this->Activity_user_model->get_to_staffs($_POST['emp_id']);
        $member = $this->staff_model->get($_POST['emp_id']);
        $deavite_follow_ids = $member->deavite_follow_ids;
        if(isset($member->deavite_follow_ids)) {
            $da = json_decode($deavite_follow_ids);
            if($da->moveto > 0) {
                $moveto = $da->moveto;
            }
        }
        //if(isset($da->projects->teamleader)
        if($data) {
            foreach($data as $val) {
                $selected = '';
                if($moveto == $val["staffid"]) {
                    $selected = 'selected';
                }
                $html .= '<option value="'.$val["staffid"].'" '.$selected.'>'.$val["firstname"].'</option>';
            } 
            $output['html'] = $html;
            if($moveto > 0) {
                $output['rollback'] = 'rollback';
                $output['rollback_id'] = $moveto;
            }
        } else {
            $output['html'] = '';
            $output['option'] = '';
        }
        echo json_encode($output);            
        exit();
    }

    
    public function getTransferDetails() {
        $id = $_REQUEST['emp_id'];
        $toid = $_REQUEST['assign'];
        $own_deals = $followed_deals = $tasks = $organization = $contacts_cnt = 0;
       
        //---task assigned 
        $this->db->where('staffid',$id);
        $task_assigned = $this->db->select('taskid')->get(db_prefix() . 'task_assigned')->result();
        $returnarr['task']['task_assigned'] = array();
        foreach($task_assigned as $kt => $vt){
            $tasks++;
        }

        if($tasks <= 1) {
            $tasks = $tasks.' - Activity';
        } else {
            $tasks = $tasks.' - Activities';
        }

// $staffs = get_staff($id);
// pre($staffs);

        $fromResult = $this->Activity_user_model->getuserDetails($id);
        $toResult = $this->Activity_user_model->getuserDetails($toid);
        //pre($fromResult->firstname);
        $html = '<p style="font-size:15px;"><b>Do you want to transfer below datas?</b></p>
                <p><b>From:</b> '.$fromResult->firstname.' ('.$fromResult->email.'), '.$fromResult->desig.'</p>
                <p>'.$tasks.'</p>
                <p><b>To:</b> '.$toResult->firstname.' ('.$toResult->email.'), '.$toResult->desig.'</p>
                <p><b style="color:red;">Note:</b></p>
                <p><b style="color:red;">If someone already transferred their datas to you, they can’t be Rollback!</b></p>';
        
        $output['html'] = $html;
        echo json_encode($output);
    }

    public function getRollbackDetailsHtml($history) {
        $id = $history->trans_to;
        $toid = $history->trans_from;
        $data['deavite_follow_ids'] = $history->transfer_details;
        $da = json_decode($data['deavite_follow_ids']);
        $tasks = count((array)$da->task->task_assigned);
        
        if($tasks <= 1) {
            $tasks = $tasks.' - Activity';
        } else {
            $tasks = $tasks.' - Activities';
        }
        
        $fromResult = $this->Activity_user_model->getuserDetails($id);
        $toResult = $this->Activity_user_model->getuserDetails($toid);
        //pre($fromResult->firstname);
        $html = '<p style="font-size:15px;"><b>Bellow Datas are Rolled Back </b></p>
                <p><b>From:</b> '.$fromResult->firstname.' ('.$fromResult->email.'), '.$fromResult->desig.'</p>
                <p>'.$tasks.'</p>
                <p><b>To:</b> '.$toResult->firstname.' ('.$toResult->email.'), '.$toResult->desig.'</p>';
        
        return $html;
    }

    public function getTransferDetailsHtml() {
        $id = $_POST['emp_id'];
        $toid = $_POST['assign'];
        $own_deals = $followed_deals = $tasks = $organization = $contacts_cnt = 0;
     
        //---task assigned 
        $this->db->where('staffid',$id);
        $task_assigned = $this->db->select('taskid')->get(db_prefix() . 'task_assigned')->result();
        $returnarr['task']['task_assigned'] = array();
        foreach($task_assigned as $kt => $vt){
            $tasks++;
        }
        
        if($tasks <= 1) {
            $tasks = $tasks.' - Activity';
        } else {
            $tasks = $tasks.' - Activities';
        }

        $html = '<p style="font-size:15px;"><b>Bellow Datas are Transfered </b></p>
                <p><b>From:</b> '.get_staff_full_name($id).' - <b>To:</b> '.get_staff_full_name($toid).'</p>
                <p>'.$tasks.'</p>';
        
        return $html;
        //echo json_encode($output);
    }

    public function getaddfollowerfields() {
        $data = $this->Activity_user_model->get_staffs_whom_follow();
        $html = '';
            $html .= '<div style="height:40px; clear:both;" class="productdiv" id="'.$_POST['length'].'"><div class="col-md-6">
            <select name="employee[]" class="form-control" required><option value="">--Select Employee--</option>';
            foreach($data as $val) {
                if (!in_array($val['staffid'], $_POST['employee']) && $val['staffid'] != $_POST['emp_id']) {
                    $disabled = '';
                    if($val['reporting_to'] == $_POST['emp_id']) {
                        $disabled = 'disabled';
                    }
                    $html .= '<option value="'.$val["staffid"].'" '.$disabled.'>'.$val["firstname"].'</option>';
                }
            } 
            $html .= '</select>';
            $html .= '</div>
            <div class="col-md-4">
            <select name="permission[]" class="form-control">
                                      <option value="view" selected>View</option>
                                      <option value="edit">Edit</option>
                                    </select></div>';
                $html .= '<div class="col-md-2"><a href="javascript:void(0);" class="removefollower_button" title="Remove field" style="position:relative; top:8px; left:15px"><i class="fa fa-trash"></i></a>
            </div></div>';
            echo $html;
        exit;
    }
	

}