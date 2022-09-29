<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Project Tasks -->
<?php
    if($project->settings->hide_tasks_on_main_tasks_table == '1') {
        echo '<i class="fa fa-exclamation fa-2x pull-left" data-toggle="tooltip" data-title="'._l('project_hide_tasks_settings_info').'"></i>';
    }
?>
<div class="tasks-table">


    <?php init_relation_tasks_table(array( 'data-new-rel-id'=>$project->id,'data-new-rel-type'=>'project')); ?>
    

<hr class="hr-panel-heading">
<?php /*<h4 class="mbot15">Files</h2>
<a href="#" data-toggle="modal" data-target="#project_files_bulk_actions" class="bulk-actions-btn table-btn hide" data-table=".table-project-files">
  <?php echo _l('bulk_actions'); ?>
</a>

<a href="#" onclick="window.location.href = '<?php echo admin_url('projects/download_all_files/'.$project->id); ?>'; return false;" class="table-btn hide" data-table=".table-project-files"><?php echo _l('download_all'); ?></a>
<div class="clearfix"></div> 
<table class="table dt-table scroll-responsive table-project-files" data-order-col="7" data-order-type="desc">
  <thead>
    <tr>
      <th data-orderable="false"><span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="project-files"><label></label></div></th>
      <th><?php echo _l('project_file_filename'); ?></th>
      <th><?php echo _l('project_file__filetype'); ?></th>
      <th><?php echo _l('project_discussion_last_activity'); ?></th>
      <th><?php echo _l('project_discussion_total_comments'); ?></th>
      <th><?php echo _l('project_file_visible_to_customer'); ?></th>
      <th><?php echo _l('project_file_uploaded_by'); ?></th>
      <th><?php echo _l('project_file_dateadded'); ?></th>
      <th><?php echo _l('options'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($files as $file){
      $path = get_upload_path_by_type('project') . $project->id . '/'. $file['file_name'];
      ?>
      <tr>
        <td>
          <div class="checkbox"><input type="checkbox" value="<?php echo $file['id']; ?>"><label></label></div>
        </td>
        <td data-order="<?php echo $file['file_name']; ?>">
          <a href="#" onclick="view_project_file(<?php echo $file['id']; ?>,<?php echo $file['project_id']; ?>); return false;">
            <?php if(is_image(PROJECT_ATTACHMENTS_FOLDER .$project->id.'/'.$file['file_name']) || (!empty($file['external']) && !empty($file['thumbnail_link']))){
              echo '<div class="text-left"><i class="fa fa-spinner fa-spin mtop30"></i></div>';
              echo '<img class="project-file-image img-table-loading" src="#" data-orig="'.project_file_url($file,true).'" width="100">';
              echo '</div>';
            }
            echo $file['subject']; ?></a>
          </td>
          <td data-order="<?php echo $file['filetype']; ?>"><?php echo $file['filetype']; ?></td>
          <td data-order="<?php echo $file['last_activity']; ?>">
            <?php
            if(!is_null($file['last_activity'])){ ?>
            <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($file['last_activity']); ?>">
              <?php echo time_ago($file['last_activity']); ?>
            </span>
            <?php } else {
              echo _l('project_discussion_no_activity');
            }
            ?>
          </td>
          <?php $total_file_comments = total_rows(db_prefix().'projectdiscussioncomments',array('discussion_id'=>$file['id'],'discussion_type'=>'file')); ?>
          <td data-order="<?php echo $total_file_comments; ?>">
            <?php echo $total_file_comments; ?>
          </td>
          <td data-order="<?php echo $file['visible_to_customer']; ?>">
            <?php
            $checked = '';
            if($file['visible_to_customer'] == 1){
              $checked = 'checked';
            }
            ?>
            <div class="onoffswitch">
              <input type="checkbox" data-switch-url="<?php echo admin_url(); ?>projects/change_file_visibility" id="<?php echo $file['id']; ?>" data-id="<?php echo $file['id']; ?>" class="onoffswitch-checkbox" value="<?php echo $file['id']; ?>" <?php echo $checked; ?>>
              <label class="onoffswitch-label" for="<?php echo $file['id']; ?>"></label>
            </div>

          </td>
          <td>
            <?php if($file['staffid'] != 0){
              $_data = '<a href="' . admin_url('staff/profile/' . $file['staffid']). '">' .staff_profile_image($file['staffid'], array(
                'staff-profile-image-small'
              )) . '</a>';
              $_data .= ' <a href="' . admin_url('staff/member/' . $file['staffid'])  . '">' . get_staff_full_name($file['staffid']) . '</a>';
              echo $_data;
            } else {
             echo ' <img src="'.contact_profile_image_url($file['contact_id'],'thumb').'" class="client-profile-image-small mrigh5">
             <a href="'.admin_url('clients/client/'.get_user_id_by_contact_id($file['contact_id']).'?contactid='.$file['contact_id']).'">'.get_contact_full_name($file['contact_id']).'</a>';
           }
           ?>
         </td>
         <td data-order="<?php echo $file['dateadded']; ?>"><?php echo _dt($file['dateadded']); ?></td>
         <td>
           <?php if(empty($file['external'])){ ?>
           <button type="button" data-toggle="modal" data-original-file-name="<?php echo $file['file_name']; ?>" data-filetype="<?php echo $file['filetype']; ?>" data-path="<?php echo PROJECT_ATTACHMENTS_FOLDER .$project->id.'/'.$file['file_name']; ?>" data-target="#send_file" class="btn btn-info btn-icon"><i class="fa fa-envelope"></i></button>
           <?php } ?>
           <?php if($file['staffid'] == get_staff_user_id() || has_permission('projects','','delete')){ ?>
           <a href="<?php echo admin_url('projects/remove_file/'.$project->id.'/'.$file['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
           <?php } ?>
         </td>
       </tr>
       <?php } ?>
     </tbody>
   </table>
   <div id="project_file_data"></div>
   <?php include_once(APPPATH . 'views/admin/clients/modals/send_file_modal.php'); */?>




</div>

<script>

// function gettasks_summary_data(){
//       $.ajax({
//           url: admin_url + 'tasks/gettasks_summary_data',
//           success: function(msg){
//             $('#tasks_summary_data').html(msg);
//           }
//       });
//    }
</script>