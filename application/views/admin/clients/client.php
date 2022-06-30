<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
.btn-disable {
  pointer-events: none;
  opacity:0.5;
}
</style>
<?php if($_REQUEST['group']=='' || $_REQUEST['group']=='tasks'){?>
	<style>
	.check_text{color:#fff !important}
	th.sorting{white-space:nowrap}
	.single_linet{white-space:nowrap}
		
	</style>
<?php }?>

<?php 
      $my_staffids = $this->staff_model->get_my_staffids();
      $view_ids = $this->staff_model->getFollowersViewList();
      $teamleader = $client->addedfrom;
      //pr($task);
      $btn = '';
      if($teamleader) {
        if ((!empty($my_staffids) && in_array($teamleader,$my_staffids) && !in_array($teamleader,$view_ids)) || is_admin(get_staff_user_id()) || $teamleader == get_staff_user_id()) {
              $btn = '';
        } else {
              $btn = 'btn-disable';
        }
      }
?>
<div id="wrapper" class="customer_profile">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <?php if(isset($client) && $client->registration_confirmed == 0 && is_admin()){ ?>
               <div class="alert alert-warning">
                  <?php echo _l('customer_requires_registration_confirmation'); ?>
                  <br />
                  <a href="<?php echo admin_url('clients/confirm_registration/'.$client->userid); ?>"><?php echo _l('confirm_registration'); ?></a>
               </div>
            <?php } else if(isset($client) && $client->active == 0 && $client->registration_confirmed == 1){ ?>
            <div class="alert alert-warning">
               <?php echo _l('customer_inactive_message'); ?>
               <br />
               <a href="<?php echo admin_url('clients/mark_as_active/'.$client->userid); ?>"><?php echo _l('mark_as_active'); ?></a>
            </div>
            <?php } ?>
            <?php if(isset($client) && (!has_permission('customers','','view') && is_customer_admin($client->userid))){?>
            <div class="alert alert-info">
               <?php echo _l('customer_admin_login_as_client_message',get_staff_full_name(get_staff_user_id())); ?>
            </div>
            <?php } ?>
         </div>
         <?php if($group == 'profile'){ ?>
         <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
            <button class="btn btn-info only-save customer-form-submiter <?php echo $btn; ?>">
            <?php echo _l( 'submit'); ?>
            </button>
            <?php if(!isset($client)){ ?>
            <button class="btn btn-info save-and-add-contact customer-form-submiter <?php echo $btn; ?>">
            <?php echo _l( 'save_customer_and_add_contact'); ?>
            </button>
            <?php } ?>
         </div>
         <?php } ?>
         <?php if(isset($client)){ ?>
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
                              <a href="<?php echo admin_url('clients/login_as_client/'.$client->userid); ?>" target="_blank">
                              <i class="fa fa-share-square-o"></i> <?php echo _l('login_as_client'); ?>
                              </a>
                           </li>
                           <?php } ?>
                           <?php if(has_permission('customers','','delete')){ ?>
                           <li>
                              <a href="<?php echo admin_url('clients/delete/'.$client->userid); ?>" class="text-danger delete-text _delete"><i class="fa fa-remove"></i> <?php echo _l('delete'); ?>
                              </a>
                           </li>
                           <?php } ?>
                        </ul>
                     </div>
                     <?php } ?>
                     <?php if(isset($client) && $client->leadid != NULL){ ?>
                        <br />
                        <small>
                           <b><?php echo _l('customer_from_lead',_l('lead')); ?></b>
                           <a href="<?php echo admin_url('leads/index/'.$client->leadid); ?>" onclick="init_lead(<?php echo $client->leadid; ?>); return false;">
                             - <?php echo _l('view'); ?>
                          </a>
                       </small>
                    <?php } ?>
                  </h4>
               </div>
            </div>
            <?php $this->load->view('admin/clients/tabs'); ?>
         </div>
         <?php } ?>
         <div class="col-md-<?php if(isset($client)){echo 9;} else {echo 12;} ?>">
            <div class="panel_s">
               <div class="panel-body">
                  <?php if(isset($client)){ ?>
                  <?php echo form_hidden('isedit'); ?>
                  <?php echo form_hidden('userid', $client->userid); ?>
                  <div class="clearfix"></div>
                  <?php } ?>
                  <div>
                     <div class="tab-content">
                           <?php $this->load->view((isset($tab) ? $tab['view'] : 'admin/clients/groups/profile')); ?>
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
<div class="modal fade" id="play_record" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:340px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Play Recorded</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center">
                    <div id="playhtml">
                      
                    </div>
                  </div>
                  
              </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" id="closeaudio" ><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="view_history" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Call History</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                    <div id="historyhtml">
                      
                    </div>
                  </div>
                  
              </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
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
<?php $this->load->view('admin/clients/client_js'); ?>
<script>
  $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );
  </script>
  <?php if($_REQUEST['group']=='' || $_REQUEST['group']=='tasks'){?>
<script>

var originalLeave = $.fn.tooltip.Constructor.prototype.leave;
$.fn.tooltip.Constructor.prototype.leave = function(obj) {
  var self = obj instanceof this.constructor ?
    obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type)
  var container, timeout;

  originalLeave.call(this, obj);

  if (obj.currentTarget) {
    container = $(obj.currentTarget).siblings('.tooltip')
    timeout = self.timeout;
	$('.check_text').click(function(e) {  
	$.fn.tooltip.Constructor.prototype.leave.call(self, self);
    });
	container.one('click', function() {
		$("[data-toggle='tooltip']").tooltip('hide');
	$.fn.tooltip.Constructor.prototype.leave.call(self, self);
      container.one('mouseleave', function() {
        $.fn.tooltip.Constructor.prototype.leave.call(self, self);
		
      });
    });
	
    container.one('mouseenter', function() {
      //We entered the actual popover – call off the dogs
      clearTimeout(timeout);
      //Let's monitor popover content instead
      container.one('mouseleave', function() {
		  clearTimeout(timeout);
        $.fn.tooltip.Constructor.prototype.leave.call(self, self);
      });
    })
  }
};


$('body').tooltip({
  selector: '[data-toggle] , .tooltip',
  trigger: 'click hover',
  placement: 'auto',
  delay: {
    show: 50,
    hide: 400
  }
});
function copyToClipboard(element) {
	 
	var str = element.id
	var req_txt =  str.split('_');
	var str1 = req_txt[0].toLowerCase().replace(/\b[a-z]/g, function(letter) {
		return letter.toUpperCase();
	});
	var req_element = 'input_'+element.id;
	element = element.id;
	
	var copyText = document.getElementById(req_element);
	copyText.select();
	navigator.clipboard.writeText(copyText.value);
   alert_float('success', str1+' Copied Successfully');
   $("[data-toggle='tooltip']").tooltip('hide');
   setTimeout( function(){ 
    $("[data-toggle='tooltip']").tooltip('hide');
  }  , 500 );
  /* Alert the copied text */
}
  
  </script>
<?php }?>
</body>
</html>
