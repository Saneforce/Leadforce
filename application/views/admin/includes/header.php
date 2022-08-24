<?php defined('BASEPATH') or exit('No direct script access allowed');
ob_start();
$top_search_area = ob_get_contents();
ob_end_clean();
?>
<div class="content-center1" id="overlay_12" style="display:none">
    <div class="pulse"> <i class="fa fa-phone fa-5x" aria-hidden="true"></i> </div>
</div>
<div id="header">

   <div id="logo">
        <a href="<?php echo site_url(); ?>" class="logo img-responsive">
            <img src="<?php echo base_url('uploads/company/logo1.png'); ?>" class="img-responsive" alt="<?php echo html_escape(get_option('companyname')); ?>">
        </a>
   </div>
   <ul class="header-search  navbar-left">
      <li class="icon header-search timer-button" data-placement="bottom" >
         <a href="#" id="header-search-ion" class="dropdown top-timers" data-toggle="dropdown">
            <input type="search" id="header_gsearch_top" name="header_gsearch_top" class="form-control input-sm" value="<?php echo (isset($globalsearch)?$globalsearch:''); ?>" placeholder="Search Leadforce..."/>
         </a>
         <div class="dropdown-menu animated fadeIn header-search-top width350" id="THheader_gsearch_top">
         <?php $this->load->view('admin/includes/header_gsearch_result_top'); ?>

         </div>
      </li>
   </ul>
   <ul class="nav navbar-nav navbar-left">
     <li class="divider-vertical"></li>
      <?php
         hooks()->do_action('before_render_aside_menu');
         $isActive = false;
         $keycount = 0;
         ?>
      <?php
      foreach($sidebar_menu as $key => $item){
         $uri = $this->uri->segment(3);
         if(isset($uri) && $uri == 'view_contact') {
            $fetch = 'all_contacts';
         } elseif (isset($uri) && $uri == 'emailmanagement') {
            $fetch = 'email';
         } else {
            $fetch = $this->router->fetch_class();
         }
         
         $keycount++;
         if(isset($item['collapse']) && count($item['children']) === 0) {
           continue;
         }
         if(!$isActive && $item['slug'] == $fetch){
            $isActive = true;
         }
         ?>
      <li class="icon header-<?php echo $item['slug']; ?> <?php echo ($item['slug'] == $fetch)?'active':''; ?>">
		<?php if($item['name'] != 'Email'){?>
			<a href="<?php echo count($item['children']) > 0 ? '#' : $item['href']; ?>" 
			 <?php if(count($item['children']) > 0 ){ ?>
			  class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"
			 <?php } ?> >
		<?php }else{ ?>
			<?php if(get_option('connect_mail') =='yes'){ ?>
				<a href="<?php echo count($item['children']) > 0 ? '#' : $item['href']; ?>" 
				<?php if(count($item['children']) > 0 ){ ?>
				  class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"
				 <?php } ?> >
			<?php }else{?>
				<a href="<?php echo site_url().'admin/outlook_mail/index';?>" class=""  >
			<?php }?>
		<?php }?>
        
             <i class="<?php echo $item['icon']; ?>  fa-fw fa-lg"></i>
             <span class="menu-text">
             <?php echo _l($item['name'],'', false); ?>
             </span>
             <?php if(count($item['children']) > 0){ ?>
             <b class="caret"></b>
             <?php } ?>
         </a>
		 <?php 
		 $staffid = get_staff_user_id();
		  $cur_sql = "SELECT ".db_prefix()."shared.id FROM ".db_prefix()."shared LEFT JOIN ".db_prefix()."report ON ".db_prefix()."shared.report_id = ".db_prefix()."report.id WHERE ".db_prefix()."shared.share_type = 'Everyone' OR ".db_prefix()."shared.id in(SELECT share_id FROM ".db_prefix()."shared_staff where staff_id = '".$staffid."')";
		 $ch_shared = $this->db->query($cur_sql)->result_array();
		 ?>
         <?php if(count($item['children']) > 0){ ?>
         <ul class="dropdown-menu animated fadeIn" aria-expanded="false">
            <?php foreach($item['children'] as $submenu){
				if($submenu['name'] !='Shared Report List' || !empty($ch_shared)){
               ?>
            <li class="sub-menu-item-<?php echo $submenu['slug']; ?>">
				<?php if($submenu['name']=='Add Report'){?>
					<a href="javascript:void(0)" data-toggle="modal" data-target="#add_report_popup" >
				<?php }else{?>
					<a href="<?php echo $submenu['href']; ?>">
				<?php }if(!empty($submenu['icon'])){ ?>
               <i class="<?php echo $submenu['icon']; ?> menu-icon"></i>
               <?php } ?>
               <span class="sub-menu-text">
                  <?php echo _l($submenu['name'],'',false); ?>
               </span>
               </a>
            </li>
            <?php }
			}
			?>
         </ul>
         <?php } ?>
      </li>
      <?php if(count((array)$sidebar_menu) > $keycount){ ?>
      <li class="divider-vertical"></li>
      <?php } ?>
      <?php hooks()->do_action('after_render_single_aside_menu', $item); ?>
      <?php } 
	  $staffid = get_staff_user_id();
	  $ch_admin = is_admin($staffid);
	  /*if(!$ch_admin){ ?>
		<li class="icon header-email ">
			<?php if(get_option('company_mail_server') != 'yes'){?>
				<a href="<?php echo site_url().'/admin/company_mail/check_user_mail';?>">
			<?php }else{?>
				<a href="<?php echo site_url().'/admin/company_mail/check_company_mail';?>">
			<?php }?>
				<i class="fa fa-envelope  fa-fw fa-lg"></i>
				<span class="menu-text"><?php  echo _l('Email');?></span>
			</a>
        </li>
	  <?php }*/?>
     
      <?php hooks()->do_action('after_render_aside_menu'); ?>
      <?php $this->load->view('admin/projects/pinned'); ?>
   </ul>
  
   <nav>
      <div class="small-logo">
         <span class="text-primary">
            <?php get_company_logo(get_admin_uri().'/') ?>
         </span>
      </div>
      <div class="mobile-menu">
         <button type="button" class="navbar-toggle visible-md visible-sm visible-xs mobile-menu-toggle collapsed" data-toggle="collapse" data-target="#mobile-collapse" aria-expanded="false">
            <i class="fa fa-chevron-down"></i>
         </button>
         <ul class="mobile-icon-menu">
            <?php
               // To prevent not loading the timers twice
            if(is_mobile()){ ?>
               <li class="dropdown notifications-wrapper header-notifications">
                  <?php $this->load->view('admin/includes/notifications'); ?>
               </li>
               <li class="header-timers">
                  <a href="#" id="top-timers" class="dropdown-toggle top-timers" data-toggle="dropdown"><i class="fa fa-clock-o fa-fw fa-lg"></i>
                     <span class="label bg-success icon-total-indicator icon-started-timers<?php if ($totalTimers = count($startedTimers) == 0){ echo ' hide'; }?>"><?php echo count($startedTimers); ?></span>
                  </a>
                  <ul class="dropdown-menu animated fadeIn started-timers-top width300" id="started-timers-top">
                     <?php $this->load->view('admin/tasks/started_timers',array('startedTimers'=>$startedTimers)); ?>
                  </ul>
               </li>
            <?php } ?>
         </ul>
         <div class="mobile-navbar collapse" id="mobile-collapse" aria-expanded="false" style="height: 0px;" role="navigation">
            <ul class="nav navbar-nav">
               <li class="header-my-profile"><a href="<?php echo admin_url('profile'); ?>"><?php echo _l('nav_my_profile'); ?></a></li>
               <?php /* ?>
               <li class="header-my-timesheets"><a href="<?php echo admin_url('staff/timesheets'); ?>"><?php echo _l('my_timesheets'); ?></a></li>
                <?php */ ?>
               <li class="header-edit-profile"><a href="<?php echo admin_url('staff/edit_profile'); ?>"><?php echo _l('nav_edit_profile'); ?></a></li>
                
               <?php if(is_staff_member()){ ?>
               <?php /* ?>
                  <li class="header-newsfeed">
                   <a href="#" class="open_newsfeed mobile">
                     <?php echo _l('whats_on_your_mind'); ?>
                  </a>
               </li><?php */ ?>
            <?php } ?>
            <li class="header-logout"><a href="#" onclick="logout(); return false;"><?php echo _l('nav_logout'); ?></a></li>
         </ul>
      </div>
   </div>
   
   <ul class="nav navbar-nav navbar-right">
      <?php
      if(!is_mobile()){
       echo $top_search_area;
    } ?>
    <?php hooks()->do_action('after_render_top_search'); ?>
    <li class="icon header-user-profile" data-toggle="tooltip" title="<?php echo $staff_full_name = get_staff_full_name(); ?>" data-placement="bottom">
      <a href="#" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="false">
         <?php echo staff_profile_image($current_user->staffid,array('img','img-responsive','staff-profile-image-small','pull-left')); ?>
		 <?php 
		 $ch_prof_name = $staff_full_name;
			
			$ch_prof_count = strlen($ch_prof_name);
			if($ch_prof_count<=21){
				$ch_prof_name = $ch_prof_name;
			}else{
				$ch_prof_name = substr($ch_prof_name,0,21).'...';
			}
		?>
         <span class="header-user-profile-full-name  pull-left"><?php echo $ch_prof_name; ?> <b class="caret"></b> <small><?php echo get_option('companyname');?></small>     </sapn>
     
      </a>
      <ul class="dropdown-menu animated fadeIn">
         <li class="header-my-profile"><a href="<?php echo admin_url('profile'); ?>"><?php echo _l('nav_my_profile'); ?></a></li>
          <?php /* ?>
         <li class="header-my-timesheets"><a href="<?php echo admin_url('staff/timesheets'); ?>"><?php echo _l('my_timesheets'); ?></a></li>
           * <?php */ ?>
         <li class="header-edit-profile"><a href="<?php echo admin_url('staff/edit_profile'); ?>"><?php echo _l('nav_edit_profile'); ?></a></li>
		 <?php $staffid = get_staff_user_id();
		  $ch_admin = is_admin($staffid);
		  //if(!$ch_admin){ ?>
			<li class="header-edit-profile">
				<?php if(get_option('company_mail_server') != 'yes'){?>
					<a href="<?php echo admin_url('company_mail/email_settings'); ?>">
				<?php }else{?>
					<a href="<?php echo admin_url('company_mail/company_mail_setting'); ?>">
				<?php }?>
					<?php echo _l('Email Setting'); ?>
				</a>
			</li>
		<?php //}?>
		<?php if(get_option('reminder_settings') == 'user'){?>
				<li class="header-edit-profile">
					<a href="<?php echo admin_url('reminder/user'); ?>">
						<?php echo _l('reminder_settings'); ?>
					</a>
				</li>
		<?php }?>
        <?php /* ?>
 <?php if(get_option('disable_language') == 0){ ?>
            <li class="dropdown-submenu pull-left header-languages">
               <a href="#" tabindex="-1"><?php echo _l('language'); ?></a>
               <ul class="dropdown-menu dropdown-menu">
                  <li class="<?php if($current_user->default_language == ""){echo 'active';} ?>"><a href="<?php echo admin_url('staff/change_language'); ?>"><?php echo _l('system_default_string'); ?></a></li>
                  <?php foreach($this->app->get_available_languages() as $user_lang) { ?>
                     <li<?php if($current_user->default_language == $user_lang){echo ' class="active"';} ?>>
                     <a href="<?php echo admin_url('staff/change_language/'.$user_lang); ?>"><?php echo ucfirst($user_lang); ?></a>
                  <?php } ?>
               </ul>
            </li>
         <?php } ?>
         * <?php */ ?>
         <?php if($this->app->show_setup_menu() == true && ( is_admin())){ ?>
      <li<?php if(get_option('show_setup_menu_item_only_on_hover') == 1) { echo ' style="display:none;"'; } ?> id="setup-menu-item">
         <a href="#" class="open-customizer">
         <span class="menu-text">
            <?php echo _l('setting_bar_heading'); ?>
            <?php
                if ($modulesNeedsUpgrade = $this->app_modules->number_of_modules_that_require_database_upgrade()) {
                  echo '<span class="badge menu-badge bg-warning">' . $modulesNeedsUpgrade . '</span>';
                }
            ?>
         </span>
         </a>
      </li>
       <?php } ?>
         <li class="header-logout">
            <a href="#" onclick="logout(); return false;"><?php echo _l('nav_logout'); ?></a>
         </li>
      </ul>
   </li>
   <?php if(is_staff_member()){ ?>
   <?php /* ?>
      <li class="icon header-newsfeed">
         <a href="#" class="open_newsfeed desktop" data-toggle="tooltip" title="<?php echo _l('whats_on_your_mind'); ?>" data-placement="bottom"><i class="fa fa-share fa-fw fa-lg" aria-hidden="true"></i></a>
      </li><?php */ ?>
   <?php } ?>
   <!-- <li class="icon header-todo">
      <a href="<?php echo admin_url('todo'); ?>" data-toggle="tooltip" title="<?php echo _l('nav_todo_items'); ?>" data-placement="bottom"><i class="fa fa-check-square-o fa-fw fa-lg"></i>
         <span class="label bg-warning icon-total-indicator nav-total-todos<?php if($current_user->total_unfinished_todos == 0){echo ' hide';} ?>"><?php echo $current_user->total_unfinished_todos; ?></span>
      </a>
   </li> -->
   <?php /* ?>
   <li class="icon header-timers timer-button" data-placement="bottom" data-toggle="tooltip" data-title="<?php echo _l('my_timesheets'); ?>">
      <a href="#" id="top-timers" class="dropdown-toggle top-timers" data-toggle="dropdown">
         <i class="fa fa-clock-o fa-fw fa-lg" aria-hidden="true"></i>
         <span class="label bg-success icon-total-indicator icon-started-timers<?php if ($totalTimers = count($startedTimers) == 0){ echo ' hide'; }?>">
            <?php echo count($startedTimers); ?>
         </span>
      </a>
      <ul class="dropdown-menu animated fadeIn started-timers-top width350" id="started-timers-top">
         <?php $this->load->view('admin/tasks/started_timers',array('startedTimers'=>$startedTimers)); ?>
      </ul>
   </li><?php */ ?>
   <li class="dropdown notifications-wrapper header-notifications" data-toggle="tooltip" title="<?php echo _l('nav_notifications'); ?>" data-placement="bottom">
      <?php $this->load->view('admin/includes/notifications'); ?>
   </li>
