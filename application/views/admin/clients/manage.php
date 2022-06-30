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
                     <a href="<?php echo admin_url('clients/client'); ?>" class="btn btn-info mright5 test pull-left display-block">
                     <?php echo _l('new_client'); ?></a>
                     <!-- <a href="<?php echo admin_url('clients/import'); ?>" class="btn btn-info pull-left display-block mright5 hidden-xs">
                     <?php echo _l('import_customers'); ?></a> -->
                     <?php } ?>
                     <!-- <a href="<?php echo admin_url('clients/all_contacts'); ?>" class="btn btn-info pull-left display-block mright5">
                     <?php echo _l('customer_contacts'); ?></a> -->
                     

                                  

<div class="btn-group pull-right mleft4 mbot25 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('clients_list_column'); ?>">
       <!-- Button trigger modal -->
<button type="button" class="btn btn-default" data-toggle="modal" data-target="#projects_list_column_orderModal">
  <i class="fa fa-list" aria-hidden="true"></i>
</button>

<!-- Modal -->
<div class="modal fade" id="projects_list_column_orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<?php echo form_open_multipart(admin_url('settings/client_list_column'),array('id'=>'projects_list_column')); ?>
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo _l('clients_list_column'); ?></h5>
      </div>
      <div class="modal-body">
        <div class="form-group">

<?php $colarr = array(
"company"=>array("ins"=>"company","ll"=>"client_company"),
"active"=>array("ins"=>"active","ll"=>"leads_dt_status"),
"customerGroups"=>array("ins"=>"customerGroups","ll"=>"kb_dt_group_name"),
"datecreated"=>array("ins"=>"datecreated","ll"=>"lead_created"),
"vat"=>array("ins"=>"vat","ll"=>"clients_vat"),
"phonenumber"=>array("ins"=>"phonenumber","ll"=>"clients_phone"),
"country"=>array("ins"=>"country","ll"=>"clients_country"),
"city"=>array("ins"=>"city","ll"=>"clients_city"),
"zip"=>array("ins"=>"zip","ll"=>"clients_zip"),
"state"=>array("ins"=>"state","ll"=>"clients_state"),
"address"=>array("ins"=>"address","ll"=>"clients_address"),
"website"=>array("ins"=>"website","ll"=>"lead_website")
); 
$custom_fields = get_table_custom_fields('customers');
foreach($custom_fields as $cfkey=>$cfval){
    $colarr[$cfval['slug']] = array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
}

