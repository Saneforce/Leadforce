<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content accounting-template proposal">
      <div class="row">
         <?php
            if(isset($proposal)){
             echo form_hidden('isedit',$proposal->id);
            }
            $rel_type = '';
            $rel_id = '';
            if(isset($proposal) || ($this->input->get('rel_id') && $this->input->get('rel_type'))){
             if($this->input->get('rel_id')){
               $rel_id = $this->input->get('rel_id');
               $rel_type = $this->input->get('rel_type');
             } else {
               $rel_id = $proposal->rel_id;
               $rel_type = $proposal->rel_type;
             }
            }
            ?>
         <?php echo form_open($this->uri->uri_string(),array('id'=>'proposal-form','class'=>'_transaction_form proposal-form')); ?>
         <input type="hidden" class="form-control" id="temp_id" name="tempid" value="">
         <input type="hidden" class="form-control" id="email_sub1" name="email_sub" value="">
         <input type="hidden" class="form-control" id="email_template_custom1" name="email_template_custom" value="">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <?php if(isset($proposal)){ ?>
                     <div class="col-md-12">
                        <?php echo format_proposal_status($proposal->status); ?>
                     </div>
                     <div class="clearfix"></div>
                     <hr />
                     <?php } ?>
                     <div class="col-md-6 border-right">
                        <?php $value = (isset($proposal) ? $proposal->subject : ''); ?>
                        <?php $attrs = (isset($proposal) ? array('onblur'=>"check_name(this.value,'subject')",'maxlength'=>"150",'onkeyup'=>"check_validate()") : array('autofocus'=>true,'onblur'=>"check_name(this.value,'subject')",'maxlength'=>"150",'onkeyup'=>"check_validate()")); ?>
                        <?php echo render_input('subject','proposal_subject',$value,'text',$attrs); ?>
						<div class="text-danger" id="subject_id" style="display:none">Please enter valid name</div>
                        <div class="form-group select-placeholder">
                           <label for="rel_type" class="control-label"><?php echo _l('proposal_related'); ?></label>
                           <select name="rel_type" id="rel_type" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                              <option value="">Choose Option</option>
                              <option value="lead" <?php if((isset($proposal) && $proposal->rel_type == 'lead') || $this->input->get('rel_type')){if($rel_type == 'lead'){echo 'selected';}} ?>><?php echo _l('proposal_for_lead'); ?></option>
                              <option value="customer" <?php if((isset($proposal) &&  $proposal->rel_type == 'customer') || $this->input->get('rel_type')){if($rel_type == 'customer'){echo 'selected';}} ?>><?php echo _l('proposal_for_customer'); ?></option>
                              <option value="project" <?php if((isset($proposal) &&  $proposal->rel_type == 'project') || $this->input->get('rel_type')){if($rel_type == 'project'){echo 'selected';}} ?>>Deal</option>
                           </select>
                        </div>
                        <div class="form-group select-placeholder<?php if($rel_id == ''){echo ' hide';} ?> " id="rel_id_wrapper">
                           <label for="rel_id"><span class="rel_id_label"></span></label>
                           <div id="rel_id_select">
                              <select name="rel_id" id="rel_id" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                              <?php if($rel_id != '' && $rel_type != ''){
                                 $rel_data = get_relation_data($rel_type,$rel_id);
                                 $rel_val = get_relation_values($rel_data,$rel_type);
                                    echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                                 } ?>
                              </select>
                           </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                              <?php $value = (isset($proposal) ? _d($proposal->date) : _d(date('Y-m-d'))) ?>
                              <?php echo render_date_input('date','proposal_date',$value); ?>
                          </div>
                          <div class="col-md-6">
                            <?php
                        $value = '';
                        if(isset($proposal)){
                          $value = _d($proposal->open_till);
                        } else {
                          if(get_option('proposal_due_after') != 0){
                              $value = _d(date('Y-m-d',strtotime('+'.get_option('proposal_due_after').' DAY',strtotime(date('Y-m-d')))));
                          }
                        }
                        echo render_date_input('open_till','proposal_open_till',$value); ?>
                          </div>
                        </div>
                        <?php
                           $selected = '';
                           $currency_attr = array('data-show-subtext'=>true);
                           foreach($currencies as $currency){
                            if($currency['isdefault'] == 1){
                              $currency_attr['data-base'] = $currency['id'];
                            }
                            if(isset($proposal)){
                              if($currency['id'] == $proposal->currency){
                                $selected = $currency['id'];
                              }
                              if($proposal->rel_type == 'customer'){
                                //$currency_attr['disabled'] = true;
                              }
                            } else {
                              if($rel_type == 'customer'){
                                $customer_currency = $this->clients_model->get_customer_default_currency($rel_id);
                                if($customer_currency != 0){
                                  $selected = $customer_currency;
                                } else {
                                  if($currency['isdefault'] == 1){
                                    $selected = $currency['id'];
                                  }
                                }
                                //$currency_attr['disabled'] = true;
                              } else {
                               if($currency['isdefault'] == 1){
                                $selected = $currency['id'];
                              }
                            }
                           }
                           }
                           $currency_attr = apply_filters_deprecated('proposal_currency_disabled', [$currency_attr], '2.3.0', 'proposal_currency_attributes');
                           $currency_attr = hooks()->apply_filters('proposal_currency_attributes', $currency_attr);
                           ?>
                           <div class="row">
                             <div class="col-md-6">
                                <div class="form-group select-placeholder">
                                  <label for="rel_type" class="control-label"><?php echo _l('proposal_currency'); ?></label>
                                  <select name="currencynew" id="currency1" class="selectpicker currencyswitcher1" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php
                                        foreach($currencies as $ac) {
                                            $selected = '';
                                            if(isset($proposal->currency)) {
                                              if($proposal->currency == $ac['id']) {
                                                  $selected = 'selected';
                                              }
                                            } else {
                                              if($base_currency->id == $ac['id']) {
                                                  $selected = 'selected';
                                              }
                                            }
                                            
                                    ?>
                                      <option value="<?php echo $ac['id']; ?>" <?php echo $selected;?> ><?php echo $ac['name']; ?></option>
                                    <?php  } ?>
                                  </select>
                                </div>
                              <?php
                              //echo render_select('currency', $currencies, array('id','name','symbol'), 'proposal_currency', $selected, $currency_attr);
                              ?>
                             </div>
                             <div class="col-md-6">
                               <div class="form-group select-placeholder">
                                 <!-- <label for="discount_type" class="control-label"><?php echo _l('discount_type'); ?></label>
                                 <select name="discount_type" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                  <option value="" selected><?php echo _l('no_discount'); ?></option>
                                  <option value="before_tax" <?php
                                  if(isset($estimate)){ if($estimate->discount_type == 'before_tax'){ echo 'selected'; }}?>><?php echo _l('discount_type_before_tax'); ?></option>
                                  <option value="after_tax" <?php if(isset($estimate)){if($estimate->discount_type == 'after_tax'){echo 'selected';}} ?>><?php echo _l('discount_type_after_tax'); ?></option>
                                </select> -->
                                 <label for="status" class="control-label"><?php echo _l('proposal_status'); ?></label>
                                 <?php
                                    $disabled = '';
                                    if(isset($proposal)){
                                     if($proposal->estimate_id != NULL || $proposal->invoice_id != NULL){
                                       $disabled = 'disabled';
                                     }
                                    }
                                    ?>
                                 <select name="status" class="selectpicker" data-width="100%" <?php echo $disabled; ?> data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php foreach($statuses as $status){ ?>
                                    <option value="<?php echo $status; ?>" <?php if((isset($proposal) && $proposal->status == $status) || (!isset($proposal) && $status == 0)){echo 'selected';} ?>><?php echo format_proposal_status($status,'',false); ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                            </div>
                            <?php if($protocol =='kataria.leadforce.mobi' || $protocol =='trial.leadforce.mobi' || $protocol =='localhost' ): ?>
                              <div class="col-md-6">
                                <?php echo render_select('pdftemplate',get_proposal_pdf_templates(),array('id','name'),'pdf_template',(!isset($proposal))?'proposalpdf':$proposal->pdftemplate,[],[],'','',false); ?>
                              </div>
                              
                            <?php endif; ?>
                            </div>
                        <?php $fc_rel_id = (isset($proposal) ? $proposal->id : false); ?>
                        <?php echo render_custom_fields('proposal',$fc_rel_id); ?>
                         <div class="form-group no-mbot">
                           <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
                           <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($proposal) ? prep_tags_input(get_tags_in($proposal->id,'proposal')) : ''); ?>" data-role="tagsinput">
                        </div>
                        <div class="form-group mtop10 no-mbot">
                            <p><?php echo _l('proposal_allow_comments'); ?></p>
                            <div class="onoffswitch">
                              <input type="checkbox" id="allow_comments" class="onoffswitch-checkbox" <?php if((isset($proposal) && $proposal->allow_comments == 1) || !isset($proposal)){echo 'checked';}; ?> value="on" name="allow_comments">
                              <label class="onoffswitch-label" for="allow_comments" data-toggle="tooltip" title="<?php echo _l('proposal_allow_comments_help'); ?>"></label>
                            </div>
                          </div>
                     </div>
                     <div class="col-md-6">
                        <!-- <div class="row">
                           <div class="col-md-6">
                              <div class="form-group select-placeholder">
                                 <label for="status" class="control-label"><?php echo _l('proposal_status'); ?></label>
                                 <?php
                                    $disabled = '';
                                    if(isset($proposal)){
                                     if($proposal->estimate_id != NULL || $proposal->invoice_id != NULL){
                                       $disabled = 'disabled';
                                     }
                                    }
                                    ?>
                                 <select name="status" class="selectpicker" data-width="100%" <?php echo $disabled; ?> data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php foreach($statuses as $status){ ?>
                                    <option value="<?php echo $status; ?>" <?php if((isset($proposal) && $proposal->status == $status) || (!isset($proposal) && $status == 0)){echo 'selected';} ?>><?php echo format_proposal_status($status,'',false); ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <?php
                                 $i = 0;
                                 $selected = '';
                                 foreach($staff as $member){
                                  if(isset($proposal)){
                                    if($proposal->assigned == $member['staffid']) {
                                      $selected = $member['staffid'];
                                    }
                                  }
                                  $i++;
                                 }
                                 echo render_select('assigned',$staff,array('staffid',array('firstname','lastname')),'proposal_assigned',$selected);
                                 ?>
                           </div>
                        </div> -->
                        <?php $value = (isset($proposal) ? $proposal->proposal_to : ''); ?>
                        <?php echo render_input('proposal_to','proposal_to',$value); ?>
                        <?php $value = (isset($proposal) ? $proposal->address : ''); ?>
                        <?php echo render_textarea('address','proposal_address',$value); ?>
                        <div class="row">
                           <div class="col-md-6">
                              <?php $value = (isset($proposal) ? $proposal->city : ''); ?>
                              <?php echo render_input('city','billing_city',$value); ?>
                           </div>
                           <div class="col-md-6">
                              <?php $value = (isset($proposal) ? $proposal->state : ''); ?>
                              <?php echo render_input('state','billing_state',$value); ?>
                           </div>
                           <div class="col-md-6">
                              <?php $countries = get_all_countries(); ?>
                              <?php $selected = (isset($proposal) ? $proposal->country : ''); ?>
                              <?php echo render_select('country',$countries,array('country_id',array('short_name'),'iso2'),'billing_country',$selected); ?>
                           </div>
                           <div class="col-md-6">
                              <?php $value = (isset($proposal) ? $proposal->zip : ''); ?>
                              <?php echo render_input('zip','billing_zip',$value); ?>
                           </div>
                           <div class="col-md-6">
                            
                              <div class="form-group" app-field-wrapper="email"><label for="email" class="control-label"> <small class="req text-danger">* </small>Email</label>
                                <input type="text" id="email" name="email" class="form-control ui-autocomplete-input" value="<?php echo $value = (isset($proposal) ? $proposal->email : ''); ?>" autocomplete="off" onkeyup="check_email(this.value,'email')"  multiple pattern="^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$">
                                <input type='hidden' id='selectuser_ids' />
                              </div>
                             
                           </div>
                           <div class="col-md-6">
                              <?php $value = (isset($proposal) ? $proposal->phone : ''); ?>
                              <?php echo render_input('phone','proposal_phone',$value); ?>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="btn-bottom-toolbar bottom-transaction text-right">
                  <!-- <p class="no-mbot pull-left mtop5 btn-toolbar-notice"><a href="#" style="color:blue;font-weight:bold;" id="choose_temp" for="template" class="control-label" >Choose Template</a>, Either it will send default template. -->
                                <?php //echo _l('include_proposal_items_merge_field_help','<b>{proposal_items}</b>'); ?></p>
                    <a href="#" id="choose_temp" for="template" class="mleft15" <?php echo (isset($proposal) && ($proposal->pdftemplate =='' || $proposal->pdftemplate !='proposalpdf'))?'style="display:none"':'' ?>>Click here to Choose Template</a>
                    <button type="button" class="btn btn-info mleft10 proposal-form-submit save-and-send transaction-submit" <?php echo (isset($proposal) && $proposal->pdftemplate !='proposalpdf')?'style="display:none"':'' ?>>
                        <?php echo _l('save_and_send'); ?>
                    </button>
                    <button class="btn btn-info mleft5 proposal-form-submit transaction-submit" type="button">
                      <?php echo _l('submit'); ?>
                    </button>
               </div>
               </div>
            </div>
         </div>
         <div class="col-md-12">
            <div class="panel_s">
               <?php $this->load->view('admin/estimates/_add_edit_items'); ?>
            </div>
         </div>
         <?php //echo form_close(); ?>
         <?php $this->load->view('admin/invoice_items/item'); ?>
            <?php echo form_close(); ?>
      </div>
      <div class="btn-bottom-pusher"></div>
   </div>
</div>
<div class="modal fade email-template" data-editor-id=".<?php echo 'tinymce-'.$proposal->id; ?>" id="proposal_send_to_customer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <?php //echo form_open('admin/proposals/send_to_email/'.$proposal->id); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close-send-template-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Select Templates to <?php echo _l('proposal_send_to_email_title'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
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
                        <?php echo render_textarea('email_template_custom','',$template->message,array(),array(),'','tinymce-'.$proposal->id); ?>
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
   var _rel_id = $('#rel_id'),
   _rel_type = $('#rel_type'),
   _rel_id_wrapper = $('#rel_id_wrapper'),
   data = {};

   $(function(){
    $(document).on('click', '#choose_temp', function() {
      $('#proposal_send_to_customer').modal({backdrop: 'static', keyboard: false});
    });
    $(document).on('click', '#btnSend', function() {
      var subject = $('#email_sub').val();
      var temp_id = $('#tempid').val();
      var message = tinyMCE.get('email_template_custom').getContent();;
      $('#proposal_send_to_customer').modal('hide');
      $('#email_template_custom1').val(message);
      $('#temp_id').val(temp_id);
      $('#email_sub1').val(subject);
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
    init_currency();
    // Maybe items ajax search
    init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'items/search');
    validate_proposal_form();
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
        if(response.country > 0) {
            $('select[name="country"]').selectpicker('val',response.country);
        } else {
            $('select[name="country"]').selectpicker('val','');
            $('select[name="country"]').selectpicker('refresh');
        }
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
                      $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Discount %</div><div class="">Total</div>');
                  } else {
                      $('#topheading').html('<div class="">Item</div><?php echo get_particulars_item_ordered_headers() ?><div class="">Price</div><div class="">Quantity</div><div class="">Total</div>');
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
                                    $('#topheading').html('<div class="">Item</div><div class="">Price</div><div class="">Quantity</div><div class="">Discount %</div><div class="">Total</div>');
                                } else {
                                    $('#topheading').html('<div class="">Item</div><div class="">Price</div><div class="">Quantity</div><div class="">Total</div>');
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
                            $('#topheading').html('<div class="">Item</div><div class="">Price</div><div class="">Quantity</div><div class="">Discount %</div><div class="">Total</div>');
                        } else {
                            $('#topheading').html('<div class="">Item</div><div class="">Price</div><div class="">Quantity</div><div class="">Total</div>');
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
    $('.rel_id_label').html(_rel_type.find('option:selected').text());
    _rel_type.on('change', function() {
      var clonedSelect = _rel_id.html('').clone();
      _rel_id.selectpicker('destroy').remove();
      _rel_id = clonedSelect;
      $('#rel_id_select').append(clonedSelect);
      proposal_rel_id_select();
      if($(this).val() != ''){
        _rel_id_wrapper.removeClass('hide');
      } else {
        _rel_id_wrapper.addClass('hide');
      }
      $('.rel_id_label').html(_rel_type.find('option:selected').text());
    });
    proposal_rel_id_select();
    <?php if(!isset($proposal) && $rel_id != ''){ ?>
      _rel_id.change();
      <?php } ?>


$( "#email" ).autocomplete({
        source: function(request, response) {
            var relid = $('#rel_id').val();
            var reltype = $('#rel_type').val();
            var searchText = extractLast(request.term);
            if(relid == '' && reltype == '') {
              alert('Select Related field to fetch email ids.');
              return false;
            }
                $.ajax({
                    url: admin_url + 'proposals/get_related_email_ids',
                    type: 'post',
                    dataType: "json",
                    data: {
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
        $('#toemail').val(trim);
        $('#selectuser_ids').val(trim);

        return false;
        },
        minLength: 3
});

});
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
    
    function proposal_rel_id_select(){
      var serverData = {};
      serverData.rel_id = _rel_id.val();
      data.type = _rel_type.val();
      init_ajax_search(_rel_type.val(),_rel_id,serverData);
    }
    $.validator.addMethod("checkTilldate",
      function (value, element, param) {
        var fit_start_time  = $("#date").val();
        var fit_end_time    = $("#open_till").val();
        if(fit_start_time <= fit_end_time){
            return true;
        }else{
          return false;
        }
      },'Open Till date should be greater than proposal date'
    );

   function validate_proposal_form(){
      appValidateForm($('#proposal-form'), {
        subject : 'required',
        proposal_to : 'required',
        rel_type: 'required',
        rel_id : 'required',
        date : {
          required:true,
          checkTilldate:true
        },
        open_till:{
          required:true,
          checkTilldate:true
        },
        email: {
         required:true
       },
       currency : 'required',
     });
   }

   function split( val ) {
     console.log(val);
      return val.split( /,\s*/ );
    }
    function extractLast( term ) {
      return split( term ).pop();
    }

function check_name(a,ch_id){
	$('#'+ch_id).val(a.trim());
}
function check_validate(){
	var name_val = $('#subject').val();
	$('#subject_id').hide();
	if ( name_val.match(/^[a-zA-Z0-9]+/)  ) {
	} else {
		$('#subject_id').show();
		return false;
	}
}
$(function () {
	$('form').on('submit', function () {
		var name_val = $('#subject').val();
		$('#subject_id').hide();
		if ( name_val.match(/^[a-zA-Z0-9]+/)  ) {
		} else {
			$('#subject_id').show();
			return false;
		}
        return true;
    });
});
$( "[name='pdftemplate']" ).change(function() {
  if($(this).val() =='proposalpdf'){
    $('#choose_temp').show();
    $('.save-and-send').show();
  }else{
    $('#choose_temp').hide();
    $('.save-and-send').hide();
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
</body>
</html>
