<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport"    content="width=device-width, initial-scale=1.0">
		<meta name="robots" content="noindex, follow">
		
		<title><?=($szMetaTagTitle != '' ? "Tally: $szMetaTagTitle" : "Tally")?></title>		
		<link rel="shortcut icon" href="<?=asset_url()?>images/favicon.ico" type="image/x-icon">
		<link rel="icon" href="<?=asset_url()?>images/favicon.ico" type="image/x-icon">
		
		<link rel="stylesheet" href="<?=asset_url()?>css/bootstrap.min.css">
		<link rel="stylesheet" href="<?=asset_url()?>css/font-awesome.min.css">

		<!-- Custom styles for our template -->
		<link rel="stylesheet" href="<?=asset_url()?>css/main.css?<?=filemtime(assets_path() . "css/main.css")?>">
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="<?=asset_url()?>js/html5shiv.js"></script>
		<script src="<?=asset_url()?>js/respond.min.js"></script>
		<![endif]-->
	</head>

	<body>
		<header class="navbar-fixed-top headroom">
			<div class="container">
				<div class="row">
					<div class="col-xs-5">
						<div class="navbar-brand wow fadeInDown"><a href="<?=__BASE_URL__?>"><img src="<?=asset_url()?>images/logo.png" alt="Tally"></a></div>
					</div>
				
					<div class="col-xs-7 header-option">
						<?php if($is_user_login){?>
						<a href="<?=__SECURE_BASE_URL__?>/users/dashboard">Dashboard</a>
						<a href="<?=__SECURE_BASE_URL__?>/users/logout" class="last">Log Out</a>
						<?php } else {?>
						<a href="<?=__SECURE_BASE_URL__?>/users/signup">Sign Up</a>
						<a href="<?=__SECURE_BASE_URL__?>/users/login" class="last">Log In</a>
						<?php }?>
					</div>
				</div>
			</div>
		</header>