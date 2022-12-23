<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css">
<div id="wrapper">
   <div class="content">
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    #wrapper{
        font-size: 14px;
    }
   .nav-tabs-horizontal li a .badge{
      margin-left: 5px;
   }
   .nav-tabs-horizontal li.active a .badge, .nav-tabs-horizontal li:hover a .badge{
      background-color: #02a9f4;
   }
</style>
   <?php
      if(isset($lead)){
           if($lead->lost == 1){
              echo '<div class="ribbon danger"><span>'._l('lead_lost').'</span></div>';
           } else if($lead->junk == 1){
              echo '<div class="ribbon warning"><span>'._l('lead_junk').'</span></div>';
           } else {
              if (total_rows(db_prefix().'clients', array(
                'leadid' => $lead->id))) {
                echo '<div class="ribbon success"><span>Deal</span></div>';
             }
          }
      }
   ?>

<style>
.horizontal-tabs {
    width:100%;
}
.project-tabs {
    float:left;
}
.pipechange {
    float:right;
    padding-top:8px;
}
.formnewpipeline .dropdown-menu {
    width:100%;
}

/* End basic CSS override */
.timeline {
  width: 85%;
  max-width: 700px;
  margin-left: 50px;
  margin-right: auto;
  display: flex;
  flex-direction: column;
  padding: 0 0 0 32px;
  border-left: 2px solid #e3e3e3;
}

.timeline-item {
  display: flex;
  gap: 24px;
}
.timeline-item + * {
  margin-top: 24px;
}
.timeline-item + .extra-space {
  margin-top: 48px;
}

.timeline .new-comment {
  width: 100%;
}
.timeline .new-comment input {
  border: 1px solid #e3e3e3;
  border-radius: 6px;
  height: 48px;
  padding: 0 16px;
  width: 100%;
}
.timeline .new-comment input::-moz-placeholder {
  color: #b2b2b2;
}
.timeline .new-comment input:-ms-input-placeholder {
  color: #b2b2b2;
}
.timeline .new-comment input::placeholder {
  color: #b2b2b2;
}
.timeline .new-comment input:focus {
  border-color: #b2b2b2;
  outline: 0;
  box-shadow: 0 0 0 4px #f4f6f8;
}

.timeline-item-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-left: -52px;
  flex-shrink: 0;
  overflow: hidden;
  box-shadow: 0 0 0 6px #fff;
}
.timeline-item-icon svg {
  width: 20px;
  height: 20px;
}
.timeline-item-icon.faded-icon {
  background-color: #f4f6f8;
  color: #7b7b7b;
}
.timeline-item-icon.filled-icon {
  background-color: #688afd;
  color: #fff;
}

.timeline-item-description {
  display: flex;
  padding-top: 6px;
  gap: 8px;
  color: #7b7b7b;
}
.timeline-item-description img {
  flex-shrink: 0;
}
.timeline-item-description a {
  color: #3d3d3d;
  font-weight: 500;
  text-decoration: none;
}
.timeline-item-description a:hover, .timeline-item-description a:focus {
  outline: 0;
  color: #688afd;
}

.timeline .avatar {
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  overflow: hidden;
  aspect-ratio: 1/1;
  flex-shrink: 0;
  width: 40px;
  height: 40px;
}
.timeline .avatar.small {
  width: 28px;
  height: 28px;
}
.timeline .avatar img {
  -o-object-fit: cover;
     object-fit: cover;
     max-width: 100%;
}

.timeline .comment {
  margin-top: 12px;
  color: #3d3d3d;
  border: 1px solid #e3e3e3;
  box-shadow: 0 4px 4px 0 #f4f6f8;
  border-radius: 6px;
  padding: 16px;
}

.timeline .button {
  border: 0;
  padding: 0;
  display: inline-flex;
  vertical-align: middle;
  margin-right: 4px;
  margin-top: 12px;
  align-items: center;
  justify-content: center;
  height: 32px;
  padding: 0 8px;
  background-color: #f4f6f8;
  flex-shrink: 0;
  cursor: pointer;
  border-radius: 99em;
}
.timeline .button:hover {
  background-color: #e3e3e3;
}
.timeline .button.square {
  border-radius: 50%;
  color: #7b7b7b;
  width: 32px;
  height: 32px;
  padding: 0;
}
.timeline .button.square svg {
  width: 24px;
  height: 24px;
}
.timeline .button.square:hover {
  background-color: #e3e3e3;
  color: #3d3d3d;
}

