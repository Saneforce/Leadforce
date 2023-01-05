<?php defined('BASEPATH') or exit('No direct script access allowed');?>

<style>
    :root{
        --theme-primary-dark: rgb(03,18,51);
        /* --theme-primary-dark: rgb(6 34 102); */
        --theme-primary-dark-hover: black;
        --theme-primary-light: #024a96;
        --theme-primary-light-hover: #01356b;
        --theme-default-text-color: rgb(03,18,51);
        --theme-primary-text-color: #032b63;
        --theme-info-dark: #058561;
        --theme-info-dark-hover: #036348;
    }
    body{
        background-color: transparent;
        color: var(--theme-default-text-color) !important;
    }

    /* width */
    ::-webkit-scrollbar {
        width: 5px;
    }
    ::-webkit-scrollbar:horizontal{
        height: 5px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
    box-shadow: inset 0 0 5px grey;
    border-radius: 10px;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
    background: #85878f;
    border-radius: 10px;
    }

    a{
        color: var(--theme-default-text-color);
    }
    a:hover{
        color: var(--theme-primary-light);;
    }

    .modal-header{
        
        background: var(--theme-primary-dark);
    }
    #appSidebar{
        background-color:var(--theme-primary-dark);
        width: 60px;
        height: 100vh;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    #appSidebar .sidebarApps{
        width: 100%;
        padding-top: 25px;
    }

    #appSidebar .sidebarApp{
        text-align: center;
        padding: 5px;
    }
    #appSidebar .sidebarApp a{
        padding: 10px;
        color: #fff;
        display: block;
        font-size: 16px;
        border-radius: 5px;
    }

    #appSidebar .sidebarApp a.active ,#appSidebar .sidebarApp a:hover{
        color: #fff;
        background-color: rgba(255, 255, 255, .3);
    }

    #wrapper{
        margin: 0px;
        width: calc( 100vw - 80px);
        background-color: #0312330f;
        min-height: unset !important;
    }
    #page-wrapper{
        display: flex;
    }
    #applogo{
        padding: 5px;
        /* border-bottom: 1px solid rgba(255, 255, 255, 0.7); */
        height: 60px;
        text-align: center;
    }

    #applogo a{
        height: 50px;
        text-align: center;
    }

    #applogo a img{
        height: 55px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        padding-bottom: 5px;
    }

    #topNav > .header-search{
        margin-bottom:0px;
    }
    .tooltip{
        z-index: 999;
    }

    .btn-primary{
        background-color: var(--theme-primary-light);
    }
    .btn-primary:hover, .btn-primary.active.focus, .btn-primary.active:focus, .btn-primary.active:hover, .btn-primary:active.focus, .btn-primary:active:focus, .btn-primary:active:hover, .open>.dropdown-toggle.btn-primary.focus, .open>.dropdown-toggle.btn-primary:focus, .open>.dropdown-toggle.btn-primary:hover{
        background-color: var(--theme-primary-light-hover) !important;
    }

    .btn-info{
        background-color: var(--theme-info-dark);
    }
    .btn-info:hover, .btn-info.active, .btn-info.active.focus, .btn-info.active:focus, .btn-info.active:hover, .btn-info:active.focus, .btn-info:active:focus, .btn-info:active:hover, .open>.dropdown-toggle.btn-info.focus, .open>.dropdown-toggle.btn-info:focus, .open>.dropdown-toggle.btn-info:hover{
        background-color: var(--theme-info-dark-hover) !important;
    }
    .btn-info.disabled, .btn-info.disabled.active, .btn-info.disabled.focus, .btn-info.disabled:active, .btn-info.disabled:focus, .btn-info.disabled:hover, .btn-info[disabled], .btn-info[disabled].active, .btn-info[disabled].focus, .btn-info[disabled]:active, .btn-info[disabled]:focus, .btn-info[disabled]:hover, fieldset[disabled] .btn-info, fieldset[disabled] .btn-info.active, fieldset[disabled] .btn-info.focus, fieldset[disabled] .btn-info:active, fieldset[disabled] .btn-info:focus, fieldset[disabled] .btn-info:hover{
        background-color: var(--theme-info-dark-hover) !important;
    }

    .dataTables_length select:focus, .uneditable-input:focus, input[type=color]:focus, input[type=date]:focus, input[type=datetime-local]:focus, input[type=datetime]:focus, input[type=email]:focus, input[type=file]:focus, input[type=month]:focus, input[type=number]:focus, input[type=password]:focus, input[type=search]:focus, input[type=tel]:focus, input[type=text]:focus, input[type=time]:focus, input[type=url]:focus, input[type=week]:focus, select.form-control:focus, textarea.form-control:focus{
        border-color: var(--theme-primary-dark);
    }

    /* .onoffswitch-checkbox:checked+.onoffswitch-label, .onoffswitch-checkbox:checked+.onoffswitch-label:before{
        background-color: #84c529;
    } */

    .onoffswitch-checkbox:checked+.onoffswitch-label {
        background-color: var(--theme-info-dark);
    }

    .btn-success{
        background-color: var(--theme-info-dark);
    }
    #topNav{
        position: sticky;
        top: 0px;
        background: #fff;
        z-index: 10;
        box-shadow: 0 2px 2px -2px rgba(0,0,0,.2);
        border-radius: 0px;
        height: 60px;
    }
    #navbarwrapper{
        display: flex;
        justify-content: space-between;
    }
    #topNav .header-search{
        width: 40vw;
    }
    #topNav .header-search > li{
        width: 100%;
    }

    .header-search-top.width350{
        left: unset;
        margin-left: 0px;
        width: 31vw;
    }
    .header-notifications .dropdown-toggle.notifications-icon{
        color: var(--theme-primary-text-color);
    }
    .header-user-profile-full-name{
        color: var(--theme-primary-text-color);
    }
    #setup-menu-wrapper{
        left: 60px;
        background-color: #fff;
        color: var(--theme-primary-text-color);
        width: unset !important;
    }
    #setup-menu-wrapper a{
        color: var(--theme-primary-text-color) !important;
        text-transform: none !important;
    }

    #setup-menu-wrapper .customizer-heading{
        color: var(--theme-primary-text-color) !important;
    }

    .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover{
        background-color: var(--theme-primary-light);
        border-color: var(--theme-primary-light);
    }
    .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover{
        background-color: var(--theme-primary-light-hover);
        border-color: var(--theme-primary-light-hover);
    }

    .pagination>li>a, .pagination>li>span{
        color: var(--theme-primary-light);
    }

    .alpha-filter .active a, .alpha-filter .active a:focus, .alpha-filter .active a:hover{
        background-color: var(--theme-primary-light);
    }


    .sidebarChild{
        position: absolute;
        left: 60px;
        background-color: var(--theme-primary-dark);
        min-width: 160px;
        padding: 10px;
        top: unset;
        margin-top: -40px;
    }
    .sidebarChildApp{
        margin-bottom: 5px;
    }

    .btn-bottom-toolbar{
        width: calc(100% - 60px);
    }
    #setup-menu-wrapper .nav.metis-menu{
        width: 243px;
        height: calc( 100vh - 66px );
        overflow-y: auto;
    }
    #wrapper.wrapperstylechange{
        width: calc( 100vw - 300px);
    }
    #setup-menu-wrapper{
        max-height: calc( 100vh - 66px );
        position: fixed;
    }
    #page-body-wrapper{
        width: 100%;
    }
    #setup-menu li .nav-second-level li.active a{
        border-radius: 5px;
        display: block;
        margin: 8px 15px 8px 15px;
        background-color: var(--theme-primary-light);
    }
    .dropdown-menu>.active>a, .dropdown-menu>.active>a:focus, .dropdown-menu>.active>a:hover{
        background-color: var(--theme-primary-light);
    }

    #setup-menu li.active>a{
        color: #333!important;
        background-color: #e3e8ee;
    }

    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover, .navbar-pills.nav-tabs>li>a:focus, .navbar-pills.nav-tabs>li>a:hover {
        border-bottom: 1px solid var(--theme-primary-light);
    }

    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover, .nav-tabs>li>a:focus, .nav-tabs>li>a:hover {
        border-bottom: 1px solid var(--theme-primary-light);
        color: var(--theme-primary-light);
    }
    .nav-tabs-horizontal li.active a .badge, .nav-tabs-horizontal li:hover a .badge {
        background-color: var(--theme-primary-light);
    }
    .lead-info-heading h4 {
        color: var(--theme-primary-light);
    }
