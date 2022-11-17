<br>
<?php echo form_open(admin_url('workflow/saveconfig/'), array('id' => 'ApprovalConfig')); ?>
<div class="form-group">
    <label for="approver" class="control-label">Select Approver</label>
    <select name="approver" id="approver" class="form-control" data-live-search="true" required>
    <option value="">Select Approver</option>
    <option value="0">Reporting Level 0</option>
    <?php foreach($staffs as $staffid => $staffname): ?>
        <option value="<?php echo $staffid ?>"><?php echo $staffname ?></option>
    <?php endforeach; ?>
    </select>
</div>
<button type="submit" class="btn btn-primary" id="saveEmailConfig">Save Configuration</button>
<?php echo form_close(); ?>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {

        appValidateForm($('#ApprovalConfig'),
            {
                approver:'required',
            },
            function(form) {
                $.ajax({
                    url: admin_url+'workflow/saveconfig/'+$('.tree .block.selected').attr('data-id'),
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        var description ='Assigned to <b>'+$('#ApprovalConfig [name="approver"] option[value='+$('#ApprovalConfig [name="approver"]').val()+']').html()+`</b>`;
                        workflowl.updateBlockContent($('.tree .block.selected').attr('data-id'),'',description);
                        alert_float('success', 'Setup saved successfully.');
                    }            
                });
            }
        );
    });
</script>