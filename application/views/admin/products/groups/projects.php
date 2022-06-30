<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="customer-profile-group-heading"><?php echo _l('projects'); ?></h4>
<?php if(isset($product)){ ?>
<!-- <?php if(has_permission('projects','','create')){ ?>
<a href="<?php echo admin_url('projects/project?customer_id='.$product->id); ?>" class="btn btn-info mbot25<?php if($client->active == 0){echo ' disabled';} ?>"><?php echo _l('new_project'); ?></a>
<?php } ?> -->
<div class="row">
   
</div>
<?php
   $this->load->view('admin/projects/table_html', array('class'=>'projects-single-client'));
}
?>
