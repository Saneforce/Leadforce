<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <?php //echo $this->import->downloadSampleFormHtml(); ?>
            <?php //echo $this->import->maxInputVarsWarningHtml(); ?>
            <?php if(!$this->import->isSimulation()) { ?>
              <?php //echo $this->import->importGuidelinesInfoHtml(); ?>
              <?php //echo $this->import->createSampleTableHtml(); ?>
            <?php } else { ?>
              <?php //echo $this->import->simulationDataInfo(); ?>
              <?php //echo $this->import->createSampleTableHtml(true); ?>
            <?php } ?>
            <form action="<?php echo admin_url('ImportData');?>" method="post" accept-charset="utf-8" style="float: left; padding-right: 10px;">
              <input type="hidden" name="download_sample" value="true">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
              <button type="submit" class="btn btn-success">Download Deal Sample</button>
            </form>
            <form action="<?php echo admin_url('ImportData');?>" method="post" accept-charset="utf-8">
              <input type="hidden" name="download_contact_sample" value="true">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
              <button type="submit" class="btn btn-success">Download Contact Sample</button>
            </form>
            <hr>
            <ul>
              <li class="">1. Your CSV data should be in the format below. The first line of your CSV file should be the column headers as in the table example. Also make sure that your file is <b>CSV(Comma delimited)</b> to avoid unnecessary <b>encoding problems</b>.</li>
              <li class="">2. Please follow the Sample CSV Data structure to import the Data's Correctly.</li>
              <li>3. Please follow the below instructions.</li>
            </ul>
            <h5>Deal Import : <font color="red">Note</font></h5>
            <ul>
              <li class="">1. Make sure the following details should not be empty.
                <ul style="margin-left: 26px; list-style-type: disclosure-closed;">
                  <li>
				  <?php if(!empty($mandatory_fields1)){ 
					foreach($mandatory_fields1 as $mandatory_field12){
						echo $mandatory_field12.', ';
					}
				  }?>
				  Activity name, Activity type, Priority, Activity start date.</li></ul>
              </li>
              
              <li class="">2. Make sure the following details should be valid.
                <ul style="margin-left: 26px; list-style-type: disclosure-closed;">
                  <li>Pipeline, Pipeline stage, Deal Owner Mail Id, Assigned Person Mail Id.</li>
                </ul>
              </li>
            </ul>
            <h5>Contact Import : <font color="red">Note</font></h5>
            <ul>
              <li class="">1. Make sure the Deals id and Person fullname should not be empty.</li>
              <li class="">2. Please provide valid Deals id.</li>
            </ul>
            <div class="row">
              <div class="col-md-4 mtop15">
                <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'import_form')) ;?>
                <?php echo form_hidden('clients_import','true'); ?>
                <?php echo render_input('file','choose_csv_file','','file'); ?>
                <?php
                // if(is_admin() || get_option('staff_members_create_inline_customer_groups') == '1'){
                //   echo render_select_with_input_group('groups_in[]',$groups,array('id','name'),'customer_groups',($this->input->post('groups_in') ? $this->input->post('groups_in') : array()),'<a href="#" data-toggle="modal" data-target="#customer_group_modal"><i class="fa fa-plus"></i></a>',array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
                // } else {
                //   echo render_select('groups_in[]',$groups,array('id','name'),'customer_groups',($this->input->post('groups_in') ? $this->input->post('groups_in') : array()),array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
                // }
                // echo render_input('default_pass_all','default_pass_clients_import',$this->input->post('default_pass_all')); ?>
                <div class="form-group">
                  <button type="submit" name="deals" id="deals" value="deals" class="btn btn-info import btn-import-submit">Deals <?php echo _l('import'); ?></button>
                  <button type="submit" name="contact" id="contact" value="contact" class="btn btn-info import btn-import-submit">Contacts <?php echo _l('import'); ?></button>
                  <!-- <button type="button" class="btn btn-info simulate btn-import-submit"><?php echo _l('simulate_import'); ?></button> -->
                </div>
                <?php echo form_close(); ?>
              </div>
            </div>
            <hr>
            <div class="clearfix"></div> 
<table class="table dt-table scroll-responsive table-project-files" data-order-col="4" data-order-type="desc">
  <thead>
    <tr>
      <th><?php echo _l('project_file_filename'); ?></th>
      <th><?php echo _l('file_imported_by'); ?></th>
      <th><?php echo _l('imported_status'); ?></th>
      <th><?php echo _l('file_details'); ?></th>
      <th><?php echo _l('project_imported_date'); ?></th>
      <th><?php echo _l('revert_option'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($files as $file){
      ?>
      <tr>
        <td data-order="<?php echo $file['filename']; ?>">
          <a href="#" onclick="download_imported_files('<?php echo $file['import_id']; ?>','<?php echo $file['filename']; ?>'); return false;">
            <?php echo $file['filename']; ?></a>
          </td>
          <td data-order="<?php echo $file['name']; ?>"><?php echo $file['name']; ?></td>
          <td data-order="<?php echo $file['status']; ?>">
            <?php echo $file['status']; ?>
          </td>
          
          <td >
            <?php
            $url = './uploads/import_files/'.$file['import_id'].'/skipped_file.xls';
            if($file['status'] == 'Finished'){ 
              if(file_exists($url)) {
                $style = "";
              } else {
                $style = "pointer-events: none; cursor: default;";
              }
            } else {
              $style = "pointer-events: none; cursor: default;";
            }
              echo 'Imported - '.$file['imported'].'<br>';
              echo 'Skipped - '.$file['skipped'].'<br>';
              echo '<a href="#" onclick="download_csv_files(\''.$file['import_id'].'\');"  style="'.$style.'">Download Skipped File</a>';
            ?>
          </td>
          
         <td data-order="<?php echo $file['created_date']; ?>"><?php echo _dt($file['created_date']); ?></td>
         <td>
           <?php
            $style = "";
            $sname = 'Revert';
            if($file['status'] != 'Finished'){ 
              $style = "pointer-events: none; cursor: default;";
              $sname = 'Reverted';
          } ?>
           <a href="<?php echo admin_url()?>ImportData/revertData/<?php echo $file['import_id']; ?>" onclick="return confirm('Are you sure? Do you want to revert?')" style="<?php echo $style;?>" class="btn btn-danger btn-icon"><?php echo $sname; ?></a>
         </td>
       </tr>
       <?php } ?>
     </tbody>
   </table>
</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
<?php $this->load->view('admin/clients/client_group'); ?>
<?php init_tail(); ?>
<script src="<?php echo base_url('assets/plugins/jquery-validation/additional-methods.min.js'); ?>"></script>
<script>
 $(function(){
   appValidateForm($('#import_form'),{
     file:{required:true,extension: "csv"},
     source:'required',
     status:'required'
    });
 });
 $("#import_form").submit(function(){	 
      var yourFileName = $("#file").val();
      var yourFileExtension = yourFileName .replace(/^.*\./, '');
      if(yourFileExtension == 'csv') {
        $('#deals').css('pointer-events','none');
        $('#contact').css('pointer-events','none');
      }

    });

  function download_imported_files(filepath, filename) {
    document.location.href = admin_url + 'ImportData/downloadcsv?filepath='+filepath+'&filename='+filename;
  }

  function download_csv_files(filepath) {
    document.location.href = admin_url + 'ImportData/downloadskipcsv?filepath='+filepath;
  }

</script>
</body>
</html>
