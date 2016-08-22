<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!empty($_POST['p_download']))
{
	$file = trim($_POST['p_file']);
	if($file != "" && file_exists($file))
	{
		$file_name = str_replace(" ", "_", trim($_POST['p_file_name']));
		$file_contents = file_get_contents($file);
		
		ob_end_clean();
		header("Pragma: ");
		header("Cache-Control: ");
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=$file_name");
		echo iconv("UTF-8", "ISO-8859-1//TRANSLIT", $file_contents);
		unset($_SESSION['PostData']);
		exit;
	}	
}

$arSearchFiles = array();
if(!empty($_POST['arSearch']))
{
	$dtSearch = sanitize_all_html_input(trim($_POST['arSearch']['dtSearch']));
	if(!empty($dtSearch))
	{
		if(date("Y-m-d", strtotime($dtSearch)) != "1970-01-01" && strtotime($dtSearch) <= time() && (date("l", strtotime($dtSearch)) == "Monday" || date("l", strtotime($dtSearch)) == "Thursday"))
		{
			$dtSearch = date("l jS F Y", strtotime($dtSearch));
			$arFiles = glob( __APP_PATH__ . "/output/*csv");
			
			if($arFiles) 
			{
			    foreach($arFiles as $fileName) 
			    { 
			    	$basename = basename($fileName);
			    	if(strpos($basename, $dtSearch) != false)
			    		$arSearchFiles[] = array("file" => $fileName, "name" => $basename);
			    }
			}
		}
		else
		{
			$obj->Admin_Model->addError("dtSearch", "Please select a past date of Monday or Thursday");
		}
	}
	else
	{
		$obj->Admin_Model->addError("dtSearch", "Please select a past date of Monday or Thursday");
	}
}

$preselected_date = (date('l') == "Monday" || date('l') == "Thursday" ? date("l, j M, Y") : "");
if($preselected_date == "")
{
	$i = 1;
	while(date('l', strtotime("-{$i} day")) != "Monday" && date('l', strtotime("-{$i} day")) != "Thursday")
	{		
		$i++;
	}
	$preselected_date = date("l, j M, Y", strtotime("-{$i} day"));
}

?>
<h1>Download Debit/Credit Transaction Files</h1>
<br>

<?php if(!empty($arSearchFiles)){?>
<div class="item_list">
	<table class="table-format1">
		<tr class="thead">
			<td>Sr. No.</td>
			<td>File Name</td>
			<td>Download</td>
		</tr>
		<?php foreach($arSearchFiles as $file){?>
		<tr>
			<td><?=++$ctr;?></td>
			<td><?=$file['name']?></td>
			<td>
				<form name="frmDownload<?=$ctr?>" id="frmDownload<?=$ctr?>" method="post" action="<?=__BASE_ADMIN_URL__?>/download/files">
					<input type="hidden" name="p_file" value="<?=$file['file']?>">
					<input type="hidden" name="p_file_name" value="<?=$file['name']?>">
					<input type="hidden" name="p_download" value="1">
					<a href="javascript:void(0);" onclick="$('#frmDownload<?=$ctr?>').submit();">Download</a>
				</form>
			</td>			
		</tr>
		<?php }?>
	</table>
	<p><strong>Total <?=$ctr?> file<?=($ctr > 1 ? 's' : '')?> found.</strong></p>
</div>
<hr>
<?php } else if(!empty($_POST['arSearch'])) {?>
<div class="alert alert-danger">No file found.</div>
<?php }?>

<div class="tn-search-form">
	<form id="frmDownload" name="frmDownload" method="post" action="<?=__BASE_ADMIN_URL__?>/download/files" class="form-horizontal">
		<div class="row">
			<div class="col-sm-12 col-md-8">
				<div class="form-group<?=(!empty($obj->Admin_Model->arErrorMessages['dtSearch']) ? ' has-error' : '')?>">
					<label class="col-sm-3">File Date</label>
					<div class="col-sm-9 input-group date datepicker" id="datepicker1" data-date-format="DD, d MM, yyyy" data-date="<?=$preselected_date?>">
						<input type="text" placeholder="Start Date" name="arSearch[dtSearch]" id="dtSearch" class="form-control" value="<?=(!empty($_POST['arSearch']['dtSearch']) ? sanitize_post_field_value($_POST['arSearch']['dtSearch']) : $preselected_date)?>" readonly>
						<span class="input-group-addon">
		                    <i class="fa fa-calendar"></i>
		                </span>
					</div>
					<?=(!empty($obj->Admin_Model->arErrorMessages['dtSearch']) ? '<span class="help-block col-sm-offset-3 pull-left"><i class="fa fa-times-circle"></i> '.$obj->Admin_Model->arErrorMessages['dtSearch'].'</span>' : '')?>
				</div>
				
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-9 col-xs-12 input-group">
						<div class="row">
							<div class="col-xs-6">
								<button class="btn btn-full">Search</button>						
							</div>
							<div class="col-xs-6">
								<a href="<?=__BASE_ADMIN_URL__?>/download/files" class="btn btn-gray btn-full">Reset</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>