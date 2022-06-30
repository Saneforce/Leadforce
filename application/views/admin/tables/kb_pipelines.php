<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = ['name', 'created_date'];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'pipeline';
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
            $link = admin_url('pipeline/view/' . $aRow['id']);
            $_data = '<b>' . $_data . '</b>';
            $_data .= '<div class="row-options">';
            $_data .= '<a href="' . $link . '">' . _l('view') . '</a>';
            if (has_permission('pipeline', '', 'edit')) {
                $_data .= ' | <a href="' . admin_url('pipeline/save/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            if (has_permission('pipeline', '', 'delete')) {
                $_data .= ' | <a href="#"  onclick="_delete_pipeline_status('.$aRow['id'].')" class="text-danger">' . _l('delete') . '</a>';
            }
            $_data .= '</div>';
        }
		elseif ($aColumns[$i] == 'created_date') {
			// $_data = date('d-m-Y',strtotime($_data));
            $_data = _dt($_data);
        }
        $row[] = $_data;
        $row['DT_RowClass'] = 'has-row-options';
    }
    $output['aaData'][] = $row;
}