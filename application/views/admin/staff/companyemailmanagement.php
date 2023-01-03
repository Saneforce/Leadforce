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
					<!-- BEGIN COMPOSE MESSAGE -->
					<div class="modal fade" id="compose-modal" tabindex="-1" role="dialog" aria-hidden="true" style="margin-bottom:25px;">
						<div class="modal-wrapper">
							<div class="modal-dialog" style="width:74.5% ">
								<div class="modal-content">
									<div class="modal-header bg-blue">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
										<h4 class="modal-title"><i class="fa fa-envelope"></i> Compose New Message</h4>
									</div>
									<div class="col-md-12 bg-white" style="border-radius:6px;">
										<div class="col-md-3">
											<ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked mtop15">
												<li class="tabs active" id="tab01">
													<a class="text-muted">Compose Email</a>
												<li class="tabs " id="tab02" onclick="gettemplate_list()">
													<a class="text-muted">Templates</a>
												</li>
												<li class="tabs " id="tab03" onclick="reset_form()">
													<a class="font-weight-bold text-muted">Create Template</a>
												</li>
											</ul>
										</div>
										<div class="col-md-9">
											<fieldset id="tab011" class="show">
												<form action="<?php echo admin_url('company_mail/createtaskcompanymail'); ?>" method="post" id="compose_email" enctype='multipart/form-data' onsubmit="over_lay('compose')">
													<input type="hidden" id="cur_draft_id" name="cur_draft_id">
													<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
													<div class="modal-body">
														<div class="form-group">
															<input name="toemail" type="email" class="form-control" placeholder="To" id="toemail" onblur="deal_values()" onkeyup="check_email(this.value,'toemail')"  multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$" >
															<input type='hidden' id='selectuser_ids' />
														</div>
														<div class="form-group">
															<input name="ccemail" type="email" class="form-control" placeholder="Cc" id="toccemail" onkeyup="check_email(this.value,'toccemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$">
														</div>
														<div class="form-group">
															<input name="bccemail" type="email" class="form-control" placeholder="Bcc" id="tobccemail" onkeyup="check_email(this.value,'tobccemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$">
															<input  type="hidden" id="deal_map" value="<?php echo get_option('deal_map');?>">
														</div>
														<div class="form-group pipeselect" >
															<label ><b>Choose Template</b></label>
																<select class="selectpicker" data-none-selected-text="<?php echo _l('Select Template'); ?>" name="select_template" id="ch_default_temp" data-width="100%" data-live-search="true" onchange="submit_default()">
																<option value=''>None</option>
																<?php if(!empty($templates)){
																	foreach($templates as $template1){
																	?>
																	<option value="<?php echo $template1['id'];?>"><?php echo $template1['template_name'];?></option>
																	<?php
																	}
																}?>
																</select>
															</div>
														
														
														<?php //if(get_option('deal_map') == 'if more than one open deal – allow to map manually'){?>
															<div class="form-group pipeselect"  style="display:none" id="pipeselect">
															<label ><b>Deal / Lead</b></label>
																<select class="selectpicker" data-none-selected-text="<?php echo _l('Select Ay Deal'); ?>" name="deal_id" id="pipeline_id" data-width="100%" <?php if(get_option('deal_map') == 'if more than one open deal – allow to map manually'){?>data-live-search="true" <?php }?> >
																</select>
															</div>
														<?php //}?>
														<div class="form-group pipeselect" id="activity_type" style="display:none">
															<label ><b>Activity Type</b></label>
															<input type="hidden" name="activity_type" value="close">
															<select class="selectpicker" data-none-selected-text="<?php echo _l('Activity Type'); ?>" name="activity_type"  data-width="100%" required>
																<option value="open">Open</option>
																<option value="close">Close</option>
															</select>
														</div>
														<div class="form-group">
															<input name="name" type="text" class="form-control" placeholder="Subject" id="c_subject" required>
														</div>
														<div class="form-group" app-field-wrapper="description" ><textarea id="description" name="description" class="form-control tinymce1" rows="6"  ><?php //echo $default_val;?></textarea></div> 
														<input type="hidden" name="priority" value="1">
														<input type="hidden" id="mfilecnt" value="1">
														<input type="hidden" id="mtotcnt" value="1">
														<input type="hidden" id="mallcnt" value="0">
														<input type="hidden" id="m_file" name="m_file">
														<input type="hidden" name="repeat_every_custom" value="1">
														<input type="hidden" name="repeat_type_custom" value="day">
														<input type="hidden" name="rel_type" value="">
														<input type="hidden" name="tasktype" value="2">
														<input type="hidden" name="billable" value="on">
														<input type="hidden" name="task_mark_complete_id" value="">
														<input type="hidden" name="tags" value="">
														<button type ="button" class="btn btn-info" style="display:block;" onclick="mget_file('getFile','m')">Add Attachement </button>
														<input type='file' id="getFile" style="display:none" multiple name="attachment[]" onchange="get_up_val('getFile','m')"> 
														
														<div class="ch_files_m list_files">
														</div>
														<div id="m_files"></div>
													</div>
													<div class="modal-footer">
														
														<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
														<div id="overlay_compose" class="overlay_new" style="display:none;position:absolute"><div class="spinner"></div></div>
														<button type="submit" class="btn btn-info pull-right"  style="margin-left:10px"><i class="fa fa-envelope"></i> Send Message</button>
														
													</div>
												</form>
											</fieldset>
											 <fieldset id="tab021" >
												<div id="template_header">
												</div>
												<form method='post' class='form-inline' id='default_template' action='<?php echo admin_url('company_mail/change_default'); ?>'>
													<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
													<div id="template_list1">
													</div>
												</form>
											</fieldset>
											<fieldset id="tab031">
												<form action="<?php echo admin_url('company_mail/create_template'); ?>" method="post" id="template_form">
													<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
													<div class="modal-body">
														<div class="form-group">
															<input name="template_name" type="text" class="form-control" placeholder="Template Name" id="template_name">
															<span class="error1" id="name_error">Please Enter Template Name</span>
														</div>
														<div class="form-group" app-field-wrapper="description" ><textarea id="template_description" name="template_description" class="form-control tinymce" rows="6"></textarea>
														<span class="error1" id="desc_error">Please Enter Text</span>
														</div> 
													</div>
													<div class="modal-footer">
														<button type="submit" class="btn btn-info pull-right">Submit</button>
													</div>
												</form>
											</fieldset>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- END COMPOSE MESSAGE -->
					
					<!--Edit Template -->
					<div class="modal fade" id="Edit-template" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-wrapper">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header bg-blue">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
										<h4 class="modal-title">Edit Template</h4>
									</div>
									<form action="<?php echo admin_url('company_mail/update_template'); ?>" method="post" id="edit_template_form">
										<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
										<input type="hidden" name="template_id" id="template_id1">
										<div class="modal-body">
											<div class="form-group">
												<input name="template_edit_name" type="text" class="form-control" placeholder="Template Name" id="template_edit_name">
												<span class="error1" id="name_edit_error">Please Enter Template Name</span>
											</div>
											<div class="form-group" app-field-wrapper="description" ><textarea id="template_edit_description" name="template_edit_description" class="form-control tinymce" rows="6"></textarea>
											<span class="error1" id="desc_edit_error">Please Enter Text</span>
											</div> 
										</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-info pull-right">Submit</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<!-- END CREATE TEMPLATE -->
					
					<!-- BEGIN FORWARD MESSAGE -->
					<div class="modal fade" id="forward-modal" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-wrapper">
							<div class="modal-dialog">
							<div id="overlay_forward" class="overlay_new" style="display:none"><div class="spinner"></div></div>
							<div id="overlay_new" class="overlay_new" style="display:none"><div class="spinner"></div></div>
								<div class="modal-content">
									<div class="modal-header bg-blue">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
										<h4 class="modal-title"><i class="fa fa-forward"></i> Forward Message</h4>
									</div>
									<form action="<?php echo admin_url('company_mail/forward'); ?>" method="post"  enctype='multipart/form-data'  onsubmit="over_lay('forward')">
										<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
										<div class="modal-body">
											<div class="form-group">
												<input name="toemail" type="email" class="form-control" placeholder="To" id="forward_toemail" onkeyup="check_email(this.value,'forward_toemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$" required>
											</div>
											<div class="form-group">
												<input name="ccemail" type="email" class="form-control" placeholder="Cc" id="forward_ccemail" onkeyup="check_email(this.value,'forward_ccemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$">
											</div>
											<div class="form-group">
												<input name="bccemail" type="email" class="form-control" placeholder="Bcc" id="forward_bccemail" onkeyup="check_email(this.value,'forward_bccemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$">
											</div>
											<div class="form-group">
												<input name="name" type="text" id ="forward_subject" class="form-control" placeholder="Subject" required readonly>
											</div>
											<div class="form-group" app-field-wrapper="description" ><textarea id="forward_description" name="description" class="form-control tinymce" rows="6"></textarea></div> 
											<button type ="button" class="btn btn-primary" style="display:block;" onclick="mget_file('f_getFile','f')">Add Attachement </button>
											<input type='file' id="f_getFile" style="display:none" multiple name="attachment[]" onchange="get_up_val('f_getFile','f')"> 
											<input type="hidden" name="priority" value="1">
											<input type="hidden" name="repeat_every_custom" value="1">
											<input type="hidden" name="repeat_type_custom" value="day">
											<input type="hidden" name="rel_type" value="project">
											<input type="hidden" name="tasktype" value="2">
											<input type="hidden" name="billable" value="on">
											<input type="hidden" name="task_mark_complete_id" value="">
											<input type="hidden" name="tags" value="">
											<input type="hidden" id="ffilecnt" value="1">
											<input type="hidden" id="ftotcnt" value="1">
											<input type="hidden" id="fallcnt" value="0">
											<input type="hidden" id="f_file" name="m_file">
											<div class="ch_files_f">
											</div>
											<div id="f_files"></div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
											<button type="submit" class="btn btn-info pull-right"><i class="fa fa-envelope"></i> Send Message</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<!-- END forward MESSAGE -->
					
					<!-- BEGIN Reply MESSAGE -->
					<div class="modal fade" id="reply-modal" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-wrapper">
							<div class="modal-dialog">
								<div id="overlay_new1" class="overlay_new" style="display:none"><div class="spinner"></div></div>
								<div class="modal-content">
									<div class="modal-header bg-blue">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
										<h4 class="modal-title"><i class="fa fa-reply"></i> Reply Message</h4>
									</div>
									<form action="<?php echo admin_url('company_mail/reply'); ?>" method="post"  enctype='multipart/form-data'  onsubmit="over_lay('new1')">
										<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
										<div class="modal-body">
											<div class="form-group">
												<input name="toemail" type="text" class="form-control" placeholder="To" id="reply_toemail" readonly >
												<input name="ch_uid" type="hidden" id="ch_uid"  >
											</div>
											<div class="form-group">
												<input name="ccemail" type="email" class="form-control" placeholder="Cc" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$" id="reply_ccemail" onkeyup="check_email(this.value,'reply_ccemail')">
											</div>
											<div class="form-group">
												<input name="bccemail" type="email" class="form-control" placeholder="Bcc" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$" id="reply_bccemail" onkeyup="check_email(this.value,'reply_bccemail')">
											</div>
											<div class="form-group">
												<input name="name" type="text" id ="reply_subject" class="form-control" placeholder="Subject" readonly>
											</div>
											<div class="form-group" app-field-wrapper="description" ><textarea id="reply_description" name="description" class="form-control tinymce" rows="6"></textarea></div> 
											<button type ="button" class="btn btn-primary" style="display:block;" onclick="mget_file('r_getFile','r')">Add Attachement </button>
											<input type='file' id="r_getFile" style="display:none" multiple name="attachment[]" onchange="get_up_val('r_getFile','r')"> 
											<input type="hidden" name="priority" value="1">
											<input type="hidden" name="repeat_every_custom" value="1">
											<input type="hidden" name="repeat_type_custom" value="day">
											<input type="hidden" name="rel_type" value="">
											<input type="hidden" name="rel_id" value="">
											<input type="hidden" name="parent_id" value="">
											<input type="hidden" name="tasktype" value="2">
											<input type="hidden" name="billable" value="on">
											<input type="hidden" name="task_mark_complete_id" value="">
											<input type="hidden" name="tags" value="">
											<input type="hidden" id="rfilecnt" value="1">
											<input type="hidden" id="rtotcnt" value="1">
											<input type="hidden" id="rallcnt" value="0">
											<input type="hidden" id="r_file" name="m_file">
											<div class="ch_files_r">
											</div>
											<div id="r_files"></div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
											<button type="submit" class="btn btn-info pull-right"><i class="fa fa-envelope"></i> Send Message</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<!-- END reply MESSAGE -->
					
					
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
<style>
.th_head_color{
	color:darkgrey !important;
}
.unread_col_col{
	background:gainsboro;
}
.headerSortDown:after,
.headerSortUp:after {
  content: ' ';
  position: relative;
  left: 2px;
  border: 5px solid transparent;
}

