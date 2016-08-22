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
		<link rel="shortcut icon" href="<?=__BASE_ASSETS_URL__?>/images/favicon.ico" type="image/x-icon">
		<link rel="icon" href="<?=__BASE_ASSETS_URL__?>/images/favicon.ico" type="image/x-icon">
		
		<link rel="stylesheet" href="<?=__BASE_ASSETS_URL__?>/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?=__BASE_ASSETS_URL__?>/css/font-awesome.min.css">

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
					<div class="col-sm-12 align-center">
						<div class="navbar-brand wow fadeInDown"><img src="<?=__BASE_ASSETS_URL__?>/images/logo.png" alt="Tally"></div>
					</div>
				</div>
			</div>
		</header>
		<section class="signup-process">
			<div class="container">
				<ul class="clearfix">
					<li class="done"><div><span class="check"><i class="fa fa-check"></i></span><span class="number">1.</span> Personal Information</div></li>
					<li class="<?=($iSignUpStep == 2 ? "active" : "done")?>"><div><span class="check"><i class="fa fa-check"></i></span><span class="number">2.</span> Mobile Verification</div></li>					
					<li class="<?=($iSignUpStep == 3 ? "active" : ($iSignUpStep > 3 ? "done" : "pending"))?>"><div><span class="check"><i class="fa fa-check"></i></span><span class="number">3.</span> Chequing Account</div></li>
					<li class="<?=($iSignUpStep == 4 ? "active" : ($iSignUpStep > 4 ? "done" : "pending"))?>"><div><span class="check"><i class="fa fa-check"></i></span><span class="number">4.</span> Account Information</div></li>
				</ul>
			</div>
		</section>