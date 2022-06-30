<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="_filters _hidden_inputs hidden">
               <?php
                  echo form_hidden('my_customers');
                  echo form_hidden('requires_registration_confirmation');
                  echo form_hidden('alphabet_All');
                  foreach(range('A', 'Z') as $letter) {
                    echo form_hidden('alphabet_'.$letter);
                  }
                  foreach($groups as $group){
                     echo form_hidden('customer_group_'.$group['id']);
                  }
                  foreach($contract_types as $type){
                     echo form_hidden('contract_type_'.$type['id']);
                  }
                  foreach($invoice_statuses as $status){
                     echo form_hidden('invoices_'.$status);
                  }
                  foreach($estimate_statuses as $status){
                     echo form_hidden('estimates_'.$status);
                  }
                  foreach($project_statuses as $status){
                  echo form_hidden('projects_'.$status['id']);
                  }
                  foreach($proposal_statuses as $status){
                  echo form_hidden('proposals_'.$status);
                  }
                  foreach($customer_admins as $cadmin){
                  echo form_hidden('responsible_admin_'.$cadmin['staff_id']);
                  }
                  foreach($countries as $country){
                  echo form_hidden('country_'.$country['country_id']);
                  }
                  ?>
            </div>
            <div class="panel_s">
               <div class="panel-body">
                  <div class="_buttons">
                     <?php if (has_permission('customers','','create')) { ?>
                     <a href="<?php echo admin_url('products/product'); ?>" class="btn btn-info mright5 test pull-left display-block">
                     <?php echo _l('new_product'); ?></a>
                     <!-- <a href="<?php echo admin_url('products/import'); ?>" class="btn btn-info pull-left display-block mright5 hidden-xs">
                     <?php echo _l('import_products'); ?></a> -->
                     <?php } ?>
                     
                     

                                  

<div class="btn-group pull-right mleft4 mbot25 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('clients_list_column'); ?>">
       <!-- Button trigger modal -->



