<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionEdit   = has_permission('projects', '', 'edit');


$hasPermissionDelete = has_permission('projects', '', 'delete');
$hasPermissionCreate = has_permission('projects', '', 'create');

if(isset($_SESSION['approvalList']) && $_SESSION['approvalList'] ==1){
    $approvalList =1;
    $approval_where =' AND ' . db_prefix() . 'projects.approved = 0';
}else{
    $approvalList =0;
    $approval_where =' AND ' . db_prefix() . 'projects.approved = 1';
}
if($approvalList){
    $userAccessApproval =false;
    $approval_flows =$this->ci->workflow_model->getflows('deal_approval',0,['service'=>'approval_level','inactive'=>0]);
    if($approval_flows){
        foreach ($approval_flows as $key => $flow) {
            if($flow->configure){
                $configure =$flow->configure;
                if($configure['approver'] ==get_staff_user_id()){
                    $userAccessApproval =true;
                    break;
                }
            }
        }
    }
}


$aColumns_temp = [
    'id'=>db_prefix() . 'projects.id as id',
    'name'=>'tblprojects.name as name',
    'teamleader_name'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'staff WHERE tblstaff.staffid=' . db_prefix() . 'projects.teamleader) as teamleader_name',
    'contact_name'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'project_contacts JOIN ' . db_prefix() . 'contacts on ' . db_prefix() . 'contacts.id = ' . db_prefix() . 'project_contacts.contacts_id WHERE tblproject_contacts.project_id=' . db_prefix() . 'projects.id AND tblproject_contacts.is_primary = 1) as contact_name',
    'project_cost'=>'project_cost',
    'product_qty'=>'(SELECT sum(quantity) FROM tblproject_products WHERE projectid = ' . db_prefix() . 'projects.id) as product_qty',
    'product_count'=>'(SELECT count(id) FROM tblproject_products WHERE projectid = ' . db_prefix() . 'projects.id) as product_count',
    'product_amt'=>'(SELECT sum(price) FROM tblproject_products WHERE projectid = ' . db_prefix() . 'projects.id) as product_amt',
   'company'=> get_sql_select_client_company(),
    'tags'=>'(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'projects.id and rel_type="project" ORDER by tag_order ASC) as tags',
   'start_date'=> 'start_date',
   'deadline'=> 'deadline',
    'members'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'project_members JOIN ' . db_prefix() . 'staff on ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'project_members.staff_id WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members',
   'status'=> 'tblprojects.status as status',
   'project_status'=> 'tblprojects.stage_of as project_status',
   'pipeline_id'=> 'pipeline_id',
   'contact_email1'=>'(SELECT ' . db_prefix() . 'contacts.email FROM ' . db_prefix() . 'project_contacts JOIN ' . db_prefix() . 'contacts on ' . db_prefix() . 'contacts.id = ' . db_prefix() . 'project_contacts.contacts_id WHERE tblproject_contacts.project_id=' . db_prefix() . 'projects.id AND tblproject_contacts.is_primary = 1 limit 1) as contact_email1',
   'contact_phone1'=>'(SELECT ' . db_prefix() . 'contacts.phonenumber FROM ' . db_prefix() . 'project_contacts JOIN ' . db_prefix() . 'contacts on ' . db_prefix() . 'contacts.id = ' . db_prefix() . 'project_contacts.contacts_id WHERE tblproject_contacts.project_id=' . db_prefix() . 'projects.id AND tblproject_contacts.is_primary = 1 limit 1) as contact_phone1',
   'contact_phone_country_code'=>'(SELECT ' . db_prefix() . 'contacts.phone_country_code FROM ' . db_prefix() . 'project_contacts JOIN ' . db_prefix() . 'contacts on ' . db_prefix() . 'contacts.id = ' . db_prefix() . 'project_contacts.contacts_id WHERE tblproject_contacts.project_id=' . db_prefix() . 'projects.id AND tblproject_contacts.is_primary = 1 limit 1) as contact_phone_country_code',
    'won_date'=>'stage_on as won_date',
    'lost_date'=>'stage_on as lost_date',
    'loss_reason_name'=>db_prefix() . 'deallossreasons.name as loss_reason_name',
    'project_currency'=>'project_currency',
    'project_created'=>'project_created',
    'project_modified'=>'project_modified',
    'modified_by'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'staff WHERE tblstaff.staffid=' . db_prefix() . 'projects.modified_by) as modified_by',
    'created_by'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'staff WHERE tblstaff.staffid=' . db_prefix() . 'projects.created_by) as created_by',
    ];
    //pre($aColumns_temp);

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'projects';


