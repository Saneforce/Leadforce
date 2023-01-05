<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">

				<div class="">
					<div class="">
						
						<div class="clearfix"></div>
						<style type="text/css">
						
						.wrapperstylechange {
    display: inline-block;
    left: 17%;
    width: 84%;
}
#setup-menu-wrapper, #wrapper {
	min-height:767px !important;
}
#setup-menu-wrapper {
	top:83px;
}
.content {
    padding: 10px 25px 0px 25px;
}
.content{
	max-width:none !important;
}
table.body {
    background: none !important;
}
/* EMAIL */
.email {
    padding: 20px 10px 15px 10px;
	font-size: 1em;
}

.email .btn.search {
	font-size: 0.9em;
}

.email h2 {
	margin-top: 0;
	padding-bottom: 8px;
}

.email .nav.nav-pills > li > a {
	border-top: 3px solid transparent;
}

.email .nav.nav-pills > li > a > .fa {
	margin-right: 5px;
}

.email .nav.nav-pills > li.active > a,
.email .nav.nav-pills > li.active > a:hover {
	background-color: #f6f6f6;
	border-top-color: #3c8dbc;
}

.email .nav.nav-pills > li.active > a {
	font-weight: 600;
}

.email .nav.nav-pills > li > a:hover {
	background-color: #f6f6f6;
}

.email .nav.nav-pills.nav-stacked > li > a {
	color: var(--theme-default-text-color);
	border-top: 0;
	border-left: 3px solid transparent;
	border-radius: 0px;
	font-size: 13px;
}

.email .nav.nav-pills.nav-stacked > li.active > a,
.email .nav.nav-pills.nav-stacked > li.active > a:hover {
	background-color: #f6f6f6;
	border-left-color: var(--theme-primary-light);
	color: #444;
}

.email .nav.nav-pills.nav-stacked > li.header {
	color: #777;
	position: relative;
	padding: 0px 0 10px 0;
}

.email table a {
	font-weight: initial;
    font-size: 14px;
    color: rgb(3 18 51);
}

.email table tr.read > td {
	background-color: #f6f6f6;
}

.email table tr.read > td {
	font-weight: 400;
}

.email table tr td > i.fa {
	font-size: 1.2em;
	line-height: 1.5em;
	text-align: center;
}

.email table tr td > i.fa-star {
	color: #f39c12;
}

.email table tr td > i.fa-bookmark {
	color: #e74c3c;
}

.email table tr > td.action {
	padding-left: 0px;
	padding-right: 2px;
}

.grid {
    position: relative;
    width: 100%;
    background: #fff;
    color: #666666;
    border-radius: 2px;
    margin-bottom: 25px;
    box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.1);
}



.grid .grid-header:after {
    clear: both;
}

.grid .grid-header span,
.grid .grid-header > .fa {
    display: inline-block;
    margin: 0;
    font-weight: 300;
    font-size: 1.5em;
    float: left;
}

.grid .grid-header span {
    padding: 0 5px;
}

.grid .grid-header > .fa {
    padding: 5px 10px 0 0;
}

.grid .grid-header > .grid-tools {
    padding: 4px 10px;
}

.grid .grid-header > .grid-tools a {
    color: #999999;
    padding-left: 10px;
    cursor: pointer;
}

.grid .grid-header > .grid-tools a:hover {
    color: #666666;
}

.grid .grid-body {
    padding: 15px 20px 15px 20px;
    font-size: 0.9em;
    line-height: 1.9em;
}

.grid .full {
    padding: 0 !important;
}

.grid .transparent {
    box-shadow: none !important;
    margin: 0px !important;
    border-radius: 0px !important;
}

.grid.top.black > .grid-header {
    border-top-color: #000000 !important;
}

.grid.bottom.black > .grid-body {
    border-bottom-color: #000000 !important;
}

.grid.top.blue > .grid-header {
    border-top-color: #007be9 !important;
}

