<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
   <div class="col-md-6 border-right project-overview-left">
      <div class="row">
       <div class="col-md-12">
         <p class="project-info bold font-size-14">
            <?php echo _l('overview'); ?>
         </p>
      </div>
      <?php if(count($project->shared_vault_entries) > 0){ ?>
      <?php $this->load->view('admin/clients/vault_confirm_password'); ?>
      <div class="col-md-12">
         <p class="bold">
           <a href="#" onclick="slideToggle('#project_vault_entries'); return false;">
             <i class="fa fa-cloud"></i> <?php echo _l('project_shared_vault_entry_login_details'); ?>
          </a>
       </p>
       <div id="project_vault_entries" class="hide">
         <?php foreach($project->shared_vault_entries as $vault_entry){ ?>
         <div class="row" id="<?php echo 'vaultEntry-'.$vault_entry['id']; ?>">
            <div class="col-md-6">
               <p class="mtop5">
                  <b><?php echo _l('server_address'); ?>: </b><?php echo $vault_entry['server_address']; ?>
               </p>
               <p>
                  <b><?php echo _l('port'); ?>: </b><?php echo !empty($vault_entry['port']) ? $vault_entry['port'] : _l('no_port_provided'); ?>
               </p>
               <p>
                  <b><?php echo _l('vault_username'); ?>: </b><?php echo $vault_entry['username']; ?>
               </p>
               <p class="no-margin">
                  <b><?php echo _l('vault_password'); ?>: </b><span class="vault-password-fake">
                     <?php echo str_repeat('&bull;',10);?>  </span><span class="vault-password-encrypted"></span> <a href="#" class="vault-view-password mleft10" data-toggle="tooltip" data-title="<?php echo _l('view_password'); ?>" onclick="vault_re_enter_password(<?php echo $vault_entry['id']; ?>,this); return false;"><i class="fa fa-lock" aria-hidden="true"></i></a>
                  </p>
               </div>
               <div class="col-md-6">
                  <?php if(!empty($vault_entry['description'])){ ?>
                  <p>
                     <b><?php echo _l('vault_description'); ?>: </b><br /><?php echo $vault_entry['description']; ?>
                  </p>
                  <?php } ?>
               </div>
            </div>
            <hr class="hr-10" />
            <?php } ?>
         </div>
         <hr class="hr-panel-heading project-area-separation" />
      </div>
      <?php } ?>
      <div class="col-md-12">


         <table class="table no-margin project-overview-table">
            <tbody>
              <tr class="project-overview-customer">
                  <td class="bold"><?php echo _l('project_name'); ?></td>
                  <td class="name">
                  <div class="data_display">
                     <span class="updated_text">
                     <?php echo $project->name; ?>
                     </span>
                     <?php if(is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(),$ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader,$my_staffids) && !in_array($project->teamleader,$viewIds))) { ?>
                        <button class="btn btn-link pull-right no-padding data_display_btn" data-val="name" ><i class="fa fa-pencil"></i></button>
                     <?php } ?>
                     </div>

                     <div class="data_edit" style=" display:none;">
                     <div class="input-group date">
                        <input type="text" id="name" name="name" class="form-control"  value=" <?php echo (isset($project) ? $project->name : 'Deal '); ?>" autocomplete="off" aria-invalid="false">
                        <div class="input-group-addon" style="opacity: 1;">
                        <a class=" data_edit_btn" data-val="name"><i class="fa fa-check"></i></a>
                        </div>
                        </div>
                        <div id="company_exists_info" class="hide"></div>
                      </div>
                      
                  </td>
                  </tr>
				  <?php if(!empty($need_fields) && in_array("clientid", $need_fields)){ ?>
              <tr class="project-overview-customer">
                  <td class="bold"><?php echo _l('project_customer'); ?></td>
                  <td class="clientid">
                     <div class="data_display">
                     <span class="updated_text">
                         <input type="hidden" id="clid" value="<?php echo $project->clientid; ?>">
                        <a href="<?php echo admin_url(); ?>clients/client/<?php echo $project->clientid; ?>">
                           <?php echo $project->client_data->company; ?>
                        </a>
                        </span>
                        <?php if(is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(),$ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader,$my_staffids) && !in_array($project->teamleader,$viewIds))) { ?>
                        <button class="btn btn-link pull-right no-padding data_display_btn" data-val="clientid" ><i class="fa fa-pencil"></i></button>
                        <?php } ?>
                      </div>
                      <div class="data_edit" style=" display:none;">
                        <div class="form-group select-placeholder clientiddiv form-group-select-input-groups_in[] input-group-select">
                                 <div class="input-group input-group-select select-groups_in[]">
                                 <select id="clientid_copy_project" name="clientid_copy_project" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
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
                           <div class="input-group-addon" style="opacity: 1;"><a href="#" data-toggle="modal" data-target="#clientid_add_modal"><i class="fa fa-plus"></i></a></div>
                           <div class="input-group-addon" style="opacity: 1;"><a class=" data_edit_btn" data-val="clientid_copy_project"><i class="fa fa-check"></i></a></div>
                           </div>
                           
                        </div>
                      </div>

                  </td>

              </tr>
				  <?php }if(!empty($need_fields) && in_array("pipeline_id", $need_fields)){ ?>
              <tr class="project-overview-customer">
                  <td class="bold"><?php echo _l('pipeline'); ?></td>
                  <td class="pipeline_id">
                  
                  <div class="data_display">
                     <span class="updated_text">
                        <?php echo (isset($pipeline)&&isset($pipeline->name))?$pipeline->name:''; ?>
                     </span>
                     <?php if(is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(),$ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader,$my_staffids) && !in_array($project->teamleader,$viewIds))) { ?>
                        <button class="btn btn-link pull-right no-padding data_display_btn" data-val="pipeline_id" ><i class="fa fa-pencil"></i></button>
                     <?php } ?> 
                    </div>
                      <div class="data_edit" style=" display:none;">
                        <div class="form-group select-placeholder form-group-select-input-groups_in[] input-group-select">
                                 <div class="input-group input-group-select select-groups_in[]">
                                 
                                 <select id="pipeline_id" name="pipeline_id" data-live-search="true" data-width="100%"  class=" selectpicker _select_input_group">
                                    <?php 
                                    foreach($pipelines as $pikay => $pival){
                                       $selected = '';
                                       $pipeline_id = (isset($project) ? $project->pipeline_id : '');
                                       if($pipeline_id == $pival['id']){
                                          $selected = 'selected="selected"';
                                       }
                                       echo '<option value="'.$pival['id'].'" '.$selected.'>'.$pival['name'].'</option>';
                                    }
                                    ?>
                                 </select>
                           <div class="input-group-addon" style="opacity: 1;"><a class=" data_edit_btn" data-val="pipeline_id"><i class="fa fa-check"></i></a></div>
                           </div>
                           
                        </div>
                      </div>
                  </td>
              </tr>
				  <?php }if(!empty($need_fields) && in_array("teamleader", $need_fields)){?>
              <tr class="project-overview-customer">
                  <td class="bold"><?php echo _l('teamleader'); ?></td>
                  <td class="teamleader">
                  
                  
                  
                  <div class="data_display">
                     <span class="updated_text">
                     <?php echo (isset($teamleader)&&isset($teamleader->firstname))?($teamleader->firstname.' '.$teamleader->lastname):''; ?>
                     </span>
                     <?php if(is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(),$ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader,$my_staffids) && !in_array($project->teamleader,$viewIds))) { ?>
                        <button class="btn btn-link pull-right no-padding data_display_btn" data-val="teamleader" ><i class="fa fa-pencil"></i></button>
                        <?php } ?>
                      </div>
                      <div class="data_edit" style=" display:none;">
                        <div class="form-group select-placeholder form-group-select-input-groups_in[] input-group-select">
                                 <div class="input-group input-group-select select-groups_in[]">
                                 
                                 <select id="teamleader" name="teamleader" data-live-search="true" data-width="100%"  class=" selectpicker _select_input_group">
                                    <?php 
                                    foreach($teamleaders as $pikay => $pival){
                                       $selected = '';
                                       $teamleader = (isset($project) ? $project->teamleader : '');
                                       if($teamleader == $pival['staffid']){
                                          $selected = 'selected="selected"';
                                       }
                                       echo '<option value="'.$pival['staffid'].'" '.$selected.'>'.$pival['firstname'].' '.$pival['lastname'].'</option>';
                                    }
                                    ?>
                                 </select>
                                        <div class="input-group-addon" style="opacity: 1;"><a class=" data_edit_btn" data-val="teamleader"><i class="fa fa-check"></i></a></div>
                           </div>
                           
                        </div>
                      </div>
                  
                  </td>
              </tr>
				  <?php }?>
               <?php if(has_permission('projects','','create') || has_permission('projects','','edit')){ ?>
               <!-- <tr class="project-overview-billing">
                  <td class="bold"><?php echo _l('project_billing_type'); ?></td>
                  <td>
                     <?php
                     if($project->billing_type == 1){
                       $type_name = 'project_billing_type_fixed_cost';
                    } else if($project->billing_type == 2){
                       $type_name = 'project_billing_type_project_hours';
                    } else {
                       $type_name = 'project_billing_type_project_task_hours';
                    }
                    echo _l($type_name);
                    ?>
                 </td> 
                 <?php if($project->billing_type == 1 || $project->billing_type == 2){
                  echo '<tr>';
                  if($project->billing_type == 1){
                    echo '<td class="bold">'._l('project_total_cost').'</td>';
                    echo '<td>'.app_format_money($project->project_cost, $currency).'</td>';
                 } else {
                    echo '<td class="bold">'._l('project_rate_per_hour').'</td>';
                    echo '<td>'.app_format_money($project->project_rate_per_hour, $currency).'</td>';
                 }
                 echo '<tr>';
              }
           }
           ?>
           -->
		   <?php if(!empty($need_fields) && in_array("project_cost", $need_fields)){?>
           <tr class="project-overview-project_total_cost">
                  <td class="bold"><?php echo _l('project_total_cost'); ?></td>
                  <td class="project_total_cost">
                  <div class="data_display">
                     <span class="updated_text">
                     <?php echo app_format_money($project->project_cost, $currency); ?>
                     </span>
                     <?php if(is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(),$ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader,$my_staffids) && !in_array($project->teamleader,$viewIds))) { ?>
                        <button class="btn btn-link pull-right no-padding data_display_btn" data-val="project_total_cost" ><i class="fa fa-pencil"></i></button>
                     <?php } ?>  
                    </div>
                      <div class="data_edit" style=" display:none;">

                      
                       <div class="input-group date">
                        <input type="text" id="project_cost" name="project_cost" class="form-control"  value="<?php echo (isset($project) ? $project->project_cost : ''); ?>" autocomplete="off" aria-invalid="false">
                        <div class="input-group-addon" style="opacity: 1;">
                        <a class=" data_edit_btn" data-val="project_cost"><i class="fa fa-check"></i></a>
                        </div>
                        </div>
                        
                      </div>
                           
                        </div>
                      </div>
                  
                  </td>
              </tr>
		   <?php }if(!empty($need_fields) && in_array("status", $need_fields)){?>
            <!--    -->
           <tr class="project-overview-status">
            <td class="bold"><?php echo _l('project_status'); ?></td>
            <td class='status'>
            
            <div class="data_display">
                     <span class="updated_text">
                     <?php echo $project_status['name']; ?>
                     </span>
                     <?php if(is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(),$ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader,$my_staffids) && !in_array($project->teamleader,$viewIds))) { ?>
                        <button class="btn btn-link pull-right no-padding data_display_btn" data-val="status" ><i class="fa fa-pencil"></i></button>
                     <?php }?>
                      </div>
                      <div class="data_edit" style=" display:none;">
                        <div class="form-group select-placeholder form-group-select-input-groups_in[] input-group-select">
                                 <div class="input-group input-group-select select-groups_in[]">
                                 <select name="status" id="status" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php foreach($statuses as $status){ ?>
                                        <option value="<?php echo $status['id']; ?>" <?php if(!isset($project) && $status['id'] == 2 || (isset($project) && $project->status == $status['id'])){echo 'selected';} ?>><?php echo $status['name']; ?></option>
                                    <?php } ?>
                                </select>
                           <div class="input-group-addon" style="opacity: 1;"><a class=" data_edit_btn" data-val="status"><i class="fa fa-check"></i></a></div>
                           </div>
                           
                        </div>
                      </div>
            
            </td>
         </tr>
		   <?php }if(!empty($need_fields) && in_array("project_start_date", $need_fields)){?>
         <!-- <tr class="project-overview-date-created">
            <td class="bold"><?php echo _l('project_datecreated'); ?></td>
            <td><?php echo _d($project->project_created); ?></td>
         </tr> -->
         <tr class="project-overview-start-date">
            <td class="bold"><?php echo _l('project_start_date'); ?></td>
            <td><?php echo _d($project->start_date); ?></td>
         </tr>
		   <?php }if(!empty($need_fields) && in_array("project_deadline", $need_fields)){//if($project->deadline){ ?>
         <tr class="project-overview-deadline">
            <td class="bold"><?php echo _l('project_deadline'); ?></td>
            <td class='deadline'>
                        <div class="data_display">
                     <span class="updated_text">
                     <?php echo _d($project->deadline); ?>
                     </span>
                     <?php if(is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(),$ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader,$my_staffids) && !in_array($project->teamleader,$viewIds))) { ?>
                     <button class="btn btn-link pull-right no-padding data_display_btn" data-val="deadline" ><i class="fa fa-pencil"></i></button>
                     <?php } ?>
                     </div>

                     <div class="data_edit" style=" display:none;">
                     
                     <div class="input-group date">
                     <input type="text" id="deadline" name="deadline" class="form-control datepicker" data-date-min-date="<?php echo (isset($project) ? $project->start_date : ' '); ?>" value="<?php echo (isset($project) ? $project->deadline : ' '); ?>" autocomplete="off" readonly>
                        
                        <div class="input-group-addon" style="opacity: 1;"><a class=" data_edit_btn" data-val="deadline"><i class="fa fa-check"></i></a></div></div>
                        
                      </div>
           
            </td>
         </tr>
		   <?php }//} ?>
         <?php /* if($project->date_finished){ ?>
         <tr class="project-overview-date-finished">
            <td class="bold"><?php echo _l('project_completed_date'); ?></td>
            <td class="text-success date_finished">
            
            <div class="data_display">
                     <span class="updated_text">
                     <?php echo _d($project->date_finished); ?>
                     </span>
                     <button class="btn btn-link pull-right no-padding data_display_btn" data-val="date_finished" ><i class="fa fa-pencil"></i></button>
                     </div>

                     <div class="data_edit" style=" display:none;">
                     
                     <div class="input-group date">
                     <input type="text" id="date_finished" name="date_finished" class="form-control datepicker" data-date-min-date="<?php echo (isset($project) ? $project->start_date : ' '); ?>" value="<?php echo (isset($project) ? $project->date_finished : ' '); ?>" autocomplete="off">
                        
                        <div class="input-group-addon" style="opacity: 1;"><a class=" data_edit_btn" data-val="date_finished"><i class="fa fa-check"></i></a></div></div>
                        
                      </div>
            
            </td>
         </tr>
         <?php //} */?>
         <!--  
         <?php if($project->estimated_hours && $project->estimated_hours != '0'){ ?>
         <tr class="project-overview-estimated-hours">
            <td class="bold<?php if(hours_to_seconds_format($project->estimated_hours) < (int)$project_total_logged_time){echo ' text-warning';} ?>"><?php echo _l('estimated_hours'); ?></td>
            <td><?php echo str_replace('.', ':', $project->estimated_hours); ?></td>
         </tr>
         <?php } ?>
         <?php if(has_permission('projects','','create')){ ?>
         <tr class="project-overview-total-logged-hours">
            <td class="bold"><?php echo _l('project_overview_total_logged_hours'); ?></td>
            <td><?php echo seconds_to_time_format($project_total_logged_time); ?></td>
         </tr>
         -->
         <?php } ?>
         <?php $custom_fields = get_custom_fields('projects');
         if(count($custom_fields) > 0){ ?>
         <?php foreach($custom_fields as $field){ ?>
         <?php $value = get_custom_field_value($project->id,$field['id'],'projects');
         if($value == ''){continue;} ?>
         <tr>
            <td class="bold"><?php echo ucfirst($field['name']); ?></td>
            <td><?php echo $value; ?></td>
         </tr>
         <?php } ?>
         <?php } ?>
      </tbody>
   </table>
