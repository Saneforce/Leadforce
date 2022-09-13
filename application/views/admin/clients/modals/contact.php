<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.btn-disable {
  pointer-events: none;
  opacity:0.5;
}
</style>
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
<!-- Modal Contact -->
<div class="modal fade" id="contact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php echo form_open(admin_url('clients/form_contact/'.$customer_id.'/'.$contactid),array('id'=>'contact-form','autocomplete'=>'off')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $title; ?><br /><small class="color-white" id=""><?php echo get_company_name($customer_id,true); ?></small></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php if(isset($contact)){ ?>
                        <img src="<?php echo contact_profile_image_url($contact->id,'thumb'); ?>" id="contact-img" class="client-profile-image-thumb">
                        <?php if(!empty($contact->profile_image)){ ?>
                        <a href="#" onclick="delete_contact_profile_image(<?php echo $contact->id; ?>); return false;" class="text-danger pull-right" id="contact-remove-img"><i class="fa fa-remove"></i></a>
                        <?php } ?>
                        <hr />
                        <?php } ?>
                        <div id="contact-profile-image" class="form-group<?php if(isset($contact) && !empty($contact->profile_image)){echo ' hide';} ?>">
                            <label for="profile_image" class="profile-image"><?php echo _l('client_profile_image'); ?></label>
                            <input type="file" name="profile_image" class="form-control" id="profile_image">
                        </div>
                        <?php if(isset($contact)){ ?>
                        <div class="alert alert-warning hide" role="alert" id="contact_proposal_warning">
                            <?php echo _l('proposal_warning_email_change',array(_l('contact_lowercase'),_l('contact_lowercase'),_l('contact_lowercase'))); ?>
                            <hr />
                            <a href="#" id="contact_update_proposals_emails" data-original-email="" onclick="update_all_proposal_emails_linked_to_contact(<?php echo $contact->id; ?>); return false;"><?php echo _l('update_proposal_email_yes'); ?></a>
                            <br />
                            <a href="#" onclick="close_modal_manually('#contact'); return false;"><?php echo _l('update_proposal_email_no'); ?></a>
                        </div>
                        <?php } ?>
                        <!-- // For email exist check -->
                        <input type="hidden" id="contactid" name="contactid" value="<?php echo $contactid; ?>">
                        <?php //echo form_hidden('contactid',$contactid); ?>
                        <?php $value=( isset($contact) ? $contact->firstname : ''); ?>
                        <?php //echo render_input( 'firstname', 'client_firstname',$value); ?>
                        <div class="form-group" app-field-wrapper="firstname">
                            <label for="firstname" class="control-label"> <small class="req text-danger">* </small>Name</label>
                            <input type="text" id="firstname" name="firstname" class="form-control" value="<?php echo $value; ?>" onkeypress="return event.charCode == 32 || (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123)">
                        </div>
                        <div id="company_exists_info" class="hide"></div>
                        <!-- <?php $value=( isset($contact) ? $contact->lastname : ''); ?>
                        <?php echo render_input( 'lastname', 'client_lastname',$value); ?> -->
                        <?php $value=( isset($contact) ? $contact->title : ''); ?>
                        <?php echo render_input( 'title', 'contact_position',$value); ?>
                        <?php $value=( isset($contact) ? $contact->email : ''); ?>
                        <!-- <?php echo render_input( 'email', 'client_email',$value, 'email'); ?> -->

                        <div class="form-group" app-field-wrapper="email">
                     
                        <label for="email" class="control-label">Email </label>
                        <div class="input-group">
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo $value; ?>">
                        <div class="input-group-addon"><span class="add_field_button_ae pointer "><i class="fa fa fa-plus"></i></span></div>
                        </div>
                        </div>
                        <!-- <label class="control-label"><?php echo _l('alternative',_l('client_email')); ?>
                        
                        </label> -->
                        <div class="input_fields_wrap_ae">
                        <?php  if(isset($contact->alternative_emails) && count($contact->alternative_emails) > 0){
                            foreach($contact->alternative_emails as $aekey => $aeval){
                                echo '<div class="input-group form-group"><input type="text" value="'.$aeval.'" placeholder="'._l('client_email').'"  id="alternative_emails" name="alternative_emails[]" class="form-control"><div class="input-group-addon"><span class="pointer  input-group-text remove_field_ae text-danger"><i class="fa fa-times"></i></span></div></div>';
                            }
                        }else{ ?>
                        <!-- <div class="input-group"><input type="email" placeholder="<?php echo _l('client_email') ?>""  id="alternative_emails" name="alternative_emails[]" class="form-control"><div class="input-group-addon"><span class="input-group-text remove_field_ae text-danger"><i class="fa fa-times"></i></span></div></div> -->
                        <?php } ?>
                        </div>
                        
                        <?php $value=( isset($contact) ? $contact->phonenumber : ''); ?>
                        <!-- <?php echo render_input( 'phonenumber', 'client_phonenumber',$value,'text',array('autocomplete'=>'off','style'=>'padding-left:90px !important')); ?> -->

                        <div class="form-group" app-field-wrapper="phonenumber" id="phonenumber_iti_wrapper">
                            <label for="phonenumber" class="control-label">Phone  </label>
                            <div class="input-group">
                            <input type="text" id="phonenumber" name="phonenumber" class="form-control" autocomplete="off" value="<?php echo $value; ?>">
                            <div class="input-group-addon"><span class="add_field_button_ap pointer "><i class="fa fa fa-plus"></i></span></div>
                            </div>
                        </div>
                        <input type="hidden" name="phone_country_code" id="phone_country_code" value="<?php echo ( isset($contact) ? $contact->phone_country_code : 'IN'); ?>">
                        <!-- <label class="control-label"><?php echo _l('alternative',_l('client_phonenumber')); ?>
                        
                        </label> -->
                        <div class="input_fields_wrap_ap">
                        <?php  if(isset($contact->alternative_phonenumber) && count($contact->alternative_phonenumber) > 0){
                            foreach($contact->alternative_phonenumber as $apkey => $apval){
                                echo '<div class="input-group form-group"><input type="text" value="'.$apval.'" placeholder="'._l('client_phonenumber').'"  id="alternative_phonenumber" name="alternative_phonenumber[]" class="form-control"><div class="input-group-addon"><span class="pointer input-group-text remove_field_ap text-danger"><i class="fa fa-times"></i></span></div></div>';
                            }
                        }else{ ?>
                        <!-- <div class="input-group"><input type="text" placeholder="<?php echo _l('client_phonenumber') ?>""  id="alternative_phonenumber" name="alternative_phonenumber[]" class="form-control"><div class="input-group-addon"><span class="input-group-text remove_field_ap text-danger"><i class="fa fa-times"></i></span></div></div> -->
                        <?php } ?>
                        </div>
                       
