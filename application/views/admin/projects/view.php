<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$can_user_edit =true;
if($project->approved==0){
   $can_user_edit =false;
}

if($deal_rejected && get_staff_user_id() == $project->created_by){
   $can_user_edit =true;
}

$hasHIstory =$this->approval_model->hasHistory('projects',$project->id)?true:false;
$hasApprovalFlow = $this->workflow_model->getflows('deal_approval',0,['service'=>'approval_level']);

?>
<?php init_head(); ?>

<div id="wrapper">
   <?php echo form_hidden('project_id',$project->id) ?>
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s project-top-panel panel-full">
               <div class="panel-body _buttons">
                  <div class="row">
                     <div class="col-md-7 project-heading">
                        <h3 class="hide project-name"><?php echo $project->name; ?></h3>
                        <div id="project_view_name" class="pull-left">
                           <!-- <select class="selectpicker" id="project_top" data-width="100%"<?php if(!empty($other_projects) && count($other_projects) > 6){ ?> data-live-search="true" <?php } ?>>
                              <option value="<?php echo $project->id; ?>" selected data-content="<?php echo $project->name; ?> - <small><?php echo '<a href="'. admin_url("clients/client/".$project->clientid).'" style="" >'.$project->client_data->company.'</a>'; ?></small>">
                                <?php echo $project->client_data->company; ?> <?php echo $project->name; ?>
                              </option>
                              <?php foreach($other_projects as $op){ ?>
                              <option value="<?php echo $op['id']; ?>" data-subtext="<?php echo $op['company']; ?>">#<?php echo $op['id']; ?> - <?php echo $op['name']; ?></option>
                              <?php } ?>
                           </select>
                           <?php echo $project->client_data->company; ?> <?php echo $project->name; ?>
                           <?php foreach($other_projects as $op){ ?>
                           #<?php echo $op['id']; ?> - <?php echo $op['name']; ?>
                           <?php } ?> -->
                           <?php //pre($project); ?>
                           <div class="dropdown bootstrap-select bs3">
                              <button type="button" class="btn dropdown-toggle btn-default" role="combobox" aria-owns="bs-select-1" aria-haspopup="listbox" aria-expanded="false" data-id="project_top" title="4654645 - TTS">
                                 <div class="filter-option">
                                    <div class="filter-option-inner">
                                       <div class="filter-option-inner-inner" style="position: absolute; display: inline-block; top: -8px; overflow:inherit">
                                       <div style="float:left;">
                                       <?php 
                                       if($productscnt > 0) {
                                          if($productscnt == 1)
                                            $projectcnt = ' - <a data-toggle="modal" data-target="#dealproduct_Modal">'.$productscnt.' Items</a>';
                                          else 
                                            $projectcnt = ' - <a data-toggle="modal" data-target="#dealproduct_Modal">'.$productscnt.' Items</a>';
                                       } else {
                                        $projectcnt = ' - <a data-toggle="modal" data-target="#dealproduct_Modal">0 item</a>';
                                       }
                                        if($can_user_edit ==false){
                                            $projectcnt =' - '.$productscnt.' Items';
                                        }
                                       ?>
                                          <?php echo $project->name; ?> - <span style="font-size:12px;"><?php echo app_format_money($project->project_cost, $currency).$projectcnt; ?></span> 
										  <?php if(!empty($project->clientid)){?>
											  <p style="line-height: 8px; position: relative; top: 0px;"><?php echo $primarycont; ?>
											  <small><?php echo '<a href="'.(($project->approved ==1)?admin_url("clients/client/".$project->clientid):"#").'" style="" >'.$project->client_data->company.'</a>'; ?></small>
											  </p>
										  <?php }?>
                                        </div>
                                       <?php //echo '<div class="label mleft15 mtop8 p8 project-status-label-'.$project->status.'" style="background:'.$project_status['color'].'; position:relative; right:-16px;top:7px;">'.$project_status['name'].'</div>'; ?>
                                    </div>
                                    </div> 
                                 </div> 
                              </button>
                           </div>
                           
                        </div>
                        <div class="visible-xs">
                           <div class="clearfix"></div>
                        </div>
                        
                     </div>
                     <div class="col-md-5 text-right">
                     <?php echo (isset($teamleader)&&isset($teamleader->firstname))?($teamleader->firstname.' '.$teamleader->lastname.' &nbsp;'):''; ?>
                     
                     <?php
                     if(is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(),$ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader,$my_staffids) && !in_array($project->teamleader,$viewIds))) { ?>
                        <div class="btn-group">
                            <?php if($hasApprovalFlow && !$hasHIstory && $project->approved ==1 && $project->stage_of ==0): ?>
                            <a href="<?php echo admin_url('projects/sendtoapproval/'.$project->id); ?>" style="" class="btn btn-info"><?php echo _l('send_to_approval'); ?></a>
                            <?php endif; ?>
                            <?php if($deal_rejected && get_staff_user_id() == $project->created_by) { ?>
                                <a href="<?php echo admin_url('projects/approvalReopen/'.$project->id); ?>" style="" class="btn btn-info"><?php echo _l('approval_reopen'); ?></a>
                            <?php } ?>
                            <?php if($project->deleted_status == 1 && $project->approved ==1) { ?>
                                <a href="<?php echo admin_url('projects/restore_project/'.$project->id); ?>" style="" class="btn btn-info"><?php echo _l('restore'); ?></a>
                            <?php } else { ?>
							<?php if($project->stage_of == 0 && $project->approved ==1){ ?>
                            <?php if(!$hasApprovalFlow || $hasHIstory): ?>
							<button type="button" class="btn btn-success" onclick="ch_deal_s_to('1')">
								<?php echo _l('project-status-won'); ?>
							</button>
							<button type="button" class="btn btn-danger" onclick="ch_deal_s_to('2')">
								<?php echo _l('project-status-loss'); ?>
							</button>
                            <?php endif; ?>
							<?php }elseif($project->approved ==1){ ?>
							<span class="btn ">
							<span style="margin: 5px ; font-weight:bold;" class="label label-<?php echo ($project->stage_of == 1)?'success':'danger'; ?>"><?php echo _l('project-status-'.$project->stage_of); ?></span>
							</span>
							<button type="button" class="btn btn-default" onclick="ch_deal_s_to(0)">
								<?php echo _l('project-status-reopen'); ?>
							</button>
                            <?php } 
                            } ?>
                     </div>
                        <?php } ?>
                        <?php /* if(has_permission('tasks','','create')){ ?>
                        <a href="#" onclick="new_task_from_relation(undefined,'project',<?php echo $project->id; ?>); return false;" class="btn btn-info"><?php echo _l('new_task'); ?></a>
                        <?php } */?>
                        <?php
                        if($project->deleted_status == 0 && $project->approved ==1) {
                           $invoice_func = 'pre_invoice_project';
                           ?>
                        <?php if(has_permission('invoices','','create')){ ?>
                        <!-- <a href="#" onclick="<?php echo $invoice_func; ?>(<?php echo $project->id; ?>); return false;" class="invoice-project btn btn-info<?php if($project->client_data->active == 0){echo ' disabled';} ?>"><?php echo _l('invoice_project'); ?></a> -->
                        <?php } ?>
                        <?php
                           $project_pin_tooltip = _l('pin_project');
                           if(total_rows(db_prefix().'pinned_projects',array('staff_id'=>get_staff_user_id(),'project_id'=>$project->id)) > 0){
                             $project_pin_tooltip = _l('unpin_project');
                           }
                           ?>
                        <div class="btn-group">
                           <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           <?php echo _l('more'); ?> <span class="caret"></span>
                           </button>
                           <ul class="dropdown-menu dropdown-menu-right width200 project-actions">
                              <li>
                                 <a href="<?php echo admin_url('projects/pin_action/'.$project->id); ?>">
                                 <?php echo $project_pin_tooltip; ?>
                                 </a>
                              </li>
                              <?php if(has_permission('projects','','edit') && $project->lead_id > 0 && $project->deleted_status == 0){ ?>
                              <li>
                                 <a href="<?php echo admin_url('leads/convert_to_lead/'.$project->id); ?>">
                                    Convert to Lead
                                 </a>
                              </li>
                              <?php } ?>
                              <?php if(has_permission('projects','','create')){ ?>
                              <li>
                                 <a href="#" onclick="copy_project(); return false;">
                                 <?php echo _l('copy_project');?>
                                 </a>
                              </li>
                              <?php } ?>
                              <?php if(has_permission('projects','','create') || has_permission('projects','','edit')){ ?>
                              <!-- <li class="divider"></li> -->
                              <?php //foreach($statuses as $status){
                                 //if($status['id'] == $project->status){continue;}
                                 ?>
                              <!-- <li>
                                 <a href="#" data-name="<?php echo _l('project_status_'.$status['id']); ?>" onclick="project_mark_as_modal(<?php echo $status['id']; ?>,<?php echo $project->id; ?>, this); return false;"><?php echo _l('project_mark_as',$status['name']); ?></a>
                              </li> -->
                              <?php //} ?>
                              <?php } ?>
                              <li class="divider"></li>
                              <?php if(has_permission('projects','','create')){ ?>
                              <!-- <li>
                                 <a href="<?php echo admin_url('projects/export_project_data/'.$project->id); ?>" target="_blank"><?php echo _l('export_project_data'); ?></a>
                              </li> -->
                              <?php } ?>
                              <?php if(is_admin()){ ?>
                              <!-- <li>
                                 <a href="<?php echo admin_url('projects/view_project_as_client/'.$project->id .'/'.$project->clientid); ?>" target="_blank"><?php echo _l('project_view_as_client'); ?></a>
                              </li> -->
                              <?php } ?>
                              <?php if(has_permission('projects','','delete')){ 
                                  if(is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(),$ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader,$my_staffids) && !in_array($project->teamleader,$viewIds))) { ?>
                              <li>
                                 <a href="<?php echo admin_url('projects/delete/'.$project->id); ?>" class="_delete">
                                 <span class="text-danger"><?php echo _l('delete_project'); ?></span>
                                 </a>
                              </li>
                              <?php } } ?>
                           </ul>
                        </div>
                        <?php } ?>
                     </div>
                  </div>
               </div>
            </div>
            <?php if($project->approved ==1): ?>
            <div class="panel_s project-menu-panel">
               <div class="panel-body">
                  <?php hooks()->do_action('before_render_project_view', $project->id); ?>
                  <?php $this->load->view('admin/projects/project_tabs'); ?>
               </div>
            </div>
            <?php endif; ?>
            <?php
               if((has_permission('projects','','create') || has_permission('projects','','edit'))
                 && $project->status == 1
                 && $this->projects_model->timers_started_for_project($project->id)
                 && $tab['slug'] != 'project_milestones') {
               ?>
            <div class="alert alert-warning project-no-started-timers-found mbot15">
               <?php echo _l('project_not_started_status_tasks_timers_found'); ?>
            </div>
            <?php } ?>
            <?php
               if($project->deadline && date('Y-m-d') > $project->deadline
                && floor((abs(time() - strtotime($project->deadline)))/(60*60*24)) >= 9) {
               ?>
            <div class="alert alert-warning bold project-due-notice mbot15">
               <?php echo _l('project_due_notice', floor((abs(time() - strtotime($project->deadline)))/(60*60*24))); ?>
            </div>
            <?php } ?>
            <?php /*
               if(!has_contact_permission('projects',get_primary_contact_user_id($project->clientid))
                 && total_rows(db_prefix().'contacts',array('userid'=>$project->clientid)) > 0
                 && $tab['slug'] != 'project_milestones') {
               ?>
            <div class="alert alert-warning project-permissions-warning mbot15">
               <?php echo _l('project_customer_permission_warning'); ?>
            </div>
            <?php } */?>
            <div class="panel_s">
               <div class="panel-body">
                  <?php echo $this->load->view(($project->approved ==1 && $tab['view'] ? $tab['view'] : 'admin/projects/project_overview')); ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</div>
