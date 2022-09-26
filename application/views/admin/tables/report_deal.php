<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionEdit   = has_permission('projects', '', 'edit');
$hasPermissionDelete = has_permission('projects', '', 'delete');
$hasPermissionCreate = has_permission('projects', '', 'create');
$aColumns_temp = [
    'id'=>'p.id as id',
    'name'=>'p.name as name',
    'teamleader_name'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'staff WHERE tblstaff.staffid=p.teamleader) as teamleader_name',
    'contact_name'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'project_contacts JOIN ' . db_prefix() . 'contacts on ' . db_prefix() . 'contacts.id = ' . db_prefix() . 'project_contacts.contacts_id WHERE '.db_prefix() .'project_contacts.project_id=p.id AND '.db_prefix().'project_contacts.is_primary = 1 limit 1) as contact_name',
    'project_cost'=>'project_cost',
    'product_qty'=>'(SELECT sum(quantity) FROM '.db_prefix().'project_products WHERE projectid = p.id) as product_qty',
    'product_count'=>'(SELECT count(quantity) FROM '.db_prefix().'project_products WHERE projectid = p.id) as product_count',
    'product_amt'=>'(SELECT sum(price) FROM '.db_prefix().'project_products WHERE projectid = p.id) as product_amt',
   'company'=> get_sql_select_client_company(),
    'tags'=>'(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = p.id and rel_type="project" ORDER by tag_order ASC) as tags',
   'start_date'=> 'start_date',
   'deadline'=> 'deadline',
    'members'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'project_members JOIN ' . db_prefix() . 'staff on ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'project_members.staff_id WHERE project_id=p.id ORDER BY staff_id) as members',
   'status'=> 'p.status as status',
   'project_status'=> 'p.stage_of as project_status',
   'pipeline_id'=> 'pipeline_id',
   'contact_email1'=>'(SELECT ' . db_prefix() . 'contacts.email FROM ' . db_prefix() . 'project_contacts JOIN ' . db_prefix() . 'contacts on ' . db_prefix() . 'contacts.id = ' . db_prefix() . 'project_contacts.contacts_id WHERE ' . db_prefix() . 'project_contacts.project_id=p.id AND ' . db_prefix() . 'project_contacts.is_primary = 1 limit 1) as contact_email1',
   'contact_phone1'=>'(SELECT ' . db_prefix() . 'contacts.phonenumber FROM ' . db_prefix() . 'project_contacts JOIN ' . db_prefix() . 'contacts on ' . db_prefix() . 'contacts.id = ' . db_prefix() . 'project_contacts.contacts_id WHERE ' . db_prefix() . 'project_contacts.project_id=p.id AND ' . db_prefix() . 'project_contacts.is_primary = 1 limit 1) as contact_phone1',
    'won_date'=>'stage_on as won_date',
    'lost_date'=>'stage_on as lost_date',
    'loss_reason_name'=>'(SELECT ' . db_prefix() . 'deallossreasons.name FROM ' . db_prefix() . 'deallossreasons  WHERE ' . db_prefix() . 'deallossreasons.id=p.loss_reason) as loss_reason_name',
    'project_currency'=>'project_currency',
    'project_created'=>'project_created',
    'project_modified'=>'project_modified',
    'modified_by'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'staff WHERE '.db_prefix() .'staff.staffid=p.modified_by) as modified_by',
    'currency'=>'(SELECT name FROM ' . db_prefix() . 'currencies WHERE '.db_prefix() .'currencies.name=p.project_currency) as currency',
    'created_by'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'staff WHERE '.db_prefix() .'staff.staffid=p.created_by) as created_by',
    ];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'projects as p ';


$join = [
    'LEFT JOIN  ' . db_prefix() . 'projects_status ON ' . db_prefix() . 'projects_status.id = p.status',
    'LEFT JOIN  ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = p.clientid',
];

