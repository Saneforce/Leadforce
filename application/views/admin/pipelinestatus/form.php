<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<?php echo form_open($this->uri->uri_string(),array('id'=>'pipelinestatus-form','onsubmit'=>'return valid_submit()')); ?>
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<div class="panel_s">
						<div class="panel-body">
							<h4 class="no-margin">
								<?php echo $title; ?>
							</h4>
							<?php if(isset($pipelinestatus)){ ?>
								<p>
									<?php if(has_permission('pipelinestatus','','create')){ ?>
										<a href="<?php echo admin_url('pipelinestatus/save'); ?>" class="btn btn-success pull-right"><?php echo _l('new_pipelinestatus'); ?></a>
									<?php } ?>
									<?php if(has_permission('pipelinestatus','','delete')){ /*?>
										<a href="<?php echo admin_url('pipelinestatus/delete_pipelinestatus/'.$pipelinestatus->id); ?>" class="btn btn-danger _delete pull-right mright5"><?php echo _l('delete'); ?></a>
									<?php */} ?>
									<div class="clearfix"></div>
								</p>
							<?php } ?>
							<hr class="hr-panel-heading" />
							<div class="clearfix"></div>
							<?php $value = (isset($pipelinestatus) ? $pipelinestatus->name : ''); ?>
							<?php $attrs = array('required' => true,'onblur'=>"change_name(this.value,'name')",'onkeyup'=>'check_name()','maxlength'=>50); ?>
							<?php echo render_input('name','name',$value,'text',$attrs); ?>
							<div class="text-danger" id="name_id" style="display:none">Please enter valid name</div>
							<?php $value = (isset($pipelinestatus) ? $pipelinestatus->statusorder : ''); ?>
							<?php $attrs = array('required' => true); ?>
							<?php echo render_input('statusorder','statusorder',$value,'number',$attrs); ?>

							<?php $value = (isset($pipelinestatus) ? $pipelinestatus->progress : ''); ?>
							<?php $attrs = array('required' => true,'min'=>0,'max'=>100); ?>
							<?php echo render_input('progress','progress',$value,'number',$attrs); ?>

							<?php $value = (isset($pipelinestatus) ? $pipelinestatus->color : ''); ?>
							<?php $attrs = array('class'=>' colorpicker-input','onkeyup'=>'check_pipline()','type'=>'text'); ?>
							<?php //echo render_color_picker('color','color',$value,'text',$attrs); ?>
							<?php echo render_color_picker('color','color',$value,$attrs); ?>
							<div class="text-danger" id="color_id" style="display:none">Please enter valid color</div>
							<!-- Status -->
							<!-- <div class="form-group select-placeholder">
								<label for="status" class="control-label"><?php echo _l('status'); ?></label>
								<select required="1" name="status" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('status'); ?>">
									<option value=""><?php echo _l('pipelinestatus_option_select'); ?></option>
									<option value="Active" <?php if(isset($pipelinestatus) && $pipelinestatus->status=='Active') { echo 'selected'; } ?>><?php echo _l('pipelinestatus_option_active'); ?></option>
									<option value="Inactive" <?php if(isset($pipelinestatus) && $pipelinestatus->status=='Inactive') { echo 'selected'; } ?>><?php echo _l('pipelinestatus_option_inactive'); ?></option>
								</select>
							</div> -->
						</div>
					</div>
				</div>
				<?php if((has_permission('pipelinestatus','','create') && !isset($pipelinestatus)) || has_permission('pipelinestatus','','edit') && isset($pipelinestatus)){ ?>
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
	appValidateForm($('#pipelinestatus-form'),{name:'required',status:'required'});
});
function valid_submit(){
	 var name_val = $('#color').val();
	 var cur_name_val = $('#name').val();
	$('#color_id').hide();
	$('#name_id').hide();
	if (  cur_name_val=='' || cur_name_val.match(/^[a-zA-Z0-9]+/)  ) {
	}else if ( cur_name_val==''  ) {
	} else {
		$('#name_id').show();
		return false;
	}
	
	if (  name_val=='' || name_val.match(/[^0-9a-zA-Z]/g)  ) {
	}else if ( name_val==''  ) {
	} else {
		$('#color_id').show();
		return false;
	}
	return true;
}
function check_pipline(){
	 var name_val = $('#color').val();
	$('#color_id').hide();
	if ( name_val.match(/[0-9a-zA-Z]/)  ) {
	}else if ( name_val==''  ) {
	} 
	else {
		$('#color_id').show();
	}
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
$(document).ready(function(){ 
  $("#color").keydown(function(event) {
     if (event.keyCode == 32) {
         event.preventDefault();
     }
	
  });
});
</script>
</body>
</html>