<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade proposal-convert-modal" id="convert_to_invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xxl" role="document">
        <?php echo form_open('admin/proposals/convert_to_invoice/'.$proposal->id,array('id'=>'proposal_convert_to_invoice_form','class'=>'_transaction_form invoice-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="close_modal_manually('#convert_to_invoice')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('proposal_convert_to_invoice'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php $this->load->view('admin/invoices/invoice_template'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default invoice-form-submit save-as-draft transaction-submit">
                    <?php echo _l('save_as_draft'); ?>
                </button>
                <button class="btn btn-info invoice-form-submit transaction-submit">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php $this->load->view('admin/invoice_items/item'); ?>

<?php //init_tail(); ?>
<script>
function close_modal_manually() {
    location.reload();
}

    init_ajax_search('customer','#clientid.ajax-search');
    init_ajax_search('project', '#rel_id.ajax-search');
    init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'items/search');
    custom_fields_hyperlink();
    init_selectpicker();
    init_tags_inputs();
    init_datepicker();
    init_color_pickers();
    init_items_sortable();
    validate_invoice_form('#proposal_convert_to_invoice_form');
   
    var _rel_id = $('#rel_id'),
   _rel_type = $('#rel_type'),
   _rel_id_wrapper = $('#rel_id_wrapper'),
   data = {};
   $('body').on('change','#rel_id', function() {
     if($(this).val() != ''){
      $.get(admin_url + 'proposals/get_relation_data_values/' + $(this).val() + '/' + _rel_type.val(), function(response) {
        $('input[name="proposal_to"]').val(response.to);
        $('textarea[name="address"]').val(response.address);
        $('input[name="email"]').val(response.email);
        $('input[name="phone"]').val(response.phone);
        $('input[name="city"]').val(response.city);
        $('input[name="state"]').val(response.state);
        $('input[name="zip"]').val(response.zip);
        $('select[name="country"]').selectpicker('val',response.country);
        var currency_selector = $('#currency');
        if(_rel_type.val() == 'customer'){
          if(typeof(currency_selector.attr('multi-currency')) == 'undefined'){
            currency_selector.attr('disabled',true);
          }

         } else {
           currency_selector.attr('disabled',false);
        }
        var proposal_to_wrapper = $('[app-field-wrapper="proposal_to"]');
        if(response.is_using_company == false && !empty(response.company)) {
          proposal_to_wrapper.find('#use_company_name').remove();
          proposal_to_wrapper.find('#use_company_help').remove();
          proposal_to_wrapper.append('<div id="use_company_help" class="hide">'+response.company+'</div>');
          proposal_to_wrapper.find('label')
          .prepend("<a href=\"#\" id=\"use_company_name\" data-toggle=\"tooltip\" data-title=\"<?php echo _l('use_company_name_instead'); ?>\" onclick='document.getElementById(\"proposal_to\").value = document.getElementById(\"use_company_help\").innerHTML.trim(); this.remove();'><i class=\"fa fa-building-o\"></i></a> ");
        } else {
          proposal_to_wrapper.find('label #use_company_name').remove();
          proposal_to_wrapper.find('label #use_company_help').remove();
        }
       /* Check if customer default currency is passed */
       if(response.currency){
         currency_selector.selectpicker('val',response.currency);
       } else {
        /* Revert back to base currency */
        currency_selector.selectpicker('val',currency_selector.data('base'));
      }
      currency_selector.selectpicker('refresh');
      currency_selector.change();
    }, 'json');
    }
    var rel_id = $('#rel_id').val();
    var rel_type = $('#rel_type').val();
    var project = $('#prject_id').val();
    $('#projid').val(rel_id);
    var proj_exist = 0;
    if(rel_type != 'project' && project.length == 0) {
      $('#change_items').val(1)
      $("#suptotaltxt").html('');
      $("#suptotal").html('');
      var url =  admin_url+'products/getemptyproduct';
        $.ajax({
            type: "POST",
            url: url,
            data: {rel_id:rel_id},
            dataType: 'json',
            success: function(msg){
                  var currency_selector = $("#currency1");
                  currency_selector.selectpicker('val',Number(msg.curId));
                  currency_selector.selectpicker('refresh');
                  
                  $("#salesnotax").prop("checked", true);
                  $("#salesintax").prop("checked", false);
                  $("#salesextax").prop("checked", false);
                  if(msg.discount_value == 1 || msg.discount_option == 1) {
                      $('#topheading').html('<div class="col-md-3">Item</div><div class="col-md-2">Price</div><div class="col-md-2">Quantity</div><div class="col-md-2">Discount %</div><div class="col-md-2">Total</div>');
                  } else {
                      $('#topheading').html('<div class="col-md-3">Item</div><div class="col-md-2">Price</div><div class="col-md-2">Quantity</div><div class="col-md-2">Total</div>');
                  }
                  
                  $('#product_index').val(1);
                  $('#method').val(1);
                  $('.field_product_wrapper').html(msg.html);
                  var sum = 0;
                  $("#stxt").html('<p>Subtotal</p>');
                  $('#stotal').html('<p>'+sum.toFixed(2)+'</p>');
                  $('#grandtotal').html(sum.toFixed(2));
                  $('#gtot').val(sum.toFixed(2));
                  $('input[name="project_cost"]').val(sum.toFixed(2));
                  $('input[name="project_cost"]').attr('readonly', true);
            }
        });
        return false;
    }
    if(rel_type == 'project' && project.length > 0) {
        var url =  admin_url+'proposals/checkproducts';
        var changeitem = $('#change_items').val();
        $.ajax({
            type: "POST",
            url: url,
            data: {rel_id:rel_id,rel_type:rel_type,project:project},
            success: function(msg1){
               if(rel_type == 'project' && (msg1 == 1 || changeitem == 1)) {
                    $('#change_items').val(1);
                    var url =  admin_url+'products/getdealproduct';
                    var datas = {project:rel_id};
                } else {
                    var url =  admin_url+'products/getpropsalproduct';
                    var datas = {project:project};
                }
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
          });
    } else {
        var currency = $('#currency1').val();
        var project = $('#prject_id').val();
        var length = $('#product_index').val();
        var item_type = $('#item_for').val();
        length = parseInt(length)+parseInt(1);
      if(rel_type == 'project' && project.length == 0) {
        var url =  admin_url+'products/getdealproduct';
        var datas = {project:rel_id};
      }
      if(rel_type != 'project' && project.length > 0) {
        $('#change_items').val(0);
        var url =  admin_url+'products/getpropsalproduct';
        var datas = {project:project};
      }
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
    
   });

</script>

<style>
.ui-autocomplete {
    position: absolute;
    top: 0;
    left: 0;
    cursor: default;
	z-index:1050 !important;
}

</style>