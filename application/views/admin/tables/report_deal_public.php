<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns_temp = [
    'id'=>db_prefix() . 'projects.id as id',
    'name'=>'tblprojects.name as name',
    'teamleader_name'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'staff WHERE tblstaff.staffid=' . db_prefix() . 'projects.teamleader) as teamleader_name',
    'contact_name'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'project_contacts JOIN ' . db_prefix() . 'contacts on ' . db_prefix() . 'contacts.id = ' . db_prefix() . 'project_contacts.contacts_id WHERE tblproject_contacts.project_id=' . db_prefix() . 'projects.id AND tblproject_contacts.is_primary = 1) as contact_name',
    'project_cost'=>'project_cost',
    'product_qty'=>'(SELECT count(id) FROM tblproject_products WHERE projectid = ' . db_prefix() . 'projects.id) as product_qty',
    'product_amt'=>'(SELECT sum(price) FROM tblproject_products WHERE projectid = ' . db_prefix() . 'projects.id) as product_amt',
   'company'=> get_sql_select_client_company(),
    'tags'=>'(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'projects.id and rel_type="project" ORDER by tag_order ASC) as tags',
   'start_date'=> 'start_date',
   'deadline'=> 'deadline',
    'members'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'project_members JOIN ' . db_prefix() . 'staff on ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'project_members.staff_id WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members',
   'status'=> 'tblprojects.status as status',
   'project_status'=> 'tblprojects.stage_of as project_status',
   'pipeline_id'=> 'pipeline_id',
   'contact_email1'=>'(SELECT ' . db_prefix() . 'contacts.email FROM ' . db_prefix() . 'project_contacts JOIN ' . db_prefix() . 'contacts on ' . db_prefix() . 'contacts.id = ' . db_prefix() . 'project_contacts.contacts_id WHERE tblproject_contacts.project_id=' . db_prefix() . 'projects.id AND tblproject_contacts.is_primary = 1) as contact_email1',
   'contact_phone1'=>'(SELECT ' . db_prefix() . 'contacts.phonenumber FROM ' . db_prefix() . 'project_contacts JOIN ' . db_prefix() . 'contacts on ' . db_prefix() . 'contacts.id = ' . db_prefix() . 'project_contacts.contacts_id WHERE tblproject_contacts.project_id=' . db_prefix() . 'projects.id AND tblproject_contacts.is_primary = 1) as contact_phone1',
    'won_date'=>'stage_on as won_date',
    'lost_date'=>'stage_on as lost_date',
    'loss_reason_name'=>'(SELECT ' . db_prefix() . 'deallossreasons.name FROM ' . db_prefix() . 'deallossreasons  WHERE ' . db_prefix() . 'deallossreasons.id=' . db_prefix() . 'projects.loss_reason) as loss_reason_name',
    'project_currency'=>'project_currency',
    'project_created'=>'project_created',
    'project_modified'=>'project_modified',
    'modified_by'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'staff WHERE tblstaff.staffid=' . db_prefix() . 'projects.modified_by) as modified_by',
    'created_by'=>'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'staff WHERE tblstaff.staffid=' . db_prefix() . 'projects.created_by) as created_by',
    ];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'projects ';


$join = [
    'LEFT JOIN  ' . db_prefix() . 'projects_status ON ' . db_prefix() . 'projects_status.id = ' . db_prefix() . 'projects.status',
    'LEFT JOIN  ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'projects.clientid',
   // 'LEFT JOIN  ' . db_prefix() . 'project_products ON ' . db_prefix() . 'project_products.projectid = ' . db_prefix() . 'projects.id',
];

$where  = [];
$filter = [];
$w_have = '';
$where  = [];
$filter = [];
$w_have = '';

$req_filters = get_flters($req_deals,'deal');
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
    array_push($where, ' AND ' . db_prefix() . 'projects.id IN (SELECT project_id FROM '.db_prefix().'project_contacts WHERE contacts_id='.$exp[1].')');
    if (count($statusIds1) > 0) {
        array_push($filter, 'OR tblprojects.status IN (' . implode(', ', $statusIds1) . ')');
        array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
    }
} elseif(isset($_REQUEST['last_order_identifier']) && strpos($_REQUEST['last_order_identifier'], 'products_projects') !== false) {
    $exp = explode('products_projects_',$_REQUEST['last_order_identifier']);
    foreach ($this->ci->projects_model->get_project_statuses() as $status) {
        array_push($statusIds1, $status['id']);
    }
    array_push($where, ' AND ' . db_prefix() . 'projects.id IN (SELECT projectid FROM '.db_prefix().'project_products WHERE productid='.$exp[1].')');
    if (count($statusIds1) > 0) {
        array_push($filter, 'OR tblprojects.status IN (' . implode(', ', $statusIds1) . ')');
        array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
    }
} else {
    if ($clientid != '') {
        array_push($where, ' AND clientid=' . $clientid);
    }
}

foreach ($this->ci->projects_model->get_project_statuses() as $status) {
    if ($this->ci->input->post('project_status_' . $status['id'])) {
        array_push($statusIds, $status['id']);
    }
}

