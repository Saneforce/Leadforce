<?php
defined('BASEPATH') or exit('No direct script access allowed');
if(!is_admin(get_staff_user_id())) {            
	$low_hie = '';
	$staff_id = get_staff_user_id();
	$lowlevel = $this->ci->staff_model->printHierarchyTree($staff_id,$prefix = '');
	if(!empty($lowlevel)) {
		$low_hie = ' OR staffid IN ('.implode(',', $lowlevel).')';
	}
	$staffdetails =  $this->ci->db->query('SELECT *, staffid as staff_id FROM ' . db_prefix() . 'staff WHERE staffid = "'.get_staff_user_id().'"'.$low_hie)->result_array();
	$all_members = $staffdetails;
} else {
	if(isset($_GET['pipelines']) && $_GET['pipelines'] != '')
		$all_members = $this->ci->pipeline_model->getPipelineFilterTeammembers($_GET['pipelines']);
	else
		$all_members = $this->ci->projects_model->get_distinct_projects_members();
}
$aColumns = [];
$aColumns = array('dashboard_name','id','create_date','update_date');
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'dashboard_report ';
$i =0;
$where = $join = [];
if(!empty($all_members)){
	$all_staff = array();
	foreach($all_members as $all_member1){
		$all_staff[] = $all_member1['staffid'];
	}
	$req_staff = implode(',',$all_staff);
	$where = array('where staff_id in('.$req_staff.')');
}

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