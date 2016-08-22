<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
	
<h1>All Customers</h1>
<?php if(!empty($arUsers)){?><p class="align-right"><a href="<?=__BASE_ADMIN_URL__?>/reports/users/export">Export All Customers</a></p><?php }?>

<?php if($show_pagination){?>
<div class="row pagination">
	<div class="col-sm-4 count"><?=$szPageText?></div>
	<div class="col-sm-8 links"><?=$obj->pagination->create_links()?></div>
</div>
<?php }?>
<table class="table-format3">
	<thead>
		<tr class="thead">
			<td>&nbsp;#</td>
			<td>Personal Info</td>
			<td>Chequing A/C Info</td>
			<td>Savings A/C Info</td>
			<td>Verified</td>
			<td>Tally A/C Info</td>
			<td>Constants</td>
		</tr>
	</thead>
	<tbody>		
		<?php $ctr = 0; if(!empty($arUsers)){foreach($arUsers as $user){$ctr++;
		$fTotalSaving = 0;
		$fTotalTransfers = 0;
		$fTallyBalance = 0;
		
		// get all saving transactions
		$obj->load->model('Configuration_Model');
		$arTransactions = $obj->Configuration_Model->getCustomerBiMonthlySavingTransactions($user['idFinicity']);
		$iTotalTransactions = count($arTransactions);
		if(!empty($arTransactions))
		{
			foreach($arTransactions as $transaction)
			{
				$fTotalSaving += $transaction['fSavingAmount'];
			}
			if($fTotalSaving != 0)
				$fTotalSaving = abs($fTotalSaving);
		}
		
		// get old saving transfers
		$arTransfers = $obj->User_Model->getCustomerSavingsTransfers($user['idFinicity']);
		$iTotalTransfers = count($arTransfers);
		if(!empty($arTransfers))
		{
			foreach($arTransfers as $transfer)
			{
				$fTotalTransfers += $transfer['fAmount'];
			}
		}
		
		// get tally balance
		if($fTotalSaving > $fTotalTransfers)
		{
			$fTallyBalance = $fTotalSaving - $fTotalTransfers;
		}
		
		// get latest chequing balance
		$fChequingbalance = $obj->User_Model->getChequingBalance($user['idFinicity']);
		?>
		<tr>
			<td><?=$ctr?></td>
			<td>
				<strong>Customer ID:</strong> <?=$user['id']?><br>
				<strong>Name:</strong> <?=$user['szFirstName']?> <?=$user['szLastName']?><br>
				<strong>Email:</strong> <a href="mailto:<?=$user['szEmail']?>"><?=$user['szEmail']?></a>
				<?php if(trim($user['szMobilePhone']) != ''){?><br><strong>Mobile:</strong> <?=$user['szMobilePhone']?><?php }?>
			</td>
			<td>
				<?php if((int)$user['iSignupStep'] >= 4){?>
				<strong>Inst:</strong> <?=$user['szFinicityInstitution']?> (<?=$user['szFinicityInstitutionNumber']?>)<br>
				<strong>Transit #:</strong> <?=$user['szFinicityAccountTransitNumber']?><br>
				<strong>A/C #:</strong> <?=$user['szFinicityAccountNumber']?>
				<?php if((int)$user['iSignupStep'] == 5){?><br><strong>A/C Balance:</strong> $<?=format_number($fChequingbalance)?><?php }?>
				<?php }?>
			</td>
			<td>
				<?php if(trim($user['idInstitution']) != '' && trim($user['szAccountNumber']) != '' && trim($user['szTransitNumber']) != ''){?>				
				<strong>Inst:</strong> <?=$user['szInstitution']?> (<?=$user['szInstitutionNumber']?>)<br>
				<strong>Transit #:</strong> <?=$user['szTransitNumber']?><br>
				<strong>A/C #:</strong> <?=$user['szAccountNumber']?><br>
				<strong>Verified:</strong> <?=((int)$user['iSavingAccountVerified'] == 1 ? 'Yes' : 'No')?>
				<?php }?>
			</td>
			<td><?=((int)$user['iSignupStep'] == 5 ? 'Yes' : 'No')?></td>
			<td>
				<?php if((int)$user['iSignupStep'] == 5){?>
				<strong>Current Bal:</strong> $<?=format_number($fTallyBalance)?><br>
				<strong>External Bal:</strong> $<?=format_number($fTotalTransfers)?><br>
				<strong>Total Savings:</strong> $<?=format_number($fTotalSaving)?><br>
				<strong>Avg Saving Amt:</strong> $<?=($iTotalTransactions > 0 ? format_number($fTotalSaving/$iTotalTransactions) : '0.00')?><br>
				<strong># of Transactions:</strong> <?=$iTotalTransactions?>
				<?php }?>
			</td>
			<td>
				<?php if((int)$user['iSignupStep'] == 5){?>
				<strong>Abs Min Balance:</strong> <?=format_number($user['fAbsoluteMinBalance'])?><br>
				<strong>Surplus Deficit Rate:</strong> <?=format_number($user['fSurplusDeficitRate'])?><br>
				<strong>If Deficit Rate:</strong> <?=format_number($user['fIfDeficitRate'])?>
				<?php }?>
			</td>
		</tr>
		<?php }}?>
	</tbody>
</table>
<?php if($show_pagination){?>
<div class="row pagination">
	<div class="col-sm-4 count"><?=$szPageText?></div>
	<div class="col-sm-8 links"><?=$obj->pagination->create_links()?></div>
</div>
<?php }?>