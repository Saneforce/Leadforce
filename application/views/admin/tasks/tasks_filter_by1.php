<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="_hidden_inputs _filters _tasks_filters">
    <?php
    $tasks_filter_assignees = $this->misc_model->get_tasks_distinct_assignees();
    $tasks_filter_tasktype = $this->misc_model->get_tasks_distinct_tasktype();
    hooks()->do_action('tasks_filters_hidden_html');
    echo form_hidden('my_tasks',(!has_permission('tasks','','view') ? 'true' : ''));
    echo form_hidden('my_following_tasks');
    // echo form_hidden('billable');
    // echo form_hidden('billed');
    // echo form_hidden('not_billed');
    echo form_hidden('not_assigned');
    // echo form_hidden('due_date_passed');
    echo form_hidden('upcoming_tasks');
    echo form_hidden('recurring_tasks');
    echo form_hidden('all_period');
    echo form_hidden('today_tasks');
    echo form_hidden('tomorrow_tasks');
    echo form_hidden('yesterday_tasks');
    echo form_hidden('thisweek_tasks');
    echo form_hidden('lastweek_tasks');
    echo form_hidden('nextweek_tasks');
    echo form_hidden('thismonth_tasks');
    echo form_hidden('lastmonth_tasks');
    echo form_hidden('nextmonth_tasks');
    echo form_hidden('custom_tasks');
    $form_hidden_var = array();
    $form_hidden_var['id'] =  $form_hidden_var['name'] = 'custom_date_start_tasks';
    $form_hidden_var['value'] = date('Y-m-d');
    $form_hidden_var['type'] = 'hidden';
   echo form_input($form_hidden_var);
   $form_hidden_var['id'] =  $form_hidden_var['name'] = 'custom_date_end_tasks';
    echo form_input($form_hidden_var);

    /* Related task filter - used in customer profile */
    echo form_hidden('tasks_related_to');

    if(has_permission('tasks','','view')){
        foreach($tasks_filter_assignees as $tf_assignee){
            echo form_hidden('task_assigned_'.$tf_assignee['assigneeid']);
        }
    } 
	if(has_permission('tasks','','view')){
        foreach($tasks_filter_tasktype as $tf_tasktype){
            echo form_hidden('task_tasktype_'.$tf_tasktype['id']);
        }
    }
    foreach($task_statuses as $status){
        $val = 'true';
        if($status['filter_default'] == false){
            $val = '';
        }
        echo form_hidden('task_status_'.$status['id'],$val);
    }
    ?>
</div>


<div class="btn-group pull-right mleft4 mbot25 btn-with-tooltip-group _filter_data display_other" data-toggle="tooltip" data-title="<?php echo _l('tasks_list_column_order'); ?>">
       <!-- Button trigger modal -->
<?php if($view_table_name != '.table-rel-tasks-leads') { ?>
<button type="button" class="btn btn-default" data-toggle="modal" data-target="#tasks_list_column_orderModal">
  <i class="fa fa-list" aria-hidden="true"></i>
</button>
<?php } ?>
<!-- Modal -->
<div class="modal fade" id="tasks_list_column_orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<?php echo form_open_multipart(admin_url('settings/tasks_list_column'),array('id'=>'tasks_list_column')); ?>
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo _l('tasks_list_column_order'); ?></h5>
      </div>
      <div class="modal-body">
        <div class="form-group">
<?php $colarr = array(
"id"=>array("ins"=>"id","ll"=>"the_number_sign"),
"task_name"=>array("ins"=>"task_name","ll"=>"tasks_dt_name"),
"project_name"=>array("ins"=>"project_name","ll"=>"task_project_name"),
"project_status"=>array("ins"=>"project_status","ll"=>"project_status"),
"project_pipeline"=>array("ins"=>"project_pipeline","ll"=>"pipeline"),
"company"=>array("ins"=>"company","ll"=>"client"),
"teamleader"=>array("ins"=>"teamleader","ll"=>"teamleader"),
"project_contacts"=>array("ins"=>"project_contacts","ll"=>"project_contacts"),
"status"=>array("ins"=>"status","ll"=>"task_status"),
"tasktype"=>array("ins"=>"tasktype","ll"=>"tasktype"),
"description"=>array("ins"=>"description","ll"=>"description"),
"startdate"=>array("ins"=>"startdate","ll"=>"scheduled_date"),
"dateadded"=>array("ins"=>"dateadded","ll"=>"create_date"),
"datemodified"=>array("ins"=>"datemodified","ll"=>"modified_date"),
"datefinished"=>array("ins"=>"datefinished","ll"=>"finished_date"),
"assignees"=>array("ins"=>"assignees","ll"=>"task_assigned"),
"tags"=>array("ins"=>"tags","ll"=>"tags"),
"priority"=>array("ins"=>"priority","ll"=>"tasks_list_priority"),
"rel_type"=> array("ins"=>"rel_type","ll"=>"rel_type"),
); 

