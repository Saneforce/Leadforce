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
						<input type="hidden" id="cur_id12" value="<?php echo $id;?>" name="cur_id12">
							<?php $attrs = array('autofocus'=>true, 'required'=>true,'onblur'=>"check_name(this)",'onkeyup'=>"check_validate(this)", 'maxlength'=>"150"); ?>
							<?php echo render_input( 'name', 'name','','text',$attrs); ?>
							<div class="text-danger" id="name_id" style="display:none">Please enter valid name</div>
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
								<?php $attrs = array('autofocus'=>true, 'required'=>true,'onblur'=>"check_name(this)",'onkeyup'=>"check_validate(this)", 'maxlength'=>"150"); ?>
								<?php echo render_input( 'name1', 'name','','',$attrs); ?>
								<div id="contact_exists_info" class="hide"></div>
								<div class="text-danger" id="name1_id" style="display:none">Please enter valid name</div>								
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
					<?php if(!empty($report_name)){?>
						<h1>
							<a href="<?php echo admin_url('reports/view_deal_report/'.$folder_id);?>" title="<?php echo _l('back');?>"><i class="fa fa-arrow-circle-left fa-6" style="font-size: 45px;padding-right: 10px;top: 4px;position: relative;"></i></a>
							<?php echo $report_name;?>
						</h1>
					<?php }?>
					<div class="" style="float:left">
					<?php $filters = (empty($filters)?array():$filters);?>
						 <span id="ch_1_filter1"><?php echo count($filters);?></span> filters applied
						 <input type="hidden" id="c_filter_val1" value="<?php echo count($filters);?>"> 
					</div>
					<?php if(empty($id)){?>
						<div class="float-right" style="float:right">
							<button type="button" class="btn btn-primary pull-right1" style="background-color:#61c786 !important;" data-toggle="modal" data-target="#clientid_add_modal">Save</button>
						</div>
					<?php }else{
						$staffid = get_staff_user_id();
						$ch_admin = is_admin($staffid);
					?>
						<div class="float-right" style="float:right">
							<?php if($ch_admin){?>
								<button type="button" class="btn btn-primary pull-right1" style="background-color:#61c786 !important;" data-toggle="modal" data-target="#shared_add_modal" onclick="load_share('<?php echo $id;?>')">Shared</button>
							<?php }?>
							<button type="button" class="btn btn-primary pull-right1" style="background-color:#61c786 !important;" data-toggle="modal" data-target="#public_add_modal" onclick="load_public('<?php echo $id;?>')">Public Link</button>
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
					<div id="overlay_deal_public" class="overlay_new" style="display: none;"><div class="spinner"></div></div>
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
			
			<div class="modal fade" id="clientid_add_modal_public" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
			<div class="modal-dialog" role="document">
			<div id="overlay_deal12" class="overlay_new" style="display: none;"><div class="spinner"></div></div>
				<div class="modal-content">
					<div class="modal-header">
						<button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">
							<span class="edit-title"><?php echo _l('public_link'); ?></span>
						</h4>
					</div>
					<?php echo form_open('admin/reports/update_public_name',array('id'=>'update_public_name')); ?>
					<div class="modal-body">
						<input type="hidden" id="link_id" name="link_id">
							<?php $attrs = array('autofocus'=>true, 'required'=>true,'onblur'=>"check_name(this)",'onkeyup'=>"check_validate(this)", 'maxlength'=>"150"); ?>
							<?php echo render_input( 'ch_name12', 'name','','text',$attrs); ?>
							<div class="text-danger" id="ch_name12_id" style="display:none">Please enter valid name</div>
							<div id="companyname_exists_info" class="hide"></div>
							
					</div>
					<div class="modal-footer">
						<button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
						<button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
						
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
			
			<div class="modal fade" id="shared_add_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
				<div class="modal-dialog" role="document">
					<div id="overlay_deal123" class="overlay_new" style="display: none;"><div class="spinner"></div></div>
					<div class="modal-content">
						<div class="modal-header">
							<button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">
								<span class="edit-title"><?php echo _l('share_report'); ?></span>
							</h4>
						</div>
						<?php echo form_open('admin/reports/share_report',array('id'=>'share_report1')); ?>
							<div class="modal-body">
								<div id="shared_all">
									<input type="hidden" name="report_id" value="<?php echo $id;?>">
									<div class="form-group select-placeholder contactsdiv" >
										<label for="project_contacts_selectpicker"
										class="control-label"><small class="req text-danger">* </small><?php echo _l('share_with'); ?></label>
										 <div class="input-group input-group-select ">
										 <?php 
										 $persons[0] = array('id'=>'','name'=>'Select');
										 $persons[1] = array('id'=>'Everyone','name'=>'Everyone');
										 $persons[2] = array('id'=>'Selected Person','name'=>'Selected Person');
										 echo render_select('shared',$persons,array('id',array('name')),false,$share_types,array('aria-describedby'=>'project_contacts-error','style'=>'height:21px;','onchange'=>'shared1(this)','required'=>true),array(),'cur_class','',false);?>
										 <div class="input-group-addon" >
											</div>
										</div>
									</div>
									<div class="form-group select-placeholder contactsdiv" id="ch_staff" <?php if(in_array('Everyone',$share_types) || empty($share_types)){?>style="display:none" <?php }?>>
										<label for="project_contacts_selectpicker"
										class="control-label"><small class="req text-danger">* </small><?php echo _l('staff'); ?></label>
										 <div class="input-group input-group-select ">
										 <?php 
										 echo render_select('teamleader12[]', $teamleaders, array('staffid', array('firstname', 'lastname')),false, $share_persons, array('multiple'=>true,'onchange'=>'ch_staff(this)','required'),array(),'','',false);?>
										 <div class="input-group-addon" >
											</div>
											<div id="error_staff" class="error"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
								<button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
							</div>
						<?php echo form_close();?>
					</div>
				</div>
			</div>
		<?php }?>
		<?php echo $report_filter;?>
		<div class="panel_s project-menu-panel" style="margin-bottom:0px;">
			<div class="panel-body">
				<div class="horizontal-tabs">
					<ul class="nav nav-pills">
						<li class="<?php if(empty($_GET['filter_tab']) || $_GET['filter_tab'] == 1){ echo 'active';}?>">
							<a data-toggle="pill" href="#summary_table" onclick="tab_summary('1');"><?php echo _l('summary');?></a>
						</li>
						<li class="<?php if(!empty($_GET['filter_tab']) && $_GET['filter_tab'] == 2){ echo 'active';}?>">
							<a data-toggle="pill" href="#report_table" onclick="tab_summary('2');"><?php echo _l('deals');?></a>
						</li>
						
					</ul>
				</div>
			</div>
		</div>
		
		<div class="panel_s project-menu-panel">
			<div class="panel-body">
				<div class="col-md-12">
					<div id="overlay_deal" style="display: none;"><div class="spinner"></div></div>
					 <div class="tab-content">
						<div id="report_table" class="tab_summary tab-pane fade <?php if(!empty($_GET['filter_tab']) && $_GET['filter_tab'] == 2){ echo 'in active';}?>" >
								<?php $this->load->view('admin/reports/deal_list_column'); ?>
					
								<?php $this->load->view('admin/reports/deal_table_html'); ?>
						</div>
						<div id="summary_table" class="tab_summary tab-pane fade <?php if(empty($_GET['filter_tab']) || $_GET['filter_tab'] == 1){ echo 'in active';}?> ">
							<?php $this->load->view('admin/reports/deal_summary',$data); ?>
						</div>
					</div>
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