$join = [
    'LEFT JOIN  ' . db_prefix() . 'projects_status ON ' . db_prefix() . 'projects_status.id = ' . db_prefix() . 'projects.status',
    'LEFT JOIN  ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'projects.clientid',
    'LEFT JOIN  ' . db_prefix() . 'deallossreasons ON ' . db_prefix() . 'deallossreasons.id = ' . db_prefix() . 'projects.loss_reason',
];

$where  = [];
$filter = [];


//array_push($where, ' AND ' . db_prefix() . 'projects_status.id = ' . db_prefix() . 'projects.status AND ' . db_prefix() . 'projects_status.status = 0');
if (!has_permission('projects', '', 'view') || $this->ci->input->post('my_projects')) {
    array_push($where, ' AND ' . db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')');
}

if ($this->ci->input->post('approval_projects')) {
    array_push($where, ' AND ' . db_prefix() . 'projects.id NOT IN (SELECT rel_id FROM ' . db_prefix() . 'approval_history WHERE rel_type="projects" AND status =0 AND reopened=0)');
}

if ($this->ci->input->post('rejected_projects')) {
    array_push($where, ' AND ' . db_prefix() . 'projects.id IN (SELECT rel_id FROM ' . db_prefix() . 'approval_history WHERE rel_type="projects" AND status =0  AND reopened=0)');
}

$statusIds = $statusIds1 = [];

// ROle based records
if(isset($_REQUEST['last_order_identifier']) && strpos($_REQUEST['last_order_identifier'], 'contacts_projects') !== false) {
    $exp = explode('contacts_projects_',$_REQUEST['last_order_identifier']);
    foreach ($this->ci->projects_model->get_project_statuses() as $status) {
        array_push($statusIds1, $status['id']);
    }
    array_push($where, ' AND ' . db_prefix() . 'projects.id IN (SELECT project_id FROM '.db_prefix().'project_contacts WHERE contacts_id='.$exp[1].$approval_where.')');
    if (count($statusIds1) > 0) {
        array_push($filter, 'OR '.db_prefix().'projects.status IN (' . implode(', ', $statusIds1) . ')');
        array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
    }
} elseif(isset($_REQUEST['last_order_identifier']) && strpos($_REQUEST['last_order_identifier'], 'products_projects') !== false) {
    $exp = explode('products_projects_',$_REQUEST['last_order_identifier']);
    //pre($exp);
    foreach ($this->ci->projects_model->get_project_statuses() as $status) {
        array_push($statusIds1, $status['id']);
    }
    array_push($where, ' AND ' . db_prefix() . 'projects.id IN (SELECT projectid FROM '.db_prefix().'project_products WHERE productid='.$exp[1].$approval_where.')');
    if (count($statusIds1) > 0) {
        array_push($filter, 'OR tblprojects.status IN (' . implode(', ', $statusIds1) . ')');
        array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
    }
} else {
    if ($clientid != '') {
        array_push($where, ' AND clientid=' . $clientid);
    }
    // $my_staffids = $this->ci->staff_model->get_my_staffids();
    // if($my_staffids){
    //     array_push($where, ' AND (' . db_prefix() . 'projects.id IN (SELECT ' . db_prefix() . 'projects.id FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ')) OR  ' . db_prefix() . 'projects.teamleader in (' . implode(',',$my_staffids) . ') )');
    // }
}

foreach ($this->ci->projects_model->get_project_statuses() as $status) {
    if ($this->ci->input->post('project_status_' . $status['id'])) {
        array_push($statusIds, $status['id']);
    }
}

