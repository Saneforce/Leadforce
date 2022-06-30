<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body tc-content">
						<h4 class="bold no-margin"><?php echo _l('pipeline_details'); ?></h4>
						<hr class="hr-panel-heading" />
						<div class="clearfix"></div>
						<div class="kb-pipeline">
							<div class="pipelinename">
								<b><?php echo _l('pipeline_name'); ?> </b><?php echo $pipeline->name; ?>
							</div>
<!--							<div class="pipelinename">
								<b><?php echo _l('project_customer'); ?> </b>
                                                                <?php
                                                                $selected = (isset($pipeline) ? $pipeline->clientid : '');
                                                                $rel_data = get_relation_data('customer',$selected);
                                $rel_val = get_relation_values($rel_data,'customer');
                                echo isset($rel_val['name'])?$rel_val['name']:'';
                                                                ?>
							</div>-->
							<div class="pipelineleadstatus">
								<?php $CI =& get_instance();
								$teamleaders = NULL;
								if(!empty($pipeline->teamleader)) {
									$teamleaderids = explode(',',$pipeline->teamleader);
									foreach($teamleaderids as $teamleaderid) {
										$teamleaderdetails = $CI->pipeline_model->getTeamleaderdetails($teamleaderid);
										$teamleaders .= $teamleaderdetails['name'].',';
									}
								}
								if(!empty($teamleaders)) {
									$teamleaders = rtrim($teamleaders,',');
								}
								?>
								<b><?php echo _l('Pipeline Assign to'); ?> </b><?php echo $teamleaders; ?>
							</div>
							<div class="pipelineleadstatus">
								<?php $teammembers = NULL;
								if(!empty($pipeline->teammembers)) {
									$teammemberids = explode(',',$pipeline->teammembers);
									foreach($teammemberids as $teammemberid) {
										$teammemberdetails = $CI->pipeline_model->getTeammemberdetails($teammemberid);
										$teammembers .= $teammemberdetails['name'].',';
									}
								}
								if(!empty($teammembers)) {
									$teammembers = rtrim($teammembers,',');
								} ?>
								<b><?php echo _l('Pipeline Team Members'); ?> </b><?php echo $teammembers; ?>
							</div>
							<div class="pipelineleadstatus">
								<?php $status = NULL;
								if(!empty($pipeline->status)) {
									$leadstatus = explode(',',$pipeline->status);
									foreach($leadstatus as $leadstat) {
										$leadstatusdetails = $CI->pipeline_model->getleadstatusName($leadstat);
										$status .= $leadstatusdetails->name.',';
									}
								}
								if(!empty($status)) {
									$status = rtrim($status,',');
								} ?>
								<b><?php echo _l('stages'); ?> </b><?php echo $status; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>