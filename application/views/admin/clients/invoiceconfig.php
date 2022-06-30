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
            <form action="" method="post" id="invoicepdf" enctype="multipart/form-data">
              <div class="col-md-12 ">
                <div class="form-group">
                  <label for="inv_logo" class="control-label">Invoice Logo<?php //echo _l('company_logo_dark'); ?></label>
                  <input type="file" name="inv_logo" class="form-control" value="" data-toggle="tooltip" title="<?php echo _l('settings_general_company_logo_tooltip'); ?>">
                  <?php if($invoice->inv_logo) { ?>
                    <img src="<?php echo base_url($invoice->inv_logo); ?>">
                  <?php } ?>
                </div>
              </div>

              <div class="col-md-12 ">
                <div class="form-group">
                  <label for="signature" class="control-label"><?php echo _l('signature'); ?></label>
                  <div class="signature-pad--body" style="border:1px solid #ccc;">
                    <canvas id="signature" height="130" width="550"></canvas>
                  </div>
                  <input type="text" style="width:1px; height:1px; border:0px;" tabindex="-1" name="signature" id="signatureInput">
                  <div class="dispay-block">
                    <button type="button" class="btn btn-default btn-xs clear" tabindex="-1" data-action="clear"><?php echo _l('clear'); ?></button>
                    <button type="button" class="btn btn-default btn-xs" tabindex="-1" data-action="undo"><?php echo _l('undo'); ?></button>
                  </div>
                  <?php if($invoice->signature) { ?>
                    <img src="<?php echo base_url($invoice->signature); ?>">
                  <?php } ?>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label">Terms & Conditions</label>
                  <div class="dropdown bootstrap-select" style="width: 100%;">
                  <input type="text" name="tc" class="form-control" value="<?php echo $invoice->tc; ?>" >
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label">Contact Details</label>
                  <div class="dropdown bootstrap-select" style="width: 100%;">
                    <textarea name="contact_details" class="form-control" style="height:100px;"><?php echo $invoice->contact_details; ?></textarea>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                    <label for="use_as_default" class="control-label clearfix">
                        Use as Default        </label>
                    <div class="radio radio-primary radio-inline">
                        <input type="radio" id="y_opt_1_use_as_default" name="use_as_default" value="1" <?php echo (($invoice->use_as_default == 1) ? 'checked':'');?> >
                        <label for="y_opt_1_use_as_default">
                            Yes            </label>
                    </div>
                    <div class="radio radio-primary radio-inline">
                            <input type="radio" id="y_opt_2_use_as_default" name="use_as_default" value="0" <?php echo (($invoice->use_as_default == 0) ? 'checked':'');?> >
                            <label for="y_opt_2_use_as_default">
                                No                </label>
                    </div>
                </div>
              </div>

              <div class="col-md-12">
                <button type="submit" name="submit" value="Save" class="btn btn-primary">Save</button>
              </div>
          </form>
                        </div>
    </div>
  </div>
  </div>
  </div>
  
  
<?php $this->load->view('admin/clients/client_group'); ?>
<?php init_tail(); ?>
<?php
 // $this->app_scripts->theme('signature-pad','assets/plugins/signature-pad/signature_pad.min.js');
?>
<script type="text/javascript" id="signature-pad" src="../assets/plugins/signature-pad/signature_pad.min.js?v=2.4.0"></script>
<script>
  $(function(){
   SignaturePad.prototype.toDataURLAndRemoveBlanks = function() {
     var canvas = this._ctx.canvas;
       // First duplicate the canvas to not alter the original
       var croppedCanvas = document.createElement('canvas'),
       croppedCtx = croppedCanvas.getContext('2d');

       croppedCanvas.width = canvas.width;
       croppedCanvas.height = canvas.height;
       croppedCtx.drawImage(canvas, 0, 0);

       // Next do the actual cropping
       var w = croppedCanvas.width,
       h = croppedCanvas.height,
       pix = {
         x: [],
         y: []
       },
       imageData = croppedCtx.getImageData(0, 0, croppedCanvas.width, croppedCanvas.height),
       x, y, index;

       for (y = 0; y < h; y++) {
         for (x = 0; x < w; x++) {
           index = (y * w + x) * 4;
           if (imageData.data[index + 3] > 0) {
             pix.x.push(x);
             pix.y.push(y);

           }
         }
       }
       pix.x.sort(function(a, b) {
         return a - b
       });
       pix.y.sort(function(a, b) {
         return a - b
       });
       var n = pix.x.length - 1;

       w = pix.x[n] - pix.x[0];
       h = pix.y[n] - pix.y[0];
       var cut = croppedCtx.getImageData(pix.x[0], pix.y[0], w, h);

       croppedCanvas.width = w;
       croppedCanvas.height = h;
       croppedCtx.putImageData(cut, 0, 0);

       return croppedCanvas.toDataURL();
     };


     function signaturePadChanged() {

       var input = document.getElementById('signatureInput');
       var $signatureLabel = $('#signatureLabel');
       $signatureLabel.removeClass('text-danger');

       if (signaturePad.isEmpty()) {
         $signatureLabel.addClass('text-danger');
         input.value = '';
         return false;
       }

       $('#signatureInput-error').remove();
       var partBase64 = signaturePad.toDataURLAndRemoveBlanks();
       partBase64 = partBase64.split(',')[1];
       input.value = partBase64;
     }

     var canvas = document.getElementById("signature");
     var clearButton = wrapper.querySelector("[data-action=clear]");
     var undoButton = wrapper.querySelector("[data-action=undo]");
     var identityFormSubmit = document.getElementById('identityConfirmationForm');

     var signaturePad = new SignaturePad(canvas, {
      maxWidth: 2,
      onEnd:function(){
        signaturePadChanged();
      }
    });

     clearButton.addEventListener("click", function(event) {
       signaturePad.clear();
       signaturePadChanged();
     });

     undoButton.addEventListener("click", function(event) {
       var data = signaturePad.toData();
       if (data) {
           data.pop(); // remove the last dot or line
           signaturePad.fromData(data);
           signaturePadChanged();
         }
       });

     $('#identityConfirmationForm').submit(function() {
       signaturePadChanged();
     });
   });
 </script>
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
            var url =  admin_url+'AccountTransfer/getToEmployees';
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
          url: admin_url + 'AccountTransfer/rollback',
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
      var url =  admin_url+'AccountTransfer/getTransferDetails';
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