</div>

</div>
<?php $tags = get_tags_in($project->id,'project'); ?>
<?php if(count($tags) > 0){ ?>
<div class="clearfix"></div>
<div class="tags-read-only-custom project-overview-tags">
   <hr class="hr-panel-heading project-area-separation hr-10" />
   <?php echo '<p class="font-size-14"><b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b></p>'; ?>
   <input type="text" class="tagsinput read-only" id="tags" name="tags" value="<?php echo prep_tags_input($tags); ?>" data-role="tagsinput">
</div>
<div class="clearfix"></div>
<?php } if(!empty($need_fields) && in_array("description", $need_fields)){?>
<div class="tc-content project-overview-description">
   <hr class="hr-panel-heading project-area-separation" />
   <p class="bold font-size-14 project-info"><?php echo _l('project_description'); ?></p>
   <?php if(empty($project->description)){
      echo '<p class="text-muted no-mbot mtop15">' . _l('no_description_project') . '</p>';
   }
   ?>
   <?php if(has_permission('projects','','edit') || has_permission('projects','','create')){ ?>
    <?php if(is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(),$ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader,$my_staffids) && !in_array($project->teamleader,$viewIds))) { ?>
   <div class="inline-block pull-right  project-member-settings" data-toggle="tooltip" data-title="<?php echo _l('project_description'); ?>">
      <a href="#" data-toggle="modal" class="pull-right" data-target="#edit_description""><i class="fa fa-pencil"></i></a>
   </div>
   <?php } } echo check_for_links($project->description); ?>