<!--                        <div class="form-group contact-direction-option">
                          <label for="direction"><?php echo _l('document_direction'); ?></label>
                          <select class="selectpicker" data-none-selected-text="<?php echo _l('system_default_string'); ?>" data-width="100%" name="direction" id="direction">
                            <option value="" <?php if(isset($contact) && empty($contact->direction)){echo 'selected';} ?>></option>
                            <option value="ltr" <?php if(isset($contact) && $contact->direction == 'ltr'){echo 'selected';} ?>>LTR</option>
                            <option value="rtl" <?php if(isset($contact) && $contact->direction == 'rtl'){echo 'selected';} ?>>RTL</option>
                        </select>
                    </div>-->

                    <script>

                    $(document).ready(function() {

                        //--------------------alternative_emails
                        var max_fields      = 25; //maximum input boxes allowed
                        var wrapper   		= $(".input_fields_wrap_ae"); //Fields wrapper
                        var add_button      = $(".add_field_button_ae"); //Add button ID
                        
                        var x = <?php echo isset($contact)?count($contact->alternative_emails):0; ?>; //initlal text box count
                        $(add_button).click(function(e){ //on add input button click
                            e.preventDefault();
                            if(x < max_fields){ //max input box allowed
                                x++; //text box increment
                                $(wrapper).append('<div class="input-group form-group"><input type="email" placeholder="<?php echo _l('client_email') ?>" id="alternative_emails" name="alternative_emails[]" class="form-control"><div class="input-group-addon"><span class="pointer input-group-text remove_field_ae text-danger"><i class="fa fa-times"></i></span></div></div>'); //add input box
                            }
                        });
                        $(wrapper).on("click",".remove_field_ae", function(e){ //user click on remove text
                            e.preventDefault(); $(this).parent('div').parent('div').remove(); x--;
                        })

                        //--------------------alternative_phonenumber
                        var max_fields_ap      = 25; //maximum input boxes allowed
                        var wrapper_ap  		= $(".input_fields_wrap_ap"); //Fields wrapper
                        var add_button_ap     = $(".add_field_button_ap"); //Add button ID
                        
                        var x_ap = <?php echo isset($contact)?count($contact->alternative_phonenumber):0; ?>; //initlal text box count
                        $(add_button_ap).click(function(e){ //on add input button click
                            e.preventDefault();
                            if(x < max_fields_ap){ //max input box allowed
                                x_ap++; //text box increment
                                $(wrapper_ap).append('<div class="input-group form-group"><input type="text" placeholder="<?php echo _l('client_phonenumber') ?>""  id="alternative_phonenumber" name="alternative_phonenumber[]" class="form-control"><div class="input-group-addon"><span class="pointer input-group-text remove_field_ap text-danger"><i class="fa fa-times"></i></span></div></div>'); //add input box
                            }
                        });
                        $(wrapper_ap).on("click",".remove_field_ap", function(e){ //user click on remove text
                            e.preventDefault(); $(this).parent('div').parent('div').remove(); x_ap--;
                        })
                    });
                    </script>
