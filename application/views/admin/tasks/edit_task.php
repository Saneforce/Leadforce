<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
p#rel_id-error {
    clear: both;
    position: absolute;
    top: 36px;
}
.btn-disable {
  pointer-events: none;
  opacity:0.5;
}
</style>
<?php echo form_open_multipart(admin_url('tasks/task/'),array('id'=>'task-form')); ?>
<div class="modal fade<?php if(isset($task)){echo ' edit';} ?>" id="_task_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"<?php if($this->input->get('opened_from_lead_id')){echo 'data-lead-id='.$this->input->get('opened_from_lead_id'); } ?>>
<div class="modal-dialog" role="document">
   <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
         <h4 class="modal-title" id="myModalLabel">
            <?php echo $title; ?>
			<!-- hidden -->
			<input type="hidden" name="task_mark_complete_id" id="task_mark_complete_id" value="">
         </h4>
      </div>
      <div class="modal-body" style="padding:15px 15px 0px 15px">
         <div class="row">
            <div class="col-md-12">
               <?php
                  $rel_type = '';
                  $rel_id = '';
                  if(isset($task) || ($this->input->get('rel_id') && $this->input->get('rel_type'))){
                      $rel_id = isset($task) ? $task->rel_id : $this->input->get('rel_id');
                      $rel_type = isset($task) ? $task->rel_type : $this->input->get('rel_type');
                   }
                   if(isset($task) && $task->billed == 1){
                     echo '<div class="alert alert-success text-center no-margin">'._l('task_is_billed','<a href="'.admin_url('invoices/list_invoices/'.$task->invoice_id).'" target="_blank">'.format_invoice_number($task->invoice_id)). '</a></div><br />';
                   }
				   
				   
                  ?>
               
               <!-- <div class="task-visible-to-customer checkbox checkbox-inline checkbox-primary<?php if((isset($task) && $task->rel_type != 'project') || !isset($task) || (isset($task) && $task->rel_type == 'project' && total_rows(db_prefix().'project_settings',array('project_id'=>$task->rel_id,'name'=>'view_tasks','value'=>0)) > 0)){echo ' hide';} ?>">
                  <input type="checkbox" id="task_visible_to_client" name="visible_to_client" <?php if(isset($task)){if($task->visible_to_client == 1){echo 'checked';}} ?>>
                  <label for="task_visible_to_client"><?php echo _l('task_visible_to_client'); ?></label>
               </div> -->
               <div class="checkbox checkbox-primary checkbox-inline task-add-edit-billable" style=" display :none;">
                  <input type="checkbox" id="task_is_billable" name="billable"
                     <?php if((isset($task) && $task->billable == 1) || (!isset($task) && get_option('task_biillable_checked_on_creation') == 1)) {echo ' checked'; }?>>
                  <label for="task_is_billable"><?php echo _l('task_billable'); ?></label>
               </div>
               <?php if(!isset($task)){ ?>
               <!-- <a href="#" class="pull-right" onclick="slideToggle('#new-task-attachments'); return false;">
               <?php echo _l('attach_files'); ?>
               </a> -->
               <div id="new-task-attachments" class="hide">
                  <!-- <hr /> -->
                  <div class="row attachments">
                     <div class="attachment">
                        <div class="col-md-12">
                           <div class="form-group">
                              <label for="attachment" class="control-label"><?php echo _l('add_task_attachments'); ?></label>
                              <div class="input-group">
                                 <input type="file" extension="<?php echo str_replace('.','',get_option('allowed_files')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="attachments[0]">
                                 <span class="input-group-btn">
                                 <button class="btn btn-success add_more_attachments p8" type="button"><i class="fa fa-plus"></i></button>
                                 </span>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <?php
                  if($this->input->get('ticket_to_task')) {
                    echo form_hidden('ticket_to_task');
                  }
                  } ?>
               <!-- <hr /> -->
			   <!-- Team Leaders -->
         
              
         <div class="row">
              <div class="col-md-6">
                <div class="form-group select-placeholder">
                  <label for="tasktype" class="control-label"><small class="req text-danger">* </small><?php echo _l('tasktype'); ?></label>
                  <select required="true" name="tasktype" id="tasktype" class="selectpicker" data-width="100%">
                    <option value=""></option>
                    <?php $i = 0; foreach($tasktypes as $tasktype) { ?>
                      <?php if(isset($task->tasktype) && $tasktype['id']==$task->tasktype) { ?>
                        <option value="<?php echo $tasktype['id']; ?>" selected><?php echo $tasktype['name']; ?></option>
                      <?php } else { 
                          if($i==0) {
                            $selected = 'selected';
                          } else {
                            $selected = '';
                          }
                          ?>
                        <option value="<?php echo $tasktype['id']; ?>" <?php echo $selected; ?> ><?php echo $tasktype['name']; ?></option>
                      <?php } ?>
                    <?php $i++; } ?>
                  </select>
                </div>
              </div>
         
              <div class="col-md-6">
               <?php $value = (isset($task) ? $task->name : 'Call'); ?>
               <?php echo render_input('name','task_add_edit_subject',$value,'',array('maxlength'=>191)); ?>
               </div>
         </div>
          <div class="row">
            <div class="col-md-12">
              <p class="bold"><?php echo _l('task_add_edit_description'); ?></p>
              <div class="form-group">
			  <?php 
			  echo render_textarea('description','',(isset($task) ? $task->description : ''),array('rows'=>4,'placeholder'=>_l('task_add_description'),'id'=>'description','class'=>'tinymce_id'),array(),'no-mbot','tinymce-task');
			  ?>
			  </div>
            </div>
          </div>
                <div class="no-mbot"><input type="hidden" class="tinymce-task"></div>
                <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                     <?php 
                          $selected_ = '';
                          foreach ($staff as $assignee) {
                            if (isset($task->assignees_ids) && in_array($assignee['staffid'],$task->assignees_ids)) {
                                $selected_= 'selected';
                            }
                          }
                      ?>
                        <label for="add_task_assignees" class="control-label"><?php echo _l('task_assignedto'); ?></label>
                        <select name="assignees[]" class="selectpicker"  data-width="100%"  title='<?php echo _l('task_assignedto'); ?>' data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                 <option value=""></option>
                           <?php
                           $options = '';
                           $staff_count = count((array)$staff);
                           $i = 1;
                           foreach ($staff as $assignee) {
                              $selected_text = '';
                              if (!empty($task->assignees_ids) && !empty($assignee['staffid']) && in_array($assignee['staffid'],$task->assignees_ids)) {
                                 $selected_text= 'selected';
                              } else {
                                  if(get_staff_user_id() == $assignee['staffid'] && empty($selected_text) && empty($selected_)) {
                                    $selected_text= 'selected';
                                  }
                              }
                              // else {
                              //   if(isset($project->teamleader)) {
                              //     if($project->teamleader == $assignee['staffid']) {
                              //       $selected_text= 'selected';
                              //     }
                              //   } else {
                              //     if($staff_count == 1 && empty($selected_text)) {
                              //         $selected_text= 'selected';
                              //     }
                              //     if($i == $staff_count) {
                              //       if(get_staff_user_id() == $assignee['staffid'] && empty($selected_text) && empty($selected_)) {
                              //         $selected_text= 'selected';
                              //       }
                              //     } 
                                  
                              //     if($i == 1 && $selected_text == '') {
                              //       $selected_text= 'selected';
                              //     }
                              //   }
                              // }
                              $options .= '<option value="' . $assignee['staffid'] . '" '.$selected_text.'>' . $assignee['full_name'] . '</option>';
                              $i++;
                            }
                           echo $options;
                           ?>
                        </select>
                        
                     </div>
                  </div>
              
                  <div class="col-md-6">
                     <?php if(isset($task)){
                        $value = _d($task->startdate);
                        } else if(isset($start_date)){
                        $value = $start_date;
                        } else {
                        $value = _d(date('Y-m-d H:i'));
                        }
                        $date_attrs = array('readonly' => 'readonly');
                        if(isset($task) && $task->recurring > 0 && $task->last_recurring_date != null) {
                        $date_attrs['disabled'] = true;
                        }
                        ?>
                     <?php echo render_datetime_input('startdate','task_add_edit_start_date',$value, $date_attrs); ?>
                  </div>
                  <!-- <div class="col-md-6">
                     <?php $value = (isset($task) ? _d($task->duedate) : ''); ?>
                     <?php echo render_date_input('duedate','task_add_edit_due_date',$value,$project_end_date_attrs); ?>
                  </div> -->
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="priority" class="control-label"><?php echo _l('task_add_edit_priority'); ?></label>
                        <select name="priority" class="selectpicker" id="priority" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option></option>   
                        <?php foreach(get_tasks_priorities() as $priority) { ?>
                           <option value="<?php echo $priority['id']; ?>"<?php if(isset($task) && $task->priority == $priority['id'] || !isset($task) && get_option('default_task_priority') == $priority['id']){echo ' selected';} ?>><?php echo $priority['name']; ?></option>
                           <?php } ?>
                           <?php hooks()->do_action('task_priorities_select', (isset($task) ? $task : 0)); ?>
                        </select>
                     </div>
                  </div>
                  <!-- 
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="repeat_every" class="control-label"><?php echo _l('task_repeat_every'); ?></label>
                        <select name="repeat_every" id="repeat_every" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <option value=""></option>
                           <option value="1-week" <?php if(isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'week'){echo 'selected';} ?>><?php echo _l('week'); ?></option>
                           <option value="2-week" <?php if(isset($task) && $task->repeat_every == 2 && $task->recurring_type == 'week'){echo 'selected';} ?>>2 <?php echo _l('weeks'); ?></option>
                           <option value="1-month" <?php if(isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'month'){echo 'selected';} ?>>1 <?php echo _l('month'); ?></option>
                           <option value="2-month" <?php if(isset($task) && $task->repeat_every == 2 && $task->recurring_type == 'month'){echo 'selected';} ?>>2 <?php echo _l('months'); ?></option>
                           <option value="3-month" <?php if(isset($task) && $task->repeat_every == 3 && $task->recurring_type == 'month'){echo 'selected';} ?>>3 <?php echo _l('months'); ?></option>
                           <option value="6-month" <?php if(isset($task) && $task->repeat_every == 6 && $task->recurring_type == 'month'){echo 'selected';} ?>>6 <?php echo _l('months'); ?></option>
                           <option value="1-year" <?php if(isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'year'){echo 'selected';} ?>>1 <?php echo _l('year'); ?></option>
                           <option value="custom" <?php if(isset($task) && $task->custom_recurring == 1){echo 'selected';} ?>><?php echo _l('recurring_custom'); ?></option>
                        </select>
                     </div>
                  </div> -->
               
               <div class="recurring_custom <?php if((isset($task) && $task->custom_recurring != 1) || (!isset($task))){echo 'hide';} ?>">
                  <div class="row">
                     <div class="col-md-6">
                        <?php $value = (isset($task) && $task->custom_recurring == 1 ? $task->repeat_every : 1); ?>
                        <?php echo render_input('repeat_every_custom','',$value,'number',array('min'=>1)); ?>
                     </div>
                     <div class="col-md-6">
                        <select name="repeat_type_custom" id="repeat_type_custom" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <option value="day" <?php if(isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'day'){echo 'selected';} ?>><?php echo _l('task_recurring_days'); ?></option>
                           <option value="week" <?php if(isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'week'){echo 'selected';} ?>><?php echo _l('task_recurring_weeks'); ?></option>
                           <option value="month" <?php if(isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'month'){echo 'selected';} ?>><?php echo _l('task_recurring_months'); ?></option>
                           <option value="year" <?php if(isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'year'){echo 'selected';} ?>><?php echo _l('task_recurring_years'); ?></option>
                        </select>
                     </div>
                  </div>
               </div>
               <div id="cycles_wrapper" class="<?php if(!isset($task) || (isset($task) && $task->recurring == 0)){echo ' hide';}?>">
                  <?php $value = (isset($task) ? $task->cycles : 0); ?>
                  <div class="form-group recurring-cycles">
                     <label for="cycles"><?php echo _l('recurring_total_cycles'); ?>
                     <?php if(isset($task) && $task->total_cycles > 0){
                        echo '<small>' . _l('cycles_passed', $task->total_cycles) . '</small>';
                        }
                        ?>
                     </label>
                     <div class="input-group">
                        <input type="number" class="form-control"<?php if($value == 0){echo ' disabled'; } ?> name="cycles" id="cycles" value="<?php echo $value; ?>" <?php if(isset($task) && $task->total_cycles > 0){echo 'min="'.($task->total_cycles).'"';} ?>>
                        <div class="input-group-addon">
                           <div class="checkbox">
                              <input type="checkbox"<?php if($value == 0){echo ' checked';} ?> id="unlimited_cycles">
                              <label for="unlimited_cycles"><?php echo _l('cycles_infinity'); ?></label>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <?php
               if($rel_type && $rel_type != 'project' && $rel_type != 'customer') {
                  $style="display:block;";
               } else {
                  $style="display:none;";
               } ?>
                  <div class="col-md-6" style="<?php echo $style; ?>">
                     <div class="form-group reltype">
                        <label for="rel_type" class="control-label"><?php echo _l('task_related_to'); ?></label>
                        <select name="rel_type" class="selectpicker" id="rel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <?php if($rel_type) { ?> 
                          <option value=""></option> 
                        <?php } ?>
                            <option value="project"
                              <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'project'){echo 'selected';}} ?>><?php echo _l('project'); ?></option>
                        <?php if($rel_type) { ?>
                              <option value="invoice" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'invoice'){echo 'selected';}} ?>>
                              <?php echo _l('invoice'); ?>
                           </option>
                           <option value="customer"
                              <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'customer'){echo 'selected';}} ?>>
                              <?php echo _l('client'); ?>
                           </option>
                           
                           <!-- <option value="estimate" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'estimate'){echo 'selected';}} ?>>
                              <?php echo _l('estimate'); ?>
                           </option>
                           <option value="contract" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'contract'){echo 'selected';}} ?>>
                              <?php echo _l('contract'); ?>
                           </option> 
                           <option value="ticket" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'ticket'){echo 'selected';}} ?>>
                              <?php echo _l('ticket'); ?>
                           </option>
                           <option value="expense" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'expense'){echo 'selected';}} ?>>
                              <?php echo _l('expense'); ?>
                           </option>-->
                           <option value="lead" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'lead'){echo 'selected';}} ?>>
                              <?php echo _l('lead'); ?>
                           </option>
                           <option value="proposal" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'proposal'){echo 'selected';}} ?>>
                              <?php echo _l('proposal'); ?>
                           </option>
                        <?php } ?>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group" id="rel_id_wrapper">
                        <label for="rel_id" class="control-label"><small class="req text-danger">* </small><span class="rel_id_label"><?php echo _l('project'); ?></span></label>
                        <div id="rel_id_select">
                        <?php if($rel_id == '' || $rel_type =='customer'){ ?>
                           <select name="rel_id" id="rel_id" class="ajax-sesarch" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <?php if($rel_id != '' && $rel_type != '' && $rel_type !='customer'){
                              $rel_data = get_relation_data($rel_type,$rel_id);
                              $rel_val = get_relation_values($rel_data,$rel_type);
                              echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                              } ?>
                           </select>
                           <?php }else{ 
                               $rel_data = get_relation_data($rel_type,$rel_id);
                               $rel_val = get_relation_values($rel_data,$rel_type);
                              ?>
                           <input type="hidden"  name="rel_id" value="<?php echo $rel_val['id']; ?>" />
                           <select id="rel_id" class="ajax-sesarch" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                              <?php echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>'; ?>
                           </select>
                           <?php } ?>
                        </div>
                     </div>
                  </div>
                  <?php if($rel_type == '' || $rel_type == 'project' || $rel_type == 'customer') { ?> 
                  <div class="col-md-6">
                     <div class="form-group" id="">
                        <label class="control-label"><?php echo _l('project_customer'); ?></label>
                        <div id="project_details_company"> <?php echo (isset($project_details_company) ? $project_details_company : ''); 
                            if($rel_type =='customer'){
                              $rel_data = get_relation_data($rel_type,$rel_id);
                                            $rel_val = get_relation_values($rel_data,$rel_type);
                              echo '<select disabled="disabled" class="selectpicker" data-width="100%"><option selected>'.$rel_val['name'].'</option></select>';
                            }
                            ?>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group" id="">
                        <label class="control-label"><?php echo _l('project_contacts'); ?></label>
                        <div id="project_contacts_text"> <?php  echo (isset($project_contacts_text) ? $project_contacts_text : ''); ?></div>
                     </div>
                  </div>
                  <?php } ?>
               </div>
               <?php
                  if(isset($task)
                     && $task->status == Tasks_model::STATUS_COMPLETE
                     && (has_permission('create') || has_permission('edit'))){
                     echo render_datetime_input('datefinished','task_finished',_dt($task->datefinished));
                  }
               ?>
               <div class="form-group checklist-templates-wrapper<?php if(count($checklistTemplates) == 0 || isset($task)){echo ' hide';}  ?>">
                  <label for="checklist_items"><?php echo _l('insert_checklist_templates'); ?></label>
                  <select id="checklist_items" name="checklist_items[]" class="selectpicker checklist-items-template-select" multiple="1" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex') ?>" data-width="100%" data-live-search="true" data-actions-box="true">
                     <option value="" class="hide"></option>
                     <?php foreach($checklistTemplates as $chkTemplate){ ?>
                     <option value="<?php echo $chkTemplate['id']; ?>">
                        <?php echo $chkTemplate['description']; ?>
                     </option>
                     <?php } ?>
                  </select>
               </div>
               <div class="form-group">
                  <div id="inputTagsWrapper">
                     <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
                     <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($task) ? prep_tags_input(get_tags_in($task->id,'task')) : ''); ?>" data-role="tagsinput">
                  </div>
               </div>
               <?php $rel_id_custom_field = (isset($task) ? $task->id : false); ?>
               <?php echo render_custom_fields('tasks',$rel_id_custom_field); ?>
               
            </div>
         </div>
      </div>
      <?php 
            $my_staffids = $this->staff_model->get_my_staffids();
            $view_ids = $this->staff_model->getFollowersViewList();
            $teamleader = $task->addedfrom;
            $assigneduser = $task->assignees_ids[0];
            //pr($task);
            $btn = '';
            if($teamleader) {
              if ((!empty($my_staffids) && in_array($teamleader,$my_staffids) && !in_array($teamleader,$view_ids)) || is_admin(get_staff_user_id()) || $teamleader == get_staff_user_id()) {
                    $btn = '';
              } else {
                    $btn = 'btn-disable';
              }
            }
            if($assigneduser) {
              if ((!empty($my_staffids) && in_array($assigneduser,$my_staffids) && !in_array($assigneduser,$view_ids)) || is_admin(get_staff_user_id()) || $assigneduser == get_staff_user_id()) {
              } else {
                    $btn = 'btn-disable';
              }
            }
      ?>
      <div class="modal-footer">
      
         <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
         <button type="submit" class="btn btn-info <?php echo $btn; ?>"><?php echo _l('submit'); ?></button>
      </div>
   </div>