</style>


<div id="appSidebar">
    <div id="applogo">
        <a href="<?php echo site_url(); ?>" class="logo ">
            <img src="<?php echo base_url('uploads/company/logo1.png'); ?>" class="" alt="<?php echo html_escape(get_option('companyname')); ?>">
        </a>
    </div>

    <ul class="sidebarApps">
        <?php 
            $isActive = false;
            $uri = $this->uri->segment(3);
        ?>
        <?php foreach($sidebar_menu as $key => $item): ?>
        <?php 
            if(isset($uri) && $uri == 'view_contact') {
                $fetch = 'all_contacts';
            } elseif (isset($uri) && $uri == 'emailmanagement') {
                $fetch = 'email';
            } else {
                $fetch = $this->router->fetch_class();
            }
        ?>
        <li class="sidebarApp">
            <?php 
                $extraclass="" ;
                $extra ='';
                if(count($item['children']) > 0 ){ 
                    $extraclass="dropright" ;
                    $extra ='data-toggle="dropdown" aria-expanded="false"';
                } 
            ?>


            <a href="<?php echo count($item['children']) > 0 ? '#' : $item['href']; ?>" class="<?php echo ($item['slug'] == $fetch)?'active':''; ?> <?php echo $$extraclass ?>" <?php echo $extra ?> data-toggle="tooltip" data-placement="right" data-html="true"  data-original-title="<?php echo $item['name'] ?>">
                <i class="<?php echo $item['icon']; ?>  fa-fw fa-lg"></i>
            </a>
            <?php if(count($item['children']) > 0): ?>
                <ul class="sidebarChild dropdown-menu animated fadeIn" aria-expanded="false">
                    <?php foreach($item['children'] as $submenu):?>
                    <li class="sidebarChildApp">
                        <?php if($submenu['name']=='Add Report'): ?>
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#add_report_popup" >
                                <?php echo $submenu['name'] ?>
                            </a>
                        <?php elseif($submenu['name']=='View Report'): ?>
                            <a href="<?php echo $submenu['href']; ?>" class="<?php echo ($submenu['slug'] == $fetch)?'active':''; ?>">
                                <?php echo $submenu['name'] ?>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo $submenu['href']; ?>" class="<?php echo ($submenu['slug'] == $fetch)?'active':''; ?>">
                                <?php echo $submenu['name'] ?>
                            </a>
                        <?php endif; ?>
                        
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
</div>

