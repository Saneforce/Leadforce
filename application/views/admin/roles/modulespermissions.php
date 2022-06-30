<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open($this->uri->uri_string()); ?>
                        <div class="row">
                            <?php foreach ($modules_permissions as $key => $value) { ?>
                                <div class="col-md-3">
                                    <div class="checkbox">
                                        <input type="checkbox"
                                        <?php echo ($value['active'] == 1) ? 'checked' : ''; ?>
                                               class="capability"
                                               id="<?php echo 'mp_' . $value['id']; ?>"
                                               name="mp[<?php echo $value['id']; ?>]"
                                               value="<?php echo $value['id']; ?>">
                                        <label for="<?php echo 'mp_' . $value['id']; ?>">
                                            <?php echo $value['features_label']; ?>
                                        </label>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <hr />
                        <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail(); ?>
    <script>
        $(function () {
            appValidateForm($('form'), {name: 'required'});
        });
    </script>
</body>
</html>
