<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [];

if (has_permission('report', '', 'delete')) {
   // $aColumns[] = '1';
}
	$aColumns = array('folder','id','create_date','update_date');


$sIndexColumn = 'id';
$sTable       = db_prefix() . 'folder ';

$i =0;

$where = $join = [];


$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
		$_data = '<a href="'.admin_url('reports/view_deal_report/'.$aRow['id']).'"> <i class="fa fa-folder-open-o" style="font-size:15px"></i>   '.$aRow['folder'].'</a>';
        $row[]              = $aRow['id'];
        $row[]              = $_data;
        $row[]              = date('d-m-Y',strtotime($aRow['create_date']));
        $row[]              = date('d-m-Y',strtotime($aRow['update_date']));
        $row['DT_RowClass'] = 'has-row-options';
    }


    $output['aaData'][] = $row;
}
