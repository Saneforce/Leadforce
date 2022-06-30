<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
var salesChart;
var groupsChart;
var paymentMethodsChart;
var customersTable;
var report_from = $('input[name="report-from"]');
var report_to = $('input[name="report-to"]');

var deals_wons_reports = $('#deals-wons-reports');
var deals_loss_reports = $('#deals-loss-reports');
var deals_loss_reports_by_div = $('#deals-loss-by-div');
var deals_started_reports = $('#deals-started-reports');
var deals_started_status_reports = $('#deals-started-status-reports');
var deals_started_reports_by_div = $('#deals-started-by-div');

var date_range = $('#date-range');
var report_from_choose = $('#report-time');
var fnServerParams = {
    "report_months": '[name="months-report"]',
    "pipeline_id": '[name="pipeline_id"]',
    "teamleader": '[name="teamleader"]',
    "deals_loss_by": '[name="deals-loss-by"]',
	"deals_started_by": '[name="deals-started-by"]',
    "report_from": '[name="report-from"]',
    "report_to": '[name="report-to"]',
    "report_currency": '[name="currency"]',
}
$(function() {
    $('select[name="currency"],select[name="deals-started-by"],select[name="deals-loss-by"],select[name="teamleader"],select[name="pipeline_id"]').on('change', function() {
        gen_reports();
        if($('select[name="deals-started-by"]').val() == 'status'){
            deals_started_status_detail_report_fn($('select[name="deals-started-by"]').val());
        }
    });

    

    $('select[name="deals-loss-by"]').on('change', function() {
        var val = $(this).val();
        deals_loss_by_table_show_hide(val);
    });
    
    
	 $('select[name="deals-started-by"]').on('change', function() {
        var val = $(this).val();
        //byuser
        if(val == 'name'){
            deals_started_reports.removeClass('hide');
            deals_started_status_reports.addClass('hide');
            deals_started_by_table_show_hide(val);
        }else if(val == 'status'){
            deals_started_reports.addClass('hide');
            deals_started_status_reports.removeClass('hide');
        //    deals_started_status_detail_report_fn(val);
        }
        
    });

    report_from.on('change', function() {
        var val = $(this).val();
        var report_to_val = report_to.val();
        if (val != '') {
            report_to.attr('disabled', false);
            if (report_to_val != '') {
                gen_reports();
                if($('select[name="deals-started-by"]').val() == 'status'){
                    deals_started_status_detail_report_fn($('select[name="deals-started-by"]').val());
                }
            }
        } else {
            report_to.attr('disabled', true);
        }
    });

    report_to.on('change', function() {
        var val = $(this).val();
        if (val != '') {
            gen_reports();
            if($('select[name="deals-started-by"]').val() == 'status'){
                deals_started_status_detail_report_fn($('select[name="deals-started-by"]').val());
            }
        }
    });

    $('select[name="months-report"]').on('change', function() {
        var val = $(this).val();
        report_to.attr('disabled', true);
        report_to.val('');
        report_from.val('');
        if (val == 'custom') {
            date_range.addClass('fadeIn').removeClass('hide');
            return;
        } else {
            if (!date_range.hasClass('hide')) {
                date_range.removeClass('fadeIn').addClass('hide');
            }
        }
        gen_reports();
        if($('select[name="deals-started-by"]').val() == 'status'){
            deals_started_status_detail_report_fn($('select[name="deals-started-by"]').val());
        }
    });

    $('select[name="deals-started-by"]').on('change', function() {
        if ($.fn.DataTable.isDataTable('.table-deals-started-report')) {
            $('.table-deals-started-report').DataTable().destroy();
        }
        initDataTable('.table-deals-started-report', admin_url + 'reports/deals_started_report', false, false, fnServerParams, [0,
            'desc'
        ]);
        var val = $('select[name="deals-started-by"]').val();
        deals_started_by_table_show_hide(val);
    });

});

function deals_started_status_detail_report_fn(val) {
    
    var d ={};
    for (var key in fnServerParams) {
        d[key] = $(fnServerParams[key]).val();
    }
    $.ajax({url: admin_url + 'reports/deals_started_status_detail_report/',  type: "POST",
        data:d,success: function(result) { 
            deals_started_status_reports.html(result);
            
        }}); 
}

function deals_started_by_table_show_hide(val) {
    $('.table-deals-started-report').on('draw.dt', function() {
            var table_deals_started_report_tobj = $(this).DataTable();
            table_deals_started_report_tobj.column(0).visible(0);
            table_deals_started_report_tobj.column(1).visible(0);
            table_deals_started_report_tobj.column(2).visible(0);
            if(val == 'status'){
                table_deals_started_report_tobj.column(1).visible(1);
            }else if(val == 'loss_reason'){
                table_deals_started_report_tobj.column(2).visible(1);
            }else{
                table_deals_started_report_tobj.column(0).visible(1);
            }
    });
}