<div class="modal fade" id="add_report_popup" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-wrapper">
		<div class="modal-dialog" style="width:50%">
			<div class="modal-content">
				<div class="modal-header bg-blue">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><?php echo _l('add_new_report');?></h4>
				</div>
				<div class="col-md-12 bg-white" style="border-radius:6px;">
					<form action="<?php echo admin_url('reports/add_report'); ?>" method="post" id="head_report" enctype='multipart/form-data' >
					<div class="col-md-12"style="border-bottom:2px solid #e5e5e5;margin-bottom:15px;">
					<div class="col-md-5" style="margin-top:10px;">
						<p class="p_head_1"><?php echo _l('choose_entity');?></p>
						<div class="tabs active1_1 tabs_div_report" id="tab012" style="border-radius:10px;" onclick="tabs_div_report('tab012')">
							<h6 class="text-muted_1"><span class="cur_deal_1"><i class="fa fa-dollar"></i></span><?php echo _l('deal');?><div class="pull-right dol_sym_1"><i class="fa fa-angle-right" style="font-size:40px;"></div></i></h6>
						</div>
						<div class="tabs tabs_div_report" id="tab022" style="border-radius:10px;" onclick="tabs_div_report('tab022')">
							<h6 class="text-muted_1"><span class="cur_deal_1"><i class="fa fas fa-tasks"></i></span><?php echo _l('activity');?><div class="pull-right dol_sym_1"><i class="fa fa-angle-right" style="font-size:40px;"></div></i></h6>
						</div>
						<div class="modal-footer"></div>
					</div>
					<div class="col-md-7" style="border-left:2px solid #e5e5e5;margin-top:10px;">
						<p class="p_head_1"><?php echo _l('choose_report_type');?></p>
						<fieldset id="tab0121" class="show report_popup" >
								<input type="hidden" id="report_12_id" name="report_12_id">
								<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
								<div class="modal-body">
									<div class="form-group">
										<div class="full_cont_div_1 req_class" onclick="show_div_1('cur_div1','performance')" id="cur_div1">
											<div class="first_cont_div_1"><?php echo _l('performance');?></div>
											<div class="second_cont_div_1 req_class" id="cur_div11">How much did you start, win, or lose?</div>
										</div>
									</div>
									<div class="form-group">
										<div class="full_cont_div_1 req_class" onclick="show_div_1('cur_div2','conversion')" id="cur_div2">
											<div class="first_cont_div_1"><?php echo _l('conversion');?></div>
											<div class="second_cont_div_1 req_class" id="cur_div21">What is your win or loss rate?</div>
										</div>
									</div>
									<div class="form-group">
										<div class="full_cont_div_1 req_class" onclick="show_div_1('cur_div3','duration')" id="cur_div3">
											<div class="first_cont_div_1"><?php echo _l('duration');?></div>
											<div class="second_cont_div_1 req_class" id="cur_div31">How long is your sales cycle?</div>
										</div>
									</div>
									<div class="form-group">
										<div class="full_cont_div_1 req_class" onclick="show_div_1('cur_div4','progress')" id="cur_div4">
											<div class="first_cont_div_1"><?php echo _l('progress');?></div>
											<div class="second_cont_div_1 req_class" id="cur_div41">Are your deals moving forward in pipeline?</div>
										</div>
									</div>
								</div>
							
						</fieldset>	
						<fieldset id="tab0221" class="hide report_popup" >
								<div class="modal-body">
									<div class="form-group">
										<div class="full_cont_div_1 req_class" onclick="show_div_1('activity_div1','activity_performance')" id="activity_div1">
											<div class="first_cont_div_1"><?php echo _l('activity_performance');?></div>
											<div class="second_cont_div_1 req_class" id="activity_div11">How many activities were Added ,Completed and Planned?</div>
										</div>
									</div>
									<div class="form-group">
										<div class="full_cont_div_1 req_class" onclick="show_div_1('activity_div2','email_performance')" id="activity_div2">
											<div class="first_cont_div_1"><?php echo _l('email_performance');?></div>
											<div class="second_cont_div_1 req_class" id="activity_div21">How many Email activities were Added ,Completed and Planned?</div>
										</div>
									</div>
									<div class="form-group">
										<div class="full_cont_div_1 req_class" onclick="show_div_1('activity_div3','call_performance')" id="activity_div3">
											<div class="first_cont_div_1"><?php echo _l('call_performance');?></div>
											<div class="second_cont_div_1 req_class" id="activity_div31">How many Call activities were Added ,Completed and Planned?</div>
										</div>
									</div>
								</div>
							
						</fieldset>	
					</div>
					</div>
					<div class="modal-footer" style="background:#f7f7f7">
						<div>
							<button type="button" class="btn pull-right1" onclick="report_cancel()"><?php echo _l('cancel');?></button>
							<button type="submit" class="btn btn-info pull-right1" disabled id="btn_report_popup"><?php echo _l('continue');?></button>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
    function report_cancel(){
        $('#add_report_popup').modal('hide');
    }
    function tabs_div_report(a){
        $('.tabs_div_report').removeClass('active1_1');
        $('#'+a).addClass('active1_1');
        $('.report_popup').removeClass('show');
        $('.report_popup').addClass('hide');
        $('.req_class').removeClass('active_new_1');
        $('#'+a+'1').removeClass('hide');
        $('#'+a+'1').addClass('show');
        $('#btn_report_popup').prop('disabled', true);
    }
    function show_div_1(a,b){
        $('#report_12_id').val(b);
        $('#goal_txt').html(b);
        $('label[class="error"]').hide();
        $('#select_manger1').hide();
        $('#assign_user_wise').hide();
        $('#select_user1').hide();
        $('#month_int').hide();
        $('#all_int').show();
        $('#goal_val').val(b);
        $('.req_class').removeClass('active_new_1');
        $('#'+a).addClass('active_new_1');
        $('#'+a+'1').addClass('active_new_1');
        $(':button').prop('disabled', false);
        
        $('#pipeline_stage').val('');
        $('#select_deal').val('');
        $('#select_deal_new').val('');
        $('#pipeline_stage').selectpicker('refresh');
        $('#select_deal').selectpicker('refresh');
        $('#select_deal_new').selectpicker('refresh');
    }
