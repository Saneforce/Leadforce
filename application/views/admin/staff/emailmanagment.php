<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">

				<div class="panel_s">
					<div class="panel-body">
						<?php if(has_permission('staff','','create')){ ?>
						<div class="_buttons">
							<!-- <a href="<?php echo admin_url('staff/member'); ?>" class="btn btn-default pull-left display-block"><?php echo _l('new_staff'); ?></a>
							<a href="<?php echo admin_url('roles'); ?>" class="btn btn-default pull-left mleft5"><?php echo _l('acs_roles'); ?></a>
							<a href="<?php echo admin_url('designation'); ?>" class="btn btn-default pull-left mleft5"><?php echo _l('acs_designation'); ?></a>
							<a href="<?php echo admin_url('staff/hierarchy'); ?>" class="btn btn-default pull-left mleft5"><?php echo _l('acs_hierarchy'); ?></a>
							<a href="<?php echo admin_url('tasks/emailmanagement'); ?>" class="btn btn-info pull-left mleft5"><?php echo _l('acs_emailmanagemnet'); ?></a> -->
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading" />
						<?php } ?>
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
	color: #666;
	border-top: 0;
	border-left: 3px solid transparent;
	border-radius: 0px;
}

.email .nav.nav-pills.nav-stacked > li.active > a,
.email .nav.nav-pills.nav-stacked > li.active > a:hover {
	background-color: #f6f6f6;
	border-left-color: #3c8dbc;
	color: #444;
}

.email .nav.nav-pills.nav-stacked > li.header {
	color: #777;
	text-transform: uppercase;
	position: relative;
	padding: 0px 0 10px 0;
}

.email table {
	font-weight: 600;
}

.email table a {
	color: #666;
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
#overlay {
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
#overlay:before {
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
#overlay:not(:required) {
  /* hide "loading..." text */
  font: 0/0 a;
  color: transparent;
  text-shadow: none;
  background-color: transparent;
  border: 0;
}