</div>
<?php }if(!empty($need_fields) && in_array("project_members[]", $need_fields)){?>
<div class="team-members project-overview-team-members">
   <hr class="hr-panel-heading project-area-separation" />
   <?php if(has_permission('projects','','edit') || has_permission('projects','','create')){ ?>
    <?php if(is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(),$ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader,$my_staffids) && !in_array($project->teamleader,$viewIds))) { ?>
   <div class="inline-block pull-right mright10 project-member-settings" data-toggle="tooltip" data-title="<?php echo _l('add_edit_members'); ?>">
      <a href="#" data-toggle="modal" class="pull-right" data-target="#add-edit-members"><i class="fa fa-plus"></i></a>
   </div>
   <?php } } ?>
   <p class="bold font-size-14 project-info">
      <?php echo _l('project_members'); ?>
   </p>
   <div class="clearfix"></div>
   <?php
   if(count($members) == 0){
      echo '<p class="text-muted mtop10 no-mbot">'._l('no_project_members').'</p>';
   }
   foreach($members as $member){ ?>
   <div class="media">
      <div class="media-left">
         <a href="<?php echo admin_url('staff/member/'.$member["staff_id"]); ?>">
            <?php echo staff_profile_image($member['staff_id'],array('staff-profile-image-small','media-object')); ?>
         </a>
      </div>
      <div class="media-body">
        
         <h5 class="media-heading mtop5"><a href="<?php echo admin_url('staff/member/'.$member["staff_id"]); ?>"><?php echo get_staff_full_name($member['staff_id']); ?></a>
            <?php if(has_permission('projects','','create') || $member['staff_id'] == get_staff_user_id()){ ?>
            <br /><small class="text-muted"><?php echo _l('total_logged_hours_by_staff') .': '.seconds_to_time_format($member['total_logged_time']); ?></small>
            <?php } ?>

            <?php if(has_permission('projects','','edit') || has_permission('projects','','create')){ ?>
                <?php if(is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(),$ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader,$my_staffids) && !in_array($project->teamleader,$viewIds))) { ?>
            <a href="<?php echo admin_url('projects/remove_team_member/'.$project->id.'/'.$member['staff_id']); ?>" class="text-danger _delete"><i class="fa fa fa-times"></i></a>
            <?php } } ?>

         </h5>
      </div>
   </div>
   <?php } ?>