</ul>
 
</nav>
</div>
<div id="mobile-search" class="<?php if(!is_mobile()){echo 'hide';} ?>">
   <ul>
      <?php
      if(is_mobile()){
       echo $top_search_area;
    }
     ?>
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
						<div class="tabs active1_1" id="tab01" style="border-radius:10px;">
							<h6 class="text-muted_1"><span class="cur_deal_1"><i class="fa fa-dollar"></i></span><?php echo _l('deal');?><div class="pull-right dol_sym_1"><i class="fa fa-angle-right" style="font-size:40px;"></div></i></h6>
						</div>
						<div class="modal-footer"></div>
					</div>
					<div class="col-md-7" style="border-left:2px solid #e5e5e5;margin-top:10px;">
						<p class="p_head_1"><?php echo _l('choose_report_type');?></p>
						<fieldset id="tab0113" class="show" style="display:block !important">
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
					</div>
					</div>
					<div class="modal-footer" style="background:#f7f7f7">
						<div>
							<button type="button" class="btn pull-right1" onclick="report_cancel()"><?php echo _l('cancel');?></button>
							<button type="submit" class="btn btn-primary pull-right1" disabled style="background-color:#61c786 !important;" ><?php echo _l('continue');?></button>
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
function show_div_1(a,b){
	$('#report_12_id').val(b);
	$('#goal_txt').html(b);
	//$('.error').hide();
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
	background-color: #468DDD !important;
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
	border-radius:10px;
	padding-bottom:7px;
	padding-top:7px;
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
.navbar-nav .divider-vertical {
   height: 63px;
   border-right: 1px solid #0251b0;
   border-left: 1px solid #5f8fca;
}
#wrapper {
    margin: 0 0 0 0px;
}
#header {
    background: #0069e8;
}
.navbar-nav > li > a {
    color: #FFF;
}
.header-search-top {
    margin: 0;
}
#header_gsearch_top{
   background: #4574ACCC;
}
#header_gsearch_top:active,#header_gsearch_top:focus{
   background: #FFF;
}

.navbar-nav.navbar-right {
    margin-right: 5px;
}
.header-search {
    padding: 6px 6px;
    width: 280px;
    margin-left: -10px;
}
.navbar-nav > li > a > i{
   display: block;
   margin: 10px auto -15px;
   font-size: 23px;
}
.navbar-nav > li.active > a {
   background: #09121c45;
}
#logo img {
    height: 50px;
    margin-top: -3px;
}
.content {
    /* padding: 10px 15px 25px 15px; */
}
.navbar-left .pinned_project .progress-bar-mini{
   margin-bottom: 5px;
}
.navbar-left li.pinned_project{
   min-width:250px;
}
#logo {
    padding: 10px 30px;
}
.header-user-profile-full-name{
   display: table;
   margin-top: -10px;
   margin-left: 10px;
}
.header-user-profile-full-name small{
   display: block;
   margin-top: -35px;
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
