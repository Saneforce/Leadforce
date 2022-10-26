<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<?php if(has_permission('DealRejectionReasons','','create')) { ?>
								<a href="<?php echo admin_url('DealRejectionReasons/save'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_DealRejectionReasons'); ?></a>
							<?php } else { ?>
								<p class="btn btn-info pull-left display-block"><?php echo _l('DealRejectionReasons'); ?></p>
							<?php } ?>
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading"/>
						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('dealrejectionreasons_name'),
							_l('created_date')
							),'dealrejectionreasonss',[],[
								'data-last-order-identifier' => 'kb-dealrejectionreasonss',
                                'data-default-order'         => get_table_last_order('kb-dealrejectionreasonss'),
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
		initKnowledgeBaseTabledealrejectionreasonss();
	});
	function initKnowledgeBaseTabledealrejectionreasonss()
	{
		var KB_dealrejectionreasonss_ServerParams = {};
		$.each($('._hidden_inputs._filters input'), function () {
			KB_dealrejectionreasonss_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
		});
		$('._filter_data').toggleClass('hide');
		initDataTable('.table-dealrejectionreasonss', window.location.href, undefined, undefined, KB_dealrejectionreasonss_ServerParams, [1, 'desc']);
	}
	</script>
</body>
</html>