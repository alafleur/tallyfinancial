<?php
if(!empty($_POST['arSearch']))
{
	$p_account_status = sanitize_all_html_input(trim($_POST['arSearch']['p_account_status']));
	$p_signup_status = sanitize_all_html_input(trim($_POST['arSearch']['p_signup_status']));
	$p_signup_op = sanitize_all_html_input(trim($_POST['arSearch']['p_signup_operator']));
	$p_signup_date = sanitize_all_html_input(trim($_POST['arSearch']['p_date']));
	
	$query = "U.szMobilePhone != ''";
	if($p_account_status != '')
	{
		$query .= " AND U.iSignupStep " . ((int)$p_account_status == 1 ? "= 5" : "!= 5");
	}
	
	if($p_signup_status != '')
	{
		$query .= " AND U.iSignupStep " . ((int)$p_signup_status == 1 ? ">= 4" : "< 4");
	}
	
	if($p_signup_date != '')
	{
		$arr = explode("/", $p_signup_date);
		$p_signup_date = "{$arr[2]}-{$arr[0]}-{$arr[1]}";
		if($p_signup_op == 'on')
			$d_query = "U.dtAddedOn >= '{$p_signup_date} 00:00:00' AND U.dtAddedOn <= '{$p_signup_date} 23:59:59'";
		else if($p_signup_op == "before")
			$d_query = "U.dtAddedOn < '{$p_signup_date} 00:00:00'";
		else
			$d_query = "U.dtAddedOn > '{$p_signup_date} 23:59:59'";
		
		$query .= "AND {$d_query}";
	}
	
	if($arg1 == "sort")
	{
		$sort_by = $arg2;
		$sort_order = $arg3;
		if($arg2 == 'name')
			$sort_var = "CONCAT(U.szFirstName, ' ', U.szLastName)";
		else if($arg2 == 'email')
			$sort_var = "U.szEmail";
		else if($arg2 == "mobile")
			$sort_var = "U.szMobilePhone";
		else if($arg2 == "bank")
			$sort_var = "FI.szInstitutionNumber";
		else if($arg2 == "signup" || $arg2 == "verified")
			$sort_var = "U.iSignupStep";
	}
	
	$arUsers = $obj->User_Model->getCustomers(false, 0, 0, $query, $sort_var, $sort_order);
	if(!empty($arUsers))
	{
		$obj->load->model('Configuration_Model');
		foreach($arUsers as $key=>$user)
		{
			// get all saving transactions
			$iFirstTransactionDays = 0; 
			$arTransactions = $obj->Configuration_Model->getCustomerBiMonthlySavingTransactions($user['idFinicity'], 0, 0, "dtCreatedOn");
			if(!empty($arTransactions))
			{
				foreach($arTransactions as $transaction)
				{
					if($iFirstTransactionDays == 0)
					{
						$iFirstTransactionDays = ceil(abs(time() - strtotime($transaction['dtCreatedOn'])) / 86400);
						break;
					}
				}
			}
			$arUsers[$key]['iFirstTransactionDays'] = $iFirstTransactionDays;
		}
		
		if($arg1 == "sort" && $arg2 == 'numdays')
		{
			$sort_by = $arg2;
			$sort_order = $arg3;
			$desc = ($arg3 == "desc" ? true : false);
			$arUsers = sortArray($arUsers, 'iFirstTransactionDays', $desc);
		}
	}
}