</div>
<?php }if(!empty($need_fields) && (in_array("project_contacts[]", $need_fields) || in_array("primary_contact", $need_fields) )){?>

<div class="team-contacts project-overview-team-contacts">
   <hr class="hr-panel-heading project-area-separation" />
   <?php if(has_permission('projects','','edit') || has_permission('projects','','create')){ ?>
    <?php if(is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(),$ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader,$my_staffids) && !in_array($project->teamleader,$viewIds))) { ?>
   <div class="inline-block pull-right mright10 project-contact-settings" data-toggle="tooltip" data-title="<?php echo _l('add_new',_l('contact')); ?>">
   <?php if(!empty($need_fields) && (in_array("project_contacts[]", $need_fields) )){?>
   <a href="#" data-toggle="modal" data-target="#project_contacts_modal"><i class="fa fa-plus"></i></a>
   <?php }?>
   </div>
   <div class="inline-block pull-right mright10 project-contact-settings" data-toggle="tooltip" data-title="<?php echo _l('change'); ?>">
      
      <a href="#" data-toggle="modal" class="pull-right getcontactsbyorg" data-target="#add-edit-contacts"><i class="fa fa-cog"></i></a>
   </div>
   <?php } } ?>
   <p class="bold font-size-14 project-info">
      <?php echo _l('project_contacts'); ?>
   </p>
   <div class="clearfix"></div>
   <?php
   if(count($contacts) == 0){
      echo '<p class="text-muted mtop10 no-mbot">'._l('no_project_contacts').'</p>';
   }
   foreach($contacts as $contact){
       ?>
   <div class="media">
      <div class="media-left">
         <a href="<?php echo admin_url('clients/view_contact/'.$contact["contacts_id"]); ?>">
         <img src="<?php echo contact_profile_image_url($contact['contacts_id'],array('staff-profile-image-small','media-object')); ?>" id="contact-img" class="staff-profile-image-small">
         </a>
      </div>
      <div class="media-body">
         
         <h5 class="media-heading mtop5" style="width:auto; float:left;"><a href="<?php echo admin_url('clients/view_contact/'.$contact["contacts_id"]); ?>"><?php echo get_contact_full_name($contact['contacts_id']); ?></a>
         
         <?php if((has_permission('projects','','edit') || has_permission('projects','','create')) && $contact['is_primary'] == 0){ ?>
            <?php if(is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(),$ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader,$my_staffids) && !in_array($project->teamleader,$viewIds))) { ?>
         <a href="<?php echo admin_url('projects/remove_team_contact/'.$project->id.'/'.$contact['contacts_id']); ?>" class="text-danger _delete"><i class="fa fa fa-times"></i></a>
         <?php } } ?>

         </h5>
         <?php
            if($contact['is_primary'] == 1) {
        ?>
              <span class="primarycontact">  Primary </span>
        <?php
            }
        ?>
      </div>
        
   </div>
   <?php } ?>
