<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if($szErrorMessage != ''){?>
<?=$szErrorMessage?>
<?php } else {?>

<?php if($arg2 == "add"){?>
<h1><?=$obj->User_Model->szFirstName?> <?=$obj->User_Model->szLastName?> Add a Savings Transaction</h1>
<a href="<?=__BASE_ADMIN_URL__?>/users/calculations/<?=$idUser?>" class="pull-right">Back to Search function</a>
<?php } else {?>
<h1><?=$obj->User_Model->szFirstName?> <?=$obj->User_Model->szLastName?> Saving Transactions</h1>
<!-- <a href="<?=__BASE_ADMIN_URL__?>/users/calculations/<?=$idUser?>/add" class="pull-right">Add a Savings Transaction</a>-->
<?php }?>
<br>

<h3>Tally Account Information</h3>
<p>
	<strong>Tally Current Balance:</strong> $<?=format_number($fTallyBalance)?> <br>
	<strong>External Balance:</strong> $<?=format_number($fTotalTransfers)?> <br>
	<strong>Total Tally Savings:</strong> $<?=format_number($fTotalSaving)?> <br>
	<strong>Chequing account balance:</strong> $<?=format_number($fChequingAccountBalance)?> <br>
	<strong>First Saving Transaction On:</strong> <?=$szFirstTransactionDate?> <br>
	<strong>Days from first transaction:</strong> <?=$iFirstTransactionDays?> 
</p>
<hr>

<?php if($arg2 == "edit"){
$arTransaction = $obj->Configuration_Model->getCustomerBiMonthlySavingTransactions($arg3, 0, 0, "dtCreatedOn", "ASC", 0, true);
if(!isset($_POST['p_amount']))
{
	$_POST['p_amount'] = format_number(abs($arTransaction[0]['fSavingAmount']));
}
if(!empty($obj->Configuration_Model->arErrorMessages['p_id'])) {?>
<div class="alert alert-success"><?=$obj->Configuration_Model->arErrorMessages['p_id']?></div>
<?php }?>
<div class="tn-add-form">
	<h3>Edit Savings Transaction</h3>
	<form id="frmAdd" name="frmAdd" method="post" action="<?=__BASE_ADMIN_URL__?>/users/calculations/<?=$idUser?>" class="form-horizontal">		
		<div class="form-group">
			<label class="col-sm-3">Transaction Date</label>
			<div class="col-sm-4"><?=date("d/m/Y", strtotime($arTransaction[0]['dtCreatedOn']))?></div>			
		</div>
		
		<div class="form-group<?=(!empty($obj->Configuration_Model->arErrorMessages['p_amount']) ? ' has-error' : '')?>">
			<label class="col-sm-3">Transaction Amount</label>
			<div class="col-sm-4">
				<input type="text" placeholder="Transaction Amount" name="p_amount" id="p_amount" class="form-control required" value="<?=sanitize_post_field_value($_POST['p_amount'])?>">
				<?=(!empty($obj->Configuration_Model->arErrorMessages['p_amount']) ? '<span class="help-block pull-left"><i class="fa fa-times-circle"></i> '.$obj->Configuration_Model->arErrorMessages['p_amount'].'</span>' : '')?>
			</div>
		</div>
		
		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-2">
				<button class="btn btn-full btn-form-submit">Update</button>
				<input type="hidden" name="p_id" value="<?=$arg3?>">
				<input type="hidden" name="p_customer" value="<?=$obj->User_Model->idFinicity?>">
				<input type="hidden" name="p_edit_transaction" value="1">
			</div>
			<div class="col-sm-2">
				<input type="submit" name="p_search_calc" class="btn btn-full btn-gray" value="Cancel">
			</div>
		</div>
		
		<input type="hidden" name="p_calculation_year" value="<?=$arTransaction[0]['iYear']?>">
		<input type="hidden" name="p_calculation_month" value="<?=$arTransaction[0]['iMonth']?>">
	</form>
</div>

<?php } else {?>
<div class="tn-search-form">
	<form id="frmSearchCalc" name="frmSearchCalc" method="post" action="<?=__BASE_ADMIN_URL__?>/users/calculations/<?=$idUser?>" class="form-horizontal">
		<div class="row">
			<div class="col-sm-12 col-md-8">
				<div class="form-group">
					<label class="col-sm-4">Calculation Year</label>
					<div class="col-sm-8">
						<select name="p_calculation_year" class="form-control">
							<option value="2016">2016</option>
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-4">Calculation Month</label>
					<div class="col-sm-8">
						<select name="p_calculation_month" class="form-control">
							<?php foreach($arMonths as $num=>$month){?>
							<option value="<?=$num?>"><?=$month?></option>
							<?php }?>
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<div class="col-sm-offset-4 col-sm-8 col-xs-12 input-group">
						<div class="row">
							<div class="col-xs-6">
								<button class="btn btn-full">Search</button>
								<input type="hidden" name="p_id" value="<?=$idUser?>">
								<input type="hidden" name="p_search_calc" value="1">
								<input type="hidden" name="p_customer" value="<?=$obj->User_Model->idFinicity?>">
							</div>
							<div class="col-xs-6">
								<a href="<?=__BASE_ADMIN_URL__?>/users/calculations/<?=$idUser?>" class="btn btn-gray btn-full">Reset</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	</form>
</div>

<?php if($szSuccessMessage != ''){?><div class="alert alert-success"><?=$szSuccessMessage?></div><?php }?>
<?php if(!empty($arTransactions)){?>
<br>
<div class="results">
	<h3><?=$arMonths[$iMonth]?> <?=$iYear?> Saving Transaction</h3>
	<hr>	
	<table class="table-format2">
		<thead>
			<tr class="thead">
				<td>Transaction #</td>
				<td>Balance</td>
				<td>Amount</td>
				<td>Surplus Deficit</td>
				<td>If Deficit</td>
				<td>Transaction Date</td>
				<td></td>
			</tr>
		</thead>
		<?php foreach($arTransactions as $ctr=>$transaction){?>
		<tr>
			<td><?=$transaction['iTransactionNumber']?></td>
			<td><?=$transaction['fBalanceAmount']?></td>
			<td><?=$transaction['fSavingAmount']?></td>
			<td><?=$transaction['fSurplusDeficitAmount']?></td>
			<td><?=$transaction['fIfDeficitAmount']?></td>
			<td><?=date("m/d/Y", strtotime($transaction['dtCreatedOn']))?></td>
			<td><a href="<?=__BASE_ADMIN_URL__?>/users/calculations/<?=$idUser?>/edit/<?=$transaction['id']?>">Edit</a></td>
		</tr>
		<?php }?>
	</table>
</div>
<?php }else if($show_message){?>
<div class="alert alert-danger">No saving transaction found for <?=$arMonths[$iMonth]?> <?=$iYear?>.</div>
<?php }

if(!empty($arCalculations)){?>
<br>
<div class="results">	
	<h3><?=$arMonths[((int)$iMonth == 1 ? 12 : ((int)$iMonth - 1 < 10 ? "0" . ((int)$iMonth - 1) : ($iMonth - 1)))]?> <?=$iYear?> calculations</h3>
	<hr>
	<div class="form-group">
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Opening balance:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fOpeningBalance']?></div>
		</div>
		
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Total expense:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fTotalExpense']?></div>
		</div>
		
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Total income:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fTotalIncome']?></div>
		</div>
		
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Closing balance:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fClosingBalance']?></div>
		</div>
		
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Saving rate:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fSavingRate']?>%</div>
		</div>
	
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Total savings:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fTotalSaving']?></div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Week 1 expense:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fWeek1Expense']?></div>
		</div>
		
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Week 2 expense:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fWeek2Expense']?></div>
		</div>
		
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Week 3 expense:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fWeek3Expense']?></div>
		</div>
		
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Week 4 expense:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fWeek4Expense']?></div>
		</div>
		
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Week 5 expense:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fWeek5Expense']?></div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Average week 1 expense:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fWeek1ExpenseAverage']?></div>
		</div>
		
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Average week 2 expense:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fWeek2ExpenseAverage']?></div>
		</div>
		
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Average week 3 expense:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fWeek3ExpenseAverage']?></div>
		</div>
		
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Average week 4 expense:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fWeek4ExpenseAverage']?></div>
		</div>
		
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Average week 5 expense:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fWeek5ExpenseAverage']?></div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Average income monthly:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fAverageIncomeMonthly']?></div>
		</div>
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Average income weekly:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fAverageIncomeWeekly']?></div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Maximum expense average:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fMaxExpenseAverage']?></div>
		</div>
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Max expense coverage:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fMaxExpenseCover']?></div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row">
			<label class="col-md-4 col-sm-6 col-xs-8">Minimum cushion:</label>
			<div class="col-md-8 col-sm-6 col-xs-4"><?=$arCalculations['fMinimumCushion']?></div>
		</div>
	</div>
</div>

<?php }}}?>