<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(!empty($types)){
	$i1 = $i2 =0;
	foreach($types as $type1){ 
		$report_id	=	$req_data[$i1]['id'];
		$filters	=	get_report_filter($report_id);
		if($i2 != $sorts[$i1]){
			for($i2;$i2 < $sorts[$i1];$i2++){
			?>
				<div class="col-md-3 <?php echo 'check_'.$i2;?>" data-container="<?php echo $i2;?>" style="display:contents"></div>
			<?php
			}
		}
?>	
		<div class="col-md-3   <?php echo 'check_'.$i2;?> " data-container="<?php echo $i2;?>" style="<?php if(!empty($width[$i1])){ echo 'width:'.$width[$i1].'px;';} ?>">
			<div data-ids="<?php echo 'check_'.$i2;?>" class=" widget padding-10 check_widget <?php if(!is_staff_member() && empty($public)){echo ' hide';} ?>" id="<?php echo $dashboard_ids[$i1];?>" data-name="<?php echo _l('s_chart',_l('leads')); ?>" style="<?php if(!empty($height[$i1])){ echo 'height:'.$height[$i1].'px;';}?>">
			   <?php if(is_staff_member() || !empty($public)){ ?>
					<div class="row">
						<div class="col-md-12">
							<div class="panel_s">
								<div class="panel-body padding-10">
								<?php if(empty($public)){?>
									<div class="widget-dragger"></div>
								<?php }?>
									<p class="padding-5">
										<span title="<?php echo $names[$i1].' ('.$report_types[$i1].')'; ?>" class="font_wieght_bold">
											<?php 
											if(strlen($names[$i1].' ('.$report_types[$i1].')')>18)
												echo substr($names[$i1].' ('.$report_types[$i1].')',0,18).'...';
											else
												echo $names[$i1].' ('.$report_types[$i1].')';
											?>
										<span>
										<?php if(empty($public)){?>
											<a href="javascript:void(0);" class="a_padding" onclick="refresh_chart('chart_<?php echo $i1;?>','<?php echo $dashboard_ids[$i1];?>','<?php echo $tabs1[$i1];?>','<?php echo $tabs2[$i1];?>','<?php echo $i;?>')">
												<i class="fa fa-refresh" aria-hidden="true"></i>
											</a>
											<div class="inline-block label" style="color:black">
												<div class="dropdown " style="position: absolute;margin-top:-5px;right: 14%;">
													<a href="javascript:void(0);" class="dropdown-toggle text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" >
														<span data-toggle="tooltip">
															<?php echo _l('more');?> <i class="fa fa-caret-down" aria-hidden="true"></i>
														</span>
													</a>
													<ul class="dropdown-menu dropdown-menu-left">
														<li>
														<a href="<?php echo admin_url('reports/edit_deal_report/'.$rep_ids[$i1].'?type='.$type1);?>"><?php echo _l('edit');?></a>
														</li>
														<li>
															<a href="<?php echo admin_url('dashboard/delete_dashboard/'.$dashboard_ids[$i1]);?>" onclick="if (confirm('<?php echo _l('check_delete');?>')){return true;}else{event.stopPropagation(); event.preventDefault();};">
																<?php echo _l('delete');?>
															</a>
														</li>
													</ul>
												</div>
											</div>
										<?php }?>
									</p>
									<?php 
									$cur_cnt = 1;$req_filter = '';$all_filters = array(); 
									if(!empty($filters)){
										echo '<p class="all_filters1">';
										foreach($filters as $filter_1){
											$req_filter1 = $filter_1['filter_1'];
											$filter_1['filter_3'] = get_filter_name($filter_1,$type1);
									?>
											<?php 
											if($cur_cnt<=1){
												if(empty($dashoard_data[0]['period'])){
													$req_filter .= ' '._l($filter_1['filter_1']).' '._l($filter_1['filter_2']).' '._l($filter_1['filter_3']).',';
												}
												else{
													$req_filter .= ' '._l($filter_1['filter_1']).' '._l($dashoard_data[0]['period']).' '._l($dashoard_data[0]['date1']).' '._l($dashoard_data[0]['date2']).',';
												}
											}
											if(check_activity_date($filter_1['filter_1'])){
												if(empty($dashoard_data[0]['period'])){
													$all_filters[] = ' '._l($filter_1['filter_1']).' '._l($filter_1['filter_2']).' '._l($filter_1['filter_3']).': '._l($filter_1['filter_4']).' '._l($filter_1['filter_5']);
												}
												else{
													$all_filters[] = ' '._l($filter_1['filter_1']).' '._l($dashoard_data[0]['period']).': '._l($dashoard_data[0]['date1']).' '._l($dashoard_data[0]['date2']);
												}
											}
											else{
												$all_filters[] = ' '._l($filter_1['filter_1']).' '._l($filter_1['filter_2']).': '._l($filter_1['filter_3']);
											}
											$cur_cnt++;
										}
										?>
										<div class="dropdown1 " style=" position: absolute;top: 8%;">
										<a href="javascript:void(0)" class="dropdown-toggle text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
											<span title="<?php echo rtrim($req_filter,",");?>" class="font_wieght_bold">
										<?php
											if(strlen(rtrim($req_filter,","))>25){
												echo substr(rtrim($req_filter,","),0,25).'...';
											}
											else
												echo rtrim($req_filter,",");
										?>
											</span>
										<?php
										if(!empty($all_filters)){
										?>
											</a>
												<ul class="dropdown-menu dropdown-menu-left">
													<?php foreach($all_filters as $all_filter12){?>
														<li class="drop_li"><?php echo $all_filter12;?></li>
													<?php }?>
												</ul>
											
										<?php }?>
										</div>
										</p>
										<?php
									}?>
									<hr class="hr-panel-heading-dashboard" >
									<div class="relative" style="height:300px;<?php if($tabs1[$i1] == 1){?>overflow:scroll;<?php }?>" id="div_<?php echo $dashboard_ids[$i1];?>">
										<?php if($tabs1[$i1] == 3 && ($tabs2[$i1] == 0 || $tabs2[$i1] == 1)){?>
											<canvas class="chart"  id="report_pie_chart_<?php echo $i1;?>"></canvas>
										<?php }else if($tabs1[$i1] == 3 && $tabs2[$i1] == 2){?>
											<canvas class="chart"  id="report_bar_chart_<?php echo $i1;?>"></canvas>
										<?php }else if($tabs1[$i1] == 3 && $tabs2[$i1] == 3){?>
											<canvas class="chart"  id="report_horizontal_chart_<?php echo $i1;?>"></canvas>
										<?php }else if($tabs1[$i1] == 3 && $tabs2[$i1] == 4){
										?>
											<div class="relative text-center bold font-20">
												<p class="bold"> <?php echo score_report($summary[$i1]);?></p>
												<p class="bold">
													<?php 
													echo ($summary[$i1]['sel_measure'] == 'Number')?$summary[$i1]['sel_measure'].' Of '._l('task'):$summary[$i1]['sel_measure'];
													?>
												</p>
											</div>
										<?php
										}else if($tabs1[$i1] == 1){
											$data['summary'] = $summary[$i1];
											$this->load->view('admin/reports/summary_view',$data);
										}?>
										
									</div>
								</div>
							</div>
						</div>
					</div>
			   <?php } ?>
			</div>
		<?php //if(!isset($sorts1[$i1+1]) || $sorts1[$i1+1] != $sorts1[$i1]){?>
			</div> 
		<?php $i2++;
		//}
		$i1++;		
	}
}else{
	echo '<center><h1>'._l('no_record').'</h1></center>';
}?>	
<style>
.widget-dragger{
	left: unset !important;
	right: 25px;
}
.widget-dragger:before{
	content:"\f0b2";
}
.btn_bg{
	background-color:#61c786 !important;
}
.w_100{
	width:100%
}
.ui-widget-header{
	color:#323a45;
}
.filter_date{
	background-color: #fff!important;
    color: #555 !important;
}
ul.dropdown-menu li:first-child{
	display:block;
}
.a_padding{
	padding-left:15px;
}
.a_dashboard{
	color: #323a45 !important;
	pointer-events:none;
}
.drop_li{
	white-space: nowrap;
    padding: 10px;
}
.all_filters1{
	position:absolute;top:8%
}
.font_wieght_bold{
	font-weight:bold;
}
th.cur_thead{
	font-weight:bold !important;
}
.rm_width{
	width:unset !important;
}
.font-20{
	font-size:20px;
	top:30%
}
</style>
<script>
function load_public(a,b){
  document.getElementById('overlay_deal_public').style.display = '';
  var data = {cur_id:a,dash_id:b};
  $('#cur_report').val(a);
	var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'dashboard/load_public',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#public_all').html(msg);
			document.getElementById('overlay_deal_public').style.display = 'none';
		}
	});
}
function add_public_link(a,b){
	var data = {req_val:a,d_id:b};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'dashboard/public_link',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#public_all').html(msg);
		}
	});
}
function check_publick(a){
	 document.getElementById('overlay_deal12').style.display = '';
	var data = {req_val:a};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'dashboard/check_publick',
		data: {req_val:a},
		dataType: '',
		success: function(msg) {
			$('#ch_name12').val(msg);
			$('#link_id').val(a);
			document.getElementById('overlay_deal12').style.display = 'none';
		}
	});
}
function delete_link(a){
	document.getElementById('overlay_deal_public').style.display = '';
	var b = $('#dashboard_id1').val();
	var data = {req_val:a,dash_id:b};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'dashboard/delete_link',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#public_all').html(msg);
			document.getElementById('overlay_deal_public').style.display = 'none';
		}
	});
}
function myFunction(a) {
  /* Get the text field */
  var copyText = document.getElementById("name_"+a);
  /* Select the text field */
  copyText.select();
  copyText.setSelectionRange(0, 99999); /* For mobile devices */

  /* Copy the text inside the text field */
  document.execCommand( 'copy' );
  alert_float('success', 'Link Copied Successfully');
  
  /* Alert the copied text */
}
function change_2_filter(a){
	var cur_val = a.value;
	var data = {cur_val:cur_val};
	var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'dashboard/get_filter',
		data: data,
		dataType: '',
		success: function(res_json) {
			var res = JSON.parse(res_json);
			if(cur_val!='custom_period'){
				$('#start_date_edit').val(res.start_date);
				$('#end_date_edit').val(res.end_date);
			}
			if(cur_val!=''){
				$("#period").removeClass("w_100");
				$("#period_date").show();
			}
			else{
				$("#period").addClass("w_100");
				$("#period_date").hide();
			}
		}
	});
	
}
function refresh_chart(a,b,c,tab_id,cur_id){
	var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'dashboard/refresh_chart',
		data: {dashboard_id:b,cur_id:cur_id},
		dataType: '',
		success: function(res_json) {
			var res = JSON.parse(res_json);
			if(c == 1){
				$('#div_'+b).html(res.summary);
			}
			else{
				if(tab_id ==1){
					var pie_chart = $('#report_pie_'+a);
					if(pie_chart.length > 0){
						var cur_chart =  new Chart(pie_chart, {
							type: 'pie',
							data: {"labels":res.labels,"datasets":[{"data":res.data,"backgroundColor":res.color,"label":res.label}]},
							options: {
								responsive:true,
								legend: {
									display: true
								},
								maintainAspectRatio:false,
						   }
					   });
					}
				}
				else if(tab_id ==2){
					var bar_chart = $('#report_bar_'+a);
					if(bar_chart.length > 0){
						new Chart(bar_chart, {
							type: 'bar',
							data: {"labels":res.labels,"datasets":[{"data":res.data,"backgroundColor":res.color,"label":res.label}]},
							options:{
								responsive:true,
								legend: {
									display: false
								},
								maintainAspectRatio:false,
								scales: {
									xAxes: [{
									  scaleLabel: {
										display: true,
										labelString: res.req_x
									  }
									}],
									yAxes: [{
									  scaleLabel: {
										display: true,
										labelString: res.req_y
									  }
									}],
								}
							}
						});
					}
				}
				else if(tab_id ==3){
					var horizontalBar = $('#report_horizontal_'+a);
					if(horizontalBar.length > 0){
						new Chart(horizontalBar, {
							type: 'horizontalBar',
							data: {"labels":res.labels,"datasets":[{"data":res.data,"backgroundColor":res.color,"label":res.label}]},
							options:{
								responsive:true,
								legend: {
									display: false
								},
								maintainAspectRatio:false,
								scales: {
									yAxes: [{
									  scaleLabel: {
										display: true,
										labelString: res.req_y
									  }
									}],
									xAxes: [{
									  scaleLabel: {
										display: true,
										labelString: res.req_x
									  }
									}]
								}
							}
						}); 
					}
				}
			}
		}
	});
}
</script>