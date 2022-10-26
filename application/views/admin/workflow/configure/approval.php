<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?php echo form_open(admin_url('workflow/saveapprovalconfig/'.$flowdetails->id), array('id' => 'ApprovalConfig')); ?>
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                
                <div class="panel_s">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?php echo admin_url('workflow') ?>">Workflow</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo admin_url('workflow/flow/'.$workflow['action']) ?>"><?php echo $workflow['name'] ?></a></li>
                                <li class="breadcrumb-item active" aria-current="page">Approval Level <?php echo $approvalLevel; ?> for deal</li>
                            </ol>
                        </nav>
                        <div class="form-group">
                            <label for="approver" class="control-label">Assing approver</label>
                            <select name="approver" id="approver" class="form-control selectpicker" data-live-search="true" required>
                                <option value="">Select approver</option>
                                <option value="REPORTING_LEVEL" <?php echo (isset($configure['approver']) &&  $configure['approver']=='REPORTING_LEVEL')?"selected":'' ?>>Reporting Level <?php echo $approvalLevel; ?></option>
                                <?php foreach($staffs as $staff):  ?>
                                <option value="<?php echo $staff->staffid ?>" <?php echo (isset($configure['approver']) &&  $configure['approver']==$staff->staffid)?"selected":'' ?>><?php echo $staff->firstname ?> <?php echo $staff->lastname ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <br>
                        <button type="button" class="btn btn-primary" id="saveApprovalConfig">Save Configuration</button>
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>

<script>
    $('#saveApprovalConfig').click(function() {
        var approver =$('#approver').val();
        $('.approval_form_errors').remove();
        $.ajax({
            url: $('#ApprovalConfig').attr('action'),
            type: "post",
            data: {approver:approver} ,
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
                        $('[name="'+k+'"]').parent().append(`<p class="text-danger approval_form_errors">`+v+`</p>`)
                    });
                }
            },
        });
    });
</script>
</body>

</html>