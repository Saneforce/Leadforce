<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('gdpr_model');
$this->ci->load->model('callsettings_model');
$allow_to_call = $this->ci->callsettings_model->accessToCall();

$lockAfterConvert      = get_option('lead_lock_after_convert_to_customer');
$has_permission_delete = has_permission('leads', '', 'delete');
$custom_fields         = get_table_custom_fields('leads');
$consentLeads          = get_option('gdpr_enable_consent_for_leads');
$statuses              = $this->ci->leads_model->get_status();
$aColumns = [];
$targets_list_column_order = (array)json_decode(get_option('leads_list_column'));
$before_targets_list_column_order = $targets_list_column_order;
//pre($targets_list_column_order);
$targets_list_column_order = array_keys($targets_list_column_order);
//echo '<pre>';print_r($aColumns);exit;
$aColumns = [
    db_prefix() . 'leads.id as id',
    db_prefix() . 'leads.client_id as client_id',
];
$aColumns = array_merge($aColumns,$targets_list_column_order);
//pr($aColumns);

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'leads ';

$where        = [];
 $where = ["AND ".db_prefix()."target.target_status = 'activity'"];
 $arrval = array();
//where = array('target_status'=>'deal');
if(!empty($aColumns)){
    $i=0;
	foreach($aColumns as $aColumn1){
		if($aColumn1 == 'name'){
			$arrval[$i] =  db_prefix()."leads.name as name";
		}
		elseif($aColumn1 == 'company'){
			$arrval[$i] =  db_prefix()."leads.company as company";
        }
        elseif($aColumn1 == 'email'){
			$arrval[$i] =  db_prefix()."leads.email as email";
        }
        elseif($aColumn1 == 'phonenumber'){
			$arrval[$i] =  db_prefix()."leads.phonenumber as phonenumber";
        }
        elseif($aColumn1 == 'state'){
			$arrval[$i] =  db_prefix()."leads.state as state";
        }
        elseif($aColumn1 == 'city'){
			$arrval[$i] =  db_prefix()."leads.city as city";
        }
        elseif($aColumn1 == 'country'){
			$arrval[$i] =  "(SELECT tblcountries.short_name FROM tblcountries WHERE country_id = tblleads.country) as country";
        }
        elseif($aColumn1 == 'assigned_firstname'){
			$arrval[$i] =  'firstname as assigned_firstname';
        }
        elseif($aColumn1 == 'source_name'){
			$arrval[$i] =  'tblleads_sources.name as source_name';
        }
        elseif($aColumn1 == 'dateadded'){
			$arrval[$i] =  db_prefix()."leads.dateadded as dateadded";
		} else {
            $arrval[$i] = $aColumn1;
        }
		$i++;
	}
}
$aColumns = $arrval;
//pre($aColumns);
// $aColumns = [
//     '1',
//     db_prefix() . 'leads.id as id',
//     db_prefix() . 'leads.name as name',
//     ];
// if (is_gdpr() && $consentLeads == '1') {
//     $aColumns[] = '1';
// }
// $aColumns = array_merge($aColumns, ['company',
//     db_prefix() . 'leads.email as email',
//     db_prefix() . 'leads.phonenumber as phonenumber',
//     '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'leads.id and rel_type="lead" ORDER by tag_order ASC LIMIT 1) as tags',
//     'firstname as assigned_firstname',
//     // db_prefix() . 'leads_status.name as status_name',
//     db_prefix() . 'leads_sources.name as source_name',
//     'dateadded',
// ]);

// $sIndexColumn = 'id';
// $sTable       = db_prefix() . 'leads';

$join = [
    'LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'leads.assigned',
    'LEFT JOIN ' . db_prefix() . 'leads_sources ON ' . db_prefix() . 'leads_sources.id = ' . db_prefix() . 'leads.source'
];
$k = 0;
$locationCustomFields= [];
foreach($arrval as $key => $val) {
    foreach ($custom_fields as $key => $field) {
        if($val == $field['slug']) { 
            unset($aColumns[$k]);
            $selectAs = 'cvalue_' .$field['slug'];
            if($field['type'] =='location'){
                array_push($locationCustomFields, 'cvalue_' .$field['slug']);
            }
            array_push($customFieldsColumns, $selectAs);
            array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
            array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'leads.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
        }
    }
    $k++;
}


