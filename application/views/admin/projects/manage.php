<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php if($this->session->flashdata('ch_error')){ ?>
    <div class="alert alert-success">
     Organization has been Restored.
   </div>
<?php } ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
            <div class="panel_s">
              <div class="panel-body">
              <div class="_buttons">
              <?php if(has_permission('projects','','create')){ ?>
                <a href="<?php echo admin_url('projects/project'); ?>" class="btn btn-info pull-left display-block mright5">
                  <?php echo _l('new_project'); ?>
                </a>
              <?php }
             // $list_url = admin_url('projects/index_list?pipelines='.$pipelines[0]['id'].'&member=&gsearch=');
              $list_url = admin_url('projects/index_list?pipelines=&member=&gsearch=');
              $kanban_onscroll_url = admin_url('projects/kanban_noscroll?pipelines='.$pipelines[0]['id'].'&member=&gsearch=');
              $kanban_url = admin_url('projects/kanbans?pipelines='.$pipelines[0]['id'].'&member=&gsearch=');
              $forecast_url = admin_url('projects/kanbans_forecast?pipelines='.$pipelines[0]['id'].'&member='.$mem.'&gsearch='.$gsearch);
              if(!is_admin(get_staff_user_id())) {
                  $list_url = admin_url('projects/index_list?pipelines='.$pipelines[0]['id'].'&member='.get_staff_user_id().'&gsearch=');
                  //$list_url = admin_url('projects/index_list?pipelines=&member='.get_staff_user_id().'&gsearch=');
                  $kanban_onscroll_url = admin_url('projects/kanban_noscroll?pipelines='.$pipelines[0]['id'].'&member='.get_staff_user_id().'&gsearch=');
                  $kanban_url = admin_url('projects/kanbans?pipelines='.$pipelines[0]['id'].'&member='.get_staff_user_id().'&gsearch=');
                  $forecast_url = admin_url('projects/kanbans_forecast?pipelines='.$pipelines[0]['id'].'&member='.$mem.'&gsearch='.$gsearch);
              }
              ?>
              <a href="<?php echo $list_url; ?>" data-toggle="tooltip" title="<?php echo _l('projects'); ?>" class="btn btn-primary"><i class="fa fa-list" aria-hidden="true"></i></a>
              <!-- <a href="<?php echo admin_url('projects/gantt?pipelines='.$pipelines[0]['id'].'&member=&gsearch='); ?>" data-toggle="tooltip" title="<?php echo _l('project_gant'); ?>" class="btn btn-default"><i class="fa fa-align-left" aria-hidden="true"></i></a> -->
              <a href="<?php echo $kanban_onscroll_url; ?>" data-toggle="tooltip" title="<?php echo _l('leads_switch_to_kanban_noscroll'); ?>" class="btn btn-default"><i class="fa fa-th" aria-hidden="true"></i></a>
              <a href="<?php echo $kanban_url; ?>" data-toggle="tooltip" title="<?php echo _l('leads_switch_to_kanban'); ?>" class="btn btn-default"><i class="fa fa-th-large" aria-hidden="true"></i></a>
              <a href="<?php echo $forecast_url; ?>" data-toggle="tooltip" title="<?php echo _l('leads_switch_to_forecast'); ?>" class="btn btn-default"><i class="fa fa-line-chart" aria-hidden="true"></i></a>
              
              

<div class="btn-group pull-right mleft4 mbot25 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('projects_list_column'); ?>">
       <!-- Button trigger modal -->
<button type="button" class="btn btn-default" data-toggle="modal" data-target="#projects_list_column_orderModal">
  <i class="fa fa-list" aria-hidden="true"></i>
</button>

<!-- Modal -->
<div class="modal fade" id="projects_list_column_orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<?php echo form_open_multipart(admin_url('settings/projects_list_column'),array('id'=>'projects_list_column')); ?>
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo _l('projects_list_column_order'); ?></h5>
      </div>
      <div class="modal-body">
        <div class="form-group">

<?php $colarr = deal_all_fields(); 
$custom_fields = get_table_custom_fields('projects');
$cus_1 = array();
foreach($custom_fields as $cfkey=>$cfval){
    $cus_1[$cfval['slug']] = $colarr[$cfval['slug']] = array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
}