$where  = [];
$filter = [];
$w_have = '';
$fil_vals = deal_values();
$req_deals =  json_decode($fil_vals, true);
if(!empty($clientid)){
	$cur_id = '_edit_'.$clientid;
}
$req_deals['filters']	=	$this->ci->session->userdata('filters'.$cur_id);
$req_deals['filters1']	=	$this->ci->session->userdata('filters1'.$cur_id);
$req_deals['filters2']	=	$this->ci->session->userdata('filters2'.$cur_id);
$req_deals['filters3']	=	$this->ci->session->userdata('filters3'.$cur_id);
$req_deals['filters4']	=	$this->ci->session->userdata('filters4'.$cur_id);
$req_filters = get_flters($req_deals);
if(!empty($req_filters)){
	array_push($where, $req_filters);
}


$statusIds = $statusIds1 = [];

// ROle based records
if(isset($_REQUEST['last_order_identifier']) && strpos($_REQUEST['last_order_identifier'], 'contacts_projects') !== false) {
    $exp = explode('contacts_projects_',$_REQUEST['last_order_identifier']);
    foreach ($this->ci->projects_model->get_project_statuses() as $status) {
        array_push($statusIds1, $status['id']);
    }
    array_push($where, ' AND p.id IN (SELECT project_id FROM '.db_prefix().'project_contacts WHERE contacts_id='.$exp[1].')');
    if (count($statusIds1) > 0) {
        array_push($filter, 'OR p.status IN (' . implode(', ', $statusIds1) . ')');
        array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
    }
} elseif(isset($_REQUEST['last_order_identifier']) && strpos($_REQUEST['last_order_identifier'], 'products_projects') !== false) {
    $exp = explode('products_projects_',$_REQUEST['last_order_identifier']);
    foreach ($this->ci->projects_model->get_project_statuses() as $status) {
        array_push($statusIds1, $status['id']);
    }
    array_push($where, ' AND p.id IN (SELECT projectid FROM '.db_prefix().'project_products WHERE productid='.$exp[1].')');
    if (count($statusIds1) > 0) {
        array_push($filter, 'OR p.status IN (' . implode(', ', $statusIds1) . ')');
        array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
    }
} 
foreach ($this->ci->projects_model->get_project_statuses() as $status) {
    if ($this->ci->input->post('project_status_' . $status['id'])) {
        array_push($statusIds, $status['id']);
    }
}

