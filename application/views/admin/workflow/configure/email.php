<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?php echo form_open(admin_url('workflow/saveemailconfig/' . $flowdetails->id), array('id' => 'EmailConfig')); ?>
        <div class="row">
            <div class="col-md-6 col-md-offset-3">

                <div class="panel_s">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?php echo admin_url('workflow') ?>">Workflow</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo admin_url('workflow/flow/' . $workflow['action']) ?>"><?php echo $workflow['name'] ?></a></li>
                                <li class="breadcrumb-item active" aria-current="page">Email</li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo $service['name'] ?></li>
                            </ol>
                        </nav>
                        <?php echo render_input('subject', 'template_subject',isset($configure['subject'])?$configure['subject']:''); ?>
                        <?php echo render_input('fromname', 'template_fromname',isset($configure['fromname'])?$configure['fromname']:''); ?>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="plaintext" id="plaintext" <?php if (isset($configure['plaintext'])&&$configure['plaintext'] == 1) {echo 'checked';} ?>>
                            <label for="plaintext"><?php echo _l('send_as_plain_text'); ?></label>
                        </div>
                        <hr />
                        <p class="bold"><?php echo _l('email_template_email_message'); ?></p>
                        <?php echo render_textarea('message', '', isset($configure['message'])?$configure['message']:'', array('data-url-converter-callback' => 'myCustomURLConverter'), array(), '', 'tinymce tinymce-manual'); ?>

                        <div class="form-group">
                            <div class="row available_merge_fields_container">
                                <?php foreach ($service['mergeFields'] as $mergeFieldsKey => $mergeFields) : ?>
                                    <?php foreach ($mergeFields as $field) : ?>
                                        <div class="col-md-4 col-xs-6"><?php echo $field; ?></div>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <br>
                        <button type="button" class="btn btn-primary" id="saveEmailConfig">Save Configuration</button>
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        init_editor('textarea[name="message"]', {
            urlconverter_callback: 'merge_field_format_url'
        });
        $('#saveEmailConfig').click(function() {
            tinymce.triggerSave();
            var subject = $('#subject').val();
            var fromname = $('#fromname').val();

            if($("#plaintext").prop('checked') == true){
                var plaintext = 1;
            }else{
                var plaintext = 0;
            }

            
            var message = $('#message').val();
            $('.email_form_errors').remove();
            $.ajax({
                url: $('#EmailConfig').attr('action'),
                type: "post",
                data: {
                    subject: subject,
                    fromname: fromname,
                    plaintext: plaintext,
                    message: message
                },
                dataType: "json",
                success: function(response) {
                    if (response.success == true) {
                        alert_float('success', response.msg);
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        alert_float('warning', response.msg);
                        $.each(response.errors, function(k, v) {
                            $('[name="' + k + '"]').parent().append(`<p class="text-danger email_form_errors">` + v + `</p>`)
                        });
                    }
                },
            });
        });
    });
</script>
</body>

</html>