</script>

<style>
.active1_1{
	background: #e6effb;
}
.active_new_1{
	background-color: var(--theme-primary-light) !important;
    color: #ffff !important;
}
.half_width_1{
	width:48%;
	float:left;
}
.mar_10_1{
	margin-left:10px;
}
.text-muted_1 {
    
    min-height: 54px;
    text-align: center;
    border-radius: 4px;
    display: flex;
    align-items: center;
    padding: 8px 10px;
    margin-bottom: 2px;
    font-weight: 800; 
    box-sizing: border-box;
}
.p_head_1{
    font-size: 15px;
    text-transform: uppercase;
    font-weight: 500;
}
.full_cont_div_1:hover {
    background: #eee;
}
.full_cont_div_1{
	white-space:nowrap;
	overflow:hidden;
	align-items:center;
	cursor:pointer;
	display:grid;
	min-height:54px;
	padding-bottom:7px;
	padding-top:7px;
    border-radius: 5px;
}
.first_cont_div_1{
	text-overflow:ellipsis;
	overflow:hidden;
	font-size:16px;
	margin-left:10px;
}
.second_cont_div_1{
	font-size:14px;
	white-space:initial;
	color:#747678;
	padding-right:22px;
	margin-left:12px;
}
.tabs_div_report{
	cursor:pointer;
}
.cur_deal_1 {
    height: 35px;
    width: 35px;
    border-radius: 50%;
    display: inline-block;
    background-color: #468DDD;
    color: #ffff;
    margin-right: 20px;
    align-items: center;
    padding: 10px;
}
.dol_sym_1{
	position:absolute;
	right:30px;
}
</style>
<style>
/* Absolute Center Spinner */
#overlay5 {
  position: fixed;
  z-index: 999;
  overflow: show;
  margin: auto;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 50px;
  height: 50px;
}

