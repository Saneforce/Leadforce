<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="customer-profile-group-heading"><?php echo _l('projects'); ?></h4>
<?php if(isset($client)){ ?>
<?php if(has_permission('projects','','create')){ ?>
<!-- <a href="<?php echo admin_url('projects/project?customer_id='.$client->userid); ?>" class="btn btn-info mbot25<?php if($client->active == 0){echo ' disabled';} ?>"><?php echo _l('new_project'); ?></a> -->
<?php } ?>
<div class="row">
   <?php
      $_where = '';
      if(!has_permission('projects','','view')){
        $_where = 'id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id='.get_staff_user_id().')';
      }
      ?>
   <?php 
   $my_staffids = $this->staff_model->get_my_staffids();
   foreach($project_statuses as $status){ ?>
   <!-- <div class="col-md-5ths total-column">
      <div class="panel_s">
         <div class="panel-body">
            <h3 class="text-muted _total">
               <?php 
               $where = ($_where == '' ? '' : $_where.' AND ').'status = '.$status['id']. ' AND clientid='.$client->userid; 
               
                if($my_staffids){
                    $where .= ' AND ' . db_prefix() . 'projects.id IN (SELECT ' . db_prefix() . 'projects.id FROM ' . db_prefix() . 'projects join ' . db_prefix() . 'project_members  on ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id WHERE ' . db_prefix() . 'project_members.staff_id in (' . implode(',',$my_staffids) . ') OR  ' . db_prefix() . 'projects.teamleader in (' . implode(',',$my_staffids) . ') )';
                }
               ?>
               <?php $where .= ' AND deleted_status=0 '; ?>
               <?php echo total_rows(db_prefix().'projects',$where); ?>
            </h3>
            <span style="color:<?php echo $status['color']; ?>"><?php echo $status['name']; ?></span>
         </div>
      </div>
   </div> -->
   <?php } ?>
</div>
<?php
   $this->load->view('admin/projects/table_html', array('class'=>'projects-single-client'));
}
?>
