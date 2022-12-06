<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .border{
        border: 1px solid #eee;
        padding: 25px 10px 50px 10px;
        border-radius: 10px;
    }

    #fbpages{
        max-height: 240px;
        overflow-y: auto;
    }

    #fbpages label {
        width: 100%;
    }

    #fbpages .card-input-element {
        display: none;
    }

    #fbpages .card-input {
        border: 1px solid #eee;
        padding: 15px 10px;
        border-radius: 10px;
    }
    #fbpages .card-input img{
        border-radius: 50px;
    }

    #fbpages .card-input:hover {
        cursor: pointer;
    }

    #fbpages .card-input-element:checked + .card-input {
        background-color: #dceafa;
    }
    .fb-logout-button{
        font-size: 16px;
        background-color: #1877f2;
        color: #fff;
        text-transform: none !important;
    }
    #saveleadgenconfig{
        background-color: #1877f2;
        color: #fff;
    }
    .fb-logout-button:hover,#saveleadgenconfig:hover{
        color: #fff;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- <div id="fb-root"></div> -->
                        <?php echo form_open(admin_url('plugin/facebook/connectleadads'),array('id'=>'facebookleadgenform')) ?>
                        <div class="row">
                            <div id="facebookLoginWrapper" class="col-md-6 col-md-offset-3">
                                <div class="card ">
                                    <div class="card-body border text-center">
                                        <img src="<?php echo base_url('assets/images/pluginslogo/facebook.jpeg') ?>" alt="">
                                        <h4>Facebook Lead Ads</h4>
                                        <p class="text-muted">Have new leads who sign up through Facebook, automatically uploaded into Leads.</p>
                                        <br><br>
                                        <div class="fb-login-button" data-width="" data-size="large" data-button-type="login_with" data-layout="default" data-auto-logout-link="false" data-use-continue-as="false" data-scope="pages_manage_ads,ads_management,pages_read_engagement,pages_show_list,pages_manage_metadata,leads_retrieval"></div>
                                        <div class="fb-logout-button btn" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div id="fbpageswrapper" style="display: none;">
                                    <div class="">
                                        <h5><small class="req text-danger">* </small>Facebook Pages</h5>
                                        <p class="text-muted">Select your facebook page.</p>
                                    </div>
                                    <div class="row" id="fbpages">
                                    
                                    </div>
                                </div>
                                
                                <br><br>
                                <div class="row" id="formlist" style="display: none;">
                                    <div class="col-md-12">
                                        <h5><small class="req text-danger">* </small>Page Ad Forms</h5>
                                        <p class="text-muted">Select your facebook page's ad form.</p>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group" >
                                            <select name="form" id="form" class="form-control selectpicker" required>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                        <div id="assingFields" style="display: none;">
                            <hr class="hr-panel-heading">
                            <h5>Assign fields</h5>
                            <p class="text-muted">Assign facebook page ad form fields to lead form fields.</p>
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
                                    <label for="email" class="control-label">Email</label>
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
                            
                            <button class="btn pull-right" id="saveleadgenconfig">Save Configuration</button>
                            <a href="<?php echo admin_url('plugin/facebook/leadads') ?>" class="btn pull-right btn-default" style="margin-right: 15px;">Cancel</a>
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
        document.addEventListener("DOMContentLoaded", function(event) { 
            var accessToken ='';
            var appId ='415646190764150';
            var userID ='';
            var pages ={};
            var leadgenforms ={};
            var facebookLoginName ='';
            window.fbAsyncInit = function() {
                FB.init({
                    appId            : appId,
                    autoLogAppEvents : true,
                    xfbml            : true,
                    version          : 'v15.0',
                });
                FB.Event.subscribe('auth.login', function() {
                    location.reload();
                });
                FB.getLoginStatus(function(response) {
                    if (response.status === 'connected') {
                        accessToken = response.authResponse.accessToken;
                        $('.fb-login-button').hide();
                        $('#facebookLoginWrapper').removeClass('col-md-offset-3');
                        FB.api('/me', {fields: 'name'},function(response) {
                            $('.fb-logout-button').html(`<i class="fa fa-sign-out" aria-hidden="true"></i> Log out `+response.name);
                            $('.fb-logout-button').show();
                            facebookLoginName =response.name;
                        });
                        userID = response.authResponse.userID;
                        getFbPages(userID,accessToken);
                    }else{
                        $('.fb-logout-button').hide();
                        $('#facebookLoginWrapper').addClass('col-md-offset-3');
                        $('.fb-login-button').show();
                        facebookLoginName ="";
                    }
                } );

                function getPageProfileLink(pageId,pageAccessToken){
                    var url ='';
                    $.ajax({
                        url: "<?php echo admin_url('plugin/facebook/get_page_profilelink')?>?pageId="+pageId+"&page_access_token="+pageAccessToken,
                        type: 'GET',
                        dataType: 'json', // added data type
                        async:false,
                        success: function(res) {
                            if(res.success ==true){
                                if(res.data.data.url){
                                    url= res.data.data.url;
                                }
                            }
                        }
                    });
                    return url;
                }
                function getFbPages(userID,accessToken){
                    $.ajax({
                        url: "<?php echo admin_url('plugin/facebook/get_pages')?>?userID="+userID+"&access_token="+accessToken,
                        type: 'GET',
                        dataType: 'json', // added data type
                        success: function(res) {
                            if(res.success ==true){
                                pages =res.data.data;
                                $('#fbpages').html('');
                                $.each(res.data.data, function(key, page) {
                                    var pageprofilelink =getPageProfileLink(page.id,page.access_token);
                                    $('#fbpages').append(`
                                        <div class="col-md-4">
                                            <label>
                                                <input type="radio" name="page" value="`+key+`" class="card-input-element" />
                                                <div class="card-input text-center">
                                                    <img src="`+pageprofilelink+`" alt="">
                                                    <h5>`+page.name+`</h5>
                                                </div>
                                            </label>
                                        </div>
                                    `);
                                });
                                $('#fbpageswrapper').show();
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
                    var pagekey =$('#fbpages [name="page"]').val();
                    var pageDetails =pages[pagekey];
                    var leadgenformdetail =leadgenforms[formid];
                    $.ajax({
                        url: "<?php echo admin_url('plugin/facebook/get_leadgen_form_details')?>?formId="+leadgenformdetail.id+"&page_access_token="+pageDetails.access_token,
                        type: 'GET',
                        dataType: 'json', // added data type
                        success: function(res) {
                            $('.leadgen-error').remove();
                            if(res.success ==true){
                                $('#assingFields').show();
                                $('#assingFields select.assignfield').html('');
                                $('#assingFields select.assignfield').append(`<option value="">Nothing selected</option>`);
                                $.each(res.data.questions, function(key, field) {
                                    $('#assingFields select.assignfield').append(`<option value="`+field.key+`">`+field.label+`</option>`);
                                });
                                $('#assingFields select.assignfield').selectpicker('refresh');
                            }else{
                                $('#assingFields').hide();
                                $('#formlist [name="form"]').parent().append(`<span class="text-danger leadgen-error">`+res.msg+`</span>`);
                            }
                        }
                    });
                }

                $('body').on('change', '#fbpages [name="page"]', function() {
                    $('#assingFields').hide();
                    getFbForms($(this).val())
                });

                $('#formlist [name="form"]').change(function(){
                    getFbFormDetails($(this).val())
                });

                $('.fb-logout-button').click(function(){
                    FB.logout(function(response) {
                        location.reload();
                    });
                })

                appValidateForm($('#facebookleadgenform'),
                    {
                        
                    },
                    function(form) {
                        var formData =$(form).serializeArray();
                        var pageId =$('#fbpages [name="page"]').val();
                        formData[0].value =pages[pageId]['name'];
                        var formId =$('#formlist [name="form"]').val();
                        formData[1].value =leadgenforms[formId]['name'];
                        var data = {};
                        data['user_id'] =userID;
                        data['page_id'] =pages[pageId]['id'];
                        data['form_id'] =leadgenforms[formId]['id'];
                        data['config']={};
                        formData.forEach(function(value, key){
                            data['config'][value.name] = value.value;
                        });
                        data['config']['facebookLoginName'] =facebookLoginName;
                        $.ajax({
                            url: form.action,
                            type: form.method,
                            data: data,
                            dataType:'Json',
                            success: function(response) {
                                if(response.success ==true){
                                    alert_float('success','Configured Successfully');
                                    setTimeout(function(){  
                                        window.location.href = admin_url+'plugin/facebook/leadads';
                                    }, 500);
                                }else{
                                    alert_float('danger','Something went wrong try again later.');
                                }
                            }            
                        });
                    }
                );
            };
        });

        
        // $("#saveleadgenconfig" ).mouseenter(function(e) {
        //     $(this).toggleClass("pull-right");
        // });
    </script>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
</body>
</html>