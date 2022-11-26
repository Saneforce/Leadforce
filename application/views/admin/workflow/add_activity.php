<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
$task_types =$this->tasktype_model->getTasktypes();
$priorities =get_tasks_priorities();
?>
<br>
<?php echo form_open(admin_url('workflow/saveconfig/'), array('id' => 'AddActivityConfig')); ?>
<div class="form-group">
    <label for="type" class="control-label">Activity Type</label>
    <select name="type" id="type" class="form-control selectpicker" required>
        <option name="">Nothing Selected</option>
        <?php if($task_types): ?>
        <?php foreach($task_types as $key => $type): ?>
            <option value="<?php echo $type['id'] ?>" <?php echo $key==0?'selected':''?>><?php echo $type['name'] ?></option>
        <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>
<div class="form-group">
    <label for="subject" class="control-label">Activity Subject</label>
    <input type="text" id="subject" name="subject" class="form-control" required>
</div>
<div class="form-group">
    <label for="description" class="control-label">Activity Description</label>
    <textarea id="description" name="description" class="form-control" rows="8"></textarea>
</div>
<div class="form-group">
    <label for="priority" class="control-label">Activity Priority</label>
    <select name="priority" id="priority" class="form-control selectpicker" required>
        <option name="">Nothing Selected</option>
        <?php if($priorities): ?>
        <?php foreach($priorities as $key => $priority): ?>
            <option value="<?php echo $priority['id'] ?>" <?php echo $key==0?'selected':''?>><?php echo $priority['name'] ?></option>
        <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>

<div class="form-group">
    <label for="startdate" class="control-label">Activity Startdate</label>
    <select name="startdate" id="startdate" class="form-control selectpicker" required>
        <option name="">Nothing Selected</option>
        <option value="+0 days" selected>Today</option>
        <option value="+1 days">Tomorrow</option>
        <option value="+2 days" >Day after Tomorrow</option>
        <option value="next sunday" >Next Sunday</option>
        <option value="next monday" >Next Monday</option>
        <option value="next tuesday" >Next Tuesday</option>
        <option value="next wednesday" >Next Wednesday</option>
        <option value="next thursday" >Next Thursday</option>
        <option value="next friday" >Next Friday</option>
        <option value="next saturday" >Next Saturday</option>
        <option value="+1 week" >Next Week</option>
        <option value="+1 month" >Next Month</option>
        <option value="+1 year" >Next year</option>
    </select>
</div>
<br>
<button type="submit" class="btn btn-primary" id="saveAddActivityConfig">Save Configuration</button>
<?php echo form_close(); ?>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {

        appValidateForm(
            $('#AddActivityConfig'),
            {},
            function(form) {
                var sendto = $('#AddActivityConfig #sendto').val();
                var template = $('#AddActivityConfig #template').val();
                var variables = [];
                var variablesfield = $('#AddActivityConfig .variablesfield');
                $.each(variablesfield, function() {
                    variables.push($(this).val());
                });
                $('#AddActivityConfig .sms_form_errors').remove();
                $.ajax({
                    url: admin_url+'workflow/saveconfig/'+$('.tree .block.selected').attr('data-id'),
                    type: "post",
                    data: $(form).serialize(),
                    dataType: "json",
                    success: function(response) {
                        if (response.success == true) {
                            var title ='';
                            var description ='Setup Configured';
                            workflowl.updateBlockContent($('.tree .block.selected').attr('data-id'),title,description);
                            alert_float('success', 'Setup saved successfully.');
                        } else {
                            alert_float('warning', response.msg);
                            $.each(response.errors, function(k, v) {
                                $('#AddActivityConfig [name="' + k + '"]').parent().append(`<p class="text-danger sms_form_errors">` + v + `</p>`)
                            });
                        }
                    },
                });
            }
        );
    });
</script>