</div>

<?php }?>
</div>
<div class="col-md-6 project-overview-right">
<div class="row">
      <!-- <div class="col-md-<?php //echo ($project->deadline ? 6 : 12); ?> project-progress-bars"> -->
      <div class="col-md-12 project-progress-bars">
         <div class="row">
           <div class="project-overview-open-tasks">
            <div class="col-md-9">
               <p class="text-uppercase bold text-dark font-medium">
                  <?php echo $tasks_not_completed; ?> / <?php echo $total_tasks; ?> <?php echo _l('project_open_tasks'); ?>
               </p>
               <!-- <p class="text-muted bold"><?php echo $tasks_not_completed_progress; ?>%</p> -->
               <p class="text-uppercase bold text-dark font-medium">
       <?php echo $tasks_completed; ?> / <?php echo $total_tasks; ?> <?php echo _l('project_completed_tasks'); ?>
     </p>
            </div>
            <div class="col-md-3 text-right">
               <i class="fa fa-check-circle<?php if($tasks_not_completed_progress >= 100){echo ' text-success';} ?>" aria-hidden="true"></i>
            </div>
            <div class="col-md-12 mtop5">
               <!-- <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $tasks_not_completed_progress; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $tasks_not_completed_progress; ?>">
                  </div>
               </div> -->
            </div>
         </div>
      </div>
   </div>
   
   <?php /* if($project->deadline){ ?>
   <div class="col-md-6 project-progress-bars project-overview-days-left">
      <div class="row">
         <div class="col-md-9">
            <p class="text-uppercase bold text-dark font-medium">
               <?php echo $project_days_left; ?> / <?php echo $project_total_days; ?> <?php echo _l('project_days_left'); ?>
            </p>
            <p class="text-muted bold"><?php echo $project_time_left_percent; ?>%</p>
         </div>
         <div class="col-md-3 text-right">
            <i class="fa fa-calendar-check-o<?php if($project_time_left_percent >= 100){echo ' text-success';} ?>" aria-hidden="true"></i>
         </div>
         <div class="col-md-12 mtop5">
            <div class="progress no-margin progress-bar-mini">
               <div class="progress-bar<?php if($project_time_left_percent == 0){echo ' progress-bar-warning ';} else { echo ' progress-bar-success ';} ?>no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $project_time_left_percent; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $project_time_left_percent; ?>">
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php } */?>
</div>
<hr class="hr-panel-heading" />