if (count($statusIds) > 0) {
    array_push($filter, 'OR p.status IN (' . implode(', ', $statusIds) . ')');
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

$custom_fields = get_table_custom_fields('projects');
$req_fields = array_column($custom_fields, 'slug'); 
$req_cnt = count($req_fields);
$req_fields[$req_cnt + 1] = 'name';
$req_fields[$req_cnt + 2] = 'teamleader_name';
$req_fields[$req_cnt + 3] ='contact_name';
$req_fields[$req_cnt + 4] = 'project_cost';
$req_fields[$req_cnt + 5] = 'product_qty';
$req_fields[$req_cnt + 6] = 'product_amt';
$req_fields[$req_cnt + 7] = 'company';
$req_fields[$req_cnt + 8] = 'rel_id';
$req_fields[$req_cnt + 9]= 'start_date';
$req_fields[$req_cnt + 10]= 'deadline';
$req_fields[$req_cnt + 11]= 'contact_email1';
$req_fields[$req_cnt + 12]= 'contact_phone1';
$report_deal_list_column = (array)json_decode(get_option('report_deal_list_column_order')); 
$custom_fields = array_merge($custom_fields,get_table_custom_fields('customers'));
$customFieldsColumns = $cus = [];
foreach ($custom_fields as $key => $field) {
    $fieldtois= db_prefix().'clients.userid';
    if($field['fieldto'] =='projects'){
        $fieldtois= 'p.id';
    }elseif($field['fieldto'] =='contacts'){
        $fieldtois= db_prefix().'contacts.id';
    }
    if(isset($report_deal_list_column[$field['slug']])){
        $selectAs = 'cvalue_' .$field['slug'];
        array_push($customFieldsColumns, $selectAs);
        $cus[$field['slug']] =  'ctable_' . $key . '.value as ' . $selectAs;
        array_push($join, 'LEFT JOIN '.db_prefix().'customfieldsvalues as ctable_' . $key . ' ON '.$fieldtois.' = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
    }
}
$aColumns = array();
$aColumns_temp = array_merge($aColumns_temp,$cus);

$idkey = 0;
foreach($report_deal_list_column as $ckey=>$cval){
    if($ckey == 'id') {
        $idkey = 1;
        $aColumns[] = db_prefix() . 'projects.id as id';
    }
         if($ckey == 'pipeline_id') {
            $aColumns[] = '(SELECT name FROM '.db_prefix().'pipeline WHERE id = p.pipeline_id) as pipeline_name';
         } else {
			 if($ckey == 'project_start_date'){
				 $ckey = 'start_date';
			 }
			 if($ckey == 'project_deadline'){
				 $ckey = 'deadline';
			 }
			 if(isset($aColumns_temp[$ckey])){
				$aColumns[] =$aColumns_temp[$ckey];
			 }
         }
}
$pipeline = $_SESSION['pipelines'];
if (empty($pipeline)) {
    $pipeline = 0;
}else{
    array_push($where, ' AND p.pipeline_id = '.$pipeline);
}
$gsearch = $_SESSION['gsearch'];

if(!empty($gsearch)){
    array_push($where, ' AND p.id IN (SELECT id FROM ' . db_prefix() . 'projects WHERE name like "%' . $gsearch . '%")');
}
if(empty($_REQUST['call']) || $_REQUEST['call']!='share'){
	$my_staffids = $this->ci->staff_model->get_my_staffids();
	if ($_SESSION['member']) {
		$memb = $_SESSION['member'];
		array_push($where, ' AND (p.id IN (SELECT ' . db_prefix() . 'projects.id FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . $memb . ')) OR  p.teamleader in (' . $memb . ') )');
	} else {
		if($my_staffids){
			array_push($where, ' AND (p.id IN (SELECT ' . db_prefix() . 'projects.id FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ')) OR  p.teamleader in (' . implode(',',$my_staffids) . ') )');
		}
	}
}

array_push($where, ' AND p.deleted_status = 0');

