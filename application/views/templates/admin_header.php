<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport"    content="width=device-width, initial-scale=1.0">
		<meta name="robots" content="noindex, follow">
		
		<title><?=($szMetaTagTitle != '' ? "Tally Administration: $szMetaTagTitle" : "Tally Administration")?></title>		
		<link rel="shortcut icon" href="<?=__BASE_ASSETS_URL__?>/images/favicon.ico" type="image/x-icon">
		<link rel="icon" href="<?=__BASE_ASSETS_URL__?>/images/favicon.ico" type="image/x-icon">
		
		<link rel="stylesheet" href="<?=__BASE_ASSETS_URL__?>/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?=__BASE_ASSETS_URL__?>/css/font-awesome.min.css">
		<link rel="stylesheet" href="<?=__BASE_ASSETS_URL__?>/css/bootstrap-datepicker.css">

		<!-- Custom styles for our template -->
		<link rel="stylesheet" href="<?=__BASE_ASSETS_URL__?>/css/main.css?<?=filemtime(__APP_PATH_ASSETS__."/css/main.css")?>">
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="<?=__BASE_ASSETS_URL__?>/js/html5shiv.js"></script>
		<script src="<?=__BASE_ASSETS_URL__?>/js/respond.min.js"></script>
		<![endif]-->
	</head>

	<body>
		<header class="navbar-fixed-top headroom">
			<div class="container">
				<div class="row">
					<div class="col-xs-5">
						<div class="navbar-brand wow fadeInDown"><a href="<?=__BASE_ADMIN_URL__?>"><img src="<?=__BASE_ASSETS_URL__?>/images/logo.png" alt="Tally"></a></div>
					</div>
				
					<div class="col-xs-7 header-option">
						<?php if($is_admin_login){?>
						<a href="<?=__BASE_ADMIN_URL__?>/logout" class="last">Log Out(<?=$_SESSION['arr']['login']?>)</a>
						<?php } else {?>
						<a href="<?=__BASE_ADMIN_URL__?>/login" class="last">Log In</a>
						<?php }?>
					</div>
				</div>
			</div>
		</header>
		<section class="main-section">
			<div class="container">
				<div id="confirmationModal" class="modal fade" role="dialog">
				  <div class="modal-dialog">
				    <!-- Modal content-->
				    <form id="frmConfirm" name="frmConfirm" method="post" action="">
				     <div class="modal-content">
				      <div class="modal-header">
				      	<button type="button" class="close" data-dismiss="modal">&times;</button>
				      	<p>Are you sure?</p>
				      </div>
				      <div class="modal-body">
				      	<p class="confirm-msg"><?=sanitize_post_field_value($_POST['confirm']['p_msg'])?></p>
				      	<div class="form-group clearfix<?=($confirm_error != "" ? " has-error" : "")?>">
				      		<label>Please type "<span class="confirm-type"><?=sanitize_post_field_value($_POST['confirm']['p_func'])?></span>" to confirm.</label>
				      		<input type="text" name="confirm[p_re_func]" id="p_re_func" value="<?=sanitize_post_field_value($_POST['confirm']['p_re_func'])?>" placeholder="Confirmation text" class="form-control required re-match" autocomplete="off">
				      		<?=($confirm_error != "" ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$confirm_error}</span>" : "")?>				      		
				      	</div>
				      	<div class="row clearfix">
				      		<div class="col-sm-6"><div class="form-group"><input type="submit" name="p_submit" value="Confirm" class="btn btn-full btn-form-submit"></div></div>
				      		<div class="col-sm-6"><div class="form-group"><input type="button" name="p_submit" value="Cancel" class="btn btn-full btn-gray" onclick="hide_modal('confirmationModal');"></div></div>
				      	</div>
				      </div>
				     </div>
				     <input type="hidden" name="confirm[p_id]" id="p_id" value="<?=sanitize_post_field_value($_POST['confirm']['p_id'])?>">
				     <input type="hidden" name="confirm[p_func]" id="p_func" value="<?=sanitize_post_field_value($_POST['confirm']['p_func'])?>">
				     <input type="hidden" name="confirm[p_sub_func]" id="p_sub_func" value="<?=sanitize_post_field_value($_POST['confirm']['p_sub_func'])?>">
				     <input type="hidden" name="confirm[p_msg]" id="p_msg" value="<?=sanitize_post_field_value($_POST['confirm']['p_msg'])?>">
				  	</form>
				  </div>
			    </div>
				  
				<?php if($show_leftmenu){?>
				<div class="admin-page clearfix">
					<div class="left-menu">
						<ul>
							<li<?=($active_menu == "dashboard" ? ' class="active"' : '')?>><a href="<?=__BASE_ADMIN_URL__?>">Dashboard</a></li>
							<li<?=($active_menu == "users" ? ' class="active"' : '')?>><a href="<?=__BASE_ADMIN_URL__?>/users/list">Manage Users</a></li>
							<li<?=($active_menu == "message" ? ' class="active"' : '')?>><a href="<?=__BASE_ADMIN_URL__?>/users/message">Send Message</a></li>
							<li<?=($active_menu == "reports" ? ' class="active"' : '')?>><a href="<?=__BASE_ADMIN_URL__?>/reports">View Reports</a></li>							
							<li<?=($active_menu == "export" ? ' class="active"' : '')?>><a href="<?=__BASE_ADMIN_URL__?>/download/files">Download Files</a></li>
							<li<?=($active_menu == "sms-templates" ? ' class="active"' : '')?>><a href="<?=__BASE_ADMIN_URL__?>/configurations/templates">SMS Templates</a></li>
							<li<?=($active_menu == "configurations" ? ' class="active"' : '')?>><a href="<?=__BASE_ADMIN_URL__?>/configurations/constants/list">Configurations</a></li>
							<li><a href="<?=__BASE_ADMIN_URL__?>/change-password">Change Password</a></li>
							<li><a href="<?=__BASE_ADMIN_URL__?>/logout">Logout(<?=$_SESSION['arr']['login']?>)</a></li>
						</ul>
					</div>
					<div class="page-content">
				<?php }?>