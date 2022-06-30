<?php

defined('BASEPATH') or exit('No direct script access allowed');
$hasPermissionDelete = has_permission('customers', '', 'delete');

$custom_fields = get_table_custom_fields('customers');

$this->ci->db->query("SET sql_mode = ''");


$aColumns = [
    //  'id'=>db_prefix() . 'projects.id as id',
      db_prefix().'clients.userid as userid'
     ];
  
      
  //pre($projects_list_column_order);
  
  // $aColumns[] = db_prefix().'clients.userid as userid';
  // pr($projects_list_column_order);
  
$clients_list_column_order = (array)json_decode(get_option('clients_list_column_order')); //pr($clients_list_column_order);
$table_data = array();


$fields = array(
"company",
"active",
"datecreated",
"vat",
"phonenumber",
"country",
"city",
"zip",
"state",
"address",
"website");
foreach($clients_list_column_order as $ckey=>$cval){
    if($ckey == 'customerGroups') {
        $table_data[] ='(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM '.db_prefix().'customer_groups JOIN '.db_prefix().'customers_groups ON '.db_prefix().'customer_groups.groupid = '.db_prefix().'customers_groups.id WHERE customer_id = '.db_prefix().'clients.userid ORDER by name ASC) as customerGroups';
    } 
    if (in_array($ckey, $fields)) {
        if($ckey == 'country') {
            $table_data[] = '(select short_name from tblcountries where country_id = tblclients.country) as country';
        } else {
            $table_data[] ='tblclients.'.$ckey.' as '.$ckey;
        }
    }
}

$aColumns = array_unique(array_merge($aColumns,$table_data));

$sIndexColumn = 'userid';
$sTable       = db_prefix().'clients';
$where        = [];

// Add blank where all filter can be stored
$filter = $customFieldsColumns = [];

$join = [
    'LEFT JOIN '.db_prefix().'contacts ON '.db_prefix().'contacts.userid='.db_prefix().'clients.userid AND '.db_prefix().'contacts.is_primary=1',
];

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $field['slug'] : 'cvalue_' . $field['slug']);
    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN '.db_prefix().'customfieldsvalues as ctable_' . $key . ' ON '.db_prefix().'clients.userid = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}



$join = hooks()->apply_filters('customers_table_sql_join', $join);


 
//$aColumns = array_unique(array_merge($aColumns,$table_data));
//pre($aColumns);

// Filter by custom groups
$groups   = $this->ci->clients_model->get_groups();
$groupIds = [];
foreach ($groups as $group) {
    if ($this->ci->input->post('customer_group_' . $group['id'])) {
        array_push($groupIds, $group['id']);
    }
}
if (count($groupIds) > 0) {
    array_push($filter, 'AND '.db_prefix().'clients.userid IN (SELECT customer_id FROM '.db_prefix().'customer_groups WHERE groupid IN (' . implode(', ', $groupIds) . '))');
}

$countries  = $this->ci->clients_model->get_clients_distinct_countries();
$countryIds = [];
foreach ($countries as $country) {
    if ($this->ci->input->post('country_' . $country['country_id'])) {
        array_push($countryIds, $country['country_id']);
    }
}
if (count($countryIds) > 0) {
    array_push($filter, 'AND country IN (' . implode(',', $countryIds) . ')');
}


$this->ci->load->model('invoices_model');
// Filter by invoices
$invoiceStatusIds = [];
foreach ($this->ci->invoices_model->get_statuses() as $status) {
    if ($this->ci->input->post('invoices_' . $status)) {
        array_push($invoiceStatusIds, $status);
    }
}
if (count($invoiceStatusIds) > 0) {
    array_push($filter, 'AND '.db_prefix().'clients.userid IN (SELECT clientid FROM '.db_prefix().'invoices WHERE status IN (' . implode(', ', $invoiceStatusIds) . '))');
}