</div>
<?php 
if(isset($discussion)){
   echo form_hidden('discussion_id',$discussion->id);
   echo form_hidden('discussion_user_profile_image_url',$discussion_user_profile_image_url);
   echo form_hidden('current_user_is_admin',$current_user_is_admin);
   }
   echo form_hidden('project_percent',$percent);
   ?>
<div id="invoice_project"></div>
<div id="pre_invoice_project"></div>
<?php $this->load->view('admin/projects/milestone'); ?>
<?php $this->load->view('admin/projects/copy_settings'); ?>
<?php $this->load->view('admin/projects/_mark_tasks_finished'); ?>

<!-- Modal -->
<div class="modal fade" id="deallossreasons_Modal" tabindex="-1" role="dialog" aria-labelledby="deallossreasons_ModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="DealLossReasons_form" method='post' name="DealLossReasons_form" >
      <div class="modal-header">
        <h5 class="modal-title" id="deallossreasons_ModalLabel"><?php echo _l('DealLossReasons'); ?></h5>
      </div>
      <div class="modal-body">
            <?php
            $tm = array("id" => "", "name" => "Nothing Selected");
            array_unshift($all_deallossreasons, $tm);
            echo render_select('deallossreasons_id', $all_deallossreasons, array('id', 'name'), 'DealLossReasons', '', array('required'=>'required'));?>
            <?php echo render_textarea( 'lossremark', 'Remark',''); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"   >Save changes</button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>

<div class="modal fade" id="changeStage" tabindex="-1" role="dialog" aria-labelledby="changeStage_ModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="changeStageForm" method='post' >
      <div class="modal-header">
        <h5 class="modal-title" id="changeStage_ModalLabel">Change Pipeline and Stage</h5>
      </div>
      <div class="modal-body">
            <div class="col-md-12">
                <div class="form-group select-placeholder pipeselect">
                    <label for="status">Pipeline</label>
                    <div class="input-group">
                        <select id="pipeli_id" name="pipeline_id" data-live-search="true" class=" selectpicker">
                        <?php 
                        foreach($pipelines as $pikay => $pival){
                            $selected = '';
                            $pipeline_id = (isset($project) ? $project->pipeline_id : '');
                            if($pipeline_id == $pival['id']){
                                $selected = 'selected="selected"';
                            }
                            echo '<option value="'.$pival['id'].'" '.$selected.'>'.$pival['name'].'</option>';
                        }
                        ?>
                        </select>
                    </div>
                </div>

                <div class="form-group select-placeholder formstage">
                    <label for="status">Stage</label>
                    <div class="input-group">
                        <select id="stage_id" name="stage_id" data-live-search="true"  class="selectpicker" required>
                            <option></option>
                        <?php 
                        foreach($pipestage as $pikay => $pival){
                            $selected = '';
                            $stage_id = (isset($project) ? $project->status : '');
                            if($stage_id == $pival['id']){
                                $selected = 'selected="selected"';
                            }
                            echo '<option value="'.$pival['id'].'" '.$selected.'>'.$pival['name'].'</option>';
                        }
                        ?>
                        </select>
                    </div>
                </div>
            </div>
      </div>
      
      <div class="modal-footer" style="display:flow-root">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary savePipelineStage"   >Save changes</button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>

