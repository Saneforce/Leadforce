<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<!-- 
							<a href="<?php echo admin_url('staff'); ?>" class="btn btn-default pull-left mleft5"><?php echo _l('staff'); ?></a>
							<a href="<?php echo admin_url('roles'); ?>" class="btn btn-default pull-left mleft5"><?php echo _l('acs_roles'); ?></a> -->
							<a href="<?php echo admin_url('staff'); ?>" class="btn btn-default pull-left mleft5"><?php echo _l('staff'); ?></a>
							<a href="<?php echo admin_url('roles'); ?>" class="btn btn-default pull-left mleft5"><?php echo _l('acs_roles'); ?></a>
							<a href="<?php echo admin_url('designation'); ?>" class="btn btn-info pull-left mleft5"><?php echo _l('acs_designation'); ?></a>
							<a href="<?php echo admin_url('staff/hierarchy'); ?>" class="btn btn-default pull-left mleft5"><?php echo _l('acs_hierarchy'); ?></a>
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading" />
						
						<div class="clearfix"></div>
						<div style="clear:both; margin-bottom:45px;"><a href="<?php echo admin_url('designation/designations'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_designation'); ?></a></div>
						<?php render_datatable(array(
							_l('designation_dt_name'),
							_l('role'),
							_l('options')
							),'designation'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php init_tail(); ?>
	<script>
		initDataTable('.table-designation', window.location.href, [1], [1]);
	</script>
</body>
</html>
