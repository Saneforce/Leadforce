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
		<div class="modal fade" id="clientid_add_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">
							<span class="edit-title"><?php echo _l('add_report'); ?></span>
						</h4>
					</div>
					<?php echo form_open('admin/reports/save_report',array('id'=>'clientid_add_group_modal')); ?>
					<div class="modal-body">
						<input type="hidden" id="cur_id12" value="<?php echo $id;?>">
							<?php $attrs = array('autofocus'=>true, 'required'=>true); ?>
							<?php echo render_input( 'name', 'name','','text',$attrs); ?>
							<div id="companyname_exists_info" class="hide"></div>
							<div class="form-group select-placeholder contactsdiv" >
								<label for="project_contacts_selectpicker"
                                class="control-label"><small class="req text-danger">* </small><?php echo _l('folder'); ?></label>
								 <div class="input-group input-group-select ">
								 <?php 
								 $selected = '';
								 echo render_select('folder',$folders,array('id',array('folder')),false,$selected,array('aria-describedby'=>'project_contacts-error','style'=>'height:21px;'),array(),'cur_class','',false);?>
								 <div class="input-group-addon" style="opacity: 1;">
									<a href="#" data-toggle="modal" data-target="#section_modal" ><i class="fa fa-plus"></i></a>
									</div>
								</div>
							</div>
					</div>
					<div class="modal-footer">
						<button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
						<button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
						
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
		<div class="modal fade" id="section_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button group="button" class="close" data-dismiss="modal" aria-label="Close"><span
								aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">
							<span class="edit-title"><?php echo _l('add_new',_l('folder')); ?></span>
						</h4>
					</div>
					<?php echo form_open('admin/reports/add_folder',array('id'=>'section_add')); ?>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<?php $attrs = array('autofocus'=>true, 'required'=>true); ?>
								<?php echo render_input( 'name', 'name','','',$attrs); ?>
								<div id="contact_exists_info" class="hide"></div>								
								<div class="input_fields_wrap_ae">
								
								</div>
								
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
						<button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>

					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
		<div class="panel_s project-menu-panel" style="margin-bottom:0px;">
			<div class="panel-body">
				<div class="horizontal-tabs">
					<div class="" style="float:left">
						 <span id="ch_1_filter1"><?php echo count($filters);?></span> filters applied
						 <input type="hidden" id="c_filter_val1" value="<?php echo count($filters);?>"> 
					</div>
					<?php if(empty($id)){?>
						<div class="float-right" style="float:right">
							<button type="button" class="btn btn-primary pull-right1" style="background-color:#61c786 !important;" data-toggle="modal" data-target="#clientid_add_modal">Save</button>
						</div>
					<?php }else{?>
						<div class="float-right" style="float:right">
							<button type="button" class="btn btn-primary pull-right1" style="background-color:#61c786 !important;" data-toggle="modal" data-target="#public_add_modal">Public Link</button>
							<a href="<?php echo admin_url('reports/update_report/'.$id);?>" class="btn btn-primary pull-right1" style="background-color:#61c786 !important;" >Save</a>
							<button type="button" class="btn btn-primary pull-right1" style="background-color:#61c786 !important;" data-toggle="modal" data-target="#clientid_add_modal">Save New</button>
						</div>
					<?php }?>
				</div>
			</div>
		</div>
		<?php if(!empty($id)){?>
			<div class="modal fade" id="public_add_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">
								<span class="edit-title"><?php echo _l('public_link'); ?></span>
							</h4>
						</div>
						<div class="modal-body">
							<div>
								Create separate links to control access for different viewer groups and name each link accordingly. To revoke access, simply delete a link.
							</div>
							<div id="public_all">
								<?php if(!empty($links)){
									foreach($links as $link12){
									?>
										<div class="form-group" app-field-wrapper="name" ><label for="name" class="control-label"> Share Link</label><input type="text"  class="form-control" value="<?php echo base_url('shared/index/'.$link12['share_link']);?>"  readonly style="width:90%;float:left;"><a href="javascript:void(0);" " style="margin-left:10px;float:left" onclick="delete_link('<?php echo $link12['id'];?>')"><i class="fa fa-trash fa-2x" style="color:red"></i></a></div>
								<?php 
									}
								}?>
							
							</div>
							<div class="row"> <div class="col-md-12"><a href="javascript:void(0)" onclick="add_public_link('<?php echo $id;?>')">Add Link</a></div></div>
						</div>
						<div class="modal-footer">
							<button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
						</div>
					</div>
				</div>
			</div>
		<?php }?>
		<?php echo $report_filter;?>
		<div class="panel_s project-menu-panel">
			<div class="panel-body">
				<div class="col-md-12">
					<div id="overlay_deal" style="display: none;"><div class="spinner"></div></div>
<?php $this->load->view('admin/reports/deal_list_column'); ?>
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
<?php echo $report_footer;?>