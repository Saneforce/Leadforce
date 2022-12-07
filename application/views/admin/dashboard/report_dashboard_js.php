<?php
$i1 = 0;
if(!empty($summary)){
	foreach($summary as $sum1){
		$req_label = $req_data = $req_color = '';
		$i = 0;
		if(!empty($sum1['rows'])){ 
			foreach($sum1['rows'] as $sum_row){
				$report_page = $types[$i1];
				if($sum_row!='Average' && $sum_row!='Total'){
					if($sum1['view_by'] == 'priority'){
						if($sum1['summary_cls'][$i]['priority'] == '1'){
							$req_label .= '"'._l('task_priority_low').'",';
						}
						else if($sum1['summary_cls'][$i]['priority'] == '2'){
							$req_label .= '"'._l('task_priority_medium').'",';
						}
						else if($sum1['summary_cls'][$i]['priority'] == '3'){
							$req_label .= '"'._l('task_priority_high').'",';
						}
						else if($sum1['summary_cls'][$i]['priority'] == '4'){
							$req_label .= '"'._l('task_priority_urgent').'",';
						}
						else{
							$req_label .= '"'._l($sum_row).'",';
						}
					}
					else if($sum1['view_by'] == 'project_status'){
						if($sum_row == '0'){
							$req_label .= '"'.  _l('proposal_status_open').'",';
						}
						else if($sum_row == '1'){
							$req_label .= '"'. _l('project-status-won').'",';
						}
						else if($sum_row == '2'){
							$req_label .= '"'. _l('project-status-loss').'",';
						}
						else{
							$req_label .= '"'._l($sum_row).'",';
						}
					}
					else if($report_page == 'activity' && $sum1['view_by'] == 'status'){
						if($sum_row == '1'){
							$req_label .= '"'. _l('task_status_1').'",';
						}
						else if($sum_row == '2'){
							$req_label .= '"'.  _l('task_status_2').'",';
						}
						else if($sum_row == '3'){
							$req_label .= '"'.  _l('task_status_3').'",';
						}
						else if($sum_row == '4'){
							$req_label .= '"'.  _l('task_status_4').'",';
						}
						else if($sum_row == '5'){
							$req_label .= '"'.  _l('task_status_5').'",';
						}
					}
					else{
						$req_label .= '"'._l($sum_row).'",';
					}
					if($report_page == 'deal'){
						if(!empty($sum1['summary_cls'][$i]['total_cnt_deal']))
							$req_data .= '"'.$sum1['summary_cls'][$i]['total_cnt_deal'].'",';
						else
							$req_data .= '"0",';
					}
					else{
						if(!empty($sum1['summary_cls'][$i]['total_val_task']))						
							$req_data .= '"'.$sum1['summary_cls'][$i]['total_val_task'].'",';
						else
							$req_data .= '"0",';
					}
					if($report_page != 'activity' && $sum1['view_by'] == 'status'){
						$this->db->select('color');
						$this->db->where('name', $sum_row);
						$progress =  $this->db->get(db_prefix() . 'projects_status')->row();
						$req_color .= '"'.$progress->color.'",';
					}
					else{
						$req_color .= '"'.random_color().'",';
					}
				}
				$i++;
			}
			$req_label	= rtrim($req_label,',');
			$req_data	= rtrim($req_data,',');
			$req_color	= rtrim($req_color,',');
			
			?>
				<script>
				$(function() {
					var pie_chart = $('#report_pie_chart_<?php echo $i1;?>');
					if(pie_chart.length > 0){
						 new Chart(pie_chart, {
							type: 'pie',
							data: {"labels":[<?php echo $req_label;?>],"datasets":[{"data":[<?php echo $req_data;?>],"backgroundColor":[<?php echo $req_color;?>],"label":"<?php echo _l('summary');?>"}]},
							options: {
								responsive:true,
								legend: {
									display: true
								},
								maintainAspectRatio:false,
						   }
					   });
					}
					var bar_chart = $('#report_bar_chart_<?php echo $i1;?>');
					if(bar_chart.length > 0){
						new Chart(bar_chart, {
							type: 'bar',
							data: {"labels":[<?php echo $req_label;?>],"datasets":[{"data":[<?php echo $req_data;?>],"backgroundColor":[<?php echo $req_color;?>],"label":"<?php echo _l('summary');?>"}]},
							options:{
								responsive:true,
								maintainAspectRatio:false,
								legend: {
									display: false
								},
								scales: {
									xAxes: [{
									  scaleLabel: {
										display: true,
										labelString: '<?php echo _l($sum1['view_by']);?>',
										fontStyle:'bold'
									  }
									}],
									yAxes: [{
									  scaleLabel: {
										display: true,
										labelString: '<?php echo $sum1['sel_measure'];?>',
										fontStyle:'bold'
									  }
									}],
								}
							}
						});
					}
					var horizontalBar = $('#report_horizontal_chart_<?php echo $i1;?>');
					if(horizontalBar.length > 0){
						new Chart(horizontalBar, {
							type: 'horizontalBar',
							data: {"labels":[<?php echo $req_label;?>],"datasets":[{"data":[<?php echo $req_data;?>],"backgroundColor":[<?php echo $req_color;?>],"label":"<?php echo _l('summary');?>"}]},
							options:{
								responsive:true,
								maintainAspectRatio:false,
								legend: {
									display: false
								},
								scales: {
									yAxes: [{
									  scaleLabel: {
										display: true,
										labelString: '<?php echo _l($sum1['view_by']);?>',
										fontStyle:'bold'
									  }
									}],
									xAxes: [{
									  scaleLabel: {
										display: true,
										labelString: '<?php echo $sum1['sel_measure'];?>',
										fontStyle:'bold'
									  }
									}]
								}
							}
						});
					}
				});
				</script>
			<?php 
		}
		$i1++;
	}
}
?>