// foreach ($custom_fields as $key => $field) {
//     $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
//     array_push($customFieldsColumns, $selectAs);
//     array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
//     array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'leads.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
// }

$where  = [];
$filter = false;

if ($this->ci->input->post('custom_view')) {
    $filter = $this->ci->input->post('custom_view');
    if ($filter == 'lost') {
        array_push($where, 'AND lost = 1');
    } elseif ($filter == 'junk') {
        array_push($where, 'AND junk = 1');
    } elseif ($filter == 'not_assigned') {
        array_push($where, 'AND assigned = 0');
    } elseif ($filter == 'contacted_today') {
        array_push($where, 'AND lastcontact LIKE "' . date('Y-m-d') . '%"');
    } elseif ($filter == 'created_today') {
        array_push($where, 'AND dateadded LIKE "' . date('Y-m-d') . '%"');
    } elseif ($filter == 'webform') {
        array_push($where, 'AND from_form_id > 0');
    } elseif (startsWith($filter, 'consent_')) {
        array_push($where, 'AND ' . db_prefix() . 'leads.id IN (SELECT lead_id FROM ' . db_prefix() . 'consents WHERE purpose_id=' . $this->ci->db->escape_str(strafter($filter, 'consent_')) . ' and action="opt-in" AND date IN (SELECT MAX(date) FROM ' . db_prefix() . 'consents WHERE purpose_id=' . $this->ci->db->escape_str(strafter($filter, 'consent_')) . ' AND lead_id=' . db_prefix() . 'leads.id))');
    }
}

// if (!$filter || ($filter && $filter != 'lost' && $filter != 'junk')) {
//     array_push($where, 'AND lost = 0 AND junk = 0');
// }

if (has_permission('leads', '', 'view') && $this->ci->input->post('assigned')) {
    array_push($where, 'AND assigned =' . $this->ci->db->escape_str($this->ci->input->post('assigned')));
} else {
    // if (!has_permission('leads', '', 'view')) {
    //     array_push($where, 'AND (assigned =' . get_staff_user_id() . ' OR addedfrom = ' . get_staff_user_id() . ' OR is_public = 1)');
    // }
    $my_staffids = $this->ci->staff_model->get_my_staffids();
    
    if($my_staffids){
        array_push($where, 'AND (assigned IN (' . implode(',',$my_staffids) . ') OR assigned = ' . get_staff_user_id() . ' OR is_public = 1)');
    }
}

// if ($this->ci->input->post('status')
//     && count($this->ci->input->post('status')) > 0
//     && ($filter != 'lost' && $filter != 'junk')) {
//     array_push($where, 'AND status IN (' . implode(',', $this->ci->db->escape_str($this->ci->input->post('status'))) . ')');
// }

if ($this->ci->input->post('source')) {
    array_push($where, 'AND source =' . $this->ci->db->escape_str($this->ci->input->post('source')));
}

if ($this->ci->input->post('client_id')) {
    array_push($where, 'AND client_id =' . $this->ci->input->post('client_id'));
}


if ($this->ci->input->post('contact_id')) {
    array_push($where, 'AND '.db_prefix().'leads.id IN ( Select lead_id from '.db_prefix().'lead_contacts Where contacts_id='.$this->ci->input->post('contact_id').')');
}


array_push($where, 'AND deleted_status = 0');

$aColumns = hooks()->apply_filters('leads_table_sql_columns', $aColumns);

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$additionalColumns = hooks()->apply_filters('leads_table_additional_columns_sql', [
    'junk',
    'lost',
    'assigned',
    'lastname as assigned_lastname',
    db_prefix() . 'leads.addedfrom as addedfrom',
    '(SELECT count(leadid) FROM ' . db_prefix() . 'clients WHERE ' . db_prefix() . 'clients.leadid=' . db_prefix() . 'leads.id) as is_converted',
    'zip',
    'from_form_id'
]);
$result = data_tables_init(array_values($aColumns), $sIndexColumn, $sTable, $join, $where, $additionalColumns);

