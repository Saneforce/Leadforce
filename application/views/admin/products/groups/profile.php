<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="customer-profile-group-heading"><?php echo _l('product_add_edit_profile'); ?>
<?php if($product->deleted_status == 1) { ?>
    <a href="<?php echo admin_url('products/restore_product/'.$product->id); ?>" style="float:right; margin-top:-6px;" class="btn btn-info"><?php echo _l('restore'); ?></a>
<?php } ?>
</h4>
<?php if($this->session->flashdata('gdpr_restore_warning')){ ?>
    <div class="alert alert-success">
     Organization has been Restored.
   </div>
<?php } ?>
<div class="row">
   <?php echo form_open($this->uri->uri_string(),array('class'=>'product-form','autocomplete'=>'off')); ?>
   <div class="additional"></div>
   <div class="col-md-12">
      <input type="hidden" id="product_id" value="<?php echo $product->id; ?>" >
      <div class="col-md-6">
        <?php $value=( isset($product) ? $product->name : ''); ?>
        <?php echo render_input( 'name', 'product_name',$value); ?>
        <?php $value=( isset($product) ? $product->code : ''); ?>
        <?php echo render_input( 'code', 'product_code',$value); 
        //pre($category);
        ?>
        
        <div
            class="form-group select-placeholder categoryiddiv form-group-select-input-groups_in[] input-group-select">

            <label for="categoryid" class="control-label"><?php echo _l('product_category'); ?></label>
            <div class="input-group input-group-select select-groups_in[]">
                <select id="categoryid" name="categoryid" data-live-search="true" data-width="100%"
                    class="ajax-search"
                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                    <?php
                      $catid = (isset($product) ? $product->categoryid : '');
                        if($catid != ''){
                            foreach($category as $val) {
                              if($catid == $val['id']) {
                                echo '<option value="'.$val['id'].'" selected>'.$val['cat_name'].'</option>';
                              }
                            }
                        } 
                    ?>
                </select>
                <div class="input-group-addon" style="opacity: 1;"><a href="#" data-toggle="modal"
                        data-target="#categoryid_add_modal"><i class="fa fa-plus"></i></a></div>
            </div>

        </div>
        <?php
        $value=( isset($product) ? $product->unit : ''); ?>
        <?php echo render_input( 'unit', 'unit',$value); ?>
        <?php if(!isset($product)) {
        ?>
        <div class="form-group">
          <label for="unit_price" class="control-label">Unit Price</label>
            <div class="field_wrapper row">
              <div style="height:40px;">
              <?php if(isset($unitprice)) { ?>
                <a href="javascript:void(0);" class="add_button" title="Add field" style="position:relative; top:10px;"><i class="fa fa-plus"></i></a>
                <?php
                foreach($unitprice as $up) {
                ?>
                <div class="col-md-6">
                    <input type="number" name="unit_price[]" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="<?php echo $up['price']; ?>" placeholder="Price" class="form-control" /> 
                </div>
                <div class="col-md-4">
                    <select name="currency[]" class="form-control" readonly>
                      <option value="<?php echo $up['currency']; ?>"><?php echo $up['currency']; ?></option>
                    </select>
                </div>
                    <?php  } ?>
                
              <?php } else { ?>
                  <div class="col-md-6">
                      <input type="number" name="unit_price[]" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="" placeholder="Price" class="form-control" /> 
                  </div>
                  <div class="col-md-4">
                      <select name="currency[]" class="form-control" >
                        <?php foreach($currencies as $val) {
                          echo '<option value="'.$val["name"].'">'.$val["name"].'</option>';
                        } ?>
                      </select>
                  </div>
                  <div class="col-md-2">
                    <a href="javascript:void(0);" class="add_button" title="Add field" style="position:relative; top:10px;"><i class="fa fa-plus"></i></a>
                  </div>
              <?php } ?>
              </div>
            </div>
        </div>
        <?php } ?>
        <?php $value=( isset($product) ? $product->tax : ''); ?>
        <?php echo render_input( 'tax', 'tax',$value,'number'); ?>
        <?php $value=( isset($product) ? $product->description : ''); ?>
        <?php echo render_textarea( 'description', 'product_descripton',$value); ?>
        <input type="hidden" name="id" value="<?php echo ( isset($product) ? $product->id : '');?>">
        </div>
        
        <?php
        if(isset($product)) {
        ?>
        <div class="col-md-9">
        <div class="form-group">
          <label for="unit_price" class="control-label"><h4>Unit Price</h4></label>
            <div class="field_wrapper row">
              
              <?php if(isset($unitprice) && !empty($unitprice)) { ?>
                
                <a href="javascript:void(0);" class="addedit_button" title="Add field" style="position:relative; top:10px;height:40px; left:15px; clear:both; float:left;"><i class="fa fa-plus"></i> Add More</a>
                <?php
                foreach($unitprice as $up) {
                ?>
                <div style="height:40px;clear:both;">
                <div class="col-md-6">
                    <input type="number" name="unit_price[]" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="<?php echo $up['price']; ?>" placeholder="Price" class="form-control" /> 
                </div>
                <div class="col-md-4">
                    <select name="currency[]" class="form-control" readonly>
                      <option value="<?php echo $up['currency']; ?>"><?php echo $up['currency']; ?></option>
                    </select>
                </div>
                <div class="col-md-2">
                </div>
                </div>
                    <?php  } ?>
                
              <?php } else { ?>
                <a href="javascript:void(0);" class="addedit_button" title="Add field" style="position:relative; top:10px;height:40px; left:15px; clear:both; float:left;"><i class="fa fa-plus"></i> Add More</a>
                <div style="height:40px;clear:both;">
                  <div class="col-md-6">
                      <input type="number" name="unit_price[]" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="" placeholder="Price" class="form-control" /> 
                  </div>
                  <div class="col-md-4">
                      <select name="currency[]" class="form-control" >
                        <?php foreach($currencies as $val) {
                          echo '<option value="'.$val["name"].'">'.$val["name"].'</option>';
                        } ?>
                      </select>
                  </div>
                  <div class="col-md-2">
                    
                  </div>
                      </div>
              <?php } ?>
            </div>
        </div>
                      </div>
        <?php } ?>

        <?php
        if(isset($product)) {
        ?>
        <div class="col-md-12">
            <div class="form-group">
                <label for="unit_price" class="control-label"><h4>Price Variation</h4></label>
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" id="variation" value="" placeholder="Variation Name"  class="form-control"/> 
                    </div>
                    <div class="col-md-6">
                    <a href="javascript:void(0);" class="btn btn-info savevariation" >Add Variation</a> 
                    </div>
                </div>
                    <?php if(isset($variations) && !empty($variations)) { ?>
                        <?php
                        $v = 0;
                        foreach($variations as $vari) {
                        ?>
                            <div id="<?php echo $v; ?>">
                            <div class="field_variation_wrapper row">
                                <h4 style="padding:16px 0px 0px 15px;"><?php echo $vari['name']; ?></h4><a href="javascript:void(0);" class="editvariation_button" title="Add field" style="position:relative; top:10px; left:15px; clear:both; float:left; height:40px;"><i class="fa fa-plus"></i> Add More</a>
                                <input type="hidden" id="varid" name="varid[]" value="<?php echo $vari['id']; ?>">
                                <?php
                                $fields = 0;
                                if(isset($unitvariation) && !empty($unitvariation)) { 
                                    foreach($unitvariation as $up) {
                                        if($up['variationid']==$vari['id']) {
                                    ?>
                                            <div style="min-height:100px;clear:both;">
                                                
                                                <div class="col-md-3">
                                                    <input type="number" name="variation_price_<?php echo $vari['id']; ?>[]" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="<?php echo $up['variation_price']; ?>" placeholder="Price" class="form-control" /> 
                                                </div>
                                                <div class="col-md-2">
                                                    <select name="variation_currency_<?php echo $vari['id']; ?>[]" class="form-control" readonly>
                                                    <option value="<?php echo $up['currency']; ?>"><?php echo $up['currency']; ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                <textarea name="comment_<?php echo $vari['id']; ?>[]" id="comment[]"  class="form-control" placeholder="Comment" rows="5" /><?php echo $up['comment']; ?></textarea>
                                                </div>
                                                <div class="col-md-1">
                                                </div>
                                            </div>
                                        <?php $fields++; } ?>
                                    <?php } 
                                
                                    if($fields == 0 ) {  ?>
                                        <div style="min-height:100px; clear:both;">
                                            <div class="col-md-3">
                                                <input type="number" name="variation_price_<?php echo $vari['id']; ?>[]" step="any" onkeypress="return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)" value="" placeholder="Price" class="form-control" /> 
                                            </div>
                                            <div class="col-md-2">
                                                <select name="variation_currency_<?php echo $vari['id']; ?>[]" class="form-control" >
                                                    <?php foreach($unit_currencies as $val) {
                                                    echo '<option value="'.$val["currency"].'">'.$val["currency"].'</option>';
                                                    } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <textarea name="comment_<?php echo $vari['id']; ?>[]" id="comment[]"  class="form-control" placeholder="Comment" rows="5" /></textarea>
                                            </div>
                                            <div class="col-md-1">
                                                
                                            </div>
                                        </div>
                                    <?php 
                                        }
                                     } ?>
                                </div>        
                            </div>
                        <?php $v++; }
                        
                        }?>

                    </div>
                </div>
                <?php } ?>
      </div>
   </div>
   <?php echo form_close(); ?>
</div>
  <div class="modal fade" id="categoryid_add_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('add_new',_l('product_category')); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/products/ajax_client',array('id'=>'categoryid_add_group_modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php $attrs = array('autofocus'=>true, 'required'=>true); ?>
                        <?php echo render_input( 'category', 'product_category','','text',$attrs); ?>
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

<!-- /.modal -->
<?php $this->load->view('admin/products/client_group'); ?>