$custom_fields = get_table_custom_fields('customers');
foreach($custom_fields as $cfkey=>$cfval){
    $cus_1[$cfval['slug']] = $colarr[$cfval['slug']] = array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
}
?>  
  <ul id="sortable">
  <?php $projects_list_column_order = (array)json_decode(get_option('projects_list_column_order')); 
  ?>
  <?php foreach($projects_list_column_order as $ckey=>$cval){
	  if((!empty($need_fields) && in_array($ckey, $need_fields)) || !empty($cus_1[$ckey])){
	  ?>
	  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
	  <input type="checkbox" name="settings[projects_list_column][<?php echo $ckey; ?>]" value="1" checked="checked" /> <?php echo _l($colarr[$ckey]['ll']); ?>
	  </li>
	  <?php }
	  } ?>
  <?php  foreach($colarr as $ckey=>$cval){  
	
	
  if((!empty($need_fields) && in_array($ckey, $need_fields)) || !empty($cus_1[$ckey])){
	 
	  if(!isset($projects_list_column_order[$ckey])){?>
	  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
	  <input type="checkbox" name="settings[projects_list_column][<?php echo $ckey; ?>]" value="1"/> <?php echo _l($cval['ll']); ?>
	  </li>
  <?php }
  }
  } ?>
  
</ul>
  
</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
	  </form>
    </div>
  </div>
</div>