<?php if(has_permission('projects','','create')) { ?>
<div class="row">
   <?php /* if($project->billing_type == 3 || $project->billing_type == 2){ ?>
   <div class="col-md-12 project-overview-logged-hours-finance">
      <div class="col-md-3">
         <?php
         $data = $this->projects_model->total_logged_time_by_billing_type($project->id);
         ?>
         <p class="text-uppercase text-muted"><?php echo _l('project_overview_logged_hours'); ?> <span class="bold"><?php echo $data['logged_time']; ?></span></p>
         <p class="bold font-medium"><?php echo app_format_money($data['total_money'], $currency); ?></p>
      </div>
      <div class="col-md-3">
         <?php
         $data = $this->projects_model->data_billable_time($project->id);
         ?>
         <p class="text-uppercase text-info"><?php echo _l('project_overview_billable_hours'); ?> <span class="bold"><?php echo $data['logged_time'] ?></span></p>
         <p class="bold font-medium"><?php echo app_format_money($data['total_money'], $currency); ?></p>
      </div>
      <div class="col-md-3">
         <?php
         $data = $this->projects_model->data_billed_time($project->id);
         ?>
         <p class="text-uppercase text-success"><?php echo _l('project_overview_billed_hours'); ?> <span class="bold"><?php echo $data['logged_time']; ?></span></p>
         <p class="bold font-medium"><?php echo app_format_money($data['total_money'], $currency); ?></p>
      </div>
      <div class="col-md-3">
         <?php
         $data = $this->projects_model->data_unbilled_time($project->id);
         ?>
         <p class="text-uppercase text-danger"><?php echo _l('project_overview_unbilled_hours'); ?> <span class="bold"><?php echo $data['logged_time']; ?></span></p>
         <p class="bold font-medium"><?php echo app_format_money($data['total_money'], $currency); ?></p>
      </div>
      <div class="clearfix"></div>
      <hr class="hr-panel-heading" />
   </div>
   <?php } */ ?>

 
      <div class="col-md-12 text-center project-percent-col mtop10">
         <p class="bold"><?php echo _l('project_progress_text'); ?></p>
         <div class="project-progress relative mtop15" data-value="<?php echo $project->progress / 100; ?>" data-size="400" data-thickness="50" data-reverse="true">
            <strong class="project-percent"></strong>
         </div>
      </div>
  
</div>
<!--  
<div class="row">
   <div class="col-md-12 project-overview-expenses-finance">
      <div class="col-md-3">
         <p class="text-uppercase text-muted"><?php echo _l('project_overview_expenses'); ?></p>
         <p class="bold font-medium"><?php echo app_format_money(sum_from_table(db_prefix().'expenses',array('where'=>array('project_id'=>$project->id),'field'=>'amount')), $currency); ?></p>
      </div>
      <div class="col-md-3">
         <p class="text-uppercase text-info"><?php echo _l('project_overview_expenses_billable'); ?></p>
         <p class="bold font-medium"><?php echo app_format_money(sum_from_table(db_prefix().'expenses',array('where'=>array('project_id'=>$project->id,'billable'=>1),'field'=>'amount')), $currency); ?></p>
      </div>
      <div class="col-md-3">
         <p class="text-uppercase text-success"><?php echo _l('project_overview_expenses_billed'); ?></p>
         <p class="bold font-medium"><?php echo app_format_money(sum_from_table(db_prefix().'expenses',array('where'=>array('project_id'=>$project->id,'invoiceid !='=>'NULL','billable'=>1),'field'=>'amount')), $currency); ?></p>
      </div>
      <div class="col-md-3">
         <p class="text-uppercase text-danger"><?php echo _l('project_overview_expenses_unbilled'); ?></p>
         <p class="bold font-medium"><?php echo app_format_money(sum_from_table(db_prefix().'expenses',array('where'=>array('project_id'=>$project->id,'invoiceid IS NULL','billable'=>1),'field'=>'amount')), $currency); ?></p>
      </div>
   </div>
</div>
  -->
<?php } ?>
<!--  
<div class="project-overview-timesheets-chart">
   <hr class="hr-panel-heading" />
   <div class="dropdown pull-right">
      <a href="#" class="dropdown-toggle" type="button" id="dropdownMenuProjectLoggedTime" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
         <?php if(!$this->input->get('overview_chart')){
            echo _l('this_week');
         } else {
            echo _l($this->input->get('overview_chart'));
         }
         ?>
         <span class="caret"></span>
      </a>
      <ul class="dropdown-menu" aria-labelledby="dropdownMenuProjectLoggedTime">
         <li><a href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_overview&overview_chart=this_week'); ?>"><?php echo _l('this_week'); ?></a></li>
         <li><a href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_overview&overview_chart=last_week'); ?>"><?php echo _l('last_week'); ?></a></li>
         <li><a href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_overview&overview_chart=this_month'); ?>"><?php echo _l('this_month'); ?></a></li>
         <li><a href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_overview&overview_chart=last_month'); ?>"><?php echo _l('last_month'); ?></a></li>
      </ul>
   </div>
   <div class="clearfix"></div>
   <canvas id="timesheetsChart" style="max-height:300px;" width="300" height="300"></canvas>