$done = false;
$p_msg_error = "";
$no_user_selected = false;
if(!empty($_POST['p_send_message']) && trim($arg4) != "sorting")
{
	if(!empty($_POST['arUser']))
	{
		$is_error = false;
		$p_msg = sanitize_all_html_input(trim($_POST['p_msg']));
		if($p_msg == '')
		{
			$p_msg_error = "Message is required";
			$is_error = true;
		}
		
		if($_FILES['p_media']['name'] != '')
		{
			$arAcceptedMIMEOnTwilio = array("video/mpeg", "video/mp4", "video/quicktime", "video/webm", "video/3gpp", "video/3gpp2", "video/3gpp-tt", "video/H261", "video/H263", "video/H263-1998", "video/H263-2000", "video/H264", "image/jpeg", "image/gif", "image/png", "image/bmp");
			if (!in_array($_FILES["p_media"]["type"], $arAcceptedMIMEOnTwilio))
			{
				$p_media_error = "Please select a valid image or video file with following allowed formats-";
				foreach($arAcceptedMIMEOnTwilio as $i=>$mime)
				{
					$p_media_error .= "<br>" . ($i+1) . ". $mime";
				}				
				$is_error = true;
			}
			else
			{
				if($_FILES['p_media']['size'] > 5120000) 
				{
		             $p_media_error = "File size exceeds allowed limit - 5 MB.";
		             $is_error = true;
		        }
			}
		}
		
		if(!$is_error)
		{
			// attach media
			$p_media = '';
			if($_FILES['p_media']['name'] != '')
			{
				if(is_uploaded_file($_FILES['p_media']['tmp_name']))
		     	{
		     		$tmp = explode(".", $_FILES["p_media"]["name"]);
				    $extension = end($tmp);
		     		$imageName = time() . "." . $extension;
		          	if(move_uploaded_file($_FILES["p_media"]['tmp_name'], __APP_PATH_ASSETS__.'/media/'.$imageName))
		         	{
		         		$p_media = __BASE_ASSETS_URL__ . "/media/" . $imageName;
		         	}
		     	}
			}
					
			$obj->load->model('Configuration_Model');
			foreach($_POST['arUser'] as $idUser)
			{				
				if($obj->User_Model->loadCustomer($idUser))
				{
					$fTallyBalance = 0;
					$fTotalSaving = 0;
					$fTotalTransfers = 0;
					$fMostRecentTransaction = 0;
					
					// get all saving transactions					
					$arTransactions = $obj->Configuration_Model->getCustomerBiMonthlySavingTransactions($obj->User_Model->idFinicity, 0, 0, "dtCreatedOn");
					$iTotalTransactions = count($arTransactions);
					if(!empty($arTransactions))
					{
						foreach($arTransactions as $transaction)
						{
							$fTotalSaving += $transaction['fSavingAmount'];
							$fMostRecentTransaction = $transaction['fSavingAmount'];
						}
						if($fTotalSaving != 0)
							$fTotalSaving = abs($fTotalSaving);
							
						if($fMostRecentTransaction != 0)
							$fMostRecentTransaction = abs($fMostRecentTransaction);
					}
					
					// get old saving transfers
					$arTransfers = $obj->User_Model->getCustomerSavingsTransfers($obj->User_Model->idFinicity);
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
					$fChequingbalance = $obj->User_Model->getChequingBalance($obj->User_Model->idFinicity);
					
					// get last months calculations
					$arLastMonthCalculations = getPreviousMonthsCalculations($obj->User_Model->idFinicity, date("n"), date("Y"), $obj->Configuration_Model, 1);					
					
					$replace_ary = array();
					$replace_ary['FIRSTNAME'] = $obj->User_Model->szFirstName;
					$replace_ary['LASTNAME'] = $obj->User_Model->szLastName;
					$replace_ary['EMAIL'] = $obj->User_Model->szEmail;
					$replace_ary['MOBILE'] = $obj->User_Model->szMobilePhone;
					$replace_ary['CHEQUINGBALANCE'] = format_number($fChequingbalance);
					$replace_ary['TOTALSAVINGS'] = format_number($fTotalSaving);
					$replace_ary['TALLYBALANCE'] = format_number($fTallyBalance);
					$replace_ary['EXTERNALBALANCE'] = format_number($fTotalTransfers);
					$replace_ary['MOSTRECENT'] = format_number($fMostRecentTransaction);
					$replace_ary['AVGWEEK1'] = format_number($arLastMonthCalculations[0]['fWeek1ExpenseAverage']);
					$replace_ary['AVGWEEK2'] = format_number($arLastMonthCalculations[0]['fWeek2ExpenseAverage']);
					$replace_ary['AVGWEEK3'] = format_number($arLastMonthCalculations[0]['fWeek3ExpenseAverage']);
					$replace_ary['AVGWEEK4'] = format_number($arLastMonthCalculations[0]['fWeek4ExpenseAverage']);
					$replace_ary['AVGWEEK5'] = format_number($arLastMonthCalculations[0]['fWeek5ExpenseAverage']);
					$replace_ary['AVGINCOMEMONTHLY'] = format_number($arLastMonthCalculations[0]['fAverageIncomeMonthly']);
					$replace_ary['AVGINCOMEWKY'] = format_number($arLastMonthCalculations[0]['fAverageIncomeWeekly']);					
					$replace_ary['MAXEXPENSEAVG'] = format_number($arLastMonthCalculations[0]['fMaxExpenseAverage']);
					$replace_ary['MAXEXPENSECOVERAGE'] = format_number($arLastMonthCalculations[0]['fMaxExpenseCover']);
					$message = createMessage($p_msg, $replace_ary);
					
					sendMessege($obj->User_Model->szMobilePhone, $message, $p_media);
				}
			}
			$done = true;
			$arUsers = array();
			$_POST['arSearch'] = array();
		}
	}
	else
	{
		$no_user_selected= true;
	}
}
?>

