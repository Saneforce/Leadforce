<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$roleselected = ''; ?>

<style>
.iti {
    position: unset !important; 
    display: block !important;
    width : 100% !important;
}
.iti__flag-container {
    z-index: 999 !important;
}

</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css">

<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php if(isset($member)){ ?>
				<!-- <div class="col-md-12">
					<div class="panel_s">
						<div class="panel-body no-padding-bottom">
							<?php //$this->load->view('admin/staff/stats'); ?>
						</div>
					</div>
				</div> -->
				<div class="member">
					<?php echo form_hidden('isedit'); ?>
					<?php echo form_hidden('memberid',$member->staffid); ?>
				</div>
			<?php } ?>
			<?php if(isset($member)){ ?>
				<div class="col-md-12">
					<?php if(total_rows(db_prefix().'departments',array('email'=>$member->email)) > 0) { ?>
						<div class="alert alert-danger">
							The staff member email exists also as support department email, according to the docs, the support department email must be unique email in the system, you must change the staff email or the support department email in order all the features to work properly.
						</div>
					<?php } ?>
					<div class="panel_s">
						<div class="panel-body">
							<h4 class="no-margin"><?php echo $member->firstname . ' ' . $member->lastname; ?>
								<?php if($member->last_activity && $member->staffid != get_staff_user_id()){ ?>
									<small> - <?php echo _l('last_active'); ?>:
										<span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($member->last_activity); ?>">
											<?php echo time_ago($member->last_activity); ?>
										</span>
									</small>
								<?php } ?>
								<a href="#" onclick="small_table_full_view(); return false;" data-placement="left" data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>" class="toggle_view pull-right">
									<i class="fa fa-expand"></i>
								</a>
							</h4>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php echo form_open_multipart($this->uri->uri_string(),array('class'=>'staff-form','autocomplete'=>'off')); ?>
				<div class="col-md-<?php if(!isset($member)){echo '8 col-md-offset-2';} else {echo '5';} ?>" id="small-table">
					<div class="panel_s">
						<div class="panel-body">
							<ul class="nav nav-tabs" role="tablist">
								<li role="presentation" class="active">
									<a href="#tab_staff_profile" aria-controls="tab_staff_profile" role="tab" data-toggle="tab">
										<?php echo _l('staff_profile_string'); ?>
									</a>
								</li>
								<li role="presentation">
									<a href="#staff_permissions" aria-controls="staff_permissions" role="tab" data-toggle="tab">
										<?php echo _l('staff_add_edit_permissions'); ?>
									</a>
								</li>
							</ul>
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active" id="tab_staff_profile">
									<?php if(total_rows(db_prefix().'emailtemplates',array('slug'=>'two-factor-authentication','active'=>0)) == 0){ ?>
										<div class="checkbox checkbox-primary">
											<input type="checkbox" value="1" name="two_factor_auth_enabled" id="two_factor_auth_enabled"<?php if(isset($member) && $member->two_factor_auth_enabled == 1){echo ' checked';} ?>>
											<label for="two_factor_auth_enabled"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('two_factor_authentication_info'); ?>"></i>
											<?php echo _l('enable_two_factor_authentication'); ?></label>
										</div>
									<?php } ?>
									<?php /*
									<div class="is-not-staff<?php if(isset($member) && $member->admin == 1){ echo ' hide'; }?>">
										<div class="checkbox checkbox-primary">
											<?php $checked = '';
											if(isset($member))
											{
												if($member->is_not_staff == 1) {
													$checked = ' checked';
												}
											} ?>
											<input type="checkbox" value="1" name="is_not_staff" id="is_not_staff" <?php echo $checked; ?>>
											<label for="is_not_staff"><?php echo _l('is_not_staff_member'); ?></label>
										</div>
										<hr/>
									</div>
									 */?>
									<?php  if((isset($member) && $member->profile_image == NULL) || !isset($member)){ ?>
										<div class="form-group">
											<label for="profile_image" class="profile-image"><?php echo _l('staff_edit_profile_image'); ?></label>
											<input type="file" name="profile_image" class="form-control" id="profile_image">
										</div>
									<?php } ?>
									<?php if(isset($member) && $member->profile_image != NULL){ ?>
										<div class="form-group">
											<div class="row">
												<div class="col-md-9">
													<?php echo staff_profile_image($member->staffid,array('img','img-responsive','staff-profile-image-thumb'),'thumb'); ?>
												</div>
												<div class="col-md-3 text-right">
													<a href="<?php echo admin_url('staff/remove_staff_profile_image/'.$member->staffid); ?>"><i class="fa fa-remove"></i></a>
												</div>
											</div>
										</div>
									<?php } ?>
									<?php $value = (isset($member) ? $member->firstname : ''); ?>
									<?php $attrs = (isset($member) ? array() : array('autofocus'=>true)); ?>
									<?php echo render_input('firstname','staff_add_edit_firstname',$value,'text',$attrs); ?>
									<?php $value = (isset($member) ? $member->lastname : ''); ?>
									<?php echo render_input('lastname','staff_add_edit_lastname',$value); ?>
									<?php $value = (isset($member) ? $member->email : ''); ?>
									<?php echo render_input('email','staff_add_edit_email',$value,'email'); ?>
									<div class="form-group">
										<label for="hourly_rate"><?php echo _l('staff_hourly_rate'); ?></label>
										<div class="input-group">
											<input type="number" name="hourly_rate" value="<?php if(isset($member)){echo $member->hourly_rate;} else {echo 0;} ?>" id="hourly_rate" class="form-control">
											<span class="input-group-addon">
												<?php echo $base_currency->symbol; ?>
											</span>
										</div>
									</div>
									<?php $value = (isset($member) ? $member->phonenumber : ''); ?>

									<div class="form-group" app-field-wrapper="phonenumber" id="phonenumber_iti_wrapper">
										<label for="phonenumber" class="control-label"><?php echo  _l('staff_add_edit_phonenumber') ?>  </label>
										<div class="input-group" style="width:100%">
											<input type="text" id="phonenumber" name="phonenumber" class="form-control" autocomplete="off" value="<?php echo $value; ?>">
										</div>
									</div>
									<input type="hidden" name="phone_country_code" id="phone_country_code" value="<?php echo ( isset($member) ? $member->phone_country_code : 'IN'); ?>">
									<?php
									$designationselected = '';
									foreach($designations as $designation)
									{
										if(isset($member))
										{
											if($member->designation == $designation['designationid']) {
												$designationselected = $designation['designationid'];
											}
										}
										else {
											$default_staff_designation = get_option('default_staff_designation');
											if($default_staff_designation == $designation['designationid']) {
												$designationselected = $designation['designationid'];
											}
										}
									} ?>
									<?php echo render_select('designation',$designations,array('designationid','name','roleid'),'staff_add_edit_designation',$designationselected); ?>
									<?php $value = (isset($member) ? $member->emp_id : ''); ?>
									<?php echo render_input('emp_id','staff_add_edit_emp_id',$value); ?>
									<div class="form-group">
										<label for="facebook" class="control-label"><i class="fa fa-facebook"></i> <?php echo _l('staff_add_edit_facebook'); ?></label>
										<input type="text" class="form-control" name="facebook" value="<?php if(isset($member)){echo $member->facebook;} ?>">
									</div>
									<div class="form-group">
										<label for="linkedin" class="control-label"><i class="fa fa-linkedin"></i> <?php echo _l('staff_add_edit_linkedin'); ?></label>
										<input type="text" class="form-control" name="linkedin" value="<?php if(isset($member)){echo $member->linkedin;} ?>">
									</div>
									<div class="form-group">
										<label for="skype" class="control-label"><i class="fa fa-skype"></i> <?php echo _l('staff_add_edit_skype'); ?></label>
										<input type="text" class="form-control" name="skype" value="<?php if(isset($member)){echo $member->skype;} ?>">
									</div>
									<?php /* if(get_option('disable_language') == 0){ ?>
										<div class="form-group select-placeholder">
											<label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?></label>
											<select name="default_language" data-live-search="true" id="default_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
												<option value=""><?php echo _l('system_default_string'); ?></option>
												<?php foreach($this->app->get_available_languages() as $availableLanguage){
													$selected = '';
													if(isset($member))
													{
														if($member->default_language == $availableLanguage) {
															$selected = 'selected';
														}
													} ?>
													<option value="<?php echo $availableLanguage; ?>" <?php echo $selected; ?>><?php echo ucfirst($availableLanguage); ?></option>
												<?php } ?>
											</select>
										</div>
									<?php } */?>
									<i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('staff_email_signature_help'); ?>"></i>
									<?php $value = (isset($member) ? $member->email_signature : ''); ?>
									<?php echo render_textarea('email_signature','settings_email_signature',$value, ['data-entities-encode'=>'true']); ?>
									<?php /*?>
									<div class="form-group select-placeholder">
										<label for="direction"><?php echo _l('document_direction'); ?></label>
										<select class="selectpicker" data-none-selected-text="<?php echo _l('system_default_string'); ?>" data-width="100%" name="direction" id="direction">
											<option value="" <?php if(isset($member) && empty($member->direction)){echo 'selected';} ?>></option>
											<option value="ltr" <?php if(isset($member) && $member->direction == 'ltr'){echo 'selected';} ?>>LTR</option>
											<option value="rtl" <?php if(isset($member) && $member->direction == 'rtl'){echo 'selected';} ?>>RTL</option>
										</select>
									</div>
									<?php } */?>
									<div class="form-group">
										<?php if(count($departments) > 0){ ?>
											<label for="departments"><?php echo _l('staff_add_edit_departments'); ?></label>
										<?php } ?>
										<?php foreach($departments as $department){ ?>
											<div class="checkbox checkbox-primary">
												<?php $checked = '';
												if(isset($member))
												{
													foreach ($staff_departments as $staff_department)
													{
														if($staff_department['departmentid'] == $department['departmentid']) {
															$checked = ' checked';
														}
													}
												} ?>
												<input type="checkbox" id="dep_<?php echo $department['departmentid']; ?>" name="departments[]" value="<?php echo $department['departmentid']; ?>"<?php echo $checked; ?>>
												<label for="dep_<?php echo $department['departmentid']; ?>"><?php echo $department['name']; ?></label>
											</div>
										<?php } ?>
									</div>
									<?php $rel_id = (isset($member) ? $member->staffid : false); ?>
									<?php echo render_custom_fields('staff',$rel_id); ?>
									<div class="row">
										<div class="col-md-12">
											<hr class="hr-10" />
											<?php if (is_admin()){ ?>
												<div class="checkbox checkbox-primary">
													<?php $isadmin = '';
													if(isset($member) && ($member->staffid == get_staff_user_id() || is_admin($member->staffid))) {
														$isadmin = ' checked';
													} ?>
													<input type="checkbox" name="administrator" id="administrator" <?php echo $isadmin; ?>>
													<label for="administrator"><?php echo _l('staff_add_edit_administrator'); ?></label>
												</div>
											<?php } ?>
											<?php /* if(!isset($member) && total_rows(db_prefix().'emailtemplates',array('slug'=>'new-staff-created','active'=>0)) === 0){ ?>
												<div class="checkbox checkbox-primary">
													<input type="checkbox" name="send_welcome_email" id="send_welcome_email" checked>
													<label for="send_welcome_email"><?php echo _l('staff_send_welcome_email'); ?></label>
												</div>
											<?php } */ ?>
										</div>
									</div>
									<?php hooks()->do_action('staff_render_permissions');
									$roleselected = '';
									foreach($roles as $role)
									{
										if(isset($member))
										{
											if($member->role == $role['roleid']) {
												$roleselected = $role['roleid'];
											}
										}
										else {
											$default_staff_role = get_option('default_staff_role');
											if($default_staff_role == $role['roleid']) {
												$roleselected = $role['roleid'];
											}
										}
									} ?>
									<?php echo render_select('role',$roles,array('roleid','name'),'staff_add_edit_role',$roleselected); ?>
									<hr/>
									<?php
									$dreporting_toselected = '';
									foreach($member_reporting_to as $reporting_to)
									{
										if(isset($member))
										{
											if($member->reporting_to == $reporting_to['staffid']) {
												$dreporting_toselected = $reporting_to['staffid'];
											}
										}
									} ?>
									
									<?php 
									echo render_select('reporting_to',$member_reporting_to,array('staffid','full_name','role'),'staff_add_edit_reporting_to',$dreporting_toselected); ?>
									
									<?php if(!isset($member) || is_admin() || !is_admin() && $member->admin == 0) { ?>
										<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
										<input type="text" class="fake-autofill-field" name="fakeusernameremembered" value='' tabindex="-1"/>
										<input type="password" class="fake-autofill-field" name="fakepasswordremembered" value='' tabindex="-1"/>
										<div class="clearfix form-group"></div>
										<label for="password" class="control-label"><?php echo _l('staff_add_edit_password'); ?></label>
										<div class="input-group">
											<input type="password" class="form-control password" name="password" autocomplete="off">
											<span class="input-group-addon">
												<a href="#password" class="show_password" onclick="showPassword('password'); return false;"><i class="fa fa-eye"></i></a>
											</span>
											<span class="input-group-addon">
												<a href="#" class="generate_password" onclick="generatePassword(this);return false;"><i class="fa fa-refresh"></i></a>
											</span>
										</div>
										<?php if(isset($member)){ ?>
											<p class="text-muted"><?php echo _l('staff_add_edit_password_note'); ?></p>
											<?php if($member->last_password_change != NULL){ ?>
												<?php echo _l('staff_add_edit_password_last_changed'); ?>:
												<span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($member->last_password_change); ?>">
													<?php echo time_ago($member->last_password_change); ?>
												</span>
											<?php }
										} ?>
									<?php } ?>
									<?php $value = (isset($member) ? $member->action_for : 'Active'); ?>
									<?php echo render_select('action_for',$member_action_for,array('text','text'),'staff_add_edit_action_for',$value); ?>
									<?php if(isset($member) && $member->action_for == 'Deactivate' && $member->deavite_follow_ids != '') { ?>
										<div class="clearfix form-group">
										<div class="input-group rollback">
											<input type="checkbox" name="rollback" value="rollback" style="zoom:1.5">
											<span style="font-size:19px; position:relative; bottom:3px;"> Rollback</span>
										</div>
										</div>
									<?php } ?>
									<?php
									$deavite_followselected = '';
									foreach($member_reporting_to as $reporting_to)
									{
										if(isset($member))
										{
											if($member->deavite_follow == $reporting_to['staffid']) {
												$deavite_followselected = $reporting_to['staffid'];
											}
										}
									} 
									if($member->deavite_re_assign == 0) {
									?>
									<div class="deavite_follow">
									<?php 
									echo render_select('deavite_follow',$member_reporting_to,array('staffid','full_name'),'staff_add_edit_deavite_follow',$deavite_followselected); ?>
									<!-- <p class="text-muted"><?php echo _l('staff_add_edit_deavite_re_assign_note'); ?></p> -->
									</div>
									<?php } ?>
									<?php
									$deavite_re_assignselected = '';
									foreach($member_reporting_to as $reporting_to)
									{
										if(isset($member))
										{
											if($member->deavite_re_assign == $reporting_to['staffid']) {
												$deavite_re_assignselected = $reporting_to['staffid'];
											}
											if($member->deavite_re_assign) {
												$args = array('disabled'=>'disabled');
											}
										}
									} ?>
									<div class="deavite_re_assign">
									<?php 
									echo render_select('deavite_re_assign',$member_reporting_to,array('staffid','full_name'),'staff_add_edit_deavite_re_assign',$deavite_re_assignselected,$args); ?>
									<!-- <p class="text-muted"><?php echo _l('staff_add_edit_deavite_re_assign_note'); ?></p> -->
									</div>
									
								</div>
								<div role="tabpanel" class="tab-pane" id="staff_permissions">
									
									<h4 class="font-medium mbot15 bold"><?php echo _l('staff_add_edit_permissions'); ?></h4>
									<?php $permissionsData = [ 'funcData' => ['staff_id'=> isset($member) ? $member->staffid : null ] ];
									if(isset($member)) {
										$permissionsData['member'] = $member;
									}
									$this->load->view('admin/staff/permissions', $permissionsData); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="btn-bottom-toolbar text-right btn-toolbar-container-out">
					<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				</div>
			<?php echo form_close(); ?>
			<?php if(isset($member)){ ?>
				<div class="col-md-7 small-table-right-col">
					<?php /* 
					<div class="panel_s">
						<div class="panel-body">
							<h4 class="no-margin">
								<?php echo _l('staff_add_edit_notes'); ?>
							</h4>
							<hr class="hr-panel-heading" />
							<a href="#" class="btn btn-success" onclick="slideToggle('.usernote'); return false;"><?php echo _l('new_note'); ?></a>
							<div class="clearfix"></div>
							<hr class="hr-panel-heading" />
							<div class="mbot15 usernote hide inline-block full-width">
								<?php echo form_open(admin_url('misc/add_note/'.$member->staffid . '/staff')); ?>
									<?php echo render_textarea('description','staff_add_edit_note_description','',array('rows'=>5)); ?>
									<button class="btn btn-info pull-right mbot15"><?php echo _l('submit'); ?></button>
								<?php echo form_close(); ?>
							</div>
							<div class="clearfix"></div>
							<div class="mtop15">
								<table class="table dt-table scroll-responsive" data-order-col="2" data-order-type="desc">
									<thead>
										<tr>
											<th width="50%"><?php echo _l('staff_notes_table_description_heading'); ?></th>
											<th><?php echo _l('staff_notes_table_addedfrom_heading'); ?></th>
											<th><?php echo _l('staff_notes_table_dateadded_heading'); ?></th>
											<th><?php echo _l('options'); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($user_notes as $note){ ?>
											<tr>
												<td width="50%">
													<div data-note-description="<?php echo $note['id']; ?>">
														<?php echo check_for_links($note['description']); ?>
													</div>
													<div data-note-edit-textarea="<?php echo $note['id']; ?>" class="hide inline-block full-width">
														<textarea name="description" class="form-control" rows="4"><?php echo clear_textarea_breaks($note['description']); ?></textarea>
														<div class="text-right mtop15">
															<button type="button" class="btn btn-default" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
															<button type="button" class="btn btn-info" onclick="edit_note(<?php echo $note['id']; ?>);"><?php echo _l('update_note'); ?></button>
														</div>
													</div>
												</td>
												<td><?php echo $note['firstname'] . ' ' . $note['lastname']; ?></td>
												<td data-order="<?php echo $note['dateadded']; ?>"><?php echo _dt($note['dateadded']); ?></td>
												<td>
													<?php if($note['addedfrom'] == get_staff_user_id() || has_permission('staff','','delete')){ ?>
														<a href="#" class="btn btn-default btn-icon" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><i class="fa fa-pencil-square-o"></i></a>
														<a href="<?php echo admin_url('misc/delete_note/'.$note['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
													<?php } ?>
												</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="panel_s">
						<div class="panel-body">
							<h4 class="no-margin">
								<?php echo _l('task_timesheets'); ?> & <?php echo _l('als_reports'); ?>
							</h4>
							<hr class="hr-panel-heading" />
							<?php echo form_open($this->uri->uri_string(),array('method'=>'GET')); ?>
								<?php echo form_hidden('filter','true'); ?>
								<div class="row">
									<div class="col-md-6">
										<div class="select-placeholder">
											<select name="range" id="range" class="selectpicker" data-width="100%">
												<option value="this_month" <?php if(!$this->input->get('range') || $this->input->get('range') == 'this_month'){echo 'selected';} ?>><?php echo _l('staff_stats_this_month_total_logged_time'); ?></option>
												<option value="last_month" <?php if($this->input->get('range') == 'last_month'){echo 'selected';} ?>><?php echo _l('staff_stats_last_month_total_logged_time'); ?></option>
												<option value="this_week" <?php if($this->input->get('range') == 'this_week'){echo 'selected';} ?>><?php echo _l('staff_stats_this_week_total_logged_time'); ?></option>
												<option value="last_week" <?php if($this->input->get('range') == 'last_week'){echo 'selected';} ?>><?php echo _l('staff_stats_last_week_total_logged_time'); ?></option>
												<option value="period" <?php if($this->input->get('range') == 'period'){echo 'selected';} ?>><?php echo _l('period_datepicker'); ?></option>
											</select>
										</div>
										<div class="row mtop15">
											<div class="col-md-12 period <?php if($this->input->get('range') != 'period'){echo 'hide';} ?>">
												<?php echo render_date_input('period-from','',$this->input->get('period-from')); ?>
											</div>
											<div class="col-md-12 period <?php if($this->input->get('range') != 'period'){echo 'hide';} ?>">
												<?php echo render_date_input('period-to','',$this->input->get('period-to')); ?>
											</div>
										</div>
									</div>
									<div class="col-md-2 text-right">
										<button type="submit" class="btn btn-success apply-timesheets-filters"><?php echo _l('apply'); ?></button>
									</div>
								</div>
							<?php echo form_close(); ?>
							<hr class="hr-panel-heading" />
							<table class="table dt-table scroll-responsive">
								<thead>
									<th><?php echo _l('task'); ?></th>
									<th><?php echo _l('timesheet_start_time'); ?></th>
									<th><?php echo _l('timesheet_end_time'); ?></th>
									<th><?php echo _l('task_relation'); ?></th>
									<th><?php echo _l('staff_hourly_rate'); ?> (<?php echo _l('als_staff'); ?>)</th>
									<th><?php echo _l('time_h'); ?></th>
									<th><?php echo _l('time_decimal'); ?></th>
									<th data-sortable="false"></th>
								</thead>
								<tbody>
									<?php $total_logged_time = 0;
									foreach($timesheets as $t){ ?>
										<tr>
											<td><a href="#" onclick="init_task_modal(<?php echo $t['task_id']; ?>); return false;"><?php echo $t['name']; ?></a></td>
											<td data-order="<?php echo $t['start_time']; ?>"><?php echo _dt($t['start_time'], true); ?></td>
											<td data-order="<?php echo $t['end_time']; ?>">
                              <?php
                                 // Allow admins or timer user to stop forgotten timers by staff member
                                 if($t['not_finished'] && (is_admin() || $t['staff_id'] === get_staff_user_id())) {
                                    ?>
                                          <a href="#"
                                          <?php
                                          // Do not show the note popover when there is no associated task
                                          // The user will be able to add note and select task in the popup window that will open
                                          if($t['task_id'] != 0){ ?>
                                          data-toggle="popover"
                                          data-placement="bottom"
                                          data-html="true"
                                          data-trigger="manual"
                                          data-title="<?php echo _l('note'); ?>"
                                          data-content='<?php echo render_textarea('timesheet_note'); ?><button type="button"
                                          onclick="timer_action(this, <?php echo $t['task_id']; ?>, <?php echo $t['id']; ?>, 1);" class="btn btn-info btn-xs"><?php echo _l('save'); ?></button>'
                                          onclick="return false;"
                                          <?php } else { ?>
                                          onclick="timer_action(this, <?php echo $t['task_id']; ?>, <?php echo $t['id']; ?>, 1); return false;"
                                          <?php } ?>
                                          class="text-danger"
                                          >
                                          <i class="fa fa-clock-o"></i>
                                          <?php echo _l('task_stop_timer'); ?>
                                          </a>
                                    <?php
                                 } else if($t['not_finished']) {
                                    echo '<b>' . _l('timer_not_stopped_yet') . '</b>';
                                 } else {
                                    echo _dt($t['end_time'], true);
                                 }
                              ?>
											</td>
											<td>
												<?php $rel_data   = get_relation_data($t['rel_type'], $t['rel_id']);
												$rel_values = get_relation_values($rel_data, $t['rel_type']);
												echo '<a href="' . $rel_values['link'] . '">' . $rel_values['name'].'</a>'; ?>
											</td>
											<td><?php echo app_format_money($t['hourly_rate'], $base_currency); ?></td>
											<td>
												<?php echo '<b>'.seconds_to_time_format($t['end_time'] - $t['start_time']).'</b>'; ?>
											</td>
											<td data-order="<?php echo sec2qty($t['total']); ?>">
												<?php $total_logged_time += $t['total'];
												echo '<b>'.sec2qty($t['total']).'</b>'; ?>
											</td>
											<td>
												<?php if(!$t['billed']) {
													if(has_permission('tasks','','delete') || (has_permission('projects','','delete') && $t['rel_type'] == 'project') || $t['staff_id'] == get_staff_user_id()) {
														echo '<a href="'.admin_url('tasks/delete_timesheet/'.$t['id']).'" class="pull-right text-danger mtop5"><i class="fa fa-remove"></i></a>';
													}
												} ?>
											</td>
										</tr>
									<?php } ?>
								</tbody>
								<tfoot>
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td align="right"><?php echo '<b>' . _l('total_by_hourly_rate') .':</b> '. app_format_money((sec2qty($total_logged_time) * $member->hourly_rate), $base_currency); ?></td>
										<td align="right">
											<?php echo '<b>'._l('total_logged_hours_by_staff') . ':</b> ' . seconds_to_time_format($total_logged_time); ?>
										</td>
										<td align="right">
											<?php echo '<b>'._l('total_logged_hours_by_staff') . ':</b> ' . sec2qty($total_logged_time); ?>
										</td>
										<td></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
					*/ ?>
					<div class="panel_s">
						<div class="panel-body">
							<h4 class="no-margin">
								<?php echo _l('projects'); ?>
							</h4>
							<hr class="hr-panel-heading" />
							<div class="_filters _hidden_inputs hidden staff_projects_filter">
								<?php echo form_hidden('staff_id',$member->staffid); ?>
							</div>
							<?php render_datatable(array(
								_l('project_name'),
								_l('project_start_date'),
								_l('project_deadline'),
								_l('project_status'),
							),'staff-projects'); ?>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
		<div class="btn-bottom-pusher"></div>
	</div>
</div>
<?php init_tail(); ?>
<style>
.filter-option .filter-option-inner .filter-option-inner-inner .text-muted,.dropdown-menu.inner  .text .text-muted{
	display:none;
}
</style>
<script>
$(function() {
	$('select[name="action_for"]').on('change', function() {
		var action_for = $(this).val();
		if(action_for == 'Active') {
			$('.rollback').show();
		} else {
			$('.rollback').hide();
		}
		var deavite_re_assign = $('#deavite_re_assign');
		var deavite_re_assign_div = deavite_re_assign.parent().parent();
		var deavite_follow = $('#deavite_follow');
		var deavite_follow_div = deavite_follow.parent().parent();
		if(action_for == 'Deactivate'){
			deavite_re_assign_div.removeClass('hide');
			deavite_follow_div.removeClass('hide');
		}else{
			$('#deavite_re_assign option:selected').attr('selected', false);
			$("#deavite_re_assign option[value='']").attr('selected', true)
			deavite_re_assign_div.addClass('hide');
			$('#deavite_follow option:selected').attr('selected', false);
			$("#deavite_follow option[value='']").attr('selected', true)
			deavite_follow_div.addClass('hide');
		}
	});
	$('select[name="action_for"]').trigger("change");
	$('select[name="role"]').on('change', function() {
		var roleid = $(this).val();
		init_roles_permissions(roleid, true);
		var reporting_to = $('#reporting_to');
		
		var reporting_to_div = reporting_to.parent().parent();
		if(roleid == 1){
			reporting_to.val('');
			$('#reporting_to option:selected').attr('selected', false);
			$("#reporting_to option[value='']").attr('selected', true);
			reporting_to_div.addClass('hide');
			$('#administrator').parent().removeClass('hide');
		}else{
			$('#administrator').parent().addClass('hide');
			reporting_to_div.removeClass('hide');
		}
		// if(roleid == 2){
		// 	$("#reporting_to option[data-subtext='2']").css('display', 'none');
		// }else{
		// 	$("#reporting_to option[data-subtext='2']").css('display', '');
		// }
		reporting_to.selectpicker('refresh');
	});
	$('select[name="designation"]').on('change', function() {
		var roleid = $('#designation option:selected').attr('data-subtext');
		// $(this).prop('data-subtext');
		$("#role option[value='"+roleid+"']").attr('selected', true)
		$('select[name="role"]').val(roleid);
		$('select[name="role"]').prop('disabled', true);
		$('select[name="role"]').trigger("change");
		//init_roles_permissions(roleid, true);
	});
	
	$('input[name="administrator"]').on('change', function() {
		var checked = $(this).prop('checked');
		var isNotStaffMember = $('.is-not-staff');
		var reporting_to = $('#reporting_to');
		var reporting_to_div = reporting_to.parent().parent();
		if (checked == true) {
			reporting_to.val('0');
			$('#reporting_to option:selected').attr('selected', false);
			$("#reporting_to option[value='']").attr('selected', true)
			isNotStaffMember.addClass('hide');
			reporting_to_div.addClass('hide');
			$('.roles').find('input').prop('disabled', true).prop('checked', false);
		}
		else {
			isNotStaffMember.removeClass('hide');
			reporting_to_div.removeClass('hide');
			isNotStaffMember.find('input').prop('checked', false);
			$('.roles').find('.capability').not('[data-not-applicable="true"]').prop('disabled', false)
		}
	});
	$('select[name="designation"]').trigger("change");
	$('#is_not_staff').on('change', function() {
		var checked = $(this).prop('checked');
		var row_permission_leads = $('tr[data-name="leads"]');
		if (checked == true) {
			row_permission_leads.addClass('hide');
			row_permission_leads.find('input').prop('checked', false);
		}
		else {
			row_permission_leads.removeClass('hide');
		}
	});

	init_roles_permissions();

	appValidateForm($('.staff-form'), {
		firstname: 'required',
		lastname: 'required',
		username: 'required',
		designation: 'required',
		emp_id: 'required',
		password: {
			required: {
				depends: function(element) {
					return ($('input[name="isedit"]').length == 0) ? true : false
				}
			}
		},
		email: {
			required: true,
			email: true,
			remote: {
				url: site_url + "/admin/misc/staff_email_exists",
				type: 'post',
				data: {
					email: function() {
						return $('input[name="email"]').val();
					},
					
					memberid: function() {
						return $('input[name="memberid"]').val();
					}
				}
			}
		},
		emp_id: {
			required: true,
			remote: {
				url: site_url + "/admin/misc/staff_emp_id_exists",
				type: 'post',
				data: {
					emp_id: function() {
						return $('input[name="emp_id"]').val();
					},
					memberid: function() {
						return $('input[name="memberid"]').val();
					}
				}
			}
		}
	});
});

$('#deavite_follow').change(function() {
		var df = $(this).val();
		if(df) {
			$('#deavite_re_assign option:selected').removeAttr('selected');
			$('.deavite_re_assign .selectpicker').selectpicker('refresh');
			$('.deavite_re_assign').hide();
		} else {
			$('#deavite_re_assign option:selected').removeAttr('selected');
			$('.deavite_re_assign .selectpicker').selectpicker('refresh');
			$('.deavite_re_assign').show();
		}
});
$('#deavite_re_assign').change(function() {
		var df = $(this).val();
		if(df) {
			$('#deavite_follow option:selected').removeAttr('selected');
			$('.deavite_follow .selectpicker').selectpicker('refresh');
			$('.deavite_follow').hide();
		} else {
			$('#deavite_follow option:selected').removeAttr('selected');
			$('.deavite_follow .selectpicker').selectpicker('refresh');
			$('.deavite_follow').show();
		}
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput-jquery.min.js"></script>
<script>

    // -----Country Code Selection
    $("#phonenumber").intlTelInput({
        initialCountry: "<?php echo ( isset($member) ? $member->phone_country_code : 'IN'); ?>",
        separateDialCode: true,
        // utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.4/js/utils.js"
    });
    $("#phonenumber_iti_wrapper .iti__flag-container ul li").click(function(){

        var country_code =$(this).attr('data-country-code').toUpperCase();
        $("#phone_country_code").val(country_code);
    });

</script>

</body>
</html>