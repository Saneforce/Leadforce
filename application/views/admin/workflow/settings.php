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
                                    <div class="col-md-3" style="font-size: 50px;text-align: center;">
                                    <?php echo $workflow['icon'] ?>
                                    </div>
                                    <div class="col-md-9">
                                        <h4><?php echo $workflow['name'] ?> </h4>
                                        <p><?php echo $workflow['description'] ?></p>
                                    </div>
                                    
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