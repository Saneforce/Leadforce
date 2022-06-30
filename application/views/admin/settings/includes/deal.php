<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div class="form-group mtop15">
	<label for="link_deal">Link With Deal Automatically</label><br />
	<select id="link_deal" name="link_deal" class="selectpicker" data-width="100%" onchange="link_deal1(this)">
		<option value="" ><?php echo _l('smtp_encryption_none'); ?></option>
		<option value="no" <?php if(get_option('link_deal') == 'no'){echo 'selected';} ?>>No</option>
		<option value="yes" <?php if(get_option('link_deal') == 'yes'){echo 'selected';} ?>>Yes</option>
	</select>
</div>
<div id="has_settings" <?php if(get_option('link_deal') == 'no'){?>style="display:none"<?php }?>>
	<div class="form-group mtop15">
		<label for="map_deal">Deal Mapping</label><br />
		<select id="map_deal" name="map_deal" class="selectpicker" data-width="100%" >
			<option value="" ><?php echo _l('smtp_encryption_none'); ?></option>
			<option value="first open deal" <?php if(get_option('deal_map') == 'first open deal'){echo 'selected';} ?>>First Open Deal</option>
			<option value="last open deal" <?php if(get_option('deal_map') == 'last open deal'){echo 'selected';} ?>>Last Open Deal</option>
			<option value="more activities available in open deal" <?php if(get_option('deal_map') == 'more activities available in open deal'){echo 'selected';} ?>>More Activities Available In Open Deal</option>
			<option value="if more than one open deal – allow to map manually" <?php if(get_option('deal_map') == 'if more than one open deal – allow to map manually'){echo 'selected';} ?>>If more than one open deal – Allow to map manually</option>
		</select>
		</select>
	</div>
</div>
<script >
function link_deal1(a){
	$('#has_settings').hide();
	if(a.value=='yes'){
		$('#has_settings').show();
	}
}
</script>