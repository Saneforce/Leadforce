<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content"> 
    <div class="row">
     <?php if ($this->session->flashdata('debug')) {
        ?>
       <div class="col-lg-12">
        <div class="alert alert-warning">
         <?php echo $this->session->flashdata('debug'); ?>
       </div>
     </div>
   <?php
    } ?>
  <?php /* <div class="col-md-3">
		<ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked">
			<li class='active'>
				<a href="<?php echo admin_url('target/'); ?>" data-group="target">Company Level Target View</a>
			</li>
			<li >
				<a href="<?php echo admin_url('target/deal'); ?>" data-group="target">Target For Deal</a>
			</li>
			<li >
				<a href="<?php echo admin_url('target/'); ?>" data-group="target">Target For Activity</a>
			</li>
      </ul>
       
  </div>*/?>
  <div class="col-md-12">
      <?php echo $tab_view;?>
</div>
<div class="clearfix"></div>
</div>
<?php echo form_close(); ?>
<div class="btn-bottom-pusher"></div>
</div>
</div>
<?php init_tail(); ?>
</body>
</html>
