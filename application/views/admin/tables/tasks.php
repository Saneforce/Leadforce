<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionEdit   = has_permission('tasks', '', 'edit');
$hasPermissionDelete = has_permission('tasks', '', 'delete');
$tasksPriorities     = get_tasks_priorities();
$CI = &get_instance();

$tasks_list_column_order = (array)json_decode(get_option('tasks_list_column_order')); //pr($tasks_list_column_order);

$custom_fields = get_table_custom_fields('tasks');

//$custom_fields = array_merge($custom_fields, get_table_custom_fields('projects'));

//$custom_fields =array_merge($custom_fields, get_table_custom_fields('contacts'));

//$custom_fields = array_merge($custom_fields,get_table_custom_fields('customers'));
$customFieldsColumns= $locationCustomFields = $cus = [];
//pre($tasks_list_column_order);
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


$result =$CI->tasks_model->get_tasks_list();
$output  = $result['output'];
$rResult = $result['rResult'];
$allow_to_call = $this->ci->callsettings_model->accessToCall();
foreach ($rResult as $aRow) {
    $row = [];

    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

    $row_temp['id'] =  '<a href="' . admin_url('tasks/view/' . $aRow['id']) . '" onclick="init_task_modal(' . $aRow['id'] . '); return false;">' . $aRow['id'] . '</a>';

    $outputName = '';
    if( $aRow['status'] != Tasks_model::STATUS_COMPLETE && ($aRow['current_user_is_assigned'] || has_permission('tasks','','edit') || $aRow['current_user_is_creator'])){
        $outputName .= '<a href="#" class="btn btn-default pull-left mright5 btnunmark" id="task-single-mark-complete-btn" autocomplete="off" data-loading-text="'._l('wait_text').'" onclick="mark_complete('.$aRow['id'].'); return false;" data-toggle="tooltip" title="'._l('task_single_mark_as_complete').'">&nbsp;</a>';
    } else if($aRow['status'] == Tasks_model::STATUS_COMPLETE && ($aRow['current_user_is_assigned'] || has_permission('tasks','','edit') || $aRow['current_user_is_creator'])){
         $outputName .= '<a href="#" class="btn btn-info  pull-left mright5 btnmark" id="task-single-unmark-complete-btn" autocomplete="off" data-loading-text="'._l('wait_text').'" onclick="unmark_complete('.$aRow['id'].'); return false;" data-toggle="tooltip" title="'._l('task_unmark_as_complete').'"><i class="fa fa-check"></i></a>';
    }

	/*
    if ($aRow['not_finished_timer_by_current_staff']) {
        $outputName .= '<span class="pull-left text-danger"><i class="fa fa-clock-o fa-fw"></i></span>';
    }
	*/
    // $outputName .= '<a href="' . admin_url('tasks/view/' . $aRow['id']) . '" class="display-block main-tasks-table-href-name' . (!empty($aRow['rel_id']) ? ' mbot5' : '') . '" onclick="init_task_modal(' . $aRow['id'] . '); return false;">' . $aRow['task_name'] . '</a>';
    $outputName .= '<a href="#" class="display-block main-tasks-table-href-name' . (!empty($aRow['rel_id']) ? ' mbot5' : '') . '" onclick="edit_task(' . $aRow['id'] . '); return false;">' . $aRow['task_name'] . '</a>';
	$row_temp['project_name']  = ' ';
	
	if(isset($aRow['project_name']) && !empty($aRow['project_name'])){
		$link = task_rel_link($aRow['rel_id'], $aRow['rel_type']);
		$row_temp['project_name'] = '<a class="task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . $link . '">' . $aRow['project_name'] . '</a>';
	} else {
        if(isset($aRow['rel_name']) && !empty($aRow['rel_name'])){
            $link = task_rel_link($aRow['rel_id'], $aRow['rel_type']);
            $row_temp['project_name'] = '<a class="task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . $link . '">' . $aRow['rel_name'] . '</a>';
        }
    }

	$row_temp['project_status']  = ' ';
	if(isset($aRow['project_status']) && !empty($aRow['project_status'])){
		$row_temp['project_status'] = '<span style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">'.$aRow['project_status'].'</span>';
	}
    $row_temp['project_pipeline']  = ' ';
    if(isset($aRow['project_pipeline']) && !empty($aRow['project_pipeline'])){
	    $row_temp['project_pipeline'] =$aRow['project_pipeline'];
    }
	$row_temp['company']  = ' ';
	if(isset($aRow['company']) && !empty($aRow['company'])){
		$row_temp['company'] =  '<a class="task-table-related" data-toggle="tooltip" title="' . _l('company') . '" href="' . admin_url("clients/client/".$aRow['userid']) . '">' . $aRow['company'] . '</a>';;
	}
    
    $row_temp['rel_type']  = ' ';
	if(isset($aRow['rel_type']) && !empty($aRow['rel_type'])){
        if($aRow['rel_type'] == 'project') {
            $row_temp['rel_type'] =  'Deal';
        } else {
            $row_temp['rel_type'] =  ucfirst($aRow['rel_type']);
        }
    }
    
	$row_temp['teamleader']  = ' ';
	if(isset($aRow['p_teamleader']) && !empty($aRow['p_teamleader'])){
		$p_teamleader = $CI->staff_model->get($aRow['p_teamleader']);
		$row_temp['teamleader'] =  isset($p_teamleader->firstname)?'<span style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">'.$p_teamleader->firstname.'</span>':' ';
	}
	
	$row_temp['project_contacts']  = ' ';
	if(isset($aRow['project_contacts']) && !empty($aRow['project_contacts'])){
        $lable = $contact = '';
        if(isset($aRow['contact_email']) && !empty($aRow['contact_email'])) {
           // $lable .= 'Email - '.$aRow['contact_email'].' </br> ';
			$lable .= "Email - <a id='email_".$aRow['id']."' href='javascript:void(0)'  class='check_text' onclick='copyToClipboard(this)'>".$aRow['contact_email']."</a> <input type='hidden' id='input_email_".$aRow['id']."' value='".$aRow['contact_email']."'></br> ";
        }
        if(isset($aRow['contact_phone']) && !empty($aRow['contact_phone'])) {
            //$lable .= 'Phone - '.$aRow['contact_phone'];
			 $lable .= "Phone - <a id='phone_".$aRow['id']."' href='javascript:void(0)' class='check_text' onclick='copyToClipboard(this)'>".$aRow['contact_phone']."</a>";
        }
        if($lable == '') {
            $lable = _l('project_contacts');
        }
        
        $contact .= '<a class="task-table-related" data-toggle="tooltip" data-html="true" title="' . $lable . '" href="' . admin_url("clients/view_contact/".$aRow['contacts_id']) . '">' .$aRow['project_contacts']. '</a><input type="hidden" id="input_phone_'.$aRow['id'].'" value="'.$aRow['contact_phone'].'">';
		$contact .= '<div style="display:flex">';
        if(isset($aRow['contact_phone']) && !empty($aRow['contact_phone']) && $allow_to_call == 1 && $aRow['type_id'] == 1) {
            $calling_code =$this->ci->callsettings_model->getCallingCode($aRow['contact_phone_country_code']);
            $contact .= '<div><a href="#" onclick="callfromdeal('.$aRow['contacts_id'].','.$aRow['id'].','.$aRow['contact_phone'].',\'task\',\''.$calling_code.'\');" title="Call Now"><img src="'.APP_BASE_URL.'/assets/images/call.png" style="width:25px;"></a></div>';
        }
        if($aRow['call_id'] && !empty($aRow['recorded'])) {
            $contact .= '<div><a href="#" onclick="playrecord(\''.$aRow['recorded'].'\');" title="Play Now"><img src="'.APP_BASE_URL.'/assets/images/play.png" style="width:25px;"></a></div>';
        }
        if($aRow['call_id']) {
            $contact .= '<div><a href="#" onclick="view_history(' . $aRow['id'] . '); return false" title="History"><img src="'.APP_BASE_URL.'/assets/images/history.png" style="width:25px;"></a></div>';
        }
		$contact .= '</div>';
        $row_temp['project_contacts']  = $contact;
		//$row_temp['project_contacts']  = '<a class="task-table-related" data-toggle="tooltip" data-html="true" title="' . $lable . '" href="' . admin_url("clients/view_contact/".$aRow['contacts_id']) . '">' .$aRow['project_contacts']. '</a><input type="hidden" id="input_phone_'.$aRow['id'].'" value="'.$aRow['contact_phone'].'">';
	}
	
	/*
    if ($aRow['rel_name']) {
        $relName = task_rel_name($aRow['rel_name'], $aRow['rel_id'], $aRow['rel_type']);

        $link = task_rel_link($aRow['rel_id'], $aRow['rel_type']);

        $outputName .= '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . $link . '">' . $relName . '</a>';
    }
	//pre($outputName);
	*/
    if ($aRow['recurring'] == 1) {
        $outputName .= '<br /><span class="label label-primary inline-block mtop4"> ' . _l('recurring_task') . '</span>';
    }

    $outputName .= '<div class="row-options">';

    $class = 'text-success bold';
    $style = '';

    $tooltip = '';
    if ($aRow['billed'] == 1 || !$aRow['is_assigned'] || $aRow['status'] == Tasks_model::STATUS_COMPLETE) {
        $class = 'text-dark disabled';
        $style = 'style="opacity:0.6;cursor: not-allowed;"';
        if ($aRow['status'] == Tasks_model::STATUS_COMPLETE) {
            $tooltip = ' data-toggle="tooltip" data-title="' . format_task_status($aRow['status'], false, true) . '"';
        } elseif ($aRow['billed'] == 1) {
            $tooltip = ' data-toggle="tooltip" data-title="' . _l('task_billed_cant_start_timer') . '"';
        } elseif (!$aRow['is_assigned']) {
            $tooltip = ' data-toggle="tooltip" data-title="' . _l('task_start_timer_only_assignee') . '"';
        }
    }

    // if ($aRow['not_finished_timer_by_current_staff']) {
    //     $outputName .= '<a href="#" class="text-danger tasks-table-stop-timer" onclick="timer_action(this,' . $aRow['id'] . ',' . $aRow['not_finished_timer_by_current_staff'] . '); return false;">' . _l('task_stop_timer') . '</a>';
    // } else {
    //     $outputName .= '<span' . $tooltip . ' ' . $style . '>
    //     <a href="#" class="' . $class . ' tasks-table-start-timer" onclick="timer_action(this,' . $aRow['id'] . '); return false;">' . _l('task_start_timer') . '</a>
    //     </span>';
    // }

    if ($hasPermissionEdit) {
        $outputName .= '<span class="text-dark"></span><a href="#" onclick="edit_task(' . $aRow['id'] . '); return false">' . _l('edit') . '</a>';
    }
    if (($hasPermissionDelete && (!empty($my_staffids) && in_array($aRow['p_teamleader'],$my_staffids) && !in_array($aRow['p_teamleader'],$view_ids))) || is_admin(get_staff_user_id()) || $aRow['p_teamleader'] == get_staff_user_id() || $aRow['is_assigned'] == get_staff_user_id()) {
        $outputName .= '<span class="text-dark"> | </span><a href="' . admin_url('tasks/delete_task/' . $aRow['id']) . '" class="text-danger _delete task-delete">' . _l('delete') . '</a>';
    }
    
    $outputName .= '</div>';
    $row_temp['task_name'] = $outputName;

    $row_temp['description'] = strlen($aRow['description']) > 20 ? '<a href="#" class="display-block main-tasks-table-href-name' . (!empty($aRow['rel_id']) ? ' mbot5' : '') . '" onclick="edit_task(' . $aRow['id'] . '); return false;">' .substr(strip_tags($aRow['description']),0,100)."..."."</a>" : '<a href="#" class="display-block main-tasks-table-href-name' . (!empty($aRow['rel_id']) ? ' mbot5' : '') . '" onclick="edit_task(' . $aRow['id'] . '); return false;">' .$aRow['description'] . '</a>';

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

    $outputStatus .= '<span class="inline-block label" style="color:' . $status['color'] . ';" task-status-table="' . $aRow['status'] . '">';
    $outputStatus .= $status['name'];

    /*if ($canChangeStatus) {
        $outputStatus .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
        $outputStatus .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableTaskStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $outputStatus .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
        $outputStatus .= '</a>';    

        $outputStatus .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTaskStatus-' . $aRow['id'] . '">';
    */
        // foreach ($task_statuses as $taskChangeStatus) {
        //     if ($aRow['status'] != $taskChangeStatus['id']) {
        //         $outputStatus .= '<li>
        //           <a href="#" onclick="task_mark_as(' . $taskChangeStatus['id'] . ',' . $aRow['id'] . '); return false;">
        //              ' . _l('task_mark_as', $taskChangeStatus['name']) . '
        //           </a>
        //        </li>';
        //     }
        // }
    /*    if($aRow['status'] ==5){
            $datas = 1;
           if(_d($aRow['startdate']) == Date('Y-m-d')){
                $datas = 3;
           }
           if(_d($aRow['startdate']) < Date('Y-m-d')){
                $datas = 2;
           }
            $outputStatus .= '<li>
                      <a href="#" onclick="task_mark_as('.$datas.',' . $aRow['id'] . '); return false;">
                         ' . _l('task_mark_as',_l('task_status_'.$datas)) . '
                      </a>
                   </li>';
        }else{
            $datas = 1;
           if(_d($aRow['startdate']) == Date('Y-m-d')){
                $datas = 3;
           }
           if(_d($aRow['startdate']) < Date('Y-m-d')){
                $datas = 2;
           }
            $outputStatus .= '<li>
                      <a href="#" onclick="task_mark_as(5,' . $aRow['id'] . '); return false;">
                         ' . _l('task_mark_as', _l('task_status_5')) . '
                      </a>
                   </li>';
        }
        $outputStatus .= '</ul>';
        $outputStatus .= '</div>';
    }
    */

    $outputStatus .= '</span><style>a.task-table-related {
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}th.sorting {
    white-space: nowrap;
}</style>';

    $row_temp['status']  = $outputStatus;
    if($aRow['tasktype'] == '')
        $row_temp['tasktype']  = 'Call';
    else
        $row_temp['tasktype']  = ($aRow['tasktype']);
    
    //$row_temp['startdate']  = _d($aRow['startdate']);
    $row_temp['startdate']  = '<span style="white-space:nowrap">'.date('d-m-Y H:i', strtotime($aRow['startdate'])).'</span>';
    $row_temp['dateadded']  = '<span style="white-space:nowrap">'.date('d-m-Y H:i', strtotime($aRow['dateadded'])).'</span>';
    $row_temp['datemodified']  = '<span style="white-space:nowrap">'.(($aRow['datemodified'] == NULL)?'':date('d-m-Y H:i', strtotime($aRow['datemodified']))).'</span>';
    $row_temp['datefinished']  = '<span style="white-space:nowrap">'.(($aRow['datefinished'] == NULL)?'':date('d-m-Y H:i', strtotime($aRow['datefinished']))).'</span>';
    

    $row_temp['assignees']  = format_display_members_by_ids_and_names($aRow['is_assigned'], $aRow['assignees']);
    $row_temp['tags']  = '';
    if(isset($aRow['tags'])) {
        $row_temp['tags']  = render_tags($aRow['tags']);
    }
    $row_temp['priority']  = '';
    if(isset($aRow['priority'])) {
        $outputPriority = '<span style="color:' . task_priority_color($aRow['priority']) . ';" class="inline-block">' . task_priority($aRow['priority']);

        if (has_permission('tasks', '', 'edit') && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
            $outputPriority .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
            $outputPriority .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableTaskPriority-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $outputPriority .= '<span data-toggle="tooltip" title="' . _l('task_single_priority') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
            $outputPriority .= '</a>';

            $outputPriority .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTaskPriority-' . $aRow['id'] . '">';
            foreach ($tasksPriorities as $priority) {
                if ($aRow['priority'] != $priority['id']) {
                    $outputPriority .= '<li>
                    <a href="#" onclick="task_change_priority(' . $priority['id'] . ',' . $aRow['id'] . '); return false;">
                        ' . $priority['name'] . '
                    </a>
                </li>';
                }
            }
            $outputPriority .= '</ul>';
            $outputPriority .= '</div>';
        }

        $outputPriority .= '</span>';
        $row_temp['priority']   = $outputPriority;
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
			//if((!empty($need_fields) && in_array($ckey, $need_fields)) || !empty($cus[$ckey])){
				$row[] =$row_temp[$ckey];
			//}
        }
    }
    // Custom fields add values
    
    // // Custom fields add values
    // foreach ($customFieldsColumns as $customFieldColumn) {
    //     $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    // }

    $row['DT_RowClass'] = 'has-row-options';

    if ((!empty($aRow['duedate']) && $aRow['duedate'] < date('Y-m-d')) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
        $row['DT_RowClass'] .= ' text-danger';
    }

    $row = hooks()->apply_filters('tasks_table_row_data', $row, $aRow);
//pre($row);
    $output['aaData'][] = $row;
}