/*$custom_fields = get_table_custom_fields('projects');
foreach($custom_fields as $cfkey=>$cfval){
    $colarr[$cfval['slug']] = array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
}
$custom_fields = get_table_custom_fields('contacts');
foreach($custom_fields as $cfkey=>$cfval){
    $colarr[$cfval['slug']] = array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
}
$custom_fields = get_table_custom_fields('customers');
foreach($custom_fields as $cfkey=>$cfval){
    $colarr[$cfval['slug']] = array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
}*/
$custom_fields = get_table_custom_fields('tasks');
$cus_1 = array();
foreach($custom_fields as $cfkey=>$cfval){
    $cus_1[$cfval['slug']] = $colarr[$cfval['slug']] = array("ins"=>$cfval['name'],"ll"=>$cfval['name']);
}
?>
<?php   
$rel_type_is = '';
if(isset($table_attributes['data-new-rel-type']) && !empty($table_attributes['data-new-rel-type'])){
    $rel_type_is ='_'.$table_attributes['data-new-rel-type'];
}

$form_hidden_var = array();
$form_hidden_var['id'] =  $form_hidden_var['name'] = 'rel_type_is';
$form_hidden_var['value'] = $rel_type_is;
$form_hidden_var['type'] = 'hidden';
echo form_input($form_hidden_var);
?>
  
  <ul id="sortable">
  <?php $tasks_list_column_order = (array)json_decode(get_option('tasks_list_column_order'.$rel_type_is)); //pr($tasks_list_column_order); ?>
  <?php foreach($tasks_list_column_order as $ckey=>$cval){ 
	if((!empty($need_fields) && (in_array($ckey, $need_fields) )) || !empty($cus_1[$ckey])){?>
	  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
	  <input type="checkbox" name="settings[tasks_list_column_order][<?php echo $ckey; ?>]" value="1" checked="checked" /> <?php echo _l($colarr[$ckey]['ll']); ?>
	  </li>
    <?php }?>
    <?php if(isset($tasks_list_column_order['rel_type']) && $ckey == 'rel_type'){ ?>
    <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
	  <input type="checkbox" name="settings[tasks_list_column_order][<?php echo $ckey; ?>]" value="1" checked="checked" /> <?php echo _l($colarr[$ckey]['ll']); ?>
	  </li>
  <?php } } ?>
  <?php foreach($colarr as $ckey=>$cval){ if(!isset($tasks_list_column_order[$ckey])){
	  if((!empty($need_fields) && (in_array($ckey, $need_fields) ))  || !empty($cus_1[$ckey])){
	  ?>
	  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
	  <input type="checkbox" name="settings[tasks_list_column_order][<?php echo $ckey; ?>]" value="1"/> <?php echo _l($cval['ll']); ?>
	  </li>
      <?php }} 
      if($ckey == 'rel_type' && !isset($tasks_list_column_order['rel_type'])){
	  ?>
	  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
	  <input type="checkbox" name="settings[tasks_list_column_order][<?php echo $ckey; ?>]" value="1"/> Type
	  </li>
      <?php
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



<div class="btn-group pull-right mleft4 mbot25 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
       <i class="fa fa-filter" aria-hidden="true"></i>
   </button>
   <ul class="dropdown-menu width300">
   
    <li>
        <a href="#" data-cview="all" onclick="dt_custom_view('','<?php echo $view_table_name; ?>',''); return false;">
            <?php echo _l('task_list_all'); ?>
        </a>
    </li>
    <!-- <li class="divider"></li> -->
    <?php foreach($task_statuses as $status){ ?>
    <li class="clear-all-prevent<?php if($status['filter_default'] == true){echo ' active';} ?>" style="display:none;">
        <a href="#" data-cview="task_status_<?php echo $status['id']; ?>" onclick="dt_custom_view('task_status_<?php echo $status['id']; ?>','<?php echo $view_table_name; ?>','task_status_<?php echo $status['id']; ?>'); return false;">
            <?php echo $status['name']; ?>
        </a>
    </li>
    <?php } ?>
    <!--<li class="divider"></li>
     <li class="filter-group" data-filter-group="group-date">
        <a href="#" data-cview="today_tasks" onclick="dt_custom_view('today_tasks','<?php echo $view_table_name; ?>','today_tasks'); return false;">
            <?php echo _l('todays_tasks'); ?>
        </a>
    </li>
      <li class="filter-group" data-filter-group="group-date">
        <a href="#" data-cview="due_date_passed" onclick="dt_custom_view('due_date_passed','<?php echo $view_table_name; ?>','due_date_passed'); return false;">
            <?php echo _l('task_list_duedate_passed'); ?>
        </a>
    </li>
    <li class="filter-group" data-filter-group="group-date">
        <a href="#" data-cview="upcoming_tasks" onclick="dt_custom_view('upcoming_tasks','<?php echo $view_table_name; ?>','upcoming_tasks'); return false;">
            <?php echo _l('upcoming_tasks'); ?>
        </a>
    </li> -->
    <li class="divider"></li>
    <li class="filter-group <?php echo (!has_permission('tasks','','view') ? ' active' : ''); ?>" data-filter-group="assigned-follower-not-assigned">
        <a href="#" data-cview="my_tasks" onclick="dt_custom_view('my_tasks','<?php echo $view_table_name; ?>','my_tasks'); return false;">
            <?php echo _l('tasks_view_assigned_to_user'); ?>
        </a>
    </li>
    <!-- <li class="filter-group" data-filter-group="assigned-follower-not-assigned">
        <a href="#" data-cview="my_following_tasks" onclick="dt_custom_view('my_following_tasks','<?php echo $view_table_name; ?>','my_following_tasks'); return false;">
            <?php echo _l('tasks_view_follower_by_user'); ?>
        </a>
    </li> -->
    <?php if(has_permission('tasks','','view')){ ?>
    <!-- <li class="filter-group" data-filter-group="assigned-follower-not-assigned">
        <a href="#" data-cview="not_assigned" onclick="dt_custom_view('not_assigned','<?php echo $view_table_name; ?>','not_assigned'); return false;">
            <?php echo _l('task_list_not_assigned'); ?>
        </a>
    </li> -->
    <?php } ?>
    <?php if(has_permission('tasks','','create') || has_permission('tasks','','edit')){ ?>
    <!-- <li>
        <a href="#" data-cview="recurring_tasks" onclick="dt_custom_view('recurring_tasks','<?php echo $view_table_name; ?>','recurring_tasks'); return false;">
            <?php echo _l('recurring_tasks'); ?>
        </a>
    </li> -->
    <?php } ?>
    <?php if(has_permission('invoices','','create')){ ?>
    <!-- <li class="divider"></li>
    <li class="filter-group" data-filter-group="group-billable">
        <a href="#" data-cview="billable" onclick="dt_custom_view('billable','<?php echo $view_table_name; ?>','billable'); return false;">
            <?php echo _l('task_billable'); ?>
        </a>
    </li>
     <li class="filter-group" data-filter-group="group-billable">
        <a href="#" data-cview="billed" onclick="dt_custom_view('billed','<?php echo $view_table_name; ?>','billed'); return false;">
            <?php echo _l('task_billed'); ?>
        </a>
    </li>
     <li class="filter-group" data-filter-group="group-billable">
        <a href="#" data-cview="not_billed" onclick="dt_custom_view('not_billed','<?php echo $view_table_name; ?>','not_billed'); return false;">
            <?php echo _l('task_billed_no'); ?>
        </a>
    </li> -->
    <?php } ?>
    <?php if(has_permission('tasks','','view')){ ?>
	
	    <?php if(count($tasks_filter_tasktype)){ ?>
    <div class="clearfix"></div>
    <li class="divider"></li>
    <li class="dropdown-submenu pull-left">
       <a href="#" tabindex="-1"><?php echo _l('filter_by_tasktype'); ?></a>
       <ul class="dropdown-menu dropdown-menu-left">
        <?php foreach($tasks_filter_tasktype as $as){ ?>
        <li>
            <a href="#" data-cview="task_tasktype_<?php echo $as['id']; ?>" onclick="dt_custom_view(<?php echo $as['id']; ?>,'<?php echo $view_table_name; ?>','task_tasktype_<?php echo $as['id']; ?>'); return false;"><?php echo $as['name']; ?></a>
        </li>
        <?php } ?>
    </ul>
</li>
<?php } ?>

	
    <?php if(count($tasks_filter_assignees)){ ?>
    <div class="clearfix"></div>
    <li class="divider"></li>
    <li class="dropdown-submenu pull-left">
       <a href="#" tabindex="-1"><?php echo _l('filter_by_assigned'); ?></a>
       <ul class="dropdown-menu dropdown-menu-left">
        <?php foreach($tasks_filter_assignees as $as){ ?>
        <li>
            <a href="#" data-cview="task_assigned_<?php echo $as['assigneeid']; ?>" onclick="dt_custom_view(<?php echo $as['assigneeid']; ?>,'<?php echo $view_table_name; ?>','task_assigned_<?php echo $as['assigneeid']; ?>'); return false;"><?php echo $as['full_name']; ?></a>
        </li>
        <?php } ?>
    </ul>
</li>
<?php } ?>

<?php } ?>

</ul>
</div>
<div class="btn-group pull-right mleft4 mbot25 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
       <i class="fa fa-calendar" aria-hidden="true"></i>
   </button>
   <ul class="dropdown-menu width300">
   <li class="filter-group" data-filter-group="group-date"></li>
   <li class="filter-group" data-filter-group="group-date">
        <a href="#" data-cview="all_period" onclick="dt_custom_view('all_period','<?php echo $view_table_name; ?>','all_period'); return false;">
            <?php echo _l('all_period'); ?>
        </a>
    </li>
    
    <li class="filter-group" data-filter-group="group-date">
        <a href="#" data-cview="today_tasks" onclick="dt_custom_view('today_tasks','<?php echo $view_table_name; ?>','today_tasks'); return false;">
            <?php echo _l('todays_tasks'); ?>
        </a>
    </li>
   
    <li class="filter-group" data-filter-group="group-date">
        <a href="#" data-cview="tomorrow_tasks" onclick="dt_custom_view('tomorrow_tasks','<?php echo $view_table_name; ?>','tomorrow_tasks'); return false;">
            <?php echo _l('tomorrow_tasks'); ?>
        </a>
    </li>
    <li class="filter-group" data-filter-group="group-date">
        <a href="#" data-cview="yesterday_tasks" onclick="dt_custom_view('yesterday_tasks','<?php echo $view_table_name; ?>','yesterday_tasks'); return false;">
            <?php echo _l('yesterday_tasks'); ?>
        </a>
    </li>
    <li class="filter-group" data-filter-group="group-date">
        <a href="#" data-cview="thisweek_tasks" onclick="dt_custom_view('thisweek_tasks','<?php echo $view_table_name; ?>','thisweek_tasks'); return false;">
            <?php echo _l('thisweek_tasks'); ?>
        </a>
    </li>
    <li class="filter-group" data-filter-group="group-date">
        <a href="#" data-cview="lastweek_tasks" onclick="dt_custom_view('lastweek_tasks','<?php echo $view_table_name; ?>','lastweek_tasks'); return false;">
            <?php echo _l('lastweek_tasks'); ?>
        </a>
    </li>
    <li class="filter-group" data-filter-group="group-date">
        <a href="#" data-cview="nextweek_tasks" onclick="dt_custom_view('nextweek_tasks','<?php echo $view_table_name; ?>','nextweek_tasks'); return false;">
            <?php echo _l('nextweek_tasks'); ?>
        </a>
    </li>
    <li class="filter-group" data-filter-group="group-date">
        <a href="#" data-cview="thismonth_tasks" onclick="dt_custom_view('thismonth_tasks','<?php echo $view_table_name; ?>','thismonth_tasks'); return false;">
            <?php echo _l('thismonth_tasks'); ?>
        </a>
    </li>
    <li class="filter-group" data-filter-group="group-date">
        <a href="#" data-cview="lastmonth_tasks" onclick="dt_custom_view('lastmonth_tasks','<?php echo $view_table_name; ?>','lastmonth_tasks'); return false;">
            <?php echo _l('lastmonth_tasks'); ?>
        </a>
    </li>
    <li class="filter-group" data-filter-group="group-date">
        <a href="#" data-cview="nextmonth_tasks" onclick="dt_custom_view('nextmonth_tasks','<?php echo $view_table_name; ?>','nextmonth_tasks'); return false;">
            <?php echo _l('nextmonth_tasks'); ?>
        </a>
    </li>
    <li class="filter-group" data-filter-group="group-date">
        <a href="#" data-cview="upcoming_tasks" onclick="dt_custom_view('upcoming_tasks','<?php echo $view_table_name; ?>','upcoming_tasks'); return false;">
            <?php echo _l('upcoming_tasks'); ?>
        </a>
    </li>
    <li class="divider"></li>
    <li class="filter-group" data-filter-group="group-date">
        <h5 style="margin-left: 15px;"> <?php echo _l('custom_tasks'); ?></h5>
        <div class="row mtop15">
            <div class="col-md-4 period">
                <div class="form-group" app-field-wrapper="period-from">
                    <div class="input-group date">
                        <input type="text" id="period-from" name="period-from" class="form-control datepicker" value="<?php echo date('Y-m-d'); ?>" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="col-md-4 period">
                <div class="form-group" app-field-wrapper="period-to">
                    <div class="input-group date">
                        <input type="text" id="period-to" name="period-to" class="form-control datepicker" value="<?php echo date('Y-m-d'); ?>" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="col-md-4 period">
            <a href="#" class="btn btn-xs" data-cview="custom_tasks" onclick="dt_custom_view('custom_tasks','<?php echo $view_table_name; ?>','custom_tasks'); return false;">
            <?php echo _l('apply'); ?>
        </a>
            </div>
        </div>
        
    </li>
    
</ul>
</div>

<style>
.period{
    padding: 0px 0px 0 20px;
margin: 0 -20px 0 10px;
}

.period .datepicker{
    padding: 6px;
}
</style>