</div>


  
                    
                     <div class="visible-xs">
                        <div class="clearfix"></div>
                     </div>
                     <div class="btn-group pull-right btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                        
                        <ul class="dropdown-menu dropdown-menu-left" style="width:300px;">
                           <li class="active"><a href="#" data-cview="all" onclick="dt_custom_view('','.table-clients',''); return false;"><?php echo _l('customers_sort_all'); ?></a>
                           </li>
                           <?php if(get_option('customer_requires_registration_confirmation') == '1' || total_rows(db_prefix().'clients','registration_confirmed=0') > 0) { ?>
                           <li class="divider"></li>
                           <li>
                              <a href="#" data-cview="requires_registration_confirmation" onclick="dt_custom_view('requires_registration_confirmation','.table-clients','requires_registration_confirmation'); return false;">
                              <?php echo _l('customer_requires_registration_confirmation'); ?>
                              </a>
                           </li>
                           <?php } ?>
                           <!-- <li class="divider"></li> -->
                           <!-- <li>
                              <a href="#" data-cview="my_customers" onclick="dt_custom_view('my_customers','.table-clients','my_customers'); return false;">
                              <?php echo _l('customers_assigned_to_me'); ?>
                              </a>
                           </li> -->
                           <!-- <li class="divider"></li> -->
                           <!-- <?php if(count($groups) > 0){ ?>
                           <li class="dropdown-submenu pull-left groups">
                              <a href="#" tabindex="-1"><?php echo _l('customer_groups'); ?></a>
                              <ul class="dropdown-menu dropdown-menu-left">
                                 <?php foreach($groups as $group){ ?>
                                 <li><a href="#" data-cview="customer_group_<?php echo $group['id']; ?>" onclick="dt_custom_view('customer_group_<?php echo $group['id']; ?>','.table-clients','customer_group_<?php echo $group['id']; ?>'); return false;"><?php echo $group['name']; ?></a></li>
                                 <?php } ?>
                              </ul>
                           </li>
                           <div class="clearfix"></div>
                           <li class="divider"></li>
                           <?php } ?>
                           <?php if(count($countries) > 1){ ?>
                           <li class="dropdown-submenu pull-left countries">
                              <a href="#" tabindex="-1"><?php echo _l('clients_country'); ?></a>
                              <ul class="dropdown-menu dropdown-menu-left">
                                 <?php foreach($countries as $country){ ?>
                                 <li><a href="#" data-cview="country_<?php echo $country['country_id']; ?>" onclick="dt_custom_view('country_<?php echo $country['country_id']; ?>','.table-clients','country_<?php echo $country['country_id']; ?>'); return false;"><?php echo $country['short_name']; ?></a></li>
                                 <?php } ?>
                              </ul>
                           </li>
                           <div class="clearfix"></div>
                           <li class="divider"></li> -->
                           <?php } ?>
                           <!-- <li class="dropdown-submenu pull-left invoice">
                              <a href="#" tabindex="-1"><?php echo _l('invoices'); ?></a>
                              <ul class="dropdown-menu dropdown-menu-left">
                                 <?php foreach($invoice_statuses as $status){ ?>
                                 <li>
                                    <a href="#" data-cview="invoices_<?php echo $status; ?>" onclick="dt_custom_view('invoices_<?php echo $status; ?>','.table-clients','invoices_<?php echo $status; ?>'); return false;"><?php echo _l('customer_have_invoices_by',format_invoice_status($status,'',false)); ?></a>
                                 </li>
                                 <?php } ?>
                              </ul>
                           </li>
                           <div class="clearfix"></div> -->
                           <!-- <li class="divider"></li>
                           <li class="dropdown-submenu pull-left estimate">
                              <a href="#" tabindex="-1"><?php echo _l('estimates'); ?></a>
                              <ul class="dropdown-menu dropdown-menu-left">
                                 <?php foreach($estimate_statuses as $status){ ?>
                                 <li>
                                    <a href="#" data-cview="estimates_<?php echo $status; ?>" onclick="dt_custom_view('estimates_<?php echo $status; ?>','.table-clients','estimates_<?php echo $status; ?>'); return false;">
                                    <?php echo _l('customer_have_estimates_by',format_estimate_status($status,'',false)); ?>
                                    </a>
                                 </li>
                                 <?php } ?>
                              </ul>
                           </li>
                           <div class="clearfix"></div> -->
                           <li class="divider"></li>
                           <li class="dropdown-submenu pull-left project">
                              <a href="#" tabindex="-1"><?php echo _l('projects'); ?></a>
                              <ul class="dropdown-menu dropdown-menu-left">
                                 <?php foreach($project_statuses as $status){ ?>
                                 <li>
                                    <a href="#" data-cview="projects_<?php echo $status['id']; ?>" onclick="dt_custom_view('projects_<?php echo $status['id']; ?>','.table-clients','projects_<?php echo $status['id']; ?>'); return false;">
                                    <?php echo _l('customer_have_projects_by',$status['name']); ?>
                                    </a>
                                 </li>
                                 <?php } ?>
                              </ul>
                           </li>
                           <div class="clearfix"></div>
                           <!-- <li class="divider"></li>
                           <li class="dropdown-submenu pull-left proposal">
                              <a href="#" tabindex="-1"><?php echo _l('proposals'); ?></a>
                              <ul class="dropdown-menu dropdown-menu-left">
                                 <?php foreach($proposal_statuses as $status){ ?>
                                 <li>
                                    <a href="#" data-cview="proposals_<?php echo $status; ?>" onclick="dt_custom_view('proposals_<?php echo $status; ?>','.table-clients','proposals_<?php echo $status; ?>'); return false;">
                                    <?php echo _l('customer_have_proposals_by',format_proposal_status($status,'',false)); ?>
                                    </a>
                                 </li>
                                 <?php } ?>
                              </ul>
                           </li>
                           <div class="clearfix"></div>
                           <?php if(count($contract_types) > 0) { ?>
                           <li class="divider"></li>
                           <li class="dropdown-submenu pull-left contract_types">
                              <a href="#" tabindex="-1"><?php echo _l('contract_types'); ?></a>
                              <ul class="dropdown-menu dropdown-menu-left">
                                 <?php foreach($contract_types as $type){ ?>
                                 <li>
                                    <a href="#" data-cview="contract_type_<?php echo $type['id']; ?>" onclick="dt_custom_view('contract_type_<?php echo $type['id']; ?>','.table-clients','contract_type_<?php echo $type['id']; ?>'); return false;">
                                    <?php echo _l('customer_have_contracts_by_type',$type['name']); ?>
                                    </a>
                                 </li>
                                 <?php } ?>
                              </ul>
                           </li>
                           <?php } ?>
                           <?php if(count($customer_admins) > 0 && (has_permission('customers','','create') || has_permission('customers','','edit'))){ ?>
                           <div class="clearfix"></div>
                           <li class="divider"></li>
                           <li class="dropdown-submenu pull-left responsible_admin">
                              <a href="#" tabindex="-1"><?php echo _l('responsible_admin'); ?></a>
                              <ul class="dropdown-menu dropdown-menu-left">
                                 <?php foreach($customer_admins as $cadmin){ ?>
                                 <li>
                                    <a href="#" data-cview="responsible_admin_<?php echo $cadmin['staff_id']; ?>" onclick="dt_custom_view('responsible_admin_<?php echo $cadmin['staff_id']; ?>','.table-clients','responsible_admin_<?php echo $cadmin['staff_id']; ?>'); return false;">
                                    <?php echo get_staff_full_name($cadmin['staff_id']); ?>
                                    </a>
                                 </li>
                                 <?php } ?>
                              </ul>
                           </li>
                           <?php } ?> -->
                        </ul>
                     </div>
                  </div>
                  <div class="clearfix"></div><br>
                  <div class="alpha-filter _filter_data">
                      <ul>
                      <li><a class="btn btn-default" style="padding:10px,15px; margin:0 2px;" data-cview="alphabet_All" onclick="dt_custom_view('alphabet_All','.table-products','alphabet_All'); return false;">All</a></li>
                  <?php foreach(range('A', 'Z') as $letter) {
                      ?>
                        <li><a class="btn btn-default" style="padding:10px,15px; margin:0 2px;" data-cview="alphabet_<?php echo $letter; ?>" onclick="dt_custom_view('alphabet_<?php echo $letter; ?>','.table-products','alphabet_<?php echo $letter; ?>'); return false;"><?php echo $letter; ?></a></li>
                  <?php  }
                ?>
                </ul>
                    </div>
                  <div class="clearfix"></div>
                  <?php if(has_permission('customers','','view') || have_assigned_customers()) {
                     $where_summary = '';
                     
                     //if(get_staff_user_id() == 1) {
                        $where_summary_activeperson_qry = 'SELECT  COUNT(*) AS `numrows`
                        FROM tblproducts
                        where status=1';
                        $CI          = & get_instance();
                        $where_summary_active     = $CI->db->query($where_summary_activeperson_qry)->result_array();

                        $where_summary_inactiveperson_qry = 'SELECT  COUNT(*) AS `numrows`
                        FROM tblproducts
                        where status=0';
                        $CI          = & get_instance();
                        $where_summary_inactive    = $CI->db->query($where_summary_inactiveperson_qry)->result_array();

                        //pre($where_summary_inactive);
//                     } else {
// //Person
//                         $where_summary_activeperson_qry = 'SELECT  COUNT(*) AS `numrows`
//                         FROM tblcontacts
//                         LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7
                        
//                         WHERE  tblcontacts.userid IN (select userid from tblcontacts where id IN (select contacts_id from tbltasks where id IN (SELECT taskid FROM `tbltask_assigned` WHERE staffid = 6) and contacts_id > 0))  AND tblcontacts.active=1';
//                         $CI          = & get_instance();
//                         $where_summary_activeperson     = $CI->db->query($where_summary_activeperson_qry)->result_array();

//                         $where_summary_inactiveperson_qry = 'SELECT  COUNT(*) AS `numrows`
//                         FROM tblcontacts
//                         LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7
                        
//                         WHERE  tblcontacts.userid IN (select userid from tblcontacts where id IN (select contacts_id from tbltasks where id IN (SELECT taskid FROM `tbltask_assigned` WHERE staffid = 6) and contacts_id > 0))  AND tblcontacts.active=0';
//                         $CI          = & get_instance();
//                         $where_summary_inactiveperson     = $CI->db->query($where_summary_inactiveperson_qry)->result_array();
//                     }
                     ?>
                  <hr class="hr-panel-heading" />
                  <div class="row mbot15">
                     <div class="col-md-12">
                        <h4 class="no-margin"><?php echo _l('products_summary'); ?></h4>
                     </div>
                     <div class="col-md-2 col-xs-6 border-right">
                        <h3 class="bold"><?php echo $totcnt = $where_summary_active[0]['numrows'] + $where_summary_inactive[0]['numrows']; ?></h3>
                        <span class="text-dark"><?php echo _l('products_summary_total'); ?></span>
                     </div>
                     <div class="col-md-2 col-xs-6 border-right">
                        <h3 class="bold"><?php echo $where_summary_active[0]['numrows']; ?></h3>
                        <span class="text-success"><?php echo _l('active_products'); ?></span>
                     </div>
                     <div class="col-md-2 col-xs-6 border-right">
                        <h3 class="bold"><?php echo $where_summary_inactive[0]['numrows']; ?></h3>
                        <span class="text-danger"><?php echo _l('inactive_products'); ?></span>
                     </div>
                     
                  </div>
                  <?php } ?>
                  <hr class="hr-panel-heading" />
                  <a href="#" data-toggle="modal" data-target="#customers_bulk_action" class="bulk-actions-btn table-btn hide" data-table=".table-clients"><?php echo _l('bulk_actions'); ?></a>
                  <div class="modal fade bulk_actions" id="customers_bulk_action" tabindex="-1" role="dialog">
                     <div class="modal-dialog" role="document">
                        <div class="modal-content">
                           <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                           </div>
                           <div class="modal-body">
                              <?php if(has_permission('customers','','delete')){ ?>
                              <div class="checkbox checkbox-danger">
                                 <input type="checkbox" name="mass_delete" id="mass_delete">
                                 <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                              </div>
                              <hr class="mass_delete_separator" />
                              <?php } ?>
                              <div id="bulk_change">
                                 <?php echo render_select('move_to_groups_customers_bulk[]',$groups,array('id','name'),'customer_groups','', array('multiple'=>true),array(),'','',false); ?>
                                 <p class="text-danger"><?php echo _l('bulk_action_customers_groups_warning'); ?></p>
                              </div>
                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                              <a href="#" class="btn btn-info" onclick="customers_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                           </div>
                        </div>
                        <!-- /.modal-content -->
                     </div>
                     <!-- /.modal-dialog -->
                  </div>
                  <!-- /.modal -->
                  <div class="checkbox">
                     <input type="checkbox" checked id="exclude_inactive" name="exclude_inactive">
                     <label for="exclude_inactive"><?php echo _l('exclude_inactive'); ?> <?php echo _l('products'); ?></label>
                  </div>
                  <div class="clearfix mtop20"></div>
                  <?php
                     $table_data = array();
                     $_table_data = array(
                      '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="clients"><label></label></div>',
                    //    array(
                    //      'name'=>_l('the_number_sign'),
                    //      'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-number')
                    //     ),
                         array(
                         'name'=>_l('product_name'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-name')
                        ),
                        array(
                         'name'=>_l('product_code'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-code')
                        ),
                         array(
                         'name'=>_l('product_unit'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-unit')
                        ),
                        array(
                         'name'=>_l('product_category'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-category')
                        ),
                        array(
                            'name'=>_l('product_tax'),
                            'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-tax')
                        ),
                        array(
                            'name'=>_l('product_description'),
                            'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-description')
                        ),
                        array(
                            'name'=>_l('product_status'),
                            'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-status')
                        ),
                        array(
                         'name'=>_l('date_created'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-date-created')
                        ),
                      );
                     foreach($_table_data as $_t){
                      array_push($table_data,$_t);
                     }


                     $table_data = hooks()->apply_filters('customers_table_columns', $table_data);

                     render_datatable($table_data,'products',[],[
                           'data-last-order-identifier' => 'customers',
                           'data-default-order'         => get_table_last_order('customers'),
                     ]);
                     ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script>
   $(function(){
       var CustomersServerParams = {};
       $.each($('._hidden_inputs._filters input'),function(){
          CustomersServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
      });
       CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';

       var tAPI = initDataTable('.table-products', admin_url+'products/table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(2,'asc'))); ?>);
       $('input[name="exclude_inactive"]').on('change',function(){
           tAPI.ajax.reload();
       });
       
   });
   function customers_bulk_action(event) {
       var r = confirm(app.lang.confirm_action_prompt);
       if (r == false) {
           return false;
       } else {
           var mass_delete = $('#mass_delete').prop('checked');
           var ids = [];
           var data = {};
           if(mass_delete == false || typeof(mass_delete) == 'undefined'){
               data.groups = $('select[name="move_to_groups_customers_bulk[]"]').selectpicker('val');
               if (data.groups.length == 0) {
                   data.groups = 'remove_all';
               }
           } else {
               data.mass_delete = true;
           }
           var rows = $('.table-clients').find('tbody tr');
           $.each(rows, function() {
               var checkbox = $($(this).find('td').eq(0)).find('input');
               if (checkbox.prop('checked') == true) {
                   ids.push(checkbox.val());
               }
           });
           data.ids = ids;
           $(event).addClass('disabled');
           setTimeout(function(){
             $.post(admin_url + 'clients/bulk_action', data).done(function() {
              window.location.reload();
          });
         },50);
       }
   }
  
    function testfun()
    {
        setTimeout(function(){
            window.location.reload();
        },50);
    }

</script>
<script>
  $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );
</script>
</body>
</html>