<?php  if(!empty($need_fields) && in_array("clientid", $need_fields)){?>
<div class="form-group select-placeholder clientiddiv input-group-select">
                            <label for="clientid" class="control-label"> <small class="req text-danger"><?php  if(!empty($mandatory_fields) && in_array("clientid", $mandatory_fields)){echo '*';}?> </small><?php echo _l('project_customer'); ?></label>
							<input type="hidden" id="ch_client" value="<?php  if(!empty($mandatory_fields) && in_array("clientid", $mandatory_fields)){echo '2';}else{ echo '1';}?>">
							<div class="input-group input-group-select select-groups_in[]">
                            <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search selectpicker" data-none-selected-text="Nothing selected">
                             <?php $selected = (isset($contact) ? $contact->clientid : '');
                             if($selected == ''){
                                 $selected = (isset($contact->clientid) ? $contact->clientid: '');
                             }
							 if($selected == ''){
                                 $selected = (isset($customer_id) ? $customer_id: '');
                             }
							// echo '<option value="" >Select</option>';
                             if(is_array($selected)){
                                 foreach ($selected as $keys => $values) {
                                      $rel_data = get_relation_data('customer',$values);
                                        $rel_val = get_relation_values($rel_data,'customer');
										/*if(!empty($all_clients)){
											foreach($all_clients as $key=>$all_client12){
												echo '<option value="'.$key.'" >'.$all_client12.'</option>';
											}
										}*/
                                        echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                                 }
                             }elseif($selected != ''){
                                $rel_data = get_relation_data('customer',$selected);
                                $rel_val = get_relation_values($rel_data,'customer');
								/*if(!empty($all_clients)){
									foreach($all_clients as $key=>$all_client12){
										if($selected == $key){
											echo '<option value="'.$key.'" selected>'.$all_client12.'</option>';
										}else{
											echo '<option value="'.$key.'" >'.$all_client12.'</option>';
										}
									}
								}*/
                                echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                            } ?>
                        </select>
						
						<div class="input-group-addon" style="opacity: 1;"><a href="#" <?php /*data-toggle="modal" data-target="#clientid_add_modal" */ ?> onclick="open_modal('clientid_add_modal')"><i class="fa fa-plus"></i></a></div>
                    </div> </div>

                   
<?php }
                        $selected = array();
                        if(isset($project_members)){
                            foreach($project_members as $member){
                                array_push($selected,$member['staff_id']);
                            }
                        } else {
                            array_push($selected,get_staff_user_id());
                        }

                        if(isset($deals_contact)){
                            $dc = array();
                            foreach($deals_contact as $dealcontact){
                                $dc[] = $dealcontact['project_id'];
                            }
                        }
                        //echo render_select('deals[]',$staff,array('staffid',array('firstname','lastname')),'project_members',$selected,array('multiple'=>true,'class'=>'formassigned','data-actions-box'=>true),array(),'','',false);
                    ?>
                    <div class="form-group" app-field-wrapper="deals[]">
                        <div class="input-group" style="width: 100%;">
                            <label for="deals[]" class="control-label">Deals</label>
                            <div class="dropdown bootstrap-select input-group-select show-tick bs3 bs3-has-addon" style="width: 100%;">
            
                                <select id="deals[]" name="deals[]" class="selectpicker deals" multiple="1" data-actions-box="1" data-width="94%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98">
                                <?php
                                    if(isset($deals)){
                                        foreach($deals as $deal){
                                            $selected = '';
                                            if( in_array( $deal['id'] ,$dc ) ) {
                                                $selected = 'selected';
                                            }
                                            echo '<option value="'.$deal['id'].'" '.$selected.'>'.$deal['name'].'</option>';
                                        }
                                    }
                                ?>
                                </select>
                                <div class="input-group-addon" style="opacity: 1;width: 34px;float: right;height: 36px;padding-top: 11px;"><a href="#" <?php /*data-toggle="modal" data-target="#projectid_add_modal"*/ ?> onclick="open_modal('projectid_add_modal')" ><i class="fa fa-plus"></i></a></div>
                            </div>
                            
                        </div>
                    </div>
                    <?php $rel_id=( isset($contact) ? $contact->id : false); ?>
                    <?php echo render_custom_fields( 'contacts',$rel_id); ?>
                    

                            <!-- -->

                    <!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
                    <!-- <input  type="text" class="fake-autofill-field" name="fakeusernameremembered" value='' tabindex="-1" />
                    <input  type="password" class="fake-autofill-field" name="fakepasswordremembered" value='' tabindex="-1"/>

                    <div class="client_password_set_wrapper">
                        <label for="password" class="control-label">
                            <?php echo _l( 'client_password'); ?>
                        </label>
                        <div class="input-group">

                            <input type="password" class="form-control password" name="password" autocomplete="false">
                            <span class="input-group-addon">
                                <a href="#password" class="show_password" onclick="showPassword('password'); return false;"><i class="fa fa-eye"></i></a>
                            </span>
                            <span class="input-group-addon">
                                <a href="#" class="generate_password" onclick="generatePassword(this);return false;"><i class="fa fa-refresh"></i></a>
                            </span>
                        </div>
                        <?php if(isset($contact)){ ?>
                        <p class="text-muted">
                            <?php echo _l( 'client_password_change_populate_note'); ?>
                        </p>
                        <?php if($contact->last_password_change != NULL){
                            echo _l( 'client_password_last_changed');
                            echo '<span class="text-has-action" data-toggle="tooltip" data-title="'._dt($contact->last_password_change).'"> ' . time_ago($contact->last_password_change) . '</span>';
                        }
                    } ?>
                </div>
                <hr /> -->
                <!-- <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="is_primary" id="contact_primary" <?php if((!isset($contact) && total_rows(db_prefix().'contacts',array('is_primary'=>1,'userid'=>$customer_id)) == 0) || (isset($contact) && $contact->is_primary == 1)){echo 'checked';}; ?> <?php if((isset($contact) && total_rows(db_prefix().'contacts',array('is_primary'=>1,'userid'=>$customer_id)) == 1 && $contact->is_primary == 1)){echo 'disabled';} ?>>
                    <label for="contact_primary">
                        <?php echo _l( 'contact_primary'); ?>
                    </label>
                </div>
                <?php if(!isset($contact) && total_rows(db_prefix().'emailtemplates',array('slug'=>'new-client-created','active'=>0)) == 0){ ?>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="donotsendwelcomeemail" id="donotsendwelcomeemail">
                    <label for="donotsendwelcomeemail">
                        <?php echo _l( 'client_do_not_send_welcome_email'); ?>
                    </label>
                </div>
                <?php } ?>
                <?php if(total_rows(db_prefix().'emailtemplates',array('slug'=>'contact-set-password','active'=>0)) == 0){ ?>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="send_set_password_email" id="send_set_password_email">
                    <label for="send_set_password_email">
                        <?php echo _l( 'client_send_set_password_email'); ?>
                    </label>
                </div>
                <?php } ?>
                <hr />
                <p class="bold"><?php echo _l('customer_permissions'); ?></p>
                <p class="text-danger"><?php echo _l('contact_permissions_info'); ?></p> -->
                <?php
                $default_contact_permissions = array();
                if(!isset($contact)){
                    $default_contact_permissions = @unserialize(get_option('default_contact_permissions'));
                }
                ?>
                <!-- <?php foreach($customer_permissions as $permission){ ?>
                <div class="col-md-6 row">
                    <div class="row">
                        <div class="col-md-6 mtop10 border-right">
                            <span><?php echo $permission['name']; ?></span>
                        </div>
                        <div class="col-md-6 mtop10">
                            <div class="onoffswitch">
                                <input type="checkbox" id="<?php echo $permission['id']; ?>" class="onoffswitch-checkbox" <?php if(isset($contact) && has_contact_permission($permission['short_name'],$contact->id) || is_array($default_contact_permissions) && in_array($permission['id'],$default_contact_permissions)){echo 'checked';} ?> value="<?php echo $permission['id']; ?>" name="permissions[]">
                                <label class="onoffswitch-label" for="<?php echo $permission['id']; ?>"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <?php } ?>
                 <hr /> -->
                <!-- <p class="bold"><?php echo _l('email_notifications'); ?><?php if(is_sms_trigger_active()){echo '/SMS';} ?></p>
                <div id="contact_email_notifications">
                <div class="col-md-6 row">
                    <div class="row">
                        <div class="col-md-6 mtop10 border-right">
                            <span><?php echo _l('invoice'); ?></span>
                        </div>
                        <div class="col-md-6 mtop10">
                            <div class="onoffswitch">
                                <input type="checkbox" id="invoice_emails" data-perm-id="1" class="onoffswitch-checkbox" <?php if(isset($contact) && $contact->invoice_emails == '1'){echo 'checked';} ?>  value="invoice_emails" name="invoice_emails">
                                <label class="onoffswitch-label" for="invoice_emails"></label>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- <div class="col-md-6 row">
                    <div class="row">
                        <div class="col-md-6 mtop10 border-right">
                            <span><?php echo _l('estimate'); ?></span>
                        </div>
                        <div class="col-md-6 mtop10">
                            <div class="onoffswitch">
                                <input type="checkbox" id="estimate_emails" data-perm-id="2" class="onoffswitch-checkbox" <?php if(isset($contact) && $contact->estimate_emails == '1'){echo 'checked';} ?>  value="estimate_emails" name="estimate_emails">
                                <label class="onoffswitch-label" for="estimate_emails"></label>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- <div class="col-md-6 row">
                    <div class="row">
                        <div class="col-md-6 mtop10 border-right">
                            <span><?php echo _l('credit_note'); ?></span>
                        </div>
                        <div class="col-md-6 mtop10">
                            <div class="onoffswitch">
                                <input type="checkbox" id="credit_note_emails" data-perm-id="1" class="onoffswitch-checkbox" <?php if(isset($contact) && $contact->credit_note_emails == '1'){echo 'checked';} ?>  value="credit_note_emails" name="credit_note_emails">
                                <label class="onoffswitch-label" for="credit_note_emails"></label>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- <div class="col-md-6 row">
                    <div class="row">
                        <div class="col-md-6 mtop10 border-right">
                            <span><?php echo _l('project'); ?></span>
                        </div>
                        <div class="col-md-6 mtop10">
                            <div class="onoffswitch">
                                <input type="checkbox" id="project_emails" data-perm-id="6" class="onoffswitch-checkbox" <?php if(isset($contact) && $contact->project_emails == '1'){echo 'checked';} ?>  value="project_emails" name="project_emails">
                                <label class="onoffswitch-label" for="project_emails"></label>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- <div class="col-md-6 row">
                    <div class="row">
                        <div class="col-md-6 mtop10 border-right">
                            <span><?php echo _l('tickets'); ?></span>
                        </div>
                        <div class="col-md-6 mtop10">
                            <div class="onoffswitch">
                                <input type="checkbox" id="ticket_emails" data-perm-id="5" class="onoffswitch-checkbox" <?php if(isset($contact) && $contact->ticket_emails == '1'){echo 'checked';} ?>  value="ticket_emails" name="ticket_emails">
                                <label class="onoffswitch-label" for="ticket_emails"></label>
                            </div>
                        </div>
                        <div class="col-md-6 mtop10 border-right">
                            <span><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('only_project_tasks'); ?>"></i> <?php echo _l('task'); ?></span>
                        </div>
                        <div class="col-md-6 mtop10">
                            <div class="onoffswitch">
                                <input type="checkbox" id="task_emails" data-perm-id="6" class="onoffswitch-checkbox" <?php if(isset($contact) && $contact->task_emails == '1'){echo 'checked';} ?>  value="task_emails" name="task_emails">
                                <label class="onoffswitch-label" for="task_emails"></label>
                            </div>
                        </div>

                    </div>
                </div> -->
                 <!-- <div class="col-md-6 row">
                    <div class="row">
                        <div class="col-md-6 mtop10 border-right">
                            <span><?php echo _l('contract'); ?></span>
                        </div>
                        <div class="col-md-6 mtop10">
                            <div class="onoffswitch">
                                <input type="checkbox" id="contract_emails" data-perm-id="3" class="onoffswitch-checkbox" <?php if(isset($contact) && $contact->contract_emails == '1'){echo 'checked';} ?>  value="contract_emails" name="contract_emails">
                                <label class="onoffswitch-label" for="contract_emails"></label>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- </div> -->
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info <?php echo $btn; ?>" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" data-form="#contact-form"><?php echo _l('submit'); ?></button>
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

