<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('gdpr_model');
$this->ci->load->model('Clients_model');

$clist = $this->ci->Clients_model->get_clients_list();
$consentContacts = get_option('gdpr_enable_consent_for_contacts');


$aColumns_temp = [
      'firstname'=>'firstname',
      'email'=>'email',
     'company'=> 'company',
      'userids'=>db_prefix() . 'contacts.userids as userids',
     'phonenumber'=> db_prefix() . 'contacts.phonenumber as phonenumber',
     'title'=> 'title',
      'last_login'=>'last_login',
     'active'=>  db_prefix() . 'contacts.active as active',
      ];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'contacts ';
$mandatory_fields = '';
$fields1 = get_option('deal_mandatory');
if(!empty($fields1) && $fields1 != 'null'){
	$mandatory_fields = json_decode($fields1);
}
if(!empty($mandatory_fields) && in_array("clientid", $mandatory_fields)){
	$join         = ['LEFT JOIN ' . db_prefix() . 'clients ON '  . db_prefix() . 'contacts.userid='. db_prefix() . 'clients.userid'];
}else{
	$join         = ['LEFT JOIN ' . db_prefix() . 'clients ON '  . db_prefix() . 'contacts.id!='."''"];
}


$custom_fields = get_table_custom_fields('contacts');
$cus = $customFieldsColumns = [];
foreach ($custom_fields as $key => $field) {
    $selectAs = 'cvalue_' .$field['slug'];
    array_push($customFieldsColumns, $selectAs);
    $cus[$field['slug']] =  'ctable_' . $key . '.value as ' . $selectAs;
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'contacts.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}
$contacts_list_column_order = (array)json_decode(get_option('contacts_list_column_order'));
$aColumns = array();
$aColumns_temp = array_merge($aColumns_temp,$cus);
$idkey = 0;
$req_fields = array_column($custom_fields, 'slug'); 
$req_cnt = count($req_fields);
$req_fields[$req_cnt + 1] = 'firstname';
$req_fields[$req_cnt + 2] = 'lastname';
$req_fields[$req_cnt + 3] = 'email';
$req_fields[$req_cnt + 4] = 'company';
$req_fields[$req_cnt + 5] = 'userids';
$req_fields[$req_cnt + 6] = 'phonenumber';
$req_fields[$req_cnt + 7] = 'title';
$req_fields[$req_cnt + 8] = 'last_login';
$req_fields[$req_cnt + 9] = 'active';
foreach($contacts_list_column_order as $ckey=>$cval){
    if($ckey == 'id') {
        $idkey = 1;
        $aColumns[] = db_prefix() . 'contacts.id as id';
    }
         if($ckey == 'company') {
            $aColumns[] = '(SELECT tblclients.company FROM tblclients WHERE tblclients.userid = tblcontacts.userid) as company';
         } else {
			if(in_array($ckey,$req_fields)){
                if($ckey =='phonenumber'){
                    $aColumns[] = db_prefix() . 'contacts.phone_country_code as phone_country_code';
                }
				$aColumns[] =$aColumns_temp[$ckey];
			}
         }
}
$where = [];


if ($this->ci->input->post('custom_view')) {
    $filter = $this->ci->input->post('custom_view');
    if (startsWith($filter, 'consent_')) {
        array_push($where, 'AND ' . db_prefix() . 'contacts.id IN (SELECT contact_id FROM ' . db_prefix() . 'consents WHERE purpose_id=' . strafter($filter, 'consent_') . ' and action="opt-in" AND date IN (SELECT MAX(date) FROM ' . db_prefix() . 'consents WHERE purpose_id=' . strafter($filter, 'consent_') . ' AND contact_id=' . db_prefix() . 'contacts.id))');
    }
}
$alphabets = array();
if(!empty($_SESSION['alpha'])){
$alphabets = array_filter($_SESSION['alpha']);
}
$likeqry = '';
$alphaCnt = count($alphabets);
$all = '';
if($alphaCnt > 0) {
    $i = 1;
    foreach ($alphabets as $val) {
        if (($key = array_search('All', $_SESSION['alpha'])) == FALSE) {
            if($i < $alphaCnt)
                $likeqry .= db_prefix()."contacts.firstname LIKE '".$val."%' OR ";
            else
                $likeqry .= db_prefix()."contacts.firstname LIKE '".$val."%'";
            $i++;
        } else {
            $all = 1;
        }
    }
}

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}
$my_staffids = $this->ci->staff_model->get_my_staffids();
$view_ids = $this->ci->staff_model->getFollowersViewList();
if (!is_admin() && empty($where)) {
    if($_GET['contacts'] && $_GET['contacts'] != 'all') {
        array_push($where, '  AND tblcontacts.id = "'.$_GET['contacts'].'" ');
    } else {
        if($my_staffids){
            array_push($where, ' AND ('.db_prefix().'contacts.addedfrom IN (' . implode(',',$my_staffids) . ') OR (' . db_prefix() . 'contacts.userid IN (SELECT ' . db_prefix() . 'projects.clientid FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ')  AND tblprojects.clientid != "")) OR  (' . db_prefix() . 'contacts.userid IN (SELECT ' . db_prefix() . 'projects.clientid FROM ' . db_prefix() . 'projects where ' . db_prefix() . 'projects.teamleader in (' . implode(',',$my_staffids) . ') AND tblprojects.clientid != "" )))');
        }
    }
    
}


