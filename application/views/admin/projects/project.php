<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<style>
    .css-table {
        display: table;
        width: 100%;
    }

    .css-table-header {
        display: table-header-group;
    }

    .css-table-body {
        display: table-row-group;
    }

    .css-table-row {
        display: table-row;
    }

    .css-table-header div,
    .css-table-row div {
        display: table-cell;
        padding: 0 6px;
    }

    .css-table-header div {
        text-align: left;
        border: 1px solid rgb(255, 255, 255);
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php echo form_open($this->uri->uri_string(),array('id'=>'project_form')); ?>
            <input type="hidden" id="projectid" value="<?php echo $project->id; ?>">
            <input type="hidden" name="lead_id" id="lead_id" value="<?php echo $lead_id; ?>">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <?php
                        $disable_type_edit = '';
                        if(isset($project)){
                            if($project->billing_type != 1){
                                if(total_rows(db_prefix().'tasks',array('rel_id'=>$project->id,'rel_type'=>'project','billable'=>1,'billed'=>1)) > 0){
                                    $disable_type_edit = 'disabled';
                                }
                            }
                        }
                        ?>
                        <?php $value = (isset($project) ? $project->name : ''); ?>
                        <?php echo render_input('name','project_name',$value,'',array('maxlength'=>191,'placeholder'=>'Enter Deal Name')); ?>
                        <div id="company_exists_info" class="hide"></div>

            
                        <?php 
						if(!empty($cur_id))
							$teamleaderselected = ((isset($project) && !empty($project->teamleader)) ? $project->teamleader : '');
						else
							$teamleaderselected = $cur_staff_id;
                            if(isset($project)) {
                                if(in_array(get_staff_user_id(),$ownerHierarchy) || $project->teamleader == get_staff_user_id() || is_admin(get_staff_user_id()))
                                    echo render_select('teamleader', $teamleaders, array('staffid', array('firstname', 'lastname')), 'teamleader', $teamleaderselected, $assigned_attrs);
                                else
                                    echo render_select('teamleader', $teamleaders, array('staffid', array('firstname', 'lastname')), 'teamleader', $teamleaderselected, array('disabled'=>true));
                            } else {
                                echo render_select('teamleader', $teamleaders, array('staffid', array('firstname', 'lastname')), 'teamleader', $teamleaderselected, $assigned_attrs);
                            }
                        ?>

						<?php if(!empty($need_fields) && in_array("clientid", $need_fields)){ ?>
							<div
								class="form-group select-placeholder clientiddiv form-group-select-input-groups_in[] input-group-select">

								<label for="clientid" class="control-label"><?php echo _l('project_customer'); ?>
								<?php if(!empty($important_fields) && in_array("clientid", $important_fields)){?>
									<span style="color: #d2be19;margin-left: 5px;" title="<?php if(!empty($important_messages->clientid)){echo $important_messages->clientid;} ?>" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>
								<?php }?>
								</label>
								<div class="input-group input-group-select select-groups_in[]">
									<select id="clientid" name="clientid" data-live-search="true" data-width="100%"
										class="ajax-search"
										data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
										<?php $selected = (isset($project) ? $project->clientid : '');
								 if($selected == ''){
									 $selected = (isset($customer_id) ? $customer_id: '');
								 }
                                 
                                 if($lead_id){
                                    $selected =$_POST['clientid'];
                                 }
								 if($selected != ''){
									$rel_data = get_relation_data('customer',$selected);
									$rel_val = get_relation_values($rel_data,'customer');
									echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
								} ?>

									</select>
									<div class="input-group-addon" style="opacity: 1;"><a href="#" data-toggle="modal"
											data-target="#clientid_add_modal"><i class="fa fa-plus"></i></a></div>
								</div>

							</div>
						<?php }if(!empty($need_fields) && in_array("project_contacts[]", $need_fields)){?>
                        <div class="form-group select-placeholder contactsdiv">
                            <label for="project_contacts_selectpicker"
                                class="control-label"><?php if(!empty($need_fields) && in_array("project_contacts[]", $need_fields) && !empty($mandatory_fields) && in_array("project_contacts[]", $mandatory_fields)){ ?> <small class="req text-danger">* </small><?php } ?><?php echo _l('project_contacts'); ?>
								<?php if(!empty($important_fields) && in_array("project_contacts[]", $important_fields)){?>
									<span style="color: #d2be19;margin-left: 5px;" title="<?php if(!empty($important_messages->project_contacts)){echo $important_messages->project_contacts;} ?>" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>
								<?php }?>
								</label>
                            <div class="input-group input-group-select ">

                                <?php 
						   $selected = array();
                        if($lead_id){
                            $selected =$_POST['project_contacts'];
                        }
            foreach($contacts as $contact){
              array_push($selected,$contact['contacts_id']);
           }
           echo render_select('project_contacts[]',$client_contacts,array('id',array('firstname','lastname')),false,$selected,array('multiple'=>true,'data-actions-box'=>true,'aria-describedby'=>'project_contacts-error'),array(),'','',false);
           
						  ?>

                                <?php /*
                            <select id="project_contacts_selectpicker" name="project_contacts[]"  data-width="100%" class="selectpicker _select_input_group" multiple='true'>
                          <?php
                          foreach($client_contacts as $cckey => $ccval){ 
                            $selected = '';
                            foreach($contacts as $scckey => $sccval){
                                if( $sccval['contacts_id'] == $ccval['id']){
                                    $selected = 'selected';
                                }
                            }
                            echo '<option value="'.$ccval['id'].'" '.$selected.' >'.$ccval['firstname'].'</option>';

                          } ?>
                                </select> */?>
                                <div class="input-group-addon" style="opacity: 1;"><a href="#" data-toggle="modal"
                                        data-target="#project_contacts_modal"><i class="fa fa-plus"></i></a></div>
                            </div>
                        </div>
                        <?php }if(!empty($need_fields) && in_array("primary_contact", $need_fields)){?>

<div class='row primarydiv'>
    <div class="col-md-12">
        <div class="form-group select-placeholder">
            <label for="status"><?php if(!empty($need_fields) && in_array("primary_contact", $need_fields) && !empty($mandatory_fields) && in_array("primary_contact", $mandatory_fields)){ ?> <small class="req text-danger">* </small><?php } ?><?php echo _l('project_primary_contacts'); ?>
			<?php if(!empty($important_fields) && in_array("primary_contact", $important_fields)){?>
				<span style="color: #d2be19;margin-left: 5px;" title="<?php if(!empty($important_messages->primary_contact)){echo $important_messages->primary_contact;} ?>" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>
			<?php }?>
			</label>
            <div class="clearfix"></div>
            <select name="primary_contact" id="primary_contact" class="selectpicker" data-width="100%"
                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
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
						<?php }?>

                        <div class="form-group" style=" display:none;">
                            <div class="checkbox checkbox-success">
                                <input type="checkbox"
                                    <?php if((isset($project) && $project->progress_from_tasks == 1) || !isset($project)){echo 'checked';} ?>
                                    name="progress_from_tasks" id="progress_from_tasks">
                                <label
                                    for="progress_from_tasks"><?php echo _l('calculate_progress_through_tasks'); ?></label>
                            </div>
                        </div>
						<?php if(!empty($need_fields) && in_array("pipeline_id", $need_fields)){?>
                        <div class="row">
                            <div class="col-md-12 pipelineid ">
                                <?php
                                    $assigned_attrs = array();
                                    $pipelineleadselected = (isset($project) ? $project->pipeline_id : '');
                                    if(!is_admin(get_staff_user_id())) {
                                        $tm = array("id" => "","id" => "", "name" => "Nothing Selected");
                                        array_unshift($pipelines, $tm);
                                    }
                                    echo render_select('pipeline_id', $pipelines, array('id', 'name'), 'pipeline', $pipelineleadselected, $assigned_attrs);
                                ?>
                            </div>

                        </div>
						<?php }if(!empty($need_fields) && in_array("status", $need_fields)){?>
                        <div class='row'>
                            <div class="col-md-12 form_status">
                                <div class="form-group select-placeholder">
                                    <label for="status"><?php if(!empty($need_fields) && in_array("status", $need_fields) && !empty($mandatory_fields) && in_array("status", $mandatory_fields)){ ?> <small class="req text-danger">* </small><?php } ?><?php echo _l('project_status'); ?>
									<?php if(!empty($important_fields) && in_array("status", $important_fields)){?>
										<span style="color: #d2be19;margin-left: 5px;" title="<?php if(!empty($important_messages->status)){echo $important_messages->status;} ?>" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>
									<?php }?>
									</label>
                                    <div class="clearfix"></div>
                                    <select name="status" id="status" class="selectpicker" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option></option>
                                        <?php foreach($statuses as $status){ ?>
                                        <option value="<?php echo $status['id']; ?>"
                                            <?php if(!isset($project) && $status['id'] == 2 || (isset($project) && $project->status == $status['id'])){echo 'selected';} ?>>
                                            <?php echo $status['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                        $exp = explode('admin/projects/project',$this->uri->uri_string());
                        //pr($exp); exit;
                        if($exp[1]) {
                         
                            if(isset($project) && $project->progress_from_tasks == 1){
                                $value = $this->projects_model->calc_progress_by_tasks($project->id);
                            } else if(isset($project) && $project->progress_from_tasks == 0){
                                $value = $project->progress;
                            } else {
                                $value = 0;
                            }
                    ?>
                        <!-- <label for=""><?php echo _l('project_progress'); ?> <span
                                class="label_progress"><?php echo $value; ?>%</span></label>
                        <?php echo form_hidden('progress',$value); ?>
                        <div class="project_progress_slider project_progress_slider_horizontal mbot15"></div> -->
                        <?php } ?>
						<?php if(!empty($need_fields) && (in_array("project_members[]", $need_fields)  || in_array("teamleader", $need_fields) )){?>
                        <div class="row">
                            <!--  
                        <div class="col-md-6">
                            <div class="form-group select-placeholder">
                                <label for="billing_type"><?php echo _l('project_billing_type'); ?></label>
                                <div class="clearfix"></div>
                                <select name="billing_type" class="selectpicker" id="billing_type" data-width="100%" <?php echo $disable_type_edit ; ?> data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""></option>
                                    <option value="1" <?php if(isset($project) && $project->billing_type == 1 || !isset($project) && $auto_select_billing_type && $auto_select_billing_type->billing_type == 1){echo 'selected'; } ?>><?php echo _l('project_billing_type_fixed_cost'); ?></option>
                                    <option value="2" <?php if(isset($project) && $project->billing_type == 2 || !isset($project) && $auto_select_billing_type && $auto_select_billing_type->billing_type == 2){echo 'selected'; } ?>><?php echo _l('project_billing_type_project_hours'); ?></option>
                                    <option value="3" data-subtext="<?php echo _l('project_billing_type_project_task_hours_hourly_rate'); ?>" <?php if(isset($project) && $project->billing_type == 3 || !isset($project) && $auto_select_billing_type && $auto_select_billing_type->billing_type == 3){echo 'selected'; } ?>><?php echo _l('project_billing_type_project_task_hours'); ?></option>
                                </select>
                                <?php if($disable_type_edit != ''){
                                    echo '<p class="text-danger">'._l('cant_change_billing_type_billed_tasks_found').'</p>';
                                }
                                ?>
                            </div>
                        </div>
                          -->
						  <?php if(!empty($need_fields) && in_array("project_members[]", $need_fields)){?>
                            <div <?php if(!empty($need_fields) && in_array("teamleader", $need_fields)){?> class="col-md-6 form_assigned"<?php }else{?> class="col-md-6 form_assigned" <?php }?>>
                                <?php
                         $selected = array();
                         if(isset($project_members)){
                            foreach($project_members as $member){
                                array_push($selected,$member['staff_id']);
                            }
                         } 
                        
                        if(isset($project)) {
                            if(in_array(get_staff_user_id(),$ownerHierarchy) || $project->teamleader == get_staff_user_id() || is_admin(get_staff_user_id()))
                                echo render_select('project_members[]',$staff,array('staffid',array('firstname','lastname')),'project_members',$selected,array('multiple'=>true,'class'=>'formassigned','data-actions-box'=>true),array('id'=>'project_members','app-field-wrapper'=>'project_members11'),'project_members1','',false);
                            else
                                echo render_select('project_members[]',$staff,array('staffid',array('firstname','lastname')),'project_members',$selected,array('multiple'=>true,'class'=>'formassigned','data-actions-box'=>true,'disabled'=>true),array('id'=>'project_members','app-field-wrapper'=>'project_members11'),'project_members1','',false);
                        } else {
                            echo render_select('project_members[]',$staff,array('staffid',array('firstname','lastname')),'project_members',$selected,array('multiple'=>true,'class'=>'formassigned','data-actions-box'=>true),array('id'=>'project_members','app-field-wrapper'=>'project_members11'),'project_members1','',false);
                        }
                        
                        ?>
                            </div>

						  <?php }?>
                        </div>
                        <?php } if(isset($project) && project_has_recurring_tasks($project->id)) { ?>
                        <div class="alert alert-warning recurring-tasks-notice hide"></div>
                        <?php } ?>
                        <?php if(total_rows(db_prefix().'emailtemplates',array('slug'=>'project-finished-to-customer','active'=>0)) == 0){ ?>
                        <div class="form-group project_marked_as_finished hide">
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="project_marked_as_finished_email_to_contacts"
                                    id="project_marked_as_finished_email_to_contacts">
                                <label
                                    for="project_marked_as_finished_email_to_contacts"><?php echo _l('project_marked_as_finished_to_contacts'); ?></label>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if(isset($project)){ ?>
                        <!-- <div class="form-group mark_all_tasks_as_completed hide">
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="mark_all_tasks_as_completed"
                                    id="mark_all_tasks_as_completed">
                                <label
                                    for="mark_all_tasks_as_completed"><?php echo _l('project_mark_all_tasks_as_completed'); ?></label>
                            </div>
                        </div>
                        <div class="notify_project_members_status_change hide">
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="notify_project_members_status_change"
                                    id="notify_project_members_status_change">
                                <label
                                    for="notify_project_members_status_change"><?php echo _l('notify_project_members_status_change'); ?></label>
                            </div>
                            <hr />
                        </div> -->
                        <input type="hidden" name="notify_project_members_status_change"
                                    id="notify_project_members_status_change" value="1">
                        <?php } ?>
                        <!--  
                         -->
                         <?php if(!isset($project->id)  && (!$lead_id || !$lead_products)) { ?>
                            <div style="display:inline-block; float:right;">
                            <select class="currency1" id="currency" name="currency">
                                <?php
                        foreach($allcurrency as $ac) {
                            $selected = '';
                            if($basecurrency == $ac['name']) {
                                $selected = 'selected';
                            }
                    ?>
                      <option value="<?php echo $ac['name']; ?>" <?php echo $selected;?> ><?php echo $ac['name']; ?></option>
                    <?php  } ?>
                            </select>&nbsp;&nbsp;
<a href="javascript:void(0)" class="addproducts">Add Items</a>
<a href="javascript:void(0)" class="removeproducts">Remove Items</a>
</div>
                         <?php } else { ?>
                                <input type="hidden" name="currency" value="<?php echo $project->project_currency; ?>">
                         <?php } ?>

        <div class="form-group showproducts">
            <label for="unit_price" class="control-label"><h4>Items</h4></label>
            <div class="css-table">
                
                <a href="javascript:void(0);" class="addproduts_btn1 row" title="Add field" style="position:relative; top:10px; left:15px; clear:both; float:left; height:40px;"><i class="fa fa-plus"></i> Add More</a>  
                <input type="hidden" id="product_index" value="0">
                <div class="field_product_wrapper row css-table-body">
                        
                    <div style="height:40px;clear:both;" class="productdiv css-table-row" id="0">
                    <div class="">
                        <select name="product[]" class="form-control" onchange="getprice1(this,0)">
                            <option value="">--Select Item--</option>
                        <?php
                            foreach($products as $prod) {
                        ?>
                        <option value="<?php echo $prod['id']; ?>"><?php echo $prod['name']; ?></option>
                        <?php  } ?>
                        </select>
                    </div>
                    <div class="">
                        <input type="text" name="price[]" value="" placeholder="Price" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,0)" class="form-control" /> 
                    </div>
                    <div class="">
                    <input type="number" name="qty[]" value="" min="1" placeholder="Qty" value="" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="qty_total(this,0)" class="form-control" /> 
                    </div>
                    <div class="">
                    <input type="number" name="total[]" value="" placeholder="Total" readonly class="form-control" /> 
                    </div>
                    <div class="">
                    </div>
            </div>
       </div>
       
</div>
<div class="col-md-8 text-right" style="padding-top:30px"><b>Total</b></div><div class="col-md-3 text-right" style="padding-top:30px"><b><span id="grandtotal">0</span></b></div>
</div>
                        <?php
                    $input_field_hide_class_total_cost = '';
                    /*if(!isset($project)){
                        if($auto_select_billing_type && $auto_select_billing_type->billing_type != 1 || !$auto_select_billing_type){
                            $input_field_hide_class_total_cost = 'hide';
                        }
                    } else if(isset($project) && $project->billing_type != 1){
                        $input_field_hide_class_total_cost = 'hide';
                    }*/
					if(!empty($need_fields) && in_array("project_cost", $need_fields) && (!$lead_id || !$lead_products)){
                    ?>
                        <div id="project_cost" style="clear:both;" class="<?php echo $input_field_hide_class_total_cost; ?>">
                            <?php $value = (isset($project) ? $project->project_cost : '');
                            if($lead_id){
                                $value = $lead_details->lead_cost;
                            }
                            if($productscnt > 0) {
                                $readonly = array('readonly' => 'readonly','min'=>0);
                            } else {
                                $readonly = array('min'=>0);
                            }
                            if($project->project_currency) {
                                $cur = $project->project_currency;
                            } else {
                                $cur = $basecurrency;
                            }
                            ?>
                            <?php echo render_input('project_cost','project_total_cost',$value,'number',$readonly,array('currency'=>$cur,'min'=>0)); ?>
                        </div>
                       
                    <?php }
                    $input_field_hide_class_rate_per_hour = '';
                    if(!isset($project)){
                        if($auto_select_billing_type && $auto_select_billing_type->billing_type != 2 || !$auto_select_billing_type){
                            $input_field_hide_class_rate_per_hour = 'hide';
                        }
                    } else if(isset($project) && $project->billing_type != 2){
                        $input_field_hide_class_rate_per_hour = 'hide';
                    }
                    ?> <!-- 
                    <div id="project_rate_per_hour" class="<?php echo $input_field_hide_class_rate_per_hour; ?>">
                        <?php $value = (isset($project) ? $project->project_rate_per_hour : ''); ?>
                        <?php
                        $input_disable = array();
                        if($disable_type_edit != ''){
                            $input_disable['disabled'] = true;
                        }
                        ?>
                        <?php echo render_input('project_rate_per_hour','project_rate_per_hour',$value,'number',$input_disable); ?>
                    </div>
                      -->
                        <!--   -->
                        <div class="row">
                            <!-- <div class="col-md-6">
                            <?php echo render_input('estimated_hours','estimated_hours',isset($project) ? $project->estimated_hours : '','number'); ?>
                        </div> -->


                        </div>
					
                        <div class="row">
							<?php if(!empty($need_fields) && in_array("project_start_date", $need_fields)){?>
                            <div <?php if(!empty($need_fields) && in_array("project_deadline", $need_fields)){?>class="col-md-6"<?php }else{?> class="col-md-12"<?php }?>>
                                <?php $start_date = (isset($project) ? _d($project->start_date) : _d(date('Y-m-d'))); ?>
                                <?php $deadline = (isset($project) ? _d($project->deadline) : ''); ?>
								<?php if(!empty($mandatory_fields) && in_array("project_start_date", $mandatory_fields)){
									$st_array = array('data-date-end-date'=>$deadline,'readonly'=>'readonly','required'=>true);
								}
								else{
									$st_array = array('data-date-end-date'=>$deadline,'readonly'=>'readonly');
								}
								echo render_date_input('start_date','project_start_date',$start_date,$st_array); ?>
                            </div>
							<?php }if(!empty($need_fields) && in_array("project_deadline", $need_fields)){?>
                            <div <?php if(!empty($need_fields) && in_array("project_start_date", $need_fields)){?>class="col-md-6" <?php }else{?> class="col-md-12"<?php }?>>
								<?php 
								if(!empty($mandatory_fields) && in_array("project_deadline", $mandatory_fields)){
									$end_array = array('data-date-min-date'=>$start_date,'readonly'=>'readonly','required'=>true);
								}
								else{
									$end_array = array('data-date-min-date'=>$start_date,'readonly'=>'readonly');
								}
								?>
                                <?php echo render_date_input('deadline','expected_closing_date',$deadline,$end_array); ?>
                            </div>
							<?php }?>
                        </div>
                        <?php if(isset($project) && $project->date_finished != null && $project->status == 4) { ?>
                        <?php echo render_datetime_input('date_finished','project_completed_date',_dt($project->date_finished)); ?>
                        <?php } if(!empty($need_fields) && in_array("tags", $need_fields)){?>
                        <div class="form-group" id="ch_tag">
                            <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i>
                                <?php echo _l('tags'); ?>
								<?php if(!empty($important_fields) && in_array("tags", $important_fields)){?>
									<span style="color: #d2be19;margin-left: 5px;" title="<?php if(!empty($important_messages->tags)){echo $important_messages->tags;} ?>" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>
								<?php }?>
							</label>
                            <input type="text" class="tagsinput" id="tags" name="tags"
                                value="<?php echo (isset($project) ? prep_tags_input(get_tags_in($project->id,'project')) : ''); ?>"
                                data-role="tagsinput" onchange="ch_tags(this)">
                        </div>
						<?php } //if(!empty($need_fields) && in_array("tags", $need_fields)){?>
                        <?php $rel_id_custom_field = (isset($project) ? $project->id : false); ?>
                        <?php echo render_custom_fields('projects',['project_id'=>$rel_id_custom_field,'lead_id'=>$lead_id]); ?>
						<?php if(!empty($need_fields) && in_array("description", $need_fields)){?>
                        <p class="bold"><?php if(!empty($need_fields) && in_array("description", $need_fields) && !empty($mandatory_fields) && in_array("description", $mandatory_fields)){ ?> <small class="req text-danger">* </small><?php } ?><?php echo _l('project_description'); ?>
						<?php if(!empty($important_fields) && in_array("description", $important_fields)){?>
							<span style="color: #d2be19;margin-left: 5px;margin-right:85%" title="<?php if(!empty($important_messages->description)){echo $important_messages->description;} ?>" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>
						<?php }?>
						</p>
                        <?php $contents = ''; if(isset($project)){$contents = $project->description;} ?>
                        <?php echo render_textarea('description','',$contents,array(),array(),''); ?>
                        <?php }
						if(total_rows(db_prefix().'emailtemplates',array('slug'=>'assigned-to-project','active'=>0)) == 0){ ?>
                        <!-- <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="send_created_email" id="send_created_email">
                            <label for="send_created_email"><?php echo _l('project_send_created_email'); ?></label>
                        </div> -->
                        <?php } ?>
                        <div class="text-right">
                            <a href="<?php echo admin_url('projects') ?>" class="btn btn-default">Cancel</a>
                            <button type="submit" data-form="#project_form" class="btn btn-info" autocomplete="off"
                                data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('submit'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5" style=" display:none">
                <div class="panel_s">
                    <div class="panel-body" id="project-settings-area">
                        <h4 class="no-margin">
                            <?php echo _l('project_settings'); ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <?php foreach($settings as $setting){

            $checked = ' checked';
            if(isset($project)){
                if($project->settings->{$setting} == 0){
                    $checked = '';
                }
            } else {
                foreach($last_project_settings as $last_setting) {
                    if($setting == $last_setting['name']){
                        // hide_tasks_on_main_tasks_table is not applied on most used settings to prevent confusions
                        if($last_setting['value'] == 0 || $last_setting['name'] == 'hide_tasks_on_main_tasks_table'){
                            $checked = '';
                        }
                    }
                }
                if(count($last_project_settings) == 0 && $setting == 'hide_tasks_on_main_tasks_table') {
                    $checked = '';
                }
            } ?>
                        <?php if($setting != 'available_features'){ ?>
                        <div class="checkbox">
                            <input type="checkbox" name="settings[<?php echo $setting; ?>]" <?php echo $checked; ?>
                                id="<?php echo $setting; ?>">
                            <label for="<?php echo $setting; ?>">
                                <?php if($setting == 'hide_tasks_on_main_tasks_table'){ ?>
                                <?php echo _l('hide_tasks_on_main_tasks_table'); ?>
                                <?php } else{ ?>
                                <?php echo _l('project_allow_client_to',_l('project_setting_'.$setting)); ?>
                                <?php } ?>
                            </label>
                        </div>
                        <?php } else { ?>
                        <div class="form-group mtop15 select-placeholder project-available-features">
                            <label for="available_features"><?php echo _l('visible_tabs'); ?></label>
                            <select name="settings[<?php echo $setting; ?>][]" id="<?php echo $setting; ?>"
                                multiple="true" class="selectpicker" id="available_features" data-width="100%"
                                data-actions-box="true" data-hide-disabled="true">
                                <?php foreach(get_project_tabs_admin() as $tab) {
                            $selected = '';
                            if(isset($tab['collapse'])){ ?>
                                <optgroup label="<?php echo $tab['name']; ?>">
                                    <?php foreach($tab['children'] as $tab_dropdown) {
                                        $selected = '';
                                        if(isset($project) && (
                                            (isset($project->settings->available_features[$tab_dropdown['slug']])
                                                && $project->settings->available_features[$tab_dropdown['slug']] == 1)
                                            || !isset($project->settings->available_features[$tab_dropdown['slug']]))) {
                                            $selected = ' selected';
                                    } else if(!isset($project) && count($last_project_settings) > 0) {
                                        foreach($last_project_settings as $last_project_setting) {
                                            if($last_project_setting['name'] == $setting) {
                                                if(isset($last_project_setting['value'][$tab_dropdown['slug']])
                                                    && $last_project_setting['value'][$tab_dropdown['slug']] == 1) {
                                                    $selected = ' selected';
                                            }
                                        }
                                    }
                                } else if(!isset($project)) {
                                    $selected = ' selected';
                                }
                                ?>
                                    <option value="<?php echo $tab_dropdown['slug']; ?>"
                                        <?php echo $selected; ?><?php if(isset($tab_dropdown['linked_to_customer_option']) && is_array($tab_dropdown['linked_to_customer_option']) && count($tab_dropdown['linked_to_customer_option']) > 0){ ?>
                                        data-linked-customer-option="<?php echo implode(',',$tab_dropdown['linked_to_customer_option']); ?>"
                                        <?php } ?>><?php echo $tab_dropdown['name']; ?></option>
                                    <?php } ?>
                                </optgroup>
                                <?php } else {
                        if(isset($project) && (
                            (isset($project->settings->available_features[$tab['slug']])
                             && $project->settings->available_features[$tab['slug']] == 1)
                            || !isset($project->settings->available_features[$tab['slug']]))) {
                            $selected = ' selected';
                    } else if(!isset($project) && count($last_project_settings) > 0) {
                        foreach($last_project_settings as $last_project_setting) {
                            if($last_project_setting['name'] == $setting) {
                                if(isset($last_project_setting['value'][$tab['slug']])
                                    && $last_project_setting['value'][$tab['slug']] == 1) {
                                    $selected = ' selected';
                            }
                        }
                    }
                } else if(!isset($project)) {
                    $selected = ' selected';
                }
                ?>
                                <option value="<?php echo $tab['slug']; ?>"
                                    <?php if($tab['slug'] =='project_overview'){echo ' disabled selected';} ?>
                                    <?php echo $selected; ?>
                                    <?php if(isset($tab['linked_to_customer_option']) && is_array($tab['linked_to_customer_option']) && count($tab['linked_to_customer_option']) > 0){ ?>
                                    data-linked-customer-option="<?php echo implode(',',$tab['linked_to_customer_option']); ?>"
                                    <?php } ?>>
                                    <?php echo $tab['name']; ?>
                                </option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <?php } ?>
                        <hr class="no-margin" />
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
        <div class="btn-bottom-pusher"></div>
    </div>
</div>


<div class="modal fade" id="clientid_add_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
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

<div class="modal fade" id="project_contacts_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('add_new',_l('contact')); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/form_contact/undefined',array('id'=>'project_contacts_add')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo form_hidden('clientid',''); ?>
                        <?php $attrs = array('autofocus'=>true, 'required'=>true); ?>
                        <?php echo render_input( 'firstname', 'client_firstname','','',$attrs); ?>
                        <div id="contact_exists_info" class="hide"></div>
                        <?php echo render_input( 'title', 'contact_position',''); ?>
                        
                        <div class="form-group" app-field-wrapper="email">
                     
                        <label for="email" class="control-label">Email </label>
                        <div class="input-group">
                        <input type="email" id="email" name="email" class="form-control" value="">
                        <div class="input-group-addon"><span class="add_field_button_ae pointer "><i class="fa fa fa-plus"></i></span></div>
                        </div>
                        </div>
                        
                        <div class="input_fields_wrap_ae">
                        
                        </div>
                        
                       
                        <div class="form-group" app-field-wrapper="phonenumber">
                        <label for="phonenumber" class="control-label">Phone  </label>
                        <div class="input-group">
                        <input type="text" id="phonenumber" name="phonenumber" class="form-control" autocomplete="off" value="">
                        <div class="input-group-addon"><span class="add_field_button_ap pointer "><i class="fa fa fa-plus"></i></span></div>
                        </div>
                        </div>
                        
                        <div class="input_fields_wrap_ap">
                        
                        </div>
                        
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


<?php init_tail(); ?>

<script>
function ch_project_member(a){
	var cur_name = a.value;
	if(cur_name.trim() !=''){
		$("#project_members").removeClass("has-error");
	}
}
function ch_tags(a){
	var cur_name = a.value;
	if(cur_name.trim() !=''){
		$("#ch_tag").removeClass("has-error");
		$("#tags-error").hide();
	}
}
$(document).ready(function() {
		$("#name").on("keyup", function() {
		var cur_name = $('#name').val();
		if(cur_name.trim() !=''){
			$("#ch_name").removeClass("has-error");
			$('#name-error').hide();
		}
	});

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

<script>
window.addEventListener('load', function() {
    appValidateForm($('#clientid_add_group_modal'), {
        company: 'required'
    }, manage_customer_groups);

function manage_customer_groups(){

}
$('#name').on('keyup', function() {
    var name = $('#name').val();
    var pid = $('#projectid').val();
    var $companyExistsDiv = $('#company_exists_info');
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

    $('#clientid_add_group_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var group_id = $(invoker).data('id');
        $('#clientid_add_group_modal input[name="company"]').val('');
        // is from the edit button
        if (typeof(group_id) !== 'undefined') {
            $('#clientid_add_group_modal input[name="company"]').val($(invoker).parents('tr').find('td')
                .eq(0).text());
        }
    });

});

$('#clientid_add_group_modal').submit(function(e) {
    e.preventDefault();
    var form = $(this);
    var data = getFormData(form);
    if (data.company) {
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),
            dataType: 'json',
            success: function(msg) {
                $('#clientid').append('<option value="' + msg.id + '" selected="selected">' + msg
                    .company + '</option>');
                $('#clientid').val(msg.id);
                $('#clientid_add_group_modal input[name="company"]').val('');
                alert_float('success', msg.message);
                setTimeout(function() {
                    $('#clientid').selectpicker('refresh');
                    $('.clientiddiv div.filter-option-inner-inner').html(msg.company)
                }, 500);
                $('#clientid_add_modal').modal('hide');
            }
        });
    }
});

$('#clientid').on('change', function() {
    var clientId = this.value;
    var data = {clientId:clientId};
   
    $.ajax({
        type: 'POST',
        url: admin_url + 'projects/getContactpersonList',
        data: data,
        dataType: 'json',
        success: function(msg) {
            $('.contactsdiv select').html(msg.success);
			$('.primarydiv select').html('');
            setTimeout(function() {
                $('.contactsdiv select').selectpicker('refresh');
                $('.primarydiv select').selectpicker('refresh');
            }, 500);
        }
    });
});



window.addEventListener('load', function() {
    appValidateForm($('#project_contacts_add'), {
        firstname: 'required'
    }, manage_project_contacts_add);

    function manage_project_contacts_add(form) {}

    $('#project_contacts_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var group_id = $(invoker).data('id');
        $('#project_contacts_add input[name="firstname"]').val('');
        $('#project_contacts_add input[name="email"]').val('');
        $('#project_contacts_add input[name="phonenumber"]').val('');
        // is from the edit button
        if (typeof(group_id) !== 'undefined') {
            $('#project_contacts_add input[name="firstname"]').val($(invoker).parents('tr').find('td')
                .eq(0).text());
        }
    });

});
$('#project_contacts_add').submit(function(e) {
    e.preventDefault();
    var form = $(this);
    var formData = {};
    var data = getFormData($('#project_contacts_add'));
    var emails = $('input[name="alternative_emails[]"]').map(function(){ 
        return this.value; 
    }).get();
    var phones = $('input[name="alternative_phonenumber[]"]').map(function(){ 
        return this.value; 
    }).get();
    data['alternative_emails[]'] = emails;
    data['alternative_phonenumber[]'] = phones;
    
    data.clientid = $('#clientid').val();
    if (data.firstname) {
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: data,
            dataType: 'json',
            success: function(msg) {
                $('.contactsdiv select').html(msg.firstname);
                $('.primarydiv select').html(msg.firstname);
                alert_float('success', msg.message);
                setTimeout(function() {
                    $('.contactsdiv select').selectpicker('refresh');
                    $('.primarydiv select').selectpicker('refresh');
                }, 500);
                $('#project_contacts_modal').modal('hide');
            }
        });
    }
});

// $('.s').on("change", function(e) {
   
//     var selected=[];
//  $('#project_contacts :selected').each(function(){
//      selected[$(this).val()]=$(this).text();
//     });
// console.log(selected);
    
// });

$(".contactsdiv .selectpicker").change(function () {
    $('#primary_contact').empty().append('<option value="">Nothing Selected</option>');
    $('#primary_contact').selectpicker('refresh');
    var option_all = $(".contactsdiv .selectpicker option:selected").map(function () {
        $('#primary_contact').append('<option value="' + $(this).val() + '">' + $(this).text() + '</option>');
    });
    $('#primary_contact').selectpicker('refresh');
});

function getFormData($form) {
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i) {
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}
</script>


<script>
<?php
if (isset($project)) {
    ?>
    var original_project_status = '<?php echo $project->status; ?>'; 
    <?php
} ?>
$(function() {
    <?php if($lead_id):?>
    <?php if(isset($_POST['clientid']) && $_POST['clientid']): ?>
        $("select[id^=project_contacts]").trigger('change');
    <?php endif; ?>
    <?php endif; ?>

    $("#start_date").on("change", function(e) {
        var obj = $("#deadline");
        obj.datepicker('destroy').attr("data-date-min-date", $(this).val());
        init_datepicker(obj);
    });
    $("#deadline").on("change", function(e) {
        var obj = $("#start_date");
        obj.datepicker('destroy').attr("data-date-end-date", $(this).val());
        init_datepicker(obj);
    });


    $('select[name="billing_type"]').on('change', function() {
        var type = $(this).val();
        if (type == 1) {
            $('#project_cost').removeClass('hide');
            $('#project_rate_per_hour').addClass('hide');
        } else if (type == 2) {
            $('#project_cost').addClass('hide');
            $('#project_rate_per_hour').removeClass('hide');
        } else {
            $('#project_cost').addClass('hide');
            $('#project_rate_per_hour').addClass('hide');
        }
    });

    appValidateForm($('form'), {
        name: {
                required: true,
                normalizer: function(value) {
                    return $.trim(value);
                }
            },
		<?php if(!empty($mandatory_fields)){
			foreach($mandatory_fields as $need_field1){
				if($need_field1 == 'project_contacts[]' || $need_field1 == 'project_members[]'){
					echo "'".$need_field1."': 'required',\n";
				}else{
					echo $need_field1.": 'required',\n";
				}
			}
		}
		?>
       // clientid: 'required',
       // 'project_contacts[]': 'required',
       // primary_contact: 'required',
       // pipeline_id: 'required',
        //status: 'required',
        teamleader: 'required',
        // 'project_members[]': 'required',
       // start_date: 'required',
       // billing_type: 'required'
    });

    $('select[name="status"]').on('change', function() {
        var status = $(this).val();
        var mark_all_tasks_completed = $('.mark_all_tasks_as_completed');
        var notify_project_members_status_change = $('.notify_project_members_status_change');
        mark_all_tasks_completed.removeClass('hide');
        if (typeof(original_project_status) != 'undefined') {
            if (original_project_status != status) {

                mark_all_tasks_completed.removeClass('hide');
                notify_project_members_status_change.removeClass('hide');

                if (status == 4 || status == 5 || status == 3) {
                    $('.recurring-tasks-notice').removeClass('hide');
                    var notice = "<?php echo _l('project_changing_status_recurring_tasks_notice'); ?>";
                    notice = notice.replace('{0}', $(this).find('option[value="' + status + '"]').text()
                        .trim());
                    $('.recurring-tasks-notice').html(notice);
                    $('.recurring-tasks-notice').append(
                        '<input type="hidden" name="cancel_recurring_tasks" value="true">');
                    mark_all_tasks_completed.find('input').prop('checked', true);
                } else {
                    $('.recurring-tasks-notice').html('').addClass('hide');
                    mark_all_tasks_completed.find('input').prop('checked', false);
                }
            } else {
                mark_all_tasks_completed.addClass('hide');
                mark_all_tasks_completed.find('input').prop('checked', false);
                notify_project_members_status_change.addClass('hide');
                $('.recurring-tasks-notice').html('').addClass('hide');
            }
        }

        if (status == 4) {
            $('.project_marked_as_finished').removeClass('hide');
        } else {
            $('.project_marked_as_finished').addClass('hide');
            $('.project_marked_as_finished').prop('checked', false);
        }
        $('#status-error').hide();
    });

    $('form').on('submit', function() {
        $('select[name="billing_type"]').prop('disabled', false);
        $('#available_features,#available_features option').prop('disabled', false);
        $('input[name="project_rate_per_hour"]').prop('disabled', false);
    });

    var progress_input = $('input[name="progress"]');
    var progress_from_tasks = $('#progress_from_tasks');
    var progress = progress_input.val();

    $('.project_progress_slider').slider({
        min: 0,
        max: 100,
        value: progress,
        disabled: progress_from_tasks.prop('checked'),
        slide: function(event, ui) {
            progress_input.val(ui.value);
            $('.label_progress').html(ui.value + '%');
        }
    });

    progress_from_tasks.on('change', function() {
        var _checked = $(this).prop('checked');
        $('.project_progress_slider').slider({
            disabled: _checked
        });
    });

    $('#project-settings-area input').on('change', function() {
        if ($(this).attr('id') == 'view_tasks' && $(this).prop('checked') == false) {
            $('#create_tasks').prop('checked', false).prop('disabled', true);
            $('#edit_tasks').prop('checked', false).prop('disabled', true);
            $('#view_task_comments').prop('checked', false).prop('disabled', true);
            $('#comment_on_tasks').prop('checked', false).prop('disabled', true);
            $('#view_task_attachments').prop('checked', false).prop('disabled', true);
            $('#view_task_checklist_items').prop('checked', false).prop('disabled', true);
            $('#upload_on_tasks').prop('checked', false).prop('disabled', true);
            $('#view_task_total_logged_time').prop('checked', false).prop('disabled', true);
        } else if ($(this).attr('id') == 'view_tasks' && $(this).prop('checked') == true) {
            $('#create_tasks').prop('disabled', false);
            $('#edit_tasks').prop('disabled', false);
            $('#view_task_comments').prop('disabled', false);
            $('#comment_on_tasks').prop('disabled', false);
            $('#view_task_attachments').prop('disabled', false);
            $('#view_task_checklist_items').prop('disabled', false);
            $('#upload_on_tasks').prop('disabled', false);
            $('#view_task_total_logged_time').prop('disabled', false);
        }
    });

    // Auto adjust customer permissions based on selected project visible tabs
    // Eq Project creator disable TASKS tab, then this function will auto turn off customer project option Allow customer to view tasks

    $('#available_features').on('change', function() {
        $("#available_features option").each(function() {
            if ($(this).data('linked-customer-option') && !$(this).is(':selected')) {
                var opts = $(this).data('linked-customer-option').split(',');
                for (var i = 0; i < opts.length; i++) {
                    var project_option = $('#' + opts[i]);
                    project_option.prop('checked', false);
                    if (opts[i] == 'view_tasks') {
                        project_option.trigger('change');
                    }
                }
            }
        });
    });
    $("#view_tasks").trigger('change'); 
    <?php
    if (!isset($project)) {
        ?>
        $('#available_features').trigger('change'); 
        <?php
    } ?>
});
</script>


<script>
$(function() {
    if ($('#status').length > 0) {
        $('.form_status .selectpicker').addClass("formstatus");
    }
    if ($('.form_assigned .selectpicker').length > 0) {
        $('.form_assigned .selectpicker').addClass("formassigned");
    }

    if ($('#teamleader').length > 0) {
        $('.form_teamleader .selectpicker').addClass("formteamleader");
    }

    $('#pipeline_id').change(function() {
        $('.formstatus').selectpicker('destroy');
        $('.formstatus').html('').selectpicker('refresh');

        $('.formassigned').selectpicker('destroy');
        $('.formassigned').html('').selectpicker('refresh');

        $('.formteamleader').selectpicker('destroy');
        $('.formteamleader').html('').selectpicker('refresh');

        var pipeline_id = $('#pipeline_id').val();
        $.ajax({
            url: admin_url + 'leads/changepipeline',
            type: 'POST',
            data: {
                'pipeline_id': pipeline_id
            },
            dataType: 'json',
            success: function success(result) {
                $('.formstatus').selectpicker('destroy');
                $('.formstatus').html(result.statuses).selectpicker('refresh');


                $('.formteamleader').selectpicker('destroy');
                $('.formteamleader').html(result.teamleaders).selectpicker('refresh');

                $('.formassigned').selectpicker('destroy');
                $('.formassigned').html(result.followers).selectpicker('refresh');
                $('#teamleader-error').hide();
            }
        });
    });

    $('#teamleader').change(function() {
        $('.formassigned').selectpicker('destroy');
        $('.formassigned').html('').selectpicker('refresh');
        var pipeline_id = $('#pipeline_id').val();
        var teamleader = $('#teamleader').val();
        $.ajax({
            url: admin_url + 'leads/getpipelineteamember',
            type: 'POST',
            data: {
                'leaderid': teamleader,
                'pipeline': pipeline_id
            },
            dataType: 'json',
            success: function success(result) {
                $('.formassigned').selectpicker('destroy');
                $('.formassigned').html(result.teammembers).selectpicker('refresh');
                $('#teamleader-error').hide();
            }
        });
    });
    var pipelines_count = <?php echo count((array)$pipelines); ?>;
    if(pipelines_count == 1){
        $('#pipeline_id option[value="<?php echo $pipelines[0]['id']; ?>"]').attr('selected', 'selected')
        $('#pipeline_id').selectpicker('refresh');
        $('#pipeline_id').trigger('change');
    }

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

    $('#firstname').on('keyup', function() {
        var name = $('#firstname').val();
        var pid = $('#contactid').val();
        var $companyExistsDiv = $('#contact_exists_info');
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

    $('input#project_cost').on('keypress', function() {
       return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57);
    });


    $("select[id^=project_contacts]").change(function() {
        $('.contactsdiv p.text-danger').hide();
    });

    $("select[id^=project_members]").change(function() {
        $('.form_assigned p.text-danger').hide();
    });
    tinymce.init({
        selector: 'textarea#description',
        height: 100,
        menubar: false,
        plugins: [
          'advlist autolink lists charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat',
		setup: function(ed) {  
                ed.on('keyup', function(e) {  
                   get_description()  
                });  
            }
      });
});
function get_description(){
	var text = tinyMCE.get('description').getContent();
	$('#description').val(text.trim());
	if(text.trim()!=''){
		$('#description-error').hide();
	}
}
</script>
<script>
      
    </script>
<style>
.contactsdiv .input-group.input-group-select .form-group {
    margin-bottom: 0px;
}
</style>
</body>

</html>