<div class="panel_s">
	<div class="panel-body">
		<h4 class="no-margin"><?php echo $title; ?></h4>
		<hr class="hr-panel-heading" />
		<div class="col-md-6 row">
		<form action="" method="post" id="company">
		  <div class="col-md-12 pipeselect">
			<div class="form-group">
			  <label class="control-label"><?php echo _l('target_view'); ?></label>
			  <div class="dropdown bootstrap-select" style="width: 100%;">
				<select id="target" name="target" class="target selectpicker" data-actions-box="1" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="false" tabindex="-98" required>
				  <option value="Calendar"  <?php if(get_option('target_company') == 'Calendar'){echo 'selected';} ?>>Calendar</option>
				  <option value="Finance Year" <?php if(get_option('target_company') == 'Finance Year'){echo 'selected';} ?>>Finance Year</option>
				</select>
			  </div>
			</div>
		  </div>
		  <div class="col-md-6">
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
			<button type="submit" value="Save" class="btn btn-primary" name="save">Save</button>
		  </div>
	  </form>
	</div>
  </div>
</div>