if (count($statusIds) > 0) {
    array_push($filter, 'OR '.db_prefix().'projects.status IN (' . implode(', ', $statusIds) . ')');
}
if(!empty($_REQUEST['status'])){
	
	array_push($where, "AND ".db_prefix()."projects.status = '".$_REQUEST['status']."' " );
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}
$custom_fields = get_table_custom_fields('projects');
$req_fields = array_column($custom_fields, 'slug'); 
$req_cnt = count($req_fields);
$req_fields[$req_cnt + 1] = 'id';
$req_fields[$req_cnt + 2] = 'name';
$req_fields[$req_cnt + 3] = 'teamleader_name';
$req_fields[$req_cnt + 4] ='contact_name';
$req_fields[$req_cnt + 5] = 'project_cost';
$req_fields[$req_cnt + 6] = 'product_qty';
$req_fields[$req_cnt + 7] = 'product_amt';
$req_fields[$req_cnt + 8] = 'company';
$req_fields[$req_cnt + 9] = 'rel_id';
$req_fields[$req_cnt + 10]= 'start_date';
$req_fields[$req_cnt + 11]= 'deadline';
$req_fields[$req_cnt + 12]= 'contact_email1';
$req_fields[$req_cnt + 13]= 'contact_phone1';
$req_fields[$req_cnt + 14]= 'won_date';
$req_fields[$req_cnt + 15]= 'lost_date';
$req_fields[$req_cnt + 16]= 'loss_reason_name';
$req_fields[$req_cnt + 17]= 'project_currency';
$req_fields[$req_cnt + 18]= 'project_created';
$req_fields[$req_cnt + 19]= 'project_modified';
$req_fields[$req_cnt + 20]= 'modified_by';
$req_fields[$req_cnt + 21]= 'created_by';
$req_fields[$req_cnt + 22]= 'product_count';
$projects_list_column_order = (array)json_decode(get_option('projects_list_column_order')); 
//pre($projects_list_column_order);
$custom_fields = array_merge($custom_fields,get_table_custom_fields('customers'));
$customFieldsColumns = $locationCustomFields = $cus = [];
//pre($custom_fields);
foreach ($custom_fields as $key => $field) {
    $fieldtois= 'clients.userid';
    if($field['fieldto'] =='projects'){
        $fieldtois= 'projects.id';
    }elseif($field['fieldto'] =='contacts'){
        $fieldtois= 'contacts.id';
    }
    if(isset($projects_list_column_order[$field['slug']])){
        $selectAs = 'cvalue_' .$field['slug'];
        if($field['type'] =='location'){
            array_push($locationCustomFields, 'cvalue_' .$field['slug']);
        }
        array_push($customFieldsColumns, $selectAs);
        $cus[$field['slug']] =  'ctable_' . $key . '.value as ' . $selectAs;
        array_push($join, 'LEFT JOIN '.db_prefix().'customfieldsvalues as ctable_' . $key . ' ON '.db_prefix().$fieldtois.' = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
    }
}
$aColumns = array();
$aColumns_temp = array_merge($aColumns_temp,$cus);
// $aColumns[] = db_prefix().'clients.userid as userid';
 //pr($aColumns_temp);


$idkey = 0;
foreach($projects_list_column_order as $ckey=>$cval){
    if($ckey == 'id') {
        $idkey = 1;
       // $aColumns[] = db_prefix() . 'projects.id as id';
    }
         if($ckey == 'pipeline_id') {
            $aColumns[] = '(SELECT name FROM tblpipeline WHERE id = tblprojects.pipeline_id) as pipeline_name';
         } else {
			 if($ckey == 'project_start_date'){
				 $ckey = 'start_date';
			 }
			 if($ckey == 'project_deadline'){
				 $ckey = 'deadline';
			 }
			 if(isset($aColumns_temp[$ckey])){
                if($ckey =='contact_phone1'){
                    $aColumns[] =$aColumns_temp['contact_phone_country_code'];
                }
				$aColumns[] =$aColumns_temp[$ckey];
			 }
         }
      //pr($aColumns);
}
//$aColumns = array_unique($aColumns);
//pre($aColumns);
//echo "<pre>"; print_r($_SESSION); exit;

// for approval
if($approvalList){
    array_push($where, ' AND ' . db_prefix() . 'projects.approved = 0');
}else{
    array_push($where, ' AND ' . db_prefix() . 'projects.approved = 1');
}


$pipeline = $_SESSION['pipelines'];
if (empty($pipeline)) {
    $pipeline = 0;
}else{
    array_push($where, ' AND ' . db_prefix() . 'projects.pipeline_id = '.$pipeline);
}
$gsearch = $_SESSION['gsearch'];

if(!empty($gsearch)){
    array_push($where, ' AND ' . db_prefix() . 'projects.id IN (SELECT id FROM ' . db_prefix() . 'projects WHERE name like "%' . $gsearch . '%")');
}

if($userAccessApproval ==false){
    $my_staffids = $this->ci->staff_model->get_my_staffids();
    if ($_SESSION['member']) {
        $memb = $_SESSION['member'];
        array_push($where, ' AND (' . db_prefix() . 'projects.id IN (SELECT ' . db_prefix() . 'projects.id FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . $memb . ')'.$approval_where.') OR  ' . db_prefix() . 'projects.teamleader in (' . $memb . ') )');
        //array_push($where, ' AND ' . db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . $memb . ')');
        //array_push($where, ' AND ' . db_prefix() . 'projects.teamleader = ' . $memb);
    } else {
        if($my_staffids){
            $lowlevel = $this->ci->staff_model->printHierarchyTree(get_staff_user_id());

            if($lowlevel){
                foreach($lowlevel as $lowstaff){
                    if(!in_array($lowstaff,$my_staffids)){
                        $my_staffids[] =$lowstaff;
                    }
                }
            }
            array_push($where, ' AND (' . db_prefix() . 'projects.id IN (SELECT ' . db_prefix() . 'projects.id FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ')'.$approval_where.') OR  ' . db_prefix() . 'projects.teamleader in (' . implode(',',$my_staffids) . ') )');
        }
    }
}

