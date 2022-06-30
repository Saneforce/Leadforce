<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<?php if(has_permission('DealLossReasons','','create')) { ?>
								<a href="<?php echo admin_url('DealLossReasons/save'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_deallossreasons'); ?></a>
							<?php } else { ?>
								<p class="btn btn-info pull-left display-block"><?php echo _l('DealLossReasons'); ?></p>
							<?php } ?>
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading"/>
						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('deallossreasons_name'),
							_l('created_date')
							),'deallossreasonss',[],[
								'data-last-order-identifier' => 'kb-deallossreasonss',
                                'data-default-order'         => get_table_last_order('kb-deallossreasonss'),
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
		initKnowledgeBaseTabledeallossreasonss();
	});
	function initKnowledgeBaseTabledeallossreasonss()
	{
		var KB_deallossreasonss_ServerParams = {};
		$.each($('._hidden_inputs._filters input'), function () {
			KB_deallossreasonss_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
		});
		$('._filter_data').toggleClass('hide');
		initDataTable('.table-deallossreasonss', window.location.href, undefined, undefined, KB_deallossreasonss_ServerParams, [1, 'desc']);
	}
	</script>
</body>
</html>