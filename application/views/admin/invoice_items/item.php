<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="sales_item_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('invoice_item_edit_heading'); ?></span>
                    <span class="add-title"><?php echo _l('invoice_item_add_heading'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/invoice_items/manage',array('id'=>'invoice_item_form','onsubmit'=>'ch_val()')); ?>
            <?php echo form_hidden('itemid'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php /*<div class="alert alert-warning affect-warning hide">
                            <?php echo _l('changing_items_affect_warning'); ?>
                        </div>*/?>
						<?php $attrs = array('onblur'=>"check_name(this.value,'name')"); ?>
                        <?php echo render_input( 'name', 'name','','text',$attrs); ?>
						<div class="text-danger" id="name_id" style="display:none">Please enter valid name</div>
						<?php $attrs = array('onblur'=>"check_name(this.value,'code')",'onkeyup'=>"ch_val1()"); ?>
						 <?php echo render_input( 'code', 'code','','text',$attrs); ?>
						 <div class="text-danger" id="code_id" style="display:none">Please enter valid code</div>
						<div class="row">
                            <div class="col-md-11">
								 <div class="form-group">
									<label class="control-label" for="category"><?php echo _l('category'); ?></label>
									<select class="selectpicker display-block" data-width="100%" name="categoryid" data-none-selected-text="Select Category" id="categoryid" >
										<option value=""></option>
										<?php foreach($categories as $category1){ ?>
										<option value="<?php echo $category1['id']; ?>" data-subtext="<?php echo $category1['name']; ?>"><?php echo $category1['cat_name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="col-md-1" style="margin-top:32px;">
								<a href="#" data-toggle="modal" data-target="#categoryid_add_modal"><i class="fa fa-plus"></i></a>
							</div>
						</div>
						<?php $attrs = array('onblur'=>"check_name(this.value,'unit')"); ?>
						<?php echo render_input( 'unit', 'unit','','text',$attrs); ?>
                       <div class="text-danger" id="unit_id" style="display:none">Please enter valid unit</div>
					<div class="form-group">
					  <label for="unit_price" class="control-label">Unit Price</label>
						<div class=" row">
						  <div class="field_wrapper" style="height:40px;marin-bottom:20px;">
								<div class="row">
									<div class="col-md-12">
										<div class="col-md-4">
										  <input type="number" name="unit_price[]" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="" placeholder="Price" class="form-control" /> 
									  </div>
									  <div class="col-md-3">
										  <select name="currency[]" class="form-control"  >
											<option value="">Select Currency</option>
											<?php foreach($currencies as $val) {
											  echo '<option value="'.$val["name"].'" selected>'.$val["name"].'</option>';
											} ?>
										  </select>
									  </div>
									  <div class="col-md-3">
										  <select name="tax[]" class="form-control"   >
												<option value="">Select Tax</option>
												<?php foreach($taxes as $tax){ ?>
												<option value="<?php echo $tax['id']; ?>" ><?php echo $tax['taxrate']; ?>%<sub><?php echo $tax['name']; ?></sub></option>
												<?php } ?>
											</select>
									  </div>
									  <div class="col-md-2">
										<a href="javascript:void(0);" class="add_button1" title="Add field" style="position:relative; top:10px;"><i class="fa fa-plus"></i></a>
									  </div>
									</div>
								</div>
							</div>
							<div class="text-danger" id="cur_unit_price" style="display:none;width: 100%;float: left;margin-left: 15px;margin-top: 15px;">Please select different currency</div>
						</div>
					</div>
					<?php /*<div class="row">
                            <div class="col-md-12">
                             <div class="form-group">
                                <label class="control-label" for="tax"><?php echo _l('tax'); ?></label>
                                <select class="selectpicker display-block" data-width="100%" name="tax" data-none-selected-text="<?php echo _l('no_tax'); ?>" required>
                                    <option value=""></option>
                                    <?php foreach($taxes as $tax){ ?>
                                    <option value="<?php echo $tax['id']; ?>" data-subtext="<?php echo $tax['name']; ?>"><?php echo $tax['taxrate']; ?>%</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
					</div>*/?>
					<?php $attrs = array('onblur'=>"check_name(this.value,'description')"); ?>
					<?php echo render_textarea( 'description', 'description','',$attrs); ?>
					<div class="text-danger" id="description_id" style="display:none">Please enter valid description</div>
				 <?php// echo render_textarea('description','description'); ?>
                <div class="clearfix mbot15"></div>
                <div id="custom_fields_items">
                    <?php echo render_custom_fields('items'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
        <?php echo form_close(); ?>
    </div>
</div>
</div>
</div>


<div class="modal fade" id="categoryid_add_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('add_new',_l('category')); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/invoice_items/ajax_client',array('id'=>'categoryid_add_group_modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php $attrs = array('autofocus'=>true, 'required'=>true); ?>
                        <?php echo render_input( 'category', 'product_category','','text',$attrs); ?>
						<div class='text-danger' id="cur_cat" style="display:none" ></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>

            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>

    // Maybe in modal? Eq convert to invoice or convert proposal to estimate/invoice
    if(typeof(jQuery) != 'undefined'){
        init_item_js();
    } else {
     window.addEventListener('load', function () {
       var initItemsJsInterval = setInterval(function(){
            if(typeof(jQuery) != 'undefined') {
                init_item_js();
                clearInterval(initItemsJsInterval);
            }
         }, 1000);
     });
  }
// Items add/edit
function manage_invoice_items(form) {
    var data = $(form).serialize();
	var req_currency  = '';
	 $('#cur_unit_price').hide();
	 $('#name_id').hide();
	 $('#code_id').hide();
	 $('#unit_id').hide();
	 $('#description_id').hide();
	 var name_val = $('#name').val();
	if ( name_val.match(/^[a-zA-Z0-9]+/)  ) {
    } else {
		$('#name_id').show();
        return false;
    }
	var code_val = $('#code').val();
	if ( code_val.match(/^[a-zA-Z0-9]+/) || code_val==''  ) {
    } else {
		$('#code_id').show();
        return false;
    }
	var unit_val = $('#unit').val();
	if ( unit_val.match(/^[a-zA-Z0-9]+/) || unit_val=='' ) {
    } else {
		$('#unit_id').show();
        return false;
    }
	var description_val = $('#description').val();
	if ( description_val.match(/^[a-zA-Z0-9]+/)  || description_val==''  ) {
    } else {
		$('#description_id').show();
        return false;
    }
	 var req_data = 1;
 $('select[name="currency[]"]').each( function () {
	  var req_txt  = req_currency.includes($(this).val());
	 req_currency = req_currency + $(this).val() + ',';
		if(req_txt && $(this).val()!=''){
			req_data =2;
		  $('#cur_unit_price').show();
		   return false;
		}
   });
	if(req_data ==2){
		return false;
	}
	
    var url = form.action;
	$('.price1-html').hide();
	$('.price1-html').html('');
    $.post(url, data).done(function (response) {
        response = JSON.parse(response);
        if (response.success == true) {
            var item_select = $('#item_select');
            if ($("body").find('.accounting-template').length > 0) {
                if (!item_select.hasClass('ajax-search')) {
                    var group = item_select.find('[data-group-id="' + response.item.group_id + '"]');
                    if (group.length == 0) {
                        var _option = '<optgroup label="' + (response.item.group_name == null ? '' : response.item.group_name) + '" data-group-id="' + response.item.group_id + '">' + _option + '</optgroup>';
                        if (item_select.find('[data-group-id="0"]').length == 0) {
                            item_select.find('option:first-child').after(_option);
                        } else {
                            item_select.find('[data-group-id="0"]').after(_option);
                        }
                    } else {
                        group.prepend('<option data-subtext="' + response.item.long_description + '" value="' + response.item.itemid + '">(' + accounting.formatNumber(response.item.rate) + ') ' + response.item.description + '</option>');
                    }
                }
                if (!item_select.hasClass('ajax-search')) {
                    item_select.selectpicker('refresh');
                } else {

                    item_select.contents().filter(function () {
                        return !$(this).is('.newitem') && !$(this).is('.newitem-divider');
                    }).remove();

                    var clonedItemsAjaxSearchSelect = item_select.clone();
                    item_select.selectpicker('destroy').remove();
                    $("body").find('.items-select-wrapper').append(clonedItemsAjaxSearchSelect);
                    init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
                }

                add_item_to_preview(response.item.itemid);
            } else {
                // Is general items view
                $('.table-invoice-items').DataTable().ajax.reload(null, false);
            }
            alert_float('success', response.message);
        }
        $('#sales_item_modal').modal('hide');
    }).fail(function (data) {
        alert_float('danger', data.responseText);
    });
    return false;
}
function init_item_js() {
     // Add item to preview from the dropdown for invoices estimates
    $("body").on('change', 'select[name="item_select"]', function () {
        var itemid = $(this).selectpicker('val');
        if (itemid != '') {
            add_item_to_preview(itemid);
        }
    });
	

    // Items modal show action
    $("body").on('show.bs.modal', '#sales_item_modal', function (event) {
	

        $('.affect-warning').addClass('hide');

        var $itemModal = $('#sales_item_modal');
        $('input[name="itemid"]').val('');
        $itemModal.find('input').not('input[type="hidden"]').val('');
        $itemModal.find('textarea').val('');
        $itemModal.find('select').selectpicker('val', '').selectpicker('refresh');
        $('select[name="categoryid"]').selectpicker('val', '').change();
        $('select[name="currency[]"]').selectpicker('val', '').change();
      //  $('select[name="tax2"]').selectpicker('val', '').change();
        $('select[name="tax[]"]').selectpicker('val', '').change();
        $itemModal.find('.add-title').removeClass('hide');
        $itemModal.find('.edit-title').addClass('hide');

        var id = $(event.relatedTarget).data('id');
        // If id found get the text from the datatable
        if (typeof (id) !== 'undefined') {

            $('.affect-warning').removeClass('hide');
            $('input[name="itemid"]').val(id);

            requestGetJSON('invoice_items/get_item_by_id/' + id).done(function (response) {
                $itemModal.find('input[name="name"]').val(response.name);
				//alert(response.unit_prices[0]['tax']);
                $itemModal.find('input[name="code"]').val(response.code);
                $itemModal.find('textarea[name="description"]').val(response.description.replace(/(<|<)br\s*\/*(>|>)/g, " "));
               // $itemModal.find('input[name="rate"]').val(response.rate);
                $itemModal.find('input[name="unit"]').val(response.unit);
                //$('select[name="tax[]"]').selectpicker('val', response.unit_prices[0]['tax']).change();
                $('select[name="categoryid"]').selectpicker('val', response.categoryid).change();
				//$('select[name="tax[]"]').empty().append('<option value="">Select Tax</option>');
					$('select[name="tax[]"]').selectpicker('refresh');
				 $itemModal.find('input[name="unit_price[]"]').val(response.unit_prices[0]['price']);
                $('select[name="currency[]"]').selectpicker('val', response.unit_prices[0]['currency']).change();
					if(response.unit_prices[0]['tax']!=0){
					$('select[name="tax[]"]').selectpicker('val', response.unit_prices[0]['tax']).change();
					}
				
			  // $('select[name="tax[]"]').selectpicker('val', '1').change();
               // $('select[name="tax2"]').selectpicker('val', response.taxid_2).change();
             //   $itemModal.find('#group_id').selectpicker('val', response.group_id);
				$('.price1-html').hide();
				$('.price1-html').html('');
				$('.field_wrapper').append(response.price_html);
                /*$.each(response, function (column, value) {
                    if (column.indexOf('rate_currency_') > -1) {
                        $itemModal.find('input[name="' + column + '"]').val(value);
                    }
                });*/

                $('#custom_fields_items').html(response.custom_fields_html);

                init_selectpicker();
                init_color_pickers();
                init_datepicker();

                $itemModal.find('.add-title').addClass('hide');
                $itemModal.find('.edit-title').removeClass('hide');
                validate_item_form();
            });

        }
    });

    $("body").on("hidden.bs.modal", '#sales_item_modal', function (event) {
        $('#item_select').selectpicker('val', '');
    });

   validate_item_form();
}
function sales_item1(){
	$('.price1-html').hide();
	$('.price1-html').html('');
}

function validate_item_form(){
    // Set validation for invoice item form
    appValidateForm($('#invoice_item_form'), {
       // description: 'required',
        name:{
			required:true,
			remote: {
				url: admin_url + "invoice_items/item_exists",
				type: 'post',
				data: {
					itemid:function(){
						return $('input[name="itemid"]').val();
					}
				}
			}
		},
		code:{
			remote: {
				url: admin_url + "invoice_items/code_exists",
				type: 'post',
				data: {
					itemid:function(){
						return $('input[name="itemid"]').val();
					}
				}
			}
		},			
        /*code: 'required',
        categoryid: 'required',
        unit: 'required',
        unit_price: 'required',
        currency: 'required',
        tax: 'required',
		
        rate: {
            required: true,
        },*/
		
    }, manage_invoice_items);
	
	
}
function check_name(a,ch_id){
	$('#'+ch_id).val(a.trim());
	var req_val = $('#code-error').html();
	if(req_val!=''){
		$('#code-error').html('This code already exists.');
		$('#code-error').attr('style','display: block !important');
	}
}

function ch_val1(){
	$('#code-error').hide();
	ch_val();
}
function ch_val(){
	setTimeout( function(){ 
	var req_val = $('#code-error').html();
	if(req_val!=''){
		$('#code-error').html('This code already exists.');
		$('#code-error').attr('style','display: block !important');
	}
	}  , 50 );
}
</script>
<style>
#code-error{
	display:none !important;
}
</style>