<div class="row">
	<div class="col-md-12 m-bt-10">
		<table class="table" cellspacing="0">
			<thead>
				<?php  $colarr = task_all_columns();
				$custom_fields = get_table_custom_fields('tasks');
				$cus_1 = array();
				foreach($custom_fields as $cfkey=>$cfval){
					$cus_1[$cfval['slug']] = $colarr[$cfval['slug']] = array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
				}
				$task_lists = (array)json_decode(get_option('report_task_list_column_order')); 
				if(!empty($task_lists)){
				?>
					<tr>
						<?php
						$j = 0;
						foreach($task_lists as $ckey=>$cval){
						?>
							<th class="cur_thead"><?php echo _l($colarr[$ckey]['ll']); ?></th>
						<?php
							$j++;
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
							if(!empty($task_lists)){
								foreach($task_lists as $ckey => $cval){
							?>
									<td class="sum_td">
										<?php 
										if(_l($colarr[$ckey]['ll']) =='Priority'){
											if($row_1[$ckey] == 1){
												echo _l('task_priority_low');
											}
											else if($row_1[$ckey] == 2){
												echo _l('task_priority_medium');
											}
											else if($row_1[$ckey] == 3){
												echo _l('task_priority_high');
											}
											else if($row_1[$ckey] == 4){
												echo _l('task_priority_urgent');
											}
										}
										else if(_l($colarr[$ckey]['ll']) !='Status'){
											 if(isset($row_1[$ckey]) || $row_1[$ckey]!=''){
												if($row_1[$ckey] == 'project'){
													echo _l('deal');
												}
												else if($row_1[$ckey] == 'customer') {
													echo  _l('client');
												}
												else{
													echo $row_1[$ckey];
												}
											 }
											 else if(isset($row_1['cvalue_'.$ckey]) || $row_1['cvalue_'.$ckey]!=''){
												 echo $row_1['cvalue_'.$ckey];
											 }
											 else{
												 echo '-';
											 }
										 }
										 else{
											 if($row_1[$ckey] ==1){
												 echo 'Upcoming';
											 }
											 else if($row_1[$ckey] ==2){
												echo 'overdue';
											 }
											 else if($row_1[$ckey] ==3){
												echo 'Today';
											 }
											 else if($row_1[$ckey] ==4){
												echo 'In Progress';
											 }
											 else if($row_1[$ckey] ==5){
												echo 'Completed';
											 }
											 else{
												 echo '-';
											 }
										 }
										?>
									</td>
							<?php 	
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