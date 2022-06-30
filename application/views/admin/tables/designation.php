<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix().'designations.name',
    db_prefix().'roles.name',
    ];

$sIndexColumn = 'designationid';
$sTable       = db_prefix().'designations';
$join         = ['LEFT JOIN '.db_prefix().'roles ON '.db_prefix().'roles.roleid = '.db_prefix().'designations.roleid'];
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,  $join, [], [
    'designationid',
    db_prefix().'roles.name as rolename'
    ]);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            $_data            = '<a href="' . admin_url('designation/designations/' . $aRow['designationid']) . '" class="mbot10 display-block">' . $_data . '</a>';
            $_data .= '<span class="mtop10 display-block">' . _l('designations_total_users') . ' ' . total_rows(db_prefix().'staff', [
                'designation' => $aRow['designationid'],
                ]) . '</span>';
        }
        $row[] = $_data;
    }

    $options = icon_btn('designation/designations/' . $aRow['designationid'], 'pencil-square-o');
    $row[]   = $options .= icon_btn('designation/delete/' . $aRow['designationid'], 'remove', 'btn-danger _delete');

    $output['aaData'][] = $row;
}
