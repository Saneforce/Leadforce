<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
   <div class="content">
      <div class="row">
         
         <div class="col-md-3">
            <div class="panel_s mbot5">
               <div class="panel-body padding-10">
                  <h4 class="bold">
                     <?php echo $title; ?>
                     
                  </h4>
               </div>
            </div>
            <?php $this->load->view('admin/clients/tabs_contact'); ?>
         </div>
         <div class="col-md-9">
            <div class="panel_s">
               <div class="panel-body">
                  <?php if(isset($client)){ ?>
                  <?php echo form_hidden('isedit'); ?>
                  <?php echo form_hidden('userid', $client->userid); ?>
                  <div class="clearfix"></div>
                  <?php } ?>
                  <div>
                     <div class="tab-content">
                           <?php $this->load->view((isset($tab) ? $tab['view'] : 'admin/clients/groups/profile_contact')); ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php if($group == 'profile'){ ?>
         <div class="btn-bottom-pusher"></div>
      <?php } ?>
   </div>
</div>
<?php init_tail(); ?>
<?php if(isset($client)){ ?>
<script>
   $(function(){
      init_rel_tasks_table(<?php echo $client->userid; ?>,'contact');
   });
</script>
<?php } ?>
<?php $this->load->view('admin/clients/client_js'); ?>
<script>
  $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );
  </script>
</body>
</html>
