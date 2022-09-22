<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content"> 
		<div class="row">
			<div class="panel_s project-menu-panel">
				<div class="panel-body">
					<div class="horizontal-tabs">
						<ul class="nav nav-tabs no-margin project-tabs nav-tabs-horizontal" role="tablist">
							<li class="<?php if($type=='deal'){?>active<?php }?>">
								<a data-group="deal" role="tab" href="<?php echo admin_url('reports/all_share/deal'); ?>">
									<i class="fa fa-usd  fa-fw fa-lg" aria-hidden="true"></i><?php echo _l('share_deal');?>
								</a>
							</li>
							<li class="<?php if($type=='activity'){?>active<?php }?>">
								<a data-group="activity" role="tab" href="<?php echo admin_url('reports/all_share/activity'); ?>">
									<i class="fa fa-tasks  fa-fw fa-lg" aria-hidden="true"></i><?php echo _l('share_activity');?>                               
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="panel_s project-menu-panel">
				<div class="panel-body">
					<div class="col-md-12">
						<div class="panel_s">
							<div class="panel-body">
								<h4 class="no-margin"><?php echo $title; ?></h4>
								<hr class="hr-panel-heading">
								<?php
									$table_data = array('Id',_l('report'),_l('create_date'),_l('update_date'));
									render_datatable($table_data,'shared_list');  
								?>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="btn-bottom-pusher"></div>
	</div>
</div>
<link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
<?php init_tail(); ?>
<script>
$(function(){
	var notSortableAndSearchableItemColumns = [];
    initDataTable('.table-shared_list', admin_url+'reports/shared_list?type=<?php echo $type;?>', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns,'undefined',[0,'asc']);
});
$( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );
  </script>
   <style>
  .dt-buttons.btn-group {
		display: none;
	}
	.dataTables_length {
		display: none;
	}
	</style>