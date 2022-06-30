<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div class="form-group mtop15">
	<label for="email_local">Save Email In Local</label><br />
	<select id="email_local" name="email_local" class="selectpicker" data-width="100%" onchange="link_deal1(this)">
		<option value="" ><?php echo _l('smtp_encryption_none'); ?></option>
		<option value="no" <?php if(get_option('email_local') == 'no'){echo 'selected';} ?>>No</option>
		<option value="yes" <?php if(get_option('email_local') == 'yes'){echo 'selected';} ?>>Yes</option>
	</select>
</div>