.grid.bottom.blue > .grid-body {
    border-bottom-color: #007be9 !important;
}

.grid.top.green > .grid-header {
    border-top-color: #00c273 !important;
}

.grid.bottom.green > .grid-body {
    border-bottom-color: #00c273 !important;
}

.grid.top.purple > .grid-header {
    border-top-color: #a700d3 !important;
}

.grid.bottom.purple > .grid-body {
    border-bottom-color: #a700d3 !important;
}

.grid.top.red > .grid-header {
    border-top-color: #dc1200 !important;
}

.grid.bottom.red > .grid-body {
    border-bottom-color: #dc1200 !important;
}

.grid.top.orange > .grid-header {
    border-top-color: #f46100 !important;
}

.grid.bottom.orange > .grid-body {
    border-bottom-color: #f46100 !important;
}

.grid.no-border > .grid-header {
    border-bottom: 0px !important;
}

.grid.top > .grid-header {
    border-top-width: 4px !important;
    border-top-style: solid !important;
}

.grid.bottom > .grid-body {
    border-bottom-width: 4px !important;
    border-bottom-style: solid !important;
}

/* Absolute Center Spinner */
#overlay,.overlay_new {
  position: fixed;
  z-index: 999;
  overflow: show;
  margin: auto;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 50px;
  height: 50px;
}

/* Transparent Overlay */
#overlay:before,.overlay_new:before {
  content: '';
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255,255,255,0.5);
}

/* :not(:required) hides these rules from IE9 and below */
#overlay:not(:required),.overlay_new:not(:required) {
  /* hide "loading..." text */
  font: 0/0 a;
  color: transparent;
  text-shadow: none;
  background-color: transparent;
  border: 0;
}

#overlay:not(:required):after,.overlay_new:not(:required):after {
  content: '';
  display: block;
  font-size: 10px;
  width: 50px;
  height: 50px;
  margin-top: -0.5em;

  border: 3px solid rgba(33, 150, 243, 1.0);
  border-radius: 100%;
  border-bottom-color: transparent;
  -webkit-animation: spinner 1s linear 0s infinite;
  animation: spinner 1s linear 0s infinite;


}

/* Animation */

@-webkit-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-moz-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-o-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

@-webkit-keyframes rotation {
   from {-webkit-transform: rotate(0deg);}
   to {-webkit-transform: rotate(359deg);}
}
@-moz-keyframes rotation {
   from {-moz-transform: rotate(0deg);}
   to {-moz-transform: rotate(359deg);}
}
@-o-keyframes rotation {
   from {-o-transform: rotate(0deg);}
   to {-o-transform: rotate(359deg);}
}
@keyframes rotation {
   from {transform: rotate(0deg);}
   to {transform: rotate(359deg);}
}

#message-modal .modal-content {
	width: 700px !important;
}
.grid-title {
	text-align:left;
	font-size:20px;
}
.email-app main {
    min-width: 0;
    flex: 1;
    padding: 1rem;
}

.email-app .message .toolbar {
    padding-bottom: 1rem;
    border-bottom: 1px solid #e1e6ef;
}

.email-app .message .details .title {
    padding: 1rem 0;
    font-weight: bold;
}

.email-app .message .details .header {
    display: flex;
    padding: 1rem 0;
    margin: 1rem 0;
    border-top: 1px solid #e1e6ef;
    border-bottom: 1px solid #e1e6ef;
}

.email-app .message .details .header .avatar {
    width: 40px;
    height: 40px;
    margin-right: 1rem;
}

.email-app .message .details .header .from {
    font-size: 12px;
    color: #9faecb;
    align-self: center;
}

.email-app .message .details .header .from span {
    display: block;
    font-weight: bold;
}

.email-app .message .details .header .date {
    margin-left: auto;
}

.email-app .message .details .attachments {
    padding: 1rem 0;
    margin-bottom: 1rem;
    border-top: 3px solid #f9f9fa;
    border-bottom: 3px solid #f9f9fa;
}

