<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
  <?php foreach(tasks_summary_data((isset($rel_id) ? $rel_id : null),(isset($rel_type) ? $rel_type : 'project')) as $summary){ ?>
    <div class="col-md-2 col-xs-6 border-right">
      <h3 class="bold no-mtop <?php echo $summary['name']; ?>1"><?php echo $summary['total_tasks']; ?></h3>
      <p class="font-medium no-mbot">
      <a style="color:<?php echo $summary['color']; ?>" href="#" id="task_status_<?php echo $summary['status_id']; ?>" data-cview="task_status_<?php echo $summary['status_id']; ?>" onclick="dt_custom_view('task_status_<?php echo $summary['status_id']; ?>','<?php echo $view_table_name; ?>','task_status_<?php echo $summary['status_id']; ?>'); return false;">
          <?php echo $summary['name']; ?>
      </a>
      </p>
      <p class="font-medium-xs no-mbot text-muted" id="me_<?php echo $summary['name']; ?>">
        <?php echo _l('tasks_view_assigned_to_user'); ?>: <?php echo $summary['total_my_tasks']; ?>
      </p>
    </div>
    <?php } ?>
<input type="hidden" id="assign_me_text" value="<?php echo _l('tasks_view_assigned_to_user'); ?>">