#overlay:not(:required):after {
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
    font-size: 14px;
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
	font-size:10.5px;
}
						</style>
							
							<div class="row">
	<!-- BEGIN INBOX -->
	<div class="col-md-12">
		<div class="grid email">
		<div id="overlay" style="display: none;"><div class="spinner"></div></div>
			<div class="grid-body">
				<div class="row">
					<!-- BEGIN INBOX MENU -->
					<div class="col-md-2">
						<h2 class="grid-title"><i class="fa fa-inbox"></i> Inbox</h2>
						<a class="btn btn-block btn-primary composebtn" data-toggle="modal" data-target="#compose-modal"><i class="fa fa-pencil"></i>&nbsp;&nbsp;COMPOSE EMAIL</a>

						<hr>
						<div>
							<ul class="nav nav-pills nav-stacked">
								<li class="header">Folders</li>
								<?php
								$i = 0;
								foreach($folders['folders'] as $name) { 
									$icon = ucwords(strtolower(str_replace('[Gmail]/','',$name)));
									if($icon == 'Inbox')
										$faicon = 'fa-inbox';
									else if($icon == 'All Mail')
										$faicon = 'fa-inbox';
									else if($icon == 'Drafts')
										$faicon = 'fa-pencil-square-o';
									else if($icon == 'Important')
										$faicon = 'fa-bookmark';
									else if($icon == 'Sent Mail')
										$faicon = 'fa-mail-forward';
									else if($icon == 'Spam')
										$faicon = 'fa-folder';
									else if($icon == 'Starred')
										$faicon = 'fa-star';
									else if($icon == 'Trash')
										$faicon = 'fa-trash';
									else if($icon == 'Bin')
										$faicon = 'fa-trash';
									else {
										$icon = $name;
										$faicon = 'fa-folder';
									}
									if($i==0) {
										$class = 'active';
									} else {
										$class = '';
									}
									?>
									<li class="<?php echo $class; ?>"><a href="#" id="<?php echo strtolower(str_replace(' ','-',$icon)); ?>" onClick="getMailList('<?php echo $icon; ?>');"><i class="fa <?php echo $faicon; ?>"></i> <?php echo $icon; ?></a></li>
									<!-- <li><a href="#" id="starred" onClick="getMailList('starred');"><i class="fa fa-star"></i> Starred</a></li>
									<li><a href="#" id="important" onClick="getMailList('important');"><i class="fa fa-bookmark"></i> Important</a></li>
									<li><a href="#" id="sent" onClick="getMailList('sent');"><i class="fa fa-mail-forward"></i> Sent</a></li>
									<li><a href="#" id="drafts" onClick="getMailList('drafts');"><i class="fa fa-pencil-square-o"></i> Drafts</a></li>
									<li><a href="#" id="spam" onClick="getMailList('spam');"><i class="fa fa-folder"></i> Spam</a></li> -->
								<?php $i++; } ?>
							</ul>
						</div>
					</div>
					<!-- END INBOX MENU -->
					
					<!-- BEGIN INBOX CONTENT -->
					<div class="col-md-10">
						<!-- <div class="row">
							<div class="col-sm-6">
								<label style="" class="">
									<div class="icheckbox_square-blue" style="position: relative;"><input type="checkbox" id="check-all" class="icheck" style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"><ins class="iCheck-helper" style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins></div>
								</label>
								<div class="btn-group">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
										Action <span class="caret"></span>
									</button>
									<ul class="dropdown-menu" role="menu">
										<li><a href="#">Mark as read</a></li>
										<li><a href="#">Mark as unread</a></li>
										<li><a href="#">Mark as important</a></li>
										<li class="divider"></li>
										<li><a href="#">Report spam</a></li>
										<li><a href="#">Delete</a></li>
									</ul>
								</div>
							</div>

							<div class="col-md-6 search-form">
								<form action="#" class="text-right">
									<div class="input-group">
										<input type="text" class="form-control input-sm" placeholder="Search">
										<span class="input-group-btn">
                                            <button type="submit" name="search" style="padding: 9px 10px" class="btn_ btn-primary btn-sm search"><i class="fa fa-search"></i></button></span>
									</div>			 
								</form>
							</div>
						</div> -->
						
						
						<div class="table-responsive">
							<table class="table" id="table">
								<tbody>
								<tr>
									
									<th><b>From</b></th>
									<th><b>To</b></th>
									<th><b>Subject</b></th>
									<th><b>Deals</b></th>
									<th><b>Attachement Icon</b></th>
									<th><b>Date</b></th>
								</tr>
								<?php echo $folders['table']; ?>
								</tbody>
							</table>
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
									<form id="changedeal" action="<?php echo admin_url('tasks/changedeals'); ?>" method="post">
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
					
					<!-- BEGIN COMPOSE MESSAGE -->
					<div class="modal fade" id="compose-modal" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-wrapper">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header bg-blue">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
										<h4 class="modal-title"><i class="fa fa-envelope"></i> Compose New Message</h4>
									</div>
									<form action="<?php echo admin_url('tasks/createtaskbymail'); ?>" method="post">
										<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
										<div class="modal-body">
											<div class="form-group">
												<input name="toemail" type="email" class="form-control" placeholder="To" id="toemail">
											</div>
											<div class="form-group">
												<input name="ccemail" type="email" class="form-control" placeholder="Cc">
											</div>
											<div class="form-group">
												<input name="bccemail" type="email" class="form-control" placeholder="Bcc">
											</div>
											<div class="form-group">
												<input name="name" type="text" class="form-control" placeholder="Subject">
											</div>
											<div class="form-group" app-field-wrapper="description" ><textarea id="description" name="description" class="form-control tinymce" rows="6"></textarea></div> 
											<!-- <div class="form-group">														<input type="file" name="attachment">
											</div> -->
											<input type="hidden" name="priority" value="1">
											<input type="hidden" name="repeat_every_custom" value="1">
											<input type="hidden" name="repeat_type_custom" value="day">
											<input type="hidden" name="rel_type" value="project">
											<input type="hidden" name="tasktype" value="2">
											<input type="hidden" name="billable" value="on">
											<input type="hidden" name="task_mark_complete_id" value="">
											<input type="hidden" name="tags" value="">
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
											<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-envelope"></i> Send Message</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<!-- END COMPOSE MESSAGE -->
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
$(function(){
	// document.getElementById('overlay').style.display = '';
	
	// $.post(admin_url + 'staff/imapint',
    // {
	// 	folder: 'Inbox',
	// 	uid:''
    // },
	// function(data,status){
	// 	document.getElementById('overlay').style.display = 'none'; 
	// 	console.log(data);
	// 	if(status == 'success') {
	// 		var json = $.parseJSON(data);
	// 		$(".table tbody").html(json.table);
	// 		$("#updatespan").html(json.field);
	// 	} else {
	// 		$(".table tbody").html('<tr><td colspan="4" style="text-align:center;">Cannot Fetch Records.</td></tr>');
	// 	}
    // });
	
	// $.getJSON( admin_url + 'staff/imapint?folder=inbox&uid=', function( data ) {
	// 	document.getElementById('overlay').style.display = 'none'; 
	// 	console.log(data);
	// 	$(".table tbody").html(data.table);
	// 	$("#updatespan").html(data.field);
	// });
	
	$(window).scroll(function() {
    	if($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
		var uid = $("#uid").val();
		var folder = $("#folder").val();
			if(uid) {
				$("body").css('overflow', 'hidden');
				$('#uid').remove();
				$('#folder').remove();
				document.getElementById('overlay').style.display = '';
				$.post(admin_url + 'staff/imapint',
				{
					folder: folder,
					uid:uid
				},
				function(data,status){
					document.getElementById('overlay').style.display = 'none'; 
					$("body").css('overflow', 'auto');
					if(status == 'success') {
						var json = $.parseJSON(data);
						$(".table tbody").append(json.table);
						$("#updatespan").html(json.field);
					} else {
						$(".table tbody").html('<tr><td colspan="4" style="text-align:center;">Cannot Fetch Records.</td></tr>');
					}
				});
			}
		}

	});

	$('.nav-pills li').click(function() {
		$('.nav-pills li.active').removeClass('active');
		$(this).addClass('active');
		var title = $.trim($(this).text());
		var titleHeader;
		if(title == 'Inbox') {
			titleHeader = '<i class="fa fa-inbox"></i> '+title;
		} else if(title == 'All Mail'){
			titleHeader = '<i class="fa fa-inbox"></i> '+title;
		} else if(title == 'Starred'){
			titleHeader = '<i class="fa fa-star"></i> '+title;
		} else if(title == 'Important'){
			titleHeader = '<i class="fa fa-bookmark"></i> '+title;
		} else if(title == 'Sent Mail'){
			titleHeader = '<i class="fa fa-mail-forward"></i> '+title;
		} else if(title == 'Drafts'){
			titleHeader = '<i class="fa fa-pencil-square-o"></i> '+title;
		} else if(title == 'Spam') {
			titleHeader = '<i class="fa fa-folder"></i> '+title;
		} else if(title == 'Trash') {
			titleHeader = '<i class="fa fa-trash"></i> '+title;
		} else if(title == 'Bin') {
			titleHeader = '<i class="fa fa-trash"></i> '+title;
		} else {
			titleHeader = '<i class="fa fa-folder"></i> '+title;
		}
		$(".grid-title").html(titleHeader);
	});
});