<div class="modal fade" id="projectid_add_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('add_new',_l('deal_for_customer')); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/ajax_project',array('id'=>'projectid_add_group_modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                    <?php $attrs = array('autofocus'=>true, 'required'=>true); ?>
                    <?php echo render_input( 'project', 'project','','text',$attrs); ?>
                    <div id="project_exists_info" class="hide"></div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="orgid" id="orgid" value="">
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<?php //init_tail(); ?>

<script>
    function open_modal(a){
		$('#'+a).modal({
            show: true,
            backdrop: 'static'
        });
	}
       appValidateForm($('#clientid_add_group_modal'), {
        company: 'required'
    });


    $('#clientid_add_group_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var group_id = $(invoker).data('id');
        $('#clientid_add_group_modal input[name="company"]').val('');
        // is from the edit button
        if (typeof(group_id) !== 'undefined') {
            $('#clientid_add_group_modal input[name="company"]').val($(invoker).parents('tr').find('td').eq(0).text());
        }
    });
      
  

   $('#clientid_add_group_modal').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var data = getFormData(form);
        if(data.company){
            $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),
            dataType: 'json',
            success: function(msg){
                $('#orgid').val(msg.id);
                $('select#clientid').append('<option value="'+msg.id+'" selected="selected">'+msg.company+'</option>');
                $('select#clientid').val(msg.id);
                $('#clientid_add_group_modal input[name="company"]').val('');
                alert_float('success', msg.message);
                setTimeout(function(){  
                    $('#clientid').selectpicker('refresh'); 
                    $('.clientiddiv div.filter-option-inner-inner').html(msg.company) 
                }, 500);
                $('#clientid_add_modal').modal('hide');
            }
            });
        }
    });

