<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="main-section">
	<div class="container">
		<div class="row clearfix">
			<div class="col-sm-4 col-md-3">
				<?php require_once('left_menu.php');?>
			</div>
			<div class="col-md-1"></div>
			<div class="col-sm-8 col-md-8">
				<?php if($szFinicityAccountTransitNumber == ''){?>
				
				<h1 class="align-center">Enter your chequing account transit number to complete your account information</h1>
				<br>
				<div class="login-form">
					<form id="frmVerifyBankingInfo" method="POST" action="<?=__SECURE_BASE_URL__?>/users/dashboard" enctype="multipart/form-data" class="form-horizontal">
						<div class="form-group">
							<label class="col-sm-4">Financial institution:</label>
							<div class="col-sm-8"><?=$szFinicityInstitution?></div>
						</div>																	
						
						<div class="form-group">
							<label class="col-sm-4">Account number:</label>
							<div class="col-sm-8"><?=$szFinicityAccountNumber?></div>
						</div>
							
						<div class="form-group<?=(!empty($arErrorMessages['p_transit_number']) ? ' has-error' : '')?>">
							<label class="col-sm-4">Transit number:</label>
							<div class="col-sm-8">
								<input type="text" placeholder="Transit number" name="arBanking[p_transit_number]" value="<?=sanitize_post_field_value($_POST['arBanking']['p_transit_number'])?>" id="p_transit_number" class="form-control required transit-number">
								<?=(!empty($arErrorMessages['p_transit_number']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$arErrorMessages['p_transit_number']}</span>" : "")?>
							</div>
						</div>
						
						<div class="form-group clearfix">
							<div class="col-sm-12">
								<input type="submit" name="arBanking[p_submit]" value="Update" class="btn btn-full btn-form-submit">
								<input type="hidden" name="arBanking[p_id]" value="<?=$id?>">
							</div>
						</div>
					</form>
				</div>
				<br>
				<h3>Where can I find my transit number?</h3>
				<h6>If you have access to a cheque from your account all the required information (institution number, account number and transit number) will be printed on the bottom of the cheque.</h6>
				<div class="form-group">
					<img src="<?=__BASE_ASSETS_URL__?>/images/account-info.jpg" alt="Account Info">
				</div>
				
				<?php }?>
				
				<?php if($transit_updated){?>
				<div class="alert alert-success">
					Your chequing account transit number has been updated successfully.
				</div>
				<?php }?>
								
				<div class="dash-box">
					<h1 class="align-center color-h5">Total Tally Savings</h1>
					<p class="saving-balance color-h1">$ <?=format_number($fTotalSaving)?></p>
											
					<div class="row">
						<div class="col-md-6">
							<div class="hbox">
								<h5 class="align-center">Current Tally Balance</h5>
								<span class="highlight">$ <?=format_number($fTallyBalance)?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="hbox">
								<h5 class="align-center">Tally Savings Transferred Out</h5>
								<span class="highlight">$ <?=format_number($fTotalTransfers)?></span>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div class="hbox">
								<h5 class="align-center">Average Tally Savings Amount</h5>
								<span class="highlight"><?php if($iTotalTransactions > 0){?>$ <?=format_number($fTotalSaving/$iTotalTransactions)?><?php } else {?>0.00<?php }?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="hbox">
								<h5 class="align-center">Number of Savings Transfers</h5>
								<span class="highlight"><?=$iTotalTransactions?></span>
							</div>
						</div>
					</div>
				
			
					<?php if(!empty($arJournalTransactions)){?>
					<br>
					<h5 class="align-center">Tally Savings Transaction History</h5>
					<br>
					<ul>
						<?php foreach($arJournalTransactions as $i=>$transaction){?>
						<li<?=(!$show_more && ($i+1) == $iLimit ? ' class="last"' : '')?>><strong>$ <?=format_number(abs($transaction['fSavingAmount']))?> saved</strong> on <?=date("F j, Y", strtotime($transaction['dtCreatedOn']))?></li>
						<?php } if($show_more){?>
						<li class="last"><a href="<?=__SECURE_BASE_URL__?>/users/dashboard/more/<?=$iPage?>">View More History</a></li>
						<?php }?>
					</ul>
					<?php }?>
				</div>
				
			</div>
		</div>
	</div>
</section>