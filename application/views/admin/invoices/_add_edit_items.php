<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel-body mtop10">
   <!-- <div class="row">
      <div class="col-md-4">
          <?php $this->load->view('admin/invoice_items/item_select'); ?>
      </div>
      <div class="col-md-8 text-right show_quantity_as_wrapper">
         <div class="mtop10">
            <span><?php echo _l('show_quantity_as'); ?></span>
            <div class="radio radio-primary radio-inline">
               <input type="radio" value="1" id="1" name="show_quantity_as" data-text="<?php echo _l('estimate_table_quantity_heading'); ?>" <?php if(isset($estimate) && $estimate->show_quantity_as == 1){echo 'checked';}else{echo'checked';} ?>>
               <label for="1"><?php echo _l('quantity_as_qty'); ?></label>
            </div>
            <div class="radio radio-primary radio-inline">
               <input type="radio" value="2" id="2" name="show_quantity_as" data-text="<?php echo _l('estimate_table_hours_heading'); ?>" <?php if(isset($estimate) && $estimate->show_quantity_as == 2){echo 'checked';} ?>>
               <label for="2"><?php echo _l('quantity_as_hours'); ?></label>
            </div>
            <div class="radio radio-primary radio-inline">
               <input type="radio" id="3" value="3" name="show_quantity_as" data-text="<?php echo _l('estimate_table_quantity_heading'); ?>/<?php echo _l('estimate_table_hours_heading'); ?>" <?php if(isset($estimate) && $estimate->show_quantity_as == 3){echo 'checked';} ?>>
               <label for="3"><?php echo _l('estimate_table_quantity_heading'); ?>/<?php echo _l('estimate_table_hours_heading'); ?></label>
            </div>
         </div>
      </div>
   </div> -->
   <!-- <div class="table-responsive s_table">
      <table class="table estimate-items-table items table-main-estimate-edit has-calculations no-mtop">
         <thead>
            <tr>
               <th></th>
               <th width="20%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('estimate_table_item_heading'); ?></th>
               <th width="25%" align="left"><?php echo _l('estimate_table_item_description'); ?></th>
               <?php
                  $custom_fields = get_custom_fields('items');
                  foreach($custom_fields as $cf){
                   echo '<th width="15%" align="left" class="custom_field">' . $cf['name'] . '</th>';
                  }

                  $qty_heading = _l('estimate_table_quantity_heading');
                  if(isset($estimate) && $estimate->show_quantity_as == 2){
                  $qty_heading = _l('estimate_table_hours_heading');
                  } else if(isset($estimate) && $estimate->show_quantity_as == 3){
                  $qty_heading = _l('estimate_table_quantity_heading') . '/' . _l('estimate_table_hours_heading');
                  }
                  ?>
               <th width="10%" class="qty" align="right"><?php echo $qty_heading; ?></th>
               <th width="15%" align="right"><?php echo _l('estimate_table_rate_heading'); ?></th>
               <th width="20%" align="right"><?php echo _l('estimate_table_tax_heading'); ?></th>
               <th width="10%" align="right"><?php echo _l('estimate_table_amount_heading'); ?></th>
               <th align="center"><i class="fa fa-cog"></i></th>
            </tr>
         </thead>
         <tbody>
            <tr class="main">
               <td></td>
               <td>
                  <textarea name="description" rows="4" class="form-control" placeholder="<?php echo _l('item_description_placeholder'); ?>"></textarea>
               </td>
               <td>
                  <textarea name="long_description" rows="4" class="form-control" placeholder="<?php echo _l('item_long_description_placeholder'); ?>"></textarea>
               </td>
               <?php echo render_custom_fields_items_table_add_edit_preview(); ?>
               <td>
                  <input type="number" name="quantity" min="0" value="1" class="form-control" placeholder="<?php echo _l('item_quantity_placeholder'); ?>">
                  <input type="text" placeholder="<?php echo _l('unit'); ?>" name="unit" class="form-control input-transparent text-right">
               </td>
               <td>
                  <input type="number" name="rate" class="form-control" placeholder="<?php echo _l('item_rate_placeholder'); ?>">
               </td>
               <td>
                  <?php
                     $default_tax = unserialize(get_option('default_tax'));
                     $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="taxname" multiple data-none-selected-text="'._l('no_tax').'">';
                     foreach($taxes as $tax){
                       $selected = '';
                       if(is_array($default_tax)){
                         if(in_array($tax['name'] . '|' . $tax['taxrate'],$default_tax)){
                           $selected = ' selected ';
                         }
                       }
                       $select .= '<option value="'.$tax['name'].'|'.$tax['taxrate'].'"'.$selected.'data-taxrate="'.$tax['taxrate'].'" data-taxname="'.$tax['name'].'" data-subtext="'.$tax['name'].'">'.$tax['taxrate'].'%</option>';
                     }
                     $select .= '</select>';
                     echo $select;
                     ?>
               </td>
               <td></td>
               <td>
                  <?php
                     $new_item = 'undefined';
                     if(isset($estimate)){
                       $new_item = true;
                     }
                     ?>
                  <button type="button" onclick="add_item_to_table('undefined','undefined',<?php echo $new_item; ?>); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button>
               </td>
            </tr>
            <?php if (isset($estimate) || isset($add_items)) {
               $i               = 1;
               $items_indicator = 'newitems';
               if (isset($estimate)) {
                 $add_items       = $estimate->items;
                 $items_indicator = 'items';
               }

               foreach ($add_items as $item) {
                 $manual    = false;
                 $table_row = '<tr class="sortable item">';
                 $table_row .= '<td class="dragger">';
                 if ($item['qty'] == '' || $item['qty'] == 0) {
                   $item['qty'] = 1;
                 }
                 if(!isset($is_invoice)){
                  $estimate_item_taxes = get_estimate_item_taxes($item['id']);
                } else {
                  $estimate_item_taxes = get_invoice_item_taxes($item['id']);
                }
                if ($item['id'] == 0) {
                 $estimate_item_taxes = $item['taxname'];
                 $manual              = true;
               }
               $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][itemid]', $item['id']);
               $amount = $item['rate'] * $item['qty'];
               $amount = app_format_number($amount);
               // order input
               $table_row .= '<input type="hidden" class="order" name="' . $items_indicator . '[' . $i . '][order]">';
               $table_row .= '</td>';
               $table_row .= '<td class="bold description"><textarea name="' . $items_indicator . '[' . $i . '][description]" class="form-control" rows="5">' . clear_textarea_breaks($item['description']) . '</textarea></td>';
               $table_row .= '<td><textarea name="' . $items_indicator . '[' . $i . '][long_description]" class="form-control" rows="5">' . clear_textarea_breaks($item['long_description']) . '</textarea></td>';
               $table_row .= render_custom_fields_items_table_in($item,$items_indicator.'['.$i.']');
               $table_row .= '<td><input type="number" min="0" onblur="calculate_total();" onchange="calculate_total();" data-quantity name="' . $items_indicator . '[' . $i . '][qty]" value="' . $item['qty'] . '" class="form-control">';
               $unit_placeholder = '';
               if(!$item['unit']){
                 $unit_placeholder = _l('unit');
                 $item['unit'] = '';
               }
               $table_row .= '<input type="text" placeholder="'.$unit_placeholder.'" name="'.$items_indicator.'['.$i.'][unit]" class="form-control input-transparent text-right" value="'.$item['unit'].'">';
               $table_row .= '</td>';
               $table_row .= '<td class="rate"><input type="number" data-toggle="tooltip" title="' . _l('numbers_not_formatted_while_editing') . '" onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][rate]" value="' . $item['rate'] . '" class="form-control"></td>';
               $table_row .= '<td class="taxrate">' . $this->misc_model->get_taxes_dropdown_template('' . $items_indicator . '[' . $i . '][taxname][]', $estimate_item_taxes, (isset($is_invoice) ? 'invoice' : 'estimate'), $item['id'], true, $manual) . '</td>';
               $table_row .= '<td class="amount" align="right">' . $amount . '</td>';
               $table_row .= '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_item(this,' . $item['id'] . '); return false;"><i class="fa fa-times"></i></a></td>';
               $table_row .= '</tr>';
               echo $table_row;
               $i++;
               }
               }
               ?>
         </tbody>
      </table>
   </div>
   <div class="col-md-8 col-md-offset-4">
      <table class="table text-right">
         <tbody>
            <tr id="subtotal">
               <td><span class="bold"><?php echo _l('estimate_subtotal'); ?> :</span>
               </td>
               <td class="subtotal">
               </td>
            </tr>
            <tr id="discount_area">
               <td>
                  <div class="row">
                     <div class="col-md-7">
                        <span class="bold"><?php echo _l('estimate_discount'); ?></span>
                     </div>
                     <div class="col-md-5">
                        <div class="input-group" id="discount-total">

                           <input type="number" value="<?php echo (isset($estimate) ? $estimate->discount_percent : 0); ?>" class="form-control pull-left input-discount-percent<?php if(isset($estimate) && !is_sale_discount($estimate,'percent') && is_sale_discount_applied($estimate)){echo ' hide';} ?>" min="0" max="100" name="discount_percent">

                           <input type="number" data-toggle="tooltip" data-title="<?php echo _l('numbers_not_formatted_while_editing'); ?>" value="<?php echo (isset($estimate) ? $estimate->discount_total : 0); ?>" class="form-control pull-left input-discount-fixed<?php if(!isset($estimate) || (isset($estimate) && !is_sale_discount($estimate,'fixed'))){echo ' hide';} ?>" min="0" name="discount_total">

                           <div class="input-group-addon">
                              <div class="dropdown">
                                 <a class="dropdown-toggle" href="#" id="dropdown_menu_tax_total_type" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                 <span class="discount-total-type-selected">
                                  <?php if(!isset($estimate) || isset($estimate) && (is_sale_discount($estimate,'percent') || !is_sale_discount_applied($estimate))) {
                                    echo '%';
                                    } else {
                                    echo _l('discount_fixed_amount');
                                    }
                                    ?>
                                 </span>
                                 <span class="caret"></span>
                                 </a>
                                 <ul class="dropdown-menu" id="discount-total-type-dropdown" aria-labelledby="dropdown_menu_tax_total_type">
                                   <li>
                                    <a href="#" class="discount-total-type discount-type-percent<?php if(!isset($estimate) || (isset($estimate) && is_sale_discount($estimate,'percent')) || (isset($estimate) && !is_sale_discount_applied($estimate))){echo ' selected';} ?>">%</a>
                                  </li>
                                  <li>
                                    <a href="#" class="discount-total-type discount-type-fixed<?php if(isset($estimate) && is_sale_discount($estimate,'fixed')){echo ' selected';} ?>">
                                      <?php echo _l('discount_fixed_amount'); ?>
                                    </a>
                                  </li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </td>
               <td class="discount-total"></td>
            </tr>
            <tr>
               <td>
                  <div class="row">
                     <div class="col-md-7">
                        <span class="bold"><?php echo _l('estimate_adjustment'); ?></span>
                     </div>
                     <div class="col-md-5">
                        <input type="number" data-toggle="tooltip" data-title="<?php echo _l('numbers_not_formatted_while_editing'); ?>" value="<?php if(isset($estimate)){echo $estimate->adjustment; } else { echo 0; } ?>" class="form-control pull-left" name="adjustment">
                     </div>
                  </div>
               </td>
               <td class="adjustment"></td>
            </tr>
            <tr>
               <td><span class="bold"><?php echo _l('estimate_total'); ?> :</span>
               </td>
               <td class="total">
               </td>
            </tr>
         </tbody>
      </table>
   </div>
   <div id="removed-items"></div> -->




   <div class="col-md-12 row" style="padding-bottom:13px;">
        
        <?php 
          $checked = '1';
          if(isset($dealproducts)) {
            foreach($dealproducts as $pr) {
              $checked = $pr['method'];
            }
          }
        ?>
        <?php if(isset($dealproducts) && !empty($dealproducts)) { ?>
                <input type="hidden" id="product_index" value="<?php echo $productscnt; ?>"> 
                <input type="hidden" name="method" id="method" value="<?php echo $checked; ?>">  
        <?php } else { ?>
                <input type="hidden" id="product_index" value="1"> 
                <input type="hidden" name="method" id="method" value="1">  
          <?php } ?>
        <input type="hidden" id="item_for" value="<?php echo $item_for; ?>"> 
        <input type="hidden" id="discount_value" value="<?php echo $discount_value; ?>"> 
        <input type="hidden" id="discount_option" value="<?php echo $discount_option; ?>"> 
        <input type="hidden" id="prject_id" name="project_id" value="<?php echo $invoice->id; ?>"> 
        <input type="hidden" id="change_items" name="change_items" value="0"> 
        <div class="col-md-2" style="display:inline-flex">
          <span><input type="radio" id="salesnotax" name="tax" value="notax" <?php if(isset($checked) && $checked == '1') { ?> checked="checked" <?php } ?> ></span>
          <span style="padding-left:10px;"><?php echo _l('notax'); ?></span>
        </div>
        <div class="col-md-2" style="display:inline-flex">
          <span><input type="radio" id="salesintax" name="tax" value="intax" <?php if(isset($checked) && $checked == '2') { ?> checked="checked" <?php } ?> ></span>
          <span style="padding-left:10px;"><?php echo _l('inclusivetax'); ?></span>
        </div>
        <div class="col-md-2" style="display:inline-flex">
          <span><input type="radio" id="salesextax" name="tax" value="extax" <?php if(isset($checked) && $checked == '3') { ?> checked="checked" <?php } ?> ></span>
          <span style="padding-left:10px;"><?php echo _l('exclusivetax'); ?></span>
        </div>
      </div>
      <hr style="clear:both;">
      <div style="height:60px;clear:both;">
        
        <?php 
          if($pr['method'] == 1) {
        ?>
        <div style="height:40px;clear:both;" id="topheading">
            <div class="col-md-3">Item</div>
            <div class="col-md-2">Price</div>
            <div class="col-md-2">Quantity</div>
            <?php if($discount_value == 1 || $discount_option == 1) { ?>
              <div class="col-md-2">Discount %</div>
            <?php } ?>
            <div class="col-md-2">Total</div>
        </div>

        <?php 
          }
          elseif($pr['method'] == 2 || $pr['method'] == 3) {
        ?>
        <div style="height:40px;clear:both;" id="topheading">
            <div class="col-md-2" style="width:20%;">Item</div>
            <div class="col-md-2">Price</div>
            <div class="col-md-1">Quantity</div>
            <div class="col-md-2">Tax</div>
            <?php if($discount_value == 1 || $discount_option == 1) { ?>
              <div class="col-md-2">Discount %</div>
            <?php } ?>
            <div class="col-md-2">Total</div>
        </div>
          <?php } else { ?>
            <div style="height:40px;clear:both;" id="topheading">
            <div class="col-md-3">Item</div>
            <div class="col-md-2">Price</div>
            <div class="col-md-2">Quantity</div>
            <?php if($discount_value == 1 || $discount_option == 1) { ?>
              <div class="col-md-2">Discount %</div>
            <?php } ?>
            <div class="col-md-2">Total</div>
        </div>
          <?php } ?>
        <div class="field_product_wrapper row">
              <?php if(isset($dealproducts) && !empty($dealproducts)) { ?>
               
                  <?php
                  $subtotal = 0.00;
                  $discount = '';
                  $tax_txt = '';
                  $tax_val = '';
                  $i = 1;
                    foreach($dealproducts as $pr) {
                      if($pr['method'] == 1) {
                  ?>
                    <div style="height:40px;clear:both;" class="productdiv" id="<?php echo $i; ?>">
                        <div class="col-md-3 wcb">
                            <input type="hidden" name="no[]" value="<?php echo $i; ?>">
                            <input type="checkbox" name="status_<?php echo $i; ?>" value="1" class="form-control cbox" <?php if($pr['status'] == 1){ echo 'checked'; } ?> >
                            <select name="product[]" class="form-control" onchange="getdealprodprice(this,<?php echo $i; ?>)" >
                                <option value="">--Select Item--</option>
                            <?php
                                foreach($products as $prod) {
                                  $selected = '';
                                  if($prod['id'] == $pr['productid']) {
                                    $selected = 'selected';
                                  }
                            ?>
                              <option value="<?php echo $prod['id']; ?>" <?php echo $selected; ?> ><?php echo $prod['name']; ?></option>
                            <?php  } ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="price[]" value="<?php echo $pr['price']; ?>" placeholder="Price" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,<?php echo $i; ?>)" class="form-control" /> 
                        </div>
                        <div class="col-md-2">
                        <input type="number" name="qty[]" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" min="1" placeholder="Qty" value="<?php echo $pr['quantity']; ?>" onchange="qty_total(this,<?php echo $i; ?>)" class="form-control" /> 
                        </div>
                        <?php if($discount_value == 1 || $discount_option == 1) { ?>
                          <div class="col-md-2">
                          <input type="number" name="discount[]"  min="0" placeholder="Discount" value="<?php echo $pr['discount']; ?>" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,<?php echo $i; ?>)" class="form-control" /> 
                          </div>
                        <?php } ?>
                        <div class="col-md-2">
                        <input type="number" name="total[]" value="<?php echo $pr['total_price']; ?>" placeholder="Total" readonly class="form-control" /> 
                        </div>
                        <span class="dropdown">
                          <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="true">...</button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                           <?php /* <li><a class="dropdown-item" href="#" onClick="gotoprod(<?php echo $i; ?>);">Go to Product</a></li>*/?>
                            <li><a class="dropdown-item" href="<?php echo base_url().'admin/invoice_items';?>" >Go to Items</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                            <?php /*<li><a class="dropdown-item" id="variationbtn_<?php echo $i; ?>" href="#" onClick="selectVariation(<?php echo $i; ?>);">Select Variation</a></li>*/?>
                          </ul>
                        </span>
                        <?php 
                        if($pr['variation']) { ?>
                          <div class="col-md-2" id="variation_<?php echo $i; ?>" style="width: 23.3%;margin: 4px 19px 15px;clear:both;">
                          <label>VARIATION</label>    
                          <select name="variation_<?php echo $i; ?>" class="form-control" onchange="getvariationprodprice(this,<?php echo $i; ?>)">
                              <option value="">--Select Variation--</option>
                            
                              <?php
                              $CI =& get_instance();
                              $vari = $CI->prodgetvaraiton($pr['productid'],$pr['currency']) ;
                              foreach($vari as $val) {
                                $selected = '';
                                if($val["id"] == $pr['variation']) {
                                  $selected = 'selected';
                                }
                                      echo '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
                              } 
                              ?>
                              </select>
                          </div>
                          
                        <?php
                        echo '<style>#variationbtn_'.$i.'{pointer-events: none; cursor: default;}</style>';
                        }
                        ?>
                    </div>
                    <?php 
                    
                    $i++; 
                      }
                      if($pr['method'] == 2 || $pr['method'] == 3) {
                    ?>
                      <div style="height:40px;clear:both;" class="productdiv" id="<?php echo $i; ?>">
                        <div class="col-md-2 wcb" style="width:20%;">
                            <input type="hidden" name="no[]" value="<?php echo $i; ?>">
                            <input type="checkbox" name="status_<?php echo $i; ?>" value="1" class="form-control cbox" <?php if($pr['status'] == 1){ echo 'checked'; } ?> >
                            <select name="product[]" class="form-control" onchange="getdealprodprice(this,<?php echo $i; ?>)" >
                                <option value="">--Select Item--</option>
                            <?php
                                foreach($products as $prod) {
                                  $selected = '';
                                  if($prod['id'] == $pr['productid']) {
                                    $selected = 'selected';
                                  }
                            ?>
                              <option value="<?php echo $prod['id']; ?>" <?php echo $selected; ?> ><?php echo $prod['name']; ?></option>
                            <?php  } ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="price[]" value="<?php echo $pr['price']; ?>" placeholder="Price" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,<?php echo $i; ?>)" class="form-control" /> 
                        </div>
                        <div class="col-md-1">
                        <input type="number" name="qty[]"  min="1" placeholder="Qty" value="<?php echo $pr['quantity']; ?>" onchange="qty_total(this,<?php echo $i; ?>)" class="form-control" /> 
                        </div>
                        <div class="col-md-2">
                        <input type="number" name="tax[]"  min="0" placeholder="Tax" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="<?php echo $pr['tax']; ?>" onchange="tax_total(this,<?php echo $i; ?>)" class="form-control" /> 
                        </div>
                        <?php if($discount_value == 1 || $discount_option == 1) { ?>
                          <div class="col-md-2">
                          <input type="number" name="discount[]"  min="0" placeholder="Discount" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="<?php echo $pr['discount']; ?>" onchange="discount_total(this,<?php echo $i; ?>)" class="form-control" /> 
                          </div>
                        <?php } ?>
                        <div class="col-md-2">
                        <input type="number" name="total[]" value="<?php echo $pr['total_price']; ?>" placeholder="Total" readonly class="form-control" /> 
                        </div>
                        <span class="dropdown">
                          <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="true">...</button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                          <?php /*  <li><a class="dropdown-item" href="#" onClick="gotoprod(<?php echo $i; ?>);">Go to Product</a></li>*/?>
                            <li><a class="dropdown-item" href="<?php echo base_url().'admin/invoice_items';?>" >Go to Items</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                           <?php /* <li><a class="dropdown-item" id="variationbtn_<?php echo $i; ?>" href="#" onClick="selectVariation(<?php echo $i; ?>);">Select Variation</a></li>*/?>
                          </ul>
                        </span>
                        <?php 
                        if($pr['variation']) { ?>
                          <div class="col-md-2" id="variation_<?php echo $i; ?>" style="width: 18.7%;margin: 4px 15px 15px;clear:both;">
                          <label>VARIATION</label>    
                          <select name="variation_<?php echo $i; ?>" class="form-control" onchange="getvariationprodprice(this,<?php echo $i; ?>)">
                              <option value="">--Select Variation--</option>
                            
                              <?php
                              $CI =& get_instance();
                              $vari = $CI->prodgetvaraiton($pr['productid'],$pr['currency']) ;
                              foreach($vari as $val) {
                                $selected = '';
                                if($val["id"] == $pr['variation']) {
                                  $selected = 'selected';
                                }
                                      echo '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
                              } 
                              ?>
                              </select>
                          </div>
                        <?php
                        echo '<style>#variationbtn_'.$i.'{pointer-events: none; cursor: default;}</style>';
                        }
                        ?>
                    </div>
                       
                      <?php 
                       if($pr['tax'] && $pr['tax'] > 0) {
                          $dec1 = ($pr['tax'] / 100); //its convert 10 into 0.10
                          $mult1 = $pr['total_price'] * $dec1; // gives the value for subtract from main value
                          if($pr['method'] == 2) {
                            $tax_txt .= '<p class="txt_'.$i.'"> Includes Tax ('.$pr['tax'].'%)</p>';
                          }
                          if($pr['method'] == 3) {
                            $tax_txt .= '<p class="txt_'.$i.'"> Excludes Tax ('.$pr['tax'].'%)</p>';
                          }
                          $tax_val .= '<p class="amt_'.$i.'"> '.number_format($mult1,2).'</p>';
                      }
                      
                      $i++; 
                      
                      } 
                      $subtotal = $subtotal+$pr['total_price'];
                      if($pr['discount'] && $pr['discount'] > 0) {
                        $dec = ($pr['discount'] / 100); //its convert 10 into 0.10
                        $mult = ($pr['price']*$pr['quantity']) * $dec; // gives the value for subtract from main value
                        $discount .= ' '.number_format($mult,2).',';
                      }

                     
                  } ?>
                 
              <?php  
              if($discount) {
                $discount = '<small>(Includes discount of '.substr($discount,0,-1).')</small>'; 
              }
            
            if($proposal) {
              $proj_cost = $proposal->total;
            } else {
              $proj_cost = $invoice->total;
            }
            } else { 
              ?>
                    <div style="height:40px;clear:both;" class="productdiv" id="0">
                        <div class="col-md-3 wcb">
                          <input type="hidden" name="no[]" value="0">
                            <input type="checkbox" name="status_0" value="1" class="form-control cbox">
                            <select name="product[]" class="form-control" onchange="getdealprodprice(this,0)">
                                <option value="">--Select Item--</option>
                            <?php
                                foreach($products as $prod) {
                            ?>
                              <option value="<?php echo $prod['id']; ?>" ><?php echo $prod['name']; ?></option>
                            <?php  } ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="price[]" value="" placeholder="Price"  step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,0)" class="form-control" /> 
                        </div>
                        <div class="col-md-2">
                        <input type="number" name="qty[]"  min="1" placeholder="Qty" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="" onchange="qty_total(this,0)" class="form-control" /> 
                        </div>
                        <?php if($discount_value == 1 || $discount_option == 1) { ?>
                          <div class="col-md-2">
                          <input type="number" name="discount[]"  min="0" placeholder="Discount" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="" onchange="discount_total(this,0)" class="form-control" /> 
                          </div>
                        <?php } ?>
                        <div class="col-md-2">
                        <input type="number" name="total[]" value="" placeholder="Total" readonly class="form-control" /> 
                        </div>
                        <span class="dropdown">
                          <button type="button" class="btn btn-primary " data-toggle="dropdown" aria-expanded="true">...</button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"><li></li>
                            <li><a class="dropdown-item" href="<?php echo base_url().'admin/invoice_items';?>" >Go to Items</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                          </ul>
                        </span>
                    </div>
              <?php  
            $proj_cost = 0;
            } ?>
              </div>
              <a href="javascript:void(0);" class="editproduts_notax_btn row" title="Add field" style="position:relative; top:10px; left:15px; clear:both; float:left; height:40px;"><i class="fa fa-plus"></i> Add a new line</a>  
              <div class="col-md-9 text-right" style="padding-top:30px"><span id="stxt"><p>Subtotal <?php echo $discount; ?></p></span><span id="suptotaltxt"><?php echo $tax_txt; ?></span><b>Total</b></div><div class="col-md-2 text-right" style="padding-top:30px"><span id="stotal"><p><?php echo number_format($subtotal,2); ?></p></span><span id="suptotal"><?php echo $tax_val; ?></span><b><span id="grandtotal"><?php echo number_format($proj_cost,2); ?></span></b></div>
              <input type="hidden" name="grandtotal" id="gtot" value="<?php echo $proj_cost; ?>">
            </div>
</div>
