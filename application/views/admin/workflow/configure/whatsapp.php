<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?php echo form_open(admin_url('workflow/savewhatsappconfig/'.$flowdetails->id), array('id' => 'WhatsappConfig')); ?>
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                
                <div class="panel_s">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo admin_url('workflow') ?>">Workflow</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo admin_url('workflow/flow/'.$workflow['action']) ?>"><?php echo $workflow['name'] ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Whatsapp</li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo $service['name'] ?></li>
                        </ol>
                        </nav>
                        <div class="form-group">
                            <label for="template" class="control-label">Template</label>
                            <select name="template" id="template" class="form-control selectpicker" data-live-search="true" required>
                                <option value="">Select Template</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message" class="control-label"><?php echo _l('message') ?></label>
                            <textarea id="message" name="message" class="form-control" rows="8" disabled></textarea>
                        </div>
                        <div id="bodyVariables">
                        </div>
                        <div class="form-group">
                            <div class="row available_merge_fields_container">
                                <?php foreach ($service['mergeFields'] as $mergeFieldsKey => $mergeFields) : ?>
                                    <?php foreach ($mergeFields as $field) : ?>
                                        <div class="col-md-3 col-xs-2"><?php echo $field; ?></div>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <br>
                        <button type="button" class="btn btn-primary" id="saveWhatsappConfig">Save Configuration</button>
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>

    </div>
</div>
<?php init_tail(); ?>

<script>
    var savedTemplate = '<?php echo isset($configure['template'])?$configure['template']:false;?>';
    var savedVariables = <?php echo isset($configure['variables'])? json_encode($configure['variables']):[];?>;
    function updatetemplates() {
        $.ajax({
            url: '<?php echo admin_url('plugins/whatsapp/gettemplates') ?>',
            type: "get",
            dataType: "json",
            success: function(response) {
                $("#template option").remove();
                $('#template')
                    .append($("<option></option>")
                        .attr("value", '')
                        .text('Select Template'));
                $.each(response.data, function(key, value) {
                    if(savedTemplate == value.name){
                        $('#template')
                    .append($("<option selected></option>")
                        .attr("value", value.name)
                        .text(value.name));
                    }else{
                        $('#template')
                    .append($("<option></option>")
                        .attr("value", value.name)
                        .text(value.name));
                    }
                    
                });
                $("#template").selectpicker('refresh');
                updateTemplateDetails();
            },
        });
    }

    function updateTemplateDetails() {
        var templateName = $('#template').val();
        if(templateName){
            $.ajax({
                url: '<?php echo admin_url('plugins/whatsapp/gettemplate') ?>/' + templateName,
                type: "get",
                dataType: "json",
                success: function(response) {
                    if (response.success == true) {
                        $.each(response.data.components, function(key, value) {
                            if (value.type == 'BODY') {
                                $('#bodyVariables').html('');
                                for (let index = 1; index <= value.variables; index++) {
                                    let variableValue ='';
                                    if(savedVariables && savedTemplate == templateName){
                                        variableValue =savedVariables[index-1];
                                    }
                                    $('#bodyVariables').append(`<div class="form-group">
                                    <label for="message" class="control-label">Variable {{` + index + `}}</label>
                                    <input type="text" id="varibale_` + index + `" value="`+variableValue+`" max="250" name="varibale_` + index + `" class="form-control variablesfield" required placeholder="" required>
                                </div>`);
                                }
                                $('#message').val(value.text);
                            }
                        })

                    } else {
                        alert_float('warning', response.msg);
                    }
                },
            });
        }
    }
    $('#template').change(function() {
        updateTemplateDetails();
    });

    $('#saveWhatsappConfig').click(function() {
        var template =$('#template').val();
        var variables=[];
        var contacts =[];

        var variablesfield = $('.variablesfield');
        $.each(variablesfield, function() {
            variables.push($(this).val());
        });
        $('.whatsapp_form_errors').remove();
        $.ajax({
            url: $('#WhatsappConfig').attr('action'),
            type: "post",
            data: {template:template,variables:variables} ,
            dataType : "json",
            success: function (response) {
                if(response.success ==true){
                    alert_float('success', response.msg);
                    setTimeout(function(){
                        window.location ='<?php echo admin_url('workflow/flow/' . $workflow['action']) ?>';
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

    document.addEventListener("DOMContentLoaded", function(event) {
        updatetemplates();
    });
</script>
</body>

</html>