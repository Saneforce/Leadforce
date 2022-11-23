<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>Daffytel Configuration</h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open(admin_url('sms/daffytel'),array('id'=>'daffytelForm'));?>
                        <div class="form-group" >
                            <label for="access_token" class="control-label ">Access Token</label>
                            <input type="text" id="access_token" name="access_token" class="form-control" required value="<?php echo set_value('access_token') ?>" >
                        </div>
                        <button type="submit" class="btn btn-primary" id="save_template">Save</button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    document.addEventListener("DOMContentLoaded", function(event) { 
        appValidateForm(
            '#daffytelForm',
            {}
        );
    });
</script>
</body>
</html>