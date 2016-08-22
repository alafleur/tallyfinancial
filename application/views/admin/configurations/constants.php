<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$msg = "";
if($p_func == "edit")
{
	if(!empty($_POST['p_add_edit']))
	{
		$p_id = (int)$_POST['p_id'];
		$p_name = sanitize_all_html_input(trim($_POST['p_name']));
		$p_value = sanitize_all_html_input(trim($_POST['p_value']));
		
		if($p_id > 0)
		{
			if($obj->Configuration_Model->saveConstant($p_value, $p_id))
			{
				$msg = "Constant details updated successfully";
			}
		}
		else
		{
			if($obj->Configuration_Model->addConstant($p_name, $p_value))
			{
				$obj->session->set_userdata('add_cons_msg', "Constant added successfully");

				ob_end_clean();
				header("Location:".__BASE_ADMIN_URL__."/configurations/constants/edit/{$obj->Configuration_Model->id}");
				die;
			}
		}
	}
	
	$p_id = (int)$arg2;
	$arConstantDetails = ($p_func == "add" ? array() : $obj->Configuration_Model->getConstants($p_id));
	
	if($p_func == "edit" && empty($arConstantDetails))
	{
		?>
		<div class="alert alert-danger">No constant details found</div>
		<?php
	}
	else
	{
		if($obj->session->userdata('add_cons_msg'))
		{
			$msg = $obj->session->userdata('add_cons_msg');
			$obj->session->unset_userdata('add_cons_msg');
		}
		
		$btnText = ($p_func == "add" ? "Add" : "Update");
		$szConstantName = ($p_func == "edit" && empty($_POST['p_name']) ? $arConstantDetails[0]['szName'] : sanitize_post_field_value($_POST['p_name']));
		$szConstantValue = ($p_func == "edit" && empty($_POST['p_value']) ? $arConstantDetails[0]['szValue'] : sanitize_post_field_value($_POST['p_value']));
		
		?>
		
		<h1>Update Constant</h1>
		<a href="<?=__BASE_ADMIN_URL__?>/configurations/constants/list" class="pull-right">Back to list</a><br>
		
		<?php if($msg != ''){?>
		<div class="alert alert-success"><?=$msg?></div>
		<?php }?>
		
		<div class="row">
			<div class="col-sm-8">
				<form name="frmManageConstants" id="frmManageConstants" method="post" action="<?=__BASE_ADMIN_URL__?>/configurations/constants/<?=$p_func?><?=($p_id > 0 ? "/$p_id" : "")?>">
					<div class="form-group clearfix <?=(!empty($obj->Configuration_Model->arErrorMessages['p_name']) ? ' has-error' : '')?>">
						<label>Constant Name</label>
						<input type="text" placeholder="Constant name" name="p_name" id="p_name" value="<?=$szConstantName?>" class="form-control required" <?=($p_func == 'edit' ? 'disabled' : '')?>>
						<?=(!empty($obj->Configuration_Model->arErrorMessages['p_name']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->Configuration_Model->arErrorMessages['p_name']}</span>" : "")?>
					</div>
					
					<div class="form-group clearfix <?=(!empty($obj->Configuration_Model->arErrorMessages['p_value']) ? ' has-error' : '')?>">
						<label>Constant Value</label>
						<input type="text" placeholder="Constant value" name="p_value" id="p_value" value="<?=$szConstantValue?>" class="form-control required">
						<?=(!empty($obj->Configuration_Model->arErrorMessages['p_value']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->Configuration_Model->arErrorMessages['p_value']}</span>" : "")?>
					</div>
					
					<div class="form-group">
						<input type="submit" name="p_submit" value="<?=$btnText?> Constant" class="btn btn-form-submit">
						<input type="hidden" name="p_id" value="<?=$p_id?>">
						<input type="hidden" name="p_add_edit" value="1">
					</div>
				</form>
			</div>
		</div>	
		<?php
	}
}
else
{
	if(!empty($_POST['p_delete_constant']))
	{
		$p_id = (int)$_POST['p_delete_constant'];
		if($obj->Configuration_Model->deleteConstant($p_id))
		{
			$msg = "Constant #{$_POST['p_number']} deleted successfully.";
		}
	}
	
	$arConstants = $obj->Configuration_Model->getConstants();?>
	
	<h1>Manage Global Constants</h1>
	<br>
	
	<?php if($msg != ''){?>
	<div class="alert alert-success"><?=$msg?></div>
	<?php }?>
	
	<?php if(!empty($arConstants)){?>
	<table class="table-format2">
		<thead>
			<tr class="thead">
				<td>Sr. No.</td>
				<td>Name</td>
				<td>Value</td>
				<td>Created On</td>
				<td>Updated On</td>
				<td>Action</td>
			</tr>
		</thead>
			<?php foreach($arConstants as $k=>$constant){?>
			<tr>
				<td><?=($k+1)?></td>
				<td><?=$constant['szName']?></td>
				<td><?=$constant['szValue']?></td>
				<td><?=date("m/d/Y", strtotime($constant['dtCreatedOn']))?></td>
				<td><?=($constant['dtUpdatedOn'] != '' && $constant['dtUpdatedOn'] != '0000-00-00 00:00:00' ? date("m/d/Y", strtotime($constant['dtUpdatedOn'])) : '')?></td>
				<td>
					<a href="<?=__BASE_ADMIN_URL__?>/configurations/constants/edit/<?=$constant['id']?>" title="Edit Constant"><i class="fa fa-edit"></i></a>
				</td>
			</tr>
			<?php }?>
		<tbody>
		</tbody>
	</table>
	<?php } else {?>
	<div class="alert alert-danger">No constant found.</div>
	<?php }
}
?>