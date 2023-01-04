<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php

$ch_contact =false;
$contact =$this->leads_model->get_lead_contact($lead->id);
if($contact){
    $this->db->where('id',$contact->contacts_id);
    $ch_contact =$this->db->get(db_prefix().'contacts')->row();
}
$emails =$this->leads_model->get_emails($lead->id);
?>
<script>
	function reset_form(){
		$('#template_name').val('');
		tinyMCE.get('template_description').setContent('');
	}
	function tab_opon_popup(){
		$(".tabs").removeClass("active");
		$(".tabs h6").removeClass("font-weight-bold");
		$(".tabs h6").addClass("text-muted");
		$('[data-target="#tab011"]').children("h6").removeClass("text-muted");
		$('[data-target="#tab011"]').children("h6").addClass("font-weight-bold");
		$('[data-target="#tab011"]').addClass("active");

		current_fs = $(".active");

		next_fs = "#tab011";
		$("fieldset").removeClass("show");
		$(next_fs).addClass("show");
		//$('#cur_draft_id').val('');
		$('.list_files').html('');
		$('#m_files').html('');
		//$('#toemail').val('');
		$('#toccemail').val('');
		$('#tobccemail').val('');
		$('#c_subject').val('');
		$('#mtotcnt').val(1);
		$('#mfilecnt').val(1);
		$('#mallcnt').val(0);
		$('#m_file').val('');
		check_email('','toemail');
		//tinyMCE.get('description').setContent('');
		$('#getFile').val('');
	}
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
	function edit_template(a){
		var BASE_URL = "<?php echo base_url();?>";
		$.ajax({
			url: BASE_URL+'admin/company_mail/edit_template',
			type: 'POST',
			data: { 'template_id': a },
			success: function(data) {
				var json = $.parseJSON(data);
				$('#template_edit_name').val(json.template_name);
				$('#template_id1').val(json.id);
				$('#template_edit_description').val(json.description);
				tinyMCE.get('template_edit_description').setContent(json.description);
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
	function mget_file(c_id,c){
		var fcnt = $('#'+c+'filecnt').val();
		var tcnt = $('#'+c+'totcnt').val();
		if(tcnt<=1){
			document.getElementById(c_id).click();
		}else{
			document.getElementById(c_id+'_'+tcnt).click();
		}
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
	function rm_file(a,b){
		var c = $('#'+a+'_file').val() + b + ',';
		$('#'+a+'_'+b+'_del').hide();
		$('#'+a+'_file').val(c);
	}
	function over_lay(cur_id){
		document.getElementById('overlay_'+cur_id).style.display = ''; 
		$(".btn").prop('disabled', true);
	}
</script>
<div class="clearfix"></div> 

<!-- BEGIN INBOX CONTENT -->
<div class="col-md-12">
<div id="overlay" style="display: none;"><div class="spinner"></div></div>
	<div class="">
			<?php if(empty($url1)){?>
				<a class="btn btn-primary composebtn" data-toggle="modal" data-target="#compose-modal" onclick="tab_opon_popup()"><i class="fa fa-pencil" ></i>&nbsp;&nbsp;<?php echo _l('compose_email');?></a>
			<?php }else{?>
				<a class="btn btn-primary composebtn" href="<?php echo $url1;?>"><i class="fa fa-pencil" ></i>&nbsp;&nbsp;<?php echo _l('compose_email');?></a>
			<?php }?>

			<a class="btn btn-primary pull-right composebtn" href="javascript:void(0)" onclick="sync_mail()" title="<?php echo _l('sync_mail_help_text');?>"><i class="fa fa-pencil" ></i>&nbsp;&nbsp;<?php echo _l('sync_mail');?></a>

			<div  class="header" id="myHeader" style="display:none;">
				<div class="col-md-12" style="background: #fff;">
					<div class="col-md-2" style="width:auto">
						<a href="javascript:void(0);" id="del_mail"><i class="fa fa-trash fa-2x"  style="color:red"></i></a>
					</div>
				</div>
			</div>
	</div>

	<div class="col-md-12 email">
		<div class="table-responsive">
			<form id="formId" >
				<table class="table dataTable" >
					<thead>
						<tr>
						  <th>
							<?php if($email_count>0){?>
								<input type="checkbox" id="select_all" onclick="check_all(this)">
							<?php }?>
						  </th>
						  <th>From</th>
						  <th>To</th>
						  <th>Subject</th>
						  <th>Attachement</th>
						  <th>Date</th>
						</tr>
					</thead>
					<tbody>
						<?php if(!empty($emails)){foreach($emails as $email1){?>
						  <tr clss="<?php echo $email1['uiid'];?>_mail_row">
							<td>
								<?php if($req_staff_id == $email1['staff_id']){?>
									<input type="checkbox" name="mails[]" onclick="check_header()" value="<?PHP echo $email1['id'];?>" class="check_mail">
								<?php }?>
							</td>
							<td data-order="<?php echo $email1['from']; ?>">
								<a href="javascript:void(0)" onClick="getMessage('<?php echo $email1['id'];?>');"><?php echo $email1['from_email'];?></a>
							  </td>
							  <?php $to_mails = json_decode($email1['mail_to'],true);?>
							  <td data-order="<?php echo $email1['to']; ?>"><a href="javascript:void(0)" onClick="getMessage('<?php echo $email1['id'];?>');"><?php echo $to_mails[0]['email']; ?></a></td>
							  <td data-order="<?php echo $email1['subject']; ?>">
								<a href="javascript:void(0)" onClick="getMessage('<?php echo $email1['id'];?>');"><?php echo $email1['subject'];?></a>
							  </td>
							  <td>
								<?php if(!empty($email1['attachements']) && $email1['attachements'] != '[]'){
									$msg_id = $email1['message_id'];
									if(!empty($email1['mail_by']) && $email1['mail_by']=='outlook'){
										$downoad_url = admin_url('outlook_mail/outlook_all_download_attachment?msg_id='.$msg_id);
										?>
										<a href="<?php echo $downoad_url;?>" ><i class="fa fa-paperclip" aria-hidden="true"></i></a>
										<?php
									}else{
									if($inboxEmails['uid']!=0){
										if($email1['folder']=='INBOX'){
									?>
									<a href="<?php echo admin_url('company_mail/download_attachment/'.$email1['uid']).'?folder=INBOX';?>" onclick="download_attachment('.$inboxEmails['uid'].')"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
										<?php }else{?>
											<a href="<?php echo admin_url('company_mail/download_attachment/'.$email1['uid']).'?folder=[Gmail]/Sent Mail';?>" onclick="download_attachment('.$inboxEmails['uid'].')"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
										<?php }?>
								<?php }else{
									?>
										<a href="<?php echo admin_url('projects/download_attachment/'.urlencode($email1['attachements']));?>" ><i class="fa fa-paperclip" aria-hidden="true"></i></a>
									<?php 
								}
								}
								} ?>
							  </td>
							  <td data-order="<?php echo $email1['date']; ?>">
								<a href="javascript:void(0)" onClick="getMessage('<?php echo $email1['id'];?>');"><?php echo date('D, d M Y h:i A',strtotime($email1['date'])); ?></a>
							  </td>
						   </tr>
						<?php }}else{ ?>
						<tr>
							<td colspan="7" class="text-center">No Record's Found</td>
						</tr>
						<?php }?>
					</tbody>
				</table>
			</form>
			<?php echo $pagination;?>
		</div>
	</div>
</div>

<!-- BEGIN COMPOSE MESSAGE -->
<div class="modal fade" id="compose-modal" tabindex="-1" role="dialog" aria-hidden="true" style="margin-bottom:25px;">
	<div class="modal-wrapper">
		<div class="modal-dialog" style="width:74.5% ">
		
			<div class="modal-content">
				<div class="modal-header bg-blue">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><i class="fa fa-envelope"></i> Compose New Message</h4>
				</div>
				<div class="col-md-12 bg-white"  style="border-radius:6px;">
				
					<div class="col-md-3">
						<div class="tabs active" data-target="#tab011">
							<h6 class="text-muted">Compose Email</h6>
						</div>
						<div class="tabs " data-target="#tab021" onclick="gettemplate_list()">
							<h6 class="text-muted">Templates</h6>
						</div>
						<div class="tabs " data-target="#tab031" onclick="reset_form()">
							<h6 class="font-weight-bold text-muted">Create Template</h6>
						</div>
					</div>
					<div class="col-md-9 tabs-pane">
						<fieldset id="tab011" class="show">
						<?PHP if(get_option('connect_mail')=='no'){?>
							<form action="<?php echo admin_url('leads/send_outlook/'.$lead->id); ?>" method="post" enctype='multipart/form-data' onsubmit="over_lay('compose')">
						<?php }else{?>
							<form action="<?php echo admin_url('leads/createtaskcompanymail/'.$lead->id); ?>" method="post" enctype='multipart/form-data' onsubmit="over_lay('compose')">
						<?php }?>
								<input type="hidden" id="cur_draft_id" name="cur_draft_id">
								<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
								<div class="modal-body">
									<div class="form-group">
										<input name="toemail" type="email" class="form-control" placeholder="To" id="toemail" readonly value="<?php echo $ch_contact->email;?>" >
										<input name="contactid" type="hidden"value="<?php echo $ch_contact->id;?>" >
										<input type='hidden' id='selectuser_ids' />
										<?php if(empty($ch_contact)){?>
											<p class="error" style="display:block;margin-top:10px">Please add primary contact person.</p>
										<?php }
										else if(empty($ch_contact->email)){?>
											<p class="error" style="display:block;margin-top:10px">Please add email for primary contact person.</p>
										<?php }?>
									</div>
									<div class="form-group">
										<input name="ccemail" type="email" class="form-control" placeholder="Cc" id="toccemail" onkeyup="check_email(this.value,'toccemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$" >
									</div>
									<div class="form-group">
										<input name="bccemail" type="email" class="form-control" placeholder="Bcc" id="tobccemail"  onkeyup="check_email(this.value,'tobccemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$" >
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
									<input type="hidden" name="email_leadid" value="<?php echo $lead->id ?>">
									<input type="hidden" name="activity_type" value="close">
									<?php /*if(get_option('deal_map') == 'if more than one open deal – allow to map manually'){?>
										<div class="form-group pipeselect"  id="pipeselect">
											<select class="selectpicker" data-none-selected-text="<?php echo _l('all'); ?>" name="deal_id" id="pipeline_id" data-width="100%" data-live-search="true" required>
												<?php if(!empty($all_dels)){
													foreach($all_dels as $all_del12){
												?>
														<option value="<?php echo $all_del12['id'];?>"><?php echo $all_del12['name'];?></option>
												<?php 
													}
												}?>
											</select>
										</div>
									<?php }*/?>
									<div class="form-group">
										<input name="name" type="text" class="form-control" placeholder="Subject" id="c_subject" required>
									</div>
									<div class="form-group" app-field-wrapper="description" ><textarea id="description" name="description" class="form-control tinymce1" rows="6"  placeholder="Message"><?php //echo $default_val;?></textarea></div> 
									<!-- <div class="form-group">														<input type="file" name="attachment">
									</div> -->
									<input type="hidden" name="priority" value="1">
									<input type="hidden" id="mfilecnt" value="1">
									<input type="hidden" id="mtotcnt" value="1">
									<input type="hidden" id="mallcnt" value="0">
									<input type="hidden" id="m_file" name="m_file">
									<input type="hidden" name="repeat_every_custom" value="1">
									<input type="hidden" name="repeat_type_custom" value="day">
									<input type="hidden" name="rel_type" value="lead">
									<input type="hidden" name="tasktype" value="2">
									<input type="hidden" name="billable" value="on">
									<input type="hidden" name="task_mark_complete_id" value="">
									<input type="hidden" name="tags" value="">
									<button type ="button" class="btn btn-primary" style="display:block;" onclick="mget_file('getFile','m')">Add Attachement </button>
									<input type='file' id="getFile" style="display:none" multiple name="attachment[]" onchange="get_up_val('getFile','m')"> 
									
									<div class="ch_files_m list_files">
									</div>
									<div id="m_files"></div>
								</div>
								<div class="modal-footer">
								
									<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
									<div id="overlay_compose" class="overlay_new" style="display:none;position:absolute"><div class="spinner"></div></div>
									<button type="submit" class="btn btn-primary pull-right" style="margin-left:10px"><i class="fa fa-envelope"></i> Send Message</button>
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
										<span class="error" id="name_error">Please Enter Template Name</span>
									</div>
									<div class="form-group" app-field-wrapper="description" ><textarea id="template_description" name="template_description" class="form-control tinymce" rows="6"></textarea>
									<span class="error" id="desc_error">Please Enter Text</span>
									</div> 
								</div>
								<div class="modal-footer">
									<button type="submit" class="btn btn-primary pull-right">Submit</button>
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
<div class="modal fade" id="message-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-wrapper">
		<div class="modal-dialog">
			<div class="modal-content" id="message_id" style="height:auto;position:absolute;">
				
			</div>
		</div>
	</div>
</div>
<!-- BEGIN FORWARD MESSAGE -->
<div class="modal fade" id="forward-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-wrapper">
		<div class="modal-dialog" style="width:54.5% ">
		<div id="overlay_forward" class="overlay_new" style="display:none"><div class="spinner"></div></div>
		<div id="overlay_new" class="overlay_new" style="display:none"><div class="spinner"></div></div>
			<div class="modal-content">
				<div class="modal-header bg-blue">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><i class="fa fa-forward"></i> Forward Message</h4>
				</div>
				<?PHP if(get_option('connect_mail')=='no'){?>
					<form action="<?php echo admin_url('projects/forward_outlook/'.$cur_project_id); ?>" method="post" enctype='multipart/form-data' onsubmit="over_lay('forward')">
				<?php }else{?>
					<form action="<?php echo admin_url('projects/forward/'.$cur_project_id); ?>" method="post" enctype='multipart/form-data' onsubmit="over_lay('forward')">
				<?php }?>
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
					<div class="modal-body">
						<div class="form-group">
							<input name="toemail" type="email" class="form-control" placeholder="To" id="forward_toemail" onkeyup="check_email(this.value,'forward_toemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$" required>
							<input name="msg_id" type="hidden" class="form-control"  id="forward_message" >
						</div>
						<div class="form-group">
							<input name="ccemail" type="email" class="form-control" placeholder="Cc" id="forward_ccemail" onkeyup="check_email(this.value,'forward_ccemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$" >
						</div>
						<div class="form-group">
							<input name="bccemail" type="email" class="form-control" placeholder="Bcc" id="forward_bccemail" onkeyup="check_email(this.value,'forward_bccemail')" multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$" >
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
						<input type="hidden" name="rel_type" value="lead">
						<input type="hidden" name="tasktype" value="2">
						<input type="hidden" name="billable" value="on">
						<input type="hidden" name="task_mark_complete_id" value="">
						<input type="hidden" name="tags" value="">
						<input type="hidden" id="ffilecnt" value="1">
						<input type="hidden" id="ftotcnt" value="1">
						<input type="hidden" id="fallcnt" value="0">
						<input type="hidden" id="f_file" name="m_file">
						<div class="ch_files_f"></div>
						<div id="f_files"></div>
						
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
<!-- END forward MESSAGE -->

<!--Edit Template -->
<div class="modal fade" id="Edit-template" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-wrapper">
		<div class="modal-dialog" style="width:54.5% ">
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
							<span class="error" id="name_edit_error">Please Enter Template Name</span>
						</div>
						<div class="form-group" app-field-wrapper="description" ><textarea id="template_edit_description" name="template_edit_description" class="form-control tinymce" rows="6"></textarea>
						<span class="error" id="desc_edit_error">Please Enter Text</span>
						</div> 
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary pull-right">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- END CREATE TEMPLATE -->

<!-- BEGIN Reply MESSAGE -->
<div class="modal fade" id="reply-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-wrapper">
		<div class="modal-dialog" style="width:54.5% ">
		<div id="overlay_new1" class="overlay_new" style="display:none"><div class="spinner"></div></div>
			<div class="modal-content">
				<div class="modal-header bg-blue">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><i class="fa fa-reply"></i> Reply Message</h4>
				</div>
				<?PHP if(get_option('connect_mail')=='no'){?>
					<form action="<?php echo admin_url('leads/reply_outlook/'.$cur_project_id); ?>" method="post" enctype='multipart/form-data' onsubmit="over_lay('new1')">
				<?php }else{?>
					<form action="<?php echo admin_url('leads/reply/'.$cur_project_id); ?>" method="post" enctype='multipart/form-data' onsubmit="over_lay('new1')">
				<?php }?>
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
					<div class="modal-body">
						<div class="form-group">
							<input name="toemail" type="text" class="form-control" placeholder="To" id="reply_toemail" readonly >
							<input name="msg_id" type="hidden" class="form-control"  id="reply_message" >
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
						<input type="hidden" name="rel_type" value="lead">
						<input type="hidden" name="tasktype" value="2">
						<input type="hidden" name="billable" value="on">
						<input type="hidden" name="task_mark_complete_id" value="">
						<input type="hidden" name="tags" value="">
						<input type="hidden" id="rfilecnt" value="1">
						<input type="hidden" id="rtotcnt" value="1">
						<input type="hidden" id="rallcnt" value="0">
						<input type="hidden" id="r_file" name="m_file">
						<input type="hidden" id="local_id" name="local_id">
						<div class="ch_files_r">
						</div>
						<div id="r_files"></div>
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

<script>
	function gettemplate_list(){
		$.post(admin_url + 'company_mail/template_list',
		{
		},
		function(data){
			var json = $.parseJSON(data);
			$("#template_list1").html(json.table);
			$("#template_header").html(json.header);
	})};
	function sync_mail(){
		document.getElementById('overlay').style.display = ''; 
		$.ajax({
				url: BASE_URL+'admin/cronjob/store_local_mails',
				type: 'POST',
				data: { },
				success: function(data) {
						alert_float('success', 'Mail Fetched Successfully');
						location.reload();
						document.getElementById('overlay').style.display = 'none';
					}
				,
				error: function(data) {
					document.getElementById('overlay').style.display = 'none';
				}
			}
		);
	}


	
	document.addEventListener("DOMContentLoaded", function(event) { 
		tinymce.init({
			selector: 'textarea#template_description',
			height: 100,
			width:675,
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
						ed.save();
					});  
				}  
		});
		tinymce.init({
			selector: '#compose-modal textarea#description',
			height: 100,
			width:665,
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
				// save_draft()  
				});  
			}  
      	});

		$('#compose-modal .tabs').click(function(){
			$('#compose-modal .tabs').removeClass('active');
			$('#compose-modal .tabs-pane fieldset').removeClass('show');
			$(this).addClass('active');
			var target =$(this).attr('data-target')
			$('#compose-modal '+target).addClass('show');
		});


		$('#edit_template_form').submit(function (e) {

			e.preventDefault();
			//$('form').preventDoubleSubmission();
			$('.error').hide();
			$.ajax({
				type: $('#edit_template_form').attr('method'),
				url: $('#edit_template_form').attr('action'),
				data: $('#edit_template_form').serialize(),
				success: function (data) {
					var json = $.parseJSON(data);
					if(json.status == 'success'){
						$('#Edit-template').modal('hide');
						gettemplate_list();
						alert_float('success', 'Template Updated Successfully');
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

		$('#template_form').submit(function (e) {
			e.preventDefault();
			//$('form').preventDoubleSubmission();
			$('.error').hide();
			$.ajax({
				type: $('#template_form').attr('method'),
				url: $('#template_form').attr('action'),
				data: $('#template_form').serialize(),
				success: function (data) {
					var json = $.parseJSON(data);
					if(json.status == 'success'){
						gettemplate_list();
						$(".tabs").removeClass("active");
						$(".tabs h6").removeClass("font-weight-bold");
						$(".tabs h6").addClass("text-muted");
						$('[data-target="#tab021"]').children("h6").removeClass("text-muted");
						$('[data-target="#tab021"]').children("h6").addClass("font-weight-bold");
						$('[data-target="#tab021"]').addClass("active");

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
	});

</script>
<!-- END reply MESSAGE -->
<style type="text/css">
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
.ui-autocomplete {
   
	z-index:99999  !important;
}						
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
.content{
	max-width:none !important;
}
table.body {
    background: none !important;
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
						
						
.text-muted {
    padding: 10px;
}
.error{
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

.tabs {
    margin: 2px 5px 0px 5px;
    cursor: pointer
}


.tabs.active {
    border-bottom: 1px solid #2196F3;
    background-color: #1e95b1;
    color: #fff;
    height: 30px;
}
#compose-modal .active .text-muted, #compose-modal .active .font-weight-bold {
    color: #fff;
    white-space: nowrap;
    vertical-align: middle;
    padding: 10px;
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
				
</style>