array_push($where, '  AND tblcontacts.deleted_status=0 ');

if($likeqry) {
    $likeqry = ' AND ( '.$likeqry.' ) ';
    array_push($where, $likeqry);
}



$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'contacts.id as id', db_prefix() . 'contacts.userid as userid', 'is_primary', '(SELECT count(*) FROM ' . db_prefix() . 'contacts c WHERE c.userid=' . db_prefix() . 'contacts.userid) as total_contacts', db_prefix() . 'clients.registration_confirmed as registration_confirmed','tblclients.addedfrom as addedfrom'],db_prefix() . 'contacts.id');
$allow_to_call = $this->ci->callsettings_model->accessToCall();
$output  = $result['output'];
$rResult = $result['rResult'];
//pre($rResult);
foreach ($rResult as $aRow) {
    $row = $rowtemp = [];

    $rowName = '<img src="' . contact_profile_image_url($aRow['id']) . '" class="client-profile-image-small mright5"><a href="#" onclick="contact(' . $aRow['userid'] . ',' . $aRow['id'] . ');return false;">' . $aRow['firstname'] . '</a>';

    $rowName .= '<div class="row-options">';
//	if(!empty($aRow['company'])){
		$rowName .= '<a href="' . admin_url('clients/view_contact/' . $aRow['id']) . '" >' . _l('view') . '</a>';
  //  }
    if (has_permission('contacts', '', 'edit') || is_customer_admin($aRow['userid'])) {
        $rowName .= ' | <a href="#" onclick="contact(' . $aRow['userid'] . ',' . $aRow['id'] . ');return false;">' . _l('edit') . '</a>';
    }
    if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1' && is_admin()) {
        $rowName .= ' | <a href="' . admin_url('clients/export/' . $aRow['id']) . '">
             ' . _l('dt_button_export') . ' (' . _l('gdpr_short') . ')
          </a>';
    }
   // if ((!empty($my_staffids) && in_array($aRow['addedfrom'],$my_staffids) && !in_array($aRow['addedfrom'],$view_ids)) || is_admin(get_staff_user_id()) || $aRow['addedfrom'] == get_staff_user_id()) {
        if (has_permission('contacts', '', 'delete') || is_customer_admin($aRow['userid'])) {
            //if ($aRow['is_primary'] == 0 || ($aRow['is_primary'] == 1 && $aRow['total_contacts'] == 1)) {
                $rowName .= ' | <a href="' . admin_url('clients/delete_contact/' . $aRow['userid'] . '/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
           // }
        }
   // }

    $rowName .= '</div>';

    $rowtemp['firstname'] = $rowName;

    //$row[] = $aRow['lastname'];

    // if (is_gdpr() && $consentContacts == '1') {
    //     $consentHTML = '<p class="bold"><a href="#" onclick="view_contact_consent(' . $aRow['id'] . '); return false;">' . _l('view_consent') . '</a></p>';
    //     $consents    = $this->ci->gdpr_model->get_consent_purposes($aRow['id'], 'contact');
    //     foreach ($consents as $consent) {
    //         $consentHTML .= '<p style="margin-bottom:0px;">' . $consent['name'] . (!empty($consent['consent_given']) ? '<i class="fa fa-check text-success pull-right"></i>' : '<i class="fa fa-remove text-danger pull-right"></i>') . '</p>';
    //     }
    //     $rowtemp[] = $consentHTML;
    // }

    $rowtemp['email'] = '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>';
    $company = '';
    if (!empty($aRow['company'])) {
        $company .= '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . $aRow['company'] . '</a>';
    } 
    // if (!empty($aRow['userids'])) {
    //     $userids = explode(',', $aRow['userids']);
    //     foreach ($userids as $key => $value) {
    //         if($aRow['userid'] != $value){
    //            $company .= ', <a href="' . admin_url('clients/client/' . $value) . '">' . $clist[$value] . '</a>'; 
    //         }
    //     }
        
    // } 
    $rowtemp['company'] = $company;

    $contact = '';
    if(isset($aRow['phonenumber']) && !empty($aRow['phonenumber']) && $allow_to_call == 1) {
        $calling_code =$this->ci->callsettings_model->getCallingCode($aRow['phone_country_code']);
        $exp_no = explode(',',$aRow['phonenumber']);
        $number = str_replace(' ', '', preg_replace('/[^A-Za-z0-9]/', '', $exp_no[0]));
        $contact .= '<div>'.$number.'</div><a href="#" onclick="callfromperson('.$aRow['id'].','.$number.',\''.$calling_code.'\');" title="Call Now"><img src="'.APP_BASE_URL.'/assets/images/call.png" style="width:25px;"></a>';
    } else {
        $exp_no = explode(',',$aRow['phonenumber']);
        $number = str_replace(' ', '', preg_replace('/[^A-Za-z0-9]/', '', $exp_no[0]));
        $contact .= '<div>'.$number.'</div>';
    }

    $rowtemp['phonenumber'] = $contact;

    $rowtemp['title'] = $aRow['title'];

    $rowtemp['last_login'] = (!empty($aRow['last_login']) ? '<span class="text-has-action is-date" data-toggle="tooltip" data-title="' . _dt($aRow['last_login']) . '">' . time_ago($aRow['last_login']) . '</span>' : '');
    if ((!empty($my_staffids) && in_array($aRow['addedfrom'],$my_staffids) && !in_array($aRow['addedfrom'],$view_ids)) || is_admin(get_staff_user_id()) || $aRow['addedfrom'] == get_staff_user_id()) {
        $diabled = "";
    } else {
        $diabled = "disabled";
    }
        $outputActive = '<div class="onoffswitch">
                    <input type="checkbox" data-switch-url="' . admin_url() . 'clients/change_contact_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '"' . ($aRow['active'] == 1 ? ' checked': '') . ' '.$diabled.'>
                    <label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
                </div>';
        // For exporting
        $outputActive .= '<span class="hide">' . ($aRow['active'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
    $rowtemp['active'] = $outputActive;
    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $rowtemp[str_replace("cvalue_","",$customFieldColumn)] =  empty($aRow[$customFieldColumn])?'':$aRow[$customFieldColumn];
    }
//pre($customFieldsColumns);
    foreach($contacts_list_column_order as $ckey=>$cval){
        //if(isset($row_temp[$ckey])){
            $row[] =$rowtemp[$ckey];
        //}
    }

    $row['DT_RowClass'] = 'has-row-options';

    // if ($aRow['registration_confirmed'] == 0) {
    //     $row['DT_RowClass'] .= ' alert-info requires-confirmation';
    //     $row['Data_Title']  = _l('customer_requires_registration_confirmation');
    //     $row['Data_Toggle'] = 'tooltip';
    // }
    //pre($row);
    $output['aaData'][] = $row;
}
