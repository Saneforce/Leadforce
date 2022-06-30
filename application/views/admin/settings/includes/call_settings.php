<?php defined('BASEPATH') or exit('No direct script access allowed');
 ?>

<div class="form-group">
	<label for="rtl_support_admin" class="control-label clearfix">
	Enable Call        </label>
	<div class="radio radio-primary radio-inline">
		<input type="radio" id="enable_call" name="enable_call" value="1">
		<label for="enable_call">
			Yes            </label>
	</div>
	<div class="radio radio-primary radio-inline">
			<input type="radio" id="enable_call" name="enable_call" value="0" checked="">
			<label for="enable_call">
				No                </label>
	</div>
</div>

<script >
function mail_server1(a){
	$('#have_server').hide();
	if(a.value=='yes'){
		$('#have_server').show();
	}
}
</script>
