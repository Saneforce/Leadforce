<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content"> 
		<div class="row">
		<?php if ($this->session->flashdata('debug')) {?>
			<div class="col-lg-12">
				<div class="alert alert-warning">
					<?php echo $this->session->flashdata('debug'); ?>
				</div>
			</div>
		<?php
		} 
		$record_val = end($this->uri->segment_array());
		?>
		<div class="panel_s project-menu-panel">
			<div class="panel-body">
				<div class="horizontal-tabs">
					<ul class="nav nav-tabs no-margin project-tabs nav-tabs-horizontal" role="tablist">
						<li class="<?php if($record_val=='deal'){?>active<?php }?>">
							<a data-group="deal" role="tab" href="<?php echo admin_url('target/deal'); ?>">
								<i class="fa fa-check-circle" aria-hidden="true"></i><?php echo _l('target_deal');?>
							</a>
						</li>
						<li class="<?php if($record_val=='activity'){?>active<?php }?>">
							<a data-group="activity" role="tab" href="<?php echo admin_url('target/activity'); ?>">
								<i class="fa fa-th" aria-hidden="true"></i><?php echo _l('target_activity');?>                               
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="panel_s project-menu-panel">
			<div class="panel-body">
				<div class="col-md-12">
					<?php echo $tab_view;?>
					
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<div class="btn-bottom-pusher"></div>
	</div>
</div>
<?php init_tail(); ?>
<script>
$( "#add_deal" ).validate({
  rules: {
	select_deal: {
	  required: true
	},
	select_user2: {
	  required: true,
	},
	select_manger2: {
	  required: true,
	}
  }
});

		$(function(){
			var notSortableAndSearchableItemColumns = [];
    <?php if(has_permission('target','','delete')){ ?>
      //notSortableAndSearchableItemColumns.push(0);
    <?php } ?>

    initDataTable('.table-target-activity', admin_url+'target/activity_table', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns,'undefined',[1,'asc']);

		});
		 $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );
$(document).ready(function(){
	/*appDatepicker();
init_datepicker();*/
$('#end_date1').datepicker({
	 dateFormat:'dd-mm-yy',
	 calendarWeeks: true,
	autoclose: true,
	changeMonth: true,
	changeYear: true,
	   timepicker: false
});
$('#start_date1').datepicker({
	 dateFormat:'dd-mm-yy',
	   timepicker: false,
	   calendarWeeks: true,
	  changeMonth: true,
	  changeYear: true,
    todayHighlight: true,
      onSelect: function(selectedDate) {
            $('#end_date1').datepicker('option', 'minDate', selectedDate);
      }
});
 
});
	</script>
<style>
.horizontal-tabs {
    width:100%;
}
.project-tabs {
    float:left;
}
</style>
<style>

select.ui-datepicker-month{
	color:#727272;
}
select.ui-datepicker-year{
	color:#727272;
}
.ui-datepicker .ui-datepicker-header{
	background:#fff;
	border:0px;
}
a.ui-datepicker-prev.ui-corner-all{
	background:#727272;
}
a.ui-datepicker-next.ui-corner-all{
	background:#727272;
}
.horizontal-tabs {
    width:100%;
}
.project-tabs {
    float:left;
}
#start_date1-error{
	float: left;
    position: absolute;
    left: 2%;
    margin-top: 7%;
    margin-bottom: 22px;
}
.ui-datepicker th{
	color:#727272;
}
.ui-datepicker table{
	background:#fff;
}
a.ui-state-default {
    border: 0px !important;
    color: #727272 !important;
}
.ui-state-disabled, .ui-widget-content .ui-state-disabled, .ui-widget-header .ui-state-disabled{
	border: 0px !important;
    color: #727272 !important;
}
span.ui-state-default {
    color: #727272 !important;
    border: 0px !important;
}
.ui-state-highlight, .ui-widget-content .ui-state-highlight, .ui-widget-header .ui-state-highlight{
	background:#51A8D5;
	color:#fff !important;
}
</style>
</body>
</html>