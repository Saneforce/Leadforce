<style>
    .th_head_color {
        color: darkgrey !important;
    }

    .unread_col_col {
        background: gainsboro;
    }

    .headerSortDown:after,
    .headerSortUp:after {
        content: ' ';
        position: relative;
        left: 2px;
        border: 5px solid transparent;
    }

    .headerSortDown:after {
        top: 10px;
        border-top-color: silver;
    }

    .headerSortUp:after {
        bottom: 10px;
        border-bottom-color: silver;
    }

    .headerSortDown,
    .headerSortUp {
        padding-right: 10px;
    }

    .ui-autocomplete {
        position: absolute;
        top: 0;
        left: 0;
        cursor: default;
        z-index: 1050 !important;
    }

    .error {
        color: red;
    }

    .error1 {
        color: red;
        display: none;
    }

    .sticky {
        position: fixed;
        top: 0;
        width: 100%;
        padding: 10px 0px;
        height: 49px;
        margin-left: -10px !important;
    }

    .header {
        z-index: 999;
        background: #fff;
        margin-left: 10px;
    }


    fieldset {
        display: none
    }

    fieldset.show {
        display: block
    }

    a:hover {
        text-decoration: none;
        color: #1565C0
    }

    .box {
        margin-bottom: 10px;
        border-radius: 5px;
        padding: 10px
    }

    .line {
        background-color: #CFD8DC;
        height: 1px;
        width: 100%
    }

    @media screen and (max-width: 768px) {
        .tabs h6 {
            font-size: 12px
        }
    }

    #emailViewerMeta p {
        font-size: 13px !important;
    }
</style>



