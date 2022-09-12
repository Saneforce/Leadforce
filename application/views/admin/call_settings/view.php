<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


<button type="button" class="btn btn-primary" id="newIvrModel" style="float:right; margin-bottom:15px;">
+ Add IVR
</button>

<!-- call settings records -->
<div>
<table class="table dt-table scroll-responsive">
  	<thead>
    <tr>
		<th><?php echo _l('vendor'); ?></th>
		<th><?php echo _l('ivr_name'); ?></th>
		<th><?php echo _l('enable_call'); ?></th>
		<th><?php echo _l('options') ?></th>
    </tr>
  	</thead>
  	<tbody>
		<?php foreach($callsettings as $settings):?>
		<tr>
			<td data-order="<?php echo $settings->source_from; ?>"><?php echo (isset($vendors[$settings->source_from]))?$vendors[$settings->source_from]:'' ?></td>
			<td data-order="<?php echo $settings->ivr_name; ?>"><?php echo $settings->ivr_name ?></td>
			<td>
				<div class="onoffswitch">
					<input type="checkbox" data-switch-url="<?= admin_url('call_settings/change_status') ?>" name="onoffswitch" class="onoffswitch-checkbox" id="<?= $settings->id ?>" data-id="<?= $settings->id ?>" <?= ($settings->enable_call==1)?"checked":"" ?>>
					<label class="onoffswitch-label" for="<?= $settings->id ?>"></label>
				</div>
			</td>
			<td ><a href="javascript:void(0)" class="text-primary editIvrModel" data-id="<?= $settings->id ?>"><?= _l("edit") ?></a> | 
			<a href="<?= admin_url('call_settings/delete_ivr/'.$settings->id) ?>" class="text-danger delete_ivr_link"><?= _l("delete") ?></a></td>
		</tr>
		<?php endforeach; ?>
    </tbody>
   </table>
</div>
<!-- end call settings records -->


