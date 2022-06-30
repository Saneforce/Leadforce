<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo basename(__FILE__, ".php"); ?>" data-name="<?php echo _l('calendar'); ?>">
    <div class="clearfix"></div>
    <div class="panel_s">
        <div class="panel-body">
            <!--<div class="widget-dragger"></div>-->
            <div class="dt-loader hide"></div>
            <?php $this->load->view('admin/utilities/calendar_filters'); ?>
            <div id="calendar"></div>
        </div>
    </div>
    <div class="clearfix"></div>
</div>

<style>
/*    .panel_s {
        margin-bottom: 6px;
    }
    .content {
        padding: 10px 25px 0px 25px;
    }*/
.fc-view {
    overflow-y: auto;
}
.fc-row.panel-default{
    border-right-width: 0px !important;
    margin-right: 0px !important;
}
.fc-scroller.fc-day-grid-container{
    overflow: unset !important;
    height: auto !important;
}
</style>