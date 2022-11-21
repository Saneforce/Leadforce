<?php $colarr = deal_all_fields();?>
<div class="row">
	<div class="col-md-12 m-bt-10">
		<table class="table" cellspacing="0">
			<thead>
				<?php  $colarr = deal_all_fields();
				$custom_fields = get_table_custom_fields('projects');
				$cus_1 = array();
				foreach($custom_fields as $cfkey=>$cfval){
					$cus_1[$cfval['slug']] = $colarr[$cfval['slug']] = array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
				}

				$custom_fields = get_table_custom_fields('customers');
				foreach($custom_fields as $cfkey=>$cfval){
					
					$cus_1[$cfval['slug']] = $colarr[$cfval['slug']] = array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
				}
				$projects_lists = (array)json_decode(get_option('report_deal_list_column_order')); 
				
				if(!empty($projects_lists)){
				?>
					<tr>
						<?php
						$j = 0;
						foreach($projects_lists as $ckey=>$cval){
							if((!empty($need_fields) && in_array($ckey, $need_fields)) || !empty($cus_1[$ckey])){
							?>
								<th class="cur_thead"><?php echo _l($colarr[$ckey]['ll']); ?></th>
							<?php
								$j++;
							}
						}
						?>
					</tr>
				<?php 
				}
				?>
			</thead>
			<tbody>
				<?php 
				if(!empty($results)){
					$i = 0;
					foreach($results as $rkey => $row_1){
						?>
						<tr>
							<?php 
							if(!empty($projects_lists)){
								foreach($projects_lists as $ckey => $cval){
									if((!empty($need_fields) && in_array($ckey, $need_fields)) || !empty($cus_1[$ckey])){
										$req_key = $ckey;
										 if($ckey == 'project_start_date'){
											 $ckey = 'start_date';
										 }
										 else if($ckey == 'project_deadline'){
											 $ckey = 'deadline';
										 }
										 else if($ckey == 'won_date' || $ckey == 'lost_date'){
											 $ckey = 'stage_on';
										 }
										
							?>
										<td class="sum_td">
											<?php 
											if(_l($colarr[$req_key]['ll']) =='Won Date'){
												if($row_1['project_status'] == 1){
													echo $row_1['won_date'];
												}
												else{
													echo '-';
												}
											}
											else if(_l($colarr[$req_key]['ll']) =='Lost Date'){
												if($row_1['project_status'] != 1){
													echo $row_1['lost_date'];
												}
												else{
													echo '-';
												}
											}
											else if(_l($colarr[$ckey]['ll']) !='Status' && _l($colarr[$ckey]['ll']) !='stage_on'){
												 if(isset($row_1[$ckey]) || $row_1[$ckey]!=''){
													 echo $row_1[$ckey];
												 }
												 else if(isset($row_1['cvalue_'.$ckey]) || $row_1['cvalue_'.$ckey]!=''){
													 echo $row_1['cvalue_'.$ckey];
												 }
												 else{
													 echo '-';
												 }
											 }
											 else{
												 if($row_1[$ckey] ==0){
													 echo 'Open';
												 }
												 else if($row_1[$ckey] ==1){
													echo 'Won';
												 }
												 else if($row_1[$ckey] ==2){
													echo 'Lost';
												 }
												 else{
													 echo '-';
												 }
											 }
											?>
										</td>
									
							<?php 	}
								}
							}
							?>
						</tr>
				<?php 
					}
				}
				else{
				?>
					<tr>
						<td colspan="<?php echo $j+1;?>" class="text-center"><?php echo _l('no_record');?></td>
					</tr>
				<?php
				}?>
			</tbody>
		</table>
	</div>
</div>
<style>
.sum_td,th.cur_thead{
	white-space:nowrap;
}
</style>