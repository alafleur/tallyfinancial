<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if($szErrorMessage != ''){?>
<?=$szErrorMessage?>
<?php } else {
?>
<div id="updateTransitModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <form id="frmUpdateTransitNumber" name="frmUpdateTransitNumber" method="post" action="">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 class="modal-title">Update <span id="account-type"><?=sanitize_post_field_value($_POST['p_type'])?></span> account transit number</h3>
      </div>
      <div class="modal-body">
      	<?php if($transit_updated){?>
      	<div class="alert alert-success">
      		<?=ucwords(sanitize_post_field_value($_POST['p_type']))?> account transit number updated successfully.
      	</div>
      	<?php }?>	
      	<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_transit_number']) ? ' has-error' : '')?>">
      		<input type="text" name="arTransit[p_transit_number]" id="p_transit_number" placeholder="Transit number" value="<?=sanitize_post_field_value($_POST['arTransit']['p_transit_number'])?>" class="form-control required transit-number">
      		<?=(!empty($obj->User_Model->arErrorMessages['p_transit_number']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_transit_number']}</span>" : "")?>
      	</div>
      </div>
      <div class="modal-footer">
      	<button class="btn btn-form-submit1">Update Transit Number</button>
      	<input type="hidden" name="arTransit[p_id]" id="p_id" value="<?=(int)$_POST['arTransit']['p_id']?>">
      	<input type="hidden" name="p_type" id="p_type" value="<?=sanitize_post_field_value($_POST['p_type'])?>">
      </div>
    </div>
    </form>				
  </div>
</div>

<div id="updateInstituteNumberModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <form id="frmUpdateTransitNumber" name="frmUpdateTransitNumber" method="post" action="">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 class="modal-title">Update institution number</h3>
      </div>
      <div class="modal-body">
      	<?php if($institution_number_updated){?>
      	<div class="alert alert-success">
      		Institution number updated successfully.
      	</div>
      	<?php }?>	
      	<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_institution_number']) ? ' has-error' : '')?>">
      		<input type="text" name="arInstitute[p_institution_number]" id="p_institution_number" placeholder="Institution number" value="<?=sanitize_post_field_value($_POST['arInstitute']['p_institution_number'])?>" class="form-control required">
      		<?=(!empty($obj->User_Model->arErrorMessages['p_institution_number']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_institution_number']}</span>" : "")?>
      	</div>
      </div>
      <div class="modal-footer">
      	<button class="btn btn-form-submit1">Update Institution Number</button>
      	<input type="hidden" name="arInstitute[p_institution_id]" id="p_institution_id" value="<?=(int)$_POST['arInstitute']['p_institution_id']?>">
      </div>
    </div>
    </form>				
  </div>
</div>

<h1><?=$obj->User_Model->szFirstName?> <?=$obj->User_Model->szLastName?> Account's Details</h1>
<br>
<?=$szSuccessMessage?>
<h3>Tally Account Information</h3>
<p>
	<strong>Tally Current Balance:</strong> $<?=format_number($fTallyBalance, true)?> <br>
	<strong>External Balance:</strong> $<?=format_number($fTotalTransfers, true)?> <br>
	<strong>Total Tally Savings:</strong> $<?=format_number($fTotalSaving, true)?> <br>
	<strong>Chequing Account Balance:</strong> $<?=format_number($fChequingAccountBalance, true)?> <br>
	<strong>First Saving Transaction On:</strong> <?=$szFirstTransactionDate?> <br>
	<strong>Days from first transaction:</strong> <?=$iFirstTransactionDays?>
</p>
<hr>

<h3>User personal and chequing account information</h3>
<p>
	<?=$obj->User_Model->szFirstName?> <?=$obj->User_Model->szLastName?><br>
	<strong>Email:</strong> <a href="mailto:<?=$obj->User_Model->szEmail?>"><?=$obj->User_Model->szEmail?></a><br>
	<?php if($obj->User_Model->iSignupStep >= 2){?>
	<strong>Mobile:</strong> <?=$obj->User_Model->szMobilePhone?>
	<?php }?>
</p>

<?php if($obj->User_Model->iSignupStep >= 3){?>
<p>
	<strong>Financial institution:</strong> <?=$obj->User_Model->szFinicityInstitution?> <?=($obj->User_Model->szFinicityInstitutionNumber != "" ? "({$obj->User_Model->szFinicityInstitutionNumber})" . ' <a href="javascript:void(0);" onclick="updateInstitutionNumber(\''.$obj->User_Model->idFinicityInstitution.'\',\''.$obj->User_Model->szFinicityInstitutionNumber.'\');">Change Institution Number</a>' : '<a href="javascript:void(0);" onclick="updateTransitNumber(\''.$obj->User_Model->idFinicityInstitution.'\',\'\');">Update Institution Number</a>')?><br>
	<strong>Transit number:</strong> <?=($obj->User_Model->szFinicityAccountTransitNumber != '' ? $obj->User_Model->szFinicityAccountTransitNumber . ' <a href="javascript:void(0);" onclick="updateTransitNumber(\''.$obj->User_Model->id.'\',\'chequing\',\''.$obj->User_Model->szFinicityAccountTransitNumber.'\');">Change Transit Number</a>' : '<a href="javascript:void(0);" onclick="updateTransitNumber(\''.$obj->User_Model->id.'\',\'chequing\',\'\');">Update Transit Number</a>')?><br>
	<strong>Account number:</strong> <?=$obj->User_Model->szFinicityAccountNumber?><br>
	<?php if($obj->User_Model->szFinicityAccountVerificationFile != '' && file_exists(__APP_PATH_ASSETS__ . "/images/users/{$obj->User_Model->szFinicityAccountVerificationFile}")){?><strong>Verification Document:</strong> <a href="<?=__BASE_ADMIN_URL__?>/users/document/chequing/<?=$obj->User_Model->szUniqueKey?>" target="_blank">View</a><br><?php }?>
	<?php if($obj->User_Model->szFinicityStatementVerificationFile != '' && file_exists(__APP_PATH__ . "/statements/{$obj->User_Model->szFinicityStatementVerificationFile}")){?><strong>Statement File:</strong> <a href="<?=__BASE_ADMIN_URL__?>/users/document/statement/<?=$obj->User_Model->szUniqueKey?>" target="_blank">View</a><?php }?>
</p>
<?php }?>
<hr>

<div class="form-group">
	<div class="row">
		<div class="col-sm-4">			
			<?php if($obj->User_Model->iSignupStep == 4){?>
			<a href="javascript:void(0);" class="btn btn-full" onclick="check_confirm('<?=$obj->User_Model->id?>', 'You are about to verify this account.', 'VERIFY', 'ACCOUNT');">Verify this account</a>
			<?php } else { ?>
			<a class="btn btn-gray btn-full">Verify this account</a>
			<?php } ?>
		</div>
		<div class="col-sm-4">
			<?php if($obj->User_Model->iSignupStep == 5){?>
			<a href="javascript:void(0);" class="btn btn-full" onclick="check_confirm('<?=$obj->User_Model->id?>', 'You are about to block this account.', 'BLOCK', 'ACCOUNT');">Block this account</a>
			<?php } else {?>
			<a class="btn btn-gray btn-full">Block this account</a>
			<?php }?>
		</div>
	</div>
	<hr>
</div>

<?php if($obj->User_Model->szVerificationFile != ''){?>
<h3>User saving account information</h3>
<p>
	<strong>Financial institution:</strong> <?=$obj->User_Model->szInstitution?> <?=($obj->User_Model->szInstitutionNumber != "" ? "({$obj->User_Model->szInstitutionNumber})" . ' <a href="javascript:void(0);" onclick="updateInstitutionNumber(\''.$obj->User_Model->idInstitution.'\',\''.$obj->User_Model->szInstitutionNumber.'\');">Change Institution Number</a>' : '<a href="javascript:void(0);" onclick="updateTransitNumber(\''.$obj->User_Model->idInstitution.'\',\'\');">Update Institution Number</a>')?><br>
	<strong>Transit number:</strong> <?=($obj->User_Model->szTransitNumber != '' ? $obj->User_Model->szTransitNumber . ' <a href="javascript:void(0);" onclick="updateTransitNumber(\''.$obj->User_Model->id.'\',\'saving\',\''.$obj->User_Model->szTransitNumber.'\');">Change Transit Number</a>' : '<a href="javascript:void(0);" onclick="updateTransitNumber(\''.$obj->User_Model->id.'\',\'saving\',\'\');">Update Transit Number</a>')?><br>
	<strong>Account number:</strong> <?=$obj->User_Model->szAccountNumber?>
	<?php if($obj->User_Model->szVerificationFile != 'SAME-AS-CHECKING'){?>
	<br><strong>Verification Document:</strong> <a href="<?=__BASE_ADMIN_URL__?>/users/document/saving/<?=$obj->User_Model->szUniqueKey?>" target="_blank">View</a>
	<?php }?>
</p>
<hr>

<div class="form-group">
	<div class="row">
		<div class="col-sm-4">			
			<?php if(!$obj->User_Model->iSavingAccountVerified){?>
			<a href="javascript:void(0);" class="btn btn-full" onclick="check_confirm('<?=$obj->User_Model->id?>', 'You are about to verify this user\'s savings account.', 'VERIFY', 'SAVING-ACCOUNT');">Verify saving account</a>
			<?php } else {?>
			<a class="btn btn-gray btn-full">Verify saving account</a>
			<?php }?>
		</div>
		<div class="col-sm-4">	
			<?php if($obj->User_Model->iSavingAccountVerified){?>
			<a href="javascript:void(0);" class="btn btn-full" onclick="check_confirm('<?=$obj->User_Model->id?>', 'You are about to block this user\'s savings account.', 'BLOCK', 'SAVING-ACCOUNT');">Block saving account</a>
			<?php } else {?>
			<a class="btn btn-full btn-gray">Block saving account</a>
			<?php }?>
		</div>
	</div>
	<hr>
</div>
<?php } ?>

<?php if($obj->User_Model->iSignupStep > 4){?>
<h3>Manage user level constants</h3>
<br>
<form class="form-horizontal" name="frmUserConstants" id="frmUserConstants" action="<?=__BASE_ADMIN_URL__?>/users/details/<?=$obj->User_Model->id?>" method="post">
	<div class="row">
		<div class="col-sm-8">
			<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_absmin']) ? ' has-error' : '')?>">
				<label class="col-sm-4">Abs min balance</label>
				<div class="col-sm-8">
					<input type="text" name="arConstants[p_absmin]" id="p_absmin" placeholder="Absolute minimum of chequing account balance" class="form-control" value="<?=sanitize_post_field_value($_POST['arConstants']['p_absmin'])?>">
					<?=(!empty($obj->User_Model->arErrorMessages['p_absmin']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_absmin']}</span>" : "")?>
				</div>
			</div>
			
			<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_surplusdeficit']) ? ' has-error' : '')?>">
				<label class="col-sm-4">Surplus deficit rate</label>
				<div class="col-sm-8">
					<input type="text" name="arConstants[p_surplusdeficit]" id="p_surplusdeficit" placeholder="Surplus deficit rate" class="form-control" value="<?=sanitize_post_field_value($_POST['arConstants']['p_surplusdeficit'])?>">
					<?=(!empty($obj->User_Model->arErrorMessages['p_surplusdeficit']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_surplusdeficit']}</span>" : "")?>
				</div>
			</div>
			
			<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['']) ? ' has-error' : '')?>">
				<label class="col-sm-4">If deficit rate</label>
				<div class="col-sm-8">
					<input type="text" name="arConstants[p_ifdeficit]" id="p_ifdeficit" placeholder="If deficit rate" class="form-control" value="<?=sanitize_post_field_value($_POST['arConstants']['p_ifdeficit'])?>">
					<?=(!empty($obj->User_Model->arErrorMessages['p_ifdeficit']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_ifdeficit']}</span>" : "")?>
				</div>
			</div>
			
			<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_id']) ? ' has-error' : '')?>">
				<div class="col-sm-offset-4 col-sm-8">
					<button class="btn">Update Constants</button>
					<input type="hidden" name="arConstants[p_id]" value="<?=$obj->User_Model->id?>">
					<?=(!empty($obj->User_Model->arErrorMessages['p_id']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_id']}</span>" : "")?>
				</div>
			</div>
		</div>
	</div>
</form>
<hr>
<?php }?>

<?php if($obj->User_Model->iSignupStep >= 2){?>
<h3>Send a Message</h3>
<br>
<form class="form-horizontal" name="frmSendMessage" id="frmSendMessage" action="<?=__BASE_ADMIN_URL__?>/users/details/<?=$obj->User_Model->id?>" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-sm-7">
			<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_msg']) ? ' has-error' : '')?>">
				<label class="col-sm-4">Message</label>
				<div class="col-sm-8">
					<textarea name="arMsg[p_msg]" id="p_msg" placeholder="Message" class="form-control"><?=sanitize_post_field_value($_POST['arMsg']['p_msg'])?></textarea>
					<?=(!empty($obj->User_Model->arErrorMessages['p_msg']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_msg']}</span>" : "")?>
				</div>
			</div>
			
			<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_media']) ? ' has-error' : '')?>">
				<label class="col-sm-4">Media file</label>
				<div class="col-sm-8">
					<input type="file" name="p_media">
					<?=(!empty($obj->User_Model->arErrorMessages['p_media']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_media']}</span>" : "")?>
				</div>
			</div>
			
			<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_id']) ? ' has-error' : '')?>">
				<div class="col-sm-offset-4 col-sm-8">
					<button class="btn">Send</button>
					<input type="hidden" name="arMsg[p_id]" value="<?=$obj->User_Model->id?>">
					<?=(!empty($obj->User_Model->arErrorMessages['p_id']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->User_Model->arErrorMessages['p_id']}</span>" : "")?>
				</div>
			</div>
		</div>
		<div class="col-sm-5">
			<table class="table-format3">
				<tr>
					<td>
						{FIRSTNAME} = First Name<br>
						{LASTNAME} = Last Name<br>
						{EMAIL} = Email Address<br>
						{MOBILE} = Mobile Number<br>
						{CHEQUINGBALANCE} = Chequing account balance<br>
						{TOTALSAVINGS} = Total Tally Savings<br>
						{TALLYBALANCE} = Current Tally Balance<br>
						{EXTERNALBALANCE} = Tally Savings Transferred Out<br>
						{MOSTRECENT} = Most recent savings transaction<br>
						{AVGWEEK1} = Avg Week1<br>
						{AVGWEEK2} = Avg Week2<br>
						{AVGWEEK3} = Avg Week3<br>
						{AVGWEEK4} = Avg Week4<br>
						{AVGWEEK5} = Avg Week5<br>
						{AVGINCOMEMONTHLY} = Avg Income Monthly<br>
						{AVGINCOMEWKY} = Avg Income Weekly<br>
						{MAXEXPENSEAVG} = Max Expense Avg<br>
						{MAXEXPENSECOVERAGE} = Max Expense Coverage
					</td>
				</tr>
			</table>
		</div>
	</div>
</form>
<hr>
<?php }}?>

<p>
	<a href="<?=__BASE_ADMIN_URL__?>/users/list">Back to user's list</a>
</p>