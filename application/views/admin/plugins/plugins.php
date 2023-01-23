<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    #wrapper{
        background-color: #f8fafb;
    }

    .plugin{
        background-color: #fff;
        border-radius: 10px;
        padding: 20px 15px;
        box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
        margin-bottom: 15px;
    }
    .plugin-logo{
        width: 100%;
    }
    .plugin-title h4{
        margin-bottom: 5px !important;
    }
    .plugin-description p{
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

</style>
<div id="wrapper">
    <div class="content">
        <h3 class="font-weight-bold">Integrations</h3>
        <p class="text-muted">Improve your workspace.</p>
        <div class="row plugins-wrapper">
            <!-- <div class="col-md-3">
                <div class="plugin">
                    <a href="<?php echo admin_url("plugin/facebook/leadads") ?>">
                    <div class="row">
                        <div class="col-xs-4"><img class="plugin-logo" src="<?php echo base_url('assets/images/pluginslogo/facebook.jpeg') ?>" alt=""></div>
                        <div class="col-xs-8">
                            <div class="plugin-title"><h4>Facebook lead ads</h4></div>
                            <div class="plugin-description"><p class="text-muted">Instant alerts for Facebook Lead Ads.</p></div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="plugin">
                    <a href="<?php echo admin_url("plugin/whatsapp") ?>">
                        <div class="row">
                            <div class="col-xs-4"><img class="plugin-logo" src="<?php echo base_url('assets/images/pluginslogo/whatsapp.jpeg') ?>" alt=""></div>
                            <div class="col-xs-8">
                                <div class="plugin-title"><h4>Whatsapp Business API</h4></div>
                                <div class="plugin-description"><p class="text-muted">Send Automated Updates, Reminders on WhatsApp & Provide seamless Customer Experience.</p></div></div>
                            </div>
                        </div>
                    </a>
            </div>
            <div class="col-md-3">
                <div class="plugin">
                    <a href="<?php echo admin_url("plugin/sms/daffytel") ?>">
                        <div class="row">
                            <div class="col-xs-4"><img class="plugin-logo" src="<?php echo base_url('assets/images/pluginslogo/daffytel.png') ?>" alt=""></div>
                            <div class="col-xs-8">
                                <div class="plugin-title"><h4>Daffytel IVR provider</h4></div>
                                <div class="plugin-description"><p class="text-muted">Daffytel Cloud Telephony in Chennai is one of the leading businesses in the Telecommunication Services.</p></div>
                            </div>
                        </div>
                    </a>
                </div>
            </div> -->
            <div class="col-md-3">
                <div class="plugin">
                    <a href="<?php echo admin_url("plugin/acres99") ?>">
                        <div class="row">
                            <div class="col-xs-4"><img class="plugin-logo" src="<?php echo base_url('assets/images/pluginslogo/99acres.jpeg') ?>" alt=""></div>
                            <div class="col-xs-8">
                                <div class="plugin-title"><h4>99Acres</h4></div>
                                <div class="plugin-description"><p class="text-muted">Online Platform To Real Estate Developers, Brokers and Owners For Listing Their Property.</p></div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>
</html>