<h1>Send Message</h1>
<br>

<h3>Search Users</h3>
<hr>
<form action="<?=__BASE_ADMIN_URL__?>/users/message" method="post" name="frmMsg" class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-3">Search by Status</label>
		<div class="col-sm-4">
			<select name="arSearch[p_account_status]" class="form-control">
				<option value="" selected>All</option>
				<option value="1">Verified</option>
				<option value="0">Not-Verified</option>
			</select>
		</div>			
	</div>
	
	<div class="form-group">
		<label class="col-sm-3">Signup status</label>
		<div class="col-sm-4">
			<select name="arSearch[p_signup_status]" class="form-control">
				<option value="" selected>All</option>
				<option value="1">Completed</option>
				<option value="0">Not-Completed</option>
			</select>
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-3">Signup time</label>
		<div class="col-sm-2">
			<select name="arSearch[p_signup_operator]" class="form-control">
				<option value="before">Before</option>
				<option value="on">On</option>
				<option value="after">After</option>
			</select>
		</div>
		<div class="col-sm-2 input-group date datepicker" id="datepicker1" data-date-format="mm/dd/yyyy">
			<input type="text" placeholder="Signup Date" name="arSearch[p_date]" id="p_date" class="form-control" value="" readonly>
			<span class="input-group-addon">
	         	<i class="fa fa-calendar"></i>
	      	</span>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-4">
			<button class="btn">Search Users</button>
		</div>
	</div>
</form>

<?php if($done){?><div class="alert alert-success">Message successfully sent to your selected users.</div><?php }?>

