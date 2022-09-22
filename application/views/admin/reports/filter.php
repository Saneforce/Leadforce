<?php 
if($report_page == 'deal'){
	echo form_open(admin_url('reports/save_filter_report'),array('id'=>'save_filter_report'));
}
else{
	echo form_open(admin_url('activity_reports/save_filter_report'),array('id'=>'save_filter_report'));
}
 ?>
	<div class="panel_s project-menu-panel" style="margin-top:-1px;">
		<div class="panel-body">
			<div class="horizontal-tabs">
				<div class="">
					<?php $filters = (empty($filters)?array():$filters);?>
					<input type="hidden" id="cur_num" value="<?php echo count($filters);?>">
					<input type="hidden" name="cur_id121" value="<?php echo $id;?>">
					<input type="hidden" name="filter_tab" value="<?php echo (!empty($_REQUEST['filter_tab']))?$_REQUEST['filter_tab']:"1";?> id="filter_tab">
					<div class="row" id="ch_ids">
						<?php 
						if(!empty($filters)){
							$i1 = 1;$i2 =0;
							foreach($filters as $key => $filter1){
							?>
								<div  class="col-md-12 m-bt-10">
									<div  class="col-md-2" >
										<select data-live-search="true" class="selectpicker" id="filter_<?php echo $i1;?>" onchange="change_filter(this)">
											<?php $cur_val ='';
											if(!empty($all_clmns)){ ?>
												<optgroup label="Deal Master" data-max-options="2">
												<?php foreach ($all_clmns as $key1 => $all_val1){
													if(($key1==$filter1 || !in_array($key1, $filters)) &&  in_array($key1, $need_fields) ){ 
													?>
													<option value="<?php echo $key1;?>" <?php if($key1==$filter1){ echo 'selected';}?>><?php echo _l($all_val1['ll']);?></option>
												<?php 
													}
												}?>
												</optgroup>
											<?php }
											if(!empty($cus_flds)){?>
												<optgroup label="Custom Fields"  data-max-options="2">
													<?php foreach ($cus_flds as  $key => $cus_fld1){
														if($key==$filter1 || !in_array($key, $filters)){ 
														?>
														<option value="<?php echo $key;?>" <?php if($key == $filter1){echo 'selected';}?>><?php echo $cus_fld1['ll'];?></option>
													<?php }
													}
													?>
												</optgroup>
											<?php }?>
										</select>
									</div>
									<div  class="col-md-6" >
										<div id="ch_dr_<?php echo $i1;?>"></div>
									</div>
								</div>
							<?php 
							$i2++;
							$i1++;
							}
						}?>
					</div>
					<div class="row">
						<div class="col-md-12">
							<input type="submit" value="<?php echo _l('apply_filter');?>" class="btn btn-primary" style="float:right;">
							<a href="javascript:void(0)" onclick="add_filter()"><i class="fa fa-plus-circle" style="font-size:xx-large"></i></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php echo form_close();?>