array_push($where, ' AND ' . db_prefix() . 'projects.deleted_status = 0');

// $custom_fields = get_table_custom_fields('projects');

// foreach ($custom_fields as $key => $field) {
//     $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
//     array_push($customFieldsColumns, $selectAs);
//     array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
//     array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'projects.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
// }


$aColumns = hooks()->apply_filters('projects_table_sql_columns', $aColumns);
array_unshift($aColumns,db_prefix() . 'projects.id as id');
// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}
if($idkey == 0) {
    $idkey = ','.db_prefix() . 'projects.id as id';
} else {
    $idkey = '';
}
//pre($aColumns);exit;
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'clientid',
    '(SELECT GROUP_CONCAT(staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_members WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members_ids'.$idkey,
    'tblprojects.teamleader',
    '(SELECT contacts_id FROM ' . db_prefix() . 'project_contacts WHERE project_id=' . db_prefix() . 'projects.id AND is_primary = 1 limit 1) as primary_id',
    '(select email from tblcontacts where id = (SELECT contacts_id FROM ' . db_prefix() . 'project_contacts WHERE project_id=' . db_prefix() . 'projects.id AND is_primary = 1 limit 1)) as contact_email',
    '(select phonenumber from tblcontacts where id = (SELECT contacts_id FROM ' . db_prefix() . 'project_contacts WHERE project_id=' . db_prefix() . 'projects.id AND is_primary = 1 limit 1)) as contact_phone',
]);

