<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
        
            <div class="panel_s">
               <div class="panel-body">
                  <div class="_buttons">
                     <a href="#" onclick="init_lead(); return false;" class="btn mright5 btn-info pull-left display-block">
                     <?php echo _l('new_lead'); ?>
                     </a>
                     <button type="button" class="btn btn-default" data-toggle="modal" data-target="#leads_list_column_orderModal" style="float: right;">
                      <i class="fa fa-list" aria-hidden="true"></i>
                    </button>
                     <!-- <?php if(is_admin() || get_option('allow_non_admin_members_to_import_leads') == '1'){ ?>
                     <a href="<?php echo admin_url('leads/import'); ?>" class="btn btn-info pull-left display-block hidden-xs">
                     <?php echo _l('import_leads'); ?>
                     </a>
                     <?php } ?> -->
                     <!-- <div class="row">
                        <div class="col-md-5">
                           <a href="#" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" data-title="<?php echo _l('leads_summary'); ?>" data-placement="bottom" onclick="slideToggle('.leads-overview'); return false;"><i class="fa fa-bar-chart"></i></a>
                           <a href="<?php echo admin_url('leads/switch_kanban/'.$switch_kanban); ?>" class="btn btn-default mleft10 hidden-xs">
                           <?php if($switch_kanban == 1){ echo _l('leads_switch_to_kanban');}else{echo _l('switch_to_list_view');}; ?>
                           </a>
                        </div>
                        <div class="col-md-4 col-xs-12 pull-right leads-search">
                           <?php if($this->session->userdata('leads_kanban_view') == 'true') { ?>
                           <div data-toggle="tooltip" data-placement="bottom" data-title="<?php echo _l('search_by_tags'); ?>">
                              <?php echo render_input('search','','','search',array('data-name'=>'search','onkeyup'=>'leads_kanban();','placeholder'=>_l('leads_search')),array(),'no-margin') ?>
                           </div>
                           <?php } ?>
                           <?php echo form_hidden('sort_type'); ?>
                           <?php echo form_hidden('sort',(get_option('default_leads_kanban_sort') != '' ? get_option('default_leads_kanban_sort_type') : '')); ?>
                        </div>
                     </div> -->
                     <div class="clearfix"></div>
                     <div class="row hide leads-overview">
                        <hr class="hr-panel-heading" />
                        <div class="col-md-12">
                           <h4 class="no-margin"><?php echo _l('leads_summary'); ?></h4>
                        </div>
                        <?php
                           foreach($summary as $status) { ?>
                              <div class="col-md-2 col-xs-6 border-right">
                                    <h3 class="bold">
                                       <?php
                                          if(isset($status['percent'])) {
                                             echo '<span data-toggle="tooltip" data-title="'.$status['total'].'">'.$status['percent'].'%</span>';
                                          } else {
                                             // Is regular status
                                             echo $status['total'];
                                          }
                                       ?>
                                    </h3>
                                   <span style="color:<?php echo $status['color']; ?>" class="<?php echo isset($status['junk']) || isset($status['lost']) ? 'text-danger' : ''; ?>"><?php echo $status['name']; ?></span>
                              </div>
                           <?php } ?>
                     </div>
                  </div>
                  <hr class="hr-panel-heading" />
                  <div class="tab-content">
                     <?php
                        if($this->session->has_userdata('leads_kanban_view') && $this->session->userdata('leads_kanban_view') == 'true') { ?>
                     <div class="active kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                        <div class="kanban-leads-sort">
                           <span class="bold"><?php echo _l('leads_sort_by'); ?>: </span>
                           <a href="#" onclick="leads_kanban_sort('dateadded'); return false" class="dateadded">
                           <?php if(get_option('default_leads_kanban_sort') == 'dateadded'){echo '<i class="kanban-sort-icon fa fa-sort-amount-'.strtolower(get_option('default_leads_kanban_sort_type')).'"></i> ';} ?><?php echo _l('leads_sort_by_datecreated'); ?>
                           </a>
                           |
                           <a href="#" onclick="leads_kanban_sort('leadorder');return false;" class="leadorder">
                           <?php if(get_option('default_leads_kanban_sort') == 'leadorder'){echo '<i class="kanban-sort-icon fa fa-sort-amount-'.strtolower(get_option('default_leads_kanban_sort_type')).'"></i> ';} ?><?php echo _l('leads_sort_by_kanban_order'); ?>
                           </a>
                           |
                           <a href="#" onclick="leads_kanban_sort('lastcontact');return false;" class="lastcontact">
                           <?php if(get_option('default_leads_kanban_sort') == 'lastcontact'){echo '<i class="kanban-sort-icon fa fa-sort-amount-'.strtolower(get_option('default_leads_kanban_sort_type')).'"></i> ';} ?><?php echo _l('leads_sort_by_lastcontact'); ?>
                           </a>
                        </div>
                        <div class="row">
                           <div class="container-fluid leads-kan-ban">
                              <div id="kan-ban"></div>
                           </div>
                        </div>
                     </div>
                     <?php } else { ?>
                     <div class="row" id="leads-table">
                        <div class="col-md-12">
                           <div class="row">
                              <div class="col-md-12">
                                 <p class="bold"><?php echo _l('filter_by'); ?></p>
                              </div>
                              <?php if(has_permission('leads','','view')){ ?>
                              <div class="col-md-3 leads-filter-column">
                                 <?php echo render_select('view_assigned',$staff,array('staffid',array('firstname','lastname')),'','',array('data-width'=>'100%','data-none-selected-text'=>_l('leads_dt_assigned'),'title'=>_l('leads_dt_assigned')),array(),'no-mbot'); ?>
                              </div>
                              <?php } ?>
                              <!-- <div class="col-md-3 leads-filter-column">
                                 <?php
                                    $selected = array();
                                    if($this->input->get('status')) {
                                     $selected[] = $this->input->get('status');
                                    } else {
                                     foreach($statuses as $key => $status) {
                                       if($status['isdefault'] == 0) {
                                         $selected[] = $status['id'];
                                       } else {
                                         $statuses[$key]['option_attributes'] = array('data-subtext'=>_l('leads_converted_to_client'));
                                       }
                                     }
                                    }
                                    echo '<div id="leads-filter-status">';
                                    echo render_select('view_status[]',$statuses,array('id','name'),'',$selected,array('data-width'=>'100%','data-none-selected-text'=>_l('leads_all'),'multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);
                                    echo '</div>';
                                    ?>
                              </div> -->
                              <div class="col-md-3 leads-filter-column">
                                 <?php
                                    echo render_select('view_source',$sources,array('id','name'),'','',array('data-width'=>'100%','data-none-selected-text'=>_l('Source'),'title'=>_l('Source')),array(),'no-mbot');
                                    ?>
                              </div>
                              <div class="col-md-3 leads-filter-column">
                                 <div class="select-placeholder">
                                    <select name="custom_view" title="<?php echo _l('additional_filters'); ?>" id="custom_view" class="selectpicker" data-width="100%">
                                       <option value="">Nothing Selected</option>
                                       <option value="lost"><?php echo _l('lead_lost'); ?></option>
                                       <option value="junk"><?php echo _l('lead_junk'); ?></option>
                                       <!-- <option value="public"><?php echo _l('lead_public'); ?></option>
                                       <option value="contacted_today"><?php echo _l('lead_add_edit_contacted_today'); ?></option> -->
                                       <option value="created_today"><?php echo _l('created_today'); ?></option>
                                       <?php if(has_permission('leads','','edit')){ ?>
                                       <option value="not_assigned"><?php echo _l('leads_not_assigned'); ?></option>
                                       <?php } ?>
                                       <?php if(isset($consent_purposes)) { ?>
                                         <optgroup label="<?php echo _l('gdpr_consent'); ?>">
                                             <?php foreach($consent_purposes as $purpose) { ?>
                                             <option value="consent_<?php echo $purpose['id']; ?>">
                                                <?php echo $purpose['name']; ?>
                                             </option>
                                             <?php } ?>
                                         </optgroup>
                                       <?php } ?>
                                    </select>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="col-md-12">
                           <a href="#" data-toggle="modal" data-table=".table-leads" data-target="#leads_bulk_actions" class="hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>
                           <div class="modal fade bulk_actions" id="leads_bulk_actions" tabindex="-1" role="dialog">
                              <div class="modal-dialog" role="document">
                                 <div class="modal-content">
                                    <div class="modal-header">
                                       <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                       <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                       <?php if(has_permission('leads','','delete')){ ?>
                                       <div class="checkbox checkbox-danger">
                                          <input type="checkbox" name="mass_delete" id="mass_delete" onchange="lead_bulkaction_confirm();">
                                          <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                       </div>
                                       <hr class="mass_delete_separator" />
                                       <?php } ?>
                                       <div id="bulk_change">
                                       <div class="form-group">
                                              <div class="checkbox checkbox-primary checkbox-inline">
                                                <input type="checkbox" name="leads_bulk_mark_lost" id="leads_bulk_mark_lost" value="1" onchange="lead_bulkaction_confirm();">
                                                <label for="leads_bulk_mark_lost">
                                                <?php echo _l('lead_mark_as_lost'); ?>
                                                </label>
                                             </div>
                                         </div>
                                          <?php //echo render_select('move_to_status_leads_bulk',$statuses,array('id','name'),'ticket_single_change_status'); ?>
                                          <?php
                                             echo render_select('move_to_source_leads_bulk',$sources,array('id','name'),'lead_source','',array('onchange'=>"lead_bulkaction_confirm();"));
                                             //echo render_datetime_input('leads_bulk_last_contact','leads_dt_last_contact');
                                             //if(has_permission('leads','','edit')){
                                               echo render_select('assign_to_leads_bulk',$staff,array('staffid',array('firstname','lastname')),'leads_dt_assigned','',array('onchange'=>"lead_bulkaction_confirm();"));
                                             //}
                                             ?>
                                          <div class="form-group">
                                             <?php echo '<p><b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b></p>'; ?>
                                             <input type="text" class="tagsinput" id="tags_bulk" name="tags_bulk" value="" data-role="tagsinput" onchange="lead_bulkaction_confirm();">
                                          </div>
                                          <!-- <hr />
                                          <div class="form-group no-mbot">
                                             <div class="radio radio-primary radio-inline">
                                                <input type="radio" name="leads_bulk_visibility" id="leads_bulk_public" value="public">
                                                <label for="leads_bulk_public">
                                                <?php echo _l('lead_public'); ?>
                                                </label>
                                             </div>
                                             <div class="radio radio-primary radio-inline">
                                                <input type="radio" name="leads_bulk_visibility" id="leads_bulk_private" value="private">
                                                <label for="leads_bulk_private">
                                                <?php echo _l('private'); ?>
                                                </label>
                                             </div>
                                          </div> -->
                                       </div>
                                    </div>
                                    <div class="modal-footer">
                                       <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                                       <a href="#" class="btn btn-info disabled" onclick="leads_bulk_action(this); return false;" id="lead_bulk_action_confirm"><?php echo _l('confirm'); ?></a>
                                    </div>
                                 </div>
                                 <!-- /.modal-content -->
                              </div>
                              <!-- /.modal-dialog -->
                           </div>
                           <!-- /.modal -->
                            <!-- Modal -->
                            <div class="modal fade" id="leads_list_column_orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <?php echo form_open_multipart(admin_url('settings/leads_list_column'),array('id'=>'leads_list_column')); ?>
                                <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Leads List Columns</h5>
                                </div>
                                <div class="modal-body">
                                <div class="form-group">

                            <?php $colarr = array(
                                                  "name"=>array("ins"=>"name","ll"=>"leads_dt_name"),
                                                  "company"=>array("ins"=>"company","ll"=>"lead_company"),
                                                  "email"=>array("ins"=>"email","ll"=>"leads_dt_email"),
                                                  "phonenumber"=>array("ins"=>"phonenumber","ll"=>"leads_dt_phonenumber"),
                                                  "country"=>array("ins"=>"country","ll"=>"lead_country"),
                                                  "state"=>array("ins"=>"state","ll"=>"lead_state"),
                                                  "city"=>array("ins"=>"city","ll"=>"lead_city"),
                                                  "assigned_firstname"=>array("ins"=>"assigned_firstname","ll"=>"leads_dt_assigned"),
                                                  "source_name"=>array("ins"=>"source_name","ll"=>"leads_source"),
                                                  "dateadded"=>array("ins"=>"dateadded","ll"=>"leads_dt_datecreated")
                                                ); 
                            $cf = get_custom_fields('leads');
                            foreach($cf as $custom_field) {
                              
                              $cur_arr = array('ins'=>$custom_field['slug'],'ll'=>$custom_field['name']);
                            $colarr[$custom_field['slug']] = $cur_arr;
                              //array_push($colarr,$cur_arr);
                            }
                            $req_columns = array();

                            ?>  
                              <ul id="sortable">
                              <?php $targets_list_column_order = (array)json_decode(get_option('leads_list_column')); $i = 0;
                              $req_table = array();
                              //print_r($targets_list_column_order);exit; ?>
                              <?php foreach($targets_list_column_order as $ckey=>$cval){
                                ?>
                                  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                    <input type="checkbox" name="settings[leads_list_column][<?php echo $ckey; ?>]" value="1" checked="checked" /> <?php echo _l($colarr[$ckey]['ll']); ?>
                                  </li>
                              <?php 
                                  $req_table[] = _l($colarr[$ckey]['ll']);
                              } ?>
                              <?php foreach($colarr as $ckey=>$cval){ 
                                if(!isset($targets_list_column_order[$ckey])){?>
                                  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                    <input type="checkbox" name="settings[leads_list_column][<?php echo $ckey; ?>]" value="1"/> <?php echo _l($cval['ll']); ?>
                                  </li>
                              <?php }
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
                           <?php
                           $table_data = [];
                           $table_data = array_merge($table_data, $req_table);
                           array_unshift($table_data, [
                              'name'     => '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="leads"><label></label></div>',
                              'th_attrs' => ['class' => (isset($bulk_actions) ? '' : 'not_visible')],
                          ]);
//pre($table_data);
                           render_datatable($table_data,'leads');  

                          //  render_datatable($table_data,'leads',
                          //  array('customizable-table'),
                          //  array(
                          //   'id'=>'table-leads',
                          //   'data-last-order-identifier'=>'leads',
                          //   'data-default-order'=>get_table_last_order('leads'),
                          //   ));

                            //   $table_data = array();
                            //   $_table_data = array(
                            //     '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="leads"><label></label></div>',
                            //     array(
                            //      'name'=>_l('the_number_sign'),
                            //      'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-number')
                            //    ),
                            //     array(
                            //      'name'=>_l('leads_dt_name'),
                            //      'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-name')
                            //    ),
                            //   );
                            //   if(is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1') {
                            //     $_table_data[] = array(
                            //         'name'=>_l('gdpr_consent') .' ('._l('gdpr_short').')',
                            //         'th_attrs'=>array('id'=>'th-consent', 'class'=>'not-export')
                            //      );
                            //   }
                            //   $_table_data[] = array(
                            //    'name'=>_l('lead_company'),
                            //    'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-company')
                            //   );
                            //   $_table_data[] =   array(
                            //    'name'=>_l('leads_dt_email'),
                            //    'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-email')
                            //   );
                            //   $_table_data[] =  array(
                            //    'name'=>_l('leads_dt_phonenumber'),
                            //    'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-phone')
                            //   );

                            //   $_table_data[] =  array(
                            //    'name'=>_l('tags'),
                            //    'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-tags')
                            //   );
                            //   $_table_data[] = array(
                            //    'name'=>_l('leads_dt_assigned'),
                            //    'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-assigned')
                            //   );
                            // //   $_table_data[] = array(
                            // //    'name'=>_l('leads_dt_status'),
                            // //    'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-status')
                            // //   );
                            // //   $_table_data[] = array(
                            // //    'name'=>_l('leads_source'),
                            // //    'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-source')
                            // //   );
                            //   $_table_data[] = array(
                            //    'name'=>_l('leads_dt_last_contact'),
                            //    'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-last-contact')
                            //   );
                            //   $_table_data[] = array(
                            //     'name'=>_l('leads_dt_datecreated'),
                            //     'th_attrs'=>array('class'=>'date-created toggleable','id'=>'th-date-created')
                            //   );
                            //   foreach($_table_data as $_t){
                            //    array_push($table_data,$_t);
                            //   }
                            //   $custom_fields = get_custom_fields('leads',array('show_on_table'=>1));
                            //   foreach($custom_fields as $field){
                            //   array_push($table_data,$field['name']);
                            //   }
                            //   $table_data = hooks()->apply_filters('leads_table_columns', $table_data);
                            //   render_datatable($table_data,'leads',
                            //   array('customizable-table'),
                            //   array(
                            //    'id'=>'table-leads',
                            //    'data-last-order-identifier'=>'leads',
                            //    'data-default-order'=>get_table_last_order('leads'),
                            //    )); ?>
                        </div>
                     </div>
                     <?php } ?>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script id="hidden-columns-table-leads" type="text/json">
   <?php echo get_staff_meta(get_staff_user_id(), 'hidden-columns-table-leads'); ?>
</script>
<?php include_once(APPPATH.'views/admin/leads/status.php'); ?>

<?php init_tail(); ?>
<script>
   var openLeadID = '<?php echo $leadid; ?>';
   $(function(){
      leads_kanban();
      $('#leads_bulk_mark_lost').on('change', function(){
          $('#move_to_status_leads_bulk').prop('disabled', $(this).prop('checked') == true);
          $('#move_to_status_leads_bulk').selectpicker('refresh')
       });
      $('#move_to_status_leads_bulk').on('change', function(){
        if($(this).selectpicker('val') != '') {
         $('#leads_bulk_mark_lost').prop('disabled', true);
         $('#leads_bulk_mark_lost').prop('checked', false);
      } else {
         $('#leads_bulk_mark_lost').prop('disabled', false);
      }
   });
   
   });
   $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );

  function lead_bulkaction_confirm(){
   var disabled =true;
   if($('#mass_delete').is(':checked') == true){
      disabled =false;
   }
   if($('#leads_bulk_mark_lost').is(':checked') == true){
      disabled =false;
   }
   if($('#move_to_source_leads_bulk').val()>0){
      disabled =false;
   }
   if($('#assign_to_leads_bulk').val() >0){
      disabled =false;
   }
   if($('#tags_bulk').val().length >0){
      disabled =false;
   }
   if(disabled ==true){
      $("#lead_bulk_action_confirm").addClass('disabled');
      
   }else{
      $("#lead_bulk_action_confirm").removeClass('disabled');
   }
   
  }
</script>

</body>
</html>
