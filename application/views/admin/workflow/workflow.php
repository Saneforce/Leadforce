<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body" id="workflowwrapper" style="overflow-x: auto; overflow-y:auto; height:90vh;">
                        <ul class="tree">
                            
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body" style="overflow-y:auto; height:90vh;">
                        <div role="toolbar" aria-label="Node tools" aria-hidden="true" class="toolbar show">
                            <span id="selectedBlockTitle" class="h5"><?php echo $moduleDetails['title'] ?></span>
                            <div class="pull-right">
                                <button type="button" class="btn btn-default" data-js="deleteNode" data-toggle="tooltip" data-title="Delete Block" id="deleteNode">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <hr class="hr-panel-heading">

                        <div class="accordion" id="accordionsidebar">
                            <div>
                                <div class="collapsible collapsed" data-toggle="collapse" data-target="#sidebarSettings" aria-expanded="false" aria-controls="sidebarSettings">
                                    <h5>Settings</h5>
                                    <p class="text-muted" id="sidebarSettingsTitle"></p>
                                </div>
                                <div class="collapse" id="sidebarSettings" data-parent="#accordionsidebar">
                                    <div class="sidebar-setup" id="sidebarsetupemail">
                                        <?php $this->load->view('admin/workflow/email'); ?>
                                    </div>

                                    <div class="sidebar-setup" id="sidebarsetupapproval">
                                        <?php $this->load->view('admin/workflow/approval'); ?>
                                    </div>

                                    <div class="sidebar-setup" id="sidebarsetupcondition">
                                        <?php $this->load->view('admin/workflow/condition'); ?>
                                    </div>
                                    <?php if($moduleDetails['name'] =='lead'): ?>
                                        <div class="sidebar-setup" id="sidebarsetupleadstaffassign">
                                            <?php $this->load->view('admin/workflow/lead/assign_staff'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="sidebar-setup show" id="sidabarnosetup">
                                        <p>No settings available.</p>
                                    </div>
                                </div>
                            </div>


                            <hr class="hr-panel-heading">
                            <div>
                                <div class="collapsible" data-toggle="collapse" data-target="#sidebarTriggers" aria-expanded="true" aria-controls="sidebarTriggers">
                                    <h5>Trigger</h5>
                                    <p class="text-muted">Click trigger to add to workflow.</p>
                                </div>
                                
                                <ul class="triggers collapse" id="sidebarTriggers" data-parent="#accordionsidebar"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>


<style>
    :root {
        --line-color: #c5ccd0;
        --line-width: .1em;
        --gutter: 2.5em;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    }
    #workflowwrapper {
        cursor: grab;
        background-image: url('<?php echo base_url(); ?>assets/images/tile.png');
        background-repeat: repeat;
        background-size: 30px 30px;
        background-color: #FBFBFB;
    }

    #workflowwrapper:active {
        cursor: grabbing;
    }

    *,
    *:before,
    *:after {
        box-sizing: border-box;
    }

    .tree {
        margin: 0 0 calc(var(--gutter) * 2);
        text-align: center;
        /* _________ */
        /* | */
        /* The root node doesn't connect upwards */
    }

    .tree,
    .tree ul,
    .tree li {
        list-style: none;
        margin: 0;
        padding: 0;
        position: relative;
    }

    .tree,
    .tree ul {
        display: table;
        width: 100%;
    }

    .tree ul {
        width: 100%;
    }

    .tree li {
        display: table-cell;
        padding: var(--gutter) 0;
        vertical-align: top;
    }

    .tree li:before {
        content: "";
        left: 0;
        outline: solid calc(var(--line-width) /2) var(--line-color);
        position: absolute;
        right: 0;
        top: 0;
    }

    .tree li:first-child:before {
        left: 50%;
    }

    .tree li:last-child:before {
        right: 50%;
    }

    .tree .block {
        border-radius: 0.2em;
        margin: 0 calc(var(--gutter) / 2) var(--gutter);
        min-height: 2.1em;
        position: relative;
        z-index: 1;
        display: inline-block;
        padding: 10px 15px;
        text-align: left;
        border-radius: 5px;
        background: #fff;
        cursor: pointer;
        font-size: 1em;
        line-height: 1.2em;
        padding: 0.4em 1em;
        position: relative;
        width: 250px;
        box-shadow: 0px 4px 30px rgba(22, 33, 74, 0.05);
        border: 1px solid #c5ccd0;
    }

    .tree [contenteditable] {
        cursor: text;
    }

    .tree .selected, .tree .block:hover, .triggers .trigger:hover {
        color: #415164;
        background-color: #f1f5f7;
        border: 1px solid #000;
        box-shadow: 0px 4px 30px rgba(22, 33, 74, 0.08);
        cursor: pointer;
    }

    .tree ul:before,
    .tree .block:before {
        outline: solid calc(var(--line-width) / 2) var(--line-color);
        content: "";
        height: var(--gutter);
        left: 50%;
        position: absolute;
        top: calc(calc(-1 * var(--gutter)) - calc(var(--line-width) / 2));
    }
    .tree ul.current-flow:before,
    .tree .block.current-flow:before {
        outline: solid calc(var(--line-width) / 2) red;
    }

    .tree>li {
        margin-top: 0;
    }

    .tree>li:before,
    .tree>li:after,
    .tree>li>.block:before {
        outline: none;
    }

    input[type=range] {
        display: block;
        width: 100%;
    }

    input[type=color] {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        border: none;
        cursor: pointer;
        display: block;
        height: 2em;
        padding: 0;
        vertical-align: middle;
        width: 100%;
    }

    .toolbar {
        display: none;
    }

    .toolbar.show {
        display: block;
    }

    ins {
        background: #fff;
        border: solid calc(var(--line-width) /2) var(--line-color);
        display: inline-block;
        font-size: 0.8em;
        left: -1em;
        margin: 1em 0 0;
        padding: 0.2em 0.5em;
        position: absolute;
        right: -1em;
        text-decoration: none;
        top: 100%;
    }

    ins:before,
    ins:after {
        border: solid 1em transparent;
        border-top: none;
        content: "";
        left: 50%;
        position: absolute;
        transform: translate(-50%, 0);
    }

    ins:before {
        border-bottom-color: var(--line-color);
        bottom: 100%;
    }

    ins:after {
        bottom: calc(100% - var(--line-width));
        border-bottom-color: #fff;
    }

    ins {
        opacity: 0;
        transition: all 0.2s ease;
        transform: scale(0, 0);
    }

    .js-confirm .confirm,
    .js-root .root {
        opacity: 1;
        transform: scale(1, 1);
    }

    .grid {
        display: flex;
        width: 100%;
    }

    .grid>* {
        flex: 1;
        margin-left: 0.5em;
        margin-right: 0.5em;
    }

    .trigger {
        display: flex;
        width: 100%;
        border: 1px solid #dee2e6;
        margin-top: 15px;
        border-radius: 0.2em;
    }
    .trigger-icon{
        font-size: 25px;
        padding: 10px 20px;
        display: flex;
        align-items: center;
    }

    .block-content {
        display: flex;
    }
    .block-content .block-icon{
        font-size: 25px;
        padding: 10px 20px;
        display: flex;
        align-items: center;
    }

    .collapsible:before {
        font-family: 'Glyphicons Halflings';
        content: "\e113";
        float: right;
        transition: all 0.5s;
    }
    .collapsible.collapsed:before {
        -webkit-transform: rotate(180deg);
        -moz-transform: rotate(180deg);
        transform: rotate(180deg);
    }

    .sidebar-setup{
        display: none;
    }

    .sidebar-setup.show{
        display: block;
    }

    .collapsible {
        cursor: pointer;
    }

    
    
</style>
<script src="<?php echo base_url('assets/js/workflow.js') ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-extendext/1.0.0/jquery-extendext.min.js" integrity="sha512-pAU2x/rE9QHeYHtKS3RJccBEx8v8Lyyo4kVsxg+K3N+w/kbwrj2C9mp02XGQA+cOwlF1FdbEzTxnKg3DrQgWuA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dot/1.1.3/doT.min.js" integrity="sha512-mv9iHAP8cyGYB1TX54qMIFYFbHpFoqo1StdcuIUoAxTXIiFfOu22TjJGrFMpY+iR4QmGkElLlHBVx5C+PiIdvg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/jQuery-QueryBuilder/dist/js/query-builder.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        // initiating workflow
        var module = <?= json_encode($this->workflow_app->getModuleDetails($moduleDetails['name']),JSON_PRETTY_PRINT); ?>;
        workflowl(module);

        // set module triggers
        var triggers = <?= json_encode($this->workflow_app->getTriggers($moduleDetails['name']),JSON_PRETTY_PRINT); ?>;

        workflowl.setTriggers(triggers);

        // make flow tree
        var flows = <?php echo json_encode($flows,JSON_PRETTY_PRINT); ?>;
        workflowl.Init(flows);
    });
</script>
</body>

</html>