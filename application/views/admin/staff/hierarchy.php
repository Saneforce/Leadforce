<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">

				<div class="panel_s">
					<div class="panel-body">
						<?php if(has_permission('staff','','create')){ ?>
						<div class="_buttons">
						<a href="<?php echo admin_url('staff'); ?>" class="btn btn-default pull-left mleft5"><?php echo _l('staff'); ?></a>
							<a href="<?php echo admin_url('roles'); ?>" class="btn btn-default pull-left mleft5"><?php echo _l('acs_roles'); ?></a>
							<a href="<?php echo admin_url('designation'); ?>" class="btn btn-default pull-left mleft5"><?php echo _l('acs_designation'); ?></a>
							<a href="<?php echo admin_url('staff/hierarchy'); ?>" class="btn btn-info pull-left mleft5"><?php echo _l('acs_hierarchy'); ?></a>
							<!-- <a href="<?php echo admin_url('tasks/emailmanagement'); ?>" class="btn btn-default pull-left mleft5"><?php echo _l('acs_emailmanagemnet'); ?></a> -->
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading" />
						<?php } ?>
						<div class="clearfix"></div>
						<link href="../../assets/css/treeflex.css" rel="stylesheet">
						<div class="tf-tree tf-gap-lg">
							<?php echo $controller->parseAndPrintTree(0,$staff_members); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
	$(function(){
		initDataTable('.table-staff', window.location.href);
	});
	function delete_staff_member(id){
		$('#delete_staff').modal('show');
		$('#transfer_data_to').find('option').prop('disabled',false);
		$('#transfer_data_to').find('option[value="'+id+'"]').prop('disabled',true);
		$('#delete_staff .delete_id input').val(id);
		$('#transfer_data_to').selectpicker('refresh');
	}
	$(document).ready(function () {
		$('ul').not(':has(li)').remove();
	});
</script>
</body>
</html>
