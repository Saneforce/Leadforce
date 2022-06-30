<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo render_input('settings[tasks_kanban_limit]','tasks_kanban_limit',get_option('tasks_kanban_limit'),'number'); ?>
<hr />
<?php echo render_yes_no_option('show_all_tasks_for_project_member','show_all_tasks_for_project_member'); ?>
<hr />
<?php render_yes_no_option('client_staff_add_edit_delete_task_comments_first_hour','settings_client_staff_add_edit_delete_task_comments_first_hour'); ?>
<hr />
<?php render_yes_no_option('new_task_auto_assign_current_member','new_task_auto_assign_current_member','new_task_auto_assign_current_member_help'); ?>
<hr />
<?php render_yes_no_option('new_task_auto_follower_current_member','new_task_auto_follower_current_member'); ?>
<hr />
<?php render_yes_no_option('auto_stop_tasks_timers_on_new_timer','auto_stop_tasks_timers_on_new_timer'); ?>
<hr />
<?php render_yes_no_option('mark_complete_mandatory_to_add_new_activity','mark_complete_mandatory_to_add_new_activity'); ?>
<hr />
<?php render_yes_no_option('timer_started_change_status_in_progress','timer_started_change_status_in_progress'); ?>
<hr />
<?php render_yes_no_option('task_biillable_checked_on_creation','task_biillable_checked_on_creation'); ?>
<hr />
<div class="form-group">

    <label for="default_task_status" class="control-label"><?php echo _l('tasks_list_column_order'); ?></label>
    <?php $colarr = array(
"id"=>array("ins"=>"id","ll"=>"the_number_sign"),
"task_name"=>array("ins"=>"task_name","ll"=>"tasks_dt_name"),
"project_name"=>array("ins"=>"project_name","ll"=>"project_name"),
"project_status"=>array("ins"=>"project_status","ll"=>"project_status"),
"company"=>array("ins"=>"company","ll"=>"client"),
"teamleader"=>array("ins"=>"teamleader","ll"=>"teamleader"),
"project_contacts"=>array("ins"=>"project_contacts","ll"=>"project_contacts"),
"status"=>array("ins"=>"status","ll"=>"task_status"),
"tasktype"=>array("ins"=>"tasktype","ll"=>"tasktype"),
"startdate"=>array("ins"=>"startdate","ll"=>"tasks_dt_datestart"),
"assignees"=>array("ins"=>"assignees","ll"=>"task_assigned"),
"tags"=>array("ins"=>"tags","ll"=>"tags"),
"priority"=>array("ins"=>"priority","ll"=>"tasks_list_priority"),
); 
$custom_fields = get_table_custom_fields('projects');
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
}
?>
    <ul id="sortable">
        <?php $tasks_list_column_order = (array)json_decode(get_option('tasks_list_column_order')); //pr($tasks_list_column_order); ?>
        <?php foreach($tasks_list_column_order as $ckey=>$cval){ ?>
        <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
            <input type="checkbox" name="settings[tasks_list_column_order][<?php echo $ckey; ?>]" value="1"
                checked="checked" /> <?php echo _l($colarr[$ckey]['ll']); ?>
        </li>
        <?php } ?>
        <?php foreach($colarr as $ckey=>$cval){ if(!isset($tasks_list_column_order[$ckey])){?>
        <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
            <input type="checkbox" name="settings[tasks_list_column_order][<?php echo $ckey; ?>]" value="1" />
            <?php echo _l($cval['ll']); ?>
        </li>
        <?php }} ?>

    </ul>

</div>
<hr />
<div class="form-group">
    <label for="default_task_status" class="control-label"><?php echo _l('default_task_status'); ?></label>
    <select name="settings[default_task_status]" class="selectpicker" id="default_task_status" data-width="100%"
        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
        <option value="auto" <?php if(get_option('default_task_status') == 'auto'){echo 'selected';} ?>>
            <?php echo _l('auto'); ?></option>
        <?php foreach($task_statuses as $status){ ?>
        <option value="<?php echo $status['id']; ?>"
            <?php if($status['id'] == get_option('default_task_status')){echo ' selected';}; ?>>
            <?php echo $status['name']; ?>
        </option>
        <?php } ?>
    </select>
</div>
<hr />
<div class="form-group">
    <label for="default_task_priority" class="control-label"><?php echo _l('default_task_priority'); ?></label>
    <select name="settings[default_task_priority]" class="selectpicker" id="default_task_priority" data-width="100%"
        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
        <?php foreach(get_tasks_priorities() as $priority) { ?>
        <option value="<?php echo $priority['id']; ?>"
            <?php if(get_option('default_task_priority') == $priority['id']){echo ' selected';} ?>>
            <?php echo $priority['name']; ?>
        </option>
        <?php } ?>
    </select>
</div>
<hr />
<div class="form-group">
    <label for="settings[task_modal_class]" class="control-label">
        <?php echo _l('modal_width_class'); ?> (modal-lg, modal-xl, modal-xxl)
    </label>
    <input type="text" id="settings[task_modal_class]" name="settings[task_modal_class]" class="form-control"
        value="<?php echo get_option('task_modal_class'); ?>">
</div>