<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$emails =$this->leads_model->get_emails($lead->id);
?>

<div class="clearfix"></div> 
<!-- BEGIN INBOX CONTENT -->
<div class="col-md-12">
<div id="overlay" style="display: none;"><div class="spinner"></div></div>
	<div class="mbot10">
			<?php if(empty($url1)){?>
				<a class="btn btn-info composebtn" data-toggle="modal" data-target="#compose-modal" onclick="tab_opon_popup()"><i class="fa fa-pencil" ></i>&nbsp;&nbsp;<?php echo _l('compose_email');?></a>
			<?php }else{?>
				<a class="btn btn-info composebtn" href="<?php echo $url1;?>"><i class="fa fa-pencil" ></i>&nbsp;&nbsp;<?php echo _l('compose_email');?></a>
			<?php }?>

			<a class="btn btn-info pull-right composebtn" href="javascript:void(0)" onclick="sync_mail()" title="<?php echo _l('sync_mail_help_text');?>"><i class="fa fa-refresh" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo _l('sync_mail');?></a>

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
				<table class="table dt-table" >
					<thead>
						<tr>
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
						<?php }}?>
					</tbody>
				</table>
			</form>
			<?php echo $pagination;?>
		</div>
	</div>
</div>
<!-- END COMPOSE MESSAGE -->
<div class="modal fade" id="message-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-wrapper">
		<div class="modal-dialog">
			<div class="modal-content" id="message_id" style="height:auto;position:absolute;width:100%">
				
			</div>
		</div>
	</div>
</div>
<?php $this->load->view("admin/staff/emailcomposer") ?>

<script>
	function sync_mail(){
		document.getElementById('overlay').style.display = ''; 
		$.ajax({
			url: admin_url+'cronjob/store_local_mails',
			type: 'POST',
			data: { },
			success: function(data) {
					alert_float('success', 'Mail Fetched Successfully');
					setTimeout(function(){  
						window.location.href = '<?php echo admin_url('leads/lead/'.$lead->id.'?group=tab_email') ?>';
					}, 500);
				}
			,
			error: function(data) {
				document.getElementById('overlay').style.display = 'none';
			}
		});
	}

	function getMessage(val){
		document.getElementById('overlay').style.display = '';
		$.post(admin_url + 'leads/getmessage',
		{
			uid:val
		},
		function(data,status){
			document.getElementById('overlay').style.display = 'none'; 
			$('#message-modal .modal-content').html(data);
				// show modal
			$('#message-modal').modal('show');
			
		});
	}
</script>