// Filter by estimates
$estimateStatusIds = [];
$this->ci->load->model('estimates_model');
foreach ($this->ci->estimates_model->get_statuses() as $status) {
    if ($this->ci->input->post('estimates_' . $status)) {
        array_push($estimateStatusIds, $status);
    }
}
if (count($estimateStatusIds) > 0) {
    array_push($filter, 'AND '.db_prefix().'clients.userid IN (SELECT clientid FROM '.db_prefix().'estimates WHERE status IN (' . implode(', ', $estimateStatusIds) . '))');
}

// Filter by projects
$projectStatusIds = [];
$this->ci->load->model('projects_model');
foreach ($this->ci->projects_model->get_project_statuses() as $status) {
    if ($this->ci->input->post('projects_' . $status['id'])) {
        array_push($projectStatusIds, $status['id']);
    }
}
if (count($projectStatusIds) > 0) {
    array_push($filter, 'AND '.db_prefix().'clients.userid IN (SELECT clientid FROM '.db_prefix().'projects WHERE status IN (' . implode(', ', $projectStatusIds) . '))');
}

// Filter by proposals
$proposalStatusIds = [];
$this->ci->load->model('proposals_model');
foreach ($this->ci->proposals_model->get_statuses() as $status) {
    if ($this->ci->input->post('proposals_' . $status)) {
        array_push($proposalStatusIds, $status);
    }
}
if (count($proposalStatusIds) > 0) {
    array_push($filter, 'AND '.db_prefix().'clients.userid IN (SELECT rel_id FROM '.db_prefix().'proposals WHERE status IN (' . implode(', ', $proposalStatusIds) . ') AND rel_type="customer")');
}

// Filter by having contracts by type
$this->ci->load->model('contracts_model');
$contractTypesIds = [];
$contract_types   = $this->ci->contracts_model->get_contract_types();

foreach ($contract_types as $type) {
    if ($this->ci->input->post('contract_type_' . $type['id'])) {
        array_push($contractTypesIds, $type['id']);
    }
}
if (count($contractTypesIds) > 0) {
    array_push($filter, 'AND '.db_prefix().'clients.userid IN (SELECT client FROM '.db_prefix().'contracts WHERE contract_type IN (' . implode(', ', $contractTypesIds) . '))');
}

// Filter by proposals
$customAdminIds = [];
foreach ($this->ci->clients_model->get_customers_admin_unique_ids() as $cadmin) {
    if ($this->ci->input->post('responsible_admin_' . $cadmin['staff_id'])) {
        array_push($customAdminIds, $cadmin['staff_id']);
    }
}

if (count($customAdminIds) > 0) {
    //array_push($filter, 'AND '.db_prefix().'clients.userid IN (SELECT customer_id FROM '.db_prefix().'customer_admins WHERE staff_id IN (' . implode(', ', $customAdminIds) . '))');
}

if ($this->ci->input->post('requires_registration_confirmation')) {
    array_push($filter, 'AND '.db_prefix().'clients.registration_confirmed=0');
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}


$alphabets = [];
$alpha   = range('A','Z');
array_unshift($alpha , 'All');
foreach ($alpha as $char) {
    if ($this->ci->input->post('alphabet_' . $char)) {
        array_push($alphabets, $char);
    }
}

//pre($alphabets);
$likeqry = '';
$alphaCnt = count($alphabets);
$all = '';
if($alphaCnt > 0) {
    $i = 1;
    foreach ($alphabets as $val) {
        if($val != 'All' && $all == '') {
            if($i < $alphaCnt)
                $likeqry .= db_prefix()."clients.company LIKE '".$val."%' OR ";
            else
                $likeqry .= db_prefix()."clients.company LIKE '".$val."%'";
            $i++;
        } else {
            $all = 1;
        }
    }
}
//echo $likeqry; exit;
if($likeqry) {
    $likeqry = ' AND ( '.$likeqry.' ) ';
    array_push($where, $likeqry);
}
array_push($where, ' AND '.db_prefix().'clients.deleted_status=0 ');
if (!has_permission('customers', '', 'view')) {
    array_push($where, 'AND '.db_prefix().'clients.userid IN (SELECT customer_id FROM '.db_prefix().'customer_admins WHERE staff_id=' . get_staff_user_id() . ') group by userid');
}

