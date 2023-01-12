<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
  <?php
  $alertclass = "";
  if($this->session->flashdata('message-success')){
    $alertclass = "success";
  } else if ($this->session->flashdata('message-warning')){
    $alertclass = "warning";
  } else if ($this->session->flashdata('message-info')){
    $alertclass = "info";
  } else if ($this->session->flashdata('message-danger')){
    $alertclass = "danger";
  }
  if($this->session->flashdata('message-'.$alertclass)){ ?>
    
      <p class="text-center text-<?php echo $alertclass; ?>"style="font-size: 16px;">
        <?php
        echo $this->session->flashdata('message-'.$alertclass);
        ?>
    </p>
  <?php } ?>