.headerSortDown:after {
  top: 10px;
  border-top-color: silver;
}

.headerSortUp:after {
  bottom: 10px;
  border-bottom-color: silver;
}

.headerSortDown,
.headerSortUp {
  padding-right: 10px;
}
</style>
<script>
function over_lay(cur_id){
	document.getElementById('overlay_'+cur_id).style.display = ''; 
	 $(".btn").prop('disabled', true);
}
function mget_file(c_id,c){
	 var fcnt = $('#'+c+'filecnt').val();
	 var tcnt = $('#'+c+'totcnt').val();
	 if(tcnt<=1){
		document.getElementById(c_id).click();
	 }else{
		 document.getElementById(c_id+'_'+tcnt).click();
	 }
 }
 function rm_file(a,b){
	 var c = $('#'+a+'_file').val() + b + ',';
	 $('#'+a+'_'+b+'_del').hide();
	 $('#'+a+'_file').val(c);
 }
 function get_up_val(c_id,c){
	 var fcnt = $('#'+c+'filecnt').val();
	 var tcnt = $('#'+c+'totcnt').val();
	 var allcnt = $('#'+c+'allcnt').val();
	 var req_tcnt = parseInt(tcnt) + parseInt(1);
	 var req_fcnt = parseInt(fcnt) + parseInt(1);
	 var req_cid = "'"+c_id+"'";
	 var req_c = "'"+c+"'";
 	 var req_id = "'"+c+"div_"+req_tcnt;
	  var file = $('#'+c_id);
	 if(tcnt!=1){
		 var file = $('#'+c_id+'_'+tcnt);
	 }
	 var fileName ='';
	 allcnt1 = parseInt(allcnt) + parseInt(file[0].files.length);
	 
	for(var i=0;i<file[0].files.length;i++){
		if(allcnt1 == file[0].files.length){
			var j = i;
		}
		else{
			var j = parseInt(allcnt) + i ;
		}
		var chr = "'"+c+"'";
		var c_no = "'"+j+"'";
		 fileName = fileName+'<div id="'+c+'_'+j+'_del" class="col-md-12" style="float:left;margin-top:5px;margin-top:5px;font-weight: 900;font-size: 15px;"><div class="col-md-9">'+ file[0].files[i].name + '</div><div class="col-md-3"><a href="javascript:void(0)"  onclick="rm_file('+chr+','+c_no+')" title="Delete"><i class="fa fa-trash fa-2x" id="" style="color:red"></i></a></div></div>';
	}
	$('.ch_files_'+c).append(fileName);
	 var req_file = '<div id="'+req_id+'"><input type="file" id="'+c_id+'_'+req_tcnt+'" style="display:none" name="attachment[]" multiple onchange="get_up_val('+req_cid+','+req_c+')"></div><br><br>';

	 $('#'+c+'_files').append(req_file);
	 $('#'+c+'filecnt').val(req_fcnt);
	 $('#'+c+'totcnt').val(req_tcnt);
	 $('#'+c+'allcnt').val(allcnt1);
	 
 }
