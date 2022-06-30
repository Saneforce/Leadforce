<?php defined('BASEPATH') or exit('No direct script access allowed');
$project_already_client_tooltip = '';
$project_is_client = 1;
if ($project['status'] == $status['id']) { ?>
<li data-project-id="<?php echo $project['id']; ?>"<?php echo $project_already_client_tooltip; ?> class="project-kan-ban<?php if($project_is_client && get_option('project_lock_after_convert_to_customer') == 1 && !is_admin()){echo ' not-sortable';} ?>">
	<div class="panel-body project-body">
		<div class="row">
			<div class="col-md-12 project-name">
				<a href="<?php echo admin_url('projects/view/'.$project['id']); ?>" onclick="init_project(<?php echo $project['id']; ?>);return false;" class="pull-left">
					<span class="inline-block mtop10 mbot10"><?php echo $project['project_name']; ?></span>
				</a>
				<div style="position: absolute;bottom: -10px;right: 10px;">
				<?php 
					if($project['taskscount'] > 0){
						$gas = $this->projects_model->get_activity_status($project['id']);
						//pre($gas);
						$today = $upcoming = $overdue = '';
						foreach($gas as $val) {
							$sdate = date('Y-m-d', strtotime($val['startdate'])); 
							if((strtotime($sdate) == strtotime(date('Y-m-d'))) && $val['status'] != 5) {
								$today = 3;
							}
							if((strtotime($sdate) > strtotime(date('Y-m-d'))) && $val['status'] != 5) {
								$upcoming = 1;
							}
							if((strtotime($sdate) < strtotime(date('Y-m-d'))) && $val['status'] != 5) {
								$overdue = 2;
							}
						}
					}
				?>
				<?php
					if($overdue == '' && $today == '' && $upcoming == ''){
						echo '<span style="color: #d2be19;" title="'._l('no_tasks_found').'" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span>';
					}
					if(!empty($overdue)){
						echo '<span style="color: red; " title="'._l('overdue_deal').'" class="pull-right"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i><span>';
					}
					if($overdue == '' && !empty($today)){
						echo '<span style="color: green; " title="'._l('today_deal').'" class="pull-right"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i><span>';
					}
					if($overdue == '' && $today == '' && !empty($upcoming)){
						echo '<span style="color: #ccc; " title="'._l('future_deal').'" class="pull-right"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i><span>';
					}
				?>
				</div>
			</div>
			<?php if($this->session->has_userdata('projects_kanban_view') && $this->session->userdata('projects_kanban_view') == 'true') { ?>
				<div class="col-md-6 text-muted">
					<small  class="text-dark"><?php echo _l('teamprojecter').': '.get_staff_full_name($project['teamprojecter']); ?></small>
				</div>
				<div class="col-md-6 text-right text-muted">
					
					
					<span class="mright5 mtop5 inline-block text-muted" data-toggle="tooltip" data-placement="left" data-title="<?php echo _l('projects_canban_notes',$project['total_notes']); ?>">
						<i class="fa fa-sticky-note-o"></i> <?php echo $project['total_notes']; ?>
					</span>
					<span class="mtop5 inline-block text-muted" data-placement="left" data-toggle="tooltip" data-title="<?php echo _l('project_kan_ban_attachments',$project['total_files']); ?>">
						<i class="fa fa-paperclip"></i>
						<?php echo $project['total_files']; ?>
					</span>
				</div>
				<?php if($project['tags']){ ?>
					<div class="col-md-12">
						<div class="mtop5 kanban-tags">
							<?php echo render_tags($project['tags']); ?>
						</div>
					</div>
				<?php } ?>
			<?php } ?>
			<a href="#" class="pull-right text-muted kan-ban-expand-top" onclick="slideToggle('#kan-ban-expand-<?php echo $project['id']; ?>'); return false;">
				<i class="fa fa-expand" aria-hidden="true"></i>
			</a>
			<div class="clearfix no-margin"></div>
			<div id="kan-ban-expand-<?php echo $project['id']; ?>" class="padding-10" style="display:none;">
				<div class="clearfix"></div>
				<hr class="hr-10" />
				<p class="text-muted project-field-heading"><?php echo _l('project_title'); ?></p>
				<p class="bold font-medium-xs"><?php echo ($project['title'] != '' ? $project['title'] : '-') ?></p>
				<p class="text-muted project-field-heading"><?php echo _l('project_add_edit_email'); ?></p>
				<p class="bold font-medium-xs"><?php echo ($project['email'] != '' ? '<a href="mailto:'.$project['email'].'">' . $project['email'].'</a>' : '-') ?></p>
				<p class="text-muted project-field-heading"><?php echo _l('project_website'); ?></p>
				<p class="bold font-medium-xs"><?php echo ($project['website'] != '' ? '<a href="'.maybe_add_http($project['website']).'" target="_blank">' . $project['website'].'</a>' : '-') ?></p>
				<p class="text-muted project-field-heading"><?php echo _l('project_add_edit_phonenumber'); ?></p>
				<p class="bold font-medium-xs"><?php echo ($project['phonenumber'] != '' ? '<a href="tel:'.$project['phonenumber'].'">' . $project['phonenumber'].'</a>' : '-') ?></p>
				<p class="text-muted project-field-heading"><?php echo _l('project_company'); ?></p>
				<p class="bold font-medium-xs"><?php echo ($project['company'] != '' ? $project['company'] : '-') ?></p>
				<p class="text-muted project-field-heading"><?php echo _l('project_address'); ?></p>
				<p class="bold font-medium-xs"><?php echo ($project['address'] != '' ? $project['address'] : '-') ?></p>
				<p class="text-muted project-field-heading"><?php echo _l('project_city'); ?></p>
				<p class="bold font-medium-xs"><?php echo ($project['city'] != '' ? $project['city'] : '-') ?></p>
				<p class="text-muted project-field-heading"><?php echo _l('project_state'); ?></p>
				<p class="bold font-medium-xs"><?php echo ($project['state'] != '' ? $project['state'] : '-') ?></p>
				<p class="text-muted project-field-heading"><?php echo _l('project_country'); ?></p>
				<p class="bold font-medium-xs"><?php echo ($project['country'] != 0 ? get_country($project['country'])->short_name : '-') ?></p>
				<p class="text-muted project-field-heading"><?php echo _l('project_zip'); ?></p>
				<p class="bold font-medium-xs"><?php echo ($project['zip'] != '' ? $project['zip'] : '-') ?></p>
			</div>
		</div>
   </div>
</li>
<?php }
