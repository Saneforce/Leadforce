<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.contactsdiv1 .form-group {
    margin-bottom: 0px;
}
</style>
<!-- Copy Project -->
<div class="modal fade" id="copy_project" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('projects/copy/'.(isset($project) ? $project->id : '')),array('id'=>'copy_form','data-copy-url'=>admin_url('projects/copy/'))); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo _l('copy_project'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                <input type="hidden" id="projectid" value="<?php echo $project->id; ?>">
            <div class="col-md-5" style=" display:none">
                <div class="panel_s">
                    <div class="panel-body" id="project-settings-area">
                        <h4 class="no-margin">
                            <?php echo _l('project_settings'); ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <?php foreach($settings as $setting){

            $checked = ' checked';
            if(isset($project)){
                if($project->settings->{$setting} == 0){
                    $checked = '';
                }
            } else {
                foreach($last_project_settings as $last_setting) {
                    if($setting == $last_setting['name']){
                        // hide_tasks_on_main_tasks_table is not applied on most used settings to prevent confusions
                        if($last_setting['value'] == 0 || $last_setting['name'] == 'hide_tasks_on_main_tasks_table'){
                            $checked = '';
                        }
                    }
                }
                if(count($last_project_settings) == 0 && $setting == 'hide_tasks_on_main_tasks_table') {
                    $checked = '';
                }
            } ?>
                        <?php if($setting != 'available_features'){ ?>
                        <div class="checkbox">
                            <input type="checkbox" name="settings[<?php echo $setting; ?>]" <?php echo $checked; ?>
                                id="<?php echo $setting; ?>">
                            <label for="<?php echo $setting; ?>">
                                <?php if($setting == 'hide_tasks_on_main_tasks_table'){ ?>
                                <?php echo _l('hide_tasks_on_main_tasks_table'); ?>
                                <?php } else{ ?>
                                <?php echo _l('project_allow_client_to',_l('project_setting_'.$setting)); ?>
                                <?php } ?>
                            </label>
                        </div>
                        <?php } else { ?>
                        <div class="form-group mtop15 select-placeholder project-available-features">
                            <label for="available_features"><?php echo _l('visible_tabs'); ?></label>
                            <select name="settings[<?php echo $setting; ?>][]" id="<?php echo $setting; ?>"
                                multiple="true" class="selectpicker" id="available_features" data-width="100%"
                                data-actions-box="true" data-hide-disabled="true">
                                <?php foreach(get_project_tabs_admin() as $tab) {
                            $selected = '';
                            if(isset($tab['collapse'])){ ?>
                                <optgroup label="<?php echo $tab['name']; ?>">
                                    <?php foreach($tab['children'] as $tab_dropdown) {
                                        $selected = '';
                                        if(isset($project) && (
                                            (isset($project->settings->available_features[$tab_dropdown['slug']])
                                                && $project->settings->available_features[$tab_dropdown['slug']] == 1)
                                            || !isset($project->settings->available_features[$tab_dropdown['slug']]))) {
                                            $selected = ' selected';
                                    } else if(!isset($project) && count($last_project_settings) > 0) {
                                        foreach($last_project_settings as $last_project_setting) {
                                            if($last_project_setting['name'] == $setting) {
                                                if(isset($last_project_setting['value'][$tab_dropdown['slug']])
                                                    && $last_project_setting['value'][$tab_dropdown['slug']] == 1) {
                                                    $selected = ' selected';
                                            }
                                        }
                                    }
                                } else if(!isset($project)) {
                                    $selected = ' selected';
                                }
                                ?>
                                    <option value="<?php echo $tab_dropdown['slug']; ?>"
                                        <?php echo $selected; ?><?php if(isset($tab_dropdown['linked_to_customer_option']) && is_array($tab_dropdown['linked_to_customer_option']) && count($tab_dropdown['linked_to_customer_option']) > 0){ ?>
                                        data-linked-customer-option="<?php echo implode(',',$tab_dropdown['linked_to_customer_option']); ?>"
                                        <?php } ?>><?php echo $tab_dropdown['name']; ?></option>
                                    <?php } ?>
                                </optgroup>
                                <?php } else {
                        if(isset($project) && (
                            (isset($project->settings->available_features[$tab['slug']])
                             && $project->settings->available_features[$tab['slug']] == 1)
                            || !isset($project->settings->available_features[$tab['slug']]))) {
                            $selected = ' selected';
                    } else if(!isset($project) && count($last_project_settings) > 0) {
                        foreach($last_project_settings as $last_project_setting) {
                            if($last_project_setting['name'] == $setting) {
                                if(isset($last_project_setting['value'][$tab['slug']])
                                    && $last_project_setting['value'][$tab['slug']] == 1) {
                                    $selected = ' selected';
                            }
                        }
                    }
                } else if(!isset($project)) {
                    $selected = ' selected';
                }
                ?>
                                <option value="<?php echo $tab['slug']; ?>"
                                    <?php if($tab['slug'] =='project_overview'){echo ' disabled selected';} ?>
                                    <?php echo $selected; ?>
                                    <?php if(isset($tab['linked_to_customer_option']) && is_array($tab['linked_to_customer_option']) && count($tab['linked_to_customer_option']) > 0){ ?>
                                    data-linked-customer-option="<?php echo implode(',',$tab['linked_to_customer_option']); ?>"
                                    <?php } ?>>
                                    <?php echo $tab['name']; ?>
                                </option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <?php } ?>
                        <hr class="no-margin" />
                        <?php } ?>
                    </div>
                </div>
            </div>
                    <div class="col-md-12">
                        
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" class="copy" name="tasks" id="c_tasks" checked>
                            <label for="c_tasks"><?php echo _l('tasks'); ?></label>
                        </div>
                        <div class="checkbox checkbox-primary mleft10 tasks-copy-option">
                            <input type="checkbox" name="tasks_include_checklist_items" id="tasks_include_checklist_items" checked>
                            <label for="tasks_include_checklist_items"><small><?php echo _l('copy_project_task_include_check_list_items'); ?></small></label>
                        </div>
                        <div class="checkbox checkbox-primary mleft10 tasks-copy-option">
                            <input type="checkbox" name="task_include_assignees" id="task_include_assignees" checked>
                            <label for="task_include_assignees"><small><?php echo _l('copy_project_task_include_assignees'); ?></small></label>
                        </div>
                        <div class="checkbox checkbox-primary mleft10 tasks-copy-option">
                            <input type="checkbox" name="task_include_followers" id="copy_project_task_include_followers" checked>
                            <label for="copy_project_task_include_followers"><small><?php echo _l('copy_project_task_include_followers'); ?></small></label>
                        </div>
                        <!-- <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="milestones" id="c_milestones" checked>
                            <label for="c_milestones"><?php echo _l('project_milestones'); ?></label>
                        </div> -->
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="members" id="c_members" class="copy" checked>
                            <label for="c_members"><?php echo _l('project_members'); ?></label>
                        </div>
                        <hr />
                        <div class="copy-project-tasks-status-wrapper">
                            <p class="bold"><?php echo _l('copy_project_tasks_status'); ?></p>
                            <?php foreach($task_statuses as $cp_task_status){ ?>
                                <div class="radio radio-primary">
                                    <input type="radio" name="copy_project_task_status" value="<?php echo $cp_task_status['id']; ?>" id="cp_task_status_<?php echo $cp_task_status['id']; ?>"<?php if($cp_task_status['id'] == '1'){echo ' checked';} ?>>
                                    <label for="cp_task_status_<?php echo $cp_task_status['id']; ?>"><?php echo $cp_task_status['name']; ?></label>
                                </div>
                            <?php } ?>
                            <hr />
                        </div>
                        
                        <?php 
                        $disable_type_edit = '';
                        if(isset($project)){
                            if($project->billing_type != 1){
                                if(total_rows(db_prefix().'tasks',array('rel_id'=>$project->id,'rel_type'=>'project','billable'=>1,'billed'=>1)) > 0){
                                    $disable_type_edit = 'disabled';
                                }
                            }
                        }
                        ?>
                        
                        <div
                            class="form-group select-placeholder clientiddiv form-group-select-input-groups_in[] input-group-select">

                            <label for="clientid" class="control-label"><?php echo _l('project_customer'); ?></label>
                            <div class="input-group input-group-select select-groups_in[]">
                                <select id="clientid" name="clientid" data-live-search="true" data-width="100%"
                                    class="ajax-search"
                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php $selected = (isset($project) ? $project->clientid : '');
                             if($selected == ''){
                                 $selected = (isset($customer_id) ? $customer_id: '');
                             }
                             if($selected != ''){
                                $rel_data = get_relation_data('customer',$selected);
                                $rel_val = get_relation_values($rel_data,'customer');
                                echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                            } ?>

                                </select>
                                <div class="input-group-addon" style="opacity: 1;"><a href="#" data-toggle="modal"
                                        data-target="#clientid_add_modal"><i class="fa fa-plus"></i></a></div>
                            </div>

                        </div>
                        <div class="form-group select-placeholder contactsdiv1" style="margin-bottom:0px;">
                            <label for="project_contacts_selectpicker"
                                class="control-label"><small class="req text-danger">* </small><?php echo _l('project_contacts'); ?></label>
                            <div class="input-group input-group-select " style="margin-bottom:10px;">

                                <?php 
						   $selected = array();
            foreach($contacts as $contact){
              array_push($selected,$contact['contacts_id']);
           }
           echo render_select('project_contacts1[]',$client_contacts,array('id',array('firstname','lastname')),false,$selected,array('multiple'=>true,'data-actions-box'=>true,'aria-describedby'=>'project_contacts-error'),array(),'','',false);
           
						  ?>

                                
                                <div class="input-group-addon" style="opacity: 1;"><a href="#" data-toggle="modal"
                                        data-target="#project_contacts_modal1"><i class="fa fa-plus"></i></a></div>
                            </div>
                        </div>
                        

<div class='row primarydiv1'>
    <div class="col-md-12">
        <div class="form-group select-placeholder">
            <label for="status"><small class="req text-danger">* </small><?php echo _l('project_primary_contacts'); ?></label>
            <div class="clearfix"></div>
            <select name="primary_contact1" id="primary_contact1" class="selectpicker" data-width="100%"
                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                <option></option>
                <?php
                    foreach($client_contacts as $cckey => $ccval){ 
                        foreach($contacts as $scckey => $sccval){
                            if( $sccval['contacts_id'] == $ccval['id']){
                                $selected = '';
                                if( $sccval['is_primary'] == 1){
                                    $selected = 'selected';
                                }
                                echo '<option value="'.$ccval['id'].'" '.$selected.' >'.$ccval['firstname'].' '.$ccval['lastname'].'</option>';
                            }
                        }
                    } 
                ?>
                <?php //foreach($statuses as $status){ ?>
                <!-- <option value="<?php echo $status['id']; ?>"
                    <?php if(!isset($project) && $status['id'] == 2 || (isset($project) && $project->status == $status['id'])){echo 'selected';} ?>>
                    <?php echo $status['name']; ?></option> -->
                <?php //} ?>
            </select>
        </div>
    </div>
</div>

                        <div class="form-group" style=" display:none;">
                            <div class="checkbox checkbox-success">
                                <input type="checkbox"
                                    <?php if((isset($project) && $project->progress_from_tasks == 1) || !isset($project)){echo 'checked';} ?>
                                    name="progress_from_tasks" id="progress_from_tasks">
                                <label
                                    for="progress_from_tasks"><?php echo _l('calculate_progress_through_tasks'); ?></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 pipelineid">
                                <?php
                                    $assigned_attrs = array();
                                    $pipelineleadselected = (isset($project) ? $project->pipeline_id : '');
                                    echo render_select('pipeid', $pipelines, array('id', 'name'), 'pipeline', $pipelineleadselected, $assigned_attrs);
                                ?>
                            </div>

                        </div>
                        <div class='row'>
                            <div class="col-md-12 form_status">
                                <div class="form-group select-placeholder">
                                    <label for="status"><small class="req text-danger">* </small><?php echo _l('project_status'); ?></label>
                                    <div class="clearfix"></div>
                                    <select name="status1" id="status1" class="selectpicker" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-none-selected-text="Nothing selected" >
                                        <option></option>
                                        <?php foreach($statuses as $status){ ?>
                                        <option value="<?php echo $status['id']; ?>"
                                            <?php if(!isset($project) && $status['id'] == 2 || (isset($project) && $project->status == $status['id'])){echo 'selected';} ?>>
                                            <?php echo $status['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php
                        
                        $exp = explode('admin/projects/project',$this->uri->uri_string());
                        //pr($exp); exit;
                        if($exp[1]) {
                         
                            if(isset($project) && $project->progress_from_tasks == 1){
                                $value = $this->projects_model->calc_progress_by_tasks($project->id);
                            } else if(isset($project) && $project->progress_from_tasks == 0){
                                $value = $project->progress;
                            } else {
                                $value = 0;
                            }
                    ?>
                        <!-- <label for=""><?php echo _l('project_progress'); ?> <span
                                class="label_progress"><?php echo $value; ?>%</span></label>
                        <?php echo form_hidden('progress',$value); ?>
                        <div class="project_progress_slider project_progress_slider_horizontal mbot15"></div> -->
                        <?php } ?>
                        <div class="row">
                            <!--  
                        <div class="col-md-6">
                            <div class="form-group select-placeholder">
                                <label for="billing_type"><?php echo _l('project_billing_type'); ?></label>
                                <div class="clearfix"></div>
                                <select name="billing_type" class="selectpicker" id="billing_type" data-width="100%" <?php echo $disable_type_edit ; ?> data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""></option>
                                    <option value="1" <?php if(isset($project) && $project->billing_type == 1 || !isset($project) && $auto_select_billing_type && $auto_select_billing_type->billing_type == 1){echo 'selected'; } ?>><?php echo _l('project_billing_type_fixed_cost'); ?></option>
                                    <option value="2" <?php if(isset($project) && $project->billing_type == 2 || !isset($project) && $auto_select_billing_type && $auto_select_billing_type->billing_type == 2){echo 'selected'; } ?>><?php echo _l('project_billing_type_project_hours'); ?></option>
                                    <option value="3" data-subtext="<?php echo _l('project_billing_type_project_task_hours_hourly_rate'); ?>" <?php if(isset($project) && $project->billing_type == 3 || !isset($project) && $auto_select_billing_type && $auto_select_billing_type->billing_type == 3){echo 'selected'; } ?>><?php echo _l('project_billing_type_project_task_hours'); ?></option>
                                </select>
                                <?php if($disable_type_edit != ''){
                                    echo '<p class="text-danger">'._l('cant_change_billing_type_billed_tasks_found').'</p>';
                                }
                                ?>
                            </div>
                        </div>
                          -->
                            <div class="col-md-6 form_teamleader1">
                                <?php 
                         $teamleaderselected = (isset($project) && !empty($project->teamleader)) ? $project->teamleader : '';
                       
                        
                            if(isset($project)) {
								if(!isset($ownerHierarchy)){
									$ownerHierarchy = array();
								}
                                if(in_array(get_staff_user_id(),$ownerHierarchy) || $project->teamleader == get_staff_user_id() || is_admin(get_staff_user_id())){
                                    echo render_select('teamleader1', $teamleaders, array('staffid', array('firstname', 'lastname')), 'teamleader', $teamleaderselected, $assigned_attrs);
								}
                                else{
                                    echo render_select('teamleader1', $teamleaders, array('staffid', array('firstname', 'lastname')), 'teamleader', $teamleaderselected, array('disabled'=>true));
								}
                            } else {
                                echo render_select('teamleader1', $teamleaders, array('staffid', array('firstname', 'lastname')), 'teamleader', $teamleaderselected, $assigned_attrs);
                            }
                            
                        
?>
                            </div>
                            <div class="col-md-6 form_assigned1">
                                <?php
                         $selected = array();
                         if(isset($project_members)){
                            foreach($project_members as $member){
                                array_push($selected,$member['staff_id']);
                            }
                         } 
                        
                        if(isset($project)) {
                            if(in_array(get_staff_user_id(),$ownerHierarchy) || $project->teamleader == get_staff_user_id() || is_admin(get_staff_user_id()))
                                echo render_select('project_members[]',$staff,array('staffid',array('firstname','lastname')),'project_members',$selected,array('multiple'=>true,'class'=>'formassigned1','data-actions-box'=>true),array(),'','',false);
                            else
                                echo render_select('project_members[]',$staff,array('staffid',array('firstname','lastname')),'project_members',$selected,array('multiple'=>true,'class'=>'formassigned1','data-actions-box'=>true,'disabled'=>true),array(),'','',false);
                        } else {
                            echo render_select('project_members[]',$staff,array('staffid',array('firstname','lastname')),'project_members',$selected,array('multiple'=>true,'class'=>'formassigned1','data-actions-box'=>true),array(),'','',false);
                        }
                        
                        ?>
                            </div>
                            <div class="col-md-6">
                                <?php $start_date = (isset($project) ? _d($project->start_date) : _d(date('Y-m-d'))); ?>
                                <?php $deadline = (isset($project) ? _d($project->deadline) : ''); ?>
                                <?php echo render_date_input('start_date1','project_start_date',$start_date,array('data-date-end-date'=>$deadline,'readonly'=>'readonly')); ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_date_input('deadline1','project_deadline',$deadline,array('data-date-min-date'=>$start_date,'readonly'=>'readonly')); ?>
                            </div>
                        <?php if(isset($project) && $project->date_finished != null && $project->status == 4) { ?>
                        <?php echo render_datetime_input('date_finished','project_completed_date',_dt($project->date_finished)); ?>
                        <?php } ?>
                            <div class="col-md-12">
                                <p class="bold"><?php echo _l('project_description'); ?></p>
                                <?php $contents = ''; if(isset($project)){$contents = $project->description;} ?>
                                <?php echo render_textarea('description1','',$contents,array(),array(),'','tinymce'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" data-form="#copy_form" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"  class="btn btn-info"><?php echo _l('copy_project'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- Copy Project end -->
<script>
// Copy project modal and set url if ID is passed manually eq from project list area
function copy_project(id) {

    $('#copy_project').modal('show');

    if (typeof(id) != 'undefined') {
        $('#copy_form').attr('action', $('#copy_form').data('copy-url') + id);
    }

    appValidateForm($('#copy_form'), {
        clientid: 'required',
        'project_contacts1[]': 'required',
        primary_contact1: 'required',
        pipeid: 'required',
        status1: 'required',
        teamleader1: 'required',
        // 'project_members[]': 'required',
        start_date1: 'required',
        clientid_copy_project: 'required',
    });

    var copy_members = $('#c_members');
    var copy_tasks = $('input[name="tasks"].copy');
    var copy_assignees_and_followers = $('input[name="task_include_assignees"],input[name="task_include_followers"]');

    copy_members.off('change');
    copy_tasks.off('change');
    copy_assignees_and_followers.off('change');

        copy_members.on('change',function(){
            if(!$(this).prop('checked')) {
                copy_assignees_and_followers.prop('checked',false)
           }
       });

        copy_tasks.on('change', function() {
          var checked = $(this).prop('checked');
          if (checked) {

              var copy_assignees = $('input[name="task_include_assignees"]').prop('checked');
              var copy_followers = $('input[name="task_include_followers"]').prop('checked');

              if (copy_assignees || copy_followers) {
                  $('input[name="members"].copy').prop('checked', true);
              }

              $('.copy-project-tasks-status-wrapper').removeClass('hide');
              $('.tasks-copy-option').removeClass('hide');

          } else {
              $('.copy-project-tasks-status-wrapper').addClass('hide');
              $('.tasks-copy-option').addClass('hide');
          }
      });

      copy_assignees_and_followers.on('change', function() {
          var checked = $(this).prop('checked');
          if (checked == true) {
              $('input[name="members"].copy').prop('checked', true);
          }
      });
}

</script>
