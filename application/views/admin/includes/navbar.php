<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" id="topNav" style="margin-bottom:0px">
    <div id="navbarwrapper">
    <div><a class="navbar-brand" href="#" style="padding-left: 25px;padding-top: 20px;"><?php echo isset($title)?$title:'' ?></a></div>
    <div>
    <ul class="header-search  navbar-left">
        <li class="icon header-search timer-button" data-placement="bottom">
            <a href="#" id="header-search-ion" class="dropdown top-timers" data-toggle="dropdown">
                <input type="search" id="header_gsearch_top" name="header_gsearch_top" class="form-control input-sm" value="<?php echo (isset($globalsearch) ? $globalsearch : ''); ?>" placeholder="Search Leadforce..." />
            </a>
            <div class="dropdown-menu animated fadeIn header-search-top width350" id="THheader_gsearch_top">
                <?php $this->load->view('admin/includes/header_gsearch_result_top'); ?>

            </div>
        </li>
    </ul>
    </div>


    <div>
    <nav>
        <div class="small-logo">
            <span class="text-primary">
                <?php get_company_logo(get_admin_uri() . '/') ?>
            </span>
        </div>
        <div class="mobile-menu">
            <button type="button" class="navbar-toggle visible-md visible-sm visible-xs mobile-menu-toggle collapsed" data-toggle="collapse" data-target="#mobile-collapse" aria-expanded="false">
                <i class="fa fa-chevron-down"></i>
            </button>
            <ul class="mobile-icon-menu">
                <?php
                // To prevent not loading the timers twice
                if (is_mobile()) { ?>
                    <li class="dropdown notifications-wrapper header-notifications">
                        <?php $this->load->view('admin/includes/notifications'); ?>
                    </li>
                    <li class="header-timers">
                        <a href="#" id="top-timers" class="dropdown-toggle top-timers" data-toggle="dropdown"><i class="fa fa-clock-o fa-fw fa-lg"></i>
                            <span class="label bg-success icon-total-indicator icon-started-timers<?php if ($totalTimers = count($startedTimers) == 0) {
                                                                                                        echo ' hide';
                                                                                                    } ?>"><?php echo count($startedTimers); ?></span>
                        </a>
                        <ul class="dropdown-menu animated fadeIn started-timers-top width300" id="started-timers-top">
                            <?php $this->load->view('admin/tasks/started_timers', array('startedTimers' => $startedTimers)); ?>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
            <div class="mobile-navbar collapse" id="mobile-collapse" aria-expanded="false" style="height: 0px;" role="navigation">
                <ul class="nav navbar-nav">
                    <li class="header-my-profile"><a href="<?php echo admin_url('profile'); ?>"><?php echo _l('nav_my_profile'); ?></a></li>
                    <li class="header-edit-profile"><a href="<?php echo admin_url('staff/edit_profile'); ?>"><?php echo _l('nav_edit_profile'); ?></a></li>

                    <?php if (is_staff_member()) { ?>
                    <?php } ?>
                    <li class="header-logout"><a href="#" onclick="logout(); return false;"><?php echo _l('nav_logout'); ?></a></li>
                </ul>
            </div>
        </div>

        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown notifications-wrapper header-notifications" data-toggle="tooltip" title="<?php echo _l('nav_notifications'); ?>" data-placement="bottom">
                <?php $this->load->view('admin/includes/notifications'); ?>
            </li>
            <li class="dropdown notifications-wrapper header-notifications" data-toggle="tooltip" title="<?php echo _l('nav_notifications'); ?>" data-placement="bottom">
            <a href="#" class="dropdown-toggle notifications-icon" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog" aria-hidden="true"></i></a>
                
            </li>
            <?php
            if (!is_mobile()) {
                echo $top_search_area;
            }
            hooks()->do_action('after_render_top_search'); ?>
            <li class="icon header-user-profile" data-toggle="tooltip" title="<?php echo $staff_full_name = get_staff_full_name(); ?>" data-placement="bottom">
                <a href="#" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="false">
                    <?php echo staff_profile_image($current_user->staffid, array('img', 'img-responsive', 'staff-profile-image-small', 'pull-left'));
                    $ch_prof_name = $staff_full_name;

                    $ch_prof_count = strlen($ch_prof_name);
                    if ($ch_prof_count <= 21) {
                        $ch_prof_name = $ch_prof_name;
                    } else {
                        $ch_prof_name = substr($ch_prof_name, 0, 21) . '...';
                    }
                    ?>
                    <span class="mleft10 header-user-profile-full-name  pull-left"><?php echo $ch_prof_name; ?> <b class="caret"></b></sapn>

                </a>
                <ul class="dropdown-menu animated fadeIn">
                    <li class="header-my-profile"><a href="<?php echo admin_url('profile'); ?>"><?php echo _l('nav_my_profile'); ?></a></li>
                    <li class="header-edit-profile"><a href="<?php echo admin_url('staff/edit_profile'); ?>"><?php echo _l('nav_edit_profile'); ?></a></li>
                    <?php $staffid = get_staff_user_id();
                    $ch_admin = is_admin($staffid);
                    ?>
                    <li class="header-edit-profile">
                        <?php if (get_option('company_mail_server') != 'yes') { ?>
                            <a href="<?php echo admin_url('company_mail/email_settings'); ?>">
                            <?php } else { ?>
                                <a href="<?php echo admin_url('company_mail/company_mail_setting'); ?>">
                                <?php } ?>
                                <?php echo _l('Email Setting'); ?>
                                </a>
                    </li>
                    <?php if (get_option('reminder_settings') == 'user') { ?>
                        <li class="header-edit-profile">
                            <a href="<?php echo admin_url('reminder/user'); ?>">
                                <?php echo _l('reminder_settings'); ?>
                            </a>
                        </li>
                    <?php }
                    if ($this->app->show_setup_menu() == true && (is_admin())) { ?>
                        <li <?php if (get_option('show_setup_menu_item_only_on_hover') == 1) {
                                echo ' style="display:none;"';
                            } ?> id="setup-menu-item">
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
        </ul>

    </nav>
    </div>
    </div>
</nav>