if ($this->ci->input->post('exclude_inactive')) {
    //array_push($where, 'AND ('.db_prefix().'clients.active = 1 OR '.db_prefix().'clients.active=0)');
    if (!is_admin()) {
        array_push($where, 'AND ('.db_prefix().'clients.active=1 ) ');
    } else {
        array_push($where, 'AND ('.db_prefix().'clients.active=1 )  group by '.db_prefix().'clients.userid ');
    }
} else {
    if (!is_admin()) {
        array_push($where, 'AND ('.db_prefix().'clients.active = 1 OR '.db_prefix().'clients.active=0 ) ');
    } else {
        array_push($where, 'AND ('.db_prefix().'clients.active = 1 OR '.db_prefix().'clients.active=0 )  group by '.db_prefix().'clients.userid ');
    }
}

if ($this->ci->input->post('my_customers')) {
    //array_push($where, 'AND '.db_prefix().'clients.userid IN (SELECT customer_id FROM '.db_prefix().'customer_admins WHERE staff_id=' . get_staff_user_id() . ')');
    array_push($where, ' AND '.db_prefix().'clients.userid IN (SELECT userid FROM '.db_prefix().'contacts WHERE email=(SELECT email FROM '.db_prefix().'staff WHERE staffid=' . get_staff_user_id() . ')) group by '.db_prefix().'clients.userid ');
}
if (!is_admin()) {
    $my_staffids = $this->ci->staff_model->get_my_staffids();
    if($my_staffids){
        $uids   = $this->ci->clients_model->get_mystaffs_userids($my_staffids);
        if(count($uids) == 0) {
            $uids[] = 0;
        }
        array_push($where, ' AND ('.db_prefix().'clients.userid IN ('.implode(",",$uids).') OR '.db_prefix().'clients.addedfrom in ('.implode(",",$my_staffids).')) group by '.db_prefix().'clients.userid ');
    } else {
        $uids   = $this->ci->clients_model->get_userids();
        if(count($uids) == 0) {
            $uids[] = 0;
        }
        array_push($where, ' AND ('.db_prefix().'clients.userid IN ('.implode(",",$uids).') OR ('.db_prefix().'clients.addedfrom="'.get_staff_user_id().'")) group by '.db_prefix().'clients.userid ');
    }
    //array_push($where, ' AND '.db_prefix().'clients.userid IN (SELECT userid FROM '.db_prefix().'contacts WHERE email=(SELECT email FROM '.db_prefix().'staff WHERE staffid=' . get_staff_user_id() . ')) group by '.db_prefix().'clients.userid ');
}
$view_ids = $this->ci->staff_model->getFollowersViewList();
//pre($aColumns);
//unset($aColumns[5]);
//unset($aColumns[6]);
//pre($aColumns);
$aColumns = hooks()->apply_filters('customers_table_sql_columns', $aColumns);
//pre($aColumns);
// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix().'contacts.id as contact_id',
    'lastname',
    db_prefix().'clients.zip as zip',
    'registration_confirmed',
    'tblclients.addedfrom',
]);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    //pre($aRow);
    $row_temp = $row = [];
    
    // Bulk actions
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['userid'] . '"><label></label></div>';
    // User id
    //$row[] = $aRow['userid'];

    // Company
    $company  = $aRow['company'];
    $isPerson = false;

    if ($company == '') {
        $company  = _l('no_company_view_profile');
        $isPerson = true;
    }

    $url = admin_url('clients/client/' . $aRow['userid']);

    if ($isPerson && $aRow['contact_id']) {
        $url .= '?contactid=' . $aRow['contact_id'];
    }

    $company = '<a href="' . $url . '">' . $company . '</a>';

    $company .= '<div class="row-options">';
    $company .= '<a href="' . $url . '">' . _l('view') . '</a>';

    if ($aRow['registration_confirmed'] == 0 && is_admin()) {
        $company .= ' | <a href="' . admin_url('clients/confirm_registration/' . $aRow['userid']) . '" class="text-success bold">' . _l('confirm_registration') . '</a>';
    }
    if (!$isPerson) {
        $company .= ' | <a href="' . admin_url('clients/client/' . $aRow['userid'] . '?group=contacts') . '">' . _l('customer_contacts') . '</a>';
    }
    if (($hasPermissionDelete && (!empty($my_staffids) && in_array($aRow['addedfrom'],$my_staffids) && !in_array($aRow['addedfrom'],$view_ids))) || is_admin(get_staff_user_id()) || $aRow['addedfrom'] == get_staff_user_id()) {
        $company .= ' | <a href="' . admin_url('clients/delete/' . $aRow['userid']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }

    $company .= '</div>';

    $row_temp['company'] = $company;
    // Primary contact
    //$row[] = ($aRow['contact_id'] ? '<a href="' . admin_url('clients/client/' . $aRow['userid'] . '?contactid=' . $aRow['contact_id']) . '" target="_blank">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>' : '');

    // Primary contact email
    //$row[] = ($aRow['email'] ? '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>' : '');

    // Primary contact phone
    //pre($aRow);

    // Toggle active/inactive customer
    if (($hasPermissionDelete && (!empty($my_staffids) && in_array($aRow['addedfrom'],$my_staffids) && !in_array($aRow['addedfrom'],$view_ids))) || is_admin(get_staff_user_id()) || $aRow['addedfrom'] == get_staff_user_id()) {
        $disabled = "";
    } else {
        $disabled = "disabled";
    }
    $toggleActive = '<div class="onoffswitch">
    <input '.$disabled.' type="checkbox"' . ($aRow['registration_confirmed'] == 0 ? ' disabled' : '') . ' data-switch-url="' . admin_url() . 'clients/change_client_status" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['userid'] . '" data-id="' . $aRow['userid'] . '" ' . ($aRow['active'] == 1 ? 'checked' : '') . '>
    <label class="onoffswitch-label" for="' . $aRow['userid'] . '"></label>
    </div>';

    // For exporting
    $toggleActive .= '<span class="hide">' . ($aRow['active'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';

    $row_temp['active'] = $toggleActive;

        $row_temp['vat'] = $aRow['vat'];
        $row_temp['website'] = $aRow['website'];
        $row_temp['country'] = $aRow['country'];
        $row_temp['city'] = $aRow['city'];
        $row_temp['state'] = $aRow['state'];
        $row_temp['zip'] = $aRow['zip'];
        $row_temp['address'] = $aRow['address'];
        $row_temp['phonenumber'] = ($aRow['phonenumber'] ? '<a href="tel:' . $aRow['phonenumber'] . '">' . $aRow['phonenumber'] . '</a>' : '');
        // Customer groups parsing
    $groupsRow = '';
    if ($aRow['customerGroups']) {
        $groups = explode(',', $aRow['customerGroups']);
        foreach ($groups as $group) {
            $groupsRow .= '<span class="label label-default mleft5 inline-block customer-group-list pointer">' . $group . '</span>';
        }
    }

    $row_temp['customerGroups'] = $groupsRow;

    $row_temp['datecreated'] = _dt($aRow['datecreated']);
    
    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row_temp[str_replace("cvalue_","",$customFieldColumn)] =  empty($aRow[$customFieldColumn])?'':$aRow[$customFieldColumn];
    }

    
    foreach($clients_list_column_order as $ckey=>$cval){
       // echo $ckey.'<br>';
            $row[] =$row_temp[$ckey];
    }
    //pre($row);
    $row['DT_RowClass'] = 'has-row-options';

    if ($aRow['registration_confirmed'] == 0) {
        $row['DT_RowClass'] .= ' alert-info requires-confirmation';
        $row['Data_Title']  = _l('customer_requires_registration_confirmation');
        $row['Data_Toggle'] = 'tooltip';
    }
//pre($row);
    //$row = hooks()->apply_filters('customers_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}
