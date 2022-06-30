<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<?php if(has_permission('pipelinestatus','','create')) { ?>
								<a href="<?php echo admin_url('pipelinestatus/save'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_pipelinestatus'); ?></a>
							<?php } else { ?>
								<p class="btn btn-info pull-left display-block"><?php echo _l('pipelinestatus'); ?></p>
							<?php } ?>
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading"/>
						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('name'),
							_l('order')
							),'pipelinestatuss',[],[
								'data-last-order-identifier' => 'pipelinestatus',
                                'data-default-order'         => get_table_last_order('pipelinestatus'),
							]); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->
<div class="modal fade" id="pipeline_stagesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title pipelinetitle"></span>
                </h4>
            </div>
            <?php echo form_open('admin/pipelinestatus/delete_pipelinestatus'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
						<div id="changestage">
							<div class="form-group">
								<h4>What would you like to do with the <b><span id="dealcount"></span></b> deals in this stage ?</h4>
							</div>
							<div class="form-group select-placeholder contactid input-group-select">
								<input type="radio" id="change_pipeline" name="selected_option" value="change"> &nbsp;<label for="contactid" class="control-label"><?php echo _l('movetoanother'); ?></label>
								<div class="dropdown bootstrap-select input-group-select show-tick bs3 bs3-has-addon" style="width: 100%;">
									<select id="pipeline_status" name="pipeline_status" class="selectpicker pipeline_status" data-actions-box="1" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98">
									</select>
								</div>
							</div>
							<div class="form-group">
								<input type="radio" id="delete_pipeline" name="selected_option" value="delete"> &nbsp;<label for="contactid" class="control-label"><?php echo _l('deletedeals'); ?></label>
							</div>
						</div>
						<div class="form-group deletestage">
							<h4>Are you sure do you want to delete this Pipeline stage.</h4>
							<input type="hidden" id="deletepipe" name="delete" value="">
						</div>
						<input type="hidden" id="pipeid" name="id" value="">
                  	</div>
              	</div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('delete'); ?></button>
                
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
	});
	function initKnowledgeBaseTablePipelines()
	{
		var KB_Pipelines_ServerParams = {};
		$.each($('._hidden_inputs._filters input'), function () {
			KB_Pipelines_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
		});
		$('._filter_data').toggleClass('hide');
		initDataTable('.table-pipelinestatuss', window.location.href, undefined, undefined, KB_Pipelines_ServerParams, [1, 'desc']);
	}
	function _delete_pipeline_status(id) {
		//alert(id);
		if (id) {
	
            $('#pipeline_status').html('').selectpicker('refresh');
			$.ajax({
				type: "GET",
				url: admin_url+'/pipelinestatus/getpipelinedeals',
				data: {id:id},
				dataType: 'json',
				success: function(msg) {
					if(msg.count > 0) {
						console.log(msg.length);
						$('#changestage').show();
						$('.deletestage').hide();
						$('.pipelinetitle').html('Delete '+msg.name+' stage');
						$('#pipeline_status').append(msg.pipelines);
						$('#pipeid').val(id);
						$('#dealcount').html(msg.count);
						$('#change_pipeline').attr('checked', 'checked');
						$('#deletepipe').val('');
						setTimeout(function() {
							$('#pipeline_status').selectpicker('refresh');
						}, 500);
						$('#pipeline_stagesModal').modal('show');
					} else {
						$('#changestage').hide();
						$('.deletestage').show();
						$('.pipelinetitle').html('Delete '+msg.name+' stage');
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