<div class="modal fade" id="dealproduct_Modal" tabindex="-1" role="dialog" aria-labelledby="dealproduct_ModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width:90%;">
    <div class="modal-content">
      <form id="Dealproduct_form" method='post' name="Dealproduct_form" action="<?php echo admin_url('projects/savedealproducts'); ?>">
      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
      <div class="modal-header">
        <h5 class="modal-title" id="deallossreasons_ModalLabel"><?php echo _l('AddItemsToDeal'); ?></h5>
      </div>
      <div class="modal-body" style="display:inline-block; width:100%;">
      <div class="col-md-12 row" style="padding-bottom:13px;">
        <div class="col-md-3" style="display:inline-flex">
          <span><?php echo _l('dealcurrency'); ?></span>
          <span style="padding-left:10px;">
          <select class="form-control currencyswitcher" id="currency" name="currency" style="padding:0px 6px; height:23px;">
          <?php
                        foreach($allcurrency as $ac) {
                            $selected = '';
                            if($currency == $ac['name']) {
                                $selected = 'selected';
                            }
                    ?>
                      <option value="<?php echo $ac['name']; ?>" <?php echo $selected;?> ><?php echo $ac['name']; ?></option>
                    <?php  } ?>
        </select></span>
        </div>
        <?php 
          $checked = '1';
          if(isset($dealproducts)) {
            foreach($dealproducts as $pr) {
              $checked = $pr['method'];
            }
          }
        ?>
        <?php if(isset($dealproducts) && !empty($dealproducts)) { ?>
                <input type="hidden" id="product_index" value="<?php echo $productscnt; ?>"> 
                <input type="hidden" name="method" id="method" value="<?php echo $checked; ?>">  
        <?php } else { ?>
                <input type="hidden" id="product_index" value="1"> 
                <input type="hidden" name="method" id="method" value="1">  
          <?php } ?>
        <input type="hidden" id="discount_value" value="<?php echo $discount_value; ?>"> 
        <input type="hidden" id="discount_option" value="<?php echo $discount_option; ?>"> 
        <input type="hidden" id="prject_id" name="project_id" value="<?php echo $project->id; ?>"> 
        <div class="col-md-2" style="display:inline-flex">
          <span><input type="radio" id="notax" name="tax" value="notax" <?php if(isset($checked) && $checked == '1') { ?> checked="checked" <?php } ?> ></span>
          <span style="padding-left:10px;"><?php echo _l('notax'); ?></span>
        </div>
        <div class="col-md-2" style="display:inline-flex">
          <span><input type="radio" id="intax" name="tax" value="intax" <?php if(isset($checked) && $checked == '2') { ?> checked="checked" <?php } ?> ></span>
          <span style="padding-left:10px;"><?php echo _l('inclusivetax'); ?></span>
        </div>
        <div class="col-md-2" style="display:inline-flex">
          <span><input type="radio" id="extax" name="tax" value="extax" <?php if(isset($checked) && $checked == '3') { ?> checked="checked" <?php } ?> ></span>
          <span style="padding-left:10px;"><?php echo _l('exclusivetax'); ?></span>
        </div>
      </div>
      <hr style="clear:both;">
      <div  class="css-table">
        <div style="height:40px;clear:both;" class="css-table-header" id="topheading">
        <?php echo get_particular_item_headers($pr['method'],$discount_option,$discount_value); ?>
        </div>

        <div class="field_product_wrapper row css-table-body">
              <?php if(isset($dealproducts) && !empty($dealproducts)) { ?>
               
                  <?php
                  $subtotal = 0.00;
                  $discount = '';
                  $tax_txt = '';
                  $tax_val = '';
                  $i = 1;
                    foreach($dealproducts as $pr) {
                      if($pr['method'] == 1) {
                  ?>
                    <div style="height:40px;clear:both;" class="productdiv css-table-row" id="<?php echo $i; ?>">
                        <div class="wcb">
                            <input type="hidden" name="no[]" value="<?php echo $i; ?>">
                            <input type="hidden" name="status_<?php echo $i; ?>" value="1" class="form-control cbox" <?php if($pr['status'] == 1){ echo 'checked'; } ?> >
                            <select name="product[]" class="form-control" onchange="getdealprodprice(this,<?php echo $i; ?>)" >
                                <option value="">--Select Item--</option>
                            <?php
                                foreach($products as $prod) {
                                  $selected = '';
                                  if($prod['id'] == $pr['productid']) {
                                    $selected = 'selected';
                                  }
                            ?>
                              <option value="<?php echo $prod['id']; ?>" <?php echo $selected; ?> ><?php echo $prod['name']; ?></option>
                            <?php  } ?>
                            </select>
                        </div>
                        <?php echo get_particulars_item_ordered_inputs($i,$pr['productid']) ?>
                        <div class="">
                            <input type="text" name="price[]" value="<?php echo $pr['price']; ?>" placeholder="Price" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,<?php echo $i; ?>)" class="form-control" /> 
                        </div>
                        <div class="">
                        <input type="number" name="qty[]" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" min="1" placeholder="Qty" value="<?php echo $pr['quantity']; ?>" onchange="qty_total(this,<?php echo $i; ?>)" class="form-control" /> 
                        </div>
                        <?php if($discount_value == 1 || $discount_option == 1) { ?>
                          <div class="">
                          <input type="number" name="discount[]"  min="0" placeholder="Discount" value="<?php echo $pr['discount']; ?>" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,<?php echo $i; ?>)" class="form-control" /> 
                          </div>
                        <?php } ?>
                        <div class="">
                        <input type="number" name="total[]" value="<?php echo $pr['total_price']; ?>" placeholder="Total" readonly class="form-control" /> 
                        </div>
                        <span class="dropdown open">
                          <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="true">...</button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                           <?php /* <li><a class="dropdown-item" href="#" onClick="gotoprod(<?php echo $i; ?>);">Go to Product</a></li>*/?>
                            <li><a class="dropdown-item" href="<?php echo base_url().'admin/invoice_items';?>" >Go to Items</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                            <?php /*<li><a class="dropdown-item" id="variationbtn_<?php echo $i; ?>" href="#" onClick="selectVariation(<?php echo $i; ?>);">Select Variation</a></li>*/?>
                          </ul>
                        </span>
                        <?php 
                        if($pr['variation']) { ?>
                          <div class="col-md-2" id="variation_<?php echo $i; ?>" style="width: 23.3%;margin: 4px 19px 15px;clear:both;">
                          <label>VARIATION</label>    
                          <select name="variation_<?php echo $i; ?>" class="form-control" onchange="getvariationprodprice(this,<?php echo $i; ?>)">
                              <option value="">--Select Variation--</option>
                            
                              <?php
                              $CI =& get_instance();
                              $vari = $CI->prodgetvaraiton($pr['productid'],$pr['currency']) ;
                              foreach($vari as $val) {
                                $selected = '';
                                if($val["id"] == $pr['variation']) {
                                  $selected = 'selected';
                                }
                                      echo '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
                              } 
                              ?>
                              </select>
                          </div>
                          
                        <?php
                        echo '<style>#variationbtn_'.$i.'{pointer-events: none; cursor: default;}</style>';
                        }
                        ?>
                    </div>
                    <?php 
                    
                    $i++; 
                      }
                      if($pr['method'] == 2 || $pr['method'] == 3) {
                    ?>
                      <div style="height:40px;clear:both;" class="productdiv css-table-row" id="<?php echo $i; ?>">
                        <div class="wcb" style="width:20%;">
                            <input type="hidden" name="no[]" value="<?php echo $i; ?>">
                            <input type="hidden" name="status_<?php echo $i; ?>" value="1" class="form-control cbox" <?php if($pr['status'] == 1){ echo 'checked'; } ?> >
                            <select name="product[]" class="form-control" onchange="getdealprodprice(this,<?php echo $i; ?>)" >
                                <option value="">--Select Item--</option>
                            <?php
                                foreach($products as $prod) {
                                  $selected = '';
                                  if($prod['id'] == $pr['productid']) {
                                    $selected = 'selected';
                                  }
                            ?>
                              <option value="<?php echo $prod['id']; ?>" <?php echo $selected; ?> ><?php echo $prod['name']; ?></option>
                            <?php  } ?>
                            </select>
                        </div>
                        <?php echo get_particulars_item_ordered_inputs($i,$pr['productid']) ?>
                        <div class="">
                            <input type="text" name="price[]" value="<?php echo $pr['price']; ?>" placeholder="Price" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,<?php echo $i; ?>)" class="form-control" /> 
                        </div>
                        <div class="">
                        <input type="number" name="qty[]"  min="1" placeholder="Qty" value="<?php echo $pr['quantity']; ?>" onchange="qty_total(this,<?php echo $i; ?>)" class="form-control" /> 
                        </div>
                        <div class="">
                        <input type="number" name="tax[]"  min="0" placeholder="Tax" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="<?php echo $pr['tax']; ?>" onchange="tax_total(this,<?php echo $i; ?>)" class="form-control" /> 
                        </div>
                        <?php if($discount_value == 1 || $discount_option == 1) { ?>
                          <div class="">
                          <input type="number" name="discount[]"  min="0" placeholder="Discount" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="<?php echo $pr['discount']; ?>" onchange="discount_total(this,<?php echo $i; ?>)" class="form-control" /> 
                          </div>
                        <?php } ?>
                        <div class="">
                        <input type="number" name="total[]" value="<?php echo $pr['total_price']; ?>" placeholder="Total" readonly class="form-control" /> 
                        </div>
                        <span class="dropdown open">
                          <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="true">...</button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                          <?php /*  <li><a class="dropdown-item" href="#" onClick="gotoprod(<?php echo $i; ?>);">Go to Product</a></li>*/?>
                            <li><a class="dropdown-item" href="<?php echo base_url().'admin/invoice_items';?>" >Go to Items</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                           <?php /* <li><a class="dropdown-item" id="variationbtn_<?php echo $i; ?>" href="#" onClick="selectVariation(<?php echo $i; ?>);">Select Variation</a></li>*/?>
                          </ul>
                        </span>
                        <?php 
                        if($pr['variation']) { ?>
                          <div class="" id="variation_<?php echo $i; ?>" style="width: 18.7%;margin: 4px 15px 15px;clear:both;">
                          <label>VARIATION</label>    
                          <select name="variation_<?php echo $i; ?>" class="form-control" onchange="getvariationprodprice(this,<?php echo $i; ?>)">
                              <option value="">--Select Variation--</option>
                            
                              <?php
                              $CI =& get_instance();
                              $vari = $CI->prodgetvaraiton($pr['productid'],$pr['currency']) ;
                              foreach($vari as $val) {
                                $selected = '';
                                if($val["id"] == $pr['variation']) {
                                  $selected = 'selected';
                                }
                                      echo '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
                              } 
                              ?>
                              </select>
                          </div>
                        <?php
                        echo '<style>#variationbtn_'.$i.'{pointer-events: none; cursor: default;}</style>';
                        }
                        ?>
                    </div>
                       
                      <?php 
                       if($pr['tax'] && $pr['tax'] > 0) {
                          $dec1 = ($pr['tax'] / 100); //its convert 10 into 0.10
                          $mult1 = $pr['total_price'] * $dec1; // gives the value for subtract from main value
                          if($pr['method'] == 2) {
                            $tax_txt .= '<p class="txt_'.$i.'"> Includes Tax ('.$pr['tax'].'%)</p>';
                          }
                          if($pr['method'] == 3) {
                            $tax_txt .= '<p class="txt_'.$i.'"> Excludes Tax ('.$pr['tax'].'%)</p>';
                          }
                          $tax_val .= '<p class="amt_'.$i.'"> '.number_format($mult1,2).'</p>';
                      }
                      
                      $i++; 
                      
                      } 
                      $subtotal = $subtotal+$pr['total_price'];
                      if($pr['discount'] && $pr['discount'] > 0) {
                        $dec = ($pr['discount'] / 100); //its convert 10 into 0.10
                        $mult = ($pr['price']*$pr['quantity']) * $dec; // gives the value for subtract from main value
                        $discount .= ' '.number_format($mult,2).',';
                      }

                     
                  } ?>
                 
              <?php  
              if($discount) {
                $discount = '<small>(Includes discount of '.substr($discount,0,-1).')</small>'; 
              }
            $proj_cost = $project->project_cost;
            } else { 
              ?>
                    <div style="height:40px;clear:both;" class="productdiv css-table-row" id="0">
                        <div class="wcb">
                          <input type="hidden" name="no[]" value="0">
                            <input type="hidden" name="status_0" value="1" class="form-control cbox">
                            <select name="product[]" class="form-control" onchange="getdealprodprice(this,0)">
                                <option value="">--Select Item--</option>
                            <?php
                                foreach($products as $prod) {
                            ?>
                              <option value="<?php echo $prod['id']; ?>" ><?php echo $prod['name']; ?></option>
                            <?php  } ?>
                            </select>
                        </div>
                        <?php echo get_particulars_item_ordered_inputs() ?>
                        <div class="">
                            <input type="text" name="price[]" value="" placeholder="Price"  step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,0)" class="form-control" /> 
                        </div>
                        <div class="">
                        <input type="number" name="qty[]"  min="1" placeholder="Qty" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="" onchange="qty_total(this,0)" class="form-control" /> 
                        </div>
                        <?php if($discount_value == 1 || $discount_option == 1) { ?>
                          <div class="">
                          <input type="number" name="discount[]"  min="0" placeholder="Discount" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="" onchange="discount_total(this,0)" class="form-control" /> 
                          </div>
                        <?php } ?>
                        <div class="">
                        <input type="number" name="total[]" value="" placeholder="Total" readonly class="form-control" /> 
                        </div>
                        <!-- <div class="col-md-1">
                        </div> -->
                        <span class="dropdown open">
                          <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="true">...</button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                           <?php /* <li><a class="dropdown-item" href="#" onClick="gotoprod(0);">Go to Product</a></li>*/?>
                            <li><a class="dropdown-item" href="<?php echo base_url().'admin/invoice_items';?>" >Go to Items</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                           <?php /* <li><a class="dropdown-item" id="variationbtn_0" href="#" onClick="selectVariation(0);">Select Variation</a></li>*/?>
                          </ul>
                        </span>
                    </div>
              <?php  
            $proj_cost = 0;
            } ?>
              </div>
              <a href="javascript:void(0);" class="editproduts_notax_btn row" title="Add field" style="position:relative; top:10px; left:15px; clear:both; float:left; height:40px;"><i class="fa fa-plus"></i> Add a new line</a>  
              <div class="css-table-row" id="particularsrowfooter">
                <div class="text-right" style="padding-top:30px">
                <span id="stxt"><p>Subtotal <?php echo $discount; ?></p></span><span id="suptotaltxt"><?php echo $tax_txt; ?></span><b>Total</b></div>
                <div class="text-right" style="padding-top:30px"><span id="stotal"><p><?php echo number_format($subtotal,2); ?></p></span><span id="suptotal"><?php echo $tax_val; ?></span><b><span id="grandtotal"><?php echo number_format($proj_cost,2); ?></span></b>
                </div>
              </div>
              <input type="hidden" name="grandtotal" id="gtot" value="<?php echo $proj_cost; ?>">
            </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"   >Save changes</button>
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
            <?php echo form_open('admin/clients/ajax_client',array('id'=>'clientid_add_group_modal1')); ?>
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
<div class="modal fade" id="project_contacts_modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('add_new',_l('contact')); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/form_contact/undefined',array('id'=>'project_contacts_add1')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo form_hidden('clientid',''); ?>
                        <?php $attrs = array('autofocus'=>true, 'required'=>true); ?>
                        <?php echo render_input( 'firstname', 'client_firstname','','',$attrs); ?>
                        <div id="contact_exists_info" class="hide"></div>
                        <?php echo render_input( 'title', 'contact_position',''); ?>
                        <?php echo render_input( 'email', 'client_email','', 'email'); ?>
                        <?php echo render_input( 'phonenumber', 'client_phonenumber','','text',array('autocomplete'=>'off')); ?>
                        
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
<div class="modal fade" id="play_record" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:340px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Play Recorded</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center">
                    <div id="playhtml">
                      
                    </div>
                  </div>
                  
              </div>
            </div>
            <div class="modal-footer">
                <button group="button" id="closeaudio" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="view_history" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Call History</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                    <div id="historyhtml">
                      
                    </div>
                  </div>
                  
              </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<?php /*
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>*/?>
<script>

