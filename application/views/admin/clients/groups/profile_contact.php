<style>
.btn-bottom-toolbar {left:10px;}
.btn-disable {
  pointer-events: none;
  opacity:0.5;
}
</style>
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php 
      $my_staffids = $this->staff_model->get_my_staffids();
      $view_ids = $this->staff_model->getFollowersViewList();
      $teamleader = $contact->addedfrom;
      //pr($task);
      $btn = '';
      if($teamleader) {
        if ((!empty($my_staffids) && in_array($teamleader,$my_staffids) && !in_array($teamleader,$view_ids)) || is_admin(get_staff_user_id()) || $teamleader == get_staff_user_id()) {
              $btn = '';
        } else {
              $btn = 'btn-disable';
        }
      }
?>
<h4 class="customer-profile-group-heading"><?php echo _l('client_add_edit_profile'); ?>
<?php if($contact->deleted_status == 1) { ?>
    <a href="<?php echo admin_url('clients/restore_contact/'.$contact->id); ?>" style="float:right; margin-top:-6px;" class="btn btn-info"><?php echo _l('restore'); ?></a>
<?php } ?>
</h4>
<?php if($this->session->flashdata('gdpr_restore_warning')){ ?>
    <div class="alert alert-success">
     Contact has been Restored.
   </div>
<?php } ?>
<?php echo form_open(admin_url('clients/form_edit_contact/'.$contact->userid.'/'.$contact->id),array('id'=>'contact-form','autocomplete'=>'off')); ?>
<div class="row">
    <div class="additional"></div>
    <div class="col-md-12">
        <!-- <div class="col-md-3">
            <?php if(isset($contact)){ ?>
            <img src="<?php echo contact_profile_image_url($contact->id,'thumb'); ?>" id="contact-img"
                class="client-profile-image-thumb">

            <?php } ?>
        </div> -->
                <div class="col-md-6">   
                    <div class="form-group">
                        <label for="website"><?php echo _l('client_firstname'); ?></label>
                        <div class="">
                            <input type="text" value="<?php echo $contact->firstname; ?>" class="form-control" name="firstname" >
                        </div>
                    </div>     
                </div>
                <div class="col-md-6">   
                    <div class="form-group">
                        <label for="website"><?php echo _l('contact_position'); ?></label>
                        <div class="">
                            <input type="text" value="<?php echo $contact->title; ?>" class="form-control" name="title" >
                        </div>
                    </div>     
                </div>
                <div class="col-md-6">   
                    <div class="form-group">
                        <label for="website"><?php echo _l('client_email'); ?></label>
                        <div class="">
                            <input type="text" value="<?php echo $contact->email; ?>" class="form-control" name="email" >
                        </div>
                    </div>     
                </div>
                
                <?php if($contact->alternative_emails != ''){ ?>
                <div class="col-md-6">   
                    <div class="form-group">
                        <label for="website"><?php echo _l('alternative',_l('client_email')); ?></label>
                        <div class="">
                        <input type="text" value="<?php echo $contact->alternative_emails; ?>" class="form-control" name="alternative_emails" >
                        <?php
                                // foreach($contact->alternative_emails as $aekey => $aeval){
                                //     echo '<div class="">
                                //     <input type="text" value="'.$aeval.'" class="form-control" name="alternative_emails[]" >
                                // </div>';
                                // }
                        ?>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <div class="col-md-6">   
                    <div class="form-group">
                        <label for="website"><?php echo _l('client_phonenumber'); ?></label>
                        <div class="">
                            <input type="text" value="<?php echo $contact->phonenumber; ?>" class="form-control" name="phonenumber" >
                        </div>
                    </div>     
                </div>
                
                <?php if($contact->alternative_phonenumber != ''){ ?>
                <div class="col-md-6">   
                    <div class="form-group">
                        <label for="website"><?php echo _l('alternative',_l('client_phonenumber')); ?></label>
                        <div class=""><input type="text" value="<?php echo $contact->alternative_phonenumber; ?>" class="form-control" name="alternative_phonenumber" >
                        <?php
                                // foreach($contact->alternative_phonenumber as $aekey => $aeval){
                                //     echo '<div class="">
                                //     <input type="text" value="'.$aeval.'" class="form-control" name="alternative_phonenumber[]" >
                                // </div>';
                                // }
                        ?>
                        </div>
                    </div>
                </div>
                <?php } 
                ?>
                    
                
                <div class="form-group col-md-6 select-placeholder clientiddiv input-group-select">
                            <label for="clientid" class="control-label"><?php echo _l('project_customer'); ?></label>
							<div class="input-group input-group-select select-groups_in[]">
                            <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                             <?php 
                             $selected = (isset($contact->userid) ? $contact->userid: '');
                             
                             if(is_array($selected)){
                                 foreach ($selected as $keys => $values) {
                                      $rel_data = get_relation_data('customer',$values);
                                        $rel_val = get_relation_values($rel_data,'customer');
                                        echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                                 }
                             }elseif($selected != ''){
                                $rel_data = get_relation_data('customer',$selected);
                                $rel_val = get_relation_values($rel_data,'customer');
                                echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                            } ?>
                        </select>
						
						<div class="input-group-addon" style="opacity: 1;"><a href="#" data-toggle="modal" data-target="#clientid_add_modal"><i class="fa fa-plus"></i></a></div>
                    </div> </div>
                <?php echo render_custom_fields( 'contacts',$contact->id); ?>				
        </div>
    </div>
    <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
            <button class="btn btn-info only-save customer-form-submiter <?php echo $btn; ?>">
            <?php echo _l( 'submit'); ?>
            </button>
            <?php if(!isset($client)){ ?>
            <button class="btn btn-info save-and-add-contact customer-form-submiter <?php echo $btn; ?>">
            <?php echo _l( 'save_customer_and_add_contact'); ?>
            </button>
            <?php } ?>
    </div>
         
