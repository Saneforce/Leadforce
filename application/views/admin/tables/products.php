<?php

defined('BASEPATH') or exit('No direct script access allowed');
$hasPermissionDelete = has_permission('customers', '', 'delete');


$this->ci->db->query("SET sql_mode = ''");

$aColumns = [
    '1',
    db_prefix().'products.id as id',
    db_prefix().'products.name as name',
    db_prefix().'products.code as code',
    db_prefix().'products.unit as unit',
    db_prefix().'products.tax as tax',
    db_prefix().'products.description as description',
    db_prefix().'products.status',
    '(SELECT cat_name FROM '.db_prefix().'product_category  WHERE id = '.db_prefix().'products.categoryid) as category',
    db_prefix().'products.created_date as created_date',
];

$sIndexColumn = 'id';
$sTable       = db_prefix().'products';
$where        = [];

// Add blank where all filter can be stored
$filter = [];



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
                $likeqry .= db_prefix()."products.name LIKE '".$val."%' OR ";
            else
                $likeqry .= db_prefix()."products.name LIKE '".$val."%'";
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
//array_push($where, ' AND '.db_prefix().'clients.deleted_status=0 ');
// if (!has_permission('customers', '', 'view')) {
//     array_push($where, 'AND '.db_prefix().'clients.userid IN (SELECT customer_id FROM '.db_prefix().'customer_admins WHERE staff_id=' . get_staff_user_id() . ') group by userid');
// }

if ($this->ci->input->post('exclude_inactive')) {
    //array_push($where, 'AND ('.db_prefix().'products.status = 1 OR '.db_prefix().'products.status=0)');
    //if (!is_admin()) {
       // array_push($where, 'AND ('.db_prefix().'products.status=1 ) ');
    // } else {
         array_push($where, 'AND ('.db_prefix().'products.status=1 )  group by '.db_prefix().'products.id ');
    // }
    
} else {
   // if (!is_admin()) {
     //   array_push($where, 'AND ('.db_prefix().'products.status = 1 OR '.db_prefix().'products.status=0 ) ');
    //} else {
        array_push($where, 'AND ('.db_prefix().'products.status = 1 OR '.db_prefix().'products.status=0 )  group by '.db_prefix().'products.id ');
   // }
}

// if ($this->ci->input->post('my_customers')) {
//     //array_push($where, 'AND '.db_prefix().'clients.userid IN (SELECT customer_id FROM '.db_prefix().'customer_admins WHERE staff_id=' . get_staff_user_id() . ')');
//     array_push($where, ' AND '.db_prefix().'clients.userid IN (SELECT userid FROM '.db_prefix().'contacts WHERE email=(SELECT email FROM '.db_prefix().'staff WHERE staffid=' . get_staff_user_id() . ')) group by '.db_prefix().'clients.userid ');
// }
// if (!is_admin()) {
//     $uids   = $this->ci->clients_model->get_userids();
//     //array_push($where, ' AND '.db_prefix().'clients.userid IN (SELECT userid FROM '.db_prefix().'contacts WHERE email=(SELECT email FROM '.db_prefix().'staff WHERE staffid=' . get_staff_user_id() . ')) group by '.db_prefix().'clients.userid ');
//     array_push($where, ' AND '.db_prefix().'clients.userid IN ('.implode(",",$uids).') group by '.db_prefix().'clients.userid ');
// }


// unset($aColumns[7]);
// unset($aColumns[8]);
//pre($aColumns);
// $aColumns = hooks()->apply_filters('customers_table_sql_columns', $aColumns);
// // Fix for big queries. Some hosting have max_join_limit
// if (count($custom_fields) > 4) {
//     @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
// }

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Bulk actions
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
    // User id
    //$row[] = $aRow['id'];

    // Company
    $name  = $aRow['name'];
    $isPerson = false;

    

    $url = admin_url('products/product/' . $aRow['id']);


    $name = '<a href="' . $url . '">' . $name . '</a>';

    $name .= '<div class="row-options">';
    $name .= '<a href="' . $url . '">' . _l('view') . '</a>';

    // if ($aRow['registration_confirmed'] == 0 && is_admin()) {
    //     $company .= ' | <a href="' . admin_url('clients/confirm_registration/' . $aRow['userid']) . '" class="text-success bold">' . _l('confirm_registration') . '</a>';
    // }
    // if (!$isPerson) {
    //     $company .= ' | <a href="' . admin_url('clients/client/' . $aRow['userid'] . '?group=contacts') . '">' . _l('customer_contacts') . '</a>';
    // }
    // if ($hasPermissionDelete) {
    //     $company .= ' | <a href="' . admin_url('clients/delete/' . $aRow['userid']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    // }

    $name .= '</div>';

    $row[] = $name;

    // Primary contact phone
    $row[] = $aRow['code'];
    
    $row[] = $aRow['unit'];
    $row[] = $aRow['category'];
    $row[] = $aRow['tax'];

    $row[] = $aRow['description'];
    
    

    // Toggle active/inactive customer
    $toggleActive = '<div class="onoffswitch" data-toggle="tooltip" data-title="' . _l('customer_active_inactive_help') . '">
    <input type="checkbox" data-switch-url="' . admin_url() . 'products/change_client_status" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . ($aRow[db_prefix().'products.status'] == 1 ? 'checked' : '') . '>
    <label class="onoffswitch-label" for="' . $aRow['id'] . '"></label>
    </div>';

    // For exporting
    $toggleActive .= '<span class="hide">' . ($aRow[db_prefix().'products.status'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';

    $row[] = $toggleActive;
    $row[] = _dt($aRow['created_date']);
   

    $row['DT_RowClass'] = 'has-row-options';

    if ($aRow['registration_confirmed'] == 0) {
        $row['DT_RowClass'] .= ' alert-info requires-confirmation';
        $row['Data_Title']  = _l('customer_requires_registration_confirmation');
        $row['Data_Toggle'] = 'tooltip';
    }

    $row = hooks()->apply_filters('customers_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}