</div>
<?php echo form_close(); ?>
<script>
   var _rel_id = $('#rel_id'),
   _rel_type = $('#rel_type'),
   _rel_id_wrapper = $('#rel_id_wrapper'),
   data = {};

   var _milestone_selected_data;
   _milestone_selected_data = undefined;

   $(function(){

     
    $('#startdate').datetimepicker({
      step: 15
    });
    $( "body" ).off( "change", "#rel_id" );

    var inner_popover_template = '<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';

    $('#_task_modal .task-menu-options .trigger').popover({
      html: true,
      placement: "top",
      trigger: 'click',
      title:"<?php echo _l('actions'); ?>",
      content: function() {
       return $('body').find('#_task_modal .task-menu-options .content-menu').html();
     },
     template: inner_popover_template
   });

    custom_fields_hyperlink();

    appValidateForm($('#task-form'), {
      name: 'required',
      rel_id: 'required',
      startdate: 'required',
      repeat_every_custom: { min: 1},
    },task_form_handler);

    $('.rel_id_label').html(_rel_type.find('option:selected').text());

    _rel_type.on('change', function() {

     var clonedSelect = _rel_id.html('').clone();
     _rel_id.selectpicker('destroy').remove();
     _rel_id = clonedSelect;
     $('#rel_id_select').append(clonedSelect);
     $('.rel_id_label').html(_rel_type.find('option:selected').text());

     task_rel_select();
     if($(this).val() != ''){
      _rel_id_wrapper.removeClass('hide');
    } else {
      _rel_id_wrapper.addClass('hide');
    }
    init_project_details(_rel_type.val());
   });

    init_datepicker();
    init_color_pickers();
    init_selectpicker();
    task_rel_select();

   
      
    $('body').on('change','#rel_id',function(){
     


     if($(this).val() != ''){
       if(_rel_type.val() == 'project'){
         $.get(admin_url + 'projects/get_rel_project_data/'+$(this).val()+'/'+taskid,function(project){
           $("select[name='milestone']").html(project.milestones);
           if(typeof(_milestone_selected_data) != 'undefined'){
            $("select[name='milestone']").val(_milestone_selected_data.id);
            $('input[name="duedate"]').val(_milestone_selected_data.due_date)
          }
          $("select[name='milestone']").selectpicker('refresh');
          if(project.billing_type == 3){
           $('.task-hours').addClass('project-task-hours');
         } else {
           $('.task-hours').removeClass('project-task-hours');
         }
         $("#project_contacts_text").html(project.project_contacts_text);
         $("#project_contacts_text select").selectpicker('refresh');
         $("#project_details_company").html(project.project_company_text);
         $("#project_details_company select").selectpicker('refresh');
         if(project.deadline) {
            var $duedate = $('#_task_modal #duedate');
            var currentSelectedTaskDate = $duedate.val();
            $duedate.attr('data-date-end-date', project.deadline);
            $duedate.datetimepicker('destroy');
            init_datepicker($duedate);

            if(currentSelectedTaskDate) {
               var dateTask = new Date(unformat_date(currentSelectedTaskDate));
               var projectDeadline = new Date(project.deadline);
               if(dateTask > projectDeadline) {
                  $duedate.val(project.deadline_formatted);
               }
            }
         } else {
            reset_task_duedate_input();
         }
         init_project_details(_rel_type.val(),project.allow_to_view_tasks);
       },'json');
       } else {
         reset_task_duedate_input();
       }
     }
   });

    <?php if(!isset($task) && $rel_id != ''){ ?>
      _rel_id.change();
      <?php } ?>

    });

   <?php if(isset($_milestone_selected_data)){ ?>
    _milestone_selected_data = '<?php echo json_encode($_milestone_selected_data); ?>';
    _milestone_selected_data = JSON.parse(_milestone_selected_data);
    <?php } ?>

    function task_rel_select(){
      var serverData = {};
      serverData.rel_id = _rel_id.val();
      data.type = _rel_type.val();
      init_ajax_search(_rel_type.val(),_rel_id,serverData);
     }

     function init_project_details(type,tasks_visible_to_customer){
      var wrap = $('.non-project-details');
      var wrap_task_hours = $('.task-hours');
      if(type == 'project'){
        if(wrap_task_hours.hasClass('project-task-hours') == true){
          wrap_task_hours.removeClass('hide');
        } else {
          wrap_task_hours.addClass('hide');
        }
        wrap.addClass('hide');
        $('.project-details').removeClass('hide');
      } else {
        wrap_task_hours.removeClass('hide');
        wrap.removeClass('hide');
        $('.project-details').addClass('hide');
        $('.task-visible-to-customer').addClass('hide').prop('checked',false);
      }
      if(typeof(tasks_visible_to_customer) != 'undefined'){
        if(tasks_visible_to_customer == 1){
          $('.task-visible-to-customer').removeClass('hide');
          $('.task-visible-to-customer input').prop('checked',true);
        } else {
          $('.task-visible-to-customer').addClass('hide')
          $('.task-visible-to-customer input').prop('checked',false);
        }
      }
    }
    function reset_task_duedate_input() {
      var $duedate = $('#_task_modal #duedate');
      $duedate.removeAttr('data-date-end-date');
      $duedate.datetimepicker('destroy');
      init_datepicker($duedate);
   }
   
   $( "#tasktype").change(function() {
         //if($( "#_task_modal #name" ).val() == ''){
            $( "#_task_modal #name" ).val($("#tasktype option:selected").text());
         //}
      });
      
</script>
<script type="text/javascript">
    tinymce.init({
		selector: 'textarea.tinymce_id',
        menubar: false,
        statusbar: false,
        toolbar: false
    });
	function check_fun(){
		
		
		//tinymce.get('description').hide();
	$('.mce-stack-layout-item').each(function () {
		var cur_id = this.id;
		var req_id = cur_id.split("_");
		var req_val = parseInt(req_id[1])+parseInt(18);
		var req_id1 = req_id[0]+'_'+req_val;
		//alert(req_id1);
		 $('#'+req_id1).html('');
		 $('#'+req_id1).hide();
		 
      
});tinymce.activeEditor.hide();
	}
	setTimeout(
    function() {
      check_fun();
    }, 900);

</script>

<style>
div#mceu_18-body {
    display: none !important;
}
.mce-container.mce-panel{
	 display: block !important;
}
textarea.form-control{
	display:none !important;
}
.mce-first{
	display:none !important;
}
</style>