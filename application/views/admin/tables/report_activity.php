<?php
defined('BASEPATH') or exit('No direct script access allowed');
$hasPermissionEdit   = has_permission('tasks', '', 'edit');
$hasPermissionDelete = has_permission('tasks', '', 'delete');
$tasks_list_column_order = (array)json_decode(get_option('report_task_list_column_order')); 
$custom_fields = get_table_custom_fields('tasks');
$customFieldsColumns= $locationCustomFields = $cus = [];
foreach ($custom_fields as $key => $field) {
    $fieldtois= 'clients.userid';
    if($field['fieldto'] =='projects'){
        $fieldtois= 'projects.id';
    }elseif($field['fieldto'] =='contacts'){
        $fieldtois= 'contacts.id';
    }
	elseif($field['fieldto'] =='tasks'){
        $fieldtois= 'tasks.id';
    }
    if(isset($tasks_list_column_order[$field['slug']])){
        if($field['type'] =='location'){
            array_push($locationCustomFields, 'cvalue_' .$field['slug']);
        }
        $selectAs = 'cvalue_' .$field['slug'];
        array_push($customFieldsColumns, $selectAs);
    }
}
if(!empty($clientid)){
	$cur_id = '_edit_'.$clientid;
}
if(empty($req_deals)){
	$req_deals = array();
	$req_deals['filters']	=	$this->ci->session->userdata('activity_filters'.$cur_id);
	$req_deals['filters1']	=	$this->ci->session->userdata('activity_filters1'.$cur_id);
	$req_deals['filters2']	=	$this->ci->session->userdata('activity_filters2'.$cur_id);
	$req_deals['filters3']	=	$this->ci->session->userdata('activity_filters3'.$cur_id);
	$req_deals['filters4']	=	$this->ci->session->userdata('activity_filters4'.$cur_id);
}
$req_filters = get_activity_filters($req_deals);
if(empty($clientid)){
	$report_name	=	$this->ci->session->userdata('report_type');
}
$fiter_type = ' where ';
if(!empty($req_filters)){
	$fiter_type = ' and ';
}
if(str_contains($report_name, 'Call Performance')){
	$req_filters .= $fiter_type.db_prefix().'tasks.tasktype = (select id from '.db_prefix().'tasktype where name="Call" and status ="Active" )';
}
if(str_contains($report_name, 'Email Performance')){
	$req_filters .= $fiter_type.db_prefix().'tasks.tasktype = (select id from '.db_prefix().'tasktype where name="E-mail" and status ="Active" )';
}
$result =$this->ci->tasks_model->get_tasks_list(false,$req_filters);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    $row_temp['id'] =   $aRow['id'];
    $outputName = '';
    $outputName .=  $aRow['task_name'];
	$row_temp['project_name']  = ' ';
	
	if(isset($aRow['project_name']) && !empty($aRow['project_name'])){
		$link = task_rel_link($aRow['rel_id'], $aRow['rel_type']);
		$row_temp['project_name'] = $aRow['project_name'];
	} else {
        if(isset($aRow['rel_name']) && !empty($aRow['rel_name'])){
            $link = task_rel_link($aRow['rel_id'], $aRow['rel_type']);
            $row_temp['project_name'] =  $aRow['rel_name'] ;
        }
    }
	$row_temp['project_status']  = ' ';
	if(isset($aRow['project_status']) && !empty($aRow['project_status'])){
		$row_temp['project_status'] = $aRow['project_status'];
	}
    $row_temp['project_pipeline']  = ' ';
    if(isset($aRow['project_pipeline']) && !empty($aRow['project_pipeline'])){
	    $row_temp['project_pipeline'] = $aRow['project_pipeline'];
    }
	$row_temp['company']  = ' ';
	if(isset($aRow['company']) && !empty($aRow['company'])){
		$row_temp['company'] =   $aRow['company'] ;
	}
    $row_temp['rel_type']  = ' ';
	if(isset($aRow['rel_type']) && !empty($aRow['rel_type'])){
        if($aRow['rel_type'] == 'project') {
            $row_temp['rel_type'] =  'Deal';
        }
		else if($aRow['rel_type'] == 'customer') {
            $row_temp['rel_type'] =  _l('client');
        }
		else {
            $row_temp['rel_type'] =  ucfirst($aRow['rel_type']);
        }
    }
	$row_temp['teamleader']  = ' ';
	if(isset($aRow['p_teamleader']) && !empty($aRow['p_teamleader'])){
		$p_teamleader = $this->ci->staff_model->get($aRow['p_teamleader']);
		$row_temp['teamleader'] =  isset($p_teamleader->firstname)?$p_teamleader->firstname:' ';
	}
	
	$row_temp['project_contacts']  = ' ';
	if(isset($aRow['project_contacts']) && !empty($aRow['project_contacts'])){
        $lable = $contact = '';
        
        if($lable == '') {
            $lable = _l('project_contacts');
        }
        
        $contact .= $aRow['project_contacts'];
        $row_temp['project_contacts']  = $contact;
	}
	
    if ($aRow['recurring'] == 1) {
        $outputName .= _l('recurring_task');
    }
    $row_temp['task_name'] = $outputName;

    $row_temp['description'] = strlen($aRow['description']) > 20 ? substr(strip_tags($aRow['description']),0,100)."..." : $aRow['description'].'';

    $canChangeStatus = ($aRow['current_user_is_creator'] != '0' || $aRow['current_user_is_assigned'] || has_permission('tasks', '', 'edit'));
    if($aRow['status'] == 2) {
        $sdate = date('Y-m-d', strtotime($aRow['startdate'])); 
        if(strtotime($sdate) == strtotime(date('Y-m-d'))) {
            $aRow['status'] = 3;
        }
        if(strtotime($sdate) > strtotime(date('Y-m-d'))) {
            $aRow['status'] = 1;
        }
    }
    $sdate = date('Y-m-d', strtotime($aRow['startdate'])); 
    if(strtotime($sdate) == strtotime(date('Y-m-d')) && $aRow['status'] != 5) {
        $aRow['status'] = 3;
    }
    if(strtotime($sdate) > strtotime(date('Y-m-d')) && $aRow['status'] != 5) {
        $aRow['status'] = 1;
    }
    $status          = get_task_status_by_id($aRow['status']);
    $outputStatus    = '';

    $outputStatus .= $status['name'];


    $row_temp['status']  = $outputStatus;
    if($aRow['tasktype'] == '')
        $row_temp['tasktype']  = 'Call';
    else
        $row_temp['tasktype']  = ($aRow['tasktype']);
    $row_temp['startdate']  = date('d-m-Y H:i', strtotime($aRow['startdate']));
    $row_temp['dateadded']  = date('d-m-Y H:i', strtotime($aRow['dateadded']));
    $row_temp['datemodified']  = (($aRow['datemodified'] == NULL)?'':date('d-m-Y H:i', strtotime($aRow['datemodified'])));
    $row_temp['datefinished']  = (($aRow['datefinished'] == NULL)?'':date('d-m-Y H:i', strtotime($aRow['datefinished'])));
    
	$assignees   = explode(',', $aRow['assignees']);
	$outputAssignees = '';
	foreach ($assignees as $key => $assigned) {
        if ($assigned != '') {
            $outputAssignees .= $assigned ;
        }
    }
    //$row_temp['assignees']  = format_display_members_by_ids_and_names($aRow['is_assigned'], $aRow['assignees']);
    $row_temp['assignees']  = $outputAssignees;
    $row_temp['tags']  = '';
    if(isset($aRow['tags'])) {
        $row_temp['tags']  = $aRow['tags'];
    }
    $row_temp['priority']  = '';
    if(isset($aRow['priority'])) {
        if($aRow['priority'] == 1){
			$row_temp['priority'] = _l('task_priority_low');
		}
		if($aRow['priority'] == 2){
			$row_temp['priority'] = _l('task_priority_medium');
		}
		if($aRow['priority'] == 3){
			$row_temp['priority'] = _l('task_priority_high');
		}
		if($aRow['priority'] == 4){
			$row_temp['priority'] = _l('task_priority_urgent');
		}

    }
    foreach ($customFieldsColumns as $customFieldColumn) {
        if(in_array($customFieldColumn,$locationCustomFields)){
            $row_temp[str_replace("cvalue_","",$customFieldColumn)] =  empty($aRow[$customFieldColumn])?'':custom_field_location_icon_link($aRow[$customFieldColumn]);
        }else{
            $row_temp[str_replace("cvalue_","",$customFieldColumn)] =  empty($aRow[$customFieldColumn])?'':$aRow[$customFieldColumn];
        }
    }
	 foreach($tasks_list_column_order as $ckey=>$cval){
        if(isset($row_temp[$ckey])){
				$row[] =$row_temp[$ckey];
        }
    }
    $row['DT_RowClass'] = 'has-row-options';

    $row = hooks()->apply_filters('tasks_table_row_data', $row, $aRow);
    $output['aaData'][] = $row;
}