<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<h1>Manage Users</h1>
<?php if(!empty($arUsers)){?><p class="align-right"><a href="<?=__BASE_ADMIN_URL__?>/users/list/export-customer-import-file">Export Verified Customer File for Rotessa</a></p><?php }?>

<?php if($error_msg != "") {?><div class="alert alert-danger"><?=$error_msg?></div><?php }?>
<?php if($success_msg != "") {?><div class="alert alert-success"><?=$success_msg?></div><?php }?>

<?php if($show_pagination){?>
<div class="row pagination">
	<div class="col-sm-4 count"><?=$szPageText?></div>
	<div class="col-sm-8 links"><?=$obj->pagination->create_links()?></div>
</div>
<?php }?>

<table class="table-format1">
	<thead>
		<tr class="thead">
			<td>Sr. No.</td>
			<td>User Informations</td>
			<td>Completed</td>
			<td>Verified</td>
			<td style="width:150px">Action</td>
		</tr>
	</thead>
	<tbody>
		<?php $ctr = 0; if(!empty($arUsers)){foreach($arUsers as $user){$ctr++;?>
		<tr>
			<td><?=$ctr?></td>
			<td>
				<h4>Personal Information</h4>
				<strong>Name:</strong> <?=$user['szFirstName']?> <?=$user['szLastName']?><br>
				<strong>Email:</strong> <a href="mailto:<?=$user['szEmail']?>"><?=$user['szEmail']?></a>
				<?php if(trim($user['szMobilePhone']) != ''){?><br><strong>Mobile:</strong> <?=$user['szMobilePhone']?><?php }?>
				
				<?php if((int)$user['iSignupStep'] >= 4){?>
				<hr>				
				<h4>Chequing account information</h4>
				<strong>Financial Institution:</strong> <?=$user['szFinicityInstitution']?> (<?=$user['szFinicityInstitutionNumber']?>)<br>
				<strong>Transit number:</strong> <?=$user['szFinicityAccountTransitNumber']?><br>
				<strong>Account number:</strong> <?=$user['szFinicityAccountNumber']?>
				<?php }?>
				
				<?php if(trim($user['idInstitution']) != '' && trim($user['szAccountNumber']) != '' && trim($user['szTransitNumber']) != ''){?>
				<hr>
				<h4>Saving account information</h4>
				<strong>Financial Institution:</strong> <?=$user['szInstitution']?> (<?=$user['szInstitutionNumber']?>)<br>
				<strong>Transit number:</strong> <?=$user['szTransitNumber']?><br>
				<strong>Account number:</strong> <?=$user['szAccountNumber']?><br>
				<strong>Verified:</strong> <?=((int)$user['iSavingAccountVerified'] == 1 ? 'Yes' : 'No')?>
				<?php }?>
			</td>
			<td><?=((int)$user['iSignupStep'] >= 4 ? 'Yes' : 'No')?></td>
			<td><?=((int)$user['iSignupStep'] == 5 ? 'Yes' : 'No')?></td>
			<td>
				<a href="<?=__BASE_ADMIN_URL__?>/users/details/<?=$user['id']?>" title="View User's Details"><i class="fa fa-bars"></i></a> &nbsp;
				<?php if((int)$user['iSignupStep'] == 5){?>
				<a href="<?=__BASE_ADMIN_URL__?>/users/transactions/<?=$user['id']?>" title="View User's Transactions"><i class="fa fa-list-alt"></i></a> &nbsp;				
				<a href="<?=__BASE_ADMIN_URL__?>/users/calculations/<?=$user['id']?>" title="View User's Saving Transactions and other Calculations"><i class="fa fa-calculator"></i></a> &nbsp;
				<a href="<?=__BASE_ADMIN_URL__?>/users/managebalance/<?=$user['id']?>" title="Manage User's Tally Balance"><i class="fa fa-balance-scale"></i></a> &nbsp;
				<?php }?>
				<a href="javascript:void(0);" title="Delete this user!" onclick="check_confirm('<?=$user['id']?>', 'You are about to delete this user account.', 'DELETE', 'ACCOUNT');"><i class="fa fa-trash"></i></a>
			</td>
		</tr>
		<?php } } else {?>
		<tr>
			<td colspan="6"><div class="alert alert-danger">No user exists!</div></td>
		</tr>
		<?php }?>
	</tbody>
</table>

<?php if($show_pagination){?>
<div class="row pagination">
	<div class="col-sm-4 count"><?=$szPageText?></div>
	<div class="col-sm-8 links"><?=$obj->pagination->create_links()?></div>
</div>
<?php }?>