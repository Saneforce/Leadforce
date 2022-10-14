<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open($this->uri->uri_string(), ['id' => 'whatsappSettingsForm']); ?>
                        <div class="form-group">
                            <label for="business_id" class="control-label"><small class="req text-danger">* </small>Business ID</label>
                            <input type="text" id="business_id" max="250" name="business_id" class="form-control" required placeholder="Business ID" value="<?php echo $settings['business_id']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="phonenumber_id" class="control-label"><small class="req text-danger">* </small>Phone number ID</label>
                            <input type="text" id="phonenumber_id" max="250" name="phonenumber_id" class="form-control" required placeholder="Phone number ID" value="<?php echo $settings['phonenumber_id'] ?>">
                        </div>

                        <div class="form-group">
                            <label for="user_access_token" class="control-label"><small class="req text-danger">* </small>Access Token</label>
                            <input type="text" id="user_access_token" name="user_access_token" class="form-control" required placeholder="Access Token" value="<?php echo $settings['user_access_token'] ?>">
                        </div>

                        <div class="form-group">
                            <label for="waba_id" class="control-label"><small class="req text-danger">* </small>WhatsApp Business Account ID</label>
                            <input type="text" id="waba_id" max="250" name="waba_id" class="form-control" required placeholder="WhatsApp Business Account ID" value="<?php echo $settings['waba_id'] ?>">
                        </div>

                        <div class="form-group">
							<label for="rtl_support_admin" class="control-label clearfix">Enable Whatsapp</label>
							<div class="radio radio-primary radio-inline">
								<input type="radio" id="activeyes" name="active" value="1" checked>
								<label for="activeyes">Yes</label>
							</div>
							<div class="radio radio-primary radio-inline">
								<input type="radio" id="activeno" name="active" value="0">
								<label for="activeno">No</label>
							</div>
						</div>

                        <button type="submit" class="btn btn-info pull-right" id="saveWhatsapp"><?php echo _l('submit'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>

    $('.delete_ivr_link').click(function(e) {
		if (confirm('Do you want to delete this?') ==false) {
			e.preventDefault();
		}
	});

    $('#saveWhatsapp').click(function(e) {
            e.preventDefault();
            var values = $('#whatsappSettingsForm').serialize();
            $('.whatsapp_form_errors').remove();
            $.ajax({
                url: $('#whatsappSettingsForm').attr('action'),
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
                            $('[name="'+k+'"]').parent().append(`<p class="text-danger whatsapp_form_errors">`+v+`</p>`)
                        });
                    }
                },
            });
        });
</script>
</body>

</html>