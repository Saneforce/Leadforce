<?php defined('BASEPATH') or exit('No direct script access allowed');
$is_admin = is_admin();
$i = 0;
$page = ($_REQUEST['offset']/$_REQUEST['limit'])+1;
$req_data = array();
foreach ($statuses as $status) {
	$status_color = '';
	if(!empty($status["color"])){
	  $status_color = 'style="background:'.$status["color"].';border:1px solid '.$status['color'].'"';
	}
	 $projects = $this->projects_model->do_kanban_query($status['id'],$this->input->get('search'),$page,array('sort_by'=>$this->input->get('sort_by'),'sort'=>$this->input->get('sort')));
	  $req_html = '';
	  foreach ($projects as $project) {
		
		$req_html .=  $this->load->view('admin/projects/_kan_ban_card',array('project'=>$project,'status'=>$status),true);
	  } 
	  $req_data['status_'.$status['id']] = $req_html;
	   $i++; 
} 
echo json_encode($req_data);
?>