</div>
  -->
</div>
</div>
<div class="modal fade" id="add-edit-members" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <?php echo form_open(admin_url('projects/add_edit_members/'.$project->id)); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('project_members'); ?></h4>
         </div>
         <div class="modal-body">
            <?php
            $selected = array();
            foreach($members as $member){
              array_push($selected,$member['staff_id']);
           }
           echo render_select('project_members[]',$staff,array('staffid',array('firstname','lastname')),'project_members',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
           ?>
        </div>
        <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
         <button type="submit" class="btn btn-info" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('submit'); ?></button>
      </div>
   </div>
   <!-- /.modal-content -->
   <?php echo form_close(); ?>
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="edit_description" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <?php echo form_open(admin_url('projects/edit_description/'.$project->id)); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('project_description'); ?></h4>
         </div>
         <div class="modal-body">
			<label for="description" class="control-label">Description</label>
			<textarea id="description_new" name="description" class="form-control tinymce" rows="5"><?php echo $project->description;?></textarea>
        </div>
        <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
         <button type="submit" class="btn btn-info" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('submit'); ?></button>
      </div>
   </div>
   <!-- /.modal-content -->
   <?php echo form_close(); ?>
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="add-edit-contacts" tabindex="-2" role="dialog">
   <div class="modal-dialog">
      <?php echo form_open(admin_url('projects/add_edit_contacts/'.$project->id)); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('project_contacts'); ?></h4>
         </div>
         <div class="modal-body">
             <div class="contactsdiv">
                <?php
                    $selected = array();
                    foreach($contacts as $contact){
                    array_push($selected,$contact['contacts_id']);
                }
                echo render_select('project_contacts[]',$client_contacts,array('id',array('firstname','lastname')),'project_contacts',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
                ?>
            </div>
            <div class='row primarydiv'>
                <div class="col-md-12">
                    <div class="form-group select-placeholder">
                        <label for="status"><?php echo _l('project_primary_contacts'); ?></label>
                        <div class="clearfix"></div>
                        <select name="primary_contact" id="primary_contact" class="selectpicker" data-width="100%"
                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required>
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

        </div>
        <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
         <button type="submit" class="btn btn-info" autocomplete="off" ><?php echo _l('submit'); ?></button>
      </div>
   </div>
   <!-- /.modal-content -->
   <?php echo form_close(); ?>
</div>
</div>
<!-- /.modal-dialog -->
<div class="modal fade" id="project_contacts_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('add_new',_l('contact')); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/form_contact/undefined',array('id'=>'project_contacts_add')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                    <?php echo form_hidden('clientid',''); ?>
                    <?php echo form_hidden('project_id',$project->id); ?>
                    <?php $attrs = array('autofocus'=>true, 'required'=>true); ?>
                        <?php echo render_input( 'firstname', 'client_firstname','','',$attrs); ?>
                        <div id="contact_exists_info" class="hide"></div>
                        <?php echo render_input( 'title', 'contact_position',''); ?>
                        <?php echo render_input( 'email', 'client_email','', 'email'); ?>
                        <?php echo render_input( 'phonenumber', 'client_phonenumber','','text',array('autocomplete'=>'off')); ?>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>



<div class="modal fade" id="clientid_add_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('add_new',_l('proposal_for_customer')); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/ajax_client',array('id'=>'clientid_add_group_modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                    <?php $attrs = array('autofocus'=>true, 'required'=>true); ?>
                    <?php echo render_input( 'company', 'client_company','','text',$attrs); ?>
                    <div id="companyname_exists_info" class="hide"></div>
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
<?php //init_tail(); ?>
<!-- <script type="text/javascript" id="vendor-js" src="<?php echo site_url('assets/builds/vendor-admin.js?v=2.4.0'); ?>"></script> -->