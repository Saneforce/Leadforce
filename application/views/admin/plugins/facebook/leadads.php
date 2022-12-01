<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open(admin_url('plugin/facebook/leadads'),array('id'=>'facebookleadgenform')) ?>
                        <!-- <div id="fb-root"></div> -->
                        <div class="text-center">
                            <div class="fb-login-button" data-width="" data-size="large" data-button-type="login_with" data-layout="default" data-auto-logout-link="false" data-use-continue-as="true" data-scope="pages_manage_ads,ads_management,pages_read_engagement,pages_show_list,pages_manage_metadata"></div>
                        </div>
                        <hr class="hr-panel-heading">
                        <div class="row">
                            <div id="pagelist"  class="col-md-6" style="display: none;">
                                <div class="form-group" >
                                    <label for="page" class="control-label "><small class="req text-danger">* </small><?php echo _l('facebook_page') ?></label>
                                    <select name="page" id="page" class="form-control selectpicker" required>
                                    </select>
                                </div>
                            </div>
                            <div id="formlist" class="col-md-6" style="display: none;">
                                <div class="form-group" >
                                    <label for="form" class="control-label "><small class="req text-danger">* </small><?php echo _l('leadgen_form') ?></label>
                                    <select name="form" id="form" class="form-control selectpicker" required>
                                    </select>
                                </div>
                            </div>
                        </div>
                        

                        <div id="assingFields" style="display: none;">
                            <hr class="hr-panel-heading">
                            <h5>Assign fields</h5>
                            <p class="text-light">Lead Fields</p>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name" class="control-label"><small class="req text-danger">* </small><?php echo _l('lead_add_edit_name') ?></label>
                                    <select name="name" id="name" class="form-control selectpicker assignfield" data-live-search="true" required data-live-search="true">
                                    </select>
                                </div>
                                <?php echo render_select('view_source',$lead_sources,array('id','name'),"lead_source",$selected,array('required'=>'required'),array(),'col-md-6'); ?>
                                <div class="form-group col-md-6" >
                                    <label for="title" class="control-label"><?php echo _l('lead_title') ?></label>
                                    <select name="title" id="title" class="form-control selectpicker assignfield" data-live-search="true">
                                    </select>
                                </div>
                                <div class="form-group col-md-6" >
                                    <label for="email" class="control-label"><?php echo _l('lead_email') ?></label>
                                    <select name="email" id="email" class="form-control selectpicker assignfield" data-live-search="true">
                                    </select>
                                </div>
                                <div class="form-group col-md-6" >
                                    <label for="website" class="control-label"><?php echo _l('lead_website') ?></label>
                                    <select name="website" id="website" class="form-control selectpicker assignfield" data-live-search="true">
                                    </select>
                                </div>
                                <div class="form-group col-md-6" >
                                    <label for="phonenumber" class="control-label"><?php echo _l('lead_add_edit_phonenumber') ?></label>
                                    <select name="phonenumber" id="phonenumber" class="form-control selectpicker assignfield" data-live-search="true">
                                    </select>
                                </div>
                                <div class="form-group col-md-6" >
                                    <label for="address" class="control-label"><?php echo _l('lead_address') ?></label>
                                    <select name="address" id="address" class="form-control selectpicker assignfield" data-live-search="true">
                                    </select>
                                </div>
                                <div class="form-group col-md-6" >
                                    <label for="city" class="control-label"><?php echo _l('lead_city') ?></label>
                                    <select name="city" id="city" class="form-control selectpicker assignfield" data-live-search="true">
                                    </select>
                                </div>
                                <div class="form-group col-md-6" >
                                    <label for="state" class="control-label"><?php echo _l('lead_state') ?></label>
                                    <select name="state" id="state" class="form-control selectpicker assignfield" data-live-search="true">
                                    </select>
                                </div>
                                <div class="form-group col-md-6" >
                                    <label for="country" class="control-label"><?php echo _l('country') ?></label>
                                    <select name="country" id="country" class="form-control selectpicker assignfield" data-live-search="true">
                                    </select>
                                </div>
                                <div class="form-group col-md-6" >
                                    <label for="zip" class="control-label"><?php echo _l('lead_zip') ?></label>
                                    <select name="zip" id="zip" class="form-control selectpicker assignfield" data-live-search="true">
                                    </select>
                                </div>
                                <div class="form-group col-md-6" >
                                    <label for="description" class="control-label"><?php echo _l('lead_description') ?></label>
                                    <select name="description" id="description" class="form-control selectpicker assignfield" data-live-search="true">
                                    </select>
                                </div>
                            </div>
                            
                            <?php $customFields =get_custom_fields("leads"); ?>
                            <?php if($customFields): ?>
                                <p class="text-light">Lead Custom Fields</p>
                                <div class="row">
                                <?php foreach ($customFields as $key => $value): ?>
                                    <div class="form-group col-md-6" >
                                    <label for="" class="control-label"><?php echo $value['required']?'<small class="req text-danger">* </small>':''; echo $value['name']; ?></label>
                                    <select name="customfields[<?php echo $value['fieldto']?>][<?php echo $value['id']?>]" class="form-control selectpicker assignfield" data-live-search="true" <?php echo $value['required']?"required":''?>>
                                    </select>
                                    </div>
                                <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <button class="btn btn-primary" id="saveleadgenconfig">Save</button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
    <script>
        var configId ="<?php echo $config_id ?>";
        document.addEventListener("DOMContentLoaded", function(event) { 
            var accessToken ='';
            var appId ='415646190764150';
            var userID ='';
            var pages ={};
            var leadgenforms ={};
            window.fbAsyncInit = function() {
                FB.init({
                    appId            : appId,
                    autoLogAppEvents : true,
                    xfbml            : true,
                    version          : 'v15.0',
                });
                FB.getLoginStatus(function(response) {
                    if (response.status === 'connected') {
                        accessToken = response.authResponse.accessToken;
                        userID = response.authResponse.userID;
                        getFbPages(userID,accessToken);
                    } 
                } );
                function getFbPages(userID,accessToken){
                    $.ajax({
                        url: "<?php echo admin_url('plugin/facebook/get_pages')?>?configId="+configId+"&userID="+userID+"&access_token="+accessToken,
                        type: 'GET',
                        dataType: 'json', // added data type
                        success: function(res) {
                            if(res.success ==true){
                                pages =res.data.data;
                                $('#pagelist').show();
                                $('#pagelist [name="page"]').html('');
                                $('#pagelist [name="page"]').append(`<option value="">Select Page</option>`);
                                $.each(res.data.data, function(key, form) {
                                    $('#pagelist [name="page"]').append(`<option value="`+key+`">`+form.name+`</option>`);
                                });
                                $('#pagelist [name="page"]').selectpicker('refresh');
                            }
                        }
                    });
                }

                function getFbForms(pagekey){
                    var pageDetails =pages[pagekey];
                    $.ajax({
                        url: "<?php echo admin_url('plugin/facebook/get_leadgen_forms')?>?pageId="+pageDetails.id+"&page_access_token="+pageDetails.access_token,
                        type: 'GET',
                        dataType: 'json', // added data type
                        success: function(res) {
                            if(res.success ==true){
                                leadgenforms =res.data.data;
                                $('#formlist').show();
                                $('#formlist [name="form"]').html('');
                                $('#formlist [name="form"]').append(`<option value="">Select Form</option>`);
                                $.each(res.data.data, function(key, page) {
                                    $('#formlist [name="form"]').append(`<option value="`+key+`">`+page.name+`</option>`);
                                });
                                $('#formlist [name="form"]').selectpicker('refresh');
                            }
                        }
                    });
                }

                function getFbFormDetails(formid){
                    var pagekey =$('#pagelist [name="page"]').val();
                    var pageDetails =pages[pagekey];
                    var leadgenformdetail =leadgenforms[formid];
                    $.ajax({
                        url: "<?php echo admin_url('plugin/facebook/get_leadgen_form_details')?>?formId="+leadgenformdetail.id+"&page_access_token="+pageDetails.access_token,
                        type: 'GET',
                        dataType: 'json', // added data type
                        success: function(res) {
                            $('#assingFields').show();
                            if(res.success ==true){
                                $('#assingFields select.assignfield').html('');
                                $('#assingFields select.assignfield').append(`<option value="">Select Value</option>`);
                                $.each(res.data.questions, function(key, field) {
                                    $('#assingFields select.assignfield').append(`<option value="`+field.key+`">`+field.label+`</option>`);
                                });
                                $('#assingFields select.assignfield').selectpicker('refresh');
                            }
                        }
                    });
                }

                $('#pagelist [name="page"]').change(function(){
                    $('#assingFields').hide();
                    getFbForms($(this).val())
                });

                $('#formlist [name="form"]').change(function(){
                    getFbFormDetails($(this).val())
                });

                appValidateForm($('#facebookleadgenform'),
                    {
                        
                    },
                    function(form) {
                        var data =$(form).serializeArray();
                        var pageId =$('#pagelist [name="page"]').val();
                        data[0].value =pages[pageId];
                        var formId =$('#pagelist [name="page"]').val();
                        data[1].value =leadgenforms[formId];
                        $.ajax({
                            url: form.action,
                            type: form.method,
                            data: data,
                            dataType:'Json',
                            success: function(response) {
                                if(response.success ==true)
                                    alert_float('success','Configured Successfully');
                            }            
                        });
                    }
                );

            };
        });
    </script>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
</body>
</html>