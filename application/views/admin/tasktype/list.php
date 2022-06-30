<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<?php if(has_permission('tasktype','','create')) { ?>
								<a href="<?php echo admin_url('tasktype/save'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_tasktype'); ?></a>
							<?php } else { ?>
								<p class="btn btn-info pull-left display-block"><?php echo _l('tasktype'); ?></p>
							<?php } ?>
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading"/>
						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('name'),
							_l('status')
							),'tasktypes',[],[
								'data-last-order-identifier' => 'tasktype',
                                'data-default-order'         => get_table_last_order('tasktype'),
							]); ?>
						</div>
					</div>
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
		initDataTable('.table-tasktypes', window.location.href, undefined, undefined, KB_Pipelines_ServerParams, [1, 'desc']);
	}
	</script>
</body>
</html>