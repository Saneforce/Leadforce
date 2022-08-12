<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content"> 
		<div class="row">
			<div class="panel_s project-menu-panel">
				<div class="panel-body">
					<div class="col-md-12">
						<div class="panel_s">
							<div class="panel-body">
								<h4 class="no-margin" style="position: relative;top: -4px;">
									<a href="<?php echo admin_url('reports/view_deal_folder');?>" title="Back"><i class="fa fa-arrow-circle-left fa-6" style="font-size: 22px;padding-right: 10px;top: 4px;position: relative"></i></a>
									<?php echo $title; ?>
								</h4>
								<hr class="hr-panel-heading">
								<?php
									$table_data = array('Id',_l('report'),_l('create_date'),_l('update_date'));
									render_datatable($table_data,'report_deal_view');  
								?>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="btn-bottom-pusher"></div>
	</div>
</div>
<div class="modal fade" id="public_add_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div id="overlay_deal" style="display: none;"><div class="spinner"></div></div>
			<div class="modal-header">
				<button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">
					<span class="edit-title"><?php echo _l('public_link'); ?></span>
				</h4>
			</div>
			<div class="modal-body">
				<div>
				<input type="hidden" id="cur_report" >
					Create separate links to control access for different viewer groups and name each link accordingly. To revoke access, simply delete a link.
				</div>
				<div id="public_all">
					
				</div>
				<div class="row" > <div class="col-md-12"><a href="javascript:void(0)" onclick="add_public_link('<?php echo $id;?>')"><?php echo _l('add_link');?></a></div></div>
			</div>
			<div class="modal-footer">
				<button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="clientid_add_modal_public" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
			<div class="modal-dialog" role="document">
			<div id="overlay_deal12" class="overlay_new" style="display: none;"><div class="spinner"></div></div>
				<div class="modal-content">
					<div class="modal-header">
						<button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">
							<span class="edit-title"><?php echo _l('public_link'); ?></span>
						</h4>
					</div>
					<?php echo form_open(admin_url('reports/update_public_name'),array('id'=>'update_public_name')); ?>
					<div class="modal-body">
						<input type="hidden" id="link_id" name="link_id">
							<?php $attrs = array('autofocus'=>true, 'required'=>true); ?>
							<?php echo render_input( 'ch_name12', 'name','','text',$attrs); ?>
							<div id="companyname_exists_info" class="hide"></div>
							
					</div>
					<div class="modal-footer">
						<button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
						<button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
						
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
<div class="modal fade" id="report_edit_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div id="overlay_deal1" class="overlay_new" style="display: none;"><div class="spinner"></div></div>
			<?php echo form_open('admin/reports/update_edit_report',array('id'=>'report_edit')); ?>
				<div class="modal-header">
					<button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">
						<span class="edit-title"><?php echo _l('edit_name'); ?></span>
					</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="report_id" name="report_id">
					<?php $attrs = array('autofocus'=>true, 'required'=>true,'onblur'=>"check_name(this)",'onkeyup'=>"check_validate(this)", 'maxlength'=>"150"); ?>
					<?php echo render_input( 'name', 'name','','',$attrs); ?>
					<div id="contact_exists_info" class="hide"></div>								
					<div class="text-danger" id="name_id" style="display:none">Please enter valid name</div>							
					<div class="input_fields_wrap_ae">
					
					</div>
				</div>
				<div class="modal-footer">
					<button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
					<button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				</div>
			<?php echo form_close();?>
		</div>
	</div>
