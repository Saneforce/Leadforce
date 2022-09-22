<?php $colarrs = task_all_columns();
unset($colarrs['description']);
unset($colarrs['project_name']);
unset($colarrs['task_name']);
$custom_fields = get_table_custom_fields('task');
$cus_1 = $measures = array();
foreach($custom_fields as $cfkey=>$cfval){
	if($cfval['type'] == 'number'){
		$measures[$cfval['slug']] = array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
	}else{
		$customs[$cfval['slug']] = array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
	}
}
$custom_fields = get_table_custom_fields('customers');
foreach($custom_fields as $cfkey=>$cfval){
    if($cfval['type'] == 'number'){
		$measures[$cfval['slug']] =  array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
	}else{
		$customs[$cfval['slug']] =  array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
	}
} 
?>
<div class="row">
	<div class="col-md-12 m-bt-10">
		<?php echo form_open(admin_url('activity_reports/summary'),array('id'=>'deal_summary','method'=>'post')); ?>
			<input type="hidden" value="<?php echo $id;?>" name="summary_edit">
			<div class="col-md-1 mar-11">
					<label><?php echo _l('view_by');?></label>
					<input type="hidden" name="view_type" id="view_type12" value="<?php echo $summary['view_type'];?>">
			</div>
			<div class="<?php if($summary['view_type'] == 'date'){ echo 'col-md-3';}else{echo 'col-md-6';}?>" id="view_by_div">
				<select data-live-search="false" data-width="100%" class="ajax-search selectpicker" name="view_by" onchange="check_view_by(this)">
					<?php if(!empty($colarrs)){
						 foreach($colarrs as $ckey=>$cval){
							 if((!empty($need_fields) && in_array($ckey, $need_fields))){
						?>
								<option value="<?php echo $ckey; ?>" <?php if($summary['view_by'] == $ckey){ echo 'selected';}?>><?php echo _l($colarrs[$ckey]['ll']); ?></option>
						<?php 
							 }
						 }
					}if(!empty($customs)){
					?>
						<optgroup label="Custom Fields"  data-max-options="2">
					<?php
						foreach($customs as $ckey=>$cus_1){
						?>
							<option value="<?php echo $ckey; ?>" <?php if($summary['view_by'] == $ckey){ echo 'selected';}?>><?php echo _l($customs[$ckey]['ll']); ?></option>
						<?php
						}
						?>
						</optgroup>
					<?php
					}
					?>
				</select>
			</div>
			<div class="col-md-3" id="view_type" <?php if($summary['view_type'] != 'date'){?>style="display:none" <?php } ?> >
				<select data-live-search="true" data-width="100%" class="ajax-search selectpicker" name="date_range1">
					<option value="<?php echo _l('weekly');?>" <?php if($summary['date_range1'] ==  _l('weekly')){ echo 'selected';}?>><?php echo _l('weekly');?></option>
					<option value="<?php echo _l('monthly');?>" <?php if($summary['date_range1'] ==  _l('monthly')){ echo 'selected';}?>><?php echo _l('monthly');?></option>
					<option value="<?php echo _l('quarterly');?>" <?php if($summary['date_range1'] ==  _l('quarterly')){ echo 'selected';}?>><?php echo _l('quarterly');?></option>
					<option value="<?php echo _l('yearly');?>" <?php if($summary['date_range1'] ==  _l('yearly')){ echo 'selected';}?>><?php echo _l('yearly');?></option>
				</select>
			</div>
			<div class="col-md-1 mar-11">
					<label><?php echo _l('measure_by');?></label>
			</div>
			<div class="col-md-3">
				<?php $report_type = '';
				if(str_contains($report_name, 'Call Performance')){
					$report_type = 'Call';
				}
				else if(str_contains($report_name, 'Email Performance')){
					$report_type = 'Email';
				}
				else if(str_contains($report_name, 'Activity Performance')){
					$report_type = 'Activity';
				}
				?>
				<select data-live-search="true" data-width="100%" class="ajax-search selectpicker" name="sel_measure">
					<option value="<?php echo _l('number');?>" <?php if($summary['sel_measure'] ==  _l('number')){ echo 'selected';}?>><?php echo _l('number').' Of '.$report_type;?></option>
					
					<?php
					if(!empty($measures)){
						foreach($measures as $ckey=>$cus_1){
						?>
							<option value="<?php echo $ckey; ?>"  <?php if($summary['sel_measure'] == $ckey){ echo 'selected';}?>><?php echo _l($measures[$ckey]['ll']); ?></option>
						<?php
						}
					}
					?>
				</select>
			</div>
			<div class="col-md-1">
				<input type="submit" class="btn btn-primary" name="submit">
			</div>
		<?php echo form_close();?>
	</div>
</div>
<div class="row">
	<div class="modal fade" id="summary_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog" role="document" style="width:80%">
			<div id="overlay_deal1234" class="overlay_new" style="display: none;"><div class="spinner"></div></div>
			<div class="modal-content" >
				<div class="modal-header">
					<button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="edit-title" id="summary_head"></span>
					</h4>
				</div>
				
					<div class="modal-body" id="req_summary_data" style="overflow:scroll">
						
					</div>
					<div class="modal-footer">
						<button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
					</div>
			</div>
		</div>
	</div>
	<div class="col-md-12 m-bt-10">
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
						?>
							<th class="cur_thead"><?php echo _l($clm1);?></th>
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
												<a href="javascript:void(0);" onclick="get_deal('<?php echo $clm1; ?>','<?php echo $req_row; ?>','<?php echo $summary['view_by']; ?>','<?php echo $summary['sel_measure']; ?>','<?php echo $date_range;?>','<?php echo $summary['summary_cls'][$i]['req_id'];?>')" data-toggle="modal" data-target="#summary_model" >
												
											<?php }?>
											<?php if(_l($clm1) == 'Status' && $j ==0){
												if($summary['summary_cls'][$i][$clm1] == '1'){
													echo _l('upcoming');
												}
												else if($summary['summary_cls'][$i][$clm1] == '2'){
													echo _l('overdue');
												}
												else if($summary['summary_cls'][$i][$clm1] == '3'){
													echo _l('today');
												}
												else if($summary['summary_cls'][$i][$clm1] == '4'){
													echo _l('in_progress');
												}
												else if($summary['summary_cls'][$i][$clm1] == '5'){
													echo _l('completed');
												}
												else{
													echo $summary['summary_cls'][$i][$clm1];
												}
											}
											else if(_l($clm1) == 'Priority'  && $j == 0){
												if($summary['summary_cls'][$i][$clm1] == '1'){
													echo _l('task_priority_low');
												}
												else if($summary['summary_cls'][$i][$clm1] == '2'){
													echo _l('task_priority_medium');
												}
												else if($summary['summary_cls'][$i][$clm1] == '3'){
													echo _l('task_priority_high');
												}
												else if($summary['summary_cls'][$i][$clm1] == '4'){
													echo _l('task_priority_urgent');
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
	</div>
</div>
<style>
.mar-11{
	margin-top:8px;
}
.cur_thead{
	background-color:#f6f8fa;
}
th,td {
    white-space: nowrap;
}
.font_wieght_bold{
	font-weight:595
}
</style>