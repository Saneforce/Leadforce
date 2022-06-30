<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<?php if(has_permission('pipeline','','create')) { ?>
								<a href="<?php echo admin_url('pipeline/save'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_pipeline'); ?></a>
							<?php } else { ?>
								<p class="btn btn-info pull-left display-block"><?php echo _l('pipeline'); ?></p>
							<?php } ?>
							<div style="float:right;width:25%">
								 <?php echo form_open('admin/pipeline'); ?>
									<label for="status">Default Pipeline</label>
									<select id="default_pipeline_id" name="default_pipeline_id" class="selectpicker pipeline_status formpipe" data-actions-box="1" data-width="30%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98">
										<option value="" >None</option>
										<?php 
										if(!empty($all_pipelines)){ 
											foreach($all_pipelines as $all_pipeline1){
										?>
												<option value="<?php echo $all_pipeline1['id'];?>" <?php if($default_pipeline == $all_pipeline1['id']){echo 'selected';}?>><?php echo $all_pipeline1['name'];?></option>
										<?php 
											}
										} ?>
									</select>
									<input type="submit" value="<?php echo _l('ticket_form_submit'); ?>" class="btn btn-info" name="default_submit">
								<?php echo form_close(); ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading"/>
						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('pipeline_name'),
							_l('created_date')
							),'pipelines',[],[
								'data-last-order-identifier' => 'kb-pipelines',
                                'data-default-order'         => get_table_last_order('kb-pipelines'),
							]); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="pipeline_stagesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title pipelinetitle"></span>
                </h4>
            </div>
            <?php echo form_open('admin/pipeline/delete_pipeline'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
						<div id="changestage">
							<div class="form-group">
								<h4>What would you like to do with the <b><span id="dealcount"></span></b> deals in this Pipeline ?</h4>
							</div>
							<div class="form-group select-placeholder contactid input-group-select">
								<input type="radio" id="change_pipeline" name="selected_option" value="change"> &nbsp;<label for="contactid" class="control-label"><?php echo _l('movetoanotherpipeline'); ?></label>
								<div class="dropdown bootstrap-select input-group-select show-tick bs3 bs3-has-addon" style="width: 100%;">
									<label for="status">Pipeline</label>
                    				<div class="input-group" style="width: 100%;">
										<select id="pipeli_id" name="pipeline_id" class="selectpicker pipeline_status formpipe" data-actions-box="1" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98">
										</select>
									</div>
								</div>
								<div class="dropdown bootstrap-select input-group-select show-tick bs3 bs3-has-addon formstage" style="padding-top:10px; width: 100%;">
									<label for="status">Stage</label>
									<div class="input-group" style="width: 100%;">
										<select id="stage_id" name="status" class="selectpicker pipeline_status" data-actions-box="1" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98">
										</select>
									</div>
								</div>
							</div>
							<div class="form-group">
								<input type="radio" id="delete_pipeline" name="selected_option" value="delete"> &nbsp;<label for="contactid" class="control-label"><?php echo _l('deletedeals'); ?></label>
							</div>
						</div>
						<div class="form-group deletestage">
							<h4>Are you sure do you want to delete this Pipeline?</h4>
							<input type="hidden" id="deletepipe" name="delete" value="">
						</div>
						<input type="hidden" id="pipeid" name="id" value="">
                  	</div>
              	</div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('ticket_form_submit'); ?></button>
                
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

	</div>
	<?php init_tail(); ?>
	<script>
	$(function () {
		initKnowledgeBaseTablePipelines();

		if ($('#stage_id').length > 0) {
			$('.formstage .selectpicker').addClass("formnewstatus");
			$('.pipeli_id').selectpicker('destroy');
			$('.pipeli_id').html('').selectpicker('refresh');
			$('.stage_id').selectpicker('destroy');
			$('.stage_id').html('').selectpicker('refresh');
        }
        if ($('.form_assigned1 .selectpicker').length > 0) {
            $('.form_assigned1 .selectpicker').addClass("formassigned1");
        }
    
        if ($('#teamleader1').length > 0) {
            $('.form_teamleader1 .selectpicker').addClass("formteamleader");
        }
    
        $('#pipeli_id').change(function() {
            $('.formnewstatus').selectpicker('destroy');
            $('.formnewstatus').html('').selectpicker('refresh');
            var pipeid = $('#pipeli_id').val();
            $.ajax({
                url: admin_url + 'pipeline/changepipeline',
                type: 'POST',
                data: {
                    'pipeline_id': pipeid
                },
                dataType: 'json',
                success: function success(result) {
                    $('.formnewstatus').selectpicker('destroy');
                    $('.formnewstatus').html(result.statuses).selectpicker('refresh');
                   // $('.formstage').html(result.statuses).selectpicker('refresh');
                }
            });
        });
	});
	function initKnowledgeBaseTablePipelines()
	{
		var KB_Pipelines_ServerParams = {};
		$.each($('._hidden_inputs._filters input'), function () {
			KB_Pipelines_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
		});
		$('._filter_data').toggleClass('hide');
		initDataTable('.table-pipelines', window.location.href, undefined, undefined, KB_Pipelines_ServerParams, [1, 'desc']);
	}

	function _delete_pipeline_status(id) {
		//alert(id);
		if (id) {
			$('.formnewstatus').selectpicker('destroy');
			$('.formnewstatus').html('').selectpicker('refresh');
			$('.formpipe').selectpicker('destroy');
            $('.formpipe').html('').selectpicker('refresh');
			$.ajax({
				type: "GET",
				url: admin_url+'pipeline/getpipelinedeals',
				data: {id:id},
				dataType: 'json',
				success: function(msg) {
					if(msg.count > 0) {
						console.log(msg.length);
						$('#changestage').show();
						$('.deletestage').hide();
						$('.pipelinetitle').html('Delete '+msg.name+' Pipeline');
						$('#pipeli_id').append(msg.pipelines);
						$('#stage_id').append(msg.projects_status);
						$('#pipeid').val(id);
						$('#dealcount').html(msg.count);
						$('#change_pipeline').attr('checked', 'checked');
						$('#deletepipe').val('');
						setTimeout(function() {
							$('#pipeli_id').selectpicker('refresh');
							$('#stage_id').selectpicker('refresh');
						}, 500);
						$('#pipeline_stagesModal').modal('show');
					} else {
						$('#changestage').hide();
						$('.deletestage').show();
						$('.pipelinetitle').html('Delete '+msg.name+' Pipeline');
						$('#pipeid').val(id);
						$('#deletepipe').val('delete');
						$('#pipeline_stagesModal').modal('show');
					}
				}
			});
		}
	}
	</script>
</body>
</html>