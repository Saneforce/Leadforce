<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AssignFollowers extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AssignFollowers_model');
    }
/**
 * DealLossReasons List
**/
    public function index()
    {
    	if (!has_permission('AssignFollowers', '', 'view')) {
            access_denied('AssignFollowers');
        }
        
        if ($this->input->post()) {
            if($_POST['assign'] == 0) {
                $this->db->where('emp_id', $_POST['emp_id']);
                $this->db->delete(db_prefix() . 'followers_permission');

                $this->db->where('emp_id', $_POST['emp_id']);
                $this->db->delete(db_prefix() . 'followers');

                set_alert('warning', _l('followers_removed'));
                redirect(admin_url('AssignFollowers')); 
            } else {
                $i=0;
                $fpermission = array();
                $fpermission['emp_id'] = $_POST['emp_id'];
                $fpermission['p_type'] = $_POST['assign'];
                $fp = $this->AssignFollowers_model->addFollowersPermission($fpermission);
                if($fp) {
                    if($_POST['assign'] == 1) {
                        $this->db->where('emp_id', $_POST['emp_id']);
                        $this->db->delete(db_prefix() . 'followers');
                        foreach($_POST['employee'] as $val) {
                            $addfollowers = array();
                            if($val != '') {
                                $addfollowers['emp_id'] = $_POST['emp_id'];
                                $addfollowers['follower_id'] = $val;
                                $addfollowers['permission'] = $_POST['permission'][$i];
                                $this->AssignFollowers_model->addFollowers($addfollowers);
                            }
                            $i++;
                        }
                    } else {
                        $this->db->where('emp_id', $_POST['emp_id']);
                        $this->db->delete(db_prefix() . 'followers');
                    }
                    set_alert('success', _l('followers_assigned'));
                    redirect(admin_url('AssignFollowers'));            
                }
            }
        }

//        pre($data['files']);\
        $data['employees']    = $this->AssignFollowers_model->get_staffs_whom_follow();
        $data['title']     = _l('AssignFollowers');
        $data['bodyclass'] = 'dynamic-create-groups';
        $this->load->view('admin/clients/assign_followers', $data);
    }

    public function getfollowerdetails() {
        //pre($_POST);
        $output = array();
        $check_permission = $this->AssignFollowers_model->check_followers_permission($_POST['emp_id']);
        if(isset($check_permission)) {
            if($check_permission->p_type == 1) {
                $check = $this->AssignFollowers_model->check_followers($_POST['emp_id']);
                $data = $this->AssignFollowers_model->get_staffs_whom_follow();
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
            }  else {
                $output['cnt'] = '';
                $output['html'] = '';
                $output['option'] = $check_permission->p_type;
            }
        } else {
            $output['cnt'] = '';
            $output['html'] = '';
            $output['option'] = '';
        }
        echo json_encode($output);            
        exit();
    }

    public function geteditfollowerfields() {
        $output = array();
        $data = $this->AssignFollowers_model->get_staffs_whom_follow();
        if($_POST['assign'] == 1) {
            $check_permission = $this->AssignFollowers_model->check_followers_permission($_POST['emp_id']);
            //pre($check_permission);
            if(isset($check_permission)) {
                if($check_permission->p_type == 1) {
                    $check = $this->AssignFollowers_model->check_followers($_POST['emp_id']);
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

    public function getaddfollowerfields() {
        $data = $this->AssignFollowers_model->get_staffs_whom_follow();
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