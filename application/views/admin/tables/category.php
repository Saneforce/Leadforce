<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'cat_name'
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'item_category';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data                       = $aRow[$aColumns[$i]];
      //  $is_referenced_expenses      = (total_rows(db_prefix().'expenses', ['tax' => $aRow['id']]) > 0 || total_rows(db_prefix().'expenses', ['tax2' => $aRow['id']]) > 0 ? 1 : 0);
        
        $row[] = $_data;
    }
	$req_id = "'".$aRow['id']."'";
    $options = icon_btn('#' . $aRow['id'], 'pencil-square-o', 'btn-default', [
        'data-toggle'                      => 'modal',
        'data-target'                      => '#category_modal',
        'data-id'                          => $aRow['id'],
        ]);

    $row[] = $options .= icon_btn('category/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');

    $output['aaData'][] = $row;
}