</div>



              <?php if(!empty($need_fields) && (in_array("status", $need_fields) )){?>
              <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-filter" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-right width300">
                  <li>
                    <a href="#" data-cview="all" onclick="dt_custom_view('','.table-projects',''); return false;">
                      <?php echo _l('expenses_list_all'); ?>
                    </a>
                  </li>
                  <?php
				  $fields = get_option('deal_fields');
					$need_fields = array();
					if(!empty($fields) && $fields != 'null'){
						$need_fields = json_decode($fields);
					}
                  // Only show this filter if user has permission for projects view otherwise wont need this becuase by default this filter will be applied
                  if(has_permission('projects','','view')/* && !empty($need_fields) && in_array("members", $need_fields)*/){ ?>
                  <li>
                    <a href="#" data-cview="my_projects" onclick="dt_custom_view('my_projects','.table-projects','my_projects'); return false;">
                      <?php echo _l('home_my_projects'); ?>
                    </a>
                  </li>
                  <?php } ?>
                  <li class="divider"></li>
                  <?php foreach($statuses as $status){ ?>
                    <li class="<?php if($status['filter_default'] == 1 && !$this->input->get('status') || $this->input->get('status') == $status['id']){echo 'active';} ?>">
                      <a href="#" data-cview="<?php echo 'project_status_'.$status['id']; ?>" onclick="dt_custom_view('project_status_<?php echo $status['id']; ?>','.table-projects','project_status_<?php echo $status['id']; ?>'); return false;">
                        <?php echo $status['name']; ?>
                      </a>
                    </li>
                    <?php } ?>
                  </ul>
                </div>
			  <?php }?>

                <div style="float:right; width:68%;">
                <div class="col-md-1 padding0">
							<h4><?php echo _l('filter_by'); ?></h4>
						</div>
						<?php echo form_open(admin_url('projects/index_list'), array('method'=>'get','id'=>'ganttFiltersForm')); ?>
						<!-- <div class="col-md-3 pipeselect">
							<select class="selectpicker" data-none-selected-text="<?php echo _l('all'); ?>" name="pipelines" data-width="100%">
              
								<?php foreach($pipelines as $status){
									?>
									<option value="<?php echo $status['id']; ?>"<?php if($selected_statuses == $status['id']){echo ' selected';} ?>>
										<?php //echo $status['name']; ?>
									</option>
								<?php } ?>
							</select>
						</div> -->
						<?php
			            /**
			             * Only show this filter if user has permission for projects view otherwise
			             * wont need this becuase by default this filter will be applied
			             */
						 $mandatory_fields = '';
						$fields = get_option('deal_fields');
						$need_fields = array();
						if(!empty($fields) && $fields != 'null'){
							$need_fields = json_decode($fields);
						}
			            if(has_permission('projects','','view') /*&& !empty($need_fields) && in_array("members", $need_fields)*/){ ?>
			            	<div class="col-md-4">
			            		<select class="selectpicker" data-live-search="true" data-title="All Members" name="member" data-width="100%">
                      <option value=""></option>
                        <?php if(is_admin(get_staff_user_id()) || count($project_members) > 1) { ?> 
                          <option value="" <?php if($selectedMember == ''){echo ' selected'; } ?>>All Members</option>
                        <?php } ?>
			            			<?php foreach($project_members as $member) { ?>
			            				<option value="<?php echo $member['staff_id']; ?>"<?php if($selectedMember == $member['staff_id']){echo ' selected'; } ?>>
			            					<?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
			            				</option>
			            			<?php } ?>
			            	</select>
			            </div>
					<?php } ?>
					<div class="col-md-4">
						<div class="form-group">
							<input type="search" name="gsearch" class="form-control input-sm" value="<?php echo (isset($gsearch)?$gsearch:''); ?>" placeholder="Search..."/>
						</div>
					</div>
			        <div class="col-md-2">
			        	<button type="submit" class="btn btn-default"><?php echo _l('apply'); ?></button>
			        </div>
              <?php echo form_close(); ?>
              </div>
			        <!-- <div class="clearfix"></div>
                <hr class="hr-panel-heading" />
              </div> -->
               <div class="row mbot15">
                <div class="col-md-12">
                  <!-- <h4 class="no-margin"><?php echo _l('projects_summary'); ?></h4>
                  <br> -->
                  <?php
                  $_where = '';
                  if(!has_permission('projects','','view')){
                    $_where = 'id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id='.get_staff_user_id().')';
                  }
                  // ROle based records
                  $my_staffids = $this->staff_model->get_my_staffids();
                  if($my_staffids && count((array)$my_staffids) > 0){
                      $_where = ($_where == '' ? '' : $_where.' AND ');
                      $_where .= ' id IN (SELECT ' . db_prefix() . 'projects.id FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ') OR  ' . db_prefix() . 'projects.teamleader in (' . implode(',',$my_staffids) . ') )';
                  }

                  ?>
                </div>
                <div class="_filters _hidden_inputs">
                  <?php
                  echo form_hidden('my_projects');
                  foreach($statuses as $status){
                   $value = $status['id'];
                     if($status['filter_default'] == 1 && !$this->input->get('status')){
                        $value = '';
                     } else if($this->input->get('status')) {
                        $value = ($this->input->get('status') == $status['id'] ? $status['id'] : "");
                     }
                     //echo form_hidden('project_status_'.$status['id'],$value);
                     echo form_hidden('project_status_'.$status['id'],'');
                    ?>
                   <!-- <div class="col-md-5ths total-column"> 
                    <div class="panel_s">
                      <div class="panel-body">
                          <?php 
                        
                          
                          $where = ($_where == '' ? '' : $_where.' AND ').'status = '.$status['id'];
                          
                          $where .= ' AND projects.deleted_status = 0 ';
                          
                          if ($_SESSION['member']) {
                              $where .= ' AND ' . db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . $_SESSION['member'] . ')';
                          }
                          
                          //echo "<pre>"; print_r($_SESSION); exit;
                          $pipeline = $_SESSION['pipelines'];
                          if (empty($pipeline)) {
                              $pipeline = 0;
                          }else{
                              $where .= ' AND ' . db_prefix() . 'projects.pipeline_id = '.$pipeline;
                          }
                          $gsearch = $_SESSION['gsearch'];
                          
                          if(!empty($gsearch)){
                              $where .= ' AND ' . db_prefix() . 'projects.id IN (SELECT id FROM ' . db_prefix() . 'projects WHERE name like "%' . $gsearch . '%")';
                          }  
                          
                          
                          ?>
                          <a href="#" onclick="dt_custom_view('project_status_<?php echo $status['id']; ?>','.table-projects','project_status_<?php echo $status['id']; ?>',true); return false;">
                          <h3 class="bold" style="margin-top:0px;"><?php echo total_rows(db_prefix().'projects',$where); ?></h3>
                          <span style="color:<?php echo $status['color']; ?>" project-status-<?php echo $status['id']; ?>">
                          <?php echo $status['name']; ?>
                          </span>
                        </a>
                      </div>
                    </div>
                  </div> -->
                      <?php } ?>
               </div>
             </div>
             <div class="clearfix"></div>
              <hr class="hr-panel-heading" />
             <?php echo form_hidden('custom_view'); ?>
			 <div  class="header" id="myHeader" style="display:none;margin-left:14%;position:absolute;z-index:999">
				<div class="col-md-12" style="background: #fff;">
					<div class="col-md-2" style="width:auto">
						<a href="javascript:void(0);" id="del_mail" data-toggle="modal" data-target="#edt_multiple"  data-backdrop="static" data-keyboard="false" title="Edit" onclick="edit_multiple()"><i class="fa fa-edit fa-2x"  style="color:red"></i></a>
					</div>
					<?php /*<div class="col-md-2" style="width:auto;padding-top:6px">
						<a href="javascript:void(0);" style="color:#666" id="read_mail">Mark as Read </a>
					</div>
					<div class="col-md-2" style="width:auto;padding-top:6px">
						<a href="javascript:void(0);" style="color:#666" id="unread_mail">Mark as Unread </a>
					</div>*/?>
				</div>
			</div>