//Deal Creation Form

appValidateForm($('#projectid_add_group_modal'), {
        project: 'required'
    });


    $('#projectid_add_group_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var group_id = $(invoker).data('id');
        $('#projectid_add_group_modal input[name="project"]').val('');
        // is from the edit button
        if (typeof(group_id) !== 'undefined') {
            $('#projectid_add_group_modal input[name="project"]').val($(invoker).parents('tr').find('td').eq(0).text());
        }
    });
      
    $('#project').on('keyup', function() {
        var name = $('#project').val();
        var pid = $('#projectid').val();
        var $companyExistsDiv = $('#project_exists_info');
        var data = {name:name};
        if (pid) {
            data['pid'] = pid;
        }
        $.ajax({
            type: 'POST',
            url: admin_url + 'projects/checkduplicate',
            data: data,
            dataType: 'json',
            success: function(msg) {
                if(msg.message != 'no') {
                    $companyExistsDiv.removeClass('hide');
                    $companyExistsDiv.html('<div class="info-block mbot15">'+msg.message+'</div>');
                } else {
                    $companyExistsDiv.addClass('hide');
                }
            }
        });
    });
    $('#firstname').on('keyup', function() {
        var name = $('#firstname').val();
        var pid = $('#contactid').val();
        var $companyExistsDiv = $('#company_exists_info');
        var data = {name:name};
        if (pid) {
            data['pid'] = pid;
        }
        $.ajax({
            type: 'POST',
            url: admin_url + 'clients/checkduplicate_contact',
            data: data,
            dataType: 'json',
            success: function(msg) {
                if(msg.message != 'no') {
                    $companyExistsDiv.removeClass('hide');
                    $companyExistsDiv.html('<div class="info-block mbot15">'+msg.message+'</div>');
                } else {
                    $companyExistsDiv.addClass('hide');
                }
            }
        });
    });
  
    $('#company').on('keyup', function() {
        var company = $(this).val();
        var $companyExistsDiv = $('#companyname_exists_info');

        if(company == '') {
            $companyExistsDiv.addClass('hide');
            return;
        }

        $.post(admin_url+'clients/check_duplicate_customer_name', {company:company})
        .done(function(response) {
            if(response) {
                response = JSON.parse(response);
                if(response.exists == true) {
                    $companyExistsDiv.removeClass('hide');
                    $companyExistsDiv.html('<div class="info-block mbot15">'+response.message+'</div>');
                } else {
                    $companyExistsDiv.addClass('hide');
                }
            }
        });
    });

   $('#projectid_add_group_modal').submit(function(e) {
       var orgid = $('#orgid').val();
       if(orgid == '') {
         //orgid = $(elt.options[elt.selectedIndex]).closest('optgroup').prop('label');
         orgid = $('#clientid').val();
         $('#orgid').val(orgid);
       }
       //alert(orgid);
       if(orgid) {
            e.preventDefault();
            var form = $(this);
            var data = getFormData(form);
            if(data.project){
                $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                dataType: 'json',
                success: function(msg){
                    $('#projectid_add_group_modal input[name="project"]').val('');
                    alert_float('success', msg.message);
                    setTimeout(function(){  
                       // $('.clientiddiv div.filter-option-inner-inner').html(msg.company)
                       $('.deals').selectpicker('destroy');
                        $('.deals').html(msg.result).selectpicker('refresh');
                    }, 500);
                    $('#projectid_add_modal').modal('hide');
                }
                });
            }
       } else {
           alert('Please Select Organization.');
           return false;
       }
    });



    $('select#clientid').on('change', function() {
        var clientid = this.value;
        $.ajax({
            url: admin_url + 'clients/getDealbyOrgId',
            type: 'POST',
            data: {
                'clientid': clientid
            },
            dataType: 'json',
            success: function success(result) {
                console.log(result.result);
                
                $('.deals').selectpicker('destroy');
                $('.deals').html(result.result).selectpicker('refresh');
            }
        });
    });
	<?php if(empty($data['need_fields']) || !in_array("clientid", $data['need_fields']) || empty($data['mandatory_fields']) || !in_array("clientid", $data['mandatory_fields'])){?>
function checkdeal(){
	$.ajax({
            url: admin_url + 'clients/getDeals',
            type: 'POST',
            data: {
                
            },
            dataType: 'json',
            success: function success(result) {
                console.log(result.result);
                
                $('.deals').selectpicker('destroy');
                $('.deals').html(result.result).selectpicker('refresh');
            }
        });
}
checkdeal();
	<?php }?>

