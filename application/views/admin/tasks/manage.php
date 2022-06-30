<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                <div class="row _buttons">
                     <div class="col-md-8">
                        <?php if(has_permission('tasks','','create')){ ?>
                        <a href="#" onclick="new_task(<?php if($this->input->get('project_id')){ echo admin_url('tasks/task?rel_id='.$this->input->get('project_id').'&rel_type=project'); } ?>); return false;" class="btn btn-info pull-left new"><?php echo _l('new_task'); ?></a>
                        <?php } ?>
                        <a href="<?php if(!$this->input->get('project_id')){ echo admin_url('tasks/switch_kanban/'.$switch_kanban); } else { echo admin_url('projects/view/'.$this->input->get('project_id').'?group=project_tasks'); }; ?>" class="btn btn-default mleft10 pull-left hidden-xs">
                           <?php if($switch_kanban == 1){ echo _l('switch_to_list_view');}else{ echo _l('leads_switch_to_kanban');} ?>
                        </a>
                     </div>
                     <div class="col-md-4">
                        <?php if($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
                        <div data-toggle="tooltip" data-placement="bottom" data-title="<?php echo _l('search_by_tags'); ?>">
                           <?php echo render_input('search','','','search',array('data-name'=>'search','onkeyup'=>'tasks_kanban();','placeholder'=>_l('search_tasks')),array(),'no-margin') ?>
                        </div>
                        <?php } else { ?>
                        <?php $this->load->view('admin/tasks/tasks_filter_by1',array('view_table_name'=>'.table-tasks')); ?>
                        <!-- <a href="<?php echo admin_url('tasks/detailed_overview'); ?>" class="btn btn-success pull-right mright5"><?php echo _l('detailed_overview'); ?></a> -->
                        <?php } ?>
                     </div>
                  </div>
                  <hr class="hr-panel-heading hr-10" />
                  <div class="clearfix"></div>
                  <?php
                  if($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
                  <div class="kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                     <div class="row">
                        <div id="kanban-params">
                           <?php echo form_hidden('project_id',$this->input->get('project_id')); ?>
                        </div>
                        <div class="container-fluid">
                           <div id="kan-ban"></div>
                        </div>
                     </div>
                  </div>
                  <?php } else { ?>
                  <?php $this->load->view('admin/tasks/_summary',array('table'=>'.table-tasks')); ?>
                  <a href="#" data-toggle="modal" data-target="#tasks_bulk_actions" class="hide bulk-actions-btn table-btn" data-table=".table-tasks"><?php echo _l('bulk_actions'); ?></a>
                  <?php $this->load->view('admin/tasks/_table',array('bulk_actions'=>true)); ?>
               <?php $this->load->view('admin/tasks/_bulk_actions'); ?>
               <?php } ?>
            </div>
         </div>
      </div>
   </div>
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
                <button group="button" id="closeaudio" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
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
<style>
	.check_text{color:#fff !important}
	th.sorting{white-space:nowrap}
	.single_linet{white-space:nowrap}
		
	</style>
<script>
   taskid = '<?php echo $taskid; ?>';
   $(function(){
       tasks_kanban();



   $( "#period-from" ).change(function () {
      $('#custom_date_start_tasks').val($( "#period-from" ).val());
   });
   $( "#period-to" ).change(function () {
      $('#custom_date_end_tasks').val($( "#period-to" ).val());
   });


   });

   
   function gettasks_summary_data(){
      // $.ajax({
      //     url: admin_url + 'tasks/gettasks_summary_data',
      //     success: function(msg){
      //       $('#tasks_summary_data').html(msg);
      //     }
      // });
   }
</script>



<script>
  $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );
  </script>
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
</body>
</html>
