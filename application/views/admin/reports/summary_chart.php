<div class="panel_s project-menu-panel" style="margin-bottom:0px;">
	<div class="panel-body">
		<div class="horizontal-tabs">
			<ul class="nav nav-pills">
				<li class="<?php if(empty($cur_tab2) || $cur_tab2 ==1){ echo ' active';}?>">
					<a data-toggle="pill" href="#pie_chart" onclick="tab_summary_1('1');"><i class="fa fa-pie-chart"></i></a>
				</li>
				<li class="<?php if(!empty($cur_tab2) && $cur_tab2 ==2){ echo ' active';}?>">
					<a data-toggle="pill" href="#column_chart" onclick="tab_summary_1('2');"><i class="fa fa-bar-chart"></i></a>
				</li>
				<li class="<?php if(!empty($cur_tab2) && $cur_tab2 ==3){ echo ' active';}?>">
					<a data-toggle="pill" href="#horizontal_chart" onclick="tab_summary_1('3');"><i class="fa fa-bars"></i></a>
				</li>
				<li class="<?php if(!empty($cur_tab2) && $cur_tab2 ==4){ echo ' active';}?>">
					<a data-toggle="pill" href="#score_card" onclick="tab_summary_1('4');"><i class="fa fas fa-tasks"></i></a>
				</li>
			</ul>
		</div>
	</div>
</div>
<div class="tab-content">
	<div id="pie_chart"  class="tab_summary tab-pane fade <?php if(empty($cur_tab2) || $cur_tab2 ==1){ echo ' in active';}?> ">
		<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('s_chart',_l('projects')); ?>">
			<div class="row">
				<div class="col-md-12">
					<div class="panel_s">
						<div class="panel-body padding-10">
							<!-- <div class="widget-dragger"></div> -->
							<p class="padding-5">
								<?php echo _l('summary'); ?>
							</p>
							<hr class="hr-panel-heading-dashboard">
							<div class="relative" style="height:490px">
								<canvas class="chart" height="250" id="report_pie_chart"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="column_chart"  class="tab_summary tab-pane fade <?php if(!empty($cur_tab2) && $cur_tab2 ==2){ echo ' in active';}?> ">
		<div class="widget"  data-name="<?php echo _l('s_chart',_l('projects')); ?>">
			<div class="row">
				<div class="col-md-12">
					<div class="panel_s">
						<div class="panel-body padding-10">
							<p class="padding-5">
								<?php echo _l('summary'); ?>
							</p>
							<hr class="hr-panel-heading-dashboard">
							<div class="relative" style="height:490px">
								<canvas class="chart" height="250" id="report_bar_chart"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="horizontal_chart"  class="tab_summary tab-pane fade <?php if(!empty($cur_tab2) && $cur_tab2 ==3){ echo ' in active';}?>  ">
		<div class="widget"  data-name="<?php echo _l('s_chart',_l('projects')); ?>">
			<div class="row">
				<div class="col-md-12">
					<div class="panel_s">
						<div class="panel-body padding-10">
							<p class="padding-5">
								<?php echo _l('summary'); ?>
							</p>
							<hr class="hr-panel-heading-dashboard">
							<div class="relative" style="height:490px">
								<canvas class="chart" height="250" id="report_horizontal_chart"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php 
	$last_key	= $summary['columns'][count($summary['columns'])-1];
	$cl_las_key = count($summary['summary_cls'])-1;
	$req_out	= $summary['summary_cls'][$cl_las_key][$last_key];
	?>
	<div id="score_card"  class="tab_summary tab-pane fade <?php if(!empty($cur_tab2) && $cur_tab2 ==4){ echo ' in active';}?>  ">
		<div class="widget"  data-name="<?php echo _l('s_chart',_l('projects')); ?>">
			<div class="row">
				<div class="col-md-12">
					<div class="panel_s">
						<div class="panel-body padding-10">
							<p class="padding-5">
								<?php echo _l('score_card'); ?>
							</p>
							<hr class="hr-panel-heading-dashboard">
							<div class="relative text-center bold font-25">
								<p> <?php echo $req_out;?></p>
								<p>
									<?php 
									echo ($summary['sel_measure'] == 'Number')?$summary['sel_measure'].' Of '._l('task'):$summary['sel_measure'];
									?>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
.font-25{
	font-size:25px;
}
</style>