<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="row">
	<div class="col-sm-<?=($is_admin_login ? 2 : 3)?>"></div>
	<div class="col-sm-<?=($is_admin_login ? 8 : 6)?>">
			<?php if($isPassLinkExists){?>
			<h1 class="align-center">Your new password</h1>
			<h6>Please enter your new password and click 'Update'</h6>
			<div class="login-form">						
				<form name="frmResetPassword" id="frmResetPassword" method="post" action="<?=__BASE_ADMIN_URL__?>/forgot-password/<?=$szPassLink?>">							
					<div class="form-group<?=($resetPassError != '' ? ' has-error' : '')?>">
						<input type="password" name="reset_password[p_password]" id="p_password" placeholder="New Password" maxlength=16 class="form-control required min-length" autocomplete="off">
						<?=($resetPassError != '' ? "<span class=\"help-block pull-left\">$resetPassError</span>" : "")?>								
					</div>
					
					<div class="form-group<?=($resetRePassError != '' ? ' has-error' : '')?>">
						<input type="password" name="reset_password[p_re_password]" id="p_re_password" placeholder="Re-enter New Password" maxlength=16 class="form-control required re-match" autocomplete="off">
						<?=($resetRePassError != '' ? "<span class=\"help-block pull-left\">$resetRePassError</span>" : "")?>								
					</div>
					
					<div class="form-group">
						<button class="btn btn-full">Update Password</button>
					</div>
					<input type="hidden" name="p_reset_link" value="<?=$szPassLink?>">
					<input type="hidden" id="p_password_minlength" value="6">
					<input type="hidden" id="p_password_maxlength" value="32">
				</form>
			</div>
			<?php } else {?>
			<h1 class="align-center">Forgot your password</h1>
			<?php if($szForgotPassSuccess != ''){?>
			<br>
			<div class="alert alert-success"><?=$szForgotPassSuccess?></div>
			<?php }else{?>
			<?php if($szForgotPassError){?><div class="alert alert-error"><?=$szForgotPassError?></div><?php }?>					
			<h6>Enter Email Address for Admin User and click 'Reset Request'.</h6>
			<div class="login-form">
				<form name="frmForgotPassword" id="frmForgotPassword" method="post" action="<?=__BASE_ADMIN_URL__?>/forgot-password">							
					<div class="form-group<?=($szForgotEmailError != '' ? ' has-error' : '')?>">
						<input type=text name="forgot_password[p_email]" placeholder="Email Address" id="p_email" maxlength=50 value="<?=$_POST['forgot_password']['p_email']?>" class="input required email">
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