<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = ['name', 'created_date'];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'deallossreasons';
$additionalSelect = ['id'];
$join = [];
// $where = ['AND publishstatus=1'];
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
            $link = admin_url('DealLossReasons/view/' . $aRow['id']);
            $_data = '<b>' . $_data . '</b>';
            $_data .= '<div class="row-options">';
            if (has_permission('DealLossReasons', '', 'edit')) {
                $_data .= '<a href="' . admin_url('DealLossReasons/save/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            if (has_permission('DealLossReasons', '', 'delete')) {
                $_data .= ' | <a href="' . admin_url('DealLossReasons/delete_deallossreasons/' . $aRow['id']) . '" class="_delete text-danger">' . _l('delete') . '</a>';
            }
            $_data .= '</div>';
        }
		elseif ($aColumns[$i] == 'created_date') {
            $_data = _dt($_data);
        }
        $row[] = $_data;
        $row['DT_RowClass'] = 'has-row-options';
    }
    $output['aaData'][] = $row;
}