if (count($statusIds) > 0) {
    array_push($filter, 'OR tblprojects.status IN (' . implode(', ', $statusIds) . ')');
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

$custom_fields = get_table_custom_fields('projects');
$req_fields = array_column($custom_fields, 'slug'); 
$req_cnt = count($req_fields);
//$req_fields[$req_cnt + 1] = 'id';
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
    $fieldtois= 'clients.userid';
    if($field['fieldto'] =='projects'){
        $fieldtois= 'projects.id';
    }elseif($field['fieldto'] =='contacts'){
        $fieldtois= 'contacts.id';
    }
    if(isset($report_deal_list_column[$field['slug']])){
        $selectAs = 'cvalue_' .$field['slug'];
        array_push($customFieldsColumns, $selectAs);
        $cus[$field['slug']] =  'ctable_' . $key . '.value as ' . $selectAs;
        array_push($join, 'LEFT JOIN '.db_prefix().'customfieldsvalues as ctable_' . $key . ' ON '.db_prefix().$fieldtois.' = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
    }
}
$aColumns = array();
$aColumns_temp = array_merge($aColumns_temp,$cus);

$idkey = 0;
foreach($report_deal_list_column as $ckey=>$cval){
    if($ckey == 'id') {
        $idkey = 1;
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
				$aColumns[] =$aColumns_temp[$ckey];
			 }
         }
}
$pipeline = $_SESSION['pipelines'];
if (empty($pipeline)) {
    $pipeline = 0;
}else{
    array_push($where, ' AND ' . db_prefix() . 'projects.pipeline_id = '.$pipeline);
}

array_push($where, ' AND ' . db_prefix() . 'projects.deleted_status = 0');

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
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'clientid',
    '(SELECT GROUP_CONCAT(staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_members WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members_ids'.$idkey,
    'tblprojects.teamleader',
    '(SELECT contacts_id FROM ' . db_prefix() . 'project_contacts WHERE project_id=' . db_prefix() . 'projects.id AND is_primary = 1) as primary_id',
    '(select email from tblcontacts where id = (SELECT contacts_id FROM ' . db_prefix() . 'project_contacts WHERE project_id=' . db_prefix() . 'projects.id AND is_primary = 1)) as contact_email',
    '(select phonenumber from tblcontacts where id = (SELECT contacts_id FROM ' . db_prefix() . 'project_contacts WHERE project_id=' . db_prefix() . 'projects.id AND is_primary = 1)) as contact_phone',
],$s_group_by);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    
    $row = [];

    $stage_of = '';
    if($aRow['project_status']) {
        $stage_of = (($aRow['project_status'] == 1)?'WON':'LOSS');
    }
    $row_temp['project_status'] = $stage_of;
    $name =  $aRow['name'] ;
    $row_temp['name'] = $name;
   $row_temp['project_cost'] = $aRow['project_cost'];
    $row_temp['product_qty'] = $aRow['product_qty'];
    if($aRow['product_amt'] > 0)
        $row_temp['product_amt'] = $aRow['product_amt'];
    else
        $row_temp['product_amt'] = '0.00';

    $row_temp['company']  =  $aRow['company'] ;
	$req_tag = '';
	if(!empty($aRow['tags'])){
		
		$req_tag = rtrim($req_tag,", ");
	}

    $row_temp['tags']  = $aRow['tags'];

    $row_temp['start_date']   = _d($aRow['start_date']);

    $row_temp['deadline']  = _d($aRow['deadline']);

    $row_temp['pipeline_id']  = $aRow['pipeline_name'];
    $row_temp['contact_email1']  = $aRow['contact_email1'];
    $row_temp['contact_phone1']  = $aRow['contact_phone1'];
    $tl =  $aRow['teamleader_name'] ;
    $row_temp['teamleader_name']  = $tl;


    $row_temp['contact_name']  = ' ';
	if(isset($aRow['contact_name']) && !empty($aRow['contact_name'])){
        $lable = '';
        $contact = '';
        $contact .= $aRow['contact_name'];
        $row_temp['contact_name']  = $contact;
	}

    $status = get_project_status_by_id($aRow['status']);
    $row_temp['status']    =  $status['name'];
	foreach ($customFieldsColumns as $customFieldColumn) {
        $row_temp[str_replace("cvalue_","",$customFieldColumn)] =  empty($aRow[$customFieldColumn])?'':$aRow[$customFieldColumn];
    }
	$i2 = 0;
    foreach($report_deal_list_column as $ckey=>$cval){
		if((!empty($need_fields) && in_array($ckey, $need_fields)) || !empty($cus[$ckey])){
			$ch_key = $ckey;
			if($ckey == 'project_start_date'){
				$ckey = 'start_date';
			}
			if($ckey == 'project_deadline'){
				$ckey = 'deadline';
			}
			$row[$ch_key][] =$row_temp[$ckey];
		}   
	}
    $row['DT_RowClass'] = 'has-row-options';
    $row = hooks()->apply_filters('projects_table_row_data', $row, $aRow);
    $output['aaData'][] = $row;
}
