<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
			echo form_open($this->uri->uri_string(),array('id'=>'invoice-form','class'=>'_transaction_form invoice-form'));
			if(isset($invoice)){
				echo form_hidden('isedit');
			}
            ?>
            <input type="hidden" class="form-control" id="temp_id" name="tempid" value="">
            <input type="hidden" class="form-control" id="email_sub1" name="email_sub" value="">
            <input type="hidden" class="form-control" id="email_template_custom1" name="email_template_custom" value="">
            <input type="hidden" class="form-control" id="email1" name="email" value="">
			<div class="col-md-12">
				<?php $this->load->view('admin/invoices/invoice_template'); ?>
			</div>
			<?php echo form_close(); ?>
			<?php //$this->load->view('admin/invoice_items/item'); ?>
		</div>
	</div>
</div>
<div class="modal fade email-template" data-editor-id=".<?php echo 'tinymce-'.$invoice->id; ?>" id="invoice_send_to_customer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <?php //echo form_open('admin/proposals/send_to_email/'.$proposal->id); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close-send-template-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Select Templates to Send Invoice</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group" app-field-wrapper="email"><label for="email" class="control-label"> <small class="req text-danger">* </small>Email</label>
                            <input type="text" id="email" name="email" class="form-control ui-autocomplete-input" value="<?php echo $value = (isset($invoice) ? $invoice->email : ''); ?>" onkeyup="check_email(this.value,'email')"  multiple >
                            <input type='hidden' id='selectuser_ids' value="" />
                        </div>
                        <?php //echo render_input('cc','CC'); ?>
                        <label for="template" class="control-label">Choose Template</label>
                        <select name="tempid" id="tempid" class="form-control">
                        <?php foreach($all_templates as $val) { ?>
                            <option value="<?php echo $val['emailtemplateid']; ?>"><?php echo $val['name']; ?></option>
                        <?php } ?>
                        </select><br>
                        <label for="subject" class="control-label">Subject</label>
                        <input type="text" class="form-control" id="email_sub" name="email_sub" value="<?php echo $template->subject; ?>">
                        <br>
                        <label for="message" class="control-label">Message</label>
                        <?php echo render_textarea('email_template_custom','',$template->message,array(),array(),'','tinymce-'.$invoice->id); ?>
                        <?php echo form_hidden('template_name',$template_name); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-default close-send-template-modal"><?php echo _l('close'); ?></button> -->
                <input type="submit" id="btnSend" class="btn btn-info" value="Done">
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
	$(function(){
		//validate_invoice_form();
	    // Init accountacy currency symbol
	    init_currency();
	    // Project ajax search
	    init_ajax_project_search_by_customer_id();
	    // Lead ajax search
	    init_ajax_lead_search_by_customer_id();
	    // Maybe items ajax search
	    init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'items/search');
            
               
    $('body').on('change', '.invoice #lead_id', function() {
        var lead_id = $(this).selectpicker('val');
        if (lead_id !== '') {
            requestGetJSON('tasks/get_billable_tasks_by_lead/' + lead_id).done(function(tasks) {
                _init_tasks_billable_select(tasks, lead_id);
            });
        } 
    });
	});
    
    $(document).on('click', '#btnSend', function() {
        var email = $('#email').val();
        var subject = $('#email_sub').val();
        var temp_id = $('#tempid').val();
        var message = tinyMCE.get('email_template_custom').getContent();
        $('#invoice_send_to_customer').modal('hide');
        $('#email_template_custom1').val(message);
        $('#temp_id').val(temp_id);
        $('#email_sub1').val(subject);
        $('#email1').val(email);
        var form = $('form._transaction_form');
        form.append(hidden_input('save_and_send', 'true'));
        form.submit();
    });
      $('#tempid').on('change', function() {
          var temp_id = this.value;
          if(temp_id) {
              var url =  admin_url+'proposals/getTempDetails';
              //$('.followers-div').show();
              $.ajax({
                  type: "POST",
                  url: url,
                  data: {temp_id:temp_id},
                  dataType: 'json',
                  success: function(msg){
                    //if(msg.phone) {
                      
                      $('#email_sub').val(msg.subject);
                      $('#temp_id').val(msg.tempid);
                      tinyMCE.get('email_template_custom').setContent(msg.message);
                      //$('#email_template_custom').val(msg.message);
                  }
              });
          }
      });

      $( "#email" ).autocomplete({
                source: function(request, response) {
                    var cid = $('#clientid').val();
                    var relid = $('#rel_id').val();
                    var reltype = $('#rel_type').val();
                    var searchText = extractLast(request.term);
                    // if(relid == '') {
                    //     alert('please select deal to fetch email id.');
                    //     return false;
                    // }
                        $.ajax({
                            url: admin_url + 'proposals/get_related_email_ids',
                            type: 'post',
                            dataType: "json",
                            data: {
                                cid: cid,
                                search: searchText,
                                relid: relid,
                                reltype: reltype
                            },
                            success: function( data ) {
                                response( data );
                            }
                        });
                },
                select: function( event, ui ) {
                    var terms = split( $('#email').val() );

                    terms.pop();

                    terms.push( ui.item.value );
                    terms.push( "" );
                    $('#email').val(terms);
                    $('#email').val(terms.join( ", " ));

                    // Id
                    var terms = split( $('#selectuser_ids').val() );

                    terms.pop();
                    var req_out = $('#email').val();
                    req_out = ','+req_out;

                    //terms.push( req_out );
                    terms.push( ui.item.value );
                    terms.push( "" );
                    var trim = req_out.replace(/(^,)|(,$)/g, "");
                    //$('#selectuser_ids').val(terms.join( ", " ));
                    $('#email').val(trim);
                    $('#selectuser_ids').val(trim);

                    return false;
                },
                minLength: 3
        });

	$('body').on('change','#project_id', function() {
		$('#rel_id').val($(this).val());
		var project = $('#invoiceid').val();
		var clientid = $('#clientid').val();
		var rel_id = $('#project_id').val();
		var proj_exist = 0;
		// if(project.length == 0) {
		// 	$('#change_items').val(1)
		// 	$("#suptotaltxt").html('');
		// 	$("#suptotal").html('');
		// 	var url =  admin_url+'products/getemptyproduct';
		// 	$.ajax({
		// 		type: "POST",
		// 		url: url,
		// 		data: {rel_id:rel_id},
		// 		dataType: 'json',
		// 		success: function(msg){
		// 			var currency_selector = $("#currency1");
		// 			currency_selector.selectpicker('val',Number(msg.curId));
		// 			currency_selector.selectpicker('refresh');
					
		// 			$("#salesnotax").prop("checked", true);
		// 			$("#salesintax").prop("checked", false);
		// 			$("#salesextax").prop("checked", false);
		// 			if(msg.discount_value == 1 || msg.discount_option == 1) {
		// 				$('#topheading').html('<div class="col-md-3">Item</div><div class="col-md-2">Price</div><div class="col-md-2">Quantity</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
		// 			} else {
		// 				$('#topheading').html('<div class="col-md-3">Item</div><div class="col-md-2">Price</div><div class="col-md-2">Quantity</div><div class="col-md-2">Total</div>');
		// 			}
					
		// 			$('#product_index').val(1);
		// 			$('#method').val(1);
		// 			$('.field_product_wrapper').html(msg.html);
		// 			var sum = 0;
		// 			$("#stxt").html('<p>Subtotal</p>');
		// 			$('#stotal').html('<p>'+sum.toFixed(2)+'</p>');
		// 			$('#grandtotal').html(sum.toFixed(2));
		// 			$('#gtot').val(sum.toFixed(2));
		// 			$('input[name="project_cost"]').val(sum.toFixed(2));
		// 			$('input[name="project_cost"]').attr('readonly', true);
		// 		}
		// 	});
		// 	return false;
		// }
		if(rel_id > 0) {
			var url =  admin_url+'invoices/checkproducts';
			var changeitem = $('#change_items').val();
			$.ajax({
				type: "POST",
				url: url,
				data: {rel_id:rel_id,clientid:clientid,project:project},
				success: function(msg1){
					if(msg1 == 1 || changeitem == 1) {
						$('#change_items').val(1);
						var url =  admin_url+'products/getdealproduct';
						var datas = {project:rel_id};
					} else {
						var url =  admin_url+'products/getinvoiceproduct';
						var datas = {project:project};
					}
					productFields(url,datas);
            	}
          });
		} else {
			var currency = $('#currency1').val();
			var project = $('#prject_id').val();
			var length = $('#product_index').val();
			var item_type = $('#item_for').val();
			length = parseInt(length)+parseInt(1);
			if(project.length == 0) {
				var url =  admin_url+'products/getdealproduct';
				var datas = {project:rel_id};
			}
			if(project.length > 0) {
				$('#change_items').val(0);
				var url =  admin_url+'products/getinvoiceproduct';
				var datas = {project:project};
			}
			productFields(url,datas);
		}
    
   });
    
	function productFields(url,datas) {
		$("#suptotaltxt").html('');
					$("#suptotal").html('');
					$.ajax({
                        type: "POST",
                        url: url,
                        data: datas,
                        dataType: 'json',
                        success: function(msg){
                          var currency_selector = $("#currency1");
                          currency_selector.selectpicker('val',Number(msg.curId));
                          currency_selector.selectpicker('refresh');
                          
                            if(msg.methode == "1") {
                                $("#salesnotax").prop("checked", true);
                                $("#salesintax").prop("checked", false);
                                $("#salesextax").prop("checked", false);
                                if(msg.discount_value == 1 || msg.discount_option == 1) {
                                    $('#topheading').html('<div class="col-md-3">Item</div><div class="col-md-2">Price</div><div class="col-md-2">Quantity</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
                                } else {
                                    $('#topheading').html('<div class="col-md-3">Item</div><div class="col-md-2">Price</div><div class="col-md-2">Quantity</div><div class="col-md-2">Total</div>');
                                }
                                
                                $('#product_index').val(msg.productscnt);
                                $('#method').val(1);
                                $('.field_product_wrapper').html(msg.html);
                                var sum = 0;
                                var inps = document.getElementsByName('total[]');
                                for (var i = 0; i <inps.length; i++) {
                                    var inp=inps[i];
                                    if(inp.value)
                                        sum = parseFloat(sum)+parseFloat(inp.value);
                                }
                                //Discount
                                var prical = document.getElementsByName('price[]');
                                var quancal = document.getElementsByName('qty[]');
                                var disc_txt = '';
                                var discal = document.getElementsByName('discount[]');
                                for (var i = 0; i < discal.length; i++) {
                                    var dis=discal[i];
                                    if(dis.value && dis.value > 0) {
                                        var inp = inps[i];
                                        var pri = prical[i];
                                        var quanc = quancal[i];
                                        var totc = (pri.value * quanc.value);
                                        var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                                        var mult1 = totc * dec1; // gives the value for subtract from main value
                                        disc_txt += ' '+mult1.toFixed(2)+',';
                                    }
                                }
                                if(disc_txt != '') {
                                    disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                                    $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
                                } else {
                                    $("#stxt").html('<p>Subtotal</p>');
                                }
                                $('#stotal').html('<p>'+sum.toFixed(2)+'</p>');
                                $('#grandtotal').html(sum.toFixed(2));
                                $('#gtot').val(sum.toFixed(2));
                                $('input[name="project_cost"]').val(sum.toFixed(2));
                                $('input[name="project_cost"]').attr('readonly', true);
                            }
                            if(msg.methode == "2") {
                                $("#salesnotax").prop("checked", false);
                                $("#salesintax").prop("checked", true);
                                $("#salesextax").prop("checked", false);
                                $('#product_index').val(msg.productscnt);
                                $('#method').val(2);
                                $('.field_product_wrapper').html(msg.html);
                                var sum = 0;
                                var inps = document.getElementsByName('total[]');
                                for (var i = 0; i <inps.length; i++) {
                                    var inp=inps[i];
                                    if(inp.value)
                                        sum = parseFloat(sum)+parseFloat(inp.value);
                                }
                                $('#grandtotal').html(sum.toFixed(2));
                                $('#gtot').val(sum.toFixed(2));
                                $('input[name="project_cost"]').val(sum.toFixed(2));
                                $('input[name="project_cost"]').attr('readonly', true);

                                var method = msg.methode;
                                //Discount
                                var prical = document.getElementsByName('price[]');
                                var quancal = document.getElementsByName('qty[]');
                                var disc_txt = '';
                                var discal = document.getElementsByName('discount[]');
                                for (var i = 0; i < discal.length; i++) {
                                    var dis=discal[i];
                                    if(dis.value && dis.value > 0) {
                                        var inp=inps[i];
                                        var pri = prical[i];
                                        var quanc = quancal[i];
                                        var totc = (pri.value * quanc.value);
                                        var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                                        var mult1 = totc * dec1; // gives the value for subtract from main value
                                        disc_txt += ' '+mult1.toFixed(2)+',';
                                    }
                                }
                                if(disc_txt != '') {
                                    disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                                    $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
                                } else {
                                    $("#stxt").html('<p>Subtotal</p>');
                                }
                                $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');    
                                if(msg.methode == "2") {
                                    if(msg.discount_value == 1 || msg.discount_option == 1) {
                                        $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
                                    } else {
                                        $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Total</div>');
                                    }
                                    
                                    //$("#stxt").html('<p>Subtotal</p>');
                                    $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                                    //$('#'+index+' input[name="tax[]"]').val('');
                                    for (var index = 0; index <inps.length; index++) {
                                        var taxvalue = $('#'+index+' input[name="tax[]"]').val();
                                        var tprice = $('#'+index+' input[name="total[]"]').val();
                                        //var total = value*price;
                                        if(taxvalue) {
                                            var taxprice = (tprice * taxvalue) / 100;
                                            
                                            //if($('.txt_'+index).length == 0) {
                                                $('#suptotaltxt').append('<p class="txt_'+index+'">Includes Tax ('+taxvalue+'%)</p>');
                                                $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
                                            // } else {
                                            //     $('.txt_'+index).html('Includes Tax ('+taxvalue+'%)');
                                            //     $('.amt_'+index).html(taxprice.toFixed(2));
                                            // }
                                        }
                                    }
                                    $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                                    $('#grandtotal').html(sum.toFixed(2));
                                    $('#gtot').val(sum.toFixed(2));
                                }
                            }
                            if(msg.methode == "3") {
                                $("#salesnotax").prop("checked", false);
                                $("#salesintax").prop("checked", false);
                                $("#salesextax").prop("checked", true);
                                $('#product_index').val(msg.productscnt);
                                $('#method').val(3);
                                $('.field_product_wrapper').html(msg.html);
                                var sum = 0;
                                var inps = document.getElementsByName('total[]');
                                for (var i = 0; i <inps.length; i++) {
                                    var inp=inps[i];
                                    if(inp.value)
                                        sum = parseFloat(sum)+parseFloat(inp.value);
                                }
                                $('#grandtotal').html(sum.toFixed(2));
                                $('#gtot').val(sum.toFixed(2));
                                $('input[name="project_cost"]').val(sum.toFixed(2));
                                $('input[name="project_cost"]').attr('readonly', true);

                                var method = msg.methode;
                                //Discount
                                var prical = document.getElementsByName('price[]');
                                var quancal = document.getElementsByName('qty[]');
                                var disc_txt = '';
                                var discal = document.getElementsByName('discount[]');
                                for (var i = 0; i < discal.length; i++) {
                                    var dis=discal[i];
                                    if(dis.value && dis.value > 0) {
                                        var inp=inps[i];
                                        var pri = prical[i];
                                        var quanc = quancal[i];
                                        var totc = (pri.value * quanc.value);
                                        var dec1 = (dis.value / 100).toFixed(2); //its convert 10 into 0.10
                                        var mult1 = totc * dec1; // gives the value for subtract from main value
                                        disc_txt += ' '+mult1.toFixed(2)+',';
                                    }
                                }
                                if(disc_txt != '') {
                                    disc_txt = '(Includes discount of'+disc_txt.slice(0, -1)+')';
                                    $("#stxt").html('<p>Subtotal <small>'+disc_txt+'</small></p>');
                                }
                                if(msg.methode == "3") {
                                    if(msg.discount_value == 1 || msg.discount_option == 1) {
                                        $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
                                    } else {
                                        $('#topheading').html('<div class="col-md-2" style="width:20%;">Item</div><div class="col-md-2">Price</div><div class="col-md-1">Quantity</div><div class="col-md-2">Tax</div><div class="col-md-2">Total</div>');
                                    }
                                    
                                    //$('#'+index+' input[name="tax[]"]').val('');
                                    for (var index = 0; index <inps.length; index++) {
                                        var taxvalue = $('#'+index+' input[name="tax[]"]').val();
                                        var tprice = $('#'+index+' input[name="total[]"]').val();
                                        //var total = value*price;
                                        if(taxvalue) {
                                            var taxprice = (tprice * taxvalue) / 100;

                                            //$("#stxt").html('<p>Subtotal</p>');
                                            $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                                            //if($('.txt_'+index).length == 0) {
                                                $('#suptotaltxt').append('<p class="txt_'+index+'">Excludes Tax ('+taxvalue+'%)</p>');
                                                $('#suptotal').append('<p class="amt_'+index+'">'+taxprice.toFixed(2)+'</p>');
                                            // } else {
                                            //     $('.txt_'+index).html('Excludes Tax ('+taxvalue+'%)');
                                            //     $('.amt_'+index).html(taxprice.toFixed(2));
                                            // }
                                        }
                                    }
                                    $("#stotal").html('<p>'+sum.toFixed(2)+'</p>');
                                    var taxpr = document.getElementsByName('tax[]');
                                    var tosumtpr = 0;
                                    for (var i = 0; i <taxpr.length; i++) {
                                        var tp=taxpr[i];
                                        var inp=inps[i];
                                        if(tp.value) {
                                            var tottax = (inp.value * tp.value) / 100;
                                            tosumtpr = parseFloat(tosumtpr)+parseFloat(tottax)+parseFloat(inp.value);
                                        } else {
                                            if(tp.value) {
                                                tosumtpr = parseFloat(tosumtpr)+parseFloat(inp.value);
                                            }
                                        }
                                    }
                                    $('#grandtotal').html(tosumtpr.toFixed(2));
                                    $('#gtot').val(tosumtpr.toFixed(2));
                                }
                            }
                        }
                    });
	}
    function check_email(a,c_id){
        var req_val = $('#'+c_id).val();
        var newStr = req_val.substring(0, req_val.length - 1);
        var check_str = newStr.substring(newStr.length-4);
        var cur_val = a.substr(a.length - 1);
        var e = event.keyCode;
        if((check_str.includes(".com") || check_str.includes(".net") || check_str.includes(".in")) && (e!=8) && e!=188){
          var req_out = newStr+','+ cur_val;
          $('#'+c_id).val(req_out);
        }
    }
    function split( val ) {
     console.log(val);
      return val.split( /,\s*/ );
    }
    function extractLast( term ) {
      return split( term ).pop();
    }


</script>
<style>
.ui-autocomplete {
    position: absolute;
    top: 0;
    left: 0;
    cursor: default;
}
.ui-autocomplete {
   
   z-index:99999  !important;
}
</style>

</body>
</html>
