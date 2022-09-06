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
		<div class="panel_s project-menu-panel" style="margin-bottom:0px;">
			<div class="panel-body">
				<div class="horizontal-tabs">
					<?php if(!empty($report_name)){?>
						<h1>
							<a href="<?php echo admin_url('reports/all_share');?>" title="<?php echo _l('back');?>"><i class="fa fa-arrow-circle-left fa-6" style="font-size: 45px;padding-right: 10px;top: 4px;position: relative;"></i></a>
							<?php echo $report_name;?>
						</h1>
					<?php }?>
				</div>
			</div>
		</div>
		<div class="panel_s project-menu-panel">
			<div class="panel-body">
				<div class="col-md-12">
					<div id="overlay_deal" style="display: none;"><div class="spinner"></div></div>
					<?php //echo $tab_view;?>
								<?php $this->load->view('admin/reports/deal_table_html'); ?>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<div class="btn-bottom-pusher"></div>
	</div>
	<input type="hidden" id="check_search_id">
</div>
<?php init_tail(); app_admin_ajax_search_function();?>
<script>
$(function(){
     var ProjectsServerParams = {};
     $.each($('._hidden_inputs._filters input'),function(){
         ProjectsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
     });
     initDataTable('.table-projects', admin_url+'reports/deal_table/<?php echo $id;?>?call=share', undefined, [0], ProjectsServerParams, <?php echo hooks()->apply_filters('projects_table_default_order', json_encode(array())); ?>);
     init_ajax_search('customer', '#clientid_copy_project.ajax-search');
});
</script>
<style>
th.sorting,td {
    white-space: nowrap;
}
</style>