</div>
<link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
<?php init_tail(); ?>
<script>
function myFunction(a) {
  /* Get the text field */
  var copyText = document.getElementById("name_"+a);
  /* Select the text field */
  copyText.select();
  copyText.setSelectionRange(0, 99999); /* For mobile devices */

  /* Copy the text inside the text field */
  document.execCommand( 'copy' );
  alert_float('success', 'Link Copied Successfully');
  
  /* Alert the copied text */
}
function check_validate(a){
	var name_val = $('#'+a.id).val();
	$('#'+a.id+'_id').hide();
	if ( name_val.match(/^[a-zA-Z0-9]+/)  ) {
	} else {
		$('#'+a.id+'_id').show();
		return false;
	}
}
function check_name(a){
	$('#'+a.id).val(a.value.trim());
}
$(function(){
	var notSortableAndSearchableItemColumns = [];
    initDataTable('.table-report_deal_view', admin_url+'reports/report_deal_view/<?php echo $id;?>', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns,'undefined',[0,'asc']);
});
$( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
	$("#report_edit").submit(function(e) {
		e.preventDefault(); // avoid to execute the actual submit of the form.
		var form = $(this);
		var actionUrl = form.attr('action');
		var name_val = $('#name').val();
		if ( name_val.match(/^[a-zA-Z0-9]+/) && name_val!='' ) {
			document.getElementById('overlay_deal1').style.display = '';
			$.ajax({
				type: form.attr('method'),
				url: actionUrl,
				data: form.serialize(), // serializes the form's elements.
				success: function(data)
				{
					$('.dataTable').DataTable().ajax.reload();
						alert_float('success', 'Report Updated Successfully');
						document.getElementById('overlay_deal1').style.display = 'none';
					   $('#report_edit_modal').modal('toggle');
				}
			});
		}
		
	});
	$("#update_public_name").submit(function(e) {
		e.preventDefault(); // avoid to execute the actual submit of the form.
		var form = $(this);
		var actionUrl = form.attr('action');
		document.getElementById('overlay_deal12').style.display = '';
		$.ajax({
			type: form.attr('method'),
			url: actionUrl,
			data: form.serialize(), // serializes the form's elements.
			success: function(data)
			{
				$('.dataTable').DataTable().ajax.reload();
				alert_float('success', 'Link Name Updated Successfully');
				document.getElementById('overlay_deal12').style.display = 'none';
				$('#clientid_add_modal_public').modal('toggle');
				var a = $('#cur_report').val();
				load_public(a);
			}
		});
		
	});
  } );
  function add_public_link(){
	 var a = $('#cur_report').val();
	 document.getElementById('overlay_deal').style.display = '';
	var data = {req_val:a};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/public_link',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#public_all').html(msg);
			document.getElementById('overlay_deal').style.display = 'none';
		}
	});
}
function check_publick(a){
	 //var a = $('#cur_report').val();
	 document.getElementById('overlay_deal12').style.display = '';
	 document.getElementById('overlay_deal').style.display = '';
	var data = {req_val:a};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/check_publick',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#ch_name12').val(msg);
			$('#link_id').val(a);
			document.getElementById('overlay_deal').style.display = 'none';
			document.getElementById('overlay_deal12').style.display = 'none';
		}
	});
}
function delete_link(a){
	//var a = $('#cur_report').val();
	document.getElementById('overlay_deal').style.display = '';
	var cur_id12 = $('#cur_report').val();
	var data = {req_val:a,cur_id12:cur_id12};
	 var ajaxRequest = $.ajax({
		type: 'POST',
		url: admin_url + 'reports/delete_link',
		data: data,
		dataType: '',
		success: function(msg) {
			$('#public_all').html(msg);
			document.getElementById('overlay_deal').style.display = 'none';
		}
	});
}
  function load_public(a){
	  document.getElementById('overlay_deal').style.display = '';
	  var data = {cur_id:a};
	  $('#cur_report').val(a);
		var ajaxRequest = $.ajax({
			type: 'POST',
			url: admin_url + 'reports/load_public',
			data: data,
			dataType: '',
			success: function(msg) {
				$('#public_all').html(msg);
				document.getElementById('overlay_deal').style.display = 'none';
			}
		});
  }
  function edit_report(a){
	  document.getElementById('overlay_deal1').style.display = '';
	  $('#report_id').val(a);
	  var data = {cur_id:a};
	  $('#cur_report').val(a);
		var ajaxRequest = $.ajax({
			type: 'POST',
			url: admin_url + 'reports/report_edit',
			data: data,
			dataType: '',
			success: function(msg) {
				$('#name').val(msg);
				document.getElementById('overlay_deal1').style.display = 'none';
			}
		});
  }
  </script>
  <style>
  .dt-buttons.btn-group {
		display: none;
	}
	.dataTables_length {
		display: none;
	}
	/* Absolute Center Spinner */
#overlay_deal,.overlay_new {
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
#overlay_deal:before,.overlay_new:before {
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
#overlay_deal:not(:required),.overlay_new:not(:required) {
  /* hide "loading..." text */
  font: 0/0 a;
  color: transparent;
  text-shadow: none;
  background-color: transparent;
  border: 0;
}

#overlay_deal:not(:required):after,.overlay_new:not(:required):after {
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