function deals_loss_by_table_show_hide(val) {
    $('.table-deals-loss-report').on('draw.dt', function() {
            var table_deals_loss_report_tobj = $(this).DataTable();
            table_deals_loss_report_tobj.column(0).visible(0);
            table_deals_loss_report_tobj.column(1).visible(0);
            table_deals_loss_report_tobj.column(2).visible(0);
            if(val == 'status'){
                table_deals_loss_report_tobj.column(1).visible(1);
            }else if(val == 'loss_reason'){
                table_deals_loss_report_tobj.column(2).visible(1);
            }else{
                table_deals_loss_report_tobj.column(0).visible(1);
            }
    });
}
function init_dealsw_details(e, id, countis) {
    var dwdr = $('#dropdowndeald_' + id);
    if (dwdr.hasClass('hide')) {
        if (countis > 25) {
            countis = 25;
        }
        var parentheight = 250 + (countis * 33);
        dwdr.removeClass('hide').css('height', parentheight);;
        dwdr.parent().css('height', parentheight + 60);
    } else {
        dwdr.addClass('hide').css('height', '');
        dwdr.parent().css('height', '');
    }
    initDataTable('.deals_wons_details_reports_' + id, admin_url + 'reports/deals_wons_detail_report/' + id, false,
        false, fnServerParams, [0, 'desc']);
}



function init_dealsl_details(e, id,status,loss_reason, countis) {
    var dldr = $('#dropdowndealdl_' + id+'_' + status+'_' + loss_reason);
    if (dldr.hasClass('hide')) {
        if (countis > 25) {
            countis = 25;
        }
        var parentheight = 250 + (countis * 33);
        dldr.removeClass('hide').css('height', parentheight);;
        dldr.parent().css('height', parentheight + 60);
    } else {
        dldr.addClass('hide').css('height', '');
        dldr.parent().css('height', '');
    }
    initDataTable('.deals_loss_details_reports_' +  id+'_' + status+'_' + loss_reason, admin_url + 'reports/deals_loss_detail_report/' +  id+'/' + status+'/' + loss_reason, false,
        false, fnServerParams, [0, 'desc']);
}


function init_dealss_details(e, id,status,started_reason, countis) {
    $('.nullh').css('height', '').removeClass('yes_select');
    $('.nullh a').css('text-decoration', '');
    var offset = e.pageX;
    var dldr = $('#dropdowndealdl_' + id+'_' + status+'_' + started_reason);
    
    
    
    if (dldr.hasClass('hide')) {
        $('.divdsdr').addClass('hide');
        if (countis > 25) {
            countis = 25;
        }
        var parentheight = 250 + (countis * 33);
        offset = offset - 120;
        dldr.removeClass('hide').css('height', parentheight).css('margin-left', '-'+offset+'px');
        dldr.parent().css('height', parentheight + 60).addClass('nullh').addClass('yes_select');
        $('.yes_select a').css('text-decoration', 'underline');
    } else {
        dldr.addClass('hide').css('height', '');
        dldr.parent().css('height', '');
    }
    initDataTable('.deals_started_details_reports_' +  id+'_' + status+'_' + started_reason, admin_url + 'reports/deals_started_detail_report/' +  id+'/' + status+'/' + started_reason, false,
        false, fnServerParams, [0, 'desc']);
}

function init_report(e, type) {
    var report_wrapper = $('#report');

    if (report_wrapper.hasClass('hide')) {
        report_wrapper.removeClass('hide');
    }

    $('head title').html($(e).text());
    $('#heading_dynamic').html($(e).text());
    
    deals_wons_reports.addClass('hide');
    deals_loss_reports.addClass('hide');
    deals_started_reports.addClass('hide');
    deals_loss_reports_by_div.addClass('hide');

    report_from_choose.addClass('hide');

    $('select[name="months-report"]').selectpicker('val', 'this_month');
    // Clear custom date picker
    report_to.val('');
    report_from.val('');
    
    deals_loss_reports_by_div.addClass('hide');
    deals_started_reports_by_div.addClass('hide');

    $('#currency').removeClass('hide');

    if (type != 'total-income' && type != 'payment-modes') {
        report_from_choose.removeClass('hide');
    }

    if (type == 'deals-won-report') {
        deals_wons_reports.removeClass('hide');
    }
    if (type == 'deals-Lost-report') {
        deals_loss_reports.removeClass('hide');
        deals_loss_reports_by_div.removeClass('hide');
    }
	if (type == 'deals-started-report') {
        deals_started_reports.removeClass('hide');
        deals_started_reports_by_div.removeClass('hide');
    }
    gen_reports();
}




function deals_wons_report() {
    if ($.fn.DataTable.isDataTable('.table-deals-wons-report')) {
        $('.table-deals-wons-report').DataTable().destroy();
    }
    initDataTable('.table-deals-wons-report', admin_url + 'reports/deals_wons_report', false, false, fnServerParams, [0,
        'desc'
    ]);
}



function deals_loss_report() {
    if ($.fn.DataTable.isDataTable('.table-deals-loss-report')) {
        $('.table-deals-loss-report').DataTable().destroy();
    }
    initDataTable('.table-deals-loss-report', admin_url + 'reports/deals_loss_report', false, false, fnServerParams, [0,
        'desc'
    ]);
    var val = $('select[name="deals-loss-by"]').val();
    deals_loss_by_table_show_hide(val);
}


function deals_started_report() {
    if ($.fn.DataTable.isDataTable('.table-deals-started-report')) {
        $('.table-deals-started-report').DataTable().destroy();
    }
    initDataTable('.table-deals-started-report', admin_url + 'reports/deals_started_report', false, false, fnServerParams, [0,
        'desc'
    ]);
    var val = $('select[name="deals-started-by"]').val();
    deals_started_by_table_show_hide(val);
}

// Main generate report function
function gen_reports() {
    if (!deals_wons_reports.hasClass('hide')) {
        deals_wons_report();
    }
    if (!deals_loss_reports.hasClass('hide')) {
        deals_loss_report();
    }
	if (!deals_started_reports.hasClass('hide')) {
        deals_started_report();
    }
}
</script>