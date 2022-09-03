<?php defined('BASEPATH') or exit('No direct script access allowed');
if(empty($callsettings) || $callsettings->enable_call == '0') {
	$style = ' style="display:none;"';
	$checked = 'checked';
} else {
	$style = ' style="display:block;"';
	$checked = '';
}
//pre($callsettings->enable_call);
 ?>
<input type="hidden" name="id" value="<?php echo (($callsettings->id) ? $callsettings->id : ''); ?>">

<div class="form-group" style="display:inline-flex;">
	<label for="rtl_support_admin" class="control-label clearfix" style="font-weight:500; margin-right:10px;">Enable Call:</label>
	<div class="radio radio-primary radio-inline">
		<input type="radio" id="enable_call" name="call_enable" value="1" <?php echo (($callsettings->enable_call == 1)?'checked':''); ?> onchange="enable_div(1);">
		<label for="enable_call">Yes</label>
	</div>
	<div class="radio radio-primary radio-inline">
			<input type="radio" id="disable_call" name="call_enable" value="0" <?php echo $checked; ?>  <?php echo (($callsettings->enable_call == 0)?'checked':''); ?> onchange="enable_div(0);">
			<label for="disable_call">No</label>
	</div>
</div>

<div id="enable_call_div" <?php echo $style; ?> >
	<div class="form-group">
		<label for="clients_default_theme" class="control-label">Vendor</label>
		<select name="source_from" id="source_from" class="form-control selectpicker"  onchange="show_settings()">
			<option value="" >Select Vendor</option>
			<option value="telecmi" <?php if($callsettings->source_from=="telecmi"){ echo 'selected';}?>>Tele CMI</option>
			<option value="tata" <?php if($callsettings->source_from=="tata"){ echo 'selected';}?>>TATA Tele Services</option>
			<option value="daffytel" <?php if($callsettings->source_from=="daffytel"){ echo 'selected';}?>>Daffytel</option>
			<!-- <option value="knowlarity" <?php if($callsettings->source_from=="knowlarity"){ echo 'selected';}?>>Knowlarity</option> -->
		</select>
	</div>


	<!-- telecmi start -->
	<div class="settings_wrapper" id="telecmi_settings_wrapper" <?php echo ($callsettings->source_from !="telecmi")?'style="display:none"':'' ?>>

		<div class="form-group">
			<label for="telecmi_channel" class="control-label">Channel</label>
			<select name="telecmi_channel" id="telecmi_channel" class="form-control selectpicker">
				<option value="" >Select Channel</option>
				<option value="national" <?php if($callsettings->source_from =="telecmi" && $callsettings->channel=="national"){ echo 'selected';}?>>National</option>
				<option value="national_softphone" <?php if($callsettings->source_from =="telecmi" && $callsettings->channel=="national_softphone"){ echo 'selected';}?>>National (Virtual Business Phone System)</option>
				<option value="international_softphone" <?php if($callsettings->source_from =="telecmi" && $callsettings->channel=="international_softphone"){ echo 'selected';}?>>International (Virtual Business Phone System)</option>
			</select>
		</div>

		<div class="form-group">
			<label for="appid" class="control-label app_id" id="app_id">App Id</label>
			<input type="text" id="appid" name="telecmi_app_key" class="form-control" required placeholder="APP ID" value="<?php echo ($callsettings->source_from =="telecmi" && isset($callsettings->app_id)?$callsettings->app_id:''); ?>">
		</div>

		<div class="form-group" >
			<label for="appsecret" class="control-label app_secret" id="app_secret">App Secret</label>
			<input type="text" id="appsecret" name="telecmi_app_secret" class="form-control" required placeholder="APP Secret" value="<?php echo ($callsettings->source_from =="telecmi" && isset($callsettings->app_secret)?$callsettings->app_secret:''); ?>">
		</div>

	</div>
	<!-- telecmi end -->

	<!-- tata start -->
	<div class="settings_wrapper" id="tata_settings_wrapper" <?php echo ($callsettings->source_from !="tata")?'style="display:none"':'' ?>>
		<div class="form-group">
			<label for="appid" class="control-label app_id" id="app_id">Login Id</label>
			<input type="text" id="appid" name="tata_app_key" class="form-control" required placeholder="APP ID" value="<?php echo ($callsettings->source_from =="tata" && isset($callsettings->app_id)?$callsettings->app_id:''); ?>">
		</div>

		<div class="form-group" >
			<label for="appsecret" class="control-label app_secret" id="app_secret">Password</label>
			<input type="text" id="appsecret" name="tata_app_secret" class="form-control" required placeholder="APP Secret" value="<?php echo ($callsettings->source_from =="tata" && isset($callsettings->app_secret)?$callsettings->app_secret:''); ?>">
		</div>
	</div>
	<!-- tata end -->

	<!-- daffytel start -->
	<div class="settings_wrapper" id="daffytel_settings_wrapper" <?php echo ($callsettings->source_from !="daffytel")?'style="display:none"':'' ?>>
		<div class="form-group">
			<label for="appid" class="control-label app_id" id="app_id">Access Token</label>
			<input type="text" id="appid" name="daffytel_app_key" class="form-control" required placeholder="Access token" value="<?php echo ($callsettings->source_from =="daffytel" && isset($callsettings->app_id)?$callsettings->app_id:''); ?>">
		</div>

		<div id="ch_country" class="form-group">
			<label for="appid" class="control-label " >Country</label>
			<select name="daffytel_country_daffy" id="country_sel" class="form-control selectpicker"  data-live-search="true">
				<option value="" >Select Country</option>
				<?php if(!empty($countries)){
					foreach($countries as $country1){?>
						<option value="<?php echo $country1['calling_code'];?>" <?php if($callsettings->source_from =="daffytel" && !empty($callsettings->country_code) && $callsettings->country_code==$country1['calling_code']){ echo 'selected';}?>><?php echo $country1['short_name'];?>	</option>
					<?php }
				}?>
			</select>
			<div style="margin-top:15px">
				<label for="daffytel_appsecret" class="control-label app_secret" >Webhook Id</label>
				<input type="text" id="webhook" name="webhook" class="form-control" required placeholder="WEBHOOK ID" value="<?php echo ($callsettings->source_from =="daffytel" && isset($callsettings->webhook)?$callsettings->webhook:''); ?>">
			</div>
		</div>

		<div class="form-group" >
			<label for="appsecret" class="control-label app_secret" id="bridge">Bridge No.</label>
			<input type="text" id="appsecret" name="daffytel_app_secret" class="form-control" required placeholder="APP Secret" value="<?php echo ($callsettings->source_from =="daffytel" && isset($callsettings->app_secret)?$callsettings->app_secret:''); ?>">
		</div>

	</div>
	<!-- daffytel end -->

	<!-- knowlarity start -->
	<div class="settings_wrapper" id="knowlarity_settings_wrapper" <?php echo ($callsettings->source_from !="knowlarity")?'style="display:none"':'' ?>>
		<div class="form-group">
			<label for="appid" class="control-label app_id" id="app_id">Access Key (x-api-key)</label>
			<input type="text" id="appid" name="knowlarity_app_key" class="form-control" required placeholder="APP ID" value="<?php echo ($callsettings->source_from =="knowlarity" && isset($callsettings->app_id)?$callsettings->app_id:''); ?>">
		</div>

		<div class="form-group" >
			<label for="appsecret" class="control-label app_secret" id="app_secret">Authorization Key (api key)</label>
			<input type="text" id="appsecret" name="knowlarity_app_secret" class="form-control" required placeholder="APP Secret" value="<?php echo ($callsettings->source_from =="knowlarity" && isset($callsettings->app_secret)?$callsettings->app_secret:''); ?>">
		</div>
		<div class="form-group">
			<label for="clients_default_theme" class="control-label">Channel</label>
			<select name="knowlarity_channel" id="channel" class="form-control selectpicker" required>
				<option value="" >Select Channel</option>
				<option value="Basic" <?php if($callsettings->source_from =="knowlarity" && $callsettings->channel=="Basic"){ echo 'selected';}?>>Basic</option>
				<option value="Advance" <?php if($callsettings->source_from =="knowlarity" && $callsettings->channel=="Advance"){ echo 'selected';}?>>Advance</option>
				<option value="Premium" <?php if($callsettings->source_from =="knowlarity" && $callsettings->channel=="Premium"){ echo 'selected';}?>>Premium</option>
				<option value="Enterprise" <?php if($callsettings->source_from =="knowlarity" && $callsettings->channel=="Enterprise"){ echo 'selected';}?>>Enterprise</option>
			</select>
		</div>
	</div>
	<!-- knowlarity end -->
	
	<div class="form-group" style="display:inline-flex;">
		<label for="rtl_support_admin" class="control-label clearfix" style="margin-right:10px;">Record Calls: </label>
		<div class="radio radio-primary radio-inline">
			<input type="radio" id="recorderyes" name="recorder" value="1" <?php echo (($callsettings->recorder == 1)?'checked':''); ?>>
			<label for="recorderyes">Yes</label>
		</div>
		<div class="radio radio-primary radio-inline">
			<input type="radio" id="recorderno" name="recorder" value="0" <?php echo $checked; ?>  <?php echo (($callsettings->recorder == 0)?'checked':''); ?> >
			<label for="recorderno">No</label>
		</div>
	</div>
</div>
<?php 

$CI = &get_instance();
$CI->app_scripts->add('jquery validator', [
     'path'       => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js',
 ]);

?>
<script >
var callSettings =JSON.parse('<?php echo json_encode($callsettings); ?>');


function show_settings(){
	$('.settings_wrapper').hide();
	var source_from =$('[name="source_from"]').val();
	if(source_from ==''){
		source_from ='telecmi';
	}
	$('#'+source_from+'_settings_wrapper').show();
}
function enable_div(a){
	if(a == 1) {
		$('#enable_call_div').show();
	} else {
		$('#enable_call_div').hide();
	}
}
</script>
<?php //init_tail(); ?>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"> </script>   -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"> </script>   -->
<script>
// just for the demos, avoids form submit

document.addEventListener("DOMContentLoaded", function(event) { 
    $( "#settings-form" ).validate({

	});
});


</script>
<style>
.error{
	color:#F3031C;
}
</style>