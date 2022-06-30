<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
	<div id="wrapper">
		<div class="content">
			<div class="row">
				<div class="col-md-12">
					<div class="panel_s">
						<div class="panel-body tc-content">
							<h4 class="bold no-margin"><?php echo _l('pipelinestatus_details'); ?></h4>
							<hr class="hr-panel-heading" />
							<div class="clearfix"></div>
							<div class="pipelinestatus">
								<div class="pipelinestatusname">
									<b><?php echo _l('name'); ?> </b><?php echo $pipelinestatus->name; ?>
								</div>
								<div class="pipelinestatusstatus">
									<b><?php echo _l('status'); ?> </b><?php echo $pipelinestatus->status; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php init_tail(); ?>
</body>
</html>