.email-app .message .details .attachments .attachment {
    display: flex;
    margin: 0.5rem 0;
    font-size: 12px;
    align-self: center;
}

.email-app .message .details .attachments .attachment .badge {
    margin: 0 0.5rem;
    line-height: inherit;
}

.email-app .message .details .attachments .attachment .menu {
    margin-left: auto;
}

.email-app .message .details .attachments .attachment .menu a {
    padding: 0 0.5rem;
    font-size: 13px;
    color: #e1e6ef;
}


@media (max-width: 575px) {
    .email-app .message .header {
        flex-flow: row wrap;
    }
    .email-app .message .header .date {
        flex: 0 0 100%;
    }
}
.composebtn {
	font-size:13px;
}
						</style>
							
							<div class="row">
	<!-- BEGIN INBOX -->
	<div class="col-md-12">
		<div class="grid email">
		<div id="overlay" style="display: none;"><div class="spinner"></div></div>
			<form id="search_sumbit1" method="POST">
				
				<div class="col-md-12" style="margin-bottom:15px;">
					<div class="pull-left">
						<a class="btn btn-block btn-info composebtn" data-toggle="modal" data-target="#compose-modal" onclick="tab_opon_popup()"><i class="fa fa-pencil" ></i> Compose Email</a>
					</div>
					<div class="pull-right" style="display: flex;">
						<input id="search_text" type="text" class="form-control" placeholder="Search" style="min-width: 300px;">
						<button type="submit" class="btn btn-info pull-right mleft10">Search</button>
					</div>
				</div>
				<input type="hidden" id="search_mail" value="">
			</form>
			<div  class="header" id="myHeader" style="display:none;">
				<div class="col-md-2"></div>
				<div class="col-md-10" style="background: #fff;">
					<div class="col-md-2" style="width:auto" id="cur_delete">
						<a href="javascript:void(0);" style="color:#666" id="del_mail"><i class="fa fa-trash fa-2x" id="" style="color:red"></i></a>
					</div>
					<div class="col-md-2" style="width:auto;display:none;padding-top:6px" id="delete_ever">
						<a href="javascript:void(0);" id="del_mail1" style="color:#666" >Delete For Ever</a>
					</div>
					<div class="col-md-2" style="width:auto;padding-top:6px">
						<a href="javascript:void(0);" style="color:#666" id="read_mail">Mark as Read </a>
					</div>
					<div class="col-md-2" style="width:auto;padding-top:6px">
						<a href="javascript:void(0);" style="color:#666" id="unread_mail">Mark as Unread </a>
					</div>
				</div>
			</div>
			<div class="grid-body content">
					<!-- BEGIN INBOX MENU -->
				<div class="row">
					<div class="col-md-2">
						<input type="hidden" id="req_page" value="1">
						<input type="hidden" id="folder" value="INBOX">
						<div id="folder_id">
						
						</div>
					</div>
					<!-- END INBOX MENU -->
					
					<!-- BEGIN INBOX CONTENT -->
					<div class="col-md-10">
						<div class="table-responsive">
							<form id="formId" >
								<input type="hidden" value="1" id="sort_val">
								<input type="hidden" value="date" id="sort_option">
								<table class="table dataTable" id="table">
									<tbody>
									<thead>
										<th><input type="checkbox" id="select_all" onclick="check_all(this)"></th>
										<?php /*<th><b>Unread Icon</b></th>*/?>
										<th id="th_from" class="th_class headerSortDown"><a href="javascript:void(0)" onclick="ch_sort('from','th_from')"><span id="tab_from" class="from_a a_header"><b>From</b></span><span id="tab_to" style="display:none" class="from_a a_header"><b>To</b></span></a></th>
										<?php /*<th><b>To</b></th>*/?>
										<th id="th_subject" class="th_class headerSortDown"><a href="javascript:void(0)" onclick="ch_sort('subject','th_subject')" class="subject_a a_header"><b>Subject</b></a></th>
										<?php /*<th><b>Content</b></th>*/?>
										<th><b>Leads / Deals</b></th>
										<th ><b>Attachement Icon</b></th>
										<th class=" th_class headerSortDown" id="th_date"><a href="javascript:void(0)" onclick="ch_sort('date','th_date')" class="a_header date_a th_head_color"><b>Date</b></a></th>
										<?php /*<th class=" th_class" id="th_date" ><b>Date</b></th>*/?>
									</thead>
									<?php //echo $folders['table']; ?>
									</tbody> 
								</table>
							</form>
							<?php //echo $pagination;?>
							<div id='pagination'></div>  
							<span id="updatespan"><?php echo $folders['field']; ?></span>
						</div>
						
					</div>
					<div class="modal fade" id="message-modal" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-wrapper">
							<div class="modal-dialog">
								<div class="modal-content">
									
								</div>
							</div>
						</div>
					</div>

					<div class="modal fade" id="deal-modal" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-wrapper">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header bg-blue">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
										<h4 class="modal-title">Change Deal</h4>
									</div>
									<form id="changedeal" action="<?php echo admin_url('company_mail/changedeals'); ?>" method="post">
										<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
										<div class="modal-body" style="min-height:66px;">
											<div class="col-md-12">
												<div class="form-group" id="rel_id_wrapper">
													<div id="rel_id_select">
														<select  id="rel_id" name="rel_id" class="ajax-sesarch" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
														</select>
														<input type="hidden" name="uid" value="" />
													</div>
													<input type="hidden" id="rel_type" value="project">
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
											<button type="submit" class="btn btn-primary pull-right">Update Deal</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<!-- END INBOX CONTENT -->
				
					<?php $this->load->view('admin/staff/emailcomposer.php') ?>
					
					
				</div>
			</div>
		</div>
	</div>
	<!-- END INBOX -->
