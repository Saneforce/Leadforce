<?php
defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = array('report_name','id','create_date','update_date');
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'report ';
$i =0;
$where = $join = [];
$where = ["AND ".db_prefix()."report.folder_id = '".$id."'"];
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
		$cur_link = "'".$aRow['id']."'";
		$_data = '<a href="'.admin_url('reports/edit_deal_report/'.$aRow['id'].'?type='.$type).'"> <i class="fa fa-area-chart" style="font-size:15px"></i>   '.$aRow['report_name'].'</a><div class="row-options"><a href="javascript:void(0);" class="" data-toggle="modal" data-target="#public_add_modal" onclick="load_public('.$cur_link.')">Public Link</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="" onclick="edit_report('.$cur_link.')" data-toggle="modal" data-target="#report_edit_modal">' . _l('edit') . '</a>&nbsp;&nbsp;&nbsp;<a href="' . admin_url('reports/delete_report/' . $aRow['id'].'/'.$type) . '" class="text-danger _delete">' . _l('delete') . '</a></div>';
		$row[]              = $aRow['id'];
        $row[]              = $_data;
		$row[]              = _d($aRow['create_date']);
        $row[]              = _d($aRow['update_date']);
        $row['DT_RowClass'] = 'has-row-options';
    }


    $output['aaData'][] = $row;
}
