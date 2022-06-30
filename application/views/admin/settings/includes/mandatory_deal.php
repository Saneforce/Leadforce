<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<table class="table table-condensed">
							<thead>
							  <tr>
								<th><?php echo _l('fields'); ?></th>
								<th><?php echo _l('is_mandatory'); ?></th>
							  </tr>
							</thead>
							<tbody>
								<tr>
									<td><?php echo _l('project_name'); ?></td>
									<td><input type="checkbox" name="deal[]" value="name" checked disabled> Yes</td>
								</tr>
								<tr>
									<td><?php echo _l('project_customer'); ?></td>
									<td><input type="checkbox" name="deal[]" value="clientid" <?php if (!empty($mandatory) && in_array("clientid", $mandatory)){ echo 'checked';}?>> Yes</td>
								</tr>
								<tr>
									<td><?php echo _l('project_contacts'); ?></td>
									<td><input type="checkbox" name="deal[]" value="project_contacts[]" <?php if (!empty($mandatory) && in_array("project_contacts[]", $mandatory)){ echo 'checked';}?>> Yes</td>
								</tr>
								<tr>
									<td><?php echo _l('project_primary_contacts'); ?></td>
									<td><input type="checkbox" name="deal[]" value="primary_contact" <?php if (!empty($mandatory) && in_array("primary_contact", $mandatory)){ echo 'checked';}?>> Yes</td>
								</tr>
								<tr>
									<td><?php echo _l('pipeline'); ?></td>
									<td><input type="checkbox" name="deal[]" value="pipeline_id" <?php if (!empty($mandatory) && in_array("pipeline_id", $mandatory)){ echo 'checked';}?>> Yes</td>
								</tr>
								<tr>
									<td><?php echo _l('project_status'); ?></td>
									<td><input type="checkbox" name="deal[]" value="status" <?php if (!empty($mandatory) && in_array("status", $mandatory)){ echo 'checked';}?>> Yes</td>
								</tr>
								<tr>
									<td><?php echo _l('teamleader_name'); ?></td>
									<td><input type="checkbox" name="deal[]" value="teamleader" <?php if (!empty($mandatory) && in_array("teamleader", $mandatory)){ echo 'checked';}?>> Yes</td>
								</tr>
								<tr>
									<td><?php echo _l('task_single_followers'); ?></td>
									<td><input type="checkbox" name="deal[]" value="project_members[]" <?php if (!empty($mandatory) && in_array("project_members[]", $mandatory)){ echo 'checked';}?>> Yes</td>
								</tr>
								<tr>
									<td><?php echo _l('project_total_cost'); ?></td>
									<td><input type="checkbox" name="deal[]" value="project_cost" <?php if (!empty($mandatory) && in_array("project_cost", $mandatory)){ echo 'checked';}?>> Yes</td>
								</tr>
								<tr>
									<td><?php echo _l('start_date'); ?></td>
									<td><input type="checkbox" name="deal[]" value="start_date" <?php if (!empty($mandatory) && in_array("start_date", $mandatory)){ echo 'checked';}?>> Yes</td>
								</tr>
								<tr>
									<td><?php echo _l('end_date'); ?></td>
									<td><input type="checkbox" name="deal[]" value="deadline" <?php if (!empty($mandatory) && in_array("deadline", $mandatory)){ echo 'checked';}?>> Yes</td>
								</tr>
								<tr>
									<td><?php echo _l('tags'); ?></td>
									<td><input type="checkbox" name="deal[]" value="tags" <?php if (!empty($mandatory) && in_array("tags", $mandatory)){ echo 'checked';}?>> Yes</td>
								</tr>
								<tr>
									<td><?php echo _l('project_description'); ?></td>
									<td><input type="checkbox" name="deal[]" value="description" <?php if (!empty($mandatory) && in_array("description", $mandatory)){ echo 'checked';}?>> Yes</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
