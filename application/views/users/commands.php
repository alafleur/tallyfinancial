<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(isset($_POST['p_threshold']))
{
	if($obj->User_Model->changeMinimumThreshold($obj->User_Model->id, $_POST['p_threshold']))
	{
		$done = "Minimum savings threshold changed successfully";
		
		// send message to admin
		sendMessege(__ADMIN_MOBILE_NUMBER__, "User {$obj->User_Model->szFirstName} {$obj->User_Model->szLastName}({$obj->User_Model->szEmail}) has changed minimum savings threshold to " . format_number($_POST['p_threshold']));
		
		// send email
		$message = "User {$obj->User_Model->szFirstName} {$obj->User_Model->szLastName}({$obj->User_Model->szEmail}) has changed minimum savings threshold to " . format_number($_POST['p_threshold']) . ".";
		$subject = $obj->User_Model->szFirstName." ".$obj->User_Model->szLastName." changed Minimum Savings Threshold";
		$from = __CUSTOMER_SUPPORT_EMAIL__;
		$to = __ADMIN_USER_EMAIL__;
		sendEmail($to, $from, $subject, $message);
	}
}

if(isset($_POST['p_withdraw']))
{
	$fTotalAvalable = (float)$_POST['p_available_amount'];
	if($obj->User_Model->validateInput($_POST['p_withdraw'], __VLD_CASE_NUMERIC__, "p_withdraw", "Withdraw amount", 1.00, ($fTotalAvalable > 0 ? $fTotalAvalable : false), true))
	{
		$fTransferAmount = (float)$_POST['p_withdraw'];
		$idCustomer = (int)$obj->User_Model->idFinicity;
		if($fTotalAvalable == 0)
		{
			$obj->User_Model->addError('p_withdraw', 'Please enter an amount less than or equal to the total available savings.');
		}
		else if ($fTotalAvalable < $fTransferAmount) {
			$obj->User_Model->addError('p_withdraw', 'Please enter an amount less than or equal to the total available savings.');
		}
		else
		{
			if($obj->User_Model->addCustomerSavingsTransfer($idCustomer, $fTransferAmount, true))
			{
				$done = "Your request to withdraw was successfully submitted and the Admin will process your request as soon as possible.";
				
				// send message to admin
				sendMessege(__ADMIN_MOBILE_NUMBER__, "User {$obj->User_Model->szFirstName} {$obj->User_Model->szLastName}({$obj->User_Model->szEmail}) has requested for withdrawal of $" . format_number($fTransferAmount, true) . " from Tally savings. Please check and approve the request.");
				
				// send email
				$message = "
				User {$obj->User_Model->szFirstName} {$obj->User_Model->szLastName}({$obj->User_Model->szEmail}) has requested for withdrawal of $" . format_number($fTransferAmount, true) . " from Tally savings.<br><br>
				Please check and approve the request at " . __BASE_ADMIN_URL__ . "/users/managebalance/{$obj->User_Model->id}" . ".";
				$subject = $obj->User_Model->szFirstName." ".$obj->User_Model->szLastName." Withdrawal Request";
				$from = __CUSTOMER_SUPPORT_EMAIL__;
				$to = __ADMIN_USER_EMAIL__;
				sendEmail($to, $from, $subject, $message);
			}
		}
	}
}

if(!empty($_POST['p_suto_saving']))
{
	$iAuto = (int)$_POST['p_suto_saving'];
	if($obj->User_Model->changeAutoSavingStatus($obj->User_Model->id, $iAuto, $_POST['p_old_date']))
	{
		$done = "Auto saving status changed successfully";
		
		// send message to admin
		if($iAuto != 1)
			sendMessege(__ADMIN_MOBILE_NUMBER__, "User {$obj->User_Model->szFirstName} {$obj->User_Model->szLastName}({$obj->User_Model->szEmail}) has paused Tally Savings for $iAuto days on " . date("m/d/Y", strtotime($obj->User_Model->dtAutoSavingsChanged)) . " AT " . date("h:i A", strtotime($obj->User_Model->dtAutoSavingsChanged)));
		else
			sendMessege(__ADMIN_MOBILE_NUMBER__, "User {$obj->User_Model->szFirstName} {$obj->User_Model->szLastName}({$obj->User_Model->szEmail}) has resumed Tally Savings on " . date("m/d/Y", strtotime($obj->User_Model->dtAutoSavingsChanged)) . " AT " . date("h:i A", strtotime($obj->User_Model->dtAutoSavingsChanged)));
		
		// send email
		if($iAuto != 1)
			$message = "User {$obj->User_Model->szFirstName} {$obj->User_Model->szLastName}({$obj->User_Model->szEmail}) has paused Tally Savings for $iAuto days on " . date("m/d/Y", strtotime($obj->User_Model->dtAutoSavingsChanged)) . " AT " . date("h:i A", strtotime($obj->User_Model->dtAutoSavingsChanged)) . ".";
		else
			$message = "User {$obj->User_Model->szFirstName} {$obj->User_Model->szLastName}({$obj->User_Model->szEmail}) has resumed Tally Savings on " . date("m/d/Y", strtotime($obj->User_Model->dtAutoSavingsChanged)) . " AT " . date("h:i A", strtotime($obj->User_Model->dtAutoSavingsChanged)) . ".";
		
		$subject = $obj->User_Model->szFirstName . " " . $obj->User_Model->szLastName . ($iAuto != 1 ? " paused " : " resumed ") . "Tally savings";
		$from = __CUSTOMER_SUPPORT_EMAIL__;
		$to = __ADMIN_USER_EMAIL__;
		sendEmail($to, $from, $subject, $message);
	}
}

