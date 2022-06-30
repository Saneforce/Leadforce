<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
<div class="content">
   <div class="row">
      <div class="col-md-7">
         <div class="panel_s">
            <div class="panel-body">
               <h4 class="no-margin">
                  <?php echo $title; ?>
               </h4>
               <hr class="hr-panel-heading" />
               <?php if(isset($designation)){ ?>
               <a href="<?php echo admin_url('designation/designations'); ?>" class="btn btn-success pull-right mbot20 display-block"><?php echo _l('new_designation'); ?></a>
               <div class="clearfix"></div>
               <?php } ?>
               <?php echo form_open($this->uri->uri_string()); ?>
               <?php if(isset($designation)){ ?>
               <?php if(total_rows(db_prefix().'staff',array('designation'=>$designation->designationid)) > 0){ ?>
               <div class="alert alert-warning bold">
                  <?php echo _l('change_designation_permission_warning'); ?>
                  <div class="checkbox">
                     <input type="checkbox" name="update_staff_permissions" id="update_staff_permissions">
                     <label for="update_staff_permissions"><?php echo _l('designation_update_staff_permissions'); ?></label>
                  </div>
               </div>
               <?php } ?>
               <?php } ?>
               <?php $attrs = (isset($designation) ? array() : array('autofocus'=>true)); ?>
               <?php $value = (isset($designation) ? $designation->name : ''); ?>
               <?php echo render_input('name','designation_add_edit_name',$value,'text',$attrs); ?>
               <?php
               $roleselected = '';
               foreach($roles as $role)
               {
                  if(isset($designation))
                  {
                     if($designation->roleid == $role['roleid']) {
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
               <?php echo render_select('roleid',$roles,array('roleid','name'),'staff_add_edit_role',$roleselected); ?>
                <?php /*
                  $permissionsData = [ 'funcData' => ['designation'=> isset($designation) ? $designation : null ] ];
                  if(isset($designation)) {
                     $permissionsData['designation'] = $designation;
                  }
                  $this->load->view('admin/staff/permissions', $permissionsData);
              */ ?>
               <hr />
                  <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                  <?php echo form_close(); ?>
            </div>
         </div>
      </div>
      <?php if(isset($designation_staff)) { ?>
      <div class="col-md-5">
         <div class="panel_s">
            <div class="panel-body">
               <h4 class="no-margin">
                  <?php echo _l('staff_which_are_using_designation'); ?>
               </h4>
               <hr class="hr-panel-heading" />
               <div class="table-responsive">
                  <table class="table dt-table">
                     <thead>
                        <tr>
                           <th><?php echo _l('staff_dt_name'); ?></th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach($designation_staff as $staff) { ?>
                        <tr>
                           <td>
                              <?php
                                 echo '<a href="' . admin_url('staff/profile/' . $staff['staffid']) . '">' . staff_profile_image($staff['staffid'], [
                                   'staff-profile-image-small',
                                 ]) . '</a>';
                                 echo ' <a href="' . admin_url('staff/member/' . $staff['staffid']) . '">' . $staff['firstname'] . ' ' . $staff['lastname'] . '</a>';
                                 ?>
                           </td>
                        </tr>
                        <?php } ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
      <?php } ?>
   </div>
</div>
<?php init_tail(); ?>
<script>
   $(function(){
     appValidateForm($('form'),{name:'required'});
   });
</script>
</body>
</html>
