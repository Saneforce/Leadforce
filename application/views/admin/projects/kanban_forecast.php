<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="panel_s">
				<div class="panel-body">
				
					<div class="row">
						<div class="col-md-3 padding0">
							<div class="_buttons">
							  <?php if(has_permission('projects','','create')){ ?>
								<a href="<?php echo admin_url('projects/project'); ?>" class="btn btn-info pull-left display-block mright5">
								  <?php echo _l('new_project'); ?>
								</a>
							  <?php }
							 
							if(isset($_SESSION['pipelines']) && !empty($_SESSION['pipelines'])) {
								$pid = $_SESSION['pipelines'];
							} else {
								$pid = $pipelines[0]['id'];
							}
							if(isset($_SESSION['member']) && !empty($_SESSION['member'])) {
								$mem = $_SESSION['member'];
							} else {
								$mem = get_staff_user_id();
							}
							if(isset($_SESSION['gsearch']) && !empty($_SESSION['gsearch'])) {
								$gsearch = $_SESSION['gsearch'];
							} else {
								$gsearch = '';
							}
							//pre($mem);
							$list_url = admin_url('projects/index_list?pipelines=&member=&gsearch=');
							$kanban_onscroll_url = admin_url('projects/kanban_noscroll?pipelines='.$pid.'&member='.$mem.'&gsearch='.$gsearch);
							$kanban_url = admin_url('projects/kanbans?pipelines='.$pid.'&member='.$mem.'&gsearch='.$gsearch);
							$forecast_url = admin_url('projects/kanbans_forecast?pipelines=&member='.$mem.'&gsearch='.$gsearch);
							if(!is_admin(get_staff_user_id())) {
							   //$list_url = admin_url('projects/index_list?pipelines='.$pipelines[0]['id'].'&member='.get_staff_user_id().'&gsearch=');
							   $list_url = admin_url('projects/index_list?pipelines=&member='.get_staff_user_id().'&gsearch=');
							   $kanban_onscroll_url = admin_url('projects/kanban_noscroll?pipelines='.$pid.'&member='.$mem.'&gsearch='.$gsearch);
							   $kanban_url = admin_url('projects/kanbans?pipelines='.$pid.'&member='.$mem.'&gsearch='.$gsearch);
							   $forecast_url = admin_url('projects/kanbans_forecast?pipelines=&member='.$mem.'&gsearch='.$gsearch);
							} 
							 ?>
							 <a href="<?php echo $list_url; ?>" data-toggle="tooltip" title="<?php echo _l('projects'); ?>" class="btn btn-default"><i class="fa fa-list" aria-hidden="true"></i></a>
							 <!-- <a href="<?php echo admin_url('projects/gantt'); ?>" data-toggle="tooltip" title="<?php echo _l('project_gant'); ?>" class="btn btn-default"><i class="fa fa-align-left" aria-hidden="true"></i></a> -->
							 <a href="<?php echo $kanban_onscroll_url; ?>" data-toggle="tooltip" title="<?php echo _l('leads_switch_to_kanban_noscroll'); ?>" class="btn btn-default"><i class="fa fa-th" aria-hidden="true"></i></a>
							 <a href="<?php echo $kanban_url; ?>" data-toggle="tooltip" title="<?php echo _l('leads_switch_to_kanban'); ?>" class="btn btn-default"><i class="fa fa-th-large" aria-hidden="true"></i></a>
							 <a href="<?php echo $forecast_url; ?>" data-toggle="tooltip" title="<?php echo _l('leads_switch_to_forecast'); ?>" class="btn btn-primary"><i class="fa fa-line-chart" aria-hidden="true"></i></a>
							
							</div>
						</div>
						<div class="col-md-1 padding0">
							<h4><?php echo _l('filter_by'); ?></h4>
						</div>
						<?php echo form_open(admin_url('projects/kanbans_forecast'), array('method'=>'get','id'=>'ganttFiltersForm')); ?>
						<div class="col-md-2 pipeselect">
							<select class="selectpicker" data-none-selected-text="<?php echo _l('all'); ?>" name="pipelines" id="pipeline_id" data-width="100%">
								<option value="">All Pipelines</option>
								<?php foreach($pipelines as $status){
									?>
									<option value="<?php echo $status['id']; ?>"<?php if($selected_statuses == $status['id']){echo ' selected';} ?>>
										<?php echo $status['name']; ?>
									</option>
								<?php } ?>
							</select>
						</div>
						<?php
			            /**
			             * Only show this filter if user has permission for projects view otherwise
			             * wont need this becuase by default this filter will be applied
			             */
						$fields = get_option('deal_fields');
						$need_fields = array();
						if(!empty($fields) && $fields != 'null'){
							$need_fields = json_decode($fields);
						}
			            if(has_permission('projects','','view')/* && !empty($need_fields) && in_array("members", $need_fields)*/){ ?>
			            	<div class="col-md-2">
			            		<select class="selectpicker" data-live-search="true" data-title="All Members" name="member" data-width="100%">
									<option value=""></option>
									<?php if(is_admin(get_staff_user_id()) || count($project_members) > 1) { ?> 
										<option value="" <?php if($selectedMember == ''){echo ' selected'; } ?>>All Members</option>
									<?php } ?>
			            			<?php foreach($project_members as $member) { ?>
			            				<option value="<?php echo $member['staff_id']; ?>"<?php if($selectedMember == $member['staff_id']){echo ' selected'; } ?>>
			            					<?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
			            				</option>
			            			<?php } ?>
			            		</option>
			            	</select>
			            </div>
					<?php } ?>
					<div class="col-md-2">
						<div class="form-group">
							<input type="search" name="gsearch" class="form-control input-sm" value="<?php echo (isset($gsearch)?$gsearch:''); ?>" placeholder="Search..."/>
						</div>
					</div>
			        <div class="col-md-1">
			        	<button type="submit" class="btn btn-default"><?php echo _l('apply'); ?></button>
			        </div>
			        <?php echo form_close(); ?>
			        <div class="clearfix"></div>

			        <hr />
				</div>
				<div class="row">
					
					<div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data" style="margin-right: 15px">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-gear" aria-hidden="true"></i>
						</button>
						<form action="" id="fc_filter" method="post">
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
							<input type="hidden" name="forecast_showby" id="forecast_showby" value="<?php echo (isset($_SESSION['forecast_showby'])) ? $_SESSION['forecast_showby'] : 'close date'; ?>">
							<input type="hidden" name="forecast_orderby" id="forecast_orderby" value="<?php echo (isset($_SESSION['forecast_orderby'])) ? $_SESSION['forecast_orderby'] : 'open deal'; ?>">
							<input type="hidden" name="forecast_intervel" id="forecast_intervel" value="<?php echo (isset($_SESSION['forecast_intervel'])) ? $_SESSION['forecast_intervel'] : 'quarter'; ?>">
							<input type="hidden" name="forecast_column" id="forecast_column" value="<?php echo (isset($_SESSION['forecast_column'])) ? $_SESSION['forecast_column'] : '4'; ?>">
							<input type="hidden" name="nav" id="nav" value="">
						</form>
						<ul class="dropdown-menu dropdown-menu-right width300">
						<li></li>	
							<li class="subheading">
								SHOW BY
								
							</li>
							<hr style="margin:1px 0px;">
							<li class="subactions <?php echo ($_SESSION['forecast_showby'] == 'created date') ? 'active' : ''; ?>">
								<a href="#" onclick="forecast_filter('showby','created date');">Deal Created</a>
							</li>
							<li class="subactions <?php echo ($_SESSION['forecast_showby'] == 'close date') ? 'active' : ''; ?>">
								<a href="#" onclick="forecast_filter('showby','close date');">Expected Close Date</a>
							</li>
							<hr style="margin:1px 0px;">
							<li class="subheading">
								ARRANGE BY
							</li>
							<hr style="margin:1px 0px;">
							<li class="subactions <?php echo ($_SESSION['forecast_orderby'] == 'won deal') ? 'active' : ''; ?>">
								<a href="#" onclick="forecast_filter('orderby','won deal');">Won deals first</a>
							</li>
							<li class="subactions <?php echo ($_SESSION['forecast_orderby'] == 'open deal') ? 'active' : ''; ?>">
								<a href="#" onclick="forecast_filter('orderby','open deal');">Open deals first</a>
							</li>
							<hr style="margin:1px 0px;">
							<li class="subheading">
								CHANGE INTERVEL
							</li>
							<hr style="margin:1px 0px;">
							<li class="subactions <?php echo ($_SESSION['forecast_intervel'] == 'month') ? 'active' : ''; ?>">
								<a href="#" onclick="forecast_filter('intervel','month');">Month</a>
							</li>
							<li class="subactions <?php echo ($_SESSION['forecast_intervel'] == 'quarter') ? 'active' : ''; ?>">
								<a href="#" onclick="forecast_filter('intervel','quarter');">Quarter</a>
							</li>
							<li class="subactions <?php echo ($_SESSION['forecast_intervel'] == 'week') ? 'active' : ''; ?>">
								<a href="#" onclick="forecast_filter('intervel','week');">Week</a>
							</li>
							<hr style="margin:1px 0px;">
							<li class="subheading">
								NUMBER OF COLUMNS
							</li>
							<hr style="margin:1px 0px;">
							<li class="subactions <?php echo ($_SESSION['forecast_column'] == 3) ? 'active' : ''; ?>">
								<a href="#" onclick="forecast_filter('columns','3');">3 Columns</a>
							</li>
							<li class="subactions <?php echo ($_SESSION['forecast_column'] == 4) ? 'active' : ''; ?>">
								<a href="#" onclick="forecast_filter('columns','4');">4 Columns</a>
							</li>
							<li class="subactions <?php echo ($_SESSION['forecast_column'] == 5) ? 'active' : ''; ?>">
								<a href="#" onclick="forecast_filter('columns','5');">5 Columns</a>
							</li>
						</ul>
					</div>
					<div class="pull-right">
						<ul class="pager">
							<li><a href="#" title="Jump Backward" onclick="forecast_filter('forecast_nav','backward');"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></li>
							<li><a href="#" title="Previous" onclick="forecast_filter('forecast_nav','prev');"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
							<li><a href="#" onclick="forecast_filter('forecast_nav','today');"> Today </a></li>
							<li><a href="#" title="Next" onclick="forecast_filter('forecast_nav','next');"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
							<li><a href="#" title="Jump Forward" onclick="forecast_filter('forecast_nav','forward');"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a></li>
						</ul>
					</div>
				</div>
				<div class="clearfix row"><hr /></div>

			        
				<div class="kan-ban-tab" id="kan-ban-tab" style="padding:0px 16px;">
				
                     <div class="row">
                        <div id="kanban-params">
                           <?php echo form_hidden('project_id',$this->input->get('project_id')); ?>
                        </div>
                        <div class=" projects-kan-ban">
                           <div id="kan-ban"></div>
                        </div>
                     </div>
                  </div>
			</div>
		</div>
	</div>
	<?php init_tail(); ?>
	<script>
		$(function(){
			project_kanban_forecast();
   });

   function forecast_filter(action, values) {
		if(action == 'showby') {
			$('#forecast_showby').val(values);
		}
		if(action == 'orderby') {
			$('#forecast_orderby').val(values);
		}
		if(action == 'intervel') {
			$('#forecast_intervel').val(values);
			$('#nav').val('start');
		}
		if(action == 'columns') {
			$('#forecast_column').val(values);
		}
		if(action == 'forecast_nav') {
			if(values == 'today') {
				$('#nav').val('start');
			}
			if(values == 'next') {
				$('#nav').val(1);
			}
			if(values == 'prev') {
				$('#nav').val(-1);
			}
			if(values == 'forward') {
				$('#nav').val('forward');
			}
			if(values == 'backward') {
				$('#nav').val('backward');
			}
		}
		
		$("#fc_filter").submit();
   } 
   