<?php if($this->session->flashdata('warning_msg_deal')){ ?>
    <div class="alert alert-warning" id="warning_div">
     <?php echo $this->session->flashdata('warning_msg_deal');
	 $this->session->unset_userdata('warning_msg_deal');
	 ?>
   </div>
<?php } ?>            
			<?php $this->load->view('admin/projects/table_html'); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="edt_multiple" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<?php echo form_open_multipart(admin_url('projects/bulk_edit'),array('id'=>'bulk_edit1')); ?>
      <div class="modal-header">
	  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h2 class="modal-title" id="exampleModalLabel"><?php echo _l('bulk_edit'); ?></h2>
      </div>
	  <input type="hidden" name="edit_cur_id" value="" id="edit_cur_id">
      <div class="modal-body">
		<?php if(!empty($need_fields_edit)){
			$i = 0;
			//echo '<pre>';print_r($mandatory_fields1);exit;
			foreach($need_fields_edit as $need_field12){
				$req_field = $need_field12;
				if($need_field12 == 'project_contacts[]'){
					$req_field = 'project_contacts';
				}
				else if($need_field12 == 'project_members[]'){
					$req_field = 'project_members';
				}
		?>
				<div class="form-group select-placeholder" style="margin-top:10px;">
					<label for="status" class="control-label required"><?php if(in_array($need_field12,$mandatory_fields1) || $req_field == 'name'){ ?><small class="req text-danger">* </small><?php  }echo _l($need_fields_label[$i]);?></label>
					<select required="true" data-actions-box="false" name="deal_fields[<?php echo $req_field;?>]" class="selectpicker ch_field" data-width="100%" onchange="check_need_field('<?php echo $need_fields_label[$i];?>',this)" id="bul_<?php echo $need_fields_label[$i];?>">
						<option value="<?php echo _l('keep_current_value');?>" selected><?php echo _l('keep_current_value');?></option>
						<option value="<?php echo _l('edit_current_value');?>" ><?php echo _l('edit_current_value');?></option>
						<?php if(!in_array($need_field12,$mandatory_fields1) && $need_fields_label[$i]!='project_name'){?>
							<option value="<?php echo _l('clear_field');?>" ><?php echo _l('clear_field');?></option>
						<?php }?>
					</select>
					<?php if($need_field12 == 'clientid'){?>
						<div class="ch_field_div" id="div_<?php echo $need_fields_label[$i];?>" style="display:none;margin-top:15px;">
							 <select id="clientid" name="sel_<?php echo $need_field12;?>" data-live-search="true" data-width="100%" class="ajax-search " style="display:block" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" onchange="get_person(this.value)">
								<?php //// $selected = (isset($project) ? $project->clientid : '');
								// if($selected == ''){
									//$selected = (isset($customer_id) ? $customer_id: '');
								// }
								// if($selected != ''){
									$selected = '';
									$rel_data = get_relation_data('customer',$selected);
									$rel_val = get_relation_values($rel_data,'customer');
									echo '<option value="'.$rel_val['id'].'" >'.$rel_val['name'].'</option>';
								//} 
								?>

							</select>
						</div>
					<?php }else if($need_field12 == 'project_contacts[]'){?>
						<div id="div_<?php echo $need_fields_label[$i];?>" style="display:none;margin-top:15px;" class="ch_field_div">
							 <select id="<?php echo $need_fields_label[$i];?>" name="sel_<?php echo $need_field12;?>" data-live-search="true" data-width="100%" class="selectpicker "
								data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" multiple="true" onchange="get_primary_person()">
								<?php 
								if(!empty($client_contacts)){
									foreach($client_contacts as $client_contact1){
										echo '<option value="'.$client_contact1['id'].'" >'.$client_contact1['firstname'].' '.$client_contact1['lastname'].'</option>';
									} 
								}
								?>

							</select>
						</div>
					<?php }else if($need_field12 == 'primary_contact'){?>
						<div id="div_<?php echo $need_fields_label[$i];?>" style="display:none;margin-top:15px;" class="ch_field_div">
							 <select id="<?php echo $need_fields_label[$i];?>" name="sel_<?php echo $need_field12;?>" data-live-search="true" data-width="100%" class="selectpicker "
								data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" >
								<?php 
								if(!empty($client_contacts)){
									foreach($client_contacts as $client_contact1){
										echo '<option value="'.$client_contact1['id'].'" >'.$client_contact1['firstname'].' '.$client_contact1['lastname'].'</option>';
									} 
								}
								?>

							</select>
						</div>
					<?php }else if($need_field12 == 'pipeline_id'){?>
						<div id="div_<?php echo $need_fields_label[$i];?>" style="display:none;margin-top:15px;" class="ch_field_div">
							 <select id="<?php echo $need_fields_label[$i];?>" name="sel_<?php echo $need_field12;?>" data-live-search="true" data-width="100%" class="selectpicker "
								data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" onchange="get_stages1(this.value)">
								<option></option>
								<?php 
								if(!empty($pipelines)){
									foreach($pipelines as $pipeline1){
										echo '<option value="'.$pipeline1['id'].'" >'.$pipeline1['name'].'</option>';
									} 
								}
								?>

							</select>
						</div>
					<?php }else if($need_field12 == 'status'){?>
						<div id="div_<?php echo $need_fields_label[$i];?>" style="display:none;margin-top:15px;" class="ch_field_div">
							 <select name="sel_<?php echo $need_field12;?>" id="<?php echo $need_fields_label[$i];?>" class="selectpicker " data-live-search="true" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
								<option></option>
								<?php foreach($statuses as $status){ ?>
									<option value="<?php echo $status['id']; ?>"><?php echo $status['name']; ?></option>
								<?php }?>
							</select>
						</div>
					<?php }else if($need_field12 == 'teamleader'){?>
						<div id="div_<?php echo $need_fields_label[$i];?>" style="display:none;margin-top:15px;" class="ch_field_div">
							 <select name="sel_<?php echo $need_field12;?>" id="<?php echo $need_fields_label[$i];?>" class="selectpicker " data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
								<option></option>
								<?php foreach($teamleaders as $teamleader1){ ?>
									<option value="<?php echo $teamleader1['id']; ?>"><?php echo $teamleader1['name']; ?></option>
								<?php }?>
							</select>
						</div>
					<?php }else if($need_field12 == 'project_members[]'){?>
						<div id="div_<?php echo $need_fields_label[$i];?>" style="display:none;margin-top:15px;" class="ch_field_div">
							 <select name="sel_<?php echo $need_field12;?>" id="<?php echo $need_fields_label[$i];?>" class="selectpicker " data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" multiple="true">
								<option></option>
								<?php foreach($teamleaders as $teamleader1){ ?>
									<option value="<?php echo $teamleader1['id']; ?>"><?php echo $teamleader1['name']; ?></option>
								<?php }?>
							</select>
						</div>
					<?php }else if($need_field12 == 'project_cost'){?>
						<div id="div_<?php echo $need_fields_label[$i];?>" style="display:none;margin-top:15px;" class="ch_field_div">
							 <select  class="selectpicker ch_field" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" name="project_currency">
								<option></option>
								<?php foreach($allcurrency as $currency){ ?>
									<option value="<?php echo $currency['name']; ?>" <?php if($basecurrency->id == $currency['id']){ echo 'selected';}?>><?php echo $currency['name'].'('.$currency['symbol'].')'; ?></option>
								<?php }?>
							</select>
							<div  style="margin-top:15px;">
								
								<input type="number" id="<?php echo $need_fields_label[$i];?>" name="sel_<?php echo $need_field12;?>" class="form-control ch_field1" min="0" value="" autocomplete="new-number" placeholder="Cost">
							</div>
						</div>
					<?php }else if($need_field12 == 'project_start_date' || $need_field12 == 'project_deadline'){?>
						<div id="div_<?php echo $need_fields_label[$i];?>" style="display:none;margin-top:15px;" class="ch_field_div">
							<div class="input-group date">
								<input type="date" id="<?php echo $need_fields_label[$i];?>" name="sel_<?php echo $need_field12;?>" class="form-control datepicker1 ch_field1" min="" <?php if($need_fields_label[$i] == 'project_start_date'){?>onchange="get_min_deadline(this)"<?php }?>>
								<div class="input-group-addon">
									<i class="fa fa-calendar calendar-icon"></i>
								</div>
							</div>
						</div>
					<?php }else if($need_field12 == 'tags' ){?>
						<div id="div_<?php echo $need_fields_label[$i];?>" style="display:none;margin-top:15px;" class="ch_field_div">
							<div class="input-group date">
								<input type="text" class="tagsinput tagit-hidden-field ch_field1" id="<?php echo $need_fields_label[$i];?>" name="sel_<?php echo $need_field12;?>" value="" data-role="tagsinput">
								<ul class="tagit ui-widget ui-widget-content ui-corner-all">
									<li class="tagit-new">
										<input type="text" class="ui-widget-content ui-autocomplete-input" placeholder="Tag" autocomplete="off" style="display:none">
									</li>
								</ul>
							</div>
						</div>
					<?php }else if($need_field12 != 'description'){?>
						<div id="div_<?php echo $need_fields_label[$i];?>" style="display:none;margin-top:15px;" class="ch_field_div">
							 <input type="text" name="sel_<?php echo $need_field12;?>"  class="form-control ch_field1"  id="<?php echo $need_fields_label[$i];?>"/>
						</div>
					<?php }else{?>
						<div id="div_<?php echo $need_fields_label[$i];?>" style="display:none;margin-top:15px;" class="ch_field_div">
							 <textarea name="sel_<?php echo $need_field12;?>"  class="form-control ch_field1" id="<?php echo $need_fields_label[$i];?>" ></textarea>
						</div>
					<?php }?>
				</div>
		<?php $i++;
			}
		} ?>
		<?php 
		$rel_id_custom_field =  false; 
        echo render_custom_fields_edit('projects',$rel_id_custom_field); 
		?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
	  </form>
    </div>
  </div>
