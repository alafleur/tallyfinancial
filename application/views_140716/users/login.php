<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="main-section">
	<div class="container">
		<div class="row">
			<div class="col-sm-3"></div>
			<div class="col-sm-6">				
				<h1 class="align-center">Log In</h1>
				<?php if($szNotVerified != ''){?>
				<div class="alert alert-danger"><?=$szNotVerified?></div>
				<?php }?>
				<div class="login-form">
					<form name="frmLogin" id="frmLogin" action="<?=__SECURE_BASE_URL__?>/users/login" method="post">
						<div class="form-group<?=(!empty($arErrorMessages['p_email']) ? ' has-error' : '')?>">
							<input type="email" name="arLogin[p_email]" id="p_email" placeholder="Email Address" class="form-control required registered-email" value="<?=sanitize_post_field_value($_POST['arLogin']['p_email'])?>" autocomplete="off">
							<?=(!empty($arErrorMessages['p_email']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$arErrorMessages['p_email']}</span>" : "")?>
						</div>
						<div class="form-group<?=(!empty($arErrorMessages['p_password']) ? ' has-error' : '')?>">
							<input type="password" name="arLogin[p_password]" id="p_password" placeholder="Password" class="form-control required" autocomplete="off">
							<?=(!empty($arErrorMessages['p_password']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$arErrorMessages['p_password']}</span>" : "")?>
						</div>
						<div class="form-group">
							<input type="submit" name="arLogin[p_login]" value="Log In" class="btn btn-form-submit btn-full">
						</div>
						<br>
						<p>
							<a href="<?=__SECURE_BASE_URL__?>/users/forgot-password">Forgot Password</a>
						</p>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>