function manage_customer_groups(form){
}


    function getFormData($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}
</script>



<?php if(!isset($contact)){ ?>
    <script>
        $(function(){
            // Guess auto email notifications based on the default contact permissios
            var permInputs = $('input[name="permissions[]"]');
            $.each(permInputs,function(i,input){
                input = $(input);
                if(input.prop('checked') === true){
                    $('#contact_email_notifications [data-perm-id="'+input.val()+'"]').prop('checked',true);
                }
            });
           
        });
    </script>
<?php } ?>
<script>
        $(function(){
           
            // On document read check and init for client ajax-search
            init_ajax_search('customer', '#clientid.ajax-search');

			$('input.daterangepicker').daterangepicker({
		 startDate: '2022-03-11',
    endDate: '2022-03-11',
      locale: {
      format: 'YYYY-MM-DD'
    },

    });
    $('input.datetimerangepicker').daterangepicker({
      timePicker: true,
      locale: {
      format: 'YYYY-MM-DD hh:mm A',
	  scrollbar: true
    }
    });
});
$('input.timepicker').timepicker({
      timeFormat: 'h:mm p',
    interval: 10,
    startTime: '10:00',
    dynamic: false,
    dropdown: true,
    scrollbar: true
    });
    </script>

<script>

    // -----Country Code Selection
    $("#phonenumber").intlTelInput({
        initialCountry: "<?php echo ( isset($contact) ? $contact->phone_country_code : 'IN'); ?>",
        separateDialCode: true,
        // utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.4/js/utils.js"
    });
    $("#phonenumber_iti_wrapper .iti__flag-container ul li").click(function(){

        var country_code =$(this).attr('data-country-code').toUpperCase();
        $("#phone_country_code").val(country_code);
    });

</script>

	<?php /*<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
*/ ?>
<!-- Latest compiled and minified JavaScript -->
<?php /*<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>*/ ?>
