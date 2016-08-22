<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$msg = "";
if($arg1 == "edit")
{
	if(!empty($_POST['p_add_edit']))
	{
		$p_id = (int)$_POST['p_id'];
		$p_name = sanitize_all_html_input(trim($_POST['p_name']));
		$p_value = sanitize_all_html_input(trim($_POST['p_value']));
		
		if($p_id > 0)
		{
			if($obj->Configuration_Model->saveTemplate($p_value, $p_id))
			{
				$msg = "Template details updated successfully";
			}
		}
		else
		{
			if($obj->Configuration_Model->addTemplate($p_name, $p_value))
			{
				$obj->session->set_userdata('add_tmpl_msg', "Template added successfully");

				ob_end_clean();
				header("Location:".__BASE_ADMIN_URL__."/configurations/template/edit/{$obj->Configuration_Model->id}");
				die;
			}
		}
	}
	
	$p_id = (int)$arg2;
	$arTemplateDetails = ($arg1 == "add" ? array() : $obj->Configuration_Model->getTemplates($p_id));
	
	if($arg1 == "edit" && empty($arTemplateDetails))
	{
		?>
		<div class="alert alert-danger">No template details found</div>
		<?php
	}
	else
	{
		if($obj->session->userdata('add_tmpl_msg'))
		{
			$msg = $obj->session->userdata('add_tmpl_msg');
			$obj->session->unset_userdata('add_tmpl_msg');
		}
		
		$btnText = ($arg1 == "add" ? "Add" : "Update");
		$szTemplateName = ($arg1 == "edit" && empty($_POST['p_name']) ? $arTemplateDetails[0]['szName'] : sanitize_post_field_value($_POST['p_name']));
		$szTemplateValue = ($arg1 == "edit" && empty($_POST['p_value']) ? $arTemplateDetails[0]['szValue'] : sanitize_post_field_value($_POST['p_value']));
		
		?>
		
		<h1>Update Template</h1>
		<a href="<?=__BASE_ADMIN_URL__?>/configurations/templates" class="pull-right">Back to list</a><br>
		
		<?php if($msg != ''){?>
		<div class="alert alert-success"><?=$msg?></div>
		<?php }?>
		
		<div class="row">
			<div class="col-sm-8">
				<form name="frmManageTemplate" id="frmManageTemplate" method="post" action="<?=__BASE_ADMIN_URL__?>/configurations/templates/<?=$arg1?><?=($p_id > 0 ? "/$p_id" : "")?>">
					<div class="form-group clearfix <?=(!empty($obj->Configuration_Model->arErrorMessages['p_name']) ? ' has-error' : '')?>">
						<label>Template Name</label>
						<input type="text" placeholder="Template name" name="p_name" id="p_name" value="<?=$szTemplateName?>" class="form-control required" <?=($arg1 == 'edit' ? 'disabled' : '')?>>
						<?=(!empty($obj->Configuration_Model->arErrorMessages['p_name']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->Configuration_Model->arErrorMessages['p_name']}</span>" : "")?>
					</div>
					
					<div class="form-group clearfix <?=(!empty($obj->Configuration_Model->arErrorMessages['p_value']) ? ' has-error' : '')?>">
						<label>Template Value</label>
						<textarea placeholder="Template value" name="p_value" id="p_value" class="form-control required"><?=$szTemplateValue?></textarea>
						<?=(!empty($obj->Configuration_Model->arErrorMessages['p_value']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$obj->Configuration_Model->arErrorMessages['p_value']}</span>" : "")?>
					</div>
					
					<div class="form-group">
						<input type="submit" name="p_submit" value="<?=$btnText?> Template" class="btn btn-form-submit">
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
	$arTemplates = $obj->Configuration_Model->getTemplates();?>
	
	<h1>Manage SMS Templates</h1>
	<br>
	
	<?php if($msg != ''){?>
	<div class="alert alert-success"><?=$msg?></div>
	<?php }?>
	
	<?php if(!empty($arTemplates)){?>
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
			<?php foreach($arTemplates as $k=>$template){?>
			<tr>
				<td><?=($k+1)?></td>
				<td><?=$template['szName']?></td>
				<td><?=$template['szValue']?></td>
				<td><?=date("m/d/Y", strtotime($template['dtCreatedOn']))?></td>
				<td><?=($template['dtUpdatedOn'] != '' && $template['dtUpdatedOn'] != '0000-00-00 00:00:00' ? date("m/d/Y", strtotime($template['dtUpdatedOn'])) : '')?></td>
				<td>
					<a href="<?=__BASE_ADMIN_URL__?>/configurations/templates/edit/<?=$template['id']?>" title="Edit Template"><i class="fa fa-edit"></i></a>
				</td>
			</tr>
			<?php }?>
		<tbody>
		</tbody>
	</table>
	<?php } else {?>
	<div class="alert alert-danger">No template found.</div>
	<?php }
}
?>