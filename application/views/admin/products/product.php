<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" class="product_profile">
   <div class="content">
      <div class="row">
         
         <?php if($group == 'profile'){ ?>
         <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
            <button class="btn btn-info only-save customer-form-submiter">
            <?php echo _l( 'submit'); ?>
            </button>
         </div>
         <?php } ?>
         <?php if(isset($product)){ ?>
         <div class="col-md-3">
            <div class="panel_s mbot5">
               <div class="panel-body padding-10">
                  <h4 class="bold">
                     <?php echo $title; ?>
                     <?php if(has_permission('customers','','delete') || is_admin()){ ?>
                     <div class="btn-group">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                           <?php if(is_admin()){ ?>
                           <li>
                              <a href="<?php echo admin_url('products/login_as_client/'.$product->id); ?>" target="_blank">
                              <i class="fa fa-share-square-o"></i> <?php echo _l('login_as_client'); ?>
                              </a>
                           </li>
                           <?php } ?>
                           <?php if(has_permission('customers','','delete')){ ?>
                           <li>
                              <a href="<?php echo admin_url('products/delete/'.$product->id); ?>" class="text-danger delete-text _delete"><i class="fa fa-remove"></i> <?php echo _l('delete'); ?>
                              </a>
                           </li>
                           <?php } ?>
                        </ul>
                     </div>
                     <?php } ?>
                  </h4>
               </div>
            </div>
            <?php $this->load->view('admin/products/tabs'); ?>
         </div>
         <?php } ?>
         <div class="col-md-<?php if(isset($product)){echo 9;} else {echo 12;} ?>">
            <div class="panel_s">
               <div class="panel-body">
                  <?php if(isset($product)){ ?>
                  <?php echo form_hidden('isedit'); ?>
                  <?php echo form_hidden('id', $product->id); ?>
                  <div class="clearfix"></div>
                  <?php } ?>
                  <div>
                     <div class="tab-content">
                           <?php $this->load->view((isset($tab) ? $tab['view'] : 'admin/products/groups/profile')); ?>
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
      init_rel_tasks_table(<?php echo $client->userid; ?>,'customer');
   });
</script>
<?php } ?>
<?php $this->load->view('admin/products/client_js'); ?>
<script>
  $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );
  </script>
</body>
</html>