<!-- BEGIN COMPOSE MESSAGE -->
<div class="modal fade" id="compose-modal" tabindex="-1" role="dialog" aria-hidden="true" style="margin-bottom:25px;">
    <div class="modal-wrapper">
        <div class="modal-dialog" style="width:74.5% ">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title"><i class="fa fa-envelope"></i> Compose New Message</h4>
                </div>
                <div class="col-md-12 bg-white" style="border-radius:0px 5px;">
                    <div class="col-md-3">
                        <ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked mtop15">
                            <li class="tabs active" id="tab01">
                                <a class="text-muted">Compose Email</a>
                            <li class="tabs " id="tab02" onclick="gettemplate_list()">
                                <a class="text-muted">Templates</a>
                            </li>
                            <li class="tabs " id="tab03" onclick="reset_form()">
                                <a class="font-weight-bold text-muted">Create Template</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-9">
                        <fieldset id="tab011" class="show">
                            <?php if(isset($email_composer_rel_type) && !$email_composer_contact_email): ?>
                                <div class="alert alert-danger mtop15 text-center">
                                    <?php if(!$email_composer_conatct_id): ?>
                                        <h5>Could not found contact person</h5>
                                    <?php else: ?>
                                        <h5>Could not found email address</h5>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                            <form action="<?php echo admin_url('company_mail/createtaskcompanymail'); ?>" method="post" id="compose_email" enctype='multipart/form-data' onsubmit="over_lay('compose')">
                                <input type="hidden" id="cur_draft_id" name="cur_draft_id">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <?php if(isset($email_composer_rel_type)): ?>
                                            <input name="toemail" type="hidden" value="<?php echo $email_composer_contact_email;?>" >
                                            <p>TO : <?php echo $email_composer_contact_email;?></p>
                                            <input name="redirect" type="hidden" value="<?php echo $email_composer_rel_type;?>" >
										    <input name="contactid" type="hidden"value="<?php echo $email_composer_conatct_id;?>" >
                                        <?php else: ?>
                                            <input name="toemail" type="email" class="form-control" placeholder="To" id="toemail" onblur="deal_values()" onkeyup="check_email(this.value,'toemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$">
                                        <?php endif; ?>
                                        <input type='hidden' id='selectuser_ids' />
                                    </div>
                                    <div class="form-group">
                                        <input name="ccemail" type="email" class="form-control" placeholder="Cc" id="toccemail" onkeyup="check_email(this.value,'toccemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$">
                                    </div>
                                    <div class="form-group">
                                        <input name="bccemail" type="email" class="form-control" placeholder="Bcc" id="tobccemail" onkeyup="check_email(this.value,'tobccemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$">
                                        <input type="hidden" id="deal_map" value="<?php echo get_option('deal_map'); ?>">
                                    </div>
                                    <div class="form-group pipeselect">
                                        <label><b>Choose Template</b></label>
                                        <select class="selectpicker" data-none-selected-text="<?php echo _l('Select Template'); ?>" name="select_template" id="ch_default_temp" data-width="100%" data-live-search="true" onchange="submit_default()">
                                            <option value=''>None</option>
                                            <?php if (!empty($templates)) {
                                                foreach ($templates as $template1) {
                                            ?>
                                                    <option value="<?php echo $template1['id']; ?>"><?php echo $template1['template_name']; ?></option>
                                            <?php
                                                }
                                            } ?>
                                        </select>
                                    </div>


                                    <?php //if(get_option('deal_map') == 'if more than one open deal – allow to map manually'){
                                    ?>
                                    <?php if(isset($email_composer_rel_type)): ?>
                                    <input type="hidden" name="deal_id" value="<?php echo $email_composer_rel_type.'_'.$email_composer_rel_id ?>">
                                    <?php else: ?>
                                    <div class="form-group pipeselect" style="display:none" id="pipeselect">
                                        <label><b>Deal / Lead</b></label>
                                        <select class="selectpicker" data-none-selected-text="<?php echo _l('Select Ay Deal'); ?>" name="deal_id" id="pipeline_id" data-width="100%" <?php if (get_option('deal_map') == 'if more than one open deal – allow to map manually') { ?>data-live-search="true" <?php } ?>>
                                        </select>
                                    </div>
                                    <?php endif; ?>
                                    <?php //}
                                    ?>
                                    <div class="form-group pipeselect" id="activity_type" style="display:none">
                                        <label><b>Activity Type</b></label>
                                        <input type="hidden" name="activity_type" value="close">
                                        <select class="selectpicker" data-none-selected-text="<?php echo _l('Activity Type'); ?>" name="activity_type" data-width="100%" required>
                                            <option value="open">Open</option>
                                            <option value="close">Close</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input name="name" type="text" class="form-control" placeholder="Subject" id="c_subject" required>
                                    </div>
                                    <div class="form-group" app-field-wrapper="description"><textarea id="description" name="description" class="form-control tinymce1" rows="6"><?php //echo $default_val;
                                                                                                                                                                                    ?></textarea></div>
                                    <input type="hidden" name="priority" value="1">
                                    <input type="hidden" id="mfilecnt" value="1">
                                    <input type="hidden" id="mtotcnt" value="1">
                                    <input type="hidden" id="mallcnt" value="0">
                                    <input type="hidden" id="m_file" name="m_file">
                                    <input type="hidden" name="repeat_every_custom" value="1">
                                    <input type="hidden" name="repeat_type_custom" value="day">
                                    <input type="hidden" name="rel_type" value="">
                                    <input type="hidden" name="tasktype" value="2">
                                    <input type="hidden" name="billable" value="on">
                                    <input type="hidden" name="task_mark_complete_id" value="">
                                    <input type="hidden" name="tags" value="">
                                    <button type="button" class="btn btn-info" style="display:block;" onclick="mget_file('getFile','m')">Add Attachement </button>
                                    <input type='file' id="getFile" style="display:none" multiple name="attachment[]" onchange="get_up_val('getFile','m')">

                                    <div class="ch_files_m list_files">
                                    </div>
                                    <div id="m_files"></div>
                                </div>
                                <div class="modal-footer">

                                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
                                    <div id="overlay_compose" class="overlay_new" style="display:none;position:absolute">
                                        <div class="spinner"></div>
                                    </div>
                                    <button type="submit" class="btn btn-info pull-right" style="margin-left:10px"><i class="fa fa-envelope"></i> Send Message</button>

                                </div>
                            </form>
                            <?php endif; ?>
                        </fieldset>
                        <fieldset id="tab021">
                            <div id="template_header">
                            </div>
                            <form method='post' class='form-inline' id='default_template' action='<?php echo admin_url('company_mail/change_default'); ?>'>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <div id="template_list1">
                                </div>
                            </form>
                        </fieldset>
                        <fieldset id="tab031">
                            <form action="<?php echo admin_url('company_mail/create_template'); ?>" method="post" id="template_form">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <input name="template_name" type="text" class="form-control" placeholder="Template Name" id="template_name">
                                        <span class="error1" id="name_error">Please Enter Template Name</span>
                                    </div>
                                    <div class="form-group" app-field-wrapper="description"><textarea id="template_description" name="template_description" class="form-control tinymce" rows="6"></textarea>
                                        <span class="error1" id="desc_error">Please Enter Text</span>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-info pull-right">Submit</button>
                                </div>
                            </form>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END COMPOSE MESSAGE -->

<!--Edit Template -->
<div class="modal fade" id="Edit-template" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-wrapper">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Edit Template</h4>
                </div>
                <form action="<?php echo admin_url('company_mail/update_template'); ?>" method="post" id="edit_template_form">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="template_id" id="template_id1">
                    <div class="modal-body">
                        <div class="form-group">
                            <input name="template_edit_name" type="text" class="form-control" placeholder="Template Name" id="template_edit_name">
                            <span class="error1" id="name_edit_error">Please Enter Template Name</span>
                        </div>
                        <div class="form-group" app-field-wrapper="description"><textarea id="template_edit_description" name="template_edit_description" class="form-control tinymce" rows="6"></textarea>
                            <span class="error1" id="desc_edit_error">Please Enter Text</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info pull-right">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END CREATE TEMPLATE -->

