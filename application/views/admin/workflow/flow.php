<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $approvalLevel =0; ?>
<?php init_head(); ?>
<style>
    div.lineHorizontal {
        border-left: 3px dashed gray;
        height: 50px;
        left: 13%;
        margin-left:13%;
    }

    div.openFlowsWrapper {
        margin-left: 8%;
        width: 10%;
        text-align: center;
    }

    div.openFlowsWrapper .btn {
        background-color: white;
    }

    .flow {
        margin-bottom: 0px;
    }

    .addflow {
        cursor: pointer;
        transition: transform .2s;
    }

    .addflow:hover .panel-body {
        border-color: black;
    }

    .flow .badge-success{
        background-color: #84c529;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo admin_url('workflow') ?>">Workflow</a></li>
                    <li class="breadcrumb-item active"><?php echo $workflow['name'] ?></li>
                </ol>
                </nav>

                <div class="panel_s flow">
                    <div class="panel-body">
                        <p class="text-muted"><?php echo _l('action') ?></p>
                        <h4 class="no-margin"><?php echo $workflow['name'] ?></h4>
                        <p><?php echo $workflow['description'] ?></p>
                    </div>
                </div>
                <div class="lineHorizontal"></div>
                <?php foreach ($workflow['flows'] as $flow) : ?>
                    <?php if (isset($workflow['services'][$flow->service])) : $service = $workflow['services'][$flow->service]; ?>
                        <div class="panel_s flow">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-3" style="font-size: 70px;text-align: center;">
                                        <?php echo $workflow['services'][$flow->service]['icon']; ?>
                                    </div>
                                    <div class="col-xs-9">
                                        <div style="display: flex;justify-content: space-between;">
                                            <p class="text-muted"><?php echo _l($service['type']) ?></p>
                                            <div>
                                                <div class="onoffswitch">
                                                    <input type="checkbox" data-switch-url="<?= admin_url('workflow/updateFlowStatus') ?>" name="onoffswitch" class="onoffswitch-checkbox" id="<?= $flow->id ?>" data-id="<?= $flow->id ?>" <?= ($flow->inactive == 0) ? "checked" : "" ?>>
                                                    <label class="onoffswitch-label" for="<?= $flow->id ?>"></label>
                                                </div>
                                            </div>
                                        </div>

                                        <h4><?php echo $service['name'];?>
                                        <?php if($workflow['services'][$flow->service]['medium']=='approval'): $approvalLevel++; ?>
                                        <span class="badge badge-success"><?php echo $approvalLevel ?></span>
                                        <?php endif; ?>
                                        </h4>
                                        <p class="no-margin"><?php echo $service['description'] ?></p>
                                        <a href="<?php echo admin_url('workflow/configure/' . $flow->id) ?>" class="btn btn-primary" style="float: right;">Configure</a>
                                        <a href="#" data-flow-id="<?php echo $flow->id ?>" class="btn btn-danger delete-flow" style="float: right;margin-right:10px">Delete</a>
                                        <?php $childer_before_br =true; ?>
                                        <?php foreach($this->workflow_model->getflows($workflow['action'],$flow->id) as $childflow): ?>
                                            <?php if($childer_before_br): ?>
                                            <br><br><br><br>
                                            <?php $childer_before_br=false; endif; ?>
                                            <?php if (isset($workflow['services'][$childflow->service])) : $service = $workflow['services'][$childflow->service]; ?>
                                                <div class="panel_s flow">
                                                    <div class="panel-body">
                                                        <div class="row">
                                                            <div class="col-xs-3" style="font-size: 70px;text-align: center;">
                                                                <?php echo $workflow['services'][$childflow->service]['icon']; ?>
                                                            </div>
                                                            <div class="col-xs-9">
                                                                <div style="display: flex;justify-content: space-between;">
                                                                    <p class="text-muted"><?php echo $service['type'] ?></p>
                                                                    <div>
                                                                        <div class="onoffswitch">
                                                                            <input type="checkbox" data-switch-url="<?= admin_url('workflow/updateFlowStatus') ?>" name="onoffswitch" class="onoffswitch-checkbox" id="<?= $childflow->id ?>" data-id="<?= $childflow->id ?>" <?= ($childflow->inactive == 0) ? "checked" : "" ?>>
                                                                            <label class="onoffswitch-label" for="<?= $childflow->id ?>"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <h4><?php echo $service['name'];?>
                                                                <?php if($workflow['services'][$childflow->service]['medium']=='approval'): $approvalLevel++; ?>
                                                                <span class="badge badge-success"><?php echo $approvalLevel ?></span>
                                                                <?php endif; ?>
                                                                </h4>
                                                                <p class="no-margin"><?php echo $service['description'] ?></p>
                                                                <a href="<?php echo admin_url('workflow/configure/' . $childflow->id) ?>" class="btn btn-primary" style="float: right;">Configure</a>
                                                                <a href="#" data-flow-id="<?php echo $childflow->id ?>" class="btn btn-danger delete-flow" style="float: right;margin-right:10px">Delete</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="lineHorizontal"></div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <?php if($workflow['services'][$flow->service]['medium']=='approval'):?>
                                        <div class="openFlowsWrapper">
                                            <button class="btn btn-dark openFlows" data-parent-flow ="<?php echo $flow->id ?>"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lineHorizontal"></div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <div class="openFlowsWrapper">
                    <button class="btn btn-dark openFlows" data-parent-flow ="0"><i class="fa fa-plus" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="flowsModal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <?php foreach ($workflow['services'] as $service_name => $service) : ?>
                        <div class="col-md-3">
                            <div class="panel_s addflow no-margin" data-flow-name="<?php echo $service_name ?>">
                                <div class="panel-body" style="padding: 10px;">
                                    <div class="" style="font-size: 50px;text-align: center;">
                                        <?php echo $service['icon']; ?>
                                    </div>
                                    <div class="">
                                        <p class="text-muted text-small"><?php echo $service['name'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    var currentParentFlowId =0;
    function addService(service) {
        var action = '<?php echo $workflow['action'] ?>';
        $.ajax({
            url: '<?= admin_url('workflow/addFlow') ?>',
            type: "post",
            data: {
                'action': action,
                'service': service,
                'parent_id':currentParentFlowId,
            },
            dataType: "json",
            success: function(response) {
                if (response.success == true) {
                    alert_float('success', response.msg);
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert_float('warning', response.msg);
                }
            },
        })
    }
    $('.openFlows').click(function() {
        currentParentFlowId =$(this).attr('data-parent-flow');
        $('#flowsModal').modal('show');
    });
    $('.addflow').click(function() {
        var service =$(this).attr('data-flow-name');
        addService(service);
    });
    $('.delete-flow').click(function(e){
        e.preventDefault();
        if(confirm("Do you want to delete this?")){
            var flowid =$(this).attr('data-flow-id');
            var url ='<?php echo admin_url('workflow/deleteflow/') ?>'+flowid;
            $.ajax({
                url: url,
                type: "post",
                dataType: "json",
                success: function(response) {
                    if (response.success == true) {
                        alert_float('success', response.msg);
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    }
                },
            });
        }
        
    });
</script>
</body>

</html>