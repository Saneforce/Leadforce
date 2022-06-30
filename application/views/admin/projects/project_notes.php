<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<p><?php echo _l('project_note_private'); ?></p>
<hr />
<?php echo form_open(admin_url('projects/save_note/'.$project->id)); ?>
<?php echo render_textarea('content','','',array(),array(),'','tinymce'); ?>
<button type="submit" id="addprojnote" class="btn btn-info"><?php echo _l('project_save_note'); ?></button>
<?php echo form_close(); ?>
<div class="clearfix"></div>
<div class="mtop25"></div>
<div class="modal fade edit_note" id="edit_note" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('edit_note'); ?></h4>
        </div>
        <form name="editnote" id="editnote" method="post" action="<?php echo admin_url('projects/edit_note/'.$project->id)?>">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p class="bold"><?php echo _l('project_notes'); ?></p>
                        <div class="form-group"><textarea id="editcontent" name="content" class="form-control" rows="4" placeholder="Add Description"></textarea></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <a href="#" class="btn btn-info" onclick="project_submit_note(this); return false;"><?php echo _l('save_note'); ?></a>
            </div>
            <input type="hidden" name="id" id="noteid" value="">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
        </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="clearfix"></div> 
<table class="table dt-table scroll-responsive table-project-files" data-order-col="3" data-order-type="desc">
  <thead>
    <tr>
      <th><?php echo _l('project_notes_created_by'); ?></th>
      <th><?php echo _l('project_notes'); ?></th>
      <th><?php echo _l('project_notes_date'); ?></th>
      <th><?php echo _l('options'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php
    //pre($notes);
    foreach($notes as $note){
      ?>
      <tr>
       
        <td data-order="<?php echo $note['fullname']; ?>">
                <?php echo $note['fullname']; ?>
        </td>
          <td data-order="<?php echo $note['content']; ?>"><?php echo $note['content']; ?></td>
          <td data-order="<?php echo $note['dateadded']; ?>"><?php echo _dt($note['dateadded']); ?></td>
         
         <td>
            <a href="#" onclick="edit_notes(<?php echo $note['id']; ?>); return false;" class="btn btn-info btn-icon"><i class="fa fa-edit"></i></a>
           <?php if($file['staffid'] == get_staff_user_id() || has_permission('projects','','delete')){ ?>
           <a href="<?php echo admin_url('projects/remove_notes/'.$project->id.'/'.$note['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
           <?php } ?>
         </td>
       </tr>
       <?php } ?>
     </tbody>
   </table>