<?php if(!empty($arUsers)){?>
<hr>
<?php if($no_user_selected){?><div class="alert alert-danger">Please select at least one user to send messgage.</div><?php }?>
<?php if($p_msg_error != '' || $p_media_error){?><div class="alert alert-danger">Please resolve the following errors and try again.</div><?php }?>
<div class="item_list">
	<form name="sendMsg" id="sendMsg" action="<?=__BASE_ADMIN_URL__?>/users/message<?=($sort_by != "" ? "/sort/$sort_by/$sort_order" : '')?>" method="post" class="form-horizontal" enctype="multipart/form-data">
		<table class="table-format2">
			<tr>
				<th><input type="checkbox" id="selecctall"></th>
				<th><a href="javascript:void(0);" onclick="$('#sendMsg').attr('action', '<?=__BASE_ADMIN_URL__?>/users/message/sort/name/<?=($sort_by == "name" && $sort_order == "asc" ? "desc" : "asc")?>/sorting').submit();">Name</a></th>
				<th><a href="javascript:void(0);" onclick="$('#sendMsg').attr('action', '<?=__BASE_ADMIN_URL__?>/users/message/sort/email/<?=($sort_by == "email" && $sort_order == "asc" ? "desc" : "asc")?>/sorting').submit();">Email</a></th>
				<th><a href="javascript:void(0);" onclick="$('#sendMsg').attr('action', '<?=__BASE_ADMIN_URL__?>/users/message/sort/mobile/<?=($sort_by == "mobile" && $sort_order == "asc" ? "desc" : "asc")?>/sorting').submit();">Mobile #</a></th>
				<th><a href="javascript:void(0);" onclick="$('#sendMsg').attr('action', '<?=__BASE_ADMIN_URL__?>/users/message/sort/bank/<?=($sort_by == "bank" && $sort_order == "asc" ? "desc" : "asc")?>/sorting').submit();">Inst #</a></th>
				<th><a href="javascript:void(0);" onclick="$('#sendMsg').attr('action', '<?=__BASE_ADMIN_URL__?>/users/message/sort/numdays/<?=($sort_by == "numdays" && $sort_order == "asc" ? "desc" : "asc")?>/sorting').submit();"># of days</a></th>
				<th><a href="javascript:void(0);" onclick="$('#sendMsg').attr('action', '<?=__BASE_ADMIN_URL__?>/users/message/sort/signup/<?=($sort_by == "signup" && $sort_order == "asc" ? "desc" : "asc")?>/sorting').submit();">Signup</a></th>
				<th><a href="javascript:void(0);" onclick="$('#sendMsg').attr('action', '<?=__BASE_ADMIN_URL__?>/users/message/sort/verified/<?=($sort_by == "verified" && $sort_order == "asc" ? "desc" : "asc")?>/sorting').submit();">Verified</th>
			</tr>
			<?php foreach($arUsers as $i=>$users){ ?>
			<tr>
				<td><input type="checkbox" name="arUser[<?=$i?>]" value="<?=$users['id']?>" class="cbx1" <?=($_POST['arUser'][$i] == $users['id'] ? "checked" : "")?>></td>
				<td><?=$users['szFirstName']?> <?=$users['szLastName']?></td>
				<td><?=$users['szEmail']?></td>
				<td><?=$users['szMobilePhone']?></td>
				<td><?=$users['szFinicityInstitutionNumber']?></td>
				<td><?=$users['iFirstTransactionDays']?></td>
				<td><?=($users['iSignupStep'] >= 4 ? 'Yes' : 'No')?></td>
				<td><?=($users['iSignupStep'] == 5 ? 'Yes' : 'No')?></td>
			</tr>
			<?php }?>
		</table>
		
		<div class="row">
			<div class="col-sm-7">
				<div class="form-group<?=(!empty($p_msg_error) ? ' has-error' : '')?>">
					<label class="col-sm-2">Message</label>
					<div class="col-sm-10">
						<textarea name="p_msg" placecolder="Message" class="form-control"><?=sanitize_post_field_value($_POST['p_msg'])?></textarea>
						<?=(!empty($p_msg_error) ? '<span class="help-block pull-left"><i class="fa fa-times-circle"></i> '.$p_msg_error.'</span>' : '')?>
					</div>
				</div>
				
				<div class="form-group<?=(!empty($p_media_error) ? ' has-error' : '')?>">
					<label class="col-sm-2">Media</label>
					<div class="col-sm-10">
						<input type="file" name="p_media">
						<?=(!empty($p_media_error) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$p_media_error}</span>" : "")?>
					</div>
				</div>
					
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10"><button class="btn">Send message to selected Users</button></div>
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
		
		<input type="hidden" name="arSearch[p_account_status]" value="<?=$p_account_status?>">
		<input type="hidden" name="arSearch[p_signup_status]" value="<?=$p_signup_status?>">
		<input type="hidden" name="arSearch[p_signup_operator]" value="<?=$p_signup_op?>">
		<input type="hidden" name="arSearch[p_date]" value="<?=$p_signup_date?>">
		<input type="hidden" name="p_send_message" value="1">			
	</form>
</div>
<?php } else if(!empty($_POST['arSearch'])){?>
<div class="alert alert-danger">
	No user found, Please try with some other filters.
</div>
<?php }?>