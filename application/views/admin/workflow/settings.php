<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $workflows = $this->app_workflow->getWorkflows(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-xs-12">
            <h3>Workflow Automation</h3>
            </div>
            <?php foreach ($workflows as $action => $workflow) : ?>
                <div class="col-md-4">
                    <div class="panel_s">
                        <div class="panel-body">
                            <a href="<?php echo admin_url('workflow/flow/' . $action) ?>">
                                <div class="card">
                                    <h4><?php echo $workflow['name'] ?></h4>
                                    <p><?php echo $workflow['description'] ?></p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>
</html>