// Edit status function which init the data to the modal
function edit_status(invoker, id) {
    $('#additional').append(hidden_input('id', id));
    $('#status input[name="name"]').val($(invoker).data('name'));
    $('#status .colorpicker-input').colorpicker('setValue', $(invoker).data('color'));
    $('#status input[name="statusorder"]').val($(invoker).data('order'));
    $('#status').modal('show');
    $('.add-title').addClass('hide');
}

function shownoprob(id) {
	$('#head_'+id+' .show_noprob').show();
	$('#head_'+id+' .show_prob').hide();
}

function showprob(id) {
	$('#head_'+id+' .show_noprob').hide();
	$('#head_'+id+' .show_prob').show();
}
	</script>
		<script>
$(function() {
	$(".panel-body").click(function(){
		$('.ui-sortable').removeClass('ui-sortable');
		$('.ui-sortable-handle').removeClass('ui-sortable-handle');
	});
    if ($('#status').length > 0) {
        $('.form_status .selectpicker').addClass("formstatus");
    }
    if ($('.form_assigned .selectpicker').length > 0) {
        $('.form_assigned .selectpicker').addClass("formassigned");
    }

    if ($('#teamleader').length > 0) {
        $('.form_teamleader .selectpicker').addClass("formteamleader");
    }

    $('#pipeline_id').change(function() {
        $('.formstatus').selectpicker('destroy');
        $('.formstatus').html('').selectpicker('refresh');

        $('.formassigned').selectpicker('destroy');
        $('.formassigned').html('').selectpicker('refresh');

        $('.formteamleader').selectpicker('destroy');
        $('.formteamleader').html('').selectpicker('refresh');

		var pipeline_id = $('#pipeline_id').val();
		if(pipeline_id) {
			$.ajax({
				url: admin_url + 'leads/changepipeline',
				type: 'POST',
				data: {
					'pipeline_id': pipeline_id
				},
				dataType: 'json',
				success: function success(result) {
					$('.formstatus').selectpicker('destroy');
					$('.formstatus').html(result.statuses).selectpicker('refresh');


					$('.formassigned').selectpicker('destroy');
					$('.formassigned').html(result.teammembers).selectpicker('refresh');

					$('.formteamleader').selectpicker('destroy');
					$('.formteamleader').html(result.teamleaders).selectpicker('refresh');

				}
			});
		} else {
			$.ajax({
						url: admin_url + 'leads/selectAllpipeline',
						type: 'POST',
						data: {
							'pipeline_id': ''
						},
						dataType: 'json',
						success: function success(result) {
							
							$('.formassigned').selectpicker('destroy');
							$('.formassigned').html(result.teammembers).selectpicker('refresh');

						}
					});
		}
    });
    var pipelines_count = <?php echo count((array)$pipelines); ?>;
    if(pipelines_count == 1){
        $('#pipeline_id option[value="<?php echo $pipelines[0]['id']; ?>"]').attr('selected', 'selected')
        $('#pipeline_id').selectpicker('refresh');
        $('#pipeline_id').trigger('change');
	}
	
		
});
</script>
</body>
</html>

<style>
.projects-kan-ban {
    min-width: auto !important;
}
.kan-ban-col {
    width: calc(100%/<?php echo $_SESSION['forecast_column']; ?>);
}
</style>
<style>
.forecast-heading {
	color:black;
}
.heading-font {
	margin:0px;font-size: 18px;font-weight: 500;
}
.panel-heading-bg .heading.pointer{
white-space: nowrap;
width: 55% !important;
overflow: hidden;
text-overflow: ellipsis;
display: block;
float: left;
margin: -3px 0 0 3px;
}
.panel-heading-bg .pointer{
  float: left;
}
.panel-heading-bg.primary-bg {
    height: 70px;
}
.projects-kan-ban{
  background-color: #e3e8ee;
  min-height: 500px;
}
.pager {
	margin:1px 8px;
}
li.subheading {
    font-weight: 500;
    padding: 10px;
}
li.subactions a {
    padding: 6px 27px !important;
}

.show_noprob {
  display: none;
}
 ul.dropdown-menu li:first-child {
	display: block !important;
}   

</style>