function getMailList(val){
	document.getElementById('overlay').style.display = '';
	$.post(admin_url + 'staff/imapint',
	{
		folder: val,
		uid:''
	},
	function(data,status){
		document.getElementById('overlay').style.display = 'none'; 
		console.log(data);
		if(status == 'success') {
			var json = $.parseJSON(data);
			$(".table tbody").html(json.table);
			$("#updatespan").html(json.field);
		} else {
			$(".table tbody").html('<tr><td colspan="4" style="text-align:center;">Cannot Fetch Records.</td></tr>');
		}
	});
}

function getMessage(val){
	var folder = $("#folder").val();
	document.getElementById('overlay').style.display = '';
	$.post(admin_url + 'staff/getmessage',
	{
		folder: folder,
		uid:val
	},
	function(data,status){
		document.getElementById('overlay').style.display = 'none'; 
		if(status == 'success') {
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

function updatedeal(val){
	document.getElementById('overlay').style.display = '';
	$.post(admin_url + 'staff/getdealsname',
	{
		uid:val
	},
	function(data,status){
		document.getElementById('overlay').style.display = 'none'; 
		if(status == 'success') {
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
</script>

  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<?php hooks()->do_action('settings_tab_footer', 'email'); ?>
<script>
  var BASE_URL = "<?php echo base_url(); ?>";
 
 $(document).ready(function() {
    $( "#toemail" ).autocomplete({
 
        source: function(request, response) {
            $.ajax({
            url: BASE_URL + "admin/emails/search",
            data: {
                    term : request.term
             },
            dataType: "json",
            success: function(data){
               var resp = $.map(data,function(obj){
                    return obj.email;
               }); 
 
               response(resp);
            }
        });
    },
    minLength: 1
 });
});
 
</script> 

</body>
</html>
