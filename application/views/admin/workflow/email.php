<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<br>
<?php echo form_open(admin_url('workflow/saveconfig/'), array('id' => 'EmailConfig')); ?>
<div class="form-group">
    <label for="sendto" class="control-label">Send to</label>
    <select name="sendto" id="sendto" class="form-control" data-live-search="true" required>
        <option value="customer">Customer</option>
        <option value="staff">Staff</option>
    </select>
</div>

<?php echo render_input('subject', 'template_subject','','text',['data-cursor'=>0]); ?>
<?php echo render_input('fromname', 'template_fromname','','text',['data-cursor'=>0]); ?>
<hr />
<?php echo render_textarea('message', 'email_template_email_message', isset($configure['message']) ? $configure['message'] : '', array('data-url-converter-callback' => 'myCustomURLConverter','id'=>'EmailConfigMessage'), array(), '', 'tinymce tinymce-manual'); ?>
<br>
<button type="submit" class="btn btn-primary" id="saveEmailConfig">Save Configuration</button>
<?php echo form_close(); ?>
<style>
    #sidebarsetupemail #EmailConfig .dropdown-menu{
        padding: 10px 15px;
        height: 500px;
        overflow-y: auto;
        overflow-x: hidden;
    }
    #sidebarsetupemail #EmailConfig .dropdown-item{
        display: block;
        padding: 5px 10px;
    }
    #sidebarsetupemail #EmailConfig .add-placeholder-btn{
        float:right; 
        margin-top:5px;
        cursor: pointer;
    }

    #sidebarsetupemail #EmailConfig .click-to-copy{
        cursor: pointer;
    }
</style>
<script src="https://cdn.jsdelivr.net/clipboard.js/1.5.12/clipboard.min.js"></script>
<script>
    var placeholderSubjectCursorPos =0;
    document.addEventListener("DOMContentLoaded", function(event) {

        var placeholders = <?= json_encode($this->workflow_app->getMergeFields($moduleDetails['name']),JSON_PRETTY_PRINT); ?>;
        
        var placeholdershtml =`
        <a type="button" class="dropdown-toggle add-placeholder-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-plus" aria-hidden="true"></i>Add placeholder
        </a>
        <div class="dropdown-menu"><div class="row">`;
            $.each(placeholders, function(field, fieldsData) {
                placeholdershtml +=`<div class="col-md-12"><h5>`+fieldsData.name+`</h5></div>`;
                $.each(fieldsData.placeholders, function(placeholder, placeholderName) {
                    placeholdershtml +=`<div class="col-md-6"><a class="dropdown-item click-to-copy" data-placeholder="`+placeholder+`" data-toggle="tooltip" data-placement="bottom" title="Click to add">`+placeholderName+`  </a></div>`;
                });
                placeholdershtml +=`<hr class="hr-panel-heading">`;
            });
            placeholdershtml +=`</div>
        </div>`;

        $('#EmailConfig [name="subject"]').parent().append(`<div class="btn-group" style="width:100%" id="placeholderSubject">`+placeholdershtml+`<div>`);
        $('#EmailConfig [name="fromname"]').parent().append(`<div class="btn-group" style="width:100%" id="placeholderFromname">`+placeholdershtml+`<div>`);
        $('#EmailConfig [name="message"]').parent().append(`<div class="btn-group" style="width:100%" id="placeholderMessage">`+placeholdershtml+`<div>`);
        
        
        $('#EmailConfig [name="subject"]').blur(function(){
            $(this).attr('data-cursor',$(this).prop('selectionStart'));
        });

        $('#EmailConfig  #placeholderSubject .click-to-copy').click(function(){
            cursor =$('#EmailConfig [name="subject"]').attr('data-cursor');
            var v = $('#EmailConfig [name="subject"]').val();
            var textBefore = v.substring(0,  cursor);
            var textAfter  = v.substring(cursor, v.length);

            $('#EmailConfig [name="subject"]').val(textBefore + $(this).attr('data-placeholder') + textAfter);
        });

        $('#EmailConfig [name="fromname"]').blur(function(){
            $(this).attr('data-cursor',$(this).prop('selectionStart'));
        });

        $('#EmailConfig  #placeholderFromname .click-to-copy').click(function(){
            cursor =$('#EmailConfig [name="fromname"]').attr('data-cursor');
            var v = $('#EmailConfig [name="fromname"]').val();
            var textBefore = v.substring(0,  cursor);
            var textAfter  = v.substring(cursor, v.length);
            $('#EmailConfig [name="fromname"]').val(textBefore + $(this).attr('data-placeholder') + textAfter);
        });

        $('#EmailConfig  #placeholderFromname .click-to-copy').click(function(){

        });

        $('#EmailConfig #placeholderMessage .click-to-copy').click(function(){
            tinymce.activeEditor.execCommand('mceInsertContent', false, $(this).attr('data-placeholder'));
        });

        init_editor('[name="message"]', {
            urlconverter_callback: 'merge_field_format_url',
            setup: function (ed) {
                ed.on("change", function () {
                    tinymce.triggerSave();
                })
            },
        });

        appValidateForm($('#EmailConfig'),
            {
                subject:'required',
                fromname:'required',
                message:'required'
            },
            function(form) {
                $.ajax({
                    url: admin_url+'workflow/saveconfig/'+$('.tree .block.selected').attr('data-id'),
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if($('#EmailConfig [name="sendto"]').val() =='customer'){
                            var title ='Send to customer';
                        }else if($('#EmailConfig [name="sendto"]').val() =='staff'){
                            var title ='Send to staff';
                        }
                        var description =$('#EmailConfig [name="subject"]').val();
                        workflowl.updateBlockContent($('.tree .block.selected').attr('data-id'),title,description);
                        alert_float('success', 'Setup saved successfully.');
                    }            
                });
            }
        );
    });
</script>