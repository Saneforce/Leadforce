<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AccountTransfer extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AccountTransfer_model');
    }

    
    public function index()
    {
    	if (!has_permission('AccountTransfer', '', 'view')) {
            access_denied('AccountTransfer');
        }
        
        if ($this->input->post()) {
            //pre($_POST);
            $data = array();
            $id = $_POST['emp_id'];
            if(isset($_POST['action']) && $_POST['action'] == 'Transfer'){
                $dataUpdate['deavite_follow_ids'] = $this->get_deavite_follow_ids($id);
                $da = json_decode($dataUpdate['deavite_follow_ids']);
                if(count((array)$da->projects->teamleader) == 0 
                && count((array)$da->projects->project_members) == 0
                && count((array)$da->task->task_assigned) == 0
                && count((array)$da->clients->addedfrom) == 0
                && count((array)$da->contacts->addedfrom) == 0){
                    set_alert('warning', _l('noaccount_transfered'));
                } else {
                    $content = $this->getTransferDetailsHtml();
                    $this->db->trans_begin();
                    
                    // $this->db->where('staffid', $id);
                    // $this->db->update(db_prefix() . 'staff', $dataUpdate);
                    $this->deavite_follow($dataUpdate,$id,$_POST['assign']);
                    //pre($content);
                    
                    $history = array();
                    $history['trans_from'] = $id;
                    $history['trans_to'] = $_POST['assign'];
                    $history['own_deals'] = count((array)$da->projects->teamleader);
                    $history['follow_deals'] = count((array)$da->projects->project_members);
                    $history['activity'] = count((array)$da->task->task_assigned);
                    $history['organizations'] = count((array)$da->clients->addedfrom);
                    $history['contacts'] = count((array)$da->contacts->addedfrom);
                    $history['transfer_by'] = get_staff_user_id();
                    $history['transfer_details'] = $dataUpdate['deavite_follow_ids'];
                    $this->AccountTransfer_model->storeTransferHierarchy($history);

                    $this->db->where('trans_to',$id);
                    $this->db->where('status',1);
                    $this->db->update(db_prefix() . 'transfer_history', ['status' => 2]);

                    if($this->db->trans_status() === FALSE){
                        $this->db->trans_rollback();
                        set_alert('warning', _l('error_transfered'));
                    }else{
                        $this->db->trans_commit();
                        $this->AccountTransfer_model->printOwnerHierarchyWithoutAdmin($_POST['assign'],$content);
                        $this->AccountTransfer_model->printOwnerHierarchyWithoutAdmin($_POST['emp_id'],$content);
                    }
                    set_alert('success', _l('account_transfered'));
                }
                
                redirect(admin_url('AccountTransfer'));   
            }
            // if(isset($_POST['action']) && $_POST['action'] == 'Rollback'){
            //     $member = $this->staff_model->get($id);
            //     $data['deavite_follow_ids'] = $member->deavite_follow_ids;
            //     $this->deavite_follow($data,$_POST['assign'],$id);

            //     $dataUpdate['deavite_follow_ids'] = '';
            //     $this->db->where('staffid', $id);
            //     $this->db->update(db_prefix() . 'staff', $dataUpdate);

            //     set_alert('success', _l('account_rollback'));
            //     redirect(admin_url('AccountTransfer'));   
            // }
        }

//        pre($data['files']);\
        $data['history'] = $this->AccountTransfer_model->get_transfer_history();
        $data['employees']    = $this->AccountTransfer_model->get_staffs_whom_follow();
        $data['title']     = _l('AccountTransfer');
        $data['bodyclass'] = 'dynamic-create-groups';
        $this->load->view('admin/clients/account_transfer', $data);
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
        //---lead teamleader
        if(isset($da->leads->assigned) && count((array)$da->leads->assigned) > 0){
            $this->db->where_in('id',$da->leads->assigned);
            $this->db->update(db_prefix() . 'leads', ['assigned' => $to_id]);

            foreach($da->leads->assigned as $vt){
                //Add Log
                $description = 'Leads changed From Staff ('.get_staff_full_name($id).') To ('.get_staff_full_name($to_id).') [ID: '.$vt.']';
                $log = [
                    'description' => $description,
                    'date'        => date('Y-m-d H:i:s'),
                    ];
                     $log['staffid'] = get_staff_full_name(get_staff_user_id());
                     $this->db->insert(db_prefix() . 'activity_log', $log);
            }
        }
        //---deal teamleader
        if(isset($da->projects->teamleader) && count((array)$da->projects->teamleader) > 0){
            $this->db->where_in('id',$da->projects->teamleader);
            $this->db->update(db_prefix() . 'projects', ['teamleader' => $to_id]);

            foreach($da->projects->teamleader as $vt){
                //Add Log
                $description = 'Project Owner changed From Staff ('.get_staff_full_name($id).') To ('.get_staff_full_name($to_id).') [ID: '.$vt.']';
                $log = [
                    'description' => $description,
                    'date'        => date('Y-m-d H:i:s'),
                    ];
                     $log['staffid'] = get_staff_full_name(get_staff_user_id());
                     $this->db->insert(db_prefix() . 'activity_log', $log);
            }
        }

        //---deal members
        if(isset($da->projects->project_members) && count((array)$da->projects->project_members) > 0){
            $this->db->where_in('project_id',$da->projects->project_members);
            $this->db->update(db_prefix() . 'project_members', ['staff_id' => $to_id]);
            
            
            foreach($da->projects->project_members as $vt){
                $returnarr['projects']['project_members'][] = $vt->project_id;
                //Add Log
                $description = 'Project Follower changed From Staff ('.get_staff_full_name($id).') To ('.get_staff_full_name($to_id).') [ID: '.$vt.']';
                $log = [
                    'description' => $description,
                    'date'        => date('Y-m-d H:i:s'),
                    ];
                    $log['staffid'] = get_staff_full_name(get_staff_user_id());
                    $this->db->insert(db_prefix() . 'activity_log', $log);
            }
        }
        //---task assigned 

        if(isset($da->task->task_assigned) && count((array)$da->task->task_assigned) > 0){
            $this->db->where_in('taskid',$da->task->task_assigned);
            $this->db->update(db_prefix() . 'task_assigned', ['staffid' => $to_id]);

            foreach($da->task->task_assigned as $vt){
                //Add Log
                $description = 'Activity Moved From Staff ('.get_staff_full_name($id).') To ('.get_staff_full_name($to_id).') [ID: '.$vt.']';
                $log = [
                    'description' => $description,
                    'date'        => date('Y-m-d H:i:s'),
                    ];
                     $log['staffid'] = get_staff_full_name(get_staff_user_id());
                     $this->db->insert(db_prefix() . 'activity_log', $log);
            }
        }

        //---Organization
        if(isset($da->clients->addedfrom) && count((array)$da->clients->addedfrom) > 0){
            $this->db->where_in('userid',$da->clients->addedfrom);
            $this->db->update(db_prefix() . 'clients', ['addedfrom' => $to_id]);
            
            foreach($da->clients->addedfrom as $vt){
                $description = 'Organization Owner changed From Staff ('.get_staff_full_name($id).') To ('.get_staff_full_name($to_id).') [ID: '.$vt.']';
                $log = [
                    'description' => $description,
                    'date'        => date('Y-m-d H:i:s'),
                    ];
                     $log['staffid'] = get_staff_full_name(get_staff_user_id());
                     $this->db->insert(db_prefix() . 'activity_log', $log);
            }
            //echo $this->db->last_query(); exit;
        }

        //---Contact person
        if(isset($da->contacts->addedfrom) && count((array)$da->contacts->addedfrom) > 0){
            $this->db->where_in('id',$da->contacts->addedfrom);
            $this->db->update(db_prefix() . 'contacts', ['addedfrom' => $to_id]);

            foreach($da->contacts->addedfrom as $vt){
                //Add Log
                $description = 'Contact Person changed From Staff ('.get_staff_full_name($id).') To ('.get_staff_full_name($to_id).') [ID: '.$vt.']';
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
        //---Lead teamleader
        $this->db->where('assigned',$id);
        $leads = $this->db->select('id')->get(db_prefix() . 'leads')->result();
        $returnarr['leads']['assigned'] = array();
        foreach($leads as $kt => $vt){
            $returnarr['leads']['assigned'][] = $vt->id;
        }

        //---deal teamleader
        $this->db->where('teamleader',$id);
        $teamleader = $this->db->select('id')->get(db_prefix() . 'projects')->result();
        $returnarr['projects']['teamleader'] = array();
        foreach($teamleader as $kt => $vt){
            $returnarr['projects']['teamleader'][] = $vt->id;
        }

        //---deal members
        $this->db->where('staff_id',$id);
        $project_members = $this->db->select('project_id')->get(db_prefix() . 'project_members')->result();
        $returnarr['projects']['project_members'] = array();
        foreach($project_members as $kt => $vt){
            $returnarr['projects']['project_members'][] = $vt->project_id;
        }

        //---task assigned 
        $this->db->where('staffid',$id);
        $task_assigned = $this->db->select('taskid')->get(db_prefix() . 'task_assigned')->result();
        $returnarr['task']['task_assigned'] = array();
        foreach($task_assigned as $kt => $vt){
            $returnarr['task']['task_assigned'][] = $vt->taskid;
        }

        //--- Organization
        $this->db->where('addedfrom',$id);
        $clients = $this->db->select('userid')->get(db_prefix() . 'clients')->result();
        $returnarr['clients']['addedfrom'] = array();
        foreach($clients as $kt => $vt){
            $returnarr['clients']['addedfrom'][] = $vt->userid;
        }

        //---Contact Person 
        $this->db->where('addedfrom',$id);
        $contacts = $this->db->select('id')->get(db_prefix() . 'contacts')->result();
        $returnarr['contacts']['addedfrom'] = array();
        foreach($contacts as $kt => $vt){
            $returnarr['contacts']['addedfrom'][] = $vt->id;
        }
        $returnarr['moveto'] = $_POST['assign'];

        return json_encode($returnarr);
    }

    public function getToEmployees() {
        //pre($_POST);
        $moveto = '';
        $output = array();
        $data = $this->AccountTransfer_model->get_to_staffs($_POST['emp_id']);
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

    public function geteditfollowerfields() {
        $output = array();
        $data = $this->AccountTransfer_model->get_staffs_whom_follow();
        if($_POST['assign'] == 1) {
            $check_permission = $this->AccountTransfer_model->check_followers_permission($_POST['emp_id']);
            //pre($check_permission);
            if(isset($check_permission)) {
                if($check_permission->p_type == 1) {
                    $check = $this->AccountTransfer_model->check_followers($_POST['emp_id']);
                    if(!empty($check)) {
                        $html = '';
                        $i = 0;
                        foreach($check as $checkdata) {
                            $html .= '<div style="height:40px; clear:both;" class="productdiv" id="'.$i.'">
                            <div class="col-md-6">
                            <select name="employee[]" class="form-control" required><option value="">--Select Employee--</option>';
                            foreach($data as $val) {
                                if ($val['staffid'] != $_POST['emp_id']) {
                                    $selected = $disabled = "";
                                    if($val['staffid'] == $checkdata['follower_id']) {
                                        $selected = 'selected';
                                    }
                                    if($val['reporting_to'] == $_POST['emp_id']) {
                                        $disabled = 'disabled';
                                    }
                                    $html .= '<option value="'.$val["staffid"].'" '.$selected.' '.$disabled.'>'.$val["firstname"].'</option>';
                                }
                            } 
                            $view = $edit = "";
                            if($checkdata['permission'] == 'view') {
                                $view = 'selected';
                            }
                            if($checkdata['permission'] == 'edit') {
                                $edit = 'selected';
                            }
                            $html .= '</select>';
                            $html .= '</div>
                            <div class="col-md-4">
                            <select name="permission[]" class="form-control">
                                                    <option value="view" '.$view.'>View</option>
                                                    <option value="edit" '.$edit.'>Edit</option>
                                                    </select></div>';
                                if($i == 0) {
                                    $html .= '<div class="col-md-2"></div></div>';
                                } else {
                                    $html .= '<div class="col-md-2"><a href="javascript:void(0);" class="removefollower_button" title="Remove field" style="position:relative; top:8px; left:15px"><i class="fa fa-trash"></i></a>
                                    </div></div> ';
                                }
                            $i++;
                        }
                        $output['cnt'] = $i;
                        $output['html'] = $html;
                        $output['option'] = $check_permission->p_type;
                    }
                } else {
                    $output['cnt'] = '';
                    $output['html'] = '';
                    $output['option'] = $check_permission->p_type;
                }
            } else {
                $html = '';
                $html .= '<div style="height:40px; clear:both;" class="productdiv" id="0">
                <div class="col-md-6">
                <select name="employee[]" class="form-control" required><option value="">--Select Employee--</option>';
                foreach($data as $val) {
                    if ($val['staffid'] != $_POST['emp_id']) {
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
                    $html .= '<div class="col-md-2">
                </div></div> ';
                $output['cnt'] = '';
                $output['html'] = $html;
                $output['option'] = '';    
            }
        } else {
            $html = '';
            $html .= '<div style="height:40px; clear:both;" class="productdiv" id="0">
            <div class="col-md-6">
            <select name="employee[]" class="form-control" required><option value="">--Select Employee--</option>';
            foreach($data as $val) {
                if ($val['staffid'] != $_POST['emp_id']) {
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
                $html .= '<div class="col-md-2">
            </div></div> ';
            $output['cnt'] = '';
            $output['html'] = $html;
            $output['option'] = '';
        }
        echo json_encode($output);            
        exit();
    }

    public function getTransferDetails() {
        $id = $_POST['emp_id'];
        $toid = $_POST['assign'];
        $own_deals = $followed_deals = $tasks = $organization = $contacts_cnt = $leads = 0;
        //---lead assigned 
        $this->db->where('assigned',$id);
        $lead_assigned = $this->db->select('id')->get(db_prefix() . 'leads')->result();
        $returnarr['leads']['assigned'] = array();
        foreach($lead_assigned as $kt => $vt){
            $leads++;
        }

        if($leads <= 1) {
            $leads = $leads.' - Lead';
        } else {
            $leads = $leads.' - Leads';
        }

        //---deal teamleader
        $this->db->where('teamleader',$id);
        $teamleader = $this->db->select('id')->get(db_prefix() . 'projects')->result();
        $returnarr['projects']['teamleader'] = array();
        foreach($teamleader as $kt => $vt){
            $own_deals++;
        }
        if($own_deals <= 1) {
            $owndeal = $own_deals.' - Own Deal';
        } else {
            $owndeal = $own_deals.' - Own Deals';
        }

        //---deal members
        $this->db->where('staff_id',$id);
        $project_members = $this->db->select('project_id')->get(db_prefix() . 'project_members')->result();
        $returnarr['projects']['project_members'] = array();
        foreach($project_members as $kt => $vt){
            $followed_deals++;
        }

        if($followed_deals <= 1) {
            $fdeal = $followed_deals.' - Followed Deal';
        } else {
            $fdeal = $followed_deals.' - Followed Deals';
        }

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

        //--- Organization
        $this->db->where('addedfrom',$id);
        $clients = $this->db->select('userid')->get(db_prefix() . 'clients')->result();
        $returnarr['clients']['addedfrom'] = array();
        foreach($clients as $kt => $vt){
            $organization++;
        }

        if($organization <= 1) {
            $organization = $organization.' - Organization';
        } else {
            $organization = $organization.' - Organizations';
        }

        //---Contact Person 
        $this->db->where('addedfrom',$id);
        $contacts = $this->db->select('id')->get(db_prefix() . 'contacts')->result();
        foreach($contacts as $kt => $vt){
            $contacts_cnt++;
        }

        if($contacts_cnt <= 1) {
            $contacts_cnt = $contacts_cnt.' - Contact';
        } else {
            $contacts_cnt = $contacts_cnt.' - Contacts';
        }
// $staffs = get_staff($id);
// pre($staffs);

        $fromResult = $this->AccountTransfer_model->getuserDetails($id);
        $toResult = $this->AccountTransfer_model->getuserDetails($toid);
        //pre($fromResult->firstname);
        $html = '<p style="font-size:15px;"><b>Do you want to transfer below datas?</b></p>
                <p><b>From:</b> '.$fromResult->firstname.' ('.$fromResult->email.'), '.$fromResult->desig.'</p>
                <p>'.$leads.'</p>
                <p>'.$owndeal.'</p>
                <p>'.$fdeal.'</p>
                <p>'.$tasks.'</p>
                <p>'.$organization.'</p>
                <p>'.$contacts_cnt.'</p>
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
        $own_deals = count((array)$da->projects->teamleader);
        $fdeal = count((array)$da->projects->project_members);
        $tasks = count((array)$da->task->task_assigned);
        $organization = count((array)$da->clients->addedfrom);
        $contacts_cnt = count((array)$da->contacts->addedfrom);
        
        if($own_deals <= 1) {
            $owndeal = $own_deals.' - Own Deal';
        } else {
            $owndeal = $own_deals.' - Own Deals';
        }
        if($fdeal <= 1) {
            $fdeal = $fdeal.' - Followed Deal';
        } else {
            $fdeal = $fdeal.' - Followed Deals';
        }
        if($tasks <= 1) {
            $tasks = $tasks.' - Activity';
        } else {
            $tasks = $tasks.' - Activities';
        }
        if($organization <= 1) {
            $organization = $organization.' - Organization';
        } else {
            $organization = $organization.' - Organizations';
        }
        if($contacts_cnt <= 1) {
            $contacts_cnt = $contacts_cnt.' - Contact';
        } else {
            $contacts_cnt = $contacts_cnt.' - Contacts';
        }
        
        $fromResult = $this->AccountTransfer_model->getuserDetails($id);
        $toResult = $this->AccountTransfer_model->getuserDetails($toid);
        //pre($fromResult->firstname);
        $html = '<p style="font-size:15px;"><b>Bellow Datas are Rolled Back </b></p>
                <p><b>From:</b> '.$fromResult->firstname.' ('.$fromResult->email.'), '.$fromResult->desig.'</p>
                <p>'.$owndeal.'</p>
                <p>'.$fdeal.'</p>
                <p>'.$tasks.'</p>
                <p>'.$organization.'</p>
                <p>'.$contacts_cnt.'</p>
                <p><b>To:</b> '.$toResult->firstname.' ('.$toResult->email.'), '.$toResult->desig.'</p>';
        
        return $html;
    }

    public function getTransferDetailsHtml() {
        $id = $_POST['emp_id'];
        $toid = $_POST['assign'];
        $own_deals = $followed_deals = $tasks = $organization = $contacts_cnt = 0;
        //---deal teamleader
        $this->db->where('teamleader',$id);
        $teamleader = $this->db->select('id')->get(db_prefix() . 'projects')->result();
        $returnarr['projects']['teamleader'] = array();
        foreach($teamleader as $kt => $vt){
            $own_deals++;
        }
        if($own_deals <= 1) {
            $owndeal = $own_deals.' - Own Deal';
        } else {
            $owndeal = $own_deals.' - Own Deals';
        }

        //---deal members
        $this->db->where('staff_id',$id);
        $project_members = $this->db->select('project_id')->get(db_prefix() . 'project_members')->result();
        $returnarr['projects']['project_members'] = array();
        foreach($project_members as $kt => $vt){
            $followed_deals++;
        }

        if($followed_deals <= 1) {
            $fdeal = $followed_deals.' - Followed Deal';
        } else {
            $fdeal = $followed_deals.' - Followed Deals';
        }

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

        //--- Organization
        $this->db->where('addedfrom',$id);
        $clients = $this->db->select('userid')->get(db_prefix() . 'clients')->result();
        $returnarr['clients']['addedfrom'] = array();
        foreach($clients as $kt => $vt){
            $organization++;
        }

        if($organization <= 1) {
            $organization = $organization.' - Organization';
        } else {
            $organization = $organization.' - Organizations';
        }

        //---Contact Person 
        $this->db->where('addedfrom',$id);
        $contacts = $this->db->select('id')->get(db_prefix() . 'contacts')->result();
        foreach($contacts as $kt => $vt){
            $contacts_cnt++;
        }

        if($contacts_cnt <= 1) {
            $contacts_cnt = $contacts_cnt.' - Contact';
        } else {
            $contacts_cnt = $contacts_cnt.' - Contacts';
        }
// $staffs = get_staff($id);
// pre($staffs);
        $html = '<p style="font-size:15px;"><b>Bellow Datas are Transfered </b></p>
                <p><b>From:</b> '.get_staff_full_name($id).' - <b>To:</b> '.get_staff_full_name($toid).'</p>
                <p>'.$owndeal.'</p>
                <p>'.$fdeal.'</p>
                <p>'.$tasks.'</p>
                <p>'.$organization.'</p>
                <p>'.$contacts_cnt.'</p>';
        
        return $html;
        //echo json_encode($output);
    }

    public function getaddfollowerfields() {
        $data = $this->AccountTransfer_model->get_staffs_whom_follow();
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