<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">99Acres
                            <?php if($configured_web_forms): ?>
                            <a data-toggle="modal" data-target="#configModal" href="<?php echo admin_url('plugin/acres99/config') ?>" class="btn btn-info pull-right">View Configure Setup</a>
                            <?php endif; ?>
                        </h4>
                        <hr class="hr-panel_heading">
                        
                        <form action="<?php echo admin_url('plugin/acres99') ?>" method="POST">
                            <div class="form-group">
                                <?php
                                    $forms =array();
                                    if($web_forms){
                                        echo '<label for="form" class="control-label">Select Form</label>';
                                        echo render_select('web_form',$web_forms,array('id','name'),'',$configured_web_forms);
                                    }else{
                                        echo '<div class="text-center"><p class="text-muted">There is no forms created</p><a class="btn btn-info" href="'.admin_url('leads/form').'"> <i class="fa fa-plus" aria-hidden="true"></i> Add Web Forms</a></div>';
                                    }
                                ?>

                                <div>
                                    <button type="submit" class="btn btn-info pull-right">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="configModal" style="display: none; z-index: 1050;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" id="configModalContent">
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>
</html>