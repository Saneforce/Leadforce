<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<h4 class="no-margin"><?php echo $title; ?></h4>
						<hr class="hr-panel-heading" />
						<form action="" method="post" id="company">
							<div class="col-md-12 row">
								<table class="table table-condensed" border="0" cellpadding="0" cellspacing="0">
									<?php /*<thead>
									  <tr>
										<th><?php echo _l('fields'); ?></th>
										<th></th>
										
									  </tr>
									</thead>*/?>
									<tbody>
										<tr style="background-color:#ccc">
											<td><?php echo _l('project_name'); ?></td>
											<td>
												<div class="form-check form-switch">
													<label class="switch" title="For deal name can't change that"><input type="checkbox" checked name="deal[]" disabled><span class="slider round"></span></label>
													<span class="margin-left-10"><?php echo _l('is_field_needed'); ?></span>
												
													<label class="switch" title="For deal name can't change that"><input type="checkbox" checked  name="deal_mandatory[]" disabled ><span class="slider round"></span></label>
													<span class="margin-left-10 cl-red"><i class="fa fa-asterisk"></i></span><span class="margin-left-10"><?php echo _l('is_field_mandatory'); ?></span>
												
													<label class="switch" title="For deal name can't change that"><input type="checkbox" checked  name="deal_important[]" disabled >
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><i class="fa fa-exclamation-triangle"></i></span><span class="margin-left-10"><?php echo _l('is_field_important'); ?></span>
												</div>
											</td>
											<td style="width:17%">
												<div class="row">
													<div  class="col-md-12">
														<div class="form-group" >
															<label class="control-label"><?php echo _l('important_message'); ?></label>
															<input type="text" name="important_message[]" class="form-control" value="<?php if(!empty($important_msg->name)){ echo $important_msg->name;}?>" >
														</div>
													</div>
													
												</div>
											</td>
										</tr>
										<tr>
											<td><?php echo _l('project_customer'); ?></td>
											<td>
												<div class="form-check form-switch">
													<label class="switch">
														<input type="checkbox" name="deal[]" value="clientid" <?php if (!empty($needed_fields) && in_array("clientid", $needed_fields)){ echo 'checked';}?> onclick="check_deal(this,'client')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><?php echo _l('is_field_needed'); ?></span>
												
													<label class="switch">
														<input type="checkbox" name="deal_mandatory[]" id="mandatory_client" value="clientid" <?php if (!empty($needed_fields) && in_array("clientid", $needed_fields) && !empty($mandatory_fields) && in_array("clientid", $mandatory_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("clientid", $needed_fields)){ echo 'disabled';}?>>
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10 cl-red"><i class="fa fa-asterisk"></i></span><span class="margin-left-10"><?php echo _l('is_field_mandatory'); ?></span>
												
													<label class="switch">
														<input type="checkbox" name="deal_important[]" id="important_client" value="clientid" <?php if (!empty($needed_fields) && in_array("clientid", $needed_fields) && !empty($important_fields) && in_array("clientid", $important_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("clientid", $needed_fields)){ echo 'disabled';}?> onclick="check_important(this,'client')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><i class="fa fa-exclamation-triangle"></i></span><span class="margin-left-10"><?php echo _l('is_field_important'); ?></span>
												</div>
											</td>

											<td style="width:17%" >
												<div class="row" id="message_client" <?php if (empty($important_fields) || !in_array("clientid", $important_fields)){ ?> style="display:none" <?php } ?>>
													<div  class="col-md-12">
														<div class="form-group" >
															<label class="control-label"><?php echo _l('important_message'); ?></label>
															<input type="text" name="important_message[]" class="form-control" value="<?php if (!empty($important_fields) && in_array("clientid", $important_fields) && !empty($important_msg->clientid)){ echo $important_msg->clientid;}?>" >
														</div>
													</div>
													
												</div>
											</td>
										</tr>
										<tr>
											<td><?php echo _l('project_contacts'); ?></td>
											<td>
												<div class="form-check form-switch">
													<label class="switch">
														<input type="checkbox" name="deal[]" value="project_contacts[]" <?php if (!empty($needed_fields) && in_array("project_contacts[]", $needed_fields)){ echo 'checked';}?> onclick="check_deal(this,'contacts')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><?php echo _l('is_field_needed'); ?></span>
												
													<label class="switch">
														<input type="checkbox" name="deal_mandatory[]" id="mandatory_contacts" value="project_contacts[]" <?php if (!empty($needed_fields) && in_array("project_contacts[]", $needed_fields) && !empty($mandatory_fields) && in_array("project_contacts[]", $mandatory_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("project_contacts[]", $needed_fields)){ echo 'disabled';}?>>
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10 cl-red"><i class="fa fa-asterisk"></i></span><span class="margin-left-10"><?php echo _l('is_field_mandatory'); ?></span>
												
													<label class="switch">
														<input type="checkbox" name="deal_important[]" id="important_contacts" value="project_contacts[]" <?php if (!empty($needed_fields) && in_array("project_contacts[]", $needed_fields) && !empty($important_fields) && in_array("project_contacts[]", $important_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("project_contacts[]", $needed_fields)){ echo 'disabled';}?>  onclick="check_important(this,'contacts')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><i class="fa fa-exclamation-triangle"></i></span><span class="margin-left-10"><?php echo _l('is_field_important'); ?></span>
												</div>
											</td>
											<td style="width:17%" >
												<div class="row" id="message_contacts" <?php if (empty($important_fields) || !in_array("project_contacts[]", $important_fields)){?> style="display:none" <?php }?>>
													<div  class="col-md-12">
														<div class="form-group" >
															<label class="control-label"><?php echo _l('important_message'); ?></label>
															<input type="text" name="important_message[]" class="form-control" value="<?php if (!empty($important_fields) && in_array("project_contacts[]", $important_fields) && !empty($important_msg->project_contacts)){ echo $important_msg->project_contacts;}?>" >
														</div>
													</div>
													
												</div>
											</td>
										</tr>
										<tr>
											<td><?php echo _l('project_primary_contacts'); ?></td>
											<td>
												<div class="form-check form-switch">
													<label class="switch">
														<input type="checkbox" name="deal[]" value="primary_contact" <?php if (!empty($needed_fields) && in_array("primary_contact", $needed_fields)){ echo 'checked';}?> onclick="check_deal(this,'primary_contact')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><?php echo _l('is_field_needed'); ?></span>
												
													<label class="switch">
														<input type="checkbox" name="deal_mandatory[]" id="mandatory_primary_contact" value="primary_contact" <?php if (!empty($needed_fields) && in_array("primary_contact", $needed_fields) && !empty($mandatory_fields) && in_array("primary_contact", $mandatory_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("primary_contact", $needed_fields)){ echo 'disabled';}?>>
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10 cl-red"><i class="fa fa-asterisk"></i></span><span class="margin-left-10"><?php echo _l('is_field_mandatory'); ?></span>
												
													<label class="switch">
														<input type="checkbox" name="deal_important[]" id="important_primary_contact" value="primary_contact" <?php if (!empty($needed_fields) && in_array("primary_contact", $needed_fields) && !empty($important_fields) && in_array("primary_contact", $important_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("primary_contact", $needed_fields)){ echo 'disabled';}?> onclick="check_important(this,'primary_contact')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><i class="fa fa-exclamation-triangle"></i></span><span class="margin-left-10"><?php echo _l('is_field_important'); ?></span>
												</div>
											</td>
											<td style="width:17%">
												<div class="row" id="message_primary_contact" <?php if (empty($important_fields) || !in_array("primary_contact", $important_fields)){?> style="display:none" <?php }?>>
													<div  class="col-md-12">
														<div class="form-group" >
															<label class="control-label"><?php echo _l('important_message'); ?></label>
															<input type="text" name="important_message[]" class="form-control" value="<?php if (!empty($important_fields) && in_array("primary_contact", $important_fields) && !empty($important_msg->primary_contact)){ echo $important_msg->primary_contact;}?>" >
														</div>
													</div>
													
												</div>
											</td>
										</tr>
										<tr>
											<td><?php echo _l('pipeline'); ?></td>
											<td>
												<div class="form-check form-switch">
													<label class="switch">
														<input type="checkbox" name="deal[]" value="pipeline_id" <?php if (!empty($needed_fields) && in_array("pipeline_id", $needed_fields)){ echo 'checked';}?> onclick="check_deal(this,'pipeline_id')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><?php echo _l('is_field_needed'); ?></span>
												
													<label class="switch">
														<input type="checkbox" name="deal_mandatory[]" id="mandatory_pipeline_id" value="pipeline_id" <?php if (!empty($needed_fields) && in_array("pipeline_id", $needed_fields) && !empty($mandatory_fields) && in_array("pipeline_id", $mandatory_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("pipeline_id", $needed_fields)){ echo 'disabled';}?>>
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10 cl-red"><i class="fa fa-asterisk"></i></span><span class="margin-left-10"><?php echo _l('is_field_mandatory'); ?></span>
												
													<label class="switch">
														<input type="checkbox" name="deal_important[]" id="important_pipeline_id" value="pipeline_id" <?php if (!empty($needed_fields) && in_array("pipeline_id", $needed_fields) && !empty($important_fields) && in_array("pipeline_id", $important_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("pipeline_id", $needed_fields)){ echo 'disabled';}?> onclick="check_important(this,'pipeline_id')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><i class="fa fa-exclamation-triangle"></i></span><span class="margin-left-10"><?php echo _l('is_field_important'); ?></span>
												</div>
											</td>
											<td style="width:17%">
												<div class="row" id="message_pipeline_id" <?php if (empty($important_fields) || !in_array("pipeline_id", $important_fields)){?> style="display:none" <?php }?>>
													<div  class="col-md-12">
														<div class="form-group" >
															<label class="control-label"><?php echo _l('important_message'); ?></label>
															<input type="text" name="important_message[]" class="form-control" value="<?php if(!empty($important_fields) && in_array("pipeline_id", $important_fields) && !empty($important_msg->pipeline_id)){ echo $important_msg->pipeline_id;}?>" >
														</div>
													</div>
													
												</div>
											</td>
										</tr>
										<tr>
											<td><?php echo _l('project_status'); ?></td>
											<td>
												<div class="form-check form-switch">
													<label class="switch">
														<input type="checkbox" name="deal[]" value="status" <?php if (!empty($needed_fields) && in_array("status", $needed_fields)){ echo 'checked';}?> onclick="check_deal(this,'project_status')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><?php echo _l('is_field_needed'); ?></span>
												
													<label class="switch">
														<input type="checkbox" name="deal_mandatory[]" id="mandatory_project_status" value="status" <?php if (!empty($needed_fields) && in_array("status", $needed_fields) && !empty($mandatory_fields) && in_array("status", $mandatory_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("status", $needed_fields)){ echo 'disabled';}?>>
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10 cl-red"><i class="fa fa-asterisk"></i></span><span class="margin-left-10"><?php echo _l('is_field_mandatory'); ?></span>
													
													<label class="switch">
														<input type="checkbox" name="deal_important[]" id="important_project_status" value="status" <?php if (!empty($needed_fields) && in_array("status", $needed_fields) && !empty($important_fields) && in_array("status", $important_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("status", $needed_fields)){ echo 'disabled';}?> onclick="check_important(this,'project_status')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><i class="fa fa-exclamation-triangle"></i></span><span class="margin-left-10"><?php echo _l('is_field_important'); ?></span>
												</div>
											</td>
											<td style="width:17%">
												<div class="row" id="message_project_status" <?php if (empty($important_fields) || !in_array("status", $important_fields)){?> style="display:none" <?php }?>>
													<div  class="col-md-12">
														<div class="form-group" >
															<label class="control-label"><?php echo _l('important_message'); ?></label>
															<input type="text" name="important_message[]" class="form-control" value="<?php if(!empty($important_fields) && in_array("status", $important_fields) && !empty($important_msg->status)){ echo $important_msg->status;}?>" >
														</div>
													</div>
													
												</div>
											</td>
										</tr>
										<tr>
											<td><?php echo _l('teamleader_name'); ?></td>
											<td>
												<div class="form-check form-switch">
													<label class="switch">
														<input type="checkbox" name="deal[]" value="teamleader" <?php if (!empty($needed_fields) && in_array("teamleader", $needed_fields)){ echo 'checked';}?> onclick="check_deal(this,'teamleader')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><?php echo _l('is_field_needed'); ?></span>
												
													<label class="switch">
														<input type="checkbox" name="deal_mandatory[]" id="mandatory_teamleader" value="teamleader" <?php if (!empty($needed_fields) && in_array("teamleader", $needed_fields) && !empty($mandatory_fields) && in_array("teamleader", $mandatory_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("teamleader", $needed_fields)){ echo 'disabled';}?>>
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10 cl-red"><i class="fa fa-asterisk"></i></span><span class="margin-left-10"><?php echo _l('is_field_mandatory'); ?></span>
													
													<label class="switch">
														<input type="checkbox" name="deal_important[]" id="important_teamleader" value="teamleader" <?php if (!empty($needed_fields) && in_array("teamleader", $needed_fields) && !empty($important_fields) && in_array("teamleader", $important_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("teamleader", $needed_fields)){ echo 'disabled';}?> onclick="check_important(this,'teamleader')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><i class="fa fa-exclamation-triangle"></i></span><span class="margin-left-10"><?php echo _l('is_field_important'); ?></span>
												</div>
											</td>
											<td style="width:17%">
												<div class="row" id="message_teamleader" <?php if (empty($important_fields) || !in_array("teamleader", $important_fields)){?> style="display:none" <?php }?>>
													<div  class="col-md-12">
														<div class="form-group" >
															<label class="control-label"><?php echo _l('important_message'); ?></label>
															<input type="text" name="important_message[]" class="form-control" value="<?php if(!empty($important_fields) && in_array("teamleader", $important_fields) && !empty($important_msg->teamleader)){ echo $important_msg->teamleader;}?>" >
														</div>
													</div>
													
												</div>
											</td>
										</tr>
										<tr>
											<td><?php echo _l('task_single_followers'); ?></td>
											<td>
												<div class="form-check form-switch">
													<label class="switch">
														<input type="checkbox" name="deal[]" value="project_members[]" <?php if (!empty($needed_fields) && in_array("project_members[]", $needed_fields)){ echo 'checked';}?> onclick="check_deal(this,'members')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><?php echo _l('is_field_needed'); ?></span>
												
													<label class="switch">
														<input type="checkbox" name="deal_mandatory[]" id="mandatory_members" value="project_members[]" <?php if (!empty($needed_fields) && in_array("project_members[]", $needed_fields) && !empty($mandatory_fields) && in_array("project_members[]", $mandatory_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("project_members[]", $needed_fields)){ echo 'disabled';}?>>
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10 cl-red"><i class="fa fa-asterisk"></i></span><span class="margin-left-10"><?php echo _l('is_field_mandatory'); ?></span>
													
													<label class="switch">
														<input type="checkbox" name="deal_important[]" id="important_members" value="project_members[]" <?php if (!empty($needed_fields) && in_array("project_members[]", $needed_fields) && !empty($important_fields) && in_array("project_members[]", $important_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("project_members[]", $needed_fields)){ echo 'disabled';}?> onclick="check_important(this,'members')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><i class="fa fa-exclamation-triangle"></i></span><span class="margin-left-10"><?php echo _l('is_field_important'); ?></span>
												</div>
											</td>
											<td style="width:17%">
												<div class="row" id="message_members" <?php if (empty($important_fields) || !in_array("project_members[]", $important_fields)){?> style="display:none" <?php }?>>
													<div  class="col-md-12">
														<div class="form-group" >
															<label class="control-label"><?php echo _l('important_message'); ?></label>
															<input type="text" name="important_message[]" class="form-control" value="<?php if(!empty($important_fields) && in_array("project_members[]", $important_fields) && !empty($important_msg->project_members)){ echo $important_msg->project_members;}?>" >
														</div>
													</div>
													
												</div>
											</td>
										</tr>
										<tr>
											<td><?php echo _l('project_total_cost'); ?></td>
											<td>
												<div class="form-check form-switch">
													<label class="switch">
														<input type="checkbox" name="deal[]" value="project_cost" <?php if (!empty($needed_fields) && in_array("project_cost", $needed_fields)){ echo 'checked';}?> onclick="check_deal(this,'cost')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><?php echo _l('is_field_needed'); ?></span>
												
													<label class="switch">
														<input type="checkbox" name="deal_mandatory[]" id="mandatory_cost" value="project_cost" <?php if (!empty($needed_fields) && in_array("project_cost", $needed_fields) && !empty($mandatory_fields) && in_array("project_cost", $mandatory_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("project_cost", $needed_fields)){ echo 'disabled';}?>>
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10 cl-red"><i class="fa fa-asterisk"></i></span><span class="margin-left-10"><?php echo _l('is_field_mandatory'); ?></span>
													
													<label class="switch">
														<input type="checkbox" name="deal_important[]" id="important_cost" value="project_cost" <?php if (!empty($needed_fields) && in_array("project_cost", $needed_fields) && !empty($important_fields) && in_array("project_cost", $important_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("project_cost", $needed_fields)){ echo 'disabled';}?> onclick="check_important(this,'cost')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><i class="fa fa-exclamation-triangle"></i></span><span class="margin-left-10"><?php echo _l('is_field_important'); ?></span>
												</div>
											</td>
											<td style="width:17%">
												<div class="row" id="message_cost" <?php if (empty($important_fields) || !in_array("project_cost", $important_fields)){?> style="display:none" <?php }?>>
													<div  class="col-md-12">
														<div class="form-group" >
															<label class="control-label"><?php echo _l('important_message'); ?></label>
															<input type="text" name="important_message[]" class="form-control" value="<?php if(!empty($important_fields) && in_array("project_cost", $important_fields) && !empty($important_msg->project_cost)){ echo $important_msg->project_cost;}?>" >
														</div>
													</div>
													
												</div>
											</td>
										</tr>
										<tr>
											<td><?php echo _l('start_date'); ?></td>
											<td>
												<div class="form-check form-switch">
													<label class="switch">
														<input type="checkbox" name="deal[]" value="project_start_date" <?php if (!empty($needed_fields) && in_array("project_start_date", $needed_fields)){ echo 'checked';}?> onclick="check_deal(this,'start_date')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><?php echo _l('is_field_needed'); ?></span>
												
													<label class="switch">
														<input type="checkbox" name="deal_mandatory[]" id="mandatory_start_date" value="project_start_date" <?php if (!empty($needed_fields) && in_array("project_start_date", $needed_fields) && !empty($mandatory_fields) && in_array("project_start_date", $mandatory_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("project_start_date", $needed_fields)){ echo 'disabled';}?>>
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10 cl-red"><i class="fa fa-asterisk"></i></span><span class="margin-left-10"><?php echo _l('is_field_mandatory'); ?></span>
													
													<label class="switch">
														<input type="checkbox" name="deal_important[]" id="important_start_date" value="project_start_date" <?php if (!empty($needed_fields) && in_array("project_start_date", $needed_fields) && !empty($important_fields) && in_array("project_start_date", $important_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("project_start_date", $needed_fields)){ echo 'disabled';}?> onclick="check_important(this,'start_date')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><i class="fa fa-exclamation-triangle"></i></span><span class="margin-left-10"><?php echo _l('is_field_important'); ?></span>
												</div>
											</td>
											<td style="width:17%">
												<div class="row" id="message_start_date" <?php if (empty($important_fields) || !in_array("project_start_date", $important_fields)){?> style="display:none" <?php }?>>
													<div  class="col-md-12">
														<div class="form-group" >
															<label class="control-label"><?php echo _l('important_message'); ?></label>
															<input type="text" name="important_message[]" class="form-control" value="<?php if(!empty($important_fields) && in_array("project_start_date", $important_fields) && !empty($important_msg->project_start_date)){ echo $important_msg->project_start_date;}?>" >
														</div>
													</div>
													
												</div>
											</td>
										</tr>
										<tr>
											<td><?php echo _l('end_date'); ?></td>
											<td>
												<div class="form-check form-switch">
													<label class="switch">
														<input type="checkbox" name="deal[]" value="project_deadline" <?php if (!empty($needed_fields) && in_array("project_deadline", $needed_fields)){ echo 'checked';}?> onclick="check_deal(this,'end_date')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><?php echo _l('is_field_needed'); ?></span>
												
													<label class="switch">
														<input type="checkbox" name="deal_mandatory[]" id="mandatory_end_date" value="project_deadline" <?php if (!empty($needed_fields) && in_array("project_deadline", $needed_fields) && !empty($mandatory_fields) && in_array("project_deadline", $mandatory_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("project_deadline", $needed_fields)){ echo 'disabled';}?>>
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10 cl-red"><i class="fa fa-asterisk"></i></span><span class="margin-left-10"><?php echo _l('is_field_mandatory'); ?></span>
													
													<label class="switch">
														<input type="checkbox" name="deal_important[]" id="important_end_date" value="project_deadline" <?php if (!empty($needed_fields) && in_array("project_deadline", $needed_fields) && !empty($important_fields) && in_array("project_deadline", $important_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("project_deadline", $needed_fields)){ echo 'disabled';}?> onclick="check_important(this,'end_date')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><i class="fa fa-exclamation-triangle"></i></span><span class="margin-left-10"><?php echo _l('is_field_important'); ?></span>
												</div>
											</td>
											<td style="width:17%">
												<div class="row" id="message_end_date" <?php if (empty($important_fields) || !in_array("project_deadline", $important_fields)){?> style="display:none" <?php }?>>
													<div  class="col-md-12">
														<div class="form-group" >
															<label class="control-label"><?php echo _l('important_message'); ?></label>
															<input type="text" name="important_message[]" class="form-control" value="<?php if(!empty($important_fields) && in_array("project_deadline", $important_fields) && !empty($important_msg->project_deadline)){ echo $important_msg->project_deadline;}?>" >
														</div>
													</div>
													
												</div>
											</td>
										</tr>
										<tr>
											<td><?php echo _l('tags'); ?></td>
											<td>
												<div class="form-check form-switch">
													<label class="switch">
														<input type="checkbox" name="deal[]" value="tags" <?php if (!empty($needed_fields) && in_array("tags", $needed_fields)){ echo 'checked';}?> onclick="check_deal(this,'tags')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><?php echo _l('is_field_needed'); ?></span>
												
													<label class="switch">
														<input type="checkbox" name="deal_mandatory[]" id="mandatory_tags" value="tags" <?php if (!empty($needed_fields) && in_array("tags", $needed_fields) && !empty($mandatory_fields) && in_array("tags", $mandatory_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("tags", $needed_fields)){ echo 'disabled';}?>>
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10 cl-red"><i class="fa fa-asterisk"></i></span><span class="margin-left-10"><?php echo _l('is_field_mandatory'); ?></span>
													
													<label class="switch">
														<input type="checkbox" name="deal_important[]" id="important_tags" value="tags" <?php if (!empty($needed_fields) && in_array("tags", $needed_fields) && !empty($important_fields) && in_array("tags", $important_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("tags", $needed_fields)){ echo 'disabled';}?> onclick="check_important(this,'tags')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><i class="fa fa-exclamation-triangle"></i></span><span class="margin-left-10"><?php echo _l('is_field_important'); ?></span>
												</div>
											</td>
											<td style="width:17%">
												<div class="row" id="message_tags" <?php if (empty($important_fields) || !in_array("tags", $important_fields)){?> style="display:none" <?php }?>>
													<div  class="col-md-12">
														<div class="form-group" >
															<label class="control-label"><?php echo _l('important_message'); ?></label>
															<input type="text" name="important_message[]" class="form-control" value="<?php if(!empty($important_fields) && in_array("tags", $important_fields) && !empty($important_msg->tags)){ echo $important_msg->tags;}?>" >
														</div>
													</div>
													
												</div>
											</td>
										</tr>
										<tr>
											<td><?php echo _l('project_description'); ?></td>
											<td>
												<div class="form-check form-switch">
													<label class="switch">
														<input type="checkbox" name="deal[]" value="description" <?php if (!empty($needed_fields) && in_array("description", $needed_fields)){ echo 'checked';}?> onclick="check_deal(this,'description')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><?php echo _l('is_field_needed'); ?></span>
												
													<label class="switch">
														<input type="checkbox" name="deal_mandatory[]" id="mandatory_description" value="description" <?php if (!empty($needed_fields) && in_array("description", $needed_fields) && !empty($mandatory_fields) && in_array("description", $mandatory_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("description", $needed_fields)){ echo 'disabled';}?>>
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10 cl-red"><i class="fa fa-asterisk"></i></span><span class="margin-left-10"><?php echo _l('is_field_mandatory'); ?></span>
													
													<label class="switch">
														<input type="checkbox" name="deal_important[]" id="important_description" value="description" <?php if (!empty($needed_fields) && in_array("description", $needed_fields) && !empty($important_fields) && in_array("description", $important_fields)){ echo 'checked';} if (empty($needed_fields) || !in_array("description", $needed_fields)){ echo 'disabled';}?> onclick="check_important(this,'description')">
														<span class="slider round"></span>
													</label>
													<span class="margin-left-10"><i class="fa fa-exclamation-triangle"></i></span><span class="margin-left-10"><?php echo _l('is_field_important'); ?></span>
												</div>
											</td>
											<td style="width:17%">
												<div class="row" id="message_description" <?php if (empty($important_fields) || !in_array("description", $important_fields)){?> style="display:none" <?php }?>>
													<div  class="col-md-12">
														<div class="form-group" >
															<label class="control-label"><?php echo _l('important_message'); ?></label>
															<input type="text" name="important_message[]" class="form-control" value="<?php if(!empty($important_fields) && in_array("description", $important_fields) && !empty($important_msg->description)){ echo $important_msg->description;}?>" >
														</div>
													</div>
													
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							
								<div class="col-md-9">
									<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
									<button type="submit" value="Save" class="btn btn-primary" name="save">Save</button>
								</div>
								<div class="col-md-3"></div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
function check_deal(a,b){
	if (a.checked) {
		$("#mandatory_"+b).removeAttr("disabled");
		$("#important_"+b).removeAttr("disabled");
	} 
	else{
		$("#mandatory_"+b).prop("checked", false);
		$("#important_"+b).prop("checked", false);
		$("#mandatory_"+b).attr("disabled", true);
		$("#important_"+b).attr("disabled", true);
		$("#message_"+b).hide();
	}
}
function check_important(a,b){
	$("#message_"+b).hide();
	if (a.checked) {
		$("#message_"+b).show();
	}
}
</script>
<style>
i.fa.fa-exclamation-triangle{
	color:#0069e8;
}
.switch {
  position: relative;
  display: inline-block;
  width: 45px;
  height: 20px;
  margin-left:10px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 15px;
  width: 15px;
  left: 0px;
  bottom: 3px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
.margin-left-10{
	margin-left:10px;
}
.cl-red{
	color:red
}
</style>
<?php init_tail(); ?>
</body>
</html>
