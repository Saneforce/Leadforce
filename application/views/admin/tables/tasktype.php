<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = ['name', 'status'];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'tasktype';
$additionalSelect = ['id'];
$join = [];
$where = [];
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow)
{
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++)
	{
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name')
		{
            $link = admin_url('tasktype/view/' . $aRow['id']);
            $_data = '<b>' . $_data . '</b>';
            $_data .= '<div class="row-options">';
            $_data .= '<a href="' . $link . '">' . _l('view') . '</a>';
            if (has_permission('tasktype', '', 'edit')) {
                $_data .= ' | <a href="' . admin_url('tasktype/save/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            if (has_permission('tasktype', '', 'delete') && $aRow['id'] != 1) {
                $_data .= ' | <a href="' . admin_url('tasktype/delete_tasktype/' . $aRow['id']) . '" class="_delete text-danger">' . _l('delete') . '</a>';
            }
            $_data .= '</div>';
        }
        $row[] = $_data;
        $row['DT_RowClass'] = 'has-row-options';
    }
    $output['aaData'][] = $row;
}