</script>
<?php init_tail(); ?>
<!-- For invoices table -->

<script>
$(function(){
	appValidateForm($('#DealLossReasons_form'),{deallossreasons_id:'required'});
});
<?php if($_REQUEST['group']=='project_overview'){?>
tinymce.init({
        selector: 'textarea#description_new',
        height: 100,
        menubar: false,
        plugins: [
          'advlist autolink lists charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat'
      });
<?php }?>
</script>
<?php if($_REQUEST['group']=='' || $_REQUEST['group']=='project_tasks'){?>
	<style>
	.check_text{color:#fff !important}
	th.sorting{white-space:nowrap}
	.single_linet{white-space:nowrap}
		
	</style>
<?php }?>
<script>
   taskid = '<?php echo $this->input->get('taskid'); ?>';
</script>
<script>
   var gantt_data = {};
   <?php if(isset($gantt_data)){ ?>
   gantt_data = <?php echo json_encode($gantt_data); ?>;
   <?php } ?>
   var discussion_id = $('input[name="discussion_id"]').val();
   var discussion_user_profile_image_url = $('input[name="discussion_user_profile_image_url"]').val();
   var current_user_is_admin = $('input[name="current_user_is_admin"]').val();
   var project_id = $('input[name="project_id"]').val();
   if(typeof(discussion_id) != 'undefined'){
     discussion_comments('#discussion-comments',discussion_id,'regular');
   }
   $(function(){
    var project_progress_color = '<?php echo hooks()->apply_filters('admin_project_progress_color','#84c529'); ?>';
    var circle = $('.project-progress').circleProgress({fill: {
     gradient: [project_progress_color, project_progress_color]
   }}).on('circle-animation-progress', function(event, progress, stepValue) {
     $(this).find('strong.project-percent').html(parseInt(100 * stepValue) + '<i>%</i>');
   });
   });

   function discussion_comments(selector,discussion_id,discussion_type){
     var defaults = _get_jquery_comments_default_config(<?php echo json_encode(get_project_discussions_language_array()); ?>);
     var options = {
      currentUserIsAdmin:current_user_is_admin,
      getComments: function(success, error) {
        $.get(admin_url + 'projects/get_discussion_comments/'+discussion_id+'/'+discussion_type,function(response){
          success(response);
        },'json');
      },
      postComment: function(commentJSON, success, error) {
        $.ajax({
          type: 'post',
          url: admin_url + 'projects/add_discussion_comment/'+discussion_id+'/'+discussion_type,
          data: commentJSON,
          success: function(comment) {
            comment = JSON.parse(comment);
            success(comment)
          },
          error: error
        });
      },
      putComment: function(commentJSON, success, error) {
        $.ajax({
          type: 'post',
          url: admin_url + 'projects/update_discussion_comment',
          data: commentJSON,
          success: function(comment) {
            comment = JSON.parse(comment);
            success(comment)
          },
          error: error
        });
      },
      deleteComment: function(commentJSON, success, error) {
        $.ajax({
          type: 'post',
          url: admin_url + 'projects/delete_discussion_comment/'+commentJSON.id,
          success: success,
          error: error
        });
      },
      uploadAttachments: function(commentArray, success, error) {
        var responses = 0;
        var successfulUploads = [];
        var serverResponded = function() {
          responses++;
            // Check if all requests have finished
            if(responses == commentArray.length) {
                // Case: all failed
                if(successfulUploads.length == 0) {
                  error();
                // Case: some succeeded
              } else {
                successfulUploads = JSON.parse(successfulUploads);
                success(successfulUploads)
              }
            }
          }
          $(commentArray).each(function(index, commentJSON) {
            // Create form data
            var formData = new FormData();
            if(commentJSON.file.size && commentJSON.file.size > app.max_php_ini_upload_size_bytes){
             alert_float('danger',"<?php echo _l("file_exceeds_max_filesize"); ?>");
             serverResponded();
           } else {
            $(Object.keys(commentJSON)).each(function(index, key) {
              var value = commentJSON[key];
              if(value) formData.append(key, value);
            });

            if (typeof(csrfData) !== 'undefined') {
               formData.append(csrfData['token_name'], csrfData['hash']);
            }
            $.ajax({
              url: admin_url + 'projects/add_discussion_comment/'+discussion_id+'/'+discussion_type,
              type: 'POST',
              data: formData,
              cache: false,
              contentType: false,
              processData: false,
              success: function(commentJSON) {
                successfulUploads.push(commentJSON);
                serverResponded();
              },
              error: function(data) {
               var error = JSON.parse(data.responseText);
               alert_float('danger',error.message);
               serverResponded();
             },
           });
          }
        });
        }
      }
      var settings = $.extend({}, defaults, options);
    $(selector).comments(settings);
   }
   
$("#DealLossReasons_form").submit(function(e){
    e.preventDefault();
    ch_deal_s_to(2)
      return false;
  });

  
   
    function ch_deal_s_to(status){
        
       var data = {project_id:<?php echo ($project->id); ?>,status_id:status};
      if(status == 2){
         data.loss_reason = $('#deallossreasons_id').val();
         data.loss_remark = $('#lossremark').val();
         if(data.loss_reason == '' ||  data.loss_reason == 'undefined' ){
            $('#deallossreasons_Modal').modal('show');
             return false;
         }else{
            $('#deallossreasons_Modal').modal('hide');
         }
      }
		 $.ajax({
            type: 'POST',
            url: admin_url + 'projects/mark_as_won_loss_reopen',
            data: data,
            dataType: 'json',
            success: function(msg){
               alert_float('success', msg.message);
               location.reload();
            }
         });
    }
</script>
<?php if($_REQUEST['group']=='' || $_REQUEST['group']=='project_tasks'){?>
<script>

var originalLeave = $.fn.tooltip.Constructor.prototype.leave;
$.fn.tooltip.Constructor.prototype.leave = function(obj) {
  var self = obj instanceof this.constructor ?
    obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type)
  var container, timeout;

  originalLeave.call(this, obj);

  if (obj.currentTarget) {
    container = $(obj.currentTarget).siblings('.tooltip')
    timeout = self.timeout;
	$('.check_text').click(function(e) {  
	$.fn.tooltip.Constructor.prototype.leave.call(self, self);
    });
	container.one('click', function() {
		$("[data-toggle='tooltip']").tooltip('hide');
	$.fn.tooltip.Constructor.prototype.leave.call(self, self);
      container.one('mouseleave', function() {
        $.fn.tooltip.Constructor.prototype.leave.call(self, self);
		
      });
    });
	
    container.one('mouseenter', function() {
      //We entered the actual popover – call off the dogs
      clearTimeout(timeout);
      //Let's monitor popover content instead
      container.one('mouseleave', function() {
		  clearTimeout(timeout);
        $.fn.tooltip.Constructor.prototype.leave.call(self, self);
      });
    })
  }
};


$('body').tooltip({
  selector: '[data-toggle] , .tooltip',
  trigger: 'click hover',
  placement: 'auto',
  delay: {
    show: 50,
    hide: 400
  }
});
function copyToClipboard(element) {
	 
	var str = element.id
	var req_txt =  str.split('_');
	var str1 = req_txt[0].toLowerCase().replace(/\b[a-z]/g, function(letter) {
		return letter.toUpperCase();
	});
	var req_element = 'input_'+element.id;
	element = element.id;
	
	var copyText = document.getElementById(req_element);
	copyText.select();
	navigator.clipboard.writeText(copyText.value);
   alert_float('success', str1+' Copied Successfully');
   $("[data-toggle='tooltip']").tooltip('hide');
   setTimeout( function(){ 
    $("[data-toggle='tooltip']").tooltip('hide');
  }  , 500 );
  /* Alert the copied text */
}
  
  </script>
<?php }?>
  <script>
  $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );
  
  </script>
  
