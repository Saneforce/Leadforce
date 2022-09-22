<?php
defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [];
if (has_permission('report', '', 'delete')) {
   // $aColumns[] = '1';
}
$aColumns = array('folder','id','num_reports'=>'(SELECT count(id) FROM '. db_prefix() .'report WHERE folder_id = ' . db_prefix() . 'folder.id) as num_reports','create_date','update_date');
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'folder ';
$i =0;
$where = $join = [];
array_push($where, " AND " . db_prefix() . "folder.folder_type = '".$_REQUEST['type']."' ");
if(isset($_REQUEST['search']['value']) && $_REQUEST['search']['value']!='') {
	array_push($where, ' AND ' . db_prefix() . 'folder.id IN (SELECT folder_id FROM ' . db_prefix() . 'report WHERE count(id)=' . $_REQUEST['search']['value'] . ')');
}
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
		$folder_id = "'".$aRow['id']."'";
		$_data = '<a href="'.admin_url('reports/view_deal_report/'.$aRow['id'].'/'.$_REQUEST['type']).'"> <i class="fa fa-folder-open-o" style="font-size:15px"></i>   '.$aRow['folder'].'</a><div class="row-options"><a href="javascript:void(0);" onclick="edit_folder('.$folder_id.')" data-toggle="modal" data-target="#folder_edit_modal">' . _l('edit') . '</a></div>';
        $row[]              = $aRow['id'];
        $row[]              = $_data;
        $row[]              = $aRow['num_reports'];
        $row[]              = date('d-m-Y',strtotime($aRow['create_date']));
        $row[]              = date('d-m-Y',strtotime($aRow['update_date']));
        $row['DT_RowClass'] = 'has-row-options';
    }
    $output['aaData'][] = $row;
}