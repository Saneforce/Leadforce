<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<?php echo form_open($this->uri->uri_string(),array('id'=>'deallossreasons-form')); ?>
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<div class="panel_s">
						<div class="panel-body">
							<h4 class="no-margin">
								<?php echo $title; ?>
							</h4>
							<?php if(isset($DealLossReasons)){ ?>
								<p>
									<?php if(has_permission('DealLossReasons','','create')){ ?>
										<a href="<?php echo admin_url('DealLossReasons/save'); ?>" class="btn btn-success pull-right"><?php echo _l('new_deallossreasons'); ?></a>
									<?php } ?>
									<?php if(has_permission('DealLossReasons','','delete')){ ?>
										<a href="<?php echo admin_url('DealLossReasons/delete_deallossreasons/'.$DealLossReasons->id); ?>" class="btn btn-danger _delete pull-right mright5"><?php echo _l('delete'); ?></a>
									<?php } ?>
									<div class="clearfix"></div>
								</p>
							<?php } ?>
							<hr class="hr-panel-heading" />
							<div class="clearfix"></div>
							<?php $value = (isset($DealLossReasons) ? $DealLossReasons->name : ''); ?>
							<?php $attrs = array('required' => true); ?>
							<?php echo render_input('name','deallossreasons_name',$value,'text',$attrs); ?>
							<!-- Publish Status -->
							<div class="form-group select-placeholder">
								<label for="publishstatus" class="control-label"><?php echo _l('deallossreasonspublishstatus'); ?></label>
								<select required="1" name="publishstatus" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('deallossreasonspublishstatus'); ?>">
									<option value=""><?php echo _l('deallossreasons_option_select'); ?></option>
									<option value="1" <?php if(isset($DealLossReasons) && $DealLossReasons->publishstatus==1) { echo 'selected'; } ?>><?php echo _l('deallossreasons_option_yes'); ?></option>
									<option value="0" <?php if(isset($DealLossReasons) && $DealLossReasons->publishstatus==0) { echo 'selected'; } ?>><?php echo _l('deallossreasons_option_no'); ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<?php if((has_permission('deallossreasons','','create') && !isset($DealLossReasons)) || has_permission('deallossreasons','','edit') && isset($DealLossReasons)){ ?>
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
	appValidateForm($('#deallossreasons-form'),{name:'required',publishstatus:'required'});
});
</script>
</body>
</html>