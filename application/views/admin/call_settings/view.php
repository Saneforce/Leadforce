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
	<label for="rtl_support_admin" class="control-label clearfix" style="font-weight:500; margin-right:10px;">
	Enable Call       : </label>
	<div class="radio radio-primary radio-inline">
		<input type="radio" id="enable_call" name="call_enable" value="1" <?php echo (($callsettings->enable_call == 1)?'checked':''); ?> onchange="enable_div(1);">
		<label for="enable_call">
			Yes            </label>
	</div>
	<div class="radio radio-primary radio-inline">
			<input type="radio" id="enable_call" name="call_enable" value="0" <?php echo $checked; ?>  <?php echo (($callsettings->enable_call == 0)?'checked':''); ?> onchange="enable_div(0);">
			<label for="enable_call">
				No                </label>
	</div>
</div>

<div id="enable_call_div" <?php echo $style; ?> >
	<div class="form-group">
		<label for="clients_default_theme" class="control-label">Vendor</label>
		<select name="source_from" id="source_from" class="form-control selectpicker"  onchange="check_call(this)">
			<option value="" >Select Vendor</option>
			<option value="telecmi" <?php if($callsettings->source_from=="telecmi"){ echo 'selected';}?>>Tele CMI</option>
			<option value="tata" <?php if($callsettings->source_from=="tata"){ echo 'selected';}?>>TATA Tele Services</option>
			<option value="daffytel" <?php if($callsettings->source_from=="daffytel"){ echo 'selected';}?>>Daffytel</option>
		</select>
	</div>
	<div class="form-group" >
		<label for="appid" class="control-label app_id" id="app_id" <?php if($callsettings->source_from != "telecmi"){?> style="display:none" <?php }?>>App Id</label>
		<label for="appid" class="control-label app_id" id="login_id" <?php if($callsettings->source_from !="tata"){?> style="display:none" <?php }?>>Login Id</label>
		<label for="appid" class="control-label app_id" id="accss_token" <?php if($callsettings->source_from !="daffytel"){?> style="display:none" <?php }?>>Access Token</label>
		<input type="text" id="appid" name="app_key" class="form-control" required placeholder="APP ID" value="<?php echo (isset($callsettings->app_id)?$callsettings->app_id:''); ?>">
	</div>
	<div id="ch_country" <?php if($callsettings->source_from !="daffytel"){?> style="display:none" <?php }?> class="form-group">
		<label for="appid" class="control-label " >Country</label>
		<select name="country_daffy" id="country_sel" class="form-control selectpicker"  data-live-search="true">
			<option value="" >Select Country</option>
			<?php if(!empty($countries)){
				foreach($countries as $country1){?>
					<option value="<?php echo $country1['calling_code'];?>" <?php if(!empty($callsettings->country_code) && $callsettings->country_code==$country1['calling_code']){ echo 'selected';}?>><?php echo $country1['short_name'];?>	</option>
				<?php }
			}?>
		</select>
		<div style="margin-top:15px">
			<label for="appsecret" class="control-label app_secret" >Webhook Id</label>
			<input type="text" id="webhook" name="webhook" class="form-control" required placeholder="WEBHOOK ID" value="<?php echo (isset($callsettings->webhook)?$callsettings->webhook:''); ?>">
		</div>
	</div> 
	<div class="form-group" >
		<label for="appsecret" class="control-label app_secret" id="app_secret" <?php if($callsettings->source_from != "telecmi"){?> style="display:none" <?php }?>>App Secret</label>
		<label for="appsecret" class="control-label app_secret" id="app_psw" <?php if($callsettings->source_from !="tata"){?> style="display:none" <?php }?>>Password</label>
		<label for="appsecret" class="control-label app_secret" id="bridge" <?php if($callsettings->source_from!="daffytel"){?> style="display:none" <?php }?>>Bridge No.</label>
		<input type="text" id="appsecret" name="app_secret" class="form-control" required placeholder="APP Secret" value="<?php echo (isset($callsettings->app_secret)?$callsettings->app_secret:''); ?>">
	</div>
	<div class="form-group" style="display:inline-flex;">
		<label for="rtl_support_admin" class="control-label clearfix" style="margin-right:10px;">
		Record Calls       : </label>
		<div class="radio radio-primary radio-inline">
			<input type="radio" id="recorder" name="recorder" value="1" <?php echo (($callsettings->recorder == 1)?'checked':''); ?>>
			<label for="recorder">
				Yes            </label>
		</div>
		<div class="radio radio-primary radio-inline">
				<input type="radio" id="recorder" name="recorder" value="0" <?php echo $checked; ?>  <?php echo (($callsettings->recorder == 0)?'checked':''); ?> >
				<label for="recorder">
					No                </label>
		</div>
	</div>
</div>

<script >
function enable_div(a){
	if(a == 1) {
		$('#enable_call_div').show();
	} else {
		$('#enable_call_div').hide();
	}
}
function check_call(a){
	$('#appid').val('');
	$('#webhook').val('');
	$('#appsecret').val('');
	$('#country_sel').val('');
	$("#country_sel").selectpicker('refresh');
	$('.app_id').hide();
	$('.app_secret').hide();
	$('#ch_country').hide();
	if(a.value=='telecmi'){
		$('#app_id').show();
		$('#app_secret').show();
		$('#appid').attr("placeholder", "APP ID");
		$('#appsecret').attr("placeholder", "App Secret");
	}
	else if(a.value=='daffytel'){
		$('#accss_token').show();
		$('#bridge').show();
		$('#ch_country').show();
		$('#country_sel').val('');
		$('#appid').attr("placeholder", "Access Token");
		$('#appsecret').attr("placeholder", "Bridge No.");
	}
	else{
		$('#login_id').show();
		$('#app_psw').show();
		$('#appid').attr("placeholder", "Login Id");
		$('#appsecret').attr("placeholder", "Password");
	}
}
</script>
<?php //init_tail(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"> </script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"> </script>  
<script>
// just for the demos, avoids form submit
$( "#settings-form" ).validate({

});
</script>
<style>
.error{
	color:#F3031C;
}
</style>