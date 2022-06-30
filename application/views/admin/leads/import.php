<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<?php echo $this->import->downloadSampleFormHtml();
						echo $this->import->maxInputVarsWarningHtml();
						if(!$this->import->isSimulation()) {
							echo $this->import->importGuidelinesInfoHtml();
							echo $this->import->createSampleTableHtml();
						}
						else {
							echo $this->import->simulationDataInfo();
							echo $this->import->createSampleTableHtml(true);
						} ?>
						<div class="row">
							<div class="col-md-4">
								<?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'import_form'));
									echo form_hidden('leads_import','true');
									echo render_input('file_csv','choose_csv_file','','file');
									echo render_select('pipeline',$pipelines, array('id',array('name')),'pipeline',$this->input->post('pipeline'), array('required' => true)); ?>
									<div class="form_status">
										<?php echo render_select('status',$statuses, array('id',array('name')),'lead_import_status',$this->input->post('status'), array('required' => true)); ?>
									</div>
									<div class="form_teamleader">
										<?php echo render_select('teamleader',$teamleaders,array('staffid',array('firstname','lastname')),'teamleader',$this->input->post('teamleader'), array('required' => true)); ?>
									</div>
									<div class="form_responsible">
										<?php echo render_select('responsible',$teammembers,array('staffid',array('firstname','lastname')),'teammembers',$this->input->post('responsible'), array('required' => true)); ?>
									</div>
									<div class="form-group">
										<button type="button" class="btn btn-info import btn-import-submit"><?php echo _l('import'); ?></button>
										<button type="button" class="btn btn-info simulate btn-import-submit"><?php echo _l('simulate_import'); ?></button>
									</div>
								<?php echo form_close(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script src="<?php echo base_url('assets/plugins/jquery-validation/additional-methods.min.js'); ?>"></script>
<script>
$(function(){
	appValidateForm($('#import_form'),{file_csv:{required:true,extension: "csv"},pipeline:'required',teamleader:'required',status:'required',responsible:'required'});
	
	if($('#status').length > 0) {
		$('.form_status .selectpicker').addClass("formstatus");
	}
	if($('#teamleader').length > 0) {
		$('.form_teamleader .selectpicker').addClass("formteamleader");
	}
	if($('#responsible').length > 0) {
		$('.form_responsible .selectpicker').addClass("formresponsible");
	}
	
	$('#pipeline').change(function() {
		$('.formstatus').selectpicker('destroy');
		$('.formstatus').html('').selectpicker('refresh');
		
		$('.formresponsible').selectpicker('destroy');
		$('.formresponsible').html('').selectpicker('refresh');
		
		$('.formteamleader').selectpicker('destroy');
		$('.formteamleader').html('').selectpicker('refresh');
		
		var pipeline = $('#pipeline').val();
		$.ajax({
			url: admin_url + 'leads/changepipeline',
			type: 'POST',
			data: { 'pipeline_id': pipeline },
			dataType: 'json',
			success: function success(result) {
				$('.formstatus').selectpicker('destroy');
				$('.formstatus').html(result.statuses).selectpicker('refresh');
				
				$('.formteamleader').selectpicker('destroy');
				$('.formteamleader').html(result.teamleaders).selectpicker('refresh');
		
				$('.formresponsible').selectpicker('destroy');
				$('.formresponsible').html(result.teammembers).selectpicker('refresh');
			}
		});
	});
});
</script>
</body>
</html>