$output  = $result['output'];
$rResult = $result['rResult'];
$view_ids = $this->ci->staff_model->getFollowersViewList();
$allow_to_call = $this->ci->callsettings_model->accessToCall();
if($approvalList){
    $allow_to_call = false;
}
// pre($where);
foreach ($rResult as $aRow) {
    
    $row = [];

    $stage_of = '';
    if($aRow['project_status']) {
        $stage_of = (($aRow['project_status'] == 1)?'WON':'LOSS');
    }
    $row_temp['project_status'] = $stage_of;
    $link = admin_url('projects/view/' . $aRow['id']);
	if ($hasPermissionEdit && $approvalList ==0) {
		$checkbox = "<input type='checkbox' id='check_".$aRow['id']."' class='check_mail' onclick='check_header()' value='".$aRow['id']."'>";
	}

    $name_style ='';
    $rejectedDeal =false;
    if(isset($_SESSION['approvalList']) && $_SESSION['approvalList'] ==1){
        $this->ci->db->where('rel_type','projects');
        $this->ci->db->where('rel_id',$aRow['id']);
        $this->ci->db->where('status',0);
        $this->ci->db->where('reopened',0);
        $rejectedDeal =$this->ci->db->get(db_prefix().'approval_history')->row();
        if($rejectedDeal){
            $name_style ='style="color:red"';
        }
    }

    // $row[] = '<a href="' . $link . '">' . $aRow['id'] . '</a>';
    $row_temp['id']  = '<a href="' . $link . '" '.$name_style.'>' . $aRow['id'] . '</a>';

    

    $name = '<a href="' . $link . '" '.$name_style.'>' . $aRow['name'] . '</a>';

    
    $name .= '<div class="row-options">';

    $name .= '<a href="' . $link . '">' . _l('view') . '</a>';

    if ($hasPermissionCreate && !$clientid) {
        //$name .= ' | <a href="#" onclick="copy_project(' . $aRow['id'] . ');return false;">' . _l('copy_project') . '</a>';
    }

    $this->ci->db->where('id',$aRow['id']);
    $project_row =$this->ci->db->get(db_prefix().'projects')->row();
    $created_by = $project_row->created_by;

    if ($hasPermissionEdit && ($approvalList==0 || ($rejectedDeal && $created_by ==get_staff_user_id()))) {
        $name .= ' | <a href="' . $link . '?group=project_overview">' . _l('edit') . '</a>';
    }
    
    
    if (($approvalList==0 || ($rejectedDeal && $created_by ==get_staff_user_id()))&& (($hasPermissionDelete && (!empty($my_staffids) && in_array($aRow['teamleader'],$my_staffids) && !in_array($aRow['teamleader'],$view_ids))) || is_admin(get_staff_user_id()) || $aRow['teamleader'] == get_staff_user_id())) {
        $name .= ' | <a href="' . admin_url('projects/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }

    $name .= '</div>';

    // $row[] = $name;
   
    $row_temp['name'] = $name;

   $row_temp['project_cost'] = $aRow['project_cost'];
    

    $row_temp['product_qty'] = $aRow['product_qty'];
    $row_temp['product_count'] = $aRow['product_count'];
    if($aRow['product_amt'] > 0)
        $row_temp['product_amt'] = $aRow['product_amt'];
    else
        $row_temp['product_amt'] = '0.00';

    $row_temp['company']  = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';

    $row_temp['tags']  = render_tags($aRow['tags']);

    $row_temp['start_date']   = _d($aRow['start_date']);

    $row_temp['deadline']  = _d($aRow['deadline']);

    $row_temp['loss_reason_name'] =$aRow['loss_reason_name'];
    $row_temp['won_date'] =$row_temp['lost_date'] ='';
    if($aRow['project_status']) {
        if($aRow['project_status'] ==1){
            $row_temp ['won_date'] = _d($aRow['won_date']);
        }else{
            $row_temp['lost_date']   = _d($aRow['lost_date']);
        }
    }
    
    $row_temp['project_created'] =_d($aRow['project_created']);
    $row_temp['project_modified'] =_d($aRow['project_modified']);
    $row_temp['project_currency'] =$aRow['project_currency'];
    $row_temp['modified_by'] =$aRow['modified_by'];
    $row_temp['created_by'] =$aRow['created_by'];

    $row_temp['pipeline_id']  = $aRow['pipeline_name'];
    $row_temp['contact_email1']  = $aRow['contact_email1'];
    $row_temp['contact_phone1']  = $aRow['contact_phone1'];
    $tl = '<a href="' . admin_url('profile/' . $aRow['teamleader']) . '">' . $aRow['teamleader_name'] . '</a>';
    $row_temp['teamleader_name']  = $tl;

    // $pc = '<a href="' . admin_url('clients/view_contact/' . $aRow['primary_id']) . '">' . $aRow['contact_name'] . '</a>';
    // $row_temp['contact_name']  = $pc;

    $row_temp['contact_name']  = ' ';
	if(isset($aRow['contact_name']) && !empty($aRow['contact_name'])){
        $lable = '';
        $contact = '';
        if(isset($aRow['contact_email']) && !empty($aRow['contact_email'])) {
           // $lable .= 'Email - '.$aRow['contact_email'].' </br> ';
			$lable .= "Email - <a id='email_".$aRow['primary_id']."' href='javascript:void(0)'  class='check_text' onclick='copyToClipboard(this)'>".$aRow['contact_email']."</a> <input type='hidden' id='input_email_".$aRow['primary_id']."' value='".$aRow['contact_email']."'></br> ";
        }
        if(isset($aRow['contact_phone']) && !empty($aRow['contact_phone'])) {
            //$lable .= 'Phone - '.$aRow['contact_phone'];
			 $lable .= "Phone - <a id='phone_".$aRow['primary_id']."' href='javascript:void(0)' class='check_text' onclick='copyToClipboard(this)'>".$aRow['contact_phone']."</a>";
        }
        if($lable == '') {
            $lable = _l('contact_name');
        }
        $contact_view_link ='#';
        if($approvalList==0){
            $contact_view_link =admin_url("clients/view_contact/".$aRow['primary_id']);
        }
        $contact .= '<a class="task-table-related" data-toggle="tooltip" data-html="true" title="' . $lable . '" href="' . $contact_view_link . '">' .$aRow['contact_name']. '</a><input type="hidden" id="input_phone_'.$aRow['primary_id'].'" value="'.$aRow['contact_phone'].'">';
        if(isset($aRow['contact_phone']) && !empty($aRow['contact_phone']) && $allow_to_call == 1) {
            $calling_code =$this->ci->callsettings_model->getCallingCode($aRow['contact_phone_country_code']);
            $contact .= '<div><a href="#" onclick="callfromdeal('.$aRow['primary_id'].','.$aRow['id'].','.$aRow['contact_phone'].',\'deal\',\''.$calling_code.'\');" title="Call Now"><img src="'.APP_BASE_URL.'/assets/images/call.png" style="width:25px;"></a></div>';
        }
        $row_temp['contact_name']  = $contact;
	}

    $membersOutput = '';

    $members       = explode(',', $aRow['members']);
    $exportMembers = '';
    foreach ($members as $key => $member) {
        if ($member != '') {
            $members_ids = explode(',', $aRow['members_ids']);
            $member_id   = $members_ids[$key];
            $membersOutput .= '<a href="' . admin_url('profile/' . $member_id) . '">' .
            staff_profile_image($member_id, [
                'staff-profile-image-small mright5',
                ], 'small', [
                'data-toggle' => 'tooltip',
                'data-title'  => $member,
                ]) . '</a>';
            // For exporting
            $exportMembers .= $member . ', ';
        }
    }

    $membersOutput .= '<span class="hide">' . trim($exportMembers, ', ') . '</span>';
    $row_temp['members']   = $membersOutput;

    $status = get_project_status_by_id($aRow['status']);
    $row_temp['status']    = '<span class="label label inline-block project-status-' . $aRow['status'] . '" style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '">' . $status['name'] . '</span>';

    // Custom fields add values
    // foreach ($customFieldsColumns as $customFieldColumn) {
    //     // $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    //    $row_temp[str_replace("cvalue_","",$customFieldColumn)] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    // }
	
	foreach ($customFieldsColumns as $customFieldColumn) {
        if(in_array($customFieldColumn,$locationCustomFields)){
            $row_temp[str_replace("cvalue_","",$customFieldColumn)] =  empty($aRow[$customFieldColumn])?'':custom_field_location_icon_link($aRow[$customFieldColumn]);
        }else{
            $row_temp[str_replace("cvalue_","",$customFieldColumn)] =  empty($aRow[$customFieldColumn])?'':$aRow[$customFieldColumn];
        }
    }
	$i2 = 0;
    foreach($projects_list_column_order as $ckey=>$cval){
		if ($hasPermissionEdit  && $approvalList ==0) {
			if($i2==0){
				 $row[] = $checkbox;
			}
			$i2++;
		}
        //if(isset($row_temp[$ckey])){
			if((!empty($need_fields) && in_array($ckey, $need_fields)) || !empty($cus[$ckey])){
				if($ckey == 'project_start_date'){
					$ckey = 'start_date';
				}
				if($ckey == 'project_deadline'){
					$ckey = 'deadline';
				}
				$row[] =$row_temp[$ckey];
			}
        //}
    }

    $row['DT_RowClass'] = 'has-row-options';

    $row = hooks()->apply_filters('projects_table_row_data', $row, $aRow);
//pre($row);
    $output['aaData'][] = $row;
}
