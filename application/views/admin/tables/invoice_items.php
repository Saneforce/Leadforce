<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [];

if (has_permission('items', '', 'delete')) {
    $aColumns[] = '1';
}
$aColumns_temp =array(
    'name'=>db_prefix() . 'items.name',
    'code'=>db_prefix() . 'items.code',
    'cat_name'=> db_prefix() . 'item_category.cat_name',
    'unit'=>db_prefix(). 'items.unit',
    'description'=>db_prefix(). 'items.description',
);
$items_list_column_order = (array)json_decode(get_option('items_list_column'));
if($items_list_column_order){
    foreach($items_list_column_order as $orderkey => $ordervalue){
        $aColumns [] =$aColumns_temp[$orderkey];
    }
}
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'items';

$join = [
    'LEFT JOIN ' . db_prefix() . 'taxes t1 ON t1.id = ' . db_prefix() . 'items.tax',
    'LEFT JOIN ' . db_prefix() . 'item_category ON ' . db_prefix() . 'item_category.id = ' . db_prefix() . 'items.categoryid',
    ];
$additionalSelect = [
    db_prefix() . 'items.id',
    't1.name as taxname_1',
    't1.taxrate as taxrate_1',
    't1.id as tax_id_1',
    'group_id',
    ];

$custom_fields = get_custom_fields('items');

$locationCustomFields =[];
foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);

    if($field['type'] =='location'){
        array_push($locationCustomFields, 'ctable_' . $key . '.value as ' . $selectAs);
    }
    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'items.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="items_pr" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }

        if(in_array($aColumns[$i] , $locationCustomFields)){
            $_data = $_data==''?'':custom_field_location_icon_link($_data);
        }
        if ($aColumns[$i] == '1') {
            $_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
        } elseif ($aColumns[$i] == 'tax') {
			if(!empty(!$aRow['taxrate_1'])){
             if (!$aRow['taxrate_1']) {
                $aRow['taxrate_1'] = 0;
            }
            $_data = '<span data-toggle="tooltip" title="' . $aRow['taxrate_1'] . '%" data-taxid="' . $aRow['taxrate_1'] . '">' . $aRow['taxrate_1'] . '%' . '</span>';
			}else{
				 $_data = '';
			}
        } /*elseif ($aColumns[$i] == db_prefix() . 'item_category.cat_name') {
			if(!empty(!$aRow[db_prefix() . 'item_category.cat_name'])){
            
            $_data = '<span data-toggle="tooltip" title="' . $aRow[db_prefix() . 'item_category.cat_name']. '" data-taxid="' . $aRow['cat_name'] . '">' . $aRow['cat_name'] . '' . '</span>';
			}else{
				 $_data = '';
			}
        }*/ elseif ($aColumns[$i] == db_prefix() . 'items.name') {
            $_data = '<a href="#" data-toggle="modal" data-target="#sales_item_modal" data-id="' . $aRow['id'] . '">' . $_data . '</a>';
            $_data .= '<div class="row-options">';

            if (has_permission('items', '', 'edit')) {
                $_data .= '<a href="#" data-toggle="modal" data-target="#sales_item_modal" data-id="' . $aRow['id'] . '">' . _l('edit') . '</a>';
            }

            if (has_permission('items', '', 'delete')) {
                $_data .= ' | <a href="' . admin_url('invoice_items/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }
            $_data .= '</div>';
        }/*elseif ($aColumns[$i] == 'description') {
            $_data = '<a href="#" data-toggle="modal" data-target="#sales_item_modal" data-id="' . $aRow['id'] . '">' . $_data . '</a>';
            $_data .= '<div class="row-options">';

           /* if (has_permission('items', '', 'edit')) {
                $_data .= '<a href="#" data-toggle="modal" data-target="#sales_item_modal" data-id="' . $aRow['id'] . '">' . _l('edit') . '</a>';
            }

            if (has_permission('items', '', 'delete')) {
                $_data .= ' | <a href="' . admin_url('invoice_items/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }
            $_data .= '</div>';
        }*/ else {
            if (startsWith($aColumns[$i], 'ctable_') && is_date($_data)) {
                $_data = _d($_data);
            }
        }

        $row[]              = $_data;
        $row['DT_RowClass'] = 'has-row-options';
    }


    $output['aaData'][] = $row;
}