</div>
							</div>
							
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>

<script>
    function over_lay(cur_id) {
        document.getElementById('overlay_' + cur_id).style.display = '';
        $(".btn").prop('disabled', true);
    }

    function mget_file(c_id, c) {
        var fcnt = $('#' + c + 'filecnt').val();
        var tcnt = $('#' + c + 'totcnt').val();
        if (tcnt <= 1) {
            document.getElementById(c_id).click();
        } else {
            document.getElementById(c_id + '_' + tcnt).click();
        }
    }

    function rm_file(a, b) {
        var c = $('#' + a + '_file').val() + b + ',';
        $('#' + a + '_' + b + '_del').hide();
        $('#' + a + '_file').val(c);
    }

    function get_up_val(c_id, c) {
        var fcnt = $('#' + c + 'filecnt').val();
        var tcnt = $('#' + c + 'totcnt').val();
        var allcnt = $('#' + c + 'allcnt').val();
        var req_tcnt = parseInt(tcnt) + parseInt(1);
        var req_fcnt = parseInt(fcnt) + parseInt(1);
        var req_cid = "'" + c_id + "'";
        var req_c = "'" + c + "'";
        var req_id = "'" + c + "div_" + req_tcnt;
        var file = $('#' + c_id);
        if (tcnt != 1) {
            var file = $('#' + c_id + '_' + tcnt);
        }
        var fileName = '';
        allcnt1 = parseInt(allcnt) + parseInt(file[0].files.length);

        for (var i = 0; i < file[0].files.length; i++) {
            if (allcnt1 == file[0].files.length) {
                var j = i;
            } else {
                var j = parseInt(allcnt) + i;
            }
            var chr = "'" + c + "'";
            var c_no = "'" + j + "'";
            fileName = fileName + '<div id="' + c + '_' + j + '_del" class="col-md-12" style="float:left;margin-top:5px;margin-top:5px;font-weight: 900;font-size: 15px;"><div class="col-md-9">' + file[0].files[i].name + '</div><div class="col-md-3"><a href="javascript:void(0)"  onclick="rm_file(' + chr + ',' + c_no + ')" title="Delete"><i class="fa fa-trash fa-2x" id="" style="color:red"></i></a></div></div>';
        }
        $('.ch_files_' + c).append(fileName);
        var req_file = '<div id="' + req_id + '"><input type="file" id="' + c_id + '_' + req_tcnt + '" style="display:none" name="attachment[]" multiple onchange="get_up_val(' + req_cid + ',' + req_c + ')"></div><br><br>';

        $('#' + c + '_files').append(req_file);
        $('#' + c + 'filecnt').val(req_fcnt);
        $('#' + c + 'totcnt').val(req_tcnt);
        $('#' + c + 'allcnt').val(allcnt1);

    }

    function ch_sort(ch_val, b) {
        var sort_val = $("#sort_val").val();
        var sort_option = $("#sort_option").val();
        $("#sort_option").val(ch_val);
        $("#sort_val").val(1);
        if (sort_option == ch_val) {
            if (sort_val == 1) {
                $("#sort_val").val(0);
            }
        }
        $(".th_class").removeClass("headerSortDown");
        $(".th_class").removeClass("headerSortUp");
        $(".th_class").addClass("headerSortDown");
        $(".a_header").removeClass("th_head_color");
        var ch_sort_val = $("#sort_val").val();
        $("#" + b).removeClass("headerSortDown");
        $("." + ch_val + "_a").addClass("th_head_color");
        if (ch_sort_val == 1) {
            $('#' + b).addClass('headerSortDown');
        } else {
            $('#' + b).addClass('headerSortUp');
        }
        //var pageno = $('#req_page').val();
        loadPagination(1);

    }
    $(document).ready(function() {

        $('#pagination').on('click', 'a', function(e) {
            e.preventDefault();
            var pageno = $(this).attr('data-ci-pagination-page');
            $('#req_page').val(pageno);
            loadPagination(pageno);
        });
    });
    loadPagination(1);

    function loadPagination(pagno) {
        if (pagno != 0) {
            var uid = $("#uid").val();
            var folder = $("#folder").val();
        } else {
            var uid = '';
            var folder = 'INBOX';
        }
        var sort_val = $("#sort_val").val();
        var sort_option = $("#sort_option").val();
        var search_txt = $('#search_mail').val();
        document.getElementById('overlay').style.display = '';
        $.post(admin_url + 'company_mail/pagination_mail/' + pagno, {
                'folder': folder,
                'uid': uid,
                'sort_val': sort_val,
                'sort_option': sort_option,
                'search_txt': search_txt
            },
            function(data, status) {
                document.getElementById('overlay').style.display = 'none';
                $("body").css('overflow', 'auto');
                var json = $.parseJSON(data);
                if (status == 'success') {
                    var json = $.parseJSON(data);
                    $(".table tbody").html(json.folders.table);
                    $("#pagination").html(json.pagination);
                    $("#updatespan").html(json.field);
                    $("#folder_id").html(json.folders.folder_values);
                } else {
                    $(".table tbody").html('<tr><td colspan="6" style="text-align:center;">Cannot Fetch Records.</td></tr>');
                }
                $("#select_all").prop('checked', false);
            });

    }

    function getMailList(val) {
        $("#sort_val").val(1);
        $("#sort_option").val('date');
        $('#search_mail').val('');
        $('#search_text').val('');
        $("#folder").val(val);
        $('#req_page').val(1);
        loadPagination(1);
    }

    /*$("#description").onkeyup(function() {
        var s = $(this).val(); 
        alert(s);
       // tinyMCE.activeEditor.setContent(s);
    });*/
    var frm4 = $('#search_sumbit1');
    frm4.submit(function(e) {

        e.preventDefault();;
        var search_txt = $('#search_text').val();
        $('#search_mail').val(search_txt);
        $('#req_page').val(1);
        loadPagination(1);
        //$('form').preventDoubleSubmission();

    });

    $('.nav-pills li').click(function() {
        $('.nav-pills li.active').removeClass('active');
        $(this).addClass('active');
        var title = $.trim($(this).text());
        var titleHeader;
        if (title == 'Inbox') {
            titleHeader = '<i class="fa fa-inbox"></i> ' + title;
        } else if (title == 'All Mail') {
            titleHeader = '<i class="fa fa-inbox"></i> ' + title;
        } else if (title == 'Starred') {
            titleHeader = '<i class="fa fa-star"></i> ' + title;
        } else if (title == 'Important') {
            titleHeader = '<i class="fa fa-bookmark"></i> ' + title;
        } else if (title == 'Sent Mail') {
            titleHeader = '<i class="fa fa-mail-forward"></i> ' + title;
        } else if (title == 'Drafts') {
            titleHeader = '<i class="fa fa-pencil-square-o"></i> ' + title;
        } else if (title == 'Spam') {
            titleHeader = '<i class="fa fa-folder"></i> ' + title;
        } else if (title == 'Trash') {
            titleHeader = '<i class="fa fa-trash"></i> ' + title;
        } else if (title == 'Bin') {
            titleHeader = '<i class="fa fa-trash"></i> ' + title;
        } else {
            titleHeader = '<i class="fa fa-folder"></i> ' + title;
        }
        $(".grid-title").html(titleHeader);
    });
    //});
    function gettemplate_list() {
        $.post(admin_url + 'company_mail/template_list', {},
            function(data) {
                var json = $.parseJSON(data);
                $("#template_list1").html(json.table);
                $("#template_header").html(json.header);
                $("#ch_default_temp").append('');
                $('#ch_default_temp').empty();
                $("#ch_default_temp").selectpicker("refresh");
                $("#ch_default_temp").append(json.select_drop);
                $("#ch_default_temp").selectpicker("refresh");

            });
    }

    function add_content(uid) {
        document.getElementById('overlay_new').style.display = '';
        $.post(admin_url + 'company_mail/content', {
                uid: uid
            },
            function(data, status) {
                var json = $.parseJSON(data);
                $('.ch_files_f').html('');
                $('#f_files').html('');
                $('#forward_toemail').val('');
                $('#forward_ccemail').val('');
                $('#forward_bccemail').val('');
                $('#ftotcnt').val(1);
                $('#ffilecnt').val(1);
                $('#fallcnt').val(0);
                $('#f_file').val('');
                check_email('', 'forward_toemail');
                $('#f_getFile').val('');
                $('#forward_subject').val('Fwd: ' + json.subject);
                tinyMCE.get('forward_description').setContent(json.message);
                document.getElementById('overlay_new').style.display = 'none';

            });
    }

    function add_to(uid) {
        document.getElementById('overlay_new1').style.display = '';
        $.post(admin_url + 'company_mail/to_mail', {
                uid: uid
            },
            function(data, status) {
                console.log('data');
                var json = $.parseJSON(data);
                $('#reply_toemail').val(json.from_address);
                $('#ch_uid').val(uid);
                $('#reply_subject').val('Re: ' + json.subject);
                $('.ch_files_r').html('');
                $('#r_files').html('');
                $('#reply_ccemail').val('');
                $('#reply_bccemail').val('');
                $('#ftotcnt').val(1);
                $('#rfilecnt').val(1);
                $('#rallcnt').val(0);
                $('#r_file').val('');
                $('#reply-modal [name="rel_type"]').val(json.rel_data.rel_type);
                $('#reply-modal [name="rel_id"]').val(json.rel_data.rel_id);
                $('#reply-modal [name="parent_id"]').val(json.rel_data.parent_id);
                tinyMCE.get('reply_description').setContent('');
                $('#r_getFile').val('');
                document.getElementById('overlay_new1').style.display = 'none';

            });
    }

    function add_reply_all(uid) {
        document.getElementById('overlay_new1').style.display = '';
        $.post(admin_url + 'company_mail/add_reply_all', {
                uid: uid
            },
            function(data, status) {
                var json = $.parseJSON(data);
                $('#reply_toemail').val(json.from_address);
                $('#ch_uid').val(uid);
                $('#reply_subject').val('Re: ' + json.subject);

                $('.ch_files_r').html('');
                $('#r_files').html('');
                $('#reply_ccemail').val('');
                $('#reply_bccemail').val('');
                $('#ftotcnt').val(1);
                $('#rfilecnt').val(1);
                $('#rallcnt').val(0);
                $('#r_file').val('');
                tinyMCE.get('reply_description').setContent('');
                $('#r_getFile').val('');
                document.getElementById('overlay_new1').style.display = 'none';

            });
    }

    function getMessage(val) {
        var folder = $("#folder").val();
        document.getElementById('overlay').style.display = '';
        $.post(admin_url + 'company_mail/getmessage', {
                folder: folder,
                uid: val
            },
            function(data, status) {
                document.getElementById('overlay').style.display = 'none';
                if (status == 'success') {
                    var json = $.parseJSON(data);
                    $('#message-modal .modal-content').html(json.body);
                    // show modal
                    $('#message-modal').modal('show');
                } else {
                    $('#message-modal .modal-content').html('Cannot Fetch Message.');
                    // show modal
                    $('#message-modal').modal('show');
                }
            });
    }

    function submit_default() {
        //$('#default_submit').prop('disabled', true);
        var default_temp = $("#ch_default_temp").val();
        $.post(admin_url + 'company_mail/change_default', {
                default_template: default_temp
            },
            function(data, status) {
                var json = $.parseJSON(data);
                if (json.status == 'success') {
                    //tinyMCE.activeEditor.setContent(json.description);
                    $(".tabs").removeClass("active");
                    $(".tabs h6").removeClass("font-weight-bold");
                    $(".tabs h6").addClass("text-muted");
                    $("#tab01").children("h6").removeClass("text-muted");
                    $("#tab01").children("h6").addClass("font-weight-bold");
                    $("#tab01").addClass("active");

                    current_fs = $(".active");

                    next_fs = "#tab011";

                    $("fieldset").removeClass("show");
                    $(next_fs).addClass("show");
                    var text = tinyMCE.get('description').getContent();
                    var req_text = text + json.description
                    tinyMCE.get('description').setContent(req_text);
                    //tinyMCE.activeEditor.execCommand('mceInsertContent',false,json.description);
                    //$('#default_submit').prop('disabled', false);
                    //gettemplate_list();

                }
            });
        if (default_temp == '') {
            var text = tinyMCE.get('description').getContent();
            tinyMCE.get('description').setContent(text);
        }
    }

    function updatedeal(val) {
        document.getElementById('overlay').style.display = '';
        $.post(admin_url + 'staff/getdealsname', {
                uid: val
            },
            function(data, status) {
                document.getElementById('overlay').style.display = 'none';
                if (status == 'success') {
                    var json = $.parseJSON(data);
                    console.log(json);
                    $("#rel_id_select").html(json.company);
                    $("#rel_id_select select").selectpicker('refresh');
                    // show modal
                    $('#deal-modal').modal('show');
                } else {
                    $('#deal-modal .modal-content .modal-body').html('Cannot Fetch Message.');
                    // show modal
                    $('#deal-modal').modal('show');
                }
            });
    }

    function reset_form() {
        $('#template_name').val('');
        tinyMCE.get('template_description').setContent('');
    }
</script>
<?php hooks()->do_action('settings_tab_footer', 'email'); ?>
<?php $this->load->view('admin/staff/emailcomposerjs.php') ?>
</body>
</html>