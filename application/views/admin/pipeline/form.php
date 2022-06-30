<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<?php echo form_open($this->uri->uri_string(),array('id'=>'pipeline-form','onsubmit'=>'return check_validate()')); ?>
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<div class="panel_s">
						<div class="panel-body">
							<h4 class="no-margin">
								<?php echo $title; ?>
							</h4>
							<?php if(isset($pipeline)){ ?>
								<p>
									<?php if(has_permission('pipeline','','create')){ ?>
										<a href="<?php echo admin_url('pipeline/save'); ?>" class="btn btn-success pull-right"><?php echo _l('new_pipeline'); ?></a>
									<?php } ?>
									<?php if(has_permission('pipeline','','delete')){ ?>
										<!-- <a href="<?php echo admin_url('pipeline/delete_pipeline/'.$pipeline->id); ?>" class="btn btn-danger _delete pull-right mright5"><?php echo _l('delete'); ?></a> -->
									<?php } ?>
									<div class="clearfix"></div>
								</p>
							<?php } ?>
							<hr class="hr-panel-heading" />
							<div class="clearfix"></div>
							<?php $value = (isset($pipeline) ? $pipeline->name : ''); ?>
							<?php $attrs = array('required' => true,'onblur'=>"check_name(this.value,'name')",'onkeyup'=>"check_validate()", 'maxlength'=>"150"); ?>
							<?php echo render_input('name','pipeline_name',$value,'text',$attrs); ?>
							<div class="text-danger" id="name_id" style="display:none">Please enter valid name</div>