.timeline .show-replies {
  color: #b2b2b2;
  background-color: transparent;
  border: 0;
  padding: 0;
  margin-top: 16px;
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
}
.timeline .show-replies svg {
  flex-shrink: 0;
  width: 24px;
  height: 24px;
}
.timeline .show-replies:hover, .show-replies:focus {
  color: #3d3d3d;
}

.timeline .avatar-list {
  display: flex;
  align-items: center;
}
.timeline .avatar-list > * {
  position: relative;
  box-shadow: 0 0 0 2px #fff;
  margin-right: -8px;
}

.timeline .note-bg{
   background-color: #fff6d6;
}
.timeline .note-color{
   color: #fff6d6;
}

.timeline .comment .document-icon-wrapper{
   font-size: 20px;
}
</style>
  <div class="row">
     <div class="col-md-4">
         <div class="panel_s">
            <div class="panel-body" style="height: 90vh; overflow-y:auto;">
               <?php $this->load->view('admin/leads/profile'); ?>
            </div>
         </div>
     </div>
     <div class="col-md-8">
         <?php if(isset($lead)){
             echo form_hidden('leadid',$lead->id);
         } ?>
    <div class="panel_s">
    <div class="panel-body">
      <div class="horizontal-scrollable-tabs preview-tabs-top">
         <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
         <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
         <div class="horizontal-tabs">

      <ul class="nav-tabs-horizontal nav nav-tabs<?php if(!isset($lead)){echo ' lead-new';} ?>" role="tablist">
         <!-- <li role="presentation" class="active" >
            <a href="#tab_lead_profile" aria-controls="tab_lead_profile" role="tab" data-toggle="tab">
            <?php echo _l('lead_profile'); ?>
            </a>
         </li> -->
         <?php if(isset($lead)){?>
         <li role="presentation" class="<?php echo ($group=='lead_activity')?"active": "" ?>">
            <a href="#lead_activity" aria-controls="lead_activity" role="tab" data-toggle="tab">
            <?php echo _l('lead_add_edit_activity'); ?>
            </a>
         </li>
         <li role="presentation" class="<?php echo ($group=='tab_tasks_leads')?"active": "" ?>">
            <a href="#tab_tasks_leads" onclick="init_rel_tasks_table(<?php echo $lead->id; ?>,'lead','.table-rel-tasks-leads');" aria-controls="tab_tasks_leads" role="tab" data-toggle="tab">
            <?php echo _l('tasks'); ?>
            </a>
         </li>
         <li role="presentation" class="<?php echo ($group=='tab_items')?"active": "" ?>">
            <a href="#tab_items" aria-controls="tab_items" role="tab" data-toggle="tab">
                <?php echo _l('items') ?><span class="badge badge-light ml-3" id="leaditemcount"><?php echo $productscnt?></span>
            </a>
         </li>
         <li role="presentation" class="<?php echo ($group=='tab_email')?"active": "" ?>">
            <a href="#tab_email" aria-controls="tab_email" role="tab" data-toggle="tab">
                <?php echo _l('email') ?><span class="badge badge-light ml-3" id="leademailcount"><?php echo $emails_count?></span>
            </a>
         </li>

         <?php if(count($mail_activity) > 0 || isset($show_email_activity) && $show_email_activity){ ?>
         <li role="presentation" class="<?php echo ($group=='tab_email_activity')?"active": "" ?>">
            <a href="#tab_email_activity" aria-controls="tab_email_activity" role="tab" data-toggle="tab">
                <?php echo hooks()->apply_filters('lead_email_activity_subject', _l('lead_email_activity')); ?>
            </a>
         </li>
         <?php } ?>
         <li role="presentation" class="<?php echo ($group=='tab_proposals_leads')?"active": "" ?>">
            <a href="#tab_proposals_leads" onclick="initDataTable('.table-proposals-lead', admin_url + 'proposals/proposal_relations/' + <?php echo $lead->id; ?> + '/lead','undefined', 'undefined','undefined',[6,'desc']);" aria-controls="tab_proposals_leads" role="tab" data-toggle="tab">
            <?php echo _l('proposals'); ?>
            </a>
         </li>
         <li role="presentation" class="<?php echo ($group=='attachments')?"active": "" ?>">
            <a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">
            <?php echo _l('lead_files'); ?>
            </a>
         </li>
         <li role="presentation" class="<?php echo ($group=='lead_notes')?"active": "" ?>">
            <a href="#lead_notes" aria-controls="lead_notes" role="tab" data-toggle="tab">
            <?php echo _l('lead_add_edit_notes'); ?>
            </a>
         </li>
         <?php if(is_gdpr() && (get_option('gdpr_enable_lead_public_form') == '1' || get_option('gdpr_enable_consent_for_leads') == '1')) { ?>
            <li role="presentation" class="<?php echo ($group=='gdpr')?"active": "" ?>">
              <a href="#gdpr" aria-controls="gdpr" role="tab" data-toggle="tab">
                <?php echo _l('gdpr_short'); ?>
              </a>
           </li>
         <?php } ?>
         <?php } ?>
      </ul>
    </div>
    </div>
   </div>
   </div>
   <div class="panel_s">
    <div class="panel-body">
   <!-- Tab panes -->
   <div class="tab-content" style="margin-top:25px;">
      <!-- from leads modal -->
      <?php if(isset($lead)){ ?>
      <div role="tabpanel" class="tab-pane <?php echo ($group=='tab_items')?"active": "" ?>" id="tab_items" >
         <form id="LeadProdcutForm" method='post' name="LeadProdcutForm" action="<?php echo admin_url('leads/saveleadproducts/'.$lead->id); ?>">
         <?php $this->load->view('admin/leads/items'); ?>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
         </div>
         <?php echo form_close(); ?>
      </div>
      <div role="tabpanel" class="tab-pane <?php echo ($group=='tab_email')?"active": "" ?>" id="tab_email">
         <?php $this->load->view('admin/leads/lead_email') ?>
      </div>
      <?php if(count($mail_activity) > 0 || isset($show_email_activity) && $show_email_activity){ ?>
      <div role="tabpanel" class="tab-pane <?php echo ($group=='tab_email_activity')?"active": "" ?>" id="tab_email_activity">
         <?php hooks()->do_action('before_lead_email_activity', array('lead'=>$lead, 'email_activity'=>$mail_activity)); ?>
         <?php foreach($mail_activity as $_mail_activity){ ?>
         <div class="lead-email-activity">
            <div class="media-left">
               <i class="fa fa-envelope"></i>
            </div>
            <div class="media-body">
               <h4 class="bold no-margin lead-mail-activity-subject">
                  <?php echo $_mail_activity['subject']; ?>
                  <br />
                  <small class="text-muted display-block mtop5 font-medium-xs"><?php echo _dt($_mail_activity['dateadded']); ?></small>
               </h4>
               <div class="lead-mail-activity-body">
                  <hr />
                  <?php echo $_mail_activity['body']; ?>
               </div>
               <hr />
            </div>
         </div>
         <div class="clearfix"></div>
         <?php } ?>
         <?php hooks()->do_action('after_lead_email_activity', array('lead_id'=>$lead->id, 'emails'=>$mail_activity)); ?>
      </div>
      <?php } ?>
      <?php if(is_gdpr() && (get_option('gdpr_enable_lead_public_form') == '1' || get_option('gdpr_enable_consent_for_leads') == '1' || (get_option('gdpr_data_portability_leads') == '1') && is_admin())) { ?>
      <div role="tabpanel" class="tab-pane <?php echo ($group=='gdpr')?"active": "" ?>" id="gdpr">

          <?php if(get_option('gdpr_enable_lead_public_form') == '1') { ?>
            <a href="<?php echo $lead->public_url; ?>" target="_blank" class="mtop5">
                <?php echo _l('view_public_form'); ?>
            </a>
          <?php } ?>
           <?php if(get_option('gdpr_data_portability_leads') == '1' && is_admin()){ ?>
               <?php
               if(get_option('gdpr_enable_lead_public_form') == '1') {
                  echo ' | ';
               }
               ?>
              <a href="<?php echo admin_url('leads/export/'.$lead->id); ?>">
                 <?php echo _l('dt_button_export'); ?>
              </a>
            <?php } ?>
             <?php if(get_option('gdpr_enable_lead_public_form') == '1' || (get_option('gdpr_data_portability_leads') == '1' && is_admin())) { ?>
              <hr class="hr-margin-n-15" />
            <?php } ?>
          <?php if(get_option('gdpr_enable_consent_for_leads') == '1') { ?>
            <h4 class="no-mbot">
                <?php echo _l('gdpr_consent'); ?>
            </h4>
          <?php $this->load->view('admin/gdpr/lead_consent'); ?>
          <hr />
          <?php } ?>
      </div>
      <?php } ?>
      <div role="tabpanel" class="tab-pane <?php echo ($group=='lead_activity')?"active": "" ?>" id="lead_activity">
         <div class="panel_s no-shadow">
            <?php 
               $activities =render_lead_activities($lead->id,0);
               if($activities){
                  echo $activities;
                  echo '<button id="loadMoreActivities" class="btn btn-primary" data-page="1">Load More</button>';
               }else{
                  echo '<p>No activities recorded</p>';
               }
            ?>
            <div class="clearfix"></div>
         </div>
      </div>
      <div role="tabpanel" class="tab-pane <?php echo ($group=='tab_proposals_leads')?"active": "" ?>" id="tab_proposals_leads">
         <?php if(has_permission('proposals','','create')){ ?>
         <a href="<?php echo admin_url('proposals/proposal?rel_type=lead&rel_id='.$lead->id); ?>" class="btn btn-info mbot25"><?php echo _l('new_proposal'); ?></a>
         <?php } ?>
         <?php /*if(total_rows(db_prefix().'proposals',array('rel_type'=>'lead','rel_id'=>$lead->id))> 0 && (has_permission('proposals','','create') || has_permission('proposals','','edit'))){ ?>
         <a href="#" class="btn btn-info mbot25" data-toggle="modal" data-target="#sync_data_proposal_data"><?php echo _l('sync_data'); ?></a>
         <?php $this->load->view('admin/proposals/sync_data',array('related'=>$lead,'rel_id'=>$lead->id,'rel_type'=>'lead')); ?>
         <?php } */?>
         <?php
            $table_data = array(
             _l('proposal') . ' #',
             _l('proposal_subject'),
             _l('proposal_total'),
             _l('proposal_date'),
             _l('proposal_open_till'),
             _l('tags'),
             _l('proposal_date_created'),
             _l('proposal_status'));
            $custom_fields = get_custom_fields('proposal',array('show_on_table'=>1));
            foreach($custom_fields as $field){
             array_push($table_data,$field['name']);
            }
            $table_data = hooks()->apply_filters('proposals_relation_table_columns', $table_data);
            render_datatable($table_data,'proposals-lead',[], [
                'data-last-order-identifier' => 'proposals-relation',
                'data-default-order'         => get_table_last_order('proposals-relation'),
            ]);
            ?>
      </div>
      <div role="tabpanel" class="tab-pane <?php echo ($group=='tab_tasks_leads')?"active": "" ?>" id="tab_tasks_leads">
         <?php init_relation_tasks_table1(array('data-new-rel-id'=>$lead->id,'data-new-rel-type'=>'lead','no-filters'=>true)); ?>
      </div>
      <div role="tabpanel" class="tab-pane <?php echo ($group=='lead_reminders')?"active": "" ?>" id="lead_reminders">
         <a href="#" data-toggle="modal" class="btn btn-info" data-target=".reminder-modal-lead-<?php echo $lead->id; ?>"><i class="fa fa-bell-o"></i> <?php echo _l('lead_set_reminder_title'); ?></a>
         <hr />
         <?php render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified')), 'reminders-leads'); ?>
      </div>
      <div role="tabpanel" class="tab-pane <?php echo ($group=='attachments')?"active": "" ?>" id="attachments">
         <?php echo form_open('admin/leads/add_lead_attachment',array('class'=>'dropzone mtop15 mbot15','id'=>'lead-attachment-upload')); ?>
         <input type="hidden" name="id" value="<?php echo $lead->id ?>">
         <?php echo form_close(); ?>
         <?php if(get_option('dropbox_app_key') != ''){ ?>
         <hr />
         <div class="text-right">
          <button class="gpicker">
            <i class="fa fa-google" aria-hidden="true"></i>
            <?php echo _l('choose_from_google_drive'); ?>
          </button>
          <div id="dropbox-chooser-lead"></div>
        </div>
         <?php } ?>
         <?php if(count($lead->attachments) > 0) { ?>
         <div class="mtop20" id="lead_attachments">
            <?php $this->load->view('admin/leads/leads_attachments_template', array('attachments'=>$lead->attachments)); ?>
         </div>
         <?php } ?>
      </div>
      <div role="tabpanel" class="tab-pane <?php echo ($group=='lead_notes')?"active": "" ?>" id="lead_notes">
         <?php echo form_open(admin_url('leads/add_note/'.$lead->id),array('id'=>'lead-notes')); ?>
         <div class="form-group">
                <textarea id="lead_note_description" name="lead_note_description" placeholder="Take a note" class="form-control" rows="4" required></textarea>
         </div>
         <div class="lead-select-date-contacted hide">
            <?php echo render_datetime_input('custom_contact_date','lead_add_edit_datecontacted','',array('data-date-end-date'=>date('Y-m-d'))); ?>
         </div>
         <!-- <div class="radio radio-primary">
            <input type="radio" name="contacted_indicator" id="contacted_indicator_yes" value="yes">
            <label for="contacted_indicator_yes"><?php echo _l('lead_add_edit_contacted_this_lead'); ?></label>
         </div>
         <div class="radio radio-primary">
            <input type="radio" name="contacted_indicator" id="contacted_indicator_no" value="no" checked>
            <label for="contacted_indicator_no"><?php echo _l('lead_not_contacted'); ?></label>
         </div> -->
         <button type="submit" class="btn btn-info pull-right"><?php echo _l('lead_add_edit_add_note'); ?></button>
         <?php echo form_close(); ?>
         <div class="clearfix"></div>
         <hr />
         <div class="panel_s no-shadow">
            <?php
               $len = count($notes);
               $i = 0;
               foreach($notes as $note){ ?>
            <div class="media lead-note">
               <a href="<?php echo admin_url('profile/'.$note["addedfrom"]); ?>" target="_blank">
               <?php echo staff_profile_image($note['addedfrom'],array('staff-profile-image-small','pull-left mright10')); ?>
               </a>
               <div class="media-body">
                  <?php if($note['addedfrom'] == get_staff_user_id() || is_admin()){ ?>
                  <a href="#" class="pull-right text-danger" onclick="delete_lead_note(this,<?php echo $note['id']; ?>, <?php echo $lead->id; ?>);return false;"><i class="fa fa fa-times"></i></a>
                  <a href="#" class="pull-right mright5" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><i class="fa fa-pencil-square-o"></i></a>
                  <?php } ?>
                  <?php if(!empty($note['date_contacted'])){ ?>
                  <span data-toggle="tooltip" data-title="<?php echo _dt($note['date_contacted']); ?>">
                  <i class="fa fa-phone-square text-success font-medium valign" aria-hidden="true"></i>
                  </span>
                  <?php } ?>
                  <small><?php echo _l('lead_note_date_added',_dt($note['dateadded'])); ?></small>
                  <a href="<?php echo admin_url('profile/'.$note["addedfrom"]); ?>" target="_blank">
                     <h5 class="media-heading bold"><?php echo get_staff_full_name($note['addedfrom']); ?></h5>
                  </a>
                  <div data-note-description="<?php echo $note['id']; ?>" class="text-muted">
                     <?php echo check_for_links(app_happy_text($note['description'])); ?>
                  </div>
                  <div data-note-edit-textarea="<?php echo $note['id']; ?>" class="hide mtop15">
                     <?php echo render_textarea('note','',$note['description']); ?>
                     <div class="text-right">
                        <button type="button" class="btn btn-default" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                        <button type="button" class="btn btn-info" onclick="edit_note(<?php echo $note['id']; ?>);"><?php echo _l('update_note'); ?></button>
                     </div>
                  </div>
               </div>
               <?php if ($i >= 0 && $i != $len - 1) {
                  echo '<hr />';
                  }
                  ?>
            </div>
            <?php $i++; } ?>
         </div>
      </div>
      <?php } ?>
    </div>

    </div>
    </div>
    
   </div>
   </div>
<?php hooks()->do_action('lead_modal_profile_bottom',(isset($lead) ? $lead->id : '')); ?>

<?php init_tail(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput-jquery.min.js"></script>
<script>
   $(document).ready(function () {
      init_rel_tasks_table(<?php echo $lead->id; ?>,'lead','.table-rel-tasks-leads');

      $('#loadMoreActivities').click(function(){
         var page =$(this).attr('data-page');
         $.ajax({
            type: 'GET',
            url: admin_url+'leads/load_more_activities/'+<?php echo $lead->id ?>,
            data: {page:page},
            dataType: "json",
            success: function(resultData) { 
               if(resultData.success==true){
                  if(resultData.content){
                     $('#lead_activities_wrapper').append(resultData.content);
                     $('#loadMoreActivities').attr('data-page',parseInt(page)+1);
                  }else{
                     $('#loadMoreActivities').remove();
                  }
               }else{
                  $('#loadMoreActivities').remove();
               }
            }
         });
      })
   });
</script>
</body>
</html>