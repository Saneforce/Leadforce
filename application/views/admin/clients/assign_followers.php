<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
.followers-div, .addfollower_btn {
  display:none;
}
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-6">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="no-margin">
                <?php echo $title; ?>
            </h4>
            <hr class="hr-panel-heading" />
            <form action="" method="post" >
            <?php echo form_open($this->uri->uri_string(),array('id'=>'assign_followers_form')); ?>
              <input type="hidden" name="id" value="">
              <div class="col-md-12 pipeselect">
                <div class="form-group select-placeholder contactid input-group-select">
                  <label class="control-label">Select Follower</label>
                  <div class="dropdown bootstrap-select emp_id input-group-select show-tick bs3 bs3-has-addon" style="width: 100%;">
                    <select id="emp_id" name="emp_id" class="emp_id selectpicker" data-actions-box="1" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98" required>
                      <option value="">Nothing Selected</option>
                      <?php
                          if(isset($employees)){
                              foreach($employees as $emp){
                                  echo '<option value="'.$emp['staffid'].'" >'.$emp['firstname'].'</option>';
                              }
                          }
                      ?>
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label"><?php echo _l('selectoptions'); ?></label>
                      <select id="assign" name="assign" class="form-control">
                        <option value="" >Nothing Selected</option>
                        <option value="1" >Assign Manually</option>
                        <option value="2" >View All</option>
                        <option value="3" >Edit All</option>
                      </select>
                </div>
              </div>
              <div class="col-md-12">
                    <div class="form-group followers-div">
                        <label for="unit_price" class="control-label"><h4>Add Employees</h4></label>
                        <input type="hidden" id="product_index" value="0">
                        <div class="field_follower_wrapper row">
                        </div>
                      </div>
                      <a href="javascript:void(0);" class="addfollower_btn row" title="Add field" style="position:relative; top:5px; right:15px; clear:both; float:right; height:40px;"><i class="fa fa-plus"></i> Add More</a>
              </div>

              <div class="col-md-6">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                <button type="submit" class="btn btn-primary" onclick="return savefollowers();">Save</button>
              </div>
              <?php echo form_close(); ?>
</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
<?php $this->load->view('admin/clients/client_group'); ?>
<?php init_tail(); ?>
<script>
 $(function(){
  $('select#assign').on('change', function() {
        var assign = this.value;
        var emp_id = $('#emp_id').val();
        if(emp_id) {
          if(assign == 1) {
            var url =  admin_url+'AssignFollowers/geteditfollowerfields';
            $.ajax({
                type: "POST",
                url: url,
                data: {assign:assign,emp_id,emp_id},
                dataType: 'json',
                success: function(msg){
                  console.log(msg.html);
                  if(msg.html) {
                    $('.followers-div').show();
                    $(".field_follower_wrapper").html('');
                    $(".field_follower_wrapper").append(msg.html);
                    $('.addfollower_btn').show();
                    if(msg.option) {
                      $('select#assign>option[value="'+msg.option+'"]').prop('selected', true);
                    }
                    if(msg.cnt) {
                      $('#product_index').val(msg.cnt);
                    }
                  } else {
                    $(".field_follower_wrapper").html('');
                    $('.followers-div').hide();
                    $('.addfollower_btn').hide();
                  }
                }
            });
          } else {
            $(".field_follower_wrapper").html('');
            $('.followers-div').hide();
            $('.addfollower_btn').hide();
          }
        } else {
          alert("Please Select Employee");
          $('select#assign>option[value=""]').prop('selected', true);
          return false;
        }
    });

    $('select#emp_id').on('change', function() {
        var emp_id = this.value;
        if(emp_id) {
            var url =  admin_url+'AssignFollowers/getfollowerdetails';
            //$('.followers-div').show();
            $.ajax({
                type: "POST",
                url: url,
                data: {emp_id,emp_id},
                dataType: 'json',
                success: function(msg){
                  console.log(msg.html);
                  if(msg.html) {
                    $('.followers-div').show();
                    $(".field_follower_wrapper").html('');
                    $(".field_follower_wrapper").append(msg.html);
                    $('.addfollower_btn').show();
                    if(msg.option) {
                      $('select#assign>option[value="'+msg.option+'"]').prop('selected', true);
                    }
                    if(msg.cnt) {
                      $('#product_index').val(msg.cnt);
                    }
                  } else {
                    if(msg.option) {
                      $('select#assign>option[value="'+msg.option+'"]').prop('selected', true);
                      $(".field_follower_wrapper").html('');
                      $('.followers-div').hide();
                      $('.addfollower_btn').hide();
                    }
                    if(!msg.option) {
                      $('select#assign>option[value=""]').prop('selected', true);
                      $(".field_follower_wrapper").html('');
                      $('.followers-div').hide();
                      $('.addfollower_btn').hide();
                    }
                  }
                }
            });
        }
    });

 });
 function savefollowers() {
    emp_id = $('#assign').val();
    if(emp_id) {
      return true;
    } else {
      if(confirm("Are you sure do you want to remove followers?")){
        return true;
      }
      else{
          return false;
      }
    }
    
    alert(emp_id);
    return false;
  }
</script>
</body>
</html>
