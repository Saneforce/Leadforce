<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<?php echo form_open($this->uri->uri_string(),array('id'=>'tasktype-form','onsubmit'=>'return valid_submit()')); ?>
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<div class="panel_s">
						<div class="panel-body">
							<h4 class="no-margin">
								<?php echo $title; ?>
							</h4>
							<?php if(isset($tasktype)){ ?>
								<p>
									<?php if(has_permission('tasktype','','create')){ ?>
										<a href="<?php echo admin_url('tasktype/save'); ?>" class="btn btn-success pull-right"><?php echo _l('new_tasktype'); ?></a>
									<?php } ?>
									<?php if(has_permission('tasktype','','delete') && $tasktype->id != 1){ ?>
										<a href="<?php echo admin_url('tasktype/delete_tasktype/'.$tasktype->id); ?>" class="btn btn-danger _delete pull-right mright5"><?php echo _l('delete'); ?></a>
									<?php } ?>
									<div class="clearfix"></div>
								</p>
							<?php } ?>
							<hr class="hr-panel-heading" />
							<div class="clearfix"></div>
							<?php $value = (isset($tasktype) ? $tasktype->name : ''); ?>
							<?php $attrs = array('required' => true,'onblur'=>"change_name(this.value,'name')",'onkeyup'=>'check_name()','maxlength'=>50); ?>
							<?php echo render_input('name','name',$value,'text',$attrs); ?>
							<div class="text-danger" id="name_id" style="display:none">Please enter valid name</div>
							<!-- Status -->
							<?php if($tasktype->id != 1) { ?>
							<div class="form-group select-placeholder">
								<label for="status" class="control-label"><?php echo _l('status'); ?></label>
								<select required="1" name="status" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('status'); ?>">
									<option value=""><?php echo _l('tasktype_option_select'); ?></option>
									<option value="Active" <?php if(isset($tasktype) && $tasktype->status=='Active') { echo 'selected'; } ?>><?php echo _l('tasktype_option_active'); ?></option>
									<option value="Inactive" <?php if(isset($tasktype) && $tasktype->status=='Inactive') { echo 'selected'; } ?>><?php echo _l('tasktype_option_inactive'); ?></option>
								</select>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php if((has_permission('tasktype','','create') && !isset($tasktype)) || has_permission('tasktype','','edit') && isset($tasktype)){ ?>
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
$(function(){
	appValidateForm($('#tasktype-form'),{name:'required',status:'required'});
});
function valid_submit(){
	 var name_val = $('#name').val();
	$('#name_id').hide();
	if (  name_val=='' ||  name_val.match(/^[a-zA-Z0-9]+/)  ) {
	}else if ( name_val==''  ) {
	} else {
		$('#name_id').show();
		return false;
	}
	return true;
}
function check_name(){
	 var name_val = $('#name').val();
	$('#name_id').hide();
	if (  name_val.match(/^[a-zA-Z0-9]+/)  ) {
	}else if ( name_val==''  ) {
	} 
	else {
		$('#name_id').show();
	}
}
function change_name(a,ch_id){
	$('#'+ch_id).val(a.trim());
}
</script>
</body>
</html>