$fTotalSaving = 0;
$fTallyBalance = 0;
$fTotalTransfers = 0;

$arTransactions = $obj->Configuration_Model->getCustomerBiMonthlySavingTransactions($obj->User_Model->idFinicity);
if(!empty($arTransactions))
{
	foreach($arTransactions as $transaction)
	{
		$fTotalSaving += $transaction['fSavingAmount'];
	}
	if($fTotalSaving != 0)
		$fTotalSaving = abs($fTotalSaving);
}

$arTransfers = $obj->User_Model->getCustomerSavingsTransfers($obj->User_Model->idFinicity);
if(!empty($arTransfers))
{
	foreach($arTransfers as $transfer)
	{
		$fTotalTransfers += $transfer['fAmount'];
	}
}
if($fTotalSaving > $fTotalTransfers)
	$fTallyBalance = $fTotalSaving - $fTotalTransfers;
?>
<section class="main-section">
	<div class="container">
		<div class="row clearfix">
			<div class="col-sm-3">
				<?php require_once('left_menu.php');?>
			</div>
			<div class="col-sm-9">
				<h1 class="align-center">Commands</h1>
				<br>
				<div class="row">
					<div class="col-sm-1"></div>
					<div class="col-sm-10">
						<?php if($done){?><div class="alert alert-success"><?=$done?></div><?php }?>
						<div class="login-form">
							<!-- <form name="frmThreshold" id="frmThreshold" action="<?=__SECURE_BASE_URL__?>/users/commands" method="post" class="form-horizontal">
								<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_threshold']) ? ' has-error' : '')?>">
									<label class="col-sm-4">Minimum Threshold</label>
									<div class="col-sm-5">
										<input type="text" name="p_threshold" id="p_threshold" placeholder="Minimum Threshold" class="form-control" value="<?=format_number(!empty($_POST['p_threshold']) ? $_POST['p_threshold'] : $obj->User_Model->fMinThreshold)?>">
										<?=(!empty($obj->User_Model->arErrorMessages['p_threshold']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_threshold']}</span>" : "")?>
									</div>
									<div class="col-sm-3">
										<button class="btn btn-full btn-sm">Update</button>
									</div>
								</div>
							</form>-->
							
							<form name="frmAutoSaving" id="frmAutoSaving" action="<?=__SECURE_BASE_URL__?>/users/commands" method="post" class="form-horizontal">
								<div class="row clearfix">
									<div class="col-sm-9">
										<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_suto_saving']) ? ' has-error' : '')?>">
											<label class="col-sm-5">Account Status</label>
											<div class="col-sm-7">
												<select name="p_suto_saving" class="form-control">
													<option value="1" <?=($obj->User_Model->iAutoSavings == 1 ? 'selected' : '')?>>On / Resume</option>
													<option value="3" <?=($obj->User_Model->iAutoSavings == 3 ? 'selected' : '')?>>Pause<?=($obj->User_Model->iAutoSavings == 3 ? 'd' : '')?> for 3 days<?=($obj->User_Model->iAutoSavings == 3 ? " (On ".date("m/d/Y", strtotime($obj->User_Model->dtAutoSavingsChanged)).")" : '')?></option>
													<option value="7" <?=($obj->User_Model->iAutoSavings == 7 ? 'selected' : '')?>>Pause<?=($obj->User_Model->iAutoSavings == 7 ? 'd' : '')?> for 7 days<?=($obj->User_Model->iAutoSavings == 7 ? " (On ".date("m/d/Y", strtotime($obj->User_Model->dtAutoSavingsChanged)).")" : '')?></option>
													<option value="10" <?=($obj->User_Model->iAutoSavings == 10 ? 'selected' : '')?>>Pause<?=($obj->User_Model->iAutoSavings == 10 ? 'd' : '')?> for 10 days<?=($obj->User_Model->iAutoSavings == 10 ? " (On ".date("m/d/Y", strtotime($obj->User_Model->dtAutoSavingsChanged)).")" : '')?></option>
												</select>
												<?=(!empty($obj->User_Model->arErrorMessages['p_suto_saving']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_suto_saving']}</span>" : "")?>
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<div class="col-xs-12">
												<button class="btn btn-full btn-sm">Update</button>
											</div>
										</div>
									</div>
								</div>
								<input type="hidden" name="p_old_date" value="<?=date("Y-m-d", strtotime($obj->User_Model->dtAutoSavingsChanged))?>">
							</form>
							
							<form name="frmWithdraw" id="frmWithdraw" action="<?=__SECURE_BASE_URL__?>/users/commands" method="post" class="form-horizontal">
								<div class="row clearfix">
									<div class="col-sm-9">
										<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_withdraw']) ? ' has-error' : '')?>">
											<label class="col-sm-5">Withdraw to Chequing Account</label>											
											<div class="col-sm-7">
												<input type="text" name="p_withdraw" id="p_withdraw" placeholder="Withdrow Amount" class="form-control" value="<?=format_number($_POST['p_withdraw'])?>">
												<span class="pull-left color-blue"><i class="fa fa-info-circle"></i> Total available: <?=format_number($fTallyBalance, true)?></span>
												<?=(!empty($obj->User_Model->arErrorMessages['p_withdraw']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_withdraw']}</span>" : "")?>
											</div>
										</div>
									</div>									
									<div class="col-sm-3">
										<div class="form-group">
											<div class="col-xs-12">
												<button class="btn btn-full btn-sm">Send</button>
											</div>
										</div>
									</div>
								</div>
								<input type="hidden" name="p_available_amount" value="<?=$fTallyBalance?>">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>