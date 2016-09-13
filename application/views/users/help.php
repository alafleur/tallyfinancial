<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!empty($_POST['p_help']))
{
	$p_comment = trim($_POST['p_comment']);
	if($p_comment != '')
	{
		if($obj->User_Model->addUserQuery($_POST))
		{
			$done = "Thanks! Your query has been successfully submitted and we'll get back to you shortly.";
			$_POST['p_comment'] = "";
		}
	}
	else
	{
		$obj->User_Model->addError("p_comment", "Your comment is required");
	}
}
?>
<section class="main-section">
	<div class="container">
		<div class="row clearfix">
			<div class="col-sm-3">
				<?php require_once('left_menu.php');?>
			</div>
			<div class="col-sm-9">
				<h1 class="align-center">Help</h1>
				<br>
				<?php if($done != ''){?>
				<div class="alert alert-success"><?=$done?></div>
				<?php }?>
				
				<div class="row">
					<div class="col-sm-2"></div>
					<div class="col-sm-8">
						<div class="login-form">
							<form name="frmHelp" id="frmHelp" action="<?=__SECURE_BASE_URL__?>/users/help" method="post" class="form-horizontal">
								<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_comment']) ? ' has-error' : '')?>">
									<label class="col-sm-3">Comments/Questions?</label>
									<div class="col-sm-9">
										<textarea name="p_comment" id="p_comment" placeholder="Your Comments/Questions?" class="form-control required"><?=sanitize_post_field_value($_POST['p_comment'])?></textarea>
										<?=(!empty($obj->User_Model->arErrorMessages['p_comment']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_comment']}</span>" : "")?>
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-offset-3 col-sm-4">
										<button class="btn btn-full btn-sm btn-form-submit">Send</button>
									</div>
								</div>
								
								<input type="hidden" name="p_help" value="1">
								<input type="hidden" name="p_id" value="<?=$obj->User_Model->id?>">
								<input type="hidden" name="p_fname" value="<?=$obj->User_Model->szFirstName?>">
								<input type="hidden" name="p_lname" value="<?=$obj->User_Model->szLastName?>">
								<input type="hidden" name="p_email" value="<?=$obj->User_Model->szEmail?>">
								<input type="hidden" name="p_mobile" value="<?=$obj->User_Model->szMobilePhone?>">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>