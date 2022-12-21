<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">
        <?php echo _l('add_new',_l('lead_lowercase'));?>
    </h4>
</div>

<div class="modal-body">
    <?php $this->load->view('admin/leads/profile'); ?>
</div>