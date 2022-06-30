<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
var salesChart;
var groupsChart;
var paymentMethodsChart;
var customersTable;
var report_from = $('input[name="report-from"]');
var report_to = $('input[name="report-to"]');
var type_reports = $('input[name="type-reports"]');
var activities_based_by = $('select[name="activities-based-by"]');

var activities_added_user_reports = $('#activities-added-user-reports');
var activities_added_type_reports = $('#activities-added-type-reports');
var activities_completed_user_reports = $('#activities-completed-user-reports');
var activities_completed_type_reports = $('#activities-completed-type-reports');

var date_range = $('#date-range');
var report_from_choose = $('#report-time');
var fnServerParams = {
    "report_months": '[name="months-report"]',
    "pipeline_id": '[name="pipeline_id"]',
    "teamleader": '[name="teamleader"]',
	"type_reports": '[name="type-reports"]',
    "activities_based_by": '[name="activities-based-by"]',
    "report_from": '[name="report-from"]',
    "report_to": '[name="report-to"]',
    "report_currency": '[name="currency"]',
}
$(function() {
    $('select[name="teamleader"],select[name="pipeline_id"]').on('change', function() {
        gen_reports();
    });
    
    $('select[name="activities-based-by"]').on('change', function() {
		hide_all_this();
		show_all_this(type_reports.val())
		gen_reports();
    });

    report_from.on('change', function() {
        var val = $(this).val();
        var report_to_val = report_to.val();
        if (val != '') {
            report_to.attr('disabled', false);
            if (report_to_val != '') {
                gen_reports();
            }
        } else {
            report_to.attr('disabled', true);
        }
    });

    report_to.on('change', function() {
        var val = $(this).val();
        if (val != '') {
            gen_reports();
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
    });

});

function init_ac_details(e, id, by,all,countis,type_reports) {
    var dwdr = $('#dropdownacd_' + id+'_' + by+'_' + all+'_' + type_reports);
	var offset = e.pageX;
	$('.yes_select a').css('text-decoration', '');
	$('.yes_select').removeClass('yes_select').css('height', '');;
    if (dwdr.hasClass('hide')) {
		$('.divdwdr').addClass('hide');
        if (countis > 25) {
            countis = 25;
        }
        var parentheight = 250 + (countis * 33);
		offset = offset - 120;
        dwdr.removeClass('hide').css('height', parentheight).css('margin-left', '-'+offset+'px');
        dwdr.parent().css('height', parentheight + 60).addClass('yes_select');
		$('.yes_select a').css('text-decoration', 'underline');
    } else {
        dwdr.addClass('hide').css('height', '');
        dwdr.parent().css('height', '');
    }
    initDataTable('.ac_details_reports_' + id+'_' + by+'_' + all+'_' + type_reports, admin_url + 'reports/activities_detail_report/' + id+'/' + by+'/' + all, false,false, fnServerParams, [0, 'desc']);
}


function hide_all_this() {
	activities_added_user_reports.addClass('hide');
	activities_added_type_reports.addClass('hide');
    activities_completed_user_reports.addClass('hide');
	activities_completed_type_reports.addClass('hide');
}

function show_all_this(type) {
	var based_by = activities_based_by.val();
	if (type == 'activities-added') {
		if(based_by == 'name'){
			activities_added_user_reports.removeClass('hide');
		}else{
			activities_added_type_reports.removeClass('hide');
		}
    }
    if (type == 'activities-completed') {
		if(based_by == 'name'){
			activities_completed_user_reports.removeClass('hide');
		}else{
			activities_completed_type_reports.removeClass('hide');
		}
    }
}
function init_report(e, type) {
    var report_wrapper = $('#report');

    if (report_wrapper.hasClass('hide')) {
        report_wrapper.removeClass('hide');
    }

    $('head title').html($(e).text());
	$('#heading_dynamic').html($(e).text());
    hide_all_this();

    report_from_choose.addClass('hide');

    $('select[name="months-report"]').selectpicker('val', 'this_month');
    // Clear custom date picker
    report_to.val('');
    report_from.val('');
    type_reports.val(type);
	
    $('#currency').removeClass('hide');

    if (type != 'total-income' && type != 'payment-modes') {
        report_from_choose.removeClass('hide');
    }

	show_all_this(type);
    gen_reports();
}




function activities_added_user_report() {
    if ($.fn.DataTable.isDataTable('.table-activities-added-user-report')) {
        $('.table-activities-added-user-report').DataTable().destroy();
    }
    initDataTable('.table-activities-added-user-report', admin_url + 'reports/activities_report', false, false, fnServerParams, [0,'desc']);
}


function activities_added_type_report() {
    if ($.fn.DataTable.isDataTable('.table-activities-added-type-report')) {
        $('.table-activities-added-type-report').DataTable().destroy();
    }
    initDataTable('.table-activities-added-type-report', admin_url + 'reports/activities_report', false, false, fnServerParams, [0,'desc']);
}



function activities_completed_user_report() {
    if ($.fn.DataTable.isDataTable('.table-activities-completed-user-report')) {
        $('.table-activities-completed-user-report').DataTable().destroy();
    }
    initDataTable('.table-activities-completed-user-report', admin_url + 'reports/activities_report',false, false,fnServerParams,[0,'desc']);
}

function activities_completed_type_report() {
    if ($.fn.DataTable.isDataTable('.table-activities-completed-type-report')) {
        $('.table-activities-completed-type-report').DataTable().destroy();
    }
    initDataTable('.table-activities-completed-type-report', admin_url + 'reports/activities_report',false, false,fnServerParams,[0,'desc']);
}



// Main generate report function
function gen_reports() {
    if (!activities_added_user_reports.hasClass('hide')) {
        activities_added_user_report();
    }
	if (!activities_added_type_reports.hasClass('hide')) {
        activities_added_type_report();
    }
	
    if (!activities_completed_user_reports.hasClass('hide')) {
        activities_completed_user_report();
    }
	
    if (!activities_completed_type_reports.hasClass('hide')) {
        activities_completed_type_report();
    }
}
</script>