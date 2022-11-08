<?php $k = '';
if(!empty($summary['columns'])){
	$i = 0;
?>
	<table class="table" cellspacing="0">
		<thead>
			<tr>
				<?php foreach($summary['columns'] as $clm1){
					if(str_contains(_l($clm1), 'Average')){
						$k = $i;
					}
					$clm2 = get_th_column(_l($clm1),'projects');
				?>
					<th class="cur_thead"><?php echo _l($clm2);?></th>
				<?php $i++;
				}?>
			</tr>
		</thead>
		<tbody>
			<?php 
			$date_range = (!empty($summary['date_range1']))?$summary['date_range1']:'';
			if(!empty($summary['rows'])){
				$i = 0;
				foreach($summary['rows'] as $row_1){
					if (str_contains($row_1, ' '.date('Y'))) {
						if (str_contains($row_1, 'W') || str_contains($row_1, 'Q')) {
							$req_row = $row_1;
						}
						else{
							$ch_row = explode(' '.date('Y'),$row_1);
							$req_row = date('m',strtotime($ch_row[0]));
						}
					}
					else{
						$req_row = $row_1;
					}
			?>
					<tr>
						<?php if(!empty($summary['columns'])){
							$j = 0;
							foreach($summary['columns'] as $clm1){?>
								<td <?php if($summary['summary_cls'][$i][$summary['columns'][0]] =='Average' || $summary['summary_cls'][$i][$summary['columns'][0]] =='Total'){ echo 'class="font_wieght_bold"';}?>>
									<?php if($j != 0 && $j != $k  && $summary['summary_cls'][$i][$summary['columns'][0]] !='Average'&& $summary['summary_cls'][$i][$summary['columns'][0]] !='Total'){
										?>
										<a class="a_dashboard" href="javascript:void(0);" onclick="get_deal('<?php echo $clm1; ?>','<?php echo $req_row; ?>','<?php echo $summary['view_by']; ?>','<?php echo $summary['sel_measure']; ?>','<?php echo $date_range;?>','<?php echo $summary['summary_cls'][$i]['req_id'];?>')" data-toggle="modal" data-target="#summary_model" >
									<?php }?>
									<?php if(_l($clm1) == 'Stage' && $j ==0){
										if($summary['summary_cls'][$i][$clm1] == '0'){
											echo _l('open');
										}
										else if($summary['summary_cls'][$i][$clm1] == '1'){
											echo _l('own');
										}
										else if($summary['summary_cls'][$i][$clm1] == '2'){
											echo _l('lost');
										}
										else{
											echo $summary['summary_cls'][$i][$clm1];
										}
									}
									else{
										echo !empty($summary['summary_cls'][$i][$clm1])?$summary['summary_cls'][$i][$clm1]:0;
									}	
									?>
									<?php if($j != 0 && $j != $k  && $summary['summary_cls'][$i][$summary['columns'][0]] !='Average' && $summary['summary_cls'][$i][$summary['columns'][0]] !='Total'){?>
										</a>
									<?php }?>
								</td>
							<?php
								$j++;
							}
						} ?>
					</tr>
			<?php 
					$i++;
				}
			}
			else{
			?>
				<tr>
					<td colspan="<?php echo count($summary['columns']);?>" class="text-center"><?php echo _l('no_record');?></td>
				</tr>
			<?php
			}?>
		</tbody>
	</table>
<?php }?>