</div>

<?php $this->load->view('admin/projects/copy_settings'); ?>
<?php init_tail(); ?>
<script>
$(function(){
     var ProjectsServerParams = {};

     $.each($('._hidden_inputs._filters input'),function(){
         ProjectsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
     });

     initDataTable('.table-projects', admin_url+'projects/table', undefined, [0], ProjectsServerParams, <?php echo hooks()->apply_filters('projects_table_default_order', json_encode(array())); ?>);

     init_ajax_search('customer', '#clientid_copy_project.ajax-search');
});
</script>
<script>
  $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );
   function check_all(a){
		$(".check_mail").prop('checked', false);
		if(a.checked == true){
			$(".check_mail").prop('checked', true);
		}
		check_header();
	}
	 function check_header(){
		 $('#myHeader').hide();
		 $('#edit_cur_id').val('');
		 var req_val = '';
		 $("input:checkbox[class=check_mail]:checked").each(function () {
			 if(this.value != 'all'){
				 req_val += this.value+','
			 }
			$('#myHeader').show();
		});
		$('#edit_cur_id').val(req_val);
		var a = $("input[type='checkbox'][class=check_mail]");
		if(a.filter(":checked").length!= a.length){
			$("#select_all").prop('checked', false);
		}
		else{
			$("#select_all").prop('checked', true);
		}
	}
	function check_need_field(a,b){
		if(a=='project_customer'){
			$('#clientid').val('');
			$('#clientid').selectpicker('refresh');
		}else{
			$('#'+a).val('');
			$('#'+a).selectpicker('refresh');
		}
		$('#div_'+a).hide();
		$("#"+a).prop('required',false);
		$("#clientid").prop('required',false);
		if(!(isNaN(a))){
			document.getElementById("custom_fields[projects]["+a+"]").required = false;
		}

		if(b.value == 'Edit current value...'){
			$("#"+a).prop('required',true);
			if(a=='project_customer'){
				$("#clientid").prop('required',true);
			}
			if(!(isNaN(a))){
				document.getElementById("custom_fields[projects]["+a+"]").required = true;
			}
			$('#div_'+a).show();
		}
		if(a == 'project_customer' || a == 'project_contacts' || a == 'project_primary_contacts'){
			$('#bul_project_customer').val('Edit current value...');
			$('#div_project_customer').show();
			$('#bul_project_primary_contacts').val('Edit current value...');
			$('#div_project_primary_contacts').show();
			$('#bul_project_contacts').val('Edit current value...');
			$('#div_project_contacts').show();
		}
		
		if(a == 'project_status' || a == 'pipeline'){
			$('#bul_pipeline').val('Edit current value...');
			$('#div_pipeline').show();
			$('#bul_project_status').val('Edit current value...');
			$('#div_project_status').show();
		}
		$('.selectpicker').selectpicker('refresh');
	}
	function edit_multiple(){
		$('.ch_field').val('Keep current value');
		$('.ch_field').selectpicker('refresh');
		$('.ch_field1').val('');
		$('.ch_field_div').hide();
		var all_val = $('#edit_cur_id').val();
		$.ajax({url: "<?php echo admin_url('projects/edit_multiple');?>?ids="+all_val, success: function(result){
			var myArr = JSON.parse(result);
			document.getElementById("project_start_date").min = myArr.start_date;
			//$('.ch_field').val('');
			//$('.ch_field').empty();
			
			$('#status').empty();
			$('#status').append(myArr.stages);
			$('#status').selectpicker('refresh');
	  }});
	}
	function get_stages(pipeline){
		$.ajax({url: "<?php echo admin_url('projects/stages');?>?pipeline="+pipeline, success: function(result){
			var myArr = JSON.parse(result);
			$('#status').empty();
			$('#status').append(myArr.stages);
			$('#status').selectpicker('refresh');
	  }});
	}
	function get_stages1(pipeline){
		$.ajax({url: "<?php echo admin_url('projects/stages');?>?pipeline="+pipeline, success: function(result){
			var myArr = JSON.parse(result);
			$('#project_status').empty();
			$('#project_status').append(myArr.stages);
			$('#project_status').selectpicker('refresh');
	  }});
	}
	function get_person(org){
		$.ajax({url: "<?php echo admin_url('projects/get_org_person');?>?org="+org, success: function(result){
			var myArr = JSON.parse(result);
			$('#project_contacts').empty();
			$('#project_contacts').append(myArr.persons);
			$('#project_contacts').selectpicker('refresh');
	  }});
	}
	function get_primary_person(){
		var person = $('#project_contacts').val();
		$.ajax({url: "<?php echo admin_url('projects/get_cont_person');?>?person="+person, success: function(result){
			var myArr = JSON.parse(result);
			$('#project_primary_contacts').empty();
			$('#project_primary_contacts').append(myArr.persons);
			$('#project_primary_contacts').selectpicker('refresh');
	  }});
	}
	function get_min_deadline(a){
		document.getElementById("project_deadline").value = '';
		document.getElementById("project_deadline").min = a.value;
	}
	$( function() {
		appValidateForm($('#bulk_edit1'), {
			<?php if(!empty($mandatory_fields1)){
				foreach($mandatory_fields1 as $need_field1){
					if($need_field1 == 'project_contacts[]' || $need_field1 == 'project_members[]'){
						echo "'".$need_field1."': 'required',\n";
					}else{
						echo $need_field1.": 'required',\n";
					}
				}
			}
			?>
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
			toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat'
		  });
		  		  
	} );
	setTimeout(function() {
    $('#warning_div').fadeOut('fast');
}, 3500); //
  </script>
  <style>
  ul.dropdown-menu li:first-child{
	  display:block !important;
  }
  </style>
</body>
</html>
