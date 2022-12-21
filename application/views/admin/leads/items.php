<style>
    .css-table {
        display: table;
        width: 100%;
    }

    .css-table-header {
        display: table-header-group;
    }

    .css-table-body {
        display: table-row-group;
    }

    .css-table-row {
        display: table-row;
    }

    .css-table-header div,
    .css-table-row div {
        display: table-cell;
        padding: 0 6px;
    }

    .css-table-header div {
        text-align: left;
        border: 1px solid rgb(255, 255, 255);
    }
</style>


        <div class="col-md-12 row" style="padding-bottom:13px;">
            <div class="col-md-3" style="display:inline-flex">
                <span><?php echo _l('dealcurrency'); ?></span>
                <span style="padding-left:10px;">
                    <select class="form-control currencyswitcher" id="currency" name="currency" style="padding:0px 6px; height:23px;">
                        <?php
                        foreach ($allcurrency as $ac) {
                            $selected = '';
                            if ($lead_currency == $ac['name']) {
                                $selected = 'selected';
                            }
                        ?>
                            <option value="<?php echo $ac['name']; ?>" <?php echo $selected; ?>><?php echo $ac['name']; ?></option>
                        <?php  } ?>
                    </select></span>
            </div>
            <?php
            $checked = '1';
            if (isset($leadproducts)) {
                foreach ($leadproducts as $pr) {
                    $checked = $pr['method'];
                }
            }
            ?>
            <?php if (isset($leadproducts) && !empty($leadproducts)) { ?>
                <input type="hidden" id="product_index" value="<?php echo $productscnt; ?>">
                <input type="hidden" name="method" id="method" value="<?php echo $checked; ?>">
            <?php } else { ?>
                <input type="hidden" id="product_index" value="1">
                <input type="hidden" name="method" id="method" value="1">
            <?php } ?>
            <input type="hidden" id="discount_value" value="<?php echo $discount_value; ?>">
            <input type="hidden" id="discount_option" value="<?php echo $discount_option; ?>">
            <div class="col-md-2" style="display:inline-flex">
                <span><input type="radio" id="notax" name="tax" value="notax" <?php if (isset($checked) && $checked == '1') { ?> checked="checked" <?php } ?>></span>
                <span style="padding-left:10px;"><?php echo _l('notax'); ?></span>
            </div>
            <div class="col-md-2" style="display:inline-flex">
                <span><input type="radio" id="intax" name="tax" value="intax" <?php if (isset($checked) && $checked == '2') { ?> checked="checked" <?php } ?>></span>
                <span style="padding-left:10px;"><?php echo _l('inclusivetax'); ?></span>
            </div>
            <div class="col-md-2" style="display:inline-flex">
                <span><input type="radio" id="extax" name="tax" value="extax" <?php if (isset($checked) && $checked == '3') { ?> checked="checked" <?php } ?>></span>
                <span style="padding-left:10px;"><?php echo _l('exclusivetax'); ?></span>
            </div>
        </div>
        <hr style="clear:both;">
        <div class="css-table">
            <div style="height:40px;clear:both;" class="css-table-header" id="topheading">
                <?php echo get_particular_item_headers($pr['method'], $discount_option, $discount_value); ?>
            </div>

            <div class="field_product_wrapper row css-table-body">
                <?php if (isset($leadproducts) && !empty($leadproducts)) { ?>

                    <?php
                    $subtotal = 0.00;
                    $discount = '';
                    $tax_txt = '';
                    $tax_val = '';
                    $i = 1;
                    foreach ($leadproducts as $pr) {
                        if ($pr['method'] == 1) {
                    ?>
                            <div style="height:40px;clear:both;" class="productdiv css-table-row" id="<?php echo $i; ?>">
                                <div class="wcb">
                                    <select name="product[]" class="form-control" onchange="getdealprodprice(this,<?php echo $i; ?>)">
                                        <option value="">--Select Item--</option>
                                        <?php
                                        foreach ($products as $prod) {
                                            $selected = '';
                                            if ($prod['id'] == $pr['productid']) {
                                                $selected = 'selected';
                                            }
                                        ?>
                                            <option value="<?php echo $prod['id']; ?>" <?php echo $selected; ?>><?php echo $prod['name']; ?></option>
                                        <?php  } ?>
                                    </select>
                                </div>
                                <?php echo get_particulars_item_ordered_inputs($i, $pr['productid']) ?>
                                <div class="">
                                    <input type="text" name="price[]" value="<?php echo $pr['price']; ?>" placeholder="Price" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,<?php echo $i; ?>)" class="form-control" />
                                </div>
                                <div class="">
                                    <input type="number" name="qty[]" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" min="1" placeholder="Qty" value="<?php echo $pr['quantity']; ?>" onchange="qty_total(this,<?php echo $i; ?>)" class="form-control" />
                                </div>
                                <?php if ($discount_value == 1 || $discount_option == 1) { ?>
                                    <div class="">
                                        <input type="number" name="discount[]" min="0" placeholder="Discount" value="<?php echo $pr['discount']; ?>" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="discount_total(this,<?php echo $i; ?>)" class="form-control" />
                                    </div>
                                <?php } ?>
                                <div class="">
                                    <input type="number" name="total[]" value="<?php echo $pr['total_price']; ?>" placeholder="Total" readonly class="form-control" />
                                </div>
                                <span class="dropdown">
                                    <button type="button" class="btn btn-primary " data-toggle="dropdown">...</button>
                                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                        <li></li>
                                        <?php /* <li><a class="dropdown-item" href="#" onClick="gotoprod(<?php echo $i; ?>);">Go to Product</a></li>*/ ?>
                                        <li><a class="dropdown-item" href="<?php echo base_url() . 'admin/invoice_items'; ?>">Go to Items</a></li>
                                        <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                                        <?php /*<li><a class="dropdown-item" id="variationbtn_<?php echo $i; ?>" href="#" onClick="selectVariation(<?php echo $i; ?>);">Select Variation</a></li>*/ ?>
                                    </ul>
                                </span>
                                <?php
                                if ($pr['variation']) { ?>
                                    <div class="col-md-2" id="variation_<?php echo $i; ?>" style="width: 23.3%;margin: 4px 19px 15px;clear:both;">
                                        <label>VARIATION</label>
                                        <select name="variation_<?php echo $i; ?>" class="form-control" onchange="getvariationprodprice(this,<?php echo $i; ?>)">
                                            <option value="">--Select Variation--</option>

                                            <?php
                                            $CI = &get_instance();
                                            $vari = $CI->prodgetvaraiton($pr['productid'], $pr['currency']);
                                            foreach ($vari as $val) {
                                                $selected = '';
                                                if ($val["id"] == $pr['variation']) {
                                                    $selected = 'selected';
                                                }
                                                echo '<option value="' . $val["id"] . '" ' . $selected . '>' . $val["name"] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                <?php
                                    echo '<style>#variationbtn_' . $i . '{pointer-events: none; cursor: default;}</style>';
                                }
                                ?>
                            </div>
                        <?php

                            $i++;
                        }
                        if ($pr['method'] == 2 || $pr['method'] == 3) {
                        ?>
                            <div style="height:40px;clear:both;" class="productdiv css-table-row" id="<?php echo $i; ?>">
                                <div class="wcb" style="width:20%;">
                                    <select name="product[]" class="form-control" onchange="getdealprodprice(this,<?php echo $i; ?>)">
                                        <option value="">--Select Item--</option>
                                        <?php
                                        foreach ($products as $prod) {
                                            $selected = '';
                                            if ($prod['id'] == $pr['productid']) {
                                                $selected = 'selected';
                                            }
                                        ?>
                                            <option value="<?php echo $prod['id']; ?>" <?php echo $selected; ?>><?php echo $prod['name']; ?></option>
                                        <?php  } ?>
                                    </select>
                                </div>
                                <?php echo get_particulars_item_ordered_inputs($i, $pr['productid']) ?>
                                <div class="">
                                    <input type="text" name="price[]" value="<?php echo $pr['price']; ?>" placeholder="Price" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,<?php echo $i; ?>)" class="form-control" />
                                </div>
                                <div class="">
                                    <input type="number" name="qty[]" min="1" placeholder="Qty" value="<?php echo $pr['quantity']; ?>" onchange="qty_total(this,<?php echo $i; ?>)" class="form-control" />
                                </div>
                                <div class="">
                                    <input type="number" name="tax[]" min="0" placeholder="Tax" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="<?php echo $pr['tax']; ?>" onchange="tax_total(this,<?php echo $i; ?>)" class="form-control" />
                                </div>
                                <?php if ($discount_value == 1 || $discount_option == 1) { ?>
                                    <div class="">
                                        <input type="number" name="discount[]" min="0" placeholder="Discount" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="<?php echo $pr['discount']; ?>" onchange="discount_total(this,<?php echo $i; ?>)" class="form-control" />
                                    </div>
                                <?php } ?>
                                <div class="">
                                    <input type="number" name="total[]" value="<?php echo $pr['total_price']; ?>" placeholder="Total" readonly class="form-control" />
                                </div>
                                <span class="dropdown">
                                    <button type="button" class="btn btn-primary " data-toggle="dropdown">...</button>
                                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                        <li></li>
                                        <?php /*  <li><a class="dropdown-item" href="#" onClick="gotoprod(<?php echo $i; ?>);">Go to Product</a></li>*/ ?>
                                        <li><a class="dropdown-item" href="<?php echo base_url() . 'admin/invoice_items'; ?>">Go to Items</a></li>
                                        <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                                        <?php /* <li><a class="dropdown-item" id="variationbtn_<?php echo $i; ?>" href="#" onClick="selectVariation(<?php echo $i; ?>);">Select Variation</a></li>*/ ?>
                                    </ul>
                                </span>
                                <?php
                                if ($pr['variation']) { ?>
                                    <div class="" id="variation_<?php echo $i; ?>" style="width: 18.7%;margin: 4px 15px 15px;clear:both;">
                                        <label>VARIATION</label>
                                        <select name="variation_<?php echo $i; ?>" class="form-control" onchange="getvariationprodprice(this,<?php echo $i; ?>)">
                                            <option value="">--Select Variation--</option>

                                            <?php
                                            $CI = &get_instance();
                                            $vari = $CI->prodgetvaraiton($pr['productid'], $pr['currency']);
                                            foreach ($vari as $val) {
                                                $selected = '';
                                                if ($val["id"] == $pr['variation']) {
                                                    $selected = 'selected';
                                                }
                                                echo '<option value="' . $val["id"] . '" ' . $selected . '>' . $val["name"] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                <?php
                                    echo '<style>#variationbtn_' . $i . '{pointer-events: none; cursor: default;}</style>';
                                }
                                ?>
                            </div>

                    <?php
                            if ($pr['tax'] && $pr['tax'] > 0) {
                                $dec1 = ($pr['tax'] / 100); //its convert 10 into 0.10
                                $mult1 = $pr['total_price'] * $dec1; // gives the value for subtract from main value
                                if ($pr['method'] == 2) {
                                    $tax_txt .= '<p class="txt_' . $i . '"> Includes Tax (' . $pr['tax'] . '%)</p>';
                                }
                                if ($pr['method'] == 3) {
                                    $tax_txt .= '<p class="txt_' . $i . '"> Excludes Tax (' . $pr['tax'] . '%)</p>';
                                }
                                $tax_val .= '<p class="amt_' . $i . '"> ' . number_format($mult1, 2) . '</p>';
                            }

                            $i++;
                        }
                        $subtotal = $subtotal + $pr['total_price'];
                        if ($pr['discount'] && $pr['discount'] > 0) {
                            $dec = ($pr['discount'] / 100); //its convert 10 into 0.10
                            $mult = ($pr['price'] * $pr['quantity']) * $dec; // gives the value for subtract from main value
                            $discount .= ' ' . number_format($mult, 2) . ',';
                        }
                    } ?>

                    <?php
                    if ($discount) {
                        $discount = '<small>(Includes discount of ' . substr($discount, 0, -1) . ')</small>';
                    }
                    $lead_cost = $lead->lead_cost;
                } else {
                    ?>
                    <div style="height:40px;clear:both;" class="productdiv css-table-row" id="0">
                        <div class="wcb">
                            <select name="product[]" class="form-control" onchange="getdealprodprice(this,0)">
                                <option value="">--Select Item--</option>
                                <?php
                                foreach ($products as $prod) {
                                ?>
                                    <option value="<?php echo $prod['id']; ?>"><?php echo $prod['name']; ?></option>
                                <?php  } ?>
                            </select>
                        </div>
                        <?php echo get_particulars_item_ordered_inputs() ?>
                        <div class="">
                            <input type="text" name="price[]" value="" placeholder="Price" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" onchange="price_update(this,0)" class="form-control" />
                        </div>
                        <div class="">
                            <input type="number" name="qty[]" min="1" placeholder="Qty" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="" onchange="qty_total(this,0)" class="form-control" />
                        </div>
                        <?php if ($discount_value == 1 || $discount_option == 1) { ?>
                            <div class="">
                                <input type="number" name="discount[]" min="0" placeholder="Discount" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="" onchange="discount_total(this,0)" class="form-control" />
                            </div>
                        <?php } ?>
                        <div class="">
                            <input type="number" name="total[]" value="" placeholder="Total" readonly class="form-control" />
                        </div>
                        <!-- <div class="col-md-1">
                        </div> -->
                        <span class="dropdown">
                            <button type="button" class="btn btn-primary " data-toggle="dropdown">...</button>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                <li></li>
                                <?php /* <li><a class="dropdown-item" href="#" onClick="gotoprod(0);">Go to Product</a></li>*/ ?>
                                <li><a class="dropdown-item" href="<?php echo base_url() . 'admin/invoice_items'; ?>">Go to Items</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item removeproduct_button" title="Remove field">Remove</a></li>
                                <?php /* <li><a class="dropdown-item" id="variationbtn_0" href="#" onClick="selectVariation(0);">Select Variation</a></li>*/ ?>
                            </ul>
                        </span>
                    </div>
                <?php
                    $lead_cost = 0;
                } ?>
            </div>
            <a href="javascript:void(0);" class="editproduts_notax_btn row" title="Add field" style="position:relative; top:10px; left:15px; clear:both; float:left; height:40px;"><i class="fa fa-plus"></i> Add a new line</a>
            <div class="css-table-row" id="particularsrowfooter">
                <div class="text-right" style="padding-top:30px">
                    <span id="stxt">
                        <p>Subtotal <?php echo $discount; ?></p>
                    </span><span id="suptotaltxt"><?php echo $tax_txt; ?></span><b>Total</b>
                </div>
                <div class="text-right" style="padding-top:30px"><span id="stotal">
                        <p><?php echo number_format($subtotal, 2); ?></p>
                    </span><span id="suptotal"><?php echo $tax_val; ?></span><b><span id="grandtotal"><?php echo number_format($lead_cost, 2); ?></span></b>
                </div>
            </div>
            <input type="hidden" name="grandtotal" id="gtot" value="<?php echo $lead_cost; ?>">
        </div>

<script>
    function addFooterEmptyCell(){
        var headercount =$('#topheading > div').length;
        $('.footer-empty-cells').remove();
        for (let index = 0; index < headercount-2; index++) {
        $('#particularsrowfooter').prepend(`<div class="footer-empty-cells"></div>`);
        }
    }
    <?php if(isset($lead) && $lead->id): ?>
    document.addEventListener("DOMContentLoaded", () => {
        addFooterEmptyCell();
        
        
        $('#LeadProdcutForm').submit(function(e){
            e.preventDefault();
            $.ajax({
                type:'POST',
                url:$(this).attr('action'),
                data:$(this).serialize(),
                dataType:'JSON',
                success: function(response){
                    if(response.success ==true){
                        $('#leaditemcount').html(response.itemscount);
                        alert_float('success', response.msg);
                        setTimeout(function(){
                            document.getElementById('overlay_12').style.display = 'none'; 
                            window.location.href = admin_url+'leads/lead/'+<?php echo $lead->id ?>+'/?group=tab_items'
                        },1000);
                    }
                }
            })
        });
        
    });
    <?php else: ?>
        addFooterEmptyCell();
    <?php endif; ?>

    
</script>