<script>
   
    window.addEventListener('load', function() {
      
        appValidateForm($('#clientid_add_group_modal1'), {
            company: 'required'
        }, manage_customer_groups);
    
    function manage_customer_groups(){
    
    }

    

    $('.savePipelineStage').click(function(e) {
        
        $(".savePipelineStage").attr("disabled", true);
            var stage = $('#stage_id').val();
            var pipeline = $('#pipeli_id').val();
            if(stage) {
        //   var data = {project_id:<?php echo ($project->id); ?>,f:$('.'+f+' data_edit').val()};
                var data = {project_id:<?php echo ($project->id); ?>,pipeline_id:pipeline,status:stage};
                $.ajax({
                    type: 'POST',
                    url: admin_url + 'projects/savepipelineAndstage',
                    data: data,
                    dataType: 'json',
                    success: function(msg){
                        if(msg.err) {
                            alert_float('warning', msg.err);
                        }
                        if(msg.message) {
                            alert_float('success', msg.message);
                        }
                        location.reload();
                    }
                });
            } else {
                alert('Please Select Stage.');
                return false;
            }
    });
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
    
        $('#clientid_add_group_modal1').on('show.bs.modal', function(e) {
            var invoker = $(e.relatedTarget);
            var group_id = $(invoker).data('id');
            $('#clientid_add_group_modal1 input[name="company"]').val('');
            // is from the edit button
            if (typeof(group_id) !== 'undefined') {
                $('#clientid_add_group_modal1 input[name="company"]').val($(invoker).parents('tr').find('td')
                    .eq(0).text());
            }
        });
    
    });
    
    $('#clientid_add_group_modal1').submit(function(e) {
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
                    $('#clientid_add_group_modal1 input[name="company"]').val('');
                    alert_float('success', msg.message);
                    $('.contactsdiv1 select').html('');
                    $('.primarydiv1 select').html('');
                    setTimeout(function() {
                        $('#clientid').selectpicker('refresh');
                        $('.clientiddiv div.filter-option-inner-inner').html(msg.company)
                        $('.contactsdiv1 select').selectpicker('refresh');
                        $('.primarydiv1 select').selectpicker('refresh');
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
                $('.contactsdiv1 select').html(msg.success);
                $('.primarydiv1 select').html('');
                setTimeout(function() {
                    $('.contactsdiv1 select').selectpicker('refresh');
                    $('.primarydiv1 select').selectpicker('refresh');
                }, 500);
            }
        });
    });
    
    
    
    window.addEventListener('load', function() {
        appValidateForm($('#project_contacts_add1'), {
            firstname: 'required'
        }, manage_project_contacts_add1);
    
        function manage_project_contacts_add1(form) {}
    
        $('#project_contacts_modal1').on('show.bs.modal', function(e) {
            var invoker = $(e.relatedTarget);
            var group_id = $(invoker).data('id');
            $('#project_contacts_add1 input[name="firstname"]').val('');
            $('#project_contacts_add1 input[name="email"]').val('');
            $('#project_contacts_add1 input[name="phonenumber"]').val('');
            // is from the edit button
            if (typeof(group_id) !== 'undefined') {
                $('#project_contacts_add1 input[name="firstname"]').val($(invoker).parents('tr').find('td')
                    .eq(0).text());
            }
        });
    
    });
    $('#project_contacts_add1').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var data = getFormData(form);
        data.clientid = $('#clientid').val();
        if (data.firstname) {
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: data,
                dataType: 'json',
                success: function(msg) {
                  //alert(msg.firstname);
                    $('.contactsdiv1 select').html(msg.firstname);
                    $('.primarydiv1 select').html(msg.firstname);
                    alert_float('success', msg.message);
                    setTimeout(function() {
                        $('.contactsdiv1 select').selectpicker('refresh');
                        $('.primarydiv1 select').selectpicker('refresh');
                    }, 500);
                    $('#project_contacts_modal1').modal('hide');
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
    
    $(".contactsdiv1 .selectpicker").change(function () {
        $('#primary_contact1').empty().append('<option value="">Nothing Selected</option>');
        $('#primary_contact1').selectpicker('refresh');
        var option_all = $(".contactsdiv1 .selectpicker option:selected").map(function () {
            $('#primary_contact1').append('<option value="' + $(this).val() + '">' + $(this).text() + '</option>');
        });
        $('#primary_contact1').selectpicker('refresh');
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
    
    
        $("#start_date1").on("change", function(e) {
            var obj = $("#deadline1");
            obj.datepicker('destroy').attr("data-date-min-date", $(this).val());
            init_datepicker(obj);
        });
        $("#deadline1").on("change", function(e) {
            var obj = $("#start_date1");
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
    
        
    
        $('select[name="status1"]').on('change', function() {
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
        function changeStage() {
            if ($('#pipeli_id').length > 0) {
                $('.pipeselect .selectpicker').addClass("formnewpipeline");
            }
            $('.formnewpipeline').selectpicker('destroy');
            $('.formnewpipeline').html('').selectpicker('refresh');
            
            if ($('#stage_id').length > 0) {
                $('.formstage .selectpicker').addClass("formnewstatus");
            }
            $('.formnewstatus').selectpicker('destroy');
            $('.formnewstatus').html('').selectpicker('refresh');
            var pipeid = <?php echo $project->pipeline_id; ?>;
            var status = <?php echo $project->status; ?>;
                $.ajax({
                    url: admin_url + 'pipeline/pickpipelineandstage',
                    type: 'POST',
                    data: {
                        'pipeline_id': pipeid,
                        'status': status
                    },
                    dataType: 'json',
                    success: function success(result) {
                        $('.formnewpipeline').selectpicker('destroy');
                        $('.formnewpipeline').html(result.pipelines).selectpicker('refresh');

                        $('.formnewstatus').selectpicker('destroy');
                        $('.formnewstatus').html(result.statuses).selectpicker('refresh');
                    // $('.formstage').html(result.statuses).selectpicker('refresh');
                        $('#changeStage').modal('show');
                    }
                });
        }
    $(function() {
        if ($('#stage_id').length > 0) {
            $('.formstage .selectpicker').addClass("formnewstatus");
        }
        if ($('.form_assigned1 .selectpicker').length > 0) {
            $('.form_assigned1 .selectpicker').addClass("formassigned1");
        }
    
        if ($('#teamleader1').length > 0) {
            $('.form_teamleader1 .selectpicker').addClass("formteamleader");
        }
    
        $('#pipeli_id').change(function() {
            $('.formnewstatus').selectpicker('destroy');
            $('.formnewstatus').html('').selectpicker('refresh');
            var pipeid = $('#pipeli_id').val();
            $.ajax({
                url: admin_url + 'leads/changepipeline',
                type: 'POST',
                data: {
                    'pipeline_id': pipeid
                },
                dataType: 'json',
                success: function success(result) {
                    $('.formnewstatus').selectpicker('destroy');
                    $('.formnewstatus').html(result.statuses).selectpicker('refresh');
                   // $('.formstage').html(result.statuses).selectpicker('refresh');
                }
            });
        });

        // if ($('#status').length > 0) {
        //     $('.formstatus').selectpicker('destroy');
        //     $('.formstatus').html('').selectpicker('refresh');
        // }
        
        if ($('#status1').length > 0) {
            $('.form_status .selectpicker').addClass("form_status1");
        }
    
        $('#pipeid').change(function() {
            $('.formstatus').selectpicker('destroy');
            $('.formstatus').html('').selectpicker('refresh');

            $('.form_status1').selectpicker('destroy');
            $('.form_status1').html('').selectpicker('refresh');
    
            // $('.formassigned1').selectpicker('destroy');
            // $('.formassigned1').html('').selectpicker('refresh');
    
            // $('.formteamleader').selectpicker('destroy');
            // $('.formteamleader').html('').selectpicker('refresh');
    
            var pipeid = $('#pipeid').val();
            $.ajax({
                url: admin_url + 'leads/changepipeline',
                type: 'POST',
                data: {
                    'pipeline_id': pipeid
                },
                dataType: 'json',
                success: function success(result) {
                    $('.formstatus').selectpicker('destroy');
                    $('.formstatus').html(result.statuses).selectpicker('refresh');
                    $('.form_status1').selectpicker('destroy');
                    $('.form_status1').html(result.statuses).selectpicker('refresh');
    
                }
            });
        });
    
        $('#teamleader1').change(function() {
            $('.formassigned1').selectpicker('destroy');
            $('.formassigned1').html('').selectpicker('refresh');
            var pipeid = $('#pipeid').val();
            var teamleader = $('#teamleader1').val();
            $.ajax({
                url: admin_url + 'leads/getpipelineteamember',
                type: 'POST',
                data: {
                    'leaderid': teamleader,
                    'pipeline': pipeid
                },
                dataType: 'json',
                success: function success(result) {
                    $('.formassigned1').selectpicker('destroy');
                    $('.formassigned1').html(result.teammembers).selectpicker('refresh');
                    $('#teamleader1-error').hide();
                }
            });
        });
        var pipelines_count = <?php echo count((array)$pipelines); ?>;
        if(pipelines_count == 1){
            $('#pipeid option[value="<?php echo $pipelines[0]['id']; ?>"]').attr('selected', 'selected')
            $('#pipeid').selectpicker('refresh');
            $('#pipeid').trigger('change');
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
    
    
        $("select[id^=project_contacts1]").change(function() {
            $('.contactsdiv p.text-danger').hide();
        });
    
        $("select[id^=project_members]").change(function() {
            $('.form_assigned1 p.text-danger').hide();
        });
    });
    </script>
	<?php if($_REQUEST['group']=='project_email'){?>
	<script>
 var BASE_URL = "<?php echo base_url(); ?>";
 window.onscroll = function() {myFunction()};

var header = document.getElementById("myHeader");
var sticky = header.offsetTop;

function sync_mail(){
	document.getElementById('overlay').style.display = ''; 
	$.ajax({
			url: BASE_URL+'admin/cronjob/store_local_mails',
			type: 'POST',
			data: { },
			success: function(data) {
					alert_float('success', 'Mail Fetched Successfully');
					location.reload();
					document.getElementById('overlay').style.display = 'none';
				}
			,
			error: function(data) {
				document.getElementById('overlay').style.display = 'none';
			}
		}
		);
	 
}

function myFunction() {
  if (window.pageYOffset > sticky) {
    header.classList.add("sticky");
  } else {
    header.classList.remove("sticky");
  }
}
function mget_file(c_id,c){
	 var fcnt = $('#'+c+'filecnt').val();
	 var tcnt = $('#'+c+'totcnt').val();
	 if(tcnt<=1){
		document.getElementById(c_id).click();
	 }else{
		 document.getElementById(c_id+'_'+tcnt).click();
	 }
 }
 function rm_file(a,b){
	 var c = $('#'+a+'_file').val() + b + ',';
	 $('#'+a+'_'+b+'_del').hide();
	 $('#'+a+'_file').val(c);
 }
 function get_up_val(c_id,c){
	 var fcnt = $('#'+c+'filecnt').val();
	 var tcnt = $('#'+c+'totcnt').val();
	 var allcnt = $('#'+c+'allcnt').val();
	 var req_tcnt = parseInt(tcnt) + parseInt(1);
	 var req_fcnt = parseInt(fcnt) + parseInt(1);
	 var req_cid = "'"+c_id+"'";
	 var req_c = "'"+c+"'";
 	 var req_id = "'"+c+"div_"+req_tcnt;
	  var file = $('#'+c_id);
	 if(tcnt!=1){
		 var file = $('#'+c_id+'_'+tcnt);
	 }
	 var fileName ='';
	 allcnt1 = parseInt(allcnt) + parseInt(file[0].files.length);
	 
	for(var i=0;i<file[0].files.length;i++){
		if(allcnt1 == file[0].files.length){
			var j = i;
		}
		else{
			var j = parseInt(allcnt) + i ;
		}
		var chr = "'"+c+"'";
		var c_no = "'"+j+"'";
		 fileName = fileName+'<div id="'+c+'_'+j+'_del" class="col-md-12" style="float:left;margin-top:5px;margin-top:5px;font-weight: 900;font-size: 15px;"><div class="col-md-9">'+ file[0].files[i].name + '</div><div class="col-md-3"><a href="javascript:void(0)"  onclick="rm_file('+chr+','+c_no+')" title="Delete"><i class="fa fa-trash fa-2x" id="" style="color:red"></i></a></div></div>';
	}
	$('.ch_files_'+c).append(fileName);
	 var req_file = '<div id="'+req_id+'"><input type="file" id="'+c_id+'_'+req_tcnt+'" style="display:none" name="attachment[]" multiple onchange="get_up_val('+req_cid+','+req_c+')"></div><br><br>';

	 $('#'+c+'_files').append(req_file);
	 $('#'+c+'filecnt').val(req_fcnt);
	 $('#'+c+'totcnt').val(req_tcnt);
	 $('#'+c+'allcnt').val(allcnt1);
	 
 }
 function check_all(a){
	$(".check_mail").prop('checked', false);
	if(a.checked == true){
		$(".check_mail").prop('checked', true);
	}
	check_header();
}
 function check_header(){
	
	 $('#myHeader').hide();
	 $("input:checkbox[class=check_mail]:checked").each(function () {
		$('#myHeader').show();
	});
	var a = $("input[type='checkbox'][class=check_mail]");
    if(a.filter(":checked").length!= a.length){
		$("#select_all").prop('checked', false);
	}
	else{
		$("#select_all").prop('checked', true);
	}
}
 function check_email(a,c_id){

	  var req_val = $('#'+c_id).val();
	  var newStr = req_val.substring(0, req_val.length - 1);
	  var check_str = newStr.substring(newStr.length-4);
	  var cur_val = a.substr(a.length - 1);
	  var e = event.keyCode;
	  if((check_str.includes(".com") || check_str.includes(".net") || check_str.includes(".in")) && (e!=8) && e!=188){
		  var req_out = newStr+','+ cur_val;
		   $('#'+c_id).val(req_out);
	  }
  }
function add_content(uid){
	document.getElementById('overlay_new').style.display = ''; 
	$.post(admin_url + 'projects/content',
	{
		uid:uid
	},
	function(data,status){
		var json = $.parseJSON(data);
		$('.ch_files_f').html('');
		$('#f_files').html('');
		$('#forward_toemail').val('');
		$('#forward_ccemail').val('');
		$('#forward_bccemail').val('');
		$('#ftotcnt').val(1);
		$('#ffilecnt').val(1);
		$('#fallcnt').val(0);
		$('#f_file').val('');
		$('#local_id').val(uid);
		check_email('','forward_toemail');
		$('#forward_subject').val('Fwd: '+json.subject);
		$('#forward_message').val(json.message_id);
		tinyMCE.get('forward_description').setContent(json.message);
		document.getElementById('overlay_new').style.display = 'none';  
		
	});
}
function add_to(uid){
	document.getElementById('overlay_new1').style.display = ''; 
	$.post(admin_url + 'projects/to_mail',
	{
		uid:uid
	},
	function(data,status){
		var json = $.parseJSON(data);
		$('#reply_message').val(json.message_id);
		$('#reply_toemail').val(json.from_address);
		$('#reply_subject').val('Re: '+json.subject); 
		$('.ch_files_r').html('');
		$('#r_files').html('');
		$('#reply_ccemail').val('');
		$('#reply_bccemail').val('');
		$('#ftotcnt').val(1);
		$('#rfilecnt').val(1);
		$('#rallcnt').val(0);
		$('#r_file').val('');
		$('#local_id').val(uid);
		tinyMCE.get('reply_description').setContent('');
		$('#r_getFile').val('');
		document.getElementById('overlay_new1').style.display = 'none'; 
		
	});
}
function add_reply_all(uid){
	document.getElementById('overlay_new1').style.display = '';
	$.post(admin_url + 'projects/add_reply_all',
	{
		uid:uid
	},
	function(data,status){
		var json = $.parseJSON(data);
		$('#reply_toemail').val(json.from_address);
		$('#reply_subject').val('Re: '+json.subject); 
		
		$('.ch_files_r').html('');
		$('#r_files').html('');
		$('#reply_ccemail').val('');
		$('#reply_bccemail').val('');
		$('#ftotcnt').val(1);
		$('#rfilecnt').val(1);
		$('#rallcnt').val(0);
		$('#r_file').val('');
		tinyMCE.get('reply_description').setContent('');
		$('#r_getFile').val('');		
		document.getElementById('overlay_new1').style.display = 'none';
		
	});
}
function getMessage(val){
	document.getElementById('overlay').style.display = '';
	$.post(admin_url + 'projects/getmessage',
	{
		uid:val
	},
	function(data,status){
		document.getElementById('overlay').style.display = 'none'; 
		$('#message-modal .modal-content').html(data);
			// show modal
		$('#message-modal').modal('show');
		
	});
}
function over_lay(cur_id){
	document.getElementById('overlay_'+cur_id).style.display = ''; 
	 $(".btn").prop('disabled', true);
}
function submit_default(){
	//$('#default_submit').prop('disabled', true);
	var default_temp = $("#ch_default_temp").val();
	$.post(admin_url + 'company_mail/change_default',
	{
		default_template: default_temp		
	},
	function(data,status){
		var json = $.parseJSON(data);
		if(json.status == 'success'){
			//tinyMCE.activeEditor.setContent(json.description);
			$(".tabs").removeClass("active");
			$(".tabs h6").removeClass("font-weight-bold");
			$(".tabs h6").addClass("text-muted");
			$("#tab01").children("h6").removeClass("text-muted");
			$("#tab01").children("h6").addClass("font-weight-bold");
			$("#tab01").addClass("active");

			current_fs = $(".active");

			next_fs = "#tab011";

			$("fieldset").removeClass("show");
			$(next_fs).addClass("show");
			var text = tinyMCE.get('description').getContent();
			var req_text = text+json.description
			tinyMCE.get('description').setContent(req_text);
			//tinyMCE.activeEditor.execCommand('mceInsertContent',false,json.description);
			//$('#default_submit').prop('disabled', false);
			//gettemplate_list();
			
		} 
	});
	if(default_temp == ''){
		var text = tinyMCE.get('description').getContent();
		tinyMCE.get('description').setContent(text);
	}
}
$(document).ready(function(){
	$("#message-modal").on("hidden.bs.modal", function(){
    $("#message_id").html("");
});
	$(".tabs").click(function(){

		$(".tabs").removeClass("active");
		$(".tabs h6").removeClass("font-weight-bold");
		$(".tabs h6").addClass("text-muted");
		$(this).children("h6").removeClass("text-muted");
		$(this).children("h6").addClass("font-weight-bold");
		$(this).addClass("active");

		current_fs = $(".active");

		next_fs = $(this).attr('id');
		next_fs = "#" + next_fs + "1";

		$("fieldset").removeClass("show");
		$(next_fs).addClass("show");

		current_fs.animate({}, {
			step: function() {
				current_fs.css({
				'display': 'none',
				'position': 'relative'
				});
				next_fs.css({
				'display': 'block'
				});
			}
		});
	});
	

});
function gettemplate_list(){
	$.post(admin_url + 'company_mail/template_list',
	{
	},
	function(data){
		var json = $.parseJSON(data);
		$("#template_list1").html(json.table);
		$("#template_header").html(json.header);
	});
}

</script>
   
<script type='text/javascript' >
function template_description(){
	var text = tinyMCE.get('template_description').getContent();
	$('#template_description').val(text.trim());
}
function template_edit_description(){
	var text = tinyMCE.get('template_edit_description').getContent();
	$('#template_edit_description').val(text.trim());
}
function tab_opon_popup(){
	$(".tabs").removeClass("active");
	$(".tabs h6").removeClass("font-weight-bold");
	$(".tabs h6").addClass("text-muted");
	$("#tab01").children("h6").removeClass("text-muted");
	$("#tab01").children("h6").addClass("font-weight-bold");
	$("#tab01").addClass("active");

	current_fs = $(".active");

	next_fs = "#tab011";
	$("fieldset").removeClass("show");
	$(next_fs).addClass("show");
	//$('#cur_draft_id').val('');
	$('.list_files').html('');
	$('#m_files').html('');
	//$('#toemail').val('');
	$('#toccemail').val('');
	$('#tobccemail').val('');
	$('#c_subject').val('');
	$('#mtotcnt').val(1);
	$('#mfilecnt').val(1);
	$('#mallcnt').val(0);
	$('#m_file').val('');
	check_email('','toemail');
	//tinyMCE.get('description').setContent('');
	$('#getFile').val('');
}
function save_draft(){
	var to = $('#toemail').val();
	var c_subject = $('#c_subject').val();
	var draft = $('#cur_draft_id').val();
	var text = tinyMCE.get('description').getContent();
	if((to!='' & text!='') || (to!='' & c_subject!='')){
		$.ajax({
			url: BASE_URL+'admin/company_mail/save_draft',
			type: 'POST',
			data: { 'to': to,'subject':c_subject,'text':text,'draft':draft },
			success: function(data) {
					$('#cur_draft_id').val(data);
				}

			}               
		);
	}
}
tinyMCE.remove();

tinymce.init({
        selector: 'textarea#template_description',
        height: 100,
		width:675,
        menubar: true,
        plugins: [
          'advlist autolink lists charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table paste code help wordcount','image code','link',
		  'emoticons template paste textcolor colorpicker textpattern imagetools','autosave'
        ],
        toolbar: 'fontselect fontsizeselect | forecolor backcolor | bold italic sizeselect | hr alignleft aligncenter alignright alignjustify | link image | link  | bullist numlist | restoredraft | code',
    fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
		 setup: function(ed) {  
                ed.on('blur', function(e) {  
                   // save_draft()  
				   template_description() 
                });  
            }  
      }); 
	  tinymce.init({
        selector: 'textarea#template_edit_description',
        height: 100,
		width:710,
        menubar: true,
        plugins: [
          'advlist autolink lists charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table paste code help wordcount','image code','link',
		  'emoticons template paste textcolor colorpicker textpattern imagetools','autosave'
        ],
        toolbar: 'fontselect fontsizeselect | forecolor backcolor | bold italic sizeselect | hr alignleft aligncenter alignright alignjustify | link image | link  | bullist numlist | restoredraft | code',
    fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
		 setup: function(ed) {  
                ed.on('blur', function(e) {  
                   // save_draft()  
				   template_edit_description() 
                });  
            }  
      }); 
tinymce.init({
        selector: 'textarea.tinymce',
        height: 100,
		width:690,
        menubar: true,
        plugins: [
          'advlist autolink lists charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table paste code help wordcount','image code','link',
		  'emoticons template paste textcolor colorpicker textpattern imagetools','autosave'
        ],
        toolbar: 'fontselect fontsizeselect | forecolor backcolor | bold italic sizeselect | hr alignleft aligncenter alignright alignjustify | link image | link  | bullist numlist | restoredraft | code',
    fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
		 setup: function(ed) {  
                ed.on('blur', function(e) {  
                   // save_draft()  
                });  
            }  
      }); 
	 tinymce.init({
        selector: 'textarea#reply_description',
        height: 100,
		width:690,
        menubar: true,
        plugins: [
          'advlist autolink lists charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table paste code help wordcount','image code','link',
		  'emoticons template paste textcolor colorpicker textpattern imagetools','autosave'
        ],
        toolbar: 'fontselect fontsizeselect | forecolor backcolor | bold italic sizeselect | hr alignleft aligncenter alignright alignjustify | link image | link  | bullist numlist | restoredraft | code',
    fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
		 setup: function(ed) {  
                ed.on('blur', function(e) {  
                   // save_draft()  
                });  
            }  
      }); 
tinymce.init({
        selector: 'textarea#description',
        height: 100,
		width:665,
        menubar: true,
        plugins: [
          'advlist autolink lists charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table paste code help wordcount','image code','link',
		  'emoticons template paste textcolor colorpicker textpattern imagetools','autosave'
        ],
        toolbar: 'fontselect fontsizeselect | forecolor backcolor | bold italic sizeselect | hr alignleft aligncenter alignright alignjustify | link image | link  | bullist numlist | restoredraft | code',
    fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
		 setup: function(ed) {  
                ed.on('blur', function(e) {  
                   // save_draft()  
                });  
            }  
      }); 
	  

	  var frm1 = $('#template_form');

    frm1.submit(function (e) {

        e.preventDefault();
		//$('form').preventDoubleSubmission();
		$('.error').hide();
        $.ajax({
            type: frm1.attr('method'),
            url: frm1.attr('action'),
            data: frm1.serialize(),
            success: function (data) {
				var json = $.parseJSON(data);
				if(json.status == 'success'){
					gettemplate_list();
					$(".tabs").removeClass("active");
					$(".tabs h6").removeClass("font-weight-bold");
					$(".tabs h6").addClass("text-muted");
					$("#tab02").children("h6").removeClass("text-muted");
					$("#tab02").children("h6").addClass("font-weight-bold");
					$("#tab02").addClass("active");

					current_fs = $(".active");

					next_fs = "#tab021";

					$("fieldset").removeClass("show");
					$(next_fs).addClass("show");
					alert_float('success', 'Template Created Successfully');
					//alert('Template Created Successfully');
				}
				else{
					if(json.name_error == 1){
						$('#name_error').show();
					}
					if(json.description_error == 1){
						$('#desc_error').show();
					}
				}
            },
            error: function (data) {
            },
        });
    });
	var frm2 = $('#edit_template_form');

    frm2.submit(function (e) {

        e.preventDefault();
		//$('form').preventDoubleSubmission();
		$('.error').hide();
        $.ajax({
            type: frm2.attr('method'),
            url: frm2.attr('action'),
            data: frm2.serialize(),
            success: function (data) {
				var json = $.parseJSON(data);
				if(json.status == 'success'){
					$('#Edit-template').modal('hide');
					gettemplate_list();
					alert_float('success', 'Template Updated Successfully');
				}
				else{
					if(json.name_error == 1){
						$('#name_edit_error').show();
					}
					if(json.description_error == 1){
						$('#desc_edit_error').show();
					}
				}
            },
            error: function (data) {
            },
        });
    });
	function reset_form(){
		$('#template_name').val('');
		tinyMCE.get('template_description').setContent('');
	}
	function edit_template(a){
		var BASE_URL = "<?php echo base_url();?>";
		$.ajax({
			url: BASE_URL+'admin/company_mail/edit_template',
			type: 'POST',
			data: { 'template_id': a },
			success: function(data) {
				var json = $.parseJSON(data);
				$('#template_edit_name').val(json.template_name);
				$('#template_id1').val(json.id);
				$('#template_edit_description').val(json.description);
				tinyMCE.get('template_edit_description').setContent(json.description);
			}

			}               
		);
	}
	function del_template(a){
		if (confirm('Are you want to delete this template')) {
			var BASE_URL = "<?php echo base_url();?>";
			$.ajax({
				url: BASE_URL+'admin/company_mail/delete_template',
				type: 'POST',
				data: { 'template_id': a },
				success: function(data) {
					$('.list_1'+a).hide();
					gettemplate_list();
					}

				}               
			);
		}
	}
	
$(document).ready(function() {
	$('#del_mail').click(function() {
	document.getElementById('overlay').style.display = '';
	var form= $("#formId");
	var BASE_URL = "<?php echo base_url();?>";
    $.ajax({
        url: BASE_URL+'admin/projects/trash',
        type: 'POST',
        data: form.serialize(),
        success: function(data) {
			//var results = JSON.parse(data);
			//if(results.length>0){
				location.reload();
			//}
			alert_float('success', 'Selected Mail Deleted Successfully');	
        }               
    });
});
	$('#pipeline_id').selectpicker('refresh');
    $( "#toemail" ).autocomplete({
 
        source: function(request, response) {
            var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL+'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
    },
	select: function( event, ui ) {
		var terms = split( $('#toemail').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		//$('#toemail').val(terms.join( ", " ));
		$('#toemail').val(terms);
		// Id
		var terms = split( $('#selectuser_ids').val() );
		
		terms.pop();
		var req_out = $('#toemail').val();
		req_out = ','+req_out;
		terms.push( ui.item.value );
		
		
		terms.push( "" );
		//$('#selectuser_ids').val(terms.join( ", " ));
		var trim = req_out.replace(/(^,)|(,$)/g, "");
		//$('#selectuser_ids').val(terms.join( ", " ));
		$('#toemail').val(trim);
		$('#selectuser_ids').val(trim);

		return false;
	},
    minLength: 3
 });
 
 $( "#toccemail" ).autocomplete({
 
        source: function(request, response) {
            var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL+'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
    },
	select: function( event, ui ) {
		var terms = split( $('#toccemail').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		//$('#toccemail').val(terms.join( ", " ));
		$('#toccemail').val(terms);
		// Id
		var terms = split( $('#selectuser_ids').val() );
		
		terms.pop();
		var req_out = $('#toccemail').val();
		req_out = ','+req_out;
		terms.push( ui.item.value );
		
		terms.push( "" );
		//$('#selectuser_ids').val(terms.join( ", " ));
		var trim = req_out.replace(/(^,)|(,$)/g, "");
		//$('#selectuser_ids').val(terms.join( ", " ));
		$('#toccemail').val(trim);
		$('#selectuser_ids').val(trim);

		return false;
	},
    minLength: 3
 });
 $( "#tobccemail" ).autocomplete({
 
        source: function(request, response) {
            var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL+'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
    },
	select: function( event, ui ) {
		var terms = split( $('#tobccemail').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		$('#tobccemail').val(terms);
		///$('#tobccemail').val(terms.join( ", " ));

		// Id
		var terms = split( $('#selectuser_ids').val() );
		
		terms.pop();
		var req_out = $('#tobccemail').val();
		req_out = ','+req_out;
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		//$('#selectuser_ids').val(terms.join( ", " ));
		var trim = req_out.replace(/(^,)|(,$)/g, "");
		//$('#selectuser_ids').val(terms.join( ", " ));
		$('#tobccemail').val(trim);
		$('#selectuser_ids').val(trim);

		return false;
	},
    minLength: 3
 });
 $( "#forward_toemail" ).autocomplete({
 
        source: function(request, response) {
            var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL+'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
    },
	select: function( event, ui ) {
		var terms = split( $('#forward_toemail').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		$('#forward_toemail').val(terms);
		//$('#forward_toemail').val(terms.join( ", " ));

		// Id
		var terms = split( $('#selectuser_ids').val() );
		
		terms.pop();
		var req_out = $('#forward_toemail').val();
		req_out = ','+req_out;
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		//$('#selectuser_ids').val(terms.join( ", " ));
		var trim = req_out.replace(/(^,)|(,$)/g, "");
		//$('#selectuser_ids').val(terms.join( ", " ));
		$('#forward_toemail').val(trim);
		$('#selectuser_ids').val(trim);

		return false;
	},
    minLength: 3
 });
 $( "#forward_ccemail" ).autocomplete({
 
        source: function(request, response) {
            var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL+'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
    },
	select: function( event, ui ) {
		var terms = split( $('#forward_ccemail').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		$('#forward_ccemail').val(terms);
		//$('#forward_ccemail').val(terms.join( ", " ));

		// Id
		var terms = split( $('#selectuser_ids').val() );
		
		terms.pop();
		var req_out = $('#forward_ccemail').val();
		req_out = ','+req_out;
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		//$('#selectuser_ids').val(terms.join( ", " ));
		var trim = req_out.replace(/(^,)|(,$)/g, "");
		//$('#selectuser_ids').val(terms.join( ", " ));
		$('#forward_ccemail').val(trim);
		$('#selectuser_ids').val(trim);

		return false;
	},
    minLength: 3
 });
 $( "#forward_bccemail" ).autocomplete({
 
        source: function(request, response) {
            var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL+'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
    },
	select: function( event, ui ) {
		var terms = split( $('#forward_bccemail').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		//$('#forward_bccemail').val(terms.join( ", " ));
		$('#forward_bccemail').val(terms);

		// Id
		var terms = split( $('#selectuser_ids').val() );
		
		terms.pop();
		var req_out = $('#forward_bccemail').val();
		req_out = ','+req_out;
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		//$('#selectuser_ids').val(terms.join( ", " ));
		var trim = req_out.replace(/(^,)|(,$)/g, "");
		//$('#selectuser_ids').val(terms.join( ", " ));
		$('#forward_bccemail').val(trim);
		$('#selectuser_ids').val(trim);

		return false;
	},
    minLength: 3
 });
 $( "#reply_ccemail" ).autocomplete({
 
        source: function(request, response) {
            var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL+'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
    },
	select: function( event, ui ) {
		var terms = split( $('#reply_ccemail').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		$('#reply_ccemail').val(terms);
		//$('#reply_ccemail').val(terms.join( ", " ));
		var req_out = $('#reply_ccemail').val();
		req_out = ','+req_out;

		// Id
		var terms = split( $('#selectuser_ids').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		//$('#selectuser_ids').val(terms.join( ", " ));
		var trim = req_out.replace(/(^,)|(,$)/g, "");
		//$('#selectuser_ids').val(terms.join( ", " ));
		$('#reply_ccemail').val(trim);
		$('#selectuser_ids').val(trim);

		return false;
	},
    minLength: 3
 });
 $( "#reply_bccemail" ).autocomplete({
 
        source: function(request, response) {
            var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL+'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
    },
	select: function( event, ui ) {
		var terms = split( $('#reply_bccemail').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		$('#reply_bccemail').val(terms);
		//$('#reply_bccemail').val(terms.join( ", " ));
		var req_out = $('#reply_bccemail').val();
		req_out = ','+req_out;

		// Id
		var terms = split( $('#selectuser_ids').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		//$('#selectuser_ids').val(terms.join( ", " ));
		var trim = req_out.replace(/(^,)|(,$)/g, "");
		//$('#selectuser_ids').val(terms.join( ", " ));
		$('#reply_bccemail').val(trim);
		$('#selectuser_ids').val(trim);

		return false;
	},
    minLength: 3
 });
}); 
    function split( val ) {
      return val.split( /,\s*/ );
    }
    function extractLast( term ) {
      return split( term ).pop();
    }

    </script> 
    <?php }?>
    
    <script>
$(document).ready(function(){
    $('.data_display_btn').click(function(e) {
    var f = $(this).attr("data-val");

    $('.'+f+' .data_display').hide();
    $('.'+f+' .data_edit').show();
    });
    $('.data_edit_btn').click(function(e) {
		var f = $(this).attr("data-val");
		var data = {project_id:<?php echo ($project->id); ?>,status:$('#status').val()};
		data['status'] = $('#status').val();
		data[f] =$('#'+f).val();
	   field_update(data,f);
    });
	function field_update(data,f){
		$.ajax({
			type: 'POST',
			url: admin_url + 'projects/dyfieldupdate',
			data: data,
			dataType: 'json',
			success: function(msg){
				if(msg.err) {
					alert_float('warning', msg.err);
				}
				if(msg.message) {
					alert_float('success', msg.message);
				}
				$('.'+f+' .data_edit').hide();
				$('.'+f+' .data_display .updated_text').html(msg.updated_text);
				$('.'+f+' .data_display').show();
				setTimeout(function() {
					location.reload();
				 }, 1500);
			}
		});
	}
	$('.data_edit_btn_custom').click(function(e) {
			var f = $(this).attr("data-val");
			var f_val = $('#'+f).val();
            var data = {project_id:<?php echo ($project->id); ?>,slug:f,f_val:f_val,custom_field:'2'};
			field_update(data,f);
    });
    $('.getcontactsbyorg').click(function(e) {
        //   var data = {project_id:<?php echo ($project->id); ?>,f:$('.'+f+' data_edit').val()};
            var data = {project_id:<?php echo ($project->id); ?>,clientid:$('#clid').val()};
            $.ajax({
                type: 'POST',
                url: admin_url + 'projects/getcontactsbyorg',
                data: data,
                dataType: 'json',
                success: function(msg){
                    $('.contactsdiv select').html(msg.contact);
                    $('.primarydiv select').html(msg.primarycontact);
                    setTimeout(function() {
                        $('.contactsdiv select').selectpicker('refresh');
                        $('.primarydiv select').selectpicker('refresh');
                    }, 1000);
                }
            });

    });
    $(".contactsdiv .selectpicker").change(function () {
        $('#primary_contact').empty().append('<option value="">Nothing Selected</option>');
        $('#primary_contact').selectpicker('refresh');
        var option_all = $(".contactsdiv .selectpicker option:selected").map(function () {
            $('#primary_contact').append('<option value="' + $(this).val() + '">' + $(this).text() + '</option>');
        });
        $('#primary_contact').selectpicker('refresh');
    });
    
<?php if(isset($project_overview_chart)){ ?>
   var project_overview_chart = <?php echo json_encode($project_overview_chart); ?>;
<?php } ?>


    $('#clientid_copy_project').on('change', function() {
        var clientId = this.value;
        var data = {clientId:clientId};
       
        $.ajax({
            type: 'POST',
            url: admin_url + 'projects/getContactpersonList',
            data: data,
            dataType: 'json',
            success: function(msg) {
                $('.contactsdiv1 select').html(msg.success);
                setTimeout(function() {
                    $('.contactsdiv1 select').selectpicker('refresh');
                }, 500);
            }
        });
    });
    
    window.addEventListener('load',function(){
       appValidateForm($('#clientid_add_group_modal'), {
        company: 'required'
    },manage_customer_groups);

    function manage_customer_groups(){

    }
    $('#clientid_add_group_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var group_id = $(invoker).data('id');
        $('#clientid_add_group_modal input[name="company"]').val('');
        // is from the edit button
        if (typeof(group_id) !== 'undefined') {
            $('#clientid_add_group_modal input[name="company"]').val($(invoker).parents('tr').find('td').eq(0).text());
        }
    });
      
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
                $('#clientid_copy_project').append('<option value="'+msg.id+'" selected="selected">'+msg.company+'</option>');
                $('#clientid_copy_project').val(msg.id);
                $('#clientid_add_group_modal input[name="company"]').val('');
                alert_float('success', msg.message);
                setTimeout(function(){  
                    $('#clientid_copy_project').selectpicker('refresh'); 
                    $('.clientiddiv div.filter-option-inner-inner').html(msg.company) 
                }, 500);
                $('#clientid_add_modal').modal('hide');
            }
            });
        }
    });


window.addEventListener('load',function(){
       appValidateForm($('#project_contacts_add'), {
        firstname: 'required'
    },manage_project_contacts_add);

function manage_project_contacts_add(form){
}
   $('#project_contacts_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var group_id = $(invoker).data('id');
        $('#project_contacts_add input[name="firstname"]').val('');
        $('#project_contacts_add input[name="email"]').val('');
        $('#project_contacts_add input[name="phonenumber"]').val('');
        // is from the edit button
        if (typeof(group_id) !== 'undefined') {
            $('#project_contacts_add input[name="firstname"]').val($(invoker).parents('tr').find('td').eq(0).text());
        }
    });
      
   });


   
   $('#project_contacts_add').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var data = getFormData(form);
        data.clientid =  $('#clid').val();
        if(data.firstname){
        $.ajax({
          type: form.attr('method'),
          url: form.attr('action'),
          data: data,
          dataType: 'json',
          success: function(msg){
            $('.team-contacts.project-overview-team-contacts').append(msg.card);
            alert_float('success', msg.message);
            
            $('#project_contacts_modal').modal('hide');
          }
        });
        }
    });

});

    function getFormData($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}

</script>
<style>
.single_linet {
    white-space: unset;
}
</style>

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
<script>
  function addFooterEmptyCell(){
    var headercount =$('#topheading > div').length;
    $('.footer-empty-cells').remove();
    for (let index = 0; index < headercount-2; index++) {
      $('#particularsrowfooter').prepend(`<div class="footer-empty-cells"></div>`);
    }
  }

  document.addEventListener("DOMContentLoaded", function(event) {
    addFooterEmptyCell();
  } );
</script>
</body>
</html>
