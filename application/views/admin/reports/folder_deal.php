<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content"> 
		<div class="row">
			<div class="panel_s project-menu-panel">
				<div class="panel-body">
					<div class="col-md-12">
						<div class="panel_s">
							<div class="panel-body">
								<h4 class="no-margin"><?php echo $title; ?></h4>
								<hr class="hr-panel-heading">
								<?php
									$table_data = array('Id',_l('folder'),_l('create_date'),_l('update_date'));
									render_datatable($table_data,'folder_deal_view');  
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
    initDataTable('.table-folder_deal_view', admin_url+'reports/folder_deal_view', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns,'undefined',[0,'asc']);
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