/* Transparent Overlay */
#overlay5:before {
  content: '';
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255,255,255,0.5);
}

/* :not(:required) hides these rules from IE9 and below */
#overlay5:not(:required) {
  /* hide "loading..." text */
  font: 0/0 a;
  color: transparent;
  text-shadow: none;
  background-color: transparent;
  border: 0;
}

#overlay5:not(:required):after {
  content: '';
  display: block;
  font-size: 10px;
  width: 50px;
  height: 50px;
  margin-top: -0.5em;

  border: 3px solid rgba(33, 150, 243, 1.0);
  border-radius: 100%;
  border-bottom-color: transparent;
  -webkit-animation: spinner 1s linear 0s infinite;
  animation: spinner 1s linear 0s infinite;


}

/* Animation */

@-webkit-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-moz-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-o-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

@-webkit-keyframes rotation {
   from {-webkit-transform: rotate(0deg);}
   to {-webkit-transform: rotate(359deg);}
}
@-moz-keyframes rotation {
   from {-moz-transform: rotate(0deg);}
   to {-moz-transform: rotate(359deg);}
}
@-o-keyframes rotation {
   from {-o-transform: rotate(0deg);}
   to {-o-transform: rotate(359deg);}
}
@keyframes rotation {
   from {transform: rotate(0deg);}
   to {transform: rotate(359deg);}
}

#connect_email-error, #email_er_data{
	color:red !important;
}
.content-center1 {
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
	position:fixed;
	width:100%;
    background-color: rgba(255,255,255,0.5);
	top:0;
	left:0;
	right:0;
	bottom:0;
	z-index:999;
}
.content-center1:before {
  content: '';
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255,255,255,0.5);
}

.pulse i {
    color: #fff
}

.pulse {
    height: 100px;
    width: 100px;
    background-color: #0069e8;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative
}

.pulse::before {
    content: "";
    position: absolute;
    border: 1px solid #0069e8;
    width: calc(100% + 40px);
    height: calc(100% + 40px);
    border-radius: 50%;
    animation: pulse 1s linear infinite
}

.pulse::after {
    content: "";
    position: absolute;
    border: 1px solid #0069e8;
    width: calc(100% + 40px);
    height: calc(100% + 40px);
    border-radius: 50%;
    animation: pulse 1s linear infinite;
    animation-delay: 0.3s
}

@keyframes pulse {
    0% {
        transform: scale(0.5);
        opacity: 0
    }

    50% {
        transform: scale(1);
        opacity: 1
    }

    100% {
        transform: scale(1.3);
        opacity: 0
    }
}

</style>