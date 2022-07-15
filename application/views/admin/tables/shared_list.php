<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [];

if (has_permission('report', '', 'delete')) {
   // $aColumns[] = '1';
}
	$aColumns = array(db_prefix() . 'shared.id',db_prefix() . 'shared.share_type',db_prefix() . 'shared.create_date',db_prefix() . 'shared.update_date',db_prefix() . 'report.report_name');


$sIndexColumn = 'id';
$sTable       = db_prefix() . 'shared ';

$i =0;

$where = $join = [];
$join = [
    'LEFT JOIN  ' . db_prefix() . 'report ON ' . db_prefix() . 'shared.report_id = ' . db_prefix() . 'report.id',
];
$staffid = get_staff_user_id();
array_push($where, " AND " . db_prefix() . "shared.share_type = 'Everyone'");
array_push($where, " OR " . db_prefix() . "shared.id in(SELECT share_id FROM ".db_prefix() ."shared_staff where staff_id = '".$staffid."') ");

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
		$folder_id = "'".$aRow['id']."'";
		
		$_data1 = '<a href="'.admin_url('reports/view_shared/'.$aRow[db_prefix().'shared.id']).'"> <i class="fa fa-area-chart" style="font-size:15px"></i>   '.$aRow[db_prefix().'report.report_name'].'</a>';
        $row[]              = $aRow[db_prefix().'shared.id'];
        $row[]              = $_data1;
        $row[]              = date('d-m-Y',strtotime($aRow[db_prefix().'shared.create_date']));
        $row[]              = date('d-m-Y',strtotime($aRow[db_prefix().'shared.update_date']));
        $row['DT_RowClass'] = 'has-row-options';
    }

    $output['aaData'][] = $row;
}