function ch_sort(ch_val,b){
	var sort_val = $("#sort_val").val();
	var sort_option = $("#sort_option").val();
	$("#sort_option").val(ch_val);
	$("#sort_val").val(1);
	if(sort_option == ch_val){		
		if(sort_val ==1){
			$("#sort_val").val(0);
		}
	}
	$( ".th_class" ).removeClass( "headerSortDown" );
	 $( ".th_class" ).removeClass( "headerSortUp" );
	 $( ".th_class" ).addClass( "headerSortDown" );
	 $( ".a_header" ).removeClass( "th_head_color" );
	 var ch_sort_val = $("#sort_val").val();
	 $( "#"+b ).removeClass( "headerSortDown" );
	 $( "."+ch_val+"_a" ).addClass( "th_head_color" );
	 if(ch_sort_val ==1){
		 $('#'+b).addClass('headerSortDown');
	 }
	 else{
		 $('#'+b).addClass('headerSortUp');
	 }
	 //var pageno = $('#req_page').val();
	 loadPagination(1);  
	 
}
	 $(document).ready(function(){  
   
		$('#pagination').on('click','a',function(e){  
       e.preventDefault();   
       var pageno = $(this).attr('data-ci-pagination-page');  
	   $('#req_page').val(pageno);
       loadPagination(pageno);  
     });  
	 });
	 loadPagination(1);
	function loadPagination(pagno){  
		if(pagno!=0){
			var uid = $("#uid").val();
			var folder = $("#folder").val();
		}
		else{
			var uid = '';
			var folder = 'INBOX';
		}
		var sort_val = $("#sort_val").val();
		var sort_option = $("#sort_option").val();
		var search_txt = $('#search_mail').val();
	   document.getElementById('overlay').style.display = '';
	   $.post(admin_url + 'company_mail/pagination_mail/'+pagno,
		{
			'folder': folder,
			'uid':uid,
			'sort_val':sort_val,
			'sort_option':sort_option,
			'search_txt':search_txt
		},
		function(data,status){
			document.getElementById('overlay').style.display = 'none'; 
			$("body").css('overflow', 'auto');
			var json = $.parseJSON(data);
			if(status == 'success') {
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
	 function getMailList(val){
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
	frm4.submit(function (e) {
		
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
//});
function gettemplate_list(){
	$.post(admin_url + 'company_mail/template_list',
	{
	},
	function(data){
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
function add_content(uid){
	document.getElementById('overlay_new').style.display = '';
	$.post(admin_url + 'company_mail/content',
	{
		uid:uid
	},
	function(data,status){
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
		check_email('','forward_toemail');
		$('#f_getFile').val('');
		$('#forward_subject').val('Fwd: '+json.subject);
		tinyMCE.get('forward_description').setContent(json.message);
		document.getElementById('overlay_new').style.display = 'none'; 
		
	});
}
function add_to(uid){
	document.getElementById('overlay_new1').style.display = '';
	$.post(admin_url + 'company_mail/to_mail',
	{
		uid:uid
	},
	function(data,status){
		console.log('data');
		var json = $.parseJSON(data);
		$('#reply_toemail').val(json.from_address);
		$('#ch_uid').val(uid);
		$('#reply_subject').val('Re: '+json.subject); 
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
function add_reply_all(uid){
	document.getElementById('overlay_new1').style.display = '';
	$.post(admin_url + 'company_mail/add_reply_all',
	{
		uid:uid
	},
	function(data,status){
		var json = $.parseJSON(data);
		$('#reply_toemail').val(json.from_address);
		$('#ch_uid').val(uid);
		$('#reply_subject').val('Re: '+json.subject); 
		
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

function getMessage(val){
	var folder = $("#folder").val();
	document.getElementById('overlay').style.display = '';
	$.post(admin_url + 'company_mail/getmessage',
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
function submit_default(){
	//$('#default_submit').prop('disabled', true);
	var default_temp = $("#ch_default_temp").val();
	$.post(admin_url + 'company_mail/change_default',
	{
		default_template: default_temp		
	},
	function(data,status){
		var json = $.parseJSON(data);
		if(json.status == 'success'){
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
			var req_text = text+json.description
			tinyMCE.get('description').setContent(req_text);
			//tinyMCE.activeEditor.execCommand('mceInsertContent',false,json.description);
			//$('#default_submit').prop('disabled', false);
			//gettemplate_list();
			
		} 
	});
	if(default_temp == ''){
		var text = tinyMCE.get('description').getContent();
		tinyMCE.get('description').setContent(text);
	}
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
function reset_form(){
	$('#template_name').val('');
	tinyMCE.get('template_description').setContent('');
}
</script>

  
<?php hooks()->do_action('settings_tab_footer', 'email'); ?>
<script>
  var BASE_URL = "<?php echo base_url(); ?>";
 
 
 
</script> 
<script>
window.onscroll = function() {myFunction()};

var header = document.getElementById("myHeader");
var sticky = header.offsetTop;

function myFunction() {
  if (window.pageYOffset > sticky) {
    header.classList.add("sticky");
  } else {
    header.classList.remove("sticky");
  }
}
function hide_div(a){
	//$('#'+a).modal('hide');
}
function check_header(){
	
	 $('#myHeader').hide();
	 $("input:checkbox[class=check_mail]:checked").each(function () {
		$('#myHeader').show();
		var folder = $("#folder").val();
		$('#delete_ever').hide();
		$('#cur_delete').show();
		if(folder == '[Gmail]/Trash'){
			$('#delete_ever').show();
			$('#cur_delete').hide();
		}
	});
	var a = $("input[type='checkbox'][class=check_mail]");
    if(a.filter(":checked").length!= a.length){
		$("#select_all").prop('checked', false);
	}
	else{
		$("#select_all").prop('checked', true);
	}
}
function check_all(a){
	$(".check_mail").prop('checked', false);
	if(a.checked == true){
		$(".check_mail").prop('checked', true);
	}
	check_header();
}

$('#del_mail1').click(function() {
	var form= $("#formId");
	var folder = $("#folder").val();
	document.getElementById('overlay').style.display = '';
	var BASE_URL = "<?php echo base_url();?>";
    $.ajax({
        url: BASE_URL+'admin/company_mail/delete_mail_all?folder='+folder,
        type: 'POST',
        data: form.serialize(),
        success: function(data) {
			var results = JSON.parse(data);
			if(results.length>0){
				for(var i =0;i<results.length;i++){
					$('.'+results[0]+'_mail_row').hide();;
				}
			}
			var pageno = $('#req_page').val();
			loadPagination(pageno);
			$('#myHeader').hide();
			alert_float('success', 'Selected Mail Deleted Successfully');
			//document.getElementById('overlay').style.display = 'none'; 
        }               
    });
});
$('#del_mail').click(function() {
	var form= $("#formId");
	var folder = $("#folder").val();
	document.getElementById('overlay').style.display = '';
	var BASE_URL = "<?php echo base_url();?>";
    $.ajax({
        url: BASE_URL+'admin/company_mail/trash?folder='+folder,
        type: 'POST',
        data: form.serialize(),
        success: function(data) {
			var results = JSON.parse(data);
			if(results.length>0){
				for(var i =0;i<results.length;i++){
					$('.'+results[0]+'_mail_row').hide();;
				}
			}
			var pageno = $('#req_page').val();
			loadPagination(pageno);
			$('#myHeader').hide();
			alert_float('success', 'Selected Mail Move To Trash Successfully');
			//document.getElementById('overlay').style.display = 'none'; 
        }               
    });
});
$('#unread_mail').click(function(){
	document.getElementById('overlay').style.display = '';
	var form= $("#formId");
	var BASE_URL = "<?php echo base_url();?>";
    $.ajax({
        url: BASE_URL+'admin/company_mail/unread',
        type: 'POST',
        data: form.serialize(),
        success: function(data) {
			var pag_no = $('#req_page').val();
			loadPagination(pag_no);
			$('#myHeader').hide();
			//window.location.href="";

        }               
    });
});
$('#read_mail').click(function(){
	document.getElementById('overlay').style.display = '';
	var form= $("#formId");
	var BASE_URL = "<?php echo base_url();?>";
    $.ajax({
        url: BASE_URL+'admin/company_mail/read_msg',
        type: 'POST',
        data: form.serialize(),
        success: function(data) {
			var pag_no = $('#req_page').val();
			loadPagination(pag_no);
			$('#myHeader').hide();
			//window.location.href="";

        }               
    });
});
function edit_template(a){
	var BASE_URL = "<?php echo base_url();?>";
	$.ajax({
		url: BASE_URL+'admin/company_mail/edit_template',
		type: 'POST',
		data: { 'template_id': a },
		success: function(data) {
			var json = $.parseJSON(data);
			//var text = tinyMCE.get('template_edit_description').getContent();
	
			$('#template_edit_name').val(json.template_name);
			$('#template_id1').val(json.id);
			$('#template_edit_description').val(json.description);
			tinyMCE.get('template_edit_description').setContent(json.description);
			
		}

		}               
	);
}
function deal_values(){
	$('#pipeselect').hide();
	$("#pipeline_id").append('');
	$('#pipeline_id').empty();
	$("#pipeline_id").selectpicker("refresh");
	var deal_map = $('#deal_map').val();
	var toemail = $('#toemail').val();
	var BASE_URL = "<?php echo base_url();?>";
	$.ajax({
		url: BASE_URL+'admin/company_mail/deal_values',
		type: 'POST',
		data: { 'toemail': toemail },
		success: function(data) {
			$('#pipeselect').hide();
			$("#pipeline_id").append(data);
			$("#pipeline_id").selectpicker("refresh");
			var deal_val = $('#pipeline_id').val();
			//$('#pipeselect').hide();
			
			if(data!=''){
				$('#pipeselect').show();
			}
		}

		}               
	);
}
function del_template(a){
	if (confirm('Are you want to delete this template')) {
		var BASE_URL = "<?php echo base_url();?>";
		$.ajax({
			url: BASE_URL+'admin/company_mail/delete_template',
			type: 'POST',
			data: { 'template_id': a },
			success: function(data) {
				$('.list_1'+a).hide();
				gettemplate_list();
				}

			}               
		);
	}
}

</script>
<script type="text/javascript">
    var frm1 = $('#template_form');

    frm1.submit(function (e) {

        e.preventDefault();
		//$('form').preventDoubleSubmission();
		$('.error1').hide();
        $.ajax({
            type: frm1.attr('method'),
            url: frm1.attr('action'),
            data: frm1.serialize(),
            success: function (data) {
				var json = $.parseJSON(data);
				if(json.status == 'success'){
					gettemplate_list();
					$(".tabs").removeClass("active");
					$(".tabs h6").removeClass("font-weight-bold");
					$(".tabs h6").addClass("text-muted");
					$("#tab02").children("h6").removeClass("text-muted");
					$("#tab02").children("h6").addClass("font-weight-bold");
					$("#tab02").addClass("active");

					current_fs = $(".active");

					next_fs = "#tab021";

					$("fieldset").removeClass("show");
					$(next_fs).addClass("show");
					  alert_float('success', 'Template Created Successfully');
					//alert('Template Created Successfully');
				}
				else{
					if(json.name_error == 1){
						$('#name_error').show();
					}
					if(json.description_error == 1){
						$('#desc_error').show();
					}
				}
            },
            error: function (data) {
            },
        });
    });
	var frm2 = $('#edit_template_form');

    frm2.submit(function (e) {

        e.preventDefault();
		//$('form').preventDoubleSubmission();
		$('.error1').hide();
        $.ajax({
            type: frm2.attr('method'),
            url: frm2.attr('action'),
            data: frm2.serialize(),
            success: function (data) {
				var json = $.parseJSON(data);
				if(json.status == 'success'){
					$('#Edit-template').modal('hide');
					gettemplate_list();
					alert_float('success', 'Template Updated Successfully');
					//a//lert('Template Updated Successfully');
				}
				else{
					if(json.name_error == 1){
						$('#name_edit_error').show();
					}
					if(json.description_error == 1){
						$('#desc_edit_error').show();
					}
				}
            },
            error: function (data) {
            },
        });
    });
	$(document).on('show.bs.modal', '.modal', function () {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
	

    
});

function tab_opon_popup(){
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
	$('.list_files').html('');
	$('#m_files').html('');
	$('#toemail').val('');
	$('#toemail').val('');
	$('#toccemail').val('');
	$('#tobccemail').val('');
	$('#c_subject').val('');
	$('#mtotcnt').val(1);
	$('#mfilecnt').val(1);
	$('#mallcnt').val(0);
	$('#m_file').val('');
	check_email('','toemail');
	//tinyMCE.get('description').setContent('');
	$('#pipeselect').hide();
	$('#getFile').val('');
	$("#pipeline_id").append('');
	$('#pipeline_id').empty();
	$("#pipeline_id").selectpicker("refresh");
}
function template_description(){
	var text = tinyMCE.get('template_description').getContent();
	$('#template_description').val(text.trim());
}
function template_edit_description(){
	var text = tinyMCE.get('template_edit_description').getContent();
	$('#template_edit_description').val(text.trim());
}
function save_draft(){
	var to = $('#toemail').val();
	var c_subject = $('#c_subject').val();
	var draft = $('#cur_draft_id').val();
	var text = tinyMCE.get('description').getContent();
	if((to!='' & text!='') || (to!='' & c_subject!='')){
		$.ajax({
			url: BASE_URL+'admin/company_mail/save_draft',
			type: 'POST',
			data: { 'to': to,'subject':c_subject,'text':text,'draft':draft },
			success: function(data) {
					$('#cur_draft_id').val(data);
				}

			}               
		);
	}
}
tinymce.init({
        selector: 'textarea#template_edit_description',
        height: 100,
		width:570,
        menubar: true,
        plugins: [
          'advlist autolink lists charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table paste code help wordcount','image code','link',
		  'emoticons template paste textcolor colorpicker textpattern imagetools','autosave'
        ],
        toolbar: 'fontselect fontsizeselect | forecolor backcolor | bold italic sizeselect | hr alignleft aligncenter alignright alignjustify | link image | link  | bullist numlist | restoredraft | code',
    fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
		 setup: function(ed) {  
                ed.on('keyup', function(e) {  
                    template_edit_description()  
                });  
            }  
      });
tinymce.init({
        selector: 'textarea#description',
        height: 100,
		width:660,
        menubar: true,
        plugins: [
          'advlist autolink lists charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table paste code help wordcount','image code','link',
		  'emoticons template paste textcolor colorpicker textpattern imagetools','autosave'
        ],
        toolbar: 'fontselect fontsizeselect | forecolor backcolor | bold italic sizeselect | hr alignleft aligncenter alignright alignjustify | link image | link  | bullist numlist | restoredraft | code',
    fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
		 setup: function(ed) {  
                ed.on('blur', function(e) {  
                    save_draft()  
                });  
            }  
      });
	  tinymce.init({
        selector: 'textarea#template_description',
        height: 100,
		width:670,
        menubar: true,
        plugins: [
          'advlist autolink lists charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table paste code help wordcount','image code','link',
		  'emoticons template paste textcolor colorpicker textpattern imagetools','autosave'
        ],
        toolbar: 'fontselect fontsizeselect | forecolor backcolor | bold italic sizeselect | hr alignleft aligncenter alignright alignjustify | link image | link  | bullist numlist | restoredraft | code',
    fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
		 setup: function(ed) {  
                ed.on('keyup', function(e) {  
                    template_description()  
                });  
            }  
      });
	  
$(document).ready(function(){
	
	$(".tabs").click(function(){

		$(".tabs").removeClass("active");
		$(".tabs h6").removeClass("font-weight-bold");
		$(".tabs h6").addClass("text-muted");
		$(this).children("h6").removeClass("text-muted");
		$(this).children("h6").addClass("font-weight-bold");
		$(this).addClass("active");

		current_fs = $(".active");

		next_fs = $(this).attr('id');
		next_fs = "#" + next_fs + "1";

		$("fieldset").removeClass("show");
		$(next_fs).addClass("show");

		current_fs.animate({}, {
			step: function() {
				current_fs.css({
				'display': 'none',
				'position': 'relative'
				});
				next_fs.css({
				'display': 'block'
				});
			}
		});
	});

});
</script>
<script type='text/javascript' >
  function check_email(a,c_id){

	  var req_val = $('#'+c_id).val();
	  var newStr = req_val.substring(0, req_val.length - 1);
	  var check_str = newStr.substring(newStr.length-4);
	  var cur_val = a.substr(a.length - 1);
	  var e = event.keyCode;
	  if((check_str.includes(".com") || check_str.includes(".net") || check_str.includes(".in")) && (e!=8) && e!=188){
		  var req_out = newStr+','+ cur_val;
		   $('#'+c_id).val(req_out);
	  }
  }
  
$(document).ready(function() {
	
    $( "#toemail" ).autocomplete({
 
        source: function(request, response) {
            var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL+'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
    },
	select: function( event, ui ) {
		var terms = split( $('#toemail').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		terms.push( "" );
		$('#toemail').val(terms);
		//$('#toemail').val(terms.join( ", " ));

		// Id
		var terms = split( $('#selectuser_ids').val() );
		
		terms.pop();
		var req_out = $('#toemail').val();
		req_out = ','+req_out;
		
		//terms.push( req_out );
		terms.push( ui.item.value );
		var deal_map = $('#deal_map').val();
		if(deal_map == 'if more than one open deal – allow to map manually'){
			deal_values();
		}
		terms.push( "" );
		var trim = req_out.replace(/(^,)|(,$)/g, "");
		//$('#selectuser_ids').val(terms.join( ", " ));
		$('#toemail').val(trim);
		$('#selectuser_ids').val(trim);

		return false;
	},
    minLength: 3
 });
 
 $( "#toccemail" ).autocomplete({
 
        source: function(request, response) {
            var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL+'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
    },
	select: function( event, ui ) {
		var terms = split( $('#toccemail').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		$('#toccemail').val(terms);
		//$('#toccemail').val(terms.join( ", " ));
		var req_out = $('#toccemail').val();
		req_out = ','+req_out;

		// Id
		var terms = split( $('#selectuser_ids').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		var trim = req_out.replace(/(^,)|(,$)/g, "");
		//$('#selectuser_ids').val(terms.join( ", " ));
		$('#toccemail').val(trim);
		$('#selectuser_ids').val(trim);

		return false;
	},
    minLength: 3
 });
 $( "#tobccemail" ).autocomplete({
 
        source: function(request, response) {
            var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL+'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
    },
	select: function( event, ui ) {
		var terms = split( $('#tobccemail').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		$('#tobccemail').val(terms);
		//$('#tobccemail').val(terms.join( ", " ));
		
		var req_out = $('#tobccemail').val();
		req_out = ','+req_out;

		// Id
		var terms = split( $('#selectuser_ids').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		var trim = req_out.replace(/(^,)|(,$)/g, "");
		//$('#selectuser_ids').val(terms.join( ", " ));
		$('#tobccemail').val(trim);
		$('#selectuser_ids').val(trim);

		return false;
	},
    minLength: 3
 });
 $( "#forward_toemail" ).autocomplete({
 
        source: function(request, response) {
            var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL+'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
    },
	select: function( event, ui ) {
		var terms = split( $('#forward_toemail').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		$('#forward_toemail').val(terms);
		//$('#forward_toemail').val(terms.join( ", " ));
		var req_out = $('#forward_toemail').val();
		req_out = ','+req_out;
		// Id
		var terms = split( $('#selectuser_ids').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		//$('#selectuser_ids').val(terms.join( ", " ));
		var trim = req_out.replace(/(^,)|(,$)/g, "");
		//$('#selectuser_ids').val(terms.join( ", " ));
		$('#forward_toemail').val(trim);
		$('#selectuser_ids').val(trim);

		return false;
	},
    minLength: 3
 });
 $( "#forward_ccemail" ).autocomplete({
 
        source: function(request, response) {
            var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL+'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
    },
	select: function( event, ui ) {
		var terms = split( $('#forward_ccemail').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		$('#forward_ccemail').val(terms);
		//$('#forward_ccemail').val(terms.join( ", " ));
		var req_out = $('#forward_ccemail').val();
		req_out = ','+req_out;

		// Id
		var terms = split( $('#selectuser_ids').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		//$('#selectuser_ids').val(terms.join( ", " ));
		var trim = req_out.replace(/(^,)|(,$)/g, "");
		//$('#selectuser_ids').val(terms.join( ", " ));
		$('#forward_ccemail').val(trim);
		$('#selectuser_ids').val(trim);

		return false;
	},
    minLength: 3
 });
 $( "#forward_bccemail" ).autocomplete({
 
        source: function(request, response) {
            var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL+'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
    },
	select: function( event, ui ) {
		var terms = split( $('#forward_bccemail').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		$('#forward_bccemail').val(terms);
		//$('#forward_bccemail').val(terms.join( ", " ));
		var req_out = $('#forward_bccemail').val();
		req_out = ','+req_out;

		// Id
		var terms = split( $('#selectuser_ids').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		//$('#selectuser_ids').val(terms.join( ", " ));
		var trim = req_out.replace(/(^,)|(,$)/g, "");
		//$('#selectuser_ids').val(terms.join( ", " ));
		$('#forward_bccemail').val(trim);
		$('#selectuser_ids').val(trim);

		return false;
	},
    minLength: 3
 });
 $( "#reply_ccemail" ).autocomplete({
 
        source: function(request, response) {
            var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL+'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
    },
	select: function( event, ui ) {
		var terms = split( $('#reply_ccemail').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		$('#reply_ccemail').val(terms);
		//$('#reply_ccemail').val(terms.join( ", " ));
		var req_out = $('#reply_ccemail').val();
		req_out = ','+req_out;

		// Id
		var terms = split( $('#selectuser_ids').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		//$('#selectuser_ids').val(terms.join( ", " ));
		var trim = req_out.replace(/(^,)|(,$)/g, "");
		//$('#selectuser_ids').val(terms.join( ", " ));
		$('#reply_ccemail').val(trim);
		$('#selectuser_ids').val(trim);

		return false;
	},
    minLength: 3
 });
 $( "#reply_bccemail" ).autocomplete({
 
        source: function(request, response) {
            var searchText = extractLast(request.term);
                $.ajax({
                    url: BASE_URL+'admin/company_mail/autocomplete',
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: searchText
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
    },
	select: function( event, ui ) {
		var terms = split( $('#reply_bccemail').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		$('#reply_bccemail').val(terms);
		//$('#reply_bccemail').val(terms.join( ", " ));
		var req_out = $('#reply_bccemail').val();
		req_out = ','+req_out;

		// Id
		var terms = split( $('#selectuser_ids').val() );
		
		terms.pop();
		
		terms.push( ui.item.value );
		
		terms.push( "" );
		//$('#selectuser_ids').val(terms.join( ", " ));
		var trim = req_out.replace(/(^,)|(,$)/g, "");
		//$('#selectuser_ids').val(terms.join( ", " ));
		$('#reply_bccemail').val(trim);
		$('#selectuser_ids').val(trim);

		return false;
	},
    minLength: 3
 });
});
    function split( val ) {
      return val.split( /,\s*/ );
    }
    function extractLast( term ) {
      return split( term ).pop();
    }

    </script>
<style>
.ui-autocomplete {
    position: absolute;
    top: 0;
    left: 0;
    cursor: default;
	z-index:1050 !important;
}
.error{
	color:red;
}
.error1{
	color:red;
	display:none;
}
.sticky {
  position: fixed;
  top: 0;
  width: 100%;
  padding: 10px 0px;
  height:49px;
  margin-left:-10px !important;
}
.header {
  z-index:999;
  background:#fff;
  margin-left:10px;
}


fieldset {
    display: none
}

fieldset.show {
    display: block
}

a:hover {
    text-decoration: none;
    color: #1565C0
}

.box {
    margin-bottom: 10px;
    border-radius: 5px;
    padding: 10px
}

.line {
    background-color: #CFD8DC;
    height: 1px;
    width: 100%
}

@media screen and (max-width: 768px) {
    .tabs h6 {
        font-size: 12px
    }
}

#emailViewerMeta p{
	font-size: 13px !important;
}

</style>
</body>
</html>
