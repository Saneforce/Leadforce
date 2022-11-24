<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $templates =$this->sms_model->getTemplates(); ?>
<br>
<?php echo form_open(admin_url('workflow/saveconfig/'), array('id' => 'SMSConfig','onsubmit="this.checkValidity()"')); ?>
<div class="form-group">
    <label for="sendto" class="control-label">Send to</label>
    <select name="sendto" id="sendto" class="form-control" required>
        <option value="customer">Customer</option>
        <option value="staff">Staff</option>
    </select>
</div>
<div class="form-group">
    <label for="template" class="control-label">Template</label>
    <select name="template" id="template" class="form-control selectpicker" data-live-search="true" required>
        <option value="">Select Template</option>
        <?php if($templates): ?>
            <?php foreach($templates as $template): ?>
                <option value="<?php echo $template->template_id ?>"><?php echo $template->name ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>
<div class="form-group">
    <label for="message" class="control-label"><?php echo _l('message') ?></label>
    <textarea id="message" name="message" class="form-control" rows="8" disabled></textarea>
</div>

<div id="bodyVariables">
</div>

<br>
<button type="submit" class="btn btn-primary" id="saveSMSConfig">Save Configuration</button>
<?php echo form_close(); ?>
<script>

function updateSMSTemplateDetails() {
        savedVariables =workflowl.getSmsVariables();
        var templateId = $('#SMSConfig #template').val();
        if (templateId) {
            $.ajax({
                url: '<?php echo admin_url('plugins/sms/getTemplate') ?>/' + templateId,
                type: "get",
                dataType: "json",
                success: function(response) {
                    if (response.success == true) {
                        $('#SMSConfig #bodyVariables').html('');
                        for (let index = 1; index <= response.data.variables; index++) {
                            let variableValue = '';
                            if (savedVariables && typeof savedVariables[index - 1] !='undefined') {
                                variableValue = savedVariables[index - 1];
                            }

                            var placeholderpicker = `<div class="btn-group placeholder-picker" data-targer-input="#variable_` + index + `" style="width:100%">` + workflowl.getPlaceHolderPicker() + `<div>`;

                            $('#SMSConfig #bodyVariables').append(`<div class="form-group">
                            <label for="variable_` + index + `" class="control-label">Variable {{` + index + `}}</label>
                            <input type="text" id="variable_` + index + `" value="` + variableValue + `" name="variable_` + index + `" class="form-control variablesfield">
                        ` + placeholderpicker + `</div>`);
                            $('#SMSConfig #variable_' + index).attr('required', "");
                        }
                        $('#SMSConfig #message').val(response.data.content);
                    } else {
                        alert_float('warning', response.msg);
                    }
                },
            });
        }
    }

    document.addEventListener("DOMContentLoaded", function(event) {

        appValidateForm(
            $('#SMSConfig'),
            {},
            function(form) {
                var sendto = $('#SMSConfig #sendto').val();
                var template = $('#SMSConfig #template').val();
                var variables = [];
                var variablesfield = $('#SMSConfig .variablesfield');
                $.each(variablesfield, function() {
                    variables.push($(this).val());
                });
                $('#SMSConfig .sms_form_errors').remove();
                $.ajax({
                    url: admin_url+'workflow/saveconfig/'+$('.tree .block.selected').attr('data-id'),
                    type: "post",
                    data: {
                        template: template,
                        variables: variables,
                        sendto: sendto,
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success == true) {
                            var title ='';
                            if($('#SMSConfig [name="sendto"]').val() =='customer'){
                                title ='Send to customer';
                            }else if($('#SMSConfig [name="sendto"]').val() =='staff'){
                                title ='Send to staff';
                            }

                            var description =$('#SMSConfig #template option[value="'+template+'"]').html();
                            workflowl.updateBlockContent($('.tree .block.selected').attr('data-id'),title,'Template : <b>'+description+'<b>');

                            alert_float('success', 'Setup saved successfully.');
                        } else {
                            alert_float('warning', response.msg);
                            $.each(response.errors, function(k, v) {
                                $('#WhatsappConfig [name="' + k + '"]').parent().append(`<p class="text-danger sms_form_errors">` + v + `</p>`)
                            });
                        }
                    },
                });
            }
        );

       $('#SMSConfig [name="template"]').change(function(){
            updateSMSTemplateDetails();
       }); 
    });
</script>