<div class="modal" id="ivrModal" style="display: none;">
    <div class="modal-dialog">
		<div class="modal-content">
			<?php echo form_open_multipart(
					(!isset($tab['update_url'])
					? $this->uri->uri_string() . '?group=' . $tab['slug'] . ($this->input->get('tab') ? '&active_tab=' . $this->input->get('tab') : '')
					: $tab['update_url']),
					['id' => 'ivrForm','autocomplete'=>'off', 'class' => isset($tab['update_url']) ? 'custom-update-url' : '']
				);
			?>
			<input type="hidden" name="id" value=0>
			<div class="modal-header">
				<span class="title">Add IVR </span>
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body" >
				<div id="enable_call_div" >
					<div class="form-group">
						<label for="clients_default_theme" class="control-label">Vendor</label>
						<select name="source_from" id="source_from" class="form-control selectpicker"  onchange="show_settings()">
							<option value="" >Select Vendor</option>
							<?php foreach($vendors as $key => $name): ?>
							<option value="<?= $key ?>" ><?= $name ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<!-- telecmi start -->
					<div class="settings_wrapper" id="telecmi_settings_wrapper" <?php echo ($callsettings->source_from !="telecmi")?'style="display:none"':'' ?>>
						<div class="form-group">
							<label for="telecmi_ivr_name" class="control-label app_id" id="app_id">IVR name</label>
							<input type="text" id="telecmi_ivr_name" max="250" name="telecmi_ivr_name" class="form-control" required placeholder="IVR name" value="<?php echo $callsettings->ivr_name; ?>">
						</div>

						<div class="form-group">
							<label for="telecmi_channel" class="control-label">Channel</label>
							<select name="telecmi_channel" id="telecmi_channel" class="form-control selectpicker">
								<option value="" >Select Channel</option>
								<option value="national" >Indian</option>
								<option value="national_softphone" >Indian (Virtual Business Phone System)</option>
								<option value="international_softphone" >International (Virtual Business Phone System)</option>
							</select>
						</div>

						<div class="form-group">
							<label for="appid" class="control-label app_id" id="app_id">App Id</label>
							<input type="text" id="appid" required name="telecmi_app_key" class="form-control" required placeholder="APP ID" >
						</div>

						<div class="form-group" >
							<label for="appsecret" class="control-label app_secret" id="app_secret">App Secret</label>
							<input type="text" id="appsecret" name="telecmi_app_secret" class="form-control" required placeholder="APP Secret" >
						</div>

						<div class="form-group" style="display:inline-flex;">
							<label for="rtl_support_admin" class="control-label clearfix" style="margin-right:10px;">Record Calls: </label>
							<div class="radio radio-primary radio-inline">
								<input type="radio" id="telecmi_recorderyes" name="telecmi_recorder" value="1" >
								<label for="telecmi_recorderes">Yes</label>
							</div>
							<div class="radio radio-primary radio-inline">
								<input type="radio" id="telecmi_recorderno" name="telecmi_recorder" value="0" checked>
								<label for="telecmi_recorderno">No</label>
							</div>
						</div>
					</div>
					<!-- telecmi end -->

					<!-- tata start -->
					<div class="settings_wrapper" id="tata_settings_wrapper" <?php echo ($callsettings->source_from !="tata")?'style="display:none"':'' ?>>
						<div class="form-group">
							<label for="tata_ivr_name" class="control-label app_id" id="app_id">IVR name</label>
							<input type="text" id="tata_ivr_name" max="250" name="tata_ivr_name" class="form-control" required placeholder="IVR name" >
						</div>

						<div class="form-group">
							<label for="tata_appid" class="control-label app_id" id="app_id">Login Id</label>
							<input type="text" id="tata_appid" name="tata_app_key" class="form-control" required placeholder="APP ID" >
						</div>

						<div class="form-group" >
							<label for="appsecret" class="control-label app_secret" id="app_secret">Password</label>
							<input type="text" id="appsecret" name="tata_app_secret" class="form-control" required placeholder="APP Secret">
						</div>

						<div class="form-group" style="display:inline-flex;">
							<label for="rtl_support_admin" class="control-label clearfix" style="margin-right:10px;">Record Calls: </label>
							<div class="radio radio-primary radio-inline">
								<input type="radio" id="tata_recorderyes" name="tata_recorder" value="1" >
								<label for="tata_recorderyes">Yes</label>
							</div>
							<div class="radio radio-primary radio-inline">
								<input type="radio" id="tata_recorderno" name="tata_recorder" value="0" checked>
								<label for="tata_recorderno">No</label>
							</div>
						</div>
					</div>
					<!-- tata end -->

					<!-- daffytel start -->
					<div class="settings_wrapper" id="daffytel_settings_wrapper" <?php echo ($callsettings->source_from !="daffytel")?'style="display:none"':'' ?>>
						<div class="form-group">
							<label for="daffytel_ivr_name" class="control-label app_id" id="app_id">IVR name</label>
							<input type="text" id="daffytel_ivr_name" max="250" name="daffytel_ivr_name" class="form-control" required placeholder="IVR name" >
						</div>

						<div class="form-group">
							<label for="appid" class="control-label app_id" id="app_id">Access Token</label>
							<input type="text" id="appid" name="daffytel_app_key" class="form-control" required placeholder="Access token">
						</div>

						<div id="ch_country" class="form-group">
							<label for="appid" class="control-label " >Country</label>
							<select name="daffytel_country_daffy" id="country_sel" class="form-control selectpicker"  data-live-search="true">
								<option value="" >Select Country</option>
								<?php if(!empty($countries)){
									foreach($countries as $country1){?>
										<option value="<?php echo $country1['calling_code'];?>" ><?php echo $country1['short_name'];?>	</option>
									<?php }
								}?>
							</select>
							<div style="margin-top:15px">
								<label for="daffytel_appsecret" class="control-label app_secret" >Webhook Id</label>
								<input type="text" id="daffytel_webhook" name="daffytel_webhook" class="form-control" required placeholder="WEBHOOK ID" >
							</div>
						</div>

						<div class="form-group" >
							<label for="appsecret" class="control-label app_secret" id="bridge">Bridge No.</label>
							<input type="text" id="appsecret" name="daffytel_app_secret" class="form-control" required placeholder="APP Secret" >
						</div>

						<div class="form-group" style="display:inline-flex;">
							<label for="rtl_support_admin" class="control-label clearfix" style="margin-right:10px;">Record Calls: </label>
							<div class="radio radio-primary radio-inline">
								<input type="radio" id="daffytel_recorderyes" name="daffytel_recorder" value="1">
								<label for="daffytel_recorderyes">Yes</label>
							</div>
							<div class="radio radio-primary radio-inline">
								<input type="radio" id="daffytel_recorderno" name="daffytel_recorder" value="0" checked>
								<label for="daffytel_recorderno">No</label>
							</div>
						</div>`

					</div>
					<!-- daffytel end -->

					<!-- knowlarity start -->
					<div class="settings_wrapper" id="knowlarity_settings_wrapper" <?php echo ($callsettings->source_from !="knowlarity")?'style="display:none"':'' ?>>
						<div class="form-group">
							<label for="knowlarity_ivr_name" class="control-label app_id" id="app_id">IVR name</label>
							<input type="text" id="knowlarity_ivr_name" max="250" name="knowlarity_ivr_name" class="form-control" required placeholder="IVR name">
						</div>

						<div class="form-group">
							<label for="appid" class="control-label app_id" id="app_id">Access Key (x-api-key)</label>
							<input type="text" id="appid" name="knowlarity_app_key" class="form-control" required placeholder="APP ID" >
						</div>

						<div class="form-group" >
							<label for="appsecret" class="control-label app_secret" id="app_secret">Authorization Key (api key)</label>
							<input type="text" id="appsecret" name="knowlarity_app_secret" class="form-control" required placeholder="APP Secret">
						</div>
						<div class="form-group">
							<label for="clients_default_theme" class="control-label">Channel</label>
							<select name="knowlarity_channel" id="channel" class="form-control selectpicker" required>
								<option value="" >Select Channel</option>
								<option value="Basic" >Basic</option>
								<option value="Advance" >Advance</option>
								<option value="Premium" >Premium</option>
								<option value="Enterprise" >Enterprise</option>
							</select>
						</div>

						<div class="form-group" style="display:inline-flex;">
							<label for="rtl_support_admin" class="control-label clearfix" style="margin-right:10px;">Record Calls: </label>
							<div class="radio radio-primary radio-inline">
								<input type="radio" id="knowlarity_recorderyes" name="knowlarity_recorder" value="1">
								<label for="knowlarity_recorderyes">Yes</label>
							</div>
							<div class="radio radio-primary radio-inline">
								<input type="radio" id="knowlarity_recorderno" name="knowlarity_recorder" value="0" checked>
								<label for="knowlarity_recorderno">No</label>
							</div>
						</div>
					</div>
					<!-- knowlarity end -->
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="save_ivr">Save</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
			<?php echo form_close(); ?>
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
	if(source_from){
		$('#'+source_from+'_settings_wrapper').show();
	}
	
}
</script>
<?php //init_tail(); ?>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"> </script>   -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"> </script>   -->
<script>
// just for the demos, avoids form submit

document.addEventListener("DOMContentLoaded", function(event) { 
	
	$('.delete_ivr_link').click(function(e) {
		if (confirm('Do you want to delete this?') ==false) {
			e.preventDefault();
		}
	});
	$('#newIvrModel').click(function(){
		$('.ivr_form_errors').remove();
		$('select[name=source_from]').val('');
		$('[name="source_from"]').removeAttr('disabled');
		$('[name="source_from"]').selectpicker('refresh');
		$('[name="id"]').val(0);
		$('#ivrForm .modal-header .title').html('Add IVR');
		$('#ivrForm').trigger('reset');
		show_settings();
		$('#ivrModal').modal('show');
	});
	$('.editIvrModel').click(function(){
		$('[name="id"]').val($(this).attr('data-id'));
		$('.ivr_form_errors').remove();
		$('[name="source_from"]').removeAttr('disabled');
		$.ajax({
			url: '<?= admin_url('call_settings/getIvr/') ?>'+$(this).attr('data-id'),
			type: "get",
			dataType : "json",
			success: function (response) {
				if(response.success ==true){
					$('#ivrForm .modal-header .title').html('Edit IVR');
					$('#ivrModal').modal('show');
					$('[name="source_from"]').attr('disabled',true);
					$('select[name=source_from]').val(response.data.source_from);
					$('[name=source_from]').selectpicker('refresh');
					
					show_settings();
					$('[name="'+response.data.source_from+'_ivr_name"]').val(response.data.ivr_name);
					$('[name="'+response.data.source_from+'_app_key"]').val(response.data.app_id);
					$('[name="'+response.data.source_from+'_app_secret"]').val(response.data.app_secret);
					$('[name="'+response.data.source_from+'_channel"]').val(response.data.channel);
					$('[name="'+response.data.source_from+'_channel"]').selectpicker('refresh');
					$('[name="'+response.data.source_from+'_webhook"]').val(response.data.webhook);
					// $('[name="'+response.data.source_from+'_recorder"]').removeAttr('checked');
					
					if(response.data.recorder ==1){
						$('#'+response.data.source_from+'_recorderyes').attr('checked',true);
					}else{
						$('#'+response.data.source_from+'_recorderno').attr('checked',true);
					}
				}else{
					alert_float('warning', response.msg);
				}
			},
		});
	})

	$('#save_ivr').click(function() {
		var values = $('#ivrForm').serialize();
		var source_from =$('[name="source_from"]').val();
		$('.ivr_form_errors').remove();
		$.ajax({
			url: $('#ivrForm').attr('action'),
			type: "post",
			data: values ,
			dataType : "json",
			success: function (response) {
				if(response.success ==true){
					alert_float('success', response.msg);
					setTimeout(function(){
						window.location.reload();
					},1000);
				}else{
					alert_float('warning', response.msg);
					$.each(response.errors, function(k, v) {
						$('[name="'+k+'"]').parent().append(`<p class="text-danger ivr_form_errors">`+v+`</p>`)
					});
				}
			},
		});
	});

});


</script>
<style>
.error{
	color:#F3031C;
}
</style>