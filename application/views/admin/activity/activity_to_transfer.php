<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
.followers-div, .addfollower_btn, #rollback {
  display:none;
}

/* Absolute Center Spinner */
#overlay {
  position: fixed;
  z-index: 999;
  overflow: show;
  margin: auto;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 50px;
  height: 50px;
}

/* Transparent Overlay */
#overlay:before {
  content: '';
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255,255,255,0.5);
}

/* :not(:required) hides these rules from IE9 and below */
#overlay:not(:required) {
  /* hide "loading..." text */
  font: 0/0 a;
  color: transparent;
  text-shadow: none;
  background-color: transparent;
  border: 0;
}

#overlay:not(:required):after {
  content: '';
  display: block;
  font-size: 10px;
  width: 50px;
  height: 50px;
  margin-top: -0.5em;

  border: 3px solid rgba(33, 150, 243, 1.0);
  border-radius: 100%;
  border-bottom-color: transparent;
  -webkit-animation: spinner 1s linear 0s infinite;
  animation: spinner 1s linear 0s infinite;


}

/* Animation */

@-webkit-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-moz-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-o-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

@-webkit-keyframes rotation {
   from {-webkit-transform: rotate(0deg);}
   to {-webkit-transform: rotate(359deg);}
}
@-moz-keyframes rotation {
   from {-moz-transform: rotate(0deg);}
   to {-moz-transform: rotate(359deg);}
}
@-o-keyframes rotation {
   from {-o-transform: rotate(0deg);}
   to {-o-transform: rotate(359deg);}
}
@keyframes rotation {
   from {transform: rotate(0deg);}
   to {transform: rotate(359deg);}
}

</style>
<div id="wrapper">
<div id="overlay" style="display:none"><div class="spinner"></div></div>
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="no-margin">
                <?php echo $title; ?>
            </h4>
            <hr class="hr-panel-heading" />
            <div class="col-md-6 row">
            <form action="" method="post" id="accountTransfer">
              <div class="col-md-12 pipeselect">
                <div class="form-group">
                  <label class="control-label"><?php echo _l('From'); ?></label>
                  <div class="dropdown bootstrap-select" style="width: 100%;">
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

              <div class="col-md-12 assignemp">
                <div class="form-group">
                  <label class="control-label"><?php echo _l('To'); ?></label>
                  <div class="dropdown bootstrap-select" style="width: 100%;">
                    <select id="assign" name="assign" class="selectpicker" data-actions-box="1" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98" required>
                      <option value="">Nothing Selected</option>
                    </select>
                  </div>
                </div>
              </div>
              
              
              <div class="col-md-6">
                <input type="hidden" id="rollback_id" value="">
                <input type="hidden" name="action" value="Transfer">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                <button type="submit" value="Transfer" class="btn btn-primary" onclick="return transferall();">Transfer</button>
                <button type="submit" id="rollback" name="action" value="Rollback" class="btn btn-primary" onclick="return transferall();">Rollback</button>
              </div>
          </form>
                        </div>
          
        
        
   
      </div>
    </div>
  </div>
  </div>
  </div>
  <div class="modal" id="confirmModal" style="display: none; z-index: 1050;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body" id="confirmMessage">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="confirmOk">Yes</button>
              <button type="button" class="btn btn-default" id="confirmCancel">Cancel</button>
            </div>
      </div>
    </div>
  </div>
  
<?php $this->load->view('admin/activity/activity_group.php'); ?>
<?php init_tail(); ?>
<script>
 $(function(){
  
    $('#confirmCancel').on('click', function() {
      document.getElementById('overlay').style.display = 'none';
      $("#confirmModal").modal("hide");
    });
    $('select#assign').on('change', function() {
      var emp_id = this.value;
      var rollbackId = $('#rollback_id').val();
      if(emp_id == rollbackId) {
        $('#rollback').show();
      } else {
        $('#rollback').hide();
      }
    });
    $('select#emp_id').on('change', function() {
        var emp_id = this.value;
        if(emp_id) {
            var url =  admin_url+'activity_transfer/getToEmployees';
            //$('.followers-div').show();
            $.ajax({
                type: "POST",
                url: url,
                data: {emp_id:emp_id},
                dataType: 'json',
                success: function(msg){
                  console.log(msg.html);
                  if(msg.html) {
                    // $('#categoryid').selectpicker('refresh');
                    // $('.categoryiddiv div.filter-option-inner-inner').html(msg.category)
                    $("select#assign").empty().append(msg.html);
                    $('#assign').selectpicker('refresh');
                    // if(msg.rollback) {
                    //   $('#rollback').show();
                    //   $('#rollback_id').val(msg.rollback_id);
                    // } else {
                    //   $('#rollback').hide();
                    //   $('#rollback_id').val('');
                    // }
                  } else {
                    $("select#assign").empty().append('<option value="">Nothing Selected</option>');
                    // $('#rollback').hide();
                    // $('#rollback_id').val('');
                  }
                }
            });
        }
    });

 });

 function rollback(id) {
  if (confirm('Are you sure, do you want to Roll Back?')) {
    document.getElementById('overlay').style.display = '';
      $.ajax({
          type: 'POST',
          url: admin_url + 'activity_transfer/rollback',
          data: {id:id},
          dataType: 'json',
          success: function(msg){
              if(msg.message) {
                document.getElementById('overlay').style.display = 'none';
                  alert_float('success', msg.message);
              } else {
                document.getElementById('overlay').style.display = 'none';
                  alert_float('warning', msg.err);
              }
              location.reload();
          }
      });
  } else {
      return false;
  }
 }

 function transferall() {
    emp_id = $('#emp_id').val();
    assign = $('#assign').val();
    if(emp_id) {
      document.getElementById('overlay').style.display = '';
      var url =  admin_url+'activity_transfer/getTransferDetails';
      //$('.followers-div').show();
      $.ajax({
          type: "POST",
          url: url,
          data: {emp_id:emp_id, assign:assign},
          dataType: 'json',
          success: function(msg){
            console.log(msg.html);
            if(msg.html) {
              confirmDialog(msg.html, function(){
                $('form#accountTransfer').submit();
              });
            } else {
              alert('Please Select the Account you want to Transfer.');
              return false;
            }
          }
      });
    } else {
        alert('Please Select the Account you want to Transfer.');
        return false;
    }
    return false;
  }

  function confirmDialog(message, onConfirm){
      var fClose = function(){
          modal.modal("hide");
      };
      var modal = $("#confirmModal");
      modal.modal("show");
      $("#confirmMessage").empty().append(message);
      $("#confirmOk").unbind().one('click', onConfirm).one('click', fClose);
      //$("#confirmCancel").unbind().one("click", fClose);
  }
</script>
</body>
</html>