?>  
  <ul id="sortable" class="ui-sortable">
  <?php $clients_list_column_order = (array)json_decode(get_option('clients_list_column_order')); //pr($projects_list_column_order); ?>
  <?php foreach($clients_list_column_order as $ckey=>$cval){ ?>
	  <li class="ui-state-default ui-sortable-handle"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
	  <input type="checkbox" name="settings[clients_list_column][<?php echo $ckey; ?>]" value="1" checked="checked" /> <?php echo _l($colarr[$ckey]['ll']); ?>
	  </li>
  <?php } ?>
  <?php foreach($colarr as $ckey=>$cval){ if(!isset($clients_list_column_order[$ckey])){?>
	  <li class="ui-state-default ui-sortable-handle"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
	  <input type="checkbox" name="settings[clients_list_column][<?php echo $ckey; ?>]" value="1"/> <?php echo _l($cval['ll']); ?>
	  </li>
  <?php }} ?>
  
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


  
                    
                     <div class="visible-xs">
                        <div class="clearfix"></div>
                     </div>
                     <div class="btn-group pull-right btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                        
                     <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-filter" aria-hidden="true"></i>
                        </button>
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
                              <a href="#" tabindex="-1"><?php echo _l('project'); ?> Stages</a>
                              <ul class="dropdown-menu dropdown-menu-left">
                                 <?php foreach($project_statuses as $status){ ?>
                                 <li>
                                    <a href="#" data-cview="projects_<?php echo $status['id']; ?>" onclick="dt_custom_view('projects_<?php echo $status['id']; ?>','.table-clients','projects_<?php echo $status['id']; ?>'); return false;">
                                    <?php //echo _l('customer_have_projects_by',$status['name']); 
                                    echo $status['name'];
                                    ?>
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
                      <li><a class="btn btn-default" style="padding:10px,15px; margin:0 2px;" data-cview="alphabet_All" onclick="dt_custom_view('alphabet_All','.table-clients','alphabet_All'); return false;">All</a></li>
                  <?php foreach(range('A', 'Z') as $letter) {
                      ?>
                        <li><a class="btn btn-default" style="padding:10px,15px; margin:0 2px;" data-cview="alphabet_<?php echo $letter; ?>" onclick="dt_custom_view('alphabet_<?php echo $letter; ?>','.table-clients','alphabet_<?php echo $letter; ?>'); return false;"><?php echo $letter; ?></a></li>
                  <?php  }
                ?>
                </ul>
                    </div>
                  <div class="clearfix"></div>
                  <?php if(has_permission('customers','','view') || have_assigned_customers()) {
                     $where_summary = '';
                     if(!has_permission('customers','','view')){
                         $where_summary = ' AND userid IN (SELECT customer_id FROM '.db_prefix().'customer_admins WHERE staff_id='.get_staff_user_id().')';
                     }

                     if(is_admin(get_staff_user_id())) {
                        $where_summary_activeperson_qry = 'SELECT  COUNT(*) AS `numrows`
                        FROM tblcontacts
                        LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7  where tblcontacts.active=1';
                        $CI          = & get_instance();
                        $where_summary_activeperson     = $CI->db->query($where_summary_activeperson_qry)->result_array();

                        // $where_summary_activeperson_qry = 'SELECT  COUNT(*) AS `numrows`
                        // FROM tblcontacts
                        // LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7  where tblcontacts.active=1';
                        // $CI          = & get_instance();
                        // $where_summary_activeperson     = $CI->db->query($where_summary_activeperson_qry)->result_array();


                         $where_summary_inactiveperson_qry = 'SELECT  COUNT(*) AS `numrows`
                         FROM tblcontacts
                         LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7  where tblcontacts.active=0';
                         $CI          = & get_instance();
                         $where_summary_inactiveperson     = $CI->db->query($where_summary_inactiveperson_qry)->result_array();

                        $where_summary_totorg_qry = 'SELECT company
                        FROM tblclients
                        LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=6 LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid AND ctable_1.fieldto="customers" AND ctable_1.fieldid=8
                        WHERE (tblclients.active = 1 OR tblclients.active = 0) AND tblclients.deleted_status=0 group by tblclients.company';
                        $CI          = & get_instance();
                        $where_summary_totorg     = $CI->db->query($where_summary_totorg_qry)->result_array();
                        
                        $where_summary_active_qry = 'SELECT company
                         FROM tblclients
                         LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=6 LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid AND ctable_1.fieldto="customers" AND ctable_1.fieldid=8
                         WHERE tblclients.active = 1 AND tblclients.deleted_status=0 group by tblclients.userid';
                        $CI          = & get_instance();
                        $where_summary_active     = $CI->db->query($where_summary_active_qry)->result_array();
                        
                        $where_summary_inactive_qry = 'SELECT company
                        FROM tblclients
                        LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=6 LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid AND ctable_1.fieldto="customers" AND ctable_1.fieldid=8
                        WHERE tblclients.active = 0 AND tblclients.deleted_status=0 group by tblclients.userid';
                        $CI          = & get_instance();
                       $where_summary_inactive     = $CI->db->query($where_summary_inactive_qry)->result_array();
                    
                    } else {
//Person
                        // $where_summary_activeperson_qry = 'SELECT  COUNT(*) AS `numrows`
                        // FROM tblcontacts
                        // LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7
                        
                        // WHERE  tblcontacts.userid IN (select userid from tblcontacts where id IN (select contacts_id from tbltasks where id IN (SELECT taskid FROM `tbltask_assigned` WHERE staffid = 6) and contacts_id > 0))  AND tblcontacts.active=1';
                        // $CI          = & get_instance();
                        // $where_summary_activeperson     = $CI->db->query($where_summary_activeperson_qry)->result_array();

                        // $where_summary_inactiveperson_qry = 'SELECT  COUNT(*) AS `numrows`
                        // FROM tblcontacts
                        // LEFT JOIN tblclients ON tblclients.userid=tblcontacts.userid LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblcontacts.id = ctable_0.relid AND ctable_0.fieldto="contacts" AND ctable_0.fieldid=7
                        
                        // WHERE  tblcontacts.userid IN (select userid from tblcontacts where id IN (select contacts_id from tbltasks where id IN (SELECT taskid FROM `tbltask_assigned` WHERE staffid = 6) and contacts_id > 0))  AND tblcontacts.active=0';
                        // $CI          = & get_instance();
                        // $where_summary_inactiveperson     = $CI->db->query($where_summary_inactiveperson_qry)->result_array();
//TOTAL Organisation
                        // $where_summary_totorg_qry = 'SELECT company
                        // FROM tblclients
                        // LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=6 LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid AND ctable_1.fieldto="customers" AND ctable_1.fieldid=8
                        // WHERE  (tblclients.active = 1 OR tblclients.active = 0)  AND (tblclients.userid IN (SELECT userid FROM tblcontacts WHERE email=(SELECT email FROM tblstaff WHERE staffid="'.get_staff_user_id().'")) OR (tblclients.userid IN (select clientid from tblprojects where id IN (select project_id from tblproject_members where staff_id="'.get_staff_user_id().'") OR tblclients.userid IN ( select clientid from tblprojects where teamleader = "'.get_staff_user_id().'")) OR tblclients.userid IN (select clientid from tblprojects where id IN (select project_id from tblproject_contacts where contacts_id="'.get_staff_user_id().'"))))  group by tblclients.company';
                        $CI          = & get_instance();
                        $my_staffids = $CI->staff_model->get_my_staffids();
                        if($my_staffids){
                            $uids   = $CI->clients_model->get_mystaffs_userids($my_staffids);
                            if(count($uids) == 0) {
                                $uids[] = 0;
                            }
                            //pre($uids);
                            // $where_summary_totorg_qry = 'SELECT company
                            // FROM tblclients
                            // LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=6 LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid AND ctable_1.fieldto="customers" AND ctable_1.fieldid=8
                            // WHERE  (tblclients.active = 1 OR tblclients.active = 0)  AND (tblclients.userid IN (SELECT userid FROM tblcontacts WHERE email=(SELECT email FROM tblstaff WHERE staffid IN (' . implode(',',$my_staffids) . '))) OR (tblclients.userid IN (select clientid from tblprojects where id IN (select project_id from tblproject_members where staff_id IN (' . implode(',',$my_staffids) . ')) OR tblclients.userid IN ( select clientid from tblprojects where teamleader IN (' . implode(',',$my_staffids) . '))) ))  group by tblclients.company';
                            // $CI          = & get_instance();
                            // $where_summary_totorg     = $CI->db->query($where_summary_totorg_qry)->result_array();
        //Active Organisation
                            $where_summary_active_qry = 'SELECT company
                                FROM tblclients
                                LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=6 LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid AND ctable_1.fieldto="customers" AND ctable_1.fieldid=8
                                WHERE  tblclients.active = 1 AND ('.db_prefix().'clients.userid IN ('.implode(",",$uids).') OR '.db_prefix().'clients.addedfrom IN ('.implode(",",$my_staffids).') OR ('.db_prefix().'clients.addedfrom="'.get_staff_user_id().'")) AND tblclients.deleted_status=0 group by '.db_prefix().'clients.userid ';
                            $CI          = & get_instance();
                            $where_summary_active     = $CI->db->query($where_summary_active_qry)->result_array();
        //Inactive Organisation
                            $where_summary_inactive_qry = 'SELECT company
                                FROM tblclients
                                LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=6 LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid AND ctable_1.fieldto="customers" AND ctable_1.fieldid=8
                                WHERE  tblclients.active = 0 AND  ('.db_prefix().'clients.userid IN ('.implode(",",$uids).') OR '.db_prefix().'clients.addedfrom IN ('.implode(",",$my_staffids).')  OR ('.db_prefix().'clients.addedfrom="'.get_staff_user_id().'")) AND tblclients.deleted_status=0 group by '.db_prefix().'clients.userid';
                            $CI          = & get_instance();
                            $where_summary_inactive     = $CI->db->query($where_summary_inactive_qry)->result_array();
                        } else {
                            $where_summary_totorg_qry = 'SELECT company
                            FROM tblclients
                            LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=6 LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid AND ctable_1.fieldto="customers" AND ctable_1.fieldid=8
                            WHERE  (tblclients.active = 1 OR tblclients.active = 0)  AND (tblclients.userid IN (SELECT userid FROM tblcontacts WHERE email=(SELECT email FROM tblstaff WHERE staffid="'.get_staff_user_id().'")) OR (tblclients.userid IN (select clientid from tblprojects where id IN (select project_id from tblproject_members where staff_id="'.get_staff_user_id().'") OR tblclients.userid IN ( select clientid from tblprojects where teamleader = "'.get_staff_user_id().'")) )) AND tblclients.deleted_status=0  group by tblclients.company';
                            $CI          = & get_instance();
                            $where_summary_totorg     = $CI->db->query($where_summary_totorg_qry)->result_array();
                        //echo $CI->db->last_query(); exit;
    //Active Organisation
                            $where_summary_active_qry = 'SELECT company
                            FROM tblclients
                            LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=6 LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid AND ctable_1.fieldto="customers" AND ctable_1.fieldid=8
                            WHERE  (tblclients.active = 1)  AND (('.db_prefix().'clients.addedfrom="'.get_staff_user_id().'") OR tblclients.userid IN (SELECT userid FROM tblcontacts WHERE email=(SELECT email FROM tblstaff WHERE staffid="'.get_staff_user_id().'")) OR (tblclients.userid IN (select clientid from tblprojects where id IN (select project_id from tblproject_members where staff_id="'.get_staff_user_id().'") OR tblclients.userid IN ( select clientid from tblprojects where teamleader = "'.get_staff_user_id().'")))) AND tblclients.deleted_status=0 group by tblclients.userid';
                            $CI          = & get_instance();
                            $where_summary_active     = $CI->db->query($where_summary_active_qry)->result_array();
    //Inactive Organisation
                            $where_summary_inactive_qry = 'SELECT company
                            FROM tblclients
                            LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=6 LEFT JOIN tblcustomfieldsvalues as ctable_1 ON tblclients.userid = ctable_1.relid AND ctable_1.fieldto="customers" AND ctable_1.fieldid=8
                            WHERE  (tblclients.active = 0)  AND (('.db_prefix().'clients.addedfrom="'.get_staff_user_id().'") OR tblclients.userid IN (SELECT userid FROM tblcontacts WHERE email=(SELECT email FROM tblstaff WHERE staffid="'.get_staff_user_id().'")) OR (tblclients.userid IN (select clientid from tblprojects where id IN (select project_id from tblproject_members where staff_id="'.get_staff_user_id().'") OR tblclients.userid IN ( select clientid from tblprojects where teamleader = "'.get_staff_user_id().'")))) AND tblclients.deleted_status=0 group by tblclients.userid';
                            $CI          = & get_instance();
                            $where_summary_inactive     = $CI->db->query($where_summary_inactive_qry)->result_array();
                            
                        }

                        
                        
                        
                    }
                     ?>
                  <hr class="hr-panel-heading" />
                  <div class="row mbot15">
                     <div class="col-md-12">
                        <h4 class="no-margin"><?php echo _l('customers_summary'); ?></h4>
                     </div>
                     <div class="col-md-2 col-xs-6 border-right">
                        <h3 class="bold"><?php echo $totcnt = count($where_summary_active) + count($where_summary_inactive); ?></h3>
                        <span class="text-dark"><?php echo _l('customers_summary_total'); ?></span>
                     </div>
                     <div class="col-md-2 col-xs-6 border-right">
                        <h3 class="bold"><?php echo count($where_summary_active); ?></h3>
                        <span class="text-success"><?php echo _l('active_customers'); ?></span>
                     </div>
                     <div class="col-md-2 col-xs-6 border-right">
                        <h3 class="bold"><?php echo count($where_summary_inactive); ?></h3>
                        <span class="text-danger"><?php echo _l('inactive_active_customers'); ?></span>
                     </div>
                     <!-- <div class="col-md-2 col-xs-6 border-right">
                        <h3 class="bold"><?php echo $where_summary_activeperson[0]['numrows']; ?></h3>
                        <span class="text-info"><?php echo _l('customers_summary_active'); ?></span>
                     </div>
                     <div class="col-md-2  col-xs-6 border-right">
                        <h3 class="bold"><?php echo $where_summary_inactiveperson[0]['numrows']; ?></h3>
                        <span class="text-danger"><?php echo _l('customers_summary_inactive'); ?></span>
                     </div>
                     <div class="col-md-2 col-xs-6">
                        <h3 class="bold"><?php echo total_rows(db_prefix().'contacts','last_login LIKE "'.date('Y-m-d').'%"'.$where_summary); ?></h3>
                        <span class="text-muted">
                        <?php
                           $contactsTemplate = '';
                           if(count($contacts_logged_in_today)> 0){
                              foreach($contacts_logged_in_today as $contact){
                               $url = admin_url('clients/client/'.$contact['userid'].'?contactid='.$contact['id']);
                               $fullName = $contact['firstname'] . ' ' . $contact['lastname'];
                               $dateLoggedIn = _dt($contact['last_login']);
                               $html = "<a href='$url' target='_blank'>$fullName</a><br /><small>$dateLoggedIn</small><br />";
                               $contactsTemplate .= html_escape('<p class="mbot5">'.$html.'</p>');
                           }
                           ?>
                        <?php } ?>
                        <span<?php if($contactsTemplate != ''){ ?> class="pointer text-has-action" data-toggle="popover" data-title="<?php echo _l('customers_summary_logged_in_today'); ?>" data-html="true" data-content="<?php echo $contactsTemplate; ?>" data-placement="bottom" <?php } ?>><?php echo _l('customers_summary_logged_in_today'); ?></span>
                        </span>
                     </div> -->
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
                     <label for="exclude_inactive"><?php echo _l('exclude_inactive'); ?> <?php echo _l('clients'); ?></label>
                  </div>
                  <div class="clearfix mtop20"></div>
                  <?php


    $table_data_temp = array(
        "userid"=>_l("the_number_sign"),
        "company"=>_l("clients_list_company"),
        "vat"=>_l("clients_vat"),
        "phonenumber"=>_l("clients_phone"),
        "country"=>_l("clients_country"),
        "city"=>_l("clients_city"),
        "zip"=>_l("clients_zip"),
        "state"=>_l("clients_state"),
        "address"=>_l("clients_address"),
        "website"=>_l("lead_website"),
        "active"=>_l("leads_dt_status"),
        "datecreated"=>_l("lead_created"),
        "customerGroups"=>_l("kb_dt_group_name")
        ); 

        $custom_fields = get_table_custom_fields('customers');
        foreach($custom_fields as $cfkey=>$cfval){
            $table_data_temp[$cfval['slug']] = $cfval['name'];
        }
        $table_data = array();
        $clients_list_column_order = (array)json_decode(get_option('clients_list_column_order')); //pr($projects_list_column_order);
        foreach($clients_list_column_order as $ckey=>$cval){
            if(isset($table_data_temp[$ckey])){
                $table_data[] = $table_data_temp[$ckey];
            }
        }

        array_unshift($table_data, [
            'name'     => '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="clients"><label></label></div>',
            'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-number')
        ]);
        
        


     
          
    
                     $table_data = hooks()->apply_filters('customers_table_columns', $table_data);

                     render_datatable($table_data,'clients',[],[
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

       var tAPI = initDataTable('.table-clients', admin_url+'clients/table', [0], [0], CustomersServerParams);
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