<!--							 <div class="form-group select-placeholder">
                            <label for="clientid" class="control-label"><small class="req text-danger">* </small><?php echo _l('project_customer'); ?></label>
                            <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                             <?php $selected = (isset($pipeline) ? $pipeline->clientid : '');
                             if($selected == ''){
                                 $selected = (isset($pipeline->clientid) ? $pipeline->clientid: '');
                             }
                             if($selected != ''){
                                $rel_data = get_relation_data('customer',$selected);
                                $rel_val = get_relation_values($rel_data,'customer');
                                echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                            } ?>
                        </select>
                    </div>-->
                                                        <!-- Status -->
							<div class="form-group select-placeholder" id="ch_id2">
								<label for="status" class="control-label required">Stages</label>
								<select required="true" data-actions-box="true" name="status[]" class="selectpicker" data-width="100%" multiple="true" onchange="get_stage(this)" id="pipe_status">
									<?php foreach($statuses as $status) { ?>
										<?php if(in_array($status['id'], explode(',',$pipeline->status))) { ?>
											<option value="<?php echo $status['id']; ?>" selected><?php echo $status['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $status['id']; ?>"><?php echo $status['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
							<div class="form-group select-placeholder">
								<label for="status" class="control-label required">Default Stage</label>
								<select required="true" data-actions-box="true" name="default_status" class="selectpicker" data-width="100%" id="default_status" onchange="get_default_stage(this)" >
									<option value="" >Nothing selected</option>
									<?php foreach($statuses as $status) { ?>
										<?php if(in_array($status['id'], $default_statuses)) { ?>
											<option value="<?php echo $status['id']; ?>" <?php if($status['id'] == $pipeline->default_status){ echo 'selected';}?>><?php echo $status['name']; ?></option>
										<?php }?>
									<?php }?>
								</select>
							</div>
							<!-- Team Leaders -->
							<!-- <div class="form-group select-placeholder">
								<label for="teamleader" class="control-label required"><?php echo _l('Pipeline Assign to'); ?></label>
								<select required="true" data-actions-box="true" name="teamleader[]"  id="teamleaderch"  class="selectpicker" data-width="100%" multiple="true">
									<?php foreach($teamleaders as $teamleader) { ?>
										<?php if(in_array($teamleader['id'], explode(',',$pipeline->teamleader))) { ?>
											<option value="<?php echo $teamleader['id']; ?>" selected><?php echo $teamleader['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $teamleader['id']; ?>"><?php echo $teamleader['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div> -->
							<!-- Team Members -->
							<!-- <div class="form-group select-placeholder" id="teammembers_div_container">
								<label for="teammembers" class="control-label"><?php echo _l('Pipeline Team Members'); ?></label>
								<select data-actions-box="true" name="teammembers[]" class="selectpicker" id="teammembersch" data-width="100%" multiple="true">
									<?php foreach($teammembers as $teammember) { ?>
										<?php if(in_array($teammember['id'], explode(',',$pipeline->teammembers))) { ?>
											<option value="<?php echo $teammember['id']; ?>" selected><?php echo $teammember['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $teammember['id']; ?>"><?php echo $teammember['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div> -->
							<!-- Publish Status -->
							<div class="form-group select-placeholder">
								<label for="publishstatus" class="control-label"><?php echo _l('pipelinepublishstatus'); ?></label>
								<select required="1" name="publishstatus" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('pipelinepublishstatus'); ?>">
									<option value=""><?php echo _l('pipeline_option_select'); ?></option>
									<option value="1" <?php if(isset($pipeline) && $pipeline->publishstatus==1) { echo 'selected'; } ?>><?php echo _l('pipeline_option_yes'); ?></option>
									<option value="0" <?php if(isset($pipeline) && $pipeline->publishstatus==0) { echo 'selected'; } ?>><?php echo _l('pipeline_option_no'); ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<?php if((has_permission('pipeline','','create') && !isset($pipeline)) || has_permission('pipeline','','edit') && isset($pipeline)){ ?>
					<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
						<button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
					</div>
				<?php } ?>
			</div>
		<?php echo form_close(); ?>
	</div>
</div>
<?php init_tail(); ?>
<script>
function check_name(a,ch_id){
	$('#'+ch_id).val(a.trim());
}
function check_validate(){
	var name_val = $('#name').val();
	$('#name_id').hide();
	if ( name_val.match(/^[a-zA-Z0-9]+/)  ) {
	} else {
		if(name_val!=''){
		$('#name_id').show();
		return false;
		}
	}
}
$(function(){
	init_editor('#description', {append_plugins: 'stickytoolbar'});
	appValidateForm($('#pipeline-form'),{name:'required',clientid:'required',status:'required',publishstatus:'required'});

	
	
    $('#teamleaderch').change(function() {
        var teamleader = $('#teamleaderch').val();
        $.ajax({
            url: admin_url + 'leads/changepipelineteamleader',
            type: 'POST',
            data: {
                'teamleader': teamleader
            },
            dataType: 'json',
            success: function success(result) {
                $('#teammembersch').selectpicker('destroy');
                $('#teammembersch').html(result.teammembers).selectpicker('refresh');
            }
        });
    });
});
function get_stage(a){
	var foo = $('#pipe_status').val(); 
	 $.ajax({
		url: admin_url + 'pipeline/get_stage',
		type: 'POST',
		data: {
			'stage': foo
		},
		dataType: 'json',
		success: function success(result) {
			$('#default_status').selectpicker('destroy');
			$('#default_status').html(result.stages).selectpicker('refresh');
			
			if(foo!=''){
				$('#pipe_status-error').html('');
				 $("#ch_id2").removeClass("has-error");
			}
			else{
				 $("#ch_id2").addClass("has-error");
				$('#pipe_status-error').html('This field is required.');
			}
			var foo1 = $('#default_status').val(); 
			if(foo1!=''){
				$('#default_status-error').html('');
			}
			else{
				$('#default_status-error').html('This field is required.');
			} 
		}
	});
}
function get_default_stage(a){
	var foo = $('#default_status').val(); 
	if(foo!=''){
		$('#default_status-error').html('');
	}
	else{
		$('#default_status-error').html('This field is required.');
	} 
}
</script>
</body>
</html>