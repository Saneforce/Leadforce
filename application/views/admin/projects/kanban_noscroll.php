<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
	.kan_ban_block .panel-heading-bg{
		position: -webkit-sticky !important;
		position: sticky !important;
		top: 0;
		z-index: 2;
	}
</style>
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
							 //$list_url = admin_url('projects/index_list?pipelines='.$pipelines[0]['id'].'&member=&gsearch=');
							if(isset($_SESSION['pipelines'])) {
								$pid = $_SESSION['pipelines'];
							} else {
								$pid = $pipelines[0]['id'];
							}
							if(isset($_SESSION['member'])) {
								$mem = $_SESSION['member'];
							} else {
								$mem = get_staff_user_id();
							}
							if(isset($_SESSION['gsearch'])) {
								$gsearch = $_SESSION['gsearch'];
							} else {
								$gsearch = '';
							}
							 $list_url = admin_url('projects/index_list?pipelines=&member=&gsearch=');
							 $kanban_onscroll_url = admin_url('projects/kanban_noscroll?pipelines='.$pid.'&member='.$mem.'&gsearch='.$gsearch);
							 $kanban_url = admin_url('projects/kanbans?pipelines='.$pid.'&member='.$mem.'&gsearch='.$gsearch);
							 $forecast_url = admin_url('projects/kanbans_forecast?pipelines='.$pid.'&member='.$mem.'&gsearch='.$gsearch);
							 if(!is_admin(get_staff_user_id())) {
								//$list_url = admin_url('projects/index_list?pipelines='.$pipelines[0]['id'].'&member='.get_staff_user_id().'&gsearch=');
								$list_url = admin_url('projects/index_list?pipelines=&member='.get_staff_user_id().'&gsearch=');
								$kanban_onscroll_url = admin_url('projects/kanban_noscroll?pipelines='.$pid.'&member='.$mem.'&gsearch='.$gsearch);
								$kanban_url = admin_url('projects/kanbans?pipelines='.$pid.'&member='.$mem.'&gsearch='.$gsearch);
								$forecast_url = admin_url('projects/kanbans_forecast?pipelines='.$pid.'&member='.$mem.'&gsearch='.$gsearch);
							 } 
							  ?>
							  <a href="<?php echo $list_url; ?>" data-toggle="tooltip" title="<?php echo _l('projects'); ?>" class="btn btn-default"><i class="fa fa-list" aria-hidden="true"></i></a>
							  <!-- <a href="<?php echo admin_url('projects/gantt'); ?>" data-toggle="tooltip" title="<?php echo _l('project_gant'); ?>" class="btn btn-default"><i class="fa fa-align-left" aria-hidden="true"></i></a> -->
							  <a href="<?php echo $kanban_onscroll_url; ?>" data-toggle="tooltip" title="<?php echo _l('leads_switch_to_kanban_noscroll'); ?>" class="btn btn-default"><i class="fa fa-th" aria-hidden="true"></i></a>
							  <a href="<?php echo $kanban_url; ?>" data-toggle="tooltip" title="<?php echo _l('leads_switch_to_kanban'); ?>" class="btn btn-primary"><i class="fa fa-th-large" aria-hidden="true"></i></a>
							  <a href="<?php echo $forecast_url; ?>" data-toggle="tooltip" title="<?php echo _l('leads_switch_to_forecast'); ?>" class="btn btn-default"><i class="fa fa-line-chart" aria-hidden="true"></i></a>
							 
							 </div>
							
						</div>
						<div class="col-md-1 padding0">
							<h4><?php echo _l('filter_by'); ?></h4>
						</div>
						<?php echo form_open(admin_url('projects/kanbans'), array('method'=>'get','id'=>'ganttFiltersForm')); ?>
						<div class="col-md-2 pipeselect">
							<select class="selectpicker" data-none-selected-text="<?php echo _l('all'); ?>" name="pipelines" data-width="100%">
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
			            if(has_permission('projects','','view') /* && !empty($need_fields) && in_array("members", $need_fields)*/){ ?>
			            	<div class="col-md-2">
			            		<select class="selectpicker" data-live-search="true" data-title="<?php echo _l('project_member'); ?>" name="member" data-width="100%">
								<option value=""></option>
									<?php if(is_admin(get_staff_user_id()) || count($project_members) > 1) { ?> 
										<option value="" <?php if($selectedMember == ''){echo ' selected'; } ?>>All Members</option>
									<?php } ?>
			            			<?php foreach($project_members as $member) { ?>
			            				<option value="<?php echo $member['staff_id']; ?>"<?php if($selectedMember == $member['staff_id']){echo ' selected'; } ?>>
			            					<?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
			            				</option>
			            			<?php } ?>
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
			    <div class="kan-ban-tab kanban-scroll-view" id="kan-ban-tab">
                     <div class="row">
                        <div id="kanban-params">
                           <?php echo form_hidden('project_id',$this->input->get('project_id')); ?>
                        </div>
                        <div class="container-fluid">
                           <div id="kan-ban" class="kan_ban_block"></div>
                        </div>
                     </div>
                  </div>
			</div>
		</div>
	</div>
	<?php init_tail(); ?>
	<script>
		$(function(){
       project_kanban();
   });

   
// Edit status function which init the data to the modal
function edit_status(invoker, id) {
    $('#additional').append(hidden_input('id', id));
    $('#status input[name="name"]').val($(invoker).data('name'));
    $('#status .colorpicker-input').colorpicker('setValue', $(invoker).data('color'));
    $('#status input[name="statusorder"]').val($(invoker).data('order'));
    $('#status').modal('show');
    $('.add-title').addClass('hide');
}

// Form handler function for leads status
function manage_projects_statuses(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function (response) {
        window.location.reload();
    });
    return false;
}
$(document).ready(function() {
	  var busy = false;
var limit = <?php echo get_option('projects_kanban_limit');?>;
var offset = 0;
function displayRecords(lim, off) {
		var url =  admin_url+'projects/kanban_more_load';
        $.ajax({
          type: "GET",
          async: false,
          url: url,
          data: "limit=" + lim + "&offset=" + off,
          cache: false,
          beforeSend: function() {
            $("#loader_message").html("").hide();
            $('#loader_image').show();
          },
          success: function(html) {
			  var obj = JSON.parse(html);
			  <?php foreach ($statuses as $status) {?>
				$("#status_<?php echo $status['id'];?>").append(obj.status_<?php echo $status['id'];?>);
			  <?php }?>

            //$("#results").append(html);
            //$('#loader_image').hide();
            if (html == "") {
             // $("#loader_message").html('<button data-atr="nodata" class="btn btn-default" type="button">No more records.</button>').show()
            } else {
              //$("#loader_message").html('<button class="btn btn-default" type="button">Loading please wait...</button>').show();
            }
          }
        });
}
$(window).scroll(function() {
          // make sure u give the container id of the data to be loaded in.
          if ($(window).scrollTop() + $(window).height() > $("#kan-ban").height() && !busy) {
            busy = true;
            offset = limit + offset;
            displayRecords(limit, offset);
          }
});
$('#kan-ban-tab').scroll(function() {
          // make sure u give the container id of the data to be loaded in.
          if ($(window).scrollTop() + $(window).height() > $("#results").height() ) {
            busy = true;
            offset = limit + offset;
            displayRecords(limit, offset);
          }
})
});
	</script>
</body>
</html>
