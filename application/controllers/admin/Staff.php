<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Staff extends AdminController
{
    /* List all staff members */
    public function index()
    {
        if (!has_permission('staff', '', 'view')) {
            access_denied('staff');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('staff');
        }
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
        $data['title']         = _l('staff_members');
        $this->load->view('admin/staff/manage', $data);
    }

    public function imapint() {
        $this->load->library('imap');
        $imapconf = array();
        $imapsettings = $this->db->get(db_prefix() . 'options')->result_array();
        
        foreach($imapsettings as $config) {
            if($config['name'] == 'imap_host')
                $imapconf['host'] = $config['value'];
            if($config['name'] == 'imap_encryption')
                $imapconf['encrypto'] = $config['value'];
            if($config['name'] == 'imap_username')
                $imapconf['username'] = $config['value'];
            if($config['name'] == 'imap_password')
                $imapconf['password'] = $config['value'];
            if($config['name'] == 'imap_port')
                $imapconf['port'] = $config['value'];
        }
        $imapconf['validate'] = true;
        //echo "<pre>"; print_r($imapconf); exit;
        //Initialize the connection:
        $imap = $this->imap->connect($imapconf);
        //echo "<pre>"; print_r($imap); exit;
        //Get the required datas:
        if ($imap) {
            $folders = $this->imap->get_inbox_email();
        } else {
            $mailList['table'] = '<tr><td colspan="4" style="text-align:center;">Cannot Connect IMAP Server. Please Check Your Credentials...</td></tr>';
			$mailList['field'] = '';
            $folders = $mailList;
        }
        //echo "<pre>"; print_r($folders); exit;
        echo json_encode($folders);
       
        exit;

    }
	public function companyimapint() {
        $this->load->library('imap');
        $imapconf = array();
        $imapsettings = $this->db->get(db_prefix() . 'options')->result_array();
        
        foreach($imapsettings as $config) {
            if($config['name'] == 'imap_host')
                $imapconf['host'] = $config['value'];
            if($config['name'] == 'imap_encryption')
                $imapconf['encrypto'] = $config['value'];
            if($config['name'] == 'imap_username')
                $imapconf['username'] = $config['value'];
            if($config['name'] == 'imap_password')
                $imapconf['password'] = $config['value'];
            if($config['name'] == 'imap_port')
                $imapconf['port'] = $config['value'];
        }
        $imapconf['validate'] = true;
        //echo "<pre>"; print_r($imapconf); exit;
        //Initialize the connection:
        $imap = $this->imap->connect($imapconf);
        //echo "<pre>"; print_r($imap); exit;
        //Get the required datas:
        if ($imap) {
            $folders = $this->imap->get_company_inbox_email();
        } else {
            $mailList['table'] = '<tr><td colspan="4" style="text-align:center;">Cannot Connect IMAP Server. Please Check Your Credentials...</td></tr>';
			$mailList['field'] = '';
            $folders = $mailList;
        }
        //echo "<pre>"; print_r($folders); exit;
        echo json_encode($folders);
       
        exit;

    }

    public function getdealsname() {
        $sQuery = "select id, name from tblprojects where id = (select rel_id from tbltasks where source_from = '".$_REQUEST['uid']."')";
        $rResult = $this->db->query($sQuery)->result_array();

        $projectQuery = "select id, name from tblprojects where deleted_status = 0 and status != 0";
        $projectResult = $this->db->query($projectQuery)->result_array();
        
        $project = '<select id="rel_id" name="rel_id" class="ajax-sesarch" data-width="100%" data-live-search="true" data-none-selected-text="'._l('dropdown_non_selected_tex').'">';
                                
        foreach($projectResult as $val) {
            $selected = '';
            if($val['id'] == $rResult[0]['id']) {
                $selected = 'selected';
            }
            $project .= '<option '.$selected.' value="'.$val['id'].'">'.$val['name'].'</option>';
        }
        
        $project .= '</select><input type="hidden" name="uid" value="'.$_REQUEST['uid'].'">';
        $data['company'] = $project;
        echo json_encode($data);
        exit;
    }

    public function getmessage() {
        $this->load->library('imap');
        $imapconf = array();
        $imapsettings = $this->db->get(db_prefix() . 'options')->result_array();
        foreach($imapsettings as $config) {
            if($config['name'] == 'imap_host')
                $imapconf['host'] = $config['value'];
            if($config['name'] == 'imap_encryption')
                $imapconf['encrypto'] = $config['value'];
            if($config['name'] == 'imap_username')
                $imapconf['username'] = $config['value'];
            if($config['name'] == 'imap_password')
                $imapconf['password'] = $config['value'];
            if($config['name'] == 'imap_port')
                $imapconf['port'] = $config['value'];
        }
        $imapconf['validate'] = true;
        //Initialize the connection:
        $this->imap->connect($imapconf);

        $folders = $this->imap->getmessage();
        $this->imap->mark_as_read($_REQUEST['uid']);
        echo json_encode($folders);
        exit;

    }

    public function hierarchy()
    {
        if (!has_permission('staff', '', 'view')) {
            access_denied('staff');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('staff');
        }
        $data['staff_members'] = $this->staff_model->getUserHierarchy();
        $data['title']         = _l('acs_hierarchy');
        $data['controller'] = $this;
        $this->load->view('admin/staff/hierarchy', $data);
    }

    public function parseAndPrintTree($root, $tree) {
        //echo "<pre>"; print_r($root); exit;
        $return = array();
        if(!is_null($tree) && count($tree) >= 0) {
            echo '<ul>';
            foreach($tree as $child => $parent) {
                //echo "<pre>"; print_r($parent); exit;
                if($parent['reporting_to'] == $root) {                    
                    unset($tree[$child]);
                    echo '<li><span class="tf-nc"><b>'.$parent['firstname'].' '.$parent['lastname'].'</b><p style="text-align:center">('.$parent['desig'].')</p></span>';
                    $this->parseAndPrintTree($child, $tree);
                    echo '</li>';
                }
            }
            echo '</ul>';
        }
    }
    /* Add new staff member or edit existing */
    public function member($id = '')
    {
        if (!has_permission('staff', '', 'view')) {
            access_denied('staff');
        }
        hooks()->do_action('staff_member_edit_view_profile', $id);

        $this->load->model('departments_model');
        if ($this->input->post()) {
            $data = $this->input->post();
            
            // Don't do XSS clean here.
            $data['email_signature'] = $this->input->post('email_signature', false);
            $data['email_signature'] = html_entity_decode($data['email_signature']);

            if ($data['email_signature'] == strip_tags($data['email_signature'])) {
                // not contains HTML, add break lines
                $data['email_signature'] = nl2br_save_html($data['email_signature']);
            }

            $data['password'] = $this->input->post('password', false);

            //password policy validation
            $this->load->model('passwordpolicy_model');
            $policy_validation =$this->passwordpolicy_model->validate_password($data['password']);
            
            if($policy_validation !== true){
                set_alert('danger', $policy_validation);
                redirect(admin_url('staff/member'));
            }
            if($id !==''){
                if(!$this->passwordpolicy_model->check_password_history(true, $id, $data['password'])){
                    set_alert('danger', _l('cannot_use_old_password'));
                    redirect(admin_url('staff/member/'.$id));
                }
            }
            // end password policy validation
            if ($id == '') {
                if (!has_permission('staff', '', 'create')) {
                    access_denied('staff');
                }
                $id = $this->staff_model->add($data);
                if ($id) {
                    handle_staff_profile_image_upload($id);
                    set_alert('success', _l('added_successfully', _l('staff_member')));
                    //redirect(admin_url('staff/member/' . $id));
                    redirect(admin_url('staff'));
                }
            } else {
                if (!has_permission('staff', '', 'edit')) {
                    access_denied('staff');
                }
                $member = $this->staff_model->get($id);
                handle_staff_profile_image_upload($id);
                if(isset($data['action_for']) && $data['action_for'] == 'Deactivate' && $member->action_for != 'Deactivate'){
                    $data['deavite_follow_ids'] = $this->get_deavite_follow_ids($data,$id);
                }
                if(isset($data['action_for']) && $data['action_for'] == 'Active' && isset($data['rollback'])){
                    $data['deavite_follow_ids'] = $member->deavite_follow_ids;
                }

                // if( $member->action_for == 'Deactivate' && $member->deavite_follow_ids != ''){
                //     $data['deavite_follow_ids'] = $member->deavite_follow_ids;
                // }
                // if( isset($data['deavite_re_assign']) && $data['deavite_re_assign'] != '' && $data['deavite_re_assign'] != 0 ){
                //     $data['deavite_follow'] = '';
                // }
                
                $response = $this->staff_model->update($data, $id);
                //pre($data);
                if (is_array($response)) {
                    if (isset($response['cant_remove_main_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_main_admin'));
                    } elseif (isset($response['cant_remove_yourself_from_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
                    }
                } elseif ($response == true) {
                    
                    //---deavite_re_assign
                    if(isset($data['action_for']) && $data['action_for'] == 'Deactivate' && isset($data['deavite_re_assign']) && $data['deavite_re_assign'] != '' && $data['deavite_re_assign'] != 0 ){
                        $this->deavite_re_assign($data,$id);
                        $dataUpdate['deavite_follow_ids'] = '';
                        $this->db->where('staffid', $id);
                        $this->db->update(db_prefix() . 'staff', $dataUpdate);
                    }
                    //---deavite_follow
                    if(isset($data['action_for']) && $data['action_for'] == 'Deactivate' && isset($data['deavite_follow']) && $data['deavite_follow'] != '' && $data['deavite_follow'] != 0 ){
                        $this->deavite_follow($data,$id);
                    }
                    if(isset($data['action_for']) && $data['action_for'] == 'Active' && isset($data['rollback'])) {
                        $this->deavite_follow($data,$id);
                        $dataUpdate['deavite_follow'] = '0';
                        $dataUpdate['deavite_re_assign'] = '0';
                        $this->db->where('staffid', $id);
                        $this->db->update(db_prefix() . 'staff', $dataUpdate);
                    }
                    if(isset($data['action_for']) && $data['action_for'] == 'Active' && !isset($data['rollback'])) {
                        $dataUpdate['deavite_follow'] = '0';
                        $dataUpdate['deavite_re_assign'] = '0';
                        $this->db->where('staffid', $id);
                        $this->db->update(db_prefix() . 'staff', $dataUpdate);
                    }

                    set_alert('success', _l('updated_successfully', _l('staff_member')));
                }
                redirect(admin_url('staff/member/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('staff_member_lowercase'));
        } else {
            $member = $this->staff_model->get($id);
            if (!$member) {
                blank_page('Staff Member Not Found', 'danger');
            }
            $data['member']            = $member;
            $title                     = $member->firstname . ' ' . $member->lastname;
            $data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);

            $ts_filter_data = [];
            if ($this->input->get('filter')) {
                if ($this->input->get('range') != 'period') {
                    $ts_filter_data[$this->input->get('range')] = true;
                } else {
                    $ts_filter_data['period-from'] = $this->input->get('period-from');
                    $ts_filter_data['period-to']   = $this->input->get('period-to');
                }
            } else {
                $ts_filter_data['this_month'] = true;
            }

            $data['logged_time'] = $this->staff_model->get_logged_time_data($id, $ts_filter_data);
            $data['timesheets']  = $data['logged_time']['timesheets'];
        }
        // $this->load->model('currencies_model');
        // $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['roles']         = $this->roles_model->get();
        $data['designations']         = $this->designation_model->get();
        //$data['member_reporting_to']     = $this->staff_model->get('', ['active' => 1,'role !=' => 3,'staffid !=' => $id]);
        $data['member_reporting_to']     = $this->staff_model->get('', ['action_for' => 'Active','staffid !=' => $id]);
        $data['member_action_for']         = array(array('text'=>'Active'),array('text'=>'Blocked'),array('text'=>'Deactivate'),array('text'=>'Vacant'));
        $data['user_notes']    = $this->misc_model->get_notes($id, 'staff');
        $data['departments']   = $this->departments_model->get();
        $data['title']         = $title;
       
        $this->load->view('admin/staff/member', $data);
    }
 
    /* get deavite follow ids */
    public function get_deavite_follow_ids($data,$id)
    {
        $returnarr = array();
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
        return json_encode($returnarr);
    }

    /* deavite re assign */
    public function deavite_re_assign($data,$id)
    {
        $da = json_decode($data['deavite_follow_ids']);
        //---deal teamleader
        if(isset($da->projects->teamleader) && count((array)$da->projects->teamleader) > 0){
            //$this->db->where('teamleader',$id);
            // $this->db->where_in('id',$da->projects->teamleader);
            // $this->db->update(db_prefix() . 'projects', ['teamleader' => $data['deavite_re_assign']]);

            //$this->db->where('teamleader',$data['deavite_follow']);
            $this->db->where_in('id',$da->projects->teamleader);
            $this->db->update(db_prefix() . 'projects', ['teamleader' => $data['deavite_re_assign']]);
        }
        

        //---deal members
        if(isset($da->projects->project_members) && count((array)$da->projects->project_members) > 0){
            //$this->db->where('staff_id',$id);
            // $this->db->where_in('project_id',$da->projects->project_members);
            // $this->db->update(db_prefix() . 'project_members', ['staff_id' => $data['deavite_re_assign']]);

            //$this->db->where('staff_id',$data['deavite_follow']);
            $this->db->where_in('project_id',$da->projects->project_members);
            $this->db->update(db_prefix() . 'project_members', ['staff_id' => $data['deavite_re_assign']]);
        }
        //---task assigned 

        if(isset($da->task->task_assigned) && count((array)$da->task->task_assigned) > 0){
            //$this->db->where('staffid',$id);
            // $this->db->where_in('taskid',$da->task->task_assigned);
            // $this->db->update(db_prefix() . 'task_assigned', ['staffid' => $data['deavite_re_assign']]);

            //$this->db->where('staffid',$data['deavite_follow']);
            $this->db->where_in('taskid',$da->task->task_assigned);
            $this->db->update(db_prefix() . 'task_assigned', ['staffid' => $data['deavite_re_assign']]);
        }
    }
    
    /* deavite follow */
    public function deavite_follow($data,$id)
    {
        $da = json_decode($data['deavite_follow_ids']);
        //echo $id;
        if(empty($data['deavite_follow'])) {
            $data['deavite_follow'] = $id;
        }
        //pre($data);
        //---deal teamleader
        if(isset($da->projects->teamleader) && count((array)$da->projects->teamleader) > 0){
            //$this->db->where('teamleader',$id);
            $this->db->where_in('id',$da->projects->teamleader);
            $this->db->update(db_prefix() . 'projects', ['teamleader' => $data['deavite_follow']]);
        }
        

        //---deal members
        if(isset($da->projects->project_members) && count((array)$da->projects->project_members) > 0){
            //$this->db->where('staff_id',$id);
            $this->db->where_in('project_id',$da->projects->project_members);
            $this->db->update(db_prefix() . 'project_members', ['staff_id' => $data['deavite_follow']]);
        }
        //---task assigned 

        if(isset($da->task->task_assigned) && count((array)$da->task->task_assigned) > 0){
            //$this->db->where('staffid',$id);
            $this->db->where_in('taskid',$da->task->task_assigned);
            $this->db->update(db_prefix() . 'task_assigned', ['staffid' => $data['deavite_follow']]);
        }
    }

    /* Get role permission for specific role id */
    public function role_changed($id)
    {
        if (!has_permission('staff', '', 'view')) {
            ajax_access_denied('staff');
        }

        echo json_encode($this->roles_model->get($id)->permissions);
    }

    public function save_dashboard_widgets_order()
    {
        hooks()->do_action('before_save_dashboard_widgets_order');

        $post_data = $this->input->post();
        foreach ($post_data as $container => $widgets) {
            if ($widgets == 'empty') {
                $post_data[$container] = [];
            }
        }
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_order', serialize($post_data));
    }

    public function save_dashboard_widgets_visibility()
    {
        hooks()->do_action('before_save_dashboard_widgets_visibility');

        $post_data = $this->input->post();
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_visibility', serialize($post_data['widgets']));
    }

    public function reset_dashboard()
    {
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_visibility', null);
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_order', null);

        redirect(admin_url());
    }

    public function save_hidden_table_columns()
    {
        hooks()->do_action('before_save_hidden_table_columns');
        $data   = $this->input->post();
        $id     = $data['id'];
        $hidden = isset($data['hidden']) ? $data['hidden'] : [];
        update_staff_meta(get_staff_user_id(), 'hidden-columns-' . $id, json_encode($hidden));
    }

    public function change_language($lang = '')
    {
        hooks()->do_action('before_staff_change_language', $lang);

        $this->db->where('staffid', get_staff_user_id());
        $this->db->update(db_prefix() . 'staff', ['default_language' => $lang]);
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url());
        }
    }

    public function timesheets()
    {
        $data['view_all'] = false;
        if (is_admin() && $this->input->get('view') == 'all') {
            $data['staff_members_with_timesheets'] = $this->db->query('SELECT DISTINCT staff_id FROM ' . db_prefix() . 'taskstimers WHERE staff_id !=' . get_staff_user_id())->result_array();
            $data['view_all']                      = true;
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('staff_timesheets', ['view_all' => $data['view_all']]);
        }

        if ($data['view_all'] == false) {
            unset($data['view_all']);
        }

        $data['logged_time'] = $this->staff_model->get_logged_time_data(get_staff_user_id());
        $data['title']       = '';
        $this->load->view('admin/staff/timesheets', $data);
    }

    public function delete()
    {
        if (!is_admin() && is_admin($this->input->post('id'))) {
            die('Busted, you can\'t delete administrators');
        }

        if (has_permission('staff', '', 'delete')) {
            $success = $this->staff_model->delete($this->input->post('id'), $this->input->post('transfer_data_to'));
            if ($success) {
                set_alert('success', _l('deleted', _l('staff_member')));
            }
        }

        redirect(admin_url('staff'));
    }

    /* When staff edit his profile */
    public function edit_profile()
    {
        if ($this->input->post()) {
            handle_staff_profile_image_upload();
            $data = $this->input->post();
            // Don't do XSS clean here.
            $data['email_signature'] = $this->input->post('email_signature', false);
            $data['email_signature'] = html_entity_decode($data['email_signature']);

            if ($data['email_signature'] == strip_tags($data['email_signature'])) {
                // not contains HTML, add break lines
                $data['email_signature'] = nl2br_save_html($data['email_signature']);
            }

            $success = $this->staff_model->update_profile($data, get_staff_user_id());
            if ($success) {
                set_alert('success', _l('staff_profile_updated'));
            }
            redirect(admin_url('staff/edit_profile/' . get_staff_user_id()));
        }
        $member = $this->staff_model->get(get_staff_user_id());
        $this->load->model('departments_model');
        $data['member']            = $member;
        $data['departments']       = $this->departments_model->get();
        $data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);
        $data['title']             = $member->firstname . ' ' . $member->lastname;
        $this->load->view('admin/staff/profile', $data);
    }

    /* Remove staff profile image / ajax */
    public function remove_staff_profile_image($id = '')
    {
        $staff_id = get_staff_user_id();
        if (is_numeric($id) && (has_permission('staff', '', 'create') || has_permission('staff', '', 'edot'))) {
            $staff_id = $id;
        }
        hooks()->do_action('before_remove_staff_profile_image');
        $member = $this->staff_model->get($staff_id);
        if (file_exists(get_upload_path_by_type('staff') . $staff_id)) {
            delete_dir(get_upload_path_by_type('staff') . $staff_id);
        }
        $this->db->where('staffid', $staff_id);
        $this->db->update(db_prefix() . 'staff', [
            'profile_image' => null,
        ]);

        if (!is_numeric($id)) {
            redirect(admin_url('staff/edit_profile/' . $staff_id));
        } else {
            redirect(admin_url('staff/member/' . $staff_id));
        }
    }

    /* When staff change his password */
    public function change_password_profile()
    {
        if ($this->input->post()) {
            $response = $this->staff_model->change_password($this->input->post(null, false), get_staff_user_id());
            if (is_array($response) && isset($response[0]['passwordnotmatch'])) {
                set_alert('danger', _l('staff_old_password_incorrect'));
            } else {
                if ($response == true) {
                    set_alert('success', _l('staff_password_changed'));
                } else {
                    set_alert('warning', _l('staff_problem_changing_password'));
                }
            }
            redirect(admin_url('staff/edit_profile'));
        }
    }

    /* View public profile. If id passed view profile by staff id else current user*/
    public function profile($id = '')
    {
        if ($id == '') {
            $id = get_staff_user_id();
        }

        hooks()->do_action('staff_profile_access', $id);

        $data['logged_time'] = $this->staff_model->get_logged_time_data($id);
        $data['staff_p']     = $this->staff_model->get($id);

        if (!$data['staff_p']) {
            blank_page('Staff Member Not Found', 'danger');
        }

        $this->load->model('departments_model');
        $data['staff_departments'] = $this->departments_model->get_staff_departments($data['staff_p']->staffid);
        $data['departments']       = $this->departments_model->get();
        $data['title']             = _l('staff_profile_string') . ' - ' . $data['staff_p']->firstname . ' ' . $data['staff_p']->lastname;
        // notifications
        $total_notifications = total_rows(db_prefix() . 'notifications', [
            'touserid' => get_staff_user_id(),
        ]);
        $data['total_pages'] = ceil($total_notifications / $this->misc_model->get_notifications_limit());
        $this->load->view('admin/staff/myprofile', $data);
    }

    /* Change status to staff active or inactive / ajax */
    public function change_staff_status($id, $status)
    {
        if (has_permission('staff', '', 'edit')) {
            if ($this->input->is_ajax_request()) {
                $this->staff_model->change_staff_status($id, $status);
            }
        }
    }

    /* Logged in staff notifications*/
    public function notifications()
    {
        $this->load->model('misc_model');
        if ($this->input->post()) {
            $page   = $this->input->post('page');
            $offset = ($page * $this->misc_model->get_notifications_limit());
            $this->db->limit($this->misc_model->get_notifications_limit(), $offset);
            $this->db->where('touserid', get_staff_user_id());
            $this->db->where('isread', 0);
            $this->db->where('isread_inline', 0);
            $this->db->order_by('date', 'desc');
            $notifications = $this->db->get(db_prefix() . 'notifications')->result_array();
            $i             = 0;
            foreach ($notifications as $notification) {
                if (($notification['fromcompany'] == null && $notification['fromuserid'] != 0) || ($notification['fromcompany'] == null && $notification['fromclientid'] != 0)) {
                    if ($notification['fromuserid'] != 0) {
                        $notifications[$i]['profile_image'] = '<a href="' . admin_url('staff/profile/' . $notification['fromuserid']) . '">' . staff_profile_image($notification['fromuserid'], [
                        'staff-profile-image-small',
                        'img-circle',
                        'pull-left',
                    ]) . '</a>';
                    } else {
                        $notifications[$i]['profile_image'] = '<a href="' . admin_url('clients/client/' . $notification['fromclientid']) . '">
                    <img class="client-profile-image-small img-circle pull-left" src="' . contact_profile_image_url($notification['fromclientid']) . '"></a>';
                    }
                } else {
                    $notifications[$i]['profile_image'] = '';
                    $notifications[$i]['full_name']     = '';
                }
                $additional_data = '';
                if (!empty($notification['additional_data'])) {
                    $additional_data = unserialize($notification['additional_data']);
                    $x               = 0;
                    foreach ($additional_data as $data) {
                        if (strpos($data, '<lang>') !== false) {
                            $lang = get_string_between($data, '<lang>', '</lang>');
                            $temp = _l($lang);
                            if (strpos($temp, 'project_status_') !== false) {
                                $status = get_project_status_by_id(strafter($temp, 'project_status_'));
                                $temp   = $status['name'];
                            }
                            $additional_data[$x] = $temp;
                        }
                        $x++;
                    }
                }
                $notifications[$i]['description'] = _l($notification['description'], $additional_data);
                $notifications[$i]['date']        = time_ago($notification['date']);
                $notifications[$i]['full_date']   = $notification['date'];
                $i++;
            } //$notifications as $notification
            echo json_encode($notifications);
            die;
        }
    }
}