<!-- BEGIN FORWARD MESSAGE -->
<div class="modal fade" id="forward-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-wrapper">
        <div class="modal-dialog">
            <div id="overlay_forward" class="overlay_new" style="display:none">
                <div class="spinner"></div>
            </div>
            <div id="overlay_new" class="overlay_new" style="display:none">
                <div class="spinner"></div>
            </div>
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title"><i class="fa fa-forward"></i> Forward Message</h4>
                </div>
                <form action="<?php echo admin_url('company_mail/forward'); ?>" method="post" enctype='multipart/form-data' onsubmit="over_lay('forward')">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <input name="toemail" type="email" class="form-control" placeholder="To" id="forward_toemail" onkeyup="check_email(this.value,'forward_toemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$" required>
                        </div>
                        <div class="form-group">
                            <input name="ccemail" type="email" class="form-control" placeholder="Cc" id="forward_ccemail" onkeyup="check_email(this.value,'forward_ccemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$">
                        </div>
                        <div class="form-group">
                            <input name="bccemail" type="email" class="form-control" placeholder="Bcc" id="forward_bccemail" onkeyup="check_email(this.value,'forward_bccemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$">
                        </div>
                        <div class="form-group">
                            <input name="name" type="text" id="forward_subject" class="form-control" placeholder="Subject" required readonly>
                        </div>
                        <div class="form-group" app-field-wrapper="description"><textarea id="forward_description" name="description" class="form-control tinymce" rows="6"></textarea></div>
                        <button type="button" class="btn btn-primary" style="display:block;" onclick="mget_file('f_getFile','f')">Add Attachement </button>
                        <input type='file' id="f_getFile" style="display:none" multiple name="attachment[]" onchange="get_up_val('f_getFile','f')">
                        <input type="hidden" name="priority" value="1">
                        <input type="hidden" name="repeat_every_custom" value="1">
                        <input type="hidden" name="repeat_type_custom" value="day">
                        <input type="hidden" name="rel_type" value="project">
                        <input type="hidden" name="tasktype" value="2">
                        <input type="hidden" name="billable" value="on">
                        <input type="hidden" name="task_mark_complete_id" value="">
                        <input type="hidden" name="tags" value="">
                        <input type="hidden" id="ffilecnt" value="1">
                        <input type="hidden" id="ftotcnt" value="1">
                        <input type="hidden" id="fallcnt" value="0">
                        <input type="hidden" id="f_file" name="m_file">
                        <div class="ch_files_f">
                        </div>
                        <div id="f_files"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
                        <button type="submit" class="btn btn-info pull-right"><i class="fa fa-envelope"></i> Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END forward MESSAGE -->

<!-- BEGIN Reply MESSAGE -->
<div class="modal fade" id="reply-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-wrapper">
        <div class="modal-dialog">
            <div id="overlay_new1" class="overlay_new" style="display:none">
                <div class="spinner"></div>
            </div>
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title"><i class="fa fa-reply"></i> Reply Message</h4>
                </div>
                <form action="<?php echo admin_url('company_mail/reply'); ?>" method="post" enctype='multipart/form-data' onsubmit="over_lay('new1')">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <input name="toemail" type="text" class="form-control" placeholder="To" id="reply_toemail" readonly>
                            <input name="ch_uid" type="hidden" id="ch_uid">
                        </div>
                        <div class="form-group">
                            <input name="ccemail" type="email" class="form-control" placeholder="Cc" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$" id="reply_ccemail" onkeyup="check_email(this.value,'reply_ccemail')">
                        </div>
                        <div class="form-group">
                            <input name="bccemail" type="email" class="form-control" placeholder="Bcc" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$" id="reply_bccemail" onkeyup="check_email(this.value,'reply_bccemail')">
                        </div>
                        <div class="form-group">
                            <input name="name" type="text" id="reply_subject" class="form-control" placeholder="Subject" readonly>
                        </div>
                        <div class="form-group" app-field-wrapper="description"><textarea id="reply_description" name="description" class="form-control tinymce" rows="6"></textarea></div>
                        <button type="button" class="btn btn-primary" style="display:block;" onclick="mget_file('r_getFile','r')">Add Attachement </button>
                        <input type='file' id="r_getFile" style="display:none" multiple name="attachment[]" onchange="get_up_val('r_getFile','r')">
                        <input type="hidden" name="priority" value="1">
                        <input type="hidden" name="repeat_every_custom" value="1">
                        <input type="hidden" name="repeat_type_custom" value="day">
                        <input type="hidden" name="rel_type" value="">
                        <input type="hidden" name="rel_id" value="">
                        <input type="hidden" name="parent_id" value="">
                        <input type="hidden" name="tasktype" value="2">
                        <input type="hidden" name="billable" value="on">
                        <input type="hidden" name="task_mark_complete_id" value="">
                        <input type="hidden" name="tags" value="">
                        <input type="hidden" id="rfilecnt" value="1">
                        <input type="hidden" id="rtotcnt" value="1">
                        <input type="hidden" id="rallcnt" value="0">
                        <input type="hidden" id="r_file" name="m_file">
                        <div class="ch_files_r">
                        </div>
                        <div id="r_files"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
                        <button type="submit" class="btn btn-info pull-right"><i class="fa fa-envelope"></i> Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END reply MESSAGE -->