$aColumns = hooks()->apply_filters('projects_table_sql_columns', $aColumns);
array_unshift($aColumns,db_prefix() . 'projects.id as id');
// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}
if($idkey == 0) {
    $idkey = ',p.id as id';
} else {
    $idkey = '';
}
if($aColumns[0] == db_prefix().'projects.id as id'){
	$aColumns[0] = 'p.id as id';
}
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'clientid',
    '(SELECT GROUP_CONCAT(staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_members WHERE project_id=p.id ORDER BY staff_id) as members_ids'.$idkey,
    'p.teamleader',
    '(SELECT contacts_id FROM ' . db_prefix() . 'project_contacts WHERE project_id=p.id AND is_primary = 1 limit 1) as primary_id',
    '(select email from '.db_prefix().'contacts where id = (SELECT contacts_id FROM ' . db_prefix() . 'project_contacts WHERE project_id=p.id AND is_primary = 1 limit 1)) as contact_email',
    '(select phonenumber from '.db_prefix().'contacts where id = (SELECT contacts_id FROM ' . db_prefix() . 'project_contacts WHERE project_id=p.id AND is_primary = 1 limit 1)) as contact_phone',
],$s_group_by);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    
    $row = [];
	$row_temp['won_date']   = $row_temp['lost_date'] = '';
    $stage_of = '';
    if($aRow['project_status']) {
		if($aRow['project_status'] == 1){
			$stage_of = 'WON';
			$row_temp['won_date']   = _d($aRow['won_date']);
		}
		if($aRow['project_status'] == 2){
			$stage_of = 'LOST';
			$row_temp['lost_date']   = _d($aRow['lost_date']);
		}
		if($aRow['project_status'] == 0){
			$stage_of = 'OPEN';
		}
    }
    $row_temp['project_status'] = $stage_of;
    $row_temp['loss_reason_name'] = $aRow['loss_reason_name'];
	

    $name = $aRow['name'];
    $row_temp['name'] = $name;
   $row_temp['project_cost'] = $aRow['project_cost'];
    $row_temp['product_qty'] = $aRow['product_qty'];
    $row_temp['product_count'] = $aRow['product_count'];
    if($aRow['product_amt'] > 0)
        $row_temp['product_amt'] = $aRow['product_amt'];
    else
        $row_temp['product_amt'] = '0.00';
    $row_temp['company']  = $aRow['company'];

    $row_temp['tags']  = $aRow['tags'];

    $row_temp['start_date']   = _d($aRow['start_date']);
	
    $row_temp['project_created']  = _d($aRow['project_created']);
    $row_temp['project_modified']  = _d($aRow['project_modified']);
    $row_temp['deadline']  = _d($aRow['deadline']);

    $row_temp['created_by']  = $aRow['created_by'];
    $row_temp['modified_by']  = $aRow['modified_by'];
    $row_temp['project_currency']  = $aRow['project_currency'];
	
    $row_temp['pipeline_id']  = $aRow['pipeline_name'];
    $row_temp['contact_email1']  = $aRow['contact_email1'];
    $row_temp['contact_phone1']  = $aRow['contact_phone1'];
    $tl = $aRow['teamleader_name'];
    $row_temp['teamleader_name']  = $tl;

    $row_temp['contact_name']  = ' ';
	if(isset($aRow['contact_name']) && !empty($aRow['contact_name'])){
        $lable = '';
        $contact = '';
        if(isset($aRow['contact_email']) && !empty($aRow['contact_email'])) {
			$lable .= "Email - ".$aRow['contact_email'].'<br>';
        }
        if(isset($aRow['contact_phone']) && !empty($aRow['contact_phone'])) {
			 $lable .= "Phone - ".$aRow['contact_phone'];
        }
        if($lable == '') {
            $lable = _l('contact_name');
        }
        $contact .= $aRow['contact_name'];
        $row_temp['contact_name']  = $contact;
	}

    $membersOutput = '';

    $members       = explode(',', $aRow['members']);
    $exportMembers = '';
    foreach ($members as $key => $member) {
        if ($member != '') {
            $members_ids = explode(',', $aRow['members_ids']);
            $member_id   = $members_ids[$key];
         
            // For exporting
            $exportMembers .= $member . ', ';
        }
    }

    $membersOutput .= trim($exportMembers, ', ');
    $row_temp['members']   = $membersOutput;

    $status = get_project_status_by_id($aRow['status']);
    $row_temp['status']    = $status['name'];
	foreach ($customFieldsColumns as $customFieldColumn) {
        $row_temp[str_replace("cvalue_","",$customFieldColumn)] =  empty($aRow[$customFieldColumn])?'':$aRow[$customFieldColumn];
    }
	$i2 = 0;
    foreach($report_deal_list_column as $ckey=>$cval){
		if ($hasPermissionEdit) {
			if($i2==0){
				 //$row[] = $checkbox;
			}
			$i2++;
		}
			if((!empty($need_fields) && in_array($ckey, $need_fields)) || !empty($cus[$ckey])){
				if($ckey == 'project_start_date'){
					$ckey = 'start_date';
				}
				if($ckey == 'project_deadline'){
					$ckey = 'deadline';
				}
				$row[] =$row_temp[$ckey];
			}
    }

    $row['DT_RowClass'] = 'has-row-options';

    $row = hooks()->apply_filters('projects_table_row_data', $row, $aRow);
    $output['aaData'][] = $row;
}
