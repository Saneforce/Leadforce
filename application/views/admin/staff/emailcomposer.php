<!-- BEGIN COMPOSE MESSAGE -->
<div class="modal fade" id="compose-modal" tabindex="-1" role="dialog" aria-hidden="true" style="margin-bottom:25px;">
    <div class="modal-wrapper">
        <div class="modal-dialog" style="width:74.5% ">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title"><i class="fa fa-envelope"></i> Compose New Message</h4>
                </div>
                <div class="col-md-12 bg-white" style="border-radius:6px;">
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
                            <form action="<?php echo admin_url('company_mail/createtaskcompanymail'); ?>" method="post" id="compose_email" enctype='multipart/form-data' onsubmit="over_lay('compose')">
                                <input type="hidden" id="cur_draft_id" name="cur_draft_id">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <input name="toemail" type="email" class="form-control" placeholder="To" id="toemail" onblur="deal_values()" onkeyup="check_email(this.value,'toemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$">
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
                                    <div class="form-group pipeselect" style="display:none" id="pipeselect">
                                        <label><b>Deal / Lead</b></label>
                                        <select class="selectpicker" data-none-selected-text="<?php echo _l('Select Ay Deal'); ?>" name="deal_id" id="pipeline_id" data-width="100%" <?php if (get_option('deal_map') == 'if more than one open deal – allow to map manually') { ?>data-live-search="true" <?php } ?>>
                                        </select>
                                    </div>
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