<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<script>
    $(function () {
        var gantt_data = [];

        gantt_data = <?php echo isset($forecast['listjson']) ? $forecast['listjson'] : array(); ?>;
        gantt = $("#leads-switch_forecast_id").gantt({
            source: gantt_data,
            itemsPerPage: 12,
            months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            navigate: 'scroll',
            scale: "days",
            minScale: "days",
            maxScale: "months",
            waitText: 'Loading...',
            scrollToToday: false,
            onRender: function () {
                $('#gantt .leftPanel .name .fn-label:empty').parents('.name').css('background', 'initial');
            },
            onItemClick: function (data) {
                init_task_modal(data.id);
            },
            
        });
        

       
    });
</script>
<style>
    .fn-gantt .leftPanel .fn-label {
        color: #484A4D !important;
    }
    .fn-gantt-hint {
        color: #484A4D !important;
    }
    .gantt {
        margin: 0px auto !important;
        border: 0px solid #ddd !important;
    }
</style>