$output  = $result['output'];
$rResult = $result['rResult'];
//pre($rResult);
foreach ($rResult as $aRow) {
    $row = [];
    $client_details =false;

    if($aRow['client_id'] >0){
        $this->ci->db->where('userid',$aRow['client_id']);
        $client_details =$this->ci->db->get(db_prefix().'clients')->row();
    }
    $person_details =false;
    $contact =$this->ci->leads_model->get_lead_contact($aRow['id']);
    if($contact){
        $this->ci->db->where('id',$contact->contacts_id);
        $person_details =$this->ci->db->get(db_prefix().'contacts')->row();
    }
    $checkbox = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

    $hrefAttr = 'href="' . admin_url('leads/lead/' . $aRow['id']) . '"';
    // $row[]    = '<a ' . $hrefAttr . '>' . $aRow['id'] . '</a>';

    $nameRow = '<a ' . $hrefAttr . '>' . $aRow['name'] . '</a>';

    $nameRow .= '<div class="row-options">';
    $nameRow .= '<a ' . $hrefAttr . '>' . _l('view') . '</a>';

    $locked = false;

    if ($aRow['is_converted'] > 0) {
        $locked = ((!is_admin() && $lockAfterConvert == 1) ? true : false);
    }

    if (!$locked) {
        $nameRow .= ' | <a href="' . admin_url('leads/lead/' . $aRow['id'] . '?edit=true') . '" >' . _l('edit') . '</a>';
    }

    if ($aRow['addedfrom'] == get_staff_user_id() || $has_permission_delete) {
        $nameRow .= ' | <a href="' . admin_url('leads/delete/' . $aRow['id']) . '" class="_delete text-danger">' . _l('delete') . '</a>';
    }
    $nameRow .= '</div>';


    $row['name'] = $nameRow;

    if (is_gdpr() && $consentLeads == '1') {
        $consentHTML = '<p class="bold"><a href="#" onclick="view_lead_consent(' . $aRow['id'] . '); return false;">' . _l('view_consent') . '</a></p>';
        $consents    = $this->ci->gdpr_model->get_consent_purposes($aRow['id'], 'lead');

        foreach ($consents as $consent) {
            $consentHTML .= '<p style="margin-bottom:0px;">' . $consent['name'] . (!empty($consent['consent_given']) ? '<i class="fa fa-check text-success pull-right"></i>' : '<i class="fa fa-remove text-danger pull-right"></i>') . '</p>';
        }
        $row[] = $consentHTML;
    }
    if (isset($aRow['company'])) {
        if($client_details){
            $row['company'] = $client_details->company;
        }else{
            $row['company'] = $aRow['company'];
        }
        
    }
    if (isset($aRow['email'])) {
        if($person_details){
            $aRow['email'] = $person_details->email;
        }

        $row['email'] = ($aRow['email'] != '' ? '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>' : '');
    }
    if (isset($aRow['phonenumber'])) {
        if($person_details){
            $aRow['phonenumber'] =$person_details->phonenumber;
        }
        $row['phonenumber'] = ($aRow['phonenumber'] != '' ? '<a href="tel:' . $aRow['phonenumber'] . '">' . $aRow['phonenumber'] . '</a>' : '');
        
        if($person_details){
            if($person_details->phonenumber && $allow_to_call == 1) {
                $calling_code =$this->ci->callsettings_model->getCallingCode($person_details->phone_country_code);
                $contact = '<div><a href="#" onclick="callfromdeal('.$person_details->id.','.$aRow['id'].','.$person_details->phonenumber.',\'lead\',\''.$calling_code.'\');" title="Call Now"><img src="'.APP_BASE_URL.'/assets/images/call.png" style="width:25px;"></a></div>';
                $row['phonenumber'] .=$contact;
            }
        }

        

    }
    //$row[] .= render_tags($aRow['tags']);
    

    $assignedOutput = '';
    if ($aRow['assigned'] != 0) {
        $full_name = $aRow['assigned_firstname'] . ' ' . $aRow['assigned_lastname'];

        $assignedOutput = '<a data-toggle="tooltip" data-title="' . $full_name . '" href="' . admin_url('profile/' . $aRow['assigned']) . '">' . staff_profile_image($aRow['assigned'], [
            'staff-profile-image-small',
            ]) . '</a>';

        // For exporting
        $assignedOutput .= '<span class="hide">' . $full_name . '</span>';
    }

    $row['assigned_firstname'] = $assignedOutput;

    // if ($aRow['status_name'] == null) {
    //     if ($aRow['lost'] == 1) {
    //         $outputStatus = '<span class="label label-danger inline-block">' . _l('lead_lost') . '</span>';
    //     } elseif ($aRow['junk'] == 1) {
    //         $outputStatus = '<span class="label label-warning inline-block">' . _l('lead_junk') . '</span>';
    //     }
    // } else {
    //     $outputStatus = '<span class="inline-block lead-status-'.$aRow['status'].' label label-' . (empty($aRow['color']) ? 'default': '') . '" style="color:' . $aRow['color'] . ';border:1px solid ' . $aRow['color'] . '">' . $aRow['status_name'];
    //     if (!$locked) {
    //         $outputStatus .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
    //         $outputStatus .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableLeadsStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    //         $outputStatus .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
    //         $outputStatus .= '</a>';

    //         $outputStatus .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableLeadsStatus-' . $aRow['id'] . '">';
    //         foreach ($statuses as $leadChangeStatus) {
    //             if ($aRow['status'] != $leadChangeStatus['id']) {
    //                 $outputStatus .= '<li>
    //               <a href="#" onclick="lead_mark_as(' . $leadChangeStatus['id'] . ',' . $aRow['id'] . '); return false;">
    //                  ' . $leadChangeStatus['name'] . '
    //               </a>
    //            </li>';
    //             }
    //         }
    //         $outputStatus .= '</ul>';
    //         $outputStatus .= '</div>';
    //     }
    //     $outputStatus .= '</span>';
    // }

    //$row[] = $outputStatus;
    if (isset($aRow['state'])) {
        if($client_details){
            $row['state'] = $client_details->state;
        }else{
            $row['state'] = $aRow['state'];
        }
    }
    if (isset($aRow['city'])) {
        if($client_details){
            $row['city'] = $client_details->city;
        }else{
            $row['city'] = $aRow['city'];
        }
    }
    if (isset($aRow['country'])) {
        if($client_details){
            $row['country'] = get_country($client_details->country)->short_name;
        }else{
            $row['country'] = $aRow['country'];
        }
    }
    if (isset($aRow['source_name'])) {
        $row['source_name'] = $aRow['source_name'];
    }
    //$row[] = ($aRow['lastcontact'] == '0000-00-00 00:00:00' || !is_date($aRow['lastcontact']) ? '' : '<span data-toggle="tooltip" data-title="' . _dt($aRow['lastcontact']) . '" class="text-has-action is-date">' . time_ago($aRow['lastcontact']) . '</span>');

    $row['dateadded'] = '<span data-toggle="tooltip" data-title="' . _dt($aRow['dateadded']) . '" class="text-has-action is-date">' . time_ago($aRow['dateadded']) . '</span>';

    // Custom fields add values
    // foreach ($customFieldsColumns as $customFieldColumn) {
    //     $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    // }
    foreach ($customFieldsColumns as $customFieldColumn) {
        if(in_array($customFieldColumn,$locationCustomFields)){
            $row[str_replace("cvalue_","",$customFieldColumn)] =  empty($aRow[$customFieldColumn])?'':custom_field_location_icon_link($aRow[$customFieldColumn]);
        }else{
            $row[str_replace("cvalue_","",$customFieldColumn)] =  empty($aRow[$customFieldColumn])?'':$aRow[$customFieldColumn];
        }
    }
    $row_new = [];
    $i2 = 0;
    //pr($row);
    //pre($before_targets_list_column_order);
    foreach($before_targets_list_column_order as $ckey=>$cval){
		if($i2==0){
			 $row_new[] = $checkbox;
		}
		$i2++;
		$row_new[] =$row[$ckey];
    }

    $row_new['DT_RowId'] = 'lead_' . $aRow['id'];

    if ($aRow['assigned'] == get_staff_user_id()) {
        $row_new['DT_RowClass'] = 'alert-info';
    }

    if (isset($row['DT_RowClass'])) {
        $row_new['DT_RowClass'] .= ' has-row-options';
    } else {
        $row_new['DT_RowClass'] = 'has-row-options';
    }

    $row_new = hooks()->apply_filters('leads_table_row_data', $row_new, $aRow);

    $output['aaData'][] = $row_new;
}
