<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="main-section">
	<div class="container">
		<div class="row clearfix">
			<div class="col-sm-3">
				<?php require_once('left_menu.php');?>
			</div>
			<div class="col-sm-9">
			<?php if(trim($arg1) == "change"){?>
			<h1 class="align-center">Confirm change of external savings account</h1>			
			<br>
			<div class="row">
				<div class="col-sm-1"></div>
				<div class="col-sm-10">
					<div class="login-form">
						<h6 class="color-red">Are you sure you want to change your external savings account details?</h6>
						<form id="frmChangeBankingInfo" method="POST" action="<?=__SECURE_BASE_URL__?>/users/saving-account">
							<div class="row clearfix">
								<div class="col-sm-6">
									<div class="form-group">
										<input type="submit" name="p_change_account" value="Confirm" class="btn btn-full">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<input type="submit" name="p_cancel_change_account" value="Cancel" class="btn btn-gray btn-full">
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
							
			<?php } else if(!$done && $obj->User_Model->szInstitution != '' && ($obj->User_Model->szVerificationFile == '' || $obj->User_Model->szTransitNumber != 'SAME-AS-CHECKING') && $obj->User_Model->szAccountNumber != '' && $obj->User_Model->szVerificationFile != ''){?>
			<h1 class="align-center">External Savings Account</h1>
			<br>
			<div class="row">
				<div class="col-sm-2"></div>
				<div class="col-sm-8">
					<div class="login-form">
						<div class="form-group">
							<div class="row">
								<label class="col-sm-5">Financial institution:</label>
								<div class="col-sm-7"><?=$obj->User_Model->szInstitution?></div>
							</div>
							
							<div class="row">
								<label class="col-sm-5">Transit number:</label>
								<div class="col-sm-7"><?=$obj->User_Model->szTransitNumber?></div>
							</div>
							
							<div class="row">
								<label class="col-sm-5">Account number:</label>
								<div class="col-sm-7"><?=$obj->User_Model->szAccountNumber?></div>
							</div>
						</div>
						
						<div class="form-group">
							<?=($obj->User_Model->iSavingAccountVerified ? '<i class="fa fa-check-circle-o color-green"></i> Verified' : '<i class="fa fa-times-circle-o color-red"></i> Not verified')?>
							<a href="<?=__SECURE_BASE_URL__?>/users/saving-account/change" class="pull-right">Change your details</a>
						</div>
					</div>
				</div>
			</div>
			<?php } else { if($done){?>
			<h1 class="align-center">Completed</h1>
			<br>
			<div class="row">
				<div class="col-sm-2"></div>
				<div class="col-sm-8">
					<div class="login-form">
						<div class="alert alert-success">
							Your external savings account details has been saved successfully. Your details currently is under verification. You'll be notified as soon as your details is verified. It usually takes 24-48hrs for the details to be verified.						
						</div>
						<p>
							<a href="<?=__SECURE_BASE_URL__?>/users/saving-account">View your details</a>
							<a href="<?=__SECURE_BASE_URL__?>/users/saving-account/change" class="pull-right">Change your details</a>
						</p>
					</div>
				</div>
			</div>
			<?php } else if($obj->User_Model->szInstitution != '' && $obj->User_Model->szTransitNumber != '' && $obj->User_Model->szAccountNumber != '' && $obj->User_Model->szVerificationFile == ''){?>
			<h1 class="align-center">Verify your external savings account</h1>
			<br>
			<p>Please upload one of the following-</p>
			<ul>
				<li>The first page of your saving bank statement</li>
				<li>An image of a void cheque</li>
				<li>A screenshot of your online banking account clearly showing your full name, bank's logo, <strong>complete account and transit number</strong>, and the current balance in your account</li>
			</ul>
			<div class="row">
				<div class="col-sm-2"></div>
				<div class="col-sm-8">
					<div class="login-form">
						<form id="frmVerifyBankingInfo" method="POST" action="<?=__SECURE_BASE_URL__?>/users/saving-account" enctype="multipart/form-data">
							<div class="form-group">
								<div class="row">
									<label class="col-sm-5">Financial institution:</label>
									<div class="col-sm-7"><?=$obj->User_Model->szInstitution?></div>
								</div>
								
								<div class="row">
									<label class="col-sm-5">Transit number:</label>
									<div class="col-sm-7"><?=$obj->User_Model->szTransitNumber?></div>
								</div>
								
								<div class="row">
									<label class="col-sm-5">Account number:</label>
									<div class="col-sm-7"><?=$obj->User_Model->szAccountNumber?></div>
								</div>
							</div>
							
							<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_verification_file']) ? ' has-error' : '')?>">						
								<input type="file" name="p_verification_file">
								<?=(!empty($obj->User_Model->arErrorMessages['p_verification_file']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_verification_file']}</span>" : "")?>						
							</div>
							
							<div class="form-group">
								<input type="submit" name="arVerify[p_submit]" value="Submit" class="btn btn-full">
								<input type="hidden" name="arVerify[p_id]" value="<?=$obj->User_Model->id?>">
							</div>
						</form>
						
						<p>
							<a href="<?=__BASE_URL__?>/articles/faqs/how-do-i-verify-my-banking" target="_blank">How do I verify my bank account?</a>
							<a href="<?=__SECURE_BASE_URL__?>/users/saving-account/change" class="pull-right">Change details</a>
						</p>
					</div>
				</div>
			</div>
			<?php } else {
			$isSavingAccountExists = false;
			$obj->load->model('Finicity_Model');
			$arAccounts = $obj->Finicity_Model->getCustomerAccountsByInstitution($obj->User_Model->idFinicity, $obj->User_Model->idFinicityInstitution);
			if(!isset($arAccounts['account'][0]))
			{
				$arAccounts = $arAccounts['account'];
				$arAccounts['account'] = array();
				$arAccounts['account'][0] = $arAccounts;
			}
			if(!empty($arAccounts['account']))
			{
				foreach($arAccounts['account'] as $account)
				{
					if($account['type'] == "savings")
					{
						$isSavingAccountExists = true;
						$_POST['arBanking']['p_institution'] = $obj->User_Model->szFinicityInstitution;
						$_POST['arBanking']['p_transit_number'] = $obj->User_Model->szFinicityAccountTransitNumber;
						$_POST['arBanking']['p_account_number'] = $account['number'];
						$_POST['arBanking']['p_institution_id'] = $obj->User_Model->idFinicityInstitution;
					}
				}
			}?>
			<h1 class="align-center">External Savings Account</h1>
			<br>
			<h6 class="align-center">If you'd like to direct the savings Tally finds to an external savings account, please enter / confirm the account details below. We will transfer your savings twice per month.</h6>
			<div class="row">
				<div class="col-sm-2"></div>
				<div class="col-sm-8">					
					<div class="login-form">
						<form id="frmBankingInfo" method="POST" action="<?=__SECURE_BASE_URL__?>/users/saving-account">
							<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_institution']) ? ' has-error' : '')?>">
								<input type="text" placeholder="Financial institution" name="arBanking[p_institution]" value="<?=sanitize_post_field_value($_POST['arBanking']['p_institution'])?>" id="search-box" class="form-control select-only" autocomplete="off">
								<input type="hidden" id="show-all" value="1">							
								<div class="suggesstion-box"></div>
								<?=(!empty($obj->User_Model->arErrorMessages['p_institution']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_institution']}</span>" : "")?>
							</div>
							
							<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_transit_number']) ? ' has-error' : '')?>">
								<input type="text" placeholder="Transit number" name="arBanking[p_transit_number]" value="<?=sanitize_post_field_value($_POST['arBanking']['p_transit_number'])?>" id="p_transit_number" class="form-control transit-number<?=(!$isSavingAccountExists ? " required" : "")?>">
								<?=(!empty($obj->User_Model->arErrorMessages['p_transit_number']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_transit_number']}</span>" : "")?>
							</div>
							
							<div class="form-group clearfix<?=(!empty($obj->User_Model->arErrorMessages['p_account_number']) ? ' has-error' : '')?>">
								<input type="text" placeholder="Account number" name="arBanking[p_account_number]" value="<?=sanitize_post_field_value($_POST['arBanking']['p_account_number'])?>" id="p_account_number" class="form-control required">
								<?=(!empty($obj->User_Model->arErrorMessages['p_account_number']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_account_number']}</span>" : "")?>
							</div>
							
							<div class="row clearfix">
								<div class="col-md-6">
									<div class="form-group">
										<?php if($obj->User_Model->iSavingAcountChanged){?>
										<input type="submit" name="arBanking[p_back]" value="Back to Previous" class="btn btn-full">
										<?php } else {?>
										<input type="submit" name="arBanking[p_confirm]" value="Confirm" class="btn btn-full<?=(!$isSavingAccountExists ? ' btn-gray' : '')?>"<?=(!$isSavingAccountExists ? ' disabled' : '')?>>
										<?php }?>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<input type="submit" name="arBanking[p_submit]" value="Update" class="btn btn-full btn-form-submit">
									</div>
								</div>
								
								<input type="hidden" name="arBanking[p_id]" value="<?=$obj->User_Model->id?>">
								<input type="hidden" name="arBanking[p_changed]" value="<?=$obj->User_Model->iSavingAcountChanged?>">
								<input type="hidden" name="arBanking[p_institution_id]" id="search-id" value="<?=sanitize_post_field_value($_POST['arBanking']['p_institution_id'])?>">
							</div>
						</form>
						<p>
							Stuck? Take a look at these <a href="<?=__BASE_URL__?>/articles/faqs/where-do-I-find-my-banking-information" target="_blank">FAQs on banking information</a>.
						</p>
					</div>
				</div>
			</div>
			<?php }}?>
			</div>
		</div>
	</div>
</section>