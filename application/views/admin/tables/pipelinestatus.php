<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = ['name', 'statusorder','status','color'];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'projects_status';
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
            $link = admin_url('pipelinestatus/view/' . $aRow['id']);
            $_data = '<b style="color: ' . $aRow['color'] . ';">' . $_data . '</b>';
            $_data .= '<div class="row-options">';
            $_data .= '<a href="' . $link . '">' . _l('view') . '</a>';
            if (has_permission('pipelinestatus', '', 'edit')) {
                $_data .= ' | <a href="' . admin_url('pipelinestatus/save/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            if (has_permission('pipelinestatus', '', 'delete')) {
                $_data .= ' | <a href="#" onclick="_delete_pipeline_status('.$aRow['id'].')" class="text-danger">' . _l('delete') . '</a>';
            }
            $_data .= '</div>';
        }
        $row[] = $_data;
        $row['DT_RowClass'] = 'has-row-options';
    }
    $output['aaData'][] = $row;
}