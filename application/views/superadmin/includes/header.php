<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<!-- Meta, title, CSS, favicons, etc. -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>LEADFORCE - <?php echo $pagetitle; ?></title>
		<base href="<?php echo base_url();?>">
		<!-- Bootstrap -->
		<link href="<?php echo base_url();?>assets/superadmin/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
		<!-- Font Awesome -->
		<link href="<?php echo base_url();?>assets/superadmin/font-awesome/css/font-awesome.min.css" rel="stylesheet">
		<!-- Custom Theme Style -->
		<link href="<?php echo base_url();?>assets/superadmin/css/custom.css" rel="stylesheet">
	</head>
	<body class="nav-md">
		<div class="container body">
			<div class="main_container">
				<div class="col-md-3 left_col">
					<div class="left_col scroll-view">
						<div class="navbar nav_title" style="border: 0;">
							<a href="<?php echo site_url(); ?>/superadmin/company" class="col-md-12">
								<img class="img-responsive" src="assets/superadmin/images/logo.png" alt="leadforce" style="width: 50%;margin-left: 20%;margin-top : 15px;"/>
							</a>
						</div>
						<div class="clearfix"></div>
						<!-- menu profile quick info -->
						<br/>
						<!-- sidebar menu -->
						<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
							<div class="menu_section">
								<ul class="nav side-menu">
									<li><a href="<?php echo site_url(); ?>/superadmin/company"><i class="fa fa-building" aria-hidden="true"></i> Company </a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>

				<!-- top navigation -->
				<div class="top_nav">
					<div class="nav_menu">
						<nav>
							<div class="nav toggle">
								<a id="menu_toggle"><i class="fa fa-bars"></i></a>
							</div>
							<ul class="nav navbar-nav navbar-right">
								<li class="">
									<a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
										<?php echo $admins->name ;?>
										<span class=" fa fa-angle-down"></span>
									</a>
									<ul class="dropdown-menu dropdown-usermenu pull-right">
										<li><a href="<?php echo site_url(); ?>/superadmin/logout"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
									</ul>
								</li>
							</ul>
						</nav>
					</div>
				</div>
				<!-- /top navigation -->

				<!-- page content -->
				<div class="right_col" role="main">
					<?php $success = $this->session->flashdata('success_msg');
					$error = $this->session->flashdata('error_msg');
					$warning = $this->session->flashdata('warn_msg');
					$message = $this->session->flashdata('message');
					if(!empty($success)) { ?>
						<div class="alert alert-success alert-dismissible fade in" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
							</button>
							<?php echo $success;?>
						</div>
					<?php } if(!empty($error)) { ?>
						<div class="alert alert-danger alert-dismissible fade in" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
							</button>
							<?php echo $error;?>
						</div>
					<?php } if(!empty($warning)) { ?>
						<div class="alert alert-warning alert-dismissible fade in" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
							</button>
							<?php echo $warning;?>
						</div>
					<?php } if(!empty($message)) { ?>
						<div class="alert alert-info alert-dismissible fade in" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
							</button>
							<?php echo $message;?>
						</div>
					<?php }
							$this->session->unset_userdata('success_msg');
							$this->session->unset_userdata('error_msg');
							$this->session->unset_userdata('warn_msg');
							$this->session->unset_userdata('message');
					?>
				