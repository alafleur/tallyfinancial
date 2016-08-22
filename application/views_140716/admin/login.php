<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="row">
	<div class="col-sm-3"></div>
	<div class="col-sm-6">				
		<h1 class="align-center">Admin Log In</h1>
		<?php if($obj->session->userdata('reset_pass_success_msg')){?>
		<div class="alert alert-success"><?=$_SESSION['reset_pass_success_msg']?></div>
		<?php $obj->session->unset_userdata('reset_pass_success_msg');}?>
		<div class="login-form">
			<form name="frmLogin" id="frmLogin" action="<?=__BASE_ADMIN_URL__?>/login" method="post">
				<div class="form-group<?=(!empty($obj->Admin_Model->arErrorMessages['p_username']) ? ' has-error' : '')?>">
					<input type="email" name="arLogin[p_username]" id="p_username" placeholder="Username" class="form-control required" value="<?=sanitize_post_field_value($_POST['arLogin']['p_username'])?>" autocomplete="off">
					<?=(!empty($obj->Admin_Model->arErrorMessages['p_username']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->Admin_Model->arErrorMessages['p_username']}</span>" : "")?>
				</div>
				<div class="form-group<?=(!empty($obj->Admin_Model->arErrorMessages['p_password']) ? ' has-error' : '')?>">
					<input type="password" name="arLogin[p_password]" id="p_password" placeholder="Password" class="form-control required" autocomplete="off">
					<?=(!empty($obj->Admin_Model->arErrorMessages['p_password']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->Admin_Model->arErrorMessages['p_password']}</span>" : "")?>
				</div>
				<div class="form-group">
					<input type="submit" name="arLogin[p_login]" value="Log In" class="btn btn-form-submit btn-full">
				</div>
				<br>
				<p>
					<a href="<?=__BASE_ADMIN_URL__?>/forgot-password">Forgot Password</a>
				</p>
			</form>
		</div>
	</div>
</div>