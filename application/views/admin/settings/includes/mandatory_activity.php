<?php defined('BASEPATH') or exit('No direct script access allowed');?>
  <table class="table table-condensed">
	<thead>
      <tr>
        <th><?php echo _l('fields'); ?></th>
        <th><?php echo _l('is_mandatory'); ?></th>
      </tr>
    </thead>
    <tbody>
		<tr>
			<td><?php echo _l('als_tasktype'); ?></td>
			<td><input type="checkbox" name="deal[]" value="name" checked disabled> Yes</td>
		</tr>
		<tr>
			<td><?php echo _l('announcement_name'); ?></td>
			<td><input type="checkbox" name="deal[]" value="clientid" <?php if (!empty($mandatory) && in_array("clientid", $mandatory)){ echo 'checked';}?>> Yes</td>
		</tr>
		<tr>
			<td><?php echo _l('task_add_edit_description'); ?></td>
			<td><input type="checkbox" name="deal[]" value="project_contacts[]" <?php if (!empty($mandatory) && in_array("project_contacts[]", $mandatory)){ echo 'checked';}?>> Yes</td>
		</tr>
		<tr>
			<td><?php echo _l('task_assigned'); ?> </td>
			<td><input type="checkbox" name="deal[]" value="primary_contact" <?php if (!empty($mandatory) && in_array("primary_contact", $mandatory)){ echo 'checked';}?>> Yes</td>
		</tr>
		<tr>
			<td><?php echo _l('announcement_date_list'); ?></td>
			<td><input type="checkbox" name="deal[]" value="pipeline_id" <?php if (!empty($mandatory) && in_array("pipeline_id", $mandatory)){ echo 'checked';}?>> Yes</td>
		</tr>
		<tr>
			<td><?php echo _l('project_status'); ?></td>
			<td><input type="checkbox" name="deal[]" value="status" <?php if (!empty($mandatory) && in_array("status", $mandatory)){ echo 'checked';}?>> Yes</td>
		</tr>
		<tr>
			<td><?php echo _l('tasks_list_priority'); ?></td>
			<td><input type="checkbox" name="deal[]" value="teamleader" <?php if (!empty($mandatory) && in_array("teamleader", $mandatory)){ echo 'checked';}?>> Yes</td>
		</tr>
		<tr>
			<td><?php echo _l('deal'); ?></td>
			<td><input type="checkbox" name="deal[]" value="project_members[]" <?php if (!empty($mandatory) && in_array("project_members[]", $mandatory)){ echo 'checked';}?>> Yes</td>
		</tr>
		<tr>
			<td><?php echo _l('client_lowercase'); ?></td>
			<td><input type="checkbox" name="deal[]" value="project_cost" <?php if (!empty($mandatory) && in_array("project_cost", $mandatory)){ echo 'checked';}?>> Yes</td>
		</tr>
		<tr>
			<td><?php echo _l('contract_start_date'); ?></td>
			<td><input type="checkbox" name="deal[]" value="start_date" <?php if (!empty($mandatory) && in_array("start_date", $mandatory)){ echo 'checked';}?>> Yes</td>
		</tr>
		<tr>
			<td><?php echo _l('project_contacts'); ?></td>
			<td><input type="checkbox" name="deal[]" value="deadline" <?php if (!empty($mandatory) && in_array("deadline", $mandatory)){ echo 'checked';}?>> Yes</td>
		</tr>
		<tr>
			<td><?php echo _l('tags'); ?></td>
			<td><input type="checkbox" name="deal[]" value="tags" <?php if (!empty($mandatory) && in_array("tags", $mandatory)){ echo 'checked';}?>> Yes</td>
		</tr>
	</tbody>
  </table>
