<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content"> 
		<div class="row">
			<div class="panel_s project-menu-panel">
			<div class="panel-body">
				<div class="horizontal-tabs">
					<ul class="nav nav-tabs no-margin project-tabs nav-tabs-horizontal" role="tablist">
						<li class="<?php if($type=='deal'){?>active<?php }?>">
							<a data-group="deal" role="tab" href="<?php echo admin_url('reports/view_deal_folder/deal'); ?>">
								<i class="fa fa-usd  fa-fw fa-lg" aria-hidden="true"></i><?php echo _l('view_report_deal');?>
							</a>
						</li>
						<li class="<?php if($type=='activity'){?>active<?php }?>">
							<a data-group="activity" role="tab" href="<?php echo admin_url('reports/view_deal_folder/activity'); ?>">
								<i class="fa fa-tasks  fa-fw fa-lg" aria-hidden="true"></i><?php echo _l('view_report_activity');?>                               
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
			<div class="panel_s project-menu-panel">
				<div class="panel-body">
					<div class="col-md-12">
						<div class="panel_s">
							<div class="panel-body">
								<h4 class="no-margin"><?php echo $title; ?></h4>
								<hr class="hr-panel-heading">
								<?php
									$table_data = array('Id',_l('folder'),_l('num_reports'),_l('create_date'),_l('update_date'));
									render_datatable($table_data,'folder_deal_view');  
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
<div class="modal fade" id="folder_edit_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div id="overlay_deal" style="display: none;"><div class="spinner"></div></div>
			<?php echo form_open(admin_url('reports/update_folder'),array('id'=>'folder_edit')); ?>
				<div class="modal-header">
					<button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">
						<span class="edit-title"><?php echo _l('edit_name'); ?></span>
					</h4>
				</div>
				<div class="modal-body" id="edit_folder">
					<input type="hidden" id="folder_id" name="folder_id">
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
$(function(){
	var notSortableAndSearchableItemColumns = [];
    initDataTable('.table-folder_deal_view', admin_url+'reports/folder_deal_view?type=<?php echo $type;?>', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns,'undefined',[0,'asc']);
});
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
$( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
	var frm = $('#folder_edit');
	
	frm.submit(function (e) {
		 document.getElementById('overlay_deal').style.display = '';
		var name_val = $('#name').val();
		e.preventDefault();
		if ( name_val.match(/^[a-zA-Z0-9]+/) && name_val!='' ) {
			$.ajax({
				type: frm.attr('method'),
				url: frm.attr('action'),
				data: frm.serialize(),
				success: function (data) {
					$('.dataTable').DataTable().ajax.reload();
					document.getElementById('overlay_deal').style.display = 'none';
					alert_float('success', 'Folder Updated Successfully');
				   $('#folder_edit_modal').modal('toggle');
				   
				},
				error: function (data) {
					console.log('An error occurred.');
					console.log(data);
				},
			});
		}
	});
  } );
  function edit_folder(a){
	  document.getElementById('overlay_deal').style.display = '';
	  $('#folder_id').val(a);
	  var data = {cur_id:a};
	  $('#cur_report').val(a);
		var ajaxRequest = $.ajax({
			type: 'POST',
			url: admin_url + 'reports/folder_edit',
			data: data,
			dataType: '',
			success: function(msg) {
				$('#name').val(msg);
				document.getElementById('overlay_deal').style.display = 'none';
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