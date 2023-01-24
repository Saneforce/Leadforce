<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">

                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#connectFormModal" style="float:right; margin-bottom:15px;">
                            + Connect Form
                        </button>

                        <div>
                            <table class="table dt-table scroll-responsive">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('form_name'); ?></th>
                                        <th>Configure Setup</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($configs) : foreach ($configs as $key => $config) : ?>
                                            <tr>
                                                <td data-order="<?php echo $config->form_name; ?>"><?php echo $config->form_name; ?></td>
                                                <td><a data-toggle="modal" data-target="#configModal" href="<?php echo admin_url('plugin/acres99/config/' . $config->id) ?>" class="">View</a> | <a class="text-danger" href="<?php echo admin_url('plugin/acres99/deleteconfig/' . $config->id) ?>" class="">Delete</a></td>
                                            </tr>
                                    <?php endforeach;
                                    endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="configModal" style="display: none; z-index: 1050;">
    <div class="modal-dialog">
        <div class="modal-header">
            <span class="title">Configuration Setup</span>
            <button type="button" class="close" data-dismiss="modal">×</button>
        </div>
        <div class="modal-content">
            <div class="modal-body" id="configModalContent">
            </div>
        </div>
    </div>
</div>

<div class="modal" id="connectFormModal" style="display: none; z-index: 1050;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo admin_url('plugin/acres99') ?>" method="POST">
                <div class="modal-header">
                    <span class="title">Connect Form</span>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body" id="connectFormModalContent">
                    <div class="form-group">
                        <?php
                        $forms = array();
                        if ($web_forms) {
                            echo '<label for="form" class="control-label">Select Form</label>';
                            echo render_select('web_form', $web_forms, array('id', 'name'), '');
                        } else {
                            echo '<div class="text-center"><p class="text-muted">There is no forms created</p><a class="btn btn-info" href="' . admin_url('leads/form') . '"> <i class="fa fa-plus" aria-hidden="true"></i> Add Web Forms</a></div>';
                        }
                        ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info pull-right">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php init_tail(); ?>
</body>

</html>