<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if($szErrorMessage != ''){?>
<?=$szErrorMessage?>
<?php } else {?>
<h1><?=$obj->User_Model->szFirstName?> <?=$obj->User_Model->szLastName?> Tally Account Information</h1>
<br>
<p>
	<strong>Tally Current Balance:</strong> $<?=format_number($fTallyBalance)?> <br>
	<strong>External Balance:</strong> $<?=format_number($fTotalTransfers)?> <br>
	<strong>Total Tally Savings:</strong> $<?=format_number($fTotalSaving)?> <br>
	<strong>Chequing account balance:</strong> $<?=format_number($fChequingAccountBalance)?> <br>
	<strong>First Saving Transaction On:</strong> <?=$szFirstTransactionDate?> <br>
	<strong>Days from first transaction:</strong> <?=$iFirstTransactionDays?> 
</p>
<hr>

<?php if($szSuccessMessage != ''){?>
<?=$szSuccessMessage?>
<?php }

$approve_request = false;
if($arg2 == "approve" && (int)$arg3 > 0)
{
	$approve_request = true;
	$arTransferData = $obj->User_Model->getCustomerSavingsTransfers($arg3, 0, 0, true);
	$_POST['p_amount'] = (!isset($_POST['p_amount']) ? $arTransferData[0]['fAmount'] : $_POST['p_amount']);
}
?>
<div class="tn-search-form">
	<form id="frmSearchTransactions" name="frmSearchTransactions" method="post" action="<?=__BASE_ADMIN_URL__?>/users/managebalance/<?=$idUser?>" class="form-horizontal">
		<?php if($approve_request){?>
		<h3>Approve user $<?=format_number($arg4)?> withdraw request</h3>
		<?php }?>
		<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_amount']) ? ' has-error' : '')?>">
			<label class="col-sm-2">Transfer amount</label>
			<div class="col-sm-4 no-left-pad">
				<input type="text" placeholder="Transfer amount" name="p_amount" id="p_amount" class="form-control" value="<?=sanitize_post_field_value($_POST['p_amount'])?>" <?=($approve_request ? "readonly" : "")?>>				
			</div>
			<div class="col-sm-6 color-blue">
				<i class="fa fa-info-circle"></i> Total available balance: $<?=format_number($fTallyBalance)?>
			</div>
			<?=(!empty($obj->User_Model->arErrorMessages['p_amount']) ? '<span class="help-block col-sm-offset-2 col-sm-10 pull-left no-left-pad"><i class="fa fa-times-circle"></i> '.$obj->User_Model->arErrorMessages['p_amount'].'</span>' : '')?>
		</div>
		
		<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_date']) ? ' has-error' : '')?>">
			<label class="col-sm-2">Transfer date</label>
			<div class="col-sm-4 input-group date datepicker" id="datepicker1" data-date-format="mm/dd/yyyy" data-date="<?=date("m/d/Y")?>">
				<input type="text" placeholder="Transfer date" name="p_date" id="p_date" class="form-control" value="<?=sanitize_post_field_value(!empty($_POST['p_date']) ? sanitize_post_field_value($_POST['p_date']) : date("m/d/Y"))?>" readonly>
				<span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </span>
			</div>
			<?=(!empty($obj->User_Model->arErrorMessages['p_date']) ? '<span class="help-block col-sm-offset-2 pull-left"><i class="fa fa-times-circle"></i> '.$obj->User_Model->arErrorMessages['p_date'].'</span>' : '')?>
		</div>
		
		<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['p_type']) ? ' has-error' : '')?>">
			<label class="col-sm-2">Transfer type</label>
			<div class="col-sm-4 no-left-pad">
				<input type="radio" name="p_type" value="0" <?=((int)$_POST['p_type'] == 0 ? "checked" : (!isset($_POST['p_type']) ? "checked" : ""))?>> Transfer to savings account<br>
				<input type="radio" name="p_type" value="1" <?=((int)$_POST['p_type'] == 1 ? "checked" : "")?>>	Withdraw into chequing accont		
			</div>
			<?=(!empty($obj->User_Model->arErrorMessages['p_type']) ? '<span class="help-block col-sm-offset-3 col-sm-9 pull-left"><i class="fa fa-times-circle"></i> '.$obj->User_Model->arErrorMessages['p_type'].'</span>' : '')?>
		</div>
		
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-4 no-left-pad"><button class="btn btn-full"><?=($approve_request ? "Approve" : "Transfer")?></button></div>
			<?php if($approve_request) {?>
			<div class="col-sm-4"><a href="<?=__BASE_ADMIN_URL__?>/users/managebalance/<?=$idUser?>" class="btn btn-full btn-gray">Cancel</a></div>
			<?php }?>
		</div>
		<input type="hidden" name="p_customer" value="<?=$obj->User_Model->idFinicity?>">
		<input type="hidden" name="p_available_amount" value="<?=$fTallyBalance?>">
		<input type="hidden" name="p_func" value="Transfer Balance">
		<?php if($approve_request) {?><input type="hidden" name="p_approve" value="<?=$arg3?>"><?php }?>
	</form>
</div>

<?php if(!$approve_request) {

if(!empty($arUnApprovedTransfers)) { ?>
<br>
<div class="dash-box">
	<h4 class="align-center">User's withdrawal request</h4>
	<br>
	<ul>
		<?php foreach($arUnApprovedTransfers as $i=>$transaction){?>
		<li<?=(($i+1) == count($arUnApprovedTransfers) ? ' class="last"' : '')?>><strong>$ <?=format_number($transaction['fAmount'])?> requested</strong> on <?=date("F j, Y", strtotime($transaction['dtCreatedOn']))?> <button type="button" onclick="window.location = '<?=__BASE_ADMIN_URL__?>/users/managebalance/<?=$idUser?>/approve/<?=$transaction['id']?>/<?=format_number($transaction['fAmount'])?>';" class="btn btn-xs btn-def">Approve</button> <button type="button" onclick="if(confirm('Are you sure you want to reject this withdraw request?')) window.location = '<?=__BASE_ADMIN_URL__?>/users/managebalance/<?=$idUser?>/reject/<?=$transaction['id']?>/<?=format_number($transaction['fAmount'])?>';" class="btn btn-gray btn-xs btn-def">Reject</button></li>
		<?php } ?>
	</ul>
</div>
<?php }

if(!empty($arTransfersHistory)) { ?>
<br>
<div class="dash-box">
	<h4 class="align-center">Transfers history</h4>
	<br>
	<ul>
		<?php foreach($arTransfersHistory as $i=>$transaction){?>
		<li<?=(!$show_more && ($i+1) == $iLimit ? ' class="last"' : '')?>><strong>$ <?=format_number($transaction['fAmount'])?> transferred</strong> on <?=($transaction['iType'] == 2 ? date("F j, Y", strtotime($transaction['dtApprovedOn'])) : date("F j, Y", strtotime($transaction['dtCreatedOn'])))?> <?=($transaction['iType'] == 2 ? " (Requested on " . date("F j, Y", strtotime($transaction['dtCreatedOn'])) . ")" : "")?></li>
		<?php } if($show_more){?>
		<li class="last"><a href="<?=__BASE_ADMIN_URL__?>/users/managebalance/<?=$idUser?>/more/<?=$iPage?>">View More History</a></li>
		<?php }?>
	</ul>
</div>
<?php }

}}
?>