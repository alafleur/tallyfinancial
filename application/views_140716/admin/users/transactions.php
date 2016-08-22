<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if($szErrorMessage != ''){?>
<?=$szErrorMessage?>
<?php } else {?>
<h1><?=$obj->User_Model->szFirstName?> <?=$obj->User_Model->szLastName?> Accounts' Transactions</h1>
<br>
<p><a href="javascript:void(0);" class="pull-right" onclick="importCoustomerTransactions(<?=$obj->User_Model->idFinicity?>);">Import User's Latest Transactions from Finicity</a></p>
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

<div id="ts-loading" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body align-center">
		<img src="<?=__BASE_ASSETS_URL__?>/images/loading.gif" alt="Loading">
	  </div>
	</div>
  </div>
</div>

<div class="tn-search-form">
	<form id="frmSearchTransactions" name="frmSearchTransactions" method="post" action="<?=__BASE_ADMIN_URL__?>/users/transactions/<?=$idUser?>" class="form-horizontal">
		<div class="row">
			<div class="col-sm-12 col-md-8">
				<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['dtStart']) ? ' has-error' : '')?>">
					<label class="col-sm-3">Start Date</label>
					<div class="col-sm-9 input-group date datepicker" id="datepicker1" data-date-format="mm/dd/yyyy" data-date="<?=date("m/d/Y", strtotime("-30 day"))?>">
						<input type="text" placeholder="Start Date" name="arSearch[dtStart]" id="dtStart" class="form-control" value="<?=(!empty($_POST['arSearch']['dtStart']) ? sanitize_post_field_value($_POST['arSearch']['dtStart']) : date("m/d/Y", strtotime("-30 day")))?>" readonly>
						<span class="input-group-addon">
		                    <i class="fa fa-calendar"></i>
		                </span>
					</div>
					<?=(!empty($obj->User_Model->arErrorMessages['dtStart']) ? '<span class="help-block col-sm-offset-3 pull-left"><i class="fa fa-times-circle"></i> '.$obj->User_Model->arErrorMessages['dtStart'].'</span>' : '')?>
				</div>
				<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['dtEnd']) ? ' has-error' : '')?>">
					<label class="col-sm-3">End Date</label>
					<div class="col-sm-9 input-group date datepicker" id="datepicker2" data-date-format="mm/dd/yyyy" data-date="<?=date("m/d/Y")?>">
						<input type="text" placeholder="End Date" name="arSearch[dtEnd]" id="dtEnd" class="form-control" value="<?=(!empty($_POST['arSearch']['dtEnd']) ? sanitize_post_field_value($_POST['arSearch']['dtEnd']) : date("m/d/Y"))?>" readonly>
						<span class="input-group-addon">
		                    <i class="fa fa-calendar"></i>
		                </span>
					</div>
					<?=(!empty($obj->User_Model->arErrorMessages['dtEnd']) ? '<span class="help-block col-sm-offset-3 pull-left"><i class="fa fa-times-circle"></i> '.$obj->User_Model->arErrorMessages['dtEnd'].'</span>' : '')?>
				</div>
				
				<div class="form-group<?=(!empty($obj->User_Model->arErrorMessages['fAmount']) ? ' has-error' : '')?>">
					<label class="col-sm-3">Amount</label>
					<div class="col-sm-9 col-xs-12 input-group">
						<div class="row">
							<div class="col-xs-5">
								<select name="arSearch[operator]" id="operator" class="form-control col-sm-2">
									<option value="eq"<?=(trim($_POST['arSearch']['operator']) == "eq" ? " selected" : "")?>>Equal to</option>
									<option value="gt"<?=(trim($_POST['arSearch']['operator']) == "gt" ? " selected" : "")?>>Greater than</option>							
									<option value="gteq"<?=(trim($_POST['arSearch']['operator']) == "gteq" ? " selected" : "")?>>Greater than equal to</option>
									<option value="lt"<?=(trim($_POST['arSearch']['operator']) == "lt" ? " selected" : "")?>>Less than</option>
									<option value="lteq"<?=(trim($_POST['arSearch']['operator']) == "lteq" ? " selected" : "")?>>Less than equal to</option>
								</select>
							</div>						
							<div class="col-xs-7"><input type="text" name="arSearch[fAmount]" placeholder="Transaction Amount" id="fAmount" class="form-control" value="<?=sanitize_post_field_value($_POST['arSearch']['fAmount'])?>"></div>
						</div>
					</div>
					<?=(!empty($obj->User_Model->arErrorMessages['fAmount']) ? '<span class="help-block col-sm-offset-3 pull-left"><i class="fa fa-times-circle"></i> '.$obj->User_Model->arErrorMessages['fAmount'].'</span>' : '')?>
				</div>				
				
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-9 col-xs-12 input-group">
						<div class="row">
							<div class="col-xs-6">
								<button class="btn btn-full" onclick="$('#szType').val('search');$('#frmSearchTransactions').submit();" type="button">Search</button>						
							</div>
							<div class="col-xs-6">
								<a href="<?=__BASE_ADMIN_URL__?>/users/transactions/<?=$idUser?>" class="btn btn-gray btn-full">Reset</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="arSearch[idCustomer]" value="<?=$obj->User_Model->idFinicity?>">
		<input type="hidden" name="arSearch[szType]" id="szType" value="search">
	</form>
</div>

<?php if(!empty($arTransactions)){?>
<div class="results">
	<hr>
	<div class="row">
		<div class="col-sm-6"><p><strong>Total <?=count($arTransactions)?> transaction<?=(($ctr+1) > 1 ? 's' : '')?> found</strong></p></div>
		<div class="col-sm-6"><p><a href="javascript:void(0);" onclick="$('#szType').val('export');$('#frmSearchTransactions').submit();" class="pull-right"><strong>Export transactions to CSV</strong></a></p></div>
	</div>
	<table class="table-format2">
		<thead>
			<tr class="thead">
				<td>Sr. No.</td>
				<td>Transaction #</td>
				<td>Account #</td>
				<td>Amount</td>
				<td>Description</td>
				<td>Category</td>
				<td>Status</td>
				<td>Transaction Date</td>
			</tr>
		</thead>
		<?php foreach($arTransactions as $ctr=>$transaction){?>
		<tr>
			<td><?=($ctr+1)?></td>
			<td><?=$transaction['id']?></td>
			<td><?=$transaction['idAccount']?></td>
			<td><?=$transaction['fAmount']?></td>
			<td><?=$transaction['szDescription']?></td>
			<td><?=$transaction['szCategory']?></td>
			<td><?=$transaction['szStatus']?></td>
			<td><?=date("m/d/Y", strtotime($transaction['dtDate']))?></td>
		</tr>
		<?php }?>
	</table>	
	<strong>Total <?=($ctr+1)?> transaction<?=(($ctr+1) > 1 ? 's' : '')?> found</strong>
	<hr>
</div>
<?php }else if($show_search_response){?>
<div class="alert alert-danger">No transaction found for your search criteria.</div>
<?php }
}
?>