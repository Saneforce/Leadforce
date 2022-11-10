<?php
defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [];
$aColumns = array('dashboard_name','id','create_date','update_date');
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'dashboard_report ';
$i =0;
$where = $join = [];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
		$folder_id = "'".$aRow['id']."'";
		$_data = '<a href="'.admin_url('dashboard/view/'.$aRow['id']).'"> <i class="fa fa-folder-open-o" style="font-size:15px"></i>   '.$aRow['dashboard_name'].'</a><div class="row-options"><a href="javascript:void(0);" onclick="edit_name('.$folder_id.')" data-toggle="modal" data-target="#folder_edit_modal">' . _l('edit') . '</a></div>';
        $row[]              = $aRow['id'];
        $row[]              = $_data;
        $row[]              = date('d-m-Y',strtotime($aRow['create_date']));
        $row[]              = date('d-m-Y',strtotime($aRow['update_date']));
        $row['DT_RowClass'] = 'has-row-options';
    }
    $output['aaData'][] = $row;
}