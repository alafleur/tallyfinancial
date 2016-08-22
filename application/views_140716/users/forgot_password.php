<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="main-section">
	<div class="container">
		<div class="row">
			<div class="col-sm-3"></div>
			<div class="col-sm-6">
					<?php if($isPassLinkExists){?>
					<h1 class="align-center">Your new password</h1>
					<h6>Please enter your new password and click 'Update'</h6>
					<div class="login-form">						
						<form name="frmResetPassword" id="frmResetPassword" method="post" action="<?=__SECURE_BASE_URL__?>/users/forgot-password/<?=$szPassLink?>">							
							<div class="form-group">
								<input type="text" name="p_reset_email" value="<?=$szEmail?>" class="form-control" disabled>
							</div>
							
							<div class="form-group<?=($resetPassError != '' ? ' has-error' : '')?>">
								<input type="password" name="reset_password[p_password]" id="p_password" placeholder="Password" maxlength=16 class="form-control required min-length" autocomplete="off">
								<?=($resetPassError != '' ? "<span class=\"help-block pull-left\">$resetPassError</span>" : "")?>								
							</div>
							
							<div class="form-group<?=($resetRePassError != '' ? ' has-error' : '')?>">
								<input type="password" name="reset_password[p_re_password]" id="p_re_password" placeholder="Re-enter Password" maxlength=16 class="form-control required re-match" autocomplete="off">
								<?=($resetRePassError != '' ? "<span class=\"help-block pull-left\">$resetRePassError</span>" : "")?>								
							</div>
							
							<div class="form-group">
								<button class="btn btn-full">Update Password</button>
							</div>
							<input type="hidden" name="p_reset_link" value="<?=$szPassLink?>">
							<input type="hidden" id="p_password_minlength" value="6">
							<input type="hidden" id="p_password_maxlength" value="32">
	                		<input type="hidden" name="reset_password[p_userid]" value="<?=$id?>">
	                		<input type="hidden" name="reset_password[p_email]" value="<?=$szEmail?>">
						</form>
					</div>
					<?php } else {?>
					<h1 class="align-center">Forgot your password</h1>
					<?php if($szForgotPassSuccess != ''){?>
					<br>
					<div class="alert alert-success"><?=$szForgotPassSuccess?></div>
					<?php }else{?>					
					<h6>Enter your Email Address and click 'Reset Request'.</h6>
					<p>You will be emailed a link that will allow you to reset your password.</p>
					<div class="login-form">
						<form name="frmForgotPassword" id="frmForgotPassword" method="post" action="<?=__SECURE_BASE_URL__?>/users/forgot-password">							
							<div class="form-group<?=($szForgotEmailError != '' ? ' has-error' : '')?>">
								<input type=text name="forgot_password[p_email]" placeholder="Email Address" id="p_email" maxlength=50 value="<?=$_POST['forgot_password']['p_email']?>" class="input required registered-email">
								<?=($szForgotEmailError != '' ? "<span class=\"help-block pull-left\">$szForgotEmailError</span>" : "")?>
							</div>
							
							<div class="form-group">
								<button class="btn btn-form-submit btn-full">Reset Request</button></p>			
							</div>
						</form>
					</div>
					<?php }}?>
				</div>
			</div>
		</div>
	</div>
</section>